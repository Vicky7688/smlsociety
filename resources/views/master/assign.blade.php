@extends('layouts.app')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-12 ">
                <div class="card page_headings cards">
                    <div class="card-body py-2">
                        <h4 class="py-2"><span class="text-muted fw-light">Masters / Employee Module / </span> User Register </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    @if (session('success'))
                      <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif


                    <form id="userregister" name="userregister" action="{{ !empty($userid) ? route('usersupdate', $userid->id) : route('userregister') }}" method="POST">
                        @csrf

                        @if(!empty($userid))
                            @method('PUT')
                        @endif

                        <div class="modal-body">
                            <div class="row row-gap-2">
                                <input type="hidden" name="userid" id="userid" value="{{ $userid->id ?? '' }}">
                                <div class="col-lg-2 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="usertype">Type</label>
                                    <select name="usertype" id="usertype" class="select21 form-select formInputsSelectReport valid"
                                        data-placeholder="Active" aria-invalid="false" onchange="userTypes(this)">
                                        @if(!empty($roles))
                                            @foreach ($roles as $row)
                                                <option @if(!empty($userid)) {{ $row->id == $userid->role ? 'selected' : '' }} @endif
                                                        value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                @if(!empty($userid->agent_id))

                                <div class="col-lg-2 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown" id="agentsdiv">
                                    <label class="form-label mb-1" for="usertype">Agents</label>
                                    <select name="agents" id="agents" class="select21 form-select formInputsSelectReport valid"
                                        data-placeholder="Active" aria-invalid="false">
                                        <option value="" {{ empty($userid->agent_id) ? 'selected' : '' }}>Select Agent</option>
                                        <option value="{{ $userid->agent_id }}" selected>{{ $userid->agent_id }} - {{ $userid->name }}</option>
                                    </select>
                                </div>

                                @else
                                    <div class="col-lg-2 col-sm-4 col-6 py-2 inputesPaddingReport ecommerce-select2-dropdown" style="display: none;" id="agentsdiv">
                                        <label class="form-label mb-1" for="usertype">Agents</label>
                                        <select name="agents" id="agents" class="select21 form-select formInputsSelectReport valid"
                                            data-placeholder="Active" aria-invalid="false">
                                            <option value=""selected>Select Agent</option>
                                        </select>
                                    </div>
                                @endif





                                <div class="col-lg-2 col-sm-4 py-2 inputesPaddingReport">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control formInputsReport"
                                        placeholder="Enter Name"   @if(!empty($userid)) value="{{ $userid->name ?? old('name') }}" @else value="{{ old('name') }}" required @endif/>

                                    @error('name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-2 col-sm-4  py-2 inputesPaddingReport">
                                    <label for="name" class="form-label">Email</label>
                                    <input type="email" name="email"  class="form-control formInputsReport"  placeholder="Enter User Email"  @if(!empty($userid)) value="{{ $userid->email }}" @else value="{{ old('email') }}"  @endif/>
                                    @error('email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div class="col-lg-2 col-sm-4  py-2 inputesPaddingReport">
                                    <label for="name" class="form-label">Password</label>
                                    <input type="password" name="password"  class="form-control formInputsReport"  placeholder="Enter User Password" value="{{ old('password') }}"/>
                                    @error('password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>



                                <div class="col-lg-2 col-sm-4  py-2 inputesPaddingReport">
                                    <label for="name" class="form-label">Mobile</label>
                                    <input type="tel" name="mobile"  class="form-control formInputsReport"  @if(!empty($userid)) value="{{ $userid->mobile }}" @else value="{{ old('mobile') }}" required @endif placeholder="Enter User Mobile"  />
                                    @error('mobile')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror

                                </div>
                                <div class="col-lg-2 col-sm-4  py-2 inputesPaddingReport">
                                    <label for="name" class="form-label">User Name</label>
                                    <input type="text" id="user_name" name="user_name" class="form-control formInputsReport"
                                        @if(!empty($userid)) value="{{ $userid->username }}"
                                        @else value="{{ old('user_name') }}"
                                        @endif required placeholder="Enter User Name" />

                                    @error('user_name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-2 col-sm-4  py-2 inputesPaddingReport">
                                    <button class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                                        Loading...">Submit</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 cards">
                <div class="card">
                    <div class="card-body py-3">
                        <h5 class="card-action-title"></h5>
                        <div class="tablee">
                            <div class="table-responsive tabledata">
                                <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                                    <thead class="table_head verticleAlignCenterReport">
                                        <tr>
                                            <th class="w-17">S No</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>User Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($usersss))
                                            @foreach($usersss as $row)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $row->name }}</td>
                                                    <td>{{ $row->rolename }}</td>
                                                    <td>{{ $row->mobile }}</td>
                                                    <td>{{ $row->email}}</td>
                                                    <td>{{ $row->username}}</td>
                                                    <td style="width:85px;">
                                                        <a href="{{ route('useredits', $row->id) }}" class="btn editbtn" >
                                                            <i class="fa-solid fa-pen-to-square border-0 iconsColorCustom"></i>
                                                        </a>
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


@endsection

@push('style')

<style>
    html:not([dir=rtl]) .modal .btn-close {
        transform: none !important;
    }

    .btn-close {
        top: 1.35rem !important;
    }
    .waves-effect {
        margin-top: 25px;
        height: 32px;
    }

    /* #datatable_wrapper .dataTables_info,
    #datatable_wrapper .dataTables_paginate {
        display: none;
    } */

</style>

@endpush

@push('script')
<script>

    function userTypes() {
        let userType = $('#usertype').val();

        if (userType === '4') {
            // AJAX request to fetch all agents
            $.ajax({
                url: "{{ route('getallagents') }}",
                type: 'post',
                data: { userType: userType },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        let allagents = res.allagents;
                        $('#agentsdiv').css('display', 'block'); // Show the agents div
                        let agentDropdown = $('#agents').empty(); // Empty the agents dropdown

                        if (allagents && allagents.length > 0) {
                            // Populate agents dropdown
                            allagents.forEach((data) => {
                                agentDropdown.append(`<option value="${data.id}">${data.id} - ${data.name}</option>`);
                            });
                        } else {
                            // No agents available
                            agentDropdown.append('<option value="">No agents available</option>');
                        }
                    } else {
                        // Handle error response
                        notify(res.messages, 'warning');
                        $('#agentsdiv').css('display', 'none'); // Hide the agents div
                    }
                },
                error: function (err) {
                    // Handle AJAX errors
                    console.error('Error fetching agents:', err);
                    notify('Something went wrong while fetching agents.', 'error');
                    $('#agentsdiv').css('display', 'none'); // Hide the agents div
                }
            });
        } else {
            // Hide the agents div for other user types
            $('#agentsdiv').css('display', 'none');
        }
    }





    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").alert('close');
        }, 1000);


        {{--  userregister  --}}
        $('form').submit(function(e) {
            var userName = $('#user_name').val();
            // Remove spaces from the user name
            $('#user_name').val(userName.replace(/\s+/g, ''));
        });

    });
</script>
@endpush
