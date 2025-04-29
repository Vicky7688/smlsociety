<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'startDate',
        'endDate',
        'status',
        'updatedBy',
        'auditPerformed',
    ];

    public function getStartDateAttribute($value)
    {
        // Assuming $value is a standard date format, modify it to the desired format
        return date('Y-m-d', strtotime($value));
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
