<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  {{-- {{dd(request()->segment(1))}} --}}
  @if(request()->segment(1) == 'change-password')
  <title>Laravel Fabric JS | Change Password</title>
  @else
  <title>Laravel Fabric JS | Login</title>
  @endif
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Google Fonts -->

  <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="icon" type="image/x-icon" href="{{ asset('images/laravel400x400.jpg') }}">


  <!-- Vendor CSS Files -->
  <link href="{{ asset('arsha/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('app.css') }}" rel="stylesheet">
  <link href="{{ asset('main.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

  <!-- Template Main CSS File -->
  <link href="{{ asset('arsha/css/style.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Arsha - v4.7.1
  * Template asset: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/sparklines/sparkline.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
  <script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>
  <script src="{{ asset('adminlte/dist/js/demo.js') }}"></script>
  <script src="{{ asset('adminlte/dist/js/pages/dashboard.js') }}"></script>
  <!-- Vendor JS Files -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="{{ asset('arsha/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('arsha/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('arsha/vendor/php-email-form/validate.js') }}"></script>
  
  <!-- Template Main JS File -->
  <script src="{{ asset('arsha/js/main.js') }}"></script>
</head>