@extends('layouts.app')
@section('title', " Group ")
@section('pagetitle', "Group ")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Accounting / </span> Create Ledger</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Ledger Master </h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    <span class="msg">Add</span> Ledger
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
                                        <th>Group</th>
                                        <th>Ledger Code</th>
                                        <th>Name</th>
                                        <th>Opening </th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($ledgers))
                                        @foreach($ledgers as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ ucwords($row->groupCode) }}</td>
                                                <td>{{ ucwords($row->ledgerCode) }}</td>
                                                <td>{{ ucwords($row->name) }}</td>
                                                <td>{{ $row->openingAmount }}</td>
                                                <td>{{ $row->openingType }}</td>
                                                <td>{{ $row->status }}</td>
                                                <td>
                                                    @php
                                                        $exit_ledger_code = DB::table('general_ledgers')->where('ledgerCode', $row->ledgerCode)->first();
                                                    @endphp
                                                    @if(!$exit_ledger_code)
                                                        <a href="javascript:void(0);"
                                                        class="edit-ledger-master"
                                                        data-id="{{ $row->id }}"
                                                        data-group-code="{{ $row->groupCode }}"
                                                        data-ledger-code="{{ $row->ledgerCode }}"
                                                        data-name="{{ $row->name }}"
                                                        data-opening-amount="{{ $row->openingAmount }}"
                                                        data-status="{{ $row->status }}"
                                                        data-opening-type="{{ $row->openingType }}">
                                                            <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="deleteledger" data-id="{{ $row->id }}">
                                                            <i class='fa-solid fa-trash iconsColorCustom'></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Ledger </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ledgerMaster" name="ledgerMaster">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">

                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Group</label>
                            <select class="form-select formInputsSelectReport" id="groupCode" name="groupCode">
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                <option value="{{$group->groupCode}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Name</label>
                            <input type="text" id="name" oninput="generateledgercode('this')" name="name" class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="ledgerCode" class="form-label">Ledger Code</label>
                            <input type="text" readonly name="ledgerCode" id="ledgerCode" class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="openingAmount" class="form-label">Opening Amount</label>
                            <input type="text" id="openingAmount" name="openingAmount" class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">Opnening Tyoe</label>
                            <select name="openingType" id="openingType" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="">Select</option>
                                <option value="Cr">Cr</option>
                                <option value="Dr">Dr</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">Status </label>
                            <select name="status" id="status" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                    </div>

                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
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
<script>
    //____________Open Modal
    function addSetup() {
        $('#ledgerMaster')[0].reset();
        $('#basicModal').find('.msg').text("Add");
        $('#basicModal').modal('show');
    }

    //__________Generate New Ledger Code
    function generateledgercode(){
        let ledger_name = $('#name').val();

        $.ajax({
            url : "{{ route('generateledgercode') }}",
            type : 'post',
            data : { ledger_name : ledger_name},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){
                    $('#ledgerCode').val(res.newgroup_code);
                }else{
                    {{--  alert(res.messages);  --}}
                }
            }
        });
    }


    $(document).ready(function(){
        //___________Form Validations
        $("#ledgerMaster").validate({
            rules: {
                groupCode : {
                    required : true
                },
                name: {
                    required: true,
                },
                status: {
                    required: true,
                },
                openingType: {
                    required: true,
                },

            },
            messages: {
                groupCode : {
                    required : 'Select Group'
                },
                name: {
                    required: "Please Enter Ledger Name",
                },
                status: {
                    required: "Please Select Status",
                },
                openingType: {
                    required: "Please Select Ledger Type",
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
        });

        //____________Ledger Insert
        $(document).on('submit', '#ledgerMaster', function(e) {
            e.preventDefault();

            if ($(this).valid()) {
                let formData = $(this).serializeArray();
                let url = $('#id').val() ? "{{ route('updateledger') }}" : "{{ route('ledgerInsert') }}";
                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#ledgerMaster')[0].reset();
                            $('#basicModal').modal('hide');
                            window.location.href = '{{ route('ledgerindex') }}';
                            toastr.success(res.messages);
                        } else {
                            {{--  alert(res.messages);  --}}
                        }
                    },
                    error: function(xhr, status, error) {
                        {{--  alert('An error occurred: ' + error);  --}}
                    }
                });
            }
        });

         //__________Edit Ledger
         $(document).on('click', '.edit-ledger-master', function() {
            let ledgerId = $(this).data('id');
            let groupCode = $(this).data('group-code');
            let ledgerCode = $(this).data('ledger-code');
            let name = $(this).data('name');
            let openingAmount = $(this).data('opening-amount');
            let status = $(this).data('status');
            let openingType = $(this).data('opening-type');
            editLedgerMasterSetup(ledgerId, groupCode, ledgerCode, name, openingAmount, status, openingType);
        });


        //_______________Delete Ledger
        $(document).on('click', '.deleteledger', function() {
            let ledgerId = $(this).data('id');
            $.ajax({
                url : "{{ route('deleteledger') }}",
                type : 'post',
                data : {ledgerId : ledgerId},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        $('#datatable').load(location.href+' .table');
                        toastr.success(res.messages);
                    }else{
                        {{--  alert(res.messages);  --}}
                    }
                }
            });
        });
    });

    //_______________Delete Ledger
    function editLedgerMasterSetup(ledgerId, groupCode, ledgerCode, name, openingAmount, status, openingType){
        $('#id').val(ledgerId);
        $('#groupCode').val(groupCode);
        $('#ledgerCode').val(ledgerCode);
        $('#name').val(name);
        $('#openingAmount').val(openingAmount);
        $('#status').val(status);
        $('#openingType').val(openingType);
        $('#basicModal').modal('show');
    }


