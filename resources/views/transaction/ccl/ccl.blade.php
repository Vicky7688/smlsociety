@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span>Open Cash Credit Limit</h4>
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

                                {{--  <h4 style="text-align: center; background-color:#7367f0; color:white; font-weight:700; font-size:20px;" class="form-control"><u>Cash Credit Limit</u></h4>  --}}

                                <div class="tab-content tableContent mt-2" id="myTabsContent">
                                    <div class="tab-pane fade show active" id="rd_details" role="tabpanel"
                                        aria-labelledby="rd-details-tab">
                                        <!-- Content for Account Details tab -->
                                        <form id="cclform">
                                            <div class="rd_details-modern">
                                                <div class="rd_details_inner">
                                                    <div class="row">
                                                        <input type="text" hidden name="account_number"
                                                            id="account_number">
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3saving_column">
                                                            <label class="form-label" for="opening date">DATE</label>
                                                            <input type="text" id="opening_date" name="opening_date"
                                                                value="{{ Session::get('currentdate') }}"
                                                                class="form-control form-control-sm mydatepic valid"
                                                                oninput="endDatecalculate('this')">
                                                        </div>

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="Member Type">MEMBER TYPE</label>
                                                            <select name="member_type" id="member_type"
                                                                class="form-select form-select-sm"
                                                                onchange="memberType('this')">
                                                                <option value="Member">Member</option>
                                                                <option value="Staff">Staff</option>
                                                                <option value="NonMember">Nominal Member</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="account_no_label">Mem No.</label>
                                                            <input type="text" id="member_no" name="member_no"
                                                                class="form-control form-control-sm"
                                                                onkeyup="getmemberlist(this)" autocomplete="off">
                                                            <div id="accountList" class="accountList"></div>
                                                        </div>

                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                            <label class="form-label" for="Member Type">Loan Agant</label>
                                                            <select name="loantype" id="loantype"
                                                                class="form-select form-select-sm readonly"
                                                                onchange="getdepositType('this')">
                                                                <option value=""selected>Select Loan Type</option>
                                                                <option value="FD">FD</option>
                                                                <option value="RD">RD</option>
                                                                <option value="DailyDeposit">Daily Deposit</option>

                                                            </select>
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

        <div class="modal fade" id="cclAdvancementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">CCL Limit Advancement</h5>
                        {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body">
                        <form id="ccladvancementForm" name="ccladvancementForm">
                            <div class="row">
                                <input type="hidden" name="cclId" id="cclId">
                                <input type="hidden" name="cclmember" id="cclmember">
                                <input type="hidden" name="ccltype" id="ccltype">
                                <input type="hidden" name="depositids" id="depositids">
                                <input type="hidden" name="cclupdateId" id="cclupdateId">

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Loan Disbursement Date</label>
                                    <input id="transcationDate" type="text" name="transcationDate"
                                        class="form-control form-control-sm mydatepic valid" placeholder="DD-MM-YYYY" value="{{ date('d-m-Y') }}"
                                        oninput="endDatecalculate('this')" />
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Membership No</label>
                                    <input type="text" name="ccl_memnumber" id="ccl_memnumber" readonly
                                        class="form-control form-control-sm " autocomplete="off">
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Deposited Amount</label>
                                    <input type="text" name="deposit_amount" id="deposit_amount" readonly
                                        value="0" class="form-control form-control-sm " autocomplete="off">
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">SOD A/c</label>
                                    <input type="text" name="ccl_acc_no" id="ccl_acc_no" class="form-control form-control-sm "
                                        autocomplete="off" onblur="checkalreadyaccount('this')">
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label" for="Member Type">Interest Type</label>
                                    <select name="interest_type" id="interest_type" class="form-select form-select-sm">
                                        @if(!empty($sodDatetails))
                                            @foreach ($sodDatetails as $row)
                                                <option value="{{ $row->id }}">{{ $row->interest_type }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Loan Amount</label>
                                    <input type="text" name="loan_amount" id="loan_amount" value="0"
                                        class="form-control form-control-sm " autocomplete="off">
                                </div>

                                {{--  <div class="col-md-3 pt-2">
                                    <label class="form-label">Year</label>
                                    <input type="text" name="year" id="year" value="0"
                                        class="form-control form-control-sm" autocomplete="off" oninput="endDatecalculate('this')">
                                </div>  --}}

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Month</label>
                                    <input type="text" name="months" id="months" value="0"
                                        class="form-control form-control-sm" autocomplete="off" oninput="endDatecalculate('this')">
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Days</label>
                                    <input type="text" name="days" id="days" value="0"
                                        class="form-control form-control-sm" autocomplete="off" oninput="endDatecalculate('this')">
                                </div>


                                <div class="col-md-3 pt-2">
                                    <label class="form-label">Rate of Intt.%</label>
                                    <input type="text" name="rate_of_interest" id="rate_of_interest"
                                        class="form-control form-control-sm " autocomplete="off">
                                </div>

                                <div class="col-md-3 pt-2">
                                    <label class="form-label">CCL End Date</label>
                                    <input id="end_date" type="text" name="end_date" class="form-control form-control-sm"
                                        placeholder="DD-MM-YYYY" value="{{ date('d-m-Y') }}"
                                        oninput="endDatecalculate('this')" />
                                </div>
                            </div>

                    </div>
                    <hr class="my-3">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger closeadvancementForm">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="viewModal" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">CCL Limit</h5>
                        {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12" id="">
                            <div class="tabledata card tablee">
                                <div class="card-body">
                                    <table class="table datatables-order table-bordered" id="maintables"
                                        style="width:100%;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>SOD A/c</th>
                                                <th>Name</th>
                                                <th>CCL Date</th>
                                                <th>Statue</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailsBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger viewclose" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12" id="rd_table_id" style="display:none;">
            <div class="tabledata card tablee">
                <div class="card-body">
                    <input type="hidden" name="maxinterests" id="maxinterests">
                    <table class="table datatables-order table-bordered" id="maintable" style="width:100%;">
                        <thead class="thead-light">
                            <tr>
                                <th>Sr.No</th>
                                <th>FD/RD Amt.</th>
                                <th>FD/RD/DailyDeposit A/c No</th>
                                <th>Deposit Amount</th>
                                <th>Status</th>
                                <th>Loan Against</th>
                            </tr>
                        </thead>
                        <tbody id="depositlist">
                        </tbody>
                        <tbody id="depositlistbtn">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-5" id="tabless">
            <div class="tabledata card tablee">
                <div class="card-body" style="overflow-x: auto;">
                    <table class="table datatables-order table-bordered" id="datatablesssss" style="width:100%">
                        <thead class="table_head thead-light">
                            <tr>
                                <th>SNO</th>
                                <th>START DATE</th>
                                <th>SOD AC NO</th>
                                <th>CCL AMOUNT</th>
                                <th>Loan Against.</th>
                                <th>ROI%</th>
                                <th>Tenure</th>
                                <th>END DATE</th>
                                <th>STATUS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listtbody">

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
            $('#member_no').val('');
            $('#loantype').empty().append(`
                <option value="" selected>Select Loan Type</option>
                <option value="FD">FD</option>
                <option value="RD">RD</option>
                <option value="DailyDeposit">Daily Deposit</option>
            `);

            $('#datatablesssss tbody').html('<tr><td colspan="10" class="text-center">Not Record Available</td></tr>');

            $('#member_name').text('');

        }

        function getmemberlist(ele) {
            let memberType = $('#member_type').val();
            let memNumber = $(ele).val();

            if (memNumber.length === 0) {
                $('#member_name').text('')
                $('#accountList').empty();
                $('#ccladvancementForm')[0].reset();
                $('#maintable').load(location.href + ' .table');
                return;
            }

            $.ajax({
                url: "{{ route('getcclmebershipnumber') }}",
                type: 'POST',
                data: {
                    memberType: memberType,
                    memNumber: memNumber
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    let accountListDropdown = $('#accountList');
                    accountListDropdown.empty(); // Clear previous results

                    if (res.status === 'success') {
                        let allMemberList = res.allmemberlist;

                        if (Array.isArray(allMemberList) && allMemberList.length > 0) {
                            allMemberList.forEach((data) => {
                                accountListDropdown.append(
                                    `<div class="membernumber" data-id="${data.accountNo}">${data.accountNo}</div>`
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
                },
                error: function() {
                    notify('An error occurred while fetching member accounts.', 'error');
                }
            });
        }

        function checkalreadyaccount() {
            let memberType = $('#member_type').val();
            let memNumber = $('#member_no').val();
            let cclAccount = $('#ccl_acc_no').val();

            $.ajax({
                url: "{{ route('checkalreadyaccount') }}",
                type: 'POST',
                data: {
                    cclAccount: cclAccount,
                    memberType: memberType,
                    memNumber: memNumber
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {

                        notify('This Account Not Taken Any Member', 'success');
                    } else {
                        {{--  $('#viewModal').modal('show');  --}}
                        $('#ccl_acc_no').val('');




                        $('#detailsBody').empty();

                        let exits_account = res.exits_account;
                        if (exits_account) {


                            $('#detailsBody').append(`
                                <tr>
                                    <td>1</td>
                                    <td>${exits_account.cclNo}</td>
                                    <td>${exits_account.name}</td>
                                    <td>${exits_account.ccl_Date}</td>
                                    <td>${exits_account.status}</td>
                                </tr>
                            `);



                            $('#cclAdvancementModal').modal('hide');
                            $('#viewModal').modal('show');
                        } else {
                            $('#viewModal').modal('hide');
                            $('#cclAdvancementModal').modal('show');
                        }

                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        function getdepositType() {

            let loantype = $('#loantype').val();
            let memberType = $('#member_type').val();
            let memNumber = $('#member_no').val();
            let opening_date = $('#opening_date').val();

            $.ajax({
                url: "{{ route('getdepositlist') }}",
                type: 'POST',
                data: {
                    memberType: memberType,
                    memNumber: memNumber,
                    loantype: loantype,
                    opening_date: opening_date
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(res) {

                    let deposit_details = res.deposit_details;
                    let depositType = res.type;
                    let maxinterest = parseFloat(res.maxinterest);
                    let newInterestRate = maxinterest + 1;
                    $('#maxinterests').val(newInterestRate);

                    let depositlistBody = $('#depositlist');
                    depositlistBody.empty();

                    let depositlistbtn = $('#depositlistbtn');
                    depositlistbtn.empty();

                    if (res.status === 'success') {

                        if(depositType){
                            $('#rd_table_id').css('display', 'block');
                            switch (depositType) {
                                case 'FD':

                                    if (Array.isArray(deposit_details) && deposit_details.length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${data.principalAmount}</td>
                                                <td>${data.accountNo}</td>
                                                <td>${data.principalAmount}</td>
                                                <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.principalAmount}"></td>
                                                <td>${data.status}</td>
                                                <td><input type="checkbox" name="depositid" id="depositid" value="${data.id}"></td>
                                            </tr>`;
                                            depositlistBody.append(row);


                                        });
                                        let buttonRow = `
                                            <tr style="border: none !important;">
                                                <td colspan="5" style="border: none !important;"></td>
                                                <td class="text-end-right" style="border: none !important;">
                                                    <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px; " class="btn plugeaccounts">
                                                        Pluge
                                                    </button>
                                                </td>
                                            </tr>`;

                                        depositlistBody.append(buttonRow);



                                    } else {
                                        notify('Their are Not FD Account', 'warning');
                                    }

                                    break;
                                case 'RD':

                                    if (Array.isArray(deposit_details) && deposit_details.length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${data.amount}</td>
                                                    <td>${data.rd_account_no}</td>
                                                    <td>${data.fetchamount}</td>
                                                    <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.fetchamount}"></td>
                                                    <td>${data.status}</td>
                                                    <td><input type="checkbox" name="depositid" id="depositid" value="${data.id}"></td>
                                                </tr>`;
                                            depositlistBody.append(row);

                                        });

                                        let buttonRow = `
                                            <tr style="border: none !important;">
                                                <td colspan="5" style="border: none !important;"></td>
                                                <td class="text-end-right" style="border: none !important;">
                                                    <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px; " class="btn plugeaccounts">
                                                        Pluge
                                                    </button>
                                                </td>
                                            </tr>`;

                                        depositlistBody.append(buttonRow);
                                    } else {
                                        notify('Their are Not FD Account', 'warning');
                                    }

                                    break;
                                default:

                                    if (Array.isArray(deposit_details) && deposit_details.length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${data.amount}</td>
                                                    <td>${data.account_no}</td>
                                                    <td>${data.deposit_amount}</td>
                                                    <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.deposit_amount}"></td>
                                                    <td>${data.status}</td>
                                                    <td><input type="checkbox" name="depositid" id="depositid" value="${data.id}"></td>
                                                </tr>`;
                                            depositlistBody.append(row);

                                        });

                                        let buttonRow = `
                                            <tr style="border: none !important;">
                                                <td colspan="5" style="border: none !important;"></td>
                                                <td class="text-end-right" style="border: none !important;">
                                                    <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px; " class="btn plugeaccounts">
                                                        Pluge
                                                    </button>
                                                </td>
                                            </tr>`;

                                        depositlistBody.append(buttonRow);
                                    } else {
                                        notify('Their are Not FD Account', 'warning');
                                    }
                            }
                        }else {

                            depositlistBody.append(`<tr>
                                <td colspan="6">No Record Found</td>
                            </tr>`);
                        }

                    } else {
                        {{--  $('#rd_table_id').css('display', 'none');  --}}

                          depositlistBody.append(`<tr>
                                <td colspan="6">No Record Found</td>
                            </tr>`);

                        toastr.error(res.messages);
                    }
                }
            });
        }

        function showData(accounts, memberdetail) {
            $('#member_name').text(memberdetail.name);
            let listtbody = $('#listtbody');
            listtbody.empty(); // Clear the table body

            if (Array.isArray(accounts) && accounts.length > 0) {
                accounts.forEach((data, index) => {
                    let dates = new Date(data.ccl_Date);
                    let daysss = dates.getDate();
                    let monthss = dates.getMonth() + 1;
                    let yearss = dates.getFullYear();

                    daysss = daysss < 10 ? `0${daysss}` : daysss;
                    monthss = monthss < 10 ? `0${monthss}` : monthss;
                    let formattedDate = `${daysss}-${monthss}-${yearss}`;

                    let enddates = new Date(data.ccl_end_Date);
                    let edaysss = enddates.getDate();
                    let emonthss = enddates.getMonth() + 1;
                    let eyearss = enddates.getFullYear();

                    edaysss = edaysss < 10 ? `0${edaysss}` : edaysss;
                    emonthss = emonthss < 10 ? `0${emonthss}` : emonthss;
                    let eformattedDate = `${edaysss}-${emonthss}-${eyearss}`;

                    let tenure = [];


                    if (data.year && parseInt(data.year) > 0) {
                        tenure.push(`${data.year}-Y`);
                    }
                    if (data.month && parseInt(data.month) > 0) {
                        tenure.push(`${data.month}-M`);
                    }
                    if (data.days && parseInt(data.days) > 0) {
                        tenure.push(`${data.days}-D`);
                    }
                    if (tenure.length === 0) {
                        tenure = 'N/A';
                    } else {
                        tenure = tenure.join(', ');
                    }

                    let row = `<tr>
                        <td>${index + 1}</td>
                        <td>${formattedDate}</td>
                        <td>${data.cclNo}</td>
                        <td>${data.ccl_amount}</td>
                        <td>${data.Types}</td>
                        <td>${data.interest}</td>
                        <td>${
                            `${tenure}`
                        }</td>
                        <td>${eformattedDate}</td>
                        <td>${data.status}</td>
                        <td style="width:85px;">
                                <button class="btn ccleditbtn p-1" data-id="${data.id}" data-datess="${formattedDate}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn ccldeletebtn p-1" data-id="${data.id}" data-datess="${formattedDate}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>
                    </tr>`;

                    listtbody.append(row); // Append the row to the table body
                });
            }
        }


        function endDatecalculate() {
            {{--  let opening_date = $('#opening_date').val();
            $('#transcationDate').val(opening_date);  --}}

            let transcationDate = $('#transcationDate').val();
            let year = parseInt($('#year').val()) || 0;
            let months = parseInt($('#months').val()) || 0;
            let days = parseInt($('#days').val()) || 0;

            let dateParts = transcationDate.split('-');
            let date = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`);

            date.setFullYear(date.getFullYear() + year);
            date.setMonth(date.getMonth() + months);
            date.setDate(date.getDate() + days);

            let endDate = date.toISOString().split('T')[0];
            let formattedEndDate = endDate.split('-').reverse().join('-');
            $('#end_date').val(formattedEndDate).prop('readonly', true);
        }


        $(document).ready(function() {

            $(document).on('click', '.membernumber', function() {
                let selectedAccount = $(this).data('id');
                let memberType = $('#member_type').val();
                $('#member_no').val(selectedAccount);
                $('#accountList').empty();

                $.ajax({
                    url: "{{ route('getmemberccl') }}",
                    type: 'POST',
                    data: {
                        selectedAccount: selectedAccount,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            let memberdetail = res.memberdetail;
                            let accounts = res.accounts;
                            if (memberdetail) {
                                showData(accounts, memberdetail);
                            } else {
                                notify(res.messages, 'warning');
                                $('#member_name').text('');
                            }
                        } else {
                            $('#member_name').text('');
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });

            $(document).on('click', '.plugeaccounts', function(event) {
                event.preventDefault();

                let loantype = $('#loantype').val();
                let memberType = $('#member_type').val();
                let memNumber = $('#member_no').val();
                let maxinterests = $('#maxinterests').val();
                let selectedIds = [];
                let totalAmount = 0;

                $('input[name="depositid"]:checked').each(function() {
                    let amountField = $(this).closest('tr').find('input[name="deposit_amountss"]');
                    let amount = parseFloat(amountField.val()) || 0;
                    totalAmount += amount;
                    selectedIds.push($(this).val());
                    $('#depositids').val(selectedIds);
                    $('#cclAdvancementModal').modal('show');
                });

                if([maxinterests,memberType,memNumber,loantype,totalAmount]){
                    $('#rate_of_interest').val(maxinterests);
                    $('#cclmember').val(memberType);
                    $('#ccl_memnumber').val(memNumber);
                    $('#ccltype').val(loantype);
                    $('#deposit_amount').val(totalAmount);
                }else{
                    $('#rate_of_interest').val('');
                    $('#cclmember').val('');
                    $('#ccl_memnumber').val('');
                    $('#ccltype').val('');
                    $('#deposit_amount').val('');
                }
            });

            $(document).on('submit', '#ccladvancementForm', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                let url = $('#cclupdateId').val() ? "{{ route('ccladvancementupdate') }}" :
                    "{{ route('ccladvancementinsert') }}";
                {{--  $('#cclupdateId').val(id);  --}}

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#ccladvancementForm')[0].reset();
                            $('#cclAdvancementModal').modal('hide');
                            $('#rd_table_id').css('display', 'none');

                            {{--  $('#loantype').val('').removeClass('readonly').addClass(
                                'readonly-remove');  --}}

                                {{--  $('#loantype').val(depositType).addClass('readonly-select').prop('readonly',true);  --}}

                            $('#depositlist').empty();
                            $('#depositlistbtn').empty();

                            let memberdetail = res.memberdetail;
                            let accounts = res.accounts;

                            if (memberdetail) {
                                showData(accounts, memberdetail);
                            } else {
                                notify(res.messages, 'warning');
                            }

                            notify(res.messages, 'success');
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        notify('An error occurred while processing the request.', 'error');
                    }
                });
            });

            $(document).on('click', '.ccleditbtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                let opening_date = $(this).data('datess');
                $('#cclupdateId').val(id);

                $.ajax({
                    url: "{{ route('editccldetails') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        opening_date: opening_date
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(res) {
                        let deposit_details = res.deposit_details;
                        let depositType = res.type;
                        let exitsId = res.exitsId;

                        let depositlistBody = $('#depositlist');
                        depositlistBody.empty();

                        let depositlistbtn = $('#depositlistbtn');
                        depositlistbtn.empty();


                        $('#loantype').val(depositType).addClass('readonly-select').prop('readonly',true);



                        if (res.status === 'success') {
                            $('#rd_table_id').css('display', 'block');

                            switch (depositType) {
                                case 'FD':
                                    if (Array.isArray(deposit_details) && deposit_details
                                        .length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${data.principalAmount}</td>
                                                    <td>${data.accountNo}</td>
                                                    <td>${data.principalAmount}</td>
                                                    <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.principalAmount}"></td>
                                                    <td>${data.status}</td>
                                                    <td>
                                                        <input type="checkbox" name="depositid" id="depositid_${data.id}" value="${data.id}" ${data.status === 'Pluge' ? 'checked' : ''}>
                                                    </td>
                                                </tr>`;

                                            depositlistBody.append(row);

                                            // Auto-select the checkbox if required
                                            if (data.isSelected) {
                                                $(`#depositid_${data.id}`).prop(
                                                    'checked', true);
                                            }
                                        });

                                        let buttonRow = `
                                        <tr style="border: none !important;">
                                            <td colspan="5" style="border: none !important;"></td>
                                            <td class="text-end-right" style="border: none !important;">
                                                <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px;" class="btn plugeaccounts">
                                                    Pluge
                                                </button>
                                            </td>
                                        </tr>`;
                                        depositlistBody.append(buttonRow);



                                        $('#transcationDate').val(dateFormat(exitsId.ccl_Date));
                                        $('#member_no').val(exitsId.membership).prop('readonly', true);
                                        $('#ccl_acc_no').val(exitsId.cclNo).prop('readonly',true);
                                        $('#loan_amount').val(exitsId.ccl_amount);
                                        $('#year').val(exitsId.year);
                                        $('#months').val(exitsId.month);
                                        $('#interest_type').val(exitsId.interestType);
                                        $('#days').val(exitsId.days);
                                        $('#maxinterests').val(exitsId.interest);
                                        $('#narration').val(exitsId.narration);
                                        $('#end_date').val(dateFormat(exitsId.ccl_end_Date));



                                    } else {
                                        notify('There are no FD Accounts', 'warning');
                                    }
                                    break;

                                case 'RD':
                                    if (Array.isArray(deposit_details) && deposit_details
                                        .length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${data.amount}</td>
                                                    <td>${data.rd_account_no}</td>
                                                    <td>${data.fetchamount}</td>
                                                    <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.fetchamount}"></td>
                                                    <td>${data.status}</td>
                                                    <td>
                                                        <input type="checkbox" name="depositid" id="depositid_${data.id}" value="${data.id}" ${data.status === 'Pluge' ? 'checked' : ''}>
                                                    </td>
                                                </tr>`;

                                            depositlistBody.append(row);

                                            // Auto-select the checkbox if required
                                            if (data.isSelected) {
                                                $(`#depositid_${data.id}`).prop(
                                                    'checked', true);
                                            }
                                        });



                                        let buttonRow = `
                                        <tr style="border: none !important;">
                                            <td colspan="5" style="border: none !important;"></td>
                                            <td class="text-end-right" style="border: none !important;">
                                                <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px;" class="btn plugeaccounts">
                                                    Pluge
                                                </button>
                                            </td>
                                        </tr>`;
                                        depositlistBody.append(buttonRow);


                                        $('#transcationDate').val(dateFormat(exitsId.ccl_Date));
                                        $('#member_no').val(exitsId.membership).prop('readonly',
                                            true);
                                        $('#ccl_acc_no').val(exitsId.cclNo).prop('readonly',
                                            true);
                                        $('#loan_amount').val(exitsId.ccl_amount);
                                        $('#year').val(exitsId.year);
                                        $('#months').val(exitsId.month);
                                        $('#days').val(exitsId.days);
                                        $('#interest_type').val(exitsId.interestType);
                                        $('#maxinterests').val(exitsId.interest);
                                        $('#narration').val(exitsId.narration);
                                        $('#end_date').val(dateFormat(exitsId.ccl_end_Date));




                                    } else {
                                        notify('There are no RD Accounts', 'warning');
                                    }
                                    break;

                                default:
                                    if (Array.isArray(deposit_details) && deposit_details
                                        .length > 0) {
                                        deposit_details.forEach((data, index) => {
                                            let row = `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${data.amount}</td>
                                                <td>${data.account_no}</td>
                                                <td>${data.deposit_amount}</td>
                                                <td hidden><input type="text" name="deposit_amountss" id="deposit_amountss" value="${data.deposit_amount}"></td>
                                                <td>${data.status}</td>
                                                <td>
                                                    <input type="checkbox" name="depositid" id="depositid_${data.id}" value="${data.id}" ${data.status === 'Pluge' ? 'checked' : ''}>
                                                </td>
                                            </tr>`;

                                            depositlistBody.append(row);

                                            // Auto-select the checkbox if required
                                            if (data.isSelected) {
                                                $(`#depositid_${data.id}`).prop(
                                                    'checked', true);
                                            }
                                        });



                                        let buttonRow = `
                                    <tr style="border: none !important;">
                                        <td colspan="5" style="border: none !important;"></td>
                                        <td class="text-end-right" style="border: none !important;">
                                            <button style="background-color: #685dd8; color: white; border: none; padding: 5px 10px;" class="btn plugeaccounts">
                                                Pluge
                                            </button>
                                        </td>
                                    </tr>`;
                                        depositlistBody.append(buttonRow);


                                        $('#transcationDate').val(dateFormat(exitsId.ccl_Date));
                                        $('#member_no').val(exitsId.membership).prop('readonly',
                                            true);
                                        $('#ccl_acc_no').val(exitsId.cclNo).prop('readonly',
                                            true);
                                        $('#loan_amount').val(exitsId.ccl_amount);
                                        $('#year').val(exitsId.year);
                                        $('#months').val(exitsId.month);
                                        $('#days').val(exitsId.days);
                                        $('#interest_type').val(exitsId.interestType);
                                        $('#maxinterests').val(exitsId.interest);
                                        $('#narration').val(exitsId.narration);
                                        $('#end_date').val(dateFormat(exitsId.ccl_end_Date));

                                    } else {
                                        notify('There are no RD Accounts', 'warning');
                                    }
                            }
                        } else {
                            $('#rd_table_id').css('display', 'none');
                            let row = `
                            <tr>
                                <td colspan="6">No Record Found</td>
                            </tr>`;
                            depositlistBody.append(row);

                            $('#ccl_acc_no').val('');
                            $('#loan_amount').val('');
                            $('#year').val('');
                            $('#months').val('');
                            $('#days').val('');
                            $('#rate_of_interest').val('');
                            $('#narration').val('');


                            notify(res.messages, 'warning');
                        }
                    }

                });
            });

            $(document).on('click', '.viewclose', function(event) {
                event.preventDefault();
                $('#cclAdvancementModal').modal('show');
                $('#viewModal').modal('hide');
            });

            $('#viewModal').hide();

            $(document).on('click', '.ccldeletebtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                let opening_date = $(this).data('datess');

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
                        $('#ledgerModal').modal('hide'); // Hide the modal before deletion starts

                        Swal.fire({
                            title: "Deleting...",
                            text: "Please wait while we delete the transaction.",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        $.ajax({
                            url: "{{ route('deletecclaccount') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                opening_date: opening_date
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                if (res.status === 'success') {
                                    let memberdetail = res.memberdetail;
                                    let accounts = res.accounts;

                                    Swal.fire({
                                        title: "Deleted!",
                                        text: res.messages || "The transaction has been successfully deleted.",
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        // Hide the modal again after deletion
                                        $('#cclAdvancementModal').modal('hide');
                                    });

                                    // Refresh the table (assuming your table is inside #maintable)
                                    $('#maintable').load(location.href + ' #maintable');

                                    // Clear the fields if needed
                                    $('#rd_table_id').css('display', 'none');
                                    $('#depositlist').empty();
                                    $('#depositlistbtn').empty();

                                    if (memberdetail) {
                                        showData(accounts, memberdetail);
                                    } else {
                                        notify(res.messages, 'warning');
                                    }
                                } else {
                                    Swal.fire({
                                        title: "Warning!",
                                        text: res.messages || "Deletion failed. Please try again.",
                                        icon: "warning",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                $('.ccldeletebtn').prop('disabled', false);

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






            $(document).on('click','.closeadvancementForm',function(event){
                event.preventDefault();

                $('#ccl_memnumber').val(0);
                $('#deposit_amount').val(0);
                $('#ccl_acc_no').val(0);
                $('#loan_amount').val(0);
                $('#year').val(0);
                $('#months').val(0);
                $('#days').val(0);
                $('#rate_of_interest').val(0);
                $('#cclAdvancementModal').modal('hide');

            });

        });
    </script>
@endpush



@push('style')
    <style>
        button.btn.editbtn,
        button.btn.deletebtn {
            padding: 0 !important;
        }

        .readonly-select {
            pointer-events: none;
            /* Disables interaction with the select dropdown */
            {{--  background-color: #f5f5f5; /* Optional: to make it look disabled */  --}}
        }

        .readonly-remove {
            pointer-events: auto;
            /* Enables interaction with the select dropdown */
            background-color: #f5f5f5;
            /* Optional: visual styling */
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
