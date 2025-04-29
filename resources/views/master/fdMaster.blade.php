@extends('layouts.app')
@section('title', " FD Type")
@section('pagetitle', "FD Type")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> FD Master 
            </h4>
            <div class="card card-action mb-5">
                <div class="card-header">
                    <div class="card-action-title">FD Master</div>
                    <div class="card-action-element">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <button type="button" class="btn btn-primary" onclick="addSetup()">
                                    Add FD
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body tablee">
                    <div class="card-datatable table-responsive tabledata">
                        <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                            <thead class="table_head">
                                <tr>
                                    <th class="w-17">#</th>
                                    <th>FD Type</th>
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

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Add FD </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fdMaster" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="fdMaster" />
                <input type="hidden" name="id" value="new" />

                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="name" class="form-label">Loan Type</label>
                            <input type="text" name="name" id="name" class="form-control">
                        <div class="col mb-12">
                            <label for="fdType" class="form-label">FD Type</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter value" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-12 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">Status</label>
                            <select name="status" id="status-org" class="select21 form-select" data-placeholder="Active">
                                <option value="Active" default selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
                        data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
$(document).ready(function() {

    var url = "{{url('statement/fetch')}}/fdMaster";
    var onDraw = function() {

    };
    var options = [{
            "data": "name",
            render: function(data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            "data": "name"
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
                    `<a href="javascript:void(0);" onclick="editFdSetup('` + full.id + `','` + full
                    .name + `','` + full.status + `')"><i class="ti ti-pencil me-1"></i></a>`;
                menu +=
                    `<a onclick="deleteItem('${full.id}', 'deleteFd')" href="javascript:void(0);"><i class="ti ti-trash me-1"></i></a>`;

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

    $("#fdMaster").validate({
        rules: {
            name: {
                required: true,
            },
            status: {
                required: true,
            }
        },
        messages: {
            name: {
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
            var form = $('#fdMaster');
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

function editFdSetup(id, name, status) {
    // Assuming '#basicModal' is your modal ID
    $('#basicModal').find('.msg').text("Edit FD");
    $('#basicModal').find('input[name="id"]').val(id);
    $('#basicModal').find('input[name="name"]').val(name);
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