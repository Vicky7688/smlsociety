<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitOrLoss extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'profit_losses';
}
