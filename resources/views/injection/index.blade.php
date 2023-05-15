@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> 成形</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<!-- <div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/masterMachine") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Master Machine</a>
			<a href="{{ url("index/masterCycleMachine") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Cycle Time Machine</a>
		</div> -->
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.3vw; color: green;"><i class="fa fa-angle-double-down"></i> Injection Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/in") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> Stock - In</a>

			<a href="{{ url("index/out") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> Stock - Out</a> -->

			<!-- <a href="{{ url("index/Schedule") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Make Schedule</a>

			<a href="{{ url("index/indexPlanAll") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Make Schedule 3 Days</a> -->

			<!-- <a href="{{ url("index/input_stock") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;">Input Daily Stock (Temp)</a> -->

			<a href="{{ url('index/injection_machine') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Machine</a>

			<a href="{{ url('index/injection/dryer_resin') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Dryer - Resin</a>

			<a href="{{ url('index/injection/transaction','in') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Transaction IN</a>

			<a href="{{ url('index/injection/transaction','out') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Transaction OUT</a>

			<a href="{{ url('index/injection/tag') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Tag</a>

			<a href="{{ url('index/injection/traceability') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Traceability</a>

			<a href="{{ url('index/injection/visual') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Visual Check</a>

			<!-- <a href="{{ url('index/maintenance/jishu_hozen') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Jishu Hozen</a>
			<a href="{{ url('index/maintenance/jishu_hozen_point?jishu_id=') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Jishu Hozen Point</a> -->

			<a href="{{ url('index/injection/cleaning') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Injection Equipment Cleaning</a>
			<a href="{{ url('index/maintenance/tbm/injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">TBM Machine Injection</a>

			<span style="font-size: 1.3vw; color: green;"><i class="fa fa-angle-double-down"></i> Molding <i class="fa fa-angle-double-down"></i></span>

			<button type="button" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;" data-toggle="modal" data-target="#push-pull-check-modal">
				Recorder Push Block Check
			</button>

			<a href="{{ url('index/machine_parameter','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Machine Parameter</a>

			<a href="{{ url('index/recorder_process_torque','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Recorder Torque Check</a>

			<a href="{{ url('index/injection/molding') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Molding Setup</a>

			<a href="{{ url('index/injection/molding_maintenance') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Molding Maintenance</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.3vw;"><i class="fa fa-angle-double-down"></i> Display Injeksi <i class="fa fa-angle-double-down"></i></span>

			<a href='{{ url("index/injection_schedule") }}' class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Injection Schedule</a>

			<a href="{{ url('/index/injection/machine_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Machine Monitoring</a>

			<!-- <a href="{{ url('/index/injection/stock_monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Stock Monitoring</a> -->

			<a href="{{ url('/index/injection/stock_monitoring/daily') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Stock Monitoring</a>

			<!-- <a href="{{ url('/index/injection/stock_monitoring/monthly') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Stock Monitoring By Month</a> -->

			<a href="{{ url('/index/injection/ng_rate?tanggal=') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Quality Monitoring Inj. Emp.</a>

			<a href='{{ url("index/injection/visual/monitoring") }}' class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Visual Check Monitoring</a>

			<a href="{{ url('index/maintenance/display/jishu_hozen?area=AINJR01') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Jishu Hozen Monitoring</a>

			<a href="{{ url('index/injection/cleaning/monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Cleaning Monitoring</a>

			<br>
			<span style="font-size: 1.3vw;"><i class="fa fa-angle-double-down"></i> Display Molding <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url('index/molding_monitoring/pasang') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Molding Terpasang</a>

			<a href="{{ url('index/molding_monitoring/lepas') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Molding Ready & Periodik</a>

			<!-- <a href="{{ url('index/molding_schedule') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Molding Schedule</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.3vw;"><i class="fa fa-angle-double-down"></i> Report Injeksi <i class="fa fa-angle-double-down"></i></span>
			 <!-- <a href="{{ url("index/dailyStock") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Daily Stock After Injection</a>

			 <a href="{{ url("index/MonhtlyStock") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Monhtly Target Injection</a> -->

			 <!-- <a href="{{ url("index/dailyNG") }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Daily NG Report Injection</a> -->

			 <!-- <a href="{{ url('index/injection/inventories/rc11') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Injection Inventories</a> -->
			 <a href="{{ url('index/injection/transactions') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Transaction History</a>
			 <a href="{{ url('index/injection/report_visual') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Visual Check</a>
			 <a href="{{ url('index/injection/report_cleaning') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Injection Cleaning</a>

			 <br>
			 <span style="font-size: 1.3vw;"><i class="fa fa-angle-double-down"></i> Report Molding <i class="fa fa-angle-double-down"></i></span>
			 <a href="{{ url('/index/recorder/report_push_block','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Push Block Check</a> 
			 <a href="{{ url('/index/recorder/resume_push_block','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Resume Push Block Check</a>
			 <a href="{{ url('index/recorder/report_torque_check','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Torque Check</a>
			 <!-- <a href="{{ url('index/recorder/push_block_check_monitoring','First Shot Approval') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Recorder Process Monitoring</a> -->
			 <a href="{{ url('index/injection/report_setup_molding') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Setup Molding History</a>
			 <a href="{{ url('index/injection/report_maintenance_molding') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Maintenance Molding</a>
		</div>
	</div>

	<div class="modal fade" id="push-pull-check-modal">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<span style="font-weight: bold; font-size: 18px;">Jumlah Hako</span>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<input id="remark" style="font-size: 20px; height: 30px; text-align: center;" type="hidden" class="form-control" value="After Injection">
											<select name="jumlah_hako" style="width: 100%; height: 40px; font-size: 17px; text-align: center;" id="jumlah_hako" class="form-control" required="required">
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="modal-footer">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
								<button value="CONFIRM" onclick="confirm()" class="btn btn-success pull-right">CONFIRM</button>
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

	function confirm() {
		var remark = $('#remark').val();
		var jumlah_hako = $('#jumlah_hako').val();
		var url = '{{ url("index/recorder_process_push_block","First Shot Approval") }}';
		for (var i = 1; i <= jumlah_hako; i++) {
			window.open(url, "_blank");
		}
	}
</script>
@endsection