@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- <h4 class="py-2"><span class="text-muted fw-light">Reports / General Reports /</span> Recurring Deposit Report</h4> -->
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Profit & Loss</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body">

                        <form name="profitLossForm" id="profitLossForm">
                            <div class="row">
                                @php
                                    $currentDate =
                                        Session::get('currentdate') ??
                                        date('d-m-Y', strtotime(session('sessionStart')));
                                @endphp
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="startDate" class="form-label">Date From</label>
                                    <input type="text" class="form-control formInputsReport onlydate" id="startDate"
                                        name="startDate" value="{{ date('d-m-Y', strtotime(session('sessionStart'))) }}">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                    <label for="DATE" class="form-label">DATE TO</label>
                                    <input type="text" class="form-control formInputsReport onlydate"
                                        placeholder="YYYY-MM-DD" id="enddate" name="enddate"
                                        value="{{ date('d-m-Y', strtotime(session('sessionEnd'))) }}" />
                                </div>
                                <div
                                    class="col-lg-7 col-md-6 col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                    <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                        <button type="submit" id="viewReportBtn"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">View</button>

                                        <button type="button" id="printButton" onclick="printReport()"
                                            class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                            Print
                                        </button>
                                        <div class="ms-2 dropdown">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tableSection row mb-4">
            <div class="col-lg-12 col-md-12 mb-4" id="sharelistprint">
                <div class="card">
                    <div class="card-body px-0 pb-2">
                        <div class="row">
                            <div class="table-responsive col-sm-6">
                                <table class="table mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th
                                                style="font-size: 16px; background-color: #7367f0; text-transform: uppercase; color: #fff;">
                                                Expenses</th>
                                        </tr>
                                    </thead>
                                </table>
                                <table class="table align-items-center text-center table-bordered mb-0 data-table">
                                    <thead class="tableHeading">
                                        <tr>
                                        <tr>
                                            <th><strong>Particular</strong></th>
                                            <th><strong>Amount</strong></th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody class="exptablebody" id="exptablebody">
                                    </tbody>
                                    <tbody id="netLossRow">
                                        <tr>
                                            {{--  <td id="netloss"></td>  --}}
                                            {{--  <td id="netlossAmount"></td>  --}}
                                        </tr>
                                    </tbody>

                                </table>
                                <div class="d-flex justify-content-end mt-3">

                                </div>
                            </div>
                            <div class="table-responsive col-sm-6">
                                <!-- <div id="loader" style="display: none;">
                                                <div class="dot-loader"></div>
                                                <div class="dot-loader dot-loader--2"></div>
                                                <div class="dot-loader dot-loader--3"></div>
                                            </div> -->
                                <table class="table mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th
                                                style="font-size: 16px; background-color: #7367f0; text-transform: uppercase; color: #fff;">
                                                Incomes</th>
                                        </tr>
                                    </thead>
                                </table>
                                <table class="table align-items-center text-center table-bordered mb-0 data-table">
                                    <thead class="tableHeading">
                                        <tr>
                                            <th><strong>Particular</strong></th>
                                            <th><strong>Amount</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody class="inctablebody" id="inctablebody">
                                    </tbody>
                                    <tbody id="netprofitRow">
                                        <tr>
                                            {{--  <td id="netprofit"></td>
                                        <td id="netprofitAmount"></td>  --}}
                                        </tr>
                                    </tbody>


                                </table>
                                <div class="d-flex justify-content-end mt-3">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('script')
    <script>
        function printReport() {
            var shareListPrint = document.getElementById('sharelistprint');
            if (shareListPrint) {
                var printContents = shareListPrint.innerHTML;
                var originalContents = document.body.innerHTML;
                var start_date = $("#startDate").val();
                var end_date = $("#enddate").val();
                var css = "<style>@media print { body { background-color: #ffffff; margin-top: .5rem; } }</style>";
                $('.table').css('border', '1px solid');

                // Add header for printing
                var header = `
                <div style="text-align: center;">
                    <h4>{{ $branch->name }}</h4>
                    <h6>{{ $branch->address }}</h6>
                    <h6> Profit & Loss From ` + formatDate(start_date) + ` To ` + formatDate(end_date) + `</h6>
                </div>
            `;
                printContents = css + header + printContents;

                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            } else {
                console.error("Element with ID 'sharelistprint' not found.");
            }
        }

        const dateInputs = document.querySelectorAll('.onlydate');
        dateInputs.forEach(dateInput => {
            dateInput.addEventListener('input', function(e) {
                let inputValue = e.target.value;
                inputValue = inputValue.replace(/[^\d]/g, '').slice(0, 8);
                if (inputValue.length >= 2 && inputValue.charAt(2) !== '-') {
                    inputValue = `${inputValue.slice(0, 2)}-${inputValue.slice(2)}`;
                }
                if (inputValue.length >= 5 && inputValue.charAt(5) !== '-') {
                    inputValue = `${inputValue.slice(0, 5)}-${inputValue.slice(5)}`;
                }
                e.target.value = inputValue;
            });
        });

        function capitalizeWords(str) {
            if (!str) return ''; // Handle null, undefined, or empty input
            return str.toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function calculateInterestAmount(principal, rate, interestType, years, months, days) {

            var totalDays = (years * 365) + (months * 30) + days; // Convert all to days
            var totalYears = Math.floor(totalDays / 365);
            var remainingDaysAfterYears = totalDays % 365;
            var fullQuarters = Math.floor(remainingDaysAfterYears / 90);
            var extraDays = remainingDaysAfterYears % 90;

            {{--  console.log(totalDays,totalYears,remainingDaysAfterYears,fullQuarters,extraDays);  --}}

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
            return interest;
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

        document.getElementById('profitLossForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let startDate = document.getElementById('startDate').value;
            let endDate = document.getElementById('enddate').value;

            $.ajax({
                url: "{{ route('getprofitlossdetails') }}",
                type: 'post',
                data: {startDate: startDate,endDate: endDate},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let currentfinancialYear = res.currentfinancialYear;
                        let incomes = res.incomes ?? [];
                        let expenses = res.expenses ?? [];
                        let lastYearEndDate = DateFormat(res.lastYearEndDate);

                        let bankInterestRecoverable = res.bankInterestRecoverable || 0;
                        let currentLoanRecoverable = res.currentLoanRecoverable || 0;
                        let currentFdInterestPayable = res.currentFdInterestPayable || [];
                        let currentDailyDepositPayable = res.currentDailyDepositPayable || [];
                        let lastpayables = res.lastpayables || [];
                        let lastfinancialYear = res.lastfinancialYear;

                        let LbsbankInterestRecoverable = res.LbsbankInterestRecoverable || 0;
                        let LbscurrentLoanRecoverable = res.LbscurrentLoanRecoverable || 0;
                        let LbscurrentFdInterestPayable = res.LbscurrentFdInterestPayable || [];
                        let LbscurrentDailyDepositPayable = res.LbscurrentDailyDepositPayable || [];
                        let LbscurrentRdInterestPayable = res.LbscurrentRdInterestPayable || [];




                        let custom_2023_2024_pay_recoverable = res.custom_2023_2024_pay_recoverable || [];
                        let custom_2022_2023_pay_recoverables = res.custom_2022_2023_pay_recoverables || [];
                        let currentRdInterestPayable = res.currentRdInterestPayable || [];


                        let inctablebody = $('#inctablebody');
                        inctablebody.empty();

                        let exptablebody = $('#exptablebody');
                        exptablebody.empty();

                        let grandInterestTotal = 0;
                        let previousYearRdInttPayableTotal = 0;
                        let totalIncome = 0;
                        let totalExpenses = 0;

                        $('#netprofitRow').empty();

                        //_____________Income && Expenses
                        let maxRow = Math.max(incomes.length, expenses.length);
                        for (let i = 0; i < maxRow; i++) {
                            // Expenses Section
                            if (i < expenses.length) {
                                let expense = parseFloat(expenses[i].debit) || 0;
                                let expense_credit = parseFloat(expenses[i].credit) || 0;
                                let expbalance = expense - expense_credit;


                                exptablebody.append(
                                    `<tr><td class="tdtext">${capitalizeWords(expenses[i].ledger_name)}</td><td class="tdtextvalue">${Math.abs(parseFloat(expbalance)).toFixed(2)}</td></tr>`
                                );
                                totalExpenses += parseFloat(expbalance);
                            }

                            // Incomes Section
                            if (i < incomes.length) {
                                let income = parseFloat(incomes[i].total_income) || 0;
                                let income_debit = parseFloat(incomes[i].total_income_debit) || 0;
                                let incomebalancesss = income - income_debit;

                                inctablebody.append(
                                    `<tr><td class="tdtext">${capitalizeWords(incomes[i].ledger_name)}</td><td class="tdtextvalue">${Math.abs(parseFloat(incomebalancesss)).toFixed(2)}</td></tr>`
                                );

                                totalIncome += parseFloat(incomebalancesss);
                            }
                        }

                        //_____________End Income && Expenses



                        //____________Current Bank FD Interest Recoverables
                        bankInterestRecoverable.forEach((data, index) => {
                            let principal = parseFloat(data.principal_amount) || 0;
                            let rate = parseFloat(data.interest_rate) || 0;
                            let interestType = data.interest_type || 'QuarterlyCompounded';

                            let dateDiff = calculateDateDifference(DateFormat(data.fd_date),
                                endDate);
                            let interestAmount = calculateInterestAmount(principal, rate,
                                interestType, dateDiff.years, dateDiff.months, dateDiff.days
                                );
                            if (interestAmount > 0) {
                                inctablebody.append(
                                    `<tr><td class="tdtext"><strong>Interest Recoverable :-</strong></td><td class="tdtextvalue"></td></tr>`
                                    );
                                inctablebody.append(
                                    `<tr><td class="tdtext">Bank ${(data.bank_name).toUpperCase()} :-</td><td class="tdtextvalue">${Math.round(parseFloat(interestAmount)).toFixed(2)}</td></tr>`
                                    );
                                totalIncome += parseFloat(interestAmount);
                            }
                        });

                        if (currentLoanRecoverable > 0) {
                            inctablebody.append(
                                `<tr><td class="tdtext">Member Loan Intt. Recoverable :- </td><td class="tdtextvalue">${Math.round((parseFloat(currentLoanRecoverable))).toFixed(2)}</td></tr>`
                                );
                            totalIncome += parseFloat(currentLoanRecoverable);
                        }
                        //____________End Bank FD Interest Recoverables



                        //_________________________Current Members/NonMembers/Staff FD Ineterest Payables__________________
                        let groupTotal = {};
                        let groupInterestTotal = {};

                        let dataArray = currentFdInterestPayable || {};

                        if (dataArray && dataArray.length > 0) {
                            exptablebody.append(`<tr>
                                <td class="tdtext"><strong>Interest Recoverable:-</strong></td>
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

                                var [day, month, year] = endDate.split('-').map(Number);
                                var currentDate = new Date(year, month - 1, day);
                                var openingDate = new Date(row.openingDate);

                                var daysElapsed = Math.round((currentDate - openingDate) / (
                                    1000 * 60 * 60 * 24));
                                var interest = 0;

                                if (daysElapsed >= 0) {
                                    interest = calculateTotalInterest(row.interestType,
                                        principal, row.interestRate, daysElapsed);
                                }

                                row.interestAmount = parseFloat(interest.toFixed(2));
                                groupTotal[fdType] += totalAmount;
                                groupInterestTotal[fdType] += row.interestAmount;
                                totalExpenses += row.interestAmount;


                            });


                            Object.keys(groupTotal).forEach((fdType) => {
                                const fdTypeLabel = dataArray.find(row => row.fdType === fdType)
                                    ?.fdname || "Unknown";

                                exptablebody.append(
                                    `<tr>
                                        <td class="tdtext">${fdTypeLabel}</td>
                                        <td class="tdtextvalue">${Math.abs(parseFloat(groupInterestTotal[fdType])).toFixed(2)}</td>
                                    </tr>`
                                );

                            });
                        }

                        //_________________________End Members/NonMembers/Staff FD Ineterest Payables__________________


                         //_________________________Current RD Collection Interest Payables Member/NonMember/Staff

                         let grandRdTotal = {};
                         let groupRdInterestTotal = {};

                         let dataRdArrays = currentRdInterestPayable.memberType || [];

                         if (dataRdArrays.length > 0) {
                             exptablebody.append(`<tr>
                                 <td class="tdtext"><strong>Interest Payables Recurring Deposit :-</strong></td>
                                 <td class="tdtextvalue"></td>
                             </tr>`);


                             let dates = document.getElementById('enddate').value;

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
                                 let day = transactionDate.getDate().toString().padStart(2, '0');
                                 let month = (transactionDate.getMonth() + 1).toString().padStart(2, '0');
                                 let year = transactionDate.getFullYear();
                                 let formattedDate = `${day}-${month}-${year}`;

                                principal = parseFloat(row.amount || 0);
                                let a = 0;

                                let interest = 0;
                                let totalAmount = 0;
                                let rate = parseFloat(row.interest);
                                let months = calculateMonthDifference(transactionDate, endDate);
                                let rdAmount = calculateRDAmount(principal, rate, months);
                                interest = calculateInterest(principal, rate, months);
                                a = principal;
                                totalAmount = principal + interest;




                                 row.interestAmount = parseFloat(interest.toFixed(2));
                                 grandRdTotal[schid] += principal;
                                 groupRdInterestTotal[schid] += row.interestAmount;
                                 totalExpenses += row.interestAmount;

                             });

                             Object.keys(grandRdTotal).forEach((schid) => {
                                 const matchingRow = dataRdArrays.find(row => String(row.schid)
                                     .trim() === String(schid).trim());
                                 const shcname = matchingRow ? matchingRow.schname : "Unknown";

                                 exptablebody.append(
                                     `<tr>
                                         <td class="tdtext">${capitalizeWords(shcname)}</td>
                                         <td class="tdtextvalue">${Math.abs(Math.round(parseFloat(groupRdInterestTotal[schid]))).toFixed(2)}</td>
                                     </tr>`
                                 );

                             });
                         }

                        //_________________________End Members/NonMembers/Staff RD Ineterest Payables__________________



                        //_________________________Current DDS Collection Interest Payables Member/NonMember/Staff

                        let groupDDSTotal = {};
                        let groupDDSInterestTotal = {};

                        let dataArrays = currentDailyDepositPayable.memberType || [];

                        if (dataArrays.length > 0) {
                            exptablebody.append(`<tr>
                                <td class="tdtext"><strong>Interest Payables Daily Deposit :-</strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);

                            let dates = document.getElementById('enddate').value;
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
                                let months = calculateMonthDifference(openingDate, endDate);
                                let rdAmount = calculateRDAmount(principal, rate, months);
                                let interest = Math.round(calculateInterest(principal, rate,
                                    months));

                                let totalInterest = 0;
                                const quarterlyRate = rate / 4 / 100;
                                const daysInQuarter = 91;
                                const totalDays = months * 30.44;
                                const completedQuarters = Math.floor(totalDays / daysInQuarter);
                                const remainingDays = totalDays % daysInQuarter;

                                let maturityAmount = principal;

                                for (let i = 0; i < completedQuarters; i++) {
                                    const quarterlyInterest = maturityAmount * quarterlyRate;
                                    totalInterest += quarterlyInterest;
                                    maturityAmount += quarterlyInterest;
                                }

                                if (remainingDays > 0) {
                                    const dailyRate = quarterlyRate / daysInQuarter;
                                    const dailyInterest = maturityAmount * dailyRate *
                                        remainingDays;
                                    totalInterest += dailyInterest;
                                    maturityAmount += dailyInterest;
                                }

                                DdsGrandTotal += principal;
                                InttPayableTotal += totalInterest;
                                NetGrandTotal += maturityAmount;

                                row.interestAmount = parseFloat(totalInterest.toFixed(2));
                                groupDDSTotal[schid] += principal;
                                groupDDSInterestTotal[schid] += row.interestAmount;
                                totalExpenses += row.interestAmount;

                            });

                            Object.keys(groupDDSTotal).forEach((schid) => {
                                const matchingRow = dataArrays.find(row => String(row.schid)
                                    .trim() === String(schid).trim());
                                const shcname = matchingRow ? matchingRow.schname : "Unknown";

                                exptablebody.append(
                                    `<tr>
                                        <td class="tdtext">${capitalizeWords(shcname)}</td>
                                        <td class="tdtextvalue">${Math.abs(Math.round(parseFloat(groupDDSInterestTotal[schid]))).toFixed(2)}</td>
                                    </tr>`
                                );

                            });
                        }

                        //_________________________End Current DDS Collection Interest Payables Member/NonMember/Staff__________________


                        //____________Financial Year Setups 2023-24 Payables/Recoverable data
                        if (Array.isArray(custom_2023_2024_pay_recoverable) &&
                            custom_2023_2024_pay_recoverable.length > 0) {
                            inctablebody.append(`<tr>
                                <td class="tdtext"><strong><u>Interest Recoverable Fy-${currentfinancialYear}</u></strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);
                            exptablebody.append(`<tr>
                                <td class="tdtext"><strong><u>Interest Payables Fy-${currentfinancialYear}</u></strong></td>
                                <td class="tdtextvalue"></td> inctablebody
                            </tr>`);
                            custom_2023_2024_pay_recoverable.forEach((data) => {
                                let types = data.types;
                                if (types === 'Payables') {

                                    exptablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );

                                    totalExpenses += parseFloat(data.amount);

                                } else {
                                    inctablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );

                                    totalIncome += parseFloat(data.amount);
                                }
                            });
                        }


                        //____________Financial Year Setups 2023-24 Payables/Recoverable data
                        if (Array.isArray(custom_2022_2023_pay_recoverables) &&
                            custom_2022_2023_pay_recoverables.length > 0) {

                            inctablebody.append(`<tr>
                                <td class="tdtext"><strong><u> LBS : -  Interest Payables Fy-${lastfinancialYear} </u></strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);
                            exptablebody.append(`<tr>
                                <td class="tdtext"><strong><u> LBS : - Interest Recovereable FD Fy-${lastfinancialYear} </u></strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);
                            custom_2022_2023_pay_recoverables.forEach((data) => {
                                let types = data.types;
                                if (types === 'Payables') {

                                    inctablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );

                                    totalIncome += parseFloat(data.amount);
                                } else {
                                    exptablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );

                                    totalExpenses += parseFloat(data.amount);
                                }
                            });
                        }






                        //____________Financial Year 2023-24 Payables/Recoverable data

                        if (Array.isArray(lastpayables) && lastpayables.length > 0) {
                            inctablebody.append(`<tr>
                                <td class="tdtext"><strong>LBS Interest Payables FD Fy-${lastfinancialYear} :-</strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);
                            exptablebody.append(`<tr>
                                <td class="tdtext"><strong>Interest Recoverable Fy-${lastfinancialYear} :-</strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);
                            lastpayables.forEach((data) => {
                                let types = data.types;
                                if (types === 'Payables') {

                                    inctablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );


                                    totalIncome += parseFloat(data.amount);

                                } else {
                                    exptablebody.append(
                                        `<tr>
                                            <td class="tdtext">${capitalizeWords(data.name)}</td>
                                            <td class="tdtextvalue">${Math.abs(parseFloat(data.amount)).toFixed(2)}</td>
                                        </tr>`
                                    );

                                    totalExpenses += parseFloat(data.amount);
                                }
                            });
                        }



                        //_______________________ LBS Payables && Recoverable ______________________________


                        //____________LBS Bank FD Interest Recoverables
                        if (Array.isArray(LbsbankInterestRecoverable) && LbsbankInterestRecoverable
                            .length > 0) {
                            LbsbankInterestRecoverable.forEach((data, index) => {
                                let principal = parseFloat(data.principal_amount) || 0;
                                let rate = parseFloat(data.interest_rate) || 0;
                                let interestType = data.interest_type || 'QuarterlyCompounded';

                                let dateDiff = calculateDateDifference(DateFormat(data.fd_date),
                                    lastYearEndDate);
                                let interestAmount = calculateInterestAmount(principal, rate,
                                    interestType, dateDiff.years, dateDiff.months, dateDiff
                                    .days);
                                if (interestAmount > 0) {
                                    exptablebody.append(
                                        `<tr><td class="tdtext"><strong>LBS :- Interest Recoverable  ${lastfinancialYear}</strong></td><td class="tdtextvalue"></td></tr>`
                                        );
                                    exptablebody.append(
                                        `<tr><td class="tdtext">Bank ${(data.bank_name).toUpperCase()}  :-  ${lastfinancialYear}</td><td class="tdtextvalue">${Math.round(parseFloat(interestAmount)).toFixed(2)}</td></tr>`
                                        );
                                    totalExpenses += parseFloat(interestAmount);

                                }
                            });
                        }

                        if (LbscurrentLoanRecoverable > 0) {
                            exptablebody.append(
                                `<tr><td class="tdtext"><strong>LBS :- Interest Recoverable ${lastfinancialYear}</strong></td><td class="tdtextvalue"></td></tr>`
                                );
                            exptablebody.append(
                                `<tr><td class="tdtext">LBS:- Member Loan Intt. Recoverable ${lastfinancialYear}</td><td class="tdtextvalue">${(parseFloat(LbscurrentLoanRecoverable)).toFixed(2)}</td></tr>`
                                );
                            totalExpenses += parseFloat(LbscurrentLoanRecoverable);
                        }




                        //_________________________LBS Members/NonMembers/Staff FD Ineterest Payables__________________
                        let lbsgroupTotal = {};
                        let lbsgroupInterestTotal = {};

                        let lbsdataArray = LbscurrentFdInterestPayable || {};

                        if (lbsdataArray && lbsdataArray.length > 0) {

                            inctablebody.append(`<tr>
                                <td class="tdtext"><strong>LBS :- Interest Payables FD Fy-${lastfinancialYear} :-</strong></td>
                                <td class="tdtextvalue"></td>
                            </tr>`);


                            Object.keys(lbsdataArray).forEach((key) => {
                                const row = lbsdataArray[key];
                                const fdType = row.fdType || "Unknown";
                                const fdTypeLabel = row.fdname || "Unknown";

                                if (!lbsgroupTotal[fdType]) {
                                    lbsgroupTotal[fdType] = 0;
                                    lbsgroupInterestTotal[fdType] = 0;
                                }

                                var principal = parseFloat(row.principalAmount);
                                var totalAmount = principal;

                                var [day, month, year] = lastYearEndDate.split('-').map(Number);
                                var currentDate = new Date(year, month - 1, day);
                                var openingDate = new Date(row.openingDate);

                                var daysElapsed = Math.round((currentDate - openingDate) / (
                                    1000 * 60 * 60 * 24));
                                var interest = 0;

                                if (daysElapsed >= 0) {
                                    interest = calculateTotalInterest(row.interestType,
                                        principal, row.interestRate, daysElapsed);
                                }

                                row.interestAmount = parseFloat(interest.toFixed(2));
                                lbsgroupTotal[fdType] += totalAmount;
                                lbsgroupInterestTotal[fdType] += row.interestAmount;
                                totalIncome += row.interestAmount;

                            });


                            Object.keys(lbsgroupTotal).forEach((fdType) => {
                                const fdTypeLabel = lbsdataArray.find(row => row.fdType ===
                                    fdType)?.fdname || "Unknown";

                                inctablebody.append(
                                    `<tr>
                                        <td class="tdtext">${fdTypeLabel}</td>
                                        <td class="tdtextvalue">${Math.abs(parseFloat(lbsgroupInterestTotal[fdType])).toFixed(2)}</td>
                                    </tr>`
                                );

                                {{--  totalIncome += parseFloat(lbsgroupInterestTotal[fdType]);  --}}
                            });
                        }

                        //_________________________End Members/NonMembers/Staff FD Ineterest Payables__________________






                        let LBSgrandRdTotal = {};
                         let LBSgroupRdInterestTotal = {};

                         let dataRdLBSArrays = LbscurrentRdInterestPayable.memberType || [];

                         if (dataRdLBSArrays.length > 0) {
                            inctablebody.append(`<tr>
                                 <td class="tdtext"><strong>LBS Interest Payables Recurring Deposit :-</strong></td>
                                 <td class="tdtextvalue"></td>
                             </tr>`);


                             let dates = document.getElementById('enddate').value;

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
                                 let day = transactionDate.getDate().toString().padStart(2, '0');
                                 let month = (transactionDate.getMonth() + 1).toString().padStart(2, '0');
                                 let year = transactionDate.getFullYear();
                                 let formattedDate = `${day}-${month}-${year}`;

                                principal = parseFloat(row.amount || 0);
                                let a = 0;

                                let interest = 0;
                                let totalAmount = 0;
                                let rate = parseFloat(row.interest);
                                let months = calculateMonthDifference(transactionDate, endDate);
                                let rdAmount = calculateRDAmount(principal, rate, months);
                                interest = calculateInterest(principal, rate, months);
                                a = principal;
                                totalAmount = principal + interest;




                                 row.interestAmount = parseFloat(interest.toFixed(2));
                                 LBSgrandRdTotal[schid] += principal;
                                 LBSgroupRdInterestTotal[schid] += row.interestAmount;
                                 totalExpenses += row.interestAmount;

                             });

                             Object.keys(LBSgrandRdTotal).forEach((schid) => {
                                 const matchingRow = dataRdLBSArrays.find(row => String(row.schid)
                                     .trim() === String(schid).trim());
                                 const shcname = matchingRow ? matchingRow.schname : "Unknown";

                                 inctablebody.append(
                                     `<tr>
                                         <td class="tdtext">${capitalizeWords(shcname)}</td>
                                         <td class="tdtextvalue">${Math.abs(Math.round(parseFloat(LBSgroupRdInterestTotal[schid]))).toFixed(2)}</td>
                                     </tr>`
                                 );

                             });
                         }



                        //_________________________LBS DDS Collection Interest Payables Member/NonMember/Staff

                        let lbsgroupDDSTotal = {};
                        let lbsgroupDDSInterestTotal = {};

                        let lbsdataArrays = LbscurrentDailyDepositPayable.memberType || [];

                        if (lbsdataArrays.length > 0) {
                            inctablebody.append(`<tr>
                                 <td class="tdtext"><strong>LBS :- Interest Payables Daily Deposit Fy-${lastfinancialYear} :-</strong></td>
                                 <td class="tdtextvalue"></td>
                             </tr>`);

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
                                let months = calculateMonthDifference(openingDate, endDate);
                                let rdAmount = calculateRDAmount(principal, rate, months);
                                let interest = Math.round(calculateInterest(principal, rate,
                                    months));

                                let totalInterest = 0;
                                const quarterlyRate = rate / 4 / 100;
                                const daysInQuarter = 91;
                                const totalDays = months * 30.44;
                                const completedQuarters = Math.floor(totalDays / daysInQuarter);
                                const remainingDays = totalDays % daysInQuarter;

                                let maturityAmount = principal;

                                for (let i = 0; i < completedQuarters; i++) {
                                    const quarterlyInterest = maturityAmount * quarterlyRate;
                                    totalInterest += quarterlyInterest;
                                    maturityAmount += quarterlyInterest;
                                }

                                if (remainingDays > 0) {
                                    const dailyRate = quarterlyRate / daysInQuarter;
                                    const dailyInterest = maturityAmount * dailyRate *
                                        remainingDays;
                                    totalInterest += dailyInterest;
                                    maturityAmount += dailyInterest;
                                }

                                DdsGrandTotal += principal;
                                InttPayableTotal += totalInterest;
                                NetGrandTotal += maturityAmount;

                                row.interestAmount = parseFloat(totalInterest.toFixed(2));
                                lbsgroupDDSTotal[schid] += principal;
                                lbsgroupDDSInterestTotal[schid] += row.interestAmount;
                                totalIncome += row.interestAmount;
                            });

                            Object.keys(lbsgroupDDSTotal).forEach((schid) => {
                                const matchingRow = lbsdataArrays.find(row => String(row.schid)
                                    .trim() === String(schid).trim());
                                const shcname = matchingRow ? matchingRow.schname : "Unknown";

                                inctablebody.append(
                                    `<tr>
                                         <td class="tdtext">${capitalizeWords(shcname)}</td>
                                         <td class="tdtextvalue">${Math.abs(Math.round(parseFloat(lbsgroupDDSInterestTotal[schid]))).toFixed(2)}</td>
                                     </tr>`
                                );
                            });
                        }

                        //_________________________End Current DDS Collection Interest Payables Member/NonMember/Staff__________________

                        let allexpenses = parseFloat(totalExpenses);
                        let allincomes = parseFloat(totalIncome);
                        let sessionId = @json(session('sessionId'));
                        let netprofit = 0;
                        let netlosses = 0;




                        let differeamount = totalIncome - totalExpenses;

                        if (differeamount > 0) {

                            exptablebody.append(
                                `<tr>
                                    <td class="tdtext"><strong>Net Profit</strong></td>
                                    <td class="tdtextvalue">${Math.abs(Math.round(parseFloat(differeamount))).toFixed(2)}<strong></td>
                                </tr>`
                            );
                            totalExpenses += differeamount;

                            netprofit += Math.abs(Math.round(parseFloat(differeamount)));



                        } else {
                            inctablebody.append(
                                `<tr>
                                    <td class="tdtext"><strong>Net Loss</strong></td>
                                    <td class="tdtextvalue"><strong>${Math.abs(Math.round(parseFloat(differeamount))).toFixed(2)}</strong></td>
                                </tr>`
                            );

                            netlosses += Math.abs(Math.round(parseFloat(differeamount)));



                            totalIncome += Math.abs(Math.round(parseFloat(differeamount)));
                        }

                        updateExpenseIncomeProfitLosses(allexpenses,allincomes,netprofit,netlosses,sessionId);


                        let inctablebodyRowCount = inctablebody.find('tr').length;
                        let exptablebodyRowCount = exptablebody.find('tr').length;
                        let maxRows = Math.max(inctablebodyRowCount, exptablebodyRowCount);

                        if (inctablebodyRowCount < maxRows) {
                            let rowsToAdd = maxRows - inctablebodyRowCount;
                            for (let i = 0; i < rowsToAdd; i++) {
                                inctablebody.append(`<tr><td colspan="">&nbsp;</td><td></td></tr>`);
                            }
                        }

                        if (exptablebodyRowCount < maxRows) {
                            let rowsToAdd = maxRows - exptablebodyRowCount;
                            for (let i = 0; i < rowsToAdd; i++) {
                                exptablebody.append(`<tr><td colspan="">&nbsp;</td><td></td></tr>`);
                            }
                        }

                        inctablebody.append(
                            `<tr style="background-color : #7367f0;"><td style="color:white;">Grand Total Income</td><td style="color:white;">${Math.round(parseFloat(totalIncome)).toFixed(2)}</td></tr>`
                            );

                        exptablebody.append(
                            `<tr style="background-color : #7367f0;"><td style="color:white;">Grand Total Expenses</td><td class="tdtextvalue" style="color:white;">${Math.round(parseFloat(totalExpenses)).toFixed(2)}</td></tr>`
                            );

                    } else {
                        notify(res.messages, 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    notify(res.messages || error, 'warning');
                }
            });
        });



        function updateExpenseIncomeProfitLosses(allexpenses,allincomes,netprofit,netlosses,sessionId){
            $.ajax({
                url : "{{ route('updateExpenseIncomeProfitLosses') }}",
                type : 'post',
                data : {
                    allexpenses : allexpenses,
                    allincomes : allincomes,
                    netprofit : netprofit,
                    netlosses : netlosses,
                    sessionId : sessionId
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){

                    }else{

                    }
                },error : function(error,status,xhr){
                    notify(error,'warning');
                }
            });
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
    </script>
@endpush

@push('style')
    <style>
        .tdtext {
            text-align: left;
        }

        .tdtextvalue {
            text-align: right;
        }
    </style>
@endpush
