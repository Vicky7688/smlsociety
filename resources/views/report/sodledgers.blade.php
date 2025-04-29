@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Report / </span>SOD Ledgers</h4>
                    </div>
                    <div class="col-md-3 accountHolderDetails">
                        <h6 class=""><span class="text-muted fw-light">Name: </span><span id="member_name"></span></h6>
                        {{--  <h6 class="pt-2"><span class="text-muted fw-light">Father Name: </span><span id="member_fathername"></span></h6>  --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardHeadingTitle">
                        <div class="row">
                            <div class="col-12">
                                <div class="tab-content tableContent mt-2" id="myTabsContent">
                                    <div class="tab-pane fade show active" id="rd_details" role="tabpanel"
                                        aria-labelledby="rd-details-tab">
                                        <!-- Content for Account Details tab -->
                                        <form id="sodform">
                                            <div class="rd_details-modern">
                                                <div class="rd_details_inner">
                                                    <div class="row">
                                                        <input type="text" hidden name="account_number"
                                                            id="account_number">
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3saving_column">
                                                            <label class="form-label" for="opening date">DATE</label>
                                                            <input type="text" id="opening_date" name="opening_date"
                                                                value="{{ Session::get('currentdate') }}"
                                                                class="form-control transactionDate valid form-select-sm">
                                                        </div>

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="Member Type">MEMBER TYPE</label>
                                                            <select name="member_type" id="member_type"
                                                                class="form-select form-select-sm"
                                                                onchange="memberType(this)">
                                                                <option value="Member">Member</option>
                                                                <option value="Staff">Staff</option>
                                                                <option value="NonMember">Nominal Member</option>
                                                            </select>
                                                        </div>

                                                        <input type="hidden" class="membershipnumbers">
                                                        <input type="hidden" name="sodid" id="sodid">

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="account_no_label">SOD A/c</label>
                                                            <input type="text" id="soc_account" name="soc_account"
                                                                class="form-control form-control-sm"
                                                                onkeyup="getmemberlist(this)" autocomplete="off">
                                                            <div id="accountList" class="accountList"></div>
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
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-1" id="tabless">
            <div class="tabledata card tablee">
                <div class="card-body" style="overflow-x: auto;">
                    <table class="table datatables-order table-bordered" id="datatabless" style="width:100%">
                        <thead class="table_head thead-light">
                            <tr>
                                <th>Sr.No</th>
                                <th>Month</th>
                                <th>SOD Withraw.</th>
                                <th>RECEIVED SOD</th>
                                <th>RECEIVED INTEREST</th>
                                <th>RECOVERABLE INTEREST</th>
                                <th>BALANCE</th>
                            </tr>
                        </thead>
                        <tbody id="accountTbody">
                            <tr>
                                <td colspan="7">No Record Available</td>
                            </tr>
                        </tbody>
                        <tbody id="grantotalbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('script')
    <script>
        function dateFormat(date) {
            let dates = new Date(date);
            let daysss = dates.getDate();
            let monthss = dates.getMonth() + 1;
            let yearss = dates.getFullYear();

            daysss = daysss < 10 ? `0${daysss}` : daysss;
            monthss = monthss < 10 ? `0${monthss}` : monthss;
            let formattedDate = `${daysss}-${monthss}-${yearss}`;
            return formattedDate;
        }


        function memberType() {
            $('#soc_account').val('');
            $('#datatabless tbody').html('<tr><td colspan="6" class="text-center">Not Record Available</td></tr>');
            $('#member_name').text('');
        }

        function getmemberlist(ele) {
            let memberType = $('#member_type').val();
            let sod_account = $(ele).val();
            let openingDate = $('#opening_date').val();

            if (sod_account.length === 0) {
                $('#member_name').text('')
                $('#accountList').empty();
                $('#sodform')[0].reset();
                $('#datatabless').load(location.href + ' .table');
                return;
            }

            $.ajax({
                url: "{{ route('getsodaccountlist') }}",
                type: 'POST',
                data: {
                    memberType: memberType,
                    sod_account: sod_account,
                    openingDate: openingDate
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    let accountListDropdown = $('#accountList');
                    accountListDropdown.empty();

                    if (res.status === 'success') {
                        let allMemberList = res.sodaccounts;

                        if (Array.isArray(allMemberList) && allMemberList.length > 0) {
                            allMemberList.forEach((data) => {
                                accountListDropdown.append(
                                    `<div class="membernumber" data-id="${data.cclNo}">${data.cclNo}</div>`
                                );
                            });
                        } else {
                            accountListDropdown.append(`<div class="membernumber">No Account Found</div>`);
                            notify(res.message || 'No accounts available.', 'warning');
                        }
                    } else {
                        accountListDropdown.append(`<div class="membernumber">No Account Found</div>`);
                        notify(res.message || 'Error retrieving accounts.', 'warning');
                    }
                    $('#datatabless').find('table').css('border-collapse', 'collapse');
                },
                error: function() {
                    notify('An error occurred while fetching member accounts.', 'warning');
                }
            });
        }






        $(document).on('click', '.membernumber', function(event) {
            event.preventDefault();
            let openingDate = $('#opening_date').val();
            let sodaccount = $(this).data('id');
            let memberType = $('#member_type').val();
            $('#member_no').val(sodaccount);
            $('#accountList').empty();

            $.ajax({
                url: "{{ route('getsodacc') }}",
                type: 'post',
                data: { sodaccount: sodaccount, memberType: memberType, openingDate: openingDate},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function(res) {
                    if (res.status === "success") {
                        let sodDetails = res.allData;
                        let opening_amount = 0; // Initialize opening amount (default or from previous data)
                        let interestrecoverable = 0;
                        let closing = 0;

                        let grandtotalwithdraw = 0;
                        let grandtotalreceived = 0;
                        let grandtotalinterest = 0;
                        let grandtotalinterestrecoverable = 0;
                        let grandtotal = 0;

                        let getnames = res.getnames;
                        $('#member_name').text(getnames.name);


                        if (Array.isArray(sodDetails) && sodDetails.length > 0) {
                            $('#accountTbody').empty(); // Clear the table body

                            sodDetails.forEach((data, index) => {
                                // Safely parse numeric values
                                let withdraw = parseFloat(data.total_withdraws) || 0;
                                let total_recovey = parseFloat(data.total_recovey) || 0;
                                let interest_received = parseFloat(data.interest_received) || 0;
                                let days_in_month = parseFloat(data.days_in_month) || 0;
                                let rateofinterest = parseFloat(data.rateOfInterest) || 0;

                                if (index === 0) {
                                    opening_amount = parseFloat(withdraw) - total_recovey;
                                }

                                interestrecoverable = Math.round((((opening_amount * rateofinterest) * days_in_month) / 100) / 365);
                                closing = opening_amount + interestrecoverable - total_recovey - interest_received;

                                let row = `<tr>
                                    <td>${index + 1}</td>
                                    <td>${data.month}</td>
                                    <td>${withdraw.toFixed(2)}</td>
                                    <td>${total_recovey.toFixed(2)}</td>
                                    <td>${interest_received.toFixed(2)}</td>
                                    <td>${interestrecoverable.toFixed(2)}</td>
                                    <td>${closing.toFixed(2)}</td>
                                </tr>`;

                                $('#accountTbody').append(row);

                                opening_amount = closing;

                                grandtotalwithdraw += withdraw;
                                grandtotalreceived += total_recovey;
                                grandtotalinterest += interest_received;
                                grandtotalinterestrecoverable += interestrecoverable;
                                grandtotal = closing;

                            });

                            $('#grantotalbody').append(`<tr style="background-color: #7367f0;">
                                <td colspan='2'  style="color: white;">Grand Total</td>
                                <td  style="color: white;">${grandtotalwithdraw.toFixed(2)}</td>
                                <td  style="color: white;">${grandtotalreceived.toFixed(2)}</td>
                                <td  style="color: white;">${grandtotalinterest.toFixed(2)}</td>
                                <td  style="color: white;">${grandtotalinterestrecoverable.toFixed(2)}</td>
                                <td  style="color: white;">${grandtotal.toFixed(2)}</td>
                            </tr>`);

                        } else {
                            $('#accountTbody').empty().append(`<tr><td colspan="7" class="text-center">No Data Available</td></tr>`);
                            notify(res.messages, 'warning');
                        }
                    } else {
                        notify(res.messages, 'warning');
                    }



                },
                error: function(xhr, error, status) {
                    notify('An error occurred while fetching member accounts.', 'warning');
                }
            });
        });
    </script>
@endpush



@push('style')
    <style>
        /* Make the modal content scrollable */
        .modal-dialog-scrollable .modal-content {
            max-height: 80vh;
            /* 90% of the viewport height */
            overflow: hidden;
        }

        /* Scrollable table body */
        #ledgersbody {
            max-height: 300px;
            /* Adjust height as needed */
            overflow-y: auto;
            padding: 1px 0px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 1px 0px;
        }

        /* Add some padding and spacing for the modal */
        .modal-body {
            padding: 1.5rem;
        }

        .modal-header {
            border-bottom: 2px solid #dee2e6;
        }

        .modal-footer {
            border-top: 2px solid #dee2e6;
        }

        /* Add some hover effect to the rows */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Style for table header */
        .table-dark th {
            background-color: #7367f0 !important;
            color: white;

        }


        .swal2-container {
            z-index: 1060 !important;
            /* Ensure SweetAlert always appears above Bootstrap modals */
        }

        button.btn.editbtn,
        button.btn.deletebtn {
            padding: 0 !important;
        }

        .pt-3 {
            padding-top: 1.5rem !important;
        }

        .clickable-cell {
            color: #7367f0 !important;
        }

        .saving_column {
            position: relative;
        }

        .saving_column .error {

            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }

        .accountList {
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
            padding-left: 11px;
        }

        .accountHolderDetails {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .thead-light tr th {
            background-color: #7367f0;
            color: white !important;
        }

        .form-label {
            text-transform: capitalize;
        }


    </style>
@endpush
