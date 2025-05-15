<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|receiptPrint.print
*/

use App\Http\Controllers\WebControllers\DynamicController;
use App\Http\Controllers\WebControllers\Reports\GeneralLedgerController;
use App\Http\Controllers\WebControllers\Reports\IssueLoanReportController;
use App\Http\Controllers\WebControllers\Reports\CashBookController;
use App\Http\Controllers\WebControllers\Reports\ReceiptAndDisbursementController;
use App\Http\Controllers\WebControllers\Reports\DayBookController;
use App\Http\Controllers\WebControllers\Transactions\JournalVoucherController;
use App\Http\Controllers\WebControllers\Reports\ShareListController;
use App\Http\Controllers\WebControllers\Transactions\SavingController;
use App\Http\Controllers\WebControllers\Transactions\AccountController;
use App\Http\Controllers\WebControllers\Transactions\ShareController;
use App\Http\Controllers\WebControllers\LoanTransactionController;
use App\Http\Controllers\WebControllers\UserController;
use App\Http\Controllers\WebControllers\HomeController;
use App\Http\Controllers\WebControllers\MasterController;
use App\Http\Controllers\WebControllers\CommonController;
use App\Http\Controllers\WebControllers\Reports\ProfitLossController;
use App\Http\Controllers\WebControllers\Reports\BalanceSheetController;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Master\MasterControllers;
use App\Http\Controllers\Master\SessionController;




Route::any('updateledger', [HomeController::class, 'updateledger']);
Route::get('/', [UserController::class, 'index'])->middleware('guest')->name('/');
Route::get('login', [UserController::class, 'index'])->middleware('guest')->name('login');

Route::group(['prefix' => 'auth'], function () {
    Route::post('check', [UserController::class, 'login'])->name('authCheck');
    Route::any('logout', [UserController::class, 'logout'])->name('logout');
    Route::post('reset', [UserController::class, 'passwordReset'])->name('authReset')->middleware('CheckPasswordAndPin:password');
    Route::any('register', [UserController::class, 'registration'])->name('register');
    Route::post('getotp', [UserController::class, 'getotp'])->name('getotp');
    Route::post('setpin', [UserController::class, 'setpin'])->name('setpin')->middleware('CheckPasswordAndPin:tpin');
    Route::post('gettxnotp', [UserController::class, 'gettxnotp'])->name('gettxnotp');
});

Route::get('sessionchange/{id}', [UserController::class, 'changesession'])->name('sessionchange');

//___________Session Master Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/sessionindex', [SessionController::class, 'sessionindex'])->name('sessionindex');
    Route::post('/sessioninsert', [SessionController::class, 'sessioninsert'])->name('sessioninsert');
    Route::post('/sessionedit', [SessionController::class, 'sessionedit'])->name('sessionedit');
    Route::post('sessionupdate', [SessionController::class, 'sessionupdate'])->name('sessionupdate');
    Route::post('/deletesession', [SessionController::class, 'deletesession'])->name('deletesession');
    Route::post('/changescurrentdate', [SessionController::class, 'changescurrentdate'])->name('changescurrentdate');
});
Route::get('/dashboard', [HomeController::class, 'index'])->name('getdashboard');
//Statemets
Route::group(['prefix' => 'statement', 'middleware' => ['auth']], function () {

    Route::post('fetch/{type}/{id?}/{returntype?}', [CommonController::class, 'fetchData']);
    Route::post('update', [CommonController::class, 'update'])->name('statementUpdate');
    Route::post('status', [CommonController::class, 'status'])->name('statementStatus');
    Route::post('delete', [CommonController::class, 'delete'])->name('statementDelete');
});

Route::group(['prefix' => 'master', 'middleware' => ['auth']], function () {
    Route::get('{type}/{id?}/{status?}', [MasterController::class, 'index'])->name('master');
    Route::post('update', [MasterController::class, 'update'])->name('masterupdate');
    Route::post('/delete/{actiontype}', [MasterController::class, 'delete'])->name('delete');
    Route::post('/modify/{actiontype}', [MasterController::class, 'modify'])->name('modify');
    Route::post('/deleteloanmaster', [MasterController::class, 'deleteloanmaster'])->name('deleteloanmaster');

    Route::post('/getallstaffnumber', [MasterController::class, 'getallstaffnumber'])->name('getallstaffnumber');
    Route::post('/getstaffnumber', [MasterController::class, 'getstaffnumber'])->name('getstaffnumber');
});

//_________Agent Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/agentindex', [MasterController::class, 'agentindex'])->name('agentindex');
    Route::post('/insertagent', [MasterController::class, 'insertagent'])->name('insertagent');
    Route::post('/editagents', [MasterController::class, 'insertagent'])->name('editagents');
    Route::post('/agentupdate', [MasterController::class, 'agentupdate'])->name('agentupdate');
    Route::post('/deleteagent', [MasterController::class, 'deleteagent'])->name('deleteagent');
});

