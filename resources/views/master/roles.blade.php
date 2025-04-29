@extends('layouts.app')
@section('content')

@php
$table = "yes";
@endphp

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 ">
            <div class="card page_headings cards">
                <div class="card-body py-2">
                    <h4 class="py-2"><span class="text-muted fw-light">Masters / Employee Module / </span> Job Profile </h4>
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
                <form id="rolesForm" name="rolesForm" action="{{ !empty($userid) ? route('roleupdate', $userid->id) : route('userinsert') }}"
                      method="POST">
                    @csrf
                    @if(!empty($userid))
                        @method('PUT')
                    @endif

                    <div class="modal-body">
                        <div class="row row-gap-2">
                            <!-- Hidden field for user ID when editing -->
                            <input type="hidden" name="userid" id="userid" value="{{ $userid->id ?? '' }}">

                            <div class="col-lg-2 col-sm-4 py-2 inputesPaddingReport">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control formInputsReport"
                                       placeholder="Enter Job Profile" required
                                       value="{{ $userid->name ?? '' }}">
                                    @error('name')
                                       <p class="text-danger">{{ $message }}</p>
                                   @enderror
                            </div>

                            <div class="col-lg-2 col-sm-4 py-2 inputesPaddingReport">
                                <button class="btn btn-primary waves-effect waves-light reportSmallBtnCustom ms-2 me-0"
                                        type="submit"
                                        data-loading-text="<span class='spinner-border me-1' role='status' aria-hidden='true'></span> Loading...">
                                    Submit
                                </button>
                            </div>
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
                    <h5 class="card-action-title">Agent Master</h5>
                    <div class="tablee">
                        <div class="table-responsive tabledata">
                            <table class="table datatables-order table table-bordered" id="datatable" style="width:100%">
                                <thead class="table_head verticleAlignCenterReport">
                                    <tr>
                                        <th class="w-17">S No</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($roles))
                                        @foreach ($roles as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td style="width:85px;">
                                                    <a href="{{ route('roleedit', $row->id) }}" class="btn editbtn" >
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
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").alert('close');
        }, 1000);
    });
</script>
@endpush
