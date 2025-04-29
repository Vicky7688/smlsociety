<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VillageMaster extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'name',
        'stateId',
        'districtId',
        'tehsilId',
        'postOfficeId',
        'status',
        'updatedBy',
    ];
    public $with = ['state', 'district', 'tehsil', 'post'];

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

    public function post()
    {
        return $this->belongsTo('App\Models\PostOfficeMaster', 'postOfficeId');
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