//___________Group Master Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/groupindex', [MasterControllers::class, 'GroupIndex'])->name('groupindex');
    Route::post('/generategroupcode', [MasterControllers::class, 'GenerateGroupCode'])->name('generategroupcode');
    Route::post('/groupInsert', [MasterControllers::class, 'GroupInsert'])->name('groupInsert');
    Route::post('/updategroup', [MasterControllers::class, 'UpdateGroup'])->name('updategroup');
    Route::post('/deletegroup', [MasterControllers::class, 'DeleteGroup'])->name('deletegroup');
});


//___________Ledger Master's Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/ledgerindex', [MasterControllers::class, 'LedgerIndex'])->name('ledgerindex');
    Route::post('/generateledgercode', [MasterControllers::class, 'GenerateLedgerCode'])->name('generateledgercode');
    Route::post('/ledgerInsert', [MasterControllers::class, 'LedgerInsert'])->name('ledgerInsert');
    Route::post('/updateledger', [MasterControllers::class, 'UpdateLedger'])->name('updateledger');
    Route::post('/deleteledger', [MasterControllers::class, 'DeleteLedger'])->name('deleteledger');
});


//___________Saving Routes
Route::group(['middleware' => ['auth']], function () {
    Route::post('/getledgers', [ShareController::class, 'GetLedgders'])->name('getledgers');
    // Route::post('/getsavingaccountsdata', 'getsavingaccountsdata')->name('saving.getsavingaccountsdata');
});

//___________Fd Type Master Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/FdTypeindex', [MasterControllers::class, 'FdTypeIndex'])->name('FdTypeindex');
    Route::post('/FdTypeInsert', [MasterControllers::class, 'FdTypeInsert'])->name('FdTypeInsert');
    Route::post('/updateFdType', [MasterControllers::class, 'UpdateFdType'])->name('updateFdType');
    Route::post('/deleteFdType', [MasterControllers::class, 'DeleteFdType'])->name('deleteFdType');
});




//___________Receipt && Disbursement Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/receiptanddisbursementIndex', [ReceiptAndDisbursementController::class, 'receiptanddisbursementIndex'])->name('receiptanddisbursementIndex');
    Route::post('/getdatareceiptanddisbursement', [ReceiptAndDisbursementController::class, 'getdatareceiptanddisbursement'])->name('getdatareceiptanddisbursement');
    Route::any('/disbursementreceiptPrint', [ReceiptAndDisbursementController::class, 'printRecept'])->name('printRecept');
});



//___________General Ledger Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/generalLegderIndex', [GeneralLedgerController::class, 'generalLegderIndex'])->name('generalLegderIndex');
    Route::post('/getledgercodesss', [GeneralLedgerController::class, 'getledgercodesss'])->name('getledgercodesss');
    Route::post('/getgerenalLedgerdata', [GeneralLedgerController::class, 'getgerenalLedgerdata'])->name('getgerenalLedgerdata');
    Route::get('/generalPrint', [GeneralLedgerController::class, 'print'])->name('generalPrint.print');
});

//___________DayBook Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/daybookindex', [DayBookController::class, 'daybookindex'])->name('daybookindex');
    Route::post('/getdaybookdata', [DayBookController::class, 'getdaybookdata'])->name('getdaybookdata');
});





//__________Share List
Route::prefix('report/shareLisr')->middleware('auth')->controller(ShareListController::class)->group(function () {
    Route::get('/', 'index')->name('shareList.index');
    Route::get('/getData', 'getData')->name('shareList.getData');
    Route::get('/sharePrint/print', 'print')->name('sharePrint.print');
});


Route::group(['prefix' => 'transaction', 'middleware' => ['auth']], function () {
    Route::get('/account', [AccountController::class, 'index'])->name('accountopen.page');
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store')->middleware('checksession');
    Route::post('/account/search', [AccountController::class, 'accountsearch'])->name('account.search');
    Route::post('/account/find', [AccountController::class, 'accountsearchfind'])->name('account.search.find');
    Route::post('/account/data', [AccountController::class, 'update'])->name('accountupdate');
    Route::post('/account/address', [AccountController::class, 'storeaddresspagedata'])->name('account.address.page');
    Route::post('/account/nomenee', [AccountController::class, 'storenomeneepagedata'])->name('account.nomenee.page');
    Route::get('/share', [ShareController::class, 'index'])->name('share');
    Route::post('/share/update', [ShareController::class, 'transaction'])->middleware('checksession')->name('shareupdate');
    Route::post('/deleteaccount', [AccountController::class, 'deleteaccount'])->name('deleteaccount');
});
Route::prefix('report')->middleware('auth')->controller(IssueLoanReportController::class)->group(function () {
    Route::get('/issueLoanReport', 'index')->name('issueLoanReport.index');
    Route::get('/issueLoanReport/getData', 'getData')->name('issueLoanReport.getData');
    Route::get('/issueLoanPrint/print', 'print')->name('issueLoanPrint.print');
});



