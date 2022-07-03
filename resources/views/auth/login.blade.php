@extends('layouts.app')

@section('content')
<body class="hold-transition login-page">
<div class="login-box" style="width: 750px;">
 
  <!-- /.login-logo -->
  <div class="row">
    <div class="card col-md-6">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="input-group mb-3">

            <input 
              type="text" 
              class="form-control @error('email') is-invalid @enderror" 
              value="{{ old('email') }}" 
              name="email"
              id="email"
              required 
              autocomplete="email" 
              autofocus
              placeholder="Email"
              >
            
            @error('email')
            {{ $message }}
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
            @enderror
            
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input 
              id="password" 
              type="password" 
              class="form-control @error('password') is-invalid @enderror" 
              name="password" 
              required 
              placeholder="Password"
              autocomplete="current-password"
            >
            @error('password')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
            @enderror

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8 icheck-primary">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <p class="mb-1 mt-2 text-muted text-sm">
          *Login menggunakan password UIM
        </p>
        {{-- <p class="mb-0">
          <a href="{{ route ('register') }}" class="text-center">Register a new membership</a>
        </p> --}}
      </div>
    </div>
    <div class="card col-md-6">
      <div class="login-logo" style="padding-top: 22%;">
        <a href="../../index2.html" style="margin-top: 50%;">Bank <b>BJB</b></a><br>
        <span class="text-md">Consumer Loan Payment System V.1.0</span>
      </div>
    </div>
  </div>
<!-- /.login-box -->
</body>
@endsection

@section('script')

  <!-- jQuery -->
  <script src="{{ url('adminlte/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ url('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ url('adminlte/dist/js/adminlte.min.js') }}"></script>

@endsection
