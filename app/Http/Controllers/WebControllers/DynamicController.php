<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\LedgerMaster;
use App\Models\MemberSaving;
use Illuminate\Http\Request;
use App\Models\GeneralLedger;
use App\Models\MemberAccount;
use App\Models\CompulsoryDeposit;
use App\Models\MemberShare;
use App\Models\MemberFd;
use App\Models\MemberLoan;
use App\Models\LoanRecovery;
use Illuminate\Support\Facades\DB;

class DynamicController extends Controller
{

    public function getLedger(Request $request) {
        $ledgers = LedgerMaster::where('groupCode', $request->groupCode)->where('ledgerCode','!=', 'BANKFD01')->where('status','Active')->orderBy('name','ASC')->get();
        if (!empty($ledgers)) {
            return response()->json([
                'status' => true,
                'ledgers' => $ledgers
            ]);
        } else {
            return response()->json([
                'status' => false,
                'ledgers' => []
            ]);
        }
    }



public function resetAccountLedger(Request $post){

   $savingAccount = MemberSaving :: where('is_delete',"No")->get() ;
    dd($savingAccount) ;
   foreach($savingAccount as $savingAccounts){
        if($savingAccounts->transactionType == "Deposit"){
             $transactionAmount  = $savingAccounts->depositAmount ;
        }else{
             $transactionAmount  = $savingAccounts->withdrawAmount ;
        }

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "saving" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  = "SAVM001";
           $savingAccounts->ledgerCode  = "SAVM001";
           $savingAccounts->save() ;

            $ledger = new GeneralLedger();
            $ledger->serialNo = $serialNo;
            $ledger->accountId =  $acdetails->id;
            $ledger->accountNo = $savingAccounts->accountNo;
            $ledger->memberType = $savingAccounts->memberType;
            $ledger->formName = 'Saving';
            $ledger->referenceNo = $savingAccounts->id;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $savingAccounts->transactionDate;
            $ledger->transactionType = 'Dr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narration;
            $ledger->groupCode = ($savingAccounts->transactionType == 'Deposit') ? "C002" : "SAVM001";
            $ledger->ledgerCode = ($savingAccounts->transactionType == 'Deposit') ? "C002" : "SAVM001";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = $savingAccounts->agentId;
            $ledger->updatedBy = 1;
            $ledger->save();


            $ledger = new GeneralLedger();
            $ledger->serialNo =  $serialNo;
            $ledger->accountId = $acdetails->id;
            $ledger->accountNo = $savingAccounts->accountNo;
            $ledger->memberType =$savingAccounts->memberType;
            $ledger->formName = 'Saving';
            $ledger->referenceNo = $savingAccounts->id;;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $savingAccounts->transactionDate;
            $ledger->transactionType = 'Cr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narration;
            $ledger->groupCode = ($savingAccounts->transactionType == 'Deposit') ? "SAVM001" : "C002";
            $ledger->ledgerCode = ($savingAccounts->transactionType == 'Deposit') ? "SAVM001" : "C002";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = 1;
            $ledger->save();

        }

   }
   dd($savingAccount) ;
}


  public function resetCDSLedger(Request $post){

   $savingAccount = CompulsoryDeposit :: where('is_delete',"No")->get() ;

   foreach($savingAccount as $savingAccounts){
        if($savingAccounts->Deposit  > 0){
             $transactionAmount  = $savingAccounts->Deposit ;
             $transactionType = "Deposit" ;
        }else{
             $transactionAmount  = $savingAccounts->Withdraw ;
             $transactionType = "Withdraw" ;
        }

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accno])->first(['accountNo', 'id']);
        $check = GeneralLedger :: where("formName","CDS")->where("referenceNo",$savingAccounts->id)->first();
       if(!$check){
             if($acdetails){
           do {
             $serialNo = "cds" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  = "CDSM001";
           $savingAccounts->ledgerCode  = "CDSM001";
           $savingAccounts->save() ;

            $ledger = new GeneralLedger();
            $ledger->serialNo = $serialNo;
            $ledger->accountId =  $acdetails->id;
            $ledger->accountNo = $savingAccounts->accno;
            $ledger->memberType = $savingAccounts->membertype;
            $ledger->formName = 'CDS';
            $ledger->referenceNo = $savingAccounts->id;
            $ledger->entryMode = 'automatic';
            $ledger->transactionDate = $savingAccounts->date;
            $ledger->transactionType = 'Dr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narration;
            $ledger->groupCode = ($transactionType == 'Deposit') ? "BANK001" : "CDSM001";
            $ledger->ledgerCode = ($transactionType == 'Deposit') ? "HPSC907" : "CDSM001";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = $savingAccounts->agentId;
            $ledger->updatedBy = 1;
            $ledger->save();


            $ledger = new GeneralLedger();
            $ledger->serialNo =  $serialNo;
            $ledger->accountId = $acdetails->id;
            $ledger->accountNo = $savingAccounts->accno;
            $ledger->memberType =$savingAccounts->membertype;
            $ledger->formName = 'CDS';
            $ledger->referenceNo = $savingAccounts->id;;
            $ledger->entryMode = 'automatic';
            $ledger->transactionDate = $savingAccounts->date;
            $ledger->transactionType = 'Cr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narrartion;
            $ledger->groupCode = ($transactionType == 'Deposit') ? "CDSM001" : "BANK001";
            $ledger->ledgerCode = ($transactionType == 'Deposit') ? "CDSM001" : "HPSC907";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = 1;
            $ledger->save();

        }
       }


   }
   dd($savingAccount) ;
}



 public function resetShareLedger(Request $post){

   $savingAccount = MemberShare :: where('is_delete',"No")->get() ;
    dd($savingAccount) ;
    foreach($savingAccount as $savingAccounts){
        if($savingAccounts->transactionType == "Deposit"){
             $transactionAmount  = $savingAccounts->depositAmount ;
        }else{
             $transactionAmount  = $savingAccounts->withdrawAmount ;
        }

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "shr" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  =  "SHAM001";
           $savingAccounts->ledgerCode  = "SHAM001";
           $savingAccounts->save() ;

            $ledger = new GeneralLedger();
            $ledger->serialNo = $serialNo;
            $ledger->accountId =  $acdetails->id;
            $ledger->accountNo = $savingAccounts->accountNo;
            $ledger->memberType = $savingAccounts->memberType;
            $ledger->formName = 'Share';
            $ledger->referenceNo = $savingAccounts->id;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $savingAccounts->transactionDate;
            $ledger->transactionType = 'Dr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narration;
            $ledger->groupCode = ($savingAccounts->transactionType == 'Deposit') ? "BANK001" : "SHAM001";
            $ledger->ledgerCode = ($savingAccounts->transactionType == 'Deposit') ? "HPSC907" : "SHAM001";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = $savingAccounts->agentId;
            $ledger->updatedBy = 1;
            $ledger->save();


            $ledger = new GeneralLedger();
            $ledger->serialNo =  $serialNo;
            $ledger->accountId = $acdetails->id;
            $ledger->accountNo = $savingAccounts->accountNo;
            $ledger->memberType =$savingAccounts->memberType;
            $ledger->formName = 'Share';
            $ledger->referenceNo = $savingAccounts->id;;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate = $savingAccounts->transactionDate;
            $ledger->transactionType = 'Cr';
            $ledger->transactionAmount = $transactionAmount;
            $ledger->narration = $savingAccounts->narration;
            $ledger->groupCode = ($savingAccounts->transactionType == 'Deposit') ? "SHAM001" : "C002";
            $ledger->ledgerCode = ($savingAccounts->transactionType == 'Deposit') ? "SHAM001" : "C002";
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = 1;
            $ledger->save();

        }

   }
   dd($savingAccount) ;
}

 public function resetFDLedger(Request $post){

   $savingAccount = MemberFd :: where('is_delete',"No")->get() ;
     dd($savingAccount) ;
    foreach($savingAccount as $savingAccounts){


         $transactionAmount  = $savingAccounts->principalAmount ;

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "fd" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  =  "FDOM001";
           $savingAccounts->ledgerCode  = "FDOM001";
           $savingAccounts->save() ;

           $drLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Cr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "FDOM001",
                    'ledgerCode' => "FDOM001",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

            // Check if serialNo already exists
            $crLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Dr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "BANK001",
                    'ledgerCode' => "HPSC907",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

        }

   }

}


 public function resetMatureFdLedger(Request $post){

   $savingAccount = MemberFd :: where('is_delete',"No")->where('status','Matured')->get() ;
   dd($savingAccount) ;
    foreach($savingAccount as $savingAccounts){


         $transactionAmount  = $savingAccounts->principalAmount ;

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "fd" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);


           $drLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Cr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "BANK001",
                    'ledgerCode' => "HPSC907",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

            // Check if serialNo already exists
            $crLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Dr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "FDOM001",
                    'ledgerCode' => "FDOM001",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

        }

   }

}


