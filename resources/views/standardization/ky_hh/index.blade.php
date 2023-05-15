@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered {
		border: 1px solid black;
	}

	table.table-bordered>thead>tr>th {
		border: 1px solid black;
		vertical-align: middle;
		text-align: center;
	}

	table.table-bordered>tbody>tr>td {
		border: 1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
		text-align: center;
	}

	table.table-bordered>tfoot>tr>th {
		border: 1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label,
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	.nav-tabs-custom>ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}

	.nav-tabs-custom>ul.nav.nav-tabs>li {
		float: none;
		display: table-cell;
	}

	.nav-tabs-custom>ul.nav.nav-tabs>li>a {
		text-align: center;
	}

	#loading, #error {
		display: none;
	}

	p.tengah {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
</style>

@section('header')
<section class="content-header" style="padding-bottom: 40px">
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
	<h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp
	}}</span></h1>

	<!-- @if(count($nama_tim) > 0)
	<button class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #7B886F; color: black;" onclick="EditTim();"><i class="fa fa-plus"></i>&nbsp; Anggota</button>
	@else
	<button class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #B4DC7F; color: black;"
	onclick="InputTim();"><i class="fa fa-user-plus" aria-hidden="true"></i> Input Tim</button>
	@endif -->

	@if($role == 'PI2101044' || $role == 'PI1211001' || $role == 'PI0904001')
	<!-- <button class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #B4DC7F; color: black;"
	onclick="InputTim();"><i class="fa fa-user-plus" aria-hidden="true"></i> Input Tim</button>
	<a href="{{ url('index/log/soal/ky') }}" class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #FEFFA5; color: black;"><i class="fa fa-book" aria-hidden="true"></i> List Soal</a>
	<button class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #FFA0AC; color: black;" onclick="ModalUpload();"><i class="fa fa-upload" aria-hidden="true"></i> Upload Soal</button>
 -->
 <a href="{{ url('index/ky_hh') }}" class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #FEFFA5; color: black;"><i class="fa fa-book" aria-hidden="true"></i> Halaman Awal</a>
	@endif
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<center style="padding-top: 250px;">
			<span style="font-size: 50px; color: white">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</center>
	</div>
	<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
		<div class="box-body">
			<div class="col-xs-6">
				<div id="tombol_ky"></div>
			</div>
			<div class="col-xs-6">
				<div id="tombol_hh"></div>
			</div>
		</div>
	</div>
	<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
		<div class="box-body">
			<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px" id="home_ky">
				<div class="box-body">
					@if($role == 'PI2101044' || $role == 'PI1211001' || $role == 'PI0904001' || $role == 'PI0904001')
					<input type="hidden" name="id_soal" id="id_soal">
					<div class="col-xs-10" style="background-color:  #33658A ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
						<span style="font-size: 25px;color: white;width: 25%;"><span id="view_bulan"></span></span>
					</div>
					<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
						<input type="text" id="bulan_monitoring" class="form-control datepicker" style="width: 100%; height: 100%; text-align: center;" placeholder="Pilih Bulan" value="{{ date('Y-m') }}" onChange="FilterBulan(this.value)">
					</div>
					<div class="col-xs-9">
						<div id="not_ok"></div>
					</div>
					<div class="col-xs-3">
						<!-- <div class="row">
							<div id="highchart1" style="height: 70%; width: 70%"></div>
						</div> -->
						<div class="row">
							<div id="resume_all"></div>
						</div>
					</div>
					<!-- <div class="row">
						<div class="xol-xs-12 col-md-2 col-lg-7" style="height: 70vh;">
							<div id="not_ok" style="height: 60vh;"></div>
						</div>
						<div class="xol-xs-12 col-md-3 col-lg-5" style="height: 70vh;">
							<div class="row">
								<div id="highchart1" style="height: 30vh;"></div>
							</div>
							<div class="row">
								<div id="resume_all" style="height: 30vh;"></div>
							</div>
						</div>
					</div> -->
					@else
					@if(count($data_tim[0]->remark) > 0)
					<div class="col-xs-12" style="padding-left: 5px;padding-right: 5px; vertical-align: middle;" align="center">
						<div class="row">
							<h2 class="text-green" id="terimakasih">
							</h2>
						</div>
					</div>
					@else
					<div class="col-xs-10" style="background-color:  #33658A ;padding-left: 5px;padding-right: 5px;height:40px" align="center">
						<span style="font-size: 25px;color: white;width: 25%;"><span id="view_bulan_else"></span></span>
					</div>
					<div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;height:40px;vertical-align: middle" align="center">
						<input type="text" id="bulan_monitoring" class="form-control datepicker" style="width: 100%; height: 100%; text-align: center;" placeholder="Pilih Bulan" value="{{ date('Y-m') }}" onChange="FilterBulan(this.value)">
					</div>
					@endif
					@endif
					<!-- <div class="row">
						<div class="col-xs-12" style="padding top: 40px">
							<table id="tableResume" class="table table-bordered table-hover" style="width: 100%">
								<thead style="background-color: #605ca8; color: white;">
									<tr>
										<th style="width: 1%">No</th>
										<th style="width: 1%">Kelompok</th>
										<th style="width: 1%">Ketua Kelompok</th>
										<th style="width: 1%">Kategori</th>
										<th style="width: 2%">Department</th>
										<th style="width: 1%">Jumlah Anggota</th>
										<th style="width: 1%">Soal</th>
										<th style="width: 1%">Nilai</th>
									</tr>
								</thead>
								<tbody style="background-color: #fcf8e3;" id="bodyResume">
								</tbody>
							</table>
						</div>
					</div> -->
				</div>
			</div>
			<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px" id="home_hh">
				<div class="box-body">
					@if($role == 'PI2101044' || $role == 'PI1211001' || $role == 'PI0904001')
					<div class="col-xs-10" style="background-color:  #F26419 ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
						<span style="font-size: 25px;color: white;width: 25%;"><span id="view_bulan_hh"></span></span>
					</div>
					<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
						<input type="text" id="bulan_monitoring_hh" class="form-control datepicker" style="width: 100%; height: 100%; text-align: center;" placeholder="Pilih Bulan" value="{{ date('Y-m') }}" onChange="FilterBulanHh(this.value)">
					</div>
					<div class="row">
						<div class="col-xs-12" style="height: 70vh;">
							<div id="test123" style="height: 60vh;"></div>
						</div>
					</div>
					@else
					<div class="col-xs-12" style="background-color:  #F26419 ;padding-left: 5px;padding-right: 5px;height:40px;" align="center">
						<span style="font-size: 25px;color: white;width: 25%;"><span id="view_bulan_hh_else"></span></span>
					</div>
					@endif
					<!-- <div class="row">
						<div class="col-xs-12" style="height: 70vh;">
							<div id="highchart3" style="height: 60vh;"></div>
						</div>
					</div> -->
					<div class="row">
						<div class="col-xs-12">
							<!-- <table id="tableResumeHH" class="table table-bordered table-hover" style="width: 100%">
								<thead style="background-color: #605ca8; color: white;">
									<tr>
										<th style="width: 1%">No</th>
										<th style="width: 1%">Kelompok</th>
										<th style="width: 1%">Ketua Kelompok</th>
										<th style="width: 1%">Kategori</th>
										<th style="width: 2%">Department</th>
										<th style="width: 1%">#</th>
									</tr>
								</thead>
								<tbody style="background-color: #fcf8e3;" id="bodyResumeHH">
								</tbody>
							</table> -->
							<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">Report Penanganan Hiyari Hatto</span>
							</div>
							<table id="tableReportHh" class="table table-bordered table-hover"
							style="width: 100%">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 1%">No</th>
									<th style="width: 1%">Id Request</th>
									<th style="width: 1%">Resiko Kejadian</th>
									<th style="width: 1%">Karyawan</th>
									<th style="width: 1%">Tanggal Kejadian</th>
									<th style="width: 2%">Lokasi Kejadian</th>
									<th style="width: 1%">Report</th>
								</tr>
							</thead>
							<tbody style="background-color: #fcf8e3;" id="bodyReportHh">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>
