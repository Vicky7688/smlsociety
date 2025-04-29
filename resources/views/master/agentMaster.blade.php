@extends('layouts.app')
@section('title', "Agent Master")
@section('pagetitle', "Agent Master")

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Daily Collection / </span> Agents </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 cards">
            <div class="card">
                <div class="card-body py-3">
                    <h5 class="card-action-title">Agent Master</h5>
                    <div class="card-action-element">

                        <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                <button type="button" class="btn btn-primary reportSmallBtnCustom" onclick="addSetup()">
                                    Add Agent
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tablee">
                        <div class="table-responsive tabledata"> <!-- removed the class "card-datatable" -->
                            <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Pan No</th>
                                        <th>Comm. on FD</th>
                                        <th>Comm. on RD</th>
                                        <th>Comm. on Daily Loan</th>
                                        <th>Comm. on Daily Saving</th>
                                        <th>Comm. on Saving</th>
                                        <th>Comm. on MIS</th>
                                        <th>Joining Date</th>
                                        <th>Releaving Date</th>
                                        <th>Status</th>
                                        <th colspan='2'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($agents))
                                        @foreach($agents as $row)
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{ucwords($row->name)}}</td>
                                            <td>{{ucwords($row->phone)}}</td>
                                            <td>{{ucwords($row->email)}}</td>
                                            <td>{{ucwords($row->address)}}</td>
                                            <td>{{ucwords($row->panNo)}}</td>
                                            <td>{{ucwords($row->commissionFD)}}</td>
                                            <td>{{ucwords($row->commissionRD)}}</td>
                                            <td>{{ucwords($row->commissionLoan)}}</td>
                                            <td>{{ucwords($row->daily_saving)}}</td>
                                            <td>{{ucwords($row->commissionSaving)}}</td>
                                            <td>{{ucwords($row->commissionmis)}}</td>
                                            <td>{{date('d-m-Y', strtotime($row->joiningDate))}}</td>
                                            <td>{{ $row->releavingDate ? date('d-m-Y', strtotime($row->releavingDate)) : '' }}</td>
                                            <td>{{ucwords($row->status)}}</td>

                                            @php
                                                $agent_entries = DB::table('opening_accounts')->where('membertype',$row->memberType)->where('membershipno',$row->staff_no)->first();
                                            @endphp

                                            @if(!empty($agent_entries))
                                                <td>

                                                </td>
                                            @else
                                                <td>
                                                    <a href="javascript:void(0);" class="editagent" data-id="{{$row->id}}"><i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i></a>
                                                    <a href="javascript:void(0);" class="deleteagent" data-id="{{$row->id}}"><i class="fa-solid fa-trash iconsColorCustom"></i></a >
                                                </td>
                                            @endif


                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> <span class="msg">ADD </span> Commission </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="agent" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="agentid" id="agentid"/>
                <div class="modal-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="joiningDate" class="form-label">Joining Date</label>
                            <input type="text" name="joiningDate" value="{{ Session::get('currentdate') }}" id="joiningDate" class="form-control formInputsReport"  />
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">MEMBER TYPE</label>
                            <select name="memberType" class="select21 form-select formInputsSelectReport" id="memberType" data-placeholder="Active">
                                <option value="Member" default selected>Member</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">Staff No</label>
                            <input type="text" name="staff_no" id="staff_no" oninput="getmemberType('this')"  class="form-control formInputsReport" placeholder="Enter name"  />
                            <div id="accountList" class="accountList"></div>
                        </div>

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control formInputsReport" placeholder="Enter name" readonly />
                        </div>


                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control formInputsReport" placeholder="Enter phone" minlength="10"  />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control formInputsReport" placeholder="Enter Email"  />
                        </div>

                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="panNo" class="form-label">Pan No</label>
                            <input type="text" name="panNo" id="panNo" class="form-control formInputsReport" maxlength="10" placeholder="Enter Pan No."  />
                        </div>
                    <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                        <label for="commissionSaving" class="form-label">Comm on Saving</label>
                        <input type="text" name="commissionSaving" id="commissionSaving" class="form-control formInputsReport" value="">
                    </div>

                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionFD" class="form-label">Comm on FD</label>
                            <input type="text" name="commissionFD" id="commissionFD" class="form-control formInputsReport" value="">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionRD" class="form-label">Comm on RD</label>
                            <input type="text" name="commissionRD" id="commissionRD" class="form-control formInputsReport" value="">
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                    <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                        <label for="commissionmis" class="form-label">Comm on MIS</label>
                        <input type="text" name="commissionmis" id="commissionmis" class="form-control formInputsReport" value="">
                    </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="" class="form-label">Comm on Daily Saving</label>
                            <input type="text" name="daily_saving" id="daily_saving" class="form-control formInputsReport" value="">
                        </div>

                    <!-- </div>
                    <div class="row mb-3"> -->

                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport" id="releavingDatediv" style="display: none;">
                            <label for="releavingDate" class="form-label">Releaving Date</label>
                            <input type="date" name="releavingDate" value="{{ Session::get('currentdate') }}" id="releavingDate" class="form-control formInputsReport" />
                        </div>
                    <!-- </div>
                    <div class="row mb-3"> -->
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="commissionDailyCollection" class="form-label">Comm on Daily Loan</label>
                            <input type="text" name="commissionDailyCollection" id="commissionDailyCollection" class="form-control formInputsReport" value="">
                        </div>
                        <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="select21 form-select formInputsSelectReport" onchange="AgentLeaveDate('this')" id="status" data-placeholder="Active">
                                <option value="Active" default selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-sm-4 col-6 py-2 inputesPaddingReport">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control formInputsReport" placeholder="Enter Address"  />
                        </div>
                    </div>
                </div>
                <div class="modal-footer me-0">
                    <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')

