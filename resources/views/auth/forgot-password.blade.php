@extends('layouts.main')

@section('content')

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-12">
            <div class="login-card login-dark">
                <div>
                    <div><a class="logo" href="{{route('getdashboard')}}"><img class="img-fluid for-light" src="{{asset('../assets/images/logo/logo.png')}}" alt="looginpage"><img class="img-fluid for-dark" src="{{asset('../assets/images/logo/logo_dark.png')}}" alt="looginpage"></a></div>


                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <div class="login-main">
                        <div class="mb-4 text-sm text-gray-600">
                            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </div>
                        <form class="theme-form" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <h4>Reset Your Password</h4>

                            <div class="form-group">
                                <label class="col-form-label" for="email">Email</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" id="email" type="email" name="email" required="" placeholder="*********" value="{{old('email')}}">
                                    <div class="text-danger">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block w-100" type="submit">Email Password Reset Link </button>
                            </div>
                            <p class="mt-4 mb-0 text-center">Already have an password?<a class="ms-2" href="{{ route('login') }}">Sign in</a></p>
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

@endsection