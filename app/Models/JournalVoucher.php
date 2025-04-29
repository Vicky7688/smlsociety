<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'voucherDate',
        'drAmount',
        'crAmount',
        'narration',
        'branchId',
        'sessionId',
        'updatedBy'
    ];

    public function journalVoucherDetails()
    {
        return $this->hasMany(JournalVoucherDetail::class, 'voucherId', 'id');
    }

    public function generalLedger()
    {
        return $this->hasMany(GeneralLedger::class, 'memberType');
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
