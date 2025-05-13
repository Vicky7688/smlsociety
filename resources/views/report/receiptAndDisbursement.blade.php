@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Receipt & Disbursement</h4>
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
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    {{-- <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="startDate" name="startDate" value="{{ now()->firstOfMonth()->format('Y-m-d') }}" /> --}}
                                    @php
                                        $currentDate =
                                            Session::get('currentdate') ??
                                            date('d-m-Y', strtotime(session('sessionStart')));
                                    @endphp
                                    <input type="text" class="form-control formInputs mydatepic" placeholder="DD-MM-YYYY"
                                        id="transactionDate" name="transactionDate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionStart'))) }}" />

                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="endDate" class="form-label">End Date</label>
                                    {{-- <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="endDate" name="endDate" value="{{ now()->format('Y-m-d') }}" /> --}}

                                    <input type="text" class="form-control formInputs mydatepic" id="endDate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" name="endDate" />

                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="reportType" class="form-label">Report Type</label>
                                    <select class="form-select formInputsSelectReport" id="reportType" name="reportType">
                                        <option value="group">Group Wise</option>
                                        <option value="ledger">Ledger Wise</option>
                                    </select>
                                </div>
                                <div
                                    class="col-lg-5 col-md-12 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                        <button type="button"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom"
                                            onclick="printReport()">
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
                                <th class="fw-bold">SR NO</th>
                                <th class="fw-bold">Group/Ledger Name</th>
                                <th class="fw-bold">Debit Amount</th>
                                <th class="fw-bold">Credit Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="table-border-bottom-0">
                            <!-- <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr> -->

                        </tbody>
                        <tbody id="openingCash">
                            <tr>
                                <td></td>
                                <td><b>Opening Cash</b></td>
                                <td></td>
                                <td id="openingCash"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Closing Cash</b></td>
                                <td id="closingCash"></td>
                                <td></td>
                            </tr>

                        </tbody>
                        <tbody id="closingCash">

                        </tbody>
                        <tbody id="grandTotalbody">
                            <tr>
                                <td></td>
                                <td><b>Grand Total</b></td>
                                <td id="drGrandTotal"></td>
                                <td id="crGrandTotal"></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .tablee table th,
        .tablee table td {
            padding: 8px !important;
            text-align: center !important;
        }

        .dataTable {
            border: 1px solid #ddd !important;
        }

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
        }

        .page_headings h4 {
            margin-bottom: 0;
        }
    </style>
@endpush
@push('script')
    <script>
        function printReport() {
            $('.table').css('border', '1px solid');

            var printContents = document.getElementById('sharelistprint').innerHTML;
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            var css = `
                <style>
                    @media print {
                        body { background-color: #ffffff; margin-top: .5rem; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                    }
                </style>`;

            var header = `
                <div style="text-align: center; margin-bottom: 1rem;">

                    <h6>General Ledger from ${startDate} to ${endDate}</h6>
                </div>`;

            var newWindow = window.open('', '_blank');
            newWindow.document.write(css + header + printContents);
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            newWindow.close();
        }



        function capitalizeWords(str) {
            if (!str || typeof str !== 'string') {
                return ''; // Return an empty string if str is undefined, null, or not a string
            }
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        $(document).ready(function() {
            $(document).on('submit', '#formData', function(event) {
                event.preventDefault();

                let transactionDate = $('#transactionDate').val();
                let endDate = $('#endDate').val();
                let reportType = $('#reportType').val();

                $.ajax({
                    url: "{{ route('getdatareceiptanddisbursement') }}",
                    type: 'post',
                    data: {
                        transactionDate: transactionDate,
                        endDate: endDate,
                        reportType: reportType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let openingCash = parseFloat(res.openingCash) || 0;
                            let closingCash = parseFloat(res.closingCash) || 0;
                            let type = res.type;
                            let grandTotalDebit = 0;
                            let grandTotalCredit = 0;

                            $('#openingCash').empty();
                            $('#closingCash').empty();
                            $('#grandTotalbody').empty();
                            $('#tableBody').empty();

                            // Display Opening and Closing Cash
                            // if (openingCash > 0 || closingCash > 0) {
                                $('#openingCash').append(`
                                    <tr><td></td><td><strong>Opening Cash</strong></td><td>0.00</td><td>${openingCash.toFixed(2)}</td></tr>
                                `);
                                $('#closingCash').append(`
                                    <tr><td></td><td><strong>Closing Cash</strong></td><td>${closingCash.toFixed(2)}</td><td>0.00</td></tr>
                                `);
                                grandTotalDebit += closingCash;
                                grandTotalCredit += openingCash;
                            // } else {
                            //     $('#openingCash').append(
                            //         `<tr><td colspan="4">No Opening Cash Available</td></tr>`
                            //     );
                            //     $('#closingCash').append(
                            //         `<tr><td colspan="4">No Closing Cash Available</td></tr>`
                            //     );
                            // }

                            if (Array.isArray(type) && type.length > 0) {
                                type.forEach((data, index) => {
                                    let headName = data.headName || data.name || 'N/A';
                                    let totalDebit = parseFloat(data.total_debit) || 0;
                                    let totalCredit = parseFloat(data.total_credit) || 0;

                                    let row = `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${capitalizeWords(headName)}</td>
                                            <td>${totalDebit.toFixed(2)}</td>
                                            <td>${totalCredit.toFixed(2)}</td>
                                        </tr>`;
                                    $('#tableBody').append(row);

                                    grandTotalDebit += totalDebit;
                                    grandTotalCredit += totalCredit;
                                });
                            } else {
                                $('#tableBody').append(
                                    `<tr><td colspan="4">No transactions found</td></tr>`);
                            }

                            // Append Grand Totals
                            $('#grandTotalbody').append(`
                                <tr style="background-color:#7367f0;">
                                    <td style="color:white;"></td>
                                    <td style="color:white;"><strong>Grand Total</strong></td>
                                    <td style="color:white;">${grandTotalDebit.toFixed(2)}</td>
                                    <td style="color:white;">${grandTotalCredit.toFixed(2)}</td>
                                </tr>
                            `);

                        } else {
                            notify(res.messages, 'warning');
                        }
                    },

                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            });
        });
    </script>
@endpush
