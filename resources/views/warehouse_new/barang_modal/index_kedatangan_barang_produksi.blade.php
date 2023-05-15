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
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">Kontrol Barang Modal<span class="text-purple"></span></h3>
		</div>
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> IN <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/kedatangan/dokumen_bc') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Upload Dokumen BC</a>
			<a href="{{ url('index/fixed_asset/transfer_cip') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;border-radius: 20px;">Transfer CIP</a>
		</div>


		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Produksi <i class="fa fa-angle-double-down"></i></span>


			<a href="{{ url('produksi/cek_kedatangan/KP') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Key Part Process</a>
			<a href="{{ url('produksi/cek_kedatangan/BP') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Body Part Process</a>
			<a href="{{ url('produksi/cek_kedatangan/FI') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Final Assy</a>
			<a href="{{ url('produksi/cek_kedatangan/SO') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Welding Process</a>
			<a href="{{ url('produksi/cek_kedatangan/MI') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Surface Treatment</a>
			<a href="{{ url('produksi/cek_kedatangan/EDIN') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Educational Treatment</a>
			<a href="{{ url('produksi/cek_kedatangan/other') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;border-radius: 20px;">Others</a>
			<!-- <a href="{{ url('produksi/cek_kedatangan/IT') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">MIS</a>
			<a href="{{ url('produksi/cek_kedatangan/HR') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">HRGA</a>
			<a href="{{ url('produksi/cek_kedatangan/PE') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Engineering</a>
			<a href="{{ url('produksi/cek_kedatangan/PC') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Control</a>
			<a href="{{ url('produksi/cek_kedatangan/PR') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Purchasing</a>
			<a href="{{ url('produksi/cek_kedatangan/Log') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Logistic</a>
			<a href="{{ url('produksi/cek_kedatangan/QA') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Quality Assurance</a> -->
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> OUT <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/disposal') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;">Pengajuan Disposal FA</a>
			<a href="{{ url('index/non_fixed_asset/disposal') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;">Pengajuan Disposal Non FA</a>
			<a href="{{ url('index/non_fixed_asset/transfer') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;border-radius: 20px;"> Transfer Non Fixed Asset</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Logistic <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url('index/fixed_asset/disposal/scrap/proses') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: blue;border-radius: 20px;">Klasifikasi Scrap FA</a> -->
			<a href="{{ url('index/fixed_asset/disposal/scrap') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: blue;border-radius: 20px;">Disposal Fixed Asset Scrap</a>
			<a href="{{ url('monitoring/fixed_asset/disposal/scrap') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: blue;border-radius: 20px;">Pengeluaran Fixed Asset Scrap</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/barang_modal/monitoring') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;border-radius: 20px;">Kontrol Barang Modal</a>
		</div>

		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: orange;"><i class="fa fa-angle-double-down"></i> Other <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/fixed_asset/map') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: orange;border-radius: 20px;">Map Fixed Asset</a>
			<a href="{{ url('index/barang_modal/stock') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: orange;border-radius: 20px;">Stock Barang Modal</a>
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