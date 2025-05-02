@extends('layouts.app')

@section('content')
    <style>
        /* Regular styles here */
        .bg-success {
            background-color: #d4edda;
            /* Example background color for success */
            padding: 10px;
            margin-bottom: 10px;
        }

        .three-btns {
            width: 100%;
            display: inline-block;
            padding-top: 22px !important;
        }

        @media print {
            .bg-success {
                page-break-after: always;
                /* Ensure a page break after this element */
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Saving List</h4>
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
                                    $currentDate =
                                        Session::get('currentdate') ??
                                        date('d-m-Y', strtotime(session('sessionStart')));
                                @endphp
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                        id="startDate" name="startDate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD"
                                        id="endDate" name="endDate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsReport" id="memberType" name="memberType"
                                        onchange="allSchemes('this')">
                                        <option value="all">All</option>
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="memberType" class="form-label">Scheme Type</label>
                                    <select class="form-select formInputsReport" id="schemeType" name="schemeType">
                                        <option value="all">All</option>
                                        @if (!empty($scheme_names))
                                            @foreach ($scheme_names as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                {{--  <div class="col-lg-5 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">  --}}
                                <div class="col-lg-4 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <div class="three-btns">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>
                                        <!--<a type="button" href="{{ route('savingPrint.print') }}" target="_blank"-->
                                        <!--    class="ms-2 btn btn-primary print-button reportSmallBtnCustom">-->
                                        <!--    Print-->
                                        <!--</a>-->
                                        <button type="button" id="printButton" onclick="printReport()"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </button>
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button"
                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i
                                                        class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i
                                                        class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i
                                                        class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div>
                                </div>
                                {{--  </div>  --}}
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
                                <td id="grandTotal">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="loader"
            style="display:none; position:fixed; top:60%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
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
                var formData = $(this).serialize();
                $('#loader').show();
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('savingList.getData') }}',
                    type: 'get',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#loader').hide();
                        $("button[type=submit]").prop('disabled', false);
                        if (res.status === 'success') {
                            let saving_entries = res.saving_entries;
                            {{--  console.log(saving_entries);  --}}
                            let tableBody = $('#tableBody');
                            tableBody.empty();
                            let balance = 0;
                            let grandTotal = 0;

                            saving_entries.forEach((data, index) => {
                                let opening_amount = parseFloat(data.opening_amount) ?
                                    parseFloat(data.opening_amount) : 0;
                                let deposit_amount = parseFloat(data.total_deposit) ?
                                    parseFloat(data.total_deposit) : 0;
                                let withdraw_amount = parseFloat(data.total_withdraw) ?
                                    parseFloat(data.total_withdraw) : 0;

                                console.log(opening_amount, deposit_amount,
                                    withdraw_amount);

                                balance = opening_amount + deposit_amount -
                                    withdraw_amount;
                                grandTotal += balance;
                                // Only add rows where balance is greater than 0
                                if (balance > 0) {
                                    let row = `<tr>
                                            <td>${(index + 1)}</td>
                                            <td>${data.accountNo ? data.accountNo : data.accountId }</td>
                                            <td>${data.name}</td>
                                            <td>${balance.toFixed(2)}</td>
                                        </tr>`;
                                    tableBody.append(row);

                                }
                            });

                            $('#grandTotal').empty();
                            $('#grandTotal').append(grandTotal.toFixed(2));
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
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();


            // Add header for printing
            var header = `
                <div style="text-align: center;">

                    <h6> Saving List From ` + startDate + ` To ` + endDate + `</h6>

                </div>
            `;


            var css = `
        <style>
            @media print {
            body { background-color: #ffffff; margin-top: .5rem; }

             .bg-success {
            page-break-after: always; /* Ensure a page break after this element */
        }

            }
        </style>
    `;

            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }


        function allSchemes() {
            let memberType = $('#memberType').val();

            $.ajax({
                url: "{{ route('getschemessavinglist') }}",
                type: 'post',
                data: {
                    memberType: memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let schemes = res.schemes;
                        let schemeType = $('#schemeType');
                        schemeType.empty();

                        if (schemes && schemes.length > 0) {
                            schemes.forEach((data) => {
                                schemeType.append(`<option value="${data.id}">${data.name}</option>`);
                            });
                        } else {
                            schemeType.append(`<option value="All">All</option>`);
                        }

                    } else {
                        toastr.error(res.messages);
                    }
                },
                error: function(err) {
                    toastr.error('Error fetching schemes. Please try again.');
                }
            });
        }
    </script>
@endpush
