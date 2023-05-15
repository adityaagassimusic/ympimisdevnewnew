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
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw; color: green;"><i class="fa fa-angle-double-down"></i> Input <i class="fa fa-angle-double-down"></i></span>
			
			<a href="{{ url('index/qa/incoming_check','wi1') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Wind Instrument 1 (Raw Material)</a>
			<a href="{{ url('index/qa/incoming_check','wi2') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Wind Instrument 2 (After Process)</a>
			<a href="{{ url('index/qa/incoming_check','ei') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Edicational Instrument (PN,VN,RC)</a>
			<a href="{{ url('index/qa/incoming_check','sx') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Saxophone Body</a>
			<a href="{{ url('index/qa/incoming_check','cs') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Case</a>
			<a href="{{ url('index/qa/incoming_check','ps') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. Pipe Silver (FL Body)</a>
			<a href="{{ url('index/qa/incoming_check','4xx') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> I.C. YCL4XX</a>
			<a href="{{ url('index/qa/kensa_check','KPP') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> Kensa KPP</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Display I.C. QA <i class="fa fa-angle-double-down"></i></span>

			<a href='{{ url("index/qa/display/incoming/lot_status") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">QA Incoming Check Lot Out Monitoring</a>
			<a href='{{ url("index/qa/display/incoming/material_defect") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">QA Incoming Check Material Defect</a>
			<a href='{{ url("index/qa/display/incoming/ng_rate") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">Daily NG Rate Incoming Check QA</a>
			<a href='{{ url("index/qa/display/incoming/ympi") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">YMPI Incoming Monitoring</a>
			<a href='{{ url("index/qa/display/incoming/vendor") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">Vendor Final Inspection Monitoring</a>
			<a href='{{ url("index/qa/display/incoming/qa_meeting") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">QA Meeting</a>

			<br>
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Display Vendor Final Inspection <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href='{{ url("index/qa/display/incoming/ng_rate_vendor/true") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">NG Rate PT. TRUE INDONESIA</a> -->

			<!-- <a href='{{ url("index/qa/display/incoming/ng_rate_vendor/arisa") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">NG Rate PT. ARISAMANDIRI PRATAMA</a> -->

			<a href='{{ url("index/qa/display/incoming/vendor/lot_out") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">Vendor Lot Out Monitoring</a>

			<!-- <a href='{{ url("index/qa/display/incoming/ng_rate_vendor/kbi") }}' class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: red;">NG Rate PT. KBI</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Report I.C. QA <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/report/incoming') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Incoming Check QA Report</a>
			<a href="{{ url('index/qa/report/kensa') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Kensa KPP Report</a>
			<a href="{{ url('index/qa/report/incoming/lot_out') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">Lot Out Report</a>

			<br>
			<span style="font-size: 1.6vw;"><i class="fa fa-angle-double-down"></i> Report Vendor Final Inspection <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/report/outgoing/true') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">PT. TRUE INDONESIA</a>
			<a href="{{ url('index/qa/report/outgoing/kbi') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">PT. KBI</a>
			<a href="{{ url('index/qa/report/outgoing/arisa') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: purple;">PT. ARISAMANDIRI PRATAMA</a>
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