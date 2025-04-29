<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MemberAccount;
use App\Models\Mis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MISReportController extends Controller
{
    public function index()
    {
        return view('report.misreport');
    }


    public function getData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $memberType = $request->input('memberType');

        $data = Mis::with('memberAccount')
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->where('member_type', $memberType)
            ->get();
        return response()->json($data);
    }


    public function print()
    {
        $startDate = request()->input('startDate');
        $endDate = request()->input('endDate');
        $memberType = request()->input('memberType');

        $data = Mis::with('memberAccount')
            ->get();

        $formattedData = $data->map(function ($item) {
            $name = $item->memberAccount->name ?? null;
            return [
                'date' => $item->date,
                'member_type' => $item->member_type,
                'name' => $name,
                'account_no' => $item->account_no,
                'mis_ac_no' => $item->mis_ac_no,
                'amount' => $item->amount
            ];
        });

        $grandTotal = $formattedData->sum('amount');

        return view('report.misPrint', compact('formattedData', 'grandTotal'));
    }
    // 

}
