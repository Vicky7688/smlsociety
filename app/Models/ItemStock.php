<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referenceNo',
        'itemCode',
        'itemName',
        'itemUnit',
        'purchaseDate',
        'purchaseQuantity',
        'purchasePrice',
        'branchId',
        'sessionId',
        'updatedBy',
    ];

    public function item() {
        return $this->belongsTo(ItemMaster::class,'code','itemCode');
    }
}
