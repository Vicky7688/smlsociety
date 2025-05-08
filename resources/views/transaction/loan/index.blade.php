@extends('layouts.app')
@section('title', " Loan")
@section('pagetitle', "Loan")

@php
$table = "no";
@endphp
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="app-academy">
            <div class="row gy-4 mb-4">
                <div class="col-lg-6">
                    <div class="card bg-label-primary h-100">
                        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
                            <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
                                <div class="card-title">
                                    <h4 class="text-primary mb-2">Loan Advancement</h4>
                                    <p class="text-body w-sm-80 app-academy-xl-100">
                                        Advance Loans, Quick Finance, Rapid Funds, Swift Borrowin.
                                    </p>
                                </div>
                                <div class="mb-0"><a style="color: white;" href="{{route('loantype', ['type' => 'advancement'])}}" class="btn btn-primary waves-effect waves-light">Proceed</a></div>
                            </div>
                            <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
                                <img class="img-fluid scaleX-n1-rtl" src="{{asset('assets/img/illustrations/boy-app-academy.png')}}" alt="boy illustration">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card bg-label-danger h-100">
                        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
                            <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
                                <div class="card-title">
                                    <h4 class="text-danger mb-2">Loan Recovery</h4>
                                    <p class="text-body app-academy-sm-60 app-academy-xl-100">
                                        Debt Collection, Retrieving Loans, Repayment Process, Recovery Solutions
                                    </p>
                                </div>
                                <div class="mb-0"><a href="{{route('loantype', ['type' => 'recovery'])}}" class="btn btn-danger waves-effect waves-light">Proceed</a></div>
                            </div>
                            <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
                                <img class="img-fluid scaleX-n1-rtl" src="{{asset('assets/img/illustrations/girl-app-academy.png')}}" alt="girl illustration">
                            </div>
                        </div>
                    </div>
                </div>




                 {{--  <div class="col-lg-3">
                 </div>  --}}
                 {{-- <div class="col-lg-6">
                    <div class="card bg-label-danger h-100">
                        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
                            <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
                                <div class="card-title">
                                    <h4 class="text-danger mb-2">Daily Loan Recovery</h4>
                                    <p class="text-body app-academy-sm-60 app-academy-xl-100">
                                        Debt Collection, Retrieving Loans, Repayment Process, Recovery Solutions
                                    </p>
                                </div>
                                <div class="mb-0"><a href="{{route('loantype', ['type' => 'dailyrecovery'])}}" class="btn btn-danger waves-effect waves-light">Proceed</a></div>
                            </div>
                            <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
                                <img class="img-fluid scaleX-n1-rtl" src="{{asset('assets/img/illustrations/girl-with-laptop.png')}}" alt="girl illustration">
                            </div>
                        </div>
                    </div>
                </div> --}}



                {{-- <div class="col-lg-6">
                    <div class="card bg-label-primary h-100" style="background-color: !important">
                        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
                            <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
                                <div class="card-title">
                                    <h4 class="text-danger mb-2">CCL Loan Recovery</h4>
                                    <p class="text-body app-academy-sm-60 app-academy-xl-100">
                                        Debt Collection, Retrieving Loans, Repayment Process, Recovery Solutions
                                    </p>
                                </div>
                                <div class="mb-0"><a href="{{route('cclrecoveryIndex')}}" class="btn btn-danger waves-effect waves-light">Proceed</a></div>
                            </div>
                            <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
                                <img class="img-fluid scaleX-n1-rtl" src="{{asset('assets/img/illustrations/girl-with-laptop.png')}}" alt="girl illustration">
                            </div>
                        </div>
                    </div>
                </div> --}}




            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-academy.css')}}" />
@endpush
