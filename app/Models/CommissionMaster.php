<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CommissionMaster extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $fillable = [
        'startDate',
        'endDate',
        'commissionSaving',
        'commissionFD',
        'commissionRD',
        'commissionShare',
        'commissionLoan',
        'commissionDailyCollection',
        'status',
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
}
