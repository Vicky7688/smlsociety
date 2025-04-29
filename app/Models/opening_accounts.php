<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class opening_accounts extends Model
{
    use HasFactory; 
    protected $table= 'opening_accounts';
    protected $primaryKey= 'id';
}
