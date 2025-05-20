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
use App\Models\SessionMaster;

class AccountController extends Controller
{
    public function index()
    {
        $state = StateMaster::all();
        $agents = AgentMaster::all();
        $membersAcc = MemberAccount::all();
        // dd($membersAcc);
        // if ($membersAcc && isset($membersAcc->status)) {
        //     if ($membersAcc->status == "Transfer") {
        //         $member = TransferedAccount::where(['accountId' => $membersAcc->id])->first();
        //     } else {
        //         $member = $membersAcc;
        //     }

        //     return view('transaction.accountopen', ['agents' => $agents, 'memberacc' => $member, 'state' => $state, 'status' => $membersAcc->status]);
        // } else {
        //     return view('transaction.accountopen', ['agents' => $agents, 'state' => $state, 'status' => '']);
        // }
        return view('transaction.accountopen', ['agents' => $agents, 'state' => $state, 'memberacc' => $membersAcc]);
    }



    public function store(Request $request)
    {
        // dd($request->all());
        $result = $this->isDateBetween(date('Y-m-d', strtotime($request->openingdate)));
        if (!$result) {
            return response()->json(['statuscode' => 'ERR', 'status' => 'Please Check session', 'message' => "Please Check session"], 400);
        }
        // $member_acc_type = $request->account_type;
        $member_type = $request->membertype;
        if ($request->member_ship_no) {
            $member_account_no = $request->member_ship_no;
        } else {
            $member_account_no = $request->member_ac_no;
        }

        $memberCheck = MemberAccount::where(['id' => $request->id])->first();

        if ($memberCheck) {
            // dd($request->all());
            $member_id = $memberCheck->id;
            if ($memberCheck->status == "Transfer") {
                $transfer_account = TransferedAccount::where(['accountId' => $member_id])->first();
                $signature = $request->file('photo');
                $photo = $request->file('photoo');
                $useridprof = $request->file('photo3');

                $transfer_account->name = $request->name;
                $transfer_account->fatherName = $request->father_husband;
                $transfer_account->gender = $request->gender;
                $transfer_account->department = $request->department;
                $transfer_account->designation = $request->designation;
                $transfer_account->panNo = $request->pan_number;
                $transfer_account->birthDate = date('Y-m-d', strtotime($request->member_dob));
                $transfer_account->aadharNo = $request->adhaar_no;
                $transfer_account->pageNo = $request->page_no;
                $transfer_account->ledgerNo = $request->ledger_no;
                $transfer_account->employeeCode = $request->emp_code;
                $transfer_account->state = $request->state;
                $transfer_account->district = $request->districtId;
                $transfer_account->tehsil = $request->tehsilId;
                $transfer_account->village = $request->villageId;
                $transfer_account->address = $request->address;
                $transfer_account->phone = $request->contact_no;
                $transfer_account->nomineeName = $request->nominee_name;
                $transfer_account->nomineeRelation = $request->relation;
                $transfer_account->nomineeadhaarno = $request->nomineeadhaarno;
                $transfer_account->nomineePhone = $request->contact_no;
                $transfer_account->nomineeAddress = $request->nominee_address;
                $transfer_account->age = $request->age;
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
                $memberCheck->department = $request->department;
                $memberCheck->designation = $request->designation;
                $memberCheck->panNo = $request->pan_number;
                $memberCheck->birthDate = date('Y-m-d', strtotime($request->member_dob));
                $memberCheck->aadharNo = $request->adhaar_no;
                $memberCheck->pageNo = $request->page_no;
                $memberCheck->ledgerNo = $request->ledger_no;
                $memberCheck->employeeCode = $request->emp_code;
                $memberCheck->memberType = $request->membertype;
                $memberCheck->state = $request->state;
                $memberCheck->district = $request->districtId;
                $memberCheck->tehsil = $request->tehsilId;
                $memberCheck->village = $request->villageId;
                $memberCheck->address = $request->address;
                $memberCheck->phone = $request->contact_no;
                $memberCheck->nomineeName = $request->nominee_name;
                $memberCheck->nomineeRelation = $request->relation;
                $memberCheck->nomineeadhaarno = $request->nomineeadhaarno;
                $memberCheck->nomineePhone = $request->contact_no;
                $memberCheck->nomineeAddress = $request->nominee_address;
                $memberCheck->openingdate = date('Y-m-d', strtotime($request->openingdate));
                if ($signature) {
                    $currentTimestamp = now()->format('YmdHis');
                    $randomString = Str::random(8); // Generate a random string of length 8
                    $filename1 = 'signature_' . $currentTimestamp . '_' . $randomString . '.' . $signature->getClientOriginalExtension();
                    $path = public_path() . '/uploads/MemberSignature/' . $filename1;
                    $signature->move(public_path() . '/uploads/MemberSignature/', $filename1);
                    $memberCheck->signature = $filename1;
                }

                if (isset($request->isavl1) && $request->isavl1 == "remove") {
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

                if (isset($request->isavl2) && $request->isavl2 == "remove") {
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

                if (isset($request->isavl3) && $request->isavl3 == "remove") {
                    $memberCheck->idProof = null;
                }

                $memberCheck->save();
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
                // $storeacount->accountType = $request->account_type;
                $storeacount->accountNo = $member_account_no;
                $storeacount->name = $request->name;
                $storeacount->fatherName = $request->father_husband;
                $storeacount->gender = $request->gender;
                $storeacount->department = $request->department;
                $storeacount->designation = $request->designation;
                $storeacount->panNo = $request->pan_number;
                $storeacount->birthDate = date('Y-m-d', strtotime($request->member_dob));
                $storeacount->aadharNo = $request->adhaar_no;
                $storeacount->pageNo = $request->page_no;
                $storeacount->ledgerNo = $request->ledger_no;
                $storeacount->employeeCode = $request->emp_code;
                $storeacount->state = $request->state;
                $storeacount->district = $request->districtId;
                $storeacount->tehsil = $request->tehsilId;
                $storeacount->village = $request->villageId;
                $storeacount->address = $request->address;
                $storeacount->phone = $request->contact_no;
                $storeacount->nomineeName = $request->nominee_name;
                $storeacount->nomineeRelation = $request->relation;
                $storeacount->nomineeadhaarno = $request->nomineeadhaarno;
                $storeacount->nomineePhone = $request->contact_no;
                $storeacount->nomineeAddress = $request->nominee_address;
                // $storeacount->agentId = $request->agent;
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
                // dd('okkkk');
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
        // dd($request->all());
        try {
            $query = MemberAccount::where(['accountNo' => $request->memberid, 'memberType' => $request->membertypeid])->first();
            if ($query->status == "Transfer") {
                $transfer = TransferedAccount::where(['accountId' => $query->id])->first();
                $transfer->state = $request->state;
                $transfer->district = $request->districtId;
                $transfer->tehsil = $request->tehsilId;
                $transfer->village = $request->villageId;
                // $transfer->wardNo = $request->ward_no;
                $transfer->address = $request->address;
                $transfer->phone = $request->contact_no;
                $transfer->save();
            } else {
                $query->state = $request->state;
                $query->district = $request->districtId;
                $query->tehsil = $request->tehsilId;
                $query->village = $request->villageId;
                // $query->wardNo = $request->ward_no;
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
        // dd($request->all());
        try {
            $query = MemberAccount::where(['accountNo' => $request->memberid, 'memberType' => $request->membertypeid])->first();

            if ($query) {
                if ($query->status == "Transfer") {
                    $transfer = TransferedAccount::where(['accountId' => $query->id])->first();
                    $transfer->nomineeName = $request->nominee_name;
                    $transfer->nomineeRelation = $request->relation;
                    $transfer->nomineeadhaarno = $request->nomineeadhaarno;
                    // $transfer->nomineeBirthDate = date('Y-m-d', strtotime($request->date_of_birth));
                    $transfer->nomineePhone = $request->contact_no;
                    $transfer->nomineeAddress = $request->nominee_address;
                    $transfer->save();
                } else {
                    $query->nomineeName = $request->nominee_name;
                    $query->nomineeadhaarno = $request->nomineeadhaarno;
                    // $query->nomineeBirthDate = date('Y-m-d', strtotime($request->date_of_birth));
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
    public function deleteaccount(Request $post)
    {
        $accountId = $post->id;
        $exits_accountId = MemberAccount::where('id', $accountId)->first();

        if (is_null($exits_accountId)) {
            //_______if Not found
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        } else {
            //_____________if found Ledger Id then Delete
            $exits_accountId->delete();
            return response()->json([
                'status' => 'success',
                'messages' => 'Record Deleted successfully'
            ]);
        }
    }
}
