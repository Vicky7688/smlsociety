<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebControllers\Transactions\ShareController;
use App\Models\AgentMaster;
use App\Models\GeneralLedger;
use App\Models\LedgerMaster;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\LoanMaster;
use App\Models\LoanRecovery;
use App\Models\MemberAccount;
use App\Models\MemberFdScheme;
use App\Models\MemberLoan;
use App\Models\PurposeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LoanTransactionController extends Controller
{
    public function index()
    {
        return view('transaction.loan.index');
    }

    public function loan($type)
    {
        $data['title'] = $type;
        switch ($type) {
            case 'advancement':
                $data['agents'] = AgentMaster::get();
                $data['loantypes'] = LoanMaster::where('status', "active")->get();
                $data['purposes'] = PurposeMaster::get();
                $data['grup'] = DB::table('group_masters')->where('groupCode', '=', 'LOA02')->get();
                $data['grupo'] = DB::table('group_masters')->where('groupCode', '=', 'LOA03')->get();
                $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
                break;
            case 'recovery':
                $data['agents'] = AgentMaster::get();
                $data['loantypes'] = LoanMaster::where('status', "active")->get();
                $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
                break;

            default:
                $data['agents'] = AgentMaster::get();
                $data['loantypes'] = LoanMaster::where('status', "active")->get();
                $data['banktypes'] = LedgerMaster::where('groupCode', "BANK001")->get();
        }
        return view('transaction.loan.' . $type)->with($data);
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
    public function getloanDetail(Request $post)
    {
        $acdetails = MemberAccount::where(['accountNo' => $post->account, 'memberType' => $post->member])->first(['name', 'address']);
        if (!$acdetails) {
            return response()->json(['status' => "Invalid Account number"]);
        }
        $share = new ShareController;
        $sharebalance = $share->getbalance($post->account, date('Y-m-d', strtotime($post->transactionDate)));
        $txnacdetails = MemberLoan::where(['accountNo' => $post->account, 'memberType' => $post->member])->orderBy('id', 'asc')->get();

        return response()->json(['status' => "success", "txnacdetails" => $txnacdetails, "acdetails" => $acdetails, 'balance' => $sharebalance]);
    }
    public function getLoanType(Request $post)
    {
        $data = LoanMaster::where('id', $post->loanType)->first();
        // dd($data);
        if (!$data) {
            return response()->json(['status' => "Invalid Loan type"]);
        }
        return response()->json(['status' => "success", "data" => $data]);
    }
    public function grantordetails(Request $post)
    {
        $acdetails = MemberAccount::where('accountNo', $post->accountid)->where('memberType', 'Member')->first();
        if (!$acdetails) {
            return response()->json(['status' => "Invalid Account number"]);
        }
        $beneficirys = MemberLoan::where('guranter1', $post->accountid)->orWhere('guranter2', $post->accountid)->orderBy('id', 'desc')->get();
        return response()->json(['status' => "success", 'data' => $acdetails, 'benelist' => $beneficirys]);
    }

    public function insertloanadvancement(Request $post)
    {
        // dd($post->all());
        $acdetails = MemberAccount::where(['accountNo' => $post->accountNumber, 'memberType' => $post->memberType])->first();
        if (!$acdetails) {
            return response()->json(['status' => "Invalid Account number"]);
        }
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
        $share = new ShareController;
        $sharebalance = $share->getbalance($post->accountNumber, date('Y-m-d', strtotime($post->transactionDate)));

        $maxlimit = $sharebalance * 10;
        if ($maxlimit < $post->amount) {
            //  return response()->json(['status' => "Amount sholud not be greter then" .  $maxlimit]);
        }
        do {
            $generalLedgers = "loan" . time();
        } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);
        if ($post->loanBy == "Transfer") {
            $ledgerMasterCR = LedgerMaster::where('id', $post->ledgerId)->first(['groupCode', 'ledgerCode']);
            if (!$ledgerMasterCR) {
                return response()->json(['status' => "Invalid Bank or Type"]);
            }
        } else {
            $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
        }
        $processingFee = (($post->amount * $loanmaster->processingFee) / 100);
        $member_ship = $post->accountNumber;
        $notice_for_installment = $post->file('notice_for_installment');
        $notice_for_election = $post->file('notice_for_election');

        if ($notice_for_installment) {
            $currentTimestamp = now()->format('YmdHis');
            $randomString = Str::random(8); // Generate a random string of length 8
            $filename1 = 'notice_for_installment_' . $currentTimestamp . '_' . $randomString . '.' . $notice_for_installment->getClientOriginalExtension();
            $path = public_path() . '/uploads/loans/' . $filename1;
            $notice_for_installment->move(public_path() . '/uploads/loans/', $filename1);
        }

        if ($notice_for_election) {
            $currentTimestamp = now()->format('YmdHis');
            $randomString = Str::random(8); // Generate a random string of length 8
            $filename2 = 'image_' . $currentTimestamp . '_' . $randomString . '.' . $notice_for_election->getClientOriginalExtension();
            $path2 = public_path() . '/uploads/loans/' . $filename2;
            $notice_for_election->move(public_path() . '/uploads/loans/', $filename2);
        }


        DB::beginTransaction();
        try {
            $loanDate = $post->loanDate;
            $installments = $this->calculateReducingEMI($post->amount, $post->loanInterest, $post->loanYear, $post->loanMonth, 0, $loanDate);

            $data = [
                'accountNo' => $acdetails->accountNo,
                'accountId' => $acdetails->id,
                'serialNo' => $generalLedgers,
                'loanDate' => date('Y-m-d', strtotime($post->loanDate)),
                'loanEndDate' => $endDate,
                'memberType' => $post->memberType,
                'ledgerCode' => $ledgerMasterCR->ledgerCode,
                'groupCode' => $ledgerMasterCR->groupCode,
                'loanAcNo' => $post->loanAcNo,
                'purpose' => $post->purpose,
                'loanType' => $post->loanType,
                'processingFee' => $processingFee,
                'processingRates' => $post->processingRates,
                'loanYear' => $post->loanYear,
                'loanMonth' => $post->loanMonth,
                'loanInterest' => $post->loanInterest,
                'loanPanelty' => $post->defintr,
                'loanAmount' => $post->amount,
                'bankDeduction' => $post->bankDeduction,
                'deductionAmount' => $post->deduction,
                'pernote' => $post->pernote,
                'loanBy' => $post->loanBy,
                'chequeNo' => '',
                'loan_app_fee' => $post->loan_app_fee,
                'installmentType' => $post->installmentType,
                'guranter1' => $post->guranter1,
                // 'document_name' => $post->guranter1name,
                'guranter2' => '',
                'Status' => 'Disbursed',
                'branchId' => session('branchid') ?? 1,
                'agentId' => $post->agentId,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Conditionally add optional files
            if (!empty($filename1)) {
                $data['notice_for_installment'] = $filename1;
            }
            if (!empty($filename2)) {
                $data['notice_for_election'] = $filename2;
            }
            // if (!empty($filename3)) {
            //     $data['documents'] = $filename3;
            // }

            $lastInsertedId = DB::table('member_loans')->insertGetId($data);
            // Handle multiple uploaded documents
            if ($post->hasFile('documents')) {
                $documents = $post->file('documents');
                $guarantorNames = $post->guranter1name;

                foreach ($documents as $index => $document) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8);
                    $filename = 'memberidproof_' . $currentTimestamp . '_' . $randomString . '.' . $document->getClientOriginalExtension();
                    $document->move(public_path('uploads/loans'), $filename);

                    DB::table('loan_documents')->insert([
                        "loanId" => $lastInsertedId,
                        "document_name" => $guarantorNames[$index] ?? 'Unknown',
                        "document_img" => $filename,
                        'created_at' => now(),
                    ]);
                }
            }



            foreach ($installments['schedule'] as $row) {
                DB::table('loan_installments')->insertGetId([
                    'LoanId' => $lastInsertedId,
                    'installmentDate' => Carbon::parse($row['emi_date'])->format('Y-m-d'),
                    'principal' => round($row['principal']),
                    'interest' => round($row['interest']),
                    'total' => round($row['emi']),
                    'paid_date' => null,
                    'status' => 'False',
                    're_amount' => 0,
                    'recovery_id' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

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
    }
    public function getInstallmets(Request $post)
    {
        // dd($post->all());
        $loanid = $post->id;
        $loan = DB::table('member_loans')->where('id', $loanid)->first();
        $installments = $this->calculateReducingEMI($post->loanAmount, $post->intrest, $post->year, $post->month, 0, $post->loandate);
        // dd($installments);
        return response()->json(['status' => 'success', 'installments' => $installments,  'loan' => $loan]);
    }
    public function updateloanadvancement(Request $post)
    {
        $loanFetch = MemberLoan::where("id", $post->loanId)->first();
        $receipt = LoanRecovery::where('LoanId', $post->id)->where('is_delete', 'No')->first();

        if ($receipt) {
            return response()->json(['status' => "Please Detele Recovery First"]);
        } else {
            $acdetails = MemberAccount::where(['accountNo' => $post->accountNumber, 'memberType' => $post->memberType])->first();
            if (!$acdetails) {
                return response()->json(['status' => "Invalid Account number"]);
            }
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
            $share = new ShareController;
            $sharebalance = $share->getbalance($post->accountNumber, date('Y-m-d', strtotime($post->transactionDate)));

            $maxlimit = $sharebalance * 10;
            if ($maxlimit < $post->amount) {
                //  return response()->json(['status' => "Amount sholud not be greter then" .  $maxlimit]);
            }
            do {
                $generalLedgers = "loan" . time();
            } while (GeneralLedger::where("serialNo", "=", $generalLedgers)->first() instanceof GeneralLedger);
            if ($post->loanBy == "Transfer") {
                $ledgerMasterCR = LedgerMaster::where('id', $post->ledgerId)->first(['groupCode', 'ledgerCode']);
                if (!$ledgerMasterCR) {
                    return response()->json(['status' => "Invalid Bank or Type"]);
                }
            } else {
                $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
            }
            $processingFee = (($post->amount * $loanmaster->processingFee) / 100);
            $member_ship = $post->accountNumber;
            $notice_for_installment = $post->file('notice_for_installment');
            $notice_for_election = $post->file('notice_for_election');
            if ($notice_for_installment) {
                $currentTimestamp = now()->format('YmdHis');
                $randomString = Str::random(8); // Generate a random string of length 8
                $filename1 = 'notice_for_installment_' . $currentTimestamp . '_' . $randomString . '.' . $notice_for_installment->getClientOriginalExtension();
                $path = public_path() . '/uploads/loans/' . $filename1;
                $notice_for_installment->move(public_path() . '/uploads/loans/', $filename1);
            }

            if ($notice_for_election) {
                $currentTimestamp = now()->format('YmdHis');
                $randomString = Str::random(8); // Generate a random string of length 8
                $filename2 = 'image_' . $currentTimestamp . '_' . $randomString . '.' . $notice_for_election->getClientOriginalExtension();
                $path2 = public_path() . '/uploads/loans/' . $filename2;
                $notice_for_election->move(public_path() . '/uploads/loans/', $filename2);
            }

            DB::beginTransaction();
            try {
                DB::table('general_ledgers')->where('serialNo', $loanFetch->serialNo)->delete();
                DB::table('member_loans')->where("id", $post->id)->delete();
                DB::table('loan_installments')->where("LoanId", $post->id)->delete();
                DB::table('loan_documents')->where("loanId", $post->id)->delete();
                DB::table('member_savings')->where('serialNo', $loanFetch->serialNo)->delete();

                $loanDate = $post->loanDate;
                $installments = $this->calculateReducingEMI($post->amount, $post->loanInterest, $post->loanYear, $post->loanMonth, 0, $loanDate);

                $data = [
                    'accountNo' => $acdetails->accountNo,
                    'accountId' => $acdetails->id,
                    'serialNo' => $generalLedgers,
                    'loanDate' => date('Y-m-d', strtotime($post->loanDate)),
                    'loanEndDate' => $endDate,
                    'memberType' => $post->memberType,
                    'ledgerCode' => $ledgerMasterCR->ledgerCode,
                    'groupCode' => $ledgerMasterCR->groupCode,
                    'loanAcNo' => $post->loanAcNo,
                    'purpose' => $post->purpose,
                    'loanType' => $post->loanType,
                    'processingFee' => $processingFee,
                    'processingRates' => $post->processingRates,
                    'loanYear' => $post->loanYear,
                    'loanMonth' => $post->loanMonth,
                    'loanInterest' => $post->loanInterest,
                    'loanPanelty' => $post->defintr,
                    'loanAmount' => $post->amount,
                    'bankDeduction' => $post->bankDeduction,
                    'deductionAmount' => $post->deduction,
                    'pernote' => $post->pernote,
                    'loanBy' => $post->loanBy,
                    'chequeNo' => '',
                    'loan_app_fee' => $post->loan_app_fee,
                    'installmentType' => $post->installmentType,
                    'guranter1' => $post->guranter1,
                    // 'document_name' => $post->guranter1name,
                    'guranter2' => '',
                    'Status' => 'Disbursed',
                    'branchId' => session('branchid') ?? 1,
                    'agentId' => $post->agentId,
                    'sessionId' => session('sessionId') ?? 1,
                    'updatedBy' => $post->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Conditionally add optional files
                if (!empty($filename1)) {
                    $data['notice_for_installment'] = $filename1;
                }
                if (!empty($filename2)) {
                    $data['notice_for_election'] = $filename2;
                }
                // if (!empty($filename3)) {
                //     $data['documents'] = $filename3;
                // }

                $lastInsertedId = DB::table('member_loans')->insertGetId($data);
                // Handle multiple uploaded documents
                if ($post->hasFile('documents')) {
                    $documents = $post->file('documents');
                    $guarantorNames = $post->guranter1name;

                    foreach ($documents as $index => $document) {
                        $currentTimestamp = now()->format('YmdHis');
                        $randomString = Str::random(8);
                        $filename = 'memberidproof_' . $currentTimestamp . '_' . $randomString . '.' . $document->getClientOriginalExtension();
                        $document->move(public_path('uploads/loans'), $filename);

                        DB::table('loan_documents')->insert([
                            "loanId" => $lastInsertedId,
                            "document_name" => $guarantorNames[$index] ?? 'Unknown',
                            "document_img" => $filename,
                            'created_at' => now(),
                        ]);
                    }
                }

                foreach ($installments['schedule'] as $row) {
                    DB::table('loan_installments')->insertGetId([
                        'LoanId' => $lastInsertedId,
                        'installmentDate' => Carbon::parse($row['emi_date'])->format('Y-m-d'),
                        'principal' => round($row['principal']),
                        'interest' => round($row['interest']),
                        'total' => round($row['emi']),
                        'paid_date' => null,
                        'status' => 'False',
                        're_amount' => 0,
                        'recovery_id' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

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

                DB::commit();
                return response()->json(['status' => "success"]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => "Some Technical issue Occurred"]);
            }
        }
    }
    public function deleteloan(Request $post)
    {
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
                DB::table('loan_documents')->where("loanId", $post->id)->delete();
                DB::table('member_savings')->where('serialNo', $loanFetch->serialNo)->delete();

                $loancode = DB::table('loan_masters')
                    ->select(
                        'loan_masters.*',
                        'ledger_masters.id as ledgerid',
                        'ledger_masters.*'
                    )
                    ->where('loan_masters.id', $post->loanType)
                    ->join('ledger_masters', 'ledger_masters.id', '=', 'loan_masters.ledger_master_id')
                    ->first();

                // if ($loancode->loantypess === 'FD') {
                //     $fdidis = explode(',', $loanFetch->fdId);
                //     foreach ($fdidis as $fdidisis) {
                //         MemberFdScheme::where('id', $fdidisis)->update(['status' => "Active"]);
                //     }
                // }
                // if ($loancode->loantypess === 'RD') {
                //     $rdidis = explode(',', $loanFetch->rd_id);
                //     foreach ($rdidis as $rdidisis) {
                //         DB::table('re_curring_rds')->where('id', $rdidisis)->update(['status' => "Active"]);
                //     }
                // }

                DB::commit();
                return response()->json(['status' => "success"]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => "Some Technical issue Occurred"]);
            }
        }
    }
    public function loandata(Request $post)
    {
        $loanaccount =  MemberLoan::where('id', $post->id)->first();
        $loandocuments = LoanDocument::where('loanId', $post->id)->get();
        if (!$loanaccount) {
            return response()->json(["status" => "Some Technical issue occurred"], 200);
        }
        return response()->json(["status" => "success", "data" => $loanaccount, "loandocuments" => $loandocuments], 200);
    }

    function calculateReducingEMI($loanAmount, $annualInterestRate, $years, $months, $days = 0, $loanDate = null)
    {
        $totalMonths = ($years * 12) + $months + floor($days / 30); // Approximating days to months
        $monthlyRate = ($annualInterestRate / 12) / 100;
        $emi = $loanAmount * $monthlyRate * pow(1 + $monthlyRate, $totalMonths) / (pow(1 + $monthlyRate, $totalMonths) - 1);
        $schedule = [];
        $remainingPrincipal = $loanAmount;
        $totalInterest = 0;
        $totalPrincipal = 0;
        // Start EMI date
        $emiDate = $loanDate ? Carbon::parse($loanDate)->addMonthsNoOverflow(1) : Carbon::now()->addMonthsNoOverflow(1);



        for ($i = 1; $i <= $totalMonths; $i++) {
            $interest = $remainingPrincipal * $monthlyRate;
            $principal = $emi - $interest;
            $remainingPrincipal -= $principal;
            $schedule[] = [
                'month' => $i,
                'emi_date' => $emiDate->copy()->format('Y-m-d'), // Save EMI date
                'emi' => round($emi, 2),
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'balance' => round(max(0, $remainingPrincipal), 2),
            ];
            // Add next month with overflow protection
            $emiDate->addMonthNoOverflow();
        }
        return [
            // 'emi' => round($emi, 2),
            // 'total_emi_paid' => round($emi * $totalMonths, 2),
            // 'total_interest_paid' => round($totalInterest, 2),
            // 'total_principal_paid' => round($totalPrincipal, 2),
            'schedule' => $schedule
        ];
    }
    public function getLoanAc(Request $post)
    {
        $acloan = MemberLoan::where('accountNo', $post->loanAcNo)->where('memberType', $post->member)->orderBy('id', 'desc')->get();
        // dd($acloan);
        if (count($acloan) > 0) {
            return response()->json(['status' => "success", 'data' => $acloan]);
        }
        return response()->json(['status' => "Loan Account not found"]);
    }


    public function getloandetails(Request $post)
    {
        $loanId = $post->id;
        $txnDate = date('Y-m-d', strtotime($post->transactiondate));

        // Fetch the loan details
        $loanDetail = DB::table('member_loans')
            ->select(
                'member_loans.*',
                'loan_masters.memberType as memberType',
                'member_accounts.name as memberName'
            )
            ->leftJoin('loan_masters', 'loan_masters.id', '=', 'member_loans.loanType')
            ->leftJoin('member_accounts', 'member_accounts.id', '=', 'member_loans.accountId')
            ->where('member_loans.id', $loanId)
            ->first();

        if (!$loanDetail) {
            return response()->json(['status' => 'error', 'message' => 'Loan not found'], 404);
        }

        $recoveryData = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->orderBy('receiptDate', 'desc')
            ->get();

        $principal_recoveries = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->sum('principal');

        $interest_recoveries = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->sum('interest');

        $overdue_recoveries = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->sum('overDueInterest');

        $pending_recoveries = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->sum('pendingInterest');

        $penality_recoveries = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->sum('penalInterest');

        $Amount = $loanDetail->loanAmount - $principal_recoveries;
        $LoanAmount = $loanDetail->loanAmount;
        $AnualInterest = $loanDetail->loanInterest;
        $InterestRate = $loanDetail->loanInterest;
        $LoanDate = Carbon::parse($loanDetail->loanDate);
        $TransactionDate = Carbon::parse($txnDate);
        $LoanType = $loanDetail->memberType;
        $LoanName = $loanDetail->memberName;
        $pernote = $loanDetail->pernote;

        // Ensure the transaction date is after the loan date
        if ($TransactionDate->lessThan($LoanDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction date must be after loan date'
            ], 400);
        }

        // Get last recovery date before or equal to transaction date
        $lastRecoveryDate = DB::table('loan_recoveries')
            ->where('LoanId', $loanDetail->id)
            ->whereDate('receiptDate', '<=', $txnDate)
            ->orderBy('receiptDate', 'desc')
            ->value('receiptDate');

        $fromDate = $lastRecoveryDate ? Carbon::parse($lastRecoveryDate) : $LoanDate;
        $totalDays = $fromDate->diffInDays($TransactionDate);

        // Accurate per-day interest calculation
        $dailyInterestRate = ($InterestRate / 100) / 365;
        $totalInterest = $Amount * $dailyInterestRate * $totalDays;

        // Fetch Installments
        $installments = DB::table('loan_installments')
            ->where('LoanId', $loanId)
            ->get();

        // Map the installment data
        $installments = $installments->map(function ($item) {
            $installmentDate = isset($item->installmentdate) && !empty($item->installmentdate)
                ? Carbon::parse($item->installmentdate)->format('Y-m-d') : null;

            return [
                'installmentDate' => $installmentDate,
                'principal' => $item->principal ?? 0,
                'interest' => $item->interest ?? 0,
                'total' => ($item->principal ?? 0) + ($item->interest ?? 0)
            ];
        });

        // Fetch the latest installment date
        $latestInstallment = $installments->first();
        $recoveryDate = $latestInstallment && $latestInstallment['installmentDate']
            ? $latestInstallment['installmentDate']
            : null;
        $pending_interest = 0 + $pending_recoveries;

        return response()->json([
            'status' => 'success',
            'recoveryData' => $recoveryData,
            'recovery' => $recoveryDate,
            'data' => [
                'Amount' => $Amount,
                'LoanAmount' => $LoanAmount,
                'LoanType' => $LoanType,
                'AnualInterest' => $AnualInterest,
                'PernoteNo' => $pernote,
                'InterestRate' => $InterestRate,
                'LoanDate' => $LoanDate->toDateString(),
                'TransactionDate' => $TransactionDate->toDateString(),
                'TotalDays' => $totalDays
            ],
            'loandetails' => [
                'MonthlyInterest' => round($Amount * ($InterestRate / 100) / 12),
                'PerDayInterest' => round($Amount * $dailyInterestRate),
                'TotalInterest' => round($totalInterest),
                'principal' => $Amount,
                'currentintrest' => round($totalInterest),
                'netintrest' => round($totalInterest),
                'pendingintrest' => $pending_interest,
            ],
            'installmet' => $installments
        ]);
    }


    public function saverecovery(Request $post)
    {
        $loanaccount = MemberLoan::find($post->id);
        if (!$loanaccount) {
            return response()->json(["status" => "Some Technical issue occurred"], 200);
        }

        $this->loanstatus($loanaccount->id);

        $existingPendingInterest = LoanRecovery::where('loanId', $post->id)
            ->where('is_delete', 'No')
            ->sum('pendingInterest');

        $receivedAmount = $post->ReceivedAmount;
        $pendingInterest = 0;
        $interest = 0;
        $penalty = 0;
        $principal = 0;
        $clearedPendingInterest = 0;

        // Clear existing pending interest first
        if ($existingPendingInterest > 0) {
            if ($receivedAmount >= $existingPendingInterest) {
                $clearedPendingInterest = $existingPendingInterest;
                $receivedAmount -= $existingPendingInterest;
            } else {
                $clearedPendingInterest = $receivedAmount;
                $pendingInterest = $existingPendingInterest - $receivedAmount;
                $receivedAmount = 0;
            }
        }

        // Then clear current interest
        if ($receivedAmount > 0) {
            if ($receivedAmount >= $post->InterestTillDate) {
                $interest = $post->InterestTillDate;
                $receivedAmount -= $post->InterestTillDate;
            } else {
                $interest = $receivedAmount;
                $pendingInterest += $post->InterestTillDate - $receivedAmount;
                $receivedAmount = 0;
            }
        }

        // Then clear penalty
        if ($receivedAmount > 0) {
            if ($receivedAmount >= $post->PenaltyTillDate) {
                $penalty = $post->PenaltyTillDate;
                $receivedAmount -= $post->PenaltyTillDate;
            } else {
                $penalty = $receivedAmount;
                $receivedAmount = 0;
            }
        }

        // Remainder goes to principal
        $principal = $receivedAmount;

        if ($loanaccount->status == "Closed") {
            return response()->json(["status" => "Loan account has been closed"], 200);
        }

        if (!$this->isDateBetween(date('Y-m-d', strtotime($post->loanDate)))) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'Access denied for this session'], 400);
        }

        $installmentsTillDate = LoanInstallment::where('LoanId', $post->id)
            ->whereDate('installmentDate', "<=", date('Y-m-d', strtotime($post->loanDate)))
            ->whereIn('status', ['False', 'Partial'])
            ->get();

        // Ledger master details
        if ($post->loanBy == "Transfer") {
            $ledgerMasterCR = LedgerMaster::find($post->ledgerId, ['groupCode', 'ledgerCode']);
            if (!$ledgerMasterCR) {
                return response()->json(['status' => "Invalid Bank or Type"]);
            }
        } else {
            $ledgerMasterCR = LedgerMaster::where('ledgerCode', "C002")->first(['groupCode', 'ledgerCode']);
        }

        DB::beginTransaction();
        try {
            $paidAmount = $post->ReceivedAmount - $clearedPendingInterest - $penalty - $interest - $post->overdue;
            $installmentIds = [];

            foreach ($installmentsTillDate as $installment) {
                $amountDue = $installment->principal + $installment->interest;
                $paidDate = date('Y-m-d', strtotime($post->loanDate));

                if ($paidAmount >= $amountDue) {
                    $installment->update([
                        "status" => "True",
                        "paid_date" => $paidDate,
                        "re_amount" => $amountDue
                    ]);
                    $paidAmount -= $amountDue;
                    $installmentIds[] = $installment->id;
                } else {
                    if ($paidAmount > 0) {
                        $installment->update([
                            "status" => "Partial",
                            "paid_date" => $paidDate,
                            "re_amount" => $amountDue - $paidAmount
                        ]);
                        $installmentIds[] = $installment->id;
                    }
                    break;
                }
            }

            // Clear pending interest if it was covered
            if ($clearedPendingInterest > 0) {
                LoanRecovery::where('loanId', $post->id)
                    ->where('is_delete', 'No')
                    ->update(['pendingInterest' => 0]);
            }

            // Save loan recovery entry
            $loan = LoanRecovery::create([
                'loanId' => $post->id,
                'receiptDate' => date('Y-m-d', strtotime($post->loanDate)),
                'principal' => $principal,
                'interest' => $interest,
                'pendingInterest' => $pendingInterest,
                'penalInterest' => $penalty,
                'total' => $post->TotalTillDate,
                'receivedAmount' => $post->ReceivedAmount,
                'overDueInterest' => $post->overdue,
                'status' => "True",
                'receivedBy' => $post->loanBy,
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'instaId' => implode(',', $installmentIds),
                'updatedBy' => $post->user()->id,
                'accountId' => $loanaccount->accountId,
                'accountNo' => $loanaccount->accountNo,
                // 'agentId' => $loanaccount->agentId,
            ]);

            // Ledger codes based on member type
            $curentintCode = $penalCode = $pendingCode = '';
             $LoanCode = DB::table('ledger_masters')
                ->select('ledger_masters.ledgerCode', 'group_masters.groupCode as group_code')
                ->leftJoin('group_masters', 'group_masters.groupCode', '=', 'ledger_masters.groupCode')
                ->where('ledger_masters.name', 'Loan Member')
                ->first();
            switch ($post->memberType) {
                case "Member":
                    $curentintCode = 'LONM002';
                    $penalCode = "LONM003";
                    $pendingCode = "LONM004";
                    break;
                case "NonMember":
                    $curentintCode = 'LONN002';
                    $penalCode = "LONN003";
                    $pendingCode = "LONN004";
                    break;
                case "Staff":
                    $curentintCode = 'LONS002';
                    $penalCode = "LONS003";
                    $pendingCode = "LONS004";
                    break;
            }

            $serialNo = "loan" . rand(1111111, 9999999);
            $baseInsert = [
                "serialNo" => $serialNo,
                "accountId" => $loanaccount->accountId,
                'accountNo' => $loanaccount->accountNo,
                'memberType' => $post->memberType,
                'agentId' => $post->agentId,
                'referenceNo' => $loan->id,
                'entryMode' => "manual",
                'transactionDate' => date('Y-m-d', strtotime($post->loanDate)),
                'formName' => "LoanReceipt",
                'branchId' => session('branchid') ?? 1,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => $post->user()->id,
            ];

            // Debit main account
            DB::table('general_ledgers')->insert(array_merge($baseInsert, [
                "ledgerCode" => $ledgerMasterCR->ledgerCode,
                "groupCode" => $ledgerMasterCR->groupCode,
                "transactionType" => 'Dr',
                "transactionAmount" => $post->ReceivedAmount,
                "narration" => $post->naration,
            ]));

            // Credit entries
            $creditEntries = [];

            if ($penalty > 0) {
                $creditEntries[] = array_merge($baseInsert, [
                    "ledgerCode" => $penalCode,
                    "groupCode" => 'INCM001',
                    "transactionType" => 'Cr',
                    "transactionAmount" => $penalty,
                    "narration" => $post->naration,
                ]);
            }

            if ($post->PendingIntrTillDate > 0) {
                $creditEntries[] = array_merge($baseInsert, [
                    "ledgerCode" => $curentintCode,
                    "groupCode" => 'INCM001',
                    "transactionType" => 'Cr',
                    "transactionAmount" => $post->PendingIntrTillDate,
                    "narration" => $post->naration,
                ]);
            }

            if ($interest > 0) {
                $creditEntries[] = array_merge($baseInsert, [
                    "ledgerCode" => $curentintCode,
                    "groupCode" => 'INCM001',
                    "transactionType" => 'Cr',
                    "transactionAmount" => $interest,
                    "narration" => $post->naration,
                ]);
            }

            if ($principal > 0) {
                $creditEntries[] = array_merge($baseInsert, [
                    "ledgerCode" => $LoanCode->ledgerCode,
                    "groupCode" => $LoanCode->group_code,
                    "transactionType" => 'Cr',
                    "transactionAmount" => $principal,
                    "narration" => $post->naration,
                ]);
            }

            if ($post->overdue > 0) {
                $creditEntries[] = array_merge($baseInsert, [
                    "ledgerCode" => $curentintCode,
                    "groupCode" => 'INCM001',
                    "transactionType" => 'Cr',
                    "transactionAmount" => $post->overdue,
                    "narration" => $post->naration,
                ]);
            }

            DB::table('general_ledgers')->insert($creditEntries);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'recovery' => LoanRecovery::where('loanId', $post->id)->where('is_delete', 'No')->get(),
                'message' => 'Loan updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => "failed",
                "message" => "Technical error: " . $e->getMessage()
            ], 200);
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
    public function deleteRecovery(Request $post)
    {
        $loanRecept = LoanRecovery::where('id', $post->id)->first();
        if (!$loanRecept) {
            return response()->json(["status" => "Some Technical issue occurred "], 200);
        }
        $installmetsIds = explode(",", $loanRecept->instaId);
        $installmets = LoanInstallment::whereIn('id', $installmetsIds)->get();
        $precheck =  DB::table('general_ledgers')->where('referenceNo', $post->id)->where('formName', 'LoanReceipt')->get();
        $result = $this->isDateBetween(date('Y-m-d', strtotime($loanRecept->receiptDate)));
        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
        }
        if (count($precheck) < 2) {
            return response()->json(["status" => "Some Technical issue occurred hii"], 200);
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
    }
    public function editRecovery(Request $post)
    {
        $data = DB::table('loan_recoveries')
            ->select('loan_recoveries.*', 'member_loans.loanAmount', 'member_loans.id as loanId')
            ->leftJoin('member_loans', 'member_loans.id', '=', 'loan_recoveries.loanId')
            ->where('loan_recoveries.id', $post->id)
            ->first();
        // dd($data);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function updaterecovery(Request $post)
    {
        // dd($post->all());
        $loanRecept = LoanRecovery::where('id', $post->edit_id)->first();
        if (!$loanRecept) {
            return response()->json(["status" => "Some Technical issue occurred haaaa"], 200);
        }
        $installmetsIds = explode(",", $loanRecept->instaId);
        $installmets = LoanInstallment::whereIn('id', $installmetsIds)->get();
        $precheck =  DB::table('general_ledgers')->where('referenceNo', $post->id)->where('formName', 'LoanReceipt')->get();
        $result = $this->isDateBetween(date('Y-m-d', strtotime($loanRecept->receiptDate)));
        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check your session', 'message' => "Please Check your session"], 400);
        }
        if (count($precheck) < 2) {
            return response()->json(["status" => "Some Technical issue occurred hiii"], 200);
        }
        $loanaccount =  MemberLoan::where('id', $post->loan_id)->first();
        if (!$loanaccount) {
            return response()->json(["status" => "Some Technical issue occurred huuu"], 200);
        }

        $this->loanstatus($loanaccount->id);
        $totalSum = $post->InterestTillDate + $post->PenaltyTillDate + $post->PendingIntrTillDate + $post->ReceivedAmount;

        if ($loanaccount->status == "Closed") {
            return response()->json(["status" => "Loan account has been closed"], 200);
        }
        if ($totalSum >= ($post->ReceivedAmount + $post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate)) {
            $pendingintrest = 0;
            $princple = $post->ReceivedAmount - ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate);
        } else {
            $princple = 0;
            $pendingintrest =  ($post->InterestTillDate + $post->PenaltyTillDate +   $post->PendingIntrTillDate) - $post->ReceivedAmount;
        }
        // dd($pendingintrest, $princple);

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

            $ledger_codes = DB::table('ledger_masters')
                ->where('loanmasterId', $loanaccount->loanType)
                ->get();

            $legders = '';
            foreach ($ledger_codes as $ledger_code) {
                if ($ledger_code->groupCode === 'INCM001') {
                    $legders = $ledger_code->ledgerCode;
                }
            }
            // $member = DB::table('')

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
                'loanId' => $post->loan_id,
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


            DB::commit();
            $loanrecovery =  LoanRecovery::where('loanId', $loanRecept->loanId)->where('is_delete', 'No')->get();
            $this->loanstatus($loanRecept->loanId);
            return response()->json([
                'status' => 'success',
                'recovery' => $loanrecovery,
                'message' => 'Loan updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return response()->json(['status' => "Some Technical issue occurred", "message" => "Some Technical issue occurred"], 200);
        }
    }
}
