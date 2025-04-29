<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankFdMaster extends Model
{
    use HasFactory;

    protected $table = 'bank_fd_masters';
    protected $primaryKey = 'id';
}
