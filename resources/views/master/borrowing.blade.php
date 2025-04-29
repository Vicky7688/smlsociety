@extends('layouts.app')
@section('title', " Session")
@section('pagetitle', "Session")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                <h4 class="py-2"><span class="text-muted fw-light">Masters / </span> Borrowing Limit</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Borrowing Limit</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Borrowing Limit
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
                                        <th>Multiply Value</th>
                                        <th>Percentage Value</th>
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
                <h5 class="modal-title" id="exampleModalLabel1"> Add Borrowing </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="borrowing" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="borrowing" />
                <input type="hidden" name="id" value="new" />

                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Multiply Value</label>
                            <input type="text" name="multiplyValue" class="form-control formInputsReport" placeholder="Enter value"
                                required />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Percentage Value</label>
                            <input type="text" name="percentageValue" class="form-control formInputsReport" placeholder="Enter value"
                                required />
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">Status </label>
                            <select name="status" id="status-org" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="Active" selected default>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
                        data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
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


.tablee table th,
.tablee table td {
    padding: 8px !important;
    text-align: center !important;
}

.dataTable {
    border: 1px solid #ddd !important;
}

.table_head tr {
    background-color: #7367f0;
}

.table_head tr th {
    color: #fff !important;
}

.page_headings h4 {
    margin-bottom: 0;
}
</style>
@endpush

@push('script')
<script type="text/javascript">
$(document).ready(function() {

    var url = "{{url('statement/fetch')}}/borrowing";
    var onDraw = function() {

    };
    var options = [{
            "data": "name",
            render: function(data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            "data": "multiplyValue"
        },
        {
            "data": "percentageValue"
        },
        {
            "data": "status",
            render: function(data, type, full, meta) {
                if (full.status == "Active") {
                    var out = `<span class="badge bg-label-success">Active</span><br/>`;
                } else if (full.status == "Inactive") {
                    var out = `<span class="badge bg-label-danger">Inactive</span><br/>`;
                } else {
                    var out = `<span class="badge badge-danger">` + full.status + `</span><br/>`;
                }
                return out;
            }
        },
        {
            "data": "action",
            render: function(data, type, full, meta) {
                var menu =
                    `<div style="display: flex;justify-content: space-evenly; align-items: center;"><a href="javascript:void(0);" onclick="editBorrowingSetup('` + full.id + `','` +
                    full.multiplyValue + `','` + full.percentageValue + `','` + full.status +
                    `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                menu +=
                    `<a onclick="deleteItem('${full.id}', 'deleteBorrowing')" href="javascript:void(0);"><i class='fa-solid fa-trash iconsColorCustom'></i></a> </div>`;
                return menu;
            }
        },
    ];

    datatableSetup(url, options, onDraw, '#datatable', {
        columnDefs: [{
            orderable: false,
            width: '80px',
            targets: [0]
        }]
    });

    $("#borrowing").validate({
        rules: {
            percentageValue: {
                required: true,
            },
            multiplyValue: {
                required: true,
            },
            status: {
                required: true,
            }
        },
        messages: {
            percentageValue: {
                required: "Please enter value",
            },
            multiplyValue: {
                required: "Please enter value",
            },
            status: {
                required: "Please enter value",
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
            var form = $('#borrowing');
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

                        notify("Task Successfully Completed", 'success');
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

function editBorrowingSetup(id, multiplyValue, percentageValue, status) {
    $('#basicModal').find('.msg').text("Edit Borrowing");
    $('#basicModal').find('input[name="id"]').val(id);
    $('#basicModal').find('input[name="multiplyValue"]').val(multiplyValue);
    $('#basicModal').find('input[name="percentageValue"]').val(percentageValue);
    var statusSelect = $('#basicModal').find('select[name="status"]');
    statusSelect.val(status).trigger('change');
    $('#basicModal').modal('show');
}

function addSetup() {
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