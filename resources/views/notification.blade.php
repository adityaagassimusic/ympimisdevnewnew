@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<div class="error" style="text-align: center;">

			@if($status)
			<p style="font-weight: bold; color: green; font-size: 3vw;">SUCCESS!</p>
			@else
			<p style="font-weight: bold; color: red; font-size: 3vw;">FAILED!</p>
			@endif
			<p>({{ $form->form_id }})<br/>{{ $form->form_name }}</p>
			<p style="font-weight: bold; font-size: 1.5vw;">{{ $message }}</p>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

	});
</script>
@endsection