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
use App\Models\MemberFdScheme;
use App\Models\MemberSaving;
use App\Models\bankfd;
use App\Models\FdTypeMaster;
use App\Models\opening_accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Auth;
use App\Models\SessionMaster;
use Session;



class FDControllerScheme extends Controller
{
    public function bankindex(Request $request)
    {
        if (!empty($request->all())) {
            $validator = Validator::make($request->all(), [
                'fdnumber' => 'required|unique:bank_fd_deposit,fdnumber',
                'accountno' => 'required|unique:bank_fd_deposit,accountno'
            ]);

            if ($validator->fails()) {
                return back();
            }

            DB::beginTransaction();
            try {
                $addfd = new bankfd();
                $addfd->fddate = date('Y-m-d', strtotime($request->fddate));
                $addfd->fdnumber = $request->fdnumber;
                $addfd->fdaccountledger = $request->fdaccountledger;
                $addfd->accountno = $request->accountno;
                $addfd->amount = $request->amount;
                $addfd->intrestfrom = date('Y-m-d', strtotime($request->intrestfrom));
                $addfd->intrestrate = $request->intrestrate;
                $addfd->period = $request->period;
                $addfd->days = $request->days;
                $addfd->intresttype = $request->intresttype;
                $addfd->savingbank = $request->savingbank;
                $addfd->maturitydate = date('Y-m-d', strtotime($request->maturitydate));
                $addfd->maturityamount = $request->maturityamount;
                $addfd->save();


                //_________________Gerenal Ledger Entry
                if ($request->fdaccountledger) {
                    $general_legder_entry = new GeneralLedger();
                    $general_legder_entry->serialNo = $addfd->id;
                    $general_legder_entry->accountId = $request->accountno;
                    $general_legder_entry->accountNo = $request->fdnumber;
                    $general_legder_entry->groupCode = 'BANK001';
                    $general_legder_entry->ledgerCode = $request->fdaccountledger;
                    $general_legder_entry->formName = 'Bank FD';
                    $general_legder_entry->referenceNo = $addfd->id;
                    $general_legder_entry->entryMode = 'manual';
                    $general_legder_entry->transactionDate = date('Y-m-d', strtotime($request->fddate));
                    $general_legder_entry->transactionType = 'Dr';
                    $general_legder_entry->transactionAmount = $request->amount;
                    $general_legder_entry->narration = 'Bank FD';
                    $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
                    $general_legder_entry->agentId = 1;
                    $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $general_legder_entry->updatedBy = $request->user()->id;
                    $general_legder_entry->is_delete = 'No';
                    $general_legder_entry->save();
                }



                //_________________Gerenal Ledger Entry
                if ($request->savingbank) {
                    $general_legder_entry = new GeneralLedger();
                    $general_legder_entry->serialNo = $addfd->id;
                    $general_legder_entry->accountId = $request->accountno;
                    $general_legder_entry->accountNo = $request->fdnumber;
                    $general_legder_entry->groupCode = 'BANK001';
                    $general_legder_entry->ledgerCode = $request->savingbank;
                    $general_legder_entry->formName = 'Bank FD';
                    $general_legder_entry->referenceNo = $addfd->id;
                    $general_legder_entry->entryMode = 'manual';
                    $general_legder_entry->transactionDate = date('Y-m-d', strtotime($request->fddate));
                    $general_legder_entry->transactionType = 'Cr';
                    $general_legder_entry->transactionAmount = $request->amount;
                    $general_legder_entry->narration = 'Bank FD';
                    $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
                    $general_legder_entry->agentId = 1;
                    $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $general_legder_entry->updatedBy = $request->user()->id;
                    $general_legder_entry->is_delete = 'No';
                    $general_legder_entry->save();
                }
                DB::commit();
                return redirect('transactions/fdscheme/bank/index');
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Record not Found',
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $formurl = url('transactions/fdscheme/bank/index');
            $groups = GroupMaster::where('id', '<=', 2)->get();
            $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
            $agents = AgentMaster::orderBy('name', 'ASC')->get();
            $bank_fds = bankfd::orderBy('fdnumber', 'DESC')->get();
            return view('transaction.fdscheme.bankfd', compact('groups', 'ledgers', 'agents', 'bank_fds', 'formurl'));
        }
    }

    //__________________Edit Bank FD
    public function editbankfd(Request $request, $id)
    {
        $formurl = url('transactions/fdscheme/bank/index');
        $groups = GroupMaster::where('id', '<=', 2)->get();
        $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $bankFixedDeposit = bankfd::find($id);



        if (!empty($request->all())) {
            $bankFixedDeposit = bankfd::where('id', '!=', $id)->where('fdnumber', '=', $request->fdnumber)->where('accountno', '=', $request->accountno)->first();

            if ($bankFixedDeposit) {
                return back();
            }

            DB::beginTransaction();
            try {
                $bankFixedDepositupdate = bankfd::find($id);
                $bankFixedDepositupdate->fddate = date('Y-m-d', strtotime($request->fddate));
                $bankFixedDepositupdate->fdnumber = $request->fdnumber;
                $bankFixedDepositupdate->fdaccountledger = $request->fdaccountledger;
                $bankFixedDepositupdate->accountno = $request->accountno;
                $bankFixedDepositupdate->amount = $request->amount;
                $bankFixedDepositupdate->intrestfrom = date('Y-m-d', strtotime($request->intrestfrom));
                $bankFixedDepositupdate->intrestrate = $request->intrestrate;
                $bankFixedDepositupdate->period = $request->period;
                $bankFixedDepositupdate->days = $request->days;
                $bankFixedDepositupdate->intresttype = $request->intresttype;
                $bankFixedDepositupdate->savingbank = $request->savingbank;
                $bankFixedDepositupdate->maturitydate = date('Y-m-d', strtotime($request->maturitydate));
                $bankFixedDepositupdate->maturityamount = $request->maturityamount;
                $bankFixedDepositupdate->save();


                //_________________Gerenal Ledger Entry
                if ($request->fdaccountledger) {
                    $general_legder_entry = GeneralLedger::where('referenceNo', $id)->where('formName', 'Bank FD')->first();
                    $general_legder_entry->serialNo = $bankFixedDepositupdate->id;
                    $general_legder_entry->accountId = $request->accountno;
                    $general_legder_entry->accountNo = $request->fdnumber;
                    $general_legder_entry->groupCode = 'BANK001';
                    $general_legder_entry->ledgerCode = $request->fdaccountledger;
                    $general_legder_entry->formName = 'Bank FD';
                    $general_legder_entry->referenceNo = $bankFixedDepositupdate->id;
                    $general_legder_entry->entryMode = 'manual';
                    $general_legder_entry->transactionDate = date('Y-m-d', strtotime($request->fddate));
                    $general_legder_entry->transactionType = 'Dr';
                    $general_legder_entry->transactionAmount = $request->amount;
                    $general_legder_entry->narration = 'Bank FD';
                    $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
                    $general_legder_entry->agentId = 1;
                    $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $general_legder_entry->updatedBy = $request->user()->id;
                    $general_legder_entry->is_delete = 'No';
                    $general_legder_entry->save();
                }



                //_________________Gerenal Ledger Entry
                if ($request->savingbank) {
                    $general_legder_entry = GeneralLedger::where('referenceNo', $id)->where('formName', 'Bank FD')->first();
                    $general_legder_entry->serialNo = $bankFixedDepositupdate->id;
                    $general_legder_entry->accountId = $request->accountno;
                    $general_legder_entry->accountNo = $request->fdnumber;
                    $general_legder_entry->groupCode = 'BANK001';
                    $general_legder_entry->ledgerCode = $request->savingbank;
                    $general_legder_entry->formName = 'Bank FD';
                    $general_legder_entry->referenceNo = $bankFixedDepositupdate->id;
                    $general_legder_entry->entryMode = 'manual';
                    $general_legder_entry->transactionDate = date('Y-m-d', strtotime($request->fddate));
                    $general_legder_entry->transactionType = 'Cr';
                    $general_legder_entry->transactionAmount = $request->amount;
                    $general_legder_entry->narration = 'Bank FD';
                    $general_legder_entry->branchId = session('branchId') ? session('branchId') : 1;
                    $general_legder_entry->agentId = 1;
                    $general_legder_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $general_legder_entry->updatedBy = $request->user()->id;
                    $general_legder_entry->is_delete = 'No';
                    $general_legder_entry->save();
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Record not Found',
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $formurl = url('transactions/fdscheme/bank/index/' . $id);
            $groups = GroupMaster::where('id', '<=', 2)->get();
            $ledgers = LedgerMaster::where('groupCode', '=', 'BANK001')->get();
            $agents = AgentMaster::orderBy('name', 'ASC')->get();
            $bank_fds = bankfd::orderBy('fdnumber', 'DESC')->get();
            return view('transaction.fdscheme.bankfd', compact('groups', 'ledgers', 'agents', 'bank_fds', 'formurl', 'bankFixedDeposit'));
        }
    }

    public function index()
    {
        $groups = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $FdTypeMaster = FdTypeMaster::where('status', 'Active')->orderBy('type', 'ASC')->get();
        $FDSchemes = DB::table('scheme_masters')->where('secheme_type', '=', 'FD')->get();

        return view('transaction.fdscheme.fd', compact('groups', 'agents', 'FdTypeMaster', 'FDSchemes'));
    }



    public function getData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $fd_id = $request->fdType;
        $scheme_id = $request->scheme_id;
        $membershipno = $request->membershipno;

        $output = '';
        if (empty($accountNo)) {
            $output .= '<li class="list-group-item memberlist"></li>';
            return response()->json([
                'status' => true,
                'data' => $output
            ]);
        }
        $data = opening_accounts::where('membertype', $memberType)
            ->where('accountNo', 'LIKE', $accountNo . '%')
            ->where('accountname', 'FD')
            ->where('fdtypeid',$fd_id)
            ->where('schemetype',$scheme_id)
            ->where('membershipno',$membershipno)
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





    public function fetchData(Request $request)
    {


        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $membershipno = $request->membershipno;
        if (!empty($accountNo)) {
            $member = opening_accounts::where('opening_accounts.membertype', $memberType)
                ->where('opening_accounts.membershipno', $membershipno)
                ->where('opening_accounts.accountNo', $accountNo)
                ->where('opening_accounts.accountname', 'FD')
                ->where('scheme_masters.memberType',  $memberType)
                ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                    'scheme_masters.renewInterestType',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                )->first();

          $fd = MemberFdScheme::select('member_fds_scheme.*', 'scheme_masters.name as schemeName')
    ->join('scheme_masters', 'member_fds_scheme.secheme_id', '=', 'scheme_masters.id')
    ->where(['member_fds_scheme.membershipno' => $membershipno,'member_fds_scheme.accountNo' => $accountNo])
    ->where('member_fds_scheme.is_delete', '!=', 'Yes')
    ->orderBy('member_fds_scheme.openingDate')
    ->get();



            return response()->json([
                'status' => true,
                'member' => $member,
                'fd' => $fd
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Account number not provided or not found'
        ]);
    }



    public function store(Request $request)
    {
        $rules = array(
            'memberType' => 'required',
            'accountNo' => 'exists:member_accounts,accountNo',
            'fdType' => 'required',
            'membershipno' => 'required',
            'scheme_name' => 'required',
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
            'maturityAmount' => 'required'
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
        $members = MemberAccount::where(['memberType' => $request->memberType, 'accountNo' => $request->membershipno])->first();

        if (empty($members)) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid account number'
            ]);
        }



        $checkfdno = MemberFdScheme::where('accountNo', $request->accountNo)
            ->where('secheme_id', '=', $request->scheme_name)
            ->where('membershipno', $request->membershipno)
            ->where('is_delete', '!=', 'Yes')
            ->when(!empty($request->fdId), function ($query) use ($request) {
                $query->where('id', '!=', $request->fdId);
            })
            ->first();

        if ($checkfdno) {
            return response()->json([
                'status' => 'account',
                'message' => 'FD no. already exists'
            ]);
        }



        // Check account opening date
        if (Carbon::createFromFormat('d-m-Y', $request->openingDate)->format('Y-m-d') < $members->openingDate) {
            return response()->json([
                'status' => "",
                'message' => 'Transaction date cannot be less than account opening date'
            ]);
        }
        //  dd($request->memberType,$request->membershipno,$request->accountNo,$request->scheme_name);
        $member = opening_accounts::where('opening_accounts.membertype', $request->memberType)
            ->where('opening_accounts.membershipno', $request->membershipno)
            ->where('opening_accounts.accountNo', $request->accountNo)
            ->where('opening_accounts.accountname', 'FD')
            ->where('scheme_masters.memberType',  $request->memberType)
            // ->where('opening_accounts.schemetype',  $request->scheme_name)
            ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                'scheme_masters.renewInterestType',
                'ledger_masters.groupCode',
                'ledger_masters.ledgerCode',
            )->first();
        do {
            $serialNo = "fd" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

        if ($request->fdId != "new") {
            $checkfdno =  MemberFdScheme::where('id', $request->fdId)->where('is_delete', "!=", "Yes")->first();
            if ($checkfdno) {
                $serialNo = $checkfdno->serialNo;
            }
        }
        // Select Group and Ledger Code
        // if ($request->memberType == 'Member') {
        //     $groupCode = 'FDOM001';
        //     $ledgerCode = 'FDOM001';
        // } else if ($request->memberType == 'NonMember') {
        //     $groupCode = 'FDON001';
        //     $ledgerCode = 'FDON001';
        // } else {
        //     $groupCode = 'FDOS001';
        //     $ledgerCode = 'FDOS001';
        // }

        // Inserting or Updating data in Member FD Module
        DB::beginTransaction();
        try {
            // Check if fdNo already exists
            $fdRecord = MemberFdScheme::updateOrCreate(
                ['id' => $request->fdId],
                [
                    'secheme_id' => $member->schemetype,
                    'accountId' => $member->id,
                    'accountNo' => $request->accountNo,
                    'agentId' => $request->agentId,
                    'autorenew' => $request->autorenew,
                    'bank' => $request->bank,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'chequeNo' => $request->chequeNo,
                    'days' => $request->days,
                    'fdNo' => $request->accountNo,
                    'fdType' => $request->fdType,
                    'groupCode' => $member->groupCode,
                    'interestAmount' => $request->interestAmount,
                    'interestRate' => $request->interestRate,
                    'interestStartDate' => Carbon::createFromFormat('d-m-Y', $request->interestStartDate)->format('Y-m-d'),
                    'interestType' => $request->interestType,
                    'ledgerCode' => $member->ledgerCode,
                    'ledgerNo' => $request->ledgerNo,
                    'membershipno' => $member->membershipno,
                    'memberType' => $request->memberType,
                    'months' => $request->months,
                    'maturityAmount' => $request->maturityAmount,
                    'maturityDate' => Carbon::createFromFormat('d-m-Y', $request->maturityDate)->format('Y-m-d'),
                    'nomineeAddress1' => $request->nomineeAddress1,
                    'nomineeAddress2' => $request->nomineeAddress2,
                    'nomineeBirthDate1' => $request->nomineeBirthDate1,
                    'nomineeBirthDate2' => $request->nomineeBirthDate2,
                    'nomineeName1' => $request->nomineeName1,
                    'nomineeName2' => $request->nomineeName2,
                    'nomineePhone1' => $request->nomineePhone1,
                    'nomineePhone2' => $request->nomineePhone2,
                    'nomineeRelation1' => $request->nomineeRelation1,
                    'nomineeRelation2' => $request->nomineeRelation2,
                    'narration' => $request->narration,
                    'openingDate' => Carbon::createFromFormat('d-m-Y', $request->openingDate)->format('Y-m-d'),
                    'pageNo' => $request->pageNo,
                    'paymentType' => $request->paymentType,
                    'principalAmount' => $request->principalAmount,
                    'schemetype' => $request->scheme_name,
                    'serialNo' => $serialNo,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'status' => $request->status,
                    'updatedBy' => $request->user()->id,
                    'years' => $request->years,
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
                    'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->openingDate)->format('Y-m-d'),
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
                    'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->openingDate)->format('Y-m-d'),
                    'transactionAmount' => $request->principalAmount,
                    'narration' => $request->narration,
                    'groupCode' =>  $member->groupCode,
                    'ledgerCode' => $member->ledgerCode,
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
        $fd = MemberFdScheme::findOrFail($viewId);
        $scheme = DB::table('scheme_masters')->whereId($fd->secheme_id)->first();
        $data = DB::table('opening_accounts')->where('membershipno', $fd->membershipno)->where(['accountname' => 'Saving'])->get();
        $savingAccNos = "";

        if (count($data) > 0) {
            $transferTypeTransfer = true;
            $savingAccNos = '<ul class="list-group membersearch" style="display:block;z-index:1">';
            foreach ($data as $row) {
                $savingAccNos .= '<li class="list-group savingmemberlist">' . $row->accountNo . '</li>';
            }
            $savingAccNos .= '</ul>';
        } else {
            $transferTypeTransfer = false;
            $savingAccNos .= '<li class="list-group-item savingmemberlist">No Data Found</li>';
        }

        return response()->json([
            'status' => true,
            'data' => $fd,
            'scheme' => $scheme,
            'savingAccNos' => $savingAccNos,
            'transferTypeTransfer' => $transferTypeTransfer
        ]);
    }



    public function mature(Request $request)
    {

        DB::beginTransaction();
        try {
            $fdRecord = MemberFdScheme::findOrFail($request->matureId);
            // $member = opening_accounts::where(['memberType' => $fdRecord->memberType, 'accountNo' => $fdRecord->accountNo])->first();




            $member = opening_accounts::where('opening_accounts.membertype', $fdRecord->memberType)
                ->where('opening_accounts.accountNo', $fdRecord->accountNo)
                ->where('opening_accounts.accountname', 'FD')
                ->where('scheme_masters.memberType',  $fdRecord->memberType)
                ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                    'scheme_masters.renewInterestType',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                )->first();

            //__________Generate Serial No
            do {
                $serialNo = "fd" . time();
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

            $principal_amount = $request->maturePrincipal ? $request->maturePrincipal : 0;
            $paid_interest = $request->actualPayableInterest ? $request->actualPayableInterest : 0;
            $penality = $request->penaltyAmount ? $request->penaltyAmount : 0;
            $tds_amount = $request->TDSAmount;

            //______net amount
            $amount = ((($principal_amount + $paid_interest) - $penality) - $tds_amount);


            //________Expense
            $expense = DB::table('ledger_masters')
                ->where('reference_id', $member->schemetype)
                ->where('groupCode', '=', 'EXPN001')
                ->first();

            //________Income
            $income = DB::table('ledger_masters')
                ->where('reference_id', $member->schemetype)
                ->where('groupCode', '=', 'INCM001')
                ->first();



            if ($request->transferType == 'Transfer') {

                $accounts = opening_accounts::where('opening_accounts.membertype', $fdRecord->memberType)
                    ->where('opening_accounts.accountNo', $request->savingAccNo)
                    ->where('opening_accounts.accountname', 'Saving')
                    ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                    ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                    ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                        'scheme_masters.renewInterestType',
                        'ledger_masters.groupCode',
                        'ledger_masters.ledgerCode',
                    )->first();

                $fdRecord->update(
                    [
                        'actualMaturityDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'actualMaturityAmount' => $request->matureAmount,
                        'actualInterestAmount' => $request->matureInterest,
                        'narration' => $request->matureNarration,
                        "onmaturityDate" =>  Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        "TDSInterest" => $request->TDSInterest,
                        "TDSAmount" => $request->TDSAmount,
                        'status' => 'Matured',
                        'matureserialNo' => $serialNo,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
                $fdId = $fdRecord->id;

                //__________Saving Account
                $saving = new MemberSaving();
                $saving->serialNo = $fdRecord->serialNo;
                $saving->accountId =  $request->savingAccNo;
                $saving->memberType = $fdRecord->memberType;
                $saving->accountNo = $fdRecord->membershipno;
                $saving->transactionDate =  Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d');
                $saving->transactionType = 'Deposit';
                $saving->depositAmount = $amount;
                $saving->withdrawAmount = 0;
                $saving->paymentType = '';
                $saving->bank = '';
                $saving->narration = 'Transfered From FD [' . $fdRecord->fdNo . ']';
                $saving->groupCode = $accounts->groupCode;
                $saving->ledgerCode =   $accounts->ledgerCode;
                $saving->chequeNo = "trfdFD";
                $saving->branchId = session('branchId') ? session('branchId') : 1;
                $saving->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $saving->updatedBy = $request->user()->id;
                $saving->save();


                GeneralLedger::create([
                    'serialNo' => $serialNo,
                    'transactionType' => 'Cr',
                    'accountId' => $member->id,
                    'accountNo' => $request->savingAccNo,
                    'memberType' => $fdRecord->memberType,
                    'formName' => 'SavingFd',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                    'transactionAmount' => $amount,
                    'narration' => 'FD Acc' . $request->matureNarration,
                    'groupCode' => $accounts->groupCode,
                    'ledgerCode' => $accounts->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $fdRecord->agentId,
                    'updatedBy' => $request->user()->id,
                ]);

                GeneralLedger::create([
                    'serialNo' => $serialNo,
                    'transactionType' => 'Dr',
                    'accountId' => $member->id,
                    'accountNo' => $fdRecord->accountNo,
                    'memberType' => $fdRecord->memberType,
                    'formName' => 'FD',
                    'referenceNo' => $fdId,
                    'entryMode' => 'Manual',
                    'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                    'transactionAmount' => $principal_amount,
                    'narration' => 'FD Acc' . $request->matureNarration,
                    'groupCode' => $fdRecord->groupCode,
                    'ledgerCode' => $fdRecord->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $fdRecord->agentId,
                    'updatedBy' => $request->user()->id,
                ]);


                if ($paid_interest > 0) {

                    $interest_amount = 0;
                    if ($tds_amount > 0 || $penality > 0) {
                        $interest_amount = ($paid_interest  - $tds_amount);
                    } else {
                        $interest_amount = $paid_interest;
                    }


                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Dr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt Paid' . $request->matureNarration,
                        'groupCode' => $expense->groupCode,
                        'ledgerCode' => $expense->ledgerCode,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }


                if ($penality > 0) {

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Cr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'FD P.Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $penality,
                        'narration' => 'Penality' . $request->matureNarration,
                        'groupCode' => $income->groupCode,
                        'ledgerCode' => $income->ledgerCode,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }


                if ($tds_amount > 0) {

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Dr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'TDS PAid FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $tds_amount,
                        'narration' => 'TDS Dedcu' . $request->matureNarration,
                        'groupCode' => 'GRTTDS01',
                        'ledgerCode' => 'LGRTDS01',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Cr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'TDS Payable FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $tds_amount,
                        'narration' => 'TDS Payable' . $request->matureNarration,
                        'groupCode' => 'GRTTDS02',
                        'ledgerCode' => 'LGRTDS02',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }
            } else {

                $fdRecord->update(
                    [
                        'actualMaturityDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'actualMaturityAmount' => $request->matureAmount,
                        'actualInterestAmount' => $request->matureInterest,
                        'narration' => $request->matureNarration,
                        "onmaturityDate" => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        "TDSInterest" => $request->TDSInterest,
                        "TDSAmount" => $request->TDSAmount,
                        'matureserialNo' => $serialNo,
                        'status' => 'Matured',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );


                $fdId = $fdRecord->id;



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
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $request->maturePrincipal,
                        'narration' => $request->matureNarration,
                        'groupCode' => $member->groupCode,
                        'ledgerCode' => $member->ledgerCode,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]
                );

                $drLedger = GeneralLedger::create(
                    [
                        'serialNo' => $serialNo,
                        'transactionType' => 'Cr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'FD',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $request->matureAmount,
                        'narration' => $request->matureNarration,
                        'groupCode' => 'C002',
                        'ledgerCode' => 'C002',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]
                );



                // Debit Interest
                if ($paid_interest > 0) {

                    $interest_amount = 0;
                    if ($tds_amount > 0 || $penality > 0) {
                        $interest_amount = ($paid_interest  - $tds_amount);
                    } else {
                        $interest_amount = $paid_interest;
                    }


                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Dr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt Paid' . $request->matureNarration,
                        'groupCode' => $expense->groupCode,
                        'ledgerCode' => $expense->ledgerCode,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }


                if ($penality > 0) {

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Cr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'FD P.Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $penality,
                        'narration' => 'Penality' . $request->matureNarration,
                        'groupCode' => $income->groupCode,
                        'ledgerCode' => $income->ledgerCode,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }


                if ($tds_amount > 0) {

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Dr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'TDS PAid FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $tds_amount,
                        'narration' => 'TDS Dedcu' . $request->matureNarration,
                        'groupCode' => 'GRTTDS01',
                        'ledgerCode' => 'LGRTDS01',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);

                    GeneralLedger::create([
                        'serialNo' => $serialNo,
                        'transactionType' => 'Cr',
                        'accountId' => $member->id,
                        'accountNo' => $fdRecord->accountNo,
                        'memberType' => $fdRecord->memberType,
                        'formName' => 'TDS Payable FD Interest',
                        'referenceNo' => $fdId,
                        'entryMode' => 'Manual',
                        'transactionDate' => Carbon::createFromFormat('d-m-Y', $request->matureDate)->format('Y-m-d'),
                        'transactionAmount' => $tds_amount,
                        'narration' => 'TDS Payable' . $request->matureNarration,
                        'groupCode' => 'GRTTDS02',
                        'ledgerCode' => 'LGRTDS02',
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $fdRecord->agentId,
                        'updatedBy' => $request->user()->id,
                    ]);
                }
            }

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












    // Function to calculate the maturity date
    private function calculateRenewMaturityDate($renewDate, $renewYears, $renewMonths, $renewDays)
    {
        $maturityDate = new \DateTime($renewDate);
        $maturityDate->modify("+$renewYears years");
        $maturityDate->modify("+$renewMonths months");
        $maturityDate->modify("+$renewDays days");

        return $maturityDate->format('Y-m-d');
    }

    private function calculateRenewInterestAmount($interestType, $principal, $rate, $interestStartDate, $maturityDate)
    {
        $interestStartDate = new \DateTime($interestStartDate);
        $maturityDate = new \DateTime($maturityDate);

        $interval = $interestStartDate->diff($maturityDate);
        $time = $interval->days;

        $interest = 0;
        if ($interestType == 'Fixed') {
            $interest = ($principal * $rate * $time) / (365 * 100);
        } else if ($interestType == 'AnnualCompounded') {
            $interest = $principal * pow(1 + (($rate / 100) / 1), ($time / 365)) - $principal;
        } else {
            $interest = $principal * pow(1 + (($rate / 100) / 4), (4 * $time / 365)) - $principal;
        }

        $interest = round($interest, 2);
        $maturityAmount = round($principal + $interest, 2);

        return ['interestAmount' => $interest, 'maturityAmount' => $maturityAmount];
    }


    public function checkmatured(Request $request)
    {

        $memberfdscheme = MemberFdScheme::where('maturityDate', '<=', now())
                                         ->where('autorenew', '=', 'yes')
                                         ->where('status', '=', 'Active')
                                         ->get();

      if(sizeof($memberfdscheme)>0){
        foreach ($memberfdscheme as $oldFd) {

            do {
                $serialNo = "fd" . time();
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

            DB::beginTransaction();
        try {





            $renewDate = $oldFd->maturityDate;
            $renewYears = $oldFd->years;
            $renewMonths = $oldFd->months;
            $renewDays = $oldFd->days;


            $maturityDate = $this->calculateRenewMaturityDate($renewDate, $renewYears, $renewMonths, $renewDays);


            $interestType = $oldFd->interestType;
            $principal = $oldFd->principalAmount;
            $rate = $oldFd->interestRate;
            $interestStartDate = $oldFd->interestStartDate;

            $interestResult = $this->calculateRenewInterestAmount($interestType, $principal, $rate, $interestStartDate, $maturityDate);



            $newFd = $oldFd->replicate();
            $newFd->save();

            $member = opening_accounts::where('opening_accounts.membertype', $oldFd->memberType)
                ->where('opening_accounts.accountNo', $oldFd->accountNo)
                ->where('opening_accounts.accountname', 'FD')
                ->where('scheme_masters.memberType',  $oldFd->memberType)
                ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                    'scheme_masters.renewInterestType',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                )->first();


                $expense = DB::table('ledger_masters')
                    ->where('reference_id', $member->schemetype)
                    ->where('groupCode', '=', 'EXPN001')
                    ->first();

                //________Income
                $income = DB::table('ledger_masters')
                    ->where('reference_id', $member->schemetype)
                    ->where('groupCode', '=', 'INCM001')
                    ->first();

            $newFd->update(
                [
                    'serialNo' => $serialNo,
                    'fdNo' => $oldFd->fdNo,
                    'openingDate' => $renewDate,
                    'principalAmount' => $oldFd->maturityAmount,
                    'interestType' => $interestType,
                    'interestStartDate' => $renewDate,
                    'interestRate' => $rate,
                    'interestAmount' => $interestResult['interestAmount'],
                    'years' => $renewYears,
                    'months' => $renewMonths,
                    'days' => $renewDays,
                    'maturityDate' => $maturityDate,
                    'maturityAmount' => $interestResult['maturityAmount'],
                    'narration' => $oldFd->narration,
                    'oldFdNo' => $oldFd->fdNo,
                    'status' => 'Active',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            $fdId = $newFd->id;
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
                    'transactionDate' => $renewDate,
                    'transactionAmount' => $oldFd->maturityAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => 'C002',
                    'ledgerCode' => 'C002',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $oldFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
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
                    'transactionDate' => $renewDate,
                    'transactionAmount' => $oldFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $newFd->groupCode,
                    'ledgerCode' => $newFd->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $oldFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );


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
                    'transactionDate' => $renewDate,
                    'transactionAmount' => $oldFd->interestAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $expense->groupCode,
                    'ledgerCode' => $expense->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $oldFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );




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
                    'transactionDate' => $renewDate,
                    'transactionAmount' => $newFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => 'C002',
                    'ledgerCode' => 'C002',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $oldFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );
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
                    'transactionDate' => $renewDate,
                    'transactionAmount' => $newFd->principalAmount,
                    'narration' => $newFd->narration,
                    'groupCode' => $newFd->groupCode,
                    'ledgerCode' => $newFd->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $oldFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );

            $oldFd->update(
                [
                    'actualMaturityDate' =>  $renewDate,
                    'actualInterestAmount' => $interestResult['interestAmount'],
                    'actualMaturityAmount' => $oldFd->maturityAmount,
                    'narration' => $oldFd->narration,
                    'transferedTo' => $request->transferedTo,
                    'renewDate' => $maturityDate,
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
    }else{
        return response()->json([
            'status' => true,
            'message' => 'No auto-renew FD schemes found',
        ]);
    }

      // Return the response with the calculated values



// If you want to return something else when no FD schemes are found

    }

    public function renew(Request $request){
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
        do {
            $serialNo = "fd" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

        DB::beginTransaction();
        try {

            $oldFd = MemberFdScheme::findOrFail($request->renewId);
            if ($oldFd->maturityDate > date('Y-m-d')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Renew date should be grator then' . $oldFd->maturityDate,
                    'errors' => ""
                ]);
            }

            $newFd = $oldFd->replicate();
            $newFd->save();

            $member = opening_accounts::where('opening_accounts.membertype', $oldFd->memberType)
                ->where('opening_accounts.accountNo', $oldFd->accountNo)
                ->where('opening_accounts.accountname', 'FD')
                ->where('scheme_masters.memberType',  $oldFd->memberType)
                ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
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
                    'scheme_masters.renewInterestType',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                )->first();


                $expense = DB::table('ledger_masters')
                    ->where('reference_id', $member->schemetype)
                    ->where('groupCode', '=', 'EXPN001')
                    ->first();

                //________Income
                $income = DB::table('ledger_masters')
                    ->where('reference_id', $member->schemetype)
                    ->where('groupCode', '=', 'INCM001')
                    ->first();


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
                    'groupCode' => $expense->groupCode,
                    'ledgerCode' => $expense->ledgerCode,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $newFd->agentId,
                    'updatedBy' => $request->user()->id,
                ]
            );




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

        $fdRecord = MemberFdScheme::findOrFail($request->unmatureId);
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


                GeneralLedger::where('serialNo', $fdRecord->matureserialNo)->where('referenceNo', $fdRecord->id)->delete();


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
                $renewedFd = MemberFdScheme::where('oldFdNo', $fdRecord->fdNo)->first();
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
        $fd = MemberFdScheme::findOrFail($request->deleteId);
        $serialNo = $fd->serialNo;
        DB::beginTransaction();
        try {
            $fd->delete();
            GeneralLedger::where('serialNo', $serialNo)->delete();
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
        $fd = MemberFdScheme::findOrFail($request->printId);
        $fd->save();
        return response()->json([
            'status' => true,
            'message' => 'FD Printed Successfully'
        ]);
    }




    public function getfdschemes(Request $post){
        $fdType = $post->fdType	;
        $schems = DB::table('scheme_masters')->where('secheme_type', '=','FD')->where('fdtype', '=',$fdType)->where('status', '=','Active')->get();
        if(!empty($schems)){
        return response()->json(['status' => 'success','schemesType' => $schems]);
        } else{
            return response()->json(['status' => 'Fail', 'messages'  => 'Record Not Found']);
        }
    }

}
