@extends('layouts.app')

@php
    $table = 'yes';
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span>Agent Commission</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <form id="agentCommissionForm" name="agentCommissionForm">
                            <div class="row row-gap-2">
                                <input type="hidden" name="savingId" id="savingId">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">From Date</label>
                                    <input type="text" class="form-control formInputs transactionDate"
                                        placeholder="DD-MM-YYYY" id="start_date" name="start_date"
                                        value="{{ Session::get('currentdate') }}" />
                                    <p class="error"></p>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">To Date</label>
                                    <input type="text" class="form-control formInputs" placeholder="DD-MM-YYYY"
                                        id="end_date" name="end_date" value="{{ date('d-m-Y') }}" />
                                    <p class="error"></p>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">Agents</label>
                                    <select name="agents" id="agents" class="form-select formInputsSelect">
                                        @if (!empty($agents))
                                            <option value="" selected>Select Agent</option>
                                            @foreach ($agents as $row)
                                                <option value="{{ $row->id }}">{{ $row->id . '-' . $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mt-4 saving_column inputesPadding">
                                    <button type="submit" id="submitButton"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card tablee">
            <div class="card-body data_tables">
                <div class="table-responsive tabledata">
                    <table class="table text-center table-bordered">
                        <thead class="table_head verticleAlignCenter">
                            <tr>
                                <th>S.No</th>
                                <th>Agent Code</th>
                                <th>Agent Name</th>
                                <th>Amount</th>
                                <th>Commission %</th>
                                <th>Comm.Amount</th>
                                <th>TDS %</th>
                                <th>TDS Amt.</th>
                                <th>Security %</th>
                                <th>Security Amt.</th>
                                <th hidden>Type of Acc.</th>
                                <th>Net Amt(After Security)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="totalrd">
                        </tbody>
                        <tbody id="totalfd">
                        </tbody>
                        <tbody id="totaldailysaving">
                        </tbody>
                        <tbody id="totaldailyloan">
                        </tbody>
                        <tbody id="grandtotalcommission">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="card tablee mt-5">
            <h4 class="text-center pt-2"><strong>Paid Commission</strong></h4>
            <div class="card-body">
                <div class="table-responsive tabledata">
                    <table class="table datatables-order table table-bordered" id="table" style="width:100%">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th>S.No</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Agent Name</th>
                                <th>Commission Amt.</th>
                                <th>Security Amt.</th>
                                <th>Paid Commission</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if(!empty($commissions))
                                @foreach ($commissions as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ date('d-m-Y',strtotime($row->startDate)) }}</td>
                                        <td>{{ date('d-m-Y',strtotime($row->endDate)) }}</td>
                                        <td>{{ ucwords($row->member_name) }}</td>
                                        <td>{{ $row->commission_amount }}</td>
                                        <td>{{ $row->security_amount }}</td>
                                        <td>{{ $row->net_amount }}</td>
                                        <td>{{ ucwords($row->status) }}</td>
                                        <td> <button class="btn deletebtn" data-id="{{ $row->id }}">
                                            <i class="fa-solid fa-trash iconsColorCustom"></i>
                                        </button></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('style')
    <style>
        .table thead th,
        .table tbody td {
            padding: 0px;
        }


        .saving_column {
            position: relative;
        }

        .saving_column .error {
            position: absolute;
            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }

        .page_headings h4,
        .page_headings h6 {
            margin-bottom: 0;
        }

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
        }

        .accountList ul {
            position: absolute;
            left: 12px;
            bottom: 0px;
            transform: translateY(90%);
            width: calc(100% - 24px);
            background-color: aliceblue;
            border: 1px solid #fff;
            border-radius: 5px;
            max-height: 100px;
            overflow-y: auto;
            z-index: 99;
        }

        .accountList ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }

        .rows {
            background-color: #519de9;
        }

        .rows td {
            color: white;
            font-size: 15px;
            font-weight: 400;
        }

        .headings {
            font-size: 15px;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            document.getElementById('agentCommissionForm').addEventListener('submit', function(event) {
                event.preventDefault();

                let start_date = document.getElementById('start_date').value;
                let end_date = document.getElementById('end_date').value;
                let agents = document.getElementById('agents').value;

                $.ajax({
                    url: "{{ route('get-agent-commission') }}",
                    type: 'POST',
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        agents: agents
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, // CSRF token for Laravel
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            notify(res.messages, 'success');
                            {{--  let saving_account = res.saving_account;  --}}
                            let rd_account = res.rd_accounts;
                            let fd_account = res.fd_accounts;
                            let daily_saving = res.daily_saving;
                            let daily_loan = res.daily_loan;
                            let tds = res.tds_rate ?? 0;
                            let security_rate = 10;

                            //_________Grand Total's
                            let grandTotal = 0;
                            let grandTotalCommissionAmount = 0;
                            let grandTotalTdsAmount = 0;
                            let grandTotalNetAmount = 0;

                            let start_date = $('#start_date').val();
                            let end_date = $('#end_date').val();

                            $('#grandtotalcommission').empty();


                            let serialNumber = 1;

                            $('#totalrd').empty();
                            if (rd_account && rd_account.length > 0) {
                                let newrow =
                                    `<tr style="color:black;"><td colspan="10">Recurring Deposit</td></tr>`;
                                $('#totalrd').append(newrow);

                                rd_account.forEach((data, index) => {
                                    let rd_amount = parseFloat(data.rd_amount);
                                    let commissionRate = parseFloat(data.commissionRD);
                                    let commissionamount = ((rd_amount * commissionRate) / 100);
                                    let tds_amount = ((commissionamount * security_rate) / 100);
                                    let netCommission = commissionamount - tds_amount;

                                    let row = `<tr>
                                    <td>${serialNumber++}</td>
                                    <td>${data.agent_ids}</td>
                                    <td>${data.agent_name}</td>
                                    <td>${data.rd_amount}</td>
                                    <td>${data.commissionRD}</td>
                                    <td>${commissionamount}</td>
                                     <td>-</td>
                                    <td>-</td>
                                    <td>${security_rate}</td>
                                    <td>${tds_amount}</td>
                                    <td>${netCommission}</td>
                                    <td hidden>${'RD'}</td>
                                    <td>
                                       -
                                    </td>
                                </tr>`;
                                    $('#totalrd').append(row);

                                    grandTotal += parseFloat(data.rd_amount);
                                    grandTotalCommissionAmount += commissionamount;
                                    grandTotalTdsAmount += tds_amount;
                                    grandTotalNetAmount += netCommission;


                                });
                            } else {
                                let noDataRow = `<tr>
                                <td colspan="9" style="text-align: center;">No RD accounts available for the selected criteria.</td>
                            </tr>`;
                                $('#totalrd').append(noDataRow);
                            }

                            $('#totalfd').empty();
                            if (fd_account && fd_account.length > 0) {
                                let newrow =
                                    `<tr style="color:black;"><td colspan="10">Fixed Deposit</td></tr>`;
                                $('#totalfd').append(newrow);

                                fd_account.forEach((data, index) => {
                                    let fd_amount = parseFloat(data.fd_amount);
                                    let commissionRate = parseFloat(data.commissionFD);
                                    let commissionamount = ((fd_amount *
                                        commissionRate) / 100);
                                    let tds_amount = ((commissionamount *
                                        security_rate) / 100);
                                    let netCommission = commissionamount - tds_amount;

                                    let row = `<tr>
                                    <td>${serialNumber++}</td>
                                    <td>${data.agent_ids}</td>
                                    <td>${data.agent_name}</td>
                                    <td>${data.fd_amount}</td>
                                    <td>${data.commissionFD}</td>
                                    <td>${commissionamount}</td>
                                     <td>-</td>
                                    <td>-</td>
                                    <td>${security_rate}</td>
                                    <td>${tds_amount}</td>
                                    <td>${netCommission}</td>
                                    <td hidden>${'FD'}</td>
                                    <td>
                                       -
                                    </td>
                                </tr>`;
                                    $('#totalfd').append(row);

                                    grandTotal += parseFloat(data.fd_amount);
                                    grandTotalCommissionAmount += commissionamount;
                                    grandTotalTdsAmount += tds_amount;
                                    grandTotalNetAmount += netCommission;


                                });
                            } else {
                                let noDataRow = `<tr>
                                <td colspan="9" style="text-align: center;">No FD accounts available for the selected criteria.</td>
                            </tr>`;
                                $('#totalfd').append(noDataRow);
                            }

                            $('#totaldailysaving').empty();
                            if (daily_saving && daily_saving.length > 0) {
                                let newrow =
                                    `<tr style="color:black;"><td colspan="10">Daily Saving</td></tr>`;
                                $('#totaldailysaving').append(newrow);

                                daily_saving.forEach((data, index) => {
                                    let daily_amount = parseFloat(data.daily_amount);
                                    let commissionRate = parseFloat(data.daily_saving);
                                    let commissionamount = ((daily_amount *
                                        commissionRate) / 100);
                                    let tds_amount = ((commissionamount *
                                        security_rate) / 100);
                                    let netCommission = commissionamount - tds_amount;

                                    let row = `<tr>
                                    <td>${serialNumber++}</td>
                                    <td>${data.agent_ids}</td>
                                    <td>${data.agent_name}</td>
                                    <td>${data.daily_amount}</td>
                                    <td>${data.daily_saving}</td>
                                    <td>${commissionamount}</td>
                                     <td>-</td>
                                    <td>-</td>
                                    <td>${security_rate}</td>
                                    <td>${tds_amount}</td>
                                    <td>${netCommission}</td>
                                     <td hidden>${'DailySaving'}</td>
                                    <td>
                                     -
                                    </td>
                                </tr>`;
                                    $('#totaldailysaving').append(row);

                                    grandTotal += parseFloat(data.daily_amount);
                                    grandTotalCommissionAmount += commissionamount;
                                    grandTotalTdsAmount += tds_amount;
                                    grandTotalNetAmount += netCommission;


                                });
                            } else {
                                let noDataRow = `<tr>
                                <td colspan="9" style="text-align: center;">No Daily Saving accounts available for the selected criteria.</td>
                            </tr>`;
                                $('#totaldailysaving').append(noDataRow);
                            }

                            $('#totaldailyloan').empty();
                            if (daily_loan && daily_loan.length > 0) {
                                let newrow =
                                    `<tr style="color:black;"><td colspan="10">Daily Loan</td></tr>`;
                                $('#totaldailyloan').append(newrow);

                                daily_loan.forEach((data, index) => {
                                    let daily_loan_amount = parseFloat(data.daily_loan);
                                    let commissionRate = parseFloat(data
                                    .commissionLoan);
                                    let commissionamount = ((daily_loan_amount *
                                        commissionRate) / 100);
                                    let tds_amount = ((commissionamount *
                                        security_rate) / 100);
                                    let netCommission = commissionamount - tds_amount;

                                    let row = `<tr>
                                    <td>${serialNumber++}</td>
                                    <td>${data.agent_ids}</td>
                                    <td>${data.agent_name}</td>
                                    <td>${data.daily_loan}</td>
                                    <td>${data.commissionLoan}</td>
                                    <td>${commissionamount}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>${security_rate}</td>
                                    <td>${tds_amount}</td>
                                    <td>${netCommission}</td>
                                     <td hidden>${'DailyLoan'}</td>
                                    <td>
                                        -
                                    </td>
                                </tr>`;
                                    $('#totaldailyloan').append(row);

                                    grandTotal += parseFloat(data.daily_loan);
                                    grandTotalCommissionAmount += commissionamount;
                                    grandTotalTdsAmount += tds_amount;
                                    grandTotalNetAmount += netCommission;

                                });
                            } else {
                                let noDataRow = `<tr>
                                <td colspan="9" style="text-align: center;">No Daily Loan accounts available for the selected criteria.</td>
                            </tr>`;
                                $('#totaldailyloan').append(noDataRow);
                            }

                            $('#grandtotalcommission').empty();


                            let tds_amt = ((grandTotalCommissionAmount * tds)/100);
                            let aftertds = (grandTotalCommissionAmount - tds_amt);
                            let security_amount =  ((aftertds * 10) / 100);
                            let netaccount = grandTotalCommissionAmount - tds_amt - security_amount;
                            $('#grandtotalcommission').append(`<tr class="rows"><td colspan="3">Grand Total</td><td>${grandTotal}</td>
                            <td></td>
                            <td>${grandTotalCommissionAmount}</td>
                            <td>${tds}</td>
                            <td>${tds_amt}</td>
                               <td></td>
                            <td>${security_amount}</td>
                            <td>${netaccount}</td>

                            <td>
                                 <button
                                        class="btn btn-success waves-effect waves-light reportSmallBtnCustom rdaccount"
                                        {{--  data-agent-id="${data.agent_ids}"  --}}
                                        data-comm-amount="${grandTotalCommissionAmount}"
                                        data-tds-amount="${security_amount}"
                                        data-net-amount="${netaccount}"
                                        data-tds-rate="${tds}"
                                        data-tds-amt="${tds_amt}"

                                        {{--  data-account-type="${'RD'}"
                                        data-agent-name="${data.agent_name}"
                                        data-account="${data.acc}"
                                        data-member-name="${data.name}"
                                        data-start-date="${start_date}"
                                        data-end-date="${end_date}"
                                        data-commission-rate="${commissionRate}"
                                        data-tds-rate="${security_rate}"  --}}
                                        >
                                        Pay
                                    </button>
                            </td>
                        </tr>`);











                            //____________________________Saving Accounts Details___________________
                            {{--  SavingDepositDetails(saving_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date);  --}}

                            //____________________________RD Accounts Details___________________
                            {{--  RecurringDepositDetails(rd_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date);  --}}

                            //____________________________FD Commission_________________________
                            {{--  FixedDepositDetails(fd_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date);  --}}

                            //____________Grand Total
                            {{--  let grandtotalcommission = $('#grandtotalcommission');
                        grandtotalcommission.empty();

                        grandtotalcommission.append(`<tr class="rows"><td colspan="3">Grand Total</td><td>${grandTotal}</td>
                           <td></td>
                            <td>${grandTotalCommissionAmount}</td>
                            <td></td>
                            <td>${grandTotalTdsAmount}</td>
                            <td>${grandTotalNetAmount}</td>
                            <td></td>
                        </tr>`);  --}}
                        } else {
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });

            //___________Commssion Save
            $(document).on('click', '.rdaccount', function(event) {
                event.preventDefault();

                //______________Get Details of Single Row
                {{--  let agent_id = $(this).data('agent-id');  --}}
                let commission_amount = $(this).data('comm-amount');
                let security_amount = $(this).data('tds-amount');
                let net_amount = $(this).data('net-amount');
                let agents = $('#agents').val();
                let tds_rate = $(this).data('tds-rate');
                let tds_amount = $(this).data('tds-amt');

                {{--  let account_type = $(this).data('account-type');
                    let agent_name = $(this).data('agent-name');
                    let account = $(this).data('account');
                    let name = $(this).data('member-name');

                    let commission_rate = $(this).data('commission-rate');
                    let tds_rate = $(this).data('tds-rate');  --}}

                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                const data = {
                    commission_amount: commission_amount,
                    security_amount: security_amount,
                    net_amount: net_amount,
                    tds_rate : tds_rate,
                    tds_amount : tds_amount,
                    agents: agents,
                    startDate: startDate,
                    endDate: endDate,
                    {{--  agentid: agent_id,  --}}
                    {{--  account_type : account_type,
                    agent_name:agent_name,
                    account : account,  --}}
                    {{--  name : name,  --}}
                    {{--  commission_rate : commission_rate,
                    tds_rate : tds_rate  --}}
                };


                $.ajax({
                    url: "{{ route('paid-agent-commission') }}",
                    type: 'post',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, // CSRF token for Laravel
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            alert(res.messages);
                            window.location.href = '{{ route('agent-commission-index') }}';
                        } else {
                            alert(res.messages);
                        }
                    }
                })
            });

            $(document).on('click','.deletebtn',function(event){
                event.preventDefault();
                let id = $(this).data('id');

                swal({
                    title: 'Are you sure?',
                    text: "You want to delete a transaction. It cannot be recovered.",
                    icon: 'warning',
                    buttons: {
                        cancel: "Cancel",
                        confirm: {
                            text: "Yes, Delete",
                            closeModal: false
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        // Show loading spinner
                        swal({
                            title: 'Deleting...',
                            text: 'Please wait while the transaction is being deleted.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false
                        });

                        $.ajax({
                            url : "{{ route('deletepaidcommission') }}",
                            type : 'post',
                            data : {id : id},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType : 'json',
                            success : function(res){
                                if(res.status === 'success'){
                                    window.location.href="{{ route('agent-commission-index') }}";
                                }else{
                                    notify(res.messages,'warning');
                                }
                            }
                        });
                    }
                });
            });


            //____________Saving Account Loop
            {{--  function SavingDepositDetails(saving_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date) {
            let saving_total = 0;
            let saving_comm_amount = 0;
            let saving_tds_amount = 0;
            let saving_net_amount = 0;

            let tableBody = $('#tableBody');
            tableBody.empty();
            let totalsaving = $('#totalsaving');
            totalsaving.empty();

            if (saving_account && saving_account.length > 0) {
                $('#savingrow').append(`<tr class="headings"><td colspan="10"><strong>Saving Account</strong></td></tr>`);

                saving_account.forEach((data, index) => {
                    //__________Get Commission
                    let amount = data.saving_amount;
                    let commission_rate = data.commissionSaving;
                    let comm = (amount * commission_rate) / 100;

                    //___________Get TDS Amount
                    let tds_amount = (comm * security_rate) / 100;

                    //_________Net Commission After Deduction of TDS
                    let net_amount = comm - tds_amount;

                    let row = `<tr class="rowssssss">
                        <td>${(index + 1)}</td>
                        <td>${data.name}</td>
                        <td>${data.acc}</td>
                        <td>${data.saving_amount}</td>
                        <td>${commission_rate}</td>
                        <td>${comm}</td>
                        <td>${security_rate}</td>
                        <td>${tds_amount}</td>
                        <td hidden>${'RD'}</td>
                        <td>${net_amount}</td>
                        <td>
                            <button
                                class="btn btn-success waves-effect waves-light reportSmallBtnCustom savingaccount"
                                data-agent-id="${data.agent_ids}"
                                data-comm-amount="${comm}"
                                data-tds-amount="${tds_amount}"
                                data-net-amount="${net_amount}"
                                data-account-type="${'saving'}"
                                data-agent-name="${data.agent_name}"
                                data-account="${data.acc}"
                                data-member-name="${data.name}"
                                data-start-date="${start_date}"
                                data-end-date="${end_date}"
                                data-commission-rate="${commission_rate}"
                                data-tds-rate="${security_rate}">
                                Pay
                            </button>
                        </td>
                    </tr>`;

                    tableBody.append(row);
                    grandTotal += data.saving_amount;
                    saving_total += data.saving_amount;
                    saving_comm_amount += comm;
                    saving_tds_amount += tds_amount;
                    saving_net_amount += net_amount;

                    grandTotalCommissionAmount += comm;
                    grandTotalTdsAmount += tds_amount;
                    grandTotalNetAmount += net_amount;
                });

                totalsaving.append(`<tr class="rows"><td colspan="3">Saving Total</td><td>${saving_total}</td>
                    <td></td>
                    <td>${saving_comm_amount}</td>
                    <td></td>
                    <td>${saving_tds_amount}</td>
                    <td>${saving_net_amount}</td>
                    <td></td>
                </tr>`);
            } else {
                console.error("Saving account data is invalid or empty.");
            }
        }  --}}

            //____________Recurring Account Loop
            {{--  function RecurringDepositDetails(rd_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date) {
            let recurring_total = 0;
            let recurring_comm_amount = 0;
            let recurring_tds_amount = 0;
            let recurring_net_amount = 0;

            let totalrecurring = $('#totalrd');
            totalrecurring.empty();

            let tableBody = $('#tableBodys');
            tableBody.empty();

            if (rd_account && rd_account.length > 0) {
                $('#rdrow').append(`<tr class="headings"><td colspan="10"><strong>RD Account</strong></td></tr>`);

                rd_account.forEach((data, index) => {
                    //___________Get Commission
                    let amount = data.rd_amount;
                    let commission_rate = data.commissionRD;
                    let comm = (amount * commission_rate) / 100;

                    //___________Get TDS Amount
                    let tds_amount = (comm * security_rate) / 100;

                    //_________Net Commission After Deduction of TDS
                    let net_amount = comm - tds_amount;

                    let row = `<tr>
                        <td>${(index + 1)}</td>
                        <td>${data.name}</td>
                        <td>${data.acc}</td>
                        <td>${data.rd_amount}</td>
                        <td>${commission_rate}</td>
                        <td>${comm}</td>
                        <td>${security_rate}</td>
                        <td>${tds_amount}</td>
                        <td hidden>${'rd'}</td>
                        <td>${net_amount}</td>
                        <td>
                            <button
                                class="btn btn-success waves-effect waves-light reportSmallBtnCustom savingaccount"
                                data-agent-id="${data.agent_ids}"
                                data-comm-amount="${comm}"
                                data-tds-amount="${tds_amount}"
                                data-net-amount="${net_amount}"
                                data-account-type="${'rd'}"
                                data-agent-name="${data.agent_name}"
                                data-account="${data.acc}"
                                data-member-name="${data.name}"
                                data-start-date="${start_date}"
                                data-end-date="${end_date}"
                                data-commission-rate="${commission_rate}"
                                data-tds-rate="${security_rate}">
                                Pay
                            </button>
                        </td>
                    </tr>`;
                    tableBody.append(row);

                    grandTotal += data.rd_amount;
                    recurring_total += data.rd_amount;
                    recurring_comm_amount += comm;
                    recurring_tds_amount += tds_amount;
                    recurring_net_amount += net_amount;

                    grandTotalCommissionAmount += comm;
                    grandTotalTdsAmount += tds_amount;
                    grandTotalNetAmount += net_amount;
                });
            } else {
                console.error("RD account data is invalid or empty.");
            }

            totalrecurring.append(`<tr class="rows"><td colspan="3">RD Total</td><td>${recurring_total}</td>
                <td></td>
                <td>${recurring_comm_amount}</td>
                <td></td>
                <td>${recurring_tds_amount}</td>
                <td>${recurring_net_amount}</td>
                <td></td>
            </tr>`);
        }  --}}

            //____________Fixed Account Loop
            {{--  function FixedDepositDetails(fd_account, security_rate, grandTotal, grandTotalCommissionAmount, grandTotalTdsAmount, grandTotalNetAmount, start_date, end_date) {
            if (fd_account && fd_account.length > 0) {
                let fixed_total = 0;
                let fixed_comm_amount = 0;
                let fixed_tds_amount = 0;
                let fixed_net_amount = 0;

                let total_fixed = $('#totalfd');
                total_fixed.empty();

                let fdtableBody = $('#fdtableBody');
                fdtableBody.empty();

                $('#fdrow').append(`<tr class="headings"><td colspan="10"><strong>FD Account</strong></td></tr>`);

                fd_account.forEach((data, index) => {
                    //____________Get Commission
                    let amount = data.fd_amount;
                    let commssion_rate = data.commissionFD;
                    let comm = (amount * commssion_rate) / 100;

                    //___________Get TDS Amount
                    let tds_amount = (comm * security_rate) / 100;

                    //_____________Net Commission After Deduction of TDS
                    let net_amount = comm - tds_amount;

                    let row = `<tr>
                        <td>${(index + 1)}</td>
                        <td>${data.name}</td>
                        <td>${data.acc}</td>
                        <td>${data.fd_amount}</td>
                        <td>${commssion_rate}</td>
                        <td>${comm}</td>
                        <td>${security_rate}</td>
                        <td>${tds_amount}</td>
                        <td hidden>${'fd'}</td>
                        <td>${net_amount}</td>
                        <td>
                            <button
                                class="btn btn-success waves-effect waves-light reportSmallBtnCustom savingaccount"
                                data-agent-id="${data.agent_ids}"
                                data-comm-amount="${comm}"
                                data-tds-amount="${tds_amount}"
                                data-net-amount="${net_amount}"
                                data-account-type="${'fd'}"
                                data-agent-name="${data.agent_name}"
                                data-account="${data.acc}"
                                data-member-name="${data.name}"
                                data-start-date="${start_date}"
                                data-end-date="${end_date}"
                                data-commission-rate="${commssion_rate}"
                                data-tds-rate="${security_rate}">
                                Pay
                            </button>
                        </td>
                    </tr>`;
                    fdtableBody.append(row);

                    grandTotal += data.fd_amount;
                    fixed_total += data.fd_amount;
                    fixed_comm_amount += comm;
                    fixed_tds_amount += tds_amount;
                    fixed_net_amount += net_amount;

                    grandTotalCommissionAmount += comm;
                    grandTotalTdsAmount += tds_amount;
                    grandTotalNetAmount += net_amount;
                });

                fdtableBody.append(`<tr class="rows mb-5"><td colspan="3">FD Total</td><td>${fixed_total}</td>
                    <td></td>
                    <td>${fixed_comm_amount}</td>
                    <td></td>
                    <td>${fixed_tds_amount}</td>
                    <td>${fixed_net_amount}</td>
                    <td></td>
                </tr>`);
            } else {
                console.error("FD account data is invalid or empty.");
            }
        }  --}}

        });
    </script>
@endpush
