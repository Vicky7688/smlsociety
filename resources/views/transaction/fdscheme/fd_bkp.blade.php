@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-6 fdHeading">
                    <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Fixed Deposit (Scheme)</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="py-2 fdHeadingName"><span class="text-muted fw-light">Name: </span><span id="memberName"></span></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards"> 
            <div class="card">
                <div class="card-body cardsY">
                    <form action="javascript:void(0)" id="formData" name="formData" autocomplete="off">
                        <div class="nav-align-top rdCustom">
                            <ul class="nav nav-tabs fdCustomUL" role="tablist"> <!-- removed "nav-pills" & "nav-fill" -->
                                <li class="col-md-3 nav-item m-3 mt-0" role="presentation">
                                    <a class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#fdDetails" aria-controls="fdDetails" aria-selected="false" tabindex="-1"> FD Details
                                    </a>
                                </li>
                                <li class="col-md-3 nav-item m-3 mt-0" role="presentation">
                                    <a class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nomineeDetails" aria-controls="nomineeDetails" aria-selected="true">
                                        Nominee Details
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content tableContent fdTabContent mt-2">
                                <div class="tab-pane fade active show" id="fdDetails" role="tabpanel">
                                    @csrf
                                    <input type="hidden" id="fdId" name="fdId" value="new">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label class="form-label mb-1" for="memberType">Member Type</label>
                                            <select name="memberType" id="memberType" class="select21 form-select form-select-sm" data-placeholder="Active">
                                                <option value="Member">Member</option>
                                                <option value="NonMember">Non Member</option>
                                                <option value="Staff">Staff</option>
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 saving_column">
                                            <label for="accountNo" class="form-label">Account No</label>
                                            <input type="text" id="accountNo" name="accountNo" class="form-control form-control-sm " placeholder="Account No" />
                                             <div id="accountList" class="accountList"></div>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                            <label class="form-label mb-1" for="fdType">FD Type</label>
                                            <select name="fdType" id="fdType" class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                                @foreach ($FdTypeMaster as $item)
                                                    <option value="{{ $item->id }}">{{ $item->type }}</option> 
                                                 @endforeach 
                                            </select>
                                            <p class="error"></p>
                                        </div> 
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                            <label for="schemetype" class="form-label">Scheme Type</label>
                                            <select class="form-select formInputsSelect" id="schemetype" name="schemetype">
                                                @foreach ($FDSchemes as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option> 
                                             @endforeach 
                                            </select> 
                                            <small class="text-danger error-schemetype"></small> 
                                        </div>  
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="membershipno" class="form-label">Membership No</label>
                                            <input type="text" id="membershipno" name="membershipno" class="form-control form-control-sm" placeholder="FD No" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="openingDate" class="form-label">Opening Date</label>
                                            <input type="date" id="openingDate" name="openingDate" class="form-control form-control-sm" placeholder="Opening Date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="principalAmount" class="form-label">Amount</label>
                                            <input type="text" step="any" min="1" id="principalAmount" name="principalAmount" class="form-control form-control-sm " placeholder="0.00" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="paymentType" class="form-label">Payment Type</label>
                                            <select class="form-select form-select-sm Select" id="paymentType" name="paymentType">
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
                                                @if (!empty($ledgers))
                                                @foreach ($ledgers as $ledger)
                                                <option value="{{$ledger->ledgerCode}}">{{$ledger->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label class="form-label mb-1" for="interestType">Interest Type</label>
                                            <select name="interestType" id="interestType" class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                                <option value="Fixed">Fixed</option>
                                                <option value="AnnualCompounded">Annual Compounded</option>
                                                <option value="QuarterlyCompounded">Quarterly Compounded</option>
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="interestStartDate" class="form-label">Interest Runs Date</label>
                                            <input type="date" id="interestStartDate" name="interestStartDate" class="form-control form-control-sm" placeholder="Interest Start Date" value="{{ now()->format('Y-m-d') }}" readonly />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="interestRate" class="form-label">Interest Rate</label>
                                            <input type="text" step="any" min="1" id="interestRate" name="interestRate" class="form-control form-control-sm" placeholder="0.00" />
                                            <p class="error"></p>
                                        </div>
                                        <input type="hidden" step="any" min="0" id="interestAmount" name="interestAmount" class="form-control form-control-sm" placeholder="Interest Paid" />
                                        <!-- <div class="col-md-3 col-sm-12 mb-3">
                                            <label for="interestAmount" class="form-label">Interest Amount</label>
                                            <input type="text" step="any" min="0" id="interestAmount" name="interestAmount"
                                                class="form-control form-control-sm" placeholder="Interest Paid" readonly />
                                            <p class="error"></p>
                                        </div> -->
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="years" class="form-label">Years</label>
                                            <input type="text" id="years" min="0" name="years" class="form-control form-control-sm" placeholder="Years" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="months" class="form-label">Months</label>
                                            <input type="text" id="months" min="0" name="months" value="0" class="form-control form-control-sm" placeholder="Months" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="days" class="form-label">Days</label>
                                            <input type="text" id="days" min="0" name="days" value="0" class="form-control form-control-sm" placeholder="Days" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="maturityDate" class="form-label">Maturity Date</label>
                                            <input type="date" id="maturityDate" name="maturityDate" class="form-control form-control-sm" placeholder="Maturity Date" value="{{ now()->format('Y-m-d') }}" readonly />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="maturityAmount" class="form-label">Maturity Amount</label>
                                            <input type="text" step="any" min="1" id="maturityAmount" name="maturityAmount" class="form-control form-control-sm" placeholder="Maturity Amount" readonly />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="ledgerNo" class="form-label">Ledger No</label>
                                            <input type="text" id="ledgerNo" name="ledgerNo" class="form-control form-control-sm" placeholder="Ledger No" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="pageNo" class="form-label">Page No</label>
                                            <input type="text" id="pageNo" name="pageNo" class="form-control form-control-sm" placeholder="Page No" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label for="agentId" class="form-label">Agent</label>
                                            <select class="form-select form-select-sm Select" id="agentId" name="agentId">
                                                @if(!empty($agents))
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                                            <label class="form-label mb-1" for="memberType">Status</label>
                                            <select name="status" id="memberType" class="select21 form-select form-select-sm Select" data-placeholder="Status">
                                                <option value="Active">Active</option>
                                                <option value="Matured">Matured</option>
                                                <option value="Renewed">Renewed</option>
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-12     ">
                                            <label for="narration" class="form-label">Narration</label>
                                            <input type="text" id="narration" name="narration" class="form-control form-control-sm" placeholder="Narration" />
                                            <p class="error"></p>
                                        </div>

                                        <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                            <div class="d-flex h-100 justify-content-end text-end">

                                                <div class="modal-footer pt-3 fdCustomButtons">
                                                    <a href="{{ route('fdscheme.index') }}" type="button" id="newFdButton" class=" btn btn-secondary waves-effect waves-light d-none dynamic-buttons">New
                                                        FD</a>
                                                    <button type="button" id="matureFdButton" matureId="" class="btn btn-success waves-effect waves-light d-none dynamic-buttons">Mature
                                                        FD</button>
                                                    <button type="button" id="renewFdButton" renewId="" class="btn btn-success waves-effect waves-light d-none dynamic-buttons">Renew
                                                        FD</button>
                                                    <button type="button" id="unmatureFdButton" unmatureId="" class="btn btn-danger waves-effect waves-light d-none dynamic-buttons">UnMature
                                                        FD</button>
                                                    <button type="button" id="printFdButton" printId="" class="btn btn-info waves-effect waves-light d-none dynamic-buttons">Print
                                                        FD</button>
                                                    <button type="button" id="deleteFdButton" deleteId="" class="btn btn-warning waves-effect waves-light d-none dynamic-buttons">Delete
                                                        FD</button>
                                                    <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light">Save</button>
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
                                            <input type="text" id="nomineeName1" name="nomineeName1" class="form-control form-control-sm" placeholder="Name" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="nomineeRelation1" class="form-label">Relation</label>
                                            <input type="text" id="nomineeRelation1" name="nomineeRelation1" class="form-control form-control-sm" placeholder="Relation" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="birthDate1" class="form-label">Date Of Birth</label>
                                            <input type="text" id="birthDate1" name="birthDate1" class="form-control form-control-sm mydatepic" placeholder="dd-mm-yyyy" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="nomineePhone1" class="form-label">Contact No.</label>
                                            <input type="text" id="nomineePhone1" name="nomineePhone1" class="form-control form-control-sm" placeholder="Contact No." />
                                            <p class="error"></p>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label class="form-label" for="share">Share</label>
                                            <input type="text" id="nominee_share" name="nominee_share" class="form-control form-control-sm" placeholder="Share">
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-6 ">
                                            <label for="nomineeAddress1" class="form-label">Address</label>
                                            <input type="text" id="nomineeAddress1" name="nomineeAddress1" class="form-control form-control-sm" placeholder="Relation" />
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <small> Nominee 2</small>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="nomineeName2" class="form-label">Nominee Name</label>
                                            <input type="text" id="nomineeName2" name="nomineeName2" class="form-control form-control-sm" placeholder="Name" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="nomineeRelation1" class="form-label">Relation</label>
                                            <input type="text" id="nomineeRelation2" name="nomineeRelation2" class="form-control form-control-sm mydatepic" placeholder="Relation" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="birthDate2" class="form-label">Date Of Birth</label>
                                            <input type="text" id="birthDate2" name="birthDate2" class="form-control form-control-sm mydatepic" placeholder="dd-yy-yyyy" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label for="nomineePhone2" class="form-label">Contact No.</label>
                                            <input type="text" id="nomineePhone2" name="nomineePhone2" class="form-control form-control-sm" placeholder="Contact No." />
                                            <p class="error"></p>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-4 col-6     ">
                                            <label class="form-label" for="share">Share</label>
                                            <input type="text" id="nominee_share" name="nominee_share" class="form-control form-control-sm" placeholder="Share">
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-6     ">
                                            <label for="nomineeAddress2" class="form-label">Address</label>
                                            <input type="text" id="nomineeAddress2" name="nomineeAddress2" class="form-control form-control-sm" placeholder="Relation" />
                                            <p class="error"></p>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                                            <div class="d-flex h-100 justify-content-end text-end">
                                                <div class="modal-footer pt-3 fdCustomButtons">
                                                    <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light">Save</button>
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
                            <th scope="col">FD No</th>
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
                <h5 class="modal-title"><span class="text-muted fw-light">FD Date: </span><span id="fdDate"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="matureFormData" name="matureFormData">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="matureId" name="matureId">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                            <label for="matureDate" class="form-label">Maturity Date</label>
                            <input type="date" class="form-control form-control-sm" placeholder="YYYY-MM-DD" id="matureDate" name="matureDate" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" />
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
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                            <label for="maturePrincipal" class="form-label">Amount</label>
                            <input type="text" step="any" min="1" class="form-control form-control-sm" id="maturePrincipal" name="maturePrincipal" readonly />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                            <label for="matureInterest" class="form-label">Payable Interest</label>
                            <input type="text" step="any" min="0" class="form-control form-control-sm" id="matureInterest" name="matureInterest" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6     ">
                            <label for="matureAmount" class="form-label">Maturity Amount</label>
                            <input type="text" step="any" min="1" class="form-control form-control-sm" id="matureAmount" name="matureAmount" readonly />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-12     ">
                            <label for="matureNarration" class="form-label">Narration</label>
                            <input type="text" class="form-control form-control-sm" id="matureNarration" name="matureNarration" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                            <div class="d-flex h-100 justify-content-end text-end">
                                <div class="modal-footer pt-3 fdCustomButtons">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <h5 class="modal-title"><span class="text-muted fw-light">FD Date: </span><span id="fdDateR"></span>
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
                            <input type="date" id="renewDate" name="renewDate" class="form-control form-control-sm" placeholder="Renew Date" max="{{ now()->format('Y-m-d') }}" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewFdNo" class="form-label">FD No</label>
                            <input type="text" id="renewFdNo" name="renewFdNo" class="form-control form-control-sm" placeholder="FD No" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewPrincipalAmount" class="form-label">Renew Amount</label>
                            <input type="text" step="any" min="1" id="renewPrincipalAmount" name="renewPrincipalAmount" class="form-control form-control-sm" placeholder="Renew Amount" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label class="form-label mb-1" for="renewInterestType">Interest Type</label>
                            <select name="renewInterestType" id="renewInterestType" class="select21 form-select form-select-sm Select" data-placeholder="Active">
                                <option value="Fixed">Fixed</option>
                                <option value="AnnualCompounded">Annual Compounded</option>
                                <option value="QuarterlyCompounded">Quarterly Compounded</option>
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewInterestStartDate" class="form-label">Interest Start Date</label>
                            <input type="date" id="renewInterestStartDate" name="renewInterestStartDate" class="form-control form-control-sm" placeholder="Interest Start Date" readonly />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewInterestRate" class="form-label">Interest Rate</label>
                            <input type="text" step="any" min="1" id="renewInterestRate" name="renewInterestRate" class="form-control form-control-sm" placeholder="0.00" />
                            <p class="error"></p>
                        </div>
                      
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewYears" class="form-label">Years</label>
                            <input type="text" min="0" id="renewYears" name="renewYears" class="form-control form-control-sm" placeholder="Years" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewMonths" class="form-label">Months</label>
                            <input type="text" min="0" id="renewMonths" name="renewMonths" class="form-control form-control-sm" placeholder="Months" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewDays" class="form-label">Days</label>
                            <input type="text" min="0" id="renewDays" name="renewDays" class="form-control form-control-sm" placeholder="Days" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewMaturityDate" class="form-label">Maturity Date</label>
                            <input type="date" id="renewMaturityDate" name="renewMaturityDate" class="form-control form-control-sm" placeholder="Maturity Date" value="{{ now()->format('Y-m-d') }}" readonly />
                            <p class="error"></p>
                        </div>
                          <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewInterestAmount" class="form-label">Interest Amount</label>
                            <input type="text" step="any" min="0" id="renewInterestAmount" name="renewInterestAmount" class="form-control form-control-sm" placeholder="Interest Amount" readonly />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6     ">
                            <label for="renewMaturityAmount" class="form-label">Maturity Amount</label>
                            <input type="text" step="any" min="1" id="renewMaturityAmount" name="renewMaturityAmount" class="form-control form-control-sm" placeholder="Maturity Amount" readonly />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12     ">
                            <label for="renewNarration" class="form-label">Narration</label>
                            <input type="text" id="renewNarration" name="renewNarration" class="form-control form-control-sm" placeholder="Narration" />
                            <p class="error"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column   savingColumnButton">
                            <div class="d-flex h-100 justify-content-end text-end">
                                <div class="modal-footer pt-3 fdCustomButtons">
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
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
<script>
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
                    console.log("Something went wrong");
                }
            });
        });

        $("#openingDate").on('input', function() {
            var openingDate = $(this).val();
            $('#interestStartDate').val(openingDate);
        });

        function calculateMaturityDate() {
            var openingDate = $("#openingDate").val();
            var years = $('#years').val() || 0;
            var months = $('#months').val() || 0;
            var days = $('#days').val() || 0;

            const maturityDate = new Date(openingDate);
            maturityDate.setFullYear(maturityDate.getFullYear() + parseInt(years, 10));
            maturityDate.setMonth(maturityDate.getMonth() + parseInt(months, 10));
            maturityDate.setDate(maturityDate.getDate() + parseInt(days, 10));

            const formattedMaturityDate = maturityDate.toISOString().split('T')[0];
            $('#maturityDate').val(formattedMaturityDate);
        }
        $("#openingDate, #years, #months, #days").on('input', calculateMaturityDate);

        function calculateInterestAmount() {
            var interestType = $('#interestType').val();
            var principal = $('#principalAmount').val() || 0;
            var rate = $('#interestRate').val() || 0;

            var interestStartDate = new Date($("#interestStartDate").val());
            var maturityDate = new Date($("#maturityDate").val());
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
            $('#interestAmount').val(Math.round(interest));
            $('#maturityAmount').val(Math.round(principal + interest));
        }
        $("#openingDate, #years, #months, #days, #principalAmount, #interestRate").on('input', calculateInterestAmount);
        $("#interestType").on('change', calculateInterestAmount);

        function calculateMaturityInterest() {
            var interestType = $('#interestType').val();
            var principal = $('#maturePrincipal').val() || 0;
            var rate = $('#interestRate').val() || 0;

            var interestStartDate = new Date($("#interestStartDate").val());
            var maturityDate = new Date($("#matureDate").val());
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
            $('#matureInterest').val(Math.round(interest));
            calculateMatureAmount();
        }
        $("#matureDate").on('input', calculateMaturityInterest);

        function calculateMatureAmount() {
            var principal = parseFloat($('#maturePrincipal').val()) || 0;
            var interest = parseFloat($('#matureInterest').val()) || 0;
            $('#matureAmount').val(Math.round(principal + interest));
        }
        $("#matureInterest").on('input', calculateMatureAmount);

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
        $("#renewDate, #renewYears, #renewMonths, #renewDays, #renewAmount, #renewInterestRate").on('input', calculateRenewInterestAmount);
        $("#renewInterestType").on('change', calculateRenewInterestAmount);

        // -------------------- Calculation Handling Javascript (Ends) -------------------- //


        // -------------------- Form Handling Javascript (Starts) -------------------- //

        $(document).on('submit', '#formData', function(event) {
            event.preventDefault();
            var element = $(this);
            var memberType = $('#memberType').val();
            var accountNo = $('#accountNo').val();
             var form = $('#formData');
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("fd.store") }}',

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
                    }else if(response.status == 'account'){
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
                url: "{{ route('fdscheme.view','') }}/" +
                    matureId,
                type: "GET",
                success: function(response) {
                    if (response['status'] == true) {
                        $('#matureId').val(matureId);
                        $('#fdDate').html(formatDate(response.data.openingDate));
                        // $('#matureDate').val(response.data.maturityDate);
                        $('#maturePrincipal').val(response.data.principalAmount);
                        // $('#matureInterest').val(response.data.interestAmount);
                        $('#matureAmount').val(response.data.maturityAmount);
                        calculateMaturityInterest();
                    }
                }
            });
            $('#matureModal').modal('show');
        });
        
          $(document).on('click','#printFdButton',function(){
            var fdId = $(this).attr('printid');
            var url = "{{ url('report/fdReport/fdPrint/print')}}/"+fdId;
            window.open(url, '_blank');
        
            console.log(url) ;
        })

        $(document).on('submit', '#matureFormData', function(event) {
            event.preventDefault();
            var element = $(this);
            var form = $('#matureFormData');
            var matureId = $('#matureId').val();
            var memberType = $('#memberType').val();
            var accountNo = $('#accountNo').val();
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route("fd.mature") }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
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
                url: "{{ route('fdscheme.view','') }}/" +
                    renewId,
                type: "GET",
                success: function(response) {
                    if (response['status'] == true) {
                        $('#renewId').val(renewId);
                        $('#fdDateR').html(formatDate(response.data.openingDate));
                        $('#renewDate,#renewInterestStartDate,#renewMaturityDate').val(response.data.maturityDate);
                        $('#renewFdNo').val(response.data.fdNo + 'R');
                        $('#renewPrincipalAmount,#renewMaturityAmount').val(response.data.maturityAmount);
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
                url: '{{ route("fd.renew") }}',

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
                url: '{{ route("fd.unmature") }}',

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
                url: '{{ route("fd.delete") }}',
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
            $.ajax({
                url: "{{ route('fdscheme.getData') }}",
                type: "GET",
                data: {
                    memberType: memberType,
                    accountNo: accountNo
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
            $.ajax({
                url: "{{ route('fdscheme.fetchData') }}",
                type: "GET",
                data: {
                    memberType: memberType,
                    accountNo: accountNo
                },
                dataType: 'json',
                success: function(response) {
                    console.table(response);
                    if (response['status'] == true) {  
                        $('#memberName').html(response.member.name);  
                        $("#schemetype").val(response.member.schemetype);
                        $("#fdType").val(response.member.fdtypeid);
                        $("#membershipno").val(response.member.membershipno);
                        $("#openingDate").val(response.member.transactionDate);
                        
                        
                        var fdRow = response.fd;
                        var tableBody = $('#tableBody');
                        tableBody.empty();
                        var sr = fdRow.length;
                        $.each(fdRow, function(index, fd) {
                            if (fd.status == "Matured") {
                                var mdate = formatDate(fd.onmaturityDate);
                                var iamount = fd.actualInterestAmount;
                            } else {
                                var mdate = formatDate(fd.maturityDate);
                                var iamount = fd.interestAmount;
                            }
                            var row = "<tr>" +
                                "<td>" + (sr--) + "</td>" +
                                "<td>" + fd.accountNo + "</td>" +
                                "<td>" + fd.fdNo + "</td>" +
                                "<td>" + formatDate(fd.openingDate) + "</td>" +
                                "<td>" + fd.principalAmount + "</td>" +
                                    "<td>" + iamount + "</td>" +
                                "<td>" + mdate + "</td>" +
                                "<td>" + parseInt(parseInt(fd.principalAmount) + parseInt(iamount))  + "</td>" +
                                "<td>" + fd.status + "</td>" +
                                "<td><button type='button' viewId='" + fd.id +
                                "' class='btn view p-0'><i class='fa-regular fa-eye reportSmallBtnCustom iconsColorCustom'></i></button></td>" +
                                "</tr>";
                            tableBody.prepend(row);
                            $("#accountNo").val(accountNo);
                            $("#accountList").html("");
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
                url: "{{ route('fdscheme.view','') }}/" + viewId,
                type: "GET",
                success: function(response) {
                    if (response['status'] == true) {
                        $('#fdId').val(viewId);
                        $('#memberType').val(response.data.memberType);
                        $('#accountNo').val(response.data.accountNo);
                        $('#fdType').val(response.data.fdType);
                        $('#fdNo').val(response.data.fdNo);
                        $('#openingDate').val(response.data.openingDate);
                        $('#principalAmount').val(response.data.principalAmount);
                        $('#paymentType').val(response.data.paymentType);
                        $('#bank').val(response.data.bank);
                        $('#interestType').val(response.data.interestType);
                        $('#interestStartDate').val(response.data.interestStartDate);
                        $('#interestRate').val(response.data.interestRate);
                        $('#interestAmount').val(response.data.interestAmount);
                        $('#years').val(response.data.years);
                        $('#months').val(response.data.months);
                        $('#days').val(response.data.days);
                        $('#maturityDate').val(response.data.maturityDate);
                        $('#maturityAmount').val(response.data.maturityAmount);
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
                            $('#renewFdButton').removeClass('d-none').attr('renewId', response
                                .data.id);
                            $('#deleteFdButton').removeClass('d-none').attr('deleteId', response
                                .data.id);
                        } else {
                            $('#unmatureFdButton').removeClass('d-none').attr('unmatureId',
                                response.data.id);
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
</script>
@endpush