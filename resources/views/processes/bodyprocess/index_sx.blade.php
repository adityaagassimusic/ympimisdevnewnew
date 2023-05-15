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

	.colCustomStyle span{
		text-align: left !important; 
	}

	.colCustomStyle a{
		text-align: left !important; 
	}

	.colCustomStyle a i{
		border-right: 1px #33333399 solid !important;
		font-size: 20px;
		padding-right: 5px ;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1 style="border-bottom:1px #33333333 solid; padding-bottom: 1rem;">
		Saxophone Body Process<span class="text-purple"> サックス</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>    
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-3 colCustomStyle" style=" color: #333;">
			<span style="font-size: 30px;"> Master Process </span>			
			<a href="{{ route("bodyPartsProcessMasterOperator","sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: #333;"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Master Operator</a>
			<a href="{{ route("bodyPartsProcessMasterKanban", "sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: #333;"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> Master Kanban</a>
			<a href="{{ route("bodyPartsProcessMasterMaterials", "sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: #333;"><i class="fa fa-cubes" aria-hidden="true"></i> Master Material</a>
			<a href="{{ route("bodyPartsProcessMasterFlow", "sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: #333;"><i class="fa fa-code-fork" style="font-size: 25px" aria-hidden="true"></i> Master Flow</a>
			<a href="{{ route("bodyPartsProcessMasterTarget", "sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: #333;"><i class="fa fa-line-chart" style="font-size: 20px" aria-hidden="true"></i> Master Target</a>
		</div>
		<div class="col-xs-3 colCustomStyle" style="">
			{{-- <span style="font-size: 30px; color: green;"> Stock Taking </span>
			<a href="{{ url("index/stocktaking/silver", "sx_bpro") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;"><i class="fa fa-cubes" aria-hidden="true"></i> Silver</a>										 --}}

			<span style="font-size: 30px; color: green;"> Process</span>															
			<a href="{{ route("bodyPartsProcessKensa", "asbody-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> AS BODY</span></a>
			<a href="{{ route("bodyPartsProcessKensa", "asbell-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> AS BELL</span></a>
			<a href="{{ route("bodyPartsProcessKensa", "asbow-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> AS BOW</span></a>
			<a href="{{ route("bodyPartsProcessKensa", "asneck-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> AS NECK</span></a>
			<a href="{{ route("bodyPartsProcessKensa", "tsneck-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> TS NECK</span></a>
			<a href="{{ route("bodyPartsProcessKensa", "bellyds-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;"><i class="fa fa-list-ul" style="font-size: 16px" aria-hidden="true"></i> Kensa <span class="highlight-process"> BELL YDS</span></a>
			
		</div>
		<div class="col-xs-3 colCustomStyle" style=" color: red;">
			{{-- <span style="font-size: 30px;"> Display </span>
			<a href="{{ url('index/body_parts_process/display_production_result/fl?tanggal=&location=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Production Result <span class="highlight-display"> Saxophone</span></a> --}}

			<span style="font-size: 30px;"> Display </span>				
			<a href="{{ url('index/body_parts_process/ng_rate/sx?tanggal=&location=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;"><i class="fa fa-television" aria-hidden="true"></i> NG Rate <span class="highlight-display">Sax</span></a>
			<a href="{{ url("index/body_parts_process/op_ng/sx?tanggal=&location=&group=") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;"><i class="fa fa-television" aria-hidden="true"></i> Operator NG Rate <span class="highlight-display">Sax</span></a>			
			<a href="{{ route('bodyPartsProcessBoard','washing-sx') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;"><i class="fa fa-television" aria-hidden="true"></i> Washing Board <span class="highlight-display">Sax</span></a>
		</div>
		<div class="col-xs-3 colCustomStyle" style=" color: purple;">
			<span style="font-size: 30px;"> Report </span>
			<a href="{{ route('bodyPartsProcessNgRecord','sx') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i>Not Good Record <span class="highlight-report"> Sax</span></a>
			<span style="font-size: 30px;"> Resume Report </span>
			<a href="{{ url('index/body_parts_process/report_resume_ng/asbody-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> AS BODY</span></a>			
			<a href="{{ url('index/body_parts_process/report_resume_ng/asbell-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> AS BELL</span></a>			
			<a href="{{ url('index/body_parts_process/report_resume_ng/asbow-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> AS BOW</span></a>			
			<a href="{{ url('index/body_parts_process/report_resume_ng/asneck-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> AS NECK</span></a>			
			<a href="{{ url('index/body_parts_process/report_resume_ng/tsneck-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> TS NECK</span></a>			
			<a href="{{ url('index/body_parts_process/report_resume_ng/bellyds-kensa-sx?bulan=&fy=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;"><i class="fa fa-file-text-o" aria-hidden="true"></i> Resume Kensa<span class="highlight-report"> BELL YDS</span></a>
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