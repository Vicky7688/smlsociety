@extends('layouts.app')
@section('title', "Account deduction")
@section('pagetitle', "Account deduction")
@php
$table = "no";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row justify-content-between">
                <div class="col-md-6 d-flex align-items-center  savingAccountHeading">
                    <h4 class=""><span class="text-muted fw-light">Transactions / </span>Auto Deduction</h4>
                </div>
                <div class="col-md-3 accountHolderDetails">
                    <h6 class=""><span class="text-muted fw-light">Name: </span><span id="memberName"></span></h6>
                    <h6 class="pt-2"><span class="text-muted fw-light">Balance: </span><span id="memberBalance"></span></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body cardsY">
                    <form id="deductionForm" action="{{route('storeDeductionData')}}"  method="post">
                        @csrf
                        <div class="row row-gap-2">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select class="form-select formInputsSelect" id="memberType" name="memberType">
                                    <option value="Member">Member</option>
                                    <option value="NonMember">Non Member</option>
                                    <option value="Staff">Staff</option>
                                </select>
                          
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control formInputs" id="accountNo" name="accountNo" placeholder="Account No" autocomplete="off" />
                                <div id="accountList" class="accountList"></div>
                           
                            </div>
                            <!--   <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">-->
                            <!--    <label for="memberType" class="form-label">Bank</label>-->
                            <!--    <select class="form-select formInputsSelect" id="banktypes" name="banktypes">-->
                            <!--        @foreach($banktypes as $banktype)-->
                            <!--        <option value="{{$banktype->ledgerCode}}">{{$banktype->name}}</option>-->
                                 
                            <!--        @endforeach-->
                            <!--    </select>-->
                              
                            <!--</div>-->
                            <!--    <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">-->
                            <!--    <label for="memberType" class="form-label">Select deduction day</label>-->
                            <!--    <select class="form-select formInputsSelect" id="deductionday" name="deductionday">-->
                            <!--        @for($i=1; $i <= 28; $i++)-->
                            <!--        <option value="{{str_pad($i, 2, '0', STR_PAD_LEFT)}}">{{str_pad($i, 2, '0', STR_PAD_LEFT)}}</option>-->
                            <!--        @endfor-->
                            <!--    </select>-->
                          
                            <!--</div>-->
                           
                            
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="memberType" class="form-label">Status</label>
                                <select class="form-select formInputsSelect" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                              
                            </div>
                               <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="from_date" class="form-label">Stop Date from</label>
                                <input type="text" class="form-control formInputs" id="from_date" name="from_date" placeholder="Account No" autocomplete="off" />
                                <div id="accountList" class="accountList"></div>
                           
                            </div>
                            
                              <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">
                                <label for="to_date" class="form-label">Stope Date To</label>
                                <input type="text" class="form-control formInputs" id="to_date" name="to_date" placeholder="Account No" autocomplete="off" />
                                <div id="accountList" class="accountList"></div>
                            </div>
                            
                             <div class=" row savingaccountList">
                                
                            </div>
                            <div class="row rdaccountList">
                                
                            </div>
                            
                            <div class="row loanaccountList">
                                
                            </div>
                        
                           <div class="col-lg-2 col-md-3 col-sm-4 col-12 py-2 saving_column inputesPadding savingColumnButton">
                                <div class="d-flex h-100 justify-content-end text-end">
                                     <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                            Loading...">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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

    .saving_column {
        position: relative;
    }

    .saving_column .error {
        position: absolute;
        bottom: -30px;
        left: 12px;
        margin: 0;
        min-height: 38px;
    }

    .page_headings h4,
    .page_headings h6 {
        margin-bottom: 0;
    }

    .table_head tr {
        background-color: #7367f0;
    }

    .table_head tr th {
        color: #fff !important;
    }

    .accountList ul {
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
    
     $("#deductionForm").validate({
        rules: {
            accountNo: {
                required: true,
            },
            banktypes: {
                required: true,
            },
            deductionday: {
                required: true,
            }
        },
        messages: {
            accountNo: {
                required: "Please enter value",
            },
            banktypes: {
                required: "Please enter value",
            },
            deductionday: {
                required: "Please enter value",
            }
        },
        errorElement: "p",
        errorPlacement: function(error, element) {
            if (element.prop("tagName").toLowerCase() === "select") {
                error.insertAfter(element.closest(".form-select").find(".select21"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function() {
            
            
            var form = $('#deductionForm');
            var id = form.find('[name="id"]').val();
            form.ajaxSubmit({
                dataType: 'json', 
                beforeSubmit: function() {
                    form.find('button[type="submit"]').html(
                        '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                    ).attr(
                        'disabled', true).addClass('btn-secondary');
                },
                success: function(data) {
                    if (data.status == "success") {
                        // form[0].reset();
                        form.find('button[type="submit"]').html('Submit').attr(
                            'disabled', false).removeClass('btn-secondary');

                        notify("Auto deduction updated Successfully", 'success');
                     
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
    
    $("#memberType").on('change', getAccountList);
    $("#accountNo").on('keyup', getAccountList);

    $(document).on('click', '#accountList .memberlist', function() {
        var accountNo = $(this).text();
        var memberType = $('#memberType').val();
        $("#accountList").html("");
        displayTable(memberType, accountNo);
    
    });

    function getAccountList() {
        var memberType = $('#memberType').val();
        var accountNo = $('#accountNo').val();
        $.ajax({
            url: "{{ route('list.getData') }}",
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
    
     
    
    
    

    function displayTable(memberType, accountNo) {
        $('#accountNo').val(accountNo);
        $.ajax({
            url: "{{ route('list.fetchData') }}",
            type: "GET",
            data: {
                memberType: memberType,
                accountNo: accountNo
            },
            dataType: 'json',
            success: function(response) {
                if (response['status'] == true) {
                    var  rdinputs = '';
                    var  loaninputs = '';
                    var savinginputs = '' ;
                    $('#memberName').html(response.member.name);
                  
                    var savingRow = response.saving;
                    var allsaved = response.allsaved;
                    var div = $('#inputesPadding');
                    div.empty();
                    var sr = savingRow.length;
                    var inputContainer = $('.rdaccountList');
                    var savingContainer = $('.savingaccountList');
                    var loanContainer = $('.loanaccountList');
                        savingContainer.empty(); 
                    var balance = 0 ;  
                    if (allsaved.length > 0){
                          $('#deductionForm').find('select[name="deductionday"]').val(allsaved[0].deduction_date).trigger('change');
                          $('#deductionForm').find('select[name="status"]').val(allsaved[0].status).trigger('change');
                          $('#deductionForm').find('select[name="banktypes"]').val(allsaved[0].bankcode).trigger('change');
                    }
                   
                    $.each(savingRow, function(index, saving) {
                        
                        //search balance 
                          $.each(allsaved, function(index, allsaved) {
                              if(allsaved.account === saving.savingNo && allsaved.type === 'saving')
                              balance = allsaved.amount ;
                          });
                       savinginputs += 
                          
                           '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="accountNo" class="form-label">CDS Account</label>'+
                            '<input type="text" class="form-control formInputs" id="saccount" name="saccount[`saving`][]" placeholder="Account No" autocomplete="off"/ value="'+ saving.savingNo+'" readonly>'+
                            '<p class="error"></p>'+
                             '</div>'+
                            
                            '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="Amount" class="form-label">Deduction Amount</label>'+
                            '<input type="text" class="form-control formInputs" id="Amount" name="sAmount[`saving`][]" placeholder="Amount" autocomplete="off" value="'+ balance +'" />'+
                            
                            '<p class="error"></p>'+
                           
                            '</div>';
                    });
                     savingContainer.append(savinginputs);
                    
                  
                      loanContainer.empty(); 
                     var loanaccounts = response.loan ;
                    $.each(loanaccounts, function(index,loanaccount) {
                        
                          $.each(allsaved, function(index, allsaved) {
                              if(allsaved.account === loanaccount.loanAcNo && allsaved.type === 'loan')
                              balance = allsaved.amount ;
                          });
                         loaninputs += 
                          
                           '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="accountNo" class="form-label">Loan Account</label>'+
                            '<input type="text" class="form-control formInputs" id="rdaccount" name="saccount[`loan`][]" placeholder="Account No" autocomplete="off"/ value="'+ loanaccount.loanAcNo+'" readonly>'+
                            '<p class="error"></p>'+
                             '</div>'+
                            
                            '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="Amount" class="form-label">Deduction Amount</label>'+
                            '<input type="text" class="form-control formInputs" id="Amount" name="sAmount[`loan`][]" placeholder="Amount" autocomplete="off" value="'+ balance +'" />'+
                            
                            '<p class="error"></p>'+
                           
                            '</div>';
                    });
                     loanContainer.append(loaninputs);
                     
                     
                     
                 
                    inputContainer.empty(); 
                    var rdaccounts = response.rd ;
                    $.each(rdaccounts, function(index,rdaccount) {
                        
                         $.each(allsaved, function(index, allsaved) {
                              if(allsaved.account === rdaccount.rd_account_no && allsaved.type === 'rd')
                              balance = allsaved.amount ;
                          });
                     
                         rdinputs += 
                          
                           '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="accountNo" class="form-label">Rd Account</label>'+
                            '<input type="text" class="form-control formInputs" id="rdaccount" name="saccount[`rd`][]" placeholder="Account No" autocomplete="off"/ value="'+ rdaccount.rd_account_no+'" readonly>'+
                            '<p class="error"></p>'+
                             '</div>'+
                            
                            '<div class="col-lg-2 col-md-3 col-sm-4 col-6 py-2 saving_column inputesPadding">'+
                            '<label for="Amount" class="form-label">Deduction Amount</label>'+
                            '<input type="text" class="form-control formInputs" id="Amount" name="sAmount[`rd`][]" placeholder="Amount" autocomplete="off" value="'+balance+'" />'+
                            
                            '<p class="error"></p>'+
                           
                            '</div>';
                    });
                     inputContainer.append(rdinputs); 
                }
            }
        });
    }
});

</script>
@endpush