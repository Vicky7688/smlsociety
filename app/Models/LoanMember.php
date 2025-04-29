<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loanDate',
        'loanEndDate',
        'memberType',
        'loanAcNo',
        'accountNumber',
        'Purpose',
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
    protected $dates = ['deleted_at'];

    public function getUpdatedAtAttribute($value)
    {
        return  date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d m y - h:i A', strtotime($value));
    }


    public function getLoannameAttribute()
    {
        $data = '';
        if ($this->loanType) {
            $loan = \App\Models\LoanMaster::where('id', $this->loanType)->first(['name']);
            $data = $loan->name;
        }
        return $data;
    }

    // Define relationships if necessary
    public function loanmaster()
    {
        return $this->belongsTo(LoanMaster::class, 'loanType'); // Adjust the column name as needed
    }

    public function branch()
    {
        return $this->belongsTo(BranchMaster::class, 'branchId');
    }

    public function session()
    {
        return $this->belongsTo(SessionMaster::class, 'sessionId');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}
