@extends('layouts.app')

@php
    $table = 'yes';
@endphp

@section('content')
    <style>
        .modal-content {
            background: #fff;
            color: #000;
            border: none;
        }

        .modal-header.mod-hd {
            background: #418da2;
            color: #fff;
            border: none;
        }

        .modal-footer.mod-ft {
            border: none;
        }

        .btn.btn-primary.model-btn {
            background: #418da2;
            border: none;
            padding: 12px 12px;
            border-radius: 7px;
        }

        .btn.btn-secondary.close-btn {
            padding: 12px 12px;
            border-radius: 7px;
        }

        .close.cross-icon {
            color: #fff;
        }

        .formInputsReport {
            background: #fff;
            border: 1px solid #80808040;
            border-radius: 5px;
            color: #000;
        }

        .form-control:focus {
            background-color: #fff;
        }

        div.dt-container .dt-paging .dt-paging-button:hover {
            color: #000 !important;
            border: 1px solid #418da2;
            background-color: #418da2 !important;
            background: linear-gradient(to bottom, #418da2 0%, #418da2 100%);
        }

        div.dt-container .dt-paging .dt-paging-button {
            color: #000 !important;
            border: none background-color: transparent;

        }

        div.dt-container .dt-paging .dt-paging-button.current,
        div.dt-container .dt-paging .dt-paging-button.current:hover {
            color: #000 !important;
        }

        .bottom-card,
        .voucher-fields {
            background: #7367f0;
            border-radius: 12px;
            padding: 20px 30px;
        }

        td {
            border: 1px solid #808080;
        }

        .field-wt-cls {
            background: #fff;
            color: #000;
            border: 1px solid #b2b2b2;
            border-radius: 5px;
        }

        .form-select {
            width: auto;
            ;
        }

        i.fa-solid.fa-pencil {
            color: #7367f0;
        }

        i.fa-sharp.fa-solid.fa-trash {
            color: red;
        }

        .text-cls {
            color: #000;
        }

        thead.tablehead-cls {
            border: 1px solid #7367f0;
            color: black;
        }

        .submit-one {
            background: #fff;
            color: #000000e0 !important;
            border: none;
            padding: 11px 15px;
        }

        #dt-length-0 {
            margin-right: 12px;
        }

        #example_info,
        #dt-length-0,
        #dt-search-0 {
            color: #000;
        }

        .Vehicle-table-wrap {
            background: #fff;
            padding: 20px 20px;
            border-radius: 13px;
        }

        .icon-td {
            text-align: center
        }

        {{--    --}}

        /* Toolbar Styling */
        .toolbar-container {
            gap: 8px;

        }

        .tablehead-cls th {
            border: 1px solid #7367f0;
            padding: 9px 6px;
            font-size: 16px;
            font-weight: 500;
        }

        /* Button Styling */
        .toolbar-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 90px;
            text-align: center;
            padding: 6px 0px;
        }

        .toolbar-button:hover {
            background: #ddd;
            transform: scale(1.05);
        }

        /* Icon Styling */
        .toolbar-icon {
            width: 32px;
            height: 32px;
            margin-bottom: 6px;
        }

        /* Button Label */
        .button-label {
            font-size: 14px;
            font-weight: 500;
            color: #000;
        }

        input.form-control.tble-imp {
            background: #ffff;
            color: #000;
            border: none;
            height: 28px;
        }

        .display td {
            padding: 0 !important;
            border-radius: 0 !important;
        }


        .toolbar-button i {
            color: #7367f0;
            margin-bottom: 4px;
        }


        .modal {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1002;
            padding: 6% 23%;
            visibility: hidden;
            opacity: 1;
            pointer-events: none;
            transition: all 0.3s;

            &:target {
                visibility: visible;
                opacity: 1;
                pointer-events: auto;
            }

            .modal-content {
                max-height: 95%;
                padding: 40px;
                background: white;
                border-radius: 5px;
                overflow: auto;
                position: relative;
            }
        }

        .modal-close {
            color: #aaa;
            line-height: 50px;
            font-size: 80%;
            position: absolute;
            right: 0;
            text-align: center;
            top: 0;
            width: 70px;
            text-decoration: none;

            &:hover {
                color: #363636;
            }
        }

        .selected {
            background-color: #d3d3d3;
            /* Light gray */
        }

        #results {
            display: none;
            background-color: #8395a5bd;
            color: white;
            padding: 4px;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card page_headings mb-4">
                    <div class="card-body py-2">
                        <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Journal Voucher</h4>
                    </div>
                </div>
                @php
                    $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                @endphp
                <div class="card">
                    <div class="card-body" id="journalVoucherForm">
                        {{--  <form action="javascript: void(0)" data-route="{{ route('journalVoucher.store') }}" method="post"
                        id="formData" name="formData">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="savingId" id="savingId">
                            <div class="col-md-2 col-12 mb-4">
                                <label for="voucherDate" class="form-label">Date</label>
                                <input type="text" class="form-control transactionDate" placeholder="YYYY-MM-DD"
                                value="{{ $currentDate }}" id="voucherDate" name= "voucherDate" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="transactionType" class="form-label">Voucher No</label>
                                <input type="number" step="NA" class="form-control" name="voucherNo"
                                    value="{{$nextVoucherId}}" readonly id="voucherNo">
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="drcr" class="form-label">DR/CR</label>
                                <select class="form-select" id="drcr" name="drcr">
                                    <option value="Dr">Dr</option>
                                    <option value="Cr">Cr</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="groupCode" class="form-label">Group</label>
                                <select class="form-select" id="groupCode" name="groupCode">
                                    <option value="">Select Group</option>
                                    @if (!empty($groups))
                                    @foreach ($groups as $group)
                                    <option value="{{$group->groupCode}}">{{$group->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-4 col-12 mb-4">
                                <label for="ledgerCode" class="form-label">Ledger</label>
                                <select name="ledgerCode" class="form-select" id="ledgerCode">
                                    <option value="">Select Ledger</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="any" class="form-control" id="amount" name="amount" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-6 col-12 mb-4">
                                <label for="narration" class="form-label">Narration</label>
                                <input type="text" class="form-control" id="narration" name="narration" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mt-4">
                                <button type="submit" id="submitButton"
                                    class="btn btn-primary waves-effect waves-light">Add</button>
                            </div>
                        </div>
                    </form>  --}}


                        <form id="inputform" autocomplete="off">
                            <div class="Vehicle-table-wrap">
                                <div class="voucher-inner mb-5">
                                    <div class="row">
                                        {{--  <div class="mb-2 col-md-3">
                                        <label>Voucher Type</label>
                                        <select name="vouchertype" id="vouchertype" class="form-select">
                                            <option value="Journal Voucher">Journal Voucher</option>
                                            <option value="Purchase Voucher">Purchase Voucher</option>
                                            <option value="Cash Voucher">Cash Voucher</option>
                                        </select>
                                    </div>  --}}
                                        <input type="hidden" name="voucherId" id="voucherId">
                                        <div class="mb-2 col-md-3">
                                            <label>Voucher Date</label>
                                            <input type="text" class="form-control formInputs mydatepic transactionDate"
                                                name="voucherdate" id="voucherdate" value="{{ date('d-m-Y') }}">
                                        </div>
                                        <div class="mb-2 col-md-3">
                                            <label>Voucher No</label>
                                            <input type="text" class="form-control formInputs" name="voucherno"
                                                id="voucherno"
                                                @if (!empty($vouchars)) value="{{ $vouchars }}" @endif
                                                readonly>
                                        </div>
                                        {{--  <div class="mb-2 col-md-3">
                                        <label>Transport</label>
                                        <input type="text" class="form-control field-wt-cls" name="transport" id="transport">
                                    </div>  --}}

                                    </div>
                                </div>
                                <table id="" class="display pt-5" style="width:100%; color: black;">
                                    <thead class="tablehead-cls">
                                        <tr>
                                            <th>DR/CR</th>
                                            <th>Code</th>
                                            <th>Account Description</th>
                                            <th>Debit Amount</th>
                                            <th>Credit Amount</th>
                                            <th>Narration</th>
                                            {{--  <th>Edit</th>  --}}
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        <tr>
                                            <td class="col-sm-1"><input class="form-control tble-imp formInputs"
                                                    name="drcr[]" id="drcr0" type="text"></td>
                                            <td class="col-sm-1"><input class="form-control tble-imp formInputs"
                                                    name="code[]" id="code0" type="text"></td>
                                            <td class="col-sm-3"><input class="form-control tble-imp formInputs" readonly
                                                    name="description[]" id="description0" type="text"></td>
                                            <td class="col-sm-1"><input class="form-control tble-imp formInputs"
                                                    name="dramount[]" id="dramount0" type="text"></td>
                                            <td class="col-sm-1"><input class="form-control tble-imp formInputs"
                                                    name="cramount[]" id="cramount0" type="text"></td>
                                            <td class="col-sm-4"><input class="form-control tble-imp formInputs"
                                                    name="narration[]" id="narration0" type="text"></td>
                                            <td class="col-sm-1"><a class="deleteRow"></a></td>
                                        </tr>
                                    </tbody>
                                    {{--  <tbody>
                                    <tr>
                                        <td colspan="3">Grand Total</td>
                                        <td id="debitgrandTotal">0</td>
                                        <td id="creditgrandTotal">0</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>  --}}
                                </table>

                                <div class="container m-0 p-0">
                                    <div class="toolbar-container d-flex flex-wrap justify-content-left pt-3">
                                        {{--  <div class="toolbar-button">
                                    <span class="button-label"> <i class="fa fa-angle-double-left"></i> Next</span>
                                    </div>
                                    <div class="toolbar-button">
                                    <span class="button-label">Previous <i class="fa fa-angle-double-right"></i></span>
                                    </div>  --}}
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                            id="savebtton">Save</button>
                                        {{--  <button class="toolbar-button" type="submit" >Save</button>  --}}



                                        {{--  <div class="toolbar-button">
                                        <span class="button-label"><i class="fa fa-trash"></i> Delete</span>
                                    </div>  --}}
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body data_tables tablee">
                <div class="table-responsive tabledata">
                    <table class="table text-center table-bordered" id="datatable">
                        <thead class="table_head">
                            <tr>
                                <th class="fw-bold">Sr.No</th>
                                <th class="fw-bold">Date</th>
                                <th class="fw-bold">Vouchar No</th>
                                <th class="fw-bold">Description</th>
                                <th class="fw-bold">Amount</th>
                                <th class="fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="journalTableBody">
                            @if (!empty($allvouchars))
                                @foreach ($allvouchars as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->voucherDate)) }}</td>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ ucwords($row->narration) ? ucwords($row->narration) : 'Journal Voucher' }}
                                        </td>
                                        <td>{{ $row->drAmount ? $row->drAmount : $row->crAmount }}</td>
                                        <td style="width:85px;">
                                            <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom voucharedit"
                                                data-id="{{ $row->id }}"></i>
                                            <i class="fa-solid fa-trash iconsColorCustom deletebtn"
                                                data-id="{{ $row->id }}"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div id="modal" class="modal">
            <div class="modal-content">
                {{-- <a href="#" title="Close" class="modal-close">Close</a> --}}
                <table id="myTableproducts">
                    <thead class="pro">
                        <tr>
                            <th>Code</th>
                            <th>Name </th>
                            <th>Group Code</th>
                            <th>Group Name </th>
                        </tr>
                    </thead>
                    <tbody id="tt">
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{--
<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modalForm" data-route="{{ route('journalVoucher.store') }}" method="post">
                @csrf
                <div class="modal-body text-center">
                    <h2 class="lead">You want to save the record?</h2>
                    <p id="modalMessage"></p>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="submitButtonn" class="btn btn-primary waves-effect waves-light"
                        type="submit">Submit</button>

                </div>
            </form>
        </div>
    </div>
</div>  --}}

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "paging": true, // Enables pagination
                "searching": true, // Enables search box
                "ordering": true, // Enables column sorting
                "info": true, // Shows "Showing X of Y entries"
                "lengthMenu": [10, 25, 50, 100], // Dropdown for entries per page
                "language": {
                    "search": "Search:", // Customizing search label
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
    {{--  <script>
$(document).ready(function() {
    $('#groupCode').change(function() {
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
                        `<option value='${item.ledgerCode}'>${item.name}</option>`
                    )
                });
            },
            error: function(jqXHR, exception) {
                console.log("Something went wrong");
            }
        });
    });

    $("#formData").validate({
        rules: {
            groupCode: {
                required: true
            },
            ledgerCode: {
                required: true
            },
            amount: {
                required: true,
                number: true
            },
            drcr: {
                required: true
            },
        },
        // Display error messages next to the corresponding form element
        errorElement: "p",
        errorPlacement: function(error, element) {
            error.addClass("error");
            element.closest("div").append(error);
        },
        submitHandler: function (form) {
            Addtransactions();
        }
    });

    // $('#submitButton').on('click', function() {
    //     $("#formData").submit();
    // });

    function editEntry(index) {
        var entry = journalEntries[index];

        if (entry) {
            $('#groupCode').val(entry.groupCode);
            $('#ledgerCode').val(entry.ledgerCode);
            $('#amount').val(entry.amount);
            $('#drcr').val(entry.drcr);
            $('#narration').val(entry.narration);

            // Set the index of the entry being edited
            $('#savingId').val(index);
        }
    }

    var journalEntries = [];

    function updateTable(journalEntries, response) {
        var tableBody = $('#journalTableBody');
        tableBody.empty();
        var grandTotalDr = 0;
        var grandTotalCr = 0;

        branchId = 1;
        sessionId = 1;

        var journalEntries = journalEntries || JSON.parse(localStorage.getItem('journalEntries')) || [];

        $.each(journalEntries, function(index, entry) {
            var drAmount = (entry.drcr === 'Dr') ? entry.amount : 0;
            var crAmount = (entry.drcr === 'Cr') ? entry.amount : 0;

            var row = '<tr>' +
                '<td>' + entry.groupName + '</td>' +
                '<td>' + entry.ledgerName + '</td>' +
                '<td>' + drAmount + '</td>' +
                '<td>' + crAmount + '</td>' +
                '<td>' +
                '<button type="button" class="btn btn-info btn-sm edit-button" data-index="' + index +
                '"><i class="ti ti-pencil me-1"></i></button>' +
                '<button type="button" class="btn btn-danger btn-sm ms-1" onclick="removeEntry(' +
                index + ')"><i class="ti ti-trash me-1"></i></button>' +
                '</td>' +
                '</tr>';
            tableBody.append(row);

            grandTotalDr += parseFloat(drAmount);
            grandTotalCr += parseFloat(crAmount);
        });

        var grandTotalRow = '<tr>' +
            '<td colspan="2" class="text-end">Grand Total</td>' +
            '<td>' + grandTotalDr + '</td>' +
            '<td>' + grandTotalCr + '</td>' +
            '<td></td>' +
            '</tr>';
        tableBody.append(grandTotalRow);
        $('#submitButtonn').prop('disabled', grandTotalDr !== grandTotalCr);

    }

    $('#viewEntries').on('click', function() {
        updateTable();
    });

    function Addtransactions() {
        var formData = {
            groupCode: $('#groupCode').val(),
            ledgerCode: $('#ledgerCode').val(),
            amount: $('#amount').val(),
            drcr: $('#drcr').val(),
            narration: $('#narration').val(),
            groupName: $('#groupCode option:selected').text(),
            ledgerName: $('#ledgerCode option:selected').text(),
        };

        var savingId = $('#savingId').val();

        if (savingId === "") {
            // Add new entry
            if (formData.groupCode && formData.ledgerCode && formData.amount && formData.drcr) {
                journalEntries.push({
                    groupCode: formData.groupCode,
                    ledgerCode: formData.ledgerCode,
                    amount: formData.amount,
                    drcr: formData.drcr,
                    narration: formData.narration,
                    groupName: formData.groupName,
                    ledgerName: formData.ledgerName
                });
                localStorage.setItem('journalEntries', JSON.stringify(journalEntries));
                updateTable();
                $('#drcr').val('Dr');
                $("#groupCode").val('');
                $('#ledgerCode').val('');
                $('#amount').val('');
                $('#narration').val('');
                notify("Transaction Added Successfully!!", 'success');
            }
        } else {
            // Edit existing entry
            journalEntries[savingId] = {
                groupCode: formData.groupCode,
                ledgerCode: formData.ledgerCode,
                amount: formData.amount,
                drcr: formData.drcr,
                narration: formData.narration,
                groupName: formData.groupName,
                ledgerName: formData.ledgerName
            };
            // Clear savingId after editing
            $('#savingId').val("");
            localStorage.setItem('journalEntries', JSON.stringify(journalEntries));
            updateTable();
            $('#drcr').val('Dr');
            $("#groupCode").val('');
            $('#ledgerCode').val('');
            $('#amount').val('');
            $('#narration').val('');
            notify("Transaction Modify Successfully!!", 'success');
        }

    };

    $(document).on('click', '.edit-button', function() {
        var rowIndex = $(this).data('index');
        editEntry(rowIndex);
    });

    function removeEntry(index) {
        journalEntries.splice(index, 1);
        localStorage.setItem('journalEntries', JSON.stringify(journalEntries));
        updateTable(journalEntries);
    }

    $('#saveButton').on('click', function() {
        var grandTotalDr = 0;
        var grandTotalCr = 0;

        var journalEntries = JSON.parse(localStorage.getItem('journalEntries')) || [];

        $.each(journalEntries, function(index, entry) {
            var drAmount = (entry.drcr === 'Dr') ? entry.amount : 0;
            var crAmount = (entry.drcr === 'Cr') ? entry.amount : 0;
            grandTotalDr += parseFloat(drAmount);
            grandTotalCr += parseFloat(crAmount);
        });

        if (grandTotalDr === grandTotalCr) {
            // Display a correct message within the modal
            $('#modalMessage').text('Dr and Cr amounts are equal. Click "OK" to submit.');
            $('#basicModal').modal('show');
        } else {
            // Display an error message within the modal
            notify("Dr and Cr amounts are not equal. Please review your entries.", 'warning');
        }

    });


    $('#submitButtonn').on('click', function() {
        var grandTotalDr = 0;
        var grandTotalCr = 0;

        var journalEntries = JSON.parse(localStorage.getItem('journalEntries')) || [];

        var hasValues = false;

        $.each(journalEntries, function(index, entry) {
            var drAmount = (entry.drcr === 'Dr') ? entry.amount : 0;
            var crAmount = (entry.drcr === 'Cr') ? entry.amount : 0;
            grandTotalDr += parseFloat(drAmount);
            grandTotalCr += parseFloat(crAmount);

            if (entry.groupCode || entry.ledgerCode || entry.amount || entry.narration) {
                hasValues = true;
            }
        });

        if (grandTotalDr === grandTotalCr) {
            var formData = {
                entries: journalEntries.map(function(entry) {
                    return {
                        groupCode: entry.groupCode,
                        ledgerCode: entry.ledgerCode,
                        drAmount: (entry.drcr === 'Dr') ? entry.amount : 0,
                        crAmount: (entry.drcr === 'Cr') ? entry.amount : 0,
                        narration: entry.narration || '',
                        branchId: branchId,
                        sessionId: sessionId
                    };
                }),

                grandTotalDr: grandTotalDr,
                grandTotalCr: grandTotalCr,
            };
            var transactionDate =  $("#voucherDate").val();

            $('#modalForm').attr('action', $('#modalForm').data('route'));

            $('#modalForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: $('#modalForm').attr('data-route'),
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'POST',
                        entries: formData.entries,
                        voucherDate : transactionDate
                    },

                    dataType: 'json',
                    success: function(response) {
                        console.log('Server response:', response);
                        $('#voucherNo').val(response.data.voucherId);
                        alert('Data submitted successfully!');
                        location.reload();
                    },
                   error: function(errors) {

                     showError(errors, "withoutform");
                }
                });
            });
        }else{
            alert('Please provide the values');
            return false;
        }
    });
    window.removeEntry = removeEntry;
});




