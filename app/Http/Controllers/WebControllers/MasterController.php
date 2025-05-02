<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\AgentMaster;
use App\Models\BranchMaster;
use App\Models\DepotMaster;
use App\Models\FdMaster;
use App\Models\ItemMaster;
use App\Models\PurchaseClientMaster;
use App\Models\SaleClientMaster;
use App\Models\TaxMaster;
use App\Models\UnitMaster;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SessionMaster;
use App\Models\StateMaster;
use App\Models\DistrictMaster;
use App\Models\TehsilMaster;
use App\Models\VillageMaster;
use App\Models\BorrowingLimitMaster;
use App\Models\PostOfficeMaster;
use App\Models\GroupTypeMaster;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\NarrationMaster;
use App\Models\LoanMaster;
use App\Models\SchemeMaster;
use App\Models\CommissionMaster;
use App\Models\LoanTypeMaster;
use App\Models\PurposeMaster;
use App\Models\Slider;
use App\Models\MemberAccount;
use Illuminate\Support\Facades\Validator;


class MasterController extends Controller
{
    public function index($type, $id = 0, $status = "pending")
    {
        switch ($type) {
            case 'form':
                // case 'session':
                //     break;
            case 'state':
                break;
            case 'district':
            case 'tehsil':
            case 'postoffice':
            case 'village':
                $data['states'] = StateMaster::all();
                break;
            case 'borrowing':
                break;
            case 'group':
                break;
            case 'ledger':
                $data['groups'] = GroupMaster::all();
                break;
            case 'naretion':
                break;
            case 'loanMaster':
                $data['types'] = LoanTypeMaster::get();
                break;
            case 'purposeMaster':
                break;
            case 'dailySchemes':
                break;
            case 'commissionMaster':
                break;
            case 'agentMaster':
                break;
            case 'fdMaster':
                break;
            case 'taxMaster':
                break;
            case 'unitMaster':
                break;
            case 'itemMaster':
                break;
            case 'depotMaster':
                break;
            case 'saleClientMaster':
                $data['states'] = StateMaster::all();
                break;
            case 'purchaseClientMaster':
                $data['states'] = StateMaster::all();
                break;
            case 'branchMaster':
                $data['states'] = StateMaster::all();
                break;
            case 'loantypeMasters':
                $data['loanType'] = DB::table('loan_type_masters')->get();
                break;
            case 'banners':

                break;
            default:
                abort(404);
                break;
        }
        $data['type'] = $type;
        $data['id'] = 0;
        return view('master.' . $type)->with($data);
    }

