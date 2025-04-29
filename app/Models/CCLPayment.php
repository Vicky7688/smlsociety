<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CCLPayment extends Model
{
    use HasFactory;

    protected $table = 'ccl_payments';
    protected $primaryKey = 'id';
}
