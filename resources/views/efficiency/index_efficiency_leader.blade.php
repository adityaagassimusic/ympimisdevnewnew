@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(161,134,190);">
					<center><span style="font-weight: bold;">Educational Instrument (EI)</span></center>
				</div>
				<div class="box-body" style="display: block;">

				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(187,230,228);">
					<center><span style="font-weight: bold;">Key Parts Process (WI-KPP)</span></center>
				</div>
				<div class="box-body" style="display: block;">

				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(66,191,221);">
					<center><span style="font-weight: bold;">Body Parts Process (WI-BPP)</span></center>
				</div>
				<div class="box-body" style="display: block;">

				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(201,117,16);">
					<center><span style="font-weight: bold;">Welding Process (WI-WP)</span></center>
				</div>
				<div class="box-body" style="display: block;">

				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(255,210,63);">
					<center><span style="font-weight: bold;">Surface Treatment (WI-ST)</span></center>
				</div>
				<div class="box-body" style="display: block;">
					<a href="{{ url("index/efficiency/report_efficiency_hourly/buffing-key-cl-cl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Buffing KEY CL</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/buffing-key-fl-fl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Buffing KEY FL</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/buffing-key-sx-sx51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Buffing KEY SX</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/buffing-body-fl-fl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Buffing BODY FL</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/buffing-body-sx-sx51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Buffing BODY SX</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/lacquering-key-sx-sx51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">LCQ/PLT KEY SX</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/lacquering-body-sx-sx51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">LCQ/PLT BODY SX</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/plating-key-cl-cl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">PLT KEY CL</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/plating-key-fl-fl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">PLT KEY FL</a>
					<a href="{{ url("index/efficiency/report_efficiency_hourly/plating-body-fl-fl51") }}" class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">PLT BODY FL</a>
				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(238,66,102);">
					<center><span style="font-weight: bold;">Final Assembly (WI-FA)</span></center>
				</div>
				<div class="box-body" style="display: block;">

				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	jQuery(document).ready(function() {

	});
</script>
@endsection