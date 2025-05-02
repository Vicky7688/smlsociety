@extends('layouts.app')

@php
    $table = 'yes';
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span>Account Opening Form </h4>
                    </div>
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <small style="position: absolute;right: 196px;">Name:</small> <small
                            style="position: absolute;right: 39px;" id="namehai"></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        {{-- form --}}
                        <form action="javascript:void(0)" id="formData" name="formData">
                            @csrf
                            <div class="row row-gap-2">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">Date</label>
                                    <input type="text" class="form-control formInputs transactionDate"
                                        placeholder="DD-MM-YYYY" id="transactionDate" name="transactionDate"
                                        value="{{ Session::get('currentdate') }}" />
                                    <small class="text-danger error-transactionDate"></small>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label class="form-label" for="MEMBERTYPE">MEMBER TYPE</label>
                                    <select name="membertype" id="membertype" class="form-select form-select-sm">
                                        <option value="Member">Member</option>
                                        <option value="Staff">Staff</option>
                                        <option value="NonMember">Nominal Member</option>
                                    </select>
                                    <small class="text-danger error-transactionDate"></small>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="membershipno" class="form-label">Membership No</label>
                                    <input type="text" class="form-control formInputs" id="membershipno"
                                        name="membershipno" placeholder="Membership No" autocomplete="off" />
                                    <div id="accountList" class="accountList"></div>
                                    <small class="text-danger error-membershipno"></small>
                                </div>


                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="accounttype" class="form-label">Account Type</label>
                                    <select class="form-select formInputsSelect" id="accounttype" name="accounttype"
                                        onchange="getschemes(this.value)">
                                        <option value="">Select</option>
                                        <option value="Saving">Saving</option>
                                        <option value="FD">FD</option>
                                        <option value="RD">RD</option>
                                        {{--  <option value="MIS">MIS</option>
                                    <option value="CDS">CDS</option>  --}}
                                        {{--  <option value="Daily Loan">Daily Loan</option>  --}}
                                        <option value="DailyDeposit">Daily Deposit</option>
                                    </select>
                                    <small class="text-danger error-accounttype"></small>
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 d-none" id="fd-section">
                                    <label class="form-label mb-1" for="fdType"> Select FD Type</label>
                                    <select onchange="getschemeall(this.value)" name="fdType" id="fdType"
                                        class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                        <option value="">Select</option>
                                        @foreach ($FdTypeMaster as $item)
                                            <option value="{{ $item->id }}">{{ $item->type }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger error-fdType"></small>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="schemetype" class="form-label">Scheme Type</label>
                                    <select class="form-select formInputsSelect" id="schemetype" name="schemetype"
                                        onchange="getschemesamount(this.value)">
                                        <option value="">Select</option>
                                    </select>
                                    <small class="text-danger error-schemetype"></small>
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="accountNo" class="form-label">Account No</label>
                                    <input type="text" class="form-control formInputs" id="accountNo" name="accountNo"
                                        placeholder="Account No" autocomplete="off" />
                                    <small class="text-danger error-accountNo"></small>
                                </div>
                                <script>
                                    function getschemes(name) {
                                        if (name === 'FD') {
                                            $('#fd-section').removeClass('d-none').show(); // Show FD section
                                        } else {
                                            $('#fd-section').addClass('d-none').hide(); // Hide FD section
                                        }
                                        $.ajax({
                                            url: '{{ route('getschemes') }}',
                                            type: 'get',
                                            data: {
                                                name: name
                                            },
                                            dataType: 'json',
                                            success: function(response) {

                                                $('#schemetype').empty();
                                                $('#schemetype').append($('<option>', {
                                                    value: '',
                                                    text: 'Select '
                                                }));

                                                response.forEach(function(scheme) {
                                                    $('#schemetype').append(
                                                        $('<option>', {
                                                            value: scheme.id,
                                                            text: scheme.name
                                                        })
                                                    );
                                                });
                                            },
                                            error: function(jqXHR, exception) {
                                                showError(jqXHR, form);
                                            }
                                        });
                                    }
                                </script>
                                <script>
                                    function getschemeall(id) {
                                        // alert(id);
                                        $.ajax({
                                            url: '{{ route('getschemeall') }}',
                                            type: 'get',
                                            data: {
                                                id: id
                                            },
                                            dataType: 'json',
                                            success: function(response) {

                                                $('#schemetype').empty();
                                                $('#schemetype').append($('<option>', {
                                                    value: '',
                                                    text: 'Select '
                                                }));

                                                response.forEach(function(scheme) {
                                                    $('#schemetype').append(
                                                        $('<option>', {
                                                            value: scheme.id,
                                                            text: scheme.name
                                                        })
                                                    );
                                                });
                                            },
                                            error: function(jqXHR, exception) {
                                                showError(jqXHR, form);
                                            }
                                        });

                                    }
                                </script>

                                <script>
                                    function getschemesamount(id) {

                                        $.ajax({
                                            url: '{{ route('getschemesamount') }}',
                                            type: 'get',
                                            data: {
                                                id: id
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                var amountttt = $('#amount').val();
                                                var years = response.years || 0;
                                                var months = response.months || 0;
                                                var days = response.days || 0;

                                                // Calculate total days
                                                var totalDays = (years * 365) + (months * 30) + days;


                                                var transactionDate = $('#transactionDate').val(); // e.g., "01-10-2024"

                                                // Parse the date string into a Date object
                                                var parts = transactionDate.split('-'); // Split by '-'
                                                var date = new Date(parts[2], parts[1] - 1, parts[0]); // Note: months are 0-indexed

                                                // Add 364 days
                                                date.setDate(date.getDate() + totalDays);

                                                // Format the new date back to the desired format (DD-MM-YYYY)
                                                var newTransactionDate = ('0' + date.getDate()).slice(-2) + '-' +
                                                    ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                                                    date.getFullYear();

                                                $('#maturitydate').val(newTransactionDate); // This will log the new date

                                                var parts = transactionDate.split('-'); // Split by '-'
                                                // Set the calculated days in the input field
                                                $('#tanure').val(totalDays);
                                                $('#roi').val(response.interest);
                                                calculate();
                                                // lock-in period in days  start
                                                var years = response.lockin_years || 0;
                                                var months = response.lockin_months || 0;
                                                var days = response.lockin_days || 0;
                                                var totalDays = (years * 365) + (months * 30) + days;
                                                $('#lockinperiod').val(totalDays);
                                                getlockdate(totalDays);
                                            },
                                            error: function(jqXHR, exception) {
                                                showError(jqXHR, form);
                                            }
                                        });
                                    }
                                </script>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="roi" class="form-label">ROI</label>
                                    <input type="text" class="form-control formInputs" id="roi" name="roi"
                                        placeholder="ROI" autocomplete="off" oninput="calculate()" />
                                    <small class="text-danger error-roi"></small>
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select formInputsSelect">
                                        <option value="Active" selected>Active</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Trfd">Transfer</option>
                                    </select>
                                    <small class="text-danger error-status"></small>
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding savingColumnButton">
                                    <div class="d-flex h-100 justify-content-end text-end">
                                        <button type="submit" id="submitButton"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- form end --}}
                    </div>
                </div>
            </div>
        </div>
        <script>
            function getlockdate(id) {
                function addDays(date, days) {
                    const newDate = new Date(date.getTime() + days * 24 * 60 * 60 * 1000);
                    return newDate;
                }
                var transactionDate = $('#transactionDate').val();
                var dateParts = transactionDate.split('-');
                var day = parseInt(dateParts[0], 10);
                var month = parseInt(dateParts[1], 10) - 1;
                var year = parseInt(dateParts[2], 10);
                var date = new Date(year, month, day);
                const daysToAdd = id;
                var newDate = addDays(date, daysToAdd);
                var lockedDate = ('0' + newDate.getDate()).slice(-2) + '-' +
                    ('0' + (newDate.getMonth() + 1)).slice(-2) + '-' +
                    newDate.getFullYear();
                $("#lockindate").val(lockedDate);
            }


            function calculate() {
                var amount = parseFloat($('#amount').val());
                var roi = parseFloat($('#roi').val());
                var tanure = parseFloat($('#tanure').val());

                // Convert tenure from days to years
                var tenureInYears = tanure / 365;

                // Calculate maturity amount
                var maturityAmount = amount + (amount * (roi / 100) * tenureInYears);
                if (!isNaN(maturityAmount)) {
                    $('#maturityamount').val(maturityAmount.toFixed(2));
                }

            }
        </script>
        <div class="card tablee">
            <div class="card-body data_tables">
                <div class="table-responsive tabledata">
                    <table class="table text-center table-bordered" id="table">
                        <thead class="table_head verticleAlignCenter">
                            <tr>
                                <th scope="col">Sr.</th>
                                <th scope="col">transactionDate</th>
                                <th scope="col">Type</th>
                                <th scope="col">accountNo</th>
                                <th scope="col">accountname</th>
                                <th scope="col">schemename</th>
                                {{-- <th scope="col">amount</th> --}}
                                <th scope="col">roi</th>
                                {{-- <th scope="col">tanure</th> --}}
                                {{-- <th scope="col">maturityamount</th> --}}
                                {{-- <th scope="col">maturitydate</th> --}}
                                {{-- <th scope="col">lockinperiod</th> --}}
                                {{-- <th scope="col">lockinDate</th> --}}
                                <th scope="col">Agent</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ..........Modals.......... -->
    <div class="modal modal-lg fade" id="modifyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered small_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Details</h5>
                    <h5 class="modal-title"><span class="text-muted fw-light">Previous Balance: </span><span
                            id="previousBalance"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="modifiedFormData" name="modifiedFormData">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="modifyId" name="modifyId">
                        <div class="row row-gap-3 justify-content-center">
                            <div class="col-md-6 col-12 inputesPadding">
                                <label for="modifiedTransactionDate" class="form-label">Date</label>
                                <input type="date" class="form-control formInputs" placeholder="YYYY-MM-DD"
                                    id="modifiedTransactionDate" name="modifiedTransactionDate"
                                    max="{{ now()->format('Y-m-d') }}" />
                                <small class="text-danger error"></p>
                            </div>

                            <div class="col-md-6 col-12 inputesPadding">
                                <label for="modifiedTransactionAmount" class="form-label">Amount</label>
                                <input type="text" step="any" min="0" class="form-control formInputs"
                                    id="modifiedTransactionAmount" name="modifiedTransactionAmount" />
                                <small class="text-danger error"></p>
                            </div>

                            <div class="col-md-12 col-12 inputesPadding">
                                <label for="modifiedNarration" class="form-label">Narration</label>
                                <input type="text" readonly class="form-control formInputs" id="modifiedNarration"
                                    name="modifiedNarration" />
                                <small class="text-danger error"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade delete_modal" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="javascript:void(0)" id="deleteFormData" name="deleteFormData">
                    @csrf
                    <input type="hidden" id="deleteId" name="deleteId">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .tablee table th,
        .tablee table td {
            padding: 8px;
        }

        .saving_column {
            position: relative;
        }

        .saving_column .error {
            position: absolute;
            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }

        .page_headings h4,
        .page_headings h6 {
            margin-bottom: 0;
        }

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
        }

        .accountList ul {
            position: absolute;
            left: 12px;
            bottom: 0px;
            transform: translateY(90%);
            width: calc(100% - 24px);
            background-color: aliceblue;
            border: 1px solid #fff;
            border-radius: 5px;
            max-height: 100px;
            overflow-y: auto;
            z-index: 99;
        }

        .accountList ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert CDN -->
    <script>
        $(document).ready(function() {

            $('#paymentType').change(function() {
                $("#bank").find("option").not(":first").remove();
                var groupCode = $(this).val();
                if (groupCode == 'BANK001') {
                    $.ajax({
                        url: '{{ route('getLedger') }}',
                        type: 'get',
                        data: {
                            groupCode: groupCode
                        },
                        dataType: 'json',
                        success: function(response) {
                            $("#bank").find("option").remove();
                            $.each(response["ledgers"], function(key, item) {
                                $("#bank").append(
                                    `<option value='${item.ledgerCode}'>${item.name}</option>`
                                )
                            });
                        },
                        error: function(jqXHR, exception) {
                            showError(jqXHR, form);
                        }
                    });
                } else {
                    $("#bank").html('');
                    $("#bank").append(
                        `<option value='C002'>Cash</option>`
                    )
                }
            });

            $('#modifiedPaymentType').change(function() {
                var form = $('#modifiedPaymentType');
                $("#modifiedBank").find("option").not(":first").remove();
                var groupCode = $(this).val();
                $.ajax({
                    url: '{{ route('getLedger') }}',
                    type: 'get',
                    data: {
                        groupCode: groupCode
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#modifiedBank").find("option").remove();
                        $.each(response["ledgers"], function(key, item) {
                            $("#modifiedBank").append(
                                `<option value='${item.ledgerCode}'>${item.name}</option>`
                            )
                        });
                    },
                    error: function(jqXHR, exception) {
                        showError(jqXHR, form);
                    }
                });
            });

            // -------------------- Form Handling Javascript (Starts) -------------------- //


            $(document).on('submit', '#formData', function(event) {
                event.preventDefault();
                var element = $(this);
                var form = $('#formData');

                // Clear previous error messages
                $('.text-danger').text(''); // Clear all error messages

                $("button[type=submit]").prop('disabled', true); // Disable the submit button

                $.ajax({
                    url: '{{ route('addaccount') }}',
                    type: 'post',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            alert(response.message);
                            var membershipno = $('#membershipno').val();
                            var membertype = $('#membertype').val();
                            displayTable(membertype, membershipno);
                            form[0].reset(); // Reset the form
                        } else {


                            {{--  alert(response.errors.message);  --}}
                            // Loop through errors and display them next to fields
                            for (var field in response.errors) {
                                let errormessages = response.errors[field][0];
                                alert(errormessages);
                                $('.error-' + field).text(response.errors[field][
                                0]); // Display the first error message
                            }
                        }
                    },
                    error: function(xhr) {
                        // Handle server error
                        if (xhr.status === 422) { // Unprocessable Entity
                            for (var field in xhr.responseJSON.errors) {
                                $('.error-' + field).text(xhr.responseJSON.errors[field][0]);
                            }
                        } else {
                            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr
                                .responseJSON.error : 'An unexpected error occurred.';
                            alert('Error: ' + errorMessage);
                        }
                    },
                    complete: function() {
                        $("button[type=submit]").prop('disabled',
                        false); // Re-enable the submit button
                    }
                });
            });


            // Modify Data
            $(document).on('click', '.modify', function(event) {
                event.preventDefault();
                var modifyId = $(this).attr('modifyId');
                $.ajax({
                    url: "{{ route('saving.edit', '') }}/" +
                        modifyId,
                    type: "GET",
                    success: function(response) {
                        if (response['status'] == true) {
                            $("#modifiedBank").find("option").remove();
                            $.each(response.ledgers, function(key, item) {
                                $("#modifiedBank").append(
                                    `<option value='${item.ledgerCode}'>${item.name}</option>`
                                )
                            });
                            $('#previousBalance').html(response.previousBalance);
                            $('#modifyId').val(modifyId);
                            $('#modifiedTransactionDate').val(response.data.transactionDate);
                            $('#modifiedTransactionType').val(response.data.transactionType);
                            $('#modifiedTransactionAmount').val(response.data.depositAmount ?
                                response.data.depositAmount : response.data.withdrawAmount);
                            $('#modifiedPaymentType').val(response.data.paymentType);
                            $('#modifiedBank').val(response.data.bank);
                            $('#modifiedNarration').val(response.data.narration);
                            // $('#modifiedAgentId').val(response.data.agentId);
                        }
                    }
                });
                $('#modifyModal').modal('show');
            });

            $('#modifiedFormData').submit(function(event) {
                event.preventDefault();
                var element = $(this);
                var form = $('#modifiedFormData');
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('saving.update') }}',
                    type: 'post',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $('#modifyModal').modal('hide');
                            displayTable(memberType, accountNo);
                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='text'],input[type='number'],select").removeClass(
                                'is-invalid');
                            notify(response.message, 'success');
                        } else {
                            var errors = response.errors;
                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='text'],input[type='number'],select").removeClass(
                                'is-invalid');
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid').siblings('p')
                                    .addClass('invalid-feedback').html(value);
                            });
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            });

            $(document).on('click', '.delete', function(event) {
                event.preventDefault();
                var deleteId = $(this).attr('deleteId');
                $('#deleteId').val(deleteId);
                $('#deleteModal').modal('show');
            });

            $('#deleteFormData').submit(function(event) {
                event.preventDefault();
                var element = $(this);
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('saving.delete') }}',
                    type: 'delete',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $('#deleteModal').modal('hide');
                            displayTable(memberType, accountNo);
                            alert(response.messages);
                        } else {
                            $('#deleteModal').modal('hide');
                            alert(response.messages);
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log("Something went wrong");
                    }
                });
            });
        });
        // -------------------- Form Handling Javascript (Ends) -------------------- //

        function getAccountList() {
            var memberType = $('#membertype').val();
            var membershipno = $('#membershipno').val();
            $.ajax({
                url: "{{ route('saving.getData') }}",
                type: "GET",
                data: {
                    memberType: memberType,
                    accountNo: membershipno
                },
                dataType: 'json',
                success: function(response) {
                    if (response['status'] == true) {
                        $("#accountList").html(response.data);
                    }
                }
            });
        }
        $("#memberType").on('change', getAccountList);
        $("#membershipno").on('keyup', getAccountList);


        $(document).on('click', '#accountList .memberlist', function() {
            var accountNo = $(this).text();
            var memberType = $('#membertype').val();
            $('#membershipno').val(accountNo);

            $("#accountList").html("");
            displayTable(memberType, accountNo);
        });

        function displayTable(memberType, accountNo) {

            // $('#membertype').val(memberType);
            // $('#membershipno').val(accountNo);
            $.ajax({
                url: "{{ route('fetdatamm') }}",
                type: "GET",
                data: {
                    memberType: memberType,
                    accountNo: accountNo
                },
                dataType: 'json',
                success: function(response) {
                    if (response['status'] === true) {
                        $('#namehai').html(response.member.name);
                        $('#memberBalance').html(response.balance);
                        var detailRows = response.detail; // Use the 'detail' array from the response
                        var tableBody = $('#tableBody');
                        tableBody.empty();

                        $.each(detailRows, function(index, item) {
                            var row = "<tr>" +
                                "<td>" + (index + 1) + "</td>" +
                                "<td>" + formatDate(item.transactionDate) + "</td>" +
                                "<td>" + item.membertype + "</td>" +
                                "<td>" + item.accountNo + "</td>" +
                                "<td>" + item.accountname + "</td>" +
                                "<td>" + item.schemename + "</td>" +
                                // "<td>" + item.amount + "</td>" +
                                "<td>" + item.roi + "</td>" +
                                // "<td>" + item.tanure + "</td>" +
                                // "<td>" + item.maturityamount + "</td>" +
                                // "<td>" + formatDate(item.maturitydate) + "</td>" +
                                // "<td>" + item.lockinperiod + "</td>" +
                                // "<td>" + item.lockindate + "</td>" +
                                // "<td>" + item.agentname + "</td>" +
                                `<td style="display: flex; justify-content: space-evenly; align-items: center;">
                                <a   onclick=deleteacc(${item.id}) href="javascript:void(0);">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </a>
                            </td></tr>`;
                            tableBody.append(row);
                        });

                        // $("#accountNo").val(accountNo);
                        $("#accountList").html("");
                    }
                }
            });
        }




        function deleteacc(id) {
            $.ajax({
                url: '{{ route('deletefetdatamm') }}',
                type: 'post', // Use the DELETE method
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                data: {
                    id: id
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting data...',
                        willOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                success: function(res) {
                    Swal.close(); // Close the loading alert
                    if (res.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.messages,
                            showConfirmButton: true
                        });


                        displayTable(res.membertype, res.accountNo); // Update table dynamically
                        $('#table').load(location.href + ' .table'); // Reload table content
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: res.messages,
                            showConfirmButton: true
                        });
                    }
                },
                error: function() {
                    Swal.close(); // Ensure SweetAlert is closed even on error
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Something went wrong',
                        showConfirmButton: true
                    });
                },
                complete: function() {
                    // Add any cleanup code if necessary
                }
            });
        }





        $(document).ready(function() {
            $('#fdType').on('change', function() {
                $('#schemetype').val(null);
                var fdTypeValue = $(this).val();
                if (fdTypeValue != '1') {
                    $('#roi, #tanure').attr('readonly', true);
                } else {
                    $('#roi, #tanure').removeAttr('readonly');
                }
            });
        });
    </script>
@endpush
