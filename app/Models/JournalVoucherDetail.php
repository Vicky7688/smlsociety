<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalVoucherDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serialNo',
        'voucherId',
        'groupCode',
        'ledgerCode',
        'transactionType',
        'drAmount',
        'crAmount',
        'branchId',
        'sessionId',
        'updatedBy'
    ];

    public $append = ["groupname"];

    public function getGroupnameAttribute()
    {
        return $this->groupMaster->name ?? '';
    }

    public function group() {
        return $this->belongsTo(GroupMaster::class,'groupCode','groupCode');
    }

    public function voucher()
    {
        return $this->belongsTo(JournalVoucher::class, 'voucherId');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}