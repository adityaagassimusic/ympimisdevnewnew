@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

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
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="form-group">
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="Hiyarihato()" class="btn pull-left" style="background-color: #e67e22;color: white;"><i class="fa fa-file-text"></i> Hiyari Hatto</a>
									</div>
									<div class="col-xs-10" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="TambahTeamLeader('Tambah Tim')" class="btn pull-right" style="background-color: #34675C; color: white;"><i class="fa fa-plus-circle"></i> Tambah Tim</a>
									</div>

									@if($role_code->position == 'Sub Leader' || $role_code->position == 'Leader' || $role_code->position == 'Foreman' || $role_code->position == 'Chief')
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div>
									@endif
									<!-- <div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div> -->

									<!-- <div class="col-xs-6" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div>

									<div class="col-xs-6" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div> -->


									<!-- <div class="col-xs-2 form-group" style="padding-right: 0;">
										<div class="small-box" style="background: #00ff73; height: 13vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Sudah Vaksin Kedua')">
											<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
												<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>Total Tim KY</b></h3>
												<h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"><b>ワクチン2回目</b></h3>
												<span style="font-size: 1.8vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="jml_tim">0</span>
											</div>
											<div class="icon" style="padding-top: 0;font-size:8vh;">
												<i class="fa fa-check"></i>
											</div>
										</div>
									</div>

									<div class="col-xs-10">
										<div id="container" style="width: 100%; height: 60vh;"></div>
									</div> -->


									<div class="col-xs-6">
										<span style="font-weight: bold; font-size: 1.6vw;" class="text-purple">Tim KY</span>
										<table id="TableListTeamLeader" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="10%" style="text-align: center;">No</th>
													<th width="15%" style="text-align: center;">Nama Tim</th>
													<th width="35%" style="text-align: center;">Anggota</th>
													<!-- <th width="10%" style="text-align: center;">Periode</th> -->
													<th width="40%" style="text-align: center;">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListTeamLeader">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>



									<div class="col-xs-6">
										<span style="font-weight: bold; font-size: 1.6vw;" class="text-purple">Report KY</span>
										<table id="TableListResume" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="10%" style="text-align: center;">No</th>
													<th width="50%" style="text-align: center;">Nama & Ketua Tim</th>
													<th width="20%" style="text-align: center;">Periode</th>
													<th width="20%" style="text-align: center;">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListResume">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>

									<div class="col-xs-12">
										<span style="font-weight: bold; font-size: 1.6vw;" class="text-purple">List Karyawan Belum Masuk Tim</span>
										<table id="TableListResumeBelumMasuk" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="10%" style="text-align: center;">No</th>
													<th width="15%" style="text-align: center;">NIK</th>
													<th width="15%" style="text-align: center;">Nama</th>
													<th width="20%" style="text-align: center;">Department</th>
													<th width="10%" style="text-align: center;">Section</th>
													<th width="20%" style="text-align: center;">Group</th>
													<th width="10%" style="text-align: center;">Sub Group</th>
												</tr>
											</thead>
											<tbody id="bodyTableListResumeBelumMasuk">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>

									<!-- <div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">Tim Yang Sudah Melaksanakan Kiken Yochi<br><small class="text-purple">(キケン予知を実践したチーム)</small></span>
									</div>
									<div class="col-xs-12">
										<table id="TableListResume" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="1%">No</th>
													<th width="3%">Nama & Ketua Tim</th>
													<th width="3%">Periode</th>
													<th width="2%">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListResume">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalTambahTim" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<h2 id="new_tim"></h2>
				</div>
				<div class="col-md-12" style="padding-top: 10px">
					<table id="TableDetail" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #BDD5EA; color: black;">
							<tr>
								<th width="1%">No</th>
								<th width="5%">Nama Tim</th>
								<th width="2%">#</th>
							</tr>
						</thead>
						<tbody id="BodyTableDetail">
						</tbody>
					</table>
				</div>
				<div class="col-xs-9">
					<div id="modal_report"></div>
				</div>
				<div class="col-xs-3">	
					<a onclick="TambahAnggota()" class="btn btn-success btn pull-right"  data-toggle="tooltip" title="Tambah Anggota" style="width: 100%"><i class="fa fa-plus-circle"></i> Tambah Anggota</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEditTim" style="z-index: 10000;">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<h2 id="edit_tim"></h2>
				</div>
				<div class="col-md-12" style="padding-top: 10px">
					<table id="TableDetailEdit" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #BDD5EA; color: black;">
							<tr>
								<th width="10%">No</th>
								<th width="60%">Nama Tim</th>
								<th width="30%">#</th>
							</tr>
						</thead>
						<tbody id="BodyTableDetailEdit">
						</tbody>
					</table>
				</div>
				<div class="col-xs-10">
					<div id="tambah_anggota"></div>
				</div>
				<div class="col-xs-2">	
					<a onclick="TambahAnggotaEdit()" class="btn btn-success btn pull-right" data-toggle="tooltip" title="Tambah Anggota"><i class="fa fa-plus-circle"></i> Anggota</a>
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
								<!-- <td style="text-align: center; width: 30%">Nama Tim</td> -->
								<td style="text-align: left; width: 50%" colspan="2">Nama Tim<span class="text-red"> * :</span></td>
								<!-- <td style="text-align: center; width: 40%" colspan="2"><input type="text" name="hh_team" id="hh_team" style="text-align: center" readonly></td> -->
								<td style="text-align: center; width: 50%">
									<div id="select_nama_tim">
										<select class="form-control select01" id="hh_team" name="hh_team" style="width: 100%" required data-placeholder="Nama Tim" onChange="SelectNama(this.value)">
											<option value="">&nbsp;</option>
											@foreach($tim as $row)
											<option value="{{$row->nama_tim}}"> {{$row->nama_tim}}</option>
											@endforeach
										</select>
									</div>
									<div id="show_nama_tim"><input type="text" name="tim" id="tim" style="text-align: center; width: 100%" readonly></div>
								</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%" colspan="2">Nama Karyawan<span class="text-red"> * :</span></td>
								<td style="text-align: center; width: 100%">
									<div id="nama_anggota"></div>
								</select>
							</td>
						</tr>
						<tr>
							<!-- <td style="text-align: left; width: 50%" colspan="2">Saksi</td> -->
							<td style="text-align: left; width: 50%" colspan="2">Saksi<br><label style="color: red">(Pilih Jika Ada, Hiraukan Jika Tidak Ada)</label></td>
							<td style="text-align: center; width: 100%">
								<div id="nama_saksi"></div>
							</select>
						</td>
					</tr>
					<tr>
						<td style="text-align: left; width: 50%" colspan="2">Tanggal Kejadian<span class="text-red"> * :</span></td>
						<td style="text-align: left; width: 50%"><div class="input-group">
							<div class="input-group-addon bg-purple" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" id="hh_date_kejadian" class="form-control datepicker" style="width: 100%; text-align: center;" placeholder="Pilih Tanggal Input" value="{{ date('Y-m-d') }}" required>
						</div></td>
					</tr>
					<tr>
						<td style="text-align: left; width: 50%" colspan="2">Lokasi Kejadian<span class="text-red"> * :</span></td>
						<td style="text-align: center; width: 100%">
							<div id="lokasi_kejadian"></div>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align: left; width: 50%" colspan="2">Ringkasan Kejadian<span class="text-red"> * :</span></td>
					<td style="text-align: center; width: 50%"><textarea class="form-control" id="hh_ringkasan" name="hh_ringkasan" style="height: 100px"></textarea></td>
				</tr>
				<tr>
					<!-- <td style="text-align: left; width: 50%" colspan="2">Alat Pelindung Diri yang digunakan (jika ada)</td> -->
					<td style="text-align: left; width: 50%" colspan="2">Alat Pelindung Diri yang digunakan (jika ada)<br><label style="color: red">(Centang Jika Ada, Hiraukan Jika Tidak Ada)</label></td>
					<td style="text-align: center; width: 50%">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="cek_apd" onclick="AdaApd()">
							<label class="form-check-label" for="cek_apd">Ada</label>
							<input type="text" id="input_apd" class="form-control" style="width: 100%; text-align: center; display:none" placeholder="APD" required>
						</div>
					</td>
				</tr>
				<tr>
					<td style="text-align: left; width: 50%" colspan="2">Keparahan<span class="text-red"> * :</span><br><label style="color: red">(Pilih salah satu dari masing masing kategori)</label></td>
					<td style="text-align: left; width: 50%">
						<!-- <div class="form-check">
							<div>
								<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left; font-size: 12px">
									<thead>
										<tr>
											<td style="width: 30px"><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Ledakan</td>
											<td style="width: 30px"><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Kebakaran</td>
											<td style="width: 30px"><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tenggelam</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Jatuh</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terguling</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Benturan</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Dehidrasi</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Kelelahan</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Pingsan</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Patah Tulang</td>
											<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tulang Dislokasi</td>
											<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tersengat Listrik</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terpental</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Tertimpa</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terjepit</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terlilit</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Luka Sayat</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Luka Lecet/Gores</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Pingsan</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Sesak Nafas</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Mata Iritasi</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Salah Injak/Terperosok</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Kulit Iritasi</td>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Reflek Gerakan</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menurunkan Penglihatan</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menurunkan Pendengaran</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menyentuh Barang2 Berbahaya</td>
										</tr>
										<tr>
											<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Menyentuh Benda2 Bertemperatur Rendah</td>
											<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menyentuh Benda2 Bertemperatur Tinggi</td>
										</tr>
									</table>
								</div>
							</div> -->
							<table class="table table-bordered table-striped table-hover">
								<thead style="background-color: #BDD5EA; color: black;">
									<tr>
										<th width="20%" style="text-align: center;">Rendah</th>
										<th width="20%" style="text-align: center;">Sedang</th>
										<th width="20%" style="text-align: center;">Tinggi</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Pingsan</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Jatuh</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Ledakan</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Sesak Nafas</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terguling</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Kebakaran</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Mata Iritasi</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Benturan</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tenggelam</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Dehidrasi</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terpental</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Patah Tulang</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Kelelahan</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Tertimpa</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tulang Dislokasi</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Pingsan</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terjepit</td>
										<td><input type="radio" name="keparahan_high" id="keparahan" value="Keparahan Tinggi"> Tersengat Listrik</td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Reflek Gerakan</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Terlilit</td>
										<td></td>
									</tr>
									<tr>
										<td><input type="radio" name="keparahan_low" id="keparahan" value="Keparahan Rendah"> Menyentuh Benda2 Bertemperatur Rendah</td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Luka Sayat</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Luka Lecet/Gores</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Salah Injak/Terperosok</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menurunkan Penglihatan</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menurunkan Penglihatan</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menurunkan Pendengaran</td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menyentuh Barang2 Berbahaya</td>
										<td></td>
									</tr><tr>
										<td></td>
										<td><input type="radio" name="keparahan_medium" id="keparahan" value="Keparahan Sedang"> Menyentuh Benda2 Bertemperatur Tinggi</td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<!-- <td style="text-align: left; width: 50%" colspan="2">Kemungkinan</td> -->
						<td style="text-align: left; width: 50%" colspan="2">Kemungkinan<span class="text-red"> * :</span><br><label style="color: red">(Pilih salah satu)</label></td>
						<td style="text-align: left; width: 50%">
							<div class="form-check">
								<div>
									<input type="radio" id="kemungkinan" name="kemungkinan_high" value="Kemungkinan Tinggi"> Kejadian sering muncul (2 kali/minggu) dan atau terjadi pada beberapa orang (lebih dari 2 orang/minggu)<br><br>
									<input type="radio" id="kemungkinan" name="kemungkinan_medium" value="Kemungkinan Sedang"> Kejadian terjadi dalam waktu tidak terlalu sering (< 2 kali/minggu) dan kejadian < 2 orang /minggu<br><br>
									<input type="radio" id="kemungkinan" name="kemungkinan_low" value="Kemungkinan Rendah"> Kecelakaan jarang terjadi dan hanya orang tertentu (diluar definisi High dan Low)<br><br>
								</div>
							</div><div>
								<p style="text-align: left; color: red">
									* Dan juga mempertimbangkan kriteria seperti kompleksitas dari suatu kejadian dan faktor manusia
								</p>
							</div></td>
						</tr>
								<!-- <tr id="resiko">
									<td style="text-align: left; width: 50%; background-color: green" colspan="4"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi</td>
								</tr> -->
								<tr>
									<td style="text-align: left; width: 50%" colspan="2"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi<span class="text-red"> * :</span></td>
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

