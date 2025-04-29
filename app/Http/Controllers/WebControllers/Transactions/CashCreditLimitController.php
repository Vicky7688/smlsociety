<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SessionMaster;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;


class CashCreditLimitController extends Controller
{
    public function cclIndex()
    {
        $sodDatetails = DB::table('sod_masters')->where('status', 'Active')->orderBy('id', 'ASC')->get();
        $data['sodDatetails'] = $sodDatetails;
        return view('transaction.ccl.ccl', $data);
    }

    public function getcclmebershipnumber(Request $post)
    {
        $memberType = $post->memberType;
        $membershipNumber = $post->memNumber;

        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', 'LIKE', $membershipNumber . '%')->where('status', 'Active')->get();

        if (!empty($allmemberlist)) {
            return response()->json(['status' => 'success', 'allmemberlist' => $allmemberlist]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
        }
    }

    public function getmemberccl(Request $post)
    {
        $memberType = $post->memberType;
        $membershipNumber = $post->selectedAccount;

        $memberdetail = DB::table('member_accounts')
            ->where('memberType', $memberType)
            ->where('accountNo', 'LIKE', $membershipNumber . '%')
            ->where('status', 'Active')
            ->first();

        $ccl_aacount = DB::table('member_ccl')
            ->where(['memberType' => $memberType, 'membership' => $membershipNumber])
            ->get();


        if (!empty($memberdetail)) {
            return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
        }
    }

    public function getdepositlist(Request $post)
    {
        $rules = [
            'memberType' => 'required',
            'memNumber' => 'required|numeric',
            'loantype' => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $memberType = $post->memberType;
        $membershipNumber = $post->memNumber;
        $depositType = $post->loantype;
        $opening_date = date('Y-m-d', strtotime($post->opening_date));


        $memberdetail = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', 'LIKE', $membershipNumber . '%')->where('status', 'Active')->first();


        switch ($depositType) {
            case "FD":

                $deposit_details = DB::table('member_fds_scheme')
                    ->join('scheme_masters', 'member_fds_scheme.secheme_id', '=', 'scheme_masters.id')
                    ->select('member_fds_scheme.*', 'scheme_masters.name as schemname')
                    ->where('member_fds_scheme.membershipno', '=', $membershipNumber)
                    ->where('member_fds_scheme.memberType', '=', $memberType)
                    ->whereDate('member_fds_scheme.openingDate', '<=', $opening_date)
                    ->where('member_fds_scheme.status', 'Active')
                    ->get();

                $maxInterestRate = $deposit_details->max('interestRate');


                if (!empty($memberdetail) && !empty($deposit_details)) {
                    return response()->json([
                        'status' => 'success',
                        'memberdetail' => $memberdetail,
                        'deposit_details' => $deposit_details,
                        'type' => $depositType,
                        'maxinterest' => $maxInterestRate
                    ]);
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                }

                break;


            case "RD":

                $deposit_details = DB::table('re_curring_rds')
                    ->join('scheme_masters', 're_curring_rds.secheme_id', '=', 'scheme_masters.id')
                    ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                    ->select(
                        're_curring_rds.id',
                        're_curring_rds.rd_account_no',
                        'scheme_masters.name as schemname',
                        're_curring_rds.status',
                        're_curring_rds.amount',
                        're_curring_rds.memberType',
                        DB::raw('SUM(rd_receiptdetails.amount) as fetchamount')
                    )
                    ->where('re_curring_rds.accountNo', '=', $membershipNumber)
                    ->where('re_curring_rds.memberType', '=', $memberType)
                    ->where('re_curring_rds.date', '<=', $opening_date)
                    ->where('re_curring_rds.status', 'Active')
                    ->groupBy('re_curring_rds.id', 're_curring_rds.rd_account_no', 'scheme_masters.name', 're_curring_rds.status', 're_curring_rds.amount', 're_curring_rds.memberType',)
                    ->get();

                $maxInterestRate = $deposit_details->max('interest');


                if (!empty($memberdetail) && !empty($deposit_details)) {
                    return response()->json([
                        'status' => 'success',
                        'memberdetail' => $memberdetail,
                        'deposit_details' => $deposit_details,
                        'type' => $depositType,
                        'maxinterest' => $maxInterestRate
                    ]);
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                }


                break;

            default:

                $deposit_details = DB::table('daily_collections')
                    ->join('scheme_masters', 'daily_collections.schemeid', '=', 'scheme_masters.id')
                    ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
                    ->select(
                        'daily_collections.id',
                        'daily_collections.account_no',
                        'scheme_masters.name as schemname',
                        'daily_collections.status',
                        'daily_collections.amount',
                        DB::raw('SUM(daily_collectionsavings.deposit) as deposit_amount')
                    )
                    ->where('daily_collections.membershipno', '=', $membershipNumber)
                    ->where('daily_collections.memberType', '=', $memberType)
                    ->where('daily_collections.opening_date', '<=', $opening_date)
                    ->where('daily_collections.status', 'Active')
                    ->groupBy('daily_collections.id', 'daily_collections.account_no', 'scheme_masters.name', 'daily_collections.status', 'daily_collections.amount')
                    ->get();

                $maxInterestRate = $deposit_details->max('interest');


                if (!empty($memberdetail) && !empty($deposit_details)) {
                    return response()->json([
                        'status' => 'success',
                        'memberdetail' => $memberdetail,
                        'deposit_details' => $deposit_details,
                        'type' => $depositType,
                        'maxinterest' => $maxInterestRate
                    ]);
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                }
        }
    }

    public function checkalreadyaccount(Request $post)
    {
        $rules = [
            "cclAccount" => "required",
            "memberType" => "required",
            "memNumber" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $exits_account = DB::table('member_ccl')
            ->select('member_ccl.*', 'member_accounts.accountNo', 'member_accounts.name')
            ->leftJoin('member_accounts', function ($join) use ($post) {
                $join->on('member_accounts.accountNo', '=', 'member_ccl.membership')
                    ->where('member_accounts.memberType', '=', $post->memberType);
            })
            ->where([
                'member_ccl.memberType' => $post->memberType,
                'member_ccl.membership' => $post->memNumber,
                'member_ccl.cclNo' => $post->cclAccount
            ])
            ->first();


        if ($exits_account) {
            return response()->json(['status' => 'Fail', 'messages' => 'Account No Already Taken', 'exits_account' => $exits_account]);
        } else {
            return response()->json(['status' => 'success', 'messages' => 'This Account Not Taken Any Member']);
        }
    }

    public function ccladvancementinsert(Request $post)
    {
        $rules = [
            "cclmember" => "required",
            "ccl_memnumber" => "required",
            "ccltype" => "required",
            "depositids" => "required",
            "transcationDate" => "required",
            "deposit_amount" => "required",
            "loan_amount" => "required",
            "rate_of_interest" => "required",
            "ccl_acc_no" => 'required|numeric',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Check All Fields']);
        }

        $deposit_ids = $post->depositids;


        $session_master = SessionMaster::find(Session::get('sessionId'));


        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $date = date('Y-m-d', strtotime($post->transcationDate));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transcationDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }




        // $deposit_ids = '';

        $type = $post->ccltype;

        $fdsaccounts = '';
        $rdaccounts = '';
        $dailyaccounts = '';

        $fd_amount = 0;
        $rd_amount = 0;
        $dailyamount = 0;


        if ($type === 'FD') {
            $fdsaccounts = $post->depositids;
            $fd_amount = $post->deposit_amount;
        } elseif ($type === 'RD') {

            $rdaccounts = $post->depositids;
            $rd_amount = $post->deposit_amount;
        } elseif ($type === 'DailyDeposit') {

            $dailyaccounts = $post->depositids;
            $dailyamount = $post->deposit_amount;
        }

        $groupCode = '';
        $ledgerCode = '';
        $interestGroup = '';
        $interestLedger = '';



        $memberType = $post->cclmember;

        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $serialNo = 'CCL' . time();

        $endDate = date('Y-m-d', strtotime($post->end_date));

        DB::beginTransaction();
        try {
            $id = DB::table('member_ccl')->insertGetId([
                'ccl_Date' => $date,
                'serialNo' => $serialNo,
                'memberType' => $memberType,
                'membership' => $post->ccl_memnumber,
                'cclNo' => $post->ccl_acc_no,
                'ccl_end_Date' => $endDate,
                'groupCode' =>  $groupCode,
                'ledgerCode' => $ledgerCode,
                'year' => $post->year,
                'month' => $post->months,
                'days' => $post->days,
                'interestType' => $post->interest_type,
                'interest' => $post->rate_of_interest,
                'fdId' => $fdsaccounts,
                'Types' => $post->ccltype,
                'fdAmount' => $fd_amount,
                'rd_id' => $rdaccounts,
                'rd_aacount' => $rd_amount,
                'dailyId' => $dailyaccounts,
                'daily_amount' => $dailyamount,
                'ccl_amount' => $post->loan_amount,
                'narration' => $post->narration,
                'status' => 'Disbursed',
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
            ]);

            //____________General Ledger Entry

            //_________________CCL Entry
            // GeneralLedger::insert([
            //     'serialNo' => $serialNo,
            //     'accountId' => $post->ccl_acc_no,
            //     'accountNo' => $post->ccl_memnumber,
            //     'memberType' => $memberType,
            //     'groupCode' => $groupCode,
            //     'ledgerCode' => $ledgerCode,
            //     'formName' => "CCL-Advancement",
            //     'referenceNo' => $id,
            //     'transactionDate' => $date,
            //     'transactionType' => "Dr",
            //     'transactionAmount' => $post->loan_amount,
            //     'branchId' => session('branchId') ? session('branchId') : 1,
            //     // 'agentId' => '',
            //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
            //     'updatedBy' => $post->user()->id,
            // ]);


            //___________Cash Entry
            // GeneralLedger::insert([
            //     'serialNo' => $serialNo,
            //     'accountId' => $post->ccl_acc_no,
            //     'accountNo' => $post->ccl_memnumber,
            //     'memberType' => $memberType,
            //     'groupCode' => 'C002',
            //     'ledgerCode' => 'C002',
            //     'formName' => "CCL-Advancement",
            //     'referenceNo' => $id,
            //     'transactionDate' => $date,
            //     'transactionType' => "Dr",
            //     'transactionAmount' => $post->loan_amount,
            //     'branchId' => session('branchId') ? session('branchId') : 1,
            //     // 'agentId' => '',
            //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
            //     'updatedBy' => $post->user()->id,
            // ]);


            $deposittypes = $post->ccltype;
            $deposit_ids = explode(',', $deposit_ids);
            if ($deposittypes === 'FD') {
                DB::table('member_fds_scheme')
                    ->whereIn('id', $deposit_ids)
                    ->update(['status' => 'Pluge']);
            } elseif ($deposittypes === 'RD') {
                DB::table('re_curring_rds')
                    ->whereIn('id', $deposit_ids)
                    ->update(['status' => 'Pluge']);
            } elseif ($deposittypes === 'DailyDeposit') {
                DB::table('daily_collections')
                    ->whereIn('id', $deposit_ids)
                    ->update(['status' => 'Pluge']);
            }


            DB::commit();

            $memberType = $post->cclmember;
            $membershipNumber = $post->ccl_memnumber;

            $memberdetail = DB::table('member_accounts')
                ->where('memberType', $memberType)
                ->where('accountNo', 'LIKE', $membershipNumber . '%')
                ->where('status', 'Active')
                ->first();

            $ccl_aacount = DB::table('member_ccl')
                ->where(['memberType' => $memberType, 'membership' => $membershipNumber])
                ->get();

            if (!empty($memberdetail)) {
                return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'Fail',
                'messages' => 'An error occurred while Entered Record',
                'error' => $e->getMessage(),
                'lines' => $e->getLine()
            ]);
        }
    }

    public function editccldetails(Request $post)
    {

        $session_master = SessionMaster::find(Session::get('sessionId'));
        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }


        $id = $post->id;
        $exitsId = DB::table('member_ccl')->where('id', $id)->first();

        $cclWithdrawAmount = DB::table('ccl_payments')->where('ccl_Id', $exitsId->id)->first();


        if (!empty($cclWithdrawAmount)) {
            return response()->json(['status' => 'Fail', 'messages' => 'The CCL limit Has Using Amounts.']);
        }

        $memberdetail = DB::table('member_accounts')->where('memberType', $exitsId->memberType)->where('accountNo', 'LIKE', $exitsId->membership . '%')->where('status', 'Active')->first();

        $opening_date = date('Y-m-d', strtotime($post->opening_date));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->opening_date)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }


        if ($exitsId) {
            $depositTypes = $exitsId->Types;

            $fds = 0;
            switch ($depositTypes) {
                case "FD":

                    $fdid = explode(',', $exitsId->fdId);

                    $deposit_details = DB::table('member_fds_scheme')
                        ->join('scheme_masters', 'member_fds_scheme.secheme_id', '=', 'scheme_masters.id')
                        ->select('member_fds_scheme.*', 'scheme_masters.name as schemname')
                        ->where('member_fds_scheme.membershipno', '=', $exitsId->membership)
                        ->where('member_fds_scheme.memberType', '=', $exitsId->memberType)
                        ->whereDate('member_fds_scheme.openingDate', '<=', $opening_date)
                        ->whereIn('member_fds_scheme.status', ['Active', 'Pluge'])
                        ->get();

                    $maxInterestRate = $deposit_details->max('interestRate');

                    if (!empty($memberdetail) && !empty($deposit_details)) {
                        return response()->json([
                            'status' => 'success',
                            'memberdetail' => $memberdetail,
                            'deposit_details' => $deposit_details,
                            'type' => $exitsId->Types,
                            'exitsId' => $exitsId,
                            'maxinterest' => $maxInterestRate
                        ]);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                    }

                    break;

                case "RD":

                    $rd_id = explode(',', $exitsId->rd_id);

                    $deposit_details = DB::table('re_curring_rds')
                        ->join('scheme_masters', 're_curring_rds.secheme_id', '=', 'scheme_masters.id')
                        ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                        ->select(
                            're_curring_rds.id',
                            're_curring_rds.rd_account_no',
                            'scheme_masters.name as schemname',
                            're_curring_rds.status',
                            're_curring_rds.amount',
                            're_curring_rds.memberType',
                            DB::raw('SUM(rd_receiptdetails.amount) as fetchamount')
                        )
                        ->where('re_curring_rds.accountNo', '=', $exitsId->membership)
                        ->where('re_curring_rds.memberType', '=', $exitsId->memberType)
                        ->where('re_curring_rds.date', '<=', $opening_date)
                        ->whereIn('re_curring_rds.status', ['Active', 'Pluge'])
                        ->groupBy('re_curring_rds.id', 're_curring_rds.rd_account_no', 'scheme_masters.name', 're_curring_rds.status', 're_curring_rds.amount', 're_curring_rds.memberType',)
                        ->get();


                    $maxInterestRate = $deposit_details->max('interest');

                    if (!empty($memberdetail) && !empty($deposit_details)) {
                        return response()->json([
                            'status' => 'success',
                            'memberdetail' => $memberdetail,
                            'deposit_details' => $deposit_details,
                            'type' => $exitsId->Types,
                            'exitsId' => $exitsId,
                            'maxinterest' => $maxInterestRate
                        ]);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                    }

                    break;

                case "DailyDeposit":

                    $dailyid = explode(',', $exitsId->dailyId);

                    // $deposit_details = DB::table('daily_collections')->whereIn('id', $dailyid)->whereIn('status', ['Active','Pluge'])->update(['status' => 'Active']);

                    $deposit_details = DB::table('daily_collections')
                        ->join('scheme_masters', 'daily_collections.schemeid', '=', 'scheme_masters.id')
                        ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
                        ->select(
                            'daily_collections.id',
                            'daily_collections.account_no',
                            'scheme_masters.name as schemname',
                            'daily_collections.status',
                            'daily_collections.amount',
                            DB::raw('SUM(daily_collectionsavings.deposit) as deposit_amount')
                        )
                        ->where('daily_collections.membershipno', '=', $exitsId->membership)
                        ->where('daily_collections.memberType', '=', $exitsId->memberType)
                        ->whereIn('daily_collections.status', ['Active', 'Pluge'])
                        ->where('daily_collections.opening_date', '<=', $opening_date)
                        ->groupBy('daily_collections.id', 'daily_collections.account_no', 'scheme_masters.name', 'daily_collections.status', 'daily_collections.amount')
                        ->get();

                    $maxInterestRate = $deposit_details->max('interest');



                    if (!empty($memberdetail) && !empty($deposit_details)) {
                        return response()->json([
                            'status' => 'success',
                            'memberdetail' => $memberdetail,
                            'deposit_details' => $deposit_details,
                            'type' => $exitsId->Types,
                            'exitsId' => $exitsId,
                            'maxinterest' => $maxInterestRate
                        ]);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
                    }

                    break;

                default:
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    break;
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function deletecclaccount(Request $post)
    {
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $id = $post->id;
        $exitsId = DB::table('member_ccl')->where('id', $id)->first();

        $cclWithdrawAmount = DB::table('ccl_payments')->where('ccl_Id', $exitsId->id)->first();

        if (!empty($cclWithdrawAmount)) {
            return response()->json(['status' => 'Fail', 'messages' => 'The CCL limit Has Using Amounts.']);
        }

        $opening_date = date('Y-m-d', strtotime($post->opening_date));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->opening_date)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }



        if ($exitsId) {
            $depositTypes = $exitsId->Types;

            $fds = 0;
            switch ($depositTypes) {
                case "FD":

                    $fdid = explode(',', $exitsId->fdId);

                    DB::beginTransaction();
                    try {

                        DB::table('member_fds_scheme')->whereIn('id', $fdid)->where('status', 'Pluge')->update(['status' => 'Active']);
                        // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                        DB::table('member_ccl')->where('id', $id)->delete();

                        DB::commit();

                        $memberdetail = DB::table('member_accounts')
                            ->where('memberType', $exitsId->memberType)
                            ->where('accountNo', 'LIKE', $exitsId->membership . '%')
                            ->where('status', 'Active')
                            ->first();

                        $ccl_aacount = DB::table('member_ccl')
                            ->where(['memberType' => $exitsId->memberType, 'membership' => $exitsId->membership])
                            ->get();

                        if (!empty($memberdetail)) {
                            return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                        } else {
                            return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }

                    break;

                case "RD":

                    $rd_id = explode(',', $exitsId->rd_id);


                    DB::beginTransaction();
                    try {

                        DB::table('re_curring_rds')->whereIn('id', $rd_id)->where('status', 'Pluge')->update(['status' => 'Active']);
                        // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                        DB::table('member_ccl')->where('id', $id)->delete();

                        DB::commit();

                        $memberdetail = DB::table('member_accounts')
                            ->where('memberType', $exitsId->memberType)
                            ->where('accountNo', 'LIKE', $exitsId->membership . '%')
                            ->where('status', 'Active')
                            ->first();

                        $ccl_aacount = DB::table('member_ccl')
                            ->where(['memberType' => $exitsId->memberType, 'membership' => $exitsId->membership])
                            ->get();

                        if (!empty($memberdetail)) {
                            return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                        } else {
                            return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }



                    break;

                case "DailyDeposit":

                    $dailyid = explode(',', $exitsId->dailyId);


                    DB::beginTransaction();
                    try {

                        DB::table('daily_collections')->whereIn('id', $dailyid)->where('status', 'Pluge')->update(['status' => 'Active']);
                        // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                        DB::table('member_ccl')->where('id', $id)->delete();

                        DB::commit();

                        $memberdetail = DB::table('member_accounts')
                            ->where('memberType', $exitsId->memberType)
                            ->where('accountNo', 'LIKE', $exitsId->membership . '%')
                            ->where('status', 'Active')
                            ->first();

                        $ccl_aacount = DB::table('member_ccl')
                            ->where(['memberType' => $exitsId->memberType, 'membership' => $exitsId->membership])
                            ->get();

                        if (!empty($memberdetail)) {
                            return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                        } else {
                            return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }
                    break;

                default:
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    break;
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function ccladvancementupdate(Request $post)
    {
        $rules = [
            "cclmember" => "required",
            "ccl_memnumber" => "required",
            "ccltype" => "required",
            // "depositids" => "required",
            "transcationDate" => "required",
            "deposit_amount" => "required",
            "loan_amount" => "required",
            "rate_of_interest" => "required",
            "ccl_acc_no" => 'required|numeric',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $deposit_ids = $post->depositids;


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $date = date('Y-m-d', strtotime($post->transcationDate));

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transcationDate)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }


        // $deposit_ids = '';

        $type = $post->ccltype;



        $fdsaccounts = '';
        $rdaccounts = '';
        $dailyaccounts = '';

        $fd_amount = 0;
        $rd_amount = 0;
        $dailyamount = 0;


        if ($type === 'FD') {
            $fdsaccounts = $post->depositids;
            $fd_amount = $post->deposit_amount;
        } elseif ($type === 'RD') {

            $rdaccounts = $post->depositids;
            $rd_amount = $post->deposit_amount;
        } elseif ($type === 'DailyDeposit') {

            $dailyaccounts = $post->depositids;
            $dailyamount = $post->deposit_amount;
        }

        $groupCode = '';
        $ledgerCode = '';
        $interestGroup = '';
        $interestLedger = '';



        $memberType = $post->cclmember;

        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $serialNo = 'CCL' . time();
        $updated_id = $post->cclupdateId;

        $exitsId = DB::table('member_ccl')->where('id', $updated_id)->where('status', 'Disbursed')->first();

        $endDate = date('Y-m-d', strtotime($post->end_date));

        switch ($type) {
            case 'FD':

                DB::beginTransaction();
                try {

                    $fdid = explode(',', $exitsId->fdId);

                    DB::table('member_fds_scheme')->whereIn('id', $fdid)->where('status', 'Pluge')->update(['status' => 'Active']);
                    // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('member_ccl')->where('id', $updated_id)->delete();

                    $id = DB::table('member_ccl')->insertGetId([
                        'ccl_Date' => $date,
                        'serialNo' => $serialNo,
                        'memberType' => $memberType,
                        'membership' => $post->ccl_memnumber,
                        'cclNo' => $post->ccl_acc_no,
                        'ccl_end_Date' => $endDate,
                        'groupCode' =>  $groupCode,
                        'ledgerCode' => $ledgerCode,
                        'year' => $post->year,
                        'month' => $post->months,
                        'days' => $post->days,
                        'interest' => $post->rate_of_interest,
                        'fdId' => $fdsaccounts,
                        'Types' => $post->ccltype,
                        'interestType' => $post->interest_type,
                        'fdAmount' => $fd_amount,
                        'rd_id' => $rdaccounts,
                        'rd_aacount' => $rd_amount,
                        'dailyId' => $dailyaccounts,
                        'daily_amount' => $dailyamount,
                        'ccl_amount' => $post->loan_amount,
                        'narration' => $post->narration,
                        'status' => 'Disbursed',
                        'branchId' => session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    //____________General Ledger Entry

                    //_________________CCL Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => $groupCode,
                    //     'ledgerCode' => $ledgerCode,
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    //___________Cash Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => 'C002',
                    //     'ledgerCode' => 'C002',
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    $deposittypes = $post->ccltype;
                    $deposit_ids = explode(',', $deposit_ids);
                    if ($deposittypes === 'FD') {
                        DB::table('member_fds_scheme')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'RD') {
                        DB::table('re_curring_rds')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'DailyDeposit') {
                        DB::table('daily_collections')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    }


                    DB::commit();

                    $memberType = $post->cclmember;
                    $membershipNumber = $post->ccl_memnumber;

                    $memberdetail = DB::table('member_accounts')
                        ->where('memberType', $memberType)
                        ->where('accountNo', 'LIKE', $membershipNumber . '%')
                        ->where('status', 'Active')
                        ->first();

                    $ccl_aacount = DB::table('member_ccl')
                        ->where(['memberType' => $memberType, 'membership' => $membershipNumber])
                        ->get();

                    if (!empty($memberdetail)) {
                        return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'An error occurred while Entered Record',
                        'error' => $e->getMessage(),
                        'lines' => $e->getLine()
                    ]);
                }
                break;

            case 'RD':

                DB::beginTransaction();
                try {

                    $rd_id = explode(',', $exitsId->rd_id);

                    DB::table('re_curring_rds')->whereIn('id', $rd_id)->where('status', 'Pluge')->update(['status' => 'Active']);
                    // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('member_ccl')->where('id', $updated_id)->delete();


                    $id = DB::table('member_ccl')->insertGetId([
                        'ccl_Date' => $date,
                        'serialNo' => $serialNo,
                        'memberType' => $memberType,
                        'membership' => $post->ccl_memnumber,
                        'cclNo' => $post->ccl_acc_no,
                        'ccl_end_Date' => $endDate,
                        'groupCode' =>  $groupCode,
                        'ledgerCode' => $ledgerCode,
                        'year' => $post->year,
                        'interestType' => $post->interest_type,
                        'month' => $post->months,
                        'days' => $post->days,
                        'interest' => $post->rate_of_interest,
                        'fdId' => $fdsaccounts,
                        'Types' => $post->ccltype,
                        'fdAmount' => $fd_amount,
                        'rd_id' => $rdaccounts,
                        'rd_aacount' => $rd_amount,
                        'dailyId' => $dailyaccounts,
                        'daily_amount' => $dailyamount,
                        'ccl_amount' => $post->loan_amount,
                        'narration' => $post->narration,
                        'status' => 'Disbursed',
                        'branchId' => session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    //____________General Ledger Entry

                    //_________________CCL Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => $groupCode,
                    //     'ledgerCode' => $ledgerCode,
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    //___________Cash Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => 'C002',
                    //     'ledgerCode' => 'C002',
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    $deposittypes = $post->ccltype;
                    $deposit_ids = explode(',', $deposit_ids);
                    if ($deposittypes === 'FD') {
                        DB::table('member_fds_scheme')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'RD') {
                        DB::table('re_curring_rds')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'DailyDeposit') {
                        DB::table('daily_collections')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    }


                    DB::commit();

                    $memberType = $post->cclmember;
                    $membershipNumber = $post->ccl_memnumber;

                    $memberdetail = DB::table('member_accounts')
                        ->where('memberType', $memberType)
                        ->where('accountNo', 'LIKE', $membershipNumber . '%')
                        ->where('status', 'Active')
                        ->first();

                    $ccl_aacount = DB::table('member_ccl')
                        ->where(['memberType' => $memberType, 'membership' => $membershipNumber])
                        ->get();

                    if (!empty($memberdetail)) {
                        return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'An error occurred while Entered Record',
                        'error' => $e->getMessage(),
                        'lines' => $e->getLine()
                    ]);
                }

                break;

            case 'DailyDeposit':

                DB::beginTransaction();
                try {

                    $dailyid = explode(',', $exitsId->dailyId);

                    DB::table('daily_collections')->whereIn('id', $dailyid)->where('status', 'Pluge')->update(['status' => 'Active']);
                    // DB::table('general_ledgers')->where('serialNo', $exitsId->serialNo)->delete();
                    DB::table('member_ccl')->where('id', $updated_id)->delete();


                    $id = DB::table('member_ccl')->insertGetId([
                        'ccl_Date' => $date,
                        'serialNo' => $serialNo,
                        'memberType' => $memberType,
                        'membership' => $post->ccl_memnumber,
                        'cclNo' => $post->ccl_acc_no,
                        'ccl_end_Date' => $endDate,
                        'groupCode' =>  $groupCode,
                        'ledgerCode' => $ledgerCode,
                        'year' => $post->year,
                        'month' => $post->months,
                        'days' => $post->days,
                        'interest' => $post->rate_of_interest,
                        'fdId' => $fdsaccounts,
                        'Types' => $post->ccltype,
                        'fdAmount' => $fd_amount,
                        'rd_id' => $rdaccounts,
                        'interestType' => $post->interest_type,
                        'rd_aacount' => $rd_amount,
                        'dailyId' => $dailyaccounts,
                        'daily_amount' => $dailyamount,
                        'ccl_amount' => $post->loan_amount,
                        'narration' => $post->narration,
                        'status' => 'Disbursed',
                        'branchId' => session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    //____________General Ledger Entry

                    //_________________CCL Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => $groupCode,
                    //     'ledgerCode' => $ledgerCode,
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    //___________Cash Entry
                    // GeneralLedger::insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $post->ccl_acc_no,
                    //     'accountNo' => $post->ccl_memnumber,
                    //     'memberType' => $memberType,
                    //     'groupCode' => 'C002',
                    //     'ledgerCode' => 'C002',
                    //     'formName' => "CCL-Advancement",
                    //     'referenceNo' => $id,
                    //     'transactionDate' => $date,
                    //     'transactionType' => "Dr",
                    //     'transactionAmount' => $post->loan_amount,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     // 'agentId' => '',
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    $deposittypes = $post->ccltype;
                    $deposit_ids = explode(',', $deposit_ids);
                    if ($deposittypes === 'FD') {
                        DB::table('member_fds_scheme')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'RD') {
                        DB::table('re_curring_rds')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    } elseif ($deposittypes === 'DailyDeposit') {
                        DB::table('daily_collections')
                            ->whereIn('id', $deposit_ids)
                            ->update(['status' => 'Pluge']);
                    }


                    DB::commit();

                    $memberType = $post->cclmember;
                    $membershipNumber = $post->ccl_memnumber;

                    $memberdetail = DB::table('member_accounts')
                        ->where('memberType', $memberType)
                        ->where('accountNo', 'LIKE', $membershipNumber . '%')
                        ->where('status', 'Active')
                        ->first();

                    $ccl_aacount = DB::table('member_ccl')
                        ->where(['memberType' => $memberType, 'membership' => $membershipNumber])
                        ->get();

                    if (!empty($memberdetail)) {
                        return response()->json(['status' => 'success', 'memberdetail' => $memberdetail, 'accounts' => $ccl_aacount, 'messages' => 'CCL Submitted Successfully']);
                    } else {
                        return response()->json(['status' => 'Fail', 'messages' => 'No Account Found']);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'An error occurred while Entered Record',
                        'error' => $e->getMessage(),
                        'lines' => $e->getLine()
                    ]);
                }
                break;

            default:
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Updated Some Technical Issue']);
        }
    }







    //____________________________Cash Credit Limit Recovery____________________

    public function cclrecoveryIndex()
    {
        $groups = DB::table('group_masters')->whereIn('groupCode', ['C002', 'BANK001'])->get();
        $data['groups'] = $groups;
        return view('transaction.ccl.cclrecovery', $data);
    }

    public function getcclaccountlist(Request $post)
    {
        $rules = [
            "memberType" => "required",
            "accountNo" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $memberType = $post->memberType;
        $cclNo = $post->accountNo;

        $allDetails = DB::table('member_ccl')->where('memberType', $memberType)->where('cclNo', 'LIKE', $cclNo . '%')->get();

        if (!empty($allDetails)) {
            return response()->json(['status' => 'success', 'allDetails' => $allDetails]);
        } else {
            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function getcclaccount(Request $post)
    {

        $rules = [
            "memberType" => "required",
            "selectaccount" => "required",
            "opening_date" => 'required'
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $opening_date = date('Y-m-d', strtotime($post->opening_date));

        $memberType = $post->memberType;
        $cclNo = $post->selectaccount;

        $cclDetails = DB::table('member_ccl')
            ->select(
                'ccl_payments.ccl_Id',
                'member_ccl.id as ids',
                'member_ccl.ccl_Date',
                'member_ccl.memberType',
                'member_ccl.membership',
                'member_ccl.cclNo',
                'member_ccl.year',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.Types',
                'member_ccl.interest',
                'member_ccl.ccl_amount',
                'member_ccl.status',
                'member_ccl.ccl_end_Date',
                DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
            )
            ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')
            ->where('member_ccl.memberType', $memberType)
            ->where('member_ccl.cclNo', $cclNo)
            ->whereDate('member_ccl.ccl_Date', '<=', $opening_date)
            // ->where('member_ccl.status', 'Disbursed')
            ->groupBy(
                'ccl_payments.ccl_Id',
                'member_ccl.ccl_Date',
                'member_ccl.memberType',
                'member_ccl.membership',
                'member_ccl.cclNo',
                'member_ccl.year',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.Types',
                'member_ccl.interest',
                'member_ccl.ccl_amount',
                'member_ccl.status',
                'member_ccl.id',
                'member_ccl.ccl_end_Date',
            )
            ->first();


        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

        if (!empty($cclDetails) && !empty($cclDetails)) {
            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
        } else {
            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function cclamounttrfdsaving(Request $post)
    {
        $id = $post->id;

        $cclDetails = DB::table('member_ccl')->where('id', $id)->where('status', 'Disbursed')->first();

        $saving_account = DB::table('opening_accounts')
            ->where(['membershipno' => $cclDetails->membership, 'membertype' => $cclDetails->memberType, 'accountname' => 'Saving', 'status' => 'Active'])
            ->first();

        $currentDate = date('Y-m-d', strtotime($post->transcationDate));


        if ($cclDetails) {
            $payments = DB::table('ccl_payments as p1')
                ->leftJoin('ccl_payments as p2', function ($join) {
                    $join->on('p1.ccl_Id', '=', 'p2.ccl_Id')
                        ->on('p2.transcationDate', '=', DB::raw('(SELECT MIN(transcationDate) FROM ccl_payments WHERE transcationDate > p1.transcationDate)'));
                })
                ->where('p1.ccl_Id', $cclDetails->id)
                ->selectRaw(
                    'p1.transcationDate as base_date,
                    LAST_DAY(p1.transcationDate) as last_day_of_month,
                    COALESCE(p2.transcationDate, ?) as compared_date,
                    DATEDIFF(COALESCE(p2.transcationDate, ?), p1.transcationDate) as day_difference,
                    SUM(p1.transfer_amount) as total_withdraw,
                    SUM(p1.recovey_amount) as total_deposit,
                    SUM(p1.interest_amount) as interest_amount',
                    [$currentDate, $currentDate]
                )
                ->groupBy(
                    DB::raw('YEAR(p1.transcationDate), MONTH(p1.transcationDate)'),
                    'p2.transcationDate',
                    'p1.transcationDate'
                )
                ->orderBy('p1.transcationDate', 'asc')
                ->get();

            $grandTotal = 0;
            $interestRate = $cclDetails->interest;

            $principal = 0;
            $totalInterest = 0;

            // Initialize sums
            $totalWithdraw = 0;
            $totalDeposit = 0;
            $days = 0;

            foreach ($payments as $payment) {
                if ($payment->last_day_of_month < $currentDate) {

                    $withdraw_amount = $payment->total_withdraw ?? 0;
                    $deposit_amount = $payment->total_deposit ?? 0;
                    $interest = $payment->interest_amount ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount + $interest;
                    $interest_amount = (($amount * $interestRate) / 100 / 365) * $days;
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;

                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                } else {
                    $withdraw_amount = $payment->total_withdraw ?? 0;
                    $deposit_amount = $payment->total_deposit ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount;
                    $interest_amount = (($amount * $interestRate) / 100 / 365) * $days;

                    // Accumulate sums
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;

                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                }
            }

            return response()->json([
                'status' => 'success',
                'principal' => round($principal, 2),
                'totalInterest' => round($totalInterest, 2),
                'grandTotal' => round($grandTotal, 2),
                'interestRate' => $interestRate,
                'days' => $days,
                'cclDetails' => $cclDetails,
                'saving_account' => $saving_account
            ]);
        }
    }

    public function ccltrfdtosavingaccount(Request $post)
    {
        // dd($post->all());
        $paymenttype = $post->paymenttype;

        if ($paymenttype === 'Cash') {
            $rules = [
                "cclId" => "required",
                "cclmemberType" => "required",
                "membershipnumbers" => "required",
                "transcationDate" => "required",
                "cashbankgroup" => "required",
                "cashbankledger" => 'required',
                "trfdamount" => 'required',
            ];
        } else {
            $rules = [
                "cclId" => "required",
                "savingId" => "required",
                "transcationDate" => "required",
                "saving_account" => "required",
                "trfdamount" => "required",
                "cclmemberType" => 'required',
                "membershipnumbers" => 'required',
            ];
        }

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transcationDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        //___________check Advancement Date To Transaction Not Exceed
        $transactionDate = date('Y-m-d', strtotime($post->transcationDate));
        $cclDetails = DB::table('member_ccl')->where('id', $post->cclId)->where('status', 'Disbursed')->first();

        if ($transactionDate < $cclDetails->ccl_Date) {
            return response()->json(['status' => 'Fail', 'messages' => 'Transaction Date cannot be earlier than the CCL Advancement Date.']);
        }

        $members = DB::table('member_accounts')->where('accountNo', $post->membershipnumbers)->where('memberType', $post->cclmemberType)->first();


        //____________Get Saving Accounts
        $saving_account = DB::table('opening_accounts')
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
            ->where('opening_accounts.membertype', $members->memberType)
            ->where('opening_accounts.membershipno', $members->accountNo)
            ->first();


        if ($transactionDate < $saving_account->transactionDate) {
            return response()->json(['status' => 'Fail', 'message' => 'Transaction Date cannot be earlier than the Saving A/c Opening Date.']);
        }

        $saving_group = '';
        $saving_ledger = '';


        if ($saving_account) {
            if ($saving_account->groupCode && $saving_account->ledgerCode) {
                $saving_group = $saving_account->groupCode;
                $saving_ledger = $saving_account->ledgerCode;
            } else {
                return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
        }


        $memberType = $post->cclmemberType;

        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $serialNo = 'CCL'.time();


        if ($paymenttype === 'Cash') {

            $cashbankgroup = $post->cashbankgroup;
            $cashbankledger = $post->cashbankledger;

            if (empty($cashbankgroup) && empty($cashbankledger)) {
                return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group/Ledger Code Not Found']);
            }






            DB::beginTransaction();

            try {
                $trfdamount = $post->trfdamount ? $post->trfdamount : 0;
                $interest_amount = $post->trfd_interest_amount ? $post->trfd_interest_amount : 0;

                $net_amounts = $trfdamount + $interest_amount;

                $id =  DB::table('ccl_payments')->insertGetId([
                    'serialNo' => $serialNo,
                    'type' => 'Withdraw',
                    'ccl_Id' => $cclDetails->id,
                    'transcationDate' => $transactionDate,
                    'memberType' => $memberType,
                    'membershipno' => $cclDetails->membership,
                    'ccl_account' => $cclDetails->cclNo,
                    'saving_account' => $saving_account->accountNo,
                    'transfer_amount' => $post->trfdamount,
                    'interest_amount' => $interest_amount,
                    'recovey_amount' => 0,
                    'groupCode' => $groupCode,
                    'payment_type' => $paymenttype,
                    'ledgerCode' => $ledgerCode,
                    'narration' => $post->narration,
                    'paymentgroup' => $cashbankgroup,
                    'paymentledger' => $cashbankledger,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'agentId' => '',
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $post->user()->id,
                ]);


                //__________________General Ledger Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'SOD Withdraw',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Cash / Bank in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $cashbankgroup,
                    'ledgerCode' => $cashbankledger,
                    'formName' => 'SOD Withdraw',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $net_amounts,
                    'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);



                //__________Interest Paid Entries in General Entries
                if (!empty($interest_amount) && $interest_amount > 0) {
                    // DB::table('general_ledgers')->insert([
                    //     'serialNo' => $serialNo,
                    //     'accountId' => $cclDetails->cclNo,
                    //     'accountNo' => $cclDetails->membership,
                    //     'memberType' => $cclDetails->memberType,
                    //     'groupCode' => $cashbankgroup,
                    //     'ledgerCode' => $cashbankledger,
                    //     'formName' => 'CCL Interest',
                    //     'referenceNo' => $id,
                    //     'entryMode' => 'manual',
                    //     'transactionDate' => $transactionDate,
                    //     'transactionType' => 'Dr',
                    //     'transactionAmount' => $interest_amount,
                    //     'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    //     'branchId' => session('branchId') ?? 1,
                    //     // 'agentId' => $post->agents,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'updatedBy' => $post->user()->id,
                    //     'is_delete' => 'No',
                    // ]);


                    // __________Member Saving Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $cclDetails->cclNo,
                        'accountNo' => $cclDetails->membership,
                        'memberType' => $cclDetails->memberType,
                        'groupCode' => $interestGroup,
                        'ledgerCode' => $interestLedger,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);
                }

                DB::commit();

                $cclNo = $cclDetails->cclNo;
                return $this->Datareturn($memberType, $cclNo);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while Entered Record',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        } else {
            DB::beginTransaction();
            try {

                $trfdamount = $post->trfdamount ? $post->trfdamount : 0;
                $interest_amount = $post->trfd_interest_amount ? $post->trfd_interest_amount : 0;

                $id =  DB::table('ccl_payments')->insertGetId([
                    'serialNo' => $serialNo,
                    'type' => 'Withdraw',
                    'ccl_Id' => $cclDetails->id,
                    'transcationDate' => $transactionDate,
                    'memberType' => $memberType,
                    'membershipno' => $cclDetails->membership,
                    'ccl_account' => $cclDetails->cclNo,
                    'saving_account' => $saving_account->accountNo,
                    'transfer_amount' => $post->trfdamount,
                    'interest_amount' => $interest_amount,
                    'groupCode' => $groupCode,
                    'payment_type' => $paymenttype,
                    'ledgerCode' => $ledgerCode,
                    'paymentgroup' => $saving_group,
                    'paymentledger' => $saving_ledger,
                    'narration' => $post->narration,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'agentId' => '',
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $post->user()->id,
                ]);


                //___________Member saving Table Entry
                DB::table('member_savings')->insertGetId([
                    'secheme_id' => $saving_account->accounttype,
                    'serialNo' => $serialNo,
                    'accountId' => $saving_account->accountNo,
                    'accountNo' => $saving_account->membershipno,
                    'memberType' => $saving_account->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'savingNo' =>  $saving_account->accountNo,
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Deposit',
                    'depositAmount' => $post->trfdamount,
                    'withdrawAmount' => 0,
                    'paymentType' => '',
                    'bank' => '',
                    'chequeNo' => 'CCL Limit',
                    'narration' => 'CCL TRFD Date ' . $transactionDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    // 'agentId' => ,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                //__________________General Ledger Entries

                //__________Interest Paid Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'CCL Trfd',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Member Saving Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $saving_account->accountNo,
                    'accountNo' => $saving_account->membershipno,
                    'memberType' => $saving_account->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'formName' => 'CCL Trfd',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);

                if (!empty($interest_amount) && $interest_amount > 0) {
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $cclDetails->cclNo,
                        'accountNo' => $cclDetails->membership,
                        'memberType' => $cclDetails->memberType,
                        'groupCode' => $groupCode,
                        'ledgerCode' => $ledgerCode,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Member Saving Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $saving_account->accountNo,
                        'accountNo' => $saving_account->membershipno,
                        'memberType' => $saving_account->membertype,
                        'groupCode' => $interestGroup,
                        'ledgerCode' => $interestLedger,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);
                }

                DB::commit();

                $cclNo = $cclDetails->cclNo;
                return $this->Datareturn($memberType, $cclNo);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while Entered Record',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        }
    }

    public function Datareturn($memberType, $cclNo){

        $cclDetails = DB::table('member_ccl')
            ->select(
                'ccl_payments.ccl_Id',
                'member_ccl.id as ids',
                'member_ccl.ccl_Date',
                'member_ccl.memberType',
                'member_ccl.membership',
                'member_ccl.cclNo',
                'member_ccl.year',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.Types',
                'member_ccl.interest',
                'member_ccl.ccl_amount',
                'member_ccl.status',
                'member_ccl.ccl_end_Date',
                DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
            )
            ->join('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')
            ->where('member_ccl.memberType', $memberType)
            ->where('member_ccl.cclNo', $cclNo)
            ->where('member_ccl.status', 'Disbursed')
            ->groupBy(
                'ccl_payments.ccl_Id',
                'member_ccl.ccl_Date',
                'member_ccl.memberType',
                'member_ccl.membership',
                'member_ccl.cclNo',
                'member_ccl.year',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.Types',
                'member_ccl.interest',
                'member_ccl.ccl_amount',
                'member_ccl.status',
                'member_ccl.id',
                'member_ccl.ccl_end_Date',
            )
            ->first();

        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

        if (!empty($cclDetails) && !empty($cclDetails)) {
            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
        } else {
            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function viewcclledgers(Request $post)
    {

        $id = $post->id;
        $cclDetails = DB::table('member_ccl')->where('id', $id)->first();
        $currentDatesss = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($post->currentDate)));

        $payments = DB::table('ccl_payments')
            ->select(
                'ccl_payments.*',
                'member_ccl.id as ccid',
                'member_ccl.ccl_amount',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.year',
                'member_ccl.interest',
                'member_ccl.status'
                )
            ->leftJoin('member_ccl','member_ccl.id','=','ccl_payments.ccl_Id')
            ->where('ccl_payments.ccl_Id', $cclDetails->id)
            ->orderBy('ccl_payments.transcationDate','ASC')
            ->get();
            // dd($payments);



        // $paymentsWithDateDiff = $payments->map(function ($payment, $index) use ($payments, $currentDatesss) {
        //     if ($index < $payments->count() - 1) {
        //         $nextPayment = $payments[$index + 1];
        //         $currentDate = Carbon::parse($payment->transcationDate);
        //         $nextDate = Carbon::parse($nextPayment->transcationDate);

        //         $daysDiff = $currentDate->diffInDays($nextDate);

        //         if ($daysDiff === null) {
        //             $payment->days_diff = $currentDate->diffInDays($currentDatesss);
        //         } else {
        //             $payment->days_diff = $daysDiff;
        //         }
        //     } else {
        //         $payment->days_diff = $currentDatesss->diffInDays(Carbon::parse($payment->transcationDate));
        //     }
        //     return $payment;
        // });

        return response()->json([
            'status' => 'success',
            'cclDetails' => $cclDetails,
            'payments' => $payments,
        ]);
    }

    public function deleteccltrfdpayment(Request $post)
    {
        $id = $post->id;
        $paymentId = DB::table('ccl_payments')->where('id', $id)->first();
        $currentDate = date('Y-m-d',strtotime($post->currentDate));
        // $cclDetails = DB::table('member_ccl')->where('id', $paymentId->ccl_Id)->where('status', 'Disbursed')->first();

        // dd($cclDetails);
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($currentDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }


        if (is_null($paymentId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record not Found']);
        } else {
            DB::beginTransaction();
            try {

                DB::table('member_savings')->where('serialNo', $paymentId->serialNo)->delete();
                DB::table('general_ledgers')->where('serialNo', $paymentId->serialNo)->delete();
                DB::table('ccl_payments')->where('id', $id)->delete();

                DB::commit();

                $cclNo = $post->ccl_account;
                $memberType = $post->member_type;
                return $this->Datareturn($memberType, $cclNo);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while Entered Record',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        }
    }

    public function recieptcclamount(Request $post)
    {
        $id = $post->id;
        $receiptDate = date('Y-m-d', strtotime($post->receipt_date));

        $cclDetails = DB::table('member_ccl')
            ->where('id', $id)
            ->where('status', 'Disbursed')
            ->first();

        if (!$cclDetails) {
            return response()->json(['status' => 'Fail', 'messages' => 'CCL Details Not Found']);
        }

        $saving_account = DB::table('opening_accounts')
            ->where([
                'membershipno' => $cclDetails->membership,
                'membertype' => $cclDetails->memberType,
                'accountname' => 'Saving',
                'status' => 'Active',
            ])
            ->first();

        $payments = DB::table('ccl_payments')
            ->where('ccl_Id', $cclDetails->id)
            ->orderBy('transcationDate', 'asc')
            ->get();

        $principal = $cclDetails->ccl_amount;
        $interestRate = floatval($cclDetails->interest);
        $totalWithdraw = 0;
        $totalDeposit = 0;
        $totalInterest = 0;
        $days = 0;

        foreach ($payments as $payment) {
            $transactionDate = $payment->transcationDate;
            $nextTransactionDate = DB::table('ccl_payments')
                ->where('ccl_Id', $cclDetails->id)
                ->where('transcationDate', '>', $transactionDate)
                ->min('transcationDate');

            $comparisonDate = $nextTransactionDate ? $nextTransactionDate : $receiptDate;
            $days = (strtotime($comparisonDate) - strtotime($transactionDate)) / (60 * 60 * 24);

            $withdraw = floatval($payment->transfer_amount ?? 0);
            $deposit = floatval($payment->recovey_amount ?? 0);
            $totalWithdraw += $withdraw;
            $totalDeposit += $deposit;

            $balance = $principal - $totalWithdraw + $totalDeposit;
            $interest = ($balance * $interestRate * $days) / (100 * 365);
            $totalInterest += $interest;
        }

        $grandTotal = $principal - $totalWithdraw + $totalDeposit + $totalInterest;

        return response()->json([
            'status' => 'success',
            'principal' => round($principal, 2),
            'interestRate' => $interestRate,
            'days' => $days,
            'cclDetails' => $cclDetails,
            'saving_account' => $saving_account,
            'totalWithdraw' => $totalWithdraw,
            'totalDeposit' => $totalDeposit,
            'totalInterest' => round($totalInterest, 2),
            'grandTotal' => round($grandTotal, 2),
        ]);
    }

    public function checkinterestdatewise(Request $post)
    {

        $id = $post->id;
        $currentDate = date('Y-m-d', strtotime($post->receipt_date));
        $cclDetails = DB::table('member_ccl')->where('id', $id)->first();

        if ($cclDetails) {

            $payments = DB::table('ccl_payments as p1')
                ->leftJoin('ccl_payments as p2', function ($join) {
                    $join->on('p1.ccl_Id', '=', 'p2.ccl_Id')
                        ->on('p2.transcationDate', '=', DB::raw('(SELECT MIN(transcationDate) FROM ccl_payments WHERE transcationDate > p1.transcationDate)'));
                })
                ->where('p1.ccl_Id', $cclDetails->id)
                ->selectRaw(
                    'p1.transcationDate as base_date,
                    LAST_DAY(p1.transcationDate) as last_day_of_month,
                    COALESCE(p2.transcationDate, ?) as compared_date,
                    DATEDIFF(COALESCE(p2.transcationDate, ?), p1.transcationDate) as day_difference,
                    SUM(p1.transfer_amount) as total_withdraw,
                    SUM(p1.recovey_amount) as total_deposit,
                    SUM(p1.interest_amount) as interest_amount',
                    [$currentDate, $currentDate]
                )
                ->groupBy(
                    DB::raw('YEAR(p1.transcationDate), MONTH(p1.transcationDate)'),
                    'p2.transcationDate',
                    'p1.transcationDate'
                )
                ->orderBy('p1.transcationDate', 'asc')
                ->get();



            $grandTotal = 0;
            $interestRate = $cclDetails->interest;

            $principal = 0;
            $totalInterest = 0;

            // Initialize sums
            $totalWithdraw = 0;
            $totalDeposit = 0;
            $days = 0;
            $principal_amounts = 0;
            $withdraw_amount = 0;
            $deposit_amount = 0;
            $interest_amount = 0;

            foreach ($payments as $payment) {
                if ($payment->last_day_of_month < $currentDate) {

                    $withdraw_amount += $payment->total_withdraw ?? 0;
                    $deposit_amount += $payment->total_deposit ?? 0;
                    $interest = $payment->interest_amount ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount + $interest;
                    $interest_amount += (($amount * $interestRate) / 100 / 365) * $days;
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;

                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                    $principal_amounts += $withdraw_amount - $deposit_amount;
                } else {
                    $withdraw_amount += $payment->total_withdraw ?? 0;
                    $deposit_amount += $payment->total_deposit ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount;
                    $interest_amount += (($amount * $interestRate) / 100 / 365) * $days;

                    // Accumulate sums
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;

                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                    $principal_amounts += $withdraw_amount - $deposit_amount;
                }
            }

            return response()->json([
                'status' => 'success',
                'principal' => round($principal, 2),
                'totalInterest' => round($totalInterest, 2),
                'grandTotal' => round($grandTotal, 2),
                'interestRate' => $interestRate,
                'days' => $days,
                'principal_amounts' => $principal_amounts,
                'totalWithdraw' => $withdraw_amount,
                'totalDeposit' => $deposit_amount,
            ]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function checktrfdinterestdatewise(Request $post){

        $id = $post->id;
        $currentDate = date('Y-m-d', strtotime($post->receipt_date));
        $cclDetails = DB::table('member_ccl')->where('id', $id)->first();

        if ($cclDetails) {
            $payments = DB::table('ccl_payments as p1')
                ->leftJoin('ccl_payments as p2', function ($join) {
                    $join->on('p1.ccl_Id', '=', 'p2.ccl_Id')
                        ->on('p2.transcationDate', '=', DB::raw('(SELECT MIN(transcationDate) FROM ccl_payments WHERE transcationDate > p1.transcationDate)'));
                })
                ->where('p1.ccl_Id', $cclDetails->id)
                ->selectRaw(
                    'p1.transcationDate as base_date,
                    LAST_DAY(p1.transcationDate) as last_day_of_month,
                    COALESCE(p2.transcationDate, ?) as compared_date,
                    DATEDIFF(COALESCE(p2.transcationDate, ?), p1.transcationDate) as day_difference,
                    SUM(p1.transfer_amount) as total_withdraw,
                    SUM(p1.recovey_amount) as total_deposit,
                    SUM(p1.interest_amount) as interest_amount',
                    [$currentDate, $currentDate]
                )
                ->groupBy(
                    DB::raw('YEAR(p1.transcationDate), MONTH(p1.transcationDate)'),
                    'p2.transcationDate',
                    'p1.transcationDate'
                )
                ->orderBy('p1.transcationDate', 'asc')
                ->get();

            $interestRate = $cclDetails->interest;

            $grandTotal = 0;
            $principal_amounts = 0;
            $principal = 0;
            $totalInterest = 0;
            $totalWithdraw = 0;
            $totalDeposit = 0;
            $days = 0;

            foreach ($payments as $payment) {
                if ($payment->last_day_of_month < $currentDate) {

                    $withdraw_amount = $payment->total_withdraw ?? 0;
                    $deposit_amount = $payment->total_deposit ?? 0;
                    $interest = $payment->interest_amount ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount + $interest;
                    $interest_amount = (($amount * $interestRate) / 100 / 365) * $days;
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;
                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                    $principal_amounts += $withdraw_amount - $deposit_amount;
                } else {

                    $withdraw_amount = $payment->total_withdraw ?? 0;
                    $deposit_amount = $payment->total_deposit ?? 0;
                    $days = $payment->day_difference ?? 0;
                    $amount = $withdraw_amount - $deposit_amount;
                    $interest_amount = (($amount * $interestRate) / 100 / 365) * $days;
                    $totalWithdraw += $withdraw_amount;
                    $totalDeposit += $deposit_amount;
                    $principal += $amount;
                    $totalInterest += $interest_amount;
                    $grandTotal = $principal + $totalInterest;
                    $principal_amounts += $withdraw_amount - $deposit_amount;
                }
            }

            return response()->json([
                'status' => 'success',
                'principal' => round($principal, 2),
                'totalInterest' => round($totalInterest, 2),
                'grandTotal' => round($grandTotal, 2),
                'interestRate' => $interestRate,
                'days' => $days,
                'principal_amounts' => $principal_amounts,
            ]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function cclreceivedgetledgers(Request $post)
    {
        $groups = $post->groups;

        if ($groups) {
            $ledgers = DB::table('ledger_masters')->where('groupCode', $groups)->where('ledgerCode', '!=', 'BANKFD01')->where('status', 'Active')->orderBy('name', 'ASC')->get();
            if (! empty($ledgers)) {
                return response()->json(['status' => 'success', 'ledgers' => $ledgers]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Ledger Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Group Not Found']);
        }
    }

    public function checkExceedBalanceCcl(Request $post)
    {
        // dd($post->all());

        $updateId = $post->updateId;
        if ($updateId) {
            $rules = [
                "updateId" => "required",
                "cclmemberType" => "required",
                "transcationDate" => "required"
            ];
        } else {
            $rules = [
                "cclId" => "required",
                "cclmemberType" => "required",
                "transcationDate" => "required"
            ];
        }


        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['statuts' => 'Fail', 'messages' => $validator->errors()]);
        }

        $cclid = $post->cclId;
        $currentDate = date('Y-m-d', strtotime($post->transcationDate));
        $cclmemberType = $post->cclmemberType;
        $cclDetails = DB::table('member_ccl')->where('id', $cclid)->where('status', 'Disbursed')->first();


        $editId = DB::table('ccl_payments')->where('id', $updateId)->first();
        if (!empty($editId)) {
            $sod_accounts = DB::table('member_ccl')->where('id', $editId->ccl_Id)->where('status', 'Closed')->first();
            if (!empty($sod_accounts)) {
                return response()->json(['status' => 'Fail', 'messages' => 'SOD A/c Closed Your Cant Access This Account']);
            }
        }


        $payments = DB::table('ccl_payments')
            ->where('ccl_payments.ccl_Id', $cclid)
            ->where('ccl_payments.memberType', $cclmemberType)
            ->whereDate('ccl_payments.transcationDate', '<=', $currentDate)
            ->get();

        $limit_amount = $cclDetails->ccl_amount;
        $withdraw_amount = $payments->sum('transfer_amount');
        $recovey_amount = $payments->sum('recovey_amount');

        if ($limit_amount || $withdraw_amount || $recovey_amount) {
            return response()->json([
                'status' => 'success',
                'withdraw_amount' => $withdraw_amount,
                'recovey_amount' => $recovey_amount,
                'limit_amount' => $limit_amount,
                'messages' => 'Amount is Valid'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function cclrecoverInsert(Request $post)
    {

        $rules = [
            "cclid" => "required",
            "rcclmemberType" => "required",
            "receipt_date" => "required",
            "principal" => "required",
            // "rate_of_interest" => "required",
            // "interest_amount" => "required",
            "total_amount" => "required",
            // "receipt_amount" => "required",
            "groupCode" => 'required',
            "ledgerCode" => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->receipt_date)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }


        $interest_amount = $post->interest_amount ? $post->interest_amount : 0;
        $receipt_amount = 0;

        // if (!empty($post->interest_amount)) {
        //     $interest_amount = $post->interest_amount;
        // } else {
        //     return response()->json(['status' => 'fail', 'messages' => "Reciept and Interest Amount One Required"]);
        // }


        if (!empty($post->receipt_amount)) {
            $receipt_amount = $post->receipt_amount;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Reciept and Interest Amount One Required"]);
        }



        //___________check Advancement Date To Transaction Not Exceed
        $transactionDate = date('Y-m-d', strtotime($post->receipt_date));
        $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status', 'Disbursed')->first();


        if ($transactionDate < $cclDetails->ccl_Date) {
            return response()->json(['status' => 'Fail', 'message' => 'Transaction Date cannot be earlier than the CCL Advancement Date.']);
        }


        $memberType = $post->rcclmemberType;
        $groupCode = '';
        $ledgerCode = '';
        $interestGroup = '';
        $interestLedger = '';


        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $groupCodecashbank = '';
        $ledgerCodecashbank = '';


        if ($post->groupCode && $post->ledgerCode) {
            $groupCodecashbank = $post->groupCode;
            $ledgerCodecashbank = $post->ledgerCode;
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Code Not Found']);
        }

        $serialNo = 'CCL' . time();

        DB::beginTransaction();
        try {



            $id =  DB::table('ccl_payments')->insertGetId([
                'serialNo' => $serialNo,
                'type' => 'Deposit',
                'ccl_Id' => $cclDetails->id,
                'transcationDate' => $transactionDate,
                'memberType' => $memberType,
                'membershipno' => $cclDetails->membership,
                'ccl_account' => $cclDetails->cclNo,
                'saving_account' => '',
                'transfer_amount' => 0,
                'recovey_amount' => $receipt_amount,
                'interest_amount' => $interest_amount,
                'payment_type' => $post->payment_type,
                'paymentgroup' => $groupCodecashbank,
                'paymentledger' => $ledgerCodecashbank,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'narration' => $post->receipt_narration,
                'branchId' => session('branchId') ? session('branchId') : 1,
                'agentId' => '',
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'updatedBy' => $post->user()->id,
            ]);



            if ($interest_amount > 0) {
                //__________CCL Recovery Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $interestGroup,
                    'ledgerCode' => $interestLedger,
                    'formName' => 'CCL Interest',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $interest_amount,
                    'narration' =>  'CCL Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);



                // __________Cash/Bank Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCodecashbank,
                    'ledgerCode' => $ledgerCodecashbank,
                    'formName' => 'CCL Interest',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $interest_amount,
                    'narration' => 'CCL Interest Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);
            }

            //__________________General Ledger Entries

            if ($receipt_amount > 0) {

                // dd($groupCode,$ledgerCode,$groupCodecashbank,$ledgerCodecashbank);
                //__________CCL Recovery Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'CCL Recovery',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $receipt_amount,
                    'narration' =>  'CCL Interest Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Cash/Bank Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCodecashbank,
                    'ledgerCode' => $ledgerCodecashbank,
                    'formName' => 'CCL Recovery',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $receipt_amount,
                    'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);
            }




            DB::commit();


            $cclNo = $cclDetails->cclNo;

            return $this->Datareturn($memberType, $cclNo);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'Fail',
                'messages' => 'An error occurred while Entered Record',
                'error' => $e->getMessage(),
                'lines' => $e->getLine()
            ]);
        }
    }

    public function editcclrecoverypayments(Request $post)
    {

        $session_master = SessionMaster::find(Session::get('sessionId'));
        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $transactionDate = date('Y-m-d', strtotime($post->currentDate));

        $result = $this->isDateBetween($transactionDate);

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $id = $post->id;

        $cclpaymentsId = DB::table('ccl_payments')->where('id', $id)->first();
        $cclDetails = DB::table('member_ccl')->where('id', $cclpaymentsId->ccl_Id)->where('status', 'Disbursed')->first();
        $allmemberlist = DB::table('member_accounts')->where('memberType', $cclpaymentsId->memberType)->where('accountNo', $cclpaymentsId->membershipno)->where('status', 'Active')->first();

        $deposit = DB::table('ccl_payments')->where('ccl_Id', $cclDetails->id)->whereDate('transcationDate', '<=', $transactionDate)->get();

        $balance = 0;
        $deposit_amount = $deposit->sum('recovey_amount');
        $withdraw_amount = $deposit->sum('transfer_amount');

        if ($deposit->where('type', 'Withdraw')->isNotEmpty()) {
            $balance += $cclDetails->ccl_amount - $withdraw_amount + $deposit_amount;
        }


        if ($deposit->where('type', 'Deposit')->isNotEmpty()) {
            $balance = $withdraw_amount - $deposit_amount;
        }


        if (is_null($cclpaymentsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'The requested record was not found. Please verify the payment details and try again.']);
        } else {
            if ($cclDetails->status === 'Closed' || $cclDetails->status === 'Inactive') {
                return response()->json(['status' => 'Fail', 'messages' => 'The loan is currently ' . $cclDetails->status . '. Editing this entry is not allowed.']);
            } else {
                return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'cclpaymentsId' => $cclpaymentsId, 'balance' => $balance, 'allmemberlist' => $allmemberlist]);
            }
        }
    }


    public function editcheckExceedBalanceCcl(Request $post)
    {

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $transactionDate = date('Y-m-d', strtotime($post->transcationDate));
        $updateId = $post->updateId;
        $memberType = $post->cclmemberType;
        $enteredAmount = $post->enteredAmount;

        $result = $this->isDateBetween($transactionDate);

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $cclpaymentsId = DB::table('ccl_payments')->where('id', $updateId)->first();


        if (!empty($cclpaymentsId)) {
            $sod_accounts = DB::table('member_ccl')->where('id', $cclpaymentsId->ccl_Id)->where('status', 'Closed')->first();
            if (!empty($sod_accounts)) {
                return response()->json(['status' => 'Fail', 'messages' => 'SOD A/c Closed Your Cant Access This Account']);
            }
        }


        // $cclpaymentsId = DB::table('ccl_payments')->where('id', $editId->id)->first();
        $cclDetails = DB::table('member_ccl')->where('id', $cclpaymentsId->ccl_Id)->where('status', 'Disbursed')->first();

        $allmemberlist = DB::table('member_accounts')->where('memberType', $cclpaymentsId->memberType)->where('accountNo', $cclpaymentsId->membershipno)->where('status', 'Active')->first();

        $deposit = DB::table('ccl_payments')->where('ccl_Id', $cclDetails->id)->whereDate('transcationDate', '<=', $transactionDate)->get();
        // dd($cclpaymentsId);

        $balance = 0;
        $limit_amount = $cclDetails->ccl_amount;
        $deposit_amount = $deposit->sum('recovey_amount');
        $withdraw_amount = $deposit->sum('transfer_amount');

        if ($deposit->where('type', 'Withdraw')->isNotEmpty()) {
            $balance += $limit_amount - $withdraw_amount + $deposit_amount + $cclpaymentsId->transfer_amount;
        }


        if ($deposit->where('type', 'Deposit')->isNotEmpty()) {
            $balance = $withdraw_amount - $deposit_amount + $cclpaymentsId->recovey_amount;
        }


        if (is_null($cclpaymentsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'The requested record was not found. Please verify the payment details and try again.']);
        } else {
            if ($cclDetails->status === 'Closed' || $cclDetails->status === 'Inactive') {
                return response()->json(['status' => 'Fail', 'messages' => 'The loan is currently ' . $cclDetails->status . '. Editing this entry is not allowed.']);
            } else {
                return response()->json([
                    'status' => 'success',
                    'limit_amount' => $limit_amount,
                    'deposit_amount' => $deposit_amount,
                    'withdraw_amount' => $withdraw_amount,
                    'cclDetails' => $cclDetails,
                    'cclpaymentsId' => $cclpaymentsId,
                    'balance' => $balance,
                    'allmemberlist' => $allmemberlist
                ]);
            }
        }
    }


    public function updateccltrfdtosavingaccount(Request $post)
    {
        $paymenttype = $post->paymenttype;

        if ($paymenttype === "Cash") {
            $rules = [
                "cclId" => "required",
                "cclmemberType" => "required",
                "updateId" => "required",
                "trfdupdatememberType" => "required",
                "membershipnumbers" => "required",
                "transcationDate" => "required",
                "paymenttype" => "required",
                "cashbankgroup" => "required",
                "cashbankledger" => "required"
            ];
        } else {
            $rules = [
                "cclId" => "required",
                "savingId" => "required",
                "cclmemberType" => "required",
                "updateId" => "required",
                "trfdupdatememberType" => "required",
                "transcationDate" => "required",
                "saving_account" => "required",
                "balance_amount" => "required",
                "trfdamount" => "required"
            ];
        }

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transcationDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        //___________check Advancement Date To Transaction Not Exceed
        $transactionDate = date('Y-m-d', strtotime($post->transcationDate));

        $cclDetails = DB::table('member_ccl')->where('id', $post->cclId)->where('status', 'Disbursed')->first();



        if ($transactionDate < $cclDetails->ccl_Date) {
            return response()->json(['status' => 'Fail', 'messages' => 'Transaction Date cannot be earlier than the CCL Advancement Date.']);
        }

        $cclpaymentId = DB::table('ccl_payments')->where('id', $post->updateId)->first();
        $gerenal_Ledger = DB::table('general_ledgers')->where('serialNo', $cclpaymentId->serialNo)->get();

        $members = DB::table('member_accounts')->where('accountNo', $post->membershipnumbers)->where('memberType', $post->cclmemberType)->first();


        //____________Get Saving Accounts
        $saving_account = DB::table('opening_accounts')
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
            ->where('opening_accounts.membertype', $members->memberType)
            ->where('opening_accounts.membershipno', $members->accountNo)
            ->first();


        if ($transactionDate < $saving_account->transactionDate) {
            return response()->json(['status' => 'Fail', 'message' => 'Transaction Date cannot be earlier than the Saving A/c Opening Date.']);
        }

        if ($saving_account) {
            if ($saving_account->groupCode && $saving_account->ledgerCode) {
                $saving_group = $saving_account->groupCode;
                $saving_ledger = $saving_account->ledgerCode;
            } else {
                return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
        }


        $memberType = $post->cclmemberType;
        $groupCode = '';
        $ledgerCode = '';
        $interestGroup = '';
        $interestLedger = '';

        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $serialNo = 'CCL' . time();


        if ($paymenttype === "Cash") {

            $cashbankgroup = $post->cashbankgroup;
            $cashbankledger = $post->cashbankledger;

            if (empty($cashbankgroup) && empty($cashbankledger)) {
                return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group/Ledger Not Found']);
            }

            DB::beginTransaction();
            try {

                $cclpaymentId = DB::table('ccl_payments')->where('id', $post->updateId)->first();
                DB::table('general_ledgers')->where('serialNo', $cclpaymentId->serialNo)->delete();
                DB::table('member_savings')->where('serialNo', $cclpaymentId->serialNo)->delete();
                DB::table('ccl_payments')->where('id', $post->updateId)->delete();


                $interest_amount = $post->trfd_interest_amount;

                //_______SOD A/c
                $id =  DB::table('ccl_payments')->insertGetId([
                    'serialNo' => $serialNo,
                    'type' => 'Withdraw',
                    'ccl_Id' => $cclDetails->id,
                    'transcationDate' => $transactionDate,
                    'memberType' => $memberType,
                    'membershipno' => $cclDetails->membership,
                    'ccl_account' => $cclDetails->cclNo,
                    'saving_account' => $saving_account->accountNo,
                    'transfer_amount' => $post->trfdamount,
                    'interest_amount' => $interest_amount,
                    'recovey_amount' => 0,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'payment_type' => $paymenttype,
                    'paymentgroup' => $cashbankgroup,
                    'paymentledger' => $cashbankledger,
                    'narration' => $post->narration,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'agentId' => '',
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $post->user()->id,
                ]);


                //__________________General Ledger Entries

                //__________SOD Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'CCL Withdraw',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Member Saving Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $saving_account->accountNo,
                    'accountNo' => $saving_account->membershipno,
                    'memberType' => $saving_account->membertype,
                    'groupCode' => $cashbankgroup,
                    'ledgerCode' => $cashbankledger,
                    'formName' => 'CCL Withdraw',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);

                if (!empty($interest_amount) && $interest_amount > 0) {
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $cclDetails->cclNo,
                        'accountNo' => $cclDetails->membership,
                        'memberType' => $cclDetails->memberType,
                        'groupCode' => $cashbankgroup,
                        'ledgerCode' => $cashbankledger,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Interest Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $cclDetails->accountNo,
                        'accountNo' => $cclDetails->membershipno,
                        'memberType' => $cclDetails->membertype,
                        'groupCode' => $interestGroup,
                        'ledgerCode' => $interestLedger,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);
                }

                DB::commit();

                $cclNo = $cclDetails->cclNo;
                return $this->Datareturn($memberType, $cclNo);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while Entered Record',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        } else {
            DB::beginTransaction();
            try {

                $cclpaymentId = DB::table('ccl_payments')->where('id', $post->updateId)->first();
                DB::table('general_ledgers')->where('serialNo', $cclpaymentId->serialNo)->delete();
                DB::table('member_savings')->where('serialNo', $cclpaymentId->serialNo)->delete();
                DB::table('ccl_payments')->where('id', $post->updateId)->delete();


                $interest_amount = $post->trfd_interest_amount;


                $id =  DB::table('ccl_payments')->insertGetId([
                    'serialNo' => $serialNo,
                    'type' => 'Withdraw',
                    'ccl_Id' => $cclDetails->id,
                    'transcationDate' => $transactionDate,
                    'memberType' => $memberType,
                    'membershipno' => $cclDetails->membership,
                    'ccl_account' => $cclDetails->cclNo,
                    'saving_account' => $saving_account->accountNo,
                    'transfer_amount' => $post->trfdamount,
                    'interest_amount' => $interest_amount,
                    'recovey_amount' => 0,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'narration' => $post->narration,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'agentId' => '',
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $post->user()->id,
                ]);


                //___________Member saving Table Entry
                DB::table('member_savings')->insertGetId([
                    'secheme_id' => $saving_account->accounttype,
                    'serialNo' => $serialNo,
                    'accountId' => $saving_account->accountNo,
                    'accountNo' => $saving_account->membershipno,
                    'memberType' => $saving_account->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'savingNo' =>  $saving_account->accountNo,
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Deposit',
                    'depositAmount' => $post->trfdamount,
                    'withdrawAmount' => 0,
                    'paymentType' => '',
                    'bank' => '',
                    'chequeNo' => 'CCL Limit',
                    'narration' => 'CCL TRFD Date ' . $transactionDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    // 'agentId' => ,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                //__________________General Ledger Entries

                //__________Interest Paid Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'CCL Trfd',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Member Saving Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $saving_account->accountNo,
                    'accountNo' => $saving_account->membershipno,
                    'memberType' => $saving_account->membertype,
                    'groupCode' => $saving_group,
                    'ledgerCode' => $saving_ledger,
                    'formName' => 'CCL Trfd',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $post->trfdamount,
                    'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);

                if (!empty($interest_amount) && $interest_amount > 0) {
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $cclDetails->cclNo,
                        'accountNo' => $cclDetails->membership,
                        'memberType' => $cclDetails->memberType,
                        'groupCode' => $groupCode,
                        'ledgerCode' => $ledgerCode,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $interest_amount,
                        'narration' =>  'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        // 'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);


                    // __________Member Saving Entries in General Entries
                    DB::table('general_ledgers')->insert([
                        'serialNo' => $serialNo,
                        'accountId' => $saving_account->accountNo,
                        'accountNo' => $saving_account->membershipno,
                        'memberType' => $saving_account->membertype,
                        'groupCode' => $interestGroup,
                        'ledgerCode' => $interestLedger,
                        'formName' => 'CCL Interest',
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $transactionDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $interest_amount,
                        'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                        'branchId' => session('branchId') ?? 1,
                        'agentId' => $post->agents,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                    ]);
                }

                DB::commit();

                $cclNo = $cclDetails->cclNo;
                return $this->Datareturn($memberType, $cclNo);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while Entered Record',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        }
    }

    public function checkRecoveryNoExceed(Request $post)
    {

        $rules = [
            "enteredAmount" => "required",
            "cclId" => "required",
            "cclmemberType" => "required",
            "transcationDate" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }



        $cclid = $post->cclId;
        $currentDate = date('Y-m-d', strtotime($post->transcationDate));
        $cclmemberType = $post->cclmemberType;
        $cclDetails = DB::table('member_ccl')->where('id', $cclid)->where('status', 'Disbursed')->first();

        $payments = DB::table('ccl_payments')
            ->where('ccl_payments.ccl_Id', $cclid)
            ->where('ccl_payments.memberType', $cclmemberType)
            ->whereDate('ccl_payments.transcationDate', '<=', $currentDate)
            ->get();

        $limit_amount = $cclDetails->ccl_amount;
        $withdraw_amount = $payments->sum('transfer_amount');
        $recovey_amount = $payments->sum('recovey_amount');

        if ($limit_amount || $withdraw_amount || $recovey_amount) {
            return response()->json([
                'status' => 'success',
                'messages' => `You Entered Amount Is Exceed Then Balance Limit`,
                'withdraw_amount' => $withdraw_amount,
                'recovey_amount' => $recovey_amount,
                'limit_amount' => $limit_amount,
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function cclrecoverUpdate(Request $post)
    {

        $rules = [
            "cclid" => "required",
            "rcclmemberType" => "required",
            "receipt_date" => "required",
            "principal" => "required",
            "rate_of_interest" => "required",
            // "interest_amount" => "required",
            "total_amount" => "required",
            // "receipt_amount" => "required",
            "groupCode" => 'required',
            "ledgerCode" => 'required',
            'updaterecoveryId' => 'required'
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->receipt_date)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        //___________check Advancement Date To Transaction Not Exceed
        $transactionDate = date('Y-m-d', strtotime($post->receipt_date));
        $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status', 'Disbursed')->first();


        if ($transactionDate < $cclDetails->ccl_Date) {
            return response()->json(['status' => 'Fail', 'message' => 'Transaction Date cannot be earlier than the CCL Advancement Date.']);
        }

        $memberType = $post->rcclmemberType;
        $groupCode = '';
        $ledgerCode = '';
        $interestGroup = '';
        $interestLedger = '';

        if ($memberType === 'Member') {
            $groupCode = 'MEM01';
            $ledgerCode = 'MEM178';
            $interestGroup = 'INCM001';
            $interestLedger = 'INM01';
        } elseif ($memberType === 'NonMember') {
            $groupCode = 'NON01';
            $ledgerCode = 'NON01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INN01';
        } else {
            $groupCode = 'STA02';
            $ledgerCode = 'STC01';
            $interestGroup = 'INCM001';
            $interestLedger = 'INS01';
        }

        $groupCode = '';
        $ledgerCode = '';

        if ($post->groupCode && $post->ledgerCode) {
            $groupCode = $post->groupCode;
            $ledgerCode = $post->ledgerCode;
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Code Not Found']);
        }



        $cclpaymentId = DB::table('ccl_payments')->where('id', $post->updaterecoveryId)->first();



        $interest_amount = 0;
        $receipt_amount = 0;

        if (!empty($post->interest_amount)) {
            $interest_amount = $post->interest_amount;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Reciept and Interest Amount One Required"]);
        }


        if (!empty($post->receipt_amount)) {
            $receipt_amount = $post->receipt_amount;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Reciept and Interest Amount One Required"]);
        }



        DB::beginTransaction();
        try {

            $serialNo = 'CCL'.time();
        // dd($serialNo);

            //__________Delete First Previouse Entry Then New Entry Update
            DB::table('general_ledgers')->where('serialNo', $cclpaymentId->serialNo)->delete();
            DB::table('ccl_payments')->where('id', $post->updaterecoveryId)->delete();


            $interest_amount = $post->interest_amount ?? null;

            $id =  DB::table('ccl_payments')->insertGetId([
                'serialNo' => $serialNo,
                'type' => 'Deposit',
                'ccl_Id' => $cclDetails->id,
                'transcationDate' => $transactionDate,
                'memberType' => $memberType,
                'membershipno' => $cclDetails->membership,
                'ccl_account' => $cclDetails->cclNo,
                'saving_account' => '',
                'transfer_amount' => 0,
                'recovey_amount' => $receipt_amount,
                'interest_amount' => $interest_amount,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'narration' => $post->receipt_narration,
                'branchId' => session('branchId') ? session('branchId') : 1,
                'agentId' => '',
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'updatedBy' => $post->user()->id,
            ]);



            //__________CCL Recovery Entries in General Entries
            if ($interest_amount > 0) {
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $groupCode,
                    'ledgerCode' => $ledgerCode,
                    'formName' => 'CCL Recovery',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $interest_amount,
                    'narration' =>  'CCL Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);


                // __________Cash/Bank Entries in General Entries
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $interestGroup,
                    'ledgerCode' => $interestLedger,
                    'formName' => 'CCL Recovery',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Dr',
                    'transactionAmount' => $interest_amount,
                    'narration' => 'CCL Interest Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);
            }


            //___________________________________General Ledger Entries________________________________________________________

            //__________CCL Recovery Entries in General Entries
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $cclDetails->cclNo,
                'accountNo' => $cclDetails->membership,
                'memberType' => $cclDetails->memberType,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'formName' => 'CCL Recovery',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $receipt_amount,
                'narration' =>  'CCL Interest Receipt Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                // 'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            // __________Cash/Bank Entries in General Entries
            DB::table('general_ledgers')->insert([
               'serialNo' => $serialNo,
                'accountId' => $cclDetails->cclNo,
                'accountNo' => $cclDetails->membership,
                'memberType' => $cclDetails->memberType,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'formName' => 'CCL Recovery',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $receipt_amount,
                'narration' => 'CCL TRFD Date ' . $post->transcationDate . ' From CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => $post->agents,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            DB::commit();

            $cclNo = $cclDetails->cclNo;

            return $this->Datareturn($memberType, $cclNo);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'Fail',
                'messages' => 'An error occurred while Entered Record',
                'error' => $e->getMessage(),
                'lines' => $e->getLine()
            ]);
        }
    }

    public function getcashbankledgercodes(Request $post)
    {
        $rules = [
            'group' => 'required'
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $groupCode = $post->group;
        $ledgers = DB::table('ledger_masters')->where('groupCode', $groupCode)
            ->where('ledgerCode', '!=', ['BANKFD01', 'HPSC43501151'])->where('status', 'Active')->orderBy('name', 'ASC')->get();

        if (!empty($ledgers)) {
            return response()->json(['status' => 'success', 'ledgers' => $ledgers]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Group Not Found']);
        }
    }

    public function sodledgerindexlist()
    {
        return view('report.sodledgers');
    }

    public function getsodaccountlist(Request $post)
    {
        $sod = $post->sod_account;
        $memberType = $post->memberType;
        $openingDate = date('Y-m-d', strtotime($post->openingDate));

        $sodaccounts = DB::table('member_ccl')->where('cclNo', 'LIKE', $sod . '%')->where('memberType', $memberType)->where('ccl_Date', '<=', $openingDate)->get();

        if (!empty($sodaccounts)) {
            return response()->json(['status' => 'success', 'sodaccounts' => $sodaccounts]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }


    public function getsodacc(Request $post)
    {
        $rules = [
            "sodaccount" => "required",
            "memberType" => "required",
            "openingDate" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $sodaccount = $post->sodaccount;
        $memberType = $post->memberType;
        $currentDate = date('Y-m-d', strtotime($post->openingDate));

        $getnames = DB::table('ccl_payments')->select('member_accounts.accountNo','member_accounts.memberType','member_accounts.name')
            ->leftJoin('member_accounts', function ($join) {
                $join->on(DB::raw('CONVERT(member_accounts.accountNo, CHAR(255) CHARACTER SET utf8mb4)'), '=', DB::raw('CONVERT(ccl_payments.membershipno, CHAR(255) CHARACTER SET utf8mb4)'))
                    ->on(DB::raw('CONVERT(member_accounts.memberType, CHAR(255) CHARACTER SET utf8mb4)'), '=', DB::raw('CONVERT(ccl_payments.memberType, CHAR(255) CHARACTER SET utf8mb4)'));
            })
            ->where('ccl_payments.ccl_account', $sodaccount)
            ->where('ccl_payments.memberType', $memberType)
            ->first();

        $sodDetails = DB::table('ccl_payments')
            ->select(
                'ccl_payments.*',
                'member_ccl.id as sodId',
                'member_ccl.year',
                'member_ccl.month',
                'member_ccl.days',
                'member_ccl.interest'
            )
            ->leftJoin('member_ccl', 'member_ccl.id', '=', 'ccl_payments.ccl_Id')
            ->where('ccl_payments.ccl_account', $sodaccount)
            ->where('ccl_payments.memberType', $memberType)
            ->where('ccl_payments.transcationDate', '<=', $currentDate)
            ->get();

        $opening_amount = 0;
        $interest_recoverable = 0;
        $monthlyData = [];

        $previousClosing = 0;

        foreach ($sodDetails as $row) {
            $txnDate = new DateTime($row->transcationDate);
            $currentDateObj = new DateTime($currentDate);
            $interval = $txnDate->diff($currentDateObj);
            $totalMonths = ($interval->y * 12) + $interval->m;

            $monthIterator = clone $txnDate;
                    $rateOfInterest = $row->interest ?? 0;


            for ($i = 0; $i <= $totalMonths; $i++) {
                $monthKey = $monthIterator->format('Y-m');
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthIterator->format('m'), $monthIterator->format('Y'));

                if (!isset($monthlyData[$monthKey])) {
                    $monthlyData[$monthKey] = [
                        'month' => $monthIterator->format('F Y'),
                        'total_withdraws' => 0,
                        'total_recovey' => 0,
                        'opening_amount' => 0, // Set opening_amount to previous month's closing
                        'interest_recoverable' => 0,
                        'interest_received' => 0,
                        'days_in_month' => $daysInMonth,
                        'balances' =>$previousClosing,
                        'closing' => $previousClosing,
                        'rateOfInterest' => $rateOfInterest
                    ];
                }

                // Only add transactions that belong to this month
                if ($txnDate->format('Y-m') === $monthKey) {
                    $monthlyData[$monthKey]['total_withdraws'] += $row->transfer_amount ?? 0;
                    $monthlyData[$monthKey]['total_recovey'] += $row->recovey_amount ?? 0;
                    $monthlyData[$monthKey]['interest_received'] += $row->interest_amount ?? 0;

                    $monthlyData[$monthKey]['balances'] = $monthlyData[$monthKey]['total_withdraws'];

                    $monthlyInterest = 0;

                    if ($previousClosing > 0) {
                        $monthlyInterest = ((($previousClosing) * $rateOfInterest * $daysInMonth) / (100 * 365));
                    } else {
                        $monthlyInterest = ((($monthlyData[$monthKey]['balances']) * $rateOfInterest * $daysInMonth) / (100 * 365));
                    }

                    $monthlyData[$monthKey]['interest_recoverable'] = $monthlyInterest;
                    $monthlyData[$monthKey]['closing'] = $monthlyData[$monthKey]['opening_amount'] +  $monthlyData[$monthKey]['total_withdraws'] - $monthlyData[$monthKey]['total_recovey'] - $monthlyData[$monthKey]['interest_received'] + $monthlyData[$monthKey]['interest_recoverable'];
                    $previousClosing = $monthlyData[$monthKey]['closing'];
                }

                $monthIterator->modify('+1 month');
            }
        }

        $allData = array_values($monthlyData);

        return response()->json(['status' => 'success', 'allData' => $allData,'getnames' => $getnames]);
    }

    public function closedsodaccount(Request $post){
        $rules = [
            "id" => "required",
            "ccl_account" => "required",
            "member_type" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check All Fields']);
        }

        $cclid = $post->id;
        $cclNo = $post->ccl_account;
        $memberType = $post->member_type;


        $cclDetails = DB::table('member_ccl')->where('id',$cclid)->first();

        if(!empty($cclDetails)){
            $type = $cclDetails->Types;
            switch ($type) {
                case "FD":

                    $fdid = explode(',', $cclDetails->fdId);

                    DB::beginTransaction();
                    try {

                        DB::table('member_fds_scheme')->whereIn('id', $fdid)->where('status', 'Pluge')->update(['status' => 'Active']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Closed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }

                    break;

                case "RD":

                    $rd_id = explode(',', $cclDetails->rd_id);


                    DB::beginTransaction();
                    try {

                        DB::table('re_curring_rds')->whereIn('id', $rd_id)->where('status', 'Pluge')->update(['status' => 'Active']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Closed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }



                    break;

                case "DailyDeposit":

                    $dailyid = explode(',', $cclDetails->dailyId);


                    DB::beginTransaction();
                    try {

                        DB::table('daily_collections')->whereIn('id', $dailyid)->where('status', 'Pluge')->update(['status' => 'Active']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Closed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }
                    break;

                default:
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    break;
            }
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }


    public function unclosedsodaccount(Request $post){
        $rules = [
            "id" => "required",
            "ccl_account" => "required",
            "member_type" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check Fields']);
        }


        $cclid = $post->id;
        $cclNo = $post->ccl_account;
        $memberType = $post->member_type;


        $cclDetails = DB::table('member_ccl')->where('id',$cclid)->first();

        if(!empty($cclDetails)){
            $type = $cclDetails->Types;
            switch ($type) {
                case "FD":

                    $fdid = explode(',', $cclDetails->fdId);

                    DB::beginTransaction();
                    try {

                        DB::table('member_fds_scheme')->whereIn('id', $fdid)->where('status', 'Active')->update(['status' => 'Pluge']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Disbursed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }

                    break;

                case "RD":

                    $rd_id = explode(',', $cclDetails->rd_id);


                    DB::beginTransaction();
                    try {

                        DB::table('re_curring_rds')->whereIn('id', $rd_id)->where('status', 'Active')->update(['status' => 'Pluge']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Disbursed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }



                    break;

                case "DailyDeposit":

                    $dailyid = explode(',', $cclDetails->dailyId);


                    DB::beginTransaction();
                    try {

                        DB::table('daily_collections')->whereIn('id', $dailyid)->where('status', 'Active')->update(['status' => 'Pluge']);
                        DB::table('member_ccl')->where('id', $cclid)->update(['status' => 'Disbursed']);

                        DB::commit();

                        $cclDetails = DB::table('member_ccl')
                        ->select(
                            'ccl_payments.ccl_Id',
                            'member_ccl.id as ids',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.ccl_end_Date',
                            DB::raw('SUM(CASE WHEN ccl_payments.transfer_amount IS NOT NULL THEN ccl_payments.transfer_amount ELSE 0 END) as trfd_amount'),
                            DB::raw('SUM(CASE WHEN ccl_payments.recovey_amount IS NOT NULL THEN ccl_payments.recovey_amount ELSE 0 END) as recovery_amount')
                        )
                        ->leftJoin('ccl_payments', 'ccl_payments.ccl_Id', '=', 'member_ccl.id')  // Corrected the join
                        ->where('member_ccl.memberType', $memberType)
                        ->where('member_ccl.cclNo', $cclNo)
                        // ->where('member_ccl.status', 'Closed')
                        ->groupBy(
                            'ccl_payments.ccl_Id',
                            'member_ccl.ccl_Date',
                            'member_ccl.memberType',
                            'member_ccl.membership',
                            'member_ccl.cclNo',
                            'member_ccl.year',
                            'member_ccl.month',
                            'member_ccl.days',
                            'member_ccl.Types',
                            'member_ccl.interest',
                            'member_ccl.ccl_amount',
                            'member_ccl.status',
                            'member_ccl.id',
                            'member_ccl.ccl_end_Date'
                        )
                        ->first();

                        $allmemberlist = DB::table('member_accounts')->where('memberType', $memberType)->where('accountNo', $cclDetails->membership)->where('status', 'Active')->first();

                        if (!empty($cclDetails) && !empty($cclDetails)) {
                            return response()->json(['status' => 'success', 'cclDetails' => $cclDetails, 'allmemberlist' => $allmemberlist]);
                        } else {
                            return response()->json(['status', 'Fail', 'messages' => 'Record Not Found']);
                        }


                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'An error occurred while processing the record.',
                            'error' => $e->getMessage(),
                            'lines' => $e->getLine()
                        ]);
                    }
                    break;

                default:
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    break;
            }

        }

    }

}
