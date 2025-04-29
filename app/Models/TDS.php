<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDS extends Model
{
    use HasFactory;

    protected $table = 'tds_master';
    protected $primaryKey = 'id';
}
