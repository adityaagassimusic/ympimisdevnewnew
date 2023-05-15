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
		Flute Welding WIP<span class="text-purple"> 表面処理</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Stock Taking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/stocktaking/silver", "fl_welding") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Silver</a>

			@foreach(Auth::user()->role->permissions as $perm)
			@php
			$navs[] = $perm->navigation_code;
			@endphp
			@endforeach

			@if(in_array('A10', $navs))
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Master Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/operator','fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Master Operator <span class="highlight-process">Flute</span></a>
			<a href="{{ url('index/welding/master_kanban','fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Master Kanban <span class="highlight-process">Flute</span></a>
			<a href="{{ url('index/welding/master_material','fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Master Material <span class="highlight-process">Flute</span></a>
			<!-- <a href="{{ url("index/middle/buffing_target/wld") }}" class="btn btn-default btn-block tab-process tab-process-body" style="font-size: 17px; border-color: green;">Welding Target <span class="highlight-black">Flute</span></a> -->
			@endif



			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa", "phs-visual-fl") }}" class="btn btn-default btn-block tab-process" style="font-size: 17px; border-color: green;">PHS <span class="highlight-process">Key</span> Kensa Visual</a>
			<a href="{{ url("index/welding/kensa", "hsa-visual-fl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">HSA <span class="highlight-process">Key</span> Kensa Visual</a>
			<a href="{{ url("index/welding/kensa", "hsa-dimensi-fl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Kensa Dimensi <span class="highlight-process">Key</span></a>

		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/display_production_result/fl?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">Production Result <span class="highlight-display">Flute</span></a>
			<!-- <span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span> -->
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/welding_board','hsa-fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">HSA Welding Board <span class="highlight-display">Flute</span></a>
			<a href="{{ url('index/welding/welding_board','phs-fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">PHS Welding Board <span class="highlight-display">Flute</span></a>
			<a href="{{ url('index/welding/welding_board','hpp-fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">HPP Welding Board <span class="highlight-display">Flute</span></a>
			<a href="{{ url('index/welding/welding_board','cuci-solder-fl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">Cuci Asam Welding Board <span class="highlight-display">Flute</span></a>
			<a href="{{ url("index/welding/ng_rate/fl?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">NG Rate <span class="highlight-display">Flute</span></a>
			<a href="{{ url("index/welding/op_ng/fl?tanggal=&location=&group=") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">Operator NG Rate <span class="highlight-display">Flute</span></a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/report_ng/fl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;">Not Good Record <span class="highlight-report">Flute</span></a>
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