@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- <h4 class="py-2"><span class="text-muted fw-light">Reports / General Reports /</span> Fixed Deposit Report</h4> -->
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span> Fixed Deposit Report</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="fdformData" name="fdformData">
                            <div class="row">
                                @php
                                    $currentDate = date('d-m-Y', strtotime(session('sessionEnd')));
                                @endphp

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="endDate" class="form-label">Date As On</label>
                                    <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate" value="{{ $currentDate }}" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>


                                {{-- <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="depositType" class="form-label">Type of Deposit</label>
                                <select class="form-select formInputsSelectReport" id="depositType" name="groupCode">
                                    <option value="All" selected>All</option>
                                    @foreach ($FdTypeMaster as $item)
                                    <option value="{{ $item->id }}">{{ $item->type }}</option>
                                  @endforeach
                                </select>
                            </div> --}}
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="depositType" class="form-label">Type of Deposit</label>
                                    <select class="form-select formInputsSelectReport" id="depositType" name="groupCode" onchange="getfdschemese(this)">
                                        <option value="All" selected default>All</option>
                                        @foreach ($FdTypeMaster as $item)
                                            <option value="{{ $item->id }}">{{ $item->type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="schemeType" class="form-label">Scheme Type</label>
                                    <select class="form-select formInputsSelectReport" id="schemeType" name="schemeType">
                                        <option value="All" selected>All</option>
                                        <!-- Options will be dynamically populated -->
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-8 col-12 py-2 inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button type="submit" id="viewReportBtn"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                        </button>
                                        <!--<a type="button" href="{{ route('fdPrint.print', ['id' => '1']) }}" target="_blank"-->
                                        <!--    class="ms-2 btn btn-primary print-button reportSmallBtnCustom">-->
                                        <!--    Print-->
                                        <!--</a>-->
                                        <button type="button" id="printButton" onclick="printReport()"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </button>
                                        {{--  <div class="ms-2 dropdown">
                                            <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                More
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i
                                                            class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i
                                                            class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="share()"><i
                                                            class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                            </ul>
                                        </div>  --}}
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
                                <th class="fw-bold">Sr. No</th>
                                <th class="fw-bold">Membership</th>
                                <th class="fw-bold">FD No</th>
                                <th class="fw-bold">Name</th>
                                <th class="fw-bold">FD Dated</th>
                                <th class="fw-bold">FD Amount</th>
                                <th class="fw-bold">Rate</th>
                                <th class="fw-bold">Interest Payable</th>
                                <th class="fw-bold">Total Payable Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-secondary-subtle" style="background-color: white !important;" id="tbody">
                            <tr>
                                <td colspan="9" class="text-center">No data available</td>
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
        {{--  function getfdschemese(ele){
            let fdtype = $(ele).val();
            let endDate = $('#endDate').val();
            let memberType = $('#memberType').val();

            $.ajax({
                url : "{{ route('getfdallschemes') }}",
                type : 'post',
                data : {fdtype : fdtype,endDate: endDate , memberType: memberType},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let schemes = res.schemes;

                        let schemeType = $('#schemeType');
                        schemeType.empty();

                        if(Array.isArray(schemes) && schemes.length > 0){
                            schemeType.append(`<option value="All">All</option>`);
                            schemes.forEach((data) => {
                                schemeType.append(`<option value="${data.id}">${data.name}</option>`);
                            });
                        }else{
                            schemeType.append(`<option value="">No Scheme</option>`);
                        }
                    }else{
                        notify(res.messages,'warning');
                    }
                },error : function(xhr,error,status){
                    alert('Ajax Not Working');
                }
            });
        }


        $(document).ready(function () {
            $(document).on('submit', '#fdformData', function (event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('getfdreportdata') }}",
                    type: 'post',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'Success') {
                            let allData = res.allData;
                            let tbody = $('#tbody');
                            tbody.empty();

                            if (Array.isArray(allData) && allData.length > 0) {
                                let pageTotalPrincipal = 0;
                                let interestAmount = 0;

                                allData.forEach((data, index) => {
                                    let principal = parseFloat(data.principalAmount) || 0;
                                    let totalAmount = principal;

                                    // Ensure valid date parsing from data.openingDate
                                    // Assuming dateFormat function is correctly implemented to return a Date object
let currentDateParts = $('#endDate').val();
let currentDate = dateFormat(currentDateParts); // Returns a Date object

let openingDateParts = data.openingDate.split('-').map(Number);
let openingDate = dateFormat(data.openingDate); // Returns a Date object

// Calculate days elapsed between dates
let daysElapsed = Math.round((currentDate - openingDate) / (1000 * 60 * 60 * 24)); // Convert to days
console.log(currentDate, openingDate, daysElapsed);

// Initialize interest and totalAmount
let interest = 0;

if (daysElapsed > 0) {
    // Calculate the interest based on days elapsed
    interest = calculateTotalInterest(data.interestType, principal, data.interestRate, daysElapsed);
    totalAmount += interest; // Add the interest to the total amount
}

// Log the final total amount
console.log('Total Amount:', totalAmount);



                                    interestAmount = interest.toFixed(2);

                                    let row = `<tr>
                                        <td>${index + 1}</td>
                                        <td>${data.membershipno || '-'}</td>
                                        <td>${data.accountNo || '-'}</td>
                                        <td>${data.name}</td>
                                        <td>${dateFormat(data.openingDate)}</td>
                                        <td>${data.principalAmount || 0}</td>
                                        <td>${data.interestRate || 0}</td>
                                        <td>${interestAmount}</td>
                                        <td>${totalAmount.toFixed(2)}</td>
                                    </tr>`;
                                    tbody.append(row);
                                });
                            } else {
                                notify(res.messages, 'warning');
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(error + ' Ajax Not Working');
                    }


                });
            });
        });

        function calculateTotalInterest(interestType, principal, rate, daysElapsed) {

            let totalInterest = 0;
            if (interestType === 'Fixed') {
                totalInterest = (principal * rate / 100) * (daysElapsed / 365);

            } else if (interestType === 'AnnualCompounded') {
                totalInterest = principal * (Math.pow(1 + rate / 100, daysElapsed / 365) - 1);
            } else if (interestType === 'QuarterlyCompounded') {
                const quarterlyRate = rate / 4 / 100;
                const completedQuarters = Math.floor(daysElapsed / 91);
                let maturityAmount = principal;



                for (let i = 0; i < completedQuarters; i++) {
                    const quarterlyInterest = maturityAmount * quarterlyRate;
                    totalInterest += quarterlyInterest;
                    maturityAmount += quarterlyInterest;
                }

                const remainingDays = daysElapsed % 91;
                if (remainingDays > 0) {
                    const dailyRate = quarterlyRate / 91;
                    totalInterest += maturityAmount * dailyRate * remainingDays;
                }
            }
            return Math.round(totalInterest);
        }

        function capitalizeWords(str) {
            return str.replace(/\b\w/g, char => char.toUpperCase());
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
    }  --}}










 function getfdschemese(ele){
            let fdtype = $(ele).val();
            let endDate = $('#endDate').val();
            let memberType = $('#memberType').val();

            $.ajax({
                url : "{{ route('getfdallschemes') }}",
                type : 'post',
                data : {fdtype : fdtype,endDate: endDate , memberType: memberType},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let schemes = res.schemes;

                        let schemeType = $('#schemeType');
                        schemeType.empty();

                        if(Array.isArray(schemes) && schemes.length > 0){
                            schemeType.append(`<option value="All">All</option>`);
                            schemes.forEach((data) => {
                                schemeType.append(`<option value="${data.id}">${data.name}</option>`);
                            });
                        }else{
                            schemeType.append(`<option value="">No Scheme</option>`);
                        }
                    }else{
                        notify(res.messages,'warning');
                    }
                },error : function(xhr,error,status){
                    alert('Ajax Not Working');
                }
            });
        }
















        function reverseDate(endDate) {
            var dateParts = endDate.split('-'); // Split by '-'
            var endDateFormatted = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            return new Date(endDateFormatted);
        };
        //_________________________________calculatoin starts here
        //_________________________________calculatoin starts here



        $(document).ready(function() {
            var entriesPerGroup = 20;

            // Function to fetch and display data
            function fetchAndDisplayData() {
                var memberType = $('#memberType').val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var depositType = $('#depositType').val();
                var schemeType = $('#schemeType').val();
                $('.spinner-border').css('display','inline-block');


                $.ajax({
                    type: 'GET',
                    url: '{{ route('fdReport.getData') }}',
                    data: {
                        memberType: memberType,
                        startDate: startDate,
                        endDate: endDate,
                        depositType: depositType,
                        schemeType: schemeType
                    },
                    success: function(response) {
                        $('.bg-secondary-subtle').empty();
                        var grandTotal = 0;
                        var grandInterestTotal = 0;
                        var srNo = 1;
                        var TotalPrincipal = 0;

                        for (var i = 0; i < response.length; i += entriesPerGroup) {
                            var groupData = response.slice(i, i + entriesPerGroup);
                            var groupTotal = 0;
                            var groupInterestTotal = 0;
                            var pageTotalPrincipal = 0;

                            groupData.forEach(function(row) {
                                var principal = parseFloat(row.principalAmount);
                                var totalAmount = principal;
                                pageTotalPrincipal += principal;

                                var [day, month, year] = endDate.split('-').map(Number);
                                var currentDate = new Date(year, month - 1, day);
                                var openingDate = new Date(row.openingDate);

                                // Calculate days elapsed
                                var daysElapsed = Math.round((currentDate - openingDate) / (
                                    1000 * 60 * 60 * 24));

                                // Calculate the total interest for the daysElapsed
                                var interest = 0;
                                if (daysElapsed > 0) {
                                    interest = calculateTotalInterest(row.interestType,
                                        principal, row.interestRate, daysElapsed);
                                    totalAmount += interest;
                                }
                                row.interestAmount = interest.toFixed(
                                2); // Set the interest amount

                                // Append each row to the table
                                $('.bg-secondary-subtle').append('<tr>' +
                                    '<td>' + srNo++ + '</td>' +
                                    '<td>' + row.membershipno + '</td>' +
                                    '<td>' + row.fdNo + '</td>' +
                                    '<td>' + capitalizeWords(row.name) +
                                    '</td>' +
                                    '<td>' + formatDate(row.openingDate) + '</td>' +
                                    '<td>' + principal.toFixed(2) + '</td>' +
                                    '<td>' + row.interestRate + '</td>' +
                                    '<td>' + row.interestAmount + '</td>' +
                                    '<td>' + totalAmount.toFixed(2) + '</td>' +
                                    '</tr>');
                                groupTotal += totalAmount;
                                groupInterestTotal += parseFloat(row.interestAmount);
                            });

                            // Append group totals to the table
                            $('.bg-secondary-subtle').append('<tr class="bg-success">' +
                                '<td colspan="5" class="text-white">Page ' + (i / entriesPerGroup +
                                    1) + '</td>' +
                                '<td class="text-white">' + pageTotalPrincipal.toFixed(2) +
                                '</td>' +
                                '<td class="text-white"></td>' +
                                '<td class="text-white">' + groupInterestTotal.toFixed(2) +
                                '</td>' +
                                '<td class="text-white">' + groupTotal.toFixed(2) + '</td>' +
                                '</tr>');
                            grandTotal += groupTotal;
                            grandInterestTotal += groupInterestTotal;
                            TotalPrincipal += pageTotalPrincipal;
                        }

                        // Append grand totals to the table
                        $('.bg-secondary-subtle').append('<tr>' +
                            '<td colspan="5"><strong>Grand Total</strong></td>' +
                            '<td>' + TotalPrincipal.toFixed(2) + '</td>' +
                            '<td></td>' +
                            '<td>' + grandInterestTotal.toFixed(2) + '</td>' +
                            '<td>' + grandTotal.toFixed(2) + '</td>' +
                            '</tr>');

                        // Update grand total display
                        $('#grandTotal').text(grandTotal.toFixed(2));
                        $('#grandInterestTotal').text(grandInterestTotal.toFixed(2));

                        let memberType = $('#memberType').val();
                        let schemeType = $('#schemeType').val();

                        if(memberType != 'All' && depositType !== 'All' && schemeType === 'All'){
                            {{--  addfinancialyearendinterestpayable(grandInterestTotal,memberType,schemeType,endDate,depositType);  --}}
                        }

                        $('.spinner-border').css('display','none');



                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }



            {{--  function addfinancialyearendinterestpayable(grandInterestTotal,memberType,schemeType,endDate,depositType) {
                $.ajax({
                    url: "{{ route('fdpayableinsert') }}",
                    type: 'post',
                    data: {
                        grandInterestTotal: grandInterestTotal,
                        memberType : memberType,
                        schemeType : schemeType,
                        endDate : endDate,
                        depositType : depositType
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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




            $('#viewReportBtn').click(function() {
                fetchAndDisplayData();
            });

            $('#endDate').on('change', function() {
                var endDateValue = $(this).val();
                checkDateSession(endDateValue);
                fetchAndDisplayData();
            });

            function checkDateSession(date) {
                const todayDate = new Date();
                const formattedToday = formatDateToDMY(todayDate);
                var dateRegex = /^\d{2}-\d{2}-\d{4}$/;

                if (!dateRegex.test(date)) {
                    notify("Invalid date format. Please use DD-MM-YYYY format.", 'warning');
                    $('#endDate').val(formattedToday);
                    return;
                }
            }

            function formatDateToDMY(date) {
                const day = ("0" + date.getDate()).slice(-2);
                const month = ("0" + (date.getMonth() + 1)).slice(-2); // Months are 0-based, so add 1
                const year = date.getFullYear();
                return `${day}-${month}-${year}`;
            }
        });

        // Function to calculate total daily interest
        function calculateTotalInterest(interestType, principal, rate, daysElapsed) {
            let totalInterest = 0;
            if (interestType === 'Fixed') {
                totalInterest = (principal * rate / 100) * (daysElapsed / 365);
            } else if (interestType === 'AnnualCompounded') {
                totalInterest = principal * (Math.pow(1 + rate / 100, daysElapsed / 365) - 1);
            } else if (interestType === 'QuarterlyCompounded') {
                const quarterlyRate = rate / 4 / 100;
                const completedQuarters = Math.round(daysElapsed / 91);
                let maturityAmount = principal;
                for (let i = 0; i < completedQuarters; i++) {
                    const quarterlyInterest = maturityAmount * quarterlyRate;
                    totalInterest += quarterlyInterest;
                    maturityAmount += quarterlyInterest;
                }
                if (completedQuarters == 0) {
                    const remainingDays = daysElapsed % 91;
                    if (remainingDays > 0) {
                        const dailyRate = quarterlyRate / 91;
                        totalInterest += maturityAmount * dailyRate * remainingDays;
                    }
                }
            }
            return Math.round(totalInterest);
        }
        //______________________________________ calculation end here
        //______________________________________ calculation end here







        // Function to calculate maturity date based on opening date and duration
        function calculateMaturityDate(openingDate, years, months, days) {
            var maturityDate = new Date(openingDate);
            maturityDate.setFullYear(maturityDate.getFullYear() + parseInt(years, 10));
            maturityDate.setMonth(maturityDate.getMonth() + parseInt(months, 10));
            maturityDate.setDate(maturityDate.getDate() + parseInt(days, 10));
            return maturityDate;
        }


        function capitalizeWords(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            var fromDateInput = document.getElementById("startDate");
            var currentDate = new Date();

            currentDate.setFullYear(currentDate.getFullYear() - 1);
            currentDate.setMonth(4 - 1);
            currentDate.setDate(1);


        });


        function printReport() {
            var printContents = document.getElementById('sharelistprint').innerHTML;
            var originalContents = document.body.innerHTML;

            var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

            // Add header for printing
            var header = `
        <div style="text-align: center;">
            <h4>{{ $branch->name }}</h4>
            <h6>{{ $branch->address }}</h6>
            <h6>{{ Carbon\Carbon::parse(request('start_date'))->format('d-M-Y') }} To {{ Carbon\Carbon::parse(request('end_date'))->format('d-M-Y') }}</h6>
            <h6>FD List </h6>
        </div>
    `;
            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }

        // Example parameters
        //dependent dropdown for fd type
        const openingAccounts = @json($OpeningAccounts);

        document.addEventListener('DOMContentLoaded', function() {
            initializeSchemeFilter();
            const depositTypeSelect = document.getElementById('depositType');
            const schemeTypeSelect = document.getElementById('schemeType');

            {{--  updateSchemeOptions(depositTypeSelect, schemeTypeSelect);  --}}
        });

        function initializeSchemeFilter() {
            const depositTypeSelect = document.getElementById('depositType');
            const schemeTypeSelect = document.getElementById('schemeType');

            depositTypeSelect.addEventListener('change', function() {
                {{--  updateSchemeOptions(this, schemeTypeSelect);  --}}
            });
        }

        {{--  function updateSchemeOptions(depositTypeSelect, schemeTypeSelect) {

            schemeTypeSelect.innerHTML = '<option value="All">All</option>';
            const selectedTypeId = depositTypeSelect.value;
            const addedSchemes = new Set();

            openingAccounts
                .filter(account => account.fdtypeid == selectedTypeId || selectedTypeId === 'All')
                .forEach(account => {
                    if (!addedSchemes.has(account.schemetype)) {
                        const option = document.createElement('option');
                        option.value = account.schemetype;
                        option.textContent = account.scheme_name;
                        schemeTypeSelect.appendChild(option);

                        addedSchemes.add(account.schemetype);
                    }
                });
        }  --}}
    </script>
@endpush
