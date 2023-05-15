@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Flute Subassy-Assembly WIP<span class="text-purple"> FL仮組・組立の仕掛品</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Stock Taking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/stocktaking/silver", "fl_assembly") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Silver</i></b></a>
			<a href="{{ url("index/stocktaking/daily", "fl_assembly") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Body</i></b></a>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_assy_fl_1") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Stamp <b><i>IoT</i></b></a> -->
			<a href="{{ url("index/assembly/flute_stamp") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Stamp <b><i>IoT</i></b> New</a>
			<a href='{{ url("index/assembly/flute/print_label") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Print Label Packing</b></a>
			<a href='{{ url("index/assembly/flute/kd_cleaning") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">KD Card Cleaning</b></a>
			<a href='{{ url("index/assembly/flute/card_cleaning") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Card Cleaning</b></a>
			<a href="{{ url('/index/assembly/return/041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Return</a>
			<!-- <a href="{{ url("index/process_assy_fl_0") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Stamp-Kariawase</a>
			<a href="{{ url("index/process_assy_fl_2") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Tanpoawase</a>
			<a href="{{ url("index/process_assy_fl_3") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Seasoning-Kanggou</a>
			<a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Chousei</a>
			<a href="{{ url("index/label_fl") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Assy FL - Print Label</a> -->
			<a href="{{ url("index/fetchResultFlnew") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Picking Schedule</a>
			<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Assemblies <i class="fa fa-angle-double-down"></i></span>
			<a href='{{ url("index/kensa","kariawase-fungsi") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Kariawase Kensa Fungsi</b></a>
			<a href='{{ url("index/kensa","kariawase-visual") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Kariawase Kensa Visual</b></a>
			<a href="{{ url('/index/kensa','perakitanawal-kensa') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Perakitan Ulang</a>
			<a href='{{ url("index/kensa","tanpoawase-kensa") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Tanpo Awase Kensa</b></a>
			<a href='{{ url("index/kensa","tanpoawase-fungsi") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Tanpo Awase Kensa Fungsi</b></a>
			<a href='{{ url("index/kensa","seasoning-process") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Seasoning Process</b></a>
			<a href='{{ url("index/kensa","kango-fungsi") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Kango Kensa Fungsi</b></a>
			<a href='{{ url("index/kensa","kango-kensa") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Kango Kensa</b></a>
			<a href='{{ url("index/kensa","renraku-fungsi") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Renraku Kensa Fungsi</b></a>
			<a href='{{ url("index/kensa","qa-fungsi") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">QA Kensa Fungsi</b></a>
			<a href='{{ url("index/kensa","fukiage1-visual") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Fukiage 1 Kensa Visual</b></a>
			<a href='{{ url("index/kensa","qa-visual1") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">QA 1 Kensa Visual</b></a>
			<a href='{{ url("index/kensa","qa-visual2") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">QA 2 Kensa Visual</b></a>
			<a href='{{ url("index/kensa","qa-kensasp") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">QA Kensa SP</b></a>
			<a href='{{ url("index/kensa","qa-audit") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">QA Audit</b></a>
			<a href='{{ url("index/kensa","repair-process") }}' class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: green;">Repair Process</b></a>
			<!-- <a href="{{ url("index/middle/buffing_target/assy_fl") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: green;">Assembly Flute Target</a> -->
		</div>

		<div class="col-xs-4" style="text-align: center; color: red;">
			<!-- <span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Return Menu <i class="fa fa-angle-double-down"></i></span> -->
			<!-- <a href="{{ url('/index/repairFl') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">Return</a> -->
			<!-- <a href="{{ url("index/ngFL") }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: red;">Ng</a> -->

			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url('/index/board','perakitan-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Perakitan Board</a>

			<a href="{{ url('/index/board','kariawase-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Kariawase Board</a>

			<a href="{{ url('/index/board','kariawase-fungsi,kariawase-visual,kariawase-repair,tanpoire-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Tanpoire Board</a>

			<a href="{{ url('/index/board','perakitanawal-kensa,tanpoawase-process') }}" class="btn btn-default btn-block" style="font-size: 1.5vw;border-radius: 50px; border-color: red;">Perakitan Ulang & Tanpo Awase Board</a>

			<a href="{{ url('/index/board','tanpoawase-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Tanpo Awase 2 Board</a>
			
			<a href="{{ url('/index/board','tanpoawase-kensa,tanpoawase-fungsi,repair-process-1,repair-process-2') }}" class="btn btn-default btn-block" style="font-size: 1.5vw;border-radius: 50px; border-color: red;">Tanpoawase Kensa & Fungsi Board</a>
			
			<a href="{{ url('/index/board','seasoning-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Seasoning Board</a>
			
			<a href="{{ url('/index/board','kango-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Kango Board</a>
			
			<a href="{{ url('/index/board','kango-fungsi,renraku-process') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Renraku Board</a>
			
			<a href="{{ url('/index/board','kango-kensa,renraku-fungsi') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Cek Fungsi Akhir Board</a>
			
			<a href="{{ url('/index/board','renraku-repair,qa-fungsi') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Cek Fungsi QA Board</a>
			
			<a href="{{ url('/index/board','fukiage1-process,repair-ringan') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Fukiage 1 Board</a>
			
			<a href="{{ url('/index/board','fukiage1-visual,qa-visual1,fukiage2-process,qa-visual2,pakcing') }}" class="btn btn-default btn-block" style="font-size: 1.5vw;border-radius: 50px; border-color: red;">QA Visual, Fukiage 2, & Packing Board</a>
			
			<a href="{{ url('/index/assembly/request/display','041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">Assembly WIP</a>
			
			<a href="{{ url("index/assembly/ng_rate") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">NG Rate</a>
			
			<!-- <a href="{{ url("index/assembly/op_ng?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">NG Rate By Operator</a> -->
			<!-- <a href="{{ url('index/assembly/ng_rate_key') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">NG By Key</a> -->

			<?php if (str_contains($role,'QA') || str_contains($role,'MIS')): ?>
				<a href="{{ url('index/assembly/pareto/041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: red;">QA Pareto</a>
			<?php endif ?>
			
		</div>

		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 20px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url('/stamp/log') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Log Process</a> -->
			<!-- <a href="{{ url('/stamp/resumes') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Production Result</a> -->
			<a href="{{ url('/index/displayWipFl') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Chart Inventory</a>
			<a href="{{ url('/index/assembly/production_result') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Production Result</a>
			<a href="{{ url('/index/assembly/stamp_record/041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Stamp Record</a>
			<a href="{{ url('/index/assembly/ng_report/production/041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">Production NG Report</a>
			<a href="{{ url('/index/assembly/ng_report/qa/041') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA NG Report</a>
			<!-- <a href="{{ url('/index/assembly/serial_number_report','qa') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA Serial Number Report</a> -->
			<a href="{{ url('/index/assembly/serial_number_report','qa-fungsi') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA Fungsi Serial Number Report</a>
			<a href="{{ url('/index/assembly/serial_number_report','qa-visual1') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA Visual 1 Serial Number Report</a>
			<a href="{{ url('/index/assembly/serial_number_report','qa-visual2') }}" class="btn btn-default btn-block" style="font-size: 17px;border-radius: 50px; border-color: purple;">QA Visual 2 Serial Number Report</a>
			<a href="{{ url('/index/assembly/report_qa_audit/041') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;border-radius: 50px;">Report QA Audit</a>
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