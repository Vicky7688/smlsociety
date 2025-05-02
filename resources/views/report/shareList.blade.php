@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Share List</h4>
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
                            <!--<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">-->
                            <!--    <label for="startDate" class="form-label">Start Date</label>-->
                            <!--    <input type="date" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="startDate"-->
                            <!--        name="startDate" value="{{ now()->format('Y-m-d') }}" />-->
                            <!--</div>-->
                            @php
                            $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                        @endphp
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="endDate" class="form-label">Date Up To</label>
                                <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="endDate"
                                    name="endDate" value="{{ $currentDate }}" />
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <input type="text" name="memberType"class="form-control formInputsReport" id="memberType" value="Member" readonly>
                            </div>
                            <div class="col-lg-5 col-md-12 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                    <button type="button"
                                         id="printButton" onclick="printReport()"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </button>

                                    {{-- <div class="ms-2 dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="sharelistprint">
        <div class="card-body tablee" >
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" >
                    <thead class="table_head verticleAlignCenterReport">
                        <tr>
                            <th class="fw-bold">SR NO</th>
                            <th class="fw-bold">A/C NO</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Father Name</th>
                            <th class="fw-bold">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="table-border-bottom-0">
                        <!-- <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr> -->
                    </tbody>
                    <tbody>
                        <tr>
                            <td><b>Grand Total</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td id="grandTotal"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="loader" style="display:none; position:fixed; top:60%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
@endsection

@push('script')
<script>
function createTableRow(sr, account, name,fname, balance) {
    var row = `<tr>
                    <td>${sr}</td>
                    <td>${account}</td>
                    <td>${name}</td>
                    <td>${fname}</td>
                    <td>${balance.toFixed(2)}</td>
                </tr>`;
    return row;
}
$(document).ready(function() {
    var entriesPerGroup = 20;
    var grandTotal = 0;

    $(document).on('submit', '#formData', function(event) {
        event.preventDefault();
        var element = $(this);
        $('#loader').show();
        $("button[type=submit]").prop('disabled', true);
        $.ajax({
            url: '{{ route("shareList.getData") }}',
            type: 'get',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('#loader').hide();
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
                            var fname = item.father_husband;
                            var balance = item.memberbalence;

                            groupTotal += parseFloat(balance);

                            var row = createTableRow(sr, account, name,fname, balance);
                            tableBody.append(row);
                        });

                        var groupGrandTotalRow = `<tr class="bg-success">
                                        <td colspan="4" class="text-white">Page ${i / entriesPerGroup + 1} </td>
                                        <td class="text-white">${groupTotal.toFixed(2)}</td>
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

function printReport() {

           $('.table').css('border', '1px solid');
            var printContents = document.getElementById('sharelistprint').innerHTML;
            var originalContents = document.body.innerHTML;
            var endDate = $('#endDate').val();
            var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

            // Add header for printing
            var header = `
                 <div style="text-align: center;">

                    <h6> Share List upto `+ formatDate(endDate) +`</h6>
                </div>
            `;
            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
</script>
@endpush
