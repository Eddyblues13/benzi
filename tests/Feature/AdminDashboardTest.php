<?php

namespace Tests\Feature;

use App\Mail\ApproveEmail;
use App\Mail\CreditEmail;
use App\Mail\DebitEmail;
use App\Mail\DeclineEmail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create(['password' => 'admin-pass']);
    }

    public function test_every_admin_page_renders(): void
    {
        $customer = User::factory()->withCodes()->create();
        $this->actingAs($this->admin)->post('/credit-user', ['id' => $customer->id, 'amount' => 50, 'description' => 'Welcome bonus']);

        $this->actingAs($this->admin)->get('/dashboard')->assertOk()->assertViewIs('admin.home')->assertSee($customer->email);
        $this->actingAs($this->admin)->get('/profile/' . $customer->id)->assertOk()->assertSee('Welcome bonus');
        $this->actingAs($this->admin)->get('/user_transactions')->assertOk()->assertSee('Welcome bonus');
        $this->actingAs($this->admin)->get('/user_transactions?type=Loan')->assertOk();
        $this->actingAs($this->admin)->get('/user_transactions?status=0')->assertOk();
        $this->actingAs($this->admin)->get('/update_wallet')->assertOk();
        $this->actingAs($this->admin)->get('/admin-change-password')->assertOk();
        $this->actingAs($this->admin)->get('/users')->assertRedirect('/dashboard');
        $this->actingAs($this->admin)->get('/profile/999999')->assertNotFound();
    }

    public function test_admin_pages_render_with_no_data(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertOk();
        $this->actingAs($this->admin)->get('/user_transactions')->assertOk()->assertSee('No transactions found');

        $customer = User::factory()->create(['otp' => null, 'ssn' => null]);
        $this->actingAs($this->admin)->get('/profile/' . $customer->id)->assertOk();
    }

    public function test_credit_and_debit_adjust_balance_and_email_customer(): void
    {
        Mail::fake();
        $customer = User::factory()->create();

        $this->actingAs($this->admin)->post('/credit-user', ['id' => $customer->id, 'amount' => 1000.25, 'description' => 'Top up'])
            ->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/debit-user', ['id' => $customer->id, 'amount' => 200, 'description' => 'Fee'])
            ->assertSessionHas('status');

        $this->assertSame(800.25, $customer->balance());
        $this->assertDatabaseHas('credits', ['user_id' => $customer->id, 'description' => 'Top up']);
        $this->assertDatabaseHas('debits', ['user_id' => $customer->id, 'description' => 'Fee']);
        Mail::assertSent(CreditEmail::class, fn ($m) => $m->hasTo($customer->email));
        Mail::assertSent(DebitEmail::class, fn ($m) => $m->hasTo($customer->email));

        $this->actingAs($this->admin)->post('/credit-user', ['id' => $customer->id, 'amount' => 'abc', 'description' => 'x'])
            ->assertSessionHasErrors('amount');
        $this->actingAs($this->admin)->post('/credit-user', ['id' => 999999, 'amount' => 5, 'description' => 'x'])
            ->assertSessionHasErrors('id');
    }

    public function test_approve_and_decline_email_the_customer(): void
    {
        Mail::fake();
        $customer = User::factory()->create();
        $pending = fn () => Transaction::create([
            'user_id' => $customer->id, 'transaction_ref' => Transaction::makeReference('X'),
            'transaction_type' => 'Debit', 'transaction' => 'Wise Withdrawal', 'transaction_amount' => 10,
            'transaction_description' => 'Wise', 'transaction_status' => 0,
        ]);

        $a = $pending();
        $this->actingAs($this->admin)->post('/approve-transaction/' . $a->id);
        $this->assertSame(1, $a->fresh()->transaction_status);
        Mail::assertSent(ApproveEmail::class);

        $d = $pending();
        $this->actingAs($this->admin)->post('/decline-transaction/' . $d->id);
        $this->assertSame(2, $d->fresh()->transaction_status);
        Mail::assertSent(DeclineEmail::class);

        $this->actingAs($this->admin)->post('/approve-transaction/' . $d->id)->assertSessionHas('error');
        $this->assertSame(2, $d->fresh()->transaction_status, 'a declined transaction cannot then be approved');
    }

    public function test_security_codes_ssn_and_loan_limit_can_be_set(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($this->admin)->post('/update_otp/' . $customer->id, ['otp' => '0042'])->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/update_cic/' . $customer->id, ['cic_code' => 'C1'])->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/update_int/' . $customer->id, ['int_code' => 'I1'])->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/update_reflection/' . $customer->id, ['reflection' => '999'])->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/update_eligible_loan/' . $customer->id, ['eligible_loan' => 2500])->assertSessionHas('status');
        $this->actingAs($this->admin)->post('/update_otp/' . $customer->id, ['otp' => ''])->assertSessionHasErrors('otp');

        $customer->refresh();
        $this->assertSame('0042', $customer->otp, 'leading zeros are preserved');
        $this->assertSame('C1', $customer->ccic_code);
        $this->assertSame('I1', $customer->int_code);
        $this->assertSame('999', $customer->ssn);
        $this->assertEquals(2500, (float) $customer->eligible_loan);
    }

    public function test_verify_toggle_kyc_and_password_reset(): void
    {
        $customer = User::factory()->create(['user_status' => '0']);

        $this->actingAs($this->admin)->post('/verify_user/' . $customer->id);
        $this->assertSame('1', $customer->fresh()->user_status);
        $this->actingAs($this->admin)->post('/verify_user/' . $customer->id);
        $this->assertSame('0', $customer->fresh()->user_status);

        $this->actingAs($this->admin)->post('/approve-id_card/' . $customer->id, ['status' => 1]);
        $this->assertSame('1', $customer->fresh()->id_card_status);

        $this->actingAs($this->admin)->post('/admin_reset-password/' . $customer->id, ['password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');
        $this->actingAs($this->admin)->post('/admin_reset-password/' . $customer->id, ['password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])
            ->assertSessionHas('status');
        $this->assertTrue(Hash::check('brand-new-pass', $customer->fresh()->password));
    }

    public function test_delete_user_requires_post_and_protects_admins(): void
    {
        $customer = User::factory()->create();
        $this->credit($customer);

        $this->actingAs($this->admin)->get('/delete/' . $customer->id)->assertMethodNotAllowed();

        $this->actingAs($this->admin)->post('/delete/' . $customer->id)->assertRedirect('/dashboard');
        $this->assertNull($customer->fresh());
        $this->assertSame(0, Transaction::where('user_id', $customer->id)->count(), 'transactions are removed with the user');

        $this->actingAs($this->admin)->post('/delete/' . $this->admin->id)->assertSessionHas('error');
        $this->assertNotNull($this->admin->fresh());
    }

    public function test_admin_password_change(): void
    {
        $this->actingAs($this->admin)->post('/admin-update-password', [
            'old_password' => 'admin-pass', 'new_password' => 'admin-pass-2', 'new_password_confirmation' => 'admin-pass-2',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('admin-pass-2', $this->admin->fresh()->password));
    }

    public function test_wallet_update(): void
    {
        $this->actingAs($this->admin)->post('/admin_update_wallet', ['wallet_address' => '0xabc'])->assertSessionHas('status');
        $this->assertSame('0xabc', $this->admin->fresh()->wallet_address);
    }

    public function test_seeder_creates_an_admin_that_can_log_in(): void
    {
        putenv('ADMIN_EMAIL=owner@Limpopay.test');
        putenv('ADMIN_PASSWORD=seeded-pass-123');

        try {
            $this->seed();
        } finally {
            putenv('ADMIN_EMAIL');
            putenv('ADMIN_PASSWORD');
        }

        $seeded = User::where('email', 'owner@Limpopay.test')->firstOrFail();
        $this->assertTrue($seeded->isAdmin());

        $this->post('/login', ['email' => 'owner@Limpopay.test', 'password' => 'seeded-pass-123'])->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertViewIs('admin.home');
    }

    private function credit(User $user): void
    {
        Transaction::create([
            'user_id' => $user->id, 'transaction_ref' => Transaction::makeReference('S'), 'transaction_type' => 'Credit',
            'transaction' => 'Credit', 'transaction_amount' => 5, 'transaction_description' => 'seed', 'transaction_status' => 1,
        ]);
    }
}
