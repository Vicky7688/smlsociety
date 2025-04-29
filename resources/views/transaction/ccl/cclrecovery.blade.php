@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / Loan / </span>SOD Recovery</h4>
                    </div>
                    <div class="col-md-3 accountHolderDetails">
                        <h6 class=""><span class="text-muted fw-light">Name: </span><span id="member_name"></span></h6>
                        {{--  <h6 class="pt-2"><span class="text-muted fw-light">Father Name: </span><span id="member_fathername"></span></h6>  --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardHeadingTitle">
                        <div class="row">
                            <div class="col-12">
                                <div class="tab-content tableContent mt-2" id="myTabsContent">
                                    <div class="tab-pane fade show active" id="rd_details" role="tabpanel"
                                        aria-labelledby="rd-details-tab">
                                        <!-- Content for Account Details tab -->
                                        <form id="cclform">
                                            <div class="rd_details-modern">
                                                <div class="rd_details_inner">
                                                    <div class="row">
                                                        <input type="text" hidden name="account_number"
                                                            id="account_number">
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3saving_column">
                                                            <label class="form-label" for="opening date">DATE</label>
                                                            <input type="text" id="opening_date" name="opening_date"
                                                                value="{{ Session::get('currentdate') }}"
                                                                class="form-control transactionDate valid form-select-sm">
                                                        </div>

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="Member Type">MEMBER TYPE</label>
                                                            <select name="member_type" id="member_type"
                                                                class="form-select form-select-sm"
                                                                onchange="memberType(this)">
                                                                <option value="Member">Member</option>
                                                                <option value="Staff">Staff</option>
                                                                <option value="NonMember">Nominal Member</option>
                                                            </select>
                                                        </div>

                                                        <input type="hidden" class="membershipnumbers">
                                                        <input type="hidden" name="sodid" id="sodid">

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="account_no_label">SOD A/c</label>
                                                            <input type="text" id="ccl_account" name="ccl_account"
                                                                class="form-control form-control-sm"
                                                                onkeyup="getmemberlist(this)" autocomplete="off">
                                                            <div id="accountList" class="accountList"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-1" id="tabless">
            <div class="tabledata card tablee">
                <div class="card-body" style="overflow-x: auto;">
                    <table class="table datatables-order table-bordered" id="datatabless" style="width:100%">
                        <thead class="table_head thead-light">
                            <tr>
                                <th>DATE</th>
                                {{--  <th>Mem. No</th>  --}}
                                <th>SOD AC</th>
                                <th>CCL Aprroved</th>
                                <th>Used CCL</th>
                                <th>RECEIVED CCL</th>
                                <th>BAL. CCL</th>
                                <th>ROI%</th>
                                <th>Tenure</th>
                                <th>END DATE</th>
                                <th>STATUS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="accountTbody">
                            <tr>
                                <td colspan="12">No Record Available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ccltrfdModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">SOD Transactions</h5>
                        <small class="modal-title membernamesssss" id="membernamesssss" style="color: white"></small>
                    </div>
                    <div class="modal-body">
                        <form id="ccltrfdForm" name="ccltrfdForm">
                            <div class="row">
                                <h6 class="modal-title membername" id="membername"></h6>
                                <input type="hidden" name="cclId" id="cclId">
                                <input type="hidden" name="savingId" id="savingId">
                                <input type="hidden" name="cclmemberType" id="cclmemberType">
                                <input type="hidden" name="updateId" id="updateId">
                                <input type="hidden" name="trfdupdatememberType" id="trfdupdatememberType">
                                <input type="hidden" name="membershipnumbers" id="membershipnumbers"
                                    class="membershipnumbers">


                                <div class="col-md-3 pt-2">
                                    <label class="form-label">TRFD Date</label>
                                    <input id="transcationDate" type="text" name="transcationDate"
                                        class="form-control transactionDate form-select-sm"
                                        value="{{ Session::get('currentdate') }}" placeholder="DD-MM-YYYY"
                                        onblur="previousinterest(this)" />
                                </div>


                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Payment Type</label>
                                    <select name="paymenttype" id="paymenttype" class="form-select form-select-sm"
                                        onchange="paymentTypes(this)">
                                        <option value=""selected>Select Type</option>
                                        <option value="Cash">Cash/Bank</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>



                                <div class="col-md-3 pt-2" id="groupdiv">
                                    <label class="form-label">Payment Type</label>
                                    <select name="cashbankgroup" id="cashbankgroup" class="form-select form-select-sm"
                                        onchange="getcashbankledgercodes(this)">
                                        <option value=""selected>Select Type</option>
                                        @if (!empty($groups))
                                            @foreach ($groups as $row)
                                                <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3 pt-2" id="ledgerdiv">
                                    <label class="form-label">Payment Type</label>
                                    <select name="cashbankledger" id="cashbankledger" class="form-select form-select-sm">
                                        <option value=""selected>Select Type</option>

                                    </select>
                                </div>

                                <div class="col-md-3 pt-2" id="savingaccoundiv">
                                    <label class="form-label">Saving A/c</label>
                                    <input id="saving_account" type="text" name="saving_account" readonly
                                        class="form-control  form-select-sm" placeholder="Saving Account No" />
                                </div>

                                <input id="balance_amount" type="hidden" name="balance_amount" class="form-control" />

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Transfer Amount</label>
                                    <input id="trfdamount" type="text" name="trfdamount"
                                        class="form-control  form-select-sm" placeholder="TRFD Amount"
                                        onkeyup="checkExceedBalanceCcl(this)" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Interest Amount</label>
                                    <input id="trfd_interest_amount" type="text" name="trfd_interest_amount"
                                        class="form-control  form-select-sm" placeholder="Interest Amount"
                                        onkeyup="checkExceedBalanceCcl(this)" />
                                </div>


                                <div class="col-md-6 pt-2">
                                    <label class="form-label">Narration</label>
                                    <input type="text" name="narration" id="narration"
                                        class="form-control  form-select-sm" autocomplete="off">
                                </div>
                            </div>

                    </div>
                    {{--  <hr>  --}}
                    <div class="modal-footer mt-2">
                        <button type="button" class="btn btn-danger closetrfdbutton">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ledgerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalreciveTitle">SOD Ledger</h5>
                        <h6 class="modal-title membernamesssss" id="dsds" style="color: white"></h6>
                        <h6 class="modal-title accountnumbers" id="accountnumbers" style="color: white"></h6>

                        {{--  <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>CCL DATE</th>
                                        <th>CCL Approved</th>
                                        <th>Withdraw Amt.</th>
                                        <th>Deposit Amt.</th>
                                        <th>Received Intt.</th>
                                        <th>Bal. Amt.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgersbody" style="max-height: 300px; overflow-y: auto;">
                                    <tr>
                                        <td colspan="7" class="text-center">No Record Available</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="m-4 text-end ">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="receiptModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalreciveTitle">SOD Recovery</h5>
                        <small class="modal-title membernamesssss" id="nmm" style="color: white"></small>
                    </div>
                    <div class="modal-body">
                        <form id="cclRecoveryForm" name="cclRecoveryForm">
                            <div class="row">
                                <input type="hidden" name="cclid" id="cclid">
                                <input type="hidden" name="rcclmemberType" id="rcclmemberType">
                                <input type="hidden" name="updaterecoveryId" id="updaterecoveryId">
                                <input type="hidden" name="membershipnumbers" id="membershipnumbers"
                                    class="membershipnumbers">

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Receipt Date</label>
                                    <input id="receipt_date" type="text" name="receipt_date"
                                        class="form-control transactionDate form-select-sm"
                                        value="{{ Session::get('currentdate') }}" placeholder="DD-MM-YYYY"
                                        onblur="checkdateinterest(this)" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Principal</label>
                                    <input id="principal" type="text" name="principal"
                                        class="form-control form-select-sm" placeholder="Principal Amount" />
                                </div>

                                <input type="hidden" id="actual_principal" name="actual_principal">

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">ROI%</label>
                                    <input id="rate_of_interest" type="text" name="rate_of_interest"
                                        class="form-control form-select-sm" placeholder="Rate Of Interest" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Interest Amount</label>
                                    <input id="interest_amount" type="text" name="interest_amount"
                                        class="form-control form-select-sm" placeholder="Interest Amount"
                                        oninput="changeinterestAmount(this)" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Total Amount</label>
                                    <input id="total_amount" type="text" name="total_amount"
                                        class="form-control form-select-sm" placeholder="Total Amount" readonly />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Principal Amount</label>
                                    <input id="receipt_amount" type="text" name="receipt_amount"
                                        class="form-control form-select-sm" onkeyup="checkRecoveryNoExceed(this)" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label for="paymentType" class="form-label">Payment Type</label>
                                    <select class="form-control form-select-sm" id="groupCode" name="groupCode"
                                        onchange="getledgerCode(this)">
                                        @if (!empty($groups))
                                            <option value="" selected>Select Group</option>
                                            @foreach ($groups as $row)
                                                <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label for="bank" class="form-label">Cash/Bank</label>
                                    <select class="form-select form-select-sm" id="ledgerCode" name="ledgerCode">
                                        <option value="">Select Group</option>
                                    </select>
                                    <p class="error"></p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Narration</label>
                                    <input type="text" name="receipt_narration" id="receipt_narration"
                                        class="form-control form-select-sm" autocomplete="off" />
                                </div>

                                <div class="col-md-6 text-end pt-3 mt-1">
                                    <button type="button" class="btn btn-danger receiptclosebtn">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('script')
    <script>
        {{--  ccl_account memberType  --}}

        $('#savingaccoundiv').hide();
        $('#groupdiv').hide();
        $('#ledgerdiv').hide();

        function paymentTypes(ele) {
            let paymentType = $(ele).val();
            let id = $('#sodid').val();
            let transcationDate = $('#transcationDate').val();



            if (paymentType === 'Cash') {
                $('#savingaccoundiv').hide();
                $('#saving_account').val('');
                $('#groupdiv').show();
                $('#ledgerdiv').show();
            } else if (paymentType === "Transfer") {
                $('#savingaccoundiv').show();
                $('#groupdiv').hide();
                $('#ledgerdiv').hide();
            } else {
                $('#savingaccoundiv').hide();
                $('#saving_account').val('');
                $('#groupdiv').hide();
                $('#ledgerdiv').hide();
            }


            $.ajax({
                url: "{{ route('cclamounttrfdsaving') }}",
                type: 'POST',
                data: {
                    id: id,
                    transcationDate: transcationDate
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        let cclDetails = res.cclDetails || {};
                        let saving_account = res.saving_account || {};
                        let principal = parseFloat(res.principal) || 0;
                        let interestRate = parseFloat(res.interestRate) || 0;
                        let days = parseFloat(res.days) || 0;

                        let interest_amount = Math.round((((principal * interestRate) / 100) / 365) * days);

                        if (paymentType === 'Cash') {
                            $('#cclId').val(cclDetails.id || '');
                            $('#cclmemberType').val(cclDetails.memberType || '');
                            $('#trfd_interest_amount').val(interest_amount);
                            $('#saving_account').val('');
                            $('#balance_amount').val('');
                            $('#savingId').val('');
                        } else {
                            $('#cclId').val(cclDetails.id || '');
                            $('#savingId').val(saving_account.id || '');
                            $('#saving_account').val(saving_account.accountNo || '');
                            $('#cclmemberType').val(cclDetails.memberType || '');
                            $('#trfd_interest_amount').val(interest_amount);
                        }
                    } else {
                        notify(res.messages, 'warning');
                        resetFormFields();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    notify('An error occurred while processing the request.', 'danger');
                }
            });
        }

        // Helper function to reset form fields
        function resetFormFields() {
            $('#cclId').val('');
            $('#saving_account').val('');
            $('#balance_amount').val('');
            $('#savingId').val('');
            $('#cclmemberType').val('');
            $('#trfd_interest_amount').val('').prop('readonly', true);
        }

        function getcashbankledgercodes(ele) {
            let group = $(ele).val();

            $.ajax({
                url: "{{ route('getcashbankledgercodes') }}",
                type: 'post',
                data: {
                    group: group
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let ledgers = res.ledgers;
                        if (Array.isArray(ledgers) && ledgers.length > 0) {
                            $('#cashbankledger').empty();
                            ledgers.forEach((data) => {
                                $('#cashbankledger').append(
                                    `<option value="${data.ledgerCode}">${data.name}</option>`);
                            });
                        } else {
                            $('#cashbankledger').append(`<option value=""selected>Select Ledger</option>`);
                            notify(res.messages, 'warning');
                        }
                    } else {
                        $('#cashbankledger').append(`<option value=""selected>Select Ledger</option>`);
                        notify(res.messages, 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Ajax Not Working');
                    notify(res.messages, 'warning');
                }
            });
        }

        function memberType(ele) {
            // Clear the fields
            $('#ccl_account').val('');
            $('#member_name').text('');
            $('.membernamesssss').text('');
            $('#balance_amount').val('');
            $('#rcclmemberType').val('');

            // Reload the table content
            $('#datatabless').load(location.href + ' .table');
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

        function getmemberlist(ele) {
            let accountNo = $(ele).val();
            let memberType = $('#member_type').val();

            $.ajax({
                url: "{{ route('getcclaccountlist') }}",
                type: 'POST',
                data: {
                    memberType: memberType,
                    accountNo: accountNo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    let accountListDropdown = $('#accountList');
                    accountListDropdown.empty();

                    if (res.status === 'success') {
                        let allDetails = res.allDetails;

                        if (Array.isArray(allDetails) && allDetails.length > 0) {
                            allDetails.forEach((data) => {
                                accountListDropdown.append(
                                    `<div class="membernumber" data-id="${data.cclNo}">${data.cclNo}</div>`
                                );
                            });

                        } else {
                            $('#datatabless').load(location.href + ' .table');
                            accountListDropdown.append(`<div class="membernumber">No Account Found</div>`);
                            notify(res.messages || 'No accounts available.', 'warning');
                        }
                    } else {
                        accountListDropdown.append(`<div class="membernumber">No Account Found</div>`);
                        notify(res.messages || 'No accounts available.', 'warning');
                    }
                }
            });
        }

        function getledgerCode(ele) {
            let groups = $(ele).val();

            $.ajax({
                url: "{{ route('cclreceivedgetledgers') }}",
                type: 'post',
                data: {
                    groups: groups
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let ledgerDropdown = $('#ledgerCode');
                    ledgerDropdown.empty();


                    if (res.status === 'success' && res.ledgers) {
                        let ledgers = res.ledgers;
                        {{--  ledgerDropdown.append(`<option value=""selected>Select Group</option>`);  --}}

                        ledgers.forEach((data) => {
                            ledgerDropdown.append(
                                `<option value="${data.ledgerCode}">${data.name}</option>`);
                            {{--  let option = document.createElement('option');
                            option.value = data.ledgerCode;
                            option.textContent = data.name;
                            ledgerDropdown.appendChild(option);  --}}
                        });
                    } else {
                        ledgerDropdown.append(`<option value=""selected>Select Group</option>`);

                        notify('No ledgers found for the selected group.', 'warning');
                    }
                },
                error: function() {
                    notify('An error occurred while fetching ledgers.', 'warning');
                }
            });

        }

        function recoveryDatashow(cclDetails, allmemberlist) {
            let accountTbody = $('#accountTbody');
            accountTbody.empty(); // Clear previous rows

            // Update member name in the header
            $('#member_name').text(allmemberlist.name);
            $('.membernamesssss').text('Name :- ' + allmemberlist.name);

            let dates = new Date(cclDetails.ccl_Date);
            let daysss = dates.getDate();
            let monthss = dates.getMonth() + 1;
            let yearss = dates.getFullYear();

            daysss = daysss < 10 ? `0${daysss}` : daysss;
            monthss = monthss < 10 ? `0${monthss}` : monthss;
            let formattedDate = `${daysss}-${monthss}-${yearss}`;

            let tenure = [];


            if (cclDetails.year && parseInt(cclDetails.year) > 0) {
                tenure.push(`${cclDetails.year}-Y`);
            }
            if (cclDetails.month && parseInt(cclDetails.month) > 0) {
                tenure.push(`${cclDetails.month}-M`);
            }
            if (cclDetails.days && parseInt(cclDetails.days) > 0) {
                tenure.push(`${cclDetails.days}-D`);
            }


            if (tenure.length === 0) {
                tenure = 'N/A';
            } else {
                tenure = tenure.join(', ');
            }

            let endDate = dateFormat(cclDetails.ccl_end_Date);


            let cclAmount = parseFloat(cclDetails.ccl_amount);
            let recovery_amount = parseFloat(cclDetails.recovery_amount);
            let trfdAmount = parseFloat(cclDetails.trfd_amount);

            $('#sodid').val(cclDetails.ids);


            if (isNaN(trfdAmount) || isNaN(cclAmount) || isNaN(recovery_amount)) {
                console.error("Invalid value(s) found in cclDetails");
                return;
            }

            let usedccl = cclDetails.trfd_amount;

            let balance = cclAmount - trfdAmount + recovery_amount;
            let balancelimit = trfdAmount - recovery_amount;
            console.log(balancelimit);

            $('#balance_amount').val(balance);
            $('#actual_principal').val(balance);

            $('#rcclmemberType').val(cclDetails.memberType);
            $('#accountnumbers').text(`CCL A/c - ${cclDetails.cclNo}`);
            $('.membershipnumbers').val(cclDetails.membership);

            let row = `
                <tr>
                    <td>${formattedDate}</td>
                    {{--  <td>${cclDetails.membership}</td>  --}}
                    <td>${cclDetails.cclNo}</td>
                    <td>${cclDetails.ccl_amount}</td>
                    <td>${usedccl}</td>
                    <td>${recovery_amount}</td>
                    <td>${balance}</td>
                    <td>${cclDetails.interest}</td>
                    <td>${tenure}</td>
                    <td>${endDate}</td>
                    <td>${cclDetails.status}</td>
                    <td class="text-end-right">
                        ${cclDetails.status === 'Closed'
                                ? `<button
                                        style="background-color: #0476b1; color: white; border: none; padding: 5px 10px;"
                                        data-id="${cclDetails.ids}"
                                        class="btn unclosed">
                                        Re-open
                                    </button>`
                                :
                                `<button
                                    style="background-color: #037034; color: white; border: none; padding: 5px 10px;"
                                    data-id="${cclDetails.ids}"
                                    class="btn transfer">
                                    Transfer
                                </button>
                                <button
                                    style="background-color: #685dd8; color: white; border: none; padding: 5px 10px;"
                                    data-id="${cclDetails.ids}"
                                    class="btn receipt">
                                    Receipt
                                </button>`
                        }
                        <button
                            style="background-color: #ea5455; color: white; border: none; padding: 5px 10px;"
                            data-id="${cclDetails.ids}"
                            class="btn viewledgers">
                            View Ledger
                        </button>

                        ${cclDetails.status !== 'Closed' && cclDetails.ccl_amount % balance === 0
                                ? `<button
                                    style="background-color: #f7780e; color: white; border: none; padding: 5px 10px;"
                                    data-id="${cclDetails.ids}"
                                    class="btn closedsod">
                                    Closed
                                </button>`
                            : ''
                        }
                    </td>
                </tr>`;
            accountTbody.append(row);
        }

        function checkExceedBalanceCcl(ele) {
            let balanceAmount = parseFloat($('#actual_principal').val()) || 0;
            let enteredAmount = parseFloat($(ele).val()) || 0;
            let updateId = $('#updateId').val();
            let cclId = $('#sodid').val();
            let cclmemberType = $('#member_type').val();
            let transcationDate = $('#transcationDate').val();

            if (isNaN(enteredAmount)) {
                notify('Please enter a valid deposit amount greater than zero.', 'warning');
                $(ele).val('');
                return;
            }

            if (isNaN(balanceAmount)) {
                notify('Invalid balance amount. Please check the balance.', 'warning');
                return;
            }

            if (updateId) {
                $.ajax({
                    url: "{{ route('editcheckExceedBalanceCcl') }}",
                    type: 'POST',
                    data: {
                        updateId: updateId,
                        cclmemberType: cclmemberType,
                        transcationDate: transcationDate,
                        enteredAmount: enteredAmount
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            let withdraw_amount = parseFloat(res.withdraw_amount) || 0;
                            let recovery_amount = parseFloat(res.recovey_amount) || 0;
                            let limit_amount = parseFloat(res.limit_amount) || 0;
                            let entryamount = parseFloat(res.cclpaymentsId?.transfer_amount) || 0;

                            let balance = limit_amount - withdraw_amount + recovery_amount + entryamount;


                            console.log("Limit:", limit_amount, "Withdraw:", withdraw_amount, "Recovery:",
                                recovery_amount, "Entry:", entryamount);

                            if (balance < enteredAmount) {
                                $('#trfdamount').val('');
                                notify(
                                    `The entered amount (${enteredAmount}) exceeds the available balance limit (${balance}).`,
                                    'warning'
                                );
                            } else {
                                notify('Valid amount entered.', 'success');
                            }
                        } else {
                            notify('The entered amount exceeds the limit.', 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        notify(`An error occurred: ${xhr.responseJSON?.message || 'Please try again later.'}`,
                            'warning');
                    }
                });

            } else {
                $.ajax({
                    url: "{{ route('checkExceedBalanceCcl') }}",
                    type: 'post',
                    data: {
                        cclId: cclId,
                        cclmemberType: cclmemberType,
                        transcationDate: transcationDate,
                        enteredAmount: enteredAmount
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            let withdraw_amount = parseFloat(res.withdraw_amount) || 0;
                            let recovery_amount = parseFloat(res.recovey_amount) || 0;
                            let limit_amount = parseFloat(res.limit_amount) || 0;

                            let balance = limit_amount - withdraw_amount + recovery_amount;

                            if (balance < enteredAmount) {
                                $('#trfdamount').val('');
                                notify(
                                    `You Entered Amount ${enteredAmount} Is Exceed Then Balance Limit ${balance}', 'warning`
                                    );
                            }

                            notify(res.messages, 'success');
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(err) {
                        notify('Something went wrong. Please try again.', 'error');
                    }
                });
            }
        }

        function checkdateinterest(ele) {
            let lastDate = $(ele).val();
            let id = $('#cclid').val();
            let receipt_date = $('#receipt_date').val();

            $.ajax({
                url: "{{ route('checkinterestdatewise') }}",
                type: 'POST',
                data: {
                    id: id,
                    receipt_date: receipt_date
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        {{--  let prinicpal = parseFloat(res.principal);
                        let totalInterest = parseFloat(res.totalInterest);  --}}

                        let interestRate = parseFloat(res.interestRate);
                        let days = parseFloat(res.days);


                        let principal = 0;
                        let interest_amount = 0;
                        let grandTotal = 0;
                        let newprincipal = 0;

                        let totalWithdraw = parseFloat(res.totalWithdraw);
                        let totalDeposit = parseFloat(res.totalDeposit);

                        principal += totalWithdraw - totalDeposit;

                        if (principal || totalInterest || interestRate || days) {
                            interest_amount = Math.round((((principal * interestRate) / 100) / 365) * days);
                            if (interest_amount && interest_amount > 0) {
                                newprincipal += principal + interest_amount;
                                grandTotal = newprincipal + interest_amount;
                                $('#interest_amount').val(interest_amount);
                                $('#total_amount').val(grandTotal);
                                $('#actual_principal').val(principal);
                                $('#cclid').val(id);
                                $('#principal').val(newprincipal).prop('readonly', true);
                                $('#rate_of_interest').val(interestRate).prop('readonly', true);
                            }






                        } else {
                            $('#actual_principal').val('');
                            $('#principal').val('').prop('readonly', true);
                            $('#rate_of_interest').val('').prop('readonly', true);
                            $('#interest_amount').val('');
                            $('#total_amount').val('');
                        }

                        $('#receiptModal').modal('show');
                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        function checkRecoveryNoExceed(ele) {
            let principal = parseFloat($('#actual_principal').val()) || 0;
            let enteredAmount = parseFloat($(ele).val()) || 0;
            let cclId = $('#cclid').val();
            let cclmemberType = $('#rcclmemberType').val();
            let transcationDate = $('#receipt_date').val();

            if (isNaN(enteredAmount)) {
                notify('Please enter a valid deposit amount greater than zero.', 'warning');
                $(ele).val('');
                return;
            }

            if (isNaN(principal)) {
                notify('Invalid balance amount. Please check the balance.', 'warning');
                return;
            }
            $.ajax({
                url: "{{ route('checkRecoveryNoExceed') }}",
                type: 'post',
                data: {
                    enteredAmount: enteredAmount,
                    cclId: cclId,
                    cclmemberType: cclmemberType,
                    transcationDate: transcationDate
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let withdraw_amount = parseFloat(res.withdraw_amount) || 0;
                        let recovery_amount = parseFloat(res.recovey_amount) || 0;
                        let limit_amount = parseFloat(res.limit_amount) || 0;

                        let balance = withdraw_amount + recovery_amount;

                        if (balance < enteredAmount) {
                            $('#receipt_amount').val('');
                            notify(
                                `You Entered Amount ${enteredAmount} Is Exceed Then Balance Limit ${balance}', 'warning`);
                        } else {
                            {{--  notify(res.messages, 'success');  --}}
                        }
                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        function previousinterest(ele) {
            let lastDate = $(ele).val();
            let id = $('#cclId').val();
            let receipt_date = $('#transcationDate').val();

            $.ajax({
                url: "{{ route('checktrfdinterestdatewise') }}",
                type: 'POST',
                data: {
                    id: id,
                    receipt_date: receipt_date
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        let prinicpal = parseFloat(res.principal);
                        let totalInterest = parseFloat(res.totalInterest);

                        let interestRate = parseFloat(res.interestRate);
                        let days = parseFloat(res.days);

                        let interest_amount = 0;
                        let granTotal = 0;

                        if (prinicpal || totalInterest || interestRate || days) {
                            interest_amount += Math.round((((prinicpal * interestRate) / 100) / 365) * days);
                            granTotal = prinicpal + interest_amount;
                            $('#cclid').val(id);
                            $('#trfd_interest_amount').val(interest_amount);
                        } else {
                            $('#cclid').val('');
                            $('#trfd_interest_amount').val('');
                        }
                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        function changeinterestAmount(ele) {
            let principal = parseFloat($('#principal').val());
            let interest_amount = parseFloat($(ele).val());
            let total_amount = 0;

            if (!isNaN(principal) || !isNaN(interest_amount)) {
                total_amount = principal + interest_amount;
                $('#total_amount').val(total_amount);
            } else {
                return notify('Entered Valid Amount', 'warning');
            }
        }

        function getMonthName(dateString) {
            const months = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            const date = new Date(dateString);
            const monthName = months[date.getMonth()];
            const year = date.getFullYear();
            return `${monthName} ${year}`;
        }

        function editcclrecovery(id) {

            let currentDate = $('#opening_date').val();

            $.ajax({
                url: "{{ route('editcclrecoverypayments') }}",
                type: 'post',
                data: {
                    id: id,
                    currentDate: currentDate
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let cclpaymentsId = res.cclpaymentsId;
                        let cclDetails = res.cclDetails;
                        let balance = parseFloat(res.balance);
                        let allmemberlist = res.allmemberlist;
                        let updatetionbalance = 0;


                        $('#ledgerModal').modal('hide');

                        if (cclpaymentsId.type === 'Withdraw') {
                            {{--  updatetionbalance += balance + parseFloat(cclpaymentsId.transfer_amount);  --}}

                            if (cclpaymentsId.payment_type === 'Transfer') {
                                $('#paymenttype').val(cclpaymentsId.payment_type).trigger('change').prop(
                                    'readonly', true);
                                $('#savingId').val(allmemberlist.id);
                                $('#saving_account').val(cclpaymentsId.saving_account);
                                $('#updateId').val(cclpaymentsId.id);
                                $('#cclId').val(cclDetails.id);
                                $('#cclmemberType').val(cclpaymentsId.memberType);

                                $('#balance_amount').val(balance);
                                $('#trfdupdatememberType').val(cclpaymentsId.memberType);
                                $('#transcationDate').val(dateFormat(cclpaymentsId.transcationDate));

                                $('#trfdamount').val(cclpaymentsId.transfer_amount);
                                $('#trfd_interest_amount').val(cclpaymentsId.interest_amount);
                                $('#narration').val(cclpaymentsId.narration);
                                $('#ccltrfdModal').modal('show');
                            } else {
                                {{--  updatetionbalance += balance - parseFloat(cclpaymentsId.recovey_amount);  --}}

                                setTimeout(function() {
                                    $('#paymenttype').val(cclpaymentsId.payment_type).trigger('change')
                                        .prop('readonly', true);
                                    $('#groupdiv').show();
                                    $('#ledgerdiv').show();
                                    $('#cashbankgroup').val(cclpaymentsId.paymentgroup).trigger(
                                        'change');

                                }, 100);


                                $('#saving_account').val(cclpaymentsId.saving_account);
                                $('#updateId').val(cclpaymentsId.id);
                                $('#cclId').val(cclDetails.id);
                                $('#cclmemberType').val(cclpaymentsId.memberType);

                                $('#balance_amount').val(balance);
                                $('#trfdupdatememberType').val(cclpaymentsId.memberType);
                                $('#transcationDate').val(dateFormat(cclpaymentsId.transcationDate));

                                $('#trfdamount').val(cclpaymentsId.transfer_amount);
                                $('#trfd_interest_amount').val(cclpaymentsId.interest_amount);
                                $('#narration').val(cclpaymentsId.narration);
                                $('#ccltrfdModal').modal('show');
                            }


                        } else {
                            $('#trfdamount').val('');
                        }



                        if (cclpaymentsId.type === 'Deposit') {

                            $('#principal').val(balance);
                            $('#cclid').val(cclDetails.id);
                            $('#updaterecoveryId').val(cclpaymentsId.id);
                            $('#rcclmemberType').val(cclpaymentsId.memberType);
                            $('#receipt_date').val(dateFormat(cclpaymentsId.transcationDate));
                            $('#rate_of_interest').val(cclDetails.interest);
                            $('#interest_amount').val(cclpaymentsId.interest_amount);
                            $('#total_amount').val(balance + cclpaymentsId.interest_amount);
                            $('#receipt_amount').val(cclpaymentsId.recovey_amount);
                            $('#receipt_narration').val(cclpaymentsId.narration);

                            setTimeout(function() {
                                $('#groupCode').val(cclpaymentsId.paymentgroup).trigger('change');
                                $('#ledgerCode').val(cclpaymentsId.paymentledger);
                            }, 100);

                            $('#receiptModal').modal('show');
                        } else {
                            $('#total_amount').val('');
                        }

                    } else {
                        $('#ccltrfdModal').modal('hide');
                        $('#receiptModal').modal('hide');
                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        $(document).ready(function() {
            $(document).on('click', '.membernumber', function(event) {
                event.preventDefault();
                let selectaccount = $(this).data('id');
                let memberType = $('#member_type').val();
                let opening_date = $('#opening_date').val();

                $('#ccl_account').val(selectaccount);
                $('#accountList').empty();

                $.ajax({
                    url: "{{ route('getcclaccount') }}",
                    type: 'POST',
                    data: {
                        memberType: memberType,
                        selectaccount: selectaccount,
                        opening_date: opening_date
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            let cclDetails = res.cclDetails;
                            let allmemberlist = res.allmemberlist;
                            if (cclDetails && allmemberlist) {
                                recoveryDatashow(cclDetails, allmemberlist);
                            } else {
                                let accountTbody = $('accountTbody');
                                accountTbody.empty();
                                accountTbody.append(
                                    `<tr><td colspan="10">No Record Available</td></tr>`);
                            }

                        } else {
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });

            $(document).on('click', '.transfer', function(event) {
                event.preventDefault();
                $('#ccltrfdModal').modal('show');
                $('#updateId').val('');
                $('#savingId').val('');
            });

            $(document).on('click', '.closetrfdbutton', function(event) {
                event.preventDefault();

                let dates = new Date();
                let days = dates.getDate();
                let month = dates.getMonth() + 1;
                let year = dates.getFullYear();

                days = days < 10 ? `0${days}` : days;
                month = month < 10 ? `0${month}` : month;
                let currentDate = `${days}-${month}-${year}`;

                $('#transcationDate').val(currentDate);
                $('#saving_account').val(0);
                $('#trfdamount').val('');
                $('#trfd_interest_amount').val('');
                $('#narration').val('');
                $('#ccltrfdForm')[0].reset();
                $('#savingaccoundiv').hide();
                $('#groupdiv').hide();
                $('#ledgerdiv').hide();
                $('#ccltrfdModal').modal('hide');
            });

            $(document).on('click', '.receiptclosebtn', function(event) {
                event.preventDefault();
                let dates = new Date();
                let days = dates.getDate();
                let month = dates.getMonth() + 1;
                let year = dates.getFullYear();

                days = days < 10 ? `0${days}` : days;
                month = month < 10 ? `0${month}` : month;
                let currentDate = `${days}-${month}-${year}`;

                $('#receipt_date').val(currentDate);
                $('#principal').val('');
                $('#rate_of_interest').val('');
                $('#interest_amount').val('');
                $('#total_amount').val('');
                $('#receipt_amount').val('');
                $('#receipt_narration').val('');
                $('#receiptModal').modal('hide');
            });

            $(document).on('input', '#opening_date', function(event) {
                event.preventDefault();
                let opening_date = $('#opening_date').val();
                $('#receipt_date').val(opening_date);
            });

            $('#ccltrfdForm').validate({
                rules: {
                    cclId: {
                        required: true,
                        number: true
                    },
                    cclmemberType: {
                        required: true
                    },
                    transcationDate: {
                        required: true,
                    },
                    trfdamount: {
                        required: true,
                        number: true
                    },
                    paymenttype: {
                        required: true
                    }
                },
                messages: {
                    cclId: {
                        required: 'Enter ID',
                        number: 'Enter Numeric'
                    },
                    cclmemberType: {
                        required: 'Enter Member'
                    },
                    transcationDate: {
                        required: 'Enter Valid Date',
                    },
                    trfdamount: {
                        required: 'Enter Withdraw Amount',
                        number: 'Enter Only Numeric Value'
                    },
                    paymenttype: {
                        required: 'Select Payment Type'
                    }
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });


            $(document).on('submit', '#ccltrfdForm', function(event) {
                event.preventDefault();
                if ($(this).valid()) {
                    let formData = $(this).serialize();

                    let url = $('#updateId').val() ? "{{ route('updateccltrfdtosavingaccount') }}" :
                        "{{ route('ccltrfdtosavingaccount') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                let cclDetails = res.cclDetails;
                                let allmemberlist = res.allmemberlist;
                                if (cclDetails && allmemberlist) {
                                    $('#ccltrfdModal').modal('hide');
                                    $('#ccltrfdForm')[0].reset();
                                    recoveryDatashow(cclDetails, allmemberlist);
                                } else {
                                    let accountTbody = $('#accountTbody');
                                    accountTbody.empty();
                                    accountTbody.append(
                                        `<tr><td colspan="10">No Record Available</td></tr>`
                                        );
                                }

                            } else {
                                $('button[type=submit]').prop('disabled', false);
                                notify(res.messages, 'warning');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.viewledgers', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                let currentDate = $('#opening_date').val();

                $.ajax({
                    url: "{{ route('viewcclledgers') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        currentDate: currentDate
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            let ledgersbody = $('#ledgersbody');
                            ledgersbody.empty();

                            let cclDetails = res.cclDetails;
                            let payments = res.payments;

                            // Display CCL Details
                            if (cclDetails) {
                                let openingDate = dateFormat(cclDetails.ccl_Date);
                                let disburmentAmount = parseFloat(cclDetails.ccl_amount);
                                let interestRate = parseFloat(cclDetails.interest);

                                ledgersbody.append(`
                                    <tr>
                                        <td>${openingDate}</td>
                                        <td>${disburmentAmount.toFixed(2)}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td></td>
                                    </tr>
                                `);
                            } else {
                                ledgersbody.append(`
                                    <tr><td colspan="7">No Record Available</td></tr>
                                `);
                            }

                            // Process Payments
                            if (Array.isArray(payments) && payments.length > 0) {
                                let monthlyData = {};
                                let lastClosingBalance = 0;

                                payments.forEach((data) => {
                                    let transcationDate = dateFormat(data
                                        .transcationDate);
                                    let withdraw = parseFloat(data.transfer_amount) ||
                                    0;
                                    let recovery_amount = parseFloat(data
                                        .recovey_amount) || 0;
                                    let interest = parseFloat(data.interest_amount) ||
                                    0;
                                    let month = getMonthName(data.transcationDate);

                                    if (!monthlyData[month]) {
                                        monthlyData[month] = {
                                            totalInterest: 0,
                                            balance: lastClosingBalance,
                                            newAmount: 0,
                                            transactions: []
                                        };
                                    }

                                    monthlyData[month].totalInterest += interest;
                                    monthlyData[month].balance += withdraw -
                                        recovery_amount;

                                    let hideButtons = data.status === "Closed" || data
                                        .chequeNo != null;

                                    console.log(data.chequeNo);

                                    let rowHTML = `
                                    <tr>
                                        <td>${transcationDate}</td>
                                        <td>-</td>
                                        <td>${withdraw.toFixed(2)}</td>
                                        <td>${recovery_amount.toFixed(2)}</td>
                                        <td>${interest.toFixed(2)}</td>
                                        <td>${monthlyData[month].balance.toFixed(2)}</td>
                                        ${
                                            hideButtons
                                                ? `<td></td>`
                                                : `<td style="width:85px;">
                                                        <button class="btn ccleditbtn p-1" onclick="editcclrecovery(${data.id})">
                                                            <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                        </button>
                                                        <button class="btn ccltrfddeletebtn p-1" data-id="${data.id}">
                                                            <i class="fa-solid fa-trash iconsColorCustom"></i>
                                                        </button>
                                                    </td>`
                                        }
                                    </tr>`;

                                    monthlyData[month].transactions.push(rowHTML);
                                    monthlyData[month].newAmount = monthlyData[month]
                                        .balance + monthlyData[month].totalInterest;
                                    lastClosingBalance = monthlyData[month].newAmount;
                                });

                                for (let month in monthlyData) {
                                    let {
                                        totalInterest,
                                        newAmount,
                                        transactions
                                    } = monthlyData[month];
                                    transactions.forEach(row => ledgersbody.append(row));

                                    ledgersbody.append(`
                                        <tr>
                                            <td colspan="4"><strong>Closing Balance (${month})</strong></td>
                                            <td><strong>${totalInterest.toFixed(2)}</strong></td>
                                            <td><strong>${newAmount.toFixed(2)}</strong></td>
                                            <td></td>
                                        </tr>
                                    `);
                                }
                            }


                            $('#ledgerModal').modal('show');
                        } else {
                            notify(res.messages, 'warning');
                        }
                    }

                });
            });

            $(document).on('click', '.receipt', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let receipt_date = $('#receipt_date').val();

                $.ajax({
                    url: "{{ route('recieptcclamount') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        receipt_date: receipt_date
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {

                            let principal = 0;
                            let interestRate = parseFloat(res.interestRate || 0);
                            let days = parseFloat(res.days || 0);
                            let interest_amount = 0;
                            let grandTotal = 0;
                            let cclDetails = res.cclDetails || {};
                            let newprincipal = 0;

                            let totalWithdraw = parseFloat(res.totalWithdraw || 0);
                            let totalDeposit = parseFloat(res.totalDeposit || 0);
                            let cclamount = parseFloat(cclDetails.ccl_amount || 0);

                            principal += totalWithdraw - totalDeposit;


                            if (principal || interestRate || days) {
                                interest_amount = Math.round((((principal * interestRate) /
                                    100) / 365) * days);

                                newprincipal += principal + interest_amount;
                                grandTotal = newprincipal + interest_amount;

                                $('#cclid').val(cclDetails.id);
                                $('#principal').val(principal).prop('readonly', true);
                                $('#rate_of_interest').val(interestRate).prop('readonly', true);
                                $('#interest_amount').val(interest_amount);
                                $('#total_amount').val(grandTotal);

                                // Show modal after populating data
                                $('#receiptModal').modal('show');
                            } else {

                                $('#principal').val('').prop('readonly', true);
                                $('#rate_of_interest').val('').prop('readonly', true);
                                $('#interest_amount').val('');
                                $('#total_amount').val('');
                            }
                        } else {
                            console.log("Error:", res.messages);
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });

            $('#cclRecoveryForm').validate({
                rules: {
                    receipt_date: {
                        required: true,
                    },
                    principal: {
                        required: true,
                        number: true
                    },
                    rate_of_interest: {
                        required: true
                    },
                    interest_amount: {
                        number: true
                    },

                    receipt_amount: {
                        number: true
                    },
                    groupCode: {
                        required: true
                    }
                },
                messages: {
                    receipt_date: {
                        required: 'Enter Date',
                    },
                    principal: {
                        required: 'Enter Prinicpal amount',
                        number: 'Enter Only Numeric Value'
                    },
                    rate_of_interest: {
                        required: true
                    },
                    interest_amount: {
                        number: 'Enter Only Numeric Value'
                    },
                    receipt_amount: {
                        number: 'Enter Only Numeric Value'
                    },
                    groupCode: {
                        required: 'Select Payment Type'
                    }
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });


            $(document).on('submit', '#cclRecoveryForm', function(event) {
                event.preventDefault();

                if ($(this).valid()) {
                    let formData = $(this).serialize();
                    let url = $('#updaterecoveryId').val() ? "{{ route('cclrecoverUpdate') }}" :
                        "{{ route('cclrecoverInsert') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                let cclDetails = res.cclDetails;
                                let allmemberlist = res.allmemberlist;
                                if (cclDetails && allmemberlist) {
                                    $('#receiptModal').modal('hide');
                                    $('#cclRecoveryForm')[0].reset();
                                    recoveryDatashow(cclDetails, allmemberlist);
                                } else {
                                    let accountTbody = $('#accountTbody');
                                    accountTbody.empty();
                                    accountTbody.append(
                                        `<tr><td colspan="10">No Record Available</td></tr>`
                                        );
                                }
                            } else {
                                $('button[type=submit]').prop('disabled', false);
                                notify(res.messages, 'warning');
                            }
                        }
                    });
                }


            });

            $(document).on('click', '.ccltrfddeletebtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                let ccl_account = $('#ccl_account').val();
                let member_type = $('#member_type').val();
                let currentDate = $('#opening_date').val();

                // Validate inputs
                if (!id || !ccl_account || !member_type) {
                    Swal.fire({
                        title: "Error!",
                        text: "Invalid data. Please refresh the page and try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                $('#ledgerModal').modal('hide');

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
                        $('#ledgerModal').modal('hide');

                        Swal.fire({
                            title: "Deleting...",
                            text: "Please wait while we delete the transaction.",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });


                        $.ajax({
                            url: "{{ route('deleteccltrfdpayment') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                ccl_account: ccl_account,
                                member_type: member_type,
                                currentDate: currentDate
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                $('.ccltrfddeletebtn').prop('disabled', false);

                                if (res.status === 'success') {
                                    if (res.cclDetails && res.allmemberlist) {
                                        recoveryDatashow(res.cclDetails, res
                                            .allmemberlist);
                                    } else {
                                        $('#accountTbody').empty().append(
                                            `<tr><td colspan="10">No Record Available</td></tr>`
                                        );
                                    }

                                    Swal.fire({
                                        title: "Deleted!",
                                        text: `Transaction ID #${id} has been successfully deleted.`,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Warning!",
                                        text: res.messages ||
                                            "Deletion failed. Please try again.",
                                        icon: "warning",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                $('.ccltrfddeletebtn').prop('disabled', false);

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

            $(document).on('click', '.closedsod', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let ccl_account = $('#ccl_account').val();
                let member_type = $('#member_type').val();
                {{--  let opening_date = $('#opening_date').val();  --}}

                // Validate inputs
                if (!id || !ccl_account || !member_type) {
                    Swal.fire({
                        title: "Error!",
                        text: "Invalid data. Please refresh the page and try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                    return;
                }


                Swal.fire({
                    title: "Are you sure?",
                    text: `You are about to delete transaction ID #${id}. This action cannot be undone!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Closed it!"
                }).then((result) => {

                    if (result.isConfirmed) {

                        Swal.fire({
                            title: "Closed...",
                            text: "Please wait while we closed the sod a/c",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });


                        $.ajax({
                            url: "{{ route('closedsodaccount') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                ccl_account: ccl_account,
                                member_type: member_type
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                $('.closedsod').prop('disabled', false);

                                if (res.status === 'success') {
                                    if (res.cclDetails && res.allmemberlist) {
                                        recoveryDatashow(res.cclDetails, res
                                            .allmemberlist);
                                    } else {
                                        $('#accountTbody').empty().append(
                                            `<tr><td colspan="10">No Record Available</td></tr>`
                                        );
                                    }

                                    Swal.fire({
                                        title: "Closed!",
                                        text: `Transaction ID #${id} has been successfully deleted.`,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Warning!",
                                        text: res.messages ||
                                            "Deletion failed. Please try again.",
                                        icon: "warning",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                $('.closedsod').prop('disabled', false);

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

            $(document).on('click', '.unclosed', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let ccl_account = $('#ccl_account').val();
                let member_type = $('#member_type').val();
                {{--  let opening_date = $('#opening_date').val();  --}}

                // Validate inputs
                if (!id || !ccl_account || !member_type) {
                    Swal.fire({
                        title: "Error!",
                        text: "Invalid data. Please refresh the page and try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                    return;
                }


                Swal.fire({
                    title: "Are you sure?",
                    text: `You are about to delete transaction ID #${id}. This action cannot be undone!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Closed it!"
                }).then((result) => {

                    if (result.isConfirmed) {

                        Swal.fire({
                            title: "Closed...",
                            text: "Please wait while we closed the sod a/c",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });


                        $.ajax({
                            url: "{{ route('unclosedsodaccount') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                ccl_account: ccl_account,
                                member_type: member_type
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                $('.closedsod').prop('disabled', false);

                                if (res.status === 'success') {
                                    if (res.cclDetails && res.allmemberlist) {
                                        recoveryDatashow(res.cclDetails, res
                                            .allmemberlist);
                                    } else {
                                        $('#accountTbody').empty().append(
                                            `<tr><td colspan="10">No Record Available</td></tr>`
                                        );
                                    }

                                    Swal.fire({
                                        title: "Closed!",
                                        text: `Transaction ID #${id} has been successfully deleted.`,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Warning!",
                                        text: res.messages ||
                                            "Deletion failed. Please try again.",
                                        icon: "warning",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                $('.closedsod').prop('disabled', false);

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


        });
    </script>
@endpush



@push('style')
    <style>
        /* Make the modal content scrollable */
        .modal-dialog-scrollable .modal-content {
            max-height: 80vh;
            /* 90% of the viewport height */
            overflow: hidden;
        }

        /* Scrollable table body */
        #ledgersbody {
            max-height: 300px;
            /* Adjust height as needed */
            overflow-y: auto;
            padding: 1px 0px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 1px 0px;
        }

        /* Add some padding and spacing for the modal */
        .modal-body {
            padding: 1.5rem;
        }

        .modal-header {
            border-bottom: 2px solid #dee2e6;
        }

        .modal-footer {
            border-top: 2px solid #dee2e6;
        }

        /* Add some hover effect to the rows */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Style for table header */
        .table-dark th {
            background-color: #7367f0 !important;
            color: white;

        }



        .swal2-container {
            z-index: 1060 !important;
            /* Ensure SweetAlert always appears above Bootstrap modals */
        }

        button.btn.editbtn,
        button.btn.deletebtn {
            padding: 0 !important;
        }

        .pt-3 {
            padding-top: 1.5rem !important;
        }

        .clickable-cell {
            color: #7367f0 !important;
        }

        .saving_column {
            position: relative;
        }

        .saving_column .error {

            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }

        .accountList {
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
            padding-left: 11px;
        }

        .accountHolderDetails {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .thead-light tr th {
            background-color: #7367f0;
            color: white !important;
        }

        .form-label {
            text-transform: capitalize;
        }
    </style>
@endpush
