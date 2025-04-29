@extends('layouts.app')
@section('title', "Sale Client Master")
@section('pagetitle', "Sale Client Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Inventory Module / </span> Sale Client</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Sale Client Master</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Sale Client
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tablee">
                        <div class="table-responsive tabledata">
                            <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Client Name</th>
                                        <th>City</th>
                                        <th>Phone No</th>
                                        <th>Email</th>
                                        <th>Fax No.</th>
                                        <th>GST No.</th>
                                        <th>Address</th>
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
                <h5 class="modal-title" id="exampleModalLabel1"> Add Sale Client </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saleClientMaster" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="saleClientMaster" />
                <input type="hidden" name="id" value="new" />

                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">Client Name</label>
                            <input type="text" name="name" id="name" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" id="city" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="status-org" class="form-label">State</label>
                            <select name="state" id="status-org" class="select21 form-select formInputsSelectReport" data-placeholder="Active" onchange="getDistrict(this)">
                                <option value="">Select</option>
                                @foreach($states as $state)
                                <option id="{{$state->id}}" value="{{$state->id}}">{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="district" class="form-label">District</label>
                            <select name="district" id="status-org" class="select21 form-select formInputsSelectReport"
                                data-placeholder="Active">
                                <option value="">Select</option>

                            </select>
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" maxlength="10" minlength="10"
                                class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="faxNo" class="form-label">FAX No.</label>
                            <input type="text" name="faxNo" id="faxNo" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="gstNo" class="form-label">GST No.</label>
                            <input type="text" name="gstNo" id="gstNo" class="form-control formInputsReport">
                        </div>
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
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
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

</style>

@endpush

@push('script')
<script type="text/javascript">
$(document).ready(function() {
    var url = "{{url('statement/fetch')}}/saleClientMaster";
    var onDraw = function() {

    };

    var options = [{
            "data": "name",
            render: function(data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            "data": "name",
        },
        {
            "data": "city",
        },
        {
            "data": "address",
        },
        {
            "data": "email",
        },
        {
            "data": "phone",
        },
        {
            "data": "faxNo",
        },
        {
            "data": "gstNo",
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
                    `<div style="display: flex;justify-content: space-evenly; align-items: center;"> <a href="javascript:void(0);" onclick="editSaleClientSetup('` + full.id + `','` +
                    full.name + `','` + full.state + `','` + full.district + `','` + full
                    .city + `','` + full.address + `','` + full.email + `','` +
                    full.phone + `','` + full.faxNo + `','` + full.gstNo + `','` + full.status +
                    `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                menu += `<a onclick="deleteItem('` + full.id +
                    `', 'deleteSaleClient')" href="javascript:void(0);" ><i class='fa-solid fa-trash iconsColorCustom'></i></a > </div>`;
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

    $("#saleClientMaster").validate({
        rules: {
            name: {
                required: true,
            },
            state: {
                required: true,
            },
            district: {
                required: true,
            },
            city: {
                required: true,
            },
            address: {
                required: true,
            },
            email: {
                required: true,
            },
            phone: {
                required: true,
                minlength: 10,
            },
            faxNo: {
                required: true,
            },
            gstNo: {
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
            state: {
                required: "Please enter value",
            },
            district: {
                required: "Please enter value",
            },
            city: {
                required: "Please enter value",
            },
            address: {
                required: "Please enter value",
            },
            email: {
                required: "Please enter value",
            },
            phone: {
                required: "Please enter value",
                minlength: "Phone number should have 10 digit",
            },
            faxNo: {
                required: "Please enter value",
            },
            gstNo: {
                required: "Please enter value",
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
            var form = $('#saleClientMaster');
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

function editSaleClientSetup(id, name, state, district, city, address, email, phone, faxNo, gstNo, status) {
    $('#basicModal').find('.msg').text("Edit Commission Master");
    $('#basicModal').find('input[name="id"]').val(id);
    $('#basicModal').find('input[name="name"]').val(name);
    $('#basicModal').find('input[name="state"]').val(state);
    $('#basicModal').find('input[name="district"]').val(district);
    $('#basicModal').find('input[name="city"]').val(city);
    $('#basicModal').find('input[name="address"]').val(address);
    $('#basicModal').find('input[name="email"]').val(email);
    $('#basicModal').find('input[name="phone"]').val(phone);
    $('#basicModal').find('input[name="faxNo"]').val(faxNo);
    $('#basicModal').find('input[name="gstNo"]').val(gstNo);
    var statusSelect = $('#basicModal').find('select[name="status"]');
    statusSelect.val(status).trigger('change');
    $('#basicModal').modal('show');
}

function addSetup() {
    $('#basicModal').find('.msg').text("Add");
    $('#basicModal').find('input[name="id"]').val("new");
    $('#basicModal').modal('show');
}

function getDistrict(ele) {
    $.ajax({
        url: "{{route('masterupdate')}}",
        type: "POST",
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {

        },
        data: {
            'actiontype': "getdistrict",
            'stateid': $(ele).val()
        },
        success: function(data) {
            swal.close();
            var out = `<option value="">Select District</option>`;
            $.each(data.dist, function(index, value) {
                out += `<option value="` + value.id + `">` + value.name +
                    `</option>`;
            });
            $('[name="district"]').html(out);
        }
    });
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