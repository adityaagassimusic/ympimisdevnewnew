@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	.tab-master {
		font-size : 17px;
		border-color : black;
		color : black;
	}

	.tab-master-block {
		font-size : 17px;
		border-color : black;
		color : white;
		background-color : #808080;
	}

	.tab-master:hover {
		font-weight: bold;
		color: black;
		background-color: #E7E7E7;
		border-color : black;
	}

	.tab-master-block:hover {
		font-weight: bold;
		color: black;
		background-color: #bfbfbf;
		border-color : black;
		color : white;
	}

	.tab-process {
		font-size : 17px;
		border-color : green;
		color : black;
	}

	.tab-process-body {
		font-size : 17px;
		border-color : green;
		color : black;
		background-color : #ccff90;
	}

	.highlight-process {
		font-size : 20px;
		font-weight: bold;
		color: green;
		text-shadow: 1px 1px 5px #ccff90;
	}	

	.tab-process:hover {
		font-weight: bold;
		color: black;
		background-color: #E7E7E7;
		border-color : green;
	}

	.tab-process-body:hover {
		font-weight: bold;
		color: black;
		background-color: #e1ffbd;
		border-color : green;
	}

	.tab-display {
		font-size : 17px;
		border-color : red;
		color : black;
	}

	.tab-display-body {
		font-size : 17px;
		border-color : red;
		color : black;
		background-color : #ff5757;
	}

	.tab-display:hover {
		font-weight: bold;
		color: black;
		background-color: #E7E7E7;
		border-color : purple;
	}

	.tab-display-body:hover {
		font-weight: bold;
		color: black;
		background-color: #ff9999;
		border-color : red;
	}

	.highlight-display {
		font-size : 20px;
		font-weight: bold;
		color: #C51C5A;
		text-shadow: 1px 1px 5px #ff9999;
	}	

	.tab-report {
		font-size : 17px;
		border-color : purple;
		color : black;	
	}

	.tab-report-body {
		font-size : 17px;
		border-color : purple;
		color : black;
		background-color : #a6a3f0;
	}

	.tab-report:hover {
		font-weight: bold;
		color: black;
		background-color: #E7E7E7;
		border-color : purple;
	}

	.tab-report-body:hover {
		font-weight: bold;
		color: black;
		background-color: #c1bff2;
		border-color : purple;
	}

	.highlight-report {
		font-size : 20px;
		font-weight: bold;
		color: purple;
		text-shadow: 1px 1px 5px #c1bff2;
	}

	.highlight-black {
		font-size : 20px;
		font-weight: bold;
	}


</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Picking & Stock Monitoring<span class="text-purple"> ピッキングと在庫の監視</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach


	<div class="row" style="margin-top: 1%;">
		
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="text-align: center;">
			<span style="font-size: 22px; color: black; font-weight: bold;"><i class="fa fa-angle-double-down"></i> KEY <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/display/sub_assy/assy_fl?date=&order2=") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Flute</span> Key Picking Monitor</a>
			<a href="{{ url("/index/display/sub_assy/assy_cl?date=&order2=") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Clarinet</span> Key Picking Monitor</a>
			<a href="{{ url("/index/display/sub_assy/assy_sax?date=&surface2=&key2=&model2=&hpl2=&order2=") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Saxophone</span> Key Picking Monitor</a>
		</div>

		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="text-align: center;">
			<span style="font-size: 22px; color: black; font-weight: bold;"><i class="fa fa-angle-double-down"></i> BODY <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/display/body/fl_body?date=&order2=") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Flute</span> Body Picking Monitor</a>
			<a href="{{ url("/index/display/body/sax_body?date=&surface2=&key2=&model2=&hpl2=&order2=") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Saxophone</span> Body Picking Monitor</a>
		</div>


		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="text-align: center;">
			<span style="font-size: 22px; color: black; font-weight: bold;"><i class="fa fa-angle-double-down"></i> OTHER <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/process/tanpo_stock_monitoring") }}" class="btn btn-default btn-block tab-master"><span class="highlight-report">Tanpo</span> Stock Monitor</a>
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