<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BranchMaster;
use App\Models\MemberLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class IssueLoanReportController extends Controller
{
    public function index()
    {
        $data['branch'] = BranchMaster::first();
        $data['loan_type'] = DB::table('loan_masters')->where('status', 'Active')->get();
        return view('report.issueLoanReport',$data);
    }

    public function getData(Request $request)
    {
        $startDate = date('Y-m-d', strtotime($request->input('startDate')));
        $endDate =  date('Y-m-d', strtotime($request->input('endDate')));
        $memberType = $request->input('memberType');
        $loanType = $request->input('loanType');
        // dd($startDate, $endDate, $memberType, $loanType);
        // $data = MemberLoan::where('is_delete', 'No')->with(['memberAccount', 'memberAccountGuranter1', 'memberAccountGuranter2'])
        //     ->where(function ($query) use ($startDate, $endDate) {
        //         if ($startDate && $endDate) {
        //             $query->whereBetween('loanDate', [$startDate, $endDate]);
        //         }
        //     })
        //     ->when($memberType && $memberType != 'All', function ($query) use ($memberType) {
        //         $query->where('memberType', $memberType);
        //     })
        //     ->when($loanType && $loanType != 'All', function ($query) use ($loanType) {
        //         $query->where('loanType', $loanType);
        //     })
        //     ->get();
        if($loanType === 'All'){
            $loan = DB::table('member_loans')
            ->select('member_loans.*','member_accounts.name as member_name')
            ->leftJoin('member_accounts', 'member_accounts.id', '=', 'member_loans.accountId')
            ->whereBetween('member_loans.loanDate', [$startDate, $endDate])
            ->where('member_loans.memberType', $memberType)
            ->get();
        }else{
            $loan = DB::table('member_loans')
            ->select('member_loans.*','member_accounts.name as member_name')
            ->leftJoin('member_accounts', 'member_accounts.id', '=', 'member_loans.accountId')
            ->whereBetween('member_loans.loanDate', [$startDate, $endDate])
            ->where('member_loans.memberType', $memberType)
            ->where('loanType', $loanType)
            ->get();
        }

        // dd($data);

        // $grandTotal = 0;
        // $formattedData = $data->map(function ($item) use (&$grandTotal) {
        //     $loanAmount = $item->loanAmount;
        //     $grandTotal += $loanAmount;
        //     return [
        //         'accountNo' => $item->accountNo,
        //         'name' => $item->memberAccount->name,
        //         'loanDate' => $item->loanDate,
        //         'pernote' => $item->pernote,
        //         'loanAmount' => $loanAmount,
        //         'purpose' => $item->purpose,
        //         'guranter1' => $item->memberAccountGuranter1->name ?? null,
        //         'guranter1AccountNo' => $item->memberAccountGuranter1->accountNo ?? null,
        //         'guranter2' => $item->memberAccountGuranter2->name ?? null,
        //         'guranter2AccountNo' => $item->memberAccountGuranter2->accountNo ?? null,
        //     ];
        // });

        // session(['formattedData' => $formattedData]);
        // session(['grandTotal' => $grandTotal]);

        // return response()->json($formattedData);
        return response()->json($loan);
    }

    public function print()
    {
        if (session()->has('formattedData') && !empty(session('formattedData'))) {
            $formattedData = session('formattedData');
            $grandTotal = session('grandTotal');

            return view('report.issueLoanPrint', compact('formattedData', 'grandTotal'));
        } else {
            return view('report.emptyPrintView');
        }
    }
}