//___________Cash Book Route's
Route::group(['middleware' => ['auth']], function () {
    Route::get('/cashbookindex', [CashBookController::class, 'cashbookindex'])->name('cashbookindex');
    Route::post('/getcashdata', [CashBookController::class, 'getcashdata'])->name('getcashdata');
});

//______CCL Loan Calculation routes

Route::middleware('auth')->controller(DynamicController::class)->group(function () {
    Route::get('/getLedger', 'getLedger')->name('getLedger');
});
//______
Route::group(['middleware' => ['auth']], function () {
    Route::get('/profitlossindex', [ProfitLossController::class, 'profitlossindex'])->name('profitlossindex');
    Route::post('/getprofitlossdetails', [ProfitLossController::class, 'getprofitlossdetails'])->name('getprofitlossdetails');
    Route::post('/updateExpenseIncomeProfitLosses', [ProfitLossController::class, 'updateExpenseIncomeProfitLosses'])->name('updateExpenseIncomeProfitLosses');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/balancesheetindex', [BalanceSheetController::class, 'balancesheetindex'])->name('balancesheetindex');
    Route::post('/getbalancesheetdate', [BalanceSheetController::class, 'getbalancesheetdate'])->name('getbalancesheetdate');
});
Route::any('report/daybook', [DayBookController::class, 'printPdf'])->name('printPdf');

Route::prefix('transactions/journalVoucher')->middleware('auth')->group(function () {
    Route::get('/', [JournalVoucherController::class, 'index'])->name('journalVoucher.index');
    // Route::post('/store', [JournalVoucherController::class, 'store'])->name('journalVoucher.store')->middleware('checksession');
    Route::post('/getledgercodes', [JournalVoucherController::class, 'getledgercodes'])->name('getledgercodes');
    Route::post('/getled', [JournalVoucherController::class, 'getled'])->name('getled');
    Route::post('/getdatadat', [JournalVoucherController::class, 'getdatadat'])->name('getdatadat');
    Route::post('/submitvoucher', [JournalVoucherController::class, 'submitvoucher'])->name('submitvoucher');
    Route::post('/deletevouchares', [JournalVoucherController::class, 'deletevouchares'])->name('deletevouchares');
    Route::post('/editvouchars', [JournalVoucherController::class, 'editvouchars'])->name('editvouchars');
    Route::post('/updatevouchar', [JournalVoucherController::class, 'updatevouchar'])->name('updatevouchar');
});
Route::group(['middleware' => ['auth']], function () {
    Route::get('/loan', [LoanTransactionController::class, 'index'])->name('loan');
    Route::get('{type}/', [LoanTransactionController::class, 'loan'])->name('loantype');
    Route::post('checkLoanNo', [LoanTransactionController::class, 'checkLoanNo'])->name('checkLoanNo');
    Route::post('checkPernoteNo', [LoanTransactionController::class, 'checkPernoteNo'])->name('checkPernoteNo');
    Route::post('getloanDetail', [LoanTransactionController::class, 'getloanDetail'])->name('getloanDetail');
    Route::post('updateloanadvancement', [LoanTransactionController::class, 'updateloanadvancement'])->name('updateloanadvancement');
    Route::post('insertloanadvancement', [LoanTransactionController::class, 'insertloanadvancement'])->name('insertloanadvancement');
    Route::post('getLoanType', [LoanTransactionController::class, 'getLoanType'])->name('getLoanType');
    Route::post('grantordetails', [LoanTransactionController::class, 'grantordetails'])->name('grantordetails');
    Route::post('deleteloan', [LoanTransactionController::class, 'deleteloan'])->name('deleteloan');
    Route::post('loandata', [LoanTransactionController::class, 'loandata'])->name('loandata');
    Route::post('getInstallmets', [LoanTransactionController::class, 'getInstallmets'])->name('getInstallmets');
    Route::post('getLoanAc', [LoanTransactionController::class, 'getLoanAc'])->name('getLoanAc');
    Route::post('getloandetails', [LoanTransactionController::class, 'getloandetails'])->name('getloandetails');
    Route::post('saverecovery', [LoanTransactionController::class, 'saverecovery'])->name('saverecovery');
    Route::post('deleteRecovery', [LoanTransactionController::class, 'deleteRecovery'])->name('deleteRecovery');
    Route::post('editRecovery', [LoanTransactionController::class, 'editRecovery'])->name('editRecovery');
    Route::post('updaterecovery', [LoanTransactionController::class, 'updaterecovery'])->name('updaterecovery');
});
