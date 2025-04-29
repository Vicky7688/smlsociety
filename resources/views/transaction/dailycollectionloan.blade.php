
@extends('layouts.app')
@section('content')
<style>
    .scroll-table {
        display: block;
        width: 100%;
        overflow-y: auto;
    }

    .form-label {
        text-transform: capitalize;
    }

</style>
<div class="container-xxl flex-grow-1 container-p-y">


    <!-- Tab content --> 
        <!-- Transaction tab -->
        <div class="tab-pane fade show active" id="transaction" role="tabpanel" aria-labelledby="transaction-tab">
            <h4 class="py-2"><span class="text-muted fw-light">Transaction / </span>Daily Loan Advancement</h4>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <form id="dailycollectionform" action="javascript:void(0)" autocomplete="off">
                                    <input type="hidden" id="updatedailycollection" name="updateid">
                                    <div class="row"> 
                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="DATE" class="form-label">Date</label>
                                            <input type="text" class="form-control" placeholder="DD-MM-YYYY" id="date_dc" name="date_dc" value="{{ now()->format('d-m-Y') }}" max="{{ now()->format('d-m-Y') }}" onchange="checkDateSession(this)" />
                                        </div>
                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="MEMBER TYPE" class="form-label">Member Type</label>
                                            <select class="form-select" name="member_type" id="member_type">
                                                <option value="Member">Member</option>
                                                <option value="Staff">Staff</option>
                                                <option value="NonMember">Non-Member</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="DAILY AC NO" class="form-label">Acc No</label>
                                            <input type="text" id="daily_ac_no" name="daily_ac_no" class="form-control thisRequired" placeholder="Daily Account No">
                                            <div id="account_no_list"></div>
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="AC NO" class="form-label">Membership No</label>
                                            <input type="text" id="account_no" name="account_no" class="form-control thisRequired" placeholder="Account No" readonly>
                                        </div>
                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="SCHEME TYPE" class="form-label">Scheme </label>
                                            <input type="text" id="scheme_name" name="scheme_name" class="form-control thisRequired" placeholder="SCHEME" readonly>
                                        </div>
                                        <input type="hidden" id="scheme_type" name="scheme_type" class="form-control " placeholder="SCHEME" readonly>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="Amount" class="form-label">Loan Amount</label>
                                            <input type="text" id="amount_daily_collection" name="amount_daily_collection"   class="form-control thisRequired numRequired" value="0">
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="DAYS" class="form-label">Days</label>
                                            <input type="text" id="days_value" name="days_value" class="form-control" placeholder="days" >
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="INTEREST%" class="form-label">Interest(%)</label>
                                            <input type="text" id="interest_value" name="interest_value" class="form-control" placeholder="Interest" >
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="TPM" class="form-label">Principal Amount</label>
                                            <input type="text" id="total_principal_amount" name="total_principal_amount" class="form-control" placeholder="Total Principal Amount" readonly>
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="MA" class="form-label">Interest Amount</label>
                                            <input type="text" id="interest_maturity_amount" name="interest_maturity_amount" class="form-control" placeholder="Interst Amount" readonly>
                                        </div>

                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="MA" class="form-label">Loan Repayment Amt</label>
                                            <input type="text" id="maturity_amount" name="maturity_amount" class="form-control" placeholder="Maturity Amount" readonly>
                                        </div>
                                        <div class="col-md-2 col-12 mb-4">
                                            <label for="MATURITY DATE" class="form-label">Loan End Date</label>
                                            <input type="text" id="maturity_date" name="maturity_date" class="form-control" placeholder="Maturity Date" readonly>
                                        </div>

                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="paymentType" class="form-label">Payment Type</label>
                                            <select class="form-select form-select-sm Select" id="paymentType" name="paymentType">
                                                <option value="">Select</option>
                                                @if(!empty($groups))
                                                @foreach($groups as $group)
                                                <option value="{{ $group->groupCode }}">{{ $group->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="bank" class="form-label">Bank</label>
                                            <select class="form-select form-select-sm Select" id="bank" name="bank">
                                                <option value="">Select</option>
                                            </select>
                                            <p class="error"></p>
                                        </div>

                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="agentId" class="form-label">Agent</label>
                                            <select class="form-select   Select" id="agentId" name="agentId">
                                                @if(!empty($agents))
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <p class="error"></p>
                                        </div>

                                        <hr>

                                        <div class="row row-gap-3" id="account_member_details" style="display:none;">
                                            <div class="col-sm-3 py-3">
                                                <input type="text" id="member_name" name="member_name" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-3 py-3">
                                                <input type="text" id="member_fathername" name="member_fathername" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-3 py-3">
                                                <input type="text" id="member_address" name="member_address" class="form-control" placeholder="Address" readonly>
                                            </div>
                                        </div>

                                        <div class="button justify-content-end text-end pt-3" id="submitbtns">
                                            <button type="button" id="viewDailyInstallmentsBtn" class="btn btn-success px-4">View Installments</button>

                                            <button type="button" id="modifyupdatebtn" class="btn btn-primary px-4 d-none">Update</button>
                                            <button type="submit" id="savedailycollection" class="btn btn-primary px-4">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="dailycollectionpaidtable">
                        <div class="tabledata card tablee">
                            <div class="card-body" style="overflow-x: auto;">
                                <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                    <thead class="table_head thead-light">
                                        <tr>
                                            <th>SNO</th>
                                            <th>START DATE</th>
                                            <th>DAILY AC NO</th>
                                            <th>DAILY AMOUNT</th>
                                            <th>SCHEME NAME</th>
                                            <th>PENDING AMT</th>
                                            <th>EXCESS AMT</th>
                                            <th>RECEIVED AMT</th>
                                            <th>WITHDRAW AMT</th>
                                            <th>PENALTY AMT</th>
                                            <th>CURRENT AMT</th>
                                            <th>INTEREST</th>
                                            <th>MONTH/DAYS</th>
                                            <th>COLLECTION TYPE</th>
                                            <th>STATUS</th>
                                            <th>RECEIVE / MATURITY</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dailycollectiontbody"></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div> 
</div>

<div class="modal fade" id="receveamountmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel3">Deposit Daily Collection</h5>
                <input type="hidden" id="receiveamount">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body receivedamountclass">

                <div class="form-group">
                    <label for="">DATE</label>
                    <input type="date" class="form-control" id="received_amount_date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="receive_amount">RECOVERABLE AMT</label>
                    <input type="text" class="form-control" id="receive_amount" readonly>
                </div>
                <div class="form-group">
                    <label for="received_amount">RECEIVER AMOUNT</label>
                    <input type="text" class="form-control" id="received_amount_modify">
                </div>
               
                <div class="form-group">
                    <label class="form-label" for="">PAYMENT TYPE</label>
                    <select name="payment_type" id="payment_type" onchange="getbank(this)"
                        class="form-select">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="">PAYMENT BANK</label>
                    <select name="payment_bank" id="payment_bank" class="form-select">
                        <option value="">Select</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmtoreceive">Receive</button>
                <button type="button" class="btn btn-danger" id="recivemodelClose">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- installmentModal Modal -->
<div class="modal fade" id="installmentModal" tabindex="-1" role="dialog" aria-labelledby="installmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="installmentModalLabel">Daily Installments</h5>
                <button type="button" class="close closeModalBtn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-hover table-bordered" id="installmentTable">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Day</th>
                            <th scope="col">Inst. Date</th>
                            <th scope="col">Principal</th>
                            <th scope="col">Interest</th>
                            <th scope="col">Total</th>
                            <th scope="col">Remaining </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Installments will be inserted here dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeModalBtn"   data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@endsection

@push('script')
<script>

 //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>       Getting data
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    $(document).ready(function() {
        var currentDate = moment().format('DD-MM-YYYY');
        $("#lock_in_date").val(currentDate);
        $("#opening_date").val(currentDate);

        var $dateInput = $('#date_dc');
        var $lockInDateInput = $('#lock_in_date');
        $dateInput.on('change', function() {
            var selectedDate = $(this).val();
            var formattedDate = moment(selectedDate).format('DD-MM-YYYY');
            $lockInDateInput.val(formattedDate);
        });


        $("#daily_ac_no").on('keyup', function () {
            var value = $(this).val();
            var membertype = $("#member_type").val();
            axios.post("{{ route('daily.collection.lists.loan') }}", {
                'accountno': value,
                'member': membertype
            }).then((response) => {
                if (response.data.status) {
                    $("#account_no_list").html(response.data.data);
                }
            });
        });



//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>        daily installments
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

function calculateDailyInstallments() {
    let amount = Math.round(parseFloat($('#amount_daily_collection').val())) || 0;
    let annualInterestRate = parseFloat($('#interest_value').val()) || 0;
    let totalDays = parseInt($('#days_value').val()) || 0;

    const monthlyRate = (annualInterestRate / 100) / 12;
    const totalMonths = totalDays / 30;

    const emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, totalMonths)) /
                (Math.pow(1 + monthlyRate, totalMonths) - 1);

    const totalAmountPaid = emi * totalMonths;
    const totalInterestPaid = totalAmountPaid - amount;
    const maturityAmount = amount + totalInterestPaid;

    const dailyRate = annualInterestRate / 36500;

    let installments = [];
    let balance = amount;
    let totalPrincipal = 0;

    let monthlyTotals = {};

    for (let day = 1; day <= totalDays; day++) {
        let dailyInterest = Math.ceil(balance * dailyRate);
        let dailyInstallment = Math.ceil((balance + dailyInterest) / (totalDays - day + 1));
        let principal = dailyInstallment - dailyInterest;
        totalPrincipal += principal;
        balance -= principal;
        if (day === totalDays && balance > 0) {
            principal += balance;
            dailyInstallment += balance;
            balance = 0;
        }

        let date = new Date(Date.now() + (day - 1) * 86400000);
        let monthYear = date.toLocaleString('en-GB', { year: 'numeric', month: 'long' });

        installments.push({
            date: date.toLocaleDateString("en-GB"),
            principal: principal,
            interest: dailyInterest,
            total: dailyInstallment,
            remaining: balance
        });

        if (!monthlyTotals[monthYear]) {
            monthlyTotals[monthYear] = { principal: 0, interest: 0, total: 0 };
        }
        monthlyTotals[monthYear].principal += principal;
        monthlyTotals[monthYear].interest += dailyInterest;
        monthlyTotals[monthYear].total += dailyInstallment;
    }

    console.log("Total Principal Paid:", totalPrincipal);
    console.log("Total Interest Paid:", totalInterestPaid);
    console.log("Total Repayment Amount:", maturityAmount);

    console.log("Type Of Calculation:");
    console.log("Loan Amount: ₹", amount);
    console.log("Rate Of Interest: %", annualInterestRate);
    console.log("Loan Tenure: Years", (totalDays / 365).toFixed(1));
    console.log("Monthly EMI: ₹", Math.round(emi));
    console.log("Total Interest: ₹", Math.round(totalInterestPaid));
    console.log("Maturity Amount: ₹", Math.round(maturityAmount));
    console.log("Monthly EMIs:");
    for (const [month, totals] of Object.entries(monthlyTotals)) {
        console.log(`${month}: Principal = ${totals.principal}, Interest = ${totals.interest}, Total EMI = ${totals.total}`);
    }

    return {
        installments,
        totalPrincipal: totalPrincipal,
        totalInterest: totalInterestPaid,
        totalPayment: maturityAmount,
        monthlyEMIs: monthlyTotals,
        emi: Math.round(emi),
        totalInterest: Math.round(totalInterestPaid),
        maturityAmount: Math.round(maturityAmount)
    };
}




// Function to show installments in the modal
function showDailyInstallmentsInModal() {
    let { installments, totalPrincipal, totalInterest, totalPayment } = calculateDailyInstallments();

    $("#installmentTable tbody").empty();
    installments.forEach((installment, index) => {
        $("#installmentTable tbody").append(`
            <tr>
                <td>${index + 1}</td>
                <td>${installment.date}</td>
                <td>${installment.principal.toFixed(2)}</td>
                <td>${installment.interest.toFixed(2)}</td>
                <td>${installment.total.toFixed(2)}</td>
                <td>${installment.remaining.toFixed(2)}</td>
            </tr>
        `);
    });

    // Add a final row for totals
    $("#installmentTable tbody").append(`
        <tr style="font-weight: bold;">
            <td colspan="2">Total</td>
            <td>${totalPrincipal.toFixed(2)}</td>
            <td>${totalInterest.toFixed(2)}</td>
            <td>${totalPayment.toFixed(2)}</td>
            <td></td>
        </tr>
    `);

    $("#installmentModal").modal('show'); // Show the modal
}

$(".closeModalBtn").on('click', () => {
    $("#installmentModal").modal('hide');
});
$("#viewDailyInstallmentsBtn").on('click', showDailyInstallmentsInModal);




//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>        calculating amount
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

function calculateAmounts() {
    let amount = Math.round(parseFloat($('#amount_daily_collection').val())) || 0;
    let annualInterestRate = parseFloat($('#interest_value').val()) || 0;
    let totalDays = parseInt($('#days_value').val()) || 0; 
    const monthlyRate = (annualInterestRate / 100) / 12;
    const totalMonths = totalDays / 30;

    const emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, totalMonths)) /(Math.pow(1 + monthlyRate, totalMonths) - 1);

    const totalAmountPaid = emi * totalMonths;
    const totalInterestPaid = totalAmountPaid - amount;
    const maturityAmount = amount + totalInterestPaid;

    const dailyRate = annualInterestRate / 36500;

    let installments = [];
    let balance = amount;
    let totalPrincipal = 0;

    let monthlyTotals = {};

    for (let day = 1; day <= totalDays; day++) {
        let dailyInterest = Math.ceil(balance * dailyRate);
        let dailyInstallment = Math.ceil((balance + dailyInterest) / (totalDays - day + 1));
        let principal = dailyInstallment - dailyInterest;
        totalPrincipal += principal;
        balance -= principal;
        if (day === totalDays && balance > 0) {
            principal += balance;
            dailyInstallment += balance;
            balance = 0;
        }

        let date = new Date(Date.now() + (day - 1) * 86400000);
        let monthYear = date.toLocaleString('en-GB', { year: 'numeric', month: 'long' });

        installments.push({
            date: date.toLocaleDateString("en-GB"),
            principal: principal,
            interest: dailyInterest,
            total: dailyInstallment,
            remaining: balance
        });

        if (!monthlyTotals[monthYear]) {
            monthlyTotals[monthYear] = { principal: 0, interest: 0, total: 0 };
        }
        monthlyTotals[monthYear].principal += principal;
        monthlyTotals[monthYear].interest += dailyInterest;
        monthlyTotals[monthYear].total += dailyInstallment;
    }

    console.log("Total Principal Paid:", totalPrincipal);
    console.log("Total Interest Paid:", totalInterestPaid);
    console.log("Total Repayment Amount:", maturityAmount);

    console.log("Type Of Calculation:");
    console.log("Loan Amount: ₹", amount);
    console.log("Rate Of Interest: %", annualInterestRate);
    console.log("Loan Tenure: Years", (totalDays / 365).toFixed(1));
    console.log("Monthly EMI: ₹", Math.round(emi));
    console.log("Total Interest: ₹", Math.round(totalInterestPaid));
    console.log("Maturity Amount: ₹", Math.round(maturityAmount));
    console.log("Monthly EMIs:");
    for (const [month, totals] of Object.entries(monthlyTotals)) {
        console.log(`${month}: Principal = ${totals.principal}, Interest = ${totals.interest}, Total EMI = ${totals.total}`);
    }
 
    $("#total_principal_amount").val(Math.round(totalPrincipal));
    $("#interest_maturity_amount").val(Math.round(totalInterestPaid));
    $("#maturity_amount").val(Math.round(maturityAmount)); 
}
$("#amount_daily_collection, #days_value, #interest_value, #type").on('input', calculateAmounts);


