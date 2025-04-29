@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 py-3">
    <div class="row">
        <div class="col-md-12">
            <h4><span class="text-muted fw-light">Transactions / </span>Purchase</h4>
        </div>
    </div>
    <div class="row">
        <form action="javascript:void(0)" id="formData" name="formData">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body px-3 pt-2 pb-0">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="invoiceId" id="invoiceId">
                            <div class="col-md-2 col-12">
                                <label for="invoiceDate" class="form-label">Date</label>
                                <input type="date" class="form-control" placeholder="YYYY-MM-DD" id="invoiceDate"
                                    name="invoiceDate" value="{{ Session::get('currentdate') }}"
                                    max="{{ now()->format('Y-m-d') }}" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="invoiceNo" class="form-label">Invoice</label>
                                <input type="text" class="form-control" placeholder="Invoice No" id="invoiceNo"
                                    name="invoiceNo" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12">
                                <label for="purchaseClient" class="form-label">Purchase Client</label>
                                <select class="form-select" id="purchaseClient" name="purchaseClient">
                                    @if($purchaseClients)
                                        @foreach($purchaseClients as $purchaseClient)
                                            <option value="{{ $purchaseClient->id }}">{{ $purchaseClient->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12">
                                <label for="depot" class="form-label">Purchase Depot</label>
                                <select class="form-select" id="depot" name="depot">
                                    @if($depots)
                                        @foreach($depots as $depot)
                                            <option value="{{ $depot->id }}">{{ $depot->depotName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12">
                                <label for="type" class="form-label">Purchase Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="Control">Control</option>
                                    <option value="NonControl">Non Control</option>
                                    <option value="Fertilizer">Fertilizer</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="paymentType" class="form-label">Type</label>
                                <select class="form-select" id="paymentType" name="paymentType">
                                    @if(!empty($groups))
                                        @foreach($groups as $group)
                                            <option value="{{ $group->groupCode }}">{{ $group->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12">
                                <label for="bank" class="form-label">Bank</label>
                                <select class="form-select" id="bank" name="bank">
                                    @if(!empty($ledgers))
                                        @foreach($ledgers as $ledger)
                                            <option value="{{ $ledger->ledgerCode }}">{{ $ledger->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                            <input type="hidden" name="itemIndex" id="itemIndex">
                            <input type="hidden" name="itemId" id="itemId">
                            <div class="col-md-1 col-12">
                                <label for="itemCode" class="form-label">Item Code</label>
                                <input type="text" class="form-control" id="itemCode" name="itemCode" />
                                <div id="itemList" class="itemList"></div>
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12">
                                <label for="itemName" class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="itemName" name="itemName" readonly />
                                <p class="error"></p>
                            </div>
                            <input type="hidden" name="itemUnit" id="itemUnit">
                            <div class="col-md-1 col-12">
                                <label for="itemQuantity" class="form-label">Quantity</label>
                                <input type="number" step="any" class="form-control" id="itemQuantity"
                                    name="itemQuantity" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemPrice" class="form-label">Rate</label>
                                <input type="number" step="any" class="form-control" id="itemPrice" name="itemPrice"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemSubTotal" class="form-label">Sub Total</label>
                                <input type="number" step="any" class="form-control" id="itemSubTotal"
                                    name="itemSubTotal" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemCess" class="form-label">CESS</label>
                                <input type="number" step="any" class="form-control" id="itemCess" name="itemCess"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemIgst" class="form-label">IGST</label>
                                <input type="number" step="any" class="form-control" id="itemIgst" name="itemIgst"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemSgst" class="form-label">SGST</label>
                                <input type="number" step="any" class="form-control" id="itemSgst" name="itemSgst"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemCgst" class="form-label">CGST</label>
                                <input type="number" step="any" class="form-control" id="itemCgst" name="itemCgst"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="itemGrandTotal" class="form-label">Total</label>
                                <input type="number" step="any" class="form-control" id="itemGrandTotal"
                                    name="itemGrandTotal" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label class="form-label"></label>
                                <button type="button" onclick="addItem()" id="addButton"
                                    class="btn btn-primary waves-effect waves-light">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="card tablee mb-2">
                <div class="card-body data_tables p-3"> -->
            <div class="table-responsive tabledata tablee mb-2">
                <table class="table text-center table-bordered">
                    <thead>
                        <tr class="bg-success-subtle">
                            <th scope="col">Code</th>
                            <th scope="col">Item&nbsp;Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Rate</th>
                            <th scope="col">SubTotal</th>
                            <th scope="col">CESS</th>
                            <th scope="col">IGST</th>
                            <th scope="col">CGST</th>
                            <th scope="col">SGST</th>
                            <th scope="col">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">

                    </tbody>
                </table>
            </div>
            <!-- </div>
            </div> -->

            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body px-3 pt-2 pb-0">
                        <div class="row">
                            <div class="col-md-1 col-12">
                                <label for="subTotal" class="form-label">Sub Total</label>
                                <input type="number" step="any" class="form-control" id="subTotal" name="subTotal" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="cess" class="form-label">CESS</label>
                                <input type="number" step="any" class="form-control" id="cess" name="cess" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="number" step="any" class="form-control" id="igst" name="igst" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="cgst" class="form-label">CGST</label>
                                <input type="number" step="any" class="form-control" id="cgst" name="cgst" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="sgst" class="form-label">SGST</label>
                                <input type="number" step="any" class="form-control" id="sgst" name="sgst" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="freight" class="form-label">Freight</label>
                                <input type="number" step="any" class="form-control" id="freight" name="freight" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="labour" class="form-label">Labour</label>
                                <input type="number" step="any" class="form-control" id="labour" name="labour" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="commission" class="form-label">Commission</label>
                                <input type="number" step="any" class="form-control" id="commission"
                                    name="commission" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="discount" class="form-label">Discount</label>
                                <input type="number" step="any" class="form-control" id="discount" name="discount" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="grandTotal" class="form-label">Total</label>
                                <input type="number" step="any" class="form-control" id="grandTotal" name="grandTotal"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label for="roundOff" class="form-label">Round Off</label>
                                <input type="number" step="any" class="form-control" id="roundOff" name="roundOff"
                                    readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-1 col-12">
                                <label class="form-label"></label>
                                <button type="submit" id="saveButton"
                                    class="btn btn-primary waves-effect waves-light">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="col-12 mt-5">
            <div class="card tablee mb-2">
                <div class="card-body data_tables p-3">
                    <div class="table-responsive tabledata">
                        <table class="table text-center table-bordered">
                            <thead>
                                <tr class="bg-success-subtle">
                                    <th scope="col">Invoice&nbsp;No</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Purchase&nbsp;Client</th>
                                    <th scope="col">Depot</th>
                                    <th scope="col">Net&nbsp;Amount</th>
                                    <th scope="col">Payment&nbsp;Type</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @if(!empty($purchaseInvoice))
                                    <tr>
                                        <td>{{ $purchaseInvoice->invoiceNo }}</td>
                                        <td>{{ $purchaseInvoice->invoiceDate }}</td>
                                        <td>{{ $purchaseInvoice->client->name }}</td>
                                        <td>{{ $purchaseInvoice->purchaseDepot->depotName }}</td>
                                        <td>{{ $purchaseInvoice->grandTotal }}</td>
                                        <td>{{ $purchaseInvoice->paymentTypee->name }}</td>
                                        <td><button type="button" viewId="{{ $purchaseInvoice->id }}"
                                                class='btn view'><i class='fa-regular fa-eye'></i></button></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

    </style>
@endpush

@push('script')
    <script>
        // Code To Handle Dynamic Item List
        $('#paymentType').change(function () {
            $("#bank").find("option").not(":first").remove();
            var groupCode = $(this).val();
            $.ajax({
                url: '{{ route("getLedger") }}',
                type: 'get',
                data: {
                    groupCode: groupCode
                },
                dataType: 'json',
                success: function (response) {
                    $("#bank").find("option").remove();
                    $.each(response["ledgers"], function (key, item) {
                        $("#bank").append(
                            `<option value='${item.ledgerCode}'>${item.name}</option>`)
                    });
                },
                error: function (jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });

        function getItemList() {
            var itemCode = $('#itemCode').val();
            $.ajax({
                url: "{{ route('purchase.getItemList') }}",
                type: "GET",
                data: {
                    itemCode: itemCode
                },
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == true) {
                        $("#itemList").html(response.data);
                    }
                }
            });
        }
        $("#itemCode").on('input', getItemList);


        $(document).on('click', '#itemList .item', function () {
            var itemCode = $(this).text();
            getItemDetail(itemCode);
        });

        function getItemDetail(itemCode) {
            $.ajax({
                url: "{{ route('purchase.getItemDetail') }}",
                type: "GET",
                data: {
                    itemCode: itemCode
                },
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == true) {
                        var itemData = response.data[0];
                        $('#itemCode').val(itemData.code);
                        $('#itemName').val(itemData.name);
                        $('#itemUnit').val(itemData.unit);
                        $('#itemPrice').val(itemData.saleRate);
                        $("#itemList").html("");
                        calculateItemTotal();
                    }
                }
            });
        }

        function calculateItemTotal() {
            var itemCode = $('#itemCode').val();
            var itemQuantity = $('#itemQuantity').val() || 0;
            var itemPrice = $('#itemPrice').val() || 0;
            var itemSubTotal = itemQuantity * itemPrice;
            $.ajax({
                url: "{{ route('purchase.getItemDetail') }}",
                type: "GET",
                data: {
                    itemCode: itemCode
                },
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == true) {
                        var itemData = response.data[0];
                        var taxData = itemData.tax;
                        var itemCess = taxData.cess / 100 || 0;
                        var itemIgst = taxData.igst / 100 || 0;
                        var itemSgst = taxData.sgst / 100 || 0;
                        var itemCgst = taxData.cgst / 100 || 0;
                        itemCess = itemCess * itemSubTotal;
                        itemIgst = itemIgst * itemSubTotal;
                        itemSgst = itemSgst * itemSubTotal;
                        itemCgst = itemCgst * itemSubTotal;
                        var itemGrandTotal = itemSubTotal + itemCess + itemIgst + itemSgst + itemCgst;
                        $('#itemSubTotal').val(itemSubTotal);
                        $('#itemCess').val(itemCess);
                        $('#itemIgst').val(itemIgst);
                        $('#itemSgst').val(itemSgst);
                        $('#itemCgst').val(itemCgst);
                        $('#itemGrandTotal').val(itemGrandTotal);
                    }
                }
            });
        }
        $("#itemQuantity, #itemPrice").on('input', calculateItemTotal);

        function calculateTotal() {
            var subTotal = $('#subTotal').val() || 0;
            var cess = $('#cess').val() || 0;
            var igst = $('#igst').val() || 0;
            var sgst = $('#sgst').val() || 0;
            var cgst = $('#cgst').val() || 0;
            var freight = $('#freight').val() || 0;
            var labour = $('#labour').val() || 0;
            var commission = $('#commission').val() || 0;
            var discount = $('#discount').val() || 0;
            var grandTotal = parseFloat(subTotal) + parseFloat(cess) + parseFloat(igst) + parseFloat(sgst) + parseFloat(
                cgst) + parseFloat(freight) + parseFloat(labour) - parseFloat(commission) - parseFloat(discount);
            var roundOff = Math.round(grandTotal);
            $('#grandTotal').val(grandTotal);
            $('#roundOff').val(roundOff);
        }
        $("#subTotal,#cess,#igst,#sgst,#cgst,#freight,#labour,#commission,#discount").on('input', calculateTotal);

        function addItem() {
            var itemIndex = $('#itemIndex').val();
            var itemCode = $('#itemCode').val();
            var quantity = $('#itemQuantity').val();
            if (quantity > 0) {
                $.ajax({
                    url: "{{ route('purchase.checkItem') }}",
                    type: "GET",
                    data: {
                        itemCode: itemCode
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response['status'] == true) {
                            var newItem = {
                                id: $('#itemId').val(),
                                code: $('#itemCode').val(),
                                name: $('#itemName').val(),
                                unit: $('#itemUnit').val(),
                                quantity: $('#itemQuantity').val(),
                                price: $('#itemPrice').val(),
                                subTotal: $('#itemSubTotal').val(),
                                cess: $('#itemCess').val(),
                                igst: $('#itemIgst').val(),
                                sgst: $('#itemSgst').val(),
                                cgst: $('#itemCgst').val(),
                                grandTotal: $('#itemGrandTotal').val(),
                            }
                            if (newItem) {
                                var items = JSON.parse(localStorage.getItem('items')) || [];
                                if (itemIndex !== undefined && itemIndex !== null && itemIndex !== '') {
                                    items[itemIndex] = newItem;
                                    var message = "Item modified successfully."
                                } else {
                                    items.push(newItem);
                                    var message = "Item added successfully."
                                }
                                localStorage.setItem('items', JSON.stringify(items));
                                displayItems();
                                $('#itemIndex').val('');
                                $('#itemId').val('');
                                $('#itemCode').val('');
                                $('#itemName').val('');
                                $('#itemUnit').val('');
                                $('#itemQuantity').val('');
                                $('#itemPrice').val('');
                                $('#itemSubTotal').val('');
                                $('#itemCess').val('');
                                $('#itemIgst').val('');
                                $('#itemSgst').val('');
                                $('#itemCgst').val('');
                                $('#itemGrandTotal').val('');
                                notify(message, 'success');
                            }
                        } else {
                            notify(message, 'error');
                        }
                    }
                });
            } else {
                notify('Invalid Quantity', 'error');
            }
        }

        function displayItems() {
            var items = JSON.parse(localStorage.getItem('items')) || [];
            var tableBody = $('#tableBody');
            tableBody.empty();
            var subTotal = 0;
            var cessTotal = 0;
            var igstTotal = 0;
            var sgstTotal = 0;
            var cgstTotal = 0;
            $.each(items, function (index, item) {
                tableBody.append(
                    '<tr><td>' + item.code + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.quantity + ' ' + item.unit + '</td>' +
                    '<td>' + item.price + '</td>' +
                    '<td>' + item.subTotal + '</td>' +
                    '<td>' + item.cess + '</td>' +
                    '<td>' + item.igst + '</td>' +
                    '<td>' + item.sgst + '</td>' +
                    '<td>' + item.cgst + '</td>' +
                    '<td>' + item.grandTotal + '</td>' +
                    '<td><button type="button" class="btn" onclick="editItem(' + index + ')"><i class="fa-regular fa-pen-to-square"></i></button>' +
                    '<button type="button" class="btn" onclick="deleteItem(' + index + ')"><i class="fa-regular fa-trash-can"></i></button></td><tr>'
                );
                subTotal += parseFloat(item.subTotal);
                cessTotal += parseFloat(item.cess);
                igstTotal += parseFloat(item.igst);
                sgstTotal += parseFloat(item.sgst);
                cgstTotal += parseFloat(item.cgst);
            });
            $('#subTotal').val(subTotal);
            $('#cess').val(cessTotal);
            $('#igst').val(igstTotal);
            $('#sgst').val(sgstTotal);
            $('#cgst').val(cgstTotal);
            calculateTotal();
        }

        function editItem(index) {
            var items = JSON.parse(localStorage.getItem('items'));
            var item = items[index];
            if (item) {
                $('#itemIndex').val(index);
                $('#itemId').val(item.id);
                $('#itemCode').val(item.code);
                $('#itemName').val(item.name);
                $('#itemUnit').val(item.unit);
                $('#itemQuantity').val(item.quantity);
                $('#itemPrice').val(item.price);
                $('#itemSubTotal').val(item.subTotal);
                $('#itemCess').val(item.cess);
                $('#itemIgst').val(item.igst);
                $('#itemSgst').val(item.sgst);
                $('#itemCgst').val(item.cgst);
                $('#itemGrandTotal').val(item.grandTotal);
            }
        }

        function deleteItem(index) {
            if (confirm('Are you sure, you want to delete this item ?')) {
                var items = JSON.parse(localStorage.getItem('items')) || [];
                items.splice(index, 1);
                localStorage.setItem('items', JSON.stringify(items));
                displayItems();
            }
        }

        $(document).on('submit', '#formData', function (event) {
            event.preventDefault();
            var element = $(this);
            var items = JSON.parse(localStorage.getItem('items'));
            var formData = element.serializeArray();
            formData.push({
                name: 'items',
                value: JSON.stringify(items)
            });
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('purchase.store') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],input[type='number'],select").removeClass(
                            'is-invalid');
                        $('#formData')[0].reset();
                        localStorage.removeItem('items');
                        displayItems();
                        notify(response.message, 'success');
                    } else {
                        var errors = response.errors;
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],input[type='number'],select").removeClass(
                            'is-invalid');
                        $.each(errors, function (key, value) {
                            $(`#${key}`).addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(value);
                        });
                        notify(response.message, 'warning');
                    }
                },
                error: function (jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });

        $(document).on('click', '.view', function (event) {
            event.preventDefault();
            var viewId = $(this).attr('viewId');
            fetchData(viewId);
        });

        function fetchData(viewId) {
            $.ajax({
                url: "{{ route('purchase.view','') }}/" + viewId,
                type: "GET",
                success: function (response) {
                    if (response['status'] == true) {
                        $('#invoiceId').val(viewId);
                        $('#invoiceDate').val(response.data.invoiceDate);
                        $('#invoiceNo').val(response.data.invoiceNo);
                        $('#purchaseClient').val(response.data.purchaseClient);
                        $('#depot').val(response.data.depot);
                        $('#type').val(response.data.type);
                        $('#paymentType').val(response.data.paymentType);
                        $('#bank').val(response.data.bank);
                        $('#subTotal').val(response.data.subTotal);
                        $('#cess').val(response.data.cess);
                        $('#igst').val(response.data.igst);
                        $('#sgst').val(response.data.sgst);
                        $('#cgst').val(response.data.cgst);
                        $('#labour').val(response.data.labour);
                        $('#freight').val(response.data.freight);
                        $('#labour').val(response.data.labour);
                        $('#commission').val(response.data.commission);
                        $('#discount').val(response.data.discount);
                        $('#grandTotal').val(response.data.grandTotal);
                        $('#roundOff').val(Math.round(response.data.grandTotal));
                        localStorage.removeItem('items');
                        var itemsData = response.data.purchase_detail;
                        $.each(itemsData, function (key, itemData) {
                            var newItem = {
                                id: itemData.id,
                                code: itemData.itemCode,
                                name: itemData.itemName,
                                unit: itemData.itemUnit,
                                quantity: itemData.quantity,
                                price: itemData.price,
                                subTotal: itemData.subTotal,
                                cess: itemData.cess,
                                igst: itemData.igst,
                                sgst: itemData.sgst,
                                cgst: itemData.cgst,
                                grandTotal: itemData.grandTotal
                            }
                            if (newItem) {
                                var items = JSON.parse(localStorage.getItem('items')) || [];
                                items.push(newItem);
                            }
                            localStorage.setItem('items', JSON.stringify(items));
                        });
                        displayItems();
                    }
                }
            });
        }

        // Load form values when the page loads
        $(document).ready(function () {
            displayItems();

            $(window).on('beforeunload', function (event) {
                var items = JSON.parse(localStorage.getItem('items'));
                if (items && items.length > 0) {
                    var confirmationMessage =
                        'You have unsaved changes. Are you sure you want to leave?';
                    event.returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });

        });

    </script>
@endpush
