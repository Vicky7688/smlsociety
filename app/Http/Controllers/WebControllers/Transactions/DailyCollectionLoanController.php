<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchemeMaster;
use App\Models\MemberAccount;
use App\Models\MemberSaving;
use App\Models\DailyCollectionLoan;
use App\Models\LedgerMaster;
use App\Models\GeneralLedger;
use App\Models\DailyCollectionRecovery;
use App\Models\opening_accounts;
use App\Models\AgentMaster; 
use App\Models\LoanInstallmentDaily; 
use App\Models\GroupMaster; 

use Illuminate\Support\Facades\DB; // Import DB facade for transactions
use DateTime;
use Carbon\Carbon; 

class DailyCollectionLoanController extends Controller
{
    public function index(){  
        $schemes = SchemeMaster::where('secheme_type','Daily Loan')->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $groups = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        return view('transaction.dailycollectionloan',['scheme'=>$schemes,'agents' => $agents, 'groups' => $groups]);  
    } 
   
    public function getaccountsdetails(Request $request)
    { 
        $memberType = $request->member;
        $accountNo = $request->accountno;
        $output = '';
        if(empty($accountNo)) {
            $output .= '<li class="list-group-item memberlist"></li>';
            return response()->json([
                'status' => true,
                'data' => $output
            ]);
        }
        $data = opening_accounts::where('membertype', $memberType)
        ->where('accountNo', 'LIKE', $accountNo . '%')
        ->where('accountname','Daily Loan')
        ->orderBy('accountNo')
        ->get(); 

        if (count($data) > 0) {
             $output = '<ul class="list-group membersearch" style="display:block;z-indez:1">';
            foreach ($data as $row) {
             $output .= '<li class="list-group memberlist">' . $row->accountNo . '</li>';
            }
             $output .= '</ul>';
        } else {
             $output .= '<li class="list-group-item memberlist">No Data Found</li>';
        }
        return response()->json([
            'status' => true,
            'data' => $output
        ]);
    } 
 
