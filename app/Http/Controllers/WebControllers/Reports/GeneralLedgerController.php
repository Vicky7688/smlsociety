<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\MemberAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BranchMaster;

class GeneralLedgerController extends Controller
{
    // public function index(){
    //     $branch = BranchMaster::first();
    //     $groups = GroupMaster::orderBy('name', 'ASC')->get();
    //     return view('report.generalLedger', compact('groups','branch'));
    // }
    public function generalLegderIndex(Request $post){
        $branch = BranchMaster::first();
        $groups = GroupMaster::orderBy('name', 'ASC')->get();
        $data['branch'] = $branch;
        $data['groups'] = $groups;
        return view('report.generalLedger',$data);
    }

    public function getledgercodesss(Request $post){
        $groupCode = $post->groupCode;
        if(!empty($groupCode)){
            $ledgers = LedgerMaster::where('groupCode',$groupCode)->orderBy('name','ASC')->get();
            if(!empty($ledgers)){
                return response()->json(['status' => 'success','ledgers' => $ledgers]);
            }else{
                return response()->json(['status' => 'Fail','messages' => 'Ledger Not Found']);
            }
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Group Not Found']);
        }
    }

    public function getgerenalLedgerdata(Request $post){
        $rules = [
            "startDate" => "required",
            "endDate" => "required",
            "groupCode" => "required",
            "ledgerCode" => "required",
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => $validator->errors()]);
        }

        $startDate = date('Y-m-d',strtotime($post->startDate));
        $endDate = date('Y-m-d',strtotime($post->endDate));
        $groupCode = $post->groupCode;
        $ledgerCode = $post->ledgerCode;


        $opening_amount = 0;
        $groupType = DB::table('group_masters')->where('groupCode',$groupCode)->first();
        $ledgerType = DB::table('ledger_masters')->where('groupCode',$groupCode)->where('ledgerCode', $ledgerCode)->first();

        $previous_amount = LedgerMaster::where('groupCode',$groupCode)->where('ledgerCode', $ledgerCode)->value('openingAmount');



        $preyearDebit = DB::table('general_ledgers')
            ->whereDate('transactionDate','<',$startDate)
            ->where('transactionType','=','Dr')
            ->where('general_ledgers.is_delete', 'No')
            ->where('groupCode',$groupCode)
            ->where('ledgerCode',$ledgerCode)
            ->sum('transactionAmount');


        $preyearCredit = DB::table('general_ledgers')
            ->whereDate('transactionDate','<',$startDate)
            ->where('transactionType','=','Cr')
            ->where('general_ledgers.is_delete', 'No')
            ->where('groupCode',$groupCode)
            ->where('ledgerCode',$ledgerCode)
            ->sum('transactionAmount');



        if($groupType->type === 'Asset' || $groupType->type === 'Expenditure'){
            $opening_amount = $previous_amount + $preyearDebit - $preyearCredit;
        }else{
            $opening_amount = $previous_amount + $preyearCredit - $preyearDebit;
        }


        $currententries =  DB::table('general_ledgers')
            ->whereDate('transactionDate','>=',$startDate)
            ->whereDate('transactionDate','<=',$endDate)
            ->where('general_ledgers.is_delete', 'No')
            ->where('groupCode',$groupCode)
            ->where('ledgerCode',$ledgerCode)
            ->orderBy('transactionDate','ASC')
            ->get();


        $post->session()->put('generalLedgerData', [
            'ledgerCode' => $ledgerCode,
            'groupCode' => $groupCode,
            'groupType' => $groupType,
            'grouphead' => $groupType->headName,
            'openingAmount' => $opening_amount,
            // 'closingAmount' => $closingAmount,
            'generalLedger' => $currententries
        ]);



        if(!empty($currententries) || !empty($opening_amount) || !empty($groupType)){
            return response()->json([
                'status' => 'success',
                'groupType' => $groupType,
                'ledgerType' => $ledgerType,
                'opening_amount' => $opening_amount,
                'currententries' => $currententries,
            ]);


        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Notr Found']);
        }
    }









    // public function getData(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'startDate' => 'required',
    //         'endDate' => 'required',
    //         'groupCode' => 'required',
    //         'ledgerCode' => 'required'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something Went Wrong. Please check all inputs'
    //         ]);
    //     }
    //     // $startDate = $request->startDate;
    //     // $endDate = $request->endDate;
    //     $groupCode = $request->groupCode;
    //     $ledgerCode = $request->ledgerCode;


    //     $startDate = date('Y-m-d', strtotime($request->startDate));
    //     $endDate = date('Y-m-d', strtotime($request->endDate));
    //     $openingShareBal = 0 ;
    //     $groups = GroupMaster::where('groupCode',  $request->groupCode)->first();

    //     if($groupCode == "SHAM001"){
    //             $openingShareBal = DB::table('member_opening_balance')->where('accType','Share')->sum('opening_amount');
    //     }else if($groupCode == "CDSM001"){
    //          $openingShareBal = DB::table('member_opening_balance')->sum('opening_amount');
    //     }else if($groupCode == "SAVM001"){
    //          $openingShareBal = DB::table('member_opening_balance')->where('accType','Share')->sum('opening_amount');
    //     }

    //     $groupType = $groups->type;
    //     // Code To Get Opening Cash
    //     $entryAmount = LedgerMaster::where('ledgerCode', $ledgerCode)->value('openingAmount');

    //     $drOpening = GeneralLedger::where('transactionType', 'Dr')
    //         ->where('groupCode', $groupCode)
    //         ->where('ledgerCode', $ledgerCode)
    //         ->where('transactionDate', '<', $startDate)
    //         ->sum('transactionAmount');

    //     $crOpening = GeneralLedger::where('transactionType', 'Cr')
    //         ->where('groupCode', $groupCode)
    //         ->where('ledgerCode', $ledgerCode)
    //         ->where('transactionDate', '<', $startDate)
    //         ->sum('transactionAmount');





    //     if ($groupType == 'Asset' ||  $groupType == "Expenditure") {
    //         $openingAmount = $entryAmount + $drOpening - $crOpening;
    //     } else {
    //            //dd($entryAmount, $drOpening, $crOpening, $ledgerCode);
    //             $openingAmount = $entryAmount + $openingShareBal + $crOpening - $drOpening ;

    //     }

    //     // Code To Get Closing Cash
    //     $drClosing = GeneralLedger::where('transactionType', 'Dr')
    //         ->where('groupCode', $groupCode)
    //         ->where('ledgerCode', $ledgerCode)
    //         ->whereDate('transactionDate','>=', $startDate)
    //         ->whereDate('transactionDate','<=', $endDate)
    //         ->sum('transactionAmount');

    //     $crClosing = GeneralLedger::where('transactionType', 'Cr')
    //         ->where('groupCode', $groupCode)
    //         ->where('ledgerCode', $ledgerCode)
    //         ->whereDate('transactionDate','>=', $startDate)
    //         ->whereDate('transactionDate','<=', $endDate)
    //         ->sum('transactionAmount');


    //     if ($groupType == 'Asset' ||  $groupType == "Expenditure") {
    //         $closingAmount = $openingAmount +  $drClosing - $crClosing ;

    //         // dd($closingAmount);
    //         // if ($groups->headName == "Loan") {
    //         //     $closingAmount = $openingAmount +  $drClosing - $crClosing ;

    //         // } else {
    //         //     $closingAmount =  $drClosing - $crClosing + $openingAmount;
    //         // }
    //     } else {
    //           $closingAmount = $openingAmount + $crClosing - $drClosing;
    //     }

    //     $generalLedger = GeneralLedger::whereBetween('transactionDate', [$startDate, $endDate])
    //          ->where('transactionAmount','>', 0)
    //          ->where('groupCode', $groupCode)
    //          ->where('ledgerCode', $ledgerCode)
    //          ->orderByRaw("convert(`accountNo`, decimal) ASC")->get();
    //         // ->orderBy("accountNo")
    //         // ->with('ledger', 'account') ->orderByRaw("CAST(transactionDate AS UNSIGNED) ASC")

    //      $request->session()->put('generalLedgerData', [
    //        'ledgerCode' => $ledgerCode,
    //         'groupCode' => $groupCode,
    //         'groupType' => $groupType,
    //         'grouphead' => $groups->headName,
    //         'openingAmount' => $openingAmount,
    //         'closingAmount' => $closingAmount,
    //         'generalLedger' => $generalLedger
    //     ]);
    // //    print_r($generalLedger);
    //     return response()->json([
    //         'status' => true,
    //         'ledgerCode' => $ledgerCode,
    //         'groupCode' => $groupCode,
    //         'groupType' => $groupType,
    //         'grouphead' => $groups->headName,
    //         'openingAmount' => $openingAmount,
    //         'closingAmount' => $closingAmount,
    //         'generalLedger' => $generalLedger
    //     ]);




    //   }

    public function print(Request $request)
    {
        $branch = BranchMaster::first();
        if (session()->has('generalLedgerData')) {
            $generalLedgerData = session('generalLedgerData');

            $openingAmount = $generalLedgerData['openingAmount'];
            $closingAmount = $generalLedgerData['closingAmount'];
            $generalLedgerEntries = $generalLedgerData['generalLedger'];

            $accountIds = $generalLedgerEntries->pluck('accountId')->unique();
            $accountNames = MemberAccount::whereIn('id', $accountIds)->pluck('name', 'id');

            $drEntries = $generalLedgerEntries->where('transactionType', 'Dr');
            $crEntries = $generalLedgerEntries->where('transactionType', 'Cr');

            $drTotal = $drEntries->sum('transactionAmount');
            $crTotal = $crEntries->sum('transactionAmount');

            $balance = $openingAmount;
            foreach ($generalLedgerEntries as $entry) {
                $balance += $entry->transactionType === 'Dr' ? $entry->transactionAmount : -$entry->transactionAmount;
                $entry->balance = $balance;
            }

            $balanceTotal = $balance;

            return view('report.generalPrint', compact('openingAmount', 'closingAmount', 'generalLedgerEntries', 'drTotal', 'crTotal', 'balanceTotal','branch'));
        } else {
            return view('report.emptyPrintView');
        }
    }
}
