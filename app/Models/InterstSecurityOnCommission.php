<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterstSecurityOnCommission extends Model
{
    use HasFactory;

    protected $table = 'securityoncomm_interest_calculations';
    protected $primeryKey = 'id';
}
