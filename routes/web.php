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
use App\Http\Controllers\WebControllers\Reports\FDReportController;
use App\Http\Controllers\WebControllers\Reports\GeneralLedgerController;
use App\Http\Controllers\WebControllers\Reports\IssueLoanReportController;
use App\Http\Controllers\WebControllers\Reports\MISReportController;
use App\Http\Controllers\WebControllers\Reports\CashBookController;
use App\Http\Controllers\WebControllers\Reports\FinancialYearEndcontroller;
use App\Http\Controllers\WebControllers\Reports\SecurityDepositListController;
use App\Http\Controllers\WebControllers\Reports\RDReportController;
use App\Http\Controllers\WebControllers\Reports\ReceiptAndDisbursementController;
use App\Http\Controllers\WebControllers\Reports\DayBookController;
use App\Http\Controllers\WebControllers\Reports\BalanceBookController;
use App\Http\Controllers\WebControllers\Transactions\FD\FDController;
use App\Http\Controllers\WebControllers\Transactions\FD\FDControllerScheme;
use App\Http\Controllers\WebControllers\Transactions\JournalVoucherController;
use App\Http\Controllers\WebControllers\Reports\SavingListController;
use App\Http\Controllers\WebControllers\Reports\ShareListController;
use App\Http\Controllers\WebControllers\Reports\BankFdReportController;
use App\Http\Controllers\WebControllers\Reports\MisListConrtoller;
use App\Http\Controllers\WebControllers\Transactions\SavingController;
use App\Http\Controllers\WebControllers\Transactions\CdsControllers;
use App\Http\Controllers\WebControllers\Transactions\SearchVoucherController;
use App\Http\Controllers\WebControllers\Transactions\Trading\PurchaseController;
use App\Http\Controllers\WebControllers\Transactions\Trading\SaleController;
use App\Http\Controllers\WebControllers\Transactions\TransferController;
use App\Http\Controllers\WebControllers\Transactions\AccountController;
use App\Http\Controllers\WebControllers\Transactions\ShareController;
use App\Http\Controllers\WebControllers\Transactions\RDController;
use App\Http\Controllers\WebControllers\Transactions\DailyLoanController;
use App\Http\Controllers\WebControllers\Transactions\MISConrtoller;
use App\Http\Controllers\WebControllers\Transactions\CashCreditLimitController;
use App\Http\Controllers\WebControllers\UserController;
use App\Http\Controllers\WebControllers\HomeController;
use App\Http\Controllers\WebControllers\StatementController;
use App\Http\Controllers\WebControllers\MasterController;
use App\Http\Controllers\WebControllers\CommonController;
use App\Http\Controllers\WebControllers\ProductsController;
use App\Http\Controllers\WebControllers\Transactions\LoanController;
use App\Http\Controllers\WebControllers\Transactions\DailyCollectionController;
use App\Http\Controllers\WebControllers\Transactions\DailyCollectionLoanController;
use App\Http\Controllers\WebControllers\Reports\ProfitLossController;
use App\Http\Controllers\WebControllers\Reports\DailyCollectionReport;
use App\Http\Controllers\WebControllers\Reports\BalanceSheetController;
use App\Http\Controllers\WebControllers\Reports\PersonalLedgerController;
use App\Http\Controllers\WebControllers\CalculationsController;
use App\Http\Controllers\WebControllers\AllListController;
use App\Http\Controllers\WebControllers\Reports\CdsController;
use App\Http\Controllers\WebControllers\Reports\ReportsNPAListController;
use App\Http\Controllers\WebControllers\Reports\BankFdController;
use App\Http\Controllers\WebControllers\Reports\CCLReportController;
use App\Http\Controllers\DividendController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MemberAccountController;
use App\Http\Controllers\ItemMasterController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\transaction\AccountOpeningControllers;
use App\Http\Controllers\transaction\AgentCommissionController;
use App\Http\Controllers\WebControllers\DepositSchemeMaster;
use App\Http\Controllers\transaction\TdsCommissionController;
use  App\Http\Controllers\Master\MasterControllers;
use  App\Http\Controllers\Master\RolesPermission;
use App\Http\Controllers\Master\SessionController;
use App\Http\Controllers\Master\SODMasterController;
use App\Http\Controllers\WebControllers\Reports\SavingInterestCalculationController;




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
    // Route::get("export/{type}", [StatementController::class, 'export'])->name('export');
    // Route::get('{type}/{id?}/{status?}', [StatementController::class, 'index'])->name('statement');
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

//___________Users Master Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/usersindex', [RolesPermission::class, 'usersindex'])->name('usersindex');
//     Route::post('/userinsert', [RolesPermission::class, 'userinsert'])->name('userinsert');
//     Route::get('/roleedit/{id}', [RolesPermission::class, 'roleedit'])->name('roleedit');
//     Route::put('/roleupdate/{id}', [RolesPermission::class, 'roleupdate'])->name('roleupdate');


//     Route::get('/usersss', [RolesPermission::class, 'users'])->name('usersss');
//     Route::post('/userregister', [RolesPermission::class, 'userregister'])->name('userregister');
//     Route::get('/useredits/{id}', [RolesPermission::class, 'useredits'])->name('useredits');
//     Route::put('/usersupdate/{id}', [RolesPermission::class, 'usersupdate'])->name('usersupdate');
//     Route::post('/getallagents', [RolesPermission::class, 'getallagents'])->name('getallagents');

// });

//__________SOD Master Controller
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/sodmasterindex',[SODMasterController::class,'sodmasterindex'])->name('sodmasterindex');
//     Route::post('/sodmasterinsert',[SODMasterController::class,'sodmasterinsert'])->name('sodmasterinsert');
//     Route::post('/sodmasteredit',[SODMasterController::class,'sodmasteredit'])->name('sodmasteredit');
//     Route::post('/sodmasterupdate',[SODMasterController::class,'sodmasterinsert'])->name('sodmasterupdate');
//     Route::post('/deletesodmaster',[SODMasterController::class,'deletesodmaster'])->name('deletesodmaster');
// });

