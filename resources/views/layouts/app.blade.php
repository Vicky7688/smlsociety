<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-starter">

<head>
    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title') - Beta Byte</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/reportCustom.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/snackbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">


    @stack('style')
    <!-- Page CSS -->

    <!-- Helpers -->

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Your custom script -->
    <script src="path/to/cclrecoveryIndex.js"></script>

    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
        }

        .form-label {
            font-weight: bold;
        }

        .leftaligntd {
            text-align: left !important;
        }

        .rightaligntd {
            text-align: right !important;
        }

        .centeraligntd {
            text-align: center !important;
        }

        h4 {
            margin: 0
        }
    </style>

    {{-- <script>
    window.addEventListener('load', function() {
        $.ajax({
            url: '{{ route("checkmatured") }}',
            type: 'get',
            dataType: 'json',
            success: function(response) {},
            error: function(jqXHR, exception) {}
        });
    });
</script> --}}

</head>


<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.sidebaar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>
                    {{-- <div class="navbar-nav align-items-center">
                        <div class="nav-item navbar-search-wrapper mb-0">
                            <a class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                                href="javascript:void(0);">
                                <h4 class="align-middle"> {{ session('Branchname') ?? '' }} </h4>
                            </a>
                        </div>
                    </div> --}}

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">

                            <div class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-sun ti-md"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-start dropdown-styles">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                            <span class="align-middle">
                                                <i class="ti ti-sun me-2"></i>
                                                Light
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                            <span class="align-middle">
                                                <i class="ti ti-moon me-2"></i>
                                                Dark
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                            <span class="align-middle">
                                                <i class="ti ti-device-desktop me-2"></i>
                                                System
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <h4 class="align-middle text-uppercase"> {{ session('Branchname') ?? '' }} </h4>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center justify-content-end">
                            <li class="col-lg-4">
                                <form id="changedate" name="changedate">
                                    <div class="input-group">
                                        <input type="text" id="currentdate" name="currentdate"
                                            class="form-control form-control-sm mydatepic valid transactionDate"
                                            value="{{ Session::get('currentdate') }}" autocomplete="off">
                                        <button style="padding:5px; font-size:12px;" id="submitButton" type="submit"
                                            class="btn btn-primary">Working Date</button>
                                    </div>
                                </form>


                            </li>
                            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <h4 class="align-middle text-uppercase">
                                        {{ date('Y', strtotime(session('sessionStart'))) }} -
                                        {{ date('Y', strtotime(session('sessionEnd'))) }}
                                    </h4>
                                </a>
                            </li>




                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-language rounded-circle ti-md"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                @php
                                    $sessions = App\Models\SessionMaster::orderby('id', 'DESC')->get();
                                @endphp
                                @foreach ($sessions as $session)
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" data-language="en"
                                            data-text-direction="ltr">
                                            <span dataid="{{ $session->id }}"
                                                class="align-middle changesession">{{ date('Y', strtotime($session->startDate)) }}
                                                - {{ date('Y', strtotime($session->endDate)) }}</span>
                                        </a>
                                    </li>
                                @endforeach

                            </ul>
                            </li>

                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                                    class="h-auto rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                                            <small class="text-muted">{{ Auth::user()->rolemaster->name }}</small>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="ti ti-user-check me-2 ti-sm"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="ti ti-settings me-2 ti-sm"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}">
                                            <i class="ti ti-logout me-2 ti-sm"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </div>

                </nav>
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    @yield('content')
                    <!-- / Content -->

                    @include('layouts.footer')

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/forms-selects.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/snackbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/block-ui/block-ui.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(document).on('submit', '#changedate', function(event) {
            event.preventDefault();
            let currentdate = $('#currentdate').val(); // Ensure correct ID

            $.ajax({
                url: "{{ route('changescurrentdate') }}",
                type: 'POST',
                data: {
                    currentdate: currentdate
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    console.log("Success:", res);
                    $('.transactionDate').val(currentdate);
                },
                error: function(xhr, status, error) {
                    console.log("Error:", xhr.responseText);
                }
            });
        });
    </script>
    <script>
        @if (isset($table) && $table == 'yes')

            function datatableSetup(urls, datas, onDraw = function() {}, ele = "#datatable", element = {}) {
                var options = {
                    dom: '<"datatable-scroll"t><"datatable-footer"ip>',
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    stateSave: true,
                    columnDefs: [{
                        orderable: false,
                        width: '130px',
                        targets: [0]
                    }],
                    language: {
                        paginate: {
                            'first': 'First',
                            'last': 'Last',
                            'next': '&rarr;',
                            'previous': '&larr;'
                        }
                    },
                    drawCallback: function() {
                        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
                    },
                    preDrawCallback: function() {
                        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                    },
                    ajax: {
                        url: urls,
                        type: "post",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.fromdate = $('#searchForm').find('[name="from_date"]').val();
                            d.todate = $('#searchForm').find('[name="to_date"]').val();
                            d.searchtext = $('#searchForm').find('[name="searchtext"]').val();
                            d.agent = $('#searchForm').find('[name="agent"]').val();
                            d.status = $('#searchForm').find('[name="status"]').val();
                            d.product = $('#searchForm').find('[name="product"]').val();
                        },
                        datatype: 'json',
                        beforeSend: function() {

                            $('#searchForm').find('button:submit').html('Loading..').attr("disabled", true)
                                .addClass('btn btn-secondary');
                        },
                        complete: function() {
                            $('#searchForm').find('button:submit').html('Submit').attr('disabled', false)
                                .removeClass('btn-secondary');
                            $('#formReset').find('button:submit').html('Submit').attr('disabled', false)
                                .removeClass('btn-secondary');
                            $('#searchForm').find('button:submit').html('Search').attr("disabled", false)
                                .removeClass('btn-secondary');
                        },
                        error: function(response) {}
                    },
                    columns: datas
                };

                $.each(element, function(index, val) {
                    options[index] = val;
                });

                var DT = $(ele).DataTable(options).on('draw.dt', onDraw);

                return DT;
            }
        @endif

        $('.mydatepic').datepicker({
            'autoclose': true,
            'clearBtn': true,
            'todayHighlight': true,
            "endDate": "today",
            'format': 'dd-mm-yyyy',
        });

        function showError(errors, form = "withoutform") {
            if (form != "withoutform") {
                form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                $('p.error').remove();
                $('div.alert').remove();
                if (errors.status == 422) {

                    $.each(errors.responseJSON.errors, function(index, value) {

                        form.find('[name="' + index + '"]').closest('div.form-control').append(
                            '<p class="error">' +
                            value + '</span>');
                    });

                    form.find('p.error').first().closest('.form-control').find('input').focus();
                    setTimeout(function() {
                        form.find('p.error').remove();
                    }, 5000);
                } else if (errors.status == 400) {
                    if (errors.responseJSON.message) {

                        form.prepend(`<div class="alert alert-danger alert-dismissible" role="alert">
                        ` + errors.responseJSON.message + `
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`);

                    } else {

                        form.prepend(`<div class="alert alert-danger alert-dismissible" role="alert">
                        ` + errors.responseJSON.status + `
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>`);

                    }

                    setTimeout(function() {
                        form.find('div.alert').remove();
                    }, 10000);
                } else {
                    notify(errors.statusText, 'warning');
                }
            } else {
                if (errors.responseJSON.message) {
                    notify(errors.responseJSON.message, 'warning');
                } else {
                    notify(errors.responseJSON.status, 'warning');
                }
            }
        }

        function notify(msg, type = "success", notitype = "popup", element = "none") {
            if (notitype == "popup") {
                let snackbar = new SnackBar;
                snackbar.make("message", [
                    msg,
                    null,
                    "bottom",
                    "right",
                    "text-" + type
                ], 10000);
            } else {
                element.find('div.alert').remove();
                element.prepend(`<div class="alert bg-` + type +
                    ` alert-styled-left">
                    <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button> ` +
                    msg + `
                </div>`);

                setTimeout(function() {
                    element.find('div.alert').remove();
                }, 10000);
            }
        }

        $(".menu-inner li a").each(function() {
            if (this.href == window.location.href) {
                $(this).addClass("active");
                $(this).parent().addClass("active");
                $(this).parent().parent().prev().addClass("open");
                $(this).parent().parent().parent().click();
                $(this).parent().parent().parent().addClass("open");
                //  $(this).parent().parent().prev().addClass("open");
                // console.log($(this).parent().parent().parent().click());
            }
        });

        $.validator.addMethod("customDate", function(value, element) {
            var dateArray = value.split('-');
            var day = parseInt(dateArray[0], 10);
            var month = parseInt(dateArray[1], 10);
            var year = parseInt(dateArray[2], 10);
            var newdate = moment(value).format('DD-MM-YYYY');
            var currentDate = new Date();
            var inputDate = new Date(newdate);
            console.log(inputDate, currentDate);
            // Check if input date is in the future
            if (inputDate > currentDate) {
                return false;
            }

            // Check if day is between 1 and 30
            if (day < 1 || day > 31) {
                return false;
            }

            // Check if month is between 1 and 12
            if (month < 1 || month > 12) {
                return false;
            }

            // Add additional conditions as needed

            return /^(\d{2})-(\d{2})-(\d{4})$/.test(value);
        }, "Please enter a valid date in the format dd-mm-yyyy");

        function blockForm(form) {
            $(form).block({
                message: '<div class="d-flex justify-content-center"><p class="me-2 mb-0">Please wait...</p> <div class="sk-wave sk-primary m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
                css: {
                    backgroundColor: "transparent",
                    border: "0"
                },
                overlayCSS: {
                    backgroundColor: "#fff",
                    opacity: 0.8
                }

            });
        }

        function formatDate(inputDate) {
            return moment(inputDate).format('DD-MM-YYYY');
        }

        $(document).ready(function() {
            var items = JSON.parse(localStorage.getItem('items'));
            if (items && items.length > 0) {
                localStorage.removeItem('items');
            }
        });

        $(document).on('click', '.changesession', function(event) {

            var modifyId = $(this).attr('dataid');
            console.log(modifyId);
            $.ajax({
                url: "{{ route('sessionchange', '') }}/" +
                    modifyId,
                type: "GET",
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        // notify("Session change successfully", 'success');
                        location.reload();
                    } else {
                        // notify(data.status, 'warning');
                    }
                }
            });





        });















        function validateForm(formId) {
            let isValid = true;
            $(".error-message").remove();
            $("#" + formId + " .thisRequired").each(function() {
                if ($(this).val().trim() === '') {
                    console.log('error in ', $(this).attr("name"));
                    $(this).after('<p class="error-message text-danger">This field is required.</p>');
                    isValid = false;
                }
            });
            $("#" + formId + " .numRequired").each(function() {
                const value = $(this).val().trim();
                if (isNaN(value) || value === '' || parseFloat(value) <= 0) {
                    console.log('error in', $(this).attr("name"));
                    if (value === '') {
                        $(this).after('<p class="error-message text-danger">Should be a number.</p>');
                    } else {
                        $(this).after('<p class="error-message text-danger">Value should be greater than 0.</p>');
                    }
                    isValid = false;
                }
            });
            $("#" + formId + " .shouldEmail").each(function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test($(this).val())) {
                    console.log('error in ', $(this).attr("name"));
                    $(this).after('<p class="error-message text-danger">Should be a valid email.</p>');
                    isValid = false;
                }
            });
            const datePattern = /^\d{2}-\d{2}-\d{4}$/;
            $("#" + formId + " .customDate").each(function() {
                if (!datePattern.test($(this).val())) {
                    console.log('error in ', $(this).attr("name"));
                    $(this).after(
                        '<p class="error-message text-danger">Please enter a valid date in the format dd-mm-yyyy.</p>'
                    );
                    isValid = false;
                }
            });
            return isValid;
        }
    </script>






    @stack('script')
    <!-- Page JS -->
</body>

</html>
