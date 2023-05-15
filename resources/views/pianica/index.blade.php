@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Pianica<span class="text-purple"> ピアニカ</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.6vw; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/Op") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: black;">Master Operator </a>
			<a href="{{ url("index/Op_Code") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: black;">Master Code Operator</a>

			<span style="font-size: 1.6vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/Bensuki") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Bentsuki-Benage </a>
			<a href="{{ url("index/Pureto") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Pureto </a>
			<a href="{{ url("index/KensaAwal") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Kensa Awal </a>
			<a href="{{ url("index/Assembly") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Assembly </a>
			<a href="{{ url("index/KensaAkhir") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Kensa Akhir </a>
			<a href="{{ url("index/KakuningVisual") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Kakunin Visual </a>

			<a href="{{ url('index/pn/qa_audit') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">QA Audit </a>
			<a href="{{ url('index/pn/card_cleaning') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Card Cleaning</a>
			<a href="{{ url('index/pn/card_migration') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Card Migration</a>

			<span style="font-size: 1.6vw; color: blue;"><i class="fa fa-angle-double-down"></i> Case <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/case_pn/KakuningVisual") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: blue;">Kakuning Visual</a>
			<!-- <br>
			<span style="font-size: 1.6vw; color: magenta;"><i class="fa fa-angle-double-down"></i> QA <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/qa/audit_fg/point_check") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Point Check Audit FG / KD QA</a>
			<a href="{{ url("/index/qa/audit_fg/audit") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Audit FG / KD QA</a>
			<a href="{{ url("/index/qa/audit_fg/report/pianica") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Report FG / KD QA</a>
			<a href="{{ url("/index/qa/packing") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Monitoring FG / KD QA</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw; color: red;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/pianica/monitoring/pn_part") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Quality Monitoring PN Part</a>
			<a href="{{ url('index/display_pn_ng_rate') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG by Operator</a>
			<!-- <a href="{{ url("index/display_pn_ng_trends?location=") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Daily NG by Operator</a> -->
			<!-- <a href="{{ url("index/display_daily_pn_ng?location=") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Daily NG</a> -->
			<a href="{{ url('index/skill_map','pn-assy-initial') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Skill Map Initial</a>
			<a href="{{ url('index/skill_map','pn-assy-final') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Skill Map Final</a>
			<a href="{{ url('index/pn/display/qa_audit') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Display QA Audit</a>
			<a href="{{ url('index/pn/display/kensa_awal?line=&tanggal=') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Quality Monitoring NG Bensuki & Tuning</a>
			<a href="{{ url('/index/reportSpotWelding') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Spot Welding</a>
			<a href="{{ url('/index/reportKensaAwalDaily') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Kensa Awal</a>
			<a href="{{ url('/index/reportAssemblyDaily') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Assembly</a>
			<a href="{{ url('/index/reportKensaAkhirDaily') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Kensa Akhir</a>
			<a href="{{ url('/index/reportVisualDaily') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Kakunin Visual</a>
			<a href="{{ url('/index/pn/stock_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">WIP Monitoring</a>
			<a href="{{ url('/index/pn/board/1') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Tuning Monitoring Line 1</a>
			<a href="{{ url('/index/pn/board/2') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Tuning Monitoring Line 2</a>
			<a href="{{ url('/index/pn/board/3') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Tuning Monitoring Line 3</a>
			<a href="{{ url('/index/pn/board/4') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Tuning Monitoring Line 4</a>
			<a href="{{ url('/index/pn/board/5') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Tuning Monitoring Line 5</a>
			<a href="{{ url('/index/pn/pass_ratio') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Pass Ratio</a>
			<a href="{{ url('/index/pn/ng_trend') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Trend NG Pianica</a>
			<a href="{{ url('/index/case_pn/ng_trend') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend Case</a>
			<a href="{{ url('/index/calendar') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Calendar Code</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('/index/DisplayPN') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Display</a>
			<a href="{{ url("/index/reportBensuki") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Bentsuki</a>			  
			<a href="{{ url("/index/reportAwal") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kensa Awal All Line</a>
			<a href="{{ url("/index/reportAwalLine") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kensa Awal / Line</a>
			<a href="{{ url("/index/reportAkhir") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kensa Akhir All Line</a>
			<a href="{{ url("/index/reportAkhirLine") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kensa Akhir / Line</a>
			<a href="{{ url("/index/reportVisual") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kakunin Visual All Line</a>
			<a href="{{ url("/index/record") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Pianica Inventories</a>

			<a href="{{ url("/index/reportDayAwal") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Monthly Report </a>

			<button type="button" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;" data-toggle="modal" data-target="#skill-map-eval-modal">
				Skill Map Evaluation
			</button>

			<a href="{{ url('/index/pn/audit_screw/report') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Audit Screw</a>

		</div>
	</div>

	<div class="modal fade" id="skill-map-eval-modal">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<center><span style="font-weight: bold; font-size: 18px;">Location</span></center>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="row">
										@foreach($location as $location)
										<a href="{{ url('/report/skill_map_evaluation',$location) }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px;">{{$location}}</a>
										@endforeach
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="modal-footer">
									<div class="row">
										<button type="button" class="btn btn-danger pull-left" data-dismiss="modal" style="width: 100%">Close</button>
									</div>
									<!-- <button value="CONFIRM" onclick="confirm()" class="btn btn-success pull-right">CONFIRM</button> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
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