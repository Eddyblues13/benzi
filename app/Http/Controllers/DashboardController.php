<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Nft;
use App\Models\Card;
use App\Models\Loan;
use App\Models\User;
use App\Models\Debit;
use App\Mail\nftEmail;
use App\Models\Credit;
use GuzzleHttp\Client;
use App\Models\Deposit;
use App\Models\Transfer;
use App\Mail\nftUserEmail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;


class DashboardController extends Controller
{

    public function transferPage()
    {
        return view('dashboard.transfer');
    }

    public function userProfile()
    {

        return view('dashboard.profile');
    }

    public function skrill()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];

        return view('dashboard.skrill', $data);
    }

    public function crypto()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.crypto', $data);
    }

    public function interBankTransfer()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.inter_bank', $data);
    }

    public function localBankTransfer()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.local_bank', $data);
    }

    public function revolutBankTransfer()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.revolut', $data);
    }
    public function wiseBankTransfer()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.wise', $data);
    }
    public function paypal()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.paypal', $data);
    }

    public function card()
    {

        $data['details'] = Card::where('user_id', Auth::user()->id)->get();
        return view('dashboard.card', $data);
    }

    public function requestCard($user_id)
    {
        $userData = User::where('id', $user_id)->first();
        $user_id = $userData->id;
        $amount = 10;



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];

        if ($amount > $data['balance']) {
            return back()->with('error', ' Your account balance is insufficient, contact our administrator for more info!!');
        }

        $card_number = rand(765039973798, 123449889412);
        $cvc = rand(765, 123);
        $ref = rand(76503737, 12344994);
        $startDate = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime($startDate . '+ 24 months'));


        $card = new Card;
        $card->user_id = $user_id;
        $card->card_number = $card_number;
        $card->card_cvc = $cvc;
        $card->card_expiry = $expiryDate;
        $card->save();

        $transaction = new Transaction;
        $transaction->user_id = $user_id;
        $transaction->transaction_id = $card->id;
        $transaction->transaction_ref = "CD" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Card";
        $transaction->transaction_amount = "10";
        $transaction->transaction_description = "Virtual Card Purchase";
        $transaction->transaction_status = 1;
        $transaction->save();

        return back()->with('status', 'Card Purchased Successfully');
    }








    public function notification()
    {
        return view('dashboard.notification');
    }
    public function transactions()
    {
        $data['transaction'] = Transaction::where('user_id', Auth::user()->id)->get();
        return view('dashboard.transactions', $data);
    }

    public function viewInvoice(Request $request, $tid)
    {

        $data['invoice'] = DB::table('cards')
            ->join('transactions', 'cards.id', '=', 'transactions.transaction_id')
            ->select('cards.*', 'transactions.*')
            ->where('transaction_id', $tid)
            ->get();

        return view('dashboard.view_invoice', $data);

        if ($request['type'] == 'Transfer') {
            $data['invoice'] = DB::table('transfers')
                ->join('transactions', 'transfers.id', '=', 'transactions.transaction_id')
                ->select('transfers.*', 'transactions.*')
                ->where('id', $tid)
                ->get();
            return view('dashboard.transfer_invoice', $data);
        }
    }

    public function pendingTransfer()
    {
        return view('dashboard.pending_transfer');
    }
    public function settings()
    {
        return view('dashboard.settings');
    }

    public function updatePassword(Request $request)
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
    public function profile()
    {
        return view('dashboard.profile');
    }

    public function userChangePassword()
    {
        return view('dashboard.change_password');
    }

    public function deposit()
    {
        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
        return view('dashboard.deposit', $data);
    }

    public function loan()
    {
        $data['outstanding_loan'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['pending_loan'] = Loan::where('user_id', Auth::user()->id)->where('status', '0')->sum('amount');
        $data['transaction'] = Transaction::where('user_id', Auth::user()->id)->where('transaction', 'Loan')->get();
        return view('dashboard.loan', $data);
    }








    public function interTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];



        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }


        $ref = rand(76503737, 12344994);

        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "TR" . $ref;
        $transaction->transaction_ref = "TR" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Bank Transfer";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Bank Transfer transaction";
        $transaction->account_name = $request['account_name'];
        $transaction->account_number = $request['account_number'];
        $transaction->account_type = $request['account_type'];
        $transaction->bank_name = $request['bank_name'];
        $transaction->routing_number = $request['routing_number'];
        $transaction->transaction_status = 0;
        $transaction->save();


        return back()->with('status', 'Withdrawal successful!!');
    }

    public function localTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];



        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }


        $ref = rand(76503737, 12344994);

        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "TR" . $ref;
        $transaction->transaction_ref = "TR" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Bank Transfer";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Bank Transfer transaction";
        $transaction->account_name = $request['account_name'];
        $transaction->account_number = $request['account_number'];
        $transaction->account_type = $request['account_type'];
        $transaction->bank_name = $request['bank_name'];
        $transaction->routing_number = $request['routing_number'];
        $transaction->transaction_status = 0;
        $transaction->save();


        return back()->with('status', 'Withdrawal successful!!');
    }
    public function revolutTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];



        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }



        $ref = rand(76503737, 12344994);


        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "REV" . $ref;
        $transaction->transaction_ref = "REV" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Revolut Withdrawal";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Revolut transaction";
        $transaction->transaction_status = 0;
        $transaction->save();


        return back()->with('status', 'Withdrawal successful!!');
    }

    public function wiseTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];



        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }


        $ref = rand(76503737, 12344994);


        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "WIS" . $ref;
        $transaction->transaction_ref = "WIS" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Wise Withdrawal";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Wise transaction";
        $transaction->transaction_status = 0;
        $transaction->save();

        return back()->with('status', 'Withdrawal successful!!');
    }

    public function paypalTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];


        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }


        $ref = rand(76503737, 12344994);


        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "PAY" . $ref;
        $transaction->transaction_ref = "PAY" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Paypal Withdrawal";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Paypal transaction";
        $transaction->transaction_status = 0;
        $transaction->save();


        return back()->with('status', 'Withdrawal successful!!');
    }




    public function cryptoTransfer(Request $request)
    {

        $amount = $request->input('amount');



        $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->sum('transaction_amount');
        $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->sum('transaction_amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] + $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];


        if ($data['balance'] <= '0') {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }

        if ($data['balance'] < $amount) {
            return back()->with('error', 'Your account balance is insufficient, contact our administrator for more info!!');
        }


        $ref = rand(76503737, 12344994);


        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = "CRP" . $ref;
        $transaction->transaction_ref = "CRP" . $ref;
        $transaction->transaction_type = "Debit";
        $transaction->transaction = "Crypto Withdrawal";
        $transaction->transaction_amount = $request['amount'];
        $transaction->wallet_type = $request['wallet_type'];
        $transaction->wallet_address = $request['wallet_address'];
        $transaction->transaction_description = "Crypto Withdrawal transaction";
        $transaction->transaction_status = 0;
        $transaction->save();

        return back()->with('status', 'Withdrawal successful!!');
    }




    public function validateOtp(Request $request)
    {
        $otp = $request->input('otp');

        if ($otp != Auth::user()->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect OTP number!'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function validateCcic(Request $request)
    {
        $ccic_code = $request->input('ccic_code');

        if ($ccic_code != Auth::user()->ccic_code) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect CCIC code!'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function validateInt(Request $request)
    {
        $int_code = $request->input('int_code');

        if ($int_code != Auth::user()->int_code) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect INT code!'
            ]);
        }

        return response()->json(['success' => true]);
    }
    public function userReflectionPin(Request $request)
    {

        $ssn = $request->input('ssn');


        if ($ssn != Auth::user()->ssn) {
            return back()->with('error', ' Incorrect Reflection Number!');
        }


        return view('dashboard.activate_account');
    }


    public function personalDetails(Request $request)
    {
        // ✅ 1. Validate inputs strictly
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'user_phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'user_address' => 'nullable|string|max:255',
            'country' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // only safe formats, 2MB max
        ]);

        // ✅ 2. Update user record
        $user = Auth::user();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone_number = $validated['user_phone'];
        $user->address = $validated['user_address'] ?? null;
        $user->country = $validated['country'];

        // ✅ 3. Handle profile image securely
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('profile_', true) . '.' . $file->getClientOriginalExtension();

            // store securely in storage/app/public/uploads/display
            $path = $file->storeAs('public/uploads/display', $filename);

            // save only relative path (not absolute system path)
            $user->display_picture = 'uploads/display/' . $filename;
        }

        $user->save();

        // ✅ 4. Response
        return back()->with('status', 'Personal Details Updated Successfully');
    }



    public function personalDp(Request $request)
    {


        $update = Auth::user();



        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/display', $filename);
            $update->display_picture =  $filename;
        }
        $update->update();

        return back()->with('status', 'Personal Details Updated Successfully');
    }




    public function makeDeposit(Request $request)
    {
        // ✅ 1. Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1', // cap deposits
            'front_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'back_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // ✅ 2. Generate safe reference (rand() was insecure)
        $ref = strtoupper('DP' . uniqid());

        // ✅ 3. Create deposit record
        $deposit = new Deposit();
        $deposit->user_id = Auth::id();
        $deposit->amount = $validated['amount'];
        $deposit->status = 0;

        // ✅ 4. Handle cheque uploads safely
        if ($request->hasFile('front_cheque')) {
            $file = $request->file('front_cheque');
            $filename = uniqid('front_', true) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/cheque', $filename);
            $deposit->front_cheque = 'uploads/cheque/' . $filename; // store relative path only
        }

        if ($request->hasFile('back_cheque')) {
            $file = $request->file('back_cheque');
            $filename = uniqid('back_', true) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/cheque', $filename);
            $deposit->back_cheque = 'uploads/cheque/' . $filename;
        }

        $deposit->save();

        // ✅ 5. Create transaction record
        $transaction = new Transaction();
        $transaction->user_id = Auth::id();
        $transaction->transaction_id = $deposit->id;
        $transaction->transaction_ref = $ref;
        $transaction->transaction_type = "Credit";
        $transaction->transaction = "Deposit";
        $transaction->transaction_amount = $validated['amount'];
        $transaction->transaction_description = "Deposit of $" . number_format($validated['amount'], 2);
        $transaction->transaction_status = 0; // set to pending
        $transaction->save();

        // ✅ 6. Return secure response
        return back()->with('status', 'Deposit detected. Please wait for administrator approval.');
    }



    public function makeLoan(Request $request)
    {


        $ssn = $request->input('ssn');
        $amount = $request->input('amount');

        if ($ssn != Auth::user()->ssn) {
            return back()->with('error', ' Incorrect SSN number!');
        }
        if ($amount > Auth::user()->eligible_loan) {
            return back()->with('error', ' You are not eligible, please check your Eligibility or contact our administrator for more info!!');
        }

        $data['user_transfers'] = Transfer::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');
        $data['user_credit'] = Credit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['user_debit'] = Debit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        $data['balance'] = $data['user_deposits'] +  $data['user_credit'] + $data['user_loans'] - $data['user_debit'] - $data['user_card'];

        $ref = rand(76503737, 12344994);



        $loan = new Loan;
        $loan->user_id = Auth::user()->id;
        $loan->amount = $request['amount'];
        $loan->status = 0;
        $loan->save();

        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_id = $loan->id;
        $transaction->transaction_ref = "LN" . $ref;
        $transaction->transaction_type = "Credit";
        $transaction->transaction = "Loan";
        $transaction->transaction_amount = $request['amount'];
        $transaction->transaction_description = "Requested for a loan of " . $request['amount'];
        $transaction->transaction_status = 0;
        $transaction->save();



        return back()->with('status', 'Loan detected, please wait for approval by the administrator');
    }
}
