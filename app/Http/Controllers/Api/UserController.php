<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BranchMaster;
use App\Models\MemberSaving;
use App\Models\MemberAccount;
use App\Models\CompulsoryDeposit;
use App\Models\MemberLoan;
use App\Models\MemberShare;
use App\Models\Mis;
use App\Models\MemberFd;
use App\Models\ReCurringRd;
use App\Models\LoanRecovery;
use App\Models\dailyrcovery;
use App\Models\RdReceiptdetails;
use App\Models\Slider;


class UserController extends Controller
{
    // public function testt(Request $request)
    // {


    //     $dateofinstallment=$request->installdate;

    //     if($request->amount<=0){

    //     }
    //         $existloanid=MemberLoan::find($request->loanid);
    //         if(!$existloanid){

    //         }
    //         $totreco=dailyrcovery::where('accountId','=',$existloanid->accountId)->where('accountNo','=',$existloanid->accountNo)->where('memberType','=',$existloanid->memberType)->sum('transactionamount');

    //         if($existloanid->loanAmount<$totreco+$request->amount){


    //         }


    //             $srnnnn="daily".time();

    //             if($request->paytype=='C002'){
    //                     $groupCode=$request->paytype;
    //                     $ledgerCode=$request->paytype;
    //             }else{
    //                     $groupCode=$request->paytype;
    //                     $ledgerCode=$request->bank;
    //             }



    //                 $enter=new dailyrcovery();
    //                 $enter->serialNo=$srnnnn;
    //                 $enter->loanid=$request->loanid;
    //                 $enter->accountId=$existloanid->accountId;
    //                 $enter->accountNo=$existloanid->accountNo;
    //                 $enter->memberType=$existloanid->memberType;
    //                 $enter->groupCode=$groupCode;
    //                 $enter->ledgerCode=$ledgerCode;
    //                 $enter->recoverydate=date('Y-m-d', strtotime($dateofinstallment));
    //                 $enter->transactionamount=$request->amount;
    //                 $enter->penaltyamount=$request->panelty;
    //                 $enter->transfered='no';
    //                 $enter->save();






    //                 DB::table('general_ledgers')->insert([
    //                     "serialNo" => $srnnnn,
    //                     'accountNo' => $existloanid->accountNo,
    //                     "accountId"  =>  $existloanid->accountId,
    //                     'memberType' =>  $existloanid->memberType,
    //                     'agentId' =>1,
    //                     "ledgerCode"   => $ledgerCode,
    //                     'groupCode' =>  $groupCode,
    //                     'referenceNo' => $enter->id,
    //                     'entryMode' => "manual",
    //                     "formName" => "Dailyloanrecovery",
    //                     'transactionDate' =>date('Y-m-d', strtotime($dateofinstallment)),
    //                     'transactionType' => 'Dr',
    //                     'transactionAmount' => $request->amount,
    //                     'narration' => '',
    //                     'branchId' =>  session('branchid') ?? 1,
    //                     'sessionId' => session('sessionId') ?? 1,
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                     'updatedBy' => 1,
    //                 ]);



    //                 DB::table('general_ledgers')->insert([
    //                     "serialNo" => $srnnnn,
    //                     'accountNo' => $existloanid->accountNo,
    //                     "accountId"  =>  $existloanid->accountId,
    //                     'memberType' =>  $existloanid->memberType,
    //                     'agentId' => 1,
    //                     "groupCode"   => 'GRTDAI01',
    //                     'ledgerCode' =>  'DAI02',
    //                     'referenceNo' => $enter->id,
    //                     'entryMode' => "manual",
    //                     "formName" => "Dailyloanrecovery",
    //                     'transactionDate' =>date('Y-m-d', strtotime($dateofinstallment)),
    //                     'transactionType' => 'Cr',
    //                     'transactionAmount' => $request->amount,
    //                     'narration' => '',
    //                     'branchId' =>  session('branchid') ?? 1,
    //                     'sessionId' => session('sessionId') ?? 1,
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                     'updatedBy' => 1,
    //                 ]);









    // }





