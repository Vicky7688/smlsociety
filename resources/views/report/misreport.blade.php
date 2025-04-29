@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>MIS Report</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="MisformData" name="MisformData">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                    id="startDate" name="startDate" value="{{ Session::get('currentdate') }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                    id="endDate" name="endDate" value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsSelectReport" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                    <option value="NonMember">Nominal Member</option>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                            <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                <button type="submit" id="viewReportBtn"
                                    class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                <a type="button" href="{{route('misPrint.print')}}" target="_blank"
                                    class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                    Print
                                </a>
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
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body tablee">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table_head verticleAlignCenterReport">
                        <tr>
                            <th class="fw-bold">SR NO</th>
                            <th class="fw-bold">Dated</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">A/c Type</th>
                            <th class="fw-bold">A/c No</th>
                            <th class="fw-bold">MIS A/c No</th>
                            <th class="fw-bold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-secondary-subtle" style="background-color: white !important;">
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
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
    var fromDateInput = document.getElementById("startDate");
    var currentDate = new Date();

    currentDate.setFullYear(currentDate.getFullYear() - 1);
    currentDate.setMonth(4 - 1);
    currentDate.setDate(1);

    var formattedDate = currentDate.toISOString().split('T')[0];

    fromDateInput.value = formattedDate;
});

$(document).ready(function() {
    var entriesPerGroup = 10; // Number of entries per page

    $('#viewReportBtn').click(function() {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var memberType = $('#memberType').val();

        $.ajax({
            type: 'GET',
            url: '{{ route("misReport.getData") }}',
            data: {
                startDate: startDate,
                endDate: endDate,
                memberType: memberType
            },
            success: function(response) {
                $('.bg-secondary-subtle').empty();
                var grandTotal = 0;

                var membersData = {};
                response.forEach(function(row) {
                    var memberKey = row.member_account.name + '-' + row.account_no;
                    if (!membersData[memberKey]) {
                        membersData[memberKey] = [];
                    }
                    membersData[memberKey].push(row);
                });

                for (var i = 0; i < response.length; i += entriesPerGroup) {
                    var groupData = response.slice(i, i + entriesPerGroup);
                    var groupTotal = 0;

                    groupData.forEach(function(row, index) {
                        var memberTotal = parseFloat(row.amount);
                        groupTotal += memberTotal;

                        $('.bg-secondary-subtle').append('<tr>' +
                            '<td>' + (i + index + 1) + '</td>' +
                            '<td>' + formatDate(row.date) + '</td>' +
                            '<td>' + row.member_account.name + '</td>' +
                            '<td>' + row.account_no + '</td>' +
                            '<td>' + row.mis_ac_no + '</td>' +
                            '<td>' + row.amount + '</td>' +
                            '</tr>');
                    });

                    $('.bg-secondary-subtle').append('<tr class="bg-success">' +
                        '<td colspan="5" class="text-white">Page ' + (i /
                            entriesPerGroup + 1) + '</td>' +
                        '<td class="text-white">' + groupTotal.toFixed(2) + '</td>' +
                        '</tr>');

                    grandTotal += groupTotal;
                }

                $('.bg-secondary-subtle').append('<tr>' +
                    '<td colspan="5"><strong>Grand Total</strong></td>' +
                    '<td>' + grandTotal.toFixed(2) + '</td>' +
                    '</tr>');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
    // now the way here the grand total is calculated same i want in my print page also

});
</script>

@endpush
