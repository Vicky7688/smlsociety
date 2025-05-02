@extends('layouts.app')
@section('title', " Loan Master")
@section('pagetitle', "Loan Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="row">
        <div class="col-12">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Loan Module / </span> Loan Master </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Loan Master</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Loan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tablee">
                        <div class="table-responsive tabledata">
                            <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Member Type</th>
                                        <th>Type</th>
                                        {{-- <th>Name</th> --}}
                                        <th>Processing Fee</th>
                                        <th>Loan App. Charges</th>
                                        <th>Interest</th>
                                        <th>PenalInt</th>
                                        {{-- <th>EMI Date</th> --}}
                                        <th>InsType</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Add Loan Master </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="loanMaster" action="{{ route('masterupdate') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="loanMaster" />
                <input type="hidden" name="id" id="new">
                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="memberType" class="form-label">Member Type</label>
                            <select name="memberType" class="select2 form-select formInputsSelectReport" id="memberType">
                                <option value="Member" default selected>Member</option>
                                <option value="NonMember">Nominal Member</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="loanType" class="form-label">Loan Type</label>
                            <select name="loanType" class="select2 form-select formInputsSelectReport" id="status-org">
                                @foreach($types as $type)
                                 <option value="{{$type->name}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="memberType" class="form-label">Loan Type</label>
                            <select name="loantypess" class="select2 form-select formInputsSelectReport" id="loantypess">
                                <option value="MTLoan" default selected>MT Loan</option>
                                <option value="RD">RD Against Loan</option>
                                <option value="FD">FD Against Loan</option>
                                <option value="DailyDeposit">Daily Deposit Loan</option>
                            </select>
                        </div> --}}


                        <!--<div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">-->
                        <!--    <label for="name" class="form-label">Loan Name</label>-->
                        <!--    <input type="text" name="name" id="name" class="form-control formInputsReport">-->
                        <!--</div>-->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="processingFee" class="form-label">Processing Fee</label>
                            <input type="text" name="processingFee" value="0" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="loan_app_charges" class="form-label">Loan App. Charges</label>
                            <input type="text" name="loan_app_charges" value="0" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="interest" class="form-label">Interest %</label>
                            <input type="text" name="interest" class="form-control formInputsReport" placeholder="Enter Interest %" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="penalInterest" class="form-label">Penalty Interest</label>
                            <input type="text" name="penaltyInterest" class="form-control formInputsReport" placeholder="Enter Penal Interest" required />
                        </div>
                        {{-- <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="emiDate" class="form-label">EMI Date</label>
                            <input type="text" name="emiDate" class="form-control formInputsReport" placeholder="Enter Value" value="0" min="0" max="30" required />
                        </div> --}}
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="insType" class="form-label">Installment Type</label>
                            <select name="insType" class="select2 form-select formInputsSelectReport" id="insType">
                                <option value="Daily" default selected>Daily</option>
                                <option value="Monthly" >Monthly</option>
                                <option value="Half Yearly">Half Yearly</option>
                            </select>
                        </div>
                        {{-- <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="advancementDate" class="form-label">Advancement Date</label>
                            <select name="advancementDate" class="select2 form-select formInputsSelectReport" id="advancementDate">
                                <option value="Yes" default selected>Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div> --}}
                        {{-- <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="recoveryDate" class="form-label">Recovery Date</label>
                            <select name="recoveryDate" class="select2 form-select formInputsSelectReport" id="recoveryDate">
                                <option value="Yes" default selected>Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div> --}}
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="years" class="form-label">Year</label>
                            <input type="text" value="1" name="years" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="months" class="form-label">Month</label>
                            <input type="text" value="0" name="months" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="days" class="form-label">Days</label>
                            <input type="text" name="days" value="0" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="select2 form-select formInputsSelectReport" id="" data-placeholder="Active">
                                <option value="Active" default selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')

<style>
    html:not([dir=rtl]) .modal .btn-close {
        transform: none !important;
    }

    .btn-close {
        top: 1.35rem !important;
    }

    /* #datatable_wrapper .dataTables_info,
    #datatable_wrapper .dataTables_paginate {
        display: none;
    } */

