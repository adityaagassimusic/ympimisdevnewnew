@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp}}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;color: green;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Form <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/registration_asset_form') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Register Asset</a>
			<a href="{{ url('index/fixed_asset/transfer_asset') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Transfer Asset</a>
			<a href="{{ url('index/fixed_asset/missing') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Missing Asset</a>
			<a href="{{ url('index/fixed_asset/disposal') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Disposal Asset</a>
			<a href="{{ url('index/fixed_asset/label_asset') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Label Request</a>
			<?php if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'ACC')) { ?>
				<a href="{{ url('index/fixed_asset/print_asset') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Label Print List</a>
				<a href="{{ url('index/fixed_asset/invoice_form') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Upload Invoice</a>
			<?php } ?>
			<a href="{{ url('index/fixed_asset/transfer_cip') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Transfer CIP</a>
			<a href="{{ url('index/fixed_asset/special_letter') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Special Letter Asset</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/monitoring') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;" >Fixed Asset Monitoring (Vendor)</a>

			<a href="{{ url('index/fixed_asset/monitoring_internal') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Fixed Asset Monitoring (YMPI)</a>

			<a href="{{ url('index/fixed_asset/monitoring_approval') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Monitoring Approval Fixed Asset</a>
			<!-- <a href="{{ url('index/fixed_asset/monitoring_all') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Resume Fixed Asset</a> -->

			<span style="font-size: 3vw; color: purple"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple; text-align: center">Report Fixed Asset</a>
		</div>
		<div class="col-xs-4" style="text-align: center;color: green;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/audit/list') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Fixed Asset Check List</a>
			<a href="{{ url('index/fixed_asset/auditor_audit/list') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Fixed Asset Audit List</a>
		</div>

		<!-- <div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Fixed Asset</a>
		</div> -->
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