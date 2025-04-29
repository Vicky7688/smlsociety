<?php
namespace App\Http\Controllers\WebControllers;
use App\Http\Controllers\Controller;
use App\Models\MemberSaving;
use App\Models\GeneralLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use Session;

class CalculationsController extends Controller
{
    public function index()
    {
        return view('report.calculation');
    }
    public function getdata(Request $request)
    {




        $accountNo = $request->accountNo;
        $paidDate = $request->paidDate;
        $startdate = date('Y-m-d', strtotime($request->startDate));
        $enddate = date('Y-m-d', strtotime($request->endDate));
        $membertype = $request->membertype;
        // Fetch all accounts for the given member type
        if($accountNo){
            $exist=GeneralLedger::where('transactionDate', '>=', $enddate)->where('accountNo', $accountNo)->where('sessionId','=',Session::get('sessionId'))->first();
            if($exist){
                $approved="yes";
                $action="Approved";
            }else{
                $approved="no";
                $action="Wating for conformation";
            }
            $accounts = DB::table('member_accounts')->where('accountNo', $accountNo)->where('memberType', $membertype)->where('is_delete','No')->get();

        }else{
    $exist=GeneralLedger::where('transactionDate', '>=', $enddate)->where('sessionId','=',Session::get('sessionId'))->first();
    if($exist){
        $approved="yes";
        $action="Approved";
    }else{
        $approved="no";
        $action="Wating for conformation";
    }
    $memberids = DB::table('member_savings')->where('memberType', $membertype)
        ->where('transactionDate', '>=', $enddate)
        ->orderBy('transactionDate')
        ->pluck('accountNo');

    $accounts = DB::table('member_accounts')->whereNotIn('accountNo', $memberids)->where('memberType', $membertype)->where('is_delete','No')->get();
        }
        $transactions = [];
        $totalInterest = 0;
        // Loop through each account
        foreach ($accounts as $account) {
            $intrest = 0;
            $lasttxnDate = $startdate ;
            $savingTxns = DB::table('member_savings')->where('memberType', $membertype)
                ->where('accountNo', $account->accountNo)
                ->where('transactionDate', '>=', $startdate)
                ->where('transactionDate', '<=', $enddate)
                ->orderBy('transactionDate')
                ->get();
            $amount = $this->getAmount($membertype,$account->accountNo,$lasttxnDate);
            $openingAmount = $amount;
            if(count($savingTxns) > 0)
            {
                foreach($savingTxns as $key=> $savingTxn){

                    $earlier = new DateTime($lasttxnDate);
                    $later = new DateTime($savingTxn->transactionDate);
                    $LastTxnday = $later->diff($earlier)->format("%a");
                    $rate = $request->interest/100 ;
                    $intrest = $intrest + $amount * $rate * ($LastTxnday/365) ;
                    $amount = $this->getAmountt($membertype,$account->accountNo,$startdate,$enddate);
                    $lasttxnDate = $savingTxn->transactionDate ;
                }
                    $earlier = new DateTime($lasttxnDate);
                    $later = new DateTime($enddate);
                    $LastTxnday = $later->diff($earlier)->format("%a");
                    $amount = $this->getAmountt($membertype,$account->accountNo,$startdate,$enddate);
                    $rate =   $request->interest / 100 ;
                    $intrest = $intrest + $amount * $rate * ($LastTxnday/365) ;
                    $closingBalance = round($amount+$intrest);

            }else{
                   $amount = $this->getAmountt($membertype,$account->accountNo,$startdate,$enddate);
                   $rate =  $request->interest / 100 ;
                   $intrest = $amount * $rate ;
                   $closingBalance = $amount;
            }

            $transactions[] = [
                'name' => $account->name,
                'accountNo' => $account->accountNo,
                'transactionDate' => date('d-m-Y', strtotime($enddate)),
                'openingAmount' => $openingAmount,
                'closingBalance' => $closingBalance,
                'interest' => round($intrest,0),
                'action' => $action,
            ];
        }


        // Return the transaction data
        return response()->json([
            'status' => 'success',
            'transactions' =>  $transactions,
            'approved' =>  $approved,
        ]);
     }

