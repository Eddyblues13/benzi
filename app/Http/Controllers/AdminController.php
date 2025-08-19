<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Nft;
use App\Models\Card;
use App\Models\Loan;
use App\Models\User;
use App\Models\Debit;
use App\Models\Credit;
use GuzzleHttp\Client;
use App\Models\Deposit;
use App\Mail\DebitEmail;
use App\Models\Transfer;
use App\Mail\CreditEmail;
use App\Mail\nftUserEmail;
use App\Mail\ApproveEmail;
use App\Mail\DeclineEmail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function users()
    {
        return view('admin.users');
    }


    public function updateWallet(Request $request)
    {


        $update = Auth::user();
        $update->wallet_address = $request['wallet_address'];
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('admin/uploads/admin', $filename);
            $update->bar_code =  $filename;
        }

        $update->save();
        return back()->with('status', 'Wallet Details Updated Successfully');
    }






    public function usersTransaction()
    {
        $user_transactions = Transaction::orderBy('id', 'desc')->get();
        return view('admin.transactions', compact('user_transactions'));
    }

    public function deleteUser($id)
    {

        $user  = User::findOrFail($id);
        $user->delete();
        return back()->with('status', 'User deleted Successfully');
    }




    public function userProfile($id)
    {



        $data['credit_transfers'] = Transaction::where('user_id', $id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', $id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', $id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', $id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', $id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', $id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', $id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];

        $userProfile = DB::table('users')->where('id', $id)->first();
        $user_transactions = Transaction::where('user_id', $id)->orderBy('id', 'desc')->get();


        return view('admin.user', $data, compact('userProfile', 'user_transactions'));
    }



    public function adminChangePassword()
    {
        return view('admin.change_password');
    }

    public function adminUpdatePassword(Request $request)
    {
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);


        #Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            $data['message'] = 'old password not correct';
            return back()->with("error", "Old Password Doesn't match! Please input your correct old password");
        }


        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        Session::flush();
        Auth::guard('web')->logout();
        return redirect('login')->with('status', 'Password Updated Successfully, Please login with your new password');
    }

    public function ApproveId(Request $request, $id)
    {




        $user = array();
        $user['id_card_status'] = $request->status;
        $update = DB::table('users')->where('id', $id)->update($user);



        return back()->with('status', 'Kyc Details Updated Successfully');
    }

    public function creditUser(Request $request)
    {

        $ref = rand(76503737, 12344994);
        $credit = new Credit;
        $credit->user_id = $request['id'];
        $credit->amount =  $request['amount'];
        $credit->description =  $request['description'];
        $credit->status = 1;
        $credit->save();

        $transaction = new Transaction;
        $transaction->user_id = $request['id'];
        $transaction->transaction_id = $credit->id;
        $ref = $transaction->transaction_ref = "CD" . $ref;
        $transaction->transaction_type = "Credit";
        $transaction->transaction = "Credit";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Credit transaction";
        $transaction->transaction_status = 1;
        $transaction->save();

        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $balance =  $request['balance'] + $request['amount'];
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];

        $user = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'balance' => $balance,
            'currency' => $currency,
            'ref' => $ref,
        ];

        Mail::to($email)->send(new CreditEmail($user));



        return back()->with('status', 'User account credit successfully');
    }


    public function debitUser(Request $request)
    {




        $ref = rand(76503737, 12344994);
        $debit = new Debit;
        $debit->user_id = $request['id'];
        $debit->amount =  $request['amount'];
        $debit->description =  $request['description'];
        $debit->status = 1;
        $debit->save();

        $transaction = new Transaction;
        $transaction->user_id = $request['id'];
        $transaction->transaction_id = $debit->id;
        $ref = $transaction->transaction_ref = "DB" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Debit";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Debit Transaction";
        $transaction->transaction_status = 1;
        $transaction->save();



        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $balance =  $request['balance'] - $request['amount'];
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];

        $user = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'balance' => $balance,
            'currency' => $currency,
            'ref' => $ref,
        ];

        Mail::to($email)->send(new DebitEmail($user));

        return back()->with('status', 'User account debit successfully');
    }

    public function verifyUser($id)
    {


        $user = array();
        $user['user_status'] = 1;
        $update = DB::table('users')->where('id', $id)->update($user);

        return back()->with('status', 'User verified Updated Successfully');
    }

    public function updateOtp(Request $request, $id)
    {




        $user = array();
        $user['otp'] = $request->otp;
        $update = DB::table('users')->where('id', $id)->update($user);



        return back()->with('status', 'user OTP Updated Successfully');
    }

    public function updateSsn(Request $request, $id)
    {

        $user = array();
        $user['ssn'] = $request->reflection;
        $update = DB::table('users')->where('id', $id)->update($user);

        return back()->with('status', 'user SSN Updated Successfully');
    }


    public function updateCicCode(Request $request, $id)
    {

        $user = array();
        $user['ccic_code'] = $request->cic_code;
        $update = DB::table('users')->where('id', $id)->update($user);

        return back()->with('status', 'user CIC Updated Successfully');
    }

    public function updateIntCode(Request $request, $id)
    {

        $user = array();
        $user['int_code'] = $request->int_code;
        $update = DB::table('users')->where('id', $id)->update($user);

        return back()->with('status', 'user INT Updated Successfully');
    }

    public function approveTransaction(Request $request, $id)
    {
        $user_id = $request->user_id;


        $transaction = array();
        $transaction['transaction_status'] = 1;

        $update = DB::table('transactions')->where('id', $id)->update($transaction);

        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];
        $ref = $request['ref'];

        $user = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'currency' => $currency,
            'ref' => $ref,
        ];

        Mail::to($email)->send(new ApproveEmail($user));
        return redirect()->back()->with('message', 'Transaction Has Been Approved Successfully');
    }

    public function declineTransaction(Request $request, $id)
    {
        $user_id = $request->user_id;


        $transaction = array();
        $transaction['transaction_status'] = 2;

        $update = DB::table('transactions')->where('id', $id)->update($transaction);

        $email = $request->email;
        $amount = $request->amount;
        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];
        $ref = $request['ref'];

        $user = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'currency' => $currency,
            'ref' => $ref,
        ];

        Mail::to($email)->send(new DeclineEmail($user));
        return redirect()->back()->with('message', 'Transaction Has Been Declined Successfully');
    }


    public function resetPassword(Request $request, $user_id)
    {
        $password =  $request['password'];
        $user = User::findOrFail($user_id);


        $user->update([
            'password' => Hash::make($password),
        ]);

        return back()->with('message', 'Password has been reset successfully.');
    }
}
