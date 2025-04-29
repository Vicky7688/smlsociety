@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>CDS List</h4>
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
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="endDate"
                                    name="endDate" value="{{ Session::get('currentdate') }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsReport" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                    <option value="NonMember">Non Member</option>
                                    <option value="Staff">Staff</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                    <a type="button" href="{{route('savingPrint.print')}}" target="_blank"
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
                            <th class="fw-bold">A/C NO</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr> -->
                    </tbody>
                    <tbody>
                        <tr>
                            <td><b>Grand Total</b></td>
                            <td></td>
                            <td></td>
                            <td id="grandTotal">
                            </td>
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
function createTableRow(sr, account, name, balance) {
    var row = `<tr>
                <td>${sr}</td>
                <td>${account}</td>
                <td>${name}</td>
                <td>${balance}</td>
            </tr>`;
    return row;
}

$(document).ready(function() {
    var entriesPerGroup = 20;
    var grandTotal = 0;

    $(document).on('submit', '#formData', function(event) {
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled', true);
        $.ajax({
            url: '{{ route('cdsList.getData') }}',
            type: 'get',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);
                if (response['status'] == true) {

                    var data = response.data;
                    var tableBody = $('#tableBody');

                    tableBody.empty();
                    grandTotal = 0;

                    for (var i = 0; i < data.length; i += entriesPerGroup) {
                        var groupData = data.slice(i, i + entriesPerGroup);
                        var groupTotal = 0;

                        groupData.forEach(function(item, index) {
                            var sr = i + index + 1;
                            var account = item.Ac_no;
                            var name = item.member_name;
                            var balance = item.memberbalence;

                            groupTotal += parseFloat(balance);

                            var row = createTableRow(sr, account, name, balance);
                            tableBody.append(row);
                        });

                        var groupGrandTotalRow = `<tr class="bg-success">
                                <td colspan="3" class="text-white">Page ${i / entriesPerGroup + 1} </td>
                                <td class="text-white">${groupTotal}</td>
                            </tr>`;
                        tableBody.append(groupGrandTotalRow);

                        grandTotal += groupTotal;
                    }

                    $('#grandTotal').text(grandTotal);
                }
            },
            error: function(jqXHR, exception) {
                $("button[type=submit]").prop('disabled', false);
                console.log("Something went wrong");
            }
        });
    });
});
</script>
@endpush
