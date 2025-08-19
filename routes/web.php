<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('home.homepage');
});

Route::get('/about', function () {
    return view('home.about');
});
Route::get('/contact', function () {
    return view('home.contact');
});

Route::get('/terms', function () {
    return view('home.terms');
});

Route::get('/services', function () {
    return view('home.services');
});





Auth::routes();

Route::get('home', [CustomAuthController::class, 'dashboard'])->middleware('user_auth')->name('dashboard');
Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->middleware('user_auth')->name('dashboard');
Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('verify/{id}', [CustomAuthController::class, 'verify'])->name('verify');
Route::post('email-verify', [CustomAuthController::class, 'emailVerify'])->name('code');
Route::get('resend-code/{id}', [CustomAuthController::class, 'resendCode'])->name('resendCode');
Route::get('register', [CustomAuthController::class, 'registration'])->name('register-user');
Route::get('register', [CustomAuthController::class, 'registration'])->name('register');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
Route::get('log_out', [CustomAuthController::class, 'signOut'])->name('logout');
Route::get('/logout', [CustomAuthController::class, 'logOut'])->name('logOut');



//User Dashboard routes



Route::get('card', [DashboardController::class, 'card'])->middleware('user_auth')->name('card');
Route::post('make-deposit', [DashboardController::class, 'makeDeposit'])->name('make.deposit');
Route::post('make-payment', [DashboardController::class, 'makePayment'])->name('make.payment');
Route::get('transfer', [DashboardController::class, 'transferPage'])->middleware('user_auth')->name('transfer.page');
Route::get('user-profile', [DashboardController::class, 'userProfile'])->middleware('user_auth')->name('user.profile');
Route::post('save_nft', [DashboardController::class, 'saveNft'])->name('save.nft');
Route::get('request-card/{user_id}', [DashboardController::class, 'requestCard'])->name('request.card');
Route::get('change-password', [DashboardController::class, 'userChangePassword'])->name('user.change.password');
Route::get('deposit', [DashboardController::class, 'deposit'])->name('deposit');
Route::get('make-deposit', [DashboardController::class, 'makeDeposit'])->name('make.deposit');
Route::get('loan', [DashboardController::class, 'loan'])->name('loan');
Route::post('make-loan', [DashboardController::class, 'makeLoan'])->name('make.loan');
Route::get('notification', [DashboardController::class, 'notification'])->name('notification');
Route::get('transactions', [DashboardController::class, 'transactions'])->name('transactions');
Route::get('pending-transfer', [DashboardController::class, 'pendingTransfer'])->name('pending-transfer');
Route::get('settings', [DashboardController::class, 'settings'])->name('settings');
Route::get('make_withdrawal', [DashboardController::class, 'getWithdrawal'])->name('withdrawal');
Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
Route::get('transaction-history', [DashboardController::class, 'transactionHistory'])->name('transaction.history');
Route::get('view_invoice/{user_id}', [DashboardController::class, 'viewInvoice'])->name('view.invoice');
Route::get('ticket', [DashboardController::class, 'ticket'])->name('ticket');
Route::get('international-transfer', [DashboardController::class, 'internationalTransfer'])->name('international-transfer');
Route::get('domestic-transfer', [DashboardController::class, 'domesticTransfer'])->name('domestic-transfer');
Route::get('skrill', [DashboardController::class, 'skrill'])->middleware('user_auth')->name('skrill');
Route::get('paypal', [DashboardController::class, 'paypal'])->middleware('user_auth')->name('paypal');
Route::get('inter_bank_transfer', [DashboardController::class, 'interBankTransfer'])->middleware('user_auth')->name('inter.bank.transfer');
Route::get('local_bank_transfer', [DashboardController::class, 'localBankTransfer'])->middleware('user_auth')->name('local.bank.transfer');
Route::get('revolut_bank_transfer', [DashboardController::class, 'revolutBankTransfer'])->middleware('user_auth')->name('revolut.bank.transfer');
Route::get('wise_bank_transfer', [DashboardController::class, 'wiseBankTransfer'])->middleware('user_auth')->name('wise.bank.transfer');
Route::get('crypto', [DashboardController::class, 'crypto'])->name('crypto');
Route::post('transfer', [DashboardController::class, 'transferFunds'])->name('transfer-fund');
Route::post('personal-details', [DashboardController::class, 'personalDetails'])->name('personal.details');
Route::post('personal-dp', [DashboardController::class, 'personalDp'])->name('personal.dp');
Route::post('transfer_funds', [DashboardController::class, 'transferFunds'])->name('transfer.funds');
Route::post('paypal-transfer', [DashboardController::class, 'paypalTransfer'])->name('paypal.transfer');
Route::post('skrill-transfer', [DashboardController::class, 'skrillTransfer'])->name('skrill.transfer');
Route::post('crypto-transfer', [DashboardController::class, 'cryptoTransfer'])->name('crypto.transfer');
Route::post('inter-transfer', [DashboardController::class, 'interTransfer'])->name('inter.transfer');
Route::post('local-transfer', [DashboardController::class, 'localTransfer'])->name('local.transfer');
Route::post('revolut-transfer', [DashboardController::class, 'revolutTransfer'])->name('revolut.transfer');
Route::post('wise-transfer', [DashboardController::class, 'wiseTransfer'])->name('wise.transfer');
Route::post('/change-password', [DashboardController::class, 'updatePassword'])->name('update-password');
Route::post('paypal', [DashboardController::class, 'userReflectionPin'])->name('reflection.pin');












