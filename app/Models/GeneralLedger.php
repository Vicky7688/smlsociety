<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GroupMaster;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralLedger extends Model
{
    use HasFactory;

    public $appends = ["groupname", "grouptype"];

    public function getGroupnameAttribute()
    {
        return $this->groupMaster->name ?? '';
    }


    public function group()
    {
        return $this->belongsTo(GroupMaster::class, 'groupCode', 'groupCode');
    }

    public function ledger()
    {
        return $this->belongsTo(LedgerMaster::class, 'ledgerCode', 'ledgerCode');
    }

    public function account()
    {
        return $this->belongsTo(MemberAccount::class, 'accountId');
    }

    public function getGrouptypeAttribute()
    { 
        return $this->group->type ?? "";
    }


    protected $fillable = [
        'serialNo',
        'transactionType',
        'accountId',
        'accountNo',
        'memberType',
        'formName',
        'referenceNo',
        'entryMode',
        'transactionDate',
        'transactionAmount',
        'narration',
        'groupCode',
        'ledgerCode',
        'branchId',
        'sessionId',
        'agentId',
        'updatedBy',
        'is_delete'
    ];
}
