@extends('layouts.app')

@php
    $table = 'yes';
@endphp

@section('content')
    @php  $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));  @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span>Security On Commission</h4>
                    </div>
                    <div class="col-md-3 accountHolderDetails">
                        <h6 class=""><span class="text-muted fw-light">Name: </span><span id="memberName"></span></h6>
                        <h6 class="pt-2"><span class="text-muted fw-light">Balance: </span><span
                                id="memberBalance"></span></h6>
                        <input type="hidden" id="fdedittyme">

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <form id="securityoncommissionForm" name="securityoncommissionForm">
                            <div class="row row-gap-2">
                                <input type="hidden" name="securityaccId" id="securityaccId">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">Date</label>
                                    <input type="text" class="form-control formInputs mydatepic transactionDate"
                                        placeholder="DD-MM-YYYY" id="transactionDate" name="transactionDate"
                                        value="{{ $currentDate }}" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="memberType" class="form-label">Action</label>
                                    <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        <option value="Satff">Staff</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">Account No</label>
                                    <input type="text" class="form-control formInputs" oninput="getsavingacclist('this')"
                                        id="account_no" name="account_no" placeholder="Account No" autocomplete="off" />
                                    <div id="accountList" class="accountList"></div>
                                </div>


                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding" hidden>
                                    <label for="accountNoo" class="form-label">Membership No</label>
                                    <input type="text" class="form-control formInputs" id="membership" name="membership"
                                        placeholder="Membership No" readonly autocomplete="off" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="transactionType" class="form-label">Action</label>
                                    <select class="form-select formInputsSelect" id="transactionType" name="transactionType"
                                        onchange="amountTransferToOthers('this')">
                                        <option value="Deposit">Deposit</option>
                                        <option value="Withdraw">Withdraw</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding banktrfd">
                                    <label class="form-label mb-1" for="status-org">Payment Type</label>
                                    <select name="cashbank" id="cashbank" class="form-select formInputsSelect"
                                        onchange="getcashbanksaving(this)">
                                        <option value=""selected>Select Payment Type</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding bank banktrfd" style="display: block;">
                                    <label for="txndate" class="form-label">Select Bank</label>
                                    <select name="ledgerId" id="ledgerId" class="form-select formInputsSelect"
                                        data-placeholder="Active">
                                        <option value="">Select</option>
                                    </select>
                                </div>


                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding savingaccountdiv" style="display: none;">
                                    <label for="txndate" class="form-label">aving A/c</label>
                                    <select name="savingaccounts" id="savingaccounts" class="form-select formInputsSelect"
                                        data-placeholder="Active">
                                        <option value="">Select</option>
                                    </select>
                                </div>

                                <div <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="narration" class="form-label">Amount</label>
                                    <input type="text" class="form-control formInputs" id="amount"
                                        placeholder="Amount" name="amount" />
                                </div>

                                <div <div class="col-lg-4 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                    <label for="narration" class="form-label">Narration</label>
                                    <input type="text" class="form-control formInputs" id="narration"
                                        placeholder="Narration" name="narration" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 mt-4 saving_column inputesPadding">
                                    {{--  <div class="d-flex h-100 justify-content-end text-end">  --}}
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">Save</button>
                                    {{--  </div>  --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="card tablee">
            <div class="card-body">
                <div class="table-responsive tabledata">
                    <table class="table datatables-order table table-bordered" id="table" style="width:100%">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th scope="col">Sr.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Deposit</th>
                                <th scope="col">Wthdraw</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Action</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="interestModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">Interest Received</h5>
                        {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body">
                        <form id="interestForm" name="interestForm">
                            <div class="row">
                                <input type="text" hidden id="interestid" name="interestid">

                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <input id="interest_date" type="text" name="interest_date"
                                        class="form-control" placeholder="DD-MM-YYYY"
                                        value="{{ date('d-m-Y') }}"  readonly/>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <input id="memberTypes" type="text" name="memberTypes"
                                        class="form-control " placeholder="MemberType"
                                       readonly/>
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label">Account No</label>
                                    <input id="ineterest_account" type="text" name="ineterest_account"
                                        class="form-control " placeholder="Account No"
                                       readonly/>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Interest Amount</label>
                                    <input id="interest_paid_amount" type="text" name="interest_paid_amount"
                                        class="form-control " placeholder="Interest Amount"
                                       />
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="updateButton" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
@endsection
@push('script')
    <script>

        $(document).on('change','#memberType',function(event){
            event.preventDefault();
            $('#account_no').val('');
            $('#table').load(location.href+' .table');

        });

        function getcashbanksaving(ele) {
            let cashbank = $('#cashbank').val();
            let type = $(ele).val();

            function updateDropdown(selector, data, defaultOption) {
                $(selector).empty();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        $(selector).append(`<option value="${item.ledgerCode || item.accountNo}">${item.name || item.accountNo}</option>`);
                    });
                } else {
                    $(selector).append(`<option value="">${defaultOption}</option>`);
                }
            }

            if (type === 'Bank') {
                $(".bank").show();
                $(".savingaccountdiv").hide();

                if (cashbank) {
                    $.ajax({
                        url: "{{ route('getcashbank') }}",
                        type: 'post',
                        data: { cashbank },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                updateDropdown('#ledgerId', res.ledgers, 'Select Ledger');
                            } else {
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function() {
                            notify("An error occurred while fetching bank data.", 'error');
                        }
                    });
                }
            }else if(type === 'Cash'){

                $(".bank").show();
                $(".savingaccountdiv").hide();

                if (cashbank) {
                    $.ajax({
                        url: "{{ route('getcashbank') }}",
                        type: 'post',
                        data: { cashbank },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                updateDropdown('#ledgerId', res.ledgers, 'Select Ledger');
                            } else {
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function() {
                            notify("An error occurred while fetching bank data.", 'error');
                        }
                    });
                }

            }else {
                $(".savingaccountdiv").hide();
                $(".bank").show();

                if (cashbank) {
                    $.ajax({
                        url: "{{ route('getcash') }}",
                        type: 'post',
                        data: { cashbank },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                updateDropdown('#ledgerId', res.ledgers, 'Select Ledger');
                            } else {
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function() {
                            notify("An error occurred while fetching cash data.", 'error');
                        }
                    });
                }
            }
        }

        function getsavingacclist(){
            let account_no = $('#account_no').val();
            let memberType = $('#memberType').val();
            $.ajax({
                url : "{{ route('getagentaccountlist') }}",
                type : 'post',
                data : {account_no : account_no , memberType : memberType},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let allAccounts = res.all_aacounts
                        let accountListdropdown = $('#accountList');
                        accountListdropdown.empty();

                        if(allAccounts && allAccounts.length > 0){
                            allAccounts.forEach((data) => {
                                accountListdropdown.append(`<div class="accountLists" data-id="${data.staff_no}">${data.staff_no}</div>`);
                            });
                        }else{
                            accountListdropdown.append(`<div class="accountLists">No Account</div>`);
                        }

                    }else{
                        notify(res.messages,'warning');
                    }
                }
            });
        }

        function ShowDataTable(security_entries, opening_amount, account) {
            let transactionType = $('#transactionType').val();
            let tableBody = $('#tableBody');
            tableBody.empty();

            // Ensure opening_amount is numeric
            let openingAmountValue = opening_amount && typeof opening_amount === 'object' ? parseFloat(opening_amount.amount || 0) : parseFloat(opening_amount || 0);
            let formattedAmount = opening_amount && typeof opening_amount === 'object' ? `${opening_amount.amount || 0} ${opening_amount.currency || ''}` : opening_amount;
            tableBody.append(`<tr><td colspan="4">Opening Balance</td><td>${formattedAmount}</td><td></td><td></td></tr>`);

            $('#memberName').text(account.name);
            $('#membership').val(account.staff_no);

            let balance_amount = openingAmountValue; // Start with the opening amount as the initial balance
            if (security_entries && security_entries.length > 0) {
                security_entries.forEach((data, index) => {
                    // Set Date Format
                    let dates = new Date(data.transactionDate);
                    let day = String(dates.getDate()).padStart(2, '0');
                    let month = String(dates.getMonth() + 1).padStart(2, '0');
                    let year = dates.getFullYear();
                    let formattedDate = `${day}-${month}-${year}`;

                    // Get Account Closing Balance
                    let deposit_amount = parseFloat(data.depositAmount) || 0;
                    let withdraw_amount = parseFloat(data.withdrawAmount) || 0;
                    balance_amount = balance_amount + deposit_amount - withdraw_amount; // Update the balance amount

                    let saving_row = `<tr>
                        <td>${index + 1}</td>
                        <td>${formattedDate}</td>
                        <td>${deposit_amount.toFixed(2)}</td>
                        <td>${withdraw_amount.toFixed(2)}</td>
                        <td>${balance_amount.toFixed(2)}</td>`;

                    if (data.transactionType === 'Comm') {
                        saving_row += `<td></td>`;
                    } else if (data.chequeNo === 'Interest Received') {

                        saving_row += `
                            <td>
                                <button class="btn editinterest"
                                data-id="${data.id}">
                                <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                            </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.staff_no}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;
                    } else {
                        saving_row += `<td style="width:85px;">
                            <button class="btn editbtn"
                                data-id="${data.id}">
                                <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                            </button>
                        </td>`;
                    }

                    // Add narration and close the row
                    saving_row += `<td>${data.narration}</td></tr>`;
                    tableBody.append(saving_row);
                });
                $('#memberBalance').text(parseFloat(balance_amount.toFixed(2)));
            }
        }


        function amountTransferToOthers(){
            let transactionType = $('#transactionType').val();
            let cashbank = $('#cashbank').val();
            {{--  let type = $(ele).val();  --}}

            if(transactionType === 'Transfer'){
                $('.banktrfd').hide();
                $('.savingaccountdiv').show();

                $(".savingaccountdiv").show();
                $(".bank").hide();
                let account_no = $('#account_no').val();
                let memberType = $('#memberType').val();

                $.ajax({
                    url: "{{ route('getsavingaccount') }}",
                    type: 'post',
                    data: { cashbank : cashbank, account_no : account_no, memberType : memberType,transactionType : transactionType },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let savingaccounts = res.savingaccount;
                            $('#savingaccounts').empty()
                            if(savingaccounts){
                                $('#savingaccounts').append(`<option value="${savingaccounts.accountNo}">${savingaccounts.accountNo}</option>`);
                            }else{
                                $('#savingaccounts').append(`<option value="">Select</option>`);
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function() {
                        notify("An error occurred while fetching saving account data.", 'error');
                    }
                });

            }else{
                $('.banktrfd').show();
                $('.savingaccountdiv').hide();

            }
        }

        $(document).ready(function(){

            $(document).on('input','#amount',function(event){
                event.preventDefault();
                let transactionType = $('#transactionType').val();
                if(transactionType === 'Withdraw' || transactionType === 'Transfer'){
                    let enteredAmount = parseFloat($('#amount').val());
                    let balance = parseFloat($('#memberBalance').text()) || 0;
                    if (isNaN(enteredAmount) || enteredAmount <= 0) {
                        notify('Please enter a valid deposit amount greater than zero.', 'warning');
                        return;
                    }

                    if (enteredAmount > balance) {
                        $('#amount').val('');
                        notify('Entered amount exceeds the allowed principal.', 'warning');
                        return;
                    }
                }else{
                    {{--  notify('Amount is valid', 'success');  --}}
                }

            });

            $(document).on('click','.accountLists',function(event){
                event.preventDefault();
                let accountNumber = $(this).data('id');
                let memberType = $('#memberType').val();

                $('#account_no').val(accountNumber);
                $('#accountList').html('');

                $.ajax({
                    url : "{{ route('getsecurityaccountdetail') }}",
                    type : 'post',
                    data : {accountNumber : accountNumber, memberType : memberType},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let account = res.account || {};
                            let security_entries = res.security_entries || [];
                            let opening_amount = res.opening_amount || { amount: 0, currency: '' };

                            ShowDataTable(security_entries, opening_amount, account);
                        } else {
                            console.error('Failed to fetch data:', res.message);
                        }
                    }

                });
            });

            $('#securityoncommissionForm').validate({
                rules : {
                    transactionDate : {
                        required : true
                    },
                    memberType : {
                        required : true
                    },
                    account_no : {
                        required : true,
                        number : true
                    },
                    transactionType : {
                        required : true,
                    },
                    cashbank : {
                        required : true,
                    },
                    ledgerId : {
                        required : true,
                    },
                },messages : {
                    transactionDate : {
                        required : 'Enter Valid Date'
                    },
                    memberType : {
                        required : 'Select Type'
                    },
                    account_no : {
                        required : 'Enter Account Number',
                        number : 'Enter Only Numeric Value'
                    },
                    transactionType : {
                        required : 'Select Transaction Type',
                    },
                    cashbank : {
                        required : 'Select Payment Type',
                    },
                },
                error : 'p',
                errorPlacement : function(error,element){
                    error.insertAfter(element);
                }
            });

            $(document).on('submit','#securityoncommissionForm',function(event){
                event.preventDefault();
                if($(this).valid()){
                    let formData = $(this).serialize();
                    let url = $('#securityaccId').val() ? "{{ route('updatesecuirtyaccount') }}" : "{{ route('insertsecuirtyaccount') }}";

                    $.ajax({
                        url : url,
                        type : 'post',
                        data : formData,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        dataType : 'json',
                        success : function(res){
                            if(res.status === 'success'){
                                let account = res.account || {};
                                let security_entries = res.security_entries || [];
                                let opening_amount = res.opening_amount || { amount: 0, currency: '' };
                                $('#amount').val('');
                                $('#narration').val('');
                                $('#cashbank').val('');
                                $('#cashbank').trigger('change');
                                $('#ledgerId').val('').html('<option value="" selected>Select</option>').trigger('change');
                                ShowDataTable(security_entries, opening_amount, account);
                            }else{
                                notify(res.messages,'warning');
                            }
                        }
                    });
                }
            });

            $(document).on('click','.deletebtn',function(event){
                event.preventDefault();

                let id = $(this).data('id');
                let account_no = $('#account_no').val();
                let memberType = $('#memberType').val();

                swal({
                    title: 'Are you sure?',
                    text: "You want to delete a transaction. It cannot be recovered.",
                    icon: 'warning',
                    buttons: {
                        cancel: "Cancel",
                        confirm: {
                            text: "Yes, Delete",
                            closeModal: false
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        // Show loading spinner
                        swal({
                            title: 'Deleting...',
                            text: 'Please wait while the transaction is being deleted.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false
                        });

                        $.ajax({
                            url : "{{ route('deletesecurityaccount') }}",
                            type : 'post',
                            data : {id : id,account_no:account_no,memberType:memberType},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType : 'json',
                            success : function(res){
                                if(res.status === 'success'){
                                    let account = res.account || {};
                                    let security_entries = res.security_entries || [];
                                    let opening_amount = res.opening_amount || { amount: 0, currency: '' };

                                    $('#amount').val('');
                                    $('#narration').val('');
                                    $('#cashbank').val('');
                                    $('#cashbank').trigger('change');
                                    $('#ledgerId').val('').html('<option value="" selected>Select</option>').trigger('change');

                                    ShowDataTable(security_entries, opening_amount, account);
                                }else{
                                    notify(res.messages,'warning');
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click','.editbtn',function(event){
                event.preventDefault();
                let id = $(this).data('id');
                let account_no = $('#account_no').val();
                let memberType = $('#memberType').val();

                $.ajax({
                    url : '{{ route('editsecurityacc') }}',
                    type : 'post',
                    data : {id : id,account_no:account_no,memberType:memberType},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){

                            let exitsaccount = res.exitsaccount;
                            let saving = res.saving;


                            if(exitsaccount){

                                $('#securityaccId').val(exitsaccount.id);

                                let dates = new Date(exitsaccount.transactionDate);
                                let day = String(dates.getDate()).padStart(2, '0');
                                let month = String(dates.getMonth() + 1).padStart(2, '0');
                                let year = dates.getFullYear();
                                let formattedDate = `${day}-${month}-${year}`;

                                if(exitsaccount.transactionType === 'Withdraw' || exitsaccount.transactionType === 'Deposit'){
                                    $('#transactionDate').val(formattedDate);
                                    {{--  $('#memberType').val(exitsaccount.memberType);  --}}
                                    $('#account_no').val(exitsaccount.staff_no);
                                    $('#transactionType').val(exitsaccount.transactionType).change();
                                    if(exitsaccount.paymentType === 'C002'){
                                        $('#cashbank').val('Cash').change();
                                    }else{
                                        $('#cashbank').val(exitsaccount.paymentType).change();
                                    }
                                    $('#ledgerId').val(exitsaccount.bank);
                                    $('#amount').val(exitsaccount.withdrawAmount ? exitsaccount.withdrawAmount :  exitsaccount.depositAmount);
                                    $('#narration').val(exitsaccount.narration);
                                    $('#savingaccounts').val('');

                                    $('#transactionType option').each(function() {
                                        if ($(this).val() !== 'Transfer') {
                                            $(this).hide();
                                        }
                                    });

                                    setTimeout(() => {
                                        if ($('#memberType option[value="' + exitsaccount.type + '"]').length === 0) {
                                            $('#memberType').val(exitsaccount.type).change();
                                        }

                                        // Make dropdown simulate readonly
                                        $('#memberType').on('focus mousedown', function(e) {
                                            e.preventDefault(); // Prevent dropdown interaction
                                        });

                                        $('#account_no').prop('readonly', true);
                                    }, 100);

                                    $('.savingaccountdiv').hide();
                                    $('.banktrfd').show();

                                }else{
                                    $('#transactionDate').val(formattedDate);
                                    {{--  $('#memberType').val(exitsaccount.memberType);  --}}
                                    $('#account_no').val(exitsaccount.staff_no);
                                    $('#transactionType').val(exitsaccount.transactionType).change();
                                    $('#cashbank').val('');
                                    $('#ledgerId').val('');

                                    $('#amount').val(exitsaccount.withdrawAmount);
                                    $('#narration').val(exitsaccount.narration);

                                    $('#transactionType option').each(function() {
                                        if ($(this).val() !== 'Withdraw') {
                                            $(this).hide();
                                        }
                                    });

                                    $('.banktrfd').hide();
                                    $('.savingaccountdiv').show();


                                    if (saving && saving.membershipno) {

                                        if ($('#savingaccounts option[value="' + saving.membershipno + '"]').length === 0) {
                                            $('#savingaccounts').append(`<option value="${saving.membershipno}">${saving.membershipno}</option>`);
                                        }

                                        $('#savingaccounts').val(saving.membershipno).change();
                                        $('#savingaccounts').prop('readonly',true);

                                    } else {
                                        console.error('Saving object or membershipno is missing:', saving);
                                    }

                                }


                            }
                        }else{
                            notify(res.messages,'warning');
                        }
                    }
                });
            });


            $(document).on('click','.editinterest',function(event){
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url : '{{ route('editsecurityinterest') }}',
                    type : 'post',
                    data : {id : id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){
                            let exitsId = res.exitsId;

                            if(exitsId){
                                let entry_date = new Date(exitsId.transactionDate);
                                let day = entry_date.getDate();
                                let month = entry_date.getMonth() + 1;
                                let year = entry_date.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                let transcationDate = `${day}-${month}-${year}`;

                                $('#interest_date').val(transcationDate);
                                $('#interestid').val(exitsId.id);
                                $('#ineterest_account').val(exitsId.staff_no);
                                $('#interest_paid_amount').val(exitsId.depositAmount);
                                $('#memberTypes').val(exitsId.type);
                                $('#interestModal').modal('show');
                            }else{
                                $('#interestid').val('');
                                $('#ineterest_account').val('');
                                $('#interest_paid_amount').val('');
                                $('#memberTypes').val('');
                                $('#interestModal').modal('hide');
                            }
                        }
                    },error : function(xhr,status,error){
                        notify(error+res.messages,'warning');
                    }
                });
            });


            $(document).on('submit', '#interestForm', function (event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('securityinterestupdate') }}",
                    type: 'post',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            let account = res.account || {};
                            let security_entries = res.security_entries || [];
                            let opening_amount = res.opening_amount || { amount: 0, currency: '' };
                            
                            notify(res.messages, 'success');
                            $('#interestForm')[0].reset();
                            $('#interestModal').modal('hide');
                            ShowDataTable(security_entries, opening_amount, account);
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function (err) {
                        console.error(err);
                        notify('An error occurred while processing your request', 'danger');
                    }
                });
            });
        });


    </script>
@endpush


@push('style')
    <style>
        .tablee table th,
        .tablee table td {
            padding: 8px;
        }

        .saving_column {
            position: relative;
        }

        {{--  .saving_column .error {
            position: absolute;
            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }  --}} .page_headings h4,
        .page_headings h6 {
            margin-bottom: 0;
        }

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
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

        .accountList ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }

        .accountListt ul {
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

        .accountListt ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }

        button.btn.editbtn {
            padding: 0 5px;
        }

        button.btn.deletebtn {
            padding: 0 5px;
        }

        .error {}
    </style>
@endpush
