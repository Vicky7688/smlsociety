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
                <h4 class="py-2"><span class="text-muted fw-light">Masters / </span> Session</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Session</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom"  onclick="addSetup()">
                                    Add Session
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
                                        <th>Session Start Date</th>
                                        <th>Session End Date</th>
                                        <th>Audit Perform</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($sessions))
                                        @foreach ($sessions as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ date('d-m-Y',strtotime($row->startDate)) }}</td>
                                                <td>{{ date('d-m-Y',strtotime($row->endDate)) }}</td>
                                                <td>{{ $row->auditPerformed }}</td>
                                                <td>{{ $row->status }}</td>
                                                <td style="width:85px;">
                                                    <button class="btn editbtn"
                                                        data-id="{{ $row->id }}">
                                                        <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                    </button>
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Session </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sessionMaster" name="sessionMaster">
                <div class="modal-body">
                    <input type="text" hidden name="id" id="id">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="flatpickr-date" class="form-label">Session Start Date</label>
                            <input type="date" name="startDate" id="startDate" value="{{ date('Y') }}-04-01"
                                class="form-control formInputsReport">
                        </div>
                    <!-- </div>

                    <div class="row g-2"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="flatpickr-date" class="form-label">End Date</label>
                            <input type="date" name="endDate" id="endDate" readOnly
                                value="{{ date('Y', strtotime('+1 year')) }}-03-31" class="form-control formInputsReport" >
                        </div>
                    <!-- </div> -->

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport form-group">
                            <label for="select2Basic" class="form-label">Status</label>
                            <select id="status" name="status" class="select21 form-select formInputsSelectReport" data-allow-clear="true">
                                <option value="Active" default selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport form-group">
                            <label for="select2Basic" class="form-label">Sort By</label>
                            <input type="text" name="sortby" id="sortby"  class="form-control formInputsReport">
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport form-group">
                            <label class="form-label mb-1" for="status-org">Audit Perform</label>
                            <select name="auditPerformed" id="auditPerformed" class="select21 form-select formInputsSelectReport">
                                <option value="No" default>No</option>
                                <option value="Yes" >Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">Close</button>
                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
                        data-loading-text="<span class='spinner-border me-1' role='status' aria-hidden='true'></span> Loading...">
                        Submit
                    </button>
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
    padding: 8px;
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

.dataTable {
    border: 1px solid #ddd !important;
}
</style>
@endpush

@push('script')
<script type="text/javascript">
    function addSetup() {
        $('#basicModal').modal('show');
    }

$(document).ready(function() {

    var currentYear = new Date().getFullYear();

    // Set start date to April 1st of the current year
    $("#startDate").val(currentYear + "-04-01");

    // Set end date to March 31st of the next year
    var nextYear = currentYear + 1;
    $("#endDate").val(nextYear + "-03-31");

    // Update end date year when start date year changes
    $("#startDate").change(function () {
        var selectedStartDate = new Date($(this).val());
        var selectedStartYear = selectedStartDate.getFullYear();
        var nextYear = selectedStartYear + 1;
        $("#endDate").val(nextYear + "-03-31");
    });


    $("#sessionMaster").validate({
        rules: {
            startDate: {
                required: true,
            },
            endDate: {
                required: true,
            },
            auditPerformed: {
                required: true,
            },
            status: {
                required: true,
            },
            sortby : {
                required : true,
                digits : true
            }
        },
        messages: {
            startDate: {
                required: "Please enter value",
            },
            endDate: {
                required: "Please enter value",
            },
            auditPerformed: {
                required: "Please enter value",
            },
            status: {
                required: "Please enter value",
            },
            sortby : {
                required: "Please enter value",
                digits : 'Entry Only Numeric Value'
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

    $(document).on('submit','#sessionMaster',function(event){
        event.preventDefault();
        if($(this).valid()){
            let formData = $(this).serialize();

            let url = $('#id').val() ? '{{ route('sessionupdate') }}' : '{{ route('sessioninsert') }}';

            $.ajax({
                url : url,
                type : 'post',
                data : formData,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        notify(res.messages,'success');
                        $('#sessionMaster')[0].reset();
                        $('#basicModal').modal('hide');
                        $('#datatable').load(location.href+' .table');
                    }else{
                        notify(res.messages,'warning');
                    }
                }
            });
        }
    });

    $(document).on('click','.editbtn',function(event){
        event.preventDefault();

        let id = $(this).data('id');
        $.ajax({
            url : '{{ route('sessionedit') }}',
            type : 'post',
            data : {id : id},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){
                    let session = res.session;

                    $('#id').val(session.id);
                    $('#startDate').val(session.startDate);
                    $('#endDate').val(session.endDate);
                    $('#status').val(session.status);
                    $('#sortby').val(session.sortno);
                    $('#auditPerformed').val(session.auditPerformed);
                    $('#basicModal').modal('show');

                }else{
                    notify(res.messages,'warning');
                }
            }
        });

    });

    $(document).on('click','.deletebtn',function(event){
        event.preventDefault();

        let id = $(this).data('id');
        $.ajax({
            url : '{{ route('deletesession') }}',
            type : 'post',
            data : {id : id},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){

                }else{
                    notify(res.messages,'warning');
                }
            }
        });

    });







    {{--  var url = "{{ url('statement/fetch') }}/sessionmaster";
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
            "data": "auditPerformed"
        },
        {
            "data": "status",
            render: function(data, type, full, meta) {
                if (full.status == "Active") {
                    var out = `<span class="badge bg-label-success">Active</span><br />`;
                } else if (full.status == "Inactive") {
                    var out = `<span class="badge bg-label-danger">Inactive</span><br />`;
                } else {
                    var out = `<span class="badge badge-danger">` + full.status +
                        `</span><br />`;
                }
                return out;
            }
        },
        {
            "data": "action",
            render: function(data, type, full, meta) {
                var menu =
                    `<div style="display: flex;justify-content: space-around; align-items: center;"> <a href="javascript:void(0);" onclick="editSessionSetup('` + full.id +
                    `','` + full.startDate + `','` + full.endDate + `','` + full.status +
                    `','` + full.auditPerformed + `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                menu += `<a onclick="deleteItem('` + full.id +
                    `', 'deleteSession')" href="javascript:void(0);"><i class='fa-solid fa-trash iconsColorCustom'></i></a> </div>`;

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

    $("#sessionMaster").validate({
        rules: {
            startDate: {
                required: true,
            },
            endDate: {
                required: true,
            },
            auditPerformed: {
                required: true,
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
            auditPerformed: {
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
            var form = $('#sessionMaster');
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
    });  --}}
});

{{--  function editSessionSetup(id, startDate, endDate, status, auditPerformed) {
    $('#basicModal').find('.msg').text("Edit Session");
    $('#basicModal').find('input[name="id"]').val(id);
    $('#basicModal').find('input[name="startDate"]').val(startDate);
    $('#basicModal').find('input[name="endDate"]').val(endDate);
    var statusSelect = $('#basicModal').find('select[name="status"]');
    statusSelect.val(status).trigger('change');
    // $('#basicModal').find('[name="auditPerformed"]').select21().val(auditPerformed).trigger('change');
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
@endpush
