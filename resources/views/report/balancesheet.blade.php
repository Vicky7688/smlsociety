@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Balance Sheet</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">
                        <form action="javascript:void(0)" id="balanceSheetform">
                            <div class="row">
                                @php
                                    $currentDate =
                                        Session::get('currentdate') ??
                                        date('d-m-Y', strtotime(session('sessionStart')));
                                @endphp
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="Date from" class="form-label">Date from</label>
                                    <input type="text" class="form-control formInputsReport" id="date_from"
                                        name="date_from" value="{{ date('d-m-Y', strtotime(session('sessionStart'))) }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="Till Date" class="form-label">Till Date</label>
                                    <input type="text" class="form-control formInputsReport" id="date_till_date"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}"
                                        name="date_till_date" />
                                </div>
                                <div class="col-lg-8 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">

                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                            id="viewdatabooksdetails">
                                            View
                                        </button>
                                        <button type="button"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom" id=""
                                            onclick="printReport()">
                                            Print
                                        </button>
                                        {{--  <a type="button" href="{{ route('balancebookPrint.print') }}" target="_blank"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </a>  --}}
                                        {{--  <div class="ms-2 dropdown">
                                            <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom"
                                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                More
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i
                                                            class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i
                                                            class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="share()"><i
                                                            class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                            </ul>
                                        </div>  --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="balancesheetdiv">
            <div class="card-body tablee" id="balanebookrecords"><!--table 1-->
                <table class="table table-bordered" style="width: 50%; float: left;" id="excelTable">
                    <thead class="table_head">
                        <tr>
                            <th class="borderr text-center text-uppercase" colspan="3">Liability</th>
                        </tr>
                        <tr>
                            <th class="borderr ">Particulars</th>
                            <th class="borderr  text-right">Amount</th>
                            <th class="borderr  text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="liabilitiesBody" id="liabilitiesBody">

                    </tbody>
                    <tbody id="liabilitiesgrandtotal">
                    </tbody>
                </table>
                <!--Table two-->
                <table class=" table table-bordered" style="width: 50%; float: left;" id="excelTablee">
                    <thead class="table_head">
                        <tr>
                            <th class="borderr  text-center text-uppercase" colspan="3">Asset</th>
                        </tr>
                        <tr>
                            <th class="borderr ">Particulars</th>
                            <th class="borderr  text-right">Amount</th>
                            <th class="borderr  text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="assetsBody" id="assetsBody">
                    </tbody>
                    <tbody id="assetsgrandtotal">
                    </tbody>
                </table>

                <div style="clear:both;"></div>

                <table class="table table-borderred table-striped table-sm" style="width: 50%; float: left;">
                    <tbody>

                    </tbody>
                </table>

                <table id="" class="table table-borderred table-striped table-sm" style="width: 50%; float: left;">
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function capitalizeWords(str) {
            if (typeof str !== 'string' || !str) {
                console.warn("Invalid input for capitalizeWords:", str);
                return '';
            }
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        {{--  $('#balancesheetdiv').hide();  --}}
        $(document).ready(function(e) {
            $(document).on('submit', '#balanceSheetform', function(event) {
                event.preventDefault();
                let start_date = $('#date_from').val();
                let end_date = $('#date_till_date').val();

                $.ajax({
                    url: "{{ route('getbalancesheetdate') }}",
                    type: 'post',
                    data: {
                        start_date: start_date,
                        end_date: end_date
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let assets = res.assets;
                            let currentYearRecoverable = res.currentYearRecoverable;
                            let currentfinancialYear = res.currentfinancialYear;
                            let custom_2023_2024_pay_recoverable = res.custom_2023_2024_pay_recoverable;
                            let incomes = res.incomes ?? [];
                            let expenses = res.expenses ?? [];
                            let lastYearStartDate = res.lastYearStartDate;
                            let lastYearEndDate = res.lastYearEndDate;

                            let currentLoanRecoverable = res.currentLoanRecoverable || 0;
                            let currentRdInterestPayable = res.currentRdInterestPayable || [];
                            let currentFdInterestPayable = res.currentFdInterestPayable || [];
                            let currentDailyDepositPayable = res.currentDailyDepositPayable ||
                            [];
                            let lastpayables = res.lastpayables || [];
                            let lastfinancialYear = res.lastfinancialYear;

                            let LbscurrentLoanRecoverable = res.LbscurrentLoanRecoverable || 0;
                            let LbscurrentFdInterestPayable = res.LbscurrentFdInterestPayable ||
                                [];
                            let LbscurrentDailyDepositPayable = res
                                .LbscurrentDailyDepositPayable || [];
                            let LbscurrentRdInterestPayable = res.LbscurrentRdInterestPayable ||
                                [];
                            let profitOrLoss = res.profitOrLoss || [];


                            let assetsgrandTotal = 0;
                            let lastGroupName = null;
                            let alastGroupName = null;

                            let assetsBody = $('#assetsBody');
                            assetsBody.empty();


                            let assetsrouptotal = 0;
                            let ledgerAmount = 0;
                            let totalIncome = 0;
                            let totalExpenses = 0;


                            if (assets && assets.length > 0) {

                                assets.forEach((data, index) => {
                                    let opening = parseFloat(data.openingAmount) ?? 0;
                                    let credit = parseFloat(data.credit_amount) ?? 0;
                                    let debit = parseFloat(data.debit_amount) ?? 0;

                                    ledgerAmount = opening + debit - credit;

                                    if (ledgerAmount) {
                                        if (alastGroupName !== data.group_name) {
                                            if (alastGroupName !== null) {
                                                assetsBody.append(
                                                    `<tr>
                                                        <td colspan="2">Total ${capitalizeWords(alastGroupName)}</td>
                                                        <td>${assetsrouptotal.toFixed(2)}</td>
                                                    </tr>`
                                                );
                                            }
                                            assetsrouptotal = 0;
                                            assetsBody.append(
                                                `<tr>
                                                    <td colspan="2" style="text-align:left;">
                                                        <strong><u>${capitalizeWords(data.group_name)} : -</u></strong>
                                                    </td>
                                                    <td></td>
                                                </tr>`
                                            );
                                            alastGroupName = data.group_name;
                                        }

                                        assetsrouptotal += ledgerAmount;

                                        assetsBody.append(
                                            `<tr>
                                                <td colspan="1" style="text-align:left;">
                                                    ${capitalizeWords(data.ledger_name)}
                                                </td>
                                                <td>${ledgerAmount.toFixed(2)}</td>
                                                <td></td>
                                            </tr>`
                                        );
                                        assetsgrandTotal += ledgerAmount;
                                    }
                                });

                                if (alastGroupName !== null) {
                                    assetsBody.append(
                                        `<tr>
                                            <td colspan="2">Total ${alastGroupName}</td>
                                            <td>${assetsrouptotal.toFixed(2)}</td>
                                        </tr>`
                                    );
                                }
                            }

                            let endDate = $('#date_till_date').val();

                            //____________Current Bank FD Interest Recoverables


                            if (currentLoanRecoverable > 0) {
                                assetsBody.append(
                                    `<tr>
                                        <td class="tdtext" style="text-align:left;">Member Loan Intt. Recoverable :- </td>
                                        <td class="tdtextvalue">${parseFloat(currentLoanRecoverable).toFixed(2)}</td>
                                        <td></td>
                                    </tr>`
                                );


                                assetsgrandTotal += parseFloat(currentLoanRecoverable);
                                totalIncome += parseFloat(currentLoanRecoverable);
                            }

                            // assetsBody.append(`<tr>
                        //         <td class="tdtext" colspan="2">Total Interest Recoverable</td>
                        //         <td class="tdtextvalue">${parseFloat(bankInterestRecoverableTotal).toFixed(2)}</td>

                        //     </tr>`
                            // );


                            //____________End Bank FD Interest Recoverables

                            let liabilities = res.liabilities;
                            let currentpayables = res.currentpayables;
                            let libilitiesgrandTotal = 0;
                            let libilitieslastGroupName = null;
                            let lialastGroupName = null;

                            let libilitiesBody = $('#liabilitiesBody');
                            libilitiesBody.empty();


                            let liabilitiesrrouptotal = 0;
                            let lialedgerAmount = 0;

                            if (liabilities && liabilities.length > 0) {
                                liabilities.forEach((data, index) => {
                                    let opening = parseFloat(data.openingAmount) ?? 0;
                                    let credit = parseFloat(data.credit_amount) ?? 0;
                                    let debit = parseFloat(data.debit_amount) ?? 0;

                                    lialedgerAmount = opening + credit - debit;

                                    if (lialedgerAmount > 0) {
                                        if (lialastGroupName !== data.group_name) {

                                            if (lialastGroupName !== null) {
                                                libilitiesBody.append(
                                                    `<tr>
                                                        <td colspan="2">Total ${capitalizeWords(lialastGroupName)}</td>
                                                        <td>${liabilitiesrrouptotal.toFixed(2)}</td>
                                                    </tr>`
                                                );
                                            }

                                            liabilitiesrrouptotal = 0;
                                            libilitiesBody.append(
                                                `<tr>
                                                    <td colspan="2" style="text-align:left;">
                                                        <u>
                                                            <strong>${capitalizeWords(data.group_name)} : -</strong>
                                                        </u>
                                                    </td>
                                                    <td></td>
                                                </tr>`
                                            );
                                            lialastGroupName = data.group_name;
                                        }

                                        liabilitiesrrouptotal += lialedgerAmount;
                                        libilitiesBody.append(
                                            `<tr>
                                                <td colspan="1" style="text-align:left;">
                                                    ${capitalizeWords(data.ledger_name)}
                                                </td>
                                                <td>${lialedgerAmount.toFixed(2)}</td>
                                                <td></td>
                                            </tr>`
                                        );
                                        libilitiesgrandTotal += lialedgerAmount;
                                    }
                                });

                                if (lialastGroupName !== null) {
                                    libilitiesBody.append(
                                        `<tr>
                                            <td colspan="2">Total ${capitalizeWords(lialastGroupName)}</td>
                                            <td>${liabilitiesrrouptotal.toFixed(2)}</td>
                                        </tr>`
                                    );
                                }
                            }

                            let payabalessubtotal = 0;
                            let recoverbalesssubtotal = 0;

                            if (Array.isArray(custom_2023_2024_pay_recoverable) &&
                                custom_2023_2024_pay_recoverable.length > 0) {
                                libilitiesBody.append(`
                                    <tr>
                                        <td colspan="2" style="text-align:left;">
                                            <strong>
                                                <u>Interest Payables - ${currentfinancialYear}</u>
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                `);

                                assetsBody.append(`
                                    <tr>
                                        <td colspan="2" style="text-align:left;">
                                            <u>
                                                <strong>
                                                    Interest Recoverable - ${currentfinancialYear}</u>
                                                </strong>
                                            </td>
                                        <td></td>
                                    </tr>
                                `);

                                custom_2023_2024_pay_recoverable.forEach((data) => {
                                    if (data.types === 'Payables') {
                                        libilitiesBody.append(`
                                            <tr>
                                                <td style="text-align:left;">${capitalizeWords(data.name)}</td>
                                                <td>${parseFloat(data.amount).toFixed(2)}</td>
                                                <td></td>
                                            </tr>
                                        `);
                                        payabalessubtotal += parseFloat(data.amount);
                                        totalExpenses += parseFloat(data.amount);
                                        libilitiesgrandTotal += parseFloat(data.amount);
                                    } else {
                                        assetsBody.append(`
                                            <tr>
                                                <td style="text-align:left;">${capitalizeWords(data.name)}</td>
                                                <td>${parseFloat(data.amount).toFixed(2)}</td>
                                                <td></td>
                                            </tr>
                                        `);
                                        recoverbalesssubtotal += parseFloat(data
                                        .amount);
                                        totalIncome += parseFloat(data.amount);
                                        assetsgrandTotal += parseFloat(data.amount);
                                    }
                                });

                                // Append subtotals after loop
                                libilitiesBody.append(`
                                    <tr>
                                        <td colspan="2" style="text-align:center;">Total Interest Payables</td>
                                        <td>${payabalessubtotal.toFixed(2)}</td>
                                    </tr>
                                `);

                                assetsBody.append(`
                                    <tr>
                                        <td colspan="2" style="text-align:center;">Total Interest Recoverable</td>
                                        <td>${recoverbalesssubtotal.toFixed(2)}</td>
                                    </tr>
                                `);
                            }



                            //_________________________Current Members/NonMembers/Staff FD Ineterest Payables__________________
                            let groupTotal = {};
                            let groupInterestTotal = {};
                            let fdpayablesGrandTotal = 0;

                            let dataArray = currentFdInterestPayable || {};

                            if (dataArray && dataArray.length > 0) {
                                libilitiesBody.append(`
                                <tr>
                                    <td class="tdtext" style="text-align:left;">
                                        <strong>Interest Payables FD :-</strong>
                                    </td>
                                    <td class="tdtextvalue"></td>
                                </tr>`);


                                Object.keys(dataArray).forEach((key) => {
                                    const row = dataArray[key];
                                    const fdType = row.fdType || "Unknown";
                                    const fdTypeLabel = row.fdname || "Unknown";

                                    if (!groupTotal[fdType]) {
                                        groupTotal[fdType] = 0;
                                        groupInterestTotal[fdType] = 0;
                                    }

                                    var principal = parseFloat(row.principalAmount);
                                    var totalAmount = principal;

                                    var [day, month, year] = endDate.split('-').map(
                                        Number);
                                    var currentDate = new Date(year, month - 1, day);
                                    var openingDate = new Date(row.openingDate);

                                    var daysElapsed = Math.round((currentDate -
                                        openingDate) / (1000 * 60 * 60 * 24));
                                    var interest = 0;

                                    if (daysElapsed >= 0) {
                                        interest = calculateTotalInterest(row
                                            .interestType, principal, row
                                            .interestRate, daysElapsed);
                                    }

                                    row.interestAmount = parseFloat(interest.toFixed(
                                    2));
                                    groupTotal[fdType] += totalAmount;
                                    groupInterestTotal[fdType] += row.interestAmount;
                                    fdpayablesGrandTotal += row.interestAmount;
                                    totalExpenses += row.interestAmount;

                                });


                                Object.keys(groupTotal).forEach((fdType) => {
                                    const fdTypeLabel = dataArray.find(row => row
                                        .fdType === fdType)?.fdname || "Unknown";

                                    libilitiesBody.append(
                                        `<tr>
                                            <td class="tdtext" style="text-align:left;">${fdTypeLabel}</td>
                                            <td class="tdtextvalue">
                                                ${Math.abs(parseFloat(groupInterestTotal[fdType])).toFixed(2)}
                                            </td>
                                        </tr>`
                                    );

                                    libilitiesgrandTotal += parseFloat(
                                        groupInterestTotal[fdType]);
                                });

                                libilitiesBody.append(
                                    `<tr>
                                        <td class="tdtext" colspan="2">Total FD Interest Payables</td>
                                        <td class="tdtextvalue">
                                            ${Math.abs(parseFloat(fdpayablesGrandTotal)).toFixed(2)}
                                        </td>
                                    </tr>`
                                );
                            }

                            //_________________________End Members/NonMembers/Staff FD Ineterest Payables__________________


                            //_________________________Current DDS Collection Interest Payables Member/NonMember/Staff

                            let groupDDSTotal = {};
                            let groupDDSInterestTotal = {};
                            let ddspayablesGrandTotal = 0;

                            let dataArrays = currentDailyDepositPayable.memberType || [];

                            if (dataArrays.length > 0) {
                                libilitiesBody.append(
                                    `<tr>
                                    <td class="tdtext" style="text-align:left;">
                                        <strong>Interest Payables Daily Deposit :-</strong>
                                    </td>
                                    <td class="tdtextvalue"></td>
                                </tr>`);

                                let dates = document.getElementById('date_till_date').value;
                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);

                                let DdsGrandTotal = 0;
                                let InttPayableTotal = 0;
                                let NetGrandTotal = 0;

                                dataArrays.forEach((row, index) => {
                                    const schid = row.schid || "Unknown";
                                    const shcname = row.schname;
                                    if (!groupDDSTotal[schid]) {
                                        groupDDSTotal[schid] = 0;
                                        groupDDSInterestTotal[schid] = 0;
                                    }

                                    let openingDate = new Date(row.opening_date);
                                    let days = openingDate.getDate();
                                    let month = openingDate.getMonth() + 1;
                                    let year = openingDate.getFullYear();

                                    days = days < 10 ? `0${days}` : days;
                                    month = month < 10 ? `0${month}` : month;
                                    let formattedDate = `${days}-${month}-${year}`;

                                    let ss = parseFloat(row.total_amount || 0);
                                    let ww = parseFloat(row.withdraw || 0);
                                    var principal = ss - ww;
                                    let rate = parseFloat(row.interest || 0);
                                    let months = calculateMonthDifference(openingDate,
                                        endDate);
                                    let rdAmount = calculateRDAmount(principal, rate,
                                        months);
                                    let interest = Math.round(calculateInterest(
                                        principal, rate, months));

                                    let totalInterest = 0;
                                    const quarterlyRate = rate / 4 / 100;
                                    const daysInQuarter = 91;
                                    const totalDays = months * 30.44;
                                    const completedQuarters = Math.floor(totalDays /
                                        daysInQuarter);
                                    const remainingDays = totalDays % daysInQuarter;

                                    let maturityAmount = principal;

                                    for (let i = 0; i < completedQuarters; i++) {
                                        const quarterlyInterest = maturityAmount *
                                            quarterlyRate;
                                        totalInterest += quarterlyInterest;
                                        maturityAmount += quarterlyInterest;
                                    }

                                    if (remainingDays > 0) {
                                        const dailyRate = quarterlyRate / daysInQuarter;
                                        const dailyInterest = maturityAmount *
                                            dailyRate * remainingDays;
                                        totalInterest += dailyInterest;
                                        maturityAmount += dailyInterest;
                                    }

                                    DdsGrandTotal += principal;
                                    InttPayableTotal += totalInterest;
                                    NetGrandTotal += maturityAmount;

                                    row.interestAmount = parseFloat(totalInterest
                                        .toFixed(2));
                                    groupDDSTotal[schid] += principal;
                                    groupDDSInterestTotal[schid] += row.interestAmount;
                                    ddspayablesGrandTotal += row.interestAmount;
                                    totalExpenses += row.interestAmount;

                                });

                                Object.keys(groupDDSTotal).forEach((schid) => {
                                    const matchingRow = dataArrays.find(row => String(
                                            row.schid).trim() === String(schid)
                                        .trim());
                                    const shcname = matchingRow ? matchingRow.schname :
                                        "Unknown";

                                    libilitiesBody.append(
                                        `<tr>
                                            <td class="tdtext" style="text-align:left;">
                                                ${capitalizeWords(shcname)}
                                            </td>
                                            <td class="tdtextvalue">
                                                ${Math.abs(parseFloat(groupDDSInterestTotal[schid])).toFixed(2)}
                                            </td>
                                        </tr>`
                                    );

                                    libilitiesgrandTotal += parseFloat(
                                        groupDDSInterestTotal[schid]);
                                });

                                libilitiesBody.append(
                                    `<tr>
                                            <td class="tdtext" colspan="2">
                                                Total DDS Interest Payables
                                            </td>
                                            <td class="tdtextvalue">
                                                ${Math.abs(parseFloat(ddspayablesGrandTotal)).toFixed(2)}
                                            </td>
                                    </tr>`
                                );
                            }

                            //_________________________End Current DDS Collection Interest Payables Member/NonMember/Staff__________________


                            //_____________Income && Expenses
                            let maxRow = Math.max(incomes.length, expenses.length);
                            for (let i = 0; i < maxRow; i++) {
                                // Expenses Section
                                if (i < expenses.length) {
                                    let expense = parseFloat(expenses[i].debit) || 0;
                                    let expense_credit = parseFloat(expenses[i].credit) || 0;
                                    let expbalance = expense - expense_credit;
                                    totalExpenses += parseFloat(expbalance);


                                }

                                // Incomes Section
                                if (i < incomes.length) {
                                    let income = parseFloat(incomes[i].total_income) || 0;
                                    let income_debit = parseFloat(incomes[i]
                                        .total_income_debit) || 0;
                                    let incomebalancesss = income - income_debit;

                                    totalIncome += parseFloat(incomebalancesss);
                                }
                            }

                            //_____________End Income && Expenses






                            //_______________________ LBS Payables && Recoverable ______________________________



                            //_________________________LBS Members/NonMembers/Staff FD Ineterest Payables__________________
                            let lbsgroupTotal = {};
                            let lbsgroupInterestTotal = {};

                            let lbsdataArray = LbscurrentFdInterestPayable || {};


                            //_________________________End Members/NonMembers/Staff FD Ineterest Payables__________________





                            //_________________________LBS DDS Collection Interest Payables Member/NonMember/Staff

                            let lbsgroupDDSTotal = {};
                            let lbsgroupDDSInterestTotal = {};

                            let lbsdataArrays = LbscurrentDailyDepositPayable.memberType || [];

                            if (lbsdataArrays.length > 0) {
                                let dates = lastYearEndDate;
                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);

                                let DdsGrandTotal = 0;
                                let InttPayableTotal = 0;
                                let NetGrandTotal = 0;

                                lbsdataArrays.forEach((row, index) => {
                                    const schid = row.schid || "Unknown";
                                    const shcname = row.schname;
                                    if (!lbsgroupDDSTotal[schid]) {
                                        lbsgroupDDSTotal[schid] = 0;
                                        lbsgroupDDSInterestTotal[schid] = 0;
                                    }

                                    let openingDate = new Date(row.opening_date);
                                    let days = openingDate.getDate();
                                    let month = openingDate.getMonth() + 1;
                                    let year = openingDate.getFullYear();

                                    days = days < 10 ? `0${days}` : days;
                                    month = month < 10 ? `0${month}` : month;
                                    let formattedDate = `${days}-${month}-${year}`;

                                    let ss = parseFloat(row.total_amount || 0);
                                    let ww = parseFloat(row.withdraw || 0);
                                    var principal = ss - ww;
                                    let rate = parseFloat(row.interest || 0);
                                    let months = calculateMonthDifference(openingDate,
                                        endDate);
                                    let rdAmount = calculateRDAmount(principal, rate,
                                        months);
                                    let interest = Math.round(calculateInterest(
                                        principal, rate, months));

                                    let totalInterest = 0;
                                    const quarterlyRate = rate / 4 / 100;
                                    const daysInQuarter = 91;
                                    const totalDays = months * 30.44;
                                    const completedQuarters = Math.floor(totalDays /
                                        daysInQuarter);
                                    const remainingDays = totalDays % daysInQuarter;

                                    let maturityAmount = principal;

                                    for (let i = 0; i < completedQuarters; i++) {
                                        const quarterlyInterest = maturityAmount *
                                            quarterlyRate;
                                        totalInterest += quarterlyInterest;
                                        maturityAmount += quarterlyInterest;
                                    }

                                    if (remainingDays > 0) {
                                        const dailyRate = quarterlyRate / daysInQuarter;
                                        const dailyInterest = maturityAmount *
                                            dailyRate * remainingDays;
                                        totalInterest += dailyInterest;
                                        maturityAmount += dailyInterest;
                                    }

                                    DdsGrandTotal += principal;
                                    InttPayableTotal += totalInterest;
                                    NetGrandTotal += maturityAmount;

                                    row.interestAmount = parseFloat(totalInterest
                                        .toFixed(2));
                                    lbsgroupDDSTotal[schid] += principal;
                                    lbsgroupDDSInterestTotal[schid] += row
                                        .interestAmount;
                                    totalIncome += row.interestAmount;

                                });
                            }

                            //_________________________End Current DDS Collection Interest Payables Member/NonMember/Staff__________________


                            //____________Financial Year 2023-24 Payables/Recoverable data
                            if (Array.isArray(lastpayables) && lastpayables.length > 0) {
                                lastpayables.forEach((data) => {
                                    let types = data.types;
                                    if (types === 'Payables') {
                                        totalIncome += parseFloat(data.amount);
                                    } else {
                                        totalExpenses += parseFloat(data.amount);
                                    }
                                });
                            }

                            //_________________________Current RD Collection Interest Payables Member/NonMember/Staff

                            let grandRdTotal = {};
                            let groupRdInterestTotal = {};
                            let grandRdTotalsssss = 0;

                            let dataRdArrays = currentRdInterestPayable.memberType || [];

                            if (dataRdArrays.length > 0) {
                                libilitiesBody.append(
                                    `<tr>
                                        <td class="tdtext">
                                            <strong>
                                                Interest Payables Recurring Deposit :-
                                            </strong>
                                        </td>
                                        <td class="tdtextvalue"></td>
                                    </tr>`);


                                let dates = document.getElementById('date_till_date').value;

                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);

                                let rdGatndTotal = 0;
                                let InttPayableTotal = 0;
                                let NetGrandTotal = 0;

                                dataRdArrays.forEach((row, index) => {
                                    const schid = row.secheme_id || "Unknown";
                                    const shcname = row.schname;
                                    if (!grandRdTotal[schid]) {
                                        grandRdTotal[schid] = 0;
                                        groupRdInterestTotal[schid] = 0;
                                    }

                                    let transactionDate = new Date(row.date);
                                    let day = transactionDate.getDate().toString()
                                        .padStart(2, '0');
                                    let month = (transactionDate.getMonth() + 1)
                                        .toString().padStart(2, '0');
                                    let year = transactionDate.getFullYear();
                                    let formattedDate = `${day}-${month}-${year}`;

                                    principal = parseFloat(row.amount || 0);
                                    let a = 0;

                                    let interest = 0;
                                    let totalAmount = 0;
                                    let rate = parseFloat(row.interest);
                                    let months = calculateMonthDifference(
                                        transactionDate, endDate);
                                    let rdAmount = calculateRDAmount(principal, rate,
                                        months);
                                    interest = calculateInterest(principal, rate,
                                        months);

                                    if (row.current_status === 'Active') {
                                        a = principal;
                                        totalAmount = principal + interest;
                                        row.interestAmount = parseFloat(interest
                                            .toFixed(2));
                                        grandRdTotal[schid] += principal;
                                        groupRdInterestTotal[schid] += row
                                            .interestAmount;
                                        totalExpenses += row.interestAmount;
                                    }

                                });

                                Object.keys(grandRdTotal).forEach((schid) => {
                                    const matchingRow = dataRdArrays.find(row => String(
                                            row.schid).trim() === String(schid)
                                        .trim());
                                    const shcname = matchingRow ? matchingRow.schname :
                                        "Unknown";

                                    libilitiesBody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(shcname)}</td>
                                            <td class="tdtextvalue">
                                                ${Math.abs(parseFloat(groupRdInterestTotal[schid])).toFixed(2)}
                                            </td>
                                        </tr>`
                                    );

                                    libilitiesgrandTotal += parseFloat(
                                        groupRdInterestTotal[schid]);
                                    grandRdTotalsssss += parseFloat(
                                        groupRdInterestTotal[schid]);
                                });

                                libilitiesBody.append(
                                    `<tr>
                                        <td class="tdtext" colspan="2">Total DDS Interest Payables</td>
                                        <td class="tdtextvalue">
                                            ${Math.abs(parseFloat(grandRdTotalsssss)).toFixed(2)}
                                        </td>
                                    </tr>`
                                );
                            }

                            //_________________________End Members/NonMembers/Staff RD Ineterest Payables__________________

                            let LBSgrandRdTotal = {};
                            let LBSgroupRdInterestTotal = {};

                            let dataRdLBSArrays = LbscurrentRdInterestPayable.memberType || [];

                            if (dataRdLBSArrays.length > 0) {
                                let dates = document.getElementById('date_till_date').value;

                                let [day, month, year] = dates.split('-');
                                let endDate = new Date(`${year}-${month}-${day}`);

                                let rdGatndTotal = 0;
                                let InttPayableTotal = 0;
                                let NetGrandTotal = 0;

                                dataRdLBSArrays.forEach((row, index) => {
                                    const schid = row.secheme_id || "Unknown";
                                    const shcname = row.schname;
                                    if (!LBSgrandRdTotal[schid]) {
                                        LBSgrandRdTotal[schid] = 0;
                                        LBSgroupRdInterestTotal[schid] = 0;
                                    }

                                    let transactionDate = new Date(row.date);
                                    let day = transactionDate.getDate().toString()
                                        .padStart(2, '0');
                                    let month = (transactionDate.getMonth() + 1)
                                        .toString().padStart(2, '0');
                                    let year = transactionDate.getFullYear();
                                    let formattedDate = `${day}-${month}-${year}`;

                                    principal = parseFloat(row.amount || 0);
                                    let a = 0;

                                    let interest = 0;
                                    let totalAmount = 0;
                                    let rate = parseFloat(row.interest);
                                    let months = calculateMonthDifference(
                                        transactionDate, endDate);
                                    let rdAmount = calculateRDAmount(principal, rate,
                                        months);
                                    interest = calculateInterest(principal, rate,
                                        months);

                                    a = principal;
                                    totalAmount = principal + interest;

                                    row.interestAmount = parseFloat(interest.toFixed(
                                    2));
                                    LBSgrandRdTotal[schid] += principal;
                                    LBSgroupRdInterestTotal[schid] += row
                                    .interestAmount;
                                    totalIncome += row.interestAmount;
                                });

                                Object.keys(LBSgrandRdTotal).forEach((schid) => {
                                    const matchingRow = dataRdLBSArrays.find(row =>
                                        String(row.schid).trim() === String(schid)
                                        .trim());
                                    const shcname = matchingRow ? matchingRow.schname :
                                        "Unknown";
                                });
                            }


                            let opening_losses = res.opening_l || 0;
                            let opening_profit = res.opening_p || 0;
                            let current_profit = res.current_profit || 0;
                            let current_losses = res.current_losses || 0;

                            // Calculate net result
                            let netResult = (opening_profit + current_profit) - (
                                opening_losses + current_losses);
                            let isProfit = netResult >= 0;

                            // Append heading
                            if (isProfit) {
                                libilitiesBody.append(
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>
                                                <u>Accumulated Profit</u>
                                            </strong>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>`
                                );
                            } else {
                                assetsBody.append(
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>
                                                <u>Accumulated Losses</u>
                                            </strong>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>`
                                );
                            }

                            // Append components
                            if (opening_losses > 0) {
                                const row =
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>
                                                Opening Losses
                                            </strong>
                                        </td>
                                        <td>${opening_losses.toFixed(2)}</td>
                                        <td></td>
                                    </tr>`;
                                (isProfit ? libilitiesBody : assetsBody).append(row);
                            }

                            if (opening_profit > 0) {
                                const row =
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>Opening Profit</strong>
                                        </td>
                                        <td>
                                            ${opening_profit.toFixed(2)}
                                        </td>
                                        <td></td>
                                    </tr>`;
                                (isProfit ? libilitiesBody : assetsBody).append(row);
                            }

                            if (current_profit > 0) {
                                const row =
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>
                                                Current Net Profit - ${currentfinancialYear}
                                            </strong>
                                        </td>
                                        <td>${current_profit.toFixed(2)}</td>
                                        <td></td>
                                    </tr>`;
                                (isProfit ? libilitiesBody : assetsBody).append(row);
                            }

                            if (current_losses > 0) {
                                const row =
                                    `<tr>
                                        <td style="text-align:left;">
                                            <strong>
                                                Current Net Loss - ${currentfinancialYear}
                                            </strong>
                                        </td>
                                        <td>${current_losses.toFixed(2)}</td>
                                        <td></td>
                                    </tr>`;
                                (isProfit ? libilitiesBody : assetsBody).append(row);
                            }

                            // Append total
                            const totalRow =
                                `<tr>
                                    <td style="text-align:left;">
                                        <strong>
                                            Total ${isProfit ? 'Profit' : 'Losses'}
                                        </strong>
                                    </td>
                                    <td></td>
                                    <td>
                                        <strong>
                                            ${Math.abs(netResult).toFixed(2)}
                                        </strong>
                                    </td>
                                </tr>`;
                            (isProfit ? libilitiesBody : assetsBody).append(totalRow);

                            // Update grand totals
                            if (isProfit) {
                                libilitiesgrandTotal += Math.abs(netResult);
                            } else {
                                assetsgrandTotal += Math.abs(netResult);
                            }

                            let liabilitiesRowCount = assetsBody.find('tr').length;
                            let assetsRowCount = libilitiesBody.find('tr').length;
                            let maxRows = Math.max(liabilitiesRowCount, assetsRowCount);

                            if (liabilitiesRowCount < maxRows) {
                                let rowsToAdd = maxRows - liabilitiesRowCount;
                                for (let i = 0; i < rowsToAdd; i++) {
                                    assetsBody.append(
                                        `<tr>
                                            <td colspan="">&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                        </tr>`
                                    );
                                }
                            }

                            if (assetsRowCount < maxRows) {
                                let rowsToAdd = maxRows - assetsRowCount;
                                for (let i = 0; i < rowsToAdd; i++) {
                                    libilitiesBody.append(
                                        `<tr>
                                            <td colspan="">&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                        </tr>`
                                    );
                                }
                            }

                            libilitiesBody.append(
                                `<tr style="background-color:#7367f0;">
                                    <td style="color:white;" colspan="2">
                                        Liabilities Grand Total
                                    </td>
                                    <td style="color:white;">
                                        ${parseFloat(libilitiesgrandTotal).toFixed(2)}
                                    </td>
                                </tr>`
                            );
                            assetsBody.append(
                                `<tr style="background-color:#7367f0;">
                                    <td style="color:white;" colspan="2">
                                        Assets Grand Total
                                    </td>
                                    <td style="color:white;">
                                        ${parseFloat(assetsgrandTotal).toFixed(2)}
                                    </td>
                                </tr>`
                            );

                        } else {
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });
        });

        function getFinancialYear(startDate, endDate) {
            let start_date = new Date(startDate);
            let end_date = new Date(endDate);

            let financialYear;
            let month = start_date.getMonth() + 1;

            if (month >= 4) {
                financialYear = `${start_date.getFullYear()}-${start_date.getFullYear() + 1}`;
            } else {
                financialYear = `${start_date.getFullYear() - 1}-${start_date.getFullYear()}`;
            }
            return financialYear;
        }

        function calculateRDAmount(principal, rate, months) {
            var rdAmount = principal;
            for (var i = 0; i < months; i++) {
                var interest = (rdAmount * rate) / (12 * 100);
                rdAmount += interest;
            }
            return rdAmount;
        }

        function calculateInterest(principal, rate, openingDate, endDate) {
            var rdAmount = calculateRDAmount(principal, rate, openingDate, endDate);
            return rdAmount - principal;
        }

        function calculateMonthDifference(openingDate, endDate) {
            var diff = (endDate.getTime() - openingDate.getTime()) / 1000;
            diff /= (60 * 60 * 24 * 7 * 4);
            return Math.abs(Math.round(diff));
        }

        function calculateTotalInterest(interestType, principal, rate, daysElapsed) {
            let totalInterest = 0;
            if (interestType === 'Fixed') {
                totalInterest = (principal * rate / 100) * (daysElapsed / 365);
            } else if (interestType === 'AnnualCompounded') {
                totalInterest = principal * (Math.pow(1 + rate / 100, daysElapsed / 365) - 1);
            } else if (interestType === 'QuarterlyCompounded') {
                const quarterlyRate = rate / 4 / 100;
                const completedQuarters = Math.round(daysElapsed / 91);
                let maturityAmount = principal;
                for (let i = 0; i < completedQuarters; i++) {
                    const quarterlyInterest = maturityAmount * quarterlyRate;
                    totalInterest += quarterlyInterest;
                    maturityAmount += quarterlyInterest;
                }
                if (completedQuarters == 0) {
                    const remainingDays = daysElapsed % 91;
                    if (remainingDays > 0) {
                        const dailyRate = quarterlyRate / 91;
                        totalInterest += maturityAmount * dailyRate * remainingDays;
                    }
                }
            }
            return Math.round(totalInterest);
        }

        function calculateDateDifference(start, end) {
            let [startDay, startMonth, startYear] = start.split('-').map(Number);
            let [endDay, endMonth, endYear] = end.split('-').map(Number);

            let startDate = new Date(startYear, startMonth - 1, startDay);
            let endDate = new Date(endYear, endMonth - 1, endDay);

            let years = endYear - startYear;
            let months = endMonth - startMonth;
            let days = endDay - startDay;

            if (days < 0) {
                months--;
                days += new Date(endYear, endMonth - 1, 0).getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            return {
                years,
                months,
                days
            };
        }

        function calculateInterestAmount(principal, rate, interestType, years, months, days) {

            var totalDays = (years * 365) + (months * 30) + days;
            var totalYears = Math.floor(totalDays / 365);
            var remainingDaysAfterYears = totalDays % 365;
            var fullQuarters = Math.floor(remainingDaysAfterYears / 90);
            var extraDays = remainingDaysAfterYears % 90;



            var maturityAmount = principal;
            var interest = 0;

            if (interestType === 'QuarterlyCompounded') {
                var n = 4;
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
            return interest;
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

        function printReport() {
            var printContents = document.getElementById('balancesheetdiv').innerHTML;
            var originalContents = document.body.innerHTML;
            var startDate = $('#date_from').val();

            var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";

            // Replace Blade variables with actual PHP-rendered values
            var header = `
                <div style="text-align: center;">
                    <h4>THE Sirmour Co-Operative NATC Society LTD</h4>
                    {{--  <h6>VILL AND PO BARI</h6>  --}}
                    <h6>{{ \Carbon\Carbon::parse(request('start_date'))->format('d-M-Y') }} To {{ \Carbon\Carbon::parse(request('end_date'))->format('d-M-Y') }}</h6>
                </div>
            `;

            printContents = css + header + printContents;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
@endpush
