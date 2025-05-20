<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SessionMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function sessionindex()
    {
        $sessions = SessionMaster::orderBy('id', 'DESC')->get();
        $data['sessions'] = $sessions;
        return view('master.session', $data);
    }



    public function sessioninsert(request $post)
    {
        $validator = Validator::make($post->all(), [
            "startDate" => "required|unique:session_masters,startDate",
            "endDate" => "required|unique:session_masters,endDate",
            "status" => "required",
            "auditPerformed" => "required",
            // "sortby" => "required|numeric|unique:session_masters,sortno,",
        ]);

        if ($validator->passes()) {
            $session = new  SessionMaster();
            $session->startDate = $post->startDate;
            $session->endDate = $post->endDate;
            $session->status = $post->status;
            $session->auditPerformed = $post->auditPerformed;
            // $session->sortno = $post->sortby;
            $session->updatedBy = $post->user()->id;
            $session->is_delete = 'No';
            $session->save();
            return response()->json(['status' => 'success', 'messages' => 'Record Inserted Successfully']);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Session Already Exit']);
        }
    }

    public function sessionedit(Request $post)
    {
        $id = $post->id;
        $session_id = SessionMaster::where('id', $id)->first();
        if (!empty($session_id)) {
            return response()->json(['status' => 'success', 'session' => $session_id]);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Record Not Found']);
        }
    }

    public function sessionupdate(Request $post)
    {
        $validator = Validator::make($post->all(), [
            "startDate" => "required|date",
            "endDate" => "required|date",
            "status" => "required",
            "auditPerformed" => "required",
            "sortby" => "required|numeric|unique:session_masters,sortno," . $post->id,
        ]);

        $validator->after(function ($validator) use ($post) {
            $existingSession = SessionMaster::where('startDate', $post->startDate)
                ->where('endDate', $post->endDate)
                ->where('id', '!=', $post->id)
                ->first();

            if ($existingSession) {
                $validator->errors()->add('startDate', 'Start Date and End Date already exists.');
            }
        });

        if ($validator->passes()) {

            DB::beginTransaction();
            try {

                $auditPerformed = $post->auditPerformed;
                $session = SessionMaster::where('id', $post->id)->first();
                $session->startDate = $post->startDate;
                $session->endDate = $post->endDate;
                $session->status = $post->status;
                $session->auditPerformed = $post->auditPerformed;
                $session->sortno = $post->sortby;
                $session->updatedBy = $post->user()->id;
                $session->is_delete = 'No';
                $session->save();

                DB::table('financial_year_end')->where('sessionId', $post->id)->update(['auditPerformed' => $auditPerformed]);
                DB::commit();
                return response()->json(['status' => 'success', 'messages' => 'Record Updated Successfully']);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Fail',
                    'error' => $e->getMessage(),
                    'lines' => $e->getLine()
                ]);
            }
        } else {
            return response()->json(['status' => 'Fail', 'messages' => $validator->errors()->first()]);
        }
    }

    public function changescurrentdate(Request $post)
    {
        $validatedData = $post->validate([
            'currentdate' => 'required|date_format:d-m-Y',
        ]);
        session()->put('currentdate', $validatedData['currentdate']);
        return response()->json(['message' => 'Current date updated successfully.']);
    }
}
