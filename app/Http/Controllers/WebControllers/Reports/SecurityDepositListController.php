<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BranchMaster;
use App\Models\GeneralLedger;
use App\Models\SessionMaster;
use App\Models\InterstSecurityOnCommission;
use App\Models\SecuritiesSaving;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SecurityDepositListController extends Controller
{
    public function securitylistIndex()
    {
        $branch = BranchMaster::first();
        $scheme_names = DB::table('scheme_masters')->orderBy('name', 'ASC')->get();
        $data['branch'] = $branch;
        return view('report.securitydepositlist', $data);
    }


    public function getsecurityoncomminterestcaluclation(Request $post)
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

        $start_date = date('Y-m-d', strtotime($post->date_from));
        $endDate = date('Y-m-d', strtotime($post->date_till_date));

        $memberType = $post->member_type;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($start_date < $session_master->startDate || $endDate > $session_master->endDate) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

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

        if ($session_startDate > $endDate || $session_endDate < $start_date) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        $account_no = $post->account_no;
        $minimum_amount = $post->minimum_amount;


        //_________________Get Months waise Calculations
        $months = 0;
        $start_date_month = date('d-m-Y', strtotime($post->date_from));
        $endDate_month = date('d-m-Y', strtotime($post->date_till_date));
        if ($start_date_month && $endDate_month) {
            $start = DateTime::createFromFormat('d-m-Y', $start_date_month);
            $end = DateTime::createFromFormat('d-m-Y', $endDate_month);
            $interval = $start->diff($end);
            $months = ($interval->y * 12) + $interval->m + 1;
        }

        if ($account_no) {

            $interestPaidDate = DB::table('securityoncomm_interest_calculations')
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
                    'securities_saving.account_no',
                    DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('securities_saving', 'member_accounts.accountNo', '=', 'securities_saving.account_no')
                ->where('securities_saving.account_no', $account_no)
                ->where('securities_saving.type', $memberType)
                ->whereDate('securities_saving.transactionDate', '<', $endDate)
                ->groupBy('securities_saving.account_no', 'member_accounts.accountNo', 'member_accounts.name')
                ->first();

            $current_period_deposit = $closingbalance->deposit_total - $closingbalance->withdraw_total;
            $last_balance = $current_period_deposit;
            $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
            $rate_of_intt = $post->rate_of_intt;
            $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
            $net_amount = $last_balance + $interest_amount;

            $results[] = [
                'membershipnumber' => $closingbalance->membershipnumber,
                'name' => $closingbalance->name,
                // 'opening_balance' => $opening_balance ?? 0,
                // 'transactions' => $closingbalance->,
                'last_balance' => $last_balance,
                'interest_amount' => $interest_amount,
                'net_amount' => $net_amount
            ];

            return response()->json([
                'status' => 'success',
                'balances' => $results,
            ]);
        } else {

            $interestPaidDate = DB::table('securityoncomm_interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
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
                    'securities_saving.account_no',
                    DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('securities_saving', 'member_accounts.accountNo', '=', 'securities_saving.account_no')
                ->where('securities_saving.type', $memberType)
                ->whereDate('securities_saving.transactionDate', '<', $endDate)
                ->orderBy('member_accounts.accountNo', 'ASC')
                ->groupBy('securities_saving.account_no', 'member_accounts.accountNo', 'member_accounts.name')
                ->get();


            $last_balances = [];
            $results = [];
            $amount = 0;
            $interest_amount = 0;

            foreach ($closingbalance as $balance) {
                $deposit_amount = $balance->deposit_total;
                $withdraw_amount = $balance->withdraw_total;
                $last_balance = $deposit_amount;

                [$balance->account_no] = $last_balance;

                if (isset($minimum_amount) && $last_balance < $minimum_amount) {
                    continue;
                }

                $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
                $amount = $deposit_amount - $withdraw_amount;

                $rate_of_intt = $post->rate_of_intt ?? 0;
                $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
                $net_amount = $amount + $interest_amount;

                $results[] = [
                    'membershipnumber' => $balance->account_no ?? $balance->membershipnumber,
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
    }


    public function paidsecurityoncomminterest(Request $post)
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

        $start_date = date('Y-m-d', strtotime($post->date_from));
        $endDate = date('Y-m-d', strtotime($post->date_till_date));

        $memberType = $post->memberType;
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($start_date < $session_master->startDate || $endDate > $session_master->endDate) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

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

        if ($session_startDate > $endDate || $session_endDate < $start_date) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        $account_no = $post->account_no;
        $minimum_amount = $post->minimum_amount;


        //_________________Get Months waise Calculations
        $months = 0;
        $start_date_month = date('d-m-Y', strtotime($post->date_from));
        $endDate_month = date('d-m-Y', strtotime($post->date_till_date));
        if ($start_date_month && $endDate_month) {
            $start = DateTime::createFromFormat('d-m-Y', $start_date_month);
            $end = DateTime::createFromFormat('d-m-Y', $endDate_month);
            $interval = $start->diff($end);
            $months = ($interval->y * 12) + $interval->m + 1;
        }

        $paidDate = date('Y-m-d', strtotime($post->paid_date));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($paidDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }



        if ($account_no) {

            $interestPaidDate = DB::table('securityoncomm_interest_calculations')
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
                    'securities_saving.account_no',
                    DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('securities_saving', 'member_accounts.accountNo', '=', 'securities_saving.account_no')
                ->where('securities_saving.account_no', $account_no)
                ->where('securities_saving.type', $memberType)
                ->whereDate('securities_saving.transactionDate', '<', $endDate)
                ->groupBy('securities_saving.account_no', 'member_accounts.accountNo', 'member_accounts.name')
                ->first();

            $current_period_deposit = $closingbalance->deposit_total - $closingbalance->withdraw_total;
            $last_balance = $current_period_deposit;
            $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
            $rate_of_intt = $post->rate_of_intt;
            $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
            $net_amount = $last_balance + $interest_amount;

            $accountDetails = DB::table('security_on_commission_account')->where('account_no', $account_no)->where('memberType', $memberType)->first();

            $ineterestNumber = 'Interest' . Str::uuid();

            if (!empty($accountDetails)) {

                $current_period_deposit = 0;
                if ($closingbalance) {
                    $current_period_deposit = $closingbalance->deposit_total - $closingbalance->withdraw_total;
                    $last_balance = $current_period_deposit;
                    $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
                    $rate_of_intt = $post->rate_of_intt;
                    $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
                    $net_amount = $last_balance + $interest_amount;

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
                    $current_period_deposit = 0;
                }



                DB::beginTransaction();
                try {
                    //_____________Interest Table Entry
                    $id = DB::table('securityoncomm_interest_calculations')->insertGetId([
                        'start_date' => $start_date,
                        'end_date' => $endDate,
                        'serialNo' => $ineterestNumber,
                        'membership' => $accountDetails->account_no,
                        'accountNo' => $accountDetails->account_no,
                        'memberType' => $accountDetails->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'INT01',
                        'depositAmount' => 0,
                        'paid_date' => $paidDate,
                        'withdrawAmount' => $interest_amount,
                        'branchId' => session('branchId') ?: 1,
                        'agentId' => $post->user()->id,
                        'sessionId' => session('sessionId') ?: 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    //___________Member Security Account Table Entry
                    DB::table('securities_saving')->insertGetId([
                        'serialNo' => $ineterestNumber,
                        'transactionDate' => $paidDate,
                        'staff_no' => $accountDetails->account_no,
                        'account_no' => $accountDetails->account_no,
                        'type' => $accountDetails->memberType,
                        'groupCode' => $accountDetails->groupCode,
                        'ledgerCode' => $accountDetails->ledgerCode,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $interest_amount,
                        'withdrawAmount' => 0,
                        'paymentType' => '',
                        'bank' => '',
                        'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $accountDetails->account_no . ' ' . $post->date_from . ' To ' . $post->date_till_date,
                        'chequeNo' => 'Interest Received',
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
                        'accountId' => $accountDetails->account_no,
                        'accountNo' => $accountDetails->account_no,
                        'memberType' => $accountDetails->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'INT01',
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $accountDetails->account_no . ' ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Member Security A/c Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $ineterestNumber,
                        'accountId' => $accountDetails->account_no,
                        'accountNo' => $accountDetails->account_no,
                        'memberType' => $accountDetails->memberType,
                        'groupCode' => $accountDetails->groupCode,
                        'ledgerCode' => $accountDetails->ledgerCode,
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. Paid On Saving A/c- ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => $post->agents,
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
                    ]);
                }
            }
        } else {



            $interestPaidDate = DB::table('securityoncomm_interest_calculations')
                ->whereDate('start_date', '=', $start_date)
                ->whereDate('end_date', '=', $endDate)
                ->where('memberType', $memberType)
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
                    'securities_saving.account_no',
                    'security_on_commission_account.staff_no',
                    'security_on_commission_account.groupCode',
                    'security_on_commission_account.ledgerCode',
                    'security_on_commission_account.status',
                    'security_on_commission_account.agentid',
                    'security_on_commission_account.memberType',
                    DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_total'),
                    DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_total')
                )
                ->leftJoin('securities_saving', 'member_accounts.accountNo', '=', 'securities_saving.account_no')
                ->leftJoin('security_on_commission_account', 'member_accounts.accountNo', '=', 'security_on_commission_account.staff_no')
                ->where('securities_saving.type', $memberType)
                ->whereDate('securities_saving.transactionDate', '<', $endDate)
                ->orderBy('member_accounts.accountNo', 'ASC')
                ->groupBy(
                    'securities_saving.account_no',
                    'member_accounts.accountNo',
                    'member_accounts.name',
                    'security_on_commission_account.staff_no',
                    'security_on_commission_account.groupCode',
                    'security_on_commission_account.ledgerCode',
                    'security_on_commission_account.status',
                    'security_on_commission_account.agentid',
                    'security_on_commission_account.memberType',
                )
                ->get();

            $last_balances = [];
            $results = [];
            $amount = 0;
            $interest_amount = 0;


            DB::beginTransaction();
            try {

                foreach ($closingbalance as $balance) {
                    $ineterestNumber = 'Interest' . Str::uuid();


                    $deposit_amount = $balance->deposit_total;
                    $withdraw_amount = $balance->withdraw_total;
                    $last_balance = $deposit_amount;



                    [$balance->account_no] = $last_balance;

                    if (isset($minimum_amount) && $last_balance < $minimum_amount) {
                        continue;
                    }

                    $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
                    $amount = $deposit_amount - $withdraw_amount;

                    $rate_of_intt = $post->rate_of_intt ?? 0;
                    $interest_amount = round(((($mainbalance * $rate_of_intt) / 100) / 12) * $months);
                    $net_amount = $amount + $interest_amount;



                    //_____________Interest Table Entry
                    $id = DB::table('securityoncomm_interest_calculations')->insertGetId([
                        'start_date' => $start_date,
                        'end_date' => $endDate,
                        'serialNo' => $ineterestNumber,
                        'membership' => $balance->staff_no,
                        'accountNo' => $balance->staff_no,
                        'memberType' => $balance->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'INT01',
                        'depositAmount' => 0,
                        'paid_date' => $paidDate,
                        'withdrawAmount' => $interest_amount,
                        'branchId' => session('branchId') ?: 1,
                        'agentId' => $post->user()->id,
                        'sessionId' => session('sessionId') ?: 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    //___________Member Security Account Table Entry
                    DB::table('securities_saving')->insertGetId([
                        'serialNo' => $ineterestNumber,
                        'transactionDate' => $paidDate,
                        'staff_no' => $balance->staff_no,
                        'account_no' => $balance->staff_no,
                        'type' => $balance->memberType,
                        'groupCode' => $balance->groupCode,
                        'ledgerCode' => $balance->ledgerCode,
                        'transactionType' => 'Deposit',
                        'depositAmount' => $interest_amount,
                        'withdrawAmount' => 0,
                        'paymentType' => '',
                        'bank' => '',
                        'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $balance->staff_no . ' ' . $post->date_from . ' To ' . $post->date_till_date,
                        'chequeNo' => 'Interest Received',
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
                        'accountId' => $balance->staff_no,
                        'accountNo' => $balance->staff_no,
                        'memberType' => $balance->memberType,
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'INT01',
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. On Sec.On Comm. A/c-  ' . $balance->staff_no . ' ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => '',
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Member Security A/c Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $ineterestNumber,
                        'accountId' => $balance->staff_no,
                        'accountNo' => $balance->staff_no,
                        'memberType' => $balance->memberType,
                        'groupCode' => $balance->groupCode,
                        'ledgerCode' => $balance->ledgerCode,
                        'formName' => 'Interest Paid',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $paidDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'Intt. Paid On Saving A/c- ' . $post->date_from . ' To ' . $post->date_till_date,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => $post->agents,
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
                    'liens' => $e->getLine()
                ]);
            }
        }
    }


    public function deletepaidsecuritycomminterest(Request $post)
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



            SecuritiesSaving::where('sessionId', '=', Session::get('sessionId'))
                ->where('type', $memberType)
                ->where('transactionDate', '=', $paidDate)
                ->where('chequeNo', 'Interest Received')
                ->forceDelete();


            InterstSecurityOnCommission::where('sessionId', '=', Session::get('sessionId'))
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


    public function securitydepositlist()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.securitylist', $data);
    }

    public function getsecuritylist(Request $post)
    {
        $rules = [
            "endDate" => "required",
            "memberType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $memberType = $post->memberType;
        $endDate = date('Y-m-d', strtotime($post->endDate));

        $closingbalance = DB::table('member_accounts')
            ->select(
                'member_accounts.accountNo as membershipnumber',
                'member_accounts.name',
                'securities_saving.account_no',
                DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_total'),
                DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_total')
            )
            ->leftJoin('securities_saving', 'member_accounts.accountNo', '=', 'securities_saving.account_no')
            ->whereDate('securities_saving.transactionDate', '<=', $endDate);


        if ($memberType != 'all') {
            $closingbalance->whereIn('securities_saving.type', (array)$memberType);
        }

        $closingbalance = $closingbalance
            ->groupBy('securities_saving.account_no', 'member_accounts.accountNo', 'member_accounts.name')
            ->get();

        if ($closingbalance->isEmpty()) {
            return response()->json(['status' => 'Fail', 'message' => 'No records found.']);
        }

        return response()->json(['status' => 'success', 'allDateils' => $closingbalance]);
    }



















    // public function getsecuritydepositlist(Request $post){
    //     $date = date('Y-m-d',strtotime($post->endDate));
    //     $memberType = $post->memberType;

    //     if($memberType === 'all'){
    //         $securityDeposit = DB::table('securities_saving')
    //             ->select(
    //                 'securities_saving.account_no',
    //                 'securities_saving.staff_no',
    //                 'securities_saving.type',
    //                 'securities_saving.transactionType',
    //                 'member_accounts.accountNo',
    //                 'member_accounts.name as names',
    //                 DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_amount'),
    //                 DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_amount')
    //             )
    //             ->leftJoin('member_accounts', function ($join) {
    //                 $join->on(DB::raw('member_accounts.accountNo COLLATE utf8mb4_unicode_ci'), '=', DB::raw('securities_saving.account_no COLLATE utf8mb4_unicode_ci'))
    //                     ->whereColumn(DB::raw('member_accounts.memberType COLLATE utf8mb4_unicode_ci'), '=', DB::raw('securities_saving.type COLLATE utf8mb4_unicode_ci'));
    //             })
    //             ->where('securities_saving.transactionDate', '<=', $date)
    //             ->groupBy(
    //                 'securities_saving.account_no',
    //                 'securities_saving.staff_no',
    //                 'securities_saving.type',
    //                 'securities_saving.transactionType',
    //                 'member_accounts.accountNo',
    //                 'member_accounts.name'
    //             )
    //             ->get();

    //         if(!empty($securityDeposit)){
    //             return response()->json(['status' => 'success','securitylist' => $securityDeposit]);
    //         }else{
    //             return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
    //         }

    //     }else{
    //         $securityDeposit = DB::table('securities_saving')
    //             ->select(
    //                 'securities_saving.account_no',
    //                 'securities_saving.staff_no',
    //                 'securities_saving.type',
    //                 'securities_saving.transactionType',
    //                 'member_accounts.accountNo',
    //                 'member_accounts.name as names',
    //                 DB::raw('SUM(CASE WHEN securities_saving.depositAmount IS NOT NULL THEN securities_saving.depositAmount ELSE 0 END) as deposit_amount'),
    //                 DB::raw('SUM(CASE WHEN securities_saving.withdrawAmount IS NOT NULL THEN securities_saving.withdrawAmount ELSE 0 END) as withdraw_amount')
    //             )
    //             ->leftJoin('member_accounts', function ($join) {
    //                 $join->on(DB::raw('member_accounts.accountNo COLLATE utf8mb4_unicode_ci'), '=', DB::raw('securities_saving.account_no COLLATE utf8mb4_unicode_ci'))
    //                     ->whereColumn(DB::raw('member_accounts.memberType COLLATE utf8mb4_unicode_ci'), '=', DB::raw('securities_saving.type COLLATE utf8mb4_unicode_ci'));
    //             })
    //             ->where('securities_saving.transactionDate', '<=', $date)
    //             ->where('securities_saving.type',$memberType)
    //             ->groupBy(
    //                 'securities_saving.account_no',
    //                 'securities_saving.staff_no',
    //                 'securities_saving.type',
    //                 'securities_saving.transactionType',
    //                 'member_accounts.accountNo',
    //                 'member_accounts.name'
    //             )
    //             ->get();

    //         if(!empty($securityDeposit)){
    //             return response()->json(['status' => 'success','securitylist' => $securityDeposit]);
    //         }else{
    //             return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
    //         }
    //     }
    // }
}
