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
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a style="font-size: 2vw; border-color: green;" class="btn btn-default btn-block" href="{{ url("index/workshop/create_wjo") }}">
				Create WJO 
				<!-- (作業依頼書の作成) -->
			</a>

			<a style="font-size: 2vw; border-color: green;" class="btn btn-default btn-block" href="{{ url("index/workshop/create_jig_wjo") }}">
				Create WJO (Jig - ST)
			</a>

		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/wjo_monitoring") }}">
				WJO Monitoring 
				<!-- (作業依頼書の監視) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/productivity") }}">
				Workshop Productivity
				 <!-- (作業依頼書の実現力) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/workload") }}">
				Workshop Workload 
				<!-- (作業依頼書一覧) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/workload/machine") }}">
				Workshop Machine Workload
				 <!-- () -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/operatorload") }}">
				Workshop Operator Work Schedule 
				<!-- (ワークショップ作業者の作業予定) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/workshop/monitoring/jig") }}">
				Workshop Jig Monitoring 
				<!-- (ワークショップ作業者の作業予定) -->
			</a>
			
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