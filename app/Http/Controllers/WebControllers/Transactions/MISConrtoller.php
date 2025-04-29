<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgentMaster;
use App\Models\MemberAccount;
use App\Models\Mis;
use App\Models\ReCurringRd;
use App\Models\MemberSaving;
use App\Models\GeneralLedger;
use App\Models\MemberLoan;
use App\Models\LedgerMaster;
use App\Models\RdInstallment;
use App\Models\MisIntallment;
use DateTime;
use DateInterval;

class MISConrtoller extends Controller
{
    public function index(){
        $agent = AgentMaster::all();
        return view('transaction.mis',['agent'=>$agent]);
    }

    public function searchaccountlist(Request $request){
        $accountNo = $request->accountno;
        $membertype = $request->membertype;
        $output = '';

        if (empty($accountNo)) {
            $output .='<li class="list-group-item memberlist"></li>';
            return response()->json([
                'status' => true,
                'data' => $output
            ]);
        }

        $query = MemberAccount::where('accountNo', 'LIKE', $accountNo.'%');

        if (!empty($membertype)) {
            $query->where('membertype', $membertype);
        }

        $data = $query->get();

        if (count($data) > 0) {
            $output = '<ul class="list-group membersearch" style="display:block;z-index:1">';
            foreach ($data as $row) {
                $output .= '<li class="list-group memberlist">'.$row->accountNo.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .='<li class="list-group-item memberlist">No Data Found</li>';
        }

        return response()->json(['status' => 'success', 'data' => $output]);
    }

    public function getaccountdetails(Request $request){
        $accountNo = $request->accountno;
        $membertype = $request->membertype;
        if(!empty($accountNo)){
            $member =  MemberAccount::where(['accountNo'=>$accountNo,'membertype'=>$membertype])->first();
            if(!empty($member)){
                if($member->status == "Active"){
                    $name = $member->name;
                    $fatherhusband = $member->fatherName;
                    $address = $member->address;
                }else if($member->status == "Transfer"){
                    $Transfer_account = TransferedAccount::where(['accountId'=>$member->id,'membertype'=>$membertype])->first();
                    $name = $Transfer_account->name;
                    $fatherhusband = $Transfer_account->fatherName;
                    $address = $Transfer_account->address;
                }
                $MIS_details = Mis::where(['account_no'=>$accountNo,'member_type'=>$membertype])->get();
                if(count($MIS_details) > 0){
                    return response()->json(['status'=>'success','name'=>$name,'fathername'=>$fatherhusband,'address'=>$address,'tabledata'=>$MIS_details]);
                }else{
                    return response()->json(['status'=>'success','name'=>$name,'fathername'=>$fatherhusband,'address'=>$address]);  
                }
            }else{
                return response()->json(['status'=>'fail','message'=>'Account No Not Found']);
            }
        }
    }

    public function interestdepositcheck(Request $request){
        $account_type = $request->type;
        $account_no = $request->accno;
        $account_membertype = $request->membertype;
     
        if($account_type == "Saving"){
            $query = MemberSaving::where(['accountNo'=>$account_no,'memberType'=>$account_membertype])->first();
            if(!empty($query)){
                
                $saving_accountno = $query->accountNo;
                return response()->json(['status'=>'success','savingno'=>$saving_accountno]);
            }else{
                return response()->json(['status'=>'success','savingno'=>$account_no]);
            }
            
        }else if($account_type == "Loan"){
            $loanacc=MemberLoan::where(['accountNO'=>$account_no,'memberType'=>$account_membertype,'status'=>'Disbursed'])->first();
            if($loanacc){
               return response()->json(['status'=>'success','loanano'=>$account_no]);
            }
        }else if($account_type == "RD"){
            $query = MemberAccount::where(['accountNo'=>$account_no,'memberType'=>$account_membertype])->first();
            if(!empty($query)){
                return response()->json(['status'=>'success','rdA/no'=>$account_no]);
            }else{
                return response()->json(['status'=>'success','savingno'=>$account_no]);
            }

        }
    }

    public function storemispagedata(Request $request){
      
        $account_no = $request->account_no;
        $account_type = $request->mis_member_type;
        $account_mis_no = $request->mis_account_no;

        $openingdate = date('Y-m-d',strtotime($request->mis_opening_date));
        $maturity_date =date('Y-m-d',strtotime($request->mis_maturity_date)); 

        $rdopeningdate = date('Y-m-d',strtotime($request->mis_opening_date));
        $rdmaturity_date =date('Y-m-d',strtotime($request->mis_maturity_date)); 

        $rdopening_datemodify = date('Y-m-d', strtotime($rdopeningdate . ' +1 month'));
        $rdmaturity_datemodify = date('Y-m-d', strtotime($rdmaturity_date . ' +1 month'));
       
        $account_depost = $request->mis_interest_deposite_type;
        if($account_depost == "Saving"){
            $saving_ac_no = $request->saving_rd_ac_no;
            if(empty($saving_ac_no)){
                return response()->json(['status'=>'fail','message'=>'Saving Acount Not Found !!']);
            }else{
                $savesavingrdloan = $saving_ac_no;    
            }
        }else if($account_depost == "RD"){
            $saving_ac_no = $request->rd_input_0;
            $rd_interest = $request->rd_input_1;
            $rd_lf_no = $request->rd_input_2;
            $rd_page_no = $request->rd_input_3;
            $rd_maturity_amount = $request->rd_input_4;
            if($saving_ac_no){
                $checkrd = ReCurringRd::where(['rd_account_no'=>$saving_ac_no])->first();
                if($checkrd){
                    return response()->json(['status'=>'fail','message'=>'Account No already Exist !!']);
                }else{
                    $savesavingrdloan = $saving_ac_no;
                }
            }else{
                return response()->json(['status'=>'fail','message'=>'Please Enter RD Account No !!']);
            }
        }else if($account_depost == "Loan"){
            $loanaccountno = $request->loan_rd_ac_no;
            $savesavingrdloan = $loanaccountno;
        }

        if($request->payment_type == "Bank"){
            if(empty($request->payment_bank)){
                return response()->json(['status'=>'fail','message'=>'Please select Bank Branch First !!']);
            }
        }

        $check = MemberAccount::where(['accountNO'=>$account_no,'memberType'=>$account_type])->first();
       
        if($check){
            $Misrand = "Mis" . rand(1111111, 9999999);
            $misdata = new Mis;
            $misdata->serialNo=$Misrand; 
            $misdata->date=$openingdate;
            $misdata->member_type=$account_type;
            $misdata->account_no=$account_no;
            $misdata->mis_ac_no=$account_mis_no;
            $misdata->amount=$request->mis_amount;
            $misdata->interest=$request->mis_interest_rate;
            $misdata->period_year=$request->mis_period_year;
            $misdata->period_month=$request->mis_period_month;
            $misdata->TotalInterest=$request->mis_total_interest;
            $misdata->monthly_interest=$request->MonthInterest;
            $misdata->payment_type=$request->payment_type;
            if ($request->payment_type == 'Bank') {
                $misdata->groupCode = "BANK001";
                $misdata->ledgerCode =$request->payment_bank;
            } elseif ($request->payment_type == 'Cash') {
                $misdata->groupCode = "C002";
                $misdata->ledgerCode = "C002";
            } 
            $misdata->maturity_date=$maturity_date;
            $misdata->maturity_amount=$request->mis_maturity_amount;
            $misdata->interest_deposite=$request->mis_interest_deposite_type;
            $misdata->SavingRdAccountNumber=$savesavingrdloan;
            if($account_depost == "RD"){
                $misdata->rd_interestROI = $rd_interest;
                $misdata->rd_interest = $rd_maturity_amount;
            }
            $misdata->status="Active";
            $misdata->maturity_date=$request->mis_maturity_date;
            $misdata->updatedBy=auth()->user()->id;
            $misdata->save();

            //Genral Ledger Entry 
            if ($account_type == 'Member') {
                $groupCode = 'MISM001';
                $ledgerCode = 'MISM001';
            } else if ($account_type == 'NonMember') {
                $groupCode = 'MISN001';
                $ledgerCode = 'MISN001';
            } else {
                $groupCode = 'MISS001';
                $ledgerCode = 'MISS001';
            }

            $misgenralid = $misdata->id;

            if($account_depost == "RD"){
                $accountId =MemberAccount::where(['accountNO'=>$account_no,'memberType'=>$account_type])->first();
                $RDAccount = new ReCurringRd;
                $RDAccount->memberType = $account_type;
                $RDAccount->accountId = $accountId->id;
                $RDAccount->rd_account_no = $saving_ac_no;
                $RDAccount->amount = $request->MonthInterest;
                $RDAccount->month=$request->mis_period_month;
                $RDAccount->date =$rdopening_datemodify;
                $RDAccount->ledger_folio_no=$rd_lf_no;
                $RDAccount->rd_created_from="MIS";
                $RDAccount->misid=$misgenralid;
                $RDAccount->interest=$rd_interest;
                $RDAccount->maturity_date=$rdmaturity_datemodify;
                $RDAccount->maturity_amount=$rd_maturity_amount;
                $RDAccount->status="Active";
                $RDAccount->updatedBy=auth()->user()->id;
                $RDAccount->save();

                //Rd InstallMents
                $interest = $rd_interest;
                $amount = $request->MonthInterest;
                $months = $request->mis_period_month;
                $start_date = $rdopening_datemodify;
                $startDate = new DateTime($start_date);
                for ($i = 1; $i <= $months; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));
                
                    $installmentsdata = new RdInstallment;
                    $installmentsdata->rd_id = $RDAccount->id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = 1;
                    $installmentsdata->sessionId=1;
                    $installmentsdata->updatedBy=auth()->user()->id;
                    $installmentsdata->save();

                    $MisInstallment = new MisIntallment;
                    $MisInstallment->mis_id=$misgenralid;
                    $MisInstallment->installment_date = $date;
                    $MisInstallment->installment_amount = $amount; 
                    $MisInstallment->installment_no = $i;
                    $MisInstallment->type = "RD";
                    $MisInstallment->status = 'pending';
                    $MisInstallment->save();
                } 
            }else if($account_depost == "Saving"){
                $amount = $request->MonthInterest;
                $months = $request->mis_period_month;
                $start_date = $rdopening_datemodify;
                $startDate = new DateTime($start_date);
                for ($i = 1; $i <= $months; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $MisInstallment = new MisIntallment;
                    $MisInstallment->mis_id=$misgenralid;
                    $MisInstallment->installment_date = $date;
                    $MisInstallment->installment_amount = $amount; 
                    $MisInstallment->installment_no = $i;
                    $MisInstallment->type = "Saving";
                    $MisInstallment->status = 'pending';
                    $MisInstallment->save();
                }
            }else if($account_depost == "Loan"){
                $amount = $request->MonthInterest;
                $months = $request->mis_period_month;
                $start_date = $rdopening_datemodify;
                $startDate = new DateTime($start_date);
                for ($i = 1; $i <= $months; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $MisInstallment = new MisIntallment;
                    $MisInstallment->mis_id=$misgenralid;
                    $MisInstallment->installment_date = $date;
                    $MisInstallment->installment_amount = $amount; 
                    $MisInstallment->installment_no = $i;
                    $MisInstallment->type = "Loan";
                    $MisInstallment->status = 'pending';
                    $MisInstallment->save();
                }
            }

            $ledger = new GeneralLedger();
            $ledger->serialNo = $Misrand;
            $ledger->accountId =  $check->id;
            $ledger->accountNo = $account_no;
            $ledger->memberType = $account_type;
            $ledger->formName = 'Mis';
            $ledger->referenceNo = $misgenralid;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate =$openingdate;
            $ledger->transactionType = 'Dr';
            $ledger->transactionAmount = $request->mis_amount; 
            if ($request->payment_type == 'Bank') {
                $ledger->groupCode = "BANK001";
                $ledger->ledgerCode =$request->payment_bank;
            } elseif ($request->payment_type == 'Cash') {
                $ledger->groupCode = "C002";
                $ledger->ledgerCode = "C002";
            } 
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = $request->user()->id;
            $ledger->save();

            $ledger = new GeneralLedger();
            $ledger->serialNo = $Misrand;
            $ledger->accountId =  $check->id;
            $ledger->accountNo = $account_no;
            $ledger->memberType = $account_type;
            $ledger->formName = 'Mis';
            $ledger->referenceNo = $misgenralid;
            $ledger->entryMode = 'Manual';
            $ledger->transactionDate =$openingdate;
            $ledger->transactionType = 'Cr';
            $ledger->transactionAmount = $request->mis_amount;
            $ledger->groupCode = $groupCode;
            $ledger->ledgerCode = $ledgerCode;
            $ledger->branchId = session('branchId') ? session('branchId') : 1;
            $ledger->sessionId =  session('sessionId') ? session('sessionId') : 1;
            $ledger->agentId = 1;
            $ledger->updatedBy = $request->user()->id;
            $ledger->save();

            return response()->json(['status'=>'success','message'=>'MIS Account Created Successfully !!']);
        }
    }

    public function getaccountdata(Request $request){
        $query = Mis::where(['id'=>$request->id])->first(['date','member_type','account_no','mis_ac_no','amount','interest','period_year','period_month','TotalInterest','monthly_interest','maturity_date','maturity_amount','interest_deposite','SavingRdAccountNumber','status','rd_interest','rd_interestROI','payment_type','ledgerCode']);
        return response()->json(['status'=>'success','data'=>$query]);
    }

    public function getbankdetails(Request $request){
        $bank=$request->value;
        if($bank == "Bank"){
            $data = LedgerMaster::where(['groupCode'=>'BANK001'])->get();
            return response()->json(["status"=>"success",'bank'=>$data]);
        }
    }

    public function getupdatemisdetails(Request $request){
        $id = $request->updateid;
        $query = Mis::where(['id'=>$id])->first();
        if($query->cron_status == "pending"){
            if($request->payment_type == "Bank"){
                if(empty($request->payment_bank)){
                    return response()->json(['status'=>'fail','message'=>'Please select Bank Branch First !!']);
                }
            }
            $maturity_date =date('Y-m-d',strtotime($request->mis_maturity_date)); 
            $opening_date =date('Y-m-d',strtotime($request->mis_opening_date));

            $query->date = $opening_date;
            $query->member_type = $request->mis_member_type;
            $query->account_no = $request->account_no;
            $query->mis_ac_no = $request->mis_account_no;
            $query->amount = $request->mis_amount;
            $query->interest = $request->mis_interest_rate;
            $query->period_year = $request->mis_period_year;
            $query->period_month = $request->mis_period_month;
            $query->TotalInterest = $request->mis_total_interest;
            $query->monthly_interest = $request->MonthInterest;
            $query->maturity_date = $maturity_date;
            $query->maturity_amount = $request->mis_maturity_amount;
            if($request->payment_type == "Cash"){
                $query->groupCode = "C002";
                $query->ledgerCode = "C002";
            }else if($request->payment_type == "Bank"){
                $query->groupCode = "BANK001";
                $query->ledgerCode =$request->payment_bank;
            }
            $query->payment_type = $request->payment_type;
            if($request->mis_interest_deposite_type == "Saving"){
                $query->interest_deposite="Saving";
                $query->SavingRdAccountNumber = $request->saving_rd_ac_no;
                $updateinstallmentsSaving = MisIntallment::where(['mis_id'=>$id])->delete();
                $amount = $request->MonthInterest;
                $months = $request->mis_period_month;
                $start_date = $rdopening_datemodify;
                $startDate = new DateTime($start_date);
                for ($i = 1; $i <= $months; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));

                    $MisInstallment = new MisIntallment;
                    $MisInstallment->mis_id=$misgenralid;
                    $MisInstallment->installment_date = $date;
                    $MisInstallment->installment_amount = $amount; 
                    $MisInstallment->installment_no = $i;
                    $MisInstallment->type = "Saving";
                    $MisInstallment->status = 'pending';
                    $MisInstallment->save();
                }
            }else if($request->mis_interest_deposite_type == "RD"){
                $query->interest_deposite="RD";
                $query->SavingRdAccountNumber = $request->rd_input_0;
                $query->rd_interestROI =  $request->rd_input_1;
                $query->rd_interest = $request->rd_input_4;

                $RDAccount =ReCurringRd::where(['misid'=>$id])->first();
                $accountId =MemberAccount::where(['accountNO'=>$request->account_no,'memberType'=>$request->mis_member_type])->first();
                $rdopening_datemodify = date('Y-m-d', strtotime($request->mis_opening_date . ' +1 month'));
                $rdmaturity_datemodify = date('Y-m-d', strtotime( $maturity_date. ' +1 month'));

                $RDAccount->memberType = $request->mis_member_type;
                $RDAccount->accountId = $accountId->id;
                $RDAccount->rd_account_no = $request->rd_input_0;//
                $RDAccount->amount = $request->MonthInterest;
                $RDAccount->month=$request->mis_period_month;
                $RDAccount->date =$rdopening_datemodify;
                $RDAccount->interest=$request->rd_input_1;
                $RDAccount->maturity_date=$rdmaturity_datemodify;
                $RDAccount->maturity_amount=$request->rd_input_4;
                $RDAccount->save();

                $installments = RdInstallment::where(['rd_id' => $RDAccount->id])->delete();
                $updateinstallmentsRd = MisIntallment::where(['mis_id'=>$id])->delete();

                $interest = $request->rd_input_1;
                $amount = $request->MonthInterest;
                $months = $request->mis_period_month;
                $start_date = $rdopening_datemodify;
                $startDate = new DateTime($start_date);
                for ($i = 1; $i <= $months; $i++) {
                    $installmentsNo = $i;
                    $date = $startDate->format('Y-m-d');
                    $startDate->add(new DateInterval("P1M"));
                
                    $installmentsdata = new RdInstallment;
                    $installmentsdata->rd_id = $RDAccount->id;
                    $installmentsdata->installment_date = $date;
                    $installmentsdata->amount = $amount;
                    $installmentsdata->intallment_no = $i;
                    $installmentsdata->branchId = 1;
                    $installmentsdata->sessionId=1;
                    $installmentsdata->updatedBy=auth()->user()->id;
                    
                    $installmentsdata->save();

                    $MisInstallment = new MisIntallment;
                    $MisInstallment->mis_id=$id;
                    $MisInstallment->installment_date = $date;
                    $MisInstallment->installment_amount = $amount; 
                    $MisInstallment->installment_no = $i;
                    $MisInstallment->type = "RD";
                    $MisInstallment->status = 'pending';
                    $MisInstallment->save();
                } 
                
            }else if($request->mis_interest_deposite_type == "Loan"){
                $query->interest_deposite="Loan";
                $query->SavingRdAccountNumber =$request->loan_rd_ac_no;
                
            }

            $account_no = MemberAccount::where(['accountNO'=>$request->account_no,'memberType'=>$request->mis_member_type])->first();
            
            $genral_ledger_1 = GeneralLedger::where(['referenceNo'=>$id,'transactionType'=>'Dr'])->first();
            $genral_ledger_2 = GeneralLedger::where(['referenceNo'=>$id,'transactionType'=>'Cr'])->first();
           
            $genral_ledger_1->accountId = $account_no->id;
            $genral_ledger_1->accountNo=$request->account_no;
            $genral_ledger_1->memberType = $request->mis_member_type;
            if($request->payment_type == "Cash"){
                $genral_ledger_1->groupCode = "C002";
                $genral_ledger_1->ledgerCode = "C002";
            }else if($request->payment_type == "Bank"){
                $genral_ledger_1->groupCode = "BANK001";
                $genral_ledger_1->ledgerCode =$request->payment_bank;
            }
            $genral_ledger_1->transactionDate = $opening_date;
            $genral_ledger_1->transactionAmount=$request->mis_maturity_amount;
            $genral_ledger_1->save();

            $genral_ledger_2->accountId=$account_no->id;
            $genral_ledger_2->accountNo=$request->account_no;
            $genral_ledger_2->memberType = $request->mis_member_type;
            $genral_ledger_2->transactionDate = $opening_date;
            $genral_ledger_2->transactionAmount=$request->mis_maturity_amount;
            $genral_ledger_2->save();
            
            $query->save();
            return response()->json(['status'=>'success','message'=>'Data Update Successfully !!']);

        }else{
            return response()->json(['status'=>'fail','message'=>'Update Failed !!']);
        }
    }


    public function getmisinstallmentlist(Request $request){
        $receiptno=$request->receiptno;
        if($receiptno){
            $query = MisIntallment::where(['mis_id'=>$receiptno,'status'=>'paid'])->with('generalLedgers')->get();
            return response()->json(['status'=>'success','installments'=>$query]);
        }else{
            return response()->json(['status'=>'fail','message'=>'Something went wrong']);
        }
    }
}


