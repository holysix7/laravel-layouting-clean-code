@extends('layouts.app')

@section('content')
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="card card-primary card-outline card-outline-tabs col-sm-12">
          <div class="card-body">
            <h4>Selamat datang {{ session('user')->name }} <h4>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
