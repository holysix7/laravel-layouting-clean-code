<nav class="main-header navbar navbar-expand navbar-primary" style="background: #ffffff !important;">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: #000000BF !important;"><i class="fas fa-bars"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" style="color: #000000BF !important; font-weight: 600 !important;">
        {{ucwords(Session::get('user')->name)}}
        <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle" style="width: 30px; height: 30px; margin-left: 10px;" alt="{{ ucwords(Session::get('user')->name) }}">
        <i class="fa fa-angle-down  "></i>
      </a>
      <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="background: #ffffff !important;">
        <a href="{{ route('logout') }}" class="dropdown-item" style="color: #000000BF !important;">
          <i class="fas fa-sign-out-alt mr-2"></i> Log Out
        </a>
        </div>
    </li>
  </ul>
</nav>