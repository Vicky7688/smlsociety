<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BranchMaster;

class BankFdReportController extends Controller
{
    public function bankfdreportindex(){
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        $data['banksMaster'] = DB::table('bank_fd_masters')->orderBy('bank_name','ASC')->get();
        return view('report.bankfdreport',$data);
    }

    public function getbankfdsreportdetails(Request $post)
    {
        $rules = [
            "endDate" => "required",
            "bankType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Please Select End Date and Bank Type']);
        }

        $endDate = date('Y-m-d', strtotime($post->endDate));
        $bankType = $post->bankType;

        $query = DB::table('bank_fd_deposit')
            ->select('bank_fd_deposit.*', 'bank_fd_masters.id as bankId', 'bank_fd_masters.bank_name', 'bank_fd_masters.ledgerCode')
            ->leftJoin('bank_fd_masters', 'bank_fd_masters.id', '=', 'bank_fd_deposit.bank_fd_type')
            ->whereDate('bank_fd_deposit.fd_date', '<=', $endDate)
            ->where('bank_fd_deposit.status', 'Active');

        if ($bankType !== 'All') {
            if (is_array($bankType)) {
                $query->whereIn('bank_fd_masters.ledgerCode', $bankType);
            } else {
                $query->where('bank_fd_masters.ledgerCode', $bankType);
            }
        }

        $results = $query->get();

        if (!empty($results)) {
            return response()->json(['status' => 'success', 'bankDetails' => $results]);
        }else{
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

}
