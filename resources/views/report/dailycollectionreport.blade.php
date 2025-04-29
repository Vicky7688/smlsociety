@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Daily Deposit Saving Report</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        @php
                            /*$currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionEnd')));*/
                            $currentDate = date('d-m-Y', strtotime(session('sessionEnd')));

                        @endphp
                        <form action="javascript:void(0)" id="ddsReportForm" name="ddsReportForm">
                            <div class="row d-flex align-items-center">
                                <!-- Date Input -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="endDate" class="form-label">Up To Date</label>
                                    <input type="text" class="form-control form-control-sm"
                                        placeholder="YYYY-MM-DD" id="endDate" name="endDate"
                                        value="{{ $currentDate }}" />
                                </div>

                                <!-- Member Type Select -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelectReport" id="memberType" name="memberType" onchange="getSchemesss('this')">
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
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom me-2">View</button>
                                    {{-- <a type="button" href="{{ route('rdPrint.print') }}" target="_blank"
                                        class="btn btn-primary print-button reportSmallBtnCustom me-2">Print</a> --}}
                                    <button type="button" class="btn btn-primary print-button reportSmallBtnCustom me-2" onclick="printReport()"> Print </button>

                                    <!-- More Button Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                            type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                            aria-expanded="false"> More </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="#" onClick="downloadPDF()"><i
                                                        class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onClick="downloadWord()"><i
                                                        class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
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
                                <th class="fw-bold">Daily Saving Date</th>
                                <th class="fw-bold">Daily Saving Amount</th>
                                <th class="fw-bold">Payable Int</th>
                                <th class="fw-bold">Total Payable Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="bg-secondary-subtle" style="background-color: white !important;">
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                        <tbody id="grandTotalbody" class="bg-secondary-subtle" style="background-color: #7367f0 !important;">
                            <tr>
                                <td id="grandTotalbd268" colspan="4" style="color:white;">Grand Total</td>
                                <td id="ddsTotal" style="color:white;">0</td>
                                <td id="interestPayable" style="color:white;">0</td>
                                <td id="netgrandTotal" style="color:white;">0</td>
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
        function getSchemesss(){
            let memberType = $('#memberType').val();

            $.ajax({
                url : "{{ route('dailysavingrepostscheme') }}",
                type : 'post',
                data : {memberType : memberType},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let schemes = res.schemes;
                        let schemeTypeDropdown = $('#schemeType');
                        schemeTypeDropdown.empty();


                       if(schemes && schemes.length > 0){
                            schemeTypeDropdown.append(`<option value="All">All</option>`);

                            schemes.forEach((data) => {
                                schemeTypeDropdown.append(`<option value="${data.id}">${data.name}</option>`);
                            });
                        }else{
                            schemes.forEach((data) => {
                                schemeTypeDropdown.append(`<option value=""></option>`);
                            });
                        }

                    }else{
                        toastr.error(res.messages);
                    }
                }
            });
        }

        $(document).ready(function(){
            $(document).on('submit','#ddsReportForm',function(event){
                event.preventDefault();

                let formData = $(this).serialize();
                $.ajax({
                    url : "{{ route('getddsDetails') }}",
                    type : 'post',
                    data : formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){
                            let dailyaccounts = res.dailyaccounts;
                            let tbody = $('#tbody');
                            tbody.empty();


                            let DdsGrandTotal = 0;
                            let InttPayableTotal = 0;
                            let NetGrandTotal = 0;

                            if(dailyaccounts && dailyaccounts.length > 0){

                                let dates = $('#endDate').val();

                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);


                                dailyaccounts.forEach((data,index) => {
                                    let openingDate = new Date(data.opening_date);
                                    let days = openingDate.getDate();
                                    let month = openingDate.getMonth() + 1;
                                    let year = openingDate.getFullYear();

                                    days = days < 10 ? `0${days}` : days;
                                    month = month < 10 ? `0${month}` : month;
                                    let formattedDate = `${days}-${month}-${year}`;

                                    let ss =  parseFloat(data.total_amount || 0);
                                    let ww =  parseFloat(data.withdraw || 0);
                                    let principal = parseFloat(ss) - parseFloat(ww);
                                    let rate = parseFloat(data.interest);
                                    let months = calculateMonthDifference(openingDate, endDate);
                                    let rdAmount = calculateRDAmount(principal, rate, months);
                                    let interest = Math.round(calculateInterest(principal, rate, months));

                                    // Maturity calculations
                                    let totalInterest = 0;
                                    const quarterlyRate = rate / 4 / 100;
                                    const daysInQuarter = 91;
                                    const totalDays = months * 30.44;
                                    const completedQuarters = Math.floor(totalDays / daysInQuarter);
                                    const remainingDays = totalDays % daysInQuarter;

                                    let maturityAmount = principal;

                                    // Calculate interest for completed quarters
                                    for (let i = 0; i < completedQuarters; i++) {
                                        const quarterlyInterest = maturityAmount * quarterlyRate;
                                        totalInterest += quarterlyInterest;
                                        maturityAmount += quarterlyInterest;
                                    }

                                    // Calculate interest for remaining days (partial quarter)
                                    if (remainingDays > 0) {
                                        const dailyRate = quarterlyRate / daysInQuarter;
                                        const dailyInterest = maturityAmount * dailyRate * remainingDays;
                                        totalInterest += dailyInterest;
                                        maturityAmount += dailyInterest;
                                    }

                                    if(principal > 0 ||  totalInterest > 0 || maturityAmount > 0){
                                        let row = `<tr>
                                            <td>${index+1}</td>
                                            <td>${data.account_no}</td>
                                            <td>${data.name}</td>
                                            <td>${formattedDate}</td>
                                            <td>${parseFloat(principal).toFixed(2)}</td>
                                            <td>${Math.round(totalInterest).toFixed(2)}</td>
                                            <td>${Math.round(maturityAmount).toFixed(2)}</td>
                                        </tr>`;

                                        DdsGrandTotal += principal;
                                        InttPayableTotal += totalInterest;
                                        NetGrandTotal += maturityAmount;

                                        tbody.append(row);
                                    }





                                });

                                $('#ddsTotal').text(DdsGrandTotal.toFixed(2));
                                $('#interestPayable').text(Math.round(InttPayableTotal).toFixed(2));
                                $('#netgrandTotal').text(NetGrandTotal.toFixed(2));


                                let memberType = $('#memberType').val();
                                let schemeType = $('#schemeType').val();


                                if(memberType != 'All' && schemeType === 'All'){
                                    {{--  addfinancialyearendinterestpayable(InttPayableTotal,memberType,schemeType,dates);  --}}
                                }

                                $('.spinner-border').css('display','none');
                            }

                        }else{
                            notify(res.messages);
                        }
                    }
                });
            });
        });

        {{--  function addfinancialyearendinterestpayable(InttPayableTotal,memberType,schemeType,dates) {
            $.ajax({
                url: "{{ route('dailypayableinsert') }}",
                type: 'post',
                data: {
                    InttPayableTotal: InttPayableTotal,
                    memberType : memberType,
                    schemeType : schemeType,
                    dates:dates
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






        function calculateRDAmount(principal, rate, months) {
            var rdAmount = principal;
            for (var i = 0; i < months; i++) {
                var interest = (rdAmount * rate) / (12 * 100);
                rdAmount += interest;
            }
            return rdAmount;
        }

        function calculateInterest(principal, rate, openingDate, endDate) {
            var rdAmount = calculateRDAmount(principal, rate, openingDate, endDate);
            return rdAmount - principal;
        }

        function calculateMonthDifference(openingDate, endDate) {
            var diff = (endDate.getTime() - openingDate.getTime()) / 1000;
            diff /= (60 * 60 * 24 * 7 * 4);
            return Math.abs(Math.round(diff));
        }




    </script>
@endpush
