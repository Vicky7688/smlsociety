<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemeMaster extends Model
{
    use HasFactory;

    protected $table = 'scheme_masters'; 
    protected $primaryKey = 'id';
    protected $fillable=['start_date', 'name','interest_type','memberType', 'secheme_type', 'durationType', 'duration', 'years', 'months', 'days', 'interest', 'penaltyInterest', 'status', 'updatedBy', 'lockin_years', 'lockin_months', 'lockin_days'];
    
    public function getUpdatedAtAttribute($value){
        return  date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value){
        return date('d m y - h:i A', strtotime($value));
    }
}
