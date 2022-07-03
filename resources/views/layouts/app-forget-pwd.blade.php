<!DOCTYPE html>
<html lang="en">
  
@include('layouts.app-heading')

<body>
  {{-- @include('layouts.app-header') --}}
  <!-- ======= Hero Section ======= -->
  <section class="d-flex align-items-center">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200">
        </div>
        <div class="col-lg-4 order-1 order-lg-2 hero-img card justify-content-center">
          <div class="card-body login-card-body">
            @include('layouts.flash-message')
            <div class="d-flex justify-content-center">
              <div class="row">
                <img src="{{asset('images/login-logo.png')}}" >
              </div>
            </div>
            <div class="column d-flex justify-content-center" style="padding-top: 10px; padding-bottom: 20px;">
              <div class="row">
                <h3 class="text-center login-text" style="width: 100%">Lupa Password</h3>
                @if($errors->any())
                  @foreach($errors->all() as $error)
                    <p class="text-center" style="color: red">{{$error}}</p>
                  @endforeach
                @endif
              </div>
            </div>
      
            <form action="{{ route('forgetpwd.check') }}" method="post">
              @csrf
              <label class="for-label">Masukan alamat email yang terdaftar</label>
              <div class="input-group mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email">
              </div>
              <div class="card">
                <div class="card-header">
                  Captcha
                </div>
                <div class="card-body">
                  <p>Pertanyaan ini untuk membuktikan apakah Anda adalah pengunjung manusia atau bukan dan untuk mencegah pengiriman spam otomatis.<br> Pertanyaan Matematika</p>
                  <span id="captcha-img">
                    {!!captcha_img('custom')!!}
                  </span>
                  <button class="btn btn-light" id="reload"><i class="fa fa-refresh"></i></button>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" id="captcha_form" name="captcha" placeholder="Jawaban" />
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary login-btn">
                Kirim
              </button>
              <div class="row d-flex">
                <p class="text-muted justify-content-center align-items-center">&copy; PT Jasamarga Tollroad Operator.</p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section><!-- End Hero -->

  {{-- <footer id="footer">
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
  </footer> --}}

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">JMTO GUARD</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row">
          <div class="col-sm-2" style="font-size: 65px;">
            <i class="fa fa-envelope"></i>
          </div>
          <div class="col-sm-10">
            <h3>Hello</h3>
            <p>Kami melihat kamu baru saja masuk site JMTO dari browser baru atau komputer baru</p>

          </div>
          <span class="text-muted text-sm">Sebagai penambahan keamanan, Kamu butuh memberikan akses untuk browser ini dengan memasukan kode spesial yang telah kami
            kirimkan ke alamat email pribadimu di gmail.com.
          </span>
          <div class="input-group mt-2">
            <input type="text" class="form-control col-sm-5" placeholder="" autofocus>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-key"></span>
              </div>
            </div>
          </div>
          <div class="col-sm-5 mt-2">
            <a href="{{ route('dashboard.main') }}">
              <button type="button" class="btn btn-primary">Submit Kode Spesial</button>
            </a><br>
            <a href="" class="text-sm text-primary">Kirim ulang kode spesial</a>
          </div>
         </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- Vendor JS Files -->
  <script type="text/javascript">
    $(document).ready(function(){
      $('#reload').on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: "/reload",
          type: 'GET',
          success: function(response){
            // console.log('======')
            // console.log(response)
            $('#captcha-img').html(response.captcha)
          }
        })
      })
    })
    
    function password_show_hide() {
      var x = document.getElementById("password");
      var show_eye = document.getElementById("show_eye");
      var hide_eye = document.getElementById("hide_eye");
      hide_eye.classList.remove("d-none");
      if (x.type === "password") {
        x.type = "text";
        show_eye.style.display = "none";
        hide_eye.style.display = "block";
      } else {
        x.type = "password";
        show_eye.style.display = "block";
        hide_eye.style.display = "none";
      }
    }

    function accord_captcha(id){
      if (id.className.indexOf("w3-show") == -1) {
        id.className += " w3-show";
      } else { 
        id.className = id.className.replace(" w3-show", "");
      }
    }
  </script>
</body>

</html>
