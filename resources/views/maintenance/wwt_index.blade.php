@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
	input[type=checkbox]:checked {
		height: 30px;
		width: 30px;
	}
	input[type=checkbox] {
		height: 30px;
		width: 30px;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<!-- <div class="col-xs-1" style="padding-top: 0px" align="center">
		<i class="fa fa-shopping-cart fa-3x" id="Disposal" onclick="detailLoading(this.id)" style="color: black;"></i>
		<span class="label label-danger" id="countDisposal"></span>
	</div>
	<div class="col-xs-11" style="padding-top: 5px" align="right">
		@if($user->user_name == 'PI1210001' || $user->role_code == 'S-MIS' || $user->role_code == 'MIS')
		<button class="btn" style="margin-left: 5px; width: 20%; background-color: #3498db; color: white;" onclick="KonfirmasiDisposal();"><i class="fa fa-list"></i> Konfirmasi Disposal</button>
		<a href="{{ url('index/logs/wwt') }}" class="btn" style="margin-left: 5px; width: 20%; background-color: #3498db; color: white;"><i class="fa fa-list"></i> Log Limbah</a>
		@endif
		<a href="{{ url('index/maintenance/wwt/monitoring') }}" class="btn" style="margin-left: 5px; width: 20%; background-color: #3498db; color: white;"><i class="fa fa-list"></i> Monitoring Limbah</a>
	</div> -->
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<center style="padding-top: 350px;">
			<span style="font-size: 50px; color: white">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</center>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif

	
	<div class="col-md-12" align="center">
		<!-- <div class="col-md-2" align="center">
			<i class="fa fa-shopping-cart fa-3x" id="Disposal" onclick="detailLoading(this.id)" style="color: black; cursor: pointer"></i>
			<span class="label label-danger" id="countDisposal"></span>
		</div> -->
		<!-- <div class="col-md-12" align="center"> -->
			<a data-toggle="modal" data-target="#modalQr" class="btn-lg" style="cursor: pointer; color:white; background-color: #3498db; width: 200px; display: inline-block">
				&nbsp;<i class="glyphicon glyphicon-qrcode"></i>&nbsp;&nbsp;&nbsp;Scan Scanner&nbsp;
			</a>
			<a data-toggle="modal" data-target="#modalScan" class="btn-lg" style="cursor: pointer; margin-left: 5px; color:white; background-color: #56d183; width: 200px; display: inline-block">
				&nbsp;<i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan Camera&nbsp;
			</a>
			@if($user->user_name == 'PI1210001' || $user->role_code == 'S-MIS' || $user->role_code == 'MIS')
			<!-- <a onclick="KonfirmasiDisposal()" class="btn-lg" style="margin-left: 5px; color:white; background-color: #74A4BC; cursor: pointer; width: 200px; display: inline-block"> -->
			<a href="{{ url('index/logs/wwt') }}" class="btn-lg" style="margin-left: 5px; color:white; background-color: #74A4BC; width: 200px; display: inline-block">
				&nbsp;<i class="fa fa-history"></i>&nbsp;&nbsp;&nbsp;Log Limbah&nbsp;
			</a>

			<a href="{{ url('index/chemical/wwt') }}" class="btn-lg" style="margin-left: 5px; color:white; background-color: #74A4BC; width: 200px; display: inline-block">
				&nbsp;<i class="fa fa-desktop"></i>&nbsp;&nbsp;&nbsp;Monitoring 2&nbsp;
			</a>
			@endif

			<a href="{{ url('index/confirmation/limbah') }}" class="btn-lg" style="margin-left: 5px; color:white; background-color: #74A4BC; cursor: pointer; width: 200px; display: inline-block">
				&nbsp;<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;&nbsp;Konfirmasi&nbsp;
			</a>

			<a href="{{ url('index/maintenance/wwt/monitoring') }}" class="btn-lg" style="margin-left: 5px; color:white; background-color: #dfc764; width: 200px; display: inline-block">
				&nbsp;<i class="fa fa-desktop"></i>&nbsp;&nbsp;&nbsp;Monitoring&nbsp;
			</a>

			<!-- </div> -->
		</div>

	<!-- <div class="col-xs-12" style="padding-top: 20px">
		<div class="box" style="background-color: #ffffff">
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="input-group col-md-8 col-md-offset-2">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
								<i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
							</div>
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="text" style="text-align: center; font-size: 30px; height: 50px" class="form-control" id="slip_limbah" placeholder="Scan Slip Label Limbah" required>
							<div class="input-group-addon" id="icon-serial">
								<i class="glyphicon glyphicon-ok"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<!-- ================== -->

	<div class="col-md-12" style="padding-top: 20px">
		<div class="col-xs-2" style="margin-top: 10px" align="center">
			<i class="fa fa-shopping-cart fa-3x" id="Disposal" onclick="detailLoading(this.id)" style="color: black; cursor: pointer; font-size : 100px"></i>
			<span class="label label-danger" style="font-size: 20px" id="countDisposal"></span>
		</div>
		<div class="col-xs-2" style="margin-top: 10px">
			<table style="text-align:center;width:100%;height: 100%">
				<tr>
					<td colspan="2" style="background-color: #e4c46d;color: black;font-size: 1vw;font-weight: bold;">JUMBO BAG</td>
				</tr>
				<tr>
					<td width="50%" onclick="detailAll('JUMBO BAG', 'IN')">
						<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>IN</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="jb_in">0</span> 
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-download"></i>
							</div>
						</div>
					</td>
					<td width="50%" onclick="detailAll('JUMBO BAG', 'Disposal')">
						<div class="small-box" style="background: #fe0000; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>OUT</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="jb_out">0</span>
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-upload"></i>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-2" style="margin-top: 10px">
			<table style="text-align:center;width:100%;height: 100%">
				<tr>
					<td colspan="2" style="background-color: #e4c46d;color: black;font-size: 1vw;font-weight: bold;">PAIL</td>
				</tr>
				<tr>
					<td width="50%" onclick="detailAll('PAIL', 'IN')">
						<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>IN</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="pail_in">0</span> 
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-download"></i>
							</div>
						</div>
					</td>
					<td width="50%" onclick="detailAll('PAIL', 'Disposal')">
						<div class="small-box" style="background: #fe0000; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>OUT</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="pail_out">0</span>
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-upload"></i>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-2" style="margin-top: 10px">
			<table style="text-align:center;width:100%;height: 100%">
				<tr>
					<td colspan="2" style="background-color: #e4c46d;color: black;font-size: 1vw;font-weight: bold;">DRUM</td>
				</tr>
				<tr>
					<td width="50%" onclick="detailAll('DRUM', 'IN')">
						<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>IN</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="drum_in">0</span> 
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-download"></i>
							</div>
						</div>
					</td>
					<td width="50%" onclick="detailAll('DRUM', 'Disposal')">
						<div class="small-box" style="background: #fe0000; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>OUT</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="drum_out">0</span>
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-upload"></i>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-2" style="margin-top: 10px">
			<table style="text-align:center;width:100%;height: 100%">
				<tr>
					<td colspan="2" style="background-color: #e4c46d;color: black;font-size: 1vw;font-weight: bold;">KARTON</td>
				</tr>
				<tr>
					<td width="50%" onclick="detailAll('KARTON', 'IN')">
						<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>IN</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="karton_in">0</span> 
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-download"></i>
							</div>
						</div>
					</td>
					<td width="50%" onclick="detailAll('KARTON', 'Disposal')">
						<div class="small-box" style="background: #fe0000; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>OUT</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="karton_out">0</span>
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-upload"></i>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-2" style="margin-top: 10px">
			<table style="text-align:center;width:100%;height: 100%">
				<tr>
					<td colspan="2" style="background-color: #e4c46d;color: black;font-size: 1vw;font-weight: bold;">KANTONG PLASTIK</td>
				</tr>
				<tr>
					<td width="50%" onclick="detailAll('KANTONG PLASTIK', 'IN')">
						<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>IN</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="plastik_in">0</span> 
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-download"></i>
							</div>
						</div>
					</td>
					<td width="50%" onclick="detailAll('KANTONG PLASTIK', 'Disposal')">
						<div class="small-box" style="background: #fe0000; height: 100%;cursor: pointer;color:black">
							<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1vw; text-align: left;"><b>OUT</b></h3>
							<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="plastik_out">0</span>
							<div class="icon" style="padding-top: 10px;font-size:5vh;">
								<i class="fa fa-cloud-upload"></i>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- ======================== -->
	<div class="col-xs-12">
		@foreach($limbah as $lmb)
		<div class="col-xs-2" style="margin-top: 10px">
			<input type="hidden" name="pilihan" id="pilihan">
			<input type="hidden" name="limbah" id="limbah">
			<input type="hidden" name = "kemasan" id="kemasan">
			<button id="{{ $lmb->waste_category }}-{{ $lmb->unit_weight }}-{{ $lmb->remark }}" class="btn btn-sm pull-right" style="font-weight: bold; font-size: 15px; height: 50px; width: 100%; color: black; display: inline-block; background-color: #56d183;" onclick="input_new(this.id)">
				{{ $lmb->waste_category }}</button>
			</div>
			@endforeach
		</div>

		<div class="row">
			<div class="col-xs-12" style="margin-top: 10px">
				<center>
					<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 10px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Waste Water Treatment (WWT)</span>
					</div>
				</center>
				<table id="tableWWT" class="table table-bordered table-striped table-hover">
					<thead style="background-color: #BDD5EA; color: black;">
						<tr>
							<th style="width: 5%; text-align: center;">#</th>
							<th style="width: 15%; text-align: center;">Slip</th>
							<th style="width: 10%; text-align: center;">Limbah & Qty</th>
							<th style="width: 10%; text-align: center;">Kemasan</th>
							<th style="width: 20%; text-align: center;">Kategori</th>
							<th style="width: 20%; text-align: center;">PIC Penerima</th>
							<th style="width: 10%; text-align: center;">Tanggal Masuk</th>
							<th style="width: 10%; text-align: center;">#</th>
						</tr>
					</thead>
					<tbody id="tableWWTBody">
					</tbody>
				</table>
			</div>
		</div>

		<div class="modal fade" id="modalDetail" style="z-index: 10000;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs" style="text-align: center">
								<h2 id="detailJudul"></h2>
							</ul>
						</div>
						<input type="hidden" id="judul_kemasan">
						<input type="hidden" id="category_kemasan">
						<div class="nav-tabs-custom tab-danger" align="center">
							<div class="col-xs-12" style="padding-bottom: 10px" align="center">
								<select class="form-control select2" name="select_filter" id='select_filter' data-placeholder="Pilih Jenis Limbah" style="width: 50%;" onchange="SelectFilter(this.value)">
									<option value=""></option>
									@foreach($select_limbah as $lmb)
									<option value="{{ $lmb->waste_category }}">{{ $lmb->waste_category }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtable">
							<thead style="background-color: rgb(126,86,134)">
								<tr>
									<th style="width: 5%; text-align: center">No</th>
									<th style="width: 10%; text-align: center">Slip</th>
									<th style="width: 10%; text-align: center">Tanggal</th>
									<th style="width: 15%; text-align: center">Jenis Limbah</th>
									<th style="width: 5%; text-align: center">Kat.</th>
									<th style="width: 10%; text-align: center">Jumlah</th>
									<th style="width: 20%; text-align: center">Request Disposal</th>
									<!-- <th>Log Book</th> -->
									<th style="width: 20%; text-align: center">Status</th>
									<th style="width: 5%; text-align: center">#</th>
								</tr>
							</thead>
							<tbody id="detailbodytable">
							</tbody>
						</table>
						<input type="hidden" id="max_limbah">
						<center>
							<div id="tombol_masukkan_keranjang"></div>
						</center>
					</div>
				</div>
			</div>
		</div>

		<!-- <div class="modal fade" id="ModalScan" style="z-index: 10000;">  ///////////////////// baru di command
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<h2 id="detailJudul"></h2>
							</ul>
						</div>
						<div class="box-body" style="padding-bottom: 30px;">
							<div class="row">
								<div class="col-md-12">
									<div class="input-group col-md-8 col-md-offset-2">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
										</div>
										<input type="hidden" value="{{csrf_token()}}" name="_token" />
										<input type="text" style="text-align: center; font-size: 30px; height: 75px" class="form-control" id="slip_limbah" placeholder="Scan Scrap Slip Here..." required>
										<div class="input-group-addon" id="icon-serial">
											<i class="glyphicon glyphicon-ok"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		<div class="modal fade" id="modalResumeLoading" style="z-index: 10000;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<!-- <form method="GET" action="{{ url("get/limbah/keranjang") }}">
							<div class="col-xs-8" style="margin-bottom : 5px" id="modal_new_tim">
								<div class="col-sm-3">
									<div class="input-group">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" id="periode" name="periode" class="form-control datepicker" style="width: 100%; text-align: center;" placeholder="Bulan" required>
									</div>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="vendor" name="vendor" data-placeholder='Pilih Vendor' style="width: 100%" required>
										<option value="">&nbsp;</option>
										@foreach($vendor as $ven)
										<option value="{{$ven->short_name}}">{{$ven->vendor}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="jenis" name="jenis" data-placeholder='Pilih Kategori' style="width: 100%" required>
										<option value="">&nbsp;</option>
										@foreach($limbah as $row)
										<option value="{{$row->waste_category}}">{{$row->waste_category}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-1" style="padding-left: 0">
									<button type="submit" class="btn btn-danger pull-left"><i class="fa fa-print"></i> Cetak Laporan</button>
								</div>
							</div>
						</form> -->

						<!-- <div class="col-xs-12" style="padding-right: 0">
							<form method="GET" action="{{ url("kirim_email/disposal") }}">
								<button type="submit" class="btn btn-info pull-right"><i class="fa fa-envelope-open-o"></i> Kirim Email</button>
							</form>
						</div> -->
						<div class="col-xs-12" style="padding-right: 0">
							<div class="nav-tabs-custom tab-danger" align="center">
								<ul class="nav nav-tabs">
									<center>
										<h2 id="loadingjudul"></h2>
									</center>
								</ul>
							</div>
						</div>
						<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtableloading">
							<thead style="background-color: rgb(126,86,134)">
								<tr>
									<th>No</th>
									<th>Slip</th>
									<th>Tanggal Masuk</th>
									<th>Jenis Limbah</th>
									<th>Ketegori</th>
									<th>Jumlah</th>
									<th>Vendor</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody id="detailbodytableloading">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalKonfirmasiDisposal" style="z-index: 10000;">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<h2>Konfirmasi Disposal</h2>
							</ul>
						</div>
						<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="TableKonfirmasiDisposal">
							<thead style="background-color: rgb(126,86,134)">
								<tr>
									<th>No</th>
									<th>Slip Disposal</th>
									<!-- <th>Tanggal Masuk</th> -->
									<th>#</th>
								</tr>
							</thead>
							<tbody id="BodyTableKonfirmasiDisposal">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalFormInsert">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<center>
									<h2 id="judulForm"></h2>
								</center>
							</ul>
						</div>
						<div class="col-xs-12" style="padding-top: 10px">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Informasi Umum</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op"></td>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2"></td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">TANGGAL</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<center>
												<div class="input-group" style="margin-left: 20%">
													<div class="input-group-addon bg-purple" style="border: none;">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" id="tanggal_input" class="form-control datepicker" style="width: 70%; text-align: center;" placeholder="Pilih Tanggal Input" value="{{ date('Y-m-d') }}" required>
												</div>
											</center>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">PIC</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<select class="select3" data-placeholder="Pilih PIC" style="width: 70%" id="pic_input">
												<option value=""></option>
												@foreach($pics as $pic)
												<option value="{{ $pic->employee_id }}/{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">DARI LOKASI</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<select class="select3" data-placeholder="Pilih Lokasi" style="width: 70%" id="dari_lokasi">
												<option value=""></option>
												@foreach($location as $loc)
												<option value="{{ $loc }}">{{ $loc }}</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">SATUAN</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-weight: bold; font-size: 25px" id="satuan">
										</td>
									</tr>
								</tbody>
							</table>
							<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" id="btn_check" onclick="check()"><i class="fa fa-check"></i> SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalFormInsertQty">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="Close()">
							<span aria-hidden="true">&times;</span>
						</button>
						<!-- <div class="col-xs-12" style="padding-top: 10px" id="tombol_logbook">
							<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" onclick="InsertLogBook()"><i class="fa fa-book"></i> Input LogBook</button>
						</div> -->
						<div class="col-xs-12" style="padding-top: 10px" id="tombol_jumlah">
							<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" onclick="InsertJumlah()"><i class="fa fa-sign-in"></i> Input Jumlah</button>
						</div>
						<div class="col-xs-12" style="padding-top: 10px" id="tambah_logbook">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2" id="number_slip"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">TANGGAL</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<center>
												<div class="input-group" style="margin-left: 20%">
													<div class="input-group-addon bg-purple" style="border: none;">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" id="tanggal_input" class="form-control datepicker" style="width: 70%; text-align: center;" placeholder="Pilih Tanggal Input" value="{{ date('Y-m-d') }}" required>
												</div>
											</center>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">PIC</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<select class="select2" data-placeholder="Pilih PIC" style="width: 70%" id="pic_input_logbook">
												<option value=""></option>
												@foreach($pics as $pic)
												<option value="{{ $pic->employee_id }}/{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">DARI LOKASI</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<select class="select2" data-placeholder="Pilih Lokasi" style="width: 70%" id="dari_lokasi_logbook">
												<option value=""></option>
												@foreach($location as $loc)
												<option value="{{ $loc }}">{{ $loc }}</option>
												@endforeach
											</select>
										</td>
									</tr>
								</tbody>
							</table>
							<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" id="btn_check" onclick="logbook()"><i class="fa fa-check"></i> SIMPAN</button>
						</div>
						<div class="col-xs-12" style="padding-top: 10px" id="input_jumlah">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2" id="number_slip"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">JUMLAH</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
											<center>
												<div class="input-group" style="margin-left: 10%; margin-right: 10%">
													<div class="input-group-addon bg-purple" style="border: none;">
														<i class="fa fa-keyboard-o"></i>
													</div>
													<input type="text" class="form-control numpad" id="qty_input" placeholder="Input Jumlah" style="text-align: center; font-weight: bold; font-size: 22px" required>
													<div class="input-group-addon bg-purple" style="border: none;">
														<i class="fa fa-keyboard-o"></i>
													</div>
												</div>
											</center>
										</td>
									</tr>
								</tbody>
							</table>
							<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" id="btn_check" onclick="SimpanUpdate()"><i class="fa fa-check"></i> SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalQr">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<center><h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan Slip Limbah</h3></center>
					</div>
					<div class="modal-body" style="padding-bottom: 75px;">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
								</div>
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="text" style="text-align: center; font-size: 30px; height: 50px" class="form-control" id="slip_limbah" placeholder="Scan Disini" required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalScan">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<center><h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan Slip Limbah</h3></center>
					</div>
					<div class="modal-body">
						<div class="row">
							<div id='scanner' class="col-xs-12">
								<div class="col-xs-12">
									<center>
										<div id="loadingMessage">
											🎥 Unable to access video stream
											(please make sure you have a webcam enabled)
										</div>
										<video autoplay muted playsinline id="video"></video>
										<div id="output" hidden>
											<div id="outputMessage">No QR code detected.</div>
										</div>
									</center>
								</div>									
							</div>
							<div class="receiveReturn" style="width:100%; padding-left: 2%; padding-right: 2%;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


	</section>

	@endsection
	@section('scripts')
	<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/jquery.numpad.js") }}"></script>
	<script src="{{ url("js/jsQR.js") }}"></script>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
		$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
		$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
		$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
		$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
		$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

		var arr_item = [];
		var item_ctg = [];
		var machine_check_list = [];
		var arr_ids = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});
			$('.select2').select2();
			$('#month').datepicker({
				format: "yyyy-mm",
				startView: "months", 
				minViewMode: "months",
				autoclose: true
			});

			$('#tanggal_input').datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd',
				todayHighlight: true
			});

			$('.select2').select2({
				dropdownParent: $('#modalResumeLoading'),
				allowClear : true,
			});


			// $('.select3').select2({
			// 	dropdownParent: $('#modalFormInsert'),
			// 	allowClear : true,
			// });

			$('.select2').select2({
				dropdownParent: $('#modalResumeLoading'),
				allowClear : true,
			});

			jumlahDisposal();
			$("#div-in").hide();
			$("#div-out").hide();
			$("#form_input").hide();

			$('#periode').datepicker({
				utoclose: true,
				format: "yyyy-mm",
				startView: "months", 
				minViewMode: "months",
				autoclose: true,
			});
			ListData();
			$('.select2').select2({
				allowClear:true,
				dropdownParent: $('#modalDetail')
			});
		})

		var vdo;

		$("#modalScan").on('shown.bs.modal', function(){
			showCheck('123');
		});

		$('#modalScan').on('hidden.bs.modal', function () {
			videoOff();
		// $('.receiveReturn').html("");
		});

		function ListData(){
			$.get('{{ url("fetch/wwt/monitoring/detail") }}', function(result, status, xhr){
				if(result.status){

					$('#tableWWT').DataTable().clear();
					$('#tableWWT').DataTable().destroy();	
					$('#tableWWTBody').html();
					var index = 1;
					$.each(result.resume_limbah, function(key, value){
						var pic = value.pic.split('/');

						tableWWTBody += '<tr>';
						tableWWTBody += '<td style="text-align: center;">'+index+++'</td>';
						tableWWTBody += '<td style="text-align: center;">'+value.slip+'</td>';
						tableWWTBody += '<td style="text-align: center;">'+value.waste_category+'<br>('+value.quantity+')</td>';
						tableWWTBody += '<td style="text-align: center;">'+value.kemasan+'</td>';
						tableWWTBody += '<td style="text-align: center;">Dari Lokasi '+value.dari_lokasi+'<br>'+value.category+'</td>';
						tableWWTBody += '<td style="text-align: center;">'+pic[0]+'<br>('+pic[1]+')</td>';
						tableWWTBody += '<td style="text-align: center;">'+value.date_in+'</td>';
						tableWWTBody += '<td style="text-align: center;"><button type="button" class="btn btn-danger" onclick="HapusLimbah('+value.id+')"><i class="fa fa-trash"> Hapus</i></button></td>';
						tableWWTBody += '</tr>';
					});

					$('#tableWWTBody').append(tableWWTBody);

					$('#tableWWT').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
							[ 10, 20, 50, -1 ],
							[ '10 rows', '20 rows', '50 rows', 'Show all' ]
							],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							]
						},
						'paging': true,
						'lengthChange': true,
						'searching': true,
						'ordering': false,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

		function HapusLimbah(id){
			if(confirm("Apakah anda yakin akan menghapus slip limbah ini?")){
				var data = {
					id:id
				}
				$.post('{{ url("delete/limbah/wwt") }}', data, function(result, status, xhr) {
					if(result.status){
						openSuccessGritter('Success','Slip Berhasil Di Hapus!');
						location.reload(true);
					}else{
						openErrorGritter('Error!', result.message);
					}
				});
			}else{
				return false;
			}
		}


		function PrintChecklist(){
			$.get('<?php echo e(url("get/limbah/keranjang")); ?>', function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', 'Silahkan Cek Dokumen Anda');
				}
				else{
					openErrorGritter('Error!', 'Maaf, Dokumen Tidak Dapat Dicetak');
				}
			});
		}

		function showCheck(kode) {
			$(".modal-backdrop").add();
			$('#scanner').show();

			var vdo = document.getElementById("video");
			video = vdo;
			var tickDuration = 200;
			video.style.boxSizing = "border-box";
			video.style.position = "absolute";
			video.style.left = "0px";
			video.style.top = "0px";
			video.style.width = "400px";
			video.style.zIndex = 1000;

			var loadingMessage = document.getElementById("loadingMessage");
			var outputContainer = document.getElementById("output");
			var outputMessage = document.getElementById("outputMessage");

			navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
				video.srcObject = stream;
				video.play();
				setTimeout(function() {tick();},tickDuration);
			});

			function tick(){
				loadingMessage.innerText = "⌛ Loading video..."

				try{

					loadingMessage.hidden = true;
					video.style.position = "static";

					var canvasElement = document.createElement("canvas");            
					var canvas = canvasElement.getContext("2d");
					canvasElement.height = video.videoHeight;
					canvasElement.width = video.videoWidth;
					canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
					var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
					var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
					if (code) {
						outputMessage.hidden = true;
						$('#modalScan').modal('hide');
						videoOff();
						ScanBenar(code.data);

					}else{
						outputMessage.hidden = false;
					}
				} catch (t) {
				// console.log("PROBLEM: " + t);

				}
				setTimeout(function() {tick();},tickDuration);
			}
		}

		function videoOff() {
			video.pause();
			video.src = "";
			video.srcObject.getTracks()[0].stop();
		}

		$('#modalQr').on('shown.bs.modal', function () {
		// $('#slip_limbah').show();
		// $('#slip_limbah').val('');
			$("#slip_limbah").focus();

			$('#slip_limbah').keydown(function(event) {
				if (event.keyCode == 13 || event.keyCode == 9) {
					slip = $("#slip_limbah").val();

					var data = {
						slip:slip
					}

					$.get('<?php echo e(url("select/slip")); ?>', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', 'Slip Ditemukan');
							$("#slip_limbah").val('');
							$("#number_slip").html(slip);
						// $('#modalDetail').modal('hide');
							$('#modalQr').modal('hide');
							$('#modalFormInsertQty').modal('show');
							$("#tambah_logbook").hide();
							$("#input_jumlah").hide();
						}
						else{
							openErrorGritter('Error!', 'Slip Tidak Sesuai');
							$("#slip_limbah").val('');
						}
					});
				}
			});
		});

		function ScanBenar(slip){
			var data = {
				slip:slip
			}

			$.get('<?php echo e(url("select/slip")); ?>', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', 'Slip Ditemukan');
					$("#slip_limbah").val('');
					$("#number_slip").html(slip);
					$('#modalDetail').modal('hide');
					$('#modalFormInsertQty').modal('show');
					$("#tambah_logbook").hide();
					$("#input_jumlah").hide();
				}
				else{
					openErrorGritter('Error!', 'Slip Tidak Sesuai');
					$("#slip_limbah").val('');
				}
			});
		}

	// $('#slip_limbah').keydown(function(event) {
	// 		if (event.keyCode == 13 || event.keyCode == 9) {
	// 			slip = $("#slip_limbah").val();

	// 			var data = {
	// 				slip:slip
	// 			}

	// 			$.get('<?php echo e(url("select/slip")); ?>', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', 'Slip Ditemukan');
	// 					$("#slip_limbah").val('');
	// 					$("#number_slip").html(slip);
	// 					$('#modalDetail').modal('hide');
	// 					$('#modalFormInsertQty').modal('show');
	// 					$("#tambah_logbook").hide();
	// 					$("#input_jumlah").hide();
	// 				}
	// 				else{
	// 					openErrorGritter('Error!', 'Slip Tidak Sesuai');
	// 					$("#slip_limbah").val('');
	// 				}
	// 			});
	// 		}
	// 	});


		function InsertLogBook(){
			$('#modalFormInsertQty').modal('show');
			$("#tambah_logbook").show();
			$("#input_jumlah").hide();
			$("#tombol_logbook").hide();
			$("#tombol_jumlah").hide();
		}

		function InsertJumlah(){
			$('#modalFormInsertQty').modal('show');
			$("#input_jumlah").show();
			$("#tambah_logbook").hide();
			$("#tombol_logbook").hide();
			$("#tombol_jumlah").hide();
		}

		function Close(){
			location.reload(true);
		}

		function kirimEmail(){
			$.get('{{ url("kirim_email/disposal") }}', function(result, status, xhr) {
				openSuccessGritter('Success','Masuk List Disposal');
			})
		}

		function resumeAll(){
			location.reload(true);
		}

		function detailAll(value, id){
		// console.log(value, id);
			$('#modalDetail').modal('show');
			$('.select2').select2({
				dropdownParent: $('#modalDetail'),
				allowClear : true,
			});
			document.getElementById("detailJudul").innerHTML = 'Resume '+value;
			fetchDetail(value, id);
		}

		function detailLoading(id){
			$('#modalResumeLoading').modal('show');
			document.getElementById("loadingjudul").innerHTML = 'Resume Pengajuan Loading';
			fetchLoading(id);
		}

		function formInsert(){
			$('#modalFormInsert').modal('show');
		}

		function div_in(id){
			var pilihan = id;
			$("#div-in").show();
			$("#div-out").hide();
			$("#form_input").hide();
			$("#pilihan").val(pilihan);
		}

		function div_out(id){
			var pilihan = id;
			$("#div-in").hide();
			$("#div-out").show();
			$("#form_input").hide();
			$("#pilihan").val(pilihan);
		}

		function input_new(value){
			var  limbah = value.split('-');

		// $("#monitoring").hide();
		// $("#div-in").hide();
		// $("#div-out").hide();
			$("#modalFormInsert").modal('show');
			$("#judulForm").html(limbah[0]);
			$("#satuan").html(limbah[1]);
			$("#kemasan").val(limbah[2]);
		// $("#form_input").show();
			$("#limbah").val(value);
			$("#stok").text("0");

			var plh = $("#pilihan").val();

			var data = {
				limbah : limbah[0],
				plh:plh
			}

			$('.select3').select2({
				dropdownParent: $('#modalFormInsert'),
				allowClear : true,
			});
		// dataTableResume(value, plh);
		}

	// function dataTableResume(value, plh){
	// 	var  limbah = value.split('-');

	// 	var data = {
	// 		limbah : limbah[0],
	// 		plh:plh
	// 	}

	// 	$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {
	// 		if (result.stock.length > 0) {
	// 			$("#stok").text(result.stock[0].remaining_stock);
	// 		}else{
	// 			$("#stok").val("0");
	// 		}

	// 		$('#table_history').DataTable().clear();
	// 		$('#table_history').DataTable().destroy();
	// 		$("#body_hostory").empty();
	// 		var body = '';

	// 		$.each(result.history_data, function(index, value){
	// 			body += '<tr>';
	// 			body += '<td>'+(index+1)+'</td>';
	// 			body += '<td>'+value.date_in+'</td>';
	// 			body += '<td>'+value.waste_category+'</td>';
	// 			body += '<td>'+value.category+'</td>';
	// 			body += '<td>'+value.quantity+' '+value.unit_weight+'</td>';
	// 			if (value.category == 'IN') {
	// 				body += '<td><button id="Add"class="btn btn-danger" style="font-weight: bold; color: white" onclick="addDisposal(\''+value.id+'\',\''+plh+'\',\''+value.waste_category+'\')"><i class="fa fa-check" aria-hidden="true"></i></button></td>';
	// 			}else{
	// 				body += '<td><button id="Add"class="btn btn-danger" style="font-weight: bold; color: white" onclick="addDisposal(\''+value.id+'\',\''+plh+'\',\''+value.waste_category+'\')"><i class="fa fa-check" aria-hidden="true"></i></button></td>';
	// 			}
	// 			body += '</tr>';
	// 		})

	// 		$("#body_hostory").append(body);

	// 		var table = $('#table_history').DataTable({
	// 			'dom': 'Bfrtip',
	// 			'responsive':true,
	// 			'lengthMenu': [
	// 			[ 10, 25, 50, -1 ],
	// 			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 			],
	// 			'buttons': {
	// 				buttons:[
	// 				{
	// 					extend: 'excel',
	// 					className: 'btn btn-info',
	// 					text: '<i class="fa fa-file-excel-o"></i> Excel',
	// 					exportOptions: {
	// 						columns: ':not(.notexport)'
	// 					}
	// 				},
	// 				{
	// 					extend: 'pageLength',
	// 					className: 'btn btn-default',
	// 				},
	// 				]
	// 			},
	// 			'paging': true,
	// 			'lengthChange': true,
	// 			'searching': true,
	// 			'ordering': true,
	// 			'info': true,
	// 			'autoWidth': true,
	// 			"sPaginationType": "full_numbers",
	// 			"bJQueryUI": true,
	// 			"bAutoWidth": false,
	// 			"processing": false,
	// 		});
	// 	})

	// }

		function addDisposal(value, kemasan, category, slip){
			var data = {
				slip:slip,
				id : value
			}
			$.post('{{ url("update/disposal") }}', data, function(result, status, xhr) {
				openSuccessGritter('Success','Masuk List Disposal');
			// dataTableResume(value, plh);
				jumlahDisposal();
				fetchDetail(kemasan, category);
			})
		}

		function KonfirmasiDisposal(){
			$('#modalKonfirmasiDisposal').modal('show');
			$.get('{{ url("fetch/maintenance/wwt/waste_control") }}',function(result, status, xhr){

				if(result.status){
					$('#TableKonfirmasiDisposal').DataTable().clear();
					$('#TableKonfirmasiDisposal').DataTable().destroy();
					$('#BodyTableKonfirmasiDisposal').html("");
					var tableData = "";
					$.each(result.konfirmasi_disposal, function(index, value) {
						tableData += '<tr>';
						tableData += '<td style="text-align: center;">'+(index+1)+'</td>';
						tableData += '<td style="text-align: center;">'+ value.slip_disposal +'</td>';
					// tableData += '<td style="text-align: center;">'+ value.date_in +'</td>';
						tableData += '<td style="font-weight: bold; text-align: center;"><a href="{{ url("confirm/limbah/keluar/") }}/'+value.slip_disposal+'" class="btn btn-success btn-md"><i class="fa fa-check-square-o"></i> Konfirmasi</a></td>';
						tableData += '</tr>';
					});
					$('#BodyTableKonfirmasiDisposal').append(tableData);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function jumlahDisposal(){
			$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', function(result, status, xhr) {
				$("#countDisposal").html(result.disposal)
				$("#jb_in").html(result.jb_in)
				$("#jb_out").html(result.jb_out)
				$("#pail_in").html(result.pail_in)
				$("#pail_out").html(result.pail_out)
				$("#drum_in").html(result.drum_in)
				$("#drum_out").html(result.drum_out)
				$("#karton_in").html(result.karton_in)
				$("#karton_out").html(result.karton_out)
				$("#plastik_in").html(result.plastik_in)
				$("#plastik_out").html(result.plastik_out)

			// if (result.jb_in == 0) { 
			// 	var element = document.getElementById("jb_in");
			// 	element.classList.remove("blink");
			// }else{
			// 	var element = document.getElementById("jb_in");
			// 	element.classList.add("blink");
			// }

			// if (result.pail_in == 0) { 
			// 	var element = document.getElementById("pail_in");
			// 	element.classList.remove("blink");
			// }else{
			// 	var element = document.getElementById("pail_in");
			// 	element.classList.add("blink");
			// }

			// if (result.drum_in == 0) { 
			// 	var element = document.getElementById("drum_in");
			// 	element.classList.remove("blink");
			// }else{
			// 	var element = document.getElementById("drum_in");
			// 	element.classList.add("blink");
			// }
			})
		}

		function inputQty(value){
			$("#number_slip").html(value);
			$('#modalDetail').modal('hide');
			$('#modalFormInsertQty').modal('show');
		}

		function SimpanUpdate(){
			$("#loading").show();
			var slip = $("#number_slip").html();
			var data = {
				slip : slip,
				jumlah : $("#qty_input").val()
			}
			$.post('{{ url("update/qty") }}', data, function(result, status, xhr) {
			// console.log("{{url('review/wwt/slip')}}/"+slip);
				$("#loading").hide();
				openSuccessGritter('Success','Qty Berhasil Ditambahkan');
				$('#modalFormInsertQty').modal('hide');
				$("#qty_input").val("");
			// jumlahDisposal();
			// fetchDetail();
				window.open("{{url('review/wwt/slip')}}/"+slip, '_blank');
				location.reload(true);
			})
		}

		function fetchDetail(value, id, limbah){
			$("#judul_kemasan").val(value);
			$("#category_kemasan").val(id);

			var data = {
				kemasan : value,
				category : id,
				select_limbah:limbah
			}
			$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {

				$('#detailtable').DataTable().clear();
				$('#detailtable').DataTable().destroy();
				$("#detailbodytable").empty();
				var body = '';

				$.each(result.resume_detail, function(index, value){
					body += '<tr>';
					body += '<td style="text-align: center">'+(index+1)+'</td>';
					body += '<td style="text-align: center">'+value.slip+'</td>';
					body += '<td style="text-align: center">'+value.date_in+'</td>';
					body += '<td style="text-align: center">'+value.waste_category+'</td>';
					body += '<td style="text-align: center">'+value.category+'</td>';
					if (value.quantity == null) {
						body += '<td style="text-align: center"><span class="label label-danger">Qty Belum Input</span></td>';
					// body += '<td><button id="Add"class="btn btn-success" style="font-weight: bold; color: white" onclick="input_new(\''+value.id+'\', \''+value.kemasan+'\', \''+value.category+'\')"><i class="fa fa-sign-in" aria-hidden="true"></i></button></td>';
					}else{
						body += '<td style="text-align: center">'+value.quantity+' '+value.unit_weight+'</td>';
					}
					if (value.category == 'IN') {
						if (value.quantity == null) {
							body += '<td style="text-align: center">-</td>';		
						}else{
							// body += '<td style="text-align: center"><button id="Add"class="btn btn-success" style="font-weight: bold; color: white" onclick="addDisposal(\''+value.id+'\', \''+value.kemasan+'\', \''+value.category+'\', \''+value.slip+'\')"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Masukkan Ke<br>Keranjang</button></td>';
							// body += '<td><select class="form-control select2" id="supplier_code" name="supplier_code" class="supplier_code" data-placeholder="Pilih Vendor" style="width: 100%"><option value="">&nbsp;</option>@foreach($vendor as $ven)<option value="{{$ven->short_name}}">{{$ven->short_name}}</option>@endforeach</select></td>'
							body += '<td style="text-align: center">-</td>';
						}
					}else{
						body += '<td style="text-align: center">1 '+value.kemasan+'</td>';
					}
				// body += '<td><button class="btn btn-info" style="font-weight: bold; color: white" onclick="addDisposal(\''+value.id+'\')"><i class="fa fa-book" aria-hidden="true"></i> Log Book</button></td>';

				// body += '<td><a href="{{ url('logbook/wwt/') }}/'+value.slip+'" class="btn btn-info"><i class="fa fa-book" aria-hidden="true"></i> Log Book</a></td>';




					if (value.category == 'IN') {
						body += '<td style="text-align: center">Belum Pengajuan Disposal</td>';
					}else{
						body += '<td style="text-align: center">Limbah Sudah Di Disposal</td>';
					}

					if (value.quantity == null) {
						body += '<td style="text-align: center">-</td>';		
					}else{
						if (id == 'IN') {
							body += '<td style="text-align: center"><input type="checkbox" value="'+value.slip+'" id="chekedmasuk" class="check_masuk"></td>';
						}else{
							body += '<td style="text-align: center">-</td>';
						}
					}
					body += '</tr>';
				})
				$("#detailbodytable").append(body);

				if (result.resume_detail.length > 0) {
					var tombol = '';
					if (id == 'IN') {
						tombol += '<button id="maukkan_keranjang" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 50%; color: white; background-color: #2ecc71;" onclick="MasukkanKeranjang()">Masukkan Keranjang</button>';
					}else{
						// tombol = '<a href="{{ url('index/confirmation/limbah') }}" class="btn" style="margin-left: 5px; width: 50%; background-color: #ffb600; color: black;"><i class="fa fa-list"></i> Approval Disposal Limbah</a>';
						tombol += '';
					}

					$("#tombol_masukkan_keranjang").html(tombol);
				}else{
					$("#tombol_masukkan_keranjang").html('');
				}

				$('.select2').select2({
					dropdownParent: $('#modalDetail'),
					allowClear : true,
				});

				var table = $('#detailtable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": false,
				});

				$("#max_limbah").val(result.resume_detail.length);
			})
}