</script>
<script type="text/javascript">
    {{--  $(document).ready(function() {

        var url = "{{url('statement/fetch')}}/ledgermaster";
        var onDraw = function() {

        };
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": "groupCode"
            },
            {
                "data": "ledgerCode"
            },
            {
                "data": "name"
            },
            {
                "data": "openingAmount"
            },
            {
                "data": "openingType"
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
                        `<div style="display: flex;justify-content: space-around; align-items: center;"> <a href="javascript:void(0);" onclick="editLedgerSetup('` + full.id + `','` + full
                        .groupCode + `','` + full.name + `','` + full.openingAmount + `','` + full
                        .openingType + `','` + full.status +
                        `','` + full.ledgerCode +
                        `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                    menu += `<a onclick="deleteItem('` + full.id +
                        `', 'deleteLedger')" href="javascript:void(0);" ><i class='fa-solid fa-trash iconsColorCustom'></i></a > </div>`;
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
                },
                openingType: {
                    required: true,
                },
                openingAmount: {
                    required: true,
                    number: true,
                },
                type: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                status: {
                    required: "Please enter value",
                },
                openingAmount: {
                    required: "Please enter value",
                    number: "Amount should be numeric",
                },
                type: {
                    required: "Please enter value",
                },
                openingType: {
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
                var form = $('#groupMaster');
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
                    },
                    complete: function() {
                        form.find('button[type="submit"]').html('Submit').attr(
                            'disabled', false).removeClass('btn-secondary');
                    }
                });
            }
        });
    });

    function editLedgerSetup(id, groupId, name, openingAmount, openingType, status, ledgerCode) {
        // Assuming '#basicModal' is your modal ID

        $('#basicModal').find('.msg').text("Edit");
        $('#basicModal').find('input[name="id"]').val(id);
        // $('#basicModal').find('[name="groupId"]').select21().val(groupId).trigger('change');
        $('#basicModal').find('input[name="name"]').val(name);
        $('#basicModal').find('select[name="groupCode"]').val(groupId).trigger('change');
        $('#basicModal').find('[name="ledgerCode"]').val(ledgerCode);
        $('#basicModal').find('input[name="openingAmount"]').val(openingAmount);
        $('#basicModal').find('select[name="openingType"]').val(openingType).trigger('change');
        var statusSelect = $('#basicModal').find('select[name="status"]');
        statusSelect.val(status).trigger('change');
        $('#basicModal').modal('show');
    }

    function addSetup() {
        $('#groupMaster')[0].reset();
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
    }  --}}
</script>
@endpush
