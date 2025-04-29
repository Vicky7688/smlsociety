<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\AgentMaster;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\LedgerMaster;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class JournalVoucherController extends Controller
{
    public function index()
    {
        $groups = GroupMaster::orderBy('name', 'ASC')->get();
        $vouchars = DB::table('journal_vouchers')->max('id') + 1;
        $allvouchars = DB::table('journal_vouchers')
            ->orderBy('journal_vouchers.id','DESC')
            ->get();
        $data['groups'] = $groups;
        $data['vouchars'] = $vouchars;
        $data['allvouchars'] = $allvouchars;
        return view('transaction.journalVoucher', $data);
    }

    public function getled(Request $post)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Ledger Not Found']);
        }

        $name = $post->name;

        $ledgers = DB::table('ledger_masters')
            ->select(
                'ledger_masters.*',
                'ledger_masters.name',
                'ledger_masters.groupCode',
                'group_masters.groupCode as groupcode',
                'group_masters.name as gname'
            )
            ->leftJoin('group_masters', 'group_masters.groupCode', '=', 'ledger_masters.groupCode')
            ->whereNotIn('ledger_masters.groupCode',
                [
                    'SHAM001','SAVM001','SAVN001','SAVS001','FDOM001','FDON001','FDOS001',
                    'RDOM001','RDON001','RDOS001','LONM001','LONN001','LONS001',
                    'DCOM001','DCON001','DCOS001','MISM001','MISN001','MISS001',
                    'MEM01','NON01','STA02','GRTDAI01','LOA02','RDL01'
                ]
            )
            ->where('ledger_masters.name', 'LIKE', $name . '%')
            ->get();


        if (!empty($ledgers)) {
            return response()->json(['status' => 'success', 'data'  => $ledgers]);
        } else {
            return response()->json(['status' => 'Fail', 'messages'  => 'Ledger Not Found']);
        }
    }

    public function getdatadat(Request $post)
    {
        $gatdata = LedgerMaster::find($post->name);
        return response()->json($gatdata);
    }

    public function submitvoucher(Request $post){

        $vodatess = date('Y-m-d', strtotime($post->voucherdate));

        DB::beginTransaction();

        try {
            $voucharNumber = $post->voucherno;
            $serialNo = 'JrVouchar' . time();

            DB::table('vouchar_master')->insert([
                'vouchar_no' => $voucharNumber,
            ]);


            $id = DB::table('journal_vouchers')->insertGetId([
                'vouchar_no' => $voucharNumber,
                'serialNo' => $serialNo,
                'voucherDate' => $vodatess,
                'drAmount' => is_array($post->dramount) ? array_sum($post->dramount) : $post->dramount,
                'crAmount' => is_array($post->cramount) ? array_sum($post->cramount) : $post->cramount,
                'narration' => is_array($post->description) ? implode(", ", $post->description) : $post->description,
                'branchId' => Session::get('branchId', 1),
                'updatedBy' => $post->user()->id,
                'sessionId' => Session::get('sessionId', 1),
                'is_delete' => 'No',
            ]);



            $allcount = is_array($post->drcr) ? count($post->drcr) : 0;

            if ($allcount > 0) {
                for ($x = 0; $x < $allcount; $x++) {
                    $getLedger = DB::table('ledger_masters')->where('ledgerCode', $post->code[$x])->first();


                    if (!$getLedger) {
                        DB::rollBack();
                        return response()->json(['status' => 'Fail', 'messages' => 'Invalid Ledger Code: ' . $post->code[$x]]);
                    }

                    DB::table('journal_voucher_details')->insertGetId([
                        'vouchar_no' => $voucharNumber,
                        'serialNo' => $serialNo,
                        'voucherId' => $id,
                        'groupCode' => $getLedger->groupCode,
                        'ledgerCode' => $getLedger->ledgerCode,
                        'transactionType' => $post->drcr[$x],
                        'drAmount' => $post->dramount[$x],
                        'crAmount' => $post->cramount[$x],
                        'branchId' => Session::get('branchId', 1),
                        'sessionId' => Session::get('sessionId', 1),
                        'updatedBy' => $post->user()->id,
                        'is_delete' => 'No',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);


                    $dr_cr = $post->drcr[$x] != "Dr" ? "Cr" : "Dr";
                    $amount = $post->drcr[$x] != "Dr" ? $post->cramount[$x] : $post->dramount[$x];

                    DB::table('general_ledgers')->insertGetId([
                        'serialNo' => $serialNo,
                        'accountId' => null,
                        'accountNo' => null,
                        'memberType' => null,
                        'groupCode' => $getLedger->groupCode,
                        'ledgerCode' => $getLedger->ledgerCode,
                        'formName' => "JournalVouchar",
                        'referenceNo' => $id,
                        'entryMode' => 'manual',
                        'transactionDate' => $vodatess,
                        'transactionType'	 => $dr_cr,
                        'transactionAmount' => $amount,
                        'narration' =>  is_array($post->description) ? implode(", ", $post->description) : $post->description,
                        'branchId' => Session::get('branchId', 1),
                        'agentId' => null,
                        'sessionId' => Session::get('sessionId', 1),
                        'updatedBy' => Session::get('userId', 1),
                        'is_delete' => 'No',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'messages' => 'Voucher Added Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Fail', 'messages' => $e->getMessage(), 'lines' => $e->getLine()]);
        }
    }

    public function deletevouchares(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check Id']);
        }

        $transactionDate = now()->format('Y-m-d');

        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        // $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        // if (!$result) {
        //     return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        // }

        $id = $post->id;
        $voucharId = DB::table('journal_vouchers')->where('id',$id)->first();

        if(is_null($voucharId)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{

            DB::beginTransaction();
            try{

                DB::table('journal_voucher_details')
                    ->where('voucherId',$voucharId->id)
                    ->where('serialNo',$voucharId->serialNo)
                    ->delete();

                DB::table('general_ledgers')
                    ->where('referenceNo',$voucharId->id)
                    ->where('serialNo',$voucharId->serialNo)
                    ->delete();

                DB::table('journal_vouchers')->where('id',$id)->delete();


                DB::commit();

                return response()->json(['status' => 'success','messages' => 'Record Deleted Successfully']);


            }catch(\Exception $e){
                DB::rollBack();
                return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'lines' => $e->getLine()]);
            }
        }
    }

    public function editvouchars(Request $post){
        $rules = [
            'id' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check Id']);
        }

        $transactionDate = now()->format('Y-m-d');

        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }

        $id = $post->id;
        $voucharId = DB::table('journal_vouchers')->where('id',$id)->first();
        $details = DB::table('journal_voucher_details')
            ->select('journal_voucher_details.*','ledger_masters.ledgerCode as lg','ledger_masters.name')
            ->leftJoin('ledger_masters','ledger_masters.ledgerCode','=','journal_voucher_details.ledgerCode')
            ->where('voucherId',$voucharId->id)
            ->where('serialNo',$voucharId->serialNo)
            ->get();

        if(!empty($details)){
            return response()->json(['status' => 'success','details' => $details,'voucharId' => $voucharId]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }

    public function updatevouchar(Request $post){

        $rules = [
            'voucherId' => 'required'
        ];

        $validator = Validator::make($post->all(),$rules);

        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check Id']);
        }

        $transactionDate = now()->format('Y-m-d');

        //__________Check Financial Year Audit && Financial Year Entries
        $session_master = SessionMaster::find(Session::get('sessionId'));

        if ($session_master->auditPerformed === 'Yes') {
            return response()->json(['status' => 'Fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
        }

        $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

        if (!$result) {
            return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
        }


        $id = $post->voucherId;
        $voucharId = DB::table('journal_vouchers')->where('id',$id)->first();
        $vodatess = date('Y-m-d', strtotime($post->voucherdate));


        DB::beginTransaction();

        if(!empty($voucharId)){

            try{

                DB::table('journal_voucher_details')->where('voucherId',$voucharId->id)->where('serialNo',$voucharId->serialNo)->delete();
                DB::table('general_ledgers')->where('referenceNo',$voucharId->id)->where('serialNo',$voucharId->serialNo)->delete();
                DB::table('journal_vouchers')->where('id',$id)->delete();


                $voucharNumber = $post->voucherno;
                $serialNo = 'JrVouchar' . time();

                DB::table('vouchar_master')->insert([
                    'vouchar_no' => $voucharNumber,
                ]);


                $id = DB::table('journal_vouchers')->insertGetId([
                    'vouchar_no' => $voucharNumber,
                    'serialNo' => $serialNo,
                    'voucherDate' => $vodatess,
                    'drAmount' => is_array($post->dramount) ? array_sum($post->dramount) : $post->dramount,
                    'crAmount' => is_array($post->cramount) ? array_sum($post->cramount) : $post->cramount,
                    'narration' => is_array($post->description) ? implode(", ", $post->description) : $post->description,
                    'branchId' => Session::get('branchId', 1),
                    'updatedBy' => $post->user()->id,
                    'sessionId' => Session::get('sessionId', 1),
                    'is_delete' => 'No',
                ]);



                $allcount = is_array($post->drcr) ? count($post->drcr) : 0;

                if ($allcount > 0) {
                    for ($x = 0; $x < $allcount; $x++) {
                        $getLedger = DB::table('ledger_masters')->where('ledgerCode', $post->code[$x])->first();

                        if (!$getLedger) {
                            DB::rollBack();
                            return response()->json(['status' => 'Fail', 'messages' => 'Invalid Ledger Code: ' . $post->code[$x]]);
                        }

                        DB::table('journal_voucher_details')->insertGetId([
                            'vouchar_no' => $voucharNumber,
                            'serialNo' => $serialNo,
                            'voucherId' => $id,
                            'groupCode' => $getLedger->groupCode,
                            'ledgerCode' => $getLedger->ledgerCode,
                            'transactionType' => $post->drcr[$x],
                            'drAmount' => $post->dramount[$x],
                            'crAmount' => $post->cramount[$x],
                            'branchId' => Session::get('branchId', 1),
                            'sessionId' => Session::get('sessionId', 1),
                            'updatedBy' => $post->user()->id,
                            'is_delete' => 'No',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);


                        $dr_cr = $post->drcr[$x] != "Dr" ? "Cr" : "Dr";
                        $amount = $post->drcr[$x] != "Dr" ? $post->cramount[$x] : $post->dramount[$x];

                        DB::table('general_ledgers')->insertGetId([
                            'serialNo' => $serialNo,
                            'accountId' => null,
                            'accountNo' => null,
                            'memberType' => null,
                            'groupCode' => $getLedger->groupCode,
                            'ledgerCode' => $getLedger->ledgerCode,
                            'formName' => "JournalVouchar",
                            'referenceNo' => $id,
                            'entryMode' => 'manual',
                            'transactionDate' => $vodatess,
                            'transactionType'	 => $dr_cr,
                            'transactionAmount' => $amount,
                            'narration' =>  is_array($post->description) ? implode(", ", $post->description) : $post->description,
                            'branchId' => Session::get('branchId', 1),
                            'agentId' => null,
                            'sessionId' => Session::get('sessionId', 1),
                            'updatedBy' => Session::get('userId', 1),
                            'is_delete' => 'No',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['status' => 'success', 'messages' => 'Voucher Updated Successfully']);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'lines' => $e->getLine()]);
            }

        }else{
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not']);
        }
    }

    // public function store(Request $post)
    // {
    //     $rules = [
    //         'entries' => 'required|array',
    //         'entries.*.groupCode' => 'required',
    //         'entries.*.ledgerCode' => 'required',
    //         'entries.*.drAmount' => 'required',
    //         'entries.*.crAmount' => 'required',
    //     ];
    //     $validator = Validator::make($post->all(), $rules);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors(),
    //             'message' => 'Please check all inputs'
    //         ]);
    //     }
    //     DB::beginTransaction();
    //     try {
    //         // Generate a unique serial number
    //         do {
    //             $serialNo = "voucher" . rand(1111111, 9999999);
    //         } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
    //         $lastVoucherId = JournalVoucher::max('id');
    //         // Increment it by 1 for the next voucher
    //         $nextVoucherId = $lastVoucherId + 1;
    //         $vodatess = date("Y-m-d", strtotime($post->voucherDate));


    //         $totalDrAmount = 0;
    //         $totalCrAmount = 0;
    //         $narrations = [];
    //         $updatedBy = $post->user()->id;
    //         $voucherDetails = [];
    //         foreach ($post->entries as $entry) {
    //             $voucherDetails[] = [
    //                 'groupCode' => $entry['groupCode'],
    //                 'ledgerCode' => $entry['ledgerCode'],
    //                 'drAmount' => $entry['drAmount'],
    //                 'crAmount' => $entry['crAmount'],
    //                 'narration' => $entry['narration'],
    //                 'branchId' => $entry['branchId'],
    //                 'sessionId' => $entry['sessionId'],
    //                 'serialNo' => $serialNo,
    //                 'updatedBy' => auth()->user()->id,
    //                 // Add other fields if needed
    //             ];
    //             $totalDrAmount += $entry['drAmount'];
    //             $totalCrAmount += $entry['crAmount'];
    //             $narrations[] = $entry['narration'];
    //         }
    //         $voucher = JournalVoucher::create([
    //             'voucherDate' => $vodatess,
    //             'drAmount' => $totalDrAmount,
    //             'crAmount' => $totalCrAmount,
    //             'narration' => implode(', ', $narrations),
    //             'branchId' => session('branchId') ? session('branchId') : 1,
    //             'sessionId' => session('sessionId') ? session('sessionId') : 1,
    //             'voucherId' => $nextVoucherId,
    //             'updatedBy' => $updatedBy,
    //         ]);
    //         $agentId = $post->input('agentId') ?? AgentMaster::min('id');
    //         $sessionId = session('sessionId') ?? 1;
    //         foreach ($voucherDetails as $detail) {
    //             $detail['voucherId'] = $voucher->id;
    //             JournalVoucherDetail::create($detail);
    //             $genralledger = new GeneralLedger;
    //             $genralledger->serialNo = $serialNo;
    //             $genralledger->memberType = "Member";
    //             $genralledger->groupCode = $detail['groupCode'];
    //             $genralledger->ledgerCode = $detail['ledgerCode'];
    //             $genralledger->formName = "JournalVoucher";
    //             $genralledger->referenceNo = $voucher->id;
    //             $genralledger->entryMode = "Manual";
    //             $genralledger->narration = $detail['narration'];
    //             $genralledger->transactionDate = $vodatess;
    //             $genralledger->transactionAmount = abs($detail['drAmount'] - $detail['crAmount']);
    //             $genralledger->transactionType = ($detail['drAmount'] > 0) ? "Dr" : "Cr";
    //             $genralledger->branchId = $detail['branchId'];
    //             $genralledger->agentId = $agentId; // Assuming this is correct
    //             $genralledger->sessionId = $sessionId ;
    //             $genralledger->updatedBy = auth()->user()->id;
    //             $genralledger->save();
    //         }
    //         DB::commit();
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Journal entries saved successfully',
    //             'data' => ['voucherId' => $voucher->id],
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Transaction Failed',
    //             'errors' => $e->getMessage()
    //         ]);
    //     }
    // }


}
