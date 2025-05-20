<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\GeneralLedger;
use App\Models\MemberLoan;
use App\Models\MemberFd;
use App\Models\ReCurringRd;
use App\Models\RdReceiptdetails;
use DateTime;
use App\Models\LoanRecovery;
use App\Models\BranchMaster;
use App\Models\RdInstallment;
use App\Models\MemberFdScheme;
use App\Models\ProfitOrLoss;
use Illuminate\Support\Facades\DB;
use App\Models\SessionMaster;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;

class ProfitLossController extends Controller
{
    public function profitlossindex()
    {
        $currentsession = session()->get('sessionyear');
        list($startYear, $endYear) = explode('-', $currentsession);
        $startDate = $startYear . '-04-01';
        $endDate = $endYear . '-03-15';
        $branch = BranchMaster::first();
        return view('report.profitLossdetails', ['startDate' => $startDate, 'branch' => $branch]);
    }



    public function getprofitlossdetails(Request $post)
    {

        $start_date = date('Y-m-d', strtotime($post->startDate));
        $end_date = date('Y-m-d', strtotime($post->endDate));

        //_______Get Current Financial Year Incomes
        $income_group = DB::table('group_masters')->where('type', 'Income')->pluck('groupCode');
        $incomes = $this->CurrentYearIncomes($income_group, $start_date, $end_date);

        //_______Get Current Financial Year Expenses
        $expenses_group = DB::table('group_masters')->where('type', 'Expenditure')->pluck('groupCode');
        $expenses = $this->CurrentYearExpenses($expenses_group, $start_date, $end_date);

        $bankInterestRecoverable = array();
        $currentLoanRecoverable = array();
        $currentFdInterestPayable = array();
        $currentDailyDepositPayable = array();
        $currentRdInterestPayable = array();
        $custom_2022_2023_pay_recoverables = array();
        $custom_2023_2024_pay_recoverable = array();
        $currentfinancialYear = '';

        if (session("sessionId")) {
            $currentSort = DB::table('session_masters')->where('id', session("sessionId"))->value('id');


            if ($currentSort) {
                $previousSort = $currentSort;

                $currentsession = SessionMaster::where('id', $previousSort)->first();
                //_______Get Current Financial Year
                $session_master = SessionMaster::find(Session::get('sessionId'));
                $sYear = date('Y', strtotime($currentsession->startDate));
                $lYear = date('y', strtotime($session_master->endDate));
                $currentfinancialYear = $sYear . '-' . $lYear;

                if (in_array($currentsession->id, [3, 4, 5])) {

                    $custom_2023_2024_pay_recoverable = array();
                    $bankInterestRecoverable = array();
                    $currentLoanRecoverable = array();
                    $currentFdInterestPayable = array();
                    $currentDailyDepositPayable = array();
                    $currentRdInterestPayable = array();
                } elseif ($currentsession->id === 1) {

                    // $custom_2023_2024_pay_recoverable = DB::table('old_payables_recoverables')->where('sessionId', $currentsession->id)->get();
                    // $custom_2022_2023_pay_recoverables = DB::table('old_payables_recoverables')->where('sessionId', 5)->get();


                    $bankInterestRecoverable = array();
                    $currentLoanRecoverable = array();
                    $currentFdInterestPayable = array();
                    $currentDailyDepositPayable = array();
                    $currentRdInterestPayable = array();
                } else {
                    //_______Current Bank FD Interest Recoverable
                    // $bankInterestRecoverable = $this->bankfdInterestRecoverable($end_date);

                    //_______Current Loan Interest Recoverable
                    $currentLoanRecoverable = $this->CurrentYearLoanInttRecoverable($start_date, $end_date);

                    //_______Current FD Interest Payable
                    // $currentFdInterestPayable = $this->CurrentFdInterestPayable($start_date, $end_date);

                    //_______Current Daily Deposit Interest Payables
                    // $currentDailyDepositPayable = $this->CurrentDailyDepositPayable($end_date);
//
                    //_______Current RD Interest Payable
                    // $currentRdInterestPayable = $this->CurrentRdInterestPayable($start_date, $end_date);
                    // dd($currentRdInterestPayable);

                }
            }
        }


        $financialYear = "";
        $customPayableRecoverbles = array();
        $lastYearStartDate = '';
        $lastYearEndDate = '';

        $LbsbankInterestRecoverable = $LbscurrentLoanRecoverable = $LbscurrentFdInterestPayable = $LbscurrentDailyDepositPayable = $LbscurrentRdInterestPayable = 0;

        if (session("sessionId")) {
            $currentSort = DB::table('session_masters')
                ->where('id', session("sessionId"))
                ->value('id');

            if ($currentSort) {
                // $previousSort = $currentSort - 1;

                $lastSession = SessionMaster::where('id', $currentSort)->first();



                $lastYearStartDate = $lastSession->startDate;
                $lastYearEndDate = $lastSession->endDate;
                $sYear = date('Y', strtotime($lastYearStartDate));
                $lYear = date('y', strtotime($lastYearEndDate));
                $financialYear = $sYear . '-' . $lYear;

                $LbsbankInterestRecoverable = $LbscurrentLoanRecoverable = $LbscurrentFdInterestPayable =
                    $LbscurrentDailyDepositPayable = $LbscurrentRdInterestPayable = 0;


                if ($lastSession) {
                    if (in_array($lastSession->id, [3, 4, 5])) {
                        // Skip calculations for sessions 3, 4, 5
                        $customPayableRecoverbles = array();
                    } elseif ($lastSession->id === 1) {
                        // Only fetch manually saved values
                        // $customPayableRecoverbles = DB::table('old_payables_recoverables')
                        //     ->where('sessionId', $lastSession->id)
                        //     ->get();
                    } else {
                        // Perform normal calculations
                        $lastYearStartDate = $lastSession->startDate;
                        $lastYearEndDate = $lastSession->endDate;

                        // $LbsbankInterestRecoverable     = $this->LbsbankfdInterestRecoverable($lastYearEndDate);
                        // $LbscurrentLoanRecoverable      = $this->LbsCurrentYearLoanInttRecoverable($lastYearStartDate, $lastYearEndDate);
                        // $LbscurrentFdInterestPayable    = $this->LbsCurrentFdInterestPayable($lastYearStartDate, $lastYearEndDate);
                        // $LbscurrentDailyDepositPayable  = $this->LbsCurrentDailyDepositPayable($lastYearEndDate);
                        // $LbscurrentRdInterestPayable    = $this->LbsCurrentRdInterestPayable($lastYearStartDate, $lastYearEndDate);

                        // Fetch any custom overrides
                        // $customPayableRecoverbles = DB::table('old_payables_recoverables')
                        //     ->where('sessionId', $lastSession->id)
                        //     ->get();
                    }
                } else {
                    // No session found, maybe default to current logic?
                    // Log::warning("No previous session found with sort number: $previousSort");

                    // You could skip calculations or assign fallbacks here
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'currentfinancialYear' => $currentfinancialYear,
            'lastfinancialYear' => $financialYear,
            'lastYearStartDate' => $lastYearStartDate,
            'lastYearEndDate' => $lastYearEndDate,
            'custom_2023_2024_pay_recoverable' => $custom_2023_2024_pay_recoverable,
            'custom_2022_2023_pay_recoverables' => $custom_2022_2023_pay_recoverables,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'bankInterestRecoverable' => $bankInterestRecoverable,
            'currentRdInterestPayable' => $currentRdInterestPayable,
            'currentFdInterestPayable' => $currentFdInterestPayable,
            'currentDailyDepositPayable' => $currentDailyDepositPayable,
            'currentLoanRecoverable' => $currentLoanRecoverable,
            'lastpayables' => $customPayableRecoverbles,
            'LbsbankInterestRecoverable' => $LbsbankInterestRecoverable,
            'LbscurrentLoanRecoverable' => $LbscurrentLoanRecoverable,
            'LbscurrentFdInterestPayable' => $LbscurrentFdInterestPayable,
            'LbscurrentDailyDepositPayable' => $LbscurrentDailyDepositPayable,
            'LbscurrentRdInterestPayable' => $LbscurrentRdInterestPayable,
        ]);
    }

    //______________________________________Current Year Expenses/Incomes/Recoverables/Payables Details____________________________________


    //________Current Year Incomes
    private function CurrentYearIncomes($income_group, $start_date, $end_date)
    {
        $incomes = GeneralLedger::select(
            'ledger_masters.name as ledger_name',
            'ledger_masters.ledgerCode',
            DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" THEN general_ledgers.transactionAmount ELSE 0 END) as total_income'),
            DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" THEN general_ledgers.transactionAmount ELSE 0 END) as total_income_debit'),
        )
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
            ->whereIn('general_ledgers.groupCode', $income_group)
            ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
            ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
            ->groupBy('ledger_masters.name', 'ledger_masters.ledgerCode')
            ->get();
        return $incomes;
    }

    //________Current Year Expenses
    private function CurrentYearExpenses($expenses_group, $start_date, $end_date)
    {
        $expenses = GeneralLedger::select(
            'ledger_masters.name as ledger_name',
            'ledger_masters.ledgerCode',
            DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" THEN general_ledgers.transactionAmount ELSE 0 END) as credit'),
            DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" THEN general_ledgers.transactionAmount ELSE 0 END) as debit'),
        )
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
            ->whereIn('general_ledgers.groupCode', $expenses_group)
            ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
            ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
            ->groupBy('ledger_masters.name', 'ledger_masters.ledgerCode')
            ->get();

        return $expenses;
    }

    //________Current Year Bank RD Interest Payables
    // private function CurrentRdInterestPayable($start_date, $end_date)
    // {
    //     $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

    //     $data['memberType'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //             re_curring_rds.rd_account_no,
    //             re_curring_rds.interest,
    //             re_curring_rds.month,
    //             re_curring_rds.date,
    //             re_curring_rds.secheme_id,
    //             member_accounts.accountNo,
    //             member_accounts.name,
    //             member_accounts.memberType as amtp,
    //             rd_receiptdetails.rc_account_no as rcac,
    //             rd_receiptdetails.memberType as rc_member_type,
    //             scheme_masters.id as schid,
    //             scheme_masters.name as schname,
    //             scheme_masters.secheme_type,
    //             SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //             IF(
    //                 re_curring_rds.date >= ?
    //                 AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                 AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                 'Active',
    //                 re_curring_rds.status
    //             ) AS current_status
    //         ", [$end_date, $start_date, $end_date])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($end_date) {
    //             $query->where(function ($q) use ($end_date) {
    //                 $q->where('re_curring_rds.date', '<=', $end_date)
    //                     ->where(function ($q2) use ($end_date) {
    //                         $q2->whereNull('re_curring_rds.actual_maturity_date')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($end_date) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $end_date)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'Member')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();


    //     $data['nonmemberType'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //             re_curring_rds.rd_account_no,
    //             re_curring_rds.interest,
    //             re_curring_rds.month,
    //             re_curring_rds.date,
    //             member_accounts.accountNo,
    //             member_accounts.name,
    //             member_accounts.memberType as amtp,
    //             rd_receiptdetails.rc_account_no as rcac,
    //             rd_receiptdetails.memberType as rc_member_type,
    //             re_curring_rds.secheme_id,
    //             scheme_masters.id as schid,
    //             scheme_masters.name as schname,
    //             scheme_masters.secheme_type,
    //             SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //                 IF(
    //                     re_curring_rds.date >= ?
    //                     AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                     AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                     'Active',
    //                     re_curring_rds.status
    //                 ) AS current_status
    //             ", [$end_date, $start_date, $end_date])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($end_date) {
    //             $query->where(function ($q) use ($end_date) {
    //                 $q->where('re_curring_rds.date', '<=', $end_date)
    //                     ->where(function ($q2) use ($end_date) {
    //                         $q2->whereNull('re_curring_rds.actual_maturity_date')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($end_date) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $end_date)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'NonMember')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();


    //     $data['Staff'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //                 re_curring_rds.rd_account_no,
    //                 re_curring_rds.interest,
    //                 re_curring_rds.month,
    //                 re_curring_rds.date,
    //                 member_accounts.accountNo,
    //                 member_accounts.name,
    //                 member_accounts.memberType as amtp,
    //                 rd_receiptdetails.rc_account_no as rcac,
    //                 rd_receiptdetails.memberType as rc_member_type,
    //                 re_curring_rds.secheme_id,
    //                 scheme_masters.id as schid,
    //                 scheme_masters.name as schname,
    //                 scheme_masters.secheme_type,
    //                 SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //                 IF(
    //                     re_curring_rds.date >= ?
    //                     AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                     AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                     'Active',
    //                     re_curring_rds.status
    //                 ) AS current_status
    //                 ", [$end_date, $start_date, $end_date])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($end_date) {
    //             $query->where(function ($q) use ($end_date) {
    //                 $q->where('re_curring_rds.date', '<=', $end_date)
    //                     ->where(function ($q2) use ($end_date) {
    //                         $q2->whereNull('re_curring_rds.actual_maturity_date')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($end_date) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $end_date)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'Staff')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();

    //     return $data;
    // }

    //________Current Year Bank Fd Interest Payables
    // private function CurrentFdInterestPayable($start_date, $end_date)
    // {
    //     $depositeTypesId = DB::table('fd_type_master')->orderBy('id', 'ASC')->pluck('id');

    //     // Query for Member type
    //     $memberData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$start_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
    //         ->where('member_fds_scheme.memberType', 'Member')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$end_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$end_date'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Query for NonMember type
    //     $nonMemberData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$start_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
    //         ->where('member_fds_scheme.memberType', 'NonMember')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$end_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$end_date'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Query for Staff type
    //     $staffData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$start_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
    //         ->where('member_fds_scheme.memberType', 'Staff')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$end_date'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$end_date'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Combine all results
    //     $data = $memberData->merge($nonMemberData)->merge($staffData);

    //     return $data;
    // }

    //________Current Year Bank Daily Deposit Interest Payables
    // private function CurrentDailyDepositPayable($end_date)
    // {

    //     $qq = DB::table('scheme_masters')->where('secheme_type', 'DDS')->orderBy('id', 'ASC')->pluck('id');

    //     $data['memberType'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //             // 'daily_collectionsavings.sch_id'
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'Member')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
    //         // ->whereIn('daily_collectionsavings.sch_id',$qq)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //             // 'daily_collectionsavings.sch_id'
    //         )
    //         ->get();


