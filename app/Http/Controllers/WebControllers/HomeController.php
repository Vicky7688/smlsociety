<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MemberAccount;
use App\Models\MemberSaving;
use App\Models\MemberShare;
use App\Models\MemberFd;
use App\Models\Mis;
use App\Models\MemberLoan;
use App\Models\ReCurringRd;
use App\Models\LedgerMaster;
use App\Models\LoanRecovery;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function updateledger()
    {
    }

    public function comingsoon()
    {
        return view('comingsoon');
    }

    public function index(Request $post){
        // $membershareAmount = DB::table('member_accounts')->get();
        // DB::beginTransaction();
        // try{
        //     foreach($membershareAmount as $row){
        //         $serialNo = 'Share' . uniqid();



        //         $ids = DB::table('member_shares')->insertGetId([
        //             'serialNo' => $serialNo,
        //             'accountId' => $row->id,
        //             'accountNo' => $row->accountNo,
        //             'memberType' => $row->memberType,
        //             'groupCode' => 'SHAM001',
        //             'ledgerCode' => 'SHAM001',
        //             'shareNo' => $row->accountNo,
        //             'transactionDate' => '2024-03-31',
        //             'transactionType' => 'Deposit',
        //             'depositAmount' => $row->shareNo,
        //             'withdrawAmount' => 0,
        //             'dividendAmount' => 0,
        //             'chequeNo' => 0,
        //             'narration' => null,
        //             'branchId' => null,
        //             'agentId' => null,
        //             'sessionId' => 1,
        //             'updatedBy' => 1,
        //             'is_delete' => 'No',
        //             'txnType' => 'main',
        //             'deleted_at' => null,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);


        //         DB::table('general_ledgers')->insert([
        //             'serialNo' => $serialNo,
        //             'accountId' => $ids,
        //             'accountNo' => $row->accountNo,
        //             'memberType' => $row->memberType,
        //             'groupCode' => 'SHAM001',
        //             'ledgerCode' => 'SHAM001',
        //             'formName' => 'Share Deposit',
        //             'referenceNo' => $ids,
        //             'entryMode' => 'manual',
        //             'transactionDate' => '2024-03-31',
        //             'transactionType' => 'Cr',
        //             'transactionAmount' => $row->shareNo,
        //             'narration' => 'Share Deposit',
        //             'branchId' => 1,
        //             'agentId' => null,
        //             'sessionId' => 1,
        //             'updatedBy' => 1,
        //             'is_delete' => 'No',
        //             'deleted_at' => null,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);

        //         DB::table('general_ledgers')->insert([
        //             'serialNo' => $serialNo,
        //             'accountId' => $ids,
        //             'accountNo' => $row->accountNo,
        //             'memberType' => $row->memberType,
        //             'groupCode' => 'BANK001',
        //             'ledgerCode' => 'HPC01',
        //             'formName' => 'Share Deposit',
        //             'referenceNo' => $ids,
        //             'entryMode' => 'manual',
        //             'transactionDate' => '2024-03-31',
        //             'transactionType' => 'Dr',
        //             'transactionAmount' => $row->shareNo,
        //             'narration' => 'Share Deposit',
        //             'branchId' => 1,
        //             'agentId' => null,
        //             'sessionId' => 1,
        //             'updatedBy' => 1,
        //             'is_delete' => 'No',
        //             'deleted_at' => null,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }

        //     DB::commit();

        // }catch(\Exception $e){
        //     DB::rollBack();
        //     return response()->json(['status' => 'Fail','messages' => $e->getMessage(),'Line' => $e->getLine()]);
        // }




        $data['title'] = "Dashboard";
        $data['memberac'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'Member')->count();
        $data['nonmember'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'NonMember')->count();
        // $data['staff'] = MemberAccount::where('is_delete', 'no')->where('memberType', 'Staff')->count();
        $data['membershare'] = MemberShare::where('is_delete', 'no')->count();
        $data['memberloan'] = MemberLoan::where('is_delete', 'no')->count();
        $data['contributions'] = Contribution::where('is_delete', 'no')->count();

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $previous_amount = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');
        $cashcode = LedgerMaster::where(['ledgerCode' => 'C002'])->first();
        return view('dashboard')->with($data);
    }
}
