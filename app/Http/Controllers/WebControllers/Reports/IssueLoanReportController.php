<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BranchMaster;
use App\Models\MemberLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class IssueLoanReportController extends Controller
{
    public function index() {
         $branch = BranchMaster::first();
        return view('report.issueLoanReport',compact('branch'));
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $memberType = $request->input('memberType');
        $loanType = $request->input('loanType');

        $data = MemberLoan::where('is_delete','No')->with(['memberAccount', 'memberAccountGuranter1', 'memberAccountGuranter2'])
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('loanDate', [$startDate, $endDate]);
                }
            })
            ->when($memberType && $memberType != 'All', function ($query) use ($memberType) {
                $query->where('memberType', $memberType);
            })
            ->when($loanType && $loanType != 'All', function ($query) use ($loanType) {
                $query->where('loanType', $loanType);
            })
            ->get();
            
         
            $grandTotal = 0;
            $formattedData = $data->map(function ($item) use (&$grandTotal) {
                $loanAmount = $item->loanAmount;
                $grandTotal += $loanAmount;
                return [
                    'accountNo' => $item->accountNo,
                    'name' => $item->memberAccount->name,
                    'loanDate' => $item->loanDate,
                    'pernote' => $item->pernote,
                    'loanAmount' => $loanAmount,
                    'purpose' => $item->purpose,
                    'guranter1' => $item->memberAccountGuranter1->name ?? null,
                    'guranter1AccountNo' => $item->memberAccountGuranter1->accountNo ?? null,
                    'guranter2' => $item->memberAccountGuranter2->name ?? null,
                    'guranter2AccountNo' => $item->memberAccountGuranter2->accountNo ?? null,
                ];
            });

            session(['formattedData' => $formattedData]);
            session(['grandTotal' => $grandTotal]);

            return response()->json($formattedData);
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