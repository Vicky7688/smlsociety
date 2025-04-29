<?php

namespace App\Http\Controllers\WebControllers\Transactions\Trading;

use App\Http\Controllers\Controller;
use App\Models\DepotMaster;
use App\Models\GeneralLedger;
use App\Models\GroupMaster;
use App\Models\ItemMaster;
use App\Models\ItemStock;
use App\Models\LedgerMaster;
use App\Models\PurchaseClientMaster;
use App\Models\PurchaseDetail;
use App\Models\PurchaseInvoice;
use App\Models\SaleClientMaster;
use App\Models\SaleInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index() {
        $saleInvoice = SaleInvoice::latest()->first();
        $saleClients = SaleClientMaster::orderBy('name','ASC')->get();
        $depots = DepotMaster::orderBy('depotName','ASC')->get();
        $groups = GroupMaster::where('id','<=','2')->get();
        $ledgers = LedgerMaster::where('id','<=','1')->get();
        return view('transaction.trading.sale', compact('saleInvoice','saleClients','depots','groups','ledgers'));
    }

    public function getItemList(Request $request){
        $itemCode = $request->itemCode;
        $data = '';
        if(empty($itemCode)){
            $data .='<li class="list-group-item item"></li>';
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }
        $itemData = ItemMaster::where('code','LIKE','%'.$itemCode.'%')->get();
        if(count($itemData) > 0){
            $data = '<ul class="list-group itemSearch" style="display:block; z-index:1;">';
            foreach($itemData as $row){
                $data .= '<li class="list-group item">'.$row->code.'</li>';
            }
            $data .= '</ul>';
        } else {
            $data .= '<li class="list-group-item item">No Data Found</li>';
        }
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }    

    public function getItemDetail(Request $request){
        $itemCode = $request->itemCode;
        if(!empty($itemCode)){
            $itemData = ItemMaster::where('code',$itemCode)->with('tax','stock')->get();
            $itemStock = ItemStock::where('itemCode',$itemCode)->sum('purchaseQuantity');
            return response()->json([
                'status'=>true,
                'data'=>$itemData,
                'itemStock'=>$itemStock
            ]);
        }
    }

    public function checkItem(Request $request){
        $itemCode = $request->itemCode;
        $item = ItemMaster::where('code',$itemCode)->first();
        if ($item) {
            return response()->json([
                'status'=>true,
                'message'=>'Item added successfully'
            ]);
        } else {
            return response()->json([
                'status'=>false,
                'message'=>'Item not found'
            ]);
        }
        
    }

    public function store(Request $request) {
        $rules = array(
            'invoiceDate' => 'required',
            'invoiceNo' => 'required',
            'purchaseClient' => 'required',
            'depot' => 'required',
            'type' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Please check all inputs'
            ]);
        }

        
        $items = json_decode($request->input('items'), true);
        if (empty($items)) {
            return response()->json([
                'status' => false,
                'message' => 'No Items Found'
            ]);
        }

        do {
            $serialNo = "purchase" . rand(1111111, 9999999);
        } while (GeneralLedger::where("serialNo", "=", $serialNo)->first() instanceof GeneralLedger);

        DB::beginTransaction();
        try {
            // Purchase Invoice Entry Code
            $purchaseInvoice = PurchaseInvoice::updateOrCreate(
                [
                    'id' => $request->invoiceId,
                ],
                [
                    'invoiceDate' => $request->invoiceDate,
                    'invoiceNo' => $request->invoiceNo,
                    'purchaseClient' => $request->purchaseClient,
                    'depot' => $request->depot,
                    'type' => $request->type,
                    'paymentType' => $request->paymentType,
                    'bank' => $request->bank,
                    'subTotal' => $request->subTotal,
                    'cess' => $request->cess,
                    'igst' => $request->igst,
                    'sgst' => $request->sgst,
                    'cgst' => $request->cgst,
                    'freight' => $request->freight,
                    'labour' => $request->labour,
                    'commission' => $request->commission,
                    'discount' => $request->discount,
                    'grandTotal' => $request->grandTotal,
                    'branchId' => session('branchId') ?: 1,
                    'sessionId' => session('sessionId') ?: 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            if (!$request->invoiceId) {
                $purchaseInvoice->update(['serialNo' => $serialNo]);
            }
            $invoiceId = $purchaseInvoice->id;

            // Delete Extra Entries From Purchase Details
            $itemIds = array_column($items, 'id');
            $deleteItem = PurchaseDetail::whereNotIn('id', $itemIds)->where('invoiceId', $invoiceId)->delete();


            // Purchase Detail Entry Code
            foreach ($items as $key => $item) {
                $purchaseItem = PurchaseDetail::updateOrCreate(
                    [
                        'id' => $item['id']
                    ],
                    [
                        'invoiceId' => $invoiceId,
                        'itemCode' => $item['code'],
                        'itemName' => $item['name'],
                        'itemUnit' => $item['unit'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subTotal' => $item['subTotal'],
                        'cess' => $item['cess'],
                        'igst' => $item['igst'],
                        'sgst' => $item['sgst'],
                        'cgst' => $item['cgst'],
                        'grandTotal' => $item['grandTotal'],
                        'branchId' => session('branchId') ?: 1,
                        'sessionId' => session('sessionId') ?: 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            

            // General Ledger Entry Code
            // Credit Grand Total Into Bank/Cash
            $crLedger = GeneralLedger::updateOrCreate(
                [
                    'serialNo' => $purchaseInvoice->serialNo,
                    'transactionType' => 'Cr',
                    'groupCode' => $request->paymentType,
                    'ledgerCode' => $request->bank
                ],
                [
                    'formName' => 'Purchase',
                    'referenceNo' => $invoiceId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $purchaseInvoice->invoiceDate,
                    'transactionAmount' => $purchaseInvoice->grandTotal,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit SubTotal From Purchase Account
            $drLedger = GeneralLedger::updateOrCreate(
                [
                    'serialNo' => $purchaseInvoice->serialNo,
                    'transactionType' => 'Dr',
                    'groupCode' => 'PURC001',
                    'ledgerCode' => 'PURC001'
                ],
                [
                    'formName' => 'Purchase',
                    'referenceNo' => $invoiceId,
                    'entryMode' => 'Manual',
                    'transactionDate' => $purchaseInvoice->invoiceDate,
                    'transactionAmount' => $purchaseInvoice->subTotal,
                    'branchId' => session('branchId') ? session('branchId') : 1,
                    'sessionId' => session('sessionId') ? session('sessionId') : 1,
                    'updatedBy' => $request->user()->id,
                ]
            );
            // Debit CESS
            if ($request->cess > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'DUTY001',
                        'ledgerCode' => 'CESS001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->cess,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Debit IGST
            if ($request->igst > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'DUTY001',
                        'ledgerCode' => 'IGST001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->igst,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Debit SGST
            if ($request->sgst > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'DUTY001',
                        'ledgerCode' => 'SGST001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->sgst,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Debit CGST
            if ($request->cgst > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'DUTY001',
                        'ledgerCode' => 'CGST001',
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->cgst,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Debit Freight
            if ($request->freight > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'FRET001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->freight,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Debit Labour
            if ($request->labour > 0) {
                $drLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Dr',
                        'groupCode' => 'EXPN001',
                        'ledgerCode' => 'LABR001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->labour,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Credit Commission
            if ($request->commission > 0) {
                $crLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Cr',
                        'groupCode' => 'INCM001',
                        'ledgerCode' => 'COMM001',
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->commission,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }
            // Credit Discount
            if ($request->discount > 0) {
                $crLedger = GeneralLedger::updateOrCreate(
                    [
                        'serialNo' => $purchaseInvoice->serialNo,
                        'transactionType' => 'Cr',
                        'groupCode' => 'INCM001',
                        'ledgerCode' => 'DISC001'
                    ],
                    [
                        'formName' => 'Purchase',
                        'referenceNo' => $invoiceId,
                        'entryMode' => 'Manual',
                        'transactionDate' => $purchaseInvoice->invoiceDate,
                        'transactionAmount' => $purchaseInvoice->discount,
                        'branchId' => session('branchId') ? session('branchId') : 1,
                        'sessionId' => session('sessionId') ? session('sessionId') : 1,
                        'updatedBy' => $request->user()->id,
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Details Inserted Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Transaction Failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function view($viewId) {
        $purchaseInvoice = PurchaseInvoice::with('purchaseDetail')->findOrFail($viewId);
        return response()->json([
            'status' => true,
            'data' => $purchaseInvoice
        ]);
    }
}
