<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\TransferedAccount;
use App\Models\MemberShare;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMaster;
use Illuminate\Support\Facades\Session;
use App\Models\SessionMaster;

class SavingListController extends Controller
{
    public function index()
    {
        $branch = BranchMaster::first();
        $scheme_names = DB::table('scheme_masters')->orderBy('name','ASC')->get();
        $data['branch'] = $branch;
        $data['scheme_names'] = $scheme_names;
        return view('report.savingList', $data);
    }

    public function getschemessavinglist(Request $post)
    {
        $memberType = $post->memberType;

        $schemes = DB::table('scheme_masters')
            ->where('memberType', $memberType)
            ->where('secheme_type','Saving')
            ->get();

        if (!empty($schemes)) {
            return response()->json([
                'status' => 'success',
                'schemes' => $schemes
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'messages' => 'Related Member Type Schemes Not Found'
            ]);
        }
    }

    public function getData(Request $request){
        $start_date = date('Y-m-d', strtotime($request->startDate));
        $end_date = date('Y-m-d', strtotime($request->endDate));
        $membertype = $request->memberType;
        $schemeType = $request->schemeType;


        $saving_entries = [];

        if ($membertype === 'all') {
                // Get all active saving accounts
                $saving_accounts = DB::table('opening_accounts')
                    ->select('opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name')
                    ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                    ->where('accountname', '=', 'Saving')
                    ->where('opening_accounts.status', '=', 'Active')
                    ->get();

                // Get opening balances for saving accounts
                $opening_balances = DB::table('member_opening_balance')
                    ->where('accType', '=', 'Saving')
                    ->get()
                    ->keyBy('account_no');

                $session_master = SessionMaster::find(Session::get('sessionId'));

                $saving_entries = [];

                foreach ($saving_accounts as $sa) {

                    $opening_amount = isset($opening_balances[$sa->accountNo]) ? $opening_balances[$sa->accountNo]->opening_amount : 0;




                    // $previous_balance = DB::table('member_savings')
                    //     ->where('accountNo', $sa->accountNo)
                    //     ->where('is_delete','=','No')
                    //     ->whereDate('transactionDate', '<', $session_master->startDate)
                    //     ->get();

                    // $opening_amount += ($previous_balance->sum('depositAmount') ?? 0) - ($previous_balance->sum('withdrawAmount') ?? 0);
                    $saving_entries_data = DB::table('member_savings')
                    ->select(
                        'member_savings.accountId',
                        'member_accounts.accountNo as membership',
                        'member_accounts.name',
                        DB::raw('SUM(member_savings.depositAmount) AS total_deposit'),
                        DB::raw('SUM(member_savings.withdrawAmount) AS total_withdraw'),
                        DB::raw($opening_amount . ' AS opening_amount')
                    )
                    ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_savings.accountId')
                    // ->whereDate('member_savings.transactionDate', '>=', $start_date)
                    ->whereDate('member_savings.transactionDate', '<=', $end_date)
                    ->where('member_savings.is_delete','=','No')
                    ->where('member_savings.accountId', $sa->accountNo)
                    ->groupBy(
                        'member_savings.accountId',
                        'member_accounts.accountNo',
                        'member_accounts.name'
                    )
                    ->get();


                    if ($saving_entries_data->isEmpty()) {
                        // Handle accounts with no savings entries
                        $saving_entries[] = [
                            'accountId' => $sa->accountId ?? null,
                            'accountNo' => $sa->accountNo ?? '',
                            'membership' => $sa->membership ?? '',
                            'name' => $sa->name ?? '',
                            'total_deposit' => 0,
                            'total_withdraw' => 0,
                            'opening_amount' => $opening_amount
                        ];
                    } else {
                        $saving_entries = array_merge($saving_entries, $saving_entries_data->toArray());
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'saving_entries' => $saving_entries,
                ]);
        }else{
                $saving_accounts = DB::table('opening_accounts')
                    ->select('opening_accounts.*', 'member_accounts.accountNo as membership', 'member_accounts.name')
                    ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                    ->where('opening_accounts.accounttype', '=', $schemeType)
                    ->where('opening_accounts.status', '=', 'Active')
                    ->get();


                // $opening_balances = DB::table('member_opening_balance')
                //     ->where('accType', '=', 'Saving')
                //     ->get()->keyBy('account_no');

                $session_master = SessionMaster::find(Session::get('sessionId'));

                foreach ($saving_accounts as $sa) {
                    // $opening_amount = isset($opening_balances[$sa->accountNo]) ? $opening_balances[$sa->accountNo]->opening_amount : 0;


                    $previous_balance = DB::table('member_savings')
                        ->where('accountNo', $sa->accountNo)
                        ->where('member_savings.is_delete','=','No')
                        ->whereDate('transactionDate', '<', $session_master->startDate)
                        ->get();

                    // $opening_amount += $previous_balance->sum('depositAmount') - $previous_balance->sum('withdrawAmount');

                    $saving_entries_data = DB::table('member_savings')
                        ->select(
                            'member_savings.accountId',
                            'member_accounts.accountNo as membership',
                            'member_accounts.name',
                            // 'opening_accounts.accountNo as saving_account',
                            DB::raw('SUM(member_savings.depositAmount) AS total_deposit'),
                            DB::raw('SUM(member_savings.withdrawAmount) AS total_withdraw'),
                            // DB::raw($opening_amount . ' AS opening_amount')
                        )
                        // ->leftJoin('opening_accounts', 'opening_accounts.accountNo', '=', 'member_savings.accountId')
                        ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                        // ->whereDate('member_savings.transactionDate', '>=', $session_master->startDate)
                        ->whereDate('member_savings.transactionDate', '<=', $end_date)
                        ->where('member_savings.accountId', $sa->accountNo)
                        ->where('member_savings.is_delete','=','No')
                        ->groupBy(
                            'member_savings.accountNo',
                            'member_accounts.accountNo',
                            'member_accounts.name',
                            'member_savings.accountId',
                            // 'opening_accounts.accountNo',
                        )
                        ->get();

                    if ($saving_entries_data->isEmpty()) {
                        // Handle accounts with no savings entries
                        $saving_entries[] = [
                            'accountId' => $sa->accountId ?? null,  // Handle potential absence of accountId
                            'accountNo' => $sa->accountNo ?? '',
                            'membership' => $sa->membership ?? '',
                            'name' => $sa->name ?? '',
                            'total_deposit' => 0,
                            'total_withdraw' => 0,
                            // 'opening_amount' => $opening_amount
                        ];
                    } else {
                        // Add each entry from $saving_entries_data to the final result
                        $saving_entries = array_merge($saving_entries, $saving_entries_data->toArray());
                    }
                }

                // dd($saving_entries);

                return response()->json([
                    'status' => 'success',
                    'saving_entries' => $saving_entries,
                ]);
        }


    }






    public function print(Request $request)
    {
        // Fetch data from session
        $branch = BranchMaster::first();
        if (session()->has('formattedData')) {
            $formattedData = session('formattedData');

            // Calculate grand total
            $grandTotal = 0;
            foreach ($formattedData as $row) {
                $grandTotal += $row['memberbalence'];
            }

            return view('report.savingPrint', compact('formattedData', 'grandTotal', 'branch'));
        } else {
            return view('report.emptyPrintView');
        }
    }
}
