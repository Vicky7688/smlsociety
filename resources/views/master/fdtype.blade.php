@extends('layouts.app')
@section('title', " Fd-Type ")
@section('pagetitle', "Fd-Type ")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Accounting / </span> Create Fd-Type</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Create Fd-Type </h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Fd Type
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
                                        <th>Fd Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($FdTypes))
                                        @foreach($FdTypes as $row)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ ucwords($row->type) }}</td>   
                                            <td>{{ $row->status }}</td>
                                            <td>
                                                @if($row->id != 1)
                                                 <a href="javascript:void(0);"
                                                       class="edit-FdType-master"
                                                       data-id="{{ $row->id }}"
                                                       data-type="{{ $row->type }}" 
                                                       data-status="{{ $row->status }}" >
                                                        <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="deleteFdType" data-id="{{ $row->id }}">
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">Add</span> FdType </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>  
            <form name="FdTypeMaster" id="FdTypeMaster">
                <input type="text" hidden name="id" id="id" />
                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-5 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="statename" class="form-label">Fd Type</label>
                            <input type="text" style="text-transform :capitalize;" name="type" id="type"   class="form-control formInputsReport" placeholder="Enter value" required />
                        </div> 
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

    

</style>

@endpush

@push('script') 
  
<script>

    //____________Open Modal
    function addSetup() {
        $('#FdTypeMaster')[0].reset();
        $('#basicModal').find('.msg').text("Add");
        {{--  $('#basicModal').find('input[name="id"]').val("new");  --}}
        $('#basicModal').modal('show');
    }
 

    $(document).ready(function(){
        //___________Form Validations
        $("#FdTypeMaster").validate({
            rules: {
                type: {
                    required: true,
                },
                status: {
                    required: true,
                },  
            },
            messages: {
                type: {
                    required: "Please enter value",
                },
                status: {
                    required: "Please enter value",
                },  
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-FdType").find(".select21"));
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //____________FdType Insert
        $(document).on('submit', '#FdTypeMaster', function(e) {
            e.preventDefault(); 
            if ($(this).valid()) {
                let formData = $(this).serializeArray();
                let url = $('#id').val() ? "{{ route('updateFdType') }}" : "{{ route('FdTypeInsert') }}";
                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#FdTypeMaster')[0].reset();
                            $('#basicModal').modal('hide');
                            window.location.href = '{{ route('FdTypeindex') }}';
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

    //__________Edit FdType
    $(document).on('click', '.edit-FdType-master', function() {
        let FdTypeId = $(this).data('id');
        let FdType = $(this).data('type');
        let headName = $(this).data('head-name'); 
        let showJournalVoucher = $(this).data('show-journal-voucher');
        let status = $(this).data('status'); 
        editFdTypeMasterSetup(FdTypeId, FdType, headName, showJournalVoucher, status);
    });
 
    //__________Edit FdType
    function editFdTypeMasterSetup(FdTypeId, FdType, headName, type, showJournalVoucher, status){
        $('#id').val(FdTypeId);
        $('#type').val(FdType); 
        $('#status').val(status);
        $('#basicModal').modal('show');
    }

        //_______________Delete FdType
        $(document).on('click', '.deleteFdType', function() {
            let FdTypeId = $(this).data('id');
            $.ajax({
                url : "{{ route('deleteFdType') }}",
                type : 'post',
                data : {FdTypeId : FdTypeId},
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
