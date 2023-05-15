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
		<div class="col-xs-4" style="text-align: center;color: red;">
			<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Utility <i class="fa fa-angle-double-down"></i></span>
			
			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/apar") }}">
				APAR Check Schedule
				<!-- (消火器・消火栓の点検日程) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/apar/expire") }}">
				APAR Expired List
				<!-- (消火器・消火栓の使用期限一覧) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/apar/resume") }}">
				APAR Resume
				<!-- (消火栓・消火器の点検進捗のまとめ) -->
			</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Plan Maintenance <i class="fa fa-angle-double-down"></i></span>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/pm/monitoring") }}">
				WWT - Waste Control
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/pm/monitoring") }}">
				Plan Maintenance Monitoring
				<!-- (保全計画の監視) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/pm/trendline") }}">
				Plan Maintenance Trendline Graph
				<!-- (保全計画の監視) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/pm/resume") }}">
				Planned Maintenance Resume
				<!-- (保全計画の監視) -->
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