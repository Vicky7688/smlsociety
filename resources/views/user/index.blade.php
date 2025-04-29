@extends('layouts.app')
@section('title', ucwords($type))
@section('pagetitle',ucwords($type))

@php
$table = "yes";
$export = "wallet";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Dashboard / User /</span> {{ucwords($type)}}
            </h4>
            <div class="card card-action mb-5">
                <div class="card-header">
                    <div class="card-action-title">{{ucwords($type)}}</div>
                    <div class="card-action-element">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                                    Add {{ucwords($type)}}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-datatable table-responsive">
                        <table class="table datatables-order table border-top" id="datatable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th class="w-17">#</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Status</th>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Add {{ucwords($type)}} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userManager" action="{{route('userStore')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="type" value="{{$type}}" />
                <input type="hidden" name="role_id" value="{{$role->id}}" />
                <div class="modal-body">

                    <div class="col mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" />
                    </div>
                    <div class="col mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" />
                    </div>
                    <div class="col mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile" />
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
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {

        var url = "{{url('statement/fetch')}}/{{$type}}/{{$id}}";
        var onDraw = function() {

        };
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<div><span class='text-inverse m-l-10'><b>` +
                        full.id +
                        `</b> </span><div class="clearfix"></div></div><span style='font-size:13px' class="pull=right">` +
                        full.created_at + `</span>`;
                }
            },
            {
                "data": "name"
            },
            {
                "data": "mobile"
            },
            {
                "data": "email"
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == "active") {
                        var out = `<span class="badge bg-label-success">Active</span><br/>`;
                    } else if (full.status == "inactive") {
                        var out = `<span class="badge bg-label-danger">Inactive</span><br/>`;
                    } else {
                        var out = `<span class="badge badge-danger">` + full.status + `</span><br/>`;
                    }
                    return out;
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

        $("#userManager").validate({
            rules: {
                name: {
                    required: true,
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    number: true,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                mobile: {
                    required: "Please enter value",
                    number: "Mobile number should be numeric",
                    minlength: "Your mobile number must be 10 digit",
                    maxlength: "Your mobile number must be 10 digit"
                },
                email: {
                    required: "Please enter value",
                    email: "Please enter valid email address",
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
                var form = $('#userManager');
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
                            $('#userModal').modal('hide');
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
</script>
@endpush