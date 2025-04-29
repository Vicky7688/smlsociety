@extends('layouts.app')
@section('title', "Commission Master")
@section('pagetitle', "Commission Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Daily Collection / </span> Commission </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Commission Master</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Commission
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tablee">
                        <div class="table-responsive tabledata"> <!-- removed the class "card-datatable" -->
                            <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Saving</th>
                                        <th>FD</th>
                                        <th>RD</th>
                                        <th>Share</th>
                                        <th>Loan</th>
                                        <th>Daily</th>
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
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Add Commission </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="commission" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="commissionMaster" />
                <input type="hidden" name="id" value="new" />

                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="startDate" class="form-label">Date From</label>
                            <input type="date" name="startDate" value="{{ Session::get('currentdate') }}" id="startDate" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="endDate" class="form-label">Date To</label>
                            <input type="date" name="endDate" value="{{ now()->format('Y-m-d') }}" id="endDate" class="form-control formInputsReport" required />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionSaving" class="form-label">Comm on Saving</label>
                            <input type="text" name="commissionSaving" id="commissionSaving" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionFD" class="form-label">Comm on FD</label>
                            <input type="text" name="commissionFD" id="commissionFD" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionRD" class="form-label">Comm on RD</label>
                            <input type="text" name="commissionRD" id="commissionRD" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionShare" class="form-label">Comm on Share</label>
                            <input type="text" name="commissionShare" id="commissionShare" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionLoan" class="form-label">Comm on Loan</label>
                            <input type="text" name="commissionLoan" id="commissionLoan" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionDailyCollection" class="form-label">Comm on Daily Collection</label>
                            <input type="text" name="commissionDailyCollection" id="commissionDailyCollection" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="status-org" class="form-label">Status</label>
                            <select name="status" class="select21 form-select formInputsSelectReport" id="status-org" data-placeholder="Active">
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
        var url = "{{url('statement/fetch')}}/commissionMaster";
        var onDraw = function() {

        };

        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": "startDate",
                render: function(data, type, full, meta) {
                    var formattedDate = moment(data).format('DD-MM-YYYY');
                    return formattedDate;
                }
            },
            {
                "data": "endDate",
                render: function(data, type, full, meta) {
                    var formattedDate = moment(data).format('DD-MM-YYYY');
                    return formattedDate;
                }
            },
            {
                "data": "commissionSaving",
            },
            {
                "data": "commissionFD",
            },
            {
                "data": "commissionRD",
            },
            {
                "data": "commissionShare",
            },
            {
                "data": "commissionLoan",
            },
            {
                "data": "commissionDailyCollection",
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
                        `<div style="display: flex;justify-content: space-around; align-items: center;"><a href="javascript:void(0);" onclick="editCommissionSetup('` + full.id + `','` +
                        full.startDate + `','` + full.endDate + `','` + full.commissionSaving + `','` + full
                        .commissionFD + `','` + full.commissionRD + `','` + full.commissionShare + `','` +
                        full.commissionLoan + `','` + full.commissionDailyCollection + `','` + full.status +
                        `')"><i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i></a>`;
                    menu += `<a onclick="deleteItem('` + full.id +
                        `', 'deleteCommission')" href="javascript:void(0);" ><i class="fa-solid fa-trash iconsColorCustom"></i></a > </div>`;
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

        $("#commission").validate({
            rules: {
                startDate: {
                    required: true,
                    date: true
                },
                endDate: {
                    required: true,
                    date: true
                },
                commissionSaving: {
                    required: true,
                    number: true,
                },
                commissionShare: {
                    required: true,
                    number: true,
                },
                commissionLoan: {
                    required: true,
                    number: true,
                },
                commissionDailyCollection: {
                    required: true,
                    number: true,
                },
                commissionFD: {
                    required: true,
                    number: true,
                },
                commissionRD: {
                    required: true,
                    number: true,
                },

                status: {
                    required: true,
                }
            },
            messages: {
                startDate: {
                    required: "Please enter value",
                },
                endDate: {
                    required: "Please enter value",
                },
                commissionSaving: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                commissionShare: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                commissionLoan: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                commissionDailyCollection: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                commissionFD: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                commissionRD: {
                    required: "Please enter value",
                    number: "Value should be numeric",
                },
                status: {
                    required: "Please enter value"
                }
            },
            erroeElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select21"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('#commission');
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
        });
    });

    function editCommissionSetup(id, startDate, endDate, commissionSaving, commissionFD, commissionRD, commissionShare,
        commissionLoan, commissionDailyCollection, status) {
        $('#basicModal').find('.msg').text("Edit Commission Master");
        $('#basicModal').find('input[name="id"]').val(id);
        $('#basicModal').find('input[name="startDate"]').val(startDate);
        $('#basicModal').find('input[name="endDate"]').val(endDate);
        $('#basicModal').find('input[name="commissionSaving"]').val(commissionSaving);
        $('#basicModal').find('input[name="commissionFD"]').val(commissionFD);
        $('#basicModal').find('input[name="commissionRD"]').val(commissionRD);
        $('#basicModal').find('input[name="commissionShare"]').val(commissionShare);
        $('#basicModal').find('input[name="commissionLoan"]').val(commissionLoan);
        $('#basicModal').find('input[name="commissionDailyCollection"]').val(commissionDailyCollection);
        var statusSelect = $('#basicModal').find('select[name="status"]');
        statusSelect.val(status).trigger('change');
        $('#basicModal').modal('show');
    }

    function addSetup() {
        $('#commission')[0].reset();
        $('#basicModal').find('.msg').text("Add");
        $('#basicModal').find('input[name="id"]').val("new");
        $('#basicModal').modal('show');
    }


    function deleteItem(id, actiontype) {
        $.ajax({
            url: '{{ route("delete", ["actiontype" => ":actiontype"]) }}'.replace(':actiontype', actiontype),
            type: 'post', // Use the HTTP DELETE method
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