    //     $data['nonmemberType'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'NonMember')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //         )
    //         ->get();



    //     $data['Staff'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'Staff')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //         )
    //         ->get();

    //     return $data;
    // }

    //_______Current Year Loan Intt. Recoverable
    private function CurrentYearLoanInttRecoverable($start_date, $end_date)
    {
        //_______Loan Interest Recoverables
        $recoverableAmountTotal = 0;
        $loanmasters = MemberLoan::where('is_delete', '!=', 'Yes')
            // ->where('status', 'Disbursed')
            // ->whereDate('loanDate', '>=',$start_date)
            ->whereDate('loanDate', '<=', $end_date)
            ->get();
        // dd($loanmasters);

        if ($loanmasters->count() > 0) {
            foreach ($loanmasters as $loanmaster) {
                $loan_recovery = LoanRecovery::where('loanId', $loanmaster->id)
                    ->where('is_delete', 'No')
                    ->where('receiptDate', '<=', $end_date)
                    ->sum('principal');
                $openingdate = new DateTime($loanmaster->loanDate);
                $currentdate = new DateTime($end_date);
                $interval = $openingdate->diff($currentdate);
                $totalDaysDifference = $interval->days + 1;
                $recoverableAmount = $loanmaster->loanAmount - $loan_recovery;
                $perdayinterest = $loanmaster->loanInterest / 365;
                $calculateformula = (($recoverableAmount * $totalDaysDifference) * $perdayinterest) / 100;
                $recoverableAmountTotal += round($calculateformula,2);
            }
        }

        return $recoverableAmountTotal;
    }

