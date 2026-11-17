<?php

namespace Tests\Feature;

use App\Mail\SupportEmail;
use App\Models\Card;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private const CODES = ['otp' => '1111', 'ccic_code' => '2222', 'int_code' => '3333'];

    private function customer(float $balance = 0, array $attributes = []): User
    {
        $user = User::factory()->withCodes()->create($attributes);

        if ($balance > 0) {
            $this->credit($user, $balance);
        }

        return $user;
    }

    private function credit(User $user, float $amount, int $status = Transaction::STATUS_APPROVED, string $channel = 'Credit'): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'transaction_ref' => Transaction::makeReference('T'),
            'transaction_type' => Transaction::TYPE_CREDIT,
            'transaction' => $channel,
            'transaction_amount' => $amount,
            'transaction_description' => 'seed',
            'transaction_status' => $status,
        ]);
    }

    public function test_every_customer_page_renders(): void
    {
        $user = $this->customer(250);
        $tx = $user->transactions()->first();
        Card::create(['user_id' => $user->id, 'card_number' => '4000123412341234', 'card_cvc' => '123', 'card_expiry' => now()->addYear()]);

        $pages = ['/dashboard', '/home', '/profile', '/user-profile', '/change-password', '/transactions',
            '/transaction-history', '/view_invoice/' . $tx->id, '/deposit', '/loan', '/card', '/ticket', '/transfer',
            '/inter_bank_transfer', '/local_bank_transfer', '/revolut_bank_transfer', '/wise_bank_transfer',
            '/paypal', '/skrill', '/crypto'];

        foreach ($pages as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }

        $this->actingAs($user)->get('/dashboard')->assertSee('$250.00', false);
        $this->actingAs($user)->get('/settings')->assertRedirect('/profile');
        $this->actingAs($user)->get('/international-transfer')->assertRedirect('/inter_bank_transfer');
        $this->actingAs($user)->get('/domestic-transfer')->assertRedirect('/local_bank_transfer');
    }

    public function test_pages_render_for_a_brand_new_account_with_no_data(): void
    {
        $user = User::factory()->create(['display_picture' => null, 'currency' => null]);

        foreach (['/dashboard', '/transactions', '/transaction-history', '/loan', '/card', '/deposit', '/crypto', '/profile'] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_customer_cannot_reach_admin_pages(): void
    {
        $user = $this->customer();
        $other = $this->customer();

        foreach (['/user_transactions', '/profile/' . $other->id, '/update_wallet', '/admin-change-password', '/users'] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }

        $this->actingAs($user)->post('/credit-user', ['id' => $user->id, 'amount' => 1000, 'description' => 'x'])->assertForbidden();
        $this->actingAs($user)->post('/delete/' . $other->id)->assertForbidden();
        $this->assertSame(0.0, $user->balance());
        $this->assertNotNull($other->fresh());
    }

    public function test_customer_cannot_view_someone_elses_receipt(): void
    {
        $owner = $this->customer(10);
        $intruder = $this->customer();

        $this->actingAs($intruder)->get('/view_invoice/' . $owner->transactions()->first()->id)->assertNotFound();
    }

    public function test_withdrawal_holds_funds_and_decline_refunds(): void
    {
        $user = $this->customer(500);

        $this->actingAs($user)->from('/paypal')
            ->post('/paypal-transfer', ['amount' => 120.50, 'email' => 'me@paypal.test'] + self::CODES)
            ->assertRedirect('/paypal')->assertSessionHas('status');

        $tx = Transaction::where('transaction', 'Paypal Withdrawal')->firstOrFail();
        $this->assertSame(Transaction::STATUS_PENDING, $tx->transaction_status);
        $this->assertSame('me@paypal.test', $tx->recipient_email);
        $this->assertEquals(120.50, (float) $tx->transaction_amount, 'cents are kept');
        $this->assertSame(379.50, $user->balance(), 'pending debit holds the funds');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/decline-transaction/' . $tx->id)->assertSessionHas('message');
        $this->assertSame(500.0, $user->balance(), 'declined debit is refunded');
    }

    public function test_every_transfer_channel_records_its_details(): void
    {
        $user = $this->customer(10000);

        $cases = [
            ['/transfer_funds', 'Bank Transfer', ['account_name' => 'Bob', 'account_number' => '0123', 'bank_name' => 'Big Bank', 'routing_number' => '111000'], ['account_name' => 'Bob', 'routing_number' => '111000']],
            ['/inter-transfer', 'International Transfer', ['holder_name' => 'Bob', 'account_number' => '99', 'bank_name' => 'Intl', 'iban_number' => 'GB00X', 'swift_code' => 'ABCDGB2L', 'bank_address' => 'London', 'country' => 'UK'], ['account_name' => 'Bob', 'iban_number' => 'GB00X', 'swift_code' => 'ABCDGB2L']],
            ['/local-transfer', 'Local Transfer', ['holder_name' => 'Ann', 'account_number' => '55', 'bank_name' => 'Local'], ['account_name' => 'Ann', 'bank_name' => 'Local']],
            ['/revolut-transfer', 'Revolut Withdrawal', ['card_number' => '5555', 'holder_name' => 'Rev', 'email' => 'r@x.test'], ['card_number' => '5555', 'recipient_email' => 'r@x.test']],
            ['/wise-transfer', 'Wise Withdrawal', ['email' => 'w@x.test'], ['recipient_email' => 'w@x.test']],
            ['/skrill-transfer', 'Skrill Withdrawal', ['email' => 's@x.test'], ['recipient_email' => 's@x.test']],
            ['/crypto-transfer', 'Crypto Withdrawal', ['wallet_type' => 'Other', 'other_wallet_type' => 'Solana', 'wallet_address' => 'So1abc'], ['wallet_type' => 'Solana', 'wallet_address' => 'So1abc']],
        ];

        foreach ($cases as [$url, $channel, $input, $expected]) {
            $this->actingAs($user)->from('/dashboard')
                ->post($url, ['amount' => 100] + $input + self::CODES)
                ->assertSessionHas('status');

            $tx = Transaction::where('transaction', $channel)->latest('id')->firstOrFail();
            foreach ($expected as $column => $value) {
                $this->assertSame($value, $tx->{$column}, "$channel $column");
            }
        }

        $this->assertSame(10000.0 - 700, $user->balance());
    }

    public function test_withdrawal_is_rejected_without_valid_codes_or_funds(): void
    {
        $user = $this->customer(100);
        $input = ['amount' => 50, 'email' => 'me@wise.test'];

        $this->actingAs($user)->post('/wise-transfer', $input + ['otp' => '0000'] + self::CODES)->assertSessionHas('error');
        $this->actingAs($user)->post('/wise-transfer', $input + array_merge(self::CODES, ['int_code' => 'bad']))->assertSessionHas('error', ' Incorrect INT code!');
        $this->actingAs($user)->post('/wise-transfer', ['amount' => 101, 'email' => 'me@wise.test'] + self::CODES)
            ->assertSessionHas('error', ' Your account balance is insufficient, contact our administrator for more info!!');
        $this->actingAs($user)->post('/wise-transfer', ['amount' => -5, 'email' => 'me@wise.test'] + self::CODES)->assertSessionHas('error');

        $noCodes = User::factory()->create();
        $this->credit($noCodes, 100);
        $this->actingAs($noCodes)->post('/wise-transfer', $input + self::CODES)->assertSessionHas('error');

        $this->assertSame(0, Transaction::where('transaction_type', 'Debit')->count());
    }

    public function test_code_check_endpoints(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->postJson('/validate-otp', ['otp' => '1111'])->assertJson(['success' => true]);
        $this->actingAs($user)->postJson('/validate-otp', ['otp' => '9999'])->assertJson(['success' => false, 'message' => 'Incorrect OTP!']);
        $this->actingAs($user)->postJson('/validate-ccic', ['ccic_code' => '2222'])->assertJson(['success' => true]);
        $this->actingAs($user)->postJson('/validate-int', ['int_code' => '3333'])->assertJson(['success' => true]);

        $fresh = User::factory()->create();
        $this->actingAs($fresh)->postJson('/validate-otp', ['otp' => ''])->assertJson(['success' => false]);
    }

    public function test_security_codes_lock_out_after_repeated_wrong_guesses(): void
    {
        $user = $this->customer(100);

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)->postJson('/validate-otp', ['otp' => '0000'])->assertJson(['message' => 'Incorrect OTP!']);
        }

        $this->actingAs($user)->postJson('/validate-otp', ['otp' => '1111'])
            ->assertJson(['success' => false])->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString('Too many incorrect OTP attempts',
            $this->actingAs($user)->postJson('/validate-otp', ['otp' => '1111'])->json('message'));

        // The lock also applies to the final submission, not just the AJAX check.
        $this->actingAs($user)->post('/wise-transfer', ['amount' => 5, 'email' => 'a@b.test'] + self::CODES)
            ->assertSessionHas('error');
        $this->assertSame(0, Transaction::where('transaction_type', 'Debit')->count());

        // Other codes are unaffected.
        $this->actingAs($user)->postJson('/validate-ccic', ['ccic_code' => '2222'])->assertJson(['success' => true]);
    }

    public function test_rate_limits_are_per_action_not_shared(): void
    {
        Mail::fake();
        config(['app.admin_email' => 'support@Limpopay.test']);
        $user = $this->customer(1000);

        // Several complete transfers (3 code checks + 1 submit each) must not
        // exhaust the budget for unrelated actions such as support tickets.
        for ($i = 0; $i < 6; $i++) {
            foreach (['/validate-otp' => 'otp', '/validate-ccic' => 'ccic_code', '/validate-int' => 'int_code'] as $url => $field) {
                $this->actingAs($user)->postJson($url, [$field => self::CODES[$field]])->assertJson(['success' => true]);
            }
            $this->actingAs($user)->post('/wise-transfer', ['amount' => 1, 'email' => 'a@b.test'] + self::CODES)->assertSessionHas('status');
        }

        $this->actingAs($user)->post('/ticket', ['subject' => 'Still works', 'message' => 'ok'])->assertSessionHas('status');
    }

    public function test_deposit_only_counts_once_approved(): void
    {
        Storage::fake('public');
        $user = $this->customer();

        $this->actingAs($user)->post('/make-deposit', [
            'amount' => 300,
            'front_cheque' => UploadedFile::fake()->image('front.jpg'),
        ])->assertSessionHas('status');

        $deposit = Deposit::firstOrFail();
        Storage::disk('public')->assertExists($deposit->front_cheque);
        $this->assertSame(0.0, $user->balance(), 'pending deposit is not spendable');

        $tx = Transaction::where('transaction', 'Deposit')->firstOrFail();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/approve-transaction/' . $tx->id)->assertSessionHas('message');

        $this->assertSame(300.0, $user->balance());
        $this->assertSame(1, $deposit->fresh()->status);

        $this->actingAs($admin)->post('/approve-transaction/' . $tx->id)->assertSessionHas('error');
        $this->assertSame(300.0, $user->balance(), 'cannot be approved twice');
    }

    public function test_loan_request_rules_and_approval(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->post('/make-loan', ['amount' => 1000, 'ssn' => 'wrong'])->assertSessionHas('error', 'Incorrect SSN number!');
        $this->actingAs($user)->post('/make-loan', ['amount' => 6000, 'ssn' => '123456789'])->assertSessionHas('error');
        $this->actingAs($user)->post('/make-loan', ['amount' => 1000, 'ssn' => '123456789'])->assertSessionHas('status');

        $loan = Loan::firstOrFail();
        $this->assertSame(0, $loan->status);
        $this->assertSame(0.0, $user->balance());
        $this->actingAs($user)->get('/loan')->assertOk()->assertSee('$1,000.00', false);

        $admin = User::factory()->admin()->create();
        $tx = Transaction::where('transaction', 'Loan')->firstOrFail();
        $this->actingAs($admin)->post('/approve-transaction/' . $tx->id);

        $this->assertSame(1000.0, $user->balance());
        $this->assertSame(1, $loan->fresh()->status);

        $noSsn = User::factory()->create(['eligible_loan' => 5000]);
        $this->actingAs($noSsn)->post('/make-loan', ['amount' => 10, 'ssn' => 'x'])->assertSessionHas('error');
    }

    public function test_virtual_card_purchase(): void
    {
        $broke = $this->customer(5);
        $this->actingAs($broke)->post('/request-card')->assertSessionHas('error');
        $this->assertSame(0, Card::count());

        $user = $this->customer(50);
        $this->actingAs($user)->post('/request-card')->assertSessionHas('status');

        $card = Card::where('user_id', $user->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^4\d{15}$/', $card->card_number);
        $this->assertMatchesRegularExpression('/^\d{3}$/', $card->card_cvc);
        $this->assertSame(40.0, $user->balance());
        $this->actingAs($user)->get('/card')->assertSee(implode(' ', str_split($card->card_number, 4)));
    }

    public function test_profile_update_and_avatar_upload(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->post('/personal-details', [
            'first_name' => 'New', 'last_name' => 'Name', 'user_phone' => '+44 20 7946 0000', 'user_address' => 'Somewhere',
        ])->assertSessionHas('status');
        $this->assertSame('New', $user->fresh()->first_name);
        $this->assertSame('United States', $user->fresh()->country, 'country kept when the form omits it');

        $this->actingAs($user)->post('/personal-dp', ['image' => UploadedFile::fake()->create('shell.php', 1, 'application/x-php')])
            ->assertSessionHasErrors('image');

        $this->actingAs($user)->post('/personal-dp', ['image' => UploadedFile::fake()->image('me.png')])->assertSessionHas('status');
        $file = $user->fresh()->display_picture;
        $this->assertFileExists(public_path('uploads/display/' . $file));
        @unlink(public_path('uploads/display/' . $file));
    }

    public function test_password_change(): void
    {
        $user = $this->customer(0, ['password' => 'old-password']);

        $this->actingAs($user)->post('/change-password', [
            'old_password' => 'nope', 'new_password' => 'new-password', 'new_password_confirmation' => 'new-password',
        ])->assertSessionHas('error');

        $this->actingAs($user)->post('/change-password', [
            'old_password' => 'old-password', 'new_password' => 'new-password', 'new_password_confirmation' => 'new-password',
        ])->assertRedirect('/login');

        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'new-password'])->assertRedirect('/dashboard');
    }

    public function test_support_ticket_is_emailed_to_admin(): void
    {
        Mail::fake();
        config(['app.admin_email' => 'support@Limpopay.test']);
        $user = $this->customer();

        $this->actingAs($user)->post('/ticket', ['subject' => 'Help', 'message' => 'My card'])->assertSessionHas('status');

        Mail::assertSent(SupportEmail::class, fn ($mail) => $mail->hasTo('support@Limpopay.test') && $mail->data['subject'] === 'Help');
    }
}
