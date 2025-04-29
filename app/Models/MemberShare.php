<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberShare extends Model
{
    use HasFactory;

    public $with = ['login'];

    

    public function login()
    {
        return $this->belongsTo('App\Models\User', 'updatedBy');
    }
}
