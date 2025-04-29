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
                        <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Bank Fixed Deposit</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <form id="bankfdform" name="bankfdform">
                            <div class="nav-align-top rdCustom">
                                <div class="tab-content tableContent fdTabContent mt-2">
                                    <div class="tab-pane fade active show" id="fdDetails" role="tabpanel">
                                        <div class="row">
                                            <input type="hidden" name="bankfd_id" id="bankfd_id">
                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Opening Date</label>
                                                <input type="text" id="txdate" name="txdate"
                                                    class="form-control txdate form-control-sm mydatepic"
                                                    placeholder="FD Date"value="{{ date('d-m-Y') }}" />
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Receipt No</label>
                                                <input type="text" id="fd_number" name="fd_number"
                                                    class="form-control form-control-sm " placeholder="FD Number" />
                                            </div>


                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Account No</label>
                                                <input type="text" id="fd_accountno" name="fd_accountno"
                                                    class="form-control form-control-sm" placeholder="Account No" />
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Bank FD</label>
                                                <select class="select21 form-select form-select-sm Select"
                                                    name="bankaccountfd" id="bankaccountfd">
                                                    <option value="" selected>Select Bank</option>
                                                    @if (!empty($bankfds))
                                                        @foreach ($bankfds as $row)
                                                            <option value="{{ $row->id }}">
                                                                {{ ucwords($row->bank_name) }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>


                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">FD Amount</label>
                                                <input type="text" id="fd_amount" name="fd_amount"
                                                    class="form-control form-control-sm " placeholder="Amount" />
                                            </div>



                                            <div class="col-md-2 mt-2">
                                                <label class="form-label mb-1">Interest Type</label>
                                                <select name="intresttype" id="intresttype"
                                                    class="select21 form-select form-select-sm Select">
                                                    {{--  <option value="" selected>Select Type</option>  --}}
                                                    <option value="QuarterlyCompounded">QuarterlyCompounded</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Int. Run From</label>
                                                <input type="text" id="intrestfrom" name="intrestfrom"
                                                    class="form-control form-control-sm " placeholder="Int. Run From"
                                                    value="{{ date('d-m-Y') }}" />
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Int. Rate %</label>
                                                <input type="text" id="intrestrate" name="intrestrate"
                                                    class="form-control form-control-sm " placeholder="Int. Rate %" />
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Year</label>
                                                <input type="text" id="year" name="year"
                                                    class="form-control year form-control-sm " placeholder="Period" />
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Month</label>
                                                <input type="text" id="month" name="month"
                                                    class="form-control month form-control-sm " placeholder="Period" />
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Day(s)</label>
                                                <input type="text" id="days" name="days"
                                                    class="form-control days form-control-sm " placeholder="Day(s)" />
                                            </div>



                                            {{--  onchange="getMaturityCalculation(this)"  --}}



                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Interest Amount</label>
                                                <input type="text" id="interestamount" name="interestamount"
                                                    class="form-control form-control-sm " placeholder="Maturity Amount" />
                                            </div>


                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Maturity Amount</label>
                                                <input type="text" id="maturityamount" name="maturityamount"
                                                    class="form-control form-control-sm " placeholder="Maturity Amount" />
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label">Maturity Date</label>
                                                <input type="text" id="maturitydate" name="maturitydate"
                                                    class="form-control form-control-sm mydatepic" placeholder="FD Date"
                                                    value="{{ date('d-m-Y') }}" />
                                            </div>




                                            {{--  <div class="col-md-2 mt-2">
                                                <label class="form-label mb-1">Deduction Bank/Cash</label>
                                                <select name="savingbank" id="savingbank" class="select21 form-select form-select-sm Select" onchange="transcationType(ele)">
                                                    <option value="">Select Saving Bank</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank">Bank</option>
                                                </select>
                                            </div>  --}}



                                            <div class="col-md-2 mt-2">
                                                <label class="form-label mb-1">Payment Type</label>
                                                <select name="groupType" id="groupType"
                                                    class="select21 groupType form-select form-select-sm Select"
                                                    onchange="transcationType(this)">
                                                    <option value="">Select Bank</option>
                                                    @if (!empty($groups))
                                                        @foreach ($groups as $row)
                                                            <option value="{{ $row->groupCode }}">{{ $row->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <label class="form-label mb-1">Ledger Type</label>
                                                <select name="ledgerType" id="ledgerType"
                                                    class="select21 form-select form-select-sm Select ledgerType">
                                                    <option value="">Select Bank</option>
                                                </select>
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
                                <th>Year</th>
                                <th>Month</th>
                                <th>Days</th>
                                {{--  <th>Intt. Type</th>  --}}
                                <th>Mat. Date</th>
                                <th>Intt. Amount</th>
                                <th>Mat. Amount</th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if ($bank_fd_deposit->isNotEmpty())
                                @foreach ($bank_fd_deposit as $row)
                                    <tr class="fonsizechange">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->fd_date)) }}</td>
                                        <td>{{ $row->fd_no }}</td>
                                        <td>{{ $row->fd_account }}</td>
                                        <td>{{ number_format($row->principal_amount, 2) }}</td>
                                        <td>{{ number_format($row->interest_rate, 2) }}%</td>
                                        <td>{{ $row->year ?? '-' }}</td>
                                        <td>{{ $row->month ?? '-' }}</td>
                                        <td>{{ $row->days ?? '-' }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->maturity_date)) }}</td>
                                        <td>{{ number_format($row->interest_amount, 2) }}</td>
                                        <td>{{ number_format($row->maturity_amount, 2) }}</td>
                                        <td>{{ $row->status }}</td>

                                        @php
                                            $maturityDate = \Carbon\Carbon::parse($row->maturity_date);
                                            $currentDate = \Carbon\Carbon::now();
                                        @endphp

                                        @if ($row->status === 'Mature' || $row->status === 'Closed')
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td>
                                                <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom editfd"
                                                    data-id="{{ $row->id }}"></i>
                                                <i class="fa-solid fa-trash iconsColorCustom deletefd"
                                                    data-id="{{ $row->id }}"></i>
                                            </td>
                                            <td style="display: flex; justify-content: space-evenly; align-items: center;">
                                                <button class="btn btn-danger btn-sm maturefd" title="Mature"
                                                    data-id="{{ $row->id }}">
                                                    Mature
                                                </button>
                                                @if ($currentDate->greaterThanOrEqualTo($maturityDate))
                                                    <button class="btn btn-success btn-sm renewfd" title="Renew"
                                                        data-id="{{ $row->id }}">
                                                        Renew
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="modal fade" id="MatureModaldata" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Bank FD Renew</h5>
                        <input type="hidden" id="updaterdnumber">
                    </div>
                    <div class="row">
                        <form id="bankfdrenew" name="bankfdrenew">
                            <div class="nav-align-top rdCustom">
                                <div class="tab-content tableContent fdTabContent mt-2">
                                    <div class="tab-pane fade active show" id="fdDetails" role="tabpanel">
                                        <div class="row">
                                            <input type="hidden" name="renew_bankfd_id" id="renew_bankfd_id">
                                            <input type="hidden" name="re_id" id="re_id">

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Opening Date</label>
                                                <input type="text" id="renew_txdate" name="renew_txdate"
                                                    class="form-control renew_txdate form-control-sm mydatepic"
                                                    placeholder="FD Date"value="{{ date('d-m-Y') }}" />
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Receipt No</label>
                                                <input type="text" id="renew_number" name="renew_number"
                                                    class="form-control form-control-sm " placeholder="FD Number" />
                                            </div>


                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Account No</label>
                                                <input type="text" id="renew_fd_accountno" name="renew_fd_accountno"
                                                    class="form-control form-control-sm" placeholder="Account No" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Bank FD</label>
                                                <select class="select21 form-select form-select-sm Select"
                                                    name="bankaccountfd" id="bankaccountfd">
                                                    <option value="" selected>Select Bank</option>
                                                    @if (!empty($bankfds))
                                                        @foreach ($bankfds as $row)
                                                            <option value="{{ $row->id }}">
                                                                {{ ucwords($row->bank_name) }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Received Interest</label>
                                                <input type="text" id="received_intt" name="received_intt"
                                                    class="form-control form-control-sm " placeholder="Amount" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Mature Amount</label>
                                                <input type="text" id="fd_mature_amount" name="fd_mature_amount"
                                                    class="form-control form-control-sm " placeholder="Amount" />
                                            </div>

                                            {{--  <div class="col-md-4 mt-2">
                                                <label class="form-label">TDS (%)</label>
                                                <input type="text" id="tds_percentage" name="tds_percentage"
                                                    class="form-control form-control-sm " placeholder="Amount"
                                                    />
                                            </div>  --}}

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">TDS Amt</label>
                                                <input type="text" id="tds_amount" name="tds_amount"
                                                    class="form-control form-control-sm " placeholder="Amount" />
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">New FD Amount</label>
                                                <input type="text" id="renew_amount" name="renew_amount"
                                                    class="form-control form-control-sm " placeholder="Amount" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label mb-1">Interest Type</label>
                                                <select name="renew_intresttype" id="renew_intresttype"
                                                    class="select21 form-select form-select-sm Select">
                                                    {{--  <option value="" selected>Select Type</option>  --}}
                                                    <option value="QuarterlyCompounded">QuarterlyCompounded</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Int. Run From</label>
                                                <input type="text" id="renew_intrestfrom" name="renew_intrestfrom"
                                                    class="form-control form-control-sm" placeholder="Int. Run From"
                                                    value="{{ date('d-m-Y') }}" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Int. Rate %</label>
                                                <input type="text" id="renew_intrestrate" name="renew_intrestrate"
                                                    class="form-control form-control-sm" placeholder="Int. Rate %" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Year</label>
                                                <input type="text" id="renew_year" name="renew_year"
                                                    class="form-control form-control-sm renew_year"
                                                    placeholder="Period" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Month</label>
                                                <input type="text" id="renew_month" name="renew_month"
                                                    class="form-control form-control-sm renew_month"
                                                    placeholder="Period" />
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Day(s)</label>
                                                <input type="text" id="renew_days" name="renew_days"
                                                    class="form-control form-control-sm renew_days"
                                                    placeholder="Day(s)" />
                                            </div>



                                            {{--  onchange="getMaturityCalculation(this)"  --}}



                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Interest Amount</label>
                                                <input type="text" id="renew_interestamount"
                                                    name="renew_interestamount" class="form-control form-control-sm "
                                                    placeholder="Maturity Amount" />
                                            </div>


                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Maturity Amount</label>
                                                <input type="text" id="renew_maturityamount"
                                                    name="renew_maturityamount" class="form-control form-control-sm "
                                                    placeholder="Maturity Amount" />
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">Maturity Date</label>
                                                <input type="text" id="renew_maturitydate" name="renew_maturitydate"
                                                    class="form-control form-control-sm mydatepic" placeholder="FD Date"
                                                    value="{{ date('d-m-Y') }}" />
                                            </div>


                                            <input type="hidden" name="old_interest_amount" id="old_interest_amount">


                                            {{--  <div class="col-md-4 mt-2">
                                                <label class="form-label mb-1">Deduction Bank/Cash</label>
                                                <select name="savingbank" id="savingbank" class="select21 form-select form-select-sm Select" onchange="transcationType(ele)">
                                                    <option value="">Select Saving Bank</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank">Bank</option>
                                                </select>
                                            </div>  --}}



                                            <div class="col-md-4 mt-2">
                                                <label class="form-label mb-1">Payment Type</label>
                                                <select name="renew_groupType" id="renew_groupType"
                                                    class="select21 groupType form-select form-select-sm Select"
                                                    onchange="transcationType(this)">
                                                    <option value="">Select Bank</option>
                                                    @if (!empty($groups))
                                                        @foreach ($groups as $row)
                                                            <option value="{{ $row->groupCode }}">{{ $row->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label mb-1">Ledger Type</label>
                                                <select name="renew_ledgerType" id="renew_ledgerType"
                                                    class="select21 form-select form-select-sm Select ledgerType">
                                                    <option value="">Select Bank</option>
                                                </select>
                                            </div>
                                            <div
                                                class="col-lg-3 col-md-3 col-sm-4 col-6 mt-2 d-flex align-items-end justify-content-start">
                                                <button type="submit" id=""
                                                    class="btn btn-danger btn-sm waves-effect waves-light closebtn">Close</button>
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

    </div>
@endsection
@push('script')
    <script>
        //________Get Cash/Bank Ledgers
        function transcationType(ele) {
            let groups_code = $(ele).val();
            let bankaccountfd = $('#bankaccountfd').val();

            $.ajax({
                url: "{{ route('getbankfdledgeres') }}",
                type: "post",
                data: {
                    groups_code: groups_code,
                    bankaccountfd: bankaccountfd
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let ledgers = res.ledgers;
                        $('.ledgerType').empty();

                        if (Array.isArray(ledgers) && ledgers.length > 0) {
                            ledgers.forEach((data) => {
                                $('.ledgerType').append(
                                    `<option value="${data.ledgerCode}">${data.name}</option>`);
                            });
                        } else {
                            $('.ledgerType').append(`<option value="">No Record Available</option>`);
                        }
                    } else {
                        $('.ledgerType').append(`<option value="">No Record Available</option>`);
                    }
                },
                error: function(error, xhr, status) {
                    notify(error, 'warning');
                }
            });
        }


        //___________Get Maturity Calcultion
        function getMaturityDate() {
            let openingDate = $('.txdate').val();
            let year = parseInt($('.year').val()) || 0;
            let month = parseInt($('.month').val()) || 0;
            let days = parseInt($('.days').val()) || 0;

            if (!openingDate) {
                $('#maturitydate').val('');
                $('#renew_maturitydate').val('');
                return;
            }

            let parts = openingDate.split("-");
            let dateObj = new Date(parts[2], parts[1] - 1, parts[0]);

            // Step 1: Add Years
            dateObj.setFullYear(dateObj.getFullYear() + year);

            // Step 2: Add Months Safely
            let currentDay = dateObj.getDate();
            dateObj.setDate(1); // Prevent overflow issues
            dateObj.setMonth(dateObj.getMonth() + month);

            // Ensure valid day
            let lastDayOfNewMonth = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();
            dateObj.setDate(Math.min(currentDay, lastDayOfNewMonth));

            // Step 3: Add Days
            dateObj.setDate(dateObj.getDate() + days);

            let maturityDate = ("0" + dateObj.getDate()).slice(-2) + "-" + ("0" + (dateObj.getMonth() + 1)).slice(-2) +
                "-" + dateObj.getFullYear();

            $('#maturitydate').val(maturityDate);
            $('#renew_maturitydate').val(maturityDate);
        }

        // Use $(document).on() to ensure event binding works dynamically
        $(document).on('input', '.txdate, .year, .month, .days', getMaturityDate);

        function renewgetMaturityDate() {
            let openingDate = $('.renew_txdate').val();
            let year = parseInt($('.renew_year').val()) || 0;
            let month = parseInt($('.renew_month').val()) || 0;
            let days = parseInt($('.renew_days').val()) || 0;

            if (!openingDate) {
                $('#renew_maturitydate').val('');
                return;
            }

            let parts = openingDate.split("-");
            let dateObj = new Date(parts[2], parts[1] - 1, parts[0]);

            // Step 1: Add Years
            dateObj.setFullYear(dateObj.getFullYear() + year);

            // Step 2: Add Months Safely
            let currentDay = dateObj.getDate();
            dateObj.setDate(1); // Prevent overflow issues
            dateObj.setMonth(dateObj.getMonth() + month);

            // Ensure valid day
            let lastDayOfNewMonth = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();
            dateObj.setDate(Math.min(currentDay, lastDayOfNewMonth));

            // Step 3: Add Days
            dateObj.setDate(dateObj.getDate() + days);

            let maturityDate = ("0" + dateObj.getDate()).slice(-2) + "-" + ("0" + (dateObj.getMonth() + 1)).slice(-2) +
                "-" + dateObj.getFullYear();

            $('#renew_maturitydate').val(maturityDate);
        }

        // Use $(document).on() to ensure event binding works dynamically
        $(document).on('input', '.renew_txdate, .renew_year, .renew_month, .renew_days', renewgetMaturityDate);


        function reverseFormatDate(dateStr) {
            var parts = dateStr.split('-');
            if (parts.length !== 3) {
                return null;
            }
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }


        function calculateInterestAmount() {
            var principal = parseFloat($('#fd_amount').val()) || 0;
            var rate = parseFloat($('#intrestrate').val()) || 0;
            var interestType = $('#intresttype').val() || 'QuarterlyCompounded';

            var years = parseInt($('#year').val()) || 0; // Total years
            var months = parseInt($('#month').val()) || 0; // Total months
            var days = parseInt($('#days').val()) || 0; // Total days

            var totalDays = (years * 365) + (months * 30) + days; // Convert all to days
            var totalYears = Math.floor(totalDays / 365);
            var remainingDaysAfterYears = totalDays % 365;
            var fullQuarters = Math.floor(remainingDaysAfterYears / 90);
            var extraDays = remainingDaysAfterYears % 90;

            var maturityAmount = principal;
            var interest = 0;

            if (interestType === 'QuarterlyCompounded') {
                var n = 4; // Compounded quarterly
                var r = rate / 100; // Convert annual interest rate to decimal

                // **Step 1: Apply compound interest for full years**
                if (totalYears > 0) {
                    maturityAmount = principal * Math.pow(1 + (r / n), n * totalYears);
                    interest = maturityAmount - principal;
                }

                // **Step 2: Apply compound interest for full quarters**
                if (fullQuarters > 0) {
                    maturityAmount *= Math.pow(1 + (r / n), fullQuarters);
                    interest = maturityAmount - principal;
                }

                // **Step 3: Apply simple interest for remaining extra days**
                if (extraDays > 0) {
                    var dailyRate = r / 365; // Convert annual rate to daily rate
                    var extraInterest = maturityAmount * dailyRate * extraDays;
                    interest += extraInterest;
                    maturityAmount += extraInterest;
                }
            } else if (interestType === 'AnnualCompounded') {
                maturityAmount = principal * Math.pow(1 + (rate / 100), totalDays / 365);
                interest = maturityAmount - principal;
            } else if (interestType === 'Fixed') {
                interest = principal * (rate / 100) * (totalDays / 365);
                maturityAmount += interest;
            }

            $('#interestamount').val(Math.round(interest));
            $('#maturityamount').val(Math.round(maturityAmount));
        }

        function DateFormat(date) {
            let newDate = new Date(date);
            let day = newDate.getDate();
            let month = newDate.getMonth() + 1;
            let year = newDate.getFullYear();
            day = day < 10 ? `0${day}` : day;
            month = month < 10 ? `0${month}` : month;
            let formattedDate = `${day}-${month}-${year}`;
            return formattedDate;
        }


        function renewcalculateInterestAmount() {
            var principal = parseFloat($('#renew_amount').val()) || 0;
            var rate = parseFloat($('#renew_intrestrate').val()) || 0;
            var interestType = $('#renew_intresttype').val() || 'QuarterlyCompounded';

            var years = parseInt($('#renew_year').val()) || 0; // Total years
            var months = parseInt($('#renew_month').val()) || 0; // Total months
            var days = parseInt($('#renew_days').val()) || 0; // Total days

            var totalDays = (years * 365) + (months * 30) + days; // Convert all to days
            var totalYears = Math.floor(totalDays / 365);
            var remainingDaysAfterYears = totalDays % 365;
            var fullQuarters = Math.floor(remainingDaysAfterYears / 90);
            var extraDays = remainingDaysAfterYears % 90;

            var maturityAmount = principal;
            var interest = 0;

            if (interestType === 'QuarterlyCompounded') {
                var n = 4; // Compounded quarterly
                var r = rate / 100; // Convert annual interest rate to decimal

                // **Step 1: Apply compound interest for full years**
                if (totalYears > 0) {
                    maturityAmount = principal * Math.pow(1 + (r / n), n * totalYears);
                    interest = maturityAmount - principal;
                }

                // **Step 2: Apply compound interest for full quarters**
                if (fullQuarters > 0) {
                    maturityAmount *= Math.pow(1 + (r / n), fullQuarters);
                    interest = maturityAmount - principal;
                }

                // **Step 3: Apply simple interest for remaining extra days**
                if (extraDays > 0) {
                    var dailyRate = r / 365; // Convert annual rate to daily rate
                    var extraInterest = maturityAmount * dailyRate * extraDays;
                    interest += extraInterest;
                    maturityAmount += extraInterest;
                }
            } else if (interestType === 'AnnualCompounded') {
                maturityAmount = principal * Math.pow(1 + (rate / 100), totalDays / 365);
                interest = maturityAmount - principal;
            } else if (interestType === 'Fixed') {
                interest = principal * (rate / 100) * (totalDays / 365);
                maturityAmount += interest;
            }

            $('#renew_interestamount').val(Math.round(interest));
            $('#renew_maturityamount').val(Math.round(maturityAmount));

        }

        function maturityAmount() {
            let principal_amount = parseFloat($('#renew_amount').val()) || 0;
            let interest_amount = parseFloat($('#renew_interestamount').val()) || 0;
            let maturityAmount = parseFloat(principal_amount) + parseFloat(interest_amount);
            $('#renew_maturityamount').val(Math.round(maturityAmount));
        }


        $(document).ready(function() {
            // **Optimized Event Listeners**
            $("#year, #month, #days, #fd_amount, #intrestrate").on('input', calculateInterestAmount);
            $("#renew_year, #renew_month, #renew_days, #renew_maturityamount, #renew_intrestrate").on('input',
                renewcalculateInterestAmount);
            $("#intresttype").on('change', calculateInterestAmount);
            $('#renew_amount,#renew_interestamount').on('input', maturityAmount);



            $('#bankfdform').validate({
                rules: {
                    txdate: {
                        required: true
                    },
                    fd_accountno: {
                        required: true
                    },
                    fd_amount: {
                        required: true,
                        digits: true
                    },
                    intrestrate: {
                        required: true
                    },
                    intresttype: {
                        required: true
                    },

                },
                messages: {
                    txdate: {
                        required: 'Enter Valid Date DD-MM-YYYY'
                    },
                    fd_accountno: {
                        required: 'Enter FD Account Number'
                    },
                    fd_amount: {
                        required: 'Enter FD Amount',
                        digits: 'Enter Only Numeric Value'
                    },
                    intrestrate: {
                        required: 'Enter Rate of Interest'
                    },
                    intresttype: {
                        required: 'Select Interest Type'
                    },
                    groupType: {
                        required: 'Select Group Type'
                    },
                    ledgerType: {
                        required: 'Select Ledger Type'
                    },
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });


            $(document).on('submit', '#bankfdform', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();
                let url = $('#bankfd_id').val() ? "{{ route('bankferupdate') }}" :
                    "{{ route('bankfdinsert') }}";

                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            if (res.status === 'success') {
                                $('#bankfdform')[0].reset();
                                notify(res.messages, 'success');
                                window.location.href = "{{ route('bankfdindex') }}";
                            } else {
                                notify(res.messages, 'warning');
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        notify(res.messages, 'warning');
                    }
                });
            });

            $(document).on('click', '.editfd', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('bankfdedit') }}",
                    type: 'post',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let existsId = res.existsId;

                            if (existsId) {
                                $('#bankfd_id').val(existsId.id);
                                $('#txdate').val(DateFormat(existsId.fd_date));
                                $('#fd_number').val(existsId.fd_no);
                                $('#fd_accountno').val(existsId.fd_account);
                                $('#bankaccountfd').val(existsId.bank_fd_type);
                                $('#fd_amount').val(existsId.principal_amount);
                                $('#intresttype').val(existsId.interest_type);
                                $('#intrestfrom').val(DateFormat(existsId.int_start_from));
                                $('#intrestrate').val(existsId.interest_rate);
                                $('#year').val(existsId.year);
                                $('#month').val(existsId.month);
                                $('#days').val(existsId.days);
                                $('#interestamount').val(existsId.interest_amount);
                                $('#maturityamount').val(existsId.maturity_amount);
                                $('#maturitydate').val(DateFormat(existsId.maturity_date));
                                $('#groupType').val(existsId.payment_group).change();
                                $('#ledgerType').val(existsId.payment_ledger);
                            } else {
                                $('#bankfd_id').val('');
                                $('#txdate').val('');
                                $('#fd_number').val('');
                                $('#fd_accountno').val('');
                                $('#bankaccountfd').val('');
                                $('#fd_amount').val('');
                                $('#intresttype').val('');
                                $('#intrestfrom').val('');
                                $('#intrestrate').val('');
                                $('#year').val('');
                                $('#month').val('');
                                $('#days').val('');
                                $('#interestamount').val('');
                                $('#maturityamount').val('');
                                $('#maturitydate').val('');
                                $('#groupType').val('');
                                $('#ledgerType').val('');
                            }

                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        notify(res.messages, 'warning');
                    }
                });
            });

            $(document).on('click', '.deletefd', function(event) {
                event.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: `You are about to delete transaction ID #${id}. This action cannot be undone!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#ledgerModal').modal('hide');

                        Swal.fire({
                            title: "Deleting...",
                            text: "Please wait while we delete the transaction.",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        $.ajax({
                            url: "{{ route('deletebankfds') }}",
                            type: 'POST',
                            data: {
                                id: id,
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                if (res.status === 'success') {
                                    // Close the loading modal
                                    swal.close();
                                    window.location.href =
                                    "{{ route('bankfdindex') }}";

                                } else {
                                    swal.close();
                                    notify(res.messages, 'warning');
                                }
                            },
                            error: function(jqXHR, textStatus) {

                                if (textStatus === "timeout") {
                                    Swal.fire({
                                        title: "Timeout!",
                                        text: "The server is taking too long to respond. Please try again later.",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: "Something went wrong. Please try again.",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.maturefd', function(event) {
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('bankfdmature') }}",
                    type: 'post',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'status') {

                        } else {

                        }
                    },
                    error: function(xhr, status, error) {
                        notify(res.messages, 'warning');
                    }
                });
            });

            $(document).on('click', '.renewfd', function(event) {
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('getdatabankfdrenew') }}",
                    type: 'post',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let existsId = res.existsId;

                            $('#fd_mature_amount').val(existsId.maturity_amount);
                            $('#old_interest_amount').val(existsId.interest_amount);
                            {{--  $('#tds_percentage').val();  --}}
                            $('#tds_amount').val();



                            if (existsId) {
                                $('#renew_bankfd_id').val(existsId.id);
                                $('#renew_txdate').val(DateFormat(existsId.maturity_date));
                                $('#renew_number').val(existsId.fd_no);
                                $('#renew_fd_accountno').val(existsId.fd_account);
                                $('#bankaccountfd').val(existsId.bank_fd_type);
                                $('#renew_amount').val(existsId.principal_amount);
                                $('#renew_intresttype').val(existsId.interest_type);
                                $('#received_intt').val(existsId.interest_amount);
                                $('#renew_intrestfrom').val(DateFormat(existsId.maturity_date));
                                $('#MatureModaldata').modal('show');

                                {{--  $('#renew_intrestfrom').val(DateFormat(existsId.int_start_from));  --}}
                                {{--  $('#intrestrate').val(existsId.interest_rate);
                            $('#year').val(existsId.year);
                            $('#month').val(existsId.month);
                            $('#days').val(existsId.days);
                            $('#interestamount').val(existsId.interest_amount);
                            $('#maturityamount').val(existsId.maturity_amount);
                            $('#maturitydate').val(DateFormat(existsId.maturity_date));
                            $('#groupType').val(existsId.payment_group).change();
                            $('#ledgerType').val(existsId.payment_ledger);  --}}
                            } else {
                                $('#bankfd_id').val('');
                                $('#txdate').val('');
                                $('#renew_number').val('');
                                $('#renew_fd_accountno').val('');
                                $('#bankaccountfd').val('');
                                $('#renew_amount').val('');
                                $('#renew_intresttype').val('');
                                $('#received_intt').val('');
                                $('#renew_intrestfrom').val('');

                                {{--  $('#renew_intrestfrom').val('');  --}}
                            }
                            {{--  $('#intrestrate').val('');  --}}
                            {{--  $('#year').val('');
                            $('#month').val('');
                            $('#days').val('');
                            $('#interestamount').val('');
                            $('#maturityamount').val('');
                            $('#maturitydate').val('');
                            $('#groupType').val('');
                            $('#ledgerType').val('');
                        }  --}}

                        } else {

                        }
                    },
                    error: function(xhr, status, error) {
                        notify(res.messages, 'warning');
                    }
                });
            });

            $(document).on('click', '.closebtn', function(event) {
                event.preventDefault();
                $('#bankfdrenew')[0].reset();
                $('#MatureModaldata').modal('hide');
            });

            $(document).on('submit', '#bankfdrenew', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();
                let url = $('#re_id').val() ? "{{ route('bankfdrenewupdate') }}" :
                    "{{ route('bankfdrenew') }}";

                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            if (res.status === 'success') {
                                $('#bankfdrenew')[0].reset();
                                notify(res.messages, 'success');
                                window.location.href = "{{ route('bankfdindex') }}";
                            } else {
                                notify(res.messages, 'warning');
                            }
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        notify(res.messages, 'warning');
                    }
                });
            });


            {{--  $(document).on('click','.unmature',function(event){
            event.preventDefault();
            let id = $(this).data('id');

            $.ajax({
                url : "{{ route('getdatabankfdunmature') }}",
                type : 'post',
                data : {id : id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){


                    }else{

                    }
                },error : function(xhr,status,error){
                    notify(res.messages,'warning');
                }
            });
        });  --}}





        });
    </script>
@endpush
