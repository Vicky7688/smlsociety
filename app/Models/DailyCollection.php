<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCollection extends Model
{
    use HasFactory;

    protected $table = 'daily_collections';
    protected $primeryKey = 'id';


}
