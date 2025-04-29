<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAccount extends Model
{
    use HasFactory;

    protected $fillable = [
       'id', 'accountType', 'memberType', 'accountNo', 'guardianAccountNo', 'shareNo', 'savingNo', 'fdNo', 'rdNo', 'loanNo', 'dailyCollectionNo', 'name', 'fatherName', 'birthDate', 'gender', 'caste', 'aadharNo', 'panNo', 'occupation', 'employeeCode', 'ledgerNo', 'pageNo', 'idProof', 'photo', 'signature', 'agentId', 'state', 'district', 'tehsil', 'postOffice', 'village', 'wardNo', 'address', 'pincode', 'phone', 'nomineeName', 'nomineeRelation', 'nomineeBirthDate', 'nomineePhone', 'nomineeAddress', 'share', 'saving', 'openingDate', 'transferDate', 'closingDate', 'status', 'branchId', 'sessionId', 'updatedBy', 'is_delete', 'deleted_at', 'created_at', 'updated_at'
    ];


    public function getjointmember(){
        return $this->hasmany(JointAccount::class,'accountId','id');
    }

    public function generalLedger() {
        return $this->hasMany(MemberAccount::class, 'accountId');
    }

}