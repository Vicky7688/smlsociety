@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Cash Book</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('printPdf') }}" target="_blank" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="DATEFROM" class="form-label">DATE FROM</label>
                                    <input type="text" class="form-control formInputsReport transactionDate"
                                        placeholder="YYYY-MM-DD" id="start_date" name="start_date"
                                        value="{{ Session::get('currentdate') }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="DATETO" class="form-label">DATE TO</label>
                                    <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                        id="end_date" name="end_date" value="{{ Session::get('currentdate') }}" />
                                </div>
                                <div
                                    class="col-lg-7 col-md-4 col-12  py-2 saving_column inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                            id="viewdatabooksdetails"> View </button>
                                        <button type="button" class="ms-2 btn btn-primary print-button"
                                            onclick="printReport()"> Print </button>
                                        {{--  <button type="submit" class="ms-2 btn btn-primary print-button"> Print </button>
                                        <!-- href="{{ route('dayBookPrint.print') }}" target="_blank" reportSmallBtnCustom -->
                                        <div class="ms-2 dropdown">
                                            <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-expanded="false"> More </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li> <a class="dropdown-item" href="#" onClick="downloadPDF()"><i
                                                            class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onClick="downloadWord()"><i
                                                            class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onClick="share()"><i
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
        <div class="col-12">
            <div class="row" id="sharelistprint">
                <div class="col-md-6 col-sm-12 cards">
                    <div class="card">
                        <div class="card-body tablee">
                            <div class="nav nav-tabs mb-4">
                                <h4 class="t-heading ps-4">RECEIPT</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table text-center table-bordered" id="excelTable">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">V.No</th>
                                            <th scope="col">A/c</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="debittbody">
                                        <tr>
                                            <td colspan="6" class="text-center">No data available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 cards">
                    <div class="card">
                        <div class="card-body tablee">
                            <div class="nav nav-tabs mb-4">
                                <h4 class="t-heading ps-4">PAYMENT</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table text-center table-bordered" id="excelTable">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">V.No</th>
                                            <th scope="col">A/C</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Bank</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="credittbody">
                                        <tr>
                                            <td colspan="6" class="text-center">No data available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
            </style>`;

            let header = `
            <div style="text-align: center; margin-bottom: 10px;">

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
            $("#viewdatabooksdetails").click(function(e) {
                e.preventDefault();
                let startDate = $("#start_date").val();
                let endDate = $("#end_date").val();
                let branch = $("#branch_id").val();

                $.ajax({
                    url: "{{ route('getcashdata') }}",
                    type: 'post',
                    data: {
                        startdate: startDate,
                        enddate: endDate,
                        branch: branch
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let debit_entries = res.debit_entries;
                            let credit_entries = res.credit_entries;
                            let closing_cash = res.closing_cash;
                            let opening_cash = res.opening_balance;
                            showdata(debit_entries, credit_entries, closing_cash, opening_cash);
                            {{--  gogo();  --}}
                        }
                    }
                });
            });
        });

        function capitalizeWords(str) {
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }


        function showdata(debit_entries, credit_entries, closing_cash, opening_cash) {
            let debitbody = $('#debittbody');
            let creditbody = $('#credittbody');
            debitbody.empty();
            creditbody.empty();

            let ledger_name = null;
            let sledger_name = null;
            let assetsrouptotal = 0;
            let assetsgrandTotal = 0;
            let lgtotal = 0;
            let leegrandTotal = 0;
            let debitgrandTotal = 0;
            let creditgrandTotal = 0;

            // Add opening cash row
            debitbody.append(
                `<tr class="openingrow"><td colspan="5">Opening Cash</td><td>${opening_cash.toFixed(2)}</td></tr>`);

            // Populate credit table
            if (credit_entries && credit_entries.length > 0) {
                credit_entries.forEach((data, index) => {
                    let entry_date = new Date(data.transactionDate);
                    let day = entry_date.getDate();
                    let month = entry_date.getMonth() + 1;
                    let year = entry_date.getFullYear();

                    day = day < 10 ? `0${day}` : day;
                    month = month < 10 ? `0${month}` : month;
                    let transcationDate = `${day}-${month}-${year}`;

                    let amount = data.transactionAmount;

                    if (ledger_name !== data.lname) {
                        if (ledger_name !== null) {
                            debitbody.append(
                                `<tr><td colspan="5">Total</td><td>${assetsrouptotal.toFixed(2)}</td></tr>`);
                        }
                        assetsrouptotal = 0;
                        debitbody.append(`<tr><td colspan="6"><strong></strong></td></tr>`);
                        debitbody.append(
                            `<tr class="openingrow"><td colspan="6"><strong>${capitalizeWords(data.lname)}</strong></td></tr>`
                        );
                        ledger_name = data.lname;
                    }

                    assetsrouptotal += parseFloat(amount);
                    assetsgrandTotal += parseFloat(amount);

                    debitbody.append(
                        `<tr>
                            <td>${transcationDate}</td>
                            <td>${data.id}</td>
                           <td>${data.accountId ? data.accountId : data.accountNo ? data.accountNo : ''}</td>
                            <td>${data.name ? capitalizeWords(data.name) : capitalizeWords(data.lname) ? capitalizeWords(data.lname) : ''}</td>
                            <td>${parseFloat(amount)}</td>
                            <td></td>
                        </tr>`
                    );
                });

                if (ledger_name !== null) {
                    debitbody.append(`<tr><td colspan="5">Total</td><td>${assetsrouptotal.toFixed(2)}</td></tr>`);
                }
            }

            debitgrandTotal += opening_cash + assetsgrandTotal;


            // Populate debit table
            if (debit_entries && debit_entries.length > 0) {
                debit_entries.forEach((data, index) => {
                    let entry_date = new Date(data.transactionDate);
                    let day = entry_date.getDate();
                    let month = entry_date.getMonth() + 1;
                    let year = entry_date.getFullYear();

                    day = day < 10 ? `0${day}` : day;
                    month = month < 10 ? `0${month}` : month;
                    let transcationDate = `${day}-${month}-${year}`;

                    let amount = data.transactionAmount;

                    if (ledger_name !== data.lname) {
                        if (ledger_name !== null && lgtotal > 0) {
                            creditbody.append(
                                `<tr><td colspan="6">Total</td><td>${lgtotal.toFixed(2)}</td></tr>`
                            );
                        }



                        lgtotal = 0;
                        creditbody.append(`<tr><td colspan="6"><strong></strong></td></tr>`);
                        creditbody.append(
                            `<tr class="openingrow"><td colspan="7"><strong>${capitalizeWords(data.lname)}</strong></td></tr>`
                        );
                        ledger_name = data.lname;


                    }

                    lgtotal += parseFloat(amount);
                    leegrandTotal += parseFloat(amount);

                    creditbody.append(
                        `<tr>
                            <td>${transcationDate}</td>
                            <td>${data.id}</td>
                            <td>${data.accountId ? data.accountId : data.accountNo ? data.accountNo : ''}</td>
                            <td>${data.name ? capitalizeWords(data.name) : capitalizeWords(data.lname) ? capitalizeWords(data.lname) : ''}</td>
                            <td>${capitalizeWords(data.lname)}</td>
                            <td>${parseFloat(amount)}</td>
                            <td></td>
                        </tr>`
                    );





                    if (index === debit_entries.length - 1 && lgtotal > 0) {
                        creditbody.append(`<tr><td colspan="6">Total</td><td>${lgtotal}</td></tr>`);
                    }
                });


            }

            let debitRows = debitbody.find('tr').length;
            let creditRows = creditbody.find('tr').length;
            let maxRows = Math.max(debitRows, creditRows);



            closing_cash = debitgrandTotal - leegrandTotal;
            creditgrandTotal += leegrandTotal + closing_cash;

            creditbody.append(
                `<tr class=""><td colspan="6" style="color:white;"></td><td style="color:white;"></td></tr>`
            );
            debitbody.append(
                `<tr class="" style="color:red;"><strong><td colspan="5" style="color:red;"></td><td style="color:red;"></td></strong></tr>`
            );
            creditbody.append(
                `<tr class="" style="color:red;"><strong><td colspan="6" style="color:red;">Cash-in-Hand</td><td style="color:red;">${closing_cash.toFixed(2)}</td></strong></tr>`
            );


            let liabilitiesRowCount = debitbody.find('tr').length;
            let assetsRowCount = creditbody.find('tr').length;
            let maxRowss = Math.max(liabilitiesRowCount, assetsRowCount);

            if (liabilitiesRowCount < maxRowss) {
                let rowsToAdd = maxRowss - liabilitiesRowCount;
                for (let i = 0; i < rowsToAdd; i++) {
                    debitbody.append(`<tr><td colspan="6">&nbsp;</td></tr>`);
                }
            }


            if (assetsRowCount < maxRowss) {
                let rowsToAdd = maxRowss - assetsRowCount;
                for (let i = 0; i < rowsToAdd; i++) {
                    creditbody.append(`<tr><td colspan="7">&nbsp;</td></tr>`);
                }
            }


            debitbody.append(
                `<tr style="background-color:#7367f0 !important;">
                    <td colspan="5" style="color:white;">Grand Total</td>
                    <td style="color:white;">${debitgrandTotal.toFixed(2)}</td>
                </tr>`
            );

            creditbody.append(
                `<tr style="background-color:#7367f0 !important;">
                    <td colspan="6" style="color:white;">Grand Total</td>
                    <td style="color:white;">${creditgrandTotal.toFixed(2)}</td>
                </tr>`
            );
        }






        function getTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            return `${day}-${month}-${year}`;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const enddateDateInput = document.getElementById('end_date');
        });




        if (document.readyState == "complete") {
            $(".transactionDate").val({{ session('currentdate') }});
        }



        {{--  function gogo() {
            const saleTable = document.querySelector('#excelTable tbody');
            const purchaseTable = document.querySelector('#excelTablee tbody');
            if (!saleTable || !purchaseTable) {
                console.error("Table elements not found."); // Debugging line
                return;
            }
            const saleRows = saleTable.querySelectorAll('tr');
            const purchaseRows = purchaseTable.querySelectorAll('tr');


            const saleRowCount = saleRows.length;
            const purchaseRowCount = purchaseRows.length;
            const insertEmptyRows = (table, rowCount, targetCount, beforeSelector) => {
                const diff = targetCount - rowCount;
                for (let i = 0; i < diff; i++) {
                    const emptyRow = document.createElement('tr');
                    const emptyTd = document.createElement('td');
                    emptyTd.setAttribute('colspan', '7');
                    emptyTd.innerHTML = '&nbsp;';
                    emptyRow.appendChild(emptyTd);
                    const beforeElement = table.querySelector(beforeSelector);
                    if (beforeElement) {
                        table.insertBefore(emptyRow, beforeElement);
                    } else {
                        table.appendChild(emptyRow);
                    }
                }
            };
            console.log("Sale Rows: ", saleRowCount);
            console.log("Purchase Rows: ", purchaseRowCount);
            if (saleRowCount > purchaseRowCount) {
                insertEmptyRows(purchaseTable, purchaseRowCount, saleRowCount, 'tr:last-child');
            } else if (purchaseRowCount > saleRowCount) {
                insertEmptyRows(saleTable, saleRowCount, purchaseRowCount, 'tr:last-child');
            }
        }  --}}
    </script>
@endpush
@push('style')
    <style>
        .alert-info td {
            text-align: start;
            padding-top: 10px !important;
        }

        #debittbody td {
            font-size: 12px;
            padding: 5px 0;
        }

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

        .buttonss {
            display: flex;
            gap: 24px;
        }

        .openingrow {
            background-color: #d6f7fb;
            color: black;
            font-weight: 700;
            font-size: 15px;
        }

        .closingcash {
            background-color: #7b7b8c !important;

            font-weight: 700;
            font-size: 15px;
            margin-bottom: 10px;
        }



        {{--  #credittbody tr.grandtotalrow {
        background-color: #7367f0 !important;
    }
    #credittbody tr.grandtotalrow td {
        color: white !important;
    }  --}}
    </style>
@endpush