<style>
    html:not([dir=rtl]) .modal .btn-close {
        transform: none !important;
    }

    .btn-close {
        top: 1.35rem !important;
    }

    /* #datatable_wrapper .dataTables_info,
    #datatable_wrapper .dataTables_paginate {
        display: none;
    } */

    .accountList {
    position: relative;
    left: 12px;
    bottom: 0px;
    /* transform: translateY(90%); */
    width: calc(100% - 24px);
    background-color: aliceblue;
    border: 1px solid #fff;
    border-radius: 5px;
    max-height: 100px;
    overflow-y: auto;
    z-index: 99;
    padding-top: 0px;
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

</style>

@endpush

@push('script')
<script type="text/javascript">

    function getmemberType(){
        let memberType = $('#memberType').val();
        let staff_no = $('#staff_no').val();

        $.ajax({
            url : "{{ route('getallstaffnumber') }}",
            type : 'post',
            data : { memberType : memberType,staff_no : staff_no},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType : 'json',
            success : function(res){
                if(res.status === 'success'){
                    let allstaff = res.allstaff;
                    let accountList = $('#accountList');
                    accountList.empty();

                    if (allstaff) {
                        allstaff.forEach((data) => {
                            accountList.append(
                                `<div class="accountLists" data-id="${data.accountNo}">${data.accountNo}</div>`
                            );
                        });
                    } else {
                        accountList.append(`<div class="accountLists">No Account</div>`);
                    }
                }else{
                    notify(res.messages,'warning');
                }
            }
        });
    }

    $(document).ready(function(){
        $(document).on('click','.accountLists',function(event){
            event.preventDefault();
            let selectdId = $(this).data('id');
            let memberType = $('#memberType').val();
            $('#staff_no').val(selectdId);
            $('#accountList').html('');

            $.ajax({
                url: "{{ route('getstaffnumber') }}",
                type: 'post',
                data: {
                    selectdId: selectdId,
                    memberType : memberType
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success'){
                        let staff_detail = res.staff_detail;
                        $('#name').val(staff_detail.name);
                        $('#phone').val(staff_detail.phone);
                        $('#address').val(staff_detail.address);
                        $('#panNo').val(staff_detail.panNo);

                    }else{
                        $('#name').val('');
                        $('#phone').val('');
                        $('#address').val('');
                        $('#panNo').val('');
                        notify(res.messages,'warning');
                    }
                }
            });
        });

        $(document).on('click','.editagent',function(event){
            event.preventDefault();
            let agentId = $(this).data('id');
            $.ajax({
                url: "{{ route('editagents') }}",
                type: 'post',
                data: {
                    agentId :agentId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success'){
                        let agent = res.exitsIdagent;

                        let newdate = new Date(agent.joiningDate);
                        let days = newdate.getDate();
                        let months = newdate.getMonth() + 1;
                        let years = newdate.getFullYear();
                        days = days < 10 ? `0${days}` : days;
                        months = months < 10 ? `0${months}` : months;
                        let formattedDate = `${days}-${months}-${years}`;


                        let newdates = new Date(agent.releavingDate);
                        let dayss = newdates.getDate();
                        let monthss = newdates.getMonth() + 1;
                        let yearss = newdates.getFullYear();
                        dayss = dayss < 10 ? `0${dayss}` : dayss;
                        monthss = monthss < 10 ? `0${months}` : monthss;
                        let formattedDates = `${dayss}-${monthss}-${yearss}`;


                        $('#agentid').val(agent.id);
                        $('#joiningDate').val(formattedDate);
                        $('#memberType').val(agent.memberType);
                        $('#staff_no').val(agent.staff_no).prop('readonly',true);
                        $('#name').val(agent.name);
                        $('#phone').val(agent.phone);
                        $('#email').val(agent.email);
                        $('#panNo').val(agent.panNo);

                        $('#commissionSaving').val(agent.commissionSaving);
                        $('#commissionmis').val(agent.commissionmis);

                        $('#commissionFD').val(agent.commissionFD);
                        $('#commissionRD').val(agent.commissionRD);
                        $('#daily_saving').val(agent.daily_saving);
                        $('#commissionDailyCollection').val(agent.commissionLoan);
                        $('#status').val(agent.status);
                        $('#address').val(agent.address);
                        $('#basicModal').modal('show');


                    }else{
                        $('#staff_no').val(agent.staff_no).prop('readonly',false);
                        notify(res.messages,'warning');
                    }
                }
            });
        });


        $("#agent").validate({
            rules: {
                memberType : {
                    required: true,
                },
                staff_no : {
                    required: true,
                    number: true,
                },
                name: {
                    required: true,
                },
                phone: {
                    required: true,
                    digits: true, // Ensures that the input consists only of digits
                    minlength: 10, // Minimum length of 10 digits
                    maxlength: 10 // Maximum length of 10 digits
                },
                // email: {
                //     required: true,
                // },
                address: {
                    required: true,
                },
                panNo: {
                    required: true,
                    // min: 9, // Minimum value allowed
                    // max: 10 // Maximum value allowed
                },

                joiningDate: {
                    required: true,
                    date: true
                },
                // releavingDate: {
                //     required: true,
                //     date: true
                // },
                status: {
                    required: true,
                },
                // commissionSaving: {
                //     required: true,
                //     number: true,
                // },
                commissionFD: {
                    required: true,
                    number: true,
                },
                commissionRD: {
                    required: true,
                    number: true,
                },
                daily_saving: {
                    required: true,
                    number: true,
                },
                commissionDailyCollection: {
                    required: true,
                    number: true,
                }
            },
            messages: {
                memberType : {
                    required: "Please Select Type",
                },
                staff_no : {
                    required: "Please enter value",
                    min: "Enter Numeric Value",
                },
                name: {
                    required: "Please enter value",
                },

                phone: {
                    required: "Please enter value",
                    min: "Phone number must be 10 digit",
                },

                // email: {
                //     required: "Please enter value",
                // },
                address: {
                    required: "Please enter value",
                },
                panNo: {
                    required: "Please enter value",
                },
                joiningDate: {
                    required: "Please enter value",
                },

                // releavingDate: {
                //     required: "Please enter value",
                // },
                status: {
                    required: "Please enter value"
                },
                // commissionSaving: {
                //     required: "Please enter value",
                //     number: "Commission Saving should be in numbers only."
                // },
                commissionFD: {
                    required: "Please enter value",
                    number: "Commision Fd should be i,n numbers only.",
                },
                commissionRD: {
                    required: "Please enter value",
                    number: "Commission Rd should be i,n numbers only",
                },
                // commissionShare: {
                //     required: "Please enter value",
                //     number: "Commission Share should be numbers only",
                // },
                daily_saving: {
                    required: "Please enter value",
                    number: "Commission Daily Saving should be i,n numbers only",
                },
                commissionDailyCollection: {
                    required: "Please enter value",
                    number: "Commission Daily Loan should be i,n numbers only",
                }
            },
            erroeElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select21"));
                } else {
                    error.insertAfter(element);
                }
            },
            // submitHandler: function() {
            //     var form = $('#agent');
            //     var id = form.find('[name="id"]').val();
            //     form.ajaxSubmit({
            //         dataType: 'json',
            //         beforeSubmit: function() {
            //             form.find('button[type="submit"]').html(
            //                 '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
            //             ).attr(
            //                 'disabled', true).addClass('btn-secondary');
            //         },
            //         success: function(data) {
            //             if (data.status == "success") {
            //                 form[0].reset();
            //                 form.find('button[type="submit"]').html('Submit').attr(
            //                     'disabled', false).removeClass('btn-secondary');

            //                 notify("Task successfully Completed", 'success');
            //                 // $('#datatable').dataTable().api().ajax.reload();
            //                 $('#datatable').load(location.href+' .table');
            //                 $('#basicModal').modal('hide');
            //             } else {
            //                 notify(data.status, 'warning');
            //             }
            //         },
            //         error: function(errors) {
            //             showError(errors, form);
            //         }
            //     });
            // }


        });

        $(document).on('submit','#agent',function(event){
            event.preventDefault();
            let formData = $(this).serialize();
            let url = $('#agentid').val() ? "{{ route('agentupdate') }}" : "{{ route('insertagent') }}"

            $.ajax({
                url: url,
                type: 'post',
                // data: {
                //     // selectdId: selectdId,
                //     transactionType: transactionType
                // },
                data : formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success'){
                        {{--  $('#datatable').load(location.href+' .table');  --}}
                        $('#basicModal').modal('hide');
                        window.location.href="{{ route('agentindex') }}";
                    }else{
                        notify(res.messages,'warning');
                    }
                }
            });

        });


        $(document).on('click','.deleteagent',function(event){
            event.preventDefault();
            let agentId = $(this).data('id');
            $.ajax({
                url: '{{ route('deleteagent')}}',
                type: 'post', // Use the HTTP DELETE method
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    agentId : agentId,
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting data',
                        onOpen: () => {
                            swal.showLoading();
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                success: function(res) {
                    swal.close();
                    if (res.status == "success") {
                        window.location.href="{{ route('agentindex') }}";
                        swal({
                            type: 'success',
                            title: 'Success',
                            text: "Data Successfully Deleted",
                            showConfirmButton: true,
                        });
                    } else {
                        swal({
                            type: 'error',
                            title: 'Failed',
                            text: "Something went wrong",
                            showConfirmButton: true,
                        });
                    }
                },
                error: function() {
                    swal.close();
                    notify('Something went wrong', 'warning');
                },
                complete: function() {}
            });
        });
    })

    function AgentLeaveDate(){
        let statusss = $('#status').val();
        if(statusss === 'Inactive'){
            let releavingDatediv = $('#releavingDatediv');
            if (releavingDatediv.css('display') === 'none') {
                releavingDatediv.css('display', 'block');
                $('#basicModal').find('input[name="releavingDate"]').val(releavingDate);
            }
        }else{
            let releavingDatediv = $('#releavingDatediv');
            if (releavingDatediv.css('display') === 'block') {
                releavingDatediv.css('display', 'none');
                $('#basicModal').find('input[name="releavingDate"]').val(releavingDate);
            }
        }
    }

    function addSetup() {
        $('#agent')[0].reset();
        $('#basicModal').modal('show');
    }



</script>
@endpush
