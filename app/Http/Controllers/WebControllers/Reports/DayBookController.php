<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BranchMaster;
use App\Models\GeneralLedger;
use App\Models\LedgerMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DayBookController extends Controller
{
    public function daybookindex(){
        $branch = BranchMaster::first();
        $data['branch'] = $branch;
        // dd($branch);
        return view('report.dayBook',$data);
    }

    public function getdaybookdata(Request $post){
        $rules = [
            "startDate" => "required",
            "endDate" => "required"
        ];

        $validator = Validator::make($post->all(),$rules);
        if($validator->fails()){
            return response()->json(['status' => 'Fail','messages' => 'Check all Inputs']);
        }

        $startDate = date('Y-m-d', strtotime($post->startDate));
        $endDate = date('Y-m-d', strtotime($post->endDate));

        //__________________Get Cash Details
        $previous_amount = LedgerMaster::where('groupCode', 'C002')->value('openingAmount');
        $preyearDebit = DB::table('general_ledgers')
            ->where('transactionType', '=', 'Dr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete', 'No')
            ->whereDate('transactionDate', '<', $startDate)
            ->sum('transactionAmount');

        $preyearCredit = DB::table('general_ledgers')
            ->where('transactionType', '=', 'Cr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete', 'No')
            ->whereDate('transactionDate', '<', $startDate)
            ->sum('transactionAmount');

        $currentyearDebit = GeneralLedger::where('transactionType', '=', 'Dr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete','=','No')
            ->whereDate('transactionDate', '>=', $startDate)
            ->whereDate('transactionDate', '<=', $endDate)
            ->sum('transactionAmount');

        $currentyearCredit = GeneralLedger::where('transactionType', '=', 'Cr')
            ->where('ledgerCode', 'C002')
            ->where('groupCode', 'C002')
            ->where('general_ledgers.is_delete','=','No')
            ->whereDate('transactionDate', '>=', $startDate)
            ->whereDate('transactionDate', '<=', $endDate)
            ->sum('transactionAmount');

        $openingCash = $previous_amount + $preyearDebit - $preyearCredit;
        $closingCash = $openingCash + $currentyearDebit - $currentyearCredit;


        //____________Get All Details Without Cash
        $debitbalance = DB::table('general_ledgers')
            ->select(
                'general_ledgers.transactionDate',
                'general_ledgers.accountNo',
                'general_ledgers.ledgerCode',
                'general_ledgers.transactionType',
                'ledger_masters.ledgerCode as masterLedgerCode',
                'ledger_masters.name as ledgerName',
                'ledger_masters.id as ledgerId',
                'member_accounts.accountNo as memnumber',
                'member_accounts.memberType as mtype',
                'member_accounts.name as memberName',
                'general_ledgers.formName',
                'general_ledgers.transactionAmount',
                'general_ledgers.id as glid'
            )
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
            ->leftJoin('member_accounts', function ($join) {
                $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
                    ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
            })
            ->where('general_ledgers.ledgerCode', '!=', 'C002')
            ->where('general_ledgers.transactionType', '=', 'Dr')
            ->where('general_ledgers.is_delete', '=', 'No')
            ->whereDate('general_ledgers.transactionDate', '>=', $startDate)
            ->whereDate('general_ledgers.transactionDate', '<=', $endDate)
            ->groupBy(
                'ledger_masters.ledgerCode', 'ledger_masters.name', 'ledger_masters.id', 'general_ledgers.accountNo',
                'general_ledgers.transactionDate','general_ledgers.ledgerCode',  'general_ledgers.transactionType',
                'member_accounts.accountNo',
                'member_accounts.memberType',
                'member_accounts.name',
                'general_ledgers.formName',
                'general_ledgers.transactionAmount',
                'general_ledgers.id'
                )
            // ->orderBy('general_ledgers.transactionDate', 'ASC')
            ->get();



        $creditbalance = DB::table('general_ledgers')
            ->select(
                'general_ledgers.transactionDate',
                'general_ledgers.accountNo',
                'general_ledgers.ledgerCode',
                'general_ledgers.transactionType',
                'ledger_masters.ledgerCode as masterLedgerCode',
                'ledger_masters.name as ledgerName',
                'ledger_masters.id as ledgerId',
                'member_accounts.accountNo as memnumber',
                'member_accounts.memberType as mtype',
                'member_accounts.name as memberName',
                'general_ledgers.formName',
                'general_ledgers.transactionAmount',
                'general_ledgers.id as glid',
                 'general_ledgers.narration'
            )
            ->leftJoin('ledger_masters', 'ledger_masters.ledgerCode', '=', 'general_ledgers.ledgerCode')
            ->leftJoin('member_accounts', function ($join) {
                $join->on('general_ledgers.accountNo', '=', 'member_accounts.accountNo')
                    ->on('general_ledgers.memberType', '=', 'member_accounts.memberType');
            })
            ->where('general_ledgers.ledgerCode', '!=', 'C002')
            ->where('general_ledgers.transactionType', '=', 'Cr')
            ->where('general_ledgers.is_delete', '=', 'No')
            ->whereDate('general_ledgers.transactionDate', '>=', $startDate)
            ->whereDate('general_ledgers.transactionDate', '<=', $endDate)
            ->groupBy(
                'ledger_masters.ledgerCode', 'ledger_masters.name', 'ledger_masters.id', 'general_ledgers.accountNo',
                'general_ledgers.transactionDate','general_ledgers.ledgerCode',  'general_ledgers.transactionType',
                'member_accounts.accountNo',
                'member_accounts.memberType',
                'member_accounts.name',
                'general_ledgers.formName',
                'general_ledgers.transactionAmount',
                'general_ledgers.id',
                'general_ledgers.narration'
                )
            // ->orderBy('general_ledgers.transactionDate', 'ASC')
            ->get();


        if(!empty($openingCash) || !empty($closingCash) || !empty($debitbalance) || !empty($creditbalance)){
            return response()->json([
                'status' => 'success',
                'openingcash' => $openingCash,
                'closingcash' => $closingCash,
                'debitbalance' => $debitbalance,
                'creditbalance' => $creditbalance
            ]);
        }else{
            return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
        }
    }


    // public function index()
    // {
    //     return view('report.dayBook');
    // }

    // public function getlist(Request $request)
    // {
    //     $start_date = date('Y-m-d', strtotime($request->startdate));
    //     $end_date = date('Y-m-d', strtotime($request->enddate));

    //     $opening = LedgerMaster::where(['ledgerCode' => 'C002'])->first();
    //     $ledgerid = $opening->ledgerCode;
    //     $ledgeramount = $opening->openingAmount;

    //     $data1 = GeneralLedger::where([
    //         'transactionType' => 'Cr',
    //     ])
    //         ->with('group')
    //         ->with('ledger')
    //         ->with('account')
    //         ->where('ledgerCode', '!=', $ledgerid)
    //         ->whereBetween('transactionDate', [$start_date, $end_date])
    //         ->where('is_delete', 'No')
    //         ->where('transactionAmount', '>', 0)
    //         ->orderBy('groupCode', 'asc')
    //         ->get();


    //     $data1 = $data1->map(function ($item) {
    //         $name = DB::table('member_accounts')->where('accountNo', $item->accountNo)->value('name');
    //         $item->name = $name;

    //         return $item;
    //     });

    //     $data2 = GeneralLedger::where('transactionType', 'Dr')
    //         ->with('group')
    //         ->with('ledger')
    //         ->with('account')
    //         ->where('ledgerCode', '!=', $ledgerid)
    //         ->whereBetween('transactionDate', [$start_date, $end_date])
    //         ->where('transactionAmount', '>', 0)
    //         ->where('is_delete', 'No')
    //         ->orderBy('groupCode', 'asc')
    //         ->get();

    //     $data2 = $data2->map(function ($item) {
    //         $name = DB::table('member_accounts')->where('accountNo', $item->accountNo)->value('name');
    //         $item->name = $name;
    //         return $item;
    //     });
    //     $getDrtotal = GeneralLedger::where('ledgerCode', $ledgerid)
    //         ->where('transactionType', 'Dr')
    //         ->whereDate('transactionDate', '<=', $end_date)
    //         ->where('is_delete', 'No')
    //         ->sum('transactionAmount');

    //     $getCrtotal = GeneralLedger::where('ledgerCode', $ledgerid)
    //         ->where('transactionType', 'Cr')
    //         ->whereDate('transactionDate', '<=', $end_date)
    //         ->where('is_delete', 'No')
    //         ->sum('transactionAmount');

    //     $closing_cash = $ledgeramount + $getDrtotal - $getCrtotal;

    //     $openingdr = GeneralLedger::where('ledgerCode', $ledgerid)
    //         ->where('transactionType', 'Dr')
    //         ->whereDate('transactionDate', '<', $start_date)
    //         ->where('is_delete', 'No')
    //         ->sum('transactionAmount');

    //     $openingCr = GeneralLedger::where('ledgerCode', $ledgerid)
    //         ->where('transactionType', 'Cr')
    //         ->whereDate('transactionDate', '<', $start_date)
    //         ->where('is_delete', 'No')
    //         ->sum('transactionAmount');
    //     $opening_cash = $ledgeramount + $openingdr - $openingCr;
    //     return response()->json(['status' => 'success', 'debetdata' => $data1, 'credetdata' => $data2, 'closingcash' => $closing_cash, 'opening_cash' => $opening_cash]);
    // }
























    public function print(Request $request)
    {
        if ($request->session()->has('dayBookData')) {
            $dayBookData = $request->session()->get('dayBookData');

            $openingAmount = $dayBookData['openingAmount'];
            $closingAmount = $dayBookData['closingAmount'];
            $debitEntries = $dayBookData['debitEntries'];
            $creditEntries = $dayBookData['creditEntries'];
            $closingCash = $dayBookData['closingCash'];

            // Fetch receipt entries
            $receiptEntries = GeneralLedger::where('transactionType', 'Cr')
                ->with('group')
                ->with('ledger')
                ->with('account')
                ->where('ledgerCode', '!=', 'C002')
                ->whereBetween('transactionDate', [$request->startdate, $request->enddate])
                ->where('is_delete', 'No')
                ->get();

            // Fetch payment entries
            $paymentEntries = GeneralLedger::where('transactionType', 'Dr')
                ->with('group')
                ->with('ledger')
                ->with('account')
                ->where('ledgerCode', '!=', 'C002')
                ->whereBetween('transactionDate', [$request->startdate, $request->enddate])
                ->where('is_delete', 'No')
                ->get();

            // Fetch opening cash separately
            $openingCash = $dayBookData['openingCash'];

            return view('report.dayBookPrint', compact('openingAmount', 'closingAmount', 'debitEntries', 'creditEntries', 'openingCash', 'closingCash', 'receiptEntries', 'paymentEntries'));
        } else {
            return view('report.emptyPrintView');
        }
    }


























    public function printPdf(Request $request)
    {

        $branch = BranchMaster::first();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $opening = LedgerMaster::where(['ledgerCode' => 'C002'])->first();
        $ledgerid = $opening->ledgerCode;
        $ledgeramount = $opening->openingAmount;

        $data1 = GeneralLedger::where([
            'transactionType' => 'Cr',
        ])
            ->with('group')
            ->with('ledger')
            ->with('account')
            ->where('ledgerCode', '!=', $ledgerid)
            ->whereBetween('transactionDate', [$start_date, $end_date])
            ->where('is_delete', 'No')
            ->where('transactionAmount', '>', 0)
            ->orderBy('groupCode', 'asc')
            ->get();

        $data2 = GeneralLedger::where('transactionType', 'Dr')
            ->with('group')
            ->with('ledger')
            ->with('account')
            ->where('ledgerCode', '!=', $ledgerid)
            ->whereBetween('transactionDate', [$start_date, $end_date])
            ->where('transactionAmount', '>', 0)
            ->where('is_delete', 'No')
            ->orderBy('groupCode', 'asc')
            ->get();

        $getDrtotal = GeneralLedger::where('ledgerCode', $ledgerid)
            ->where('transactionType', 'Dr')
            ->whereDate('transactionDate', '<=', $end_date)
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $getCrtotal = GeneralLedger::where('ledgerCode', $ledgerid)
            ->where('transactionType', 'Cr')
            ->whereDate('transactionDate', '<=', $end_date)
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $closing_cash = $ledgeramount + $getDrtotal - $getCrtotal;

        $openingdr = GeneralLedger::where('ledgerCode', $ledgerid)
            ->where('transactionType', 'Dr')
            ->whereDate('transactionDate', '<', $start_date)
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $openingCr = GeneralLedger::where('ledgerCode', $ledgerid)
            ->where('transactionType', 'Cr')
            ->whereDate('transactionDate', '<', $start_date)
            ->where('is_delete', 'No')
            ->sum('transactionAmount');

        $opening_cash = $ledgeramount + $openingdr - $openingCr;

        require_once base_path('vendor/tcpdf/tcpdf.php');
        require_once 'vendor/tcpdf/config/tcpdf_config.php';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Startech Co-Operative Society Ltd.');
        $pdf->SetTitle('Day Book');
        $pdf->SetKeywords('');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(5, 5, 5);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set font
        $pdf->SetFont('helvetica', '', 9);

        // add a page
        $pdf->AddPage('L');


        $html = '
        <div style="text-align:center;">

        			<span style="font-size:26px;"><b>' . $branch->name . '</b></span><br/>
        			<span style="font-size:18px;">' . $branch->address . '</span><br/>
        			<span style="font-size:14px;">Day Book Report ' . date('d-m-Y', strtotime($request->start_date)) . ' to ' . date('d-m-Y', strtotime($request->end_date)) . '</span>

        		</div>';

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        $html = '
              <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }

                    .table-bordered > th{
                        border:1px solid #100101;
                    }
                    .table-bordered > td{
                        border:1px solid #100101;
                    }
              </style>

            <table border="0" cellspacing="5" cellpadding="2" width="100%">
             	<tr>
     				<td align="center" bgcolor="#cccccc" style="font-size:14px;background:#cccccc;"><strong>RECEIPT</strong></td>
     				<td align="center" style="font-size:14px;background-color:#cccccc;"><strong>PAYMENT</strong></td>
     			</tr>

     			<tr>
     			<td>
     			<table border="0" cellspacing="0" cellpadding="2" width="100%" class="table-bordered">
         			   <tr>
     							<th width="12%"><b>Date</b></th>
     							<th width="8%"><b>VNo.</b></th>
     							<th width="10%"><b>Acc.No.</b></th>
     							<th width="40%"><b>Name</b></th>
     							<th align="right" width="15%"><b>Amount</b></th>
     							<th align="right" width="15%"><b>Total</b></th>
     					</tr>
     					<tr>

     							<td colspan="4" align="center"><strong>Opening Cash</strong></td>
     							<td> </td>
     							<td align="right">' . $opening_cash . '</td>
     				   </tr>';
        $creditdata = 0;
        $ledgerName = '';
        $total = 0;
        $crgrandTotal = 0;
        foreach ($data1 as $key => $creditData) {
            $crgrandTotal += $creditData->transactionAmount;
            if (isset($creditData->account)) {
                $name = $creditData->account->name;
            } else {
                $name = $creditData->narration ?? "";
            }
            if ($name == '' || $name == null) {
                $name = $creditData->ledger->name;
            }
            if ($ledgerName != $creditData->ledger->name) {
                $ledgerName = $creditData->ledger->name;
                $html .= '<tr> <td></td> <td></td> <td></td> <td align="center"><strong>' . $creditData->ledger->name . '</strong></td> <td></td> <td></td></tr>';
                $total  = $creditData->transactionAmount;
            } else {
                $total  += $creditData->transactionAmount;
            }

            $html .= '

                            <tr>
     						    <td colspan="" > ' . date('d-m-y', strtotime($creditData->transactionDate)) . '</td>
     						   	<td colspan="" > ' . $creditData->id . '</td>
     						   	<td colspan="" > ' . $creditData->accountNo . '</td>
     							<td colspan="" > ' . $name . '</td>

     							<td align="right">' . $creditData->transactionAmount . '</td>';
            if (!isset($data1[$key + 1]->ledger->name) || $creditData->ledger->name != $data1[$key + 1]->ledger->name) {
                $html .= '<td align="right">' . $total . '</td>';
            } else {
                $html .= '<td align="right"> </td>';
            }

            $html .= ' </tr>';
        }
        if (count($data1) < count($data2)) {
            for ($i = count($data1); $i <= count($data2); $i++) {
                $html .= '<tr>
     							<td  align="center" colspan="6" >Total</td>

     				         </tr>';
            }
        }
        $html .= '<tr>
     							<td  align="center" colspan="5" >Total</td>
     							<td align="right">' . $crgrandTotal . '</td>
     				         </tr>';
        $html .= '</table>
     			</td>
     			<td>
     			 <table border="0" cellspacing="0" cellpadding="2" width="100%" class="table-bordered">
         			   <tr>
     							<th width="12%"><b>Date</b></th>
     							<th width="10%"><b>VNo.</b></th>
     							<th width="10%"><b>Acc.No.</b></th>
     							<th width="40%"><b>Name</b></th>
     							<th align="right" width="15%"><b>Amount</b></th>
     							<th align="right" width="15%"><b>Total</b></th>
     					</tr>	<tr>
     							<td colspan="6" align="center"></td>

     				   </tr>';

        $creditdata = 0;
        $ledgerName = '';
        $total = 0;
        $crgrandTotal = 0;
        foreach ($data2 as $key => $creditData) {
            $crgrandTotal += $creditData->transactionAmount;
            if (isset($creditData->account)) {
                $name = $creditData->account->name;
            } else {
                $name = $creditData->narration ?? "";
            }
            if ($name == '' || $name == null) {
                $name = $creditData->ledger->name;
            }
            if ($ledgerName != $creditData->ledger->name) {
                $ledgerName = $creditData->ledger->name;
                $html .= '<tr> <td></td> <td></td> <td></td> <td align="center"><strong>' . $creditData->ledger->name . '</strong></td> <td></td> <td></td></tr>';
                $total  = $creditData->transactionAmount;
            } else {
                $total  += $creditData->transactionAmount;
            }

            $html .= '

                            <tr>
     						    <td colspan="" > ' . date('d-m-y', strtotime($creditData->transactionDate)) . '</td>
     						   	<td colspan="" > ' . $creditData->id . '</td>
     						   	<td colspan="" > ' . $creditData->accountNo . '</td>
     							<td colspan="" > ' . $name . '</td>
     							<td align="right">' . $creditData->transactionAmount . '</td>';
            if (!isset($data1[$key + 1]->ledger->name) || $creditData->ledger->name != $data1[$key + 1]->ledger->name) {
                $html .= '<td align="right">' . $total . '</td>';
            } else {
                $html .= '<td align="right"> </td>';
            }

            $html .= ' </tr>';
        }
        if (count($data2) < count($data1)) {
            for ($i = count($data2); $i < count($data1); $i++) {
                $html .= '<tr>
                 							<td  align="center" colspan="6" ></td>
                 				         </tr>';
            }
        }
        $html .= '	<tr>
     							<td colspan="5" align="center"><strong>Closing Cash</strong></td>
     							<td align="right">' . $opening_cash . '</td>
     				   </tr>

     			       <tr>
     							<td  align="center" colspan="5" >Total</td>
     							<td align="right">' . $closing_cash . '</td>
     				         </tr>';
        $html .= '</table>
     			</td>
     			</tr>

            </table>';


        // $pdf->WriteHTML($html , true, false, true, false, '');
        //d($html) ;

        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        $pdf->Output('daybook-print.pdf', 'I');
    }
}