    public function update(Request $post)
    {
        switch ($post->actiontype) {

            case 'sliderstatus':
                $action = Slider::updateOrCreate(['id' => $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'sliderDelete':
                $action = Slider::where('id', $post->id)->delete();
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'slider':
                $rules = array(
                    'title' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                if ($post->hasFile('image')) {

                    $filename = time() . '.' . $post->file('image')->guessExtension();
                    $post->file('image')->move(public_path('img/products/'), $filename);
                    $insert['image'] = 'img/products/' . $filename;
                }
                $action = Slider::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'getdistrict':
                return response()->json(['status' => "success", "dist" => DistrictMaster::where("stateId", $post->stateid)->get()], 200);
                break;
            case 'gettehsil':
                return response()->json(['status' => "success", "data" => TehsilMaster::where("districtId", $post->distId)->get()], 200);
                break;
            case 'getpostoffice':
                return response()->json(['status' => "success", "data" => PostOfficeMaster::where("tehsilId", $post->tehsilId)->get()], 200);
                break;
            case 'getvillage':
                return response()->json(['status' => "success", "data" => VillageMaster::where("postOfficeId", $post->postOfficeId)->get()], 200);
                break;
            case 'state':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = [
                    'name' => $post->input('name'),
                    'status' => $post->input('status'),
                    'updatedBy' => $post->user()->id,
                ];
                $action = StateMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            case 'loantypemaster':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = [
                    'name' => $post->input('name'),
                    'status' => $post->input('status'),
                    'updatedBy' => $post->user()->id,
                ];
                $action = LoanTypeMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            case 'districts':
                $rules = array(
                    'name' => 'required',
                    "stateId" => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = DistrictMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'tehsil':
                $rules = array(
                    'name' => 'required',
                    "districtId" => 'required',
                    "stateId" => 'required',
                    'status' => 'required',
                );
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = TehsilMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'postoffice':
                $rules = array(
                    'name' => 'required',
                    "districtId" => 'required',
                    "stateId" => 'required',
                    "tehsilId" => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = PostOfficeMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            // case 'session':

            //     $rules = array(
            //         'startDate' => 'required',
            //         'endDate' => 'required',
            //         'auditPerformed' => 'required',
            //         'status' => 'required',
            //     );

            //     $validator = \Validator::make($post->all(), $rules);
            //     if ($validator->fails()) {
            //         return response()->json(['errors' => $validator->errors()], 422);
            //     }
            //     $insert = $post->all();
            //     $insert['updatedBy'] = $post->user()->id;
            //     $action = SessionMaster::updateOrCreate(['id' => $post->id], $insert);

            //     if ($action) {
            //         return response()->json(['status' => "success"], 200);
            //     } else {
            //         return response()->json(['status' => "Task Failed, please try again"], 200);
            //     }

            //     break;



            case 'village':
                $rules = array(
                    'name' => 'required',
                    "districtId" => 'required',
                    'tehsilId'  => 'required',
                    "stateId" => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = VillageMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'borrowing':
                $rules = array(
                    'multiplyValue' => 'required',
                    'percentageValue' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = BorrowingLimitMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'group':
                dd($post->all());
                $rules = array(
                    'name' => 'required',
                    //'groupCode' => 'required',
                    'type' => 'required',
                    'showJournalVoucher' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                if ($post->id == "new") {
                    $check  = GroupMaster::where('groupCode', $post->groupCode)->first();
                    if ($check) {
                        return response()->json(['status' => "Group Code  already exist"], 400);
                    }
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = GroupMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'ledger':
                $rules = array(
                    'name' => 'required',
                    'openingAmount' => 'required',
                    'groupCode' => 'required',
                    'ledgerCode' => 'required',
                    'openingType' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                if ($post->id == "new") {
                    $check  = LedgerMaster::where('ledgerCode', $post->ledgerCode)->first();
                    if ($check) {
                        return response()->json(['status' => "Ledger Code already exist"], 400);
                    }
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = LedgerMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    $ledger = LedgerMaster::with('group')->find($action->id);
                    return response()->json(['status' => "success",  'ledger' => $ledger], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }

                break;
            case 'naretion':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = NarrationMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => "success"], 200);
                } else {
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            case 'loanMaster':
                $rules = array(
                    'memberType' => 'required',
                    'loanType' => 'required',
                    // 'name' => 'required',
                    'processingFee' => 'required',
                    'interest' => 'required',
                    // 'loantypess' => 'required',
                    'penaltyInterest' => 'required',
                    'loan_app_charges' => 'required',
                    // 'emiDate' => 'required',
                    'insType' => 'required',
                    'years' => 'required',
                    'months' => 'required',
                    'days' => 'required',
                    // 'advancementDate' => 'required',
                    // 'recoveryDate' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $groupCode = '';

                if ($post->memberType == "Member") {
                    $groupCode = "LONM001";
                } else if ($post->memberType == "NonMember") {
                    $groupCode = "LONN001";
                } else if ($post->memberType == "Staff") {
                    $groupCode = "LONS001";
                }


                do {
                    $ledgerCode =  "LOAN" . time();
                } while (LedgerMaster::where("ledgerCode", "=", $ledgerCode)->first() instanceof LedgerMaster);


                $existsId = LoanMaster::where('id', $post->id)->first();

                if ($existsId) {
                    $checkLoanMasterExits = DB::table('member_loans')
                        ->where('loanType', $existsId->id)
                        ->first();
                    if (!empty($checkLoanMasterExits)) {
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Loan Master Has Loan In Advancement Loan'
                        ], 422);
                    } else {

                        DB::table('ledger_masters')->where('loanmasterId', $existsId->id)->delete();
                        DB::table('loan_masters')->where('id', $post->id)->delete();

                        DB::beginTransaction();
                        try {
                            $loanMaster = LoanMaster::create([
                                'memberType' => $post->memberType,
                                'loanType' => $post->loanType,
                                'name' => $post->loanType,
                                'processingFee' => $post->processingFee,
                                'interest' => $post->interest,
                                'loantypess' => $post->loantypess,
                                'penaltyInterest' => $post->penaltyInterest,
                                'loan_app_charges' => $post->loan_app_charges,
                                // 'emiDate' => $post->emiDate,
                                'insType' => $post->insType,
                                'years' => $post->years,
                                'months' => $post->months,
                                'days' => $post->days,
                                // 'advancementDate' => $post->advancementDate,
                                // 'recoveryDate' => $post->recoveryDate,
                                'status' => $post->status,
                            ]);
                            $loanId = $loanMaster->id;

                            // Ledger Master Entry 1
                            $ledger_master = new LedgerMaster();
                            $ledger_master->groupCode = $groupCode;
                            $ledger_master->name = $post->loanType;
                            $ledger_master->ledgerCode = $ledgerCode;
                            $ledger_master->reference_id = $loanId;
                            $ledger_master->loanmasterId = $loanId;
                            $ledger_master->scheme_code = $ledgerCode;
                            $ledger_master->openingAmount = 0;
                            $ledger_master->openingType = 'Dr';
                            $ledger_master->status = $post->status;
                            $ledger_master->updatedBy = $post->user()->id;
                            $ledger_master->is_delete = 'No';
                            $ledger_master->save();

                            $id = $ledger_master->id;

                            LoanMaster::where('id', $loanId)->update([
                                'ledger_master_id' => $id
                            ]);

                            // Ledger Master Entry 2
                            // $ledger_master = new LedgerMaster();
                            // $ledger_master->groupCode = "INCM001";
                            // $ledger_master->name = 'Penality Rec. On ' . $post->loanType;
                            // $ledger_master->ledgerCode = $ledgerCode . (string) $loanId;
                            // $ledger_master->reference_id = $loanId;
                            // $ledger_master->loanmasterId = $loanId;
                            // $ledger_master->scheme_code = $ledgerCode . (string) $loanId;
                            // $ledger_master->openingAmount = 0;
                            // $ledger_master->openingType = 'Cr';
                            // $ledger_master->status = $post->status;
                            // $ledger_master->updatedBy = $post->user()->id;
                            // $ledger_master->is_delete = 'No';
                            // $ledger_master->save();

                            // Ledger Master Entry 3
                            $ledger_master = new LedgerMaster();
                            $ledger_master->groupCode = "INCM001";
                            $ledger_master->name = 'Intt Rec. On ' . $post->loanType;
                            $ledger_master->ledgerCode = $ledgerCode . (string) $loanId;
                            $ledger_master->reference_id = $loanId; // Fixed this line
                            $ledger_master->loanmasterId = $loanId;
                            $ledger_master->scheme_code = $ledgerCode . (string) $loanId;
                            $ledger_master->openingAmount = 0;
                            $ledger_master->openingType = 'Cr';
                            $ledger_master->status = $post->status;
                            $ledger_master->updatedBy = $post->user()->id;
                            $ledger_master->is_delete = 'No';
                            $ledger_master->save();

                            DB::commit();

                            return response()->json([
                                'status' => 'success',
                                'messages' => 'Record Inserted Successfully',
                            ]);
                        } catch (\Exception $e) {
                            DB::rollback();
                            return response()->json([
                                'status' => 'Fail',
                                'messages' => 'Some Technical Issue',
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                } else {

                    DB::beginTransaction();
                    try {
                        $loanMaster = LoanMaster::create([
                            'memberType' => $post->memberType,
                            'loanType' => $post->loanType,
                            'name' => $post->loanType,
                            'processingFee' => $post->processingFee,
                            'interest' => $post->interest,
                            'loantypess' => $post->loantypess,
                            'penaltyInterest' => $post->penaltyInterest,
                            'loan_app_charges' => $post->loan_app_charges,
                            // 'emiDate' => $post->emiDate,
                            'insType' => $post->insType,
                            'years' => $post->years,
                            'months' => $post->months,
                            'days' => $post->days,
                            // 'advancementDate' => $post->advancementDate,
                            // 'recoveryDate' => $post->recoveryDate,
                            'status' => $post->status,
                        ]);

                        $loanId = $loanMaster->id;

                        // Ledger Master Entry 1
                        $ledger_master = new LedgerMaster();
                        $ledger_master->groupCode = $groupCode;
                        $ledger_master->name = $post->loanType;
                        $ledger_master->ledgerCode = $ledgerCode;
                        $ledger_master->reference_id = $loanId;
                        $ledger_master->loanmasterId = $loanId;
                        $ledger_master->scheme_code = $ledgerCode;
                        $ledger_master->openingAmount = 0;
                        $ledger_master->openingType = 'Dr';
                        $ledger_master->status = $post->status;
                        $ledger_master->updatedBy = $post->user()->id;
                        $ledger_master->is_delete = 'No';
                        $ledger_master->save();

                        $id = $ledger_master->id;

                        LoanMaster::where('id', $loanId)->update([
                            'ledger_master_id' => $id
                        ]);

                        // Ledger Master Entry 2
                        $ledger_master = new LedgerMaster();
                        $ledger_master->groupCode = "INCM001";
                        $ledger_master->name = 'Penality Rec. On ' . $post->loanType;
                        $ledger_master->ledgerCode = $ledgerCode . (string) $loanId;
                        $ledger_master->reference_id = $loanId;
                        $ledger_master->loanmasterId = $loanId;
                        $ledger_master->scheme_code = $ledgerCode . (string) $loanId;
                        $ledger_master->openingAmount = 0;
                        $ledger_master->openingType = 'Cr';
                        $ledger_master->status = $post->status;
                        $ledger_master->updatedBy = $post->user()->id;
                        $ledger_master->is_delete = 'No';
                        $ledger_master->save();

                        // Ledger Master Entry 3
                        $ledger_master = new LedgerMaster();
                        $ledger_master->groupCode = "INCM001";
                        $ledger_master->name = 'Intt Rec. On ' . $post->loanType;
                        $ledger_master->ledgerCode = $ledgerCode . (string) ($loanId + 1);
                        $ledger_master->reference_id = $loanId; // Fixed this line
                        $ledger_master->loanmasterId = $loanId;
                        $ledger_master->scheme_code = $ledgerCode . (string) ($loanId + 1);
                        $ledger_master->openingAmount = 0;
                        $ledger_master->openingType = 'Cr';
                        $ledger_master->status = $post->status;
                        $ledger_master->updatedBy = $post->user()->id;
                        $ledger_master->is_delete = 'No';
                        $ledger_master->save();

                        DB::commit();

                        return response()->json([
                            'status' => 'success',
                            'messages' => 'Record Inserted Successfully',
                        ]);
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json([
                            'status' => 'Fail',
                            'messages' => 'Some Technical Issue',
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // dd($existsId);








                // $insert = $post->all();
                // $insert['name'] = $post['loanType'] ;
                // $insert['updatedBy'] = $post->user()->id;

                // if ($post->memberType == "Member") {
                //     $groupCode = "LONM001";
                // } else if ($post->memberType == "NonMember") {
                //     $groupCode = "LONN001";
                // } else if ($post->memberType == "Staff") {
                //     $groupCode = "LONS001";
                // }




                // do {
                //     $ledgerCode =  "LOAN" . rand(0000, 9999);
                // } while (LedgerMaster::where("ledgerCode", "=", $ledgerCode)->first() instanceof LedgerMaster);

                // $ledger['groupCode'] =  $groupCode;
                // $ledger['name']  = $post->loanType;
                // $ledger['ledgerCode']  =   $ledgerCode;
                // $ledger['openingAmount'] = 0;
                // $ledger['openingType']  = "Dr";
                // $ledger['updatedBy']  = $post->user()->id;


                // if ($post->id == "new") {
                //     $ledgermaster = LedgerMaster::updateOrCreate(['id' => $post->id], $ledger);
                //     $insert["ledger_master_id"] =  $ledgermaster->id;
                // }


                // $action = LoanMaster::updateOrCreate(['id' => $post->id], $insert);



                // if ($post->id != "new") {
                //     $ledgermaster = LedgerMaster::where('id', $action->ledger_master_id)->update(['name' => $post->loanType]);
                // }


                // if ($action) {
                //     return response()->json(['status' => 'success'], 200);
                // } else {
                //     return response()->json(['status' => "Task Failed, Please try again"]);
                // }


                break;

            case 'purposeMaster':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = [
                    'name' => $post->input('name'),
                    'status' => $post->input('status'),
                    'updatedBy' => $post->user()->id,
                ];
                $action = PurposeMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
            case 'dailySchemes':
                $rules = array(
                    'name' => 'required',
                    'durationType' => 'required',
                    // 'duration' => 'required',
                    'interest' => 'required',
                    'penaltyInterest' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();

                $insert['updatedBy'] = $post->user()->id;

                $action = SchemeMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
            case 'commissionMaster':
                $rules = array(
                    'startDate' => 'required',
                    'endDate' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;

                $action = CommissionMaster::updateOrCreate(['id' => $post->id], $insert);

                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
                // case 'agentMaster':


                break;
            case 'fdMaster':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = [
                    'name' => $post->input('name'),
                    'status' => $post->input('status'),
                    'updatedBy' => $post->user()->id,
                ];
                $action = FdMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;




            case 'taxMaster':
                $rules = array(
                    'name' => 'required',
                    'cess' => 'required',
                    'cgst' => 'required',
                    'igst' => 'required',
                    'sgst' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = TaxMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
            case 'unitMaster':
                $rules = array(
                    'name' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = UnitMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
            case 'depotMaster':
                $rules = array(
                    'depotName' => 'required',
                    'salesmanName' => 'required',
                    'phone' => 'required|max:10',
                    'address' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = DepotMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;
            case 'saleClientMaster':
                $rules = array(
                    'name' => 'required',
                    'state' => 'required',
                    'district' => 'required',
                    'city' => 'required',
                    'address' => 'required',
                    'email' => 'required',
                    'phone' => 'required|max:10',
                    'faxNo' => 'required',
                    'gstNo' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = SaleClientMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            case 'purchaseClientMaster':
                $rules = array(
                    'name' => 'required',
                    'state' => 'required',
                    'district' => 'required',
                    'city' => 'required',
                    'address' => 'required',
                    'email' => 'required',
                    'phone' => 'required|max:10',
                    'faxNo' => 'required',
                    'gstNo' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = PurchaseClientMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            case 'itemMaster':
                $rules = array(
                    'name' => 'required',
                    'code' => 'required',
                    'type' => 'required',
                    'unit' => 'required',
                    'purchaseRate' => 'required',
                    'saleRate' => 'required',
                    'taxId' => 'required',
                    'purchaseTax' => 'required',
                    'saleTax' => 'required',
                    'openingStock' => 'required',
                    'reorderLevel' => 'required',
                    'status' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = ItemMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            case 'branchMaster':
                $rules = array(
                    //  'type' => 'required',
                    'name' => 'required',
                    'registrationNo' => 'required',
                    'registrationDate' => 'required',
                    'stateId' => 'required',
                    'districtId' => 'required',
                    'tehsilId' => 'required',
                    'postOfficeId' => 'required',
                    'villageId' => 'required',
                    //'wardNo' => 'required',
                    'address' => 'required',
                    'pincode' => 'required',
                    'phone' => 'required|max:10',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $insert = $post->all();
                $insert['updatedBy'] = $post->user()->id;
                $action = BranchMaster::updateOrCreate(['id' => $post->id], $insert);
                if ($action) {
                    return response()->json(['status' => 'success'], 200);
                } else {
                    return response()->json(['status' => 'Task Failed, Please try again']);
                }
                break;

            default:
                return response()->json(['status' => "Invalid request type"], 200);
                break;
        }
    }

    public function delete(Request $post)
    {
        DB::beginTransaction();

        try {

            // Adjust the namespace based on your actual implementation
            $modelNamespace = 'App\\Models\\';

            switch ($post->actiontype) {
                // case 'deleteSession':
                //     $modelClass = $modelNamespace . 'SessionMaster';
                //     // dd($modelClass);

                //     if(MemberAccount::where('sessionId', $post->id)->exists()){
                //         return response()->json(['status' => 'fail','messages' => 'Session Has Data Exit']);
                //     }
                //     break;
                case 'deleteState':
                    $modelClass = $modelNamespace . 'StateMaster';
                    break;
                case 'deleteDistrict':
                    $modelClass = $modelNamespace . 'DistrictMaster';
                    break;
                case 'deleteTehsil':
                    $modelClass = $modelNamespace . 'TehsilMaster';
                    break;
                case 'deletePostOffice':
                    $modelClass = $modelNamespace . 'PostOfficeMaster';
                    break;
                case 'deleteVillage':
                    $modelClass = $modelNamespace . 'VillageMaster';
                    break;
                case 'deletePurpose':
                    $modelClass = $modelNamespace . 'PurposeMaster';
                    break;
                case 'deleteBorrowing':
                    $modelClass = $modelNamespace . 'BorrowingLimitMaster';
                    break;
                case 'deleteGroup':
                    $modelClass = $modelNamespace . 'GroupMaster';
                    break;
                case 'deleteNaretion':
                    $modelClass = $modelNamespace . 'NarrationMaster';
                    break;
                case 'deleteLedger':
                    $modelClass = $modelNamespace . 'LedgerMaster';
                    break;
                case 'deleteCommission':
                    $modelClass = $modelNamespace . 'CommissionMaster';
                    break;
                case 'deleteLoan':

                case 'deleteLoan':




                    break;
                case 'deleteScheme':
                    $modelClass = $modelNamespace . 'SchemeMaster';
                    break;
                case 'deleteAgent':
                    // $modelClass = $modelNamespace . 'AgentMaster';
                    break;
                case 'deleteFd':
                    $modelClass = $modelNamespace . 'FdMaster';
                    break;
                case 'deleteTax':
                    $modelClass = $modelNamespace . 'TaxMaster';
                    break;
                case 'deleteUnit':
                    $modelClass = $modelNamespace . 'UnitMaster';
                    break;
                case 'deleteDepot':
                    $modelClass = $modelNamespace . 'DepotMaster';
                    break;
                case 'deleteSaleClient':
                    $modelClass = $modelNamespace . 'SaleClientMaster';
                    break;
                case 'deletePurchaseClient':
                    $modelClass = $modelNamespace . 'PurchaseClientMaster';
                    break;
                case 'deleteItem':
                    $modelClass = $modelNamespace . 'ItemMaster';
                    break;
                case 'deleteBranch':
                    $modelClass = $modelNamespace . 'BranchMaster';
                    break;
                case 'deleteLoanType':
                    $modelClass = $modelNamespace . 'LoanTypeMaster';
                    $model = $modelClass::withTrashed()->findOrFail($post->id);
                    // $check = LoanMaster :: where('loanType',$model->name)->first();
                    // if($check){
                    //      return response()->json([
                    //         'status' => 'Delete not allowed ',
                    //         'message' => 'Delete not allowed ',
                    //     ]);
                    // }
                    break;
                // Add more cases for other models as needed

                default:
                    throw new \InvalidArgumentException('Invalid actiontype provided');
            }

            $model = $modelClass::withTrashed()->findOrFail($post->id);
            $model->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => ucfirst($post->actiontype) . ' deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Deletion failed',
                'errors' => $e->getMessage()
            ]);
        }
    }


    //_______________Generate Group Code
    public function GenerateGroupCode(Request $post)
    {
        $group_name = $post->group_name;
        $newGroupCode = '';
    }


    public function deleteloanmaster(Request $post)
    {
        $loanMaster = DB::table('loan_masters')->where('id', $post->id)->first();

        if ($loanMaster) {
            $checkLoanMasterExits = DB::table('member_loans')
                ->where('loanType', $loanMaster->id)
                ->exists();

            if ($checkLoanMasterExits) {
                return response()->json([
                    'status' => 'Fail',
                    'messages' => 'Loan Master has loans in advancement.',
                ]);
            }

            DB::table('ledger_masters')->where('loanmasterId', $loanMaster->id)->delete();
            DB::table('loan_masters')->where('id', $post->id)->delete();

            return response()->json([
                'status' => 'success',
                'messages' => 'Loan Master and associated records deleted successfully.',
            ]);
        }

        return response()->json([
            'status' => 'Fail',
            'messages' => 'Loan Master not found.',
        ]);
    }


    public function getallstaffnumber(Request $post)
    {
        $rules = [
            "memberType" => "required",
            "staff_no" => "required",
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $staff_no = $post->staff_no;
        $memberType = $post->memberType;

        $all_staffs = DB::table('member_accounts')
            ->where('memberType', $memberType)
            ->where('accountNo', 'LIKE', $staff_no . '%')
            ->orderBy('accountNo', 'ASC')
            ->get();
        if (!empty($all_staffs)) {
            return response()->json(['status' => 'success', 'allstaff' => $all_staffs]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Staff Number Not Found']);
        }
    }

    public function getstaffnumber(Request $post)
    {
        $rules = [
            "selectdId" => "required",
            "memberType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'error' => $validator->errors()]);
        }

        $staff_no = $post->selectdId;
        $memberType = $post->memberType;

        $exits_staff = DB::table('agent_masters')->where('memberType', $memberType)->where('staff_no', $staff_no)->first();

        if (!empty($exits_staff)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Agent Already Registerd']);
        } else {

            $staff_detail = DB::table('member_accounts')
                ->where('memberType', $memberType)
                ->where('accountNo', $staff_no)
                ->orderBy('accountNo', 'ASC')
                ->first();

            if (!empty($staff_detail)) {
                return response()->json(['status' => 'success', 'staff_detail' => $staff_detail]);
            } else {
                return response()->json(['status' => 'Fail', 'messages' => 'Staff Number Not Found']);
            }
        }
    }

    public function agentindex()
    {
        $agents = DB::table('agent_masters')->orderBy('id', 'DESC')->get();
        $data['agents'] = $agents;
        return view('master.agentMaster', $data);
    }

    public function insertagent(Request $post)
    {
        // dd($post->all());
        $exitsId = DB::table('agent_masters')->where('id', $post->agentId)->first();
        if (!empty($exitsId)) {
            return response()->json(['status' => 'success', 'exitsIdagent' => $exitsId]);
        } else {
            $rules = array(
                'memberType' => 'required',
                'staff_no' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'address' => 'required',
                'panNo' => 'required',
                'joiningDate' => 'required',
                'commissionSaving' => 'required',
                'commissionmis' => 'required',
                'commissionFD' => 'nullable',
                'commissionRD' => 'nullable',
                'daily_saving' => 'nullable',
                'commissionDailyCollection' => 'nullable',
                // 'commLoan' => 'nullable',
                // 'commissionDailyCollection' => 'nullable',
                'status' => 'required',
            );

            $validator = \Validator::make($post->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            try {
                $agentId = DB::table('agent_masters')->insertGetId([
                    'name' => $post->name,
                    'memberType' => $post->memberType,
                    'staff_no' => $post->staff_no,
                    'phone' => $post->phone,
                    'commissionSaving' => $post->commissionSaving,
                    'commissionmis' => $post->commissionmis,
                    'email' => $post->email,
                    'address' => $post->address,
                    'panNo' => $post->panNo,
                    'commissionFD' => $post->commissionFD,
                    'commissionRD' => $post->commissionRD,
                    'commissionLoan' => $post->commissionDailyCollection,
                    'daily_saving' => $post->daily_saving,
                    'joiningDate' => date('Y-m-d', strtotime($post->joiningDate)),
                    'status' => $post->status,
                    'updatedBy' => $post->user()->id,
                ]);

                // No need to use `$agentId->id`; `$agentId` already contains the ID.
                DB::table('security_on_commission_account')->insert([
                    'openingDate' => date('Y-m-d', strtotime($post->joiningDate)),
                    'name' => $post->name,
                    'memberType' => $post->memberType,
                    'staff_no' => $post->staff_no,
                    'account_no' => $post->staff_no,
                    'groupCode' => 'SEC01',
                    'ledgerCode' => 'SEC02',
                    'status' => $post->status,
                    'updatedBy' => $post->user()->id,
                    'branchId' => session('branchId') ?: 1,
                    'sessionId' => session('sessionId') ?: 1,
                    'agentId' => $agentId, // Use the correct ID here.
                ]);

                DB::commit();

                return response()->json(['status' => 'success', 'messages' => 'Agent Inserted Successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function agentupdate(Request $post)
    {
        // dd($post->all());
        $rules = array(
            'memberType' => 'required',
            'agentid' => 'required',
            'staff_no' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'commissionSaving' => 'required',
            'commissionmis' => 'required',
            'address' => 'required',
            'panNo' => 'required',
            'joiningDate' => 'required',
            'commissionFD' => 'nullable',
            'commissionRD' => 'nullable',
            'daily_saving' => 'nullable',
            'commissionDailyCollection' => 'nullable',
            'status' => 'required',
        );

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {

            DB::table('agent_masters')->where('id', $post->agentid)->delete();

            $data = [
                'name' => $post->name,
                'memberType' => $post->memberType,
                'staff_no' => $post->staff_no,
                'phone' => $post->phone,
                'email' => $post->email,
                'address' => $post->address,
                'panNo' => $post->panNo,
                'commissionSaving' => $post->commissionSaving,
                'commissionmis' => $post->commissionmis,
                'commissionFD' => $post->commissionFD,
                'commissionRD' => $post->commissionRD,
                'commissionLoan' => $post->commissionDailyCollection,
                'daily_saving' => $post->daily_saving,
                'joiningDate' => date('Y-m-d', strtotime($post->joiningDate)),
                'releavingDate' => $post->releavingDate ? date('Y-m-d', strtotime($post->releavingDate)) : null,
                'status' => $post->status,
                'updatedBy' => auth()->id(),
            ];

            DB::table('agent_masters')->insert($data);

            // $id = $data->id;

            // DB::table('security_on_commission_account')->insert([
            //     'openingDate' => date('Y-m-d',strtotime($post->joiningDate)),
            //     'name' => $post->name,
            //     'staff_no' => $post->staff_no,
            //     'account_no' => $post->staff_no,
            //     'groupCode' => 'SEC01',
            //     'ledgerCode' => 'SEC02',
            //     'status' => $post->status,
            //     'updatedBy' => $post->user()->id,
            //     'branchId' => session('branchId') ? session('branchId') : 1,
            //     'sessionId' => session('sessionId') ? session('sessionId') : 1,
            //     'agentId' =>$post->agentid,
            // ]);


            DB::commit();

            return response()->json(['status' => 'success', 'messages' => 'Agent Inserted Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Fail', 'error' => $e->getMessage()]);
        }
    }

    public function deleteagent(Request $post)
    {
        $agentId =  $post->agentId;
        $exitsId = DB::table('agent_masters')->where('id', $agentId)->first();
        if (is_null($exitsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {
            DB::table('agent_masters')->where('id', $agentId)->delete();
            return response()->json(['status' => 'success', 'messages' => 'Record Successfully Deleted']);
        }
    }


    //________________Bank Fd Master_______________
    public function bankfdmasterindex()
    {
        $data['bankfdsDetails'] = DB::table('bank_fd_masters')->orderBy('id', 'DESC')->get();
        return view('master.bankfdmaster', $data);
    }

    public function insertfdmaster(Request $post)
    {
        $rules = [
            "name" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Enter Bank Name']);
        }

        DB::beginTransaction();
        try {
            //___________Bank Fd Master Entries
            $bankFdMasterId = DB::table('bank_fd_masters')->insertGetId([
                'bank_name' => 'FD -' . $post->name,
                'groupCode' => 'BANK001',
                'ledgerCode' => $post->ledgercode,
                'address' => $post->address,
                'branch_name' => $post->branch_name,
                'branch_pincode' => $post->pincode,
                'sessionId' => session('sessionId') ?: 1,
                'updatedBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //_________Ledger Master Entries

            //________Create Bank FD Ledger
            DB::table('ledger_masters')->insert([
                'name' => $post->name,
                'groupCode' => 'BANK001',
                'ledgerCode' => $post->ledgercode,
                'reference_id' => $bankFdMasterId,
                'loanmasterId' => null,
                'scheme_code' => $post->ledgercode,
                'openingAmount' => 0,
                'openingType' => 'Dr',
                'status' => 'Active',
                'updatedBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $interesLedger = $post->ledgercode . (string) ($bankFdMasterId + 1);
            $interestRecoverable = $post->ledgercode . (string) ($bankFdMasterId + 1) . ($bankFdMasterId + 1);
            $tdspaid = $post->ledgercode . (string) ($bankFdMasterId + 1) . ($bankFdMasterId + 1) . ($bankFdMasterId + 1);


            //________Create Bank FD Interest Received Ledger
            $ledgerId = DB::table('ledger_masters')->insertGetId([
                'name' => 'Int. Recevied From' . $post->name,
                'groupCode' => 'INCM001',
                'ledgerCode' => $interesLedger,
                'reference_id' => $bankFdMasterId,
                'bankfd_id' => $bankFdMasterId,
                'loanmasterId' => null,
                'scheme_code' => $post->ledgercode,
                'openingAmount' => 0,
                'openingType' => 'Dr',
                'status' => 'Active',
                'updatedBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //________Create Bank FD Interest Recoverable Ledger
            DB::table('ledger_masters')->insert([
                'name' => 'Int. Recoverable Of' . $post->name,
                'groupCode' => 'INCM001',
                'ledgerCode' => $interestRecoverable,
                'reference_id' => $bankFdMasterId,
                'sch_id' => $bankFdMasterId,
                'loanmasterId' => null,
                'scheme_code' => $post->ledgercode,
                'openingAmount' => 0,
                'openingType' => 'Dr',
                'status' => 'Active',
                'updatedBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //________Create Bank FD Tds Paid Ledger
            DB::table('ledger_masters')->insert([
                'name' => 'Tds Paid On Bank Fd -' . $post->name,
                'groupCode' => '    ',
                'ledgerCode' => $tdspaid,
                'reference_id' => $bankFdMasterId,
                'sch_id' => $bankFdMasterId,
                'loanmasterId' => null,
                'scheme_code' => $post->ledgercode,
                'openingAmount' => 0,
                'openingType' => 'Cr',
                'status' => 'Active',
                'updatedBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            DB::table('bank_fd_masters')->where('id', $bankFdMasterId)->update([
                'interest_ledger' => $interesLedger,
                'intrecoverable_ledger' => $interestRecoverable,
                'tds_ledger' => $tdspaid
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Some Technical Issue',
                'error' => $e->getMessage(),
            ]);
        }
    }


    //_____________Edit Bank Fd Ledger
    public function editbankfdmasterid(Request $post)
    {
        $rules = [
            "id" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'REcord Not Found']);
        }

        $id = $post->id;
        $existsId = DB::table('bank_fd_masters')->where('id', $id)->first();
        if (is_null($existsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        } else {
            return response()->json(['status' => 'success', 'existsId' => $existsId]);
        }
    }

    public function updatefdmaster(Request $post)
    {
        $rules = [
            "id" => "required",
            "name" => "required"
        ];


        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        $id = $post->id;
        $existsId = DB::table('bank_fd_masters')->where('id', $id)->first();

        if (!$existsId) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }


        DB::beginTransaction();
        try {
            //___________Bank FD Master Update
            DB::table('bank_fd_masters')->where('id', $id)->update([
                'bank_name' =>   $post->name ?  $post->name : $existsId->name,
                'groupCode' => 'BANK001',
                'ledgerCode' => $existsId->ledgerCode,
                'address' => $post->address ? $post->address : $existsId->address,
                'branch_name' => $post->branch_name ? $post->branch_name : $existsId->branch_name,
                'branch_pincode' => $post->pincode ? $post->pincode : $existsId->branch_pincode,
                'sessionId' => session('sessionId') ?? 1,
                'updatedBy' => auth()->id() ?? 0,
                'updated_at' => now(),
            ]);

            //________Update Ledger Master Entries
            $referenceId = LedgerMaster::where('reference_id', $existsId->id)->where('ledgerCode', $existsId->ledgerCode)->first();
            $referenceId->name = 'FD' . $post->name;
            $referenceId->save();

            //________Update Bank FD Interest Received Ledger
            $interest = LedgerMaster::where('bankfd_id', $existsId->id)->first();
            $interest->name = "Int. Received From " . $post->name ? "Int. Received From " . $post->name : $existsId->name;
            $interest->save();

            //________Update Bank FD Interest Recoverable Ledger
            $interest_recoverable = LedgerMaster::where('sch_id', $existsId->id)->first();
            $interest_recoverable->name = "Int. Recoverable Of " . $post->name ? "Int. Recoverable Of  " . $post->name : $existsId->name;
            $interest_recoverable->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Updated Successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Some Technical Issue',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function deletebankfdmaster(Request $post)
    {

        $rules = [
            "id" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        $id = $post->id;
        $existsId = DB::table('bank_fd_masters')->where('id', $id)->first();

        if (is_null($existsId)) {
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }

        // Check if Ledger has entries
        if (DB::table('general_ledgers')->where('ledgerCode', $existsId->ledgerCode)->exists()) {
            return response()->json(['status' => 'Fail', 'messages' => 'This Ledger Has Entries, You Can’t Delete']);
        }

        DB::beginTransaction();
        try {
            // Delete from ledger_masters
            DB::table('ledger_masters')->where('reference_id', $existsId->id)->where('scheme_code', $existsId->ledgerCode)->delete();

            // Delete from bank_fd_masters
            DB::table('bank_fd_masters')->where('id', $id)->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'messages' => 'Record Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Fail', 'messages' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
}
