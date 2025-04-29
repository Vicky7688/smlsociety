<?php 

namespace App\Http\Controllers;

use App\Imports\MemberAccountsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MemberAccountController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv', 
        ]);

        Excel::import(new MemberAccountsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Member accounts imported successfully.');
    }
}
