@extends('layouts.app')
@section('title', 'Branch Master')
@section('pagetitle', 'Branch Master')

@php
    $table = 'yes';
@endphp

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-12">
                <div class="card page_headings cards">
                    <div class="card-body py-2">
                        <h4 class="py-2"><span class="text-muted fw-light">Masters / Loan Module / </span> Branch Master
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        form

        <div class="row">
            <div class="col-12 cards">
                <div class="card">
                    <div class="card-body py-3">
                        <h5 class="card-action-title">Branch Master</h5>
                        <div class="card-action-element">
                            <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                    <button type="button" class="btn btn-primary reportSmallBtnCustom"
                                        onclick="addSetup()">
                                        Add Branch
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tablee">
                            <div class="table-responsive tabledata">
                                <table class="table datatables-order table table-bordered" id="datatable"
                                    style="width:100%">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th class="w-17">S No</th>
                                            {{-- <th>Type</th> --}}
                                            <th>Name</th>
                                            <th>Reg. No</th>
                                            <th>Reg. Date</th>
                                            <th>Address</th>
                                            <th>Pincode</th>
                                            <th>Phone</th>
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
    <div class="modal fade modal-lg" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"> Add Branch </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="branchMaster" action="{{ route('masterupdate') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="actiontype" value="branchMaster" />
                    <input type="hidden" name="id" value="new" />

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-4 col-6 ">
                                <label for="registrationDate" class="form-label mydatepic">Registration Date</label>
                                <input type="text" value="{{ Session::get('currentdate') }}" name="registrationDate"
                                    id="registrationDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-4 col-6 ">
                                <label for="name" class="form-label">Branch Name</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                <label for="registrationNo" class="form-label">Registration No</label>
                                <input type="text" name="registrationNo" id="registrationNo"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                <label for="branch_code" class="form-label">Branch Code</label>
                                <input type="text" name="branch_code" id="branch_code"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                <label for="branch_limit" class="form-label">Branch Limit</label>
                                <input type="text" name="branch_limit" id="branch_limit" value="1"
                                    class="form-control form-control-sm">
                            </div>

                            <!--<div class="col-lg-3 col-md-3 col-sm-4 col-6">-->
                            <!--    <label for="type" class="form-label">Type</label>-->
                            <!--    <select name="type" id="status-org" class="select21 form-select form-select-sm" data-placeholder="type">-->
                            <!--        <option value="" selected disabled>Select</option>-->
                            <!--        <option value="HeadOffice">Head Office</option>-->
                            <!--        <option value="BranchOffice">Branch Office</option>-->
                            <!--    </select>-->
                            <!--</div>-->

                            <!-- </div>
                            <div class="row mb-3"> -->


                            <!-- </div>
                            <div class="row"> -->
                            <div class="col-lg-3 col-sm-4 col-6">
                                <label for="commissionRD" class="form-label">State</label>
                                {{-- <select name="stateId" id="status-org" class="select21 form-select formInputsSelectReport" data-placeholder="State"
                                onchange="getDistrict(this)">
                                <option value="">Select</option>
                                @foreach ($states as $state)
                                <option value="{{$state->id}}">{{$state->name}}</option>
                                @endforeach
                            </select> --}}
                                <input type="text" name="stateId" id="branch_limit" placeholder="State"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-3 col-sm-4 col-6 ">
                                <label for="commissionShare" class="form-label">District</label>
                                {{-- <select name="districtId" id="status-org" onchange="getTehsil(this)"
                                class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="">Select</option>
                            </select> --}}
                                <input type="text" name="districtId" id="districtId" placeholder="District"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-3 col-sm-4 col-6">
                                <label for="tehsilId" class="form-label">Tehsil</label>
                                {{-- <select name="tehsilId" id="status-org" onchange="getPostoffice(this)"
                                class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="">Select</option>
                            </select> --}}
                                <input type="text" name="tehsilId" id="tehsilId" placeholder="Tehsil"
                                    class="form-control form-control-sm">
                            </div>
                            <!-- </div>
                            <div class="row"> -->
                            <div class="col-lg-3 col-sm-4 col-6 ">
                                <label for="postOfficeId" class="form-label">Post Office</label>
                                {{-- <select name="postOfficeId" id="status-org" onchange="getVillage(this)"
                                class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="">Select</option>
                            </select> --}}
                                <input type="text" name="postOfficeId" id="postOfficeId" placeholder="Post Office"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-3 col-sm-4 col-6">
                                <label for="villageId" class="form-label">Village</label>
                                {{-- <select name="villageId" id="status-org" class="select21 form-select formInputsSelectReport"
                                data-placeholder="Active">
                                <option value="">Select</option>
                            </select> --}}
                                <input type="text" name="villageId" id="villageId" placeholder="Village"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-3 col-sm-4 col-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" step="NA" name="phone" id="phone" maxlength="10"
                                    minlength="10" class="form-control form-control-sm">
                            </div>
                            <!--<div class="col-lg-4 col-sm-4 col-6 ">-->
                            <!--    <label for="wardNo" class="form-label">Ward No</label>-->
                            <!--    <input type="text" name="wardNo" id="wardNo" class="form-control form-control-sm">-->
                            <!--</div>-->
                            <!-- </div>
                            <div class="row"> -->
                            <div class="col-lg-3 col-sm-4 col-6">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" maxlength="6" minlength="6" name="pincode" id="pincode"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-9 col-sm-12 col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="address"
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer me-0">
                        <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0"
                            data-bs-dismiss="modal">
                            Close
                        </button>

                        <button id="submitButton"
                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
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
            var url = "{{ url('statement/fetch') }}/branchMaster";
            var onDraw = function() {

            };

            var options = [{
                    "data": "name",
                    render: function(data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                // {
                //     "data": "type",
                // },
                {
                    "data": "name",
                },
                {
                    "data": "registrationNo",
                },
                {
                    "data": "registrationDate",
                    render: function(data, type, full, meta) {
                        return formatDate(full.registrationDate);
                    }
                },
                {
                    "data": "address",
                },
                {
                    "data": "pincode",
                },
                {
                    "data": "phone",
                },
                {
                    "data": "action",
                    render: function(data, type, full, meta) {
                        var menu =
                            `<div style="display: flex; justify-content: space-evenly; align-items: center;">`;

                        menu += `<a onclick="deleteItem('` + full.id + `', 'deleteBranch')" href="javascript:void(0);" >
                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                 </a>`;

                        menu += `</div>`;
                        return menu;
                    }
                }
                //   {
                //     "data": "action",
                //     render: function(data, type, full, meta) {

                //         var menu =
                //             <div style="display: flex;justify-content: space-evenly; align-items: center;"><a href="javascript:void(0);" onclick="editBranchSetup(' +
                //             full.id + ',' + full.name + ',' + full.registrationNo + ',' + full
                //             .registrationDate + ',' + full.stateId + ',' + full.districtId + ',' +
                //             full.tehsilId + ',' + full.villageId + ',' + full.wardNo + ',' + full
                //             .address + ',' + full.pincode + ',' + full.phone +
                //             ')"><i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i></a>;
                //         menu += <a onclick="deleteItem(' + full.id +
                //             ', 'deleteBranch')" href="javascript:void(0);" ><i class="fa-solid fa-trash iconsColorCustom"></i></a > </div>;
                //         return menu;
                //     }
                // }

            ];
            //  + full.type + `','`
            datatableSetup(url, options, onDraw, '#datatable', {
                columnDefs: [{
                    orderable: false,
                    width: '80px',
                    targets: [0]
                }]
            });

            $("#branchMaster").validate({
                rules: {
                    type: {
                        required: true,
                    },
                    name: {
                        required: true,
                    },
                    registrationNo: {
                        required: true,
                    },
                    registrationDate: {
                        required: true,
                    },
                    stateId: {
                        required: true,
                    },
                    districtId: {
                        required: true,
                    },
                    tehsilId: {
                        required: true,
                    },
                    villageId: {
                        required: true,
                    },
                    wardNo: {
                        required: true,
                    },
                    address: {
                        required: true,
                    },
                    pincode: {
                        required: true,
                    },
                    phone: {
                        required: true,
                        minlength: 10,
                    }
                },
                messages: {
                    type: {
                        required: "Please enter value",
                    },
                    name: {
                        required: "Please enter value",
                    },
                    registrationNo: {
                        required: "Please enter value",
                    },
                    registrationDate: {
                        required: "Please enter value",
                    },
                    stateId: {
                        required: "Please enter value",
                    },
                    districtId: {
                        required: "Please enter value",
                    },
                    tehsilId: {
                        required: "Please enter value",
                    },
                    postOfficeId: {
                        required: "Please enter value",
                    },
                    villageId: {
                        required: "Please enter value",
                    },
                    wardNo: {
                        required: "Please enter value",
                    },
                    address: {
                        required: "Please enter value",
                    },
                    pincode: {
                        required: "Please enter value",
                    },
                    phone: {
                        required: "Please enter value",
                        minlength: "Phone number should have 10 digit",

                    },
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
                    var form = $('#branchMaster');
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

        function editBranchSetup(id, type, name, registrationNo, registrationDate, stateId, districtId,
            tehsilId, villageId, wardNo, address, pincode, phone) {
            $('#basicModal').find('.msg').text("Edit Commission Master");
            $('#basicModal').find('input[name="id"]').val(id);
            // var typeSelect = $('#basicModal').find('select[name="type"]');
            // typeSelect.val(type).trigger('change');
            $('#basicModal').find('input[name="name"]').val(name);
            $('#basicModal').find('input[name="registrationNo"]').val(registrationNo);
            $('#basicModal').find('input[name="registrationDate"]').val(registrationDate);
            $('#basicModal').find('input[name="stateId"]').val(stateId);
            $('#basicModal').find('input[name="districtId"]').val(districtId);
            $('#basicModal').find('input[name="tehsilId"]').val(tehsilId);
            $('#basicModal').find('input[name="villageId"]').val(villageId);
            $('#basicModal').find('input[name="wardNo"]').val(wardNo);
            $('#basicModal').find('input[name="address"]').val(address);
            $('#basicModal').find('input[name="pincode"]').val(pincode);
            $('#basicModal').find('input[name="phone"]').val(phone);
            $('#basicModal').modal('show');
        }

        function addSetup() {
            $('#basicModal').find('.msg').text("Add");
            $('#basicModal').find('input[name="id"]').val("new");
            $('#basicModal').modal('show');
        }


        function deleteItem(id, actiontype) {
            $.ajax({
                url: '{{ route('delete', ['actiontype' => ':actiontype']) }}'.replace(':actiontype', actiontype),
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

        function getDistrict(ele) {
            $.ajax({
                url: "{{ route('masterupdate') }}",
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
                    $('[name="districtId"]').html(out);
                }
            });
        }

        function getTehsil(ele) {
            $.ajax({
                url: "{{ route('masterupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {

                },
                data: {
                    'actiontype': "gettehsil",
                    'distId': $(ele).val()
                },
                success: function(data) {
                    swal.close();
                    var out = `<option value="">Select Tehsil</option>`;
                    $.each(data.data, function(index, value) {
                        out += `<option value="` + value.id + `">` + value.name +
                            `</option>`;
                    });
                    $('[name="tehsilId"]').html(out);
                }
            });
        }

        function getPostoffice(ele) {
            $.ajax({
                url: "{{ route('masterupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {

                },
                data: {
                    'actiontype': "getpostoffice",
                    'tehsilId': $(ele).val()
                },
                success: function(data) {
                    swal.close();
                    var out = `<option value="">Select Post office</option>`;
                    $.each(data.data, function(index, value) {
                        out += `<option value="` + value.id + `">` + value.name +
                            `</option>`;
                    });
                    $('[name="postOfficeId"]').html(out);
                }
            });
        }

        function getVillage(ele) {
            $.ajax({
                url: "{{ route('masterupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // You can add any pre-request actions here
                },
                data: {
                    'actiontype': "getvillage",
                    'postOfficeId': $(ele).val()
                },
                success: function(data) {
                    // Close any alert or loader here if needed
                    var out = '<option value="">Select Village</option>';
                    $.each(data.data, function(index, value) {
                        out += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $('[name="villageId"]').html(out);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endpush