<div class="modal fade" id="FormModalUpload" data-keyboard="false">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"
						style="background-color: #FFA0AC; text-align: center; margin-bottom: 5px; color: white">
						<span style="font-weight: bold; font-size: 1.6vw;">Upload Soal</span>
					</div>
				</ul>
			</div>
			<form id="importForm" name="importForm" method="post" action="{{ url('upload/file') }}"
			enctype="multipart/form-data">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="col-xs-12" align="left">
				<div class="nav-tabs-custom tab-danger">
					<ul class="nav nav-tabs">
						<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Upload Soal</a></li>
						<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Kunci Jawaban</a></li>
					</ul>
				</div>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1">
					<div class="form-group row" align="left">
						<label for="" class="col-sm-4 control-label" style="color: black; text-align: right;">Judul<span class="text-red"> :</span></label>
						<div class="col-sm-8">
							<input type="text" name="judul" id="judul" style="width: 95%; height: 30px" required>
						</div>
						<div class="col-sm-12" style="padding-top: 10px;">
							<center>
								<img id="image-preview" style="width: 500px">
							</center>
						</div>
						<label for="" class="col-sm-4 control-label" style="color: black; text-align: right">Periode KYT<span class="text-red"> :</span></label>
						<div class="col-sm-6" style="padding-top: 10px;">
							<div class="input-group">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" id="periode_kyt" name="periode_kyt" class="form-control datepicker" style="width: 70%; text-align: center;" placeholder="Periode KYT" required>
							</div>
						</div>
						<label for="" class="col-sm-4 control-label" style="color: black; text-align: right">Masukkan
							File Gambar<span class="text-red"> :</span></label>
							<div class="col-sm-6" style="padding-top: 10px;">
								<input type="file" name="file_gambar" id="file_gambar" onchange="previewImage()"
								accept="image/*,application/pdf">
							</div>
							<label for="" class="col-sm-4 control-label" style="color: black; text-align: right">Deskripsi<span class="text-red"> :</span></label>
							<input type="text" name="lop" id="lop" value="1" hidden>
							<div class='col-md-12' style='margin-bottom : 5px'>
								<div class="col-md-10" style="margin-bottom : 5px">
									<textarea rows="2" class="form-control" id="header1" name="header1" required></textarea>
								</div>
								<div class="col-xs-2" style="padding-left: 0; padding-top: 10px">
									<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i
										class='fa fa-plus'></i></button>
									</div>
								</div>
								<div id="tambah"></div>
							</div>
						</div>
						<div class="tab-pane" id="tab_2">
							<div class="form-group row" align="left">
								<label for="" class="col-sm-4 control-label" style="color: black; text-align: right">Kunci Jawaban<span class="text-red"> :</span></label>
								<input type="text" name="lop_jawaban" id="lop_jawaban" value="1" hidden>
								<div class='col-md-12' style='margin-bottom : 5px'>
									<div class="col-md-10" style="margin-bottom : 5px">
										<textarea rows="2" class="form-control" id="header1_jawaban" name="header1_jawaban" required></textarea>
									</div>
									<div class="col-xs-2" style="padding-left: 0; padding-top: 10px">
										<button class="btn btn-success" type="button" onclick='tambah_jawaban("tambah_jawaban","lop_jawaban");'><i class='fa fa-plus'></i></button>
									</div>
								</div>
								<div id="tambah_jawaban"></div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<center>
							<button type="submit" id="button_upload" class="btn btn-succes"
							style="font-weight: bold; font-size: 1.3vw; width: 100%; color: white; background-color: #2ecc71;">Simpan</button>
						</center>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="ModalInputTim" role="dialog">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"
						style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Input Tim</span>
					</div>
				</ul>
			</div>
			<form id="importForm" name="importForm" method="post" action="{{ url('insert/tim') }}"
			enctype="multipart/form-data">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="form-group row" align="center">
				<div class="col-xs-12" style="margin-bottom : 5px" id="modal_new_tim">
					<input type="text" name="test" id="test" value="1" hidden>
					<div class="col-xs-9" style="padding-left: 0">
						<select class="form-control select2" id="description1" name="description1"
						data-placeholder='Pilih Nama' style="width: 100%" required>
						<option value="">&nbsp;</option>
						@foreach($user as $row)
						<option
						value="{{$row->employee_id}}/{{$row->name}}/{{$row->department_shortname}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">
						{{$row->employee_id}} - {{$row->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-3" style="padding-left: 0">
					<select class="form-control select2" id="header1" name="header1"
					data-placeholder='Pilih Kategori' style="width: 100%" required>
					<option value="">&nbsp;</option>
					<option value="Ketua">Ketua</option>
					<option value="Wakil">Wakil</option>
					<option value="Anggota">Anggota</option>
				</select>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<center>
			<button type="submit" id="button_simpan" class="btn btn-succes"
			style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #78a1d0;">Simpan</button>
		</center>
	</div>
</form>
</div>
</div>
</div>
</div>
@if(count($nama_tim) > 0)
<div class="modal fade" id="ModalEditTim" role="dialog">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"
						style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Tim {{ $nama_tim[0]->nama_tim }}</span>
					</div>
				</ul>
			</div>
			<div class="col-sm-12">
				<br>
				<button class="btn btn-success" onclick="tambah_anggota()"><i class="fa fa-plus"></i>&nbsp; Tambah Anggota</button>
				<table class="table" style="width: 100%">
					<thead>
						<tr>
							<th style="width: 60%">Karyawan</th>
							<th style="width: 30%">Kategori</th>
							<th style="width: 10%">Hapus</th>
						</tr>
					</thead>
					<tbody id="body_penerima">
					</tbody>
				</table>
			</div>
			<div class="col-md-12" style="margin-top: 5px;">
				<a class="btn btn pull-right" onclick="SaveTrial('update')" style="font-weight: bold; font-size: 1.3vw; width: 100%; color: white; background-color: #78a1d0;">Simpan</a>
			</div>
		</div>
	</div>
</div>
</div>
@endif
<div class="modal fade" id="ModalKonfirmasiTim" role="dialog">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"
						style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Tim : </span><span
						style="font-weight: bold; font-size: 1.6vw;" id="nama_tim"></span>
					</div>
				</ul>
			</div>
			<div class="form-group row" align="center">
				<div class="col-md-12">
					<table class="table table-bordered table-hover"
					style="width: 100%; margin-bottom: 0px; text-align: center" id="TableKehadiran">
					<thead style="background-color: #605ca8; color: white;">
						<tr>
							<th style="text-align: center; width: 30%">Nama Tim</th>
							<th style="text-align: center; width: 40%">Nama</th>
							<th style="text-align: center; width: 30%">Kehadiran</th>
						</tr>
					</thead>
					<tbody id="BodyTableKehadiran" style="background-color: #fcf8e3;">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<div class="modal fade" id="ModalScore" role="dialog">
	<div class="modal-xs modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
							<span style="font-weight: bold; font-size: 1.6vw;">Detail Nilai</span>
						</div>
					</ul>
				</div>
				<div class="form-group row" align="center">
					<div class="col-md-12">
						<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center" id="TableScore">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="text-align: center; width: 30%">Nama Tim</th>
									<th style="text-align: center; width: 30%">Nama Ketua</th>
									<th style="text-align: center; width: 30%">Department</th>
								</tr>
							</thead>
							<tbody id="BodyTableScore" style="background-color: #fcf8e3;">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="ModalPengisianHH" role="dialog">
	<div class="modal-lg modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12"
						style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Hiyari Hatto (Near Miss)
						Form</span><br>
						<span style="font-weight: bold; font-size: 1vw;">PT. Yamaha Musical Products
						Indonesia</span>
					</div>
				</ul>
			</div>
			<div class="form-group row" align="center">
				<div class="col-md-12">
					<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th colspan="3">Kejadian berdasarkan pengalaman selama bekerja, yang hampir menimbulkan
									kecelakaan kerja yang kemudian dilakukan tindakan penanganan untuk menghilangkan-nya
								</th>
							</tr>
						</thead>
						<tbody style="background-color: #fcf8e3;">
							<tr>
								<td style="text-align: center; width: 30%">Nama Tim</td>
								<td style="text-align: center; width: 40%" colspan="2"><input type="text" name="hh_team" id="hh_team" value="{{$nama_tim[0]->nama_tim}}" style="text-align: center" readonly></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Nama Karyawan</td>
								<td style="text-align: center; width: 100%"><select class="form-control select01" id="hh_user" name="hh_user"
									style="width: 100%" required data-placeholder="Nama Karyawan">
									<option value="">&nbsp;</option>
									@foreach($user_team as $row)
									<option
									value="{{$row->nik}}/{{$row->nama}}/{{$row->department_short}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">
									{{$row->nik}} - {{$row->nama}}</option>
									@endforeach
								</select></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Saksi</td>
								<td style="text-align: center; width: 100%"><select class="form-control select02" id="hh_saksi" name="hh_saksi"
									style="width: 100%" required data-placeholder="Nama Karyawan">
									<option value="">&nbsp;</option>
									@foreach($karyawan as $row)
									<option
									value="{{$row->employee_id}}/{{$row->name}}/{{$row->department_shortname}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">
									{{$row->employee_id}} - {{$row->name}}</option>
									@endforeach
								</select></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Tanggal Kejadian</td>
								<td style="text-align: center; width: 50%"><div class="input-group" style="margin-left: 20%">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" id="hh_date_kejadian" class="form-control datepicker" style="width: 72%; text-align: center;" placeholder="Pilih Tanggal Input" value="{{ date('Y-m-d') }}" required>
								</div></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Lokasi Kejadian</td>
								<!-- <td style="text-align: left; width: 50%"><input type="text" id="hh_lokasi" class="form-control" style="width: 100%; text-align: left;" placeholder="Lokasi Kejadian" required></td> -->
								<td style="text-align: center; width: 100%"><select class="form-control select01" id="hh_lokasi" name="hh_lokasi"
									style="width: 100%" required data-placeholder="Lokasi Kejadian">
									<option value="">&nbsp;</option>
									@foreach($location as $loc)
									<option
									value="{{$loc->location}}">{{$loc->location}}</option>
									@endforeach
								</select></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Ringkasan Kejadian</td>
								<td style="text-align: center; width: 50%"><textarea class="form-control" id="hh_ringkasan" name="hh_ringkasan" style="height: 100px"></textarea></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Alat Pelindung Diri yang digunakan (jika ada)</td>
								<td style="text-align: center; width: 50%">
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" id="cek_apd" onclick="AdaApd()">
										<label class="form-check-label" for="cek_apd">Ada</label>
										<input type="text" id="input_apd" class="form-control" style="width: 100%; text-align: center; display:none" placeholder="APD" required>
									</div></td>
								</tr>
								<tr>
									<td style="text-align: left; width: 50%" colspan="2">Keparahan</td>
									<td style="text-align: left; width: 50%">
										<div class="form-check">
											<div>
												<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left; font-size: 12px">
													<thead>
														<tr>
															<td style="width: 30px"><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Ledakan</td>
															<td style="width: 30px"><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Kebakaran</td>
															<td style="width: 30px"><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Tenggelam</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Jatuh</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Terguling</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Benturan</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Dehidrasi</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Kelelahan</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Pingsan</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Patah Tulang</td>
															<td><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Tulang Dislokasi</td>
															<td><input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Tersengat Listrik</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Terpental</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Tertimpa</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Terjepit</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Terlilit</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Luka Sayat</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Luka Lecet/Gores</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Pingsan</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Sesak Nafas</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Mata Iritasi</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Salah Injak/Terperosok</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Kulit Iritasi</td>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Reflek Gerakan</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Menurunkan Penglihatan</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Menurunkan Pendengaran</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Menyentuh Barang2 Berbahaya</td>
														</tr>
														<tr>
															<td><input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Menyentuh Benda2 Bertemperatur Rendah</td>
															<td><input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Menyentuh Benda2 Bertemperatur Tinggi</td>
														</tr>
												</table>
												<!-- <input type="radio" id="keparahan_high" name="check_keparahan" value="Keparahan Tinggi"> Fatal, cacat permanent atau mengakibatkan  kerugian uang lebih dari Rp 10 juta<br><br>
												<input type="radio" id="keparahan_medium" name="check_keparahan" value="Keparahan Sedang"> Cacat tidak permanent atau mengakibatkan kerugian kurang dari Rp 10 juta<br><br>
												<input type="radio" id="keparahan_low" name="check_keparahan" value="Keparahan Rendah"> Kecelakaan kecil atau tidak ada luka dan tidak ada kerugian<br><br> -->
											</div>
										</div>
										<!-- <div>
											<p style="text-align: left">
												* Dan juga semua yang berhubungan dengan beberapa faktor seperti luka fisik, kerusakan peralatan, properti dan lingkungan.
											</p>
										</div> -->
									</td>
									</tr>
									<tr>
										<td style="text-align: left; width: 50%" colspan="2">Kemungkinan</td>
										<td style="text-align: left; width: 50%">
											<div class="form-check">
												<div>
													<input type="radio" id="kemungkinan_high" name="check_kemungkinan" value="Kemungkinan Tinggi"> Kejadian sering muncul (2 kali/minggu) dan atau terjadi pada beberapa orang (lebih dari 2 orang/minggu)<br><br>
													<input type="radio" id="kemungkinan_medium" name="check_kemungkinan" value="Kemungkinan Sedang"> Kejadian terjadi dalam waktu tidak terlalu sering (< 2 kali/minggu) dan kejadian < 2 orang /minggu<br><br>
													<input type="radio" id="kemungkinan_low" name="check_kemungkinan" value="Kemungkinan Rendah"> Kecelakaan jarang terjadi dan hanya orang tertentu (diluar definisi High dan Low)<br><br>
												</div>
											</div><div>
												<p style="text-align: left">
													* Dan juga mempertimbangkan kriteria seperti kompleksitas dari suatu kejadian dan faktor manusia
												</p>
											</div></td>
										</tr>
										<tr id="resiko">
											<td style="text-align: left; width: 50%; background-color: green" colspan="4"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi</td>
										</tr>
										<tr>
											<td style="text-align: left; width: 50%" colspan="2"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi</td>
											<td style="text-align: center; width: 50%"><textarea class="form-control" id="hh_perbaikan" name="hh_perbaikan" style="height: 100px"></textarea></td>
										</tr>
										<tr>
											<td style="text-align: left; width: 50%" colspan="2"> Informasi Lain</td>
											<td style="text-align: center; width: 50%"><textarea class="form-control" id="hh_lain" name="hh_lain" style="height: 100px"></textarea></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Batal</button>
							<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="SimpanHH()">Simpan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="ModalDetailGrafikBatangKy" role="dialog">
			<div class="modal-xs modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<div class="col-xs-12" style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
									<span style="font-weight: bold; font-size: 1.6vw;" id="dpt"></span>
								</div>
							</ul>
						</div>
						<div class="form-group row" align="center">
							<div class="col-md-12">
								<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center" id="DetailGrafikKy">
									<thead style="background-color: #605ca8; color: white;">
										<tr>
											<th style="text-align: center; width: 30%">Nama Tim</th>
											<th style="text-align: center; width: 30%">Nama Ketua</th>
											<th style="text-align: center; width: 30%">Kehadiran</th>
										</tr>
									</thead>
									<tbody id="BodyDetailGrafikKy" style="background-color: #fcf8e3;"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="ModalDetailGrafikHh" role="dialog">
			<div class="modal-xs modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<div class="col-xs-12" style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;"> 
									<span style="font-weight: bold; font-size: 1.6vw;">Data Bulan : </span>
									<span style="font-weight: bold; font-size: 1.6vw;" id="bulan"></span>
								</div>
							</ul>
						</div>
						<div class="form-group row" align="center">
							<div class="col-md-12">
								<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center" id="DetailGrafikHh">
									<thead style="background-color: #605ca8; color: white;">
										<tr>
											<th style="text-align: center; width: 25%">Request Id</th>
											<th style="text-align: center; width: 25%">Nama Request</th>
											<th stdyle="text-align: center; width: 25%">Status</th>
											<th style="text-align: center; width: 25%">Aksi</th>
										</tr>
									</thead>
									<tbody id="BodyDetailGrafikHh" style="background-color: #fcf8e3;"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="ModalReport" role="dialog">
			<div class="modal-lg modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
									<div id="modal_report">
									</div>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="ModalPresentasi" role="dialog" style="z-index: 10000;">
			<div class="modal-xs modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<center><span style="font-weight: bold; font-size: 1.6vw;" id="judul_report"></span></center>
									<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center;" id="DetailPresentasi">
									<thead style="background-color: #605ca8; color: white;">
										<tr>
											<th style="text-align: center; width: 25%">Nama Tim</th>
											<th style="text-align: center; width: 25%">Nama Ketua Tim</th>
										</tr>
									</thead>
									<tbody id="BodyDetailPresentasi" style="background-color: #fcf8e3;"></tbody>
								</table>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endsection
		@section('scripts')
		<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
		<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
		<script src="{{ url('js/buttons.flash.min.js')}}"></script>
		<script src="{{ url('js/jszip.min.js')}}"></script>
		<script src="{{ url('js/vfs_fonts.js')}}"></script>
		<script src="{{ url('js/buttons.html5.min.js')}}"></script>
		<script src="{{ url('js/buttons.print.min.js')}}"></script>
		<script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
		<script src="{{ url('js/highcharts.js')}}"></script>
		<script src="{{ url('js/highcharts-3d.js')}}"></script>
		<script src="{{ url('js/exporting.js')}}"></script>
		<script src="{{ url('js/export-data.js')}}"></script>
		<script>
			var no = 2;
			var no_jawaban = 2;
			var no_penerima = 1;
			var dept = <?php echo json_encode($user); ?>;
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var urutan_db = '{{$urutan[0]->urutan+1}}';
			var bulannnn = '{{ date("Y-m") }}';
			jQuery(document).ready(function() {
				$(document).keydown(function(e) {
					switch(e.which) {
						case 48:
						location.reload(true);
						break;
						case 49:
						$("#tab_header_1").click()
						break;
						case 50:
						$("#tab_header_2").click()
						break;
					}
				});
				$('body').toggleClass("sidebar-collapse");
				FilterBulan("{{ date('Y-m') }}");
				HomeKy();
				$('#file_gambar').on('change',function(){
					var fileName = $('input[type=file]').val().split('\\').pop();
					$(this).next('.custom-file-label').html(fileName);
				})
				$('#file_soal').on('change',function(){
					var fileName = $('input[type=file]').val().split('\\').pop();
					$(this).next('.custom-file-label').html(fileName);
				})
				$('.select2').select2({
					dropdownParent: $('#ModalInputTim'),
					allowClear : true,
				});
				$('.select01').select2({
					dropdownParent: $('#ModalPengisianHH'),
					allowClear : true,
				});
				$('.select02').select2({
					dropdownParent: $('#ModalPengisianHH'),
					allowClear : true,
				});
				$('#hh_date_kejadian').datepicker({
					autoclose: true,
					format: 'yyyy-mm-dd',
					todayHighlight: true
				});
				$('.select3').select2({
					dropdownParent: $('#ModalEditTim'),
					allowClear : true,
				});
				$('#bulan_monitoring').datepicker({
					autoclose: true,
					format: "yyyy-mm",
					startView: "months", 
					minViewMode: "months",
					autoclose: true,
				});
				$('#bulan_monitoring_hh').datepicker({
					autoclose: true,
					format: "yyyy-mm",
					startView: "months", 
					minViewMode: "months",
					autoclose: true,
				});
				$('#periode_kyt').datepicker({
					utoclose: true,
					format: "yyyy-mm",
					startView: "months", 
					minViewMode: "months",
					autoclose: true,
				});
			});
			function fetchTableKy(value){
				var data = {
					bulan:value
				}
				$.get('{{ url("fetch/resume/index") }}', data, function(result, status, xhr) {
					$('#tableResume').DataTable().clear();
					$('#tableResume').DataTable().destroy();
					$("#bodyResume").empty();
					var body = '';
					$.each(result.resume, function(index, value){
						var report = '{{ url("data_file/pengisian_ky")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						body += "<td>"+value.nama_tim+"</td>";
						body += "<td>"+value.nama+"</td>";
						body += "<td>"+value.posisi+"</td>";
						body += "<td>"+value.department+"</td>";
						body += "<td style='height: 10px; padding-bottom: 10px'>"+value.jml_tim+"<br><button type='button' class='btn btn-primary btn-xs' onclick='OpenModalBelumMengisi(\""+value.id+"\", \""+value.kode_soal+"\")'><i class='fa fa-eye'></i> LIHAT</button></td>";
						if (value.nama_tim == result.cek_tim[0].nama_tim) {
							if (result.open_soal > 0) {
								if (value.remark != null) {
									body += "<td><a href='"+report+"/"+value.kode_soal+"-"+value.nama_tim+".pdf' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
								}else{
									body += "<td><a href='{{ url("index/soal/ky") }}' class='btn btn-success btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Kerjakan Soal</a></td>";
								}
							}else{
								body += "<td><button type='button' class='btn btn-danger btn-xs'><i class='fa fa-times-circle-o'></i> Tidak Ada Soal</button></td>";
							}	
						}else{
							body += "<td>-</td>";
						}
						body += "<td>"+(value.score||'-')+"</td>";
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
					$('#tableResumeHH').DataTable().clear();
					$('#tableResumeHH').DataTable().destroy();
					$("#bodyResumeHH").empty();
					var body = '';
					$.each(result.resume2, function(index, value){
						var report = '{{ url("data_file/pengisian_ky")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						body += "<td>"+value.nama_tim+"</td>";
						body += "<td>"+value.nama+"</td>";
						body += "<td>"+value.posisi+"</td>";
						body += "<td>"+value.department+"</td>";
						body += "<td><button type='button' class='btn btn-success btn-xs' onclick='Hiyarihato()'><i class='fa fa-check-square-o'></i> Form HH</button></td>";
						body += "</tr>";
					})
					$("#bodyResumeHH").append(body);
					var table = $('#tableResumeHH').DataTable({
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
					$('#tableReportHh').DataTable().clear();
					$('#tableReportHh').DataTable().destroy();
					$("#bodyReportHh").empty();
					var body = '';
					$.each(result.resume3, function(index, value){
						var karyawan = value.karyawan.split('/');
						var level = value.level.split('/');
						var report = '{{ url("data_file/pengisian_hh")}}';
						var penanganan = '{{ url("index/penanganan/hiyarihatto")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						if (value.remark == 'Open') {
							body += "<td>"+value.request_id+"<br><span class='label label-danger' style=color: white>OPEN</span></td>";
						}else{
							body += "<td>"+value.request_id+"<br><span class='label label-success' style=color: white>CLOSE</span></td>";
						}
						body += "<td>"+level[2]+"</td>";
						body += "<td>("+karyawan[0]+")<br>"+karyawan[1]+"</td>";
						body += "<td>"+value.tanggal+"</td>";
						body += "<td>"+value.lokasi+"</td>";
						if (value.remark == 'Open') {
							body += "<td><a href='"+penanganan+"/"+value.request_id+"/"+value.id_ketua+"' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-pencil' aria-hidden='true'></i> Penanganan</a></td>";
						}else{
							body += "<td><a href='"+report+"/"+value.request_id+".pdf' target='_blank' class='btn btn-success btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
						}
						body += "</tr>";
					})
					$("#bodyReportHh").append(body);
					var table = $('#tableReportHh').DataTable({
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
				});
}
function FilterBulan(value) {
	$("#loading").show();
	var q = new Date(value);
	var strArray=['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var arr_bulan = strArray[q.getMonth()];
	$('#view_bulan').html('Monitoring Kiken Yochi - '+arr_bulan);
	$('#view_bulan_else').html('Form Kiken Yochi - '+arr_bulan);
	$('#terimakasih').html('<i class="fa fa-check-circle fa-lg"></i> Terimakasih Telah Melaksanakan KY Bulan '+arr_bulan);
	var data = {
		bulan:value
	}
	$.get('<?php echo e(url("fetch/resume/ky")); ?>', data, function(result, status, xhr){
		if (result.status) {
			$("#loading").hide();
			if (result.username == 'PI2101044' || result.username == 'PI1211001' || result.username == 'PI0904001') {
				ChartUpdate(value);
				Chart1(value);	
				$('#id_soal').val(result.kode_soal[0].kode_soal);
			}else{
				fetchTableKy(value);
			}
		}else{
			$("#loading").hide();
			openErrorGritter('Error!',data.message);
		}
	});
}
function FilterBulanHh(value) {
	$("#loading").show();
	var q = new Date(value);
	var strArray=['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var arr_bulan = strArray[q.getMonth()];
	$('#view_bulan_hh').html('Monitoring Tindak Lanjut Kejadian hampir celaka (Hiyari Hatto/Near Miss) - '+arr_bulan);
	$('#view_bulan_hh_else').html('Kejadian hampir celaka (Hiyari Hatto/Near Miss) - '+arr_bulan);
	var data = {
		bulan:value
	}
	$.get('<?php echo e(url("fetch/resume/ky")); ?>', data, function(result, status, xhr){
		if (result.status) {
			$("#loading").hide();
			if (result.username == 'PI2101044' || result.username == 'PI1211001' || result.username == 'PI0904001') {
				ChartUpdate(value);
				Chart1(value);	
			}else{
				fetchTableKy(value);


				var categori_hh = [];
				var series_hh = [];
				var series_open = [];
				var series_close = [];
				$.each(result.series, function(key, value){
					var isi = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_hh, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_hh.push(value2.jumlah);
							isi = 1;
						}
					});
					if (isi == 0) {
						series_hh.push(0);
					}
				});

				$.each(result.series, function(key, value){
					var isi_1 = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_open, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_open.push(value2.jumlah);
							isi_1 = 1;
						}
					});
					if (isi_1 == 0) {
						series_open.push(0);
					}
				});
				$.each(result.series, function(key, value){
					var isi_2 = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_close, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_close.push(value2.jumlah);
							isi_2 = 1;
						}
					});
					if (isi_2 == 0) {
						series_close.push(0);
					}
				});

				// Highcharts.chart('highchart3', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'area',
				// 		options3d: {
				// 			enabled: true,
				// 			alpha: 15,
				// 			beta: 0,
				// 			depth: 50,
				// 			viewDistance: 50
				// 		}
				// 	},
				// 	title: {
				// 		text: 'Monitoring Pengisian HiyariHato'
				// 	},
				// 	xAxis: {
				// 		tickInterval: 1,
				// 		gridLineWidth: 1,
				// 		categories: categori_hh,
				// 		crosshair: true
				// 	},
				// 	yAxis: {
				// 		allowDecimals: false,
				// 		min: 0,
				// 		title: {
				// 			text: ''
				// 		}
				// 	},
				// 	tooltip: {
				// 		formatter: function () {
				// 			return '<b>' + this.x + '</b><br/>' +
				// 			this.series.name + ': ' + this.y
				// 		}
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			stacking: 'normal',
				// 			pointPadding: 0.93,
				// 			groupPadding: 0.93,
				// 			borderWidth: 0.8,
				// 			borderColor: 'black'
				// 		},
				// 		series: {
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y}',
				// 				style:{
				// 					textOutline: false
				// 				}
				// 			},
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						DetailGrafikHh(this.category, this.series.name);
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},
				// 	legend: {
				// 		enabled: true,
				// 		borderWidth: 1
				// 	},
				// 	series: [{
				// 		name: 'Total',
				// 		data: series_hh,
				// 		stack: '1',
				// 		color: '#E6E6EA'
				// 	},{
				// 		name: 'Open',
				// 		data: series_open,
				// 		stack: '1',
				// 		color: 'red'
				// 	},{
				// 		name: 'Close',
				// 		data: series_close,
				// 		stack: '1',
				// 		color: '#85D2D0'
				// 	}]
				// });
			}
		}else{
			$("#loading").hide();
			openErrorGritter('Error!', data.message);
		}
	});
}
function HomeKy(){
	$("#home_ky").show();
	$("#home_hh").hide();
	$("#tombol_ky").html('<button id="click_simpati" onclick="HomeKy()" class="btn btn-light" style="font-weight: bold; font-size: 15px; width: 100%; color: white; background-color: #33658A">Kiken Yochi<br>お見舞いのお金</button>');
	$("#tombol_hh").html('<button onclick="HomeHh()" class="btn btn-light" style="font-weight: bold; font-size: 15px; width: 100%; color: white; background-color: #f8af88">Hiyari Hatto<br>お見舞いのお金</button>');
}
function HomeHh(){
	$("#home_hh").show();	
	$("#home_ky").hide();
	$("#tombol_ky").html('<button id="click_simpati" onclick="HomeKy()" class="btn btn-light" style="font-weight: bold; font-size: 15px; width: 100%; color: white; background-color: #a4c5dd">Kiken Yochi<br>お見舞いのお金</button>');
	$("#tombol_hh").html('<button onclick="HomeHh()" class="btn btn-light" style="font-weight: bold; font-size: 15px; width: 100%; color: white; background-color: #f26419">Hiyari Hatto<br>お見舞いのお金</button>');
	FilterBulanHh(bulannnn);
}
function tambah_anggota(){
	var body = "";
	if ($('.dept').length+parseInt('{{$urutan[0]->urutan}}') >= 6) {
		openErrorGritter('Error!','Jumlah Tim Anda Terlalu Banyak');
		return false;
	}
	var option_dept = "";
	$.each(dept, function(key, value) { 
		option_dept += "<option value='"+value.employee_id+"/"+value.name+"/"+value.department_shortname+"/"+value.department+"/"+value.section+"/"+value.group+"/"+value.sub_group+"'>"+value.employee_id+" - "+value.name+"</option>" ;
	})
	body += '<tr id="tr_'+no_penerima+'">';
	body += '<td style="padding-right: 10px"><select type="text" class="form-control select5 dept" data-placeholder="Pilih Karyawan" id="dept_'+no_penerima+'"><option value=""></option>'+option_dept+'</select></td>';
	body += '<td style="padding-left: 10px"><select class="form-control select5 sec" id="sec_'+no_penerima+'" data-placeholder="Pilih Kategori"><option value=""></option><option value="Ketua">Ketua</option><option value="Wakil">Wakil</option><option value="Anggota">Anggota</option></select></td>';
	body += '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
	body += '</tr>';
	$("#body_penerima").append(body);
	$('.select5').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#tr_'+no_penerima),
	});
	no_penerima++;
}
function deleteMat(elem) {
	$(elem).closest('tr').remove();
}
function SaveTrial(){
	var dept_arr = [];
	$('.dept').each(function(index, value) {
		dept_arr.push($(this).val());
	});
	var sec_arr = [];
	$('.sec').each(function(index, value) {
		sec_arr.push($(this).val());
	});
	var formData = new FormData();
	formData.append('karyawan', dept_arr);
	formData.append('kategori', sec_arr);
	formData.append('id_tim', '{{$nama_tim[0]->nama_tim}}');
	formData.append('nomor', '{{$urutan[0]->urutan}}');
	$.ajax({
		url:"{{ url('edit/tim') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				openSuccessGritter('Success', data.message);
				$('#loading').hide();
				$('#ModalEditTim').modal('hide');
				location.reload(true);
			}else{
				openErrorGritter('Error!',data.message);
				$('#loading').hide();
			}
		}
	});
}
function Percobaan(id){
	var radioValue = $(".type:checked").val();
	var value = [];
	value.push(id);
}
function ModalScore(category){
	$("#ModalScore").modal('show');
	var data = {
		category:category
	}
	$.get('<?php echo e(url("fetch/data/score")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#TableScore').DataTable().clear();
			$('#TableScore').DataTable().destroy();
			$('#BodyTableScore').html("");
			var tableData = "";
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
				tableData += '<td style="text-align: center;">'+ value.nama +'</td>';
				tableData += '<td style="text-align: center;">'+ value.department_short +'</td>';
				tableData += '</tr>';
			});
			$('#BodyTableScore').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}