//_________Agent Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/agentindex', [MasterController::class, 'agentindex'])->name('agentindex');
    Route::post('/insertagent', [MasterController::class, 'insertagent'])->name('insertagent');
    Route::post('/editagents', [MasterController::class, 'insertagent'])->name('editagents');
    Route::post('/agentupdate', [MasterController::class, 'agentupdate'])->name('agentupdate');
    Route::post('/deleteagent', [MasterController::class, 'deleteagent'])->name('deleteagent');
});


//_________Bank FD Master Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/bankfdmasterindex', [MasterController::class, 'bankfdmasterindex'])->name('bankfdmasterindex');
//     Route::post('/insertfdmaster', [MasterController::class, 'insertfdmaster'])->name('insertfdmaster');
//     Route::post('/editbankfdmasterid',[MasterController::class, 'editbankfdmasterid'])->name('editbankfdmasterid');
//     Route::post('/updatefdmaster', [MasterController::class, 'updatefdmaster'])->name('updatefdmaster');
//     Route::post('/deletebankfdmaster', [MasterController::class, 'deletebankfdmaster'])->name('deletebankfdmaster');
// });




//__________Item Deperciation Master Controller
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/itemindex',[ItemMasterController::class, 'itemindex'])->name('itemindex');
// });


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
    Route::get('/savingaccountindex', [SavingController::class, 'SavingAccountIndex'])->name('savingaccountindex');
    Route::post('/trfsavingtoloan', [SavingController::class, 'trfsavingtoloan'])->name('trfsavingtoloan');
    Route::get('/dlttrfsavingtoloan', [SavingController::class, 'dlttrfsavingtoloan'])->name('dlttrfsavingtoloan');
    Route::post('/getloanpending', [SavingController::class, 'getloanpending'])->name('getloanpending');
    Route::post('/getledgers', [SavingController::class, 'GetLedgders'])->name('getledgers');
    Route::post('/getsavingacclist', [SavingController::class, 'GetSavingAccountList'])->name('getsavingacclist');
    Route::post('/getsavingdetails', [SavingController::class, 'GetSavingDetails'])->name('getsavingdetails');
    Route::post('/savingentryinsert', [SavingController::class, 'SavingEntryInsert'])->name('savingentryinsert');
    Route::post('/deletesavingentry', [SavingController::class, 'DeleteSavingEntry'])->name('deletesavingentry');
    Route::post('/savingentryupdate', [SavingController::class, 'SavingEntryUpdate'])->name('savingentryupdate');
    Route::post('/getrdaccountdetails', [SavingController::class, 'GetRdAccount'])->name('getrdaccountdetails');
    Route::post('/getsavingeditdetails', [SavingController::class, 'GetSavingEitDetails'])->name('getsavingeditdetails');
    Route::post('/getdailysavingaccount', [SavingController::class, 'getdailysavingaccount'])->name('getdailysavingaccount');
    Route::post('/savingtrfddailyaccount', [SavingController::class, 'savingtrfddailyaccount'])->name('savingtrfddailyaccount');
    Route::post('/savingtrfddailyupdate', [SavingController::class, 'savingtrfddailyupdate'])->name('savingtrfddailyupdate');
    Route::post('/getfddetails', [SavingController::class, 'getfddetails'])->name('getfddetails');

    Route::post('/editpaidinterest', [SavingController::class, 'editpaidinterest'])->name('editpaidinterest');
    Route::post('/paidinterestchange', [SavingController::class, 'paidinterestchange'])->name('paidinterestchange');

    Route::post('/fdtrfddailyaccount', [SavingController::class, 'fdtrfddailyaccount'])->name('fdtrfddailyaccount');
    Route::post('/fdtrfddailyupdate', [SavingController::class, 'fdtrfddailyupdate'])->name('fdtrfddailyupdate');

    //___________Saving Trfd To CCL
    Route::post('/getcclaccountdetails',[SavingController::class, 'getcclaccountdetails'])->name('getcclaccountdetails');
    Route::post('/getcheckinterestdatewiseccl',[SavingController::class, 'getcheckinterestdatewiseccl'])->name('getcheckinterestdatewiseccl');
    Route::post('/savingtrfdtocclrecovery',[SavingController::class, 'savingtrfdtocclrecovery'])->name('savingtrfdtocclrecovery');
    Route::post('/editsavingtrdfccl',[SavingController::class, 'editsavingtrdfccl'])->name('editsavingtrdfccl');
    Route::post('/savingtrfdtocclrecoveryupdate',[SavingController::class, 'savingtrfdtocclrecoveryupdate'])->name('savingtrfdtocclrecoveryupdate');



    // Route::post('/getsavingaccountsdata', 'getsavingaccountsdata')->name('saving.getsavingaccountsdata');
});

//___________Fd Type Master Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/FdTypeindex', [MasterControllers::class, 'FdTypeIndex'])->name('FdTypeindex');
    Route::post('/FdTypeInsert', [MasterControllers::class, 'FdTypeInsert'])->name('FdTypeInsert');
    Route::post('/updateFdType', [MasterControllers::class, 'UpdateFdType'])->name('updateFdType');
    Route::post('/deleteFdType', [MasterControllers::class, 'DeleteFdType'])->name('deleteFdType');
});



//______________Deposit Master's
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/deposit-secheme-index', [DepositSchemeMaster::class, 'DepositSechemesIndex'])->name('deposit-secheme-index');
//     Route::post('/generateschemecode', [DepositSchemeMaster::class, 'GenerateSchemeCode'])->name('generateschemecode');
//     Route::post('/deposit-secheme-insert', [DepositSchemeMaster::class, 'DepositSechemeInsert'])->name('deposit-secheme-insert');
//     Route::post('/deposit-delete-sechemes', [DepositSchemeMaster::class, 'DeleteDepositSecheme'])->name('deposit-delete-sechemes');
//     Route::post('/update-sechemes-enddate', [DepositSchemeMaster::class, 'UpdateDepositSecheme'])->name('update-sechemes-enddate');
//     Route::get('/scheme-details/{id}', [DepositSchemeMaster::class, 'getSchemeDetails'])->name('scheme.details');
// });


