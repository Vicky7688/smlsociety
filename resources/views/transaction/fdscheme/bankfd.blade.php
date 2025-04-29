@extends('layouts.app')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')
    <style>
        .fonsizechange th,
        .fonsizechange>td {
            font-size: 12px !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-6 fdHeading">
                        <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Bank Fixed (Scheme) Deposit</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <form action="{{ $formurl }}" id="formData" name="formData">
                            @csrf
                            <div class="nav-align-top rdCustom">
                                <div class="tab-content tableContent fdTabContent mt-2">
                                    <div class="tab-pane fade active show" id="fdDetails" role="tabpanel">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">FD Date</label>
                                                <input type="text" id="fddate" name="fddate"
                                                    class="form-control form-control-sm transactionDate" placeholder="FD Date"
                                                    @if (!empty($bankFixedDeposit->fddate)) value="{{ date('d-m-Y', strtotime($bankFixedDeposit->fddate)) }}"
                                                    @else
                                                        value="{{ Session::get('currentdate') }}" @endif />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">FD Number</label>
                                                <input type="text" id="fdnumber" name="fdnumber"
                                                    class="form-control form-control-sm " placeholder="FD Number"
                                                    @if (!empty($bankFixedDeposit->fdnumber)) value="{{ $bankFixedDeposit->fdnumber }}" @endif />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label mb-1">FD Account Ledger</label>
                                                <select name="fdaccountledger" id="fdaccountledger"
                                                    class="select21 form-select form-select-sm Select">
                                                    <option value="">Select Bank</option>

                                                    @foreach ($ledgers as $ledgerslisttt)
                                                        <option
                                                            @if (!empty($bankFixedDeposit->fdaccountledger))
                                                                @if ($bankFixedDeposit->fdaccountledger == $ledgerslisttt->ledgerCode)
                                                                    @selected(true)
                                                                @endif
                                                            @endif
                                                            value="{{ $ledgerslisttt->ledgerCode }}">
                                                            {{ $ledgerslisttt->name }}</option>
                                                    @endforeach


                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Account No</label>
                                                <input type="text" id="accountno" name="accountno"
                                                    class="form-control form-control-sm"
                                                    @if (!empty($bankFixedDeposit->accountno)) value="{{ $bankFixedDeposit->accountno }}" @endif
                                                    placeholder="Account No" />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Amount</label>
                                                <input type="text" id="amount" name="amount"
                                                    class="form-control form-control-sm " placeholder="Amount"
                                                    @if (!empty($bankFixedDeposit->amount)) value="{{ $bankFixedDeposit->amount }}" @endif />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Int. Run From</label>
                                                <input type="text" id="intrestfrom" name="intrestfrom"
                                                    class="form-control form-control-sm " placeholder="Int. Run From"
                                                    @if (!empty($bankFixedDeposit->intrestfrom))
                                                        value="{{ date('d-m-Y',strtotime($bankFixedDeposit->intrestfrom)) }}"
                                                    @else
                                                        value="{{ date('d-m-Y') }}"
                                                    @endif
                                                    />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Int. Rate %</label>
                                                <input type="text" id="intrestrate" name="intrestrate"
                                                    class="form-control form-control-sm " placeholder="Int. Rate %"
                                                    @if (!empty($bankFixedDeposit->intrestrate))
                                                        value="{{ $bankFixedDeposit->intrestrate }}"
                                                    @endif
                                                    />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Period</label>
                                                <input type="text" id="period" name="period"
                                                    class="form-control form-control-sm " placeholder="Period"
                                                    @if (!empty($bankFixedDeposit->period))
                                                        value="{{ $bankFixedDeposit->period }}"
                                                    @endif
                                                    />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Day(s)</label>
                                                <input type="text" id="days" name="days"
                                                    class="form-control form-control-sm " placeholder="Day(s)"
                                                    @if (!empty($bankFixedDeposit->days))
                                                        value="{{ $bankFixedDeposit->days }}"
                                                    @endif
                                                    />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label mb-1">Interest Type</label>
                                                <select name="intresttype" id="intresttype"
                                                    class="select21 form-select form-select-sm Select">
                                                    <option value="Simple Interest">Simple</option>
                                                    <option value="Quarterly Interest">Q-Compound</option>
                                                    <option value="Yearly Interest">Y-Compound</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label mb-1">Saving Bank</label>
                                                <select name="savingbank" id="savingbank"
                                                    class="select21 form-select form-select-sm Select">
                                                    <option value="">Select Saving Bank</option>
                                                    @foreach ($ledgers as $ledgerslisttt)
                                                        <option
                                                        @if(!empty($bankFixedDeposit->savingbank))
                                                            @if($bankFixedDeposit->savingbank == $ledgerslisttt->ledgerCode)
                                                                @selected(true)
                                                            @endif
                                                        @endif

                                                        value="{{ $ledgerslisttt->ledgerCode }}">
                                                            {{ $ledgerslisttt->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Maturity Date</label>
                                                <input type="text" id="maturitydate" name="maturitydate"
                                                    class="form-control form-control-sm " placeholder="Maturity Date"
                                                    @if(!empty($bankFixedDeposit->maturitydate))
                                                            value="{{ date('d-m-Y',strtotime($bankFixedDeposit->maturitydate)) }}"
                                                    @else
                                                        value="{{ date('d-m-Y') }}"
                                                    @endif
                                                     />
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2">
                                                <label class="form-label">Maturity Amount</label>
                                                <input type="text" id="maturityamount" name="maturityamount"
                                                    class="form-control form-control-sm " placeholder="Maturity Amount"

                                                    @if(!empty($bankFixedDeposit->maturityamount))
                                                        value="{{ $bankFixedDeposit->maturityamount }}"
                                                    @endif


                                                    />
                                            </div>

                                            <div
                                                class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2 d-flex align-items-end justify-content-start">
                                                <button type="submit" id="submitButton"
                                                    class="btn btn-primary btn-sm waves-effect waves-light">Save</button>
                                            </div>
                                        </div>
                                    </div>
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
                        <thead class="table_head verticleAlignCenterReport">
                            <tr class="fonsizechange">
                                <th>S No</th>
                                <th>FD Date</th>
                                <th>FD No</th>
                                <th>FD Account No</th>
                                <th>FD Amount</th>
                                <th>Intt.</th>
                                <th>Period</th>
                                <th>Days</th>
                                <th>Intt. Type</th>
                                <th>Mat. Date</th>
                                <th>Mat. Amount</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if (!empty($bank_fds))
                                @foreach ($bank_fds as $row)
                                    <tr class="fonsizechange">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->fddate)) }}</td>
                                        <td>{{ $row->fdnumber }}</td>
                                        <td>{{ $row->accountno }}</td>
                                        <td>{{ $row->amount }}</td>
                                        <td>{{ $row->intrestrate }}</td>
                                        <td>{{ $row->period }}</td>
                                        <td>{{ $row->days }}</td>
                                        <td>{{ $row->intresttype }}</td>
                                        <td>{{ $row->savingbank }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->maturitydate)) }}</td>
                                        <td>{{ $row->maturityamount }}</td>
                                        <td style="display: flex;justify-content: space-evenly; align-items: center;">
                                            <a href="{{ url('transactions/fd/bank/index') }}/{{ $row->id }}"
                                                class="editbtn">
                                                <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i>
                                            </a>
                                            <a href="{{ url('transactions/fd/bank/delete') }}/{{ $row->id }}">
                                                <i class="fa-solid fa-trash iconsColorCustom"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#formData').on('submit', function(e) {
                e.preventDefault(); // Prevent the form from submitting via the browser

                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    url: $(this).attr('action'), // Dynamically set the form action URL
                    type: "POST", // The HTTP method to use (you can change this to 'GET' if needed)
                    data: formData,
                    success: function(response) {
                        // On success, show a success message or perform other actions
                        {{--  alert("Form submitted successfully!");  --}}
                        window.location.href="{{ url('transactions/fd/bank/index') }}";

                        // Reset the form
                        $('#formData')[0].reset();

                        // Optionally, reset select2 elements if you're using select2
                        $('.select21').val('').trigger('change');


                    },
                    error: function(response) {
                        // On error, show an error message or perform other actions
                        alert(response.messages);
                    }
                });
            });
        });
    </script>


    <script>
        function calculateMaturityAmount() {
            var type = $("#intresttype").val();
            var interest = parseFloat($("#intrestrate").val()) || 0;
            var year = parseFloat($("#period").val()) || 0;
            var days = parseFloat($("#days").val()) || 0;
            var amount = parseFloat($("#amount").val()) || 0;
            var maturityAmt = amount; // Default to the amount if any value is missing

            // If any of the necessary inputs are missing, just return the amount
            if (!type || interest === 0 || amount === 0) {
                $("#maturityamount").val(amount);
                return;
            }

            if (type === 'Simple Interest') {
                var totalDays = year * 365 + days;
                var interestAmount = (amount * interest * totalDays) / 36500;
                maturityAmt = amount + interestAmount;
            } else if (type === 'Quarterly Interest') {
                maturityAmt = amount;
                for (var i = 1; i <= year * 4; i++) {
                    maturityAmt *= (interest / 4 + 100) / 100;
                }
            } else if (type === 'Yearly Interest') {
                maturityAmt = amount;
                for (var i = 1; i <= year; i++) {
                    maturityAmt *= (interest + 100) / 100;
                }
                var additionalInterest = (amount * interest * days) / 36500;
                maturityAmt += additionalInterest;
            }

            $("#maturityamount").val(Math.round(maturityAmt));
        }

        $("#days, #period, #amount, #intrestrate").on('keyup', calculateMaturityAmount);
        $("#intresttype").on('change', calculateMaturityAmount);


        if (document.readyState == "complete") {
            $(".transactionDate").val({{  session('currentdate') }});
        }
    </script>
@endsection
