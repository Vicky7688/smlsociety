<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchemeMaster;
use App\Models\MemberAccount;
use App\Models\GroupMaster;
use App\Models\DailyCollection;
use App\Models\LedgerMaster;
use App\Models\GeneralLedger;
use App\Models\DailyCollectionsaving;
use App\Models\opening_accounts;
use App\Models\AgentMaster;
use App\Models\DailySavingInstallment;
use App\Models\SessionMaster;
use DateTime;
use DateInterval;
use App\Models\MemberSaving;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DailyCollectionController extends Controller
{
    public function dailysavingcollectionindex()
    {
        // $schemes = SchemeMaster::where('secheme_type','DailyDeposit')->get();
        // ['scheme'=>$schemes,'agents' => $agents]
        $groups = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $data['agents'] = $agents;
        $data['groups'] = $groups;
        return view('transaction.dailycollection', $data);
    }

    public function getddsaccountslist(Request $post)
    {
        $dds_account = $post->daliySavingAccount;
        $membertype = $post->memberType;

        if ($dds_account && $membertype) {
            $daily_account_list = DB::table('opening_accounts')
                ->where('membertype', $membertype)
                ->where('accountNo', 'LIKE', $dds_account . '%')
                ->where('accountname', '=', 'DailyDeposit')
                ->get();

            if (!empty($daily_account_list)) {
                return response()->json(['status' => 'success', 'dailyaccounts' => $daily_account_list]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'This MemberType Has Not Account No']);
        }
    }

    public function getddsaccount(Request $post)
    {
        $accountNumber = $post->accountNumber;
        $memberType = $post->memberType;
        $daily_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'member_accounts.accountNo as membership',
                'member_accounts.name as customer_name',
                'scheme_masters.id as sch_id',
                'scheme_masters.name as scheme_name',
                'scheme_masters.memberType as mtype',
                'scheme_masters.days',
                'member_accounts.fatherName',
                'member_accounts.address',
                'member_accounts.photo',
                'scheme_masters.penaltyInterest',
                'member_accounts.memberType as type'
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.accountNo', $accountNumber)
            ->where('opening_accounts.membertype', $memberType)
            ->where('member_accounts.memberType','=',$memberType)
            ->where('opening_accounts.accountname', '=', 'DailyDeposit')
            ->where('opening_accounts.status', '=', 'Active')
            ->orderBy('opening_accounts.accountNo','ASC')
            ->first();
        // dd($daily_account);

        $old_account = DB::table('daily_collections')
            ->select(
                'daily_collections.*', 'member_accounts.accountNo as membership', 'member_accounts.photo',
                'daily_collectionsavings.dailyaccountid',
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collections.membershipno')
            ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
            ->where('daily_collections.account_no', $post->accountNumber)
            // ->where('daily_collections.membertype', '=', $daily_account->membertype)
            ->where('daily_collections.membertype', $post->memberType)
            ->orderBy('daily_collections.account_no','ASC')
            ->first();


        if (!empty($daily_account) || !empty($old_account)) {
            return response()->json(['status' => 'success', 'daily_account' => $daily_account, 'old_account' => $old_account]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }


    public function insertdailysavingaccount(Request $post)
    {

        $rules = [
            'opening_date' => 'required',
            'member_type' => 'required',
            'daily_ac_no' => 'required|numeric',
            'membership_no' => 'required|numeric',
            'sch_id' => 'required',
            'daily_amount' => 'required|numeric',
            'rate_of_interest' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'total_interest' => 'required|numeric',
            'maturity_amount' => 'required|numeric',
            'maturity_date' => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        //_________check Session
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->opening_date)));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $opening_accounts = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'schmeaster.id as sch_id',
                'schmeaster.scheme_code',
                'ledger_masters.reference_id',
                'ledger_masters.ledgerCode',
                'ledger_masters.groupCode',
                'refSchemeMaster.scheme_code as ref_scheme_code',
                'refSchemeMaster.months',
                'schmeaster.penaltyInterest',
            )
            ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
            ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
            ->where('opening_accounts.accountNo', $post->daily_ac_no)
            ->where('opening_accounts.accountname', 'DailyDeposit')
            ->where('opening_accounts.membertype', $post->member_type)
            ->where('opening_accounts.status', 'Active')
            ->first();

        $openingdatee = date('Y-m-d', strtotime($post->opening_date));

        if ($opening_accounts->transactionDate > $openingdatee) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Check Account Open Date"], 400);
        }


        if ($opening_accounts->groupCode && $opening_accounts->ledgerCode) {
            $scheme_group_code = $opening_accounts->groupCode;
            $scheme_ledger_code = $opening_accounts->ledgerCode;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Group Code And Ledger Code Not Found"], 400);
        }

        $rand = "DDS" . time();
        $preAccount = DB::table('daily_collections')
            ->where('account_no', $post->daily_ac_no)
            ->where('membertype', $post->member_type)
            ->first();

        if ($preAccount) {
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {
            DB::beginTransaction();
            try {
                $dailyaccount = new DailyCollection();
                $dailyaccount->serialno = $rand;
                $dailyaccount->membertype = $post->member_type;
                $dailyaccount->customer_name = $post->member_name;
                $dailyaccount->membershipno = $post->membership_no;
                $dailyaccount->account_no = $post->daily_ac_no;
                $dailyaccount->opening_date = $openingdatee;
                $dailyaccount->scheme_name = $post->scheme_type;
                $dailyaccount->lockindays = $post->lock_in_days;
                $dailyaccount->lockindate = $post->lock_in_date;
                $dailyaccount->amount = $post->daily_amount;
                $dailyaccount->penelty = $post->penality_ineterst;
                $dailyaccount->interest_amount = $post->total_interest;
                $dailyaccount->schemeid = $post->sch_id;
                $dailyaccount->scheme_groupCode = $scheme_group_code;
                $dailyaccount->scheme_ledgerCode = $scheme_ledger_code;
                $dailyaccount->interest = $post->rate_of_interest;
                $dailyaccount->days = $post->days;

                $dailyaccount->maturitydate = date('Y-m-d', strtotime($post->maturity_date));
                $dailyaccount->principalamount = $post->total_amount;
                $dailyaccount->maturityamount = $post->maturity_amount;
                $dailyaccount->actualMaturitydate = null;
                $dailyaccount->ActualyMaturityAmount = null;
                $dailyaccount->status = 'Active';
                $dailyaccount->branchId = session('branchId') ? session('branchId') : 1;
                $dailyaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $dailyaccount->updatedBy = $post->user()->id;
                $dailyaccount->agentId = $post->agentId;
                $dailyaccount->save();

                $id = $dailyaccount->id;

                // Store Installments
                $interest = $post->rate_of_interest;
                $amount = $post->daily_amount;
                $days = $post->days;
                $startDate = new DateTime($openingdatee);

                for ($i = 1; $i <= $days; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $installmentsdata = new DailySavingInstallment;
                    $installmentsdata->serialNo = $dailyaccount->serialno;
                    $installmentsdata->daily_id = $id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = session('branchId') ? session('branchId') : 1;
                    $installmentsdata->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $installmentsdata->updatedBy = $post->user()->id;
                    $installmentsdata->save();

                    // Increment date by 1 day
                    $startDate->add(new DateInterval("P1D"));
                }

                DB::commit();
                return response()->json(['status' => 'success', 'messages' => 'Account Inserted Successfully', 'dailyaccount' => $dailyaccount]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function deleteddssaving(Request $post)
    {
        $dds_id = $post->id;
        $dds_account = DB::table('daily_collections')->where('id', $dds_id)->where('status', 'Active')->first();

        $date = $dds_account->opening_date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $daily_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'member_accounts.accountNo as membership',
                'member_accounts.name as customer_name',
                'scheme_masters.id as sch_id',
                'scheme_masters.name as scheme_name',
                'scheme_masters.memberType as mtype',
                'scheme_masters.days',
                'member_accounts.fatherName',
                'member_accounts.address',
                'member_accounts.photo',
                'scheme_masters.penaltyInterest',
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.accountNo', $dds_account->account_no)
            ->where('opening_accounts.membertype', $dds_account->membertype)
            ->where('opening_accounts.accountname', '=', 'DailyDeposit')
            ->where('opening_accounts.status', '=', 'Active')
            ->first();



        if ($start_date >= $date ||  $date <= $end_date) {
            if (is_null($dds_account)) {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            } else {
                DB::beginTransaction();
                try {
                    DB::table('dailysavinginstallment')->where('daily_id', $dds_account->id)->delete();

                    DB::table('daily_collections')->where('id', $dds_id)->delete();

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'messages' => 'Record Deleted Successfully',
                        'daily_account' => $daily_account
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
        } else {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Session Date With Transaction Date'
            ]);
        }
    }

    public function editddssaving(Request $post)
    {
        $dds_id = $post->id;
        $dds_account = DB::table('daily_collections')->where('id', $dds_id)->where('status', 'Active')->first();

        $date = $dds_account->opening_date;


        $session_master = SessionMaster::find(Session::get('sessionId'));

            if (is_null($dds_account)) {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'Record Not Found'
                ], 404);
            } else {
                return response()->json([
                    'status' => 'success',
                    'dds_account' => $dds_account
                ]);
            }

    }

    public function updatedailysavingaccount(Request $post)
    {
        $rules = [
            'ac_id' => 'required',
            'opening_date' => 'required',
            'member_type' => 'required',
            'daily_ac_no' => 'required|numeric',
            'membership_no' => 'required|numeric',
            'sch_id' => 'required',
            'daily_amount' => 'required|numeric',
            'rate_of_interest' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'total_interest' => 'required|numeric',
            'maturity_amount' => 'required|numeric',
            'maturity_date' => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'messages' => $validator->errors()
            ]);
        }

        //_________check Session
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->opening_date)));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $opening_accounts = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'schmeaster.id as sch_id',
                'schmeaster.scheme_code',
                'ledger_masters.reference_id',
                'ledger_masters.ledgerCode',
                'ledger_masters.groupCode',
                'refSchemeMaster.scheme_code as ref_scheme_code',
                'refSchemeMaster.months',
                'schmeaster.penaltyInterest',
                'member_accounts.accountNo as mno',
                'member_accounts.photo',
            )
            ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
            ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
            ->where('opening_accounts.accountNo', $post->daily_ac_no)
            ->where('opening_accounts.membertype',$post->member_type)
            ->where('member_accounts.memberType',$post->member_type)
            ->where('opening_accounts.accountname', 'DailyDeposit')
            ->where('opening_accounts.status', 'Active')
            ->first();

        $openingdatee = date('Y-m-d', strtotime($post->opening_date));

        if ($opening_accounts->transactionDate > $openingdatee) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Check Account Open Date"], 400);
        }


        if ($opening_accounts->groupCode && $opening_accounts->ledgerCode) {
            $scheme_group_code = $opening_accounts->groupCode;
            $scheme_ledger_code = $opening_accounts->ledgerCode;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Group Code And Ledger Code Not Found"], 400);
        }

        $dds_id = $post->ac_id;

        $rand = "DDS" . time();
        $preAccount = DB::table('daily_collections')
            ->where('account_no', $post->daily_ac_no)
            ->where('membertype', $post->member_type)
            ->where('id', '!=', $dds_id)
            ->first();

        if ($preAccount) {
            return response()->json(['status' => 'fail', 'messages' => 'Account already exists']);
        }

        if ($opening_accounts) {
            DB::beginTransaction();
            try {


                $dds_accounts = $post->daily_ac_no;

                $this->ddsdeleteInstallments($dds_id, $dds_accounts);


                $dailyaccount = new DailyCollection();
                $dailyaccount->serialno = $rand;
                $dailyaccount->membertype = $post->member_type;
                $dailyaccount->customer_name = $post->member_name;
                $dailyaccount->membershipno = $post->membership_no;
                $dailyaccount->account_no = $post->daily_ac_no;
                $dailyaccount->opening_date = $openingdatee;
                $dailyaccount->scheme_name = $post->scheme_type;
                $dailyaccount->lockindays = $post->lock_in_days;
                $dailyaccount->lockindate = $post->lock_in_date;
                $dailyaccount->amount = $post->daily_amount;
                $dailyaccount->penelty = $post->penality_ineterst;
                $dailyaccount->interest_amount = $post->total_interest;
                $dailyaccount->schemeid = $post->sch_id;
                $dailyaccount->scheme_groupCode = $scheme_group_code;
                $dailyaccount->scheme_ledgerCode = $scheme_ledger_code;
                $dailyaccount->interest = $post->rate_of_interest;
                $dailyaccount->days = $post->days;
                $dailyaccount->agentId = $post->agentId;
                $dailyaccount->maturitydate = date('Y-m-d', strtotime($post->maturity_date));
                $dailyaccount->principalamount = $post->total_amount;
                $dailyaccount->maturityamount = $post->maturity_amount;
                $dailyaccount->actualMaturitydate = null;
                $dailyaccount->ActualyMaturityAmount = null;
                $dailyaccount->status = 'Active';
                $dailyaccount->branchId = session('branchId') ? session('branchId') : 1;
                $dailyaccount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $dailyaccount->updatedBy = $post->user()->id;
                $dailyaccount->save();

                $id = $dailyaccount->id;

                // Store Installments
                $interest = $post->rate_of_interest;
                $amount = $post->daily_amount;
                $days = $post->days;
                $startDate = new DateTime($openingdatee);

                for ($i = 1; $i <= $days; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $installmentsdata = new DailySavingInstallment;
                    $installmentsdata->serialNo = $dailyaccount->serialno;
                    $installmentsdata->daily_id = $id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = session('branchId') ? session('branchId') : 1;
                    $installmentsdata->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $installmentsdata->updatedBy = $post->user()->id;
                    $installmentsdata->save();

                    // Increment date by 1 day
                    $startDate->add(new DateInterval("P1D"));
                }

                DB::commit();
                return response()->json(['status' => 'success', 'messages' => 'Account Inserted Successfully', 'dailyaccount' => $dailyaccount,'opening_accounts' => $opening_accounts]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'An error occurred while deleting the record',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function ddsdeleteInstallments($dds_id, $dds_accounts)
    {
        $dds_account = DB::table('daily_collections')->where('id', $dds_id)->where('account_no', $dds_accounts)->where('status', 'Active')->first();

        if (is_null($dds_account)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('dailysavinginstallment')->where('daily_id', $dds_account->id)->delete();
                DB::table('daily_collections')->where('id', $dds_id)->where('account_no', $dds_accounts)->where('status', 'Active')->delete();
                DB::commit();
                return response()->json([
                    'status' => 'success',
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
    }


    public function getddreceivedsaccountslist(Request $post)
    {
        $dds_account = $post->daliySavingAccount;
        $membertype = $post->memberType;

        if ($dds_account && $membertype) {
            $daily_account_list = DB::table('daily_collections')
                ->where('membertype', $membertype)
                ->where('account_no', 'LIKE', $dds_account . '%')
                ->get();

            if (!empty($daily_account_list)) {
                return response()->json(['status' => 'success', 'dailyaccounts' => $daily_account_list]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'This MemberType Has Not Account No']);
        }
    }


    public function getreceievedddsaccount(Request $post)
    {
        $accountNumber = $post->accountNumber;
        $membertype = $post->memberType;
        $account_number = DB::table('daily_collections')
            ->select(
                'daily_collections.id',
                'daily_collections.customer_name as name',
                'daily_collections.account_no',
                'daily_collections.membershipno',
                'daily_collections.opening_date',
                'daily_collections.amount',
                'daily_collections.status',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.scheme_name',
                'member_accounts.accountNo as membership',
                'member_accounts.photo',
                'daily_collectionsavings.dailyaccountid',
                'daily_collections.ActualyMaturityAmount',
                DB::raw('SUM(CASE WHEN daily_collectionsavings.deposit IS NOT NULL THEN daily_collectionsavings.deposit ELSE 0 END) deposit_amount')
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collections.membershipno')
            ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
            ->where('daily_collections.account_no', $accountNumber)
            ->where('daily_collections.membertype', $membertype)
            ->groupBy(
                'daily_collections.customer_name',
                'daily_collections.account_no',
                'daily_collections.membershipno',
                'daily_collections.opening_date',
                'daily_collections.amount',
                'daily_collections.interest',
                'daily_collections.days',
                'member_accounts.accountNo',
                'member_accounts.photo',
                'daily_collectionsavings.dailyaccountid',
                'daily_collections.status',
                'daily_collections.scheme_name',
                'daily_collections.ActualyMaturityAmount',
                'daily_collections.id',
            )
            ->first();

        if (!empty($account_number)) {
            return response()->json(['status' => 'success', 'account_number' => $account_number]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function ddsreceivedledger(Request $post)
    {
        $groups_code = $post->groups_code;
        if ($groups_code) {
            $ledgers = LedgerMaster::where('groupCode', $groups_code)->where('ledgerCode', '!=', 'BANKFD01')->where('status', 'Active')->orderBy('name', 'ASC')->get();

            if (!empty($ledgers)) {
                return response()->json([
                    'status' => 'success',
                    'ledgers' => $ledgers
                ]);
            } else {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Ledger Not Found'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Group Not Found'
            ]);
        }
    }

    public function dailysavingreceived(Request $post)
    {
        $rules = [
            "receiveamountaccount" => "required|numeric",
            "receiveaccounttype" => "required",
            "received_amount_date" => "required",
            "receive_amount" => "required",
            "payment_type" => "required",
            "payment_bank" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        //_________check Session
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->received_amount_date)));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $account = $post->receiveamountaccount;
        $daily_account = DB::table('daily_collections')
            ->select('daily_collections.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
            ->where('account_no', $account)
            ->first();

        $date = date('Y-m-d', strtotime($post->received_amount_date));
        //___________Check Account Group Or Ledger Code
        if ($daily_account->scheme_groupCode && $daily_account->scheme_ledgerCode) {
            $scheme_group_code = $daily_account->scheme_groupCode;
            $scheme_ledger_code = $daily_account->scheme_ledgerCode;
        } else {
            return response()->json(['status' => 'fail', 'messages' => "Account Group Code And Ledger Code Not Found"], 400);
        }


        if ($post->payment_type && $post->payment_bank) {
            $cashBankGroup = $post->payment_type;
            $cashBankLedger = $post->payment_bank;
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group Code Not Found']);
        }

        $installments = DailySavingInstallment::where(['daily_id' => $daily_account->id])->orderBy('id', 'desc')->first();
        $paid_amount = DailySavingInstallment::where(['daily_id' => $daily_account->id])->sum('paid_amount');

        $deposit_amount = $post->receive_amount;
        $daily_installment_amount = $installments->amount;
        $no_of_installments = $installments->intallment_no;
        $total_amount = $daily_installment_amount * $no_of_installments;
        $balance_amount = $total_amount - $paid_amount;


        // if ($deposit_amount <= $balance_amount || $balance_amount == 0) {
            $dailyToPay = $deposit_amount / $daily_installment_amount;

            // if ($dailyToPay <= 0) {
            //     return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given Daily payment.']);
            // } elseif ($dailyToPay > $no_of_installments) {
            //     return response()->json(['status' => 'fail', 'messages' => 'Amount is not perfect for ' . $no_of_installments . ' Daily']);
            // } else {
                $paymentSuccess = false;
                $penaltyApplied = false;
                $daily_account_details = DailyCollection::where(['account_no' => $account])->first();

                do {
                    $generalLedgers = "DDS" . time();
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);

                DB::beginTransaction();
                try {
                    //_______________DDS Receipt
                    $lastInsertedId = DB::table('daily_collectionsavings')->insertGetId([
                        "serialNo" => $generalLedgers,
                        "dailyaccountid" => $daily_account_details->id,
                        "membershipno"  =>  $daily_account_details->membershipno,
                        "sch_id"  =>  $daily_account_details->schemeid,
                        "account_no"  =>  $daily_account_details->account_no,
                        "receipt_date" => $date,
                        "deposit"  => $post->receive_amount,
                        'type' => 'Deposit',
                        'payment_mode' => $cashBankGroup,
                        'bank_name' => $cashBankLedger,
                        'memberType' => $daily_account_details->membertype,
                        "cheque_no" => 0,
                        'groupcode' => $scheme_group_code,
                        'ledgercode' => $scheme_ledger_code,
                        "narration" => $post->narration ?? '',
                        "branchId" => session('branchId') ? session('branchId') : 1,
                        "sessionId" => session('sessionId') ? session('sessionId') : 1,
                        "updatedBy" => $post->user()->id,
                        "agentId" => $post->agentId
                    ]);

                    //__________DDS Installment
                    for ($i = 1; $i <= $dailyToPay; $i++) {
                        $distributedPayment = min($daily_installment_amount, $deposit_amount);
                        $deposit_amount -= $distributedPayment;
                        $query = DailySavingInstallment::where(['daily_id' => $daily_account->id, 'payment_status' => 'pending'])->first();

                        if ($query && $query->payment_status === "pending") {
                            $query->payment_date = $date;
                            if (!$penaltyApplied) {
                                $query->panelty = 0;
                                $penaltyApplied = true;
                            }
                            $query->paid_amount = $distributedPayment;
                            $query->panelty = 0;
                            $query->recpt_id = $lastInsertedId;
                            $query->payment_status = "paid";
                            $query->serialNo = $generalLedgers;
                            $query->save();
                            $paymentSuccess = true;
                        }
                    }

                    //__________________________Gerenal Ledger DDS Entry_____________________

                    //___________DDS Amount Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $generalLedgers;
                    $genral_ledger->accountId = $daily_account_details->account_no;
                    $genral_ledger->accountNo = $daily_account_details->membershipno;
                    $genral_ledger->memberType = $daily_account_details->membertype;
                    $genral_ledger->groupCode = $scheme_group_code;
                    $genral_ledger->ledgerCode = $scheme_ledger_code;
                    $genral_ledger->formName = "DDS";
                    $genral_ledger->referenceNo = $lastInsertedId;
                    $genral_ledger->transactionDate = $date;
                    $genral_ledger->transactionType = "Cr";
                    $genral_ledger->transactionAmount = $post->receive_amount;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agentId;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();

                    //____________Cash/Bank Entry
                    $genral_ledger = new GeneralLedger;
                    $genral_ledger->serialNo = $generalLedgers;
                    $genral_ledger->accountId = $daily_account_details->account_no;
                    $genral_ledger->accountNo = $daily_account_details->membershipno;
                    $genral_ledger->memberType = $daily_account_details->membertype;
                    $genral_ledger->formName = "DDS";
                    $genral_ledger->groupCode = $cashBankGroup;
                    $genral_ledger->ledgerCode = $cashBankLedger;
                    $genral_ledger->referenceNo = $lastInsertedId;
                    $genral_ledger->transactionDate = $date;
                    $genral_ledger->transactionType = "Dr";
                    $genral_ledger->transactionAmount = $post->receive_amount;
                    $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $genral_ledger->agentId = $post->agentId;
                    $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $genral_ledger->updatedBy = $post->user()->id;
                    $genral_ledger->save();

                    $changestatus = DailySavingInstallment::where(['daily_id' => $daily_account_details->id])->orderBy('intallment_no', 'desc')->first();
                    if ($changestatus->payment_status == "paid") {
                        $moodifystatus = DailyCollection::where(['id' => $daily_account_details->id])->update(['status' => 'Active']);
                    }

                    DB::commit();
                    $accountNumber = $post->receiveamountaccount;
                    $membertype = $post->receiveaccounttype;
                    $account_number = $this->Balances($accountNumber, $membertype);



                    return response()->json([
                        'status' => 'success',
                        'rd_account' => $daily_account,
                        'deposit_amount' => $deposit_amount,
                        'account_number' => $account_number,
                        'messages' => "Installment Paid Successfully !!"
                    ]);


                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => "fail", "messages" => "Some Technical issue occurred", 'error' => $e->getMessage()]);
                }
        //     }
        // }
    }
    public function Balances($accountNumber, $membertype)
    {
        $account_number = DB::table('daily_collections')
            ->select(
                'daily_collections.customer_name as name',
                'daily_collections.account_no',
                'daily_collections.membershipno',
                'daily_collections.opening_date',
                'daily_collections.amount',
                'daily_collections.status',
                'daily_collections.interest',
                'daily_collections.days',
                'daily_collections.scheme_name',
                'member_accounts.accountNo as membership',
                'member_accounts.photo',
                'daily_collectionsavings.dailyaccountid',
                DB::raw('SUM(CASE WHEN daily_collectionsavings.deposit IS NOT NULL THEN daily_collectionsavings.deposit ELSE 0 END) deposit_amount')
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collections.membershipno')
            ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
            ->where('daily_collections.account_no', $accountNumber)
            ->where('daily_collections.membertype', $membertype)
            ->groupBy(
                'daily_collections.customer_name',
                'daily_collections.account_no',
                'daily_collections.membershipno',
                'daily_collections.opening_date',
                'daily_collections.amount',
                'daily_collections.interest',
                'daily_collections.days',
                'member_accounts.accountNo',
                'member_accounts.photo',
                'daily_collectionsavings.dailyaccountid',
                'daily_collections.status',
                'daily_collections.scheme_name'
            )
            ->first();
        return $account_number;
    }

    public function viewdepositeamount(Request $post)
    {
        $account_no = $post->account_number;
        $memberType = $post->memberType;

        $account_number = DB::table('daily_collectionsavings')
    ->select(
        'daily_collectionsavings.*',
        'daily_collectionsavings.id as ids',
        'daily_collections.id as id',
        'daily_collections.account_no as acc',
        'daily_collections.memberType',
        'daily_collections.status'
    )
    ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
    ->where('daily_collectionsavings.account_no', $account_no)
    ->where('daily_collectionsavings.memberType', $memberType)
    ->where('daily_collectionsavings.deposit', '>', 0) // Excludes 0 and NULL
    ->get();



        if (!empty($account_number)) {
            return response()->json(['status' => 'success', 'account_number' => $account_number]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'No Any Installments Paid']);
        }
    }

    public function viewdailyinstallments(Request $post)
    {
        $account_no = $post->account_number;
        $memberType = $post->memberType;

        $account_number = DB::table('daily_collections')
            ->where('account_no', $account_no)
            ->where('membertype', $memberType)
            ->first();

        if (!empty($account_number)) {
            $installments = DB::table('dailysavinginstallment')
                ->where('daily_id', $account_number->id)
                ->orderBy('intallment_no', 'ASC')
                ->get();


            return response()->json(['status' => 'success', 'installments' => $installments]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function dailyinstallmentsdelete(Request $post)
    {
        $id = $post->id;

        // Retrieve receipt details
        $receipt_id = DB::table('daily_collectionsavings')->where('id', $id)->first();


        $date = $receipt_id->receipt_date;
        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        if ($start_date > $date ||  $date < $end_date) {
            $account = $receipt_id->account_no;
        } else {
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
            $daily_account = DB::table('daily_collections')
                ->select('daily_collections.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('account_no', $account)
                ->first();

            // Delete general ledger records associated with this receipt
            DB::table('general_ledgers')
                ->where('serialNo', $receipt_id->serialNo)
                ->where('referenceNo', $receipt_id->id)
                ->delete();

            // Update installments if any exist for this receipt
            $installmentsUpdated = DB::table('dailysavinginstallment')
                ->where('recpt_id', $receipt_id->id)
                ->exists();

            if ($installmentsUpdated) {
                DB::table('dailysavinginstallment')
                    ->where('recpt_id', $receipt_id->id)
                    ->update([
                        'serialNo' => $daily_account->serialno,
                        'payment_status' => 'pending',
                        'paid_amount' => 0,
                        'panelty' => 0,
                        'payment_date' => null,
                        'recpt_id' => null
                    ]);
            }

            // Delete the receipt detail entry
            DB::table('daily_collectionsavings')->where('id', $id)->delete();

            // Mark the Daily account as Active if linked
            if ($daily_account) {
                DB::table('daily_collections')
                    ->where('id', $receipt_id->account_no)
                    ->update(['status' => 'Active']);
            }

            DB::commit();

            $accountNumber = $receipt_id->account_no;
            $membertype = $receipt_id->memberType;
            $account_number = $this->Balances($accountNumber, $membertype);


            return response()->json([
                'status' => 'success',
                'daily_account' => $account_number,
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

    public function dailyinstallmentsmodify(Request $post)
    {
        $id = $post->id;
        $membertype = $post->memberType;
        $receipt_id = DB::table('daily_collectionsavings')
            ->where('id', $id)
            ->first();

        $date = $receipt_id->receipt_date;

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        if ($date < $start_date || $date > $end_date) {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Session Date With Transaction Date'
            ]);
        }

        $daily_account = DB::table('daily_collectionsavings')
            ->select(
                'daily_collectionsavings.*',
                'daily_collections.opening_date',
                'daily_collections.amount',
                'daily_collections.days',
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->where('daily_collectionsavings.id', $id)
            ->where('daily_collections.account_no', $receipt_id->account_no)
            ->where('daily_collections.membertype', $membertype)
            ->first();


        if (!empty($receipt_id)) {
            return response()->json(['status' => 'success', 'daily_account' => $daily_account]);
        } else {
            return response()->json(['status' => 'fail', 'messages' => 'Record Not Found']);
        }
    }

    public function dailysavingreceivedupdate(Request $post)
    {

        $rules = [
            "dailyid" => 'required',
            "receiveamountaccount" => "required|numeric",
            "receiveaccounttype" => "required",
            "received_amount_date" => "required",
            "receive_amount" => "required",
            "payment_type" => "required",
            "payment_bank" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        //_________check Session
        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->received_amount_date)));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $id = $post->dailyid;

        if ($id) {
            $check_dds_no = DailyCollectionsaving::where('id', $id)->first();

            $daily_account = DB::table('daily_collections')
                ->select('daily_collections.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                ->where('account_no', $check_dds_no->account_no)
                ->where('daily_collections.membertype', $check_dds_no->memberType)
                ->first();

            if ($check_dds_no) {
                $dds_account_details = DB::table('daily_collections')
                    ->select('daily_collections.*', 'scheme_masters.id as sch_id')
                    ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
                    ->where('daily_collections.account_no', $check_dds_no->account_no)
                    ->where('daily_collections.membertype', $check_dds_no->memberType)
                    ->orderBy('daily_collections.opening_date', 'ASC')
                    ->first();



                $date = date('Y-m-d', strtotime($post->received_amount_date));

                $session_master = SessionMaster::find(Session::get('sessionId'));

                $start_date = $session_master->startDate;
                $end_date = $session_master->endDate;

                if ($session_master->auditPerformed === 'Yes') {
                    return response()->json([
                        'status' => 'fail',
                        'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
                    ]);
                }


                //______dates
                $transDate = $date;

                //_________Check Account Group Or Ledger
                if ($dds_account_details->scheme_groupCode && $dds_account_details->scheme_ledgerCode) {
                    $account_group = $dds_account_details->scheme_groupCode;
                    $account_ledger = $dds_account_details->scheme_ledgerCode;
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Account Group Code Or Ledger Code Not Found']);
                }


                if ($post->payment_type && $post->payment_bank) {
                    $cashBankGroup = $post->payment_type;
                    $cashBankLedger = $post->payment_bank;
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group Code Not Found']);
                }


                $ledger = GeneralLedger::where('serialNo', $check_dds_no->serialNo)->where('is_delete', 'No')->get();


                if (!$ledger) {
                    return response()->json(['status' => 'fail', 'messages' => "Data Not found"]);
                }

                $last_amount = $check_dds_no->deposit;
                $latest_amount_cr = $post->receive_amount;


                $lastinstallmet = DailySavingInstallment::where(['recpt_id' => $check_dds_no->id])->orderBy('id', 'desc')->first();

                DB::beginTransaction();
                try {



                    $paidamount =  DailySavingInstallment::where(['daily_id' => $check_dds_no->dailyaccountid])->sum('paid_amount');

                    $totalRdAmount = $lastinstallmet->amount * $lastinstallmet->intallment_no;
                    $totaldueRd =  $totalRdAmount - $paidamount;
                    // dd($lastinstallmet->amount,$lastinstallmet->intallment_no,$totalRdAmount,$paidamount,$totaldueRd,$post->receive_amount);
                    // if ($post->receive_amount <= $totaldueRd) {

                        $monthsToPay = $post->receive_amount / $lastinstallmet->amount;

                        if ($monthsToPay <= 0 && $monthsToPay > $lastinstallmet->intallment_no) {
                            return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given Daily payment.']);
                        }

                        DailySavingInstallment::where('serialNo', $check_dds_no->serialNo)->update([
                            'payment_status' => 'pending',
                            'paid_amount' => 0,
                            'payment_date' => null,
                            'panelty' => 0,
                            'recpt_id' => null
                        ]);

                        //_______update rd recived
                        $check_dds_no->receipt_date = $transDate;
                        $check_dds_no->deposit = $latest_amount_cr;
                        // $check_dds_no->matureserialNo =  $check_dds_no->serialNo;
                        $check_dds_no->save();

                        //______update installmets
                        for ($i = 1; $i <= $monthsToPay; $i++) {
                            $pendinginstalment = DailySavingInstallment::where([
                                'daily_id' => $check_dds_no->dailyaccountid,
                                'payment_status' => 'pending'
                            ])->first();

                            if ($pendinginstalment && $pendinginstalment->payment_status == "pending") {
                                $pendinginstalment->payment_date = $transDate;
                                $pendinginstalment->paid_amount = $lastinstallmet->amount;
                                $pendinginstalment->payment_status = "paid";
                                $pendinginstalment->serialNo = $check_dds_no->serialNo;
                                $pendinginstalment->recpt_id = $check_dds_no->id;
                                $pendinginstalment->save();
                            }
                        }

                        //_______________Delete Gerenal ledger
                        GeneralLedger::where('serialNo', $check_dds_no->serialNo)
                            ->where('referenceNo', $check_dds_no->id)
                            ->where('is_delete', 'No')
                            ->delete();



                        //__________________________Gerenal Ledger Rd Entry_____________________

                        //___________RD Amount Entry
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $check_dds_no->serialNo;
                        $genral_ledger->accountId = $dds_account_details->account_no;
                        $genral_ledger->accountNo = $dds_account_details->membershipno;
                        $genral_ledger->memberType = $dds_account_details->membertype;
                        $genral_ledger->groupCode = $account_group;
                        $genral_ledger->ledgerCode = $account_ledger;
                        $genral_ledger->formName = "DDS";
                        $genral_ledger->referenceNo = $check_dds_no->id;
                        $genral_ledger->transactionDate = $transDate;
                        $genral_ledger->transactionType = "Cr";
                        $genral_ledger->transactionAmount = $post->receive_amount;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();

                        //____________Cash/Bank Entry
                        $genral_ledger = new GeneralLedger;
                        $genral_ledger->serialNo = $check_dds_no->serialNo;
                        $genral_ledger->accountId = $dds_account_details->account_no;
                        $genral_ledger->accountNo = $dds_account_details->membershipno;
                        $genral_ledger->memberType = $dds_account_details->membertype;
                        $genral_ledger->formName = "DDS";
                        $genral_ledger->groupCode = $cashBankGroup;
                        $genral_ledger->ledgerCode = $cashBankLedger;
                        $genral_ledger->referenceNo = $check_dds_no->id;
                        $genral_ledger->transactionDate = $transDate;
                        $genral_ledger->transactionType = "Dr";
                        $genral_ledger->transactionAmount = $post->receive_amount;
                        $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $genral_ledger->agentId = $post->agent_id;
                        $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                        $genral_ledger->updatedBy = $post->user()->id;
                        $genral_ledger->save();

                        DB::commit();

                        $accountNumber = $post->receiveamountaccount;
                        $membertype = $post->receiveaccounttype;
                        $account_number = $this->Balances($accountNumber, $membertype);

                        return response()->json([
                            'status' => 'success',
                            'dds_account' => $daily_account,
                            'account_number' => $account_number,
                            'messages' => "Installment Updated Successfully !!"
                        ]);
                    // }
                } catch (\Exception $e) {
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


    public function getdetaildailyaccountmature(Request $post){



        $accountNumber = $post->account_number;
        $membertype = $post->memberType;

        $opening_account = DB::table('opening_accounts')
            ->where('opening_accounts.accountNo', $accountNumber)
            ->where('opening_accounts.membertype', $membertype)
            ->where('opening_accounts.accountname', 'DailyDeposit')
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


            $account_number = DB::table('daily_collections')
                ->select(
                    'daily_collections.customer_name as name',
                    'daily_collections.account_no',
                    'daily_collections.membershipno',
                    'daily_collections.opening_date',
                    'daily_collections.amount',
                    'daily_collections.status',
                    'daily_collections.interest',
                    'daily_collections.days',
                    'daily_collections.scheme_name',
                    'member_accounts.accountNo as membership',
                    'member_accounts.photo',
                    'daily_collectionsavings.dailyaccountid',
                    DB::raw('SUM(CASE WHEN daily_collectionsavings.deposit IS NOT NULL THEN daily_collectionsavings.deposit ELSE 0 END) deposit_amount')
                )
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collections.membershipno')
                ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
                ->where('daily_collections.account_no', $accountNumber)
                ->where('daily_collections.membertype', $membertype)
                ->groupBy(
                    'daily_collections.customer_name',
                    'daily_collections.account_no',
                    'daily_collections.membershipno',
                    'daily_collections.opening_date',
                    'daily_collections.amount',
                    'daily_collections.interest',
                    'daily_collections.days',
                    'member_accounts.accountNo',
                    'member_accounts.photo',
                    'daily_collectionsavings.dailyaccountid',
                    'daily_collections.status',
                    'daily_collections.scheme_name'
                )
                ->first();

            if(empty($opening_account) || !empty($saving_account) || !empty($account_number)){
                return response()->json([
                    'status' => 'success',
                    'details' => $saving_account,
                    'opening_account' => $opening_account,
                    'account_number' => $account_number
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

    public function dailyaccountmature(Request $post){
        $rules = [
            "mreceiveamountaccount" => "required|numeric",
            "mreceiveaccounttype" => "required",
            'membernumber' => 'required',
            "received_amount_dated" => "required",
            "saving_amount" => "required",
            "standing_amount" => "required|numeric",
            "interst_rate" => "required",
            "paid_interst_amount" => "required",
            "net_amount" => "required|numeric",
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->received_amount_dated)));

        $date = date('Y-m-d', strtotime($post->received_amount_dated));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $account_no = $post->mreceiveamountaccount;
        $membertype = $post->mreceiveaccounttype;
        $membership = $post->membernumber;


        //__________Get Account Scheme and Scheme Group Code and Ledger Code
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
            ->where('opening_accounts.membertype', $membertype)
            ->where('opening_accounts.membershipno', $membership)
            ->where('opening_accounts.accountname', 'DailyDeposit')
            ->first();

        //_______Date Format Convert
        $transactionDate = $date;

        //__________Check account opening date not less then
        if ($transactionDate < $account_opening->transactionDate) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors(), 'messages' => 'Transaction date can not be less than account opening date']);
        }

        $rand = "DDS" . time();

        DB::beginTransaction();
        try{
            //_________saving Account Number
            $saving_accountNo = $post->saving_amount;

            //___________Get DDS Account Number
            $dds_account_details = DB::table('daily_collections')
                ->select('daily_collections.*', 'scheme_masters.id as sch_id')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
                ->where('daily_collections.account_no', $account_opening->accountNo)
                ->orderBy('daily_collections.opening_date', 'ASC')
                ->first();
            // dd($dds_account_details);

            if($dds_account_details->scheme_groupCode && $dds_account_details->scheme_ledgerCode){
                $dds_group = $dds_account_details->scheme_groupCode;
                $dds_ledger = $dds_account_details->scheme_ledgerCode;
            }else{
                return response()->json(['status' => 'Fail','messages' => 'DDS Group Code Or Ledger Code Not Found']);
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
                ->where('opening_accounts.membershipno', $membership)
                ->where('opening_accounts.accountNo', $saving_accountNo)
                ->where('opening_accounts.accountname','Saving')
                ->where('opening_accounts.status','Active')
                ->first();

            if($saving_account->groupCode && $saving_account->ledgerCode){
                $saving_group = $saving_account->groupCode;
                $saving_ledger = $saving_account->ledgerCode;
            }else{
                return response()->json(['status' => 'Fail','messages' => 'Saving Group Code Or Ledger Code Not Found']);
            }

            //_________Get Daily Account Interest Paid Code
            $scheme_interest_group_legder_code = DB::table('daily_collections')
                ->select('ledger_masters.*','scheme_masters.id as sch_id','ledger_masters.reference_id')
                ->leftJoin('scheme_masters','scheme_masters.id','=','daily_collections.schemeid')
                ->leftJoin('ledger_masters','ledger_masters.reference_id','=','scheme_masters.id')
                ->where('ledger_masters.groupCode','EXPN001')
                ->where('daily_collections.schemeid',$dds_account_details->schemeid)
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
            $dds_amount = $post->standing_amount ?? 0;
            $interest_amount = $post->paid_interst_amount ?? 0;
            $mature_amount = ($dds_amount + $interest_amount);

            //_______________DDS Receipt
            $dds_entry = new DailyCollectionsaving();
            $dds_entry->serialNo = $dds_account_details->serialno;
            $dds_entry->dailyaccountid = $dds_account_details->id;
            $dds_entry->membershipno  =  $dds_account_details->membershipno;
            $dds_entry->account_no  =  $dds_account_details->account_no;
            $dds_entry->sch_id =  $dds_account_details->sch_id;
            $dds_entry->receipt_date = $date;
            $dds_entry->deposit  = 0;
            $dds_entry->matureserialNo = $rand;
            $dds_entry->withdraw = $dds_amount;
            $dds_entry->type = 'Withdraw';
            $dds_entry->payment_mode = $saving_group;
            $dds_entry->bank_name = $saving_ledger;
            $dds_entry->memberType = $dds_account_details->membertype;
            $dds_entry->cheque_no = 0;
            $dds_entry->groupcode = $dds_group;
            $dds_entry->ledgercode = $dds_ledger;
            $dds_entry->narration = "Amount Transfer From Daily Account ".$saving_account->accountNo ?? '';
            $dds_entry->branchId = session('branchId') ? session('branchId') : 1;
            $dds_entry->sessionId = session('sessionId') ? session('sessionId') : 1;
            $dds_entry->updatedBy = $post->user()->id;
            $dds_entry->agentId = $post->agentId;
            $dds_entry->save();


            $savingacc = new MemberSaving();
            $savingacc->serialNo = $rand;
            $savingacc->secheme_id = $saving_account->schemetype;
            $savingacc->accountId = $saving_account->accountNo;
            $savingacc->accountNo = $saving_account->membershipno;
            $savingacc->memberType = $saving_account->membertype;
            $savingacc->groupCode = $saving_group;
            $savingacc->ledgerCode = $saving_ledger;
            $savingacc->savingNo = $saving_account->accountNo;
            $savingacc->transactionDate = $transactionDate;
            $savingacc->chequeNo = 'trfdFromDDS';
            $savingacc->transactionType = 'Deposit';
            $savingacc->depositAmount = $mature_amount;
            $savingacc->withdrawAmount = 0;
            $savingacc->paymentType = null;
            $savingacc->bank = null;
            $savingacc->narration = "Amount Transfer From Daily Account ".$post->mreceiveamountaccount;
            $savingacc->branchId = session('branchId') ? session('branchId') : 1;
            $savingacc->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $savingacc->agentId = $saving_account->agentId;
            $savingacc->updatedBy = $post->user()->id;
            $savingacc->is_delete = 'No';
            $savingacc->save();

            //___________Gerenal Ledger Entries

            //___________DDS Amount Entry
            $genral_ledger = new GeneralLedger;
            $genral_ledger->serialNo = $dds_entry->matureserialNo;
            $genral_ledger->accountId = $dds_account_details->account_no;
            $genral_ledger->accountNo = $dds_account_details->membershipno;
            $genral_ledger->memberType = $dds_account_details->membertype;
            $genral_ledger->groupCode = $interest_group;
            $genral_ledger->ledgerCode = $interest_ledger;
            $genral_ledger->formName = "DDS-Mature";
            $genral_ledger->referenceNo = $dds_entry->id;
            $genral_ledger->transactionDate = $transactionDate;
            $genral_ledger->transactionType = "Dr";
            $genral_ledger->transactionAmount = $interest_amount;
            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
            $genral_ledger->agentId = $post->agentId;
            $genral_ledger->narration = "Amount Transfer From Daily Account ".$post->mreceiveamountaccount;
            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
            $genral_ledger->updatedBy = $post->user()->id;
            $genral_ledger->save();

            //___________Interest Amount Entry
            $genral_ledger = new GeneralLedger;
            $genral_ledger->serialNo = $dds_entry->matureserialNo;
            $genral_ledger->accountId = $dds_account_details->account_no;
            $genral_ledger->accountNo = $dds_account_details->membershipno;
            $genral_ledger->memberType = $dds_account_details->membertype;
            $genral_ledger->groupCode = $dds_group;
            $genral_ledger->ledgerCode = $dds_ledger;
            $genral_ledger->formName = "DDS-Mature";
            $genral_ledger->referenceNo = $dds_entry->id;
            $genral_ledger->transactionDate = $transactionDate;
            $genral_ledger->transactionType = "Dr";
            $genral_ledger->transactionAmount =  $dds_amount;
            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
            $genral_ledger->agentId = $post->agentId;
            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
            $genral_ledger->updatedBy = $post->user()->id;
            $genral_ledger->narration = "Amount Transfer From Daily Account ".$post->mreceiveamountaccount;
            $genral_ledger->save();


            //____________Saving A/c
            $genral_ledger = new GeneralLedger;
            $genral_ledger->serialNo = $dds_entry->matureserialNo;
            $genral_ledger->accountId = $saving_account->accountNo;
            $genral_ledger->accountNo = $saving_account->membershipno;
            $genral_ledger->memberType = $saving_account->membertype;
            $genral_ledger->formName = "DDS";
            $genral_ledger->groupCode = $saving_group;
            $genral_ledger->ledgerCode = $saving_ledger;
            $genral_ledger->referenceNo = $dds_entry->id;
            $genral_ledger->transactionDate = $transactionDate;
            $genral_ledger->transactionType = "Cr";
            $genral_ledger->transactionAmount = $mature_amount;
            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
            $genral_ledger->agentId = $post->agentId;
            $genral_ledger->narration = "Amount Transfer From Daily Account ".$saving_account->accountNo ?? '';
            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
            $genral_ledger->updatedBy = $post->user()->id;
            $genral_ledger->save();


            //_________Check Maturity Date Status
            $maturity_date = new DateTime($dds_account_details->maturitydate);
            $todaydate = $date;
            $status = '';

            //___________If Today Date Greater Then Maturity
            if($todaydate > $maturity_date) {
                $status = 'Mature';
            } else {
                $status = 'PreMature';
            }


            //__________Account Open Table Update Status
            $account_open = opening_accounts::where('accountNo',$dds_account_details->account_no)
                ->where('opening_accounts.membertype', $dds_account_details->membertype)
                ->where('opening_accounts.membershipno', $dds_account_details->membershipno)
                ->where('accountname','DailyDeposit')
                ->first();

            $account_open->status = 'Closed';
            $account_open->save();



            DB::table('daily_collections')
            ->where('account_no', $dds_account_details->account_no)
            ->update([
                'actualMaturitydate' => $todaydate,
                'ActualyMaturityAmount' => $mature_amount,
                'matureserialNo' => $rand,
                'type' => 'SAVING',
                'status' => $status,
            ]);


            DB::commit();

            $accountNumber = $post->mreceiveamountaccount;
            $account_number = $this->Balances($accountNumber, $membertype);

            return response()->json([
                'status' => 'success',
                'messages' => 'Maturity Amount paid successfully !!',
                'account_number' => $account_number,
                'opening_account' => $account_opening,
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred while inserting the record',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function dailyunmature(Request $post){

        $rules = [
            'unmatureaccountNumber' => 'required',
            'unmaturetype' => 'required',
            'unmaturedate' => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $dds_account = $post->unmatureaccountNumber;
        $memberType = $post->unmaturetype;
        $unmaturedate = $post->unmaturedate;
        $membership = $post->memnumbers;


        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->unmaturedate)));

        $date = date('Y-m-d', strtotime($post->unmaturedate));

        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }


        //__________Get Account Scheme and Scheme Group Code and Ledger Code
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
            ->where('opening_accounts.accountNo', $dds_account)
            ->where('opening_accounts.membertype', $memberType)
            ->where('opening_accounts.membershipno', $membership)
            ->where('opening_accounts.accountname', 'DailyDeposit')
            ->where('opening_accounts.status','=','Closed')
            ->first();

        //_______Date Format Convert
        $transactionDate = $date;

        //__________Check account opening date not less then
        if ($transactionDate < $account_opening->transactionDate) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
                'messages' => 'Transaction date can not be less than account opening date'
            ]);
        }

        DB::beginTransaction();
        try{
            $dailycollaccount = DB::table('daily_collections')
            ->where('membertype', $account_opening->membertype)
            ->where('account_no', $account_opening->accountNo)
            ->whereIn('status', ['PreMature', 'Mature', 'Closed', 'Deleted'])
            ->first();

            if(!empty($dailycollaccount)){
                $daliysaving = DB::table('daily_collectionsavings')
                    ->where('matureserialNo', $dailycollaccount->matureserialNo)
                    ->where('membershipno', $dailycollaccount->membershipno)
                    ->where('account_no', $dailycollaccount->account_no)
                    ->first();

                DB::table('general_ledgers')
                    ->where('serialNo', $dailycollaccount->matureserialNo)
                    ->where('referenceNo', $daliysaving->id)
                    ->delete();

                DB::table('member_savings')
                    ->where('serialNo', $daliysaving->matureserialNo)
                    ->where('chequeNo', 'trfdFromDDS')
                    ->delete();


                DB::table('daily_collections')
                    ->where('account_no', $daliysaving->account_no)
                    ->update([
                        'actualMaturitydate' => null,
                        'ActualyMaturityAmount' => null,
                        'matureserialNo' => null,
                        'type' => null,
                        'status' => 'Active',
                    ]);

                DB::table('opening_accounts')
                    ->where('accountNo', $dds_account)
                    ->where('membertype', $memberType)
                    ->where('membershipno', $membership)
                    ->where('accountname', 'DailyDeposit')
                    ->update([
                        'status' => 'Active',
                    ]);

                DB::table('daily_collectionsavings')
                    ->where('matureserialNo', $dailycollaccount->matureserialNo)
                    ->where('membershipno', $dailycollaccount->membershipno)
                    ->where('account_no', $dailycollaccount->account_no)
                    ->delete();

                DB::commit();

                $accountNumber = $post->unmatureaccountNumber;
                $membertype = $post->unmaturetype;
                $account_number = $this->Balances($accountNumber, $membertype);

                return response()->json([
                    'status' => 'success',
                    'account_number' => $account_number
                ]);
            }

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred while inserting the record',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function getddsaccountsssss(Request $post){

       $rules = [
            "account_number" => "required",
            "memberType" => "required"
       ];

       $validator = Validator::make($post->all(),$rules);
       if($validator->fails()){
            return response()->json(['status' => 'Fail','error' => $validator->errors(),'messages' => 'Record Not Found']);
       }


       $daily_collections = DB::table('daily_collections')
            ->where('account_no', $post->account_number)
            ->where('membertype', $post->memberType)
            ->whereIn('status', ['Active', 'Pluge'])
            ->first();

       if(!empty($daily_collections)){
            return response()->json(['status' => 'success','daily_collections' => $daily_collections]);
       }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
       }
    }


}
