<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\MemberLoan;
use DateTime;
use App\Models\LoanRecovery;
use App\Models\ReCurringRd;
use App\Models\GeneralLedger;
use App\Models\RdReceiptdetails;
use App\Models\MemberFdScheme;
use App\Models\BranchMaster;
use App\Models\SessionMaster;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BalanceSheetController extends Controller
{
    public function balancesheetindex()
    {
        $branch = BranchMaster::first();
        return view('report.balancesheet', compact('branch'));
    }

    public function getbalancesheetdate(Request $post)
    {



        //     // Fetch ccl_payments where serialNo not in general_ledgers
        // $cclPayments = DB::table('ccl_payments')
        //     ->whereNotIn(
        //         DB::raw('CONVERT(`serialNo` USING utf8mb4) COLLATE utf8mb4_unicode_ci'),
        //         function ($query) {
        //             $query->select(DB::raw('CONVERT(`serialNo` USING utf8mb4) COLLATE utf8mb4_unicode_ci'))
        //                 ->from('general_ledgers')
        //                 ->where('transactionDate', '<=', '2026-04-30')
        //                 ->where('groupCode', 'MEM01');
        //         }
        //     )
        //     ->get();
        // dd($cclPayments);

        // foreach($cclPayments as $row){
        //     $gerenalLedger = DB::table('general_ledgers')
        //         ->where('serialNo',$row->serialNo)
        //         ->where('referenceNo',$row->id)
        //         ->where('transactionAmount',$row->recovey_amount)
        //         ->where('transactionType','Cr')
        //         ->update([
        //             'groupCode' => 'MEM01',
        //             'ledgerCode' => 'MEM178'
        //         ]);

        // }
        //     return response()->json(['status' => 'success','messages' => 'Record Updated successfully']);



        $start_date = date('Y-m-d', strtotime($post->start_date));
        $end_date = date('Y-m-d', strtotime($post->end_date));



        //_________Get Current Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));
        $sYear = date('Y', strtotime($session_master->startDate));
        $lYear = date('y', strtotime($session_master->endDate));
        $currentfinancialYear = $sYear . '-' . $lYear;


        //_______Get Current Year Assets Data
        $assets_groups = DB::table('group_masters')->where('type', 'Asset')->pluck('groupCode');
        $assets =  $this->CurrentAssets($assets_groups, $start_date, $end_date);


        //_______Get Current Year Liabilities Data
        $liablity_group = DB::table('group_masters')->where('type', 'Liability')->pluck('groupCode');
        $liabilties = $this->CurrentLiabilities($liablity_group, $start_date, $end_date);


        //_______Get Current Financial Year Incomes
        $income_group = DB::table('group_masters')->where('type', 'Income')->pluck('groupCode');
        $incomes = $this->CurrentYearIncomes($income_group, $start_date, $end_date);

        //_______Get Current Financial Year Expenses
        $expenses_group = DB::table('group_masters')->where('type', 'Expenditure')->pluck('groupCode');
        $expenses = $this->CurrentYearExpenses($expenses_group, $start_date, $end_date);


        $sessionId = Session::get('sessionId');
        // dd($sessionId);

        // $opening_losses = DB::table('profit_losses')
        //     ->where('sessionId', '<', $sessionId)
        //     ->where('name', 'Opening Losses')
        //     ->sum('amount');

        // $opening_profit = DB::table('profit_losses')
        //     ->where('sessionId', '<', $sessionId)
        //     ->where('name', 'Opening profit')
        //     ->sum('amount');

        // $net_profit = DB::table('profit_losses')
        //     ->where('sessionId', '<', $sessionId)
        //     ->where('name', 'Net Profit')
        //     ->sum('amount');

        // $net_losses = DB::table('profit_losses')
        //     ->where('sessionId', '<', $sessionId)
        //     ->where('name', 'Net Loss')
        //     ->sum('amount');


        // $current_losses = DB::table('profit_losses')
        //     ->where('sessionId', $sessionId)
        //     ->where('name', 'Net Loss')
        //     ->sum('amount');

        // $current_profit = DB::table('profit_losses')
        //     ->where('sessionId', $sessionId)
        //     ->where('name', 'Net Profit')
        //     ->sum('amount');

        $opening_p = 0;
        $opening_l = 0;
        $opening_sss = 0;
        $opening_losses = 0;
        $opening_profit = 0;
        $net_profit = 0;
        $net_losses = 0;
        $current_losses = 0;
        $current_profit = 0;


        $opening_sss = $opening_losses - $opening_profit - $net_profit + $net_losses;





        if ($opening_sss > 0) {
            $opening_l += $opening_sss;
        } else {
            $opening_p += abs($opening_sss);
        }





        $bankInterestRecoverable = array();
        $currentLoanRecoverable = array();
        $currentFdInterestPayable = array();
        $currentDailyDepositPayable = array();
        $currentRdInterestPayable = array();
        $custom_2022_2023_pay_recoverables = array();
        $custom_2023_2024_pay_recoverable = array();

        $currentfinancialYear = '';

        if (session("sessionId")) {
            $currentSort = DB::table('session_masters')->where('id', session("sessionId"))->value('sortno');
            if ($currentSort) {
                $previousSort = $currentSort;

                $currentsession = SessionMaster::where('sortno', $previousSort)->first();
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
                    $custom_2022_2023_pay_recoverables = array();
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
                    $bankInterestRecoverable = $this->bankfdInterestRecoverable($end_date);

                    //_______Current Loan Interest Recoverable
                    $currentLoanRecoverable = $this->CurrentYearLoanInttRecoverable($start_date, $end_date);

                    //_______Current FD Interest Payable
                    $currentFdInterestPayable = $this->CurrentFdInterestPayable($start_date, $end_date);

                    //_______Current Daily Deposit Interest Payables
                    $currentDailyDepositPayable = $this->CurrentDailyDepositPayable($end_date);

                    //_______Current RD Interest Payable
                    $currentRdInterestPayable = $this->CurrentRdInterestPayable($start_date, $end_date);
                }
            }
        }

        $financialYear = "";
        // $customPayableRecoverbles = array();

        $LbsbankInterestRecoverable = 0;
        $LbscurrentLoanRecoverable = 0;
        $LbscurrentFdInterestPayable = 0;
        $LbscurrentDailyDepositPayable = 0;
        $LbscurrentRdInterestPayable = 0;
        $lastYearStartDate = '';
        $lastYearEndDate = '';


        if (session("sessionId")) {
            $currentSort = DB::table('session_masters')
                ->where('id', session("sessionId"))
                ->value('sortno');

            if ($currentSort) {
                $previousSort = $currentSort - 1;

                $lastSession = SessionMaster::where('sortno', $previousSort)->first();

                $lastYearStartDate = $lastSession->startDate;
                $lastYearEndDate = $lastSession->endDate;
                $sYear = date('Y', strtotime($lastYearStartDate));
                $lYear = date('y', strtotime($lastYearEndDate));
                $financialYear = $sYear . '-' . $lYear;

                $LbsbankInterestRecoverable = 0;
                $LbscurrentLoanRecoverable = 0;
                $LbscurrentFdInterestPayable = 0;
                $LbscurrentDailyDepositPayable = 0;
                $LbscurrentRdInterestPayable = 0;


                if ($lastSession) {
                    if (in_array($lastSession->id, [3, 4, 5])) {
                        // Skip calculations for sessions 3, 4, 5
                        // $customPayableRecoverbles = array();
                    } elseif ($lastSession->id === 1) {
                        // Only fetch manually saved values
                        // $customPayableRecoverbles = DB::table('old_payables_recoverables')->where('sessionId', $lastSession->id)->get();
                    } else {
                        // Perform normal calculations
                        $lastYearStartDate = $lastSession->startDate;
                        $lastYearEndDate = $lastSession->endDate;

                        $LbsbankInterestRecoverable    = $this->LbsbankfdInterestRecoverable($lastYearEndDate);
                        $LbscurrentLoanRecoverable     = $this->LbsCurrentYearLoanInttRecoverable($lastYearStartDate, $lastYearEndDate);
                        $LbscurrentFdInterestPayable   = $this->LbsCurrentFdInterestPayable($lastYearStartDate, $lastYearEndDate);
                        $LbscurrentDailyDepositPayable = $this->LbsCurrentDailyDepositPayable($lastYearEndDate);
                        $LbscurrentRdInterestPayable   = $this->LbsCurrentRdInterestPayable($lastYearStartDate, $lastYearEndDate);

                        // Fetch any custom overrides
                        // $customPayableRecoverbles = DB::table('old_payables_recoverables')->where('sessionId', $lastSession->id)->get();
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
            'assets' => $assets,
            'liabilities' => $liabilties,
            'custom_2023_2024_pay_recoverable' => $custom_2023_2024_pay_recoverable,
            'currentfinancialYear' => $currentfinancialYear,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'lastYearStartDate' => $lastYearStartDate,
            'lastYearEndDate' => $lastYearEndDate,
            'bankInterestRecoverable' => $bankInterestRecoverable,
            'currentRdInterestPayable' => $currentRdInterestPayable,
            'currentFdInterestPayable' => $currentFdInterestPayable,
            'currentDailyDepositPayable' => $currentDailyDepositPayable,
            'currentLoanRecoverable' => $currentLoanRecoverable,
            'lastpayables' => $custom_2022_2023_pay_recoverables,
            'LbsbankInterestRecoverable' => $LbsbankInterestRecoverable,
            'LbscurrentLoanRecoverable' => $LbscurrentLoanRecoverable,
            'LbscurrentFdInterestPayable' => $LbscurrentFdInterestPayable,
            'LbscurrentDailyDepositPayable' => $LbscurrentDailyDepositPayable,
            'LbscurrentRdInterestPayable' => $LbscurrentRdInterestPayable,
            'opening_l' => $opening_l,
            'opening_p' => $opening_p,
            'current_losses' => $current_losses,
            'current_profit' => $current_profit,

        ]);
    }


    private function CurrentAssets($assets_groups, $start_date, $end_date)
    {
        $assets = DB::table('ledger_masters')
            ->select(
                'ledger_masters.groupCode as groups',
                'ledger_masters.ledgerCode as ledgers',
                'ledger_masters.name as ledger_name',
                'group_masters.groupCode as grcode',
                'group_masters.name as group_name',
                'ledger_masters.openingType',
                'ledger_masters.openingAmount',
                DB::raw('CASE WHEN (ledger_masters.openingType = "Dr" AND ledger_masters.openingAmount IS NOT NULL) THEN ledger_masters.openingAmount ELSE 0 END as opening'),
                DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" AND general_ledgers.transactionAmount IS NOT NULL THEN general_ledgers.transactionAmount ELSE 0 END) as credit_amount'),
                DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" AND general_ledgers.transactionAmount IS NOT NULL THEN general_ledgers.transactionAmount ELSE 0 END) as debit_amount')
            )
            ->leftJoin('general_ledgers', function ($join) use ($end_date) {
                $join->on('ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
                    ->whereDate('general_ledgers.transactionDate', '<=', $end_date);
            })
            ->leftJoin('group_masters', 'group_masters.groupCode', '=', 'ledger_masters.groupCode')
            ->whereIn('ledger_masters.groupCode', $assets_groups)
            ->where('general_ledgers.is_delete', 'No')
            ->groupBy(
                'ledger_masters.groupCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.name',
                'group_masters.groupCode',
                'group_masters.name',
                'ledger_masters.openingAmount',
                'ledger_masters.openingType'
            )
            ->get();

        return $assets;
    }
    private function CurrentLiabilities($liablity_group, $start_date, $end_date)
    {

        $liabilties = DB::table('ledger_masters')
            ->select(
                'ledger_masters.groupCode as groups',
                'ledger_masters.ledgerCode as ledgers',
                'ledger_masters.name as ledger_name',
                'group_masters.groupCode as grcode',
                'group_masters.name as group_name',
                'ledger_masters.openingType',
                'ledger_masters.openingAmount',
                DB::raw('CASE WHEN (ledger_masters.openingType = "Cr" AND ledger_masters.openingAmount IS NOT NULL) THEN ledger_masters.openingAmount ELSE 0 END as opening'),
                DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" AND general_ledgers.transactionAmount IS NOT NULL THEN general_ledgers.transactionAmount ELSE 0 END) as credit_amount'),
                DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" AND general_ledgers.transactionAmount IS NOT NULL THEN general_ledgers.transactionAmount ELSE 0 END) as debit_amount')
            )
            ->leftJoin('general_ledgers', function ($join) use ($end_date) {
                $join->on('ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
                    ->whereDate('general_ledgers.transactionDate', '<=', $end_date);
            })
            ->leftJoin('group_masters', 'group_masters.groupCode', '=', 'ledger_masters.groupCode')
            ->whereIn('ledger_masters.groupCode', $liablity_group)
            ->where('general_ledgers.is_delete', 'No')
            ->groupBy(
                'ledger_masters.groupCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.name',
                'group_masters.groupCode',
                'group_masters.name',
                'ledger_masters.openingAmount',
                'ledger_masters.openingType'
            )
            ->get();
        return $liabilties;
    }


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
                $recoverableAmountTotal += round($calculateformula, 2);
            }
        }

        return $recoverableAmountTotal;
    }






    //________Current Year Bank Fd Interest Recoverables
    private function bankfdInterestRecoverable($end_date)
    {

        // $bankInterestRecoverable = DB::table('bank_fd_deposit')
        //     ->select('bank_fd_deposit.*', 'bank_fd_masters.id as bankId', 'bank_fd_masters.bank_name', 'bank_fd_masters.ledgerCode')
        //     ->leftJoin('bank_fd_masters', 'bank_fd_masters.id', '=', 'bank_fd_deposit.bank_fd_type')
        //     ->whereDate('bank_fd_deposit.fd_date', '<=', $end_date)
        //     ->where('bank_fd_deposit.status', 'Active')
        //     ->get();

        // return $bankInterestRecoverable;
    }

    //________Current Year Bank Fd Interest Payables
    private function CurrentFdInterestPayable($start_date, $end_date)
    {

        $depositeTypesId = DB::table('fd_type_master')->orderBy('id', 'ASC')->pluck('id');

        // Query for Member type
        $memberData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                         member_fds_scheme.openingDate >= '$start_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                         AND member_fds_scheme.status != 'Matured'
                         AND member_fds_scheme.status != 'Renewed',
                         member_fds_scheme.status, 'Other'
                     ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
            ->where('member_fds_scheme.memberType', 'Member')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                     (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                     AND (
                         member_fds_scheme.openingDate <= '$end_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                     )
                 )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                 AND member_fds_scheme.openingDate > '$end_date'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Query for NonMember type
        $nonMemberData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                         member_fds_scheme.openingDate >= '$start_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                         AND member_fds_scheme.status != 'Matured'
                         AND member_fds_scheme.status != 'Renewed',
                         member_fds_scheme.status, 'Other'
                     ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
            ->where('member_fds_scheme.memberType', 'NonMember')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                     (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                     AND (
                         member_fds_scheme.openingDate <= '$end_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                     )
                 )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                 AND member_fds_scheme.openingDate > '$end_date'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Query for Staff type
        $staffData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                         member_fds_scheme.openingDate >= '$start_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                         AND member_fds_scheme.status != 'Matured'
                         AND member_fds_scheme.status != 'Renewed',
                         member_fds_scheme.status, 'Other'
                     ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
            ->where('member_fds_scheme.memberType', 'Staff')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                     (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                     AND (
                         member_fds_scheme.openingDate <= '$end_date'
                         AND (member_fds_scheme.actualMaturityDate <= '$end_date' OR member_fds_scheme.actualMaturityDate IS NULL)
                     )
                 )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                 AND member_fds_scheme.openingDate > '$end_date'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Combine all results
        $data = $memberData->merge($nonMemberData)->merge($staffData);

        return $data;
    }

    //________Current Year Bank Daily Deposit Interest Payables
    private function CurrentDailyDepositPayable($end_date)
    {

        $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

        $data['memberType'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
                // 'daily_collectionsavings.sch_id'
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'Member')
            ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
            // ->whereIn('daily_collectionsavings.sch_id',$qq)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
                // 'daily_collectionsavings.sch_id'
            )
            ->get();


        $data['nonmemberType'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'NonMember')
            ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
            )
            ->get();



        $data['Staff'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'Staff')
            ->where('daily_collectionsavings.receipt_date', '<=', $end_date)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
            )
            ->get();

        return $data;
    }


    private function CurrentRdInterestPayable($start_date, $end_date)
    {

        $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

        $data['memberType'] = DB::table('re_curring_rds')
            ->selectRaw("
                re_curring_rds.rd_account_no,
                re_curring_rds.interest,
                re_curring_rds.month,
                re_curring_rds.date,
                re_curring_rds.secheme_id,
                member_accounts.accountNo,
                member_accounts.name,
                member_accounts.memberType as amtp,
                rd_receiptdetails.rc_account_no as rcac,
                rd_receiptdetails.memberType as rc_member_type,
                scheme_masters.id as schid,
                scheme_masters.name as schname,
                scheme_masters.secheme_type,
                  SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
            IF(
               DATE(re_curring_rds.date) <= ?
               AND (DATE(re_curring_rds.actual_maturity_date) >= ? OR re_curring_rds.actual_maturity_date IS NULL)
               AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
               'Active',
               re_curring_rds.status
           ) AS current_status
       ", [$end_date, $start_date, $end_date])








            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($end_date) {
                $query->where(function ($q) use ($end_date) {
                    $q->where('re_curring_rds.date', '<=', $end_date)
                        ->where(function ($q2) use ($end_date) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($end_date) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $end_date)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'Member')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',
            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();


        $data['nonmemberType'] = DB::table('re_curring_rds')
            ->selectRaw("
                re_curring_rds.rd_account_no,
                re_curring_rds.interest,
                re_curring_rds.month,
                re_curring_rds.date,
                member_accounts.accountNo,
                member_accounts.name,
                member_accounts.memberType as amtp,
                rd_receiptdetails.rc_account_no as rcac,
                rd_receiptdetails.memberType as rc_member_type,
                re_curring_rds.secheme_id,
                scheme_masters.id as schid,
                scheme_masters.name as schname,
                scheme_masters.secheme_type,
                   SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
            IF(
               DATE(re_curring_rds.date) <= ?
               AND (DATE(re_curring_rds.actual_maturity_date) >= ? OR re_curring_rds.actual_maturity_date IS NULL)
               AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
               'Active',
               re_curring_rds.status
           ) AS current_status
       ", [$end_date, $start_date, $end_date])

            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($end_date) {
                $query->where(function ($q) use ($end_date) {
                    $q->where('re_curring_rds.date', '<=', $end_date)
                        ->where(function ($q2) use ($end_date) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($end_date) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $end_date)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'NonMember')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',
            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();


        $data['Staff'] = DB::table('re_curring_rds')
            ->selectRaw("
                    re_curring_rds.rd_account_no,
                    re_curring_rds.interest,
                    re_curring_rds.month,
                    re_curring_rds.date,
                    member_accounts.accountNo,
                    member_accounts.name,
                    member_accounts.memberType as amtp,
                    rd_receiptdetails.rc_account_no as rcac,
                    rd_receiptdetails.memberType as rc_member_type,
                    re_curring_rds.secheme_id,
                    scheme_masters.id as schid,
                    scheme_masters.name as schname,
                    scheme_masters.secheme_type,
                       SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
            IF(
               DATE(re_curring_rds.date) <= ?
               AND (DATE(re_curring_rds.actual_maturity_date) >= ? OR re_curring_rds.actual_maturity_date IS NULL)
               AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
               'Active',
               re_curring_rds.status
           ) AS current_status
       ", [$end_date, $start_date, $end_date])

            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($end_date) {
                $query->where(function ($q) use ($end_date) {
                    $q->where('re_curring_rds.date', '<=', $end_date)
                        ->where(function ($q2) use ($end_date) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $end_date);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($end_date) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $end_date)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($end_date)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'Staff')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',

            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$end_date])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();

        return $data;
    }


    //______________________________________Last Year LBS Recoverables/Payables Details____________________________________


    //________Current Year Bank RD Interest Payables
    private function LbsCurrentRdInterestPayable($lastYearStartDate, $lastYearEndDate)
    {

        $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

        $data['memberType'] = DB::table('re_curring_rds')
            ->selectRaw("
                        re_curring_rds.rd_account_no,
                        re_curring_rds.interest,
                        re_curring_rds.month,
                        re_curring_rds.date,
                        member_accounts.accountNo,
                        member_accounts.name,
                        member_accounts.memberType as amtp,
                        rd_receiptdetails.rc_account_no as rcac,
                        rd_receiptdetails.memberType as rc_member_type,
                        re_curring_rds.secheme_id,
                        scheme_masters.id as schid,
                        scheme_masters.name as schname,
                        scheme_masters.secheme_type,
                        SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
                        IF(
                            re_curring_rds.date >= ?
                            AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
                            AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
                            'Active',
                            re_curring_rds.status
                        ) AS current_status
                    ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($lastYearEndDate) {
                $query->where(function ($q) use ($lastYearEndDate) {
                    $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
                        ->where(function ($q2) use ($lastYearEndDate) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($lastYearEndDate) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $lastYearEndDate)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'Member')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',
            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();


        $data['nonmemberType'] = DB::table('re_curring_rds')
            ->selectRaw("
                         re_curring_rds.rd_account_no,
                        re_curring_rds.interest,
                        re_curring_rds.month,
                        re_curring_rds.date,
                        member_accounts.accountNo,
                        member_accounts.name,
                        member_accounts.memberType as amtp,
                        rd_receiptdetails.rc_account_no as rcac,
                        rd_receiptdetails.memberType as rc_member_type,
                        re_curring_rds.secheme_id,
                        scheme_masters.id as schid,
                        scheme_masters.name as schname,
                        scheme_masters.secheme_type,
                        SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
                            IF(
                                re_curring_rds.date >= ?
                                AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
                                AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
                                'Active',
                                re_curring_rds.status
                            ) AS current_status
                        ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($lastYearEndDate) {
                $query->where(function ($q) use ($lastYearEndDate) {
                    $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
                        ->where(function ($q2) use ($lastYearEndDate) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($lastYearEndDate) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $lastYearEndDate)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'NonMember')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',
            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();


        $data['Staff'] = DB::table('re_curring_rds')
            ->selectRaw("
                            re_curring_rds.rd_account_no,
                        re_curring_rds.interest,
                        re_curring_rds.month,
                        re_curring_rds.date,
                        member_accounts.accountNo,
                        member_accounts.name,
                        member_accounts.memberType as amtp,
                        rd_receiptdetails.rc_account_no as rcac,
                        rd_receiptdetails.memberType as rc_member_type,
                        re_curring_rds.secheme_id,
                        scheme_masters.id as schid,
                        scheme_masters.name as schname,
                        scheme_masters.secheme_type,
                            SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
                            IF(
                                re_curring_rds.date >= ?
                                AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
                                AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
                                'Active',
                                re_curring_rds.status
                            ) AS current_status
                            ", [$lastYearEndDate, $lastYearStartDate, $lastYearEndDate])
            ->leftJoin('member_accounts', function (JoinClause $join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                    ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function (JoinClause  $join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('scheme_masters', function (JoinClause  $join) {
                $join->on('scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->on('scheme_masters.memberType', '=', 're_curring_rds.memberType');
                // ->on('scheme_masters.secheme_type', '=', 'RD');
            })
            ->where(function ($query) use ($lastYearEndDate) {
                $query->where(function ($q) use ($lastYearEndDate) {
                    $q->where('re_curring_rds.date', '<=', $lastYearEndDate)
                        ->where(function ($q2) use ($lastYearEndDate) {
                            $q2->whereNull('re_curring_rds.actual_maturity_date')
                                ->orWhere('re_curring_rds.actual_maturity_date', '>=', $lastYearEndDate);
                        })
                        ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                    ->orWhere(function ($q) use ($lastYearEndDate) {
                        $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                            ->where('re_curring_rds.date', '<=', $lastYearEndDate)
                            ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($lastYearEndDate)->subDay()->format('Y-m-d'));
                    });
            })
            ->where('re_curring_rds.memberType', 'Staff')
            ->groupBy(
                're_curring_rds.rd_account_no',
                're_curring_rds.interest',
                're_curring_rds.month',
                're_curring_rds.date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'member_accounts.memberType',
                'rd_receiptdetails.rc_account_no',
                'rd_receiptdetails.memberType',
                're_curring_rds.actual_maturity_date',
                're_curring_rds.status',
                're_curring_rds.secheme_id',
                'scheme_masters.id',
                'scheme_masters.name',
                'scheme_masters.secheme_type',
            )
            ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$lastYearEndDate])
            ->orderBy('re_curring_rds.date', 'ASC')
            ->get();

        return $data;
    }


    //________Current Year Bank Fd Interest Payables
    private function LbsCurrentFdInterestPayable($lastYearStartDate, $lastYearEndDate)
    {

        $depositeTypesId = DB::table('fd_type_master')->orderBy('id', 'ASC')->pluck('id');

        // Query for Member type
        $memberData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                        member_fds_scheme.openingDate >= '$lastYearStartDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                        AND member_fds_scheme.status != 'Matured'
                        AND member_fds_scheme.status != 'Renewed',
                        member_fds_scheme.status, 'Other'
                    ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
            ->where('member_fds_scheme.memberType', 'Member')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                    (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                    AND (
                        member_fds_scheme.openingDate <= '$lastYearEndDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                    )
                )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                AND member_fds_scheme.openingDate > '$lastYearEndDate'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Query for NonMember type
        $nonMemberData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                        member_fds_scheme.openingDate >= '$lastYearStartDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                        AND member_fds_scheme.status != 'Matured'
                        AND member_fds_scheme.status != 'Renewed',
                        member_fds_scheme.status, 'Other'
                    ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
            ->where('member_fds_scheme.memberType', 'NonMember')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                    (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                    AND (
                        member_fds_scheme.openingDate <= '$lastYearEndDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                    )
                )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                AND member_fds_scheme.openingDate > '$lastYearEndDate'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Query for Staff type
        $staffData = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'fd_type_master.id as typeids',
                'fd_type_master.type as fdname',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                        member_fds_scheme.openingDate >= '$lastYearStartDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                        AND member_fds_scheme.status != 'Matured'
                        AND member_fds_scheme.status != 'Renewed',
                        member_fds_scheme.status, 'Other'
                    ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->leftJoin('fd_type_master', 'fd_type_master.id', '=', 'member_fds_scheme.fdType')
            ->whereDate('member_fds_scheme.openingDate', '<=', $lastYearEndDate)
            ->where('member_fds_scheme.memberType', 'Staff')
            ->whereIn('member_fds_scheme.fdType', $depositeTypesId)
            ->whereRaw(
                "NOT (
                    (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                    AND (
                        member_fds_scheme.openingDate <= '$lastYearEndDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$lastYearEndDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                    )
                )"
            )
            ->orWhereRaw(
                "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                AND member_fds_scheme.openingDate > '$lastYearEndDate'"
            )
            ->orderBy('member_fds_scheme.openingDate', 'ASC')
            ->get();

        // Combine all results
        $data = $memberData->merge($nonMemberData)->merge($staffData);

        return $data;
    }

    //________Current Year Bank Daily Deposit Interest Payables
    private function LbsCurrentDailyDepositPayable($lastYearEndDate)
    {

        $qq = DB::table('scheme_masters')->where('secheme_type', 'RD')->orderBy('id', 'ASC')->pluck('id');

        $data['memberType'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
                // 'daily_collectionsavings.sch_id'
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'Member')
            ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
            // ->whereIn('daily_collectionsavings.sch_id',$qq)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
                // 'daily_collectionsavings.sch_id'
            )
            ->get();


        $data['nonmemberType'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'NonMember')
            ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
            )
            ->get();



        $data['Staff'] = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid',
                DB::raw('SUM(deposit) AS total_amount'),
                DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo as anumber',
                'member_accounts.name',
                'scheme_masters.id as schid',
                'daily_collectionsavings.memberType',
                'scheme_masters.name as schname',
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collectionsavings.sch_id')
            ->where('daily_collectionsavings.memberType', 'Staff')
            ->where('daily_collectionsavings.receipt_date', '<=', $lastYearEndDate)
            ->groupBy(
                'dailyaccountid',
                'daily_collections.id',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.opening_date',
                'member_accounts.accountNo',
                'member_accounts.name',
                'scheme_masters.id',
                'daily_collectionsavings.memberType',
                'scheme_masters.name',
            )
            ->get();

        return $data;
    }

    //_______Current Year Loan Intt. Recoverable
    private function LbsCurrentYearLoanInttRecoverable($lastYearStartDate, $lastYearEndDate)
    {
        //_______Loan Interest Recoverables
        $recoverableAmountTotal = 0;
        $loanmasters = MemberLoan::where('is_delete', '!=', 'Yes')
            ->where('status', 'Disbursed')
            // ->whereDate('loanDate', '>=',$lastYearStartDate)
            ->whereDate('loanDate', '<=', $lastYearEndDate)
            ->get();

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
                $recoverableAmountTotal += round($calculateformula, 2);
            }
        }
        return $recoverableAmountTotal;
    }

    //________Current Year Bank Fd Interest Recoverables
    private function LbsbankfdInterestRecoverable($lastYearEndDate)
    {
        // $bankInterestRecoverable = DB::table('bank_fd_deposit')
        //     ->select('bank_fd_deposit.*', 'bank_fd_masters.id as bankId', 'bank_fd_masters.bank_name', 'bank_fd_masters.ledgerCode')
        //     ->leftJoin('bank_fd_masters', 'bank_fd_masters.id', '=', 'bank_fd_deposit.bank_fd_type')
        //     ->whereDate('bank_fd_deposit.fd_date', '<=', $lastYearEndDate)
        //     ->where('bank_fd_deposit.status', 'Active')
        //     ->get();

        // return $bankInterestRecoverable;
    }
}
