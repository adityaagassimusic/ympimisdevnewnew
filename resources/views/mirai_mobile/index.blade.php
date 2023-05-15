@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		MIRAI Mobile Report<span class="text-purple"> モバイルMIRAIの記録</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 2vw; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('/index/master/pkb') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: black;">Master PKB</a>
			<br>
			<br>
			<span style="font-size: 2vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Chousei</a> -->
			<!-- <a href="{{ url('index/competition/attendance') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">YMPI Competition Attendance</a> -->

			<a href="{{ url('index/family_day/attendance') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Family Day Attendance</a>

			<a href="{{ url('index/vehicle/attendance/motor') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Pembagian Stiker Motor</a>
			<a href="{{ url('index/vehicle/attendance/mobil') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Pembagian Stiker Mobil</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 2vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("/index/mirai_mobile/healthy_report") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Attendance & Health Report</a>
			<a href="{{ url("/radar_covid") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Radar Covid19</a>
			<a href="{{ url("index/corona_information") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Daily Corona Data</a>
			<a href="{{ url("index/mirai_mobile/corona_map") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">YMPI Corona Map</a>
			<hr style="border: 1px solid red"> -->
			<a href="{{ url("index/survey_covid") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Survey Covid</a>
			<a href="{{ url("index/survey") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Emergency Survey</a>
			<a href="{{ url("index/vaksin/monitoring") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Vaccine Monitoring</a>
			<!-- <a href="{{ url("index/peduli_lindungi") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Peduli Lindungi</a> -->
			<a href="{{ url("index/pkb") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Surat Pernyataan PKB</a>
			<a href="{{ url("index/kode/etik") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Training Kode Etik Kepatuhan</a>
			<!-- <a href="{{ url("index/data_komunikasi") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Data Komunikasi Lebaran</a> -->
			<a href="{{ url("index/hasil_mcu") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">Hasil Survey MCU</a>
			<a href="{{ url("index/wpos/monitoring") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red">WPOS Monitoring</a>

			<a href="{{ url('index/cool_finding_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Cool Finding Monitoring</a>
			<!-- <a href="{{ url('index/slogan/monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Slogan Mutu YMPI Monitoring</a> -->
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 2vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/mirai_mobile/report_attendance") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Attendance Data</a>
<!-- 			<a href="{{ url("index/mirai_mobile/report_shift") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Group Data</a> 
			<a href="#" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Location Data</a> 
			<a href="{{ url("index/mirai_mobile/report_location") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Location Data</a>  -->
			<a href="{{ url("index/mirai_mobile/report_indication") }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Health Indication Data</a>
			<hr style="border: 1px solid red">
			<a href="{{ url('index/survey_covid/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Survey Covid Report</a>
			<!-- <a href="" class="btn btn-default btn-block" style="font-size: 1.5vw;">&nbsp;</a> -->
			<a href="{{ url('index/guest_assessment/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Guest Assessment Report</a>
			<a href="{{ url('index/vendor_assessment/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Vendor Assessment Report</a>

			<a href="{{ url('index/vaksin/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Vaccination Report</a>
			<!-- <a href="{{ url('index/vaksin/registration/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Vaccination Registration</a> -->

			<!-- <a href="{{ url('index/peduli_lindungi/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Peduli Lindungi Report</a> -->

			<a href="{{ url('index/pkb/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Surat Pernyataan PKB</a>

			<!-- <a href="{{ url('index/competition/registration/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">YMPI Competition</a> -->

			<a href="{{ url('index/wpos/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">WPOS Report</a>

			<a href="{{ url('index/family_day/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Family Day Report</a>

			<a href="{{ url('index/vehicle/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Vehicle Report</a>

			<!-- <a href="{{ url('index/slogan/report') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Slogan Kebijakan Mutu Report</a> -->
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