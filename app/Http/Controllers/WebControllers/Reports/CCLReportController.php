<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BranchMaster;

class CCLReportController extends Controller
{
    public function ccllistIndex()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.cclreport',$data);
    }

    public function getdataccllist(Request $post){
        $rules = [
            "endDate" => "required",
            "memberType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Check All Fields']);
        }

        $dates = date('Y-m-d', strtotime($post->endDate));
        $memberType = $post->memberType;

        $queries = DB::table('member_ccl')
            ->select(
                'member_ccl.id','member_ccl.ccl_Date',
                DB::raw('SUM(ccl_payments.transfer_amount) as transfer_amount'),
                DB::raw('SUM(ccl_payments.recovey_amount) as recovey_amount'),
                DB::raw('SUM(ccl_payments.interest_amount) as interest_amount'),
                DB::raw('MAX(ccl_payments.ccl_Id) as ccl_Id'),
                DB::raw('MAX(ccl_payments.transcationDate) as transcationDate'),
                DB::raw('DATEDIFF("' . $dates . '", MAX(ccl_payments.transcationDate)) as day_difference'),
                'member_accounts.name as mname','member_accounts.accountNo as membershipno','member_ccl.interest','member_ccl.month',
                'member_ccl.days','member_ccl.year','member_accounts.memberType as mtype','member_ccl.cclNo',
            )
            ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_ccl.membership')
            ->where('ccl_payments.transcationDate', '<=', $dates);


        if ($memberType != 'All') {
            $queries->whereIn('member_accounts.memberType', (array)$memberType);
        }

        $queries = $queries->groupBy(
            'member_accounts.name','member_accounts.accountNo','member_ccl.interest','member_ccl.month','member_ccl.days',
            'member_ccl.year','member_accounts.memberType','member_ccl.id','member_ccl.ccl_Date','member_ccl.cclNo',
        )
        ->get();

        if(!empty($queries)){
            return response()->json(['status' => 'success','allDatas' => $queries]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }
}
