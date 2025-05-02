@extends('layouts.app')
@section('title', " Group Type")
@section('pagetitle', "Group Type")

@php
    $table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12">
            <div class="card page_headings mb-4">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Dashboard /</span> Loan Type Master</h4>
                </div>
            </div>
            <div class="card card-action mb-5">
                <div class="card-header">
                    <div class="card-action-title">Loan Type Master</div>
                    <div class="card-action-element">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#basicModal">
                                    Add Loan Type
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
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @if(!empty($loanType))
                                @foreach($loanType as $row)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ ucwords($row->name) }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            @endif --}}
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Loan Type </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="groupMaster" action="{{ route('masterupdate') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="loantypemaster" />
                <input type="hidden" name="id" value="new" />
                <div class="modal-body">

                    <div class="row">
                        <div class="col mb-3">
                            <label for="statename" class="form-label">Loan type</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter value" required />
                        </div>
                    </div>

                    <div class="mb-3 col ecommerce-select2-dropdown">
                        <label class="form-label mb-1" for="status-org">Status </label>
                        <select name="status" id="status-org" class="select21 form-select" data-placeholder="Active">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
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
        $(document).ready(function () {

            var url = "{{ url('statement/fetch') }}/loanTypeMaster";
            var onDraw = function () {

            };
            var options = [{
                    "data": "id",
                    render: function (data, type, full, meta) {
                        return `<div><span class='text-inverse m-l-10'><b>` +
                            parseInt(meta.row+1) + `</span>`;
                            // `</b> </span><div class="clearfix"></div></div><span style='font-size:13px' class="pull=right">` + full.created_at +
                    }
                },
                {
                    "data": "name",
                      render: function (data, type, full, meta) {

                          return full.name.toUpperCase();
                      }

                },
                {
                    "data": "status",
                    render: function (data, type, full, meta) {
                        if (full.status == "Active") {
                            var out = `<span class="badge bg-label-success">Active</span><br/>`;
                        } else if (full.status == "Inactive") {
                            var out = `<span class="badge bg-label-danger">Inactive</span><br/>`;
                        } else {
                            var out = `<span class="badge badge-danger">` + full.status +
                            `</span><br/>`;
                        }
                        return out;
                    }
                },
                {
                    "data": "action",
                    render: function (data, type, full, meta) {
                        var menu =
                            `<a href="javascript:void(0);" onclick="editGroupTypeMasterSetup('` + full
                            .id + `','` + full.name +
                            `','` + full.status +
                            `')"><i class="ti ti-pencil me-1"></i></a>`;
                        menu += `<a onclick="deleteItem('` + full.id +
                            `' ,'deleteLoanType')" href="javascript:void(0);" ><i class="ti ti-trash me-1"></i></a >`;
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

            $("#groupMaster").validate({
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
                errorPlacement: function (error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select21"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function () {
                    var form = $('#groupMaster');
                    var id = form.find('[name="id"]').val();
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function () {
                            form.find('button[type="submit"]').html(
                                '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                            ).attr(
                                'disabled', true).addClass('btn-secondary');
                        },
                        success: function (data) {
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
                        error: function (errors) {
                            showError(errors, form);
                        }
                    });
                }
            });
        });

        function editGroupTypeMasterSetup(id, name, status) {
            $('#basicModal').find('.msg').text("Edit Group Type");
            $('#basicModal').find('input[name="id"]').val(id);
            $('#basicModal').find('input[name="name"]').val(name);
            $('#basicModal').find('select[name="status"]').val(status).trigger('change');
            $('#basicModal').modal('show');
        }

        function deleteItem(id, actiontype) {
            $.ajax({
                url: '{{ route("delete", ["actiontype" => ":actiontype"]) }}'
                    .replace(':actiontype', actiontype),
                type: 'post', // Use the HTTP DELETE method
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "id": id,
                    "actiontype": actiontype
                },
                beforeSend: function () {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting data',
                        onOpen: () => {
                            swal.showLoading();
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                success: function (data) {
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
                            text: data.status,
                            showConfirmButton: true,
                        });
                    }
                },
                error: function () {
                    swal.close();
                    notify('Something went wrong', 'warning');
                },
                complete: function () {}
            });
        }

    </script>
@endpush
