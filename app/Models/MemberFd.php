<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MemberAccount;

class MemberFd extends Model
{
    use HasFactory;

    protected $fillable = [
        'fdNo',
        'serialNo',
        'saving_account',
        'accountId',
        'accountNo',
        'membershipno',
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
        'oldFdNo',
        'status',
        'agentId',
        'branchId',
        'sessionId',
        'is_delete',
        'updatedBy',
    ];

    public function memberAccount()
    {
        return $this->belongsTo(MemberAccount::class, 'accountId');
    }
}
