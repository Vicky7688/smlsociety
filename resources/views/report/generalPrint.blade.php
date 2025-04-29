@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-10 col-8 pe-0">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / General Ledger / </span> Print</h4>
                </div>
                <div class="col-lg-2 col-4 text-end">
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
                            <h6>{{Carbon\Carbon::parse(request('startDate'))->format('d-M-Y'). " To " .Carbon\Carbon::parse(request('endDate'))->format('d-M-Y')}}
                            </h6>
                        </div>
                    </div>
                </div> -->
                <div class="question_master_table row">
                    <div class="col-12">
                        <div class="table-responsive mx-2">
                            <table class="table text-center table-bordered">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="fw-bold">SR No</th>
                                        <th class="fw-bold">Date</th>
                                        <th class="fw-bold">A/c No</th>
                                        <th class="fw-bold">Head</th>
                                        <th class="fw-bold">Transaction</th>
                                        <th class="fw-bold">Debit</th>
                                        <th class="fw-bold">Credit</th>
                                        <th class="fw-bold">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Opening Amount</td>
                                        <td id="openingAmount">{{ $openingAmount }}</td>
                                        <td></td>
                                        <td id="balanceAmount">{{ $openingAmount }}</td>
                                    </tr>
                                </tbody>
                                <tbody id="tableBody" class="text-center">
                                    @foreach ($generalLedgerEntries as $index => $entry)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d/m/y', strtotime($entry['transactionDate'])) }}</td>
                                        <td>{{ $entry->accountNo }}</td>
                                        <td>{{ Str::startsWith($entry->formName, 'Member-') ? $entry->formName : 'Member-' . $entry->formName }}
                                        </td>
                                        <td>{{ $entry->accountName ?? 'N/A'}}</td>
                                        <td>{{ $entry->transactionType == 'Dr' ? $entry->transactionAmount : '0' }}</td>
                                        <td>{{ $entry->transactionType == 'Cr' ? $entry->transactionAmount : '0' }}</td>
                                        <td>{{ $entry->balance }} (Dr)</td>
                                    </tr>
                                    <!-- and wherever the name is not there show N/A  -->
                                    @endforeach
                                </tbody>
                                <tbody class="text-center">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Grand Total</b></td>
                                        <td id="drTotal">{{ $drTotal }}</td>
                                        <td id="crTotal">{{ $crTotal }}</td>
                                        <td id="balanceTotal">{{ $balanceTotal }} (Dr)</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- -->
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
            <h4>{{$branch->name}}</h4>
            <h6>{{$branch->address}}</h6>
            <h6>{{Carbon\Carbon::parse(request('startDate'))->format('d-M-Y')}} To {{Carbon\Carbon::parse(request('endDate'))->format('d-M-Y')}}</h6>
        </div>
    `;
    printContents = css + header + printContents;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>

@endpush
