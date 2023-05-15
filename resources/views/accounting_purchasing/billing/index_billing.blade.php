	@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
<!-- 	<h1>
		List Patrol<small class="text-purple"></small>
	</h1> -->
</section>
@stop
@section('content')
<section class="content" style="padding-top:0;">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">E-Billing<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/supplier') }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: blue;border-radius: 20px;">Vendor Data</a>
			<a href="{{ url('index/bank') }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: blue;border-radius: 20px;">Bank List</a>
			<a href="{{ url('index/gl_account') }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: blue;border-radius: 20px;">GL Account</a>
			<a href="{{ url('index/cost_center') }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: blue;border-radius: 20px;">Cost Center</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('billing/receive_material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Receive Material (YMES)</a>
			<a href="{{ url('billing/receive_non_material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Receive Non Material (YMES)</a>
			<a href="{{ url('billing/index/list_bank') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Maintain List Bank</a>
			<a href="{{ url('billing/upload_jurnal') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Upload Jurnal</a>
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('billing/tanda_terima/material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Tanda Terima Material</a>
			<a href="{{ url('billing/tanda_terima/non_material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Tanda Terima Equipment</a>
			<a href="{{ url('billing/payment_request/all') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Payment Request</a>
			<a href="{{ url('check/payment_request') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Check Payment Request</a>
			<a href="{{ url('billing/list_payment') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Create List Payment</a>
			<a href="{{ url('billing/jurnal') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Jurnal</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/tanda_terima/monitoring/material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;">Monitoring Invoice Material</a>
			<a href="{{ url('index/tanda_terima/monitoring/non_material') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;">Monitoring Invoice Equipment</a>
			<a href="{{ url('index/payment_request/monitoring') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;">Monitoring Payment Request</a>
		</div>
		
		<div class="col-xs-12" style="text-align: center;">
		<hr style="border: 1px solid red;">
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> General <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('invoice/tanda_terima') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Tanda Terima</a>
			<a href="{{ url('billing/payment_request/general') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Payment Request</a>
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