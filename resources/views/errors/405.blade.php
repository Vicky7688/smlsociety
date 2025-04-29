<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>405 - Service Down</title>
  <!-- JS -->
  <link rel="stylesheet" href="{{asset('')}}theme/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/typography.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/style.css">
  <link rel="stylesheet" href="{{asset('')}}theme/css/responsive.css">
  <link href="{{asset('style.css')}}" rel="stylesheet" type="text/css">
  <link href="{{asset('custom.css')}}" rel="stylesheet" type="text/css">

</head>
<body  style="overflow:hidden;">
      <div class="row">
        <div class="col-sm-12 text-center">
          <div class="iq-error mt-5">
            <img src="{{asset('error/02.png')}}" class="img-fluid iq-error-img" alt="">
            <h2 class="mb-0 mt-4">Service is down</h2>
            <p>Service is down for some time. Please check back soon.</p>
            <a class="btn btn-primary" href="{{ route('getdashboard') }}"><i class="ri-home-4-line"></i>Go To Dashboard</a>
          </div>
        </div>
      </div>
</body>

</html>


