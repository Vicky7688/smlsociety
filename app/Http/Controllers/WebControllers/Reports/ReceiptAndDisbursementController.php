<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\BranchMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class ReceiptAndDisbursementController extends Controller
{
    public function receiptanddisbursementIndex()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.receiptAndDisbursement',$data);
    }

    public function getdatareceiptanddisbursement(Request $post)
    {
        $rules = [
            "transactionDate" => "required",
            "endDate" => "required",
            "reportType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Check All Inputs']);
        }

        $startDate = date('Y-m-d', strtotime($post->transactionDate));
        $endDate = date('Y-m-d', strtotime($post->endDate));
        $reportType = $post->reportType;
        $openingCash = 0;
        $closingCash = 0;

        //___________Get Cash Details Opening and Closing
        $previous_amount = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');
        $preyearDebit = GeneralLedger::where('transactionType', '=', 'Dr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete', 'No')
            ->whereDate('transactionDate', '<', $startDate)
            ->sum('transactionAmount');

        $preyearCredit = GeneralLedger::where('transactionType', '=', 'Cr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete', 'No')
            ->whereDate('transactionDate', '<', $startDate)
            ->sum('transactionAmount');

        $currentyearDebit = GeneralLedger::where('transactionType', '=', 'Dr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete','=','No')
            ->whereDate('transactionDate', '>=', $startDate)
            ->whereDate('transactionDate', '<=', $endDate)
            ->sum('transactionAmount');

        $currentyearCredit = GeneralLedger::where('transactionType', '=', 'Cr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete','=','No')
            ->whereDate('transactionDate', '>=', $startDate)
            ->whereDate('transactionDate', '<=', $endDate)
            ->sum('transactionAmount');

        $openingCash = $previous_amount + $preyearDebit - $preyearCredit;
        $closingCash = $openingCash + $currentyearDebit - $currentyearCredit;


        //________Get all Ledgers Details Without Cash
        if ($reportType == "group") {

            $type = DB::table('general_ledgers')
                ->select(
                    'group_masters.groupCode','group_masters.headName','group_masters.id as gid',
                    DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" THEN general_ledgers.transactionAmount ELSE 0 END) AS total_credit'),
                    DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" THEN general_ledgers.transactionAmount ELSE 0 END) AS total_debit')
                    )
                ->leftJoin('group_masters','group_masters.groupCode','=','general_ledgers.groupCode')
                ->where('general_ledgers.groupCode', "!=", "C002")
                ->where('general_ledgers.is_delete','=','No')
                ->whereDate('general_ledgers.transactionDate', '>=', $startDate)
                ->whereDate('general_ledgers.transactionDate', '<=', $endDate)
                ->groupBy('group_masters.groupCode','group_masters.headName','group_masters.id')
                ->orderBy('group_masters.id','ASC')
                ->get();


                // dd($type);
        } else {

            $type = DB::table('general_ledgers')
                ->select(
                    'ledger_masters.ledgerCode','ledger_masters.name','ledger_masters.id as lds',
                    DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Cr" THEN general_ledgers.transactionAmount ELSE 0 END) AS total_credit'),
                    DB::raw('SUM(CASE WHEN general_ledgers.transactionType = "Dr" THEN general_ledgers.transactionAmount ELSE 0 END) AS total_debit')
                    )
                ->leftJoin('ledger_masters','ledger_masters.ledgerCode','=','general_ledgers.ledgerCode')
                ->where('general_ledgers.ledgerCode', "!=", "C002")
                ->where('general_ledgers.is_delete','=','No')
                ->whereDate('general_ledgers.transactionDate', '>=', $startDate)
                ->whereDate('general_ledgers.transactionDate', '<=', $endDate)
                ->groupBy('ledger_masters.ledgerCode','ledger_masters.name','ledger_masters.id')
                ->orderBy('ledger_masters.id','ASC')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'messages' => 'Data retrieved successfully',
            'openingCash' => $openingCash,
            'closingCash' => $closingCash,
            'type' => $type,
        ]);
    }



































    // public function getData(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'reportType' => 'required',
    //         'startDate' => 'required',
    //         'endDate' => 'required'
    //     ]);

    //     // if ($validator->fails()) {
    //     //     return response()->json([
    //     //         'status' => false,
    //     //         'message' => 'Something Went Wrong. Please check all inputs'
    //     //     ]);
    //     // }

    //     $start_date = date('Y-m-d',strtotime($request->transactionDate));
    //     $end_date = date('Y-m-d',strtotime($request->endDate));

    //     $entryCash = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');
    //     $drOpening = GeneralLedger::where('transactionType', 'Dr')
    //         ->where('ledgerCode', 'C002')
    //         ->where('groupCode', 'C002')
    //         ->where('transactionDate', '<', $start_date)
    //         ->sum('transactionAmount');



    //     $crOpening = GeneralLedger::where('transactionType', 'Cr')
    //         ->where('ledgerCode', 'C002')
    //         ->where('groupCode', 'C002')
    //         ->where('transactionDate', '<', $start_date)
    //         ->sum('transactionAmount');
    //     $openingCash = $entryCash + $drOpening - $crOpening;



    //     // Code To Get Closing Cash
    //     $drClosing = GeneralLedger::where('transactionType', 'Dr')
    //         ->where('ledgerCode', 'C002')
    //         ->where('groupCode', 'C002')
    //         ->where('transactionDate', '>=', $start_date)
    //         ->where('transactionDate', '<=', $end_date)
    //         ->sum('transactionAmount');


    //     $crClosing = GeneralLedger::where('transactionType', 'Cr')
    //         ->where('ledgerCode', 'C002')
    //         ->where('groupCode', 'C002')
    //         ->where('transactionDate', '>=', $start_date)
    //         ->where('transactionDate', '<=', $end_date)
    //         ->sum('transactionAmount');
    //     $closingCash = $openingCash + $drClosing - $crClosing;


    //     if ($request->reportType == "group") {
    //         $group = GeneralLedger::select('groupCode')
    //             ->selectRaw('SUM(CASE WHEN transactionType = "Cr" THEN transactionAmount ELSE 0 END) AS total_credit')
    //             ->selectRaw('SUM(CASE WHEN transactionType = "Dr" THEN transactionAmount ELSE 0 END) AS total_debit')
    //             ->where('groupCode', "!=", "C002")
    //             ->where('is_delete', 'No')
    //             ->whereBetween('transactionDate', [date('Y-m-d',strtotime($request->transactionDate)), date('Y-m-d',strtotime($request->endDate))])
    //             ->groupBy('groupCode')->with('group')
    //             ->get();
    //     } else {


    //         $group = GeneralLedger::select('ledgerCode')
    //             ->selectRaw('SUM(CASE WHEN transactionType = "Cr" THEN transactionAmount ELSE 0 END) AS total_credit')
    //             ->selectRaw('SUM(CASE WHEN transactionType = "Dr" THEN transactionAmount ELSE 0 END) AS total_debit')
    //             ->where('ledgerCode', "!=", "C002")
    //             ->where('is_delete', 'No')
    //             ->whereBetween('transactionDate', [date('Y-m-d',strtotime($request->transactionDate)), date('Y-m-d',strtotime($request->endDate))])
    //             ->groupBy('ledgerCode')->with('ledger')
    //             ->get();
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data retrieved successfully',
    //         'openingCash' => $openingCash,
    //         'closingCash' => $closingCash,
    //         'groups' => $group,
    //     ]);
    // }
    public function print(Request $request)
    {
        return view('report.receiptPrint');
    }


    public function printRecept(Request $request)
    {



        $entryCash = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');
        $drOpening = GeneralLedger::where('transactionType', 'Dr')->where('is_delete', 'No')->where('ledgerCode', 'C002')
            ->where('transactionDate', '<', $request->startDate)->sum('transactionAmount');
        $crOpening = GeneralLedger::where('transactionType', 'Cr')->where('is_delete', 'No')->where('ledgerCode', 'C002')
            ->where('transactionDate', '<', $request->startDate)->sum('transactionAmount');
        $openingCash = $entryCash + $drOpening - $crOpening;

        // Code To Get Closing Cash
        $drClosing = GeneralLedger::where('transactionType', 'Dr')->where('is_delete', 'No')->where('ledgerCode', 'C002')
            ->where('transactionDate', '<=', $request->endDate)->sum('transactionAmount');
        $crClosing = GeneralLedger::where('transactionType', 'Cr')->where('is_delete', 'No')->where('ledgerCode', 'C002')
            ->where('transactionDate', '<=', $request->endDate)->sum('transactionAmount');
        $closingCash = $entryCash + $drClosing - $crClosing;

        if ($request->reportType == "group") {
            $group = GeneralLedger::select('groupCode')
                ->selectRaw('SUM(CASE WHEN transactionType = "Cr" THEN transactionAmount ELSE 0 END) AS total_credit')
                ->selectRaw('SUM(CASE WHEN transactionType = "Dr" THEN transactionAmount ELSE 0 END) AS total_debit')
                ->where('groupCode', "!=", "C002")
                ->where('is_delete', 'No')
                ->whereBetween('transactionDate', [$request->startDate, $request->endDate])
                ->groupBy('groupCode')->with('group')
                ->get();
        } else {


            $group = GeneralLedger::select('ledgerCode')
                ->selectRaw('SUM(CASE WHEN transactionType = "Cr" THEN transactionAmount ELSE 0 END) AS total_credit')
                ->selectRaw('SUM(CASE WHEN transactionType = "Dr" THEN transactionAmount ELSE 0 END) AS total_debit')
                //->where('ledgerCode', "!=", "C002")
                ->where('is_delete', 'No')
                ->whereBetween('transactionDate', [$request->startDate, $request->endDate])
                ->groupBy('ledgerCode')->with('ledger')
                ->get();
        }

        $data = [
            'branch' =>  BranchMaster::first(),
            'endDate' => date('d-m-Y', strtotime($request->endDate)),
            'startDate' => date('d-m-Y', strtotime($request->startDate)),
            'openingCash' => $openingCash,
            'closingCash' => $closingCash,
            'groups' => $group,
        ];
        return view('report.printRd')->with($data);
    }
}