function previewImage() {
	document.getElementById("image-preview").style.display = "block";
	var oFReader = new FileReader();
	oFReader.readAsDataURL(document.getElementById("file_gambar").files[0]);
	oFReader.onload = function(oFREvent) {
		document.getElementById("image-preview").src = oFREvent.target.result;
	};
};
function AdaApd(){
	var cek_apd = document.getElementById("cek_apd");
	var input_apd = document.getElementById("input_apd");
	if (cek_apd.checked == true){
		input_apd.style.display = "block";
	} else {
		input_apd.style.display = "none";
		$("#input_apd").val('');
	}
}
function Hiyarihato(){
	$("#ModalPengisianHH").modal('show');
	$("#isian_apd").hide();
	$("#resiko").hide();
}
function DetailGrafikBatangKy(category, name){
	$("#ModalDetailGrafikBatangKy").modal('show');
	$("#dpt").html(category);
	var data = {
		category:category,
		name:name
	}
	$.get('<?php echo e(url("fetch/data/score")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#DetailGrafikKy').DataTable().clear();
			$('#DetailGrafikKy').DataTable().destroy();
			$('#BodyDetailGrafikKy').html("");
			var tableData = "";
			$.each(result.grafik_ky, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
				tableData += '<td style="text-align: center;">'+ value.nama +'</td>';
				if (name == 'Sudah Mengisi') {
					tableData += "<td><span class='label label-success' style=color: white>"+name+"</span></td>";
				}else if (name == 'Tidak Hadir') {
					tableData += "<td><span class='label label-danger' style=color: white>"+name+"</span></td>";
				}
				tableData += '</tr>';
			});
			$('#BodyDetailGrafikKy').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}
