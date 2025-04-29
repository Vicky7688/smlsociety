@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-2"><span class="text-muted fw-light">Transaction / </span>Monthly Income Scheme</h4>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="MisformData">
                            <div class="row">
                                <div class="col-md-2 col-12 mb-4">
                                    <label for="mis_opening_date" class="form-label">DATE</label>
                                    <input type="text" class="form-control" name="mis_opening_date"
                                        id="mis_opening_date" required>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="MEMBER TYPE" class="form-label">MEMBER TYPE</label>
                                    <select class="form-select" name="mis_member_type" id="mis_member_type">
                                        <option value="Member">Member</option>
                                        <option value="Staff">Staff</option>
                                        <option value="NonMember">Non-Member</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="account_no" class="form-label">ACCOUNT NO</label>
                                    <input type="text" name="account_no" id="account_no" class="form-control"
                                        placeholder="Account No">
                                    <div id="account_no_list"></div>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="MIS ACCOUNT NO" class="form-label">MIS ACCOUNT NO</label>
                                    <input type="text" name="mis_account_no" id="mis_account_no" class="form-control"
                                        placeholder="Mis Account No">
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="Amount" class="form-label">AMOUNT</label>
                                    <input type="text" name="mis_amount" id="mis_amount"
                                        class="form-control mis-input-cal" placeholder="Amount">
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="INTEREST RATE %" class="form-label">INTEREST RATE %</label>
                                    <input type="number" name="mis_interest_rate" id="mis_interest_rate"
                                        class="form-control mis-input-cal" placeholder="Interest Rate">
                                </div>
                                <div class="row row-gap-3" id="account_member_details" style="display:none;">
                                    <div class="col-sm-3 py-3">
                                        <input type="text" id="member_name" name="member_name" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="col-sm-3 py-3">
                                        <input type="text" id="member_fathername" name="member_fathername"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-3 py-3">
                                        <input type="text" id="member_address" name="member_address"
                                            class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="PERIOD(Y)" class="form-label">PERIOD(Y)</label>
                                    <input type="number" name="mis_period_year" id="mis_period_year"
                                        class="form-control mis-input-cal" placeholder="Period Y">
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="PERIOD(M)" class="form-label">PERIOD(M)</label>
                                    <input type="number" name="mis_period_month" id="mis_period_month"
                                        class="form-control" placeholder="Period M" readonly>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="TOTAL INTEREST" class="form-label">TOTAL INTEREST</label>
                                    <input type="number" name="mis_total_interest" id="mis_total_interest"
                                        class="form-control" placeholder="Total Interest" readonly>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="MONTHLY INTEREST" class="form-label">MONTHLY INTEREST</label>
                                    <input type="number" name="MonthInterest" id="MonthInterest" class="form-control"
                                        placeholder="Maturity Interest" readonly>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="MATURITY DATE" class="form-label">MATURITY DATE</label>
                                    <input type="text" name="mis_maturity_date" id="mis_maturity_date"
                                        class="form-control" placeholder="Maturity Date" readonly>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="MATURITY AMOUNT" class="form-label">MATURITY AMOUNT</label>
                                    <input type="text" name="mis_maturity_amount" id="mis_maturity_amount"
                                        class="form-control" placeholder="Maturity Amount" readonly>
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="INTEREST DEPOSITE" class="form-label">INTEREST DEPOSITE</label>
                                    <select class="form-select" name="mis_interest_deposite_type"
                                        onchange="InterestDepositefun(this)" id="mis_interest_deposite_type">
                                        <option value="">Select</option>
                                        <option value="Saving">Saving</option>
                                        <option value="RD">RD</option>
                                        <option value="Loan">Loan</option>
                                    </select>
                                </div>

                                <div id="rd_selector" class="row">
                                </div>

                                <div class="col-md-2 col-12 mb-4">
                                    <label for="Agent" class="form-label">Agent</label>
                                    <select class="form-select" name="mis_agent" id="mis_agent">
                                        <option value="">Select Agent</option>
                                        @foreach($agent as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="">PAYMENT TYPE</label>
                                    <select name="payment_type" id="payment_type" onchange="getbank(this)"
                                        class="form-select">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="">PAYMENT BANK</label>
                                    <select name="payment_bank" id="payment_bank" class="form-select">
                                        <option value="">Select</option>
                                    </select>
                                </div>


                                <div class="col-md-3 col-12 mb-4 pt-4" id="formsbuttons">
                                    <div id="submitbtndata">
                                        <button type="submit" id="savesubmitbtn"
                                            class="btn btn-primary waves-effect waves-light">Save</button>
                                    </div>
                                    <button type="button" class="btn btn-danger waves-effect waves-light"
                                        id="clearMisAccount">Clear</button>
                                    <button type="button" class="btn btn-success waves-effect waves-light">Last</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="mis_table_id" style="display:none;">
                <div class="tabledata card tablee">
                    <div class="card-body">
                        <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>MIS NO</th>
                                    <th>MIS Ac NO</th>
                                    <th>START DATE</th>
                                    <th>MEMBER TYPE</th>
                                    <th>ACC NO</th>
                                    <th>AMOUNT</th>
                                    <th>MATURITY DATE</th>
                                    <th>MATURED ON DATE</th>
                                    <th>MATURITY AMT</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                    <th>PRINT</th>
                                </tr>
                            </thead>
                            <tbody id="mis_tbody_list">

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>




        </div>
    </div>

</div>

<div class="modal fade" id="MisModelInstallments" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="display: grid;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Mis InstallMents</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Table to be displayed in the modal -->
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Type</th>
                            <th scope="col">Installment No</th>
                            <th scope="col">Credit Amount</th>
                            <th scope="col">Debit Amount</th>
                            <th scope="col">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="reciept_sheet_data">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="modelclosebtn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        $(document).ready(function () {
            var currentDate = moment().format('DD-MM-YYYY');
            $("#mis_opening_date").val(currentDate);

            $("#account_no").on('keyup', function () {
                var value = $(this).val();
                var membertype = $("#mis_member_type").val();
                axios.post("{{ route('mis.account.lists') }}", {
                    'accountno': value,
                    'membertype': membertype
                }).then((response) => {
                    if (response.data.status == "success") {
                        $("#account_no_list").html(response.data.data);
                    }
                });
            });

            $(document).on('click', '#account_no_list .memberlist', function () {
                var accountNo = $(this).text();
                $("#account_no").val(accountNo);
                var membertype = $("#mis_member_type").val();
                $("#mis_account_no").val(accountNo);
                $("#mis_account_no").prop("readonly", true);
                axios.post("{{ route('get.mis.account.details') }}", {
                    'accountno': accountNo,
                    'membertype': membertype
                }).then((response) => {
                    if (response.data.status == "success") {
                        $("#account_no_list").html('');
                        $("#member_name").val(response.data.name);
                        $("#member_fathername").val(response.data.fathername);
                        $("#member_address").val(response.data.address);
                        $("#account_member_details").show();
                        if (response.data.tabledata) {

                            $("#mis_tbody_list").html('');
                            response.data.tabledata.forEach(function (row) {
                                var tr = $("<tr>");
                                tr.append("<td>" + row.id + "</td>");
                                tr.append("<td>" + row.mis_ac_no + "</td>");
                                tr.append("<td>" + row.date + "</td>")
                                tr.append("<td>" + row.member_type + "</td>");
                                tr.append("<td>" + row.account_no + "</td>");
                                tr.append("<td><span class='editdetails' data-id=" + row
                                    .id + " style='color: blue;'>" + row.amount +
                                    "</span></td>");
                                tr.append("<td>" + row.maturity_date + "</td>");
                                tr.append("<td></td>");
                                tr.append("<td>" + row.amount + "</td>");
                                tr.append("<td>" + row.status + "</td>");
                                tr.append(
                                    "<td><button class='receipts_view btn btn-primary' data-id=" +
                                    row.id + ">View Receipts</button></td>");
                                tr.append("<td></td>");
                                $("#mis_tbody_list").append(tr);
                            });
                            $("#mis_table_id").show();
                        }
                    }
                });
            });

            $('#mis_table_id').on('click', '.receipts_view', function () {
                var dataId = $(this).data('id');
                axios.post('{{ route("mislist.details.data") }}', {
                    'receiptno': dataId
                }).then((response) => {
                    if (response.data.status == "success") {
                        $("#reciept_sheet_data").html('');
                        console.log(response.data.installments[0].general_ledgers);
                        var totalPaidAmount = 0;
                        response.data.installments.forEach(function (row) {
                            // Create a row for each installment
                            var tr = $("<tr>");
                            tr.append("<td>" + row.receipt_date + "</td>");
                            tr.append("<td>" + row.type + "</td>");
                            tr.append("<td>" + row.installment_no + "</td>");

                            // Initialize debit and credit amounts
                            var debitAmount = 0;
                            var creditAmount = 0;

                            // Check each ledger for "Dr" or "Cr"
                            row.general_ledgers.forEach(function (ledger) {
                                if (ledger.transactionType === 'Dr') {
                                    // If "Dr" is found, add to debitAmount
                                    debitAmount += parseFloat(ledger
                                        .transactionAmount);
                                } else if (ledger.transactionType === 'Cr') {
                                    // If "Cr" is found, add to creditAmount
                                    creditAmount += parseFloat(ledger
                                        .transactionAmount);
                                }
                            });

                            // Append debit and credit amounts to the row
                            tr.append("<td>" + debitAmount + "</td>");
                            tr.append("<td>" + creditAmount + "</td>");

                            // Calculate and append the balance (debit - credit)
                            var balance = debitAmount - creditAmount;
                            tr.append("<td>" + balance + "</td>");

                            // Append the row to the table
                            $("#reciept_sheet_data").append(tr);
                        });


                        $("#MisModelInstallments").modal("show");
                    } else if (response.data.status == "fail") {
                        notify(response.data.message, 'warning');
                    }
                });
            });

            $("#modelclosebtn").on('click', function (e) {
                $("#MisModelInstallments").modal("hide");
            });

            $(document).on('click', '.editdetails', function (e) {
                var id = $(this).data('id');
                axios.post('{{ route("get.account.mis.details") }}', {
                    'id': id
                }).then((response) => {
                    if (response.data.status == "success") {
                        var originalDate = response.data.data.date;
                        var formattedDate = moment(originalDate).format("DD-MM-YYYY");
                        $("#mis_opening_date").val(formattedDate);
                        $("#mis_account_no").val(response.data.data.mis_ac_no);
                        $("#mis_amount").val(response.data.data.amount);
                        $("#mis_interest_rate").val(response.data.data.interest);
                        $("#mis_period_year").val(response.data.data.period_year);
                        $("#mis_period_month").val(response.data.data.period_month);
                        $("#mis_total_interest").val(response.data.data.TotalInterest);
                        $("#MonthInterest").val(response.data.data.monthly_interest);
                        $("#mis_maturity_date").val(response.data.data.maturity_date);
                        $("#mis_maturity_amount").val(response.data.data.maturity_amount);
                        $("#mis_interest_deposite_type").val(response.data.data
                            .interest_deposite);
                        $("#mis_interest_deposite_type").prop("readonly", true);
                        var rdSelector = document.getElementById('rd_selector');
                        rdSelector.innerHTML = '';
                        if (response.data.data.interest_deposite == "Saving") {
                            var div = document.createElement('div');
                            div.className = 'col-md-2 col-12 mb-4';

                            var label = document.createElement('label');
                            label.for = 'SAVING A/C';
                            label.className = 'form-label';
                            label.textContent = 'SAVING A/C';

                            var input = document.createElement('input');
                            input.type = 'number';
                            input.name = 'saving_rd_ac_no';
                            input.id = 'saving_rd_ac_no';
                            input.className = 'form-control';
                            input.placeholder = 'SAVING ACCOUNT NO';
                            input.value = response.data.data.SavingRdAccountNumber;
                            input.readOnly = true;

                            div.appendChild(label);
                            div.appendChild(input);

                            rdSelector.appendChild(div);

                        } else if (response.data.data.interest_deposite == "RD") {
                            var one = response.data.data.SavingRdAccountNumber;
                            var two = response.data.data.rd_interestROI;
                            var five = response.data.data.rd_interest;

                            var labels = ['RD ACCOUNT NO', 'RD INTEREST', 'L/F NO', 'PAGE NO',
                                'MATURITY AMOUNT'
                            ];

                            for (var i = 0; i < labels.length; i++) {
                                var div = document.createElement('div');
                                div.className = 'col-md-2 col-12 mb-4';

                                var label = document.createElement('label');
                                label.for = 'rd_input_' + i;
                                label.className = 'form-label';
                                label.textContent = labels[i];

                                var input = document.createElement('input');
                                input.type = 'number';
                                input.name = 'rd_input_' + i;
                                input.id = 'rd_input_' + i;
                                input.className = 'form-control';
                                input.placeholder = labels[i];
                                if (i === 0) {
                                    input.readOnly = true;
                                }

                                div.appendChild(label);
                                div.appendChild(input);

                                rdSelector.appendChild(div);
                            }

                            $("#rd_input_0").val(one);
                            $("#rd_input_1").val(two);
                            $("#rd_input_4").val(five);

                        } else if (response.data.data.interest_deposite == "Loan") {
                            var div = document.createElement('div');
                            div.className = 'col-md-2 col-12 mb-4';

                            var label = document.createElement('label');
                            label.for = 'loan A/C';
                            label.className = 'form-label';
                            label.textContent = 'loan A/C';

                            var input = document.createElement('input');
                            input.type = 'number';
                            input.name = 'loan_rd_ac_no';
                            input.id = 'loan_rd_ac_no';
                            input.className = 'form-control';
                            input.placeholder = 'loan ACCOUNT NO';
                            input.value = response.data.data.SavingRdAccountNumber;;


                            div.appendChild(label);
                            div.appendChild(input);

                            rdSelector.appendChild(div);
                        }
                        // Hide savesubmitbtn button

                        if (response.data.data.payment_type == "Cash") {
                            $("#payment_type").val(response.data.data.payment_type);
                        } else if (response.data.data.payment_type == "Bank") {
                            $("#payment_type").val(response.data.data.payment_type);
                            var value = "Bank";
                            axios.post("{{ route('mis.bank.details') }}", {
                                'value': value
                            }).then((response) => {
                                if (response.data.status == "success") {
                                    var out = `<option value="">Select Bank</option>`;
                                    response.data.bank.forEach(function (value) {
                                        out +=
                                            `<option value="${value.ledgerCode}">${value.name}</option>`;
                                    });
                                    $('[name="payment_bank"]').attr('id',
                                        'payment_bank');

                                    $('[name="payment_bank"]').html(out);
                                    //$("#payment_bank").val(response.data.data.ledgerCode);
                                }
                            })
                            $('#MisformData').find('select[name="payment_bank"]').val(response
                                .data.data.ledgerCode).trigger('change');

                        }
                        $("#submitbtndata").empty();
                        var updateButton = $('<button/>', {
                            id: 'updatebtn',
                            type: 'button',
                            text: 'Update',
                            class: 'btn btn-primary',
                            'data-id': id,
                        });
                        $("#submitbtndata").append(updateButton);
                    }
                });
            });

            $(".mis-input-cal").on('keyup', realtimecalculation);

            function realtimecalculation() {
                var DateInput = $("#mis_opening_date");
                var DateValue = DateInput.val();
                var parsedDate = moment(DateValue, 'DD-MM-YYYY');
                var formattedDate = parsedDate.format('YYYY-MM-DD');

                var Amount = parseFloat($("#mis_amount").val()) || 0;
                var InterestRate = parseFloat($("#mis_interest_rate").val()) || 0;
                var PeriodYear = parseFloat($("#mis_period_year").val()) || 0;
                var PeriodMonth = parseFloat($("#mis_period_month").val()) || 0;

                Period = Month = Math.round(PeriodYear * 12);
                $('#mis_period_month').val(Month);

                TotalInterest = Math.round((InterestRate / 100 * Amount) * PeriodYear);
                $("#mis_total_interest").val(TotalInterest);

                MonthInterest = Math.round(TotalInterest / Month);
                $('#MonthInterest').val(MonthInterest);

                var newDate = parsedDate.clone().add(PeriodYear, 'years');
                var formattedNewDate = newDate.format('DD-MM-YYYY');

                $("#mis_maturity_date").val(formattedNewDate);

                var MaturityAmount = Amount;
                $('#mis_maturity_amount').val(MaturityAmount);
            }


            $("#MisformData").validate({
                rules: {
                    mis_opening_date: {
                        required: true,
                        customDate: true,
                    },
                    mis_member_type: {
                        required: true,
                    },
                    account_no: {
                        required: true,
                        number: true,
                    },
                    mis_account_no: {
                        required: true,
                        number: true,
                    },
                    mis_amount: {
                        required: true,
                        number: true,
                    },
                    mis_interest_rate: {
                        required: true,
                    },
                    mis_period_year: {
                        required: true,
                    },
                    mis_interest_deposite_type: {
                        required: true,
                    },
                    rd_input_0: {
                        required: true,
                    },
                    rd_input_1: {
                        required: true,
                    },
                    rd_input_4: {
                        required: true,
                    },

                },
                messages: {
                    mis_opening_date: {
                        required: "Please enter a date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    },
                    mis_member_type: {
                        required: "Required",
                    },
                    account_no: {
                        required: "Required",
                        number: "Please enter a valid number",
                    },
                    mis_account_no: {
                        required: "Required",
                        number: "Please enter a valid number",
                    },
                    mis_amount: {
                        required: "Required",
                        number: "Please enter a valid number",
                    },
                    mis_interest_rate: {
                        required: "Required",
                    },
                    mis_period_year: {
                        required: "Required",
                    },
                    mis_interest_deposite_type: {
                        required: "Required",
                    },
                    rd_input_0: {
                        required: "Required",
                    },
                    rd_input_1: {
                        required: "Required",
                    },
                    rd_input_4: {
                        required: "Required",
                    },

                },
                errorElement: "p",
                errorPlacement: function (error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                     var form = $('#MisformData');
                    axios.post('{{ route("store.mis.details") }}', formData).then(
                        (response) => {
                            if (response.data.status == "success") {
                                $("#MisformData")[0].reset();
                                $("#mis_opening_date").val(currentDate);
                                $("#account_member_details").hide();
                                $("#rd_selector").empty();
                                $("#payment_type").val('Cash');
                                $("#payment_bank").val('');
                                notify(response.data.message, 'success');
                            } else if (response.data.status == "fail") {
                                notify(response.data.message, 'warning');
                            }
                        }).catch(error => {
                         console.log(error.response) ;
                        // Check if the error is a 400 Bad Request error
                        if (error.response && error.response.status === 400) {
                             notify(error.response.data.message, 'warning');
                            
                        } else {
                          // Handle other errors
                          console.error('Error:', error.message);
                        }
                    })
                }
            });

            $(document).on('keyup', '#rd_selector #rd_input_1', function () {
                var interest = $(this).val();

                var amount = parseFloat($("#MonthInterest").val()) || 0;
                var monthsToAdddate = $("#mis_period_month").val();


                var VAmount = amount;
                var VInterest = interest;
                var VMonth = monthsToAdddate;
                var i;
                var XAmt;
                var XIntr;
                i = 1;
                XAmt = 0;
                XIntr = 0
                var npery = 4;
                var rate = ((VInterest) / (100));
                var effect_value = ((Math.pow(1 + (rate / npery), npery)) - 1) * 100;
                effect_value = effect_value.toFixed(3);

                var nperyy = 12;
                npery = parseInt(nperyy);
                var eeef = (effect_value / 100) + 1;
                var ennn = 1 / npery;
                var epow = Math.pow(eeef, ennn);
                var epow1 = (epow - 1) * 100;
                var nominal = (npery * epow1).toFixed(3);

                var rt = nominal / npery;
                var rtt = rt.toFixed(3);

                var sumt = VAmount;
                var summ = i = intval = intvall = 0;
                for (i = 0; i < VMonth; i++) {
                    intval = (sumt * rtt) / 100;
                    intvall = intval.toFixed(2);
                    summ = parseFloat(sumt) + parseFloat(intvall);
                    sumt = summ + VAmount;
                }
                $("#rd_input_4").val(summ.toFixed(2));
            });

            $(document).on('click', '#updatebtn', function () {
                var dataIdValue = $("#updatebtn").attr('data-id');
                var formDataArray = $('#MisformData').serializeArray();
                var formData = new FormData();
                $.each(formDataArray, function (index, field) {
                    formData.append(field.name, field.value);
                });
                formData.append('updateid', dataIdValue);

                axios.post('{{ route("update.mis.details") }}', formData).then((
                    response) => {
                    if (response.data.status == "success") {
                        $("#MisformData")[0].reset();
                        $("#mis_opening_date").val(currentDate);
                        $("#account_member_details").hide();
                        $("#rd_selector").empty();
                        $("#payment_type").val('Cash');
                        $("#payment_bank").val('');
                        $("#submitbtndata").empty();
                        var submitbtn = $('<button/>', {
                            id: 'savesubmitbtn',
                            type: 'submit',
                            text: 'Save',
                            class: 'btn btn-primary',
                        });
                        $("#submitbtndata").append(submitbtn);
                        notify(response.data.message, 'success');
                    } else if (response.data.status == "fail") {
                        notify(response.data.message, 'warning');
                    }
                });
            });

            $("#clearMisAccount").click(function (e) {
                $("#MisformData")[0].reset();
                $("#mis_opening_date").val(currentDate);
                $("#account_member_details").hide();
                $("#rd_selector").empty();
                $("#payment_type").val('Cash');
                $("#payment_bank").val('');
                $("#submitbtndata").empty();
                var submitbtn = $('<button/>', {
                    id: 'savesubmitbtn',
                    type: 'submit',
                    text: 'Save',
                    class: 'btn btn-primary',
                });
                $("#submitbtndata").append(submitbtn);
            });

        });

        function InterestDepositefun(selectElement) {
            var selectedValue = selectElement.value;
            var rdSelector = document.getElementById('rd_selector');
            rdSelector.innerHTML = '';

            if (selectedValue === 'RD') {

                var accountno = $("#account_no").val();
                var membertype = $("#mis_member_type").val();
                if (accountno == '') {
                    $("#mis_interest_deposite_type").val('');
                    console.log("Please insert Account No first");
                } else {
                    var labels = ['RD ACCOUNT NO', 'RD INTEREST', 'L/F NO', 'PAGE NO', 'MATURITY AMOUNT'];
                    var rdSelector = document.getElementById('rd_selector');
                    for (var i = 0; i < labels.length; i++) {
                        var div = document.createElement('div');
                        div.className = 'col-md-2 col-12 mb-4';

                        var label = document.createElement('label');
                        label.for = 'rd_input_' + i;
                        label.className = 'form-label';
                        label.textContent = labels[i];

                        var input = document.createElement('input');
                        input.type = 'number';
                        input.name = 'rd_input_' + i;
                        input.id = 'rd_input_' + i;
                        input.className = 'form-control';
                        input.placeholder = labels[i];

                        // Set value for rd_input_1
                        if (i === 0) {
                            input.value = accountno;
                            input.readOnly = true;
                        }

                        div.appendChild(label);
                        div.appendChild(input);

                        rdSelector.appendChild(div);
                    }
                }

            } else if (selectedValue === 'Saving') {
                var value = "Saving";
                var accountno = $("#account_no").val();
                var membertype = $("#mis_member_type").val();
                if (accountno == '') {
                    $("#mis_interest_deposite_type").val('');
                    console.log("Please insert Account No first");
                } else {
                    axios.post('{{ route("interest.deposit.check") }}', {
                        'type': value,
                        'accno': accountno,
                        'membertype': membertype
                    }).then((response) => {
                        if (response.data.status == "success") {
                            var div = document.createElement('div');
                            div.className = 'col-md-2 col-12 mb-4';

                            var label = document.createElement('label');
                            label.for = 'SAVING A/C';
                            label.className = 'form-label';
                            label.textContent = 'SAVING A/C';

                            var input = document.createElement('input');
                            input.type = 'number';
                            input.name = 'saving_rd_ac_no';
                            input.id = 'saving_rd_ac_no';
                            input.className = 'form-control';
                            input.placeholder = 'SAVING ACCOUNT NO';
                            input.value = response.data.savingno;
                            input.setAttribute('readonly', true);

                            div.appendChild(label);
                            div.appendChild(input);

                            rdSelector.appendChild(div);
                        }

                    }).catch(error => {
                         console.log(error.response) ;
                        // Check if the error is a 400 Bad Request error
                        if (error.response && error.response.status === 400) {
                             notify(error.response.data.message, 'warning');
                            
                        } else {
                          // Handle other errors
                          console.error('Error:', error.message);
                        }
                    })
                }
            } else if (selectedValue === 'Loan') {
                var value = "Loan";
                var accountno = $("#account_no").val();
                var membertype = $("#mis_member_type").val();
                if (accountno == '') {
                    $("#mis_interest_deposite_type").val('');
                    console.log("Please insert Account No first");
                } else {
                    axios.post('{{ route("interest.deposit.check") }}', {
                        'type': value,
                        'accno': accountno,
                        'membertype': membertype
                    }).then((response) => {
                        if (response.data.status == "success") {
                            var div = document.createElement('div');
                            div.className = 'col-md-2 col-12 mb-4';

                            var label = document.createElement('label');
                            label.for = 'loan A/C';
                            label.className = 'form-label';
                            label.textContent = 'loan A/C';

                            var input = document.createElement('input');
                            input.type = 'number';
                            input.name = 'loan_rd_ac_no';
                            input.id = 'loan_rd_ac_no';
                            input.className = 'form-control';
                            input.placeholder = 'loan ACCOUNT NO';
                            input.value = response.data.loanano;
                            input.setAttribute('readonly', true);

                            div.appendChild(label);
                            div.appendChild(input);

                            rdSelector.appendChild(div);
                        }
                    }).catch(error => {
                         console.log(error.response) ;
                        // Check if the error is a 400 Bad Request error
                        if (error.response && error.response.status === 400) {
                             notify(error.response.data.message, 'warning');
                            
                        } else {
                          // Handle other errors
                          console.error('Error:', error.message);
                        }
                    })
                }
            }
        }

        function getbank(ele) {
            var value = $(ele).val();
            if (value == "Bank") {
                axios.post("{{ route('mis.bank.details') }}", {
                    'value': value
                }).then((response) => {
                    if (response.data.status == "success") {
                        var out = `<option value="">Select Bank</option>`;
                        response.data.bank.forEach(function (value) {
                            out += `<option value="${value.ledgerCode}">${value.name}</option>`;
                        });
                        $('[name="payment_bank"]').html(out);
                    }
                }).catch(function (error) {
                    console.error('Error:', error);
                });
            } else {
                $("#payment_bank").val('');
            }
        }

    </script>
@endpush
