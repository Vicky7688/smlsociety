<?php

namespace App\Http\Controllers\WebControllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\SessionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FinancialYearEndcontroller extends Controller
{
    // public function rdpayableinsert(Request $post){
    //     $rdinterestPayable = $post->grandInterestTotal;
    //     $transactionDate = date('Y-m-d', strtotime($post->endDatesssss));
    //     $session_master = SessionMaster::find(Session::get('sessionId'));

    //     if ($session_master->auditPerformed === 'Yes') {
    //         return response()->json(['status' => 'fail','messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //     }

    //     if (!$this->isDateBetween($transactionDate)) {
    //         return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
    //     }


    //     $rdtype = DB::table('financial_year_end')->where('name', 'RD')->get();

    //     foreach ($rdtype as $row) {
    //         if ($session_master->id === $row->sessionId  &&  $row->sch_id === $post->schemeType && $row->auditPerformed === 'Yes') {
    //             return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //         }
    //     }




    //     $sorno = $session_master->sortno;

    //     if($rdinterestPayable > 0){
    //         try {
    //             DB::transaction(function () use ($session_master, $post, $transactionDate, $rdinterestPayable, $sorno) {
    //                 DB::table('financial_year_end')->updateOrInsert(
    //                     [
    //                         'sessionId' => $session_master->id,
    //                         'sch_id' => $post->schemeType,
    //                         'name' => 'RD',
    //                         'memberType' => $post->memberType,
    //                         'sortno' => $sorno,
    //                     ],
    //                     [
    //                         'amount' => $rdinterestPayable > 0 ? round($rdinterestPayable, 2) : 0,
    //                         'entry_date' => $transactionDate,
    //                         'auditPerformed' => 'No',
    //                         'sortno' => $sorno,
    //                     ]
    //                 );
    //             });

    //             return response()->json([
    //                 'status' => 'success',
    //                 'messages' => 'RD Interest Payable Inserted/Updated Successfully'
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 'fail',
    //                 'error' => $e->getMessage(),
    //                 'line' => $e->getLine()
    //             ]);
    //         }
    //     }
    // }

    // public function fdpayableinsert(Request $post){
    //     //__________Check Financial Year Audit && Financial Year Entries
    //     $transactionDate = date('Y-m-d', strtotime($post->endDate));
    //     $session_master = SessionMaster::find(Session::get('sessionId'));

    //     if ($session_master->auditPerformed === 'Yes') {
    //         return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //     }

    //     $result = $this->isDateBetween(date('Y-m-d', strtotime($transactionDate)));

    //     if (!$result) {
    //         return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
    //     }


    //     $fdtype = DB::table('financial_year_end')->where('name', 'FD')->get();

    //     foreach ($fdtype as $row) {
    //         if ($session_master->id === $row->sessionId  &&  $row->sch_id === $post->schemeType && $row->auditPerformed === 'Yes') {
    //             return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //         }
    //     }

    //     $sorno = $session_master->sortno;
    //     $depositType = $post->depositType;
    //     $fd_payable_amount = $post->grandInterestTotal;

    //     if($fd_payable_amount > 0){
    //         try {
    //             DB::transaction(function () use ($session_master, $post, $transactionDate, $fd_payable_amount, $sorno,$depositType) {
    //                 DB::table('financial_year_end')->updateOrInsert(
    //                     [
    //                         'sessionId' => $session_master->id,
    //                         'sch_id' => $post->schemeType,
    //                         'name' => 'FD',
    //                         'memberType' => $post->memberType,
    //                         'sortno' => $sorno,
    //                         'depositType' => $depositType
    //                     ],
    //                     [
    //                         'amount' => $fd_payable_amount > 0 ? round($fd_payable_amount, 2) : 0,
    //                         'entry_date' => $transactionDate,
    //                         'auditPerformed' => 'No',
    //                         'sortno' => $sorno,
    //                         'depositType' => $depositType

    //                     ]
    //                 );
    //             });

    //             return response()->json([
    //                 'status' => 'success',
    //                 'messages' => 'FD Interest Payable Inserted/Updated Successfully'
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 'fail',
    //                 'error' => $e->getMessage(),
    //                 'line' => $e->getLine()
    //             ]);
    //         }
    //     }
    // }


    // public function dailypayableinsert(Request $post){

    //     $transactionDate = date('Y-m-d', strtotime($post->dates));
    //     $session_master = SessionMaster::find(Session::get('sessionId'));

