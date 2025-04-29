<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchemeMaster;
use App\Models\LedgerMaster;
use Session;
use DateTime;
use Illuminate\Support\Facades\DB;

class DepositSchemeMaster extends Controller
{
    public function DepositSechemesIndex(){
        $sechemnes = SchemeMaster::orderby('name','DESC')->get();
        $data['sechemnes'] = $sechemnes;
        return view('master.depositsechememaster',$data);
    }

    //_________Generate Ledger Code
    public function GenerateSchemeCode(Request $post){
        $sechemeName = strtoupper($post->sechemeName);
        $newgroup_code = '';

        $first_name = substr($sechemeName, 0, 3);

        DB::beginTransaction();

        try {
            $last_group_code = LedgerMaster::where('ledgerCode', 'LIKE', $first_name . '%')
                ->lockForUpdate()
                ->orderBy('ledgerCode', 'desc')
                ->first();

            if (!empty($last_group_code)) {
                $last_number = (int)substr($last_group_code->ledgerCode, -2);
                $new_number = str_pad($last_number + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $new_number = '01';
            }

            $newgroup_code = $first_name . $new_number;

            if (!LedgerMaster::where('ledgerCode', $newgroup_code)->exists()) {
                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'newgroup_code' => $newgroup_code
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Code already exists. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    //__________Insert Sechemes
    public function DepositSechemeInsert(Request $post){
        DB::beginTransaction();
        $memberType = $post->memberType;
        $depositType = $post->depositType;
        $groupcode = '';

        if($memberType === 'Member'){
            switch($depositType){
                case 'Saving':
                    $groupcode = 'SAVM001';
                break;
                case 'FD':
                    $groupcode = 'FDOM001';
                break;
                case 'RD':
                    $groupcode = 'RDOM001';
                break;
                case 'MIS':
                    $groupcode = 'MISM001';
                break;
                case 'CDS':
                    $groupcode = 'CDSM001';
                break;
                case 'Daily Loan':
                    $groupcode = 'LONM001';
                break;
                case 'DailyDeposit':
                    $groupcode = 'DCOM001';
                break;

                default:
                    $groupcode = '';
            }
        } elseif($memberType === 'NonMember') {
            switch($depositType){
                case 'Saving':
                    $groupcode = 'SAVN001';
                break;
                case 'FD':
                    $groupcode = 'FDON001';
                break;
                case 'RD':
                    $groupcode = 'RDON001';
                break;
                case 'MIS':
                    $groupcode = 'MISN001';
                break;
                case 'CDS':
                    $groupcode = 'CDSM001';
                break;
                case 'Daily Loan':
                    $groupcode = 'DCON001';
                break;
                case 'DailyDeposit':
                    $groupcode = 'DCON001';
                break;
                default:
                    $groupcode = '';
            }
        } elseif($memberType === 'Staff') {
            switch($depositType){
                case 'Saving':
                    $groupcode = 'SAVS001';
                break;
                case 'FD':
                    $groupcode = 'FDOS001';
                break;
                case 'RD':
                    $groupcode = 'RDOS001';
                break;
                case 'MIS':
                    $groupcode = 'MISS001';
                break;
                case 'CDS':
                    $groupcode = 'CDSM001';
                break;
                case 'Daily Loan':
                    $groupcode = 'DCOS001';
                break;
                case 'DailyDeposit':
                    $groupcode = 'DCOS001';
                break;
                default:
                    $groupcode = '';
            }
        }

        try {
            // Insert scheme master
            $scheme_insert = new SchemeMaster();
            $scheme_insert->start_date = date('Y-m-d', strtotime($post->start_date));
            $scheme_insert->name = $post->sechemeName;
            $scheme_insert->fdType = $post->fdType;
            $scheme_insert->scheme_code = $post->scheme_code;
            $scheme_insert->memberType = $post->memberType;
            $scheme_insert->secheme_type = $post->depositType;
            $scheme_insert->durationType = $post->depType;
            $scheme_insert->days = $post->days;
            $scheme_insert->months = $post->months;
            $scheme_insert->years = $post->years;
            $scheme_insert->interest_type = $post->interestType;
            $scheme_insert->interest = $post->rateofinterest;
            $scheme_insert->penaltyInterest = $post->prematureDeduction;
            $scheme_insert->status = $post->status;
            $scheme_insert->lockin_days = $post->lockin_days;
            $scheme_insert->renewInterestType = $post->renewInterestType;
            $scheme_insert->updatedBy = $post->user()->id;
            $scheme_insert->save();

            //________Get Sechme Id
            $scheme_id = $scheme_insert->id;

            // Insert ledger master
            $ledger_master = new LedgerMaster();
            $ledger_master->groupCode = $groupcode;
            $ledger_master->name = $post->sechemeName;
            $ledger_master->ledgerCode = $post->scheme_code;
            $ledger_master->reference_id = $scheme_id;
            $ledger_master->sch_id = $scheme_id;
            $ledger_master->scheme_code =  $post->scheme_code;
            $ledger_master->openingAmount = 0;
            $ledger_master->openingType = 'Cr';
            $ledger_master->status = $post->status;
            $ledger_master->updatedBy = $post->user()->id;
            $ledger_master->is_delete = 'No';
            $ledger_master->save();

            // Insert ledger master
            $ledger_master = new LedgerMaster();
            $ledger_master->groupCode = "INCM001";
            $ledger_master->name = 'Penality Rec. On '.$post->sechemeName;
            $ledger_master->ledgerCode = $post->scheme_code .strval($scheme_id);
            $ledger_master->reference_id = $scheme_id;
            $ledger_master->sch_id = $scheme_id;
            $ledger_master->scheme_code = $post->scheme_code .strval($scheme_id);
            $ledger_master->openingAmount = 0;
            $ledger_master->openingType = 'Cr';
            $ledger_master->status = $post->status;
            $ledger_master->updatedBy = $post->user()->id;
            $ledger_master->is_delete = 'No';
            $ledger_master->save();


            if($depositType === 'Daily Loan'){
                // Penality Ledger master
                $ledger_master = new LedgerMaster();
                $ledger_master->groupCode = "INCM001";
                $ledger_master->name = 'Intt Rec. On '.$post->sechemeName;
                $ledger_master->ledgerCode = $post->scheme_code .strval($scheme_id)+1;
                $ledger_master->reference_id = $scheme_id;
                $ledger_master->sch_id = $scheme_id;
                $ledger_master->scheme_code = $post->scheme_code .strval($scheme_id)+1;
                $ledger_master->openingAmount = 0;
                $ledger_master->openingType = 'Cr';
                $ledger_master->status = $post->status;
                $ledger_master->updatedBy = $post->user()->id;
                $ledger_master->is_delete = 'No';
                $ledger_master->save();
            } else{
                // Insert ledger master
                $ledger_master = new LedgerMaster();
                $ledger_master->groupCode = "EXPN001";
                $ledger_master->name = 'Intt. Paid On '.$post->sechemeName;
                $ledger_master->ledgerCode = $post->scheme_code .strval($scheme_id)+1;
                $ledger_master->reference_id = $scheme_id;
                $ledger_master->sch_id = $scheme_id;
                $ledger_master->scheme_code = $post->scheme_code .strval($scheme_id)+1;
                $ledger_master->openingAmount = 0;
                $ledger_master->openingType = 'Dr';
                $ledger_master->status = $post->status;
                $ledger_master->updatedBy = $post->user()->id;
                $ledger_master->is_delete = 'No';
                $ledger_master->save();
            }

            DB::commit();
            return response()->json(['status' => 'success', 'messages' => 'Record Added Successfully']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getSchemeDetails($id)
    {
        $scheme = SchemeMaster::where('id', $id)->first();

        if ($scheme) {
            return response()->json([
                'success' => true,
                'data' => $scheme
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Scheme not found'
            ]);
        }
    }



    //__________Delete Sechemes
    public function DeleteDepositSecheme(Request $post){
        $id = $post->id;
        $secheme_master = SchemeMaster::where('id',$id)->first();
        if(is_null($secheme_master)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            DB::beginTransaction();
            try{
                DB::table('ledger_masters')
                    ->where('sch_id',$secheme_master->id)
                    ->delete();
                $secheme_master->delete();
                DB::commit();
                return response()->json(['status' => 'success','messages' => 'Record Deleted successfully']);

            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'error' => $e->getMessage(),
                    'messages' => 'Some Technical Issue'
                ]);
            }
        }
    }


    //__________Edit End Of Secheme

    public function UpdateDepositSecheme(Request $post) {

        $id = $post->updateid;
        $scheme_insert = SchemeMaster::where('id', $id)->first();

        if ($scheme_insert) {
            $sessionStart = Session::get('sessionStart');
            $sessionEnd = Session::get('sessionEnd');

            // Format session start and end dates to 'Y-m-d'
            $sessionStart = date('Y-m-d', strtotime($sessionStart));
            $sessionEnd = date('Y-m-d', strtotime($sessionEnd));

            // Handle `start_date` with both possible formats
            $startDate = $post->start_date;
            $startDateObj = DateTime::createFromFormat('d/m/Y', $startDate) ?: DateTime::createFromFormat('d-m-Y', $startDate);

            if (!$startDateObj) {
                return response()->json([
                    'success' => false,
                    'messages' => 'Invalid start date format. Please enter a valid date in the format dd/mm/yyyy or dd-mm-yyyy.',
                ]);
            }

            $startDate = $startDateObj->format('Y-m-d');  // Convert to 'Y-m-d'

            // Check if start date is within session range
            if ($startDate < $sessionStart || $startDate > $sessionEnd) {
                return response()->json([
                    'success' => false,
                    'messages' => 'Start date must be between ' . date('d-m-Y', strtotime($sessionStart)) . ' and ' . date('d-m-Y', strtotime($sessionEnd)),
                ]);
            }

            // Handle `edit_end_date` if the status is 'Inactive'
            $endDate = $post->edit_end_date ?? null;  // Retrieve `edit_end_date` if provided

            if ($post->status == 'Inactive' && $endDate) {
                $endDateObj = DateTime::createFromFormat('d/m/Y', $endDate) ?: DateTime::createFromFormat('d-m-Y', $endDate);

                if (!$endDateObj) {
                    return response()->json([
                        'success' => false,
                        'messages' => 'Invalid end date format. Please enter a valid date in the format dd/mm/yyyy or dd-mm-yyyy.',
                    ]);
                }

                $endDate = $endDateObj->format('Y-m-d');  // Convert to 'Y-m-d'

                // Check if `edit_end_date` is within session range
                if ($endDate < $sessionStart || $endDate > $sessionEnd) {
                    return response()->json([
                        'success' => false,
                        'messages' => 'End date must be between ' . date('d-m-Y', strtotime($sessionStart)) . ' and ' . date('d-m-Y', strtotime($sessionEnd)),
                    ]);
                }

                // Assign the `edit_end_date` to the model
                $scheme_insert->secheme_end_date = $endDate;
            }

            // Update other fields in the model
            $scheme_insert->start_date = $startDate;
            $scheme_insert->name = $post->sechemeName;
            $scheme_insert->memberType = $post->memberType;
            // $scheme_insert->secheme_type = $post->secheme_type;
            $scheme_insert->secheme_type = $post->depositType;
            $scheme_insert->durationType = $post->depType;
            $scheme_insert->renewInterestType = $post->renewInterestType;
            $scheme_insert->days = $post->days;
            $scheme_insert->months = $post->months ?? null;
            $scheme_insert->years = $post->years ?? null;
            $scheme_insert->interest_type = $post->interestType;
            $scheme_insert->interest = $post->rateofinterest;
            $scheme_insert->penaltyInterest = $post->prematureDeduction;
            $scheme_insert->status = $post->status;

            $scheme_insert->lockin_days = $post->lockin_days;
            $scheme_insert->updatedBy = $post->user()->id;

            $scheme_insert->save();  // Save the updated scheme

            return response()->json(['status' => 'success','messages' => 'Record Updated Successfully']);
        } else {
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }





}
