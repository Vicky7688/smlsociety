<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\TransferedAccount;
use App\Models\MemberShare;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMaster;
use App\Models\Contribution;

class ContributionListController extends Controller
{
     public function index()
    {
        // dd('okk');
        $branch = BranchMaster::first();
        return view('report.ContributionList', compact('branch'));
    }
    public function getData(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'endDate' => 'required',
            'memberType' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong. Please check all inputs'
            ]);
        }
        // $start_date = date('Y-m-d', strtotime($request->startDate));
        $end_date = date('Y-m-d', strtotime($request->endDate));
        $membertype = $request->memberType;
        $Interest = $request->interest;


        $query = MemberAccount::where(['memberType' => 'Member'])
            ->where('is_delete', 'No')
            ->orderByRaw("CAST(accountNo AS UNSIGNED) ASC")
            ->get(['accountNo', 'fatherName', 'name', 'id', 'status']);
            // dd($query);
        $resultsArray = [];

        foreach ($query as $data) {

            $Balence = $this->getbalance($data->accountNo, $end_date);
            // dd($Balence);

            if ($Balence != 0) {

                if ($data->status == "Transfer") {
                    $memberid = $data->id;
                    $transfer_member = TransferedAccount::where(['accountId' => $memberid])->first();
                    $resultsArray[] = [
                        "Ac_no" => $transfer_member->accountNo,
                        "member_name" => $transfer_member->name,
                        "father_husband" => $transfer_member->fatherName,
                        "memberbalence" => $Balence,
                        "Interest" => $Interest,
                    ];
                } else {
                    $resultsArray[] = [
                        "Ac_no" => $data->accountNo,
                        "member_name" => $data->name,
                        "father_husband" => $data->fatherName,
                        "memberbalence" => $Balence,
                        "Interest" => $Interest,
                    ];
                }
            }
        }
        // dd($resultsArray);

        return response()->json(['status' => true, 'data' => $resultsArray]);
    }
     public function getbalance($ac, $lastDate)
    {
        $openingBal = DB::table('member_accounts')->where('accountNo', $ac)->first();
        $shareBal = $openingBal->opening_amount ?? 0;
        $credit =  Contribution::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Deposit')->whereDate('transactionDate', '<=', $lastDate)->sum("depositAmount");
        $debit =  Contribution::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Withdraw')->whereDate('transactionDate', '<=', $lastDate)->sum("withdrawAmount");
        return $shareBal + $credit - $debit;
    }

}
