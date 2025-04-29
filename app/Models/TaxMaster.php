<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxMaster extends Model
{
    use HasFactory; use SoftDeletes;

    protected $fillable =[
        'name',
        'cess',
        'igst',
        'cgst',
        'sgst',
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

    public function itemMaster() {
        return $this->hasMany(ItemMaster::class, 'tax');
    }
}
