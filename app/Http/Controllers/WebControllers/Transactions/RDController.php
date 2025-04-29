<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgentMaster;
use App\Models\MemberAccount;
use App\Models\ReCurringRd;
use App\Models\RdInstallment;
use App\Models\GeneralLedger;
use App\Models\TransferedAccount;
use App\Models\LedgerMaster;
use App\Models\MemberSaving;
use App\Models\RdReceiptdetails;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\SessionMaster;
use App\Models\GroupMaster;
use App\Models\opening_accounts;

class RDController extends Controller
{
    public function index(){
        $groups = GroupMaster::whereIn('groupCode',['C002','BANK001'])->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $data['groups'] = $groups;
        $data['agents'] = $agents;
        return view('transaction.rd_recurring', $data);
    }

    //__________Get Ledger's Behalf Of Groups
    public function Getcashbankledgers(Request $post){
        $groups_code = $post->groups_code;
        if($groups_code){
            $ledgers = LedgerMaster::where('groupCode', $groups_code)->where('ledgerCode','!=', 'BANKFD01')->where('status','Active')->orderBy('name','ASC')->get();


            if(!empty($ledgers)){
                return response()->json([
                    'status' => 'success',
                    'ledgers' => $ledgers
                ]);
            }else{
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Ledger Not Found'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Group Not Found'
            ]);
        }
    }

    public function GetRDAccountList(Request $post){
        $account_no = $post->account_no;
        $memberType = $post->memberType;
        if(!empty($account_no)){
            $account_nos = DB::table('opening_accounts')
                ->where('accountNo','LIKE',$account_no.'%')
                ->where('membertype','=',$memberType)
                ->where('accountname','=','RD')
                ->get();

            if($account_nos){
                return response()->json([
                    'status' => 'success',
                    'accounts' => $account_nos
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Account Number Not Exits'
            ]);
        }
    }

    //_________Get Customer Details
    public function GetRDDetails(Request $post){
        //_________Checked Account in Opening Account Table
        $account_no = $post->selectdId;
        $member_type = $post->member_type;

        $opening_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'member_accounts.accountNo as membership',
                'member_accounts.name as customer_name',
                'scheme_masters.id as schid',
                'scheme_masters.months as schmonth',
                'member_accounts.memberType as type'
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.accountNo', $account_no)
            ->where('opening_accounts.membertype','=',$member_type)
            ->where('member_accounts.memberType','=',$member_type)
            ->where('opening_accounts.accountname','=','RD')
            ->first();



        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if(!empty($session_master)){

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('rd_receiptdetails')
                ->where('rc_account_no',$opening_account->accountNo)
                ->where('memberType', '=', $opening_account->membertype)
                ->whereDate('payment_date','<',$session_master->startDate)
                ->get();


            //_________Get Current Year Entries
            $rd_accounts = DB::table('re_curring_rds')
                ->select('re_curring_rds.*','agent_masters.id as agentid','agent_masters.name as agentname')
                ->leftJoin('agent_masters','agent_masters.id','re_curring_rds.agentid')
                ->where('re_curring_rds.memberType',$opening_account->membertype)
                ->where('re_curring_rds.rd_account_no',$opening_account->accountNo)
                ->where('re_curring_rds.accountNo',$opening_account->membershipno)
                ->orderBy('date','ASC')
                ->first();

            $deposit_amount = '';
            if(is_null($rd_accounts)){
                $deposit_amount = '';
            }else{
                $deposit_amount = DB::table('rd_receiptdetails')
                    ->select(
                        DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                        DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                    )
                    ->where('rd_account_no',$rd_accounts->accountId)
                    ->where('memberType',$rd_accounts->memberType)
                    ->where('rc_account_no',$rd_accounts->id)
                    ->get();

            }

            if($previous_balance || $rd_accounts || !is_null($opening_account) || !is_null($deposit_amount)){
                return response()->json([
                    'status' => 'success',
                    'previous_balance' => $previous_balance,
                    'rd_accounts' => $rd_accounts,
                    'opening_account' => $opening_account,
                    'deposit_amount' => $deposit_amount
                ]);
            }else{
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Record Not Found'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Check Your Session'
            ]);
        }
    }

    public function RdInsert(Request $post){

        $rules = array(
            'rd_opening_date' => 'required',
            'member_type' => 'required',
            'rd_account_no' => 'required|numeric',
            'rd_account_amount' => 'required|numeric',
            'rd_account_interest' => 'required|numeric',
            'rd_account_month' => 'required|numeric',
            'rd_account_maturity_date' => 'required',
            'membership_no' => 'required|numeric'
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }


        $openingdatee = date('Y-m-d', strtotime($post->rd_opening_date));
        $maturity_date = date('Y-m-d', strtotime($post->rd_account_maturity_date));
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->rd_opening_date))) ;

        if (!$result) {
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Please Check your session"]);
        }


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }



        $opening_accounts = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*','schmeaster.id as sch_id','schmeaster.scheme_code',
                'ledger_masters.reference_id','ledger_masters.ledgerCode','ledger_masters.groupCode',
                'refSchemeMaster.scheme_code as ref_scheme_code','refSchemeMaster.months'
            )
            ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
            ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
            ->where('opening_accounts.accountNo', $post->rd_account_no)
            ->where('accountname','RD')
            ->where('opening_accounts.status','Active')
            ->first();
        // dd($opening_accounts);




        if($opening_accounts->transactionDate > $openingdatee){
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Check Account Open Date"],400);
        }


        if($opening_accounts->groupCode && $opening_accounts->ledgerCode){
            $scheme_group_code = $opening_accounts->groupCode;
            $scheme_ledger_code = $opening_accounts->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Group Code And Ledger Code Not Found"],400);
        }


        $rand = "RD" . time();
        $preAccount = DB::table('re_curring_rds')
            ->where('rd_account_no',$post->rd_account_no)
            ->where('memberType',$post->member_type)
            ->first();

        if($preAccount){
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {

            DB::beginTransaction();
            try{
                $rdaccount = new ReCurringRd;
                $rdaccount->serialNo = $rand;
                $rdaccount->secheme_id = $opening_accounts->schemetype;
                $rdaccount->memberType = $post->member_type;
                $rdaccount->accountId = $opening_accounts->accountNo;
                $rdaccount->accountNo = $opening_accounts->membershipno;
                $rdaccount->rd_account_no = $post->rd_account_no;
                $rdaccount->amount = $post->rd_account_amount;
                $rdaccount->month = $post->rd_account_month;
                $rdaccount->date = $openingdatee;
                $rdaccount->paid_interest = !empty($post->rd_account_paid_interest) ? $post->rd_account_paid_interest : 0;
                $rdaccount->ledger_folio_no = $post->rd_account_lf_no;
                $rdaccount->misid = 0;
                $rdaccount->matureserialNo = null;
                $rdaccount->interest = $post->rd_account_interest;
                $rdaccount->maturity_date = $maturity_date;
                $rdaccount->groupCode = $scheme_group_code;
                $rdaccount->ledgerCode = $scheme_ledger_code;
                $rdaccount->actual_maturity_date = null;
                $rdaccount->actual_maturity_amount = null;
                $rdaccount->branchId = session('branchId') ? session('branchId') : 1;
                $rdaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $rdaccount->agentId = $post->rd_account_agent;
                $rdaccount->updatedBy = $post->user()->id;
                $rdaccount->status = 'Active';
                $rdaccount->save();

                // Store Installments
                $interest = $post->rd_account_interest;
                $amount = $post->rd_account_amount;
                $months = $post->rd_account_month;
                $startDate = new DateTime($openingdatee);
                $branchid = "1";
                for ($i = 1; $i <= $months; $i++) {
                    $randinstall = "RdI" . time();
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $installmentsdata = new RdInstallment;
                    $installmentsdata->serialNo = $randinstall;
                    $installmentsdata->rd_id = $rdaccount->id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = session('branchId') ? session('branchId') : 1;
                    $installmentsdata->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $installmentsdata->updatedBy = $post->user()->id;
                    $installmentsdata->save();
                }

                DB::commit();


                $deposit_amount = '';
                if(is_null($rdaccount)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('rd_receiptdetails')
                        ->select(
                            DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                        )
                        ->where('rd_account_no',$rdaccount->accountId)
                        ->where('rc_account_no',$rdaccount->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'rdaccount' => $rdaccount,
                    'deposit_amount' => $deposit_amount,
                    'opening_accounts'=> $opening_accounts,
                    'messages' => 'Data Store Successfully !!'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }

        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Account No Not Found !!']);
        }
    }

    public function RdReceiveAmount(Request $post){
        $rules = array(
            'deposit_opening_date' => 'required',
            'rd_account' => 'required',
            'payment_type' => 'required',
            'payment_bank' => 'required',
            'deposit_amount' => 'required|numeric',
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        $account = $post->rd_account;
        $rd_account = DB::table('re_curring_rds')
            ->select('re_curring_rds.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
            ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
            ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
            ->where('accountId',$account)
            ->first();

        //___________Check Account Group Or Ledger Code
        if($rd_account->groupCode && $rd_account->ledgerCode){
            $account_group_code = $rd_account->groupCode;
            $account_ledger_code = $rd_account->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Group Code And Ledger Code Not Found"],400);
        }


        //_________Get Penaily Code
        $scheme_plty_group_legder_code = DB::table('re_curring_rds')
            ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
            ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
            ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
            ->where('ledger_masters.groupCode','INCM001')
            ->where('accountId',$account)
            ->first();
        // dd($scheme_plty_group_legder_code);

        //___________Check Income Group Or Ledger Code
        if($scheme_plty_group_legder_code->groupCode && $scheme_plty_group_legder_code->ledgerCode){
            $penality_group = $scheme_plty_group_legder_code->groupCode;
            $penality_ledger = $scheme_plty_group_legder_code->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Group Code And Ledger Code Not Found"],400);
        }

        $installmentdate = date('Y-m-d', strtotime($post->deposit_opening_date));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->deposit_opening_date))) ;
        if (!$result) {
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Please Check your session"]);
        }


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        $installments = RdInstallment::where(['rd_id' => $rd_account->id])->orderBy('id', 'desc')->first();
        $paid_amount = RdInstallment::where(['rd_id' => $rd_account->id])->sum('paid_amount');

        $deposit_amount = $post->deposit_amount;
        $monthly_installment_amount = $installments->amount;
        $no_of_installments = $installments->intallment_no;
        $total_amount = $monthly_installment_amount * $no_of_installments;
        $balance_amount = $total_amount - $paid_amount;



        if(($deposit_amount % $monthly_installment_amount) != 0){
            return response()->json(['status' => 'fail', 'messages' => 'Amount should be multiple of '.$monthly_installment_amount ]);
        }

         if ($deposit_amount <= $balance_amount || $balance_amount == 0) {
             $monthsToPay = $deposit_amount / $monthly_installment_amount;

             if ($monthsToPay <= 0) {
                 return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given monthly payment.']);
             } elseif ($monthsToPay > $no_of_installments) {
                 return response()->json(['status' => 'fail', 'messages' => 'Amount is not perfect for ' . $no_of_installments . ' Month']);
             } else {
                $paymentSuccess = false;
                $penaltyApplied = false;
                $rd_ids_details = ReCurringRd::where(['accountId' => $account])->first();
                do {
                    $generalLedgers = "Rd" . time();
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);

                  DB::beginTransaction();
                 try {

                        //_______________RD Receipt
                        $lastInsertedId = DB::table('rd_receiptdetails')->insertGetId([
                                "rc_account_no" => $rd_ids_details->id,
                                "rd_account_no"  =>  $rd_ids_details->accountId,
                                "amount" => $post->deposit_amount,
                                "serialNo" => $generalLedgers,
                                "payment_date"  => $installmentdate,
                                "installment_date"  => $installmentdate,
                                'groupCode' => $post->payment_type,
                                'ledgerCode' => $post->payment_bank,
                                'memberType' => $rd_ids_details->memberType,
                                "panelty" => $post->deposit_penalty ?? 0,
                                'mis_id' =>   "",
                                "narration" => $post->naretion ?? '',
                                "entry_mode" => "manual",
                                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                                'agentid' => $post->agent_id,
                                'updatedBy' => $post->user()->id,
                            ]);


                        //__________Rd Installment
                        for ($i = 1; $i <= $monthsToPay; $i++) {
                            $distributedPayment = min($monthly_installment_amount, $deposit_amount);
                            $deposit_amount -= $distributedPayment;
                            $query = RdInstallment::where(['rd_id' => $rd_account->id, 'payment_status' => 'pending'])->first();

                            if ($query && $query->payment_status == "pending") {
                                $query->payment_date = $installmentdate;
                                    if (!$penaltyApplied) {
                                        $query->panelty = empty($request->deposit_penalty) ? 0 : $post->deposit_penalty;
                                        $penaltyApplied = true;
                                    }
                                $query->paid_amount = $distributedPayment;
                                $query->panelty = $post->deposit_penalty;
                                $query->recpt_id = $lastInsertedId;
                                $query->payment_status = "paid";
                                $query->serialNo = $generalLedgers ;
                                $query->save();
                                $paymentSuccess = true;
                            }
                        }

                        //__________________________Gerenal Ledger Rd Entry_____________________

                        //___________RD Amount Entry
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $generalLedgers;
                        $genral_ledger->accountId = $rd_ids_details->accountId;
                        $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                        $genral_ledger->memberType = $rd_ids_details->memberType;
                        $genral_ledger->groupCode = $account_group_code;
                        $genral_ledger->ledgerCode = $account_ledger_code;
                        $genral_ledger->formName = "Rd";
                        $genral_ledger->referenceNo = $lastInsertedId;
                        $genral_ledger->transactionDate = $installmentdate;
                        $genral_ledger->transactionType = "Cr";
                        $genral_ledger->transactionAmount = $post->deposit_amount;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();

                        //____________Cash/Bank Entry
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $generalLedgers;
                        $genral_ledger->accountId = $rd_ids_details->accountId;
                        $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                        $genral_ledger->memberType = $rd_ids_details->memberType;
                        $genral_ledger->formName = "Rd";
                        $genral_ledger->groupCode = $post->payment_type;
                        $genral_ledger->ledgerCode = $post->payment_bank;
                        $genral_ledger->referenceNo =$lastInsertedId;
                        $genral_ledger->transactionDate = $installmentdate;
                        $genral_ledger->transactionType = "Dr";
                        $genral_ledger->transactionAmount = $post->deposit_amount;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();


                        //________________If penality Received on Rd

                        if (!empty($post->deposit_penalty) && $post->deposit_penalty != 0) {
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $generalLedgers;
                            $genral_ledger->accountId = $rd_ids_details->accountId;
                            $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                            $genral_ledger->memberType = $rd_ids_details->memberType;
                            $genral_ledger->groupCode = $penality_group;
                            $genral_ledger->ledgerCode = $penality_ledger;
                            $genral_ledger->formName = "Rd-Penalty";
                            $genral_ledger->referenceNo =$lastInsertedId;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = "Cr";
                            $genral_ledger->transactionAmount = $post->deposit_penalty;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();


                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $generalLedgers;
                            $genral_ledger->accountId = $rd_ids_details->accountId;
                            $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                            $genral_ledger->memberType = $rd_ids_details->memberType;
                            $genral_ledger->groupCode = $post->payment_type;
                            $genral_ledger->ledgerCode = $post->payment_bank;
                            $genral_ledger->formName = "Rd-Penalty";
                            $genral_ledger->referenceNo = $lastInsertedId;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = "Dr";
                            $genral_ledger->transactionAmount = $post->deposit_penalty;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();
                        }

                        $changestatus = RdInstallment::where(['rd_id' => $rd_ids_details->id])->orderBy('intallment_no', 'desc')->first();
                        if ($changestatus->payment_status == "paid") {
                            $moodifystatus = ReCurringRd::where(['id' => $rd_ids_details->id])->update(['status' => 'Active']);
                        }

                        if ($paymentSuccess) {
                            DB::commit();

                            $total = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('paid_amount');
                            $totalpanality = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('panelty');
                            $grand_total = $total + $totalpanality;

                            $deposit_amount = '';
                            if(is_null($rd_account)){
                                $deposit_amount = '';
                            }else{
                                $deposit_amount = DB::table('rd_receiptdetails')
                                    ->select(
                                        DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                                        DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                                    )
                                    ->where('rd_account_no',$rd_account->accountId)
                                    ->where('rc_account_no',$rd_account->id)
                                    ->get();
                            }

                            return response()->json([
                                'status' => 'success',
                                'total' => $total,
                                'payment_id' => $rd_ids_details->id,
                                'panality' => $totalpanality,
                                'rd_account' => $rd_account,
                                'deposit_amount' => $deposit_amount,
                                'messages' => "Installment Paid Successfully !!"
                            ]);
                        } else {
                            return response()->json(['status' => 'fail', 'messages' => 'Installment payment failed.']);
                        }
                } catch (\Exception $e) {
                     DB::rollBack();
                     return response()->json(['status' => "fail", "messages" => "Some Technical issue occurred",'error' => $e->getMessage()], 200);
                }
             }
         } else {
             return response()->json(['status' => 'fail', 'messages' => 'Installment payment amount not satisfy.']);
         }
    }

    public function GetInstallmentsDetails(Request $post){
        $rdaccountnumber = $post->rdaccountnumber;
        $rd_accounts = DB::table('re_curring_rds')
            ->where('rd_account_no',$rdaccountnumber)
            ->first();

        $received_amount = DB::table('rd_receiptdetails')
            ->select(
                'rd_receiptdetails.*','re_curring_rds.id as rd_id','re_curring_rds.amount as rd_amount',
                're_curring_rds.month as rd_month','re_curring_rds.status as rdstatus',
                'users.id as userid','users.name'
                )
            ->join('re_curring_rds','re_curring_rds.id','=','rd_receiptdetails.rc_account_no')
            ->leftJoin('users','users.id','=','rd_receiptdetails.updatedBy')
            ->where('rd_receiptdetails.rd_account_no',$rd_accounts->accountId)
            ->orderBy('installment_date','ASC')
            ->get();

        $installments = DB::table('rd_installments')
            ->select('rd_installments.*','users.id as userid','users.name')
            ->leftJoin('users','users.id','=','rd_installments.updatedBy')
            ->where('rd_id',$rd_accounts->id)
            ->where('payment_status','paid')
            ->orderBy('id', 'desc')
            ->get();


        if(!empty($received_amount) || !empty($rd_accounts)){
            return response()->json([
                'status' => 'success',
                'received_amount' => $received_amount,
                'installments' => $installments,
                'rd_accounts' => $rd_accounts
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function ViewInstallmentsDetails(Request $post){
        $id = $post->accountId;
        $rd_accounts = DB::table('re_curring_rds')
            ->where('id',$id)
            ->first();


        $installments = DB::table('rd_installments')
            ->where('rd_id',$rd_accounts->id)
            ->orderBy('intallment_no', 'ASC')
            ->get();



        if(!empty($installments)){
            return response()->json([
                'status' => 'success',
                'installments' => $installments,
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function DeleteInstallments(Request $post){
        $id = $post->id;

        // Retrieve receipt details
        $receipt_id = DB::table('rd_receiptdetails')->where('id',$id)->first();

        $date = $receipt_id->installment_date;
        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        if($start_date > $date ||  $date < $end_date){
            $account = $receipt_id->rd_account_no;
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Session Date With Transaction Date'
            ]);
        }



        if (!$receipt_id) {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Record Not Found'
            ]);
        }
        DB::beginTransaction();

        try {

            // Retrieve RD account details if receipt exists
            $rd_account = DB::table('re_curring_rds')
                ->select('re_curring_rds.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('accountId', $account)
                ->first();

            // Delete general ledger records associated with this receipt
            DB::table('general_ledgers')
                ->where('serialNo', $receipt_id->serialNo)
                ->where('referenceNo', $receipt_id->id)
                ->delete();

            // Update installments if any exist for this receipt
            $installmentsUpdated = DB::table('rd_installments')
                ->where('recpt_id', $receipt_id->id)
                ->exists();

            if ($installmentsUpdated) {
                DB::table('rd_installments')
                    ->where('recpt_id', $receipt_id->id)
                    ->update([
                        'serialNo' => $rd_account->serialNo,
                        'payment_status' => 'pending',
                        'paid_amount' => 0,
                        'panelty' => 0,
                        'payment_date' => null,
                        'recpt_id' => null
                    ]);
            }

            // Delete the receipt detail entry
            DB::table('rd_receiptdetails')->where('id', $id)->delete();

            // Mark the RD account as Active if linked
            if ($rd_account) {
                DB::table('re_curring_rds')
                    ->where('id', $receipt_id->rc_account_no)
                    ->update(['status' => 'Active']);
            }

            DB::commit();

            // Retrieve updated deposit and penalty amounts, if available
            $deposit_amount = DB::table('rd_receiptdetails')
                ->select(
                    DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                    DB::raw('SUM(rd_receiptdetails.panelty) as penalty')
                )
                ->where('rd_account_no', $rd_account->accountId ?? 0)
                ->where('rc_account_no', $rd_account->id ?? 0)
                ->first();

            return response()->json([
                'status' => 'success',
                'rd_account' => $rd_account,
                'deposit_amount' => $deposit_amount,
                'messages' => 'Record Deleted Successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred while deleting the record',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function RdAmountUpdatereceive(Request $post){

        $validator = Validator::make($post->all(),[
            'deposit_opening_date' => 'required',
            'edit_rd_account' => 'required',
            'rdid' => 'required',
            'payment_type' => 'required',
            'payment_bank' => 'required',
            'deposit_amount' => 'required|numeric'
        ]);

        if($validator->passes()){
            $rowid = $post->rdid;

            if ($rowid) {
                $check_rd_no = RdReceiptdetails::where('id',$rowid)->first();
                // dd($check_rd_no);
                $rd_account = DB::table('re_curring_rds')
                    ->select('re_curring_rds.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                    ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                    ->where('accountId',$check_rd_no->rd_account_no)
                    ->first();

                if ($check_rd_no) {
                    $rd_ids_details = DB::table('re_curring_rds')
                        ->select('re_curring_rds.*','scheme_masters.id as sch_id')
                        ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                        ->where('re_curring_rds.id', $check_rd_no->rc_account_no)
                        ->orderBy('re_curring_rds.date', 'ASC')
                        ->first();


                        $date = date('Y-m-d', strtotime($post->deposit_opening_date));

                        $session_master = SessionMaster::find(Session::get('sessionId'));

                        $start_date = $session_master->startDate;
                        $end_date = $session_master->endDate;

                        if($session_master->auditPerformed === 'Yes'){
                            return response()->json([
                                'status' => 'fail',
                                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
                            ]);
                        }


                        $result = $this->isDateBetween($date) ;

                        if (!$result) {
                            return response()->json(['status'=>'fail', 'messages' => "Please Check your session"]);
                        }

                        //______dates
                        $transDate = $date;

                        //_________Check Account Group Or Ledger
                        if($rd_ids_details->groupCode && $rd_ids_details->ledgerCode){
                            $account_group = $rd_ids_details->groupCode;
                            $account_ledger = $rd_ids_details->ledgerCode;
                        }else{
                            return response()->json(['status' => 'Fail','messages' => 'Account Group Code Or Ledger Code Not Found']);
                        }

                    //_________Get Penaily Code
                    $scheme_plty_group_legder_code = DB::table('re_curring_rds')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','INCM001')
                        ->where('accountId',$rd_ids_details->accountId)
                        ->first();

                        if($scheme_plty_group_legder_code->groupCode && $scheme_plty_group_legder_code->ledgerCode){
                            $penality_group = $scheme_plty_group_legder_code->groupCode;
                            $penality_ledger = $scheme_plty_group_legder_code->ledgerCode;
                        }else{
                            return response()->json(['status' => 'Fail','messages' => 'Penality Group Code Or Ledger Code Not Found']);
                        }


                    $ledger = GeneralLedger::where('serialNo',$check_rd_no->serialNo)->where('is_delete','No')->get();


                    if(!$ledger){
                         return response()->json(['status' => 'fail', 'messages' => "Data Not found"]);
                    }

                    $last_amount = $check_rd_no->amount;
                    $latest_amount_cr = $post->deposit_amount;

                    $lastinstallmet = RdInstallment::where(['rd_id' => $check_rd_no->rc_account_no])->orderBy('id', 'desc')->first();

                    if ($last_amount != 0 && $latest_amount_cr % $lastinstallmet->amount === 0) {
                        DB::beginTransaction();
                        try {

                            RdInstallment::where('serialNo',$check_rd_no->serialNo)->update([
                                'payment_status'=>'pending',
                                'paid_amount'=>0,
                                'payment_date'=>null,
                                'panelty'=>0,
                                'recpt_id' => null
                            ]);

                            $paidamount =  RdInstallment::where(['rd_id' => $check_rd_no->rc_account_no])->sum('paid_amount');
                            $totalRdAmount = $lastinstallmet->amount * $lastinstallmet->intallment_no;
                            $totaldueRd =  $totalRdAmount - $paidamount;

                            if ($post->deposit_amount <= $totaldueRd) {

                                $monthsToPay = $post->deposit_amount / $lastinstallmet->amount;

                                if ($monthsToPay <= 0 && $monthsToPay > $lastinstallmet->intallment_no) {
                                    return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given monthly payment.']);
                                }



                                //_______update rd recived
                                $check_rd_no->payment_date = $transDate;
                                $check_rd_no->amount = $latest_amount_cr;
                                $check_rd_no->panelty = $post->deposit_penalty ? $post->deposit_penalty : 0;
                                $check_rd_no->matureserialNo =  $check_rd_no->serialNo;
                                $check_rd_no->save();

                                //______update installmets
                                for ($i = 1; $i <= $monthsToPay; $i++) {
                                    $pendinginstalment = RdInstallment::where([
                                        'rd_id' => $check_rd_no->rc_account_no,
                                        'payment_status' => 'pending'])->first();

                                    if ($pendinginstalment && $pendinginstalment->payment_status == "pending") {
                                        $pendinginstalment->panelty = empty($post->deposit_penalty) ? 0 : $post->deposit_penalty;
                                        $pendinginstalment->payment_date = $transDate;
                                        $pendinginstalment->paid_amount = $lastinstallmet->amount;
                                        $pendinginstalment->payment_status = "paid";
                                        $pendinginstalment->serialNo = $check_rd_no->serialNo ;
                                        $pendinginstalment->recpt_id = $check_rd_no->id;
                                        $pendinginstalment->save();
                                    }
                                }

                                //_______________Delete Gerenal ledger
                                GeneralLedger::where('serialNo',$check_rd_no->serialNo)
                                    ->where('referenceNo',$check_rd_no->id)
                                    ->where('is_delete','No')
                                    ->delete();



                            //__________________________Gerenal Ledger Rd Entry_____________________

                            //___________RD Amount Entry
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $check_rd_no->serialNo;
                            $genral_ledger->accountId = $rd_ids_details->accountId;
                            $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                            $genral_ledger->memberType = $rd_ids_details->memberType;
                            $genral_ledger->groupCode = $account_group;
                            $genral_ledger->ledgerCode = $account_ledger;
                            $genral_ledger->formName = "Rd";
                            $genral_ledger->referenceNo = $check_rd_no->id;
                            $genral_ledger->transactionDate = $transDate;
                            $genral_ledger->transactionType = "Cr";
                            $genral_ledger->transactionAmount = $check_rd_no->amount;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            //____________Cash/Bank Entry
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $check_rd_no->serialNo;
                            $genral_ledger->accountId = $rd_ids_details->accountId;
                            $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                            $genral_ledger->memberType = $rd_ids_details->memberType;
                            $genral_ledger->formName = "Rd";
                            $genral_ledger->groupCode = $post->payment_type;
                            $genral_ledger->ledgerCode = $post->payment_bank;
                            $genral_ledger->referenceNo = $check_rd_no->id;
                            $genral_ledger->transactionDate = $transDate;
                            $genral_ledger->transactionType = "Dr";
                            $genral_ledger->transactionAmount = $check_rd_no->amount;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();


                            //________________If penality Received on Rd

                            if (!empty($post->deposit_penalty) && $post->deposit_penalty != 0) {
                                $genral_ledger = new GeneralLedger;
                                $genral_ledger->serialNo = $check_rd_no->serialNo;
                                $genral_ledger->accountId = $rd_ids_details->accountId;
                                $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                                $genral_ledger->memberType = $rd_ids_details->memberType;
                                $genral_ledger->groupCode = $penality_group;
                                $genral_ledger->ledgerCode = $penality_ledger;
                                $genral_ledger->formName = "Rd-Penalty";
                                $genral_ledger->referenceNo = $check_rd_no->id;
                                $genral_ledger->transactionDate = $transDate;
                                $genral_ledger->transactionType = "Cr";
                                $genral_ledger->transactionAmount = $post->deposit_penalty;
                                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                                $genral_ledger->agentId = $post->agent_id;
                                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                                $genral_ledger->updatedBy = $post->user()->id;
                                $genral_ledger->save();

                                $genral_ledger = new GeneralLedger;
                                $genral_ledger->serialNo = $check_rd_no->serialNo;
                                $genral_ledger->accountId = $rd_ids_details->accountId;
                                $genral_ledger->accountNo = $rd_ids_details->rd_account_no;
                                $genral_ledger->memberType = $rd_ids_details->memberType;
                                $genral_ledger->groupCode = $post->payment_type;
                                $genral_ledger->ledgerCode = $post->payment_bank;
                                $genral_ledger->formName = "Rd-Penalty";
                                $genral_ledger->referenceNo = $check_rd_no->id;
                                $genral_ledger->transactionDate = $transDate;
                                $genral_ledger->transactionType = "Dr";
                                $genral_ledger->transactionAmount = $post->deposit_penalty;
                                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                                $genral_ledger->agentId = $post->agent_id;
                                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                                $genral_ledger->updatedBy = $post->user()->id;
                                $genral_ledger->save();
                            }

                                DB::commit();

                                $deposit_amount = '';
                                if(is_null($check_rd_no)){
                                    $deposit_amount = '';
                                }else{
                                    $deposit_amount = DB::table('rd_receiptdetails')
                                        ->select(
                                            DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                                            DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                                        )
                                        ->where('rd_account_no',$rd_account->accountId)
                                        ->where('rc_account_no',$rd_account->id)
                                        ->get();
                                }

                                $total =  RdInstallment::where(['rd_id' => $check_rd_no->rc_account_no])->sum('paid_amount');
                                $panelty =  RdReceiptdetails::where(['rc_account_no' => $check_rd_no->rc_account_no,'is_delete'=>"No"])->sum('panelty');
                                return response()->json([
                                    'status' => 'success',
                                    'messages' => 'Reciept Update SuccessFully !!',
                                    'total' => $total,
                                    'rd_account' => $rd_account,
                                    'deposit_amount' => $deposit_amount,
                                    'receipt' => $check_rd_no->rc_account_no,
                                    'panelty'=>$panelty
                                ]);
                            }
                        }catch(\Exception $e){
                            DB::rollBack();
                            return response()->json([
                                'status' => 'fail',
                                'messages' => 'An error occurred while deleting the record',
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }

    }

    public function GetRDMatureData(Request $post){
        $accountNo = $post->accountNo;

        if ($accountNo) {

           $rd_acount =  DB::table('re_curring_rds')
                ->select('re_curring_rds.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('re_curring_rds.rd_account_no', $accountNo)
                ->orderBy('re_curring_rds.date', 'ASC')
                ->first();

            $totalinstallmentpaid = RdInstallment::where(['rd_id' => $rd_acount->id])->sum('paid_amount');

            return response()->json(['status' => 'success', 'totalpaid' => $totalinstallmentpaid, 'details' => $rd_acount]);
        } else {
            return response()->json(['status' => 'fail', 'messages' => "something went wrong"]);
        }
    }

    public function GetSavingAccountno(Request $post){
        $rd_account = $post->rd_account;
        $opening_account = DB::table('opening_accounts')
            ->where('opening_accounts.accountNo', $rd_account)
            ->where('opening_accounts.accountname', 'RD')
            ->where('opening_accounts.status', 'Active')
            ->first();

        if($opening_account){
            $saving_account = DB::table('opening_accounts')
                ->select(
                    'opening_accounts.*','schmeaster.id as sch_id','schmeaster.scheme_code',
                    'ledger_masters.reference_id','ledger_masters.ledgerCode','ledger_masters.groupCode',
                    'refSchemeMaster.scheme_code as ref_scheme_code','refSchemeMaster.months'
                )
                ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                ->where('opening_accounts.membershipno', $opening_account->membershipno)
                ->where('accountname','Saving')
                ->where('opening_accounts.status','Active')
                ->first();

            if(empty($opening_account) || !empty($saving_account)){
                return response()->json([
                    'status' => 'success',
                    'details' => $saving_account,
                    'opening_account' => $opening_account
                ]);
            }else{
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Record Not Found'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function RdMature(Request $post){

        $rules = array(
            'rd_mature_date' => 'required',
            'account_id' => 'required|numeric',
            'rd_mature_amount_receive' => 'required|numeric',
            'rd_mature_actual_interest' => 'numeric',
            'rd_mature_actual_penality_value' => 'numeric',
            'rdtotalnewamount' => 'numeric',
            'payment_type' => 'required',
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        $account_id = $post->account_id;

        $opening_account = DB::table('opening_accounts')
            ->select('opening_accounts.*','member_accounts.accountNo as membership','member_accounts.name as customer_name')
            ->leftJoin('member_accounts','member_accounts.accountNo','=','opening_accounts.membershipno')
            ->where('opening_accounts.accountNo',$account_id)
            ->where('opening_accounts.accountname','=','RD')
            ->first();

        $rand = "RD" . time();
        $payment_type = $post->payment_type;


        $date = date('Y-m-d', strtotime($post->rd_mature_date));


        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        if($start_date >= $date ||  $date <= $end_date){
            $todaydate = $date;

            if($payment_type === 'TRASFER'){
                DB::beginTransaction();

                try{

                    //_________saving Account Number
                    $accountNo = $post->saving;

                    //___________Get RD Account Number
                    $rd_account = DB::table('re_curring_rds')
                        ->select('re_curring_rds.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('accountId',$account_id)
                        ->first();

                        if($rd_account->groupCode && $rd_account->ledgerCode){
                            $rd_group = $rd_account->groupCode;
                            $rd_ledger = $rd_account->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'RD Group Code Or Ledger Code Not Found'
                            ]);
                        }


                    //____________Get Openning Details For Exp|Income Group|Ledger Code
                    $saving_account = DB::table('opening_accounts')
                        ->select(
                            'opening_accounts.*','schmeaster.id as sch_id','schmeaster.scheme_code',
                            'ledger_masters.reference_id','ledger_masters.ledgerCode','ledger_masters.groupCode',
                            'refSchemeMaster.scheme_code as ref_scheme_code','refSchemeMaster.months'
                        )
                        ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                        ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                        ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                        ->where('opening_accounts.membershipno', $accountNo)
                        ->where('accountname','Saving')
                        ->where('opening_accounts.status','Active')
                        ->first();

                        if($saving_account->groupCode && $saving_account->ledgerCode){
                            $saving_group = $saving_account->groupCode;
                            $saving_ledger = $saving_account->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Saving Group Code Or Ledger Code Not Found'
                            ]);
                        }

                    //_________Get Penaily Code
                    $scheme_plty_group_legder_code = DB::table('re_curring_rds')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','INCM001')
                        ->where('accountId',$account_id)
                        ->first();


                        if($scheme_plty_group_legder_code->groupCode && $scheme_plty_group_legder_code->ledgerCode){
                            $penality_group = $scheme_plty_group_legder_code->groupCode;
                            $penality_ledger = $scheme_plty_group_legder_code->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Penality Group Code Or Ledger Code Not Found'
                            ]);
                        }


                    //_________Get Interest Paid Code
                    $scheme_interest_group_legder_code = DB::table('re_curring_rds')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','EXPN001')
                        ->where('accountId',$account_id)
                        ->first();

                        if($scheme_interest_group_legder_code->groupCode && $scheme_interest_group_legder_code->ledgerCode){
                            $interest_group = $scheme_interest_group_legder_code->groupCode;
                            $interest_ledger = $scheme_interest_group_legder_code->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Interest Group Code Or Ledger Code Not Found'
                            ]);
                        }



                    //___________Get Amount
                    $rd_amount = $post->rd_mature_amount_receive ?? 0;
                    $interest_amount = $post->rd_mature_actual_interest ?? 0;
                    $penality_amount = $post->rd_mature_actual_penality_value ?? 0;

                    $mature_amount = (($rd_amount + $interest_amount) - $penality_amount);


                    //_________RD Trfd To Saving Account Entry
                    $savingacc = new MemberSaving;
                    $savingacc->serialNo = $rand;
                    $savingacc->secheme_id = $saving_account->schemetype;
                    $savingacc->accountId = $accountNo;
                    $savingacc->accountNo = $saving_account->membershipno;
                    $savingacc->memberType = $saving_account->membertype;
                    $savingacc->groupCode = $saving_group;
                    $savingacc->ledgerCode = $saving_ledger;
                    $savingacc->savingNo = $accountNo;
                    $savingacc->transactionDate = $todaydate;
                    $savingacc->chequeNo = 'trfdFromRD';
                    $savingacc->transactionType = 'Deposit';
                    $savingacc->depositAmount = $mature_amount;
                    $savingacc->withdrawAmount = 0;
                    $savingacc->paymentType = null;
                    $savingacc->bank = null;
                    $savingacc->narration = "Amount Transfer From RD account ".$post->mature_account_no;
                    $savingacc->branchId = session('branchId') ? session('branchId') : 1;
                    $savingacc->sessionId =  session('sessionId') ? session('sessionId') : 1;
                    $savingacc->agentId = $saving_account->agentId;
                    $savingacc->updatedBy = $post->user()->id;
                    $savingacc->is_delete = 'No';
                    $savingacc->save();



                    //_________Rd Gerenal Ledger Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $rd_account->accountId;
                    $genral_ledger->accountNo = $rd_account->rd_account_no;
                    $genral_ledger->memberType = $rd_account->memberType;
                    $genral_ledger->groupCode = $rd_group;
                    $genral_ledger->ledgerCode = $rd_ledger;
                    $genral_ledger->formName = "Rd-Mature";
                    $genral_ledger->referenceNo = $rd_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Dr";
                    $genral_ledger->transactionAmount = $rd_amount;
                    $genral_ledger->narration = "Amount Transfer To Saving Acc ".$accountNo;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();



                    //_________Saving Gerenal Ledger Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $savingacc->accountId;
                    $genral_ledger->accountNo = $savingacc->accountNo;
                    $genral_ledger->memberType = $savingacc->memberType;
                    $genral_ledger->groupCode = $saving_group;
                    $genral_ledger->ledgerCode = $saving_ledger;
                    $genral_ledger->formName = "Rd-Saving";
                    $genral_ledger->referenceNo = $rd_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Cr";
                    $genral_ledger->transactionAmount = $mature_amount;
                    $genral_ledger->narration = "Amount Transfer From RD Acc ".$rd_account->rd_account_no;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();



                    //___________if Interest Amount is Greater Then 0
                    if($interest_amount && $interest_amount > 0){
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $rand;
                        $genral_ledger->accountId = $rd_account->accountId;
                        $genral_ledger->accountNo = $rd_account->accountNo;
                        $genral_ledger->memberType = $rd_account->memberType;
                        $genral_ledger->groupCode = $interest_group;
                        $genral_ledger->ledgerCode = $interest_ledger;
                        $genral_ledger->formName = "Rd-Interest Paid";
                        $genral_ledger->referenceNo = $rd_account->id;
                        $genral_ledger->transactionDate = $todaydate;
                        $genral_ledger->transactionType = "Dr";
                        $genral_ledger->transactionAmount = $interest_amount;
                        $genral_ledger->narration = $scheme_interest_group_legder_code->name.'-'.$rd_account->rd_account_no;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();
                    }



                    //___________if Penality Amount is Greater Then 0
                    if($penality_amount && $penality_amount > 0){
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $rand;
                        $genral_ledger->accountId = $rd_account->accountId;
                        $genral_ledger->accountNo = $rd_account->accountNo;
                        $genral_ledger->memberType = $rd_account->memberType;
                        $genral_ledger->groupCode = $penality_group;
                        $genral_ledger->ledgerCode = $penality_ledger;
                        $genral_ledger->formName = "Rd-Penality Rec.";
                        $genral_ledger->referenceNo = $rd_account->id;
                        $genral_ledger->transactionDate = $todaydate;
                        $genral_ledger->transactionType = "Cr";
                        $genral_ledger->transactionAmount = $penality_amount;
                        $genral_ledger->narration = $scheme_plty_group_legder_code->name.'-'.$rd_account->rd_account_no;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();
                    }

                    //_________Check Maturity Date Status
                    $maturity_date = new DateTime($rd_account->maturity_date);
                    $todaydate = new DateTime();
                    $status = '';

                    //___________If Today Date Greater Then Maturity
                    if($todaydate > $maturity_date) {
                        $status = 'Mature';
                    } else {
                        $status = 'PreMature';
                    }

                    $rd_account = ReCurringRD::where('accountId', $account_id)->first();

                    //__________Account Open Table Update Status
                    $account_open = opening_accounts::where('accountNo',$rd_account->accountId)->where('accountname','RD')->first();
                    $account_open->status = 'Closed';
                    $account_open->save();

                    //___________Rd Recurring Table Fileds Update
                    $rd_account->actual_maturity_date = $date;
                    $rd_account->maturity_amount = $mature_amount;
                    $rd_account->matureserialNo = $rand;
                    $rd_account->rd_type = "SAVING";
                    $rd_account->status = $status;
                    $rd_account->save();


                    $deposit_amount = '';
                    if(is_null($rd_account)){
                        $deposit_amount = '';
                    }else{
                        $deposit_amount = DB::table('rd_receiptdetails')
                            ->select(
                                DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                                DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                            )
                            ->where('rd_account_no',$rd_account->accountId)
                            ->where('rc_account_no',$rd_account->id)
                            ->get();
                    }


                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'messages' => 'Maturity Amount paid successfully !!',
                        'rd_account' => $rd_account,
                        'opening_account' => $opening_account,
                        'deposit_amount' => $deposit_amount
                    ]);
                }catch(\Exception $e){
                    DB::rollBack();
                    return response()->json([
                        'status' => 'fail',
                        'messages' => 'An error occurred while deleting the record',
                        'error' => $e->getMessage()
                    ]);
                }
            }else{
                DB::beginTransaction();

                try{
                    //____________Date Conversion
                    $todaydate =  date('Y-m-d', strtotime($post->rd_mature_date));

                    //___________Get RD Account Number
                    $rd_account = DB::table('re_curring_rds')
                        ->select('re_curring_rds.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('accountId',$account_id)
                        ->first();

                        if($rd_account->groupCode && $rd_account->ledgerCode){
                            $rd_groups = $rd_account->groupCode;
                            $rd_legders = $rd_account->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Rd Account Group && Ledger Not Found'
                            ]);
                        }


                    //_________Get Penaily Code
                    $scheme_plty_group_legder_code = DB::table('re_curring_rds')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','INCM001')
                        ->where('accountId',$account_id)
                        ->first();


                        if($scheme_plty_group_legder_code->groupCode && $scheme_plty_group_legder_code->ledgerCode){
                            $penality_groups = $scheme_plty_group_legder_code->groupCode;
                            $penality_ledgers = $scheme_plty_group_legder_code->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Interest Group && Ledger Not Found'
                            ]);
                        }


                    //_________Get Interest Paid Code
                    $scheme_interest_group_legder_code = DB::table('re_curring_rds')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','re_curring_rds.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','EXPN001')
                        ->where('accountId',$account_id)
                        ->first();


                        if($scheme_interest_group_legder_code->groupCode && $scheme_interest_group_legder_code->ledgerCode){
                            $interest_groups = $scheme_interest_group_legder_code->groupCode;
                            $interest_ledgers = $scheme_interest_group_legder_code->ledgerCode;
                        }else{
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Interest Group && Ledger Not Found'
                            ]);
                        }


                    //___________Get Amount
                    $rd_amount = $post->rd_mature_amount_receive ?? 0;
                    $interest_amount = $post->rd_mature_actual_interest ?? 0;
                    $penality_amount = $post->rd_mature_actual_penality_value ?? 0;

                    $mature_amount = (($rd_amount + $interest_amount) - $penality_amount);


                    //_________Rd Gerenal Ledger Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $rd_account->accountId;
                    $genral_ledger->accountNo = $rd_account->rd_account_no;
                    $genral_ledger->memberType = $rd_account->memberType;
                    $genral_ledger->groupCode = $rd_groups;
                    $genral_ledger->ledgerCode = $rd_legders;
                    $genral_ledger->formName = "Rd-Mature";
                    $genral_ledger->referenceNo = $rd_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Dr";
                    $genral_ledger->transactionAmount = $rd_amount;
                    $genral_ledger->narration = 'To Amt. Paid'.$rd_account->rd_account_no;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();

                    //_________Cash Gerenal Ledger Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $rd_account->accountId;
                    $genral_ledger->accountNo = $rd_account->rd_account_no;
                    $genral_ledger->memberType = $rd_account->memberType;
                    $genral_ledger->groupCode = $post->payment_type;
                    $genral_ledger->ledgerCode = $post->ledgercodess;
                    $genral_ledger->formName = "Rd-Saving";
                    $genral_ledger->referenceNo = $rd_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Cr";
                    $genral_ledger->transactionAmount = $mature_amount;
                    $genral_ledger->narration = 'To Amt. Paid'.$rd_account->rd_account_no;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();



                    //___________if Interest Amount is Greater Then 0
                    if($interest_amount && $interest_amount > 0){
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $rand;
                        $genral_ledger->accountId = $rd_account->accountId;
                        $genral_ledger->accountNo = $rd_account->accountNo;
                        $genral_ledger->memberType = $rd_account->memberType;
                        $genral_ledger->groupCode = $interest_groups;
                        $genral_ledger->ledgerCode = $interest_ledgers;
                        $genral_ledger->formName = "Rd-Interest Paid";
                        $genral_ledger->referenceNo = $rd_account->id;
                        $genral_ledger->transactionDate = $todaydate;
                        $genral_ledger->transactionType = "Dr";
                        $genral_ledger->transactionAmount = $interest_amount;
                        $genral_ledger->narration = $scheme_interest_group_legder_code->name.'-'.$rd_account->rd_account_no;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();
                    }




                    //___________if Penality Amount is Greater Then 0
                    if($penality_amount && $penality_amount > 0){
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $rand;
                        $genral_ledger->accountId = $rd_account->accountId;
                        $genral_ledger->accountNo = $rd_account->accountNo;
                        $genral_ledger->memberType = $rd_account->memberType;
                        $genral_ledger->groupCode = $penality_groups;
                        $genral_ledger->ledgerCode = $penality_ledgers;
                        $genral_ledger->formName = "Rd-Penality Rec.";
                        $genral_ledger->referenceNo = $rd_account->id;
                        $genral_ledger->transactionDate = $todaydate;
                        $genral_ledger->transactionType = "Cr";
                        $genral_ledger->transactionAmount = $penality_amount;
                        $genral_ledger->narration = $scheme_plty_group_legder_code->name.'-'.$rd_account->rd_account_no;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();
                    }

                    //_________Check Maturity Date Status
                    $maturity_date = new DateTime($rd_account->maturity_date);
                    $todaydate = new DateTime();
                    $status = '';

                    //___________If Today Date Greater Then Maturity
                    if($todaydate > $maturity_date) {
                        $status = 'Mature';
                    } else {
                        $status = 'PreMature';
                    }

                    $rd_account = ReCurringRD::where('accountId', $account_id)->first();

                    //__________Account Open Table Update Status
                    $account_open = opening_accounts::where('accountNo',$rd_account->accountId)->where('accountname','RD')->first();
                    $account_open->status = 'Closed';
                    $account_open->save();

                    //___________Rd Recurring Table Fileds Update
                    $rd_account->actual_maturity_date = date('Y-m-d',strtotime($post->rd_mature_date));
                    $rd_account->maturity_amount = $mature_amount;
                    $rd_account->matureserialNo = $rand;
                    $rd_account->status = $status;
                    $rd_account->save();

                    DB::commit();


                    $deposit_amount = '';
                    if(is_null($rd_account)){
                        $deposit_amount = '';
                    }else{
                        $deposit_amount = DB::table('rd_receiptdetails')
                            ->select(
                                DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                                DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                            )
                            ->where('rd_account_no',$rd_account->accountId)
                            ->where('rc_account_no',$rd_account->id)
                            ->get();
                    }

                    return response()->json([
                        'status' => 'success',
                        'messages' => 'Maturity Amount paid successfully !!',
                        'rd_account' => $rd_account,
                        'opening_account' => $opening_account,
                        'deposit_amount' => $deposit_amount
                    ]);


                }catch(\Exception $e){
                    DB::rollBack();
                    return response()->json([
                        'status' => 'fail',
                        'messages' => 'An error occurred while deleting the record',
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }else{
            return response()->json(['status' => 'fail','messages'=> 'Checked Session With Transcation Date']);
        }

    }

    public function rdunmature(Request $post){

        //_____Rd Account Number
        $account_id = $post->accountNo;
        $opening_account = DB::table('opening_accounts')
            ->select('opening_accounts.*','member_accounts.accountNo as membership','member_accounts.name as customer_name')
            ->leftJoin('member_accounts','member_accounts.accountNo','=','opening_accounts.membershipno')
            ->where('opening_accounts.accountNo',$account_id)
            ->where('opening_accounts.accountname','=','RD')
            ->first();

        $rd = ReCurringRD::where('accountId',$account_id)->first();


        $date = $rd->actual_maturity_date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        // if($start_date >= $date ||  $date <= $end_date){
            DB::beginTransaction();

            try{

                $rand = "RD" . time();
                //__________Account Open Table Update Status
                $account_open = opening_accounts::where('accountNo',$account_id)
                    ->where('accountname','RD')
                    ->update(['status' => 'Active']);


                //__________RD Ruccring Table Table Update Status
                $rd = ReCurringRD::where('accountId',$account_id)->first();

                //_____________Gerenal Ledger Delete Entry
                $gerenal_ledger = DB::table('general_ledgers')
                    ->where('serialNo',$rd->matureserialNo)
                    ->delete();


                //_____________Saving account Delete Entry
                $saving_account = DB::table('member_savings')
                    ->where('serialNo',$rd->matureserialNo)
                    ->get();

                if($saving_account){
                    //_____________Saving account Delete Entry
                    $saving_account = DB::table('member_savings')
                    ->where('serialNo',$rd->matureserialNo)
                    ->delete();
                }


                //______After All Updatation then Update Rd Table
                $rd->actual_maturity_date = null;
                $rd->maturity_amount = 0;
                $rd->matureserialNo = $rand;
                $rd->rd_type = "SAVING";
                $rd->status = 'Active';
                $rd->save();

                DB::commit();

                $deposit_amount = '';
                if(is_null($rd)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('rd_receiptdetails')
                        ->select(
                            DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                        )
                        ->where('rd_account_no',$rd->accountId)
                        ->where('rc_account_no',$rd->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'messages' => 'Record Updated Successfully',
                    'rd_account' => $rd,
                    'opening_account' => $opening_account,
                    'deposit_amount' => $deposit_amount
                ]);

            }catch(\Exception $e){
                DB::rollback();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }
        // }else{
        //     return response()->json([
        //         'status' => 'fail',
        //         'messages' => 'Check Session Date With Your Transcation Date'
        //     ]);
        // }





    }

    public function DeleteRd(Request $post){

        $rdid = $post->id;
        $rd_account = DB::table('re_curring_rds')->where('id', $rdid)->where('status', 'Active')->first();

        $date = $rd_account->date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        if($start_date >= $date ||  $date <= $end_date){
            if (is_null($rd_account)) {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            } else {
                DB::beginTransaction();
                try{
                    DB::table('rd_installments')->where('rd_id', $rd_account->id)->delete();

                    DB::table('re_curring_rds')->where('id', $rdid)->delete();

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'messages' => 'Record Deleted Successfully'
                    ]);
                }catch(\Exception $e){
                    DB::rollBack();
                    return response()->json([
                        'status' => 'fail',
                        'messages' => 'An error occurred while deleting the record',
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Session Date With Transaction Date'
            ]);
        }




    }

    public function RdModify(Request $post){
        $rdid = $post->id;
        $rd_account = DB::table('re_curring_rds')->where('id', $rdid)->where('status', 'Active')->first();

        $date = $rd_account->date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        if($start_date >= $date &&  $date <= $end_date){
            if(is_null($rd_account)){
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Record Not Found'
                ]);
            }else{
                return response()->json([
                    'status' => 'success',
                    'rd_account' => $rd_account
                ]);
            }
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Session Date With Transaction Date'
            ]);
        }
    }

    public function RdUpdate(Request $post){

        $rules = array(
            'rd_opening_date' => 'required',
            'member_type' => 'required',
            'rd_account_no' => 'required|numeric',
            'rd_account_amount' => 'required|numeric',
            'rd_account_interest' => 'required|numeric',
            'rd_account_month' => 'required|numeric',
            'rd_account_maturity_date' => 'required',
            'membership_no' => 'required|numeric'
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        $rd_account_no = $post->rd_account_no;


        $this->rddeleteInstallments($rd_account_no);

        $maturity_date = date('Y-m-d', strtotime($post->rd_account_maturity_date));
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->rd_opening_date))) ;

        if (!$result) {
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Please Check your session"],400);
        }

        $date = date('Y-m-d', strtotime($post->rd_opening_date));

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if($session_master->auditPerformed === 'Yes'){
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        if($start_date >= $date &&  $date <= $end_date){
            $openingdatee = $date;
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $openingdatee = $date;

        $opening_accounts = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*','schmeaster.id as sch_id','schmeaster.scheme_code',
                'ledger_masters.reference_id','ledger_masters.ledgerCode','ledger_masters.groupCode',
                'refSchemeMaster.scheme_code as ref_scheme_code','refSchemeMaster.months'
            )
            ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
            ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
            ->where('opening_accounts.accountNo', $post->rd_account_no)
            ->where('accountname','RD')
            ->where('opening_accounts.status','Active')
            ->first();

        if($opening_accounts->transactionDate > $openingdatee){
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Check Account Open Date"],400);
        }

        if($opening_accounts->groupCode && $opening_accounts->ledgerCode){
            $rd_group = $opening_accounts->groupCode;
            $rd_ledger = $opening_accounts->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Rd Group Code & Ledger Code"],400);
        }


        $rand = "RD" . time();
        $preAccount = DB::table('re_curring_rds')
            ->where('rd_account_no',$post->rd_account_no)
            ->where('memberType',$post->member_type)
            ->first();

        if($preAccount){
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {

            DB::beginTransaction();
            try{
                $rdaccount = new ReCurringRd;
                $rdaccount->serialNo = $rand;
                $rdaccount->secheme_id = $opening_accounts->schemetype;
                $rdaccount->memberType = $post->member_type;
                $rdaccount->accountId = $opening_accounts->accountNo;
                $rdaccount->accountNo = $post->rd_account_no;
                $rdaccount->rd_account_no = $post->rd_account_no;
                $rdaccount->amount = $post->rd_account_amount;
                $rdaccount->month = $post->rd_account_month;
                $rdaccount->date = $openingdatee;
                $rdaccount->paid_interest = !empty($post->rd_account_paid_interest) ? $post->rd_account_paid_interest : 0;
                $rdaccount->ledger_folio_no = $post->rd_account_lf_no;
                $rdaccount->misid = 0;
                $rdaccount->matureserialNo = null;
                $rdaccount->interest = $post->rd_account_interest;
                $rdaccount->maturity_date = $maturity_date;
                $rdaccount->groupCode = $rd_group;
                $rdaccount->ledgerCode = $rd_ledger;
                $rdaccount->actual_maturity_date = null;
                $rdaccount->actual_maturity_amount = null;
                $rdaccount->branchId = session('branchId') ? session('branchId') : 1;
                $rdaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $rdaccount->agentId = $post->rd_account_agent;
                $rdaccount->updatedBy = $post->user()->id;
                $rdaccount->status = 'Active';
                $rdaccount->save();

                // Store Installments
                $interest = $post->rd_account_interest;
                $amount = $post->rd_account_amount;
                $months = $post->rd_account_month;
                $startDate = new DateTime($openingdatee);
                $branchid = "1";
                for ($i = 1; $i <= $months; $i++) {
                    $randinstall = "RdI" . time();
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $installmentsdata = new RdInstallment;
                    $installmentsdata->serialNo = $randinstall;
                    $installmentsdata->rd_id = $rdaccount->id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = session('branchId') ? session('branchId') : 1;
                    $installmentsdata->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $installmentsdata->updatedBy = $post->user()->id;
                    $installmentsdata->save();
                }

                DB::commit();


                $deposit_amount = '';
                if(is_null($rdaccount)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('rd_receiptdetails')
                        ->select(
                            DB::raw('SUM(rd_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(rd_receiptdetails.panelty) as penality')
                        )
                        ->where('rd_account_no',$rdaccount->accountId)
                        ->where('rc_account_no',$rdaccount->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'rdaccount' => $rdaccount,
                    'deposit_amount' => $deposit_amount,
                    'opening_accounts'=> $opening_accounts,
                    'messages' => 'Data Updated Successfully !!'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }

        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Account No Not Found !!']);
        }
    }

    public function rddeleteInstallments($rd_account_no){
        $rd_account = DB::table('re_curring_rds')->where('rd_account_no', $rd_account_no)->where('status', 'Active')->first();

        if (is_null($rd_account)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {
            DB::beginTransaction();
            try{
                DB::table('rd_installments')->where('rd_id', $rd_account->id)->delete();
                DB::table('re_curring_rds')->where('rd_account_no', $rd_account_no)->where('status', 'Active')->delete();
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'messages' => 'Record Deleted Successfully'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }






}
