@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>NPA Report</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="formData" name="formData">
                            <div class="row align-items-end">
                                @php
                                $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                            @endphp
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="startDate" class="form-label">Date To</label>
                                    <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                        id="startDate" name="startDate" value="{{ $currentDate }}" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="branch" class="form-label">Branch</label>
                                    <select class="form-select formInputsSelectReport" id="branch" name="branch">
                                        <option value="branch" selected>Shimla</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <button type="submit"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                    <a type="button" href="{{ route('issueLoanPrint.print') }}" target="_blank"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </a>
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
                                <th class="fw-bold" colspan="6"></th>
                                <th class="fw-bold" colspan="7">Amount Considered Loan</th>
                                <th class="fw-bold" colspan="7">Amount Considered interest</th>
                            </tr>
                            <tr>
                                <th class="fw-bold">SR No</th>
                                <th class="fw-bold">Member Name</th>
                                <th class="fw-bold">Acc No</th>
                                <th class="fw-bold">Loan Type</th>
                                <th class="fw-bold">Loan Dt</th>
                                <th class="fw-bold">Due Date</th>
                                <th class="fw-bold">0-1 yr</th>
                                <th class="fw-bold">1-3 yr</th>
                                <th class="fw-bold">3-4 yr</th>
                                <th class="fw-bold">4-6 yr</th>
                                <th class="fw-bold">Above 6 yr</th>
                                <th class="fw-bold">NPA %</th>
                                <th class="fw-bold">NPA Amount</th>
                                <th class="fw-bold">0-1 yr</th>
                                <th class="fw-bold">1-3 yr</th>
                                <th class="fw-bold">3-4 yr</th>
                                <th class="fw-bold">4-6 yr</th>
                                <th class="fw-bold">Above 6 yr</th>
                                <th class="fw-bold">NPA %</th>
                                <th class="fw-bold">NPA Interest</th>
                            </tr>
                        </thead>
                        <tbody class="bg-secondary-subtle" id="tbody" style="background-color: white !important;">
                            <tr>
                                <td colspan="20" class="text-center">No data available</td>
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
        $(document).ready(function(){
            $(document).on('submit','#formData',function(e){
                e.preventDefault();
                var formData = $(this).serializeArray();
                $.ajax({
                    url : '{{ route("npaList.getData") }}',
                    type : 'post',
                    data : formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType : 'json',
                    success: function(res) {
                    if (res.status == 'success') {
                        var datarow = res.npalist;
                        var npaDetails = res.npaDetails;
                        var tbody = $("#tbody");
                        tbody.empty();
                        $.each(datarow, function(index, data) {
                            var loanAmount = data.loanAmount;
                            var receiveAmount = data.reciveamount;
                            var balanceAmount = parseInt(loanAmount) - parseInt(receiveAmount);
                            var yearDifference = parseFloat(data.yearDifference);
                            // Find the corresponding npaDetail for this loan
                            var dueAmount = npaDetails.find(detail => detail.loan_id === data.id)?.total_due || 0;
                            dueAmount = Math.round(dueAmount);
                            // console.log(dueAmount);
                                var balance = 0;
                                switch (true){
                                    case yearDifference > 0 && yearDifference <= 1:
                                        balance = balanceAmount;
                                        break;
                                    case yearDifference > 1 && yearDifference <= 3:
                                        balance = balanceAmount;
                                        break;
                                    case yearDifference > 3 && yearDifference <= 4:
                                        balance = balanceAmount;
                                        break;
                                    case yearDifference > 4 && yearDifference <=6:
                                        balance = balanceAmount;
                                        break;
                                    case yearDifference > 6:
                                        balance = balanceAmount;
                                    break;
                                    default:
                                        balance = balanceAmount;
                                    break;
                                }
                                var row = '<tr>'+
                                    '<td>'+(index+1)+'</td>'+
                                    '<td>'+ data.member_account.name +'</td>'+
                                    '<td>'+ data.accountNo +'</td>'+
                                    '<td>' + data.loanname + ' ' + data.loanYear + ' Year ' +'</td>' +
                                    '<td>'+ data.loanDate +'</td>'+
                                    '<td>'+ data.loanEndDate +'</td>'+
                                    '<td>' + (yearDifference > 0 && yearDifference <= 1 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 1 && yearDifference <= 3 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 3 && yearDifference <= 4 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 4 && yearDifference < 6 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 6 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + 0 +'</td>'+
                                    '<td>' + 0 +'</td>'+
                                    '<td>' + (yearDifference > 0 && yearDifference <= 1 ? dueAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 1 && yearDifference <= 3 ? dueAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 3 && yearDifference <= 4 ? dueAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 4 && yearDifference < 6 ? dueAmount : 0) + '</td>'+
                                    '<td>' + (yearDifference > 6 ? balanceAmount : 0) + '</td>'+
                                    '<td>' + 0 +'</td>'+
                                    '<td>' + 0 +'</td>'+
                                '</tr>';
                                tbody.append(row);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
