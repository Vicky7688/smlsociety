<?php

namespace App\Http\Controllers\transaction;

use App\Http\Controllers\Controller;
use App\Models\AgentCommission;
use App\Models\LedgerMaster;
use App\Models\AgentMaster;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AgentCommissionController extends Controller
{


    public function securityoncommissionIndex()
    {
        return view('transaction.securityoncommission');
    }

    public function getcashbanksaving(Request $post)
    {
        $cashbanksaving = $post->cashbank;
        $memberType = $post->memberType;

        $transactionType = $post->transactionType;

        if ($transactionType === 'Transfer') {
            $account_no = $post->account_no;
            $securityAccount = DB::table('security_on_commission_account')
                ->where('staff_no', $account_no)
                ->where('memberType', $memberType)
                ->first();
            if (!empty($securityAccount)) {

                $saving = DB::table('opening_accounts')
                    ->where('membershipno', $securityAccount->staff_no)
                    ->where('membertype', $securityAccount->memberType)
                    ->where('accountname', 'Saving')
                    ->where('status', 'Active')
                    ->first();

                if (!empty($saving)) {
                    return response()->json([
                        'status' => 'success',
                        'savingaccount' => $saving
                    ]);
                } else {
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'Record Not Found'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Security Account Not Found'
                ]);
            }
        } else {

            switch ($cashbanksaving) {
                case 'Cash':
                    $ledgers = LedgerMaster::where('name', $cashbanksaving)->where('status', 'Active')->orderBy('name', 'ASC')->get();
                    if (!empty($ledgers)) {
                        return response()->json([
                            'status' => 'success',
                            'ledgers' => $ledgers
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Record Not Found'
                        ]);
                    }
                    break;
                case 'Bank':
                    $ledgers = LedgerMaster::where('groupCode', 'BANK001')->where('ledgerCode', '!=', 'BANKFD01')->where('status', 'Active')->orderBy('name', 'ASC')->get();
                    if (!empty($ledgers)) {
                        return response()->json([
                            'status' => 'success',
                            'ledgers' => $ledgers
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Record Not Found'
                        ]);
                    }
                    break;
            }
        }
    }

    public function deletepaidcommission(Request $post){
        $id = $post->id;
        $commissionId = DB::table('agent_commissions')->where('id',$id)->first();

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($commissionId->endDate)));

        if (! $result) {
            return response()->json(['status' => 'Fail', 'messages' => 'Please Check your session']);
        }



        if(is_null($commissionId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            DB::beginTransaction();
            try{

                DB::table('general_ledgers')->where('serialNo',$commissionId->serialNo)->delete();
                DB::table('member_savings')->where('serialNo',$commissionId->serialNo)->delete();
                DB::table('securities_saving')->where('serialNo',$commissionId->serialNo)->delete();
                DB::table('agent_commissions')->where('id',$id)->delete();

                DB::commit();

                return response()->json(['status' => 'success','messages' => 'Record Deleted Successfully']);

            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        }
    }


    public function getagentaccountlist(Request $post)
    {

        $account_no = $post->account_no;
        $memberType = $post->memberType;
        $all_accounts = DB::table('security_on_commission_account')->where('staff_no', 'LIKE', $account_no . '%')->where('memberType', $memberType)->get();
        if (!empty($all_accounts)) {
            return response()->json(['status' => 'success', 'all_aacounts' => $all_accounts]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function getsecurityaccountdetail(Request $post)
    {
        $accountNumber = $post->accountNumber;
        $memberType = $post->memberType;
        $account = DB::table('security_on_commission_account')->where('staff_no', $accountNumber)->where('memberType', $memberType)->first();

        $opening_amount = 0;

        if (!empty($account)) {
            $session_master = SessionMaster::find(Session::get('sessionId'));

            if (! empty($session_master)) {

                //__________Get Pervious Year Closing Balance From Member Saving Table
                $previous_balance = DB::table('securities_saving')
                    ->where('staff_no', $account->staff_no)
                    ->where('type', $account->memberType)
                    ->whereDate('transactionDate', '<', $session_master->startDate)
                    ->get();


                // _________Get Current Year Entries
                $security_entries = DB::table('securities_saving')
                    ->select('securities_saving.*', 'users.id as userid', 'users.name as username')
                    ->leftJoin('users', 'users.id', 'securities_saving.updatedBy')
                    ->where('securities_saving.staff_no', $account->staff_no)
                    ->where('securities_saving.account_no', $account->account_no)
                    ->where('type', $account->memberType)
                    ->whereDate('securities_saving.transactionDate', '>=', $session_master->startDate)
                    ->whereDate('securities_saving.transactionDate', '<=', $session_master->endDate)
                    ->orderBy('transactionDate', 'ASC')
                    ->get();


                //______Get Opening Amount
                if ($previous_balance) {
                    $previous_balance = collect($previous_balance);
                    $opening_amount = $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');
                } else {
                    $opening_amount = 0;
                }



                if ($previous_balance || $security_entries || $opening_amount) {
                    return response()->json([
                        'status' => 'success',
                        'account' => $account,
                        'opening_amount' => $opening_amount,
                        'security_entries' => $security_entries,
                    ]);
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                }
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function AgentCommissionIndex()
    {
        $agents = AgentMaster::where('status', '=', 'Active')->orderBy('name', 'DESC')->get();
        $commissions = AgentCommission::orderBy('id','DESC')->get();
        $data['agents'] = $agents;
        $data['commissions'] = $commissions;
        return view('transaction.agentcommission', $data);
    }

    public function GetAgentCommissions(Request $post){

        $start_date = date('Y-m-d', strtotime($post->start_date));
        $end_date = date('Y-m-d', strtotime($post->end_date));
        $agent_id = $post->agents;

        $checkCommissionPaid = DB::table('agent_commissions')
            ->whereDate('startDate', '>=', $start_date)
            ->whereDate('endDate', '<=', $end_date)
            ->where('agentid', $agent_id)
            ->get();

        if (!$checkCommissionPaid->isEmpty()) {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Commission Already Paid: ' . $post->start_date . ' Between ' . $post->end_date
            ]);
        } else {
            $rd_accounts = DB::table('rd_receiptdetails')
                ->select(
                    DB::raw('SUM(amount) as rd_amount'), // Sum of all RD amounts
                    'agent_masters.name as agent_name',
                    'agent_masters.commissionRD',
                    'agent_masters.id as agent_ids'
                )
                ->leftJoin('agent_masters', 'agent_masters.id', '=', 'rd_receiptdetails.agentid')
                ->where('rd_receiptdetails.agentid', '=', $agent_id)
                ->whereDate('rd_receiptdetails.payment_date', '>=', $start_date)
                ->whereDate('rd_receiptdetails.payment_date', '<=', $end_date)
                ->groupBy(
                    'agent_masters.name',
                    'agent_masters.commissionRD',
                    'agent_masters.id'
                )
                ->get();




            $fd_accounts = DB::table('member_fds_scheme')
                ->select(
                    DB::raw('SUM(principalAmount) as fd_amount'),
                    'agent_masters.name as agent_name',
                    'agent_masters.commissionFD',
                    'agent_masters.id as agent_ids'
                )
                ->leftJoin('agent_masters', 'agent_masters.id', '=', 'member_fds_scheme.agentId')
                ->where('member_fds_scheme.agentId', '=', $agent_id)
                ->whereDate('member_fds_scheme.openingDate', '>=', $start_date)
                ->whereDate('member_fds_scheme.openingDate', '<=', $end_date)
                ->groupBy(
                    'agent_masters.name',
                    'agent_masters.commissionFD',
                    'agent_masters.id'
                )
                ->get();



            $daily_saving =  DB::table('daily_collectionsavings')
                ->select(
                    DB::raw('SUM(deposit) as daily_amount'),
                    'agent_masters.name as agent_name',
                    'agent_masters.daily_saving',
                    'agent_masters.id as agent_ids'
                )
                ->leftJoin('agent_masters', 'agent_masters.id', '=', 'daily_collectionsavings.agentid')
                ->where('daily_collectionsavings.agentid', '=', $agent_id)
                ->whereDate('daily_collectionsavings.receipt_date', '>=', $start_date)
                ->whereDate('daily_collectionsavings.receipt_date', '<=', $end_date)
                ->groupBy(
                    'agent_masters.name',
                    'agent_masters.daily_saving',
                    'agent_masters.id'
                )
                ->get();

            $daily_loan =  DB::table('dailyrcovery')
                ->select(
                    DB::raw('SUM(transactionamount) as daily_loan'),
                    'agent_masters.name as agent_name',
                    'agent_masters.commissionLoan',
                    'agent_masters.id as agent_ids'
                )
                ->leftJoin('agent_masters', 'agent_masters.id', '=', 'dailyrcovery.agentId')
                ->where('dailyrcovery.agentId', '=', $agent_id)
                ->whereDate('dailyrcovery.recoverydate', '>=', $start_date)
                ->whereDate('dailyrcovery.recoverydate', '<=', $end_date)
                ->groupBy(
                    'agent_masters.name',
                    'agent_masters.commissionLoan',
                    'agent_masters.id'
                )
                ->get();


            // $grand_total_saving = 0;
            // foreach($saving_accounts as $item){
            //     $grand_total_saving += $item->saving_amount;
            // }


            $grand_total_rd = 0;
            foreach ($rd_accounts as $item) {
                $grand_total_rd += (($item->rd_amount * $item->commissionRD)/100);
            }


            $grand_total_fd = 0;
            foreach ($fd_accounts as $item) {
                $grand_total_fd += (($item->fd_amount * $item->commissionFD)/100);
            }

            $grand_total_DailySaving = 0;
            foreach ($daily_saving as $item) {
                $grand_total_DailySaving += (($item->daily_amount * $item->daily_saving)/100);
            }


            $grand_total_DailyLoan = 0;
            foreach ($daily_loan as $item) {
                $grand_total_DailyLoan += (($item->daily_loan * $item->commissionLoan)/100);
            }

            //________Total Collection Amount
            $total_collected_amount =  $grand_total_rd  + $grand_total_fd + $grand_total_DailySaving + $grand_total_DailyLoan;

            // dd($total_collected_amount);

            $tds_slabs = DB::table('tds_master')->where('status', '=', 'Active')->get();

            $tds_rate = 0;

            if (!empty($tds_slabs)) {
                foreach ($tds_slabs as $slab) {
                    if ($slab->start_amount <= $total_collected_amount && $total_collected_amount <= $slab->end_amount) {
                        $tds_rate = $slab->tds_rate;
                        break;
                    }
                }
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'TDS Rate Not Found']);
            }


            if (!empty($saving_accounts) || !empty($rd_accounts) || !empty($fd_accounts) || !empty($daily_saving) || !empty($daily_loan)) {
                return response()->json([
                    'status' => 'success',
                    'rd_accounts' => $rd_accounts,
                    'fd_accounts' => $fd_accounts,
                    'daily_saving' => $daily_saving,
                    'daily_loan' => $daily_loan,
                    'tds_rate' => $tds_rate
                ]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            }


            // $saving_accounts = DB::table('member_savings')
            //     ->select(
            //         DB::raw('SUM(depositAmount) as saving_amount'),
            //         'agent_masters.name as agent_name','agent_masters.commissionSaving','agent_masters.id as agent_ids'
            //     )
            //     ->leftJoin('agent_masters', 'agent_masters.id', '=', 'member_savings.agentId')
            //     ->where('member_savings.agentId', '=', $agent_id)
            //     ->whereDate('member_savings.transactionDate', '>=', $start_date)
            //     ->whereDate('member_savings.transactionDate', '<=', $end_date)
            //     ->groupBy('agent_masters.name','agent_masters.commissionSaving','agent_masters.id')
            //     ->get();






            // //_______Saving Account Commission_________________
            // $saving_accounts =  $this->SavingAccountDetails($start_date,$end_date,$agent_id);
            // dd($saving_accounts);

            // $grand_total_saving = 0;
            // foreach($saving_accounts as $item){
            //     $grand_total_saving += $item->saving_amount;
            // }

            // //_______RD Account Commission______________
            // $rd_accounts =  $this->RecurringDepositAccountDetails($start_date,$end_date,$agent_id);

            // $grand_total_rd = 0;
            // foreach($rd_accounts as $item){
            //     $grand_total_rd += $item->rd_amount;
            // }

            // //_________________FD Account Commission___________________
            // $fd_accounts =  $this->FixedDepositAccountDetails($start_date,$end_date,$agent_id);

            // $grand_total_fd = 0;
            // foreach($fd_accounts as $item){
            //     $grand_total_fd += $item->fd_amount;
            // }

            // //________Total Collection Amount
            // $total_collected_amount = $grand_total_rd + $grand_total_rd  + $grand_total_fd;

            // //__________Get Tax Slab


            // if($saving_accounts || $rd_accounts || $fd_accounts || $tds_rate){
            //     return response()->json([
            //         'status' => 'success',
            //         'saving_account' => $saving_accounts,
            //         'rd_account' => $rd_accounts,
            //         'fd_account' => $fd_accounts,
            //         'tds_rate' => $tds_rate
            //     ]);
            // }else{
            //     return response()->json([
            //         'status' => 'Fail',
            //         'messages' => 'Record not Found'
            //     ]);
            // }
        }
    }


    // private function SavingAccountDetails($start_date,$end_date,$agent_id){

    //     $saving_accounts = DB::table('member_savings')
    //         ->select(
    //             DB::raw('SUM(depositAmount) as saving_amount'),
    //             'member_savings.accountNo as acc','agent_masters.name as agent_name','agent_masters.commissionSaving',
    //             'agent_masters.id as agent_ids','member_accounts.accountNo as membership_no','member_accounts.name'
    //         )
    //         ->leftJoin('agent_masters', 'agent_masters.id', '=', 'member_savings.agentId')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
    //         ->where('member_savings.agentId', '=', $agent_id)
    //         ->whereDate('member_savings.transactionDate', '>=', $start_date)
    //         ->whereDate('member_savings.transactionDate', '<=', $end_date)
    //         ->groupBy(
    //             'member_savings.accountNo','agent_masters.name','agent_masters.commissionSaving',
    //             'agent_masters.id','member_accounts.accountNo','member_accounts.name'
    //         )
    //         ->get();
    //         return $saving_accounts;
    // }

    // private function RecurringDepositAccountDetails($start_date,$end_date,$agent_id){
    //     $rd_accounts = DB::table('rd_receiptdetails')
    //         ->select(
    //             DB::raw('SUM(amount) as rd_amount'),
    //             'rd_receiptdetails.rd_account_no as acc','agent_masters.name as agent_name','agent_masters.commissionRD',
    //             'agent_masters.id as agent_ids','member_accounts.accountNo as membership_no','member_accounts.name'
    //         )
    //         ->leftJoin('agent_masters', 'agent_masters.id', '=', 'rd_receiptdetails.agentid')
    //         ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'rd_receiptdetails.rd_account_no')
    //         ->where('rd_receiptdetails.agentid', '=', $agent_id)
    //         ->whereDate('rd_receiptdetails.payment_date', '>=', $start_date)
    //         ->whereDate('rd_receiptdetails.payment_date', '<=', $end_date)
    //         ->groupBy(
    //             'rd_receiptdetails.rd_account_no','agent_masters.name','agent_masters.commissionRD',
    //             'agent_masters.id','member_accounts.accountNo','member_accounts.name'
    //         )
    //         ->get();
    //     return $rd_accounts;
    // }

    // private function FixedDepositAccountDetails($start_date,$end_date,$agent_id){
    //     $fd_accounts = DB::table('member_fds')
    //     ->select(
    //         DB::raw('SUM(principalAmount) as fd_amount'),
    //         'member_fds.accountNo as acc','agent_masters.name as agent_name','agent_masters.commissionFD',
    //         'agent_masters.id as agent_ids','member_accounts.accountNo as membership_no','member_accounts.name'
    //     )
    //     ->leftJoin('agent_masters', 'agent_masters.id', '=', 'member_fds.agentId')
    //     ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_fds.accountNo')
    //     ->where('member_fds.agentId', '=', $agent_id)
    //     ->whereDate('member_fds.openingDate', '>=', $start_date)
    //     ->whereDate('member_fds.openingDate', '<=', $end_date)
    //     ->groupBy(
    //         'member_fds.accountNo','agent_masters.name','agent_masters.commissionFD',
    //         'agent_masters.id','member_accounts.accountNo','member_accounts.name'
    //     )
    //     ->get();
    //     dd($fd_accounts);
    //     return $fd_accounts;
    // }


    public function PaidAgentCommission(Request $post){
        // dd($post->all());
        $agentDetails = DB::table('agent_masters')->where('id', $post->agents)->first();
        $savingAccount = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'ledger_masters.reference_id',
                'ledger_masters.ledgerCode as schCode',
                'ledger_masters.groupCode'
            )
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.membershipno', $agentDetails->staff_no)
            ->where('opening_accounts.membertype', $agentDetails->memberType)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();

        if ($savingAccount) {
            if ($savingAccount->schCode && $savingAccount->groupCode) {
                $savingGroup = $savingAccount->groupCode;
                $savingLedger = $savingAccount->schCode;
            } else {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Saving Group/Ledger Not Found'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Saving Account Not Found'
            ]);
        }

        $securityAccount = DB::table('security_on_commission_account')->where('account_no', $agentDetails->staff_no)->first();

        if (!empty($securityAccount)) {
            if ($securityAccount->groupCode && $securityAccount->ledgerCode) {
                $securityGroup = $securityAccount->groupCode;
                $securityLedger = $securityAccount->ledgerCode;
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Security Group/Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Security Account Not Found']);
        }

        $staffNumber = $agentDetails->staff_no;
        $accountNumber = $savingAccount->accountNo;

        DB::beginTransaction();

        try {
            $serialNumber = 'Commission' . time();

            $id = DB::table('agent_commissions')->insertGetId([
                'serialNo' => $serialNumber,
                'startDate' => date('Y-m-d', strtotime($post->startDate)),
                'endDate' => date('Y-m-d', strtotime($post->endDate)),
                'member_name' => $savingAccount->membershipno,
                'account_no' => $accountNumber,
                'account_type' => $securityAccount->memberType,
                'agent_name' => $agentDetails->name,
                'commission_amount' => $post->commission_amount,
                'security_amount' => $post->security_amount,
                'net_amount' => $post->net_amount,
                'commission_rate' => $post->commission_rate,
                'tds_rate' => $post->tds_rate,
                'tds_amount' => $post->tds_amount,
                'agentid' => $post->agents,
                'status' => 'Paid',
                'narration' => 'Commission Trfd To Agent',
                'branchId' => session('branchId') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
            ]);


            // $security_amount = round((($post->commission_amount * 10) / 100));
            // $amount =  round($post->net_amount - $security_amount);

            // $saving_amount = $post->commission_amount ?? 0 - $post->tds_amount ?? 0 - $post->security_amount ?? 0;
            // $security_amount = ($post->commission_amount ?? 0 - $post->tds_amount)


            //____________Saving Account Table

            DB::table('member_savings')->insertGetId([
                'secheme_id' => $savingAccount->schemetype,
                'serialNo' => $serialNumber,
                'accountId' => $accountNumber,
                'accountNo' => $savingAccount->membershipno,
                'memberType' => $savingAccount->membertype,
                'groupCode' => $savingGroup,
                'ledgerCode' => $savingLedger,
                'savingNo' =>  $accountNumber,
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'transactionType' => 'Deposit',
                'depositAmount' => $post->net_amount,
                'withdrawAmount' => 0,
                'paymentType' => '',
                'bank' => '',
                'chequeNo' => 'Agent Commission',
                'narration' => 'Agent Commission Paid',
                'branchId' => session('branchId') ? session('branchId') : 1,
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'agentId' => $post->agents,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            //__________Securitries Entries
            DB::table('securities_saving')->insertGetId([
                'serialNo' => $serialNumber,
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'staff_no' => $securityAccount->staff_no,
                'account_no' => $securityAccount->account_no,
                'type' => $savingAccount->membertype,
                'groupCode' => $securityGroup,
                'ledgerCode' => $securityLedger,
                'transactionType' => 'Comm',
                'depositAmount' => $post->security_amount,
                'withdrawAmount' => 0,
                'paymentType' => '',
                'bank' => '',
                'narration' => 'Agent Commission Paid ' . date('d-m-Y', strtotime($post->startDate)) . ' To ' . date('d-m-Y', strtotime($post->endDate)),
                'branchId' => session('branchId') ? session('branchId') : 1,
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'agentId' => $post->agents,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            //________________________Gerenal Ledger's Entries

            // __________Commission Entry In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNumber,
                'accountId' => $accountNumber,
                'accountNo' => $staffNumber,
                'memberType' => $savingAccount->membertype,
                'groupCode' => 'EXPN001',
                'ledgerCode' => 'AGE01',
                'formName' => 'Agent Comm.',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'transactionType' => 'Dr',
                'transactionAmount' => $post->commission_amount,
                'narration' => 'Commission Paid',
                'branchId' => session('branchId') ?? 1,
                'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            // // __________TDS Entry In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNumber,
                'accountId' => $accountNumber,
                'accountNo' => $staffNumber,
                'memberType' => $savingAccount->membertype,
                'groupCode' => 'GRTTDS01',
                'ledgerCode' => 'TDS04',
                'formName' => 'Agent Comm.',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'transactionType' => 'Cr',
                'transactionAmount' => $post->tds_amount,
                'narration' => 'TDS Deduction',
                'branchId' => session('branchId') ?? 1,
                'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            //__________Security Entry In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNumber,
                'accountId' => $accountNumber,
                'accountNo' => $staffNumber,
                'memberType' => $savingAccount->membertype,
                'groupCode' => $securityGroup,
                'ledgerCode' => $securityLedger,
                'formName' => 'Agent Comm.',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'transactionType' => 'Cr',
                'transactionAmount' => $post->security_amount,
                'narration' => 'TDS Security',
                'branchId' => session('branchId') ?? 1,
                'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            //__________Saving A/c Entry In Gerenal Ledger
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNumber,
                'accountId' => $accountNumber,
                'accountNo' => $staffNumber,
                'memberType' => $savingAccount->membertype,
                'groupCode' => $savingGroup,
                'ledgerCode' =>  $savingLedger,
                'formName' => 'Agent Comm.',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($post->endDate)),
                'transactionType' => 'Cr',
                'transactionAmount' => $post->net_amount,
                'narration' => 'Commsion TRFD Saving A/c- ' . $savingAccount->accountNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'messages' => 'Commission Paid Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'Fail',
                'error' => $e->getMessage(),
                'lines' => $e->getLine()
            ]);
        }
    }


    public function insertsecuirtyaccount(Request $post){

        if (!empty($post->securityaccId)) {
            $id = $post->securityaccId;
            $transactionType = $post->transactionType;

            if ($transactionType === 'Withdraw' || $transactionType === 'Deposit') {
                $rules = [
                    'transactionDate' => 'required',
                    'account_no' => 'required|numeric',
                    'memberType' => 'required',
                    'transactionType' => 'required',
                    'cashbank' => 'required',
                    'ledgerId' => 'required'
                ];
            } else {
                $rules = [
                    'transactionDate' => 'required',
                    'account_no' => 'required|numeric',
                    'memberType' => 'required',
                    'transactionType' => 'required',
                    'savingaccounts' => 'required'
                ];
            }

            $validator = Validator::make($post->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
            }

            $accountNumber = $post->account_no;
            $type = $post->memberType;
            $member_ship = $post->membership;
            $memberType = $post->memberType;
            $amounts = $post->amount;

            $balance = $this->showDataTable($accountNumber, $memberType);


            $date = date('Y-m-d', strtotime($post->transactionDate));
            $session_master = SessionMaster::find(Session::get('sessionId'));

            if ($session_master->auditPerformed === 'Yes') {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
                ]);
            }

            $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate)));

            if (! $result) {
                return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
            }


            $checkpaymentbalance = $this->checkpaymentbalance($accountNumber, $date, $amounts, $memberType);

            if ($checkpaymentbalance) {
                return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
            }


            $account_opening = DB::table('opening_accounts')
                ->select(
                    'opening_accounts.*',
                    'schmeaster.id as sch_id',
                    'schmeaster.scheme_code',
                    'ledger_masters.reference_id',
                    'ledger_masters.ledgerCode',
                    'ledger_masters.groupCode',
                    'refSchemeMaster.scheme_code as ref_scheme_code'
                )
                ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                ->where('opening_accounts.membershipno', $member_ship)
                ->where('opening_accounts.accountNo', $accountNumber)
                ->where('opening_accounts.membertype', $type)
                ->where('opening_accounts.accountname', 'Saving')
                ->first();



            $securityAccount = DB::table('security_on_commission_account')
                ->where('staff_no', $accountNumber)
                ->where('memberType', $type)
                ->first();

            if ($securityAccount->groupCode && $securityAccount->ledgerCode) {
                $securityGroup = $securityAccount->groupCode;
                $securityLedger = $securityAccount->ledgerCode;
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Security A/c Group/Ledger Code Not Found']);
            }


            $serialNumber = 'Securities' . time();

            if ($transactionType === 'Withdraw') {

                DB::beginTransaction();
                try {
                        $exitsId = DB::table('securities_saving')->where('id', $id)->first();

                        DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                        DB::table('securities_saving')->where('id', $id)->delete();


                    if ($post->cashbank && $post->ledgerId) {
                        $cash_bank_group = 'C002';;
                        $cash_ledger_group = $post->ledgerId;
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group||Ledger Code Not Found']);
                    }


                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Withdraw',
                        'depositAmount' => 0,
                        'withdrawAmount' => $post->amount,
                        'paymentType' => $cash_bank_group,
                        'bank' => $cash_ledger_group,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId')  ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $cash_bank_group,
                        'ledgerCode' => $cash_ledger_group,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();


                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }elseif($transactionType === 'Deposit'){
                DB::beginTransaction();
                try {

                    $exitsId = DB::table('securities_saving')->where('id', $id)->first();

                    DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('securities_saving')->where('id', $id)->delete();

                    if ($post->cashbank && $post->ledgerId) {
                        $cash_bank_group = 'C002';
                        $cash_ledger_group = $post->ledgerId;
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group||Ledger Code Not Found']);
                    }


                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $post->amount,
                        'withdrawAmount' => 0,
                        'paymentType' => $cash_bank_group,
                        'bank' => $cash_ledger_group,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId')  ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Deposit',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $cash_bank_group,
                        'ledgerCode' => $cash_ledger_group,
                        'formName' => 'Security Deposit',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();

                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }else {
                DB::beginTransaction();
                try {

                    $exitsId = DB::table('securities_saving')->where('id', $id)->first();

                    DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('member_savings')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('securities_saving')->where('id', $id)->delete();

                    if ($account_opening) {
                        if ($account_opening->groupCode && $account_opening->ledgerCode) {
                            $saving_group = $account_opening->groupCode;
                            $saving_ledger = $account_opening->ledgerCode;
                        } else {
                            return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                        }
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                    }

                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Transfer',
                        'depositAmount' => 0,
                        'withdrawAmount' => $post->amount,
                        'paymentType' => '',
                        'bank' => '',
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('member_savings')->insert([
                        'secheme_id' => $account_opening->schemetype,
                        'serialNo' => $serialNumber,
                        'accountId' => $account_opening->accountNo,
                        'accountNo' => $account_opening->membershipno,
                        'memberType' => $account_opening->membertype,
                        'groupCode' => $saving_group,
                        'ledgerCode' => $saving_ledger,
                        'savingNo' =>  $accountNumber,
                        'transactionDate' => $date,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $post->amount,
                        'withdrawAmount' => 0,
                        'paymentType' => '',
                        'bank' => '',
                        'chequeNo' => 'Security Trfd',
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        // 'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $saving_group,
                        'ledgerCode' => $saving_ledger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();
                    $memberType = $post->memberType;
                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }

        } else {

            $transactionType = $post->transactionType;

            if ($transactionType === 'Withdraw' || $transactionType === 'Deposit') {
                $rules = [
                    'transactionDate' => 'required',
                    'account_no' => 'required|numeric',
                    'memberType' => 'required',
                    'transactionType' => 'required',
                    'cashbank' => 'required',
                    'ledgerId' => 'required'
                ];
            } else {
                $rules = [
                    'transactionDate' => 'required',
                    'account_no' => 'required|numeric',
                    'memberType' => 'required',
                    'transactionType' => 'required',
                    'savingaccounts' => 'required'
                ];
            }

            $validator = Validator::make($post->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
            }

            $accountNumber = $post->account_no;
            $type = $post->memberType;
            $member_ship = $post->membership;
            $memberType = $post->memberType;
            $amounts = $post->amount;

            $balance = $this->showDataTable($accountNumber, $memberType);


            $date = date('Y-m-d', strtotime($post->transactionDate));
            $session_master = SessionMaster::find(Session::get('sessionId'));

            if ($session_master->auditPerformed === 'Yes') {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
                ]);
            }

            $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate)));

            if (! $result) {
                return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
            }




            $account_opening = DB::table('opening_accounts')
                ->select(
                    'opening_accounts.*',
                    'schmeaster.id as sch_id',
                    'schmeaster.scheme_code',
                    'ledger_masters.reference_id',
                    'ledger_masters.ledgerCode',
                    'ledger_masters.groupCode',
                    'refSchemeMaster.scheme_code as ref_scheme_code'
                )
                ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                ->where('opening_accounts.membershipno', $member_ship)
                ->where('opening_accounts.accountNo', $accountNumber)
                ->where('opening_accounts.membertype', $type)
                ->where('opening_accounts.accountname', 'Saving')
                ->first();



            $securityAccount = DB::table('security_on_commission_account')
                ->where('staff_no', $accountNumber)
                ->where('memberType', $type)
                ->first();

            if ($securityAccount->groupCode && $securityAccount->ledgerCode) {
                $securityGroup = $securityAccount->groupCode;
                $securityLedger = $securityAccount->ledgerCode;
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Security A/c Group/Ledger Code Not Found']);
            }


            $serialNumber = 'Securities' . time();

            if ($transactionType === 'Withdraw') {


                DB::beginTransaction();
                try {

                    $checkpaymentbalance = $this->checkpaymentbalance($accountNumber, $date, $amounts, $memberType);

                    if ($checkpaymentbalance) {
                        return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                    }



                    if ($post->cashbank && $post->ledgerId) {
                        $cash_bank_group = 'C002';
                        $cash_ledger_group = $post->ledgerId;
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group||Ledger Code Not Found']);
                    }


                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Withdraw',
                        'depositAmount' => 0,
                        'withdrawAmount' => $post->amount,
                        'paymentType' => $cash_bank_group,
                        'bank' => $cash_ledger_group,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId')  ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $cash_bank_group,
                        'ledgerCode' => $cash_ledger_group,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();


                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }elseif($transactionType === 'Deposit'){

                DB::beginTransaction();
                try {
                    if ($post->cashbank && $post->ledgerId) {
                        $cash_bank_group = $cash_bank_group = 'C002';;
                        $cash_ledger_group = $post->ledgerId;

                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group||Ledger Code Not Found']);
                    }


                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $post->amount,
                        'withdrawAmount' => 0,
                        'paymentType' => $cash_bank_group,
                        'bank' => $cash_ledger_group,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId')  ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Deposit',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $cash_bank_group,
                        'ledgerCode' => $cash_ledger_group,
                        'formName' => 'Security Deposit',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Deposit A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();


                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }else {
                DB::beginTransaction();
                try {

                    $checkpaymentbalance = $this->checkpaymentbalance($accountNumber, $date, $amounts, $memberType);

                    if ($checkpaymentbalance) {
                        return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                    }


                    if ($account_opening) {
                        if ($account_opening->groupCode && $account_opening->ledgerCode) {
                            $saving_group = $account_opening->groupCode;
                            $saving_ledger = $account_opening->ledgerCode;
                        } else {
                            return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                        }
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                    }

                    $id = DB::table('securities_saving')->insertGetId([
                        'serialNo' => $serialNumber,
                        'transactionDate' => $date,
                        'staff_no' => $accountNumber,
                        'account_no' => $accountNumber,
                        'type' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'transactionType' => 'Transfer',
                        'depositAmount' => 0,
                        'withdrawAmount' => $post->amount,
                        'paymentType' => '',
                        'bank' => '',
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    DB::table('member_savings')->insert([
                        'secheme_id' => $account_opening->schemetype,
                        'serialNo' => $serialNumber,
                        'accountId' => $account_opening->accountNo,
                        'accountNo' => $account_opening->membershipno,
                        'memberType' => $account_opening->membertype,
                        'groupCode' => $saving_group,
                        'ledgerCode' => $saving_ledger,
                        'savingNo' =>  $accountNumber,
                        'transactionDate' => $date,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $post->amount,
                        'withdrawAmount' => 0,
                        'paymentType' => '',
                        'bank' => '',
                        'chequeNo' => 'Security Trfd',
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        // 'agentId' => '',
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    //________________________Gerenal Ledger's Entries

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $securityGroup,
                        'ledgerCode' => $securityLedger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' =>  'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNumber,
                        'accountId' => $accountNumber,
                        'accountNo' => $accountNumber,
                        'memberType' => $type,
                        'groupCode' => $saving_group,
                        'ledgerCode' => $saving_ledger,
                        'formName' => 'Security Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $date,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => 'Security Amount Paid A/c- ' . $accountNumber . 'To Saving A/c- ' . $accountNumber ?? $post->narration,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);

                    DB::commit();
                    $memberType = $post->memberType;
                    return $this->showDataTable($accountNumber, $memberType);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                }
            }
        }
    }

    public function deletesecurityaccount(Request $post)
    {
        $id = $post->id;
        $memberType = $post->memberType;
        $accountNumber = $post->account_no;


        $exitsId = DB::table('securities_saving')->where('id', $id)->first();

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($exitsId->transactionDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        if (is_null($exitsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {

            $transactionType = $exitsId->transactionType;
            if ($transactionType === 'Withdraw') {
                DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                DB::table('securities_saving')->where('id', $id)->delete();

                return $this->showDataTable($accountNumber, $memberType);
            }elseif($transactionType === 'Deposit'){

                if($exitsId->chequeNo === 'Interest Received'){

                    DB::table('securities_saving')->where('id', $id)->delete();
                    DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('securityoncomm_interest_calculations')->where('serialNo',$exitsId->serialNo)->delete();

                }else{

                    DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('securities_saving')->where('id', $id)->delete();

                }

                return $this->showDataTable($accountNumber, $memberType);

            }else {

                DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                DB::table('member_savings')->where('serialNo', $exitsId->serialNo)->delete();
                DB::table('securities_saving')->where('id', $id)->delete();

                return $this->showDataTable($accountNumber, $memberType);
            }
        }
    }


    public function showDataTable($accountNumber, $memberType)
    {

        $account = DB::table('security_on_commission_account')->where('staff_no', $accountNumber)->where('memberType', $memberType)->first();

        $opening_amount = 0;

        if (!empty($account)) {
            $session_master = SessionMaster::find(Session::get('sessionId'));

            if (! empty($session_master)) {

                //__________Get Pervious Year Closing Balance From Member Saving Table
                $previous_balance = DB::table('securities_saving')
                    ->where('staff_no', $account->staff_no)
                    ->where('type', $account->memberType)
                    ->whereDate('transactionDate', '<', $session_master->startDate)
                    ->get();


                // _________Get Current Year Entries
                $security_entries = DB::table('securities_saving')
                    ->select('securities_saving.*', 'users.id as userid', 'users.name as username')
                    ->leftJoin('users', 'users.id', 'securities_saving.updatedBy')
                    ->where('securities_saving.staff_no', $account->staff_no)
                    ->where('securities_saving.account_no', $account->account_no)
                    ->where('type', $account->memberType)
                    ->whereDate('securities_saving.transactionDate', '>=', $session_master->startDate)
                    ->whereDate('securities_saving.transactionDate', '<=', $session_master->endDate)
                    ->orderBy('transactionDate', 'ASC')
                    ->get();


                //______Get Opening Amount
                if ($previous_balance) {
                    $previous_balance = collect($previous_balance);
                    $opening_amount = $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');
                } else {
                    $opening_amount = 0;
                }



                if ($previous_balance || $security_entries || $opening_amount) {
                    return response()->json([
                        'status' => 'success',
                        'account' => $account,
                        'opening_amount' => $opening_amount,
                        'security_entries' => $security_entries,
                    ]);
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                }
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }


    public function checkpaymentbalance($accountNumber, $date, $amounts, $memberType)
    {
        $account = DB::table('security_on_commission_account')->where('staff_no', $accountNumber)->where('memberType', $memberType)->first();


        $opening_amount = 0;

        if (!empty($account)) {
            $session_master = SessionMaster::find(Session::get('sessionId'));

            if (! empty($session_master)) {

                //__________Get Pervious Year Closing Balance From Member Saving Table
                $previous_balance = DB::table('securities_saving')
                    ->where('staff_no', $account->staff_no)
                    ->where('type', $account->memberType)
                    ->whereDate('transactionDate', '<', $session_master->startDate)
                    ->get();


                // _________Get Current Year Entries
                $security_entries = DB::table('securities_saving')
                    ->select('securities_saving.*', 'users.id as userid', 'users.name as username')
                    ->leftJoin('users', 'users.id', 'securities_saving.updatedBy')
                    ->where('securities_saving.staff_no', $account->staff_no)
                    ->where('securities_saving.account_no', $account->account_no)
                    ->where('type', $account->memberType)
                    ->whereDate('securities_saving.transactionDate', '>=', $session_master->startDate)
                    ->whereDate('securities_saving.transactionDate', '<=', $session_master->endDate)
                    ->orderBy('transactionDate', 'ASC')
                    ->get();


                //______Get Opening Amount
                if ($previous_balance) {
                    $previous_balance = collect($previous_balance);
                    $opening_amount = $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');





                    // $balance = $opening_amount; // Start with the opening amount as the initial balance

                    // if ($security_entries && count($security_entries) > 0) {
                    //     foreach ($security_entries as $saving_entrieslist) {
                    //         if (strtotime($saving_entrieslist->transactionDate) <= strtotime($date)) {
                    //             $balance += $saving_entrieslist->depositAmount - $saving_entrieslist->withdrawAmount;
                    //         }
                    //     }

                    //     if ($balance < $amounts) {
                    //         return response()->json(['status' => 'fail', 'messages' => 'You Have Insufficient Balance In Account On That Date']);
                    //     }
                    // } else {
                    //     $balance = $opening_amount;
                    //     if ($balance < $amounts) {
                    //         return response()->json(['status' => 'fail', 'messages' => 'You Have Insufficient Balance In Account On That Date']);
                    //     }
                    // }

                    // return response()->json(['status' => 'success', 'balance' => $balance]);



                } else {
                    $opening_amount = 0;
                }
            }
        }
    }

    public function editsecurityacc(Request $post)
    {
        $id = $post->id;
        $memberType = $post->memberType;
        $accountNumber = $post->account_no;

        $exitsId = DB::table('securities_saving')->where('id', $id)->first();
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($exitsId->transactionDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        if (is_null($exitsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {
            if ($exitsId->transactionType === 'Withdraw') {
                return response()->json(['status' => 'success', 'exitsaccount' => $exitsId]);
            } else {
                $account_opening = DB::table('securities_saving')->where('id', $id)->first();
                $account_opening = DB::table('opening_accounts')
                    ->select(
                        'opening_accounts.*',
                        'schmeaster.id as sch_id',
                        'schmeaster.scheme_code',
                        'ledger_masters.reference_id',
                        'ledger_masters.ledgerCode',
                        'ledger_masters.groupCode',
                        'refSchemeMaster.scheme_code as ref_scheme_code'
                    )
                    ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                    ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                    ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                    ->where('opening_accounts.membershipno', $exitsId->staff_no)
                    ->where('opening_accounts.accountNo', $accountNumber)
                    ->where('opening_accounts.membertype', $memberType)
                    ->where('opening_accounts.accountname', 'Saving')
                    ->first();
                return response()->json(['status' => 'success', 'saving' => $account_opening, 'exitsaccount' => $exitsId]);
            }
        }
    }


    public function editsecurityinterest(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check ID']);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));
        // $transactionDate = now()->format('Y-m-d');

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        // $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        // if (! $result) {
        //     return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        // }

        $id = $post->id;
        $exitsId = DB::table('securities_saving')->where('id', $id)->first();
        if(is_null($exitsId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            return response()->json(['status' => 'success','exitsId' => $exitsId]);
        }
    }


    public function securityinterestupdate(Request $post){
        $rules = [
            "interestid" => "required",
            "interest_date" => "required",
            "ineterest_account" => "required",
            "interest_paid_amount" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check ID']);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));
        $transactionDate = date('Y-m-d',strtotime($post->interest_date));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        $id = $post->interestid;
        $exitsId = DB::table('securities_saving')->where('id', $id)->first();

        if(is_null($exitsId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            DB::beginTransaction();
            try{

                //_______Delete Interest Entries In Interest Table
                $interest_table = DB::table('securityoncomm_interest_calculations')
                    ->where('serialNo', $exitsId->serialNo)
                    ->where('membership', $exitsId->staff_no)
                    ->where('accountNo', $exitsId->staff_no)
                    ->where('memberType', $post->memberTypes)
                    ->first();

                //_______Delete Interest Entries In Gerenal Ledger Table
                DB::table('general_ledgers')
                    ->where('serialNo', $exitsId->serialNo)
                    ->where('accountId', $exitsId->staff_no)
                    ->where('accountNo', $exitsId->staff_no)
                    ->where('memberType', $post->memberTypes)
                    ->delete();

                DB::table('securityoncomm_interest_calculations')
                    ->where('serialNo', $exitsId->serialNo)
                    ->where('membership', $exitsId->staff_no)
                    ->where('accountNo', $exitsId->staff_no)
                    ->where('memberType', $post->memberTypes)
                    ->delete();
                //_______Delete Interest Entries In Security Saving Table
                DB::table('securities_saving')->where('id', $id)->delete();

                $ineterestNumber = 'Interest' . Str::uuid();



                //_____________Interest Table Entry
                $id = DB::table('securityoncomm_interest_calculations')->insertGetId([
                    'start_date' => $interest_table->start_date,
                    'end_date' => $interest_table->end_date,
                    'serialNo' => $ineterestNumber,
                    'membership' => $post->ineterest_account,
                    'accountNo' => $post->ineterest_account,
                    'memberType' => $post->memberTypes,
                    'groupCode' => 'EXPN001',
                    'ledgerCode' => 'INT01',
                    'depositAmount' => 0,
                    'paid_date' => $transactionDate,
                    'withdrawAmount' => $post->interest_paid_amount,
                    'branchId' => session('branchId') ?: 1,
                    'agentId' => $post->user()->id,
                    'sessionId' => session('sessionId') ?: 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                //___________Member Security Account Table Entry
                DB::table('securities_saving')->insertGetId([
                    'serialNo' => $ineterestNumber,
                    'transactionDate' => $transactionDate,
                    'staff_no' => $post->ineterest_account,
                    'account_no' => $post->ineterest_account,
                    'type' => $post->memberTypes,
                    'groupCode' => $exitsId->groupCode,
                    'ledgerCode' => $exitsId->ledgerCode,
                    'transactionType' => 'Deposit',
                    'depositAmount' =>  $post->interest_paid_amount,
                    'withdrawAmount' => 0,
                    'paymentType' => '',
                    'bank' => '',
                    'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $exitsId->staff_no . ' ' . $interest_table->start_date. ' To ' . $interest_table->end_date,
                    'chequeNo' => 'Interest Received',
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $post->user()->id,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                //__________________General Ledger Entries_____________________

                //__________Interest Paid Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $ineterestNumber,
                    'accountId' => $post->ineterest_account,
                    'accountNo' => $post->ineterest_account,
                    'memberType' => $post->memberTypes,
                    'groupCode' => 'EXPN001',
                    'ledgerCode' => 'INT01',
                    'formName' => 'Interest Paid',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $post->interest_paid_amount,
                    'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $exitsId->staff_no . ' ' . $interest_table->start_date. ' To ' . $interest_table->end_date,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => '',
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                // __________Member Security A/c Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $ineterestNumber,
                    'accountId' => $post->ineterest_account,
                    'accountNo' => $post->ineterest_account,
                    'memberType' => $post->memberTypes,
                    'groupCode' => $exitsId->groupCode,
                    'ledgerCode' => $exitsId->ledgerCode,
                    'formName' => 'Interest Paid',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $post->interest_paid_amount,
                    'narration' => 'Intt. Paid On Saving A/c- '. $exitsId->staff_no . ' ' . $interest_table->start_date. ' To ' . $interest_table->end_date,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                DB::commit();

                $memberType = $post->memberTypes;
                $accountNumber = $post->ineterest_account;
                return $this->showDataTable($accountNumber, $memberType);

            }catch(\Exception $e){
                DB::rollBack();
                return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'lines' => $e->getLine()]);
            }
        }
    }
}