    //________Current Year Bank Fd Interest Recoverables
    // private function bankfdInterestRecoverable($end_date)
    // {
    //     $bankInterestRecoverable = DB::table('bank_fd_deposit')
    //         ->select('bank_fd_deposit.*', 'bank_fd_masters.id as bankId', 'bank_fd_masters.bank_name', 'bank_fd_masters.ledgerCode')
    //         ->leftJoin('bank_fd_masters', 'bank_fd_masters.id', '=', 'bank_fd_deposit.bank_fd_type')
    //         ->whereDate('bank_fd_deposit.fd_date', '<=', $end_date)
    //         ->where('bank_fd_deposit.status', 'Active')
    //         ->get();

    //     return $bankInterestRecoverable;
    // }


    //______________________________________Last Year LBS Recoverables/Payables Details____________________________________

    //________Current Year Bank RD Interest Payables
    // private function LbsCurrentRdInterestPayable($lastYearStartDate, $lastYearEndDate)
    // {

    //     $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

    //     $data['memberType'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //                 re_curring_rds.rd_account_no,
    //                 re_curring_rds.interest,
    //                 re_curring_rds.month,
    //                 re_curring_rds.date,
    //                 member_accounts.accountNo,
    //                 member_accounts.name,
    //                 member_accounts.memberType as amtp,
    //                 rd_receiptdetails.rc_account_no as rcac,
    //                 rd_receiptdetails.memberType as rc_member_type,
    //                 re_curring_rds.secheme_id,
    //                 scheme_masters.id as schid,
    //                 scheme_masters.name as schname,
    //                 scheme_masters.secheme_type,
    //                 SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //                 IF(
    //                     re_curring_rds.date >= ?
    //                     AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                     AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                     'Active',
    //                     re_curring_rds.status
    //                 ) AS current_status
    //             ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($lastYearEndDate) {
    //             $query->where(function ($q) use ($lastYearEndDate) {
    //                 $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                     ->where(function ($q2) use ($lastYearEndDate) {
    //                         $q2->whereNull('re_curring_rds.actual_maturity_date')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($lastYearEndDate) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'Member')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();