    //     if ($session_master->auditPerformed === 'Yes') {
    //         return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //     }

    //     $result = $this->isDateBetween($transactionDate);

    //     if (!$result) {
    //         return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
    //     }


    //     $ddsType = DB::table('financial_year_end')->where('name', 'DDS')->get();

    //     foreach ($ddsType as $row) {
    //         if ($session_master->id === $row->sessionId  &&  $row->sch_id === $post->schemeType && $row->auditPerformed === 'Yes') {
    //             return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //         }
    //     }


    //     $InttPayableTotal = $post->InttPayableTotal;
    //     $sorno = $session_master->sortno;
    //     // $depositType = $post->depositType;


    //     if($InttPayableTotal > 0){
    //         try {
    //             DB::transaction(function () use ($session_master, $post, $transactionDate, $InttPayableTotal, $sorno) {
    //                 DB::table('financial_year_end')->updateOrInsert(
    //                     [
    //                         'sessionId' => $session_master->id,
    //                         'sch_id' => $post->schemeType,
    //                         'name' => 'DDS',
    //                         'memberType' => $post->memberType,
    //                         'sortno' => $sorno,
    //                         // 'depositType' => $depositType
    //                     ],
    //                     [
    //                         'amount' => $InttPayableTotal > 0 ? round($InttPayableTotal, 2) : 0,
    //                         'entry_date' => $transactionDate,
    //                         'auditPerformed' => 'No',
    //                         'sortno' => $sorno,
    //                         // 'depositType' => $depositType

    //                     ]
    //                 );
    //             });

    //             return response()->json([
    //                 'status' => 'success',
    //                 'messages' => 'DDS Interest Payable Inserted/Updated Successfully'
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 'fail',
    //                 'error' => $e->getMessage(),
    //                 'line' => $e->getLine()
    //             ]);
    //         }
    //     }




    //     return response()->json(['status' => 'success', 'messages' => 'Daily Deposit Interest Payable Updated/Inserted Successfully']);
    // }

    // public function loaninterestrecoverable(Request $post){

    //     $transactionDate = date('Y-m-d', strtotime($post->endDate));
    //     $session_master = SessionMaster::find(Session::get('sessionId'));

    //     if ($session_master->auditPerformed === 'Yes') {
    //         return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //     }

    //     $result = $this->isDateBetween($transactionDate);

    //     if (!$result) {
    //         return response()->json(['status' => 'fail', 'messages' => "Please Check your session"]);
    //     }


    //     $ddsType = DB::table('financial_year_end')->where('name', 'MTLoan Recoverable')->get();

    //     foreach ($ddsType as $row) {
    //         if ($session_master->id === $row->sessionId  &&  $row->sch_id === $post->schemeType && $row->auditPerformed === 'Yes') {
    //             return response()->json(['status' => 'Fail', 'messages' => 'This Session Audit Has Done, You Are Not Allowed Anything Performed']);
    //         }
    //     }


    //     $loaninterestrecoverable = $post->loaninterestrecoverable;
    //     $sorno = $session_master->sortno;
    //     // $depositType = $post->depositType;


    //     if($loaninterestrecoverable > 0){
    //         try {
    //             DB::transaction(function () use ($session_master, $post, $transactionDate, $loaninterestrecoverable, $sorno) {
    //                 DB::table('financial_year_end')->updateOrInsert(
    //                     [
    //                         'sessionId' => $session_master->id,
    //                         'sch_id' => $post->schemeType,
    //                         'name' => 'MTLoan Recoverable',
    //                         'memberType' => $post->memberType,
    //                         'sortno' => $sorno,
    //                         // 'depositType' => $depositType
    //                     ],
    //                     [
    //                         'amount' => $loaninterestrecoverable > 0 ? round($loaninterestrecoverable, 2) : 0,
    //                         'entry_date' => $transactionDate,
    //                         'auditPerformed' => 'No',
    //                         'sortno' => $sorno,
    //                         // 'depositType' => $depositType

    //                     ]
    //                 );
    //             });

    //             return response()->json([
    //                 'status' => 'success',
    //                 'messages' => 'DDS Interest Payable Inserted/Updated Successfully'
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 'fail',
    //                 'error' => $e->getMessage(),
    //                 'line' => $e->getLine()
    //             ]);
    //         }
    //     }


    // }
}
