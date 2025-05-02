<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MemberAccount;
use App\Models\MemberSaving;
use App\Models\MemberShare;
use App\Models\MemberFd;
use App\Models\Mis;
use App\Models\MemberLoan;
use App\Models\ReCurringRd;
use App\Models\LedgerMaster;
use App\Models\LoanRecovery;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function updateledger()
    {


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////
        // $mem = LoanRecovery::where('is_delete', 'Yes')->pluck('id');

        // $records = GeneralLedger::where('formName', 'LoanReceipt')
        //     ->whereIn('referenceNo', $mem)
        //     ->get();
        //     foreach($records as $recordslist){
        //        $update= GeneralLedger::find($recordslist->id);
        //        $update->is_delete="Yes";
        //        $update->save();
        //     }


        // $memm = MemberLoan::where('is_delete', 'Yes')->pluck('id');

        // $recordss = GeneralLedger::where('formName', 'LoanDisbursed')
        //     ->whereIn('referenceNo', $memm)
        //     ->get();
        //     foreach($recordss as $recordsslist){
        //        $update= GeneralLedger::find($recordsslist->id);
        //        $update->is_delete="Yes";
        //        $update->save();
        //     }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////


        //         $mem=DB::table('member_shares')->get();
        // foreach($mem as $memm){






        //     DB::table('general_ledgers')->insert([
        //         "serialNo" => 'shr4655948',
        //         "accountId"  =>  $memm->accountId,
        //         'accountNo' => $memm->accountNo,
        //         'memberType' => $memm->memberType,
        //         'agentId' => $memm->agentId,
        //         'ledgerCode' => "C002",
        //         'groupCode' => "C002",
        //         'formName'   => "Share",
        //         'referenceNo' => $memm->id,
        //         'transactionDate' => $memm->transactionDate,
        //         'transactionType' => 'Cr',
        //         'transactionAmount' => $memm->depositAmount,
        //         'narration' => $memm->narration,
        //         'branchId' =>  1,
        //         'sessionId' =>2,
        //         'updatedBy' => 2,
        //     ]);

        //     DB::table('general_ledgers')->insert([
        //         "serialNo" =>'shr4655948',
        //         "accountId"  =>  $memm->accountId,
        //         'accountNo' => $memm->accountNo,
        //         'memberType' => $memm->memberType,
        //         'agentId' =>$memm->agentId,
        //         'groupCode' => "SHAM001",
        //         'ledgerCode' => "SHAM001",
        //         'formName'   => "Share",
        //         'referenceNo' =>  $memm->id,
        //         'transactionDate' => $memm->transactionDate,
        //         'transactionType' => 'Dr',
        //         'transactionAmount' => $memm->depositAmount,
        //         'narration' => $memm->narration,
        //         'branchId' =>  1,
        //         'sessionId' =>2,
        //         'updatedBy' => 2,
        //     ]);


        // }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////Membersaving////////////////Membersaving////////////////////////////////Membersaving////////////////Membersaving////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // $mem=MemberSaving::all();
        // foreach($mem as $memm){
        //                $serialNo = "loan".rand(1111,9999).time();

        //                if ($memm->memberType == 'Member') {
        //                 $groupCode = 'SAVM001';
        //                 $ledgerCode = 'SAVM001';
        //             } else if ($memm->memberType == 'NonMember') {
        //                 $groupCode = 'SAVN001';
        //                 $ledgerCode = 'SAVN001';
        //             } else {
        //                 $groupCode = 'SAVS001';
        //                 $ledgerCode = 'SAVS001';
        //             }



        //             if($memm->transactionType=='Deposit'){
        //                 $transactionDa=$memm->depositAmount;


        //                 if($memm->groupCode=='SAVM001'){
        //                     $groupCode=$memm->paymentType;
        //                     $ledgerCode=$memm->bank;
        //                 }

        //                 if($memm->groupCode=='EXPN001'){
        //                     $groupCode=$memm->groupCode;
        //                     $ledgerCode=$memm->ledgerCode;
        //                 }
        //             }else{

        //                 $transactionDa=$memm->withdrawAmount;



        //                     $groupCode=$memm->groupCode;
        //                     $ledgerCode=$memm->ledgerCode;


        //             }







        //                $ledger = new GeneralLedger();
        //                $ledger->serialNo = $memm->serialNo;
        //                $ledger->accountId =  $memm->accountId;
        //                $ledger->accountNo = $memm->accountNo;
        //                $ledger->memberType = $memm->memberType;
        //                $ledger->formName = 'Saving';
        //                $ledger->referenceNo = $memm->id;
        //                $ledger->entryMode = 'Manual';
        //                $ledger->transactionDate = $memm->transactionDate;
        //                $ledger->transactionType = 'Dr';
        //                $ledger->transactionAmount = $transactionDa;
        //                $ledger->narration = $memm->narration;
        //                $ledger->groupCode = $groupCode;
        //                $ledger->ledgerCode = $ledgerCode;
        //                $ledger->branchId = $memm->branchId;
        //                $ledger->sessionId =  $memm->sessionId;
        //                $ledger->agentId = 1;
        //                $ledger->updatedBy = 2;
        //                $ledger->save();


        //             if($memm->transactionType=='Deposit'){
        //                 $transactionDa=$memm->depositAmount;


        //                 if($memm->groupCode=='SAVM001'){
        //                     $groupCode=$memm->groupCode;
        //                     $ledgerCode=$memm->ledgerCode;
        //                 }
        //                 if($memm->groupCode=='EXPN001'){
        //                     $groupCode='SAVM001';
        //                     $ledgerCode='SAVM001';
        //                 }
        //             }else{

        //                 $transactionDa=$memm->withdrawAmount;


        //                 $groupCode=$memm->paymentType;
        //                 $ledgerCode=$memm->bank;

        //             }

        //                $ledger = new GeneralLedger();
        //                $ledger->serialNo = $memm->serialNo;
        //                $ledger->accountId =  $memm->accountId;
        //                $ledger->accountNo = $memm->accountNo;
        //                $ledger->memberType = $memm->memberType;
        //                $ledger->formName = 'Saving';
        //                $ledger->referenceNo = $memm->id;
        //                $ledger->entryMode = 'Manual';
        //                $ledger->transactionDate = $memm->transactionDate;
        //                $ledger->transactionType = 'Cr';
        //                $ledger->transactionAmount = $transactionDa;
        //                $ledger->narration = $memm->narration;
        //                $ledger->groupCode = $groupCode;
        //                $ledger->ledgerCode = $ledgerCode;
        //                $ledger->branchId = $memm->branchId;
        //                $ledger->sessionId =  $memm->sessionId;
        //                $ledger->agentId = 1;
        //                $ledger->updatedBy = 2;
        //                $ledger->save();

        // }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////LoanRecovery////////////////LoanRecovery////////////////////////////////LoanRecovery////////////////LoanRecovery////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////LoanRecovery////////////////LoanRecovery////////////////////////////////LoanRecovery////////////////LoanRecovery////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //          $mem=LoanRecovery::all();
        //          foreach($mem as $memm){
        //             $generalLedgers = "loan".rand(1111,9999).time();


        //             $member_loans=DB::table('member_loans')->where('id','=',$memm->loanId)->first();
        //             if ($memm->receivedBy == "Transfer") {
        //                 $ledgerMasterCR = LedgerMaster::where('id', 80)->first(['groupCode', 'ledgerCode']);
        //                 if (!$ledgerMasterCR) {
        //                     return response()->json(['status' => "Invalid Bank or Type"]);
        //                 }
        //             } else {
        //                 $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
        //             }

        // if(empty($member_loans->accountNo)){
        // }else{






        //     $intGroupCode = "INCM001";
        //     if ($member_loans->memberType == "Member") {
        //         $pricpleCode = "LONM001";
        //         $curentintCode = 'LONM002';
        //         $penalCode = "LONM003";
        //         $pendingCode = "LONM004";
        //     } else if ($member_loans->memberType == "NonMember") {
        //         $pricpleCode = "LONN001";
        //         $curentintCode = 'LONN002';
        //         $penalCode = "LONN003";
        //         $pendingCode = "LONN004";
        //     } else if ($member_loans->memberType == "Staff") {
        //         $pricpleCode = "LONS001";
        //         $curentintCode = 'LONS002';
        //         $penalCode = "LONS003";
        //         $pendingCode = "LONS004";
        //     }





        //             DB::table('general_ledgers')->insert([
        //                 "serialNo" => $generalLedgers,
        //                 "accountId"  =>  $member_loans->accountId,
        //                 'accountNo' => $member_loans->accountNo,
        //                 'memberType' => $member_loans->memberType,
        //                 'agentId' => $member_loans->agentId,
        //                 "ledgerCode"   => $ledgerMasterCR->ledgerCode,
        //                 'groupCode' =>  $ledgerMasterCR->groupCode,
        //                 'referenceNo' => $memm->id,
        //                 'entryMode' => "manual",
        //                 'transactionDate' => $memm->receiptDate,
        //                 'transactionType' => 'Dr',
        //                 "formName"        => "LoanReceipt",
        //                 'transactionAmount' => $memm->receivedAmount,
        //                 'narration' =>'recovry',
        //                 'branchId' =>  $member_loans->branchId,
        //                 'sessionId' => $member_loans->sessionId,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updatedBy' => 2,
        //             ]);
        //             $insert = [
        //                 "serialNo" => $generalLedgers,
        //                 "accountId"  =>  $member_loans->accountId,
        //                 'accountNo' => $member_loans->accountNo,
        //                 'memberType' => 'Member',
        //                 'agentId' =>  $member_loans->agentId,
        //                 'referenceNo' => $memm->id,
        //                 'entryMode' => "manual",
        //                 'transactionDate' => $memm->receiptDate,
        //                 'transactionType' => 'Cr',
        //                 "formName"        => "LoanReceipt",
        //                 'narration' => 'recovry',
        //                 'branchId' =>  $member_loans->branchId,
        //                 'sessionId' => $member_loans->sessionId,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updatedBy' => 2,
        //             ];
        //             /*   insert penal  interest transaction */
        //             if ($memm->penalInterest > 0) {
        //                 $insert["ledgerCode"]  = $penalCode;
        //                 $insert["groupCode"]  = $intGroupCode;
        //                 $insert['transactionAmount'] = $memm->penalInterest;
        //                 DB::table('general_ledgers')->insert($insert);
        //             }

        //             /*   insert pending  interest transaction */
        //             // if ($post->PendingIntrTillDate > 0) {

        //             //     $insert["ledgerCode"]  = $pendingCode;
        //             //     $insert["groupCode"]  = $intGroupCode;
        //             //     $insert['transactionAmount'] = $post->PendingIntrTillDate;
        //             //     DB::table('general_ledgers')->insert($insert);
        //             // }
        //             /*   insert current  interest transaction */
        //             if ($memm->interest > 0) {

        //                 $insert["ledgerCode"]  = $curentintCode;
        //                 $insert["groupCode"]  = $intGroupCode;
        //                 $insert['transactionAmount'] = $memm->interest;
        //                 DB::table('general_ledgers')->insert($insert);
        //             }

        //             /*   insert princple transaction */
        //             if ($memm->principal > 0) {

        //                 $insert["ledgerCode"]  = $pricpleCode;
        //                 $insert["groupCode"]  = $pricpleCode;
        //                 $insert['transactionAmount'] = $memm->principal;
        //                 DB::table('general_ledgers')->insert($insert);
        //             }
        //             if ($memm->overDueInterest > 0) {
        //                 $insert["ledgerCode"]  = $pendingCode;
        //                 $insert["groupCode"]  = $intGroupCode;
        //                 $insert['transactionAmount'] = $memm->overDueInterest;
        //                 DB::table('general_ledgers')->insert($insert);
        //             }




        //          }
        //         }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////advancement Ledger////////////////advancement Ledger////////////////////////////////advancement Ledger////////////////advancement Ledger////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // $mem=MemberLoan::all();
        // foreach($mem as $memm){
        //      $generalLedgers = "loan".rand(1111,9999).time();



        //      if ($memm->memberType == "Member") {
        //         $ledgerMaster = LedgerMaster::where('ledgerCode', "LONM001")->first(['groupCode', 'ledgerCode']);
        //     } else if ($memm->memberType == "NonMember") {
        //         $ledgerMaster = LedgerMaster::where('ledgerCode', "LONN001")->first(['groupCode', 'ledgerCode']);
        //     } else if ($memm->memberType == "Staff") {
        //         $ledgerMaster = LedgerMaster::where('ledgerCode', "LONS001")->first(['groupCode', 'ledgerCode']);
        //     }


        //     if ($memm->loanBy == "Transfer") {
        //         $ledgerMasterCR = LedgerMaster::where('id', 80)->first(['groupCode', 'ledgerCode']);
        //         if (!$ledgerMasterCR) {
        //             return response()->json(['status' => "Invalid Bank or Type"]);
        //         }
        //     } else {
        //         $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
        //     }



        //     DB::table('general_ledgers')->insert([
        //         "serialNo" => $generalLedgers,
        //         'accountNo' => $memm->accountNo,
        //         "accountId"  =>  $memm->accountId,
        //         'memberType' => $memm->memberType,
        //         'agentId' => $memm->agentId,
        //         "ledgerCode"   => $ledgerMasterCR->ledgerCode,
        //         'groupCode' =>  $ledgerMasterCR->groupCode,
        //         'referenceNo' => $memm->id,
        //         'entryMode' => "automatic",
        //         "formName"        => "LoanDisbursed",
        //         'transactionDate' => $memm->loanDate,
        //         'transactionType' => 'Cr',
        //         'transactionAmount' => $memm->loanAmount,
        //         'narration' => '',
        //         'branchId' =>  $memm->branchId,
        //         'sessionId' => $memm->sessionId,
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updatedBy' => 2,
        //     ]);

        //     DB::table('general_ledgers')->insert([
        //         "serialNo" => $generalLedgers,
        //         'accountNo' =>  $memm->accountNo,
        //         "accountId"  =>  $memm->accountId,
        //         'memberType' => $memm->memberType,
        //         'agentId' => $memm->agentId,
        //         "ledgerCode"   => $ledgerMaster->ledgerCode,
        //         'groupCode' =>  $ledgerMaster->groupCode,
        //         'referenceNo' => $memm->id,
        //         'entryMode' => "automatic",
        //         "formName" => "LoanDisbursed",
        //         'transactionDate' => $memm->loanDate,
        //         'transactionType' => 'Dr',
        //         'transactionAmount' => $memm->loanAmount,
        //         'narration' => '',
        //         'branchId' =>  $memm->branchId,
        //         'sessionId' => $memm->sessionId,
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updatedBy' => 2,
        //     ]);



        // }
    }
    public function comingsoon()
    {
        return view('comingsoon');
    }

    public function index(Request $post)
    {
        $data['title'] = "Dashboard";
        $data['memberac'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'Member')->count();
        $data['nonmember'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'NonMember')->count();
        $data['staff'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'Staff')->count();
        $data['memberfd'] = DB::table('member_fds_scheme')->where('is_delete', 'no')->count();
        $data['membersaving'] = DB::table('member_savings')->where('is_delete', 'no')->count();
        $data['memberrd'] = ReCurringRd::where('is_delete', 'no')->count();
        $data['membershare'] = MemberShare::where('is_delete', 'no')->count();
        $data['memberloan'] = MemberLoan::where('is_delete', 'no')->count();
        // $data['membermis'] = Mis::where('is_delete', 'no')->count();

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $previous_amount = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');

        $cashcode = LedgerMaster::where(['ledgerCode' => 'C002'])->first();

        // Calculate debit and credit amounts for opening balance
        // $debit_amount = DB::table('general_ledgers')
        //     ->where('ledgerCode', $cashcode->ledgerCode)
        //     ->whereDate('transactionDate', '<', $start_date)
        //     ->where('transactionType', 'Dr')
        //     ->where('is_delete', 'No')
        //     ->sum('transactionAmount');

        // $credit_amount = DB::table('general_ledgers')
        //     ->where('ledgerCode', $cashcode->ledgerCode)
        //     ->whereDate('transactionDate', '<', $start_date)
        //     ->where('transactionType', 'Cr')
        //     ->where('is_delete', 'No')
        //     ->sum('transactionAmount');

        // $opening_balance = $previous_amount + $debit_amount - $credit_amount;



        // $serial_numbers = DB::table('general_ledgers')
        //     ->where('ledgerCode', $cashcode->ledgerCode)
        //     ->where('is_delete', 'No')
        //     ->pluck('serialNo');


        // if ($serial_numbers->isNotEmpty()) {

        //     $debit_entries = DB::table('general_ledgers')
        //         ->leftJoin('ledger_masters', 'general_ledgers.ledgerCode', '=', 'ledger_masters.ledgerCode')
        //         ->leftJoin('member_accounts', function ($join) {
        //             $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
        //                 ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
        //         })
        //         ->whereIn('general_ledgers.serialNo', $serial_numbers)
        //         ->where('general_ledgers.transactionType', 'Dr')
        //         ->where('general_ledgers.ledgerCode', '!=', $cashcode->ledgerCode)
        //         ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
        //         ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
        //         ->where('general_ledgers.is_delete', 'No')
        //         ->select(
        //             'general_ledgers.*',
        //             'ledger_masters.name as lname',
        //             'member_accounts.accountNo as memno',
        //             'member_accounts.name',
        //             'ledger_masters.ledgerCode as lg'
        //         )
        //         ->get();



        //     $credit_entries = DB::table('general_ledgers')
        //         ->leftJoin('ledger_masters', 'general_ledgers.ledgerCode', '=', 'ledger_masters.ledgerCode')
        //         ->leftJoin('member_accounts', function ($join) {
        //             $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
        //                 ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
        //         })
        //         ->whereIn('general_ledgers.serialNo', $serial_numbers)
        //         ->where('general_ledgers.transactionType', 'Cr')
        //         ->where('general_ledgers.ledgerCode', '!=', $cashcode->ledgerCode)
        //         ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
        //         ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
        //         ->where('general_ledgers.is_delete', 'No')
        //         ->select(
        //             'general_ledgers.*',
        //             'ledger_masters.name as lname',
        //             'member_accounts.accountNo as memno',
        //             'member_accounts.name',
        //             'ledger_masters.ledgerCode as lg'
        //         )
        //         ->get();

        //     $closing_cash = $opening_balance + $debit_entries->sum('transactionAmount') + $credit_entries->sum('transactionAmount');

        // }

        // dd($closing_cash);
        // $data['opening_balance'] = $opening_balance;
        // $data['closing_cash'] = $closing_cash;




        return view('dashboard')->with($data);
    }
}