//______________Account Open Master's
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/account-opening-index', [AccountOpeningControllers::class, 'AccountOpenIndex'])->name('account-opening-index');
//     Route::post('/addaccount', [AccountOpeningControllers::class, 'addaccount'])->name('addaccount');
//     Route::get('/getschemes', [AccountOpeningControllers::class, 'getschemes'])->name('getschemes');
//     Route::get('/getschemeall', [AccountOpeningControllers::class, 'getschemeall'])->name('getschemeall');
//     Route::get('/getschemesamount', [AccountOpeningControllers::class, 'getschemesamount'])->name('getschemesamount');
//     Route::get('/fetdatamm', [AccountOpeningControllers::class, 'fetdatamm'])->name('fetdatamm');
//     Route::post('/deletefetdatamm', [AccountOpeningControllers::class, 'deletefetdatamm'])->name('deletefetdatamm');
//     Route::get('/getData', [AccountOpeningControllers::class, 'getData'])->name('saving.getData');
// });


//______________Tds Route's
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/tds-index', [TdsCommissionController::class, 'TdsIndex'])->name('tds-index');
//     Route::post('/tds-insert', [TdsCommissionController::class, 'TdsInsert'])->name('tds-insert');
//     Route::post('/tds-status-edit', [TdsCommissionController::class, 'TdsStatusEdit'])->name('tds-status-edit');
//     Route::post('/tds-status-update', [TdsCommissionController::class, 'TdsStatusUpdate'])->name('tds-status-update');
// });




//__________Security On commission && Agent Commission
// Route::group(['middleware' => ['auth']], function () {

//     Route::get('/securityoncommissionIndex', [AgentCommissionController::class, 'securityoncommissionIndex'])->name('securityoncommissionIndex');
//     Route::post('/getcash', [AgentCommissionController::class, 'getcashbanksaving'])->name('getcash');
//     Route::post('/getcashbank', [AgentCommissionController::class, 'getcashbanksaving'])->name('getcashbank');
//     Route::post('/getsavingaccount', [AgentCommissionController::class, 'getcashbanksaving'])->name('getsavingaccount');
//     Route::post('/getagentaccountlist', [AgentCommissionController::class, 'getagentaccountlist'])->name('getagentaccountlist');
//     Route::post('/getsecurityaccountdetail', [AgentCommissionController::class, 'getsecurityaccountdetail'])->name('getsecurityaccountdetail');

//     Route::post('/insertsecuirtyaccount', [AgentCommissionController::class, 'insertsecuirtyaccount'])->name('insertsecuirtyaccount');
//     Route::post('/updatesecuirtyaccount', [AgentCommissionController::class, 'insertsecuirtyaccount'])->name('updatesecuirtyaccount');
//     Route::post('/deletesecurityaccount', [AgentCommissionController::class, 'deletesecurityaccount'])->name('deletesecurityaccount');
//     Route::post('/editsecurityacc', [AgentCommissionController::class, 'editsecurityacc'])->name('editsecurityacc');


//     Route::post('/editsecurityinterest', [AgentCommissionController::class, 'editsecurityinterest'])->name('editsecurityinterest');
//     Route::post('/securityinterestupdate', [AgentCommissionController::class, 'securityinterestupdate'])->name('securityinterestupdate');





//     //______________Agent Commission Route's
//     Route::get('/agent-commission-index', [AgentCommissionController::class, 'AgentCommissionIndex'])->name('agent-commission-index');
//     Route::post('/get-agent-commission', [AgentCommissionController::class, 'GetAgentCommissions'])->name('get-agent-commission');
//     Route::post('/paid-agent-commission', [AgentCommissionController::class, 'PaidAgentCommission'])->name('paid-agent-commission');
//     Route::post('/deletepaidcommission', [AgentCommissionController::class, 'deletepaidcommission'])->name('deletepaidcommission');
// });


//___________Receipt && Disbursement Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/receiptanddisbursementIndex',[ReceiptAndDisbursementController::class,'receiptanddisbursementIndex'])->name('receiptanddisbursementIndex');
    Route::post('/getdatareceiptanddisbursement',[ReceiptAndDisbursementController::class,'getdatareceiptanddisbursement'])->name('getdatareceiptanddisbursement');
    Route::any('/disbursementreceiptPrint',[ReceiptAndDisbursementController::class, 'printRecept'])->name('printRecept');
});



//___________General Ledger Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/generalLegderIndex',[GeneralLedgerController::class,'generalLegderIndex'])->name('generalLegderIndex');
//     Route::post('/getledgercodesss',[GeneralLedgerController::class,'getledgercodesss'])->name('getledgercodesss');
//     Route::post('/getgerenalLedgerdata',[GeneralLedgerController::class,'getgerenalLedgerdata'])->name('getgerenalLedgerdata');
//     Route::get('/generalPrint',[GeneralLedgerController::class,'print'])->name('generalPrint.print');
// });

//___________DayBook Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/daybookindex', [DayBookController::class, 'daybookindex'])->name('daybookindex');
    Route::post('/getdaybookdata',[DayBookController::class, 'getdaybookdata'])->name('getdaybookdata');
});


//_____________saving List Routes
Route::prefix('reports/savingList')->middleware('auth')->controller(SavingListController::class)->group(function () {
    Route::get('/', 'index')->name('savingList.index');
    Route::post('/getschemessavinglist', 'getschemessavinglist')->name('getschemessavinglist');
    Route::get('/getData', 'getData')->name('savingList.getData');
    Route::get('/savingPrint/print', 'print')->name('savingPrint.print');
});


//__________Share List
Route::prefix('report/shareLisr')->middleware('auth')->controller(ShareListController::class)->group(function () {
    Route::get('/', 'index')->name('shareList.index');
    Route::get('/getData', 'getData')->name('shareList.getData');
    Route::get('/sharePrint/print', 'print')->name('sharePrint.print');
});