// Admin Controller

Route::get('users', [AdminController::class, 'users'])->name('view.users');
Route::get('update_wallet', [AdminController::class, 'eth'])->name('update.wallet');
Route::get('admin_upload_nft', [AdminController::class, 'uploadNft'])->name('admin.upload.nft');
Route::get('uploaded-nfts', [AdminController::class, 'allNfts'])->name('users.uploaded.nft');
Route::post('admin_save_nft', [AdminController::class, 'adminSaveNft'])->name('admin.save.nft');
Route::get('user_transactions', [AdminController::class, 'usersTransaction'])->name('user.transaction');
Route::get('admin_nft_market', [AdminController::class, 'nftMarket'])->name('admin.buy.nft');
Route::post('admin_update_wallet', [AdminController::class, 'updateWallet'])->name('admin.save.wallet');
Route::post('transfer', [AdminController::class, 'transferFunds'])->name('transfer-fund');
Route::post('reflection-pin', [AdminController::class, 'reflectionPin'])->name('reflection');
Route::get('/profile/{id}/', [AdminController::class, 'userProfile']);
Route::get('/delete/{id}', [AdminController::class, 'deleteUser']);
Route::get('admin-change-password', [AdminController::class, 'adminChangePassword'])->name('admin.change.password');
Route::match(['get', 'post'], 'admin-update-password', [AdminController::class, 'adminUpdatePassword'])->name('admin.update.password');
Route::match(['get', 'post'], 'approve-id_card/{id}/', [AdminController::class, 'ApproveId'])->name('approve.id');
Route::match(['get', 'post'], 'credit-user', [AdminController::class, 'creditUser'])->name('credit.user');
Route::match(['get', 'post'], 'debit-user', [AdminController::class, 'debitUser'])->name('debit.user');
Route::match(['get', 'post'], 'verify_user/{id}/', [AdminController::class, 'verifyUser'])->name('verify_user');
Route::match(['get', 'post'], 'update_otp/{id}/', [AdminController::class, 'updateOtp'])->name('update.otp');
Route::match(['get', 'post'], 'update_reflection/{id}/', [AdminController::class, 'updateSsn'])->name('update.reflection');
Route::match(['get', 'post'], 'update_cic/{id}/', [AdminController::class, 'updateCicCode'])->name('update.cic');
Route::match(['get', 'post'], 'update_int/{id}/', [AdminController::class, 'updateIntCode'])->name('update.int');
Route::match(['get', 'post'], 'approve-transaction/{id}/', [AdminController::class, 'approveTransaction'])->name('approve.transaction');
Route::match(['get', 'post'], 'decline-transaction/{id}/', [AdminController::class, 'declineTransaction'])->name('decline.transaction');
Route::post('admin_reset-password/{user}', [AdminController::class, 'resetPassword'])->name('reset.password');
