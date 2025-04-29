<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\GeneralLedger;

class PersonalLedgerController extends Controller
{
    public function index(){
        return view('report.personalledger');
    }

    public function getpersonalledgerdata(Request $request){
        $datefrom = date('Y-m-d',strtotime($request->date_from));
        $dateto =date('Y-m-d',strtotime($request->date_to));
        $membertype = $request->memberType;
        $accountNo = $request->account_no;
        $accounttype = $request->accounttype;

        $MemberAccount = MemberAccount::where(['memberType'=>$membertype,'accountNo'=>$accountNo])->where('is_delete', '!=', 'Yes')->first();
        if($MemberAccount){
            
            $PERSONAL_LEDGER_HTML = "";
            $PERSONAL_LEDGER_HTML .= "<div class=\"row\">";
            $PERSONAL_LEDGER_HTML .= "<div class=\"col-md-12\">";
            $PERSONAL_LEDGER_HTML .= "<div class=\"card\">";
            $PERSONAL_LEDGER_HTML .= "<div class=\"card-body\">";
            $PERSONAL_LEDGER_HTML .= "<div class=\"table-responsive custom-border\">";
            $PERSONAL_LEDGER_HTML .= "<table id=\"example1\" class=\"display nowrap\" style=\"width:100%;border:2px solid black;\">";
            $PERSONAL_LEDGER_HTML .= "<thead class=\"bg-primary text-white\">";
            $PERSONAL_LEDGER_HTML .= "<tr>";
            $PERSONAL_LEDGER_HTML .= "<th class=\"border border-primary\" colspan=\"24\">Name :- $MemberAccount->name</th>";
            $PERSONAL_LEDGER_HTML .= "</tr>";
            $PERSONAL_LEDGER_HTML .= "<tr style=\"width:100%;border:2px solid black;\">";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Sr</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Date</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">V NO</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Naration</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Dr</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Cr</th>";
            $PERSONAL_LEDGER_HTML .= "<th style=\"text-align:left;border:1px solid black;\">Balance</th>";
            $PERSONAL_LEDGER_HTML .= "</tr>";
            $PERSONAL_LEDGER_HTML .= "</thead>";
            $PERSONAL_LEDGER_HTML .= "<tbody id=\"tableshow\">";
            
            $amountarray=[];
            $PersonalLedgerArray=[];
            if($accounttype == "Saving" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "SAVM001";
                }elseif($membertype == "NonMember"){
                    $group = "SAVN001";
                }else{
                    $group = "SAVS001";
                }

                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingsaving=0;
                foreach($genral_ledger as $savingdata){
                    if($savingdata->transactionType == "Dr"){
                        $Dramount = $savingdata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($savingdata->transactionType == "Cr"){
                        $Cramount = $savingdata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }

                    $balancedata=$openingsaving+$Cramount-$Dramount; 
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($savingdata->transactionDate)),
                        'vno'=>$savingdata->id,
                        'narration'=>$savingdata->narration,
                        'Dr' => ($savingdata->transactionType == "Dr") ? $savingdata->transactionAmount : null,
                        'Cr' => ($savingdata->transactionType == "Cr") ? $savingdata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$savingdata->transactionType,
                    ];
                    $openingsaving=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }


            if($accounttype == "Share" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "SHAM001";
                }
                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingshare=0;
                foreach($genral_ledger as $sharedata){
                    if($sharedata->transactionType == "Dr"){
                        $Dramount = $sharedata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($sharedata->transactionType == "Cr"){
                        $Cramount = $sharedata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingshare+$Cramount-$Dramount;
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($sharedata->transactionDate)),
                        'vno'=>$sharedata->id,
                        'narration'=>$sharedata->narration,
                        'Dr' => ($sharedata->transactionType == "Dr") ? $sharedata->transactionAmount : null,
                        'Cr' => ($sharedata->transactionType == "Cr") ? $sharedata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$sharedata->transactionType,
                    ];
                    $openingshare=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);

            }

            if($accounttype == "Rd" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "RDOM001";
                }elseif($membertype == "NonMember"){
                    $group = "RDON001";
                }else{
                    $group = "RDOS001";
                }

                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingRD=0;
                foreach($genral_ledger as $rdledgerdata){
                    if($rdledgerdata->transactionType == "Dr"){
                        $Dramount = $rdledgerdata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($rdledgerdata->transactionType == "Cr"){
                        $Cramount = $rdledgerdata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingRD+$Cramount-$Dramount;
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($rdledgerdata->transactionDate)),
                        'vno'=>$rdledgerdata->id,
                        'narration'=>$rdledgerdata->narration,
                        'Dr' => ($rdledgerdata->transactionType == "Dr") ? $rdledgerdata->transactionAmount : null,
                        'Cr' => ($rdledgerdata->transactionType == "Cr") ? $rdledgerdata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$rdledgerdata->transactionType,
                    ];
                    $openingRD=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }

            if($accounttype == "Loan" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "LONM001";
                }elseif($membertype == "NonMember"){
                    $group = "LONN001";
                }else{
                    $group = "LONS001";
                }

                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingloan=0;
                foreach($genral_ledger as $loandata){
                    if($loandata->transactionType == "Dr"){
                        $Dramount = $loandata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($loandata->transactionType == "Cr"){
                        $Cramount = $loandata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingloan+$Dramount-$Cramount;
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($loandata->transactionDate)),
                        'vno'=>$loandata->id,
                        'narration'=>$loandata->narration,
                        'Dr' => ($loandata->transactionType == "Dr") ? $loandata->transactionAmount : null,
                        'Cr' => ($loandata->transactionType == "Cr") ? $loandata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$loandata->transactionType,
                    ];
                    $openingloan=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }

            if($accounttype == "Fd" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "FDOM001";
                }elseif($membertype == "NonMember"){
                    $group = "FDON001";
                }else{
                    $group = "FDOS001";
                }

                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingfd=0;
                foreach($genral_ledger as $fddata){
                    if($fddata->transactionType == "Dr"){
                        $Dramount = $fddata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($fddata->transactionType == "Cr"){
                        $Cramount = $fddata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingfd+$Cramount-$Dramount;
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($fddata->transactionDate)),
                        'vno'=>$fddata->id,
                        'narration'=>$fddata->narration,
                        'Dr' => ($fddata->transactionType == "Dr") ? $fddata->transactionAmount : null,
                        'Cr' => ($fddata->transactionType == "Cr") ? $fddata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$fddata->transactionType,
                    ];
                    $openingfd=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }

            if($accounttype == "Mis" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "MISM001";
                }elseif($membertype == "NonMember"){
                    $group = "MISN001";
                }else{
                    $group = "MISS001";
                }
                $openingmis=0;
                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                foreach($genral_ledger as $misdata){
                    if($misdata->transactionType == "Dr"){
                        $Dramount = $misdata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($misdata->transactionType == "Cr"){
                        $Cramount = $misdata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingmis+$Cramount-$Dramount;
                    $amountarray[]=[
                        "Date"=>date("d-m-Y", strtotime($misdata->transactionDate)),
                        'vno'=>$misdata->id,
                        'narration'=>$misdata->narration,
                        'Dr' => ($misdata->transactionType == "Dr") ? $misdata->transactionAmount : null,
                        'Cr' => ($misdata->transactionType == "Cr") ? $misdata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$misdata->transactionType,
                    ];
                    $openingmis=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }

            if($accounttype == "DailyCollection" || $accounttype =="All"){
                if($membertype == "Member"){
                    $group = "DCOM001";
                }elseif($membertype == "NonMember"){
                    $group = "DCON001";
                }else{
                    $group = "DCOS001";
                }

                $genral_ledger = GeneralLedger::where(['accountNo'=>$accountNo,'memberType'=>$membertype,'groupCode'=>$group])->get();
                $openingdailycollection=0;
                foreach($genral_ledger as $dailycollectiondata){
                    if($dailycollectiondata->transactionType == "Dr"){
                        $Dramount = $dailycollectiondata->transactionAmount;    
                    }else{
                        $Dramount=0;
                    }

                    if($dailycollectiondata->transactionType == "Cr"){
                        $Cramount = $dailycollectiondata->transactionAmount;
                    }else{
                        $Cramount=0;
                    }
                    $balancedata=$openingdailycollection+$Cramount-$Dramount;
                    $amountarray[]=[
                        "Date"=>$dailycollectiondata->transactionDate,
                        'vno'=>$dailycollectiondata->id,
                        'narration'=>$dailycollectiondata->narration,
                        'Dr' => ($dailycollectiondata->transactionType == "Dr") ? $dailycollectiondata->transactionAmount : null,
                        'Cr' => ($dailycollectiondata->transactionType == "Cr") ? $dailycollectiondata->transactionAmount : null,
                        'Balance'=>$balancedata,
                        'type'=>$dailycollectiondata->transactionType,
                    ];
                    $openingdailycollection=$balancedata;
                }
                $PersonalLedgerArray= array_merge($amountarray);
               
            }


            $srno=1;
            foreach ($PersonalLedgerArray as $personalledgerdata) {
                $PERSONAL_LEDGER_HTML .= "<tr>";
                $PERSONAL_LEDGER_HTML .= "<td>$srno</td>"; $srno++;
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['Date']}</td>";
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['vno']}</td>";
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['narration']}</td>";
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['Dr']}</td>";
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['Cr']}</td>";
                $PERSONAL_LEDGER_HTML .= "<td>{$personalledgerdata['Balance']} ({$personalledgerdata['type']})</td>";
                $PERSONAL_LEDGER_HTML .= "</tr>";
            }

            $PERSONAL_LEDGER_HTML .="</tbody>";
            $PERSONAL_LEDGER_HTML .="</table>";

            return response()->json(['status'=>'success','data'=>$PERSONAL_LEDGER_HTML]);

        }else{
            return response()->json(['status'=>'fail','message'=>'Account No Not Found']);
        }
    }
}
