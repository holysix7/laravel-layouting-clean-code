<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Home Page</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('arsha/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('arsha/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('arsha/css/style.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Arsha - v4.7.1
  * Template URL: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center">

      <h1 class="logo me-auto">
          <a href="index.html"></a>
          <img src="{{ asset('images/logo.png') }}" alt="JasaMargaLogo" class="brand-image" style="float: none;">
        </h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="{{ route('home') }}">Home</a></li>
          <li><a class="nav-link scrollto" href="#about">About</a></li>
          <li><a class="nav-link scrollto" href="#services">Services</a></li>
          <li><a class="nav-link scrollto" href="#team">Team</a></li>
          <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
          <li><a class="getstarted scrollto" href="{{ route('login') }}">Login</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->
  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center">
    <div class="container">
        
      <div class="row">
        <div class="col-lg-12 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
          <div class="card" style="padding-top: 2vh; padding-bottom: 3vh;">
            <div class="card-body register-card-body">
              <h3 class="text-info text-center">Register(Lampiran)</h3>
              <p class="login-box-msg text-center">Lengkapi data lampiran untuk mempercepat petugas kami untuk memvalidasi data merchant anda</p>
        
              <form action="../../index.html" method="post">
                <div class="row">
                  <div class="col-sm-2"></div>
                  <div class="col-sm-4">
                    <form role="form">
                      <div class="card-body">
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>KTP Pemilik</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>Foto Merchant 1</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>SIUP/TDUP</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.card-body -->
      
                      
                    </form>

                  </div>
                  <div class="col-sm-4">
                    <form role="form">
                      <div class="card-body">
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>Foto Selfie + KTP Pemilik</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>Foto Merchant 2</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                        <div class="form-group mb-3">
                          <label for="exampleInputFile mb-1"><b>Dokumen Pendukung Lainnya</b>*</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                            </div>
                          </div>
                        </div>
                      </div>
                  
                  </div>
                  <div class="col-sm-2"></div>
                </div>
              </div>
        
              <div class="social-auth-links text-center">
                <div class="form-group">
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                    <label for="customCheckbox1" class="custom-control-label">I agree to the terms and conditions</label>
                  </div>
                <a href="{{ route('home') }}" class="btn btn-block btn-primary">
                  <i class="fab fa-facebook mr-2"></i>
                  Simpan
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section><!-- End Hero -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container footer-bottom clearfix">
      <div class="copyright">
        &copy; Copyright <strong><span>PT. Reka Cipta Solusi</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('arsha/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('arsha/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('arsha/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('arsha/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('arsha/js/main.js') }}"></script>

</body>

</html>