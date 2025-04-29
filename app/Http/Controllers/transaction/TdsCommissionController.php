<?php

namespace App\Http\Controllers\transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TDS;
use Illuminate\Support\Facades\Validator;

class TdsCommissionController extends Controller
{
    //______________TDS view Page
    public function TdsIndex(){
        $tds_slabs = TDS::orderBy('start_date','DESC')->get();
        $data['tds_slabs'] = $tds_slabs;
        return view('master.tds',$data);
    }

    //____________Insert TDS Slab
    public function TdsInsert(Request $post){
        $validator = Validator::make($post->all(),[
            'start_date' => 'required',
            'tds_start_amount' => 'required|numeric',
            'tds_end_amount' => 'required|numeric',
            'tds_rate' => 'required|numeric',
            'status' => 'required',
        ]);

        if($validator->passes()){
            $tds_insert = new TDS();
            $tds_insert->start_date = date('Y-m-d',strtotime($post->start_date));
            $tds_insert->start_amount = $post->tds_start_amount;
            $tds_insert->end_amount = $post->tds_end_amount;
            $tds_insert->tds_rate = $post->tds_rate;
            $tds_insert->status = $post->status;
            $tds_insert->save();
            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Something Went Wrong'
            ]);
        }
    }


    public function TdsStatusEdit(Request $post){
        $id = $post->id;
        $tds_id = TDS::where('id',$id)->first();
        if(is_null($tds_id)){
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }else{
            return response()->json(['status' => 'success', 'tds_id' => $tds_id]);
        }
    }

    public function TdsStatusUpdate(Request $post){
        $id = $post->updateid;

        $tds_id = TDS::where('id',$id)->first();
        if(is_null($tds_id)){
            return response()->json(['status' => 'Fail', 'messages' => 'Record Not Found']);
        }else{
            $tds_id->status =$post->editstatus;
            $tds_id->save();
            return response()->json(['status' => 'success', 'messages' => 'Record Updated Successfully']);
        }

    }
}
