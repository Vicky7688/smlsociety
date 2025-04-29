<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Models\LedgerMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMaster;

class CashBookController extends Controller
{
    public function cashbookindex()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.cashbook',$data);
    }


    public function getcashdata(Request $request)
    {
        $start_date = date('Y-m-d', strtotime($request->startdate));
        $end_date = date('Y-m-d', strtotime($request->enddate));
        $cashcode = LedgerMaster::where(['ledgerCode' => 'C002'])->first();
        $previous_amount = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');


        // Calculate debit and credit amounts for opening balance
        $debit_amount = DB::table('general_ledgers')
            ->where('ledgerCode', $cashcode->ledgerCode)
            ->whereDate('transactionDate', '<', $start_date)
            ->where('transactionType', 'Dr')
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $credit_amount = DB::table('general_ledgers')
            ->where('ledgerCode', $cashcode->ledgerCode)
            ->whereDate('transactionDate', '<', $start_date)
            ->where('transactionType', 'Cr')
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $opening_balance = $previous_amount + $debit_amount - $credit_amount;


        $serial_numbers = DB::table('general_ledgers')
            ->where('ledgerCode', $cashcode->ledgerCode)
            ->where('is_delete', 'No')
            ->pluck('serialNo');


        if ($serial_numbers->isNotEmpty()) {

            $debit_entries = DB::table('general_ledgers')
                ->leftJoin('ledger_masters', 'general_ledgers.ledgerCode', '=', 'ledger_masters.ledgerCode')
                ->leftJoin('member_accounts', function($join) {
                    $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
                        ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
                })
                ->whereIn('general_ledgers.serialNo', $serial_numbers)
                ->where('general_ledgers.transactionType', 'Dr')
                ->where('general_ledgers.ledgerCode', '!=', $cashcode->ledgerCode)
                ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
                ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
                ->where('general_ledgers.is_delete', 'No')
                ->select(
                    'general_ledgers.*',
                    'ledger_masters.name as lname',
                    'member_accounts.accountNo as memno',
                    'member_accounts.name',
                    'ledger_masters.ledgerCode as lg'
                )
                ->get();



            $credit_entries = DB::table('general_ledgers')
                ->leftJoin('ledger_masters', 'general_ledgers.ledgerCode', '=', 'ledger_masters.ledgerCode')
                ->leftJoin('member_accounts', function($join) {
                    $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
                        ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
                })
                ->whereIn('general_ledgers.serialNo', $serial_numbers)
                ->where('general_ledgers.transactionType', 'Cr')
                ->where('general_ledgers.ledgerCode', '!=', $cashcode->ledgerCode)
                ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
                ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
                ->where('general_ledgers.is_delete', 'No')
                ->select(
                    'general_ledgers.*',
                    'ledger_masters.name as lname',
                    'member_accounts.accountNo as memno',
                    'member_accounts.name',
                    'ledger_masters.ledgerCode as lg'
                )
                ->get();





            // $credit_entries = DB::table('general_ledgers')
            //     ->leftJoin('ledger_masters', 'general_ledgers.ledgerCode', '=', 'ledger_masters.ledgerCode')
            //     // ->leftJoin('group_masters', 'ledger_masters.groupCode', '=', 'group_masters.groupCode')
            //     ->whereIn('general_ledgers.serialNo', $serial_numbers)
            //     ->where('general_ledgers.ledgerCode', '!=',$cashcode->ledgerCode)
            //     ->where('general_ledgers.transactionType', 'Cr')
            //     ->whereDate('general_ledgers.transactionDate', '>=', $start_date)
            //     ->whereDate('general_ledgers.transactionDate', '<=', $end_date)
            //     ->where('general_ledgers.is_delete', 'No')
            //     ->select(
            //         'general_ledgers.*',
            //         'ledger_masters.name as lname',
            //         // 'group_masters.name as gname'
            //     )
            //     ->get();


            $closing_cash = $opening_balance + $debit_entries->sum('transactionAmount') + $credit_entries->sum('transactionAmount');


            return response()->json([
                'status' => 'success',
                'opening_balance' => $opening_balance ?? 0,
                'debit_entries' => $debit_entries,
                'credit_entries' => $credit_entries,
                'closing_cash' => $closing_cash
            ]);
        } else {


            return response()->json([
                'status' => 'Fail',
                'opening_balance' => $opening_balance,
                'debit_entries' => [],
                'credit_entries' => [],
            ]);
        }
    }




    public function print(Request $request)
    {
        if ($request->session()->has('dayBookData')) {
            $dayBookData = $request->session()->get('dayBookData');

            $openingAmount = $dayBookData['openingAmount'];
            $closingAmount = $dayBookData['closingAmount'];
            $debitEntries = $dayBookData['debitEntries'];
            $creditEntries = $dayBookData['creditEntries'];
            $closingCash = $dayBookData['closingCash'];

            // Fetch receipt entries
            $receiptEntries = GeneralLedger::where('transactionType', 'Cr')
                ->with('group')
                ->with('ledger')
                ->with('account')
                ->where('ledgerCode', '!=', 'C002')
                ->whereBetween('transactionDate', [$request->startdate, $request->enddate])
                ->where('is_delete', 'No')
                ->get();

            // Fetch payment entries
            $paymentEntries = GeneralLedger::where('transactionType', 'Dr')
                ->with('group')
                ->with('ledger')
                ->with('account')
                ->where('ledgerCode', '!=', 'C002')
                ->whereBetween('transactionDate', [$request->startdate, $request->enddate])
                ->where('is_delete', 'No')
                ->get();

            // Fetch opening cash separately
            $openingCash = $dayBookData['openingCash'];

            return view('report.dayBookPrint', compact('openingAmount', 'closingAmount', 'debitEntries', 'creditEntries', 'openingCash', 'closingCash', 'receiptEntries', 'paymentEntries'));
        } else {
            return view('report.emptyPrintView');
        }
    }
}