    public function getaccountsdetailselected(Request $request)
{
    $accountNo = $request->accountno;
    $memberType = $request->membertype;

    if (!empty($accountNo)) {
        $membershipno = opening_accounts::where('accountNo', $accountNo)
            ->where('membertype', $memberType)
            ->where('accountname', 'Daily Loan')
            ->value('membershipno');

        $member = MemberAccount::where(['accountNo' => $membershipno, 'memberType' => $memberType])->first();

        if ($member) {
            if ($member->status == "Active") {
                $name = $member->name;
                $fatherhusband = $member->fatherName;
                $address = $member->address;
            } elseif ($member->status == "Transfer") {
                $Transfer_account = TransferedAccount::where(['accountId' => $member->id])->first();
                $name = $Transfer_account->name;
                $fatherhusband = $Transfer_account->fatherName;
                $address = $Transfer_account->address;
            }

            $accountDetails = opening_accounts::where('opening_accounts.membertype', $memberType)
                ->where('opening_accounts.accountNo', $accountNo)
                ->where('opening_accounts.accountname', 'Daily Loan')
                ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->select(
                    'opening_accounts.*',
                    'member_accounts.name',
                    'scheme_masters.id as scheme_id',
                    'scheme_masters.name as scheme_name',
                    'scheme_masters.scheme_code',
                    'scheme_masters.durationType',
                    'scheme_masters.days',
                    'scheme_masters.months',
                    'scheme_masters.years',
                    'scheme_masters.interest',
                    'scheme_masters.penaltyInterest',
                    'scheme_masters.secheme_type',
                    'scheme_masters.status as scheme_status',
                    'scheme_masters.lockin_days',
                    'scheme_masters.renewInterestType'
                )->first();

            $rd_details = DailyCollectionLoan::where(['accountId' => $member->id, 'membertype' => $memberType])->get();
            $recoveryDetails = DailyCollectionRecovery::where('dailyaccountid', $member->id)
                ->where('is_delete', 'No')
                ->selectRaw('sum(recovery) as TotalRecovery, sum(withdrow) as TotalWithdrow, sum(penalty) as TotalPenalty')
                ->first();

            if (count($rd_details) > 0) {
                $dailycollectiondata = [];
                foreach ($rd_details as $key => $rd) {
                    $totalRecovery = $recoveryDetails->TotalRecovery ?? 0;
                    $totalWithdrow = $recoveryDetails->TotalWithdrow ?? 0;
                    $totalPenalty = $recoveryDetails->TotalPenalty ?? 0;

                    $dailycollectiondata[$key] = [
                        'id' => $rd->id,
                        'date' => $rd->date,
                        'daily_loan_accno' => $rd->daily_loan_accno,
                        'amount' => $rd->amount,
                        'schemename' => $rd->schemename,
                        'pending_amount' => max(0, $rd->amount - $totalRecovery),
                        'excess_amount' => max(0, $totalRecovery - $rd->amount),
                        'received_amount' => $totalRecovery,
                        'withdraw_amount' => $totalWithdrow,
                        'penalty_amount' => $totalPenalty,
                        'current_amount' => $totalRecovery - ($totalWithdrow + $totalPenalty),
                        'interest' => $rd->interest,
                        'month' => $rd->month,
                        'days' => $rd->days,
                        'collectiontype' => $rd->collectiontype,
                        'status' => $rd->status,
                    ];
                }

                return response()->json([
                    'status' => 'success',
                    'name' => $name,
                    'fathername' => $fatherhusband,
                    'address' => $address,
                    'account_details' => $accountDetails,
                    'tabledata' => $dailycollectiondata
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'name' => $name,
                    'fathername' => $fatherhusband,
                    'address' => $address,
                    'account_details' => $accountDetails,
                    'tabledata' => []
                ]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'No details found']);
        }
    } else {
        return response()->json(['status' => 'error', 'message' => 'Account number is empty']);
    }
}
  
public function storedailycollectionaccount(Request $request) {
    DB::beginTransaction(); // Start the transaction
    try { 
        $dailycollectionrand = "DC" . rand(1111111, 9999999);  
        $account = MemberAccount::where(['accountNo' => $request->account_no, 'memberType' => $request->member_type])->first();
        if (!$account) {
            return response()->json(['status' => 'fail', 'message' => 'Account not found.']);
        } 
        // Check if the daily collection account already exists
        $existingDailyCollection = DailyCollectionLoan::where('daily_loan_accno', $request->daily_ac_no)
            ->where('accountNo', $account->accountNo)
            ->first(); 
        if ($existingDailyCollection) {
            return response()->json(['status' => 'fail', 'message' => 'Daily Collection A/C already exists for this account number.']);
        } 
        $schemes = SchemeMaster::where(['id' => $request->scheme_type])->first(); 
        $lockindate = date('Y-m-d', strtotime($request->lock_in_date));
        $maturitydate = date('Y-m-d', strtotime($request->maturity_date));

     


        // Create the daily collection loan entry
        $datastore = new DailyCollectionLoan;
        $datastore->serialno = $dailycollectionrand;
        $datastore->membertype = $request->member_type;
        $datastore->accountid = $account->id;
        $datastore->accountNo = $account->accountNo;
        $datastore->daily_loan_accno = $request->daily_ac_no;
        $datastore->date =  date('Y-m-d', strtotime($request->date_dc));
        $datastore->maturitydate = $maturitydate; 
        $datastore->schemename = $schemes->name;
        $datastore->schemeid = $schemes->id;
        $datastore->lockindays = $request->lock_in_days;
        $datastore->lockindate = $lockindate;
        $datastore->amount = $request->amount_daily_collection;
        $datastore->interest = $request->interest_value;
        $datastore->interest_amount = $request->interest_maturity_amount;
        $datastore->month = $request->days_value;
        // $datastore->collectiontype = $request->type;
        $datastore->maturityamount = $request->maturity_amount;
        $datastore->principalamount = $request->total_principal_amount;
        $datastore->actualMaturitydate = $maturitydate;
        $datastore->ActualyMaturityAmount = $request->maturity_amount;
        $datastore->agent_name = $request->agentId;
        $datastore->branchId = session('branchId') ? session('branchId') : 1;
        $datastore->sessionId = session('sessionId') ? session('sessionId') : 1;
        $datastore->updatedBy = auth()->user()->id;
        $datastore->save();

        // Validate installments_count before proceeding
        $installmentsCount = $request->days_value;
        if ($installmentsCount <= 0) {
            throw new \Exception("Installments count must be greater than zero.");
        }

        $totalAmount = $request->total_principal_amount;
        $interest = $request->interest_maturity_amount;
        $installmentAmount = $totalAmount / $installmentsCount;
        $installmentInterest = $interest / $installmentsCount;

        // Loop for creating loan installments
        for ($i = 1; $i <= $installmentsCount; $i++) {
            $installmentDate = date('Y-m-d', strtotime("+$i month", strtotime($request->date_dc))); 
            $LoanInstallmentDaily = new LoanInstallmentDaily();
            $LoanInstallmentDaily->LoanId = $datastore->id;
            $LoanInstallmentDaily->installmentDate = $installmentDate;
            $LoanInstallmentDaily->principal = $installmentAmount;
            $LoanInstallmentDaily->interest = $installmentInterest;
            $LoanInstallmentDaily->total = $installmentAmount + $installmentInterest;
            $LoanInstallmentDaily->status = 'Pending';
            $LoanInstallmentDaily->created_at = now();
            $LoanInstallmentDaily->updated_at = now();
            $LoanInstallmentDaily->save();
        }

        $serialNo  =    "DDL" . time();  
        $ledger_master =  DB::table('ledger_masters')->where('reference_id', $schemes->id)->where('scheme_code', $schemes->scheme_code)->first(); 
 
        $ledger = new GeneralLedger();
        $ledger->serialNo = $serialNo;
        $ledger->accountId =   $datastore->id;
        $ledger->accountNo = $datastore->daily_loan_accno;
        $ledger->memberType =  $datastore->membertype;
        $ledger->formName = 'DailyDepositLoan';
        $ledger->referenceNo = $datastore->id;
        $ledger->entryMode = 'manual';
        $ledger->transactionDate = $datastore->date;
        $ledger->transactionType = 'Dr';
        $ledger->transactionAmount =  $datastore->principalamount;
        $ledger->groupCode = $ledger_master->groupCode;
        $ledger->ledgerCode =  $ledger_master->ledgerCode;
        $ledger->branchId = session('branchId') ? session('branchId') : 1;
        $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
        $ledger->save(); 

        $ledger = new GeneralLedger();
        $ledger->serialNo = $serialNo;
        $ledger->accountId =   $datastore->id;
        $ledger->accountNo = $datastore->daily_loan_accno;
        $ledger->memberType =  $datastore->membertype;
        $ledger->formName = 'DailyDepositLoan';
        $ledger->referenceNo = $datastore->id;
        $ledger->entryMode = 'manual';
        $ledger->transactionDate = $datastore->date;
        $ledger->transactionType = 'Cr';
        $ledger->transactionAmount =  $datastore->principalamount;
        $ledger->groupCode =  $request->paymentType;
        $ledger->ledgerCode =  $request->bank;
        $ledger->branchId = session('branchId') ? session('branchId') : 1;
        $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
        $ledger->save();

        DB::commit(); 

        return response()->json(['status' => 'success', 'message' => 'Daily Collection A/C Created and Installments Scheduled Successfully!']);

    } catch (\Exception $e) {
        DB::rollBack(); // Roll back the transaction in case of error
        return response()->json(['status' => 'fail', 'message' => 'An error occurred while creating the account: ' . $e->getMessage()]);
    }
}
 


public function geteditdetails(Request $request){
    $id = $request->id;
    $query = DailyCollectionLoan::where(['id'=>$id])->first();
    return response()->json(['status'=>'success','data'=>$query]);
}
   

public function updatemodification(Request $request) {
    DB::beginTransaction();  
    try {
        // dd($request->all());
        $id = $request->updateid;

        $datastore = DailyCollectionLoan::find($id);
        if (!$datastore) {
            return response()->json(['status' => 'fail', 'message' => 'Daily Collection Account not found.']);
        } 
        $account = MemberAccount::where(['accountNo' => $request->account_no, 'memberType' => $request->member_type])->first();
        if (!$account) {
            return response()->json(['status' => 'fail', 'message' => 'Account not found.']);
        }
        $schemes = SchemeMaster::where(['id' => $request->scheme_type])->first();
        if (!$schemes) {
            return response()->json(['status' => 'fail', 'message' => 'Scheme not found.']);
        }

        $lockindate = date('Y-m-d', strtotime($request->lock_in_date));
        $maturitydate = date('Y-m-d', strtotime($request->maturity_date));

        // Update daily collection loan entry
        $datastore->membertype = $request->member_type;
        $datastore->accountid = $account->id;
        $datastore->accountNo = $account->accountNo;
        $datastore->daily_loan_accno = $request->daily_ac_no;
        $datastore->date = date('Y-m-d', strtotime($request->date_dc));
        $datastore->maturitydate = $maturitydate;
        $datastore->schemename = $schemes->name;
        $datastore->schemeid = $schemes->id;
        $datastore->lockindays = $request->lock_in_days;
        $datastore->lockindate = $lockindate;
        $datastore->amount = $request->amount_daily_collection;
        $datastore->interest = $request->interest_value;
        $datastore->interest_amount = $request->interest_maturity_amount;
        $datastore->month = $request->days_value;
        $datastore->maturityamount = $request->maturity_amount;
        $datastore->principalamount = $request->total_principal_amount;
        $datastore->actualMaturitydate = $maturitydate;
        $datastore->ActualyMaturityAmount = $request->maturity_amount;
        $datastore->agent_name = $request->agentId;
        $datastore->branchId = session('branchId') ? session('branchId') : 1;
        $datastore->sessionId = session('sessionId') ? session('sessionId') : 1;
        $datastore->updatedBy = auth()->user()->id;
        $datastore->save();

        // Validate installments_count
        $installmentsCount = $request->days_value;
        if ($installmentsCount <= 0) {
            throw new \Exception("Installments count must be greater than zero.");
        }

        // Update installment entries if needed
        LoanInstallmentDaily::where('LoanId', $datastore->id)->delete();
        $totalAmount = $request->total_principal_amount;
        $interest = $request->interest_maturity_amount;
        $installmentAmount = $totalAmount / $installmentsCount;
        $installmentInterest = $interest / $installmentsCount;

        for ($i = 1; $i <= $installmentsCount; $i++) {
            $installmentDate = date('Y-m-d', strtotime("+$i month", strtotime($request->date_dc)));
            $LoanInstallmentDaily = new LoanInstallmentDaily();
            $LoanInstallmentDaily->LoanId = $datastore->id;
            $LoanInstallmentDaily->installmentDate = $installmentDate;
            $LoanInstallmentDaily->principal = $installmentAmount;
            $LoanInstallmentDaily->interest = $installmentInterest;
            $LoanInstallmentDaily->total = $installmentAmount + $installmentInterest;
            $LoanInstallmentDaily->status = 'Pending';
            $LoanInstallmentDaily->created_at = now();
            $LoanInstallmentDaily->updated_at = now();
            $LoanInstallmentDaily->save();
        }

        // Update ledger entries
        GeneralLedger::where('accountId', $datastore->id)->delete();
        $serialNo  = "DDL" . time();
        $ledger_master = DB::table('ledger_masters')->where('reference_id', $schemes->id)->where('scheme_code', $schemes->scheme_code)->first();

        // Debit ledger entry
        $ledger = new GeneralLedger();
        $ledger->serialNo = $serialNo;
        $ledger->accountId = $datastore->id;
        $ledger->accountNo = $datastore->daily_loan_accno;
        $ledger->memberType = $datastore->membertype;
        $ledger->formName = 'DailyDepositLoan';
        $ledger->referenceNo = $datastore->id;
        $ledger->entryMode = 'manual';
        $ledger->transactionDate = $datastore->date;
        $ledger->transactionType = 'Dr';
        $ledger->transactionAmount = $datastore->principalamount;
        $ledger->groupCode = $ledger_master->groupCode;
        $ledger->ledgerCode = $ledger_master->ledgerCode;
        $ledger->branchId = session('branchId') ? session('branchId') : 1;
        $ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
        $ledger->save();

        // Credit ledger entry
        $ledger = new GeneralLedger();
        $ledger->serialNo = $serialNo;
        $ledger->accountId = $datastore->id;
        $ledger->accountNo = $datastore->daily_loan_accno;
        $ledger->memberType = $datastore->membertype;
        $ledger->formName = 'DailyDepositLoan';
        $ledger->referenceNo = $datastore->id;
        $ledger->entryMode = 'manual';
        $ledger->transactionDate = $datastore->date;
        $ledger->transactionType = 'Cr';
        $ledger->transactionAmount = $datastore->principalamount;
        $ledger->groupCode = $request->paymentType;
        $ledger->ledgerCode = $request->bank;
        $ledger->branchId = session('branchId') ? session('branchId') : 1;
        $ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
        $ledger->save();

        DB::commit();

        return response()->json(['status' => 'success', 'message' => 'Daily Collection A/C updated successfully!']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'fail', 'message' => 'An error occurred while updating the account: ' . $e->getMessage()]);
    }
}








    

}
