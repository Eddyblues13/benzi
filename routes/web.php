<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public website
Route::view('/', 'home.homepage');
Route::view('/about', 'home.about');
Route::view('/contact', 'home.contact');
Route::view('/terms', 'home.terms');
Route::view('/services', 'home.services');

// Login, registration and password reset (Laravel UI controllers)
Auth::routes(['verify' => false]);
Route::get('/logout', [CustomAuthController::class, 'logOut'])->name('logOut');

/*
|--------------------------------------------------------------------------
| Logged-in users
|--------------------------------------------------------------------------
*/
Route::middleware('user_auth')->group(function () {

    // Email activation code (registration step 2)
    Route::get('verify', [CustomAuthController::class, 'verify'])->name('verify');
    Route::post('email-verify', [CustomAuthController::class, 'emailVerify'])->middleware('throttle:verify-code')->name('code');
    Route::get('resend-code', [CustomAuthController::class, 'resendCode'])->middleware('throttle:resend-code')->name('resendCode');

    Route::middleware('activated')->group(function () {

        // Admins and customers share the landing page; it renders per role.
        Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('home', [CustomAuthController::class, 'dashboard'])->name('home');

        /*
        |------------------------------------------------------------------
        | Customer dashboard
        |------------------------------------------------------------------
        */

        // Account pages
        Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
        Route::get('user-profile', [DashboardController::class, 'profile'])->name('user.profile');
        Route::get('settings', fn () => redirect()->route('profile'))->name('settings');
        Route::get('change-password', [DashboardController::class, 'userChangePassword'])->name('user.change.password');
        Route::post('change-password', [DashboardController::class, 'updatePassword'])->name('update-password');
        Route::post('personal-details', [DashboardController::class, 'personalDetails'])->name('personal.details');
        Route::post('personal-dp', [DashboardController::class, 'personalDp'])->name('personal.dp');

        // History
        Route::get('transactions', [DashboardController::class, 'transactions'])->name('transactions');
        Route::get('transaction-history', [DashboardController::class, 'transactionHistory'])->name('transaction.history');
        Route::get('view_invoice/{transaction}', [DashboardController::class, 'viewInvoice'])->name('view.invoice');

        // Deposits, loans, cards, support
        Route::get('deposit', [DashboardController::class, 'deposit'])->name('deposit');
        Route::post('make-deposit', [DashboardController::class, 'makeDeposit'])->name('make.deposit');
        Route::get('loan', [DashboardController::class, 'loan'])->name('loan');
        Route::post('make-loan', [DashboardController::class, 'makeLoan'])->name('make.loan');
        Route::get('card', [DashboardController::class, 'card'])->name('card');
        Route::post('request-card', [DashboardController::class, 'requestCard'])->name('request.card');
        Route::get('ticket', [DashboardController::class, 'ticket'])->name('ticket');
        Route::post('ticket', [DashboardController::class, 'submitTicket'])->middleware('throttle:tickets')->name('ticket.submit');

        // Transfer / withdrawal forms
        Route::get('transfer', [DashboardController::class, 'transferPage'])->name('transfer.page');
        Route::get('inter_bank_transfer', [DashboardController::class, 'interBankTransfer'])->name('inter.bank.transfer');
        Route::get('local_bank_transfer', [DashboardController::class, 'localBankTransfer'])->name('local.bank.transfer');
        Route::get('international-transfer', fn () => redirect()->route('inter.bank.transfer'))->name('international-transfer');
        Route::get('domestic-transfer', fn () => redirect()->route('local.bank.transfer'))->name('domestic-transfer');
        Route::get('revolut_bank_transfer', [DashboardController::class, 'revolutBankTransfer'])->name('revolut.bank.transfer');
        Route::get('wise_bank_transfer', [DashboardController::class, 'wiseBankTransfer'])->name('wise.bank.transfer');
        Route::get('paypal', [DashboardController::class, 'paypal'])->name('paypal');
        Route::get('skrill', [DashboardController::class, 'skrill'])->name('skrill');
        Route::get('crypto', [DashboardController::class, 'crypto'])->name('crypto');

        // Transfer / withdrawal submissions
        Route::middleware('throttle:transfers')->group(function () {
            Route::post('transfer_funds', [DashboardController::class, 'transferFunds'])->name('transfer.funds');
            Route::post('inter-transfer', [DashboardController::class, 'interTransfer'])->name('inter.transfer');
            Route::post('local-transfer', [DashboardController::class, 'localTransfer'])->name('local.transfer');
            Route::post('revolut-transfer', [DashboardController::class, 'revolutTransfer'])->name('revolut.transfer');
            Route::post('wise-transfer', [DashboardController::class, 'wiseTransfer'])->name('wise.transfer');
            Route::post('paypal-transfer', [DashboardController::class, 'paypalTransfer'])->name('paypal.transfer');
            Route::post('skrill-transfer', [DashboardController::class, 'skrillTransfer'])->name('skrill.transfer');
            Route::post('crypto-transfer', [DashboardController::class, 'cryptoTransfer'])->name('crypto.transfer');
        });

        // Security-code checks used by the transfer modals (wrong guesses are also locked out in the controller)
        Route::middleware('throttle:code-checks')->group(function () {
            Route::post('validate-otp', [DashboardController::class, 'validateOtp'])->name('validate.otp');
            Route::post('validate-ccic', [DashboardController::class, 'validateCcic'])->name('validate.ccic');
            Route::post('validate-int', [DashboardController::class, 'validateInt'])->name('validate.int');
        });

        /*
        |------------------------------------------------------------------
        | Administrator
        |------------------------------------------------------------------
        */
        Route::middleware('admin')->group(function () {
            Route::get('users', [AdminController::class, 'users'])->name('view.users');
            Route::get('user_transactions', [AdminController::class, 'usersTransaction'])->name('user.transaction');
            Route::get('profile/{id}', [AdminController::class, 'userProfile'])->whereNumber('id')->name('admin.user.profile');
            Route::post('delete/{id}', [AdminController::class, 'deleteUser'])->whereNumber('id')->name('delete.user');

            Route::get('update_wallet', [AdminController::class, 'eth'])->name('update.wallet');
            Route::post('admin_update_wallet', [AdminController::class, 'updateWallet'])->name('admin.save.wallet');
            Route::get('admin-change-password', [AdminController::class, 'adminChangePassword'])->name('admin.change.password');
            Route::post('admin-update-password', [AdminController::class, 'adminUpdatePassword'])->name('admin.update.password');

            Route::post('credit-user', [AdminController::class, 'creditUser'])->name('credit.user');
            Route::post('debit-user', [AdminController::class, 'debitUser'])->name('debit.user');
            Route::post('approve-transaction/{id}', [AdminController::class, 'approveTransaction'])->whereNumber('id')->name('approve.transaction');
            Route::post('decline-transaction/{id}', [AdminController::class, 'declineTransaction'])->whereNumber('id')->name('decline.transaction');

            Route::post('verify_user/{id}', [AdminController::class, 'verifyUser'])->whereNumber('id')->name('verify_user');
            Route::post('approve-id_card/{id}', [AdminController::class, 'ApproveId'])->whereNumber('id')->name('approve.id');
            Route::post('update_otp/{id}', [AdminController::class, 'updateOtp'])->whereNumber('id')->name('update.otp');
            Route::post('update_cic/{id}', [AdminController::class, 'updateCicCode'])->whereNumber('id')->name('update.cic');
            Route::post('update_int/{id}', [AdminController::class, 'updateIntCode'])->whereNumber('id')->name('update.int');
            Route::post('update_reflection/{id}', [AdminController::class, 'updateSsn'])->whereNumber('id')->name('update.reflection');
            Route::post('update_eligible_loan/{id}', [AdminController::class, 'updateEligibleLoan'])->whereNumber('id')->name('update.eligible.loan');
            Route::post('admin_reset-password/{user}', [AdminController::class, 'resetPassword'])->whereNumber('user')->name('reset.password');
        });
    });
});
