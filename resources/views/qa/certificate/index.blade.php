@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.inprocess{
		font-weight: bold;
		color: green;
		font-size: 20px;
	}

	.inprocessdisplay{
		font-weight: bold;
		color: red;
		font-size: 20px;
	}

	.fg{
		font-weight: bold;
		color: green;
		font-size: 20px;
	}

	.fgdisplay{
		font-weight: bold;
		color: red;
		font-size: 20px;
	}

	.tab-process:hover{
		font-weight: bold;
		font-size: 22px;
	}

	.tab-fg:hover{
		font-weight: bold;
		font-size: 22px;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<small><span class="text-purple"> {{$title_jp}}</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center; color: red;">
			<!-- <span style="font-size: 1.7vw; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span> -->
			<span style="font-size: 1.7vw; color: green;"><i class="fa fa-angle-double-down"></i> Certificate Submission <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/submission/qa/certificate') }}" class="btn btn-default btn-block tab-fg" style="font-size: 1.3vw; border-color: green;border-radius: 50px;"> Certificate Submission</a>
			<!-- <a href="{{ url('index/qa/certificate/code') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: black;"> Master Certificate Code</a> -->
			<!-- <br> -->
			<br>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.7vw; color: green;"><i class="fa fa-angle-double-down"></i> Input Certificate <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('new/qa/certificate') }}" class="btn btn-default btn-block tab-fg" style="font-size: 1.3vw; border-color: green;border-radius: 50px;"> New Certificate <span class="fg">FG / KD</span></a>
			<a href="{{ url('renew/qa/certificate/000') }}" class="btn btn-default btn-block tab-fg" style="font-size: 1.3vw; border-color: green;border-radius: 50px;"> Renew Certificate <span class="fg">FG / KD</span></a>
			<a href="{{ url('new/qa/certificate/inprocess') }}" class="btn btn-default btn-block tab-process" style="font-size: 1.3vw; border-color: green;border-radius: 50px;"> New Certificate <span class="inprocess">Inprocess</span></a>
			<a href="{{ url('renew/qa/certificate/inprocess/000') }}" class="btn btn-default btn-block tab-process" style="font-size: 1.3vw; border-color: green;border-radius: 50px;"> Renew Certificate <span class="inprocess">Inprocess</span></a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.7vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/certificate/code') }}" class="btn btn-default btn-block tab-fg" style="font-size: 1.3vw; border-color:red;border-radius: 50px;padding-top: 14px;padding-bottom: 14px;"> Kensa Certificate <span class="fgdisplay">FG / KD</span> Monitoring (By Area)</a>
			<a href='{{ url("index/qa/certificate/schedule") }}' class="btn btn-default btn-block tab-fg" style="font-size: 1.3vw; border-color: red;border-radius: 50px;padding-top: 14px;padding-bottom: 14px;">Renewal Schedule Monitoring <span class="fgdisplay">FG / KD</span>  (By Periode)</a>
			<a href="{{ url('index/qa/certificate/code/inprocess') }}" class="btn btn-default btn-block tab-process" style="font-size: 1.3vw; border-color:red;border-radius: 50px;padding-top: 14px;padding-bottom: 14px;"> Kensa Certificate <span class="inprocessdisplay">Inprocess</span> Monitoring (By Area)</a>
		</div>
		<!-- <div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.7vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span> -->
			<!-- <a href="{{ url('index/qa/report/incoming') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Report Kensa Certificate</a> -->
		<!-- </div> -->
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