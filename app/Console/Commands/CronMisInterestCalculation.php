<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MemberAccount;
use App\Models\Mis;
use App\Models\ReCurringRd;
use App\Models\MemberSaving;
use App\Models\GeneralLedger;
use App\Models\MemberLoan;
use App\Jobs\MisCalculationJob;

class CronMisInterestCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:miscalculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //$todaydate = date('Y-m-d');
        $todaydate='2024-04-05';
        $mischeckrows = Mis::where('cron_status', 'pending')
        ->orWhere('cron_status', 'processing')
        ->get();
        
        foreach($mischeckrows as $mis_row){
            if($mis_row->interest_deposite == "RD"){
                $id=$mis_row->id;
                $type = $mis_row->interest_deposite;
                $today = $todaydate; 
                dispatch(new MisCalculationJob($id,$type,$today));        
            }elseif($mis_row->interest_deposite == "Saving"){
                $id=$mis_row->id;
                $type = $mis_row->interest_deposite;
                $today = $todaydate;
                dispatch(new MisCalculationJob($id,$type,$today));
            }elseif($mis_row->interest_deposite == "Loan"){
                $id=$mis_row->id;
                $type = $mis_row->interest_deposite;
                $today = $todaydate;
                dispatch(new MisCalculationJob($id,$type,$today));
            }
        }
    }
}