    //     $data['nonmemberType'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //                  re_curring_rds.rd_account_no,
    //                 re_curring_rds.interest,
    //                 re_curring_rds.month,
    //                 re_curring_rds.date,
    //                 member_accounts.accountNo,
    //                 member_accounts.name,
    //                 member_accounts.memberType as amtp,
    //                 rd_receiptdetails.rc_account_no as rcac,
    //                 rd_receiptdetails.memberType as rc_member_type,
    //                 re_curring_rds.secheme_id,
    //                 scheme_masters.id as schid,
    //                 scheme_masters.name as schname,
    //                 scheme_masters.secheme_type,
    //                 SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //                     IF(
    //                         re_curring_rds.date >= ?
    //                         AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                         AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                         'Active',
    //                         re_curring_rds.status
    //                     ) AS current_status
    //                 ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($lastYearEndDate) {
    //             $query->where(function ($q) use ($lastYearEndDate) {
    //                 $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                     ->where(function ($q2) use ($lastYearEndDate) {
    //                         $q2->whereNull('re_curring_rds.actual_maturity_date')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($lastYearEndDate) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'NonMember')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();


    //     $data['Staff'] = DB::table('re_curring_rds')
    //         ->selectRaw("
    //                 re_curring_rds.rd_account_no,
    //                 re_curring_rds.interest,
    //                 re_curring_rds.month,
    //                 re_curring_rds.date,
    //                 member_accounts.accountNo,
    //                 member_accounts.name,
    //                 member_accounts.memberType as amtp,
    //                 rd_receiptdetails.rc_account_no as rcac,
    //                 rd_receiptdetails.memberType as rc_member_type,
    //                 re_curring_rds.secheme_id,
    //                 scheme_masters.id as schid,
    //                 scheme_masters.name as schname,
    //                 scheme_masters.secheme_type,
    //                     SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
    //                     IF(
    //                         re_curring_rds.date >= ?
    //                         AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
    //                         AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
    //                         'Active',
    //                         re_curring_rds.status
    //                     ) AS current_status
    //                     ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
    //         ->leftJoin('member_accounts', function (JoinClause $join) {
    //             $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
    //                 ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
    //             $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
    //                 ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
    //         })
    //         ->leftJoin('scheme_masters', function (JoinClause  $join) {
    //             $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
    //                 ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
    //                 // ->on('scheme_masters.secheme_type', '=', 'RD');
    //         })
    //         ->where(function ($query) use ($lastYearEndDate) {
    //             $query->where(function ($q) use ($lastYearEndDate) {
    //                 $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                     ->where(function ($q2) use ($lastYearEndDate) {
    //                         $q2->whereNull('re_curring_rds. ')
    //                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
    //                     })
    //                     ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
    //             })
    //                 ->orWhere(function ($q) use ($lastYearEndDate) {
    //                     $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
    //                         ->where('re_curring_rds.date', '<=', $lastYearEndDate)
    //                         ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
    //                 });
    //         })
    //         ->where('re_curring_rds.memberType', 'Staff')
    //         ->groupBy(
    //             're_curring_rds.rd_account_no',
    //             're_curring_rds.interest',
    //             're_curring_rds.month',
    //             're_curring_rds.date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType',
    //             'rd_receiptdetails.rc_account_no',
    //             'rd_receiptdetails.memberType',
    //             're_curring_rds.actual_maturity_date',
    //             're_curring_rds.status',
    //             're_curring_rds.secheme_id',
    //             'scheme_masters.id',
    //             'scheme_masters.name',
    //             'scheme_masters.secheme_type',
    //         )
    //         ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
    //         ->orderBy('re_curring_rds.date', 'ASC')
    //         ->get();

