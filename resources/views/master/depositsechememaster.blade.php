@extends('layouts.app')
@section('title', ' Loan Master')
@section('pagetitle', 'Loan Master')

@php
    $table = 'yes';
@endphp

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-12">
                <div class="card page_headings cards">
                    <div class="card-body py-2">
                        <h4 class="py-2"><span class="text-muted fw-light">Masters / Secheme Module / </span> Secheme Master
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 cards">
                <div class="card">
                    <div class="card-body py-3">
                        <h5 class="card-action-title">Scheme Master</h5>
                        <div class="card-action-element">
                            <div class="col-12 py-2 saving_column inputesPaddingReport reportBtnInput">
                                <div class="d-flex h-100 justify-content-end text-end pt-2 pb-3">
                                    <button type="button" class="btn btn-primary reportSmallBtnCustom"
                                        onclick="addSetup()">
                                        Add Scheme
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tablee">
                            <div class="table-responsive tabledata">
                                <table class="table datatables-order table table-bordered" id="datatable"
                                    style="width:100%">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th class="w-17">S No</th>
                                            <th>Member Type</th>
                                            <th>Start Date</th>
                                            <th>Name</th>
                                            <th>Scheme Type</th>
                                            <th>Duration Type</th>
                                            <th>Tenure (D)</th>
                                            <th>Tenure (M)</th>
                                            <th>Tenure (Y)</th>
                                            <th>Lock-In (D)</th>
                                            <th>Lock-In (M)</th>
                                            <th>Lock-In (Y)</th>
                                            <th>InstType</th>
                                            <th>Intt.</th>
                                            <th>P.Intt.</th>
                                            <th>Status</th>
                                            <th>End Date</th>
                                            <th colspan="2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($sechemnes))
                                            @foreach ($sechemnes as $row)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $row->memberType }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($row->start_date)) }}</td>
                                                    <td>{{ $row->name }}</td>
                                                    <td>{{ $row->secheme_type }}</td>
                                                    <td>{{ $row->durationType }}</td>
                                                    <td>{{ $row->days ? $row->days : '-' }}</td>
                                                    <td>{{ $row->months ? $row->months : '-' }}</td>
                                                    <td>{{ $row->years ? $row->years : '-' }}</td>
                                                    <td>{{ $row->lockin_days ? $row->lockin_days : '-' }}</td>
                                                    <td>{{ $row->lockin_months ? $row->lockin_months : '-' }}</td>
                                                    <td>{{ $row->lockin_years ? $row->lockin_years : '-' }}</td>
                                                    <td>{{ $row->interest_type }}</td>
                                                    <td>{{ $row->interest }}</td>
                                                    <td>{{ $row->penaltyInterest }}</td>
                                                    <td>{{ $row->status }}</td>
                                                    <td>{{ $row->secheme_end_date ? date('d-m-Y', strtotime($row->secheme_end_date)) : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($row->status === 'Active')
                                                            <button class="btn editbtn" data-id="{{ $row->id }}">
                                                                <i
                                                                    class='fa-solid fa-pen-to-square border-0 iconsColorCustom'></i>
                                                            </button>
                                                        @endif

                                                        @php
                                                            $candelete = 'Yes';
                                                            //________Get Scheme Details
                                                            $scheme = DB::table('scheme_masters')
                                                                ->where('id', $row->id)
                                                                ->first();

                                                            //______Check Scheme Has Account
                                                            $opening_account = DB::table('opening_accounts')
                                                                ->where('schemetype', $scheme->id)
                                                                ->first();

                                                            //_______if Scheme Has Account Then Not Delete
                                                            if ($opening_account) {
                                                                $candelete = 'no';
                                                            } else {
                                                                $candelete = 'Yes';
                                                            }
                                                        @endphp

                                                        @if ($candelete == 'Yes')
                                                            <button class="btn deletebtn" data-id="{{ $row->id }}">
                                                                <i class="fa-solid fa-trash iconsColorCustom"></i>
                                                            </button>
                                                        @endif
                                                    </td>
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
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"> Add Scheme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form name="sechemeMasterForm" id="sechemeMasterForm">
                    <div class="modal-body">
                        <div class="row row-gap-2">
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="text" name="start_date" class="form-control formInputsReport mydatepic"
                                    id="start_date" value="{{ Session::get('currentdate') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select name="memberType" class=" form-select formInputsSelectReport" id="memberType">
                                    <option value="Member" default selected>Member</option>
                                    <option value="NonMember">Nominal Member</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="depositType" class="form-label">Secheme Type</label>
                                <select name="depositType"
                                    class="depositType form-select formInputsSelectReport thisdepositType" id="depositType"
                                    onchange="hideInterestType(this)">
                                    <option value="Saving">Saving</option>
                                    <option value="FD">FD</option>
                                    <option value="RD">RD</option>
                                    <option value="MIS">MIS</option>
                                    <option value="CDS">CDS</option>
                                    <option class="this_is_daily" value="Daily Loan">Daily Loan</option>
                                    <option class="this_is_daily" value="DailyDeposit">Daily Deposit</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="sechemeName" class="form-label">Secheme Name</label>
                                <input type="text" name="sechemeName" class="form-control formInputsReport"
                                    id="sechemeName" oninput="generatesechemecode('this')" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="sechemeName" class="form-label">Secheme Code</label>
                                <input type="text" name="scheme_code" readonly class="form-control formInputsReport"
                                    id="scheme_code" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="interestType" class="form-label">ROI Type</label>
                                <select name="interestType" class="  form-select formInputsSelectReport"
                                    id="interestType">
                                    <option value="Simple" default selected>Simple</option>
                                    <option value="Special">Special</option>
                                    <option value="SeniorCitizen">Senoir Citizen</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport">
                                <label for="rateofinterest" class="form-label">ROI %</label>
                                <input type="text" name="rateofinterest" value="0"
                                    class="form-control formInputsReport" id="rateofinterest" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport">
                                <label for="prematureDeduction" class="form-label">Penalty Interest</label>
                                <input type="text" name="prematureDeduction" value="0"
                                    class="form-control formInputsReport" id="prematureDeduction" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport" id="schemetttttt">
                                <label for="depType" class="form-label">Deposit Type</label>
                                <select name="depType" class="depType  form-select formInputsSelectReport"
                                    id="depType">
                                    <option value="" default selected>Select Dep Type</option>
                                    <option value="Days">Daily</option>
                                    <option class="hide_on_daily" value="Monthly">Monthly</option>
                                    <option class="hide_on_daily" value="Yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport forfdonly">
                                <label class="form-label mb-1" for="renewInterestType">Interest Type</label>
                                <select name="renewInterestType" id="renewInterestType"
                                    class="formInputsSelectReport form-select   Select" data-placeholder="Active">
                                    <option value="Fixed">Fixed</option>
                                    <option value="AnnualCompounded">Annual Compounded</option>
                                    <option value="QuarterlyCompounded">Quarterly Compounded</option>
                                </select>
                                <p class="error"></p>
                            </div>


                            @php $fdtt=DB::table('fd_type_master')->get(); @endphp
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport forfdonly">
                                <label class="form-label mb-1" for="fdType">FD Type</label>
                                <select name="fdType" id="fdType" class="formInputsSelectReport form-select   Select"
                                    data-placeholder="Active">
                                    @if (sizeof($fdtt) > 0)
                                        @foreach ($fdtt as $fdttlist)
                                            <option value="{{ $fdttlist->id }}">{{ $fdttlist->type }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>

                            <hr>
                            <p>
                                Tenure
                            </p>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport yeardiv" id="yeardiv">
                                <label for="years" class="form-label">Year</label>
                                <input type="text" value="1" name="years"
                                    class="form-control formInputsReport" id="years" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport monthdiv" id="monthdiv">
                                <label for="months" class="form-label">Month</label>
                                <input type="text" value="0" name="months"
                                    class="form-control formInputsReport" id="months" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport daysdiv" id="daysdiv">
                                <label for="days" class="form-label">Days</label>
                                <input type="text" name="days" value="0"
                                    class="form-control formInputsReport" id="days" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport lockin_daysdiv"
                                id="lockin_daysdiv">
                                <label for="days" class="form-label">lock-in Days</label>
                                <input type="text" name="lockin_days" value="0"
                                    class="form-control formInputsReport" id="lockin_days" />
                            </div>
                            <hr>

                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce--dropdown">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class=" form-select formInputsSelectReport" id="status"
                                    data-placeholder="Active" onchange="inactiveSatuts('this')">
                                    <option value="Active" default selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            {{--  <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport" id="enddatediv" style="display:none;">
                            <label for="memberType" class="form-label">End Date</label>
                            <input type="text" name="end_date" class="form-control formInputsReport" id="end_date" value="{{ now()->format('d-m-Y') }}"/>
                        </div>  --}}
                        </div>
                    </div>
                    <div class="modal-footer me-0">
                        <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0"
                            data-bs-dismiss="modal">
                            Close
                        </button>

                        <button id="submitButton"
                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
                            data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editmodal" tabindex="-1" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Update Scheme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form name="editschemeeMasterForm" id="editschemeeMasterForm">
                    <div class="modal-body">
                        <div class="row row-gap-2">
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="text" name="start_date" class="form-control formInputsReport mydatepic"
                                    id="edit_start_date" value="{{ now()->format('d-m-Y') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="memberType" class="form-label">Member Type</label>
                                <select name="memberType" class=" form-select formInputsSelectReport"
                                    id="edit_memberType">
                                    <option value="Member" default selected>Member</option>
                                    <option value="NonMember">Nominal Member</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="depositType" class="form-label">Secheme Type</label>
                                <select name="depositType"
                                    class="depositType form-select formInputsSelectReport thisdepositType"
                                    id="edit_depositType" onchange="hideInterestType(this)">
                                    <option value="Saving" default selected>Saving</option>
                                    <option value="FD">FD</option>
                                    <option value="RD">RD</option>
                                    <option value="MIS">MIS</option>
                                    <option value="CDS">CDS</option>
                                    <option class="this_is_daily" value="Daily Loan">Daily Loan</option>
                                    <option class="this_is_daily" value="DailyDeposit">Daily Deposit</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="sechemeName" class="form-label">Secheme Name</label>
                                <input type="text" name="sechemeName" class="form-control formInputsReport"
                                    id="edit_sechemeName" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="interestType" class="form-label">ROI Type</label>
                                <select name="interestType" class=" form-select formInputsSelectReport"
                                    id="edit_interestType">
                                    <option value="Simple" default selected>Simple</option>
                                    <option value="Special">Special</option>
                                    <option value="SeniorCitizen">Senoir Citizen</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="rateofinterest" class="form-label">ROI %</label>
                                <input type="text" name="rateofinterest" value="0"
                                    class="form-control formInputsReport" id="edit_rateofinterest" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="prematureDeduction" class="form-label">Penalty Interest</label>
                                <input type="text" name="prematureDeduction" value="0"
                                    class="form-control formInputsReport" id="edit_prematureDeduction" />
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport">
                                <label for="depType" class="form-label">Deposit Type</label>
                                <select name="depType" class=" depType form-select formInputsSelectReport"
                                    id="edit_depType">
                                    <option value="Days">Daily</option>
                                    <option class="hide_on_daily" value="Monthly">Monthly</option>
                                    <option class="hide_on_daily" value="Yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport forfdonly">
                                <label class="form-label mb-1" for="edit_renewInterestType">Interest Type</label>
                                <select name="renewInterestType" id="edit_renewInterestType"
                                    class=" formInputsSelectReport form-select   Select" data-placeholder="Active">
                                    <option value="Fixed">Fixed</option>
                                    <option value="AnnualCompounded">Annual Compounded</option>
                                    <option value="QuarterlyCompounded">Quarterly Compounded</option>
                                </select>
                                <p class="error"></p>
                            </div>

                            <hr>
                            <p>
                                Tenure
                            </p>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport yeardiv" id="edit_yeardiv">
                                <label for="years" class="form-label">Year</label>
                                <input type="text" value="1" name="years"
                                    class="form-control formInputsReport" id="edit_years" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport monthdiv" id="edit_monthdiv">
                                <label for="months" class="form-label">Month</label>
                                <input type="text" value="0" name="months"
                                    class="form-control formInputsReport" id="edit_months" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport daysdiv" id="edit_daysdiv">
                                <label for="days" class="form-label">Days</label>
                                <input type="text" name="days" value="0"
                                    class="form-control formInputsReport" id="edit_days" />
                            </div>
                            <div class="col-lg-3 col-sm-3 col-6 py-2 inputesPaddingReport lockin_daysdiv"
                                id="edit_lockin_daysdiv">
                                <label for="days" class="form-label">lock-in Days</label>
                                <input type="text" name="lockin_days" value="0"
                                    class="form-control formInputsReport" id="edit_lockin_days" />
                            </div>
                            <hr>

                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce--dropdown">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="edit_status  form-select formInputsSelectReport"
                                    id="edit_status" data-placeholder="Active" onchange="krokuj()">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <script>
                                function krokuj() {
                                    var edit_status = $('#edit_status').val();
                                    if (edit_status == 'Inactive') {
                                        $('#edit_enddatediv').css('display', 'block');
                                    } else {
                                        $('#edit_enddatediv').css('display', 'none');
                                    }
                                }
                            </script>
                            <div class="col-lg-4 col-sm-4 col-6 py-2 inputesPaddingReport  " id="edit_enddatediv"
                                style="display:none;">
                                <label for="memberType" class="edit_enddate form-label">End Date</label>
                                <input type="text" name="edit_end_date"
                                    class="mydatepic form-control formInputsReport" id="edit_end_date"
                                    value="{{ now()->format('d-m-Y') }}" />
                            </div>
                        </div>
                        <input type="text" hidden name="updateid" id="updateid">

                    </div>
                    <div class="modal-footer me-0">
                        <button type="button" class="btn btn-label-secondary reportSmallBtnCustom m-0"
                            data-bs-dismiss="modal">
                            Close
                        </button>

                        <button id="submitButton"
                            class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit"
                            data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
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
    </style>
@endpush

@push('script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script type="text/javascript">
        //__________Generate New Scheme Code
        function generatesechemecode() {
            let sechemeName = $('#sechemeName').val();

            $.ajax({
                url: "{{ route('generateschemecode') }}",
                type: 'post',
                data: {
                    sechemeName: sechemeName
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#scheme_code').val(res.newgroup_code);
                    } else {
                        alert(res.messages);
                    }
                }
            });
        }

        function addSetup() {

            $('#basicModal').find('.msg').text("Add");
            $('#basicModal').find('input[name="id"]').val("new");
            $('#basicModal').modal('show');
        }


        function inactiveSatuts() {
            //_________Secheme End Date Div
            let status = document.getElementById('status').value;
            let enddatediv = document.getElementById('enddatediv');

            if (status === 'Inactive') {
                enddatediv.style.display = 'block';
            } else {
                enddatediv.style.display = 'none';
            }
        }


        $(document).ready(function() {
            const validationRules = {
                renewInterestType: {
                    required: true
                },
                start_date: {
                    required: true
                },
                memberType: {
                    required: true
                },
                depositType: {
                    required: true
                },
                sechemeName: {
                    required: true,
                    minlength: 3
                },
                rateofinterest: {
                    required: true,
                    number: true
                },
                prematureDeduction: {
                    required: true,
                    number: true
                },
                depType: {
                    required: true
                },
                years: {
                    required: true,
                    number: true
                },
                months: {
                    required: true,
                    number: true
                },
                days: {
                    required: true,
                    number: true
                },
                lockin_days: {
                    required: true,
                    number: true
                },
                status: {
                    required: true
                }
            };

            const validationMessages = {
                renewInterestType: {
                    required: "Please select a interest type.",
                },
                start_date: {
                    required: "Please select a start date.",
                },
                memberType: "Please select member type.",
                depositType: "Please select deposit type.",
                sechemeName: {
                    required: "Please enter scheme name.",
                    minlength: "Scheme name must be at least 3 characters long."
                },
                rateofinterest: {
                    required: "Please enter the rate of interest.",
                    number: "Please enter a valid number.",
                },
                prematureDeduction: {
                    required: "Please enter penalty interest.",
                    number: "Please enter a valid number."
                },
                depType: "Please select deposit type.",
                years: {
                    required: "Please enter the number of years.",
                    number: "Please enter a valid number.",
                    greaterThanZero: "Years must be greater than 0."
                },
                months: {
                    required: "Please enter the number of months.",
                    number: "Please enter a valid number.",
                    greaterThanZero: "Months must be greater than 0."
                },
                days: {
                    required: "Please enter the number of days.",
                    number: "Please enter a valid number.",
                    greaterThanZero: "Days must be greater than 0."
                },
                lockin_days: {
                    required: "Please enter lock-in days.",
                    number: "Please enter a valid number.",
                    greaterThanZero: "Lock-in days must be greater than 0."
                },
                status: "Please select status."
            };


            $.validator.addMethod("greaterThanZero", function(value, element) {
                return this.optional(element) || parseInt(value) > 0;
            }, "Must be greater than 0.");
            $('#sechemeMasterForm').validate({
                rules: validationRules,
                messages: validationMessages,
                submitHandler: function(form) {
                    event.preventDefault();

                    let formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('deposit-secheme-insert') }}",
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success(res.messages);
                                $('.table').load(location.href + ' .table');
                                $('#sechemeMasterForm')[0].reset();
                                $('#basicModal').modal('hide');
                            }
                        }
                    });
                }
            });



            $(document).on('click', '.deletebtn', function(event) {
                event.preventDefault();

                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('deposit-delete-sechemes') }}",
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
                            {{--  $('#datatable').dataTable().api().ajax.reload();  --}}
                            swal({
                                type: 'success',
                                title: 'Success',
                                text: "Data Successfully Deleted",
                                showConfirmButton: true,
                            });
                            $('.table').load(location.href + ' .table');
                        }
                    }
                });
            });


            $(document).on('click', '.editbtn', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                // let enddatediv = document.getElementById('enddatediv');
                // enddatediv.style.display = 'block';
                $('#editmodal').modal('show');
                $('#updateid').val(id);
                let url = "{{ route('scheme.details', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let scheme = response.data;
                            $('#edit_start_date').val(scheme.start_date ? new Date(scheme
                                .start_date).toLocaleDateString('en-GB') : '');
                            $('#edit_memberType').val(scheme.memberType);
                            $('#edit_depositType').val(scheme.secheme_type).on('mousedown', function (e) {
                                e.preventDefault();
                            });

                            $('#edit_sechemeName').val(scheme.name);
                            $('#edit_interestType').val(scheme.interest_type);
                            $('#edit_rateofinterest').val(scheme.interest);
                            $('#edit_prematureDeduction').val(scheme.penaltyInterest);
                            $('#edit_depType').val(scheme.durationType);
                            $('#edit_renewInterestType').val(scheme.renewInterestType);
                            $('#edit_years').val(scheme.years);
                            $('#edit_months').val(scheme.months);
                            $('#edit_days').val(scheme.days);
                            $('#edit_lockin_days').val(scheme.lockin_days);
                            $('#edit_status').val(scheme.status);
                        }
                    },
                    error: function(err) {
                        console.error('Error fetching scheme details:', err);
                    }
                });
            });

            document.getElementById('editschemeeMasterForm').addEventListener('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    url: "{{ route('update-sechemes-enddate') }}",
                    type: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status === 'success') {
                            toastr.success(res.messages);
                            $('.table').load(location.href + ' .table');
                            $('#sechemeMasterForm')[0].reset();
                            $('#editmodal').modal('hide');
                        } else {
                            toastr.error(res.messages);

                        }
                    }
                });
            });
        });

        $(document).ready(function() {
            $(".depType .hide_on_daily").hide();

            $('.depositType').on('change', function() {
                var selectedValue = $(this).val();

                if (selectedValue === 'Daily Loan' || selectedValue === 'DailyDeposit') {
                    $(".depType .hide_on_daily").hide();
                    $(".depType option[value='Days']").show();
                } else {
                    // Show options with class 'hide_on_daily' and hide Daily
                    $(".depType .hide_on_daily").show();
                    $(".depType option[value='Days']").hide();
                }
            });
        });


        function hideInterestType(element) {
            $('.forfdonly').toggle(element.value === "FD");
            $('.daysdiv').show();
            $('.monthdiv').show();
            $('.yeardiv').show();
            $('.lockin_daysdiv').show();
            if (element.value === 'Daily Loan') {
                $('.monthdiv').hide();
                $('.yeardiv').hide();
                $('.lockin_daysdiv').hide();
                $('#schemetttttt').show();
            } else if (element.value === 'RD') {
                $('.daysdiv').hide();
                $('.yeardiv').hide();
                $('#schemetttttt').show();
            } else if (element.value === 'DailyDeposit') {
                $('.monthdiv').hide();
                $('.yeardiv').hide();
                $('.lockin_daysdiv').hide();
                $('#schemetttttt').show();
            }else{
                $('.monthdiv').hide();
                $('.yeardiv').hide();
                $('.lockin_daysdiv').hide();
                $('#schemetttttt').hide();
            }
        }

        $(document).ready(function() {
            hideInterestType(document.querySelector('.thisdepositType'));
        });
    </script>
@endpush
