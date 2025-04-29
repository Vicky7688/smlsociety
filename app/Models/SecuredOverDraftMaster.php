<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuredOverDraftMaster extends Model
{
    use HasFactory;

    protected $table = 'sod_masters';
    protected $primaryKey = 'id';
}
