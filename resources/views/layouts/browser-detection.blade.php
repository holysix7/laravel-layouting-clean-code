<!DOCTYPE html>
<html lang="en">
  @include('layouts.app-heading')
  <body>
    <section class="d-flex align-items-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200"></div>
          <div class="col-lg-4 order-1 order-lg-2 hero-img card justify-content-center">
            <div class="card-body login-card-body">
              <div class="d-flex justify-content-center">
                <div class="row">
                  <img src="{{ asset('images/login-logo.png') }}" alt="">
                </div>
              </div>
              <div class="column d-flex justify-content-center" style="padding-top: 10px; padding-bottom: 20px;">
                <div class="row">
                  <h3 class="text-center login-text" style="width:100%">Warning</h3>
                </div>
              </div>

              <!-- Disini konten peringatannya -->
              <div class="alert alert-danger">
                <h4 class="alert-heading text-center">{{ $message_heading }}</h4>
                <p>{{ $message_content }}</p>
              </div>
              <div class="row d-flex">
                <p class="text-muted justify-content-center align-items-center">&copy; PT Jasamarga Tollroad Operator.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>    
  </body>
</html>