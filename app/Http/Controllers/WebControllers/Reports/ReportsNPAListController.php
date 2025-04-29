<?php
namespace App\Http\Controllers\WebControllers\Reports;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberLoan;
use Carbon\Carbon;

class ReportsNPAListController extends Controller
{
    public function index(){
        return view('report.npa');
    }
    
    public function getData(Request $request)
    {
        $date = Carbon::parse($request->startDate);
        $NpaLists = MemberLoan::select('*')
                ->selectRaw("TIMESTAMPDIFF(YEAR, loanEndDate, ?) AS yearDifference", [$date->toDateString()])
                ->where('is_delete', 'No')
                ->where('loanEndDate', '<=', $date->toDateString())
                ->get();
        $npaDetails = [];
        foreach($NpaLists as $loan){
            $loanAmount = $loan->loanAmount;
            $receivedAmount = $loan->reciveamount;
            $balanceAmount = $loanAmount - $receivedAmount;
            if($balanceAmount > 0){
                $loanEndDate = Carbon::parse($loan->loanEndDate);
                $daysDue = $date->diffInDays($loanEndDate, false); // negative if past due
                // Calculate NPA based on days past due
                if($daysDue < 0){
                    // Assuming 'daysDue' is the number of days the payment is overdue
                    // and 'loanInterestRate' is the annual interest rate
                    $loanInterestRate = $loan->loanInterest / 100; // Convert percentage to decimal
                    $yearsOverdue = abs($daysDue) / 365;
                    $interestDue = $balanceAmount * $loanInterestRate * $yearsOverdue;
                    $totalDue = $balanceAmount + $interestDue;
                    $npaPercentage = ($totalDue / $loanAmount) * 100;
                    //dd($daysDue,$loanInterestRate, $yearsOverdue , $interestDue, $totalDue , $npaPercentage );
                    // Add to npaDetails array
                    $npaDetails[] = [
                        'loan_id' => $loan->id,
                        'npa_percentage' => $npaPercentage,
                        'total_due' => $totalDue,
                        'days_overdue' => abs($daysDue),
                    ];
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'npalist' => $NpaLists,
            'npaDetails' => $npaDetails
        ]);
    }
}