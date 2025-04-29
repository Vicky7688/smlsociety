<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MemberAccount;

class Mis extends Model
{
    use HasFactory;
    use SoftDeletes;
     
    protected $fillable = [
        'serialNo',
        'date',
        'member_type',
        'account_no',
        'mis_ac_no',
        'amount',
        'interest',
        'period_year',
        'period_month',
        'TotalInterest',
        'monthly_interest',
        'maturity_date',
        'maturity_amount',
        'payment_type',
        'groupCode',
        'ledgerCode',
        'rd_interestROI',
        'rd_interest',
        'interest_deposite',
        'SavingRdAccountNumber',
        'status',
    ];

    public function memberAccount()
    {
        return $this->belongsTo(MemberAccount::class, 'account_no', 'accountNo');
    }
}