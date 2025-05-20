<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\MemberAccount;
use App\Models\SessionMaster;
use App\Models\MemberShare;
use App\Models\AgentMaster;
use App\Models\Contribution;
use App\Models\GeneralLedger;
use App\Models\LedgerMaster;
use Illuminate\Support\Facades\DB;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Session;
use App\Models\GroupMaster;
use Illuminate\Support\Facades\Validator;

class ContributionController extends Controller
{
    public function index()
    {
        $data['title'] = "Contribution";
        // $data['agents'] = AgentMaster::get();
        $data['contribution'] = Contribution::get();
        $data['groups'] = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        // dd($data['shares']);
        return view('transaction.contribution')->with($data);
    }
    public function transaction(Request $post)
    {
        //  dd($post->all());
        switch ($post->actiontype) {
            case 'getdata':

                if ($post->actiontype === 'deposit' || $post->actiontype === 'withdrawal') {
                    $acdetails = MemberAccount::where('accountNo', $post->account)->where("memberType", $post->membertype)->first('name');

                    if (!$acdetails) {
                        return response()->json(['status' => "Invalid Account number"]);
                    }

                    $SessionMaster = SessionMaster::find(Session::get('sessionId'));

                    $txnacdetails = Contribution::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->whereBetween('transactionDate', [$SessionMaster->startDate, $SessionMaster->endDate])
                        ->orderBy('transactionDate', 'asc')
                        ->get();




                    $txnacdetailsamount = Contribution::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->where('transactionDate', '<=', $SessionMaster->startDate)
                        ->orderBy('transactionDate', 'asc')
                        ->get();


                    // Calculate the sum of depositAmount minus withdrawAmount
                    $totalBalance = $txnacdetailsamount->sum(function ($detail) {
                        return $detail->depositAmount - $detail->withdrawAmount;
                    });

                    $sessionenddate = $SessionMaster->endDate;
                    $openingBal = DB::table('member_accounts')
                        ->where('accountNo', '=', $post->account)
                        // ->where('accType','Share')
                        ->first();

                    return response()->json(['status' => "success", "sessionenddate" => $sessionenddate, "acdetails" => $acdetails, "totalBalance" => $totalBalance, "openingBal" => $openingBal, 'txndetails' => $txnacdetails, 'balance' => $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)))]);
                } else {

                    $acdetails = MemberAccount::where('accountNo', $post->account)->where("memberType", $post->membertype)->first('name');
                    if (!$acdetails) {
                        return response()->json(['status' => "Invalid Account number"]);
                    }

                    $SessionMaster = SessionMaster::find(Session::get('sessionId'));
                    $txnacdetails = Contribution::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->whereBetween('transactionDate', [$SessionMaster->startDate, $SessionMaster->endDate])
                        ->orderBy('transactionDate', 'asc')
                        ->get();
                    // dd($txnacdetails);


                    $txnacdetailsamount = Contribution::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->where('transactionDate', '<=', $SessionMaster->startDate)
                        ->orderBy('transactionDate', 'asc')
                        ->get();


                    // Calculate the sum of depositAmount minus withdrawAmount
                    $totalBalance = $txnacdetailsamount->sum(function ($detail) {
                        return $detail->depositAmount - $detail->withdrawAmount;
                    });

                    $sessionenddate = $SessionMaster->endDate;
                    $openingBal = DB::table('member_accounts')
                        ->where('accountNo', '=', $post->account)
                        // ->where('accType','Share')
                        ->first();
                    // dd($openingBal);

                    return response()->json([
                        'status' => "success",
                        "sessionenddate" => $sessionenddate,
                        "acdetails" => $acdetails,
                        "totalBalance" => $totalBalance,
                        "openingBal" => $openingBal,
                        'txndetails' => $txnacdetails,
                        'balance' => $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate))),
                        // 'saving_account' => $saving_account
                    ]);
                }

                break;
            case 'contributionsave':
                $rules = array(
                    'action' => 'required',
                    'account' => 'required',
                );
                $validator = Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $acdetails = MemberAccount::where('accountNo', $post->account)->first(['name', 'id', 'openingDate']);
                if (!$acdetails) {
                    return response()->json(['status' => "Invalid Account number"]);
                }

                if (date('Y-m-d', strtotime($post->transactionDate)) < $acdetails->openingDate) {
                    return response()->json([
                        'status' => 'Transaction date can not be less than account opening date',
                        'message' => 'Transaction date can not be less than account opening date'
                    ], 400);
                }

                do {
                    $generalLedgers = "Contribution" . rand(1111111, 9999999);
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);
                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
                }
                if ($post->action == "withdrawal") {

                    $balance =  $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)));
                    if ($balance == $post->amount) {
                        return response()->json(['status' => "Insufficient Balance", "message" => "Insufficient Balance"], 200);
                    }
                    $ldgerid = LedgerMaster::where('id', 1)
                        ->first();

                    DB::beginTransaction();
                    try {

                        $lastInsertedId = DB::table('contributions')->insertGetId([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' =>  $post->memberType,
                            "groupCode"   => "CON001",
                            'ledgerCode' => "CON001",
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            "transactionType"   => "Withdraw",
                            'depositAmount' => 0,
                            'withdrawAmount' => $post->amount,
                            "dividendAmount"   => 0,
                            'chequeNo' => "",
                            'narration' => $post->naraton,
                            "branchId"   => session('bramchid') ?? 1,
                            // 'agentId' => $post->agentId,
                            'sessionId' => session('sessionId') ?? 1,
                            "updatedBy"   => $post->user()->id,
                        ]);
                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => $post->memberType,
                            // 'agentId' => $post->agentId,
                            'ledgerCode' => $post->bank,
                            'groupCode' => $post->groupCode,
                            'formName'   => "Contributions",
                            'referenceNo' => $lastInsertedId,
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            'transactionType' => 'Cr',
                            'transactionAmount' => $post->amount,
                            'narration' => $post->naration,
                            'branchId' =>  session('bramchid') ?? 1,
                            'sessionId' => session('sessionId') ?? 1,
                            'updatedBy' => $post->user()->id,
                        ]);

                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' =>  $post->memberType,
                            // 'agentId' => $post->agentId,
                            'groupCode' => "CON001",
                            'ledgerCode' => "CON001",
                            'formName'   => "Contributions",
                            'referenceNo' => $lastInsertedId,
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            'transactionType' => 'Dr',
                            'transactionAmount' => $post->amount,
                            'narration' => $post->naration,
                            'branchId' =>  session('bramchid') ?? 1,
                            'sessionId' => session('sessionId') ?? 1,
                            'updatedBy' => $post->user()->id,
                        ]);
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        // dd($e->getMessage());
                        return response()->json(['status' => "failed", "message" => "Some Technical issue occurred"], 200);
                    }
                } else {
                    // dd($post->all());
                    DB::beginTransaction();
                    try {
                        $lastInsertedId = DB::table('contributions')->insertGetId([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => 'Member',
                            'groupCode' => "CON001",
                            'ledgerCode' => "CON001",
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            "transactionType"   => "Deposit",
                            'depositAmount' =>  $post->amount,
                            'withdrawAmount' => 0,
                            "dividendAmount"   => 0,
                            'chequeNo' => "",
                            'narration' => $post->naration,
                            "branchId"   => session('bramchid') ?? 1,
                            // 'agentId' => $post->agentId,
                            'sessionId' => session('sessionId') ?? 1,
                            "updatedBy"   => $post->user()->id,
                        ]);
                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => 'Member',
                            // 'agentId' => $post->agentId ?? 1,
                            'ledgerCode' => $post->bank,
                            'groupCode' => $post->groupCode,
                            'referenceNo' => $lastInsertedId,
                            'formName'   => "Contributions",
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            'transactionType' => 'Dr',
                            'transactionAmount' => $post->amount,
                            'narration' => $post->naration,
                            'branchId' =>  session('bramchid') ?? 1,
                            'sessionId' => session('sessionId') ?? 1,
                            'updatedBy' => $post->user()->id,
                        ]);

                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => 'Member',
                            'groupCode' => "CON001",
                            'ledgerCode' => "CON001",
                            'formName'   => "Contributions",
                            'referenceNo' => $lastInsertedId,
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            'transactionType' => 'Cr',
                            'transactionAmount' => $post->amount,
                            'narration' => $post->naration,
                            'branchId' =>  session('bramchid') ?? 1,
                            'sessionId' => session('sessionId') ?? 1,
                            'updatedBy' => $post->user()->id,
                        ]);
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        dd($e->getMessage());
                        return response()->json(['status' => "success", "message" => "Some Technical issue occurred"], 200);
                    }
                }
                return response()->json(['status' => "success", 'message' => "Successfully data inserted"], 200);
                break;
            case 'updatecontribution':
                // dd($post->all());
                $rules = array(
                    'id' => 'required',
                    'amount' => 'required',
                    'transactionDate' => 'required',
                    'action' => 'required',
                );
                // dd($post->all());
                $validator = Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $check = DB::table('contributions')->where('id', $post->id)->first();
                if (!$check) {
                    return response()->json(['status' => "Invalid Transaction", "message" => "Invalid Transaction"], 200);
                }
                if ($post->action == "Withdraw") {
                    if ($this->getbalance($check->accountNo, date('Y-m-d', strtotime($post->transactionDate))) < $post->amount || $this->getbalance($check->accountNo, date('Y-m-d', strtotime($post->transactionDate))) == $post->amount) {
                        return response()->json(['status' => "Insufficient Balance", "message" => "Insufficient Balance"], 200);
                    }
                    $depositAmount = 0;
                    $withdrawAmount = $post->amount;
                } else {
                    $depositAmount =  $post->amount;
                    $withdrawAmount = 0;
                }
                DB::beginTransaction();
                try {
                    $lastInsertedId = DB::table('contributions')->where('id', $check->id)->update([
                        'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                        "transactionType"   => $post->type,
                        'depositAmount' =>  $depositAmount,
                        'withdrawAmount' => $withdrawAmount,
                        'narration' => $post->naraton,
                        "updatedBy"   => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->where([
                        'accountNo' =>  $check->accountNo,
                        'referenceNo' => $check->id,
                        'groupCode' => $check->groupCode,
                        'ledgerCode' => $check->ledgerCode,
                        'transactionType' => 'Cr'
                    ])->update([
                        'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                        // 'transactionType' => $txntype,
                        'transactionAmount' => $post->amount,
                        'narration' => $post->narration,
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::table('general_ledgers')->where([
                        'accountNo' =>  $check->accountNo,
                        'referenceNo' => $check->id,
                        'groupCode' => "C002",
                        'ledgerCode' => "C002",
                        'transactionType' => 'Dr'
                    ])->update([
                        'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                        // 'transactionType' => $txntype,
                        'transactionAmount' => $post->amount,
                        'narration' => $post->narration,
                        'updatedBy' => $post->user()->id,
                    ]);

                    DB::commit();
                    return response()->json(['status' => "success", "message" => "Transaction updated successfully"], 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    return response()->json(['status' => "Some Technical issue occurred", "message" => "Some Technical issue occurred"], 200);
                }
                break;
            case 'deletecontribution':
                // dd($post->all());
                $action = DB::table('contributions')->where('id', $post->id)->first();
                // dd($action);
                if (!$action) {
                    return response()->json(['status' => "failed", "message" => "Transaction not found"], 200);
                }

                $result = $this->isDateBetween(date('Y-m-d', strtotime($action->transactionDate)));
                if (!$result) {
                    return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
                }
                DB::beginTransaction();
                try {
                    GeneralLedger::where([
                        'referenceNo' => $action->id,
                        'serialNo' => $action->serialNo,
                        //'transactionType' => 'Dr'
                    ])->delete();

                    GeneralLedger::where([
                        'referenceNo' => $action->id,
                        'serialNo' => $action->serialNo,
                        // 'transactionType' => 'Cr'
                    ])->delete();

                    Contribution::where('id', $post->id)->delete();
                    DB::commit();
                    return response()->json(['status' => "success", "message" => "Transaction deleted successfully"], 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    dd($e->getMessage());
                    return response()->json(['status' => "Some Technical issue occurred", "message" => "Some Technical issue occurred"], 200);
                }
                break;
            case 'getaccount':
                $acdetails = MemberAccount::where('accountNo', 'like', "%" . $post->search . "%")->limit(5)->get(['accountNo']);
                return response()->json(['status' => 'success', "data" => $acdetails], 200);
                break;
            default:

                break;
        }
    }
    public function getbalance($ac, $lastDate)
    {
        $openingBal = DB::table('member_accounts')->where('accountNo', $ac)->first();
        $contributionBal = $openingBal->opening_amount ?? 0;
        $credit =  Contribution::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Deposit')->whereDate('transactionDate', '<=', $lastDate)->sum("depositAmount");
        $debit =  Contribution::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Withdraw')->whereDate('transactionDate', '<=', $lastDate)->sum("withdrawAmount");
        return $contributionBal + $credit - $debit;
    }
}
