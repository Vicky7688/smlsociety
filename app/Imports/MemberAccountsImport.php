<?php

namespace App\Imports;

use App\Models\MemberAccount;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MemberAccountsImport implements ToModel, WithHeadings
{
  
    public function model(array $row)
{
    // Log the row for debugging
    \Log::info('Row data', $row);
    
    // Ensure the row has enough elements
    if (count($row) < 17) { // Change this based on your actual expected columns
        return null; // Skip this row
    }

    return new MemberAccount([
        'accountNo' => $row[0] ?? null, // 1st column
        'name' => $row[1] ?? null, // 2nd column
        'birthDate' => is_numeric($row[2]) ? 
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]) : 
            null, // 3rd column
        'fatherName' => $row[3] ?? null, // 4th column
        'gender' => $row[4] ?? null, // 5th column
        'aadharNo' => $row[5] ?? null, // 6th column
        'panNo' => $row[6] ?? null, // 7th column
        'phone' => $row[7] ?? null, // 8th column
        'address' => $row[8] ?? null, // 9th column
        'nomineeName' => $row[9] ?? null, // 10th column
        'nomineeRelation' => $row[10] ?? null, // 11th column
        'ledgerNo' => $row[11] ?? null, // 12th column
        'pageNo' => $row[12] ?? null, // 13th column
        'share' => $row[13] ?? null, // 14th column
        'saving' => $row[14] ?? null, // 15th column
        'openingDate' => is_numeric($row[15]) ? 
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[15]) : 
            null, // 16th column
        'sessionId' => $row[16] ?? null, // 17th column
    ]);
}

    public function headings(): array
    {
        return [
            'accountNo',
            'name',
            'birthDate',
            'fatherName',
            'gender',
            'aadharNo',
            'panNo',
            'phone',
            'address',
            'nomineeName',
            'nomineeRelation',
            'ledgerNo',
            'pageNo',
            'share',
            'saving',
            'OpeningDATE',
            'Financial Year', 
        ];
    }
}
