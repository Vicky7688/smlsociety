@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Bank FD List</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="formData" name="formData">
                        <div class="row">
                            @php
                                $currentDate = date('d-m-Y', strtotime(session('sessionEnd')));
                            @endphp
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="endDate" class="form-label">Date Up To</label>
                                <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="endDate"
                                    name="endDate" value="{{ $currentDate }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="" class="form-label">Banks</label>
                                <select name="bankType" id="bankType" class="form-select formInputsSelectReport">
                                    <option value="All" select>All</option>
                                    @if(!empty($banksMaster))
                                        @foreach ($banksMaster as $row)
                                            <option value="{{ $row->ledgerCode }}">{{ $row->bank_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-lg-5 col-md-12 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom me-2">View</button>
                                    <button type="button" class="btn btn-primary print-button reportSmallBtnCustom me-2" onclick="printReport()"> Print </button>
                                    {{--  <button type="button"
                                         id="printButton" onclick="printReport()"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </button>

                                    <div class="ms-2 dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
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
        <div class="card-body tablee" >
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" >
                    <thead class="table_head verticleAlignCenterReport">
                        <tr>
                            <th class="fw-bold">SR NO</th>
                            <th class="fw-bold">Receipt No</th>
                            <th class="fw-bold">Account No</th>
                            <th class="fw-bold">Bank Name</th>
                            <th class="fw-bold">FD Date</th>
                            <th class="fw-bold">FD Amount</th>
                            <th class="fw-bold">Rate</th>
                            <th class="fw-bold">Interest Recoverable</th>
                            <th class="fw-bold">Total Payable Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="table-border-bottom-0">
                        <tr>
                            <td colspan="9" class="text-center">No data available</td>
                        </tr>
                    </tbody>
                    <tbody id="grandtotal">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="loader" style="display:none; position:fixed; top:60%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
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
    function capitalizeWords(str) {
        return str.toUpperCase();
    }


    function DateFormmate(date){
        let entry_date = new Date(date);
        let day = entry_date.getDate();
        let month = entry_date.getMonth() + 1;
        let year = entry_date.getFullYear();

        day = day < 10 ? `0${day}` : day;
        month = month < 10 ? `0${month}` : month;
        let transcationDate = `${day}-${month}-${year}`;
        return transcationDate;
    }






    function calculateInterestAmount(principal, rate, interestType, duration) {
        principal = parseFloat(principal) || 0;
        rate = parseFloat(rate) || 0;
        interestType = interestType || 'QuarterlyCompounded';

        let years = duration.years || 0;
        let months = duration.months || 0;
        let days = duration.days || 0;

        var totalDays = (years * 365) + (months * 30) + days;
        var totalYears = Math.floor(totalDays / 365);
        var remainingDaysAfterYears = totalDays % 365;
        var fullQuarters = Math.floor(remainingDaysAfterYears / 90);
        var extraDays = remainingDaysAfterYears % 90;

        var maturityAmount = principal;
        var interest = 0;
        var interestRecoverableQuarterly = [];

        if (interestType === 'QuarterlyCompounded') {
            var n = 4; // Quarterly compounding
            var r = rate / 100;

            // **Step 1: Apply compound interest for full years**
            if (totalYears > 0) {
                maturityAmount = principal * Math.pow(1 + (r / n), n * totalYears);
                interest = maturityAmount - principal;
            }

            // **Step 2: Apply compound interest for full quarters**
            var tempAmount = maturityAmount;
            for (var i = 0; i < fullQuarters; i++) {
                var quarterInterest = tempAmount * (r / n);
                tempAmount += quarterInterest;
                interestRecoverableQuarterly.push(Math.round(quarterInterest));
            }
            maturityAmount = tempAmount;
            interest = maturityAmount - principal;

            // **Step 3: Apply simple interest for extra days**
            if (extraDays > 0) {
                var dailyRate = r / 365;
                var extraInterest = maturityAmount * dailyRate * extraDays;
                interest += extraInterest;
                maturityAmount += extraInterest;
            }
        } else if (interestType === 'AnnualCompounded') {
            maturityAmount = principal * Math.pow(1 + (rate / 100), totalDays / 365);
            interest = maturityAmount - principal;
        } else if (interestType === 'Fixed') {
            interest = principal * (rate / 100) * (totalDays / 365);
            maturityAmount += interest;
        }

        return {
            totalInterest: Math.round(interest),
            maturityAmount: Math.round(maturityAmount),
            interestRecoverableQuarterly: interestRecoverableQuarterly
        };
    }



    function calculateDuration(startDateStr, endDateStr) {
        let [startDay, startMonth, startYear] = startDateStr.split('-').map(Number);
        let [endDay, endMonth, endYear] = endDateStr.split('-').map(Number);

        let startDate = new Date(startYear, startMonth - 1, startDay);
        let endDate = new Date(endYear, endMonth - 1, endDay);

        let years = endYear - startYear;
        let months = endMonth - startMonth;
        let days = endDay - startDay;

        if (days < 0) {
            months--;
            let prevMonth = new Date(endYear, endMonth - 1, 0);
            days += prevMonth.getDate();
        }

        if (months < 0) {
            years--;
            months += 12;
        }

        return { years, months, days };
    }




    $(document).on('submit', '#formData', function (event) {
        event.preventDefault();
        let formData = $(this).serialize();
        let endDate = $('#endDate').val(); // Get end date from input field

        $.ajax({
            url: "{{ route('getbankfdsreportdetails') }}",
            type: 'post',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    let bankDetails = res.bankDetails;

                    let principalTotal = 0;
                    let interestTotal = 0;
                    let grandTotal = 0;

                    let tableBody = $("#tableBody");
                    tableBody.empty();

                    $('#grandtotal').empty();

                    if (Array.isArray(bankDetails) && bankDetails.length > 0) {
                        bankDetails.forEach((data, index) => {
                            let duration = calculateDuration(DateFormmate(data.fd_date), endDate);

                            // Fix: Correctly passing required data to the function
                            let interestData = calculateInterestAmount(
                                data.principal_amount,
                                data.interest_rate,
                                data.interest_type,
                                duration
                            );

                            let row = `<tr>
                                <td>${index + 1}</td>
                                <td>${data.fd_no}</td>
                                <td>${data.fd_account}</td>
                                <td>${capitalizeWords(data.bank_name)}</td>
                                <td>${DateFormmate(data.fd_date)}</td>
                                <td>${parseFloat(data.principal_amount).toFixed(2)}</td>
                                <td>${data.interest_rate}</td>
                                <td>${parseFloat(interestData.totalInterest).toFixed(2)}</td>
                                <td>${parseFloat(interestData.maturityAmount).toFixed(2)}</td>
                            </tr>`;

                            principalTotal += parseFloat(data.principal_amount);
                            interestTotal += parseFloat(interestData.totalInterest);
                            grandTotal += parseFloat(interestData.maturityAmount);

                            tableBody.append(row);
                        });

                        $('#grandtotal').append(`
                            <tr>
                                <td colspan="5">Grand Total</td>
                                <td>${parseFloat(principalTotal).toFixed(2)}</td>
                                <td></td>
                                <td>${parseFloat(interestTotal).toFixed(2)}</td>
                                <td>${parseFloat(grandTotal).toFixed(2)}</td>
                            </tr>`
                            );
                    } else {
                        tableBody.append('<tr><td colspan="9" class="text-center">No Record Available</td></tr>');
                    }
                } else {
                    notify('No data found', 'warning');
                }
            },
            error: function (xhr, status, error) {
                notify(error, 'warning');
            }
        });
    });


</script>
@endpush
