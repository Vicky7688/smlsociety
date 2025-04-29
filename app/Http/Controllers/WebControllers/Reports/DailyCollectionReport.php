<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchemeMaster;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use App\Models\BranchMaster;

class DailyCollectionReport extends Controller
{
    public function dailyreportindex()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.dailycollectionreport',$data);
    }

    public function dailysavingrepostscheme(Request $post)
    {
        $memberType = $post->memberType;
        $schemes = [];
        if ($memberType === 'All') {
            $schemes = SchemeMaster::where('secheme_type', '=', 'DailyDeposit')->get();
        } else {
            $schemes = SchemeMaster::where('memberType', $memberType)->where('secheme_type', '=', 'DailyDeposit')->get();
        }

        if ($schemes) {
            return response()->json(['status' => 'success', 'schemes' => $schemes]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Schemes Not Found']);
        }
    }

    public function getddsDetails(Request $post)
    {
        $dates = date('Y-m-d', strtotime($post->endDate));
        $memberType = $post->memberType;
        $schemeType = $post->schemeType;

        $query = DB::table('daily_collectionsavings')
            ->select(
                'dailyaccountid', DB::raw('SUM(deposit) AS total_amount'), DB::raw('SUM(withdraw) AS withdraw'),
                'daily_collections.id as ids','daily_collections.interest',
                'member_accounts.accountNo as number','member_accounts.name','daily_collections.interest', 'daily_collections.days','daily_collections.opening_date','daily_collectionsavings.account_no'
            )
            ->leftJoin('daily_collections', 'daily_collections.id', '=', 'daily_collectionsavings.dailyaccountid')
            ->leftJoin('member_accounts', 'member_accounts.accountNo', '=', 'daily_collectionsavings.membershipno')
            // ->whereIn('daily_collections.status',['Active','Pluge'])
            ->where('daily_collectionsavings.receipt_date', '<=', $dates);


        if ($memberType != 'All') {
            $query->whereIn('member_accounts.memberType', (array)$memberType);
        }

        if ($schemeType != 'All') {
            $query->whereIn('daily_collections.schemeid', (array)$schemeType);
        }

        $query = $query->groupBy(
            'dailyaccountid','daily_collections.id','daily_collections.interest',
                'member_accounts.accountNo','member_accounts.name',
                'daily_collections.days','daily_collections.opening_date','daily_collectionsavings.account_no',
                'daily_collections.status',
            )
        ->get();



        return response()->json(['status' => 'success', 'dailyaccounts' => $query]);
    }
}
