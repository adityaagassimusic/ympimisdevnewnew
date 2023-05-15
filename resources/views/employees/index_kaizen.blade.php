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
		<div class="col-xs-4" style="text-align: center; color: blue;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>

			<a style="font-size: 2vw; border-color: blue;" class="btn btn-default btn-block" href="{{ url("index/kaizen/aproval/resume") }}">
				Resume e-Kaizen Progress
				<!-- (E-改善進捗のまとめ) -->
			</a>

			<a style="font-size: 2vw; border-color: blue;" class="btn btn-default btn-block" href="{{ url("index/kaizen/applied") }}">
				Applied List e-Kaizen
				<!-- (未承認E-改善のリスト) -->
			</a>

			<a style="font-size: 2vw; border-color: blue;" class="btn btn-default btn-block" href="{{ url("index/kaizen") }}">
				List Unverified e-Kaizen 
				<!-- (未承認E-改善のリスト) -->
			</a>
		</div>

		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/kaizen2/resume") }}">
				Report All Kaizen
				<!-- (全改善のリポート) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/kaizen2/report") }}">
				Report Kaizen Excellent
				<!-- (エクセレント改善のリポート) -->
			</a>			

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/kaizen2/value") }}">
				Report Kaizen Reward 
				<!-- (改善リポートのリワード) -->
			</a>

			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/kaizen/data") }}">
				Kaizen Teian Data
				<!-- (改善リポートのリワード) -->
			</a>
		</div>

		<div class="col-xs-4" style="text-align: center; color: green;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>

			<a style="font-size: 2vw; border-color: green;" class="btn btn-default btn-block" href="{{ url("index/kaizen/aproval/grafik/resume") }}">Outstanding Approval Kaizen</a>

			<a style="font-size: 2vw; border-color: green;" class="btn btn-default btn-block" href="{{ url("index/kaizen/outstanding/grafik/resume") }}">Resume Outstanding Kaizen</a>
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