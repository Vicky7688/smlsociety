<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MemberAccount;
use App\Models\ReCurringRd;
use App\Models\RdInstallment;
use App\Models\SchemeMaster;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\BranchMaster;


class RDReportController extends Controller
{
    public function index()
    {
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        return view('report.rdReport',$data);
    }

    public function getrdschemes(Request $post)
    {
        $memberType = $post->memberType;
        $schemes = [];
        if ($memberType === 'All') {
            $schemes = SchemeMaster::where('secheme_type', '=', 'RD')->get();
        } else {
            $schemes = SchemeMaster::where('memberType', $memberType)->where('secheme_type', '=', 'RD')->get();
        }

        if ($schemes) {
            return response()->json(['status' => 'success', 'schemes' => $schemes]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Schemes Not Found']);
        }
    }

    public function getrdData(Request $post)
    {
        $dates = Carbon::parse($post->endDate)->format('Y-m-d');
        $memberType = $post->memberType;
        $schemeType = $post->schemeType;

        $sessionId = session('sessionId');
        $session = DB::table('session_masters')->where('id', $sessionId)->first();
        $startDate = $session->startDate;

        $details = DB::table('re_curring_rds')
            ->selectRaw("
                re_curring_rds.rd_account_no,
                re_curring_rds.interest,
                re_curring_rds.month,
                re_curring_rds.date,
                member_accounts.accountNo,
                member_accounts.name,
                member_accounts.memberType as amtp,
                rd_receiptdetails.rc_account_no as rcac,
                rd_receiptdetails.memberType as rc_member_type,
                SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) as amount,
                IF(
                    re_curring_rds.date >= ?
                    AND (re_curring_rds.actual_maturity_date >= ? OR re_curring_rds.actual_maturity_date IS NULL)
                    AND re_curring_rds.status NOT IN ('Closed', 'Mature', 'PreMature'),
                    'Active',
                    re_curring_rds.status
                ) AS current_status
            ", [$dates, $startDate, $dates])
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 're_curring_rds.accountNo')
                     ->on('member_accounts.memberType', '=', 're_curring_rds.memberType');
            })
            ->leftJoin('rd_receiptdetails', function ($join) {
                $join->on('rd_receiptdetails.rc_account_no', '=', 're_curring_rds.id')
                     ->on('rd_receiptdetails.memberType', '=', 're_curring_rds.memberType');
            })
            ->where(function ($query) use ($dates) {
                $query->where(function ($q) use ($dates) {
                    $q->where('re_curring_rds.date', '<=', $dates)
                      ->where(function ($q2) use ($dates) {
                          $q2->whereNull('re_curring_rds.actual_maturity_date')
                             ->orWhere('re_curring_rds.actual_maturity_date', '>=', $dates);
                      })
                      ->whereNotIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature']);
                })
                ->orWhere(function ($q) use ($dates) {
                    $q->whereIn('re_curring_rds.status', ['Closed', 'Mature', 'PreMature'])
                      ->where('re_curring_rds.date', '<=', $dates)
                      ->where('re_curring_rds.actual_maturity_date', '>', Carbon::parse($dates)->subDay()->format('Y-m-d'));
                });
            });

        // Apply filters
        if ($memberType !== 'All') {
            $details->where('re_curring_rds.memberType', $memberType);
        }

        if ($schemeType !== 'All') {
            $details->where('re_curring_rds.secheme_id', $schemeType);
        }

        $details->groupBy(
            're_curring_rds.rd_account_no',
            're_curring_rds.interest',
            're_curring_rds.month',
            're_curring_rds.date',
            'member_accounts.accountNo',
            'member_accounts.name',
            'member_accounts.memberType',
            'rd_receiptdetails.rc_account_no',
            'rd_receiptdetails.memberType',
            're_curring_rds.actual_maturity_date',
            're_curring_rds.status'
        )
        ->havingRaw("SUM(CASE WHEN rd_receiptdetails.payment_date <= ? THEN rd_receiptdetails.amount ELSE 0 END) > 0", [$dates])
        ->orderBy('re_curring_rds.date', 'ASC');

        $result = $details->get();

        return response()->json(['status' => 'success', 'rdaccount' => $result]);
    }





    public function print(Request $request){

        $endDate = $request->input('endDate');
        $memberType = $request->input('memberType');

        // Fetch data from the database
        $data = ReCurringRd::with('memberAccount')
            ->with(['rdInstallments' => function ($query) {
                $query->select('rd_id', DB::raw('SUM(paid_amount) as paid_amount'))
                    ->groupBy('rd_id');
            }])->get();

        $formattedData = [];
        $grandTotal = 0;
        $grandInterestTotal = 0;

        foreach ($data as $row) {
            $principal = $row->rdInstallments->first()->paid_amount;
            $rate = $row->interest;
            $startDate = new Carbon($row->date);


            $months = $this->calculateMonthDifference($startDate, $endDate);

            if ($months >= 0) {
                $rdAmount = $this->calculateRDAmount($principal, $rate, $months);
                $interest = $this->calculateInterest($principal, $rate, $months);
                $totalAmount = $rdAmount;

                $formattedData[] = [
                    'id' => $row->id,
                    'accountNo' => optional($row->memberAccount)->accountNo,
                    'name' => optional($row->memberAccount)->name,
                    'date' => $row->date,
                    'principalAmount' => $principal,
                    'interestAmount' => $interest,
                    'totalAmount' => $totalAmount,
                ];

                $grandTotal += $totalAmount;
                $grandInterestTotal += $interest;
            }
        }
        return view('report.rdPrint', compact('formattedData', 'grandTotal', 'grandInterestTotal'));
    }

    public function calculateRDAmount($principal, $rate, $months)
    {
        $rdAmount = $principal;

        for ($i = 0; $i < $months; $i++) {
            $interest = ($rdAmount * $rate) / (12 * 100);
            $rdAmount += $interest;
        }

        return $rdAmount;
    }

    public function calculateInterest($principal, $rate, $months)
    {
        $rdAmount = $this->calculateRDAmount($principal, $rate, $months);
        return $rdAmount - $principal;
    }

    public function calculateMonthDifference($startDate, $endDate)
    {
        $startDate = new Carbon($startDate);
        $endDate = new Carbon($endDate);
        $diffInMonths = $startDate->diffInMonths($endDate);
        return abs($diffInMonths);
    }
}
