<?php

namespace App\Http\Controllers\transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\opening_accounts;
use App\Models\FdTypeMaster;
use DB;

class AccountOpeningControllers extends Controller
{
    public function AccountOpenIndex()
    {
        $agent_masters = DB::table('agent_masters')->get();
        $FdTypeMaster = FdTypeMaster::where('status', 'Active')->orderBy('type', 'ASC')->get();
        return view('transaction.accountopeningform', compact('agent_masters', 'FdTypeMaster'));
    }
    public function addaccount(Request $request)
    {

        try {
            // Validate the incoming request
            $this->validate($request, [
                'transactionDate' => 'required|date',
                'membershipno' => 'required|string',
                'accountNo' => 'required|string',
                'accounttype' => 'required|string',
                'schemetype' => 'required|integer',
                // 'amount' => 'required|numeric',
                'roi' => 'required|numeric',
                // 'tanure' => 'required|integer',
                // 'maturityamount' => 'required|numeric',
                // 'maturitydate' => 'required|date',
                // 'lockinperiod' => 'required|integer',
                'agentId' => 'required|integer',
                'fdType' => 'nullable|integer',
            ]);

            $transactionDate = date('Y-m-d', strtotime($request->transactionDate));
            $maturitydate = date('Y-m-d', strtotime($request->maturitydate));
            $lockindate = date('Y-m-d', strtotime($request->lockindate));

            // Check for duplicate entries


            if($request->accounttype=="FD"){

// dd($request->accountNo,$request->schemetype);
                $duplicateCheck = DB::table('opening_accounts')
                ->where('accountNo', $request->accountNo)
                ->where('accountname', $request->accounttype)
                ->where('schemetype', $request->schemetype)
                ->where('membertype', $request->membertype)
                ->exists();
                // dd($duplicateCheck);
            }else{
                $duplicateCheck = DB::table('opening_accounts')
                ->where('accountNo', $request->accountNo)
                ->where('accountname', $request->accounttype)
                ->where('membertype', $request->membertype)
                ->exists();
            }

            if ($duplicateCheck) {
                return response()->json(['success' => false, 'errors' => ['accountNo' => ['Duplicate entry found for this account number.']]]);
            }

            $result = $this->isDateBetween(date('Y-m-d', strtotime($request->transactionDate)));

            if (!$result) {
                return response()->json(['status' => 'fail', 'errors' =>  ['messages' => ["Please Check your session"]]]);
            }

            // Create new account entry
            $oppp = new opening_accounts();
            // Assign values to the model's properties
            $oppp->transactionDate = $transactionDate;
            $oppp->membershipno = $request->membershipno;
            $oppp->membertype = $request->membertype;
            $oppp->accountNo = $request->accountNo;
            $oppp->accounttype = DB::table('scheme_masters')->where('secheme_type', '=', $request->accounttype)->value('id');
            $oppp->accountname = $request->accounttype;
            $oppp->schemetype = $request->schemetype;
            $oppp->schemename = DB::table('scheme_masters')->where('id', '=', $request->schemetype)->value('name');
            $oppp->amount = $request->amount;
            $oppp->roi = $request->roi;
            $oppp->tanure = $request->tanure;
            $oppp->maturityamount = $request->maturityamount;
            $oppp->maturitydate = $maturitydate;
            $oppp->lockinperiod = $request->lockinperiod;
            $oppp->lockindate = $lockindate;
            $oppp->status = $request->status;
            $oppp->agentId = $request->agentId;
            $oppp->fdtypeid = $request->fdType;
            $oppp->fdtype = DB::table('fd_type_master')->where('id', '=', $request->fdType)->value('type');
            $oppp->save();
            // Return success message
            return response()->json(['success' => true, 'message' => 'Account added successfully.'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json(['success' => false, 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Return generic error message
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getschemes(Request $request)
    {
        $schems = DB::table('scheme_masters')->where('secheme_type', '=', $request->name)->get();
        return response()->json($schems);
    }

    public function getschemeall(Request $request)
    {
        $schems = DB::table('scheme_masters')->where('secheme_type', '=','FD')->where('fdtype', '=',$request->id)->get();
        return response()->json($schems);
    }
    public function getschemesamount(Request $request)
    {

        $schems = DB::table('scheme_masters')->where('id', '=', $request->id)->first();

        return response()->json($schems);
    }

    public function fetdatamm(Request $request)
    {
        $detail = opening_accounts::where('membershipno', $request->accountNo)->where('opening_accounts.membertype',$request->memberType)
            ->leftJoin('agent_masters', 'opening_accounts.agentId', '=', 'agent_masters.id')
            ->select('agent_masters.name as agentname', 'opening_accounts.*')
            ->get();
        $member = DB::table('member_accounts')->where('accountNo', $request->accountNo)->where('memberType',$request->memberType)->first();
        if ($detail && $member) {
            return response()->json([
                'status' => true,
                'detail' => $detail,
                'member' => $member
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No records found for the provided account number.'
            ]);
        }
    }


    public function deletefetdatamm(Request $request){

        $accountNo = opening_accounts::where('id', '=', $request->id)->first();

        $type = $accountNo->accountname;
        switch($type){
            case 'Saving':
                $saving = DB::table('member_savings')->where('accountNo',$accountNo->membershipno)->where('memberType',$accountNo->membertype)->first();

                if(!empty($saving)){
                    return response()->json(['status' => 'Fail', 'messages' => 'The Saving Account contains data and cannot be deleted.']);
                }else{
                    DB::table('opening_accounts')->where('id',$request->id)->delete();

                    // opening_accounts::where('id', '=', $request->id)->delete();
                    return response()->json(['status' => true, 'accountNo' => $accountNo, 'membertype' => $accountNo->membertype]);
                }
            break;

            case 'FD':
                opening_accounts::where('id', '=', $request->id)->delete();
               return response()->json(['status' => true, 'accountNo' => $accountNo, 'membertype' => $accountNo->membertype]);

            break;

            case 'RD':
                $rd = DB::table('re_curring_rds')->where('accountId',$accountNo->accountNo)->first();
                if($rd){
                    return response()->json(['status' => 'Fail', 'messages' => 'The RD Account contains data and cannot be deleted.']);
                }else{
                    opening_accounts::where('id', '=', $request->id)->delete();
                    return response()->json(['status' => true, 'accountNo' => $accountNo, 'membertype' => $accountNo->membertype]);
                }
            break;
            case 'DailyDeposit':
                $dailysaving = DB::table('daily_collections')->where('account_no',$accountNo->accountNo)->first();
                if($dailysaving){
                    return response()->json(['status' => 'Fail', 'messages' => 'The Daily Saving Account contains data and cannot be deleted.']);
                }else{
                    opening_accounts::where('id', '=', $request->id)->delete();
                    return response()->json(['status' => true, 'accountNo' => $accountNo, 'membertype' => $accountNo->membertype]);
                }
            break;

            case 'Daily Loan':
                    opening_accounts::where('id', '=', $request->id)->delete();
                    return response()->json(['status' => true, 'accountNo' => $accountNo, 'membertype' => $accountNo->membertype]);
            break;


        }
    }
}
