<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebControllers\Transactions\RDController;
use App\Models\AccountDeduction;
use App\Models\LedgerMaster;
use App\Models\LoanMaster;
use App\Models\MemberAccount;
use App\Models\MemberLoan;
use App\Models\MemberSaving;
use Illuminate\Http\Request;
use App\Models\ReCurringRd;
use App\Models\DeductionAc;
use App\Models\GeneralLedger;
use App\Models\CompulsoryDeposit;
use DB;

class AllListController extends Controller
{
    public function index()
    {
          $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();

        return view('report.autodeduction')->with($data);
    }


    public function getData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $output = '';

        if (empty($accountNo)) {
            $output .= '<li class="list-group-item memberlist"></li>';
            return response()->json([
                'status' => true,
                'data' => $output
            ]);
        }
        $data = MemberAccount::where('memberType', $memberType)->where('accountNo', 'LIKE', $accountNo . '%')->limit(10)->get();
        $saving = MemberSaving::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();
        $loan = MemberLoan::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();
        $rd = ReCurringRd::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();


        if (count($data) > 0) {
            $output = '<ul class="list-group membersearch" style="display:block;z-indez:1">';
            foreach ($data as $row) {
                $output .= '<li class="list-group memberlist">' . $row->accountNo . '</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<li class="list-group-item memberlist">No Data Found</li>';
        }
        return response()->json([
            'status' => true,
            'data' => $output,
            'saving' =>$saving,
            'loan' =>$loan,
            'rd' =>$rd
        ]);
    }

    public function fetchData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        if (!empty($accountNo)) {
            $member = MemberAccount::where(['memberType' => $memberType, 'accountNo' => $accountNo])->first();
            $saving = MemberSaving::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();
            $loan = MemberLoan::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();
            $rd = ReCurringRd::where(['memberType' => $memberType, 'accountNo' => $accountNo])->get();
            $allsaved = DeductionAc::where(['account_no'=>$request->accountNo,'member_type'=>$request->memberType])->get() ;
            return response()->json([
                'status' => true,
                'member' => $member,
                'saving' => $saving,
                'loan' => $loan,
                'rd' => $rd,
                'allsaved' => $allsaved
            ]);
        }
    }

    public function storeData(Request $post){
        $allamounts = $post->sAmount ;

        $insert['account_no'] = $post->accountNo;
        $insert['member_type'] = $post->memberType;
        $insert['deduction_date'] = $post->deductionday ;
        $insert['bankcode'] = $post->banktypes;
        $insert['status']  = $post->status;
        foreach($post->saccount as $key=> $account){
                 $insert['type'] = str_replace("`","", $key);
             foreach($account as $key2=>$accountvalue){
                $insert['account'] = $accountvalue;
                $insert["amount"] = $allamounts[$key][$key2] ?? 0;
                DeductionAc::updateOrCreate(['account_no'=>$post->accountNo,'member_type'=>$post->memberType,'account'=>$accountvalue],$insert);
             }

        }
       return response()->json(['status' => 'success'], 200);
    }

   public function autoDeduction(Request $post){

     $autodeductions =  DeductionAc :: where('status',"active")->get();
     foreach($autodeductions as $autodeduction){
        $member = MemberAccount::where(['memberType' => $autodeduction->member_type, 'accountNo' => $autodeduction->account_no])->first();
         $groupCode = 'CDSM002';
         $ledgerCode = 'CDSM002';
         if(date('Y-m')."-".$autodeduction->deduction_date > date('Y-m-d')) {
             continue ;
         }

         $checkprevouse = CompulsoryDeposit :: where(['accno'=>$autodeduction->account_no,"date"=> date('Y-m')."-".$autodeduction->deduction_date,'amount'=>$autodeduction->amount])->count() ;
         if($checkprevouse > 0){
              continue ;
         }

         if($autodeduction->type == "saving"){
           if($member){
              DB::beginTransaction();
            try {

                 do {
                    $serialNo = "cds" . rand(1111111, 9999999);
                } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

                $saving = new CompulsoryDeposit();
                $saving->serialNo = $serialNo;
                $saving->accountId =  $member->id;
                $saving->accno = $autodeduction->account_no;
                $saving->date = date('Y-m')."-".$autodeduction->deduction_date;
                // $saving->transactionType = $request->transactionType;
                $saving->membertype = $autodeduction->member_type;
                $saving->Deposit = $autodeduction->amount;
                $saving->Withdraw = 0;
                $saving->acc = $autodeduction->bankcode;
                $saving->Bank = $autodeduction->bankcode;;
                $saving->type = "1";
                $saving->narrartion = "Complusory deposit";
                $saving->Interest = "0.00" ;
                $saving->admissionfee = "0.00" ;
                $saving->ChqNo = "0" ;
                $saving->bankname = " " ;
                $saving->SessionYear = session('sessionyear') ;
                $saving->Branch = session('Branchname') ;
                $saving->groupCode = $groupCode;
                $saving->ledgerCode = $ledgerCode;
                $saving->logged_branch = session('branchId') ? session('branchId') : 1;
                $saving->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $saving->agent = "1";
                $saving->LoginId = "1";
                $saving->save();

                $savingId = $saving->id;

                $ledger = new GeneralLedger();
                $ledger->serialNo = $serialNo;
                $ledger->accountId =  $member->id;
                $ledger->accountNo = $autodeduction->account_no;
                $ledger->memberType = $autodeduction->member_type;
                $ledger->formName = 'CDS';
                $ledger->referenceNo = $savingId;
                $ledger->entryMode = 'automatic';
                $ledger->transactionDate = date('Y-m')."-".$autodeduction->deduction_date;
                $ledger->transactionType = 'Dr';
                $ledger->transactionAmount = $autodeduction->amount;
                $ledger->narration = "Complusory deposit";
                $ledger->groupCode =  $groupCode;
                $ledger->ledgerCode = $ledgerCode;
                $ledger->branchId = session('branchId') ? session('branchId') : 1;
                $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $ledger->agentId = 1;
                $ledger->updatedBy = "1";
                $ledger->save();



                $ledger = new GeneralLedger();
                $ledger->serialNo = $serialNo;
                $ledger->accountId =  $member->id;
                $ledger->accountNo = $autodeduction->account_no;
                $ledger->memberType = $autodeduction->member_type;
                $ledger->formName = 'CDS';
                $ledger->referenceNo = $savingId;
                $ledger->entryMode = 'Manual';
                $ledger->transactionDate = date('Y-m')."-".$autodeduction->deduction_date;;
                $ledger->transactionType = 'Cr';
                $ledger->transactionAmount = $autodeduction->amount;
                $ledger->narration = "Complusory deposit";
                $ledger->groupCode = "BANK001";
                $ledger->ledgerCode = $autodeduction->bankcode;
                $ledger->branchId = session('branchId') ? session('branchId') : 1;
                $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $ledger->agentId = 1;
                $ledger->updatedBy = "1";
                $ledger->save();

                DB::commit();
            } catch (\Exception $e) {
                    DB::rollback();
                    dd($e) ;
                }
            }
         }
     }

     dd($autodeduction) ;
   }

}
