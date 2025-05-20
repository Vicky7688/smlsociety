@extends('layouts.app')
@section('title', ' Member Share')
@section('pagetitle', ' Member Share')

@php
    $table = 'yes';
@endphp
@section('content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span> Contributions</h4>
                    </div>
                    <div class="col-md-3 accountHolderDetails">
                        <h6 class="m-0"><span class="text-muted fw-light">Name: </span><span id="name"></span></h6>
                        <h6 class="pt-2 mb-0"><span class="text-muted fw-light">Contribution Balance: </span><span
                                id="saving"></span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card mb-3">
                    <div class="card-body cardsY">
                        <form id="contribution" action="{{ route('contributionupdate') }}" method="post">
                            {{ csrf_field() }}
                            <div class="row row-gap-2">
                                <input type="hidden" name="actiontype" value="contributionsave" />
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3  inputesPadding">
                                    <label for="txndate" class="form-label">Date</label>
                                    <input id="transactionDate" type="text" name="transactionDate"
                                        class="form-control formInputs transactionDate"
                                        value="{{ Session::get('currentdate') }}" placeholder="Enter value" required />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3  inputesPadding">
                                    <label class="form-label mb-1" for="status-org">Action </label>
                                    <select name="action" id="action" class="select21 form-select formInputsSelect"
                                        data-placeholder="Active" onchange="sharetrfdsaving('this')">
                                        <option value="deposit">Deposit</option>
                                        <option value="withdrawal">Withdrawal</option>
                                        {{-- <option value="transfer">Transfer</option> --}}
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3  inputesPadding">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        <!-- <option value="NonMember">Non Member</option>
                                                        <option value="Staff">Staff</option> -->
                                    </select>
                                    <p class="error"></p>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3  inputesPadding">
                                    <label for="txndate" class="form-label">Ac number</label>
                                    <input type="text" id="account" name="account" class="form-control formInputs"
                                        placeholder="Enter value" required />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3  inputesPadding">
                                    <label for="txndate" class="form-label">Amount</label>
                                    <input type="text" name="amount" class="form-control formInputs"
                                        placeholder="Enter value" required />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding"
                                    id="groupdiv">
                                    <label for="paymentType" class="form-label">Payment Type</label>
                                    <select class="form-select formInputsSelect" id="groupCode" name="groupCode"
                                        onchange="getledgerCode('this')">
                                        @if (!empty($groups))
                                            <option value="" selected>Select Group</option>
                                            @foreach ($groups as $row)
                                                <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding"
                                    id="ledgerdiv">
                                    <label for="bank" class="form-label">Bank</label>
                                    <select class="form-select formInputsSelect" id="bank" name="bank">
                                        <option value="">Select Group</option>
                                    </select>
                                    <p class="error"></p>
                                </div>
                                <!-- </div>
                                                <div class="row"> -->

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding" id="accountdiv">
                                    <label for="txndate" class="form-label">Saving No</label>
                                    <input type="text" name="saving_no" id="saving_no" class="form-control formInputs"
                                        placeholder="Enter value" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-6 py-3 inputesPadding">
                                    <label for="txndate" class="form-label">Naration</label>
                                    <input type="text" name="naration" class="form-control formInputs"
                                        placeholder="Enter value" />
                                </div>
                                <!-- </div> -->
                                <!-- <div class="row"> -->
                                <!-- <div class=" col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding accountdetails" style="display: none;">
                                            <label for="txndate" class="form-label">Name</label>
                                            <input id="name" type="text" name="name" class="form-control" placeholder="Enter value" required />
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding accountdetails" style="display: none;">
                                            <label for="saving" class="form-label">Balance Share</label>
                                            <input id="saving" type="text" name="saving" class="form-control" placeholder="Enter value" required />
                                    </div> -->
                            </div>

                            <!-- <div class="modal-footer">
                                                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Save</button>
                                                </div> -->
                            <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 inputesPadding savingColumnButton">
                                <div class="d-flex h-100 justify-content-end text-end">
                                    <button id="submitButton"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                        type="submit"
                                        data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                            Loading...">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card ">
                    <!-- <div class="card-body"> -->
                    <div class="card-body table-responsive text-nowrap transactionTabale" style="display: none;">
                        <table class="table text-center table-bordered">
                            <thead class="table_head verticleAlignCenterReport">
                                <tr>
                                    <th>V No</th>
                                    <th>Date</th>
                                    <th>Deposit</th>
                                    <th>Withdraw</th>
                                    <th>Balance</th>
                                    <th>Remarks</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 transactionData">

                            </tbody>
                        </table>
                    </div>
                    <!-- </div> -->
                </div>
                <!-- <div class="container-xxl flex-grow-1 container-p-y">
                                        <div class="card">
                                            <h5 class="card-header">Striped rows</h5>


                                        </div>
                                    </div> -->
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Transaction </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateContribution" action="{{ route('contributionupdate') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="actiontype" value="updatecontribution" />
                    <input type="hidden" name="id" value="" />
                    <div class="modal-body">
                        <div class="row row-gap-2">
                            <div class="col-lg-6 col-sm-6">
                                <label for="txndate" class="form-label">Date</label>
                                <input id="transactionDate" type="text" name="transactionDate"
                                    class="form-control formInputs transactionDate"
                                    value="{{ Session::get('currentdate') }}" placeholder="Enter value" required />
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <label class="form-label mb-1" for="status-org">Action </label>
                                <select name="action" id="action" class="select21 form-select formInputsSelect"
                                    data-placeholder="Active">
                                    <option value="">Select</option>
                                    <option value="Deposit">Deposit</option>
                                    <option value="Withdraw">Withdrawal</option>
                                </select>
                            </div>
                            <!-- </div>
                                            <div class="row"> -->
                            <div class="col-lg-6 col-sm-6">
                                <label for="txndate" class="form-label">Amount</label>
                                <input type="text" name="amount" class="form-control formInputs"
                                    placeholder="Enter value" required />
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <label for="txndate" class="form-label">Naration</label>
                                <input type="text" name="naration" class="form-control formInputs"
                                    placeholder="Enter value" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer me-0">
                        <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="submitButton"
                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
                            data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
@endsection

@push('style')
    <style>
        html:not([dir=rtl]) .modal .btn-close {
            transform: none !important;
        }

        .btn-close {
            top: 1.35rem !important;
        }

        .ui-autocomplete {
            font-weight: bold;
            list-style-type: none;
            padding-top: 5px;
            width: 184px !important;
            background-color: aliceblue;
            border: 1px solid #fff;
        }

        .ui-menu-item {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#contribution").validate({
                rules: {
                    account: {
                        required: true,
                        number: true,
                    },
                    action: {
                        required: true,
                    },
                    transactionDate: {
                        required: true,
                        customDate: true,
                    }
                },
                messages: {
                    account: {
                        required: "Please enter account number",
                        number: "Account number should be numeric",
                    },
                    action: {
                        required: "Please select action type",
                    },
                    transactionDate: {
                        required: "Please enter a date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select21"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#contribution');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html(
                                '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                            ).attr(
                                'disabled', true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');
                        },
                        success: function(data) {

                            if (data.status == "success") {
                                var account = $('#contribution').find(
                                    'input[name="account"]').val();
                                var member = $('#contribution').find(
                                    'select[name="memberType"]').val();
                                var txndate = $('#contribution').find(
                                    'input[name="transactionDate"]').val();
                                getaccountdetails(account, member, txndate);
                                form[0].reset();
                                var currentDate = moment().format('DD-MM-YYYY');
                                $("#transactionDate").val(currentDate);
                                notify("Task Successfully Completed", 'success');
                            } else {
                                notify(data.status, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });

            $('#account').keyup(function() {
                var inputValue = $(this).val();
                $.ajax({
                    url: "{{ route('contributionupdate') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    data: {
                        search: inputValue,
                        'actiontype': "getaccount"
                    },
                    success: function(data) {
                        var accountNumbers = data.data.map(function(item) {
                            return item.accountNo;
                        });
                        console.log(accountNumbers);
                        $("#account").autocomplete({
                            source: accountNumbers,
                            minLength: 0
                        }).focus(function() {
                            $(this).autocomplete("search", "");
                        });
                    },
                    error: function(xhr, status, error) {}
                });
            });




            function deleteshare(id) {
                swal({
                    title: 'Are you sure ?',
                    text: "You want to delete a transaction. It cannot be recovered",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Yes Delete",
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            $.ajax({
                                url: "{{ route('contributionupdate') }}",
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(data) {

                                    if (data.status == "success") {
                                        var account = $('#contribution').find(
                                            'input[name="account"]').val();
                                        var member = $('#contribution').find(
                                            'select[name="memberType"]').val();
                                        var txndate = $('#contribution').find(
                                                'input[name="transactionDate"]')
                                            .val();
                                        getaccountdetails(account, member, txndate);
                                    } else {
                                        notify(data.status, 'warning');
                                    }
                                },
                                error: function(errors) {
                                    showError(errors, form);
                                }
                            });
                        });
                    },
                });
            }



            $("#updateContribution").validate({
                rules: {
                    account: {
                        required: true,
                        number: true,
                    },
                    action: {
                        required: true,
                    },
                    transactionDate: {
                        required: true,
                        customDate: true,
                    }
                },
                messages: {
                    account: {
                        required: "Please enter account number",
                        number: "Account number should be numeric",
                    },
                    action: {
                        required: "Please select action type",
                    },
                    transactionDate: {
                        required: "Please enter a date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select21"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#updateContribution');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html(
                                '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                            ).attr(
                                'disabled', true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');
                        },

                        success: function(data) {
                            if (data.status == "success") {
                                var account = $('#contribution').find(
                                    'input[name="account"]').val();
                                var member = $('#contribution').find(
                                    'select[name="memberType"]').val();
                                var txndate = $('#contribution').find(
                                    'input[name="transactionDate"]').val();
                                getaccountdetails(account, member, txndate);

                                form[0].reset();
                                form.find('button[type="submit"]').html('Submit').attr(
                                    'disabled', false).removeClass('btn-secondary');
                                $('#basicModal').modal('hide');

                                notify("Task Successfully Completed", 'success');
                            } else {
                                $('#basicModal').modal('hide');
                                notify(data.status, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });

            $("#account").change(function() {
                var account = $(this).closest('form').find('input[name="account"]').val();
                var txndate = $(this).closest('form').find('input[name="transactionDate"]').val();
                var member = $('#contribution').find('select[name="memberType"]').val();
                getaccountdetails(account, member, txndate);
                $("#contribution").block({
                    message: '<div class="sk-wave sk-primary mx-auto"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div>',
                    timeout: 1000,
                    css: {
                        backgroundColor: "transparent",
                        border: "0"
                    },
                    overlayCSS: {
                        backgroundColor: "#fff",
                        opacity: 0.8
                    }
                })
            });
        });

        function deletecontribution(id) {
            swal({
                title: 'Are you sure ?',
                text: "You want to delete a transaction. It cannot be recovered",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes Delete",
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !swal.isLoading(),
                preConfirm: () => {
                    return new Promise((resolve) => {
                        $.ajax({
                            url: "{{ route('contributionupdate') }}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            data: {
                                'actiontype': "deletecontribution",
                                'id': id,
                            },
                            success: function(data) {
                                swal.close();
                                if (data.status == "success") {

                                    var account = $('#contribution').find(
                                        'input[name="account"]').val();
                                    var member = $('#contribution').find(
                                        'select[name="memberType"]').val();
                                    var txndate = $('#contribution').find(
                                        'input[name="transactionDate"]').val();
                                    getaccountdetails(account, member, txndate);

                                    swal(
                                        'Deleted',
                                        "Transaction deleted successfully",
                                        'success'
                                    );
                                } else {
                                    swal('Oops!', data.status, 'error');
                                }
                            },
                            error: function(errors) {
                                swal.close();
                                showError(errors, 'withoutform');
                            }
                        });
                    });
                },
            });
        }

        function editSetup(id, transactionDate, depositAmount, withdrawAmount, narration, type) {

            var sharedate = moment(transactionDate).format('DD-MM-YYYY');
            console.log(narration);
            if (narration == null || narration == 'null') {
                narration = '';
            }
            $('#basicModal').find('.msg').text("Edit");
            $('#basicModal').find('input[name="id"]').val(id);
            $('#basicModal').find('select[name="action"]').val(type).trigger('change');
            $('#basicModal').find('input[name="transactionDate"]').val(sharedate);
            if (type == "Withdraw") {
                $('#basicModal').find('input[name="amount"]').val(withdrawAmount);
            } else {
                $('#basicModal').find('input[name="amount"]').val(depositAmount);
            }
            $('#basicModal').find('[name="naration"]').val(narration);
            $('#basicModal').modal('show');
        }

        function getledgerCode() {
            let groups_code = $('#groupCode').val();

            $.ajax({
                url: "{{ route('getledgers') }}",
                type: 'post',
                data: {
                    groups_code: groups_code
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let ledgerDropdown = document.getElementById('bank');
                    ledgerDropdown.innerHTML = '';

                    if (res.status === 'success' && res.ledgers) {
                        let ledgers = res.ledgers;

                        ledgers.forEach((data) => {
                            let option = document.createElement('option');
                            option.value = data.ledgerCode;
                            option.textContent = data.name;
                            ledgerDropdown.appendChild(option);
                        });
                    } else {
                        notify('No ledgers found for the selected group.', 'warning');
                    }
                },
                error: function() {
                    notify('An error occurred while fetching ledgers.', 'warning');
                }
            });
        }

        function getaccountdetails(account, member, txndate) {

            $.ajax({
                url: "{{ route('contributionupdate') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    'account': account,
                    'membertype': member,
                    'transactionDate': txndate,
                    'actiontype': "getdata"
                },
                beforeSend: function() {
                    $('.transactionData').html('');
                    //blockForm('#contribution')
                },
                complate: function() {
                    //  $("#contribution").unblock();
                },
                success: function(data) {
                    //$("#contribution").unblock();
                    if (data.status == "success") {
                        $('#name').text(data.acdetails.name);
                        $('#saving').text(data.balance);
                        // $('#saving_no').val(data.saving_account.accountNo);
                        $(".accountdetails").css("display", "block");
                        $(".transactionTabale").css("display", "block");
                        $(".transactionData").html("");
                        var tbody = '';
                        if (data.openingBal) {
                                // tbody += "<tr>" +
                                //     "<td>-</td>" +
                                //     "<td>" + moment(data.openingBal.transferDate).format('DD-MM-YYYY') + "</td>" +
                                //     // "<td>" + (parseInt(data.openingBal.opening_amount)+parseInt(data.totalBalance)) + "</td>" +
                                //     "<td>" + (parseInt(data.totalBalance)) + "</td>" +
                                //     "<td>" + 0 + "</td>" +
                                //     // "<td>" + (parseInt(data.openingBal.opening_amount)+parseInt(data.totalBalance)) + "</td>" +
                                //     "<td>" + (parseInt(data.totalBalance)) + "</td>" +
                                //     "<td>" + "Opeing Account Balance" + "</td>" +
                                //     "<td>" + " " + "</td>" +
                                //     `<td style="display: flex;justify-content: space-evenly; align-items: center;"></td> `;
                        }
                        if (data.txndetails.length === 0) {} else {
                            if (data.openingBal && data.openingBal.opening_amount) {
                                var balanceAmount = data.openingBal.opening_amount;
                            } else {
                                var balanceAmount = 0;
                            }

                            var srno = 1;
                            var lastbal = "";
                            $.each(data.txndetails, function(index, val) {
                                if (lastbal) {
                                    lastbal = (parseInt(lastbal) + parseInt(val.depositAmount) -
                                        parseInt(val.withdrawAmount));
                                } else {
                                    lastbal = (parseInt(balanceAmount) + parseInt(val.depositAmount) -
                                        parseInt(val.withdrawAmount));
                                }
                                tbody += `<tr>
                                    <td>${srno++}</td>
                                    <td>${moment(val.transactionDate).format('DD-MM-YYYY')}</td>
                                    <td>${val.depositAmount}</td>
                                    <td>${val.withdrawAmount}</td>
                                    <td>${lastbal}</td>
                                    <td>${val.narration}</td>
                                    <td>${val.login.name ? val.login.name : ''}</td>
                                    <td style="display: flex; justify-content: space-evenly; align-items: center;">
                                        <a href="javascript:void(0);" onclick="editSetup('${val.id}', '${val.transactionDate}', '${val.depositAmount}', '${val.withdrawAmount}', '${val.narration}', '${val.transactionType}')">
                                            <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="deletecontribution('${val.id}')">
                                            <i class="fa-solid fa-trash iconsColorCustom"></i>
                                        </a>
                                    </td>
                                </tr>`;
                            });
                            // $.each(data.txndetails, function(index, val) {
                            //     if (lastbal) {
                            //         lastbal = (parseInt(lastbal) + parseInt(val.depositAmount) -
                            //             parseInt(val.withdrawAmount));
                            //     } else {
                            //         lastbal = (parseInt(balanceAmount) + parseInt(val.depositAmount) -
                            //             parseInt(val.withdrawAmount));
                            //     }
                            //     tbody += "<tr>" +
                            //         "<td>" + srno++ + "</td>" +
                            //         "<td>" + moment(val.transactionDate).format('DD-MM-YYYY') +
                            //         "</td>" +
                            //         "<td>" + val.depositAmount + "</td>" +
                            //         "<td>" + val.withdrawAmount + "</td>" +
                            //         "<td>" + lastbal + "</td>" +
                            //         "<td>" + val.narration + "</td>" +
                            //         "<td>" + val.login.name ? val.login.name : '' + "</td>";

                            //     if (val.chequeNo === 'trfdShare') {
                            //         tbody += "<td>" + '-' + "</td>";

                            //     } else if (val.chequeNo === 'trfdSaving') {
                            //         tbody += `<td style="display: flex; justify-content: space-evenly; align-items: center;">

                        //     <a href="javascript:void(0);" onclick="deleteshare('` + val.id + `')">
                        //         <i class="fa-solid fa-trash iconsColorCustom"></i>
                        //     </a>
                        //   </td>`;

                            //     } else {
                            //         tbody += `<td style="display: flex; justify-content: space-evenly; align-items: center;">
                        //             <a href='javascript:void(0);' onclick="editSetup('` + val.id + `','` + val
                            //             .transactionDate + `','` + val.depositAmount + `','` + val
                            //             .withdrawAmount + `','` + val.narration + `','` + val
                            //             .transactionType + `')">
                        //                 <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i>
                        //             </a>
                        //             <a href="javascript:void(0);" onclick="deleteshare('` + val.id + `')">
                        //                 <i class="fa-solid fa-trash iconsColorCustom"></i>
                        //             </a>
                        //           </td>`;
                            //     }

                            //     tbody += "</tr>";

                            // });
                        }

                        // $('#datatable').load(location+' .table');

                        $('.transactionData').html(tbody);
                    } else {
                        $('#name').val();
                        $('#saving').val();
                        $(".accountdetails").css("display", "none");
                        $(".transactionTabale").css("display", "none");
                        $('.transactionData').html('');
                        notify(data.status, 'danger');
                    }
                },
                error: function(error) {
                    // $("#contribution").unblock();
                    notify("Something went wrong", 'warning');
                }
            });
        }


        $(document).ready(function() {
            $('#accountdiv').hide();
            $('#saving_no').val('');
            sharetrfdsaving();
        });

        function sharetrfdsaving() {
            let action = $('#action').val();
            switch (action) {
                case 'deposit':
                case 'withdrawal':
                    $('#accountdiv').hide();
                    $('#saving_no').val('');
                    break;
                case 'transfer':
                    $('#accountdiv').show();
                    break;
                default:
                    $('#accountdiv').hide();
                    $('#saving_no').val('');
            }
        }

        if (document.readyState == "complete") {
            $("#transactionDate").val({{ session('currentdate') }});
        }
    </script>
@endpush