<div class="modal fade" id="modalDetail" style="z-index: 10000;">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<center>
						<h2 id="detailJudul"></h2>
						<h3 id="detailJudulBulan"></h3>
					</center>
				</div>
				<div id="tableDetail"></div>
				<!-- <table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtable">
					<thead style="background-color: rgb(126,86,134)">
						<tr>
							<th>No</th>
							<th>Nama Tim</th>
							<th>Nama Ketua</th>
							<th>Jumlah</th>
							<th>Tanggal Masuk</th>
							<th>Tanggal Keluar</th>
							<th>Masa Simpan</th>
						</tr>
					</thead>
					<tbody id="detailbodytable">
					</tbody>
				</table> -->
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
		$('.select2').select2({
			dropdownParent: $('#modalTambahTim'),
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
		DataList();

		$('#hh_date_kejadian').datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true
		});
		// FetchMonitoring();
	});

	function FetchMonitoring(){
		$.get('{{ url("fetch/monitoring/ky_hh") }}', function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Data Ditemukan');
				var categori = [];
				var series_ky_belum = [];
				var series_ky_sudah = [];
				var series_hh_open = [];
				var series_hh_close = [];

				$.each(result.fy, function(key, value){
					var isi = 0;
					categori.push(value.bulan);
					$.each(result.ky_belum, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_ky_belum.push(value2.jumlah);
							isi = 1;
						};
					});
					if (isi == 0) {
						series_ky_belum.push(0);
					}
				});

				$.each(result.fy, function(key, value){
					var isi = 0;
					categori.push(value.bulan);
					$.each(result.ky_sudah, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_ky_sudah.push(value2.jumlah);
							isi = 1;
						};
					});
					if (isi == 0) {
						series_ky_sudah.push(0);
					}
				});

				$.each(result.fy, function(key, value){
					var isi = 0;
					categori.push(value.bulan);
					$.each(result.hh_open, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_hh_open.push(value2.jumlah);
							isi = 1;
						};
					});
					if (isi == 0) {
						series_hh_open.push(0);
					}
				});

				$.each(result.fy, function(key, value){
					var isi = 0;
					categori.push(value.bulan);
					$.each(result.hh_close, function(key2, value2){
						if (value.bulan == value2.bulan2) {
							series_hh_close.push(value2.jumlah);
							isi = 1;
						};
					});
					if (isi == 0) {
						series_hh_close.push(0);
					}
				});

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 15,
							beta: 0,
							depth: 50,
							viewDistance: 50,
						}
					},
					title: {
						text: 'Grafik Pengerjaan KY & Hiyari Hatto'
					},
					xAxis: {
						categories: categori,
						type: 'category',
						gridLineColor: '#707073',
						labels: {
							style: {
								color: 'black'
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
					},yAxis: [{
						title: {
							text: 'Total',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"15px"
							}
						},
						type: 'linear',
						opposite: false
					},
					],
					tooltip: {
						headerFormat: '<span>{series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						itemStyle: {
							color: 'black'
						},
						itemHoverStyle: {
							color: '#FFF'
						},
						itemHiddenStyle: {
							color: '#606063'
						}
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ClickDetail(this.category, this.series.name);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true
							}
						}
					},credits: {
						enabled: false
					},
					series: [{
						data: series_ky_belum,
						name: 'Sudah Mengerjakan KY',
						zIndex: 0,
						color: 'red',
						stack : '1'
					},{
						data: series_ky_sudah,
						name: 'Belum Mengerjakan KY',
						zIndex: 0,
						color: 'green',
						stack : '1'
					},{
						data: series_hh_open,
						name: 'Temuan Open HH',
						zIndex: 0,
						color: 'blue',
						stack : '2'
					},{
						data: series_hh_close,
						name: 'Temuan Close HH',
						zIndex: 0,
						color: 'yellow',
						stack : '2'
					}]
				});

				var test = result.jml_tim.length;
				$('#jml_tim').html(test + ' Tim');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
}

