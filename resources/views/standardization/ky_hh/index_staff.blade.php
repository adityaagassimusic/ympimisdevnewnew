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
									<input type="hidden" name="role_user" id="role_user" value="{{$role_code->position}}">
									@if($username == 'PI2101044' || $username == 'PI1211001' || $username == 'PI0904001')
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<div class="col-xs-3" style="text-align: center; margin-bottom: 5px">
										<button class="btn" style="width: 100%; background-color: #FFA0AC; color: black;" onclick="ModalUpload();"><i class="fa fa-upload" aria-hidden="true"></i> Upload Soal</button>
									</div>
									<div class="col-xs-3" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/log/soal/ky') }}" class="btn" style="margin-left: 5px; width: 100%; background-color: #FEFFA5; color: black;"><i class="fa fa-book" aria-hidden="true"></i> List Soal</a>
									</div>
									<div class="col-xs-3" style="text-align: center; margin-bottom: 5px">
										<a onclick="Hiyarihato()" class="btn" style="background-color: #e67e22;color: white; margin-left: 5px; width: 100%;"><i class="fa fa-file-text"></i> Hiyari Hatto</a>
									</div>
									<div class="col-xs-3" style="text-align: center; margin-bottom: 5px">
										<a onclick="TambahTeamLeader('Tambah Tim')" class="btn" style="background-color: #34675C; color: white; width: 100%"><i class="fa fa-plus-circle"></i> Tambah Tim</a>
									</div>
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div>
									<!-- <div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Test</a>
									</div> -->
									@elseif($role_code->position == 'Chief' || $role_code->position == 'Coordinator' || $role_code->position == 'Foreman' || $role_code->position == 'Manager' || $role_code->position == 'Senior Staff' || $role_code->position == 'Staff')
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="Hiyarihato()" class="btn pull-left" style="background-color: #e67e22;color: white;"><i class="fa fa-file-text"></i> Hiyari Hatto</a>
									</div>
									<div class="col-xs-10" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="TambahTeamLeader('Tambah Tim')" class="btn pull-right" style="background-color: #34675C; color: white;"><i class="fa fa-plus-circle"></i> Tambah Tim</a>
									</div>
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<a href="{{ url('index/monitoring/ky_hh') }}" class="btn" style="width: 100%; background-color: #2874a6; color: white;"><i class="fa fa-book" aria-hidden="true"></i> Monitoring</a>
									</div>
									@else
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="Hiyarihato()" class="btn pull-left" style="background-color: #e67e22;color: white;"><i class="fa fa-file-text"></i> Hiyari Hatto</a>
									</div>
									<div class="col-xs-10" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<div class="col-xs-1" style="text-align: center; margin-bottom: 5px">
										<a onclick="TambahTeamLeader('Tambah Tim')" class="btn pull-right" style="background-color: #34675C; color: white;"><i class="fa fa-plus-circle"></i> Tambah Tim</a>
									</div>
									@endif
									<!-- <div class="col-xs-12">
										<table id="TableListTeamLeader" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="1%">No</th>
													<th width="3%">Nama Tim</th>
													<th width="3%">Anggota</th>
													<th width="2%">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListTeamLeader">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>

									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
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
										<!-- <div id="grafik_karyawan" style="width: 100%; height: 50vh; margin-bottom: 10px; border: 1px solid black;"></div> -->
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

									@if($username == 'PI2101044' || $username == 'PI1211001')
									<div class="col-xs-12">
										<span style="font-weight: bold; font-size: 1.6vw;" class="text-purple">Report KY All YMPI</span>
										<table id="TableListResumeAll" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="10%" style="text-align: center;">No</th>
													<th width="50%" style="text-align: center;">Nama & Ketua Tim</th>
													<th width="20%" style="text-align: center;">Periode</th>
													<th width="20%" style="text-align: center;">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListResumeAll">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>

									<div class="col-xs-12">
										<span style="font-weight: bold; font-size: 1.6vw;" class="text-purple">Report Hiyarihatto All YMPI</span>
										<table id="TableListResumeAllHiyarihatto" class="table table-bordered table-striped table-hover">
											<thead style="background-color: #BDD5EA; color: black;">
												<tr>
													<th width="10%" style="text-align: center;">No</th>
													<th width="12.5%" style="text-align: center;">Request ID</th>
													<th width="12.5%" style="text-align: center;">Nama Tim</th>
													<th width="12.5%" style="text-align: center;">Nama Karyawan</th>
													<th width="12.5%" style="text-align: center;">Keterangan</th>
													<th width="20%" style="text-align: center;">Periode</th>
													<th width="20%" style="text-align: center;">#</th>
												</tr>
											</thead>
											<tbody id="bodyTableListResumeAllHiyarihatto">
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>
									@endif

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
	<div class="modal-dialog modal-lg">
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
								<th width="1%">No</th>
								<th width="5%">Nama Tim</th>
								<th width="2%">#</th>
							</tr>
						</thead>
						<tbody id="BodyTableDetailEdit">
						</tbody>
					</table>
				</div>
				<div class="col-xs-9">
					<div id="tambah_anggota"></div>
				</div>
				<div class="col-xs-3">	
					<a onclick="TambahAnggotaEdit()" class="btn btn-success btn pull-right"  data-toggle="tooltip" title="Tambah Anggota" style="width: 100%"><i class="fa fa-plus-circle"></i> Tambah Anggota</a>
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
								<td style="text-align: left; width: 50%" colspan="2">Nama Tim<span class="text-red"> * :</span></td>
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
					<td style="text-align: left; width: 50%" colspan="2">Alat Pelindung Diri yang digunakan (jika ada)<br><label style="color: red">(Centang Jika Ada, Hiraukan Jika Tidak Ada)</label></td>
					<td style="text-align: center; width: 50%">
						<div class="form-check form-switch" onclick="AdaApd()">
							<input class="form-check-input" type="checkbox" id="cek_apd">
							<label class="form-check-label" for="cek_apd">Ada</label>
							<input type="text" id="input_apd" class="form-control" style="width: 100%; text-align: center; display:none" placeholder="APD" required>
						</div>
					</td>
				</tr>
				<tr>
					<td style="text-align: left; width: 50%" colspan="2">Keparahan<span class="text-red"> * :</span><br><label style="color: red">(Pilih salah satu dari masing masing kategori)</label></td>
					<td style="text-align: left; width: 50%">
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