//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>        Fetching data for account selection
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$(document).on('click', '#account_no_list .memberlist', function() {
    var accountNo = $(this).text();  // Fetch selected account number
    var memberType = $("#member_type").val();  // Get member type

    axios.post("{{ route('get.account.details.selected.loan') }}", {
        'accountno': accountNo,
        'membertype': memberType
    }).then((response) => {
        if (response.data.status == "success") {
            $("#daily_ac_no").val(accountNo);
            $("#account_no_list").html('');
            $("#member_name").val(response.data.name);
            $("#member_fathername").val(response.data.fathername);
            $("#member_address").val(response.data.address);
            $("#account_member_details").show();

            // Check if account details are available
            var accountDetails = response.data.account_details;
            if (accountDetails) {
                // Calculate maturity date
                $("#account_no").val(accountDetails.membershipno);
                var transactionDate = moment(accountDetails.transactionDate, 'YYYY-MM-DD');
                var maturityDate = transactionDate.clone().add({
                    years: accountDetails.years,
                    months: accountDetails.months,
                    days: accountDetails.days
                });
                var totalDurationInDays = accountDetails.days;
                $("#days_value").val(totalDurationInDays);

                var lockInDate = transactionDate.clone().add(accountDetails.lockin_days, 'days');

                $("#maturity_date").val(maturityDate.format('DD-MM-YYYY'));

                // Populate other form fields
                $("#date_dc").val(moment(accountDetails.transactionDate, 'YYYY-MM-DD').format('DD-MM-YYYY'));
                $("#scheme_name").val(accountDetails.scheme_name);
                $("#scheme_type").val(accountDetails.schemetype); // hidden input
                $("#interest_value").val(Math.round(accountDetails.interest));
                $("#amount_daily_collection").val(accountDetails.amount || "0");
                $("#total_principal_amount").val(accountDetails.amount || "0");
                $("#interest_maturity_amount").val(Math.round(accountDetails.interest) || "0");
                $("#maturity_amount").val(Math.round(accountDetails.maturityamount) || "0");
                $("#agentId").val(accountDetails.agentId || "0");

                // Update the table rows starts
                $("#dailycollectiontbody").html('');
                if (response.data.tabledata) {
                    response.data.tabledata.forEach(function(row, index) {
                        var date = moment(row.date, 'YYYY-MM-DD').format('DD-MM-YYYY');
                        var newRow = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + date + '</td>' +
                            '<td>' + row.daily_loan_accno + '</td>' +
                            '<td>' + Math.ceil(row.amount) + '</td>' +
                            '<td>' + row.schemename + '</td>' +
                            '<td>' + Math.ceil(row.pending_amount) + '</td>' +
                            '<td>' + Math.ceil(row.excess_amount) + '</td>' +
                            '<td>' + Math.ceil(row.received_amount) + '</td>' +
                            '<td>' + Math.ceil(row.withdraw_amount) + '</td>' +
                            '<td>' + Math.ceil(row.penalty_amount) + '</td>' +
                            '<td>' + Math.ceil(row.current_amount) + '</td>' +
                            '<td>' + row.interest + '</td>' +
                            '<td>' + row.month + '</td>' +  // Adjust based on your actual data
                            '<td>' + row.collectiontype + '</td>' +
                            '<td>' + row.status + '</td>' +
                            '<td style="display: flex;">' +
                                '<button type="button" class="btn btn-primary receive_saving" style="margin-right:4px;" data-id="' + row.id + '" data-amount="' + Math.ceil(row.received_amount) + '">Receive</button>' +
                                '<button type="button" class="btn btn-secondary mature" style="margin-left:4px;" data-id="' + row.id + '" data-amount="' + Math.ceil(row.received_amount) + '">Mature Daily Loan</button>' +
                            '</td>' +
                            '<td style="display: flex;">' +
                                '<button type="button" class="view" style="margin-right:4px;" data-id="' + row.id + '"><i class="fas fa-edit"></i></button>' +
                                '<button type="button" class="deletebtn" style="margin-left:4px;" data-id="' + row.id + '"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
                            '</td>' +
                        '</tr>';

                        // Append new row to table body
                        $("#dailycollectiontbody").append(newRow);
                    });
                }


                // Update the table ends
            }
        }
    }).catch((error) => {
        console.error("Error fetching account details:", error);
    });
});


