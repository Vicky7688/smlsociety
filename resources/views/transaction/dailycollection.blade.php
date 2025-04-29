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
            font-size: 11px
        }

        .right-img {
            text-align: center
        }

        .modal-footer {
            padding: 11px;
        }

        .right-img img {
            width: 100%;
            border: 1px solid #ddd;
            min-width: 70px;
            min-height: 70px;
            margin: 0 auto;
        }

        .paddingl-r-0 {
            padding-left: 0;
            padding-right: 0;
        }

        .w100 {
            width: 100%;
        }

        .mauto {
            margin: 0 auto
        }

        textarea {
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #6f6b7d;
            padding: 10px
        }

        .account-right {
            height: 457px;
        }

        .h100 {
            height: 100%;
        }

        .accountList {
            {{--  position: absolute;  --}}
            left: 12px;
            bottom: 0px;
            {{--  transform: translateY(90%);
            width: calc(100% - 24px);  --}}
            background-color: aliceblue;
            border: 1px solid #fff;
            border-radius: 5px;
            max-height: 100px;
            overflow-y: auto;
            z-index: 99;
            padding-left: 11px;
        }

        .accountList ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }

        .accountListt ul {
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
        }

        .accountListt ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }




        .accountpaidnolist {
            {{--  position: absolute;  --}}
            left: 12px;
            bottom: 0px;
            {{--  transform: translateY(90%);
            width: calc(100% - 24px);  --}}
            background-color: aliceblue;
            border: 1px solid #fff;
            border-radius: 5px;
            max-height: 100px;
            overflow-y: auto;
            z-index: 99;
            padding-left: 11px;
        }

        .accountpaidnolist ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }

        .accountListt ul {
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
        }

        .accountListt ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }







        thead,
        tbody,
        tfoot,
        tr,
        td,
        th .trcolors {
            color: red !important;
        }

        thead,
        tbody,
        tfoot,
        tr,
        td,
        th .activecolors {
            color: #5d596c !important;
        }

        .btn-padding {
            padding: 10px 18px !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Tab navigation -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="transaction-tab" data-bs-toggle="tab" href="#transaction" role="tab"
                    aria-controls="transaction" aria-selected="true">Daily Collection Saving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="daily-collection-tab" data-bs-toggle="tab" href="#daily-collection" role="tab"
                    aria-controls="daily-collection" aria-selected="false">Daily Collection </a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content">
            <!-- Transaction tab -->
            <div class="tab-pane fade show active" id="transaction" role="tabpanel" aria-labelledby="transaction-tab">
                <h4 class="py-2"><span class="text-muted fw-light">Transaction / </span>Account Open Daily Collection
                    Saving</h4>
                <div class="container">
                    <div class="row">
                        <div class="col-9 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <form id="dailycollectionform" action="javascript:void(0)" autocomplete="off">
                                        <div class="row">
                                            <input hidden type="text" name="ac_id" id="ac_id">
                                            <div class="col-md-3 col-12 mb-4">
                                                <label for="DATE" class="form-label">DATE</label>
                                                <input type="text" class="form-control mydatepic"
                                                    placeholder="YYYY-MM-DD" id="opening_date" name="opening_date"
                                                    value="{{ date('d-m-Y') }}" />
                                            </div>
                                            <div class="col-md-3 col-12 mb-4">
                                                <label for="MEMBER TYPE" class="form-label">MEMBER TYPE</label>
                                                <select class="form-select" name="member_type" id="member_type">
                                                    <option value="Member">Member</option>
                                                    <option value="Staff">Staff</option>
                                                    <option value="NonMember">Non-Member</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-12 mb-4">
                                                <label for="DAILY AC NO" class="form-label">ACCOUNT NO</label>
                                                <input type="text" id="daily_ac_no" name="daily_ac_no"
                                                    class="form-control" placeholder="Daily Account No"
                                                    oninput="getDailyAccountNumber('this')">
                                                <div id="accountList" class="accountList"></div>
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="AC NO" class="form-label">MEMBERSHIP NO</label>
                                                <input type="text" id="membership_no" name="membership_no"
                                                    class="form-control" placeholder="membershipno" readonly>
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="SCHEME TYPE" class="form-label">SCHEME </label>
                                                <input type="text" hidden name="sch_id" id="sch_id">
                                                <input type="text" class="form-control" readonly name="scheme_type"
                                                    id="scheme_type" placeholder="Scheme Name">
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="Amount" class="form-label">AMOUNT</label>
                                                <input type="text" id="daily_amount" name="daily_amount"
                                                    class="form-control" placeholder="Amount"
                                                    oninput="interestCalculate('this')">
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="DAYS" class="form-label">DAYS</label>
                                                <input type="text" id="days" name="days" class="form-control"
                                                    oninput="interestCalculate('this')" placeholder="Days">
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="INTEREST%" class="form-label">INTEREST%</label>
                                                <input type="text" id="rate_of_interest" name="rate_of_interest"
                                                    oninput="interestCalculate('this')" class="form-control"
                                                    placeholder="Interest">
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="INTEREST%" class="form-label">Penality%</label>
                                                <input type="text" id="penality_ineterst" name="penality_ineterst"
                                                    oninput="interestCalculate('this')" class="form-control"
                                                    placeholder="Interest">
                                            </div>


                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="TPM" class="form-label">TOTAL AMOUNT</label>
                                                <input type="text" id="total_amount" name="total_amount"
                                                    class="form-control" placeholder="Total Principal Amount"
                                                    oninput="interestCalculate('this')" readonly>
                                            </div>
                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="MA" class="form-label">INTEREST AMOUNT</label>
                                                <input type="text" id="total_interest" name="total_interest"
                                                    class="form-control" oninput="interestCalculate('this')"
                                                    placeholder="Interst Amount" readonly>
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="MA" class="form-label">MATURITY AMOUNT</label>
                                                <input type="text" id="maturity_amount" name="maturity_amount"
                                                    oninput="interestCalculate('this')" class="form-control"
                                                    placeholder="Maturity Amount" readonly>
                                            </div>
                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="MATURITY DATE" class="form-label">MATURITY DATE</label>
                                                <input type="text" id="maturity_date" name="maturity_date"
                                                    oninput="interestCalculate('this')" class="form-control"
                                                    placeholder="Maturity Date" readonly>
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="LINDAYS" class="form-label">LOCK-IN DAYS</label>
                                                <input type="text" id="lock_in_days" name="lock_in_days"
                                                    class="form-control" placeholder="Lock-IN Days"
                                                    oninput="interestCalculate('this')">
                                                <input type="hidden" id="updatedailycollection">
                                            </div>

                                            <div class="col-md-3 col-12 mb-4 hidediv">
                                                <label for="LINDATE" class="form-label">LOCK-IN DATE</label>
                                                <input type="text" id="lock_in_date" name="lock_in_date"
                                                    class="form-control" readonly oninput="interestCalculate('this')">
                                            </div>

                                            <div class="col-md-3 col-md-3 col-sm-4 col-6 hidediv">
                                                <label for="agentId" class="form-label">AGENT</label>
                                                <select class="form-select" id="agentId" name="agentId">
                                                    @if (!empty($agents))
                                                        @foreach ($agents as $agent)
                                                            <option value="{{ $agent->id }}">{{ $agent->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mt-4 savedailycollection">
                                                <button type="submit" class="btn btn-primary px-4">Save</button>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>

                        {{--  account fetch data right side  --}}
                        <div class="col-3 account-right mb-4">
                            <div class="card h100">
                                <div class="card-body">
                                    <form id="dailycollectionform" action="javascript:void(0)" autocomplete="off">
                                        <div class="row">
                                            <div class="col-lg-12 p-0">
                                                <div class="right-img">
                                                    <img src="http://placehold.it/180" id="imagess" alt="profile-pic">
                                                </div>
                                            </div>
                                            <div class="row w100 mauto" id="account_member_details">
                                                <div class="col-sm-12 py-1 paddingl-r-0">
                                                    <label for="member_name">Name</label>
                                                    <input type="text" id="member_name" name="member_name"
                                                        class="form-control" readonly>
                                                </div>
                                                <div class="col-sm-12 py-1 paddingl-r-0">
                                                    <label for="member_fathername">Fathername</label>
                                                    <input type="text" id="member_fathername" name="member_fathername"
                                                        class="form-control" readonly>
                                                </div>
                                                <div class="col-sm-12 py-1 paddingl-r-0">
                                                    <label for="member_address">Address</label>
                                                    <textarea id="member_address" name="member_address" class="w100" rows="3" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        </form>



                        {{--  end  --}}
                        <div class="col-md-12" id="dailycollectiontable">
                            <div class="tabledata card tablee">
                                <div class="card-body">
                                    <table class="table datatables-order scroll-table" id="datatable"
                                        style="width:100%; display: inline-table;">
                                        <thead class="table_head thead-light">
                                            <tr>
                                                <th>MEMBER TYPE</th>
                                                <th>MEMBERSHIP NO</th>
                                                <th>ACC NO</th>
                                                <th>AC HOLDER NAME</th>
                                                <th>OPENING DATE</th>
                                                <th>MATURITY DATE</th>
                                                <th>PERIOD</th>
                                                <th>DAILY AMT</th>
                                                <th>STATUS</th>
                                                <th colspan="2">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dailycollectiontbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!---------------------- Daily Deposit Form tab ----------------->

            <!-- Daily Collection tab -->
            <div class="tab-pane fade" id="daily-collection" role="tabpanel" aria-labelledby="daily-collection-tab">
                <h4 class="py-2"><span class="text-muted fw-light">Transaction / </span>Daily Collection Deposit
                </h4>
                <div class="container">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <form action="javascript:void(0)" id="dailycollectionpaid">
                                        <div class="row">
                                            <div class="col-md-2 col-12 mb-4">
                                                <label for="Date" class="form-label">DATE</label>
                                                <input type="date" class="form-control" placeholder="YYYY-MM-DD"
                                                    id="paid_date" name="paid_date" value="{{ now()->format('Y-m-d') }}"
                                                    max="{{ now()->format('Y-m-d') }}" />
                                            </div>
                                            <div class="col-md-2 col-12 mb-4">
                                                <label for="MEMBER TYPE" class="form-label">MEMBER TYPE</label>
                                                <select class="form-select" name="paid_member_type"
                                                    id="paid_member_type">
                                                    <option value="Member">Member</option>
                                                    <option value="Staff">Staff</option>
                                                    <option value="NonMember">Non-Member</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-12 mb-4">
                                                <label for="AC NO" class="form-label">ACCOUNT NO</label>
                                                <input type="text" id="dds_account" name="dds_account"
                                                    class="form-control" placeholder="Account No"
                                                    oninput="getDDSAccount('this')">
                                                <div id="accountpaidnolist" class="accountpaidnolist"></div>
                                            </div>
                                            <div class="col-md-2 p-0  mt-4 trascationbutton" style="display: none;">
                                                <button type="button" class="btn btn-success btn-padding depositbtn">Deposit</button>
                                                <button type="button" class="btn btn-danger btn-padding maturebtn">Mature</button>
                                            </div>
                                            <div class="col-md-2 p-0 mt-4 ledgerbutton" style="display: none;">
                                                <button type="button" class="btn btn-primary receivedinstallments">View
                                                    Ledger</button>
                                            </div>
                                            <div class="col-md-2 p-0 mt-4 unmature" style="display: none;">
                                                <button type="button" class="btn btn-warning unmature">Unmature</button>
                                            </div>
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
                                            <th>DATE</th>
                                            <th>AC NO</th>
                                            <th>DAILY AMT.</th>
                                            <th>SCHEME NAME</th>
                                            <th>PENDING AMT</th>
                                            <th>EXCESS AMT</th>
                                            <th>REC. AMT</th>
                                            <th>INTT.</th>
                                            <th>DAYS</th>
                                            <th>STATUS</th>
                                            <th>MATURITY</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dailycollectionpaidtbody">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!---------------------- Daily Deposit End tab ----------------->
        </div>
    </div>



    <div class="modal fade" id="receveamountmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Deposit Daily Collection</h5>
                </div>
                <form name="receveamountForm" id="receveamountForm">
                    <div class="modal-body receivedamountclass">
                        <div class="row">
                            <input type="text" hidden name="dailyid" id="dailyid">
                            <input type="text" hidden name="receiveamountaccount" id="receiveamountaccount">
                            <input type="text" hidden name="receiveaccounttype" id="receiveaccounttype">
                            <!-- Add receive input fields here -->
                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">DATE</label>
                                <input type="text" class="form-control" id="received_amount_date"
                                    name="received_amount_date" value="{{ date('d-m-Y') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="receive_amount" class="form-label">Exceed Amount</label>
                                <input type="text" class="form-control" id="exceed_amount" name="exceed_amount"
                                    readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="receive_amount" class="form-label">PENDING AMT</label>
                                <input type="text" class="form-control" id="pending_amount" name="pending_amount"
                                    readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="received_amount" class="form-label">REC. AMOUNT</label>
                                <input type="text" class="form-control" id="receive_amount" name="receive_amount" value="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="">PAYMENT TYPE</label>
                                <select name="payment_type" id="payment_type" onchange="getRecievedCashbank(this)"
                                    class="form-select">
                                    <option value=""selected>Select</option>
                                    @if (!empty($groups))
                                        @foreach ($groups as $row)
                                            <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="">PAYMENT BANK</label>
                                <select name="payment_bank" id="payment_bank" class="form-select">
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="received_amount" class="form-label">Narration</label>
                                <input type="text" class="form-control" id="narration" name="narration">
                            </div>
                            <div class="col-md-3 col-md-3 col-sm-4 col-6">
                                <label for="agentId" class="form-label">AGENT</label>
                                <select class="form-select" id="agentId" name="agentId">
                                    @if (!empty($agents))
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>


                            <!-- Add more input fields if needed -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Receive</button>
                        <button type="button" class="btn btn-danger recbtns">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewrecipetmodal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabelWithdraw" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabelWithdraw">Receipt Amount</h5>
                </div>

                <div class="col-md-12">
                    <div class="tabledata card tablee">
                        <div class="card-body" style="overflow-x: auto;">
                            <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head thead-light">
                                    <tr>
                                        <th>DATE</th>
                                        <th>AC NO</th>
                                        <th>DAILY AMT.</th>
                                        <th colspan="2">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dailyreceivedamountdetails">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    {{--  <button type="button" class="btn btn-success viewinstallments">View Installments</button>  --}}
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewinstallmentModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabelWithdraw" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabelWithdraw">Withdraw Amount</h5>
                </div>

                <div class="col-md-12">
                    <div class="tabledata card tablee" style="height: 80vh;">
                        <div class="card-body" style="overflow-x: auto;">
                            <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head thead-light">
                                    <tr>
                                        <th>Install.DATE</th>
                                        <th>Receipt Date</th>
                                        <th>DAILY AMT.</th>
                                        <th>Install.No</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="installmentbody">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="matureModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Daily Deposit Mature</h5>
                </div>
                <form name="matureForm" id="matureForm">
                    <div class="modal-body receivedamountclass">
                        <div class="row">
                            <input type="text" hidden name="mdailyid" id="mdailyid">
                            <input type="text" hidden name="mreceiveamountaccount" id="mreceiveamountaccount">
                            <input type="text" hidden name="mreceiveaccounttype" id="mreceiveaccounttype">
                            <input type="text" hidden name="membernumber" id="mno">

                            <!-- Add receive input fields here -->

                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">A/c Opening Date</label>
                                <input type="text" class="form-control" id="start_date" name="start_date"
                                    oninput="getmaturity('this')" readonly>
                            </div>



                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">DATE</label>
                                <input type="text" class="form-control" id="received_amount_dated"
                                    name="received_amount_dated" value="{{ date('d-m-Y') }}"
                                    oninput="getmaturity('this')">
                            </div>

                            {{--  <input type="text" hidden name="start_date" id="start_date" oninput="getmaturity('this')">  --}}
                            <div class="col-md-4 mb-3">
                                <label for="receive_amount" class="form-label">Saving Account</label>
                                <input type="text" class="form-control" id="saving_amount" name="saving_amount"
                                    readonly>
                            </div>

                            <div class="col-md-4  mb-3">
                                <label for="received_amount" class="form-label">Daily Amount</label>
                                <input type="text" class="form-control" id="daily_amt" name="daily_amt"
                                    oninput="getmaturity('this')" readonly>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label for="receive_amount" class="form-label">Balance Amount</label>
                                <input type="text" class="form-control" id="standing_amount" name="standing_amount"
                                    readonly oninput="getmaturity('this')">
                            </div>

                            <div class="col-md-4  mb-3">
                                <label for="received_amount" class="form-label">Interest</label>
                                <input type="text" class="form-control" id="interst_rate" name="interst_rate"
                                    oninput="getmaturity('this')">
                            </div>

                            <div class="col-md-4  mb-3">
                                <label for="received_amount" class="form-label">Interest Amount</label>
                                <input type="text" class="form-control" id="paid_interst_amount"
                                    name="paid_interst_amount" oninput="getmaturity('this')">
                            </div>


                            <div class="col-md-4  mb-3">
                                <label for="received_amount" class="form-label">Total Amount</label>
                                <input type="text" class="form-control" id="net_amount" name="net_amount"
                                    oninput="getmaturity('this')">
                            </div>


                            <div class="col-md-8 mb-3">
                                <label for="received_amount" class="form-label">Narration</label>
                                <input type="text" class="form-control" id="narration" name="narration">
                            </div>

                            <!-- Add more input fields if needed -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Receive</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="unmatureconfirmationmodel" tabindex="-1"
        aria-labelledby="exampleModalLabel"aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">UnMature</h5>
                </div>
                <div class="modal-body">
                    <p>Are You Sure You want Unmature</p>
                </div>

                <form name="unmatureForm" id="unmatureForm">
                    <input type="hidden" id="unmatureaccountNumber" name="unmatureaccountNumber">
                    <input type="hidden" id="unmaturetype" name="unmaturetype">
                    <input type="hidden" id="dailycid" name="dailycid">
                    <input type="hidden" id="memnumbers" name="memnumbers">
                    <input type="hidden" name="unmaturedate" id="unmaturedate" value="{{ date('d-m-Y') }}">

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('.savedailycollection').show();

        function getDailyAccountNumber() {
            let daliySavingAccount = $('#daily_ac_no').val();
            let memberType = $('#member_type').val();

            $.ajax({
                url: "{{ route('getddsaccountslist') }}",
                type: 'post',
                data: {
                    daliySavingAccount: daliySavingAccount,
                    memberType: memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let DailySavingAccounts = res.dailyaccounts;
                        if (DailySavingAccounts && DailySavingAccounts.length > 0) {
                            let accountListDropDown = $('#accountList');
                            accountListDropDown.empty();

                            DailySavingAccounts.forEach((data) => {
                                accountListDropDown.append(
                                    `<div class="accountLists" data-id="${data.accountNo}">${data.accountNo}</div>`
                                );
                            });

                        } else {
                            let accountListDropDown = $('#accountList');
                            accountListDropDown.empty();

                            accountListDropDown.append(`<div class="accountLists">No Account</div>`);
                        }
                    }
                }
            });
        }

        function interestCalculate() {
            $("#maturity_amount").val('');

            let DailyAmount = $('#daily_amount').val();

            let Days = $('#days').val();
            let months = Math.floor(Days / 30.44) + 1;
            let totalprinicpalamount = DailyAmount * Days;
            let asdasamount = totalprinicpalamount;
            let monthliyamount = totalprinicpalamount / months;

            $('#total_amount').val(totalprinicpalamount);

            let monthsToAdddate = months;
            let openingDate = $("#opening_date").val();
            let amount = monthliyamount;
            let interest = parseFloat($("#rate_of_interest").val()) || 0;


            //___________Maturity Date And Maturity amount Calculate
            if (!isNaN(monthsToAdddate) && openingDate && amount && interest) {

                $("#maturity_date").val('');
                let dateParts = openingDate.split("-");
                let startDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                startDate.setMonth(startDate.getMonth() + monthsToAdddate);
                let resultDate = ("0" + startDate.getDate()).slice(-2) + "-" + ("0" + (startDate.getMonth() + 1)).slice(-
                    2) + "-" + startDate.getFullYear();

                $("#maturity_date").val(resultDate);

                let lockingDays = parseInt($('#lock_in_days').val(), 10);

                //___________Lock-in Period Date
                if (!isNaN(lockingDays) && openingDate) {
                    let datePartss = openingDate.split("-");
                    let startDates = new Date(datePartss[2], datePartss[1] - 1, datePartss[0]);

                    // Add locking days
                    startDates.setDate(startDates.getDate() + lockingDays);


                    // Format locking date back to "DD-MM-YYYY"
                    let lockingDate = ("0" + startDates.getDate()).slice(-2) + "-" +
                        ("0" + (startDates.getMonth() + 1)).slice(-2) + "-" +
                        startDates.getFullYear();
                    $("#lock_in_date").val(lockingDate);
                }

                let currentAmount = 0;
                let totalInterest = 0;
                let monthlyInterest = 0;

                for (let i = 0; i < months; i++) {
                    currentAmount += monthliyamount;
                    let monthlyInterest = (currentAmount * interest) / 100 / 12; // Avoid rounding here
                    totalInterest += monthlyInterest;
                    currentAmount += monthlyInterest; // Add monthly interest to the current amount
                    {{--  console.log(`Month ${i + 1}: Interest = ${monthlyInterest.toFixed(2)}`);  --}}
                }

                let ssa = monthlyInterest;
                {{--  console.log(ssa);  --}}


                let maturityAmount = currentAmount;
                $('#total_interest').val(totalInterest.toFixed(2));
                $("#maturity_amount").val(maturityAmount.toFixed(2));
            }
        }

        function showdata(account) {
            if (!account) {
                console.error("Account data is missing!");
                return;
            }

            const dailycollectiontbody = $('#dailycollectiontbody');
            dailycollectiontbody.empty();

            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();

                day = day < 10 ? `0${day}` : day;
                month = month < 10 ? `0${month}` : month;
                return `${day}-${month}-${year}`;
            };

            const formattedOpeningDate = formatDate(account.opening_date);
            const formattedMaturityDate = formatDate(account.maturitydate);

            const row = `
                    <tr>
                        <td>${account.membertype}</td>
                        <td>${account.membershipno}</td>
                        <td>${account.account_no}</td>
                        <td>${account.customer_name}</td>
                        <td>${formattedOpeningDate}</td>
                        <td>${formattedMaturityDate}</td>
                        <td>${account.days}</td>
                        <td>${account.amount}</td>
                        <td>${account.status}</td>
                        ${
                            account.dailyaccountid != null
                            ? ''
                            : `<td style="width:85px;">
                                <button class="btn editbtn" data-id="${account.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn" data-id="${account.id}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`
                        }
                    </tr>`;

            dailycollectiontbody.append(row);

            const imageUrl = account.photo ?
                `public/uploads/MemberPhotos/${account.photo}` :
                'path/to/default-image.jpg';

            $("#imagess").attr('src', imageUrl);
        }

        function getDDSAccount() {
            let daliySavingAccount = $('#dds_account').val();
            let memberType = $('#paid_member_type').val();

            $.ajax({
                url: "{{ route('getddreceivedsaccountslist') }}",
                type: 'post',
                data: {
                    daliySavingAccount: daliySavingAccount,
                    memberType: memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let DailySavingAccounts = res.dailyaccounts;
                        if (DailySavingAccounts && DailySavingAccounts.length > 0) {
                            let accountListDropDown = $('#accountpaidnolist');
                            accountListDropDown.empty();

                            DailySavingAccounts.forEach((data) => {
                                accountListDropDown.append(
                                    `<div class="accountListss" data-id="${data.account_no}">${data.account_no}</div>`
                                );
                            });

                        } else {
                            let accountListDropDown = $('#accountpaidnolist');
                            accountListDropDown.empty();
                            accountListDropDown.append(`<div class="accountListss">No Account</div>`);
                        }
                    }
                }
            });
        }

        function DdsReceievedshowdata(account) {
            if (!account) {
                console.error("Account data is missing!");
                return;
            }

            const dailycollectiontbody = $('#dailycollectionpaidtbody');
            dailycollectiontbody.empty();

            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();

                day = day < 10 ? `0${day}` : day;
                month = month < 10 ? `0${month}` : month;
                return `${day}-${month}-${year}`;
            };

            const formattedOpeningDate = formatDate(account.opening_date);
            const formattedMaturityDate = formatDate(account.maturitydate);
            let daily_amount = parseFloat(account.amount);
            let deposit_amount = parseFloat(account.deposit_amount) ? parseFloat(account.deposit_amount) : 0;
            console.log(deposit_amount);
            let opening_date = new Date(account.opening_date);
            let currentDate = new Date();
            let time_difference = currentDate - opening_date;
            let day_difference = Math.round(time_difference / (1000 * 60 * 60 * 24));


            let pending_amount = ((daily_amount * day_difference) - deposit_amount);



            $('#exceed_amount').val(pending_amount < 0 ? Math.abs(pending_amount) : 0);
            $('#pending_amount').val(pending_amount > 0 ? pending_amount : 0);
            $('#dailycid').val(account.id);
            $('#memnumbers').val(account.membershipno);
            let maturity_amount = account.ActualyMaturityAmount ?? 0;

            let account_status = account.status;

            if (account_status === 'Active' || account_status === 'Pluge') {
                $('.trascationbutton').show();
                $('.ledgerbutton').show();
                $('.unmature').hide();
            } else {
                $('.trascationbutton').hide();
                $('.ledgerbutton').show();
                $('.unmature').show();
            }


            const rowClass = account.status === 'Active' ? 'activecolors' : 'trcolors';



            const row = `
            <tr class="${rowClass}">
                <td>${formattedOpeningDate}</td>
                <td>${account.account_no}</td>
                <td>${account.amount}</td>
                <td>${account.scheme_name}</td>
                <td>${pending_amount > 0 ? pending_amount : 0}</td>
                <td>${pending_amount < 0 ? Math.abs(pending_amount) : 0}</td>
                <td>${deposit_amount}</td>
                <td>${account.interest}</td>
                <td>${account.days}</td>
                <td>${account.status}</td>
                <td>${maturity_amount}</td>
            </tr>`;
            dailycollectiontbody.append(row);




        }

        function getRecievedCashbank() {
            let groups_code = $('#payment_type').val();

            $.ajax({
                url: "{{ route('ddsreceivedledger') }}",
                type: 'post',
                data: {
                    groups_code: groups_code
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let ledgerDropdown = document.getElementById('payment_bank');
                    ledgerDropdown.innerHTML = '';

                    if (res.status === 'success' && res.ledgers) {
                        let ledgers = res.ledgers;

                        ledgers.forEach((data) => {
                            let option = document.createElement('option');
                            option.value = data.ledgerCode;
                            option.textContent = data.name;
                            ledgerDropdown.appendChild(option);
                        });
                    } else {
                        notify('No ledgers found for the selected group.', 'warning');
                    }
                },
                error: function() {
                    notify('An error occurred while fetching ledgers.', 'warning');
                }
            });
        }

        function getmaturity() {
            // Fetch and validate dates
            let openingDate = $('#start_date').val();
            let currentDate = $('#received_amount_dated').val();

            if (!openingDate || !currentDate) {
                {{--  alert('Please provide both the opening and current dates.');  --}}
                return;
            }

            let [openingDay, openingMonth, openingYear] = openingDate.split('-');
            let [currentDay, currentMonth, currentYear] = currentDate.split('-');

            let openingDateObj = new Date(`${openingYear}-${openingMonth}-${openingDay}`);
            let currentDateObj = new Date(`${currentYear}-${currentMonth}-${currentDay}`);

            if (isNaN(openingDateObj) || isNaN(currentDateObj)) {
                {{--  alert('Invalid date format. Please use DD-MM-YYYY.');  --}}
                return;
            }

            // Calculate total days and months
            let timeDifference = currentDateObj - openingDateObj;
            let totalDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

            {{--  if (totalDays <= 0) {
                alert('The current date must be after the opening date.');
                return;
            }  --}}

            let months = Math.floor(totalDays / 30.44); // Approximate months

            // Fetch input values
            let dailyAmount = parseFloat($('#daily_amt').val()) || 0;
            let rateOfInterest = parseFloat($('#interst_rate').val()) || 0;
            let receivedAmount = parseFloat($('#standing_amount').val());
            console.log(receivedAmount);

            // Validate numeric inputs
            if (dailyAmount <= 0 || rateOfInterest <= 0) {
                {{--  alert('Daily amount and interest rate must be positive values.');  --}}
                return;
            }

            let totalPrincipalAmount = dailyAmount * totalDays;

            {{--  if (receivedAmount > totalPrincipalAmount) {
                alert('Received amount cannot exceed the total principal amount.');
                return;
            }  --}}

            let currentAmount = 0;
            let totalInterest = 0;
            let paidinterest = parseFloat($('#paid_interst_amount').val() || 0);
            let balance = (paidinterest + receivedAmount);
            $('#net_amount').val(balance);

            {{--  if (months >= 6) {
                for (let i = 0; i < months; i++) {
                    let monthlyAmount = receivedAmount / months;
                    currentAmount += monthlyAmount;
                    let monthlyInterest = (currentAmount * rateOfInterest) / 100 / 12;
                    totalInterest += monthlyInterest;
                    currentAmount += monthlyInterest;
                }

                $('#paid_interst_amount').val(totalInterest);
                $('#net_amount').val(currentAmount.toFixed(2));
            } else {
                currentAmount = receivedAmount;
                for (let i = 0; i < months; i++) {
                    let monthlyInterest = (currentAmount * rateOfInterest) / 100 / 12;
                    totalInterest += monthlyInterest;
                    currentAmount += monthlyInterest;
                }

            }  --}}

        }



        $(document).ready(function() {
            $(document).on('click', '.accountLists', function(e) {
                e.preventDefault();

                let accountNumber = $(this).data('id');
                $('#daily_ac_no').val(accountNumber);
                let memberType = $('#member_type').val();
                $('#accountList').html('');

                $.ajax({
                    url: "{{ route('getddsaccount') }}",
                    type: 'post',
                    data: {
                        accountNumber: accountNumber,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let daily_account = res.daily_account;
                            let account = res.old_account;

                            if (account) {
                                $('#dailycollectionform')[0].reset();
                                $('.savedailycollection').hide();
                                $('#daily_ac_no').val(accountNumber);

                                $('#membership_no,#lock_in_days,#agentId,#daily_amount,#scheme_type, #sch_id, #days, #rate_of_interest, #member_name, #member_fathername, #member_address, #penality_ineterst')
                                    .val('').prop('readonly', true);

                                showdata(account);
                            } else {
                                $('#membership_no,#lock_in_days,#agentId,#daily_amount,#scheme_type, #sch_id, #days, #rate_of_interest, #member_name, #member_fathername, #member_address, #penality_ineterst')
                                    .prop('readonly', false);


                                $('#membership_no').val(daily_account.membershipno);
                                $('#scheme_type').val(daily_account.schemename);
                                $('#sch_id').val(daily_account.sch_id);
                                $('#days').val(daily_account.days);
                                $('#rate_of_interest').val(daily_account.roi);
                                $('#member_name').val(daily_account.customer_name);
                                $('#member_fathername').val(daily_account.fatherName);
                                $('#member_address').val(daily_account.address);
                                $('#penality_ineterst').val(daily_account.penaltyInterest ?
                                    daily_account.penaltyInterest : 0);


                                if (daily_account.photo) {
                                    const baseUrl = 'public/uploads/MemberPhotos/';
                                    const imageUrl = `${baseUrl}${daily_account.photo}`;
                                    console.log("Image URL:", imageUrl);

                                    $("#imagess").attr('src', imageUrl);
                                } else {
                                    console.error("Photo URL is missing!");
                                }
                            }
                        }
                    }
                });
            });

            $('#dailycollectionform').validate({
                rules : {
                    opening_date : {
                        required : true
                    },
                    member_type : {
                        required : true
                    },
                    daily_ac_no : {
                        required : true,
                        digits : true
                    },
                    sch_id : {
                        required : true,
                    },
                    daily_amount : {
                        required : true,
                        digits : true
                    },
                    days : {
                        required : true,
                    },
                    rate_of_interest : {
                        required : true,
                    },
                    total_amount : {
                        required : true,
                    },
                    total_interest : {
                        required : true,
                    },
                    maturity_amount : {
                        required : true,
                    },
                    maturity_date : {
                        required : true,
                    },
                },messages : {
                    opening_date : {
                        required : 'Enter Opening Date'
                    },
                    member_type : {
                        required : 'Select Customer Type'
                    },
                    daily_ac_no : {
                        required : 'Enter Daily Account number',
                        digits : 'Enter Only Numeric Value'
                    },
                    sch_id : {
                        required : 'Scheme ID Not Found',
                    },
                    daily_amount : {
                        required : 'Enter Daily Amount',
                        digits : 'Enter Onlky Numeric Value'
                    },
                    days : {
                        required : 'Enter Daily Account Days',
                    },
                    rate_of_interest : {
                        required : 'Enter Rate Of Interest',
                    },
                    total_amount : {
                        required : 'Enter Total  Principal Amount',
                    },
                    total_interest : {
                        required : 'Enter Interest Amount',
                    },
                    maturity_amount : {
                        required : 'Enter Maturity Amount',
                    },
                    maturity_date : {
                        required : 'Enter Maturity Date',
                    },
                },
                errorElement : 'p',
                errorPlacement : function(error,element){
                    error.insertAfter(element);
                }
            });

            $(document).on('submit', '#dailycollectionform', function(event) {
                event.preventDefault();
                let formData = $(this).serialize();

                if($(this).valid()){
                    let url = $('#ac_id').val() ? "{{ route('updatedailysavingaccount') }}" : "{{ route('insertdailysavingaccount') }}";

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
                                $('#dailycollectionform')[0].reset();
                                notify(res.messages,'success');
                                let account = res.dailyaccount;
                                let opening_accounts = res.opening_accounts;

                                let dailycollectiontbody = $('#dailycollectiontbody');
                                dailycollectiontbody.empty();

                                showdata(account);
                                if(opening_accounts){
                                    const imageUrl = opening_accounts.photo ? `public/uploads/MemberPhotos/${opening_accounts.photo}` :'path/to/default-image.jpg';
                                    $("#imagess").attr('src', imageUrl);
                                }
                            } else {
                                notify(res.messages,'warning');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.editbtn', function(event) {
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('editddssaving') }}",
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
                            $('.savedailycollection').show();
                            let daily_account = res.dds_account;

                            $('#membership_no,#lock_in_days,#agentId,#daily_amount,#sch_id, #days, #rate_of_interest, #member_name, #member_fathername, #member_address, #penality_ineterst')
                                .prop('readonly', false);

                            const formatDate = (dateStr) => {
                                const date = new Date(dateStr);
                                let day = date.getDate();
                                let month = date.getMonth() + 1;
                                let year = date.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                return `${day}-${month}-${year}`;
                            };

                            const formattedOpeningDate = formatDate(daily_account.opening_date);
                            const formattedMaturityDate = formatDate(daily_account
                                .maturitydate);

                            $('#opening_date').val(formattedOpeningDate);
                            $('#ac_id').val(daily_account.id);
                            $('#membership_no').val(daily_account.membershipno).prop('readonly',
                                true);
                            $('#daily_ac_no').val(daily_account.account_no).prop('readonly',
                                true);
                            $('#member_type').val(daily_account.membertype).prop('readonly',
                                true);
                            $('#scheme_type').val(daily_account.scheme_name).prop('readonly',
                                true);
                            $('#sch_id').val(daily_account.schemeid);
                            $('#daily_amount').val(daily_account.amount);
                            $('#days').val(daily_account.days);
                            $('#rate_of_interest').val(daily_account.interest);
                            $('#member_name').val(daily_account.customer_name);
                            $('#total_interest').val(daily_account.interest_amount);
                            $('#total_amount').val(daily_account.principalamount);
                            $('#penality_ineterst').val(daily_account.penelty || 0);
                            $('#maturity_amount').val(daily_account.maturityamount);
                            $('#maturity_date').val(formattedMaturityDate);
                            $('#lock_in_days').val(daily_account.lockindays || 'NA');
                            $('#lock_in_date').val(daily_account.lockindate || 'NA');
                            $('#agentId').val(daily_account.agentId);

                            if (daily_account.photo) {
                                const baseUrl = 'public/uploads/MemberPhotos/';
                                const imageUrl = `${baseUrl}${daily_account.photo}`;
                                console.log("Image URL:", imageUrl);

                                $("#imagess").attr('src', imageUrl);
                            } else {
                                console.error("Photo URL is missing!");
                            }
                        } else {
                            notify(res.messages);
                        }
                    }
                });
            });

            $(document).on('click', '.deletebtn', function(event) {
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
                        {{--  $('#ledgerModal').modal('hide'); // Hide the modal before deletion starts  --}}

                        Swal.fire({
                            title: "Deleting...",
                            text: "Please wait while we delete the transaction.",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        $.ajax({
                            url: "{{ route('deleteddssaving') }}",
                            type: 'post',
                            data: { id: id },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            dataType: 'json',
                            success: function(res) {
                                Swal.close()
                                if (res.status === 'success') {
                                    window.location.href =
                                        "{{ route('dailysavingcollectionindex') }}";
                                } else {
                                    $('#dailycollectiontbody').empty();
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                {{--  $('.ccldeletebtn').prop('disabled', false);  --}}

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



            $(document).on('click', '.accountListss', function(e) {
                e.preventDefault();
                let accountNumber = $(this).data('id');
                $('#dds_account').val(accountNumber);
                let memberType = $('#paid_member_type').val();
                $('#accountListss').val('');
                $('#accountpaidnolist').empty();

                $.ajax({
                    url: "{{ route('getreceievedddsaccount') }}",
                    type: 'post',
                    data: {
                        accountNumber: accountNumber,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let account = res.account_number;
                            $('.trascationbutton').show();
                            $('.ledgerbutton').show();
                            $('.unmature').hide();
                            if (account) {
                                DdsReceievedshowdata(account);
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.depositbtn', function(event) {
                event.preventDefault();
                let account_number = $('#dds_account').val();
                let paid_member_type = $('#paid_member_type').val();

                $.ajax({
                    url: "{{ route('getddsaccountsssss') }}",
                    type: 'post',
                    data: {
                        account_number: account_number,
                        memberType: paid_member_type
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let daily_collections = res.daily_collections;
                            $('#receiveamountaccount').val(daily_collections.account_no);
                            $('#receiveaccounttype').val(daily_collections.membertype);

                            $('#receveamountmodel').modal('show');

                        }else{
                            notify(res.messages,'warning')
                        }
                    }
                });
            });

            $(document).on('click','.recbtns',function(event){
                event.preventDefault();
               $('#receive_amount').val('');
                $('#receveamountmodel').modal('hide');
            });

            $('#receveamountForm').validate({
                rules: {
                    receive_amount: {
                        required: true,
                        digits: true
                    },
                    payment_type: {
                        required: true,
                    },
                    payment_bank: {
                        required: true,
                    }
                },
                messages: {
                    receive_amount: {
                        required: 'Please Enter Amount',
                        digits: 'Enter Only Numeric Value'
                    },
                    payment_type: {
                        required: 'Please Select Group',
                    },
                    payment_bank: {
                        required: 'Pleae Select Ledger',
                    }
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });


            $(document).on('submit', '#receveamountForm', function(event) {
                event.preventDefault();
                if ($(this).valid()) {
                    $('button[type=submit]').prop('disabled', true);

                    let url = $('#dailyid').val() ? "{{ route('dailysavingreceivedupdate') }}" :
                        "{{ route('dailysavingreceived') }}";
                    let formData = $(this).serialize();

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
                                $('button[type=submit]').prop('disabled', false);
                                let account = res.account_number;
                                notify(res.messages);
                                $('#receveamountForm')[0].reset();
                                $('#receveamountmodel').modal('hide');
                                $('.trascationbutton').show();
                                DdsReceievedshowdata(account);

                            } else {
                                notify(res.messages);
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.receivedinstallments', function(event) {
                event.preventDefault();
                let account_number = $('#dds_account').val();
                let memberType = $('#paid_member_type').val();

                $.ajax({
                    url: "{{ route('viewdepositeamount') }}",
                    type: 'post',
                    data: {
                        account_number: account_number,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let account = res.account_number;

                            let receivedTbody = $('#dailyreceivedamountdetails');
                            receivedTbody.empty();

                            if (account && account.length > 0) {
                                account.forEach((data) => {
                                    const formatDate = (dateStr) => {
                                        const date = new Date(dateStr);
                                        let day = date.getDate();
                                        let month = date.getMonth() + 1;
                                        let year = date.getFullYear();

                                        day = day < 10 ? `0${day}` : day;
                                        month = month < 10 ? `0${month}` : month;
                                        return `${day}-${month}-${year}`;
                                    };

                                    const formattedOpeningDate = formatDate(data
                                        .receipt_date);

                                        let row = `<tr>
                                            <td>${formattedOpeningDate}</td>
                                            <td>${data.account_no}</td>
                                            <td>${data.deposit}</td>
                                            <td style="width:85px;">
                                                ${
                                                    data.status === "Active" && data.type === "Deposit"
                                                    ? `<button class="btn instteditbtn" data-id="${data.ids}">
                                                            <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                        </button>
                                                        <button class="btn installdeletebtn" data-id="${data.ids}">
                                                            <i class="fa-solid fa-trash iconsColorCustom"></i>
                                                        </button>`
                                                    : ``
                                                }
                                            </td>
                                        </tr>`;

                                    receivedTbody.append(row);
                                });
                            } else {
                                notify(res.messages);
                            }

                            $('#viewrecipetmodal').modal('show');

                        }
                    }
                });
            });

            $(document).on('click', '.viewinstallments', function(event) {
                event.preventDefault();
                let account_number = $('#dds_account').val();
                let memberType = $('#paid_member_type').val();

                $.ajax({
                    url: "{{ route('viewdailyinstallments') }}",
                    type: 'post',
                    data: {
                        account_number: account_number,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let installments = res.installments;
                            let installmentbody = $('#installmentbody');
                            installmentbody.empty();

                            if (installments && installments.length > 0) {
                                installments.forEach((data) => {
                                    const formatDate = (dateStr) => {
                                        if (!dateStr || dateStr === '-' || isNaN(
                                                new Date(dateStr).getTime())) {
                                            return '-';
                                        }

                                        const date = new Date(dateStr);
                                        let day = date.getDate();
                                        let month = date.getMonth() + 1;
                                        let year = date.getFullYear();

                                        day = day < 10 ? `0${day}` : day;
                                        month = month < 10 ? `0${month}` : month;
                                        return `${day}-${month}-${year}`;
                                    };

                                    const formattedinstallmentDate = formatDate(data
                                        .installment_date);
                                    const formattedinstallmentReceiptDate = formatDate(
                                        data.payment_date ?? '-');
                                    const status = data.payment_status === 'paid' ?
                                        'Paid' : 'Unpaid';

                                    let row = `<tr>
                                        <td>${formattedinstallmentDate}</td>
                                        <td>${formattedinstallmentReceiptDate}</td>
                                        <td>${data.amount.toFixed(2)}</td>
                                        <td>${data.intallment_no}</td>
                                        <td>${status}</td>
                                    </tr>`;
                                    installmentbody.append(row);
                                });

                                // Ensure only one modal is shown at a time
                                $('#viewinstallmentModal').modal('show');
                                $('#viewrecipetmodal').modal('hide');
                            } else {
                                notify(res.messages);
                            }
                        }
                        $('#viewinstallmentModal').on('hidden.bs.modal', function() {
                            $('#viewrecipetmodal').modal('show');
                        });

                        $('#viewinstallmentModal').modal('hide');
                    }
                });
            });




            $(document).on('click', '.installdeletebtn', function(event) {
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
                        {{--  $('#ledgerModal').modal('hide'); // Hide the modal before deletion starts  --}}

                        Swal.fire({
                            title: "Deleting...",
                            text: "Please wait while we delete the transaction.",
                            icon: "info",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        $.ajax({
                            url: "{{ route('dailyinstallmentsdelete') }}",
                            type: 'post',
                            data: { id: id },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            dataType: 'json',
                            success: function(res) {
                                Swal.close()
                                if (res.status === 'success') {
                                    let account = res.daily_account;
                                    notify(res.messages);
                                    $('.trascationbutton').show();
                                    DdsReceievedshowdata(account);
                                } else {
                                    notify(res.messages);
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                {{--  $('.ccldeletebtn').prop('disabled', false);  --}}

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

            $(document).on('click', '.instteditbtn', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let memberType = $('#paid_member_type').val();

                $.ajax({
                    url: "{{ route('dailyinstallmentsmodify') }}",
                    type: 'post',
                    data: {
                        id: id,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let account = res.daily_account;
                            $('#viewrecipetmodal').modal('hide');


                            const formatDate = (dateStr) => {
                                const date = new Date(dateStr);
                                let day = date.getDate();
                                let month = date.getMonth() + 1;
                                let year = date.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                return `${day}-${month}-${year}`;
                            };

                            const formattedOpeningDate = formatDate(account.receipt_date);



                            let daily_amount = parseFloat(account.amount);
                            let deposit_amount = parseFloat(account.deposit) ? parseFloat(
                                account.deposit) : 0;
                            let opening_date = new Date(account.opening_date);
                            let currentDate = new Date();
                            let time_difference = currentDate - opening_date;
                            let day_difference = Math.round(time_difference / (1000 * 60 * 60 *
                                24));
                            let pending_amount = ((daily_amount * day_difference) -
                                deposit_amount);

                            $('#dailyid').val(account.id);
                            $('#receiveamountaccount').val(account.account_no);
                            $('#receiveaccounttype').val(account.memberType);
                            $('#received_amount_date').val(formattedOpeningDate);
                            $('#exceed_amount').val(pending_amount < 0 ? Math.abs(
                                pending_amount) : 0);
                            $('#pending_amount').val(pending_amount > 0 ? pending_amount : 0);
                            $('#receive_amount').val(account.deposit);
                            $('#payment_type').val(account.payment_mode);

                            setTimeout(function() {
                                getRecievedCashbank();
                                setTimeout(function() {
                                    $('#payment_bank').val(account.bank_name);
                                }, 1000);
                            }, 100);

                            $('#narration').val(account.narration);
                            $('#receveamountmodel').modal('show');

                        } else {
                            notify(res.messages);
                        }
                    },
                });


            });

            $(document).on('click', '.maturebtn', function(event) {
                event.preventDefault();
                let account_number = $('#dds_account').val();
                let memberType = $('#paid_member_type').val();

                $('#mreceiveamountaccount').val(account_number);
                $('#mreceiveaccounttype').val(memberType);
                $('#matureModal').modal('show');


                $.ajax({
                    url: "{{ route('getdetaildailyaccountmature') }}",
                    type: 'post',
                    data: {
                        account_number: account_number,
                        memberType: memberType
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let details = res.details;
                            let account_number = res.account_number;

                            const formatDate = (dateStr) => {
                                const date = new Date(dateStr);
                                let day = date.getDate();
                                let month = date.getMonth() + 1;
                                let year = date.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                return `${day}-${month}-${year}`;
                            };

                            const formattedOpeningDate = formatDate(account_number
                            .opening_date);
                            $('#start_date').val(formattedOpeningDate);
                            $('#mno').val(account_number.membershipno);
                            $('#saving_amount').val(details.accountNo);
                            $('#standing_amount').val(account_number.deposit_amount);
                            $('#interst_rate').val(account_number.interest);
                            $('#daily_amt').val(account_number.amount);

                            getmaturity();


                        } else {
                            notify(res.messages);
                        }
                    }
                });

            });

            $('#matureForm').validate({
                rules : {
                    received_amount_dated : {
                        required : true
                    },
                    saving_amount : {
                        required : true,
                        digits : true
                    },
                    standing_amount : {
                        required : true,
                        digits : true
                    },
                    interst_rate : {
                        required : true,
                    },
                    net_amount : {
                        required : true
                    }
                },messages :{
                    received_amount_dated : {
                        required : 'Enter Mature Date'
                    },
                    saving_amount : {
                        required : 'Enter Saving Account Number',
                        digits : 'Enter Only Numeric Value'
                    },
                    standing_amount : {
                        required : 'Enter Account Closing Balance',
                        digits : 'Enter Only Numeric Value'
                    },
                    interst_rate : {
                        required : 'Enter Rate Of Interest',
                    },
                    net_amount : {
                        required : 'Enter Mature Value',
                    }
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });


            $(document).on('submit', '#matureForm', function(event) {
                event.preventDefault();

                if($(this).valid()){
                    let formData = $(this).serialize();
                    $('button[type=submit]').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('dailyaccountmature') }}",
                        type: 'post',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                $('button[type=submit]').prop('disabled', false);
                                let account = res.account_number;
                                $('#matureForm')[0].reset();
                                DdsReceievedshowdata(account);
                                $('#matureModal').modal('hide');
                            } else {
                                notify(res.messages);
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.unmature', function(event) {
                event.preventDefault();
                let account_number = $('#dds_account').val();
                let memberType = $('#paid_member_type').val();
                let unmatureDate = $('#paid_date').val();

                $('#unmatureaccountNumber').val(account_number);
                $('#unmaturetype').val(memberType);
                $('#unmatureconfirmationmodel').modal('show');
            });

            $(document).on('submit', '#unmatureForm', function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('dailyunmature') }}",
                    type: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let account = res.account_number;
                            DdsReceievedshowdata(account);
                            $('#unmatureconfirmationmodel').modal('hide');
                        } else {
                            notify(res.messages);
                        }
                    }
                });

            });
        });
    </script>
@endpush

