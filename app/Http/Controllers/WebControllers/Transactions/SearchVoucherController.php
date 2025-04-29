<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\GroupMaster;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;

use App\Models\LedgerMaster;
use Illuminate\Http\Request;

class SearchVoucherController extends Controller
{
    public function index()
{
    // Fetch data from both tables using a JOIN query
    $journalEntries = JournalVoucher::join('journal_voucher_details', 'journal_vouchers.id', '=', 'journal_voucher_details.voucherId')
        ->select('journal_vouchers.voucherDate', 'journal_vouchers.id as voucherNo', 'journal_voucher_details.groupCode', 'journal_voucher_details.ledgerCode', 'journal_voucher_details.transactionType', 'journal_voucher_details.drAmount', 'journal_voucher_details.crAmount', 'journal_vouchers.narration')
        ->get();

    // Fetch group and ledger names based on groupCode and ledgerCode
    $groups = GroupMaster::pluck('name', 'groupCode');
    $ledgers = LedgerMaster::pluck('name', 'ledgerCode');

    // Iterate over the fetched data and display it in the table
    return view('transaction.searchVoucher', compact('journalEntries', 'groups', 'ledgers'));
}
    public function print($voucherNo)
    {       
        $journalEntries = JournalVoucherDetail::where('voucherId', $voucherNo)->get();

        $groups = GroupMaster::pluck('name', 'groupCode');
        $ledgers = LedgerMaster::pluck('name', 'ledgerCode');
        return view('transaction.voucherPrint', ['journalEntries' => $journalEntries], compact('groups', 'ledgers'));
    }
}