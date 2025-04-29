@extends('layouts.app')
@section('title', 'Calculation')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- <h4 class="py-2"><span class="text-muted fw-light">Reports / General Reports /</span> Recurring Deposit Report</h4> -->
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Calculations / </span>Calculation</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <form action="javascript:void(0)" name="calculation-Form" id="calculation-Form">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="calculationtype" class="form-label">CALCULATION TYPE</label>
                                <select class="form-select" name="calculation" id="calculation" class="form-control" style="text-transform: uppercase;">
                                    <option value="SocietyInterest">SOCIETY INTEREST</option>
                                    <option value="BankInterest">Bank Interest</option>
                                </select>
                            </div>
                            @php
                            $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));
                        @endphp
                            <div class="col-sm-2">
                                <label for="startDate" class="form-label">DATE FROM</label>
                                <input type="text" name="startDate" id="startDate" class="from_date form-control"   value="{{ $currentDate }}">
                            </div>
                            <div class="col-sm-2">
                                <label for="endDate" class="form-label">DATE TO</label>
                                <input type="text" name="endDate" id="endDate" class="to_date form-control"   value="{{   date('d-m-Y', strtotime(Session::get('sessionEnd')))}}">
                            </div>
                            <div class="col-sm-2">
                                <label for="all" class="form-label">MEMBER TYPE</label>
                                <select class="form-select" name="membertype" id="membertype" class="form-control">
                                    <option value="Select">Select</option>
                                    <option value="Member">Member</option>
                                    <option value="NonMember">NonMember</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="interest" class="form-label">INTEREST %</label>
                                <input type="text" class="form-control" name="interest" id="interest">
                            </div>
                            <div class="col-sm-2">
                                <label for="minimumAmount" class="form-label">MINIMUM AMT.</label>
                                <input type="text" class="form-control" name="minimumAmount" id="minimumAmount">
                            </div>
                        </div>
                        <div class="row py-3">
                            <div class="col-md-2">
                                <label for="paidDate" class="form-label">PAID DATE</label>
                                <input type="text" class="form-control" name="paidDate" id="paidDate" value="{{ now()->format('d-m-Y') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="accountNo" class="form-label">ACCOUNT NO. (OPTIONAL)</label>
                                <input type="text" class="form-control" name="accountNo" id="accountNo">
                            </div>
                        </div>
                        <div class="d-flex gap-3 py-3">
                            <div class="">
                                <button type="submit" class="btn btn-primary reportSmallBtnCustom waves-effect waves-light">Calculate</button>
                            </div>
                            {{-- <div class="">
                                <button type="submit" class="btn btn-primary reportSmallBtnCustom waves-effect waves-light">Print</button>
                            </div>
                            <div class="">
                                <button type="submit" class="btn btn-danger reportSmallBtnCustom waves-effect waves-light">Delete</button>
                            </div> --}}
                            <div id="json"></div>
                            <div class="something">
                                <button style="display:none !important" id="approving" type="button" class="btn btn-success reportSmallBtnCustom waves-effect waves-light" onclick="approve()">Approve</button>
                            </div>
                            <div class="somethingg">
                                <button style="display:none !important" id="deleting" type="button" class="btn btn-danger reportSmallBtnCustom waves-effect waves-light"  onclick="deleteentry()" >Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body tablee">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th class="fw-bold">Sr No</th>
                                <th class="fw-bold">Account No</th>
                                <th class="fw-bold">Date</th>
                                <th class="fw-bold">Member Name</th>
                                <th class="fw-bold">Op. Bal</th>
                                <th class="fw-bold">Interest</th>
                                <th class="fw-bold">Cl. Bal</th>
                                <th class="fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-secondary-subtle" style="background-color: white !important;" id="tbody">
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


{{-- ,
                    paidDate: {
                        required: true,
                        date: true
                    } --}}