//____________Bank FD Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/bankfdindex', [BankFdController::class, 'bankfdindex'])->name('bankfdindex');
//     Route::post('/getbankfdledgeres', [BankFdController::class, 'getbankfdledgeres'])->name('getbankfdledgeres');
//     Route::post('/bankfdinsert', [BankFdController::class, 'bankfdinsert'])->name('bankfdinsert');
//     Route::post('/bankfdedit', [BankFdController::class, 'bankfdedit'])->name('bankfdedit');
//     Route::post('/bankferupdate', [BankFdController::class, 'bankferupdate'])->name('bankferupdate');
//     Route::post('/deletebankfds',[BankFdController::class, 'deletebankfds'])->name('deletebankfds');
//     Route::post('/bankfdmature',[BankFdController::class, 'bankfdmature'])->name('bankfdmature');
//     Route::post('/getdatabankfdrenew',[BankFdController::class, 'getdatabankfdrenew'])->name('getdatabankfdrenew');
//     Route::post('/bankfdrenew',[BankFdController::class, 'bankfdrenew'])->name('bankfdrenew');
//     Route::post('/bankfdrenewupdate',[BankFdController::class, 'bankfdrenewupdate'])->name('bankfdrenewupdate');
//     // Route::post('/getdatabankfdunmature',[BankFdController::class, 'getdatabankfdunmature'])->name('getdatabankfdunmature');
// });


//_____________FD List Routes
// Route::prefix('report/fdReport')->middleware('auth')->controller(FDReportController::class)->group(function () {
//     Route::post('/getfdallschemes','getfdallschemes')->name('getfdallschemes');
//     Route::get('/', 'index')->name('fdReport.index');
//     Route::get('/fdReport/getData', 'getData')->name('fdReport.getData');
//     Route::get('/fdPrint/print/{id}', 'printFd')->name('fdPrint.print');
// });



//_______________Rd List Routes
// Route::prefix('report/rdReport')->middleware('auth')->controller(RDReportController::class)->group(function () {
//     Route::get('/', 'index')->name('rdReport.index');
//     Route::post('/getrdschemes', 'getrdschemes')->name('getrdschemes');
//     Route::post('/getrdData', 'getrdData')->name('getrdData');
//     Route::get('/rdReport/getData', 'getData')->name('rdReport.getData');
//     Route::get('/rdPrint/print', 'print')->name('rdPrint.print');
// });


//____________Issue Loan List Routes
// Route::prefix('report')->middleware('auth')->controller(IssueLoanReportController::class)->group(function () {
//     Route::get('/issueLoanReport', 'index')->name('issueLoanReport.index');
//     Route::get('/issueLoanReport/getData', 'getData')->name('issueLoanReport.getData');
//     Route::get('/issueLoanPrint/print', 'print')->name('issueLoanPrint.print');
// });



//___________Cash Book Route's
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/cashbookindex', [CashBookController::class, 'cashbookindex'])->name('cashbookindex');
//     Route::post('/getcashdata', [CashBookController::class, 'getcashdata'])->name('getcashdata');
// });


//___________Recurring Deposit
// Route::prefix('transactions/RD_recurring_new')->middleware('auth')->controller(RDController::class)->group(function () {
//     Route::get('/', 'index')->name('rd.recurring.index');
//     Route::post('/getrdacclist', 'GetRDAccountList')->name('getrdacclist');
//     Route::post('/getrddetails', 'GetRDDetails')->name('getrddetails');
//     Route::post('/rdinsert', 'RdInsert')->name('rdinsert');
//     Route::post('/getcashbankledgers', 'Getcashbankledgers')->name('getcashbankledgers');
//     Route::post('/rdamountreceive', 'RdReceiveAmount')->name('rdamountreceive');
//     Route::post('/getinstallmentsdetails', 'GetInstallmentsDetails')->name('getinstallmentsdetails');
//     Route::post('/viewinstallmentsdetails', 'ViewInstallmentsDetails')->name('viewinstallmentsdetails');
//     Route::post('/deleteinstallments', 'DeleteInstallments')->name('deleteinstallments');
//     Route::post('/rdamountupdatereceive', 'RdAmountUpdatereceive')->name('rdamountupdatereceive');
//     Route::post('/getrdmaturedata', 'GetRDMatureData')->name('getrdmaturedata');
//     Route::post('/getsavingaccountno', 'GetSavingAccountno')->name('getsavingaccountno');
//     Route::post('/rdmature', 'RdMature')->name('rdmature');
//     Route::post('/rdunmature', 'rdunmature')->name('rdunmature');
//     Route::post('/deleterd', 'DeleteRd')->name('deleterd');
//     Route::post('/rdmodify', 'RdModify')->name('rdmodify');
//     Route::post('/rdupdate', 'RdUpdate')->name('rdupdate');
// });




// Route::prefix('transactions/fd')->middleware('auth')->controller(FDController::class)->group(function () {
//     // Route::any('/bank/index', 'bankindex')->name('fd.bank.index');
//     // Route::any('/bank/index/{id}', 'editbankfd')->name('fd.bank.edit');
//     // Route::any('bank/delete/{id}', 'deletebankfd')->name('fd.bank.delete');

//     Route::get('/', 'index')->name('fd.index');
//     Route::post('/store', 'store')->name('fd.store')->middleware('checksession');
//     Route::get('/view/{viewId}', 'view')->name('fd.view');
//     Route::put('/update', 'update')->name('fd.update')->middleware('checksession');
//     Route::delete('/delete', 'destroy')->name('fd.delete')->middleware('checksession');
//     Route::get('/pagination', 'pagination')->name('fd.pagination');
//     Route::get('/getData', 'getData')->name('fd.getData');
//     Route::get('/fetchData', 'fetchData')->name('fd.fetchData');

//     Route::put('/mature', 'mature')->name('fd.mature')->middleware('checksession');
//     Route::put('/renew', 'renew')->name('fd.renew')->middleware('checksession');
//     Route::put('/unmature', 'unmature')->name('fd.unmature')->middleware('checksession');
//     Route::put('/delete', 'destroy')->name('fd.delete')->middleware('checksession');
//     Route::put('/print', 'print')->name('fd.print');
// });


// Route::prefix('transactions/fdscheme')->middleware('auth')->controller(FDControllerScheme::class)->group(function () {
//     Route::any('/bank/index', 'bankindex')->name('fdscheme.bank.index');
//     Route::any('/bank/index/{id}', 'editbankfd')->name('fdscheme.bank.edit');
//     Route::any('bank/delete/{id}', 'deletebankfd')->name('fdscheme.bank.delete');

