<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRecovery extends Model
{
    use HasFactory;
    protected $fillable = [
        'serialNo',
        'loanId', 'receiptDate', 'principal', 'interest', 'pendingInterest',
        'penalInterest', 'total', 'receivedAmount', 'receivedBy', 'status',
        'entry_mode', 'is_delete', 'deleted_date', 'Branch',
        'updatedBy', 'sessionId', 'branchId', 'total','instaId',
    ];
}
