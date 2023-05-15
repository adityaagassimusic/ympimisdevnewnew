@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		GS Control Activity
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			@if($role_code == 'S-MIS' || $role_code == 'C-MIS' || $role_code == 'MIS' || $role_code == 'S-GA' || $role_code == 'C-GA' || $role_code == 'GA' || $role_code == 'M') 	
			<a href="{{ url("index/gs/operator/job") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Master Operator Job</a>
			<a href="{{ url("gs/control") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Joblist GS Daily</a>
			@endif
		</div>
		
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/aktual/gs") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">GS Productivity</a> 
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/gs_resume") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;">Resume Joblist GS</a>
			<a href="{{ url("index/monitoring/daily") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;">Daily Job Monitoring GS</a>

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