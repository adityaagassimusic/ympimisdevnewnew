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

			<a href="{{ url('index/qc_report/create') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Create/Issue CPAR</a>
			<a href="{{ url('index/qc_report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> CPAR Data</a>
			<a href="{{ url('index/market_claim') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Input Market Claim Data</a>

		</div>
		<div class="col-xs-6" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url('index/qc_report/grafik_cpar') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;"> CPAR & CAR Monitoring</a>
			<a href='{{ url("index/qc_report/cpar_meeting") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">CPAR By Meeting</a>
			<a href='{{ url("index/cpar/resume") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Resume CPAR & CAR</a>
			<a href='{{ url("index/qc_report/grafik_kategori") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">CPAR By Category</a>
			<a href='{{ url("index/qc_report/grafik_tanggungan") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Monitoring PIC On Progress CPAR-CAR</a>
			<a href='{{ url("index/qc_report/market_claim/wi") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Market Claim WI</a>
			<a href='{{ url("index/qc_report/market_claim/edin") }}' class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Market Claim EDIN</a>

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