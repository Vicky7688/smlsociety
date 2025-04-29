<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LedgerMaster extends Model
{
    // use HasFactory;
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    protected $fillable = [
        'groupCode',
        'name',
        'ledgerCode',
        'openingAmount',
        'openingType',
        'reference_id',
        'loanmasterId',
        'status',
        'is_delete',
        'bankfd_id',
        'sch_id',
        'updatedBy',
        // 'deleted_at'
    ];


}