public function resetRDLedger(Request $post){

   $savingAccount = MemberFd :: where('is_delete',"No")->get() ;
     dd($savingAccount) ;
    foreach($savingAccount as $savingAccounts){


         $transactionAmount  = $savingAccounts->principalAmount ;

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "fd" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  =  "FDOM001";
           $savingAccounts->ledgerCode  = "FDOM001";
           $savingAccounts->save() ;

           $drLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Dr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "FDOM001",
                    'ledgerCode' => "FDOM001",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

            // Check if serialNo already exists
            $crLedger = GeneralLedger::updateOrCreate(
                ['serialNo' => $serialNo, 'transactionType' => 'Cr'],
                [
                    'accountId' => $acdetails->id,
                    'accountNo' => $savingAccounts->accountNo,
                    'memberType' => "Member",
                    'formName' => 'fd',
                    'referenceNo' => $savingAccounts->id,
                    'entryMode' => 'Manual',
                    'transactionDate' => $savingAccounts->openingDate,
                    'transactionAmount' => $savingAccounts->principalAmount,
                    'narration' => $savingAccounts->narration,
                    'groupCode' => "C002",
                    'ledgerCode' => "C002",
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'agentId' => $savingAccounts->agentId,
                    'updatedBy' => "1",
                ]
            );

        }

   }

}



