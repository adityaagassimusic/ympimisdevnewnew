@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> 成形</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-6" style="text-align: center; color: red;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process & Report <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url('index/kecelakaan/kerja') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Informasi Kecelakaan Kerja</a>
			<a href="{{ url('index/kecelakaan/lalu_lintas') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Informasi Kecelakaan Lalu Lintas</a>
			<!-- <a href="{{ url('index/kecelakaan_lalu_lintas_resmi') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Laporan KLL Resmi</a> -->
			<!-- <a href="{{ url('index/kecelakaan/yokotenkai') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Form Yokotenkai</a> -->
		</div>
		<div class="col-xs-6" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<!-- <a href="{{ url('monitoring/kecelakaan_kerja') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;"> Monitoring Kecelakaan Kerja</a> -->

			<a href="{{ url('monitoring/yokotenkai') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;"> Monitoring Konten Yokotenkai</a>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
@endsection