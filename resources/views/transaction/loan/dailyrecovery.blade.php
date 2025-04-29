@extends('layouts.app')
@section('title', " Recovery")
@section('pagetitle', "Recovery")
@php
$table = "no";
@endphp
@section('content')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
$( function() {
  $( "#datepickermodal" ).datepicker({
    'autoclose': true,
    'clearBtn': true,
    'todayHighlight': true,
    "endDate": "today",
    'format': 'dd-mm-yyyy',
   });
} );
</script>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card">
                <h5 class="card-header">Loan Recovery</h5>
                <div class="table-responsive text-nowrap pb-3">
                    <form id="installmentsPaid" action="{{route('loanupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="paidinstallments" />
                        <input type="hidden" name="id" value="" />
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">Date</label>
                                    <input id="transactionDate" type="text" name="loanDate" class="form-control form-control-sm mydatepic" value="{{date('d-m-Y')}}" placeholder="DD-MM-YYYY" required />
                                </div>
                                <div class="mb-3 col-md-3 col-sm-12">
                                    <label class="form-label mb-1" for="status-org">Member </label>
                                    <select name="memberType" id="memberType" class=" form-select form-select-sm" data-placeholder="Select Member">
                                        <option value="Member">Member</option>
                                        <option value="Nominal Member">Nominal Member</option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label for="name" class="form-label">Ac Number</label>
                                    <input type="text" id="accountNumber" name="accountNumber" class="form-control form-control-sm" placeholder="Enter value" required  autocomplete="off">
                                </div>
                <div class="col-md-4 col-sm-6 mb-3" id="issubmit" style="display: none;">
                                    <button id="submitButton" class="btn btn-primary waves-effect waves-light mt-4" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span> Loading...">Submit</button>
                                    <button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#modalLong">
                                        View Installment
                                    </button>
                                </div>
                            </div>
                        </div>
            </form>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="col-sm-12 col-md-12">
            <div class="card recovery" style="display: none;">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Recovery Date</th>
                                <th>Principal Received</th>
                                <th>Interest Received</th>
                                <th>Penal Interest Received</th>
                                <th>Total Received</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 recoveryData">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="card loandetails" style="display: none;">
            <div class="table-responsive text-nowrap">
                <table class="table datatables-order table table-bordered dataTable no-footer">
                    <thead class="table_head verticleAlignCenterReport">
                        <tr>
                            <th>Date</th>
                            <th>Loan Name</th>
                            <th>Loan amount</th>
                            <th>Total Installment</th>
                            <th>Type</th>
                            <th>Recieved</th>
                            <th>Transfered</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th> Recieve</th>
                            <th> Recieved</th>
                            <th> Transfer </th>
                            <th> View </th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 transactionData">
                    </tbody>
                </table>
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
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="table-responsive text-nowrap print-content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Inst. Date</th>
                                <th>Principal</th>
                                <th>Interest</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="installmentsdata">
                            <!-- Dynamic rows will be inserted here -->
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
@endsection
@push('style')
@endpush
<div class="modal fade" id="transferamount" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferamountTitle">Transfer to loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferinstallmentForm">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="loanid"  id="transferloanid">
                        
                        <div class="col-md-4">
                            <label   class="form-label">Transfer Date</label>
                            <input id="datepickermodal" type="text" name="installdate" class="form-control form-control-sm mydatepic" value="{{ date('d-m-Y') }}" placeholder="DD-MM-YYYY" required />
                        </div>
                        <div class="col-md-4">
                            <label  class="form-label">Available Amount</label>
                            <input type="text" name="availableamount" id="availableamount" value="0" class="form-control form-control-sm" autocomplete="off" >
                        </div>
                        <div class="col-md-4">
                            <label  class="form-label">Transfer To Loan</label>
                            <input type="text" name="amount" id="amount" value="0" class="form-control form-control-sm" autocomplete="off">
                        </div>
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updatetransferButton" class="btn btn-primary">Transfer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalrecive" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalreciveTitle">Receive Installment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="installmentForm">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="loanid"  id="loanid" >
                        <div class="col-md-4">
                            <label   class="form-label">Date</label>
                            <input id="datepickermodal" type="text" name="installdate" class="form-control form-control-sm mydatepic" value="{{ date('d-m-Y') }}" placeholder="DD-MM-YYYY" required />
                        </div>
                        <div class="col-md-4">
                            <label  class="form-label">Amount</label>
                            <input type="text" name="amount" id="amount" value="0" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label   class="form-label">Panelty</label>
                            <input type="text" name="panelty" id="panelty" value="0" class="form-control form-control-sm" autocomplete="off">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payment type</label>
                            <select class="form-select form-select-sm" id="paytype" name="paytype" onchange="toggleBankSection()">

                                <option value="C002">Cash</option>
                                <option value="BANK001">Bank</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="bank-section" style="display:none">
                            <label class="form-label">Bank</label>
                            <select class="form-select form-select-sm" id="bank" name="bank">
                                <option value="">Select Bank</option>
                                @foreach($banktypes as $banktypeslist)
                                    <option value="{{ $banktypeslist->ledgerCode }}">{{ $banktypeslist->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-4">
                            <label class="form-label mb-1" for="status-org">Agent </label>
                            <select name="agentId" id="status-org" class="form-select form-select-sm"
                                data-placeholder="Active">
                                <option value="">Select Agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <script>
                            function toggleBankSection() {
                                var paytype = document.getElementById('paytype').value;
                                var bankSection = document.getElementById('bank-section');
                                if (paytype !== 'C002') {
                                    bankSection.style.display = 'block';
                                } else {
                                    bankSection.style.display = 'none';
                                }
                            }
                            window.onload = function() {
                                toggleBankSection();
                            };
                        </script>
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateButton" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modaltable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltableTitle">Recieved</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap print-content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>recoverydate</th>
                                <th>Loan recieved</th>
                                <th>penaltyamount</th>
                                <th>transfered</th>
                                <th> </th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody class="transactionTableBody">
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
@push('script')
<script type="text/javascript">
    $(document).ready(function() {
    var currentDate = moment().format('DD-MM-YYYY');
    $("#transactionDate").val(currentDate);
    $("#transactionDatee").val(currentDate);
    $("#accountNumber").blur(function() {
       var account = $(this).closest('form').find('input[name="accountNumber"]').val();
       var member = $('#memberType').val();
       getLoanAc(account, member);
    });
 });

 function update() {
    var account = $('input[name="accountNumber"]').val();
    var member = $('#memberType').val();
    getLoanAc(account, member);
 }

 function getLoanAc(account, member) {
    $.ajax({
       url: "{{route('getdailyloanaccount')}}",
       type: "POST",
       dataType: 'json',
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       beforeSend: function() {
          $(".loandetails").css("display", "none");
          $(".recovery").css("display", "none");
          $('.transactionData').html('');
          $(".recoveryData").html("");
       },
       data: {
          'actiontype': "getLoanAc",
          'loanAcNo': account,
          'member': member
       },
       success: function(data) {
          swal.close();
          if (data.status == "success") {
             $(".loandetails").css("display", "block");
             $(".transactionData").html("");
             var tbody = '';
             if (data.data.length === 0) {} else {
                $.each(data.data, function(index, val) {
                   if (val.status == "Disbursed") {
                      var trclass = `class="table-success"`;
                   } else if (val.status == "Closed") {
                      var trclass = `class="table-danger"`;
                   } else if (val.status == "Inactive") {
                      var trclass = `class="table-warning"`;
                   }
                   tbody += "<tr " + trclass + ">" +
                      "<td style='display:none'>" + val.id + "</td>" +
                      "<td>" + formatDate(val.loanDate) + "</td>" +
                      "<td>" + val.purpose + "</td>" +
                      "<td>" + val.loanAmount + "</td>" +
                      "<td>" + val.emiinstatotal + "</td>" +
                      "<td>" + val.installmentType + "</td>" +
                      "<td>" + val.totalTransactionAmount + "</td>" +
                      "<td>" + val.totalTransferredAmount + "</td>" +
                      "<td>" + (parseInt(val.totalTransactionAmount) - parseInt(val.totalTransferredAmount)) + "</td>" +
                      "<td>" + val.status + "</td>" +
                      "<td><button  onclick=modalrecive(" + val.id + ")  type='button' class='btn btn-sm btn-warning'>Recieve</button></td>" +
                      "<td><button onclick=getrecieved(" + val.id + ")  type='button' class='btn btn-sm btn-success'>Recieved</button></td>" +
                      "<td><button onclick=transfer(" + val.id + ")  type='button' class='btn btn-sm btn-primary'>Transfer</button></td>" +
                      "<td><button   onclick=rowClicked(" + val.id + ")  type='button' class='btn btn-sm btn-dark'>Installments</button></td>";
                });
             }
             $('.transactionData').html(tbody);
          } else {
             notify(data.status, 'warning');
          }
       }
    });
 }

 function getrecieved(id) {
    $.ajax({
       url: "{{route('getdailyloanaccountreceived')}}",
       type: "POST",
       dataType: 'json',
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       beforeSend: function() {

       },
       data: {
          'id': id,
       },
       success: function(response) {
          if (response.success) {
             var tbody = $('.transactionTableBody');
             tbody.empty();
             var data = response.message;
             data.forEach(function(transaction, index) {
                if (transaction.transfered == 'no') {
                   var linkedit = '<a onclick="deleteItem(' + transaction.id + ')" href="javascript:void(0);"><i class="ti ti-pencil me-1"></i></a>';
                   var linkdelt = '<a onclick="deleteItem(' + transaction.id + ')" href="javascript:void(0);"><i class="ti ti-trash me-1"></i></a>';
                } else {
                   var linkedit = '';
                   var linkdelt = '';
                }
                var row = '<tr>' +
                   '<td>' + (index + 1) + '</td>' +
                   '<td>' + formatDate(transaction.recoverydate) + '</td>' +
                   '<td>' + transaction.transactionamount + '</td>' +
                   '<td>' + transaction.penaltyamount + '</td>' +
                   '<td>' + transaction.transfered + '</td>' +
                   '<td>' + linkedit + '</td>' +
                   '<td>' + linkdelt + '</td>' +
                   '</tr>';
                console.log(row);
                tbody.append(row);
             });
          } else {
             notify("No transaction data found", 'warning');
          }
       },
       error: function() {
          notify("Error fetching data", 'warning');

       }
    });
    $('#modaltable').modal('show');
 }

 function modalrecive(id) {
    $('#loanid').val(id); // Opens the modal
    $('#modalrecive').modal('show'); // Opens the modal
 }

 function deleteItem(id) {
    $.ajax({
       url: "{{route('deleteItemtransfered')}}",
       type: "POST",
       dataType: 'json',
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       beforeSend: function() {
        swal({
             title: 'Wait!',
             text: 'We are fetching loan details.',
             allowOutsideClick: () => !swal.isLoading(),
             onOpen: () => {
                swal.showLoading()
             }
          });
       },
       data: {
          'id': id,
       },
       success: function(response) {

        },
       error: function() {
          notify("Error fetching data", 'warning');
       }
    });
 }

 function transfer(id) {
    $.ajax({
       url: "{{route('transferloanaccountreceived')}}",
       type: "POST",
       dataType: 'json',
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       beforeSend: function() {

       },
       data: {
          'id': id,
       },
       success: function(response) {
          if (response.success) {
             $('#availableamount').val(response.amount);
             $('#transferloanid').val(id);
             $('#transferloanid').val(id);
             $('#transferamount').modal('show');
             document.getElementById('availableamount').readOnly = true;
          } else {
             notify("No transaction data found", 'warning');
          }
       },
       error: function() {
          notify("Error fetching data", 'warning');
       }
    });
 }
 $(document).ready(function() {
    $('#updatetransferButton').on('click', function() {
       var formData = $('#transferinstallmentForm').serialize();
       $.ajax({
          url: '{{ route("getdailytransfer") }}',
          type: 'POST',
          data: formData,
          success: function(response) {
             if (response.success) {
                $('#transferinstallmentForm')[0].reset();
                $('#transferamount').modal('hide');
                update();
                notify(response.message, 'success');
             } else {
                notify(response.message, 'warning');
             }
          },
          error: function(xhr, status, error) {
             notify(xhr.responseText, 'warning');
          }
       });
    });
 });

 $(document).ready(function() {
    $('#updateButton').on('click', function() {
       var formData = $('#installmentForm').serialize();
       $.ajax({
          url: '{{ route("getdailyloanperday") }}',
          type: 'POST',
          data: formData,
          success: function(response) {
             if (response.success) {
                $('#installmentForm')[0].reset();
                $('#modalrecive').modal('hide');
                update();

                notify(response.message, 'success');
             } else {
                notify(response.message, 'warning');
             }
          },
          error: function(xhr, status, error) {
             notify(xhr.responseText, 'warning');
          }
       });
    });
 });

 function rowClicked(id) {
    var transactiondate = $('#transactionDate').val();
    $.ajax({
       url: "{{route('loanupdate')}}",
       type: "POST",
       dataType: 'json',
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       beforeSend: function() {
          swal({
             title: 'Wait!',
             text: 'We are fetching loan details.',
             allowOutsideClick: () => !swal.isLoading(),
             onOpen: () => {
                swal.showLoading()
             }
          });
       },
       data: {
          'actiontype': "getloandetails",
          'id': id,
          'transactiondate': transactiondate
       },
       success: function(data) {
          swal.close();
          $('.installmentsdata').html('');
          if (data.status == "success") {
             $('#installmentsPaid').find('input[name="PrincipalTillDate"]').val(data.loandetails.principal);
             $('#installmentsPaid').find('input[name="id"]').val(id);
             $('#installmentsPaid').find('input[name="InterestTillDate"]').val(data.loandetails.currentintrest);
             $('#installmentsPaid').find('input[name="TotalTillDate"]').val(data.loandetails.netintrest);
             $('#installmentsPaid').find('input[name="PendingIntrTillDate"]').val(data.loandetails.pendingintrest);
             $('#installmentsPaid').find('input[name="overdue"]').val(data.loandetails.overdueintrest);
             var tbody;
             if (data.installmet.length === 0) {} else {
                var srno = 1;
                $.each(data.installmet, function(index, val) {
                   tbody += "<tr>" +
                      "<td>" + srno++ + "</td>" +
                      "<td>" + moment(val.installmentDate).format('DD-MM-YYYY') + "</td>" +
                      "<td>" + val.principal + "</td>" +
                      "<td>" + val.interest + "</td>" +
                      "<td>" + val.total + "</td>" +
                      "</tr>";
                });
                $('.installmentsdata').html(tbody);
                $('#modalLong').modal('show');
             }
          } else {
             notify(data.status, 'warning');
          }
       }
    });
 }

</script>
@endpush