if (document.readyState == "complete") {
    $(".transactionDate").val({{  session('currentdate') }});
}
</script>  --}}

    <script>
        $(document).on('keypress', 'input[name="drcr[]"]', function(event) {
            let charCode = event.which;
            let charStr = String.fromCharCode(charCode);
            if (charStr !== 'c' && charStr !== 'd' && charStr !== 'D' && charStr !== 'D' && charCode !== 13) {
                event.preventDefault();
                return;
            }

            if (event.which === 13) {
                event.preventDefault();
                let value = $(this).val();
                let id = $(this).attr('id');
                let codeval = id.replace('drcr', '');
                console.log("Value:", value, "ID:", id, "codeval:", codeval);
                if (value == 'c') {

                    $(this).val('Cr');
                    $('#code' + codeval).focus().select();
                }
                if (value == 'C') {

                    $(this).val('Cr');
                    $('#code' + codeval).focus().select();
                }

                if (value == 'd') {

                    $(this).val('Dr');
                    $('#code' + codeval).focus().select();
                }
                if (value == 'D') {

                    $(this).val('Dr');
                    $('#code' + codeval).focus().select();
                }

                if (value == 'Dr') {

                    $(this).val('Dr');
                    $('#code' + codeval).focus().select();
                }
                if (value == 'Cr') {

                    $(this).val('Dr');
                    $('#code' + codeval).focus().select();
                }

            }
        });

        $(document).on('keypress', 'input[name="search"]', function(event) {
            if (event.which === 13) {
                event.preventDefault();
            }
        });

        $(document).on('keypress', 'input[name="code[]"]', function(event) {
            if (event.which === 13) {
                event.preventDefault();
                let value = $(this).val();
                let id = $(this).attr('id');
                let codeval = id.replace('code', '');
                console.log("Value:", value, "ID:", id, "codeval:", codeval);

                if (value != "") {
                    $.ajax({
                        url: "{{ route('getled') }}",
                        type: "POST",
                        data: {
                            name: value,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'success') {
                                // $('#description' + codeval).focus();
                                let alldata = data.data;
                                $('#modal').css('visibility', 'visible');
                                $('#tt').empty();
                                $.each(alldata, function(index, item) {
                                    var $row = $('<tr id="' + item.id + ',' + codeval +
                                        '">');
                                    $row.append('<td>' + item.ledgerCode + '</td>');
                                    $row.append('<td>' + item.name + '</td>');
                                    $row.append('<td>' + item.groupcode + '</td>');
                                    $row.append('<td>' + item.gname + '</td>');
                                    $('#myTableproducts').append($row);
                                });
                                let selectedRowIndex = -1;

                                function selectRow(index) {
                                    const rows = $('#myTableproducts tr');
                                    if (index >= 1 && index < rows.length) {
                                        rows.removeClass('selected');
                                        $(rows[index]).addClass('selected');
                                        selectedRowIndex = index;
                                    }
                                }
                                $(document).keydown(function(e) {
                                    const rows = $('#myTableproducts tr');
                                    if (e.key === 'ArrowDown') {
                                        if (selectedRowIndex < rows.length - 1) {
                                            selectRow(selectedRowIndex + 1);
                                        }
                                    } else if (e.key === 'ArrowUp') {
                                        if (selectedRowIndex > 1) {
                                            selectRow(selectedRowIndex - 1);
                                        }
                                    }
                                });
                                selectRow(1);
                            }
                        }
                    });
                }

                $(document).on('keydown', function(event) {
                    if (event.which === 13) {
                        var selectedId = $('#myTableproducts .selected').attr('id');
                        if (selectedId) {
                            var [name, number] = selectedId.split(',');
                            getdatadat(name, number);
                            $('#modal').css('visibility', 'hidden');
                            $('#myTableproducts #tt').empty();
                            $('#dramount' + number).blur();
                            $('#cramount' + number).blur();
                            if (number != 0) {
                                var drcr = $('#drcr' + number).val();
                                var sumdramount = 0;
                                var sumcramount = 0;
                                $('input[name="dramount[]"]').each(function() {
                                    sumdramount += parseFloat($(this).val()) || 0;
                                });
                                $('input[name="cramount[]"]').each(function() {
                                    sumcramount += parseFloat($(this).val()) || 0;
                                });

                                if (drcr == 'Cr') {

                                    $('#cramount' + number).val(0);
                                    $('#dramount' + number).val(0);

                                    $('#cramount' + number).focus().select();
                                    $('#cramount' + number).val(parseInt(sumdramount) - parseInt(
                                        sumcramount));
                                    setTimeout(function() {
                                        $('#cramount' + number).focus().select();
                                    }, 1000);
                                }
                                if (drcr == 'Dr') {

                                    $('#dramount' + number).val(0);
                                    $('#cramount' + number).val(0);

                                    $('#dramount' + number).focus().select();
                                    $('#dramount' + number).val(parseInt(sumcramount) - parseInt(
                                        sumdramount));
                                    setTimeout(function() {
                                        $('#dramount' + number).focus().select();
                                    }, 1000);
                                }

                            } else {

                                if ($('#drcr' + number).val() == 'Cr') {
                                    $('#dramount' + number).val(0);
                                    $('#cramount' + number).val(0);
                                    $('#cramount' + number).focus().select();
                                }

                                if ($('#drcr' + number).val() == 'Dr') {
                                    $('#dramount' + number).val(0);
                                    $('#cramount' + number).val(0);
                                    $('#dramount' + number).focus().select();
                                }
                            }
                        } else {
                            console.log('No row selected.');
                        }
                    }
                });

            }
        });


        function getdatadat(name, number) {
            $.ajax({
                url: "{{ route('getdatadat') }}",
                type: "POST",
                data: {
                    name: name,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(number);
                    $('#code' + number).val(data.ledgerCode);
                    $('#description' + number).val(data.name);
                }
            });
        }

        $(document).on('blur', '[name="dramount[]"]', function() {
            let total = 0;
            $('[name="dramount[]"]').each(function() {
                let value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $('#debitgrandTotal').text(total);
        });

        $(document).on('blur', '[name="cramount[]"]', function() {
            let total = 0;
            $('[name="cramount[]"]').each(function() {
                let value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $('#creditgrandTotal').text(total);
        });

        $(document).on('keypress', 'input[name="dramount[]"]', function(event) {
            if (event.which === 13) {
                event.preventDefault();
                let value = $(this).val();
                let id = $(this).attr('id');
                let codeval = id.replace('dramount', '');
                if (value == '') {
                    $(this).addClass('error');
                } else if (value > 0) {
                    console.log("narration");
                    $('#narration' + codeval).focus().select();
                    $(this).removeClass('error');
                } else {
                    $(this).addClass('error');
                }
            }
        });

        $(document).on('keypress', 'input[name="cramount[]"]', function(event) {
            if (event.which === 13) {
                event.preventDefault();
                let value = $(this).val();
                let id = $(this).attr('id');
                let codeval = id.replace('cramount', '');
                if (value == '') {
                    $(this).addClass('error');
                } else if (value > 0) {
                    console.log("narration");
                    $('#narration' + codeval).focus().select();
                    $(this).removeClass('error');
                } else {
                    $(this).addClass('error');
                }
            }
        });


        $(document).on('keydown', function(event) {
            if (event.key === "Escape") {

                $('#modal').css('visibility', 'hidden');
                $('#tt').empty();

                var focusedElement = $(document.activeElement);
                var focusedElementId = focusedElement.attr('id');

                if (focusedElementId && focusedElementId.startsWith('description')) {

                    let codeval = focusedElementId.replace('description', '');
                    console.log("Focused element ID on Escape:", codeval);
                    $('#code' + codeval).focus().select();
                } else {
                    console.log("No ID found for the focused element.");
                }

            }
        });

        $(document).on('keypress', 'input[name="narration[]"]', function(event) {
            if (event.which === 13) { // Check if Enter key is pressed
                event.preventDefault();

                let id = $(this).attr('id');
                let codeval = id.replace('narration', '');
                let nextval = parseInt(codeval) + 1;

                let sumdramount = 0,
                    sumcramount = 0;

                // Sum all dramount[]
                $('input[name="dramount[]"]').each(function() {
                    sumdramount += parseFloat($(this).val()) || 0;
                });

                // Sum all cramount[]
                $('input[name="cramount[]"]').each(function() {
                    sumcramount += parseFloat($(this).val()) || 0;
                });

                if (sumdramount !== sumcramount) {
                    // Append a new row
                    let newRow = `
                    <tr>
                        <td class="col-sm-1">
                            <input class="form-control tble-imp" name="drcr[]" id="drcr${nextval}" type="text">
                        </td>
                        <td class="col-sm-1">
                            <input class="form-control tble-imp" name="code[]" id="code${nextval}" type="text">
                        </td>
                        <td class="col-sm-3">
                            <input class="form-control tble-imp" readonly name="description[]" id="description${nextval}" type="text">
                        </td>
                        <td class="col-sm-1">
                            <input class="form-control tble-imp" name="dramount[]" id="dramount${nextval}" type="text">
                        </td>
                        <td class="col-sm-1">
                            <input class="form-control tble-imp" name="cramount[]" id="cramount${nextval}" type="text">
                        </td>
                        <td class="col-sm-4">
                            <input class="form-control tble-imp" name="narration[]" id="narration${nextval}" type="text">
                        </td>
                        <td class="col-sm-1">
                            <a class="deleteRow">
                                <i class="fa-sharp fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                `;
                    $('#tbody').append(newRow);

                    // Focus on drcr of the new row
                    $('#drcr' + nextval).focus().select();
                } else {
                    // If amounts match, move focus to next row if exists
                    if ($('#narration' + nextval).length) {
                        $('#drcr' + nextval).focus().select();
                    } else {
                        // If all rows are balanced, focus the save button
                        $('#savebtton').focus().css('background-color', 'rgb(66, 142, 163) !important').trigger(
                            'submit');
                    }
                }
            }
        });

        // Remove row on clicking delete & recalculate total
        $(document).on('click', '.deleteRow', function() {
            $(this).closest('tr').remove();

            // Recalculate dramount[] and cramount[] after deletion
            let sumdramount = 0,
                sumcramount = 0;
            $('input[name="dramount[]"]').each(function() {
                sumdramount += parseFloat($(this).val()) || 0;
            });

            $('input[name="cramount[]"]').each(function() {
                sumcramount += parseFloat($(this).val()) || 0;
            });

            // If equal after deletion, move focus to save button
            if (sumdramount === sumcramount) {
                $('#savebtton').focus().css('background-color', 'rgb(66, 142, 163) !important').trigger('submit');
            }
        });

        $('#inputform').on('submit', function(event) {
            event.preventDefault();

            var sumdramount = 0;
            var sumcramount = 0;

            $('input[name="dramount[]"]').each(function() {
                sumdramount += parseFloat($(this).val()) || 0;
            });
            $('input[name="cramount[]"]').each(function() {
                sumcramount += parseFloat($(this).val()) || 0;
            });

            if (sumdramount === sumcramount) {
                var formData = $(this).serialize();

                let url = $('#voucherId').val() ? "{{ route('updatevouchar') }}" : '{{ route('submitvoucher') }}';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $("#myTable tbody tr").remove();
                            $('#inputform')[0].reset();
                            $("#type-success").trigger("click");
                            setTimeout(function() {
                                notify(res.messages, 'success');
                            }, 1000);
                            window.location.href = "{{ route('journalVoucher.index') }}";

                        } else {
                            notify(res.messages, 'warning');
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                alert('Entries do not match');
            }
        });

        $(document).on('click', '.deletebtn', function(event) {
            event.preventDefault();

            let id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: `You are about to delete transaction ID #${id}. This action cannot be undone!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#ledgerModal').modal('hide'); // Hide the modal before deletion starts

                    Swal.fire({
                        title: "Deleting...",
                        text: "Please wait while we delete the transaction.",
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        url: "{{ route('deletevouchares') }}",
                        type: 'POST',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                swal.close();
                                window.location.href = "{{ route('journalVoucher.index') }}";
                            } else {
                                swal.close();
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function(jqXHR, textStatus) {

                            if (textStatus === "timeout") {
                                Swal.fire({
                                    title: "Timeout!",
                                    text: "The server is taking too long to respond. Please try again later.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong. Please try again.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.voucharedit', function(event) {
            event.preventDefault();

            let id = $(this).data('id');
            $.ajax({
                url: "{{ route('editvouchars') }}",
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        let details = res.details;
                        let voucharId = res.voucharId;

                        $('#voucherId').val(voucharId.id);

                        if (Array.isArray(details) && details.length > 0) {
                            $('#tbody').empty();
                            details.forEach((data, index) => {
                                $('#tbody').append(`
                                <tr>
                                    <td class="col-sm-1" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" name="drcr[]" id="drcr${index}" type="text" value="${data.transactionType}">
                                    </td>
                                    <td class="col-sm-1" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" name="code[]" id="code${index}" type="text" value="${data.ledgerCode}">
                                    </td>
                                    <td class="col-sm-3" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" readonly name="description[]" id="description${index}" type="text" value="${data.name}">
                                    </td>
                                    <td class="col-sm-1" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" name="dramount[]" id="dramount${index}" type="text" value="${data.drAmount}">
                                    </td>
                                    <td class="col-sm-1" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" name="cramount[]" id="cramount${index}" type="text" value="${data.crAmount}">
                                    </td>
                                    <td class="col-sm-4" style="text-align:center;">
                                        <input class="form-control tble-imp formInputs" name="narration[]" id="narration${index}" type="text" value="${voucharId.narration}">
                                    </td>
                                    <td class="col-sm-1" style="text-align:center;">
                                        <a class="delete-row">
                                            <i class="fa-sharp fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            `);
                            });

                            // Attach delete event for dynamically added rows
                            $('.delete-row').on('click', function() {
                                $(this).closest('tr').remove();
                            });

                        } else {
                            notify('No data found', 'info');
                        }

                    } else {
                        notify(res.messages, 'warning');
                    }
                },
                error: function(jqXHR) {
                    notify('Error: ' + jqXHR.responseText, 'warning');
                }
            });
        });
    </script>
@endpush
