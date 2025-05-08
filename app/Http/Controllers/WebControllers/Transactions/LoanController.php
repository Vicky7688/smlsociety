<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\MemberAccount;
use App\Models\GeneralLedger;
use App\Models\AgentMaster;
use App\Models\LoanInstallment;
use App\Models\LedgerMaster;
use App\Models\PurposeMaster;
use App\Models\LoanMaster;
use App\Models\MemberLoan;
use App\Models\LoanRecovery;
use App\Models\MemberFd;
use App\Models\MemberFdScheme;
use App\Models\ReCurringRd;
use App\Models\dailyrcovery;
use App\Models\MemberSaving;
use DateTime;
// use DateInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class LoanController extends Controller
{

    public function index()
    {
        return view('transaction.loan.index');
    }

    public function loan($type)
    {
        $data['title'] = $type;
        if ($type == "advancement") {
            $data['agents'] = AgentMaster::get();
            $data['loantypes'] = LoanMaster::where('status', "active")->get();
            $data['purposes'] = PurposeMaster::get();
            $data['grup'] = DB::table('group_masters')->where('groupCode', '=', 'LOA02')->get();
            $data['grupo'] = DB::table('group_masters')->where('groupCode', '=', 'LOA03')->get();
            $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
        } elseif ($type == "recovery") {
            $data['agents'] = AgentMaster::get();
            $data['loantypes'] = LoanMaster::where('status', "active")->get();
            $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
        } else {
            $data['agents'] = AgentMaster::get();
            $data['loantypes'] = LoanMaster::where('status', "active")->get();
            $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
        }
        return view('transaction.loan.' . $type)->with($data);
    }



    public function getsavingaccount(Request $post)
    {
        $rules = [
            "membertype" => "required",
            "membership" => "required"
        ];
        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $membership_number = $post->membership;
        $memberType = $post->membertype;


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
            ->where('opening_accounts.membershipno', $membership_number)
            ->where('opening_accounts.membertype', $memberType)
            ->where('opening_accounts.accountname', 'Saving')
            ->where('opening_accounts.status', 'Active')
            ->first();
        if (!empty($saving_account)) {
            return response()->json([
                'status' => 'success',
                'saving_account' => $saving_account
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Saving Account Not Found'
            ]);
        }
    }




    public function getCheckedSchemes(Request $post)
    {
        if ($post->type == 'FD') {
            if ($post->schemes) {
                $data = DB::table('member_fds_scheme')->whereIn('id', $post->schemes)->sum('principalAmount');
                $interestRate = DB::table('member_fds_scheme')->whereIn('id', $post->schemes)->max('interestRate');
                return response()->json(['status' => "success", "upto" => $data, 'interestRate' => $interestRate]);
            } else {
                return response()->json(['status' => "fail", "upto" => 0]);
            }
        } elseif ($post->type == 'RD') {
            if ($post->schemes) {
                $data = DB::table('rd_receiptdetails')->whereIn('rc_account_no', $post->schemes)->sum('amount');
                return response()->json(['status' => "success", "upto" => $data]);
            } else {
                return response()->json(['status' => "fail", "upto" => 0]);
            }
        } elseif ($post->type == 'DailyDeposit') {
            if ($post->schemes) {
                $data = DB::table('daily_collectionsavings')->whereIn('dailyaccountid', $post->schemes)->sum('deposit');
                return response()->json(['status' => "success", "upto" => $data]);
            } else {
                return response()->json(['status' => "fail", "upto" => 0]);
            }
        }
    }



    public function getfdschemesloan(Request $post)
    {
        $loanType = $post->loanId;

        $loan = DB::table('loan_masters')->where('id', $post->loanId)->first();

        if (!empty($loan)) {
            return response()->json(['status' => 'success', 'loanType' => $loan]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        // switch ($loanType) {
        //     case "FD":
        //         $loan = DB::table('loan_masters')
        //             ->where('id', $post->loanId)
        //             ->where('loantypess', 'FD')
        //             ->first();

        //         $data = DB::table('member_fds_scheme')
        //             ->join('scheme_masters', 'member_fds_scheme.secheme_id', '=', 'scheme_masters.id')
        //             ->select('member_fds_scheme.*', 'scheme_masters.name as schemname')
        //             ->where('member_fds_scheme.membershipno', '=', $post->accountNumber)
        //             ->where('member_fds_scheme.memberType', '=', $post->member)
        //             ->whereIn('member_fds_scheme.status', ['Active', 'Pluge'])
        //             ->get();
        //         if (!empty($loan) && !empty($data)) {
        //             return response()->json(['status' => 'success', 'loanType' => $loan, 'data' => $data]);
        //         } else {
        //             return response()->json(['status' => 'Fail', 'messages' => 'Fd A/c Not Found']);
        //         }
        //         break;
        //     case "RD":
        //         $loan = DB::table('loan_masters')
        //             ->where('id', $post->loanId)
        //             ->where('loantypess', 'RD')
        //             ->first();

        //         $data = DB::table('re_curring_rds')
        //             ->join('scheme_masters', 're_curring_rds.secheme_id', '=', 'scheme_masters.id')
        //             ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
        //             ->select(
        //                 're_curring_rds.id',
        //                 're_curring_rds.rd_account_no',
        //                 'scheme_masters.name as schemname',
        //                 're_curring_rds.status',
        //                 DB::raw('SUM(rd_receiptdetails.amount) as fetchamount')
        //             )
        //             ->where('re_curring_rds.accountNo', '=', $post->accountNumber)
        //             ->where('re_curring_rds.memberType', '=', $post->member)
        //             ->whereIn('re_curring_rds.status', ['Active', 'Pluge'])
        //             ->groupBy('re_curring_rds.id', 're_curring_rds.rd_account_no', 'scheme_masters.name', 're_curring_rds.status',)
        //             ->get();
        //         if (!empty($loan) && !empty($data)) {
        //             return response()->json(['status' => 'success', 'loanType' => $loan, 'data' => $data]);
        //         } else {
        //             return response()->json(['status' => 'Fail', 'messages' => 'Rd A/c Not Found']);
        //         }

        //         break;
        //     case "DailyDeposit" :

        //             $loan = DB::table('loan_masters')
        //                 ->where('id', $post->loanId)
        //                 ->where('loantypess', 'DailyDeposit')
        //                 ->first();

        //             $data = DB::table('daily_collections')
        //                 ->join('scheme_masters', 'daily_collections.schemeid', '=', 'scheme_masters.id')
        //                 ->leftJoin('daily_collectionsavings', 'daily_collectionsavings.dailyaccountid', '=', 'daily_collections.id')
        //                 ->select(
        //                     'daily_collections.id',
        //                     'daily_collections.account_no',
        //                     'scheme_masters.name as schemname',
        //                     'daily_collections.status',
        //                     DB::raw('SUM(daily_collectionsavings.deposit) as deposit_amount')
        //                 )
        //                 ->where('daily_collections.membershipno', '=', $post->accountNumber)
        //                 ->where('daily_collections.memberType', '=', $post->member)
        //                 ->whereIn('daily_collections.status', ['Active', 'Pluge'])
        //                 ->groupBy('daily_collections.id', 'daily_collections.account_no', 'scheme_masters.name', 'daily_collections.status')
        //                 ->get();

        //                 if (!empty($loan) && !empty($data)) {
        //                     return response()->json(['status' => 'success', 'loanType' => $loan, 'data' => $data]);
        //                 } else {
        //                     return response()->json(['status' => 'Fail', 'messages' => 'Rd A/c Not Found']);
        //                 }

        //         break;
        //     default:
        //     // dd($post->loanId);
        //         $loan = DB::table('loan_masters')
        //             ->where('id', $post->loanId)
        //             // ->where('loantypess', 'MTLoan')
        //             ->first();
        //             // dd($loan);
        //         if (!empty($loan)) {
        //             return response()->json(['status' => 'success', 'loanType' => $loan]);
        //         } else {
        //             return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        //         }
        // }
    }



    // public function getrdschemesloan(Request $post)
        // {

        //     $data = DB::table('re_curring_rds')
        //     ->join('scheme_masters', 're_curring_rds.secheme_id', '=', 'scheme_masters.id')
        //     ->leftJoin('rd_receiptdetails', 'rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
        //     ->select(
        //         're_curring_rds.id',
        //         're_curring_rds.rd_account_no',
        //         'scheme_masters.name as schemname',
        //         DB::raw('SUM(rd_receiptdetails.amount) as fetchamount')
        //     )
        //     ->where('re_curring_rds.accountNo', '=', $post->accountNumber)
        //     ->where('re_curring_rds.status', '=', 'Active')
        //     ->groupBy('re_curring_rds.id','re_curring_rds.rd_account_no','scheme_masters.name') // Group by the necessary columns
        //     ->get();

        // if (sizeof($data)<=0) {
        //     return response()->json(['status' => "fail", "data" => $data]);
        // }
        //     return response()->json(['status' => "success", "data" => $data]);
    // }



    public function transaction(Request $post)
    {
        // dd($post->all());

        switch ($post->actiontype) {
            case 'getLoanType':
                $loantypes = LoanMaster::where('memberType', $post->memberType)->get();
                return response()->json(['status' => "success", 'data' => $loantypes]);
                break;
            case 'getdata':
                $acdetails = MemberAccount::where(['accountNo' => $post->account, 'memberType' => $post->member])->first(['name', 'address']);
                if (!$acdetails) {
                    return response()->json(['status' => "Invalid Account number"]);
                }
                $share = new ShareController;
                $sharebalance = $share->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)));
                $txnacdetails = MemberLoan::where(['accountNo' => $post->account, 'memberType' => $post->member])->orderBy('id', 'asc')->get();

                return response()->json(['status' => "success", "txnacdetails" => $txnacdetails, "acdetails" => $acdetails, 'balance' => $sharebalance]);


                break;
            case 'grantordetails':
                $acdetails = MemberAccount::where('accountNo', $post->accountid)->where('memberType', 'Member')->first();
                if (!$acdetails) {
                    return response()->json(['status' => "Invalid Account number"]);
                }
                $beneficirys = MemberLoan::where('guranter1', $post->accountid)->orWhere('guranter2', $post->accountid)->orderBy('id', 'desc')->get();
                return response()->json(['status' => "success", 'data' => $acdetails, 'benelist' => $beneficirys]);
                break;
            case 'getLoatype':
                $data = LoanMaster::where('id', $post->loantypeid)->first();
                // dd($data);
                if (!$data) {
                    return response()->json(['status' => "Invalid Loan type"]);
                }
                return response()->json(['status' => "success", "data" => $data]);
                break;
            case 'getLoanAc':
                $acloan = MemberLoan::where('accountNo', $post->loanAcNo)->where('memberType', $post->member)->orderBy('id', 'desc')->get();
                // dd($acloan);
                if (count($acloan) > 0) {
                    return response()->json(['status' => "success", 'data' => $acloan]);
                }
                return response()->json(['status' => "Loan Account not found"]);
                break;
            case 'getaclist':

                $data['inpup'] = "Select FD AC";
                $data['inpup'] = "FD_id";
                $data['aclist'] = [];
                $data['type'] = $post->type;
                if ($post->type == "FD") {
                    $data['inpup'] = "Select FD AC";
                    $data['inpuplabel'] = "FD No";
                    $data['aclist'] = MemberFdScheme::where('membershipno', $post->memberAc)->where('is_delete', 'No')->whereIn('status', ['Active', 'Renewed'])->select('fdNo as account', 'principalAmount as amount', 'id')->get();
                } else if ($post->type == "RD") {
                    $data['inpup'] = "Select RD AC";
                    $data['inpup'] = "RD No";
                    $data['aclist'] = ReCurringRd::where('accountNo', $post->memberAc)->where('is_delete', 'No')->where('status', 'Active')->select('rd_account_no as account', 'amount as amount', 'id')->get();
                }


                return response()->json(['status' => "success", 'data' => $data]);
                break;
            case 'transactionloan':
                // dd($post->all());
                $acdetails = MemberAccount::where(['accountNo' => $post->accountNumber, 'memberType' => $post->memberType])->first();



                if (!$acdetails) {
                    return response()->json(['status' => "Invalid Account number"]);
                }

                //_______Get Old Loan
                $acloan = MemberLoan::where(
                    [
                        'loanAcNo' => $post->loanAcNo,
                        'accountNo' => $post->accountNumber,
                        'memberType' => $post->memberType
                    ]
                )
                    ->where('is_delete', '=', 'No')
                    ->first(['id']);


                if ($acloan) {
                    return response()->json(['status' => "Account number already exist"]);
                }

                $loanmaster = LoanMaster::where('id', $post->loanType)->first();
                if (!$loanmaster) {
                    return response()->json(['status' => "Invalid Loan Type"]);
                }

                $endDate =  date('Y-m-d', strtotime($post->loanDate));

                if (date('Y-m-d', strtotime($post->loanDate)) < $acdetails->openingDate) {
                    return response()->json(['status' => "Date should not greator " . $acdetails->openingDate]);
                }


                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->loanDate)));

                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session', 'message' => "Access denied for this session"], 400);
                }
                if (isset($post->loanYear) && $post->loanYear > 0) {
                    $loanDtate  = date('Y-m-d', strtotime($post->loanDate));
                    $newDateTimestamp = strtotime("+$post->loanYear years", strtotime($loanDtate));
                    $endDate = date('Y-m-d', $newDateTimestamp);
                }

                if (isset($post->loanMonth) && $post->loanMonth > 0) {
                    // dd($post->loanMonth);
                    $loanDtate  = date('Y-m-d', strtotime($endDate));
                    $newDateTimestamp = strtotime("+$post->loanMonth months", strtotime($loanDtate));
                    $endDate = date('Y-m-d', $newDateTimestamp);
                }



                $loancode = DB::table('loan_masters')
                    ->select(
                        'loan_masters.*',
                        'ledger_masters.id as ledgerid',
                        'ledger_masters.*'
                    )
                    ->where('loan_masters.id', $post->loanType)
                    ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                    ->first();




                // $fdIdsStrings = '';
                // $rdIdsStrings = '';
                // $dailyIdsStrings = '';

                // if ($loancode->loantypess === 'FD') {
                //     if (!empty($post->schemenames)) {
                //         $fdIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $fdIdsStrings = "";
                //     }
                // }

                // if ($loancode->loantypess === 'RD') {
                //     if (!empty($post->schemenames)) {
                //         $rdIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $rdIdsStrings = "";
                //     }
                // }


                // if ($loancode->loantypess === 'DailyDeposit') {
                //     if (!empty($post->schemenames)) {
                //         $dailyIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $dailyIdsStrings = "";
                //     }
                // }

                $share = new ShareController;
                $sharebalance = $share->getbalance($post->accountNumber, date('Y-m-d', strtotime($post->transactionDate)));

                $maxlimit = $sharebalance * 10;
                if ($maxlimit < $post->amount) {
                    //  return response()->json(['status' => "Amount sholud not be greter then" .  $maxlimit]);
                }

                do {
                    $generalLedgers = "loan" . time();
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);

                // if ($post->memberType == "Member") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //      dd($ledgerMaster);
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONM001")->first(['groupCode', 'ledgerCode']);
                // } else if ($post->memberType == "NonMember") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONN001")->first(['groupCode', 'ledgerCode']);
                // } else if ($post->memberType == "Staff") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONS001")->first(['groupCode', 'ledgerCode']);
                // }



                if ($post->loanBy == "Transfer") {
                    $ledgerMasterCR = LedgerMaster::where('id', $post->ledgerId)->first(['groupCode', 'ledgerCode']);
                    if (!$ledgerMasterCR) {
                        return response()->json(['status' => "Invalid Bank or Type"]);
                    }
                    // } else if ($post->loanBy == "Saving") {

                    //     $member_ship = $post->accountNumber;
                    //     $account_opening = DB::table('opening_accounts')
                    //         ->select(
                    //             'opening_accounts.*',
                    //             'schmeaster.id as sch_id',
                    //             'schmeaster.scheme_code',
                    //             'ledger_masters.reference_id',
                    //             'ledger_masters.ledgerCode',
                    //             'ledger_masters.groupCode',
                    //             'refSchemeMaster.scheme_code as ref_scheme_code'
                    //         )
                    //         ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                    //         ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                    //         ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                    //         ->where('opening_accounts.membershipno', $member_ship)
                    //         ->where('opening_accounts.accountname', 'Saving')
                    //         ->where('opening_accounts.status', 'Active')
                    //         ->first();

                    //     $account_nos = $post->savingaccounts;

                    //     if ($account_opening) {
                    //         if ($account_opening->groupCode && $account_opening->ledgerCode) {
                    //             // $saving_group = $account_opening->groupCode;
                    //             // $saving_ledger = $account_opening->ledgerCode;
                    //             $ledgerMasterCR = [
                    //                 'groupCode' => $account_opening->groupCode,
                    //                 'ledgerCode' => $account_opening->ledgerCode
                    //             ];
                    //         } else {
                    //             return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                    //         }
                    //     } else {
                    //         return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                    //     }
                } else {
                    $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
                }


                // if (!empty($post->schemenames)) {
                //     $post['year'] =  $post->loanYear;
                //     $post['month'] =  $post->loanMonth;
                //     $post['intrest'] =  $post->loanInterest;
                //     $post['loanAmount']  = $post->amount;
                //     $post['loanType']  =  $post->loanType;
                //     $post['loandate']  =  $post->loanDate;
                //     $processingFee = $post->processingRates;
                // } else {
                //     $post['year'] =  $loanmaster->years;
                //     $post['month'] =  $loanmaster->months;
                //     $post['intrest'] =  $loanmaster->interest;
                //     $post['loanAmount']  = $post->amount;
                //     $post['loanType']  =  $loanmaster->id;
                //     $post['loandate']  =  $post->loanDate;
                $processingFee = (($post->amount * $loanmaster->processingFee) / 100);
                // }

                // dd($processingFee);


                $member_ship = $post->accountNumber;
                // $account_opening = DB::table('opening_accounts')
                //     ->select(
                //         'opening_accounts.*',
                //         'schmeaster.id as sch_id',
                //         'schmeaster.scheme_code',
                //         'ledger_masters.reference_id',
                //         'ledger_masters.ledgerCode',
                //         'ledger_masters.groupCode',
                //         'refSchemeMaster.scheme_code as ref_scheme_code'
                //     )
                //     ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                //     ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                //     ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                //     ->where('opening_accounts.membershipno', $member_ship)
                //     ->where('opening_accounts.accountname', 'Saving')
                //     ->where('opening_accounts.status', 'Active')
                //     ->first();

                // $account_nos = $account_opening->accountNo;

                // if ($account_opening) {
                //     if ($account_opening->groupCode && $account_opening->ledgerCode) {
                //         $savingss = [
                //             'groupCode' => $account_opening->groupCode,
                //             'ledgerCode' => $account_opening->ledgerCode
                //         ];
                //     } else {
                //         return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                //     }
                // } else {
                //     return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                // }

                DB::beginTransaction();
                try {
                    // dd(date('Y-m-d', strtotime($post->loanDate)),$endDate);
                    $lastInsertedId = DB::table('member_loans')->insertGetId([
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        "serialNo" => $generalLedgers,
                        "loanDate"  => date('Y-m-d', strtotime($post->loanDate)),
                        "loanEndDate" => $endDate,
                        'memberType' => $post->memberType,
                        "ledgerCode"   => $ledgerMasterCR->ledgerCode,
                        'groupCode' =>  $ledgerMasterCR->groupCode,
                        "loanAcNo"  => $post->loanAcNo,
                        "purpose"   => $post->purpose,
                        "loanType"  => $post->loanType,
                        "processingFee" => $processingFee,
                        "processingRates" => $post->processingRates,
                        "loanYear" => $post->loanYear,
                        "loanMonth" => $post->loanMonth,
                        "loanInterest"  =>  $post->loanInterest,
                        "loanPanelty" => $post->defintr,
                        // "fdId"   =>  $fdIdsStrings,
                        // "fdAmount"  =>  $fdAcsString,
                        // "rd_id"   =>  $rdIdsStrings,
                        // "rd_aacount"  =>  $rdAcsString,
                        // "dailyId" => $dailyIdsStrings,
                        "loanAmount" => $post->amount,
                        "bankDeduction" => $post->bankDeduction,
                        "deductionAmount" => $post->deduction,
                        "pernote"  => $post->pernote,
                        "loanBy" => $post->loanBy,
                        "chequeNo" => "",
                        "loan_app_fee" => $post->loan_app_fee,
                        "installmentType" => $post->installmentType,
                        "guranter1" => "",
                        // "gaurantor1name" => "",
                        // "documents" => "",
                        "guranter2" => "",
                        "Status"   => "Disbursed",
                        "branchId"   => session('branchid') ?? 1,
                        'agentId' => $post->agentId,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    if (!empty($post->schemenames)) {
                        $this->insertInstallments($this->getinstallmetslistfd($post), $lastInsertedId);
                    } else {
                        $this->insertInstallments($this->getinstallmetslist($post), $lastInsertedId);
                    }

                    // if ($post->loanBy == "Saving") {
                    //     $saving_withdraw = new MemberSaving();
                    //     $saving_withdraw->secheme_id = $account_opening->sch_id;
                    //     $saving_withdraw->serialNo = $generalLedgers;
                    //     $saving_withdraw->accountId = $account_nos;
                    //     $saving_withdraw->accountNo = $post->accountNumber;
                    //     $saving_withdraw->memberType = $post->memberType;
                    //     $saving_withdraw->groupCode = $ledgerMasterCR['groupCode'];
                    //     $saving_withdraw->ledgerCode = $ledgerMasterCR['ledgerCode'];
                    //     $saving_withdraw->savingNo = '';
                    //     $saving_withdraw->transactionDate = date('Y-m-d', strtotime($post->loanDate));
                    //     $saving_withdraw->transactionType = 'toloan';
                    //     $saving_withdraw->depositAmount = $post->amount;
                    //     $saving_withdraw->withdrawAmount = 0;
                    //     $saving_withdraw->paymentType = '';
                    //     $saving_withdraw->bank = '';
                    //     $saving_withdraw->chequeNo = 'trfdFromLoan';
                    //     $saving_withdraw->narration = 'Saving A/c- ' . $account_nos . ' - From Loan' . $post->accountNumber;
                    //     $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    //     $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    //     $saving_withdraw->agentId = $post->agentId;
                    //     $saving_withdraw->updatedBy = $post->user()->id;
                    //     $saving_withdraw->is_delete = 'No';
                    //     $saving_withdraw->save();

                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $acdetails->id,
                    //     'memberType' => $post->memberType,
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   =>  $ledgerMasterCR['ledgerCode'],
                    //     'groupCode' => $ledgerMasterCR['groupCode'],
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName"        => "LoanDisbursed",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Cr',
                    //     'transactionAmount' => $post->amount,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);

                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $acdetails->id,
                    //     'memberType' => 'Member',
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   => $ledgerMaster->ledgerCode,
                    //     'groupCode' =>  $ledgerMaster->groupCode,
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName" => "LoanDisbursed",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Dr',
                    //     'transactionAmount' => $post->amount,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);



                    // if ($processingFee > 0) {
                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   => 'PRO01',
                    //         'groupCode' => 'INCM001',
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "Processing Fee",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Cr',
                    //         'transactionAmount' => $processingFee,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);

                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $account_nos,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   => $ledgerMasterCR['ledgerCode'],
                    //         'groupCode' => $ledgerMasterCR['groupCode'],
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "Processing Fee",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Dr',
                    //         'transactionAmount' => $processingFee,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);


                    //     DB::table('member_savings')->insert([
                    //         'secheme_id' => $account_opening->sch_id,
                    //         'serialNo' => $generalLedgers,
                    //         'accountId' => $account_nos,
                    //         'accountNo' => $post->accountNumber,
                    //         'memberType' => $post->memberType,
                    //         'groupCode' => $ledgerMasterCR['groupCode'],
                    //         'ledgerCode' => $ledgerMasterCR['ledgerCode'],
                    //         'savingNo' => '',
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'toloan',
                    //         'depositAmount' => 0,
                    //         'withdrawAmount' => $processingFee,
                    //         'paymentType' => '',
                    //         'bank' => '',
                    //         'chequeNo' => 'trfdFromLoan',
                    //         'narration' => 'Saving A/c- ' . $account_nos . ' - Processing Fee of Loan ' . $post->accountNumber,
                    //         'branchId' => session('branchId') ? session('branchId') : 1,
                    //         'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //         'agentId' => $post->agentId,
                    //         'updatedBy' => $post->user()->id,
                    //         'is_delete' => 'No',
                    //     ]);
                    // }

                    // $loan_application_charges = $post->loan_app_fee;

                    // if ($loan_application_charges > 0) {
                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   => 'LO05',
                    //         'groupCode' => 'INCM001',
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "Loan Applicaton Fee",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Cr',
                    //         'transactionAmount' => $loan_application_charges,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);

                    //     DB::table('member_savings')->insert([
                    //         'secheme_id' => $account_opening->sch_id,
                    //         'serialNo' => $generalLedgers,
                    //         'accountId' => $account_nos,
                    //         'accountNo' => $post->accountNumber,
                    //         'memberType' => $post->memberType,
                    //         'groupCode' => $savingss['groupCode'],
                    //         'ledgerCode' => $savingss['ledgerCode'],
                    //         'savingNo' => '',
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'toloan',
                    //         'depositAmount' => 0,
                    //         'withdrawAmount' => $loan_application_charges,
                    //         'paymentType' => '',
                    //         'bank' => '',
                    //         'chequeNo' => 'trfdFromLoan',
                    //         'narration' => 'Saving A/c- ' . $account_nos . ' - Loan App. Charges Loan ' . $post->accountNumber,
                    //         'branchId' => session('branchId') ? session('branchId') : 1,
                    //         'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //         'agentId' => $post->agentId,
                    //         'updatedBy' => $post->user()->id,
                    //         'is_delete' => 'No',
                    //     ]);

                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   =>  $savingss['ledgerCode'],
                    //         'groupCode' => $savingss['groupCode'],
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "LoanDisbursed",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Dr',
                    //         'transactionAmount' => $loan_application_charges,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);
                    // }

                    // if ($loancode->loantypess === 'FD') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $fdid) {
                    //             MemberFdScheme::where('id', $fdid)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }

                    // if ($loancode->loantypess === 'RD') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $fdid) {
                    //             ReCurringRd::where('id', $fdid)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }

                    // if ($loancode->loantypess === 'DailyDeposit') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $dailyId) {
                    //             DB::table('daily_collections')->where('id', $dailyId)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }

                    // } else {
                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $acdetails->id,
                    //     'memberType' => $post->memberType,
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   => 'PRO01',
                    //     'groupCode' => 'INCM001',
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName"        => "Processing Fee",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Cr',
                    //     'transactionAmount' => $processingFee,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);

                    // DB::table('member_savings')->insert([
                    //     'secheme_id' => $account_opening->sch_id,
                    //     'serialNo' => $generalLedgers,
                    //     'accountId' => $account_nos,
                    //     'accountNo' => $post->accountNumber,
                    //     'memberType' => $post->memberType,
                    //     'groupCode' => $savingss['groupCode'],
                    //     'ledgerCode' => $savingss['ledgerCode'],
                    //     'savingNo' => '',
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'toloan',
                    //     'depositAmount' => 0,
                    //     'withdrawAmount' => $processingFee,
                    //     'paymentType' => '',
                    //     'bank' => '',
                    //     'chequeNo' => 'trfdFromLoan',
                    //     'narration' => 'Saving A/c- ' . $account_nos . ' - Processing Fee of Loan ' . $post->accountNumber,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'agentId' => $post->agentId,
                    //     'updatedBy' => $post->user()->id,
                    //     'is_delete' => 'No',
                    // ]);

                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $account_opening->id,
                    //     'memberType' => $post->memberType,
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   => $savingss['ledgerCode'],
                    //     'groupCode' => $savingss['groupCode'],
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName"        => "Processing Fee",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Dr',
                    //     'transactionAmount' => $processingFee,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);



                    DB::table('general_ledgers')->insert([
                        "serialNo" => $generalLedgers,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $post->memberType,
                        'agentId' => $post->agentId,
                        "ledgerCode"   =>  $ledgerMasterCR['ledgerCode'],
                        'groupCode' => $ledgerMasterCR['groupCode'],
                        'referenceNo' => $lastInsertedId,
                        'entryMode' => "automatic",
                        "formName"  => "LoanDisbursed",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => now(),
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->insert([
                        "serialNo" => $generalLedgers,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => 'Member',
                        'agentId' => $post->agentId,
                        "ledgerCode"   => $loancode->ledgerCode,
                        'groupCode' =>  $loancode->groupCode,
                        'referenceNo' => $lastInsertedId,
                        'entryMode' => "automatic",
                        "formName" => "LoanDisbursed",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => now(),
                        'updatedBy' => $post->user()->id,
                    ]);



                    // $loan_application_charges = $post->loan_app_fee;

                    // if ($loan_application_charges > 0) {
                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   => 'LO05',
                    //         'groupCode' => 'INCM001',
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "Loan Applicaton Fee",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Cr',
                    //         'transactionAmount' => $loan_application_charges,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);

                    //     DB::table('member_savings')->insert([
                    //         'secheme_id' => $account_opening->sch_id,
                    //         'serialNo' => $generalLedgers,
                    //         'accountId' => $account_nos,
                    //         'accountNo' => $post->accountNumber,
                    //         'memberType' => $post->memberType,
                    //         'groupCode' => $savingss['groupCode'],
                    //         'ledgerCode' => $savingss['ledgerCode'],
                    //         'savingNo' => '',
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'toloan',
                    //         'depositAmount' => 0,
                    //         'withdrawAmount' => $loan_application_charges,
                    //         'paymentType' => '',
                    //         'bank' => '',
                    //         'chequeNo' => 'trfdFromLoan',
                    //         'narration' => 'Saving A/c- ' . $account_nos . ' - Loan App. Charges Loan ' . $post->accountNumber,
                    //         'branchId' => session('branchId') ? session('branchId') : 1,
                    //         'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //         'agentId' => $post->agentId,
                    //         'updatedBy' => $post->user()->id,
                    //         'is_delete' => 'No',
                    //     ]);

                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   =>  $savingss['ledgerCode'],
                    //         'groupCode' => $savingss['groupCode'],
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "LoanDisbursed",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Dr',
                    //         'transactionAmount' => $loan_application_charges,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);
                    // }


                    // dd($post->all());

                    // $loancode = DB::table('loan_masters')
                    //     ->select(
                    //         'loan_masters.*',
                    //         'ledger_masters.id as ledgerid',
                    //         'ledger_masters.*'
                    //     )
                    //     ->where('loan_masters.id', $post->loanType)
                    //     ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                    //     ->first();



                    // if ($loancode->loantypess === 'FD') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $fdid) {
                    //             MemberFdScheme::where('id', $fdid)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }

                    // if ($loancode->loantypess === 'RD') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $fdid) {
                    //             ReCurringRd::where('id', $fdid)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }

                    // if ($loancode->loantypess === 'DailyDeposit') {

                    //     if (!empty($post->schemenames)) {
                    //         foreach ($post->schemenames as $dailyId) {
                    //             DB::table('daily_collections')->where('id', $dailyId)->update(['status' => "Pluge"]);
                    //         }
                    //     }
                    // }
                    // }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Loan updated successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    return response()->json(['status' => $e->getMessage(), "message" => $e->getLine()]);
                }
                break;
            case 'actiontypeupdate':

                $acdetails = MemberAccount::where(['accountNo' => $post->accountNumber, 'memberType' => $post->memberType])->first();

                if (!$acdetails) {
                    return response()->json(['status' => "Invalid Account number"]);
                }
                // dd($post->all());
                // $acloan = MemberLoan::where(['loanAcNo' => $post->loanAcNo, 'accountNo' => $post->accountNumber, 'memberType' => $post->memberType])
                //      ->where('is_delete','=','No')->first(['id']);
                // if ($acloan) {
                //     return response()->json(['status' => "Account number already exist"]);
                // }

                // if (!empty($post->schemenames)) {
                // } else {
                $loanmaster = LoanMaster::where('id', $post->loanType)->first();
                if (!$loanmaster) {
                    return response()->json(['status' => "Invalid Loan Type"]);
                }
                // }


                if (date('Y-m-d', strtotime($post->loanDate)) < $acdetails->openingDate) {
                    return response()->json(['status' => "Date should not greator " . $acdetails->openingDate]);
                }

                $endDate =  date('Y-m-d', strtotime($post->loanDate));
                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->loanDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session', 'message' => "Access denied for this session"], 400);
                }
                if (isset($post->loanYear) && $post->loanYear > 0) {
                    $loanDtate  = date('Y-m-d', strtotime($post->loanDate));
                    $newDateTimestamp = strtotime("+$post->loanYear years", strtotime($loanDtate));
                    $endDate = date('Y-m-d', $newDateTimestamp);
                }
                if (isset($post->loanMonth) && $post->loanMonth > 0) {
                    $loanDtate  = date('Y-m-d', strtotime($endDate));
                    $newDateTimestamp = strtotime("+$post->loanMonth months", strtotime($loanDtate));
                    $endDate = date('Y-m-d', $newDateTimestamp);
                }

                $loancode = DB::table('loan_masters')
                    ->select(
                        'loan_masters.*',
                        'ledger_masters.id as ledgerid',
                        'ledger_masters.*'
                    )
                    ->where('loan_masters.id', $post->loanType)
                    ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                    ->first();



                // $fdIdsStrings = '';
                // $rdIdsStrings = '';
                // $dailyIdsStrings = '';

                // if ($loancode->loantypess === 'FD') {
                //     if (!empty($post->schemenames)) {
                //         $fdIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $fdIdsStrings = "";
                //     }
                // }

                // if ($loancode->loantypess === 'RD') {
                //     if (!empty($post->schemenames)) {
                //         $rdIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $rdIdsStrings = "";
                //     }
                // }

                // if ($loancode->loantypess === 'DailyDeposit') {
                //     if (!empty($post->schemenames)) {
                //         $dailyIdsStrings = implode(',', $post->schemenames);
                //     } else {
                //         $dailyIdsStrings = "";
                //     }
                // }


                // $rdIdsStrings = implode(',', $rdIdStrings);
                // $rdAcsString = implode(',', $rdAcString);

                $share = new ShareController;
                $sharebalance = $share->getbalance($post->accountNumber, date('Y-m-d', strtotime($post->transactionDate)));

                $maxlimit = $sharebalance * 10;
                if ($maxlimit < $post->amount) {
                    //  return response()->json(['status' => "Amount sholud not be greter then" .  $maxlimit]);
                }

                do {
                    $generalLedgers = "loan" . time();
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);



                // if ($post->memberType == "Member") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONM001")->first(['groupCode', 'ledgerCode']);
                // } else if ($post->memberType == "NonMember") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONN001")->first(['groupCode', 'ledgerCode']);
                // } else if ($post->memberType == "Staff") {
                //     $ledgerMaster = DB::table('loan_masters')
                //         ->select(
                //             'loan_masters.*',
                //             'ledger_masters.id as ledgerid',
                //             'ledger_masters.*'
                //         )
                //         ->where('loan_masters.id', $post->loanType)
                //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                //         ->first();
                //     // $ledgerMaster = LedgerMaster::where('ledgerCode', "LONS001")->first(['groupCode', 'ledgerCode']);
                // }


                if ($post->loanBy == "Transfer") {
                    $ledgerMasterCR = LedgerMaster::where('id', $post->ledgerId)->first(['groupCode', 'ledgerCode']);
                    if (!$ledgerMasterCR) {
                        return response()->json(['status' => "Invalid Bank or Type"]);
                    }
                } else {
                    //  else if ($post->loanBy == "Saving") {
                    //     $member_ship = $post->accountNumber;
                    //     $account_opening = DB::table('opening_accounts')
                    //         ->select(
                    //             'opening_accounts.*',
                    //             'schmeaster.id as sch_id',
                    //             'schmeaster.scheme_code',
                    //             'ledger_masters.reference_id',
                    //             'ledger_masters.ledgerCode',
                    //             'ledger_masters.groupCode',
                    //             'refSchemeMaster.scheme_code as ref_scheme_code'
                    //         )
                    //         ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                    //         ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                    //         ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                    //         ->where('opening_accounts.membershipno', $member_ship)
                    //         ->where('opening_accounts.accountname', 'Saving')
                    //         ->where('opening_accounts.status', 'Active')
                    //         ->first();

                    //     $account_nos = $post->savingaccounts;

                    //     if ($account_opening) {
                    //         if ($account_opening->groupCode && $account_opening->ledgerCode) {
                    //             // $saving_group = $account_opening->groupCode;
                    //             // $saving_ledger = $account_opening->ledgerCode;
                    //             $ledgerMasterCR = [
                    //                 'groupCode' => $account_opening->groupCode,
                    //                 'ledgerCode' => $account_opening->ledgerCode
                    //             ];
                    //         } else {
                    //             return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                    //         }
                    //     } else {
                    //         return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                    //     }
                    // } else {
                    $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
                }


                // if (!empty($post->schemenames)) {
                //     $post['year'] =  $post->loanYear;
                //     $post['month'] =  $post->loanMonth;
                //     $post['intrest'] =  $post->loanInterest;
                //     $post['loanAmount']  = $post->amount;
                //     $post['loanType']  =  $post->loanType;
                //     $post['loandate']  =  $post->loanDate;
                $processingFee = $post->processingRates;
                // } else {
                //     $post['year'] =  $loanmaster->years;
                //     $post['month'] =  $loanmaster->months;
                //     $post['intrest'] =  $loanmaster->interest;
                //     $post['loanAmount']  = $post->amount;
                //     $post['loanType']  =  $loanmaster->id;
                //     $post['loandate']  =  $post->loanDate;
                //     $processingFee = (($post->amount * $loanmaster->processingFee) / 100);
                // }


                $member_ship = $post->accountNumber;
                // $account_opening = DB::table('opening_accounts')
                //     ->select(
                //         'opening_accounts.*',
                //         'schmeaster.id as sch_id',
                //         'schmeaster.scheme_code',
                //         'ledger_masters.reference_id',
                //         'ledger_masters.ledgerCode',
                //         'ledger_masters.groupCode',
                //         'refSchemeMaster.scheme_code as ref_scheme_code'
                //     )
                //     ->leftJoin('scheme_masters as schmeaster', 'schmeaster.id', '=', 'opening_accounts.schemetype')
                //     ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'schmeaster.scheme_code')
                //     ->leftJoin('scheme_masters as refSchemeMaster', 'refSchemeMaster.id', '=', 'ledger_masters.reference_id')
                //     ->where('opening_accounts.membershipno', $member_ship)
                //     ->where('opening_accounts.accountname', 'Saving')
                //     ->where('opening_accounts.status', 'Active')
                //     ->first();

                // $account_nos = $account_opening->accountNo;

                // if ($account_opening) {
                //     if ($account_opening->groupCode && $account_opening->ledgerCode) {
                //         $savingss = [
                //             'groupCode' => $account_opening->groupCode,
                //             'ledgerCode' => $account_opening->ledgerCode
                //         ];
                //     } else {
                //         return response()->json(['status' => 'fail', 'messages' => 'Saving Group && Ledger Code Not Found']);
                //     }
                // } else {
                //     return response()->json(['status' => 'fail', 'messages' => 'Saving Account Not Found']);
                // }



                DB::beginTransaction();
                try {

                    $loanFetch = MemberLoan::where("id", $post->id)->first();

                    if (!$loanFetch) {
                        return response()->json(['status' => "Invlid account number"]);
                    }
                    $result = $this->isDateBetween(date('Y-m-d', strtotime($loanFetch->loanDate)));

                    if (!$result) {
                        return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
                    }


                    $receipt = LoanRecovery::where('LoanId', $post->id)->where('is_delete', 'No')->first();

                    // if ($receipt) {
                    //     return response()->json(['status' => "Please Detele Recovery First"]);
                    // }else {

                    // if (!empty($loanFetch->fdId)) {
                    //     $fdidis = explode(',', $loanFetch->fdId);
                    //     foreach ($fdidis as $fdidisis) {
                    //         MemberFdScheme::where('id', $fdidisis)->update(['status' => "Active"]);
                    //     }
                    // }

                    // if (!empty($loanFetch->rd_id)) {
                    //     $rdidis = explode(',', $loanFetch->rd_id);
                    //     foreach ($rdidis as $rdidisis) {
                    //         DB::table('re_curring_rds')->where('id', $rdidisis)->update(['status' => "Active"]);
                    //     }
                    // }

                    // if (!empty($loanFetch->dailyId)) {
                    //     $dailyids = explode(',', $loanFetch->dailyId);
                    //     foreach ($dailyids as $ids) {
                    //         DB::table('daily_collections')->where('id', $ids)->update(['status' => "Active"]);
                    //     }
                    // }


                    DB::table('general_ledgers')->where('serialNo', $loanFetch->serialNo)->delete();
                    DB::table('member_loans')->where("id", $post->id)->delete();
                    DB::table('loan_installments')->where("LoanId", $post->id)->delete();
                    // DB::table('member_savings')->where('serialNo', $loanFetch->serialNo)->delete();


                    $lastInsertedId = DB::table('member_loans')->insertGetId([
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        "serialNo" => $generalLedgers,
                        "loanDate"  => date('Y-m-d', strtotime($post->loanDate)),
                        "loanEndDate" =>  $endDate,
                        'memberType' => $post->memberType,
                        "ledgerCode"   => $ledgerMasterCR->ledgerCode,
                        'groupCode' =>  $ledgerMasterCR->groupCode,
                        "loanAcNo"  => $post->loanAcNo,
                        "purpose"   => $post->purpose,
                        "loanType"  => $post->loanType,
                        "processingFee" => $processingFee,
                        "processingRates" => $post->processingRates,
                        "loanYear" => $post->loanYear,
                        "loanMonth" => $post->loanMonth,
                        "loanInterest"  =>  $post->loanInterest,
                        "loanPanelty" => $post->defintr,
                        // "fdId"   =>  $fdIdsStrings,
                        // "fdAmount"  =>  $fdAcsString,
                        // "rd_id"   =>  $rdIdsStrings,
                        // "rd_aacount"  =>  $rdAcsString,
                        // "dailyId"   =>  $dailyIdsStrings,

                        "loanAmount" => $post->amount,
                        "bankDeduction" => $post->bankDeduction,
                        "deductionAmount" => $post->deduction,
                        "pernote"  => $post->pernote,
                        "loanBy" => $post->loanBy,
                        "chequeNo" => "",
                        "loan_app_fee" => $post->loan_app_fee,
                        "installmentType" => $post->installmentType,
                        "guranter1" => "",
                        "guranter2" => "",
                        // "documents" => "",
                        // "gaurantor1name" => "",
                        "Status"   => "Disbursed",
                        "branchId"   => session('branchid') ?? 1,
                        'agentId' => $post->agentId,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    if (!empty($post->schemenames)) {
                        $this->insertInstallments($this->getinstallmetslistfd($post), $lastInsertedId);
                    } else {
                        $this->insertInstallments($this->getinstallmetslist($post), $lastInsertedId);
                    }

                    // if ($post->loanBy == "Saving") {
                    //     $saving_withdraw = new MemberSaving();
                    //     $saving_withdraw->secheme_id = $account_opening->sch_id;
                    //     $saving_withdraw->serialNo = $generalLedgers;
                    //     $saving_withdraw->accountId = $account_nos;
                    //     $saving_withdraw->accountNo = $post->accountNumber;
                    //     $saving_withdraw->memberType = $post->memberType;
                    //     $saving_withdraw->groupCode = $ledgerMasterCR['groupCode'];
                    //     $saving_withdraw->ledgerCode = $ledgerMasterCR['ledgerCode'];
                    //     $saving_withdraw->savingNo = '';
                    //     $saving_withdraw->transactionDate = date('Y-m-d', strtotime($post->loanDate));
                    //     $saving_withdraw->transactionType = 'toloan';
                    //     $saving_withdraw->depositAmount = $post->amount;
                    //     $saving_withdraw->withdrawAmount = 0;
                    //     $saving_withdraw->paymentType = '';
                    //     $saving_withdraw->bank = '';
                    //     $saving_withdraw->chequeNo = 'trfdFromLoan';
                    //     $saving_withdraw->narration = 'Saving A/c- ' . $account_nos . ' - From Trfd Loan ' . $post->accountNumber;
                    //     $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    //     $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    //     $saving_withdraw->agentId = $post->agentId;
                    //     $saving_withdraw->updatedBy = $post->user()->id;
                    //     $saving_withdraw->is_delete = 'No';
                    //     $saving_withdraw->save();

                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => $post->memberType,
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   =>  $ledgerMasterCR['ledgerCode'],
                    //         'groupCode' => $ledgerMasterCR['groupCode'],
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName"        => "LoanDisbursed",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Cr',
                    //         'transactionAmount' => $post->amount,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);

                    //     DB::table('general_ledgers')->insert([
                    //         "serialNo" => $generalLedgers,
                    //         'accountNo' => $acdetails->accountNo,
                    //         "accountId"  =>  $acdetails->id,
                    //         'memberType' => 'Member',
                    //         'agentId' => $post->agentId,
                    //         "ledgerCode"   => $ledgerMaster->ledgerCode,
                    //         'groupCode' =>  $ledgerMaster->groupCode,
                    //         'referenceNo' => $lastInsertedId,
                    //         'entryMode' => "automatic",
                    //         "formName" => "LoanDisbursed",
                    //         'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //         'transactionType' => 'Dr',
                    //         'transactionAmount' => $post->amount,
                    //         'narration' => $post->naration,
                    //         'branchId' =>  session('branchid') ?? 1,
                    //         'sessionId' => session('sessionId') ?? 1,
                    //         'created_at' => date('Y-m-d H:i:s'),
                    //         'updatedBy' => $post->user()->id,
                    //     ]);



                    //     if ($processingFee > 0) {
                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $acdetails->id,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   => 'PRO01',
                    //             'groupCode' => 'INCM001',
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "Processing Fee",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Cr',
                    //             'transactionAmount' => $processingFee,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);

                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $account_nos,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   => $ledgerMasterCR['ledgerCode'],
                    //             'groupCode' => $ledgerMasterCR['groupCode'],
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "Processing Fee",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Dr',
                    //             'transactionAmount' => $processingFee,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);


                    //         DB::table('member_savings')->insert([
                    //             'secheme_id' => $account_opening->sch_id,
                    //             'serialNo' => $generalLedgers,
                    //             'accountId' => $account_nos,
                    //             'accountNo' => $post->accountNumber,
                    //             'memberType' => $post->memberType,
                    //             'groupCode' => $ledgerMasterCR['groupCode'],
                    //             'ledgerCode' => $ledgerMasterCR['ledgerCode'],
                    //             'savingNo' => '',
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'toloan',
                    //             'depositAmount' => 0,
                    //             'withdrawAmount' => $processingFee,
                    //             'paymentType' => '',
                    //             'bank' => '',
                    //             'chequeNo' => 'trfdFromLoan',
                    //             'narration' => 'Saving A/c- ' . $account_nos . ' - Processing Fee of Loan ' . $post->accountNumber,
                    //             'branchId' => session('branchId') ? session('branchId') : 1,
                    //             'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //             'agentId' => $post->agentId,
                    //             'updatedBy' => $post->user()->id,
                    //             'is_delete' => 'No',
                    //         ]);
                    //     }

                    //     $loan_application_charges = $post->loan_app_fee;

                    //     if ($loan_application_charges > 0) {
                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $acdetails->id,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   => 'LO05',
                    //             'groupCode' => 'INCM001',
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "Loan Applicaton Fee",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Cr',
                    //             'transactionAmount' => $loan_application_charges,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);

                    //         DB::table('member_savings')->insert([
                    //             'secheme_id' => $account_opening->sch_id,
                    //             'serialNo' => $generalLedgers,
                    //             'accountId' => $account_nos,
                    //             'accountNo' => $post->accountNumber,
                    //             'memberType' => $post->memberType,
                    //             'groupCode' => $savingss['groupCode'],
                    //             'ledgerCode' => $savingss['ledgerCode'],
                    //             'savingNo' => '',
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'toloan',
                    //             'depositAmount' => 0,
                    //             'withdrawAmount' => $loan_application_charges,
                    //             'paymentType' => '',
                    //             'bank' => '',
                    //             'chequeNo' => 'trfdFromLoan',
                    //             'narration' => 'Saving A/c- ' . $account_nos . ' - Loan App. Charges Loan ' . $post->accountNumber,
                    //             'branchId' => session('branchId') ? session('branchId') : 1,
                    //             'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //             'agentId' => $post->agentId,
                    //             'updatedBy' => $post->user()->id,
                    //             'is_delete' => 'No',
                    //         ]);

                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $acdetails->id,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   =>  $savingss['ledgerCode'],
                    //             'groupCode' => $savingss['groupCode'],
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "LoanDisbursed",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Dr',
                    //             'transactionAmount' => $loan_application_charges,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);

                    //     }

                    //     if ($loancode->loantypess === 'FD') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $fdid) {
                    //                 MemberFdScheme::where('id', $fdid)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }

                    //     if ($loancode->loantypess === 'RD') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $fdid) {
                    //                 ReCurringRd::where('id', $fdid)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }

                    //     if ($loancode->loantypess === 'DailyDeposit') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $dailyId) {
                    //                 DB::table('daily_collections')->where('id', $dailyId)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }
                    // } else {
                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $acdetails->id,
                    //     'memberType' => $post->memberType,
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   => 'PRO01',
                    //     'groupCode' => 'INCM001',
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName"        => "Processing Fee",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Cr',
                    //     'transactionAmount' => $processingFee,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);

                    // DB::table('member_savings')->insert([
                    //     'secheme_id' => $account_opening->sch_id,
                    //     'serialNo' => $generalLedgers,
                    //     'accountId' => $account_nos,
                    //     'accountNo' => $post->accountNumber,
                    //     'memberType' => $post->memberType,
                    //     'groupCode' => $savingss['groupCode'],
                    //     'ledgerCode' => $savingss['ledgerCode'],
                    //     'savingNo' => '',
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'toloan',
                    //     'depositAmount' => 0,
                    //     'withdrawAmount' => $processingFee,
                    //     'paymentType' => '',
                    //     'bank' => '',
                    //     'chequeNo' => 'trfdFromLoan',
                    //     'narration' => 'Saving A/c- ' . $account_nos . ' - Processing Fee of Loan ' . $post->accountNumber,
                    //     'branchId' => session('branchId') ? session('branchId') : 1,
                    //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //     'agentId' => $post->agentId,
                    //     'updatedBy' => $post->user()->id,
                    //     'is_delete' => 'No',
                    // ]);

                    // DB::table('general_ledgers')->insert([
                    //     "serialNo" => $generalLedgers,
                    //     'accountNo' => $acdetails->accountNo,
                    //     "accountId"  =>  $account_opening->id,
                    //     'memberType' => $post->memberType,
                    //     'agentId' => $post->agentId,
                    //     "ledgerCode"   => $savingss['ledgerCode'],
                    //     'groupCode' => $savingss['groupCode'],
                    //     'referenceNo' => $lastInsertedId,
                    //     'entryMode' => "automatic",
                    //     "formName"        => "Processing Fee",
                    //     'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //     'transactionType' => 'Dr',
                    //     'transactionAmount' => $processingFee,
                    //     'narration' => $post->naration,
                    //     'branchId' =>  session('branchid') ?? 1,
                    //     'sessionId' => session('sessionId') ?? 1,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updatedBy' => $post->user()->id,
                    // ]);


                    DB::table('general_ledgers')->insert([
                        "serialNo" => $generalLedgers,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $post->memberType,
                        'agentId' => $post->agentId,
                        "ledgerCode"   =>  $ledgerMasterCR['ledgerCode'],
                        'groupCode' => $ledgerMasterCR['groupCode'],
                        'referenceNo' => $lastInsertedId,
                        'entryMode' => "automatic",
                        "formName"        => "LoanDisbursed",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Cr',
                        'transactionAmount' => $post->amount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->insert([
                        "serialNo" => $generalLedgers,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => 'Member',
                        'agentId' => $post->agentId,
                        "ledgerCode"   => $loancode->ledgerCode,
                        'groupCode' =>  $loancode->groupCode,
                        'referenceNo' => $lastInsertedId,
                        'entryMode' => "automatic",
                        "formName" => "LoanDisbursed",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Dr',
                        'transactionAmount' => $post->amount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post->user()->id,
                    ]);



                    //     $loan_application_charges = $post->loan_app_fee;

                    //     if ($loan_application_charges > 0) {
                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $acdetails->id,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   => 'LO05',
                    //             'groupCode' => 'INCM001',
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "Loan Applicaton Fee",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Cr',
                    //             'transactionAmount' => $loan_application_charges,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);

                    //         DB::table('member_savings')->insert([
                    //             'secheme_id' => $account_opening->sch_id,
                    //             'serialNo' => $generalLedgers,
                    //             'accountId' => $account_nos,
                    //             'accountNo' => $post->accountNumber,
                    //             'memberType' => $post->memberType,
                    //             'groupCode' => $savingss['groupCode'],
                    //             'ledgerCode' => $savingss['ledgerCode'],
                    //             'savingNo' => '',
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'toloan',
                    //             'depositAmount' => 0,
                    //             'withdrawAmount' => $loan_application_charges,
                    //             'paymentType' => '',
                    //             'bank' => '',
                    //             'chequeNo' => 'trfdFromLoan',
                    //             'narration' => 'Saving A/c- ' . $account_nos . ' - Loan App. Charges Loan ' . $post->accountNumber,
                    //             'branchId' => session('branchId') ? session('branchId') : 1,
                    //             'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    //             'agentId' => $post->agentId,
                    //             'updatedBy' => $post->user()->id,
                    //             'is_delete' => 'No',
                    //         ]);

                    //         DB::table('general_ledgers')->insert([
                    //             "serialNo" => $generalLedgers,
                    //             'accountNo' => $acdetails->accountNo,
                    //             "accountId"  =>  $acdetails->id,
                    //             'memberType' => $post->memberType,
                    //             'agentId' => $post->agentId,
                    //             "ledgerCode"   =>  $savingss['ledgerCode'],
                    //             'groupCode' => $savingss['groupCode'],
                    //             'referenceNo' => $lastInsertedId,
                    //             'entryMode' => "automatic",
                    //             "formName"        => "LoanDisbursed",
                    //             'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                    //             'transactionType' => 'Dr',
                    //             'transactionAmount' => $loan_application_charges,
                    //             'narration' => $post->naration,
                    //             'branchId' =>  session('branchid') ?? 1,
                    //             'sessionId' => session('sessionId') ?? 1,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //             'updatedBy' => $post->user()->id,
                    //         ]);
                    //     }

                    //     $loancode = DB::table('loan_masters')
                    //         ->select(
                    //             'loan_masters.*',
                    //             'ledger_masters.id as ledgerid',
                    //             'ledger_masters.*'
                    //         )
                    //         ->where('loan_masters.id', $post->loanType)
                    //         ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                    //         ->first();



                    //     if ($loancode->loantypess === 'FD') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $fdid) {
                    //                 MemberFdScheme::where('id', $fdid)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }

                    //     if ($loancode->loantypess === 'RD') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $fdid) {
                    //                 ReCurringRd::where('id', $fdid)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }

                    //     if ($loancode->loantypess === 'DailyDeposit') {

                    //         if (!empty($post->schemenames)) {
                    //             foreach ($post->schemenames as $dailyId) {
                    //                 DB::table('daily_collections')->where('id', $dailyId)->update(['status' => "Pluge"]);
                    //             }
                    //         }
                    //     }
                    // }
                    // }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Loan updated successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    return response()->json(['status' => $e->getMessage(), "message" => $e->getline()], 200);
                }
                break;
            case 'installmentsupdate':

                $loanRecept = LoanRecovery::where('id', $post->id)->first();
                if (!$loanRecept) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }
                $memberLoan  =  MemberLoan::where('id', $loanRecept->loanId)->first();
                if (!$memberLoan) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }

                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->loanDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session', 'message' => "Access denied for this session"], 400);
                }

                DB::beginTransaction();
                try {

                    if ($post->ReceivedAmount >= ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate - $post->overdue)) {
                        $pendingintrest = 0;
                        $princple =  $post->ReceivedAmount - ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate + $post->overdue);
                    } else {
                        $princple = 0;
                        $pendingintrest =  ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate - $post->overdue) - $post->ReceivedAmount;
                    }

                    $loan =  LoanRecovery::where(['id' => $post->id])->update([
                        'receiptDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'principal' =>  $princple,
                        'interest' => $post->InterestTillDate,
                        'pendingInterest' => $pendingintrest,
                        'penalInterest' => $post->PenaltyTillDate,
                        'total' => $post->TotalTillDate,
                        'receivedAmount' => $post->ReceivedAmount,
                        "overDueInterest" => $post->overdue,
                        'status' => "True",
                        "receivedBy" => "Cash",
                        "branchId"   => session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->where(['referenceNo' => $post->id, 'transactionType' => "Dr"])->update([
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $post->memberType,
                        'agentId' => $post->agentId,
                        "ledgerCode"   => $ledgerMasterCR->ledgerCode,
                        'groupCode' =>  $ledgerMasterCR->groupCode,
                        "formName"        => "LoanReceipt",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionAmount' => $post->ReceivedAmount,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->where(['referenceNo' => $post->id, 'transactionType' => "Cr"])->update([
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => 'Member',
                        'agentId' => $post->agentId,
                        "ledgerCode"   => $ledgerMaster->ledgerCode,
                        'groupCode' =>  $ledgerMaster->groupCode,
                        "formName" => "LoanDisbursed",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionAmount' => $post->amount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Loan updated successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    return response()->json(['status' => "Some Technical issue occurred", "message" => "Some Technical issue occurred"], 200);
                }

                break;
            case 'getInstallmets':
                $loanid = $post->id;
                // dd($loanid);
                if (!empty($loanid)) {
                    $loan = DB::table('member_loans')->where('id', $loanid)->first();
                    $datas = DB::table('loan_installments')->where('LoanId', $loanid)->get();
                    return response()->json(['status' => 'success', 'installments' => $datas, 'loan' => $loan]);
                } else {
                    $datas['installments'] = $this->getinstallmetslist($post);
                    return response()->json(view('transaction.loan.installments')->with($datas)->render());
                }
                break;
            case 'guarantor':

                break;
            case 'loandata':
                $loanaccount =  MemberLoan::where('id', $post->id)->first();
                if (!$loanaccount) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }
                return response()->json(["status" => "success", "data" => $loanaccount], 200);
                break;
            case 'paidinstallments':
                // dd($post->all());

                $loanaccount =  MemberLoan::where('id', $post->id)->first();

                if (!$loanaccount) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }

                $this->loanstatus($loanaccount->id);

                if ($loanaccount->status == "Closed") {
                    return response()->json(["status" => "Loan account has been closed"], 200);
                }
                if ($post->ReceivedAmount >= ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate - $post->overdue)) {
                    $pendingintrest = 0;
                    $princple =  $post->ReceivedAmount - ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate + $post->overdue);
                } else {
                    $princple = 0;
                    $pendingintrest =  ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate - $post->overdue) - $post->ReceivedAmount;
                }

                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->loanDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session', 'message' => "Access denied for this session"], 400);
                }

                $installmentsTillDate = LoanInstallment::where('LoanId', $post->id)
                    ->whereDate('installmentDate', "<=", date('Y-m-d', strtotime($post->loanDate)))
                    ->whereIn('status', ['False', 'Partial'])
                    ->get();


                if ($post->loanBy == "Transfer") {
                    $ledgerMasterCR = LedgerMaster::where('id', $post->ledgerId)->first(['groupCode', 'ledgerCode']);
                    if (!$ledgerMasterCR) {
                        return response()->json(['status' => "Invalid Bank or Type"]);
                    }
                } else {
                    $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
                }





                DB::beginTransaction();
                try {
                    // $loaninstallments->status = "True";
                    // $loaninstallments->paid_date = date('Y-m-d', strtotime($post->transactionDate));
                    // $loaninstallments->save();
                    // $intGroupCode = "INCM001";

                    $ledger_codes = DB::table('ledger_masters')->where('loanmasterId', $loanaccount->loanType)->get();

                    $legders = '';
                    foreach ($ledger_codes as $ledger_code) {
                        if ($ledger_code->groupCode === 'INCM001') {
                            $legders = $ledger_code->ledgerCode;
                        }
                    }


                    if ($post->memberType == "Member") {
                        // $pricpleCode = $ledger_codes->;
                        $curentintCode = 'LONM002';
                        $penalCode = "LONM003";
                        $pendingCode = "LONM004";
                    } else if ($post->memberType == "NonMember") {
                        // $pricpleCode = "LONN001";
                        $curentintCode = 'LONN002';
                        $penalCode = "LONN003";
                        $pendingCode = "LONN004";
                    } else if ($post->memberType == "Staff") {
                        // $pricpleCode = $loanaccount->groupCode;
                        $curentintCode = 'LONS002';
                        $penalCode = "LONS003";
                        $pendingCode = "LONS004";
                    }

                    // dd($pricpleCode,$curentintCode,$penalCode,$pendingCode);

                    $paidAmount = $post->ReceivedAmount - $post->PendingIntrTillDate - $post->overdue - $post->PenaltyTillDate;
                    $InstallmentIds  = [];

                    foreach ($installmentsTillDate as $key => $installment) {

                        if ($paidAmount >= $installment->principal + $installment->interest) {
                            LoanInstallment::where('id', $installment->id)->update([
                                "status" => "True",
                                "paid_date" => date('Y-m-d', strtotime($post->loanDate)),
                                "re_amount" =>  $installment->principal + $installment->interest
                            ]);

                            $paidAmount  = $paidAmount - $installment->principal + $installment->interest;
                            $InstallmentIds[$key]  =  $installment->id;
                        } else {
                            $total = $paidAmount - ($installment->principal + $installment->interest);


                            if (($paidAmount - $installment->interest) > 0) {
                                LoanInstallment::where('id', $installment->id)
                                    ->update([
                                        "status" => "Partial",
                                        "paid_date" => date('Y-m-d', strtotime($post->loanDate)),
                                        "re_amount" => ($installment->principal + $installment->interest) - $paidAmount,
                                    ]);
                                $InstallmentIds[$key]  =  $installment->id;
                            }
                            break;
                        }
                    }
                    $InstallmentId = implode(',', $InstallmentIds);
                    $loan =  LoanRecovery::create([
                        'loanId' => $post->id,
                        'receiptDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'principal' =>  $princple,
                        'interest' => $post->InterestTillDate,
                        'pendingInterest' => $pendingintrest,
                        'penalInterest' => $post->PenaltyTillDate ?? 0,
                        'total' => $post->TotalTillDate,
                        'receivedAmount' => $post->ReceivedAmount,
                        "overDueInterest" => $post->overdue,
                        'status' => "True",
                        "receivedBy" => $post->loanBy,
                        "branchId"   => session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'instaId' => $InstallmentId,
                        'updatedBy' => $post->user()->id,
                    ]);


                    do {
                        $generalLedgers = "loan" . rand(1111111, 9999999);
                    } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);


                    DB::table('general_ledgers')->insert([
                        "serialNo" => $generalLedgers,
                        "accountId"  =>  $loanaccount->accountId,
                        'accountNo' => $loanaccount->accountNo,
                        'memberType' => $post->memberType,
                        'agentId' => $post->agentId,
                        "ledgerCode"   => $ledgerMasterCR->ledgerCode,
                        'groupCode' =>  $ledgerMasterCR->groupCode,
                        'referenceNo' => $loan->id,
                        'entryMode' => "manual",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Dr',
                        "formName"        => "LoanReceipt",
                        'transactionAmount' => $post->ReceivedAmount,
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'updatedBy' => $post->user()->id,
                    ]);

                    $insert = [
                        "serialNo" => $generalLedgers,
                        "accountId"  =>  $loanaccount->accountId,
                        'accountNo' => $loanaccount->accountNo,
                        'memberType' => 'Member',
                        'agentId' => $post->agentId,
                        'referenceNo' => $loan->id,
                        'entryMode' => "manual",
                        'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                        'transactionType' => 'Cr',
                        "formName"        => "LoanReceipt",
                        'narration' => $post->naration,
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post->user()->id,
                    ];


                    /*   insert penal  interest transaction */
                    if ($post->PenaltyTillDate > 0) {

                        $insert["ledgerCode"]  = $penalCode;
                        $insert["groupCode"]  = 'INCM001';
                        $insert['transactionAmount'] = $post->PenaltyTillDate;
                        DB::table('general_ledgers')->insert($insert);
                    }



                    /*   insert pending  interest transaction */
                    if ($post->PendingIntrTillDate > 0) {

                        $insert["ledgerCode"]  = $curentintCode;
                        $insert["groupCode"]  = 'INCM001';
                        $insert['transactionAmount'] = $post->PendingIntrTillDate;
                        DB::table('general_ledgers')->insert($insert);
                    }



                    /*   insert current  interest transaction */
                    if ($post->InterestTillDate > 0) {

                        $insert["ledgerCode"]  = $curentintCode;
                        $insert["groupCode"]  = 'INCM001';
                        $insert['transactionAmount'] = $post->InterestTillDate;
                        DB::table('general_ledgers')->insert($insert);
                    }



                    /*   insert princple transaction */
                    if ($princple > 0) {

                        $insert["ledgerCode"]  = $loanaccount->ledgerCode;
                        $insert["groupCode"]  = $loanaccount->groupCode;
                        $insert['transactionAmount'] = $princple;
                        DB::table('general_ledgers')->insert($insert);
                    }



                    if ($post->overdue > 0) {
                        $insert["ledgerCode"]  = $curentintCode;
                        $insert["groupCode"]  = 'INCM001';
                        $insert['transactionAmount'] = $post->overdue;
                        DB::table('general_ledgers')->insert($insert);
                    }

                    $loanrecovery =  LoanRecovery::where('loanId', $post->id)->where('is_delete', 'No')->get();
                    $this->loanstatus($loanaccount->id);
                    DB::commit();


                    return response()->json([
                        'status' => 'success',
                        'recovery' => $loanrecovery,
                        'message' => 'Loan updated successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    dd($e->getMessage());
                    return response()->json(['status' => "failed", "message" => "Some Technical issue occurred"], 200);
                }

                break;
            case 'getloandetails':

                $loanFetch = MemberLoan::where("id", $post->id)->first();

                $loanAmount = $loanFetch->loanAmount;
                $loanDate = \Carbon\Carbon::parse($loanFetch->loanDate);
                $transactionDate = \Carbon\Carbon::parse($post->transactiondate);

                // Elapsed days
                $diffDays = $loanDate->diffInDays($transactionDate);

                // Total loan term in days
                $years = (int)$loanFetch->loanYear;
                $months = (int)$loanFetch->loanMonth;
                $totalLoanDays = ($years * 365) + ($months * 30);

                if ($totalLoanDays === 0) {
                    throw new \Exception("Total loan duration cannot be zero.");
                }

                // Annual interest in percent (e.g., 9.5 for 9.5%)
                $annualInterestRate = $loanFetch->loanInterest;

                // Accrued principal: linear increase
                $accruedPrincipal = ($loanAmount / $totalLoanDays) * $diffDays;

                // Accrued interest: simple interest on full loan amount
                $accruedInterest = ($loanAmount * $annualInterestRate * $diffDays) / 36500;

                // Round results
                // $accruedPrincipal = round($accruedPrincipal, 2);
                // $accruedInterest = round($accruedInterest, 2);

                // dd([
                //     'Loan Amount' => round($loanAmount, 2),
                //     'Elapsed Days' => $diffDays,
                //     'Total Loan Days' => $totalLoanDays,
                //     'Annual Interest Rate (%)' => $annualInterestRate,
                //     'Accrued Principal' => $accruedPrincipal,
                //     'Accrued Interest' => $accruedInterest,
                // ]);

                if (!$loanFetch) {
                    return response()->json(['status' => "Invlid account number"]);
                }

                $loanFetch1 = LoanMaster::where('id', $loanFetch->loanType)
                    ->where('is_delete', 'No')
                    ->first();

                $installmentlist = DB::table('loan_installments')->where('LoanId', $post->id)->get();

                $loanDate = $loanFetch->loanDate;
                $loanInterest = $loanFetch->loanInterest;
                $loanPanelty = $loanFetch->loanPanelty;
                $loanStatus = $loanFetch->status;

                $todayDate  = date('Y-m-d', strtotime($post->transactiondate));

                if ($todayDate < $loanDate && $todayDate != $loanDate) {
                    return response()->json(['status' => 'Date is Not Correct, It Should Be ' . date("d-m-Y", strtotime($loanDate)) . ' or Above.']);
                }

                $receiptTillDate = LoanRecovery::where('LoanId', $post->id)
                    ->where('is_delete', 'No')
                    ->sum('Principal');

                $receiptMaster = LoanRecovery::where('LoanId', $post->id)
                    ->where('is_delete', 'No')
                    ->max('ReceiptDate');

                $countReceipt = LoanRecovery::where('LoanId', $post->id)
                    ->where('is_delete', 'No')
                    ->count();

                $pendingInterest = LoanRecovery::where('LoanId', $post->id)
                    ->where('ReceiptDate', $receiptMaster)
                    ->where('is_delete', 'No')
                    ->first();

                $allrecovery =  LoanRecovery::where('loanId', $post->id)
                    ->where('is_delete', 'No')
                    ->get();

                $loanReceipt = LoanRecovery::where('LoanId', $post->id)
                    ->where('is_delete', 'No')
                    ->count();

                $lastReceiptDate = $receiptMaster ? $receiptMaster : $loanDate;

                $datetime1 = Carbon::parse($lastReceiptDate);
                $datetime2 = Carbon::parse($post->transactiondate);
                $days = $datetime1->diffInDays($datetime2);
                if ($days == 0) {
                    // $days =  1;
                }
                $countDays =  $days;
                $installmentsTillDate = LoanInstallment::where('LoanId', $post->id)
                    ->whereDate('installmentDate', "<=", date('Y-m-d', strtotime($post->transactiondate)))
                    ->whereIn('status', ['False', 'Partial'])
                    ->get();
                $pintr = $pendingInterest->pendingInterest ?? 0;
                $interest = 0;
                $panelty = 0;
                $principalTillDate = 0;
                $finterest = 0;
                $balInstallment = $loanFetch->loanAmount - $receiptTillDate;
                $overdues = 0;

                foreach ($installmentsTillDate as $key => $installment) {
                    if ($installment->status == "Partial") {
                        $principalTillDate += $installment->re_amount;
                    } else {
                        $principalTillDate += $installment->principal;
                        $interest +=   $installment->interest;
                    }

                    $datetime1 = Carbon::parse($installment->installmentDate);
                    $datetime2 = Carbon::parse($post->transactiondate);
                    $duedays = $datetime1->diffInDays($datetime2);
                    $dailyintrest =  (($installment->principal + $installment->interest) * ($loanInterest + $loanPanelty) / 100) / 365;
                    if ($installment->status != "Partial") {
                        $overdues += $dailyintrest * $duedays;
                    }

                    //  $interest += ($balInstallment * $days * $loanInterest) / 36500;
                }

                $receiptSum = LoanRecovery::where('LoanId', $post->id)->where('is_delete', 'No')->sum('principal');

                $principal = round($loanFetch->loanAmount - $receiptSum);

                $total = round($pintr + $principal + $interest + $panelty);
                $totalinterest = round(($principal * $countDays * $loanInterest) / 36500);
                $paneltyInterest = round(($pintr * $countDays * $loanPanelty) / 36500, 0);


                if ($principalTillDate < 1) {
                    // $pintr = 0;
                    // $principalTillDate = 0;
                    // $interest = 0;
                    // $panelty = 0;
                }
                // $totalTillDate = $pintr + $principalTillDate + $interest + $panelty + round($overdues, 0);
                $totalTillDate = $pintr + $principalTillDate + $interest + $panelty;


                //dd($totalTillDate, $pintr, $principalTillDate, $interest, $panelty);
                //dd($interest, $finterest);
                $data['totalprincple'] =   $principal;
                $data['principal']  =  $principalTillDate;
                // $data['principal']  =  round($accruedPrincipal, 2);
                $data['currentintrest'] =  $interest;
                // $data['overdueintrest'] =  round($overdues, 0);
                $data['pendingintrest'] = $pintr;
                $data['penalinrest'] =  $paneltyInterest;
                $data['netintrest'] =   $totalTillDate;

                return response()->json(['status' => "success", 'recovery' => $allrecovery, "data" => $loanFetch, 'loandetails' => $data, 'installmet' => $installmentlist], 200);
                break;
            case 'guarantorupdate':
                $loanFetch = MemberLoan::where("id", $post->loanid)->first();
                if (!$loanFetch) {
                    return response()->json(['status' => "Invlid account number"]);
                }
                $update = MemberLoan::where("id", $post->loanid)->update(['guranter2' => $post->guranter2, 'guranter1' => $post->guranter1]);
                if ($update) {
                    return  response()->json(["status" => "success", "message" => "Guarantor Information Updated"], 200);
                } else {
                    return  response()->json(["status" => "Some technical issue occurred please try again", "message" => "Some technical issue occurred please try again "], 400);
                }
                break;
            case 'deleteloan':

                $loanFetch = MemberLoan::where("id", $post->id)->first();

                if (!$loanFetch) {
                    return response()->json(['status' => "Invlid account number"]);
                }
                $result = $this->isDateBetween(date('Y-m-d', strtotime($loanFetch->loanDate)));

                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
                }
                $receipt = LoanRecovery::where('LoanId', $post->id)->where('is_delete', 'No')->first();

                if ($receipt) {
                    return response()->json(['status' => "Please Detele Recovery First"]);
                } else {
                    DB::beginTransaction();
                    try {
                        DB::table('general_ledgers')->where('serialNo', $loanFetch->serialNo)->delete();
                        DB::table('member_loans')->where("id", $post->id)->delete();
                        DB::table('loan_installments')->where("LoanId", $post->id)->delete();
                        DB::table('member_savings')->where('serialNo', $loanFetch->serialNo)->delete();


                        //  $deteletelegder =  DB::table('general_ledgers')->where('referenceNo', $loanFetch->id)->update([
                        //     'is_delete' => "Yes"
                        //     ]);
                        // $deteleloan =  DB::table('member_loans')->where("id", $post->id)->update(['is_delete' => "Yes"]);


                        $loancode = DB::table('loan_masters')
                            ->select(
                                'loan_masters.*',
                                'ledger_masters.id as ledgerid',
                                'ledger_masters.*'
                            )
                            ->where('loan_masters.id', $post->loanType)
                            ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                            ->first();




                        // if($loancode->loantypess === 'FD'){

                        //     if (!empty($post->schemenames)) {
                        //         foreach ($post->schemenames as $fdid) {
                        //             dd($fdid)    ;
                        //             MemberFdScheme::where('id', $fdid)->update(['status' => "Active"]);
                        //         }
                        //     }
                        // }

                        // if($loancode->loantypess === 'RD'){

                        //     if (!empty($post->schemenames)) {
                        //         foreach ($post->schemenames as $fdid) {
                        //             ReCurringRd::where('id', $fdid)->update(['status' => "Active"]);
                        //         }
                        //     }
                        // }

                        if ($loancode->loantypess === 'FD') {
                            $fdidis = explode(',', $loanFetch->fdId);
                            foreach ($fdidis as $fdidisis) {
                                MemberFdScheme::where('id', $fdidisis)->update(['status' => "Active"]);
                            }
                        }

                        if ($loancode->loantypess === 'RD') {
                            $rdidis = explode(',', $loanFetch->rd_id);
                            foreach ($rdidis as $rdidisis) {
                                DB::table('re_curring_rds')->where('id', $rdidisis)->update(['status' => "Active"]);
                            }
                        }




                        DB::commit();
                        //    $this->loanstatus($loanFetch->id);
                        return response()->json(['status' => "success"]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        //dd($e->getMessage());
                        return response()->json(['status' => "Some Technical issue Occurred"]);
                    }
                }
                break;
            case 'deleteinstallmets':

                $loanRecept = LoanRecovery::where('id', $post->id)->first();
                if (!$loanRecept) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }
                $installmetsIds = explode(",", $loanRecept->instaId);
                $installmets = LoanInstallment::whereIn('id', $installmetsIds)->get();
                $precheck =  DB::table('general_ledgers')->where('referenceNo', $post->id)->where('formName', 'LoanReceipt')->get();
                $result = $this->isDateBetween(date('Y-m-d', strtotime($loanRecept->receiptDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
                }
                if (count($precheck) < 2) {
                    return response()->json(["status" => "Some Technical issue occurred"], 200);
                }
                try {
                    DB::beginTransaction();
                    if (count($installmets) > 0) {
                        foreach ($installmets as $installmet) {
                            LoanInstallment::where('id', $installmet->id)->update([
                                "status" => "False",
                            ]);
                        }
                    }
                    LoanRecovery::where('id', $post->id)->delete();
                    DB::table('general_ledgers')->where('referenceNo', $post->id)->where('formName', 'LoanReceipt')->delete();
                    DB::commit();
                    $loanrecovery =  LoanRecovery::where('loanId', $loanRecept->loanId)->where('is_delete', 'No')->get();
                    $this->loanstatus($loanRecept->loanId);
                    return response()->json(['status' => "success", 'recovery' => $loanrecovery]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    return response()->json(['status' => "Some Technical issue occurred", "message" => "Some Technical issue occurred"], 200);
                }

                break;
            default:
                return response()->json(['status' => "Invlid request type"]);
                break;
        }
    }




    public function getinstallmetslistfd($post)
    {
        $loanDay = date('d', strtotime($post->loandate));
        $emiDay = date('d', strtotime($post->loandate));

        // Set start date and final EMI date
        $startDate = date('Y-m-' . $emiDay, strtotime($post->loandate . '+1 month'));
        $finalemidate = date('Y-m-' . $emiDay, strtotime($startDate . ($loanDay > $emiDay ? ' +1 month' : '')));
        $startDateTime = new DateTime($startDate);
        $emiDateTime = new DateTime($finalemidate);

        // Calculate the difference in days between the dates
        $interval = $startDateTime->diff($emiDateTime);
        $diffInDays = $interval->days;


        // Calculate the advancement days and day interest
        $advancementDay = $diffInDays;
        $dayintrest  = 0;


        $loanAmount = intval($post->loanAmount);
        $dayintrest = ($loanAmount * $advancementDay * $post->intrest) / 36500;


        // Initialize installment data
        $data['Installments'] = 0;

        // Handle installment type logic for "Monthly" and "Daily"

        $installmentsPerYear = 12;
        $data['Installments'] = ($post->year * 12) + $post->month;


        if ($data['Installments'] == 0) {
            throw new \Exception("Number of installments cannot be zero.");
        }

        $data['InstallmentAmount'] = round($loanAmount / $data['Installments'], 2);
        $data['RateofInterest'] = ($post->intrest / $installmentsPerYear) / 100;

        $totalInstallments = $data['Installments'];


        $emiDateTime = new DateTime($post->loandate);


        // Generate and return the installments
        return $installments = $this->generateInstallments($loanAmount, $post->loanInterest, $installmentsPerYear, $totalInstallments, $emiDateTime->format('Y-m-d'), $startDateTime->format('d-m-Y'), $dayintrest);
    }


    public function getinstallmetslist($post)
    {
        // dd($post->all());
        // Fetch loan type from database
        $loanType = LoanMaster::find($post->loanType);
        $loanDay = date('d', strtotime($post->loandate));

        // Determine EMI day based on loan date or loanType setting
        // Determine EMI day
        if ($loanType->emiDate == 0) {
            $emiDay = date('d', strtotime($post->loandate));
            // dd($emiDay);
        } else {
            $emiDay = $loanType->emiDate;
        }


        // Validate emiDay
        // if (!is_numeric($emiDay) || $emiDay < 1 || $emiDay > 31) {
        //     throw new Exception("Invalid EMI day: $emiDay");
        // }

        // Pad emiDay with leading zero if needed
        $emiDay = str_pad($emiDay, 2, '0', STR_PAD_LEFT);

        // Get loanDay from loandate
        $loanDay = date('d', strtotime($post->loandate));

        // Calculate start date (EMI starts from next month)
        $startDate = date('Y-m-' . $emiDay, strtotime($post->loanDate . ' +1 month'));
        // dd($startDate);

        // Calculate final EMI date
        $finalemidate = date(
            'Y-m-' . $emiDay,
            strtotime($startDate . ($loanDay > $emiDay ? ' +1 month' : ''))
        );

        // dd([
        //     'emiDay' => $emiDay,
        //     'startDate' => $startDate,
        //     'finalemidate' => $finalemidate,
        // ]);


        $startDateTime = new DateTime($startDate);
        $emiDateTime = new DateTime($finalemidate);

        // Calculate the difference in days between the dates
        $interval = $startDateTime->diff($emiDateTime);
        $diffInDays = $interval->days;

        // Day difference logic based on loan type
        $daydiff = ($loanType->advancementDate == "Yes" && $loanType->recoveryDate == "Yes") ? 1 : -1;

        // Calculate the advancement days and day interest
        $advancementDay = $diffInDays;
        $dayintrest  = 0;
        $interestRate = $post->intrest ? $post->intrest : $post->loanInterest;
        $loanYear = $post->loanYear ? $post->loanYear : $post->year;
        $loanMonth = $post->loanMonth ? $post->loanMonth : $post->month;
        $loanAmount = 0;

        if ($loanType->advancementDate == "Not Applicable") {
            $emiDateTime = new DateTime($finalemidate);
        } else if ($loanType->advancementDate == "Loan Date") {
            $emiDateTime = new DateTime($startDate);
        } else {
            $loanAmount = intval($post->amount) ? intval($post->amount) : intval($post->loanAmount);
            // dd($post->all());
            $dayintrest = ($loanAmount * $advancementDay * $interestRate) / 36500;
        }

        // Initialize installment data
        $data['Installments'] = 0;
        // dd($loanType->insType);
        // Handle installment type logic for "Monthly" and "Daily"
        if ($loanType->insType == "Monthly") {
            $installmentsPerYear = 12;
            $data['Installments'] = ($loanYear * 12) + $loanMonth;
        } elseif ($loanType->insType == "Daily") {
            $installmentsPerYear = 365;
            $data['Installments'] = ($loanYear * 365) + ($loanMonth * 30) + ($loanType->days);


            // Roughly convert months to days
            // dd($post->loanYear,$post->loanMonth,$loanType->days);

        } else {
            throw new \Exception("Invalid installment type.");
        }


        // if ($data['Installments'] == 0) {
        //     throw new \Exception("Number of installments cannot be zero.");
        // }


        $data['InstallmentAmount'] = round($loanAmount / $data['Installments'], 2);
        $data['RateofInterest'] = ($post->loanInterest / $installmentsPerYear) / 100;

        $totalInstallments = $data['Installments'];

        if ($loanType->emiDate == 0) {
            $emiDateTime = new DateTime($post->loandate);
        }

        // Generate and return the installments
        return $installments = $this->generateInstallments($loanAmount, $interestRate, $installmentsPerYear, $totalInstallments, $emiDateTime->format('Y-m-d'), $startDateTime->format('d-m-Y'), $dayintrest);
    }


    // public function getinstallmetslist($post)
        // {
        //     $loanType = LoanMaster::find($post->loanType);
        //     $loanDay =  date('d', strtotime($post->loandate));
        //     if($loanType->emiDate == "0"){
        //          $emiDay =  date('d', strtotime($post->loandate));
        //     }else{
        //        $emiDay = $loanType->emiDate;
        //     }

        //     $startDate = date('Y-m-' . $emiDay, strtotime($post->loandate . '+1 month'));
        //     $finalemidate = date('Y-m-' . $emiDay, strtotime($startDate . ($loanDay > $emiDay ? ' +1 month' : '')));
        //     $startDateTime = new DateTime($startDate);
        //     $emiDateTime = new DateTime($finalemidate);

        //     $interval = $startDateTime->diff($emiDateTime);
        //     $diffInDays = $interval->days;

        //     $daydiff = ($loanType && $loanType->advancementDate == "Yes" && $loanType->recoveryDate == "Yes") ? 1 : (($loanType && $loanType->advancementDate == "No" && $loanType->recoveryDate == "No") ? -1 : 0);

        //     $advancementDay = $diffInDays; //+ $daydiff;
        //     $dayintrest  = 0;
        //     if ($loanType->advancementDate == "Not Applicable") {
        //         $emiDateTime = new DateTime($finalemidate);
        //     } else if ($loanType->advancementDate == "Loan Date") {
        //         $emiDateTime = new DateTime($startDate);
        //     } else {


        //         $dayintrest = ($loanAmount = intval($post->loanAmount)) * $advancementDay * $post->intrest / 36500;
        //     }
        //     $data['Installments'] = 0;
        //     $installmentsPerYear = ($loanType->insType == "Monthly") ? 12 : 2;
        //     $data['Installments'] = ($loanType->insType == "Monthly") ? ($post->year * 12) + $post->month : (($loanType->insType == "Half Yearly") ? ($post->year * 2) + $post->month / 6 : 0);
        //     $data['InstallmentAmount'] = round($loanAmount / $data['Installments'], 2); //1045
        //     $data['RateofInterest'] = ($post->intrest / $installmentsPerYear) / 100;

        //     $totalInstallments = $data['Installments'];

        //     if($loanType->emiDate == "0"){
        //         $emiDateTime = new DateTime($post->loandate);
        //     }
        //     return $installments = $this->generateInstallments($loanAmount, $post->intrest, $installmentsPerYear, $totalInstallments, $emiDateTime->format('Y-m-d'), $startDateTime->format('d-m-Y'), $dayintrest);
    // }

    public function calculateReducingInterest($principal, $annualInterestRate, $installmentsPerYear, $monthsSinceLastInstallment)
    {
        // Convert annual interest rate to monthly rate
        $monthlyInterestRate = $annualInterestRate / $installmentsPerYear / 100;

        // Calculate interest using the reducing balance formula
        $interest = $principal * $monthlyInterestRate;

        return $interest;
    }

    function generateInstallments($loanAmount, $annualInterestRate, $installmentsPerYear, $totalInstallments, $startDate, $advancementDate, $dayintrest)
    {
        // dd($advancementDate);
        $installments = [];

        // If there's accrued daily interest before the first installment, add it
        if ($dayintrest != 0) {
            $installments[] = [
                'installment' => 0,
                'installment_date' => Carbon::parse($advancementDate)->format('d-m-Y'),
                'opening_balance' => round($loanAmount),
                'principal' => 0,
                'interest' => round($dayintrest),
                'total' => round($dayintrest),
                'remaining_balance' => round($loanAmount),
            ];
        }

        // Calculate installment settings
        $gaps = ($installmentsPerYear == 2) ? 6 : 1;
        $monthlyInterestRate = $annualInterestRate / $installmentsPerYear / 100;

        // EMI formula (standard amortized loan formula)
        $monthlyInstallment = $loanAmount * (
            $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalInstallments)
        ) / (
            pow(1 + $monthlyInterestRate, $totalInstallments) - 1
        );

        // Start generating reducing balance installments
        $currentDate = Carbon::parse($advancementDate);
        $remainingLoan = $loanAmount;

        for ($i = 1; $i <= $totalInstallments; $i++) {
            $dueDate = $currentDate->copy()->addMonths(($i - 1) * $gaps);

            $interest = $remainingLoan * $monthlyInterestRate;
            $principal = $monthlyInstallment - $interest;

            $installments[] = [
                'installment' => $i,
                'installment_date' => $dueDate->format('d-m-Y'),
                'opening_balance' => round($remainingLoan),
                'principal' => round($principal),
                'interest' => round($interest),
                'total' => round($monthlyInstallment),
                'remaining_balance' => round(max($remainingLoan - $principal, 0)),
            ];

            $remainingLoan -= $principal;
        }

        return $installments;
    }
    public function insertInstallments($installments, $loanId)
    {
        dd($installments);
        $check =  DB::table('loan_installments')->where('LoanId', $loanId)->first();
        // dd($installments);
        if (!$check) {
            foreach ($installments as $installment) {
                // dd(date('Y-m-d', strtotime($installment['installment_date'])));
                DB::table('loan_installments')->insert([
                    "LoanId" => $loanId,
                    "installmentDate" => date('Y-m-d', strtotime($installment['installment_date'])),
                    'principal' => $installment['principal'],
                    'interest' => $installment['interest'],
                    'total' => $installment['total'],
                ]);
            }
        }
    }

    public function updateInstallments($installments, $loanId)
    {
        DB::table('loan_installments')->where('LoanId', $loanId)->delete();
        foreach ($installments as $installment) {
            DB::table('loan_installments')->insert([
                "LoanId" => $loanId,
                "installmentDate" => date('Y-m-d', strtotime($installment['installment_date'])),
                'principal' => $installment['principal'],
                'interest' => $installment['interest'],
                'total' => $installment['total'],
            ]);
        }
    }

    public function loanstatus($loanid)
    {
        $allrecovery =  LoanRecovery::where('loanId', $loanid)->where('is_delete', 'No')->sum('principal');
        $memberloan = MemberLoan::where(['id' => $loanid])->first();
        // dd($memberloan);
        if ($allrecovery >= $memberloan->loanAmount) {
            $memberloan->status = "Closed";
            $memberloan->save();
        } else if ($allrecovery < $memberloan->loanAmount) {
            $memberloan->status = "Disbursed";
            $memberloan->save();
        }
    }



    public function getdailyloanaccount(Request $post)
    {

        $acloan = MemberLoan::where('accountNo', $post->loanAcNo)
            ->where('memberType', $post->member)
            // ->where('installmentType', '=', 'Daily')
            ->orderBy('id', 'desc')
            ->get();


        if ($acloan->isNotEmpty()) {
            $loanTransactionSummary = [];
            foreach ($acloan as $loan) {
                $totreco = dailyrcovery::where('loanid', '=', $loan->id)
                    ->sum('transactionamount');
                $allrecoveredLoanRecovery = LoanRecovery::where('loanId', '=', $loan->id)->sum('total');
                $emiinstatotal = LoanInstallment::where('LoanId', '=', $loan->id)->sum('total');
                $loanData = $loan->toArray();
                $loanData['totalTransactionAmount'] = $totreco;
                $loanData['totalTransferredAmount'] = $allrecoveredLoanRecovery;
                $loanData['emiinstatotal'] = $emiinstatotal;
                $loanTransactionSummary[] = $loanData;
            }
            return response()->json(['status' => "success", 'data' => $loanTransactionSummary]);
        }
        return response()->json(['status' => "Loan Account not found"]);
    }







    public function getdailyloanperday(Request $request)
    {

        if ($request->amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid Amount']);
        }

        $existloanid = MemberLoan::find($request->loanid);


        if (!$existloanid) {
            return response()->json(['success' => false, 'message' => 'Invalid Loan Id']);
        }
        $emiinstatotal = LoanInstallment::where('LoanId', '=', $request->loanid)->sum('total');

        $totreco = dailyrcovery::where('loanid', '=', $request->loanid)->sum('transactionamount');

        if ($emiinstatotal < $totreco + $request->amount) {

            return response()->json(['success' => false, 'message' => 'Amount is greater  the expected']);
        }

        $srnnnn = "daily" . time();

        if ($request->paytype == 'C002') {
            $groupCode = $request->paytype;
            $ledgerCode = $request->paytype;
        } else {
            $groupCode = $request->paytype;
            $ledgerCode = $request->bank;
        }


        DB::beginTransaction();

        try {

            $enter = new dailyrcovery();
            $enter->serialNo = $srnnnn;
            $enter->loanid = $request->loanid;
            $enter->accountId = $existloanid->accountId;
            $enter->accountNo = $existloanid->accountNo;
            $enter->memberType = $existloanid->memberType;
            $enter->groupCode = $groupCode;
            $enter->ledgerCode = $ledgerCode;
            $enter->recoverydate = date('Y-m-d', strtotime($request->installdate));
            $enter->transactionamount = $request->amount;
            $enter->penaltyamount = $request->panelty;
            $enter->transfered = 'no';
            $enter->branchId = session('branchId') ? session('branchId') : 1;
            $enter->sessionId = session('sessionId') ? session('sessionId') : 1;
            $enter->agentId = $request->agentId;
            $enter->updatedBy = $request->user()->id;
            $enter->save();


            DB::table('general_ledgers')->insert([
                "serialNo" => $srnnnn,
                'accountNo' => $existloanid->accountNo,
                "accountId"  =>  $existloanid->accountId,
                'memberType' =>  $existloanid->memberType,
                'agentId' => $request->user()->id,
                "ledgerCode"   => $ledgerCode,
                'groupCode' =>  $groupCode,
                'referenceNo' => $enter->id,
                'entryMode' => "manual",
                "formName" => "Dailyloanrecovery",
                'transactionDate' => date('Y-m-d', strtotime($request->installdate)),
                'transactionType' => 'Dr',
                'transactionAmount' => $request->amount,
                'narration' => '',
                'branchId' =>  session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updatedBy' => $request->user()->id,
            ]);



            DB::table('general_ledgers')->insert([
                "serialNo" => $srnnnn,
                'accountNo' => $existloanid->accountNo,
                "accountId"  =>  $existloanid->accountId,
                'memberType' =>  $existloanid->memberType,
                'agentId' => $request->user()->id,
                "groupCode"   => 'GRTDAI01',
                'ledgerCode' =>  'DAI02',
                'referenceNo' => $enter->id,
                'entryMode' => "manual",
                "formName" => "Dailyloanrecovery",
                'transactionDate' => date('Y-m-d', strtotime($request->installdate)),
                'transactionType' => 'Cr',
                'transactionAmount' => $request->amount,
                'narration' => '',
                'branchId' =>  session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updatedBy' => $request->user()->id,
            ]);




            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Loan details updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! Please try again.'
            ]);
        }
    }


    public function transferloanaccountreceived(Request $request)
    {

        $allrecovered = LoanRecovery::where('loanId', '=', $request->id)->sum('total');
        $amount = dailyrcovery::where('loanid', '=', $request->id)->sum('transactionamount');

        $aa = $amount - $allrecovered;
        return response()->json(['success' => true, 'amount' => $aa]);
    }


    public function getdailytransfer(Request $request)
    {
        $loanaccount =  MemberLoan::where('id', $request->loanid)->first();
        if (!$loanaccount) {
            return response()->json(["status" => "Some Technical issue occurred"], 200);
        }
        if ($request->amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid Amount']);
        }
        $allrecoveredLoanRecovery = LoanRecovery::where('loanId', '=', $request->loanid)->sum('total');
        $amountdailyrcovery = dailyrcovery::where('loanid', '=', $request->loanid)->sum('transactionamount');
        $checkamount = $amountdailyrcovery - $allrecoveredLoanRecovery;
        $availableamount = dailyrcovery::where('loanid', '=', $request->loanid)->where('transfered', '=', 'no')->sum('transactionamount');

        $principalamountrecovery = LoanRecovery::where('loanId', '=', $request->loanid)->sum('principal');
        $interestuntrecovery = LoanRecovery::where('loanId', '=', $request->loanid)->sum('interest');

        // return response()->json(['success' => false,'message' => $availableamount]);

        if ($checkamount < $request->amount) {
            return response()->json(['success' => false, 'message' => 'Amount is greater  the expected']);
        }

        $instaall = LoanInstallment::where('LoanId', '=', $request->loanid)->get();
        if (!$instaall) {
            return response()->json(['success' => false, 'message' => 'Technical issue']);
        }
        $paymentis = $request->amount + $principalamountrecovery + $interestuntrecovery;
        $intrestis = 0;
        $principleis = 0;
        $InstallmentIds  = [];
        foreach ($instaall as $instaallllisst) {
            if ($paymentis >= $instaallllisst->interest + $instaallllisst->principal) {
                $paymentis = $paymentis - $instaallllisst->interest;
                $intrestis = $intrestis + $instaallllisst->interest;
                $paymentis = $paymentis - $instaallllisst->principal;
                $principleis = $principleis + $instaallllisst->principal;
                LoanInstallment::where('id', $instaallllisst->id)->update([
                    "status" => "True",
                    "paid_date" => date('Y-m-d', strtotime($request->installdate)),
                    "re_amount" =>  $instaallllisst->principal + $instaallllisst->interest
                ]);

                $InstallmentIds[]  =  $instaallllisst->id;
            } else {
                LoanInstallment::where('id', $instaallllisst->id)->update([
                    "status" => "Partial",
                    "paid_date" => date('Y-m-d', strtotime($request->installdate)),
                    "re_amount" => $paymentis,
                ]);
                $InstallmentIds[]  =  $instaallllisst->id;
                $principleis = $principleis + $paymentis;
                $paymentis = $paymentis - $paymentis;
                break;
            }
        }


        $principleis = $principleis - $principalamountrecovery;
        $intrestis = $intrestis - $interestuntrecovery;


        $InstallmentId = implode(',', $InstallmentIds);
        $loan =  LoanRecovery::create([
            'loanId' => $request->loanid,
            'receiptDate' => date('Y-m-d', strtotime($request->installdate)),
            'principal' =>  $principleis,
            'interest' => $intrestis,
            'pendingInterest' => 0,
            'penalInterest' => 0,
            'total' => $principleis + $intrestis,
            'receivedAmount' => $principleis + $intrestis,
            "overDueInterest" => 0,
            'status' => "True",
            'entry_mode' => "automatic",
            "receivedBy" => 'Transfer',
            "branchId"   => session('branchid') ?? 1,
            'sessionId' => session('sessionId') ?? 1,
            'instaId' => $InstallmentId,
            'updatedBy' => $request->user()->id,
        ]);



        $generalLedgers = "srno" . time();


        DB::table('general_ledgers')->insert([
            "serialNo" => $generalLedgers,
            "accountId"  =>  $loanaccount->accountId,
            'accountNo' => $loanaccount->accountNo,
            'memberType' => $loanaccount->memberType,
            'agentId' => $request->user()->id,
            "ledgerCode"   => 'DAI02',
            'groupCode' => 'GRTDAI01',
            'referenceNo' => $loan->id,
            'entryMode' => "automatic",
            'transactionDate' => date('Y-m-d', strtotime($request->installdate)),
            'transactionType' => 'Dr',
            "formName"        => "Transfer",
            'transactionAmount' => $request->amount,
            'narration' => "Transfer",
            'branchId' =>  session('branchid') ?? 1,
            'sessionId' => session('sessionId') ?? 1,
            'updatedBy' => $request->user()->id,
        ]);



        $intGroupCode = "INCM001";


        if ($loanaccount->memberType == "Member") {
            $pricpleCode = "LONM001";
            $curentintCode = 'LONM002';
            $penalCode = "LONM003";
            $pendingCode = "LONM004";
        } else if ($loanaccount->memberType == "NonMember") {
            $pricpleCode = "LONN001";
            $curentintCode = 'LONN002';
            $penalCode = "LONN003";
            $pendingCode = "LONN004";
        } else if ($loanaccount->memberType == "Staff") {
            $pricpleCode = "LONS001";
            $curentintCode = 'LONS002';
            $penalCode = "LONS003";
            $pendingCode = "LONS004";
        }


        $insert = [
            "serialNo" => $generalLedgers,
            "accountId"  =>  $loanaccount->accountId,
            'accountNo' => $loanaccount->accountNo,
            'memberType' => $loanaccount->memberType,
            'agentId' => $request->agentId,
            'referenceNo' => $loan->id,
            'entryMode' => "automatic",
            'transactionDate' => date('Y-m-d', strtotime($request->installdate)),
            'transactionType' => 'Cr',
            "formName"        => "Transfer",
            'narration' => "Transfer",
            'branchId' =>  session('branchid') ?? 1,
            'sessionId' => session('sessionId') ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updatedBy' => $request->user()->id,
        ];
        /*   insert penal  interest transaction */
        if ($intrestis > 0) {

            $insert["ledgerCode"]  = $curentintCode;
            $insert["groupCode"]  = $intGroupCode;
            $insert['transactionAmount'] = $intrestis;
            DB::table('general_ledgers')->insert($insert);
        }

        /*   insert princple transaction */
        if ($principleis > 0) {

            $insert["ledgerCode"]  = 'LONM001';
            $insert["groupCode"]  = 'LONM001';
            $insert['transactionAmount'] = $principleis;
            DB::table('general_ledgers')->insert($insert);
        }


        $allrecovered = LoanRecovery::where('loanId', '=', $request->loanid)->sum('total');


        $dailyrcoveryUpdated = dailyrcovery::where('loanid', '=', $request->loanid)
            ->where('transfered', '=', 'yes')
            ->update(['transfered' => 'no']);
        $dailyrcget = dailyrcovery::where('loanid', '=', $request->loanid)->get();
        foreach ($dailyrcget as $dailyrcgetlist) {
            if ($allrecovered >= $dailyrcgetlist->transactionamount) {
                dailyrcovery::where('loanid', '=', $request->loanid)
                    ->where('id', '=', $dailyrcgetlist->id)
                    ->update(['transfered' => 'yes']);
                $allrecovered = $allrecovered - $dailyrcgetlist->transactionamount;
            } else {
                break;
            }
        }
        return response()->json(['success' => true, 'message' => 'Transfered']);
    }


    public function getdailyloanaccountreceived(Request $request)
    {

        $dailyrcoverydata = dailyrcovery::where('loanid', '=', $request->id)->get();

        if (sizeof($dailyrcoverydata) > 0) {
            return response()->json(['success' => true, 'message' => $dailyrcoverydata]);
        } else {
            return response()->json(['success' => false, 'message' => 'Amount is greater  the expected']);
        }
    }
    public function checkLoanNo(Request $request)
    {
        $loanAcNo = $request->input('loanAcNo');

        $loan = MemberLoan::where('loanAcNo', $loanAcNo)->first();

        if ($loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan account number already exists.'
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Loan account number is available.'
            ]);
        }
    }
    public function checkPernoteNo(Request $request)
    {
        $PernoteNo = $request->PernoteNo;

        $pernote = MemberLoan::where('pernote', $PernoteNo)->first();
        // dd($pernote);
        if ($pernote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pernote number already exists.'
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Pernote number is available.'
            ]);
        }
    }
}
