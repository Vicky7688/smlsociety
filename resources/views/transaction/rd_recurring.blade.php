@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between">
                <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                    <h4 class=""><span class="text-muted fw-light">Transactions / </span>Recurring Deposit</h4>
                </div>
                <div class="col-md-3 accountHolderDetails">
                    <h6 class=""><span class="text-muted fw-light">Name: </span><span id="member_name"></span></h6>
                    {{--  <h6 class="pt-2"><span class="text-muted fw-light">Father Name: </span><span id="member_fathername"></span></h6>  --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body cardHeadingTitle">
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-tabs rdCustom" id="myTabs" role="tablist">
                                <li class="col-md-3 nav-item" role="presentation">
                                    <a class="nav-link active" id="rd-details-tab" data-bs-toggle="tab"
                                        href="#rd_details" role="tab" aria-controls="rd_details"
                                        aria-selected="true">
                                        RD Details
                                    </a>
                                </li>
                                <li class="col-md-3 nav-item" role="presentation">
                                    <a class="nav-link" id="nominee-details-tab" data-bs-toggle="tab"
                                        href="#nominee-details" role="tab" aria-controls="nominee-details"
                                        aria-selected="false">
                                        Nominee Details
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content tableContent mt-2" id="myTabsContent">
                                <div class="tab-pane fade show active" id="rd_details" role="tabpanel"
                                    aria-labelledby="rd-details-tab">
                                    <!-- Content for Account Details tab -->
                                    <form id="rdAccountForm">
                                        <div class="rd_details-modern">
                                            <div class="rd_details_inner">
                                                <div class="row">
                                                    <input type="text" hidden name="account_number" id="account_number">
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3saving_column">
                                                        <label class="form-label" for="opening date">DATE</label>
                                                        <input type="text" id="rd_opening_date" name="rd_opening_date"
                                                        value="{{ Session::get('currentdate') }}" class="form-control form-control-sm transactionDate" required>
                                                    </div>

                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                        <label class="form-label" for="Member Type">MEMBER TYPE</label>
                                                        <select name="member_type" id="member_type" class="form-select form-select-sm">
                                                            <option value="Member">Member</option>
                                                            <option value="Staff">Staff</option>
                                                            <option value="NonMember">Nominal Member</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                                        <label class="form-label" for="account_no_label">Account No.</label>
<input type="text" id="rd_account_no" oninput="getsavingacclist('this')" name="rd_account_no" class="form-control form-control-sm" autocomplete="off">
                                                        <div id="accountList" class="accountList"></div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_amount_label">Installment Amount</label>
                                                        <input type="text" id="rd_account_amount" name="rd_account_amount" class="form-control form-control-sm" oninput="interestMaturityCalculation('this')" placeholder="0.00">
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_interest_label">INTEREST</label>
                                                        <input type="text" id="rd_account_interest" name="rd_account_interest" class="form-control form-control-sm" oninput="interestMaturityCalculation('this')" placeholder="0.00">
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_month_label">MONTH</label>
                                                        <input type="text" id="rd_account_month" name="rd_account_month" oninput="interestMaturityCalculation('this')" class="form-control form-control-sm rd-input-cal">
                                                    </div>
                                                <!-- </div> -->

                                                <!-- <div class="row row-gap-2"> -->
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_paid_interest_label">PAID INTEREST</label>
                                                        <input type="text" id="rd_account_paid_interest" value="0" name="rd_account_paid_interest" class="form-control form-control-sm" placeholder="0.00">
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_maturity_date_label">MATURITY DATE</label>
                                                        <input type="text" id="rd_account_maturity_date" name="rd_account_maturity_date" oninput="interestMaturityCalculation('this')" class="form-control formInputs" placeholder="DD-MM-YYYY" readonly>
                                                    </div>
                                                    <!-- </div> -->
                                                    <!-- <div class="row row-gap-2"> -->
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3  saving_column hidefileds">
                                                            <label class="form-label" for="rd_account_maturity_amount_label">MATURITY AMOUNT</label>
                                                            <input type="text" id="rd_account_maturity_amount" name="rd_account_maturity_amount" oninput="interestMaturityCalculation('this')" class="form-control form-control-sm" placeholder="0.00" readonly>
                                                        </div>
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3  saving_column hidefileds">
                                                            <label class="form-label" for="rd_account_rd_ac_no_label">Membership No</label>
                                                            <input type="text" id="membership_no" readonly name="membership_no" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 hidefileds">
                                                            <label class="form-label" for="rd_account_lf_no_label">L/F NO</label>
                                                            <input type="text" id="rd_account_lf_no" name="rd_account_lf_no" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                            <label class="form-label" for="rd_account_page_no_label">PAGE NO</label>
                                                            <input type="text" id="rd_account_page_no" name="rd_account_page_no" class="form-control form-control-sm">
                                                        </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column hidefileds">
                                                        <label class="form-label" for="rd_account_agent_label">AGENT</label>
                                                        <select name="rd_account_agent" id="rd_account_agent" class="form-select form-select-sm">
                                                            <option value="">Please Select</option>
                                                            @foreach($agents as $agent)
                                                            <option value="{{$agent->id}}">{{$agent->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                     <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column pt-3 hidefileds" id="submitbtns">
                                                        {{--  <button type="button" id="modifyupdatebtn" class="btn btn-primary btn-sm px-4 d-none">Update</button>  --}}
                                                        <button type="submit" class="btn btn-primary btn-sm px-4">Save</button>
                                                        {{--  <button class="btn btn-danger px-4 btn-sm" id="recurring_details_form_clear">clear</button>  --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="nominee-details" role="tabpanel" aria-labelledby="nominee-details-tab">
                                    <!-- Content for Address Details tab -->
                                    <form action="javascript:void(0)" id="nomineeaddressdetailsform">
                                        <div class="row pt-2">
                                        <h5>Nominee 1</h5>
                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_details">NAME</label>
                                                <input type="text" id="nominee_name" name="nominee_name" class="form-control form-control-sm" placeholder="Nominee Name">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_relation">RELATION</label>
                                                <input type="text" id="nominee_relation" name="nominee_relation" class="form-control form-control-sm" placeholder="Relation">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_contact">CONTACT No</label>
                                                <input type="text" id="nominee_contact" name="nominee_contact" class="form-control form-control-sm" placeholder="Contact No.">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="share">Share</label>
                                                <input type="text" id="nominee_share" name="nominee_share" class="form-control form-control-sm" placeholder="Share">
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_address">ADDRESS</label>
                                                <input type="text" id="nominee_contact" name="nominee_contact" class="form-control form-control-sm" placeholder="Address">
                                            </div>
                                        </div>

                                        <div class="row pt-2">
                                            <h5>Nominee 2</h5>
                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_details">NAME</label>
                                                <input type="text" id="nominee_name" name="nominee_name" class="form-control formInputs" placeholder="Nominee Name">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_relation">RELATION</label>
                                                <input type="text" id="nominee_relation" name="nominee_relation" class="form-control formInputs placeholder="Relation"">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_contact">CONTACT No</label>
                                                <input type="text" id="nominee_contact" name="nominee_contact" class="form-control formInputs" placeholder="Contact No.">
                                            </div>

                                            <div class="col-md-2 col-sm-3 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="share">Share</label>
                                                <input type="text" id="nominee_share" name="nominee_share" class="form-control formInputs" placeholder="Share">
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-6 py-2 inputesPadding">
                                                <label class="form-label" for="nominee_address">ADDRESS</label>
                                                <input type="text" id="nominee_contact" name="nominee_contact" class="form-control formInputs" placeholder="Address">
                                            </div>
                                        </div>

                                        <div class="button justify-content-end text-end pt-1">
                                            <button type="submit" class="btn btn-primary px-4">save</button>
                                            <button class="btn btn-danger px-4">clear</button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4" id="rd_amount_div" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <h3>Amount: <span id="rd_amount_value"></span> </h3>
                    <h3 hidden>Month: <span id="rd_months"></span> </h3>

                    <form action="javascript:void(0)" id="Rd_Installments_receive_form" autocomplete="off">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="" class="form-label">Date</label>
                                <input type="text" id="deposit_opening_date" name="deposit_opening_date" value="{{ Session::get('currentdate') }}" class="form-control form-control-sm transactionDate" required>
                                <input type="hidden" name="rdid" id="rdid">
                                <input type="hidden" name="edit_rd_account" id="edit_rd_account">
                                <input type="hidden" name="rd_account" id="rd_account">


                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="">PAYMENT TYPE</label>
                                <select name="payment_type" id="payment_type" onchange="getledgerCode('this')"  class="form-select form-select-sm">
                                    @if(!empty($groups))
                                        <option value="" selected>Select Group</option>
                                        @foreach ($groups as $row)
                                            <option value="{{ $row->groupCode }}">{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="">PAYMENT BANK</label>
                                <select name="payment_bank" id="payment_bank" class="form-select form-select-sm">
                                    <option value="">Select Group</option>
                                </select>
                                 {{--  <input type="hidden" id="paymentbank_id">  --}}
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 saving_column">
                                <label class="form-label" for="rd_account_agent_label">AGENT</label>
                                <select name="agent_id" id="agent_id" class="form-select form-select-sm">
                                    <option value="">Please Select</option>
                                    @foreach($agents as $agent)
                                    <option value="{{$agent->id}}">{{$agent->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="" class="form-label">AMOUNT</label>
                                <input type="text" id="deposit_amount" name="deposit_amount" onblur="amountCheckNotExceedAndMultiple(this)" class="form-control form-control-sm" placeholder="AMOUNT">
                                <input type="hidden" id="rd_account_row">
                            </div>
                            <div class="col-md-2">
                                <label for="" class="form-label">PENALTY</label>
                                <input type="text" id="deposit_penalty" value= "0" name="deposit_penalty" class="form-control form-control-sm" placeholder="PENALTY" >
                                <input type="hidden" id="rd_amount_account">
                            </div>
                            <div class="col-md-2 pt-3">
                                <button type="submit" id="receive_submit_btn" class="btn btn-primary btn-sm">Receive</button>
                                <button type="button" id="receiptupdatebtn" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="rd_table_id">
            <div class="tabledata card tablee">
                <div class="card-body">
                    <table class="table datatables-order table-bordered" id="maintable" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Start Date</th>
                                <th>Month(s)</th>
                                <th>ROI %</th>
                                <th>Intallment</th>
                                <th>Received Amt.</th>
                                <th>Penality</th>
                                <th>Maturity Amount</th>
                                <th>Maturity Date</th>
                                <th>Status</th>
                                <th>Receipts</th>
                                <th>Mature</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rd_tbody_list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ReciptModaldata" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel">Paid Installment List</h5>

            </div>
            <div class="modal-body">
                <!-- Table to be displayed in the modal -->
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Receipt Date</th>
                            <th scope="col">Received Amount</th>
                            <th scope="col">Penalty</th>
                            <th colspan="2">Action</th>
                            <th scope="col">Entered By</th>
                        </tr>
                    </thead>
                    <tbody id="reciept_sheet_data">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success installmentModal" id="installmentModal" data-dismiss="modal">View Installments</button>
                <button type="button" class="btn btn-danger" id="modelclosebtn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewInstallmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel">Paid Installment List</h5>

            </div>
            <div class="modal-body">
                <!-- Table to be displayed in the modal -->
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Receipt Date</th>
                            <th scope="col">Received Amount</th>
                            <th scope="col">No. Of Installment</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody id="view_installment_modal">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="modelclosebtn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="MatureModaldata" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2">RD MATURE</h5>
                <input type="hidden" id="updaterdnumber">
            </div>
        <div class="row">
            <form action="javascript:void(0)" id="rdMatureForm">
                <div class="modal-body row-gap-3" style="display: flex; flex-wrap: wrap">
                     <input type="hidden" name="mature_account_no" id="mature_account_no">
                     <input type="hidden" name="account_id" id="account_id">
                     <input type="hidden" name="rdId" id="rdId">

                      <div class="form-group col-sm-4 px-2">
                        <label for="ACTUAL MATURITY DATE" class="form-label">DATE</label>
                        <input type="text" class="form-control formInputs transactionDate" id="rd_mature_date" name="rd_mature_date" value="{{ Session::get('currentdate') }}">
                    </div>

                    <div class="form-group col-sm-4 px-2">
                        <label for="AMOUNT RECEIVED" class="form-label">AMOUNT RECEIVED</label>
                        <input type="text" class="form-control formInputs" oninput="checkbalancemature('this')" id="rd_mature_amount_receive" name="rd_mature_amount_receive" readonly>
                    </div>

                    <div class="form-group col-sm-4 px-2">
                        <label for="RD INTEREST" class="form-label">RD INTEREST</label>
                        <input type="text" class="form-control formInputs totalrdcal" oninput="checkbalancemature('this')" id="rd_mature_actual_interest" name="rd_mature_actual_interest" value="0">
                    </div>

                    <div class="form-group col-sm-4 px-2">
                        <label for="RD PENALITY" class="form-label">RD PENALITY</label>
                        <input type="text" class="form-control formInputs totalrdcal" oninput="checkbalancemature('this')" id="rd_mature_actual_penality_value" name="rd_mature_actual_penality_value" value="0">
                    </div>

                    <div class="form-group col-sm-4 px-2">
                        <label for="TOTAL RD" class="form-label">Payable  RD</label>
                        <input type="text" class="form-control formInputs" oninput="checkbalancemature('this')" id="rdtotalnewamount" name="rdtotalnewamount" readonly>
                    </div>

                    @php
                        $cash_groups = \App\Models\GroupMaster::where('groupCode','C002')->first();
                    @endphp

                    <div class="form-group col-sm-4 px-2">
                        <label for="PAYMENT" class="form-label">PAYMENT</label>
                        <select name="payment_type" id="payment_type" onchange="maturitydata(this.value)" class="form-select formInputs">
                            <option value="" selected>Select Payment</option>
                            @if(!empty($cash_groups))
                                <option value="{{ $cash_groups->groupCode }}">{{ $cash_groups->headName }}</option>
                            @endif
                            <option value="TRASFER">TRASFER</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-4 px-2" id="ledgerdiv" style="display:none;">
                        <label for="PAYMENT" class="form-label">Ledger Code</label>
                        <select name="ledgercodess" id="ledgercodess" class="form-select formInputs">

                        </select>
                    </div>

                    <div class="form-group col-sm-4 px-2" id="savingaccountdiv">
                        <label for="PAYMENT" class="form-label">Saving Account</label>
                        <input type="text" class="form-control formInputs" id="saving" name="saving" readonly>
                    </div>

                </div>

                <div class="modal-footer mt-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" id="rdmatureclosebtn">Close</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationUnmatureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="UnmatureForm" id="UnmatureForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Confirmation</h5>
                    <input type="hidden" name="unmatureid" id="unmatureid">
                </div>
                <div class="modal-body">
                    Are you sure you want to unmature?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary confirmationUnmatureModal" id="No">No</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection



@push('script')
<script>
    //___________Legder's Behalf Of Group
    function getledgerCode() {
        let groups_code = $('#payment_type').val();

        $.ajax({
            url : "{{ route('getcashbankledgers') }}",
            type : 'post',
            data : { groups_code: groups_code },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType : 'json',
            success : function(res) {
                let ledgerDropdown = document.getElementById('payment_bank');
                ledgerDropdown.innerHTML = '';

                {{--  let defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select Group';
                ledgerDropdown.appendChild(defaultOption);  --}}

                if(res.status === 'success' && res.ledgers) {
                    let ledgers = res.ledgers;

                    ledgers.forEach((data) => {
                        let option = document.createElement('option');
                        option.value = data.ledgerCode;
                        option.textContent = data.name;
                        ledgerDropdown.appendChild(option);
                    });
                } else {
                    notify('No ledgers found for the selected group.','warning');
                }
            },
            error: function() {
                notify('An error occurred while fetching ledgers.','warning');
            }
        });
    }

    //__________Get Saving Account List
    function getsavingacclist(){
        let account_no = $('#rd_account_no').val();
        let memberType = $('#member_type').val();

        $.ajax({
            url : "{{ route('getrdacclist') }}",
            type : 'post',
            data : {account_no : account_no , memberType : memberType},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){
                    let accounts = res.accounts;
                    let accountList = $('#accountList');
                    accountList.empty();

                    if(accounts){
                        accounts.forEach((data) => {
                            accountList.append(`<div class="accountLists" data-id="${data.accountNo}">${data.accountNo}</div>`);
                        });
                    }
                }else{
                    notify(res.messages,'warning');;
                }
            }
        });
    }


    function ShowDataTable(previous_balance,rd_entries,rd_account,rd_accountss){
        let tableBody = $('#tableBody');
        tableBody.empty();
        $('#member_name').empty();

        //________Show Customer Name
        $('#member_name').append(rd_account.customer_name);
    }


    function amountCheckNotExceedAndMultiple() {
        let enteredAmount = parseFloat(document.getElementById('deposit_amount').value);
        let rd_amount = parseFloat($('#rd_amount_value').text().replace(/[^0-9.-]+/g, ""));
        let month = parseFloat($('#rd_months').text().replace(/[^0-9.-]+/g, ""));

        // Ensure enteredAmount is provided and greater than zero
        if (isNaN(enteredAmount) || enteredAmount <= 0) {
            notify('Please enter a valid deposit amount greater than zero.','warning');
            return;
        }

        // Ensure all other values are valid numbers
        if (isNaN(rd_amount) || isNaN(month)) {
            notify('Invalid RD amount or month value.','warning');
            return;
        }

        let received_amount = enteredAmount;
        let principal = rd_amount * month;

        // Check if entered amount is a multiple of rd_amount
      if (enteredAmount % rd_amount  !== 0) {
            document.getElementById('deposit_amount').value = '';
            notify(`The entered amount (${enteredAmount}) must be a multiple of ${rd_amount}.`,'warning');
            return;
        }

        // Check if received_amount exceeds the principal
         if (received_amount > principal) {
            document.getElementById('deposit_amount').value = '';
            notify('Entered amount exceeds the allowed principal.','warning');
            return;
        }

        notify('Amount is valid.','success');
    }


    //__________Interest & Maturity Calculate
    function interestMaturityCalculation() {
        $("#rd_account_maturity_date").val('');
        $("#rd_account_maturity_amount").val('');

        let monthsToAdddate = parseFloat($("#rd_account_month").val(), 0);
        let openingDate = $("#rd_opening_date").val();
        let amount = parseFloat($("#rd_account_amount").val()) || 0;
        let interest = parseFloat($("#rd_account_interest").val()) || 0;

        if (!isNaN(monthsToAdddate) && openingDate && amount && interest) {
            // Date Calculation
            $("#rd_account_maturity_date").val('');
            let dateParts = openingDate.split("-");
            let startDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
            startDate.setMonth(startDate.getMonth() + monthsToAdddate);
            let resultDate = ("0" + startDate.getDate()).slice(-2) + "-" + ("0" + (startDate.getMonth() + 1)).slice(-2) + "-" + startDate.getFullYear();

            $("#rd_account_maturity_date").val(resultDate);

            //_________Maturity Amount Calculation
            let i = 1;
            let quateres_in_year = 4;
            let rate = ((interest) / (100));
            let effect_value = ((Math.pow(1 + (rate / quateres_in_year), quateres_in_year)) - 1) * 100;
            effect_value = effect_value.toFixed(3);

            let month_in_year = 12;
            quateres_in_year = parseInt(month_in_year);

            let eeef = (effect_value / 100) + 1;
            let ennn = 1 / quateres_in_year;
            let epow = Math.pow(eeef, ennn);
            let epow1 = (epow - 1) * 100;
            let nominal = (quateres_in_year * epow1).toFixed(3);

            let rt = nominal / quateres_in_year;
            let rtt = rt.toFixed(3);

            let sumt = amount;
            let summ = i = intval = intvall = 0;

            for (i = 0; i < monthsToAdddate; i++) {
                intval = (sumt * rtt) / 100;
                intvall = intval.toFixed(2);
                summ = parseFloat(sumt) + parseFloat(intvall);
                sumt = summ + amount;
            }

            $("#rd_account_maturity_amount").val(summ.toFixed(2));

        }
    }


    function checkbalancemature() {
        let amount = parseFloat($("#rd_mature_amount_receive").val()) || 0;
        let interest_amount = parseFloat($("#rd_mature_actual_interest").val()) || 0;
        let penalty_amount = parseFloat($("#rd_mature_actual_penality_value").val()) || 0;
        let net_maturity_amount = (amount + interest_amount) - penalty_amount;
        $("#rdtotalnewamount").val(net_maturity_amount);
    }


    function meturty(accountNo) {
        var accountNo = accountNo;
        var currentDate = moment().format('DD-MM-YYYY');

        $.ajax({
            url: '{{ route("getrdmaturedata") }}',
            type: 'POST',
            data: { accountNo: accountNo },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(res) {
                if (res.status == "success") {

                    var dateString = new Date();
                    var formattedDate = moment(dateString).format('DD-MM-YYYY');
                    $("#rd_mature_date").val(formattedDate);

                    const amount = res.details.amount;
                    const months = res.details.month;
                    const totalAmount = amount * months;

                    // Calculate months difference
                    let rdmonth = monthdiffs(moment(dateString).format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));

                    let rdIntrest = rdmeturtyCalculation(rdmonth, formattedDate, res.totalpaid, res.details.interest, res.details.amount, months);

                    let maturedate = res.details.maturity_date;
                    let formattedDate2 = moment(maturedate).format('DD-MM-YYYY');
                    let interest = parseInt(rdIntrest) - parseInt(res.totalpaid);
                    if (interest < 0) {
                        interest = 0;
                    }

                    $("#rd_mature_actual_interest").val(interest);
                    $("#rd_mature_amount_receive").val(res.totalpaid);
                    $("#rd_mature_actual_maturity_date").val(currentDate);
                    $("#mature_account_no").val(res.details.rd_account_no);
                    $("#account_id").val(res.details.accountId);
                    $("#rdtotalnewamount").val(parseInt(rdIntrest));
                    $("#rdId").val(res.details.id);

                    $("#MatureModaldata").modal('show');
                } else if (res.status == "fail") {
                    notify("Something went wrong !!", 'warning');
                }
            },
            error: function(error) {
                console.log(error.responseJSON);
                if (error.status === 400) {
                    notify(error.responseJSON.message, 'warning');
                } else {
                    console.error('Error:', error.message);
                }
            }
        });
    }


    function rdmeturtyCalculation(rdmonth, openingDate, amount, interest, installmentAmount, tenure) {
        let VMonth = amount / installmentAmount;
        let i = 1;
        let intval = parseFloat(0);
        let sumt = parseInt(amount);
        let totalint = 0;
        let totalsum = 0;

        for (i = 0; i < VMonth; i++) {
            intval = parseFloat((parseFloat(installmentAmount) * (parseFloat(interest) / 100)) / (parseFloat(tenure)));
            totalint = totalint + intval;
            totalsum += totalint;
            installmentAmount = installmentAmount + intval;
        }

        return Math.round(sumt + totalsum);
    }


    function monthdiffs(date1,date2){
        let startDate = new Date(date1);
        let endDate = new Date(date2);

        // Calculate the difference in months
        let diffInMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12;
            diffInMonths -= startDate.getMonth() + 1;
            diffInMonths += endDate.getMonth();
         //   console.log(diffInMonths,date1,date2) ;
        return diffInMonths ;
    }

    function unmeturty(accountNo){
        $("#unmatureid").val(accountNo);
        $("#confirmationUnmatureModal").modal('show');
    }

    function showData(rd_account,deposit_amount){

        if (rd_account || deposit_amount) {
            $("#rd_tbody_list").html('');
            var currentDate = moment().format('DD-MM-YYYY');
            var datemodify = moment(rd_account.date, 'Y-MM-DD').format('DD-MM-YYYY');
            var tr = $("<tr>");
            tr.append("<td>" + datemodify + "</td>");
            tr.append("<td id='getmonths'>" + rd_account.month + "</td>");
            tr.append("<td>" + rd_account.interest + "</td>");

            var amountCell = $("<td id='rdamount'>").text(rd_account.amount).addClass("clickable-cell").attr("id", "rowid" + rd_account.id);
            if (rd_account.getinstallmentsdata && rd_account.getinstallmentsdata.every(installment => installment.payment_status === "paid")) {
                amountCell.removeClass("clickable-cell").css("color", "red");
            }
            tr.append(amountCell);

            var receivedAmount = 0;
            var penaltyAmount = 0;

            if (deposit_amount && deposit_amount.length > 0) {
                deposit_amount.forEach(function(installment) {
                    receivedAmount += installment.deposit;
                    penaltyAmount += installment.penality;
                });
            }
            var $total = receivedAmount;

            tr.append("<td id='member_received" + rd_account.id + "'>" + $total + "</td>");
            tr.append("<td id='member_received_panality" + rd_account.id + "'>" + penaltyAmount + "</td>");

            if(rd_account.status == "Mature"){
                tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
            } else {
                tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
            }

            var modifymaturity_date = moment(rd_account.actual_maturity_date, 'Y-MM-DD').format('DD-MM-YYYY');
            if(rd_account.status == "Mature"){
                tr.append("<td>" + modifymaturity_date + "</td>");
            } else {
                tr.append("<td>" + moment(rd_account.maturity_date, 'Y-MM-DD').format('DD-MM-YYYY') + "</td>");
            }

            if (rd_account.status == "Active") {
                tr.append("<td><span class='bg-label-success' id='statussss'>" + 'Active' + "</span></td>");
            } else if (rd_account.status == "Mature" || rd_account.status == "PreMature" || rd_account.status == "Pluge") {
                tr.append("<td><span class='badge bg-label-danger' id='statussss' data-id=" + rd_account.accountNo + "> " + rd_account.status + "</span></td>");
            } else if (rd_account.status == "Closed") {
                tr.append("<td><span class='badge bg-label-danger' id='statussss'>" + 'Active' + "</span></td>");
            }


            tr.append("<td><button class='receipts_view btn btn-sm btn-primary btn-sm' data-id=" + rd_account.accountId + ">Receipts</button></td>");

            if(rd_account.status == "Mature" || rd_account.status == "PreMature"){
                tr.append("<td> <button type='button' onclick='unmeturty("+rd_account.accountId+")' class='btn btn-danger btn-sm'>Unmature</button> </td>");
            }else if(rd_account.status == "Pluge"){
                tr.append("<td>  </td>");
            }else {
                tr.append("<td> <button type='button' onclick='meturty("+rd_account.accountId+")' class='btn btn-success btn-sm'>Mature</button></td>");
                if (deposit_amount && deposit_amount.length > 0 && deposit_amount.some(item => item.deposit != null && item.penality != null)) {
                    tr.append("<td></td> ");
                } else {
                    tr.append("<td> <button type='button' data-id='" + rd_account.id + "' class='btn view p-0 dropdown-toggle hide-arrow editrd'><i class='fa-solid fa-pen-to-square'></i></button> <button type='button' data-id='" + rd_account.id + "' class='btn p-0 dropdown-toggle hide-arrow deleteIds'><i class='fa-solid fa-trash'></i></button></td> ");
                }

            }

            $("#rd_tbody_list").append(tr);

            if (rd_account.status == "Mature" || rd_account.status == "PreMature") {
                amountCell.removeClass("clickable-cell").css("color", "red");
                tr.find("td").css("color", "red");
            } else {
                amountCell.click({amount: rd_account.amount,accountId: rd_account.accountId}, function(event) {
                    $("#Rd_Installments_receive_form")[0].reset();
                    var clickedAmount = event.data.amount;
                    var clickedRowId = event.data.accountId;

                    let getmonths = $('#getmonths').text();
                    $('#rd_account').val(clickedRowId);
                    $("#rd_amount_value").text(clickedAmount);
                    $('#rd_months').text(getmonths);
                    $("#rd_amount_account").val(clickedAmount);
                    $("#deposit_opening_date").val(currentDate);
                    $("#rd_account_row").val(clickedRowId);
                    $("#receive_submit_btn").removeClass('d-none');
                    $("#receiptupdatebtn").addClass('d-none');
                    $("#rd_amount_div").show();
                });
            }
        }
    }


    $('#ledgerdiv').hide();
    $('#savingaccountdiv').hide();

    function maturitydata(groupcode) {
        var groups_code = groupcode;
        let rd_account = $("#mature_account_no").val();

        if (groups_code === 'C002') {
            $.ajax({
                url: "{{ route('getcashbankledgers') }}",
                type: 'post',
                data: { groups_code: groups_code },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let ledgers = res.ledgers;
                        if (ledgers && ledgers.length > 0) {
                            ledgers.forEach((data) => {
                                $('#saving').val('');
                                $('#ledgercodess').append(`<option value="${data.ledgerCode}">${data.name}</option>`);
                                $('#ledgerdiv').show();
                                $('#savingaccountdiv').hide();
                            });
                        } else {
                            console.log('Ledger Code not found in response');
                        }
                    } else {
                        console.log('Failed response:', res);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            $.ajax({
                url : "{{ route('getsavingaccountno') }}",
                type : 'post',
                data : {rd_account : rd_account},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let details =  res.details;

                        if(details){
                            $('#saving').val(details.accountNo);
                            $('#ledgercodess').val('');
                            $('#ledgerdiv').hide();
                            $('#savingaccountdiv').show();
                        }
                    }
                }
            });
        }
    }

    $(document).ready(function(){
        //_________Get Single Account Details
        $(document).on('click','.accountLists',function(e){
            e.preventDefault();
            let selectdId = $(this).data('id');
            $('#rd_account_no').val(selectdId);
            $('#accountList').html('');
            let member_type = $('#member_type').val();
            let transactionType = $('#transactionType').val();

            $.ajax({
                url : "{{ route('getrddetails') }}",
                type : 'post',
                data : {selectdId : selectdId,transactionType:transactionType,member_type:member_type},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let previous_balance = res.previous_balance;
                        let rd_account = res.rd_accounts;
                        let deposit_amount = res.deposit_amount;
                        let opening_account = res.opening_account;


                        $('#membership_no').val(opening_account.membershipno);
                        $('#rd_account_interest').val(opening_account.roi);
                        $('#member_name').text(opening_account.customer_name);
                        $('#rd_account_month').val(opening_account.schmonth);

                        let transactionDate = new Date(opening_account.transactionDate);
                        let day = transactionDate.getDate();
                        let month = transactionDate.getMonth() + 1;
                        let year = transactionDate.getFullYear();

                        day = day < 10 ? `0${day}` : day;
                        month = month < 10 ? `0${month}` : month;
                        let formattedDate = `${day}-${month}-${year}`;

                        $('#rd_opening_date').val(formattedDate);

                        if (rd_account && rd_account.amount > 0) {
                            $('.hidefileds').hide();

                            // Clear the values of the hidden fields
                            $('#rd_account_amount').val('');
                            $('#rd_account_interest').val('');
                            $('#rd_account_month').val('');
                            $('#rd_account_paid_interest').val('');
                            $('#rd_account_maturity_date').val('');
                            $('#membership_no').val('');
                            $('#rd_account_lf_no').val('');
                            $('#rd_account_page_no').val('');
                            $('#rd_account_agent').val('');
                            $('#rd_account_maturity_amount').val('');
                        } else {
                            $('.hidefileds').show();
                        }

                        if (rd_account || deposit_amount) {
                            $("#rd_tbody_list").html('');
                            var currentDate = moment().format('DD-MM-YYYY');
                            var datemodify = moment(rd_account.date, 'Y-MM-DD').format('DD-MM-YYYY');
                            var tr = $("<tr>");
                            tr.append("<td>" + datemodify + "</td>");
                            tr.append("<td id='getmonths'>" + rd_account.month + "</td>");
                            tr.append("<td>" + rd_account.interest + "</td>");

                            var amountCell = $("<td id='rdamount'>").text(rd_account.amount).addClass("clickable-cell").attr("id", "rowid" + rd_account.id);
                            if (rd_account.getinstallmentsdata && rd_account.getinstallmentsdata.every(installment => installment.payment_status === "paid")) {
                                amountCell.removeClass("clickable-cell").css("color", "red");
                            }
                            tr.append(amountCell);

                            var receivedAmount = 0;
                            var penaltyAmount = 0;

                            if (deposit_amount && deposit_amount.length > 0) {
                                deposit_amount.forEach(function(installment) {
                                    receivedAmount += installment.deposit;
                                    penaltyAmount += installment.penality;
                                });
                            }
                            var $total = receivedAmount;

                            tr.append("<td id='member_received" + rd_account.id + "'>" + $total + "</td>");
                            tr.append("<td id='member_received_panality" + rd_account.id + "'>" + penaltyAmount + "</td>");

                            if(rd_account.status == "Mature"){
                                tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                            } else {
                                tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                            }

                            var modifymaturity_date = moment(rd_account.actual_maturity_date, 'Y-MM-DD').format('DD-MM-YYYY');
                            if(rd_account.status == "Mature"){
                                tr.append("<td>" + modifymaturity_date + "</td>");
                            } else {
                                tr.append("<td>" + modifymaturity_date + "</td>");
                            }

                            if (rd_account.status == "Active") {
                                tr.append("<td><span class='bg-label-success' id='statussss'>" + 'Active' + "</span></td>");
                            } else if (rd_account.status == "Mature" || rd_account.status == "PreMature" || rd_account.status == "Pluge") {
                                tr.append("<td><span class='badge bg-label-danger' id='statussss' data-id=" + rd_account.accountNo + "> " + rd_account.status + "</span></td>");
                            } else if (rd_account.status == "Closed") {
                                tr.append("<td><span class='badge bg-label-danger' id='statussss'>" + 'Active' + "</span></td>");
                            }


                            tr.append("<td><button class='receipts_view btn btn-sm btn-primary btn-sm' data-id=" + rd_account.accountId + ">Receipts</button></td>");

                            if(rd_account.status == "Mature" || rd_account.status == "PreMature"){
                                tr.append("<td> <button type='button' onclick='unmeturty("+rd_account.accountId+")' class='btn btn-danger btn-sm'>Unmature</button> </td>");
                            }else if(rd_account.status == "Pluge"){
                                tr.append("<td>  </td>");
                            }else {
                                tr.append("<td> <button type='button' onclick='meturty("+rd_account.accountId+")' class='btn btn-success btn-sm'>Mature</button></td>");
                                if (deposit_amount && deposit_amount.length > 0 && deposit_amount.some(item => item.deposit != null && item.penality != null)) {
                                    tr.append("<td></td> ");
                                } else {
                                    tr.append("<td> <button type='button' data-id='" + rd_account.id + "' class='btn view p-0 dropdown-toggle hide-arrow editrd'><i class='fa-solid fa-pen-to-square'></i></button> <button type='button' data-id='" + rd_account.id + "' class='btn p-0 dropdown-toggle hide-arrow deleteIds'><i class='fa-solid fa-trash'></i></button></td> ");
                                }

                            }

                            $("#rd_tbody_list").append(tr);

                            if (rd_account.status == "Mature" || rd_account.status == "PreMature") {
                                amountCell.removeClass("clickable-cell").css("color", "red");
                                tr.find("td").css("color", "red");
                            } else {
                                amountCell.click({amount: rd_account.amount,accountId: rd_account.accountId}, function(event) {
                                    $("#Rd_Installments_receive_form")[0].reset();
                                    var clickedAmount = event.data.amount;
                                    var clickedRowId = event.data.accountId;

                                    let getmonths = $('#getmonths').text();
                                    $('#rd_account').val(clickedRowId);
                                    $("#rd_amount_value").text(clickedAmount);
                                    $('#rd_months').text(getmonths);
                                    $("#rd_amount_account").val(clickedAmount);
                                    $("#deposit_opening_date").val(currentDate);
                                    $("#rd_account_row").val(clickedRowId);
                                    $("#receive_submit_btn").removeClass('d-none');
                                    $("#receiptupdatebtn").addClass('d-none');
                                    $("#rd_amount_div").show();
                                });
                            }

                            $("#rd_table_id").show();
                        }
                    } else {
                        notify(res.messages,'warning');;
                    }
                }
            });
        });


        $("#rdAccountForm").validate({
            rules: {
                rd_opening_date: {
                    required: true,
                    customDate: true,
                },
                member_type: {
                    required: true,
                },
                rd_account_no: {
                    required: true,
                    digits : true
                },
                rd_account_interest: {
                    required: true,
                    number : true
                },
                rd_account_amount : {
                    required: true,
                    digits : true
                },
                rd_account_month: {
                    required: true,
                    digits : true
                },
                rd_account_maturity_date: {
                    required: true,
                },
                rd_account_maturity_amount: {
                    required: true,
                    number : true
                }
            },
            message: {
                rd_opening_date: {
                    required: "Please enter a date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
                member_type: {
                    required: "Required",
                },
                rd_account_no: {
                    required: "Required",
                    digits : 'Required'
                },
                rd_account_interest: {
                    required: "Required",
                    number : 'Required'
                },
                rd_account_month: {
                    required: "Required",
                    digits : 'Required'
                },
                rd_account_maturity_date: {
                    required: "Required",
                },
                rd_account_maturity_amount: {
                    required: "Required",
                    number : 'Required'
                },
                rd_account_amount : {
                    required: "Required",
                    digits : 'Required'
                },
            },
        });

        $(document).on('submit','#rdAccountForm',function(e){
            e.preventDefault();
            if($(this).valid()){
                let formData = $(this).serialize();
                $('button[tyep=submit]').prop('disabled',true);

                let url = $('#account_number').val() ? "{{ route('rdupdate') }}" : "{{ route('rdinsert') }}";
                $.ajax({
                    url : url,
                    type : 'post',
                    data : formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){
                            $('button[tyep=submit]').prop('disabled',false);
                            $("#rdAccountForm")[0].reset();
                            let rd_account = res.rdaccount;
                            let deposit_amount = res.deposit_amount;
                            let opening_account = res.opening_accounts;


                            if (opening_account.status === 'Closed' || rd_account.amount > 0 ) {
                                $('.hidefileds').hide();

                                // Clear the values of the hidden fields
                                $('#rd_account_amount').val('');
                                $('#rd_account_interest').val('');
                                $('#rd_account_month').val('');
                                $('#rd_account_paid_interest').val('');
                                $('#rd_account_maturity_date').val('');
                                $('#membership_no').val('');
                                $('#rd_account_lf_no').val('');
                                $('#rd_account_page_no').val('');
                                $('#rd_account_agent').val('');
                                $('#rd_account_maturity_amount').val('');
                            } else {
                                $('.hidefileds').show();
                            }

                            if (rd_account || deposit_amount) {
                                $("#rd_tbody_list").html('');
                                var currentDate = moment().format('DD-MM-YYYY');
                                var datemodify = moment(rd_account.date, 'Y-MM-DD').format('DD-MM-YYYY');
                                var tr = $("<tr>");
                                tr.append("<td>" + datemodify + "</td>");
                                tr.append("<td id='getmonths'>" + rd_account.month + "</td>");
                                tr.append("<td>" + rd_account.interest + "</td>");

                                var amountCell = $("<td id='rdamount'>").text(rd_account.amount).addClass("clickable-cell").attr("id", "rowid" + rd_account.id);
                                if (rd_account.getinstallmentsdata && rd_account.getinstallmentsdata.every(installment => installment.payment_status === "paid")) {
                                    amountCell.removeClass("clickable-cell").css("color", "red");
                                }
                                tr.append(amountCell);

                                var receivedAmount = 0;
                                var penaltyAmount = 0;

                                if (deposit_amount && deposit_amount.length > 0) {
                                    deposit_amount.forEach(function(installment) {
                                        receivedAmount += installment.deposit;
                                        penaltyAmount += installment.penality;
                                    });
                                }
                                var $total = receivedAmount;

                                tr.append("<td id='member_received" + rd_account.id + "'>" + $total + "</td>");
                                tr.append("<td id='member_received_panality" + rd_account.id + "'>" + penaltyAmount + "</td>");

                                if(rd_account.status == "Mature"){
                                    tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                                } else {
                                    tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                                }

                                var modifymaturity_date = moment(rd_account.actual_maturity_date, 'Y-MM-DD').format('DD-MM-YYYY');
                                if(rd_account.status == "Mature"){
                                    tr.append("<td>" + modifymaturity_date + "</td>");
                                } else {
                                    tr.append("<td>" + moment(rd_account.maturity_date, 'Y-MM-DD').format('DD-MM-YYYY') + "</td>");
                                }

                                if (rd_account.status == "Active") {
                                    tr.append("<td><span class='bg-label-success' id='statussss'>" + 'Active' + "</span></td>");
                                } else if (rd_account.status == "Mature" || rd_account.status == "PreMature" || rd_account.status == "Pluge") {
                                    tr.append("<td><span class='badge bg-label-danger' id='statussss' data-id=" + rd_account.accountNo + "> " + rd_account.status + "</span></td>");
                                } else if (rd_account.status == "Closed") {
                                    tr.append("<td><span class='badge bg-label-danger' id='statussss'>" + 'Active' + "</span></td>");
                                }


                                tr.append("<td><button class='receipts_view btn btn-sm btn-primary btn-sm' data-id=" + rd_account.accountId + ">Receipts</button></td>");

                                if(rd_account.status == "Mature" || rd_account.status == "PreMature"){
                                    tr.append("<td> <button type='button' onclick='unmeturty("+rd_account.accountId+")' class='btn btn-danger btn-sm'>Unmature</button> </td>");
                                }else if(rd_account.status == "Pluge"){
                                    tr.append("<td>  </td>");
                                }else {
                                    tr.append("<td> <button type='button' onclick='meturty("+rd_account.accountId+")' class='btn btn-success btn-sm'>Mature</button></td>");
                                    if (deposit_amount && deposit_amount.length > 0 && deposit_amount.some(item => item.deposit != null && item.penality != null)) {
                                        tr.append("<td></td> ");
                                    } else {
                                        tr.append("<td> <button type='button' data-id='" + rd_account.id + "' class='btn view p-0 dropdown-toggle hide-arrow editrd'><i class='fa-solid fa-pen-to-square'></i></button> <button type='button' data-id='" + rd_account.id + "' class='btn p-0 dropdown-toggle hide-arrow deleteIds'><i class='fa-solid fa-trash'></i></button></td> ");
                                    }

                                }

                                $("#rd_tbody_list").append(tr);

                                if (rd_account.status == "Mature" || rd_account.status == "PreMature") {
                                    amountCell.removeClass("clickable-cell").css("color", "red");
                                    tr.find("td").css("color", "red");
                                } else {
                                    amountCell.click({amount: rd_account.amount,accountId: rd_account.accountId}, function(event) {
                                        $("#Rd_Installments_receive_form")[0].reset();
                                        var clickedAmount = event.data.amount;
                                        var clickedRowId = event.data.accountId;

                                        let getmonths = $('#getmonths').text();
                                        $('#rd_account').val(clickedRowId);
                                        $("#rd_amount_value").text(clickedAmount);
                                        $('#rd_months').text(getmonths);
                                        $("#rd_amount_account").val(clickedAmount);
                                        $("#deposit_opening_date").val(currentDate);
                                        $("#rd_account_row").val(clickedRowId);
                                        $("#receive_submit_btn").removeClass('d-none');
                                        $("#receiptupdatebtn").addClass('d-none');
                                        $("#rd_amount_div").show();
                                    });
                                }
                                $("#rd_table_id").show();
                            }
                        }else{
                            notify(res.messages,'warning');;
                        }
                    }
                });
            }
        });

        $("#Rd_Installments_receive_form").validate({
            rules: {
                deposit_opening_date: {
                    required: true,
                    customDate: true,
                },
                payment_type: {
                    required: true,
                },
                payment_bank: {
                    required: true,
                },
                deposit_amount: {
                    required: true,
                    digits : true
                },
                deposit_penalty : {
                    digits : true
                }
            },
            message: {
                deposit_opening_date: {
                    required: "Please enter a date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
                payment_type: {
                    required: "Required",
                },
                payment_bank: {
                    required: "Required",
                },
                deposit_amount: {
                    required: "Required",
                    digits : "Required"
                },
                deposit_penalty : {
                    digits : "Required"
                }
            },
        });

        $(document).on('submit','#Rd_Installments_receive_form',function(e){
            e.preventDefault();
            if($(this).valid()){
                let formData = $(this).serialize();
                let url =  $('#edit_rd_account').val() ? "{{ route('rdamountupdatereceive') }}" : "{{ route('rdamountreceive') }}";
                $('button[type=submit]').prop('disabled',true);

                $.ajax({
                    url : url,
                    type : 'post',
                    data : formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'fail'){
                            notify(res.messages,'warning');;
                            $('#Rd_Installments_receive_form')[0].reset();
                            $('#rd_amount_div').hide();
                        }else if(res.status === 'success'){
                            $('button[type=submit]').prop('disabled',false);

                            $("#Rd_Installments_receive_form")[0].reset();
                            $('#rdid').val('');
                            $('#edit_rd_account').val('');
                            $('#rd_account').val('');
                            $('#rd_amount_div').hide();

                            notify(res.messages,'success');
                            let rd_account = res.rd_account;
                            let deposit_amount = res.deposit_amount;

                            if (rd_account || deposit_amount) {
                                $("#rd_tbody_list").html('');
                                var currentDate = moment().format('DD-MM-YYYY');
                                var datemodify = moment(rd_account.date, 'Y-MM-DD').format('DD-MM-YYYY');
                                var tr = $("<tr>");
                                tr.append("<td>" + datemodify + "</td>");
                                tr.append("<td id='getmonths'>" + rd_account.month + "</td>");
                                tr.append("<td>" + rd_account.interest + "</td>");

                                var amountCell = $("<td id='rdamount'>").text(rd_account.amount).addClass("clickable-cell").attr("id", "rowid" + rd_account.id);
                                if (rd_account.getinstallmentsdata && rd_account.getinstallmentsdata.every(installment => installment.payment_status === "paid")) {
                                    amountCell.removeClass("clickable-cell").css("color", "red");
                                }
                                tr.append(amountCell);

                                var receivedAmount = 0;
                                var penaltyAmount = 0;

                                if (deposit_amount && deposit_amount.length > 0) {
                                    deposit_amount.forEach(function(installment) {
                                        receivedAmount += installment.deposit;
                                        penaltyAmount += installment.penality;
                                    });
                                }
                                var $total = receivedAmount;

                                tr.append("<td id='member_received" + rd_account.id + "'>" + $total + "</td>");
                                tr.append("<td id='member_received_panality" + rd_account.id + "'>" + penaltyAmount + "</td>");

                                if(rd_account.status == "Mature"){
                                    tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                                } else {
                                    tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");
                                }

                                var modifymaturity_date = moment(rd_account.actual_maturity_date, 'Y-MM-DD').format('DD-MM-YYYY');
                                if(rd_account.status == "Mature"){
                                    tr.append("<td>" + modifymaturity_date + "</td>");
                                } else {
                                    tr.append("<td>" + moment(rd_account.maturity_date, 'Y-MM-DD').format('DD-MM-YYYY') + "</td>");
                                }

                                if (rd_account.status == "Active") {
                                        tr.append("<td><span class='bg-label-success' id='statussss'>" + 'Active' + "</span></td>");
                                    } else if (rd_account.status == "Mature" || rd_account.status == "PreMature" || rd_account.status == "Pluge") {
                                        tr.append("<td><span class='badge bg-label-danger' id='statussss' data-id=" + rd_account.accountNo + "> " + rd_account.status + "</span></td>");
                                    } else if (rd_account.status == "Closed") {
                                        tr.append("<td><span class='badge bg-label-danger' id='statussss'>" + 'Active' + "</span></td>");
                                    }


                                    tr.append("<td><button class='receipts_view btn btn-sm btn-primary btn-sm' data-id=" + rd_account.accountId + ">Receipts</button></td>");

                                    if(rd_account.status == "Mature" || rd_account.status == "PreMature"){
                                        tr.append("<td> <button type='button' onclick='unmeturty("+rd_account.accountId+")' class='btn btn-danger btn-sm'>Unmature</button> </td>");
                                    }else if(rd_account.status == "Pluge"){
                                        tr.append("<td>  </td>");
                                    }else {
                                        tr.append("<td> <button type='button' onclick='meturty("+rd_account.accountId+")' class='btn btn-success btn-sm'>Mature</button></td>");
                                        if (deposit_amount && deposit_amount.length > 0 && deposit_amount.some(item => item.deposit != null && item.penality != null)) {
                                            tr.append("<td></td> ");
                                        } else {
                                            tr.append("<td> <button type='button' data-id='" + rd_account.id + "' class='btn view p-0 dropdown-toggle hide-arrow editrd'><i class='fa-solid fa-pen-to-square'></i></button> <button type='button' data-id='" + rd_account.id + "' class='btn p-0 dropdown-toggle hide-arrow deleteIds'><i class='fa-solid fa-trash'></i></button></td> ");
                                        }

                                    }

                                $("#rd_tbody_list").append(tr);

                                if (rd_account.status == "Mature" || rd_account.status == "PreMature") {
                                    amountCell.removeClass("clickable-cell").css("color", "red");
                                    tr.find("td").css("color", "red");
                                } else {
                                    amountCell.click({amount: rd_account.amount,accountId: rd_account.accountId}, function(event) {
                                        $("#Rd_Installments_receive_form")[0].reset();
                                        var clickedAmount = event.data.amount;
                                        var clickedRowId = event.data.accountId;

                                        let getmonths = $('#getmonths').text();
                                        $('#rd_account').val(clickedRowId);
                                        $("#rd_amount_value").text(clickedAmount);
                                        $('#rd_months').text(getmonths);
                                        $("#rd_amount_account").val(clickedAmount);
                                        $("#deposit_opening_date").val(currentDate);
                                        $("#rd_account_row").val(clickedRowId);
                                        $("#receive_submit_btn").removeClass('d-none');
                                        $("#receiptupdatebtn").addClass('d-none');
                                        $("#rd_amount_div").show();
                                    });
                                }
                                $("#rd_table_id").show();
                            }
                        }else{
                            notify(res.messages,'warning');;
                        }
                    }
                });
            }
        });


        $(document).on('click', '.receipts_view', function(event) {
            event.preventDefault();
            let rdaccountnumber = $(this).data('id');

            $.ajax({
                url: "{{ route('getinstallmentsdetails') }}",
                type: 'post',
                data: { rdaccountnumber: rdaccountnumber },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let received_amount = res.received_amount;
                        let rd_accounts = res.rd_accounts;

                        if (received_amount && received_amount.length > 0) {
                            let receipt_modal = $('#reciept_sheet_data');
                            receipt_modal.empty();

                            received_amount.forEach((data) => {
                                let dates = new Date(data.installment_date);
                                let formattedDate = `${String(dates.getDate()).padStart(2, '0')}-${String(dates.getMonth() + 1).padStart(2, '0')}-${dates.getFullYear()}`;

                                let row = `<tr>
                                    <td>${formattedDate}</td>
                                    <td>${data.amount}</td>
                                    <td>${data.panelty}</td>`;

                                //__________Adding the buttons based on conditions
                                if (data.rdstatus === 'PreMature' || data.rdstatus === 'Closed' || data.rdstatus === 'Mature') {
                                    row += `<td></td><td></td>`;
                                } else if (data.status === 'trfdfromsaving') {
                                    row += `<td colspan="2">${data.narration}</td>`;
                                } else {
                                    row += `<td style="width: 4px !important">
                                        <button class="btn editbtninstallment"
                                            data-id="${data.id}"
                                            data-amount="${data.rd_amount}"
                                            data-deposit-amount="${data.amount}"
                                            data-paymentdate="${data.payment_date}"
                                            data-account-no="${data.id}"
                                            data-memberType="${data.memberType}"
                                            data-installmentdate="${data.installment_date}"
                                            data-penality="${data.panelty}"
                                            data-groupcode="${data.groupCode}"
                                            data-ledgercode="${data.ledgerCode}"
                                            data-months="${data.rd_month}"
                                            data-agent="${data.agentid}">
                                            <i class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                        </button>
                                    </td>
                                    <td style="width: 4px !important">
                                        <button class="btn deletebtninstallment" data-id="${data.id}">
                                            <i class="fa-solid fa-trash iconsColorCustom"></i>
                                        </button>
                                    </td>`;
                                }

                                row += `<td>${data.name}</td>`;
                                row += `</tr>`;
                                receipt_modal.append(row);
                            });

                            $('#ReciptModaldata').modal('show');
                        }
                        // Store the account ID for further actions if needed
                        $('#installmentModal').data('account-id', rd_accounts.id);
                    }
                }
            });
        });


        $(document).on('click','.installmentModal', function(event) {
            event.preventDefault();
            let accountId = $(this).data('account-id');
            $.ajax({
                url : "{{ route('viewinstallmentsdetails') }}",
                type : 'post',
                data : {accountId : accountId},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let installments = res.installments;

                        if(installments && installments.length > 0){

                            let InstallmentModal = $('#view_installment_modal');
                            InstallmentModal.empty();

                            installments.forEach((data,index) => {
                                let dates = new Date(data.installment_date);
                                let days = dates.getDate();
                                let months = dates.getMonth() + 1;
                                let year = dates.getFullYear();

                                days = days < 10 ? `0${days}` : `${days}`;
                                months = months < 10 ? `0${months}` : `${months}`;
                                let formattedDate = `${days}-${months}-${year}`;

                                let displayStatus = (data.payment_status === 'paid') ? 'Received' : (data.payment_status === 'pending') ? 'Unpaid' : 'Unknown';

                                let row =`<tr>
                                    <td>${formattedDate}</td>
                                    <td>${data.amount}</td>
                                    <td>${data.intallment_no}</td>
                                    <td>${displayStatus}</td>
                                </tr>`;
                                InstallmentModal.append(row);
                                $('#ViewInstallmentModal').modal('show');
                                $('#ReciptModaldata').modal('hide');
                            });
                        }
                    }
                }
            });
        });

        $(document).on('click','#modelclosebtn',function(event){
            $('#ViewInstallmentModal').modal('hide');
            $('#ReciptModaldata').modal('show');
        });

        $(document).on('click','#modelclosebtn',function(event){
            $('#ReciptModaldata').modal('hide');
        });

        $(document).on('click','#rdmatureclosebtn',function(event){
            $('#MatureModaldata').modal('hide');
        });

        $(document).on('click','.confirmationUnmatureModal',function(event){
            $('#confirmationUnmatureModal').modal('hide');
        });



        //_______Delete Installments
        $(document).on('click', '.deletebtninstallment', function(event) {
            event.preventDefault();

            let id = $(this).data('id');
            $('#ReciptModaldata').modal('hide');

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
                        url: "{{ route('deleteinstallments') }}",
                        type: 'POST',
                        data: { id: id },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: "json",
                        success: function(res) {
                            Swal.close()
                            if(res.status === 'fail'){
                                notify(res.messages,'warning');
                                $('#Rd_Installments_receive_form')[0].reset();
                                $('#rd_amount_div').hide();
                            }else if(res.status === 'success'){

                                $('button[type=submit]').prop('disabled',false);

                                $("#Rd_Installments_receive_form")[0].reset();
                                $('#rdid').val('');
                                $('#edit_rd_account').val('');
                                $('#rd_account').val('');
                                $('#rd_amount_div').hide();

                                {{--  notify(res.messages,'success');;  --}}
                                let rd_account = res.rd_account;
                                let deposit_amount = res.deposit_amount;


                                if (rd_account || deposit_amount) {
                                    // Clear the existing table body
                                    $("#rd_tbody_list").html("");

                                    // Format date
                                    var currentDate = moment().format('DD-MM-YYYY');
                                    var datemodify = moment(rd_account.date, 'Y-MM-DD').format('DD-MM-YYYY');

                                    // Create a new table row
                                    var tr = $("<tr>");
                                    tr.append("<td>" + datemodify + "</td>");
                                    tr.append("<td id='getmonths'>" + rd_account.month + "</td>");
                                    tr.append("<td>" + rd_account.interest + "</td>");

                                    // Amount Cell
                                    var amountCell = $("<td id='rdamount'>").text(rd_account.amount)
                                        .addClass("clickable-cell")
                                        .attr("id", "rowid" + rd_account.id);

                                    if (rd_account.getinstallmentsdata && rd_account.getinstallmentsdata.every(installment => installment.payment_status === "paid")) {
                                        amountCell.removeClass("clickable-cell").css("color", "red");
                                    }
                                    tr.append(amountCell);

                                    // Calculate Received and Penalty Amount
                                    var receivedAmount = deposit_amount.deposit ? deposit_amount.deposit : 0;
                                    var penaltyAmount = deposit_amount.penality ? deposit_amount.penality : 0;
                                    var total = receivedAmount;

                                    // Append the amounts
                                    tr.append("<td id='member_received" + rd_account.id + "'>" + total + "</td>");
                                    tr.append("<td id='member_received_panality" + rd_account.id + "'>" + penaltyAmount + "</td>");

                                    // Maturity Amount
                                    tr.append("<td>" + rd_account.actual_maturity_amount + "</td>");

                                    // Maturity Date
                                    var maturityDate = rd_account.status === "Mature"
                                        ? moment(rd_account.actual_maturity_date, 'Y-MM-DD').format('DD-MM-YYYY')
                                        : moment(rd_account.maturity_date, 'Y-MM-DD').format('DD-MM-YYYY');
                                    tr.append("<td>" + maturityDate + "</td>");

                                    // Status
                                    if (rd_account.status === "Active") {
                                        tr.append("<td><span class='bg-label-success' id='statussss'>Active</span></td>");
                                    } else if (["Mature", "PreMature", "Pluge"].includes(rd_account.status)) {
                                        tr.append("<td><span class='badge bg-label-danger' id='statussss' data-id=" + rd_account.accountNo + "> " + rd_account.status + "</span></td>");
                                    } else if (rd_account.status === "Closed") {
                                        tr.append("<td><span class='badge bg-label-danger' id='statussss'>Closed</span></td>");
                                    }

                                    // Action Buttons
                                    tr.append("<td><button class='receipts_view btn btn-sm btn-primary btn-sm' data-id=" + rd_account.accountId + ">Receipts</button></td>");
                                    if (rd_account.status === "Mature" || rd_account.status === "PreMature") {
                                        tr.append("<td><button type='button' onclick='unmeturty(" + rd_account.accountId + ")' class='btn btn-danger btn-sm'>Unmature</button></td>");
                                    } else if (rd_account.status === "Pluge") {
                                        tr.append("<td></td>");
                                    } else {
                                        tr.append("<td><button type='button' onclick='meturty(" + rd_account.accountId + ")' class='btn btn-success btn-sm'>Mature</button></td>");
                                        tr.append("<td><button type='button' data-id='" + rd_account.id + "' class='btn view p-0 dropdown-toggle hide-arrow editrd'><i class='fa-solid fa-pen-to-square'></i></button> <button type='button' data-id='" + rd_account.id + "' class='btn p-0 dropdown-toggle hide-arrow deleteIds'><i class='fa-solid fa-trash'></i></button></td>");
                                    }

                                    // Append the row to the table body
                                    $("#rd_tbody_list").append(tr);

                                    // Additional Styling for Mature or PreMature Status
                                    if (rd_account.status === "Mature" || rd_account.status === "PreMature") {
                                        amountCell.removeClass("clickable-cell").css("color", "red");
                                        tr.find("td").css("color", "red");
                                    } else {
                                        amountCell.click({ amount: rd_account.amount, accountId: rd_account.accountId }, function (event) {
                                            $("#Rd_Installments_receive_form")[0].reset();
                                            var clickedAmount = event.data.amount;
                                            var clickedRowId = event.data.accountId;

                                            let getmonths = $('#getmonths').text();
                                            $('#rd_account').val(clickedRowId);
                                            $("#rd_amount_value").text(clickedAmount);
                                            $('#rd_months').text(getmonths);
                                            $("#rd_amount_account").val(clickedAmount);
                                            $("#deposit_opening_date").val(currentDate);
                                            $("#rd_account_row").val(clickedRowId);
                                            $("#receive_submit_btn").removeClass('d-none');
                                            $("#receiptupdatebtn").addClass('d-none');
                                            $("#rd_amount_div").show();
                                        });
                                    }

                                    $("#rd_table_id").show();
                                } else {
                                    console.log("No data to display in rd_tbody_list.");
                                }

                            }else{
                                notify(res.messages,'warning');;
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
                }else{
                    $('#ReciptModaldata').modal('show');
                }
            });
        });
























        //_______Edit Installments
        $(document).on('click','.editbtninstallment',function(event){
            event.preventDefault();
            let id = $(this).data('id');
            let amount = $(this).data('amount');
            let paymentdate = $(this).data('paymentdate');
            let account = $(this).data('account-no');
            let memberType = $(this).data('membertype');
            let installmentdate = $(this).data('installmentdate');
            let penality = $(this).data('penality');
            let groupcode = $(this).data('groupcode');
            let ledgercode = $(this).data('ledgercode');
            let months = $(this).data('months');
            let deposit_amount = $(this).data('deposit-amount');
            let agentid = $(this).data('agent');

            let dates = new Date(paymentdate);
            let days = dates.getDate();
            let monthss = dates.getMonth() + 1;
            let year = dates.getFullYear();

            days = days < 10 ? `0${days}` : `${days}`;
            monthss = monthss < 10 ? `0${monthss}` : `${monthss}`;
            let formattedDate = `${days}-${monthss}-${year}`;

            $("#Rd_Installments_receive_form")[0].reset();
            $('#edit_rd_account').val(account);
            $("#rd_amount_value").text(amount);
            $('#payment_type').val(groupcode);

            setTimeout(function() {
                getledgerCode();
                setTimeout(function() {
                    $("#payment_bank").val(ledgercode);
                }, 1000);
            }, 100);

            $('#rd_months').text(months);
            $('#rdid').val(id);
            $("#rd_amount_value").text(amount);
            $("#deposit_opening_date").val(formattedDate);
            $("#agent_id").val(agentid);
            $('#deposit_amount').val(deposit_amount);
            $('#deposit_penalty').val(penality);
            $("#receive_submit_btn").removeClass('d-none');
            $("#receiptupdatebtn").addClass('d-none');
            $('#ReciptModaldata').modal('hide');
            $("#rd_amount_div").show();
        });

        $("#rdMatureForm").validate({
            rules: {
                rd_mature_date: {
                    required: true,
                    customDate: true,
                },
                rd_mature_amount_receive: {
                    required: true,
                    digits : true
                },
                rd_mature_actual_interest: {
                    required: true,
                    number: true,
                },
                rd_mature_actual_penality_value : {
                    digits: true,
                },
                rdtotalnewamount : {
                    required: true,
                    digits: true,
                },
                payment_type : {
                    required: true,
                }
            },
            message: {
                deposit_opening_date: {
                    required: "Please enter a date",
                    customDate: "Please enter a valid date in the format dd-mm-yyyy",
                },
                rd_mature_amount_receive: {
                    required: "Required",
                    digits : "Required"
                },
                rd_mature_actual_interest: {
                    required: 'Required',
                    number: 'Required',
                },
                rd_mature_actual_penality_value : {
                    digits: 'Required',
                },
                rdtotalnewamount : {
                    required: 'Required',
                    digits: 'Required',
                },
                payment_type : {
                    required: 'Required',
                }
            },
        });

        //_______Rd Mature
        $(document).on('submit','#rdMatureForm',function(event){
            event.preventDefault();
            if($(this).valid()){
                let formData = $(this).serialize();

                $.ajax({
                    url : "{{ route('rdmature') }}",
                    type : 'post',
                    data : formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType : 'json',
                    success : function(res){
                        if(res.status === 'success'){
                            {{--  let previous_balance = res.previous_balance;  --}}
                            let rd_account = res.rd_account;
                            let deposit_amount = res.deposit_amount;
                            showData(rd_account,deposit_amount);
                            $('#MatureModaldata').modal('hide');
                            $("#rdMatureForm")[0].reset();
                        } else {
                            notify(res.messages,'warning');;
                        }
                    }
                });
            }
        });

        //_______Rd Un-Mature
        $(document).on('submit','#UnmatureForm',function(event){
            event.preventDefault();
            let accountNo = $("#unmatureid").val();

            $.ajax({
                url : "{{ route('rdunmature') }}",
                type : 'post',
                data : {accountNo : accountNo},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        {{--  let previous_balance = res.previous_balance;  --}}
                        let deposit_amount = res.deposit_amount;
                        let rd_account = res.rd_account;
                        showData(rd_account,deposit_amount);
                        $('#confirmationUnmatureModal').modal('hide');
                        {{--  $("#rd_table_id").show();  --}}
                    } else {
                        notify(res.messages,'warning');;
                    }
                }
            });
        });

        $(document).on('click','.editrd',function(event){
            event.preventDefault();
            let id = $(this).data('id');

            $.ajax({
                url : "{{ route('rdmodify') }}",
                type : 'post',
                data : {id : id},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){
                    if(res.status === 'success'){
                        let rd_account = res.rd_account;

                        if(rd_account){
                            let dates = new Date(rd_account.date);
                            let day = dates.getDate();
                            let month = dates.getMonth() + 1;
                            let year = dates.getFullYear();

                            day = day < 10 ? `0${day}` : day;
                            month = month < 10 ? `0{month}` : month;
                            let formattedDate = `${day}-${month}-${year}`;


                            let datess = new Date(rd_account.actual_maturity_date);
                            let days = datess.getDate();
                            let months = datess.getMonth() + 1;
                            let years = datess.getFullYear();

                            days = days < 10 ? `0${days}` : days;
                            months = months < 10 ? `0{months}` : months;
                            let actual_maturity_date = `${days}-${months}-${years}`;

                            $('#rd_opening_date').val(formattedDate);
                            $('#account_number').val(rd_account.rd_account_no);
                            $('#member_type').val(rd_account.memberType);
                            $('#rd_account_no').val(rd_account.rd_account_no).prop('readonly',true);
                            $('#rd_account_amount').val(rd_account.amount);
                            $('#rd_account_interest').val(rd_account.interest);
                            $('#rd_account_month').val(rd_account.month);
                            $('#rd_account_paid_interest').val(rd_account.paid_interest);
                            $('#rd_account_maturity_date').val(actual_maturity_date);
                            $('#rd_account_maturity_amount').val(rd_account.actual_maturity_amount);
                            $('#membership_no').val(rd_account.accountNo);
                            $('#rd_account_lf_no').val(rd_account.ledger_folio_no);
                            $('#rd_account_page_no').val(rd_account.rd_created_from);
                            $('#rd_account_agent').val(rd_account.agentId);

                            $('.hidefileds').show();

                        }else{
                            $('.hidefileds').hide();
                            $('#rd_opening_date').val();
                            $('#member_type').val();
                            $('#rd_account_no').val();
                            $('#rd_account_amount').val('');
                            $('#rd_account_interest').val('');
                            $('#rd_account_month').val('');
                            $('#rd_account_paid_interest').val('');
                            $('#rd_account_maturity_date').val('');
                            $('#rd_account_maturity_amount').val('');
                            $('#membership_no').val('');
                            $('#rd_account_lf_no').val('');
                            $('#rd_account_page_no').val('');
                            $('#rd_account_agent').val('');
                        }
                    }else{
                        notify(res.messages,'warning');;
                    }
                }
            });
        });



        $(document).on('click', '.deleteIds', function(event) {
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
                        url: "{{ route('deleterd') }}",
                        type: 'post',
                        data: { id: id },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: 'json',
                        success: function(res) {
                            Swal.close()
                            if (res.status === 'success') {
                                notify('Transaction deleted successfully!','success');
                                setTimeout(() => {
                                    window.location.href = "{{ route('rd.recurring.index') }}";
                                }, 1000);
                            } else {
                                notify(res.messages,'warning');;
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



        //_______Delete RD Account



    });

    if (document.readyState == "complete") {
        $(".transactionDate").val({{  session('currentdate') }});
    }
</script>
@endpush



@push('style')
<style>
    button.btn.editbtn, button.btn.deletebtn{
        padding: 0 !important;
    }
    .pt-3 {
        padding-top: 1.5rem !important;
    }
    .clickable-cell {
        color: #7367f0 !important;
    }

    .saving_column {
        position: relative;
    }

    .saving_column .error {

        bottom: -30px;
        left: 12px;
        margin: 0;
        min-height: 38px;
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

    .accountHolderDetails {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .thead-light tr th{
        background-color: #7367f0;
        color: white !important;
    }

    .form-label{
        text-transform: capitalize;
    }


</style>
@endpush


