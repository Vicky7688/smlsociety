<?php

namespace App\Http\Controllers\WebControllers\Transactions\FD;

use App\Http\Controllers\Controller;
use App\Models\AgentMaster;
use App\Models\FdInterestMaster;
use App\Models\FdMaster;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\LoanMaster;
use App\Models\MemberAccount;
use App\Models\MemberFd;
use App\Models\MemberSaving;
use App\Models\bankfd;
use App\Models\opening_accounts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\SessionMaster;
use Session;

class FDController extends Controller
{
    // public function bankindex(Request $request){
    //     if(!empty($request->all())){
    //         $validator = Validator::make($request->all(),[
    //             'fdnumber' => 'required|unique:bank_fd_deposit,fdnumber',
    //             'accountno' => 'required|unique:bank_fd_deposit,accountno'
    //         ]);

    //         if($validator->fails()){
    //             return back();
    //         }

    //     DB::beginTransaction();
    //     try{
    //             $addfd = new bankfd();
    //             $addfd->fddate = date('Y-m-d',strtotime($request->fddate));
    //             $addfd->fdnumber = $request->fdnumber;
    //             $addfd->fdaccountledger = $request->fdaccountledger;
    //             $addfd->accountno = $request->accountno;
    //             $addfd->amount = $request->amount;
    //             $addfd->intrestfrom = date('Y-m-d',strtotime($request->intrestfrom));
    //             $addfd->intrestrate = $request->intrestrate;
    //             $addfd->period = $request->period;
    //             $addfd->days = $request->days;
    //             $addfd->intresttype = $request->intresttype;
    //             $addfd->savingbank = $request->savingbank;
    //             $addfd->maturitydate = date('Y-m-d',strtotime($request->maturitydate));
    //             $addfd->maturityamount = $request->maturityamount;
    //             $addfd->save();


    //             //_________________Gerenal Ledger Entry
    //             if($request->fdaccountledger){
    //                 $general_legder_entry = new GeneralLedger();
    //                 $general_legder_entry->serialNo = $addfd->id;
    //                 $general_legder_entry->accountId = $request->accountno;
    //                 $general_legder_entry->accountNo = $request->fdnumber;
    //                 $general_legder_entry->groupCode = 'BANK001';
    //                 $general_legder_entry->ledgerCode = $request->fdaccountledger;
    //                 $general_legder_entry->formName = 'Bank FD';
    //                 $general_legder_entry->referenceNo = $addfd->id;
    //                 $general_legder_entry->entryMode = 'manual';
    //                 $general_legder_entry->transactionDate = date('Y-m-d',strtotime($request->fddate));
    //                 $general_legder_entry->transactionType = 'Dr';
    //                 $general_legder_entry->transactionAmount = $request->amount;
    //                 $general_legder_entry->narration = 'Bank FD';
    //                 $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
    //                 $general_legder_entry->agentId = 1;
    //                 $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
    //                 $general_legder_entry->updatedBy = $request->user()->id;
    //                 $general_legder_entry->is_delete = 'No';
    //                 $general_legder_entry->save();
    //             }



    //             //_________________Gerenal Ledger Entry
    //             if($request->savingbank){
    //                 $general_legder_entry = new GeneralLedger();
    //                 $general_legder_entry->serialNo = $addfd->id;
    //                 $general_legder_entry->accountId = $request->accountno;
    //                 $general_legder_entry->accountNo = $request->fdnumber;
    //                 $general_legder_entry->groupCode = 'BANK001';
    //                 $general_legder_entry->ledgerCode = $request->savingbank;
    //                 $general_legder_entry->formName = 'Bank FD';
    //                 $general_legder_entry->referenceNo = $addfd->id;
    //                 $general_legder_entry->entryMode = 'manual';
    //                 $general_legder_entry->transactionDate = date('Y-m-d',strtotime($request->fddate));
    //                 $general_legder_entry->transactionType = 'Cr';
    //                 $general_legder_entry->transactionAmount = $request->amount;
    //                 $general_legder_entry->narration = 'Bank FD';
    //                 $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
    //                 $general_legder_entry->agentId = 1;
    //                 $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
    //                 $general_legder_entry->updatedBy = $request->user()->id;
    //                 $general_legder_entry->is_delete = 'No';
    //                 $general_legder_entry->save();
    //             }
    //             DB::commit();
    //             return redirect('transactions/fd/bank/index');
    //         }catch(\Exception $e){
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'Fail',
    //                 'messages' => 'Record not Found',
    //                 'error' => $e->getMessage()
    //             ]);
    //         }
    //         }else{
    //             $formurl=url('transactions/fd/bank/index');
    //             $groups = GroupMaster::where('id', '<=', 2)->get();
    //             $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
    //             $agents = AgentMaster::orderBy('name', 'ASC')->get();
    //             $bank_fds = bankfd::orderBy('fdnumber','DESC')->get();
    //             return view('transaction.fd.bankfd', compact('groups', 'ledgers', 'agents','bank_fds','formurl'));
    //         }

