	@extends('layouts.master')
	@section('stylesheets')
	<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
	<style type="text/css">

	</style>
	@stop
	@section('header')
	<section class="content-header">
		<h1>
			HR Monitoring & Information<span class="text-purple"> 人事モニタリング・人事情報</span>
		</h1>
	</section>
	@stop
	@section('content')
	<section class="content">
		<div class="row">
			<div class="col-xs-4" style="text-align: center;color: green;">
				<span style="font-size: 1.5vw; color: green;"><i class="fa fa-angle-double-down"></i> Overtime Information (残業の情報) <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('index/report/overtime_monthly_fq') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: green;">OT Monitor By CC (Forecast) <br> (コストセンターによる残業管理)</a>
				<a href="{{ url('index/report/overtime_monthly_bdg') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: green;">OT Monitor By CC (Budget) <br> (コストセンターによる残業管理)</a>
				<a href="{{ url('index/report/overtime_yearly') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: green;">Overtime Fiscal Year Resume <br> (年度残業まとめ)</a>
				<a href="{{ url('index/report/overtime_section') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: green;">OT By CC (コストセンター別の残業)</a>
				<a href="{{ url('index/report/overtime_data') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: green;">OT Data (残業データ)</a>
			</div>
			<div class="col-xs-4" style="text-align: center; color: red;">
				<span style="font-size: 1.5vw;"><i class="fa fa-angle-double-down"></i> Presence Information (出勤情報) <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('index/report/employee_resume') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: red;">Employee Resume (従業員のまとめ)</a>
				<a href="{{ url('index/report/absence') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: red;">Daily Attendance (YMPI日常出勤まと)</a>
				<a href="{{ url('index/report/absence_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: red;">Daily Absence Monitoring (日次出席監視)</a>
				<a href="{{ url('index/report/attendance_data') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: red;">Attendance Data (出席データ)</a>
				<a href="{{ url('index/report/checklog_data') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: red;">Checklog Data (出退勤登録データ)</a>
			</div>
			<div class="col-xs-4" style="text-align: center; color: purple;">
				<span style="font-size: 1.5vw;"><i class="fa fa-angle-double-down"></i> Manpower Information (人工の情報) <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('index/report/manpower') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: purple;">Manpower Information (人工の情報)</a>
			</div>

			<div class="col-xs-4" style="text-align: center; color: blue;">
				<span style="font-size: 1.5vw;"><i class="fa fa-angle-double-down"></i> Clinic (クリニック) <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('index/display/clinic_disease?month=') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: blue;">Clinic Diagnostic Data (クリニック見立てデータ)</a>
				<a href="{{ url('index/display/clinic_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: blue;">Clinic Monitoring (クリニック監視)</a>
				<a href="{{ url('index/display/clinic_visit?datefrom=&dateto=') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: blue;">Clinic Visit (クリニック訪問)</a>
			</div>

			<div class="col-xs-4" style="text-align: center; color: brown;">
				<span style="font-size: 1.5vw;"><i class="fa fa-angle-double-down"></i> Visitor (ビジター) <i class="fa fa-angle-double-down"></i></span>
				<a href="{{ url('visitor_display') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: brown;">Visitor Data (ビジターデータ)</a>
				<a href="{{ url('visitor_index') }}" class="btn btn-default btn-block" style="font-size: 1.1vw; border-color: brown;">Visitor Control Security (ビジターコントロール)</a>
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