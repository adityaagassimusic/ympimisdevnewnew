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
			<a href="{{ url('index/sakurentsu/summary/3m') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">3M Summary</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<a href="{{ url('index/sakurentsu/summary/trial') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;" >Trial Request Summary</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<a href="{{ url('index/sakurentsu/summary/information') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Information Summary</a>
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