    // }

    // //__________________Edit Bank FD
    // public function editbankfd(Request $request,$id){
    //     $formurl=url('transactions/fd/bank/index');
    //     $groups = GroupMaster::where('id', '<=', 2)->get();
    //     $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
    //     $agents = AgentMaster::orderBy('name', 'ASC')->get();
    //     $bankFixedDeposit = bankfd::find($id);



    //     if(!empty($request->all())){
    //         $bankFixedDeposit = bankfd::where('id','!=', $id)->where('fdnumber', '=', $request->fdnumber)->where('accountno', '=', $request->accountno)->first();

    //         if($bankFixedDeposit){
    //             return back();
    //         }

    //         DB::beginTransaction();
    //         try{
    //             $bankFixedDepositupdate = bankfd::find($id);
    //             $bankFixedDepositupdate->fddate = date('Y-m-d',strtotime($request->fddate));
    //             $bankFixedDepositupdate->fdnumber = $request->fdnumber;
    //             $bankFixedDepositupdate->fdaccountledger = $request->fdaccountledger;
    //             $bankFixedDepositupdate->accountno = $request->accountno;
    //             $bankFixedDepositupdate->amount = $request->amount;
    //             $bankFixedDepositupdate->intrestfrom = date('Y-m-d',strtotime($request->intrestfrom));
    //             $bankFixedDepositupdate->intrestrate = $request->intrestrate;
    //             $bankFixedDepositupdate->period = $request->period;
    //             $bankFixedDepositupdate->days = $request->days;
    //             $bankFixedDepositupdate->intresttype = $request->intresttype;
    //             $bankFixedDepositupdate->savingbank = $request->savingbank;
    //             $bankFixedDepositupdate->maturitydate = date('Y-m-d',strtotime($request->maturitydate));
    //             $bankFixedDepositupdate->maturityamount = $request->maturityamount;
    //             $bankFixedDepositupdate->save();


    //             //_________________Gerenal Ledger Entry
    //             if($request->fdaccountledger){
    //                 $general_legder_entry = GeneralLedger::where('referenceNo',$id)->where('formName','Bank FD')->first();
    //                 $general_legder_entry->serialNo = $bankFixedDepositupdate->id;
    //                 $general_legder_entry->accountId = $request->accountno;
    //                 $general_legder_entry->accountNo = $request->fdnumber;
    //                 $general_legder_entry->groupCode = 'BANK001';
    //                 $general_legder_entry->ledgerCode = $request->fdaccountledger;
    //                 $general_legder_entry->formName = 'Bank FD';
    //                 $general_legder_entry->referenceNo = $bankFixedDepositupdate->id;
    //                 $general_legder_entry->entryMode = 'manual';
    //                 $general_legder_entry->transactionDate = date('Y-m-d',strtotime($request->fddate));
    //                 $general_legder_entry->transactionType = 'Dr';
    //                 $general_legder_entry->transactionAmount = $request->amount;
    //                 $general_legder_entry->narration = 'Bank FD';
    //                 $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
    //                 $general_legder_entry->agentId = 1;
    //                 $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
    //                 $general_legder_entry->updatedBy = $request->user()->id;
    //                 $general_legder_entry->is_delete = 'No';
    //                 $general_legder_entry->save();
    //             }



