@extends('layouts.app')
@section('title', "Item Master")
@section('pagetitle', "Item Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Inventory Module / </span> Create Item</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Item Master</h5>
                    <div class="card-action-element">
                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary " onclick="addSetup()">
                                    Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tablee">
                        <div class="table-responsive text-nowrap tabledata">
                            <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th scope="col">S No.</th>
                                        <th scope="col">Item Code</th>
                                        <th scope="col">Item Name</th>
                                        <th scope="col">Unit</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Opening Stock</th>
                                        <th scope="col">Sale Rate</th>
                                        <th scope="col">Purchase Rate</th>
                                        <th scope="col">Tax On Purchase</th>
                                        <th scope="col">Tax On Sale</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                        
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
                <h5 class="modal-title" id="exampleModalLabel1"> Create Item </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="itemMaster" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="itemMaster" />
                <input type="hidden" name="id" value="new" />

                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="code" class="form-label">Item Code</label>
                            <input type="text" name="code" id="code" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" name="name" id="name" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" name="unit" id="unit" class="form-control formInputsReport" required />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="saleRate" class="form-label">Sale Rate</label>
                            <input type="text" name="saleRate" id="saleRate" class="form-control formInputsReport" required />
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="purchaseRate" class="form-label">Purchase Rate</label>
                            <input type="text" name="purchaseRate" id="purchaseRate" class="form-control formInputsReport" required />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="address" class="form-label">Type</label>
                            <select name="type" class="select21 form-select formInputsSelectReport" id="status-org" data-placeholder="Active">
                                <option value="" selected disabled>Select Type</option>
                                <option value="Control">Control</option>
                                <option value="NonControl">NonControl</option>
                                <option value="Fertilizer">Fertilizer</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="taxId" class="form-label">Tax</label>
                            <input type="text" name="taxId" id="taxId" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="purchaseTax" class="form-label">Purchase Tax</label>
                            <input type="text" name="purchaseTax" id="purchaseTax"
                                class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="saleTax" class="form-label">Sale Tax</label>
                            <input type="text" name="saleTax" id="saleTax" class="form-control formInputsReport">
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="openingStock" class="form-label">Opening Stock</label>
                            <input type="text" step="NA" name="openingStock" id="openingStock" class="form-control formInputsReport">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                            <label for="status-org" class="form-label">Reorder Level</label>
                            <input type="text" step="NA" name="reorderLevel" id="reorderLevel" class="form-control formInputsReport">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
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
    var url = "{{url('statement/fetch')}}/itemMaster";
    var onDraw = function() {

    };

    var options = [{
            "data": "name",
            render: function(data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            "data": "code",
        },
        {
            "data": "name",
        },
        {
            "data": "unit",
        },
        {
            "data": "type",
        },
        {
            "data": "openingStock",
        },
        {
            "data": "saleRate",
        },
        {
            "data": "purchaseRate",
        },
        {
            "data": "purchaseTax",
        },
        {
            "data": "saleTax",
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
                    `<div style="display: flex;justify-content: space-around; align-items: center;"> <a href="javascript:void(0);" onclick="editItemSetup('` + full.id + `','` +
                    full.code + `','` + full.name + `','` + full.type + `','` + full
                    .unit + `','` + full.purchaseRate + `','` + full.saleRate + `','` +
                    full.taxId + `','` + full.purchaseTax + `','` + full.saleTax + `','` + full.openingStock + `','`+ full.reorderLevel + `','` + full.status +
                    `')"><i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i></a>`;
                menu += `<a onclick="deleteItem('` + full.id +
                    `', 'deleteItem')" href="javascript:void(0);" ><i class='fa-solid fa-trash iconsColorCustom'></i></a > </div>`;
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

    $("#itemMaster").validate({
        rules: {
            name: {
                required: true,
            },
            code: {
                required: true,
            },
            type: {
                required: true,
            },
            unit: {
                required: true,
            },
            purchaseRate: {
                required: true,
            },
            saleRate: {
                required: true,
            },
            taxId: {
                required: true,
            },
            purchaseTax: {
                required: true,
            },
            saleTax: {
                required: true,
            },
            openingLevel: {
                required: true,
            },
            reorderLevel: {
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
            code: {
                required: "Please enter value",
            },
            type: {
                required: "Please enter value",
            },
            unit: {
                required: "Please enter value",
            },
            purchaseRate: {
                required: "Please enter value",
            },
            saleRate: {
                required: "Please enter value",
            },
            taxId: {
                required: "Please enter value",
            },
            purchaseTax: {
                required: "Please enter value",
            },
            saleTax: {
                required: "Please enter value",
            },
            openingStock: {
                required: "Please enter value",
            },
            reorderLevel: {
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
            var form = $('#itemMaster');
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

function editItemSetup(id,code, name, type, unit, purchaseRate, saleRate, taxId, purchaseTax, saleTax, openingStock, reorderLevel, status) {
    $('#basicModal').find('.msg').text("Edit Item Master");
    $('#basicModal').find('input[name="id"]').val(id);
    $('#basicModal').find('input[name="code"]').val(code);
    $('#basicModal').find('input[name="name"]').val(name);
    var typeSelect = $('#basicModal').find('select[name="type"]');
    typeSelect.val(type).trigger('change');
    $('#basicModal').find('input[name="unit"]').val(unit);
    $('#basicModal').find('input[name="purchaseRate"]').val(purchaseRate);
    $('#basicModal').find('input[name="saleRate"]').val(saleRate);
    $('#basicModal').find('input[name="taxId"]').val(taxId);
    $('#basicModal').find('input[name="purchaseTax"]').val(purchaseTax);
    $('#basicModal').find('input[name="saleTax"]').val(saleTax);
    $('#basicModal').find('input[name="openingStock"]').val(openingStock);
    $('#basicModal').find('input[name="reorderLevel"]').val(reorderLevel);
    var statusSelect = $('#basicModal').find('select[name="status"]');
    statusSelect.val(status).trigger('change');
    $('#basicModal').modal('show');
}

function addSetup() {
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