<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;
    protected $fillable = [
        'accountNo',
        'memberType',
        'groupId',
        'ledgerId',
        'transactionDate',
        'transactionType',
        'depositAmount',
        'withdrawAmount',
        'dividendAmount',
        'chequeNo',
        'narration',
        'branchId',
        'agentId',
        'sessionId',
        'updatedBy',
    ];
}
