@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Day Book</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form id="daybookForm" name="daybookForm">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="DATEFROM" class="form-label">DATE FROM</label>
                                    <input type="text" class="form-control formInputs mydatepic" placeholder="YYYY-MM-DD"
                                        id="startDate" name="startDate" value="{{ Session::get('currentdate') }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="DATETO" class="form-label">DATE TO</label>
                                    <input type="text" class="form-control formInputs mydatepic" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate" value="{{ Session::get('currentdate') }}" />
                                </div>
                                <div
                                    class="col-lg-7 col-md-4 col-12  py-2 saving_column inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                            id="viewdatabooksdetails"> View </button>
                                        <button type="button" class="ms-2 btn btn-primary print-button"
                                            onclick="printReport()"> Print </button>
                                        {{--  <!-- href="{{ route('dayBookPrint.print') }}" target="_blank" reportSmallBtnCustom -->  --}}
                                        {{--  <div class="ms-2 dropdown">
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
                <div class="col-md-6 col-sm-12 cards">
                    <div class="card">
                        <div class="card-body tablee">
                            <div class="nav nav-tabs mb-4">
                                <h4 class="t-heading ps-4">PAYMENT</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table  text-center table-striped table-bordered" id="excelTablee">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">V.No</th>
                                            <th scope="col">A/C</th>
                                            <th scope="col">Name</th>
                                            {{--  <th scope="col">Bank</th>  --}}
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
            </div>
        </div>
    </div>
@endsection

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

        .headings {
            text-align: center;
            font-size: 18px;
            font-weight: 500;
            background-color: #7367f0;
            color: white;
        }
    </style>
@endpush

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
            $(document).on('submit', '#daybookForm', function(event) {
                event.preventDefault();
                let startDate = $('#startDate').val();
                let endDate = $('#endDate').val();

                $.ajax({
                    url: "{{ route('getdaybookdata') }}",
                    type: 'post',
                    data: {
                        startDate: startDate,
                        endDate: endDate
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let openingcash = res.openingcash || 0;
                            let debitbalance = res.debitbalance || [];
                            let creditbalance = res.creditbalance || [];

                            let grandtotals = openingcash;
                            let debittotalsss = 0;

                            showdata(debitbalance, creditbalance, openingcash);
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(xhr, error, status) {
                        console.log('Ajax Not Working');
                    }
                });
            });
        });

        function showdata(debitbalance, creditbalance, openingcash) {
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
            creditbody.append(
                `<tr class="openingrow"><td colspan="5">Opening Cash</td><td>${openingcash.toFixed(2)}</td></tr>`);

            // Populate credit table
            if (creditbalance && creditbalance.length > 0) {
                creditbalance.forEach((data, index) => {
                    let entry_date = new Date(data.transactionDate);
                    let day = entry_date.getDate();
                    let month = entry_date.getMonth() + 1;
                    let year = entry_date.getFullYear();

                    day = day < 10 ? `0${day}` : day;
                    month = month < 10 ? `0${month}` : month;
                    let transcationDate = `${day}-${month}-${year}`;

                    let amount = data.transactionAmount;

                    if (ledger_name !== data.ledgerName) {
                        if (ledger_name !== null) {
                            creditbody.append(
                                `<tr><td colspan="5">Total</td><td>${assetsrouptotal.toFixed(2)}</td></tr>`);
                        }
                        assetsrouptotal = 0;
                        {{--  creditbody.append(`<tr><td colspan="6"><strong></strong></td></tr>`);  --}}
                        creditbody.append(
                            `<tr class="openingrow"><td colspan="6"><strong>${capitalizeWords(data.ledgerName)}</strong></td></tr>`
                            );
                        ledger_name = data.ledgerName;
                    }

                    assetsrouptotal += parseFloat(amount);
                    assetsgrandTotal += parseFloat(amount);

                    creditbody.append(
                        `<tr>
                            <td>${transcationDate}</td>
                            <td>${data.glid}</td>
                            <td>${data.accountNo ? data.accountNo : data.memnumber ? data.memnumber : ''}</td>
                            <td>${data.memberName ? capitalizeWords(data.memberName) : capitalizeWords(data.lmemberName) ? capitalizeWords(data.lmemberName) : ''}</td>
                            <td>${parseFloat(amount)}</td>
                            <td></td>
                        </tr>`
                    );
                });

                if (ledger_name !== null) {
                    creditbody.append(`<tr><td colspan="5">Total</td><td>${assetsrouptotal.toFixed(2)}</td></tr>`);
                }
            }

            debitgrandTotal += openingcash + assetsgrandTotal;


            // Populate debit table
            if (debitbalance && debitbalance.length > 0) {
                debitbalance.forEach((data, index) => {
                    let entry_date = new Date(data.transactionDate);
                    let day = entry_date.getDate();
                    let month = entry_date.getMonth() + 1;
                    let year = entry_date.getFullYear();

                    day = day < 10 ? `0${day}` : day;
                    month = month < 10 ? `0${month}` : month;
                    let transcationDate = `${day}-${month}-${year}`;

                    let amount = data.transactionAmount;

                    if (ledger_name !== data.ledgerName) {
                        if (ledger_name !== null && lgtotal > 0) {
                            debitbody.append(
                                `<tr><td colspan="5">Total</td><td>${lgtotal.toFixed(2)}</td></tr>`
                            );
                        }

                        lgtotal = 0;
                        debitbody.append(`<tr><td colspan="6"><strong></strong></td></tr>`);
                        debitbody.append(
                            `<tr class="openingrow"><td colspan="6"><strong>${capitalizeWords(data.ledgerName)}</strong></td></tr>`
                            );
                        ledger_name = data.ledgerName;
                    }

                    lgtotal += parseFloat(amount);
                    leegrandTotal += parseFloat(amount);

                    debitbody.append(
                        `<tr>
                            <td>${transcationDate}</td>
                            <td>${data.glid}</td>
                            <td>${data.accountNo ? data.accountNo : data.memnumber ? data.memnumber : ''}</td>
                            <td>${data.memberName ? capitalizeWords(data.memberName) : capitalizeWords(data.ledgerName) ? capitalizeWords(data.ledgerName) : ''}</td>
                            <td>${parseFloat(amount)}</td>
                            <td></td>
                        </tr>`
                    );

                    if (index === debitbalance.length - 1 && lgtotal > 0) {
                        debitbody.append(`<tr><td colspan="5">Total</td><td>${lgtotal}</td></tr>`);
                    }
                });


            }

            let debitRows = debitbody.find('tr').length;
            let creditRows = debitbody.find('tr').length;
            let maxRows = Math.max(debitRows, creditRows);

            closing_cash = debitgrandTotal - leegrandTotal;
            creditgrandTotal += leegrandTotal + closing_cash;

            creditbody.append(`<tr class=""><td colspan="5" style="color:white;"></td><td style="color:white;"></td></tr>`);
            debitbody.append(
                `<tr class="" style="color:red;"><strong><td colspan="5" style="color:red;">Cash-in-Hand</td><td style="color:red;">${closing_cash.toFixed(2)}</td></strong></tr>`
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
                    creditbody.append(`<tr><td colspan="6">&nbsp;</td></tr>`);
                }
            }

            debitbody.append(
                `<tr >
        <td colspan="5">Grand Total</td>
        <td>${debitgrandTotal.toFixed(2)}</td>
    </tr>`
            );


            creditbody.append(
                `<tr>
                    <td colspan="5">Grand Total</td>
                    <td >${creditgrandTotal.toFixed(2)}</td>
                </tr>`
            );
        }







        function printReport() {
            // Apply consistent borders to the tables
            $('.table').css({ 'border': '1px solid #ddd', 'border-collapse': 'collapse' });
            $('th, td').css({ 'padding': '8px', 'text-align': 'left', 'border': '1px solid #ddd' });

            let printContents = document.getElementById('sharelistprint').innerHTML;
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();

            let css = `
                <style>
                    @media print {
                        body { background-color: #ffffff; margin: 20px; padding: 10px; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
                        #sharelistprint { display: flex; justify-content: space-between; }
                        .cards { width: 48%; }
                        .t-heading { font-weight: bold; text-align: center; }
                        h4, h6 { text-align: center; margin: 5px 0; }
                    }
                </style>`;


            let header = `
                <div style="text-align: center; margin-bottom: 1rem;">
                    <h6>Day Book from ${startDate} to ${endDate}</h6>
                </div>`;

            let newWindow = window.open('', '_blank');
            newWindow.document.write(css + header + printContents);
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            newWindow.close();
        }







        function gogo() {
            const saleTable = document.querySelector('#excelTable tbody');
            const purchaseTable = document.querySelector('#excelTablee tbody');

            if (!saleTable || !purchaseTable) {
                console.error("Table elements not found.");
                return;
            }

            const saleRows = saleTable.querySelectorAll('tr');
            const purchaseRows = purchaseTable.querySelectorAll('tr');

            const saleRowCount = saleRows.length;
            const purchaseRowCount = purchaseRows.length;

            const insertEmptyRows = (table, rowCount, targetCount) => {
                const diff = targetCount - rowCount;
                for (let i = 0; i < diff; i++) {
                    const emptyRow = document.createElement('tr');
                    const emptyTd = document.createElement('td');
                    emptyTd.setAttribute('colspan', '7'); // Adjust colspan to match the number of columns in your table
                    emptyTd.innerHTML = '&nbsp;';
                    emptyRow.appendChild(emptyTd);
                    table.appendChild(emptyRow); // Append to the end of the table
                }
            };

            if (saleRowCount > purchaseRowCount) {
                insertEmptyRows(purchaseTable, purchaseRowCount, saleRowCount);
            } else if (purchaseRowCount > saleRowCount) {
                insertEmptyRows(saleTable, saleRowCount, purchaseRowCount);
            }
        }





















        {{--  $(document).ready(function() {
            $("#viewdatabooksdetails").click(function(e) {
                e.preventDefault();
                var startDate = $("#start_date").val();
                var endDate = $("#end_date").val();
                var branch = $("#branch_id").val();
                axios.post("{{ route('reports.list') }}", {
                        'startdate': startDate,
                        'enddate': endDate,
                        'branch': branch
                    })
                    .then((response) => {
                        if (response.data.status == "success") {
                            var debitData = response.data.debetdata;
                            var creditData = response.data.credetdata;
                            var closingamount = response.data.closingcash;
                            var openingamount = response.data.opening_cash;
                            updateTableBody('debittbody', debitData, 0, openingamount);
                            updateTableBody('credittbody', creditData, closingamount);
                            // $("#closingamount").text(closingamount);
                            gogo();
                        } else if (response.data.status == "error") {
                            var errors = response.data.errors;
                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='date'],input[type='number'],select").removeClass(
                                'is-invalid');
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}`).siblings('p.error').addClass(
                                    'invalid-feedback').html(value);
                            });
                        }
                    });
            });
        });  --}}

        {{--  function getTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            return `${day}-${month}-${year}`;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const enddateDateInput = document.getElementById('end_date');  --}}
        {{--  startDateInput.value = getTodayDate();
        enddateDateInput.value = getTodayDate();  --}}
        {{--  });  --}}

        {{--  function updateTableBody(tableId, data, closingAmount, opening = 0) {
            var tbody = $(`#${tableId}`);
            tbody.empty();
            var totals = {};
            var grandTotal = 0;
            if (tableId === 'debittbody') {
                var openingCashRow = `<tr class="Opening_cash">
                        <td colspan="5"><strong>Opening Cash</strong></td>
                        <td>${opening}</td>
                    </tr>`;
                tbody.append(openingCashRow);
            }
            $.each(data, function(index, row) {
                var groupName = row.group?.name;
                var ledgerName = row.ledger?.name;
                var groupLedgerKey = groupName + '-' + ledgerName;
                grandTotal += parseInt(row.transactionAmount) || 0;

                if (!totals[groupLedgerKey]) {
                    totals[groupLedgerKey] = {
                        ledgerTotal: 0,
                    };
                    var cals = (tableId === 'credittbody') ? 6 : 5;
                    var groupRow = `<tr class="alert-info">
                       <td colspan="` + cals + `"><strong>${ledgerName}</strong></td>
                       <td colspan="1"></td>
                      </tr>`;
                    tbody.append(groupRow);
                }

                var formattedDate = moment(row.transactionDate).format('DD-MM-YYYY');
                var tr = $("<tr>");
                tr.append(`<td>${formattedDate}</td>`);
                tr.append(`<td>${row.id}</td>`);
                tr.append(`<td>${row.accountNo ?? 'Null'}</td>`);
                tr.append(`<td>${row.name ?? 'Unknown Name'}</td>`);
                if (tableId === 'credittbody') {
                    tr.append(`<td>${ledgerName}</td>`);
                }
                tr.append(`<td>${parseFloat(row.transactionAmount)}</td>`);
                totals[groupLedgerKey].ledgerTotal += parseFloat(row.transactionAmount) || 0;

                var viewtotals = ``;
                if (
                    index === data.length - 1 ||
                    data[index + 1]?.group?.groupCode !== row.group?.groupCode ||
                    data[index + 1]?.ledger?.ledgerCode !== row.ledger?.ledgerCode
                ) {
                    viewtotals = totals[groupLedgerKey].ledgerTotal;
                }

                tr.append(`<td>` + viewtotals + `</td>`);
                tbody.append(tr);
            });

            if (tableId === 'debittbody') {
                var grandTotalRow = `<tr class="grand-total">
                            <td colspan="5"><strong>Total</strong></td>
                            <td>${grandTotal+opening}</td>
                        </tr>`;
                tbody.append(grandTotalRow);
            }
            if (tableId === 'credittbody') {
                var closingAmountRow = `<tr class="closing-amount">
                              <td colspan="6"><strong>Closing Cash</strong></td>
                              <td>${closingAmount}</td>
                            </tr>`;
                tbody.append(closingAmountRow);
                var grandTotalRow = `<tr class="grand-total">
                            <td colspan="6"><strong>Total</strong></td>
                            <td>${grandTotal+closingAmount}</td>
                        </tr>`;
                tbody.append(grandTotalRow);
            }

        }
        if (document.readyState == "complete") {
            $(".transactionDate").val({{ session('currentdate') }});
        }  --}}
    </script>
@endpush
