<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bankfd extends Model
{
    use HasFactory;
    protected $table= 'bank_fd_deposit';
    protected $primaryKey= 'id';
}