//     Route::get('/', 'index')->name('fdscheme.index');
//     Route::post('/store', 'store')->name('fdscheme.store')->middleware('checksession');
//     Route::get('/view/{viewId}', 'view')->name('fdscheme.view');
//     Route::put('/update', 'update')->name('fdscheme.update')->middleware('checksession');
//     Route::delete('/delete', 'destroy')->name('fdscheme.delete')->middleware('checksession');
//     Route::get('/pagination', 'pagination')->name('fdscheme.pagination');
//     Route::get('/getData', 'getData')->name('fdscheme.getData');
//     Route::get('/fetchData', 'fetchData')->name('fdscheme.fetchData');

//     Route::put('/mature', 'mature')->name('fdscheme.mature')->middleware('checksession');


//     Route::post('/getfdschemes', 'getfdschemes')->name('getfdschemes');
//     // Route::post('/checkDateSession', 'checkDateSession')->name('fdscheme.checkDateSession')->middleware('checksession');
//     Route::get('/checkmatured', 'checkmatured')->name('checkmatured');
//     Route::put('/renew', 'renew')->name('fdscheme.renew')->middleware('checksession');
//     Route::put('/unmature', 'unmature')->name('fdscheme.unmature')->middleware('checksession');
//     Route::put('/delete', 'destroy')->name('fdscheme.delete')->middleware('checksession');
//     Route::put('/print', 'print')->name('fdscheme.print');
// });


// Route::prefix('transactions/journalVoucher')->middleware('auth')->group(function () {
//     Route::get('/', [JournalVoucherController::class, 'index'])->name('journalVoucher.index');
//     // Route::post('/store', [JournalVoucherController::class, 'store'])->name('journalVoucher.store')->middleware('checksession');
//     Route::post('/getledgercodes', [JournalVoucherController::class, 'getledgercodes'])->name('getledgercodes');
//     Route::post('/getled', [JournalVoucherController::class, 'getled'])->name('getled');
//     Route::post('/getdatadat', [JournalVoucherController::class, 'getdatadat'])->name('getdatadat');
//     Route::post('/submitvoucher', [JournalVoucherController::class, 'submitvoucher'])->name('submitvoucher');
//     Route::post('/deletevouchares',[JournalVoucherController::class, 'deletevouchares'])->name('deletevouchares');
//     Route::post('/editvouchars',[JournalVoucherController::class, 'editvouchars'])->name('editvouchars');
//     Route::post('/updatevouchar',[JournalVoucherController::class, 'updatevouchar'])->name('updatevouchar');
// });



Route::group(['prefix' => 'transaction/loan', 'middleware' => ['auth']], function () {


    Route::get('/', [LoanController::class, 'index'])->name('loan');
    Route::get('{type}/', [LoanController::class, 'loan'])->name('loantype');
    Route::post('/getdailyloanaccount', [LoanController::class, 'getdailyloanaccount'])->name('getdailyloanaccount')->middleware('checksession');
    Route::post('/getdailyloanperday', [LoanController::class, 'getdailyloanperday'])->name('getdailyloanperday')->middleware('checksession');
    Route::post('/getdailyloanaccountreceived', [LoanController::class, 'getdailyloanaccountreceived'])->name('getdailyloanaccountreceived')->middleware('checksession');
    Route::post('/transferloanaccountreceived', [LoanController::class, 'transferloanaccountreceived'])->name('transferloanaccountreceived')->middleware('checksession');
    Route::post('/getdailytransfer', [LoanController::class, 'getdailytransfer'])->name('getdailytransfer')->middleware('checksession');
    Route::post('/deleteItemtransfered', [LoanController::class, 'deleteItemtransfered'])->name('deleteItemtransfered')->middleware('checksession');
    Route::post('/update', [LoanController::class, 'transaction'])->name('loanupdate')->middleware('checksession');
    Route::post('getfdschemesloan', [LoanController::class, 'getfdschemesloan'])->name('getfdschemesloan');
    Route::post('getrdschemesloan', [LoanController::class, 'getrdschemesloan'])->name('getrdschemesloan');
    Route::post('getCheckedSchemes', [LoanController::class, 'getCheckedSchemes'])->name('getCheckedSchemes');
    Route::post('/get-saving-account', [LoanController::class, 'getsavingaccount'])->name('get-saving-account');


});







//__________Daily Collection Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/dailysavingcollectionindex', [DailyCollectionController::class, 'dailysavingcollectionindex'])->name('dailysavingcollectionindex');
//     Route::post('/getddsaccountslist', [DailyCollectionController::class, 'getddsaccountslist'])->name('getddsaccountslist');
//     Route::post('/getddsaccount', [DailyCollectionController::class, 'getddsaccount'])->name('getddsaccount');
//     Route::post('/insertdailysavingaccount', [DailyCollectionController::class, 'insertdailysavingaccount'])->name('insertdailysavingaccount');
//     Route::post('/deleteddssaving', [DailyCollectionController::class, 'deleteddssaving'])->name('deleteddssaving');
//     Route::post('/editddssaving', [DailyCollectionController::class, 'editddssaving'])->name('editddssaving');
//     Route::post('/updatedailysavingaccount', [DailyCollectionController::class, 'updatedailysavingaccount'])->name('updatedailysavingaccount');
//     Route::post('/getddreceivedsaccountslist', [DailyCollectionController::class, 'getddreceivedsaccountslist'])->name('getddreceivedsaccountslist');
//     Route::post('/getreceievedddsaccount', [DailyCollectionController::class, 'getreceievedddsaccount'])->name('getreceievedddsaccount');
//     Route::post('/ddsreceivedledger', [DailyCollectionController::class, 'ddsreceivedledger'])->name('ddsreceivedledger');
//     Route::post('/dailysavingreceived', [DailyCollectionController::class, 'dailysavingreceived'])->name('dailysavingreceived');
//     Route::post('/viewdepositeamount', [DailyCollectionController::class, 'viewdepositeamount'])->name('viewdepositeamount');
//     Route::post('/viewdailyinstallments', [DailyCollectionController::class, 'viewdailyinstallments'])->name('viewdailyinstallments');
//     Route::post('/dailyinstallmentsdelete', [DailyCollectionController::class, 'dailyinstallmentsdelete'])->name('dailyinstallmentsdelete');
//     Route::post('/dailyinstallmentsmodify', [DailyCollectionController::class, 'dailyinstallmentsmodify'])->name('dailyinstallmentsmodify');
//     Route::post('/dailysavingreceivedupdate', [DailyCollectionController::class, 'dailysavingreceivedupdate'])->name('dailysavingreceivedupdate');
//     Route::post('/getdetaildailyaccountmature', [DailyCollectionController::class, 'getdetaildailyaccountmature'])->name('getdetaildailyaccountmature');
//     Route::post('/dailyaccountmature', [DailyCollectionController::class, 'dailyaccountmature'])->name('dailyaccountmature');
//     Route::post('/dailyunmature', [DailyCollectionController::class, 'dailyunmature'])->name('dailyunmature');