public function resetLoanLedger(Request $post){

   $savingAccount = MemberLoan :: where('is_delete',"No")->get() ;
    dd($savingAccount) ; exit ;
    foreach($savingAccount as $savingAccounts){

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "loan" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  =  "LONM001";
           $savingAccounts->ledgerCode  = "LONN001";
           $savingAccounts->save() ;

           DB::table('general_ledgers')->insert([
                        "serialNo" => $serialNo,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $acdetails->memberType,
                        'agentId' =>  0,
                        "ledgerCode"   => "C002",
                        'groupCode' =>  "C002",
                        'referenceNo' => $savingAccounts->id,
                        'entryMode' => "automatic",
                        "formName"        => "Loan Disbursed",
                        'transactionDate' => $savingAccounts->loanDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $savingAccounts->loanAmount,
                        'narration' => "Loan Disbursed",
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => 1,
                    ]);

                    DB::table('general_ledgers')->insert([
                        "serialNo" => $serialNo,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $acdetails->memberType,
                        'agentId' =>  0,
                        "ledgerCode"   => "LONM001",
                        'groupCode' =>  "LONM001",
                        'referenceNo' =>  $savingAccounts->id,
                        'entryMode' => "automatic",
                        "formName" => "Loan Disbursed",
                        'transactionDate' => $savingAccounts->loanDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $savingAccounts->loanAmount,
                        'narration' => "Loan Disbursed",
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => 1,
                    ]);

        }

   }

}

public function resetLoanReceptLedger(Request $post){

   $savingAccount = LoanRecovery :: where('is_delete',"No")->get() ;
   dd($savingAccount) ;
    foreach($savingAccount as $savingAccounts){

         $acdetails = MemberAccount::where(['accountNo' => $savingAccounts->accountNo])->first(['accountNo', 'id']);
        if($acdetails){
           do {
             $serialNo = "loan" . rand(1111111, 9999999);
            } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);
           $savingAccounts->accountId  = $acdetails->id;
           $savingAccounts->serialNo  = $serialNo;
           $savingAccounts->groupCode  =  "LONM001";
           $savingAccounts->ledgerCode  = "LONN001";
           $savingAccounts->save() ;

           DB::table('general_ledgers')->insert([
                        "serialNo" => $serialNo,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $acdetails->memberType,
                        'agentId' =>  0,
                        "ledgerCode"   => "C002",
                        'groupCode' =>  "C002",
                        'referenceNo' => $savingAccounts->id,
                        'entryMode' => "automatic",
                        "formName"        => "Loan Disbursed",
                        'transactionDate' => $savingAccounts->loanDate,
                        'transactionType' => 'Cr',
                        'transactionAmount' => $savingAccounts->loanAmount,
                        'narration' => "Loan Disbursed",
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => 1,
                    ]);

                    DB::table('general_ledgers')->insert([
                        "serialNo" => $serialNo,
                        'accountNo' => $acdetails->accountNo,
                        "accountId"  =>  $acdetails->id,
                        'memberType' => $acdetails->memberType,
                        'agentId' =>  0,
                        "ledgerCode"   => "LONM001",
                        'groupCode' =>  "LONM001",
                        'referenceNo' =>  $savingAccounts->id,
                        'entryMode' => "automatic",
                        "formName" => "Loan Disbursed",
                        'transactionDate' => $savingAccounts->loanDate,
                        'transactionType' => 'Dr',
                        'transactionAmount' => $savingAccounts->loanAmount,
                        'narration' => "Loan Disbursed",
                        'branchId' =>  session('branchid') ?? 1,
                        'sessionId' => session('sessionId') ?? 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updatedBy' => 1,
                    ]);

        }

   }

}





