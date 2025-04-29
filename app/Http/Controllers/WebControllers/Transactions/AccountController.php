<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\MemberAccount;
use App\Models\JointAccount;
use App\Models\AgentMaster;
use App\Models\StateMaster;
use App\Models\DistrictMaster;
use App\Models\TehsilMaster;
use App\Models\TransferedAccount;
use App\Models\VillageMaster;
use App\Models\PostOfficeMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class AccountController extends Controller
{
    public function index()
    {
        $state = StateMaster::all();
        $agents = AgentMaster::all();
        $membersAcc = MemberAccount::latest()->first();


        if ($membersAcc && isset($membersAcc->status)) {
            if ($membersAcc->status == "Transfer") {
                $member = TransferedAccount::where(['accountId' => $membersAcc->id])->first();
            } else {
                $member = $membersAcc;
            }

            return view('transaction.accountopen', ['agents' => $agents, 'memberacc' => $member, 'state' => $state, 'status' => $membersAcc->status]);
        } else {
            return view('transaction.accountopen', ['agents' => $agents, 'state' => $state, 'status' => '']);
        }
    }



    public function store(Request $request)
    {
       $result = $this->isDateBetween(date('Y-m-d', strtotime($request->openingdate))) ;
       if (!$result) {
             return response()->json(['statuscode'=>'ERR', 'status'=>'Please Check session', 'message' => "Please Check session"],400);
        }
        $member_acc_type = $request->account_type;
        $member_type = $request->membertype;
        if ($request->member_ship_no) {
            $member_account_no = $request->member_ship_no;
        } else {
            $member_account_no = $request->member_ac_no;
        }

        $memberCheck = MemberAccount::where(['id' => $request->id])->first();

        if ($memberCheck) {
            $member_id = $memberCheck->id;
            if ($memberCheck->status == "Transfer") {
                $transfer_account = TransferedAccount::where(['accountId' => $member_id])->first();
                $signature = $request->file('photo');
                $photo = $request->file('photoo');
                $useridprof = $request->file('photo3');

                $transfer_account->name = $request->name;
                $transfer_account->fatherName = $request->father_husband;
                $transfer_account->gender = $request->gender;
                $transfer_account->occupation = $request->occupation;
                $transfer_account->panNo = $request->pan_number;
                $transfer_account->caste = $request->member_caste;
                $transfer_account->birthDate = date('Y-m-d',strtotime($request->member_dob));
                $transfer_account->aadharNo = $request->adhaar_no;
                $transfer_account->pageNo = $request->page_no;
                $transfer_account->ledgerNo = $request->ledger_no;
                $transfer_account->employeeCode = $request->emp_code;
                $transfer_account->agentId = $request->agent;
                $transfer_account->openingdate = date('Y-m-d', strtotime($request->openingdate));

                if ($signature) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8); // Generate a random string of length 8
                    $filename1 = 'transfersignature_' . $currentTimestamp . '_' . $randomString . '.' . $signature->getClientOriginalExtension();
                    $path = public_path() . '/uploads/MemberSignature/' . $filename1;
                    $signature->move(public_path() . '/uploads/MemberSignature/', $filename1);
                    $transfer_account->signature = $filename1;
                }


                if ($photo) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8); // Generate a random string of length 8
                    $filename2 = 'transferimage_' . $currentTimestamp . '_' . $randomString . '.' . $photo->getClientOriginalExtension();
                    $path2 = public_path() . '/uploads/MemberPhotos/' . $filename2;
                    $photo->move(public_path() . '/uploads/MemberPhotos/', $filename2);
                    $transfer_account->photo = $filename2;
                }


                if ($useridprof) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8);
                    $filename3 = 'transfermemberidproof_' . $currentTimestamp . '_' . $randomString . '.' . $useridprof->getClientOriginalExtension();
                    $path3 = public_path() . '/uploads/MemberIdProof' . $filename3;
                    $useridprof->move(public_path() . '/uploads/MemberIdProof', $filename3);
                    $transfer_account->idProof = $filename3;
                }




                $transfer_account->save();
            } else {
                $signature = $request->file('photo');
                $photo = $request->file('photoo');
                $useridprof = $request->file('photo3');

                $memberCheck->name = $request->name;
                $memberCheck->fatherName = $request->father_husband;
                $memberCheck->gender = $request->gender;
                $memberCheck->occupation = $request->occupation;
                $memberCheck->panNo = $request->pan_number;
                $memberCheck->caste = $request->member_caste;
                $memberCheck->birthDate = date('Y-m-d',strtotime($request->member_dob));
                $memberCheck->aadharNo = $request->adhaar_no;
                $memberCheck->pageNo = $request->page_no;
                $memberCheck->ledgerNo = $request->ledger_no;
                $memberCheck->employeeCode = $request->emp_code;
                $memberCheck->agentId = $request->agent;
                $memberCheck->memberType = $request->membertype;
                $memberCheck->openingdate = date('Y-m-d', strtotime($request->openingdate));
                if ($signature) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8); // Generate a random string of length 8
                    $filename1 = 'signature_' . $currentTimestamp . '_' . $randomString . '.' . $signature->getClientOriginalExtension();
                    $path = public_path() . '/uploads/MemberSignature/' . $filename1;
                    $signature->move(public_path() . '/uploads/MemberSignature/', $filename1);
                    $memberCheck->signature = $filename1;
                }

                  if(isset($request->isavl1) && $request->isavl1 == "remove"){
                    $memberCheck->signature = '';
                }

                if ($photo) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8); // Generate a random string of length 8
                    $filename2 = 'image_' . $currentTimestamp . '_' . $randomString . '.' . $photo->getClientOriginalExtension();
                    $path2 = public_path() . '/uploads/MemberPhotos/' . $filename2;
                    $photo->move(public_path() . '/uploads/MemberPhotos/', $filename2);
                    $memberCheck->photo = $filename2;
                }

                 if(isset($request->isavl2) && $request->isavl2 == "remove"){
                    $memberCheck->photo = '';
                }

                if ($useridprof) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8);
                    $filename3 = 'memberidproof_' . $currentTimestamp . '_' . $randomString . '.' . $useridprof->getClientOriginalExtension();
                    $path3 = public_path() . '/uploads/MemberIdProof' . $filename3;
                    $useridprof->move(public_path() . '/uploads/MemberIdProof', $filename3);
                    $memberCheck->idProof = $filename3;
                }

                  if(isset($request->isavl3) && $request->isavl3 == "remove"){
                    $memberCheck->idProof = null;
                }

                $memberCheck->save();
            }

            if ($member_acc_type == "Joint") {
                $jointcurrentTimestamp = now()->format('YmdHis');
                $jointrandomString = Str::random(8);

                $jointaccount = JointAccount::where(['accountId' => $member_id])->first();
                $jointaccount->name = $request->joint_name;
                $jointaccount->fatherName = $request->joint_father_husband;
                $jointaccount->birthDate = date('Y-m-d',strtotime($request->joint_member_dob));
                $jointaccount->gender = $request->joint_gender;
                $jointaccount->caste = $request->joint_member_caste;
                $jointaccount->aadharNo = $request->joint_adhaar_no;
                $jointaccount->panNo = $request->joint_pan_number;
                $jointaccount->occupation = $request->joint_occupation;
                $jointaccount->employeeCode = $request->joint_emp_code;
                $jointaccount->state = $request->joint_state;
                $jointaccount->district = $request->districtId;
                $jointaccount->tehsil  = $request->tehsilId;
                $jointaccount->village  = $request->villageId;
                $jointaccount->wardNo = $request->joint_ward_no;
                $jointaccount->address = $request->joint_address;
                $jointaccount->phone = $request->joint_contact_no;
                if ($request->hasFile('signature2img')) {
                    $jointsignature = $request->file('signature2img');
                    $jointsignaturefilename = 'jointsignature_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jointsignature->getClientOriginalExtension();
                    $path = public_path() . '/uploads/MemberSignature/' . $jointsignaturefilename;
                    $jointsignature->move(public_path() . '/uploads/MemberSignature/', $jointsignaturefilename);
                    $jointaccount->signature = $jointsignaturefilename;
                }

                if ($request->hasFile('imgphoto2')) {
                    $jointimg = $request->file('imgphoto2');
                    $jointimgfilename = 'jointimage_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jointimg->getClientOriginalExtension();
                    $path2 = public_path() . '/uploads/MemberPhotos/' . $jointimgfilename;
                    $jointimg->move(public_path() . '/uploads/MemberPhotos/', $jointimgfilename);
                    $jointaccount->photo = $jointimgfilename;
                }

                if ($request->hasFile('useridprove')) {
                    $jountidproof = $request->file('useridprove');
                    $jointprooffilename = 'jointmemberidproof_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jountidproof->getClientOriginalExtension();
                    $path3 = public_path() . '/uploads/MemberIdProof' . $jointprooffilename;
                    $jountidproof->move(public_path() . '/uploads/MemberIdProof', $jointprooffilename);
                    $jointaccount->idProof = $jointprooffilename;
                }
                $jointaccount->updatedBy = $request->user()->id;
                $jointaccount->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Account Updated Successfully']);
        } else {

            $signature = $request->file('photo');
            $photo = $request->file('photoo');
            $useridprof = $request->file('photo3');
            if ($signature) {
                $currentTimestamp = now()->format('YmdHis');
                $randomString = Str::random(8); // Generate a random string of length 8
                $filename1 = 'signature_' . $currentTimestamp . '_' . $randomString . '.' . $signature->getClientOriginalExtension();
                $path = public_path() . '/uploads/MemberSignature/' . $filename1;
                $signature->move(public_path() . '/uploads/MemberSignature/', $filename1);
            }

            if ($photo) {
                $currentTimestamp = now()->format('YmdHis');
                $randomString = Str::random(8); // Generate a random string of length 8
                $filename2 = 'image_' . $currentTimestamp . '_' . $randomString . '.' . $photo->getClientOriginalExtension();
                $path2 = public_path() . '/uploads/MemberPhotos/' . $filename2;
                $photo->move(public_path() . '/uploads/MemberPhotos/', $filename2);
            }
            if ($useridprof) {
                $currentTimestamp = now()->format('YmdHis');
                $randomString = Str::random(8);
                $filename3 = 'memberidproof_' . $currentTimestamp . '_' . $randomString . '.' . $useridprof->getClientOriginalExtension();
                $path3 = public_path() . '/uploads/MemberIdProof' . $filename3;
                $useridprof->move(public_path() . '/uploads/MemberIdProof', $filename3);
            }
            $requestDate = Carbon::createFromFormat('d-m-Y', $request->opening_date);

            DB::beginTransaction();
            try {
                $storeacount = new MemberAccount;
                $storeacount->memberType = $member_type;
                $storeacount->openingDate = $requestDate;
                $storeacount->accountType = $request->account_type;
                $storeacount->accountNo = $member_account_no;
                $storeacount->name = $request->name;
                $storeacount->fatherName = $request->father_husband;
                $storeacount->gender = $request->gender;
                $storeacount->occupation = $request->occupation;
                $storeacount->panNo = $request->pan_number;
                $storeacount->caste = $request->member_caste;
                $storeacount->birthDate = date('Y-m-d',strtotime($request->member_dob));
                $storeacount->aadharNo = $request->adhaar_no;
                $storeacount->pageNo = $request->page_no;
                $storeacount->ledgerNo = $request->ledger_no;
                $storeacount->employeeCode = $request->emp_code;
                $storeacount->agentId = $request->agent;
                // Check if filename1 is not empty before assigning
                if (!empty($filename1)) {
                    $storeacount->signature = $filename1;
                }

                // Check if filename2 is not empty before assigning
                if (!empty($filename2)) {
                    $storeacount->photo = $filename2;
                }

                // Check if filename3 is not empty before assigning
                if (!empty($filename3)) {
                    $storeacount->idProof = $filename3;
                }
                $storeacount->branchId = session('branchId') ? session('branchId') : 1;
                $storeacount->sessionId = session('sessionId') ? session('sessionId') : 1;
                $storeacount->updatedBy = $request->user()->id;
                $storeacount->save();

                if ($request->account_type == "Joint") {
                    $jointcurrentTimestamp = now()->format('YmdHis');
                    $jointrandomString = Str::random(8);

                    $jointaccount = new JointAccount;
                    $jointaccount->accountId =  $storeacount->id;
                    $jointaccount->name = $request->joint_name;
                    $jointaccount->fatherName = $request->joint_father_husband;
                    $jointaccount->birthDate = date('Y-m-d',strtotime($request->joint_member_dob));
                    $jointaccount->gender = $request->joint_gender;
                    $jointaccount->caste = $request->joint_member_caste;
                    $jointaccount->aadharNo = $request->joint_adhaar_no;
                    $jointaccount->panNo = $request->joint_pan_number;
                    $jointaccount->occupation = $request->joint_occupation;
                    $jointaccount->employeeCode = $request->joint_emp_code;
                    if ($request->hasFile('signature2img')) {
                        $jointsignature = $request->file('signature2img');
                        $jointsignaturefilename = 'jointsignature_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jointsignature->getClientOriginalExtension();
                        $path = public_path() . '/uploads/MemberSignature/' . $jointsignaturefilename;
                        $jointsignature->move(public_path() . '/uploads/MemberSignature/', $jointsignaturefilename);
                        $jointaccount->signature = $jointsignaturefilename;
                    }

                    if ($request->hasFile('imgphoto2')) {
                        $jointimg = $request->file('imgphoto2');
                        $jointimgfilename = 'jointimage_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jointimg->getClientOriginalExtension();
                        $path2 = public_path() . '/uploads/MemberPhotos/' . $jointimgfilename;
                        $jointimg->move(public_path() . '/uploads/MemberPhotos/', $jointimgfilename);
                        $jointaccount->photo = $jointimgfilename;
                    }

                    if ($request->hasFile('useridprove')) {
                        $jountidproof = $request->file('useridprove');
                        $jointprooffilename = 'jointmemberidproof_' . $jointcurrentTimestamp . '_' . $jointrandomString . '.' . $jountidproof->getClientOriginalExtension();
                        $path3 = public_path() . '/uploads/MemberIdProof' . $jointprooffilename;
                        $jountidproof->move(public_path() . '/uploads/MemberIdProof', $jointprooffilename);
                        $jointaccount->idProof = $jointprooffilename;
                    }
                    $jointaccount->state = $request->joint_state;
                    $jointaccount->district = $request->districtId;
                    $jointaccount->tehsil  = $request->tehsilId;
                    $jointaccount->village  = $request->villageId;
                    $jointaccount->wardNo = $request->joint_ward_no;
                    $jointaccount->address = $request->joint_address;
                    $jointaccount->phone = $request->joint_contact_no;
                    $jointaccount->updatedBy = $request->user()->id;
                    $jointaccount->save();
                }


                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'A/C No Created Successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => "fail", 'message' => $e->getMessage()]);
            }
        }
    }

    public function accountsearch(Request $request)
    {
        $memberaccountNo = $request->memberaccno;
        $memberaccounttype = $request->membertype;
        $searchmember = MemberAccount::where(['accountNo' => $memberaccountNo, 'memberType' => $memberaccounttype])->first();

        if ($searchmember) {
            $id = $searchmember->id;
            return response()->json(['status' => 'success', 'data' => Crypt::encrypt($id)]);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function accountsearchfind(Request $request)
    {
        try {
            $memberaccount = Crypt::decrypt($request->memberid);
            $query = MemberAccount::with('getjointmember')->where(['id' => $memberaccount])->first();
            if ($query->status == "Transfer") {
                $transfer_table = TransferedAccount::where(['accountId' => $memberaccount])->first();
                return response()->json([
                    'status' => 'success',
                    'member' => $transfer_table,
                    'signature' => !empty($transfer_table->signature) ? asset('public/uploads/MemberSignature/' . $transfer_table->signature) : null,
                    'photo' => !empty($transfer_table->photo) ? asset('public/uploads/MemberPhotos/' . $transfer_table->photo) : null,
                    'photoidproof' => !empty($transfer_table->idProof) ? asset('public/uploads/MemberIdProof/' . $transfer_table->idProof) : null,
                    'signaturejoint' => !empty($transfer_table->getjointmember) && !empty($transfer_table->getjointmember[0]->signature) ? asset('public/uploads/MemberSignature/' . $transfer_table->getjointmember[0]->signature) : null,
                    'photojoint' => !empty($transfer_table->getjointmember) && !empty($transfer_table->getjointmember[0]->photo) ? asset('public/uploads/MemberPhotos/' . $transfer_table->getjointmember[0]->photo) : null,
                    'photoidproofjoint' => !empty($transfer_table->getjointmember) && !empty($transfer_table->getjointmember[0]->idProof) ? asset('public/uploads/MemberIdProof/' . $transfer_table->getjointmember[0]->idProof) : null,
                ]);
            } else if ($query->status == "Active") {
                return response()->json([
                    'status' => 'success',
                    'member' => $query,
                    'signature' => !empty($query->signature) ? asset('public/uploads/MemberSignature/' . $query->signature) : null,
                    'photo' => !empty($query->photo) ? asset('public/uploads/MemberPhotos/' . $query->photo) : null,
                    'photoidproof' => !empty($query->idProof) ? asset('public/uploads/MemberIdProof/' . $query->idProof) : null,
                    'signaturejoint' => !empty($query->getjointmember) && !empty($query->getjointmember[0]->signature) ? asset('public/uploads/MemberSignature/' . $query->getjointmember[0]->signature) : null,
                    'photojoint' => !empty($query->getjointmember) && !empty($query->getjointmember[0]->photo) ? asset('public/uploads/MemberPhotos/' . $query->getjointmember[0]->photo) : null,
                    'photoidproofjoint' => !empty($query->getjointmember) && !empty($query->getjointmember[0]->idProof) ? asset('public/uploads/MemberIdProof/' . $query->getjointmember[0]->idProof) : null,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $post)
    {
        switch ($post->actiontype) {
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
                return response()->json(['status' => "success", "data" => VillageMaster::where(['stateId' => $post->stateId, 'districtId' => $post->districtId, 'tehsilId' => $post->tehsilId])->get()], 200);
                break;
            default:
                return response()->json(['status' => "Invalid request type"], 200);
                break;
        }
    }

    public function storeaddresspagedata(Request $request)
    {
        try {
            $query = MemberAccount::where(['accountNo' => $request->memberid, 'memberType' => $request->membertypeid])->first();
            if ($query->status == "Transfer") {
                $transfer = TransferedAccount::where(['accountId' => $query->id])->first();
                $transfer->state = $request->state;
                $transfer->district = $request->districtId;
                $transfer->tehsil = $request->tehsilId;
                $transfer->village = $request->villageId;
                $transfer->wardNo = $request->ward_no;
                $transfer->address = $request->address;
                $transfer->phone = $request->contact_no;
                $transfer->save();
            } else {
                $query->state = $request->state;
                $query->district = $request->districtId;
                $query->tehsil = $request->tehsilId;
                $query->village = $request->villageId;
                $query->wardNo = $request->ward_no;
                $query->address = $request->address;
                $query->phone = $request->contact_no;
                $query->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Record updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function storenomeneepagedata(Request $request)
    {
        try {
            $query = MemberAccount::where(['accountNo' => $request->memberid, 'memberType' => $request->membertypeid])->first();

            if ($query) {
                if ($query->status == "Transfer") {
                    $transfer = TransferedAccount::where(['accountId' => $query->id])->first();
                    $transfer->nomineeName = $request->nominee_name;
                    $transfer->nomineeRelation = $request->relation;
                    $transfer->nomineeBirthDate = date('Y-m-d', strtotime($request->date_of_birth));
                    $transfer->nomineePhone = $request->contact_no;
                    $transfer->nomineeAddress = $request->nominee_address;
                    $transfer->save();
                } else {
                    $query->nomineeName = $request->nominee_name;
                    $query->nomineeRelation = $request->relation;
                    $query->nomineeBirthDate = date('Y-m-d', strtotime($request->date_of_birth));
                    $query->nomineePhone = $request->contact_no;
                    $query->nomineeAddress = $request->nominee_address;
                    $query->save();
                }

                return response()->json(['status' => 'success', 'message' => 'Record submitted successfully']);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'No matching record found']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