    public function deleteentry(Request $request)
    {
        $accountNo = $request->accountNo;
        $paidDate = date('Y-m-d', strtotime($request->paidDate));
        $startdate = date('Y-m-d', strtotime($request->startDate));
        $enddate = date('Y-m-d', strtotime($request->endDate));

        if($accountNo){

        GeneralLedger::where('sessionId','=',Session::get('sessionId'))->where('accountNo','=',$accountNo)->where('transactionDate','=',$paidDate)->forceDelete();
        MemberSaving::where('sessionId','=',Session::get('sessionId'))->where('accountNo','=',$accountNo)->where('transactionDate','=',$paidDate)->forceDelete();
        }else{

            GeneralLedger::where('sessionId','=',Session::get('sessionId'))->where('transactionDate','=',$paidDate)->forceDelete();
            MemberSaving::where('sessionId','=',Session::get('sessionId'))->where('transactionDate','=',$paidDate)->forceDelete();
        }



        return response()->json([
            'status' => 'success'
        ]);
    }



    public function approve(Request $request)
    {


        $paidDate = $request->paidDate;
        $accountNo = $request->accountNo;
        $startdate = date('Y-m-d', strtotime($request->startDate));
        $enddate = date('Y-m-d', strtotime($request->endDate));
        $membertype = $request->membertype;
        // Fetch all accounts for the given member type

        if($accountNo){
            $exist=GeneralLedger::where('transactionDate', '>=', $enddate)->where('accountNo', $accountNo)->where('sessionId','=',Session::get('sessionId'))->first();
            if($exist){
                $approved="yes";
                $action="Approved";
            }else{
                $approved="no";
                $action="Wating for conformation";
            }
            $accounts = DB::table('member_accounts')->where('accountNo', $accountNo)->where('memberType', $membertype)->where('is_delete','No')->get();

        }else{
            $exist=GeneralLedger::where('transactionDate', '>=', $enddate)->where('sessionId','=',Session::get('sessionId'))->first();
            if($exist){
                $approved="yes";
                $action="Approved";
            }else{
                $approved="no";
                $action="Wating for conformation";
            }
            $memberids = DB::table('member_savings')->where('memberType', $membertype)
                ->where('transactionDate', '>=', $enddate)
                ->orderBy('transactionDate')
                ->pluck('accountNo');

            $accounts = DB::table('member_accounts')->whereNotIn('accountNo', $memberids)->where('memberType', $membertype)->where('is_delete','No')->get();
         }
        $transactions = [];
        $totalInterest = 0;
        // Loop through each account
        foreach ($accounts as $account) {
            $intrest = 0;
            $lasttxnDate = $startdate ;
            $savingTxns = DB::table('member_savings')->where('memberType', $membertype)
                ->where('accountNo', $account->accountNo)
                ->where('transactionDate', '>=', $startdate)
                ->where('transactionDate', '<=', $enddate)
                ->orderBy('transactionDate')
                ->get();
            $amount = $this->getAmount($membertype,$account->accountNo,$lasttxnDate);
            $openingAmount = $amount;
            if(count($savingTxns) > 0)
            {
                foreach($savingTxns as $key=> $savingTxn){

                    $earlier = new DateTime($lasttxnDate);
                    $later = new DateTime($savingTxn->transactionDate);
                    $LastTxnday = $later->diff($earlier)->format("%a");
                    $rate = $request->interest/100 ;
                    $intrest = $intrest + $amount * $rate * ($LastTxnday/365) ;
                    $amount = $this->getAmountt($membertype,$savingTxn->accountNo,$startdate,$enddate);
                    $lasttxnDate = $savingTxn->transactionDate ;
                }
                    $earlier = new DateTime($lasttxnDate);
                    $later = new DateTime($enddate);
                    $LastTxnday = $later->diff($earlier)->format("%a");
                    $amount = $this->getAmountt($membertype,$account->accountNo,$startdate,$enddate);
                    $rate =   $request->interest / 100 ;
                    $intrest = $intrest + $amount * $rate * ($LastTxnday/365) ;
                    $closingBalance = round($amount+$intrest);

            }else{
                   $amount = $this->getAmountt($membertype,$account->accountNo,$startdate,$enddate);
                   $rate =  $request->interest / 100 ;
                   $intrest = $amount * $rate ;
                   $closingBalance = $amount;
            }




            $accountNo=$account->accountNo;
            $interest=round($intrest,0);
            $group=DB::table('group_masters')->where('name','=','Expenses')->first();
            $member=DB::table('member_accounts')->where('accountNo','=',$accountNo)->first();
            // $ledger=DB::table('ledger_masters')->where('groupCode','=',$group->groupCode)->first();
            $ledgercodex=DB::table('ledger_masters')->where('groupCode','=',$group->groupCode)->where('name','=','Interest Paid On Saving')->first();



            $serialno='saving'.rand(111111,999999);
           $insert= new MemberSaving();
           $insert->serialNo=$serialno;
           $insert->accountId=$member->id;
           $insert->accountNo=$accountNo;
           $insert->memberType=$request->membertype;
           $insert->groupCode=$ledgercodex->groupCode;
           $insert->ledgerCode=$ledgercodex->ledgerCode;
           $insert->transactionDate=date('Y-m-d', strtotime($paidDate));
           $insert->transactionType='Deposit';
           $insert->depositAmount=$interest;
           $insert->paymentType='Transfer';
           $insert->branchId=1;
           $insert->narration='Intrest Paid upto '.date('d-m-Y', strtotime($enddate));
           $insert->sessionId=Session::get('sessionId');
           $insert->updatedBy=Session::get('sessionId');
           $insert->is_delete='No';
           $insert->save();



           $insertgenrel=new GeneralLedger();
           $insertgenrel->serialNo=$serialno;
           $insertgenrel->accountId=$member->id;
           $insertgenrel->accountNo=$accountNo;
           $insertgenrel->memberType=$request->membertype;
           $insertgenrel->groupCode='SAVM001';
           $insertgenrel->ledgerCode='SAVM001';
           $insertgenrel->formName='Saving';
           $insertgenrel->referenceNo=$insert->id;
           $insertgenrel->transactionDate=date('Y-m-d', strtotime($paidDate));
           $insertgenrel->transactionType='Cr';
           $insertgenrel->transactionAmount=$interest;
           $insertgenrel->entryMode='manual';
           $insertgenrel->branchId=1;
           $insertgenrel->narration='Intrest Paid upto '.date('d-m-Y', strtotime($enddate));
           $insertgenrel->agentId=1;
           $insertgenrel->sessionId=Session::get('sessionId');
           $insertgenrel->updatedBy=Session::get('sessionId');
           $insertgenrel->is_delete='No';
           $insertgenrel->save();


           $insertgenrel=new GeneralLedger();
           $insertgenrel->serialNo=$serialno;
           $insertgenrel->accountId=$member->id;
           $insertgenrel->accountNo=$accountNo;
           $insertgenrel->memberType=$request->membertype;
           $insertgenrel->groupCode=$ledgercodex->groupCode;
           $insertgenrel->ledgerCode=$ledgercodex->ledgerCode;
           $insertgenrel->formName='Saving';
           $insertgenrel->referenceNo=$insert->id;
           $insertgenrel->transactionDate=date('Y-m-d', strtotime($paidDate));
           $insertgenrel->transactionType='Dr';
           $insertgenrel->transactionAmount=$interest;
           $insertgenrel->entryMode='manual';
           $insertgenrel->branchId=1;
           $insertgenrel->narration='Intrest Paid upto '.date('d-m-Y', strtotime($enddate));
           $insertgenrel->agentId=1;
           $insertgenrel->sessionId=Session::get('sessionId');
           $insertgenrel->updatedBy=Session::get('sessionId');
           $insertgenrel->is_delete='No';
           $insertgenrel->save();





           $transactions[] = [
            'name' => $account->name,
            'accountNo' => $account->accountNo,
            'transactionDate' => date('d-m-Y', strtotime($enddate)),

            'openingAmount' => $openingAmount,
            'closingBalance' => $closingBalance,
            'interest' => round($intrest,0),
            'action' => 'Approved',
        ];


        }
        // Return the transaction data
         return response()->json([
            'status' => 'success',
            'transactions' =>  $transactions

        ]);
    }



