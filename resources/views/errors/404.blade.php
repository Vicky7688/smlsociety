<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 - Page not found</title>
  <!-- JS -->
  <link rel="stylesheet" href="{{asset('')}}theme/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/typography.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/style.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/responsive.css">
  <link href="{{asset('style.css')}}" rel="stylesheet" type="text/css">
  <link href="{{asset('custom.css')}}" rel="stylesheet" type="text/css">

</head>
<body  style="overflow:hidden;">
      <div class="row mt-5">
        <div class="col-sm-12 text-center">
          <div class="iq-error mt-5">
            <img src="{{asset('error/404.png')}}" class="img-fluid iq-error-img" alt="">
            <h2 class="mb-0 mt-4">Oops! This Page is Not Found.</h2>
            <p>The requested page dose not exist.</p>
            <a class="btn btn-primary" href="{{ route('getdashboard') }}"><i class="ri-home-4-line"></i>Back to Home</a>
          </div>
        </div>
      </div>
</body>

</html>