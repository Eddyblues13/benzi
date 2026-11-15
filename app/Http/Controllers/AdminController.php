<?php

namespace App\Http\Controllers;

use App\Mail\ApproveEmail;
use App\Mail\CreditEmail;
use App\Mail\DebitEmail;
use App\Mail\DeclineEmail;
use App\Models\Credit;
use App\Models\Debit;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /* ------------------------------------------------------------------
     | Pages
     * ------------------------------------------------------------------ */

    public function users()
    {
        return redirect()->route('dashboard');
    }

    public function usersTransaction(Request $request)
    {
        $query = Transaction::with('user')->latest();

        if ($type = $request->query('type')) {
            $query->where('transaction', $type);
        }

        if ($request->query('status') !== null && $request->query('status') !== '') {
            $query->where('transaction_status', (int) $request->query('status'));
        }

        return view('admin.transactions', [
            'user_transactions' => $query->get(),
            'filter_type' => $type,
            'filter_status' => $request->query('status'),
        ]);
    }

    public function userProfile($id)
    {
        $userProfile = User::findOrFail($id);
        $user_transactions = $userProfile->transactions()->latest()->get();

        $deposits = Deposit::where('user_id', $userProfile->id)->get()->keyBy('id');

        return view('admin.user', [
            'userProfile' => $userProfile,
            'user_transactions' => $user_transactions,
            'balance' => $userProfile->balance(),
            'deposits' => $deposits,
        ]);
    }

    public function eth()
    {
        return view('admin.ethereum');
    }

    public function adminChangePassword()
    {
        return view('admin.change_password');
    }

    /* ------------------------------------------------------------------
     | Admin's own account
     * ------------------------------------------------------------------ */

    public function updateWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'nullable|string|max:150',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        $admin = Auth::user();
        $admin->wallet_address = $request->input('wallet_address');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'wallet_' . bin2hex(random_bytes(6)) . '.' . $file->extension();
            $file->move(public_path('admin/uploads/admin'), $filename);
            $admin->bar_code = $filename;
        }

        $admin->save();

        return back()->with('status', 'Wallet details updated successfully.');
    }

    public function adminUpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::user();

        if (! Hash::check($request->old_password, $admin->password)) {
            return back()->with('error', "Old Password Doesn't match! Please input your correct old password");
        }

        $admin->forceFill(['password' => Hash::make($request->new_password)])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Password updated successfully, please log in with your new password.');
    }

    /* ------------------------------------------------------------------
     | Customer management
     * ------------------------------------------------------------------ */

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id() || $user->isAdmin()) {
            return back()->with('error', 'Administrator accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()->route('dashboard')->with('status', 'User deleted successfully.');
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->user_status = (string) $user->user_status === '1' ? '0' : '1';
        $user->save();

        return back()->with('status', $user->user_status === '1' ? 'User verified successfully.' : 'User verification removed.');
    }

    public function ApproveId(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:0,1,2']);

        $user = User::findOrFail($id);
        $user->id_card_status = $request->input('status');
        $user->save();

        return back()->with('status', 'KYC details updated successfully.');
    }

    public function updateOtp(Request $request, $id)
    {
        return $this->updateUserField($request, $id, 'otp', 'otp', 'OTP');
    }

    public function updateCicCode(Request $request, $id)
    {
        return $this->updateUserField($request, $id, 'cic_code', 'ccic_code', 'CCIC code');
    }

    public function updateIntCode(Request $request, $id)
    {
        return $this->updateUserField($request, $id, 'int_code', 'int_code', 'INT code');
    }

    public function updateSsn(Request $request, $id)
    {
        return $this->updateUserField($request, $id, 'reflection', 'ssn', 'SSN');
    }

    public function updateEligibleLoan(Request $request, $id)
    {
        $request->validate(['eligible_loan' => 'required|numeric|min:0|max:100000000']);

        $user = User::findOrFail($id);
        $user->eligible_loan = $request->input('eligible_loan');
        $user->save();

        return back()->with('status', 'Eligible loan amount updated successfully.');
    }

    private function updateUserField(Request $request, $id, string $input, string $column, string $label)
    {
        $request->validate([$input => 'required|string|max:50'], [], [$input => $label]);

        $user = User::findOrFail($id);
        $user->{$column} = trim($request->input($input));
        $user->save();

        return back()->with('status', "User {$label} updated successfully.");
    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);

        $user = User::findOrFail($user_id);
        $user->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return back()->with('status', 'Password has been reset successfully.');
    }

    /* ------------------------------------------------------------------
     | Money movements
     * ------------------------------------------------------------------ */

    public function creditUser(Request $request)
    {
        return $this->adjustBalance($request, Transaction::TYPE_CREDIT);
    }

    public function debitUser(Request $request)
    {
        return $this->adjustBalance($request, Transaction::TYPE_DEBIT);
    }

    private function adjustBalance(Request $request, string $type)
    {
        $data = $request->validate([
            'id' => ['required', Rule::exists('users', 'id')],
            'amount' => 'required|numeric|min:0.01|max:100000000',
            'description' => 'required|string|max:255',
        ]);

        $isCredit = $type === Transaction::TYPE_CREDIT;
        $amount = round((float) $data['amount'], 2);

        $transaction = DB::transaction(function () use ($data, $isCredit, $amount, $type) {
            $user = User::whereKey($data['id'])->lockForUpdate()->first();

            $model = $isCredit ? new Credit() : new Debit();
            $model->user_id = $user->id;
            $model->amount = $amount;
            $model->description = $data['description'];
            $model->status = 1;
            $model->save();

            return Transaction::create([
                'user_id' => $user->id,
                'transaction_id' => $model->id,
                'transaction_ref' => Transaction::makeReference($isCredit ? 'CR' : 'DB'),
                'transaction_type' => $type,
                'transaction' => $type,
                'transaction_amount' => $amount,
                'transaction_description' => $data['description'],
                'transaction_status' => Transaction::STATUS_APPROVED,
            ]);
        });

        $user = $transaction->user;
        $this->notify($user, $isCredit ? CreditEmail::class : DebitEmail::class, $transaction);

        return back()->with('status', 'User account ' . ($isCredit ? 'credited' : 'debited') . ' successfully.');
    }

    public function approveTransaction($id)
    {
        return $this->settleTransaction($id, Transaction::STATUS_APPROVED);
    }

    public function declineTransaction($id)
    {
        return $this->settleTransaction($id, Transaction::STATUS_DECLINED);
    }

    /**
     * Approve or decline a pending transaction and keep the linked deposit or
     * loan record in step with it.
     */
    private function settleTransaction($id, int $status)
    {
        $transaction = DB::transaction(function () use ($id, $status) {
            $transaction = Transaction::whereKey($id)->lockForUpdate()->firstOrFail();

            if (! $transaction->isPending()) {
                return null;
            }

            $transaction->transaction_status = $status;
            $transaction->save();

            $linked = match ($transaction->transaction) {
                'Deposit' => Deposit::find($transaction->transaction_id),
                'Loan' => Loan::find($transaction->transaction_id),
                default => null,
            };

            if ($linked) {
                $linked->status = $status;
                $linked->save();
            }

            return $transaction;
        });

        if (! $transaction) {
            return back()->with('error', 'This transaction has already been processed.');
        }

        $approved = $status === Transaction::STATUS_APPROVED;

        if ($transaction->user) {
            $this->notify($transaction->user, $approved ? ApproveEmail::class : DeclineEmail::class, $transaction);
        }

        return back()->with('message', 'Transaction has been ' . ($approved ? 'approved' : 'declined') . ' successfully.');
    }

    /**
     * Email the customer about a transaction. A mail failure is logged but
     * never undoes the money movement that has already been committed.
     */
    private function notify(User $user, string $mailable, Transaction $transaction): void
    {
        try {
            Mail::to($user->email)->send(new $mailable([
                'account_number' => $user->a_number,
                'account_name' => $user->name,
                'full_name' => $user->name,
                'description' => $transaction->transaction_description,
                'amount' => number_format((float) $transaction->transaction_amount, 2),
                'date' => $transaction->updated_at ?? now(),
                'balance' => number_format($user->balance(), 2),
                'currency' => $user->currency,
                'ref' => $transaction->transaction_ref,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