function DetailGrafikHh(category, name){
	$("#ModalDetailGrafikHh").modal();
	$("#bulan").html(category);
	var data = {
		category:category,
		name:name
	}
	$.get('<?php echo e(url("fetch/data/score")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#DetailGrafikHh').DataTable().clear();
			$('#DetailGrafikHh').DataTable().destroy();
			$('#BodyDetailGrafikHh').html("");
			var tableData = "";
			$.each(result.grafik_hh, function(key, value) {
				var karyawan = value.karyawan.split('/');
				var report = '{{ url("data_file/pengisian_hh")}}';
				var penanganan = '{{ url("index/penanganan/hiyarihatto")}}';
				tableData += '<tr>';
				tableData += '<td style="text-align: center;">'+ value.request_id +'</td>';
				tableData += '<td style="text-align: center;">'+ karyawan[1] +'</td>';
				tableData += '<td style="text-align: center;">'+ value.remark +'</td>';
				if (value.remark == 'Open') {
					tableData += "<td><a href='"+penanganan+"/"+value.request_id+"/"+value.id_ketua+"' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-pencil' aria-hidden='true'></i> Penanganan</a></td>";
				}else if (value.remark == 'Close') {
					tableData += "<td><a href='"+report+"/"+value.request_id+".pdf' target='_blank' class='btn btn-success btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
				}
				tableData += '</tr>';
			});
			$('#BodyDetailGrafikHh').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}
