@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between">
                <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                    <h4 class=""><span class="text-muted fw-light">Transactions / </span>TDS</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body cardsY">
                    <form id="tdsForm" name="tdsForm">
                        <div class="row row-gap-2 pb-5">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">Start Date</label>
                                <input type="text" class="form-control formInputs" placeholder="DD-MM-YYYY" id="start_date" name="start_date" value="{{ Session::get('currentdate') }}"/>

                            </div>
                             {{--  <p class="error"></p>  --}}
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">TDS Start amount</label>
                                <input type="text" class="form-control formInputs" id="tds_start_amount" name="tds_start_amount"/>

                            </div>
                            {{--  <p class="error"></p>  --}}
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">TDS End amount</label>
                                <input type="text" class="form-control formInputs" id="tds_end_amount" name="tds_end_amount"/>

                            </div>
                            {{--  <p class="error"></p>  --}}
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">TDS%</label>
                                <input type="text" class="form-control formInputs" id="tds_rate" name="tds_rate"/>

                            </div>
                            {{--  <p class="error"></p>  --}}
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                <label for="transactionDate" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select formInputsSelect">
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mt-4 saving_column inputesPadding">
                                <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card tablee">
        <div class="card-body data_tables">
            <div class="table-responsive tabledata">
                <table class="table text-center table-bordered">
                    <thead class="table_head verticleAlignCenter">
                        <tr>
                            <th>S.No</th>
                            <th>Start Date</th>
                            <th>Start Slab</th>
                            <th>End Slab</th>
                            <th>TDS Rate</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodys">
                        @if(!empty($tds_slabs))
                            @foreach ($tds_slabs as $row)
                                <tr>
                                    <td>{{ ($loop->index + 1) }}</td>
                                    <td>{{ date('d-m-Y',strtotime($row->start_date)) }}</td>
                                    <td>{{ $row->start_amount }}</td>
                                    <td>{{ $row->end_amount }}</td>
                                    <td>{{ $row->tds_rate }}</td>
                                    <td>{{ ucwords($row->status) }}</td>
                                    <td>
                                        <button class="btn editbtn" data-id="{{ $row->id }}">
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


<div class="modal fade" id="editmodal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Update Scheme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form  name="edittdsstatusForm" id="edittdsstatusForm">
                <div class="modal-body">
                    <input type="text" hidden name="updateid" id="updateid">
                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                        <label for="transactionDate" class="form-label">Status</label>
                        <select name="editstatus" id="editstatus" class="form-select formInputsSelect">
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
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
    .tablee table th,
    .tablee table td {
        padding: 8px;
    }

    .saving_column {
        position: relative;
    }

    .saving_column .error {
        {{--  position: absolute;  --}}
        bottom: -40px;
        left: 12px;
        margin: 0;
        min-height: 38px;
    }

    .page_headings h4,
    .page_headings h6 {
        margin-bottom: 0;
    }

    .table_head tr {
        background-color: #7367f0;
    }

    .table_head tr th {
        color: #fff !important;
    }

    .accountList ul {
        position: absolute;
        left: 12px;
        bottom: 0px;
        transform: translateY(90%);
        width: calc(100% - 24px);
        background-color: aliceblue;
        border: 1px solid #fff;
        border-radius: 5px;
        max-height: 100px;
        overflow-y: auto;
        z-index: 99;
    }

    .accountList ul li {
        border-bottom: 1px solid #fff;
        border-radius: 0;
        padding: 5px 12px;
    }
</style>
@endpush

@push('script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript"></script>

<script>
    $(document).ready(function(){
        $('#tdsForm').validate({
            rules : {
                tds_start_amount : {
                    required : true,
                    digits : true
                },
                tds_end_amount : {
                    required : true,
                    digits : true
                },
                tds_rate : {
                    required : true,
                    digits : true
                },
                status : {
                    required : true
                }
            },
            messages : {
                tds_start_amount : {
                    required : 'Enter Slab Start Amount',
                    digits : 'Enter Only Numeric Value'
                },
                tds_end_amount : {
                   required : 'Enter Slab End Amount',
                    digits : 'Enter Only Numeric Value'
                },
                tds_rate : {
                   required : 'Enter Only Numeric Value',
                    digits : 'Enter Only Numeric Value'
                },
                status : {
                    required : 'Select Slab Status'
                }
            },
            errorElement: 'p',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        $(document).on('submit', '#tdsForm', function(event) {
            event.preventDefault();

            if ($(this).valid()) {
                let formData = $(this).serialize();
                {{--  $('button[type=submit]').prop('disabled', true);  --}}
                $.ajax({
                    url: "{{ route('tds-insert') }}",
                    type: 'post',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(res) {
                        $('button[type=submit]').prop('disabled', false);
                        if (res.status === 'success') {
                            toastr.success(res.messages);
                            $('.table').load(location.href + ' .table');
                            {{--  window.location.href = "{{ route('tds-index') }}";  --}}
                            $('#tdsForm')[0].reset();
                        }
                    }
                });
            }
        });

        $(document).on('click','.editbtn',function(event){
            event.preventDefault();
            $('#editmodal').modal('show');
            let id = $(this).data('id');
            $('#updateid').val(id);

            $.ajax({
                url : "{{ route('tds-status-edit') }}",
                type: 'post',
                data: {id:id},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success'){
                        let tds_id = res.tds_id;
                        $('#editstatus').val(tds_id.status);
                    }
                }
            });
        });

        $(document).on('submit', '#edittdsstatusForm', function(event) {
            event.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('tds-status-update') }}",
                type: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#editmodal').modal('hide');
                        toastr.success(res.messages);
                        $('.table').load(location.href + ' .table');

                    }else{
                        toastr.error(res.messages);
                    }
                }
            });
        });


    });
</script>


@endpush
