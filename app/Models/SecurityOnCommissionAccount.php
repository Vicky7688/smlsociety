<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityOnCommissionAccount extends Model
{
    use HasFactory;
    protected $table = 'security_on_commission_account';
    protected $primaryKey = 'id';
}
