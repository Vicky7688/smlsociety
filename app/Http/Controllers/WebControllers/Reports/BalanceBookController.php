<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\Mis;
use App\Models\ReCurringRd;
use App\Models\MemberSaving;
use App\Models\GeneralLedger;
use App\Models\MemberLoan;
use App\Models\MemberShare;
use App\Models\LoanInstallment;
use App\Models\LoanRecovery;
use App\Models\BranchMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DateTime;

class BalanceBookController extends Controller
{
    public function balancebookindex(){
        $branch = BranchMaster::first();
        return view("report.balancebook", compact('branch'));
    }

    public function balancebookgetdata(Request $post)
    {
        $duedate = date('Y-m-d', strtotime($post->endDate));
        $memberType = $post->memberType;
        $loantype = $post->loanType;
        $loandatatype = ($loantype == "WithoutloanAgainstfd") ? 0 : 1;

        $account_members = DB::table('member_accounts')
            ->where('memberType', $memberType)
            ->where('is_delete', '=', 'No')
            ->get();

        $data = [];
        $totalInterestRecover = 0;

        // Precompute share and savings balances for each account
        $accountDetails = [];
        foreach ($account_members as $accounts) {
            $shareBal = $accounts->share ?? 0;
            $shareBalSaving = $accounts->saving ?? 0;

            // Calculate share balances
            $shareDeposit = DB::table('member_shares')
                ->where('memberType', $memberType)
                ->where('accountNo', $accounts->accountNo)
               ->where('is_delete', '=', 'No')
               ->whereDate('transactionDate','<=',$duedate)
                // ->where('transactionType', 'Deposit')
                ->sum('depositAmount');

            $shareWithdraw = DB::table('member_shares')
                ->where('memberType', $memberType)
                ->where('accountNo', $accounts->accountNo)
               ->where('is_delete', '=', 'No')
               ->whereDate('transactionDate','<=',$duedate)
                // ->where('transactionType', 'Withdraw')
                ->sum('withdrawAmount');

            $totalShareAmount = $shareBal + $shareDeposit - $shareWithdraw;

            // Calculate savings balances
            $contributionDeposit = DB::table('contributions')
                ->where('memberType', $memberType)
                ->where('accountNo', $accounts->accountNo)
                // ->where('transactionType', 'Deposit')
                ->where('transactionDate', '<=', $duedate)
               ->where('is_delete', '=', 'No')
                ->sum('depositAmount');

            $contributionWithdraw = DB::table('contributions')
                ->where('memberType', $memberType)
                ->where('accountNo', $accounts->accountNo)
                // ->where('transactionType', 'Withdraw')
                ->where('transactionDate', '<=', $duedate)
                ->where('is_delete', '!=', 'Yes')
                ->sum('withdrawAmount');

            $totalContributionAmount = $shareBalSaving + $contributionDeposit - $contributionWithdraw;

            $accountDetails[$accounts->accountNo] = [
                'Share' => $totalShareAmount,
                'Contribution' => $totalContributionAmount,
                'MemberName' => $accounts->name,
                'Used' => false, // Track usage of balances
            ];
        }

        foreach ($account_members as $srno => $accounts) {
            $loans = ($loandatatype == 0)
                ? DB::table('member_loans')
                    ->where('member_loans.accountNo', $accounts->accountNo)
                    ->where('member_loans.memberType', $memberType)
                    ->where('member_loans.is_delete', '=', 'No')
                    ->where('member_loans.status', 'Disbursed')
                    ->where('loanDate', '<=', $duedate)
                    ->get()
                : DB::table('member_loans')
                    ->where('member_loans.accountNo', $accounts->accountNo)
                    ->where('member_loans.memberType', $memberType)
                    ->where('member_loans.is_delete', '=', 'No')
                    // ->where('fdId', '!=', '')
                    ->where('member_loans.status', 'Disbursed')
                    ->get();

            if ($loans->isEmpty()) {
                $data[] = [
                    'SNo' => $srno + 1,
                    'AccountNo' => $accounts->accountNo,
                    'MemberName' => $accountDetails[$accounts->accountNo]['MemberName'] ?? 0,
                    'Share' => !$accountDetails[$accounts->accountNo]['Used'] ? $accountDetails[$accounts->accountNo]['Share'] : 0,
                    'Contribution' => !$accountDetails[$accounts->accountNo]['Used'] ? $accountDetails[$accounts->accountNo]['Contribution'] : 0,
                    'LoanDate' => 0,
                    'LoanAmount' => 0,
                    'LoanBalance' => 0,
                    'FDNo' => 0,
                    'LastInstDate' => 0,
                    'InterestRecoverable' => 0,
                ];
                $accountDetails[$accounts->accountNo]['Used'] = true;
            } else {
                foreach ($loans as $loan) {
                    $loanRecovery = DB::table('loan_recoveries')
                        ->where('loanId', $loan->id)
                        ->where('receiptDate', '<=', $duedate)
                        ->where('is_delete', '=', 'No')
                        ->sum('principal');

                    $recoveryDate = DB::table('loan_recoveries')
                        ->where('loanId', $loan->id)
                        ->where('is_delete', '=', 'No')
                        ->orderBy('receiptDate', 'DESC')
                        ->whereDate('receiptDate', '<=', $duedate)
                        ->value('receiptDate');

                    $totalAmount = $loan->loanAmount - $loanRecovery;
                    $interestRecoverable = $this->CurrentYearLoanInttRecoverable($loan->id, $duedate);
                    $totalInterestRecover += $interestRecoverable;

                    $data[] = [
                        'SNo' => $srno + 1,
                        'AccountNo' => $loan->accountNo,
                        'MemberName' => $accountDetails[$loan->accountNo]['MemberName'] ?? 0,
                        'Share' => !$accountDetails[$loan->accountNo]['Used'] ? $accountDetails[$loan->accountNo]['Share'] : 0,
                        'Saving' => !$accountDetails[$loan->accountNo]['Used'] ? $accountDetails[$loan->accountNo]['Contribution'] : 0,
                        'LoanDate' => date('d-m-Y', strtotime($loan->loanDate)),
                        'LoanAmount' => $loan->loanAmount,
                        'LoanBalance' => $totalAmount,
                        'FDNo' => $loan->fdId ?? 0,
                        'LastInstDate' => $recoveryDate ? date('d-m-Y', strtotime($recoveryDate)) : 0,
                        'InterestRecoverable' => $interestRecoverable,
                    ];
                    $accountDetails[$loan->accountNo]['Used'] = true;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'allDetails' => $data,
        ]);
    }








    public function CurrentYearLoanInttRecoverable($loanid, $duedate)
    {
        //_______Loan Interest Recoverables
        $recoverableAmountTotal = 0;
        $loanmaster = DB::table('member_loans')->where('id',$loanid)->where('is_delete', 'No')->first();
        // if ($loanmasters->count() > 0) {
        //     foreach ($loanmasters as $loanmaster) {
        $loan_recovery = DB::table('loan_recoveries')->where('loanId', $loanmaster->id)
            ->where('is_delete', 'No')
            ->where('receiptDate', '<=', $duedate)
            ->sum('principal');


        $openingdate = new DateTime($loanmaster->loanDate);
        $currentdate = new DateTime($duedate);
        $interval = $openingdate->diff($currentdate);
        $totalDaysDifference = $interval->days + 1;
        $recoverableAmount = $loanmaster->loanAmount - $loan_recovery;
        $perdayinterest = $loanmaster->loanInterest / 365;
        $calculateformula = (($recoverableAmount * $totalDaysDifference) * $perdayinterest) / 100;
        //    $recoverableAmountTotal += $calculateformula;
        //     }
        // }
        return round($calculateformula, 2);
    }



    public function print(Request $request)
    {
        return view('report.balancebookPrint');
    }
}
