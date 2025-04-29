<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MemberCCL;
use App\Models\SecuredOverDraftMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SODMasterController extends Controller
{
    public function sodmasterindex(){
        $sodDetails = SecuredOverDraftMaster::orderBy('id','DESC')->get();
        $data['sodDetails'] = $sodDetails;
        return view('master.sodmaster',$data);
    }

    public function sodmasterinsert(Request $post) {
        $actionType = $post->actionType;

        $rules = [
            "startDate" => "required",
            "interesttype" => "required",
            "status" => "required",
        ];

        if ($actionType === 'update') {
            $rules["id"] = "required|exists:sod_masters,id";
            $rules["interesttype"] = "nullable|unique:sod_masters,interest_type,{$post['id']},id";
        } else {
            $rules["interesttype"] = "nullable|unique:sod_masters,interest_type";
        }

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'The interesttype has already been taken',
                'error' => $validator->errors()
            ]);
        }

        if ($actionType === 'insert') {
            SecuredOverDraftMaster::insert([
                'start_date' => date('Y-m-d', strtotime($post->startDate)),
                'interest_type' => $post->interesttype,
                'status' => $post->status,
            ]);

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully'
            ]);
        } elseif ($actionType === 'update') {

            DB::table('sod_masters')->where('id', $post->id)->update([
                'start_date' => date('Y-m-d', strtotime($post->startDate)),
                'interest_type' => $post->interesttype,
                'status' => $post->status,
            ]);

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Updated Successfully'
            ]);
        }

        return response()->json([
            'status' => 'Fail',
            'messages' => 'Something Went Wrong'
        ]);
    }

    public function sodmasteredit(Request $post){
        $id = $post->id;
        if(is_null($id)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            $exitdId = SecuredOverDraftMaster::where('id',$post->id)->first();
            if(!empty($exitdId)){
                return response()->json(['status' => 'success','sodDetails' => $exitdId]);
            }else{
                return response()->json(['status' => 'Fail','messages' => 'Record Not Exists']);
            }
        }
    }

    public function deletesodmaster(Request $post){
        $id = $post->id;
        if(is_null($id)){
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }else{
            DB::table('sod_masters')->where('id',$post->id)->delete();
            return response()->json(['status' => 'success','messages' => 'Record Deleted Successfully']);
        }
    }
}
