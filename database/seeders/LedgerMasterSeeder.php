<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ledger_masters')->truncate();
        DB::table('ledger_masters')->insert([
            ['groupCode'=>'C002', 'name'=>'Cash', 'ledgerCode'=>'C002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'SHAM001', 'name'=>'Share', 'ledgerCode'=>'SHAM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'SAVM001', 'name'=>'Saving Member', 'ledgerCode'=>'SAVM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'SAVN001', 'name'=>'Saving NonMember', 'ledgerCode'=>'SAVN001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'SAVS001', 'name'=>'Saving Staff', 'ledgerCode'=>'SAVS001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'FDOM001', 'name'=>'FD Member', 'ledgerCode'=>'FDOM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'FDON001', 'name'=>'FD NonMember', 'ledgerCode'=>'FDON001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'FDOS001', 'name'=>'FD Staff', 'ledgerCode'=>'FDOS001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'RDOM001', 'name'=>'RD Member', 'ledgerCode'=>'RDOM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'RDON001', 'name'=>'RD NonMember', 'ledgerCode'=>'RDON001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'RDOS001', 'name'=>'RD Staff', 'ledgerCode'=>'RDOS001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'LONM001', 'name'=>'Loan Member', 'ledgerCode'=>'LONM001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'LONN001', 'name'=>'Loan NonMember', 'ledgerCode'=>'LONN001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'LONS001', 'name'=>'Loan Staff', 'ledgerCode'=>'LONS001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DCOM001', 'name'=>'Daily Collection Member', 'ledgerCode'=>'DCOM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DCON001', 'name'=>'Daily Collection NonMember', 'ledgerCode'=>'DCON001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DCOS001', 'name'=>'Daily Collection Staff', 'ledgerCode'=>'DCOS001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'MISM001', 'name'=>'Monthly Income Scheme Member', 'ledgerCode'=>'MISM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'MISN001', 'name'=>'Monthly Income Scheme NonMember', 'ledgerCode'=>'MISN001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'MISS001', 'name'=>'Monthly Income Scheme Staff', 'ledgerCode'=>'MISS001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],

            ['groupCode'=>'EXPN001', 'name'=>'Share Interest', 'ledgerCode'=>'SHAM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Member Saving', 'ledgerCode'=>'SAVM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on NonMember Saving', 'ledgerCode'=>'SAVN002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Staff Saving', 'ledgerCode'=>'SAVS002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Member FD', 'ledgerCode'=>'FDOM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on NonMember FD', 'ledgerCode'=>'FDON002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Staff FD', 'ledgerCode'=>'FDOS002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Member RD', 'ledgerCode'=>'RDOM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on NonMember RD', 'ledgerCode'=>'RDON002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Staff RD', 'ledgerCode'=>'RDOS002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Intt.Recv on Member Loan', 'ledgerCode'=>'LONM002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Intt.Recv on NonMember Loan', 'ledgerCode'=>'LONN002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Intt.Recv on Staff Loan', 'ledgerCode'=>'LONS002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Member Daily Collection', 'ledgerCode'=>'DCOM002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on NonMember Daily Collection', 'ledgerCode'=>'DCON002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Staff Daily Collection', 'ledgerCode'=>'DCOS002', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Member Monthly Income Scheme', 'ledgerCode'=>'MISM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on NonMember Monthly Income Scheme', 'ledgerCode'=>'MISN002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Intt.Paid on Staff Monthly Income Scheme', 'ledgerCode'=>'MISS002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],

            ['groupCode'=>'INCM001', 'name'=>'Share Penalty', 'ledgerCode'=>'SHAM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Member Saving', 'ledgerCode'=>'SAVM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on NonMember Saving', 'ledgerCode'=>'SAVN003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Staff Saving', 'ledgerCode'=>'SAVS003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Member FD', 'ledgerCode'=>'FDOM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on NonMember FD', 'ledgerCode'=>'FDON003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Staff FD', 'ledgerCode'=>'FDOS003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Member RD', 'ledgerCode'=>'RDOM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on NonMember RD', 'ledgerCode'=>'RDON003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Staff RD', 'ledgerCode'=>'RDOS003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Member Loan', 'ledgerCode'=>'LONM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on NonMember Loan', 'ledgerCode'=>'LONN003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Staff Loan', 'ledgerCode'=>'LONS003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Member Daily Collection', 'ledgerCode'=>'DCOM003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on NonMember Daily Collection', 'ledgerCode'=>'DCON003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Penalty Recv on Staff Daily Collection', 'ledgerCode'=>'DCOS003', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],

            ['groupCode'=>'INCM001', 'name'=>'Loan Pending Interest Member', 'ledgerCode'=>'LONM004', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Loan Pending Interest NonMember', 'ledgerCode'=>'LONN004', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Loan Pending Interest Staff', 'ledgerCode'=>'LONS004', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],

            ['groupCode'=>'PURC001', 'name'=>'Purchase Account', 'ledgerCode'=>'PURC001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'SALE001', 'name'=>'Sale Account', 'ledgerCode'=>'SALE001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DUTY001', 'name'=>'CESS', 'ledgerCode'=>'CESS001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DUTY001', 'name'=>'IGST', 'ledgerCode'=>'IGST001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DUTY001', 'name'=>'SGST', 'ledgerCode'=>'SGST001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'DUTY001', 'name'=>'CGST', 'ledgerCode'=>'CGST001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Freight', 'ledgerCode'=>'FRET001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Labour', 'ledgerCode'=>'LABR001', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Discount', 'ledgerCode'=>'DISC001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'INCM001', 'name'=>'Commission', 'ledgerCode'=>'COMM001', 'openingType'=>'Cr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],

            ['groupCode'=>'EXPN001', 'name'=>'Interest Paid DDS Member', 'ledgerCode'=>'DDSIM002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Interest Paid DDS NonMember', 'ledgerCode'=>'DDSIN002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
            ['groupCode'=>'EXPN001', 'name'=>'Interest Paid DDS Staff', 'ledgerCode'=>'DDSIS002', 'openingType'=>'Dr', 'openingAmount'=>0, 'status'=>'Active', 'updatedBy'=>1],
        ]);
    }
}