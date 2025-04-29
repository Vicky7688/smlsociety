@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings mb-4">
        <div class="card-body py-2">
            <h4 class="py-2"><span class="text-muted fw-light">Account / </span>Transfer Account</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="account_details_inner">
                            <form action="javascript:void(0)" id="transferform">
                                <div class="row row-gap-3">
                                    <div class="col-sm-3 py-2 saving_column">
                                        <label for="inputData" class="form-label">ACCOUNT NO</label>
                                        <input type="text" class="form-control" id="account_no" name="account_no"
                                            placeholder="Accout No" autocomplete="off">
                                    </div>

                                    <div class="col-sm-3 py-2">
                                        <div class="d-flex h-100 align-items-end">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row" id="accounts_detailsdata" style="display:none;">
                <div class="col-md-5">
                    <div class="card p-4">
                        <h6 class="mb-0 pb-2">TRANSFER FROM</h6>
                        <form id="account_member_details">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="OPENINGDATE">OPENING DATE</label>
                                            <input type="text" class="form-control" id="openingdate" value="{{ Session::get('currentdate') }}" readonly>
                                        </div>
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="ACCOUNTNAME">ACCOUNT NAME</label>
                                            <input type="text" class="form-control" id="account_name" readonly>
                                        </div>
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="GENDER">GENDER</label>
                                            <input type="text" class="form-control" id="gender" readonly>
                                        </div>

                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="ADHAAARNUMBER">ADHAAAR NUMBER</label>
                                            <input type="text" class="form-control" id="addharno" readonly>
                                        </div>
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="PANNUMBER">PAN NUMBER</label>
                                            <input type="text" class="form-control" id="panNo" readonly>
                                        </div>
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="WARDNUMBER">WARD NUMBER</label>
                                            <input type="text" class="form-control" id="wardNo" readonly>
                                        </div>
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="ADDRESS">ADDRESS</label>
                                            <input type="text" class="form-control" id="account_address" readonly>
                                        </div>
                                        <input type="hidden" id="creditaccount">
                                        <input type="hidden" id="accounttype">
                                        <div class="col-md-6 py-2">
                                            <label class="form-label" for="CONTACTNO">CONTACT NO.</label>
                                            <input type="text" class="form-control" id="account_contact" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <div class="cardd bg-transparent">
                                        <div class="card-body p-2">
                                            <div class="text-center">
                                                <label for="IMAGE" class="form-label text-center">IMAGE</label></div>
                                            <div class="img-prev text-center">
                                                <img src="" alt="" id="imageprofile">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <div class="cardd bg-transparent">
                                        <div class="card-body p-2">
                                            <div class="text-center">
                                                <label for="IMAGE" class="form-label">SIGN</label>
                                            </div>
                                            <div class="img-prev">
                                                <img src="" alt="" id="signature">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <div class="cardd bg-transparent">
                                        <div class="card-body p-2">
                                            <div class="text-center">
                                                <label for="IMAGE" class="form-label">IDENTITY</label>
                                            </div>
                                            <div class="img-prev">
                                                <img src="" alt="" id="id_proof">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-0 pb-2">TRANSFER TO</h6>
                            <form action="javascript:void(0)" id="TransferAccountTo">
                                <div class="row row-gap-4">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="OPENINGDATE">TRANSFER DATE</label>
                                                <input type="text" id="transfer_opening_date" class="form-control"
                                                    name="transfer_opening_date">
                                            </div>
                                            <div class="col-md-4 py-2 saving_column">
                                                <label class="form-label" for="ACCOUNTNAME">TRANSFER REASON</label>
                                                <input type="text" id="transfer_reason" class="form-control"
                                                    name="transfer_reason">
                                            </div>
                                            <div class="col-md-4 py-2 saving_column">
                                                <label class="form-label" for="ACCOUNTNAME">TRANSFER NAME</label>
                                                <input type="text" id="transfer_account_name" class="form-control"
                                                    name="transfer_account_name">
                                            </div>

                                            <div class="col-md-4 py-2 saving_column">
                                                <label class="form-label" for="ACCOUNTNAME">FATHER/HUSBAND</label>
                                                <input type="text" id="transfer_father_husband" class="form-control"
                                                    name="transfer_father_husband">
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="GENDER">GENDER</label>
                                                <select name="USERTYPE" id="transfer_gender" name="transfer_gender"
                                                    class="form-select">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="ADHAAR">ADHAAR NO.</label>
                                                <input type="text" id="transfer_aadharno" class="form-control"
                                                    name="transfer_aadharno" maxlength="12" minlength="12"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                    pattern="[0-9]{12}" inputmode="numeric" autocomplete="off">
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="PAN">PAN NO.</label>
                                                <input type="text" id="transfer_pan_no" class="form-control"
                                                    name="transfer_pan_no" maxlength="10"
                                                    oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="WARD">WARD NO.</label>
                                                <input type="number" id="transfer_wardno" class="form-control"
                                                    name="transfer_wardno">
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="ADDRESS">ADDRESS</label>
                                                <textarea class="form-control" name="transfer_address"
                                                    id="transfer_address" cols="30" rows="1"></textarea>
                                            </div>
                                            <div class="col-md-4 py-2">
                                                <label class="form-label" for="CONTACTNO">CONTACT NO.</label>
                                                <input type="tel" id="transfer_contact_no" class="form-control"
                                                    name="transfer_contact_no">
                                            </div>
                                            <div class="col-sm-4 py-2">
                                                <label class="form-label" for="STATE">STATE</label>
                                                <select name="transfer_state" id="transfer_state" class="form-select"
                                                    onchange="getDistrict(this)">
                                                    <option value="">Select State</option>
                                                    @foreach($state as $state)
                                                        <option id="{{ $state->id }}" value="{{ $state->name }}">{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="form-label" for="DISTRICT">DISTRICT</label>
                                                <select name="districtId" id="transfer_districtId"
                                                    onchange="gettehsil(this)" class="form-select">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="form-label" for="TEHSIL">TEHSIL</label>
                                                <select name="tehsilId" id="transfer_tehsilId"
                                                    onchange="getjointvillage(this)" class="form-select">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="form-label" for="VILLAGE POST">VILLAGE POST</label>
                                                <select name="villageId" id="transfer_villageId" class="form-select">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label" for="AGENT">AGENT</label>
                                                <select name="agent" id="agent" class="form-select">
                                                    <option value="">Select Agent</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{ $agent->id }}">{{ $agent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="cardd bg-transparent">
                                            <div class="card-body px-2 py-3">
                                                <div class="photo_upload text-center">
                                                    <label for="" class="form-label">ID Proof</label>
                                                    <div class="photo_upload_inner position-relative">
                                                        <img src="http://placehold.it/180" id="blah" alt="Image"
                                                            class="upload rounded-1">
                                                    </div>
                                                    <button class="border-0 bg-white" type="button">
                                                        <label for="photo"
                                                            class="custom-file-upload">Upload</label></button>
                                                    <button class="close_btn" type="button" onclick="removeImg()">Remove
                                                    </button>
                                                    <input class="inputFile" type="file" id="photo" name="photo"
                                                        onchange="readUrl(this)"
                                                        value="{{ old('photo') }}"
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="cardd bg-transparent">
                                            <div class="card-body px-2 py-3">
                                                <div class="photo_upload text-center">
                                                    <label for="" class="form-label">Photo</label>
                                                    <div class="photo_upload_inner position-relative">
                                                        <img src="http://placehold.it/180" id="upload" alt="Image"
                                                            class="upload rounded-1">
                                                    </div>
                                                    <label for="photoo" class="custom-file-upload">Upload</label>
                                                    <button class="close_btn" type="button"
                                                        onclick="removeImgB()">Remove
                                                    </button>
                                                    <input class="inputFile" type="file" id="photoo" name="photoo"
                                                        onchange="readUrlB(this)"
                                                        value="{{ old('photo') }}"
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="cardd bg-transparent">
                                            <div class="card-body px-2 py-3">
                                                <div class="photo_upload text-center">
                                                    <label for="" class="form-label">Signature</label>
                                                    <div class="photo_upload_inner position-relative">
                                                        <img src="http://placehold.it/180" id="upload3" alt="Image"
                                                            class="upload rounded-1">
                                                    </div>
                                                    <label for="photo3" class="custom-file-upload">Upload</label>
                                                    <button class="close_btn" type="button"
                                                        onclick="removeImg3()">Remove
                                                    </button>
                                                    <input class="inputFile" type="file" id="photo3" name="photo3"
                                                        onchange="readUrl3(this)"
                                                        value="{{ old('photo') }}"
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="button text-end">
                                            <button type="submit" class="btn btn-primary px-4 py-2">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="tabledata">

            <table class="table datatables-order table border-top" id="datatable" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th class="w-17"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>

                    </tr>
                </tbody>
            </table>

        </div> -->
    </div>
