@extends('layouts.app')
@section('title', " Recovery")
@section('pagetitle', "Recovery")

@php
$table = "no";

@endphp
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card">
                <h5 class="card-header">Loan Recovery</h5>
                <div class="table-responsive text-nowrap pb-3">
                    <form id="installmentsPaid" action="{{route('loanupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="paidinstallments" />
                        <input type="hidden" name="id" value="" />
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">Date</label>
                                    <input id="transactionDate" type="text" name="loanDate" class="form-control form-control-sm mydatepic" value="{{date('d-m-Y')}}" placeholder="DD-MM-YYYY" required />
                                </div>
                                <div class="mb-3 col-md-3 col-sm-12">
                                    <label class="form-label mb-1" for="status-org">Member </label>
                                    <select name="memberType" id="memberType" class=" form-select form-select-sm" data-placeholder="Select Member">
                                        <option value="Member">Member</option>
                                        <option value="Nominal Member">Nominal Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">Ac Number</label>
                                    <input type="text" id="accountNumber" name="accountNumber" class="form-control form-control-sm" placeholder="Enter value" required />
                                </div>
                                <!-- <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="loanid">Loan </label>
                                    <select name="loanidDetails" id="loanId" class="select2 form-select form-select-sm" data-placeholder="Active" onchange="getloanDetails(this)">

                                    </select>
                                </div> -->
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">Princple</label>
                                    <input type="text" name="PrincipalTillDate" id="TPrincipal" value="0" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="name" class="form-label"> Pending Intrest</label>
                                    <input type="text" id="TInterest" autocomplete="off" name="PendingIntrTillDate" value="0" class="form-control form-control-sm pendingintrest" readonly>
                                </div>
                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Intrest</label>
                                    <input type="text" id="InterestTillDate" value="0" name="InterestTillDate" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="ovrdue" class="form-label">Overdue Int.</label>
                                    <input type="text" id="overdue" value="0" name="overdue" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">PENAL INT.</label>
                                    <input type="text" id="TPenalty" autocomplete="off" name="PenaltyTillDate" value="0" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Net</label>
                                    <input type="text" id="TillDateTotal" value="0" name="TotalTillDate" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">PAYMENT RECEIVED</label>
                                    <div id="receiveamount">
                                        <input type="text" required="" autocomplete="off" onkeyup="Validate_number(this)" name="ReceivedAmount" class="form-control form-control-sm">
                                    </div>
                                </div>
                                      <div class="mb-3 col-md-3 col-sm-12">
                                    <label class="form-label mb-1" for="status-org">BY </label>
                                    <select name="loanBy" id="loanBy" class="form-select form-select-sm" onchange="loanby(this)">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 col-sm-12 bank" style="display: none;">
                                    <label for="txndate" class="form-label">Select Bank</label>
                                    <select name="ledgerId" id="status-org" class="form-select form-select-sm" data-placeholder="Active">
                                        <option value="">Select</option>
                                        @foreach($banktypes as $banktype)
                                        <option value="{{$banktype->id}}">{{$banktype->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 col-sm-12 bank" style="display: none;">
                                    <label for="chequeNo" class="form-label">Cheque No Bank</label>
                                    <input id="chequeNo" type="text" name="chequeNo" class="form-control form-control-sm" placeholder="Cheque No" />
                                </div>
                                <div class="col-md-4 col-sm-6 mb-3" id="issubmit" style="display: none;">
                                    <button id="submitButton" class="btn btn-primary waves-effect waves-light mt-4" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span> Loading...">Submit</button>
                                    <button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#modalLong">
                                        View Installment
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card">
                <h5 class="card-header">Pending Overall Loan</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <caption class="ms-4">
                            Details of Loan
                        </caption>
                        <tbody>
                            <tr>
                                <th>Princple</th>
                                <td class="totalprincple">0</td>
                            </tr>
                            <tr>
                                <th>Pending Intrest</th>
                                <td class="pendingintrest">0</td>
                            </tr>
                            <tr>
                                <th>Current Int</th>
                                <td class="currentintrest">0</td>
                            </tr>
                            <tr>
                                <th>Penal Int.</th>
                                <td class="penalinrest">0</td>
                            </tr>
                            <tr>
                                <th>Net</th>
                                <td class="netintrest">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card">
                <h5 class="card-header">Loan Details</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <caption class="ms-4">
                            Details of Loan
                        </caption>
                        <tbody>
                            <tr>
                                <th>Loan Amount</th>
                                <td class="loanAmount"></td>
                            </tr>
                            <tr>
                                <th>Loan Date</th>
                                <td class="loanDate"></td>
                            </tr>

                            <tr>
                                <th>Loan Type</th>
                                <td class="loanname"></td>
                            </tr>
                            <!-- <tr>
                                <th>Purpose</th>
                                <td class="purpose"></td>
                            </tr> -->
                            <tr>
                                <th>Loan By</th>
                                <td class="loanBy"></td>
                            </tr>
                            <tr>
                                <th>Per Note No</th>
                                <td class="pernote"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="col-sm-12 col-md-12">
            <div class="card recovery" style="display: none;">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Recovery Date</th>
                                <th>Principal Received</th>
                                <th>Interest Received</th>
                                <th>Penal Interest Received</th>
                                <th>Total Received</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 recoveryData">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="card loandetails" style="display: none;">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Loan Name</th>
                            <th>Ac</th>
                            <th>Loan A/c</th>
                            <th>Aamount</th>
                            <th>Type</th>
                            <th>Installment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 transactionData">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Installments </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="installmentUpdateMaster" action="{{route('loanupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="installmentsupdate" />
                <input type="hidden" name="id" value="new" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <label for="name" class="form-label">Date</label>
                            <input id="installmentsDate" type="text" name="loanDate" class="form-control form-control-sm mydatepic" placeholder="DD-MM-YYYY" required />
                        </div>

                        <!--<div class="col-md-3 col-sm-6 mb-3">-->
                        <!--    <label for="name" class="form-label">Loan Ac Number</label>-->
                        <!--    <input type="text" id="loanAcNo" name="loanAcNo" class="form-control form-control-sm" placeholder="Enter value" required />-->
                        <!--</div>-->
                        <!-- <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="loanid">Loan </label>
                                    <select name="loanidDetails" id="loanId" class="select2 form-select form-select-sm" data-placeholder="Active" onchange="getloanDetails(this)">

                                    </select>
                                </div> -->
                        <div class="col-md-4 col-sm-6 mb-3">
                            <label for="name" class="form-label">Princple</label>
                            <input type="text" name="PrincipalTillDate" id="TPrincipal" readonly="" value="0" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label for="name" class="form-label"> Pending Intrest</label>
                            <input type="text" id="TInterest" autocomplete="off" name="PendingIntrTillDate" value="0" class="form-control form-control-sm pendingintrest">
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label for="name" class="form-label">Intrest</label>
                            <input type="text" readonly="" id="TPendingInterest" value="0" name="InterestTillDate" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label for="ovrdue" class="form-label">Overdue Intrest</label>
                            <input type="text" readonly="" id="overdue" value="0" name="overdue" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <label for="name" class="form-label">PENAL INT.</label>
                            <input type="text" id="TPenalty" autocomplete="off" name="PenaltyTillDate" value="0" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label for="name" class="form-label">Net</label>
                            <input type="text" id="TillDateTotal" readonly="" value="0" name="TotalTillDate" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <label for="name" class="form-label">PAYMENT RECEIVED</label>
                            <div id="receiveamount">
                                <input type="text" required="" autocomplete="off" onkeyup="Validate_number(this)" name="ReceivedAmount" class="form-control form-control-sm">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalLong" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLongTitle">Installments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap print-content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Inst. Date</th>
                                <th>Principal</th>
                                <th>Interest</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="installmentsdata">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="printButton" class="btn btn-primary">Print</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var currentDate = moment().format('DD-MM-YYYY');
        $("#transactionDate").val(currentDate);

        $("#accountNumber").blur(function() {
            var account = $(this).closest('form').find('input[name="accountNumber"]').val();
            var member = $('#memberType').val();
            getLoanAc(account, member);
            $("#sharemember").block({
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

        $("#installmentsPaid").validate({
            rules: {
                loanDate: {
                    required: true,
                    customDate: true,
                },
                memberType: {
                    required: true,
                },
                accountNumber: {
                    required: true,
                    number: true,
                },
                memberType: {
                    required: true,
                },
            },
            messages: {
                loanDate: {
                    required: "Please enter Loan Date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
                amount: {
                    required: "Please enter amount",
                    number: "Amount number should be numeric",
                },
                pernote: {
                    required: "Please enter pernote",
                    number: "Pernote number should be numeric",
                },
                installmentType: {
                    required: "Please select installment Type",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('#installmentsPaid');
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
                            var account = $('#loanmember').find(
                                'input[name="account"]').val();
                            form[0].reset();
                            var currentDate = moment().format('DD-MM-YYYY');
                            $("#transactionDate").val(currentDate);
                            setReciverydate(data.recovery);
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


        $("#installmentUpdateMaster").validate({
            rules: {
                loanDate: {
                    required: true,
                    customDate: true,
                },
                PrincipalTillDate: {
                    required: true,
                     number: true,
                },
                TPendingInterest: {
                    required: true,
                    number: true,
                },
                overdue: {
                    required: true,
                     number: true,
                },
                PenaltyTillDate: {
                    required: true,
                     number: true,
                },
                  TotalTillDate: {
                    required: true,
                     number: true,
                },
                ReceivedAmount: {
                    required: true,
                     number: true,
                },
                 InterestTillDate: {
                    required: true,
                     number: true,
                },
            },
            messages: {
                loanDate: {
                    required: "Please enter Loan Date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
                PrincipalTillDate: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                TPendingInterest: {
                   required: "Please enter value",
                    number: "Value should be numeric",
                },
                overdue: {
                   required: "Please enter value",
                    number: "Value should be numeric",
                },
                PenaltyTillDate: {
                   required: "Please enter value",
                    number: "Value should be numeric",
                },
                  TotalTillDate: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                ReceivedAmount: {
                   required: "Please enter value",
                    number: "Value should be numeric",
                },
                 InterestTillDate: {
                   required: "Please enter value",
                    number: "Value should be numeric",
                },
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('#installmentUpdateMaster');
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
                            var account = $('#loanmember').find(
                                'input[name="account"]').val();
                            form[0].reset();
                            var currentDate = moment().format('DD-MM-YYYY');
                            $("#transactionDate").val(currentDate);
                            setReciverydate(data.recovery);
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

    });

    function getInstallmentsPaid(data) {

        $('#basicModal').find('input[name="id"]').val(data.id);
        $('#basicModal').find('input[name="princple"]').val(data.principal);
        $('#basicModal').find('input[name="intrest"]').val(data.interest);
        $('#basicModal').find('input[name="total"]').val(data.total);
        $('#basicModal').modal('show');
    }



    function getLoanAc(account, member) {
        $.ajax({
            url: "{{route('loanupdate')}}",
            type: "POST",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $(".loandetails").css("display", "none");
                $(".recovery").css("display", "none");
                $('.transactionData').html('');
                $(".recoveryData").html("");
                // swal({
                //     title: 'Wait!',
                //     text: 'We are fetching loan details.',
                //     allowOutsideClick: () => !swal.isLoading(),
                //     onOpen: () => {
                //         swal.showLoading()
                //     }
                // });
            },
            data: {
                'actiontype': "getLoanAc",
                'loanAcNo': account,
                'member': member
            },
            success: function(data) {
                swal.close();
                if (data.status == "success") {
                    var out = `<option value="">Select Loan</option>`;
                    $.each(data.data, function(index, value) {
                        out += `<option value="` + value.id + `">` + value.loanname + `(` + value
                            .loanAmount +
                            `)</option>`;
                    });

                    // $('[name="loanidDetails"]').html(out);
                    $(".loandetails").css("display", "block");
                    $(".transactionData").html("");
                    var tbody = '';
                    if (data.data.length === 0) {} else {
                        $.each(data.data, function(index, val) {
                            if (val.status == "Disbursed") {
                                var trclass = `class="table-success"`;
                            } else if (val.status == "Closed") {
                                var trclass = `class="table-danger"`;
                            } else if (val.status == "Inactive") {
                                var trclass = `class="table-warning"`;
                            }
                            tbody += "<tr onclick='rowClicked(this)' " + trclass + ">" +
                                "<td style='display:none'>" + val.id + "</td>" +
                                "<td>" + formatDate(val.loanDate) + "</td>" +
                                "<td>" + val.purpose + "</td>" +
                                "<td>" + val.accountNo + "</td>" +
                                "<td>" + val.loanAcNo + "</td>" +
                                "<td>" + val.loanAmount + "</td>" +
                                "<td>" + val.installmentType + "</td>" +
                                "<td>" + "50000" + "</td>" +
                                "<td>" + val.status + "</td>" +
                                `<td>
                                  <a href="javascript:void(0);" onclick="viewloan('` +
                                val.id + `')"><i class="ti ti-eye me-1"></i></a>`;
                        });
                    }
                    $('.transactionData').html(tbody);
                } else {
                    notify(data.status, 'warning');
                }
            }
        });
    }

    function rowClicked(ele) {
        var id = $(ele).closest('tr').find('td').eq(0).text();
        var transactiondate = $('#transactionDate').val();

        $.ajax({
            url: "{{route('loanupdate')}}",
            type: "POST",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                swal({
                    title: 'Wait!',
                    text: 'We are fetching loan details.',
                    allowOutsideClick: () => !swal.isLoading(),
                    onOpen: () => {
                        swal.showLoading()
                    }
                });
            },
            data: {
                'actiontype': "getloandetails",
                'id': id,
                'transactiondate': transactiondate
            },
            success: function(data) {
                swal.close();
                $('.installmentsdata').html('');
                if (data.status == "success") {
                    $("#issubmit").css("display", "block");
                    setReciverydate(data.recovery);
                    $.each(data.data, function(index, values) {
                        $("." + index).text(values);
                    });
                    $.each(data.loandetails, function(index, values) {
                        $("." + index).text(values);
                    });
                    $('#installmentsPaid').find('input[name="PrincipalTillDate"]').val(data.loandetails.principal);

                    $('#installmentsPaid').find('input[name="id"]').val(id);
                    $('#installmentsPaid').find('input[name="InterestTillDate"]').val(data.loandetails.currentintrest);

                    $('#installmentsPaid').find('input[name="TotalTillDate"]').val(data.loandetails.netintrest);

                    $('#installmentsPaid').find('input[name="PendingIntrTillDate"]').val(data.loandetails.pendingintrest);

                    {{--  $('#installmentsPaid').find('input[name="overdue"]').val(data.loandetails.overdueintrest);  --}}

                    var tbody;
                    if (data.installmet.length === 0) {} else {
                        var srno = 1;
                        $.each(data.installmet, function(index, val) {
                            tbody += "<tr>" +
                                "<td>" + srno++ + "</td>" +
                                "<td>" + moment(val.installmentDate).format('DD-MM-YYYY') + "</td>" +
                                "<td>" + val.principal + "</td>" +
                                "<td>" + val.interest + "</td>" +
                                "<td>" + val.total + "</td>" +
                                "</tr>";
                        });
                        $('.installmentsdata').html(tbody);
                    }
                } else {
                    notify(data.status, 'warning');
                }
            }
        });
    }

    function setReciverydate(recovery) {

        $(".recovery").css("display", "block");
        $(".recoveryData").html("");
        var tbody = '';
        var i = 1;
        if (recovery.length === 0) {} else {
            $.each(recovery, function(index, val) {

                tbody += "<tr>" +
                    "<td>" + i++ + "</td>" +
                    "<td>" + formatDate(val.receiptDate) + "</td>" +
                    "<td>" + val.principal + "</td>" +
                    "<td>" + val.interest + "</td>" +
                    "<td>" + val.penalInterest + "</td>" +
                    "<td>" + val.receivedAmount + "</td>" +
                    "<td>" + "<a onclick=\"deleteItem('" + val.id +
                    "', 'deleteDistrict')\" href='javascript:void(0);' ><i class='ti ti-trash me-1'></i></a > " +
                    "</td>";
            });
   // ` <a onclick=\"editItem('" +  val.id +  "','" + formatDate(val.receiptDate) + "','" + val.receiptDate + "','123'," + val.principal +  "," + val.pendingInterest + "," + val.interest + "," + val.penalInterest + ",0," + val   .receivedAmount +  ")\" href='javascript:void(0);' ><i class='ti ti-pencil me-1'></i></a >`;

        }
        $('.recoveryData').html(tbody);
    }

    $(function() {
        $('#TPrincipal, #TPendingInterest, #TInterest,#TPenalty,#TTotal,#FPrincipal,#FPendingInterest,#FInterest,#FPenalty,#FTotal')
            .keyup(function() {
                var TPrincipal = parseFloat($('#TPrincipal').val()) || 0;
                var TPendingInterest = parseFloat($('#TPendingInterest').val()) || 0;
                var TInterest = parseFloat($('#TInterest').val()) || 0;
                var TPenalty = parseFloat($('#TPenalty').val()) || 0;
                var TTotal = parseFloat($('#TTotal').val()) || 0;
                var FPrincipal = parseFloat($('#FPrincipal').val()) || 0;
                var FPendingInterest = parseFloat($('#FPendingInterest').val()) || 0;
                var FInterest = parseFloat($('#FInterest').val()) || 0;
                var FPenalty = parseFloat($('#FPenalty').val()) || 0;
                var FTotal = parseFloat($('#FTotal').val()) || 0;

                FianlInterest = Math.round(TInterest);
                $('#FianlInterest').val(FianlInterest);

                FianlPenalty = Math.round(TPenalty);
                $('#FianlPenalty').val(FianlPenalty);

                TillDateTotal = Math.round(TPrincipal + TPendingInterest + TInterest + TPenalty);
                $('#TillDateTotal').val(TillDateTotal);

                FianlTotal = Math.round(FPrincipal + TPendingInterest + TInterest + TPenalty);
                $('#FianlTotal').val(FianlTotal);
            });
    });

    function editItem(id, txndate, member, loanacountno, princple, pendingInt, currentInt, penalInt, net, recivedAmt) {
        $('#basicModal').find('.msg').text("Edit");
        $('#basicModal').find('input[name="id"]').val(id);
        $("#installmentsDate").val(txndate);
        $('#basicModal').find('input[name="loanAcNo"]').val(loanacountno);
        $('#basicModal').find('input[name="PrincipalTillDate"]').val(princple);
        $('#basicModal').find('input[name="PendingIntrTillDate"]').val(pendingInt);
        $('#basicModal').find('input[name="InterestTillDate"]').val(currentInt);
        $('#basicModal').find('input[name="PenaltyTillDate"]').val(penalInt);
        $('#basicModal').find('input[name="TotalTillDate"]').val(net);
        $('#basicModal').find('input[name="ReceivedAmount"]').val(recivedAmt);
        $('#basicModal').modal('show');
    }


    function getinstallments() {
        var id = $('#installmentsPaid').find('input[name="id"]').val();
        console.log(id);
        $.ajax({
            url: "{{route('loanupdate')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            data: {
                'id': id,
                'actiontype': "getInstallmentslist",
            },
            beforeSend: function() {
                blockForm('#installmentsPaid')
            },
            complate: function() {
                $("#installmentsPaid").unblock();
            },
            success: function(data) {
                $("#installmentsPaid").unblock();
                if (data.status == "success") {
                    console.log(data);
                } else {
                    notify(data.status, 'danger');
                }
            },
            error: function(error) {
                $("#installmentsPaid").unblock();
                notify("Something went wrong", 'warning');
            }
        });
    }

    function deleteItem(id){
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
                            url: "{{route('loanupdate')}}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: {
                                'actiontype': "deleteinstallmets",
                                'id': id,
                            },
                            success: function(data) {
                                    swal.close();
                                if (data.status == "success") {
                                     setReciverydate(data.recovery);
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

    function viewloan(id){

    }

      function loanby(ele) {
        var type = $(ele).val();
        if (type == 'Transfer') {
            $(".bank").css("display", "block");
        } else {
            $(".bank").css("display", "none");
        }
    }
    function Validate_number(){

    }

</script>
@endpush
