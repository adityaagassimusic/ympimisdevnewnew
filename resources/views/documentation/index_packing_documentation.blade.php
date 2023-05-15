	@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
<!-- 	<h1>
		List Patrol<small class="text-purple"></small>
	</h1> -->
</section>
@stop
@section('content')
<section class="content" style="padding-top:0;">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">(Inner) Dokumentasi Packing<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/packing/documentation/fl') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Packing Flute</a>
			<a href="{{ url('index/packing/documentation/cl') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Packing Clarinet</a>
			<a href="{{ url('index/packing/documentation/sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Packing Saxophone</a>
		</div>
		<div class="col-xs-4" style="text-align: center;margin-top: ">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('report/packing_documentation/flute') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Dokumentasi Flute</a>
			<a href="{{ url('report/packing_documentation/clarinet') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Dokumentasi Clarinet</a>
			<a href="{{ url('report/packing_documentation/saxophone') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Dokumentasi Saxophone</a>
			<a href="{{ url('report/latch/flute') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Flute (New Lock)</a>
			<a href="{{ url('report/latch/saxophone') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Saxophone (Special Acceptance)</a>
			<a href="{{ url('report/latch/clarinet') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Clarinet (New Reed)</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('monitoring/packing_documentation') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Monitoring Documentation Inner</a>
		</div>

		<div class="col-xs-12" style="text-align: center;margin-bottom: 10px;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #009688;padding: 10px">(Outer) Dokumentasi Packing<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/packing_outer/documentation/fl') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Outer Packing Flute</a>
			<a href="{{ url('index/packing_outer/documentation/cl') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Outer Packing Clarinet</a>
		</div>
		<div class="col-xs-4" style="text-align: center;margin-top: ">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('report/packing_outer_documentation/flute') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Outer Flute</a>
			<a href="{{ url('report/packing_outer_documentation/clarinet') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Outer Clarinet</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('monitoring/packing_outer_documentation') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Monitoring Documentation Outer</a>
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
		$('body').toggleClass("sidebar-collapse");
	});
</script>
@endsection