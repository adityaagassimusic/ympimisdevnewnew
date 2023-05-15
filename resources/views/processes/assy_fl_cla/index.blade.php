@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Clarinet Subassy-Assembly WIP<span class="text-purple"> CL仮組・組立の仕掛品</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/assembly/clarinet_stamp") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Stamp <b><i>IoT</i></b> New</a>
			<a href="{{ url("index/assembly/clarinet_registration") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Registration</a>
			<a href="{{ url("index/assembly/clarinet_print_label") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Print Packing Label</a>
			<a href="{{ url("index/assembly/clarinet_print_label_outer") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Print Packing Label Outer</a>
			<a href="{{ url('/index/assembly/return/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Return</a>
			<a href="{{ url('/index/assembly/operator/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Master Operator</a>
			<!-- {{-- <span style="font-size: 2vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_stamp_cl_1") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Stamp <b><i>IoT</i></b></a> --}}
			{{-- <a href="{{ url("index/process_assy_fl_0") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Stamp-Kariawase</a>
			<a href="{{ url("index/process_assy_fl_2") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Tanpoawase</a>
			<a href="{{ url("index/process_assy_fl_3") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Seasoning-Kanggou</a>
			<a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Chousei</a> --}} -->

			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Kensa <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/cl/kensa','kensa-process') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Kensa Process</a>
			<a href="{{ url('index/cl/kensa','qa-kensa') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Kensa QA</a>
			<a href="{{ url('index/cl/kensa','qa-audit') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px">Kensa QA Audit</a>

			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> YCL4XX <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('/index/seasoning_in/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px"><span class="text-green" style="font-weight: bold;font-style: italic;">IN</span> Seasoning</a>
			<a href="{{ url('/index/seasoning_progress/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px"><span class="text-blue" style="font-weight: bold;font-style: italic;">IN-Progress</span> Seasoning</a>
			<a href="{{ url('/index/seasoning/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;border-radius: 50px"><span class="text-red" style="font-weight: bold;font-style: italic;">OUT</span> Seasoning</a>
			<a href="{{ url('/index/assembly/seasoning/report/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;"><span class="text-yellow" style="font-weight: bold;font-style: italic;">Report</span> Seasoning</a>
			<!-- <br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> YCL4XX <i class="fa fa-angle-double-down"></i></span> -->
			<!-- <a href="{{ url('index/seasoning','042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">YCL4XX Prcoess</a> -->
			<!-- <a href="{{ url('index/cl/kensa','repair-process') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Repair Process</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/repairCl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Return/Repair</a> 
			<!-- <a href="{{ url('/index/board/clarinet','registration-process,kariawase-upper,kariawase-lower') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Registration & Kariawase Board</a>
			<a href="{{ url('/index/board/clarinet','tanpoawase-upper,tanpoawase-lower') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Tanpoawase Board</a>
			<a href="{{ url('/index/board/clarinet','penyambungan-process,kensa-process,qa-kensa') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Kensa Process & QA Kensa</a> -->
			<a href="{{ url('/index/board/clarinet','1') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Line 1</a>
			<a href="{{ url('/index/board/clarinet','2') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Line 2</a>
			<a href="{{ url('/index/board/clarinet','3') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Line 3</a>
			<a href="{{ url("index/assembly/clarinet/ng_rate") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">NG Rate</a>
			<a href="{{ url('index/assembly/eff/clarinet') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Operator Efficiency</a>
			<a href="{{ url('index/assembly/resume_ng?origin_group_code=042&ng_name=Kizu&date_from=&date_to=') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Resume NG</a>
			<!-- <a href="{{ url("index/assembly/clarinet/op_ng?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">NG Rate by Operator</a> -->
			<!-- <a href="{{ url('index/assembly/overall_eff/clarinet') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Overall Efficiency</a> -->
			<!-- <a href="{{ url('index/assembly/group_balance/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Group Work Balance</a> -->
			<a href="{{ url('index/assembly/resume?location=&date_from=&date_to=&origin_group=042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Production Resume</a>
			<a href="{{ url('index/assembly/ongoing/042/kariawase') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Ongoing Assembly Kariawase</a>
			<a href="{{ url('index/assembly/ongoing/042/tanpoawase') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Ongoing Assembly Tanpoawase</a>
			<a href="{{ url('index/assembly/ongoing/042/cl450') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Ongoing Assembly CL450</a>
			<?php if (str_contains($role,'QA') || str_contains($role,'MIS')): ?>
				<a href="{{ url('index/assembly/pareto/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">QA Pareto</a>
			<?php endif ?>
			<a href="{{ url('index/assembly/productivity/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;border-radius: 50px;">Operator Productivity</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			{{-- <a href="{{ url("/stamp/log") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Log Process</a>
			<a href="{{ url("/stamp/resumes_cl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Production Result</a>
			<a href="{{ url("/index/displayWipFl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Chart Inventory</a> --}}
			<!-- <a href="{{ url("/stamp/resumes_cl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Production Result</a> -->
			<a href="{{ url('/index/assembly/stamp_record/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Stamp Record</a>
			<a href="{{ url('/index/assembly/ng_report/production/042') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Production NG Report</a>
			<a href="{{ url('/index/assembly/ng_report/qa/042') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA NG Report</a>
			<a href="{{ url('/index/assembly/cl/serial_number_report','qa-kensa') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">QA Kensa Serial Number Report</a>
			<a href="{{ url('/index/assembly/report_qa_audit/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report QA Audit</a>
			<a href="{{ url('/index/assembly/status_material/042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report Status Material</a>
			<!-- <a href="{{ url('/index/assembly/resume_qa','042') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;">Pass Ratio</a> -->
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