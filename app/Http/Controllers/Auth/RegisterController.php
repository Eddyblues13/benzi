<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | Creates the account, emails a 4-digit activation code and logs the user
    | in. The `activated` middleware then keeps them on the verify page until
    | the code is confirmed.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'account_type' => ['required', Rule::in(['Savings', 'Checking'])],
            'currency' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'account_type.in' => 'Please select an account type.',
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'token' => (string) random_int(1000, 9999),
            'address' => $data['address'],
            'phone_number' => $data['phone'],
            'country' => $data['country'],
            'account_type' => $data['account_type'],
            'currency' => self::currencySymbol($data['currency'] ?? null, $data['country']),
            'a_number' => self::generateAccountNumber(),
            'password' => Hash::make($data['password']),
        ]);

        try {
            Mail::to($user->email)->send(new VerificationEmail($user->token));
        } catch (\Throwable $e) {
            report($e);
        }

        return $user;
    }

    /**
     * Resolve the currency symbol: the chosen currency country first, then the
     * country of residence, then dollars.
     */
    public static function currencySymbol(?string $currencyCountry, ?string $country): string
    {
        $map = config('currencies', []);

        return $map[$currencyCountry] ?? $map[$country] ?? '$';
    }

    public static function generateAccountNumber(): string
    {
        do {
            $number = (string) random_int(1000000000, 9999999999);
        } while (User::where('a_number', $number)->exists());

        return $number;
    }
}
