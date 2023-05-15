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
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			
			<a style="font-size: 2vw; border-color: green;" class="btn btn-default btn-block" href="{{ url("index/maintenance/list/user") }}">
				Create SPK
			</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/spk/grafik") }}">
				SPK Monitoring
				<!-- (作業依頼書の管理) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/spk/workload") }}">
				SPK Workload
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/operator/workload") }}">
				Operator Workload
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/spk/weekly") }}">
				SPK Weekly Report
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