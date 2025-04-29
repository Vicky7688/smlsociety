<?php

namespace App\Jobs;

use DateTime;
use DateInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MemberAccount;
use App\Models\Mis;
use App\Models\ReCurringRd;
use App\Models\MemberSaving;
use App\Models\GeneralLedger;
use App\Models\MemberLoan;
use App\Models\LedgerMaster;
use App\Models\RdInstallment;
use App\Models\MisIntallment;
use App\Console\Commands\CronMisInterestCalculation;

class MisCalculationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id,$type,$todaydate;
    /**
     * Create a new job instance.
     */
    public function __construct($id,$type,$todaydate)
    {
        $this->id=$id;
        $this->type=$type;
        $this->todaydate=$todaydate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $id=$this->id;
        $type=$this->type;
        $todaydate=$this->todaydate;
        $mistable = Mis::where(['id'=>$id])->first();
        $misid = $mistable->id;result("que run");
        if($type == "RD"){
            $RDTable = ReCurringRd::where(['misid'=>$misid])->first();
            $check = RdInstallment::where(['rd_id' => $RDTable->id])->orderBy('intallment_no', 'desc')->first();
            if($check->payment_status == "paid"){
                $mistable->cron_status = 'success';
                $mistable->save();
            }else{
                $rdInstallment = RdInstallment::where(['rd_id'=>$RDTable->id,'payment_status'=>'pending'])->first();
                if($rdInstallment){
                    if($rdInstallment->installment_date == $todaydate){result("work");
                        $installmentamount = $rdInstallment->amount;
                        //RD PAID
                        $rdrand = "RD" . rand(1111111, 9999999);
                        $rdInstallment->paid_amount =$installmentamount;
                        $rdInstallment->payment_date = $todaydate;
                        $rdInstallment->serialNo=$rdrand;
                        $rdInstallment->payment_status='paid';
                        $rdInstallment->save();
                        //LEDGER PAID
                        
                        $account_type=$mistable->member_type;
                        if ($account_type == 'Member') {
                            $groupCode_1 = 'EXPN001';
                            $ledgerCode_1 = 'MISM002';
        
                            $groupCode_2 = 'RDOM002';
                            $ledgerCode_2 = 'RDOM002';
                        } else if ($account_type == 'NonMember') {
                            $groupCode_1 = 'EXPN001';
                            $ledgerCode_1 = 'MISN002';
        
                            $groupCode_2 = 'RDON002';
                            $ledgerCode_2 = 'RDON002';
                        } else {
                            $groupCode_1 = 'EXPN001';
                            $ledgerCode_1 = 'MISF002';
        
                            $groupCode_2 = 'RDOS002';
                            $ledgerCode_2 = 'RDOS002';
                        }
        
                        $ledger = new GeneralLedger();
                        $ledger->serialNo = $rdrand;
                        $ledger->accountId =  $RDTable->accountId;
                        $ledger->accountNo = $RDTable->rd_account_no;
                        $ledger->memberType = $RDTable->memberType;
                        $ledger->formName = 'RD';
                        $ledger->referenceNo = $rdInstallment->id;
                        $ledger->entryMode = 'automatic';
                        $ledger->transactionDate =$todaydate;
                        $ledger->transactionType = 'Dr';
                        $ledger->transactionAmount = $installmentamount;
                        $ledger->groupCode =$groupCode_1;
                        $ledger->ledgerCode =$ledgerCode_1;
                        $ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
                        $ledger->save();
        
        
                        $ledger = new GeneralLedger();
                        $ledger->serialNo = $rdrand;
                        $ledger->accountId =  $RDTable->accountId;
                        $ledger->accountNo = $RDTable->rd_account_no;
                        $ledger->memberType = $RDTable->memberType;
                        $ledger->formName = 'RD';
                        $ledger->referenceNo = $rdInstallment->id;
                        $ledger->entryMode = 'automatic';
                        $ledger->transactionDate =$todaydate;
                        $ledger->transactionType = 'Cr';
                        $ledger->transactionAmount = $installmentamount;
                        $ledger->groupCode =$groupCode_2;
                        $ledger->ledgerCode =$ledgerCode_2;
                        $ledger->branchId = session('branchId') ? session('branchId') : 1;
                        $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
                        $ledger->save();
        
                        
                        $misinstallment = MisIntallment::where(['mis_id'=>$misid,'type'=>'RD','status'=>'pending'])->first();
                        $misinstallment->status = 'paid';
                        $misinstallment->serialNo=$rdrand;
                        $misinstallment->receipt_date = $todaydate;
                        $misinstallment->save();

                        $mistable->cron_status = 'processing';
                        $mistable->save();

                    }
                }
            }

                        
        }elseif($type == "Saving"){
            $savingrand = "saving" . rand(1111111, 9999999);
            $account_no = $mistable->account_no;
            $account_type=$mistable->member_type;
            if ($account_type == 'Member') {
                $groupCode = 'SAVM001';
                $ledgerCode = 'SAVM001';
            } else if ($account_type == 'NonMember') {
                $groupCode = 'SAVN001';
                $ledgerCode = 'SAVN001';
            } else {
                $groupCode = 'SAVS001';
                $ledgerCode = 'SAVS001';
            }
            $accountId =MemberAccount::where(['accountNO'=>$account_no,'memberType'=>$account_type])->first();
            $misinstallmentSaving = MisIntallment::where(['mis_id'=>$misid,'type'=>'Saving','status'=>'pending','installment_date'=>$todaydate])->first();
            if($misinstallmentSaving){
                $savingacc = new MemberSaving;
                $savingacc->serialNo =$savingrand;
                $savingacc->accountId= $accountId->id;
                $savingacc->accountNo=$account_no;
                $savingacc->memberType=$account_type;
                $savingacc->groupCode=$groupCode;
                $savingacc->ledgerCode=$ledgerCode;
                $savingacc->savingNo=$mistable->SavingRdAccountNumber;
                $savingacc->transactionDate=date('Y-m-d', strtotime($mistable->date. ' +1 month'));
                $savingacc->transactionType='Deposit';
                $savingacc->depositamount=$mistable->monthly_interest;
                $savingacc->branchId = session('branchId') ? session('branchId') : 1;
                $savingacc->sessionId =  session('sessionId') ? session('sessionId') : 1;
                $savingacc->save();
    
                $savingId = $savingacc->id;
    
                $ledger = new GeneralLedger();
                $ledger->serialNo = $savingrand;
                $ledger->accountId =  $accountId->id;
                $ledger->accountNo = $account_no;
                $ledger->memberType = $account_type;
                $ledger->formName = 'Saving';
                $ledger->referenceNo = $savingId;
                $ledger->entryMode = 'automatic';
                $ledger->transactionDate =date('Y-m-d', strtotime($mistable->date. ' +1 month'));
                $ledger->transactionType = 'Cr';
                $ledger->transactionAmount = $mistable->monthly_interest;
                $ledger->groupCode = $groupCode;
                $ledger->ledgerCode = $ledgerCode;
                $ledger->save();
    
                $ledger = new GeneralLedger();
                $ledger->serialNo = $savingrand;
                $ledger->accountId =  $accountId->id;
                $ledger->accountNo = $account_no;
                $ledger->memberType = $account_type;
                $ledger->formName = 'Saving';
                $ledger->referenceNo = $savingId;
                $ledger->entryMode = 'automatic';
                $ledger->transactionDate =date('Y-m-d', strtotime($mistable->date. ' +1 month'));
                $ledger->transactionType = 'Dr';
                $ledger->transactionAmount = $mistable->monthly_interest;
                $ledger->groupCode = "C002";
                $ledger->ledgerCode = "C002";
                $ledger->save();
    
                $misinstallment = MisIntallment::where(['mis_id'=>$misid,'type'=>'Saving','status'=>'pending'])->first();
                $misinstallment->status = 'paid';
                $misinstallment->serialNo=$savingrand;
                $misinstallment->receipt_date = $todaydate;
                $misinstallment->save();
    
                $installmentsaving = MisIntallment::where(['mis_id'=>$misid])->orderBy('installment_no', 'desc')->first();
                if($installmentsaving->status == 'paid'){
                    $mistable->cron_status = 'success';
                    $mistable->save();
                }else{
                    $mistable->cron_status = 'processing';
                    $mistable->save();
                }
            }
            
            
    
        }elseif($type == "Loan"){
            // $loanrand = "loan" . rand(1111111, 9999999);
            // $account_no = $mistable->account_no;
            // $account_type=$mistable->member_type;
            // if ($account_type == 'Member') {
            //     $groupCode = 'LONM001';
            //     $ledgerCode = 'LONM001';
            // } else if ($account_type == 'NonMember') {
            //     $groupCode = 'LONN001';
            //     $ledgerCode = 'LONN001';
            // } else {
            //     $groupCode = 'LONS001';
            //     $ledgerCode = 'LONS001';
            // }
            // $accountId =MemberAccount::where(['accountNO'=>$account_no,'memberType'=>$account_type])->first();
            // $Member_loan = new MemberLoan;
            // $Member_loan->serialNo = $loanrand;
            // $Member_loan->accountId = $accountId->id;
            // $Member_loan->accountNo = $account_no;
            // $Member_loan->loanDate = 
            // $Member_loan->loanEndDate = 
            // $Member_loan->memberType = $account_type;
            // $Member_loan->groupCode = $groupCode;
            // $Member_loan->ledgerCode = $ledgerCode;
            // $Member_loan->purpose = 
            // $Member_loan->loanType = 
            // $Member_loan->processingFee = 
            // $Member_loan->cropType="Cash";
            // $Member_loan->loanyear = 
            // $Member_loan->loanMonth =
            // $Member_loan->loanInterest =
            // $Member_loan->loanAmount = 
            // $Member_loan->pernote = $account_no;
            // $Member_loan->loanBy = "Cash";
            // $Member_loan->installmentType = 
            // $Member_loan->status = "Closed";
            // $Member_loan->save();

        }
        
    }
}