function fetchLoading(id){
	var data = {
		id : id
	}
	$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {

		$('#detailtableloading').DataTable().clear();
		$('#detailtableloading').DataTable().destroy();
		$("#detailbodytableloading").empty();
		var body = '';

		$.each(result.detail_disposal, function(index, value){
			body += '<tr>';
			body += '<td>'+(index+1)+'</td>';
			body += '<td>'+value.slip+'</td>';
			body += '<td>'+value.date_in+'</td>';
			body += '<td>'+value.waste_category+'</td>';
			body += '<td>'+value.category+'</td>';
			body += '<td>'+value.quantity+' KG</td>';
			body += '<td>'+value.short_name+'</td>';
			body += '<td>1 '+value.kemasan+'</td>';
			body += '</tr>';
		})

		$("#detailbodytableloading").append(body);

		var table = $('#detailtableloading').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
			'buttons': {
				buttons:[
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'pageLength',
					className: 'btn btn-default',
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": false,
		});
	})
}

function check_balance() {
	var elem = $("#limbah").val();
	if (elem == "") {
		return false;
	}

	$("#stok").text("0");

	var data = {
		limbah : elem
	}

	$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {
		if (result.history_data.length > 0) {
			$("#stok").text(result.history_data[0].remaining_stock);
		}

		$('#table_history').DataTable().clear();
		$('#table_history').DataTable().destroy();
		$("#body_hostory").empty();
		var body = '';

		$.each(result.history_data, function(index, value){
			body += '<tr>';
			body += '<td>'+(index+1)+'</td>';
			body += '<td>'+value.date_in+'</td>';
			body += '<td>'+value.waste_category+'</td>';
			body += '<td>'+value.category+'</td>';
			body += '<td>'+value.quantity+'</td>';
			body += '<td>'+value.remaining_stock+'</td>';
			body += '</tr>';
		})

		$("#body_hostory").append(body);

		var table = $('#table_history').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
			'buttons': {
				buttons:[
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'pageLength',
					className: 'btn btn-default',
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": false,
		});
	})
}

