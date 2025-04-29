@extends('layouts.app')
@section('title', " Bank Fd Master")
@section('pagetitle', "Bank Fd Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Bank Fd Master / </span> Create Bank FD</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Create Bank FD Master </h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Bank Details
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
                                        <th>Bank Name</th>
                                        <th>Bank Bank Code</th>
                                        <th>Bank Legder Code</th>
                                        <th>Address</th>
                                        <th>Branch Name</th>
                                        <th>Branch Pincode</th>
                                        {{--  <th>Status</th>  --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($bankfdsDetails))
                                        @foreach($bankfdsDetails as $row)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ ucwords($row->bank_name) }}</td>
                                            <td>{{ ucwords($row->groupCode) }}</td>
                                            <td>{{ ucwords($row->ledgerCode) }}</td>
                                            <td>{{ $row->address }}</td>
                                            <td>{{ $row->branch_name }}</td>
                                            <td>{{ $row->branch_pincode }}</td>
                                            <td>
                                                @php
                                                    $exit_group_code = DB::table('ledger_masters')->where('ledgerCode', $row->groupCode)->first();
                                                @endphp
                                                @if(!$exit_group_code)
                                                    <a href="javascript:void(0);"
                                                       class="editbankfdmaster"
                                                       data-id="{{ $row->id }}">
                                                        <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="deletebankfd" data-id="{{ $row->id }}">
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> Bank Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="bankfdformmaster" id="bankfdformmaster">
                <input type="text" hidden name="id" id="id" />
                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-6 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Bank Name</label>
                            <input type="text" style="text-transform :capitalize;" name="name" id="name" oninput="generategroupcode('this')" class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-6 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="groupCode" class="form-label">Bank Ledger Code</label>
                            <input type="text" readonly name="ledgercode" id="ledgercode"  class="form-control formInputsReport" placeholder="Enter value" readonly />
                        </div>

                        <div class="col-lg-12 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Address</label>
                            <input type="text" name="address" id="address"  class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-6 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Branch Name</label>
                            <input type="text" name="branch_name" id="branch_name"  class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>
                        <div class="col-lg-6 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Pincode</label>
                            <input type="text" name="pincode" id="pincode"  class="form-control formInputsReport" placeholder="Enter value" required />
                        </div>

                        {{--  <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label class="form-label mb-1" for="status-org">Status </label>
                            <select name="status" id="status-org" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>  --}}
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
        $('#bankfdformmaster')[0].reset();
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
                    $('#ledgercode').val(res.newgroup_code);
                }else{
                    alert(res.messages);
                }
            }
        });
    }

    $(document).ready(function(){
        //___________Form Validations
        $("#bankfdformmaster").validate({
            rules: {
                name: {
                    required: true,
                },
            },
            messages: {
                name: {
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
        $(document).on('submit', '#bankfdformmaster', function(e) {
            e.preventDefault();

            if ($(this).valid()) {
                let formData = $(this).serializeArray();
                let url = $('#id').val() ? "{{ route('updatefdmaster') }}" : "{{ route('insertfdmaster') }}";
                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#bankfdformmaster')[0].reset();
                            $('#basicModal').modal('hide');
                            window.location.href = '{{ route('bankfdmasterindex') }}';
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
        $(document).on('click', '.editbankfdmaster', function(event) {
            event.preventDefault();
            let id = $(this).data('id');

            $.ajax({
                url: "{{ route('editbankfdmasterid') }}",
                type: 'post',
                data: {id : id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let existsId = res.existsId;
                        if(!existsId){
                            $('#id').val('');
                            $('#name').val('');
                            $('#ledgercode').val('').prop('disabled',false);
                            $('#address').val('');
                            $('#branch_name').val('');
                            $('#pincode').val('');

                            $('#basicModal').modal('hide');
                        }else{
                            $('#id').val(existsId.id);
                            $('#name').val(existsId.bank_name);
                            $('#ledgercode').val(existsId.ledgerCode).prop('disabled',true);
                            $('#address').val(existsId.address);
                            $('#branch_name').val(existsId.branch_name);
                            $('#pincode').val(existsId.branch_pincode);

                            $('#basicModal').modal('show');
                        }
                    } else {

                        notify(res.messages,'warning');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });



        //_______________Delete Group

        $(document).on('click', '.deletebankfd', function(event) {
            event.preventDefault();

            let id = $(this).data('id');

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
                    $('#ledgerModal').modal('hide'); // Hide the modal before deletion starts

                    Swal.fire({
                        title: "Deleting...",
                        text: "Please wait while we delete the transaction.",
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        url: "{{ route('deletebankfdmaster') }}",
                        type: 'POST',
                        data: {id: id,},
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                // Close the loading modal
                                swal.close();
                                window.location.href="{{ route('bankfdmasterindex') }}";

                            } else {
                                swal.close();
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function(jqXHR, textStatus) {

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
