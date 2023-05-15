@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }} <small><span class="text-purple">リコーダー組立工程</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.6vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Stamp <b><i>IoT</i></b></a> -->
			<!-- <a href="{{ url("index/recorder_process_push_block","After Injection") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Recorder Push Block Check</a> -->
			<button type="button" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;" data-toggle="modal" data-target="#push-pull-check-modal">
				Recorder Push Block Check
			</button>
			<a href="{{ url('index/recorder_process_torque_ai','After Injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Recorder Torque Check</a>
			<a href="{{ url('index/recorder/return') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Return Material</a>
			<a href="{{ url('index/recorder/cdm') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">CDM Recorder</a>
			<a href="{{ url('index/recorder/kensa/initial') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Inisialisasi Kensa Kakuning</a>
			<a href="{{ url('index/recorder/kensa') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Kensa Kakuning</a>
			<!-- <a href="{{ url("index/recorder_push_pull_check") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Recorder Assy Check</a> -->
			<a href="{{ url('index/recorder/clean_kanban') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Clean Kanban</a>
			<a href="{{ url('index/injection/traceability') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">Recorder Traceability</a>
			<a href="{{ url('index/recorder/qa_audit') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: green;">QA Audit</a>

			<!-- <br>
			<span style="font-size: 1.6vw; color: magenta;"><i class="fa fa-angle-double-down"></i> QA <i class="fa fa-angle-double-down"></i></span> -->
			<!-- <a href="{{ url("/index/qa/audit_fg/point_check") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Point Check Audit FG / KD QA</a>
			<a href="{{ url("/index/qa/audit_fg/audit") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Audit FG / KD QA</a>
			<a href="{{ url("/index/qa/audit_fg/report/recorder") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Report FG / KD QA</a>
			<a href="{{ url("/index/qa/packing") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: magenta;">Monitoring FG / KD QA</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/recorder/push_block_check_monitoring','After Injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Recorder Process Monitoring</a>
			<a href="{{ url("index/recorder/rc_picking_result") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Recorder Picking Result</a>
			<a href="{{ url('index/recorder/display/kensa/1') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;background-color: #d7ff91">Display Kensa Kakuning Line 1</a>
			<a href="{{ url('index/recorder/display/kensa/2') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;background-color: #ffbdbd">Display Kensa Kakuning Line 2</a>
			<a href="{{ url('index/recorder/display/kensa/3') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;background-color: #bde2ff">Display Kensa Kakuning Line 3</a>
			<a href="{{ url('index/recorder/display/ng_kensa?tanggal=') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Kensa Kakuning</a>
			<a href="{{ url('index/recorder/display/ng_trend') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Trend</a>
			<a href="{{ url('index/recorder/display/ng_rate') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Rate</a>
			<a href="{{ url('index/recorder/display/qa_audit') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">QA Audit Monitoring</a>
			<a href="{{ url('index/recorder/display/traceability') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Recorder Traceability</a>

			<a href="{{ url('index/recorder/display/parameter') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Big Data Injection Parameter</a>
			<a href="{{ url('index/recorder/display/parameter/ng') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">NG Parameter</a>
			<a href="{{ url('index/recorder/display/ng') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Record NG</a>
			<!-- <a href="{{ url('index/recorder/display/ng/mesin') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: red;">Record NG By Mesin</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('/index/recorder/report_push_block','After Injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Push Block Check</a>
			<a href="{{ url('/index/recorder/resume_push_block','After Injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Resume Push Block Check</a>
			<a href="{{ url('index/recorder/report_torque_check','After Injection') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Torque Check</a>
			<a href="{{ url('index/recorder/cdm_report') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report CDM</a>
			<a href="{{ url('index/recorder/kensa_report') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Report Kensa Kakuning</a>
			<a href="{{ url('index/injection/transactions') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Transaction History</a>
			<!-- <a href="{{ url('index/injection/inventories/rc91') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Injection Inventories</a> -->
			<a href="{{ url('index/recorder/ng_box') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Recorder NG Box</a>
			<a href="{{ url('index/recorder/ng_rate/data') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">NG Rate Data</a>
			<!-- <a href="{{ url("/index/recorder/resume_assy_rc") }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-radius:50px; border-color: purple;">Resume Assembly Recorder</a> -->
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
		$('#push-pull-check-modal').modal('hide');
		var remark = $('#remark').val();
		var jumlah_hako = $('#jumlah_hako').val();
		var url = '{{ url("index/recorder_process_push_block","After Injection") }}';
		for (var i = 1; i <= jumlah_hako; i++) {
			window.open(url, "_blank");
		}
	}
</script>
@endsection