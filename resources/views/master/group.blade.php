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
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Accounting / </span> Create Group</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Create Group </h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Group
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
                                        <th>Group Name</th>
                                        <th>Group Code</th>
                                        <th>Group Type</th>
                                        <th>Show In Journal Voucher</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($groups))
                                        @foreach($groups as $row)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ ucwords($row->name) }}</td>
                                            <td>{{ ucwords($row->groupCode) }}</td>
                                            <td>{{ $row->type }}</td>
                                            <td>{{ $row->showJournalVoucher }}</td>
                                            <td>{{ $row->status }}</td>
                                            <td>
                                                @php
                                                    $exit_group_code = DB::table('ledger_masters')->where('groupCode', $row->groupCode)->first();
                                                @endphp
                                                @if(!$exit_group_code)
                                                    <a href="javascript:void(0);"
                                                       class="edit-group-master"
                                                       data-id="{{ $row->id }}"
                                                       data-name="{{ $row->name }}"
                                                       data-head-name="{{ $row->headName }}"
                                                       data-type="{{ $row->type }}"
                                                       data-show-journal-voucher="{{ $row->showJournalVoucher }}"
                                                       data-status="{{ $row->status }}"
                                                       data-group-code="{{ $row->groupCode }}">
                                                        <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="deletegroup" data-id="{{ $row->id }}">
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Group </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{--  <form id="groupMaster" action="{{ route('masterupdate') }}" method="post">  --}}
                {{--  {{ csrf_field() }}  --}}
                {{--  <input type="hidden" name="actiontype" value="group" />  --}}

            <form name="groupMaster" id="groupMaster">
                <input type="text" hidden name="id" id="id" />
                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Group Name</label>
                            <input type="text" style="text-transform :capitalize;" name="name" id="name" oninput="generategroupcode('this')" class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="groupCode" class="form-label">Group Code</label>
                            <input type="text" readonly name="groupCode" id="groupCode"  class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>

                        <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Group Type</label>
                            <select name="type" id="type" class="select21 form-select formInputsSelectReport" data-placeholder="Type">
                                <option value="">Select Group</option>
                                <option value="Liability">Liability</option>
                                <option value="Asset">Asset</option>
                                <option value="Expenditure">Expenditure</option>
                                <option value="Income">Income</option>
                                <option value="Trading">Trading</option>
                                <option value="Profit and Loss">Profit and Loss</option>
                            </select>
                        </div>
                        {{--  <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ">
                            <label class="form-label mb-1" for="status-org">Show in Journal Voucher</label>
                            <select name="showJournalVoucher" id="status" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="NO">No</option>
                            </select>
                        </div>  --}}

                        <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label class="form-label mb-1" for="status-org">Status </label>
                            <select name="status" id="status-org" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
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
<script type="text/javascript">
    {{--  $(document).ready(function() {

        var url = "{{ url('statement/fetch') }}/groupmaster";
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
                "data": "groupCode"
            },
            {
                "data": "type"
            },
            {
                "data": "showJournalVoucher"
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
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
                render: function(data, type, full, meta) {
                    var menu =
                        `<div style="display: flex;justify-content: space-around; align-items: center;"><a href="javascript:void(0);" onclick="editGroupMasterSetup('` + full.id +
                        `','` + full.name + `','` + full.headName + `','` + full.type + `','` + full
                        .showJournalVoucher + `','` + full.status +
                        `','` + full.groupCode +
                        `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                    menu += `<a onclick="deleteItem('` + full.id +
                        `', 'deleteGroup')" href="javascript:void(0);" ><i class='fa-solid fa-trash iconsColorCustom'></i></a > </div>`;
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
                showJournalVoucher: {
                    required: true,
                },
                type: {
                    required: true,
                },

            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                status: {
                    required: "Please enter value",
                },
                showJournalVoucher: {
                    required: "Please enter value",
                },
                type: {
                    required: "Please enter value",
                },

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

    function editGroupMasterSetup(id, name, headName, type, showJournalVoucher, status, groupCode) {
        $('#basicModal').find('.msg').text("Edit Group");
        $('#basicModal').find('input[name="id"]').val(id);
        $('#basicModal').find('input[name="name"]').val(name);
        $('#basicModal').find('input[name="groupCode"]').val(groupCode);
        $('#basicModal').find('select[name="type"]').val(type).trigger('change');
        $('#basicModal').find('select[name="showJournalVoucher"]').val(showJournalVoucher).trigger('change');
        var statusSelect = $('#basicModal').find('select[name="status"]');
        statusSelect.val(status).trigger('change');
        $('#basicModal').modal('show');
    }  --}}


    {{--  function deleteItem(id, actiontype) {
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


//___________New Code
<script>

    //____________Open Modal
    function addSetup() {
        $('#groupMaster')[0].reset();
        $('#basicModal').find('.msg').text("Add");
        {{--  $('#basicModal').find('input[name="id"]').val("new");  --}}
        $('#basicModal').modal('show');
    }

    //__________Generate New Group Code
    function generategroupcode(){
        let group_name = $('#name').val();

        $.ajax({
            url : "{{ route('generategroupcode') }}",
            type : 'post',
            data : { group_name : group_name},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){
                    $('#groupCode').val(res.newgroup_code);
                }else{
                    alert(res.messages);
                }
            }
        });
    }

    $(document).ready(function(){
        //___________Form Validations
        $("#groupMaster").validate({
            rules: {
                name: {
                    required: true,
                },
                status: {
                    required: true,
                },
                showJournalVoucher: {
                    required: true,
                },
                type: {
                    required: true,
                },

            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                status: {
                    required: "Please enter value",
                },
                showJournalVoucher: {
                    required: "Please enter value",
                },
                type: {
                    required: "Please enter value",
                },

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

        //____________Group Insert
        $(document).on('submit', '#groupMaster', function(e) {
            e.preventDefault();

            if ($(this).valid()) {
                let formData = $(this).serializeArray();
                let url = $('#id').val() ? "{{ route('updategroup') }}" : "{{ route('groupInsert') }}";
                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#groupMaster')[0].reset();
                            $('#basicModal').modal('hide');
                            window.location.href = '{{ route('groupindex') }}';
                            toastr.success(res.messages);
                        } else {
                            alert(res.messages);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            }
        });

        //__________Edit Group
        $(document).on('click', '.edit-group-master', function() {
            let groupId = $(this).data('id');
            let groupName = $(this).data('name');
            let headName = $(this).data('head-name');
            let type = $(this).data('type');
            let showJournalVoucher = $(this).data('show-journal-voucher');
            let status = $(this).data('status');
            let groupCode = $(this).data('group-code');
            editGroupMasterSetup(groupId, groupName, headName, type, showJournalVoucher, status, groupCode);
        });

        //__________Edit Group
        function editGroupMasterSetup(groupId, groupName, headName, type, showJournalVoucher, status, groupCode){
            $('#id').val(groupId);
            $('#name').val(groupName);
            $('#groupCode').val(groupCode);
            $('#type').val(type);
            $('#status').val(status);
            $('#basicModal').modal('show');
        }

        //_______________Delete Group
        $(document).on('click', '.deletegroup', function() {
            let groupId = $(this).data('id');
            $.ajax({
                url : "{{ route('deletegroup') }}",
                type : 'post',
                data : {groupId : groupId},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        $('#datatable').load(location.href+' .table');
                        toastr.success(res.messages);
                    }else{
                        alert(res.messages);
                    }
                }
            });
        });
    });
</script>
@endpush
