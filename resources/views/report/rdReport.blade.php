@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Recurring Deposit Report</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        @php
                            /* $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionEnd')));*/
                            $currentDate = date('d-m-Y', strtotime(session('sessionEnd')));

                        @endphp
                        <form action="javascript:void(0)" id="rdReportForm" name="rdReportForm">
                            <div class="row d-flex align-items-center">
                                <!-- Date Input -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="endDate" class="form-label">Up To Date</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate" value="{{ $currentDate }}" />
                                </div>

                                <!-- Member Type Select -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelectReport" id="memberType" name="memberType"
                                        onchange="getSchemes('this')">
                                        <option value="All">All</option>
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>

                                <!-- Scheme Type Select -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="schemeType" class="form-label">Scheme Type</label>
                                    <select class="form-select formInputsSelectReport" id="schemeType" name="schemeType">
                                        <option value="All">All</option>
                                    </select>
                                </div>

                                <!-- Buttons Section -->
                                <div class="col d-flex three-btns align-items-center">
                                    <button type="submit" id="viewReportBtn"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom me-2">View
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                            style="display: none;"></span>
                                    </button>
                                    {{-- <a type="button" href="{{ route('rdPrint.print') }}" target="_blank"
                                        class="btn btn-primary print-button reportSmallBtnCustom me-2">Print</a> --}}
                                    <button type="button" class="btn btn-primary print-button reportSmallBtnCustom me-2"
                                        onclick="printReport()"> Print </button>

                                    <!-- More Button Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button"
                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"> More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="#" onClick="downloadPDF()"><i
                                                        class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onClick="downloadWord()"><i
                                                        class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#" onClick="share()"><i
                                                        class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="sharelistprint">
            <div class="card-body tablee">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th class="fw-bold">Sr No</th>
                                <th class="fw-bold">A/c No</th>
                                <th class="fw-bold">Member Name</th>
                                <th class="fw-bold">RD Date</th>
                                <th class="fw-bold">RD Amount</th>
                                <th class="fw-bold">Payable Int</th>
                                <th class="fw-bold">Total Payable Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="bg-secondary-subtle" style="background-color: white !important;">
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
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

        function getSchemes() {
            let memberType = $('#memberType').val();

            $.ajax({
                url: "{{ route('getrdschemes') }}",
                type: 'post',
                data: {
                    memberType: memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let schemes = res.schemes;
                        let schemeTypeDropdown = $('#schemeType');
                        schemeTypeDropdown.empty();


                        if (schemes && schemes.length > 0) {
                            schemeTypeDropdown.append(`<option value="All">All</option>`);
                            schemes.forEach((data) => {
                                schemeTypeDropdown.append(
                                    `<option value="${data.id}">${data.name}</option>`);
                            });
                        } else {
                            schemes.forEach((data) => {
                                schemeTypeDropdown.append(`<option value=""></option>`);
                            });
                        }

                    } else {
                        toastr.error(res.messages);
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#rdReportForm').validate({
                rules: {
                    endDate: {
                        required: true,
                        customDate: true,
                    }
                },
                messages: {
                    endDate: {
                        required: 'Required',
                        customDate: 'Enter Valid Date',
                    }
                }
            });


            function DateFormat(date) {
                let transactionDate = new Date(date);
                let day = transactionDate.getDate();
                let month = transactionDate.getMonth() + 1;
                let year = transactionDate.getFullYear();

                day = day < 10 ? `0${day}` : day;
                month = month < 10 ? `0${month}` : month;


                let formattedDate = `${day}-${month}-${year}`;
                return formattedDate;
            }


            $('#endDate').change(function() {
                fetchAndDisplayData();
            });


            $('#viewReportBtn').click(function() {
                fetchAndDisplayData();
            });

            function fetchAndDisplayData() {
                let endDatesssss = $('#endDate').val();

                let formData = $('#rdReportForm').serialize();
                $('.spinner-border').css('display', 'inline-block');
                $.ajax({
                    url: "{{ route('getrdData') }}",
                    type: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        let tbody = $('#tbody');
                        tbody.empty();
                        if (res.status === 'success') {
                            let rdaccount = res.rdaccount;

                            if (rdaccount && rdaccount.length > 0) {


                                let grandTotal = 0;
                                let grandInterestTotal = 0;
                                let principalgrand = 0;
                                let dates = $('#endDate').val();

                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);


                                rdaccount.forEach((data, index) => {
                                    let transactionDate = new Date(data.date);
                                    let day = transactionDate.getDate().toString().padStart(2,
                                        '0');
                                    let month = (transactionDate.getMonth() + 1).toString()
                                        .padStart(2, '0');
                                    let year = transactionDate.getFullYear();
                                    let formattedDate = `${day}-${month}-${year}`;

                                    principal = parseFloat(data.amount || 0);
                                    let a = 0;

                                    let interest = 0;
                                    let totalAmount = 0;
                                    let rate = parseFloat(data.interest);
                                    let months = calculateMonthDifference(transactionDate,
                                        endDate);
                                    let rdAmount = calculateRDAmount(principal, rate, months);
                                    interest = calculateInterest(principal, rate, months);
                                    a = principal;
                                    totalAmount = principal + interest;

                                    // Generate the row to display
                                    let row = `<tr>
                                            <td>${index + 1}</td>
                                            <td>${data.rd_account_no}</td>
                                            <td>${data.name}</td>
                                            <td>${formattedDate}</td>
                                            <td>${a.toFixed(2)}</td>
                                            <td>${interest.toFixed(2)}</td>
                                            <td>${totalAmount.toFixed(2)}</td>
                                        </tr>`;
                                    tbody.append(row);

                                    // Accumulate grand totals
                                    principalgrand += principal;
                                    grandTotal += totalAmount;
                                    grandInterestTotal += interest;


                                });



                                let grandTotalRow = `<tr>
                                    <td colspan="4"><b>Grand Total</b></td>
                                    <td>${principalgrand.toFixed(2)}</td>
                                    <td>${grandInterestTotal.toFixed(2)}</td>
                                    <td>${grandTotal.toFixed(2)}</td>
                                </tr>`;

                                tbody.append(grandTotalRow);


                                $('#grandTotal').text(grandTotal.toFixed(2));
                                $('#grandInterestTotal').text(grandInterestTotal.toFixed(2));

                                let memberType = $('#memberType').val();
                                let schemeType = $('#schemeType').val();

                                if (memberType != 'All' && schemeType === 'All') {
                                    {{--  addfinancialyearendinterestpayable(grandInterestTotal,memberType,schemeType,endDatesssss);  --}}
                                }

                                $('.spinner-border').css('display', 'none');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            {{--  function addfinancialyearendinterestpayable(grandInterestTotal,memberType,schemeType,endDatesssss){
                $.ajax({
                    url : "{{ route('rdpayableinsert') }}",
                    type : 'post',
                    data : { grandInterestTotal : grandInterestTotal,memberType:memberType,schemeType:schemeType,endDatesssss:endDatesssss},
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function (res) {
                        if(res.status === 'success'){
                            notify(res.messages,'success');
                        }else{
                            notify(res.messages,'warning');
                        }
                    }
                });
            }  --}}


            function calculateRDAmount(principal, rate, months) {
                var rdAmount = principal;
                for (var i = 0; i < months; i++) {
                    var interest = (rdAmount * rate) / (12 * 100);
                    rdAmount += interest;
                }
                return rdAmount;
            }

            function calculateInterest(principal, rate, transactionDate, endDate) {
                var rdAmount = calculateRDAmount(principal, rate, transactionDate, endDate);
                return rdAmount - principal;
            }

            function calculateMonthDifference(transactionDate, endDate) {
                var diff = (endDate.getTime() - transactionDate.getTime()) / 1000;
                diff /= (60 * 60 * 24 * 7 * 4);
                return Math.abs(Math.round(diff));
            }
        });





        {{--  $(document).ready(function() {
            var entriesPerGroup = 10;
            var rdAmountMap = {};
            var interestMap = {};

            function fetchAndDisplayData() {
                var memberType = $('#memberType').val();
                var endDate = $('#endDate').val();
                $.ajax({
                    type: 'post',
                    url: '{{ route('rdReport.getData') }}',
                    data: { memberType: memberType,},
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        $('.bg-secondary-subtle').empty();
                        var grandTotal = 0;
                        var grandInterestTotal = 0;
                        for (var i = 0; i < response.length; i += rdaccount) {
                            var rdaccount = response.slice(i, i + rdaccount);
                            var groupTotal = 0;
                            var groupInterestTotal = 0;
                            var i = 1;
                            rdaccount.forEach(function(row) {
                                var principal = parseFloat(row.total_amount ?? 0);
                                if (parseInt(principal) > 0) {
                                    var rate = parseFloat(row.interest);
                                    var startDate = new Date(row.transactionDate);
                                    var currentDate = new Date(endDate);
                                    var months = calculateMonthDifference(startDate,
                                        currentDate);
                                    if (!isNaN(months) && months >= 0) {
                                        var rdAmount = calculateRDAmount(principal, rate,
                                            months);
                                        var interest = calculateInterest(row.amount, rate,
                                            months);
                                        var totalAmount = principal + interest; //rdAmount;
                                        $('.bg-secondary-subtle').append('<tr>' +
                                            '<td>' + i++ + '</td>' +
                                            '<td>' + row.member_account.accountNo +
                                            '</td>' +
                                            '<td>' + row.member_account.name + '</td>' +
                                            '<td>' + formatDate(row.date) + '</td>' +
                                            '<td>' + principal.toFixed(0) + '</td>' +
                                            '<td>' + interest.toFixed(0) + '</td>' +
                                            '<td>' + totalAmount.toFixed(0) + '</td>' +
                                            '</tr>');
                                        groupTotal += totalAmount;
                                        groupInterestTotal += interest;
                                    }
                                }
                            });
                            $('.bg-secondary-subtle').append('<tr class="bg-success">' +
                                '<td colspan="5">Page ' + (i / entriesPerGroup + 1) + '</td>' +
                                '<td>' + groupInterestTotal.toFixed(2) + '</td>' +
                                '<td>' + groupTotal.toFixed(2) + '</td>' +
                                '</tr>');
                            grandTotal += groupTotal;
                            grandInterestTotal += groupInterestTotal;
                        }
                        $('.bg-secondary-subtle').append('<tr>' +
                            '<td colspan="5"><b>Grand Total</b></td>' +
                            '<td>' + grandInterestTotal.toFixed(2) + '</td>' +
                            '<td>' + grandTotal.toFixed(2) + '</td>' +
                            '</tr>');
                        $('#grandTotal').text(grandTotal.toFixed(2));
                        $('#grandInterestTotal').text(grandInterestTotal.toFixed(2));
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
            $('#endDate').change(function() {
                fetchAndDisplayData();
            });
            $('#viewReportBtn').click(function() {
                fetchAndDisplayData();
            });

            function calculateRDAmount(principal, rate, months) {
                var rdAmount = principal;
                for (var i = 0; i < months; i++) {
                    var interest = (rdAmount * rate) / (12 * 100);
                    rdAmount += interest;
                }
                return rdAmount;
            }

            function calculateInterest(principal, rate, startDate, endDate) {
                var rdAmount = calculateRDAmount(principal, rate, startDate, endDate);
                return rdAmount - principal;
            }

            function calculateMonthDifference(startDate, endDate) {
                var diff = (endDate.getTime() - startDate.getTime()) / 1000;
                diff /= (60 * 60 * 24 * 7 * 4);
                return Math.abs(Math.round(diff));
            }
        });  --}}
    </script>
@endpush
