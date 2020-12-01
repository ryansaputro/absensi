<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Required meta tags-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="RFID">
  <meta name="author" content="Ryan Saputro">
  <meta name="keywords" content="RFID, Dashbord for monitoring, RFID Total Solution, System RFID">
  <title>{{ config('app.name', 'RFID') }}</title>
  <!-- Fonts -->
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  {{-- <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css"> --}}
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;1,300&display=swap" rel="stylesheet">
  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

  <!--Cool Admin-->
  <link href="{{ asset('css/font-face.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/font-awesome-4.7/css/font-awesome.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/font-awesome-5/css/fontawesome-all.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">
  <!-- Bootstrap CSS-->
  <link href="{{ asset('vendor/bootstrap-4.1/bootstrap.min.css') }}" rel="stylesheet" media="all">
  <!-- Vendor CSS-->
  <link href="{{ asset('vendor/animsition/animsition.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/wow/animate.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/slick/slick.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" media="all">
  <link href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet" media="all">
  <!-- Main CSS-->
  <link href="css/theme.css" rel="stylesheet" media="all">
</head>
<body class="animsition">
    <div class="page-wrapper">
        <div id="RyanApp">
            <index></index>
        </div>
    </div>
  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>
  <!-- Jquery JS-->
  <script src="{{ asset('vendor/jquery-3.2.1.min.js') }}"></script>
  <!-- Bootstrap JS-->
  <script src="{{ asset('vendor/bootstrap-4.1/popper.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap-4.1/bootstrap.min.js') }}"></script>
  <!-- Vendor JS       -->
  <script src="{{ asset('vendor/slick/slick.min.js') }}"></script>
  <script src="{{ asset('vendor/wow/wow.min.js') }}"></script>
  <script src="{{ asset('vendor/animsition/animsition.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
  <script src="{{ asset('vendor/counter-up/jquery.waypoints.min.js') }}"></script>
  <script src="{{ asset('vendor/counter-up/jquery.counterup.min.js') }}"></script>
  <script src="{{ asset('vendor/circle-progress/circle-progress.min.js') }}"></script>
  <script src="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('vendor/chartjs/Chart.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    
    <!-- Main JS-->
  <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>