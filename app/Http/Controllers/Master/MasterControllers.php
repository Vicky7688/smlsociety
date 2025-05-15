<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\GroupMaster;
use App\Models\LedgerMaster;
use App\Models\FdTypeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class MasterControllers extends Controller
{

    //____________________________Group Master Work_______________________
    public function GroupIndex()
    {
        // $data['groups'] = GroupMaster::orderBy('name','DESC')->get();
        $data['groups'] = DB::table('group_masters')
            ->select(
                'group_masters.*',
                'users.name as user_name'
            )
            ->leftJoin('users', 'users.id', '=', 'group_masters.updatedBy')
            ->get();
        return view('master.group', $data);
    }

    //___________Generate Group Code


    public function GenerateGroupCode(Request $post)
    {
        $rules = [
            'group_name' => 'required'
        ];

        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'messages' => 'Check Fields'
            ]);
        }

        $group_name = strtoupper($post->group_name);
        $prefix = substr($group_name, 0, 3);
        $newgroup_code = '';

        DB::beginTransaction();
        try {
            // Lock existing group codes with same prefix
            $last_group = DB::table('group_masters')
                ->where('groupCode', 'LIKE', $prefix . '%')
                ->orderBy('groupCode', 'desc')
                ->lockForUpdate()
                ->first();

            if (!empty($last_group)) {
                $last_number = substr($last_group->groupCode, -3);
                $new_number = str_pad((int)$last_number + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $new_number = '001';
            }

            $newgroup_code = $prefix . $new_number;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'newgroup_code' => $newgroup_code
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred: ' . $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    //___________ Group Insert
    public function GroupInsert(Request $post)
    {

        $insert['updatedBy'] = $post->user()->id;

        $validator = Validator::make($post->all(), [
            'name' => 'required',
            'groupCode' => 'required',
            'type' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {
            GroupMaster::create([
                'name' => $post->name,
                'groupCode' => $post->groupCode,
                'headName' => $post->name,
                'type' => $post->type,
                'showJournalVoucher' => 'Yes',
                'status' => $post->status,
                'updatedBy' => $insert['updatedBy'],
            ]);

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Inserted'
            ]);
        }
    }

    //_____________Update Group
    public function UpdateGroup(Request $post)
    {
        $group_id = $post->id;

        $update_group_id = GroupMaster::where('id', $group_id)->first();

        if (!empty($update_group_id)) {
            $update_group_id->name = $post->name;
            $update_group_id->type = $post->type;
            $update_group_id->status = $post->status;
            $update_group_id->save();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Some Technical Issue'
            ]);
        }
    }

    //_____________Delete Group
    public function DeleteGroup(Request $post)
    {
        $groupId = $post->groupId;
        $exits_groupId = GroupMaster::where('id', $groupId)->first();

        if (is_null($exits_groupId)) {
            //_______if Not found
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        } else {
            //_____________if found Group Id then Delete
            $exits_groupId->delete();
            return response()->json([
                'status' => 'success',
                'messages' => 'Record Deleted successfully'
            ]);
        }
    }


    //____________________________Ledger's Master Work_______________________

    //_________Ledger View Page
    public function LedgerIndex()
    {
        $groups = GroupMaster::orderBy('name', 'DESC')->get();
        // $ledgers = LedgerMaster::orderBy('name','DESC')->get();
        $data['ledgers'] = DB::table('ledger_masters')
            ->select(
                'ledger_masters.*',
                'users.name as user_name'
            )
            ->leftJoin('users', 'users.id', '=', 'ledger_masters.updatedBy')
            ->get();
        // $data['ledgers'] = $ledgers;
        $data['groups'] = $groups;
        return view('master.ledger', $data);
    }


    //_________Generate Ledger Code
    public function GenerateLedgerCode(Request $post)
    {
        $rules = [
            'ledger_name' => 'required'
        ];
        $validator = Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Check Fields']);
        }
        $ledger_name = strtoupper($post->ledger_name);
        $first_name = substr($ledger_name, 0, 4); // Extract first 4 letters
        $new_number = '01';
        DB::beginTransaction();
        try {
            $last_group_code = LedgerMaster::where('ledgerCode', 'LIKE', $first_name . '%')
                ->orderBy('ledgerCode', 'desc')
                ->lockForUpdate()
                ->first();
            if (!empty($last_group_code)) {
                // Extract the numeric part and increment
                $last_number = (int)substr($last_group_code->ledgerCode, strlen($first_name));
                $new_number = str_pad($last_number + 1, 2, '0', STR_PAD_LEFT);
            }
            // Generate a unique ledger code
            do {
                $newgroup_code = $first_name . $new_number;
                $exists = LedgerMaster::where('ledgerCode', $newgroup_code)->exists();
                if ($exists) {
                    $new_number = str_pad((int)$new_number + 1, 2, '0', STR_PAD_LEFT);
                }
            } while ($exists);
            DB::commit();
            return response()->json(['status' => 'success', 'newgroup_code' => $newgroup_code]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'messages' => 'An error occurred: ' . $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }


    //_________Insert Ledger
    public function LedgerInsert(Request $post)
    {
        $insert['updatedBy'] = $post->user()->id;

        $validator = Validator::make($post->all(), [
            'name' => 'required',
            'groupCode' => 'required',
            'openingType' => 'required',
            'status' => 'required'
        ]);



        if ($validator->passes()) {
            LedgerMaster::create([
                'name' => $post->name,
                'groupCode' => $post->groupCode,
                'ledgerCode' => $post->ledgerCode,
                'openingAmount' => $post->openingAmount,
                'openingType' => $post->openingType,
                'status' => $post->status,
                'is_delete' => 'No',
                'updatedBy' => $insert['updatedBy'],
            ]);

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Inserted'
            ]);
        }
    }

    //____________Update Ledger
    public function UpdateLedger(Request $post)
    {
        $ledger_id = $post->id;

        $update_ledger_id = LedgerMaster::where('id', $ledger_id)->first();

        if (!empty($update_ledger_id)) {
            $update_ledger_id->groupCode = $post->groupCode;
            $update_ledger_id->name = $post->name;
            $update_ledger_id->openingAmount = $post->openingAmount;
            $update_ledger_id->openingType = $post->openingType;
            $update_ledger_id->status = $post->status;
            $update_ledger_id->is_delete = 'No';
            $update_ledger_id->save();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Some Technical Issue'
            ]);
        }
    }

    //______________Delete Ledger
    public function DeleteLedger(Request $post)
    {
        $ledgerId = $post->ledgerId;
        $exits_ledgerId = LedgerMaster::where('id', $ledgerId)->first();

        if (is_null($exits_ledgerId)) {
            //_______if Not found
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        } else {
            //_____________if found Ledger Id then Delete
            $exits_ledgerId->delete();
            return response()->json([
                'status' => 'success',
                'messages' => 'Record Deleted successfully'
            ]);
        }
    }




    //____________________________FdType Master Work_______________________

    public function FdTypeIndex()
    {
        $FdTypes = FdTypeMaster::orderBy('type', 'DESC')->get();
        return view('master.fdtype', ['FdTypes' => $FdTypes]);
    }

    //___________ FdType Insert

    public function FdTypeInsert(Request $post)
    {
        $insert['updatedBy'] = $post->user()->id;
        $validator = Validator::make($post->all(), [
            'type' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            FdTypeMaster::create([
                'type' => $post->type,
                'status' => $post->status,
                'updatedBy' => $insert['updatedBy'],

            ]);

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Inserted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Inserted'
            ]);
        }
    }

    //_____________Update FdType
    public function UpdateFdType(Request $post)
    {
        $FdType_id = $post->id;

        $update_FdType_id = FdTypeMaster::where('id', $FdType_id)->where('id', '!=', 1)->first();

        if (!empty($update_FdType_id)) {
            $update_FdType_id->type = $post->type;
            $update_FdType_id->status = $post->status;
            $update_FdType_id->updatedBy = $post->user()->id;
            $update_FdType_id->save();

            return response()->json([
                'status' => 'success',
                'messages' => 'Record Updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Some Technical Issue'
            ]);
        }
    }

    //_____________Delete FdType
    public function DeleteFdType(Request $post)
    {
        $FdTypeId = $post->FdTypeId;
        $exits_FdTypeId = FdTypeMaster::where('id', $FdTypeId)->where('id', '!=', 1)->first();

        if (is_null($exits_FdTypeId)) {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Record Not Found'
            ]);
        } else {
            //_____________if found FdType Id then Delete
            $exits_FdTypeId->delete();
            return response()->json([
                'status' => 'success',
                'messages' => 'Record Deleted successfully'
            ]);
        }
    }









    //_________________________________________ END _________________________________________________

}