function SimpanHH(){
	$("#loading").show();
	var keparahan;
	if (document.getElementById('keparahan_high').checked) {
		keparahan = 'Keparahan Tinggi';
	}else if(document.getElementById('keparahan_medium').checked){
		keparahan = 'Keparahan Sedang';
	}else if(document.getElementById('keparahan_low').checked){
		keparahan = 'Keparahan Rendah';
	}
	var kemungkinan;
	if (document.getElementById('kemungkinan_high').checked) {
		kemungkinan = 'Kemungkinan Tinggi';
	}else if(document.getElementById('kemungkinan_medium').checked){
		kemungkinan = 'Kemungkinan Sedang';
	}else if(document.getElementById('kemungkinan_low').checked){
		kemungkinan = 'Kemungkinan Rendah';
	}
	var team     	= $("#hh_team").val();
	var karyawan 	= $("#hh_user").val();
	var saksi 	 	= $("#hh_saksi").val();
	var tanggal  	= $("#hh_date_kejadian").val();
	var lokasi   	= $("#hh_lokasi").val();
	var ringkasan	= $("#hh_ringkasan").val();
	var apd      	= $("#input_apd").val();
	var perbaikan   = $("#hh_perbaikan").val();
	var lain        = $("#hh_lain").val();
	var data = {
		team : team,
		karyawan : karyawan,
		saksi : saksi,
		tanggal : tanggal,
		lokasi : lokasi,
		ringkasan : ringkasan,
		apd : apd,
		keparahan : keparahan,
		kemungkinan : kemungkinan,
		perbaikan : perbaikan,
		lain : lain 
	}
	$.post('{{ url("create/hiyarihatto") }}', data, function(result, status, xhr) {
		if(result.status){
			$("#ModalPengisianHH").modal('hide');
			$("#loading").hide();
			openSuccessGritter('Success!', result.message);
			location.reload(true);
		}else{
			openErrorGritter('Error!', result.message);
		}
	})
}
function InputTim(id){
	$("#ModalInputTim").modal('show');
	$('.select2').select2({
		dropdownParent: $('#ModalInputTim'),
		allowClear : true,
	});
}
function EditTim(){
	$("#ModalEditTim").modal('show');
}
function hapus_baris(id){
}
function tambah(id,lop) {
	var id = id;
	var lop = "";
	if (id == "tambah"){
		lop = "lop";
	}else{
		lop = "lop2";
	}
	var divdata = "";
	divdata += "<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'>";
	divdata += "<div id='divheader_"+no+"' class='col-xs-10' style='margin-bottom : 5px'>";
	divdata += "<textarea rows='2' class='form-control' id='header"+no+"' name='header"+no+"' required></textarea>";
	divdata += "</div>";
	divdata += "<div class='col-xs-2' style='padding:0; padding-top: 10px'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button>&nbsp;";
	divdata += "<button type='button' onclick='tambah(\""+id+"\",\""+lop+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button>";
	divdata += "</div>";
	divdata += "</div>";
	$("#"+id).append(divdata);
	document.getElementById(lop).value = no;
	no+=1;
}

