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
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / MIS Report / </span> Print</h4>
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
                                        <th scope="col">Sr No</th>
                                        <th scope="col">Dated</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">A/c Type</th>
                                        <th scope="col">A/c No</th>
                                        <th scope="col">MIS A/c No</th>
                                        <th scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="data-container">
                                    <tr>
                                        <td colspan="7" class="text-center">No data available</td>
                                    </tr>

                                    @foreach($formattedData as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['name'] ?? 'N/A' }}</td>
                                        <td>{{ $row['member_type'] }}</td>
                                        <td>{{ $row['account_no'] }}</td>
                                        <td>{{ $row['mis_ac_no'] }}</td>
                                        <td>{{ $row['amount'] }}</td>
                                    </tr>
                                    @endforeach
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