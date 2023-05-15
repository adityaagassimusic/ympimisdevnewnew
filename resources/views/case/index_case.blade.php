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

		<div class="col-xs-12" style="text-align: center;margin-bottom: 10px;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #009688;padding: 10px">Case Control<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/case') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Request Pengambilan Case</a>
			<a href="{{ url('index/case/audit') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit Kesesuaian Case</a>
		</div>
		<div class="col-xs-4" style="text-align: center;margin-top: ">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('report/case') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Pengambilan Case</a>
			<a href="{{ url('report/case/audit') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Audit Case</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<!-- <span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('monitoring/packing_outer_documentation') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Monitoring Documentation Outer</a> -->
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