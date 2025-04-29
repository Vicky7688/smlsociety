<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisIntallment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function generalLedgers()
    {
        return $this->hasMany(GeneralLedger::class, 'serialNo', 'serialNo');
    }
}
