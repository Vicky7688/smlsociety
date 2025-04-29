@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between">
                <div class="col-lg-10 col-8 pe-0 misPrintMainHeading">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / Day Book / </span> Print</h4>
                </div>
                <div class="col-lg-2 col-4 " style="display: flex; justify-content: flex-end; align-items: center;">
                    <button type="button" class="btn btn-primary  waves-effect waves-light smallBtnCustom reportMisAddBtnCustom" id="printButton" onclick="printReport()"><i class="fa-solid fa-print"></i></button>
                </div>
            </div>
        </div>
    </div>

    <h5></h5>
    <section class="section">
        <div id="printDiv" class="card pt-3 pb-4">
            <div class="card-body cardsY">
                <!-- <div class="row justify-content-between">
                    <div class="col-md-12">
                        <div class="address pb-3 w-50 mx-auto text-center">
                            <h4>THE BARI AGRICULTURE SERVICE SOCIETY LTD</h4>
                            <h6>VILL AND PO BARI</h6>
                            <h6>{{Carbon\Carbon::parse(request('start_date'))->format('d-M-Y'). " To " .Carbon\Carbon::parse(request('end_date'))->format('d-M-Y')}}
                            </h6>
                        </div>
                    </div>
                </div> -->
                <div>
                    <div class="card-body tablee">
                        <div class="row">
                            <div class="col-md-6 mb-5">
                                <div class="table-responsive">
                                    <h4 class="t-heading mb-2">RECEIPT</h4>
                                    <table class="table text-center table-bordered table-striped">
                                        <thead class="table_head verticleAlignCenterReport">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">V.No</th>
                                                <th scope="col">A/c No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="debittbody">
                                            @foreach ($receiptEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->transactionDate }}</td>
                                                <td>{{ $entry->voucherNo }}</td>
                                                <td>{{ $entry->accountId }}</td>
                                                <td>{{ $entry->accountName }}</td>
                                                <td>{{ $entry->transactionAmount }}</td>
                                                <td>{{ $entry->total }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <h4 class="t-heading mb-2">PAYMENT</h4>
                                    <table class="table text-center table-bordered table-striped">
                                        <thead class="table_head verticleAlignCenterReport">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">V.No</th>
                                                <th scope="col">A/C No</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="credittbody">
                                            @foreach ($paymentEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->transactionDate }}</td>
                                                <td>{{ $entry->voucherNo }}</td>
                                                <td>{{ $entry->accountId }}</td>
                                                <td>{{ $entry->accountName }}</td>
                                                <td>{{ $entry->transactionAmount }}</td>
                                                <td>{{ $entry->total }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td class="bg-danger text-start" colspan="5">Closing Cash</td>
                                                <td class="bg-danger" style="text-align:right" id="closingamount">
                                                    {{ $closingCash }}</td>
                                            </tr>
                                            <tr class="font-weight-bold">
                                                <td class="bg-success text-start text-white" colspan="5"><strong>Total</strong></td>
                                                <td class="bg-success text-white" style="text-align:right">
                                                    {{ $closingAmount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('script')

<script>
function printReport() {
    var printContents = document.getElementById('printDiv').innerHTML;
    var originalContents = document.body.innerHTML;

    var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

    // Add header for printing
    var header = `
        <div style="text-align: center;">
            <h4>THE BARI AGRICULTURE SERVICE SOCIETY LTD</h4>
            <h6>VILL AND PO BARI</h6>
            <h6>{{Carbon\Carbon::parse(request('start_date'))->format('d-M-Y')}} To {{Carbon\Carbon::parse(request('end_date'))->format('d-M-Y')}}</h6>
        </div>
    `;
    printContents = css + header + printContents;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>

@endpush