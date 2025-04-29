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
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / Saving List / </span> Print</h4>
                </div>
                <div class="col-lg-2 col-4 " style="display: flex; justify-content: flex-end; align-items: center;">
                    <button type="button" class="btn btn-primary  waves-effect waves-light smallBtnCustom reportMisAddBtnCustom" id="printButton" onclick="printReport()"><i class="fa-solid fa-print"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="col-sm-12">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <h5 class="reportPrintHeading"></h5>
            </div>
        </div>
    </div> -->

    <section class="section">
        <div id="printDiv" class="card pt-3 pb-4">
            <div class="card-body cardsY">
                <!-- <div class="row justify-content-between">
                    <div class="col-md-12">
                        <div class="address pb-3 w-70 mx-auto text-center">
                            <h4>THE BARI AGRICULTURE SERVICE SOCIETY LTD</h4>
                            <h6>VILL AND PO BARI</h6>
                            <h6>{{Carbon\Carbon::parse(request('start_date'))->format('d-M-Y'). " To " .Carbon\Carbon::parse(request('end_date'))->format('d-M-Y')}}
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
                                        <th scope="col"><b>Sr No</b></th>
                                        <th scope="col"><b>A/C No</b></th>
                                        <th scope="col"><b>Name</b></th>
                                        <th scope="col"><b>Balance</b></th>
                                    </tr>
                                </thead>
                                <tbody id="data-container">
                                    @foreach($formattedData as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row['Ac_no'] }}</td>
                                        <td>{{ $row['member_name'] }}</td>
                                        <td>{{ $row['memberbalence'] }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3"><strong>Grand Total</strong></td>
                                        <td>{{ $grandTotal }}</td>
                                    </tr>
                                </tbody>
                            </table>
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
            <h4>{{$branch->name}}</h4>
            <h6>{{$branch->address}}</h6>
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