    public function getAmount($type,$ac,$date){
        $openSaving = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
        $openingBal =  $openSaving->Saving ?? 0 ;
        $savingAmt = 0 ;
        $results = DB::table('member_savings')
            ->select([
                DB::raw('SUM(depositAmount) AS credit'),
                DB::raw('SUM(withdrawAmount) AS debit')
            ])
            ->where('accountNo', $ac)
            ->where('is_delete', 'No')
            ->where('transactionDate', '>=', $date)
            ->first();
        if($results){
            $savingAmt = $results->credit - $results->debit;
        }
        return $openingBal + $savingAmt ;
    }
    public function getAmountt($type,$ac,$startdate,$date){
        $openSaving = DB::table('opening_account_details')->where('AccountNumber',$ac)->where('TransferReason',"!=",'Deleted')->first();
        $openingBal =  $openSaving->Saving ?? 0 ;
        $savingAmt = 0 ;
        $results = DB::table('member_savings')
            ->select([
                DB::raw('SUM(depositAmount) AS credit'),
                DB::raw('SUM(withdrawAmount) AS debit')
            ])
            ->where('accountNo', $ac)
            ->where('is_delete', 'No')
            ->where('transactionDate', '>=', $startdate)
            ->where('transactionDate', '<=', $date)
            ->first();
        if($results){
            $savingAmt = $results->credit - $results->debit;
        }
        return $openingBal + $savingAmt ;
    }
}
