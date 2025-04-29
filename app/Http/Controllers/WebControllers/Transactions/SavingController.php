<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\AgentMaster;
use App\Models\divident;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\LoanInstallment;
use App\Models\LoanMaster;
use App\Models\LoanRecovery;
use App\Models\MemberAccount;
use App\Models\MemberFdScheme;
use App\Models\MemberLoan;
use App\Models\MemberOpeningBalance;
use App\Models\MemberSaving;
use App\Models\MemberShare;
use App\Models\RdInstallment;
use App\Models\ReCurringRd;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\DailySavingInstallment;
use App\Models\DailyCollection;
use App\Models\opening_accounts;
use Illuminate\Support\Str;
class SavingController extends Controller
{
    public function SavingAccountIndex()
    {
        $groups = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        $agents = AgentMaster::orderBy('name', 'ASC')->get();
        $data['groups'] = $groups;
        $data['agents'] = $agents;

        return view('transaction.saving', $data);
    }

    public function dlttrfsavingtoloan(Request $post)
    {
        DB::beginTransaction();
        try {

            $memsav = DB::table('member_savings')->where('id', '=', $post->id)->first();
            $loanRecept = DB::table('loan_recoveries')->where('receivedBy', '=', $memsav->serialNo)->first();
            $installmetsIds = explode(',', $loanRecept->instaId);
            $installmets = LoanInstallment::whereIn('id', $installmetsIds)->get();
            if (count($installmets) > 0) {
                foreach ($installmets as $installmet) {
                    LoanInstallment::where('id', $installmet->id)->update([
                        'status' => 'False',
                    ]);
                }
            }

            DB::table('general_ledgers')->where('serialNo', '=', $memsav->serialNo)->delete();
            DB::table('loan_recoveries')->where('receivedBy', '=', $memsav->serialNo)->delete();
            DB::table('member_savings')->where('id', '=', $post->id)->delete();

            DB::commit();
            //   $loanrecovery =  LoanRecovery::where('loanId',$post->id)->where('is_delete', 'No')->get();

            return response()->json([
                'status' => 'success',
                'acc' => $memsav->accountNo,
                'message' => 'Loan updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => 'Some Technical issue occurred'], 200);
        }
    }

    public function trfsavingtoloan(Request $post)
    {

        $member_ship = $post->membershipnumbers;

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
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.membershipno', $member_ship)
        // ->where
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

        DB::beginTransaction();
        try {

            $loanaccount = MemberLoan::where('id', $post->trfloanid)->first();

            if (! $loanaccount) {
                return response()->json(['status' => 'Some Technical issue occurred'], 200);
            }

            $memberType = $post->trfmemberType;
            $principle = $post->trfprinciple;
            $intrest = $post->trfintrest;
            $panelty = $post->trfpanelty;
            $totalpayment = $post->trftotalpayment;
            $loandate = $post->trfinstalldate;

            $result = $this->isDateBetween(date('Y-m-d', strtotime($loandate)));
            if (! $result) {
                return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session', 'message' => 'Access denied for this session'], 400);
            }

            $intGroupCode = 'INCM001';
            if ($memberType == 'Member') {
                $pricpleCode = 'LONM001';
                $curentintCode = 'LONM002';
                $penalCode = 'LONM003';
                $pendingCode = 'LONM004';
            } elseif ($memberType == 'NonMember') {
                $pricpleCode = 'LONN001';
                $curentintCode = 'LONN002';
                $penalCode = 'LONN003';
                $pendingCode = 'LONN004';
            } elseif ($memberType == 'Staff') {
                $pricpleCode = 'LONS001';
                $curentintCode = 'LONS002';
                $penalCode = 'LONS003';
                $pendingCode = 'LONS004';
            }
            $paidAmount = $totalpayment;
            $InstallmentIds = [];

            $installmentsTillDate = LoanInstallment::where('LoanId', $post->trfloanid)
            // ->whereDate('installmentDate', "<=", date('Y-m-d', strtotime($loandate)))
                ->whereIn('status', ['False', 'Partial'])
                ->get();

            foreach ($installmentsTillDate as $key => $installment) {

                if ($paidAmount >= $principle + $intrest) {
                    LoanInstallment::where('id', $installment->id)->update([
                        'status' => 'True',
                        'paid_date' => date('Y-m-d', strtotime($loandate)),
                        're_amount' => $installment->principal + $installment->interest,
                    ]);

                    $paidAmount = $paidAmount - $principle + $intrest;
                    $InstallmentIds[$key] = $installment->id;

                } else {

                    if (($paidAmount - $installment->interest) > 0) {
                        LoanInstallment::where('id', $installment->id)->update([
                            'status' => 'Partial',
                            'paid_date' => date('Y-m-d', strtotime($loandate)),
                            're_amount' => ($principle + $intrest) - $paidAmount,
                        ]);
                        $InstallmentIds[$key] = $installment->id;
                    }
                    break;
                }
            }

            $generalLedgers = 'loan'.time();

            $saving_deposit = new MemberSaving;
            $saving_deposit->secheme_id = $account_opening->sch_id;
            $saving_deposit->serialNo = $generalLedgers;
            $saving_deposit->accountId = $account_opening->accountNo;
            $saving_deposit->accountNo = $account_opening->membershipno;
            $saving_deposit->memberType = $account_opening->membertype;
            $saving_deposit->groupCode = $saving_group;
            $saving_deposit->ledgerCode = $saving_ledger;
            $saving_deposit->savingNo = '';
            $saving_deposit->transactionDate = date('Y-m-d', strtotime($loandate));
            $saving_deposit->transactionType = 'toloan';
            $saving_deposit->depositAmount = 0;
            $saving_deposit->withdrawAmount = $totalpayment;
            $saving_deposit->paymentType = '';
            $saving_deposit->bank = '';
            $saving_deposit->chequeNo = 'SavingTrfdToLoan';
            $saving_deposit->narration = 'Saving A/c'.$account_opening->accountNo.'Trfd To Loan'.$loanaccount->accountNo;
            $saving_deposit->branchId = session('branchId') ? session('branchId') : 1;
            $saving_deposit->sessionId = session('sessionId') ? session('sessionId') : 1;
            $saving_deposit->agentId = $post->agentId;
            $saving_deposit->updatedBy = $post->user()->id;
            $saving_deposit->is_delete = 'No';
            $saving_deposit->save();

            $saving_id = $saving_deposit->id;

            $InstallmentId = implode(',', $InstallmentIds);
            $loan = LoanRecovery::create([
                'loanId' => $post->trfloanid,
                'receiptDate' => date('Y-m-d', strtotime($loandate)),
                'principal' => $principle,
                'interest' => $intrest,
                'pendingInterest' => 0,
                'penalInterest' => $panelty,
                'total' => $totalpayment,
                'receivedAmount' => $totalpayment,
                'overDueInterest' => 0,
                'status' => 'True',
                'receivedBy' => $generalLedgers,
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'instaId' => $InstallmentId,
                'updatedBy' => $post->user()->id,
            ]);

            DB::table('general_ledgers')->insert([
                'serialNo' => $generalLedgers,
                'accountId' => $loanaccount->accountId,
                'accountNo' => $loanaccount->accountNo,
                'memberType' => $memberType,
                'agentId' => $post->user()->id,
                'ledgerCode' => $saving_ledger,
                'groupCode' => $saving_group,
                'referenceNo' => $loan->id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($loandate)),
                'transactionType' => 'Dr',
                'formName' => 'LoanReceipt',
                'transactionAmount' => $totalpayment,
                'narration' => 'Transferd From Saving',
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
            ]);

            $insert = [
                'serialNo' => $generalLedgers,
                'accountId' => $loanaccount->accountId,
                'accountNo' => $loanaccount->accountNo,
                'memberType' => 'Member',
                'agentId' => $post->agentId,
                'referenceNo' => $loan->id,
                'entryMode' => 'manual',
                'transactionDate' => date('Y-m-d', strtotime($loandate)),
                'transactionType' => 'Cr',
                'formName' => 'LoanReceipt',
                'narration' => $post->naration,
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updatedBy' => $post->user()->id,
            ];
            /*   insert penal  interest transaction */

            if ($panelty > 0) {
                $insert['ledgerCode'] = $penalCode;
                $insert['groupCode'] = $intGroupCode;
                $insert['transactionAmount'] = $panelty;
                DB::table('general_ledgers')->insert($insert);
            }
            if ($intrest > 0) {
                $insert['ledgerCode'] = $curentintCode;
                $insert['groupCode'] = $intGroupCode;
                $insert['transactionAmount'] = $intrest;
                DB::table('general_ledgers')->insert($insert);
            }
            if ($principle > 0) {
                $insert['ledgerCode'] = $pricpleCode;
                $insert['groupCode'] = $pricpleCode;
                $insert['transactionAmount'] = $principle;
                DB::table('general_ledgers')->insert($insert);
            }

            DB::commit();
            //   $loanrecovery =  LoanRecovery::where('loanId',$post->id)->where('is_delete', 'No')->get();

            return response()->json([
                'status' => 'success',
                'acc' => $account_opening->accountNo,
                'message' => 'Loan updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());

            return response()->json(['status' => 'failed', 'message' => 'Some Technical issue occurred'], 200);
        }

    }

    public function getloanpending(Request $request)
    {
        $acloan = MemberLoan::where('accountNo', $request->mmshpno)->where('memberType', $request->memberType)->orderBy('id', 'desc')->first();
        if ($acloan) {
            $loanFetch = MemberLoan::where('id', $acloan->id)->first();
            if (! $loanFetch) {
                return response()->json(['status' => 'Invlid account number']);
            }
            $loanFetch1 = LoanMaster::where('id', $loanFetch->loanType)->where('is_delete', 'No')->first();
            $installmentlist = DB::table('loan_installments')->where('LoanId', $acloan->id)->get();
            $loanDate = $loanFetch->loanDate;
            $todayDate = date('Y-m-d', strtotime($request->transactionDate));
            if ($todayDate < $loanDate && $todayDate != $loanDate) {
                return response()->json(['status' => 'Date is Not Correct, It Should Be '.date('d-m-Y', strtotime($loanDate)).' or Above.']);
            }
            $installmentsTillDate = LoanInstallment::where('LoanId', $acloan->id)->whereIn('status', ['False', 'Partial'])->first();
            if ($installmentsTillDate) {
                return response()->json(['status' => true,  'loanid' => $acloan->id, 'installmet' => $installmentsTillDate]);
            } else {
                return response()->json(['status' => 'Data not Found.']);
            }
        } else {
            return response()->json(['status' => 'Data not Found.']);
        }

    }

    //__________Get Ledger's Behalf Of Groups
    public function GetLedgders(Request $post)
    {
        $groups_code = $post->groups_code;
        if ($groups_code) {
            $ledgers = LedgerMaster::where('groupCode', $groups_code)->where('ledgerCode', '!=', 'BANKFD01')->where('status', 'Active')->orderBy('name', 'ASC')->get();

            if (! empty($ledgers)) {
                return response()->json(['status' => 'success', 'ledgers' => $ledgers]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Ledger Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Group Not Found']);
        }
    }

    //__________Get Saving Account List
    public function GetSavingAccountList(Request $post)
    {

        $account_no = $post->account_no;
        $memberType = $post->memberType;
        if (! empty($account_no)) {
            $account_nos = DB::table('opening_accounts')
                ->where('accountNo', 'LIKE', $account_no.'%')
                ->where('membertype', '=', $memberType)
                ->where('accountname', '=', 'Saving')
                ->where('status', '=', 'Active')
                ->get();
            if ($account_nos) {
                return response()->json(['status' => 'success', 'accounts' => $account_nos]);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Account Number Not Exits']);
        }
    }

    //_________Get Customer Details
    public function GetSavingDetails(Request $post)
    {


        $transactionType = $post->transactionType;
        //_________Checked Account in Opening Account Table
        $account_no = $post->selectdId;
        $memberType = $post->memberType;
        $saving_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name','member_accounts.memberType as type'
                )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->where('opening_accounts.accountNo','=',$account_no)
            ->where('opening_accounts.membertype','=',$memberType)
            ->where('member_accounts.memberType','=',$memberType)
            ->where('opening_accounts.accountname', '=', 'Saving')
            ->where('opening_accounts.status', '=', 'Active')
            ->first();


        //_________Get Old Balances From Member Opening Balance Table
        $opning_balance = DB::table('member_opening_balance')
            ->where('membership_no', '=', $saving_account->membershipno)
            ->where('memberType', '=', $saving_account->membertype)
            ->where('account_no', $saving_account->accountNo)
            ->where('accType', 'Saving')
            ->first();


        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if (! empty($session_master)) {

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('member_savings')
                ->where('accountId', $saving_account->accountNo)
                ->where('memberType', '=', $saving_account->membertype)
                ->whereDate('transactionDate', '<', $session_master->startDate)
                ->get();


            //_________Get Current Year Entries
            $saving_entries = DB::table('member_savings')
                ->select('member_savings.*', 'users.id as userid', 'users.name as username')
                ->leftJoin('users', 'users.id', 'member_savings.updatedBy')
                ->where('member_savings.accountId', $saving_account->accountNo)
                ->where('member_savings.memberType', $saving_account->membertype)
                ->where('member_savings.accountNo', $saving_account->membershipno)
                ->whereDate('member_savings.transactionDate', '>=', $session_master->startDate)
                ->whereDate('member_savings.transactionDate', '<=', $session_master->endDate)
                ->orderBy('transactionDate', 'ASC')
                ->get();


            //______Get Opening Amount
            if ($opning_balance) {
                $previous_balance = collect($previous_balance);
                $opening_amount = $opning_balance->opening_amount + $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');

            } else {
                $opening_amount = 0;
            }

            //_______________Get Fd Account
            $fdAccounts = DB::table('opening_accounts')
                ->select(
                    'opening_accounts.*',
                    'member_accounts.accountNo as membership',
                    'member_accounts.name as customer_name',
                    'scheme_masters.id as sch_id',
                    'scheme_masters.name',
                    'scheme_masters.scheme_code',
                    'scheme_masters.lockin_days',
                    'scheme_masters.days',
                    'scheme_masters.months',
                    'scheme_masters.years',
                    'scheme_masters.interest'
                )
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
                ->where('opening_accounts.membershipno', $saving_account->membership)
                ->where('opening_accounts.membertype', $saving_account->membertype)
                ->where('opening_accounts.accountname', '=', 'FD')
                ->where('opening_accounts.status', '=', 'Active')
                ->get();

            $filteredAccounts = [];

            foreach ($fdAccounts as $fd) {
                $existingAccount = DB::table('member_fds_scheme')
                    ->where('membershipno', $fd->membershipno)
                    ->where('accountNo', $fd->accountNo)
                    ->exists();

                if (! $existingAccount) {
                    $filteredAccounts[] = $fd;
                }
            }

            //_______________Get Rd Account
            $rd_account = DB::table('re_curring_rds')
                ->select(
                    're_curring_rds.*',
                    'member_accounts.accountNo as membership',
                    'member_accounts.name as customer_name',
                    'scheme_masters.id as sch_id',
                    'scheme_masters.name',
                    'scheme_masters.scheme_code',
                    'scheme_masters.lockin_days',
                    'scheme_masters.days',
                    'scheme_masters.months',
                    'scheme_masters.years',
                    'scheme_masters.interest'
                )
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                ->where('re_curring_rds.accountNo', $saving_account->membership)
                ->where('re_curring_rds.status', '=', 'Active')
                ->get();

            if ($previous_balance || $saving_entries || $opening_amount || $filteredAccounts || $rd_account) {
                return response()->json([
                    'status' => 'success',
                    'saving_account' => $saving_account,
                    'opening_amount' => $opening_amount,
                    'saving_entries' => $saving_entries,
                    'rd_account' => $rd_account,
                    'fd' => $filteredAccounts,

                ]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Check Your Session']);
        }
    }

    public function checkpaymentbalance($account_no, $date, $amounts)
    {
        $saving_account = DB::table('opening_accounts')
            ->select('opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->where('opening_accounts.accountNo', $account_no)
            ->where('opening_accounts.accountname', '=', 'Saving')
            ->where('opening_accounts.status', '=', 'Active')
            ->first();


        //_________Get Old Balances From Member Opening Balance Table
        $opning_balance = DB::table('member_opening_balance')
            ->where('membership_no', '=', $saving_account->membershipno)
            ->where('account_no', $saving_account->accountNo)
            ->where('accType', 'Saving')
            ->first();


        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if (! empty($session_master)) {

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('member_savings')
                ->where('accountId', $saving_account->accountNo)
                ->whereDate('transactionDate', '<', $session_master->startDate)
                ->get();


            //_________Get Current Year Entries
            $saving_entries = DB::table('member_savings')
                ->select('member_savings.*', 'users.id as userid', 'users.name as username')
                ->leftJoin('users', 'users.id', 'member_savings.updatedBy')
                ->where('member_savings.accountId', $saving_account->accountNo)
                ->where('member_savings.accountNo', $saving_account->membershipno)
                // ->whereDate('member_savings.transactionDate', '>=', $session_master->startDate)
                ->whereDate('member_savings.transactionDate', '<=', $session_master->endDate)
                ->orderBy('transactionDate', 'ASC')
                ->get();


            //______Get Opening Amount
            if ($opning_balance) {
                $previous_balance = collect($previous_balance);
                $opening_amount = $opning_balance->opening_amount + $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');

                $balance = 0;
                foreach ($saving_entries as $saving_entrieslist) {
                    if ($saving_entrieslist->transactionDate <= $date) {
                        $balance += $opening_amount + $saving_entries->sum('depositAmount') - $saving_entries->sum('withdrawAmount');
                    }
                }

                if ($balance < $amounts) {
                    return response()->json(['status' => 'fail', 'messages' => 'You Have Insufficent Balance In Account In that Date']);
                }

            } else {
                $opening_amount = 0;
            }
        }
    }

    //_______Entry In Saving Account
    public function SavingEntryInsert(Request $post)
    {
        $transactionType = $post->transactionType;

        if ($transactionType === 'Deposit' || $transactionType === 'Withdraw') {
            $rules = [
                'transactionDate' => 'required',
                'transactionType' => 'required',
                'memberType' => 'required',
                'accountNo' => 'required',
                'transactionAmount' => 'required',
                'paymentType' => 'required',
                'bank' => 'required',
            ];
        } elseif ($transactionType === 'tord') {
            $rules = [
                'transactionDate' => 'required',
                'transactionType' => 'required',
                'memberType' => 'required',
                'accountNo' => 'required',
                'transactionAmount' => 'required',
                'rd_scheme_id' => 'required',
                'rdmonths' => 'required',
            ];
        } elseif ($transactionType === 'toFd') {
            $rules = [
                'transactionDate' => 'required',
                'transactionType' => 'required',
                'memberType' => 'required',
                'accountNo' => 'required',
                'transactionAmount' => 'required',
                'principal_amount' => 'required',
                'fd_date' => 'required',
                'rate_of_interest' => 'required',
                'maturity_amount' => 'required',
                'interest_amount' => 'required',
                'maturity_date' => 'required',
                'fdaccounts' => 'required'
            ];
        } elseif ($transactionType === 'toshare') {
            $rules = [
                'transactionDate' => 'required',
                'memberType' => 'required',
                'accountNo' => 'required',
                'membership' => 'required',
                'transactionType' => 'required',
                'transactionAmount' => 'required',
            ];
        }

        $validator = Validator::make($post->all(), $rules);

        if (! empty($post->groupCode) && ! empty($post->bank)) {
            $cash_bank_group = $post->groupCode;
            $cash_ledger_group = $post->bank;
        }

        $date = date('Y-m-d', strtotime($post->transactionDate));

        $amounts = $post->transactionAmount;

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

        $account_no = $post->accountNo;
        $member_ship = $post->membership;

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
            ->where('opening_accounts.memberType',$post->memberType)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.membershipno', $member_ship)
            // ->where
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

        // //_______Date Format Convert
        $transactionDate = $date;

        //________Get Account Open Date
        $member = DB::table('opening_accounts')->where(['memberType' => $post->memberType, 'accountNo' => $post->accountNo, 'membershipno' => $member_ship])->first();

        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors(), 'messages' => 'Invalid account number']);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors(), 'messages' => 'Transaction date can not be less than account opening date']);
        }

        //__________serial Number
        do {
            $serialNo = 'saving'.time();
        } while (GeneralLedger::where('serialNo', '=', $serialNo)->first() instanceof GeneralLedger);

        // dd($post->all());

        switch ($transactionType) {
            case 'Deposit':
                DB::beginTransaction();
                try {
                    //___________Entry in Member Saving Table
                    $saving_deposit = new MemberSaving;
                    $saving_deposit->secheme_id = $account_opening->sch_id;
                    $saving_deposit->serialNo = $serialNo;
                    $saving_deposit->accountId = $account_no;
                    $saving_deposit->accountNo = $post->membership;
                    $saving_deposit->memberType = $post->memberType;
                    $saving_deposit->groupCode = $saving_group;
                    $saving_deposit->ledgerCode = $saving_ledger;
                    $saving_deposit->savingNo = '';
                    $saving_deposit->transactionDate = $transactionDate;
                    $saving_deposit->transactionType = 'Deposit';
                    $saving_deposit->depositAmount = $post->transactionAmount;
                    $saving_deposit->withdrawAmount = 0;
                    $saving_deposit->paymentType = $cash_bank_group;
                    $saving_deposit->bank = $cash_ledger_group;
                    $saving_deposit->chequeNo = '';
                    $saving_deposit->narration = $post->narration ?? 'Amount Deposit';
                    $saving_deposit->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_deposit->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_deposit->agentId = $post->agentId;
                    $saving_deposit->updatedBy = $post->user()->id;
                    $saving_deposit->is_delete = 'No';
                    $saving_deposit->save();
                    $saving_id = $saving_deposit->id;

                    //________________________________________General Ledger Entry_________________________
                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->formName = 'Saving';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Cr';
                    $gerenal_ledger->transactionAmount = $post->transactionAmount;
                    $gerenal_ledger->narration = $post->narration;
                    $gerenal_ledger->groupCode = $account_opening->groupCode;
                    $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = 1;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->save();

                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->formName = 'Saving';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Dr';
                    $gerenal_ledger->transactionAmount = $post->transactionAmount;
                    $gerenal_ledger->narration = $post->narration;
                    $gerenal_ledger->groupCode = $cash_bank_group;
                    $gerenal_ledger->ledgerCode = $cash_ledger_group;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = 1;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->save();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $post->memberType;

                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'Some Technical Issue',
                        'error' => $e->getMessage(),
                    ]);
                }
                break;
            case 'Withdraw':
                DB::beginTransaction();
                try {

                    $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                    if ($checkpaymentbalance) {
                        return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                    }

                    //___________Entry in Member Saving Table
                    $saving_withdraw = new MemberSaving;
                    $saving_withdraw->secheme_id = $account_opening->sch_id;
                    $saving_withdraw->serialNo = $serialNo;
                    $saving_withdraw->accountId = $account_no;
                    $saving_withdraw->accountNo = $post->membership;
                    $saving_withdraw->memberType = $post->memberType;
                    $saving_withdraw->groupCode = $account_opening->groupCode;
                    $saving_withdraw->ledgerCode = $account_opening->ledgerCode;
                    $saving_withdraw->savingNo = '';
                    $saving_withdraw->transactionDate = $transactionDate;
                    $saving_withdraw->transactionType = 'Withdraw';
                    $saving_withdraw->depositAmount = 0;
                    $saving_withdraw->withdrawAmount = $amounts;
                    $saving_withdraw->paymentType = $cash_bank_group;
                    $saving_withdraw->bank = $cash_ledger_group;
                    $saving_withdraw->chequeNo = '';
                    $saving_withdraw->narration = $post->narration ?? 'Amount Withdraw';
                    $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_withdraw->agentId = $post->agentId;
                    $saving_withdraw->updatedBy = $post->user()->id;
                    $saving_withdraw->is_delete = 'No';
                    $saving_withdraw->save();
                    $saving_id = $saving_withdraw->id;

                    //________________________________________General Ledger Entry_________________________
                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->formName = 'Saving';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Dr';
                    $gerenal_ledger->transactionAmount = $amounts;
                    $gerenal_ledger->narration = $post->narration;
                    $gerenal_ledger->groupCode = $account_opening->groupCode;
                    $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = 1;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->save();

                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->formName = 'Saving';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Cr';
                    $gerenal_ledger->transactionAmount = $amounts;
                    $gerenal_ledger->narration = $post->narration;
                    $gerenal_ledger->groupCode = $cash_bank_group;
                    $gerenal_ledger->ledgerCode = $cash_ledger_group;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = 1;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->save();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $post->memberType;

                    return $this->showDataTable($account_no,$type);
                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getMessage()]);
                }
                break;

            case 'toshare':
                DB::beginTransaction();
                try {

                    $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                    if ($checkpaymentbalance) {
                        return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                    }

                    //___________Entry in Member Saving Table
                    $saving_withdraw = new MemberSaving;
                    $saving_withdraw->secheme_id = $account_opening->sch_id;
                    $saving_withdraw->serialNo = $serialNo;
                    $saving_withdraw->accountId = $account_no;
                    $saving_withdraw->accountNo = $post->membership;
                    $saving_withdraw->memberType = $post->memberType;
                    $saving_withdraw->groupCode = $account_opening->groupCode;
                    $saving_withdraw->ledgerCode = $account_opening->ledgerCode;
                    $saving_withdraw->savingNo = '';
                    $saving_withdraw->transactionDate = $transactionDate;
                    $saving_withdraw->transactionType = 'toshare';
                    $saving_withdraw->depositAmount = 0;
                    $saving_withdraw->withdrawAmount = $amounts;
                    $saving_withdraw->paymentType = '';
                    $saving_withdraw->bank = '';
                    $saving_withdraw->chequeNo = 'trfdShare';
                    $saving_withdraw->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                    $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_withdraw->agentId = $post->agentId;
                    $saving_withdraw->updatedBy = $post->user()->id;
                    $saving_withdraw->is_delete = 'No';
                    $saving_withdraw->save();

                    //________Get Saving Account Id
                    $saving_id = $saving_withdraw->id;

                    //___________Entry in Member Share Table
                    $saving_trfd_share = new MemberShare;
                    $saving_trfd_share->serialNo = $serialNo;
                    $saving_trfd_share->accountId = $account_no;
                    $saving_trfd_share->accountNo = $post->membership;
                    $saving_trfd_share->memberType = $post->memberType;
                    $saving_trfd_share->groupCode = 'SHAM001';
                    $saving_trfd_share->ledgerCode = 'SHAM001';
                    $saving_trfd_share->shareNo = '';
                    $saving_trfd_share->transactionDate = $transactionDate;
                    $saving_trfd_share->transactionType = 'Deposit';
                    $saving_trfd_share->depositAmount = $amounts;
                    $saving_trfd_share->withdrawAmount = 0;
                    $saving_trfd_share->dividendAmount = 0;
                    $saving_trfd_share->chequeNo = 'trfdShare';
                    $saving_trfd_share->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                    $saving_trfd_share->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_trfd_share->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_trfd_share->agentId = $post->agentId;
                    $saving_trfd_share->updatedBy = $post->user()->id;
                    $saving_trfd_share->txnType = 'transfer';
                    $saving_trfd_share->is_delete = 'No';
                    $saving_trfd_share->save();

                    //________________________________________General Ledger Entry_________________________

                    //________Saving Entry
                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->groupCode = $account_opening->groupCode;
                    $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                    $gerenal_ledger->formName = 'trfdShare';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Dr';
                    $gerenal_ledger->transactionAmount = $amounts;
                    $gerenal_ledger->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $saving_trfd_share->is_delete = 'No';
                    $gerenal_ledger->save();

                    //________Share Entry
                    $gerenal_ledger = new GeneralLedger;
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $account_no;
                    $gerenal_ledger->accountNo = $post->membership;
                    $gerenal_ledger->memberType = $post->memberType;
                    $gerenal_ledger->groupCode = 'SHAM001';
                    $gerenal_ledger->ledgerCode = 'SHAM001';
                    $gerenal_ledger->formName = 'trfdShare';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $gerenal_ledger->transactionType = 'Cr';
                    $gerenal_ledger->transactionAmount = $amounts;
                    $gerenal_ledger->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId = $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $saving_trfd_share->is_delete = 'No';
                    $gerenal_ledger->save();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $post->memberType;

                    return $this->showDataTable($account_no,$type);
                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getMessage()]);
                }
                break;

            case 'dividend':
                DB::beginTransaction();
                try {
                    $serialNo = 'saving'.time();

                    $member = MemberAccount::where(['memberType' => $post->memberType, 'accountNo' => $post->membership])->first();

                    $sesession_year = SessionMaster::where('startDate', '<=', $transactionDate)
                        ->where('endDate', '>=', $transactionDate)
                        ->first();
                    if ($sesession_year) {
                        $openingBal = DB::table('opening_account_details')->where('AccountNumber', $post->membership)->where('TransferReason', '!=', 'Deleted')->first();
                        $shareBal = $openingBal->Sharee ?? 0;
                        $credit = MemberShare::where('accountNo', $post->membership)->where('is_delete', 'No')->where('transactionType', 'Deposit')->whereDate('transactionDate', '<=', $sesession_year->endDate)->sum('depositAmount');
                        $debit = MemberShare::where('accountNo', $post->membership)->where('is_delete', 'No')->where('transactionType', 'Withdraw')->whereDate('transactionDate', '<=', $sesession_year->endDate)->sum('withdrawAmount');
                        $totalamount = $shareBal + $credit - $debit;
                    } else {
                        $totalamount = 0;
                    }

                    $upkro = new divident;
                    $upkro->serialNo = $serialNo;
                    $upkro->accountno = $post->membership;
                    $upkro->amount = $totalamount;
                    $upkro->session_year = $sesession_year->id;
                    $upkro->paid_date = $transactionDate;
                    $upkro->div_amount = $post->transactionAmount;
                    $upkro->save();

                    $saving = new MemberSaving;
                    $saving->serialNo = $serialNo;
                    $saving->accountId = $member->id;
                    $saving->accountNo = $post->membership;
                    $saving->accountId = $post->accountNoo;
                    $saving->transactionDate = $transactionDate;
                    $saving->transactionType = 'Deposit';
                    $saving->memberType = $post->memberType;
                    $saving->depositAmount = $post->transactionAmount;
                    $saving->withdrawAmount = 0;
                    $saving->paymentType = 'Transfer';
                    $saving->narration = $post->narration;
                    $saving->groupCode = 'SAVM001';
                    $saving->ledgerCode = 'SAVM001';
                    $saving->branchId = session('branchId') ? session('branchId') : 1;
                    $saving->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving->agentId = 1;
                    $saving->updatedBy = $post->user()->id;
                    $saving->save();

                    $savingId = $saving->id;

                    $ledger = new GeneralLedger;
                    $ledger->serialNo = $serialNo;
                    $ledger->accountId = $member->id;
                    $ledger->accountNo = $post->membership;
                    $ledger->memberType = $post->memberType;
                    $ledger->formName = 'Saving';
                    $ledger->referenceNo = $savingId;
                    $ledger->entryMode = 'Manual';
                    $ledger->transactionDate = $transactionDate;
                    $ledger->transactionType = 'Dr';
                    $ledger->transactionAmount = $post->transactionAmount;
                    $ledger->narration = $post->narration;
                    $ledger->groupCode = 'UDP0001';
                    $ledger->ledgerCode = 'UDPL00001';
                    $ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $ledger->agentId = 1;
                    $ledger->updatedBy = $post->user()->id;
                    $ledger->save();

                    $ledger = new GeneralLedger;
                    $ledger->serialNo = $serialNo;
                    $ledger->accountId = $member->id;
                    $ledger->accountNo = $post->membership;
                    $ledger->memberType = $post->memberType;
                    $ledger->formName = 'Saving';
                    $ledger->referenceNo = $savingId;
                    $ledger->entryMode = 'Manual';
                    $ledger->transactionDate = $transactionDate;
                    $ledger->transactionType = 'Cr';
                    $ledger->transactionAmount = $post->transactionAmount;
                    $ledger->narration = $post->narration;
                    $ledger->groupCode = 'SAVM001';
                    $ledger->ledgerCode = 'SAVM001';
                    $ledger->branchId = session('branchId') ? session('branchId') : 1;
                    $ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $ledger->agentId = 1;
                    $ledger->updatedBy = $post->user()->id;
                    $ledger->save();

                    DB::commit();

                    return response()->json([
                        'status' => true,
                        'messages' => 'Record Inserted successfully',
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'status' => false,
                        'messages' => 'Transaction Failed',
                        'errors' => $e->getMessage(),
                    ]);
                }
                break;

            case 'tord':

                $account = $post->rd_account_no;
                $rd_account = DB::table('re_curring_rds')
                    ->select('re_curring_rds.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                    ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                    ->where('accountId', $account)
                    ->first();

                if ($rd_account) {
                    //______check Scheme Id
                    if ($rd_account->secheme_id) {
                        $saving_scheme_id = $rd_account->secheme_id;
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Saving Scheme Id Not Found'], 400);
                    }

                    //___________Check Account Group Or Ledger Code
                    if ($rd_account->groupCode && $rd_account->ledgerCode) {
                        $account_group_code = $rd_account->groupCode;
                        $account_ledger_code = $rd_account->ledgerCode;
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Group Code And Ledger Code Not Found'], 400);
                    }
                } else {
                    return response()->json(['status' => 'fail', 'messages' => 'Rd Account Not Found'], 400);
                }

                $installmentdate = date('Y-m-d', strtotime($post->transactionDate));
                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate)));
                if (! $result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => 'Please Check your session']);
                }

                $installments = RdInstallment::where(['rd_id' => $rd_account->id])->orderBy('id', 'desc')->first();
                $paid_amount = RdInstallment::where(['rd_id' => $rd_account->id])->sum('paid_amount');

                $deposit_amount = $amounts;
                $monthly_installment_amount = $installments->amount;
                $no_of_installments = $installments->intallment_no;
                $total_amount = $monthly_installment_amount * $no_of_installments;
                $balance_amount = $total_amount - $paid_amount;

                if (($deposit_amount % $monthly_installment_amount) != 0) {
                    return response()->json(['status' => 'fail', 'messages' => 'Amount should be multiple of '.$monthly_installment_amount]);
                }

                $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                if ($checkpaymentbalance) {
                    return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                }

                if ($deposit_amount <= $balance_amount || $balance_amount == 0) {
                    $monthsToPay = $deposit_amount / $monthly_installment_amount;

                    if ($monthsToPay <= 0) {
                        return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given monthly payment.']);
                    } elseif ($monthsToPay > $no_of_installments) {
                        return response()->json(['status' => 'fail', 'messages' => 'Amount is not perfect for '.$no_of_installments.' Month']);
                    } else {
                        $paymentSuccess = false;
                        $penaltyApplied = false;
                        $rd_ids_details = ReCurringRd::where(['accountId' => $account])->first();

                        do {
                            $generalLedgers = 'savingrd'.time();
                        } while (GeneralLedger::where('serialNo', '=', $generalLedgers)->first() instanceof GeneralLedger);

                        DB::beginTransaction();
                        try {

                            //___________Entry in Member Saving Table
                            $saving_trfd_rd = new MemberSaving;
                            $saving_trfd_rd->secheme_id = $saving_scheme_id;
                            $saving_trfd_rd->serialNo = $generalLedgers;
                            $saving_trfd_rd->accountId = $account_opening->accountNo;
                            $saving_trfd_rd->accountNo = $post->membership;
                            $saving_trfd_rd->memberType = $post->memberType;
                            $saving_trfd_rd->groupCode = $saving_group;
                            $saving_trfd_rd->ledgerCode = $saving_ledger;
                            $saving_trfd_rd->savingNo = '';
                            $saving_trfd_rd->transactionDate = $transactionDate;
                            $saving_trfd_rd->transactionType = 'tord';
                            $saving_trfd_rd->depositAmount = 0;
                            $saving_trfd_rd->withdrawAmount = $amounts;
                            $saving_trfd_rd->paymentType = '';
                            $saving_trfd_rd->bank = '';
                            $saving_trfd_rd->chequeNo = 'trfdtoRd';
                            $saving_trfd_rd->narration = 'Trfd RD A/c -'.$post->rd_account_no ? 'Trfd RD A/c -'.$post->rd_account_no : $post->narration;
                            $saving_trfd_rd->branchId = session('branchId') ? session('branchId') : 1;
                            $saving_trfd_rd->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $saving_trfd_rd->agentId = $post->agentId;
                            $saving_trfd_rd->updatedBy = $post->user()->id;
                            $saving_trfd_rd->is_delete = 'No';
                            $saving_trfd_rd->save();

                            $saving_id = $saving_trfd_rd->id;

                            //_______________RD Receipt
                            $lastInsertedId = DB::table('rd_receiptdetails')->insertGetId([
                                'rc_account_no' => $rd_ids_details->id,
                                'rd_account_no' => $rd_ids_details->accountId,
                                'amount' => $amounts,
                                'serialNo' => $generalLedgers,
                                'payment_date' => $installmentdate,
                                'installment_date' => $installmentdate,
                                'groupCode' => $account_group_code,
                                'ledgerCode' => $account_ledger_code,
                                'memberType' => $rd_ids_details->memberType,
                                'panelty' => 0,
                                'mis_id' => '',
                                'narration' => 'Trfd Saving A/c -'.$account_opening->accountNo ? 'Trfd Saving A/c -'.$account_opening->accountNo : $post->narration,
                                'entry_mode' => 'manual',
                                'status' => 'trfdfromsaving',
                                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                                'agentid' => $post->agentId,
                                'updatedBy' => $post->user()->id,
                            ]);

                            //__________Rd Installment
                            for ($i = 1; $i <= $monthsToPay; $i++) {
                                $distributedPayment = min($monthly_installment_amount, $deposit_amount);
                                $deposit_amount -= $distributedPayment;
                                $query = RdInstallment::where(['rd_id' => $rd_account->id, 'payment_status' => 'pending'])->first();

                                if ($query && $query->payment_status == 'pending') {
                                    $query->payment_date = $installmentdate;
                                    if (! $penaltyApplied) {
                                        $query->panelty = empty($request->deposit_penalty) ? 0 : $post->deposit_penalty;
                                        $penaltyApplied = true;
                                    }
                                    $query->paid_amount = $distributedPayment;
                                    $query->panelty = 0;
                                    $query->recpt_id = $saving_id;
                                    $query->payment_status = 'paid';
                                    $query->serialNo = $generalLedgers;
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
                            $genral_ledger->formName = 'SavingTrfdRd';
                            $genral_ledger->referenceNo = $saving_id;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = 'Cr';
                            $genral_ledger->transactionAmount = $amounts;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            //____________Saving Account
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $generalLedgers;
                            $genral_ledger->accountId = $account_opening->accountNo;
                            $genral_ledger->accountNo = $account_opening->membershipno;
                            $genral_ledger->memberType = $account_opening->membertype;
                            $genral_ledger->formName = 'SavingTrfdRd';
                            $genral_ledger->groupCode = $saving_group;
                            $genral_ledger->ledgerCode = $saving_ledger;
                            $genral_ledger->referenceNo = $saving_id;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = 'Dr';
                            $genral_ledger->transactionAmount = $amounts;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            $changestatus = RdInstallment::where(['rd_id' => $rd_ids_details->id])->orderBy('intallment_no', 'desc')->first();
                            if ($changestatus->payment_status == 'paid') {
                                $moodifystatus = ReCurringRd::where(['id' => $rd_ids_details->id])->update(['status' => 'Active']);
                            }

                            if ($paymentSuccess) {
                                DB::commit();

                                $total = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('paid_amount');
                                $totalpanality = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('panelty');
                                $grand_total = $total + $totalpanality;

                                //_________Checked Account in Opening Account Table
                                $account_no = $post->accountNo;
                                $type = $post->memberType;

                                return $this->showDataTable($account_no,$type);
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();

                            return response()->json(['status' => 'fail', 'messages' => 'Some Technical issue occurred', 'error' => $e->getMessage()], 200);
                        }
                    }
                } else {
                    return response()->json(['status' => 'fail', 'messages' => 'Installment payment amount not satisfy.']);
                }
                break;
        }

    }

    public function fdtrfddailyaccount(Request $post){
        // dd($post->all());
        $rules = [
            'datessss' => 'required',
            'mtypes' => 'required',
            'saccount' => 'required',
            'principal_amount' => 'required',
            'fd_date' => 'required',
            'rate_of_interest' => 'required',
            'maturity_amount' => 'required',
            'interest_amount' => 'required',
            'maturity_date' => 'required',
            'fdaccounts' => 'required'
        ];


        $validator = Validator::make($post->all(), $rules);

        $date = date('Y-m-d', strtotime($post->datessss));

        $session_master = SessionMaster::find(Session::get('sessionId'));

        $amounts = $post->transactionAmount;

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->datessss)));

        if (! $result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        $account_no = $post->saccount;
        $member_ship = $post->mnumberss;

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
            ->where('opening_accounts.memberType',$post->mtypes)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.membershipno', $member_ship)
            // ->where
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

        // //_______Date Format Convert
        $transactionDate = $date;

        //________Get Account Open Date
        $member = DB::table('opening_accounts')->where(['memberType' => $post->mtypes, 'accountNo' => $account_no, 'membershipno' => $account_opening->membershipno])->first();

        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors(), 'messages' => 'Invalid account number']);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors(), 'messages' => 'Transaction date can not be less than account opening date']);
        }

        //__________serial Number
        do {
            $serialNo = 'saving'.time();
        } while (GeneralLedger::where('serialNo', '=', $serialNo)->first() instanceof GeneralLedger);


        DB::beginTransaction();
        try {
            //__________Get FD Details
            $fdid = $post->fdid;
            $fd =opening_accounts::where('opening_accounts.id',$fdid)
            ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
            ->select(
                'opening_accounts.*',
                'member_accounts.name',
                'scheme_masters.id as scheme_id',
                'scheme_masters.name as scheme_name',
                'scheme_masters.scheme_code',
                'scheme_masters.durationType',
                'scheme_masters.days',
                'scheme_masters.months',
                'scheme_masters.years',
                'scheme_masters.interest',
                'scheme_masters.penaltyInterest',
                'scheme_masters.secheme_type',
                'scheme_masters.status as scheme_status',
                'scheme_masters.lockin_days',
                'scheme_masters.renewInterestType',
                'ledger_masters.groupCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.groupCode',
            )->first();
            // dd($fd);

            if ($fd->groupCode && $fd->ledgerCode) {
                $fd_group = $fd->groupCode;
                $fd_ledger = $fd->ledgerCode;

            } else {
                return response()->json(['stauts' => 'fail', 'messages' => 'FD Group/Ledger Code Not Found']);
            }

            $amounts = $post->principal_amount;

            $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

            if ($checkpaymentbalance) {
                return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
            }


            //___________Entry in Member Saving Table
            $saving_withdraw = new MemberSaving;
            $saving_withdraw->secheme_id = $account_opening->sch_id;
            $saving_withdraw->serialNo = $serialNo;
            $saving_withdraw->accountId = $account_no;
            $saving_withdraw->accountNo = $post->mnumberss;
            $saving_withdraw->memberType = $post->mtypes;
            $saving_withdraw->groupCode = $account_opening->groupCode;
            $saving_withdraw->ledgerCode = $account_opening->ledgerCode;
            $saving_withdraw->savingNo = '';
            $saving_withdraw->transactionDate = $transactionDate;
            $saving_withdraw->transactionType = 'toFd';
            $saving_withdraw->depositAmount = 0;
            $saving_withdraw->withdrawAmount = $amounts;
            $saving_withdraw->paymentType = '';
            $saving_withdraw->bank = '';
            $saving_withdraw->chequeNo = 'trfdSavingtoFD';
            $saving_withdraw->narration = 'Saving A/c- '.$account_no.' - to Trfd FD'.$post->fdaccounts ? 'Saving A/c- '.$account_no.'to Trfd FD -'.$post->fdaccounts : $post->narration;
            $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
            $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
            $saving_withdraw->agentId = $post->agentId;
            $saving_withdraw->updatedBy = $post->user()->id;
            $saving_withdraw->is_delete = 'No';
            $saving_withdraw->save();

            //________Get Saving Account Id
            $saving_id = $saving_withdraw->id;
            $saving_accounts = $saving_withdraw->accountId;

            //___________Entry in Member FD Table
            $member_fd = new MemberFdScheme;
            $member_fd->serialNo = $serialNo;
            $member_fd->autorenew = $post->autorenew;
            $member_fd->secheme_id = $fd->schemetype;
            $member_fd->matureserialNo = '';
            $member_fd->accountId = $fdid;
            $member_fd->membershipno = $post->mnumberss;
            $member_fd->accountNo = $post->fdaccounts;
            $member_fd->memberType = $post->mtypes;
            $member_fd->groupCode = $fd_group;
            $member_fd->ledgerCode = $fd_ledger;
            $member_fd->fdNo = $post->fdaccounts;
            $member_fd->fdType = $fd->fdtypeid;
            $member_fd->openingDate = date('Y-m-d', strtotime($post->fd_date));
            $member_fd->principalAmount = $amounts;
            $member_fd->interestType = $post->interestType;
            $member_fd->interestStartDate = date('Y-m-d', strtotime($post->fd_date));
            $member_fd->interestRate = $post->rate_of_interest;
            $member_fd->interestAmount = $post->interest_amount;
            $member_fd->years = $post->years;
            $member_fd->months = $post->months;
            $member_fd->days = $post->days;
            $member_fd->maturityDate = date('Y-m-d', strtotime($post->maturity_date));
            $member_fd->onmaturityDate = null;
            $member_fd->maturityAmount = $post->maturity_amount;
            $member_fd->actualMaturityDate = null;
            $member_fd->actualInterestAmount = 0;
            $member_fd->actualMaturityAmount = 0;
            $member_fd->ledgerNo = '';
            $member_fd->pageNo = '';
            $member_fd->narration = 'Saving A/c- '.$account_no.' - to Trfd FD'.$post->fd_account_no ? 'Saving A/c- '.$account_no.'to Trfd FD -'.$post->fd_account_no : $post->narration;
            $member_fd->transferedFrom = 'trfdSavingtoFD';
            $member_fd->paymentType =  $account_opening->groupCode;
            $member_fd->bank = $account_opening->ledgerCode;
            $member_fd->chequeNo = 'trfdSavingtoFD';
            $member_fd->transferedTo = '';
            $member_fd->transferedPaymentType = '';
            $member_fd->transferedBank = '';
            $member_fd->transferedChequeNo = '';
            $member_fd->nomineeName1 = '';
            $member_fd->nomineeRelation1 = '';
            $member_fd->nomineeBirthDate1 = null;
            $member_fd->nomineePhone1 = null;
            $member_fd->nomineeAddress1 = '';
            $member_fd->nomineeName2 = '';
            $member_fd->nomineeRelation2 = '';
            $member_fd->nomineeBirthDate2 = null;
            $member_fd->nomineePhone2 = null;
            $member_fd->nomineeAddress2 = '';
            $member_fd->renewDate = null;
            $member_fd->oldFdNo = '';
            $member_fd->status = 'Active';
            $member_fd->agentId = $post->agentId;
            $member_fd->branchId = session('branchId') ?: 1;
            $member_fd->sessionId = session('sessionId') ?: 1;
            $member_fd->updatedBy = $post->user()->id;
            $member_fd->is_delete = 'No';
            $member_fd->save();

            //________________________________________General Ledger Entry_________________________

            //________Saving Entry
            $gerenal_ledger = new GeneralLedger;
            $gerenal_ledger->serialNo = $serialNo;
            $gerenal_ledger->accountId = $account_no;
            $gerenal_ledger->accountNo = $post->mnumberss;
            $gerenal_ledger->memberType = $post->mtypes;
            $gerenal_ledger->groupCode = $account_opening->groupCode;
            $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
            $gerenal_ledger->formName = 'trfdSavingtoFD';
            $gerenal_ledger->referenceNo = $saving_id;
            $gerenal_ledger->entryMode = 'Manual';
            $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->datessss));
            $gerenal_ledger->transactionType = 'Dr';
            $gerenal_ledger->transactionAmount = $amounts;
            $gerenal_ledger->narration = 'FD A/c- '.$post->fdaccounts.' Trfd FD' ? 'Saving A/c-'.$post->fdaccounts.'Trfd FD' : $post->narration;
            $gerenal_ledger->branchId = session('branchId') ?: 1;
            $gerenal_ledger->sessionId = session('sessionId') ?: 1;
            $gerenal_ledger->agentId = $post->agentId;
            $gerenal_ledger->updatedBy = $post->user()->id;
            $gerenal_ledger->is_delete = 'No';
            $gerenal_ledger->save();

            //________FD Entry
            $gerenal_ledger = new GeneralLedger;
            $gerenal_ledger->serialNo = $serialNo;
            $gerenal_ledger->accountId = $post->fdaccounts;
            $gerenal_ledger->accountNo = $post->mnumberss;
            $gerenal_ledger->memberType = $post->mtypes;
            $gerenal_ledger->groupCode = $fd_group;
            $gerenal_ledger->ledgerCode = $fd_ledger;
            $gerenal_ledger->formName = 'trfdSavingtoFD';
            $gerenal_ledger->referenceNo = $saving_id;
            $gerenal_ledger->entryMode = 'Manual';
            $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->datessss));
            $gerenal_ledger->transactionType = 'Cr';
            $gerenal_ledger->transactionAmount = $amounts;
            $gerenal_ledger->narration = 'Saving A/c- '.$account_no.' Trfd FD' ? 'Saving A/c-'.$account_no.'Trfd FD' : $post->narration;
            $gerenal_ledger->branchId = session('branchId') ?: 1;
            $gerenal_ledger->sessionId = session('sessionId') ?: 1;
            $gerenal_ledger->agentId = $post->agentId;
            $gerenal_ledger->updatedBy = $post->user()->id;
            $gerenal_ledger->is_delete = 'No';
            $gerenal_ledger->save();

            DB::commit();

            //_________Checked Account in Opening Account Table
            $account_no = $post->saccount;
            $type = $post->mtypes;

            return $this->showDataTable($account_no,$type);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getMessage(),'line' => $e->getLine()]);
        }
    }

    public function fdtrfddailyupdate(Request $post){

        $savingId = $post->savingfddids;
        $member_saving = DB::table('member_savings')->where('id', $savingId)->first();


        if (is_null($member_saving)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        $check_saving_Id = DB::table('member_savings')->where('id', $savingId)->first();

        $date = date('Y-m-d', strtotime($post->datessss));
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->datessss)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => 'Please Check your session']);
        }

        $transactionDate = $date;
        $member_ship = $post->mnumberss;

        // Fetch account details and opening information
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
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();


            $account_nos = $account_opening->accountNo;

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

            // Ensure transaction date is after the account opening date
            $member = DB::table('opening_accounts')
                ->where('memberType',$post->mtypes)
                ->where('accountNo',$account_opening->accountNo)
                ->where('membershipno',$member_ship)
                ->first();


            if ($transactionDate < $member->transactionDate) {
                return response()->json(['status' => 'Fail', 'message' => 'Transaction date cannot be less than account opening date']);
            }

            // Generate a unique serial number for the new transaction
            do {
                $serialNo = 'saving'.time();
            } while (GeneralLedger::where('serialNo', '=', $serialNo)->first() instanceof GeneralLedger);

        DB::beginTransaction();

        try{
            // Delete related records
            DB::table('general_ledgers')
                ->where('referenceNo', $member_saving->id)
                ->where('serialNo', $member_saving->serialNo)
                ->delete();

            DB::table('member_fds_scheme')
                ->where('serialNo', $member_saving->serialNo)
                ->delete();

            DB::table('member_savings')->where('id', $savingId)->delete();



            $amounts = $post->principal_amount;
            $account_no = $account_opening->accountNo;
            $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

            if ($checkpaymentbalance) {
                return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
            }


            //__________Get FD Details
            $fdid = $post->fdid;
            $fd = opening_accounts::where('opening_accounts.id',$fdid)
            ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
            ->select(
                'opening_accounts.*',
                'member_accounts.name',
                'scheme_masters.id as scheme_id',
                'scheme_masters.name as scheme_name',
                'scheme_masters.scheme_code',
                'scheme_masters.durationType',
                'scheme_masters.days',
                'scheme_masters.months',
                'scheme_masters.years',
                'scheme_masters.interest',
                'scheme_masters.penaltyInterest',
                'scheme_masters.secheme_type',
                'scheme_masters.status as scheme_status',
                'scheme_masters.lockin_days',
                'scheme_masters.renewInterestType',
                'ledger_masters.groupCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.ledgerCode',
                'ledger_masters.groupCode',
            )->first();

            if ($fd->groupCode && $fd->ledgerCode) {
                $fd_group = $fd->groupCode;
                $fd_ledger = $fd->ledgerCode;

            } else {
                return response()->json(['stauts' => 'fail', 'messages' => 'FD Group/Ledger Code Not Found']);
            }

            $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

            if ($checkpaymentbalance) {
                return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
            }


            //___________Entry in Member Saving Table
            $saving_withdraw = new MemberSaving;
            $saving_withdraw->secheme_id = $account_opening->sch_id;
            $saving_withdraw->serialNo = $serialNo;
            $saving_withdraw->accountId = $account_no;
            $saving_withdraw->accountNo = $post->mnumberss;
            $saving_withdraw->memberType = $post->mtypes;
            $saving_withdraw->groupCode = $account_opening->groupCode;
            $saving_withdraw->ledgerCode = $account_opening->ledgerCode;
            $saving_withdraw->savingNo = '';
            $saving_withdraw->transactionDate = $transactionDate;
            $saving_withdraw->transactionType = 'toFd';
            $saving_withdraw->depositAmount = 0;
            $saving_withdraw->withdrawAmount = $amounts;
            $saving_withdraw->paymentType = '';
            $saving_withdraw->bank = '';
            $saving_withdraw->chequeNo = 'trfdSavingtoFD';
            $saving_withdraw->narration = 'Saving A/c- '.$account_no.' - to Trfd FD'.$post->fdaccounts ? 'Saving A/c- '.$account_no.'to Trfd FD -'.$post->fdaccounts : $post->narration;
            $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
            $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
            $saving_withdraw->agentId = $post->agentId;
            $saving_withdraw->updatedBy = $post->user()->id;
            $saving_withdraw->is_delete = 'No';
            $saving_withdraw->save();

            //________Get Saving Account Id
            $saving_id = $saving_withdraw->id;
            $saving_accounts = $saving_withdraw->accountId;

            //___________Entry in Member FD Table
            $member_fd = new MemberFdScheme;
            $member_fd->serialNo = $serialNo;
            $member_fd->autorenew = $post->autorenew;
            $member_fd->secheme_id = $fd->schemetype;
            $member_fd->matureserialNo = '';
            $member_fd->accountId = $fdid;
            $member_fd->membershipno = $post->mnumberss;
            $member_fd->accountNo = $post->fdaccounts;
            $member_fd->memberType = $post->mtypes;
            $member_fd->groupCode = $fd_group;
            $member_fd->ledgerCode = $fd_ledger;
            $member_fd->fdNo = $post->fdaccounts;
            $member_fd->fdType = $fd->fdtype;
            $member_fd->openingDate = date('Y-m-d', strtotime($post->fd_date));
            $member_fd->principalAmount = $amounts;
            $member_fd->interestType = $post->interestType;
            $member_fd->interestStartDate = date('Y-m-d', strtotime($post->fd_date));
            $member_fd->interestRate = $post->rate_of_interest;
            $member_fd->interestAmount = $post->maturity_amount;
            $member_fd->years = $post->years;
            $member_fd->months = $post->months;
            $member_fd->days = $post->days;
            $member_fd->maturityDate = date('Y-m-d', strtotime($post->maturity_date));
            $member_fd->onmaturityDate = null;
            $member_fd->maturityAmount = $post->maturity_amount;
            $member_fd->actualMaturityDate = null;
            $member_fd->actualInterestAmount = 0;
            $member_fd->actualMaturityAmount = 0;
            $member_fd->ledgerNo = '';
            $member_fd->pageNo = '';
            $member_fd->narration = 'Saving A/c- '.$account_no.' - to Trfd FD'.$post->fdaccounts ? 'Saving A/c- '.$account_no.'to Trfd FD -'.$post->fdaccounts : $post->narration;
            $member_fd->transferedFrom = 'trfdSavingtoFD';
            $member_fd->paymentType =  $account_opening->groupCode;
            $member_fd->bank = $account_opening->ledgerCode;
            $member_fd->chequeNo = 'trfdSavingtoFD';
            $member_fd->transferedTo = '';
            $member_fd->transferedPaymentType = '';
            $member_fd->transferedBank = '';
            $member_fd->transferedChequeNo = '';
            $member_fd->nomineeName1 = '';
            $member_fd->nomineeRelation1 = '';
            $member_fd->nomineeBirthDate1 = null;
            $member_fd->nomineePhone1 = null;
            $member_fd->nomineeAddress1 = '';
            $member_fd->nomineeName2 = '';
            $member_fd->nomineeRelation2 = '';
            $member_fd->nomineeBirthDate2 = null;
            $member_fd->nomineePhone2 = null;
            $member_fd->nomineeAddress2 = '';
            $member_fd->renewDate = null;
            $member_fd->oldFdNo = '';
            $member_fd->status = 'Active';
            $member_fd->agentId = $post->agentId;
            $member_fd->branchId = session('branchId') ?: 1;
            $member_fd->sessionId = session('sessionId') ?: 1;
            $member_fd->updatedBy = $post->user()->id;
            $member_fd->is_delete = 'No';
            $member_fd->save();

            //________________________________________General Ledger Entry_________________________

            //________Saving Entry
            $gerenal_ledger = new GeneralLedger;
            $gerenal_ledger->serialNo = $serialNo;
            $gerenal_ledger->accountId = $account_no;
            $gerenal_ledger->accountNo = $post->mnumberss;
            $gerenal_ledger->memberType = $post->mtypes;
            $gerenal_ledger->groupCode = $account_opening->groupCode;
            $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
            $gerenal_ledger->formName = 'trfdSavingtoFD';
            $gerenal_ledger->referenceNo = $saving_id;
            $gerenal_ledger->entryMode = 'Manual';
            $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->datessss));
            $gerenal_ledger->transactionType = 'Dr';
            $gerenal_ledger->transactionAmount = $amounts;
            $gerenal_ledger->narration = 'FD A/c- '.$post->fdaccounts.' Trfd FD' ? 'Saving A/c-'.$post->fdaccounts.'Trfd FD' : $post->narration;
            $gerenal_ledger->branchId = session('branchId') ?: 1;
            $gerenal_ledger->sessionId = session('sessionId') ?: 1;
            $gerenal_ledger->agentId = $post->agentId;
            $gerenal_ledger->updatedBy = $post->user()->id;
            $gerenal_ledger->is_delete = 'No';
            $gerenal_ledger->save();

            //________FD Entry
            $gerenal_ledger = new GeneralLedger;
            $gerenal_ledger->serialNo = $serialNo;
            $gerenal_ledger->accountId = $post->fdaccounts;
            $gerenal_ledger->accountNo = $post->mnumberss;
            $gerenal_ledger->memberType = $post->mtypes;
            $gerenal_ledger->groupCode = $fd_group;
            $gerenal_ledger->ledgerCode = $fd_ledger;
            $gerenal_ledger->formName = 'trfdSavingtoFD';
            $gerenal_ledger->referenceNo = $saving_id;
            $gerenal_ledger->entryMode = 'Manual';
            $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->datessss));
            $gerenal_ledger->transactionType = 'Cr';
            $gerenal_ledger->transactionAmount = $amounts;
            $gerenal_ledger->narration = 'Saving A/c- '.$account_no.' Trfd FD' ? 'Saving A/c-'.$account_no.'Trfd FD' : $post->narration;
            $gerenal_ledger->branchId = session('branchId') ?: 1;
            $gerenal_ledger->sessionId = session('sessionId') ?: 1;
            $gerenal_ledger->agentId = $post->agentId;
            $gerenal_ledger->updatedBy = $post->user()->id;
            $gerenal_ledger->is_delete = 'No';
            $gerenal_ledger->save();

            DB::commit();

            // //_________Checked Account in Opening Account Table
            $account_no = $account_opening->accountNo;
            $type = $post->memberType;
            return $this->showDataTable($account_no,$type);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getLine()]);
        }
    }



    //___________Delete Entry
    public function DeleteSavingEntry(Request $post){
        $id = $post->id;
        $member_saving = DB::table('member_savings')->where('id', $id)->first();
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed',
            ]);
        }

        $transactionType = $post->transactionType;

        switch ($transactionType) {
            case 'Deposit':
                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_saving = DB::table('member_savings')->where('id', $id)->first();

                    if (is_null($member_saving)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    $trfrdtype = $post->trfrdtype;

                    if($trfrdtype === 'Interest Received'){
                        DB::table('interest_calculations')->where('serialNo',$member_saving->serialNo)->delete();

                        //_________Delete from General Ledger
                        DB::table('general_ledgers')
                            ->where('accountId', $member_saving->accountId)
                            ->where('accountNo', $member_saving->accountNo)
                            ->where('serialNo', $member_saving->serialNo)
                            ->delete();

                         //_______Delete from Member Savings
                        DB::table('member_savings')->where('id', $id)->delete();


                    }else{
                        //_________Delete from General Ledger
                        DB::table('general_ledgers')
                        ->where('referenceNo', $member_saving->id)
                        ->where('accountId', $member_saving->accountId)
                        ->where('accountNo', $member_saving->accountNo)
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();
                         //_______Delete from Member Savings
                        DB::table('member_savings')->where('id', $id)->delete();
                    }

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;

                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issues', 'error' => $e->getMessage()]);
                }
                break;

            case 'Withdraw':
                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_saving = DB::table('member_savings')->where('id', $id)->first();

                    if (is_null($member_saving)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Delete from General Ledger
                    DB::table('general_ledgers')
                        ->where('referenceNo', $member_saving->id)
                        ->where('accountId', $member_saving->accountId)
                        ->where('accountNo', $member_saving->accountNo)
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //___________Delete from Member Savings
                    DB::table('member_savings')->where('id', $id)->delete();

                    // Commit transaction
                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;


                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issues', 'error' => $e->getMessage()]);
                }
                break;

            case 'toshare':
                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_saving = DB::table('member_savings')->where('id', $id)->first();

                    if (is_null($member_saving)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Delete from General Ledger
                    DB::table('general_ledgers')
                        ->where('referenceNo', $member_saving->id)
                        ->where('accountId', $member_saving->accountId)
                        ->where('accountNo', $member_saving->accountNo)
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //________Delete Member Share
                    DB::table('member_shares')
                        ->where('accountId', $member_saving->accountId)
                        ->where('accountNo', $member_saving->accountNo)
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //___________Delete from Member Savings
                    DB::table('member_savings')->where('id', $id)->delete();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;


                    return $this->showDataTable($account_no,$type);
                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issues', 'error' => $e->getMessage()]);
                }
                break;

            case 'toFd':

                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_saving = DB::table('member_savings')->where('id', $id)->first();

                    if (is_null($member_saving)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Delete from General Ledger
                    DB::table('general_ledgers')
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //________Delete Member FD
                    DB::table('member_fds_scheme')
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //___________Delete from Member Savings
                    DB::table('member_savings')->where('id', $id)->delete();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;


                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issues', 'error' => $e->getMessage()]);
                }
                break;

            case 'tord':
                try {
                    DB::beginTransaction();


                    $id = $post->id;
                    $member_savings = DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->first();



                    if (is_null($member_savings)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Get Member RD Installemt Table
                    $rd_receipt_amount = DB::table('rd_receiptdetails')
                        ->where('serialNo', $member_savings->serialNo)
                        ->first();


                    if ($rd_receipt_amount) {
                        //__________Get Rd Table Detail
                        $rd_ids_details = ReCurringRd::where('accountId', $rd_receipt_amount->rd_account_no)->first();
                        // dd($rd_ids_details);

                        DB::table('general_ledgers')
                            ->where('referenceNo', $member_savings->id)
                            ->where('serialNo', $member_savings->serialNo)
                            ->delete();

                        //________Installments Update
                        $installment_update = DB::table('rd_installments')
                            ->where('serialNo', $member_savings->serialNo)
                            ->where('recpt_id', $member_savings->id)
                            ->get();


                        $installmentIds = $installment_update->pluck('id')->toArray();

                        //_________Update Rd Installemt Status
                        DB::table('rd_installments')
                            ->whereIn('id', $installmentIds)
                            ->update([
                                'paid_amount' => 0,
                                'panelty' => 0,
                                'recpt_id' => null,
                                'payment_date' => null,
                                'payment_status' => 'pending',
                                'serialNo' => $rd_ids_details->serialNo,
                            ]);

                        //________Get Member RD Installemt Table
                        DB::table('rd_receiptdetails')
                            ->where('serialNo', $member_savings->serialNo)
                            ->delete();

                        //___________Delete from Member Savings
                        DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->delete();

                        DB::commit();
                    }



                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;


                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issues', 'error' => $e->getMessage()]);
                }

                break;
            case 'DailySaving':
                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_savings = DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->first();


                    if (is_null($member_savings)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Get Member RD Installemt Table
                    $dds_receipt = DB::table('daily_collectionsavings')
                        ->where('serialNo', $member_savings->serialNo)
                        ->first();


                    if ($dds_receipt) {
                        //__________Get Rd Table Detail
                        $daily_ss = DailyCollection::where('account_no', $dds_receipt->account_no)->first();
                        // dd($rd_ids_details);

                        DB::table('general_ledgers')
                            ->where('referenceNo', $member_savings->id)
                            ->where('serialNo', $member_savings->serialNo)
                            ->delete();

                        //________Installments Update
                        $installment_update = DB::table('dailysavinginstallment')
                            ->where('serialNo', $member_savings->serialNo)
                            ->where('recpt_id', $member_savings->id)
                            ->get();


                        $installmentIds = $installment_update->pluck('id')->toArray();

                        //_________Update Rd Installemt Status
                        DB::table('dailysavinginstallment')
                            ->whereIn('id', $installmentIds)
                            ->update([
                                'paid_amount' => 0,
                                'panelty' => 0,
                                'recpt_id' => null,
                                'payment_date' => null,
                                'payment_status' => 'pending',
                                'serialNo' => $daily_ss->serialNo,
                            ]);

                        //________Get Member RD Installemt Table
                        DB::table('daily_collectionsavings')
                            ->where('serialNo', $member_savings->serialNo)
                            ->delete();

                        //___________Delete from Member Savings
                        DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->delete();

                        DB::commit();
                    }



                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;


                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'Some Technical Issues',
                        'error' => $e->getMessage()
                    ]);
                }
                break;
            case 'toCCL':
                try {
                    DB::beginTransaction();

                    $id = $post->id;
                    $member_savings = DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->first();


                    if (is_null($member_savings)) {
                        return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                    }

                    //________Get Member CCL Payament Installemt Table
                    $cclPayment = DB::table('ccl_payments')
                        ->where('serialNo', $member_savings->serialNo)
                        ->delete();


                    DB::table('general_ledgers')
                        ->where('referenceNo', $member_savings->id)
                        ->where('serialNo', $member_savings->serialNo)
                        ->delete();

                    DB::table('member_savings')
                        ->where('id', $id)
                        ->where('transactionType',$post->transactionType)
                        ->delete();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->accountNo;
                    $type = $member_saving->memberType;

                    return $this->showDataTable($account_no,$type);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'Some Technical Issues',
                        'error' => $e->getMessage()
                    ]);
                }
            break;
        }

    }



    public function SavingEntryUpdate(Request $post)
    {
        $savingId = $post->savingId;
        $check_saving_Id = DB::table('member_savings')->where('id', $savingId)->first();


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

        DB::beginTransaction();
        try {
            $transactionType = $post->transactionType;

            //________Delete existing entries based on the transaction type
            if ($transactionType === 'toshare') {

              DB::table('member_shares')
                    ->where('accountNo', $check_saving_Id->accountNo)
                    ->where('accountId', $check_saving_Id->accountId)
                    ->where('serialNo', $check_saving_Id->serialNo)
                    ->delete();

                // Delete related General Ledger and Member Shares
                DB::table('general_ledgers')
                    ->where('referenceNo', $check_saving_Id->id)
                    ->where('accountId', $check_saving_Id->accountId)
                    ->where('accountNo', $check_saving_Id->accountNo)
                    ->where('serialNo', $check_saving_Id->serialNo)
                    ->delete();


                // Delete the saving entry
              DB::table('member_savings')->where('id', $savingId)->delete();

            } elseif ($transactionType === 'tord') {

                $member_saving = DB::table('member_savings')->where('id', $savingId)->first();

                if (is_null($member_saving)) {
                    return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
                }

                //________Get Member RD Installemt Table
                $rd_receipt_amount = DB::table('rd_receiptdetails')
                    ->where('serialNo', $member_saving->serialNo)
                    ->first();

                if ($rd_receipt_amount) {

                    //__________Get Rd Table Detail
                    $rd_ids_details = ReCurringRd::where('accountId', $rd_receipt_amount->rd_account_no)->first();

                    DB::table('general_ledgers')
                        ->where('referenceNo', $member_saving->id)
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //________Installments Update
                    $installment_update = DB::table('rd_installments')
                        ->where('serialNo', $member_saving->serialNo)
                        ->where('recpt_id', $member_saving->id)
                        ->get();

                    $installmentIds = $installment_update->pluck('id')->toArray();

                    //_________Update Rd Installemt Status
                    DB::table('rd_installments')
                        ->whereIn('id', $installmentIds)
                        ->update([
                            'paid_amount' => 0,
                            'panelty' => 0,
                            'recpt_id' => null,
                            'payment_date' => null,
                            'payment_status' => 'pending',
                            'serialNo' => $rd_ids_details->serialNo,
                        ]);

                    //________Get Member RD Installemt Table
                    DB::table('rd_receiptdetails')
                        ->where('serialNo', $member_saving->serialNo)
                        ->delete();

                    //___________Delete from Member Savings
                    DB::table('member_savings')->where('id', $savingId)->delete();
                }
            } else {

                // Delete related General Ledger and Saving Entry for other transaction types
                DB::table('general_ledgers')
                    ->where('referenceNo', $check_saving_Id->id)
                    ->where('accountId', $check_saving_Id->accountId)
                    ->where('accountNo', $check_saving_Id->accountNo)
                    ->where('serialNo', $check_saving_Id->serialNo)
                    ->delete();

                DB::table('member_savings')->where('id', $savingId)->delete();
            }

            $transactionDate = $date;


            $member_ship = $post->membership;

            // Fetch account details and opening information
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
                ->where('opening_accounts.memberType', $post->memberType)
                ->where('opening_accounts.membershipno', $member_ship)
                ->where('opening_accounts.accountname', 'Saving')
                ->first();
            $account_nos = $account_opening->accountNo;

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

            // Ensure transaction date is after the account opening date
            $member = DB::table('opening_accounts')
                ->where('memberType',$post->memberType)
                ->where('accountNo',$account_opening->accountNo)
                ->where('membershipno',$member_ship)
                ->first();



            if ($transactionDate < $member->transactionDate) {
                return response()->json(['status' => 'Fail', 'message' => 'Transaction date cannot be less than account opening date']);
            }

            // Generate a unique serial number for the new transaction
            do {
                $serialNo = 'saving'.time();
            } while (GeneralLedger::where('serialNo', '=', $serialNo)->first() instanceof GeneralLedger);



            // Handle 'toshare' transaction type
            if ($transactionType == 'toshare') {
                $amounts = $post->transactionAmount;
                $account_no = $check_saving_Id->accountId;

                $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                if ($checkpaymentbalance) {
                    return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                }

                $transactionDate = date('Y-m-d', strtotime($post->transactionDate));

                //________Get Account Open Date
                $member = DB::table('opening_accounts')->where(['memberType' => $post->memberType, 'accountNo' => $account_nos, 'membershipno' => $member_ship])->first();

                //___________Entry in Member Saving Table
                $saving_withdraw = new MemberSaving;
                $saving_withdraw->secheme_id = $account_opening->sch_id;
                $saving_withdraw->serialNo = $serialNo;
                $saving_withdraw->accountId = $account_nos;
                $saving_withdraw->accountNo = $member_ship;
                $saving_withdraw->memberType = $post->memberType;
                $saving_withdraw->groupCode = $account_opening->groupCode;
                $saving_withdraw->ledgerCode = $account_opening->ledgerCode;
                $saving_withdraw->savingNo = '';
                $saving_withdraw->transactionDate = $transactionDate;
                $saving_withdraw->transactionType = 'toshare';
                $saving_withdraw->depositAmount = 0;
                $saving_withdraw->withdrawAmount = $amounts;
                $saving_withdraw->paymentType = '';
                $saving_withdraw->bank = '';
                $saving_withdraw->chequeNo = 'trfdShare';
                $saving_withdraw->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                $saving_withdraw->agentId = $post->agentId;
                $saving_withdraw->updatedBy = $post->user()->id;
                $saving_withdraw->is_delete = 'No';
                $saving_withdraw->save();

                //________Get Saving Account Id
                $saving_id = $saving_withdraw->id;

                //___________Entry in Member Share Table
                $saving_trfd_share = new MemberShare;
                $saving_trfd_share->serialNo = $serialNo;
                $saving_trfd_share->accountId = $account_nos;
                $saving_trfd_share->accountNo = $post->membership;
                $saving_trfd_share->memberType = $post->memberType;
                $saving_trfd_share->groupCode = 'SHAM001';
                $saving_trfd_share->ledgerCode = 'SHAM001';
                $saving_trfd_share->shareNo = '';
                $saving_trfd_share->transactionDate = $transactionDate;
                $saving_trfd_share->transactionType = 'Deposit';
                $saving_trfd_share->depositAmount = $amounts;
                $saving_trfd_share->withdrawAmount = 0;
                $saving_trfd_share->dividendAmount = 0;
                $saving_trfd_share->chequeNo = 'trfdShare';
                $saving_trfd_share->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                $saving_trfd_share->branchId = session('branchId') ? session('branchId') : 1;
                $saving_trfd_share->sessionId = session('sessionId') ? session('sessionId') : 1;
                $saving_trfd_share->agentId = $post->agentId;
                $saving_trfd_share->updatedBy = $post->user()->id;
                $saving_trfd_share->txnType = 'transfer';
                $saving_trfd_share->is_delete = 'No';
                $saving_trfd_share->save();

                //__________________________________________________General Ledger Entry___________________________________________

                //________Saving Entry
                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->groupCode = $account_opening->groupCode;
                $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                $gerenal_ledger->formName = 'trfdShare';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Dr';
                $gerenal_ledger->transactionAmount = $amounts;
                $gerenal_ledger->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = $post->agentId;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $saving_trfd_share->is_delete = 'No';
                $gerenal_ledger->save();

                //________Share Entry
                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->groupCode = 'SHAM001';
                $gerenal_ledger->ledgerCode = 'SHAM001';
                $gerenal_ledger->formName = 'trfdShare';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Cr';
                $gerenal_ledger->transactionAmount = $amounts;
                $gerenal_ledger->narration = 'Saving A/c- '.$post->membership.' Trfd Share' ? 'Saving A/c-'.$post->membership.'Trfd Share' : $post->narration;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = $post->agentId;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $saving_trfd_share->is_delete = 'No';
                $gerenal_ledger->save();

                DB::commit();

                //_________Checked Account in Opening Account Table
                $account_no = $post->accountNo;

                $type = $post->memberType;

                return $this->showDataTable($account_no,$type);

            } elseif ($transactionType == 'Deposit') {

                if ($post->groupCode && $post->bank) {
                    $cash_bank_group = $post->groupCode;
                    $cash_bank_ledger = $post->bank;
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group And Ledger Code Not Found']);
                }

                $saving_entry = new MemberSaving;
                $saving_entry->secheme_id = $account_opening->sch_id;
                $saving_entry->serialNo = $serialNo;
                $saving_entry->accountId = $account_nos;
                $saving_entry->accountNo = $post->membership;
                $saving_entry->memberType = $post->memberType;
                $saving_entry->groupCode = $account_opening->groupCode;
                $saving_entry->ledgerCode = $account_opening->ledgerCode;
                $saving_entry->transactionDate = $transactionDate;
                $saving_entry->transactionType = $transactionType;
                $saving_entry->depositAmount = $post->transactionAmount;
                $saving_entry->withdrawAmount = 0;
                $saving_entry->paymentType = $cash_bank_group;
                $saving_entry->bank = $cash_bank_ledger;
                $saving_entry->chequeNo = '';
                $saving_entry->narration = $post->narration ?? 'Amount Deposit';
                $saving_entry->branchId = session('branchId') ?: 1;
                $saving_entry->sessionId = session('sessionId') ?: 1;
                $saving_entry->agentId = $post->agentId;
                $saving_entry->updatedBy = $post->user()->id;
                $saving_entry->is_delete = 'No';
                $saving_entry->save();

                $saving_id = $saving_entry->id;

                //________________________________________General Ledger Entry_________________________

                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->formName = 'Saving';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Cr';
                $gerenal_ledger->transactionAmount = $post->transactionAmount;
                $gerenal_ledger->narration = $post->narration;
                $gerenal_ledger->groupCode = $account_opening->groupCode;
                $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = 1;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $gerenal_ledger->save();

                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->formName = 'Saving';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Dr';
                $gerenal_ledger->transactionAmount = $post->transactionAmount;
                $gerenal_ledger->narration = $post->narration;
                $gerenal_ledger->groupCode = $cash_bank_group;
                $gerenal_ledger->ledgerCode = $cash_bank_ledger;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = 1;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $gerenal_ledger->save();

                DB::commit();

                //_________Checked Account in Opening Account Table
                $account_no = $post->accountNo;

                $type = $post->memberType;

            return $this->showDataTable($account_no,$type);

            } elseif ($transactionType == 'Withdraw') {

                if ($post->groupCode && $post->bank) {
                    $cash_bank_group = $post->groupCode;
                    $cash_bank_ledger = $post->bank;
                } else {
                    return response()->json(['status' => 'Fail', 'messages' => 'Cash/Bank Group And Ledger Code Not Found']);
                }

                $amounts = $post->transactionAmount;
                $account_no = $check_saving_Id->accountNo;

                $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                if ($checkpaymentbalance) {
                    return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                }

                $saving_entry = new MemberSaving;
                $saving_entry->secheme_id = $account_opening->sch_id;
                $saving_entry->serialNo = $serialNo;
                $saving_entry->accountId = $account_nos;
                $saving_entry->accountNo = $post->membership;
                $saving_entry->memberType = $post->memberType;
                $saving_entry->groupCode = $account_opening->groupCode;
                $saving_entry->ledgerCode = $account_opening->ledgerCode;
                $saving_entry->transactionDate = $transactionDate;
                $saving_entry->transactionType = $transactionType;
                $saving_entry->depositAmount = 0;
                $saving_entry->withdrawAmount = $amounts;
                $saving_entry->paymentType = $cash_bank_group;
                $saving_entry->bank = $cash_bank_ledger;
                $saving_entry->chequeNo = '';
                $saving_entry->narration = $post->narration ?? 'Amount Withdraw';
                $saving_entry->branchId = session('branchId') ?: 1;
                $saving_entry->sessionId = session('sessionId') ?: 1;
                $saving_entry->agentId = $post->agentId;
                $saving_entry->updatedBy = $post->user()->id;
                $saving_entry->is_delete = 'No';
                $saving_entry->save();

                $saving_id = $saving_entry->id;

                //________________________________________General Ledger Entry_________________________
                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->formName = 'Saving';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Cr';
                $gerenal_ledger->transactionAmount = $amounts;
                $gerenal_ledger->narration = $post->narration;
                $gerenal_ledger->groupCode = $cash_bank_group;
                $gerenal_ledger->ledgerCode = $cash_bank_ledger;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = 1;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $gerenal_ledger->save();

                $gerenal_ledger = new GeneralLedger;
                $gerenal_ledger->serialNo = $serialNo;
                $gerenal_ledger->accountId = $account_nos;
                $gerenal_ledger->accountNo = $post->membership;
                $gerenal_ledger->memberType = $post->memberType;
                $gerenal_ledger->formName = 'Saving';
                $gerenal_ledger->referenceNo = $saving_id;
                $gerenal_ledger->entryMode = 'Manual';
                $gerenal_ledger->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                $gerenal_ledger->transactionType = 'Dr';
                $gerenal_ledger->transactionAmount = $amounts;
                $gerenal_ledger->narration = $post->narration;
                $gerenal_ledger->groupCode = $account_opening->groupCode;
                $gerenal_ledger->ledgerCode = $account_opening->ledgerCode;
                $gerenal_ledger->branchId = session('branchId') ?: 1;
                $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                $gerenal_ledger->agentId = 1;
                $gerenal_ledger->updatedBy = $post->user()->id;
                $gerenal_ledger->save();

                DB::commit();

                //_________Checked Account in Opening Account Table
                $account_no = $post->accountNo;

                $type = $post->memberType;

                return $this->showDataTable($account_no,$type);


            } elseif ($transactionType == 'tord') {

                $amounts = $post->transactionAmount;
                $account_no = $check_saving_Id->accountId;

                $checkpaymentbalance = $this->checkpaymentbalance($account_no, $date, $amounts);

                if ($checkpaymentbalance) {
                    return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
                }

                $account = $post->rd_account_no;
                $rd_account = DB::table('re_curring_rds')
                    ->select('re_curring_rds.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
                    ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 're_curring_rds.secheme_id')
                    ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
                    ->where('accountId', $account)
                    ->first();

                if ($rd_account) {
                    //______check Scheme Id
                    if ($rd_account->secheme_id) {
                        $saving_scheme_id = $rd_account->secheme_id;
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Saving Scheme Id Not Found'], 400);
                    }

                    //___________Check Account Group Or Ledger Code
                    if ($rd_account->groupCode && $rd_account->ledgerCode) {
                        $account_group_code = $rd_account->groupCode;
                        $account_ledger_code = $rd_account->ledgerCode;
                    } else {
                        return response()->json(['status' => 'fail', 'messages' => 'Group Code And Ledger Code Not Found'], 400);
                    }
                } else {
                    return response()->json(['status' => 'fail', 'messages' => 'Rd Account Not Found'], 400);
                }

                $installmentdate = date('Y-m-d', strtotime($post->transactionDate));
                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate)));
                if (! $result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'fail', 'messages' => 'Please Check your session']);
                }

                $installments = RdInstallment::where(['rd_id' => $rd_account->id])->orderBy('id', 'desc')->first();
                $paid_amount = RdInstallment::where(['rd_id' => $rd_account->id])->sum('paid_amount');

                $deposit_amount = $post->transactionAmount;
                $monthly_installment_amount = $installments->amount;
                $no_of_installments = $installments->intallment_no;
                $total_amount = $monthly_installment_amount * $no_of_installments;
                $balance_amount = $total_amount - $paid_amount;

                if (($deposit_amount % $monthly_installment_amount) != 0) {
                    return response()->json(['status' => 'fail', 'messages' => 'Amount should be multiple of '.$monthly_installment_amount]);
                }

                if ($deposit_amount <= $balance_amount || $balance_amount == 0) {
                    $monthsToPay = $deposit_amount / $monthly_installment_amount;

                    if ($monthsToPay <= 0) {
                        return response()->json(['status' => 'fail', 'messages' => 'Not possible to pay off the debt with the given monthly payment.']);
                    } elseif ($monthsToPay > $no_of_installments) {
                        return response()->json(['status' => 'fail', 'messages' => 'Amount is not perfect for '.$no_of_installments.' Month']);
                    } else {
                        $paymentSuccess = false;
                        $penaltyApplied = false;
                        $rd_ids_details = ReCurringRd::where(['accountId' => $account])->first();

                        do {
                            $generalLedgers = 'savingrd'.time();
                        } while (GeneralLedger::where('serialNo', '=', $generalLedgers)->first() instanceof GeneralLedger);

                        DB::beginTransaction();
                        try {

                            //___________Entry in Member Saving Table
                            $saving_trfd_rd = new MemberSaving;
                            $saving_trfd_rd->secheme_id = $saving_scheme_id;
                            $saving_trfd_rd->serialNo = $generalLedgers;
                            $saving_trfd_rd->accountId = $account_opening->accountNo;
                            $saving_trfd_rd->accountNo = $post->membership;
                            $saving_trfd_rd->memberType = $post->memberType;
                            $saving_trfd_rd->groupCode = $saving_group;
                            $saving_trfd_rd->ledgerCode = $saving_ledger;
                            $saving_trfd_rd->savingNo = '';
                            $saving_trfd_rd->transactionDate = $transactionDate;
                            $saving_trfd_rd->transactionType = 'tord';
                            $saving_trfd_rd->depositAmount = 0;
                            $saving_trfd_rd->withdrawAmount = $amounts;
                            $saving_trfd_rd->paymentType = '';
                            $saving_trfd_rd->bank = '';
                            $saving_trfd_rd->chequeNo = 'trfdtoRd';
                            $saving_trfd_rd->narration = 'Trfd RD A/c -'.$post->rd_account_no ? 'Trfd RD A/c -'.$post->rd_account_no : $post->narration;
                            $saving_trfd_rd->branchId = session('branchId') ? session('branchId') : 1;
                            $saving_trfd_rd->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $saving_trfd_rd->agentId = $post->agentId;
                            $saving_trfd_rd->updatedBy = $post->user()->id;
                            $saving_trfd_rd->is_delete = 'No';
                            $saving_trfd_rd->save();

                            $saving_id = $saving_trfd_rd->id;

                            //_______________RD Receipt
                            $lastInsertedId = DB::table('rd_receiptdetails')->insertGetId([
                                'rc_account_no' => $rd_ids_details->id,
                                'rd_account_no' => $rd_ids_details->accountId,
                                'amount' => $amounts,
                                'serialNo' => $generalLedgers,
                                'payment_date' => $installmentdate,
                                'installment_date' => $installmentdate,
                                'groupCode' => $account_group_code,
                                'ledgerCode' => $account_ledger_code,
                                'memberType' => $rd_ids_details->memberType,
                                'panelty' => 0,
                                'mis_id' => '',
                                'narration' => 'Trfd Saving A/c -'.$account_opening->accountNo ? 'Trfd Saving A/c -'.$account_opening->accountNo : $post->narration,
                                'entry_mode' => 'manual',
                                'status' => 'trfdfromsaving',
                                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                                'agentid' => $post->agentId,
                                'updatedBy' => $post->user()->id,
                            ]);

                            //__________Rd Installment
                            for ($i = 1; $i <= $monthsToPay; $i++) {
                                $distributedPayment = min($monthly_installment_amount, $deposit_amount);
                                $deposit_amount -= $distributedPayment;
                                $query = RdInstallment::where(['rd_id' => $rd_account->id, 'payment_status' => 'pending'])->first();

                                if ($query && $query->payment_status == 'pending') {
                                    $query->payment_date = $installmentdate;
                                    if (! $penaltyApplied) {
                                        $query->panelty = empty($request->deposit_penalty) ? 0 : $post->deposit_penalty;
                                        $penaltyApplied = true;
                                    }
                                    $query->paid_amount = $distributedPayment;
                                    $query->panelty = 0;
                                    $query->recpt_id = $saving_id;
                                    $query->payment_status = 'paid';
                                    $query->serialNo = $generalLedgers;
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
                            $genral_ledger->formName = 'SavingTrfdRd';
                            $genral_ledger->referenceNo = $saving_id;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = 'Cr';
                            $genral_ledger->transactionAmount = $amounts;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            //____________Saving Account
                            $genral_ledger = new GeneralLedger;
                            $genral_ledger->serialNo = $generalLedgers;
                            $genral_ledger->accountId = $account_opening->accountNo;
                            $genral_ledger->accountNo = $account_opening->membershipno;
                            $genral_ledger->memberType = $account_opening->membertype;
                            $genral_ledger->formName = 'SavingTrfdRd';
                            $genral_ledger->groupCode = $saving_group;
                            $genral_ledger->ledgerCode = $saving_ledger;
                            $genral_ledger->referenceNo = $saving_id;
                            $genral_ledger->transactionDate = $installmentdate;
                            $genral_ledger->transactionType = 'Dr';
                            $genral_ledger->transactionAmount = $amounts;
                            $genral_ledger->branchId = session('branchId') ? session('branchId') : 1;
                            $genral_ledger->agentId = $post->agent_id;
                            $genral_ledger->sessionId = session('sessionId') ? session('sessionId') : 1;
                            $genral_ledger->updatedBy = $post->user()->id;
                            $genral_ledger->save();

                            $changestatus = RdInstallment::where(['rd_id' => $rd_ids_details->id])->orderBy('intallment_no', 'desc')->first();
                            if ($changestatus->payment_status == 'paid') {
                                $moodifystatus = ReCurringRd::where(['id' => $rd_ids_details->id])->update(['status' => 'Active']);
                            }

                            if ($paymentSuccess) {
                                DB::commit();

                                $total = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('paid_amount');
                                $totalpanality = RdInstallment::where(['rd_id' => $rd_ids_details->id])->sum('panelty');
                                $grand_total = $total + $totalpanality;

                                //_________Checked Account in Opening Account Table
                                $account_no = $account_nos;

                                $type = $post->memberType;

                                return $this->showDataTable($account_no,$type);
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();

                            return response()->json(['status' => 'fail', 'messages' => 'Some Technical issue occurred', 'error' => $e->getMessage()], 200);
                        }
                    }
                } else {
                    return response()->json(['status' => 'fail', 'messages' => 'Installment payment amount not satisfy.']);
                }
            }

            DB::commit();

            //_________Checked Account in Opening Account Table
            $account_no = $account_nos;
            $type = $post->memberType;

            return $this->showDataTable($account_no,$type);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getLine()]);
        }
    }

    public function showDataTable($account_no,$type)
    {
        $saving_account = DB::table('opening_accounts')
            ->select('opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->where('opening_accounts.accountNo', $account_no)
            ->where('opening_accounts.membertype',$type)
            ->where('member_accounts.memberType',$type)
            ->where('opening_accounts.accountname', '=', 'Saving')
            ->where('opening_accounts.status', '=', 'Active')
            ->first();


        //_________Get Old Balances From Member Opening Balance Table
        $opning_balance = DB::table('member_opening_balance')
            ->where('membership_no', '=', $saving_account->membershipno)
            ->where('account_no', $saving_account->accountNo)
            ->where('memberType',$type)
            ->where('accType', 'Saving')
            ->first();


        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if (! empty($session_master)) {

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('member_savings')
                ->where('accountId', $saving_account->accountNo)
                ->where('memberType',$type)
                ->whereDate('transactionDate', '<', $session_master->startDate)
                ->get();


            //_________Get Current Year Entries
            $saving_entries = DB::table('member_savings')
                ->select('member_savings.*', 'users.id as userid', 'users.name as username')
                ->leftJoin('users', 'users.id', 'member_savings.updatedBy')
                ->where('member_savings.accountId', $saving_account->accountNo)
                ->where('member_savings.accountNo', $saving_account->membershipno)
                ->where('member_savings.memberType',$type)
                ->whereDate('member_savings.transactionDate', '>=', $session_master->startDate)
                ->whereDate('member_savings.transactionDate', '<=', $session_master->endDate)
                ->orderBy('transactionDate', 'ASC')
                ->get();

            //______Get Opening Amount
            if ($opning_balance) {
                $previous_balance = collect($previous_balance);
                $opening_amount = $opning_balance->opening_amount + $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');
            } else {
                $opening_amount = 0;
            }

            if ($previous_balance || $saving_entries || $opening_amount) {
                return response()->json([
                    'status' => 'success',
                    'saving_account' => $saving_account,
                    'opening_amount' => $opening_amount,
                    'saving_entries' => $saving_entries,
                    'messages' => 'Transcation  Completed Successfully....!',
                ]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Check Your Session']);
        }
    }

    public function GetRdAccount(Request $post)
    {

        $rd_account = $post->selectedAccountNo;

        $opening_account = DB::table('opening_accounts')
            ->select(
                'opening_accounts.*',
                'member_accounts.accountNo as membership',
                'member_accounts.name as customer_name',
                'scheme_masters.id as schid',
                'scheme_masters.months as schmonth',
            )
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'opening_accounts.schemetype')
            ->where('opening_accounts.accountNo', $rd_account)
            ->where('opening_accounts.accountname', '=', 'RD')
            ->first();

        //_______Get Login Financial Year
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if (! empty($session_master)) {

            //__________Get Pervious Year Closing Balance From Member Saving Table
            $previous_balance = DB::table('rd_receiptdetails')
                ->where('rc_account_no', $opening_account->accountNo)
                ->whereDate('payment_date', '<', $session_master->startDate)
                ->get();

            //_________Get Current Year Entries
            $rd_accounts = DB::table('re_curring_rds')
                ->select(
                    're_curring_rds.id',
                    're_curring_rds.accountNo',
                    're_curring_rds.rd_account_no',
                    're_curring_rds.amount',
                    're_curring_rds.date',
                    're_curring_rds.interest',
                    're_curring_rds.maturity_date',
                    'scheme_masters.id as sch_id',
                    'scheme_masters.name',
                    'rd_receiptdetails.rd_account_no as rd_account',
                    're_curring_rds.month',
                    DB::raw('SUM(CASE WHEN rd_receiptdetails.amount IS NOT NULL THEN rd_receiptdetails.amount ELSE 0 END) as deposit'),
                    DB::raw('SUM(CASE WHEN rd_receiptdetails.panelty IS NOT NULL THEN rd_receiptdetails.panelty ELSE 0 END) as penalty')
                )
                ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rd_account_no', 're_curring_rds.rd_account_no')
                ->leftJoin('scheme_masters', 'scheme_masters.id', 're_curring_rds.secheme_id')
                ->where('re_curring_rds.rd_account_no', $opening_account->accountNo)
                ->where('re_curring_rds.accountNo', $opening_account->membershipno)
                ->orderBy('date', 'ASC')
                ->groupBy(
                    'rd_receiptdetails.rd_account_no',
                    're_curring_rds.accountNo',
                    're_curring_rds.rd_account_no',
                    're_curring_rds.amount',
                    're_curring_rds.month',
                    're_curring_rds.date',
                    're_curring_rds.interest',
                    're_curring_rds.maturity_date',
                    'scheme_masters.id',
                    'scheme_masters.name',
                    're_curring_rds.id',
                )
                ->first();

            if (! empty($previous_balance) || ! empty($rd_accounts) || ! is_null($opening_account)) {
                return response()->json([
                    'status' => 'success',
                    'previous_balance' => $previous_balance,
                    'rd_accounts' => $rd_accounts,
                    'opening_account' => $opening_account,
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'Please open an RD in Recurring Deposit.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Please Check Your Login Session',
            ]);
        }
    }

    public function GetSavingEitDetails(Request $post)
    {
        $id = $post->id;
        $saving_id = DB::table('member_savings')->where('id', $id)->first();



        if (is_null($saving_id)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {

            if ($saving_id->transactionType === 'tord') {
                $rd_accounts = DB::table('re_curring_rds')
                    ->select(
                        're_curring_rds.id','re_curring_rds.accountNo','re_curring_rds.rd_account_no',
                        're_curring_rds.amount','re_curring_rds.date','re_curring_rds.interest',
                        're_curring_rds.maturity_date','scheme_masters.id as sch_id','scheme_masters.name',
                        'rd_receiptdetails.rd_account_no as rd_account','re_curring_rds.month',
                        DB::raw('SUM(CASE WHEN rd_receiptdetails.amount IS NOT NULL THEN rd_receiptdetails.amount ELSE 0 END) as deposit'),
                        DB::raw('SUM(CASE WHEN rd_receiptdetails.panelty IS NOT NULL THEN rd_receiptdetails.panelty ELSE 0 END) as penalty')
                    )
                    ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rd_account_no', 're_curring_rds.rd_account_no')
                    ->leftJoin('scheme_masters', 'scheme_masters.id', 're_curring_rds.secheme_id')
                    ->where('rd_receiptdetails.serialNo', $saving_id->serialNo)
                    ->orderBy('date', 'ASC')
                    ->groupBy(
                        'rd_receiptdetails.rd_account_no','re_curring_rds.accountNo','re_curring_rds.rd_account_no',
                        're_curring_rds.amount','re_curring_rds.month','re_curring_rds.date',
                        're_curring_rds.interest','re_curring_rds.maturity_date','scheme_masters.id',
                        'scheme_masters.name', 're_curring_rds.id',
                    )
                    ->first();

                return response()->json(['status' => 'success', 'savings' => $saving_id, 'rd_accounts' => $rd_accounts]);
            }elseif ($saving_id->transactionType === 'DailySaving') {
                $dailyaccount = DB::table('daily_collectionsavings')
                    ->select('daily_collectionsavings.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name')
                    ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
                    ->where('daily_collectionsavings.serialNo', $saving_id->serialNo)
                    ->where('daily_collectionsavings.membershipno', $saving_id->accountNo)
                    ->where('daily_collectionsavings.membertype', $saving_id->memberType)
                    ->first();

                return response()->json(['status' => 'success', 'savings' => $saving_id, 'dailyaccount' => $dailyaccount]);

            }elseif ($saving_id->transactionType === 'toFd') {
            $fds = DB::table('member_fds_scheme')
                ->select(
                    'member_fds_scheme.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name',
                    'opening_accounts.id as idss','scheme_masters.id as schid','scheme_masters.name as schname'
                    )
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                ->leftJoin('opening_accounts', 'opening_accounts.id', '=', 'member_fds_scheme.accountId')
                ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'member_fds_scheme.secheme_id')
                ->where('member_fds_scheme.serialNo', $saving_id->serialNo)
                ->where('member_fds_scheme.membertype', $saving_id->memberType)
                ->first();
                return response()->json(['status' => 'success', 'savings' => $saving_id, 'fd' => $fds]);


            }else {
                return response()->json(['status' => 'success', 'savings' => $saving_id]);
            }
        }
    }

    public function getdailysavingaccount(Request $post)
    {

        $membership = $post->membership_no;
        $membertype = $post->memberTypes;
        $date = $post->transactionDates;
        $savingaccountno = $post->accountNo;

        $saving_account = DB::table('opening_accounts')
            ->select('opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
            ->where('opening_accounts.accountNo', $savingaccountno)
            ->where('opening_accounts.membershipno', $membership)
            ->where('opening_accounts.membertype', $membertype)
            ->where('opening_accounts.accountname', '=', 'Saving')
            ->where('opening_accounts.status', '=', 'Active')
            ->first();

        if ($saving_account) {
            $dailyaccount = DB::table('daily_collections')
                ->select('daily_collections.*', 'member_accounts.accountNo as membership', 'member_accounts.name as customer_name')
                ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collections.membershipno')
                ->where('daily_collections.membershipno', $saving_account->membershipno)
                ->where('daily_collections.membertype', $saving_account->membertype)
                ->where('daily_collections.status', '=', 'Active')
                ->get();
            if ($dailyaccount) {
                return response()->json(['status' => 'success', 'daily_accounts' => $dailyaccount]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Daily Account Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Saving Account Not Found']);
        }

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
                'data' => $output,
            ]);
        }

        $data = MemberAccount::where('memberType', $memberType)->where('accountNo', 'LIKE', $accountNo.'%')->get();

        if (count($data) > 0) {
            $output = '<ul class="list-group membersearch" style="display:block;z-indez:1">';
            foreach ($data as $row) {
                $output .= '<li class="list-group memberlist">'.$row->accountNo.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<li class="list-group-item memberlist">No Data Found</li>';
        }

        return response()->json([
            'status' => true,
            'data' => $output,
        ]);
    }

    public function fetchData(Request $request)
    {
        $memberType = $request->memberType;
        $accountNo = $request->accountNo;
        $openingBal = DB::table('opening_accounts')->where('membershipno', $request->accountNo)->sum('amount');
        $data = DB::table('opening_accounts')->where('membershipno', $request->accountNo)->where(['accountname' => 'Saving'])->get();

        $output = '';
        if (count($data) > 0) {
            $output = '<ul class="list-group membersearch" style="display:block;z-indez:1">';
            foreach ($data as $row) {
                $output .= '<li class="list-group memberlist">'.$row->accountNo.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<li class="list-group-item memberlist">No Data Found</li>';
        }

        if (! empty($accountNo)) {
            $member = MemberAccount::where(['memberType' => $memberType, 'accountNo' => $accountNo])->first();
            $SessionMaster = SessionMaster::find(Session::get('sessionId'));

            // ----------------saving balance----------------//
            $saving = MemberSaving::where(['memberType' => $memberType, 'accountNo' => $accountNo])
                ->where('is_delete', '!=', 'Yes')
                ->whereBetween('transactionDate', [$SessionMaster->startDate, $SessionMaster->endDate])
                ->orderBy('transactionDate')
                ->get();

            // ----------------opening balance----------------//
            $savingg = MemberSaving::where(['memberType' => $memberType, 'accountNo' => $accountNo])->where('is_delete', '!=', 'Yes')->where('transactionDate', '<', $SessionMaster->startDate)->orderBy('transactionDate')->get();

            // ----------------opening balance----------------//
            $openingBal = $openingBal + $savingg->sum('depositAmount') - $savingg->sum('withdrawAmount');
            // ----------------saving balance----------------//
            $savingBalance = $openingBal + $saving->sum('depositAmount') - $saving->sum('withdrawAmount');

            return response()->json([
                'status' => true,
                'member' => $member,
                'saving' => $saving,
                'balance' => $savingBalance,
                'openingBal' => $openingBal,
                'opening_accounts' => $output,
            ]);
        }
    }

    public function getsavingaccountsdata(Request $post)
    {
        $membershipno = $post->membershipno;
        $accountNoo = $post->accountNoo;
        $memberType = $post->memberType;
        $session_master = SessionMaster::find(Session::get('sessionId'));
        $start_date = $session_master->startDate;
        $end_date = $session_master->endDate;

        if (! empty($accountNoo)) {

            $member = MemberOpeningBalance::where(['memberType' => $memberType, 'account_no' => $accountNoo])
                ->where('membership_no', $membershipno)
                ->where('accType', 'Saving')
                ->first();

            $saving = DB::table('member_savings')
                ->where(['memberType' => $memberType, 'accountId' => $accountNoo])
                ->where('is_delete', '!=', 'Yes')
                ->where('transactionDate', '>=', $start_date)
                ->where('transactionDate', '<=', $end_date)
                ->orderBy('transactionDate')
                ->get();

            //__________Previous Year Closing Balance
            $previous_bal = MemberSaving::where(['memberType' => $memberType, 'accountId' => $accountNoo])
                ->where('is_delete', '!=', 'Yes')
                ->where('transactionDate', '<', $start_date)
                ->orderBy('transactionDate')
                ->get();

            //__________Opening Balance From Opening Account Table
            $openingBal = DB::table('member_opening_balance')
                ->where(['memberType' => $memberType, 'account_no' => $accountNoo])
                ->where('membership_no', $membershipno)
                ->where('accType', 'Saving')
                ->sum('opening_amount');

            $openingBal = $openingBal + $previous_bal->sum('depositAmount') - $previous_bal->sum('withdrawAmount');
            $savingBalance = $openingBal + $saving->sum('depositAmount') - $saving->sum('withdrawAmount');

            if ($saving || $openingBal || $savingBalance) {
                return response()->json([
                    'status' => 'success',
                    'member' => $member,
                    'saving' => $saving,
                    'savingbalance' => $savingBalance,
                    'openingBal' => $openingBal,
                ]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Account Has Not Data']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }


    public function savingtrfddailyaccount(Request $post){
        $rules = [
            "membershipnumberss" => "required",
            "trfdsavingstype" => "required",
            "currentdatedailysaving" => "required",
            "savingaccountnumber" => "required",
            "dailysavingaccountno" => "required",
            "trfddailyamount" => "required",
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()
            ]);
        }

        $date = date('Y-m-d', strtotime($post->currentdatedailysaving));

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->currentdatedailysaving)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
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
            ->where('opening_accounts.accountNo', $post->savingaccountnumber)
            ->where('opening_accounts.membershipno', $post->membershipnumberss)
            ->where('opening_accounts.membertype', $post->trfdsavingstype)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();




        if ($account_opening) {
            if ($account_opening->groupCode && $account_opening->ledgerCode) {
                $saving_group = $account_opening->groupCode;
                $saving_ledger = $account_opening->ledgerCode;
            } else {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'Saving Group && Ledger Code Not Found'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Saving Account Not Found'
            ]);
        }


        //_______Date Format Convert
        $transactionDate = $date;

        //________Get Account Open Date
        $member = DB::table('opening_accounts')
            ->where([
                'memberType' => $post->trfdsavingstype,
                'accountNo' => $post->savingaccountnumber,
                'membershipno' => $post->membershipnumberss
            ])
            ->first();


        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
                'messages' => 'Invalid account number'
            ]);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
                'messages' => 'Transaction date can not be less than account opening date'
            ]);
        }

        //__________serial Number
        do {
            $serialNo = "saving" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);


        //_________________Get Daily Saving Account Details
        $dailysavingaccount = $post->dailysavingaccountno;
        $getdailyaccount = DB::table('daily_collections')
            ->select('daily_collections.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
            ->where('daily_collections.account_no', $dailysavingaccount)
            ->where('daily_collections.membershipno', $post->membershipnumberss)
            ->where('daily_collections.membertype', $post->trfdsavingstype)
            ->first();


        dd($getdailyaccount);

        if (!empty($getdailyaccount)) {
            if ($getdailyaccount->scheme_groupCode && $getdailyaccount->scheme_ledgerCode) {
                $dailyGroupCode = $getdailyaccount->scheme_groupCode;
                $dailyLedgerCode = $getdailyaccount->scheme_ledgerCode;
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Saving Group/Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        $checkpaymentbalance =  $this->checkpaymentbalance($post->savingaccountnumber, $date, $post->trfddailyamount);

        if ($checkpaymentbalance) {
            return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
        }

        $installments = DailySavingInstallment::where(['daily_id' => $getdailyaccount->id])->orderBy('id', 'desc')->first();
        $paid_amount = DailySavingInstallment::where(['daily_id' => $getdailyaccount->id])->sum('paid_amount');

        $deposit_amount = $post->trfddailyamount;
        $daily_installment_amount = $installments->amount;
        $no_of_installments = $installments->intallment_no;
        $total_amount = $daily_installment_amount * $no_of_installments;
        $balance_amount = $total_amount - $paid_amount;


            $dailyToPay = $deposit_amount / $daily_installment_amount;


                $paymentSuccess = false;
                $penaltyApplied = false;
                $daily_account_details = DailyCollection::where(['account_no' => $dailysavingaccount])->first();



                DB::beginTransaction();
                try {
                    //___________Entry in Member Saving Table
                    $saving_withdraw = new MemberSaving();
                    $saving_withdraw->secheme_id = $account_opening->sch_id;
                    $saving_withdraw->serialNo = $serialNo;
                    $saving_withdraw->accountId = $post->savingaccountnumber;
                    $saving_withdraw->accountNo = $post->membershipnumberss;
                    $saving_withdraw->memberType = $post->trfdsavingstype;
                    $saving_withdraw->groupCode = $saving_group;
                    $saving_withdraw->ledgerCode = $saving_ledger;
                    $saving_withdraw->savingNo = '';
                    $saving_withdraw->transactionDate = $transactionDate;
                    $saving_withdraw->transactionType = 'DailySaving';
                    $saving_withdraw->depositAmount = 0;
                    $saving_withdraw->withdrawAmount = $post->trfddailyamount;
                    $saving_withdraw->paymentType = '';
                    $saving_withdraw->bank = '';
                    $saving_withdraw->chequeNo = 'trfdSavingtoDailyDeposit';
                    $saving_withdraw->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_withdraw->agentId = $post->agentId;
                    $saving_withdraw->updatedBy = $post->user()->id;
                    $saving_withdraw->is_delete = 'No';
                    $saving_withdraw->save();

                    //________Get Saving Account Id
                    $saving_id = $saving_withdraw->id;
                    $saving_accounts = $saving_withdraw->accountId;



                    DB::table('daily_collectionsavings')->insertGetId([
                        "serialNo" => $serialNo,
                        "dailyaccountid" => $getdailyaccount->id,
                        "membershipno"  =>  $getdailyaccount->membershipno,
                        "account_no"  =>  $getdailyaccount->account_no,
                        "receipt_date" => $date,
                        "deposit"  => $post->trfddailyamount,
                        'type' => 'trfdSavingtoDailyDeposit',
                        'payment_mode' => $saving_group,
                        'bank_name' => $saving_ledger,
                        'sch_id' => $getdailyaccount->sch_id,
                        'memberType' => $getdailyaccount->membertype,
                        "cheque_no" => 0,
                        'groupcode' => $dailyGroupCode,
                        'ledgercode' => $dailyLedgerCode,
                        "narration" => $post->trfddailyamountnarration ?? '',
                        "branchId" => session('branchId') ? session('branchId') : 1,
                        "sessionId" => session('sessionId') ? session('sessionId') : 1,
                        "updatedBy" => $post->user()->id,
                        "agentId" => $post->agentId
                    ]);


                    //__________Entry Installment Table
                    for ($i = 1; $i <= $dailyToPay; $i++) {
                        $distributedPayment = min($daily_installment_amount, $deposit_amount);
                        $deposit_amount -= $distributedPayment;
                        $query = DailySavingInstallment::where(['daily_id' => $getdailyaccount->id, 'payment_status' => 'pending'])->first();

                        if ($query && $query->payment_status === "pending") {
                            $query->payment_date = $date;
                            if (!$penaltyApplied) {
                                $query->panelty = 0;
                                $penaltyApplied = true;
                            }
                            $query->paid_amount = $distributedPayment;
                            $query->panelty = 0;
                            $query->recpt_id = $saving_id;
                            $query->payment_status = "paid";
                            $query->serialNo = $serialNo;
                            $query->save();
                            $paymentSuccess = true;
                        }
                    }



                    //________________________________________General Ledger Entry_________________________

                    //________Saving Entry
                    $gerenal_ledger = new GeneralLedger();
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $saving_accounts;
                    $gerenal_ledger->accountNo = $post->membershipnumberss;
                    $gerenal_ledger->memberType = $post->trfdsavingstype;
                    $gerenal_ledger->groupCode = $saving_group;
                    $gerenal_ledger->ledgerCode = $saving_ledger;
                    $gerenal_ledger->formName = 'trfdSavingtoDailyDeposit';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = $transactionDate;
                    $gerenal_ledger->transactionType = 'Dr';
                    $gerenal_ledger->transactionAmount = $post->trfddailyamount;
                    $gerenal_ledger->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId =  $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->is_delete = 'No';
                    $gerenal_ledger->save();


                    //________Daily Entry
                    $gerenal_ledger = new GeneralLedger();
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $post->dailysavingaccount;
                    $gerenal_ledger->accountNo = $post->membershipnumberss;
                    $gerenal_ledger->memberType = $post->trfdsavingstype;
                    $gerenal_ledger->groupCode = $dailyGroupCode;
                    $gerenal_ledger->ledgerCode = $dailyLedgerCode;
                    $gerenal_ledger->formName = 'trfdSavingtoDailyDeposit';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = $transactionDate;
                    $gerenal_ledger->transactionType = 'Cr';
                    $gerenal_ledger->transactionAmount = $post->trfddailyamount;
                    $gerenal_ledger->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId =  $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->is_delete = 'No';
                    $gerenal_ledger->save();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->savingaccountnumber;
                    $type = $post->trfdsavingstype;
                    return $this->showDataTable($account_no,$type);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getMessage()]);
                }


    }

    public function savingtrfddailyupdate(Request $post){
        $rules = [
            "membershipnumberss" => "required",
            "savingtrfdid" => "required",
            "trfddailysavingid" => "required",
            "trfdsavingstype" => "required",
            "currentdatedailysaving" => "required",
            "savingaccountnumber" => "required",
            "dailysavingaccountno" => "required",
            "trfddailyamount" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()
            ]);
        }

        $date = date('Y-m-d', strtotime($post->currentdatedailysaving));

        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json([
                'status' => 'fail',
                'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed'
            ]);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($post->currentdatedailysaving)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
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
            ->where('opening_accounts.accountNo', $post->savingaccountnumber)
            ->where('opening_accounts.membershipno', $post->membershipnumberss)
            ->where('opening_accounts.membertype', $post->trfdsavingstype)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();


        if ($account_opening) {
            if ($account_opening->groupCode && $account_opening->ledgerCode) {
                $saving_group = $account_opening->groupCode;
                $saving_ledger = $account_opening->ledgerCode;
            } else {
                return response()->json([
                    'status' => 'fail',
                    'messages' => 'Saving Group && Ledger Code Not Found'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Saving Account Not Found'
            ]);
        }


        //_______Date Format Convert
        $transactionDate = $date;

        //________Get Account Open Date
        $member = DB::table('opening_accounts')
            ->where([
                'memberType' => $post->trfdsavingstype,
                'accountNo' => $post->savingaccountnumber,
                'membershipno' => $post->membershipnumberss
            ])
            ->first();


        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
                'messages' => 'Invalid account number'
            ]);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
                'messages' => 'Transaction date can not be less than account opening date'
            ]);
        }

        //__________serial Number
        do {
            $serialNo = "saving" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);


        //_________________Get Daily Saving Account Details
        $saving_id = $post->savingtrfdid;


        $savingEntries = MemberSaving::where('id',$saving_id)
            ->where('memberType',$post->trfdsavingstype)
            ->where('accountId',$post->savingaccountnumber)
            ->where('accountNo',$post->membershipnumberss)
            ->first();


        $daily_id = $post->trfddailysavingid;

        $dailysavingaccount = $post->dailysavingaccountno;
        $getdailyaccount = DB::table('daily_collections')
            ->select('daily_collections.*', 'scheme_masters.id as sch_id', 'ledger_masters.reference_id')
            ->leftJoin('scheme_masters', 'scheme_masters.id', '=', 'daily_collections.schemeid')
            ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'scheme_masters.id')
            ->where('daily_collections.account_no', $dailysavingaccount)
            ->where('daily_collections.membershipno', $post->membershipnumberss)
            ->where('daily_collections.membertype', $post->trfdsavingstype)
            ->first();


        $getdailyaccountsssss = DB::table('daily_collectionsavings')
            ->where('serialNo',$savingEntries->serialNo)
            ->first();



        if (!empty($getdailyaccount)) {
            if ($getdailyaccount->scheme_groupCode && $getdailyaccount->scheme_ledgerCode) {
                $dailyGroupCode = $getdailyaccount->scheme_groupCode;
                $dailyLedgerCode = $getdailyaccount->scheme_ledgerCode;
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Saving Group/Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        $checkpaymentbalance =  $this->checkpaymentbalance($post->savingaccountnumber, $date, $post->trfddailyamount);

        if ($checkpaymentbalance) {
            return response()->json(['status' => 'fail', 'messages' => 'Check Your Balance Accoding Date']);
        }

        $installments = DailySavingInstallment::where(['recpt_id' => $savingEntries->id])->orderBy('id', 'desc')->first();
        $paid_amount = DailySavingInstallment::where(['recpt_id' => $savingEntries->id])->sum('paid_amount');

        $deposit_amount = $post->trfddailyamount;
        $daily_installment_amount = $installments->amount;
        $no_of_installments = $installments->intallment_no;
        $total_amount = $daily_installment_amount * $no_of_installments;
        $balance_amount = $total_amount - $paid_amount;


        // if ($deposit_amount <= $balance_amount || $balance_amount == 0) {
            $dailyToPay = $deposit_amount / $daily_installment_amount;


                $paymentSuccess = false;
                $penaltyApplied = false;
                $daily_account_details = DailyCollection::where('account_no',$dailysavingaccount)
                    ->where('membertype',$post->trfdsavingstype)
                    ->where('membershipno',$post->membershipnumberss)
                    ->first();



                DB::beginTransaction();
                try {

                    DailySavingInstallment::where('recpt_id',$savingEntries->id)
                        ->where('serialNo',$savingEntries->serialNo)
                        ->where('payment_status','paid')
                        ->update([
                            'serialNo' => $getdailyaccount->serialno,
                            'payment_date' => null,
                            'paid_amount' => null,
                            'recpt_id' => null,
                            'payment_status' => 'pending'
                        ]);


                    DB::table('daily_collectionsavings')
                        ->where('serialNo',$savingEntries->serialNo)
                        ->delete();


                    DB::table('general_ledgers')
                        ->where('referenceNo',$savingEntries->id)
                        ->where('serialNo',$savingEntries->serialNo)
                        ->delete();

                    MemberSaving::where('id',$saving_id)
                        ->where('memberType',$post->trfdsavingstype)
                        ->where('accountId',$post->savingaccountnumber)
                        ->where('accountNo',$post->membershipnumberss)
                        ->delete();

                    //___________Entry in Member Saving Table
                    $saving_withdraw = new MemberSaving();
                    $saving_withdraw->secheme_id = $account_opening->sch_id;
                    $saving_withdraw->serialNo = $serialNo;
                    $saving_withdraw->accountId = $post->savingaccountnumber;
                    $saving_withdraw->accountNo = $post->membershipnumberss;
                    $saving_withdraw->memberType = $post->trfdsavingstype;
                    $saving_withdraw->groupCode = $saving_group;
                    $saving_withdraw->ledgerCode = $saving_ledger;
                    $saving_withdraw->savingNo = '';
                    $saving_withdraw->transactionDate = $transactionDate;
                    $saving_withdraw->transactionType = 'DailySaving';
                    $saving_withdraw->depositAmount = 0;
                    $saving_withdraw->withdrawAmount = $post->trfddailyamount;
                    $saving_withdraw->paymentType = '';
                    $saving_withdraw->bank = '';
                    $saving_withdraw->chequeNo = 'trfdSavingtoDailyDeposit';
                    $saving_withdraw->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_withdraw->agentId = $post->agentId;
                    $saving_withdraw->updatedBy = $post->user()->id;
                    $saving_withdraw->is_delete = 'No';
                    $saving_withdraw->save();

                    //________Get Saving Account Id
                    $saving_id = $saving_withdraw->id;
                    $saving_accounts = $saving_withdraw->accountId;



                    DB::table('daily_collectionsavings')->insertGetId([
                        "serialNo" => $serialNo,
                        "dailyaccountid" => $getdailyaccount->id,
                        "membershipno"  =>  $getdailyaccount->membershipno,
                        "account_no"  =>  $getdailyaccount->account_no,
                        "receipt_date" => $date,
                        "deposit"  => $post->trfddailyamount,
                        'type' => 'trfdSavingtoDailyDeposit',
                        'payment_mode' => $saving_group,
                        'sch_id' => $getdailyaccount->sch_id,
                        'bank_name' => $saving_ledger,
                        'memberType' => $getdailyaccount->membertype,
                        "cheque_no" => 0,
                        'groupcode' => $dailyGroupCode,
                        'ledgercode' => $dailyLedgerCode,
                        "narration" => $post->trfddailyamountnarration ?? '',
                        "branchId" => session('branchId') ? session('branchId') : 1,
                        "sessionId" => session('sessionId') ? session('sessionId') : 1,
                        "updatedBy" => $post->user()->id,
                        "agentId" => $post->agentId
                    ]);


                    //__________Entry Installment Table
                    for ($i = 1; $i <= $dailyToPay; $i++) {
                        $distributedPayment = min($daily_installment_amount, $deposit_amount);
                        $deposit_amount -= $distributedPayment;
                        $query = DailySavingInstallment::where(['daily_id' => $getdailyaccount->id, 'payment_status' => 'pending'])->first();

                        if ($query && $query->payment_status === "pending") {
                            $query->payment_date = $date;
                            if (!$penaltyApplied) {
                                $query->panelty = 0;
                                $penaltyApplied = true;
                            }
                            $query->paid_amount = $distributedPayment;
                            $query->panelty = 0;
                            $query->recpt_id = $saving_id;
                            $query->payment_status = "paid";
                            $query->serialNo = $serialNo;
                            $query->save();
                            $paymentSuccess = true;
                        }
                    }

                    //________________________________________General Ledger Entry_________________________

                    //________Saving Entry
                    $gerenal_ledger = new GeneralLedger();
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $saving_accounts;
                    $gerenal_ledger->accountNo = $post->membershipnumberss;
                    $gerenal_ledger->memberType = $post->trfdsavingstype;
                    $gerenal_ledger->groupCode = $saving_group;
                    $gerenal_ledger->ledgerCode = $saving_ledger;
                    $gerenal_ledger->formName = 'trfdSavingtoDailyDeposit';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = $transactionDate;
                    $gerenal_ledger->transactionType = 'Dr';
                    $gerenal_ledger->transactionAmount = $post->trfddailyamount;
                    $gerenal_ledger->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId =  $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->is_delete = 'No';
                    $gerenal_ledger->save();


                    //________Daily Entry
                    $gerenal_ledger = new GeneralLedger();
                    $gerenal_ledger->serialNo = $serialNo;
                    $gerenal_ledger->accountId = $post->dailysavingaccount;
                    $gerenal_ledger->accountNo = $post->membershipnumberss;
                    $gerenal_ledger->memberType = $post->trfdsavingstype;
                    $gerenal_ledger->groupCode = $dailyGroupCode;
                    $gerenal_ledger->ledgerCode = $dailyLedgerCode;
                    $gerenal_ledger->formName = 'trfdSavingtoDailyDeposit';
                    $gerenal_ledger->referenceNo = $saving_id;
                    $gerenal_ledger->entryMode = 'Manual';
                    $gerenal_ledger->transactionDate = $transactionDate;
                    $gerenal_ledger->transactionType = 'Cr';
                    $gerenal_ledger->transactionAmount = $post->trfddailyamount;
                    $gerenal_ledger->narration = 'Saving A/c- ' . $post->savingaccountnumber . ' - to Trfd Daily' . $dailysavingaccount  ?  'Saving A/c- ' . $post->savingaccountnumber . 'to Trfd Daily -' . $dailysavingaccount : $post->trfddailyamountnarration;
                    $gerenal_ledger->branchId = session('branchId') ?: 1;
                    $gerenal_ledger->sessionId = session('sessionId') ?: 1;
                    $gerenal_ledger->agentId =  $post->agentId;
                    $gerenal_ledger->updatedBy = $post->user()->id;
                    $gerenal_ledger->is_delete = 'No';
                    $gerenal_ledger->save();

                    DB::commit();

                    //_________Checked Account in Opening Account Table
                    $account_no = $post->savingaccountnumber;
                    $type = $post->trfdsavingstype;
                    return $this->showDataTable($account_no,$type);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'Fail', 'messages' => 'Some Technical Issue', 'error' => $e->getMessage()]);
                }

        // }
    }

    public function getfddetails(Request $post){
        $fdid = $post->fdid;

        $fdAccounts =opening_accounts::where('opening_accounts.id',$fdid)
        ->leftJoin('scheme_masters', 'opening_accounts.schemetype', '=', 'scheme_masters.id')
        ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
        ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.schemetype')
        ->select(
            'opening_accounts.*',
            'member_accounts.name',
            'scheme_masters.id as scheme_id',
            'scheme_masters.name as scheme_name',
            'scheme_masters.scheme_code',
            'scheme_masters.durationType',
            'scheme_masters.days',
            'scheme_masters.months',
            'scheme_masters.years',
            'scheme_masters.interest',
            'scheme_masters.penaltyInterest',
            'scheme_masters.secheme_type',
            'scheme_masters.status as scheme_status',
            'scheme_masters.lockin_days',
            'scheme_masters.renewInterestType',
            'ledger_masters.groupCode',
            'ledger_masters.ledgerCode',
        )->first();



        if(!empty($fdAccounts)){
            return response()->json([
                'status' => 'success',
                'fdaccount' => $fdAccounts
            ]);
        }else{
            return response()->json([
                'stauts' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        }
    }

    public function editpaidinterest(Request $post){
        $id = $post->id;

        $exits_id = DB::table('member_savings')->where('id',$id)->first();

        if(!empty($exits_id)){
            return response()->json(['status' => 'success','details' => $exits_id]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }


    public function paidinterestchange(Request $post)
    {
        $rules = [
            "interestid" => "required",
            "interest_date" => "required",
            "ineterest_account" => "required",
            "interest_paid_amount" => "required",
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()]);
        }

        $id = $post->interestid;
        $exits_id = DB::table('member_savings')->where('id', $id)->first();

        DB::beginTransaction();
        try {
            DB::table('interest_calculations')
                ->where('serialNo', $exits_id->serialNo)
                ->update(['withdrawAmount' => $post->interest_paid_amount]);

            DB::table('general_ledgers')
                ->where('serialNo', $exits_id->serialNo)
                ->update(['transactionAmount' => $post->interest_paid_amount]);

            DB::table('member_savings')
                ->where('id', $id)
                ->update(['depositAmount' => $post->interest_paid_amount]);

            DB::commit();


            $account_no = $post->ineterest_account;
            $type = $exits_id->memberType;
            return $this->showDataTable($account_no,$type);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Something Went Wrong',
                'error' => $e->getMessage(),
                'lines' => $e->getLine()
            ]);
        }
    }

    public function getcclaccountdetails(Request $post){
        $rules = [
            "membern" => "required",
            "memberTypess" => "required",
            "transactionDatess" => "required",
            "accountNo" => 'required'
        ];

        //_________Check Validations
        $validator = Validator::make($post->all(),$rules);
        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => $validator->errors()]);
        }

        $receiptDate = date('Y-m-d',strtotime($post->transactionDatess));


        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($receiptDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }


        $savings = DB::table('opening_accounts')
            ->where('memberType',$post->memberTypess)
            ->where('accountNo',$post->accountNo)
            ->where('membershipno',$post->membern)
            ->where('accountname','=','Saving')
            ->first();

       //_________Check if member account exist or not
        if (empty($savings)) {
            return response()->json(['status' => 'Fail','messages' => 'Invalid account number']);
        }

        //__________Check account opening date not less then
        if ($receiptDate < $savings->transactionDate) {
            return response()->json(['status' => 'fail','messages' => 'Transaction date can not be less than account opening date']);
        }

        //_____Check CCl Advancement Table
        $cclDetails = DB::table('member_ccl')
            ->where('membership', $post->membern)
            ->where('memberType', $post->memberTypess)
            ->whereDate('ccl_Date', '<=', $receiptDate)
            ->where('status', '=', 'Disbursed')
            ->first();


        if(!empty($cclDetails)){
            //________Get Interest Amount / Pending Days / Pending Amount
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

            return response()->json([
                'status' => 'success',
                'savings' => $savings,
                'cclDetails' => $cclDetails,
                'interestRate' => $interestRate,
                'totalWithdraw' => $totalWithdraw,
                'totalDeposit' => $totalDeposit,
                'days' => $days,
            ]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }

    public function getcheckinterestdatewiseccl(Request $post){
        $id = $post->id;
        $currentDate = date('Y-m-d', strtotime($post->receipt_date));
        $cclDetails = DB::table('member_ccl')->where('id', $id)->first();

        if($currentDate < $cclDetails->ccl_Date){
            return response()->json(['status' => 'Fail','messages' => 'Current Date is ' .$currentDate. ' smaller Then '.$cclDetails->ccl_Date.'CCL Opening Date ']);
        }


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
                'interestRate' => $interestRate,
                'totalWithdraw' => $withdraw_amount,
                'totalDeposit' => $deposit_amount,
                'days' => $days,
                'cclDetails' => $cclDetails
            ]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }
    }

    public function savingtrfdtocclrecovery(Request $post){
        $rules = [
            "cclid" => "required",
            "sbid" => "required",
            "savingaccts" => "required",
            "sbmemberno" => "required",
            "sbmembertype" => "required",
            "ccltrfdamount" => "required",
            "ccltrfdDate" => "required",
            "ccl_account" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','error' => $validator->errors()]);
        }

        $transactionDate = date('Y-m-d',strtotime($post->ccltrfdDate));

        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $saving_account = $post->savingaccts;
        $savingmemberType = $post->sbmembertype;
        $membershipno = $post->sbmemberno;


        //__________________________Get Saving Accounts Details
        $savings = DB::table('opening_accounts')
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
            ->where('opening_accounts.accountNo', $saving_account)
            ->where('opening_accounts.membershipno', $membershipno)
            ->where('opening_accounts.membertype', $savingmemberType)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();

        if ($savings) {
            if ($savings->groupCode && $savings->ledgerCode) {
                $saving_group = $savings->groupCode;
                $saving_ledger = $savings->ledgerCode;
            } else {
                return response()->json(['status' => 'fail','messages' => 'Saving Group && Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'fail','messages' => 'Saving Account Not Found']);
        }


        //________Get Account Open Date
        $member = DB::table('opening_accounts')->where(['memberType' => $savingmemberType,'accountNo' => $saving_account,'membershipno' => $membershipno])->first();


        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json(['status' => 'fail','messages' => 'Invalid account number']);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json(['status' => 'fail','messages' => 'Transaction date can not be less than account opening date']);
        }

        $interest_amount = $post->ccl_interest_amount ?? 0;
        $cclamount = $post->ccl_trfd_amount ?? 0;
        $netpaid = $interest_amount + $cclamount;


        //________________Get CCL Account Details
        $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status','=','Disbursed')->first();
        $recoveries = DB::table('ccl_payments')->where('ccl_Id',$cclDetails->id)->where('transcationDate','<=',$transactionDate)->get();
        $used_amount = $recoveries->sum('transfer_amount');
        $paid_amount = $recoveries->sum('recovey_amount');
        $balance_ccl = $used_amount - $paid_amount;


        //__________Not Exceed Amount
        if($balance_ccl < $cclamount){
            return response()->json(['status' => 'Fail','messages' => 'Trfd Amount Exceed Then Balance CCL amount']);
        }

        $memberType = $cclDetails->memberType;

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

        do {
            $serialNo = "saving" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

        DB::beginTransaction();

        try{

            //___________Member saving Table Entry
            $id = DB::table('member_savings')->insertGetId([
                'secheme_id' => $savings->accounttype,
                'serialNo' => $serialNo,
                'accountId' => $savings->accountNo,
                'accountNo' => $savings->membershipno,
                'memberType' => $savings->membertype,
                'groupCode' => $saving_group,
                'ledgerCode' => $saving_ledger,
                'savingNo' =>  $savings->accountNo,
                'transactionDate' => $transactionDate,
                'transactionType' => 'toCCL',
                'depositAmount' => 0,
                'withdrawAmount' => $netpaid,
                'paymentType' => '',
                'bank' => '',
                'chequeNo' => 'SavingTrfd',
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ? session('branchId') : 1,
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                // 'agentId' => ,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            DB::table('ccl_payments')->insertGetId([
                'transcationDate' => $transactionDate,
                'serialNo' => $serialNo,
                'ccl_Id' => $cclDetails->id,
                'memberType' => $memberType,
                'membershipno' => $cclDetails->membership,
                'ccl_account' => $cclDetails->cclNo,
                'saving_account' => $savings->accountNo,
                'type' => 'Deposit',
                'transfer_amount' => 0,
                'recovey_amount' =>  $cclamount,
                'interest_amount' => $interest_amount,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'narration' => 'Saving TRFD Date ' . $transactionDate . 'From A/c- SB' .  $savings->accountNo,
                'chequeNo' => 'SavingTrfd',
                'branchId' => session('branchId') ? session('branchId') : 1,
                'agentId' => '',
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'updatedBy' => $post->user()->id,
            ]);


            //__________________General Ledger Entries

            if (!empty($interest_amount) && $interest_amount > 0) {
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $interestGroup,
                    'ledgerCode' => $interestLedger,
                    'formName' => 'CCL Interest Received',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $interest_amount,
                    'narration' =>  'CCL Intt. Received From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);
            }

            // __________Member Saving Entries in General Entries
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $savings->accountNo,
                'accountNo' => $savings->membershipno,
                'memberType' => $savings->membertype,
                'groupCode' => $saving_group,
                'ledgerCode' => $saving_ledger,
                'formName' => 'Saving Trfd',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $netpaid,
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => null,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            //________CCL Enteries In General Enrtries
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $cclDetails->cclNo,
                'accountNo' => $cclDetails->membership,
                'memberType' => $cclDetails->memberType,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'formName' => 'Saving Trfd',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $cclamount,
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => null,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            // if($balance_ccl === $netpaid){
            //     $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status','=','Disbursed')->first();

            // }

            DB::commit();
            $account_no = $savings->accountNo;
            $type = $savings->membertype;
            return $this->showDataTable($account_no,$type);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'Fail','messages' => 'Some Technical Issue','error' => $e->getMessage(),'lines' => $e->getline()]);
        }
    }

    public function editsavingtrdfccl(Request $post){
        $id = $post->id;
        $transactionDate = date('Y-m-d',strtotime($post->transactionDate));


        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }


        //_________Get Data Saving A/c
        $savings = DB::table('member_savings')->where('id',$id)->first();

        $payments = DB::table('ccl_payments')
            ->where('serialNo', $savings->serialNo)
            ->first();


        $cclDetails = DB::table('member_ccl')
            ->where('membership', $payments->membershipno)
            ->where('memberType', $payments->memberType)
            ->where('id', $payments->ccl_Id)
            ->first();

        $allData =  DB::table('ccl_payments')
            ->where('ccl_Id', $cclDetails->id)
            ->whereDate('transcationDate','<=',$transactionDate)
            ->get();



        if(!empty($savings) || !empty($payments) || !empty($cclDetails)){
            return response()->json([
                'status' => 'success',
                'savings' => $savings,
                'payments' => $payments,
                'cclDetails' => $cclDetails,
                'allData' => $allData
            ]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }

    public function savingtrfdtocclrecoveryupdate(Request $post){
        $rules = [
            "cclid" => "required",
            "savingaccts" => "required",
            "sbmemberno" => "required",
            "sbmembertype" => "required",
            "ccltrfdsavingupdateid" => "required",
            "ccltrfdDate" => "required",
            "ccl_account" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','error' => $validator->errors()]);
        }

        $transactionDate = date('Y-m-d',strtotime($post->ccltrfdDate));

        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $saving_account = $post->savingaccts;
        $savingmemberType = $post->sbmembertype;
        $membershipno = $post->sbmemberno;


        //__________________________Get Saving Accounts Details
        $savings = DB::table('opening_accounts')
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
            ->where('opening_accounts.accountNo', $saving_account)
            ->where('opening_accounts.membershipno', $membershipno)
            ->where('opening_accounts.membertype', $savingmemberType)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();

        if ($savings) {
            if ($savings->groupCode && $savings->ledgerCode) {
                $saving_group = $savings->groupCode;
                $saving_ledger = $savings->ledgerCode;
            } else {
                return response()->json(['status' => 'fail','messages' => 'Saving Group && Ledger Code Not Found']);
            }
        } else {
            return response()->json(['status' => 'fail','messages' => 'Saving Account Not Found']);
        }


        //________Get Account Open Date
        $member = DB::table('opening_accounts')->where(['memberType' => $savingmemberType,'accountNo' => $saving_account,'membershipno' => $membershipno])->first();


        //_________Check if member account exist or not
        if (empty($member)) {
            return response()->json(['status' => 'fail','messages' => 'Invalid account number']);
        }

        //__________Check account opening date not less then
        if ($transactionDate < $member->transactionDate) {
            return response()->json(['status' => 'fail','messages' => 'Transaction date can not be less than account opening date']);
        }

        $interest_amount = $post->ccl_interest_amount ?? 0;
        $cclamount = $post->ccl_trfd_amount ?? 0;
        $netpaid = $interest_amount + $cclamount;


        //________________Get CCL Account Details
        $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status','=','Disbursed')->first();
        $recoveries = DB::table('ccl_payments')->where('ccl_Id',$cclDetails->id)->where('transcationDate','<=',$transactionDate)->get();
        $used_amount = $recoveries->sum('transfer_amount');
        $paid_amount = $recoveries->sum('recovey_amount');
        $balance_ccl = $used_amount - $paid_amount;


        //__________Not Exceed Amount
        if($balance_ccl < $cclamount){
            return response()->json(['status' => 'Fail','messages' => 'Trfd Amount Exceed Then Balance CCL amount']);
        }

        $memberType = $cclDetails->memberType;

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

        do {
            $serialNo = "saving" . time();
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);


        DB::beginTransaction();

        try{

            $trfdId = DB::table('member_savings')->where('id',$post->ccltrfdsavingupdateid)->first();

            //____________Delete CCL A/c
            DB::table('ccl_payments')->where('serialNo',$trfdId->serialNo)->delete();

            //____________Delete General Ledger
            DB::table('general_ledgers')->where('serialNo',$trfdId->serialNo)->delete();

            //____________Delete Member Saving A/c
            DB::table('member_savings')->where('id',$post->ccltrfdsavingupdateid)->delete();

            //___________Member saving Table Entry
            $id = DB::table('member_savings')->insertGetId([
                'secheme_id' => $savings->accounttype,
                'serialNo' => $serialNo,
                'accountId' => $savings->accountNo,
                'accountNo' => $savings->membershipno,
                'memberType' => $savings->membertype,
                'groupCode' => $saving_group,
                'ledgerCode' => $saving_ledger,
                'savingNo' =>  $savings->accountNo,
                'transactionDate' => $transactionDate,
                'transactionType' => 'toCCL',
                'depositAmount' => 0,
                'withdrawAmount' => $netpaid,
                'paymentType' => '',
                'bank' => '',
                'chequeNo' => 'SavingTrfd',
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ? session('branchId') : 1,
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                // 'agentId' => ,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            DB::table('ccl_payments')->insertGetId([
                'transcationDate' => $transactionDate,
                'serialNo' => $serialNo,
                'ccl_Id' => $cclDetails->id,
                'memberType' => $memberType,
                'membershipno' => $cclDetails->membership,
                'ccl_account' => $cclDetails->cclNo,
                'saving_account' => $savings->accountNo,
                'type' => 'Deposit',
                'transfer_amount' => 0,
                'recovey_amount' =>  $cclamount,
                'interest_amount' => $interest_amount,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'narration' => 'Saving TRFD Date ' . $transactionDate . 'From A/c- SB' .  $savings->accountNo,
                'chequeNo' => 'SavingTrfd',
                'branchId' => session('branchId') ? session('branchId') : 1,
                'agentId' => '',
                'sessionId' => session('sessionId') ? session('sessionId') : 1,
                'updatedBy' => $post->user()->id,
            ]);


            //__________________General Ledger Entries

            if (!empty($interest_amount) && $interest_amount > 0) {
                DB::table('general_ledgers')->insert([
                    'serialNo' => $serialNo,
                    'accountId' => $cclDetails->cclNo,
                    'accountNo' => $cclDetails->membership,
                    'memberType' => $cclDetails->memberType,
                    'groupCode' => $interestGroup,
                    'ledgerCode' => $interestLedger,
                    'formName' => 'CCL Interest Received',
                    'referenceNo' => $id,
                    'entryMode' => 'manual',
                    'transactionDate' => $transactionDate,
                    'transactionType' => 'Cr',
                    'transactionAmount' => $interest_amount,
                    'narration' =>  'CCL Intt. Received From CCL A/c-' . $cclDetails->cclNo,
                    'branchId' => session('branchId') ?? 1,
                    // 'agentId' => $post->agents,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'is_delete' => 'No',
                ]);
            }

            // __________Member Saving Entries in General Entries
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $savings->accountNo,
                'accountNo' => $savings->membershipno,
                'memberType' => $savings->membertype,
                'groupCode' => $saving_group,
                'ledgerCode' => $saving_ledger,
                'formName' => 'Saving Trfd',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Dr',
                'transactionAmount' => $netpaid,
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => null,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);


            //________CCL Enteries In General Enrtries
            DB::table('general_ledgers')->insert([
                'serialNo' => $serialNo,
                'accountId' => $cclDetails->cclNo,
                'accountNo' => $cclDetails->membership,
                'memberType' => $cclDetails->memberType,
                'groupCode' => $groupCode,
                'ledgerCode' => $ledgerCode,
                'formName' => 'Saving Trfd',
                'referenceNo' => $id,
                'entryMode' => 'manual',
                'transactionDate' => $transactionDate,
                'transactionType' => 'Cr',
                'transactionAmount' => $cclamount,
                'narration' => 'Saving TRFD Date ' . $transactionDate . ' To CCL A/c-' . $cclDetails->cclNo,
                'branchId' => session('branchId') ?? 1,
                'agentId' => null,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'is_delete' => 'No',
            ]);

            // if($balance_ccl === $netpaid){
            //     $cclDetails = DB::table('member_ccl')->where('id', $post->cclid)->where('status','=','Disbursed')->first();

            // }

            DB::commit();
            $account_no = $savings->accountNo;
            $type = $savings->membertype;
            return $this->showDataTable($account_no,$type);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'Fail','messages' => 'Some Technical Issue','error' => $e->getMessage(),'lines' => $e->getline()]);
        }

    }
}