</div>
@endsection
@push('style')
    <style>
        .img-prev img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            max-width: 150px;
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
            border-radius: 10px;
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
        }

        function base_url() {
            return "{{ asset('') }}";
        }

        function getDistrict(ele) {
            axios.post("{{ route('locationsupdate') }}", {
                    actiontype: "getdistrict",
                    stateid: $(ele).find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                })
                .then(function (response) {
                    var out = `<option value="">Select District</option>`;
                    response.data.dist.forEach(function (value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="districtId"]').html(out);
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        }

        function gettehsil(ele) {
            axios.post("{{ route('locationsupdate') }}", {
                    actiontype: "gettehsil",
                    distId: $(ele).find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function (response) {
                    var out = `<option value="">Select Tehsil</option>`;
                    response.data.data.forEach(function (value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="tehsilId"]').html(out);
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        }

        function getvillage(ele) {
            axios.post("{{ route('locationsupdate') }}", {
                    actiontype: "getvillage",
                    tehsilId: $(ele).find(':selected').attr('id'),
                    stateId: $('#state').find(':selected').attr('id'),
                    districtId: $('#districtId').find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function (response) {
                    var out = `<option value="">Select Village</option>`;
                    response.data.data.forEach(function (value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="villageId"]').html(out);
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        }

        function getjointvillage(ele) {
            axios.post("{{ route('locationsupdate') }}", {
                    actiontype: "getvillage",
                    tehsilId: $(ele).find(':selected').attr('id'),
                    stateId: $('#transfer_state').find(':selected').attr('id'),
                    districtId: $('#transfer_districtId').find(':selected').attr('id'),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                })
                .then(function (response) {
                    var out = `<option value="">Select Village</option>`;
                    response.data.data.forEach(function (value) {
                        out += `<option id="${value.id}" value="${value.name}">${value.name}</option>`;
                    });
                    $('[name="villageId"]').html(out);
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        }

        $(document).ready(function () {
            var currentDate = moment().format('DD-MM-YYYY');
            $("#transfer_opening_date").val(currentDate);

            $("#transferform").validate({
                rules: {
                    account_no: {
                        required: true,
                    }
                },
                messages: {
                    account_no: "This field is required",
                },
                errorElement: "p",
                errorPlacement: function (error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    axios.post("{{ route('transfer.account.detail') }}", formData)
                        .then((response) => {
                            if (response.data.status == "success") {
                                $("#creditaccount").val(response.data.accountcredit);
                                $("#accounttype").val(response.data.account.accountType);
                                $("#openingdate").val(response.data.account.openingDate);
                                $("#account_name").val(response.data.account.name);
                                $("#gender").val(response.data.account.gender);
                                $("#addharno").val(response.data.account.aadharNo);
                                $("#panNo").val(response.data.account.panNo);
                                $("#wardNo").val(response.data.account.wardNo);
                                $("#account_address").val(response.data.account.address);
                                $("#account_contact").val(response.data.account.phone);
                                if (response.data.account.signature) {
                                    var signature = base_url() +
                                        'public/uploads/MemberSignature/' + response.data
                                        .account.signature;
                                    $("#signature").attr("src", signature);
                                }
                                if (response.data.account.photo) {
                                    var photo = base_url() + 'public/uploads/MemberPhotos/' +
                                        response.data.account.photo;
                                    $("#imageprofile").attr("src", photo);
                                }
                                if (response.data.account.idProof) {
                                    var idproof = base_url() + 'public/uploads/MemberIdProof/' +
                                        response.data.account.idProof;
                                    $("#id_proof").attr("src", idproof);
                                }
                                $("#accounts_detailsdata").show();

                            } else if (response.data.status == "error") {
                                notify(response.data.message, 'warning');
                                $("#account_no").val('');
                                $("#accounts_detailsdata").hide();
                            }
                        });
                }

            });

            $("#TransferAccountTo").validate({
                rules: {
                    transfer_opening_date: {
                        required: true,
                    },
                    transfer_reason: {
                        required: true,
                    },
                    transfer_account_name: {
                        required: true,
                    },
                    transfer_father_husband: {
                        required: true,
                    },

                },
                messages: {
                    transfer_opening_date: "This field is required",
                    transfer_reason: "This field is required",
                    transfer_account_name: "This field is required",
                    transfer_father_husband: "This field is required",

                },
                errorElement: "p",
                errorPlacement: function (error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    var account = $("#creditaccount").val();
                    if (!account) {
                        notify("Please Enter Account No first", 'warning');
                    } else {
                        var formData = new FormData(form);
                        formData.append('account', account);
                        axios.post("{{ route('store.transfer.account') }}",
                                formData)
                            .then((response) => {
                                if (response.data.status == "success") {
                                    $("#TransferAccountTo")[0].reset();
                                    $("#account_member_details")[0].reset();
                                    $("#creditaccount").val('');
                                    $("#account_no").val('');
                                    $('#blah').attr('src', 'http://placehold.it/180');
                                    $("#upload").attr('src', 'http://placehold.it/180');
                                    $("#upload3").attr('src', 'http://placehold.it/180');
                                    $("#accounts_detailsdata").hide();
                                    notify("Transfer Account Created", 'success');
                                } else if (response.data.status == "error") {
                                    notify(response.data.message, 'warning');
                                }
                            });
                    }

                }

            })
        });

    </script>
@endpush