function tambah_jawaban(id_jawaban,lop_jawaban) {
	var id = id_jawaban;
	var lop = "";
	if (id == "tambah_jawaban"){
		lop = "lop_jawaban";
	}else{
		lop = "lop2_jawaban";
	}
	var divdata = "";
	divdata += "<div id='"+no_jawaban+"' class='col-md-12' style='margin-bottom : 5px'>";
	divdata += "<div id='divheader_"+no_jawaban+"' class='col-xs-10' style='margin-bottom : 5px'>";
	divdata += "<textarea rows='2' class='form-control' id='header"+no_jawaban+"_jawaban' name='header"+no_jawaban+"_jawaban' required></textarea>";
	divdata += "</div>";
	divdata += "<div class='col-xs-2' style='padding:0; padding-top: 10px'>&nbsp;<button onclick='kurang_jawaban(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button>&nbsp;";
	divdata += "<button type='button' onclick='tambah_jawaban(\""+id+"\",\""+lop+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button>";
	divdata += "</div>";
	divdata += "</div>";
	$("#"+id).append(divdata);
	document.getElementById(lop).value = no_jawaban;
	no_jawaban+=1;
}
function kurang(elem,lop) {
	var lop = lop;
	var ids = $(elem).parent('div').parent('div').attr('id');
	var oldid = ids;
	$(elem).parent('div').parent('div').remove();
	var newid = parseInt(ids) + 1;
	jQuery("#"+newid).attr("id",oldid);
	jQuery("#divheader_"+newid).attr("id",oldid);
	jQuery("#description"+newid).attr("name","description"+oldid);
	jQuery("#header"+newid).attr("name","header"+oldid);
	jQuery("#description"+newid).attr("id","description"+oldid);
	jQuery("#header"+newid).attr("id","header"+oldid);
	no-=1;
	var a = no -1;
	for (var i =  ids; i <= a; i++) { 
		var newid = parseInt(i) + 1;
		var oldid = newid - 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);
		jQuery("#header"+newid).attr("name","header"+oldid);

		jQuery("#description"+newid).attr("id","description"+oldid);
		jQuery("#header"+newid).attr("id","header"+oldid);
	}
	document.getElementById(lop).value = a;
}