    //     return $data;
    // }


    //________Current Year Bank Fd Interest Payables
    // private function LbsCurrentFdInterestPayable($lastYearStartDate, $lastYearEndDate)
    // {
    //     $depositeTypesId = DB::table('fd_type_master')->orderBy('id', 'ASC')->pluck('id');

    //     // Query for Member type
    //     $memberData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$lastYearStartDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
    //         ->where('member_fds_scheme.memberType', 'Member')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$lastYearEndDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$lastYearEndDate'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Query for NonMember type
    //     $nonMemberData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$lastYearStartDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
    //         ->where('member_fds_scheme.memberType', 'NonMember')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$lastYearEndDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$lastYearEndDate'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Query for Staff type
    //     $staffData = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             'member_accounts.accountNo as ac',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'fd_type_master.id as typeids',
    //             'fd_type_master.type as fdname',
    //             'member_fds_scheme.secheme_id',
    //             DB::raw(
    //                 "IF(
    //                     member_fds_scheme.openingDate >= '$lastYearStartDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                     AND member_fds_scheme.status != 'Matured'
    //                     AND member_fds_scheme.status != 'Renewed',
    //                     member_fds_scheme.status, 'Other'
    //                 ) AS status"
    //             )
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
    //         ->where('member_fds_scheme.memberType', 'Staff')
    //         ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
    //         ->whereRaw(
    //             "NOT (
    //                 (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //                 AND (
    //                     member_fds_scheme.openingDate <= '$lastYearEndDate'
    //                     AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
    //                 )
    //             )"
    //         )
    //         ->orWhereRaw(
    //             "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
    //             AND member_fds_scheme.openingDate > '$lastYearEndDate'"
    //         )
    //         ->orderBy('member_fds_scheme.openingDate', 'ASC')
    //         ->get();

