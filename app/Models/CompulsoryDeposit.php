<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompulsoryDeposit extends Model
{
    protected $guarded = [];

    // Define relationships or additional methods here if needed
    
     protected $fillable = [
        'serialNo', 'accountId', 'type', 'accno', 'date', 'Withdraw', 'Deposit',
        'agent', 'Interest', 'acc', 'Bank', 'narrartion', 'entry_mode', 'ChqNo',
        'membertype', 'admissionfee', 'SessionYear', 'Branch', 'logged_branch',
        'is_delete', 'DeletedBy', 'LoginId', 'bankname'
    ];
}
 