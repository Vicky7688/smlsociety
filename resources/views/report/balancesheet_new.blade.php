@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Balance Sheet</h4>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="balanceSheetform">
                        <div class="row">
                            @php
                            $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                        @endphp
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="Date from" class="form-label">Date from</label>
                                <input type="text" class="form-control formInputsReport" id="date_from" name="date_from" value="{{ $currentDate }}"
                                />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="Till Date" class="form-label">Till Date</label>
                                <input type="text" class="form-control formInputsReport" id="date_till_date" value="{{ date('d-m-Y', strtotime(session('sessionEnd')))}}" name="date_till_date" />
                            </div>
                            <div class="col-lg-8 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">

                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                        id="viewdatabooksdetails">
                                        View
                                    </button>
                                    <!--<a type="button" href="{{route('balancebookPrint.print')}}" target="_blank"-->
                                    <!--    class="ms-2 btn btn-primary print-button reportSmallBtnCustom">-->
                                    <!--    Print-->
                                    <!--</a>-->
                                     <button type="button"
                                         id="printButton" onclick="printReport()"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </button>
                                    <div class="ms-2 dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card" >
        <div class="card-body tablee" id="balanebookrecords">

        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function(e){
        // var currentYear = new Date().getFullYear();
        // var nextYear = currentYear + 1;
        // var formattedDate1 = "01-04-" + currentYear;
        // var formattedDate2 = "31-03-" + nextYear;
        // $("#date_from").val(formattedDate1);
        // $("#date_till_date").val(formattedDate2);



        $(document).on("click","#viewdatabooksdetails",function(e){
           var startDate = $('#balanceSheetform').find('[name="date_from"]').val();
           var endDate = $('#balanceSheetform').find('[name="date_till_date"]').val();

              $.ajax({
                url: "{{route('getbalancesheet')}}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                     blockForm('#balanceSheetform')
                },
                data: {
                    'startdate': startDate,
                    'enddate': endDate,
                },
                success: function(data) {
                     $("#balanceSheetform").unblock();
                    $('#balanebookrecords').html(data);
                },
                 error: function(error) {
                    $("#balanceSheetform").unblock();
                    notify("Something went wrong", 'warning');
                }
            });
        });
    });


     function printReport() {

           $('.table').css('border', '1px solid');
           $('th').css('border:1px solid #100101');
           $('td').css('border:1px solid #100101');
            var printContents = document.getElementById('balanebookrecords').innerHTML;
            var originalContents = document.body.innerHTML;
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
             var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

            // Add header for printing
            var header = `
                <div style="text-align: center;">
                    <h4>{{$branch->name}}</h4>
                    <h6>{{$branch->address}}</h6>
                    <h6>Balancesheet From `+ formatDate(startDate) +` To `+ formatDate(endDate) +`</h6>
                </div>
            `;
            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
</script>
@endpush
