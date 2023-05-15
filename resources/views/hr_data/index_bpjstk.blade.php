@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.onoffswitch2-inner {
		display: block; width: 200%; margin-left: -100%;
		-moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
		-o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
	}

	.onoffswitch2-inner:before, .onoffswitch2-inner:after {
		display: block; float: left; width: 50%; height: 40px; line-height: 40px;
		font-size: 12px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
		-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
	}

	.onoffswitch2-inner:before {
		content: "Order Makan Ramadhan";
		padding-right: 10px;
		font-size: 20px;
		text-align: center;
		background-color: #2196F3; color: #FFFFFF;
	}

	.onoffswitch2-inner:after {
		content: "Order Extra Food";
		background-color: #00a65a;; color: #FFFFFF;
		font-size: 20px;
		text-align: center;
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
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait .. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
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
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12" style="padding-bottom: 10px">
				<a class="btn btn-md pull-right" style="color:white;background-color: #a16eac;" onclick="TambahPeserta()"><i class="fa fa-users"></i>&nbsp;&nbsp;Tambah Anggota Keluarga</a>
				<!-- <a class="btn btn-md pull-left" style="color:white;background-color: #5ba6ec;" onclick="InsertDB()"><i class="fa fa-users"></i>&nbsp;&nbsp;Test Insert</a> -->
			</div>
		</div>
	</div>

	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12" style="text-align: center;">
				<h1 style="background-color: #a1887f; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
					Data Keluarga Keikutsertaan BPJS
				</h1>
			</div>
		</div>
	</div>

	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-6">
				<center><span style="color: black; font-weight: bold; font-size: 3vw;">KELUARGA INTI</span></center>
			</div>
			<div class="col-xs-6">
				<center><span style="color: black; font-weight: bold; font-size: 3vw;">KELUARGA TAMBAHAN</span></center>
			</div>
		</div>
	</div>

	<div class="col-xs-12" style="padding-bottom: 10px">
		<div class="row">
			<div class="col-xs-6 col-xs-12" style="padding:2px;padding-top:0px">
				<div class="col-xs-4" style="margin-left: 10px; padding: 2px; width:22%;">
				</div>
				<div class="col-xs-4" style="margin-left: 15px; padding: 2px; width:48%;">
					<div class="small-box bg-green" style="font-size: 20px;font-weight: bold;height: 153px; cursor: pointer;" onclick="DataCustomField('done')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b></b></h3>
							<h2 style="padding-left: 35px; margin: 10px;font-size: 5vw; color: white;" id='total_inti'>0</h2>
						</div>
						<div class="icon" style="padding-top: 35px;">
							<i class="fa fa-users"></i>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-6 col-xs-12" style="padding:2px;padding-top:0px"> 
				<div class="col-xs-4" style="margin-left: 10px; padding: 2px; width:22%;">
				</div>
				<div class="col-xs-4" style="margin-left: 15px; padding: 2px; width:48%;">
					<div class="small-box" style="background: #ff9800; font-size: 20px;font-weight: bold;height: 153px;cursor: pointer;" onclick="DataCustomField('ppp')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw; color: white;"><b></b></h3>
							<h2 style="padding-left: 35px; margin: 10px;font-size: 5vw; color: white;" id='total_tambahan'>0</h2>
						</div>
						<div class="icon" style="padding-top: 35px;">
							<i class="fa fa-users"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<!-- /ISIKAN DISINI/ -->
								<div class="col-xs-12" style="text-align: center;">
									<table id="TableDetailCustomField" class="table table-bordered table-striped table-hover">
										<thead style="background-color: #BDD5EA; color: black;">
											<tr>
												<th width="5%" style="text-align: center;">No</th>
												<th width="15%" style="text-align: center;">Nama</th>
												<th width="10%" style="text-align: center;">No BPJS</th>
												<th width="10%" style="text-align: center;">No KTP</th>
												<th width="10%" style="text-align: center;">Hubungan</th>
												<th width="10%" style="text-align: center;">TTL</th>
												<th width="5%" style="text-align: center;">Jenis Kelamin</th>
												<th width="10%" style="text-align: center;">Alamat</th>
												<th width="10%" style="text-align: center;">Nama Faskes</th>
												<th width="10%" style="text-align: center;">Kelas Rawat</th>
												<th width="5%" style="text-align: center;">#</th>
											</tr>
										</thead>
										<tbody id="bodyTableDetailCustomField">
										</tbody>
										<tfoot>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Form Penambahan Data Keluarga BPJS
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<center>
								<h3 style="font-weight: bold; padding: 3px; margin-top: 0; color: black;">
									Data Karyawan
								</h3>
							</center>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Keluarga Dari<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<center>
								<h3 style="font-weight: bold; padding: 3px; margin-top: 0; color: black;">
									Data Yang Ingin Ditambahkan
								</h3>
							</center>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No BPJS<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="no_bpjs" pattern="\d*" maxlength="16" placeholder="Nomor Kartu BPJS. Contoh : 35730112323...">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No KK<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<!-- <input type="text" class="form-control" id="no_kk" placeholder="No KK"> -->
									<input type="text" pattern="\d*" maxlength="16" class="form-control" id="no_kk" name="no_kk" placeholder="Nomor Kartu Keluarga. Contoh : 35730112323..." required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Upload KK<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="file" accept="image/*" id="upload_kk" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No KTP<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<!-- <input type="text" class="form-control" id="no_ktp" placeholder="No KTP"> -->
									<input type="text" pattern="\d*" maxlength="16" class="form-control" id="no_ktp" name="no_ktp" placeholder="Nomor Induk Kependudukan. Contoh : 35730112323..." required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="nama_keluarga" placeholder="Nama" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Hubungan<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divHubunganKeluarga">
									<select class="form-control" id="hubungan_keluarga" data-placeholder='Hubungan Keluarga' style="width: 100%" required>
										<option value="">&nbsp;</option>
										@if($emp->gender == 'L')
										<option value="KELUARGA TAMBAHAN ISTRI">ISTRI</option>
										@else
										<option value="KELUARGA TAMBAHAN SUAMI">SUAMI</option>
										@endif
										<option value="KELUARGA TAMBAHAN ANAK">ANAK</option>
										<option value="KELUARGA TAMBAHAN ORANG TUA/MERTUA">KELUARGA TAMBAHAN ORANG TUA/MERTUA</option>
										<option value="ANGGOTA">KARYAWAN YMPI</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tempat Lahir<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="tempat_lahir" placeholder="Tempat Lahir" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Lahir<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<div class="input-group">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" id="tanggal_lahir" name="tanggal_lahir" class="form-control datepicker" style="width: 50%; text-align: center;" placeholder="Tanggal Lahir" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jenis Kelamin<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divJenisKelamin">
									<select class="form-control select2" id="jenis_kelamin" data-placeholder='Jenis Kelamin' style="width: 100%" required>
										<option value="">&nbsp;</option>
										<option value="L">LAKI - LAKI</option>
										<option value="P">PEREMPUAN</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Status Kawin<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divStatus">
									<select class="form-control select2" id="status_perkawinan" data-placeholder='Status Perkawinan' style="width: 100%" required>
										<option value="">&nbsp;</option>
										<option value="Belum Kawin">Belum Kawin</option>
										<option value="Kawin">Kawin</option>
										<option value="Cerai">Cerai</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Alamat<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="alamat" placeholder="Alamat" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">RT/RW<span class="text-red"> * :</span></label>
								<div class="col-sm-4">
									<input type="text" pattern="\d*" maxlength="3" class="form-control" id="rt" placeholder="Contoh : 001" required>
								</div>
								<div class="col-sm-4">
									<input type="text" pattern="\d*" maxlength="3" class="form-control" id="rw" placeholder="Contoh : 002" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kode Pos<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" pattern="\d*" maxlength="5" class="form-control" id="kode_pos" placeholder="Kode Pos" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kelurahan<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kelurahan" placeholder="Kelurahan" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kecamatan<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kecamatan" placeholder="Kecamatan" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama Faskes Tk. 1<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="nama_faskes" placeholder="Nama Faskes" required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kelas Rawat<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kelas_rawat" value="Kelas 1" readonly>
								</div>
							</div>
							<a class="btn btn-primary pull-right" id="addCartBtn" onclick="TambahAnggota()">Tambah Anggota<br>カートに追加 <i class="fa fa-user"></i></a>
						</div>
					</form>
					<div class="col-xs-12" style="padding-top: 10px;">
						<div class="row">
							<span style="font-weight: bold; font-size: 1.2vw;"><i class="fa fa-user"></i> List Yang Ditambahkan</span>
							<table class="table table-hover table-bordered table-striped" id="tableTambahAnggota">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%;">No</th>
										<th style="width: 5%;">Nama</th>
										<th style="width: 5%;">No BPJS</th>
										<th style="width: 5%;">Keterangan</th>
										<th style="width: 3%;">Aksi</th>
									</tr>
								</thead>
								<tbody id="bodytableTambahAnggota" style="background-color: RGB(252, 248, 227);">
								</tbody>
							</table>
						</div>
					</div>
					<input type="hidden" id="jumlah_pengajuan">
					<button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Kembali</button>
					<button class="btn btn-success pull-right" id="tombol_kirim_pengajuan" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="KirimPengajuan()">Kirim Pengajuan Ke HR</i></button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Edit Detail Anggota Keluarga
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" class="form-control" id="id_update">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<center>
								<h3 style="font-weight: bold; padding: 3px; margin-top: 0; color: black;">
									Data Karyawan
								</h3>
							</center>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No BPJS<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="no_bpjs_update" pattern="\d*" maxlength="16" placeholder="Nomor Kartu BPJS. Contoh : 35730112323...">
								</div>
							</div>
							<center>
								<h3 style="font-weight: bold; padding: 3px; margin-top: 0; color: black;">
									Data Keluarga Tambahan
								</h3>
							</center>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No KK<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<!-- <input type="text" class="form-control" id="no_kk_update" placeholder="No KK"> -->
									<input type="text" pattern="\d*" maxlength="16" class="form-control" id="no_kk_update" name="no_kk_update" placeholder="Nomor Kartu Keluarga. Contoh : 35730112323..." required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No KTP<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<!-- <input type="text" class="form-control" id="no_ktp_update" placeholder="No KTP"> -->
									<input type="text" pattern="\d*" maxlength="16" class="form-control" id="no_ktp_update" name="no_ktp_update" placeholder="Nomor Induk Kependudukan. Contoh : 35730112323..." required>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="nama_keluarga_update" placeholder="Nama">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Hubungan<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divHubunganKeluargaUpdate">
									<select class="form-control" id="hubungan_keluarga_update" data-placeholder='Hubungan Keluarga' style="width: 100%" required>
										<option value="">&nbsp;</option>
										@if($emp->gender == 'L')
										<option value="KELUARGA TAMBAHAN ISTRI">ISTRI</option>
										@else
										<option value="KELUARGA TAMBAHAN SUAMI">SUAMI</option>
										@endif
										<option value="KELUARGA TAMBAHAN ANAK">ANAK</option>
										<option value="KELUARGA TAMBAHAN ORANG TUA/MERTUA">KELUARGA TAMBAHAN ORANG TUA/MERTUA</option>
										<option value="ANGGOTA">KARYAWAN YMPI</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tempat Lahir<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="tempat_lahir_update" placeholder="Tempat Lahir">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Lahir<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<div class="input-group">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" id="tanggal_lahir_update" name="tanggal_lahir_update" class="form-control datepicker" style="width: 50%; text-align: center;" placeholder="Tanggal Lahir" required="">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jenis Kelamin<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divJenisKelaminUpdate">
									<select class="form-control select2" id="jenis_kelamin_update" data-placeholder='Jenis Kelamin' style="width: 100%" required>
										<option value="">&nbsp;</option>
										<option value="L">LAKI - LAKI</option>
										<option value="P">PEREMPUAN</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Status Kawin<span class="text-red"> * :</span></label>
								<div class="col-sm-9" id="divStatusUpdate">
									<select class="form-control select2" id="status_perkawinan_update" data-placeholder='Status Perkawinan' style="width: 100%" required>
										<option value="">&nbsp;</option>
										<option value="Belum Kawin">Belum Kawin</option>
										<option value="Kawin">Kawin</option>
										<option value="Cerai">Cerai</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Alamat<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="alamat_update" placeholder="Alamat">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">RT/RW<span class="text-red"> * :</span></label>
								<div class="col-sm-2">
									<input type="text" pattern="\d*" maxlength="3" class="form-control" id="rt_update" placeholder="Contoh : 001">
								</div>
								<div class="col-sm-2">
									<input type="text" pattern="\d*" maxlength="3" class="form-control" id="rw_update" placeholder="Contoh : 002">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kode Pos<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kode_pos_update" placeholder="Kode Pos">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kelurahan<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kelurahan_update" placeholder="Kelurahan">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kecamatan<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kecamatan_update" placeholder="Kecamatan">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama Faskes Tk. 1<span class="text-red"> * :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="nama_faskes_update" placeholder="Nama Faskes">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kelas Rawat<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="kelas_rawat_update" value="Kelas 1" readonly>
								</div>
							</div>
							<button type="button" class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Kembali</button>
							<button type="button" class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="SimpanPerubahan()">Simpan Perubahan</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalKartuKeluarga" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="judul_kk">
					</h3>
					<div id="gambar_kk"></div><br>
					<button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1vw; width: 40%;">Kembali</button>
				</center>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$(function () {
			$('#hubungan_keluarga').select2({
				dropdownParent: $('#divHubunganKeluarga'),
				allowClear:true
			});
		});
		$(function () {
			$('#jenis_kelamin').select2({
				dropdownParent: $('#divJenisKelamin'),
				allowClear:true
			});
		});
		$(function () {
			$('#status_perkawinan').select2({
				dropdownParent: $('#divStatus'),
				allowClear:true
			});
		});
		// $(function () {
		// 	$('#kelas_rawat').select2({
		// 		dropdownParent: $('#divKelasRawat'),
		// 		allowClear:true
		// 	});
		// });

		$(function () {
			$('#hubungan_keluarga_update').select2({
				dropdownParent: $('#divHubunganKeluargaUpdate'),
				allowClear:true
			});
		});
		$(function () {
			$('#jenis_kelamin_update').select2({
				dropdownParent: $('#divJenisKelaminUpdate'),
				allowClear:true
			});
		});
		$(function () {
			$('#status_perkawinan_update').select2({
				dropdownParent: $('#divStatusUpdate'),
				allowClear:true
			});
		});
		// $(function () {
		// 	$('#kelas_rawat_update').select2({
		// 		dropdownParent: $('#divKelasRawatUpdate'),
		// 		allowClear:true
		// 	});
		// });

		Count();
		DataCustomField('all');
		// $('#tanggal_lahir').datepicker({
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd"
		// });
		$(".datepicker").datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
		});
	});

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

	function Count(){
		$.get('{{ url("fetch/data/bpjs") }}', function(result, status, xhr){
			if(result.status){
				var count_inti = parseInt(result.keluarga_inti);
				var count_tambahan = parseInt(result.keluarga_tambahan);

				$('#total_inti').html(count_inti + '<sup style="font-size: 2vw"> Orang</sup>');
				$('#total_tambahan').html(count_tambahan + '<sup style="font-size: 2vw"> Orang</sup>');
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function DetailUser(param){
		$("#modalEdit").modal('show');
		var data = {
			param : param
		}
		$.get('{{ url("fetch/data/update/bpjs") }}', data, function(result, status, xhr){
			if(result.status){
				$('#id_update').val(param);
				$('#nama_keluarga_update').val(result.data[0].name);
				$('#no_bpjs_update').val(result.data[0].bpjs_number);
				$('#no_kk_update').val(result.data[0].no_kk);
				$('#no_ktp_update').val(result.data[0].no_ktp);
				$('#tempat_lahir_update').val(result.data[0].tempat_lahir);
				$('#tanggal_lahir_update').val(result.data[0].tanggal_lahir);
				$('#alamat_update').val(result.data[0].alamat);
				$('#rt_update').val(result.data[0].rt);
				$('#rw_update').val(result.data[0].rw);
				$('#kode_pos_update').val(result.data[0].kode_post);
				$('#kecamatan_update').val(result.data[0].kecamatan);
				$('#kelurahan_update').val(result.data[0].kelurahan);
				$('#nama_faskes_update').val(result.data[0].nama_faskes);
				$('#kelas_rawat_update').val('Kelas 1');
				$('#hubungan_keluarga_update').val(result.data[0].hubungan).trigger('change');
				$('#jenis_kelamin_update').val(result.data[0].jenis_kelamin).trigger('change');
				$('#status_perkawinan_update').val(result.data[0].status_kawin).trigger('change');
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function SimpanPerubahan(){
		var id_update = $('#id_update').val();
		var nama_keluarga_update = $('#nama_keluarga_update').val();
		var hubungan_keluarga_update = $('#hubungan_keluarga_update').val();
		var no_bpjs_update = $('#no_bpjs_update').val();
		var no_kk_update = $('#no_kk_update').val();
		var no_ktp_update = $('#no_ktp_update').val();
		var tempat_lahir_update = $('#tempat_lahir_update').val();
		var tanggal_lahir_update = $('#tanggal_lahir_update').val();
		var jenis_kelamin_update = $('#jenis_kelamin_update').val();
		var status_perkawinan_update = $('#status_perkawinan_update').val();
		var alamat_update = $('#alamat_update').val();
		var rt_update = $('#rt_update').val();
		var rw_update = $('#rw_update').val();
		var kode_pos_update = $('#kode_pos_update').val();
		var kecamatan_update = $('#kecamatan_update').val();
		var kelurahan_update = $('#kelurahan_update').val();
		var nama_faskes_update = $('#nama_faskes_update').val();
		var kelas_rawat_udpate = $('#kelas_rawat_udpate').val();

		var data = {
			id_update : id_update,
			nama_keluarga_update : nama_keluarga_update,
			hubungan_keluarga_update : hubungan_keluarga_update,
			no_bpjs_update : no_bpjs_update,
			no_kk_update : no_kk_update,
			no_ktp_update : no_ktp_update,
			tempat_lahir_update : tempat_lahir_update,
			tanggal_lahir_update : tanggal_lahir_update,
			jenis_kelamin_update : jenis_kelamin_update,
			status_perkawinan_update : status_perkawinan_update,
			alamat_update : alamat_update,
			rt_update : rt_update,
			rw_update : rw_update,
			kode_pos_update : kode_pos_update,
			kecamatan_update : kecamatan_update,
			kelurahan_update : kelurahan_update,
			nama_faskes_update : nama_faskes_update,
			kelas_rawat_udpate : kelas_rawat_udpate
		}

		$.post('{{ url("update/data/detail/bpjs") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modalEdit").modal('hide');
				openSuccessGritter('Success','Data Berhasil Dirubah!');
				DataCustomField('all');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function LihatKK(id, upload_kk){
		var data = {
			id : id
		}
		$.get('{{ url("open/kk") }}', data, function(result, status, xhr){
			if(result.status){
				console.log(upload_kk);
				var url = "{{ url('hr_bpjs') }}/"+upload_kk;
				// openSuccessGritter('Success','Data Ditemukan!');
				$("#ModalKartuKeluarga").modal('show');
				$("#judul_kk").html('KARTU KELUARGA');
				$("#gambar_kk").html('<img src="'+url+'" style="width: 85%">');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function DeleteUser(id){
		var data = {
			id : id
		}
		$.post('{{ url("delete/data/tambahan") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Data Berhasil Dihapus!');
				location.reload();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DataCustomField(data){
		var data = {
			data : data
		}
		$.get('{{ url("fetch/data/bpjs") }}', data, function(result, status, xhr){
			if(result.status){
				$('#TableDetailCustomField').DataTable().clear();
				$('#TableDetailCustomField').DataTable().destroy();
				$('#bodyTableDetailCustomField').html("");
				var tableData = "";
				var index = 1;

				$.each(result.resumes, function(key, value){

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td style="text-align: center">'+value.name+'</td>';
					tableData += '<td style="text-align: center">'+(value.bpjs_number||'')+'</td>';
					// tableData += '<td style="text-align: center">'+value.no_ktp+'</td>';

					tableData += '<td style="text-align: center">';
					tableData += ''+value.no_ktp+'';
					if (value.hubungan == 'Keluarga Tambahan') {
						tableData += '<br><button type="button" class="btn btn-success btn-xs" onclick="LihatKK(\''+value.id+'\', \''+value.upload_kk+'\')">Lihat KK</button>';
					}
					tableData += '</td>';

					// tableData += '<td style="text-align: center">'+value.hubungan+'</td>';

					tableData += '<td style="text-align: center">';
					if (value.hubungan == 'Keluarga Tambahan') {
						tableData += '<button type="button" class="btn btn-warning btn-xs">KELUARGA TAMBAHAN</button>';
					}else{
						tableData += ''+value.hubungan+'';
					}
					tableData += '</td>';

					tableData += '<td style="text-align: center">'+value.tempat_lahir+', '+value.tanggal_lahir+'</td>';
					tableData += '<td style="text-align: center">'+value.jenis_kelamin+'</td>';
					if (value.rt == null) {
						tableData += '<td style="text-align: center">'+value.alamat+'</td>';
					}else{
						tableData += '<td style="text-align: center">'+value.alamat+', '+value.rt+'-'+value.rw+', '+value.kelurahan+', '+value.kecamatan+'</td>';
					}
					tableData += '<td style="text-align: center">'+(value.nama_faskes||'')+'</td>';
					tableData += '<td style="text-align: center">'+value.kelas_rawat+'</td>';
					if (value.status == 0) {
						tableData += '<td style="text-align: center">';
						tableData += '<span class="label label-danger">Menunggu Konfirmasi HR</span><br><br>';
						tableData += '<button type="button" class="btn btn-primary btn-xs" onclick="DetailUser(\''+value.id+'\')"> Update</button>&nbsp&nbsp';
						tableData += '<button type="button" class="btn btn-danger btn-xs" onclick="DeleteUser(\''+value.id+'\')"> Hapus</button>';
						tableData += '</td>';
					}else{
						tableData += '<td style="text-align: center">';
						tableData += '<span class="label label-success">Disetujui HR</span><br><br>';
						tableData += '<button type="button" class="btn btn-primary btn-xs" onclick="DetailUser(\''+value.id+'\')"> Update</button>';
						tableData += '</td>';
					}
					tableData += '</tr>';

				});
				$('#bodyTableDetailCustomField').append(tableData);

				var table = $('#TableDetailCustomField').DataTable({
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

	function PerbaruiTambahAnggota(){
		var jenis = 'waiting';
		var data = {
			jenis : jenis
		}
		$.get('{{ url("fetch/data/bpjs") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableTambahAnggota').DataTable().clear();
				$('#tableTambahAnggota').DataTable().destroy();
				$('#bodytableTambahAnggota').html("");
				var tableData = "";
				var index = 1;

				$.each(result.resumes, function(key, value){

					tableData += '<tr>';
					tableData += '<td>'+ index++ +'</td>';
					tableData += '<td>'+value.name+'</td>';
					tableData += '<td>'+value.bpjs_number+'</td>';
					tableData += '<td>'+value.remark+'</td>';
					// tableData += '<td><button type="button" class="btn btn-danger btn-xs" onclick="DeleteUser(\''+value.id+'\')"> Hapus</button></td>';
					tableData += '<td style="text-align: center">';
					tableData += '<span class="label label-danger">Menunggu Konfirmasi HR</span><br><br>';
					tableData += '<button type="button" class="btn btn-danger btn-xs" onclick="DeleteUser(\''+value.id+'\')"> Hapus</button>';
					tableData += '</td>';
					tableData += '</tr>';

				});
				$('#bodytableTambahAnggota').append(tableData);

				if (result.resumes.length == 0) {
					$("#tombol_kirim_pengajuan").hide();
				}else{
					$("#tombol_kirim_pengajuan").show();
				}
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function TambahPeserta(){
		$("#modalCreate").modal('show');
		PerbaruiTambahAnggota();

		// var jumlah = $("#jumlah_pengajuan").val();

		// if (jumlah == 0) {
		// 	$("#tombol_kirim_pengajuan").hide();
		// }else{
		// 	$("#tombol_kirim_pengajuan").show();
		// }

		// $.get('{{ url("cek/select/keluarga") }}', function(result, status, xhr) {
		// 	if(result.status){
		// 		openSuccessGritter('Success','Data Ditemukan!');
		// 	}else{
		// 		openErrorGritter('Error!', result.message);
		// 	}
		// });
	}

	function InsertDB(){
		$.post('{{ url("insert/bpjskes") }}', function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','Data Berhasil Ditambahkan!');
				DataCustomField('all');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function TambahAnggota(){
		// var nama_keluarga = $('#nama_keluarga').val();
		// var hubungan_keluarga = $('#hubungan_keluarga').val();
		// var no_bpjs = $('#no_bpjs').val();
		// var no_kk = $('#no_kk').val();
		// var no_ktp = $('#no_ktp').val();
		// var tempat_lahir = $('#tempat_lahir').val();
		// var tanggal_lahir = $('#tanggal_lahir').val();
		// var jenis_kelamin = $('#jenis_kelamin').val();
		// var status_perkawinan = $('#status_perkawinan').val();
		// var alamat = $('#alamat').val();
		// var rt = $('#rt').val();
		// var rw = $('#rw').val();
		// var kode_pos = $('#kode_pos').val();
		// var kecamatan = $('#kecamatan').val();
		// var kelurahan = $('#kelurahan').val();
		// var nama_faskes = $('#nama_faskes').val();
		// var kelas_rawat = $('#kelas_rawat').val();
		// var upload_kk = $('#upload_kk').prop('files')[0];

		// var data = {
		// 	no_bpjs : no_bpjs,
		// 	no_kk : no_kk,
		// 	no_ktp : no_ktp,
		// 	nama_keluarga : nama_keluarga,
		// 	hubungan_keluarga : hubungan_keluarga,
		// 	tempat_lahir : tempat_lahir,
		// 	tanggal_lahir : tanggal_lahir,
		// 	jenis_kelamin : jenis_kelamin,
		// 	status_perkawinan : status_perkawinan,
		// 	alamat : alamat,
		// 	rt : rt,
		// 	rw : rw,
		// 	kode_pos : kode_pos, 
		// 	kecamatan : kecamatan,
		// 	kelurahan : kelurahan,
		// 	nama_faskes : nama_faskes,
		// 	kelas_rawat : kelas_rawat,
		// 	upload_kk : upload_kk
		// }
		var hubungan = $("#hubungan_keluarga").val();
		var no_kk = $("#no_kk").val();
		var upload_kk = $("#upload_kk").val();
		var no_ktp = $("#no_ktp").val();
		var nama_keluarga = $("#nama_keluarga").val();
		var hubungan_keluarga = $("#hubungan_keluarga").val();
		var tempat_lahir = $("#tempat_lahir").val();
		var tanggal_lahir = $("#tanggal_lahir").val();
		var jenis_kelamin = $("#jenis_kelamin").val();
		var status_perkawinan = $("#status_perkawinan").val();
		var alamat = $("#alamat").val();
		var rt = $("#rt").val();
		var rw = $("#rw").val();
		var kode_pos = $("#kode_pos").val();
		var kelurahan = $("#kelurahan").val();
		var kecamatan = $("#kecamatan").val();
		var nama_faskes = $("#nama_faskes").val();

		var formData = new FormData();

		formData.append('nama_keluarga', $("#nama_keluarga").val());
		formData.append('hubungan_keluarga', $("#hubungan_keluarga").val());
		formData.append('no_kk', $("#no_kk").val());
		formData.append('no_bpjs', $("#no_bpjs").val());
		formData.append('no_ktp', $("#no_ktp").val());
		formData.append('tempat_lahir', $("#tempat_lahir").val());
		formData.append('tanggal_lahir', $("#tanggal_lahir").val());
		formData.append('jenis_kelamin', $("#jenis_kelamin").val());
		formData.append('status_perkawinan', $("#status_perkawinan").val());
		formData.append('alamat', $("#alamat").val());
		formData.append('rt', $("#rt").val());
		formData.append('rw', $("#rw").val());
		formData.append('kode_pos', $("#kode_pos").val());
		formData.append('kelurahan', $("#kelurahan").val());
		formData.append('kecamatan', $("#kecamatan").val());
		formData.append('nama_faskes', $("#nama_faskes").val());
		formData.append('kelas_rawat', $("#kelas_rawat").val());
		formData.append('upload_kk', $("#upload_kk").prop('files')[0]);

		console.log(hubungan);
		if (hubungan == '' || hubungan == '' || no_kk == '' || upload_kk == '' || no_ktp == '' || nama_keluarga == '' || hubungan_keluarga == '' || tempat_lahir == '' || tanggal_lahir == '' || jenis_kelamin == '' || status_perkawinan == '' || alamat == '' || rt == '' || rt == '0000' || rw == '' || rw == '0000' || kode_pos == '' || kelurahan == '' || kecamatan == '' || nama_faskes == '') {
			openErrorGritter('Error!', 'Data Gagal Ditambahkan!');
		}else{
				$.ajax({
				url:"{{ url('insert/bpjskes/detail') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {
					if (response.status) {
					// $("#loading").hide();
						openSuccessGritter('Success','Data Berhasil Ditambahkan!');
						PerbaruiTambahAnggota();
						DataCustomField('all');
						$('#nama_keluarga').val('');
						$('#no_bpjs').val('');
						$('#no_kk').val('');
						$('#no_ktp').val('');
						$('#tempat_lahir').val('');
						$('#tanggal_lahir').val('');
						$('#alamat').val('');
						$('#rt').val('');
						$('#rw').val('');
						$('#kode_pos').val('');
						$('#kecamatan').val('');
						$('#kelurahan').val('');
						$('#nama_faskes').val('');
						$('#kelas_rawat').val('').trigger('change');;
						$('#hubungan_keluarga').val('').trigger('change');
						$('#jenis_kelamin').val('').trigger('change');
						$('#status_perkawinan').val('').trigger('change');
						$('#upload_kk').val('');
						$('#kelas_rawat').val('Kelas 1');
					}
				}
			})
		}
		// $.post('{{ url("insert/bpjskes/detail") }}', data, function(result, status, xhr) {
		// 	if(result.status){
		// 		openSuccessGritter('Success','Data Berhasil Ditambahkan!');
		// 		PerbaruiTambahAnggota();
		// 		DataCustomField();
		// 		$('#nama_keluarga').val('');
		// 		$('#no_bpjs').val('');
		// 		$('#no_kk').val('');
		// 		$('#no_ktp').val('');
		// 		$('#tempat_lahir').val('');
		// 		$('#tanggal_lahir').val('');
		// 		$('#alamat').val('');
		// 		$('#rt').val('');
		// 		$('#rw').val('');
		// 		$('#kode_pos').val('');
		// 		$('#kecamatan').val('');
		// 		$('#kelurahan').val('');
		// 		$('#nama_faskes').val('');
		// 		$('#kelas_rawat').val('').trigger('change');;
		// 		$('#hubungan_keluarga').val('').trigger('change');
		// 		$('#jenis_kelamin').val('').trigger('change');
		// 		$('#status_perkawinan').val('').trigger('change');
		// 		$('#upload_kk').val('');
		// 	}else{
		// 		openErrorGritter('Error!', result.message);
		// 	}
		// });
	}

	function KirimPengajuan(){
		$("#loading").show();
		$.post('{{ url("send/pengajuan") }}', function(result, status, xhr) {
			if(result.status){
				$("#loading").hide();
				$("#modalCreate").modal('hide');
				openSuccessGritter('Success','Data Berhasil Ditambahkan!');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
</script>
@endsection