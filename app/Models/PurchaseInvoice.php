<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serialNo',
        'invoiceDate',
        'invoiceNo',
        'purchaseClient',
        'depot',
        'type',
        'paymentType',
        'bank',
        'subTotal',
        'cess',
        'igst',
        'sgst',
        'cgst',
        'freight',
        'labour',
        'commission',
        'discount',
        'grandTotal',
        'branchId',
        'sessionId',
        'updatedBy',
    ];

    public function purchaseDetail() {
        return $this->hasMany(PurchaseDetail::class, 'invoiceId');
    }

    public function client() {
        return $this->belongsTo(PurchaseClientMaster::class, 'purchaseClient');
    }

    public function purchaseDepot() {
        return $this->belongsTo(DepotMaster::class, 'depot');
    }

    public function paymentTypee() {
        return $this->belongsTo(GroupMaster::class,'paymentType','groupCode');
    }
}
