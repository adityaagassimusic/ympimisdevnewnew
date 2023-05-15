@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Saxophone Subassy-Assembly WIP<span class="text-purple"> サックス仮組　～　仕掛品組み立て</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/assembly/saxophone_registration") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Registration</a>
			<a href="{{ url("index/assembly/saxophone_print_label") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Print Packing Label</a>
			<!-- <a href="{{ url("/index/repairSx") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Return</a> -->
			<a href="{{ url('/index/assembly/return/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Return</a>
			<a href="{{ url('/index/assembly/operator/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Master Operator</a>
			<a href="{{ url("index/process_check_transaction") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Check Transaction</a>

			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Kensa <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/sax/kensa','kensa-process') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Kensa Process</a>
			<a href="{{ url('index/sax/kensa','repair-process') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Repair Process</a>

			<a href="{{ url('index/sax/kensa','qa-kensa') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Kensa QA</a>

			<a href="{{ url('index/sax/kensa','qa-fungsi') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">QA Fungsi</a>

			<a href="{{ url('index/sax/kensa','qa-visual') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">QA Visual</a>
			<a href="{{ url('index/sax/kensa','qa-audit') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">QA Audit</a>

			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Stock Taking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/stocktaking/daily", "sx_assembly") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Body</a>
			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Incoming Check <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_assembly_kensa", "subassy-incoming-sx") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Incoming Check Key</a>
			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Screwdriver <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/lifetime/screwdriver/assysax') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Lifetime Screwdriver</a>
			<!-- <a href="{{ url("index/ngSx") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Ng</a> -->
			<br>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process Old<i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Stamp <b><i>IoT</i></b></a> -->
			<a href="{{ url("index/process_stamp_sx_2") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Assy - Print</a>
			<a href="{{ url("index/process_stamp_sx_check") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Assy - Print Check Sheet</a>
			<a href="{{ url("index/process_stamp_sx_3") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Assy - Print Label</a>
			<a href="{{ url("index/fetchResultSaxnew") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Picking Schedule</a>
			<!-- <a href="{{ url("index/packing_documentation") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: green;">Packing Documentation</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url("index/middle/ic_atokotei_subassy?loc=lacquering&date=&key=") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">I.C Subassy LCQ Product</a>

			<a href="{{ url("index/middle/ic_atokotei_subassy?loc=plating&date=&key=") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">I.C Subassy PLT Product</a>

			<a href="{{ url('/index/board/saxophone','preparation-process') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Preparation Board</a>

			<a href="{{ url('/index/board/saxophone','1') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line 1</a>
			<a href="{{ url('/index/board/saxophone','2') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line 2</a>
			<a href="{{ url('/index/board/saxophone','3') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line 3</a>
			<a href="{{ url('/index/board/saxophone','4') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line 4</a>
			<a href="{{ url('/index/board/saxophone','5') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line 5</a>

			<a href="{{ url("index/assembly/saxophone/ng_rate") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">NG Rate</a>

			<!-- <a href="{{ url("index/assembly/saxophone/op_ng?tanggal=&location=") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">NG Rate by Operator</a> -->

			<a href="{{ url('index/assembly/eff/saxophone') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Operator Efficiency</a>

			<!-- <a href="{{ url('index/assembly/overall_eff/saxophone') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Overall Efficiency</a> -->

			<!-- <a href="{{ url('index/assembly/group_balance/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Group Work Balance</a> -->

			<a href="{{ url('index/assembly/line_balance/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Line Balance</a>

			<a href="{{ url('index/assembly/resume?location=&date_from=&date_to=&origin_group=043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Production Resume</a>
			<?php if (str_contains($role,'QA') || str_contains($role,'MIS')): ?>
			<a href="{{ url('index/assembly/ng_trend/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">NG Trend</a>
			<?php endif ?>
			<a href="{{ url('index/assembly/ongoing/043/1') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Ongoing Assembly Line 1</a>
			<a href="{{ url('index/assembly/ongoing/043/2') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Ongoing Assembly Line 2</a>
			<a href="{{ url('index/assembly/ongoing/043/3') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Ongoing Assembly Line 3</a>
			<a href="{{ url('index/assembly/ongoing/043/4') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Ongoing Assembly Line 4</a>
			<a href="{{ url('index/assembly/ongoing/043/5') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">Ongoing Assembly Line 5</a>
			<?php if (str_contains($role,'QA') || str_contains($role,'MIS')): ?>
				<a href="{{ url('index/assembly/pareto/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: red;">QA Pareto</a>
			<?php endif ?>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("/stamp/log") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Log Process</a> -->
			<a href="{{ url("stamp/resumes_sx") }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">Production Result</a>
			<a href="{{ url('/index/assembly/ng_report/production/043') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Production NG Report</a>
			<a href="{{ url('/index/assembly/ng_report/qa/043') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA NG Report</a>
			<!-- <a href="{{ url("/index/displayWipFl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Chart Inventory</a>  -->
			<a href="{{ url('/index/assembly/stamp_record/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">Stamp Record</a>

			<a href="{{ url('/index/assembly/sax/serial_number_report','qa-fungsi') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">QA Fungsi Serial Number Report</a>
			<a href="{{ url('/index/assembly/sax/serial_number_report','qa-visual') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">QA Visual Serial Number Report</a>
			<a href="{{ url('/index/assembly/sax/serial_number_report','qa-kensa') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">QA Tenor Serial Number Report</a>
			<a href="{{ url('/index/assembly/report_qa_audit/043') }}" class="btn btn-default btn-block" style="border-radius: 50px;font-size: 17px; border-color: purple;">Report QA Audit</a>
			<a href="{{ url('/index/assembly/status_material/043') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report Status Material</a>
			<a href="{{ url('/index/assembly/report_spec_product_process') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report Spec Product Process</a>
			<a href="{{ url('/index/assembly/report_spec_product') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report Spec Product QA</a>

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