<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCollectionLoan extends Model
{
    protected $table = "daily_collection_loan";
    use HasFactory, SoftDeletes;
}