function kurang_jawaban(elem,lop_jawaban) {
	var lop_jawaban = lop_jawaban;
	var ids = $(elem).parent('div').parent('div').attr('id');
	var oldid = ids;
	$(elem).parent('div').parent('div').remove();
	var newid = parseInt(ids) + 1;
	jQuery("#"+newid).attr("id",oldid);
	jQuery("#divheader_"+newid).attr("id",oldid);
	jQuery("#description"+newid).attr("name","description"+oldid);
	jQuery("#header"+newid).attr("name","header"+oldid);
	jQuery("#description"+newid).attr("id","description"+oldid);
	jQuery("#header"+newid).attr("id","header"+oldid);
	no-=1;
	var a = no -1;
	for (var i =  ids; i <= a; i++) { 
		var newid = parseInt(i) + 1;
		var oldid = newid - 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);
		jQuery("#header"+newid).attr("name","header"+oldid);

		jQuery("#description"+newid).attr("id","description"+oldid);
		jQuery("#header"+newid).attr("id","header"+oldid);
	}
	document.getElementById(lop_jawaban).value = a;
}
function tambah_1(id,test) {
	if (no > 6) {
		alert("Maaf Jumlah Tim Max 6 Orang.");
	}else{
		var id = id;
		var test = "";
		if (id == "tambah_1"){
			test = "test";
		}else{
			test = "test";
		}
		var divdata = "";
		divdata += "<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'>";
		divdata += "<div class='col-xs-7' style='padding-left:0;'>";
		divdata += "<select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' style='width: 100%'>";
		divdata += "<option value=''>&nbsp;</option>@foreach($user as $row)<option value='{{$row->employee_id}}/{{$row->name}}/{{$row->department_shortname}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}'>{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>";
		divdata += "</div>";
		divdata += "<div id='divheader_"+no+"' class='col-xs-3' style='padding-left:0;'>";
		divdata += "<select class='form-control select7' id='header"+no+"' name='header"+no+"' data-placeholder='Pilih Kategori' style='width: 100%'>";
		divdata += "<option value=''>&nbsp;</option>";
		divdata += "<option value='Ketua'>Ketua</option>";
		divdata += "<option value='Wakil'>Wakil</option>";
		divdata += "<option value='Anggota'>Anggota</option>";
		divdata += "</select>";
		divdata += "</div>";
		divdata += "<div class='col-xs-2' style='padding:0;'><button onclick='kurang_1(this,\""+test+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button>&nbsp;";
		divdata += "<button type='button' onclick='tambah_1(\""+id+"\",\""+test+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button>";
		divdata += "</div>";
		divdata += "</div>";
		$("#"+id).append(divdata);
		document.getElementById(test).value = no;
		no+=1;
		$('.select7').select2({
			dropdownParent : $("#ModalInputTim"),
			tags : true
		});
	}
}
function tambah_2(id) {
	var urut = urutan_db;
	var urutan = urut;
	if (urutan >= 6) {
		alert("Maaf Jumlah Tim Max 6 Orang.");
	}else{
		var id = id;
		var test = "";
		if (id == "tambah_2"){
			urutan = "urut";
		}else{
			urutan = "urut";
		}
		var divdata = "";
		divdata += "<div id='"+urut+"' value='"+urut+"' class='col-md-12' style='margin-bottom : 5px'>";
		divdata += "<div class='col-xs-7' style='padding-left:0;'>";
		divdata += "<select class='form-control select9' id='edit_description"+urut+"' name='edit_description"+urut+"' data-placeholder='"+urut+"' style='width: 100%'>";
		divdata += "<option value=''>&nbsp;</option>@foreach($user as $row)<option value='{{$row->employee_id}}/{{$row->name}}/{{$row->department_shortname}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}'>{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>";
		divdata += "</div>";
		divdata += "<div id='edit_divheader_"+urut+"' class='col-xs-3' style='padding-left:0;'>";
		divdata += "<select class='form-control select9' id='edit_header"+urut+"' name='edit_header"+urut+"' data-placeholder='Pilih Kategori' style='width: 100%'>";
		divdata += "<option value=''>&nbsp;</option>";
		divdata += "<option value='Ketua'>Ketua</option>";
		divdata += "<option value='Wakil'>Wakil</option>";
		divdata += "<option value='Anggota'>Anggota</option>";
		divdata += "</select>";
		divdata += "</div>";
		divdata += "<div class='col-xs-2' style='padding:0;'><button onclick='kurang_2(this,\""+urut+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button>&nbsp;";
		divdata += "<button type='button' onclick='tambah_2(\""+id+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button>";
		divdata += "</div>";
		divdata += "</div>";
		$("#"+id).append(divdata);
		urutan_db++;
		$('.select9').select2({
			dropdownParent : $("#ModalEditTim"),
			tags : true
		});
	}
}
function kurang_1(elem,test) {
	var test = test;
	var ids = $(elem).parent('div').parent('div').attr('id');
	var oldid = ids;
	$(elem).parent('div').parent('div').remove();
	var newid = parseInt(ids) + 1;
	jQuery("#"+newid).attr("id",oldid);
	jQuery("#divheader_"+newid).attr("id",oldid);
	jQuery("#description"+newid).attr("name","description"+oldid);
	jQuery("#header"+newid).attr("name","header"+oldid);
	jQuery("#description"+newid).attr("id","description"+oldid);
	jQuery("#header"+newid).attr("id","header"+oldid);
	no-=1;
	var a = no -1;
	for (var i =  ids; i <= a; i++) { 
		var newid = parseInt(i) + 1;
		var oldid = newid - 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);
		jQuery("#header"+newid).attr("name","header"+oldid);
		jQuery("#description"+newid).attr("id","description"+oldid);
		jQuery("#header"+newid).attr("id","header"+oldid);
	}
	document.getElementById(test).value = a;
}
function kurang_2(elem,test) {
	var test = test;
	var ids = $(elem).parent('div').parent('div').attr('id');
	var oldid = ids;
	$(elem).parent('div').parent('div').remove();
	var newid = parseInt(ids) + 1;
	jQuery("#"+newid).attr("id",oldid);
	jQuery("#edit_divheader_"+newid).attr("id",oldid);
	jQuery("#edit_description"+newid).attr("name","edit_description"+oldid);
	jQuery("#header"+newid).attr("name","header"+oldid);
	jQuery("#edit_description"+newid).attr("id","edit_description"+oldid);
	jQuery("#header"+newid).attr("id","header"+oldid);
	urutan-=1;
	var a = urutan -1;
	for (var i =  ids; i <= a; i++) { 
		var newid = parseInt(i) + 1;
		var oldid = newid - 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#edit_description"+newid).attr("name","edit_description"+oldid);
		jQuery("#header"+newid).attr("name","header"+oldid);
		jQuery("#edit_description"+newid).attr("id","edit_description"+oldid);
		jQuery("#header"+newid).attr("id","header"+oldid);
	}
	document.getElementById(test).value = a;
}
function ModalUpload(){
	$("#FormModalUpload").modal('show');
}
function Refresh(){
	location.reload(true);   
}
function ShowDetail(cat){
}
function Chart1(value) {
	$("#show_detail").hide();
	var data = {
		status : 2,
		dpt:$('#dpt').val(),
		stt:$('#stt').val(),
		nm:$('#nm').val(),
		date_to:$('#date_to').val(),
		bulan:value
	}
	$.get('{{ url("fetch/resume/ky") }}',data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){
				$("#loading").hide();
				var dept = [];
				var sudah = [];
				var belum = [];
				var series = []
				var series2 = [];
				var jumlah = [];
				var series3 = [];
				var type = [];
				var score = [];
				var scores = [];
				var resumeTimBody = "";
				$('#resumeTimBody').html("");
				$('#jumlah_tim').html(result.jumlah);
				$('#resumeTimBody').append(resumeTimBody);
				for (var i = 0; i < result.grafik_update.length; i++) {
					dept.push(result.grafik_update[i].department_short);
					sudah.push(parseInt(result.grafik_update[i].sudah));
					series.push([dept[i], sudah[i]]);
					belum.push(parseInt(result.grafik_update[i].belum));
					series2.push([dept[i], belum[i]]);
					jumlah.push(parseInt(result.grafik_update[i].jumlah));
					series3.push([dept[i], jumlah[i]]);
				}
				var categori_hh = [];
				var series_hh = [];
				var series_open = [];
				var series_close = [];
				$.each(result.series, function(key, value){
					var isi = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_hh, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_hh.push(value2.jumlah);
							isi = 1;
						}
					});
					if (isi == 0) {
						series_hh.push(0);
					}
				});
				$.each(result.series, function(key, value){
					var isi_1 = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_open, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_open.push(value2.jumlah);
							isi_1 = 1;
						}
					});
					if (isi_1 == 0) {
						series_open.push(0);
					}
				});
				$.each(result.series, function(key, value){
					var isi_2 = 0;
					categori_hh.push(value.bulan);
					$.each(result.grafik_close, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_close.push(value2.jumlah);
							isi_2 = 1;
						}
					});
					if (isi_2 == 0) {
						series_close.push(0);
					}
				});
				for (var i = 0; i < result.score.length; i++) {
					type.push(result.score[i].score);
					scores.push(parseInt(result.score[i].jumlah));
				}
				var pp = [];
				var series_namatim = [];
				$.each(result.grafik_score, function(key, value){
					var isi_p = 0;
					pp.push(value.nama_tim);
					$.each(result.score, function(key2, value2){
						if (value.jumlah == value2.jumlah2) {
							series_namatim.push(value.nama_tim);
							isi_p = 1;
						}
					});
					if (isi_p == 0) {
						series_namatim.push(0);
					}
				});
				var jml = scores.reduce((partialSum, a) => partialSum + a, 0);
				for (var i = 0; i < scores.length; i++) {
					score.push({y:parseInt(scores[i]),key:((scores[i]/parseInt(jml))*100).toFixed(0)});
				}
				// Highcharts.chart('highchart2', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'column',
				// 		options3d: {
				// 			enabled: true,
				// 			alpha: 15,
				// 			beta: 0,
				// 			depth: 50,
				// 			viewDistance: 50
				// 		}
				// 	},
				// 	title: {
				// 		text: 'Resume Nilai KYT'
				// 	},
				// 	xAxis: {
				// 		tickInterval: 1,
				// 		gridLineWidth: 1,
				// 		categories: type,
				// 		crosshair: true,
				// 		title: {
				// 			text: 'Nilai (%)'
				// 		}
				// 	},
				// 	yAxis: {
				// 		allowDecimals: false,
				// 		min: 0,
				// 		title: {
				// 			text: 'Jumlah Tim'
				// 		}
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			stacking: 'normal',
				// 			pointPadding: 0.93,
				// 			groupPadding: 0.93,
				// 			borderWidth: 0.8,
				// 			borderColor: 'black'
				// 		},
				// 		series: {
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y} Tim</b>',
				// 				style:{
				// 					textOutline: false
				// 				}
				// 			},
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						ModalScore(this.category);
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},

				// 	legend: {
				// 		enabled: false,
				// 		borderWidth: 1
				// 	},

				// 	credits:{
				// 		enabled:false
				// 	},

				// 	series: [{
				// 		name: 'Score',
				// 		data: score,
				// 		color: '#FFE45E'
				// 	}]
				// });	
				// Highcharts.chart('highchart3', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'area',
				// 		options3d: {
				// 			enabled: true,
				// 			alpha: 15,
				// 			beta: 0,
				// 			depth: 50,
				// 			viewDistance: 50
				// 		}
				// 	},
				// 	title: {
				// 		text: 'Monitoring Pengisian HiyariHato'
				// 	},
				// 	xAxis: {
				// 		tickInterval: 1,
				// 		gridLineWidth: 1,
				// 		categories: categori_hh,
				// 		crosshair: true
				// 	},
				// 	yAxis: {
				// 		allowDecimals: false,
				// 		min: 0,
				// 		title: {
				// 			text: ''
				// 		}
				// 	},
				// 	tooltip: {
				// 		formatter: function () {
				// 			return '<b>' + this.x + '</b><br/>' +
				// 			this.series.name + ': ' + this.y
				// 		}
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			stacking: 'normal',
				// 			pointPadding: 0.93,
				// 			groupPadding: 0.93,
				// 			borderWidth: 0.8,
				// 			borderColor: 'black'
				// 		},
				// 		series: {
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y}',
				// 				style:{
				// 					textOutline: false
				// 				}
				// 			},
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						DetailGrafikHh(this.category, this.series.name);
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},
				// 	legend: {
				// 		enabled: true,
				// 		borderWidth: 1
				// 	},
				// 	series: [{
				// 		name: 'Total',
				// 		data: series_hh,
				// 		stack: '1',
				// 		color: '#E6E6EA'
				// 	},{
				// 		name: 'Open',
				// 		data: series_open,
				// 		stack: '1',
				// 		color: 'red'
				// 	},{
				// 		name: 'Close',
				// 		data: series_close,
				// 		stack: '1',
				// 		color: '#85D2D0'
				// 	}]
				// });
				var category_not_ok = [];
				var pp = [];
				var series_not_ok = [];
				for (var i = 0; i < result.not_ok.length; i++) {
					category_not_ok.push(result.not_ok[i].nama_tim);
					pp.push(parseInt(result.not_ok[i].score));
					series_not_ok.push([category_not_ok[i], pp[i]]);
				}
				Highcharts.chart('not_ok', {
					chart: {
						backgroundColor: null,
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 15,
							beta: 0,
							depth: 50,
							viewDistance: 50
						}
					},
					title: {
						text: 'Resume Tim Not Ok'
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: category_not_ok,
						crosshair: true
					},
					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: 'NILAI'
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: 'black'
						},
						// series: {
						// 	dataLabels: {
						// 		enabled: true,
						// 		format: '{point.y}',
						// 		style:{
						// 			textOutline: false
						// 		}
						// 	},
						// 	events: {
						// 		legendItemClick: function(e) {
						// 			e.preventDefault();
						// 		}
						// 	}
						// }
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										CekIni(this.category);
									}
								}
							},
							dataLabels: {
								enabled: false,
								format: '{point.y}'
							}
						}
					},
					legend: {
						enabled: true,
						borderWidth: 1
					},
					credits:{
						enabled:false
					},
					series: [{
						name: 'NAMA TIM',
						data: series_not_ok,
						stack: '1',
						color: '#F4B9B8'
					}]
				});

				Highcharts.chart('test123', {
					chart: {
						type: 'column',
						backgroundColor: null
					},
					title: {
						text: "Temuan Patrol by Lokasi"
					},
					xAxis: {
						type: 'category',
						categories: categori_hh,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1,
						labels: {
							formatter: function (e) {
								return this.value;
							}
						}
					},
					yAxis: {
						lineWidth:2,
						lineColor:'#fff',
						type: 'linear',
						title: {
							text: 'Total Temuan'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: 'black'
							}
						}
					},
					legend: {
						itemStyle:{
							color: "black",
							fontSize: "12px",
							fontWeight: "bold",

						}
					},
					plotOptions: {
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showModalLokasi(this.category,this.series.name,result.category);
									}
								}
							},
							dataLabels: {
								enabled: false,
								format: '{point.y}'
							}
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},

					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [
					{
						name: 'Temuan Open',
						data: series_hh,
						color : '#b22a00'
					},{
						name: 'Temuan Progress',
						data: series_open,
						color : '#f39c12'
					},{
						name: 'Temuan Close',
						data: series_close,
						color : '#357a38'
					}
					]
				});
				$('#detailTim').DataTable().clear();
				$('#detailTim').DataTable().destroy();
				$("#detailbodytable").empty();
				var body = '';
				$.each(result.grafik_update, function(index, value){
					body += '<tr>';
					body += '<td style="text-align: center">'+value.department_short+'</td>';
					body += '<td style="text-align: center">'+value.jumlah+'</td>';
					body += '</tr>';
				})
				$("#detailbodyTim").append(body);
			}
		}
	});
}
function CekIni(value){
	var q = $("#id_soal").val();
	var url = '{{ url("data_file/pengisian_ky") }}/'+q+'-'+value+'.pdf';
	$("#modal_report").html('<iframe src="'+url+'" width="100%" height="800px"></iframe>');
	$("#ModalReport").modal('show');
}
function OpenModalBelumMengisi(id, kode_soal){
	$("#ModalKonfirmasiTim").modal('show');
	var data = {
		id:id,
		kode_soal:kode_soal
	}
	$.get('<?php echo e(url("fetch/detail/jumlah/tim")); ?>', data, function(result, status, xhr){
		$('#nama_tim').html(result.nm[0].nama_tim);
		if(result.status){
			$('#TableKehadiran').DataTable().clear();
			$('#TableKehadiran').DataTable().destroy();
			$('#BodyTableKehadiran').html("");
			var tableData = "";
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
				tableData += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
				if (value.remark ==  null) {
					tableData += '<td style="text-align: center; background-color: RGB(255,204,255)">Tidak Hadir</td>';
				}else{
					tableData += '<td style="text-align: center; background-color: RGB(204,255,255)">'+ value.remark +'</td>';
				}
				tableData += '</tr>';
			});
			$('#BodyTableKehadiran').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}
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

function DetailPie(category){
	$("#ModalPresentasi").modal('show');
	$("#judul_report").html(category);
	var kode_soal = $('#id_soal').val();
	var periode = $('#bulan_monitoring').val();
	
	var data = {
		category:category,
		kode_soal:kode_soal,
		periode:periode
	}
	$.get('<?php echo e(url("fetch/data/presentase")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#DetailPresentasi').DataTable().clear();
			$('#DetailPresentasi').DataTable().destroy();
			$('#BodyDetailPresentasi').html("");
			var tableData = "";
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
				tableData += '<td style="text-align: center;">'+ value.nama +'</td>';
				tableData += '</tr>';
			});
			$('#BodyDetailPresentasi').append(tableData);

			var table = $('#DetailPresentasi').DataTable({
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
		}
		else{
			alert('Maaf Data Tidak Ditemukan');
		}
	});
}

