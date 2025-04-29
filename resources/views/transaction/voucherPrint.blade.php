@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8">
            <h4 class="py-2"><span class="text-muted fw-light"></span>SERVICE SOCIETY LTD</h4>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" style="float: right;" id="printButton"
                onclick="printReport()">Print</button>
        </div>
    </div>
    <h5>Voucher No: {{ $journalEntries->first()->voucherId }}</h5>
    <section class="section">
        <div id="printDiv" class="card pt-5 pb-4">
            <div class="row justify-content-between">
                <div class="col-md-12">
                    <div class="address pb-4 w-50 mx-auto text-center">
                        <h3>THE BARI AGRICULTURE SERVICE SOCIETY LTD</h3>
                        <h6>VILL AND PO BARI</h6>
                        <h6>{{Carbon\Carbon::parse(request('start_date'))->format('d-M-Y'). " To " .Carbon\Carbon::parse(request('end_date'))->format('d-M-Y')}}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="question_master_table row">
                <div class="col-12">
                    <div class="table-responsive mx-2">
                        <table class="table text-center table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Sr No</th>
                                    <th scope="col">Group</th>
                                    <th scope="col">Ledger</th>
                                    <th scope="col">Dr Amount</th>
                                    <th scope="col">Cr Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $grandTotalDr = 0;
                                $grandTotalCr = 0;
                                @endphp
                                @foreach ($journalEntries as $index => $entry)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <!-- Display group name -->
                                    <td>{{ isset($groups[$entry->groupCode]) ? $groups[$entry->groupCode] : 'N/A' }}
                                    </td>
                                    <!-- Display ledger name -->
                                    <td>{{ isset($ledgers[$entry->ledgerCode]) ? $ledgers[$entry->ledgerCode] : 'N/A' }}
                                    </td>
                                    <td>{{ $entry->drAmount }}</td>
                                    <td>{{ $entry->crAmount }}</td>
                                </tr>
                                @php
                                $grandTotalDr += $entry->drAmount;
                                $grandTotalCr += $entry->crAmount;
                                @endphp
                                @endforeach
                                <!-- Displaying grand total row -->
                                <tr>
                                    <td colspan="3" class="text-end"><b>Grand Total</b></td>
                                    <td>{{ $grandTotalDr }}</td>
                                    <td>{{ $grandTotalCr }}</td>
                                </tr>
                            </tbody>


                        </table>
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

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>

@endpush