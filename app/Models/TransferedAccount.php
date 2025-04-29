<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferedAccount extends Model
{
    use HasFactory;
    public function getjointmember(){
        return $this->hasmany(JointAccount::class,'accountId','accountId');
    }
}