//     Route::post('/getddsaccountsssss', [DailyCollectionController::class, 'getddsaccountsssss'])->name('getddsaccountsssss');
// });


//__________Security Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/securitylistIndex', [SecurityDepositListController::class, 'securitylistIndex'])->name('securitylistIndex');
//     Route::post('/getsecurityoncomminterestcaluclation', [SecurityDepositListController::class, 'getsecurityoncomminterestcaluclation'])->name('getsecurityoncomminterestcaluclation');
//     Route::post('/paidsecurityoncomminterest', [SecurityDepositListController::class, 'paidsecurityoncomminterest'])->name('paidsecurityoncomminterest');
//     Route::post('/deletepaidsecuritycomminterest', [SecurityDepositListController::class, 'deletepaidsecuritycomminterest'])->name('deletepaidsecuritycomminterest');

//     Route::get('securitydepositlist', [SecurityDepositListController::class, 'securitydepositlist'])->name('securitydepositlist');
//     Route::post('/getsecuritylist', [SecurityDepositListController::class, 'getsecuritylist'])->name('getsecuritylist');
// });


//_________Financial Year End Route's
// Route::group(['middleware' => ['auth']], function () {
//     // Route::post('/rdpayableinsert', [FinancialYearEndcontroller::class, 'rdpayableinsert'])->name('rdpayableinsert');
//     // Route::post('/fdpayableinsert', [FinancialYearEndcontroller::class, 'fdpayableinsert'])->name('fdpayableinsert');
//     // Route::post('/dailypayableinsert', [FinancialYearEndcontroller::class, 'dailypayableinsert'])->name('dailypayableinsert');
//     // Route::post('/loaninterestrecoverable', [FinancialYearEndcontroller::class, 'loaninterestrecoverable'])->name('loaninterestrecoverable');
// });


//______Saving Interest Calculation routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/interestcalculationindex', [SavingInterestCalculationController::class, 'interestcalculationindex'])->name('interestcalculationindex');
//     Route::post('/getsavinginterestcaluclation', [SavingInterestCalculationController::class, 'getsavinginterestcaluclation'])->name('getsavinginterestcaluclation');
//     Route::post('/paidsavinginterest', [SavingInterestCalculationController::class, 'paidsavinginterest'])->name('paidsavinginterest');
//     Route::post('/deletepaidinterest', [SavingInterestCalculationController::class, 'deletepaidinterest'])->name('deletepaidinterest');
// });



//______CCL Loan Calculation routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('cclIndex', [CashCreditLimitController::class, 'cclIndex'])->name('cclIndex');
    Route::post('/getcclmebershipnumber', [CashCreditLimitController::class, 'getcclmebershipnumber'])->name('getcclmebershipnumber');
    Route::post('/getmemberccl', [CashCreditLimitController::class, 'getmemberccl'])->name('getmemberccl');
    Route::post('/getdepositlist', [CashCreditLimitController::class, 'getdepositlist'])->name('getdepositlist');
    Route::post('/getdepositamount', [CashCreditLimitController::class, 'getdepositamount'])->name('getdepositamount');
    Route::post('/ccladvancementinsert', [CashCreditLimitController::class, 'ccladvancementinsert'])->name('ccladvancementinsert');
    Route::post('/checkalreadyaccount', [CashCreditLimitController::class, 'checkalreadyaccount'])->name('checkalreadyaccount');
    Route::post('/editccldetails', [CashCreditLimitController::class, 'editccldetails'])->name('editccldetails');
    Route::post('/deletecclaccount', [CashCreditLimitController::class, 'deletecclaccount'])->name('deletecclaccount');
    Route::post('/ccladvancementupdate', [CashCreditLimitController::class, 'ccladvancementupdate'])->name('ccladvancementupdate');


