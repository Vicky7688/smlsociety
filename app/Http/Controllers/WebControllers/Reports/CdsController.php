<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\TransferedAccount;
use App\Models\MemberShare;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Validator;
use App\Models\CompulsoryDeposit;
use Illuminate\Support\Facades\DB;


class CdsController extends Controller
{
    public function index() { 
        return view('report.cdsList');
    }

    public function getData(Request $request) {
        
        $validator = Validator::make($request->all(),[
           
            'endDate' => 'required',
            'memberType' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong. Please check all inputs'
            ]);
        }
       
        $end_date = date('Y-m-d', strtotime($request->endDate));
        $membertype = $request->memberType;

        if($membertype == "all"){
            $query = MemberAccount::where('OpeningDate', '<=', $end_date)
             ->where('is_delete',"!=","Yes")
            ->get();
        }else{
            $query = MemberAccount::where('OpeningDate', '<=', $end_date)
            ->where(['memberType' => $membertype])
            ->where('is_delete',"!=","Yes")
            ->get();
        }

        $resultsArray = [];
        
        foreach($query as $data){
            
                $totalDeposit = 0;
                $totalWithdraw = 0;
                $Balence = $this->getbalance($data->accountNo,$end_date);
               
                if($Balence != 0){
                     $resultsArray[] = [
                            "Ac_no"=>$data->accountNo,
                            "member_name"=>$data->name,
                            "father_husband"=>$data->fatherName,
                            "memberbalence"=>$Balence,
                        ];
                }
            }
        
        session(['formattedData' => $resultsArray]);

        return response()->json(['status' => true, 'data' => $resultsArray]);
    }

    public function print(Request $request){
        // Fetch data from session
        if (session()->has('formattedData')) {
            $formattedData = session('formattedData');
    
            // Calculate grand total
            $grandTotal = 0;
            foreach ($formattedData as $row) {
                $grandTotal += $row['memberbalence'];
            }
    
            return view('report.savingPrint', compact('formattedData', 'grandTotal'));        
        } else {
            return view('report.emptyPrintView');
        }
    }

 public function getbalance($ac, $lastDate)
    {
        $openingBal = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
        $shareBal = $openingBal->OpeningCompulsoryDeposit ?? 0 ;
        $credit =  CompulsoryDeposit::where('accno', $ac)->where('is_delete', 'No')->whereDate('date', '<=', $lastDate)->sum("Deposit");
        $debit =  CompulsoryDeposit::where('accno', $ac)->where('is_delete', 'No')->whereDate('date', '<=', $lastDate)->sum("Withdraw");
        return $shareBal + $credit - $debit;
    }    
    
}
