<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberSaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'accountId',
        'accountNo',
        'memberType',
        'groupId',
        'ledgerId',
        'savingAccountNo',
        'transactionDate',
        'transactionType',
        'depositAmount',
        'withdrawAmount',
        'chequeNo',
        'narration',
        'branchId',
        'agentId',
        'sessionId',
        'updatedBy'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getDeletedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
