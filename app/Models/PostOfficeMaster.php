<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostOfficeMaster extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'name',
        'stateId',
        'districtId',
        'tehsilId',
        'status',
        'pincode',
        'updatedBy',
    ];
    public $with = ['state', 'district', 'tehsil'];

    public function district()
    {
        return $this->belongsTo('App\Models\DistrictMaster', 'districtId');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\StateMaster', 'stateId');
    }

    public function tehsil()
    {
        return $this->belongsTo('App\Models\TehsilMaster', 'tehsilId');
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
