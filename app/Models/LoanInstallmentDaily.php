<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInstallmentDaily extends Model
{
    use HasFactory;
    protected $table = "loan_installments_daily";
    protected $fillable = ['status', 'paid_date'];
}
