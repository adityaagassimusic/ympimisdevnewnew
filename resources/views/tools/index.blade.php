@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Automation Control And Order Tools Equipment<small class="text-purple"></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">Automation Control And Order Tools Equipment<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url('dies/master') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Master Data</a>
			<hr style="color: red;border: 1px solid red"> -->
			<a href="{{ url('tools/kanban') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Print Kanban</a>
			<a href="{{ url('tools/master') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">All Data </a>
			<a href="{{ url('tools/bom') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">BOM List </a>
			<a href="{{ url('tools/calculation') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Tools/Equipment Stock</a>
			<!-- <a href="{{ url('tools/target') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Tools Production Plan</a> -->
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url('dies/stock_out') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Dies Stock Out</a>
			<hr style="border: 1px solid red"> -->
			<a href="{{ secure_url('tools/stock_out') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Use Tools / Equipment</a>
			<a href="{{ url('tools/need_order') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Order Tools / Equipment </a>
			<a href="{{ url('tools/audit') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit Kesesuaian Tools</a>
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Automation <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('tools/request') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Tools Request By Month</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('tools/monitoring') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Tools Monitoring</a>
			<a href="{{ url('tools/log') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Tools Log Usage</a>
			<a href="{{ url('tools/bom_progress') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Tools BOM Progress</a>
		</div>

<!-- 		<div class="col-xs-12" style="text-align: center;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: purple;padding: 10px">Digital Control Tools<span class="text-purple"></span></h3>

			<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('tools/master') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Tools Master Data</a>
			<a href="{{ url('tools/kanban') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Print Kanban Tools</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="https://10.109.52.1/miraidev/public/tools/stock_out" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Tools Usage</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('tools/calculation/temp') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Tools Calculation</a>
			<a href="{{ url('tools/log') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Tools Log Usage</a>
		</div>
		</div> -->
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