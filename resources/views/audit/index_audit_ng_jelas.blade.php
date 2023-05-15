@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}}<small class="text-purple">{{$title_jp}}</small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.6vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_ng_jelas/schedule') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Schedule Audit NG Jelas</a>
			<a href="{{ url('index/audit_ng_jelas') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Audit NG Jelas</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.6vw; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/audit_ng_jelas_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">Audit NG Jelas Monitoring</a>
		</div>

		<div class="col-xs-4" style="text-align: center;margin-top: ">
			<span style="font-size: 1.6vw; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/audit_ng_jelas_report') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Report Audit NG Jelas</a>
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