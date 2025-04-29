<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
     public function isDateBetween($datecheck) {
        $dateToCheck = Carbon::parse($datecheck);
        $startDate = Carbon::parse(session('sessionStart'));
        $endDate = Carbon::parse(session('sessionEnd'));
    return $dateToCheck->between($startDate, $endDate);
  }
}
