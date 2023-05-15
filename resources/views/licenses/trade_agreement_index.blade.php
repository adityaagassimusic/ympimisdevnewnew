	@extends('layouts.master')
	@section('stylesheets')
	<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
	<style type="text/css">

	</style>
	@stop
	@section('header')
	<section class="content-header">
		<h1>
			{{ $title }}<span class="text-purple"> {{ $title_jp}}</span>
		</h1>
	</section>
	@stop
	@section('content')
	<section class="content">
		<div class="row">
			<div class="col-xs-4" style="text-align: center;color: green;">
				<span style="font-size: 2vw; color: green;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			</div>
			<div class="col-xs-4" style="text-align: center; color: red;">
				<span style="font-size: 2vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('index/trade_agreement/monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">MTA Monitoring</a>
				<a href="{{ url('index/trade_agreement/list') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Supplier List</a>
				<a href="{{ url('index/trade_agreement/monitoring_cms') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">CMS Response Monitoring</a>
			</div>
			<div class="col-xs-4" style="text-align: center; color: purple;">
				<span style="font-size: 2vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
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