@push('script')
    <script>
            $("#calculation-Form").validate({
                errorClass: "invalid",
                successClass: "success",
                rules: {
                    startDate: {
                        required: true,
                        date: true
                    },
                    endDate: {
                        required: true,
                        // date: true
                    },
                    membertype: {
                        required: true
                    },
                    interest: {
                        required: true
                    },
                    // paidDate: {
                    //     required: true,
                    //     date: true
                    // }
                },
                messages: {
                    membertype: {
                        required: "Please select a member type"
                    },
                    interest: {
                        required: "Please Enter ROI"
                    }
                }
            });
            $(document).on("submit", "#calculation-Form", function(e){
                e.preventDefault();
                if($(this).valid()){
                    var formdata = $(this).serializeArray();
                    $("button[type=submit]").prop('disabled',true);

                    $.ajax({
                        url: "{{ route('calculation.getdata') }}", // Change to your actual route
                        type: "post",
                        data: formdata,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(res){
                            $("button[type=submit]").prop('disabled',false);
                            if (res.status == 'success') {
                                var datarow = res.transactions;
                                var tbody = $("#tbody");
                                tbody.empty();
                                $.each(datarow, function(index, data) {
                                  if(data.openingAmount>0){
                                    var row = "<tr>" +
                                        "<td>" + (index+1) + "</td>" +
                                        "<td>" + data.accountNo + "</td>" +
                                        "<td>" + data.transactionDate + "</td>" +
                                        "<td>" + data.name + "</td>" +
                                        "<td>" + data.openingAmount + "</td>" +
                                        "<td>" + data.interest + "</td>" +
                                        "<td>" + data.closingBalance + "</td>" +
                                        "<td> <span class='text-success'>" + data.action + " </span></td>" +
                                        "</tr>";

                                    tbody.append(row);
                                  }
                                });




                            /////////////////////////////////// download csv///////////////////////////////////

                            var fileName = "transactions.csv";
                            var columnNames = ["Name", "Account No", "Opening Amount", "Interest", "Closing Balance"];
                            var records = [];

                            $.each(res.transactions, function(index, data) {
                            if(data.openingAmount>0){
                            var record = [
                            data.name,
                            data.accountNo,
                            data.openingAmount,
                            data.interest,
                            data.closingBalance
                            ];
                            records.push(record);
                            }
                            });

                            var responseExcel = columnNames.join(",") + "\n";
                            $.each(records, function(index, record) {
                            responseExcel += record.join(",") + "\n";
                            });

                            var blob = new Blob([responseExcel], {type: "text/csv"});
                            var url = URL.createObjectURL(blob);

                            var a = document.createElement('a');

                            a.classList.add('btn', 'btn-warning', 'reportSmallBtnCustom', 'waves-effect', 'waves-light');
                            a.href = url;
                            a.download = fileName;
                            a.textContent = "Download Report";


                            var jsonElement = document.getElementById('json');
                        jsonElement.innerHTML = ''; // Clear previous download links
                        jsonElement.appendChild(a);


                            if(res.approved=='yes'){
                            $('#deleting').css('display','inline-block');
                                                        }else{
                            $('#approving').css('display','inline-block');
                            }
                            /////////////////////////////////// download csv///////////////////////////////////
                            }
                        },
                        // error: function(xhr, status, error){
                        //     console.error(xhr.responseText); // Log any errors to console
                        // }
                    });
                }
            });
    </script>

    <script>
        function approve(){
            $('#approving').prop('disabled',true);
            var formdata = $('#calculation-Form').serializeArray();
            $.ajax({
                        url: "{{ route('calculation.approve') }}", // Change to your actual route
                        type: "post",
                        data: formdata,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(res){
                            $("button[type=submit]").prop('disabled',false);
                            if (res.status == 'success') {

                                var datarow = res.transactions;
                                var tbody = $("#tbody");
                                tbody.empty();
                                $.each(datarow, function(index, data) {
                                  if(data.openingAmount>0){
                                    var row = "<tr>" +
                                        "<td>" + (index+1) + "</td>" +
                                        "<td>" + data.accountNo + "</td>" +
                                        "<td>" + data.transactionDate + "</td>" +
                                        "<td>" + data.name + "</td>" +
                                        "<td>" + data.openingAmount + "</td>" +
                                        "<td>" + data.interest + "</td>" +
                                        "<td>" + data.closingBalance + "</td>" +
                                        "<td> <span class='text-success'>" + data.action + " </span></td>" +
                                        "</tr>";

                                    tbody.append(row);
                                  }
                                });


                                $('#deleting').css('display','inline-block');
                                $('.something').fadeOut();
                            }
                        },
                    });
        }
        function deleteentry(){
            var formdata = $('#calculation-Form').serializeArray();
            $.ajax({
                        url: "{{ route('calculation.deleteentry') }}", // Change to your actual route
                        type: "post",
                        data: formdata,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(res){
                            $("button[type=submit]").prop('disabled',false);
                            if (res.status == 'success') {

                             $('.somethingg').fadeOut();

                             var tbody = $("#tbody");
                                tbody.empty();
                            }
                        },
                    });
        }
    </script>

<script>
    var startDate = new Date('{{ date('d-m-Y', strtotime(Session::get('sessionStart')))}}');
    var FromEndDate = new Date();
    var ToEndDate = new Date();

    ToEndDate.setDate(ToEndDate.getDate() + 365);

    $('.from_date').datepicker({
        weekStart: 1,
        startDate: '{{ date('d-m-Y', strtotime(Session::get('sessionStart')))}}',
        endDate: FromEndDate,
        autoclose: true,
        format: 'dd-mm-yyyy' // Set the format to 'd-m-Y'
    })
    .on('changeDate', function(selected) {
        startDate = new Date(selected.date.valueOf());
        startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
        $('.to_date').datepicker('setStartDate', startDate);

        // Check if the selected "From" date is after the current selected "To" date
        if (startDate > $('.to_date').datepicker('getDate')) {
            $('.to_date').datepicker('update', startDate);
        }
    });

    $('.to_date').datepicker({
        weekStart: 1,
        startDate: startDate,
        endDate: ToEndDate,
        autoclose: true,
        format: 'dd-mm-yyyy' // Set the format to 'd-m-Y'
    })
    .on('changeDate', function(selected) {
        FromEndDate = new Date(selected.date.valueOf());
        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
        $('.from_date').datepicker('setEndDate', FromEndDate);
    });
</script>

@endpush
