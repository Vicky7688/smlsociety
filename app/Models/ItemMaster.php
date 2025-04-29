<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMaster extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'type',
        'unit',
        'purchaseRate',
        'saleRate',
        'taxId',
        'purchaseTax',
        'saleTax',
        'openingStock',
        'reorderLevel',
        'status',
        'updatedBy',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function tax() {
        return $this->belongsTo(TaxMaster::class, 'taxId');
    }

    public function stock() {
        return $this->hasMany(ItemStock::class,'itemCode','code');
    }
}