function ChartUpdate(value) {
	$("#show_detail").hide();
	var data = {
		bulan:value
	}
	$.get('{{ url("fetch/resume/ky") }}', data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){
				$("#loading").hide();
				var data_1 = [];
				var data_2 = [];
				data_1.push(result.data_1[0].jumlah);
				data_2.push(result.data_2[0].jumlah);
				var sudah = parseInt(data_2);
				var belum = parseInt(data_1)-parseInt(data_2);
				// Highcharts.chart('highchart1', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'pie',
				// 		options3d: {
				// 			enabled: true,
				// 			alpha: 45,
				// 			beta: 0
				// 		},
				// 	},
				// 	title: {
				// 		text: 'Resume Pengisian KYT'
				// 	},
				// 	accessibility: {
				// 		point: {
				// 			valueSuffix: '%'
				// 		}
				// 	},
				// 	legend: {
				// 		enabled: false,
				// 		symbolRadius: 1,
				// 		borderWidth: 1
				// 	},
				// 	credits:{
				// 		enabled:false
				// 	},
				// 	tooltip: {
				// 		pointFormat: '{series.data.name}<b>{point.percentage:.1f}%</b>'
				// 	},
				// 	plotOptions: {
				// 		pie: {
				// 			allowPointSelect: true,
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 					}
				// 				}
				// 			},
				// 			edgeWidth: 1,
				// 			edgeColor: '#e3dede',
				// 			depth: 35,
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '<b>{point.name}<br>{point.y} Karyawan</b><br>{point.percentage:.1f} %',
				// 				style:{
				// 					fontSize:'0.7vw',
				// 					textOutline:0
				// 				},
				// 				color:'black',
				// 				connectorWidth: '3px'
				// 			},
				// 			showInLegend: true,
				// 		}
				// 	},
				// 	series: [{
				// 		name: 'Brands',
				// 		colorByPoint: true,
				// 		data: [{
				// 			name: 'Sudah Mengisi',
				// 			y: 500,
				// 			sliced: true,
				// 			selected: true,
				// 			color : '#68C5DB'
				// 		}, {
				// 			name: 'Belum Mengisi',
				// 			y: belum,
				// 			color : '#E8871E'
				// 		}]
				// 	}]
				// });

				var all_team = result.jumlah_all;
				var persen_sudah = (result.jumlah_sudah/all_team)*100;
				var persen_belum = (result.jumlah_belum/all_team)*100;
				var jml_sudah = result.jumlah_sudah;
				var jml_belum = result.jumlah_belum;

				Highcharts.chart('resume_all', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45,
							beta: 0
						},
					},
					title: {
						text: 'Presentasi Pengisian KYT'
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					legend: {
						enabled: false,
						symbolRadius: 1,
						borderWidth: 1
					},
					credits:{
						enabled:false
					},
					tooltip: {
						pointFormat: '{series.data.name}<b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										DetailPie(this.name);
									}
								}
							},
							edgeWidth: 1,
							edgeColor: '#e3dede',
							depth: 35,
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}<br>{point.a} Tim</b><br>{point.percentage:.1f} %',
								style:{
									fontSize:'0.7vw',
									textOutline:0
								},
								color:'black',
								connectorWidth: '3px'
							},
							showInLegend: true,
						}
					},
					series: [{
						name: 'Brands',
						colorByPoint: true,
						data: [{
							name: 'Sudah Mengisi',
							y: persen_sudah,
							sliced: true,
							selected: true,
							color : '#FED766',
							a : jml_sudah
						}, {
							name: 'Belum Mengisi',
							y: persen_belum,
							color : '#2AB7CA',
							a : jml_belum
						}]
					}]
				});

				$.get('{{ url("fetch/resume/index") }}', data, function(result, status, xhr) {
					$('#tableResume').DataTable().clear();
					$('#tableResume').DataTable().destroy();
					$("#bodyResume").empty();
					var body = '';
					$.each(result.resume, function(index, value){
						var report = '{{ url("data_file/pengisian_ky")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						body += "<td>"+value.nama_tim+"</td>";
						body += "<td>"+value.nama+"</td>";
						body += "<td>"+value.posisi+"</td>";
						body += "<td>"+value.department+"</td>";
						body += "<td style='height: 10px; padding-bottom: 10px'>"+value.jml_tim+"<br><button type='button' class='btn btn-primary btn-xs' onclick='OpenModalBelumMengisi(\""+value.id+"\", \""+value.kode_soal+"\")'><i class='fa fa-eye'></i> LIHAT</button></td>";
						if (value.nama_tim == result.cek_tim[0].nama_tim) {
							if (result.open_soal > 0) {
								if (value.remark != null) {
									body += "<td><a href='"+report+"/"+value.kode_soal+"-"+value.nama_tim+".pdf' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
								}else{
									body += "<td><a href='{{ url("index/soal/ky") }}' class='btn btn-success btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Kerjakan Soal</a></td>";
								}
							}else{
								body += "<td><button type='button' class='btn btn-danger btn-xs'><i class='fa fa-times-circle-o'></i> Tidak Ada Soal</button></td>";
							}	
						}else{
							if (result.username == 'PI2101044' && value.remark != null) {
								body += "<td><a href='"+report+"/"+value.kode_soal+"-"+value.nama_tim+".pdf' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
							}else{
								body += "<td>-</td>";
							}
						}
						body += "<td>"+(value.score || 'Belum Mengisi')+"</td>";
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
					$('#tableResumeHH').DataTable().clear();
					$('#tableResumeHH').DataTable().destroy();
					$("#bodyResumeHH").empty();
					var body = '';
					$.each(result.resume2, function(index, value){
						var report = '{{ url("data_file/pengisian_ky")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						body += "<td>"+value.nama_tim+"</td>";
						body += "<td>"+value.nama+"</td>";
						body += "<td>"+value.posisi+"</td>";
						body += "<td>"+value.department+"</td>";
						body += "<td><button type='button' class='btn btn-success btn-xs' onclick='Hiyarihato()'><i class='fa fa-check-square-o'></i> Form HH</button></td>";
						body += "</tr>";
					})
					$("#bodyResumeHH").append(body);
					var table = $('#tableResumeHH').DataTable({
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
					$('#tableReportHh').DataTable().clear();
					$('#tableReportHh').DataTable().destroy();
					$("#bodyReportHh").empty();
					var body = '';
					$.each(result.resume3, function(index, value){
						var karyawan = value.karyawan.split('/');
						var level = value.level.split('/');
						var report = '{{ url("data_file/pengisian_hh")}}';
						var penanganan = '{{ url("index/penanganan/hiyarihatto")}}';
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						if (value.remark == 'Open') {
							body += "<td>"+value.request_id+"<br><span class='label label-danger' style=color: white>OPEN</span></td>";
						}else{
							body += "<td>"+value.request_id+"<br><span class='label label-success' style=color: white>CLOSE</span></td>";
						}
						body += "<td>"+level[2]+"</td>";
						body += "<td>("+karyawan[0]+")<br>"+karyawan[1]+"</td>";
						body += "<td>"+value.tanggal+"</td>";
						body += "<td>"+value.lokasi+"</td>";
						if (value.remark == 'Open') {
							body += "<td><a href='"+penanganan+"/"+value.request_id+"/"+value.id_ketua+"' target='_blank' class='btn btn-danger btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-pencil' aria-hidden='true'></i> Penanganan</a></td>";
						}else{
							body += "<td><a href='"+report+"/"+value.request_id+".pdf' target='_blank' class='btn btn-success btn-xs' style='margin-left: 5px; color: white;'><i class='fa fa-check-square-o'></i> Report</a></td>";
						}
						body += "</tr>";
					})
					$("#bodyReportHh").append(body);
					var table = $('#tableReportHh').DataTable({
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
}
});
}
</script>
@endsection