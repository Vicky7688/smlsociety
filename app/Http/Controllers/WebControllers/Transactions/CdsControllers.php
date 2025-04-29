<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\AgentMaster;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\MemberAccount;
use App\Models\CompulsoryDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CdsControllers extends Controller
{
    public function index()
    {
        $groups = GroupMaster::where('headName','Bank')->get();
        $ledgers = LedgerMaster::where('groupcode', 'BANK001')->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        return view('transaction.cds', compact('groups', 'ledgers', 'agents'));
    }

    public function store(Request $request)
    {
        $rules = array(
            'transactionDate' => 'required',
            'transactionType' => 'required',
            'memberType' => 'required',
            'accountNo' => 'required',
            'transactionAmount' => 'required',
            'paymentType' => 'required',
            'bank' => 'required'
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

        // Check if member account exist or not
        if (empty($member)) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid account number'
            ]);
        }

        // Check account opening date
        if ($request->transactionDate < $member->openingDate) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Transaction date can not be less than account opening date'
            ]);
        }

        // Check saving account balance
        $savingAccount = CompulsoryDeposit::where(['accno' => $request->accountNo])->where('is_delete','!=','Yes')->whereDate('date', '<=', $request->transactionDate)->get();
      

        DB::beginTransaction();
        try {

            do {
                $serialNo = "cds" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

            if ($request->memberType == 'Member') {
                $groupCode = 'CDSM001';
                $ledgerCode = 'CDSM001';
            } 


            $saving = new CompulsoryDeposit();
            $saving->serialNo = $serialNo;
            $saving->accountId =  $member->id;
            $saving->accno = $request->accountNo;
            $saving->date = $request->transactionDate;
            // $saving->transactionType = $request->transactionType;
            $saving->membertype = $request->memberType;
            $saving->Deposit = ($request->transactionType == 'Deposit') ? $request->transactionAmount : 0;
            $saving->Withdraw = (in_array($request->transactionType, ['Withdraw', 'toshare', 'toFd', 'tord', 'toloan'])) ? $request->transactionAmount : 0;
            $saving->acc = $request->paymentType;
            $saving->Bank = $request->bank;
            $saving->type = "1";
            $saving->narrartion = $request->narration;
            $saving->Interest = "0.00" ;
            $saving->admissionfee = "0.00" ;
            $saving->ChqNo = "0" ;
            $saving->bankname = "0" ;
            $saving->SessionYear = session('sessionyear') ;
            $saving->Branch = session('Branchname') ;
            $saving->groupCode = $groupCode;
            $saving->ledgerCode = $ledgerCode; 
            $saving->logged_branch = session('branchId') ? session('branchId') : 1;
            $saving->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $saving->agent = $request->agentId; 
            $saving->LoginId = $request->user()->id; 
            $saving->save();

            $savingId = $saving->id;

            $ledger = new GeneralLedger();
            $ledger->serialNo = $serialNo;
            $ledger->accountId =  $member->id;
            $ledger->accountNo = $request->accountNo;
            $ledger->memberType = $request->memberType;
            $ledger->formName = 'CDS';
            $ledger->referenceNo = $savingId;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $request->transactionDate;
            $ledger->transactionType = 'Dr';
            $ledger->transactionAmount = $request->transactionAmount;
            $ledger->narration = $request->narration;
            $ledger->groupCode = ($request->transactionType == 'Deposit') ? $request->paymentType : $groupCode;
            $ledger->ledgerCode = ($request->transactionType == 'Deposit') ? $request->bank : $ledgerCode;
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = $request->user()->id;
            $ledger->save();



            $ledger = new GeneralLedger();
            $ledger->serialNo = $serialNo;
            $ledger->accountId =  $member->id;
            $ledger->accountNo = $request->accountNo;
            $ledger->memberType = $request->memberType;
            $ledger->formName = 'CDS';
            $ledger->referenceNo = $savingId;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $request->transactionDate;
            $ledger->transactionType = 'Cr';
            $ledger->transactionAmount = $request->transactionAmount;
            $ledger->narration = $request->narration;
            $ledger->groupCode = ($request->transactionType == 'Deposit') ? $groupCode : $request->paymentType;
            $ledger->ledgerCode = ($request->transactionType == 'Deposit') ? $ledgerCode : $request->bank;
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = $request->user()->id;
            $ledger->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Record Inserted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => "failed",
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function edit($modifyId) 
    {
        $saving = CompulsoryDeposit::findOrFail($modifyId);
        $accountNo = $saving->accno;
        $previousTransactions = CompulsoryDeposit::where('accno', $accountNo)->where('id', '<', $modifyId)->get();
        $previousBalance = $previousTransactions->sum('depositAmount') - $previousTransactions->sum('withdrawAmount');
        $ledgers = LedgerMaster::where('groupCode', "BANK001")->get();
        return response()->json([
            'status' => true,
            'data' => $saving,
            'ledgers' => $ledgers,
            'previousBalance' => $previousBalance
        ]);
    }

    public function update(Request $request)
    {
        $rules = array(
            'modifiedTransactionDate' => 'required',
            'modifiedTransactionType' => 'required',
            'modifiedTransactionAmount' => 'required',
            'modifiedPaymentType' => 'required',
            'modifiedBank' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Please check all inputs'
            ]);
        }
    
        $saving = MemberSaving::findOrFail($request->modifyId);
     
        // Calculate Pevious Balance Before Transaction Date
        $previousTransactions = MemberSaving::where('accountNo', $saving->accountNo)->where('id', '<', $request->modifyId)->get();
        $previousBalance = $previousTransactions->sum('depositAmount') - $previousTransactions->sum('withdrawAmount');

        // Calculate Saving Account Latest Balance
        $latestTransactions = MemberSaving::where('accountNo', $saving->accountNo)->get();
        $latestBalance = $latestTransactions->sum('depositAmount') - $latestTransactions->sum('withdrawAmount');

        // Check saving account balance
        if ($request->modifiedTransactionType == 'Withdraw') {
            if ($request->modifiedTransactionAmount > $previousBalance) {
                if ($saving->transactionType == 'Deposit') {
                    if (($saving->depositAmount + $request->modifiedTransactionAmount) > $latestBalance) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->errors(),
                            'message' => 'Action Not Allowed'
                        ]);
                    }
                }
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Insufficient Balance'
                ]);
            }
        } else {
            if (($saving->depositAmount - $request->modifiedTransactionAmount) > $latestBalance) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Action Not Allowed'
                ]);
            }
        }
      
        DB::beginTransaction();
        try {
         
            $saving->transactionDate = $request->modifiedTransactionDate;
            $saving->transactionType = $request->modifiedTransactionType;
            $saving->depositAmount = ($request->modifiedTransactionType == 'Deposit') ? $request->modifiedTransactionAmount : 0;
            $saving->withdrawAmount = ($request->modifiedTransactionType == 'Withdraw') ? $request->modifiedTransactionAmount : 0;
            $saving->paymentType = $request->modifiedPaymentType;
            $saving->bank = $request->modifiedBank;
            $saving->narration = $request->modifiedNarration;
            $saving->branchId = session('branchId') ? session('branchId') : 1;
            $saving->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $saving->agentId = $request->modifiedAgentId;
            $saving->updatedBy = $request->user()->id;
            $saving->save();
            $serialNo = $saving->serialNo;

            $drLedger = GeneralLedger::where('serialNo', $serialNo)->where('transactionType', 'Dr')->first();
            $crLedger = GeneralLedger::where('serialNo', $serialNo)->where('transactionType', 'Cr')->first();

            $drLedger->groupCode = ($request->modifiedTransactionType == 'Deposit') ? $request->modifiedPaymentType : $saving->groupCode;
            $drLedger->ledgerCode = ($request->modifiedTransactionType == 'Deposit') ? $request->modifiedBank : $saving->ledgerCode;
            $drLedger->transactionDate = $request->modifiedTransactionDate;
            $drLedger->transactionAmount = $request->modifiedTransactionAmount;
            $drLedger->narration = $request->modifiedNarration;
            $drLedger->branchId = session('branchId') ? session('branchId') : 1;
            $drLedger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $drLedger->agentId = $request->modifiedAgentId;
            $drLedger->updatedBy = $request->user()->id;
            $drLedger->save();

            $crLedger->groupCode = ($request->modifiedTransactionType == 'Deposit') ? $saving->groupCode :  $request->modifiedPaymentType; 
            $crLedger->ledgerCode = ($request->modifiedTransactionType == 'Deposit') ? $saving->ledgerCode : $request->modifiedBank; ;
            $crLedger->transactionDate = $request->modifiedTransactionDate;
            $crLedger->transactionAmount = $request->modifiedTransactionAmount;
            $crLedger->narration = $request->modifiedNarration;
            $crLedger->branchId = session('branchId') ? session('branchId') : 1;
            $crLedger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $crLedger->agentId = $request->modifiedAgentId;
            $crLedger->updatedBy = $request->user()->id;
            $crLedger->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Record updated successfully'
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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        $saving = MemberSaving::findOrFail($request->deleteId);
        $serialNo = $saving->serialNo;

        // Calculate Saving Account Latest Balance
        $latestTransactions = MemberSaving::where('accountNo', $saving->accountNo)->get();
        $latestBalance = $latestTransactions->sum('depositAmount') - $latestTransactions->sum('withdrawAmount');

        if ($saving->transactionType == 'Deposit') {
            if ($saving->depositAmount > $latestBalance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Action Not Allowed'
                ]);
            }
        }

        try {
            $saving->delete();

            $ledger = GeneralLedger::where('serialNo', $serialNo)->delete();

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
        $data = MemberAccount::where('memberType', $memberType)->where('accountNo', 'LIKE', $accountNo . '%')->get();
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
        if (!empty($accountNo)) {
            $openingBal = DB::table('opening_account_details')->where('AccountNumber',$accountNo)->where('TransferReason',"!=",'Deleted')->first();
            $cdsBal = $openingBal->OpeningCompulsoryDeposit ?? 0 ;
       
            $member = MemberAccount::where(['memberType' => $memberType, 'accountNo' => $accountNo])->first();
            $saving = CompulsoryDeposit::where(['membertype' => $memberType, 'accno' => $accountNo])->where('is_delete','!=',"Yes")->orderBy('date')->get();
            $savingBalance =   $saving->sum('depositAmount') - $saving->sum('withdrawAmount');

            return response()->json([
                'status' => true,
                'member' => $member,
                'openingBal' => $openingBal,
                'saving' => $saving,
                'balance' => $savingBalance
            ]);
        }
    }
}
