<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FdTypeMaster extends Model
{
    use HasFactory; 
    protected $table = 'fd_type_master'; 
    protected $fillable = ['sortNo','type','updatedBy']; 


    


}
