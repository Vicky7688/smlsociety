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
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Balance Book</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="balancebookform">
                            <div class="row">
                                {{-- <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="due_up_to" class="form-label">From date </label>
                                <input type="text" class="form-control formInputsReport" id="due_from_to" name="due_from_to" />
                            </div> --}}
                                @php
                                    /* $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionEnd')));*/
                                    $currentDate = date('d-m-Y', strtotime(session('sessionEnd')));
                                @endphp
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="due_up_to" class="form-label">due up to</label>
                                    <input type="text" class="form-control formInputsReport" id="due_up_to"
                                        name="due_up_to" value="{{ $currentDate }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        {{-- <option value="Staff">Staff</option>
                                        <option value="NonMember">Nominal Member</option> --}}
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport d-none">
                                    <label for="memberType" class="form-label">Loan Type</label>
                                    <select class="form-select formInputsSelectReport" id="loanType" name="loanType">
                                        <option value="WithoutloanAgainstfd">Without FD Against Loan</option>
                                        <option value="LoanAgainstfd">FD Against Loan</option>
                                    </select>
                                </div>
                                <div class="col-lg-5 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">

                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                            id="viewdatabooksdetails">
                                            View
                                        </button>
                                        <button id="printButton" onclick="printReport()" type="button"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </button>
                                        <div class="ms-2 dropdown">
                                            <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                More
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="downloadPDF()">
                                                        <i class="fa-regular fa-file-pdf"></i>
                                                        &nbsp; Download as PDF
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="downloadWord()">
                                                        <i class="fa-regular fa-file-word"></i>
                                                        &nbsp; Download as Word
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="share()">
                                                        <i class="fa-solid fa-share-nodes"></i>
                                                        &nbsp; Share
                                                    </a>
                                                </li>
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

        {{--  <div class="card">
        <div class="card-body tablee printDiv" id="balanebookrecords">

        </div>
    </div>  --}}

        <div class="card" id="sharelistprint">
            <div class="card-body tablee">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th class="fw-bold">SR No</th>
                                <th class="fw-bold">Employee Code</th>
                                <th class="fw-bold">Name</th>
                                <th class="fw-bold">Share</th>
                                <th class="fw-bold">Contribution</th>
                                <th class="fw-bold">Loan Date</th>
                                <th class="fw-bold">Loan Amt</th>
                                <th class="fw-bold">Loan Bal</th>
                                <th class="fw-bold">Loan Inst. Dt</th>
                                <th class="fw-bold">Intt. Recoverable</th>
                            </tr>
                        </thead>
                        <tbody class="bg-secondary-subtle" style="background-color: white !important;" id="tablebody">
                            <tr>
                                <td colspan="10" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="loader"
        style="display:none; position:fixed; top:60%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function capitalizeWords(str) {
            if (!str || typeof str !== 'string') {
                return '';
            }
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        function dateFormat(date) {
            let dates = new Date(date);
            let daysss = dates.getDate();
            let monthss = dates.getMonth() + 1;
            let yearss = dates.getFullYear();

            daysss = daysss < 10 ? `0${daysss}` : daysss;
            monthss = monthss < 10 ? `0${monthss}` : monthss;
            let formattedDate = `${daysss}-${monthss}-${yearss}`;
            return formattedDate;
        }


        $(document).ready(function() {
            $(document).on('submit', '#balancebookform', function(event) {
                event.preventDefault();

                let endDate = $('#due_up_to').val();
                let memberType = $('#memberType').val();
                let loanType = $('#loanType').val();


                $('#loader').show();

                $.ajax({
                    url: "{{ route('balancebookgetdata') }}",
                    type: 'post',
                    data: {
                        endDate: endDate,
                        memberType: memberType,
                        loanType: loanType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#loader').hide();

                        if (res.status === 'success') {
                            let allDetails = res.allDetails;
                            $('#tablebody').empty();

                            let sharegrandtotal = 0;
                            let conttributiongrandtotal = 0;
                            let loangrandtotal = 0;
                            let loanbalancegrandtotal = 0;
                            let interestrecoverablegrandtotal = 0;



                            if (Array.isArray(allDetails) && allDetails.length > 0) {
                                allDetails.forEach((data, index) => {
                                    let Share = parseFloat(data.Share) || 0;
                                    let Contribution = parseFloat(data.Contribution) || 0;
                                    let LoanAmount = parseFloat(data.LoanAmount) || 0;
                                    let LoanBalance = parseFloat(data.LoanBalance) || 0;
                                    let InterestRecoverable = parseFloat(data
                                        .InterestRecoverable) || 0;

                                    // ✅ Skip this row if all values are 0
                                    if (
                                        Share === 0 &&
                                        Contribution === 0 &&
                                        LoanAmount === 0 &&
                                        LoanBalance === 0 &&
                                        InterestRecoverable === 0
                                    ) {
                                        return; // Skip this entry
                                    }

                                    let row = `<tr>
                                        <td>${ index + 1}</td>
                                        <td>${ data.AccountNo }</td>
                                        <td>${ data.MemberName || '-'}</td>
                                        <td>${ Share.toFixed(2) }</td>
                                        <td>${ Contribution.toFixed(2) }</td>
                                        <td>${ data.LoanDate || '-' }</td>
                                        <td>${ LoanAmount.toFixed(2) }</td>
                                        <td>${ LoanBalance.toFixed(2) }</td>
                                        <td>${ data.LastInstDate || '-' }</td>
                                        <td>${ InterestRecoverable.toFixed(2) }</td>
                                    </tr>`;
                                    $('#tablebody').append(row);
                                    // Only add to totals if you want grand totals to exclude 0s
                                    sharegrandtotal += Share;
                                    conttributiongrandtotal += Contribution;
                                    loangrandtotal += LoanAmount;
                                    loanbalancegrandtotal += LoanBalance;
                                    interestrecoverablegrandtotal += InterestRecoverable;
                                });

                                $('#tablebody').append(`<tr style="background-color:#7367f0">
                                    <td colspan="3" style="color:white;">Grand Total</td>
                                    <td style="color:white;">${sharegrandtotal.toFixed(2)}</td>
                                    <td style="color:white;">${conttributiongrandtotal.toFixed(2)}</td>
                                    <td style="color:white;"></td>
                                    <td style="color:white;">${loangrandtotal.toFixed(2)}</td>
                                    <td style="color:white;">${loanbalancegrandtotal.toFixed(2)}</td>
                                    <td style="color:white;"></td>
                                    <td style="color:white;">${interestrecoverablegrandtotal.toFixed(2)}</td>
                                </tr>`);

                                if (interestrecoverablegrandtotal > 0) {
                                    {{--  addfinancialyearendinterestpayable(interestrecoverablegrandtotal,endDate,memberType);  --}}
                                }
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loader').hide(); // Hide the loader on error
                        console.log('AJAX request failed:', error);
                    }
                });
            });

            {{--  var currentDate = moment().format('DD-MM-YYYY');
        $("#due_up_to").val(currentDate);  --}}
            {{--  $("#balancebookform").validate({
            rules:{
                due_up_to:{
                    required: true,
                    customDate: true,
                },
            },
            messages:{
                due_up_to:{
                    required: "Please enter a date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
            },
            errorElement: "p",
            errorPlacement: function (error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                axios.post('{{route("get.balancebook.details")}}',formData).then((response)=>{
                    if(response.data.status == "success"){
                        $("#balanebookrecords").html(response.data.data);
                        let loaninterestrecoverable = response.data.loanInterestRecover;

                        addfinancialyearendinterestpayable(loaninterestrecoverable);

                    }else if(response.data.status == "fail"){

                    }
                });
            }
        });  --}}
        });

        {{--  function addfinancialyearendinterestpayable(loaninterestrecoverable,endDate,memberType) {
            $.ajax({
                url: "{{ route('loaninterestrecoverable') }}",
                type: 'post',
                data: {
                    loaninterestrecoverable: loaninterestrecoverable,
                    endDate : endDate,
                    memberType : memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        notify(res.messages, 'success');
                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }  --}}


        function printReport() {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            let printContents = document.getElementById('sharelistprint').innerHTML;

            let css = `
                <style>
                    body { margin: 10px; font-family: Arial, sans-serif; zoom: 90%;
                        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    #sharelistprint {
                        display: flex;
                        justify-content: space-between;
                        width: 100%;
                    }
                    .cards {
                        width: 48%;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 10px;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 6px;
                        text-align: left;
                        font-size: 12px;
                    }
                    .t-heading {
                        font-weight: bold;
                        text-align: center;
                        font-size: 14px;
                    }
                    .grand-total {
                        font-weight: 900 !important;
                        color: #000000 !important;
                        font-size: 16px !important;
                    }
                    h4, h6 {
                        margin: 2px 0;
                        text-align: center;
                    }
                    .table th, .table td {
                        vertical-align: middle !important; /* Ensures that the text is centered vertically in the table cells */
                    }
                </style>`;

            let header = `
                <div style="text-align: center; margin-bottom: 10px;">
                    <h4>{{ $branch->name }}</h4>
                    <h6>{{ $branch->address }}</h6>
                    <h6>Day Book from ${startDate} to ${endDate}</h6>
                </div>`;

            let newWindow = window.open('', '_blank');
            newWindow.document.write('<html><head><title>Print</title>' + css + '</head><body>');
            newWindow.document.write(header);
            newWindow.document.write('<div id="sharelistprint">' + printContents + '</div>');
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            newWindow.close();
        }
    </script>
@endpush