function ClickDetail(bulan, judul){
	$('#modalDetail').modal('show');
	$('#detailJudul').html(judul);
	$('#detailJudulBulan').html('Bulan '+bulan);

	if (judul == 'Sudah Mengerjakan KY' || judul == 'Belum Mengerjakan KY') {
		$('#tableDetail').html('<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtableKY"><thead style="background-color: #BDD5EA; color: black; text-align: center"><tr><th>No</th><th>Nama Tim</th><th>Nama Ketua</th></tr></thead><tbody id="detailbodytableKY"></tbody></table>');

		var data = {
			bulan:bulan,
			judul:judul
		}

		$.get('<?php echo e(url("fetch/data/presentase")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#detailtableKY').DataTable().clear();
				$('#detailtableKY').DataTable().destroy();
				$('#detailbodytableKY').html("");
				var tableData = "";
				var index = 1;
				$.each(result.resumes, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align: center;">'+ index++ +'</td>';
					tableData += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
					tableData += '<td style="text-align: center;">'+ value.nama +'</td>';
					tableData += '</tr>';
				});
				$('#detailbodytableKY').append(tableData);

				var table = $('#detailtableKY').DataTable({
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
	}else{
		$('#tableDetail').html('<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtableHH"><thead style="background-color: #BDD5EA; color: black; text-align: center"><tr><th>No</th><th>Temuan HH</th><th>Nama Ketua</th></tr></thead><tbody id="detailbodytableHH"></tbody></table>');

		var data = {
			bulan:bulan,
			judul:judul
		}

		$.get('<?php echo e(url("fetch/data/presentase")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#detailtableHH').DataTable().clear();
				$('#detailtableHH').DataTable().destroy();
				$('#detailbodytableHH').html("");
				var tableData = "";
				var index = 1;
				$.each(result.resumes, function(key, value) {
					var jenis = value.karyawan.split("/");
					var ringkasan = value.ringkasan.split("/");

					tableData += '<tr>';
					tableData += '<td style="text-align: center;">'+ index++ +'</td>';
					tableData += '<td style="text-align: center;">'+ value.request_id +'</td>';
					tableData += '<td style="text-align: center;">'+ ringkasan[0] +'</td>';
					tableData += '</tr>';
				});
				$('#detailbodytableHH').append(tableData);

				var table = $('#detailtableHH').DataTable({
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
}

function DataList(){
	$.get('{{ url("fetch/home/leader") }}', function(result, status, xhr){
		if(result.status){
			$('#TableListTeamLeader').DataTable().clear();
			$('#TableListTeamLeader').DataTable().destroy();
			$('#bodyTableListTeamLeader').html("");
			var tableData = "";
			var index = 1;
			$.each(result.data, function(key, value) {

				var urutan = value.nama.split(",");
				var report = '{{ url("data_file/pengisian_ky")}}';
				var date = new Date(value.periode);
				var bulan = date.getMonth();
				var tahun = date.getFullYear();
				switch(bulan) {
				case 0: bulan = "Januari"; break;
				case 1: bulan = "Februari"; break;
				case 2: bulan = "Maret"; break;
				case 3: bulan = "April"; break;
				case 4: bulan = "Mei"; break;
				case 5: bulan = "Juni"; break;
				case 6: bulan = "Juli"; break;
				case 7: bulan = "Agustus"; break;
				case 8: bulan = "September"; break;
				case 9: bulan = "Oktober"; break;
				case 10: bulan = "November"; break;
				case 11: bulan = "Desember"; break;
				}

				tableData += '<tr>';
				tableData += '<td style="text-align: center">'+ index++ +'</td>';
				tableData += '<td style="text-align: center">'+ value.nama_tim +'</td>';

				tableData += '<td style=" text-align: left">';
				tableData += '<ol>';

				for(var i = 0; i < urutan.length; i++){
					tableData += '<li style="color: #e53935;">';
					tableData += '<a target="_blank" style="color: red;">';
					tableData += urutan[i];
					tableData += '</a>';
					tableData += '</li>';
				};

				tableData += '</ol>';
				tableData += '</td>';
				// tableData += '<td style="text-align: center">'+bulan+'<br>'+tahun+'</td>';

				tableData += '<td style=" text-align: center;">';
				tableData += '<a href="{{ url('index/soal/ky') }}/'+value.nama_tim+'/'+value.periode+'" class="btn btn-primary" data-toggle="tooltip" title="Kerjakan KY"><i class="fa fa-file-text-o"></i> Kerjakan KY</a><br><br>';
				// tableData += '<a onclick="EditAnggotaTim(\''+value.nama_tim+'\')" class="btn btn-success" data-toggle="tooltip" title="Edit Tim"><i class="fa fa-pencil"></i> Edit Tim</a>&nbsp&nbsp';
				tableData += '<a onclick="DeleteTim(\''+value.nama_tim+'\')" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Delete Tim</a>';
				tableData += '</td>';
				tableData += '</tr>';

			});
			$('#bodyTableListTeamLeader').append(tableData);

			var table = $('#TableListTeamLeader').DataTable({
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
				'pageLength': 5,
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

				//resume
			$('#TableListResume').DataTable().clear();
			$('#TableListResume').DataTable().destroy();
			$('#bodyTableListResume').html("");
			var tableData = "";
			var index = 1;
			$.each(result.data_resume, function(key, value) {

				var urutan = value.nama.split(",");
				var report = '{{ url("data_file/pengisian_ky")}}';
				var date = new Date(value.periode);
				var bulan = date.getMonth();
				var tahun = date.getFullYear();
				switch(bulan) {
				case 0: bulan = "Januari"; break;
				case 1: bulan = "Februari"; break;
				case 2: bulan = "Maret"; break;
				case 3: bulan = "April"; break;
				case 4: bulan = "Mei"; break;
				case 5: bulan = "Juni"; break;
				case 6: bulan = "Juli"; break;
				case 7: bulan = "Agustus"; break;
				case 8: bulan = "September"; break;
				case 9: bulan = "Oktober"; break;
				case 10: bulan = "November"; break;
				case 11: bulan = "Desember"; break;
				}

				tableData += '<tr>';
				tableData += '<td style="text-align: center">'+ index++ +'</td>';
				tableData += '<td>';
				tableData += '<a target="_blank" style="color: red;">';
				tableData += 'Nama Tim : '+value.nama_tim+'';
				tableData += '</a><br>';
				tableData += 'Nama Ketua : '+urutan[0]+'';
				tableData += '</td>';
				tableData += '<td style="text-align: center">'+bulan+'<br>'+tahun+'</td>';
				tableData += '<td style=" text-align: center;">';
				if (value.atasan != null) {
					tableData += '<a href="resume/ky/'+value.id_jawaban+'" class="btn btn-primary btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Report KY</a><br><br>';
					tableData += '<a href="sosialisasi_ulang/kyt/'+value.nama_tim+'/'+value.soal+'" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Sosialisasi KY</a>';
				}else{
					tableData += '<a href="resume/ky/'+value.id_jawaban+'" class="btn btn-primary btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Report KY</a>';
				}
				tableData += '</td>';
				tableData += '</tr>';

			});
			$('#bodyTableListResume').append(tableData);

			var table = $('#TableListResume').DataTable({
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
				'pageLength': 5,
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
			
				// belum masuk
			$('#TableListResumeBelumMasuk').DataTable().clear();
			$('#TableListResumeBelumMasuk').DataTable().destroy();
			$('#bodyTableListResumeBelumMasuk').html("");
			var tableData = "";
			var index = 1;

			$.each(result.employee_sync, function(key, value){
				var isi = 0;
				$.each(result.employee_record, function(key2, value2){
					if (value.employee_id == value2.nik) {
						isi = 1;
					}
				});
				if (isi == 0) {
					isi = 0;
				}

				if (isi == 0) {
					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td style="text-align: center">'+value.employee_id+'</td>';
					tableData += '<td style="text-align: center">'+value.name+'</td>';
					tableData += '<td style="text-align: center">'+value.department+'</td>';
					tableData += '<td style="text-align: center">'+value.section+'</td>';
					tableData += '<td style="text-align: center">'+(value.group || '-')+'</td>';
					tableData += '<td style="text-align: center">'+(value.sub_group || '-')+'</td>';
					tableData += '</tr>';
				}

			});
			$('#bodyTableListResumeBelumMasuk').append(tableData);

			var table = $('#TableListResumeBelumMasuk').DataTable({
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
				'pageLength': 5,
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

		// 		$.each(result.employee_record, function(key, value) {


		// });
}

function ModalEdit(tim){
	$('#modalTambahTim').modal('show');
	var jenis = 'Tambah Baru';
	var data = {
		tim:tim,
		jenis:jenis
	}
	$.get('{{ url("fetch/home/leader") }}',data, function(result, status, xhr){
		if(result.status){
			$('#TableDetail').DataTable().clear();
			$('#TableDetail').DataTable().destroy();
			$('#BodyTableDetail').html("");
			var tableData = "";
			var index = 1;
			$.each(result.list, function(key, value) {

				tableData += '<tr>';
				tableData += '<td width="1%">'+ index++ +'</td>';
				tableData += '<td width="5%">'+ value.nama +'</td>';
				tableData += '<td style=" text-align: center;" width="2%">';
				tableData += '<a onclick="DeleteList(\''+value.id+'\', \'Tim Baru\', \''+tim+'\')" class="btn btn-danger"  data-toggle="tooltip" title="Delete" style="width: 50px;"><i class="fa fa-trash"></i></a>';
				tableData += '</td>';
				tableData += '</tr>';

			});
			$('#BodyTableDetail').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}

function TambahTeamLeader(ket){
	$('#modalTambahTim').modal('show');
	$.post('{{ url("create/tim/leader") }}', function(result, status, xhr) {
		if(result.status){
			openSuccessGritter('Success','Nama Tim Berhasil Dibuat!');
			$("#new_tim").html(result.slip);
		}else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function EditAnggotaTim(tim){
	$('#modalEditTim').modal('show');
	$("#edit_tim").html(tim);
	var jenis = 'Edit Tim';
	var data = {
		nama_tim:tim,
		jenis:jenis
	}
	$.get('{{ url("fetch/home/leader") }}',data, function(result, status, xhr){
		if(result.status){
			$('#TableDetailEdit').DataTable().clear();
			$('#TableDetailEdit').DataTable().destroy();
			$('#BodyTableDetailEdit').html("");
			var tableData = "";
			var index = 1;
			$.each(result.list_edit, function(key, value) {
				var q = value.urutan;
				var w = result.list_edit.length;

				tableData += '<tr>';
				tableData += '<td>'+ index++ +'</td>';
				tableData += '<td>'+ value.nama +'</td>';
				tableData += '<td style=" text-align: center;">';
				tableData += '<a onclick="DeleteList(\''+value.id+'\', \'Edit Tim\', \''+tim+'\')" class="btn btn-danger btn-xs pull-right" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
				if (q == 1) {
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-info btn-xs" data-toggle="tooltip"><i class="fa fa-arrow-down"></i></a>';
				}
				else if(q == w){
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-warning btn-xs" data-toggle="tooltip"><i class="fa fa-arrow-up"></i></a>';
				}
				else{
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-warning btn-xs" data-toggle="tooltip"><i class="fa fa-arrow-up"></i></a>';
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-info btn-xs" data-toggle="tooltip"><i class="fa fa-arrow-down"></i></a>';
				}
				tableData += '</td>';
				tableData += '</tr>';

			});
			$('#BodyTableDetailEdit').append(tableData);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}

function TambahAnggota(){
	$("#modal_report").show();
	$("#modal_report").html('<div class="col-xs-9" style="padding-left: 0"><select class="form-control select2" id="add_user" name="add_user" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select></div><div class="col-xs-3" style="padding-left: 0"><select onchange="AddAnggota()" class="form-control select2" id="add_header" name="add_header" data-placeholder="Pilih Header" style="width: 100%" required><option value="">&nbsp;</option><option value="Ketua">Ketua</option><option value="Wakil">Wakil</option><option value="Anggota">Anggota</option></select></div>');

	$('.select2').select2({
		dropdownParent: $('#modalTambahTim'),
		allowClear : true,
	});
}

function TambahAnggotaEdit(){
	$("#tambah_anggota").show();
	$("#tambah_anggota").html('<div class="col-xs-8" style="padding-left: 0"><select class="form-control select2" id="add_user_edit" name="add_user_edit" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select></div><div class="col-xs-4" style="padding-left: 0"><select onchange="AddAnggotaEdit()" class="form-control select2" id="add_header_edit" name="add_header_edit" data-placeholder="Pilih Header" style="width: 100%" required><option value="">&nbsp;</option><option value="Ketua">Ketua</option><option value="Wakil">Wakil</option><option value="Anggota">Anggota</option></select></div>');

	$('.select2').select2({
		dropdownParent: $('#modalEditTim'),
		allowClear : true,
	});
}

function AddAnggota(){
	var tim = $('#new_tim').html();
	var user = $('#add_user').val();
	var header = $('#add_header').val();
	if (header == '') {
		return false;
	}else{
		var data = {
			tim:tim,
			user:user,
			header:header
		}
		$.post('{{ url("add/inject/tim") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','List Kategori Anggota Berhasil Di Tambahkan!');
				ModalEdit(tim);
				DataList();
				$("#add_user").val('').trigger('change');
				$("#add_header").val('').trigger('change');
				$("#modal_report").hide();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
}

function AddAnggotaEdit(){
	var jenis = 'Edit Tim';
	var tim = $('#edit_tim').html();
	var user = $('#add_user_edit').val();
	var header = $('#add_header_edit').val();
	if (header == '') {
		return false;
	}else{
		var data = {
			jenis:jenis,
			tim_edit:tim,
			user_edit:user,
			header_edit:header
		}
		$.post('{{ url("add/inject/tim") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','List Kategori Anggota Berhasil Di Tambahkan!');
				EditAnggotaTim(tim);
				DataList();
				$("#add_user_edit").val('').trigger('change');
				$("#add_header_edit").val('').trigger('change');
				$("#tambah_anggota").hide();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
}

function DeleteTim(tim){
	if(confirm("Apakah anda yakin akan menghapus tim ini?")){
		var jenis = 'Hapus Tim';
		var data = {
			jenis:jenis,
			tim:tim
		}
		$.post('{{ url("delete/list/anggota") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','Tim Berhasil Di Hapus!');
				DataList();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}else{
		return false;
	}
}

function DeleteList(id, jenis, tim){
	if(confirm("Apakah anda yakin akan menghapus list anggota ini?")){
		var jenis = 'Hapus List';
		var data = {
			id:id,
			tim:tim
		}
		$.post('{{ url("delete/list/anggota") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#modalTambahTim').modal('hide');
				openSuccessGritter('Success','List Anggota Berhasil Di Hapus!');
				EditAnggotaTim(tim)
				DataList();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}else{
		return false;
	}
}

function Naikkan(id, tim){
	var jenis = 'Naikkan';
	var data = {
		jenis:jenis,
		tim:tim,
		id:id
	}
	$.post('{{ url("pindah/posisi/anggota") }}', data, function(result, status, xhr) {
		EditAnggotaTim(tim);
		DataList();
	});
}

function Turunkan(id, tim){
	var jenis = 'Turunkan';
	var data = {
		jenis:jenis,
		tim:tim,
		id:id
	}
	$.post('{{ url("pindah/posisi/anggota") }}', data, function(result, status, xhr) {
		EditAnggotaTim(tim);
		DataList();
	});
}

function Hiyarihato(){
	$("#ModalPengisianHH").modal('show');
	$("#show_nama_tim").hide();
	// $("#isian_apd").hide();
	// $("#resiko").hide();
	// $("#hh_team").val(tim);
}

function SelectNama(tim){
	var data = {
		tim:tim
	}
	$.get('{{ url("fetch/anggota/tim") }}', data, function(result, status, xhr) {
		if(result.status){
			$("#select_nama_tim").hide();
			$("#show_nama_tim").show();
			$("#tim").val(tim);
			$("#nama_anggota").html('<select class="form-control select2" id="anggota_tim" name="anggota_tim" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option></select>');
			$('.select2').select2({
				dropdownParent: $('#ModalPengisianHH'),
				allowClear : true,
			});
			$.each(result.anggota_tim, function(key, value) {
				$('#anggota_tim').append('<option value="'+value.nik+'/'+value.nama+'/'+value.department_short+'/'+value.department+'/'+value.section+'/'+value.group+'/'+value.sub_group+'">'+value.nik+' - '+value.nama+'</option>');
			});

			$("#nama_saksi").html('<select class="form-control select2" id="saksi" name="saksi" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option></select>');
			$('.select2').select2({
				dropdownParent: $('#ModalPengisianHH'),
				allowClear : true,
			});
			$.each(result.saksi, function(key, value) {
				$('#saksi').append('<option value="'+value.name+'">'+value.name+'</option>');
			});

			$("#lokasi_kejadian").html('<select class="form-control select2" id="lokasi" name="lokasi" data-placeholder="Pilih Lokasi" style="width: 100%" required><option value="">&nbsp;</option></select>');
			$('.select2').select2({
				dropdownParent: $('#ModalPengisianHH'),
				allowClear : true,
			});
			$.each(result.lokasi, function(key, value) {
				$('#lokasi').append('<option value="'+value.location+'">'+value.location+'</option>');
			});
		}else{
			openErrorGritter('Error!', result.message);
		}
	});
}

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

function SimpanHH(){
	$("#loading").show();
	var team     	= $("#tim").val();
	var karyawan 	= $("#anggota_tim").val();
	var saksi 	 	= $("#saksi").val();
	var tanggal  	= $("#hh_date_kejadian").val();
	var lokasi   	= $("#lokasi").val();
	var ringkasan	= $("#hh_ringkasan").val();
	var apd      	= $("#input_apd").val();
	var perbaikan   = $("#hh_perbaikan").val();
	var lain        = $("#hh_lain").val();
	var keparahan   = $('input[id="keparahan"]:checked').val();
	var kemungkinan   = $('input[id="kemungkinan"]:checked').val();
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
</script>
@endsection