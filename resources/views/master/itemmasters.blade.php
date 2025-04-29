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

    function addSetup() {
        $('#agent')[0].reset();
        $('#basicModal').modal('show');
    }



</script>
@endpush