    //             //_________________Gerenal Ledger Entry
    //             if($request->savingbank){
    //                 $general_legder_entry = GeneralLedger::where('referenceNo',$id)->where('formName','Bank FD')->first();
    //                 $general_legder_entry->serialNo = $bankFixedDepositupdate->id;
    //                 $general_legder_entry->accountId = $request->accountno;
    //                 $general_legder_entry->accountNo = $request->fdnumber;
    //                 $general_legder_entry->groupCode = 'BANK001';
    //                 $general_legder_entry->ledgerCode = $request->savingbank;
    //                 $general_legder_entry->formName = 'Bank FD';
    //                 $general_legder_entry->referenceNo = $bankFixedDepositupdate->id;
    //                 $general_legder_entry->entryMode = 'manual';
    //                 $general_legder_entry->transactionDate = date('Y-m-d',strtotime($request->fddate));
    //                 $general_legder_entry->transactionType = 'Cr';
    //                 $general_legder_entry->transactionAmount = $request->amount;
    //                 $general_legder_entry->narration = 'Bank FD';
    //                 $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
    //                 $general_legder_entry->agentId = 1;
    //                 $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
    //                 $general_legder_entry->updatedBy = $request->user()->id;
    //                 $general_legder_entry->is_delete = 'No';
    //                 $general_legder_entry->save();
    //             }
    //             DB::commit();

    //         }catch(\Exception $e){
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'Fail',
    //                 'messages' => 'Record not Found',
    //                 'error' => $e->getMessage()
    //             ]);
    //         }
    //     }else{
    //         $formurl=url('transactions/fd/bank/index/'.$id);
    //         $groups = GroupMaster::where('id', '<=', 2)->get();
    //         $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
    //         $agents = AgentMaster::orderBy('name', 'ASC')->get();
    //         $bank_fds = bankfd::orderBy('fdnumber','DESC')->get();
    //         return view('transaction.fd.bankfd', compact('groups', 'ledgers', 'agents','bank_fds','formurl','bankFixedDeposit'));
    //     }

    // }

























    public function index()
    {
        $groups = GroupMaster::where('id', '<=', 2)->get();
        $ledgers = LedgerMaster::where('id', '<=', '1')->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        return view('transaction.fd.fd', compact('groups', 'ledgers', 'agents'));
    }

    public function getData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $output = '';

        if (empty($accountNo)) {
            $output .= '<li class="list-group-item memberlist"></li>';
            return response()->json([
                'status' => true,
                'data' => $output
            ]);
        }
        $data = opening_accounts::where('memberType', $memberType)
        ->where('accountNo', 'LIKE', $accountNo . '%')
        ->where('accountname','FD')
        ->select('accountNo')
        ->get();