// public function loop(Request $request) {


//     DB::beginTransaction();

//     try {

       
//     $loandisbursed=DB::table('member_loans')->where('loanDate','>=','2023-04-01')->get();


//     foreach($loandisbursed as $loandislist){
    
//         echo $loandislist->accountNo; echo "<br>";

        
//         $accountNo=DB::table('opening_accounts')->where('membershipno','=',$loandislist->accountNo)->where('accountname','=','Saving')->value('accountNo');

//         echo $accountNo; echo "<br><br>";   

//         $add=new MemberSaving();
//         $add->serialNo = $loandislist->serialNo;
//         $add->accountId = $loandislist->accountNo;
//         $add->accountNo = $accountNo;
//         $add->memberType = $loandislist->memberType;
//         $add->groupCode = 'SAVM001';
//         $add->ledgerCode = 'LGRSAV01';
//         $add->secheme_id = 25;
//         $add->transactionDate = $loandislist->loanDate;
//         $add->transactionType = 'Deposit';
//         $add->depositAmount = $loandislist->loanAmount;
//         $add->withdrawAmount = 0;
//         $add->paymentType = 'SAVM001';
//         $add->bank = 'LGRSAV01';
//         $add->chequeNo = 'LoanTrfdSaving';
//         $add->narration = 'Loan Trfd To Saving';
//         $add->branchId = 1;
//         $add->agentId = 1;
//         $add->sessionId = 1;
//         $add->updatedBy = 1;
//         $add->is_delete = 'No';
//         $add->save();


//         $gerenal_ledger_delete = DB::table('general_ledgers')->where('serialNo',$loandislist->serialNo)->where('referenceNo',$loandislist->id)->delete();

//         DB::table('general_ledgers')->insert([
//             "serialNo" => $loandislist->serialNo,
//             'accountNo' => $accountNo,
//             "accountId"  =>  $loandislist->accountNo,
//             'memberType' => $loandislist->memberType,
//             'agentId' =>  1,
//             "groupCode"   => "SAVM001",
//             'ledgerCode' =>  "LGRSAV01",
//             "formName"        => "Loan Disbursed", 
//             'referenceNo' => $loandislist->id,
//             'entryMode' => "automatic",
//             'transactionDate' => $loandislist->loanDate,
//             'transactionType' => 'Cr',
//             'transactionAmount' => $loandislist->loanAmount,
//             'narration' => "Loan Disbursed",
//             'branchId' =>   1,
//             'sessionId' => 1,
//             'created_at' => date('Y-m-d H:i:s'),
//             'updatedBy' => 1,
//         ]);

//         DB::table('general_ledgers')->insert([
//             "serialNo" => $loandislist->serialNo,
//             'accountNo' => $accountNo,
//             "accountId"  =>  $loandislist->accountNo,
//             'memberType' => $loandislist->memberType,
//             'agentId' =>  1,
//             "groupCode"   => $loandislist->groupCode,
//             'ledgerCode' =>  $loandislist->ledgerCode,
//             "formName"        => "Loan Disbursed", 
//             'referenceNo' => $loandislist->id,
//             'entryMode' => "automatic",
//             'transactionDate' => $loandislist->loanDate,
//             'transactionType' => 'Dr',
//             'transactionAmount' => $loandislist->loanAmount,
//             'narration' => "Loan Disbursed",
//             'branchId' =>   1,
//             'sessionId' => 1,
//             'created_at' => date('Y-m-d H:i:s'),
//             'updatedBy' => 1,
//         ]);

       




//     }


//                 DB::commit();
//             } catch (Exception $e) {
//                 DB::rollBack();
//                 Log::error('Transaction failed: ' . $e->getMessage());
//             }
// }

}
