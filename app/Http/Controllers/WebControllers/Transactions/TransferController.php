<?php

namespace App\Http\Controllers\WebControllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\TransferedAccount;
use App\Models\StateMaster;
use Illuminate\Support\Carbon;
use App\Models\DistrictMaster;
use App\Models\TehsilMaster;
use App\Models\AgentMaster;
use App\Models\VillageMaster;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class TransferController extends Controller
{
    public function index(){
        $state = StateMaster::all();
        $agents=AgentMaster::all();
        return view('transaction.transferaccount',['state'=>$state,'agents'=>$agents]);
    }

    public function getaccountdetails(Request $request){
        $accountno = $request->account_no;
        $account = MemberAccount::where(['accountNo'=>$accountno,'memberType'=>'Member'])->first(['status','openingDate','accountNo','fatherName','name','gender','panNo','aadharNo','address','wardNo','phone','signature','photo','idProof','accountType']);
        if($account){
            $status = $account->status;
            if($status == "Transfer"){
                return response()->json(['status'=>'error','message'=>'Account Already Transfer !!']);
            }elseif($status == "Active"){
                $accountdata = MemberAccount::where(['accountNo'=>$accountno,'memberType'=>'Member'])->first();
                $accountid = Crypt::encrypt($accountdata->id);
                return response()->json(['status'=>'success','account'=>$account,'accountcredit'=>$accountid]);
            }
            
        }else{
            return response()->json(['status'=>'error','message'=>'Account Not Found !!']);
        }
    }

    public function storetransferaccount(Request $request){
        $transfercurrentTimestamp = now()->format('YmdHis');
        $transfertrandomString = Str::random(8);


        $accountid = Crypt::decrypt($request->account);
        $memberdetails = MemberAccount::where(['id'=>$accountid])->first();
        if($accountid){
            $memberaccount =  MemberAccount::where(['id'=>$accountid])->update(['status'=>'Transfer']);
            $transfer = new TransferedAccount;
            $transfer->accountId = $accountid;
            $transfer->memberType = "Member";
            $transfer->accountType = $memberdetails->accountType;
            $transfer->accountNo = $memberdetails->accountNo;
            $transfer->transferDate = Carbon::createFromFormat('d-m-Y', $request->transfer_opening_date);
            $transfer->transferReason = $request->transfer_reason;
            $transfer->name=$request->transfer_account_name;
            $transfer->fatherName=$request->transfer_father_husband;
            $transfer->gender=$request->USERTYPE;
            $transfer->aadharNo=$request->transfer_aadharno;
            $transfer->panNo=$request->transfer_pan_no;
            $transfer->wardNo=$request->transfer_wardno;
            $transfer->address=$request->transfer_address;
            if($request->hasFile('photo')){
                $transfersignature = $request->file('photo');
                $transfersignaturefilename = 'transfersignature_' . $transfercurrentTimestamp . '_' . $transfertrandomString . '.' . $transfersignature->getClientOriginalExtension();
                $path = public_path().'/uploads/MemberSignature/'.$transfersignaturefilename;
                $transfersignature->move(public_path().'/uploads/MemberSignature/', $transfersignaturefilename);
                $transfer->signature = $transfersignaturefilename; 
            }

            if($request->hasFile('photoo')){
                $transferimg = $request->file('photoo');
                $transferimgfilename = 'jointimage_' . $transfercurrentTimestamp . '_' . $transfertrandomString . '.' . $transferimg->getClientOriginalExtension();
                $path2 = public_path().'/uploads/MemberPhotos/'.$transferimgfilename;
                $transferimg->move(public_path().'/uploads/MemberPhotos/', $transferimgfilename);
                $transfer->photo = $transferimgfilename;
            }

            if($request->hasFile('photo3')){
                $transferidproof = $request->file('photo3');
                $transferprooffilename = 'transfermemberidproof_'.$transfercurrentTimestamp.'_'.$transfertrandomString.'.'.$transferidproof->getClientOriginalExtension();
                $path3 = public_path().'/uploads/MemberIdProof'.$transferprooffilename;
                $transferidproof->move(public_path().'/uploads/MemberIdProof',$transferprooffilename); 
                $transfer->idProof = $transferprooffilename; 
            }
            $transfer->agentId = $request->agent;
            $transfer->state = $request->transfer_state;
            $transfer->district = $request->districtId;
            $transfer->tehsil  = $request->tehsilId;
            $transfer->village  = $request->villageId; 
            $transfer->branchId = 1;
            $transfer->sessionId  = 1;
            $transfer->updatedBy  = auth()->user()->id;
            $transfer->save();

            return response()->json(["status"=>"success","message"=>"Transfer Account Created Successfully !!"]);
        }else{
            return response()->json(['status'=>'error','message'=>'Account Not Found !!']);
        }
    }

    public function update(Request $post){
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
                return response()->json(['status' => "success", "data" => VillageMaster::where(['stateId'=>$post->stateId , 'districtId'=> $post->districtId ,'tehsilId'=> $post->tehsilId ])->get()], 200);
                break; 
            default:
                return response()->json(['status' => "Invalid request type"], 200);
                break;
            }
    }
}
