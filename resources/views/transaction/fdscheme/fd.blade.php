@extends('layouts.app')

@php
    $table = 'yes';
@endphp
<style>
    .modal-footer.pt-3.fdCustomButtons .waves-light {
	margin-right: 5px;
}
</style>
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings cards mb-4">
            <div class="card-body py-2">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-6 fdHeading">
                        <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Fixed Deposit (Scheme)</h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="py-2 fdHeadingName"><span class="text-muted fw-light">Name: </span><span
                                id="memberName"></span></h6>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body cardsY">
                        <input type="hidden" id="accountOpeningDateSession">
                        <form action="javascript:void(0)" id="formData" name="formData" autocomplete="off">
                            <div class="nav-align-top rdCustom">
                                <ul class="nav nav-tabs fdCustomUL" role="tablist">
                                    <!-- removed "nav-pills" & "nav-fill" -->
                                    <li class="col-md-3 nav-item m-3 mt-0" role="presentation">
                                        <a class="nav-link active" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#fdDetails" aria-controls="fdDetails" aria-selected="false"
                                            tabindex="-1"> FD Details
                                        </a>
                                    </li>
                                    <li class="col-md-3 nav-item m-3 mt-0" role="presentation">
                                        <a class="nav-link" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#nomineeDetails" aria-controls="nomineeDetails"
                                            aria-selected="true">
                                            Nominee Details
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content tableContent fdTabContent mt-2">
                                    <div class="tab-pane fade active show" id="fdDetails" role="tabpanel">
                                        @csrf
                                        <input type="hidden" id="fdId" name="fdId" value="new">
                                        <!-- HTML Input Field -->

                                        <div class="row">
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="openingDate" class="form-label">Opening Date</label>
                                                <input type="text" id="openingDate" name="openingDate"
                                                    class=" form-control form-control-sm transactionDate"
                                                    placeholder="Opening Date" value="{{ Session::get('currentdate') }}"
                                                    max="{{ now()->format('d-m-Y') }}"
                                                    onchange="checkDateSessionForStore(this)" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label class="form-label mb-1" for="memberType">Member Type</label>
                                                <select name="memberType" id="memberType"
                                                    class="select21 form-select form-select-sm" data-placeholder="Active"
                                                    onblur="">
                                                    <option value="Member">Member</option>
                                                    <option value="NonMember">Non Member</option>
                                                    <option value="Staff">Staff</option>
                                                </select>
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-3 col-md-4 col-sm-5 col-7">
                                                <label class="form-label mb-1" for="">FD Type</label>
                                                <select name="fdType" id="fdType"
                                                    class="select21 form-select form-select-sm" data-placeholder="Active">
                                                    <option value="">Select Fd Type</option>
                                                    @foreach ($FdTypeMaster as $item)
                                                        <option value="{{ $item->id }}">{{ $item->type }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-3 col-md-4 col-sm-5 col-7">
                                                {{--  <input type="hidden" name="schemetype">  --}}
                                                <label for="scheme_name" class="form-label">Scheme</label>
                                                <select name="scheme_name" id="scheme_name"
                                                    class="select21 form-select form-select-sm" data-placeholder="Active">
                                                    <option value="">Select Scheme</option>
                                                </select>
                                                <small class="text-danger error-schemetype"></small>
                                            </div>





                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="membershipno" class="form-label">Membership No</label>
                                                <input type="text" id="membershipno" name="membershipno"
                                                    class="form-control form-control-sm" placeholder="Membership No"
                                                      />
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 saving_column">
                                                <label for="accountNo" class="form-label">Account No</label>
                                                <input type="text" id="accountNo" name="accountNo"
                                                    class="form-control form-control-sm " placeholder="Account No" />
                                                <div id="accountList" class="accountList"></div>
                                                <p class="error"></p>
                                            </div>





                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="principalAmount" class="form-label">Principal Amount</label>
                                                <input type="text" step="any" min="1" id="principalAmount"
                                                    name="principalAmount" class="form-control form-control-sm "
                                                    placeholder="0.00" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="paymentType" class="form-label">Payment Type</label>
                                                <select class="form-select form-select-sm Select" id="paymentType"
                                                    name="paymentType">
                                                    <option value="">Select</option>
                                                    @if (!empty($groups))
                                                        @foreach ($groups as $group)
                                                            <option value="{{ $group->groupCode }}">{{ $group->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="bank" class="form-label">Bank</label>
                                                <select class="form-select form-select-sm Select" id="bank"
                                                    name="bank">
                                                    <option value="">Select</option>

                                                    {{-- @if (!empty($ledgers))
                                                @foreach ($ledgers as $ledger)
                                                <option value="{{$ledger->ledgerCode}}">{{$ledger->name}}</option>
                                                @endforeach
                                                @endif --}}
                                                </select>
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label class="form-label mb-1" for="interestType">Interest Type</label>
                                                <input type="text" step="any" min="1" id="interestType"
                                                    name="interestType" class="form-control form-control-sm "
                                                    data-placeholder="Active" readonly />

                                                {{-- <select name="interestType" id="interestType" class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                                <option value="Fixed">Fixed</option>
                                                <option value="AnnualCompounded">Annual Compounded</option>
                                                <option value="QuarterlyCompounded">Quarterly Compounded</option>
                                            </select>
                                            <script>
                                                document.getElementById('interestType').addEventListener('mousedown', function(e) {
                                                    e.preventDefault();
                                                });
                                            </script>  --}}
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="interestStartDate" class="form-label">Interest Runs
                                                    Date</label>
                                                <input type="text" id="interestStartDate" name="interestStartDate"
                                                    class="mydatepic form-control form-control-sm"
                                                    placeholder="Interest Start Date" value="{{ now()->format('d-m-Y') }}"
                                                    readonly />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="interestRate" class="form-label">Interest Rate</label>
                                                <input type="text" step="any" min="1" id="interestRate"
                                                    name="interestRate" class="form-control form-control-sm"
                                                    placeholder="0.00" />
                                                <p class="error"></p>
                                            </div>
                                            <input type="hidden" step="any" min="0" id="interestAmount"
                                                name="interestAmount" class="form-control form-control-sm"
                                                placeholder="Interest Paid" />
                                            <!-- <div class="col-md-3 col-sm-12 mb-3">
                                                <label for="interestAmount" class="form-label">Interest Amount</label>
                                                <input type="text" step="any" min="0" id="interestAmount" name="interestAmount"
                                                    class="form-control form-control-sm" placeholder="Interest Paid" readonly />
                                                <p class="error"></p>
                                            </div> -->
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="years" class="form-label">Years</label>
                                                <input type="text" id="years" min="0" name="years"
                                                    class="form-control form-control-sm" placeholder="Years" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="months" class="form-label">Months</label>
                                                <input type="text" id="months" min="0" name="months"
                                                    class="form-control form-control-sm" placeholder="Months" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="days" class="form-label">Days</label>
                                                <input type="text" id="days" min="0" name="days"
                                                    class="form-control form-control-sm" placeholder="Days" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="maturityDate" class="form-label">Maturity Date</label>
                                                <input type="text" id="maturityDate" name="maturityDate"
                                                    class="form-control form-control-sm  " placeholder="Maturity Date"
                                                    value="{{ now()->format('d-m-Y') }}" readonly />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="maturityAmount" class="form-label">Maturity Amount</label>
                                                <input type="text" step="any" min="1" id="maturityAmount"
                                                    name="maturityAmount" class="form-control form-control-sm"
                                                    placeholder="Maturity Amount" readonly />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="ledgerNo" class="form-label">Ledger No</label>
                                                <input type="text" id="ledgerNo" name="ledgerNo"
                                                    class="form-control form-control-sm" placeholder="Ledger No" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="pageNo" class="form-label">Page No</label>
                                                <input type="text" id="pageNo" name="pageNo"
                                                    class="form-control form-control-sm" placeholder="Page No" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label for="agentId" class="form-label">Agent</label>
                                                <select class="form-select form-select-sm Select" id="agentId"
                                                    name="agentId">
                                                    @if (!empty($agents))
                                                        @foreach ($agents as $agent)
                                                            <option value="{{ $agent->id }}">{{ $agent->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                                <label class="form-label mb-1" for="memberType">Status</label>
                                                <select name="status" id="memberType"
                                                    class="select21 form-select form-select-sm Select"
                                                    data-placeholder="Status">
                                                    <option value="Active">Active</option>
                                                    <option value="Matured">Matured</option>
                                                    <option value="Renewed">Renewed</option>
                                                </select>
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 col-12     ">
                                                <label for="narration" class="form-label">Narration</label>
                                                <input type="text" id="narration" name="narration"
                                                    class="form-control form-control-sm" placeholder="Narration" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6">

                                               <div class="row">
                                                <label   class="form-label">Auto Renew</label>
                                                <div class="col-md-2">
                                                <div class="form-check mt-4">
                                                    <input name="autorenew" class="form-check-input" type="radio" value="yes" id="defaultRadio1" checked>
                                                    <label class="form-check-label" for="defaultRadio1">
                                                      Yes
                                                    </label>
                                                  </div>
                                                  </div>
                                                  <div class="col-md-2">
                                                <div class="form-check mt-4">
                                                    <input name="autorenew" class="form-check-input" type="radio" value="no" id="defaultRadio2">
                                                    <label class="form-check-label" for="defaultRadio2">
                                                      No
                                                    </label>
                                                  </div>
                                                  </div>
                                                  </div>


                                                <p class="error"></p>
                                            </div>

                                            <div
                                                class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                                <div class="d-flex h-100 justify-content-end text-end">

                                                    <div class="modal-footer pt-3 fdCustomButtons">
                                                        <a href="{{ route('fdscheme.index') }}" type="button"
                                                            id="newFdButton"
                                                            class=" btn btn-secondary waves-effect waves-light d-none dynamic-buttons">New
                                                            FD</a>
                                                        <button type="button" id="matureFdButton" matureId=""
                                                            class="btn btn-success waves-effect waves-light d-none dynamic-buttons">Mature
                                                            FD</button>
                                                        <button type="button" id="renewFdButton" renewId=""
                                                            class="btn btn-success waves-effect waves-light d-none dynamic-buttons">Renew
                                                            FD</button>
                                                        <button type="button" id="unmatureFdButton" unmatureId=""
                                                            class="btn btn-danger waves-effect waves-light d-none dynamic-buttons">UnMature
                                                            FD</button>
                                                        <button type="button" id="printFdButton" printId=""
                                                            class="btn btn-info waves-effect waves-light d-none dynamic-buttons">Print
                                                            FD</button>
                                                        <button type="button" id="deleteFdButton" deleteId=""
                                                            class="btn btn-warning waves-effect waves-light d-none dynamic-buttons">Delete
                                                            FD</button>
                                                        <button type="submit" id="submitButton"
                                                            class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade " id="nomineeDetails" role="tabpanel">
                                        @csrf
                                        <div class="row">
                                            <small> Nominee 1</small>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineeName1" class="form-label">Nominee Name</label>
                                                <input type="text" id="nomineeName1" name="nomineeName1"
                                                    class="form-control form-control-sm" placeholder="Name" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineeRelation1" class="form-label">Relation</label>
                                                <input type="text" id="nomineeRelation1" name="nomineeRelation1"
                                                    class="form-control form-control-sm" placeholder="Relation" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="birthDate1" class="form-label">Date Of Birth</label>
                                                <input type="text" id="birthDate1" name="birthDate1"
                                                    class="form-control form-control-sm mydatepic"
                                                    placeholder="dd-mm-yyyy" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineePhone1" class="form-label">Contact No.</label>
                                                <input type="text" id="nomineePhone1" name="nomineePhone1"
                                                    class="form-control form-control-sm" placeholder="Contact No." />
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label class="form-label" for="share">Share</label>
                                                <input type="text" id="nominee_share" name="nominee_share"
                                                    class="form-control form-control-sm" placeholder="Share">
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-12 col-6 ">
                                                <label for="nomineeAddress1" class="form-label">Address</label>
                                                <input type="text" id="nomineeAddress1" name="nomineeAddress1"
                                                    class="form-control form-control-sm" placeholder="Relation" />
                                                <p class="error"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <small> Nominee 2</small>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineeName2" class="form-label">Nominee Name</label>
                                                <input type="text" id="nomineeName2" name="nomineeName2"
                                                    class="form-control form-control-sm" placeholder="Name" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineeRelation1" class="form-label">Relation</label>
                                                <input type="text" id="nomineeRelation2" name="nomineeRelation2"
                                                    class="form-control form-control-sm mydatepic"
                                                    placeholder="Relation" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="birthDate2" class="form-label">Date Of Birth</label>
                                                <input type="text" id="birthDate2" name="birthDate2"
                                                    class="form-control form-control-sm mydatepic"
                                                    placeholder="dd-yy-yyyy" />
                                                <p class="error"></p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label for="nomineePhone2" class="form-label">Contact No.</label>
                                                <input type="text" id="nomineePhone2" name="nomineePhone2"
                                                    class="form-control form-control-sm" placeholder="Contact No." />
                                                <p class="error"></p>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                                <label class="form-label" for="share">Share</label>
                                                <input type="text" id="nominee_share" name="nominee_share"
                                                    class="form-control form-control-sm" placeholder="Share">
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-6     ">
                                                <label for="nomineeAddress2" class="form-label">Address</label>
                                                <input type="text" id="nomineeAddress2" name="nomineeAddress2"
                                                    class="form-control form-control-sm" placeholder="Relation" />
                                                <p class="error"></p>
                                            </div>
                                            <div
                                                class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                                <div class="d-flex h-100 justify-content-end text-end">
                                                    <div class="modal-footer pt-3 fdCustomButtons">
                                                        <button type="submit" id="submitButton"
                                                            class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card tablee">
            <div class="card-body data_tables">
                <div class="table-responsive tabledata">
                    <table class="table text-center table-bordered">
                        <thead class="table_head verticleAlignCenterReport">
                            <tr>
                                <th scope="col">S No</th>
                                <th scope="col">Account No</th>
                                <th scope="col">Membership No</th>
                                <th scope="col">Scheme Name</th>
                                <th scope="col">FD Date</th>
                                <th scope="col">Principal Amount</th>
                                <th scope="col">Interest</th>
                                <th scope="col">Maturity Date</th>
                                <th scope="col">Maturity Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">View</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ..........Modals.......... -->
    <div class="modal fade" id="matureModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered small_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mature FD</h5>
                    <h5 class="modal-title"><span class="text-muted fw-light">FD Date: </span><span
                            id="fdDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="matureFormData" name="matureFormData">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="matureId" name="matureId">
                        <input type="hidden" id="openingDatNonEditable">
                        <div class="row">
                            <input type="hidden" name="matureDateNonEditable" id="matureDateNonEditable">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                                <label for="matureDate" class="form-label">Maturity Date</label>
                                <input type="text" class="  form-control form-control-sm  " placeholder="DD-MM-YYYY"
                                    id="matureDate" name="matureDate" min=""
                                    value="{{ now()->format('d-m-Y') }}" max="{{ now()->format('d-m-Y') }}"
                                    onchange="checkDateSession(this)" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                                <label for="transferType" class="form-label">Action</label>
                                <select class="form-select form-select-sm Select" id="transferType" name="transferType">
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-6 saving_column saving_acc_column">
                                <label for="savingAccNo" class="form-label">Saving Account No</label>
                                <input type="text" id="savingAccNo" name="savingAccNo"
                                    class="form-control form-control-sm" placeholder="Saving Account No" />
                                <div id="savingaccountList" class="fdaccountList"></div>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                                <label for="maturePrincipal" class="form-label">Amount</label>
                                <input type="text" step="any" min="1" class="form-control form-control-sm"
                                    id="maturePrincipal" name="maturePrincipal" @readonly(true) />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-4     ">
                                <label for="actualPayableInterest" class="form-label">Actual Payable Interest</label>
                                <input type="text" step="any" min="0" class="form-control form-control-sm"
                                    id="actualPayableInterest" name="actualPayableInterest" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-4    d-none ">
                                <label for="matureInterest" class="form-label">Payable Interest (After Deductions)</label>
                                <input type="text" step="any" min="0" class="form-control form-control-sm"
                                    id="matureInterest" name="matureInterest" />
                                <p class="error"></p>
                            </div>
                            {{-- tds  --}}
                            {{--  <div class="col-lg-4 col-md-4 col-sm-4 col-4   ">
                                <label for="TDSInterest" class="form-label">TDS Interest (%)</label>
                                <input type="text" id="TDSInterest" name="TDSInterest"
                                    class="form-control form-control-sm" readonly />
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-4   ">
                                <label for="TDSAmount" class="form-label">TDS Amount</label>
                                <input type="text" id="TDSAmount" name="TDSAmount"
                                    class="form-control form-control-sm" />
                            </div>  --}}
                            {{-- tds end --}}
                            <input type="hidden" id="penaltyInterest" name="penaltyInterest"
                                class="form-control form-control-sm" min="0" step="any">

                            {{-- <div class="col-lg-4 col-md-4 col-sm-4 col-4 " id="penaltyField" style="display:none;">
                            <label class="form-label" for="penaltyInterest">Penalty Interest (%)</label>
                            <input id="penaltyInterest" name="penaltyInterest" class="form-control form-control-sm" min="0" step="any"  >
                            <p class="error"></p>
                        </div> --}}

                            <div class="col-lg-4 col-md-4 col-sm-4 col-4     ">
                                <label for="penaltyAmount" class="form-label">Penalty Amount</label>
                                <input type="text" step="any" min="0" class="form-control form-control-sm"
                                    id="penaltyAmount" name="penaltyAmount" />
                                <p class="error"></p>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-4     ">
                                <label for="matureAmount" class="form-label">Maturity Amount</label>
                                <input type="text" step="any" min="1" class="form-control form-control-sm"
                                    id="matureAmount" name="matureAmount" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12     ">
                                <label for="matureNarration" class="form-label">Narration</label>
                                <input type="text" class="form-control form-control-sm" id="matureNarration"
                                    name="matureNarration" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                <div class="d-flex h-100 justify-content-end text-end">
                                    <div class="modal-footer pt-3 fdCustomButtons">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-xl fade" id="renewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered small_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Renew FD</h5>
                    <h5 class="modal-title"><span class="text-muted fw-light">FD Date: </span><span
                            id="fdDateR"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="renewFormData" name="renewFormData">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="renewId" name="renewId">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewDate" class="form-label">Renew Date</label>
                                <input type="date" id="renewDate" name="renewDate"
                                    class="form-control form-control-sm" placeholder="Renew Date"
                                    max="{{ now()->format('Y-m-d') }}" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewFdNo" class="form-label">FD No</label>
                                <input type="text" id="renewFdNo" name="renewFdNo"
                                    class="form-control form-control-sm" placeholder="FD No" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewPrincipalAmount" class="form-label">Renew Amount</label>
                                <input type="text" step="any" min="1" id="renewPrincipalAmount"
                                    name="renewPrincipalAmount" class="form-control form-control-sm"
                                    placeholder="Renew Amount" />
                                <p class="error"></p>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <label class="form-label mb-1" for="renewInterestType">Interest Type</label>
                                <select name="renewInterestType" id="renewInterestType"
                                    class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                    <option value="Fixed">Fixed</option>
                                    <option value="AnnualCompounded">Annual Compounded</option>
                                    <option value="QuarterlyCompounded">Quarterly Compounded</option>
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewInterestStartDate" class="form-label">Interest Start Date</label>
                                <input type="date" id="renewInterestStartDate" name="renewInterestStartDate"
                                    class="form-control form-control-sm" placeholder="Interest Start Date" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewInterestRate" class="form-label">Interest Rate</label>
                                <input type="text" step="any" min="1" id="renewInterestRate"
                                    name="renewInterestRate" class="form-control form-control-sm" placeholder="0.00" />
                                <p class="error"></p>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewYears" class="form-label">Years</label>
                                <input type="text" min="0" id="renewYears" name="renewYears"
                                    class="form-control form-control-sm" placeholder="Years" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewMonths" class="form-label">Months</label>
                                <input type="text" min="0" id="renewMonths" name="renewMonths"
                                    class="form-control form-control-sm" placeholder="Months" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewDays" class="form-label">Days</label>
                                <input type="text" min="0" id="renewDays" name="renewDays"
                                    class="form-control form-control-sm" placeholder="Days" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewMaturityDate" class="form-label">Maturity Date</label>
                                <input type="text" id="renewMaturityDate" name="renewMaturityDate"
                                    class="form-control form-control-sm" placeholder="Maturity Date" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewInterestAmount" class="form-label">Interest Amount</label>
                                <input type="text" step="any" min="0" id="renewInterestAmount"
                                    name="renewInterestAmount" class="form-control form-control-sm"
                                    placeholder="Interest Amount" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                <label for="renewMaturityAmount" class="form-label">Maturity Amount</label>
                                <input type="text" step="any" min="1" id="renewMaturityAmount"
                                    name="renewMaturityAmount" class="form-control form-control-sm"
                                    placeholder="Maturity Amount" readonly />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12     ">
                                <label for="renewNarration" class="form-label">Narration</label>
                                <input type="text" id="renewNarration" name="renewNarration"
                                    class="form-control form-control-sm" placeholder="Narration" />
                                <p class="error"></p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                <div class="d-flex h-100 justify-content-end text-end">
                                    <div class="modal-footer pt-3 fdCustomButtons">
                                        <button type="button" class="btn btn-secondary px-4"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade delete_modal" id="unmatureModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="javascript:void(0)" id="unmatureFormData" name="unmatureFormData">
                    @csrf
                    <input type="hidden" id="unmatureId" name="unmatureId">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to Unmature this FD?
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                        <div class="d-flex h-100 justify-content-end text-end">
                            <div class="modal-footer pt-3 fdCustomButtons">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Confirm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade delete_modal" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="javascript:void(0)" id="deleteFormData" name="deleteFormData">
                    @csrf
                    <input type="hidden" id="deleteId" name="deleteId">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to Delete this FD?
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                        <div class="d-flex h-100 justify-content-end text-end">
                            <div class="modal-footer pt-3 fdCustomButtons">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Confirm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .tablee table th,
        .tablee table td {
            padding: 8px;
        }

        .tabledata th,
        .tabledata td {
            white-space: nowrap;
        }

        .saving_column {
            position: relative;
        }

        .saving_column .error {
            display: contents;
            position: absolute;
            bottom: 3px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }

        .accountList ul {
            position: absolute;
            left: 12px;
            bottom: 0px;
            transform: translateY(45%);
            width: calc(100% - 24px);
            background-color: aliceblue;
            border: 1px solid #fff;
            border-radius: 5px;
            max-height: 100px;
            overflow-y: auto;
            z-index: 99;
        }

        .accountList ul li {
            border-bottom: 1px solid #fff;
            border-radius: 0;
            padding: 5px 12px;
        }
    </style>
@endpush

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


    <script>
        // Function to convert dd-mm-yyyy format to a Date object
        function reverseFormatDate(dateStr) {
            var parts = dateStr.split('-');
            if (parts.length !== 3) {
                return null;
            }
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        $(document).on('change', '#fdType', function (e) {
            e.preventDefault();
            let fdType = $(this).val();
            let schemeDropdown = $('#scheme_name');
            $.ajax({
                url: "{{ route('getfdschemes') }}",
                type: 'post',
                data: {
                    fdType: fdType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (res) {
                    schemeDropdown.empty();

                    if (res.status === 'success') {
                        let schemesType = res.schemesType;

                        if (schemesType && schemesType.length > 0) {
                            schemesType.forEach((data) => {
                                schemeDropdown.append(`<option value="${data.id}">${data.name}</option>`);
                            });
                        } else {
                            schemeDropdown.append(`<option value="">No schemes available</option>`);
                        }
                    } else {
                        schemeDropdown.append(`<option value="">Error fetching schemes</option>`);
                    }
                },
                error: function () {
                    schemeDropdown.empty();
                    schemeDropdown.append(`<option value="">Unable to load schemes</option>`);
                }
            });
        });




        $(document).ready(function() {

            // -------------------- Calculation Handling Javascript (Starts) -------------------- //
            $('#memberType').change(function() {
                // This function will be executed whenever the selection changes
                // $('#formData')[0].reset();
                // $('#memberType').val($(this).val());
                // You can perform other actions based on the selected option here
            });

            $('#paymentType').change(function() {
                $("#bank").find("option").not(":first").remove();
                var groupCode = $(this).val();
                $.ajax({
                    url: '{{ route('getLedger') }}',
                    type: 'get',
                    data: {
                        groupCode: groupCode
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#bank").find("option").remove();
                        $.each(response["ledgers"], function(key, item) {
                            $("#bank").append(
                                `<option value='${item.ledgerCode}'>${item.name}</option>`
                                );
                        });
                    },
                    error: function(jqXHR, exception) {}
                });
            });


            $("#openingDate").on('input', function() {
                var openingDate = $(this).val();
                $('#interestStartDate').val(openingDate);
            });

            function calculateMaturityDate() {
                if ($('#accountNo').val() == "") {
                    var days = 1;
                } else {
                    var days = parseInt($('#days').val(), 10) || 0;
                }
                var openingDateStr = reverseFormatDate($("#openingDate").val());
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

                $('#maturityDate').val(formattedMaturityDate);
            }
            // Attach input event listener to relevant fields
            $("#openingDate, #years, #months, #days").on('input', calculateMaturityDate);


            function calculateInterestAmount() {
                var principal = parseFloat($('#principalAmount').val()) || 0;
                var rate = parseFloat($('#interestRate').val()) || 0;
                var interestType = $('#interestType').val() || 'QuarterlyCompounded';

                var interestStartDateStr = $("#interestStartDate").val();
                var maturityDateStr = $("#maturityDate").val();
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





                    // var quarterlyRate = rate / 4 / 100;
                    // var quarters = Math.round(totalDays / 91);

                    // for (var i = 0; i < quarters; i++) {
                    //     var quarterlyInterest = maturityAmount * quarterlyRate;
                    //     interest += quarterlyInterest;
                    //     maturityAmount += quarterlyInterest;
                    // }
                } else if (interestType === 'AnnualCompounded') {
                    maturityAmount = principal * Math.pow(1 + (rate / 100), totalDays / 365);
                    interest = maturityAmount - principal;
                } else if (interestType === 'Fixed') {
                    interest = principal * (rate / 100) * (totalDays / 365);
                    maturityAmount += interest;
                }
                $('#interestAmount').val(Math.round(interest));
                maturityAmount = Math.round(maturityAmount);
                $('#maturityAmount').val(isNaN(maturityAmount) ? 0 : maturityAmount);
            }

            // Event listeners for inputs
            $("#openingDate, #years, #months, #days, #principalAmount, #interestRate").on('input',
                calculateInterestAmount);
            $("#interestType").on('change', calculateInterestAmount);




            //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Mature date start
            //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Mature date start
            // Function to calculate maturity interest

            function calculateMaturityInterest() {

                var interestType = $('#interestType').val();
                var principal = parseFloat($('#maturePrincipal').val()) || 0;
                var rate = parseFloat($('#interestRate').val()) || 0;

                // Validate and format dates
                var interestStartDateStr = reverseFormatDate($("#openingDatNonEditable").val());
                if (!interestStartDateStr) {
                    alert("Please enter a valid interest start date.");
                    return;
                }
                var interestStartDate = new Date(interestStartDateStr);

                var matureDateStr = reverseFormatDate($("#matureDate").val());
                var matureDate = new Date(matureDateStr);
                // if (isNaN(matureDate.getTime())) {
                //     alert("Invalid maturity date format.");
                //     return;
                // }
                var matureDateNonEditable = new Date($("#matureDateNonEditable").val());
                var time = (matureDate - interestStartDate) / (1000 * 60 * 60 * 24); // Time in days

                // Interest calculation
                var interest = 0;
                if (interestType === 'Fixed') {
                    interest = (principal * rate * time) / (365 * 100);
                } else if (interestType === 'AnnualCompounded') {
                    interest = principal * Math.pow(1 + (rate / 100), time / 365) - principal;
                } else {
                    interest = principal * Math.pow(1 + (rate / 100 / 4), (4 * time / 365)) - principal;
                }

                var actualPayableInterest = interest;
                var isPremature = matureDateNonEditable > new Date();
                var penaltyInterest = parseFloat($('#penaltyInterest').val()) || 0;
                var penaltyAmount = parseFloat($('#penaltyAmount').val()) || 0;

                // Penalty logic
                if (isPremature && penaltyInterest > 0 && $('#penaltyAmount').val() === '') {
                    penaltyAmount = (penaltyInterest / 100) * principal;
                    // $('#penaltyAmount').val(Math.round(penaltyAmount));
                }

                // Log calculated values
                $('#actualPayableInterest').val(Math.round(actualPayableInterest));

                // Recalculate TDS
                recalculateTDS();
            }

            // TDS calculation
            function recalculateTDS() {
                var actualPayableInterest = parseFloat($('#actualPayableInterest').val()) || 0;
                var tdsAmount = 0;
                var tdsPercentage = 10; // Assume 10% TDS

                // TDS calculation on interest above ₹10,000
                if (actualPayableInterest > 10000) {
                    var taxableInterest = actualPayableInterest - 10000;
                    tdsAmount = (taxableInterest * tdsPercentage) / 100;
                    {{--  $('#TDSAmount').val(Math.round(tdsAmount));
                    $('#TDSInterest').val(tdsPercentage);  --}}
                } else {
                    $('#TDSAmount').val(0); // No TDS for amounts below ₹10,000
                    $('#TDSInterest').val(0); // Reset TDS percentage
                }
                calculateMatureAmount();
            }

            // Function to calculate the final maturity amount
            function calculateMatureAmount() {
                var principal = parseFloat($('#maturePrincipal').val()) || 0;
                var actualPayableInterest = parseFloat($('#actualPayableInterest').val()) || 0;
                var penaltyAmount = parseFloat($('#penaltyAmount').val()) || 0;
                {{--  var tdsAmount = parseFloat($('#TDSAmount').val()) || 0;  --}}

                // Calculate final interest after penalties
                var finalInterest = actualPayableInterest - penaltyAmount;
                $('#matureInterest').val(finalInterest < 0 ? 0 : Math.round(finalInterest));

                // Calculate final maturity amount
                var maturityAmount = principal + actualPayableInterest - penaltyAmount;
                $('#matureAmount').val(maturityAmount >= 0 ? maturityAmount : 0);
            }

            // Event Listeners
            $("#matureDate").on('input', calculateMaturityInterest);
            $("#matureDate").on('change', calculateMaturityInterest);
            $("#actualPayableInterest").on('input', recalculateTDS); // Recalculate TDS on interest change
            $("#penaltyAmount").on('input', function() {
                calculateMatureAmount(); // Manual penalty overrides automatic calculation
            });
            $("#matureInterest, #TDSInterest, #matureAmount").on('input', calculateMatureAmount);


            //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Mature date end






            //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Renew Mature date start
            $("#renewDate").on('input', function() {
                var renewDate = $(this).val();
                $('#renewInterestStartDate').val(renewDate);
            });

            function calculateRenewMaturityDate() {
                var renewDate = $("#renewDate").val();
                var years = $('#renewYears').val() || 0;
                var months = $('#renewMonths').val() || 0;
                var days = $('#renewDays').val() || 0;

                const maturityDate = new Date(renewDate);
                maturityDate.setFullYear(maturityDate.getFullYear() + parseInt(years, 10));
                maturityDate.setMonth(maturityDate.getMonth() + parseInt(months, 10));
                maturityDate.setDate(maturityDate.getDate() + parseInt(days, 10));

                const formattedMaturityDate = maturityDate.toISOString().split('T')[0];
                $('#renewMaturityDate').val(formattedMaturityDate);
            }
            $("#renewDate, #renewYears, #renewMonths, #renewDays").on('input', calculateRenewMaturityDate);

            function calculateRenewInterestAmount() {
                var interestType = $('#renewInterestType').val();
                var principal = $('#renewPrincipalAmount').val() || 0;
                var rate = $('#renewInterestRate').val() || 0;

                var interestStartDate = new Date($("#renewInterestStartDate").val());
                var maturityDate = new Date($("#renewMaturityDate").val());
                var time = (maturityDate - interestStartDate) / (1000 * 60 * 60 * 24);

                var interest = 0;
                if (interestType == 'Fixed') {
                    interest = (principal * rate * time) / (365 * 100);
                } else if (interestType == 'AnnualCompounded') {
                    interest = principal * Math.pow(1 + ((rate / 100) / 1), (1 * time / 365)) - principal;
                } else {
                    interest = principal * Math.pow(1 + ((rate / 100) / 4), (4 * time / 365)) - principal;
                }

                principal = parseFloat(principal);
                interest = parseFloat(interest);
                $('#renewInterestAmount').val(Math.round(interest));
                $('#renewMaturityAmount').val(Math.round(principal + interest));
            }
            $("#renewDate, #renewYears, #renewMonths, #renewDays, #renewAmount, #renewInterestRate").on('input',
                calculateRenewInterestAmount);
            $("#renewInterestType").on('change', calculateRenewInterestAmount);

            //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Renew Mature date end

            // -------------------- Calculation Handling Javascript (Ends) -------------------- //


            // -------------------- Form Handling Javascript (Starts) -------------------- //



            $(document).on('submit', '#formData', function(event) {
                event.preventDefault();
                checkDateSessionForStore($('#openingDate'));
                var element = $(this);
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                var form = $('#formData');
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('fdscheme.store') }}',
                    type: 'post',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='text'],input[type='number'],select").removeClass(
                                'is-invalid');
                            displayTable(memberType, accountNo);
                            // displayData(response.fdId);
                            $('#formData')[0].reset();
                            notify(response.message, 'success');
                        } else if (response.status == 'account') {
                            notify(response.message, 'warning');
                        } else {
                            var errors = response.errors;
                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='text'],input[type='number'],select").removeClass(
                                'is-invalid');
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid').siblings('p')
                                    .addClass('invalid-feedback').html(value);
                            });
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            });

            // -------------------- Mature FD (Starts) -------------------- //
            $(document).on('click', '#matureFdButton', function(event) {
                event.preventDefault();
                var matureId = $(this).attr('matureId');
                $.ajax({
                    url: "{{ route('fdscheme.view', '') }}/" +
                        matureId,
                    type: "GET",
                    success: function(response) {
                        if (response['status'] === true) {

                            $('#matureDate').attr('min', reverseFormatDate(response.data
                                .openingDate));
                            $('#openingDatNonEditable').val(reverseFormatDate(response.data
                                .openingDate));

                            $('#matureId').val(matureId);
                            $('#fdDate').html(reverseFormatDate(response.data.openingDate));
                            $('#maturePrincipal').val(response.data.principalAmount);
                            $('#matureAmount').val(response.data.maturityAmount);

                            const todaydate = new Date();
                            $('#matureDate').val(formatDateToDMY(todaydate));
                            // $('#matureDate').val(reverseFormatDate(response.data.maturityDate));

                            $('#matureDateNonEditable').val(response.data.maturityDate);
                            $('#matureInterest').val(response.data.interestAmount);
                            $('#savingaccountList').html(response.savingAccNos);

                            var today = new Date();
                            var maturityDate = new Date(response.data.maturityDate);
                            var isPremature = maturityDate > today;

                            // Handle penalty interest only for premature maturity
                            if (isPremature) {
                                $('#penaltyInterest').val(response.scheme.penaltyInterest);
                                $('#penaltyField').show();
                            } else {
                                $('#penaltyField').hide();
                            }

                            // If there is no saving account disable the transfer type = transfer
                            var transferTypeSelect = $('#transferType');
                            transferTypeSelect.find('option[value="Transfer"]').prop('disabled',
                                !response.transferTypeTransfer);

                            calculateMaturityInterest();
                        }
                    }
                });
                $('#matureModal').modal('show');
            });

            $(document).on('click', '#printFdButton', function() {
                var fdId = $(this).attr('printid');
                var url = "{{ url('report/fdReport/fdPrint/print') }}/" + fdId;
                window.open(url, '_blank');

            })

            $(document).on('submit', '#matureFormData', function(event) {
                event.preventDefault();
                var element = $(this);
                var form = $('#matureFormData');

                // Get the maturity date value
                var maturityDateStr = $('#matureDate').val();
                var maturityDate = new Date(reverseFormatDate(maturityDateStr));

                // Today's date and opening date
                const todayDate = new Date();
                const formattedToday = formatDateToDMY(todayDate); // Format today's date

                const openingDateStr = $('#openingDatNonEditable').val();
                const openingDateParts = openingDateStr.split('-');
                const openingDate = new Date(openingDateParts[2], openingDateParts[1] - 1, openingDateParts[
                    0]); // Parse opening date

                // Session start and end dates from server
                const sessionStart = new Date(@json(session('sessionStart')));
                const sessionEnd = new Date(@json(session('sessionEnd')));

                if (maturityDate < openingDate) {
                    $('#matureDate').val(formattedToday);
                    notify("Maturity date must be on or after the opening date.", 'warning');
                    return;
                }

                if (maturityDate > todayDate) {
                    $('#matureDate').val(formattedToday);
                    notify("Maturity date cannot be later than today’s date.", 'warning');
                    return;
                }

                if (maturityDate < sessionStart) {
                    $('#matureDate').val(formattedToday);
                    notify("Maturity date must be on or after the start of the current session.",
                    'warning');
                    return;
                }

                if (maturityDate > sessionEnd) {
                    $('#matureDate').val(formattedToday);
                    notify("Maturity date cannot be later than the end of the current session.", 'warning');
                    return;
                }


                // Proceed with form submission if validation is successful
                var matureId = $('#matureId').val();
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                $("button[type=submit]").prop('disabled', true);

                $.ajax({
                    url: '{{ route('fdscheme.mature') }}',
                    type: 'put',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] === true) {
                            $('#matureModal').modal('hide');
                            displayTable(memberType, accountNo);
                            displayData(matureId);
                            $('#formData')[0].reset();
                            notify(response.message, 'success');
                        } else {
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            });


            // -------------------- Mature FD (Ends) -------------------- //


            // -------------------- Renew FD (Starts) -------------------- //
            $(document).on('click', '#renewFdButton', function(event) {
                event.preventDefault();
                var renewId = $(this).attr('renewId');
                $.ajax({
                    url: "{{ route('fdscheme.view', '') }}/" +
                        renewId,
                    type: "GET",
                    success: function(response) {
                        if (response['status'] == true) {
                            $('#renewId').val(renewId);
                            $('#fdDateR').html(formatDate(response.data.openingDate));
                            $('#renewDate,#renewInterestStartDate,#renewMaturityDate').val(
                                response.data.maturityDate);
                            $('#renewFdNo').val(response.data.fdNo);
                            $('#renewPrincipalAmount,#renewMaturityAmount').val(response.data
                                .maturityAmount);
                            $('#renewInterestType').val(response.data.interestType);
                        }
                    }
                });
                $('#renewModal').modal('show');
            });

            $(document).on('submit', '#renewFormData', function(event) {
                event.preventDefault();
                var element = $(this);
                var renewId = $('#renewId').val();
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                var form = $('#renewFormData');

                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('fdscheme.renew') }}',

                    type: 'put',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $('#renewModal').modal('hide');
                            displayTable(memberType, accountNo);
                            displayData(renewId);
                            $('#formData')[0].reset();
                            notify(response.message, 'success');
                        } else {
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            });

            // Unmature FD
            $(document).on('click', '#unmatureFdButton', function(event) {
                event.preventDefault();
                var unmatureId = $(this).attr('unmatureId');
                $('#unmatureId').val(unmatureId);
                $('#unmatureModal').modal('show');
            });

            $(document).on('submit', '#unmatureFormData', function(event) {
                event.preventDefault();
                var element = $(this);
                var unmatureId = $('#unmatureId').val();
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                var form = $('#unmatureFormData');

                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('fdscheme.unmature') }}',

                    type: 'put',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $('#unmatureModal').modal('hide');
                            displayTable(memberType, accountNo);
                            displayData(unmatureId);
                            $('#formData')[0].reset();
                            notify(response.message, 'success');
                        } else {
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            });

            // Delete FD
            $(document).on('click', '#deleteFdButton', function(event) {
                event.preventDefault();
                var deleteId = $(this).attr('deleteId');
                $('#deleteId').val(deleteId);
                $('#deleteModal').modal('show');
            });

            $(document).on('submit', '#deleteFormData', function(event) {
                event.preventDefault();
                var element = $(this);
                var deleteId = $('#deleteId').val();
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('fdscheme.delete') }}',
                    type: 'put',
                    data: element.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response['status'] == true) {
                            $('#deleteModal').modal('hide');
                            $('#formData')[0].reset();

                            displayTable(memberType, accountNo);
                            notify(response.message, 'success');
                            location.reload()
                        } else {
                            notify(response.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, "withoutform");
                    }
                });
            });

            // -------------------- Form Handling Javascript (Ends) -------------------- //



            // -------------------- Display Handling Javascript (Starts) -------------------- //

            function getAccountList() {
                var memberType = $('#memberType').val();
                var accountNo = $('#accountNo').val();
                let fdType = $('#fdType').val();
                let scheme_id = $('#scheme_name').val();
                let membershipno = $('#membershipno').val();
// alert(membershipno);
                $.ajax({
                    url: "{{ route('fdscheme.getData') }}",
                    type: "GET",
                    data: {
                        memberType: memberType,
                        accountNo: accountNo,
                        fdType : fdType,
                        scheme_id :scheme_id,
                        membershipno :membershipno
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response['status'] == true) {
                            $("#accountList").html(response.data);
                        }
                    }
                });
            }



            $("#memberType").on('change', getAccountList);
            $("#accountNo").on('keyup', getAccountList);

            $(document).on('click', '#accountList .memberlist', function() {
                var accountNo = $(this).text();
                var memberType = $('#memberType').val();
                $("#accountList").html("");
                $('#accountNo').val(accountNo);

                displayTable(memberType, accountNo);

            });

            function displayTable(memberType, accountNo) {
                let membershipno = $('#membershipno').val();
                $.ajax({
                    url: "{{ route('fdscheme.fetchData') }}",
                    type: "GET",
                    data: {
                        memberType: memberType,
                        accountNo: accountNo,
                        membershipno: membershipno
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response['status'] == true) {

                            $('#memberName').html(response.member.name);
                            {{--  $("#schemetype").val(response.member.schemetype);
                            $("#scheme_name").val(response.member.schemetype);  --}}
                            {{--  $("#fdType").val(response.member.fdtypeid);  --}}
                            //$("#membershipno").val(response.member.membershipno);
                            $("#accountOpeningDateSession").val(reverseFormatDate(response.member.transactionDate));
                            // $("#openingDate").val(reverseFormatDate(response.member.transactionDate));

                            $("#interestRate").val(response.member.interest);
                            $("#years").val(response.member.years);
                            $("#months").val(response.member.months);
                            $("#days").val(response.member.days);

                            $("#interestType").val(response.member.renewInterestType).trigger('change');
                            calculateMaturityDate();
                            var fdRow = response.fd;
                            var tableBody = $('#tableBody');
                            tableBody.empty();
                            var sr = fdRow.length;

                            $.each(fdRow, function(index, fd) {
                                // Define variables outside the condition to avoid re-declaration
                                var mdate, iamount, maturityAmount;

                                // Set values based on the status of the fd
                                if (fd.status == "Matured") {
                                    mdate = formatDate(fd.onmaturityDate);
                                    iamount = fd.actualInterestAmount;
                                    maturityAmount = fd.actualMaturityAmount;
                                } else {
                                    mdate = formatDate(fd.maturityDate);
                                    iamount = fd.interestAmount;
                                    maturityAmount = fd.principalAmount + iamount;
                                }

                                // Create a table row with dynamic content
                                var row = "<tr>" +
                                    "<td>" + (sr--) + "</td>" + // Assuming sr is defined elsewhere
                                    "<td>" + fd.accountNo + "</td>" +
                                    "<td>" + fd.membershipno + "</td>" +
                                    "<td>" + fd.schemeName + "</td>" +
                                    "<td>" + formatDate(fd.openingDate) + "</td>" +
                                    "<td>" + fd.principalAmount + "</td>" +
                                    "<td>" + iamount + "</td>" +
                                    "<td>" + mdate + "</td>" +
                                    "<td>" + Math.floor(maturityAmount) + "</td>" + // Round the maturityAmount if needed
                                    "<td>" + fd.status + "</td>" +
                                    "<td><button type='button' viewId='" + fd.id +
                                    "' class='btn view p-0'><i class='fa-regular fa-eye reportSmallBtnCustom iconsColorCustom'></i></button></td>" +
                                    "</tr>";

                                // Prepend the row to the table body
                                tableBody.prepend(row);

                                // Set the account number in the input field and clear the account list
                                $("#accountNo").val(fd.accountNo);
                                $("#accountList").html(""); // Clear the account list
                            });

                        }
                    }
                });
            }

            $(document).on('click', '.view', function(event) {
                event.preventDefault();
                var viewId = $(this).attr('viewId');
                displayData(viewId);
            });

            function displayData(viewId) {
                $.ajax({
                    url: "{{ route('fdscheme.view', '') }}/" + viewId,
                    type: "GET",
                    success: function(response) {
                        if (response['status'] == true) {

                            if (response.data.autorenew === 'yes') {
                                $('#defaultRadio1').prop('checked', true);
                             } else if (response.data.autorenew === 'no') {
                                $('#defaultRadio2').prop('checked', true);
                            }
                            $('#fdId').val(viewId);
                            $('#memberType').val(response.data.memberType);
                            $('#accountNo').val(response.data.accountNo);
                            $('#fdType').val(response.data.fdType);
                            $('#fdNo').val(response.data.fdNo);
                            $('#openingDate').val(reverseFormatDate(response.data.openingDate));
                            $('#principalAmount').val(response.data.principalAmount);
                            $('#paymentType').val(response.data.paymentType);
                            $('#bank').val(response.data.bank);
                            $('#interestType').val(response.data.interestType);
                            $('#interestStartDate').val(reverseFormatDate(response.data
                                .interestStartDate));
                            $('#interestRate').val(response.data.interestRate);
                            $('#interestAmount').val(response.data.interestAmount);
                            $('#years').val(response.data.years);
                            $('#months').val(response.data.months);
                            $('#days').val(response.data.days);
                            $('#maturityDate').val(reverseFormatDate(response.data.maturityDate));
                            $('#maturityAmount').val(response.data.maturityAmount) || 0;
                            $('#narration').val(response.data.narration);
                            $('#ledgerNo').val(response.data.ledgerNo);
                            $('#pageNo').val(response.data.pageNo);
                            $('#agentId').val(response.data.agentId);
                            $('#status').val(response.data.status);
                            $('#nomineeName1').val(response.data.nomineeName1);
                            $('#nomineeRelation1').val(response.data.nomineeRelation1);
                            $('#nomineeBirthDate1').val(response.data.nomineeBirthDate1);
                            $('#nomineePhone1').val(response.data.nomineePhone1);
                            $('#nomineeAddress1').val(response.data.nomineeAddress1);
                            $('#nomineeName2').val(response.data.nomineeName2);
                            $('#nomineeRelation2').val(response.data.nomineeRelation2);
                            $('#nomineeBirthDate2').val(response.data.nomineeBirthDate2);
                            $('#nomineePhone2').val(response.data.nomineePhone2);
                            $('#nomineeAddress2').val(response.data.nomineeAddress2);

                            $('.dynamic-buttons').addClass('d-none');
                            $('#newFdButton').removeClass('d-none');
                            $('#printFdButton').removeClass('d-none').attr('printId', response.data
                                .id);
                            if (response.data.status == 'Active') {
                                $('#matureFdButton').removeClass('d-none').attr('matureId', response
                                    .data.id);
                                $('#renewFdButton').removeClass('d-none').attr('renewId', response.data
                                    .id);
                                if (response.data.transferedFrom !== 'trfdSavingtoFD') {
                                    $('#deleteFdButton').removeClass('d-none').attr('deleteId', response
                                        .data.id);
                                }
                            } else {
                                $('#unmatureFdButton').removeClass('d-none').attr('unmatureId', response
                                    .data.id);
                            }
                        }
                    }
                });
            }
            // -------------------- Display Handling Javascript (Ends) -------------------- //
        });

        function newTab(url) {
            var form = document.createElement("form");
            form.method = "GET";
            form.action = url;
            form.target = "_blank";
            document.body.appendChild(form);
            form.submit();
        }

        // toggle saving account no on mature fd transfer type
        $(document).ready(function() {
            toggleSavingAccount();
            // Listen for changes in the transfer type dropdown
            $('#transferType').change(function() {
                toggleSavingAccount();
            });

            function toggleSavingAccount() {
                var transferType = $('#transferType').val();

                if (transferType === 'Transfer') {
                    // Show the saving account field and make it editable
                    $('.saving_acc_column').show();
                    $('#savingAccNo').prop('readonly', false); // Enable the input
                } else {
                    // Hide the saving account field and clear its value
                    $('.saving_acc_column').hide();
                    $('#savingAccNo').val(''); // Clear the value
                    $('#savingAccNo').prop('readonly', true); // Make it readonly again
                }
            }

            $(document).on('click', '.savingmemberlist', function() {
                var accountNo = $(this).text();
                $('#savingAccNo').val(accountNo);
                $('#savingaccountList').empty();
            });





        });

        function formatDateToDMY(date) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = ("0" + (date.getMonth() + 1)).slice(-2); // Months are 0-based, so add 1
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function checkDateSessionForStore(input) {
            const todayDate = new Date();
            const formattedToday = formatDateToDMY(todayDate); // Format today's date

            const openingDateStr = $('#accountOpeningDateSession').val();
            const openingDateParts = openingDateStr.split('-');
            const openingDate = new Date(openingDateParts[2], openingDateParts[1] - 1, openingDateParts[0]);

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
            console.log('accOpeningDate - openingDate ', accOpeningDate + '-------------' + openingDate);

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
        // Function to validate maturity date
        function checkDateSession(input) {
            const todayDate = new Date();
            const formattedToday = formatDateToDMY(todayDate); // Format today's date

            const openingDateStr = $('#openingDatNonEditable').val();
            const openingDateParts = openingDateStr.split('-');
            const openingDate = new Date(openingDateParts[2], openingDateParts[1] - 1, openingDateParts[0]);

            const sessionStart = new Date(@json(session('sessionStart')));
            const sessionEnd = new Date(@json(session('sessionEnd')));
            var maturityDateStr = $(input).val();

            var dateRegex = /^\d{2}-\d{2}-\d{4}$/;
            if (!dateRegex.test(maturityDateStr)) {
                notify("Invalid date format. Please use DD-MM-YYYY format.", 'warning');
                $(input).val(formattedToday); // Clear the input if the format is invalid
                return;
            }

            var maturityDate = new Date(reverseFormatDate(maturityDateStr));

            if (maturityDate < openingDate) {
                $(input).val(formattedToday);
                notify("Maturity date cannot be before the opening date.", 'warning');
                return;
            }

            if (maturityDate > todayDate) {
                $(input).val(formattedToday);
                notify("Maturity date cannot be in the future.", 'warning');
                return;
            }

            if (maturityDate < sessionStart) {
                $(input).val(formattedToday);
                notify("Maturity date must be within the current session start date.", 'warning');
                return;
            }

            if (maturityDate > sessionEnd) {
                $(input).val(formattedToday);
                notify("Maturity date cannot exceed the session end date.", 'warning');
                return;
            }

        }


        if (document.readyState == "complete") {
            $(".transactionDate").val({{ session('currentdate') }});
        }
    </script>
@endpush
