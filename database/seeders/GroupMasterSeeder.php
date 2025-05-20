<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('group_masters')->truncate();
        DB::table('group_masters')->insert([
            ['name'=>'Cash', 'groupCode'=>'C002', 'headName'=>'Cash', 'type'=>'Asset', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Bank', 'groupCode'=>'BANK001', 'headName'=>'Bank', 'type'=>'Asset', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Income', 'groupCode'=>'INCM001', 'headName'=>'Income', 'type'=>'Income', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Expenses', 'groupCode'=>'EXPN001', 'headName'=>'Expenses', 'type'=>'Expenditure', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Share', 'groupCode'=>'SHAM001', 'headName'=>'Share', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Contribution', 'groupCode'=>'CON001', 'headName'=>'Contributions', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            // ['name'=>'CDS', 'groupCode'=>'CDSM001', 'headName'=>'CDS', 'type'=>'Liability', 'showJournalVoucher'=>'No', 'status'=>'Active', 'updatedBy'=>1],

            ['name'=>'Saving Member', 'groupCode'=>'SAVM001', 'headName'=>'Saving', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Saving NonMember', 'groupCode'=>'SAVN001', 'headName'=>'Saving', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Saving Staff', 'groupCode'=>'SAVS001', 'headName'=>'Saving', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            ['name'=>'FD Member', 'groupCode'=>'FDOM001', 'headName'=>'Fixed Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'FD NonMember', 'groupCode'=>'FDON001', 'headName'=>'Fixed Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'FD Staff', 'groupCode'=>'FDOS001', 'headName'=>'Fixed Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            ['name'=>'RD Member', 'groupCode'=>'RDOM001', 'headName'=>'Recurring Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'RD NonMember', 'groupCode'=>'RDON001', 'headName'=>'Recurring Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'RD Staff', 'groupCode'=>'RDOS001', 'headName'=>'Recurring Deposit', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            ['name'=>'Loan Member', 'groupCode'=>'LONM001', 'headName'=>'Loan', 'type'=>'Asset', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Loan NonMember', 'groupCode'=>'LONN001', 'headName'=>'Loan', 'type'=>'Asset', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Loan Staff', 'groupCode'=>'LONS001', 'headName'=>'Loan', 'type'=>'Asset', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            // ['name'=>'Daily Collection Member', 'groupCode'=>'DCOM001', 'headName'=>'Daily Collection', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            // ['name'=>'Daily Collection NonMember', 'groupCode'=>'DCON001', 'headName'=>'Daily Collection', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            // ['name'=>'Daily Collection Staff', 'groupCode'=>'DCOS001', 'headName'=>'Daily Collection', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            // ['name'=>'Monthly Income Scheme Member', 'groupCode'=>'MISM001', 'headName'=>'Monthly Income Scheme', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            // ['name'=>'Monthly Income Scheme NonMember', 'groupCode'=>'MISN001', 'headName'=>'Monthly Income Scheme', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            // ['name'=>'Monthly Income Scheme Staff', 'groupCode'=>'MISS001', 'headName'=>'Monthly Income Scheme', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],

            // ['name'=>'Purchase Account', 'groupCode'=>'PURC001', 'headName'=>'Purchase Account', 'type'=>'Trading', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            // ['name'=>'Sale Account', 'groupCode'=>'SALE001', 'headName'=>'Sale Account', 'type'=>'Trading', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1],
            ['name'=>'Duty & Tax', 'groupCode'=>'DUTY001', 'headName'=>'Duty & Tax', 'type'=>'Liability', 'showJournalVoucher'=>'Yes', 'status'=>'Active', 'updatedBy'=>1]
        ]);
    }
}
