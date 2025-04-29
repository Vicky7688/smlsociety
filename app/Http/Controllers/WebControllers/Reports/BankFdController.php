<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BankFdController extends Controller
{
    public function bankfdindex(){
        $data['groups'] = DB::table('group_masters')->whereIn('groupCode',['C002','BANK001'])->get();
        $data['bankfds'] = DB::table('bank_fd_masters')->orderBy('id','DESC')->get();
        $data['bank_fd_deposit'] = DB::table('bank_fd_deposit')->orderBy('id','DESC')->get();
        return view('transaction.fd.bankfd',$data);
    }

    public function getbankfdledgeres(Request $post){
        $groups_code = $post->groups_code;
        $bankfdId = $post->bankaccountfd;

        $bankfds = DB::table('bank_fd_masters')->pluck('ledgerCode')->toArray();

        if ($groups_code) {
            $ledgers = LedgerMaster::where('groupCode', $groups_code)
                ->whereNotIn('ledgerCode', $bankfds)
                ->orderBy('name', 'ASC')
                ->get();

            if ($ledgers->isNotEmpty()) {
                return response()->json(['status' => 'success', 'ledgers' => $ledgers]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Ledger Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Group Not Found']);
        }

    }
    public function bankfdinsert(Request $post){
        // dd($post->all());
        // $rules = [
        //     "txdate" => "21-03-2025"
        //   "fd_number" => "1233333"
        //   "fd_accountno" => "232323"
        //   "bankaccountfd" => "1"
        //   "fd_amount" => "1600000"
        //   "intresttype" => "QuarterlyCompounded"
        //   "intrestfrom" => "21-03-2025"
        //   "intrestrate" => "5.25"
        //   "year" => "1"
        //   "month" => null
        //   "days" => null
        //   "interestamount" => "85668"
        //   "maturityamount" => "1685668"
        //   "maturitydate" => "21-03-2026"
        //   "groupType" => "BANK001"
        //   "ledgerType" => "HPSCBANK"
        // ];

        $txnDate = date('Y-m-d',strtotime($post->txdate));
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($txnDate)));
        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        //_________Get Bank Fd Details
        $bankDetails = DB::table('bank_fd_masters')->where('id',$post->bankaccountfd)->first();

        $fdGroupCode = '';
        $fdLedgerCode = '';

        if(!empty($bankDetails)){
            $fdGroupCode = $bankDetails->groupCode;
            $fdLedgerCode = $bankDetails->ledgerCode;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Fd Group/ Ledger Code Not Found']);
        }

        $cashBankGroupCode = '';
        $cashBankLedgerCode = '';


        if($post->groupType && $post->ledgerType){
            $cashBankGroupCode = $post->groupType;
            $cashBankLedgerCode = $post->ledgerType;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Cash/Bank Group/Ledger Not Found']);
        }

        $serialNo = 'BankFD'.time();

        DB::beginTransaction();
        try{
           //___________Entries In Bank FD Table
           $fdId =  DB::table('bank_fd_deposit')->insertGetId([
                'serialNo' => $serialNo,
                'fd_date' => $txnDate,
                'fd_no' => $post->fd_number,
                'fd_account' => $post->fd_accountno,
                'fd_renew_id' => '',
                'bank_fd_type' => $post->bankaccountfd,
                'principal_amount' => $post->fd_amount,
                'int_start_from' => date('Y-m-d',strtotime($post->intrestfrom)),
                'interest_rate' => $post->intrestrate,
                'year' => $post->year,
                'month' => $post->month,
                'days' => $post->days,
                'interest_type' => $post->intresttype,
                'interest_amount' => $post->interestamount,
                'maturity_amount' => $post->maturityamount,
                'maturity_date' => date('Y-m-d',strtotime($post->maturitydate)),
                'mature_serialNo' => '',
                'status' => 'Active',
                'payment_group' => $cashBankGroupCode,
                'payment_ledger' => $cashBankLedgerCode,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            //___________Entries Gerenal Ledger Table
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->fd_number,
                'accountNo' => $post->fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $fdGroupCode,
                'ledgerCode' => $fdLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $post->fd_amount,
                'narration' => 'Bank FD A/c- '.$post->fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->fd_number,
                'accountNo' => $post->fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $cashBankGroupCode,
                'ledgerCode' => $cashBankLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $post->fd_amount,
                'narration' => 'Bank FD A/c- '.$post->fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['status' => 'success','messages' => 'FD Entries Created Successfully']);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'line' => $e->getLine()]);
        }
    }


    public function bankfdedit(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'FD Id is Empty']);
        }

        $id = $post->id;
        $existsId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();
        if(is_null($existsId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            if($existsId->status === 'Active'){
                return response()->json(['status' => 'success','existsId' => $existsId]);
            }else{
                return response()->json(['status' => 'Fail','messages' => 'FD Has Mature You Cant Edit']);
            }
        }
    }

    public function bankferupdate(Request $post){
        $txnDate = date('Y-m-d',strtotime($post->txdate));
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($txnDate)));
        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        //_________Get Bank Fd Details
        $bankDetails = DB::table('bank_fd_masters')->where('id',$post->bankaccountfd)->first();
        $fdGroupCode = '';
        $fdLedgerCode = '';

        if(!empty($bankDetails)){
            $fdGroupCode = $bankDetails->groupCode;
            $fdLedgerCode = $bankDetails->ledgerCode;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Fd Group/ Ledger Code Not Found']);
        }

        $cashBankGroupCode = '';
        $cashBankLedgerCode = '';


        if($post->groupType && $post->ledgerType){
            $cashBankGroupCode = $post->groupType;
            $cashBankLedgerCode = $post->ledgerType;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Cash/Bank Group/Ledger Not Found']);
        }

        $id = $post->bankfd_id;
        $existId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();



        $serialNo = 'BankFD'.time();

        DB::beginTransaction();

        try{

            //_________Delete Previous Entries Gerenal Ledger
            DB::table('general_ledgers')->where('serialNo',$existId->serialNo)->where('referenceNo',$existId->id)->delete();

            //_________Delete Previous Entries Bank Fd Table
            DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->delete();



            //___________Entries In Bank FD Table
           $fdId =  DB::table('bank_fd_deposit')->insertGetId([
                'serialNo' => $serialNo,
                'fd_date' => $txnDate,
                'fd_no' => $post->fd_number,
                'fd_account' => $post->fd_accountno,
                'fd_renew_id' => '',
                'bank_fd_type' => $post->bankaccountfd,
                'principal_amount' => $post->fd_amount,
                'int_start_from' => date('Y-m-d',strtotime($post->intrestfrom)),
                'interest_rate' => $post->intrestrate,
                'year' => $post->year,
                'month' => $post->month,
                'days' => $post->days,
                'interest_type' => $post->intresttype,
                'interest_amount' => $post->interestamount,
                'maturity_amount' => $post->maturityamount,
                'maturity_date' => date('Y-m-d',strtotime($post->maturitydate)),
                'mature_serialNo' => '',
                'status' => 'Active',
                'payment_group' => $cashBankGroupCode,
                'payment_ledger' => $cashBankLedgerCode,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            //___________Entries Gerenal Ledger Table
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->fd_number,
                'accountNo' => $post->fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $fdGroupCode,
                'ledgerCode' => $fdLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $post->fd_amount,
                'narration' => 'Bank FD A/c- '.$post->fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->fd_number,
                'accountNo' => $post->fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $cashBankGroupCode,
                'ledgerCode' => $cashBankLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $post->fd_amount,
                'narration' => 'Bank FD A/c- '.$post->fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['status' => 'success','messages' => 'FD Entries Created Successfully']);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'line' => $e->getLine()]);
        }
    }


    public function deletebankfds(Request $post){

        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'FD Id is Empty']);
        }

        $id = $post->id;
        $existId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();

        $txnDate = $existId->fd_date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween($txnDate);
        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        if(is_null($existId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            DB::beginTransaction();
            try{
                DB::table('general_ledgers')->where('serialNo',$existId->serialNo)->where('referenceNo',$existId->id)->delete();
                DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->delete();
                DB::commit();
                return response()->json(['status' => 'success', 'messages' => 'Recordx Deleted Successfully']);


            }catch(\Exception $e){
                DB::rollBack();
                return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'line' => $e->getLine()]);
            }
        }
    }


    public function bankfdmature(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'FD Id is Empty']);
        }

        $id = $post->id;
        $existId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();

        $txnDate = $existId->fd_date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween($txnDate);
        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }




    }


    public function getdatabankfdrenew(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'FD Id is Empty']);
        }

        $id = $post->id;
        $existsId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();

        // $txnDate = $existsId->fd_date;

        // $session_master = SessionMaster::find(Session::get('sessionId'));

        // if ($session_master->auditPerformed === 'Yes') {
        //     return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        // }

        // $result = $this->isDateBetween($txnDate);
        // if (! $result) {
        //     return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        // }

        if(is_null($existsId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            if($existsId->status === 'Active'){
                return response()->json(['status' => 'success','existsId' => $existsId]);
            }else{
                return response()->json(['status' => 'Fail','messages' => 'FD Has Mature You Cant Edit']);
            }
        }
    }

    public function bankfdrenew(Request $post){
        // dd($post->all());
        // $rules = [
        //     "renew_bankfd_id" => "6"
        //     "renew_txdate" => "22-06-2023"
        //     "renew_number" => "1233333"
        //     "renew_fd_accountno" => "232323"
        //     "bankaccountfd" => "3"
        //     "renew_fd_amount" => "1685668"
        //     "tds_amount" => "8714"
        //     "renew_fd_account" => "1676954"
        //     "renew_intresttype" => "QuarterlyCompounded"
        //     "renew_intrestfrom" => "22-06-2023"
        //     "renew_intrestrate" => "7"
        //     "renew_year" => null
        //     "renew_month" => "23"
        //     "renew_days" => null
        //     "renew_interestamount" => "238683"
        //     "renew_maturityamount" => "1915637"
        //     "renew_maturitydate" => "22-05-2025"
        //     "old_interest_amount" => "85668"
        //     "renew_groupType" => "BANK001"
        //     "renew_ledgerType" => "HPSCBANK"
        // ];

        $id = $post->renew_bankfd_id;
        $existsId = DB::table('bank_fd_deposit')->where('id',$id)->where('status','Active')->first();
        $txnDate = date('Y-m-d',strtotime($post->renew_txdate));
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($txnDate)));
        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        //_________Get Bank Fd Details
        $bankDetails = DB::table('bank_fd_masters')->where('id',$post->bankaccountfd)->first();



        //_______________FD Group Code && Ledger Code
        $fdGroupCode = '';
        $fdLedgerCode = '';

        if(!empty($bankDetails)){
            $fdGroupCode = $bankDetails->groupCode;
            $fdLedgerCode = $bankDetails->ledgerCode;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Fd Group/ Ledger Code Not Found']);
        }




        //_______________Cash/Bank Group Code && Ledger Code
        $cashBankGroupCode = '';
        $cashBankLedgerCode = '';

        if($post->renew_groupType && $post->renew_ledgerType){
            $cashBankGroupCode = $post->renew_groupType;
            $cashBankLedgerCode = $post->renew_ledgerType;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Cash/Bank Group/Ledger Not Found']);
        }



        //_______________Interest Received Group Code && Ledger Code
        $receivedInterestGroup = '';
        $receivedLedgerCode = '';

        if($bankDetails->ledgerCode && $bankDetails->interest_ledger){
            $receivedInterestGroup = $bankDetails->ledgerCode;
            $receivedLedgerCode = $bankDetails->interest_ledger;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Received Interest Group/Ledger Not Found']);
        }


        //__________Get Tds Group/Ledger Code
        $paidtdsGroup = '';
        $paidtdsLedger = '';

        $tdsGroupLedgers = DB::table('ledger_masters')->where('ledgerCode',$bankDetails->tds_ledger)->first();
        if($tdsGroupLedgers->groupCode && $tdsGroupLedgers->ledgerCode){
            $paidtdsGroup = $tdsGroupLedgers->groupCode;
            $paidtdsLedger = $tdsGroupLedgers->ledgerCode;
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Received Interest Group/Ledger Not Found']);
        }

        $serialNo = 'BankFD'.time();


        $received_intt = $post->received_intt ? $post->received_intt : 0;
        $paid_tds_amount = $post->tds_amount ? $post->tds_amount : 0;
        $renew_amount = $post->renew_amount ? $post->renew_amount : 0;


        DB::beginTransaction();
        try{

            //___________________________________________Mature Entries____________________________________________


            DB::table('bank_fd_deposit')->where('id',$id)->update([
                'mature_serialNo' => $serialNo,
                'fd_renew_id' => $existsId->id,
                'status' => 'Mature',
            ]);

            //__________Mature FD Amount In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->renew_number,
                'accountNo' => $post->renew_fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $fdGroupCode,
                'ledgerCode' => $fdLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $existsId->id,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $existsId->principal_amount,
                'narration' => 'Bank FD A/c- '.$post->renew_fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //__________Interest Received Amount In Gerenal Ledger
            if($received_intt > 0){
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $post->renew_number,
                    'accountNo' => $post->renew_fd_accountno,
                    'memberType' => null,
                    'vouchar_no' => '',
                    'groupCode' => 'INCM001',
                    'ledgerCode' => $receivedLedgerCode,
                    'formName' => 'Bank FD '.$bankDetails->bank_name,
                    'referenceNo' => $existsId->id,
                    'entryMode' => 'manual',
                    'transactionDate' => $txnDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $received_intt,
                    'narration' => 'Intt. Received Bank FD A/c- '.$post->renew_fd_accountno,
                    'branchId' => session('branchId') ?: 1,
                    'sessionId' => session('sessionId') ?: 1,
                    'updatedBy' => $post->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            //______________Paid TDS Entries In Gerenal Ledger
            if($paid_tds_amount > 0){
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $post->renew_number,
                    'accountNo' => $post->renew_fd_accountno,
                    'memberType' => null,
                    'vouchar_no' => '',
                    'groupCode' => $paidtdsGroup,
                    'ledgerCode' => $paidtdsLedger,
                    'formName' => 'Bank FD '.$bankDetails->bank_name,
                    'referenceNo' => $existsId->id,
                    'entryMode' => 'manual',
                    'transactionDate' => $txnDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $paid_tds_amount,
                    'narration' => 'Bank FD A/c- '.$post->renew_fd_accountno,
                    'branchId' => session('branchId') ?: 1,
                    'sessionId' => session('sessionId') ?: 1,
                    'updatedBy' => $post->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            //______________Bank FD Mature Entries In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->renew_number,
                'accountNo' => $post->renew_fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $cashBankGroupCode,
                'ledgerCode' => $cashBankLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' =>  $existsId->id,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $renew_amount,
                'narration' => 'Bank FD A/c- '.$post->renew_fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);





            //___________________________________________New FD Entries____________________________________________

            //___________Entries In Bank FD Table
            $fdId = DB::table('bank_fd_deposit')->insertGetId([
                'serialNo' => $serialNo,
                'fd_date' => $txnDate,
                'fd_no' => $post->renew_number,
                'fd_account' => $post->renew_fd_accountno,
                'fd_renew_id' => '',
                'bank_fd_type' => $post->bankaccountfd,
                'principal_amount' => $renew_amount,
                'int_start_from' => date('Y-m-d',strtotime($post->renew_intrestfrom)),
                'interest_rate' => $post->renew_intrestrate,
                'year' => $post->renew_year,
                'month' => $post->renew_month,
                'days' => $post->renew_days,
                'interest_type' => $post->renew_intresttype,
                'interest_amount' => $post->renew_interestamount,
                'maturity_amount' => $post->renew_maturityamount,
                'maturity_date' => date('Y-m-d',strtotime($post->renew_maturitydate)),
                'mature_serialNo' => '',
                'status' => 'Active',
                'payment_group' => $cashBankGroupCode,
                'payment_ledger' => $cashBankLedgerCode,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //___________Entries Gerenal Ledger Table
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->renew_number,
                'accountNo' => $post->renew_fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $fdGroupCode,
                'ledgerCode' => $fdLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $renew_amount,
                'narration' => 'Renew Bank FD A/c- '.$post->renew_fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $post->renew_number,
                'accountNo' => $post->renew_fd_accountno,
                'memberType' => null,
                'vouchar_no' => '',
                'groupCode' => $cashBankGroupCode,
                'ledgerCode' => $cashBankLedgerCode,
                'formName' => 'Bank FD '.$bankDetails->bank_name,
                'referenceNo' => $fdId,
                'entryMode' => 'manual',
                'transactionDate' => $txnDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $renew_amount,
                'narration' => 'Renew Bank FD A/c- '.$post->renew_fd_accountno,
                'branchId' => session('branchId') ?: 1,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['status' => 'success','messages' => 'FD Entries Renew
            Successfully']);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'line' => $e->getLine()]);
        }
    }

    // public function getdatabankfdunmature(Request $post){
    //     $rules = [
    //         'id' => 'required'
    //     ];

    //     $validator = Validator::make($post->all(),$rules);

    //     if($validator->fails()){
    //         return response()->json(['status' => 'Fail','messages' => 'FD Id is Empty']);
    //     }

    //     $id = $post->id;
    //     $unmature = DB::table('bank_fd_deposit')->where('id',$id)->first();
    //     $next = DB::table('bank_fd_deposit')->where('serialNo',$unmature->mature_serialNo)->first();
    //     dd($unmature,$next);

    //     if(is_null($unmature)){
    //         return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
    //     }else{
    //         $gerenalLedger = DB::table('general_ledgers')->where('serialNo',$unmature->serialNo)->get();
    //         dd($gerenalLedger);

    //     }


    // }


}
