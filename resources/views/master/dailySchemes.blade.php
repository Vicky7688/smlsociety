@extends('layouts.app')
@section('title', "Daily Schemes")
@section('pagetitle', "Daily Schemes")

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings my-3">
                <div class="card-body py-2">
                <h4 class="py-2"><span class="text-muted fw-light">Masters / Daily Collection / </span> Create Daily Schemes</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <!-- <div class="card-header"> -->
                        <h5 class="card-action-title">Daily Collection Scheme</h5>
                        <div class="card-action-element">
                            <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                    <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                        Add Scheme
                                    </button>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                    <div class="tablee">
                        <div class="table-responsive tabledata"> <!-- removed the class "card-datatable" -->
                            <table class="table datatables-order table table-bordered" id="datatable" >
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Member Type</th>
                                        <th>Start Date</th>
                                        <th>Name</th>
                                        <th>Scheme Type</th>
                                        <th>Duration Type</th>
                                        <th>Days</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>InstType</th>
                                        <th>Intt.</th>
                                        <th>P.Intt.</th>
                                        <th>Status</th>
                                        <th>End Date</th>
                                        <th colspan="2">Action</th>
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
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg"> Add </span> Scheme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dailySchemes" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="dailySchemes" />
                <input type="hidden" name="id" value="new" />
                <div class="modal-body">

                    <div class="row row-gap-2">
<input type="hidden" name="secheme_type" value="CDS" >
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="text" name="start_date" id="start_date" class="form-control formInputsReport" value="{{ Session::get('currentdate') }}">
                        </div>


                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="memberType" class="form-label">Member Type</label>
                            <select name="memberType" id="memberType" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="" disabled selected>Select</option>
                                <option value="Member">Member</option>
                                <option value="NonMember">NonMember</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="interest_type" class="form-label">Member Type</label>
                    <select name="interest_type" id="interest_type" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="" disabled selected>Select</option>
                                <option value="Simple">Simple</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="durationType" class="form-label">Type</label>
                            <select name="durationType" id="durationType" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="" disabled selected>Select</option>
                                <option value="Days">Days</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>
                    <!-- </div>
 <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                    <div class="row"> -->

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="years" class="form-label">Year</label>
                            <input type="text" name="years" id="years" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="months" class="form-label">Month</label>
                            <input type="text" name="months" id="months" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="days" class="form-label">Days</label>
                            <input type="text" name="days" id="days" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="interest" class="form-label">Interest</label>
                            <input type="text" name="interest" id="interest" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="penaltyInterest" class="form-label">Penalty</label>
                            <input type="text" name="penaltyInterest" id="penaltyInterest" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status">Status </label>
                            <select name="status" id="status" class="select21 form-select formInputsSelectReport" data-placeholder="Active">
                                <option value="Active" selected default>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
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
        var url = "{{url('statement/fetch')}}/dailySchemes";
        var onDraw = function() {

        };

        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": "start_date	"
            },
            {
                "data": "memberType"
            },
            {
                "data": "secheme_type"
            },
            {
                "data": "durationType"
            },
            {
                "data": "days"
            },
            {
                "data": "months"
            },
            {
                "data": "years"
            },
            {
                "data": "interest_type"
            },
            {
                "data": "interest"
            },
            {
                "data": "penaltyInterest"
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
                        `<div style="display: flex;justify-content: space-around; align-items: center;"> <a href="javascript:void(0);" onclick="editDailySchemeSetup('` + full.id + `','` +
                        full.name +
                        `','` + full.durationType + `','` + full.interest + `','` +
                        full.penaltyInterest + `','` + full.status + `','` + full.days + `','` + full.months + `','` + full.years + `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                    menu += `<a onclick="deleteItem('` + full.id +
                        `', 'deleteScheme')" href="javascript:void(0);"><i class='fa-solid fa-trash iconsColorCustom'></i></a > </div>`;
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

        $("#dailySchemes").validate({
            rules: {
                name: {
                    required: true,
                },
                durationType: {
                    required: true,
                },
                interest: {
                    required: true,
                    number: true,
                },
                penaltyInterest: {
                    required: true,
                    number: true,
                },
                status: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                durationType: {
                    required: "Please enter value",
                },
                interest: {
                    required: "Please enter value",
                },
                penaltyInterest: {
                    required: "Please enter value",
                },
                status: {
                    required: "Please enter value"
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
                var form = $('#dailySchemes');
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
        })
    })

    function editDailySchemeSetup(id, name, durationType, interest, penaltyInterest, status, days, months, years) {
        $('#basicModal').find('.msg').text("Edit ");
        $('#basicModal').find('input[name="id"]').val(id);
        $('#basicModal').find('input[name="name"]').val(name);
        $('#basicModal').find('select[name="durationType"]').val(durationType).trigger('change');
        // $('#basicModal').find('input[name="duration"]').val(duration);
        $('#basicModal').find('input[name="interest"]').val(interest);
        $('#basicModal').find('input[name="days"]').val(days);
        $('#basicModal').find('input[name="months"]').val(months);
        $('#basicModal').find('input[name="years"]').val(years);
        $('#basicModal').find('input[name="penaltyInterest"]').val(penaltyInterest);
        var statusSelect = $('#basicModal').find('select[name="status"]');
        statusSelect.val(status).trigger('change');
        $('#basicModal').modal('show');
    }

    function addSetup() {
        $('#dailySchemes')[0].reset();
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
    }
</script>
@endpush
