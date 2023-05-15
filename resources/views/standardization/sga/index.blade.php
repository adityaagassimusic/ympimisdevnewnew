@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> <small class="text-purple">小グループ活動（SGA）</small></span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw; color: green;"><i class="fa fa-angle-double-down"></i> Input <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url('index/sga/master') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> SGA Master</a> -->
			<a href="{{ url('index/sga/assessment') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> SGA Assessment</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href='{{ url("index/sga/monitoring") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">SGA Monitoring</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/sga/master') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;"> SGA Master</a>
			<a href="{{ url('index/sga/report') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">SGA Report</a>
			<a href="{{ url('index/sga/point_check') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">SGA Point Check</a>
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