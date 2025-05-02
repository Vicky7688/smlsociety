<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login Beta Byte Technology</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!-- Sweet alert -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .login_bg {
            background-image: url(assets/login.png);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            /* padding-top: 30px; */
        }

        .container {
            width: 70%;
            margin: auto;
        }

        .text_primary {
            color: #7367F0;
        }

        .app-brand-logo img {
            max-width: 92px;
        }

        .leftLoginformImg {
            padding-inline: 2rem;
        }

        .login_form {
            padding: 40px 0 60px 0;
            width: 300px !important;
            padding-inline: 2rem;
        }

        .login_main {
            background-image: url(assets/login_bg.png);
            border-radius: 15px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            /* background-position: bottom;
            background-repeat: no-repeat;
            background-size: cover; */
        }

        .forgetPassword {
            margin-block: 1rem;
        }




        <blade media|(max-width%3A%201399px)%20%7B>.login_form {
            padding: 40px 0 60px 0;
        }

        .login_txt {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover authentication-bg login_bg">
        <div class="container">
            <div class="authentication-inner row login_main">
                <!-- /Left Text -->
                <div class="d-none d-lg-flex col-lg-6 p-0">
                    <div class="auth-cover-bg h-100 d-flex justify-content-center align-items-center m-0 leftLoginformImg">
                        <img src="assets/img/illustrations/login_img.png" alt="auth-login-cover" class="img-fluid my-5 auth-illustration" data-app-light-img="illustrations/login_img.png" />
                    </div>
                </div>
                <!-- /Left Text -->

                <!-- Login -->
                <div class="d-flex col-12 col-lg-6 align-items-center p-sm-0 p-4">
                    <div class="login_form w-px-400 mx-auto">
                        <!-- Logo -->
                        <div class="app-brand mb-4 justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
                                <span class="app-brand-logo">
                                    <img src="assets/img/branding/logo.png" alt="">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h3 class="login_txt mb-1 text-center text_primary">Welcome to Beta Byte</h3>

                        <form id="loginForm" method="POST" action="{{ route('authCheck') }}">
                            @csrf
                            <div class="mb-3 col">
                                <label class="form-label mb-1" for="status-org">Session </label>
                                <select name="sessionId" id="status-org" class="select2 form-select" data-placeholder="Active">
                                    @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">
                                        {{ date('Y',strtotime($session->startDate)) }} -
                                        {{ date('Y',strtotime($session->endDate)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Username</label>
                                <input type="text" id="email" class="form-control" name="mobile" placeholder="Enter your email or username" autofocus />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>

                                <div class="forgetPassword">
                                    <a href="#">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>

                            </div>

                            <button class="btn btn-primary d-grid w-100">Sign in</button>
                        </form>

                        <!-- <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="auth-register-cover.html">
                            <span>Create an account</span>
                        </a>
                    </p> -->

                        <!-- <div class="divider my-4">
                            <div class="divider-text">or</div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                                <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                                <i class="tf-icons fa-brands fa-google fs-5"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                                <i class="tf-icons fa-brands fa-twitter fs-5"></i>
                            </a>
                        </div> -->
                    </div>
                </div>
                <!-- /Login -->
            </div>
        </div>

    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}">
    </script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}">
    </script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}">
    </script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}">
    </script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
    <!-- Validate JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Sweet alert js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {

            $("#loginForm").validate({
                rules: {
                    username: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },

                },
                messages: {
                    username: {
                        required: "Please enter username",
                    },
                    password: {
                        required: "Please enter password",

                    },
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    //  debugger;
                    var form = $('#loginForm');
                    form.ajaxSubmit({
                        dataType: 'json',

                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html('loading').attr(
                                'disabled', true).addClass('btn-secondary');
                            swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');
                            swal.close();

                            if (data.status == "Login") {
                                swal({
                                    type: 'success',
                                    title: 'Success',
                                    text: 'Successfully loggedIn.',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    onClose: () => {
                                        window.location = "dashboard";
                                    },
                                });

                            } else {
                                swal({
                                    type: 'error',
                                    title: 'Failed',
                                    text: data.message,
                                    showConfirmButton: true,
                                    onClose: () => {
                                        form[0].reset();
                                    },
                                });
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                            swal.close();

                            // showError(xhr, form);
                            // notify("An error occurred. Please try again.", 'error');
                        }
                    });

                }
            });

            $("#passwordForm").validate({
                rules: {
                    token: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },

                },
                messages: {
                    token: {
                        required: "Please enter Otp",
                    },
                    password: {
                        required: "Please enter password",
                    },
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    // e.prevantDefault();

                    var form = $('#passwordForm');
                    form.ajaxSubmit({
                        dataType: 'json',

                        beforeSubmit: function() {
                            swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            console.log('data', data)
                            swal.close();

                            if (data.status == "TXN") {
                                $('#passwordModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Success',
                                    text: data.message,
                                    showConfirmButton: false,
                                });
                                setTimeout(function() {
                                    swal.close();
                                }, 3000)

                            } else {
                                swal({
                                    type: 'error',
                                    title: 'Failed',
                                    text: data.message,
                                    showConfirmButton: false,
                                    onClose: () => {
                                        form[0].reset();
                                    },
                                });
                                setTimeout(function() {
                                    swal.close();
                                }, 3000)
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                            swal.close();

                            // showError(xhr, form);
                            notify("An error occurred. Please try again.", 'error');
                        }
                    });

                }
            });
        });

        function showError(errors, form = "withoutform") {
            if (form != "withoutform") {
                form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                $('p.error').remove();
                $('div.alert').remove();
                if (errors.status == 422) {
                    $.each(errors.responseJSON.errors, function(index, value) {
                        form.find('[name="' + index + '"]').closest('div.form-group').append(
                            '<p class="error">' +
                            value + '</span>');
                    });
                    form.find('p.error').first().closest('.form-group').find('input').focus();
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
    </script>

</body>

</html>
