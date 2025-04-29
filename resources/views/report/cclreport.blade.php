@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Secured Over Draft(SOD) Report</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        @php
                            $currentDate =
                                Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionEnd')));
                        @endphp
                        <form action="javascript:void(0)" id="cclReportForm" name="cclReportForm">
                            <div class="row d-flex align-items-center">
                                <!-- Date Input -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="" class="form-label">Up To Date</label>
                                    <input type="text" class="form-control  mydatepic valid" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate" value="{{ $currentDate }}" />
                                </div>

                                <!-- Member Type Select -->
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 inputesPaddingReport me-2">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                        <option value="All">All</option>
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
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
                                        <button type="button" class="btn btn-primary print-button reportSmallBtnCustom me-2" onclick="printReport()"> Print </button>

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
                                <th class="fw-bold">CCL Amount</th>
                                <th class="fw-bold">Interest Recoverable</th>
                                <th class="fw-bold">Total Recoverable Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="bg-secondary-subtle" style="background-color: white !important;">
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                        <tbody id="totalbody" class="bg-secondary-subtle" style="background-color: white !important;">
                            <tr style="background-color: #7367f0;">
                                <td colspan="3" style="color: white;">Grand Total</td>
                                <td style="color: white;">0.00</td>
                                <td style="color: white;">0.00</td>
                                <td style="color: white;">0.00</td>
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
        $(document).ready(function() {
            $(document).on('submit','#cclReportForm',function(event){
                event.preventDefault();

                let endDate = $('#endDate').val();
                let memberType = $('#memberType').val();

                $.ajax({
                    url : "{{ route('getdataccllist') }}",
                    type : 'post',
                    data : {endDate : endDate, memberType : memberType},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if (res.status === 'success') {
                            let allDatas = res.allDatas;
                            let tableBody = $('#tbody');
                            tableBody.empty();

                            let grandTotalCclAmount = 0;
                            let grandInterestRecoverables = 0;
                            let grandCclAmountRecoverables = 0;

                            if (Array.isArray(allDatas) && allDatas.length > 0) {
                                let displayIndex = 1; // For correctly numbering only shown rows

                                allDatas.forEach((data) => {
                                    const withdrawAmount = parseFloat(data.transfer_amount) || 0;
                                    const recoveryAmount = parseFloat(data.recovey_amount) || 0;
                                    const rateOfInterest = parseFloat(data.interest) || 0;
                                    const dayDifference = parseFloat(data.day_difference) || 0;

                                    const balances = withdrawAmount - recoveryAmount;
                                    const interestRecoverables = (((balances * rateOfInterest) / 100) / 365) * dayDifference;
                                    const netAmount = balances + interestRecoverables;

                                    if (balances > 0) {
                                        const row = `
                                            <tr>
                                                <td>${displayIndex}</td>
                                                <td>${data.cclNo || '-'}</td>
                                                <td>${data.mname || '-'}</td>
                                                <td>${balances.toFixed(2)}</td>
                                                <td>${interestRecoverables.toFixed(2)}</td>
                                                <td>${netAmount.toFixed(2)}</td>
                                            </tr>`;
                                        tableBody.append(row);

                                        grandTotalCclAmount += balances;
                                        grandInterestRecoverables += interestRecoverables;
                                        grandCclAmountRecoverables += netAmount;

                                        displayIndex++; // Increment only if data is shown
                                    }
                                });

                                $('#totalbody').empty().append(`
                                    <tr style="background-color: #7367f0;">
                                        <td colspan="3" style="color: white;">Grand Total</td>
                                        <td style="color: white;">${grandTotalCclAmount.toFixed(2)}</td>
                                        <td style="color: white;">${grandInterestRecoverables.toFixed(2)}</td>
                                        <td style="color: white;">${grandCclAmountRecoverables.toFixed(2)}</td>
                                    </tr>
                                `);
                            } else {
                                tableBody.append(`<tr><td colspan="7" class="text-center">No data available</td></tr>`);
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }

                    }
                });
            });
        });
    </script>
@endpush
