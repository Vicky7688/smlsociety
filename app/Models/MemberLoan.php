<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MemberAccount;


class MemberLoan extends Model
{
    use HasFactory, SoftDeletes;

    // protected $table = 'member_loan';

    protected $fillable = [
        'serialNo',
        'accountId',
        'accountNo',
        'loanDate',
        'loanEndDate',
        'memberType',
        'loanAcNo',
        'purpose',
        'loanType',
        'processingFee',
        'processingRates',
        'cropType',
        'cropMasterId',
        'invoiceNumber',
        'loanYear',
        'loanMonth',
        'loanInterest',
        'loanPanelty',
        'fdId',
        'fdAmount',
        'dailyId',
        'daily_amount',
        'loanAmount',
        'bankDeduction',
        'deductionAmount',
        'pernote',
        'loanBy',
        'ledgerBankAccountId',
        'chequeNo',
        'installmentType',
        'guranter1',
        'guranter2',
        'Status',
        'branchId',
        'sessionId',
        'updatedBy',
        'bankname',
    ];
    public $appends = ["loanname"];
    public $with = ['memberAccount'];
    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function installments()
    {
        return $this->hasMany('App\Models\LoanInstallment',  'LoanId', 'id');
    }

    public function getLoannameAttribute()
    {
        $data = '';
        if ($this->loanType) {
            $loan = \App\Models\LoanMaster::where('id', $this->loanType)->first(['name']);
            if ($loan) {
                $data = $loan->name;
            } else {
                // Return empty if no matching loan type is found
                $data = '';
            }
        }
        return $data;
    }

    public function getDeletedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function memberAccount()
    {
        return $this->belongsTo(MemberAccount::class, 'accountId');
    }

    public function memberAccountGuranter1()
    {
        return $this->belongsTo(MemberAccount::class, 'guranter1', 'accountNo')->where('memberType', 'Member');
    }

    public function memberAccountGuranter2()
    {
        return $this->belongsTo(MemberAccount::class, 'guranter2', 'accountNo')->where('memberType', 'Member');
    }
}
