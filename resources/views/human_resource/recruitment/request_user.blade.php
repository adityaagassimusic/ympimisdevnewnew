@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
	#loading { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-right: 0;">
				<div>
					<button class="btn pull-right" style="margin-left: 5px; width: 10%; color: black; padding-bottom; background-color: #2ecc71" onclick="InputMp();"><i class="fa fa-compress" aria-hidden="true"></i> Forecast MP</button>

					@if($role->username == 'PI2101044')
					<button class="btn pull-right" style="margin-left: 5px; width: 10%; color: black; padding-bottom; background-color: #DADBCD" onclick="TestEmail();"><i class="fa fa-user-o"></i> Tombol Test</button>
					@endif
					<!-- <button class="btn pull-right" style="margin-left: 5px; width: 10%; color: black; padding-bottom; background-color: #eed7a1" onclick="UploadMPInterview();"><i class="fa fa-user-o"></i> Data Interview</button>
					<button class="btn pull-right" style="margin-left: 5px; width: 10%; color: black; padding-bottom; background-color: #DADBCD" onclick="TestEmail();"><i class="fa fa-user-o"></i> Tombol Test</button> -->
					<!-- <button class="btn pull-right" style="margin-left: 5px; width: 15%; color: white; padding-bottom; background-color: #3c9ce7" onclick="RequestMagang();"><i class="fa fa-list"></i> Request Magang</button> -->
				</div>
				<!-- <div class="col-xs-3" style="padding-left: 0px; padding-bottom: 10px; padding-top: 10px">
					<select id="select_dept" style="width: 100%;text-align: left;height:30px;" class="form-control select4" data-placeholder="Pilih Department" onchange="fillChart(this.value)">
						<option value="">&nbsp;</option>
						@foreach($departments as $row)
						<option value="{{$row->department_name}}">{{$row->department_name}}</option>
						@endforeach
					</select>
				</div> -->
			</div>
			<div class="col-xs-3" style="padding-right: 0;">
				<a href="{{ url('index/emp_data') }}">
					<!-- <div class="small-box" style="background: #E5DACE; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailMPInterview()"> -->
						<div class="small-box" style="background: #E5DACE; height: 25vh; margin-bottom: 5px;cursor: pointer; color:black">
							<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
								<!-- <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Interview</b></h3> -->
								<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Aktual MP</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 2vw;color: #0d47a1;"><b>誘導トレーニング</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="aktual_sekarang"></h3>
								<!-- <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_interview">0</h5> -->
								<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_aktual">0</h5>
							</div>
							<div class="icon" style="padding-top: 10px; font-size:10vh">
								<i class="fa fa-address-book" aria-hidden="true"></i>
							</div>
						</div>
					</a>
					<div class="small-box" style="background: #EBC8B4; height: 25vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailEmpPutus()">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Putus Kontrak</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: #0d47a1;"><b>見習い</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="putus_kontrak"></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_putus_kontrak">0</h5>
						</div>
						<div class="icon" style="padding-top: 10px; font-size:10vh">
							<i class="fa fa-user" aria-hidden="true"></i>
						</div>
					</div>
					<!-- <div class="small-box" style="background: #B2B2A2; height: 25vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailMPMagang()"> -->
						<div class="small-box" style="background: #B2B2A2; height: 25vh; margin-bottom: 5px;cursor: not-allowed; color:black">
							<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
								<!-- <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Magang</b></h3> -->
								<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kebutuhan</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 2vw;color: #0d47a1;"><b>見習い</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="kebutuhan_bulan"></h3>
								<!-- <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_magang">0</h5> -->
								<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_direkrut">0</h5>
							</div>
							<div class="icon" style="padding-top: 10px; font-size:10vh">
								<i class="fa fa-podcast" aria-hidden="true"></i>
							</div>
						</div>
						<!-- <div class="small-box" style="background: #6D7973; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailMPStock()"> -->
							<!-- <div class="small-box" style="background: #6D7973; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailMPMagang()">
								<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
									<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Magang</b></h3>
									<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: #0d47a1;"><b>見習い</b></h3>
									<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_magang">0</h5>
								</div>
								<div class="icon" style="padding-top: 10px; font-size:10vh">
									<i class="fa fa-users"></i>
								</div>
							</div> -->
						</div>
						<!-- <div class="col-xs-2" style="padding-right: 0;"> -->
					<!-- <a href="{{ url('index/emp_data') }}">
						<div class="small-box" style="background: #00af50; height: 30.5vh; margin-bottom: 5px;cursor: pointer; color:black">
							<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw; color: black;"><b>Aktual MP</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>誘導トレーニング</b></h3>
								<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="induction">0</h5>
								<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_aktual">0</h5>
							</div>
							<div class="icon" style="padding-top: 75px;">
								<i class="fa fa-clock-o" aria-hidden="true"></i>
							</div>
						</div>
					</a> -->

					<!-- <div class="small-box" style="background: #e4c46d; height: 30vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="ShowModalAll('MAGANG')"> -->
				<!-- <div class="small-box" style="background: #F24545; height: 30.5vh; margin-bottom: 5px;cursor: pointer; color:black">
					<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kebutuhan</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>見習い</b></h3>
						<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red;" id="magang">0</h5>
						<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="total_direkrut">0</h5>
					</div>
					<div class="icon" style="padding-top: 75px;">
						<i class="fa fa-minus-circle" aria-hidden="true"></i>
					</div>
				</div> -->
				<!-- </div> -->
				<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px">
					<div id="grafik01" style="width: 100%;height: 500px;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 10px;padding-right: 5px">
					<div id="grafik03" style="width: 100%; height: 250px"></div>
					<div id="grafik02" style="width: 100%; height: 250px"></div>
				</div>
			<!-- <div class="col-xs-4" style="padding-right: 0;">
				<div class="small-box" style="background: #E2B091; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black; border: 10px inset green" onclick="DetailMPInterview()">
					<div class="inner" style="padding-bottom: 0px;padding-top: 20px;">
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Interview</b></h3>
					</div>
					<div class="icon" style="padding-top: 10px;">
						<i class="fa fa-users"></i>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-xs-12" style="padding-right: 0;">
				<div class="col-xs-2" style="padding-left: 0px; padding-bottom: 10px; padding-top: 10px">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control pull-right" id="select_month" placeholder="Pilih Bulan" onchange="fillChart()">
					</div>
				</div>
			</div> -->
			<!-- <div class="col-xs-12" style="padding-right: 0;">
				<div style="margin-top: 5px;text-align: center;background-color: #ff8a33;">
					<span style="font-size: 24px;font-weight: bold;color: white;">Resume MP Habis Kontrak Bulan <span id="id_bulan"></span></span>
				</div>
				<div style="margin-top: 5px">
					<table id="tableResume" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%">No</th>
								<th style="width: 1%">NIK</th>
								<th style="width: 2%">Nama</th>
								<th style="width: 3%">Department</th>
								<th style="width: 1%">Tanggal Kontrak</th>
								<th style="width: 1%">Habis Kontrak</th>
								<th style="width: 1%">Perpanjang</th>
							</tr>
						</thead>
						<tbody id="bodyResume">
						</tbody>
					</table>
				</div> -->
				<!-- <div style="margin-top: 5px">
					<button style="width: 100%;font-weight: bold;font-size: 30px" class="btn btn-success btn-xs" onclick="OpenModalPerpanjang()"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Perpanjang Kontrak</button>
				</div> -->
				<!-- </div> -->
			</div>
		</div>
	</section>

	<div class="modal fade" id="ModalDetailProcess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;background-color: #1a237e;">
						<span style="font-size: 24px;font-weight: bold;color: white;">Detail Request Manpower</span>
					</div>
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
					</div>  
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
						<table class="table table-hover table-bordered table-striped" id="ListDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th>Point</th>
									<th>Content</th>
								</tr>
							</thead>
							<tbody id="BodyListDetail">
							</tbody>
						</table>
					</div> 
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="ModalDetailMagang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;background-color: #1a237e;">
						<span style="font-size: 24px;font-weight: bold;color: white;">Detail Request Karyawan Magang</span>
					</div>
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
					</div>  
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
						<table class="table table-hover table-bordered table-striped" id="ListDetailMagang">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th>Point</th>
									<th>Content</th>
								</tr>
							</thead>
							<tbody id="BodyListDetailMagang">
							</tbody>
						</table>
					</div> 
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="nav-tabs-custom tab-danger" align="center">
						<ul class="nav nav-tabs">
							<center><h3 style="background-color: #2ecc71; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Upload Forecast MP</h3>
							</center>
						</ul>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
						<div class="col-xs-12">
						<!-- <div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label" style="color: black;">Bulan<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="bulan" name="bulan" placeholder="Pilih Bulan">
								</div>
							</div>
						</div> -->
						<!-- <div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label" style="color: black;">Pilih Keterangan<span class="text-red"> :</span></label>
							<div class="col-sm-6" align="left">
								<select id="upload_keterangan" style="width: 100%;text-align: left;height:30px;" class="form-control select4" data-placeholder="Pilih Keterangan" onchange="PilihKeterangan(this.value)">
									<option value="">&nbsp;</option>
									<option value="INDIRECT">INDIRECT</option>
									<option value="DIRECT">DIRECT</option>
								</select>
							</div>
						</div> -->
						<div class="form-group row" align="right" id="pilih_section">
							<label for="" class="col-sm-4 control-label" style="color: black;">Pilih Section<span class="text-red"> :</span></label>
							<div class="col-sm-6" align="left">
								<select id="upload_sect" style="width: 100%;text-align: left;height:30px;" class="form-control select4" data-placeholder="Pilih Section">
									<option value="">&nbsp;</option>
									@foreach($emp_sect as $row)
									<option value="{{$row->section}}">{{$row->section}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-4 control-label" style="color: black;">Contoh Template Upload<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<a href="{{ url('uploads/kebutuhan_mp/TemplateKebutuhanMp.xlsx') }}">TemplateMp.xlsx</a>
							</div>
							<!-- <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
							Format: [Material Number][Material Description][SLoc][Unrestricted][Download Date][Download Time]<br>
							Sample: <a href="{{ url('download/manual/import_storage_location_stock.txt') }}">import_storage_location_stock.txt</a> Code: #Truncate -->
						</div>
						<div class="modal-footer">
							<center>
								<!-- <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> -->
								<!-- <button onclick="UploadMp()" class="btn btn-success">Upload</button> -->
								<button type="button" id="button_submit" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #2ecc71;" onclick="UploadMp()"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
							</center>
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="ModalUploadMPInterview">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<center><h3 style="background-color: #eed7a1; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Upload MP Interview</h3>
						</center>
					</ul>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="file" name="upload_interview" id="upload_interview" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-4 control-label" style="color: black;">Contoh Template Upload<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<a href="{{ url('uploads/kebutuhan_mp/TemplateUploadInterview.xlsx') }}">TemplateUpload.xlsx</a>
							</div>
						</div>
						<div class="modal-footer">
							<center>
								<button type="button" id="button_submit" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #2ecc71;" onclick="InsertMpInterview()"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalDetailMPInterview">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="padding-bottom: 15px;color: black; text-align: center" class="modal-title">Detail Interview</h2>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table id="tableResumeInterview" class="table table-bordered table-hover" style="width: 100%">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 1%">No</th>
								<th style="width: 3%">NIK</th>
								<th style="width: 3%">Nama</th>
								<th style="width: 3%">Aksi</th>
							</tr>
						</thead>
						<tbody style="background-color: #fcf8e3;" id="bodyResumeInterview">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalDetailMPMagang">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="padding-bottom: 15px;color: black; text-align: center" class="modal-title">Detail Magang</h2>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table id="tableResumeMagang" class="table table-bordered table-hover" style="width: 100%">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 1%">No</th>
								<th style="width: 3%">NIK</th>
								<th style="width: 3%">Nama</th>
								<th style="width: 3%">Aksi</th>
							</tr>
						</thead>
						<tbody style="background-color: #fcf8e3;" id="bodyResumeMagang">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalDetailPutusKontrak">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="padding-bottom: 15px;color: black; text-align: center" class="modal-title">Detail Putus Kontrak</h2>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table id="tableResumePutusKontrak" class="table table-bordered table-striped table-hover" style="width: 100%">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 10%">No</th>
								<th style="width: 20%">NIK</th>
								<th style="width: 30%">Nama</th>
								<th style="width: 40%">Departemen</th>
							</tr>
						</thead>
						<tbody style="background-color: #fcf8e3;" id="bodyResumePutusKontrak">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalDetailMPStock">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="padding-bottom: 15px;color: black; text-align: center" class="modal-title">Detail Stock</h2>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table id="tableResumeStock" class="table table-bordered table-hover" style="width: 100%">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 1%">No</th>
								<th style="width: 3%">NIK</th>
								<th style="width: 3%">Nama</th>
								<th style="width: 3%">Aksi</th>
							</tr>
						</thead>
						<tbody style="background-color: #fcf8e3;" id="bodyResumeStock">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog" style="width:1000px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<div id="table_detail">
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th>#</th>
									<!-- <th style="width: 9%;">Department</th> -->
									<th>Section</th>
									<!-- <th style="width: 2%;">Group</th>
										<th style="width: 1%;">Sub Group</th> -->
										<th>Jumlah</th>
										<!-- <th style="width: 1%;">Buat Request</th> -->
									</tr>
								</thead>
								<tbody id="tableDetailBody">
								</tbody>
							</table>
						</div>
						<div id="form_request">
							<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
								<form class="form-horizontal">
									<div class="col-xs-12">
										<div class="col-xs-12" style="background-color: #2ecc71;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
											<span style="font-size: 25px;color: white;width: 25%;">Form Request Manpower</span>
										</div>
										<div class="form-group">
											<label style="padding-top: 30px;" for="" class="col-sm-3 control-label">Posisi<span class="text-red">*</span> :</label>
											<div class="col-sm-6" style="padding-top: 30px;">
												<select class="form-control select2" id="createPosition" data-placeholder="Pilih Posisi" style="width: 100%;" onchange="SelectEmployee()">
													<option></option>
													@foreach($positions as $position)
													<option value="{{ $position }}">{{ $position }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="form-group">
											<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="createDepartment" name="createDepartment" readonly>
											</div>
										</div>
										<div class="form-group">
											<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Section<span class="text-red">*</span> :</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="createSection" name="createSection" readonly>
											</div>
										</div>
										<div class="form-group">
											<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Group<span class="text-red">*</span> :</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="createGroup" name="createGroup" readonly>
											</div>
										</div>
										<div class="form-group">
											<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Sub Group<span class="text-red">*</span> :</label>
											<div class="col-sm-6">
												<input type="text" class="form-control" id="createSubGroup" name="createSubGroup" readonly>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<div class="col-xs-12" style="background-color: #e74c3c;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle; width: 30%;" align="center">
													<span style="font-size: 25px;color: white;width: 25%;">Batas Request : <span id="kuota"></span></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah<span class="text-red">*</span> :</label>
											<div class="col-sm-3">
												<div class="input-group">
													<input id="createMale" name="createMale" type="number" class="form-control numpad" value="0" onchange="checkValue(this.id)">
													<span class="input-group-addon" style="width: 100px;">Laki-laki</span>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="input-group">
													<input id="createFemale" name="createFemale" type="number" class="form-control numpad" value="0" onchange="checkValue(this.id)">
													<span class="input-group-addon" style="width: 100px;">Perempuan</span>
												</div>
											</div>
										</div>
							<!-- <div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label"><span class="text-red"> </span> </label>
								<div class="col-sm-3">
									<div class="input-group">
										<input id="createFemale" name="createFemale" type="number" class="form-control numpad" value="0" onchange="checkValue(this.id)">
										<span class="input-group-addon" style="width: 100px;">Perempuan</span>
									</div>
								</div>
							</div> -->
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Alasan Penambahan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Masukkan Alasan Penambahan" id="createReason"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Perkiraan Tanggal Masuk<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<!-- <input type="hidden" class="form-control pull-right" id="datenow"> -->
										<input type="text" class="form-control pull-right" id="datein" placeholder="Pilih Tanggal" onchange="checkDate(this.value)">
									</div>
								</div>
								<!-- <div class="col-md-4">
									<div class="form-group">
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="datefrom" data-placeholder="Select Date">
										</div>
									</div>
								</div> -->
								<div class="col-sm-6" id="alert_tanggal">
									<span class="text-red">*</span><span>Maksimal kurang dari 3 minggu</span>
								</div>
							</div>
							<!-- <div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Perkiraan Tanggal Masuk<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="createStartDate" value="{{ $date }}" readonly>
								</div>
								<div class="col-sm-6">
									<span class="text-red">*</span><span>Maksimal kurang dari 3 minggu</span>
								</div>
							</div> -->
						</div>				
					</form>
					<span style="font-weight: bold; font-size: 1.2vw;">Kualifikasi Umum</span>
					<hr style="margin-top: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Usia<span class="text-red"></span> :</label>
									<div class="col-sm-4">
										<div class="input-group">
											<span class="input-group-addon" style="width: 50px;">Min</span>
											<input type="number" class="form-control numpad" placeholder="Usia" value="0" id="createMinAge" min="0">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="input-group">
											<span class="input-group-addon" style="width: 50px;">Max</span>
											<input type="number" class="form-control numpad" placeholder="Usia" value="0" id="createMaxAge" min="0">
										</div>
									</div>
								</div>
								<!-- <div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label"><span class="text-red"></span> </label>
									<div class="col-sm-5">
										<div class="input-group">
											<span class="input-group-addon" style="width: 50px;">Max</span>
											<input type="number" class="form-control numpad" placeholder="Usia" value="0" id="createMaxAge" min="0">
										</div>
									</div>
								</div> -->
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Status<span class="text-red"></span> :</label>
									<div class="col-sm-8">
										<select class="form-control select2" id="createMarriageStatus" data-placeholder="Pilih Status" style="width: 100%;">
											<option></option>
											<option value="Belum Menikah">Belum Menikah</option>
											<option value="Sudah Menikah">Sudah Menikah</option>
										</select>
									</div>
								</div>
								<!-- <div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Domisili<span class="text-red"></span> :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" placeholder="Masukkan Domisili" id="createDomicile">
									</div>
								</div> -->
								<!-- <div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Pengalaman<span class="text-red"></span> :</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="number" class="form-control numpad" placeholder="Qty" value="0" id="createWorkExperience" min="0">
											<span class="input-group-addon" style="width: 60px;">Tahun</span>
										</div>
									</div>
								</div> -->
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jenjang<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<select class="form-control select3" multiple="" id="createEducationLevel" data-placeholder="Pilih Jenjang" style="width: 100%;">
											<option value="SMA">SMA</option>
											<option value="SMK">SMK</option>
											<option value="SMA">D1</option>
											<option value="SMK">D2</option>
											<option value="SMA">D3</option>
											<option value="SMK">D4</option>
											<option value="SMK">S1</option>
											<option value="SMK">S2</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jurusan<span class="text-red"></span> :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" placeholder="Pilih Jurusan" id="createMajor">
									</div>
								</div>
							</div>
						</div>
					</form>
					<span style="font-weight: bold; font-size: 1.2vw;">Kualifikasi Khusus</span>
					<hr style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="col-xs-6">
							<div style="margin-bottom: 10px;">
								<span style="font-weight: bold; font-size: 14px;">Keahlian/ketrampilan yang diutamakan<span class="text-red">*</span></span>
								<button class="btn btn-success btn-xs pull-right" onclick="addSkill()"><i class="fa fa-plus"></i></button>
							</div>
							<div id="skill">
							</div>
						</div>
						<div class="col-xs-6">
							<div style="margin-bottom: 10px;">
								<span style="font-weight: bold; font-size: 14px;">Persyaratan Lainnya </span>
								<button class="btn btn-success btn-xs pull-right" onclick="addRequirement()"><i class="fa fa-plus"></i></button>
							</div>
							<div id="requirement">
							</div>
						</div>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="select">
						<span style="font-weight: bold; font-size: 1.2vw;">Employee</span>
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="VeteranEmployee()">Rekontrak Employee</button>
						<button class="btn btn-success pull-left" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="NewEmployee()">New Employee</button>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="btn-new">
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="NewEmployee()">New Employee</button>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="btn-veteran">
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="VeteranEmployee()">Rekontrak Employee</button>
					</div>

					<div id="new">
						<span style="font-weight: bold; font-size: 1.2vw;">Request Karyawan Baru</span>
						<hr style="margin-top: 5px;">
						<div class="col-md-12" style="margin-bottom : 5px;">
							<div class="col-xs-4" style="padding:0">
								<input type="checkbox" name="new_employee" id="new_employee" value="Request Karyawan Baru"> Request Karyawan Baru
							</div>
							<!-- <div class="col-xs-4" style="padding:0">
								<span>Section</span>
								<select class="form-control select9" id="sect_penempatan" name="sect_penempatan" data-placeholder='Section' onchange="SelectSection(this.value)" style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-2" style="padding:0; padding-left: 5px">
								<span>Group</span>
								<select class="form-control select9" id="loc_penempatan" name="loc_penempatan" data-placeholder='Group' onchange="SelectGroup(this.value)" style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-2" style="padding:0; padding-left: 5px">
								<span>Sub Group</span>
								<select class="form-control select9" id="process_penempatan" name="process_penempatan" data-placeholder='Sub Group' style="width: 100%" required>
								</select>
							</div> -->
						</div>
					</div>

					<div id="veteran">
						<span style="font-weight: bold; font-size: 1.2vw;">Request Rekontrak Employee</span>
						<hr style="margin-top: 5px;">
						<input type="hidden" name="lop" id="lop" value="1">
						<input type="hidden" name="req_id" id="req_id">
						<div id="1" class="col-md-12" style="margin-bottom : 5px;">
							<!-- <div class="col-xs-3" style="padding:0;">
								<select class="form-control select5" id="description1" name="description1" data-placeholder='Pilih Nama' style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-3" style="padding:0; padding-left: 5px">
								<select class="form-control select6" id="penempatan1" name="penempatan1" data-placeholder='Sub Group' style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-3" style="padding:0; padding-left: 5px">
								<input type="text" class="form-control" name="process1" id="process1" placeholder='Process' required>
							</div>
							<div class="col-xs-1" style="padding:0; padding-left: 5px">
								<input type="text" class="form-control" placeholder="/Bulan" name="durasi1" id="durasi1">
							</div>
							<div class="col-xs-2" style="padding:0; padding-left: 5px;">
								<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
							</div>   -->
							<div class="col-xs-3" style="padding:0;">
								<label style="color: black;">Nama</label>
								<select class="form-control select5" id="description1" name="description1" data-placeholder='Pilih Nama' onchange="SelectNik(this.value)" style="width: 100%" required>
								</select>
								<input type="hidden" class="form-control" id="name" placeholder="NIK Lama" required>
							</div>
							<!-- <div class="col-xs-3" style="padding:0; padding-left: 5px">
								<label style="color: black;">Group</label>
								<input type="text" class="form-control" id="group1" placeholder="Group" required readonly>
							</div> -->
							<!-- <div class="col-xs-3" style="padding:0; padding-left: 5px">
								<label style="color: black;">Sub Group</label>
								<input type="text" class="form-control" id="sub_group1" placeholder="Sub Group" required readonly>
							</div> -->
							<!-- <div class="col-xs-1" style="padding:0; padding-left: 5px">
								<label style="color: black;">Waktu</label>
								<select class="form-control select5" name="durasi1" id='durasi1' style="width: 100%; height: 100px;">
									<option value="0">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
								</select>
							</div> -->
							<!-- <div class="form-group row">
								<div class="col-xs-3" style="padding:0; padding-left: 5px">
									<select class="form-control select6" id="penempatan1" name="penempatan1" data-placeholder='Sub Group' style="width: 100%" required>
									</select>
								</div>
							</div> -->
							<div class="col-xs-2" style="padding:0; padding-left: 5px; padding-top: 25px">
								<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
							</div>
							<div id="tambah"></div>
						</div>
					</div>

					<span style="font-weight: bold; font-size: 1.2vw;">Catatan</span><br>
					<span style="font-size: 95%;">(Cantumkan NIK, Nama, Departemen Rekomendasi)</span>
					<hr style="margin-top: 5px;">
					<div class="col-xs-12" style="margin-bottom: 15px;">
						<textarea class="form-control" rows="3" placeholder="Masukkan Catatan" id="createNote"></textarea>
					</div>
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Kembali</button>
					<button id="button_submit" class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmRequest()">Konfirmasi</button>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>

<div class="modal fade" id="ModalDetailRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;background-color: #1a237e;">
					<span style="font-size: 24px;font-weight: bold;color: white;">Detail Request Recruitment HR</span>
				</div> 
				<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
					<table class="table table-hover table-bordered table-striped" id="RequestDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th>Keterangan</th>
								<th>Detail</th>
							</tr>
						</thead>
						<tbody id="RequestBodyDetail">
						</tbody>
					</table>
				</div> 
				<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%" id="karyawan_lama">
					<table class="table table-hover table-bordered table-striped" id="DetailKaryawan">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th>No</th>
								<th>NIK</th>
								<th>Nama</th>
								<th>Department</th>
								<th>Group</th>
								<th>Sub Group</th>
							</tr>
						</thead>
						<tbody id="BodyDetailKaryawan">
						</tbody>
					</table>
				</div> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="showModalAll" style="z-index: 10000;">
	<div class="modal-dialog" style="width:1250px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="ModalAllTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingAll" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableAll">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 9%;">Department</th>
								<th style="width: 9%;">Section</th>
								<th style="width: 9%;">Group</th>
								<th style="width: 9%;">Sub Group</th>
								<th style="width: 6%;">Jumlah</th>
							</tr>
						</thead>
						<tbody id="tableAllBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalRequestMagang">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<form class="form-horizontal">
							<div class="col-xs-12">
								<div class="col-xs-12" style="background-color: #3c9ce7;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
									<span style="font-size: 25px;color: white;width: 75%;">Form Request Magang</span>
								</div>
								<div class="form-group">
									<label style="padding-top: 30px;" for="" class="col-sm-3 control-label">Posisi<span class="text-red">*</span> :</label>
									<div class="col-sm-9" style="padding-top: 30px;">
										<input type="text" class="form-control" id="PositionMagang" name="PositionMagang" value="Operator" readonly>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="DepartmentMagang" name="DepartmentMagang" readonly>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Section<span class="text-red">*</span> :</label>
									<div class="col-sm-9">
										<!-- <input type="text" class="form-control" id="SectionMagang" name="SectionMagang" readonly> -->
										<select class="form-control select11" id="SectionMagang" data-placeholder="Pilih Section" onchange="CreateSection(this.value)" style="width: 100%;">
											<option></option>
											@foreach($emp_sect as $sect)
											<option value="{{ $sect->section }}">{{ $sect->section }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Group<span class="text-red">*</span> :</label>
									<div class="col-sm-9">
										<!-- <input type="text" class="form-control" id="GroupMagang" name="GroupMagang" readonly> -->
										<select class="form-control select12" id="GroupMagang" name="GroupMagang" data-placeholder="Pilih Group" onchange="CreateGroup(this.value)" style="width: 100%" required>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Sub Group<span class="text-red">*</span> :</label>
									<div class="col-sm-9">
										<!-- <input type="text" class="form-control" id="SubGroupMagang" name="SubGroupMagang" readonly> -->
										<select class="form-control select13" id="SubGroupMagang" name="SubGroupMagang" data-placeholder="Pilih Sub Group" style="width: 100%" required>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah<span class="text-red">*</span> :</label>
									<div class="col-sm-4">
										<div class="input-group">
											<input id="qty_male" name="qty_male" type="number" class="form-control numpad" value="0" onchange="checkValue(this.id)" style="width: 70px">
											<span class="input-group-addon">Laki-Laki</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="input-group">
											<input id="qty_female" name="qty_female" type="number" class="form-control numpad" value="0" onchange="checkValue(this.id)" style="width: 70px">
											<span class="input-group-addon">Perempuan</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Target Tanggal Masuk Magang<span class="text-red">*</span> :</label>
									<div class="col-sm-6">
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="DateMagang" placeholder="Pilih Tanggal">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Perkiraan Masuk Kontrak<span class="text-red">*</span> :</label>
									<div class="col-sm-6">
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="DateKontrak" placeholder="Pilih Tanggal">
										</div>
									</div>
								</div>
								<div class="form-group">
									<center>	
										<button type="button" id="button_submit" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #2ecc71;" onclick="confirmMagang()">Konfirmasi</button>
									</center>
								</div>
							</div>				
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalPerpanjang" role="dialog">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="form-group row" align="center">
					<div class="col-md-12">
						<table class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 0px; text-align: center">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th style="text-align: center; width: 30%">NIK</th>
									<th style="text-align: center; width: 40%">Nama</th>
									<th style="text-align: center; width: 30%">Habis Kontrak</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td id="id_nik"></td>
									<td id="id_nama"></td>
									<td id="id_habis" style="background-color: #ec5a43"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="form-group row" align="center">
					<div class="col-md-6">
						<span>Perpanjang Sampai Dengan : </span>
					</div>
					<div class="col-md-6" style="width: 45%">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="DatePerpanjang" placeholder="Pilih Tanggal">
						</div>
					</div>
				</div>
				<div class="form-group row" align="center">
					<div class="col-md-12">
						<button class="btn pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; color: white; width: 45%; background-color: #ec5a43">Kembali</button>
						<button type="button" id="button_submit" class="btn pull-right" style="font-weight: bold; font-size: 1.3vw; width: 45%; color: white; background-color: #2ecc71;" onclick="PerpanjangKontrak()">Simpan Perubahan</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<!-- <script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/highcharts-3d.js")}}"></script> -->
	<script src="{{ url("js/highstock.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/jquery.numpad.js") }}"></script>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
		var intervalChart;
		var skill_count = 0;
		var skills = [];
		var requirement_count = 0;
		var requirements = [];
		var emps = 	'';
		var sec = '';
		var new_karyawan = '';
		var no = 2;
		var kuotas = 0;


	// $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	// $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	// $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	// $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	// $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	// $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

		jQuery(document).ready(function(){
			$('body').toggleClass("sidebar-collapse");
		// ListMonitoring();
			fillChart();
			$('.select2').select2();
			$('.select1').select2();
			$('#old_nik').show();
			$('#name').show();
			$('#address').show();
			$('#no_whatsapp').show();
			$('#department').show();
			$('#section').show();
			$('#group').show();
			$('#sub_group').show();
			$('#end_date').show();
			$('.select2').select2({
				dropdownParent:$('#form_request')
			});
			$('.select3').select2({
				dropdownParent:$('#form_request')
			});
			$('.select01').select2({
				dropdownParent:$('#form_request')
			});
			$('.select9').select2({
				dropdownParent:$('#new')
			});
			$('.select5').select2({
				dropdownParent:$('#veteran')
			});
			$('.select6').select2({
				dropdownParent:$('#veteran')
			});
			$('.select11').select2({
				dropdownParent:$('#modalRequestMagang')
			});
			$('.select12').select2({
				dropdownParent:$('#modalRequestMagang')
			});
			$('.select13').select2({
				dropdownParent:$('#modalRequestMagang')
			});
			$('#createDepartment').val('{{$emp_dept->department}}').trigger('change');
			$('#select').show();
			$('#btn-new').hide();
			$('#btn-veteran').hide();
			$('#new').hide();
			$('#veteran').hide();
		// $('.numpad').numpad({
		// 	hidePlusMinusButton : true,
		// 	decimalSeparator : '.'
		// });
			$('.select8').select2({
				dropdownParent:$('#veteran')
			});
			$('.select4').select2({
				allowClear:true
			});
			$('#old_nik').show();

			$('#DatePerpanjang').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,
				autoclose: true,
			});

			$('#select_month').datepicker({
				autoclose: true,
				format: "M-yyyy",
				todayHighlight: true,
				startView: "months", 
				minViewMode: "months",
				autoclose: true,
			});
		});

		Highcharts.theme = {
			colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
				'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			chart: {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [
						[0, '#2a2a2b'],
						[1, '#3e3e40']
						]
				},
				style: {
					fontFamily: 'sans-serif'
				},
				plotBorderColor: '#606063'
			},
			title: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				title: {
					style: {
						color: '#A0A0A3'

					}
				}
			},
			yAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				tickWidth: 1,
				title: {
					style: {
						color: '#A0A0A3'
					}
				}
			},
			tooltip: {
				backgroundColor: 'rgba(0, 0, 0, 0.85)',
				style: {
					color: '#F0F0F0'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: 'white'
					},
					marker: {
						lineColor: '#333'
					}
				},
				boxplot: {
					fillColor: '#505053'
				},
				candlestick: {
					lineColor: 'white'
				},
				errorbar: {
					color: 'white'
				}
			},
			legend: {
				itemStyle: {
					color: '#E0E0E3'
				},
				itemHoverStyle: {
					color: '#FFF'
				},
				itemHiddenStyle: {
					color: '#606063'
				}
			},
			credits: {
				style: {
					color: '#666'
				}
			},
			labels: {
				style: {
					color: '#707073'
				}
			},

			drilldown: {
				activeAxisLabelStyle: {
					color: '#F0F0F3'
				},
				activeDataLabelStyle: {
					color: '#F0F0F3'
				}
			},

			navigation: {
				buttonOptions: {
					symbolStroke: '#DDDDDD',
					theme: {
						fill: '#505053'
					}
				}
			},

			rangeSelector: {
				buttonTheme: {
					fill: '#505053',
					stroke: '#000000',
					style: {
						color: '#CCC'
					},
					states: {
						hover: {
							fill: '#707073',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						},
						select: {
							fill: '#000003',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						}
					}
				},
				inputBoxBorderColor: '#505053',
				inputStyle: {
					backgroundColor: '#333',
					color: 'silver'
				},
				labelStyle: {
					color: 'silver'
				}
			},

			navigator: {
				handles: {
					backgroundColor: '#666',
					borderColor: '#AAA'
				},
				outlineColor: '#CCC',
				maskFill: 'rgba(255,255,255,0.1)',
				series: {
					color: '#7798BF',
					lineColor: '#A6C7ED'
				},
				xAxis: {
					gridLineColor: '#505053'
				}
			},

			scrollbar: {
				barBackgroundColor: '#808083',
				barBorderColor: '#808083',
				buttonArrowColor: '#CCC',
				buttonBackgroundColor: '#606063',
				buttonBorderColor: '#606063',
				rifleColor: '#FFF',
				trackBackgroundColor: '#404043',
				trackBorderColor: '#404043'
			},

			legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
			background2: '#505053',
			dataLabelsColor: '#B0B0B3',
			textColor: '#C0C0C0',
			contrastTextColor: '#F0F0F3',
			maskColor: 'rgba(255,255,255,0.3)'
		};
		Highcharts.setOptions(Highcharts.theme);

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-01",
			todayHighlight: true,
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('.datepicker').datepicker({
			<?php $tgl_max = date('d-m-Y') ?>
			autoclose: true,
			format: "dd-mm-yyyy",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		$('#datein').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#DateMagang').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#DateKontrak').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.selectMonth').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			todayHighlight: true,
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		function PilihKeterangan(value){
			if (value == 'DIRECT') {
				$('#pilih_section').show();
			}else{
				$('#pilih_section').hide();
				$('#upload_sect').val(null);
			}
		}

		function SelectNik(value) {
			var dept = $('#createDepartment').val();
			var sec = $('#createSection').val();
			var group = $('#createGroup').val();
			var sub_group = $('#createSubGroup').val();
			if (value.length > 0 ) {
        	  // var  old_nik = $('#description1').val().split('/');

				var old_nik = [];
				for(var i = 1; i <= $('#lop').val(); i++){
					if($('#description'+i).val() != ""){
						old_nik.push($('#description'+i).val().split('/')[0]);
					}
				}

				var data = {
					old_nik:old_nik,
					dept:dept,
					sec:sec,
					group:group,
					sub_group:sub_group
				}

				$.get('{{ url("select/veteran/employee") }}',data, function(result, status, xhr){
					if(result.status){
						$('#old_nik').show();
						$('#name').show();
						$('#address').show();
						$('#no_whatsapp').show();
						$('#department').show();
						$('#section').show();
						$('#group1').show();
						$('#sub_group1').show();
						$('#end_date').show();
						$('#group'+no).show();
						$('#sub_group'+no).show();

						$.each(result.data, function(key, value) {
							$('#name').val(value.name);
							$('#address').val(value.address);
							$('#no_whatsapp').val(value.no_whatsapp);
							$('#department').val(value.department);
							$('#section').val(value.section);
							$('#group1').val(value.group);
							$('#sub_group1').val(value.sub_group);
							$('#end_date').val(value.end_date);
							$('#group'+no).val(value.group);
							$('#sub_group'+no).val(value.sub_group);
						});
					}
					no+1;
				});
			}
        // else{
        //     openErrorGritter('Error!','Data Tidak Ditemukan.');
        // }
		}

		function addZero(i) {
			if (i < 10) {
				i = "0" + i;
			}
			return i;
		}

		function checkValue(id){
			var kuota = $('#kuota').text();
			var male = $('#createMale').val();
			var female = $('#createFemale').val();
			var jumlah = (parseFloat(male)+parseFloat(female));
			var sisa = (kuotas - parseFloat(jumlah));

			if (jumlah > kuotas) {
				window.alert("Max Jumlah Request "+kuotas+", Kurangi Jumlah Request!");
				$('#button_submit').hide();
				$('#createMale').val(0);
				$('#createFemale').val(0);
				$('#kuota').text(kuotas);
			}else{
				$('#button_submit').show();
				$('#kuota').html(sisa);
			}
		}

		function checkDate(value){
			Date.prototype.addDays = function(days) {
				var date = new Date(this.valueOf());
				date.setDate(date.getDate() + days);
				return date;
			}	
			var create_dt = new Date();
			var day_after = create_dt.addDays(21);
			var a = $('#datein').val();
			var select_date = new Date($('#datein').val());
			if (new Date(select_date).getTime() <= new Date(day_after).getTime()) {
				alert('Maksimal kurang dari 3 minggu');
				$('#button_submit').hide();
			}else{
				$('#button_submit').show();
			}
		}

		function ShowModalAll(answer){
    	// console.log(answer);
			$('#showModalAll').modal('show');
			$('#loadingAll').show();
			$('#ModalAllTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Data "+answer+"</span></center>");
			var dept = $('#select_dept').val();
			var data = {
				answer:answer,
				dept:dept
			}

			$.get('{{ url("fetch/data/all") }}', data, function(result, status, xhr) {
				if(result.status){

					$("#loading").hide();
					$('#tableAllBody').html('');

					$('#tableAll').DataTable().clear();
					$('#tableAll').DataTable().destroy();

					var no = 1;
					var resultData = "";
					var total = 0;

					$.each(result.detail, function(key, value) {
						resultData += '<tr>';
						resultData += '<td>'+ no +'</td>';
						resultData += '<td>'+ value.department +'</td>';
						if (value.section == null) {
							resultData += '<td></td>';
						}else{
							resultData += '<td>'+ value.section +'</td>';
						}
						if (value.group == null) {
							resultData += '<td></td>';
						}else{
							resultData += '<td>'+ value.group +'</td>';
						}
						if (value.sub_group == null) {
							resultData += '<td></td>';
						}else{
							resultData += '<td>'+ value.sub_group +'</td>';
						}
						resultData += '<td>'+ value.jumlah +'</td>';
						resultData += '</tr>';
						no += 1;

					});

					$('#tableAllBody').append(resultData);
					$('#loadingAll').hide();
					$('#tableAll').show();
					var table = $('#tableAll').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
							[ 10, 25, 50, -1 ],
							[ '10 rows', '25 rows', '50 rows', 'Show all' ]
							],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
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
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function CreateForm(count, department, section, group, sub_group){
			kuotas = count;

			$('#table_detail').hide();
			$('#modalDetailTitle').hide();
			$('#form_request').show();
			$('#kuota').html(count);
			$('#createDepartment').val(department);
			$('#createSection').val(section);

			if (group == 'null') {
				$('#createGroup').val("");
			}else{
				$('#createGroup').val(group);
			}

			if (sub_group == 'null') {
				$('#createSubGroup').val("");
			}else{
				$('#createSubGroup').val(sub_group);
			}
  //   	$('.numpad').numpad({
		// 	hidePlusMinusButton : true,
		// 	decimalSeparator : '.'
		// });
		}


		function ShowModal(month,answer) {
			clearInterval(intervalChart);
			$('#modalDetail').modal('show');
			$('#loadingDetail').show();
			$('#modalDetailTitle').html("");
			$('#tableDetail').hide();
			$('#table_detail').show();
			$('#form_request').hide();


    	// $("#loading").show();

			var tanggal = $('#tanggal').val();
			var dept = $('#select_dept').val();

			var data = {
				month:month,
				answer:answer,
				tanggal:tanggal,
				dept:dept
			}

			$.get('{{ url("fetch/grafik/detail") }}', data, function(result, status, xhr) {
				if(result.status){

					$("#loading").hide();
					$('#tableDetailBody').html('');

					$('#tableDetail').DataTable().clear();
					$('#tableDetail').DataTable().destroy();

					var no = 1;
					var resultData = "";
					var total = 0;

    			// var date = new Date();
    			// var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    			// var dateString;

    			// date.setDate(date.getDate());

    			// dateString = months[date.getMonth()];

				// var month = new Date('Y-m-d');

					$.each(result.detail, function(key, value) {

					// var count = value.count;

					// if (parseInt(count) > 0) {
					// 	resultData += '<tr>';
					// 	resultData += '<td>'+ no +'</td>';
					// 	resultData += '<td>'+ value.department +'</td>';
					// 	if (value.section == null) {
					// 		resultData += '<td></td>';
					// 	}else{
					// 		resultData += '<td>'+ value.section +'</td>';
					// 	}
					// 	if (value.group == null) {
					// 		resultData += '<td></td>';
					// 	}else{
					// 		resultData += '<td>'+ value.group +'</td>';
					// 	}
					// 	if (value.sub_group == null) {
					// 		resultData += '<td></td>';
					// 	}else{
					// 		resultData += '<td>'+ value.sub_group +'</td>';
					// 	}
					// 	resultData += '<td>'+ value.count +'</td>';
					// 	resultData += '</tr>';
					// 	no += 1;
					// }
						resultData += '<tr>';
						resultData += '<td>'+ no +'</td>';
						// resultData += '<td>'+ value.department +'</td>';
						// if (value.section == null) {
						// 	resultData += '<td></td>';
						// }else{
						// 	resultData += '<td>'+ value.section +'</td>';
						// }
						// if (value.group == null) {
						// 	resultData += '<td></td>';
						// }else{
						// 	resultData += '<td>'+ value.group +'</td>';
						// }
						// if (value.sub_group == null) {
						// 	resultData += '<td></td>';
						// }else{
						// 	resultData += '<td>'+ value.sub_group +'</td>';
						// }
						resultData += '<td>'+ value.section +'</td>';
						resultData += '<td>'+ value.count +'</td>';
						// if (answer == 'Request MP') {
						// 	resultData += '<td><center><button class="btn btn-success btn-sm" onclick="CreateForm(\''+value.count+'\', \''+value.department+'\', \''+value.section+'\', \''+value.group+'\', \''+value.sub_group+'\')"><i class="fa fa-pencil-square-o"></i></button></center></td>';
						// }else{
						// 	resultData += '<td>-</td>';
						// }
						resultData += '</tr>';
						no += 1;

					});


					$('#tableDetailBody').append(resultData);
					$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail "+result.answer+"<br>"+result.month+"</span></center>");

					$('#loadingDetail').hide();
					$('#tableDetail').show();
					var table = $('#tableDetail').DataTable({
					// 'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
							[ 10, 25, 50, -1 ],
							[ '10 rows', '25 rows', '50 rows', 'Show all' ]
							],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				// intervalChart = setInterval(fillChart,60000);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
}

function confirmMagang(){
	$("#loading").show();

	var position = $('#PositionMagang').val();
	var department = $('#DepartmentMagang').val();
	var section = $('#SectionMagang').val();
	var group = $('#GroupMagang').val();
	var sub_group = $('#SubGroupMagang').val();
	var qty_male = $('#qty_male').val();
	var qty_female = $('#qty_female').val();
	var hire_date = $('#DateMagang').val();
	var end_date = $('#DateKontrak').val();

	var data = {
		position:position,
		department:department,
		section:section,
		group:group,
		sub_group:sub_group,
		qty_male:qty_male,
		qty_female:qty_female,
		hire_date:hire_date,
		end_date:end_date
	}
	$.get('{{ url("input/request/magang") }}', data, function(result, status, xhr){
		if(result.status){
			$("#loading").hide();
			ListMonitoring();
			resetMagang();
			$('#modalRequestMagang').modal('hide');
			openSuccessGritter('Success!', result.message);
		}
		else{
			openErrorGritter('Error!',result.message);
		}
	});
}

function confirmRequest(){
	$("#loading").show();

	var position = $('#createPosition').val();
	var department = $('#createDepartment').val();
	var quantity_male = $('#createMale').val();
	var quantity_female = $('#createFemale').val();
	var reason = $('#createReason').val();
	var start_date = $('#datein').val();
	var min_age = $('#createMinAge').val();
	var max_age = $('#createMaxAge').val();			
	var marriage_status = $('#createMarriageStatus').val();
	var domicile = $('#createDomicile').val();
	var work_experience = $('#createWorkExperience').val();
	var education_level = $('#createEducationLevel').val();
	var major = $('#createMajor').val();
	var note = $('#createNote').val();
		// var new_employee = $('#new_employee').val();
		// var new_employee = $("input[name='new_employee']:checked");
	var new_employee = '';
	$("input[name='new_employee']:checked").each(function (i) {
		new_employee = $(this).val();
	});
	var create_section = $('#createSection').val();
	var create_group = $('#createGroup').val();
	var create_sub_group = $('#createSubGroup').val();

	var skill = "";
	var requirement = "";
	var status_at = "";

	if(skills.length > 0){
		$.each(skills, function(key, value){
			if($('#skill_'+value).val() != ""){
				skill += $('#skill_'+value).val();
				skill += ";";					
			}
		});
	}

	if(requirements.length > 0){
		$.each(requirements, function(key, value){
			if($('#requirement_'+value).val() != ""){
				requirement += $('#requirement_'+value).val();
				requirement += ";";
			}
		});
	}

	var employee = [];
	for(var i = 1; i <= $('#lop').val(); i++){
		if($('#description'+i).val() != ""){
			employee.push($('#description'+i).val());
		}
	}

	var group = [];
	for(var i = 1; i <= $('#lop').val(); i++){
		if($('#group'+i).val() != ""){
			group.push($('#group'+i).val());
		}
	}

	var sub_group = [];
	for(var i = 1; i <= $('#lop').val(); i++){
		if($('#sub_group'+i).val() != ""){
			sub_group.push($('#sub_group'+i).val());
		}
	}

		// var penempatan = [];
		// for(var i = 1; i <= $('#lop').val(); i++){
		// 	if($('#penempatan'+i).val() != ""){
		// 		penempatan.push($('#penempatan'+i).val());
		// 	}
		// }

		// var prc_penempatan = [];
		// for(var i = 1; i <= $('#lop').val(); i++){
		// 	if($('#process'+i).val() != ""){
		// 		prc_penempatan.push($('#process'+i).val());
		// 	}
		// }

	var durasi = [];
	for(var i = 1; i <= $('#lop').val(); i++){
		if($('#durasi'+i).val() != ""){
			durasi.push($('#durasi'+i).val());
		}
	}

	var data = {
		position:position,
		department:department,
		quantity_male:quantity_male,
		quantity_female:quantity_female,
		reason:reason,
		start_date:start_date,
		min_age:min_age,
		max_age:max_age,
		marriage_status:marriage_status,
		domicile:domicile,
		work_experience:work_experience,
		education_level:education_level,
		major:major,
		note:note,
		skill:skill,
		requirement:requirement,
		status_at:status_at,
		employee:employee,
			// penempatan:penempatan,
		durasi:durasi,
		new_employee:new_employee,
		create_section:create_section,
		create_group:create_group,
		create_sub_group:create_sub_group,
			// prc_penempatan:prc_penempatan,
		group:group,
		sub_group:sub_group
			// sect_penempatan:sect_penempatan
	}

	$.get('{{ url("input/hr/request_manpower") }}', data, function(result, status, xhr){
		if(result.status){
			$("#loading").hide();
			ListMonitoring();
			fillChart();
			reset();
				// window.location.reload();
			$('#modalDetail').modal('hide');
			openSuccessGritter('Success!', result.message);
		}
		else{
			openErrorGritter('Error!',result.message);
		}
	});
}

function resetMagang(){
	$('#SectionMagang').val("").trigger('change');
	$('#GroupMagang').val("").trigger('change');
	$('#SubGroupMagang').val("").trigger('change');
	$('#qty_male').val(0);
	$('#qty_female').val(0);
	$('#DateMagang').val("");
	$('#DateKontrak').val("");
}

function reset(){
	$('#createPosition').val("").trigger('change');
		// $('#createDepartment').val("").trigger('change');
	$('#createMale').val(0);
	$('#createFemale').val(0);
	$('#createReason').val("");
		// $('#createStartDate').val("");
	$('#datein').val("");
	$('#createMinAge').val(0);
	$('#createMaxAge').val(0);
	$('#createMarriageStatus').val("").trigger('change');
	$('#createEducationLevel').val("").trigger('change');
	$('#createDomicile').val("");
	$('#createWorkExperience').val(0);
	$('#createMajor').val("");
	$('#createNote').val("");
	$("input[name='new_employee']").each(function (i) {
		$('#new_employee')[i].checked = false;
	});
		// $('#createDepartment').val('{{$emp_dept->department}}').trigger('change');
	$('#select').show();
	$('#btn-new').hide();
	$('#btn-veteran').hide();
	$('#new').hide();
	$('#veteran').hide();
		// $('#loc_penempatan').val("").trigger('change');
		// $('#sect_penempatan').val("").trigger('change');
	$('#process_penempatan').val("");
	$('#process1').val("");
	$('#durasi').val("").trigger('change');
}

function tambah(id,lop) {
	var id = id;
	var lop = "";
	if (id == "tambah"){
		lop = "lop";
	}else{
		lop = "lop2";
	}
		// var divdata = $("<input type='text' name='lop' id='lop' value='"+no+"' hidden><div id='"+no+"' class='col-md-12' style='padding: 0; padding-top: 5px'><div class='col-xs-3' style='padding:0;'><select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' style='width: 100%'></select></div><div class='col-xs-3' style='padding:0; padding-left: 5px'><select class='form-control select8' id='penempatan"+no+"' name='penempatan"+no+"' data-placeholder='Penempatan' style='width: 100%' required></select></div><div class='col-xs-3' style='padding:0; padding-left: 5px'><input type='text' class='form-control' name='process"+no+"' id='process"+no+"' placeholder='Process' required></div><div class='col-xs-1' style='padding:0; padding-left: 5px'><input type='text' class='form-control' placeholder='/Bulan' name='durasi"+no+"' id='durasi"+no+"''></div><div class='col-xs-2' style='padding:0; padding-left: 5px'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i></button>&nbsp;<button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

	var divdata = $("<input type='text' name='lop' id='lop' value='"+no+"' hidden><div id='"+no+"' class='col-md-12' style='padding: 0; padding-top: 5px'><div class='col-xs-3' style='padding:0;'><select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' onchange='SelectNik(this.value)' style='width: 100%' required></select><div class='col-xs-1' style='padding:0; padding-left: 5px'><select class='form-control select7' name='durasi"+no+"' id='durasi"+no+"' style='width: 100%; height: 100px;'><option value='0'>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option></select></div><div class='col-xs-2' style='padding:0; padding-left: 5px'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i></button>&nbsp;<button class='btn btn-success' type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

	$("#"+id).append(divdata);

	$('#description'+no+'').append(emps);
	$('#description'+no+'').val('').trigger('change');
	$('#penempatan'+no+'').append(sec);
	$('#penempatan'+no+'').val('').trigger('change');

	$('#lop').val(no);
	$('.select7').select2({
		dropdownParent:$('#veteran')
	});
	$('.select8').select2({
		dropdownParent:$('#veteran')
	});
	$('.select5').select2({
		dropdownParent:$('#veteran')
	});
	no+=1;
}

function kurang(elem,lop) {
	var lop = lop;
	var ids = $(elem).parent('div').parent('div').attr('id');
	var oldid = ids;
	$(elem).parent('div').parent('div').remove();
	var newid = parseInt(ids) + 1;
	jQuery("#"+newid).attr("id",oldid);
	jQuery("#description"+newid).attr("name","description"+oldid);

	jQuery("#description"+newid).attr("id","description"+oldid);
	no-=1;
	var a = no -1;

	for (var i =  ids; i <= a; i++) { 
		var newid = parseInt(i) + 1;
		var oldid = newid - 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);
		jQuery("#description"+newid).attr("id","description"+oldid);
	}
	document.getElementById(lop).value = a;
}

function SelectEmployee(value) {
	var dept = $('#createDepartment').val();
	var sec = $('#createSection').val();
	var group = $('#createGroup').val();
	var sub_group = $('#createSubGroup').val();

	var data = {
		dept:dept,
		sec:sec,
		group:group,
		sub_group:sub_group
	}
	$.get('{{ url("select/veteran/employee") }}',data, function(result, status, xhr){
		if(result.status){
			$('#description1').show();
			$('#description1').html('');
			emps = '';

			emps += '<option value=""></option>';

			$.each(result.employee, function(key, value) {
				emps += '<option value="'+value.old_nik+'/'+value.name+'">'+value.name+'</option>';
			});

			$('#description'+no).show();
			$('#description'+no).html('');
			emps = '';

			emps += '<option value=""></option>';

			$.each(result.employee, function(key, value) {
				emps += '<option value="'+value.old_nik+'/'+value.name+'">'+value.name+'</option>';
			});
			no+1;

			$('#penempatan1').show();
			$('#penempatan1').html('');
			sec = '';

			sec += '<option value=""></option>';

			$.each(result.sub_grp, function(key, value) {
				sec += '<option value="'+value.sub_group+'">'+value.sub_group+'</option>';
			});

				// $('#sect_penempatan').show();
				// $('#sect_penempatan').html('');
				// sect_penempatan = '';

				// sect_penempatan += '<option value=""></option>';

				// $.each(result.sec, function(key, value) {
				// 	sect_penempatan += '<option value="'+value.section+'">'+value.section+'</option>';
				// });

			$('#createSection').show();
			$('#createSection').html('');
			section = '';

			section += '<option value=""></option>';

			$.each(result.sec, function(key, value) {
				section += '<option value="'+value.section+'">'+value.section+'</option>';
			});

			$('#description1').append(emps);
			$('#description1').val('').trigger('change');
			$('#penempatan1').append(sec);
			$('#penempatan1').val('').trigger('change');
				// $('#sect_penempatan').append(sect_penempatan);
				// $('#sect_penempatan').val('').trigger('change');
			$('#group').val('');
			$('#sub_group').val('');
			$('#durasi').val('').trigger('change');
				// $('#createSection').append(section);
				// $('#createSection').val('').trigger('change');

			po = '';

			po += '<option value=""></option>';

			$.each(result.po, function(key, value) {
				po += '<option value="'+value.diff+'">'+value.diff+'</option>';
			});
			$('#test').html(po);
		}
	});
}

	// function SelectSection(value){
	// 	var data = {
	// 		sect_penempatan:$('#sect_penempatan').val()
	// 	}
	// 	$.get('{{ url("select/section/new") }}',data, function(result, status, xhr){
	// 		if(result.status){
	// 			// $('#loc_penempatan').show();
	// 			// $('#loc_penempatan').html('');
	// 			// group = '';

	// 			// group += '<option value=""></option>';

	// 			// $.each(result.group, function(key, value) {
	// 			// 	group += '<option value="'+value.group+'">'+value.group+'</option>';
	// 			// });

	// 			$('#createGroup').show();
	// 			$('#createGroup').html('');
	// 			a = '';

	// 			a += '<option value=""></option>';

	// 			$.each(result.group, function(key, value) {
	// 				a += '<option value="'+value.group+'">'+value.group+'</option>';
	// 			});

	// 			// $('#loc_penempatan').append(group);
	// 			// $('#loc_penempatan').val('').trigger('change');
	// 			$('#createGroup').append(a);
	// 			$('#createGroup').val('').trigger('change');
	// 		}
	// 	});
	// }

function CreateSection(value){
	var data = {
		dpt:$('#DepartmentMagang').val(),
		sect_penempatan:$('#SectionMagang').val()
	}
	$.get('{{ url("select/section/new") }}',data, function(result, status, xhr){
		if(result.status){
			a = '';

			a += '<option value=""></option>';

			$.each(result.group, function(key, value) {
				a += '<option value="'+value.group+'">'+value.group+'</option>';
			});

			$('#GroupMagang').append(a);
			$('#GroupMagang').val('').trigger('change');

			po = '';

			po += '<option value=""></option>';

			$.each(result.po, function(key, value) {
				po += '<option value="'+value.diff+'">'+value.diff+'</option>';
			});
			$('#test').html(po);
		}
	});
}

	// function SelectGroup(value){
	// 	var data = {
	// 		loc_penempatan:$('#loc_penempatan').val()
	// 	}
	// 	$.get('{{ url("select/group/new") }}',data, function(result, status, xhr){
	// 		if(result.status){
	// 			$('#process_penempatan').show();
	// 			$('#process_penempatan').html('');
	// 			sub_group = '';

	// 			sub_group += '<option value=""></option>';

	// 			$.each(result.sub_group, function(key, value) {
	// 				sub_group += '<option value="'+value.sub_group+'">'+value.sub_group+'</option>';
	// 			});

	// 			$('#process_penempatan').append(sub_group);
	// 			$('#process_penempatan').val('').trigger('change');
	// 		}
	// 	});
	// }

function CreateGroup(value){
	var data = {
		dpt:$('#DepartmentMagang').val(),
		sect:$('#SectionMagang').val(),
		loc_penempatan:$('#GroupMagang').val()
	}
	$.get('{{ url("select/group/new") }}',data, function(result, status, xhr){
		if(result.status){
			$('#SubGroupMagang').show();
			$('#SubGroupMagang').html('');
			b = '';

			b += '<option value=""></option>';

			$.each(result.sub_group, function(key, value) {
				b += '<option value="'+value.sub_group+'">'+value.sub_group+'</option>';
			});

			$('#SubGroupMagang').append(b);
			$('#SubGroupMagang').val('').trigger('change');

			po = '';

			po += '<option value=""></option>';

			$.each(result.po, function(key, value) {
				po += '<option value="'+value.diff+'">'+value.diff+'</option>';
			});
			$('#test').html(po);
		}
	});
}

function CreateSubGroup(value){
	var data = {
		dpt:$('#createDepartment').val(),
		sect:$('#createSection').val(),
		loc_penempatan:$('#createGroup').val(),
		sub_group:$('#createSubGroup').val()

	}
	$.get('{{ url("select/sub_group/new") }}',data, function(result, status, xhr){
		if(result.status){
			po = '';

			po += '<option value=""></option>';

			$.each(result.po, function(key, value) {
				po += '<option value="'+value.diff+'">'+value.diff+'</option>';
			});
			$('#test').html(po);
		}
	});
}

function modalRequest(){
	$('#modalDetail').modal('hide');
	$('#modalRequest').modal('show');
}

function VeteranEmployee(){
	$('#select').hide();
	$('#veteran').show();
	$('#new').hide();
	$('#btn-new').show();
	$('#btn-veteran').hide();
}

function NewEmployee(){
	$('#select').hide();
	$('#veteran').hide();
	$('#new').show();
	$('#btn-new').hide();
	$('#btn-veteran').show();
}

function InputMp(){
	$('#modalCreate').modal('show');
	$('#pilih_section').hide();
}

function UploadMPInterview(){
	$('#ModalUploadMPInterview').modal('show');
}

function DetailMPInterview(){
	$('#ModalDetailMPInterview').modal('show');
	$.get('{{ url("fetch/mp/interview") }}', function(result, status, xhr) {

		$('#tableResumeInterview').DataTable().clear();
		$('#tableResumeInterview').DataTable().destroy();
		$("#bodyResumeInterview").empty();
		var body = '';

		$.each(result.resumes, function(index, value){
			body += '<tr>';
			body += '<td>'+(index+1)+'</td>';
			body += '<td>'+value.nik+'</td>';
			body += '<td>'+value.nama+'</td>';
			body += '<td><button type="button" class="btn btn-success btn-xs" onclick="LolosMagang('+value.id+')"><i class="fa fa-check-square-o"></i> Interview OK</button></td>';
			body += '</tr>';
		})

		$("#bodyResumeInterview").append(body);

		var table = $('#tableResumeInterview').DataTable({
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

function LolosMagang(id){
	var data = {
		id : id
	}
	$.post('{{ url("update/mp/magang") }}', data, function(result, status, xhr) {
		if(result.status){
			$("#loading").hide();
			openSuccessGritter('Success!', result.message);
			DetailMPInterview();
			fillChart();
		}else{
			openErrorGritter('Error!', result.message);
		}
	})
}

function StockMp(id){
	var data = {
		id : id
	}
	$.post('{{ url("update/mp/stock") }}', data, function(result, status, xhr) {
		if(result.status){
			$("#loading").hide();
			openSuccessGritter('Success!', result.message);
			DetailMPMagang();
			fillChart();
		}else{
			openErrorGritter('Error!', result.message);
		}
	})
}

function DetailMPMagang(){
	$('#ModalDetailMPMagang').modal('show');
	$.get('{{ url("fetch/mp/magang") }}', function(result, status, xhr) {

		$('#tableResumeMagang').DataTable().clear();
		$('#tableResumeMagang').DataTable().destroy();
		$("#bodyResumeMagang").empty();
		var body = '';

		$.each(result.resumes, function(index, value){
			body += '<tr>';
			body += '<td>'+(index+1)+'</td>';
			body += '<td>'+value.nik+'</td>';
			body += '<td>'+value.nama+'</td>';
			body += '<td><button type="button" class="btn btn-success btn-xs" onclick="StockMp('+value.id+')"><i class="fa fa-check-square-o"></i> Calon Karyawan OK</button></td>';
			body += '</tr>';
		})

		$("#bodyResumeMagang").append(body);

		var table = $('#tableResumeMagang').DataTable({
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

function DetailMPStock(){
	$('#ModalDetailMPStock').modal('show');
	$.get('{{ url("fetch/mp/stock") }}', function(result, status, xhr) {

		$('#tableResumeStock').DataTable().clear();
		$('#tableResumeStock').DataTable().destroy();
		$("#bodyResumeStock").empty();
		var body = '';

		$.each(result.resumes, function(index, value){
			body += '<tr>';
			body += '<td>'+(index+1)+'</td>';
			body += '<td>'+value.nik+'</td>';
			body += '<td>'+value.nama+'</td>';
			body += '<td><button type="button" class="btn btn-success btn-xs" onclick="StockMp('+value.id+')"><i class="fa fa-check-square-o"></i> Interview OK</button></td>';
			body += '</tr>';
		})

		$("#bodyResumeStock").append(body);

		var table = $('#tableResumeStock').DataTable({
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

function RequestMagang(){
	$('#modalRequestMagang').modal('show');
	$('#DepartmentMagang').val('{{$emp_dept->department}}');
}

function Test(){
	$.get('{{ url("test/data/employee") }}', function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success!', result.message);
		}
		else{
			openErrorGritter('Error!',result.message);
		}
	});
}

function UploadMp(){
	$('#loading').show();
	if($('#upload_file').val() == ""){
		openErrorGritter('Error!', 'Gagal Upload, Masukkan File !');
		audio_error.play();
		$('#loading').hide();
		return false;	
	}

	var formData = new FormData();
	var newAttachment  = $('#upload_file').prop('files')[0];
	var file = $('#upload_file').val().replace(/C:\\fakepath\\/i, '').split(".");

	var section = $('#upload_sect').val();

	formData.append('newAttachment', newAttachment);
	formData.append('section', section);
	formData.append('bulan', $("#bulan").val());

	formData.append('extension', file[1]);
	formData.append('file_name', file[0]);

	$.ajax({
		url:"{{ url('upload/kebutuhan/mp') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				openSuccessGritter('Success!',data.message);
				audio_ok.play();
				$('#bulan').val("");
				$('#keterangan').val("");
				$('#upload_sect').val("");
				$('#upload_file').val("");
				$('#modalCreate').modal('hide');
				$('#loading').hide();
				fillChart();
			}else{
				openErrorGritter('Error!',data.message);
				audio_error.play();
				$('#loading').hide();
			}

		}
	});
}

function InsertMpInterview(){
	$('#loading').show();

	var formData = new FormData();
	var newAttachment  = $('#upload_interview').prop('files')[0];
	var file = $('#upload_interview').val().replace(/C:\\fakepath\\/i, '').split(".");
	formData.append('newAttachment', newAttachment);
	formData.append('extension', file[1]);
	formData.append('file_name', file[0]);

	$.ajax({
		url:"{{ url('upload/mp/interview') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				$('#loading').hide();
				openSuccessGritter('Success!',data.message);
				audio_ok.play();
				$('#ModalUploadMPInterview').modal('hide');
				fillChart();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',data.message);
				audio_error.play();
			}

		}
	});
}

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate() {
	var d = new Date();
	var day = addZero(d.getDate());
	var month = addZero(d.getMonth()+1);
	var year = addZero(d.getFullYear());
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
}

function CobaSelect(){
	openSuccessGritter('Success!');
}

function fillChart() {
	$("#loading").show();
	var dept = $('#select_dept').val();
	var select_bulan = $('#select_month').val();
	var data = {
		dept:dept,
		select_bulan:select_bulan
	}

		// var tanggal = $('#tanggal').val();

		// var data = {
		// 	tanggal:tanggal
		// }

		// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

	$.get('{{ url("fetch/user/grafik") }}', data,function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){
				$("#loading").hide();

				var department = [];
				var process_sign = [];
				var done_process_sign = [];
				var month = [];
				var forecasts = [];
				var mp_aktual = [];
				var request_produksi = [];
				var diterima_hr = [];
				var hr_recruitment = [];
				var request = [];
				var processt = [];

				var bulan = [];
				var aktual = [];
				var forecast = [];
				var kontrak = [];
				var selisih = [];
				var up_minus = [];
				var id_bulan = [];

				var mp_actual = [];
				var mp_direct = [];
				var mp_indirect = [];
				var mp_staff = [];

				var total = [];

				var mp_interview = [];
				var mp_magang = [];
				var mp_stock = [];

				var kebutuhan = [];

				var data_habis_kontrak = [];

				$.each(result.grafik, function(key, value) {
					department.push(value.department);
					month.push(value.month);
					forecasts.push(parseInt(value.forecasts));
					mp_aktual.push(parseInt(value.mp_aktual));
					request_produksi.push(parseInt(value.request_produksi));
					diterima_hr.push(parseInt(value.diterima_hr));
					hr_recruitment.push(parseInt(value.hr_recruitment));
					request.push(parseInt(value.request));
					processt.push(parseInt(value.processt));

					process_sign.push(parseInt(value.process_sign));
					done_process_sign.push(parseInt(value.done_process_sign));

					aktual.push(parseInt(value.aktual-value.kontrak));
					forecast.push(parseInt(value.forecast));
					kontrak.push(parseInt(value.kontrak));
					selisih.push(parseInt(value.selisih));
					up_minus.push(parseInt(value.up_minus));
					mp_actual.push(parseInt(result.mp_actual));
					mp_direct.push((parseInt(result.mp_direct))-(parseInt(result.planing_mp_direct)));
					mp_indirect.push(((parseInt(result.mp_indirect))-(parseInt(result.planing_mp_indirect)))+((parseInt(result.mp_staff))-(parseInt(result.planing_mp_staff))));
					mp_staff.push((parseInt(result.mp_staff))-(parseInt(result.planing_mp_staff)));
					kebutuhan.push(((parseInt(value.forecast))-((parseInt(mp_direct))+(parseInt(mp_indirect))))-(mp_stock.length));
					total.push(parseInt(result.mp_direct)+parseInt(result.mp_indirect)+parseInt(result.mp_pl));
					data_habis_kontrak.push((parseInt(result.planing_mp_direct))+(parseInt(result.planing_mp_indirect))+(parseInt(result.planing_mp_staff)));
					bulan.push((value.bulan));

				});

				var bln_sekarang = [];
				var categori = [];
				var categori2 = [];
				var series_end_contract = [];
				var series_forecast = [];
				var series_actual = [];
				var test = [];
				var kebutuhan = [];
				var regressions_total = [1,2,3,4,5,3];
				var kenaikan = [];
				var series_p = [];


				$.each(result.emp_end, function(key, value){
					var date_sekarang = new Date();
					var nama_bulan_sekarang = date_sekarang.getMonth();
					var tahun_sekarang = date_sekarang.getFullYear();

					var date = new Date(value.bulan);
					var nama_bulan = date.getMonth();
					var tahun = date.getFullYear();
					var mp_aktual_sekarang = result.emp_aktual[0].jumlah;
					switch(nama_bulan) {
					case 0: nama_bulan = "Jan"; break;
					case 1: nama_bulan = "Feb"; break;
					case 2: nama_bulan = "Mar"; break;
					case 3: nama_bulan = "Apr"; break;
					case 4: nama_bulan = "May"; break;
					case 5: nama_bulan = "Jun"; break;
					case 6: nama_bulan = "Jul"; break;
					case 7: nama_bulan = "Aug"; break;
					case 8: nama_bulan = "Sep"; break;
					case 9: nama_bulan = "Oct"; break;
					case 10: nama_bulan = "Nov"; break;
					case 11: nama_bulan = "Dec"; break;
					}

					switch(nama_bulan_sekarang) {
					case 0: nama_bulan_sekarang = "Jan"; break;
					case 1: nama_bulan_sekarang = "Feb"; break;
					case 2: nama_bulan_sekarang = "Mar"; break;
					case 3: nama_bulan_sekarang = "Apr"; break;
					case 4: nama_bulan_sekarang = "May"; break;
					case 5: nama_bulan_sekarang = "Jun"; break;
					case 6: nama_bulan_sekarang = "Jul"; break;
					case 7: nama_bulan_sekarang = "Aug"; break;
					case 8: nama_bulan_sekarang = "Sep"; break;
					case 9: nama_bulan_sekarang = "Oct"; break;
					case 10: nama_bulan_sekarang = "Nov"; break;
					case 11: nama_bulan_sekarang = "Dec"; break;
					}
					var isi = 0;
					var isi_p = 0;
					categori.push(nama_bulan+'-'+tahun);
					bln_sekarang.push(nama_bulan_sekarang+'-'+tahun_sekarang);
					categori2.push(nama_bulan);
					$.each(result.emp_forecast, function(key2, value2){
						if (value.bulan == value2.bulan) {
							series_forecast.push(parseInt(value2.jumlah));
							series_end_contract.push(parseInt(value.jumlah));
							series_actual.push(mp_aktual_sekarang);
							test.push(series_actual[0]-value.jumlah);
							kebutuhan.push(value2.jumlah-(series_actual[0]-value.jumlah));
							isi = 1;
						}
					});

					if (isi == 0) {
						series_forecast.push(0);
						series_end_contract.push(0);
						series_actual.push(0);
						test.push(0);
						kebutuhan.push(0);
					}

					$.each(result.data_p, function(key2, value2){
						if (value.bulan == value2.bulan) {
							series_p.push(parseInt(value2.jumlah));
							isi_p = 1;
						}
					});
					if (isi_p == 0) {
						series_p.push(0);
					}
				});


				for (var i = 0; i < result.emp_end.length; i++) {
					// var isi2 = ((series_forecast[i+1]-series_forecast[i])/series_forecast[i])*100;
					var isi2 = series_forecast[i];
					kenaikan.push(isi2);
				}

				$('#putus_kontrak').html(categori[0]);
				$('#kebutuhan_bulan').html(categori[0]);
				$('#aktual_sekarang').html(bln_sekarang[0]);

				$('#total_putus_kontrak').html(series_end_contract[0]+"<span style='font-size:2.4vw'> 人</span>");
				$('#total_aktual').html(series_actual[0] +"<span style='font-size:2.4vw'> 人</span>");

				$('#total_direkrut').html(kebutuhan[0]+"<span style='font-size:2.4vw'> 人</span>");

				$.each(result.mp_interview, function(key, value) {
					mp_interview.push(parseInt(value.id));
				});
				$('#total_interview').html(mp_interview.length +"<span style='font-size:2.4vw'> 人</span>");

				$.each(result.mp_magang, function(key, value) {
					mp_magang.push(parseInt(value.id));
				});
				$('#total_magang').html(mp_magang.length +"<span style='font-size:2.4vw'> 人</span>");

				$.each(result.mp_stock, function(key, value) {
					mp_stock.push(parseInt(value.id));
				});
				$('#total_stock').html(mp_stock.length +"<span style='font-size:2.4vw'> 人</span>");

				if (select_bulan == '') {
					id_bulan.push(result.grafik[0].bulan);
					$('#id_bulan').html(id_bulan);
				}else{
					$('#id_bulan').html(select_bulan);
				}

				var contract_1 = [];
				var contract_2 = [];
				var contract_3 = [];
				$.each(result.ct1, function(key, value) {contract_1.push(parseInt(value.jm_ct1 || 0));});
				$.each(result.ct2, function(key, value) {contract_2.push(parseInt(value.jm_ct2 || 0));});
				$.each(result.ct3, function(key, value) {contract_3.push(parseInt(value.jm_ct3 || 0));});

					// $('#contract1').html(contract_1 +"<span style='font-size:2.4vw'> 人</span>");
				$('#contract1').html('10' +"<span style='font-size:2.4vw'> 人</span>");
				$('#contract2').html(contract_2 +"<span style='font-size:2.4vw'> 人</span>");
					// $('#contract2').html('7' +"<span style='font-size:2.4vw'> 人</span>");
					// $('#contract3').html(contract_3 +"<span style='font-size:2.4vw'> 人</span>");
				$('#contract3').html('7' +"<span style='font-size:2.4vw'> 人</span>");

				var induction = [];
				var magang = [];

				$.each(result.induction, function(key, value){induction.push(parseInt(value.induction || 0));});
				$.each(result.magang, function(key, value){magang.push(parseInt((value.magang || 0)));});

					// $('#induction').html(induction +"<span style='font-size:2.4vw'> 人</span>");
				$('#induction').html('10' +"<span style='font-size:2.4vw'> 人</span>");
					// $('#magang').html(magang +"<span style='font-size:2.4vw'> 人</span>");
				$('#magang').html('15' +"<span style='font-size:2.4vw'> 人</span>");

				Highcharts.chart('grafik01', {
					chart: {
						type: 'column',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "Monitoring Forecast, Aktual dan Kebutuhan MP",
						style: {
							fontSize: '30px'
						}
					},
						// subtitle: {
						// 	text: 'test',
						// 	style: {
						// 		fontSize: '1vw',
						// 		fontWeight: 'bold'
						// 	}
						// },
					xAxis: {
						categories:categori,
						plotBands: [{
							from: -0.5,
							to: 0.5,
							color: 'RGBA(235, 14, 62, 0.5)',
							label: {
								style: {
									text: 'Priority',
									color: '#ffffff'
								},
							}
						},
						{
							from: 0.5,
							to: 2.5,
							color: 'RGBA(240, 137, 58, 0.5)',
							label: {
								style: {
									text: 'Stabil',
									color: '#ffffff'
								},
							}
						},
						{
							from: 2.5,
							to: 4.5,
							color: 'RGBA(58, 240, 110, 0.5)',
							label: {
								style: {
									text: 'Stabil',
									color: '#ffffff'
								},
							}
						},
						// {
						// 	from: kebutuhan.length-0.5+3,
						// 	to: kebutuhan.length-0.5+5,
						// 	color: 'RGBA(87, 123, 242, 0.5)',
						// 	label: {
						// 		style: {
						// 			text: 'Stabil',
						// 			color: '#ffffff'
						// 		},
						// 	}
						// }],
						],
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '16px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: {
						title: {
							text: 'JUMLAH MP',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
					},
					legend: {
						enabled:true,
						reversed : true
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							// point: {
							// 	events: {
							// 		click: function (e) {
							// 			showHighlight(this.series.name,this.category,'All');
							// 		}
							// 	}
							// },
							dataLabels: {
								enabled: true,
									// format: '{point.y}',
								style:{
									fontSize: '1vw'
								},
								formatter: function(){
									return (this.y!=0)?this.y:"";
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							stacking: 'normal'
						},
					},
					series: [
					{
						type: 'column',
						data: kebutuhan,
						name: "Kebutuhan",
						colorByPoint: false,
						color: "red",
						animation: false,
						stack:'GG'
					},
					{
						type: 'column',
						data: test,
						name: "Aktual",
						colorByPoint: false,
						color: "#aa6eff",
						animation: false,
						stack:'GG'
					},
					{
						type: 'column',
						data: series_forecast,
						name: "Forecast",
						colorByPoint: false,
						color: "#788cff",
						animation: false,
						stack:'GC'
					}
					]
				});

				Highcharts.chart('grafik02', {
					chart: {
						type: 'column',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "FORECAST MP",
						style: {
							fontSize: '15px'
						}
					},
					xAxis: {
						categories:categori2,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '16px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: 
					{
						title: {
							text: 'JUMLAH MP',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
					},
					legend: {
						enabled:false,
						reversed : true
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							// point: {
							// 	events: {
							// 		click: function (e) {
							// 			showHighlight(this.series.name,this.category,'All');
							// 		}
							// 	}
							// },
							dataLabels: {
								enabled: true,
									// format: '{point.y}',
								style:{
									fontSize: '1vw'
								},
								formatter: function(){
									return (this.y!=0)?this.y:"";
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							stacking: 'normal'
						},
					},
					series: [
					{
						type: 'spline',
						data: kenaikan,
						name: 'Presentasi',
						colorByPoint: false,
						color:'#d62d2d',
						// yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							}
						},
					}
					// ,{
					// 	type: 'line',
					// 	data: kenaikan,
					// 	name: "Trendline",
					// 	colorByPoint: false,
					// 	color: "#fff",
					// 	// yAxis:2,
					// 	animation: false,
					// 	dashStyle:'shortdash',
					// 	lineWidth: 4,
					// 	marker: {
					// 		radius: 4,
					// 		lineColor: '#fff',
					// 		lineWidth: 1
					// 	},
					// },
					]
				});

				Highcharts.chart('grafik03', {
					chart: {
						type: 'column',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "KARYAWAN HABIS KONTRAK",
						style: {
							fontSize: '15px'
						}
					},
					xAxis: {
						categories:categori2,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '16px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: {
						title: {
							text: 'JUMLAH MP',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
					},
					legend: {
						enabled:false,
						reversed : true
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {
										// showHighlight(this.series.name,this.category,'All');
										location.replace('{{ url("index/employee_end_contract") }}');
									}
								}
							},
							dataLabels: {
								enabled: true,
								style:{
									fontSize: '1vw'
								},
								formatter: function(){
									return (this.y!=0)?this.y:"";
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							stacking: 'normal'
						},
					},
					series: [{
						type: 'column',
						data: series_p,
						name: "End Contract",
						colorByPoint: false,
						color: "#f9a825",
						animation: false,
						stack:'GC'
					}]
				});

				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$("#bodyResume").empty();
				var body = '';
				$.each(result.resume_mp, function(index, value){

					var report = '{{ url("data_file/pengisian_ky")}}';

					body += "<tr>";
					body += "<td>"+(index+1)+"</td>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+value.department+"</td>";
					body += "<td>"+value.hire_date+"</td>";
					body += "<td>"+value.planing_end_date+"</td>";
					body += "<td style='height: 10px; padding-bottom: 10px'><button type='button' class='btn btn-success btn-xs' onclick='OpenModalPerpanjang(\""+value.employee_id+"\", \""+value.name+"\", \""+value.planing_end_date+"\")'><i class='fa fa-check-square-o'></i></button></td>";
						// body += "<td style='height: 10px; padding-bottom: 10px'><input type='checkbox' name='update' id='update' class='update' value='"+value.employee_id+"'></td>";
					body += "</tr>";
				})
				$("#bodyResume").append(body);
				var table = $('#tableResume').DataTable({
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

					// if (result.resume_mp.length >= 0) {
					// 	console.log('kirim email');
					// }
			}
		}
	});
}

function PerpanganKontrak(){
	$('#loading').show();
	var update = [];
	$("input[name='update']:checked").each(function (i) {
		update[i] = $(this).val();
	});
	if (update.length == 0) {
		$('#loading').hide();
		openErrorGritter('Error!','Pilih Karyawan Yang Akan Diperpanjang');
		return false;
	}

	var data = {
		update:update,
		date:$('#DatePerpanjang').val()
	}

	$.post('{{ url("update/habis_kontrak") }}', data, function(result, status, xhr) {
		$('#ModalPerpanjang').modal('hide');
		openSuccessGritter('Success','Qty Berhasil Ditambahkan');
		fillChart();
		$('#DatePerpanjang').val("");
	})

	console.log(update);
}

function OpenModalPerpanjang(employee_id, name, planing_end_date){
	// console.log(employee_id);
	$('#ModalPerpanjang').modal('show');
	$('#id_nik').html(employee_id);
	$('#id_nama').html(name);
	$('#id_habis').html(planing_end_date);
}

function PerpanjangKontrak(){
	$('#loading').show();
	// var update = [];
	// $("input[name='update']:checked").each(function (i) {
	// 	update[i] = $(this).val();
	// });
	// if (update.length == 0) {
	// 	$('#loading').hide();
	// 	openErrorGritter('Error!','Pilih Karyawan Yang Akan Diperpanjang');
	// 	return false;
	// }

	var data = {
		// update:update,
		nik:$('#id_nik').html(),
		date:$('#DatePerpanjang').val()
	}
	$.post('{{ url("update/habis_kontrak") }}', data, function(result, status, xhr) {
		$('#ModalPerpanjang').modal('hide');
		openSuccessGritter('Success','Qty Berhasil Ditambahkan');
		fillChart();
		$('#DatePerpanjang').val("");
	})
}

function ListMonitoring(dept){
	var bulan = $('#month').val();
		// console.log(bulan);
		// var dept = $('select_dept').val();
	var data = {
		bulan:bulan,
		dept:dept
	}

	$.get('{{ url("fetch/monitoring/request") }}', data, function(result, status, xhr){
		if(result.status){
			$('#tableListMonitoring').DataTable().clear();
			$('#tableListMonitoring').DataTable().destroy();
			var tableData = '';
			$('#tableBodyListMonitoring').html("");
			$('#tableBodyListMonitoring').empty();
				// fillChart();

			var count = 1;
			$.each(result.list_open, function(key, value) {
				var  appr = value.approval.split(',');
				var  status = value.status.split(',');
				var  approved_at = value.approved_at.split(',');
				var nik = value.approver_id.split(',');

				if (value.position == 'Staff') {
					if (value.remark == 'Menunggu Persetujuan') {
						tableData += '<tr onclick="DetailManPowerRequest(\''+value.request_id+'\')" style="border:1px solid black !important;">';
					}else{
						tableData += '<tr style="border:1px solid black !important;">';
					}
					tableData += '<td>'+ count +'</td>';
					tableData += '<td onclick="DetailManPower(\''+value.request_id+'\')" style="cursor:pointer">'+ value.request_id +'</td>';
					tableData += '<td onclick="DetailManPower(\''+value.request_id+'\')" style="cursor:pointer">'+ value.department +'</td>';
					tableData += '<td>'+ value.created_at +'</td>';
					tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+ value.name +'</td>';
					if (status[0] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[0]+'<br>'+approved_at[0]+'<br>'+status[0]+'</td>';
					}else{
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[0]+'">'+appr[0]+'<br>Waiting</a></td>';
					}

					if (status[1] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[1]+'<br>'+approved_at[1]+'<br>'+status[1]+'</td>';
					}
					else if ((status[0] != '') && (status[1] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[1]+'">'+appr[1]+'<br>Waiting</a></td>';
					}
					else if ((status[0] == '') && (status[1] == '')) {
						tableData += '<td></td>';
					}

					if (status[2] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[2]+'<br>'+approved_at[2]+'<br>'+status[2]+'</td>';
					}
					else if ((status[1] != '') && (status[2] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[2]+'<br>Waiting</a></td>';
					}
					else if ((status[1] == '') && (status[2] == '')) {
						tableData += '<td></td>';
					}
					else if (status[2] == 'none') {
						tableData += '<td></td>';
					}

					if (status[3] == 'Mengetahui') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[3]+'<br>'+status[3]+'</td>';
					}
					else if (status[3] == '-') {
						tableData += '<td></td>'	
					}
					else if ((status[1] == '') && (status[3] == '')) {
						tableData += '<td></td>';
					}


					if (status[4] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[4]+'<br>'+approved_at[4]+'<br>'+status[4]+'</td>';
					}
					else if ((status[3] != '') && (status[4] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[4]+'">'+appr[4]+'<br>Waiting</a></td>';
					}
					else if ((status[3] == '') && (status[4] == '')) {
						tableData += '<td></td>';
					}

					if (status[5] == 'Mengetahui') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[5]+'<br>'+status[5]+'</td>';
					}
					else if ((status[4] != '') && (status[5] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[5]+'<br>Waiting</a></td>';
					}
					else if ((status[4] == '') && (status[5] == '')) {
						tableData += '<td></td>';
					}
					
					if (status[5] == 'Mengetahui') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">Diterima HR</td>';
					}
					else if ((status[4] == '') && (status[5] == '')) {
						tableData += '<td></td>';
					}
					tableData += '<td>'+ value.status_at +'</td>';
				}else{
					tableData += '<tr style="border:1px solid black !important;">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td onclick="DetailManPower(\''+value.request_id+'\')" style="cursor:pointer">'+ value.request_id +'</td>';
					tableData += '<td onclick="DetailManPower(\''+value.request_id+'\')" style="cursor:pointer">'+ value.department +'</td>';
					tableData += '<td>'+ value.created_at +'</td>';
					tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+ value.name +'</td>';
					if (status[0] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[0]+'<br>'+approved_at[0]+'<br>'+status[0]+'</td>';
					}else{
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[0]+'">'+appr[0]+'<br>Waiting</a></td>';
					}
					if (status[1] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[1]+'<br>'+approved_at[1]+'<br>'+status[1]+'</td>';
					}
					else if ((status[0] != '') && (status[1] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[1]+'">'+appr[1]+'<br>Waiting</a></td>';
					}
					else if ((status[0] == '') && (status[1] == '')) {
						tableData += '<td></td>';
					}
					if (status[2] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[2]+'<br>'+approved_at[2]+'<br>'+status[2]+'</td>';
					}
					else if ((status[1] != '') && (status[2] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[2]+'<br>Waiting</a></td>';
					}
					else if ((status[1] == '') && (status[2] == '')) {
						tableData += '<td></td>';
					}
					else if (status[2] == 'none') {
						tableData += '<td></td>';
					}
					tableData += '<td></td>';
					if (status[3] == 'Approved') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[3]+'<br>'+approved_at[3]+'<br>'+status[3]+'</td>';
					}
					else if ((status[1] != '') && (status[3] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('recruitment/approval/sign/') }}/'+value.request_id+'/'+nik[3]+'">'+appr[3]+'<br>Waiting</a></td>';
					}
					else if ((status[1] == '') && (status[3] == '')) {
						tableData += '<td></td>';
					}
					if (status[4] == 'Mengetahui') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[4]+'<br>'+status[4]+'</td>';
					}
					else if ((status[3] != '') && (status[4] == '')) {
						tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[4]+'<br>Waiting</a></td>';
					}
					else if ((status[3] == '') && (status[4] == '')) {
						tableData += '<td></td>';
					}
					if (status[4] == 'Mengetahui') {
						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">Diterima HR</td>';
					}
					else if ((status[3] == '') && (status[4] == '')) {
						tableData += '<td></td>';
					}
					tableData += '<td>'+ value.status_at +'</td>';
				}
				tableData += '</tr>';
				count += 1;
			});
$('#tableBodyListMonitoring').append(tableData);

$('#tableListMonitoringMagang').DataTable().clear();
$('#tableListMonitoringMagang').DataTable().destroy();
var tableData = '';
$('#tableBodyListMonitoringMagang').html("");
$('#tableBodyListMonitoringMagang').empty();
var no = 1;

$.each(result.list_magang, function(key, value) {
	var  approver = value.approval.split(',');
	var  approver_status = value.status.split(',');
	var  approver_at = value.approved_at.split(',');
	var  approver_nik = value.approver_id.split(','); 

	tableData += '<tr>';
	tableData += '<td>'+ no +'</td>';
	tableData += '<td onclick="DetailRequestMagang(\''+value.request_id+'\')" style="cursor:pointer">'+ value.request_id +'</td>';
	tableData += '<td onclick="DetailRequestMagang(\''+value.request_id+'\')" style="cursor:pointer">'+ value.department +'</td>';
	tableData += '<td>'+ value.created_at +'</td>';
	if (approver_status[0] == 'Approved') {
		tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[0]+'<br>'+approver_at[0]+'<br>'+approver_status[0]+'</td>';
	}else{
		tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('request/magang/approval/sign/') }}/'+value.request_id+'/'+approver_nik[0]+'">'+approver[0]+'<br>Waiting</a></td>';
	}
	if (approver_status[1] == 'Approved') {
		tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[1]+'<br>'+approver_at[1]+'<br>'+approver_status[1]+'</td>';
	}
	else if ((approver_status[0] != '') && (approver_status[1] == '')) {
		tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('request/magang/approval/sign/') }}/'+value.request_id+'/'+approver_nik[1]+'">'+approver[1]+'<br>Waiting</a></td>';
	}
	else if ((approver_status[0] == '') && (approver_status[1] == '')) {
		tableData += '<td></td>';
	}
	if (approver_status[2] == 'Mengetahui') {
		tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[2]+'<br>'+approver_status[2]+'</td>';
	}
	else if ((approver_status[1] != '') && (approver_status[2] == '')) {
		tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[2]+'<br>Waiting</a></td>';
	}
	else if ((approver_status[1] == '') && (approver_status[2] == '')) {
		tableData += '<td></td>';
	}
	else if (approver_at[2] != null) {
		tableData += '<td></td>';
	}
	if (approver_status[3] == 'Mengetahui') {
		tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[3]+'<br>'+approver_status[3]+'</td>';
	}
	else if ((approver_status[2] != '') && (approver_status[3] == '')) {
		tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+approver[3]+'<br>Waiting</a></td>';
	}
	else if ((approver_status[2] == '') && (approver_status[3] == '')) {
		tableData += '<td></td>';
	}
	else if (approver_at[3] != null) {
		tableData += '<td></td>';
	}
	tableData += '<td>'+value.st+'</td>';
	tableData += '</tr>';
	no += 1;
});
$('#tableBodyListMonitoringMagang').append(tableData);
}
else{
	openErrorGritter('Error!', result.message);
}
});
}

function ModalDetailProcess(){
	$('#ModalDetailProcess').modal('show');
}
function ModalDetailMagang(){
	$('#ModalDetailMagang').modal('show');
}
function ModalDetailRequest(){
	$('#ModalDetailRequest').modal('show');
}

function DetailManPower(req_id){
	var data = {
		req_id:req_id
	};
	$.get('<?php echo e(url("human_resource/resume_request")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#tableDetailRequest').DataTable().clear();
			$('#tableDetailRequest').DataTable().destroy();
			var tableData = '';
			$('#tableBodyDetailRequest').html("");
			$('#tableBodyDetailRequest').empty();

			ModalDetailProcess();

			var remark = "";
			$.each(result.rekrut, function(key, value) {
				var  appr = value.approval.split(',');
				var  status = value.status.split(',');
				remark = value.remark;
				tableData += '<tr>';
				tableData += '<td>';
				if (status[0] == 'Approved') {
					tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black; font-size: 13px">'+appr[0]+'</span>';
				}else{
					tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black; font-size: 13px">'+appr[0]+'</span>';
				}
				tableData += '</td>';
				tableData += '<td>';
				if (status[1] == 'Approved') {
					tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black; font-size: 13px">'+appr[1]+'</span>';
				}else{
					tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black; font-size: 13px">'+appr[1]+'</span>';
				}
				tableData += '</td>';
				tableData += '<td>';
				if (status[2] == 'Approved') {
					tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black; font-size: 13px">'+appr[2]+'</span>';
				}else{
					tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black; font-size: 13px">'+appr[2]+'</span>';
				}
				tableData += '</td>';
				tableData += '<td>';
				$('#req_id').val(value.request_id);
			});

			$('#tableBodyDetailRequest').append(tableData);

			$('#ListDetail').DataTable().clear();
			$('#ListDetail').DataTable().destroy();
			var tableData = '';
			$('#BodyListDetail').html("");
			$('#BodyListDetail').empty();

			$.each(result.detail_rekrut, function(key, value) {
				tableData += '<tr><td>Jumlah Pria</td><td>'+value.quantity_male+'</td></tr>';
				tableData += '<tr><td>Jumlah Perempuan</td><td>'+value.quantity_female+'</td></tr>';
				tableData += '<tr><td>Status Karyawan</td><td>'+value.employment_status+'</td></tr>';
				tableData += '<tr><td>Tanggal Masuk</td><td>'+value.start_date+'</td></tr>';
				tableData += '<tr><td>Section</td><td>'+value.section+'</td></tr>';
				tableData += '<tr><td>Group</td><td>'+value.group+'</td></tr>';
				tableData += '<tr><td>Sub Group</td><td>'+value.sub_group+'</td></tr>';
				tableData += '<tr><td>Status Request</td><td>'+value.status_req+'</td></tr>';
				tableData += '<tr><td>Alasan</td><td>'+value.reason+'</td></tr>';
				$('#req_id').val(value.request_id);
			});

			$('#BodyListDetail').append(tableData);
			$('#req_id').val(req_id);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function DetailRequestMagang(req_id){
	var data = {
		req_id:req_id
	};
	$.get('<?php echo e(url("human_resource/resume_request")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#ListDetailMagang').DataTable().clear();
			$('#ListDetailMagang').DataTable().destroy();
			var tableData = '';
			$('#BodyListDetailMagang').html("");
			$('#BodyListDetailMagang').empty();
			ModalDetailMagang();

			$.each(result.detail_magang, function(key, value) {
				tableData += '<tr><td>Jumlah Pria</td><td>'+value.qty_male+'</td></tr>';
				tableData += '<tr><td>Jumlah Perempuan</td><td>'+value.qty_female+'</td></tr>';
				tableData += '<tr><td>Section</td><td>'+value.section+'</td></tr>';
				tableData += '<tr><td>Group</td><td>'+value.group+'</td></tr>';
				tableData += '<tr><td>Sub Group</td><td>'+value.sub_group+'</td></tr>';
				tableData += '<tr><td>Tanggal Masuk Magang</td><td>'+value.hire_date+'</td></tr>';
				tableData += '<tr><td>Tanggal Masuk Kontrak</td><td>'+value.end_date+'</td></tr>';
				$('#req_id').val(value.request_id);
			});

			$('#BodyListDetailMagang').append(tableData);
			$('#req_id').val(req_id);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function DetailManPowerRequest(req_id){
	var data = {
		req_id:req_id
	};
	$.get('<?php echo e(url("human_resource/resume_request")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#RequestDetail').DataTable().clear();
			$('#RequestDetail').DataTable().destroy();
			var tableData = '';
			$('#RequestBodyDetail').html("");
			$('#RequestBodyDetail').empty();

			ModalDetailRequest();

			$.each(result.detail_rekrut, function(key, value) {
				tableData += '<tr><td>Jumlah Pria</td><td>'+value.quantity_male+'</td></tr>';
				tableData += '<tr><td>Jumlah Perempuan</td><td>'+value.quantity_female+'</td></tr>';
				tableData += '<tr><td>Posisi</td><td>'+value.position+'</td></tr>';
				tableData += '<tr><td>Status Karyawan</td><td>'+value.employment_status+'</td></tr>';
				tableData += '<tr><td>Tanggal Masuk</td><td>'+value.start_date+'</td></tr>';
				tableData += '<tr><td>Status Request</td><td>'+value.status_req+'</td></tr>';
				tableData += '<tr><td>Alasan</td><td>'+value.reason+'</td></tr>';
				$('#req_id').val(value.request_id);
			});

			$('#RequestBodyDetail').append(tableData);
			$('#karyawan_lama').hide();

			var data = result.detail_rekrut;

			if (data[0].status_req == 'Request Veteran Employee') {
				$('#karyawan_lama').show();
				$('#DetailKaryawan').DataTable().clear();
				$('#DetailKaryawan').DataTable().destroy();
				var tableData = '';
				$('#BodyDetailKaryawan').html("");
				$('#BodyDetailKaryawan').empty();
				var no = 1;

				$.each(result.request_karyawan, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ no +'</td>';
					tableData += '<td>'+ value.nik +'</td>';
					tableData += '<td>'+ value.nama +'</td>';
					tableData += '<td>'+ value.department +'</td>';
					tableData += '<td>'+ value.group +'</td>';
					tableData += '<td>'+ value.sub_group +'</td>';
					tableData += '</tr>';
					no += 1;
				});
				$('#BodyDetailKaryawan').append(tableData);
			}
			$('#req_id').val(req_id);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function addRequirement(){
	var requirement = "";
	requirement_count += 1;

	requirement += '<div class="form-group">';
	requirement += '<input type="text" class="form-control" id="requirement_'+requirement_count+'">';
	requirement += '<div>';

	requirements.push(requirement_count);
	$('#requirement').append(requirement);
}

function addSkill(){
	var skillData = "";
	skill_count += 1;

	skillData += '<div class="form-group">';
	skillData += '<input type="text" class="form-control" id="skill_'+skill_count+'">';
	skillData += '<div>';

	skills.push(skill_count);
	$('#skill').append(skillData);
}

function truncate(str, n){
	return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
};

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '2000'
	});
}

function ClearAll(){
	$('#old_nik').val('').trigger('change');
	$('#name').val('');
	$('#address').val('');
	$('#no_whatsapp').val('');
	$('#department').val('');
	$('#section').val('');
	$('#group').val('');
	$('#sub_group').val('');
	$('#proces').val('');
	$('#end_date').val('');
	$('#remark').val('');
}

function TestEmail(){
	// $.post('{{ url("test/data/employee") }}', function(result, status, xhr) {
	$.post('{{ url("insert/employee/end_date") }}', function(result, status, xhr) {
		if(result.status){
			$("#loading").hide();
			openSuccessGritter('Success!', result.message);
			fillChart();
		}else{
			openErrorGritter('Error!', result.message);
		}
	})
}

function DetailEmpPutus(){
	$('#ModalDetailPutusKontrak').modal('show');
	$("#loading").show();

	var data = {
		kode:'Cek Data Sunfish',
		jenis:'Data Grafik',
		month_end_sunfish: '2023-01'
	}

	$.get('{{ url("fetch/employee_end_contract") }}', data, function(result, status, xhr){
		if(result.status){
			$("#loading").hide();
			openSuccessGritter('Success', 'Success !');
			$('#tableResumePutusKontrak').DataTable().clear();
			$('#tableResumePutusKontrak').DataTable().destroy();
			$('#bodyResumePutusKontrak').html("");
			var tableData = "";
			var index = 1;
			$.each(result.data, function(key, value) {

				tableData += '<tr>';
				tableData += '<td style="text-align: center">'+ index++ +'</td>';
				tableData += '<td>'+ value.Emp_no +'</td>';
				tableData += '<td>'+ value.Full_name +'</td>';
				tableData += '<td>'+ value.Department +'</td>';
				tableData += '</tr>';

			});
			$('#bodyResumePutusKontrak').append(tableData);

			var table = $('#tableResumePutusKontrak').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
				'buttons': {
					buttons:[{
						extend: 'pageLength',
						className: 'btn btn-default',
					},
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					}]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
				'DataListing': true	,
				'ordering': true,
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
			alert('Attempt to retrieve data failed');
		}
	});

}

</script>
@endsection