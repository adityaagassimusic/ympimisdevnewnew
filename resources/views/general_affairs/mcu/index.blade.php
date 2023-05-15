@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Medical Check Up<small class="text-purple">健康診ち行列</small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.7vw; color: green;"><i class="fa fa-angle-double-down"></i> Process Cek Fisik<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/ga_control/mcu/physical/clinic') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Cek Fisik Petugas Klinik</a>
			<a href="{{ url('index/ga_control/mcu/physical/doctor') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">Cek Fisik Dokter</a>
			<br>
			<span style="font-size: 1.7vw; color: green;"><i class="fa fa-angle-double-down"></i> Process MCU<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/ga_control/mcu/attendance/fy199/ambil_darah_dan_urine') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">MCU Attendance (Darah + Urine)</a>
			<a href="{{ url('index/ga_control/mcu/attendance/fy199/thorax') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">MCU Attendance (Thorax)</a>
			<a href="{{ url('index/ga_control/mcu/attendance/fy199/ecg_treadmill_usg') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">MCU Attendance (ECG + Treadmill + USG)</a>
			<a href="{{ url('index/ga_control/mcu/attendance/fy199/audiometri') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: green;">MCU Attendance (Audiometri)</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.7vw; color: red;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/ga_control/mcu/monitoring/physical') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Monitoring Cek Fisik</a>
			<!-- <a href="{{ url('index/ga_control/mcu/queue') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Medical Check Up Queue</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.7vw; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/ga_control/mcu/report/physical') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Report Cek Fisik</a>
			<a href="{{ url('index/ga_control/mcu/report/physical/format') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Report Format Cek Fisik</a>
			<a href="{{ url('index/ga_control/mcu/report/attendance') }}" class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: purple;">Report MCU Attendance</a>
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