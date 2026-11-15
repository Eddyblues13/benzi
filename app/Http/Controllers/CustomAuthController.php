<?php

namespace App\Http\Controllers;

use App\Mail\VerificationEmail;
use App\Mail\welcomeEmail;
use App\Models\Card;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CustomAuthController extends Controller
{
    /**
     * Admins land on the user list; customers on their account overview.
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $data['result'] = User::where('user_type', '0')->orderByDesc('id')->get();
            $data['stats'] = [
                'users' => User::where('user_type', '0')->count(),
                'pending' => Transaction::where('transaction_status', Transaction::STATUS_PENDING)->count(),
                'credits' => Transaction::where('transaction_type', Transaction::TYPE_CREDIT)
                    ->where('transaction_status', Transaction::STATUS_APPROVED)->sum('transaction_amount'),
                'debits' => Transaction::where('transaction_type', Transaction::TYPE_DEBIT)
                    ->where('transaction_status', Transaction::STATUS_APPROVED)->sum('transaction_amount'),
            ];

            return view('admin.home', $data);
        }

        $transactions = $user->transactions();

        $data['balance'] = $user->balance();
        $data['recent_transactions'] = $user->transactions()->latest()->take(5)->get();
        $data['overview'] = [
            'withdrawals' => (clone $transactions)->where('transaction_type', Transaction::TYPE_DEBIT)->count(),
            'transfers' => (clone $transactions)->where('transaction_type', Transaction::TYPE_DEBIT)
                ->where('transaction_status', Transaction::STATUS_APPROVED)->count(),
            'cards' => Card::where('user_id', $user->id)->count(),
            'loans' => Loan::where('user_id', $user->id)->count(),
        ];

        return view('dashboard.home', $data);
    }

    public function verify()
    {
        $user = Auth::user();

        if ($user->isActivated() || $user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify', [
            'email' => $user->email,
            'id' => $user->id,
        ]);
    }

    public function emailVerify(Request $request)
    {
        $request->validate([
            'digit' => 'required|string|max:10',
        ], [
            'digit.required' => 'Please enter the activation code sent to your email.',
        ]);

        $user = Auth::user();

        if (! $user->token || ! hash_equals((string) $user->token, trim($request->input('digit')))) {
            return back()->with('error', 'Incorrect Activation Code!');
        }

        $user->forceFill([
            'is_activated' => '1',
            'email_verified_at' => now(),
            'token' => null,
        ])->save();

        try {
            Mail::to($user->email)->send(new welcomeEmail([
                'name' => $user->name,
                'a_number' => $user->a_number,
                'email' => $user->email,
                'password' => '*********',
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('dashboard')->with('status', 'Your account has been verified successfully.');
    }

    public function resendCode()
    {
        $user = Auth::user();

        if ($user->isActivated()) {
            return redirect()->route('dashboard');
        }

        $user->forceFill(['token' => (string) random_int(1000, 9999)])->save();

        try {
            Mail::to($user->email)->send(new VerificationEmail($user->token));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('verify')->with('error', 'We could not send the email right now. Please try again shortly.');
        }

        return redirect()->route('verify')->with('message', 'A new verification code has been sent to your email.');
    }

    public function logOut(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
