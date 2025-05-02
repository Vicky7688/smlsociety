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
use App\Models\GeneralLedger;
use App\Models\LedgerMaster;
use Illuminate\Support\Facades\DB;
use App\Models\MemberSaving;
use Session;
use App\Models\GroupMaster;
class ShareController extends Controller
{
    public function index()
    {
        $data['title'] = "Share";
        // $data['agents'] = AgentMaster::get();
        $data['shares'] = MemberShare::get();
        $data['groups'] = GroupMaster::whereIn('groupCode', ['C002', 'BANK001'])->get();
        // dd($data['shares']);
        return view('transaction.share')->with($data);
    }

    public function transaction(Request $post)
    {
        // dd($post->all());
        switch ($post->actiontype) {
            case 'getdata':

                if($post->actiontype === 'deposit' || $post->actiontype === 'withdrawal'){
                    $acdetails = MemberAccount::where('accountNo', $post->account)->where("memberType", $post->membertype)->first('name');

                    if (!$acdetails) {
                        return response()->json(['status' => "Invalid Account number"]);
                    }

                    $SessionMaster =SessionMaster::find(Session::get('sessionId'));
                    $txnacdetails = MemberShare::where('accountNo', $post->account)
                        ->where('is_delete', 'No')->whereBetween('transactionDate', [$SessionMaster->startDate, $SessionMaster->endDate])
                        ->orderBy('transactionDate', 'asc')
                        ->get();


                    $txnacdetailsamount = MemberShare::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->where('transactionDate', '<=',$SessionMaster->startDate)
                        ->orderBy('transactionDate', 'asc')
                        ->get();


                    // Calculate the sum of depositAmount minus withdrawAmount
                    $totalBalance = $txnacdetailsamount->sum(function ($detail) {
                        return $detail->depositAmount - $detail->withdrawAmount;
                    });

                    $sessionenddate=$SessionMaster->endDate;
                    $openingBal = DB::table('member_accounts')
                        ->where('accountNo','=',$post->account)
                        // ->where('accType','Share')
                        ->first();

                    return response()->json(['status' => "success", "sessionenddate" => $sessionenddate, "acdetails" => $acdetails, "totalBalance"=>$totalBalance, "openingBal"=>$openingBal,'txndetails' => $txnacdetails, 'balance' => $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)))]);

                }else{
                    $acdetails = MemberAccount::where('accountNo', $post->account)->where("memberType", $post->membertype)->first('name');
                    if (!$acdetails) {
                        return response()->json(['status' => "Invalid Account number"]);
                    }

                    $SessionMaster =SessionMaster::find(Session::get('sessionId'));
                    $txnacdetails = MemberShare::where('accountNo', $post->account)
                        ->where('is_delete', 'No')->whereBetween('transactionDate', [$SessionMaster->startDate, $SessionMaster->endDate])
                        ->orderBy('transactionDate', 'asc')
                        ->get();

                    $txnacdetailsamount = MemberShare::where('accountNo', $post->account)
                        ->where('is_delete', 'No')
                        ->where('transactionDate', '<=',$SessionMaster->startDate)
                        ->orderBy('transactionDate', 'asc')
                        ->get();


                    // Calculate the sum of depositAmount minus withdrawAmount
                    $totalBalance = $txnacdetailsamount->sum(function ($detail) {
                        return $detail->depositAmount - $detail->withdrawAmount;
                    });

                    $sessionenddate=$SessionMaster->endDate;
                    $openingBal = DB::table('member_accounts')
                        ->where('accountNo','=',$post->account)
                        // ->where('accType','Share')
                        ->first();

                    //_______Saving Accounts
                    $saving_account = DB::table('opening_accounts')
                        ->select('opening_accounts.*','member_accounts.accountNo as membership','member_accounts.name as customer_name')
                        ->leftJoin('member_accounts','member_accounts.accountNo','=','opening_accounts.membershipno')
                        ->where('opening_accounts.membershipno',$post->account)
                        ->where('opening_accounts.membertype',$post->membertype)
                        ->where('opening_accounts.accountname','=','Saving')
                        ->where('opening_accounts.status','=','Active')
                        ->first();

                    return response()->json([
                        'status' => "success",
                        "sessionenddate" => $sessionenddate,
                        "acdetails" => $acdetails,
                        "totalBalance"=>$totalBalance,
                        "openingBal"=>$openingBal,
                        'txndetails' => $txnacdetails,
                        'balance' => $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate))),
                        'saving_account' => $saving_account
                    ]);
                }

                break;
            case 'share':
                // dd($post->all());
                $rules = array(
                    'action' => 'required',
                    'account' => 'required',
                );
                $validator = \Validator::make($post->all(), $rules);
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
                    $generalLedgers = "shr" . rand(1111111, 9999999);
                } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);
                $result = $this->isDateBetween(date('Y-m-d', strtotime($post->transactionDate))) ;
                if (!$result) {
                         return response()->json(['statuscode'=>'ERR', 'status'=>'Please Check your session', 'message' => "Please Check your session"],400);
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

                        $lastInsertedId = DB::table('member_shares')->insertGetId([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' =>  $post->memberType,
                            "groupCode"   => "SHAM001",
                            'ledgerCode' => "SHAM001",
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            "transactionType"   => "Withdraw",
                            'depositAmount' => 0,
                            'withdrawAmount' => $post->amount,
                            "dividendAmount"   => 0,
                            'chequeNo' => "",
                            'narration' => $post->naraton,
                            "branchId"   => session('bramchid') ?? 1,
                            'agentId' => $post->agentId,
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
                            'formName'   => "Share",
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
                            'agentId' => $post->agentId,
                            'groupCode' => "SHAM001",
                            'ledgerCode' => "SHAM001",
                            'formName'   => "Share",
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
                } else if($post->action == "transfer"){
                    // dd($post->all());

                    $saving_acc = $post->saving_no;

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
                        ->where('opening_accounts.accountNo', $saving_acc)
                        ->where('opening_accounts.membershipno', $post->account)
                        ->first();


                      $balance =  $this->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)));
                    if ($balance < $post->amount) {
                        return response()->json(['status' => "Insufficient Balance", "message" => "Insufficient Balance"], 200);
                    }
                    $ldgerid = LedgerMaster::where('id', 1)
                        ->first();
                    // dd($ldgerid);

                    DB::beginTransaction();
                    try {
                        // $lastInsertedId = DB::table('member_shares')->insertGetId([
                        //     "serialNo" => $generalLedgers,
                        //     "accountId"  =>  $acdetails->id,
                        //     'accountNo' => $post->account,
                        //     'memberType' =>  $post->memberType,
                        //     "groupCode"   => "SHAM001",
                        //     'ledgerCode' => "SHAM001",
                        //     'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                        //     "transactionType"   => "Withdraw",
                        //     'depositAmount' => 0,
                        //     'withdrawAmount' => $post->amount,
                        //     "dividendAmount"   => 0,
                        //     'chequeNo' => "",
                        //     'narration' => $post->naraton,
                        //     "branchId"   => session('bramchid') ?? 1,
                        //     'agentId' => $post->agentId,
                        //     'sessionId' => session('sessionId') ?? 1,
                        //     'txnType' => "transfer",
                        //     "updatedBy"   => $post->user()->id,
                        // ]);


                         //___________Entry in Member Share Table
                    $saving_trfd_share = new MemberShare();
                    $saving_trfd_share->serialNo = $generalLedgers;
                    $saving_trfd_share->accountId = $post->saving_no;
                    $saving_trfd_share->accountNo = $post->account;
                    $saving_trfd_share->memberType = $post->memberType;
                    $saving_trfd_share->groupCode = 'SHAM001';
                    $saving_trfd_share->ledgerCode = 'SHAM001';
                    $saving_trfd_share->shareNo = '';
                    $saving_trfd_share->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $saving_trfd_share->transactionType = 'Withdraw';
                    $saving_trfd_share->depositAmount = 0;
                    $saving_trfd_share->withdrawAmount = $post->amount;
                    $saving_trfd_share->dividendAmount = 0;
                    $saving_trfd_share->chequeNo = 'trfdSaving';
                    $saving_trfd_share->narration = 'Saving A/c- '. $post->saving_no. ' Trfd Share' ?  'Saving A/c-' . $post->saving_no . 'Trfd Share' : $post->saving_no;
                    $saving_trfd_share->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_trfd_share->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_trfd_share->agentId = $post->agentId;
                    $saving_trfd_share->updatedBy = $post->user()->id;
                    $saving_trfd_share->txnType = 'transfer';
                    $saving_trfd_share->is_delete = 'No';
                    $saving_trfd_share->save();

                    $lastInsertedId = $saving_trfd_share->id;

                    //___________Entry in Member Saving Table
                    $saving_withdraw = new MemberSaving();
                    $saving_withdraw->secheme_id = $saving_account->sch_id;
                    $saving_withdraw->serialNo = $generalLedgers;
                    $saving_withdraw->accountId = $post->saving_no;
                    $saving_withdraw->accountNo = $post->account;
                    $saving_withdraw->memberType = $post->memberType;
                    $saving_withdraw->groupCode = $saving_account->groupCode;
                    $saving_withdraw->ledgerCode = $saving_account->ledgerCode;
                    $saving_withdraw->savingNo = '';
                    $saving_withdraw->transactionDate = date('Y-m-d', strtotime($post->transactionDate));
                    $saving_withdraw->transactionType = 'toshare';
                    $saving_withdraw->depositAmount = $post->amount;
                    $saving_withdraw->withdrawAmount = 0;
                    $saving_withdraw->paymentType = $post->groupCode;
                    $saving_withdraw->bank = $post->bank;
                    $saving_withdraw->chequeNo = 'trfdSaving';
                    $saving_withdraw->narration = 'Saving A/c- '. $post->account. ' Trfd Share' ?  'Saving A/c-' . $post->account . 'Trfd Share' : $post->account;
                    $saving_withdraw->branchId = session('branchId') ? session('branchId') : 1;
                    $saving_withdraw->sessionId = session('sessionId') ? session('sessionId') : 1;
                    $saving_withdraw->agentId = $post->agentId;
                    $saving_withdraw->updatedBy = $post->user()->id;
                    $saving_withdraw->is_delete = 'No';
                    $saving_withdraw->save();





                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => $post->memberType,
                            'agentId' => $post->agentId,
                            'ledgerCode' => $saving_account->ledgerCode,
                            'groupCode' => $saving_account->groupCode,
                            'formName'   => "trfdSaving",
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
                            'agentId' => $post->agentId,
                            'groupCode' => "SHAM001",
                            'ledgerCode' => "SHAM001",
                            'formName'   => "trfdSaving",
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
                        $lastInsertedId = DB::table('member_shares')->insertGetId([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => 'Member',
                            'groupCode' => "SHAM001",
                            'ledgerCode' => "SHAM001",
                            'transactionDate' => date('Y-m-d', strtotime($post->transactionDate)),
                            "transactionType"   => "Deposit",
                            'depositAmount' =>  $post->amount,
                            'withdrawAmount' => 0,
                            "dividendAmount"   => 0,
                            'chequeNo' => "",
                            'narration' => $post->naration,
                            "branchId"   => session('bramchid') ?? 1,
                            'agentId' => $post->agentId,
                            'sessionId' => session('sessionId') ?? 1,
                            "updatedBy"   => $post->user()->id,
                        ]);
                        DB::table('general_ledgers')->insert([
                            "serialNo" => $generalLedgers,
                            "accountId"  =>  $acdetails->id,
                            'accountNo' => $post->account,
                            'memberType' => 'Member',
                            'agentId' => $post->agentId ?? 1,
                            'ledgerCode' => $post->bank,
                            'groupCode' => $post->groupCode,
                            'referenceNo' => $lastInsertedId,
                            'formName'   => "Share",
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
                            'agentId' => $post->agentId ?? 1,
                            'groupCode' => "SHAM001",
                            'formName'   => "Share",
                            'ledgerCode' => "SHAM001",
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
            case 'updateshare':
                $rules = array(
                    'id' => 'required',
                    'amount' => 'required',
                    'transactionDate' => 'required',
                    'action' => 'required',
                );
                // dd($post->all());
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $check = DB::table('member_shares')->where('id', $post->id)->first();
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
                    $lastInsertedId = DB::table('member_shares')->where('id', $check->id)->update([
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
            case 'deleteshare':
                $action = DB::table('member_shares')->where('id', $post->id)->first();
                // dd($action);
                if (!$action) {
                    return response()->json(['status' => "failed", "message" => "Transaction not found"], 200);
                }

                 $result = $this->isDateBetween(date('Y-m-d', strtotime($action->transactionDate))) ;
                if (!$result) {
                         return response()->json(['statuscode'=>'ERR', 'status'=>'Please Check your session', 'message' => "Please Check your session"],400);
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

                    //________Delete Member Share
                    DB::table('member_savings')
                        ->where('accountId', $action->accountId)
                        ->where('accountNo', $action->accountNo)
                        ->where('serialNo', $action->serialNo)
                        ->delete();

                    MemberShare::where('id', $post->id)->delete();
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

    public function getbalance($ac, $lastDate){
        $openingBal = DB::table('member_accounts')->where('accountNo',$ac)->first();
        $shareBal = $openingBal->opening_amount ?? 0 ;
        $credit =  MemberShare::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Deposit')->whereDate('transactionDate', '<=', $lastDate)->sum("depositAmount");
        $debit =  MemberShare::where('accountNo', $ac)->where('is_delete', 'No')->where('transactionType', 'Withdraw')->whereDate('transactionDate', '<=', $lastDate)->sum("withdrawAmount");
        return $shareBal + $credit - $debit;
    }
}
