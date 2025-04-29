@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- <p class="h4"><span>Issue Loan Report</span></p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" class="text-muted fw-light">Reports</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-muted fw-light">Audit Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Issue Loan Reports</li>
        </ol>
    </nav> -->

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Issue Loan Report</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="formData" name="formData">
                        <div class="row">
                            @php
                            $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                        @endphp
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="startDate" class="form-label">Date From</label>
                                <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                    id="startDate" name="startDate" value="{{ $currentDate }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="endDate" class="form-label">Date To</label>
                                <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                    id="endDate" name="endDate" value="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                    <option value="NominalMember">Non Member</option>
                                    <option value="Staff">Under Guardian</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">SORT BY</label>
                                <select class="form-select formInputsSelectReport" id="sortBy" name="sortBy">
                                    <option value="loanDate">Loan Date</option>
                                    <option value="accountNo" default selected>Account Number</option>
                                    <option value="pernote">Pernote</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="loanType" class="form-label">Loan Type</label>
                                <select class="form-select formInputsSelectReport" id="loanType" name="loanType">
                                    <option value="All" selected>All</option>
                                    <option value="LAFD">LAFD</option>
                                    <option value="NonLAFD">Non LAFD</option>
                                </select>
                            </div>
                            <div class="col-lg-12 col-md-9 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                    <button type="button" id="viewReportBtn"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                    <!--<a type="button" href="{{route('issueLoanPrint.print')}}" target="_blank"-->
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

    <div class="card" id="sharelistprint">
        <div class="card-body tablee">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table_head verticleAlignCenterReport">
                        <tr>
                            <th class="fw-bold">SR No</th>
                            <th class="fw-bold">A/c No</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Issue Date</th>
                            <th class="fw-bold">Pronote No</th>
                            <th class="fw-bold">Loan Amount</th>
                            <th class="fw-bold">Purpose</th>
                            <th class="fw-bold">Guranteer</th>
                        </tr>
                    </thead>
                    <tbody class="bg-secondary-subtle" style="background-color: white !important;">
                        <tr>
                            <td colspan="8" class="text-center">No data available</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>

document.addEventListener("DOMContentLoaded", function() {
    {{--  var fromDateInput = document.getElementById("startDate");
    var currentDate = new Date();

    currentDate.setFullYear(currentDate.getFullYear() - 1);
    currentDate.setMonth(4 - 1);
    currentDate.setDate(1);

    var formattedDate = currentDate.toISOString().split('T')[0];

    fromDateInput.value = formattedDate;  --}}
});


$(document).ready(function() {
    var entriesPerGroup = 10;

    function fetchAndDisplayData() {
        var memberType = $('#memberType').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var loanType = $('#loanType').val();

        $.ajax({
            type: 'GET',
            url: '{{ route("issueLoanReport.getData") }}',
            data: {
                memberType: memberType,
                startDate: startDate,
                endDate: endDate,
                loanType: loanType
            },
            success: function(response) {
                $('.bg-secondary-subtle').empty();
                var grandTotal = 0;

                for (var i = 0; i < response.length; i += entriesPerGroup) {
                    var groupData = response.slice(i, i + entriesPerGroup);
                    var groupTotal = 0;

                    groupData.forEach(function(row, index) {
                        $('.bg-secondary-subtle').append('<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + row.accountNo + '</td>' +
                        '<td>' + row.name + '</td>' +
                        '<td>' + formatDate(row.loanDate) + '</td>' +
                        '<td>' + row.pernote + '</td>' +
                        '<td>' + row.loanAmount + '</td>' +
                        '<td>' + row.purpose + '</td>' +
                       '<td>' + (row.guranter1AccountNo != null ? row.guranter1AccountNo + ' - ' + row.guranter1 + '<br>' : '') + (row.guranter2AccountNo != null ? row.guranter2AccountNo + ' - ' + row.guranter2 : '') + '</td>' +

                        '</tr>');

                        // $('.bg-secondary-subtle').append('<tr>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td></td>' +
                        // '<td>' + ' (AccNo: ' + row.guranter2AccountNo + ')' + row.guranter2 +  '</td>' +
                        // '</tr>');

                        groupTotal += parseFloat(row.loanAmount);
                    });

                    $('.bg-secondary-subtle').append('<tr class="bg-success">' +
                        '<td colspan="5" class="text-white">Page ' + (i / entriesPerGroup + 1) + '</td>' +
                        '<td class="text-white">' + groupTotal.toFixed(2) + '</td>' +
                        '<td colspan="2"></td>' +
                        '</tr>');

                    grandTotal += groupTotal;
                }

                $('.bg-secondary-subtle').append('<tr>' +
                    '<td colspan="5">Grand Total:</td>' +
                    '<td>' + grandTotal.toFixed(2) + '</td>' +
                    '</tr>');

                $('#grandTotal').text(grandTotal.toFixed(2));
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    $('#viewReportBtn').click(function() {
        fetchAndDisplayData();
    });
});

 function printReport() {

           $('.table').css('border', '1px solid');
            var printContents = document.getElementById('sharelistprint').innerHTML;
            var originalContents = document.body.innerHTML;
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var selectedOption = $('#ledgerCode option:selected');
            var dataValue = selectedOption.attr('data');
             var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

            // Add header for printing
            var header = `
                <div style="text-align: center;">
                    <h4>{{$branch->name}}</h4>
                    <h6>{{$branch->address}}</h6>
                    <h6> Issue Loan Reports  `+ formatDate(startDate) +` To `+ formatDate(endDate) +`</h6>

                </div>
            `;
            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }


</script>
@endpush
