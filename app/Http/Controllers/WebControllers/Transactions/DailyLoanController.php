<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgentMaster;
use App\Models\MemberAccount;
use App\Models\GeneralLedger;
use App\Models\TransferedAccount;
use App\Models\LedgerMaster;
use App\Models\MemberSaving; 
use App\Models\DailyLoanDeposits;
use App\Models\DailyLoanInstallments; 
use App\Models\DailyLoanReceiptdetails;   
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\SessionMaster;
use App\Models\GroupMaster;
use App\Models\opening_accounts;

class DailyLoanController extends Controller
{
    public function index(){
        $groups = GroupMaster::whereIn('groupCode',['C002','BANK001'])->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $data['groups'] = $groups;
        $data['agents'] = $agents;
        return view('transaction.dailyloan', $data);
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

    public function GetDailyLoanAccountList(Request $post){
        $account_no = $post->account_no;
        $memberType = $post->memberType;
        if(!empty($account_no)){
            $account_nos = DB::table('opening_accounts')
                ->where('accountNo','LIKE',$account_no.'%')
                ->where('accountname','=','Daily Loan')
                ->where('membertype','=',$memberType)
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
    public function getdailyloandetails(Request $post){
        //_________Checked Account in Opening Account Table
        $account_no = $post->selectdId;

        $opening_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'member_accounts.accountNo as membership',
                'member_accounts.name as customer_name',
                'scheme_masters.id as schid',
                'scheme_masters.days as schdays',
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.accountNo', $account_no)
            ->where('opening_accounts.accountname','=','Daily Loan')
            ->first();


        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if(!empty($session_master)){

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('dailyloan_receiptdetails')
                ->where('rc_account_no',$opening_account->accountNo)
                ->whereDate('payment_date','<',$session_master->startDate)
                ->get();


            //_________Get Current Year Entries
            $dailyloan_accounts = DB::table('dailyloandeposits')
                ->select('dailyloandeposits.*','agent_masters.id as agentid','agent_masters.name as agentname')
                ->leftJoin('agent_masters','agent_masters.id','dailyloandeposits.agentid')
                ->where('dailyloandeposits.dailyloan_account_no',$opening_account->accountNo)
                ->where('dailyloandeposits.accountNo',$opening_account->membershipno)
                ->orderBy('date','ASC')
                ->first();

            $deposit_amount = '';
            if(is_null($dailyloan_accounts)){
                $deposit_amount = '';
            }else{  
                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                    ->select(
                        DB::raw('SUM(CASE WHEN dailyloan_receiptdetails.amount IS NOT NULL THEN dailyloan_receiptdetails.amount ELSE 0 END) as deposit'),
                        DB::raw('SUM(CASE WHEN dailyloan_receiptdetails.panelty IS NOT NULL THEN dailyloan_receiptdetails.panelty ELSE 0 END) as penalty')
                    )
                    ->where([
                        ['dailyloan_account_no', '=', $dailyloan_accounts->accountId],
                        ['rc_account_no', '=', $dailyloan_accounts->id]
                    ])
                    ->get(); 
            } 
            if($previous_balance || $dailyloan_accounts || !is_null($opening_account) || !is_null($deposit_amount)){
                return response()->json([
                    'status' => 'success',
                    'previous_balance' => $previous_balance,
                    'dailyloan_accounts' => $dailyloan_accounts,
                    'opening_account' => $opening_account,
                    'deposit_amount' => $deposit_amount
                ]);
            }else{
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Recodailyloan Not Found'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Check Your Session'
            ]);
        }
    }

    public function dailyloaninsert(Request $post){

        $rules = array(
            'dailyloanopening_date' => 'required',
            'member_type' => 'required',
            'dailyloan_account_no' => 'required|numeric',
            'dailyloanaccount_amount' => 'required|numeric',
            'dailyloanaccount_interest' => 'required|numeric',
            'dailyloanaccount_days' => 'required|numeric',
            'dailyloanaccount_maturity_date' => 'required',
            'membership_no' => 'required|numeric'
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }


        $openingdatee = date('Y-m-d', strtotime($post->dailyloanopening_date));
        $maturity_date = date('Y-m-d', strtotime($post->dailyloan_account_maturity_date));
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->dailyloanopening_date))) ;

