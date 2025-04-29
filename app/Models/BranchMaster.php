<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BranchMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'registrationNo',
        'registrationDate',
        'stateId',
        'districtId',
        'tehsilId',
        'postOfficeId',
        'villageId',
        'wardNo',
        'address',
        'pincode',
        'phone',
        'updatedBy'
    ];

    public $with = ['state', 'district', 'tehsil', 'post', 'village'];

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

    public function village()
    {
        return $this->belongsTo('App\Models\VillageMaster', 'villageId');
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