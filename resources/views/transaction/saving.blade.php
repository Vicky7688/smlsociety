@extends('layouts.app')

@php
    $table = 'yes';
@endphp

@section('content')

    @php  $currentDate = Session::get('currentdate') ?? date('d-m-Y', strtotime(session('sessionStart')));  @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between">
                    <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                        <h4 class=""><span class="text-muted fw-light">Transactions / </span>Saving Account</h4>
                    </div>
                    <div class="col-md-3 accountHolderDetails">
                        <h6 class=""><span class="text-muted fw-light">Name: </span><span id="memberName"></span></h6>
                        <h6 class="pt-2"><span class="text-muted fw-light">Balance: </span><span
                                id="memberBalance"></span></h6>
                        <input type="hidden" id="fdedittyme">

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <form id="savingform" name="savingform">
                            <div class="row row-gap-2">
                                <input type="hidden" name="savingId" id="savingId">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                    <label for="transactionDate" class="form-label">Date</label>
                                    <input type="text" class="form-control formInputs mydatepic transactionDate"
                                        placeholder="DD-MM-YYYY" id="transactionDate" name="transactionDate"
                                        value="{{ $currentDate }}" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="" class="form-label">Account No.</label>
                                    <input type="text" class="form-control formInputs" oninput="getsavingacclist('this')"
                                        id="accountNo" name="accountNo" placeholder="Account No" autocomplete="off" />
                                    <div id="accountList" class="accountList"></div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding" hidden>
                                    <label for="accountNoo" class="form-label">Membership No</label>
                                    <input type="text" class="form-control formInputs" id="membership" name="membership"
                                        placeholder="Membership No" readonly autocomplete="off" />
                                    <div id="accountListt" class="accountListt"></div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="transactionType" class="form-label">Action</label>
                                    <select class="form-select formInputsSelect" id="transactionType" name="transactionType"
                                        onchange="amountTransferToOthers('this')">
                                        <option value="Deposit">Deposit</option>
                                        <option value="Withdraw">Withdraw</option>
                                        <option value="toshare">Transfer to Share</option>
                                        <option value="toFd">Transfer to FD</option>
                                        <option value="tord">Transfer to RD</option>
                                        <option value="toloan">Transfer to loan</option>
                                        <option value="DailySaving">Transfer to DailySaving</option>
                                        <option value="toCcl">Transfer to CCL</option>

                                        {{--
                  <option value="dividend">Dividend</option>
                    --}}

                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding"
                                    id="groupdiv">
                                    <label for="paymentType" class="form-label">Payment Type</label>
                                    <select class="form-select formInputsSelect" id="groupCode" name="groupCode"
                                        onchange="getledgerCode('this')">

                                        @if (!empty($groups))
                                            <option value="" selected>Select Group</option>

                                            @foreach ($groups as $row)
                                                <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding"
                                    id="ledgerdiv">
                                    <label for="bank" class="form-label">Bank</label>
                                    <select class="form-select formInputsSelect" id="bank" name="bank">
                                        <option value="">Select Group</option>
                                    </select>
                                    <p class="error"></p>
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding rdAccountDiv">
                                    <label for="narration" class="form-label">RD Account No</label>
                                    <select name="rd_account_no" id="rd_account_no" class="form-select">
                                        <option value=""selected>Select Account</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                    <label for="transactionAmount" class="form-label">Amount</label>
                                    <input type="text" step="any" min="1" class="form-control formInputs"
                                        placeholder="0.00" id="transactionAmount" name="transactionAmount"
                                        oninput="getAmount('this')" />
                                </div>

                                {{--  <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding fdAccountDiv">
                                    <label for="narration" class="form-label">Lokin Period (Days)</label>
                                    <input type="text" class="form-control formInputs" id="lokin_period"
                                        placeholder="Lokin Period" name="lokin_period" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding fdAccountDiv">
                                    <label for="narration" class="form-label">Lokin Date</label>
                                    <input type="text" class="form-control formInputs mydatepic valid" id="lokin_date"
                                        placeholder="Maturity Date" readonly name="lokin_date"
                                        oninput="getAmount('this')" />
                                </div>  --}}
                                <input type="text" hidden class="form-control formInputs" id="rd_scheme_id"
                                    placeholder="Scheme Name" readonly name="rd_scheme_id" />
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding rdAccountDiv">
                                    <label for="narration" class="form-label">Scheme Name</label>
                                    <input type="text" class="form-control formInputs" id="rd_scheme_name"
                                        placeholder="Scheme Name" readonly name="rd_scheme_name" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding rdAccountDiv">
                                    <label for="narration" class="form-label">Installment Amount</label>
                                    <input type="text" readonly class="form-control formInputs" id="rd_mount"
                                        placeholder="Installemt Amount" readonly name="rd_mount" value="0" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding rdAccountDiv">
                                    <label for="narration" class="form-label">Received Amount</label>
                                    <input type="text" readonly class="form-control formInputs"
                                        id="rd_received_amount" placeholder="Received Amount" readonly
                                        name="rd_received_amount" value="0" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding rdAccountDiv">
                                    <label for="narration" class="form-label">Months</label>
                                    <input type="text" class="form-control formInputs" id="rdmonths"
                                        placeholder="Months" readonly name="rdmonths" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                    <label for="agentId" class="form-label">Agent</label>
                                    <select class="form-select formInputsSelect" id="agentId" name="agentId">

                                        @if (!empty($agents))
                                            @foreach ($agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Agent Present</option>
                                        @endif

                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                    <label for="narration" class="form-label">Narration</label>
                                    <input type="text" class="form-control formInputs" id="narration"
                                        placeholder="Narration" name="narration" />
                                </div>
                                <div
                                    class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding savingColumnButton">
                                    <div class="d-flex h-100 justify-content-end text-end">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalrecive" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">Transfer to Loan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="installmentForm">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="membershipnumbers" id="membershipnumbers">
                                <input type="hidden" name="trfloanid" id="trfloanid">
                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <input id="text" type="text" name="trfinstalldate"
                                        class="form-control form-control-sm mydatepic trfinstalldate"
                                        placeholder="DD-MM-YYYY" required />
                                </div>
                                <div class="col-md-4">
                                    <label for="memberType" class="form-label">Member Type</label>
                                    <select class="form-select formInputsSelect" id="trfmemberType" name="trfmemberType">
                                        <option value="Member">Member</option>
                                        <option value="NonMember">Non Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Principle</label>
                                    <input type="text" name="trfprinciple" id="trfprinciple" value="0"
                                        class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Intrest</label>
                                    <input type="text" name="trfintrest" id="trfintrest" value="0"
                                        class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Panelty</label>
                                    <input type="text" name="trfpanelty" id="trfpanelty" value="0"
                                        class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total payment</label>
                                    <input type="text" name="trftotalpayment" id="trftotalpayment" value="0"
                                        class="form-control form-control-sm" readonly autocomplete="off">
                                </div>
                                <div class="col-md-4" style="display: none">
                                    <label class="form-label">memberBalanceinput</label>
                                    <input type="text" name="memberBalanceinput" id="memberBalanceinput"
                                        value="0" class="form-control form-control-sm" readonly autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr class="my-3">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="updateButton" class="btn btn-primary">Transfer</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card tablee">
            <div class="card-body">
                <div class="table-responsive tabledata">
                    <table class="table datatables-order table table-bordered" id="table" style="width:100%">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th scope="col">Sr.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Deposit</th>
                                <th scope="col">Wthdraw</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Entered By</th>
                                <th scope="col">Action</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="modal fade" id="dailysavingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">Transfer to Daily Saving</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="dailysavingtrfdForm" name="dailysavingtrfdForm">
                            <div class="row">

                                <input type="hidden" name="membershipnumberss" id="membershipnumberss">
                                <input type="hidden" name="savingtrfdid" id="savingtrfdid">
                                <input type="hidden" name="trfddailysavingid" id="trfddailysavingid">
                                <input type="hidden" name="trfdsavingstype" id="trfdsavingstype">
                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <input id="currentdatedailysaving" type="text" name="currentdatedailysaving"
                                        class="form-control mydatepic trfinstalldate" placeholder="DD-MM-YYYY"
                                        value="{{ date('d-m-Y') }}" />
                                </div>

                                <input type="text" hidden name="savingaccountnumber" id="savingaccountnumber"
                                    value="0" class="form-control " autocomplete="off">

                                <div class="col-md-4">
                                    <label class="form-label">Daily Saving A/C</label>
                                    <select name="dailysavingaccountno" id="dailysavingaccountno" class="form-select">
                                        <option value=""selected>Select A/c</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Amount</label>
                                    <input type="text" name="trfddailyamount" id="trfddailyamount" value="0"
                                        class="form-control " autocomplete="off" oninput="getAmount('this')">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Narration</label>
                                    <input type="text" name="trfddailyamountnarration" id="trfddailyamountnarration"
                                        class="form-control " autocomplete="off">
                                </div>

                            </div>
                    </div>
                    <hr class="my-3">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="updateButton" class="btn btn-primary">Transfer</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="fdmodal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">Transfer to Daily Saving</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="fdtrfdForm" name="fdtrfdForm">
                            <div class="row">
                                <input type="text" hidden id="saccount" name="saccount">
                                <input type="text" hidden id="mtypes" name="mtypes">
                                <input type="text" hidden id="savingfddids" name="savingfddids">
                                <input type="text" hidden id="mnumberss" name="mnumberss">

                                <div class="col-md-2">
                                    <label class="form-label">Date</label>
                                    <input id="datessss" type="text" name="datessss"
                                        class="form-control mydatepic trfinstalldate" placeholder="DD-MM-YYYY"
                                        value="{{ date('d-m-Y') }}" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Principal Amount</label>
                                    <input type="text" class="form-control formInputs" id="principal_amount"
                                        placeholder="Principal Amount" name="principal_amount" value="0"
                                        oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-4 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding">
                                    <label for="narration" class="form-label">FD Account No</label>
                                    <select name="fdid" id="fdid" class="form-select"
                                        onchange="getFdAccount('this')">
                                        <option value=""selected>Select FD</option>
                                    </select>
                                    {{--  <input type="text" class="form-control formInputs" id=""
                                    placeholder="FD Account No" readonly name="" />  --}}

                                </div>



                                <input type="text" hidden name="fdaccounts" id="fdaccounts">

                                <input type="text" hidden name="interestType" id="interestType">

                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">FD Date</label>
                                    <input type="text" class="form-control formInputs   transactionDate"
                                        id="fd_date" placeholder="FD Date" name="fd_date"
                                        value="{{ Session::get('currentdate') }}" oninput="getAmount(this)" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Scheme Name</label>
                                    <input type="text" class="form-control formInputs" id="fd_scheme_name"
                                        placeholder="Scheme Name" readonly name="fd_scheme_name" />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Year</label>
                                    <input type="text" class="form-control formInputs" id="years"
                                        placeholder="Year" name="years" oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Months</label>
                                    <input type="text" class="form-control formInputs" id="months"
                                        placeholder="Months" name="months" oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Days</label>
                                    <input type="text" class="form-control formInputs" id="days"
                                        placeholder="Days" name="days" oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Rate of Interest</label>
                                    <input type="text" class="form-control formInputs" id="rate_of_interest"
                                        placeholder="Interest" name="rate_of_interest" oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Interest Amount</label>
                                    <input type="text" class="form-control formInputs" id="interest_amount"
                                        placeholder="Interest Amount" name="interest_amount"
                                        oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Maturity Amount</label>
                                    <input type="text" class="form-control formInputs" id="maturity_amount"
                                        placeholder="Maturity Amount" name="maturity_amount"
                                        oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding  ">
                                    <label for="narration" class="form-label">Maturity Date</label>
                                    <input type="text" class="form-control formInputs mydatepic valid"
                                        id="maturity_date" placeholder="Maturity Date" name="maturity_date"
                                        oninput="getAmount('this')" />
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding ">
                                    <label for="narration" class="form-label">Auto Renew</label>
                                    <select name="autorenew" id="autorenew" class="form-select">
                                        <option value="yes"selected>Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    {{--  <input type="text" class="form-control formInputs" id=""
                                placeholder="FD Account No" readonly name="" />  --}}

                                </div>
                            </div>
                    </div>
                    <hr class="my-3">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="updateButton" class="btn btn-primary">Transfer</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="interestModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">Interest Received</h5>
                        {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body">
                        <form id="interestForm" name="interestForm">
                            <div class="row">
                                <input type="text" hidden id="interestid" name="interestid">

                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <input id="interest_date" type="text" name="interest_date"
                                        class="form-control" placeholder="DD-MM-YYYY"
                                        value="{{ date('d-m-Y') }}"  readonly/>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Account No</label>
                                    <input id="ineterest_account" type="text" name="ineterest_account"
                                        class="form-control " placeholder="Account No"
                                       readonly/>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Interest Amount</label>
                                    <input id="interest_paid_amount" type="text" name="interest_paid_amount"
                                        class="form-control " placeholder="Interest Amount"
                                       />
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="updateButton" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="cclModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalreciveTitle">CCL Transfer</h5>
                        <h5 class="modal-title" id="cclbalnces">Balance</h5>

                        {{--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  --}}
                    </div>
                    <div class="modal-body">
                        <form id="cclttrfdForm" name="cclttrfdForm">
                            <div class="row">
                                <input type="text" hidden id="cclid" name="cclid">
                                <input type="text" hidden id="sbid" name="sbid">
                                <input type="text" hidden id="savingaccts" name="savingaccts">
                                <input type="text" hidden id="sbmemberno" name="sbmemberno">
                                <input type="text" hidden id="sbmembertype" name="sbmembertype">
                                <input type="text" hidden id="ccltrfdamount" name="ccltrfdamount">
                                <input type="text" hidden id="ccltrfdsavingupdateid" name="ccltrfdsavingupdateid">



                                <div class="col-md-3">
                                    <label class="">Transfer Date</label>
                                    <input id="ccltrfdDate" type="text" name="ccltrfdDate"
                                        class="form-control form-control-sm mydatepic valid" placeholder="DD-MM-YYYY"
                                        value="{{ date('d-m-Y') }}" onblur="checkdateinterest(this)"/>
                                </div>

                                <div class="col-md-3">
                                    <label class="">CCL Account</label>
                                    <input id="ccl_account" type="text" name="ccl_account"
                                        class="form-control form-control-sm" placeholder="Account No"
                                       readonly/>
                                </div>

                                <div class="col-md-3">
                                    <label class="">Trfd Amount</label>
                                    <input id="ccl_trfd_amount" type="text" name="ccl_trfd_amount"
                                        class="form-control form-control-sm" placeholder="Amount" onblur="checkexceedcclamounts('this')"/>
                                </div>

                                <div class="col-md-3">
                                    <label class="">Interest Amount</label>
                                    <input id="ccl_interest_amount" type="text" name="ccl_interest_amount"
                                        class="form-control form-control-sm" placeholder="Interest Amount" onblur="checkexceedcclamounts('this')"
                                       />
                                </div>
                            </div>
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger closebtncclform">Close</button>
                        <button type="submit" class="btn btn-primary">Transfer</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('script')
<script>
    function calculateTotalPayment() {
        let principle = parseFloat(document.getElementById('trfprinciple').value) || 0;
        let interest = parseFloat(document.getElementById('trfintrest').value) || 0;
        let penalty = parseFloat(document.getElementById('trfpanelty').value) || 0;
        let memberBalance = parseFloat(document.getElementById('memberBalanceinput').value) || 0;

        let totalPayment = principle + interest + penalty;
        if (totalPayment > memberBalance) {
            notify('Total payment cannot exceed member balance.', 'warning');
        }
        document.getElementById('trftotalpayment').value = totalPayment.toFixed(2);
    }

    function allowOnlyNumbers(event) {
        const value = event.target.value;
        event.target.value = value.replace(/[^0-9.]/g, '');
    }
    document.getElementById('trfprinciple').addEventListener('input', function(event) {
        allowOnlyNumbers(event);
        calculateTotalPayment();
    });
    document.getElementById('trfintrest').addEventListener('input', function(event) {
        allowOnlyNumbers(event);
        calculateTotalPayment();
    });
    document.getElementById('trfpanelty').addEventListener('input', function(event) {
        allowOnlyNumbers(event);
        calculateTotalPayment();
    });
    document.getElementById('memberBalanceinput').addEventListener('input', calculateTotalPayment);
    calculateTotalPayment();


        $(document).on('change', '#memberType', function(event) {
            event.preventDefault();

            let memberType = $('#memberType').val();

            // Common reset actions for all conditions
            $('#installmentForm')[0].reset();
            $('#dailysavingtrfdForm')[0].reset();
            $('#fdtrfdForm')[0].reset();
            $('#memberName').text('');
            $('#memberBalance').text('');
            $('#fdedittyme').val('');

            if (memberType === 'Member') {
                {{--  $('#savingform')[0].reset();  --}}
            }
        });


        $(document).ready(function() {
            $('#updateButton').on('click', function() {
                var formData = $('#installmentForm').serialize();
                $.ajax({
                    url: '{{ route('trfsavingtoloan') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        $('#installmentForm')[0].reset();
                        $('#modalrecive').modal('hide');
                        $.ajax({
                            url: "{{ route('getsavingdetails') }}",
                            type: 'post',
                            data: {
                                selectdId: response.acc,
                                transactionType: "Deposit"
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    let saving_account = res.saving_account;
                                    let opening_amount = res.opening_amount;
                                    let saving_entries = res.saving_entries;
                                    let fd_account_details = res.fd;
                                    let rd_account = res.rd_account;
                                    ShowDataTable(saving_account, opening_amount,
                                        saving_entries, fd_account_details,
                                        rd_account);
                                    getAmount();
                                } else {
                                    notify(res.messages);
                                }
                            }
                        });


                        //  if (response.success) {
                        //     $('#installmentForm')[0].reset();
                        //     $('#modalrecive').modal('hide');
                        //     update();

                        //     notify(response.message, 'success');
                        //  } else {
                        //     notify(response.message, 'warning');
                        //  }
                    },
                    error: function(xhr, status, error) {
                        //  notify(xhr.responseText, 'warning');
                    }
                });
            });
        });


        //___________Legder's Behalf Of Group
        function getledgerCode() {
            let groups_code = $('#groupCode').val();

            $.ajax({
                url: "{{ route('getledgers') }}",
                type: 'post',
                data: {
                    groups_code: groups_code
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let ledgerDropdown = document.getElementById('bank');
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

        //__________Get Saving Account List
        function getsavingacclist() {
            let account_no = $('#accountNo').val();
            let memberType = $('#memberType').val();

            $.ajax({
                url: "{{ route('getsavingacclist') }}",
                type: 'post',
                data: {
                    account_no: account_no,
                    memberType: memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let accounts = res.accounts;
                        let accountList = $('#accountList');
                        accountList.empty();

                        if (accounts) {
                            accounts.forEach((data) => {
                                accountList.append(
                                    `<div class="accountLists" data-id="${data.accountNo}">${data.accountNo}</div>`
                                );
                            });
                        } else {
                            accountList.append(`<div class="accountLists">No Account</div>`);
                        }
                    }
                }
            });
        }


        //______Check Not Paid Exceed Amount
        function getAmount() {
            let transactionType = $('#transactionType').val();
            let typesToCheck = ['Withdraw', 'toloan', 'tord', 'toFd', 'toshare', 'dividend', 'DailySaving']
            if (typesToCheck.includes(transactionType)) {
                if (transactionType == 'toFd') {

                    let fdedittyme = parseFloat($('#fdedittyme').val());
                    let amount = parseFloat($('#principal_amount').val());

                    if (isNaN(amount) || amount > fdedittyme) {
                        notify(`qqqAmount exceeds your available balance of ${fdedittyme}`, 'warning');
                        $('#principal_amount').val('');
                    }
                } else {
                    let memberBalance = parseFloat($('#memberBalance').text());

                    let amount = parseFloat($('#transactionAmount').val());

                    if (isNaN(amount) || amount > memberBalance) {
                        $('#transactionAmount').val('');
                        notify(`qqqAmount exceeds your available balance of ${memberBalance}`, 'warning');
                        $('#principal_amount').val('');
                    }
                }
            }

            if (transactionType === 'DailySaving') {
                let memberbalance = parseFloat($('#memberBalance').text());
                let trfddailyamount = $('#trfddailyamount').val();
                if (isNaN(trfddailyamount) || trfddailyamount > memberbalance) {
                    $('#trfddailyamount').val('');
                    notify(`aaAmount exceeds your available balance of ${memberbalance}`, 'warning');
                }
            }



            if (transactionType === 'toFd') {
                {{--  let amounts = parseFloat($('#transactionAmount').val());  --}}
                var principal = parseFloat($('#principal_amount').val()) || 0;
                var rate = parseFloat($('#rate_of_interest').val()) || 0;
                var interestType = $('#interestType').val() || 'QuarterlyCompounded';

                var interestStartDateStr = $("#fd_date").val();
                var maturityDateStr = $("#maturity_date").val();
                var interestStartDateFormatted = reverseFormatDate(interestStartDateStr);
                var maturityDateFormatted = reverseFormatDate(maturityDateStr);

                var interestStartDate = new Date(interestStartDateFormatted);
                var maturityDate = new Date(maturityDateFormatted);

                var totalDays = (maturityDate - interestStartDate) / (1000 * 60 * 60 * 24);
                // if (totalDays < 0) {
                //     alert("Maturity date must be after the interest start date.");
                //     return;
                // }
                var interest = 0;
                var maturityAmount = principal;

                if (interestType === 'QuarterlyCompounded') {

                    var rate = rate; // Annual interest rate in percentage (example: 5%)
                    var maturityAmount = maturityAmount; // Starting amount (example: 1000)
                    var interest = 0; // Interest that will accumulate
                    var totalMonths = $('#months').val(); // Total duration in months
                    var totaldayss = totalMonths * 30; // Total duration in months
                    var quatredays = 90; // Total duration in months

                    // Calculate the quarterly rate
                    var quarterlyRate = rate / 4 / 100; // Quarterly interest rate

                    // Determine the number of full quarters and remaining months
                    var fullQuarters = Math.floor(totaldayss / quatredays); // 3 months in a quarter
                    var extraMonths = totaldayss % quatredays; // Remaining months after full quarters

                    console.log('Full Quarters:', fullQuarters);
                    console.log('Extra Months:', extraMonths);

                    // Apply interest for full quarters
                    for (var i = 0; i < fullQuarters; i++) {
                        var quarterlyInterest = maturityAmount * quarterlyRate;
                        interest += quarterlyInterest;
                        maturityAmount += quarterlyInterest;
                    }

                    console.log('Interest after full quarters:', interest);
                    console.log('Maturity Amount after full quarters:', maturityAmount);

                    // Handle the extra months (if any)
                    if (extraMonths > 0) {
                        var monthlyInterestRate = rate / 365 / 100; // Monthly interest rate
                        var extraInterest = maturityAmount * monthlyInterestRate * extraMonths;
                        console.log('Extra Interest for', extraMonths, 'months:', extraInterest);

                        interest += extraInterest;
                        maturityAmount += extraInterest;
                    }

                    console.log('Total Interest:', interest);
                    console.log('Final Maturity Amount:', maturityAmount);


                } else if (interestType === 'AnnualCompounded') {
                    maturityAmount = principal * Math.pow(1 + (rate / 100), totalDays / 365);
                    interest = maturityAmount - principal;
                } else if (interestType === 'Fixed') {
                    interest = principal * (rate / 100) * (totalDays / 365);
                    maturityAmount += interest;
                }
                $('#interest_amount').val(Math.round(interest));
                maturityAmount = Math.round(maturityAmount);
                $('#maturity_amount').val(isNaN(maturityAmount) ? 0 : maturityAmount);

                {{--  $('#principal_amount').val(principal);  --}}
            } else {
                $('#interest_amount').val('');
                $('#maturity_date').val('');
                $('#maturity_amount').val('');
                $('#total_days').val('');
                $('#lokin_date').val('');

            }
        }

        function ShowDataTable(saving_account, opening_amount, saving_entries, fd_account_details, rd_account) {
            let transactionType = $('#transactionType').val();

            if (transactionType !== 'Deposit') {
                let tableBody = $('#tableBody');
                tableBody.empty();
                //__________Append Opening Balance Row
                tableBody.append(
                    `<tr><td colspan="4">Opening Balance</td><td>${opening_amount.toFixed(2)}</td><td></td><td></td></tr>`
                );

                $('#memberBalance').empty();
                $('#memberName').empty();

                //________Show Customer Name
                $('#memberName').append(saving_account.customer_name);
                $('#membership').val(saving_account.membershipno);

                if (saving_entries) {
                    let balance_amount = parseFloat(opening_amount);
                    saving_entries.forEach((data, index) => {
                        // Set Date Format
                        let dates = new Date(data.transactionDate);
                        let day = String(dates.getDate()).padStart(2, '0');
                        let month = String(dates.getMonth() + 1).padStart(2, '0');
                        let year = dates.getFullYear();
                        let formattedDate = `${day}-${month}-${year}`;

                        // Get Account Closing Balance
                        let deposit_amount = parseFloat(data.depositAmount) ?? 0;
                        let withdraw_amount = parseFloat(data.withdrawAmount) ?? 0;
                        balance_amount += deposit_amount - withdraw_amount;

                        // Append Data in Table
                        let saving_row = ` <tr>
                            <td>${index + 1}</td>
                            <td>${formattedDate}</td>
                            <td>${data.depositAmount.toFixed(2)}</td>
                            <td>${data.withdrawAmount.toFixed(2)}</td>
                            <td>${balance_amount.toFixed(2)}</td>
                            <td>${data.username}</td>`;

                        if (data.chequeNo === 'trfdSaving') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Agent Commission') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFD') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Security Trfd') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromDDS') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromRD') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'LoanTrfdSaving') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromLoan') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'CCL Limit') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'SavingTrfdToLoan') {
                            saving_row += `<td style="width:85px;">
                                <button class="btn" onclick=deltqry(${data.id}) >
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;
                        } else if (data.chequeNo === 'Interest Received') {
                            {{--  saving_row += `<td style="width:85px;">
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;  --}}


                             saving_row += `
                            <td style="width:85px;">
                               <button class="btn interestbtn"
                                    data-id="${data.id}"
                                    >
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>     `;
                        } else if (data.chequeNo === 'SavingTrfd') {
                            saving_row += `<td style="width:85px;">
                                 <button class="btn savinngtrfdcclbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;



                        } else if (data.chequeNo === 'TrfdToLoan') {
                            saving_row += `<td style="width:85px;">

                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;

                        } else {
                            saving_row += `<td style="width:85px;">
                                <button class="btn editbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;
                        }

                        // Add narration and close the row
                        saving_row += `<td>${data.narration}</td></tr>`;

                        tableBody.append(saving_row);
                        $('#accountNo').attr('disabled', false);

                    });
                    $('#fdid').prop('disabled', false);

                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');

                    //__________Show Closing Balance in Account
                    $('#memberBalance').append(balance_amount.toFixed(2));
                    $('#fdedittyme').val(balance_amount.toFixed(2));
                    $('#memberBalanceinput').val(balance_amount.toFixed(2));
                }
            } else {
                let tableBody = $('#tableBody');
                tableBody.empty();
                //__________Append Opening Balance Row
                tableBody.append(
                    `<tr><td colspan="4">Opening Balance</td><td>${opening_amount}</td><td></td><td></td></tr>`);

                $('#memberBalance').empty();
                $('#memberName').empty();

                //________Show Customer Name
                $('#memberName').append(saving_account.customer_name);
                $('#membership').val(saving_account.membershipno);

                if (saving_entries) {
                    let balance_amount = parseFloat(opening_amount);

                    saving_entries.forEach((data, index) => {
                        // Set Date Format
                        let dates = new Date(data.transactionDate);
                        let day = String(dates.getDate()).padStart(2, '0');
                        let month = String(dates.getMonth() + 1).padStart(2, '0');
                        let year = dates.getFullYear();
                        let formattedDate = `${day}-${month}-${year}`;

                        // Get Account Closing Balance
                        let deposit_amount = parseFloat(data.depositAmount) ?? 0;
                        let withdraw_amount = parseFloat(data.withdrawAmount) ?? 0;
                        balance_amount += deposit_amount - withdraw_amount;

                        // Append Data in Table
                        let saving_row = `<tr>
                                <td>${index + 1}</td>
                                <td>${formattedDate}</td>
                                <td>${data.depositAmount.toFixed(2)}</td>
                                <td>${data.withdrawAmount.toFixed(2)}</td>
                                <td>${balance_amount.toFixed(2)}</td>
                                <td>${data.username}</td>
                            `;


                        // Conditional logic to add different rows based on `data.chequeNo`
                        if (data.chequeNo === 'trfdSaving') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Agent Commission') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFD') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Security Trfd') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'tord') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromRD') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromDDS') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromDDS') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromLoan') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'CCL Limit') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'SavingTrfdToLoan') {
                            saving_row += `<td style="width:85px;">
                                  <button class="btn" onclick=deltqry(${data.id}) >
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;
                        } else if (data.chequeNo === 'TrfdToLoan') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Interest Received') {
                            saving_row += `
                            <td style="width:85px;">
                               <button class="btn interestbtn"
                                    data-id="${data.id}"
                                    >
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>     `;
                        } else if (data.chequeNo === 'SavingTrfd') {
                            saving_row += `<td style="width:85px;">
                                 <button class="btn savinngtrfdcclbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;

                        } else {
                            saving_row += `<td style="width:85px;">
                                <button class="btn editbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td> `;
                        }

                        // Add narration and close the row
                        saving_row += `<td>${data.narration}</td></tr>`;
                        tableBody.append(saving_row);
                        $('#accountNo').attr('disabled', false);

                    });
                    let fdaccounts = $('#fdid');
                    fdaccounts.empty();


                    fdaccounts.append(`<option value=""selected>Select FD A/c</option>`);


                    if (fd_account_details && fd_account_details.length > 0) {
                        fd_account_details.forEach((data, index) => {

                            //___________Set Date Format
                            let dates = new Date(data.transactionDate);
                            let day = dates.getDate();
                            let month = dates.getMonth() + 1;
                            let year = dates.getFullYear();

                            day = day < 10 ? `0${day}` : day;
                            month = month < 10 ? `0${month}` : month;
                            let formettedDate = `${day}-${month}-${year}`;

                            fdaccounts.append(
                                `<option value="${data.id}">${data.accountNo}-${data.schemename}</option>`);
                        });
                    }

                    let rdAccountDropdown = $('#rd_account_no');
                    rdAccountDropdown.empty();

                    // Set default option
                    rdAccountDropdown.append(`<option value="">Select Rd Acc</option>`);

                    // Populate dropdown if there are accounts

                    console.log(rd_account);
                    if (rd_account && rd_account.length > 0) {
                        rd_account.forEach((data) => {
                            rdAccountDropdown.append(
                                `<option class="rdaccountlist" value="${data.rd_account_no}">${data.rd_account_no}-RD-${data.amount}</option>`
                            );
                        });
                    }

                    // Add onchange event to the dropdown
                    rdAccountDropdown.on('change', function() {
                        const selectedAccountNo = $(this).val();
                        if (selectedAccountNo) {
                            rdaccountget(selectedAccountNo);
                        }
                    });
                    //__________Show Closing Balance in Account
                    $('#memberBalance').append(balance_amount.toFixed(2));

                    $('#fdedittyme').val(balance_amount.toFixed(2));
                    $('#memberBalanceinput').val(balance_amount.toFixed(2));
                }
            }
        }

        //________Get RD Account
        function rdaccountget(selectedAccountNo) {
            $.ajax({
                url: "{{ route('getrdaccountdetails') }}",
                type: 'post',
                data: {
                    selectedAccountNo: selectedAccountNo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let rd_account = res.rd_accounts;

                        if (rd_account) {
                            $('#rd_scheme_id').val(rd_account.id);
                            $('#rd_scheme_name').val(rd_account.name);
                            $('#rd_mount').val(rd_account.amount);
                            $('#rd_received_amount').val(rd_account.deposit);
                            $('#rdmonths').val(rd_account.month);
                        } else {
                            notify(res.messages || 'Please open an RD in Recurring Deposit.', 'warning');
                            $('#rd_scheme_id').val('');
                            $('#rd_scheme_name').val('');
                            $('#rd_mount').val(0);
                            $('#rd_received_amount').val(0);
                            $('#rdmonths').val(0);
                        }
                    } else {
                        notify(res.messages || 'Please open an RD in Recurring Deposit.', 'warning');
                    }
                }
            });
        }


        function checkdateinterest(ele) {
            let lastDate = $(ele).val();
            let id = $('#cclid').val();
            let receipt_date = $('#ccltrfdDate').val();

            $.ajax({
                url: "{{ route('getcheckinterestdatewiseccl') }}",
                type: 'POST',
                data: {id: id,receipt_date: receipt_date},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        let cclDetails = res.cclDetails;
                        let totalWithdraw = parseFloat(res.totalWithdraw) || 0;
                        let totaldeposit = parseFloat(res.totalDeposit) || 0;
                        let interestRate = parseFloat(res.interestRate) || 0;
                        let days = parseFloat(res.days) || 0;
                        let interest_amount = 0;

                        let balances = 0;

                        if (cclDetails || totalWithdraw || totaldeposit  || interestRate || days) {
                            balances = totalWithdraw - totaldeposit;
                            interest_amount = Math.round((((balances * interestRate) / 100) / 365) * days);
                            console.log(interest_amount);

                            $('#cclbalnces').text('Balance :- '+balances);
                            $('#ccl_interest_amount').val(interest_amount);
                            $('#ccltrfdamount').val(balances);
                            $('#cclModal').modal('show');

                        }else {

                            $('#ccltrfdamount').val('');
                            $('#ccl_interest_amount').val('');
                            $('#cclid').val('');
                            $('#sbid').val('');
                            $('#savingaccts').val('');
                            $('#balance_amount').val('');
                            $('#sbmembertype').val('');
                            $('#cclbalnces').val('');
                            $('#cclModal').modal('hide');

                        }

                    } else {
                        notify(res.messages, 'warning');
                    }
                }
            });
        }

        function checkexceedcclamounts(){

            let entredAmount = parseFloat($('#ccl_trfd_amount').val()) || 0;
            let enteredinterest = parseFloat($('#ccl_interest_amount').val()) || 0;
            let cclbalance = parseFloat($('#ccltrfdamount').val()) || 0;
            let savingsamounts = parseFloat($('#fdedittyme').val()) || 0;
            let cclbalncess = parseFloat($('#cclbalnces').val()) || 0;
            console.log(entredAmount,enteredinterest,cclbalncess,savingsamounts);

            if(isNaN(entredAmount) || isNaN(enteredinterest) || isNaN(cclbalance)){
                notify('Entered Numeric Value','warning');
                return ;
            }

            if(entredAmount > cclbalance){
                $('#ccl_trfd_amount').val('');
                notify(`Entered Amount ${entredAmount} Exceed Then CCL Balance Amount ${cclbalance}`,'warning');
            }

            if(enteredinterest > cclbalance){
                $('#ccl_interest_amount').val('');
                notify(`Entered Amount ${enteredinterest} Exceed Then CCL Balance Amount ${cclbalance}`,'warning');
            }

            if(enteredinterest > savingsamounts){
                $('#ccl_interest_amount').val('');
                notify(`Entered Amount ${enteredinterest} Exceed Then Saving Balance Amount ${savingsamounts}`,'warning');
            }

            if(entredAmount > savingsamounts){
                $('#ccl_trfd_amount').val('');
                notify(`Entered Amount ${entredAmount} Exceed Then Saving Balance Amount ${savingsamounts}`,'warning');
            }



        }

        $('.rdAccountDiv').hide();

        function amountTransferToOthers() {
            let accountNo = $('#accountNo').val();

            let transactionType = $('#transactionType').val();

            switch (transactionType) {
                case 'Deposit':
                    $('#groupdiv').show();
                    $('#ledgerdiv').show();

                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');

                    $('#rd_account_no').val('');
                    $('#rd_scheme_name').val('');
                    $('#rd_mount').val('');
                    $('#rd_received_amount').val('');
                    $('#rdmonths').val('');


                    break;
                case 'Withdraw':
                    $('#groupdiv').show();
                    $('#ledgerdiv').show();

                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');

                    $('#rd_account_no').val('');
                    $('#rd_scheme_name').val('');
                    $('#rd_mount').val('');
                    $('#rd_received_amount').val('');
                    $('#rdmonths').val('');

                    break;
                case 'toshare':
                    $('#groupdiv').hide();
                    $('#ledgerdiv').hide();
                    $('.rdAccountDiv').hide();

                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');


                    $('#rd_account_no').val('');
                    $('#rd_scheme_name').val('');
                    $('#rd_mount').val('');
                    $('#rd_received_amount').val('');
                    $('#rdmonths').val('');


                    break;
                case 'toFd':
                    var mmshpno = $('#membership').val();
                    var memberType = $('#memberType').val();
                    var transactionDate = $('#transactionDate').val();
                    var memberType = $('#memberType').val();
                    {{--  $('#groupdiv').hide();
                    $('#ledgerdiv').hide();
                    $('.rdAccountDiv').hide();  --}}
                    $('#rd_account_no').val('');
                    $('#rd_scheme_name').val('');
                    $('#rd_mount').val('');
                    $('#rd_received_amount').val('');
                    $('#rdmonths').val('');

                    $('#saccount').val(accountNo);
                    $('#mtypes').val(memberType);
                    $('#datessss').val(transactionDate);
                    $('#mnumberss').val(mmshpno);

                    $('#fdmodal').modal('show');

                    break;
                case 'tord':
                    $('#groupdiv').hide();
                    $('#ledgerdiv').hide();
                    $('.rdAccountDiv').show();

                    $('#fd_date').val('');
                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');

                    break;
                case 'toloan':

                    var mmshpno = $('#membership').val();
                    var memberType = $('#memberType').val();
                    var transactionDate = $('#transactionDate').val();
                    var memberType = $('#memberType').val();

                    $.ajax({
                        url: '{{ route('getloanpending') }}',
                        type: 'POST',
                        data: {
                            mmshpno: mmshpno,
                            memberType: memberType,
                            transactionDate: transactionDate,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {


                            if (response.status === true) {

                                $('#trfloanid').val(response.loanid);
                                $('.trfinstalldate').val(transactionDate);
                                $('#trfmemberType').val(memberType);
                                $('#trfprinciple').val(response.installmet.principal);
                                $('#trfintrest').val(response.installmet.interest);
                                $('#trfpanelty').val(0);
                                $('#trftotalpayment').val(response.installmet.total);
                                $('#membershipnumbers').val(mmshpno);
                                $('#modalrecive').modal('show');
                            } else {
                                notify(response.status, 'warning');
                            }
                        },
                        error: function(xhr, status, error) {
                            notify(xhr.responseText, 'warning');
                        }
                    });
                    break;
                case 'DailySaving':

                    let membership_no = $('#membership').val();
                    let memberTypes = $('#memberType').val();
                    let transactionDates = $('#transactionDate').val();

                    $.ajax({
                        url: '{{ route('getdailysavingaccount') }}',
                        type: 'POST',
                        data: {
                            membership_no: membership_no,
                            memberTypes: memberTypes,
                            transactionDates: transactionDates,
                            accountNo: accountNo
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                let dailyaccount = res.daily_accounts;

                                $('#savingaccountnumber').val(accountNo);
                                $('#trfdsavingstype').val(memberTypes);
                                $('#membershipnumberss').val(membership_no);

                                if (dailyaccount && dailyaccount.length > 0) {
                                    $('#dailysavingaccountno').empty().append(
                                        '<option value="" selected>Select A/c</option>');

                                    dailyaccount.forEach((data) => {
                                        $('#dailysavingaccountno').append(
                                            `<option value="${data.account_no}">${data.account_no}</option>`
                                        );
                                    });
                                }

                                // Show the modal
                                $('#dailysavingModal').modal('show');
                            } else {
                                notify(res.messages, 'warning');
                            }
                        },
                        error: function(xhr, status, error) {
                            notify(xhr.resText, 'warning');
                        }
                    });
                    break;
                case 'toCcl' :

                    let membern = $('#membership').val();
                    let memberTypess = $('#memberType').val();
                    let transactionDatess = $('#transactionDate').val();

                    $.ajax({
                        url : "{{ route('getcclaccountdetails') }}",
                        type : 'post',
                        data : {membern : membern , memberTypess : memberTypess , transactionDatess: transactionDatess, accountNo : accountNo },
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        dataType : 'json',
                        success : function(res){
                            if(res.status === 'success'){

                                let cclDetails = res.cclDetails;
                                let saving_account = res.savings;
                                let totalWithdraw = parseFloat(res.totalWithdraw) || 0;
                                let totaldeposit = parseFloat(res.totalDeposit) || 0;
                                let interestRate = parseFloat(res.interestRate) || 0;
                                let days = parseFloat(res.days) || 0;
                                let interest_amount = 0;

                                let balances = 0;

                                if (cclDetails && saving_account || totalWithdraw || totaldeposit  || interestRate || days) {
                                    balances = totalWithdraw - totaldeposit;
                                    interest_amount = Math.round((((balances * interestRate) / 100) / 365) * days);

                                    $('#cclid').val(cclDetails.id);
                                    $('#ccl_account').val(cclDetails.cclNo);
                                    $('#sbid').val(saving_account.id);
                                    $('#savingaccts').val(saving_account.accountNo);
                                    $('#sbmemberno').val(cclDetails.membership);
                                    $('#sbmembertype').val(cclDetails.memberType);
                                    $('#cclbalnces').text('Balance :- '+balances);
                                    $('#ccl_interest_amount').val(interest_amount);
                                    $('#ccltrfdamount').val(balances);
                                    $('#cclModal').modal('show');

                                } else {

                                    $('#ccltrfdamount').val('');
                                    $('#ccl_interest_amount').val('');
                                    $('#cclid').val('');
                                    $('#sbid').val('');
                                    $('#savingaccts').val('');
                                    $('#balance_amount').val('');
                                    $('#sbmembertype').val('');
                                    $('#cclbalnces').val('');
                                    $('#cclModal').modal('hide');

                                }

                            }else{
                                notify(res.messages,'warning');
                            }
                        }
                    });
                break;
            }
        }

        function amountCheckNotExceedAndMultiple() {
            let enteredAmount = parseFloat($('#transactionAmount').val());
            let rd_amount = parseFloat($('#rd_mount').val());
            let month = parseFloat($('#rdmonths').val());
            let rd_received_amount = parseFloat($('#rd_received_amount').val()) || 0;
            if (isNaN(enteredAmount) || enteredAmount <= 0) {
                notify('Please enter a valid deposit amount greater than zero.', 'warning');
                return;
            }
            if (isNaN(rd_amount) || isNaN(month)) {
                notify('Invalid RD amount or month value.', 'warning');
                return;
            }

            let principal = (rd_amount * month) - rd_received_amount;
            if (enteredAmount % rd_amount !== 0) {
                $('#transactionAmount').val('');
                notify(`The entered amount (${enteredAmount}) must be a multiple of ${rd_amount}`, 'warning');
                return;
            }
            if (enteredAmount > principal) {
                $('#transactionAmount').val('');
                notify('Entered amount exceeds the allowed principal.', 'warning');
                return;
            }
            notify('Amount is valid', 'success');
        }

        function getFdAccount() {
            let fdid = $('#fdid').val();
            let transactionAmount = $('#transactionAmount').val();

            $.ajax({
                url: "{{ route('getfddetails') }}",
                type: 'post',
                data: {
                    fdid: fdid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let data = res.fdaccount;
                        $('#fd_scheme_name').val(data.name);
                        {{--  $('#principal_amount').val(transactionAmount);  --}}
                        $('#days').val(data.days);
                        $('#months').val(data.months);
                        $('#years').val(data.years);
                        $('#rate_of_interest').val(data.interest);
                        $('#maturity_amount').val();
                        $('#maturity_date').val();
                        $('#lokin_period').val(data.lockin_days);
                        $("#interestType").val(data.renewInterestType).trigger('change');
                        $('#fdaccounts').val(data.accountNo);
                        calculateMaturityDate();
                        {{--  calculateInterestAmount();  --}}
                    } else {
                        $('#fdid').prop('disabled', false);
                        notify(res.messages);
                    }
                }
            });
        }

        function reverseFormatDate(dateStr) {
            var parts = dateStr.split('-');
            if (parts.length !== 3) {
                return null;
            }
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        function calculateMaturityDate() {
            if ($('#accountNo').val() == "") {
                var days = 1;
            } else {
                var days = parseInt($('#days').val(), 10) || 0;
            }
            var openingDateStr = reverseFormatDate($("#fd_date").val());
            var years = parseInt($('#years').val(), 10) || 0;
            var months = parseInt($('#months').val(), 10) || 0;

            var openingDate = new Date(openingDateStr);
            // Calculate maturity date
            const maturityDate = new Date(openingDate);
            maturityDate.setFullYear(maturityDate.getFullYear() + years);
            maturityDate.setMonth(maturityDate.getMonth() + months);
            maturityDate.setDate(maturityDate.getDate() + days);

            // Format the maturity date
            const day = ("0" + maturityDate.getDate()).slice(-2);
            const month = ("0" + (maturityDate.getMonth() + 1)).slice(-2);
            const year = maturityDate.getFullYear();
            const formattedMaturityDate = `${day}-${month}-${year}`;

            $('#maturity_date').val(formattedMaturityDate);
        }

        $("#openingDate, #years, #months, #days, #principal_amount, #rate_of_interest").on('input', getAmount);
        $("#interestType").on('change', getAmount);


        $(document).ready(function() {

            $('#rd_mount, #rd_received_amount, #rdmonths, #transactionAmount').on('blur',
                amountCheckNotExceedAndMultiple);

            $('#savingform').validate({
                rules: {
                    transactionDate: {
                        required: true
                    },
                    memberType: {
                        required: true
                    },
                    accountNo: {
                        required: true,
                        digits: true
                    },
                    transactionType: {
                        required: true
                    },
                    transactionAmount: {
                        required: true,
                        digits: true
                    },
                    groupCode: {
                        required: true
                    },
                    agentId: {
                        required: true
                    }
                },
                messages: {
                    transactionDate: {
                        required: 'Select Date'
                    },
                    memberType: {
                        required: 'Select Member Type'
                    },
                    accountNo: {
                        required: 'Enter Account Number',
                        digits: 'Entry Only Numeric Value'
                    },
                    transactionType: {
                        required: 'Select Transaction Type'
                    },
                    transactionAmount: {
                        required: 'Enter Amount',
                        digits: 'Enter Only Numreic Value'
                    },
                    groupCode: {
                        required: 'Select Group Type'
                    },
                    agentId: {
                        required: 'Select Agent'
                    }
                },
                errorElement: 'p',
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

            $(document).on('click', '.accountLists', function(e) {
                e.preventDefault();
                let selectdId = $(this).data('id');
                $('#accountNo').val(selectdId);
                let membership = $('#membership').val();
                let memberType = $('#memberType').val();
                $('#accountList').html('');

                let transactionType = $('#transactionType').val();

                $.ajax({
                    url: "{{ route('getsavingdetails') }}",
                    type: 'post',
                    data: {
                        selectdId: selectdId,
                        transactionType: transactionType,
                        memberType: memberType,
                        membership: membership
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let saving_account = res.saving_account;
                            let opening_amount = res.opening_amount;
                            let saving_entries = res.saving_entries;
                            let fd_account_details = res.fd;
                            let rd_account = res.rd_account;

                            if (saving_account.membertype === 'Member') {
                                $('#memberType').val(saving_account.membertype).trigger('change');
                                ShowDataTable(saving_account, opening_amount, saving_entries,fd_account_details, rd_account);
                                getAmount();
                            } else if (saving_account.membertype === 'Staff') {
                                $('#memberType').val(saving_account.membertype).trigger('change');
                                ShowDataTable(saving_account, opening_amount, saving_entries,fd_account_details, rd_account);
                                getAmount();
                            } else {
                                $('#memberType').val(saving_account.membertype).trigger('change');
                                ShowDataTable(saving_account, opening_amount, saving_entries,fd_account_details, rd_account);
                                getAmount();
                            }
                        } else {
                            notify(res.messages);
                        }
                    }
                });
            });

            $(document).on('submit', '#savingform', function(e) {
                e.preventDefault();
                if ($(this).valid()) {
                    let transactionType = $('#transactionType').val();
                    let typesToCheck = ['toloan', 'tord', 'toFd', 'toshare', 'dividend'];
                    let formData = $(this).serialize();

                    let url = $('#savingId').val() ? "{{ route('savingentryupdate') }}" :
                        "{{ route('savingentryinsert') }}";

                    if (transactionType === 'Deposit' || transactionType === 'Withdraw' || typesToCheck
                        .includes(transactionType)) {
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
                                    notify(res.messages, 'success');
                                    let account = res.saving_account;

                                    setTimeout(() => {
                                        $('#memberType').append(
                                            `<option value="${account.membertype}">${account.membertype}</option>`
                                            );
                                        $('#memberType').val(account.membertype)
                                        .change();
                                        updateTable(res);
                                    }, 100);

                                    $('#savingform')[0].reset();
                                } else {
                                    notify(res.messages, 'warning');
                                }

                            }
                        });
                    }
                }
            });

            function updateTable(res) {
                // Reset form
                let saving_account = res.saving_account;

                let opening_amount = res.opening_amount;
                let saving_entries = res.saving_entries;
                let tableBody = $('#tableBody');
                tableBody.empty();



                // Append Opening Balance Row
                tableBody.append(`
                <tr>
                    <td colspan="4">Opening Balance</td>
                    <td>${opening_amount.toFixed(2)}</td>
                    <td></td>
                    <td></td>
                </tr>`);

                $('#memberBalance').empty();
                $('#memberName').empty();

                // Show Customer Name
                $('#memberName').append(saving_account.customer_name);
                $('#membership').val(saving_account.membershipno);

                if (saving_entries) {
                    let balance_amount = parseFloat(opening_amount);

                    saving_entries.forEach((data, index) => {
                        // Set Date Format
                        let dates = new Date(data.transactionDate);
                        let day = String(dates.getDate()).padStart(2, '0');
                        let month = String(dates.getMonth() + 1).padStart(2, '0');
                        let year = dates.getFullYear();
                        let formattedDate = `${day}-${month}-${year}`;

                        // Get Account Closing Balance
                        let deposit_amount = parseFloat(data.depositAmount) ?? 0;
                        let withdraw_amount = parseFloat(data.withdrawAmount) ?? 0;
                        balance_amount += deposit_amount - withdraw_amount;

                        // Append Data in Table
                        let saving_row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formattedDate}</td>
                                <td>${data.depositAmount.toFixed(2)}</td>
                                <td>${data.withdrawAmount.toFixed(2)}</td>
                                <td>${balance_amount.toFixed(2)}</td>
                                <td>${data.username}</td>
                            `;

                        if (data.chequeNo === 'trfdSaving') {
                            saving_row += `
                            <td></td>
                        `;
                        } else if (data.chequeNo === 'Agent Commission') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFD') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'Security Trfd') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromDDS') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'trfdFromLoan') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'CCL Limit') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'LoanTrfdSaving') {
                            saving_row += `<td></td>`;
                        } else if (data.chequeNo === 'SavingTrfdToLoan') {
                            saving_row += `
                            <td style="width:85px;">
                                 <button class="btn" onclick=deltqry(${data.id}) >
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>
                        `;
                        } else if (data.chequeNo === 'Interest Received') {

                            saving_row += `
                            <td style="width:85px;">

                               <button class="btn interestbtn"
                                    data-id="${data.id}"
                                    >
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>

                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>

                            </td>`;



                            {{--  $('#interestModal').modal('show')  --}}
                            {{--  saving_row += `<td style="width:85px;">
                            <button class="btn deletebtn"
                                data-account-no="${data.accountId}"
                                data-id="${data.id}"
                                data-transaction-type="${data.transactionType}"
                                data-cheque-no="${data.chequeNo}">
                                <i class="fa-solid fa-trash iconsColorCustom"></i>
                            </button>
                        </td>`;  --}}
                        } else if (data.chequeNo === 'TrfdToLoan') {
                            saving_row += `
                            <td style="width:85px;">
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>
                        `;
                     } else if (data.chequeNo === 'SavingTrfd') {
                            saving_row += `<td style="width:85px;">
                                 <button class="btn savinngtrfdcclbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>`;

                        } else {
                            saving_row += `
                            <td style="width:85px;">
                                <button class="btn editbtn"
                                    data-id="${data.id}">
                                    <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                </button>
                                <button class="btn deletebtn"
                                    data-account-no="${data.accountId}"
                                    data-id="${data.id}"
                                    data-transaction-type="${data.transactionType}"
                                    data-cheque-no="${data.chequeNo}">
                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                </button>
                            </td>
                        `;
                        }

                        saving_row += `<td>${data.narration}</td></tr>`;
                        tableBody.append(saving_row);
                        $('#accountNo').attr('disabled', false);

                    });


                    $('#fdid').val('');
                    $('#fd_scheme_name').val('');
                    $('#principal_amount').val('');
                    $('#days').val('');
                    $('#months').val('');
                    $('#years').val('');
                    $('#rate_of_interest').val('');
                    $('#maturity_amount').val('');
                    $('#maturity_date').val('');
                    $('#lokin_period').val('');
                    $('.fdAccountDiv').hide();
                    $('#memberBalance').append(balance_amount.toFixed(2));
                    $('#fdedittyme').val(balance_amount.toFixed(2));
                    $('#memberBalanceinput').val(balance_amount.toFixed(2));
                    notify(res.messages, 'success');
                }
            }
            $(document).on('click', '.interestbtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                console.log(id);

                $.ajax({
                    url: "{{ route('editpaidinterest') }}",
                    type: 'post',
                    data: { id: id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let details = res.details;
                            if(details){

                                let entry_date = new Date(details.transactionDate);
                                let day = entry_date.getDate();
                                let month = entry_date.getMonth() + 1;
                                let year = entry_date.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                let transcationDate = `${day}-${month}-${year}`;


                                {{--  $('#interestForm  --}}
                                $('#interest_date').val(transcationDate);
                                $('#interestid').val(details.id);
                                $('#ineterest_account').val(details.accountId);
                                $('#interest_paid_amount').val(details.depositAmount);
                                $('#interestModal').modal('show');
                            }else{
                                $('#interestid').val('');
                                $('#ineterest_account').val('');
                                $('#interest_paid_amount').val('');
                                $('#interestModal').modal('hide');
                            }

                            // $('#interestModal').modal('show');
                        }else{
                            notify(res.messages,'warning');
                        }
                    }
                });
            });

            $(document).on('submit', '#interestForm', function (event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('paidinterestchange') }}",
                    type: 'post',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            notify(res.messages, 'success');
                            $('#interestForm')[0].reset();
                            $('#interestModal').modal('hide');
                            updateTable(res);
                        } else {
                            notify(res.messages, 'warning');
                        }
                    },
                    error: function (err) {
                        console.error(err);
                        notify('An error occurred while processing your request', 'danger');
                    }
                });
            });

            $(document).on('click','.closebtncclform',function(event){
                event.preventDefault();
                $('#cclttrfdForm')[0].reset();
                $('#cclModal').modal('hide');
            });

            // function deltqry(id) {

            //             alert(id);
            //         // var confirmation = confirm("Are you sure you want to delete this query?");
            //         // if (confirmation) {
            //         //     $.ajax({
            //         //         url: '{{ route('dlttrfsavingtoloan') }}',
            //         //         type: 'GET',
            //         //         data: { id: id },
            //         //         success: function(response) {
            //         //             console.log("Query deleted successfully", response);
            //         //         },
            //         //         error: function(xhr, status, error) {
            //         //             console.error("Error deleting query", error);
            //         //         }
            //         //     });
            //         // } else {
            //         //     console.log("Deletion canceled.");
            //         // }

            //         }

            $(document).on('click', '.editbtn', function(event) {
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('getsavingeditdetails') }}",
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
                            let savings = res.savings;
                            let rd_accounts = res.rd_accounts;
                            let dailyaccount = res.dailyaccount;
                            let fds = res.fd;

                            $('#savingId').val(savings.id);
                            $('#accountNo').val(savings.accountId);
                            $('#membership').val(savings.accountNo);


                            if (savings.transactionType === 'toshare') {
                                $('#groupdiv').hide();
                                $('#ledgerdiv').hide();
                                $('#accountNo').attr('readonly', true);
                                $('#groupCode').val('');
                                $('#bank').val('');

                                $('#transactionType option').each(function() {
                                    if ($(this).val() !== 'toshare') {
                                        $(this).hide();
                                    }
                                });
                                setTimeout(() => {
                                    if ($('#memberType option[value="' + savings
                                            .memberType + '"]').length === 0) {
                                        $('#memberType').val(savings.memberType)
                                        .change();
                                    }

                                    // Make dropdown simulate readonly
                                    $('#memberType').on('focus mousedown', function(e) {
                                        e
                                    .preventDefault(); // Prevent dropdown interaction
                                    });

                                    $('#accountNo').prop('readonly', true);
                                }, 100);




                            } else if (savings.transactionType === 'tord') {
                                setTimeout(() => {
                                    if ($('#memberType option[value="' + savings
                                            .memberType + '"]').length === 0) {
                                        $('#memberType').append(
                                            `<option value="${savings.memberType}">${savings.memberType}</option>`
                                            );
                                    }

                                    $('#memberType').val(savings.memberType).change();
                                    $('#memberType').prop('readonly', true);
                                    $('#accountNo').prop('readonly', true);
                                }, 100);

                                $('#groupdiv').hide();
                                $('#ledgerdiv').hide();
                                $('#accountNo').attr('readonly', true);
                                $('#groupCode').val('');
                                $('#bank').val('');
                                $('.rdAccountDiv').show();

                                $('#transactionType option').each(function() {
                                    if ($(this).val() !== 'tord') {
                                        $(this).hide();
                                    }
                                });


                                $('#rd_account_no option').each(function() {
                                    if ($(this).val() === rd_accounts.rd_account_no) {
                                        $(this).prop('selected', true);
                                    } else {
                                        $(this).prop('selected', false);
                                    }
                                });

                                $('#rd_scheme_name').val(rd_accounts.name);
                                $('#rd_mount').val(rd_accounts.amount);
                                $('#rd_received_amount').val(rd_accounts.deposit);
                                $('#rdmonths').val(rd_accounts.month);

                            } else if (savings.transactionType === 'DailySaving') {

                                setTimeout(() => {
                                    if ($('#memberType option[value="' + savings
                                            .memberType + '"]').length === 0) {
                                        $('#memberType').val(savings.memberType)
                                        .change();
                                    }

                                    // Make dropdown simulate readonly
                                    $('#memberType').on('focus mousedown', function(e) {
                                        e
                                    .preventDefault(); // Prevent dropdown interaction
                                    });

                                    $('#accountNo').prop('readonly', true);
                                }, 100);



                                $('#groupdiv').hide();
                                $('#ledgerdiv').hide();
                                $('#accountNo').attr('readonly', true);
                                $('#groupCode').val('');
                                $('#bank').val('');

                                {{--  console.log(dailyaccount.account_no);  --}}

                                {{--  $('#dailysavingaccountno option').each(function() {

                                });  --}}
                                // Wait for dropdown to populate
                                setTimeout(() => {
                                    // Append the account number as an option
                                    $('#dailysavingaccountno').append(
                                        `<option value="${dailyaccount.account_no}">${dailyaccount.account_no}</option>`
                                        );

                                    // Set the dynamically added option as the selected value
                                    $('#dailysavingaccountno').val(dailyaccount
                                        .account_no).change();
                                }, 500);



                                $('#membershipnumberss').val(dailyaccount.membershipno);
                                $('#trfddailysavingid').val(dailyaccount.id);
                                $('#savingtrfdid').val(savings.id);
                                $('#trfdsavingstype').val(dailyaccount.memberType);
                                $('#savingaccountnumber').val(savings.accountId);
                                $('#trfddailyamount').val(dailyaccount.deposit);
                                $('#trfddailyamountnarration').val(dailyaccount.narration);

                                // Show the modal
                                $('#dailysavingModal').modal('show');
                            } else if (savings.transactionType === 'toFd') {

                                setTimeout(() => {
                                    if ($('#memberType option[value="' + savings
                                            .memberType + '"]').length === 0) {
                                        $('#memberType').val(savings.memberType)
                                        .change();
                                    }

                                    // Make dropdown simulate readonly
                                    $('#memberType').on('focus mousedown', function(e) {
                                        e
                                    .preventDefault(); // Prevent dropdown interaction
                                    });

                                    $('#accountNo').prop('readonly', true);
                                }, 100);



                                $('#groupdiv').hide();
                                $('#ledgerdiv').hide();
                                $('#accountNo').attr('disabled', true);
                                $('#groupCode').val('');
                                $('#bank').val('');

                                $('#fdmodal').modal('show');

                                var mmshpno = $('#membership').val();
                                var memberType = $('#memberType').val();
                                var transactionDate = $('#transactionDate').val();
                                var memberType = $('#memberType').val();

                                $('#saccount').val(savings.accountNo);
                                $('#mtypes').val(memberType);
                                $('#datessss').val(transactionDate);
                                $('#mnumberss').val(mmshpno);
                                $('#savingfddids').val(savings.id);


                                var pehlivalue = $('#memberBalance').text();
                                var valuechiye = parseFloat(fds.principalAmount) + parseFloat(
                                    pehlivalue);

                                $('#fdedittyme').val(valuechiye);
                                setTimeout(() => {
                                    // Append the account number as an option
                                    $('#fdid').append(
                                        `<option value="${fds.idss}">${fds.accountNo}-${fds.schname}</option>`
                                        );
                                    $('#fdid').val(fds.idss).change();
                                    $('#autorenew').val(fds.autorenew);
                                }, 500);

                                $('#principal_amount').val(fds.principalAmount)

                                {{--  $('#membershipnumberss').val(dailyaccount.membershipno);
                                $('#trfddailysavingid').val(dailyaccount.id);
                                $('#savingtrfdid').val(savings.id);
                                $('#trfdsavingstype').val(dailyaccount.memberType);
                                $('#savingaccountnumber').val(savings.accountId);
                                $('#trfddailyamount').val(dailyaccount.deposit);
                                $('#trfddailyamountnarration').val(dailyaccount.narration);

                                // Show the modal
                                $('#dailysavingModal').modal('show');  --}}





                            } else if (savings.transactionType === 'Deposit' || savings
                                .transactionType === 'Withdraw') {

                                $('#groupCode').val(savings.paymentType);
                                setTimeout(function() {
                                    getledgerCode();
                                    setTimeout(function() {
                                        $('#bank').val(savings.bank);
                                    }, 1000);
                                }, 100);

                                $('#groupdiv').show();
                                $('#ledgerdiv').show();


                                $('#accountNo').val(savings.accountNo);

                                $('#transactionDate').val(savings.transactionDate);

                                $('#membership').val(savings.accountNo);
                                $('#savingfddids').val(savings.id);


                                $('#transactionType').val(savings.transactionType);
                                $('#transactionAmount').val(savings.depositAmount ? savings
                                    .depositAmount : savings.withdrawAmount);
                                $('#agentId').val(savings.agentId);

                                setTimeout(() => {
                                    if ($('#memberType option[value="' + savings
                                            .memberType + '"]').length === 0) {
                                        $('#memberType').val(savings.memberType)
                                        .change();
                                    }

                                    // Make dropdown simulate readonly
                                    $('#memberType').on('focus mousedown', function(e) {
                                        e
                                    .preventDefault(); // Prevent dropdown interaction
                                    });

                                    $('#accountNo').prop('readonly', true);
                                }, 100);


                            }
                            $('#transactionType').val(savings.transactionType);
                            $('#transactionAmount').val(savings.depositAmount ? savings
                                .depositAmount : savings.withdrawAmount);
                            $('#agentId').val(savings.agentId);


                            {{--  $('#transactionType option').each(function() {
                                if ($(this).val() !== 'Deposit' && $(this).val() !== 'Withdraw') {
                                    $(this).hide();
                                }
                            });  --}}



                        } else {
                            notify(res.messages, 'warning');
                        }
                    }
                });
            });




            $(document).on('click', '.deletebtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');
                let accountNo = $(this).data('account-no');
                let transactionType = $(this).data('transaction-type');
                let trfrdtype = $(this).data('cheque-no');
                let transactionDate = $('#transactionDate').val();

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
                            url: "{{ route('deletesavingentry') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                transactionType: transactionType,
                                trfrdtype: trfrdtype,
                                accountNo: accountNo,
                                transactionDate: transactionDate
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: "json",
                            success: function(res) {
                                if (res.status === 'success') {
                                    // Close the loading modal
                                    swal.close();

                                    // Update UI based on response data
                                    let saving_account = res.saving_account;
                                    let opening_amount = res.opening_amount;
                                    let saving_entries = res.saving_entries;
                                    let fd_account_details = res.fd;
                                    let rd_account = res.rd_account;

                                    ShowDataTable(
                                        saving_account,
                                        opening_amount,
                                        saving_entries,
                                        fd_account_details,
                                        rd_account
                                    );

                                    getAmount();
                                    $('#savingform')[0].reset();
                                } else {
                                    swal.close();
                                    notify(res.messages, 'warning');
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






            $(document).on('submit', '#dailysavingtrfdForm', function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
                let url = $('#savingtrfdid').val() ? "{{ route('savingtrfddailyupdate') }}" :
                    "{{ route('savingtrfddailyaccount') }}"
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
                            notify(res.messages, 'success');
                            $('#dailysavingtrfdForm')[0].reset();
                            $('#savingform')[0].reset();
                            $('#dailysavingModal').modal('hide');
                            updateTable(res);
                        } else {
                            notify(res.messages);
                        }
                    }
                });
            });

            $(document).on('submit', '#fdtrfdForm', function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
                let url = $('#savingfddids').val() ? "{{ route('fdtrfddailyupdate') }}" :
                    "{{ route('fdtrfddailyaccount') }}"
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
                            notify(res.messages, 'success');
                            $('#fdtrfdForm')[0].reset();
                            $('#savingform')[0].reset();
                            $('#fdmodal').modal('hide');
                            updateTable(res);
                        } else {
                            notify(res.messages);
                        }
                    }
                });
            });

            //___________CCL Entry
            $(document).on('submit','#cclttrfdForm',function(event){
                event.preventDefault();

                let formData = $(this).serialize();
                let url = $('#ccltrfdsavingupdateid').val() ? "{{ route('savingtrfdtocclrecoveryupdate') }}" : "{{ route('savingtrfdtocclrecovery') }}";

                $.ajax({
                    url : url,
                    type : 'post',
                    data : formData,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if (res.status === 'success') {
                            notify(res.messages, 'success');
                            $('#fdtrfdForm')[0].reset();
                            $('#cclttrfdForm')[0].reset();
                            $('#savingform')[0].reset();
                            $('#cclModal').modal('hide');
                            updateTable(res);
                        } else {
                            notify(res.messages);
                        }
                    }
                });
            });


            $(document).on('click','.savinngtrfdcclbtn',function(event){
                event.preventDefault();
                let id = $(this).data('id');
                let transactionDate = $('#transactionDate').val();


                $('#transactionAmount').val('');

                $.ajax({
                    url : "{{ route('editsavingtrdfccl') }}",
                    type : 'post',
                    data : {id : id, transactionDate:transactionDate},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){
                            let payments = res.payments;
                            let cclDetails = res.cclDetails;
                            let saving_account = res.savings;
                            let allData = res.allData;
                            let balances = 0;


                            if(payments || cclDetails || saving_account || allData){

                                if(Array.isArray(allData) && allData.length > 0){
                                    allData.forEach((data) => {
                                        let withdraw = parseFloat(data.transfer_amount) || 0;
                                        let recovery = parseFloat(data.recovey_amount) || 0;
                                        balances += withdraw - recovery;
                                    });
                                }

                                balances +=payments.recovey_amount;

                                $('#cclid').val(cclDetails.id);
                                $('#ccl_account').val(cclDetails.cclNo);
                                $('#ccltrfdsavingupdateid').val(saving_account.id);
                                $('#savingaccts').val(saving_account.accountNo);
                                $('#sbmemberno').val(cclDetails.membership);
                                $('#sbmembertype').val(cclDetails.memberType);
                                $('#ccltrfdamount').val(balances);
                                $('#cclbalnces').text('Balance :- '+balances);
                                $('#ccl_interest_amount').val(payments.interest_amount);
                                $('#ccl_trfd_amount').val(payments.recovey_amount);
                                $('#cclModal').modal('show');
                            }else{
                                $('#cclid').val('');
                                $('#ccl_account').val('');
                                $('#ccltrfdsavingupdateid').val('');
                                $('#savingaccts').val('');
                                $('#sbmemberno').val('');
                                $('#sbmembertype').val('');
                                $('#cclbalnces').text('');
                                $('#ccl_interest_amount').val('');
                                $('#ccl_trfd_amount').val('');
                                $('#cclModal').modal('hide');
                            }

                        }else{
                            notify(res.messages,'warning');
                        }
                    }
                });
            });
        });

    </script>
    <script>
        function deltqry(id) {

            // alert(id);
            var confirmation = confirm("Are you sure you want to delete this query?");
            if (confirmation) {
                $.ajax({
                    url: '{{ route('dlttrfsavingtoloan') }}',
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(response) {

                        $.ajax({
                            url: "{{ route('getsavingdetails') }}",
                            type: 'post',
                            data: {
                                selectdId: response.acc,
                                transactionType: "Deposit"
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    let saving_account = res.saving_account;
                                    let opening_amount = res.opening_amount;
                                    let saving_entries = res.saving_entries;
                                    let fd_account_details = res.fd;
                                    let rd_account = res.rd_account;
                                    ShowDataTable(saving_account, opening_amount, saving_entries,
                                        fd_account_details, rd_account);
                                    getAmount();
                                } else {
                                    notify(res.messages);
                                }
                            }
                        });

                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting query", error);
                    }
                });
            } else {
                console.log("Deletion canceled.");
            }
        }
    </script>
@endpush
@push('style')
    <style>
        .tablee table th,
        .tablee table td {
            padding: 8px;
        }

        .saving_column {
            position: relative;
        }

        {{--  .saving_column .error {
            position: absolute;
            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }  --}} .page_headings h4,
        .page_headings h6 {
            margin-bottom: 0;
        }

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
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

        button.btn.editbtn {
            padding: 0 5px;
        }

        button.btn.deletebtn {
            padding: 0 5px;
        }

        .error {}
    </style>
@endpush
