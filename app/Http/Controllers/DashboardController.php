<?php

namespace App\Http\Controllers;

use App\Mail\SupportEmail;
use App\Models\Card;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /** Fee charged for issuing a virtual card. */
    public const CARD_FEE = 10;

    /** Wrong guesses allowed per code before a lockout, and the lockout length. */
    private const CODE_MAX_ATTEMPTS = 10;
    private const CODE_LOCKOUT_SECONDS = 900;

    /** The admin-issued codes a customer must supply to move money out. */
    private const SECURITY_CODES = [
        'otp' => 'OTP',
        'ccic_code' => 'CCIC code',
        'int_code' => 'INT code',
    ];

    /* ------------------------------------------------------------------
     | Pages
     * ------------------------------------------------------------------ */

    private function withBalance(string $view, array $extra = [])
    {
        return view($view, array_merge(['balance' => Auth::user()->balance()], $extra));
    }

    public function transferPage()
    {
        return $this->withBalance('dashboard.transfer');
    }

    public function skrill()
    {
        return $this->withBalance('dashboard.skrill');
    }

    public function crypto()
    {
        return $this->withBalance('dashboard.crypto');
    }

    public function interBankTransfer()
    {
        return $this->withBalance('dashboard.inter_bank');
    }

    public function localBankTransfer()
    {
        return $this->withBalance('dashboard.local_bank');
    }

    public function revolutBankTransfer()
    {
        return $this->withBalance('dashboard.revolut');
    }

    public function wiseBankTransfer()
    {
        return $this->withBalance('dashboard.wise');
    }

    public function paypal()
    {
        return $this->withBalance('dashboard.paypal');
    }

    public function deposit()
    {
        return $this->withBalance('dashboard.deposit');
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function userChangePassword()
    {
        return view('dashboard.change_password');
    }

    public function transactions()
    {
        $data['transaction'] = Auth::user()->transactions()->latest()->get();

        return view('dashboard.transactions', $data);
    }

    public function transactionHistory()
    {
        $data['details'] = Auth::user()->transactions()->latest()->get();

        return view('dashboard.transaction_history', $data);
    }

    public function viewInvoice(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 404);

        return view('dashboard.view_invoice', ['invoice' => $transaction]);
    }

    public function loan()
    {
        $userId = Auth::id();

        $data['outstanding_loan'] = Loan::where('user_id', $userId)->where('status', 1)->sum('amount');
        $data['pending_loan'] = Loan::where('user_id', $userId)->where('status', 0)->sum('amount');
        $data['transaction'] = Transaction::where('user_id', $userId)->where('transaction', 'Loan')->latest()->get();

        return view('dashboard.loan', $data);
    }

    public function card()
    {
        $data['details'] = Auth::user()->cards()->latest()->get();
        $data['fee'] = self::CARD_FEE;

        return view('dashboard.card', $data);
    }

    public function ticket()
    {
        return view('dashboard.ticket');
    }

    /* ------------------------------------------------------------------
     | Account actions
     * ------------------------------------------------------------------ */

    public function requestCard()
    {
        $userId = Auth::id();

        $result = DB::transaction(function () use ($userId) {
            $user = User::whereKey($userId)->lockForUpdate()->first();

            if ($user->balance() < self::CARD_FEE) {
                return 'Your account balance is insufficient for the ' . $user->currency . self::CARD_FEE . ' card fee.';
            }

            $card = Card::create([
                'user_id' => $user->id,
                'card_number' => '4' . str_pad((string) random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT),
                'card_cvc' => (string) random_int(100, 999),
                'card_expiry' => now()->addMonths(24)->toDateString(),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'transaction_id' => $card->id,
                'transaction_ref' => Transaction::makeReference('CD'),
                'transaction_type' => Transaction::TYPE_DEBIT,
                'transaction' => 'Card',
                'transaction_amount' => self::CARD_FEE,
                'transaction_description' => 'Virtual Card Purchase',
                'transaction_status' => Transaction::STATUS_APPROVED,
            ]);

            return true;
        });

        return $result === true
            ? back()->with('status', 'Card purchased successfully.')
            : back()->with('error', $result);
    }

    public function submitTicket(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $recipient = config('app.admin_email');

        if (! $recipient) {
            return back()->withInput()->with('error', 'Support email is not configured. Please contact us via live chat.');
        }

        try {
            Mail::to($recipient)->send(new SupportEmail([
                'name' => $user->name,
                'email' => $user->email,
                'a_number' => $user->a_number,
                'subject' => $data['subject'],
                'message' => $data['message'],
            ]));
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'We could not send your ticket right now. Please try again shortly.');
        }

        return back()->with('status', 'Your ticket has been sent. Our support team will contact you by email.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->old_password, $user->password)) {
            return back()->with('error', "Old Password Doesn't match! Please input your correct old password");
        }

        $user->forceFill(['password' => Hash::make($request->new_password)])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Password updated successfully, please log in with your new password.');
    }

    public function personalDetails(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'user_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'user_address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $user = Auth::user();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone_number = $validated['user_phone'] ?? $user->phone_number;
        $user->address = $validated['user_address'] ?? null;
        $user->country = $validated['country'] ?? $user->country;

        if ($request->hasFile('image')) {
            $this->replaceDisplayPicture($user, $request->file('image'));
        }

        $user->save();

        return back()->with('status', 'Personal details updated successfully.');
    }

    public function personalDp(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $user = Auth::user();
        $this->replaceDisplayPicture($user, $request->file('image'));
        $user->save();

        return back()->with('status', 'Profile picture updated successfully.');
    }

    /**
     * Store an uploaded avatar in public/uploads/display (which is where every
     * view reads it from) and remove the previous one.
     */
    private function replaceDisplayPicture(User $user, $file): void
    {
        $directory = public_path('uploads/display');
        $filename = 'profile_' . $user->id . '_' . bin2hex(random_bytes(6)) . '.' . $file->extension();
        $file->move($directory, $filename);

        $old = $user->display_picture;
        if ($old && $old !== 'avatar.png' && basename($old) === $old && is_file($directory . '/' . $old)) {
            @unlink($directory . '/' . $old);
        }

        $user->display_picture = $filename;
    }

    public function makeDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:100000000',
            'front_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'back_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $deposit = new Deposit();
            $deposit->user_id = Auth::id();
            $deposit->amount = $validated['amount'];
            $deposit->status = 0;

            foreach (['front_cheque', 'back_cheque'] as $field) {
                if ($request->hasFile($field)) {
                    $deposit->{$field} = $request->file($field)->store('uploads/cheque', 'public');
                }
            }

            $deposit->save();

            Transaction::create([
                'user_id' => Auth::id(),
                'transaction_id' => $deposit->id,
                'transaction_ref' => Transaction::makeReference('DP'),
                'transaction_type' => Transaction::TYPE_CREDIT,
                'transaction' => 'Deposit',
                'transaction_amount' => $validated['amount'],
                'transaction_description' => 'Cheque deposit of ' . Auth::user()->currency . number_format($validated['amount'], 2),
                'transaction_status' => Transaction::STATUS_PENDING,
            ]);
        });

        return back()->with('status', 'Deposit received. It will reflect in your balance once approved by an administrator.');
    }

    public function makeLoan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'ssn' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->with('error', $validator->errors()->first());
        }

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        if (! $user->ssn) {
            return back()->with('error', 'Loans are not yet enabled for your account. Please contact support.');
        }

        if (! hash_equals((string) $user->ssn, trim((string) $request->input('ssn')))) {
            return back()->with('error', 'Incorrect SSN number!');
        }

        if ($amount > (float) $user->eligible_loan) {
            return back()->with('error', 'The amount exceeds your eligible loan of ' . $user->currency . number_format((float) $user->eligible_loan, 2) . '. Contact our administrator for more info.');
        }

        DB::transaction(function () use ($user, $amount) {
            $loan = Loan::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 0,
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'transaction_id' => $loan->id,
                'transaction_ref' => Transaction::makeReference('LN'),
                'transaction_type' => Transaction::TYPE_CREDIT,
                'transaction' => 'Loan',
                'transaction_amount' => $amount,
                'transaction_description' => 'Loan request of ' . $user->currency . number_format($amount, 2),
                'transaction_status' => Transaction::STATUS_PENDING,
            ]);
        });

        return back()->with('status', 'Loan request received, please wait for approval by the administrator.');
    }

    /* ------------------------------------------------------------------
     | Withdrawals / transfers
     * ------------------------------------------------------------------ */

    public function transferFunds(Request $request)
    {
        return $this->processWithdrawal($request, 'Bank Transfer', 'TR', [
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:150',
            'routing_number' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ], fn ($v) => [
            'account_name' => $v['account_name'],
            'account_number' => $v['account_number'],
            'bank_name' => $v['bank_name'],
            'routing_number' => $v['routing_number'],
        ], fn ($v) => $v['description'] ?? null);
    }

    public function interTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'International Transfer', 'INT', [
            'holder_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:150',
            'iban_number' => 'required|string|max:50',
            'swift_code' => 'required|string|max:20',
            'bank_address' => 'required|string|max:255',
            'country' => 'required|string|max:100',
        ], fn ($v) => [
            'account_name' => $v['holder_name'],
            'account_number' => $v['account_number'],
            'bank_name' => $v['bank_name'],
            'iban_number' => $v['iban_number'],
            'swift_code' => $v['swift_code'],
            'bank_address' => $v['bank_address'],
            'country' => $v['country'],
        ]);
    }

    public function localTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Local Transfer', 'LT', [
            'holder_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:150',
        ], fn ($v) => [
            'account_name' => $v['holder_name'],
            'account_number' => $v['account_number'],
            'bank_name' => $v['bank_name'],
        ]);
    }

    public function revolutTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Revolut Withdrawal', 'REV', [
            'card_number' => 'required|string|max:30',
            'holder_name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
        ], fn ($v) => [
            'card_number' => $v['card_number'],
            'account_name' => $v['holder_name'],
            'recipient_email' => $v['email'],
        ]);
    }

    public function wiseTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Wise Withdrawal', 'WIS', [
            'email' => 'required|email|max:255',
        ], fn ($v) => ['recipient_email' => $v['email']]);
    }

    public function paypalTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Paypal Withdrawal', 'PAY', [
            'email' => 'required|email|max:255',
        ], fn ($v) => ['recipient_email' => $v['email']]);
    }

    public function skrillTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Skrill Withdrawal', 'SKR', [
            'email' => 'required|email|max:255',
        ], fn ($v) => ['recipient_email' => $v['email']]);
    }

    public function cryptoTransfer(Request $request)
    {
        return $this->processWithdrawal($request, 'Crypto Withdrawal', 'CRP', [
            'wallet_type' => 'required|string|max:50',
            'other_wallet_type' => 'required_if:wallet_type,Other|nullable|string|max:50',
            'wallet_address' => 'required|string|max:150',
        ], fn ($v) => [
            'wallet_type' => $v['wallet_type'] === 'Other' ? $v['other_wallet_type'] : $v['wallet_type'],
            'wallet_address' => $v['wallet_address'],
        ]);
    }

    /**
     * Shared handler for every outgoing transfer: validates input, re-checks
     * the security codes server-side, and records a pending debit while
     * holding a row lock so concurrent requests cannot overdraw the account.
     */
    private function processWithdrawal(Request $request, string $channel, string $prefix, array $rules, callable $details, ?callable $description = null)
    {
        $rules = array_merge(['amount' => 'required|numeric|min:1|max:100000000'], $rules);
        foreach (array_keys(self::SECURITY_CODES) as $field) {
            $rules[$field] = 'required|string|max:50';
        }

        $validator = Validator::make($request->all(), $rules, [], [
            'otp' => 'OTP',
            'ccic_code' => 'CCIC code',
            'int_code' => 'INT code',
        ]);

        if ($validator->fails()) {
            return back()->withInput($request->except(array_keys(self::SECURITY_CODES)))
                ->with('error', ' ' . $validator->errors()->first());
        }

        $validated = $validator->validated();

        if ($error = $this->securityCodeError(Auth::user(), $validated)) {
            return back()->withInput($request->except(array_keys(self::SECURITY_CODES)))->with('error', ' ' . $error);
        }

        $amount = round((float) $validated['amount'], 2);
        $userId = Auth::id();

        $result = DB::transaction(function () use ($userId, $amount, $channel, $prefix, $details, $description, $validated) {
            $user = User::whereKey($userId)->lockForUpdate()->first();

            if ($amount > $user->balance()) {
                return null;
            }

            return Transaction::create(array_merge([
                'user_id' => $user->id,
                'transaction_ref' => $ref = Transaction::makeReference($prefix),
                'transaction_id' => $ref,
                'transaction_type' => Transaction::TYPE_DEBIT,
                'transaction' => $channel,
                'transaction_amount' => $amount,
                'transaction_description' => ($description ? $description($validated) : null) ?: $channel,
                'transaction_status' => Transaction::STATUS_PENDING,
            ], $details($validated)));
        });

        if (! $result) {
            return back()->withInput($request->except(array_keys(self::SECURITY_CODES)))
                ->with('error', ' Your account balance is insufficient, contact our administrator for more info!!');
        }

        return back()->with('status', 'Your ' . strtolower($channel) . ' of ' . Auth::user()->currency
            . number_format($amount, 2) . ' (ref ' . $result->transaction_ref . ') has been submitted and is pending approval.');
    }

    private function securityCodeError(User $user, array $input): ?string
    {
        foreach (array_keys(self::SECURITY_CODES) as $field) {
            if ($error = $this->verifyCode($user, $field, $input[$field] ?? '')) {
                return $error;
            }
        }

        return null;
    }

    /**
     * Check one security code, locking the code out after repeated wrong
     * guesses so it cannot be brute-forced. Returns an error message or null.
     */
    private function verifyCode(User $user, string $field, $given): ?string
    {
        $label = self::SECURITY_CODES[$field];
        $expected = (string) $user->{$field};
        $key = "security-code:{$user->id}:{$field}";

        if ($expected === '') {
            return "No {$label} has been issued for your account yet. Please contact support.";
        }

        if (RateLimiter::tooManyAttempts($key, self::CODE_MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return "Too many incorrect {$label} attempts. Please try again in {$minutes} minute(s) or contact support.";
        }

        if (! hash_equals($expected, trim((string) $given))) {
            RateLimiter::hit($key, self::CODE_LOCKOUT_SECONDS);

            return "Incorrect {$label}!";
        }

        RateLimiter::clear($key);

        return null;
    }

    /* ------------------------------------------------------------------
     | AJAX code checks used by the transfer modals
     * ------------------------------------------------------------------ */

    public function validateOtp(Request $request)
    {
        return $this->checkCode($request, 'otp');
    }

    public function validateCcic(Request $request)
    {
        return $this->checkCode($request, 'ccic_code');
    }

    public function validateInt(Request $request)
    {
        return $this->checkCode($request, 'int_code');
    }

    private function checkCode(Request $request, string $field)
    {
        $error = $this->verifyCode(Auth::user(), $field, $request->input($field, ''));

        return response()->json($error ? ['success' => false, 'message' => $error] : ['success' => true]);
    }
}
