<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoiceId',
        'itemCode',
        'itemName',
        'itemUnit',
        'quantity',
        'price',
        'subTotal',
        'cess',
        'igst',
        'sgst',
        'cgst',
        'grandTotal',
        'branchId',
        'sessionId',
        'updatedBy',
    ];

    public function purchaseInvoice() {
        return $this->belongsTo(PurchaseInvoice::class, 'invoiceId');
    }
}