// giving daily loan
$("#savedailycollection").on("click", function(e) {
    e.preventDefault();

    if (!validateForm('dailycollectionform')) {
        return; // Stop submission if validation fails
    } 
    $("#savedailycollection").prop("disabled", true);
    const formData = new FormData($("#dailycollectionform")[0]);

    axios.post("{{ route('dailycollection.account.store.loan') }}", formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        if (response.data.status === "success") {
            // Reset form and update UI
            $("#dailycollectiontbody").html('');
            $("#dailycollectionform")[0].reset();
            $("#lock_in_date, #opening_date").val(currentDate);
            $("#account_member_details").hide();
            $("#savedailycollection").prop("disabled", false);
            notify(response.data.message, 'success');
        } else if (response.data.status === "fail") {
            notify(response.data.message, 'warning');
        }
    }).catch(error => {
        console.log(error.response);
        if (error.response && error.response.status === 400) {
            notify(error.response.data.message, 'warning');
        } else {
            console.error('Error:', error.message);
        }
        $("#savedailycollection").prop("disabled", false);
    });
});


 
    });

    $(document).on('click', '#dailycollectiontbody .receive_saving', function() {
    var dataId = $(this).data('id');
    var amount = $(this).data('amount');
    $("#receiveamount").val(dataId);
    $("#receive_amount").val(amount);
    $("#received_amount_modify").val(amount);
    $("#receveamountmodel").modal('show');
});

        $(document).on('click', '#recivemodelClose ', function() {
            $("#receveamountmodel").modal('hide');
        });


        function checkDateSession(input) {
            console.log(input);
        const todayDate = new Date();
        const formattedToday = formatDateToDMY(todayDate);

        // const openingDateStr = $('#accountOpeningDateSession').val();
        // const openingDateParts = openingDateStr.split('-');
        // const openingDate = new Date(openingDateParts[2], openingDateParts[1] - 1, openingDateParts[0]);

        const sessionStart = new Date(@json(session('sessionStart')));
        const sessionEnd = new Date(@json(session('sessionEnd')));

        var accOpeningDateStr = $(input).val();
        var dateRegex = /^\d{2}-\d{2}-\d{4}$/;
        if (!dateRegex.test(accOpeningDateStr)) {
            notify("Invalid date format. Please use DD-MM-YYYY format.", 'warning');
            $(input).val(formattedToday);
            return;
        }
        var accOpeningDate = new Date(reverseFormatDate(accOpeningDateStr));

        // if (accOpeningDate < openingDate) {
        //     $(input).val(formattedToday);
        //     notify("Account opening date must be on or after the initial opening date.", 'warning');
        //     return; // Prevents form submission
        // }

        if (accOpeningDate > todayDate) {
            $(input).val(formattedToday);
            notify("Account opening date cannot be in the future.", 'warning');
            return;
        }

        if (accOpeningDate < sessionStart) {
            $(input).val(formattedToday);
            notify("Account opening date must be within the current session start date.", 'warning');
            return;
        }

        if (accOpeningDate > sessionEnd) {
            $(input).val(formattedToday);
            notify("Account opening date cannot be beyond the session end date.", 'warning');
            return;
        }
    }
    function formatDateToDMY(date) {
        const day = ("0" + date.getDate()).slice(-2);
        const month = ("0" + (date.getMonth() + 1)).slice(-2);
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }
    function reverseFormatDate(dateStr) {
        var parts = dateStr.split('-');
        if (parts.length !== 3) {
            return null;
        }
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    $('#paymentType').change(function() {
                $("#bank").find("option").not(":first").remove();
            var groupCode = $(this).val();
            $.ajax({
                url: '{{ route("getLedger") }}',
                type: 'get',
                data: {
                    groupCode: groupCode
                },
                dataType: 'json',
                success: function(response) {
                    $("#bank").find("option").remove();
                    $.each(response["ledgers"], function(key, item) {
                        $("#bank").append(`<option value='${item.ledgerCode}'>${item.name}</option>`);
                    });
                },
                error: function(jqXHR, exception) {
                }
            });
        });

        //get account data on edit click _____________
        $('#dailycollectiontbody').on('click', '.view', function(e) {
            e.preventDefault();
            var dataId = $(this).data('id');
            axios.post('{{ route("dailycollectionloan.edit.details") }}', {
                'id': dataId
            }).then((response) => {
                if (response.data.status == "success") {
                    $("#daily_ac_no").val(response.data.data.daily_loan_accno);
                    $("#date_dc").val(response.data.data.date);
                    $("#scheme_type").val(response.data.data.schemeid);
                    $("#maturity_date").val(response.data.data.maturitydate);
                    $("#interest_value").val(response.data.data.interest);
                    $("#days_value").val(response.data.data.month);
                    $("#type").val(response.data.data.collectiontype);
                    $("#amount_daily_collection").val(response.data.data.amount);
                    $("#total_principal_amount").val(response.data.data.principalamount);
                    $("#interest_maturity_amount").val(Math.round(response.data.data.interest_amount));
                    $("#maturity_amount").val(Math.round(response.data.data.maturityamount));
                    $("#lock_in_days").val(response.data.data.lockindays);
                    $("#lock_in_date").val(moment(response.data.data.lockindate, 'Y-MM-DD')
                        .format('DD-MM-YYYY'));
                    $("#updatedailycollection").val(response.data.data.id);
                    $("#payment_type").val(response.data.data.PaymentType);
                    if (response.data.data.PaymentType == "Bank") {
                        var value = response.data.data.PaymentType;
                        axios.post(
                            "{{ route('dailycollectionloan.bank.details') }}", {
                                'value': value
                            }).then((response) => {
                            if (response.data.status == "success") {
                                var out = `<option value="">Select Bank</option>`;
                                response.data.bank.forEach(function(value) {
                                    out +=
                                        `<option value="${value.ledgerCode}">${value.name}</option>`;
                                });
                                $('[name="payment_bank"]').html(out);

                            }
                        }).catch(function(error) {
                            console.error('Error:', error);
                        });


                    }

                    $("#modifyupdatebtn").removeClass('d-none');
                    $('#dailycollectionform').find('input[name="payment_bank"]').select2().val(response.data.data.ledgercode).trigger('change');
                    $("#savedailycollection").addClass('d-none');
                    $("#maturebtn").removeClass('d-none');

                } else if (response.data.status == "fail") {
                    notify(response.data.message, 'warning');
                }
            });
        });

        //update account data on update click _____________
        $(document).on('click', '#modifyupdatebtn', function (e) {
                e.preventDefault();
                var form = document.getElementById("dailycollectionform");
                var formdata = new FormData(form);
                axios.post('{{ route("update.dailyloan.collection") }}', formdata).then((
                    response) => {
                        console.log('response' , response);
                        console.log('response . data' , response.data);
                    if (response.data.status == "success") {
                        $("#dailycollectiontbody").html('');
                        $("#dailycollectionform")[0].reset();
                        $("#lock_in_date").val(currentDate);
                        $("#opening_date").val(currentDate);
                        $("#account_member_details").hide();
                        $("#savedailycollection").removeClass('d-none');
                        $("#modifyupdatebtn").addClass('d-none');
                        $("#maturebtn").addClass('d-none');
                        notify(response.data.message, 'success');
                    } else if (response.data.status == "fail") {
                        notify(response.data.message, 'warning');
                    }
                });
            });

</script>
@endpush
