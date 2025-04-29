<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseClientMaster extends Model
{
    use HasFactory, SoftDeletes;

    public function purchaseInvoice() {
        return $this->hasMany(PurchaseInvoice::class, 'purchaseClient');
    }

    protected $fillable = [
        'name',
        'state',
        'district',
        'city',
        'address',
        'email',
        'phone',
        'faxNo',
        'gstNo',
        'status',
        'updatedBy',
    ];
    public $with = ['state', 'district'];

    public function state()
    {
        return $this->belongsTo('App\Models\StateMaster', 'state');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\DistrictMaster', 'district');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