</style>

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/loanMaster";
        var onDraw = function() {

        };

        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": "memberType"
            },
            {
                "data": "loanType"
            },
            // {
            //     "data": "loantypess"
            // },
            {
                "data": "processingFee"
            },
            {
                "data": "loan_app_charges"
            },
            {
                "data": "interest"
            },
            {
                "data": "penaltyInterest"
            },
            // {
            //     "data": "emiDate"
            // },
            {
                "data": "insType"
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == "Active") {
                        var out = `<span class="badge bg-label-success">Active</span><br/>`;
                    } else if (full.status == "Inactive") {
                        var out = `<span class="badge bg-label-danger">Inactive</span><br/>`;
                    } else {
                        var out = `<span class="badge badge-danger">` + full.status + `</span><>br/`;
                    }
                    return out;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    var menu =
                        `<div style="display: flex;justify-content: space-evenly; align-items: center;"><a href="javascript:void(0);" onclick="editLoanMasterSetup('` + full.id + `','` +
                        full.memberType +
                        `','` + full.loanType + `','` + full.processingFee + `','` + full.loan_app_charges + `','` + full.interest + `','` + full.penaltyInterest + `','` + full.years + `','` + full
                        .months + `','` + full.days + `','` + full.status + `' ,'` + full.insType + `' )"><i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i></a>`;
                    menu += `<a onclick="deleteItem('` + full.id + `', 'deleteLoan')" href="javascript:void(0);" ><i class="fa-solid fa-trash iconsColorCustom"></i></a > </div>`;
                    return menu;
                }
            }
        ];

        datatableSetup(url, options, onDraw, '#datatable', {
            columnDefs: [{
                orderable: false,
                width: '80px',
                targets: [0]
            }]
        });

        $("#loanMaster").validate({
            rules: {
                memberType: {
                    required: true,
                },
                loanType: {
                    required: true,
                },
                name: {
                    required: true,
                },
                processingFee: {
                    required: true,
                    number: true,
                },
                interest: {
                    required: true,
                    number: true,
                },
                penaltyInterest: {
                    required: true,
                    number: true,
                },
                // emiDate: {
                //     required: true,
                //     number: true,
                //     min: 0,
                //     max: 25
                // },
                insType: {
                    required: true,
                },
                years: {
                    required: true,
                    number: true,
                },
                months: {
                    required: true,
                    number: true,
                },
                days: {
                    required: true,
                    number: true,
                    // max: 29
                },
                // advancementDate: {
                //     required: true,
                // },
                // recoveryDate: {
                //     required: true,
                // },
                status: {
                    required: true,
                }
            },
            messages: {
                memberType: {
                    required: "Please enter value",
                },
                loanType: {
                    required: "Please enter value",
                },
                name: {
                    required: "Please enter value",
                },
                processingFee: {
                    required: "Please enter value",
                },
                interest: {
                    required: "Please enter value",
                },
                penaltyInterest: {
                    required: "Please enter value",
                },
                // emiDate: {
                //     required: "Please enter value",
                // },
                insType: {
                    required: "Please enter value",
                },
                years: {
                    required: "Please enter value",
                },
                days: {
                    required: "Please enter value",
                },
                // advancementDate: {
                //     required: "Please enter value",
                // },
                // recoveryDate: {
                //     required: "Please enter value",
                // },
                status: {
                    required: "Please enter value"
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
                var form = $('#loanMaster');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').html(
                            '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                        ).attr(
                            'disabled', true).addClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form[0].reset();
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');

                            notify("Task successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#basicModal').modal('hide');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        })
    });

    function editLoanMasterSetup(id, memberType, loanType ,processingFee,loan_app_charges, interest, penaltyInterest, years, months, days, status, insType) {
        $('#basicModal').find('.msg').text("Edit Loan");
        $('#basicModal').find('input[name="id"]').val(id);
        $('#basicModal').find('select[name="memberType"]').val(memberType).trigger('change');
        $('#basicModal').find('select[name="loanType"]').val(loanType).trigger('change');

        // setTimeout(() => {
        //     $('#basicModal').find('select[name="loantypess"]').val(loantypess).trigger('change');
        // }, 100);



        $('#basicModal').find('input[name="loan_app_charges"]').val(loan_app_charges);
        $('#basicModal').find('input[name="processingFee"]').val(processingFee);
        $('#basicModal').find('input[name="interest"]').val(interest);
        $('#basicModal').find('input[name="penaltyInterest"]').val(penaltyInterest);
        // $('#basicModal').find('input[name="emiDate"]').val(emiDate);
        $('#basicModal').find('select[name="insType"]').val(insType).trigger('change');
        $('#basicModal').find('input[name="years"]').val(years);
        $('#basicModal').find('input[name="months"]').val(months);
        $('#basicModal').find('input[name="days"]').val(days);
        // $('#basicModal').find('select[name="advancementDate"]').val(advancementDate).trigger('change');
        // $('#basicModal').find('select[name="recoveryDate"]').val(recoveryDate).trigger('change');
        $('#basicModal').find('select[name="status"]').val(status).trigger('change');
        $('#basicModal').modal('show');
    }

    function addSetup() {
        $('#loanMaster')[0].reset();
        $('#basicModal').find('.msg').text("Add");
        $('#basicModal').find('input[name="id"]').val("new");
        $('#basicModal').modal('show');
    }

    function deleteItem(id, actiontype) {
        $.ajax({
            // url: '{{ route("delete", ["actiontype" => ":actiontype"]) }}'.replace(':actiontype', actiontype),
            url : "{{ route('deleteloanmaster') }}",
            type: 'post',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "id": id,
                "actiontype": actiontype
            },
            beforeSend: function() {
                swal({
                    title: 'Wait!',
                    text: 'Please wait, we are deleting data',
                    onOpen: () => {
                        swal.showLoading();
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
            },
            success: function(data) {
                swal.close();
                if (data.status == "success") {
                    $('#datatable').dataTable().api().ajax.reload();
                    swal({
                        type: 'success',
                        title: 'Success',
                        text: "Data Successfully Deleted",
                        showConfirmButton: true,
                    });
                } else {
                    swal({
                        type: 'error',
                        title: 'Failed',
                        text: "Something went wrong",
                        showConfirmButton: true,
                    });
                }
            },
            error: function() {
                swal.close();
                notify('Something went wrong', 'warning');
            },
            complete: function() {}
        });
    }
</script>
@endpush
