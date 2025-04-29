<?php

namespace App\Imports;

use App\Models\MemberAccount;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            $birthDate = Date::excelToDateTimeObject($row[2]);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    
        return new MemberAccount([
         
            'accountNo' => $row[0],
            'name' => $row[1],
            'birthDate' => Date::excelToDateTimeObject($row[2]),
            'fatherName' => $row[3],
            'gender' => $row[4],
            'aadharNo' => $row[5],
            'panNo' => $row[6],
            'phone' => $row[7],
            'address' => $row[8],
            'nomineeName' => $row[9],
            'nomineeRelation' => $row[10],
            'ledgerNo' => $row[11],
            'pageNo' => $row[12],
            'share' => $row[13],
            'saving' => $row[14],
            'openingDate' => Date::excelToDateTimeObject($row[15]), 
            
        ]);
    }
}
