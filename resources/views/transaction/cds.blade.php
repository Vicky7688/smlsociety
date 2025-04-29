@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between">
                <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                    <h4 class=""><span class="text-muted fw-light">Transactions / </span>Compulsory Deposit</h4>
                </div>
                <div class="col-md-3 accountHolderDetails">
                    <h6 class=""><span class="text-muted fw-light">Name: </span><span id="memberName"></span></h6>
                    <h6 class="pt-2"><span class="text-muted fw-light">Balance: </span><span id="memberBalance"></span></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body cardsY">
                    <form action="javascript:void(0)" id="formData" name="formData">
                        @csrf
                        <div class="row row-gap-2">
                            <input type="hidden" name="savingId" id="savingId">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">Date</label>
                                <input type="date" class="form-control formInputs" placeholder="YYYY-MM-DD" id="transactionDate" name="transactionDate" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="transactionType" class="form-label">Action</label>
                                <select class="form-select formInputsSelect" id="transactionType" name="transactionType">
                                    <option value="Deposit">Deposit</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control formInputs" id="accountNo" name="accountNo" placeholder="Account No" autocomplete="off" />
                                <div id="accountList" class="accountList"></div>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="transactionAmount" class="form-label">Amount</label>
                                <input type="text" step="any" min="1" class="form-control formInputs" placeholder="0.00" id="transactionAmount" name="transactionAmount" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="paymentType" class="form-label">Payment Type</label>
                                <select class="form-select formInputsSelect" id="paymentType" name="paymentType">
                                    @if (!empty($groups))
                                    @foreach ($groups as $group)
                                    <option value="{{$group->groupCode}}">{{$group->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                <label for="bank" class="form-label">Bank</label>
                                <select class="form-select formInputsSelect" id="bank" name="bank">
                                    @if (!empty($ledgers))
                                    @foreach ($ledgers as $ledger)
                                    <option value="{{$ledger->ledgerCode}}">{{$ledger->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                <label for="agentId" class="form-label">Agent</label>
                                <select class="form-select formInputsSelect" id="agentId" name="agentId">
                                    @if(!empty($agents))
                                    @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="">No Agent Present</option>
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                <label for="narration" class="form-label">Narration</label>
                                <input type="text" class="form-control formInputs" id="narration" placeholder="Narration" name="narration" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding savingColumnButton">
                                <div class="d-flex h-100 justify-content-end text-end">
                                    <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card tablee">
        <div class="card-body data_tables">
            <div class="table-responsive tabledata">
                <table class="table text-center table-bordered">
                    <thead class="table_head verticleAlignCenter">
                        <tr>
                            <th scope="col">Sr.</th>
                            <th scope="col">Date</th>
                            <th scope="col">Voucher</th>
                            <th scope="col">Deposit</th>
                            <th scope="col">Wthdraw</th>
                            <th scope="col">Balance</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Action</th>

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
                <h5 class="modal-title"><span class="text-muted fw-light">Previous Balance: </span><span id="previousBalance"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="modifiedFormData" name="modifiedFormData">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="modifyId" name="modifyId">
                    <div class="row row-gap-3 justify-content-center">
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedTransactionDate" class="form-label">Date</label>
                            <input type="date" class="form-control formInputs" placeholder="YYYY-MM-DD" id="modifiedTransactionDate" name="modifiedTransactionDate" max="{{ now()->format('Y-m-d') }}" />
                            <p class="error"></p>
                        </div>
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedTransactionType" class="form-label">Action</label>
                            <select class="form-select formInputsSelect" id="modifiedTransactionType" name="modifiedTransactionType">
                                <option value="Deposit">Deposit</option>
                              
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedTransactionAmount" class="form-label">Amount</label>
                            <input type="text" step="any" min="0" class="form-control formInputs" id="modifiedTransactionAmount" name="modifiedTransactionAmount" />
                            <p class="error"></p>
                        </div>
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedPaymentType" class="form-label">Payment Type</label>
                            <select class="form-select formInputsSelect" id="modifiedPaymentType" name="modifiedPaymentType">
                                @if (!empty($groups))
                                @foreach ($groups as $group)
                                <option value="{{$group->groupCode}}">{{$group->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedBank" class="form-label">Bank</label>
                            <select class="form-select formInputsSelect" id="modifiedBank" name="modifiedBank">
                                @if (!empty($ledgers))
                                @foreach ($ledgers as $ledger)
                                <option value="{{$ledger->ledgerCode}}">{{$ledger->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="col-md-4 col-12 inputesPadding">
                            <label for="modifiedAgentId" class="form-label">Agent</label>
                            <select class="form-select formInputsSelect" id="modifiedAgentId" name="modifiedAgentId">
                                @if(!empty($agents))
                                @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                                @else
                                <option value="">No Agent Present</option>
                                @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="col-md-12 col-12 inputesPadding">
                            <label for="modifiedNarration" class="form-label">Narration</label>
                            <input type="text" class="form-control formInputs" id="modifiedNarration" name="modifiedNarration" />
                            <p class="error"></p>
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
<script>
    $(document).ready(function() {

        $('#paymentType').change(function() {
            $("#bank").find("option").not(":first").remove();
            var groupCode = $(this).val();
            if (groupCode == 'BANK001') {
                $.ajax({
                    url: '{{ route("getLedger") }}',
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
                        console.log("Something went wrong");
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
            $("#modifiedBank").find("option").not(":first").remove();
            var groupCode = $(this).val();
            $.ajax({
                url: '{{ route("getLedger") }}',
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
                    console.log("Something went wrong");
                }
            });
        });

        // -------------------- Form Handling Javascript (Starts) -------------------- //
        $(document).on('submit', '#formData', function(event) {
            event.preventDefault();
             var form = $('#formData');
            var element = $(this);
            var memberType = $('#memberType').val();
            var accountNo = $('#accountNo').val();
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{route("cds.store")}}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
                        displayTable(memberType, accountNo);
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],input[type='number'],select").removeClass(
                            'is-invalid');
                        $("#bank").find("option").not(":first").remove();
                        $('#formData')[0].reset();
                        notify(response.message, 'success');
                    } else if(response.status == "failed"){
                          notify(response.message, 'warning');
                    } else {
                        var errors = response.errors;
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],input[type='number'],select").removeClass(
                            'is-invalid');
                        $.each(errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(value);
                        });
                      
                    }
                },
                error: function(errors) {
                     
                     showError(errors, form);
                }
            });
        });

        // Modify Data
        $(document).on('click', '.modify', function(event) {
            event.preventDefault();
            var modifyId = $(this).attr('modifyId');
            $.ajax({
                url: "{{ route('cds.edit','') }}/" +
                    modifyId,
                type: "GET",
                success: function(response) {
                    if (response['status'] == true) {
                        //$("#modifiedBank").find("option").remove();
                        // $.each(response.ledgers, function(key, item) {
                        //     $("#modifiedBank").append(
                        //         `<option value='${item.ledgerCode}'>${item.name}</option>`
                        //     )
                        // });
                        $('#previousBalance').html(response.previousBalance);
                        $('#modifyId').val(modifyId);
                        $('#modifiedTransactionDate').val(response.data.date);
                      
                        $('#modifiedTransactionAmount').val(response.data.Deposit);
                        // $('#modifiedPaymentType').val(response.data.acc);
                        // $('#modifiedBank').val(response.data.bank);
                        $('#modifiedNarration').val(response.data.narration);
                        $('#modifiedAgentId').val(response.data.agentId);
                          $('#modifyModal').modal('show');
                    }else{
                       notify(response.message, 'warning');
                    }
                },
                error: function(errors) {
                     
                     showError(errors, "withoutform");
                }
            });
          
        });

        $('#modifiedFormData').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            var memberType = $('#memberType').val();
            var accountNo = $('#accountNo').val();
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{route("cds.update")}}',
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
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
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
                url: '{{route("cds.delete")}}',
                type: 'delete',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
                        $('#deleteModal').modal('hide');
                        displayTable(memberType, accountNo);
                        notify(response.message, 'success');
                    } else {
                        $('#deleteModal').modal('hide');
                        notify(response.message, 'warning');
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
        var memberType = $('#memberType').val();
        var accountNo = $('#accountNo').val();
        $.ajax({
            url: "{{ route('cds.getData') }}",
            type: "GET",
            data: {
                memberType: memberType,
                accountNo: accountNo
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
    $("#accountNo").on('keyup', getAccountList);


    $(document).on('click', '#accountList .memberlist', function() {
        var accountNo = $(this).text();
        var memberType = $('#memberType').val();
        $("#accountList").html("");
        displayTable(memberType, accountNo);
    });

    function displayTable(memberType, accountNo) {
        
        $('#accountNo').val(accountNo);
        $.ajax({
            url: "{{ route('cds.fetchData') }}",
            type: "GET",
            data: {
                memberType: memberType,
                accountNo: accountNo
            },
            dataType: 'json',
            success: function(response) {
                if (response['status'] == true) {
                    $('#memberName').html(response.member.name);
                    $('#memberBalance').html(response.balance);
                    var savingRow = response.saving;
                    var tableBody = $('#tableBody');
                    tableBody.empty();
                    var sr = savingRow.length + 1;
                    if(response.openingBal){
                    var balanceAmount = response.openingBal.OpeningCompulsoryDeposit;
                    if(balanceAmount != 0){
                        var row = "<tr>" +
                        "<td>" + (sr) + "</td>" +
                        "<td>" +  moment(response.openingBal.TransferDate).format('DD-MM-YYYY') + "</td>" +
                        "<td>" + response.openingBal.id + "</td>" +
                        "<td>" + response.openingBal.OpeningCompulsoryDeposit + "</td>" +
                        "<td>" + 0 + "</td>" +
                        "<td>" + balanceAmount + "</td>" +
                        "<td>" + "Opeing Account Balance" + "</td>" +
                        `<td style="display: flex;justify-content: space-evenly; align-items: center;"></td></tr> `;    
                        tableBody.prepend(row);
                    }
                }
                    
                    $.each(savingRow, function(index, saving) {
       balanceAmount += (parseInt(saving.Deposit) - parseInt(saving.Withdraw));
                        var row = "<tr>" +
                            "<td>" + (sr--) + "</td>" +
                            "<td>" + formatDate(saving.date) + "</td>" +
                            "<td>" + saving.Id + "</td>" +
                            "<td>" + saving.Deposit + "</td>" +
                            "<td>" + saving.Withdraw + "</td>" +
                            "<td>" + balanceAmount + "</td>" +
                            "<td>" + saving.narrartion + "</td>" +
                            `<td style="display: flex;justify-content: space-evenly; align-items: center;"><a class="modify" modifyId= '` + saving.Id + `'href='javascript:void(0);'>
                           <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i></a>
                            <a  class='delete' deleteId= '` + saving.Id + `' href="javascript:void(0);"><i class="fa-solid fa-trash iconsColorCustom"></i></a></td></tr> `;

                        tableBody.prepend(row);
                        $("#accountNo").val(accountNo);
                        $("#accountList").html("");
                    });
                    
                
                    
                }
            }
        });
    }
</script>
@endpush