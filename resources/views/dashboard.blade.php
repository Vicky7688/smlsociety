@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y cardsTop">
        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-truck ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">
                                @isset($opening_balance)
                                {{ $opening_balance }}
                                @endisset
                            </h4>
                        </div>
                        <p class="mb-1">OPENING CASH</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-alert-triangle ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">
                                @isset($closing_cash)
                                {{ $closing_cash }}
                                @endisset
                            </h4>
                        </div>
                        <p class="mb-1">CLOSING CASH</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-git-fork ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">
                                @isset($memberac)
                                {{$memberac}}
                            @endisset
                            </h4>
                        </div>
                        <p class="mb-1">MEMBERS ACCOUNT</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="ti ti-clock ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">
                                @isset($nonmember)
                                {{ $nonmember }}
                                @endisset
                            </h4>
                        </div>
                        <p class="mb-1">NOMINAL MEMBERS</p>

                    </div>
                </div>
            </div>

            {{-- <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($staff)
                                {{$staff}}
                                @endisset
                            </h5>
                            <small>Staff</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div> --}}



            {{-- <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($memberfd)
                                {{$memberfd}}
                                @endisset
                            </h5>
                            <small>Fixed Deposit</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($contributions)
                                {{$contributions}}
                                @endisset
                            </h5>
                            <small>Contributions</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($membershare)
                                {{$membershare}}
                                @endisset
                            </h5>
                            <small>Share Member</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($memberrd)
                                {{$memberrd}}
                                @endisset
                            </h5>
                            <small>Recurring Deposit</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">
                                @isset($memberloan)
                                {{$memberloan}}
                                @endisset
                            </h5>
                            <small>Loan</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{--  <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">{{$membermis}}</h5>
                            <small>MIS</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>  --}}
            {{--  <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">86%</h5>
                            <small>Journal Voucher</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">86%</h5>
                            <small>Daily</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">86%</h5>
                            <small>Daybook</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2">86%</h5>
                            <small>Book Tool</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-cpu ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>  --}}
        </div>
    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Container-fluid Ends-->
@endsection

@push('script')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {

        $("#searchbydate").validate({
            rules: {
                fromdate: {
                    required: true,
                },
                todate: {
                    required: true,
                }
            },
            messages: {
                fromdate: {
                    required: "Please select fromdate",
                },
                todate: {
                    required: "Please select fromdate",
                },
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('form#searchbydate');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').button('loading');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $('.' + index).text(value);
                        });
                    },
                    error: function(errors) {
                        showError(errors, form.find('.modal-body'));
                    }
                });
            }
        });
    });
</script>
@endpush