<div class="modal fade" id="ModalDetailKaryawan" data-keyboard="false">
	<div class="modal-lg modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12" style="text-align: center; margin-bottom: 5px; color: black">
							<span style="font-weight: bold; font-size: 1.6vw;" id="header_modal_detail_karyawan"></span>
						</div>
					</ul>
					<div>
						<table id="TableDetailModalKaryawan" class="table table-bordered table-striped table-hover">
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
							<tbody id="bodyTableDetailModalKaryawan">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
					<div id="detail_approval_log" style="padding-top: 30px"></div>
				</div>
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

	var no = 2;
	var no_jawaban = 2;
	var no_penerima = 1;

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
		Grafik();
		DataListAll();
		DataListHiyarihatto();

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
		$('#file_soal').on('change',function(){
			var fileName = $('input[type=file]').val().split('\\').pop();
			$(this).next('.custom-file-label').html(fileName);
		});
		$('#periode_kyt').datepicker({
			utoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#hh_date_kejadian').datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true
		});
	});

	function previewImage() {
		document.getElementById("image-preview").style.display = "block";
		var oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById("file_gambar").files[0]);
		oFReader.onload = function(oFREvent) {
			document.getElementById("image-preview").src = oFREvent.target.result;
		};
	};

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

	function DataListHiyarihatto(){
		$.get('{{ url("fetch/hiyarihatto/all") }}', function(result, status, xhr){
			if(result.status){
				$('#TableListResumeAllHiyarihatto').DataTable().clear();
				$('#TableListResumeAllHiyarihatto').DataTable().destroy();
				$('#bodyTableListResumeAllHiyarihatto').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {

					// var urutan = value.nama.split(",");
					// var report = '{{ url("data_file/pengisian_ky")}}';
					var date = new Date(value.tanggal);
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

					var nama = value.karyawan.split('/');

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td style="text-align: center">'+value.request_id+'</td>';
					tableData += '<td style="text-align: center">'+value.nama_tim+'</td>';
					tableData += '<td style="text-align: center">'+nama[1]+'</td>';
					tableData += '<td style="text-align: center">'+value.remark+'</td>';
					tableData += '<td style="text-align: center">'+bulan+'<br>'+tahun+'</td>';
					tableData += '<td style=" text-align: center;">';
					tableData += '<a href="resume/hh/'+value.request_id+'" target="_blank" class="btn btn-primary btn-xs"  data-toggle="tooltip" title="HiyariHato" style="width: 100px"><i class="fa fa-pencil"></i> Report HH</a>';
					tableData += '</td>';
					tableData += '</tr>';

				});
				$('#bodyTableListResumeAllHiyarihatto').append(tableData);

				var table = $('#TableListResumeAllHiyarihatto').DataTable({
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
			}else{
				alert('Attempt to retrieve data failed');
			}
		});
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
					// if (value.periode == null) {
					// 	tableData += '<td style="text-align: center">Belum Memilih Periode</td>';
					// }else{
					// 	tableData += '<td style="text-align: center">'+bulan+'<br>'+tahun+'</td>';
					// }

					tableData += '<td style=" text-align: center;">';
					tableData += '<a href="{{ url('index/soal/ky') }}/'+value.nama_tim+'/'+value.periode+'" class="btn btn-primary data-toggle="tooltip" title="Kerjakan KY"><i class="fa fa-file-text-o"></i> Kerjakan KY</a><br><br>';
					// tableData += '<a onclick="EditAnggotaTim(\''+value.nama_tim+'\')" class="btn btn-success data-toggle="tooltip" title="Edit Tim"><i class="fa fa-pencil"></i> Edit Tim</a>&nbsp&nbsp';
					tableData += '<a onclick="DeleteTim(\''+value.nama_tim+'\')" class="btn btn-danger data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Delete Tim</a>';
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
					tableData += '<td>'+ index++ +'</td>';
					tableData += '<td>';
					tableData += '<a target="_blank" style="color: red;">';
					tableData += 'Nama Tim : '+value.nama_tim+'';
					tableData += '</a><br>';
					tableData += 'Nama Ketua : '+urutan[0]+'<br>';
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
}

function DataListAll(){
	$.get('{{ url("fetch/home/leader/all") }}', function(result, status, xhr){
		if(result.status){
			$('#TableListResumeAll').DataTable().clear();
			$('#TableListResumeAll').DataTable().destroy();
			$('#bodyTableListResumeAll').html("");
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
					tableData += '<a href="resume/ky/'+value.id_jawaban+'" class="btn btn-primary btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Report KY</a>&nbsp&nbsp';
					tableData += '<a href="sosialisasi_ulang/kyt/'+value.nama_tim+'/'+value.soal+'" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Sosialisasi KY</a>';
				}else{
					tableData += '<a href="resume/ky/'+value.id_jawaban+'" class="btn btn-primary btn-xs"  data-toggle="tooltip" title="Kiken Yochi" style="width: 100px"><i class="fa fa-pencil"></i> Report KY</a>';
				}
				tableData += '</td>';
				tableData += '</tr>';

			});
			$('#bodyTableListResumeAll').append(tableData);

			var table = $('#TableListResumeAll').DataTable({
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
				tableData += '<td width="1%">'+ index++ +'</td>';
				tableData += '<td width="5%">'+ value.nama +'</td>';
				tableData += '<td style=" text-align: center;" width="2%">';
				tableData += '<a onclick="DeleteList(\''+value.id+'\', \'Edit Tim\', \''+tim+'\')" class="btn btn-danger btn pull-right"  data-toggle="tooltip" title="Delete" style="width: 50px;"><i class="fa fa-trash"></i></a>';
				if (q == 1) {
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
				}
				else if(q == w){
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
				}
				else{
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
					tableData += '&nbsp&nbsp';
					tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+tim+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
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
	$("#tambah_anggota").html('<div class="col-xs-9" style="padding-left: 0"><select class="form-control select2" id="add_user_edit" name="add_user_edit" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}/{{$row->department}}/{{$row->section}}/{{$row->group}}/{{$row->sub_group}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select></div><div class="col-xs-3" style="padding-left: 0"><select onchange="AddAnggotaEdit()" class="form-control select2" id="add_header_edit" name="add_header_edit" data-placeholder="Pilih Header" style="width: 100%" required><option value="">&nbsp;</option><option value="Ketua">Ketua</option><option value="Wakil">Wakil</option><option value="Anggota">Anggota</option></select></div>');

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

function ModalUpload(){
	$("#FormModalUpload").modal('show');
}

function TestMail(){
	$.post('{{ url("send_email/reminder") }}', function(result, status, xhr) {
		if(result.status){
			openSuccessGritter('Success!', result.message);
			location.reload(true);
		}else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function Grafik(fy){
	$("#loading").show();

	var p = '';
	if (fy == 'undefined') {
		p = '';
	}else{
		p = fy;
	}

	var data = {
		fy:p
	}
	$.get('{{ url("fetch/monitoring/karyawan") }}', data, function(result, status, xhr){
		if(result.status){
			$("#loading").hide();


			var categori = [];
			var series = [];
				// var year = result.fy;

				// $.each(result.wc, function(key, value){
				// 	var isi = 0;
				// 	var date = new Date(value.bulan);
				// 	var nama_bulan = date.getMonth();
				// 	switch(nama_bulan) {
				// 	case 0: nama_bulan = "January"; break;
				// 	case 1: nama_bulan = "February"; break;
				// 	case 2: nama_bulan = "March"; break;
				// 	case 3: nama_bulan = "April"; break;
				// 	case 4: nama_bulan = "May"; break;
				// 	case 5: nama_bulan = "June"; break;
				// 	case 6: nama_bulan = "July"; break;
				// 	case 7: nama_bulan = "August"; break;
				// 	case 8: nama_bulan = "September"; break;
				// 	case 9: nama_bulan = "October"; break;
				// 	case 10: nama_bulan = "November"; break;
				// 	case 11: nama_bulan = "December"; break;
				// 	}

				// 	categori.push(nama_bulan);

				// 	$.each(result.data, function(key2, value2){
				// 		if (value.bulan == value2.bulan) {
				// 			series.push({y:parseInt(value2.jumlah), key: value.bulan});
				// 			isi = 1;
				// 		}
				// 	});
				// 	if (isi == 0) {
				// 		series.push(0);
				// 	}
				// });

			$.each(result.employee_sync, function(key, value){
				var isi = 0;
					// var date = new Date(value.bulan);
					// var nama_bulan = date.getMonth();
					// switch(nama_bulan) {
					// case 0: nama_bulan = "January"; break;
					// case 1: nama_bulan = "February"; break;
					// case 2: nama_bulan = "March"; break;
					// case 3: nama_bulan = "April"; break;
					// case 4: nama_bulan = "May"; break;
					// case 5: nama_bulan = "June"; break;
					// case 6: nama_bulan = "July"; break;
					// case 7: nama_bulan = "August"; break;
					// case 8: nama_bulan = "September"; break;
					// case 9: nama_bulan = "October"; break;
					// case 10: nama_bulan = "November"; break;
					// case 11: nama_bulan = "December"; break;
					// }

				categori.push(value.department);

				$.each(result.employee_record, function(key2, value2){
					if (value.department == value2.department) {
							// series.push({y:parseInt(value.employee_id.length), key: value.name});
						series.push({y:parseInt(value.jumlah - value2.jumlah), key: value.department});
						isi = 1;
					}
				});
				if (isi == 0) {
					series.push(0);
				}
			});

			var role = $("#role_user").val();

			Highcharts.chart('grafik_karyawan', {
				chart: {
					scrollablePlotArea: {
						minWidth: 700
					}
				},
				title: {
					text: 'Monitoring Karyawan Belum Masuk Ke Tim Ky'
				},
				credits: {
					enabled: false
				},
				xAxis: {
					tickInterval: 1,
					gridLineWidth: 1,
					categories: categori,
					crosshair: true
				},
				yAxis: [{
					title: {
						text: ''
					}
				}],
				legend: {
					borderWidth: 1
				},
				tooltip: {
					backgroundColor: '#FCFFC5',
					borderColor: 'black',
					borderRadius: 5,
					borderWidth: 1
				},
				plotOptions: {
					column: {
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.8,
						borderColor: '#212121'
					},
					series: {
						dataLabels: {
							enabled: true
						},
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									ModalDetailKaryawan(this.options.key, role);
								}
							}
						}
					}
				},
				series: [{
					name: 'Qty Employee',
					type: 'column',
					data: series,
					color: '#f9a825'
				}]
			});
		}else{
			alert('Attempt to retrieve data failed');
		}
	});
}

function ModalDetailKaryawan(id, role){
	$("#ModalDetailKaryawan").modal('show');
	$("#header_modal_detail_karyawan").html(id);

	var data = {
		id : id,
		role : role
	}

	$.get('{{ url("fetch/detail/monitoring/karyawan") }}', data, function(result, status, xhr){
		if(result.status){
				// belum masuk
			$('#TableDetailModalKaryawan').DataTable().clear();
			$('#TableDetailModalKaryawan').DataTable().destroy();
			$('#bodyTableDetailModalKaryawan').html("");
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
			$('#bodyTableDetailModalKaryawan').append(tableData);

			var table = $('#TableDetailModalKaryawan').DataTable({
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