        if ($data->count() > 0) {
            $output = '<ul class="list-group membersearch" style="display:block; z-index: 1;">';
            foreach ($data as $row) {
                $output .= '<li class="list-group-item memberlist">' . htmlspecialchars($row->accountNo) . '</li>';
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



    public function fetchData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $fdaccountNoo = $request->fdaccountNoo;
        // $data = opening_accounts::where('membershipno', $accountNo)->get();
        $data = DB::table('opening_accounts')->where('membershipno', $accountNo)->where(['accountname' => 'FD'])->get();
        if(!empty($fdaccountNoo)){
         $data = DB::table('opening_accounts')->where('membershipno', $accountNo)->where('accountNo',$fdaccountNoo)->where(['accountname' => 'FD'])->get();
        }
        $output = "";
        if (count($data) > 0 ) {
            $output = '<ul class="list-group membersearch" style="display:block;z-index:1">';
            foreach ($data as $row) {
                $output .= '<li class="list-group fdmemberlist">' . $row->accountNo . '</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<li class="list-group-item fdmemberlist">No Data Found</li>';
        }

        if (!empty($accountNo)) {
            $member = MemberAccount::where(['memberType' => $memberType, 'accountNo' => $accountNo])->first();
            $fd = MemberFd::where(['memberType' => $memberType, 'accountNo' => $accountNo])->where('is_delete',"!=", "Yes")->orderBy('openingDate')->get();
            return response()->json([
                'status' => true,
                'member' => $member,
                'fd' => $fd,
                'fd_accounts' => $output
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = array(
            'memberType' => 'required',
            'accountNo' => 'exists:member_accounts,accountNo',
            'fdType' => 'required',
            // 'fdNo' => 'required',
            'openingDate' => 'required|date',
            'principalAmount' => 'required|numeric',
            'paymentType' => 'required',
            'bank' => 'required',
            'interestType' => 'required',
            'interestStartDate' => 'required|date',
            'interestRate' => 'required|numeric',
            'interestAmount' => 'required|numeric',
            'years' => 'nullable|integer',
            'months' => 'nullable|integer',
            'days' => 'nullable|integer',
            'maturityDate' => 'required|date',
            'maturityAmount' => 'required|numeric'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Please check all inputs'
            ]);
        }

        // Get member details
        $member = MemberAccount::where(['memberType' => $request->memberType, 'accountNo' => $request->accountNo])->first();

        // Check if member account exists or not
        if (empty($member)) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid account number'
            ]);
        }

        $checkfdno =  MemberFd :: where('fdNo',$request->fdNo)->where('is_delete',"!=","Yes")->first();
        if($checkfdno && $request->fdId == "new"){
              return response()->json([
                'status' => "account",
                'message' => 'FD no. allready exist'
            ]);
        }



        // Check account opening date
        if ($request->openingDate < $member->openingDate) {
            return response()->json([
                'status' => "",
                'message' => 'Transaction date cannot be less than account opening date'
            ]);
        }

        // Generate Serial No
        do {
            $serialNo = "fd" . rand(1111111, 9999999);
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

          if($request->fdId != "new"){

                $checkfdno =  MemberFd :: where('id',$request->fdId)->where('is_delete',"!=","Yes")->first();
                if($checkfdno){
                    $serialNo = $checkfdno->serialNo;
                }
          }
        // Select Group and Ledger Code
        if ($request->memberType == 'Member') {
            $groupCode = 'FDOM001';
            $ledgerCode = 'FDOM001';
        } else if ($request->memberType == 'NonMember') {
            $groupCode = 'FDON001';
            $ledgerCode = 'FDON001';
        } else {
            $groupCode = 'FDOS001';
            $ledgerCode = 'FDOS001';
        }

        // Inserting or Updating data in Member FD Module
        DB::beginTransaction();
        try {
            // Check if fdNo already exists
            $fdRecord = MemberFd::updateOrCreate(
                ['id' => $request->fdId],
                [
                    'serialNo' => $serialNo,
                    'membershipNo' => opening_accounts::where('accountNo',$request->accountNo)->value('membershipno'),
                    'accountNo' => $request->accountNo,
                    'fdNo' => $request->accountNo,
                    'memberType' => $request->memberType,
                    'groupCode' =>  $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'fdType' => $request->fdType,
                    'openingDate' => $request->openingDate,
                    'principalAmount' => $request->principalAmount,
                    'interestType' => $request->interestType,
                    'interestStartDate' => $request->interestStartDate,
                    'interestRate' => $request->interestRate,
                    'interestAmount' => $request->interestAmount,
                    'years' => $request->years,
                    'months' => $request->months,
                    'days' => $request->days,
                    'maturityDate' => $request->maturityDate,
                    'maturityAmount' => $request->maturityAmount,
                    'ledgerNo' => $request->ledgerNo,
                    'pageNo' => $request->pageNo,
                    'narration' => $request->narration,
                    'paymentType' => $request->paymentType,
                    'bank' => $request->bank,
                    'chequeNo' => $request->chequeNo,
                    'nomineeName1' => $request->nomineeName1,
                    'nomineeRelation1' => $request->nomineeRelation1,
                    'nomineeBirthDate1' => $request->nomineeBirthDate1,
                    'nomineePhone1' => $request->nomineePhone1,
                    'nomineeAddress1' => $request->nomineeAddress1,
                    'nomineeName2' => $request->nomineeName2,
                    'nomineeRelation2' => $request->nomineeRelation2,
                    'nomineeBirthDate2' => $request->nomineeBirthDate2,
                    'nomineePhone2' => $request->nomineePhone2,
                    'nomineeAddress2' => $request->nomineeAddress2,
                    'status' => $request->status,
                    'agentId' => $request->agentId,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );

            $fdId = $fdRecord->id;

            // Check if serialNo already exists
            $drLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $fdRecord->serialNo, 'transactionType' => 'Dr'],
                [
                    'accountId' => $member->id,
                    'accountNo' => $request->accountNo,
                    'memberType' => $request->memberType,
                    'formName' => 'fd',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->openingDate,
                    'transactionAmount' => $request->principalAmount,
                    'narration' => $request->narration,
                    'groupCode' => $request->paymentType,
                    'ledgerCode' => $request->bank,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $request->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );

            // Check if serialNo already exists
            $crLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $fdRecord->serialNo, 'transactionType' => 'Cr'],
                [
                    'accountId' => $member->id,
                    'accountNo' => $request->accountNo,
                    'memberType' => $request->memberType,
                    'formName' => 'fd',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->openingDate,
                    'transactionAmount' => $request->principalAmount,
                    'narration' => $request->narration,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $request->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Record Inserted or Updated successfully',
                'fdId' => $fdId
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function view($viewId)
    {
        $fd = MemberFd::findOrFail($viewId);
        $data = DB::table('opening_accounts')->where('membershipno', $fd->accountNo)->where(['accountname' => 'Saving'])->get();
        $savingAccNos = "";
        if (count($data) > 0 ) {
            $savingAccNos = '<ul class="list-group membersearch" style="display:block;z-index:1">';
            foreach ($data as $row) {
                $savingAccNos .= '<li class="list-group savingmemberlist">' . $row->accountNo . '</li>';
            }
            $savingAccNos .= '</ul>';
        } else {
            $savingAccNos .= '<li class="list-group-item savingmemberlist">No Data Found</li>';
        }
        return response()->json([
            'status' => true,
            'data' => $fd,
            'savingAccNos' => $savingAccNos
        ]);
    }

    public function mature(Request $request)
    {

        DB::beginTransaction();
        try {
            $fdRecord = MemberFd::findOrFail($request->matureId);
            $member = MemberAccount::where(['memberType' => $fdRecord->memberType, 'accountNo' => $fdRecord->accountNo])->first();

            // Check old maturity date
            // if ($request->renewDate < $oldFd->maturityDate) {
            //     return response()->json([
            //         'status' => false,
            //         'errors' => $validator->errors(),
            //         'message' => 'Renew date can not be less than Old Maturity date'
            //     ]);
            // }

            $fdRecord->update(
                [
                    'actualMaturityDate' => $request->matureDate,
                    'actualMaturityAmount' => $request->matureAmount,
                    'actualInterestAmount' => $request->matureInterest,
                    'narration' => $request->matureNarration,
                    "onmaturityDate" => $request->matureDate,
                    // 'transferedTo' => $request->transferedTo,
                    // 'transferedPaymentType' => $request->transferedPaymentType,
                    // 'transferedBank' => $request->transferedBank,
                    // 'transferedChequeNo' => $request->transferedChequeNo,
                    'status' => 'Matured',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            $fdId = $fdRecord->id;

            if ($request->transferType == 'Transfer') {
                if ($fdRecord->memberType == 'Member') {
                    $groupCode = 'SAVM001';
                    $ledgerCode = 'SAVM001';
                } else if ($fdRecord->memberType == 'NonMember') {
                    $groupCode = 'SAVN001';
                    $ledgerCode = 'SAVN001';
                } else {
                    $groupCode = 'SAVS001';
                    $ledgerCode = 'SAVS001';
                }
                $saving = new MemberSaving();
                $saving->serialNo = $fdRecord->serialNo;
                $saving->accountId =  $request->savingAccNo;
                $saving->memberType = $fdRecord->memberType;
                $saving->accountNo = $fdRecord->accountNo;
                $saving->transactionDate = $request->matureDate;
                $saving->transactionType = 'Deposit';
                $saving->depositAmount = $request->matureAmount;
                $saving->withdrawAmount = 0;
                $saving->paymentType = $fdRecord->groupCode;
                $saving->bank = $fdRecord->ledgerCode;
                $saving->narration = 'Transfered From FD [' . $fdRecord->fdNo . ']';
                $saving->groupCode = $groupCode;
                $saving->ledgerCode = $ledgerCode;
                $saving->chequeNo = "trfdFD";
                $saving->branchId = session('branchId') ? session('branchId') : 1;
                $saving->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $saving->updatedBy = $request->user()->id;
                $saving->save();
            }

            // Generate Serial No
            do {
                $serialNo = "fd" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

            // Select Group and Ledger Code
            if ($request->memberType == 'Member') {
                $groupCode = 'EXPN001';
                $ledgerCode = 'FDOM002';
            } else if ($request->memberType == 'NonMember') {
                $groupCode = 'EXPN001';
                $ledgerCode = 'FDOM002';
            } else {
                $groupCode = 'EXPN001';
                $ledgerCode = 'FDOM002';
            }

            // Credit Maturity Amount
            $crLedger = GeneralLedger::create(
                [
                    'serialNo' => $serialNo,
                    'transactionType' => 'Cr',
                    'accountId' => $member->id,
                    'accountNo' => $fdRecord->accountNo,
                    'memberType' => $fdRecord->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->matureDate,
                    'transactionAmount' => $request->matureAmount,
                    'narration' => $request->matureNarration,
                    'groupCode' => ($request->transferType == 'Transfer') ? $saving->groupCode : 'C002',
                    'ledgerCode' => ($request->transferType == 'Transfer') ? $saving->ledgerCode : 'C002',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $fdRecord->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit Principal Amount
            $drLedger = GeneralLedger::create(
                [
                    'serialNo' => $serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $fdRecord->accountNo,
                    'memberType' => $fdRecord->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->matureDate,
                    'transactionAmount' => $request->maturePrincipal,
                    'narration' => $request->matureNarration,
                    'groupCode' => $fdRecord->groupCode,
                    'ledgerCode' => $fdRecord->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $fdRecord->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit Interest
            $drLedger = GeneralLedger::create(
                [
                    'serialNo' => $serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $fdRecord->accountNo,
                    'memberType' => $fdRecord->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->matureDate,
                    'transactionAmount' => $request->matureInterest,
                    'narration' => $request->matureNarration,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $fdRecord->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'FD Matured Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function renew(Request $request)
    {

        $rules = array(
            'renewFdNo' => 'required|unique:member_fds,fdNo,' . $request->fdId . ',id',
            'renewDate' => 'required|date',
            'renewPrincipalAmount' => 'required|numeric',
            'renewInterestType' => 'required',
            'renewInterestStartDate' => 'required|date',
            'renewInterestRate' => 'required|numeric',
            'renewInterestAmount' => 'required|numeric',
            'renewYears' => 'nullable|integer',
            'renewMonths' => 'nullable|integer',
            'renewDays' => 'nullable|integer',
            'renewMaturityDate' => 'required|date',
            'renewMaturityAmount' => 'required|numeric'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Please check all inputs'
            ]);
        }

        // Generate Serial No
        do {
            $serialNo = "fd" . rand(1111111, 9999999);
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

        // Select Group and Ledger Code
        if ($request->memberType == 'Member') {
            $groupCode = 'EXPN001';
            $ledgerCode = 'FDOM002';
        } else if ($request->memberType == 'NonMember') {
            $groupCode = 'EXPN001';
            $ledgerCode = 'FDOM002';
        } else {
            $groupCode = 'EXPN001';
            $ledgerCode = 'FDOM002';
        }

        // Inserting or Updating data in Member FD Module
        DB::beginTransaction();
        try {

            $oldFd = MemberFd::findOrFail($request->renewId);
            if($oldFd->maturityDate > date('Y-m-d')){
                 return response()->json([
                'status' => false,
                'message' => 'Renew date should be grator then'.$oldFd->maturityDate ,
                'errors' => ""
            ]);
            }

            $newFd = $oldFd->replicate();
            $newFd->save();

            // Get member details
            $member = MemberAccount::where(['memberType' => $oldFd->memberType, 'accountNo' => $oldFd->accountNo])->first();

            // Check old maturity date
            // if ($request->renewDate < $oldFd->maturityDate) {
            //     return response()->json([
            //         'status' => false,
            //         'errors' => $validator->errors(),
            //         'message' => 'Renew date can not be less than Old Maturity date'
            //     ]);
            // }

            $newFd->update(
                [
                    'serialNo' => $serialNo,
                    'fdNo' => $request->renewFdNo,
                    'openingDate' => $request->renewDate,
                    'principalAmount' => $request->renewPrincipalAmount,
                    'interestType' => $request->renewInterestType,
                    'interestStartDate' => $request->renewInterestStartDate,
                    'interestRate' => $request->renewInterestRate,
                    'interestAmount' => $request->renewInterestAmount,
                    'years' => $request->renewYears,
                    'months' => $request->renewMonths,
                    'days' => $request->renewDays,
                    'maturityDate' => $request->renewMaturityDate,
                    'maturityAmount' => $request->renewMaturityAmount,
                    'narration' => $request->renewNarration,
                    'oldFdNo' => $oldFd->fdNo,
                    'status' => 'Active',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            $fdId = $newFd->id;

            // Credit Maturity Amount In Cash
            $crLedger = GeneralLedger::create(
                [
                    'serialNo' => $newFd->serialNo,
                    'transactionType' => 'Cr',
                    'accountId' => $member->id,
                    'accountNo' => $newFd->accountNo,
                    'memberType' => $newFd->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->renewDate,
                    'transactionAmount' => $oldFd->maturityAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => 'C002',
                    'ledgerCode' => 'C002',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit Principal Amount From FD
            $drLedger = GeneralLedger::create(
                [
                    'serialNo' => $newFd->serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $newFd->accountNo,
                    'memberType' => $newFd->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->renewDate,
                    'transactionAmount' => $oldFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $newFd->groupCode,
                    'ledgerCode' => $newFd->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit Interest Amount From FD
            $drLedger = GeneralLedger::create(
                [
                    'serialNo' => $newFd->serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $newFd->accountNo,
                    'memberType' => $newFd->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->renewDate,
                    'transactionAmount' => $oldFd->interestAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit Renewal Amount From Cash
            $drLedger = GeneralLedger::create(
                [
                    'serialNo' => $newFd->serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $newFd->accountNo,
                    'memberType' => $newFd->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->renewDate,
                    'transactionAmount' => $newFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => 'C002',
                    'ledgerCode' => 'C002',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Credit Renewal Amount Into FD
            $crLedger = GeneralLedger::create(
                [
                    'serialNo' => $newFd->serialNo,
                    'transactionType' => 'Cr',
                    'accountId' => $member->id,
                    'accountNo' => $newFd->accountNo,
                    'memberType' => $newFd->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $request->renewDate,
                    'transactionAmount' => $newFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $newFd->groupCode,
                    'ledgerCode' => $newFd->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );

            $oldFd->update(
                [
                    'actualMaturityDate' => $request->renewMaturityDate,
                    'actualInterestAmount' => $request->renewInterestAmount,
                    'actualMaturityAmount' => $request->renewMaturityAmount,
                    'narration' => $request->renewNarration,
                    'transferedTo' => $request->transferedTo,
                    'renewDate' => $request->renewMaturityDate,
                    'status' => 'Renewed',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'FD renewed successfully',
                'fdId' => $fdId
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function unmature(Request $request)
    {

        $fdRecord = MemberFd::findOrFail($request->unmatureId);
        if ($fdRecord->status == 'Matured') {
            DB::beginTransaction();
            try {
                $fdRecord->update(
                    [
                        'actualMaturityDate' => null,
                        'actualInterestAmount' => null,
                        'actualMaturityAmount' => null,
                        'transferedTo' => null,
                        'renewDate' => null,
                        'onmaturityDate' => null,
                        'status' => 'Active',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
                $drLedger = GeneralLedger::where('formName', 'FD')->where('referenceNo', $fdRecord->id)->where('serialNo', '!=', $fdRecord->serialNo)->where('transactionType', 'Dr')->delete();
                $crLedger = GeneralLedger::where('formName', 'FD')->where('referenceNo', $fdRecord->id)->where('serialNo', '!=', $fdRecord->serialNo)->where('transactionType', 'Cr')->delete();

                $savingRecord = MemberSaving::where('serialNo', $fdRecord->serialNo)->first();
                if (!empty($savingRecord)) {
                    $savingRecord->delete();
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'FD Unmatured Successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction Failed',
                    'errors' => $e->getMessage()
                ]);
            }
        } else if ($fdRecord->status == 'Renewed') {
            DB::beginTransaction();
            try {
                $fdRecord->update(
                    [
                        'actualMaturityDate' => null,
                        'actualInterestAmount' => null,
                        'actualMaturityAmount' => null,
                        'transferedTo' => null,
                        'renewDate' => null,
                        'status' => 'Active',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
                $renewedFd = MemberFd::where('oldFdNo', $fdRecord->fdNo)->first();
                $ledger = GeneralLedger::where('serialNo', $renewedFd->serialNo)->delete();
                $renewedFd->delete();

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'FD Unmatured Successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction Failed',
                    'errors' => $e->getMessage()
                ]);
            }
        }
    }

    public function destroy(Request $request)
    {
        $fd = MemberFd::findOrFail($request->deleteId);
        $serialNo = $fd->serialNo;
        try {
            $fd->update(['is_delete'=>'Yes']);
            $ledger = GeneralLedger::where('serialNo', $serialNo)->update(['is_delete'=>'Yes']);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Record deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function print(Request $request)
    {
        $fd = MemberFd::findOrFail($request->printId);
        $fd->save();
        return response()->json([
            'status' => true,
            'message' => 'FD Printed Successfully'
        ]);
    }
}
