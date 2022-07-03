@php
$alert = Session::get('alert-type');
$message = Session::get('message');
@endphp
@if ($alert == 'success')
<div class="alert alert-success alert-block d-print-none">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{!! $message !!}</strong>
</div>
@endif


@if ($alert == 'error')
<div class="alert alert-danger alert-block d-print-none">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{!! $message !!}</strong>
</div>
@endif


@if ($alert == 'warning')
<div class="alert alert-warning alert-block d-print-none">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{!! $message !!}</strong>
</div>
@endif


@if ($alert == 'info')
<div class="alert alert-info alert-block d-print-none">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{!! $message !!}</strong>
</div>
@endif


@if ($alert == 'danger')
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{!! $message !!}</strong>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-block d-print-none">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif