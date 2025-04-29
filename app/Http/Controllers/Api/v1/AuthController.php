<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Str;
use Validator;
use App\Models\User;
use App\Models\MemberAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'branch_code' =>  'required|numeric',
            'memeber_type' => 'required',
            'account_number' => 'required|numeric',
            'password'     =>   'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => "failed",
                'message'   => 'Validation errors',
                'data'      => $validator->errors()
            ], 200);
        }

       $member = MemberAccount::where(['accountNo' => $request->account_number, 'memberType' => $request->memeber_type])->first(['name','memberType','accountNo','fatherName','openingDate']);
       if($member){
                $response['accessToken'] =  $string = Str::random(40);
                $response['user'] = $member;
                $response['status'] = 'success';
                $response['message'] = 'Hi ' . $member->name . ', welcome to home';
                return response()->json($response);
       }else{
             return response()->json(['status' => "failed", 'message' => 'Invalid login credential']);
       }
    }



   public function getprofile(Request $request){
          $validator = Validator::make($request->all(), [
            'branch_code' =>  'required|numeric',
            'memeber_type' => 'required',
            'account_number' => 'required|numeric',
            'password'     =>   'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => "failed",
                'message'   => 'Validation errors',
                'data'      => $validator->errors()
            ], 200);
        }

       $member = MemberAccount::where(['accountNo' => $request->account_number, 'memberType' => $request->memeber_type])->first(['name','memberType','accountNo','fatherName','openingDate']);
       if($member){
                $response['user'] = $member;
                $response['status'] = 'success';
                $response['message'] = "Record fetched successfully" ;
                return response()->json($response);
       }else{
             return response()->json(['status' => "failed", 'message' => 'Invalid account details']);
       }

   }

    // method for user logout and delete token
    public function logout(Request $request)
    {
         $user = User::where('id', $request->user()->id)->update(['uid'=>""]);
        auth()->user()->tokens()->delete();
        return response()->json(['status' => "success", 'message' => 'You have successfully logged out and the token was successfully deleted']);
        return [
          'status' => "success",  'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
