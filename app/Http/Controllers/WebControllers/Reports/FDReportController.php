<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MemberFd;
use App\Models\MemberFdScheme;
use App\Models\BranchMaster;
use App\Models\FdTypeMaster;
use App\Models\opening_accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class FDReportController extends Controller
{
    public function index()
    {
        $schemes = DB::table('scheme_masters')->where('secheme_type', 'FD')->get();
        foreach ($schemes as $s) {
            DB::table('ledger_masters')
                ->where('groupCode', '=', 'FDOM001')
                ->where('reference_id', $s->id)
                ->update(['ledgerCode' => $s->scheme_code, 'scheme_code' => $s->scheme_code]);

            $fd =  DB::table('member_fds_scheme')
                ->where('groupCode', '=', 'FDOM001')
                ->where('secheme_id', $s->id)->get();
            // ->update(['ledgerCode' => $s->scheme_code]);

            foreach ($fd as $f) {
                $dd =  DB::table('general_ledgers')
                    ->where('groupCode', '=', 'FDOM001')
                    ->where('serialNo', $f->serialNo)
                    ->where('referenceNo', $f->id)
                    ->update(['ledgerCode' => $f->ledgerCode]);
            }
            // ->update(['ledgerCode' => $f->ledgerCode]);
        }
        // dd($dd);



        $branch = BranchMaster::first();
        $OpeningAccounts = DB::table('opening_accounts as oa')
            ->join('fd_type_master as ft', 'oa.fdtypeid', '=', 'ft.id')
            ->join('scheme_masters as sm', 'oa.schemetype', '=', 'sm.id')
            ->where('oa.accountname', 'FD')
            ->select('oa.fdtypeid', 'oa.schemetype', 'ft.type as fd_type', 'sm.name as scheme_name')
            ->get()
            ->toArray();
        $FdTypeMaster = FdTypeMaster::all();
        return view('report.fdReport', compact('branch', 'FdTypeMaster', 'OpeningAccounts'));
        // return view('report.fdReport', compact('branch', 'FdTypeMaster'));
    }


    public function getfdallschemes(Request $post)
    {
        $rules = [
            "fdtype" => "required",
            "endDate" => "required",
            "memberType" => "required"
        ];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'messages' => 'Check All Inputs']);
        }

        $fdid = $post->fdtype;
        $endDate = date('Y-m-d', strtotime($post->endDate));
        $memberType = $post->memberType;


        $schemes = DB::table('scheme_masters')
            ->where('scheme_masters.secheme_type', 'FD')
            ->where('fdtype', $fdid)
            ->where('memberType', $memberType)
            ->whereDate('start_date', '<=', $endDate)
            ->get();

        if (!empty($schemes)) {
            return response()->json(['status' => 'success', 'schemes' => $schemes]);
        } else {
            return response()->json(['status' => 'Fail', 'messages' => 'Sechemes Not Found']);
        }
    }

    // public function getfdreportdata(Request $post)
    // {
    //     $rules = [
    //         "endDate" => "required",
    //         "memberType" => "required",
    //         "groupCode" => "required",
    //         "schemeType" => "required"
    //     ];

    //     $validator = Validator::make($post->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 'Fail', 'messages' => 'Check All Input']);
    //     }

    //     $endDate = date('Y-m-d', strtotime($post->endDate));

    //     $query = DB::table('member_fds_scheme')
    //         ->select(
    //             'member_fds_scheme.*',
    //             // 'member_accounts.accountNo',
    //             'member_accounts.name',
    //             'member_accounts.memberType as mt',
    //             'member_fds_scheme.openingDate',
    //             'member_fds_scheme.fdType',
    //             'member_fds_scheme.secheme_id',
    //             'member_fds_scheme.status'
    //         )
    //         ->leftJoin('member_accounts', function ($join) {
    //             $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
    //                 ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
    //         })
    //         ->whereDate('member_fds_scheme.openingDate', '<=', $endDate)
    //         ->whereIn('member_fds_scheme.status', ['Active', 'Pluge']);

    //     // Apply dynamic conditions
    //     if ($post->memberType !== 'All') {
    //         $query->where('member_fds_scheme.memberType', $post->memberType);
    //     }

    //     if (isset($post->fdType) && $post->fdType !== 'All') {
    //         $query->where('member_fds_scheme.fdType', $post->fdType);
    //     }

    //     if ($post->schemeType !== 'All') {
    //         $query->where('member_fds_scheme.secheme_id', $post->schemeType);
    //     }

    //     $result = $query->orderBy('member_accounts.accountNo', 'ASC')->get();

    //     return response()->json([
    //         'status' => 'Success',
    //         'allData' => $result
    //     ]);
    // }


    public function getData(Request $request)
    {
        $memberType = $request->memberType;
        $endDate = date('Y-m-d', strtotime($request->endDate));

        $depositType = $request->depositType;
        $schemeType = $request->schemeType;

        $sessionId = session('sessionId');
        $session = DB::table('session_masters')->where('id', $sessionId)->first();
        $startDate = $session->startDate;

        $query = DB::table('member_fds_scheme')
            ->select(
                'member_fds_scheme.*',
                'member_accounts.accountNo as ac',
                'member_accounts.name',
                'member_accounts.memberType as mt',
                'member_fds_scheme.openingDate',
                'member_fds_scheme.fdType',
                'member_fds_scheme.secheme_id',
                DB::raw(
                    "IF(
                        member_fds_scheme.openingDate >= '$startDate'
                        AND (member_fds_scheme.actualMaturityDate <= '$endDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                        AND member_fds_scheme.status != 'Matured'
                        AND member_fds_scheme.status != 'Renewed',
                        member_fds_scheme.status, 'Other'
                    ) AS status"
                )
            )
            ->leftJoin('member_accounts', function ($join) {
                $join->on('member_accounts.accountNo', '=', 'member_fds_scheme.membershipno')
                    ->on('member_accounts.memberType', '=', 'member_fds_scheme.memberType');
            })
            ->whereDate('member_fds_scheme.openingDate', '<=', $endDate)
            ->where('member_fds_scheme.memberType', $memberType);

        if ($depositType != 'All') {
            if (is_array($depositType)) {
                $query->whereIn('fdType', $depositType);
            } else {
                $query->where('fdType', $depositType);
            }
        }

        if ($schemeType != 'All') {
            if (is_array($schemeType)) {
                $query->whereIn('secheme_id', $schemeType);
            } else {
                $query->where('secheme_id', $schemeType);
            }
        }

        // Exclude records that are "Matured" or "Renewed" before or within the specified period
        $query->whereRaw(
            "NOT (
                (member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
                AND (
                    member_fds_scheme.openingDate <= '$endDate'
                    AND (member_fds_scheme.actualMaturityDate <= '$endDate' OR member_fds_scheme.actualMaturityDate IS NULL)
                )
            )"
        );

        // Include records that became "Matured" or "Renewed" after the specified period
        $query->orWhereRaw(
            "(member_fds_scheme.status = 'Matured' OR member_fds_scheme.status = 'Renewed')
            AND member_fds_scheme.openingDate > '$endDate'"
        );

        $data = $query->orderBy('member_fds_scheme.openingDate', 'ASC')->get();

        return response()->json($data);
    }



    public function print(Request $request)
    {
        $memberType = $request->input('memberType');
        $endDate = $request->input('endDate');
        $depositType = $request->input('depositType');

        $data = MemberFdScheme::with('memberAccount')->get();

        $grandTotal = 0;
        $grandInterestTotal = 0;
        $formattedData = [];

        foreach ($data as $item) {
            $principal = $item->principalAmount;
            $interestRate = $item->interestRate;
            $openingDate = Carbon::parse($item->openingDate);
            $currentDate = Carbon::parse($endDate);
            $daysElapsed = $currentDate->diffInDays($openingDate);
            $totalAmount = $principal;

            if ($daysElapsed > 0) {
                // Calculate daily interest based on the item data
                $dailyInterest = $this->calculateDailyInterest($item->interestType, $principal, $interestRate, $daysElapsed);
                $interest = $dailyInterest; // Total interest over the elapsed days
                $totalAmount += $interest;
                $interestAmount = number_format($interest, 2);
            } else {
                $interestAmount = '0.00';
            }

            $formattedData[] = [
                'accountNo' => $item->accountNo,
                'fdNo' => $item->fdNo,
                'name' => optional($item->memberAccount)->name,
                'openingDate' => $item->openingDate,
                'principalAmount' => number_format($principal, 2),
                'interestRate' => $interestRate,
                'interestAmount' => $interestAmount,
                'totalAmount' => number_format($totalAmount, 2),
            ];

            $grandTotal += $totalAmount;
            $grandInterestTotal += $interest;
        }

        return view('report.fdPrint', compact('formattedData', 'grandTotal', 'grandInterestTotal'));
    }

    private function calculateDailyInterest($interestType, $principal, $rate, $daysElapsed)
    {
        $dailyInterest = 0;

        if ($interestType == 'Fixed') {
            $dailyInterest = ($principal * $rate) / (365 * 100);
        } elseif ($interestType == 'AnnualCompounded') {
            $dailyInterest = ($principal * pow(1 + ($rate / 100), 1 / 365)) - $principal;
        } else {
            $quarterlyRate = $rate / 4 / 100; // Convert annual rate to quarterly rate
            $quarters = round($daysElapsed / 91); // Approximate number of quarters in the elapsed days
            $maturityAmount = $principal; // Start with the principal for compounding

            for ($i = 0; $i < $quarters; $i++) {
                $quarterlyInterest = $maturityAmount * $quarterlyRate;
                $dailyInterest += $quarterlyInterest;
                $maturityAmount += $quarterlyInterest;
            }
        }

        return $dailyInterest;
    }


    public function printFd($id)
    {
        $data['branch'] = BranchMaster::first();
        $data['fd'] = MemberFdScheme::where('id', $id)->with('memberAccount')->first();
        $data['wordamount']  = $this->numberToWords($data['fd']->principalAmount);
        $data['mwordamount']  = $this->numberToWords($data['fd']->maturityAmount);


        return view('transaction.fd.fdPrint')->with($data);
    }

    public function numberToWords($number)
    {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