$('input[type=radio][name=category_input]').change(function() {
	if (this.value == 'IN') {
		$("#tanggal_input").prop('disabled', false);
	}
	else if (this.value == 'OUT') {
		$("#tanggal_input").val('{{ date("Y-m-d") }}').trigger('change');
		$("#tanggal_input").prop('disabled', true)
	}
});

function logbook(){
	var data = {
		tanggal : $("#tanggal_input").val(),
		pic : $("#pic_input_logbook").val(),
		dari_lokasi : $("#dari_lokasi_logbook").val(),
		slip : $("#number_slip").html(),
	}
	$.post('{{ url("insert/logbook/update") }}', data, function(result, status, xhr) {
		if(result.status){
			openSuccessGritter('Success!', result.message);
			location.reload(true);
		}else{
			openErrorGritter('Error!', result.message);
		}
	})

}

function check() {
	$("#loading").show();
	if ($('#pilihan').val() == 'IN') {
		var sisa_stok = parseFloat($("#stok").text()) + parseFloat($("#qty_input").val());
	} else if($('#pilihan').val() == 'OUT') {
		var sisa_stok = parseFloat($("#stok").text()) - parseFloat($("#qty_input").val());
	}		

	var data = {
		kategori : $('#pilihan').val(),
		pic : $("#pic_input").val(),
		tanggal : $("#tanggal_input").val(),
		jenis_limbah : $("#judulForm").html(),
			// jumlah : $("#qty_input").val(),
		satuan : $("#satuan").html(),
		kemasan : $("#kemasan").val(),
		dari_lokasi : $("#dari_lokasi").val(),
		sisa_stok : sisa_stok
	}

	$.post('{{ url("post/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {
		if(result.status){
			$("#loading").hide();
			check_balance();
			openSuccessGritter('Success!', result.message);
			location.reload(true);
			window.open("{{url('review/wwt')}}/"+result.slip, '_blank');
		}else{
			openErrorGritter('Error!', result.message);
		}
	})
}

function SelectFilter(limbah){
	var jenis = $("#judul_kemasan").val();
	var category = $("#category_kemasan").val();

	fetchDetail(jenis, category, limbah)
}

function MasukkanKeranjang(){
	$("#loading").show();
	var checked_masuk = "";
	var ck = [];

	$.each($(".check_masuk"), function(key, value) {
		if($(this).is(":checked")){
			ck.push(value.value+'-'+$("#supplier_code").val());
		}
	});

	checked_masuk = ck.toString();

	if (checked_masuk.length == 0) {
		openErrorGritter('Error','Pilih slip terlebih dahulu.');
	}else{
		data = {
			slip:checked_masuk
		}
		$.post('{{ url("update/disposal") }}', data, function(result, status, xhr) {
			$("#loading").hide();
			openSuccessGritter('Success','Masuk List Disposal');
			location.reload(true);
			// {{ url("kirim_email/disposal") }}
		})
	}
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '4000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '4000'
	});
}

</script>
@endsection