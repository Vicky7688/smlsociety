<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GroupMaster extends Model
{
    use HasFactory;

    // use SoftDeletes;
    protected $fillable = [
        'name',
        'groupCode',
        'headName',
        'type',
        'showJournalVoucher',
        'sortBy',
        'status',
        'updatedBy',
    ];
    public function ledger(){
        return $this->hasMany(LedgerMaster::class, 'groupCode', 'groupCode');
    }

    public function generalLedger () {
        return $this->hasMany(GeneralLedger::class, 'groupCode','ledgerCode');
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
