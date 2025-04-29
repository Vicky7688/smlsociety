<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Models\InterestCalculation;
use App\Models\MemberSaving;
use App\Models\SessionMaster;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\BranchMaster;


class SavingInterestCalculationController extends Controller
{
    public function interestcalculationindex(Request $post)
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.savinginterestcalculation',$data);
    }

    public function getsavinginterestcaluclation(Request $post)
    {
        $rules = [
            "date_from" => "required|date",
            "date_till_date" => "required|date|after_or_equal:date_from",
            "rate_of_intt" => "required|numeric|min:0",
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }


        $months = 0;
        $start_date_month = date('d-m-Y', strtotime($post->date_from));
        $endDate_month = date('d-m-Y', strtotime($post->date_till_date));
        if ($start_date_month && $endDate_month) {
            $start = DateTime::createFromFormat('d-m-Y', $start_date_month);
            $end = DateTime::createFromFormat('d-m-Y', $endDate_month);
            $interval = $start->diff($end);
            $months = ($interval->y * 12) + $interval->m + 1;
        }


        $start_date = date('Y-m-d', strtotime($post->date_from));
        $endDate = date('Y-m-d', strtotime($post->date_till_date));
        $minium_amount = $post->minimum_amount;

        $memberType = $post->member_type;
        $session_master = SessionMaster::find(Session::get('sessionId'));



        // if ($start_date < $session_master->startDate || $endDate > $session_master->endDate) {
        //     return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        // }


        if (!$session_master) {
            return response()->json(['status' => 'Fail', 'messages' => 'Invalid Session']);
        }


        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }


        $session_startDate = $session_master->startDate;
        $session_endDate = $session_master->endDate;

        // if ($session_startDate > $endDate || $session_endDate < $start_date) {
        //     return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        // }



        $account_no = $post->account_no;
        $minimum_amount = $post->minimum_amount;

        if ($account_no) {

            $interestPaidDate = DB::table('interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
                ->where('accountNo', $account_no)
                ->first();

            if ($interestPaidDate) {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Interest Already Paid from ' . $post->date_from . ' To ' . $post->date_till_date
                ]);
            }

            $closingbalance = DB::table('member_accounts')
                ->select(
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name',
                    'member_savings.accountId',
                    DB::raw('SUM(CASE WHEN member_savings.depositAmount IS NOT NULL THEN member_savings.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN member_savings.withdrawAmount IS NOT NULL THEN member_savings.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                ->where('member_accounts.accountNo', $account_no)
                ->where('member_savings.memberType', $memberType)
                ->whereDate('member_savings.transactionDate', '<=', $endDate)
                ->groupBy('member_savings.accountId', 'member_accounts.accountNo', 'member_accounts.name')
                ->first();



            $account_opening = DB::table('opening_accounts as oa')
                ->select(
                    'oa.*',
                    'schmeaster.id as sch_id',
                    'schmeaster.scheme_code',
                    'ledger_masters.reference_id',
                    'ledger_masters.ledgerCode',
                    'ledger_masters.groupCode',
                    'refSchemeMaster.scheme_code as ref_scheme_code',
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name'
                )
                ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'oa.schemetype')
                ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'oa.membershipno') // Correct join here
                ->where('oa.accountNo', $account_no)
                ->where('oa.accountname', 'Saving')
                ->where('oa.membertype', $memberType)
                ->first();


            $opening_balance = 0;

            // if ($closingbalance) {
            //     $deposit_total = $closingbalance->deposit_total ?? 0;
            //     $withdraw_total = $closingbalance->withdraw_total ?? 0;
            //     $opening_balance = $deposit_total - $withdraw_total;
            // }

            // $saving_entries = DB::table('member_savings')
            //     ->select(
            //         'member_savings.*',
            //         'users.id as userid',
            //         'users.name as username',
            //     )
            //     ->leftJoin('users', 'users.id', 'member_savings.updatedBy')
            //     ->where('member_savings.accountId', $account_no)
            //     ->where('member_savings.memberType', $memberType)
            //     ->whereDate('member_savings.transactionDate', '>=', $start_date)
            //     ->whereDate('member_savings.transactionDate', '<=', $endDate)
            //     ->orderBy('transactionDate', 'ASC')
            //     ->get();

            $current_period_deposit = $closingbalance->deposit_total - $closingbalance->withdraw_total;
            $last_balance = $opening_balance + $current_period_deposit;

            $rate_of_intt = $post->rate_of_intt;
            $interest_amount = round(((($last_balance * $rate_of_intt) / 100) / 12) * $months);
            $net_amount = $last_balance + $interest_amount;




            $results[] = [
                'membershipnumber' => $account_opening->membershipno,
                'name' => $account_opening->name,
                'opening_balance' => $opening_balance ?? 0,
                // 'transactions' => $closingbalance->,
                'last_balance' => $last_balance,
                'interest_amount' => $interest_amount,
                'net_amount' => $net_amount,
                'amount' => $last_balance
            ];



            return response()->json([
                'status' => 'success',
                'balances' => $results,
            ]);
        } else {

            $interestPaidDate = DB::table('interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
                // ->select(['start_date', 'end_date','memberType'])
                ->first();

            if ($interestPaidDate) {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Interest Already Paid from ' . $post->date_from . ' To ' . $post->date_till_date
                ]);
            }

            // dd($post->all());



            $closing_balances = DB::table('member_accounts')
                ->select(
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name',
                    'member_savings.accountId',
                    DB::raw('SUM(CASE WHEN member_savings.depositAmount IS NOT NULL THEN member_savings.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN member_savings.withdrawAmount IS NOT NULL THEN member_savings.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                ->where('member_savings.memberType', $memberType)
                ->whereDate('member_savings.transactionDate', '<=', $endDate)
                ->groupBy('member_savings.accountId', 'member_accounts.accountNo', 'member_accounts.name')
                ->get();




            $last_balances = [];
            $results = [];
            $minimum_amount = $minimum_amount ?? 0; // Ensure a default value for $minimum_amount

            foreach ($closing_balances as $balance) {
                $deposit_amount = $balance->deposit_total;
                $withdraw_amount = $balance->withdraw_total;

                $last_balance = $deposit_amount - $withdraw_amount;

                $last_balances[$balance->accountId] = $last_balance;

                if ($last_balance < $minimum_amount) {
                    continue;
                }

                $mainbalance = $last_balance;
                $amount = $deposit_amount - $withdraw_amount;

                $rate_of_intt = $post->rate_of_intt ?? 0;
                $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
                $net_amount = $amount + $interest_amount;

                $results[] = [
                    'membershipnumber' => $balance->membershipnumber ?? null,
                    'name' => $balance->name ?? null,
                    'last_balance' => $last_balance,
                    'interest_amount' => $interest_amount,
                    'net_amount' => $net_amount,
                    'amount' => $amount
                ];
            }

            return response()->json([
                'status' => 'success',
                'balances' => $results
            ]);
        }


        // dd($last_balance);

    }

    public function paidsavinginterest(Request $post)
    {

        $rules = [
            "date_from" => "required|date",
            "date_till_date" => "required|date|after_or_equal:date_from",
            "rate_of_intt" => "required|numeric|min:0",
            'paid_date' => "required|date"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $start_date = date('Y-m-d', strtotime($post->date_from));
        $endDate = date('Y-m-d', strtotime($post->date_till_date));

        $session_master = SessionMaster::find(Session::get('sessionId'));

        // if ($start_date < $session_master->startDate || $endDate > $session_master->endDate) {
        //     return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        // }



        if (!$session_master) {
            return response()->json(['status' => 'Fail', 'messages' => 'Invalid Session']);
        }

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $session_startDate = $session_master->startDate;
        $session_endDate = $session_master->endDate;
        $account_no = $post->account_no;
        $memberType = $post->memberType;
        $start_date = date('Y-m-d', strtotime($post->date_from));
        $endDate = date('Y-m-d', strtotime($post->date_till_date));
        $minium_amount = $post->minimum_amount;
        $paidDate = date('Y-m-d', strtotime($post->paid_date));

        $months = 0;
        $start_date_month = date('d-m-Y', strtotime($post->date_from));
        $endDate_month = date('d-m-Y', strtotime($post->date_till_date));
        if ($start_date_month && $endDate_month) {
            $start = DateTime::createFromFormat('d-m-Y', $start_date_month);
            $end = DateTime::createFromFormat('d-m-Y', $endDate_month);
            $interval = $start->diff($end);
            $months = ($interval->y * 12) + $interval->m + 1;
        }


        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->paid_date)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }


        if ($account_no) {

            $interestPaidDate = DB::table('interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
                ->where('accountNo', $account_no)
                ->first();

            if ($interestPaidDate) {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Interest Already Paid from ' . $post->date_from . ' To ' . $post->date_till_date
                ]);
            }


            $closingbalance = DB::table('member_accounts')
                ->select(
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name',
                    'member_savings.accountId',
                    DB::raw('SUM(CASE WHEN member_savings.depositAmount IS NOT NULL THEN member_savings.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN member_savings.withdrawAmount IS NOT NULL THEN member_savings.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                ->where('member_accounts.accountNo', $account_no)
                ->where('member_accounts.memberType', $memberType)
                ->whereDate('member_savings.transactionDate', '<', $start_date)
                ->groupBy('member_savings.accountId', 'member_accounts.accountNo', 'member_accounts.name')
                ->first();
            // dd($closingbalance);



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
                ->where('opening_accounts.accountNo', $account_no)
                // ->where('member_accounts.memberType', $memberType)
                ->where('opening_accounts.membertype', $memberType)
                ->where('opening_accounts.accountname', 'Saving')
                ->first();



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


            $opening_balance = 0;

            $saving_entries = DB::table('member_savings')
                ->select('member_savings.*', 'users.id as userid', 'users.name as username')
                ->leftJoin('users', 'users.id', 'member_savings.updatedBy')
                ->where('member_savings.accountId', $account_no)
                ->where('member_savings.memberType', $post->memberType)
                ->whereDate('member_savings.transactionDate', '>=', $start_date)
                ->whereDate('member_savings.transactionDate', '<=', $endDate)
                ->orderBy('transactionDate', 'ASC')
                ->get();

            $interest_amount = 0;

            if ($closingbalance) {
                $opening_balance = $closingbalance->deposit_total - $closingbalance->withdraw_total;
                $current_period_deposit = $saving_entries->sum('depositAmount');
                $last_balance = $opening_balance + $current_period_deposit;
                $rate_of_intt = $post->rate_of_intt;
                $interest_amount += round(((($last_balance * $rate_of_intt) / 100) / 12) * $months);
                if (isset($minium_amount)) {
                    if ($last_balance < $minium_amount) {
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Account Amount Less Than Entered Minimum Amount'
                        ]);
                    }
                    $mainbalance = $last_balance;
                } else {
                    $mainbalance = $minium_amount ?? 0;
                }
            } else {
                $opening_balance = 0;
            }


            $ineterestNumber = 'Interest' . Str::uuid();

            DB::beginTransaction();
            try {
                //_____________Interest Table Entry
                $id = DB::table('interest_calculations')->insertGetId([
                    'start_date' => $start_date,
                    'end_date' => $paidDate,
                    'serialNo' => $ineterestNumber,
                    'membership' => $account_opening->membershipno,
                    'accountNo' => $account_opening->accountNo,
                    'memberType' => $account_opening->membertype,
                    'groupCode' => 'EXPN001',
                    'ledgerCode' => 'SAVM002',
                    'depositAmount' => 0,
                    'paid_date' => $paidDate,
                    'withdrawAmount' => $interest_amount,
                    'branchId' => session('branchId') ?: 1,
                    'agentId' => $post->user()->id,
                    'sessionId' => session('sessionId') ?: 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                //___________Member saving Table Entry
                DB::table('member_savings')->insertGetId([
                    'secheme_id' => $account_opening->accounttype,
                    'serialNo' => $ineterestNumber,
                    'accountId' => $account_opening->accountNo,
                    'accountNo' => $account_opening->membershipno,
                    'memberType' => $account_opening->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'savingNo' =>  $account_opening->accountNo,
                    'transactionDate' => $paidDate,
                    'transactionType' => 'Deposit',
                    'depositAmount' => $interest_amount,
                    'withdrawAmount' => 0,
                    'paymentType' => 'EXPN001',
                    'bank' => 'SAVM002',
                    'chequeNo' => 'Interest Received',
                    'narration' => 'Upto Date From' . $post->date_from . ' To ' . $post->date_till_date,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $post->user()->id,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                //__________________General Ledger Entries

                //__________Interest Paid Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $ineterestNumber,
                    'accountId' => $account_opening->accountNo,
                    'accountNo' => $account_opening->membershipno,
                    'memberType' => $account_opening->membertype,
                    'groupCode' => 'EXPN001',
                    'ledgerCode' => 'SAVM002',
                    'formName' => 'Interest Paid',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $paidDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $interest_amount,
                    'narration' => 'Upto Date From' . $post->date_from . ' To ' . $post->date_till_date,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Member Saving Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $ineterestNumber,
                    'accountId' => $account_opening->accountNo,
                    'accountNo' => $account_opening->membershipno,
                    'memberType' => $account_opening->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'formName' => 'Interest Paid',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $paidDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $interest_amount,
                    'narration' => 'Upto Date From' . $post->date_from . ' To ' . $post->date_till_date,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'messages' => 'Interest Paid Successfully'
                ]);
            } catch (\Exception $e) {

                DB::rollBack();

                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Some Technical Issue',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        } else {

            $interestPaidDate = DB::table('interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
                // ->select(['start_date', 'end_date','memberType'])
                ->first();

            if ($interestPaidDate) {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Interest Already Paid from ' . $post->date_from . ' To ' . $post->date_till_date
                ]);
            }


            // $closing_balances = DB::table('member_accounts')
            //     ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
            //     ->leftJoin('opening_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            //     ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.accounttype')
            //     ->where('ledger_masters.name', 'Saving')
            //     ->where('opening_accounts.membertype', $memberType)
            //     ->where('member_accounts.memberType', $memberType)
            //     ->where('member_savings.transactionDate', '<=', $endDate)
            //     ->select(
            //         'member_accounts.accountNo as membershipnumber',
            //         'member_accounts.name as customer_name',
            //         'member_savings.accountId',
            //         'opening_accounts.membershipno',
            //         'opening_accounts.accountNo',
            //         'opening_accounts.membertype',
            //         'opening_accounts.accounttype',
            //         'ledger_masters.reference_id',
            //         'ledger_masters.groupCode',
            //         'ledger_masters.ledgerCode',
            //         'ledger_masters.name'
            //     )
            //     ->selectRaw(
            //         'COALESCE(SUM(member_savings.depositAmount), 0) as totalDeposits,
            //     COALESCE(SUM(member_savings.withdrawAmount), 0) as totalWithdrawals'
            //     )
            //     ->groupBy(
            //         'member_savings.accountId',
            //         'member_accounts.accountNo',
            //         'member_accounts.name',
            //         'opening_accounts.membershipno',
            //         'opening_accounts.accountNo',
            //         'opening_accounts.membertype',
            //         'opening_accounts.accounttype',
            //         'ledger_masters.reference_id',
            //         'ledger_masters.groupCode',
            //         'ledger_masters.ledgerCode',
            //         'ledger_masters.name'
            //     )
            //     ->get();

            $closing_balances = DB::table('member_accounts')
                ->select(
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name',
                    'member_savings.accountId',
                    'member_savings.memberType',
                    'member_savings.secheme_id',
                    DB::raw('SUM(CASE WHEN member_savings.depositAmount IS NOT NULL THEN member_savings.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN member_savings.withdrawAmount IS NOT NULL THEN member_savings.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                ->where('member_savings.memberType', $memberType)
                ->whereDate('member_savings.transactionDate', '<=', $endDate)
                ->groupBy('member_savings.accountId', 'member_accounts.accountNo', 'member_accounts.name','member_savings.memberType','member_savings.secheme_id',)
                ->get();

            $savingGroupCode = '';
            $savingLedgerCode = '';

            $opening_amounts = [];
            $mainbalances = [];
            $last_balances = [];
            $account_opening = [];

            foreach ($closing_balances as $balance) {

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
                    ->where('opening_accounts.accountNo', $balance->accountId)
                    // ->where('member_accounts.memberType', $balance->memberType)
                    ->where('opening_accounts.membertype', $balance->memberType)
                    ->where('opening_accounts.accountname', 'Saving')
                    ->get();


                foreach($account_opening as $row){
                    if($row->groupCode && $row->ledgerCode){
                        $savingGroupCode = $row->groupCode;
                        $savingLedgerCode = $row->ledgerCode;
                    }else{
                        return response()->json(['status' => 'Fail','messages' => 'Saving Group/Ledger Not Found']);
                    }
                }
            }


            $results = [];
            $grandinterestAmount = 0;
            DB::beginTransaction();

            try {
                foreach ($closing_balances as $balance) {
                    $deposit_amount = $balance->deposit_total;
                    $withdraw_amount = $balance->withdraw_total;
                    $last_balance = $deposit_amount;

                    $last_balances[$balance->accountId] = $last_balance;

                    if (isset($minium_amount) && $last_balance < $minium_amount) {
                        continue;
                    }

                    $mainbalance = ($last_balance >= $minium_amount) ? $last_balance : ($minium_amount ?? 0);
                    $amount = $deposit_amount - $withdraw_amount;

                    $rate_of_intt = $post->rate_of_intt ?? 0;
                    $interest_amount = round(((($last_balance * $rate_of_intt) / 100) / 12) * $months);
                    $ineterestNumber = 'Interest' . Str::uuid();

                    //_____________Interest Table Entry
                    $id = DB::table('interest_calculations')->insertGetId([
                        'start_date' => $start_date,
                        'end_date' => $endDate,
                        'serialNo' => $ineterestNumber,
                        'membership' => $balance->membershipnumber,
                        'accountNo' => $balance->accountId,
                        'memberType' => $balance->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'SAVM002',
                        'depositAmount' => 0,
                        'paid_date' => $paidDate,
                        'withdrawAmount' => $interest_amount,
                        'branchId' => session('branchId') ?: 1,
                        'agentId' => $post->user()->id,
                        'sessionId' => session('sessionId') ?: 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    //___________Member saving Table Entry
                    DB::table('member_savings')->insertGetId([
                        'secheme_id' => $balance->secheme_id,
                        'serialNo' => $ineterestNumber,
                        'accountId' => $balance->accountId,
                        'accountNo' => $balance->membershipnumber,
                        'memberType' => $balance->memberType,
                        'groupCode' => $savingGroupCode,
                        'ledgerCode' => $savingLedgerCode,
                        'savingNo' =>  $balance->accountId,
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $interest_amount,
                        'withdrawAmount' => 0,
                        'paymentType' => 'EXPN001',
                        'bank' => 'SAVM002',
                        'chequeNo' => 'Interest Received',
                        'narration' => 'Intt. Received On Saving A/c- ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'agentId' => $post->user()->id,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    //__________________General Ledger Entries

                    //__________Interest Paid Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $ineterestNumber,
                        'accountId' => $balance->accountId,
                        'accountNo' => $balance->membershipnumber,
                        'memberType' => $balance->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'SAVM002',
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. Paid On Saving A/c- ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Member Saving Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $ineterestNumber,
                        'accountId' => $balance->accountId,
                        'accountNo' => $balance->membershipnumber,
                        'memberType' => $balance->memberType,
                        'groupCode' => $savingGroupCode,
                        'ledgerCode' => $savingLedgerCode,
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. Paid On Saving A/c- ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);
                }



                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'messages' => 'Interest Paid Successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Some Technical Issue',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        }
    }

    public function deletepaidinterest(Request $post)
    {
        $paidDate = date('Y-m-d', strtotime($post->paid_date));
        $memberType = $post->memberType;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->paid_date)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }



        DB::beginTransaction();

        try {

            GeneralLedger::where('sessionId', '=', Session::get('sessionId'))->where('memberType', $memberType)
                ->where('transactionDate', '=', $paidDate)
                ->where('formName', 'Interest Paid')
                ->forceDelete();


            MemberSaving::where('sessionId', '=', Session::get('sessionId'))
                ->where('memberType', $memberType)
                ->where('transactionDate', '=', $paidDate)
                ->where('chequeNo', 'Interest Received')
                ->forceDelete();


            InterestCalculation::where('sessionId', '=', Session::get('sessionId'))
                ->where('memberType', $memberType)
                ->where('paid_date', '=', $paidDate)
                ->forceDelete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'Fail',
                'error' => $e->getMessage(),
                'lines' => $e->getline()
            ]);
        }
    }
}
