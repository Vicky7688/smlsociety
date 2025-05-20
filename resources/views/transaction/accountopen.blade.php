@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card page_headings mb-4 cards">
            <div class="card-body py-2">
                <h4 class="py-2"><span class="text-muted fw-light">Account / </span>Member Account</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 cards">
                <div class="card">
                    <div class="card-body pb-1 cardHeadingTitle">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs gap-2 account_tabs" id="myTabs" role="tablist">
                                    <li class="nav-item d-flex" role="presentation">
                                        <a class="nav-link active" id="account-details-tab" data-bs-toggle="tab"
                                            href="#account-details" role="tab" aria-controls="account-details"
                                            aria-selected="true" style="width: 300px;">
                                            Account Details
                                        </a>
                                        <a class="nav-link active" href="{{ route('accountopen.page') }}">Reset</a>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="address-details-tab" data-bs-toggle="tab"
                                            href="#address-details" role="tab" aria-controls="address-details"
                                            aria-selected="false">
                                            Address Details
                                        </a>
                                    </li> --}}
                                    {{-- <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="nominee-details-tab" data-bs-toggle="tab"
                                            href="#nominee-details" role="tab" aria-controls="nominee-details"
                                            aria-selected="false">
                                            Nominee Details
                                        </a>
                                    </li> --}}
                                </ul>
                                <script>
                                    function resetform() {
                                        var memberTypeValue = $('#membertype').val();
                                        $('#openaccountdetails')[0].reset();
                                        $('#membertype').val(memberTypeValue);
                                        $("#member_ship_no").prop('disabled', false);
                                        $('#blah').attr('src', 'http://placehold.it/180');
                                        $("#upload").attr('src', 'http://placehold.it/180');
                                        $("#upload3").attr('src', 'http://placehold.it/180');
                                    }
                                </script>
                                <div class="tab-content mt-2" id="myTabsContent">
                                    <div class="tab-pane fade show active" id="account-details" role="tabpanel"
                                        aria-labelledby="account-details-tab">
                                        <!-- Content for Account Details tab -->
                                        <form id="openaccountdetails" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input type="hidden" id="member_id" name="id" , value="new" />
                                            <div class="account-details-modern">
                                                <!-- <div class="account_details_inner"> -->
                                                <div class="row row-gap-2">
                                                    @php
                                                        $sessionId = Session::get('sessionId');
                                                        $session = DB::table('session_masters')
                                                            ->where('id', $sessionId)
                                                            ->first();
                                                        $startDate = date('d-m-Y');

                                                        if ($session) {
                                                            $currentYear = date('Y');
                                                            $sessionStartYear = date(
                                                                'Y',
                                                                strtotime($session->startDate),
                                                            );

                                                            if ($sessionStartYear < $currentYear) {
                                                                $startDate = date(
                                                                    'd-m-Y',
                                                                    strtotime($session->startDate),
                                                                );
                                                            }
                                                        }
                                                    @endphp

                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="opening date">OPENING
                                                            DATE</label>
                                                        <input type="text" id="openingdate" name="openingdate"
                                                            class="form-control form-control-sm transactionDate"
                                                            value="{{ $startDate }}" required>
                                                    </div>

                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="MEMBERTYPE">MEMBER TYPE</label>
                                                        <select name="membertype" id="membertype"
                                                            class="form-select form-select-sm" onchange="resetform()">
                                                            <option value="Member">Member</option>
                                                            <option value="Staff">Staff</option>
                                                            <option value="NonMember">Nominal Member</option>
                                                        </select>
                                                    </div>
                                                    {{-- <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="ACCTYPE">ACC TYPE</label>
                                                        <input type="text" id="account_type" value="Single"
                                                            class="form-control form-control-sm" name="account_type"
                                                            autocomplete="off">
                                                    </div> --}}
                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding saving_column">
                                                        <label class="form-label" for="MEMBERSHIPNO">Employee Code</label>
                                                        <input type="text" id="member_ship_no"
                                                            onchange="handleMembershipfun(this)"
                                                            class="form-control form-control-sm" name="member_ship_no"
                                                            autocomplete="off">
                                                    </div>
                                                    <input type="hidden" id="member_ac_no" name="member_ac_no">
                                                    <!-- </div> -->
                                                    <!-- </div> -->
                                                    <!-- <div class="account_details_inner"> -->
                                                    <!-- <div class="row row-gap-2"> -->
                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                                        <label class="form-label" for="NAME">NAME</label>
                                                        <input type="text" id="name"
                                                            class="form-control form-control-sm" name="name"
                                                            autocomplete="off">
                                                    </div>
                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                                        <label class="form-label" for="USERTYPE">FATHER/HUSBAND</label>
                                                        <input type="text" name="father_husband" id="father_husband"
                                                            class="form-control form-control-sm" autocomplete="off">
                                                    </div>

                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 form-group inputesPadding">
                                                        <label class="form-label" for="GENDER">GENDER</label>
                                                        <select name="gender" id="gender"
                                                            class="form-select form-select-sm">
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>

                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                                        <label class="form-label" for="department">Department</label>
                                                        <input type="text" id="department"
                                                            class="form-control form-control-sm" name="department"
                                                            autocomplete="off">
                                                    </div>
                                                    <div
                                                        class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 saving_column inputesPadding">
                                                        <label class="form-label" for="designation">Designation</label>
                                                        <input type="text" id="designation"
                                                            class="form-control form-control-sm" name="designation"
                                                            autocomplete="off">
                                                    </div>
                                                    <!-- </div> -->
                                                    <!-- </div> -->
                                                    <!-- <div class="account_details_inner"> -->
                                                    <!-- <div class="row row-gap-2"> -->
                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="pan_number">PAN NO.</label>
                                                        <input type="text" id="pan_number"
                                                            class="form-control form-control-sm" name="pan_number"
                                                            maxlength="10" oninput="this.value = this.value.toUpperCase()"
                                                            autocomplete="off">
                                                    </div>

                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="DATEOFBIRTH">DATE OF
                                                            BIRTH</label>
                                                        <input type="text" id="member_dob"
                                                            class="form-control form-control-sm" name="member_dob"
                                                            value="{{ date('d-m-Y') }}">
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="ADHAARNO">ADHAAR NO.</label>
                                                        <input type="text" id="adhaar_no"
                                                            class="form-control form-control-sm" name="adhaar_no"
                                                            maxlength="12" minlength="12"
                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                            pattern="[0-9]{12}" inputmode="numeric" autocomplete="off">
                                                    </div>
                                                    <!-- </div> -->
                                                    <!-- </div> -->
                                                    <!-- <div class="account_details_inner"> -->
                                                    <!-- <div class="row row-gap-2"> -->

                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="PAGENO">PAGE NO.</label>
                                                        <input type="text" id="page_no"
                                                            class="form-control form-control-sm" name="page_no">
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="LEDGERNo">LEDGER NO.</label>
                                                        <input type="text" id="ledger_no"
                                                            class="form-control form-control-sm" name="ledger_no">
                                                    </div>
                                                    {{-- <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="EMPLOYEECODE">EMPLOYEE
                                                            CODE</label>
                                                        <input type="text" id="emp_code"
                                                            class="form-control form-control-sm" name="emp_code">
                                                    </div> --}}
                                                    {{-- <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                        <label class="form-label" for="AGENT">AGENT</label>
                                                        <select name="agent" id="agent"
                                                            class="form-select form-select-sm">
                                                            <option value="">Select Agent</option>
                                                            @foreach ($agents as $agent)
                                                                <option value="{{ $agent->id }}">{{ $agent->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div> --}}
                                                </div>
                                                <!-- </div> -->
                                            </div>
                                            <h2>Address</h2>
                                            <div class="row row-gap-2">
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="ADDRESS">ADDRESS</label>
                                                    <input type="text" id="address" name="address"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="STATE">STATE</label>
                                                    <input type="text" id="state" name="state"
                                                        class="form-control form-control-sm">
                                                    {{-- <select name="state" id="state"
                                                        class="form-select form-select-sm" onchange="getDistrict(this)">
                                                        <option value="">Select State</option>
                                                        @foreach ($state as $state)
                                                            <option id="{{ $state->id }}"
                                                                value="{{ $state->name }}">
                                                                {{ $state->name }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="DISTRICT">DISTRICT</label>
                                                    <input type="text" id="districtId" name="districtId"
                                                        class="form-control form-control-sm">
                                                    {{-- <select name="districtId" id="districtId" onchange="gettehsil(this)"
                                                        class="form-select form-select-sm">
                                                        <option value="">Select</option> --}}
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="TEHSIL">TEHSIL</label>
                                                    <input type="text" id="tehsilId" name="tehsilId"
                                                        class="form-control form-control-sm">
                                                    {{-- <select name="tehsilId" id="tehsilId" onchange="getvillage(this)"
                                                        class="form-select form-select-sm">
                                                        <option value="">Select</option>
                                                    </select> --}}
                                                </div>
                                                <!-- </div>

                                                        <div class="row row-gap-2"> -->
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="VILLAGE POST">VILLAGE POST</label>
                                                    <input type="text" id="villageId" name="villageId"
                                                        class="form-control form-control-sm">
                                                    {{-- <select name="villageId" id="villageId"
                                                        class="form-select form-select-sm">
                                                        <option value="">Select</option>
                                                    </select> --}}
                                                </div>
                                                {{-- <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="WARD NO">WARD NO</label>
                                                    <input type="text" id="ward_no" name="ward_no"
                                                        class="form-control form-control-sm">
                                                </div> --}}
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="CONTACT NO.">CONTACT NO.</label>
                                                    <input type="text" id="contact_no" name="contact_no"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <h2>Nominee Detail</h2>
                                            <div class="row row-gap-2">
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="NOMINEENAME">NOMINEE
                                                        NAME</label>
                                                    <input type="text" id="nominee_name" name="nominee_name"
                                                        class="form-control form-control-sm">
                                                </div>

                                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="RELATION">RELATION</label>
                                                    <input type="text" id="relation" name="relation"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="AdharNo">Adhaar No</label>
                                                    <input type="text" id="nomineeadhaarno" name="nomineeadhaarno"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="NOMINEEADDRESS">ADDRESS</label>
                                                    <input type="text" id="nominee_address" name="nominee_address"
                                                        class="form-control form-control-sm">
                                                </div>
                                                {{-- <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="AGE">AGE</label>
                                                    <input type="text" id="age" name="age"
                                                        class="form-control form-control-sm">
                                                </div> --}}

                                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPadding">
                                                    <label class="form-label" for="CONTACTNO">CONTACT NO</label>
                                                    <input type="text" id="nomineecontact_no" name="contact_no"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="row mt-4 uploadimagesdata">
                                                <div class="col-md-3 col-sm-4 pt-4 col-6 inputesPadding">
                                                    <div class="cardd bg-transparent">
                                                        <div class="card-body cardBodyCustom">
                                                            <div class="photo_upload text-center">
                                                                <label for="" class="form-label">ID Proof</label>
                                                                <div class="photo_upload_inner position-relative">
                                                                    <img src="http://placehold.it/180" id="blah"
                                                                        alt="Image" class="upload">
                                                                </div>
                                                                <div class="buttons">
                                                                    <!-- <button class="border-0 bg-white" type="button"> -->
                                                                    <label for="photo" class="custom-file-upload"><i
                                                                            class="fa-solid fa-cloud-arrow-up border-0 iconsColorCustom"></i></label>
                                                                    <!-- </button> -->
                                                                    <button class="close_btn" type="button"
                                                                        onclick="removeImg()"><i
                                                                            class="fa-solid fa-trash iconsColorCustom"></i>
                                                                    </button>
                                                                </div>
                                                                <input class="inputFile" type="file" id="photo"
                                                                    name="photo" onchange="readUrl(this)"
                                                                    value="{{ old('photo') }}" style="display: none;">
                                                                <input type="hidden" id="isavl1" name="isavl1"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-4 pt-4 col-6 inputesPadding">
                                                    <div class="cardd bg-transparent">
                                                        <div class="card-body cardBodyCustom">
                                                            <div class="photo_upload text-center">
                                                                <label for="" class="form-label">Photo</label>
                                                                <div class="photo_upload_inner position-relative">
                                                                    <img src="http://placehold.it/180" id="upload"
                                                                        alt="Image" class="upload">
                                                                </div>
                                                                <div class="buttons">
                                                                    <label for="photoo" class="custom-file-upload"><i
                                                                            class="fa-solid fa-cloud-arrow-up border-0 iconsColorCustom"></i></label>
                                                                    <button class="close_btn" type="button"
                                                                        onclick="removeImgB()"><i
                                                                            class="fa-solid fa-trash iconsColorCustom"></i>
                                                                    </button>
                                                                </div>
                                                                <input class="inputFile" type="file" id="photoo"
                                                                    name="photoo" onchange="readUrlB(this)"
                                                                    value="{{ old('photo') }}" style="display: none;">
                                                                <input type="hidden" id="isavl2" name="isavl2"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-4 pt-4 col-6 inputesPadding">
                                                    <div class="cardd bg-transparent">
                                                        <div class="card-body cardBodyCustom">
                                                            <div class="photo_upload text-center">
                                                                <label for="" class="form-label">Signature</label>
                                                                <div class="photo_upload_inner position-relative">
                                                                    <img src="http://placehold.it/180" id="upload3"
                                                                        alt="Image" class="upload">
                                                                </div>
                                                                <div class="buttons">
                                                                    <label for="photo3" class="custom-file-upload"><i
                                                                            class="fa-solid fa-cloud-arrow-up border-0 iconsColorCustom"></i></label>
                                                                    <button class="close_btn" type="button"
                                                                        onclick="removeImg3()"><i
                                                                            class="fa-solid fa-trash iconsColorCustom"></i>
                                                                    </button>
                                                                </div>
                                                                <input class="inputFile" type="file" id="photo3"
                                                                    name="photo3" onchange="readUrl3(this)"
                                                                    value="{{ old('photo') }}" style="display: none;">
                                                                <input type="hidden" id="isavl3" name="isavl3"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="button justify-content-end text-end pt-3" id="submitbtns">
                                                <button type="submit"
                                                    class="btn btn-primary px-4 reportSmallBtnCustom">Save</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="nominee-details" role="tabpanel"
                                        aria-labelledby="nominee-details-tab">
                                        <!-- Content for Nominee Details tab -->
                                        <form action="javascript:void(0)" id="nomineedetailsform">
                                            <div class="">
                                                <div class="">

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="tabledata card tablee">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datatables-order table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenter">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Name</th>
                                        <th>A/c No </th>
                                        <th>Member Type</th>
                                        {{-- <th>Acc Type</th> --}}
                                        <th>Contact</th>
                                        {{-- <th>Status</th> --}}
                                        <th>Created by</th>
                                        <th>AC Opening Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {{-- @if (empty($memberacc->memberType) && empty($memberacc->accountNo) && empty($memberacc->accountType) && empty($memberacc->name) && empty($memberacc->phone) && empty($memberacc->openingDate))
                                            <td colspan="11">All values are empty</td>
                                        @else --}}
                                    @isset($memberacc)
                                        @foreach ($memberacc as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->accountNo }}</td>
                                                <td>{{ $row->memberType }}</td>
                                                {{-- <td>{{ $row->accountType }}</td> --}}
                                                <td>{{ $row->panNo }}</td>
                                                {{-- <td>{{ $status }}</td> --}}
                                                @php
                                                    $createdby = DB::table('users')
                                                        ->where('id', $row->updatedBy)
                                                        ->value('username');
                                                @endphp

                                                <td>{{ $createdby }}</td>
                                                <td>{{ \Carbon\Carbon::parse($row->openingDate)->format('d-m-Y') }}</td>

                                                <td><button class="btn deletebtn" data-id="{{ $row->id }}">
                                                    <i class="fa-solid fa-trash iconsColorCustom"></i>
                                                </button></td>
                                            </tr>
                                        @endforeach
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="membershipmodel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered loan_modal">
            <div class="modal-content w-60 mx-auto custom-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="memberaccountno">
                <div class="modal-body text-center custom-modal-body">
                    <p>Account Number Already Exist</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="viewmemberdetails">view</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .custom-file-upload {
            /* background-color: #7367f0; */
            color: black;
            /* padding: 8px 10px; */
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
            border-radius: 5px;
            object-fit: cover;
            position: relative;
        }

        .img-hover-effect {
            transition: transform 0.3s ease;
        }

        .img-hover-effect:hover {
            transform: scale(1.1);
        }

        .close_btn {
            /* background-color: #9F0000; */
            color: black;
            /* padding: 5px 8px; */
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

        .table_head tr {
            background-color: #7367f0;
        }

        .table_head tr th {
            color: #fff !important;
        }

        .page_headings h4 {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('script')
    <script>
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
            $('#isavl1').val('remove');
        }

        var b = document.getElementById("upload");
        var photob = document.getElementById("photoo");

        function readUrlB(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = (e) => {
                    b.src = e.target.result;
                };
            }
        }

        function removeImgB() {
            b.src = "http://placehold.it/180";
            photob.value = "";
            $('#isavl2').val('remove');
        }


        var c = document.getElementById("upload3");
        var photoc = document.getElementById("photoo");

        function readUrl3(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = (e) => {
                    c.src = e.target.result;
                };
            }
        }

        function removeImg3() {
            c.src = "http://placehold.it/180";
            photoc.value = "";
            $('#isavl3').val('remove');
        }



        //if Account Type is Joint Account

        function readurlc(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function(e) {
                    var d = document.getElementById("signature2person");
                    if (d) {
                        d.src = e.target.result;
                    } else {
                        console.error('Element with ID "signature2person" not found.');
                    }
                };
            }
        }

        function removeNewImage() {
            var d = document.getElementById("signature2person");
            var dinput = document.getElementById("signature2img");

            if (d) {
                d.src = "http://placehold.it/180";
            } else {
                console.error('Element with ID "signature2person" not found.');
            }
        }


        function readUrle(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function(e) {
                    var f = document.getElementById("imgphoto2screen");
                    if (f) {
                        f.src = e.target.result;
                    } else {
                        console.error('Element with ID "" not found.');
                    }
                };
            }
        }

        function removeImgphototimg() {
            var f = document.getElementById("imgphoto2screen");
            var finput = document.getElementById("imgphoto2");
            if (f) {
                f.src = "http://placehold.it/180";
            } else {
                console.error('Element with ID "signature2person" not found.');
            }

        }

        function readUrlg(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function(e) {
                    var g = document.getElementById("userprofilescreen3");
                    if (g) {
                        g.src = e.target.result;
                    } else {
                        console.error('Element with ID "" not found.');
                    }
                };
            }

        }

        function removeImgphotoprofile3remove() {
            var g = document.getElementById("userprofilescreen3");
            var ginput = document.getElementById("useridprove");
            if (g) {
                g.src = "http://placehold.it/180";
            } else {
                console.error('Element with ID "signature2person" not found.');
            }
        }

        function toggleDiv() {
            var accountTypeSelect = document.getElementById("account_type");
            var jointDiv = document.getElementById("jointDiv");

            if (accountTypeSelect.value === "Joint") {
                jointDiv.style.display = "block";
            } else {
                jointDiv.style.display = "none";
            }
        }
    </script>
    <script>
        function handleMembershipfun(inputElement) {
            var newMembershipNo = inputElement.value;
            var membertype = $("#membertype").val();
            axios.post('{{ route('account.search') }}', {
                'memberaccno': newMembershipNo,
                'membertype': membertype
            }).then((response) => {
                if (response.data.status == "success") {
                    $("#memberaccountno").val(response.data.data);
                    $("#member_ship_no").val('');
                    $("#membershipmodel").modal('show');
                }
            });
        }

        function getDistrict(ele) {
            axios.post("{{ route('accountupdate') }}", {
                    actiontype: "getdistrict",
                    stateid: $(ele).find(':selected').attr('id')
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                })
                .then(function(response) {
                    var out = `<option value="">Select District</option>`;
                    response.data.dist.forEach(function(value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="districtId"]').html(out);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }

        function gettehsil(ele) {
            axios.post("{{ route('accountupdate') }}", {
                    actiontype: "gettehsil",
                    distId: $(ele).find(':selected').attr('id')
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function(response) {
                    var out = `<option value="">Select Tehsil</option>`;
                    response.data.data.forEach(function(value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="tehsilId"]').html(out);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }

        function getvillage(ele) {
            axios.post("{{ route('accountupdate') }}", {
                    actiontype: "getvillage",
                    tehsilId: $(ele).find(':selected').attr('id'),
                    stateId: $('#state').find(':selected').attr('id'),
                    districtId: $('#districtId').find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function(response) {
                    var out = `<option value="">Select Village</option>`;
                    response.data.data.forEach(function(value) {
                        out += `<option value="${value.id}">${value.name}</option>`;
                    });
                    $('[name="villageId"]').html(out);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }

        function getjointvillage(ele) {
            axios.post("{{ route('accountupdate') }}", {
                    actiontype: "getvillage",
                    tehsilId: $(ele).find(':selected').attr('id'),
                    stateId: $('#joint_state').find(':selected').attr('id'),
                    districtId: $('#joint_districtId').find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function(response) {
                    var out = `<option value="">Select Village</option>`;
                    response.data.data.forEach(function(value) {
                        out += `<option value="${value.id}">${value.name}</option>`;
                    });
                    $('[name="villageId"]').html(out);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }
    </script>
    <script>
        {{--
      var currentDate = moment().format('DD-MM-YYYY');
      $("#openingdate").val(currentDate);  --}}

        $(document).ready(function() {

            $("#openaccountdetails").validate({
                rules: {
                    openingdate: {
                        required: true,
                        customDate: true,
                    },
                    father_husband: {
                        required: true,
                    },
                    membertype: {
                        required: true,
                    },
                    gender: {
                        required: true,
                    },
                    // account_type: {
                    //     required: true,
                    // },

                    member_ship_no: {
                        required: true,
                    },
                    page_no: {
                        number: true,
                    },

                    name: {
                        required: true,
                    },

                },
                messages: {
                    openingdate: {
                        required: "Please enter a date",
                        customDate: "Please enter a valid date in the format dd-mm-yyyy",
                    },
                    father_husband: "This field is required",
                    membertype: "This field is required",
                    gender: "This field is required",
                    // account_type: "This field is required",

                    member_dob: "This field is required",
                    member_ship_no: "This field is required",
                    page_no: {
                        number: "Please enter a valid number",
                    },
                    name: "This field is required",
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are processing data',
                        onOpen: () => {
                            swal.showLoading();
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                    var openingDateValue = $("#openingdate").val();
                    var formData = new FormData(form);
                    formData.append('opening_date', openingDateValue);
                    axios.post("{{ route('account.store') }}", formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then((response) => {
                            if (response.data.status == "success") {



                                $(".tabledata").load(location.href + ' .tabledata');
                                $('#openaccountdetails')[0].reset();
                                $('#blah').attr('src', 'http://placehold.it/180');
                                $("#upload").attr('src', 'http://placehold.it/180');
                                $("#upload3").attr('src', 'http://placehold.it/180');
                                var Date = moment().format('DD-MM-YYYY');
                                $("#openingdate").val(Date);
                                $("#submitbtns").empty();
                                $("#submitbtns").append(
                                    '<button type="submit" class="btn btn-primary px-4">Save</button>'
                                );
                                $("#member_ship_no").prop('disabled', false);
                                $("#jointDiv").hide();
                                // notify(response.data.message, 'success');
                                swal({
                                    type: 'success',
                                    title: 'Success',
                                    text: "Data Successfully Updated",
                                    showConfirmButton: true,
                                });
                            } else {
                                swal.close();
                                //  notify("Something went wrong", 'warning');
                                swal({
                                    type: 'error',
                                    title: 'Failed',
                                    text: "Something went wrong",
                                    showConfirmButton: true,
                                });
                            }
                        })
                        .catch(error => {
                            console.log(error.response);
                            // Check if the error is a 400 Bad Request error
                            if (error.response && error.response.status === 400) {
                                swal.close();
                                //  notify("Something went wrong", 'warning');
                                swal({
                                    type: 'error',
                                    title: 'Failed',
                                    text: error.response.data.message,
                                    showConfirmButton: true,
                                });
                            } else {
                                // Handle other errors
                                console.error('Error:', error.message);
                            }
                        })
                    // .finally(() => {
                    //       var form = $('#openaccountdetails');
                    //       showError(errors, form);
                    //     //swal.close();
                    // });

                }
            });

            $(document).on("submit", "#addressdetailsform", function(e) {
                e.preventDefault();
                var membershipno = $("#member_ship_no").val();
                var membertype = $("#membertype").val();
                if (membershipno.trim() === '' || membertype.trim() === '') {
                    // notify("Membership Number and Member Type are required", 'error');
                    // return;
                    alert("MembershipNo and MemberType required");
                } else {
                    var form = $(this);
                    var formData = new FormData(form[0]);
                    formData.append('memberid', membershipno);
                    formData.append('membertypeid', membertype);
                    axios.post("{{ route('account.address.page') }}", formData).then((
                        response) => {
                        if (response.data.status == "success") {
                            $(".tabledata").load(location.href + ' .tabledata');
                            $('#addressdetailsform')[0].reset();
                            swal({
                                type: 'success',
                                title: response.data.status,
                                text: response.data.message,
                                showConfirmButton: true,
                            });
                        } else {
                            console.log(response);
                        }
                    });
                }

            });

            $(document).on("submit", "#nomineedetailsform", function(e) {
                e.preventDefault();
                var membershipno = $("#member_ship_no").val();
                var membertype = $("#membertype").val();
                if (membershipno.trim() === '' || membertype.trim() === '') {
                    // notify("Membership Number and Member Type are required", 'error');
                    // return;
                    alert("MembershipNo and MemberType required");
                } else {
                    var form = $(this);
                    var formData = new FormData(form[0]);
                    formData.append('memberid', membershipno);
                    formData.append('membertypeid', membertype);
                    axios.post("{{ route('account.nomenee.page') }}", formData).then((response) => {
                        if (response.data.status == "success") {
                            $(".tabledata").load(location.href + ' .tabledata');
                            $('#nomineedetailsform')[0].reset();
                            swal({
                                type: 'success',
                                title: response.data.status,
                                text: response.data.message,
                                showConfirmButton: true,
                            });
                        } else {
                            console.log(response);
                        }
                    });
                }

            });


            $(document).on('click', '#viewmemberdetails', function(e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                var memberaccno = $("#memberaccountno").val();
                axios.post("{{ route('account.search.find') }}", {
                    'memberid': memberaccno
                }).then((response) => {
                    if (response.data.status == "success") {
                        $("#member_id").val(response.data.member.id);
                        $("#member_ship_no").val(response.data.member.accountNo);
                        $("#member_ac_no").val(response.data.member.accountNo);
                        $("#member_ship_no").prop('disabled', true);
                        $("#name").val(response.data.member.name);


                        var originalDate = new Date(response.data.member.openingDate);
                        var day = ('0' + originalDate.getDate()).slice(-2);
                        var month = ('0' + (originalDate.getMonth() + 1)).slice(-2);
                        var year = originalDate.getFullYear();
                        var formattedDate = `${day}-${month}-${year}`;


                        // alert(response.data.member.openingDate);
                        // alert(formattedDate);

                        // var formattedDate = moment(response.data.member.openingDate).format('DD-MM-YYYY');
                        $("#openingdate").val(formattedDate);
                        $("#membertype").val(response.data.member.memberType);
                        // $("#account_type").val(response.data.member.accountType);


                        $("#father_husband").val(response.data.member.fatherName);
                        $("#gender").val(response.data.member.gender);
                        $("#adhaar_no").val(response.data.member.aadharNo);
                        $("#pan_number").val(response.data.member.panNo);
                        $("#department").val(response.data.member.department);
                        $("#designation").val(response.data.member.designation);
                        $("#ledger_no").val(response.data.member.ledgerNo);
                        $("#page_no").val(response.data.member.pageNo);
                       $("#member_dob").val(moment(response.data.member.birthDate).format('DD-MM-YYYY'));
                        $("#emp_code").val(response.data.member.employeeCode);
                        // $("#agent").val(response.data.member.agentId);

                        //member address
                        $("#address").val(response.data.member.address);
                        $("#state").val(response.data.member.state);
                        $("#districtId").val(response.data.member.district);
                        $("#tehsilId").val(response.data.member.tehsil);
                        $("#villageId").val(response.data.member.village);
                        $("#contact_no").val(response.data.member.phone);

                        //Nominee
                        $("#nominee_name").val(response.data.member.nomineeName);
                        $("#relation").val(response.data.member.nomineeRelation);
                        $("#nomineeadhaarno").val(response.data.member.nomineeadhaarno);
                        $("#nominee_address").val(response.data.member.nomineeAddress);
                        // $("#date_of_birth").val(response.data.member.nomineeBirthDate);
                        $("#nomineecontact_no").val(response.data.member.nomineePhone);

                        if (response.data.signature) {
                            document.getElementById("blah").src = response.data.signature;
                        }

                        if (response.data.photo) {
                            document.getElementById("upload").src = response.data.photo;
                        }

                        if (response.data.photoidproof) {
                            document.getElementById("upload3").src = response.data.photoidproof;
                        }

                        $("#membershipmodel").modal('hide');
                        $("#viewmemberdetails").prop('disabled', false);
                        $("#submitbtns").empty();
                        $("#submitbtns").append(
                            '<button type="submit" class="btn btn-primary px-4" name="Update">Update</button>'
                        );
                    } else {
                        alert("somethink went wrong !!");
                    }
                });
            });


        });

        if (document.readyState == "complete") {
            $(".transactionDate").val({{ session('currentdate') }});
        }
        $(document).on('click','.deletebtn',function(event){
                event.preventDefault();
                let id = $(this).data('id');

                swal({
                    title: 'Are you sure?',
                    text: "You want to delete a transaction. It cannot be recovered.",
                    icon: 'warning',
                    buttons: {
                        cancel: "Cancel",
                        confirm: {
                            text: "Yes, Delete",
                            closeModal: false
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        // Show loading spinner
                        swal({
                            title: 'Deleting...',
                            text: 'Please wait while the transaction is being deleted.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false
                        });

                        $.ajax({
                            url : "{{ route('deleteaccount') }}",
                            type : 'post',
                            data : {id : id},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType : 'json',
                            success : function(res){
                                if(res.status === 'success'){
                                    window.location.href="{{ route('accountopen.page') }}";
                                }else{
                                    notify(res.messages,'warning');
                                }
                            }
                        });
                    }
                });
            });
    </script>
@endpush
