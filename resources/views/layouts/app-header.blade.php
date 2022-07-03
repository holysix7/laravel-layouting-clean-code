@extends('layouts.app-heading')
<header id="header" class="backgroundLp">
  <div class="container row">
    <div>
      <div class="headerNewVamp">
        <div class="col-md-2">
          <h1 class="logo me-auto">
            <a href="index.html"></a>
            <img src="{{ asset('images/logo-jmto-full.png') }}" alt="JasaMargaLogo" class="brand-image" style="float: none;">
          </h1>
        </div>
        <div class="col-md-8">
          <nav id="navbar" class="navbar">
            <ul>
              <li><a class="nav-link scrollto fontPrimary active" href="{{ route('home') }}" style="font-weight: bold">Home</a></li>
              <li><a class="nav-link scrollto fontPrimary" href="javascript:void(0)" style="font-weight: bold">About</a></li>
              <li><a class="nav-link scrollto fontPrimary" href="javascript:void(0)" style="font-weight: bold">Services</a></li>
              <li><a class="nav-link scrollto fontPrimary" href="javascript:void(0)" style="font-weight: bold">Team</a></li>
              <li><a class="nav-link scrollto fontPrimary" href="javascript:void(0)" style="font-weight: bold">Contact</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
          </nav>
        </div>
        <div class="col-md-2">
          <nav id="navbar" class="navbar" style="justify-content: flex-end">
            <ul>
              <li><a class="getstarted scrollto fontPrimary btn-primary" href="{{asset('/login')}}">Login</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>
