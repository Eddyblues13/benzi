<?php

namespace Tests\Feature;

use App\Mail\VerificationEmail;
use App\Mail\welcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'address' => '1 Analytical Way',
            'phone' => '+1 555 0100',
            'country' => 'Nigeria',
            'account_type' => 'Savings',
            'currency' => 'United Kingdom',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ], $overrides);
    }

    public function test_public_and_guest_pages_render(): void
    {
        foreach (['/', '/about', '/contact', '/terms', '/services', '/login', '/register', '/password/reset'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_registration_creates_account_and_sends_activation_code(): void
    {
        Mail::fake();

        $this->post('/register', $this->registrationData())->assertRedirect('/verify');

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertSame('Ada', $user->first_name);
        $this->assertSame('£', $user->currency, 'currency comes from the chosen currency country');
        $this->assertMatchesRegularExpression('/^\d{10}$/', $user->a_number);
        $this->assertMatchesRegularExpression('/^\d{4}$/', $user->token);
        $this->assertFalse($user->isActivated());
        $this->assertFalse($user->isAdmin());
        $this->assertAuthenticatedAs($user);

        Mail::assertSent(VerificationEmail::class);
    }

    public function test_registration_validates_input(): void
    {
        $this->from('/register')
            ->post('/register', $this->registrationData(['account_type' => 'select', 'password_confirmation' => 'nope']))
            ->assertRedirect('/register')
            ->assertSessionHasErrors(['account_type', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_cannot_self_register_as_admin(): void
    {
        Mail::fake();

        $this->post('/register', $this->registrationData(['user_type' => '1', 'is_activated' => '1', 'otp' => '9']));

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isActivated());
        $this->assertNull($user->otp);
    }

    public function test_trueman_last_name_no_longer_grants_admin_access(): void
    {
        $user = User::factory()->create(['last_name' => 'trueman']);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertViewIs('dashboard.home');
        $this->actingAs($user)->get('/user_transactions')->assertForbidden();
    }

    public function test_unactivated_user_is_sent_to_verify_and_can_activate(): void
    {
        Mail::fake();
        $user = User::factory()->unactivated()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/verify');
        $this->actingAs($user)->get('/crypto')->assertRedirect('/verify');
        $this->actingAs($user)->get('/verify')->assertOk()->assertSee($user->email);

        $this->actingAs($user)->from('/verify')->post('/email-verify', ['digit' => '0000'])
            ->assertRedirect('/verify')->assertSessionHas('error');
        $this->assertFalse($user->fresh()->isActivated());

        $this->actingAs($user)->post('/email-verify', ['digit' => '4321'])->assertRedirect('/dashboard');
        $this->assertTrue($user->fresh()->isActivated());
        $this->assertNull($user->fresh()->token);
        Mail::assertSent(welcomeEmail::class);

        $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
        $this->actingAs($user->fresh())->get('/verify')->assertRedirect('/dashboard');
    }

    public function test_resend_code_issues_a_new_token(): void
    {
        Mail::fake();
        $user = User::factory()->unactivated()->create();

        $this->actingAs($user)->get('/resend-code')->assertRedirect('/verify')->assertSessionHas('message');

        $this->assertNotNull($user->fresh()->token);
        Mail::assertSent(VerificationEmail::class);
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse']);

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-horse'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->get('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/dashboard', '/home', '/crypto', '/transactions', '/loan', '/card', '/user_transactions', '/profile/1', '/verify'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $this->postJson('/validate-otp', ['otp' => '1'])->assertUnauthorized();
    }
}
