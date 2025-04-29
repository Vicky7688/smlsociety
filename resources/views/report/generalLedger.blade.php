@extends('layouts.app')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>General Ledger</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form  id="formData" name="formData">
                            @php
                                $currentDate =Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                            @endphp
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="text" class="form-control formInputs mydatepic transactionDate valid"
                                        placeholder="YYYY-MM-DD" id="startDate" name="startDate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionStart'))) }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="text" class="form-control formInputs mydatepic transactionDate valid" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate" value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="groupCode" class="form-label">Group</label>
                                    <select class="form-select formInputsSelectReport" id="groupCode" name="groupCode" onchange="getgerenalLedger(this)">
                                        <option value="">Select Group</option>
                                        @if (!empty($groups))
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->groupCode }}">{{ $group->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="ledgerCode" class="form-label">Ledger</label>
                                    <select class="form-select formInputsSelectReport" id="ledgerCode" name="ledgerCode">
                                        <option value="">Select Ledger</option>
                                    </select>
                                </div>
                                <div
                                    class="col-lg-4 col-md-12 col-sm-8 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button type="submit"
                                            class="btn btn-primary reportSmallBtnCustom waves-effect waves-light">View</button>
                                        {{--  <!--<a type="button" href="{{route('generalPrint.print')}}" target="_blank" class="btn btn-primary print-button ms-2 reportSmallBtnCustom">-->  --}}
                                        {{--  <!--    Print-->
                                        <!--</a>-->  --}}
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
        <div class="col-12 cards">
            <div class="card" id="sharelistprint">
                <div class="card-body tablee">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead class="table_head verticleAlignCenterReport">
                                <tr>
                                    <th class="fw-bold">SR NO</th>
                                    <th class="fw-bold">Date</th>
                                    <th class="fw-bold">A/c No</th>
                                    <th class="fw-bold">Head</th>
                                    <th class="fw-bold">Transaction</th>
                                    <th class="fw-bold">Debit</th>
                                    <th class="fw-bold">Credit</th>
                                    <th class="fw-bold">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="openingbalance">
                                <tr>
                                    <td colspan="5"><strong>Opening Balance</strong></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                </tr>
                            </tbody>
                            <tbody id="tabledataBody">
                            </tbody>
                            <tbody id="balancebody">
                                <tr style="background-color: #7367f0;">
                                    <td colspan="5"  style="color: white;"><b>Grand Total</b></td>
                                    <td  style="color: white;" id="drTotal">0</td>
                                    <td  style="color: white;" id="crTotal">0</td>
                                    <td  style="color: white;" id="balanceTotal">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>

    function getgerenalLedger(ele) {
        let groupCode = $(ele).val();

        $.ajax({
            url: "{{ route('getledgercodesss') }}",
            type: 'post',
            data: { groupCode: groupCode },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function (res) {
                let ledgerCode = $('#ledgerCode');
                ledgerCode.empty();

                if (res.status === 'success') {
                    let ledgers = res.ledgers;

                    if (Array.isArray(ledgers) && ledgers.length > 0) {
                        let options = ledgers.map(data => `<option value="${data.ledgerCode}">${data.name}</option>`).join('');
                        ledgerCode.append(options);

                    } else {

                        ledgerCode.append(`<option value="">Select Ledger</option>`);
                        notify(res.messages || 'No ledgers found', 'warning');

                    }
                } else {

                    ledgerCode.append(`<option value="">Select Ledger</option>`);
                    notify(res.messages || 'Failed to fetch ledgers', 'warning');

                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                notify('An error occurred while fetching ledger codes. Please try again.', 'error');
            }
        });
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


    $(document).on('submit','#formData',function(event){
        event.preventDefault();

       let startDate = $('#startDate').val();
       let endDate = $('#endDate').val();
       let groupCode = $('#groupCode').val();
       let ledgerCode = $('#ledgerCode').val();

        $.ajax({
            url : "{{ route('getgerenalLedgerdata') }}",
            type : 'post',
            data : {startDate : startDate , endDate: endDate,groupCode: groupCode,ledgerCode :ledgerCode },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function (res) {
                if(res.status === 'success'){
                    let groupType = res.groupType;
                    let ledgerType = res.ledgerType;
                    let opening_amount = parseFloat(res.opening_amount) ?? 0;
                    let currententries = res.currententries;

                    let grandTotalDebit = 0;
                    let grandTotalCredit = 0;



                    $('#openingbalance').empty();
                    $('#tabledataBody').empty();
                    $('#balancebody').empty();


                    if(opening_amount){
                        $('#openingbalance').append(`<tr><td colspan="5">Opening Balance</td><td></td><td></td><td>${parseFloat(opening_amount.toFixed(2))}</td></tr>`);
                    }else{
                        $('#openingbalance').append(`<tr><td colspan="5">Opening Balance</td><td></td><td></td><td>0</td></tr>`);
                    }


                    let balances = parseFloat(opening_amount);

                    if (Array.isArray(currententries) && currententries.length > 0) {

                        currententries.forEach((data, index) => {
                            let debit = data.transactionType === 'Dr' ? parseFloat(data.transactionAmount) : 0;
                            let credit = data.transactionType === 'Cr' ? parseFloat(data.transactionAmount) : 0;

                            grandTotalDebit += debit;
                            grandTotalCredit += credit;

                            if (groupType.type === 'Asset' || groupType.type === 'Expenditure') {
                                balances += debit - credit;
                            } else {
                                balances += credit - debit;
                            }

                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${dateFormat(data.transactionDate)}</td>
                                    <td>${data.accountNo}</td>
                                    <td>${groupType.headName}</td>
                                    <td>${data.formName || 'N/A'} - ${'A/c - '+data.accountNo}</td>
                                    <td>${debit.toFixed(2)}</td>
                                    <td>${credit.toFixed(2)}</td>
                                    <td>${balances.toFixed(2)}</td>
                                </tr>`;
                            $('#tabledataBody').append(row);
                        });
                    }

                    const grandTotal = balances;

                    $('#balancebody').append(`
                    <tr style="background-color: #7367f0;" color: white;">
                        <td colspan="5"  style="color: white;">Grand Total</td>
                        <td style="color: white;">${grandTotalDebit.toFixed(2)}</td>
                        <td style="color: white;">${grandTotalCredit.toFixed(2)}</td>
                        <td style="color: white;">${grandTotal.toFixed(2)}</td>
                    </tr>`);


                }else{
                    notify(res.messages,'warning');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                {{--  notify('An error occurred while fetching ledger codes. Please try again.', 'error');  --}}
            }
        });
    });

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
                <h4>{{$branch->name}}</h4>
                <h6>{{$branch->address}}</h6>
                <h6>General Ledger from ${startDate} to ${endDate}</h6>
            </div>`;

        var newWindow = window.open('', '_blank');
        newWindow.document.write(css + header + printContents);
        newWindow.document.close();
        newWindow.focus();
        newWindow.print();
        newWindow.close();
    }










        {{--  $(document).ready(function() {  --}}

        {{--  $('#groupCode').change(function() {
            $("#ledgerCode").find("option").not(":first").remove();
            var groupCode = $(this).val();
            $.ajax({
                url: '{{ route("getLedger") }}',
                type: 'get',
                data: {
                    groupCode: groupCode
                },
                dataType: 'json',
                success: function(response) {
                    $("#ledgerCode").find("option").not(":first").remove();
                    $.each(response["ledgers"], function(key, item) {
                        $("#ledgerCode").append(
                            `<option data='`+ item.name +`' value='${item.ledgerCode}'>${item.name}</option>`
                        )
                    });
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });


        $(document).on('submit', '#formData', function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);

            $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
            $.ajax({
                url: "{{route('generalLedger.getData')}}",
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
                        var generalLedgers = response.generalLedger;
console.table(generalLedgers);
                        var tableBody = $('#tableBody');
                        tableBody.empty();
                        // Logic To Decide Balance Nature
                        if (response.groupType == 'Asset' || response.groupType == 'Expenditure') {

                            var openingNature = " (Dr)";
                            if (parseInt(response.openingAmount) < 0) {
                                var openingNature = " (Cr)";
                            }

                            var closingNature = "Dr";
                            if (response.grouphead == "Loan") {
                                if (parseInt(response.closingAmount) > 0) {
                                    var closingNature = "Dr";
                                }
                            } else {

                                if (parseInt(response.closingAmount) < 0) {
                                    var closingNature = "Cr";
                                }

                            }

                        } else if (response.groupType == 'Liability' || response.groupType == 'Income') {
                            var openingNature = " (Cr)";
                            if (parseInt(response.openingAmount) < 0) {
                                var openingNature = " (Dr)";
                            }

                            var closingNature = "Cr";
                            if (parseInt(response.closingAmount) < 0) {
                                var closingNature = "Dr";
                            }
                        } else {
                            var openingNature = " (Cr)";
                            if (parseInt(response.openingAmount) < 0) {
                                var openingNature = " (Dr)";
                            }
                            var closingNature = "Cr";
                            if (parseInt(esponse.closingAmount) < 0) {
                                var closingNature = "Dr";
                            }
                        }

                        var balanceAmount = parseInt(response.openingAmount);
                        $('#balanceAmount').html(balanceAmount + openingNature);

                        var sr = 1;
                        var drTotal = 0;
                        var crTotal = 0;
                        var balanceTotal = balanceAmount;

                        $.each(generalLedgers, function(index, row) {

                            var drAmount = (row.transactionType == 'Dr') ? row
                                .transactionAmount : 0;
                            var crAmount = (row.transactionType == 'Cr') ? row
                                .transactionAmount : 0;

                            if (response.groupType == 'Asset' || response.groupType == 'Expenditure') {

                                var balanceNature = " (Dr)";
                                if (response.grouphead == "Loan") {
                                    balanceTotal = parseInt(balanceTotal) + parseInt(drAmount) - parseInt(crAmount);
                                } else {
                                    balanceTotal = parseInt(balanceTotal) + parseInt(drAmount) - parseInt(crAmount);
                                }

                                if (response.grouphead == "Loan") {
                                    if (parseInt(balanceTotal) > 0) {
                                        var balanceNature = "(Dr)";
                                    }
                                } else {
                                    if (parseInt(balanceTotal) < 0) {
                                        var balanceNature = "(Cr)";
                                    }
                                }


                            } else if (response.groupType == 'Liability' || response.groupType == 'Income') {

                                var balanceNature = " (Cr)";
                                balanceTotal = parseInt(balanceTotal) + parseInt(crAmount) - parseInt(drAmount);

                                if (parseInt(response.balanceTotal) < 0) {
                                    var balanceNature = " (Dr)";
                                }

                            } else {

                                var balanceNature = " (Cr)";
                                balanceTotal = parseInt(balanceTotal) + parseInt(crAmount) - parseInt(drAmount);

                                if (parseInt(response.balanceTotal) < 0) {
                                    var balanceNature = " (Dr)";
                                }
                            }

                            //console.log(balanceTotal, crAmount, drAmount)
                            //  balanceTotal = Math.abs(balanceTotal);

                            var rowHTML = "<tr>" +
                                "<td>" + (sr++) + "</td>" +
                                "<td>" + formatDate(row.transactionDate) +
                                "</td>" +
                                "<td>" + (row.accountNo ? row.accountNo : '-') + "</td>" +
                                "<td class='leftaligntd'>" + (row.memberType ? (row.memberType + " - ") : '') + row.formName +
                                "</td>" +
                                "<td class='leftaligntd'>" + (row.account ? row.account.name : '-') + "</td>" +
                                "<td class='rightaligntd'>" + drAmount + "</td>" +
                                "<td class='rightaligntd'>" + crAmount + "</td>" +
                                "<td class='rightaligntd'>" + balanceTotal + " " + balanceNature +
                                "</td>" +
                                "</tr>";
                            tableBody.append(rowHTML);

                            drTotal += drAmount;
                            crTotal += crAmount;
                        });

                        $('#drTotal').html(drTotal);
                        $('#crTotal').html(crTotal);
                        $('#balanceTotal').html(balanceTotal + ' ' + closingNature);
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });
    });  --}}

    </script>
@endpush
