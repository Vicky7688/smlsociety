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
                                {{-- <i class="tf-icons ti ti-circle-number-1 ti-md me-1"></i>  --}}
                                Loan Details
                                <!-- <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">3</span> -->
                            </button>
                        </li>
                        {{-- <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-Guarantor"
                                aria-controls="navs-pills-justified-Guarantor" aria-selected="true">
                                <i class="tf-icons ti ti-circle-number-2 ti-md me-1"></i> Guarantor Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-loanDocs"
                                aria-controls="navs-pills-justified-loanDocs" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-3 ti-md me-1"></i>Loan Documents
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-loanNotice"
                                aria-controls="navs-pills-justified-loanNotice" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-4 ti-md me-1"></i>Notice
                            </button>
                        </li> --}}

                        {{-- <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-pills-justified-mclDetails"
                                aria-controls="navs-pills-justified-mclDetails" aria-selected="false" tabindex="-1">
                                <i class="tf-icons ti ti-circle-number-5 ti-md me-1"></i> MCL Details
                            </button>
                        </li> --}}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="navs-pills-justified-home" role="tabpanel">
                            <form id="loanmember" enctype="multipart/form-data">
                                {{-- <input type="hidden" id="fdamountupto" name="fdamountupto">
                                <input type="hidden" id="rdamountupto" name="rdamountupto"> --}}
                                <input type="hidden" id="dailyamountupto" name="dailyamountupto">
                                <div class="row">
                                    <input type="hidden" name="actiontype" value="transactionloan" />
                                    <input type="hidden" id="id" name="id" value="" />
                                    <input type="hidden" id="loanId" name="loanId" value="" />
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
                                            {{-- <option value="NonMember">Nominal Member</option>
                                            <option value="Staff">Staff</option> --}}
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Membership No</label>
                                        <input type="text" id="accountNumber" name="accountNumber"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                        <div id="accountdetails" class="form-text text-success"> </div>
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="loanAcNo" class="form-label">Loan AC No</label>
                                        <input type="text" id="loanAcNo" onkeyup="checkLoanNo(this)" name="loanAcNo"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                        <div id="loanerror" class="form-text text-success"> </div>
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Purpose</label>
                                        <select name="purpose" id="" class="select form-select form-select-sm"
                                            data-placeholder="Active">
                                            <option value="">Select Purpose</option>
                                            @foreach ($purposes as $purpose)
                                                <option value="{{ $purpose->name }}">{{ $purpose->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col col-md-3 col-sm-12 ecommerce-select-dropdown">
                                        <label class="form-label mb-1" for="status-org">Loan Name</label>
                                        <select name="loanType" id="loanType" class="select form-select form-select-sm"
                                            onchange="getLoanType(this)">
                                            <option value="" disabled selected>Loan Type</option>
                                            @foreach ($loantypes as $loantype)
                                                <option value="{{ $loantype->id }}">
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
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                                    name="check" value="option1" onchange="getLoanType(this)">
                                                <label class="form-check-label" for="inlineCheckbox1">1</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Amount</label>
                                        <input type="text" name="amount" id="loanamount"
                                            class="form-control form-control-sm" placeholder="Enter value" required
                                            oninput="checkfdshceme(this)" />
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Pernote No</label>
                                        <input type="text" name="pernote" onkeyup="checkPernoteNo(this)"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                        <div id="pernoterror" class="form-text text-success"> </div>
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
                                        <input type="text" name="loanInterest" class="form-control form-control-sm"
                                            id="loanInterest" placeholder="Enter value" required readonly />
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="txndate" class="form-label">Penal Intrest</label>
                                        <input type="text" name="defintr" class="form-control form-control-sm"
                                            placeholder="Enter value" required />
                                    </div>
                                    {{-- <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">Bank Deduction </label>
                                        <select name="bankDeduction" id="status-org" class="form-select form-select-sm"
                                            onchange="bankdeduction(this)">
                                            <option selected value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div> --}}
                                    <div class="col-md-3 mb-3 col-sm-12" id="deductionamt" style="display: none;">
                                        <label for="txndate" class="form-label">Deduction Amount</label>
                                        <input type="text" name="deduction" class="form-control form-control-sm"
                                            placeholder="Enter value" value="0" required />
                                    </div>

                                    <div class="col-md-3 mb-3 col-sm-12">
                                        <label for="" class="form-label">Loan App Fees</label>
                                        <input type="text" name="loan_app_fee" id="loan_app_fee" value="0"
                                            class="form-control form-control-sm" placeholder="Enter value" required />
                                    </div>

                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <label class="form-label mb-1" for="status-org">BY</label>
                                        <select name="loanBy" id="loanBy" class="form-select form-select-sm"
                                            onchange="loanby(this)">
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Bank</option>
                                            {{-- <option value="Saving">Saving</option> --}}
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
                                        <input type="text" name="savingaccounts" id="savingaccounts"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 mb-3 col-sm-12 bank" style="display: none;">
                                        <label for="chequeNo" class="form-label">Cheque No Bank</label>
                                        <input id="chequeNo" type="text" name="chequeNo"
                                            class="form-control form-control-sm" placeholder="Cheque No" />
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
                                <h2>Guarantor Details</h2>
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
                                {{-- <div class="row">
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
                                </div> --}}
                                {{-- <h2>Notice</h2>

                                <div class="row">
                                    <div class="col-md-6 card bg-transparent">
                                        <div class="card-body p-3">
                                            <div class="photo_upload text-center">
                                                <label class="form-label mb-1" for="status-org">Notice for
                                                    Installment</label>
                                                <input id="notice_for_installment" type="file"
                                                    name="notice_for_installment" class="form-control form-control-sm"
                                                    placeholder="" />

                                                <a id="download_notice_for_installment_img" href="" download>
                                                    <img id="notice_for_installment_img" src="" width="100"
                                                        style="display: none; margin-top: 10px;" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 card bg-transparent">
                                        <div class="card-body p-3">
                                            <div class="photo_upload text-center">
                                                <label class="form-label mb-1" for="status-org">Notice for
                                                    Election</label>
                                                <input id="notice_for_election" type="file" name="notice_for_election"
                                                    class="form-control form-control-sm" placeholder="" />
                                                <a id="download_notice_for_election_img" href="" download>
                                                    <img id="notice_for_election_img" src="" width="100"
                                                        style="display: none; margin-top: 10px;" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <h2>Loan Document</h2>
                                <div id="documentsContainer">
                                    <div class="row document-row">
                                        <div class="mb-3 col-md-3 col-sm-12">
                                            <label class="form-label mb-1">Document Name</label>
                                            <input type="text" name="guranter1name[]"
                                                class="form-control form-control-sm" placeholder="Guarantor Name"
                                                required />
                                        </div>

                                        <div class="mb-3 col-md-3 col-sm-12">
                                            <label class="form-label mb-1">Documents</label>
                                            <input type="file" name="documents[]"
                                                class="form-control form-control-sm doc-input" required />
                                            <a class="download-link" href="" download>
                                                <img class="docimg" src="" width="100"
                                                    style="display: none; margin-top: 10px;" />
                                            </a>
                                        </div>

                                        <div class="mb-3 col-md-2 col-sm-12 align-self-end">
                                            <button type="button" class="btn btn-success btn-sm add-row">Add</button>
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-row">Remove</button>
                                        </div>
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
                        {{-- <div class="tab-pane fade " id="navs-pills-justified-Guarantor" role="tabpanel">
                            <form id="guarantorform" >

                                <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
                                    data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                    Loading...">Submit</button>
                            </form>
                        </div> --}}
                        {{-- <div class="tab-pane fade" id="navs-pills-justified-loanNotice" role="tabpanel">
                            <form id="loannotice" >

                            </form>
                        </div> --}}
                        {{-- <div class="tab-pane fade" id="navs-pills-justified-loanDocs" role="tabpanel">
                            <form id="laondoc" >
                                {{ csrf_field() }}


                                <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit"
                                    data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                    Loading...">Submit</button>
                            </form>
                        </div> --}}
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
        $(document).ready(function() {
            function previewImage(input, imgElement, linkElement) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(imgElement).attr('src', e.target.result).show();
                        $(linkElement).attr('href', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Handle image preview
            $(document).on('change', '.doc-input', function() {
                var row = $(this).closest('.document-row');
                var img = row.find('.docimg');
                var link = row.find('.download-link');
                previewImage(this, img, link);
            });

            // Add new row
            $(document).on('click', '.add-row', function() {
                var newRow = $(this).closest('.document-row').clone();
                newRow.find('input').val(''); // Clear inputs
                newRow.find('img').attr('src', '').hide(); // Hide image
                newRow.find('a').attr('href', ''); // Clear href
                $('#documentsContainer').append(newRow);
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if ($('.document-row').length > 1) {
                    $(this).closest('.document-row').remove();
                } else {
                    alert("At least one document entry is required.");
                }
            });
        });


        $(document).ready(function() {
            function previewImage(input, imgElement, linkElement) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(imgElement).attr('src', e.target.result).show(); // Set image source and show it
                        $(linkElement).attr('href', e.target.result); // Set link for download
                    };
                    reader.readAsDataURL(input.files[0]); // Read file
                }
            }
            $("#documentsimg").change(function() {
                previewImage(this, "#docimg", "#downloadpucimg");
            });
            $("#notice_for_installment").change(function() {
                previewImage(this, "#notice_for_installment_img", "#download_notice_for_installment_img");
            });
            $("#notice_for_election").change(function() {
                previewImage(this, "#notice_for_election_img", "#download_notice_for_election_img");
            });
        });


        var currentDate = moment().format('DD-MM-YYYY');
        $("#transactionDate").val(currentDate);

        function checkLoanNo(ele) {
            let loanAcNo = $(ele).val();

            $.ajax({
                url: "{{ route('checkLoanNo') }}",
                type: 'POST',
                data: {
                    loanAcNo: loanAcNo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let $input = $('#loanAcNo');
                    let $errorDiv = $('#loanerror');

                    if (res.status === 'error') {
                        $input.addClass('is-invalid');
                        $errorDiv.removeClass('text-success').addClass('text-danger');
                        $errorDiv.text(res.message);
                    } else {
                        $input.removeClass('is-invalid');
                        $errorDiv.removeClass('text-danger').addClass('text-success');
                        $errorDiv.text(res.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        function getLoanType(ele) {
            var schemesHtml = '';
            var member = $('[name="memberType"]').val();
            var accountNumber = $('[name="accountNumber"]').val();
            let membernumber = $('#accountNumber').val();
            let loanType = $(ele).val();
            // let loanType = $(ele).val();
            let loanId = $(ele).find(':selected').val();
            $.ajax({
                url: "{{ route('getLoanType') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'We are fetching Data.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
                data: {
                    'member': member,
                    'accountNumber': accountNumber,
                    'loanType': loanType,
                    'loanId': loanId
                },
                success: function(data) {
                    if (data.status === 'success') {
                        swal.close();

                        let loans = data.data; // FIXED: correctly accessing data

                        if (loans && loans.interest !== undefined) {
                            $('#loaninterestRatess').val(loans.interest);
                        }

                        $('#schemesContainermaster').css('display', 'none');

                        $('#loanmember').find('[name="processingRates"]').val(loans.processingFee).prop(
                            'readonly', true);
                        $('#loanmember').find('[name="fee"]').val(0).prop('readonly', true);
                        $('#loanmember').find('[name="loanMonth"]').val(loans.months).prop('readonly', true);
                        $('#loanmember').find('[name="loanYear"]').val(loans.years).prop('readonly', true);
                        $('#loanmember').find('[name="installmentType"]').val(loans.insType).prop('readonly',
                            true);
                        $('#loanmember').find('[name="loanInterest"]').val(loans.interest).prop('readonly',
                            true);
                        $('#loanmember').find('[name="defintr"]').val(loans.penaltyInterest).prop('readonly',
                            true);
                        $('#loanmember').find('[name="loan_app_fee"]').val(loans.loan_app_charges).prop(
                            'readonly', true);
                    }
                }
            });
        }

        function checkPernoteNo(ele) {
            let PernoteNo = $(ele).val();

            $.ajax({
                url: "{{ route('checkPernoteNo') }}",
                type: 'POST',
                data: {
                    PernoteNo: PernoteNo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    let $input = $('#pernote');
                    let $errorDiv = $('#pernoterror');

                    if (res.status === 'error') {
                        $input.addClass('is-invalid');
                        $errorDiv.removeClass('text-success').addClass('text-danger');
                        $errorDiv.text(res.message);
                    } else {
                        $input.removeClass('is-invalid');
                        $errorDiv.removeClass('text-danger').addClass('text-success');
                        $errorDiv.text(res.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        function loanby(ele) {
            var type = $(ele).val();
            if (type == 'Transfer') {
                $(".bank").css("display", "block");
                $(".savingaccountdiv").css("display", "none");
            } else {
                $(".savingaccountdiv").css("display", "none");
                $(".bank").css("display", "none");
            }
        }
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
                url: "{{ route('grantordetails') }}",
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

        function getaccountdetails(account, member) {
            var txndate = $('[name="loanDate"]').val();
            let membernumber = $('#accountNumber').val();

            $.ajax({
                url: "{{ route('getloanDetail') }}",
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
                            $('#loanmember').find('input[name="loanid"]').val(data.txnacdetails[0].id);
                            $('#loanmember').find('input[name="guranter1"]').val(data.txnacdetails[0]
                                .guranter1);
                            $('#loanmember').find('input[name="guranter2"]').val(data.txnacdetails[0]
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
                                    <a href="javascript:void(0);" onclick="rowClicked('` + val.id + `')">
                                        <i class="fa-solid fa-pen-to-square border-0"></i>
                                    </a>
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

        $(document).ready(function() {
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
                    error.insertAfter(element);
                    error.addClass('text-danger');
                },
            });

            $(document).on('submit', '#loanmember', function(event) {
                event.preventDefault();

                if ($(this).valid()) {
                    let formData = new FormData($('#loanmember')[0]);
                    let url = $('#loanId').val() ? "{{ route('updateloanadvancement') }}" :
                        "{{ route('insertloanadvancement') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        contentType: false, // This should be false for FormData
                        processData: false,
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
        });

        function rowClicked(id) {
            blockForm('#loanmember');

            $.ajax({
                url: "{{ route('loandata') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'actiontype': "loandata",
                    'id': id,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        const documents = response.loandocuments;

                        // 🟢 Fill form fields
                        $('#loanmember').find('input[name="id"]').val(id);
                        $('#loanmember').find('input[name="loanId"]').val(id);
                        $('#guarantorform').find('input[name="loanid"]').val(id);
                        $('#loanmember').find('input[name="accountNumber"]').val(data.accountNo);
                        $('#loanmember').find('input[name="processingRates"]').val(data.processingRates);
                        $('#loanmember').find('input[name="loanYear"]').val(data.loanYear);
                        $('#loanmember').find('input[name="loanMonth"]').val(data.loanMonth);
                        $('#loanmember').find('input[name="loanInterest"]').val(data.loanInterest);
                        $('#loanmember').find('input[name="defintr"]').val(data.loanPanelty);
                        $('#loanmember').find('input[name="guranter1"]').val(data.guranter1);
                        $('#loanmember').find('input[name="guranter2"]').val(data.guranter2);
                        $('#loanmember').find('input[name="actiontype"]').val('actiontypeupdate');
                        $('#loanmember').find('input[name="loanDate"]').val(formatDate(data.loanDate));
                        $('#loanmember').find('input[name="loanAcNo"]').val(data.loanAcNo);
                        $('#loanmember').find('select[name="purpose"]').val(data.purpose).trigger('change');
                        $('#loanmember').find('select[name="loanType"]').val(data.loanType).trigger('change');
                        $('#loanmember').find('input[name="loan_app_fee"]').val(data.loan_app_charges);
                        $('#loanmember').find('input[name="amount"]').val(data.loanAmount);
                        $('#loanmember').find('input[name="pernote"]').val(data.pernote);
                        $('#loanmember').find('input[name="bankDeduction"]').val(data.bankDeduction);
                        $('#loanmember').find('select[name="loanBy"]').val(data.cropType).trigger('change');

                        // 🟢 Preview single image files (if present)
                        if (data.notice_for_installment) {
                            let imgUrl = "{{ asset('/public/uploads/loans') }}/" + data.notice_for_installment;
                            $('#notice_for_installment_img').attr('src', imgUrl).show();
                            $('#download_notice_for_installment_img').attr('href', imgUrl);
                        } else {
                            $('#notice_for_installment_img').hide();
                            $('#download_notice_for_installment_img').attr('href', '');
                        }

                        if (data.notice_for_election) {
                            let imgUrl = "{{ asset('/public/uploads/loans') }}/" + data.notice_for_election;
                            $('#notice_for_election_img').attr('src', imgUrl).show();
                            $('#download_notice_for_election_img').attr('href', imgUrl);
                        } else {
                            $('#notice_for_election_img').hide();
                            $('#download_notice_for_election_img').attr('href', '');
                        }

                        // 🟢 Populate document rows
                        $('#documentsContainer').empty(); // clear old

                        documents.forEach(function(doc) {
                            let docImgUrl = "{{ asset('/public/uploads/loans') }}/" + doc.document_img;

                            let row = `
                        <div class="row document-row">
                            <div class="mb-3 col-md-3 col-sm-12">
                                <label class="form-label mb-1">Document Name</label>
                                <input type="text" name="guranter1name[]" value="${doc.document_name}"
                                    class="form-control form-control-sm" placeholder="Guarantor Name" required />
                            </div>

                            <div class="mb-3 col-md-3 col-sm-12">
                                <label class="form-label mb-1">Documents</label>
                                <input type="file" name="documents[]" class="form-control form-control-sm doc-input" />
                                <a class="download-link" href="${docImgUrl}" download>
                                    <img class="docimg" src="${docImgUrl}" width="100" style="display: block; margin-top: 10px;" />
                                </a>
                            </div>

                            <div class="mb-3 col-md-2 col-sm-12 align-self-end">
                                <button type="button" class="btn btn-success btn-sm add-row">Add</button>
                                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                            </div>
                        </div>
                    `;

                            $('#documentsContainer').append(row);
                        });

                        bindDocumentRowEvents();
                        $("#loanmember").unblock();
                    }
                }
            });
        }

        function bindDocumentRowEvents() {
            $('.add-row').off().on('click', function() {
                let clone = $(this).closest('.document-row').clone();
                clone.find('input[type="text"]').val('');
                clone.find('input[type="file"]').val('');
                clone.find('.docimg').attr('src', '').hide();
                clone.find('.download-link').attr('href', '');
                $('#documentsContainer').append(clone);
            });

            $('.remove-row').off().on('click', function() {
                if ($('.document-row').length > 1) {
                    $(this).closest('.document-row').remove();
                }
            });

            // File input preview binding
            $('.doc-input').off().on('change', function() {
                let input = this;
                let reader = new FileReader();
                reader.onload = function(e) {
                    $(input).siblings('.download-link').find('.docimg')
                        .attr('src', e.target.result)
                        .show();
                };
                if (input.files[0]) reader.readAsDataURL(input.files[0]);
            });
        }

        function deleteloan(id, loanType) {

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
                            url: "{{ route('deleteloan') }}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: {
                                'actiontype': "deleteloan",
                                'id': id,
                                'loanType': loanType,
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

        function getinstallments() {
            var loanAmount = $('#loanmember').find('[name="amount"]').val();
            var intrest = $('#loanmember').find('[name="loanInterest"]').val();
            var instType = $('#loanmember').find('[name="installmentType"]').val();
            var year = $('#loanmember').find('[name="loanYear"]').val();
            var month = $('#loanmember').find('[name="loanMonth"]').val();
            var loandate = $('#loanmember').find('[name="loanDate"]').val();
            var loanType = $('#loanType').val(); //$('#loanmember').find('[name="loanType"]').val();
            var id = $('input[name="id"]').val();

            $.ajax({
                url: "{{ route('getInstallmets') }}",
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
                    "id": id,
                },
                success: function(data) {
                    let installments = data.installments;
                    let loan = data.loan;
                    // alert(loan.id);
                    if (id) {

                        if (loan) {
                            let loanDate = new Date(loan.loanDate);
                            let dayss = loanDate.getDate();
                            let month = loanDate.getMonth() + 1;
                            let year = loanDate.getFullYear();

                            dayss = dayss < 10 ? `0${dayss}` : dayss;
                            month = month < 10 ? `0${month}` : month;
                            let formattedDates = `${dayss}-${month}-${year}`;

                            let row = `<tr>
                                    <td></td>
                                    <td>${formattedDates}</td>
                                    <td>${loan.loanAmount}</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>${loan.loanAmount}</td>
                                    <td>${loan.loanAmount}</td>
                                </tr>`;
                            $('#modalLong').find('.installmentsdata').append(row);
                        }

                        // let balance = loan.loanAmount;

                        if (installments.schedule && Array.isArray(installments.schedule) && installments
                            .schedule.length > 0) {
                            installments.schedule.forEach((item, index) => {
                                // Format date to dd-mm-yyyy
                                let installmentDate = new Date(item.emi_date);
                                let day = installmentDate.getDate();
                                let month = installmentDate.getMonth() + 1;
                                let year = installmentDate.getFullYear();

                                day = day < 10 ? `0${day}` : day;
                                month = month < 10 ? `0${month}` : month;
                                let formattedDate = `${day}-${month}-${year}`;
                                // Create row
                                let row = `<tr>
                                    <td>${index + 1}</td>
                                    <td>${formattedDate}</td>
                                    <td>${Math.round(item.balance)}</td>
                                    <td>${Math.round(item.principal)}</td>
                                    <td>${Math.round(item.interest)}</td>
                                    <td>${Math.round(item.emi)}</td>
                                    <td>${Math.round(item.balance)}</td>
                                </tr>`;


                                $('#modalLong').find('.installmentsdata').append(row);
                            });
                        }


                    } else {
                        $('#modalLong').find('.installmentsdata').html(data);
                    }
                }
            });
            $('#modalLong').modal('show');
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