//     //_____________________Recoery Route's
    Route::get('/cclrecoveryIndex', [CashCreditLimitController::class, 'cclrecoveryIndex'])->name('cclrecoveryIndex');
    Route::post('/getcclaccountlist', [CashCreditLimitController::class, 'getcclaccountlist'])->name('getcclaccountlist');
    Route::post('/getcclaccount', [CashCreditLimitController::class, 'getcclaccount'])->name('getcclaccount');
    Route::post('/cclamounttrfdsaving', [CashCreditLimitController::class, 'cclamounttrfdsaving'])->name('cclamounttrfdsaving');
    Route::post('/ccltrfdtosavingaccount', [CashCreditLimitController::class, 'ccltrfdtosavingaccount'])->name('ccltrfdtosavingaccount');
    Route::post('/viewcclledgers', [CashCreditLimitController::class, 'viewcclledgers'])->name('viewcclledgers');
    Route::post('/deleteccltrfdpayment', [CashCreditLimitController::class, 'deleteccltrfdpayment'])->name('deleteccltrfdpayment');
    Route::post('/recieptcclamount', [CashCreditLimitController::class, 'recieptcclamount'])->name('recieptcclamount');
    Route::post('/checkinterestdatewise', [CashCreditLimitController::class, 'checkinterestdatewise'])->name('checkinterestdatewise');
    Route::post('/cclrecoverInsert', [CashCreditLimitController::class, 'cclrecoverInsert'])->name('cclrecoverInsert');
    Route::post('/checktrfdinterestdatewise', [CashCreditLimitController::class, 'checktrfdinterestdatewise'])->name('checktrfdinterestdatewise');
    Route::post('/cclreceivedgetledgers', [CashCreditLimitController::class, 'cclreceivedgetledgers'])->name('cclreceivedgetledgers');
    Route::post('/checkExceedBalanceCcl', [CashCreditLimitController::class, 'checkExceedBalanceCcl'])->name('checkExceedBalanceCcl');
    Route::post('/editcheckExceedBalanceCcl', [CashCreditLimitController::class, 'editcheckExceedBalanceCcl'])->name('editcheckExceedBalanceCcl');
    Route::post('/editcclrecoverypayments', [CashCreditLimitController::class, 'editcclrecoverypayments'])->name('editcclrecoverypayments');
    Route::post('/updateccltrfdtosavingaccount', [CashCreditLimitController::class, 'updateccltrfdtosavingaccount'])->name('updateccltrfdtosavingaccount');
    Route::post('/cclrecoverUpdate', [CashCreditLimitController::class, 'cclrecoverUpdate'])->name('cclrecoverUpdate');
    Route::post('/checkRecoveryNoExceed', [CashCreditLimitController::class, 'checkRecoveryNoExceed'])->name('checkRecoveryNoExceed');
    Route::post('/getcashbankledgercodes', [CashCreditLimitController::class, 'getcashbankledgercodes'])->name('getcashbankledgercodes');

    Route::get('/sodledgerindexlist', [CashCreditLimitController::class, 'sodledgerindexlist'])->name('sodledgerindexlist');
    Route::post('/getsodaccountlist', [CashCreditLimitController::class, 'getsodaccountlist'])->name('getsodaccountlist');
    Route::post('/getsodacc', [CashCreditLimitController::class, 'getsodacc'])->name('getsodacc');


    Route::post('/closedsodaccount', [CashCreditLimitController::class, 'closedsodaccount'])->name('closedsodaccount');
    Route::post('/unclosedsodaccount', [CashCreditLimitController::class, 'unclosedsodaccount'])->name('unclosedsodaccount');

});


//________________Bank Fd Report Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/bankfdreportindex',[BankFdReportController::class,'bankfdreportindex'])->name('bankfdreportindex');
//     Route::post('/getbankfdsreportdetails',[BankFdReportController::class,'getbankfdsreportdetails'])->name('getbankfdsreportdetails');
// });






//________________CCL Report  Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/ccllistIndex', [CCLReportController::class, 'ccllistIndex'])->name('ccllistIndex');
//     Route::post('/getdataccllist', [CCLReportController::class, 'getdataccllist'])->name('getdataccllist');
// });


//________________Daily Collection Saving Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('dailyreportindex', [DailyCollectionReport::class, 'dailyreportindex'])->name('dailyreportindex');
//     Route::post('dailysavingrepostscheme', [DailyCollectionReport::class, 'dailysavingrepostscheme'])->name('dailysavingrepostscheme');
//     Route::post('getddsDetails', [DailyCollectionReport::class, 'getddsDetails'])->name('getddsDetails');
// });

//________________Balancebook Routes
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('balancebookindex', [BalanceBookController::class, 'balancebookindex'])->name('balancebookindex');
//     Route::post('/balancebookgetdata',[BalanceBookController::class, 'balancebookgetdata'])->name('balancebookgetdata');

// });












// Route::get('comingsoon', [HomeController::class, 'comingsoon'])->name('comingsoon');

// Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');


Route::middleware('auth')->controller(DynamicController::class)->group(function () {
    Route::get('/getLedger', 'getLedger')->name('getLedger');
    // Route::get('resetAccountLedger', 'resetAccountLedger')->name('resetAccountLedger');
    // Route::get('resetCdsLedger', 'resetCDSLedger')->name('resetCDSLedger');
    // Route::get('resetShareLedger', 'resetShareLedger')->name('resetShareLedger');
    // Route::get('resetFdLedger', 'resetFDLedger')->name('resetFDLedger');
    // Route::get('resetMatureFdLedger', 'resetMatureFdLedger')->name('resetMatureFDLedger');
    // Route::get('resetLoanLedger', 'resetLoanLedger')->name('resetLoanLedger');
    // Route::get('resetLoanReceptLedger', 'resetLoanReceptLedger')->name('resetLoanReceptLedger');

});

// Route::prefix('reports/cds')->middleware('auth')->controller(CdsController::class)->group(function () {
//     Route::get('/', 'index')->name('cds.index.report');
//     Route::get('/getData', 'getData')->name('cdsList.getData');
//     Route::get('/savingPrint/print', 'print')->name('cdsPrint.print');
// });


// Route::prefix('report')->middleware('auth')->controller(MISReportController::class)->group(function () {
//     Route::get('/misReport', 'index')->name('misReport.index');
//     Route::get('/misReport/getData', 'getData')->name('misReport.getData');
//     Route::get('/misPrint/print', 'print')->name('misPrint.print');
// });


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

Route::prefix('transactions/saving')->middleware('auth')->controller(SavingController::class)->group(function () {
    Route::get('/', 'index')->name('saving.index');
    Route::post('/store', 'store')->name('saving.store')->middleware('checksession');
    Route::get('/edit/{modifyId}', 'edit')->name('saving.edit');
    Route::put('/update', 'update')->name('saving.update')->middleware('checksession');
    Route::delete('/delete', 'destroy')->name('saving.delete')->middleware('checksession');
    Route::get('/pagination', 'pagination')->name('saving.pagination');
    Route::get('/getData', 'getData')->name('saving.getData');
    Route::get('/fetchData', 'fetchData')->name('saving.fetchData');
    Route::post('/getsavingaccountsdata', 'getsavingaccountsdata')->name('saving.getsavingaccountsdata');
});




// Route::prefix('transactions/trading/purchase')->middleware('auth')->controller(PurchaseController::class)->group(function () {
//     Route::get('/', 'index')->name('purchase.index');
//     Route::get('/getItemList', 'getItemList')->name('purchase.getItemList');
//     Route::get('/getItemDetail', 'getItemDetail')->name('purchase.getItemDetail');
//     Route::get('/checkItem', 'checkItem')->name('purchase.checkItem');
//     Route::post('/store', 'store')->name('purchase.store');
//     Route::get('/view/{viewId}', 'view')->name('purchase.view');
// });