    public function login(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $memeber_type = $request->memeber_type;
        $account_number = $request->AccountNumber;
        $password = $request->AccountNumber;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $account = DB::table('member_accounts')
                ->where('memberType', $memeber_type)
                ->where('accountNo', $account_number)
                ->where('status', 'Active')
                ->first();

            if (!$account) {
                $data['msg'] = "Please check your account number!";
                $data['status'] = false;
                return response()->json($data);
            }

            $password_match = DB::table('opening_account_details')
                ->where('AccountNumber', $account_number)
                ->where('AccountNumber', $password)
                ->where('Status', 'Active')
                ->where('MemberType', $memeber_type)
                ->first();

            if (!$password_match) {
                $data['msg'] = "Invalid password!";
                $data['status'] = false;
                return response()->json($data);
            }

            $login_details = DB::table('opening_account_details')
                ->where('MemberType', $memeber_type)
                ->where('AccountNumber', $account_number)
                ->where('AccountNumber', $password)
                ->where('MemberType', $memeber_type)
                ->where('Status', 'Active')
                ->first();

            if ($login_details) {
                $data['status'] = true;
                $data['msg'] = "Login Successfully";

                $data['branch_name'] = $branch->name;
                $data['branch_type'] = $branch->type;
                $data['branch_code'] = $branch->branch_code;
                $data['MemberType'] = $login_details->MemberType;
                $data['AccountType'] = $login_details->AccountType;
                $data['GuardianAccountNumber'] = $login_details->GuardianAccountNumber;
                $data['AccountNumber'] = $login_details->AccountNumber;
                $data['Name'] = $login_details->Name;
                $data['NameHindi'] = $login_details->NameHindi;
                $data['dob'] = $login_details->dob;
                $data['ac_identity'] = $login_details->ac_identity;
                $data['FatherHusband'] = $login_details->FatherHusband;
                $data['Gender'] = $login_details->Gender;
                $data['Caste'] = $login_details->Caste;
                $data['AdmissionFee'] = $login_details->AdmissionFee;
                $data['AadharNumber'] = $login_details->AadharNumber;
                $data['PanNumber'] = $login_details->PanNumber;
                $data['ContactNumber'] = $login_details->ContactNumber;
                $data['WardNumber'] = $login_details->WardNumber;
                $data['Branch'] = $login_details->Branch;
                $data['MaritalStatus'] = $login_details->MaritalStatus;
                $data['Occupation'] = $login_details->Occupation;
                $data['NomineeName'] = $login_details->NomineeName;
                $data['NomineeRelation'] = $login_details->NomineeRelation;
                $data['NomineeAddress'] = $login_details->NomineeAddress;
                $data['NomineeContact'] = $login_details->NomineeContact;
                $data['LedgerFolioNumber'] = $login_details->LedgerFolioNumber;
                $data['PageNumber'] = $login_details->PageNumber;
                $data['SavingAccount'] = $login_details->SavingAccount;
                $data['Sharee'] = $login_details->Sharee;
                $data['ShareCredit'] = $login_details->ShareCredit;
                $data['Saving'] = $login_details->Saving;
                $data['SavingCredit'] = $login_details->SavingCredit;
                $data['CompulsoryDeposit'] = $login_details->CompulsoryDeposit;
                $data['DateOfBirth'] = $login_details->DateOfBirth;
                $data['Age'] = $login_details->Age;
                $data['Pic1'] = $login_details->Pic1;
                $data['Pic2'] = $login_details->Pic2;

                $path = "https://app.betabyte.in/new/Society-/api/login";
                if ($login_details->Pic1 != null) {
                    $data['image1'] = $path . $login_details->Pic1;
                } else {
                    $data['image1'] = "";
                }
                if ($login_details->Pic2 != null) {
                    $data['image2'] = $path . $login_details->Pic2;
                } else {
                    $data['image2'] = "";
                }

                $data['Pincode'] = $login_details->Pincode;
                $data['AgentName'] = $login_details->AgentName;
                $data['Status'] = $login_details->Status;
                $data['OpeningDate'] = $login_details->OpeningDate;
                $data['CloseingDate'] = $login_details->CloseingDate;
                $data['LoginType'] = $login_details->LoginType;
                $data['LoginId'] = $login_details->LoginId;
                $data['SessionYear'] = $login_details->SessionYear;
                $data['CreatedDate'] = $login_details->CreatedDate;
                $data['is_delete'] = $login_details->is_delete;
                $data['DeletedDate'] = $login_details->DeletedDate;
                $data['DeletedBy'] = $login_details->DeletedBy;
                $data['UpdatedDate'] = $login_details->UpdatedDate;
                $data['UpdatedBy'] = $login_details->UpdatedBy;

                // Similarly, add other fields

                return response()->json($data);
            } else {
                $data['msg'] = "Invalid Login Details!";
                $data['status'] = false;
                return response()->json($data);
            }
        } else {
            $data['msg'] = "Invalid branch code";
            $data['status'] = false;
            return response()->json($data);
        }
    }

        public function showMIS(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $branch_id = $request->branch_id;
        $account_number = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $mis_records = DB::table('mis')
                ->where('member_type', $member_type)
                ->where('account_no', $account_number)
                ->where('branchId', $branch_id)
                ->where('is_delete', 'No')
                ->get();

            if ($mis_records->isNotEmpty()) {
                $json['msg'] = "MIS list show successfully!";
                $json['status'] = true;
                $finaldata = [];

                foreach ($mis_records as $showmisd) {
                    $data = [];
                    $data['member_type'] = $showmisd->member_type;
                    $data['start_date'] = date('d-m-Y', strtotime($showmisd->date));
                    $data['account_no'] = $showmisd->account_no;
                    $data['maturity_date'] = date('d-m-Y', strtotime($showmisd->maturity_date));
                    $data['maturity_amount'] = $showmisd->maturity_amount;
                    $data['amount'] = $showmisd->amount;
                    $data['interest'] = $showmisd->interest;
                    $data['TotalInterest'] = $showmisd->TotalInterest;
                    $data['monthly_interest'] = $showmisd->monthly_interest;
                    $data['status'] = $showmisd->status;
                    $finaldata['Rest'][] = $data;
                }

                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function saving(Request $request){
        $branch_code = strtolower($request->branch_code);
        $BranchId = strtolower($request->branch_id);
        $memberType = $request->memeber_type;
        $accountNo = $request->SavingAccountNo;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if($branch){
            $savings = DB::table('member_savings')
            ->where('accountNo', $accountNo)
             //->where('branchId', $BranchId)
             ->where('memberType', $memberType)
            ->get();

            if ($savings->isNotEmpty()) {
            $balance = 0;
            $finaldata = [];
            $data = [
                    'Voucher No' => 0,
                    'date' => "01-01-1950",
                    'Deposit' => 0 ,
                    'Withdraw' => 0,
                    'balance' => $this->getSaving($accountNo),
                ];
            $finaldata['Rest'][] = $data;
            foreach ($savings as $saving) {
                $balance += $saving->depositAmount - $saving->withdrawAmount;

                $data = [
                    'Voucher No' => $saving->id,
                    'date' => date('d-m-Y', strtotime($saving->transactionDate)),
                    'Deposit' => number_format($saving->depositAmount),
                    'Withdraw' => number_format($saving->withdrawAmount),
                    'balance' => number_format($balance)
                ];
                $finaldata['Rest'][] = $data;
            }

            $json['msg'] = "Success";
            $json['status'] = true;
            $json['Response'] = $finaldata;
        }else{
                $json['msg'] = "Success";
                $json['status'] = "true";
                $data = [
                    'Voucher No' => 0,
                    'date' => "01-01-1950",
                    'Deposit' => 0 ,
                    'Withdraw' => 0,
                    'Balance' => $this->getSaving($accountNo),
                ];

                $finaldata['Rest'][] = $data;
                $json['Response'] = $finaldata;
            }
        }else{
            $json['status'] = false;
            $json['msg'] = "Branch code doesn't match!";
        }

        return response()->json($json);
    }

    public function shareDetail(Request $request){
        $branch_code = strtolower($request->branch_code);
        $BranchId = strtolower($request->branch_id);
        $memberType = $request->memeber_type;
        $accountNo = $request->AccountNo;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if($branch){
            $share_account_details = DB::table('member_shares')
            ->where('accountNo', $accountNo)
            ->where('branchId', $BranchId)
            ->where('memberType', $memberType)
            ->get();



            if($share_account_details->isNotEmpty()){
                $json['msg'] = "Success";
                $json['status'] = "true";
                $finalData = [];
                $balance = $this->getsharebalance($accountNo);
                 $data = [
                    'Voucher No' => 0,
                    'date' => "01-01-1950",
                    'Deposit' => 0 ,
                    'Withdraw' => 0,
                    'Balance' => $this->getsharebalance($accountNo),
                ];

                $finaldata['Rest'][] = $data;
                $json['Response'] = $finaldata;
                foreach($share_account_details as $share_details){
                    $balance += $share_details->depositAmount - $share_details->withdrawAmount;
                    $data = [
                        'Voucher No' => $share_details->id,
                        'date' => date('d-m-Y', strtotime($share_details->transactionDate)),
                        'Deposit' => number_format($share_details->depositAmount),
                        'Withdraw' => number_format($share_details->withdrawAmount),
                        'Balance' => number_format($balance),
                        ];

                    $finaldata['Rest'][] = $data;
                }

                $json['Response'] = $finaldata;
            }else{
                $json['msg'] = "Success";
                $json['status'] = "true";
                $data = [
                    'Voucher No' => 0,
                    'date' => "01-01-1950",
                    'Deposit' => 0 ,
                    'Withdraw' => 0,
                    'Balance' => $this->getsharebalance($accountNo),
                ];

                $finaldata['Rest'][] = $data;
                $json['Response'] = $finaldata;
            }
        }else{
            $json['status'] = false;
            $json['msg'] = "Branch code doesn't match!";
        }
        return response()->json($json);
    }

    public function showrdDetail(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $rd_id = $request->RdId;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $rd_receipt_details = DB::table('rd_receiptdetails')
                ->where('rc_account_no', $rd_id)
                ->where('is_delete', 'No')
                ->orderBy('id', 'desc')
                ->get();

            $receipt_count = $rd_receipt_details->count();
            $total = 0;
            $finaldata = ['Rest' => []];

            if ($receipt_count > 0) {
                $i = 0;
                foreach ($rd_receipt_details as $receipt_detail) {
                    $i++;
                    $data = [
                        'Sr_no' => $i,
                        'payment_date' => date('d-m-Y', strtotime($receipt_detail->payment_date)),
                        'memberType' => $receipt_detail->memberType,
                        'amount' => $receipt_detail->amount,
                        'rc_account_no' => $receipt_detail->rc_account_no,
                        'rd_account_no' => $receipt_detail->rd_account_no,
                        'installment_date' => $receipt_detail->installment_date,
                        'panelty' => $receipt_detail->panelty,
                        'mis_id' => $receipt_detail->mis_id,
                    ];
                    $total += $receipt_detail->amount;
                    $finaldata['Rest'][] = $data;
                }

                $json['msg'] = "Receipt List Showed Successfully";
                $json['status'] = true;
                $json['total'] = $total;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function rdList(Request $request){
        $branch_code = strtolower($request->branch_code);
        $branch_id = $request->branch_id;
        $account_no = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if($branch){
            $rd = DB::table('re_curring_rds')
            ->where('accountNo', $account_no)
            ->where('memberType', $member_type)
            ->where('is_delete', 'No')
            ->get();

        $count = $rd->count();

        if($count > 0){
            $finaldata = ['Rest' => []];

            foreach($rd as $rdrow){
                $RD_reccuring = DB::table('re_curring_rds')
                ->where('id', $rdrow->id)
                ->where('is_delete', 'No')
                ->first();

                $RD_rr = DB::table('rd_receipts')
                ->where('rd_account_no', $rdrow->rd_account_no)
                ->first();

                $RD_acc = DB::table('opening_account_details')
                ->where('AccountNumber', $RD_reccuring->accountNo)
                ->where('MemberType', $RD_reccuring->memberType)
                ->where('Branch',$branch_id)
                ->where('is_delete', 'No')
                ->first();

                $sum_Rdrr = DB::table('rd_receiptdetails')
                    ->select(DB::raw('sum(amount) as Tota_Received_Amount'))
                    ->where('rd_account_no', $rdrow->rd_account_no)
                    ->where('is_delete', 'No')
                    ->first();
                $sum_Rdrr_Tota_Received_Amount = $sum_Rdrr->Tota_Received_Amount ?? 0;

                $data = [
                    'id' => $rdrow->id,
                    'memberType' => $RD_reccuring->memberType,
                    'accountNo' => $RD_reccuring->accountNo,
                    'amount' => $RD_reccuring->amount,
                    'start_date' => date('d-m-Y', strtotime($RD_reccuring->rd_created_from)),
                    'monthly_installment' => $rdrow->amount,
                    'maturity_date' => date('d-m-Y', strtotime($rdrow->maturity_date)),
                    'maturity_amount' => $rdrow->maturity_amount,
                    'month' => $RD_reccuring->month,
                    'status' => $rdrow->status,
                ];

                $finaldata['Rest'][] = $data;
            }
            $json['msg'] = "RD list showed successfully!";
            $json['status'] = true;
            $json['Response'] = $finaldata;
        }
        else {
            $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
            $json['status'] = false;
        }
        }
        else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }
        return response()->json($json);

    }


    public function showfdDetails(Request $request){
        $branch_code = strtolower($request->branch_code);
        $branch_id = $request->branch_id;
        $account_no = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if($branch){
            $fd_query = DB::table('member_fds')
            ->where('accountNo', $account_no)
                ->where('memberType', $member_type)
                ->where('is_delete', 'No')
                ->where('branchId', $branch_id)
                ->get();

                $count = $fd_query->count();

            if ($count > 0) {
                $finaldata = ['Rest' => []];

                foreach ($fd_query as $fd_query_result) {
                    $data = [
                        'fdNo' => $fd_query_result->fdNo,
                        'accountNo' => $fd_query_result->accountNo,
                        'ReceiptDate' => $fd_query_result->openingDate,
                        'Amount' => $fd_query_result->principalAmount,
                        'interestRate' => $fd_query_result->interestRate,
                        'status' => $fd_query_result->status,
                    ];

                    if ($fd_query_result->status == 'Active') {
                        $data['maturity_date'] = $fd_query_result->maturityDate;
                        $data['maturity_amount'] = round($fd_query_result->maturityAmount);
                    } else {
                        $data['maturity_date'] = $fd_query_result->actualMaturityDate;
                        $data['maturity_amount'] = round($fd_query_result->actualMaturityAmount);
                    }

                    $finaldata['Rest'][] = $data;
                }

                $json['msg'] = "FD list show Successfully";
                $json['status'] = true;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        }
        else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }
        return response()->json($json);
    }


    public function showLoanList(Request $request){

        $branch_code = strtolower($request->branch_code);
        $branch_id = $request->branch_id;
        $account_number = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $loan_query = DB::table('member_loans')
                ->where('memberType', $member_type)
                ->where('accountNo', $account_number)
                //->where('branchId', $branch_id)
                ->where('is_delete', 'No')
                ->get();

            $check = $loan_query->count();
            $i = 0;

            if ($check > 0) {
                $finaldata = ['Rest' => []];

                foreach ($loan_query as $loan_row) {
                    $i++;
                    $installment_query = DB::table('loan_installments')
                        ->select(DB::raw('SUM(Principal) as pr, sum(Principal) as Ipr'))
                        ->where('LoanId', $loan_row->accountId)
                        ->first();

                    $receipt_query = DB::table('loan_recoveries')
                        ->select(DB::raw('SUM(Principal) as pr, sum(Total) as Rpr'))
                        ->where('LoanId', $loan_row->accountId)
                        ->where('is_delete', 'No')
                        ->first();

                    $loanType = DB::table('loan_masters')
                    ->where('id', $loan_row->loanType)
                    ->where('is_delete', '!=', 'Yes')
                    ->first();

                    if($loanType){
                        $loan_name = $loanType->name;
                    }else {
                        $loan_name = null;
                    }

                    $installment_amount = $installment_query->Ipr;
                    $receipt_amount = $receipt_query->Rpr;

                    if ($installment_amount >= $receipt_amount) {
                        $color = "green";
                    } else {
                        $color = "red";
                    }

                    $pending_loan_amount = $loan_row->loanAmount - $receipt_query->pr;

                    $data = [
                        'LoanId' => $loan_row->id,
                        'LoanDate' => date('d-m-Y', strtotime($loan_row->loanDate)),
                        'LoanAmount' => number_format($loan_row->loanAmount),
                        'pending_loan_amount' => number_format(max($pending_loan_amount, 0)),
                        'LoanName' => $loan_name,
                        'LoanInterest' => $loan_row->loanInterest,
                        'LoanPanelty' => $loan_row->loanPanelty,
                        'LoanYear' => $loan_row->loanYear,

                    ];


                    $finaldata['Rest'][] = $data;
                }

                $json['msg'] = "Loan list show successfully";
                $json['status'] = true;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function getProfile(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $branch_id = strtolower($request->branchId);
        $member_type = $request->member_type;
        $account_number = $request->accountNo;
        $account_type = $request->AccountType;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $account_details_query = DB::table('opening_account_details')
                ->where('MemberType', $member_type)
                ->where('AccountNumber', $account_number)
                ->where('Branch', $branch_id)
                ->where('Status', 'Active')
                ->first();
            if ($account_details_query) {
                $data['msg'] = "Successfull";
                $data['status'] = true;
                $data['Name'] = str_replace("/", " & ", $account_details_query->Name);
                $data['AccountNumber'] = $account_details_query->AccountNumber;
                $data['FatherHusband'] = $account_details_query->FatherHusband;
                $data['ContactNumber'] = $account_details_query->ContactNumber;
                $data['WardNumber'] = $account_details_query->WardNumber;
                $state = $account_details_query->State;
                $district = $account_details_query->District;
                $tehsil = $account_details_query->Tehsil;
                $village = $account_details_query->Village;
                $data['Address'] = $account_details_query->Address . "," . $state . "," . $district . "," . $tehsil . "," . $village;
                $pic1 = $account_details_query->Pic1;
                $pic2 = $account_details_query->Pic2;

                $path = "https://startechcoop.com/societydesign/OpeningAccount/";

                if (!is_null($pic1)) {
                    $data['image1'] = $path . $pic1;
                    $data['image2'] = "";
                } else {
                    $data['image1'] = "";
                    $data['image2'] = $path . $pic2;
                }

                if (!is_null($pic1) && !is_null($pic2)) {
                    $data['image1'] = $path . $pic1;
                    $data['image2'] = $path . $pic2;
                }
            } else {
                $data['msg'] = "failed";
                $data['status'] = false;
                $data['error'] = "Record not found in database";
            }
        } else {
            $data['status'] = false;
            $data['msg'] = "Branch code doesn't match!";
        }

        return response()->json($data);
    }

    public function getDailySavingCashLoan(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $branch_id = strtolower($request->branch_id);
        $id = $request->vno;
        $account_number = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $daily_collection = DB::table('daily_collections')
                ->where('accountid', $id)
                ->where('is_delete', '!=', 'Yes')
                ->first();

            if ($daily_collection) {
                $data = [];
                $data['start_date'] = date('d-m-Y', strtotime($daily_collection->date));
                $data['voucher_no'] = $daily_collection->id;
                $data['interest'] = $daily_collection->interest_amount;
                $data['month'] = $daily_collection->month;
                $data['agent_name'] = $daily_collection->agent_name;

                $finaldata['Rest'][] = $data;

                $json['msg'] = "Daily saving cash loan list showed successfully";
                $json['status'] = true;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function dailySavingCash(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $branch_id = strtolower($request->branch_id);
        $account_number = $request->AccountNumber;
        $member_type = $request->member_type;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $daily_saving_data = DB::table('daily_collections')
                ->where('membertype', $member_type)
                ->where('accountNo', $account_number)
                ->where('branchId', $branch_id)
                ->where('is_delete', 'No')
                ->get();

            $count = $daily_saving_data->count();

            if ($count > 0) {
                $finaldata = [];
                foreach ($daily_saving_data as $daily_saving_result) {
                    $vno = $daily_saving_result->id;
                    $received_amount = isset($daily_saving_result->Recovery) ? number_format($daily_saving_result->Recovery, 2) : 0;
                    $data['voucher_no'] = $vno;
                    $data['scheme_name']=$daily_saving_result->schemename;
                    $data['received_amount'] = $received_amount;

                    $finaldata['Rest'][] = $data;
                }

                $json['msg'] = "Daily saving cash list showed successfully";
                $json['status'] = true;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function showLoanRecovery(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $LoanId = $request->loanId;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $Loan = DB::table('loan_recoveries')
                ->where('loanId', $LoanId)
                ->where('is_delete', 'No')
                ->orderByDesc('receiptDate')
                ->get();

            $check = $Loan->count();

            if ($check > 0) {
                $finaldata = [];
                $i = 0;

                foreach ($Loan as $LoanRow) {
                    $i++;
                    $data['Sr_no'] = $i;
                    $data['LoanId'] = $LoanRow->loanId;
                    $data['ReceiptDate'] = date('d-m-Y', strtotime($LoanRow->receiptDate));
                    $data['Principal'] = number_format($LoanRow->principal);
                    $data['Interest'] = $LoanRow->interest;
                    $data['PenalInterest'] = $LoanRow->penalInterest;
                    $data['Total'] = number_format($LoanRow->total);

                    $finaldata['Rest'][] = $data;
                }

                $json['msg'] = "Loan recovery shown successfully";
                $json['status'] = true;
                $json['Response'] = $finaldata;
            } else {
                $json['msg'] = "Parameter missing / Incorrect parameter value! / Data not in database!";
                $json['status'] = false;
            }
        } else {
            $json['msg'] = "Branch code doesn't match!";
            $json['status'] = false;
        }

        return response()->json($json);
    }

    public function changePassword(Request $request)
    {
        $branch_code = strtolower($request->branch_code);
        $BranchId = strtolower($request->Branch);
        $AccountNumber = $request->AccountNumber;
        $member_type = $request->member_type;
        $Old_password = $request->Password;
        $New_password = $request->New_password;

        $branch = BranchMaster::where('branch_code', $branch_code)->first();

        if ($branch) {
            $check_pwd_query = DB::table('opening_account_details')
                ->where('Password', $Old_password)
                ->where('Branch', $BranchId)
                ->where('AccountNumber', $AccountNumber)
                ->where('MemberType', $member_type)
                ->where('Status', 'Active')
                ->first();

            if ($check_pwd_query) {
                if ($Old_password != $New_password) {
                    $update_new_password_query = DB::table('opening_account_details')
                        ->where('Branch', $BranchId)
                        ->where('AccountNumber', $AccountNumber)
                        ->where('MemberType', $member_type)
                        ->where('Status', 'Active')
                        ->update(['Password' => $New_password]);

                    $data['msg'] = "Password Change Successfully";
                    $data['status'] = true;
                } else {
                    $data['msg'] = "New password cannot be the same as the old password. Please enter a new password!";
                    $data['status'] = false;
                }
            } else {
                $data['msg'] = "Your current password is incorrect!";
                $data['status'] = false;
            }
        } else {
            $data['msg'] = "Branch code doesn't match!";
            $data['status'] = false;
        }

        return response()->json($data);
    }

public function cdsDetails(Request $request)
{
    $branch_code = strtolower($request->branch_code);
    $BranchId = strtolower($request->branch_id);
    $member_type = $request->memeber_type;
    $account_no = $request->AccountNo;

    $branch = BranchMaster::where('branch_code', $branch_code)->first();

    if ($branch) {
        $saving_account_details = DB::table('compulsory_deposits')
            ->where('accno', $account_no)
            ->where('Branch', $BranchId)
            ->where('membertype', $member_type)
            ->where('is_delete', 'No')
            ->get();

        $count = $saving_account_details->count();

        if ($count > 0) {
            $response = [
                'msg' => 'Success',
                'status' => true,
                'Response' => []
            ];
            $balance = 0;

            foreach ($saving_account_details as $details) {
                $balance += $details->Deposit - $details->Withdraw;
                $data = [
                    'Voucher No' => $details->id,
                    'date' => date('d-m-Y', strtotime($details->date)),
                    'Deposit' => number_format($details->Deposit),
                    'Withdraw' => number_format($details->Withdraw),
                    'balance' => number_format($balance),
                ];
                $response['Response'][] = $data;
            }

            return response()->json($response);
        } else {
            $response = [
                'msg' => 'Record not found in database',
                'status' => false
            ];

            return response()->json($response);
        }
    } else {
        $response = [
            'msg' => 'Branch code doesn\'t match!',
            'status' => false
        ];

        return response()->json($response);
    }
}

public function homebalance(Request $post){

     $branch = BranchMaster::where('branch_code', $post->branch_code)->first();
     if(!$branch){
            $data = [
            'msg' => 'Branch code doesn\'t match!',
            'status' => false
        ];
       return response()->json($data);
     }

     $share_account_details = DB::table('member_accounts')
            ->where('accountNo', $post->AccountNumber)
           // ->where('memberType', $post->member_type)
            ->first();
     if(!$share_account_details){
           $data = [
            'msg' => 'Account No doesn\'t match!',
            'status' => false
        ];
         return response()->json($data);
     }

    	    $data['msg']		= "home list show successfully!";
			$data['status']		= true;
			$data['saving_bal'] = $this->getSaving($post->AccountNumber);
			$data['share_bal']  = $this->getsharebalance($post->AccountNumber);
			$data['cds_bal']    = $this->getCdsBalance($post->AccountNumber,$post->member_type) ;
			$data['loan_bal']  	= $this->getLoanbal($post->AccountNumber,$post->member_type);
			$data['fd_bal']  	= $this->fdbalance($post->AccountNumber,$post->member_type);
			$data['rd_bal']  	= $this->rdBalance($post->AccountNumber,$post->member_type);
			$data['mis_bal']  	= $this->misbalance($post->AccountNumber,$post->member_type);
			$data['daily_bal'] 	= 0 ;
			$data['mcl_bal'] 	= 0;
			$data['noti_count']	= 1;


    return response()->json($data);
}

public function getSaving($ac){
        $openingBal = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
        $SavBal = $openingBal->Saving ?? 0 ;
        $saving = MemberSaving::where(['accountNo' => $ac])->where('is_delete','!=',"Yes")->orderBy('transactionDate')->get();
        $savingBalance =$SavBal + $saving->sum('depositAmount') - $saving->sum('withdrawAmount');
        return $savingBalance;
}

public function getsharebalance($ac)
  {
        $openingBal = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
        $shareBal = $openingBal->Sharee ?? 0 ;
        $credit =  MemberShare::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Deposit')->sum("depositAmount");
        $debit =  MemberShare::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Withdraw')->sum("withdrawAmount");
        return $shareBal + $credit - $debit;
  }


  public function getCdsBalance($ac,$member){
            $openingBal = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
            $cdsBal = $openingBal->OpeningCompulsoryDeposit ?? 0 ;
            $member = MemberAccount::where(['memberType' => $member, 'accountNo' => $ac])->first();
            $saving = CompulsoryDeposit::where(['membertype' => $member, 'accno' => $ac])->where('is_delete','!=',"Yes")->orderBy('date')->get();
            $savingBalance =  $cdsBal + $saving->sum('depositAmount') - $saving->sum('withdrawAmount');
            return $savingBalance ;

  }

  public function getLoanbal($acount,$member){
          $loanBal = 0 ;
          $loanmasters = MemberLoan::where('is_delete', '!=', 'Yes')->where('status','Disbursed')
          ->where(['accountNo'=>$acount])->get();
        if(count($loanmasters) > 0){
            $recovory = 0;
            foreach($loanmasters as $loanmaster){
                 $loan_recovery = LoanRecovery::where(['loanId'=>$loanmaster->id])->where('is_delete', 'No')->sum('principal');
                 $recoveryDate = LoanRecovery::where(['loanId'=>$loanmaster->id])->where('is_delete', 'No')->orderBy('receiptDate', 'DESC')->first('receiptDate');
                 $loanBal=$loanmaster->loanAmount - $loan_recovery;
            }
        }
        return $loanBal;
  }


  public function fdbalance($ac,$member){
        $fdbalance = MemberFd::where('is_delete', '!=', 'Yes')->where('status',"!=",'Matured')->where(['accountNo'=>$ac])->sum('principalAmount');
       return $fdbalance ?? 0; //,"memberType"=>$member
  }


  public function rdBalance($ac,$member){
      $rdbalance = 0 ;
      $rdmasters = ReCurringRd::where('is_delete', '!=', 'Yes')->where(['accountNo'=>$ac])->whereIn('status',['Locked','Active'])->get(); //"memberType"=>$member
      if(count($rdmasters) > 0){
        $recovory = 0;
        foreach($rdmasters as $rdmaster){
             $rd_received = RdReceiptdetails::where(['rc_account_no'=>$rdmaster->id])->where('is_delete', 'No')->sum('amount');
             $rdbalance += $rd_received ;
            }
        }
    return $rdbalance ;
  }

   public function misbalance($ac,$member){
        $misbalance = Mis::where('is_delete', '!=', 'Yes')->where('status','Active')->where(['account_no'=>$ac])->sum('amount'); //"member_type"=>$member
       return $misbalance ?? 0;
  }


  public function banner(){
        $data['banners'] = Slider::where('status', 'active')->get();
        return response()->json(['status' => 'success', 'message' => 'Data fetched Successfully', 'data' => $data]);
  }
}
