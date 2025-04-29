<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanMaster extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'memberType',
        'loanType',
        'name',
        'processingFee',
        'loantypess',
        'interest',
        'loan_app_charges',
        'penaltyInterest',
        'emiDate',
        'insType',
        'years',
        'months',
        'days',
        'advancementDate',
        'recoveryDate',
        'status',
        'updatedBy',
        'ledger_master_id'
    ];


    public function getUpdatedAtAttribute($value)
    {
        return  date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d m y - h:i A', strtotime($value));
    }
}
