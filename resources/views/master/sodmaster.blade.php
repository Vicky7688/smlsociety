@extends('layouts.app')
@section('title', " SOD")
@section('pagetitle', "SOD")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                <h4 class="py-2"><span class="text-muted fw-light">Masters / </span> Secured Over Draft(SOD)</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Secured Over Draft(SOD)</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom"  onclick="addSetup('this')">
                                    Add SOD
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
                                        <th>Start Date</th>
                                        <th>Interest Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($sodDetails))
                                        @foreach ($sodDetails as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ date('d-m-Y',strtotime($row->start_date)) }}</td>
                                                <td>{{ $row->interest_type }}</td>
                                                <td>{{ $row->status }}</td>
                                                @php
                                                    $check_sod_advancements = \App\Models\MemberCCL::where('interestType', $row->id)->exists();
                                                @endphp
                                                @if(!empty($check_sod_advancements))
                                                    <td></td>
                                                @else
                                                <td style="width:85px;">
                                                    <button class="btn editbtn"
                                                        data-id="{{ $row->id }}">
                                                        <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                    </button>
                                                    <button class="btn deletebtn"
                                                        data-id="{{ $row->id }}">
                                                        <i class='ti ti-trash me-1 border-0 iconsColorCustom'></i>
                                                    </button>
                                                </td>
                                                @endif
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Secured Over Draft(SOD) </h5>
                {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
            </div>
            <form id="sodmaster" name="sodmaster">
                <div class="modal-body">
                    <input type="text" hidden name="id" id="id">
                    <input type="text" hidden name="actiontype" id="actiontype">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="flatpickr-date" class="form-label">Start Date</label>
                            <input type="text" name="startDate" id="startDate" value="{{ date('d-m-Y') }}"
                                class="form-control formInputsReport">
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport form-group">
                            <label for="select2Basic" class="form-label">Interest Type</label>
                            <select id="interesttype" name="interesttype" class="select21 form-select formInputsSelectReport" data-allow-clear="true">
                                <option value="Daily" default selected>Daily</option>
                                <option value="Monthly">Monthly</option>
                            </select>
                        </div>


                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport form-group">
                            <label for="select2Basic" class="form-label">Status</label>
                            <select id="status" name="status" class="select21 form-select formInputsSelectReport" data-allow-clear="true">
                                <option value="Active" default selected>Active</option>
                                <option value="Inactive">Inactive</option>
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


@push('script')
<script type="text/javascript">
    function addSetup(){
        $('#basicModal').modal('show');
    }

    function dateFormat(date) {
        let dates = new Date(date);
        let daysss = dates.getDate();
        let monthss = dates.getMonth() + 1;
        let yearss = dates.getFullYear();

        daysss = daysss < 10 ? `0${daysss}` : daysss;
        monthss = monthss < 10 ? `0${monthss}` : monthss;
        let formattedDate = `${daysss}-${monthss}-${yearss}`;
        return formattedDate;
    }

    $(document).ready(function(){
        $(document).on('submit', '#sodmaster', function (event) {
            event.preventDefault();

            let startDate = $('#startDate').val();
            let interesttype = $('#interesttype').val();
            let status = $('#status').val();
            let id = $('#id').val(); // Get the id field value

            let url = id ? "{{ route('sodmasterupdate') }}" : "{{ route('sodmasterinsert') }}";
            let actionType = id ? 'update' : 'insert'; // Simplified action type logic

            // Log data to debug
            console.log({startDate, interesttype, status, actionType, id});

            $.ajax({
                url: url,
                type: 'post',
                data: {startDate: startDate, interesttype: interesttype, status: status, actionType: actionType, id: id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function (res) {
                    if (res.status === "success") {
                        notify(res.messages, 'success');
                        $('#basicModal').modal('hide');
                        $('#sodmaster')[0].reset();
                        window.location.href = "{{ route('sodmasterindex') }}";
                    } else {
                        notify(res.messages, 'warning');
                    }
                },
                error: function (xhr, status, error) {
                    notify('Ajax Not Working', 'warning');
                }
            });
        });





        $(document).on('click','.editbtn',function(event){
            let id = $(this).data('id');

            $.ajax({
                url : "{{ route('sodmasteredit') }}",
                type : 'post',
                data : {id : id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function(res) {
                    if (res.status === "success") {
                        let sodDetails = res.sodDetails;

                        if(sodDetails){
                            setTimeout(function(){
                                $('#id').val(sodDetails.id);
                                $('#startDate').val(dateFormat(sodDetails.start_date));
                                $('#interesttype').val(sodDetails.interest_type);
                                $('#status').val(sodDetails.status);
                            },100);

                            $('#basicModal').modal('show');

                        }else{
                            notify(res.messages,'warning');
                        }
                    }else{
                        notify(res.messages,'warning');
                    }
                },error : function(xhr,status,error){
                    {{--  alert('Ajax Not Working');  --}}
                    notify('Ajax Not Working','warning');
                }
            });
        });

        $(document).on('click', '.deletebtn', function(event) {
            event.preventDefault();

            let id = $(this).data('id');

            if (!id) {
                Swal.fire({
                    title: "Error!",
                    text: "Invalid data. Please refresh the page and try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }


            Swal.fire({
                title: "Are you sure?",
                text: `You are about to delete transaction ID #${id}. This action cannot be undone!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Deleting...",
                        text: "Please wait while we delete the transaction.",
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });


                    $.ajax({
                        url: "{{ route('deletesodmaster') }}",
                        type: 'POST',
                        data: {id: id,},
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        dataType: "json",
                        success: function(res) {
                            $('.ccltrfddeletebtn').prop('disabled', false);

                            if (res.status === 'success') {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: `Transaction ID #${id} has been successfully deleted.`,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });
                            window.location.href = "{{ route('sodmasterindex') }}";

                            } else {
                                Swal.fire({
                                    title: "Warning!",
                                    text: res.messages ||
                                        "Deletion failed. Please try again.",
                                    icon: "warning",
                                    confirmButtonText: "OK"
                                });
                            }
                        },
                        error: function(jqXHR, textStatus) {
                            $('.ccltrfddeletebtn').prop('disabled', false);

                            if (textStatus === "timeout") {
                                Swal.fire({
                                    title: "Timeout!",
                                    text: "The server is taking too long to respond. Please try again later.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong. Please try again.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        }
                    });
                }
            });
        });


    });

</script>
@endpush



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


