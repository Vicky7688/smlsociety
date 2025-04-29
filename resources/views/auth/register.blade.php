@extends('layouts.main')

@section('content')
<div class="container-fluid p-0">
    <div class="row m-0">
        <div class="col-xl-7 p-0"><img class="bg-img-cover bg-center" src="{{asset('../assets/images/login/1.jpg')}}" alt="looginpage"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    {{-- <div><a class="logo text-start" href="{{route('getdashboard')}}"><img class="img-fluid for-light" src="{{asset('../assets/images/logo/logo.png')}}" alt="looginpage"><img class="img-fluid for-dark" src="{{asset('../assets/images/logo/logo_dark.png')}}" alt="looginpage"></a></div> --}}
                    <div class="login-main">
                        <form class="theme-form" method="POST" action="{{ route('register') }}">
                            @csrf
                            <h4>Create your account</h4>
                            <p>Enter your personal details to create account</p>

                            <div class="form-group">
                                <label class="col-form-label" for="name">Name</label>
                                <input class="form-control" id="name" type="text" required="" placeholder="user" name="name" value="{{old('name')}}">
                                <div class="text-danger">
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-form-label" for="email">Email Address</label>
                                <input class="form-control" id="email" type="email" required="" placeholder="Test@example.com" name="email" value="{{old('email')}}">
                                <div class="text-danger">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label" for="password">Password</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" id="password" type="password" name="password" required="" placeholder="*********">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <div class="text-danger">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label" for="password_confirmation">Confirm Password</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required="" placeholder="*********">
                                </div>
                                <div class="text-danger">
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="checkbox p-0">
                                    <input id="checkbox1" type="checkbox">
                                    <label class="text-muted" for="checkbox1">Agree with<a class="ms-2" href="#">Privacy Policy</a></label>
                                </div>
                                <button class="btn btn-primary btn-block w-100" type="submit">Create Account</button>
                            </div>
                            <h6 class="text-muted mt-4 or">Or signup with</h6>
                            <div class="social mt-4">
                                <div class="btn-showcase"><a class="btn btn-light" href="https://www.linkedin.com/login" target="_blank"><i class="txt-linkedin" data-feather="linkedin"></i> LinkedIn </a><a class="btn btn-light" href="https://twitter.com/login?lang=en" target="_blank"><i class="txt-twitter" data-feather="twitter"></i>twitter</a><a class="btn btn-light" href="https://www.facebook.com/" target="_blank"><i class="txt-fb" data-feather="facebook"></i>facebook</a></div>
                            </div>
                            <p class="mt-4 mb-0 text-center">Already have an account?<a class="ms-2" href="/">Sign in</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom_script')
<!-- latest jquery-->
<script src="{{asset('../assets/js/jquery.min.js')}}"></script>
<!-- Bootstrap js-->
<script src="{{asset('../assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
<!-- feather icon js-->
<script src="{{asset('../assets/js/icons/feather-icon/feather.min.js')}}"></script>
<script src="{{asset('../assets/js/icons/feather-icon/feather-icon.js')}}"></script>
<!-- scrollbar js-->
<!-- Sidebar jquery-->
<script src="{{asset('../assets/js/config.js')}}"></script>
<!-- Plugins JS start-->
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{asset('../assets/js/script.js')}}"></script>
<!-- Plugin used-->

@endsection