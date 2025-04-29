@extends('layouts.app')
@section('title', ' Loan Advancement')
@section('pagetitle', 'Loan Advancement')

@php
    $table = 'no';
@endphp
@section('content')
    <style>
        #schemesContainer {
            border: 1px solid #f2e3e3;
            padding: 4px;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12 ">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Dashboard / Loan /</span> Advancement
                </h4>

                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-home" aria-controls="navs-pills-justified-home"
                                aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-1 ti-md me-1"></i> Loan Details
                                <!-- <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">3</span> -->
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-Guarantor"
                                aria-controls="navs-pills-justified-Guarantor" aria-selected="true">
                                <i class="tf-icons ti ti-circle-number-2 ti-md me-1"></i> Guarantor Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-loanNotice"
                                aria-controls="navs-pills-justified-loanNotice" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-3 ti-md me-1"></i> Notice
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-loanDocs"
                                aria-controls="navs-pills-justified-loanDocs" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-4 ti-md me-1"></i> Loan Documents
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-mclDetails"
                                aria-controls="navs-pills-justified-mclDetails" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-5 ti-md me-1"></i> MCL Details
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="navs-pills-justified-home" role="tabpanel">
                            <form id="loanmember" action="{{ route('loanupdate') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" id="fdamountupto" name="fdamountupto">
                                <input type="hidden" id="rdamountupto" name="rdamountupto">
                                <input type="hidden" id="dailyamountupto" name="dailyamountupto">
                                <div class="row">
                                    <input type="hidden" name="actiontype" value="transactionloan" />
                                    <input type="hidden" name="id" value="new" />
                                    <div class="col-md-3 col-sm-12 mb-3">
                                        <label for="txndate" class="form-label">Loan Date</label>
                                        <input id="transactionDate" type="text" name="loanDate"
                                            class="form-control form-control-sm mydatepic" placeholder="DD-MM-YYYY"
                                            required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Member </label>
                                        <select name="memberType" id="status-org" class="form-select form-select-sm"
                                            onchange="getmemberLoanType(this)">
                                            <option value="Member">Member</option>
                                            <option value="NonMember">Nominal Member</option>
                                            <option value="Staff">Staff</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Membership No</label>
                                        <input type="text" id="accountNumber" name="accountNumber"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                        <div id="accountdetails" class="form-text text-success"> </div>
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Loan AC No</label>
                                        <input type="text" id="loanaccount" name="loanAcNo"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                    </div>
                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Purpose </label>
                                        <select name="purpose" id="" class="select form-select form-select-sm"
                                            data-placeholder="Active">
                                            <option value="">Select Purpose</option>
                                            @foreach ($purposes as $purpose)
                                                <option value="{{ $purpose->name }}">{{ $purpose->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col col-md-3 col-sm-12 ecommerce-select-dropdown">
                                        <label class="form-label mb-1" for="status-org">Loan Name </label>
                                        <select name="loanType" id="loanType" class="select form-select form-select-sm"
                                                onchange="getLoanType(this)">
                                            <option value="" disabled selected>Loan Type</option>
                                            @foreach ($loantypes as $loantype)
                                                <option data-loantype="{{ $loantype->loantypess }}" value="{{ $loantype->id }}">
                                                    {{ $loantype->name }}
                                                </option>
                                            @endforeach
                                            {{-- @foreach ($grup as $grusp)
                                                <option value="{{ $grusp->id }}">{{ $grusp->headName }}</option>
                                            @endforeach
                                            @foreach ($grupo as $gruspo)
                                                <option value="{{ $gruspo->id }}">{{ $gruspo->headName }}</option>
                                            @endforeach --}}

                                        </select>
                                    </div>
                                    <div class="extrafiled col-md-3 mb-3" stlye="" style="display: none;">
                                        <div class="">
                                            <label id="extraf" for="selectSuccess" class="form-label">Success</label>
                                            <div class="select-success">
                                                <select name="fd_ids[]" id=""
                                                    class="select form-select form-select-sm" multiple>
                                                    <option value="">Select FD</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <input hidden type="text" id="loaninterestRatess" value="0">


                                    <div class="col-md-6 mb-3 col-sm-6" id="schemesContainermaster" style="display:none">
                                        <label for="txndate" class="form-label">Select Deposit A/c</label>
                                        <div id="schemesContainer">

                                            <div class="form-check form-check-inline mt-4">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="check"
                                                    value="option1"  onchange="getLoanType(this)">
                                                <label class="form-check-label" for="inlineCheckbox1">1</label>
                                            </div>



                                        </div>


                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Amount</label>
                                        <input type="text" name="amount" id="loanamount" class="form-control form-control-sm"
                                            placeholder="Enter value" required oninput="checkfdshceme(this)" />
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Pernote No</label>
                                        <input type="text" name="pernote" class="form-control form-control-sm"
                                            placeholder="Enter value" required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Installment Type</label>
                                        <input type="text" name="installmentType" value="Monthly"
                                            class="form-control form-control-sm" placeholder="Enter value" readonly />
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Processing Rate</label>
                                        <input type="text" name="processingRates" class="form-control form-control-sm"
                                            placeholder="Enter value" required readonly />
                                    </div>

                                    <!-- <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Fee</label>
                                        <input type="text" name="fee" class="form-control form-control-sm"
                                            placeholder="Enter value" required />
                                    </div> -->
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Year</label>
                                        <input type="text" name="loanYear" readonly
                                            class="form-control form-control-sm" placeholder="Enter value" />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Month</label>
                                        <input type="text" name="loanMonth" class="form-control form-control-sm"
                                            value=0 placeholder="Enter value" readonly />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Intrest</label>
                                        <input type="text" name="loanInterest" class="form-control form-control-sm" id="loanInterest"
                                            placeholder="Enter value" required readonly />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Penal Intrest</label>
                                        <input type="text" name="defintr" class="form-control form-control-sm"
                                            placeholder="Enter value" required />
                                    </div>
                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Bank Deduction </label>
                                        <select name="bankDeduction" id="status-org" class="form-select form-select-sm"
                                            onchange="bankdeduction(this)">
                                            <option selected value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12" id="deductionamt" style="display: none;">
                                        <label for="txndate" class="form-label">Deduction Amount</label>
                                        <input type="text" name="deduction" class="form-control form-control-sm"
                                            placeholder="Enter value" value="0" required />
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="" class="form-label">Loan App Fees</label>
                                        <input type="text" name="loan_app_fee" id="loan_app_fee" class="form-control form-control-sm"
                                            placeholder="Enter value" value="0" required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">BY </label>
                                        <select name="loanBy" id="loanBy" class="form-select form-select-sm"
                                            onchange="loanby(this)">
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Bank</option>
                                            <option value="Saving">Saving</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 bank" style="display: none;">
                                        <label for="txndate" class="form-label">Select Bank</label>
                                        <select name="ledgerId" id="status-org" class="form-select form-select-sm"
                                            data-placeholder="Active">
                                            <option value="">Select</option>
                                            @foreach ($banktypes as $banktype)
                                                <option value="{{ $banktype->id }}">{{ $banktype->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 savingaccountdiv" style="display: none;">
                                        <label for="txndate" class="form-label">Saving A/c</label>
                                        <input type="text" name="savingaccounts" id="savingaccounts" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 bank" style="display: none;">
                                        <label for="chequeNo" class="form-label">Cheque No Bank</label>
                                        <input id="chequeNo" type="text" name="chequeNo"
                                            class="form-control form-control-sm" placeholder="Cheque No" />
                                    </div>
                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Agent </label>
                                        <select name="agentId" id="status-org" class="form-select form-select-sm"
                                            data-placeholder="Active">
                                            <option value="">Select Agent</option>
                                            @foreach ($agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 accountdetails">
                                        <label for="txndate" class="form-label">Name</label>
                                        <input id="name" type="text" name="name"
                                            class="form-control form-control-sm" placeholder="Account Holder" disabled />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 accountdetails">
                                        <label for="share" class="form-label">Share Amount</label>
                                        <input id="balance" type="text" name="balance"
                                            class="form-control form-control-sm" placeholder="Balance" disabled />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 accountdetails">
                                        <label for="address" class="form-label">Address</label>
                                        <input id="member-address" type="text" name="address"
                                            class="form-control form-control-sm" placeholder="Address" disabled />
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button id="submitButton" class="btn btn-primary waves-effect waves-light mr-1"
                                        type="submit"
                                        data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                   Loading...">Submit</button>
                                    &nbsp;&nbsp;

                                    <button id="installment" onclick="getinstallments()"
                                        class="btn btn-primary waves-effect waves-light" type="button"
                                        data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                   Loading...">View
                                        Installments</button>
                                    <button type="reset" class="btn btn-label-danger waves-effect"
                                        onclick="resetforms()" aria-label="Close"> Clear </button>


                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade " id="navs-pills-justified-Guarantor" role="tabpanel">
                            <form id="guarantorform" action="{{ route('loanupdate') }}" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="actiontype" value="guarantorupdate" />
                                    <input type="hidden" name="loanid" value="" />
                                    <div class="col-md-2 col-sm-12 mb-3">
                                        <label for="txndate" class="form-label">Guarantor Ac</label>
                                        <input id="gaurantor1" type="text" name="guranter1"
                                            class="form-control form-control-sm gaurantor" placeholder="Ac no" required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Guarantor Name </label>
                                        <input id="gaurantor1name" type=" text" name="gaurantor1name"
                                            class="form-control form-control-sm gaurantor" placeholder="Guarantor Name"
                                            required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Address</label>
                                        <input id="gaurantor1add" type="text" name="gaurantor1add"
                                            class="form-control form-control-sm" placeholder="Address" />
                                    </div>


                                    <div class="mb-3 col-md-3 col-sm-12 mt-4">
                                        <button type="button" class="btn btn-info waves-effect waves-light btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#guarantorlist1">
                                            View
                                        </button>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-sm-12 mb-3">
                                        <label for="txndate" class="form-label">Guarantor Ac</label>
                                        <input id="gaurantor2" type="text" name="guranter2"
                                            class="form-control form-control-sm gaurantor2" placeholder="Ac no" />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Guarantor Name </label>
                                        <input id="gaurantor2name" type="text" name="gaurantor2name"
                                            class="form-control form-control-sm gaurantor" placeholder="Guarantor Name" />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Address</label>
                                        <input id="gaurantor2add" type="text" name="gaurantor2add"
                                            class="form-control form-control-sm" placeholder="Addresss" />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <button type="button" class="btn btn-info waves-effect waves-light btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#guarantorlist2">
                                            View
                                        </button>
                                    </div>
                                </div>
                                <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
                                    data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                    Loading...">Submit</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-loanNotice" role="tabpanel">
                            <form id="loannotice" action="{{ route('shareupdate') }}" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6 card bg-transparent">
                                        <div class="card-body p-3">
                                            <div class="photo_upload text-center">
                                                <label for="" class="form-label">Notice for Installment</label>
                                                <div class="photo_upload_inner position-relative">
                                                    <img src="http://placehold.it/180" id="upload3" alt="Image"
                                                        class="upload">
                                                </div>
                                                <label for="photo3" class="custom-file-upload">Upload</label>
                                                <button class="close_btn" type="button" onclick="removeImg3()">Remove
                                                </button>
                                                <input class="inputFile" type="file" id="photo3" name="photo3"
                                                    onchange="readUrl3(this)" value="{{ old('photo') }}"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 card bg-transparent">
                                        <div class="card-body p-3">
                                            <div class="photo_upload text-center">
                                                <label for="" class="form-label">Notice for Election</label>
                                                <div class="photo_upload_inner position-relative">
                                                    <img src="http://placehold.it/180" id="upload3" alt="Image"
                                                        class="upload">
                                                </div>
                                                <label for="photo3" class="custom-file-upload">Upload</label>
                                                <button class="close_btn" type="button" onclick="removeImg3()">Remove
                                                </button>
                                                <input class="inputFile" type="file" id="photo3" name="photo3"
                                                    onchange="readUrl3(this)" value="{{ old('photo') }}"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-loanDocs" role="tabpanel">
                            <form id="laondoc" action="{{ route('shareupdate') }}" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Document Name </label>
                                        <input id="gaurantor1name" type="text" name="guranter1name"
                                            class="form-control form-control-sm" placeholder="Guarantor Name" required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Documents</label>
                                        <input id="documents" type="file" name="documents"
                                            class="form-control form-control-sm" placeholder="" required />
                                    </div>
                                </div>

                                <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
                                    data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                    Loading...">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card loandetails" style="display: none;">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Loan Name</th>
                                    <th>Ac</th>
                                    <th>Loan A/c</th>
                                    <th>Aamount</th>
                                    <th>Type</th>
                                    <th>Installment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 transactionData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalLong" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLongTitle">Installments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap print-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Inst. Date</th>
                                    <th>Op. Bal.</th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>Total</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody class="installmentsdata">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="printButton" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="guarantorlist1" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLongTitle">Guarantor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap print-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Loan Date</th>
                                    <th>Name</th>
                                    <th>Loan Amount</th>
                                    <th>Loan Bal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="guarantorDetails1">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="guarantorlist2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLongTitle">Guarantor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap print-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Loan Date</th>
                                    <th>Name</th>
                                    <th>Loan Amount</th>
                                    <th>Loan Bal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="guarantorDetails2">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script type="text/javascript">
        var a = document.getElementById("blah");
        var photo = document.getElementById("photo");

        function readUrl(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = (e) => {
                    a.src = e.target.result;
                };
            }
        }

        function removeImg() {
            a.src = "http://placehold.it/180";
            photo.value = "";
        }

        $(document).ready(function() {

        //    $('#loanamount').on('input', function(event) {
        //         event.preventDefault();
        //         let loanAmount = parseFloat($(this).val());
        //         let sharebalance = parseFloat($('#balance').val());

        //         if (isNaN(loanAmount) || isNaN(sharebalance) || sharebalance <= 0) {
        //             notify('Please provide valid numeric values for Loan Amount and Share Balance.', 'warning');
        //             $('#loanamount').val('');
        //             return;
        //         }
        //         let checkshareMoney = (loanAmount * 10) / 100;
        //         console.log(checkshareMoney);
        //         if (sharebalance === checkshareMoney) {
        //             notify('Share has value equal to Loan Amount x 10%', 'success');
        //         } else {
        //             $('#loanamount').val('');
        //             notify(`Share balance (${sharebalance}) does not equal Loan Amount x 10% (${checkshareMoney}).`, 'warning');
        //         }
        //     });




            $("#printButton").on("click", function() {
                var printContent = $(".print-content").clone();
                // Create a new window and append the cloned content
                var printWindow = window.open('', '_blank');
                printWindow.document.write(
                    '<html><head><title>Print</title><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></head><body>'
                );
                printWindow.document.write(printContent.html());
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                // Wait for the window to load before triggering the print
                printWindow.onload = function() {
                    printWindow.print();
                };
            });

            var currentDate = moment().format('DD-MM-YYYY');
            $("#transactionDate").val(currentDate);

            $("#accountNumber").blur(function() {
                var account = $(this).closest('form').find('input[name="accountNumber"]').val();
                var member = $('[name="memberType"]').val();
                getaccountdetails(account, member);
                $("#loanmember").block({
                    message: '<div class="sk-wave sk-primary mx-auto"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div>',
                    timeout: 500,
                    css: {
                        backgroundColor: "transparent",
                        border: "0"
                    },
                    overlayCSS: {
                        backgroundColor: "#fff",
                        opacity: 0.8
                    }
                })
            });

            $("#gaurantor1").blur(function() {
                var inputValue = $(this).val();
                var account = $('[name="accountNumber"]').val();
                if (account == "" || account == undefined) {
                    $("#gaurantor1").val('');
                    notify("Fill Loan Details", 'warning');
                    return false;
                }
                if (account == inputValue) {
                    $("#gaurantor1").val('');
                    notify("You Can't use same Ac details ", 'warning');
                    return false;
                }
                let gaurantor2 = $("#gaurantor2").val();
                if (gaurantor2 == inputValue) {
                    $("#gaurantor1").val('');
                    notify("You can't use same account number ", 'warning');
                    return false;
                }
                $.ajax({
                    url: "{{ route('loanupdate') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        'accountid': inputValue,
                        'actiontype': "grantordetails",
                    },
                    beforeSend: function() {
                        blockForm('#guarantorform')
                    },
                    complate: function() {
                        $("#guarantorform").unblock();
                    },
                    success: function(data) {
                        $("#guarantorform").unblock();
                        if (data.status == "success") {
                            $('#gaurantor1name').val(data.data.name);
                            $('#address').val(data.data.village);
                            setBenedata('guarantorDetails1', data.benelist)
                        } else {
                            $("#gaurantor2").val('');
                            notify(data.status, 'danger');
                        }
                    },
                    error: function(error) {
                        $("#guarantorform").unblock();
                        notify("Something went wrong", 'warning');
                    }
                });
            });

            $("#gaurantor2").blur(function() {
                var account = $('[name="accountNumber"]').val();
                var inputValue = $(this).val();
                if (account == "" || account == undefined) {
                    $("#gaurantor2").val('');
                    notify("Fill Loan Details", 'warning');
                    return false;
                }
                if (account == inputValue) {
                    $("#gaurantor2").val('');
                    notify("You Can't use same Ac details ", 'warning');
                    return false;
                }
                let gaurantor2 = $("#gaurantor1").val();
                if (gaurantor2 == inputValue) {
                    $("#gaurantor2").val('');
                    notify("You can't use same account number ", 'warning');
                    return false;
                }
                $.ajax({
                    url: "{{ route('loanupdate') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        'accountid': inputValue,
                        'actiontype': "grantordetails",
                    },
                    beforeSend: function() {
                        blockForm('#guarantorform')
                    },
                    complate: function() {
                        $("#guarantorform").unblock();
                    },
                    success: function(data) {
                        $("#guarantorform").unblock();
                        if (data.status == "success") {
                            $('#gaurantor2name').val(data.data.name);
                            $('#gaurantor2add').val(data.data.village);
                            setBenedata('guarantorDetails2', data.benelist)
                        } else {
                            $("#gaurantor2").val('');
                            notify(data.status, 'danger');
                        }
                    },
                    error: function(error) {
                        $("#guarantorform").unblock();
                        notify("Something went wrong", 'warning');
                    }
                });
            });

            $("#loanmember").validate({
                rules: {
                    loanDate: {
                        required: true,
                        customDate: true,
                    },
                    memberType: {
                        required: true,
                    },
                    accountNumber: {
                        required: true,
                        number: true,
                    },
                    memberType: {
                        required: true,
                    },
                    loanAcNo: {
                        required: true,
                    },
                    purpose: {
                        required: true,
                    },
                    loanType: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        number: true,
                    },
                    pernote: {
                        required: true,
                        number: true,
                    },
                    installmentType: {
                        required: true,
                    },
                    loanBy: {
                        required: true,
                    },
                    processingRates: {
                        required: true,
                        number: true,
                    },
                    fee: {
                        required: true,
                        number: true,
                    },
                    bankDeduction: {
                        required: true,
                    },
                    deductionAmount: {
                        required: true,
                        number: true,
                    }
                },
                messages: {
                    loanDate: {
                        required: "Please enter Loan Date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    },
                    memberType: {
                        required: "Please select action type",
                    },
                    accountNumber: {
                        required: "Please enter account number",
                        number: "Account number should be numeric",
                    },
                    loanAcNo: {
                        required: "Please enter Loan Account number",
                    },
                    purpose: {
                        required: "Please select purpose",
                    },
                    loanType: {
                        required: "Please select Loan Type",
                    },
                    amount: {
                        required: "Please enter amount",
                        number: "Amount number should be numeric",
                    },
                    pernote: {
                        required: "Please enter pernote",
                        number: "Pernote number should be numeric",
                    },
                    installmentType: {
                        required: "Please select installment Type",
                    },
                    loanBy: {
                        required: "Please select lona type",
                    },
                    processingRates: {
                        required: "Please enter processing rate",
                        number: "Rate should be numeric",
                    },
                    fee: {
                        required: "Please enter processing fee",
                        number: "Fee should be numeric",
                    },
                    bankDeduction: {
                        required: "Please select bank deduction",
                    },
                    deductionAmount: {
                        required: "Please enter deduction amount",
                        number: "Deduction amount number should be numeric",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#loanmember');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html(
                                '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                            ).attr(
                                'disabled', true).addClass('btn-secondary');
                            $(".extrafiled").css("display", "none");
                        },
                        complete: function() {
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status == "success") {
                                var account = $('#loanmember').find(
                                    'input[name="accountNumber"]').val();
                                var member = $('[name="memberType"]').val();
                                $('#loanmember')[0].reset();
                                getaccountdetails(account, member);
                                $('#schemesContainer').empty();
                                notify("Task Successfully Completed", 'success');
                            } else {
                                notify(data.status, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });

            $("#guarantorform").validate({
                rules: {
                    loanDate: {
                        required: true,
                        customDate: true,
                    },
                    memberType: {
                        required: true,
                    },
                },
                messages: {
                    loanDate: {
                        required: "Please enter Loan Date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    },
                    memberType: {
                        required: "Please select action type",
                    },
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#guarantorform');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html(
                                '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                            ).attr(
                                'disabled', true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status == "success") {
                                var account = $('#loanmember').find(
                                    'input[name="accountNumber"]').val();
                                var member = $('[name="memberType"]').val();
                                getaccountdetails(account, member);
                                form[0].reset();
                                notify("Task Successfully Completed", 'success');
                            } else {
                                notify(data.status, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });
        });

        function getaccountdetails(account, member) {
            var txndate = $('[name="loanDate"]').val();
            let membernumber =  $('#accountNumber').val();
            $.ajax({
                url: "{{ route('loanupdate') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    'account': account,
                    'member': member,
                    'transactionDate': txndate,
                    'actiontype': "getdata"
                },
                beforeSend: function() {
                    $('.transactionData').html('');
                    $("#accountdetails").css("display", "none");
                    $(".loandetails").css("display", "none");
                    $('#accountdetails').text('');
                    //blockForm('#sharemember')
                },
                complate: function() {
                    //  $("#sharemember").unblock();
                },
                success: function(data) {
                    //$("#sharemember").unblock();
                    if (data.status == "success") {


                        // $('#name').val(data.acdetails.name);
                        $('#name').val(data.acdetails.name);
                        $('#member-address').val(data.acdetails.address);
                        $('#balance').val(data.balance);
                        $('#accountNumber').val(membernumber);
                        var accountdetails = "Name " + data.acdetails.name + " Share Bal. " + data.balance

                        $('#accountdetails').text(accountdetails);
                        $("#accountdetails").css("display", "block");
                        $(".loandetails").css("display", "block");
                        $(".transactionData").html("");
                        var tbody = '';

                        if (data.txnacdetails.length === 0) {} else {
                            $('#guarantorform').find('input[name="loanid"]').val(data.txnacdetails[0].id);
                            $('#guarantorform').find('input[name="guranter1"]').val(data.txnacdetails[0]
                                .guranter1);
                            $('#guarantorform').find('input[name="guranter2"]').val(data.txnacdetails[0]
                                .guranter2);
                            $.each(data.txnacdetails, function(index, val) {

                                if (val.status == "Disbursed") {
                                    var trclass = `class="table-success"`;
                                } else if (val.status == "Closed") {
                                    var trclass = `class="table-danger"`;
                                } else if (val.status == "Inactive") {
                                    var trclass = `class="table-warning"`;
                                }
                                tbody += "<tr" + trclass + " >" +
                                    "<td>" + formatDate(val.loanDate) + "</td>" +
                                    "<td>" + val.purpose + "</td>" +
                                    "<td>" + val.accountNo + "</td>" +
                                    "<td>" + val.loanAcNo + "</td>" +
                                    "<td>" + val.loanAmount + "</td>" +
                                    "<td>" + val.installmentType + "</td>" +
                                    "<td>-</td>" +
                                    "<td>" + val.status + "</td>" +
                                    `<td>
                                <a href="javascript:void(0);" onclick="rowClicked('` +
                                    val.id + `')"><i class="fa-solid fa-pen-to-square border-0"></i></a>
                              <a href="javascript:void(0);" data-id="${val.loanType}" onclick="deleteloan('${val.id}', '${val.loanType}')">
                            <i class="fa-solid fa-trash border-0"></i>
                        </a>
                                                        </td></tr>`;
                            });
                        }
                        $('.transactionData').html(tbody);
                    } else {
                        $('#name').val();
                        $('#saving').val();
                        $(".accountdetails").css("display", "none");
                        $(".loandetails").css("display", "none");
                        notify(data.status, 'danger');
                    }
                },
                error: function(error) {
                    // $("#sharemember").unblock();
                    notify("Something went wrong", 'warning');
                }
            });
        }

        function getCheckedSchemes() {
            var selectedSchemes = [];
            $('input.form-check-input:checked').each(function() {
                selectedSchemes.push($(this).val());
            });
            return selectedSchemes;
        }

        function getCheckedSchemeds() {
            var selectedSchemes = [];
            $('input.form-check-input:checked').each(function() {
                selectedSchemes.push($(this).val());
            });
            return selectedSchemes;
        }

        function handleCheckedSchemes() {
            var selectedSchemes = getCheckedSchemes();
            {{--  console.log("Selected Schemes: ", selectedSchemes);  --}}
            sendToAjax(selectedSchemes);
        }

        function handleCheckedSchemess() {
            var selectedSchemes = getCheckedSchemeds();
            {{--  console.log("Selected Schemes: ", selectedSchemes);  --}}
            sendToAjasx(selectedSchemes);
        }
        $(document).on('change', 'input.form-check-inpute', function() {
            handleCheckedSchemes();
        });
        $(document).on('change', 'input.form-check-inputtt', function() {
            handleCheckedSchemess();
        });


        function sendToAjax(selectedSchemes) {
            $.ajax({
                url: '{{ route('getCheckedSchemes') }}',
                type: 'POST',
                data: {
                    schemes: selectedSchemes,
                    type: 'FD'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#fdamountupto').val(response.upto);
                        let loaninterests = $('#loaninterestRatess').val();
                        let newinterest = 0;
                        if (loaninterests) {
                            let maximuimInterestRate = parseFloat(response.interestRate);

                            if(!isNaN(maximuimInterestRate) && !isNaN(loaninterests)){
                                newinterest = parseFloat(loaninterests) + parseFloat(maximuimInterestRate);
                                $('#loanInterest').val(newinterest);
                            }else{
                                $('#loanInterest').val(newinterest);
                            }
                        } else {
                            console.warn("Value not set for #loaninterestRatess");
                        }




                        $('#rdamountupto').val('');
                        $('#dailyamountupto').val('');
                    } else {
                        $('#fdamountupto').val('');
                        $('#rdamountupto').val('');
                        $('#dailyamountupto').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }





        function sendToAjasx(selectedSchemes) {
            $.ajax({
                url: '{{ route('getCheckedSchemes') }}',
                type: 'POST',
                data: {
                    schemes: selectedSchemes,
                    type: 'RD'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    {{--  console.log(response.status);  --}}
                    if (response.status == 'success') {
                        $('#rdamountupto').val(response.upto);
                        $('#fdamountupto').val('');
                        $('#dailyamountupto').val('');
                    } else {
                        $('#rdamountupto').val('');
                        $('#fdamountupto').val('');
                        $('#dailyamountupto').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function sendToAjasx(selectedSchemes) {
            $.ajax({
                url: '{{ route('getCheckedSchemes') }}',
                type: 'POST',
                data: {
                    schemes: selectedSchemes,
                    type: 'DailyDeposit'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    {{--  console.log(response.status);  --}}
                    if (response.status == 'success') {
                        $('#dailyamountupto').val(response.upto);
                        $('#rdamountupto').val('');
                        $('#fdamountupto').val('');
                    } else {
                        $('#rdamountupto').val('');
                        $('#fdamountupto').val('');
                        $('#dailyamountupto').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // $(document).ready(function () {
        //     $('#loanamount').blur(function () {
        //         let loanAmount = parseFloat($(this).val());
        //         let sharebalance = parseFloat($('#balance').val());


        //         var fdamountupto = $('#fdamountupto').val();
        //         var rdamountupto = $('#rdamountupto').val();
        //         if ((fdamountupto !== "" && !isNaN(fdamountupto)) && rdamountupto === "") {

        //         }else if (fdamountupto !== '' && !isNaN(fdamountupto) && !isNaN(input.value)) {

        //         }else {
        //             let checkshareMoney = (loanAmount * 10) / 100;
        //             if (checkshareMoney <= sharebalance) {
        //                notify('Share has value equal to Loan Amount x 10%', 'success');
        //             } else {
        //                $(this).val('');
        //                notify(`Share balance (${sharebalance}) does not equal Loan Amount x 10% (${checkshareMoney}).`, 'warning');
        //             }
        //         }



        //     });
        // });




        function checkfdshceme(input) {
            var fdamountupto = $('#fdamountupto').val();
            var rdamountupto = $('#rdamountupto').val();
            var dailyamountupto = $('#dailyamountupto').val();

            let loanAmount = parseFloat($('#loanamount').val());
            let sharebalance = parseFloat($('#balance').val());
            // Check if one is empty and the other is a valid number
            if ((fdamountupto !== "" && !isNaN(fdamountupto)) && rdamountupto === "" && dailyamountupto === "") {

                fdamountupto = parseFloat(fdamountupto);
                var enteredAmount = parseFloat(input.value);

                if (enteredAmount > fdamountupto) {
                    input.value = fdamountupto;
                    notify('Amount cannot exceed ' + fdamountupto, 'warning');
                }
            } else if ((rdamountupto !== "" && !isNaN(rdamountupto)) && fdamountupto === "" && dailyamountupto === "") {

                fdamountupto = parseFloat(rdamountupto);
                var enteredAmount = parseFloat(input.value);

                if (enteredAmount > fdamountupto) {
                    input.value = fdamountupto;
                    notify('Amount cannot exceed ' + fdamountupto, 'warning');
                }

            }else if((dailyamountupto !== "" && !isNaN(dailyamountupto)) && fdamountupto === "" && rdamountupto === ""){

                fdamountupto = parseFloat(dailyamountupto);
                var enteredAmount = parseFloat(input.value);

                if (enteredAmount > fdamountupto) {
                    input.value = fdamountupto;
                    notify('Amount cannot exceed ' + fdamountupto, 'warning');
                }


            }else {
                let checkshareMoney = (loanAmount * 10) / 100;
                {{--  console.log(checkshareMoney);
                console.log(sharebalance);  --}}
                    if (checkshareMoney <= sharebalance) {
                        notify('Share has value equal to Loan Amount x 10%', 'success');
                    } else {
                        $('#loanamount').val('');
                        notify(`Share balance (${sharebalance}) does not equal Loan Amount x 10% (${checkshareMoney}).`, 'warning');
                    }
            }

            if (fdamountupto !== '' && !isNaN(fdamountupto) && !isNaN(input.value)) {
                fdamountupto = parseFloat(fdamountupto);
                var enteredAmount = parseFloat(input.value);

                if (enteredAmount > fdamountupto) {
                    input.value = fdamountupto;
                    notify('Amount cannot exceed ' + fdamountupto, 'warning');
                }
            }
        }

        function interestChange(){
            let asdasdas = $(inlineCheckbox).val();
        }


        function getLoanType(ele) {
            $('#fdamountupto').val('');
            $('#rdamountupto').val('');
            var schemesHtml = '';
            var member = $('[name="memberType"]').val();
            var accountNumber = $('[name="accountNumber"]').val();
            let membernumber =  $('#accountNumber').val();
            let loanType = $(ele).find(':selected').data('loantype');
            let loanId = $(ele).find(':selected').val();

            $('#schemesContainermaster').css('display', 'none');

            $.ajax({
                    url: "{{ route('getfdschemesloan') }}",
                    type: "POST",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'We are fetching Fd.',
                            allowOutsideClick: () => !swal.isLoading(),
                            onOpen: () => {
                                swal.showLoading()
                            }
                        });
                    },
                    data: {
                        'actiontype': "getLoatype",
                        'member': member,
                        'accountNumber': accountNumber,
                        'loanType': loanType,
                        'loanId' : loanId
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            swal.close();
                            let loans = data.loanType;
                            let fdOrRdLoan = data.data;

                            if (loans && loans.interest !== undefined) {
                                $('#loaninterestRatess').val(loans.interest);
                            }


                            if (loans.loantypess === 'FD') {
                                $('#fdamountupto').val(0);
                                $('#rdamountupto').val('');
                                $('#dailyamountupto').val('');
                                $('#schemesContainermaster').css('display', 'inline-block');

                                let schemesHtml = '';
                                $.each(fdOrRdLoan, function (index, scheme) {
                                    {{--  console.log(scheme);  --}}
                                    schemesHtml += `
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input form-check-inpute"
                                                name="schemenames[]"
                                                type="checkbox"
                                                id="inlineCheckbox${scheme.id}"
                                                value="${scheme.id}"
                                                ${scheme.status === 'Pluge' ? 'checked' : ''}>
                                            <label class="form-check-label" for="inlineCheckbox${scheme.id}">
                                                <strong>Scheme Name:</strong>
                                                <small>${scheme.schemname},
                                                    <strong>|| Fd.No:</strong>: ${scheme.fdNo},
                                                    <strong>|| Amount:</strong>: ${scheme.principalAmount}</small>
                                                    <strong>|| Intt.:</strong>: ${scheme.interestRate}</small>
                                            </label>
                                        </div>`;

                                });



                                $('#schemesContainer').html(schemesHtml);
                                $('#loanmember').find('[name="processingRates"]').val(loans.processingFee).prop('readonly', false);
                                $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
                                $('#loanmember').find('[name="loanMonth"]').val(loans.months).prop('readonly', false);
                                $('#loanmember').find('[name="loanYear"]').val(loans.years).prop('readonly', false);
                                $('#loanmember').find('[name="installmentType"]').val(loans.insType).prop('readonly', false);
                                $('#loanmember').find('[name="loanInterest"]').val(loans.interest).prop('readonly', false);

                                $('#loanmember').find('[name="defintr"]').val(loans.penaltyInterest).prop('readonly', false);
                                $('#loanmember').find('[name="loan_app_fee"]').val(loans.loan_app_charges).prop('readonly', false);

                            } else if (loans.loantypess === 'RD') {
                                $('#schemesContainermaster').css('display', 'inline-block');
                                $('#schemesContainer').html('');

                                let schemesHtml = '';
                                $.each(fdOrRdLoan, function(index, scheme) {
                                schemesHtml += `
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input form-check-inputtt" name="schemenames[]"
                                            type="checkbox" id="inlineCheckbox${scheme.id}" value="${scheme.id}"
                                            ${scheme.status === 'Pluge' ? 'checked' : ''}>
                                            <label class="form-check-label" for="inlineCheckbox${scheme.id}">
                                                <strong>Scheme Name:</strong>
                                                <small>${scheme.schemname}, <strong>RD.No:</strong>: ${scheme.rd_account_no},
                                                    <strong>Amount:</strong>: ${scheme.fetchamount}</small>
                                            </label>
                                    </div>`;
                                });

                                $('#schemesContainer').html(schemesHtml);
                                $('#loanmember').find('[name="processingRates"]').val(loans.processingFee).prop('readonly', false);
                                $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
                                $('#loanmember').find('[name="loanMonth"]').val(loans.months).prop('readonly', false);
                                $('#loanmember').find('[name="loanYear"]').val(loans.years).prop('readonly', false);
                                $('#loanmember').find('[name="installmentType"]').val(loans.insType).prop('readonly', false);
                                $('#loanmember').find('[name="loanInterest"]').val(loans.interest).prop('readonly', false);
                                $('#loanmember').find('[name="defintr"]').val(loans.penaltyInterest).prop('readonly', false);
                                $('#loanmember').find('[name="loan_app_fee"]').val(loans.loan_app_charges).prop('readonly', false);

                            } else if(loans.loantypess === 'MTLoan'){
                                $('#schemesContainermaster').css('display', 'none');
                                $('#loanmember').find('[name="processingRates"]').val(loans.processingFee).prop('readonly', false);
                                $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
                                $('#loanmember').find('[name="loanMonth"]').val(loans.months).prop('readonly', false);
                                $('#loanmember').find('[name="loanYear"]').val(loans.years).prop('readonly', false);
                                $('#loanmember').find('[name="installmentType"]').val(loans.insType).prop('readonly', false);
                                $('#loanmember').find('[name="loanInterest"]').val(loans.interest).prop('readonly', false);
                                $('#loanmember').find('[name="defintr"]').val(loans.penaltyInterest).prop('readonly', false);
                                $('#loanmember').find('[name="loan_app_fee"]').val(loans.loan_app_charges).prop('readonly', false);
                            }else if(loans.loantypess === 'DailyDeposit'){
                                $('#schemesContainermaster').css('display', 'inline-block');
                                $('#schemesContainer').html('');

                                let schemesHtml = '';
                                $.each(fdOrRdLoan, function(index, scheme) {
                                schemesHtml += `
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input form-check-inputtt" name="schemenames[]"
                                            type="checkbox" id="inlineCheckbox${scheme.id}" value="${scheme.id}"
                                            ${scheme.status === 'Pluge' ? 'checked' : ''}>
                                            <label class="form-check-label" for="inlineCheckbox${scheme.id}">
                                                <strong>Scheme Name:</strong>
                                                <small>${scheme.schemname}, <strong>Daily.No:</strong>: ${scheme.account_no},
                                                    <strong>Amount:</strong>: ${scheme.deposit_amount}</small>
                                            </label>
                                    </div>`;
                                });

                                $('#schemesContainer').html(schemesHtml);
                                $('#loanmember').find('[name="processingRates"]').val(loans.processingFee).prop('readonly', false);
                                $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
                                $('#loanmember').find('[name="loanMonth"]').val(loans.months).prop('readonly', false);
                                $('#loanmember').find('[name="loanYear"]').val(loans.years).prop('readonly', false);
                                $('#loanmember').find('[name="installmentType"]').val(loans.insType).prop('readonly', false);
                                $('#loanmember').find('[name="loanInterest"]').val(loans.interest).prop('readonly', false);
                                $('#loanmember').find('[name="defintr"]').val(loans.penaltyInterest).prop('readonly', false);
                                $('#loanmember').find('[name="loan_app_fee"]').val(loans.loan_app_charges).prop('readonly', false);
                            }
                        }
                    }
                });





            // if (gcode == '46') {
            //     if (!accountNumber && accountNumber == '') {
            //         notify('Please Enter Membership No', 'warning');
            //         $('[name="loanType"]').select().val('').trigger('change');
            //         return false;
            //     }
            //     $.ajax({
            //         url: "{{ route('getfdschemesloan') }}",
            //         type: "POST",
            //         dataType: 'json',
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         beforeSend: function() {
            //             swal({
            //                 title: 'Wait!',
            //                 text: 'We are fetching Fd.',
            //                 allowOutsideClick: () => !swal.isLoading(),
            //                 onOpen: () => {
            //                     swal.showLoading()
            //                 }
            //             });
            //         },
            //         data: {
            //             'actiontype': "getLoatype",
            //             'member': member,
            //             'accountNumber': accountNumber,
            //             'loantypeid': gcode
            //         },
            //         success: function(data) {
            //             if (data.status == 'success') {
            //                 $('#fdamountupto').val(0);
            //                 $('#rdamountupto').val('');
            //                 swal.close();
            //                 var schemes = data.data;
            //                 var schemesHtml = '';
            //                 $('#schemesContainermaster').css('display', 'inline-block');
            //                 $.each(schemes, function(index, scheme) {
            //                     schemesHtml += `
            //                         <div class="form-check form-check-inline">
            //                             <input class="form-check-input form-check-inpute"
            //                                 name="schemenames[]"
            //                                 type="checkbox"
            //                                 id="inlineCheckbox${scheme.id}"
            //                                 value="${scheme.id}"
            //                                 ${scheme.status === 'Pluge' ? 'checked' : ''}>
            //                             <label class="form-check-label" for="inlineCheckbox${scheme.id}">
            //                                 <strong>Scheme Name:</strong>
            //                                 <small>${scheme.schemname},
            //                                     <strong>Fd.No:</strong>: ${scheme.fdNo},
            //                                     <strong>Amount:</strong>: ${scheme.principalAmount}</small>
            //                             </label>
            //                         </div>`;
            //                 });




            //                 $('#schemesContainer').html(schemesHtml);
            //                 $('#loanmember').find('[name="processingRates"]').val('').prop('readonly',
            //                     false);
            //                 $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
            //                 $('#loanmember').find('[name="loanMonth"]').val(schemes.months).prop('readonly', false);
            //                 $('#loanmember').find('[name="loanYear"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="installmentType"]').val('').prop('readonly', false);
            //                 // $('[name="loanYear"]').val(data.year);
            //                 $('#loanmember').find('[name="loanInterest"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="defintr"]').val('').prop('readonly', false);
            //                 // getextradetails(data.data.loanType)
            //                 // console.log(data);
            //             } else {
            //                 $('#fdamountupto').val(0);
            //                 $('#rdamountupto').val('');
            //                 swal.close();
            //             }
            //         }
            //     });

            // } else if (gcode == '47') {

            //     if (!accountNumber && accountNumber == '') {
            //         notify('Please Enter Membership No', 'warning');
            //         $('[name="loanType"]').select().val('').trigger('change');
            //         return false;
            //     }
            //     $.ajax({
            //         url: "{{ route('getrdschemesloan') }}",
            //         type: "POST",
            //         dataType: 'json',
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         beforeSend: function() {
            //             swal({
            //                 title: 'Wait!',
            //                 text: 'We are fetching Rd.',
            //                 allowOutsideClick: () => !swal.isLoading(),
            //                 onOpen: () => {
            //                     swal.showLoading()
            //                 }
            //             });
            //         },
            //         data: {
            //             'actiontype': "getLoatype",
            //             'member': member,
            //             'accountNumber': accountNumber,
            //             'loantypeid': gcode
            //         },
            //         success: function(data) {
            //             if (data.status == 'success') {
            //                 $('#rdamountupto').val(0);
            //                 $('#rdamountupto').val('');
            //                 swal.close();
            //                 var schemes = data.data;
            //                 var schemesHtml = '';
            //                 $('#schemesContainermaster').css('display', 'inline-block');
            //                 $.each(schemes, function(index, scheme) {
            //                 schemesHtml += `
            //                     <div class="form-check form-check-inline ">
            //                         <input class="form-check-input form-check-inputtt" name="schemenames[]" type="checkbox" id="inlineCheckbox${scheme.id}" value="${scheme.id}">
            //                             <label class="form-check-label" for="inlineCheckbox${scheme.id}">
            //                                 <strong>Scheme Name:</strong>
            //                                 <small>${scheme.schemname}, <strong>Fd.No:</strong>: ${scheme.rd_account_no}, <strong>Amount:</strong>: ${scheme.fetchamount}</small>
            //                             </label>
            //                     </div>`;
            //                 });
            //                 $('#schemesContainer').html(schemesHtml);
            //                 $('#loanmember').find('[name="processingRates"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="fee"]').val(0).prop('readonly', false);
            //                 $('#loanmember').find('[name="loanMonth"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="loanYear"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="installmentType"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="loanInterest"]').val('').prop('readonly', false);
            //                 $('#loanmember').find('[name="defintr"]').val('').prop('readonly', false);

            //             } else {

            //                 $('#rdamountupto').val(0);
            //                 $('#rdamountupto').val('');
            //                 swal.close();
            //             }
            //         }
            //     });



            // } else {


            //     $.ajax({
            //         url: "{{ route('loanupdate') }}",
            //         type: "POST",
            //         dataType: 'json',
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         beforeSend: function() {
            //             // swal({
            //             //     title: 'Wait!',
            //             //     text: 'We are fetching district.',
            //             //     allowOutsideClick: () => !swal.isLoading(),
            //             //     onOpen: () => {
            //             //         swal.showLoading()
            //             //     }
            //             // });
            //         },
            //         data: {
            //             'actiontype': "getLoatype",
            //             'member': member,
            //             'loantypeid': $(ele).val()
            //         },
            //         success: function(data) {
            //             swal.close();

            //             // $('#accountNumber').val(membernumber);
            //             console.log(data);


            //             $('#loanmember').find('[name="accountNumber"]').val(accountNumber);
            //             $('#loanmember').find('[name="processingRates"]').val(data.data.processingFee).prop(
            //                 'readonly',
            //                 true);
            //             $('#loanmember').find('[name="loan_app_fee"]').val(data.data.loan_app_charges);
            //             $('#loanmember').find('[name="fee"]').val(0).prop('readonly', true);
            //             $('#loanmember').find('[name="loanMonth"]').val(data.data.months).prop('readonly',
            //             true);
            //             $('#loanmember').find('[name="loanYear"]').val(data.data.years).prop('readonly', true);
            //             $('#loanmember').find('[name="installmentType"]').val(data.data.insType).prop(
            //                 'readonly', true);
            //             // $('[name="loanYear"]').val(data.year);
            //             $('#loanmember').find('[name="loanInterest"]').val(data.data.interest).prop('readonly',
            //                 true);
            //             $('#loanmember').find('[name="defintr"]').val(data.data.penaltyInterest).prop(
            //                 'readonly', true);
            //             getextradetails(data.data.loanType)
            //             console.log(data);
            //         }
            //     });

            // }
        }

        function loanby(ele) {
            var type = $(ele).val();
            if (type == 'Transfer') {
                $(".bank").css("display", "block");
                $(".savingaccountdiv").css("display", "none");
            }else if(type == 'Saving'){

                $(".savingaccountdiv").css("display", "block");
                let membertype = $('#status-org').val();
                let membership = $('#accountNumber').val();
                if(membertype && membership){
                    $.ajax({
                        url : "{{ route('get-saving-account') }}",
                        type : 'post',
                        data : {membertype : membertype ,membership:membership},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType : 'json',
                        success : function(res){
                            if(res.status === 'success'){
                                let saving_account = res.saving_account;
                                $('#savingaccounts').val(saving_account.accountNo);
                                $(".bank").css("display", "none");
                            }else{
                                notify(res.messages,'warning');
                            }
                        }
                    });
                }

            }else {
                $(".savingaccountdiv").css("display", "none");
                $(".bank").css("display", "none");
            }
        }

        function bankdeduction(ele) {
            var type = $(ele).val();
            if (type == 'Yes') {
                $("#deductionamt").css("display", "block");
            } else {
                $("#deductionamt").css("display", "none");
            }
        }

        function getinstallments() {
            var loanAmount = $('#loanmember').find('[name="amount"]').val();
            var intrest = $('#loanmember').find('[name="loanInterest"]').val();
            var instType = $('#loanmember').find('[name="installmentType"]').val();
            var year = $('#loanmember').find('[name="loanYear"]').val();
            var month = $('#loanmember').find('[name="loanMonth"]').val();
            var loandate = $('#loanmember').find('[name="loanDate"]').val();
            var loanType = $('#loanType').val(); //$('#loanmember').find('[name="loanType"]').val();

            $.ajax({
                url: "{{ route('loanupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // swal({
                    //     title: 'Wait!',
                    //     text: 'We are fetching district.',
                    //     allowOutsideClick: () => !swal.isLoading(),
                    //     onOpen: () => {
                    //         swal.showLoading()
                    //     }
                    // });
                },
                data: {
                    'actiontype': "getInstallmets",
                    'loanAmount': loanAmount,
                    'intrest': intrest,
                    'instType': instType,
                    'year': year,
                    'month': month,
                    "loandate": loandate,
                    "loanType": loanType,
                },
                success: function(data) {
                    $('#modalLong').find('.installmentsdata').html(data);
                }
            });
            $('#modalLong').modal('show');
        }

        function getmemberLoanType(ele) {
            $.ajax({
                url: "{{ route('loanupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'We are fetching district.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
                data: {
                    'actiontype': "getLoanType",
                    'memberType': $(ele).val()
                },
                success: function(data) {
                    swal.close();
                    var out = `<option value="">Loan Type</option>`;
                    $.each(data.data, function (index, value) {
                        out += `<option data="` + value.loanType + `" value="` + value.id + `">` + value.name + `</option>`;

                    });

                    $('[name="loanType"]').html(out);
                }
            });
        }

        function getextradetails() {

            var account = $('[name="accountNumber"]').val();
            var typeid = $('[name="loanType"]').val();
            var type = $('#loanType option:selected').attr('data');

            if (account != "" && type != "") {
                $.ajax({
                    url: "{{ route('loanupdate') }}",
                    type: "POST",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('[name="fd_ids[]"]').html('');
                        $(".extrafiled").css("display", "none");
                        // swal({
                        //     title: 'Wait!',
                        //     text: 'We are fetching district.',
                        //     allowOutsideClick: () => !swal.isLoading(),
                        //     onOpen: () => {
                        //         swal.showLoading()
                        //     }
                        // });
                    },
                    data: {
                        'actiontype': "getaclist",
                        'type': type,
                        'typeid': typeid,
                        "memberAc": account
                    },
                    success: function(data) {
                        if (data.data.type == "Loan Against FD" || data.data.type == "Loan Against RD") {
                            $(".extrafiled").css("display", "block");
                            $("#extraf").text(data.data.inpuplabel);
                            var out = `<option value="">` + data.data.inpup + `</option>`;
                            $.each(data.data.aclist, function(index, value) {
                                out += `<option  value="` + value.id + `">` + data.data.inpuplabel +
                                    `- ` + value.account + ` Amount - ` + value.amount +
                                    `</option>`;
                            });
                            $('[name="fd_ids[]"]').html(out);
                            //   $('[name="fd_id[]"]').html(out);
                        }
                    }
                });
            }
            // var htmldata =
            //     ` <div class="mb-3 col-md-3 col-sm-12">
        //                  <label class="form-label mb-1" for="status-org">Purpose </label>
        //                      <select name="purpose" id="" class="select form-select form-select-sm" data-placeholder="Active">
        //                        <option value="">Select Purpose</option>
        //                        <option value="">"Hello"</option>
        //                     </select>
        //                 </div>`;

            // $('.extrafiled').html(htmldata);
            // $(".extrafiled").css("display", "block");
        }

        function setBenedata(tableclass, benedata) {
            var srNo = 1;
            var tbody = '';
            $.each(benedata, function(index, value) {
                tbody += "<tr><td> " + srNo++ + "</td>" +
                    "<td style='display:none'>" + value.id + "</td>" +
                    "<td>" + value.loanDate + "</td>" +
                    "<td>" + value.member_account.name + "</td>" +
                    "<td>" + value.loanAmount + "</td>" +
                    "<td>" + "6000" + "</td>" +
                    "<td>" + value.status + "</td></td>";
            });
            $('.' + tableclass).html(tbody);
        }

        function viewdetails(id) {


        }

        function rowClicked(id) {
            blockForm('#loanmember')

            $.ajax({
                url: "{{ route('loanupdate') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {},
                data: {
                    'actiontype': "loandata",
                    'id': id,
                },
                success: function(response) {
                    if (response.status == 'success') {

                        // $('#loanmember').find('input[name="name"]').val(response.data.member_account.name);
                        $('#loanmember').find('input[name="id"]').val(id);
                        $('#guarantorform').find('input[name="loanid"]').val(id);
                        $('#loanmember').find('input[name="accountNumber"]').val(response.data.accountNo);
                        $('#loanmember').find('input[name="processingRates"]').val(response.data.processingRates);
                        $('#loanmember').find('input[name="loanYear"]').val(response.data.loanYear);
                        $('#loanmember').find('input[name="loanMonth"]').val(response.data.loanMonth);
                        $('#loanmember').find('input[name="loanInterest"]').val(response.data.loanInterest);
                        $('#loanmember').find('input[name="defintr"]').val(response.data.loanPanelty);
                        $('#guarantorform').find('input[name="guranter1"]').val(response.data.guranter1);
                        $('#guarantorform').find('input[name="guranter2"]').val(response.data.guranter2);
                        $('#loanmember').find('input[name="actiontype"]').val('actiontypeupdate');
                        $('#loanmember').find('input[name="loanDate"]').val(formatDate(response.data.loanDate));
                        $('#loanmember').find('input[name="loanAcNo"]').val(response.data.loanAcNo);
                        $('#loanmember').find('select[name="purpose"]').val(response.data.purpose).trigger(
                            'change');
                        $('#loanmember').find('select[name="loanType"]').val(response.data.loanType).trigger(
                            'change');
                        $('#loanmember').find('input[name="loan_app_fee"]').val(response.data.loan_app_charges);
                        $('#loanmember').find('input[name="amount"]').val(response.data.loanAmount);
                        $('#loanmember').find('input[name="pernote"]').val(response.data.pernote);
                        $('#loanmember').find('input[name="bankDeduction"]').val(response.data.bankDeduction);
                        $('#loanmember').find('select[name="loanBy"]').val(response.data.cropType).trigger(
                            'change');
                        $('#loanmember').find('select[name="agentId"]').val(response.data.agentId).trigger(
                            'change');
                    }
                }
            });
            $("#loanmember").unblock();
        }

        function deleteloan(id,loanType) {

            swal({
                title: 'Are you sure ?',
                text: "You want to delete a transaction. It cannot be recovered",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes Delete",
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !swal.isLoading(),
                preConfirm: () => {
                    return new Promise((resolve) => {
                        $.ajax({
                            url: "{{ route('loanupdate') }}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: {
                                'actiontype': "deleteloan",
                                'id': id,
                                'loanType' : loanType,
                            },
                            success: function(data) {

                                if (data.status == "success") {
                                    var account = $('#loanmember').find(
                                        'input[name="accountNumber"]').val();
                                    var member = $('[name="memberType"]').val();
                                    getaccountdetails(account, member);
                                    swal(
                                        'Deleted',
                                        "Transaction deleted successfully",
                                        'success'
                                    );
                                } else {

                                    swal('Oops!', data.status, 'error');
                                }
                            },
                            error: function(errors) {
                                swal.close();
                                showError(errors, 'withoutform');
                            }
                        });
                    });
                },
            });
        }

        function resetforms() {

            var form = $('#loanmember');
            form[0].reset();
            var currentDate = moment().format('DD-MM-YYYY');
            $("#transactionDate").val(currentDate);

        }
    </script>
@endpush

@push('style')
    <style>
        /* Add an asterisk to the right of required text fields */
        label.required::after {
            content: " *";
            color: red;
            /* You can customize the color */
        }

        /* Optional: Adjusting spacing for better aesthetics */
        label {
            margin-right: 5px;
            /* Adjust as needed */
        }

        .custom-file-upload {
            background-color: #7367f0;
            color: white;
            padding: 8px 10px;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin: 0;
            margin-top: 15px;
            font-size: 13px;
            border-radius: 5px;
        }

        .inputFile {
            display: none;
        }

        .photo_upload img {
            width: 150px;
            height: 110px;
            border-radius: 5px;
            object-fit: cover;
            position: relative;
        }

        .close_btn {
            background-color: #9F0000;
            color: white;
            padding: 8px 10px;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin: 0;
            margin-top: 15px;
            font-size: 13px;
            border-radius: 5px;
        }

        .tablee table th,
        .tablee table td {
            padding: 8px;
        }

        .saving_column {
            position: relative;
        }

        .saving_column p {
            position: absolute;
            bottom: -30px;
            left: 12px;
            margin: 0;
            min-height: 38px;
        }
    </style>
@endpush


