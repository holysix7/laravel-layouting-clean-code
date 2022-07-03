<!DOCTYPE html>
<html lang="en">
  
@include('layouts.app-heading')

<div class="content-header">
  <div class="container-fluid">
      <div class="row mb-2">
          <div class="col-sm-12">
              &nbsp;
          </div><!-- /.col -->
      </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
      <div class="row justify-content-center">
          <div class="col-md-4">
              <div class="card justify-content-center shaodw-lg card-1 border-0 bg-white">
                  <div class="card-body">
                      <div class="row d-flex justify-content-center align-items-center">
                        <img src="{{ asset('images/finish.png') }}">
                      </div>
                      <div class="row">
                        <h3 style="font-size: 23px; d-flex justify-content-center"><b>Reset Lupa Password Berhasil</b></h3>
                      </div>
                      <div class="row">
                        <p class="text-muted">Kami telah mengirimkan link konfirmasi, silahkan untuk mengecek folder email Anda</p>
                      </div>
                      <div class="row">
                          <div class="col">
                              <a href="{{ route('login') }}" class="btn btn-primary-custom btn-block">Kembali Ke Login</a>
                              <br>
                              <p class="small d-flex justify-content-center">© PT Jasamarga Tollroad Operator.</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
</html>
