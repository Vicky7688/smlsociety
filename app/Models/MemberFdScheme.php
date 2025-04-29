<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MemberAccount;

class MemberFdScheme extends Model
{
    use HasFactory;

    protected $table = "member_fds_scheme";
    protected $fillable = [
        'fdNo',
        'matureserialNo',
        'serialNo',
        'accountId',
        'accountNo',
        'membershipno', 
        'secheme_id',  
        'memberType',
        'groupCode',
        'ledgerCode',
        'fdType',
        'openingDate',
        'principalAmount',
        'interestType',
        'interestStartDate',
        'interestRate',
        'interestAmount',
        'years',
        'months',
        'days',
        'maturityDate',
        'maturityAmount',
        'actualMaturityDate',
        'actualInterestAmount',
        'actualMaturityAmount',
        'ledgerNo',
        'pageNo',
        'narration',
        'transferedFrom',
        'paymentType',
        'bank',
        'chequeNo',
        'transferedTo',
        'transferedPaymentType',
        'transferedBank',
        'transferedChequeNo',
        'nomineeName1',
        'nomineeRelation1',
        'nomineeBirthDate1',
        'nomineePhone1',
        'nomineeAddress1',
        'nomineeName2',
        'nomineeRelation2',
        'nomineeBirthDate2',
        'nomineePhone2',
        'nomineeAddress2',
        'renewDate',
        'onmaturityDate', 
        'TDSInterest',
         'TDSAmount',    
        'oldFdNo',
        'status',
        'agentId',
        'branchId',
        'sessionId',
        'is_delete',
        'updatedBy',
        'autorenew',
    ];

    

         public function memberAccount()
        {
            return $this->belongsTo(MemberAccount::class, 'membershipno', 'accountNo');
        }
        public function openingAccount()
        {
            return $this->belongsTo(MemberAccount::class, 'membershipno', 'accountNo');
        }


        


}