        if (!$result) {
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Please Check your session"],200);
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
            ->where('opening_accounts.accountNo', $post->dailyloan_account_no)
            ->where('accountname','Daily Loan')
            ->where('opening_accounts.status','Active')
            ->first();

        if($opening_accounts->transactionDate > $openingdatee){
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Check Account Open Date"],200);
        }


        if($opening_accounts->groupCode && $opening_accounts->ledgerCode){
            $scheme_group_code = $opening_accounts->groupCode;
            $scheme_ledger_code = $opening_accounts->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Group Code And Ledger Code Not Found"],200);
        }


        $rand = "DDL" . time();
        $preAccount = DB::table('dailyloandeposits')
            ->where('dailyloan_account_no',$post->dailyloan_account_no)
            ->where('memberType',$post->member_type)
            ->first();

        if($preAccount){
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {

            DB::beginTransaction();
            try{
                $dailyloanaccount = new DailyLoanDeposits;
                $dailyloanaccount->serialNo = $rand;
                $dailyloanaccount->secheme_id = $opening_accounts->schemetype;
                $dailyloanaccount->memberType = $post->member_type;
                $dailyloanaccount->accountId = $opening_accounts->membershipno;
                $dailyloanaccount->accountNo = $opening_accounts->membershipno;
                $dailyloanaccount->dailyloan_account_no = $post->dailyloan_account_no;
                $dailyloanaccount->amount = $post->dailyloanaccount_amount;
                $dailyloanaccount->month = $post->dailyloanaccount_days;
                $dailyloanaccount->date = $openingdatee;
                $dailyloanaccount->paid_interest = !empty($post->dailyloan_account_paid_interest) ? $post->dailyloan_account_paid_interest : 0;
                $dailyloanaccount->ledger_folio_no = $post->dailyloan_account_lf_no;
                $dailyloanaccount->misid = 0;
                $dailyloanaccount->matureserialNo = $rand;
                $dailyloanaccount->interest = $post->dailyloanaccount_interest_label;
                $dailyloanaccount->maturity_date = $maturity_date;
                $dailyloanaccount->groupCode = $scheme_group_code;
                $dailyloanaccount->ledgerCode = $scheme_ledger_code;
                $dailyloanaccount->actual_maturity_date = $maturity_date;
                $dailyloanaccount->actual_maturity_amount = $post->dailyloan_account_maturity_amount;
                $dailyloanaccount->branchId = session('branchId') ? session('branchId') : 1;
                $dailyloanaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $dailyloanaccount->agentId = $post->dailyloan_account_agent;
                $dailyloanaccount->updatedBy = $post->user()->id;
                $dailyloanaccount->status = 'Active';
                $dailyloanaccount->save();

                // Store Installments
                $interest = $post->dailyloanaccount_interest_label;
                $amount = $post->dailyloanaccount_amount;
                $months = $post->dailyloanaccount_days;
                $startDate = new DateTime($openingdatee);
                $branchid = "1";
                for ($i = 1; $i <= $months; $i++) {
                    $randinstall = "RdI" . time();
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $installmentsdata = new DailyLoanInstallments;
                    $installmentsdata->serialNo = $randinstall;
                    $installmentsdata->dailyloan_id = $dailyloanaccount->id;
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
                if(is_null($dailyloanaccount)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                        ->select(
                            DB::raw('SUM(CASE WHEN dailyloan_receiptdetails.amount IS NOT NULL THEN dailyloan_receiptdetails.amount ELSE 0 END) as deposit'),
                            DB::raw('SUM(dailyloan_receiptdetails.panelty IS NOT NULL THEN dailyloan_receiptdetails.panelty ELSE 0 END) as penality')
                        )
                        ->where('dailyloan_account_no',$dailyloanaccount->accountId)
                        ->where('rc_account_no',$dailyloanaccount->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'dailyloanaccount' => $dailyloanaccount,
                    'deposit_amount' => $deposit_amount,
                    'opening_accounts'=> $opening_accounts,
                    'messages' => 'Data Store Successfully !!'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }

        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Account No Not Found !!']);
        }
    }

    public function dailyreceiveamount(Request $post){
        $rules = array(
            'deposit_opening_date' => 'required',
            'dailyloan_account' => 'required',
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

        $account = $post->dailyloan_account;
        $dailyloan_account = DB::table('dailyloandeposits')
            ->select('dailyloandeposits.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
            ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
            ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
            ->where('accountId',$account)
            ->first();

        //___________Check Account Group Or Ledger Code
        if($dailyloan_account->groupCode && $dailyloan_account->ledgerCode){
            $account_group_code = $dailyloan_account->groupCode;
            $account_ledger_code = $dailyloan_account->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Group Code And Ledger Code Not Found"],400);
        }


        //_________Get Penaily Code
        $scheme_plty_group_legder_code = DB::table('dailyloandeposits')
            ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
            ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
            ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
            ->where('ledger_masters.groupCode','INCM001')
            ->where('accountId',$account)
            ->first();

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


        $installments = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_account->id])->orderBy('id', 'desc')->first();
        $paid_amount = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_account->id])->sum('paid_amount');

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
                $dailyloan_ids_details = DailyLoanDeposits::where(['accountId' => $account])->first();
                do {
                    $generalLedgers = "Rd" . time();
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);

                  DB::beginTransaction();
                 try {

                        //_______________RD Receipt
                        $lastInsertedId = DB::table('dailyloan_receiptdetails')->insertGetId([
                                "rc_account_no" => $dailyloan_ids_details->id,
                                "dailyloan_account_no"  =>  $dailyloan_ids_details->accountId,
                                "amount" => $post->deposit_amount,
                                "serialNo" => $generalLedgers,
                                "payment_date"  => $installmentdate,
                                "installment_date"  => $installmentdate,
                                'groupCode' => $post->payment_type,
                                'ledgerCode' => $post->payment_bank,
                                'memberType' => $dailyloan_ids_details->memberType,
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
                            $query = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_account->id, 'payment_status' => 'pending'])->first();

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
                        $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                        $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                        $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                        $genral_ledger->groupCode = $account_group_code;
                        $genral_ledger->ledgerCode = $account_ledger_code;
                        $genral_ledger->formName = "DailyDepositLoan";
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
                        $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                        $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                        $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                        $genral_ledger->formName = "DailyDepositLoan";
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
                            $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                            $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                            $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                            $genral_ledger->groupCode = $penality_group;
                            $genral_ledger->ledgerCode = $penality_ledger;
                            $genral_ledger->formName = "DailyLoan-Penalty";
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
                            $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                            $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                            $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                            $genral_ledger->groupCode = $post->payment_type;
                            $genral_ledger->ledgerCode = $post->payment_bank;
                            $genral_ledger->formName = "DailyLoan-Penalty";
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

                        $changestatus = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_ids_details->id])->orderBY('intallment_no', 'desc')->first();
                        if ($changestatus->payment_status == "paid") {
                            $moodifystatus = DailyLoanDeposits::where(['id' => $dailyloan_ids_details->id])->update(['status' => 'Active']);
                        }

                        if ($paymentSuccess) {
                            DB::commit();

                            $total = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_ids_details->id])->sum('paid_amount');
                            $totalpanality = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_ids_details->id])->sum('panelty');
                            $grand_total = $total + $totalpanality;

                            $deposit_amount = '';
                            if(is_null($dailyloan_account)){
                                $deposit_amount = '';
                            }else{
                                $deposit_amount = DB::table('dailyloan_receiptdetails')
                                    ->select(
                                        DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                                        DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                                    )
                                    ->where('dailyloan_account_no',$dailyloan_account->accountId)
                                    ->where('rc_account_no',$dailyloan_account->id)
                                    ->get();
                            }

                            return response()->json([
                                'status' => 'success',
                                'total' => $total,
                                'payment_id' => $dailyloan_ids_details->id,
                                'panality' => $totalpanality,
                                'dailyloan_account' => $dailyloan_account,
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
        $dailyloanaccountnumber = $post->dailyloanaccountnumber;
        $dailyloan_accounts = DB::table('dailyloandeposits')
            ->where('dailyloan_account_no',$dailyloanaccountnumber)
            ->first();

        $received_amount = DB::table('dailyloan_receiptdetails')
            ->select(
                'dailyloan_receiptdetails.*','dailyloandeposits.id as dailyloan_id','dailyloandeposits.amount as dailyloan_amount','dailyloandeposits.month as dailyloan_month'
                )
            ->join('dailyloandeposits','dailyloandeposits.id','=','dailyloan_receiptdetails.rc_account_no')
            ->where('dailyloan_receiptdetails.dailyloan_account_no', $dailyloan_accounts->accountId)
            ->orderBy('installment_date','ASC')
            ->get();
        // dd($received_amount);

        $installments = DB::table('dailyloan_installments')
            ->where('dailyloan_id',$dailyloan_accounts->id)
            ->where('payment_status','paid')
            ->orderBy('id', 'desc')
            ->get();


        if(!empty($received_amount) || !empty($dailyloan_accounts)){
            return response()->json([
                'status' => 'success',
                'received_amount' => $received_amount,
                'installments' => $installments,
                'dailyloan_accounts' => $dailyloan_accounts
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Recodailyloan Not Found'
            ]);
        }
    }

    public function ViewInstallmentsDetails(Request $post){
        $id = $post->accountId;
        $dailyloan_accounts = DB::table('dailyloandeposits')
            ->where('id',$id)
            ->first();


        $installments = DB::table('dailyloan_installments')
            ->where('dailyloan_id',$dailyloan_accounts->id)
            ->orderBy('intallment_no', 'ASC')
            ->get();
            // dd($dailyloan_accounts);



        if(!empty($installments)){
            return response()->json([
                'status' => 'success',
                'installments' => $installments,
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'messages' => 'Recodailyloan Not Found'
            ]);
        }
    }

    public function DeleteInstallments(Request $post){
        $id = $post->id;

        // Retrieve receipt details
        $receipt_id = DB::table('dailyloan_receiptdetails')->where('id',$id)->first();

        if (!$receipt_id) {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Recodailyloan Not Found'
            ]);
        }
        DB::beginTransaction();

        try {

            // Retrieve RD account details if receipt exists
            $dailyloan_account = DB::table('dailyloandeposits')
                ->select('dailyloandeposits.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'dailyloandeposits.secheme_id')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('accountId', $receipt_id->dailyloan_account_no)
                ->first();

            // Delete general ledger recodailyloans associated with this receipt
            DB::table('general_ledgers')
                ->where('serialNo', $receipt_id->serialNo)
                ->where('referenceNo', $receipt_id->id)
                ->delete();

            // Update installments if any exist for this receipt
            $installmentsUpdated = DB::table('dailyloan_installments')
                ->where('recpt_id', $receipt_id->id)
                ->exists();

            if ($installmentsUpdated) {
                DB::table('dailyloan_installments')
                    ->where('recpt_id', $receipt_id->id)
                    ->update([
                        'serialNo' => $dailyloan_account->serialNo,
                        'payment_status' => 'pending',
                        'paid_amount' => 0,
                        'panelty' => 0,
                        'payment_date' => null,
                        'recpt_id' => null
                    ]);
            }

            // Delete the receipt detail entry
            DB::table('dailyloan_receiptdetails')->where('id', $id)->delete();

            // Mark the RD account as Active if linked
            if ($dailyloan_account) {
                DB::table('dailyloandeposits')
                    ->where('id', $receipt_id->rc_account_no)
                    ->update(['status' => 'Active']);
            }

            DB::commit();

            // Retrieve updated deposit and penalty amounts, if available
            $deposit_amount = DB::table('dailyloan_receiptdetails')
                ->select(
                    DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                    DB::raw('SUM(dailyloan_receiptdetails.panelty) as penalty')
                )
                ->where('dailyloan_account_no', $dailyloan_account->accountId ?? 0)
                ->where('rc_account_no', $dailyloan_account->id ?? 0)
                ->first();

            return response()->json([
                'status' => 'success',
                'dailyloan_account' => $dailyloan_account,
                'deposit_amount' => $deposit_amount,
                'messages' => 'Recodailyloan Deleted Successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred while deleting the recodailyloan',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function dailyloanamountupdatereceive(Request $post){

        $validator = Validator::make($post->all(),[
            'deposit_opening_date' => 'required',
            'edit_dailyloan_account' => 'required',
            'dailyloanid' => 'required',
            'payment_type' => 'required',
            'payment_bank' => 'required',
            'deposit_amount' => 'required|numeric'
        ]);

        if($validator->passes()){
            $rowid = $post->dailyloanid;

            if ($rowid) {
                $check_dailyloan_no = DailyLoanReceiptdetails::where('id',$rowid)->first();
                // dd($check_dailyloan_no);
                $dailyloan_account = DB::table('dailyloandeposits')
                    ->select('dailyloandeposits.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
                    ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                    ->where('accountId',$check_dailyloan_no->dailyloan_account_no)
                    ->first();

                if ($check_dailyloan_no) {
                    $dailyloan_ids_details = DB::table('dailyloandeposits')
                        ->select('dailyloandeposits.*','scheme_masters.id as sch_id')
                        ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'dailyloandeposits.secheme_id')
                        ->where('dailyloandeposits.id', $check_dailyloan_no->rc_account_no)
                        ->orderBy('dailyloandeposits.date', 'ASC')
                        ->first();

                        //_________Check Account Group Or Ledger
                        if($dailyloan_ids_details->groupCode && $dailyloan_ids_details->ledgerCode){
                            $account_group = $dailyloan_ids_details->groupCode;
                            $account_ledger = $dailyloan_ids_details->ledgerCode;
                        }else{
                            return response()->json(['status' => 'Fail','messages' => 'Account Group Code Or Ledger Code Not Found']);
                        }

                    //_________Get Penaily Code
                    $scheme_plty_group_legder_code = DB::table('dailyloandeposits')
                        ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                        ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
                        ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                        ->where('ledger_masters.groupCode','INCM001')
                        ->where('accountId',$dailyloan_ids_details->accountId)
                        ->first();

                        if($scheme_plty_group_legder_code->groupCode && $scheme_plty_group_legder_code->ledgerCode){
                            $penality_group = $scheme_plty_group_legder_code->groupCode;
                            $penality_ledger = $scheme_plty_group_legder_code->ledgerCode;
                        }else{
                            return response()->json(['status' => 'Fail','messages' => 'Penality Group Code Or Ledger Code Not Found']);
                        }


                    $ledger = GeneralLedger::where('serialNo',$check_dailyloan_no->serialNo)->where('is_delete','No')->get();


                    if(!$ledger){
                         return response()->json(['status' => 'fail', 'messages' => "Data Not found"]);
                    }

                    $last_amount = $check_dailyloan_no->amount;
                    $latest_amount_cr = $post->deposit_amount;
                    $transDate = date('Y-m-d', strtotime($post->deposit_opening_date));
                    $lastinstallmet = DailyLoanInstallments::where(['dailyloan_id' => $check_dailyloan_no->rc_account_no])->orderBy('id', 'desc')->first();

                    if ($last_amount != 0 && $latest_amount_cr % $lastinstallmet->amount === 0) {
                        DB::beginTransaction();
                        try {

                            DailyLoanInstallments::where('serialNo',$check_dailyloan_no->serialNo)->update([
                                'payment_status'=>'pending',
                                'paid_amount'=>0,
                                'payment_date'=>null,
                                'panelty'=>0,
                                'recpt_id' => null
                            ]);

                            $paidamount =  DailyLoanInstallments::where(['dailyloan_id' => $check_dailyloan_no->rc_account_no])->sum('paid_amount');
                            $totalRdAmount = $lastinstallmet->amount * $lastinstallmet->intallment_no;
                            $totaldueRd =  $totalRdAmount - $paidamount;

                            if ($post->deposit_amount <= $totaldueRd) {

                                $monthsToPay = $post->deposit_amount / $lastinstallmet->amount;

                                if ($monthsToPay <= 0 && $monthsToPay > $lastinstallmet->intallment_no) {
                                    return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given monthly payment.']);
                                }



                                //_______update dailyloan recived
                                $check_dailyloan_no->payment_date = $transDate;
                                $check_dailyloan_no->amount = $latest_amount_cr;
                                $check_dailyloan_no->panelty = $post->deposit_penalty ? $post->deposit_penalty : 0;
                                $check_dailyloan_no->matureserialNo =  $check_dailyloan_no->serialNo;
                                $check_dailyloan_no->save();

                                //______update installmets
                                for ($i = 1; $i <= $monthsToPay; $i++) {
                                    $pendinginstalment = DailyLoanInstallments::where([
                                        'dailyloan_id' => $check_dailyloan_no->rc_account_no,
                                        'payment_status' => 'pending'])->first();

                                    if ($pendinginstalment && $pendinginstalment->payment_status == "pending") {
                                        $pendinginstalment->panelty = empty($post->deposit_penalty) ? 0 : $post->deposit_penalty;
                                        $pendinginstalment->payment_date = $transDate;
                                        $pendinginstalment->paid_amount = $lastinstallmet->amount;
                                        $pendinginstalment->payment_status = "paid";
                                        $pendinginstalment->serialNo = $check_dailyloan_no->serialNo ;
                                        $pendinginstalment->recpt_id = $check_dailyloan_no->id;
                                        $pendinginstalment->save();
                                    }
                                }

                                //_______________Delete Gerenal ledger
                                GeneralLedger::where('serialNo',$check_dailyloan_no->serialNo)
                                    ->where('referenceNo',$check_dailyloan_no->id)
                                    ->where('is_delete','No')
                                    ->delete();



                            //__________________________Gerenal Ledger Rd Entry_____________________

                            //___________RD Amount Entry
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $check_dailyloan_no->serialNo;
                            $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                            $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                            $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                            $genral_ledger->groupCode = $account_group;
                            $genral_ledger->ledgerCode = $account_ledger;
                            $genral_ledger->formName = "DailyDepositLoan";
                            $genral_ledger->referenceNo = $check_dailyloan_no->id;
                            $genral_ledger->transactionDate = $transDate;
                            $genral_ledger->transactionType = "Cr";
                            $genral_ledger->transactionAmount = $check_dailyloan_no->amount;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            //____________Cash/Bank Entry
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $check_dailyloan_no->serialNo;
                            $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                            $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                            $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                            $genral_ledger->formName = "DailyDepositLoan";
                            $genral_ledger->groupCode = $post->payment_type;
                            $genral_ledger->ledgerCode = $post->payment_bank;
                            $genral_ledger->referenceNo = $check_dailyloan_no->id;
                            $genral_ledger->transactionDate = $transDate;
                            $genral_ledger->transactionType = "Dr";
                            $genral_ledger->transactionAmount = $check_dailyloan_no->amount;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();


                            //________________If penality Received on Rd

                            if (!empty($post->deposit_penalty) && $post->deposit_penalty != 0) {
                                $genral_ledger = new GeneralLedger;
                                $genral_ledger->serialNo = $check_dailyloan_no->serialNo;
                                $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                                $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                                $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                                $genral_ledger->groupCode = $penality_group;
                                $genral_ledger->ledgerCode = $penality_ledger;
                                $genral_ledger->formName = "DailyLoan-Penalty";
                                $genral_ledger->referenceNo = $check_dailyloan_no->id;
                                $genral_ledger->transactionDate = $transDate;
                                $genral_ledger->transactionType = "Cr";
                                $genral_ledger->transactionAmount = $post->deposit_penalty;
                                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                                $genral_ledger->agentId = $post->agent_id;
                                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                                $genral_ledger->updatedBy = $post->user()->id;
                                $genral_ledger->save();

                                $genral_ledger = new GeneralLedger;
                                $genral_ledger->serialNo = $check_dailyloan_no->serialNo;
                                $genral_ledger->accountId = $dailyloan_ids_details->accountId;
                                $genral_ledger->accountNo = $dailyloan_ids_details->dailyloan_account_no;
                                $genral_ledger->memberType = $dailyloan_ids_details->memberType;
                                $genral_ledger->groupCode = $post->payment_type;
                                $genral_ledger->ledgerCode = $post->payment_bank;
                                $genral_ledger->formName = "DailyLoan-Penalty";
                                $genral_ledger->referenceNo = $check_dailyloan_no->id;
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
                                if(is_null($check_dailyloan_no)){
                                    $deposit_amount = '';
                                }else{
                                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                                        ->select(
                                            DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                                            DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                                        )
                                        ->where('dailyloan_account_no',$dailyloan_account->accountId)
                                        ->where('rc_account_no',$dailyloan_account->id)
                                        ->get();
                                }

                                $total =  DailyLoanInstallments::where(['dailyloan_id' => $check_dailyloan_no->rc_account_no])->sum('paid_amount');
                                $panelty =  DailyLoanReceiptdetails::where(['rc_account_no' => $check_dailyloan_no->rc_account_no,'is_delete'=>"No"])->sum('panelty');
                                return response()->json([
                                    'status' => 'success',
                                    'messages' => 'Reciept Update SuccessFully !!',
                                    'total' => $total,
                                    'dailyloan_account' => $dailyloan_account,
                                    'deposit_amount' => $deposit_amount,
                                    'receipt' => $check_dailyloan_no->rc_account_no,
                                    'panelty'=>$panelty
                                ]);
                            }
                        }catch(\Exception $e){
                            DB::rollBack();
                            return response()->json([
                                'status' => 'fail',
                                'messages' => 'An error occurred while deleting the recodailyloan',
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }

    }

    public function getdailyloanmaturedata(Request $post){
        $accountNo = $post->accountNo;

        if ($accountNo) {

           $dailyloan_acount =  DB::table('dailyloandeposits')
                ->select('dailyloandeposits.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'dailyloandeposits.secheme_id')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('dailyloandeposits.dailyloan_account_no', $accountNo)
                ->orderBy('dailyloandeposits.date', 'ASC')
                ->first();

            $totalinstallmentpaid = DailyLoanInstallments::where(['dailyloan_id' => $dailyloan_acount->id])->sum('paid_amount');

            return response()->json(['status' => 'success', 'totalpaid' => $totalinstallmentpaid, 'details' => $dailyloan_acount]);
        } else {
            return response()->json(['status' => 'fail', 'messages' => "something went wrong"]);
        }
    }

    public function GetSavingAccountno(Request $post){
        $dailyloan_account = $post->dailyloan_account;
        $opening_account = DB::table('opening_accounts')
            ->where('opening_accounts.accountNo', $dailyloan_account)
            ->where('opening_accounts.accountname', 'Daily Loan')
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
                    'messages' => 'Recodailyloan Not Found'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Recodailyloan Not Found'
            ]);
        }
    }

    public function dailyloanmature(Request $post){
        $rules = array(
            'dailyloan_mature_date' => 'required',
            'account_id' => 'required|numeric',
            'dailyloan_mature_amount_receive' => 'required|numeric',
            'dailyloan_mature_actual_interest' => 'numeric',
            'dailyloan_mature_actual_penality_value' => 'numeric',
            'dailyloantotalnewamount' => 'numeric',
            'payment_type' => 'required',
            'ledgercodess' => 'required'
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
            ->where('opening_accounts.accountname','=','Daily Loan')
            ->first();

        $rand = "DDL" . time();
        $payment_type = $post->payment_type;

        if($payment_type === 'TRASFER'){
            DB::beginTransaction();

            try{

                //_________saving Account Number
                $accountNo = $post->saving;

                //____________Date Conversion
                $todaydate =  date('Y-m-d', strtotime($post->dailyloan_mature_date));

                //___________Get RD Account Number
                $dailyloan_account = DB::table('dailyloandeposits')
                    ->select('dailyloandeposits.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
                    ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                    ->where('accountId',$account_id)
                    ->first();

                    if($dailyloan_account->groupCode && $dailyloan_account->ledgerCode){
                        $dailyloan_group = $dailyloan_account->groupCode;
                        $dailyloan_ledger = $dailyloan_account->ledgerCode;
                    }else{
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Daily Loan Group Code Or Ledger Code Not Found'
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
                $scheme_plty_group_legder_code = DB::table('dailyloandeposits')
                    ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
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
                $scheme_interest_group_legder_code = DB::table('dailyloandeposits')
                    ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
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
                $dailyloan_amount = $post->dailyloan_mature_amount_receive ?? 0;
                $interest_amount = $post->dailyloan_mature_actual_interest ?? 0;
                $penality_amount = $post->dailyloan_mature_actual_penality_value ?? 0;

                $mature_amount = (($dailyloan_amount + $interest_amount) - $penality_amount);


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
                $savingacc->chequeNo = 'trfdFromDailyLoan';
                $savingacc->transactionType = 'Deposit';
                $savingacc->depositAmount = $mature_amount;
                $savingacc->withdrawAmount = 0;
                $savingacc->paymentType = null;
                $savingacc->bank = null;
                $savingacc->narration = "Amount Transfer From Daily Loan account ".$post->mature_account_no;
                $savingacc->branchId = session('branchId') ? session('branchId') : 1;
                $savingacc->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $savingacc->agentId = $saving_account->agentId;
                $savingacc->updatedBy = $post->user()->id;
                $savingacc->is_delete = 'No';
                $savingacc->save();



                //_________Rd Gerenal Ledger Entry
                $genral_ledger = new GeneralLedger;
                $genral_ledger->serialNo = $rand;
                $genral_ledger->accountId = $dailyloan_account->accountId;
                $genral_ledger->accountNo = $dailyloan_account->dailyloan_account_no;
                $genral_ledger->memberType = $dailyloan_account->memberType;
                $genral_ledger->groupCode = $dailyloan_group;
                $genral_ledger->ledgerCode = $dailyloan_ledger;
                $genral_ledger->formName = "DailyLoan-Mature";
                $genral_ledger->referenceNo = $dailyloan_account->id;
                $genral_ledger->transactionDate = $todaydate;
                $genral_ledger->transactionType = "Dr";
                $genral_ledger->transactionAmount = $dailyloan_amount;
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
                $genral_ledger->formName = "DailyDepositLoan";
                $genral_ledger->referenceNo = $dailyloan_account->id;
                $genral_ledger->transactionDate = $todaydate;
                $genral_ledger->transactionType = "Cr";
                $genral_ledger->transactionAmount = $mature_amount;
                $genral_ledger->narration = "Amount Transfer From Daily Loan Acc ".$dailyloan_account->dailyloan_account_no;
                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                $genral_ledger->agentId = $post->agent_id;
                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                $genral_ledger->updatedBy = $post->user()->id;
                $genral_ledger->save();



                //___________if Interest Amount is Greater Then 0
                if($interest_amount && $interest_amount > 0){
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $dailyloan_account->accountId;
                    $genral_ledger->accountNo = $dailyloan_account->accountNo;
                    $genral_ledger->memberType = $dailyloan_account->memberType;
                    $genral_ledger->groupCode = $interest_group;
                    $genral_ledger->ledgerCode = $interest_ledger;
                    $genral_ledger->formName = "DailyLoan-Interest Paid";
                    $genral_ledger->referenceNo = $dailyloan_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Dr";
                    $genral_ledger->transactionAmount = $interest_amount;
                    $genral_ledger->narration = $scheme_interest_group_legder_code->name.'-'.$dailyloan_account->dailyloan_account_no;
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
                    $genral_ledger->accountId = $dailyloan_account->accountId;
                    $genral_ledger->accountNo = $dailyloan_account->accountNo;
                    $genral_ledger->memberType = $dailyloan_account->memberType;
                    $genral_ledger->groupCode = $penality_group;
                    $genral_ledger->ledgerCode = $penality_ledger;
                    $genral_ledger->formName = "DailyLoan-Penality Rec.";
                    $genral_ledger->referenceNo = $dailyloan_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Cr";
                    $genral_ledger->transactionAmount = $penality_amount;
                    $genral_ledger->narration = $scheme_plty_group_legder_code->name.'-'.$dailyloan_account->dailyloan_account_no;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();
                }

                //_________Check Maturity Date Status
                $maturity_date = new DateTime($dailyloan_account->maturity_date);
                $todaydate = new DateTime();
                $status = '';

                //___________If Today Date Greater Then Maturity
                if($todaydate > $maturity_date) {
                    $status = 'Mature';
                } else {
                    $status = 'PreMature';
                }

                $dailyloan_account = DailyLoanDeposits::where('accountId', $account_id)->first();

                //__________Account Open Table Update Status
                $account_open = opening_accounts::where('accountNo',$dailyloan_account->accountId)->where('accountname','Daily Loan')->first();
                $account_open->status = 'Closed';
                $account_open->save();

                //___________Rd Recurring Table Fileds Update
                $dailyloan_account->actual_maturity_date = $todaydate;
                $dailyloan_account->maturity_amount = $mature_amount;
                $dailyloan_account->matureserialNo = $rand;
                $dailyloan_account->dailyloan_type = "SAVING";
                $dailyloan_account->status = $status;
                $dailyloan_account->save();


                $deposit_amount = '';
                if(is_null($dailyloan_account)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                        ->select(
                            DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                        )
                        ->where('dailyloan_account_no',$dailyloan_account->accountId)
                        ->where('rc_account_no',$dailyloan_account->id)
                        ->get();
                }


                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'messages' => 'Maturity Amount paid successfully !!',
                    'dailyloan_account' => $dailyloan_account,
                    'opening_account' => $opening_account,
                    'deposit_amount' => $deposit_amount
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }
        }else{
            DB::beginTransaction();

            try{
                //____________Date Conversion
                $todaydate =  date('Y-m-d', strtotime($post->dailyloan_mature_date));

                //___________Get RD Account Number
                $dailyloan_account = DB::table('dailyloandeposits')
                    ->select('dailyloandeposits.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
                    ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                    ->where('accountId',$account_id)
                    ->first();

                    if($dailyloan_account->groupCode && $dailyloan_account->ledgerCode){
                        $dailyloan_groups = $dailyloan_account->groupCode;
                        $dailyloan_legders = $dailyloan_account->ledgerCode;
                    }else{
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Rd Account Group && Ledger Not Found'
                        ]);
                    }


                //_________Get Penaily Code
                $scheme_plty_group_legder_code = DB::table('dailyloandeposits')
                    ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
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
                $scheme_interest_group_legder_code = DB::table('dailyloandeposits')
                    ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                    ->leftJoin('scheme_masters','scheme_masters.id','=','dailyloandeposits.secheme_id')
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
                $dailyloan_amount = $post->dailyloan_mature_amount_receive ?? 0;
                $interest_amount = $post->dailyloan_mature_actual_interest ?? 0;
                $penality_amount = $post->dailyloan_mature_actual_penality_value ?? 0;

                $mature_amount = (($dailyloan_amount + $interest_amount) - $penality_amount);


                //_________Rd Gerenal Ledger Entry
                $genral_ledger = new GeneralLedger;
                $genral_ledger->serialNo = $rand;
                $genral_ledger->accountId = $dailyloan_account->accountId;
                $genral_ledger->accountNo = $dailyloan_account->dailyloan_account_no;
                $genral_ledger->memberType = $dailyloan_account->memberType;
                $genral_ledger->groupCode = $dailyloan_groups;
                $genral_ledger->ledgerCode = $dailyloan_legders;
                $genral_ledger->formName = "DailyLoan-Mature";
                $genral_ledger->referenceNo = $dailyloan_account->id;
                $genral_ledger->transactionDate = $todaydate;
                $genral_ledger->transactionType = "Dr";
                $genral_ledger->transactionAmount = $dailyloan_amount;
                $genral_ledger->narration = 'To Amt. Paid'.$dailyloan_account->dailyloan_account_no;
                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                $genral_ledger->agentId = $post->agent_id;
                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                $genral_ledger->updatedBy = $post->user()->id;
                $genral_ledger->save();

                //_________Cash Gerenal Ledger Entry
                $genral_ledger = new GeneralLedger;
                $genral_ledger->serialNo = $rand;
                $genral_ledger->accountId = $dailyloan_account->accountId;
                $genral_ledger->accountNo = $dailyloan_account->dailyloan_account_no;
                $genral_ledger->memberType = $dailyloan_account->memberType;
                $genral_ledger->groupCode = $post->payment_type;
                $genral_ledger->ledgerCode = $post->ledgercodess;
                $genral_ledger->formName = "DailyLoan-Saving";
                $genral_ledger->referenceNo = $dailyloan_account->id;
                $genral_ledger->transactionDate = $todaydate;
                $genral_ledger->transactionType = "Cr";
                $genral_ledger->transactionAmount = $mature_amount;
                $genral_ledger->narration = 'To Amt. Paid'.$dailyloan_account->dailyloan_account_no;
                $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                $genral_ledger->agentId = $post->agent_id;
                $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                $genral_ledger->updatedBy = $post->user()->id;
                $genral_ledger->save();



                //___________if Interest Amount is Greater Then 0
                if($interest_amount && $interest_amount > 0){
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $rand;
                    $genral_ledger->accountId = $dailyloan_account->accountId;
                    $genral_ledger->accountNo = $dailyloan_account->accountNo;
                    $genral_ledger->memberType = $dailyloan_account->memberType;
                    $genral_ledger->groupCode = $interest_groups;
                    $genral_ledger->ledgerCode = $interest_ledgers;
                    $genral_ledger->formName = "DailyLoan-Interest Paid";
                    $genral_ledger->referenceNo = $dailyloan_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Dr";
                    $genral_ledger->transactionAmount = $interest_amount;
                    $genral_ledger->narration = $scheme_interest_group_legder_code->name.'-'.$dailyloan_account->dailyloan_account_no;
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
                    $genral_ledger->accountId = $dailyloan_account->accountId;
                    $genral_ledger->accountNo = $dailyloan_account->accountNo;
                    $genral_ledger->memberType = $dailyloan_account->memberType;
                    $genral_ledger->groupCode = $penality_groups;
                    $genral_ledger->ledgerCode = $penality_ledgers;
                    $genral_ledger->formName = "DailyLoan-Penality Rec.";
                    $genral_ledger->referenceNo = $dailyloan_account->id;
                    $genral_ledger->transactionDate = $todaydate;
                    $genral_ledger->transactionType = "Cr";
                    $genral_ledger->transactionAmount = $penality_amount;
                    $genral_ledger->narration = $scheme_plty_group_legder_code->name.'-'.$dailyloan_account->dailyloan_account_no;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agent_id;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();
                }

                //_________Check Maturity Date Status
                $maturity_date = new DateTime($dailyloan_account->maturity_date);
                $todaydate = new DateTime();
                $status = '';

                //___________If Today Date Greater Then Maturity
                if($todaydate > $maturity_date) {
                    $status = 'Mature';
                } else {
                    $status = 'PreMature';
                }

                $dailyloan_account = DailyLoanDeposits::where('accountId', $account_id)->first();

                //__________Account Open Table Update Status
                $account_open = opening_accounts::where('accountNo',$dailyloan_account->accountId)->where('accountname','Daily Loan')->first();
                $account_open->status = 'Closed';
                $account_open->save();

                //___________Rd Recurring Table Fileds Update
                $dailyloan_account->actual_maturity_date = $todaydate;
                $dailyloan_account->maturity_amount = $mature_amount;
                $dailyloan_account->matureserialNo = $rand;
                $dailyloan_account->status = $status;
                $dailyloan_account->save();

                DB::commit();


                $deposit_amount = '';
                if(is_null($dailyloan_account)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                        ->select(
                            DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                        )
                        ->where('dailyloan_account_no',$dailyloan_account->accountId)
                        ->where('rc_account_no',$dailyloan_account->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'messages' => 'Maturity Amount paid successfully !!',
                    'dailyloan_account' => $dailyloan_account,
                    'opening_account' => $opening_account,
                    'deposit_amount' => $deposit_amount
                ]);


            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function dailyloanunmature(Request $post){

        //_____Rd Account Number
        $account_id = $post->accountNo;
        $opening_account = DB::table('opening_accounts')
            ->select('opening_accounts.*','member_accounts.accountNo as membership','member_accounts.name as customer_name')
            ->leftJoin('member_accounts','member_accounts.accountNo','=','opening_accounts.membershipno')
            ->where('opening_accounts.accountNo',$account_id)
            ->where('opening_accounts.accountname','=','Daily Loan')
            ->first();


        DB::beginTransaction();

        try{

            $rand = "DDL" . time();
            //__________Account Open Table Update Status
            $account_open = opening_accounts::where('accountNo',$account_id)
                ->where('accountname','Daily Loan')
                ->update(['status' => 'Active']);


            //__________RD Ruccring Table Table Update Status
            $dailyloan = DailyLoanDeposits::where('accountId',$account_id)->first();

            //_____________Gerenal Ledger Delete Entry
            $gerenal_ledger = DB::table('general_ledgers')
                ->where('serialNo',$dailyloan->matureserialNo)
                ->delete();


            //_____________Saving account Delete Entry
            $saving_account = DB::table('member_savings')
                ->where('serialNo',$dailyloan->matureserialNo)
                ->delete();


            //______After All Updatation then Update Rd Table
            $dailyloan->actual_maturity_date = null;
            $dailyloan->maturity_amount = 0;
            $dailyloan->matureserialNo = $rand;
            $dailyloan->dailyloan_type = "SAVING";
            $dailyloan->status = 'Active';
            $dailyloan->save();

            DB::commit();

            $deposit_amount = '';
            if(is_null($dailyloan)){
                $deposit_amount = '';
            }else{
                $deposit_amount = DB::table('dailyloan_receiptdetails')
                    ->select(
                        DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                        DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                    )
                    ->where('dailyloan_account_no',$dailyloan->accountId)
                    ->where('rc_account_no',$dailyloan->id)
                    ->get();
            }

            return response()->json([
                'status' => 'success',
                'messages' => 'Recodailyloan Updated Successfully',
                'dailyloan_account' => $dailyloan,
                'opening_account' => $opening_account,
                'deposit_amount' => $deposit_amount
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred while deleting the recodailyloan',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deletedailyloan(Request $post){

        $dailyloanid = $post->id;
        $dailyloan_account = DB::table('dailyloandeposits')->where('id', $dailyloanid)->where('status', 'Active')->first();

        if (is_null($dailyloan_account)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Recodailyloan Not Found']);
        } else {
            DB::beginTransaction();
            try{
                DB::table('dailyloan_installments')->where('dailyloan_id', $dailyloan_account->id)->delete();

                DB::table('dailyloandeposits')->where('id', $dailyloanid)->delete();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'messages' => 'Recodailyloan Deleted Successfully'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function dailyloanmodify(Request $post){
        $dailyloanid = $post->id;
        $dailyloan_account = DB::table('dailyloandeposits')->where('id', $dailyloanid)->where('status', 'Active')->first();

        if(is_null($dailyloan_account)){
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Recodailyloan Not Found'
            ]);
        }else{
            return response()->json([
                'status' => 'success',
                'dailyloan_account' => $dailyloan_account
            ]);
        }
    }

    public function dailyloanupdate(Request $post){

        $rules = array(
            'dailyloanopening_date' => 'required',
            'member_type' => 'required',
            'dailyloan_account_no' => 'required|numeric',
            'dailyloanaccount_amount' => 'required|numeric',
            'dailyloanaccount_interest_label' => 'required|numeric',
            'dailyloanaccount_days' => 'required|numeric',
            'dailyloan_account_maturity_date' => 'required',
            'membership_no' => 'required|numeric'
        );

        $validator = Validator::make($post->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        $dailyloan_account_no = $post->dailyloan_account_no;


        $this->dailyloandeleteInstallments($dailyloan_account_no);

        $openingdatee = date('Y-m-d', strtotime($post->dailyloanopening_date));
        $maturity_date = date('Y-m-d', strtotime($post->dailyloan_account_maturity_date));
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->dailyloanopening_date))) ;

        if (!$result) {
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Please Check your session"],400);
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
            ->where('opening_accounts.accountNo', $post->dailyloan_account_no)
            ->where('accountname','Daily Loan')
            ->where('opening_accounts.status','Active')
            ->first();

        if($opening_accounts->transactionDate > $openingdatee){
            return response()->json(['statuscode'=>'ERR', 'status'=>'fail', 'messages' => "Check Account Open Date"],400);
        }

        if($opening_accounts->groupCode && $opening_accounts->ledgerCode){
            $dailyloan_group = $opening_accounts->groupCode;
            $dailyloan_ledger = $opening_accounts->ledgerCode;
        }else{
            return response()->json(['status'=>'fail', 'messages' => "Daily Loan Group Code & Ledger Code"],400);
        }


        $rand = "RD" . time();
        $preAccount = DB::table('dailyloandeposits')
            ->where('dailyloan_account_no',$post->dailyloan_account_no)
            ->where('memberType',$post->member_type)
            ->first();

        if($preAccount){
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {

            DB::beginTransaction();
            try{
                $dailyloanaccount = new DailyLoanDeposits;
                $dailyloanaccount->serialNo = $rand;
                $dailyloanaccount->secheme_id = $opening_accounts->schemetype;
                $dailyloanaccount->memberType = $post->member_type;
                $dailyloanaccount->accountId = $opening_accounts->accountNo;
                $dailyloanaccount->accountNo = $post->dailyloan_account_no;
                $dailyloanaccount->dailyloan_account_no = $post->dailyloan_account_no;
                $dailyloanaccount->amount = $post->dailyloanaccount_amount;
                $dailyloanaccount->month = $post->dailyloanaccount_days;
                $dailyloanaccount->date = $openingdatee;
                $dailyloanaccount->paid_interest = !empty($post->dailyloan_account_paid_interest) ? $post->dailyloan_account_paid_interest : 0;
                $dailyloanaccount->ledger_folio_no = $post->dailyloan_account_lf_no;
                $dailyloanaccount->misid = 0;
                $dailyloanaccount->matureserialNo = $rand;
                $dailyloanaccount->interest = $post->dailyloanaccount_interest_label;
                $dailyloanaccount->maturity_date = $maturity_date;
                $dailyloanaccount->groupCode = $dailyloan_group;
                $dailyloanaccount->ledgerCode = $dailyloan_ledger;
                $dailyloanaccount->actual_maturity_date = $maturity_date;
                $dailyloanaccount->actual_maturity_amount = $post->dailyloan_account_maturity_amount;
                $dailyloanaccount->branchId = session('branchId') ? session('branchId') : 1;
                $dailyloanaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $dailyloanaccount->agentId = $post->dailyloan_account_agent;
                $dailyloanaccount->updatedBy = $post->user()->id;
                $dailyloanaccount->status = 'Active';
                $dailyloanaccount->save();

                // Store Installments
                $interest = $post->dailyloanaccount_interest_label;
                $amount = $post->dailyloanaccount_amount;
                $months = $post->dailyloanaccount_days;
                $startDate = new DateTime($openingdatee);
                $branchid = "1";
                for ($i = 1; $i <= $months; $i++) {
                    $randinstall = "RdI" . time();
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $installmentsdata = new DailyLoanInstallments;
                    $installmentsdata->serialNo = $randinstall;
                    $installmentsdata->dailyloan_id = $dailyloanaccount->id;
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
                if(is_null($dailyloanaccount)){
                    $deposit_amount = '';
                }else{
                    $deposit_amount = DB::table('dailyloan_receiptdetails')
                        ->select(
                            DB::raw('SUM(dailyloan_receiptdetails.amount) as deposit'),
                            DB::raw('SUM(dailyloan_receiptdetails.panelty) as penality')
                        )
                        ->where('dailyloan_account_no',$dailyloanaccount->accountId)
                        ->where('rc_account_no',$dailyloanaccount->id)
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'dailyloanaccount' => $dailyloanaccount,
                    'deposit_amount' => $deposit_amount,
                    'opening_accounts'=> $opening_accounts,
                    'messages' => 'Data Updated Successfully !!'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }

        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Account No Not Found !!']);
        }
    }


    public function dailyloandeleteInstallments($dailyloan_account_no){
        $dailyloan_account = DB::table('dailyloandeposits')->where('dailyloan_account_no', $dailyloan_account_no)->where('status', 'Active')->first();

        if (is_null($dailyloan_account)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Recodailyloan Not Found']);
        } else {
            DB::beginTransaction();
            try{
                DB::table('dailyloan_installments')->where('dailyloan_id', $dailyloan_account->id)->delete();
                DB::table('dailyloandeposits')->where('dailyloan_account_no', $dailyloan_account_no)->where('status', 'Active')->delete();
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'messages' => 'Recodailyloan Deleted Successfully'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'An error occurred while deleting the recodailyloan',
                    'error' => $e->getMessage()
                ]);
            }
        }
    } 
}
