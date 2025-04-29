@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Saving Interest Calculation</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="interestcalculationForm" name="interestcalculationForm">
                            <div class="row">
                                @php
                                    $currentDate =
                                        Session::get('currentdate') ??
                                        date('d-m-Y', strtotime(session('sessionStart')));
                                @endphp
                                <div class="col-md-2 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="Date from" class="form-label">Date from</label>
                                    <input type="text" class="form-control formInputsReport" id="date_from"
                                        name="date_from" value="{{ date('d-m-Y', strtotime(session('sessionStart'))) }}" />
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="Till Date" class="form-label">Till Date</label>
                                    <input type="text" class="form-control formInputsReport" id="date_till_date"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}"
                                        name="date_till_date" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">ROI %</label>
                                    <input type="text" class="form-control formInputs" id="rate_of_intt"
                                        name="rate_of_intt" placeholder="Rate of Interest" required />
                                </div>

                                <div class="col-md-2 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">Account No</label>
                                    <input type="text" class="form-control formInputs" id="account_no" name="account_no"
                                        placeholder="Account No" autocomplete="off" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">Minimum Amt.</label>
                                    <input type="text" class="form-control formInputs" id="minimum_amount"
                                        name="minimum_amount" placeholder="Min. Amount" autocomplete="off" />
                                </div>

                            </div>

                            <div class="row gap-0">
                                <div class="col-md-2 col-sm-4 col-6 py-3 saving_column inputesPaddingReport">
                                    <label for="Till Date" class="form-label">Paid Date</label>
                                    <input type="text" class="form-control formInputsReport" id="paid_date"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" name="paid_date" />
                                </div>

                                <div class="col-md-2 col-sm-4 mt-4 col-6 py-3 inputesPaddingReport">
                                    <button type="button"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom me-2"
                                        id="viewdatabooksdetails">
                                        View
                                    </button>
                                    <button type="button" class="btn btn-primary print-button reportSmallBtnCustom me-2" onclick="printReport()"> Print </button>
                                </div>

                                <div class="col-md-2 col-sm-4 mt-4 col-6 py-3 saving_column inputesPaddingReport" id="deletebutton"
                                    style="display: none;">
                                    <button type="submit"
                                        class="btn btn-danger waves-effect waves-light reportSmallBtnCustom">
                                        Delete Ineterest
                                    </button>
                                </div>

                                <div class="col-md-2 col-sm-4 mt-4 col-6 py-3 saving_column inputesPaddingReport" id="paidbutton"
                                    style="display: none;">
                                    <button type="submit"
                                        class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                        id="submitbutton">
                                        Pay Interest
                                    </button>

                                </div>



                                {{--  <div class="col-lg-12 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">  --}}
                                {{--  <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">  --}}

                                {{--  <div class="row">
                                        <div class="col-md-4">
                                            <button type="button"
                                                class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                                id="viewdatabooksdetails">
                                                View
                                            </button>  --}}

                                {{--  <a type="button" href="{{ route('balancebookPrint.print') }}" target="_blank"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </a>  --}}




                                {{--  </div>
                                        <div class="col-md-4">
                                            <div class="" id="paidbutton" style="display: none;">
                                                <button type="submit"
                                                    class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                                    id="submitbutton">
                                                    Pay Interest
                                                </button>
                                            </div>

                                            <div class="col-md-4" id="deletebutton" style="display: none;">
                                                <div class="">
                                                    <button type="submit"
                                                        class="btn btn-danger waves-effect waves-light reportSmallBtnCustom">
                                                        Delete Ineterest
                                                    </button>
                                                </div>
                                            </div>  --}}

                                {{--  </div>  --}}
                                {{--  <div class="ms-2 dropdown">  --}}
                                {{--  <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                More
                                            </button>  --}}
                                {{--  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i
                                                            class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i
                                                            class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="share()"><i
                                                            class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                            </ul>  --}}
                                {{--  </div>
                                        </div>
                                    </div>
                                </div>  --}}
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
                                <th class="fw-bold">Sr No</th>
                                <th class="fw-bold">Account No</th>
                                {{--  <th class="fw-bold">Date</th>  --}}
                                <th class="fw-bold">Member Name</th>
                                <th class="fw-bold">Opening Bal</th>
                                <th class="fw-bold">Interest</th>
                                <th class="fw-bold">Closing Bal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-secondary-subtle" style="background-color: white !important;" id="tbody">
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="3">Grand Total</td>
                                <td id="totalamount">0</td>
                                <td id="totalinterest">0</td>
                                <td id="total">0</td>
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
         function printReport() {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            let printContents = document.getElementById('sharelistprint').innerHTML;

            let css = `
                <style>
                    body { margin: 10px; font-family: Arial, sans-serif; zoom: 90%;
                        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    #sharelistprint {
                        display: flex;
                        justify-content: space-between;
                        width: 100%;
                    }
                    .cards {
                        width: 48%;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 10px;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 6px;
                        text-align: left;
                        font-size: 12px;
                    }
                    .t-heading {
                        font-weight: bold;
                        text-align: center;
                        font-size: 14px;
                    }
                    .grand-total {
                        font-weight: 900 !important;
                        color: #000000 !important;
                        font-size: 16px !important;
                    }
                    h4, h6 {
                        margin: 2px 0;
                        text-align: center;
                    }
                    .table th, .table td {
                        vertical-align: middle !important; /* Ensures that the text is centered vertically in the table cells */
                    }
                </style>`;

                        let header = `
                <div style="text-align: center; margin-bottom: 10px;">
                    <h4>{{ $branch->name }}</h4>
                    <h6>{{ $branch->address }}</h6>
                    <h6>Day Book from ${startDate} to ${endDate}</h6>
                </div>`;

            let newWindow = window.open('', '_blank');
            newWindow.document.write('<html><head><title>Print</title>' + css + '</head><body>');
            newWindow.document.write(header);
            newWindow.document.write('<div id="sharelistprint">' + printContents + '</div>');
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            newWindow.close();
        }
        function getineterestcalculations() {
            let dateFrom = $('#date_from').val();
            let dateTo = $('#date_till_date').val();
            let memberType = $('#memberType').val();
            let rate_of_intt = $('#rate_of_intt').val();
            let account_no = $('#account_no').val();
            let minimum_amount = $('#minimum_amount').val();

            $('#deletebutton').css('display', 'block');
            let data = {
                date_from: dateFrom,
                date_till_date: dateTo,
                member_type: memberType,
                rate_of_intt: rate_of_intt,
                account_no: account_no,
                minimum_amount: minimum_amount
            };

            $.ajax({
                url: "{{ route('getsavinginterestcaluclation') }}",
                type: 'post',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let balances = res.balances;

                        $('#paidbutton').css('display', 'block');
                        $('#deletebutton').css('display', 'block');


                        let tableBody = $('#tbody');
                        tableBody.empty();

                        let closingbalance = 0;
                        let rowNumber = 1;

                        let grnadTotal = 0;
                        let grnadTotalInterest = 0;
                        let grnadTotalSavingInterest = 0;
                        let interest = 0;
                        if (balances && balances.length > 0) {
                            balances.forEach((data) => {
                                let openingbalance = parseFloat(data.net_amount);
                                let interest_amount = parseFloat(data.interest_amount);

                                if (interest_amount > 0 || interest_amount > 0) {
                                    closingbalance += openingbalance + interest_amount;
                                    interest = interest_amount;
                                }

                                if (data.last_balance > 0) {
                                    let row = `<tr>
                                    <td>${rowNumber}</td>
                                    <td>${data.membershipnumber}</td>
                                    <td>${data.name}</td>
                                    <td>${data.amount}</td>
                                    <td>${interest}</td>
                                    <td>${data.net_amount}</td>
                                </tr>`;
                                    tableBody.append(row);
                                    rowNumber++;
                                }

                                grnadTotal += data.last_balance;
                                grnadTotalInterest += data.interest_amount;
                                grnadTotalSavingInterest += data.net_amount;

                            });
                            $('#totalamount').text(grnadTotal);
                            $('#totalinterest').text(grnadTotalInterest);
                            $('#total').text(grnadTotalSavingInterest);

                        } else {
                            $('#totalamount').text(0);
                            $('#totalinterest').text(0);
                            $('#total').text(0);
                            $('#deletebutton').css('display', 'block');

                            let row = `<tr><td colspan="7">No data available</td></tr>`;
                            tableBody.append(row);
                        }
                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }


        $(document).ready(function() {
            $(document).on('change', '#memberType', function(event) {
                event.preventDefault();

                let memberType = $('#memberType').val();

                $('#rate_of_intt').val('');
                $('#account_no').val('');
                $('#minimum_amount').val('');
            });

            {{--  $('#deletebutton').css('display', 'block');  --}}



            $('#viewdatabooksdetails').on('click', function() {
                getineterestcalculations();
                if ($('#interestcalculationForm').valid()) {}
            });

            $('#interestcalculationForm').validate({
                rules: {
                    date_from: {
                        required: true
                    },
                    date_till_date: {
                        required: true
                    },
                    memberType: {
                        required: true
                    },
                    rate_of_intt: {
                        required: true,
                        digits: true
                    },
                    paid_date: {
                        required: true
                    },
                },
                messages: {
                    date_from: {
                        required: 'Enter Date From'
                    },
                    date_till_date: {
                        required: 'Enter Date To'
                    },
                    memberType: {
                        required: 'Select Type'
                    },
                    rate_of_intt: {
                        required: 'Enter Rate Of Interest',
                        digits: 'Enter Only Digits'
                    },
                    paid_date: {
                        required: 'Enter Paid Date' // Corrected this message
                    },
                },
                errorElement: 'p', // You can also change this to 'span' or any other element
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });



            $(document).on('submit', '#interestcalculationForm', function(event) {
                event.preventDefault(); // Prevents the default form submission

                if ($(this).valid()) {
                    let formData = $(this).serialize(); // Serialize the form data

                    // Disable the submit button to prevent multiple submissions
                    $('button[type=submit]').prop('disabled', true);

                    // AJAX request to submit the form
                    $.ajax({
                        url: "{{ route('paidsavinginterest') }}", // The route to handle the form submission
                        type: 'POST', // POST request
                        data: formData, // Serialized form data
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Include CSRF token
                        },
                        dataType: 'json', // Expect a JSON response
                        success: function(res) {
                            // Re-enable the submit button
                            $('button[type=submit]').prop('disabled', false);

                            if (res.status === 'success') {
                                // If the response is successful, show a success notification
                                setTimeout(function() {
                                    notify(res.messages, 'success');
                                    // Redirect to the interest calculation index page after a short delay
                                    window.location.href =
                                        "{{ route('interestcalculationindex') }}";
                                }, 500);
                            } else {
                                // Show a warning if the response status is not 'success'
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle any errors during the AJAX request (e.g., network issues)
                            $('button[type=submit]').prop('disabled', false);
                            notify("An error occurred. Please try again.", 'error');
                        }
                    });
                }
            });


            $(document).on('click', '#deletebutton', function(event) {
                event.preventDefault();


                let paid_date = $('#paid_date').val();
                let memberType = $('#memberType').val();

                $('button[type=submit]').prop('disabled', true);


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
                            url: "{{ route('deletepaidinterest') }}",
                            type: 'post',
                            data: {
                                paid_date: paid_date,
                                memberType: memberType
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    $('button[type=submit]').prop('disabled', false);
                                    setTimeout(function() {
                                        notify(res.messages, 'success');
                                        window.location.href =
                                            "{{ route('interestcalculationindex') }}";
                                    }, 500);
                                } else {
                                    notify(res.messages, 'warning');
                                }
                            },
                            error: function(xhr, status, error) {
                                swal.close();
                                console.error('AJAX Error:', error);
                                swal({
                                    title: 'Error',
                                    text: 'An error occurred while trying to delete the transaction. Please try again later.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
