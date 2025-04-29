@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- <p class="h4"><span>Balance Book</span></p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" class="text-muted fw-light">Reports</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-muted fw-light">General Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Balance Book</li>
        </ol>
    </nav> -->
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Personal Ledger</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="personalledgerform">
                        <div class="row">
                            @php
                            $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                        @endphp
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="DATE FROM" class="form-label">DATE FROM</label>
                                <input type="text" class="form-control formInputsReport" id="date_from" name="date_from" value="{{ $currentDate }}" readonly />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="DATE TO" class="form-label">DATE TO</label>
                                <input type="text" class="form-control formInputsReport" id="date_to" name="date_to" readonly/>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                    <option value="Staff">Staff</option>
                                    <option value="NonMember">Nominal Member</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="ACCOUNT NO" class="form-label">ACCOUNT NO</label>
                                <input type="number" class="form-control formInputsReport" id="account_no" name="account_no"/>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="Account Type" class="Account Type">Account Type</label>
                                <select class="form-select formInputsSelectReport" id="accounttype" name="accounttype">
                                    <option value="">Select Account Type</option>
                                    <option value="All">All</option>
                                    <option value="Rd">RD</option>
                                    <option value="Fd">FD</option>
                                    <option value="Loan">LOAN</option>
                                    <option value="Mis">MIS</option>
                                    <option value="Saving">SAVING</option>
                                    <option value="Share">SHARE</option>
                                    <option value="DailyCollection">Daily Collection</option>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">

                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                        id="viewddetails">
                                        View
                                    </button>
                                    <a type="button" href="{{route('balancebookPrint.print')}}" target="_blank"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </a>
                                    <div class="ms-2 dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body tablee" id="personledgerrecords">

        </div>
    </div>

</div>
@endsection
@push('script')
<script>
    $(document).ready(function(e){
        var currentYear = new Date().getFullYear();
        var PastYear = currentYear - 1;
        var formattedDate1 = "01-04-" + PastYear;
        var currentDate = moment().format('DD-MM-YYYY');
        {{--  $("#date_from").val(formattedDate1);  --}}
        $("#date_to").val(currentDate);


        $("#personalledgerform").validate({
            rules:{
                account_no:{
                    required: true,
                },
                accounttype:{
                    required: true,
                }
            },
            message:{
                account_no:{
                    required: "Please enter Account No",
                },
                accounttype:{
                    required: "Please enter Account No",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#viewddetails").prop("disabled",true);
                axios.post('{{route("personal.ledger.details")}}',formData).then((response)=>{
                    if(response.data.status =="success"){
                        $("#viewddetails").prop("disabled",false);
                        $("#personledgerrecords").html(response.data.data);
                    }else if(response.data.status =="fail"){
                        $("#account_no").val('');
                        $("#viewddetails").prop("disabled",false);
                        notify(response.data.message, 'warning');
                    }
                });
            }
        });
    });
</script>
@endpush