// Route::prefix('transactions/trading/sale')->middleware('auth')->controller(SaleController::class)->group(function () {
//     Route::get('/', 'index')->name('sale.index');
//     Route::get('/getItemList', 'getItemList')->name('sale.getItemList');
//     Route::get('/getItemDetail', 'getItemDetail')->name('sale.getItemDetail');
//     Route::get('/checkItem', 'checkItem')->name('sale.checkItem');
//     Route::post('/store', 'store')->name('sale.store');
//     Route::get('/view/{viewId}', 'view')->name('sale.view');
// });



// Route::prefix('transactions')->middleware('auth')->group(function () {
//     Route::get('/searchVoucher', [SearchVoucherController::class, 'index'])->name('searchVoucher.index');
//     Route::get('/voucherPrint/{voucherNo}', [SearchVoucherController::class, 'print'])->name('voucherPrint.print');
// });

// Route::group(['prefix' => 'users'], function () {
//     Route::get('{type}/', [UserController::class, 'usersList'])->name('users');
//     Route::post('userStore', [UserController::class, 'userStore'])->name('userStore');
// });

// Route::prefix('transfer')->middleware('auth')->controller(TransferController::class)->group(function () {
//     Route::get('/page', 'index')->name('transfer.page');
//     Route::post('/account', 'getaccountdetails')->name('transfer.account.detail');
//     Route::post('/account/store', 'storetransferaccount')->name('store.transfer.account')->middleware('checksession');
//     Route::post('/account/data', 'update')->name('locationsupdate')->middleware('checksession');
// });


// Route::prefix('transactions/daily-loan')->middleware('auth')->controller(DailyLoanController::class)->group(function () {});

// Route::prefix('transaction/Mis')->middleware('auth')->controller(MISConrtoller::class)->group(function () {
//     Route::get('/page', 'index')->name('transaction.misspage');
//     Route::post('/account/lists', 'searchaccountlist')->name('mis.account.lists');
//     Route::post('/account/details', 'getaccountdetails')->name('get.mis.account.details');
//     Route::post('/store/mis/data', 'storemispagedata')->name('store.mis.details')->middleware('checksession');
//     Route::post('/interest/account/check', 'interestdepositcheck')->name('interest.deposit.check');
//     Route::post('/get/account/details', 'getaccountdata')->name('get.account.mis.details');
//     Route::post('/bank/receipts', 'getbankdetails')->name('mis.bank.details');
//     Route::post('/update/mis/details', 'getupdatemisdetails')->name('update.mis.details');
//     Route::post('/get/mis/installment/list', 'getmisinstallmentlist')->name('mislist.details.data');
// });




// Route::prefix('/daily/collection/loan')->middleware('auth')->controller(DailyCollectionLoanController::class)->group(function () {
//     Route::get('/', 'index')->name('daily.collection.page.loan');
//     Route::post('/get/account/details', 'getaccountsdetails')->name('daily.collection.lists.loan');
//     Route::post('/get/account/details/selected', 'getaccountsdetailselected')->name('get.account.details.selected.loan');
//     Route::post('/store/account', 'storedailycollectionaccount')->name('dailycollection.account.store.loan')->middleware('checksession');
//     Route::post('/account/edit', 'geteditdetails')->name('dailycollectionloan.edit.details');
//     Route::post('/account/bank/details', 'getbankdetails')->name('dailycollectionloan.bank.details');
//     Route::post('/modify/dailycollection', 'updatemodification')->name('update.dailyloan.collection')->middleware('checksession');
// });

//______
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/profitlossindex', [ProfitLossController::class, 'profitlossindex'])->name('profitlossindex');
//     Route::post('/getprofitlossdetails', [ProfitLossController::class, 'getprofitlossdetails'])->name('getprofitlossdetails');
//     Route::post('/updateExpenseIncomeProfitLosses',[ProfitLossController::class, 'updateExpenseIncomeProfitLosses'])->name('updateExpenseIncomeProfitLosses');
// });


// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/balancesheetindex', [BalanceSheetController::class, 'balancesheetindex'])->name('balancesheetindex');
//     Route::post('/getbalancesheetdate', [BalanceSheetController::class, 'getbalancesheetdate'])->name('getbalancesheetdate');
// });


// Route::prefix('/report/personal/ledger')->middleware('auth')->controller(PersonalLedgerController::class)->group(function () {
//     Route::get('/page', 'index')->name('personal.ledger.index');
//     Route::post('/getdetails', 'getpersonalledgerdata')->name('personal.ledger.details');
// });



// Route::prefix('/calculations')->middleware('auth')->controller(CalculationsController::class)->group(function () {
//     Route::get('/intrest', 'index')->name('calculation.index');
//     Route::post('/getdata', 'getdata')->name('calculation.getdata');
//     Route::post('/approve', 'approve')->name('calculation.approve');
//     Route::post('/deleteentry', 'deleteentry')->name('calculation.deleteentry');
// });



// Route::post('storeDeductionData', [AllListController::class, 'storeData'])->name('storeDeductionData');
// Route::get('deductionData', [AllListController::class, 'autoDeduction'])->name('autoDeduction');

// Route::any('report/daybook', [DayBookController::class, 'printPdf'])->name('printPdf');

// Route::prefix('/npaList')->middleware('auth')->controller(ReportsNPAListController::class)->group(function () {
//     Route::get('/index', 'index')->name('npaList.index');
//     Route::post('/getData', 'getData')->name('npaList.getData');
// });


// Route::prefix('/transaction/dividend')->middleware('auth')->controller(DividendController::class)->group(function () {
//     Route::get('/', 'index')->name('dividend.index');
//     // Route::post('/getbankfddetails','getbankfddetails')->name('getbankfddetails');
// });


// Route::get('/import', function () {
//     return view('import'); // Create this view for uploading files.
// });

// Route::post('/import', [ImportController::class, 'import'])->name('import');

// Route::post('import-member-accounts', [MemberAccountController::class, 'import'])->name('import.member.accounts');
