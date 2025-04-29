<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepotMaster extends Model
{
    use HasFactory, SoftDeletes;

    public function purchaseInvoice() {
        return $this->hasMany(PurchaseInvoice::class, 'depot');
    }

    protected $fillable = [
        'depotName',
        'salesmanName',
        'phone',
        'address',
        'status',
        'updatedBy'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