    //     // Combine all results
    //     $data = $memberData->merge($nonMemberData)->merge($staffData);

    //     return $data;
    // }

    //________Current Year Bank Daily Deposit Interest Payables
    // private function LbsCurrentDailyDepositPayable($lastYearEndDate)
    // {
    //     $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

    //     $data['memberType'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //             // 'daily_collectionsavings.sch_id'
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'Member')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
    //         // ->whereIn('daily_collectionsavings.sch_id',$qq)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //             // 'daily_collectionsavings.sch_id'
    //         )
    //         ->get();


    //     $data['nonmemberType'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'NonMember')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //         )
    //         ->get();



    //     $data['Staff'] = DB::table('daily_collectionsavings')
    //         ->select(
    //             'dailyaccountid',
    //             DB::raw('SUM(deposit) AS total_amount'),
    //             DB::raw('SUM(withdraw) AS withdraw'),
    //             'daily_collections.id as ids',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo as anumber',
    //             'member_accounts.name',
    //             'scheme_masters.id as schid',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name as schname',
    //         )
    //         ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
    //         ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
    //         ->where('daily_collectionsavings.memberType', 'Staff')
    //         ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
    //         ->groupBy(
    //             'dailyaccountid',
    //             'daily_collections.id',
    //             'daily_collections.interest',
    //             'daily_collections.days',
    //             'daily_collections.opening_date',
    //             'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'scheme_masters.id',
    //             'daily_collectionsavings.memberType',
    //             'scheme_masters.name',
    //         )
    //         ->get();

    //     return $data;
    // }

    //_______Current Year Loan Intt. Recoverable
    private function LbsCurrentYearLoanInttRecoverable($lastYearStartDate, $lastYearEndDate)
    {
        //_______Loan Interest Recoverables
        $recoverableAmountTotal = 0;
        $loanmasters = MemberLoan::where('is_delete', '!=', 'Yes')
            // ->where('status', 'Disbursed')
            // ->whereDate('loanDate', '>=',$lastYearStartDate)
            ->whereDate('loanDate', '<=', $lastYearEndDate)
            ->get();
        // dd($loanmasters);

        if ($loanmasters->count() > 0) {
            foreach ($loanmasters as $loanmaster) {
                $loan_recovery = LoanRecovery::where('loanId', $loanmaster->id)
                    ->where('is_delete', 'No')
                    ->where('receiptDate', '<=', $lastYearEndDate)
                    ->sum('principal');
                $openingdate = new DateTime($loanmaster->loanDate);
                $currentdate = new DateTime($lastYearEndDate);
                $interval = $openingdate->diff($currentdate);
                $totalDaysDifference = $interval->days + 1;
                $recoverableAmount = $loanmaster->loanAmount - $loan_recovery;
                $perdayinterest = $loanmaster->loanInterest / 365;
                $calculateformula = (($recoverableAmount * $totalDaysDifference) * $perdayinterest) / 100;
                $recoverableAmountTotal += round($calculateformula,2);
            }
        }

        return $recoverableAmountTotal;
    }

    //________Current Year Bank Fd Interest Recoverables
    // private function LbsbankfdInterestRecoverable($lastYearEndDate)
    // {
    //     $bankInterestRecoverable = DB::table('bank_fd_deposit')
    //         ->select('bank_fd_deposit.*', 'bank_fd_masters.id as bankId', 'bank_fd_masters.bank_name', 'bank_fd_masters.ledgerCode')
    //         ->leftJoin('bank_fd_masters', 'bank_fd_masters.id', '=', 'bank_fd_deposit.bank_fd_type')
    //         ->whereDate('bank_fd_deposit.fd_date', '<=', $lastYearEndDate)
    //         ->where('bank_fd_deposit.status', 'Active')
    //         ->get();

    //     return $bankInterestRecoverable;
    // }







    public function updateExpenseIncomeProfitLosses(Request $post){

        $rules = [
            "allexpenses" => "required",
            "allincomes" => "required",
            "sessionId" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Updated']);
        }

        $sessionId = $post->sessionId;
        $netprofit = abs((float) $post->netprofit);
        $netlosses = abs((float) $post->netlosses);
        logger(['Net Profit' => $netprofit, 'Net Losses' => $netlosses]);


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        try{

            $existsCheck = ProfitOrLoss::where('sessionId',$sessionId)->first();

            // $existsCheck = DB::table('profit_losses')->where('sessionId',$sessionId)->first();
            if(!empty($existsCheck)){

                if ($netprofit > 0) {
                    $existsCheck->name = 'Net Profit';
                    $existsCheck->sessionId = $sessionId;
                    $existsCheck->amount = $netprofit;
                    $existsCheck->save();
                }

                if ($netlosses > 0) {
                    $existsCheck->name = 'Net Loss';
                    $existsCheck->sessionId = $sessionId;
                    $existsCheck->amount = $netlosses;
                    $existsCheck->save();
                }
            }else{
                if ($netprofit > 0) {
                    DB::table('profit_losses')->insert([
                        'name' => 'Net Profit',
                        'sessionId' => $sessionId,
                        'amount' => $netprofit
                    ]);
                }

                if ($netlosses > 0) {
                    DB::table('profit_losses')->insert([
                        'name' => 'Net Loss',
                        'sessionId' => $sessionId,
                        'amount' => $netlosses
                    ]);
                }
            }

            return response()->json(['status' => 'Success', 'messages' => 'Data Updated Successfully']);

        }catch(\Exception $e){

            return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'line' => $e->getLine()]);

        }
    }
}
