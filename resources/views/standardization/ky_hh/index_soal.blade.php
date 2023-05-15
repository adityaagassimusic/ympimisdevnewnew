@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(100, 100, 100);
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
	#loading, #error { display: none; }
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
	<h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }} Periode {{ $periode_ky[0]->bulan }}</h1>
	<!-- <a href="{{ url('index/ky_hh') }}" class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #ffb600; color: white;"><i class="fa fa-list"></i> Monitoring</a> -->
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 35%; left: 30%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<!-- <div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Hubungan<span class="text-red"> * :</span></label>
				<div class="col-sm-9" id="divHubunganKeluarga">
					<select class="form-control" id="hubungan_keluarga" data-placeholder='Hubungan Keluarga' style="width: 100%" required>
						<option value="">&nbsp;</option>
						<option value="KELUARGA TAMBAHAN ANAK">ANAK</option>
						<option value="KELUARGA TAMBAHAN ORANG TUA/MERTUA">KELUARGA TAMBAHAN ORANG TUA/MERTUA</option>
						<option value="ANGGOTA">KARYAWAN YMPI</option>
					</select>
				</div>
			</div>
		</div>
	</div> -->

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-2" style="padding-bottom: 20px">
				<!-- <label style="padding-top: 0;" for="" class="col-sm-5 control-label">Ganti Periode KY : </label> -->
				<!-- <div class="col-sm-7">
					
				</div> -->
				<select id="select-periode" class="form-control select2" onchange="SelectPeriode(value)" data-placeholder="Ubah Periode KY ..." style="width: 100%; font-size: 20px;">
					<option></option>
					@foreach($list_soal as $list_soal)
					<option value="{{ $list_soal->kode_soal }}/{{ $list_soal->periode }}">Periode {{ $list_soal->bulan }} - ({{ $list_soal->remark }})</option>
					@endforeach
				</select>
			</div>

			<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
				<span style="font-weight: bold; font-size: 1.6vw;"><span id="id_kode_soal"></span></span>
				<input type="hidden" id="nama_tim" value="{{ $kode }}">
				<input type="hidden" id="kode_soal">
				<input type="hidden" id="test_kode_soal" value="{{ $kode_soal[0]->kode_soal }}">
				<input type="hidden" id="update_kode_soal" value="{{ $periode_ky[0]->kode_soal }}">
			</div>

			<div class="col-xs-12">
				<div class="box box-solid" style="border: 1px solid grey;">
					<div class="box-body">
						<div id="attach_pdf" align="center"></div>
					</div>
				</div>
			</div>

			<div class="col-xs-12">
				<div class="box box-solid" style="border: 1px solid grey;">
					<div class="box-body">
						<table id="TableDetailSoal" class="table table-bordered table-hover" style="margin-bottom: 0;">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="text-align: center;">Soal</th>
								</tr>
							</thead>
							<tbody id="bodyTableDetailSoal" style="background-color: #fcf8e3;">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<tr>
								<td rowspan="2" style="width: 20%; text-align: center; background-color: #fcf8e3">Tanggal Pelaksanaan</td>
								<td rowspan="2" style="width: 20%; text-align: center;">{{ $today }}</td>
								<td style="width: 20%; text-align: center; background-color: #fcf8e3">Nama Bagian</td>
								<td colspan="2" style="width: 20%; text-align: center; background-color: #fcf8e3">TTD</td>
							</tr>
							<tr>
								<td style="text-align: center;">TIM {{ $kode }}</td>
								<td rowspan="2" style="width: 20%; text-align: center; height: 70%">Ketua<br><br>{{ $ketua[0]->nama }}</td>
								<td rowspan="2" style="width: 20%; text-align: center; height: 70%">Wakil<br><br>{{ $a }}</td>
							</tr>
							<tr>
								<td style="width: 20%; text-align: center; background-color: #fcf8e3">Nama Pekerjaan/Kejadian</td>
								<td colspan="2" style="width: 20%; text-align: center;">{{ $kode_soal[0]->remark }}</td>
							</tr>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<tr>
								<td style="width: 20%; background-color: #fcf8e3">Tahap ke-1</td>
								<td style="width: 80%;">Menentukan jenis potensi bahaya yang akan terjadi sesuai dengan kasus yang ada</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #fcf8e3">Tahap ke-2</td>
								<td style="width: 80%;">Memprediksikan faktor bahaya (tindakan tidak aman+kondisi tidak aman) dan gejala yang bisa timbul. Serta memilih item yang paling dianggap berbahaya dari bahaya yang ditemukan dan Lingkarilah.</td>
							</tr>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 10%">No</th>
									<th style="width: 30%">Faktor Bahaya (Tindakan yang tidak aman+kondisi yang tidak aman)</th>
									<th style="width: 30%">Jenis Kecelakaan</th>
									<th style="width: 30%">Kesimpulan</th>
								</tr>
							</thead>
							<tr>
								<td rowspan="2">1.</td>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" id="isian_1" name="isian_1"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="jenis_1" name="jenis_1" style="height: 250px"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="kesimpulan_1" name="kesimpulan_1" style="height: 250px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[0] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[0] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[0] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" id="benda_1" name="benda_1" style="padding-top: 30px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[0] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								<td rowspan="2">2.</td>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" id="isian_2" name="isian_2"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="jenis_2" name="jenis_2" style="height: 250px"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="kesimpulan_2" name="kesimpulan_2" style="height: 250px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[1] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[1] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[1] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_2" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" id="benda_2" name="benda_2" style="padding-top: 30px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[1] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								<td rowspan="2">3.</td>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" id="isian_3" name="isian_3"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="jenis_3" name="jenis_3" style="height: 250px"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="kesimpulan_3" name="kesimpulan_3" style="height: 250px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[2] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[2] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[2] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_2" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" id="benda_3" name="benda_3" style="padding-top: 30px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[2] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								<td rowspan="2">4.</td>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" id="isian_4" name="isian_4"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="jenis_4" name="jenis_4" style="height: 250px"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="kesimpulan_4" name="kesimpulan_4" style="height: 250px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[3] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[3] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[3] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_2" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" id="benda_4" name="benda_4" style="padding-top: 30px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[3] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								<td rowspan="2">5.</td>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" id="isian_5" name="isian_5"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="jenis_5" name="jenis_5" style="height: 250px"></textarea></td>
								<td rowspan="2"><textarea class="form-control" id="kesimpulan_5" name="kesimpulan_5" style="height: 250px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-success" style="color: white; font-size: 10px;">Tidak Aman Orang</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								<td rowspan="2"><textarea class="form-control" style="height: 250px" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_2" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
							<tr>
								@if(count($cek) == '0')
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" id="benda_5" name="benda_5" style="padding-top: 30px"></textarea></td>
								@else
								<td style="padding-top: 10px"><span class="label label-info" style="color: white; font-size: 10px;">Tidak Aman Benda</span><br><br><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
								<!-- <td style="text-align: center"><a href="javascript:void(0)" class="btn btn-info btn-md" id="kesimpulan_1" onclick="ShowModalKesimpulan(this.id)"><i class="fa fa-file-pdf-o"></i> Isian</a></td> -->
							</tr>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<tr>
								<td style="width: 5.5%; background-color: #fcf8e3">Tahap ke-3</td>
								<td style="width: 20%;">(Menyusun Tindakan penanggulangan/apa yang akan anda lakukan). Memikirkan penanggulangan yang bisa dilakukan secara konkret untuk menyelesaikan item-item. [Point bahaya yang telah dilingkari]</td>
							</tr>
							<tr>
								<td style="width: 5.5%; background-color: #fcf8e3">Tahap ke-4</td>
								<td style="width: 20%;">Menetapkan [Item pelaksanaan penting]. Dan diberi tanda # kemudian menjadikannya target. Tindakan Tim untuk merealisasikannya.</td>
							</tr>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 5%">No</th>
									<th style="width: 95%">Penanggulangan Konkret</th>
								</tr>
							</thead>
							<tr>
								<td style="text-align: center; width: 5%">1</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="konkrit_1" name="konkrit_1"></textarea></td>
								<!-- <td style="text-align: center; width: 15%"><a href="javascript:void(0)" class="btn btn-info btn-md" id="btn_konkret_1" onclick="UpdateKonkrit(this.id)"><i class="fa fa-file-pdf-o"></i> Pilih</a></td> -->
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
							<tr>
								<td style="text-align: center; width: 5%">2</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="konkrit_2" name="konkrit_2"></textarea></td>
								<!-- <td style="text-align: center; width: 15%"><a href="javascript:void(0)" class="btn btn-info btn-md" id="btn_konkret_1" onclick="UpdateKonkrit(this.id)"><i class="fa fa-file-pdf-o"></i> Pilih</a></td> -->
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
							<tr>
								<td style="text-align: center; width: 5%">3</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="konkrit_3" name="konkrit_3"></textarea></td>
								<!-- <td style="text-align: center; width: 15%"><a href="javascript:void(0)" class="btn btn-info btn-md" id="btn_konkret_1" onclick="UpdateKonkrit(this.id)"><i class="fa fa-file-pdf-o"></i> Pilih</a></td> -->
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
							<tr>
								<td style="text-align: center; width: 5%">4</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="konkrit_4" name="konkrit_4"></textarea></td>
								<!-- <td style="text-align: center; width: 15%"><a href="javascript:void(0)" class="btn btn-info btn-md" id="btn_konkret_1" onclick="UpdateKonkrit(this.id)"><i class="fa fa-file-pdf-o"></i> Pilih</a></td> -->
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
							<tr>
								<td style="text-align: center; width: 5%">5</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="konkrit_5" name="konkrit_5"></textarea></td>
								<!-- <td style="text-align: center; width: 15%"><a href="javascript:void(0)" class="btn btn-info btn-md" id="btn_konkret_1" onclick="UpdateKonkrit(this.id)"><i class="fa fa-file-pdf-o"></i> Pilih</a></td> -->
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
						</table>
						<br>
						<table class="table table-bordered table-hover">
							<tr>
								<td style="text-align: center; width: 20%; background-color: #fcf8e3">Target / Tindakan Tim</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="tindakan_tim" name="tindakan_tim"></textarea></td>
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
							<tr>
								<td style="text-align: center; width: 20%; background-color: #fcf8e3">Item Ikrar (Yubishasi koshou)</td>
								@if(count($cek) == '0')
								<td style="text-align: center; width: 80%"><textarea class="form-control" id="ikrar" name="ikrar"></textarea></td>
								@else
								<td style="text-align: center; width: 80%"><textarea class="form-control" readonly>{{ $faktor_bahaya[4] }}</textarea></td>
								@endif
							</tr>
						</table>
						<br>
						@if(count($cek) == '0')
						<button id="simpan_ky" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" type="button"  onclick="KonfirmasiTim()">Konfirmasi Tim<br>提出物を提出する</button>
						@else
						<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center" id="TableKehadiranA">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="text-align: center; width: 30%">NIK</th>
									<th style="text-align: center; width: 30%">Nama Tim</th>
									<th style="text-align: center; width: 30%">Kehadiran</th>
								</tr>
							</thead>
							<tbody id="BodyTableKehadiranA" style="background-color: #fcf8e3;">
							</tbody>
						</table>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="ModalKesimpulan" role="dialog">
		<div class="modal-xs modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<!-- <form id ="importForm" name="importForm" method="post" action="{{ url('upload/dokumen/teknis') }}" enctype="multipart/form-data"> -->
							<!-- <input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="hidden" id="slip_id" name="slip_id">
							<input type="hidden" id="category_upload" name="category_upload"> -->
							<div class="nav-tabs-custom tab-danger" align="center">
								<ul class="nav nav-tabs">
									<div class="col-xs-12" style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;">Kesimpulan</span>
									</div>
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;" id="slip_teknis" name="slip_teknis"></span>
									</div>
								</ul>
							</div>
							<div class="form-group row" align="right">
								<div class="col-sm-12">
									<textarea style="height: 30%; width: 100%" class="form-control" id="kesimpulan" name="kesimpulan"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<center>
									<button type="button" id="button_simpan" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #78a1d0;">Simpan</button>
								</center>
							</div>
							<!-- </form> -->
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="ModalKonfirmasiTim" role="dialog">
				<div class="modal-xs modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<div class="nav-tabs-custom tab-danger" align="center">
								<ul class="nav nav-tabs">
									<div class="col-xs-12" style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;">Konfirmasi Tim</span>
									</div>
								</ul>
							</div>
							@if(count($cek) == '0')
							<div class="form-group row" align="center">
								<div class="col-md-12">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
										<i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
									</div>
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									<input type="text" style="text-align: center; font-size: 30px; height: 50px" class="form-control" id="nik_user" name="nik_user" placeholder="Scan NIK" required>
									<div class="input-group-addon" id="icon-serial">
										<i class="glyphicon glyphicon-ok"></i>
									</div>
								</div>
							</div>
							@endif
							<div class="form-group row" align="center">
								<div class="col-md-12">
									<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center" id="TableKehadiran">
										<thead style="background-color: #605ca8; color: white;">
											<tr>
												<th style="text-align: center; width: 30%">Nama Tim</th>
												<th style="text-align: center; width: 30%">Nama</th>
												<th style="text-align: center; width: 30%">Kehadiran</th>
											</tr>
										</thead>
										<tbody id="BodyTableKehadiran" style="background-color: #fcf8e3;">
										</tbody>
									</table>
								</div>
							</div>
							@if(count($cek) == '0')
							<div class="modal-footer">
								<center>
									<button id="button_simpan" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" type="button"  onclick="SimpanJawaban()">Simpan</button>
								</center>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</section>
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
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			jQuery(document).ready(function() {
				$('body').toggleClass("sidebar-collapse");
				ViewSoal();
				dataTim();
				kataKunci();

				$('.select2').select2();
			});

			var global_key = [];

			function SimpanJawaban(){
				$("#loading").show();
				var isian_1 = $('#isian_1').val();
				var isian_2 = $('#isian_2').val();
				var isian_3 = $('#isian_3').val();
				var isian_4 = $('#isian_4').val();
				var isian_5 = $('#isian_5').val();

				var benda_1 = $('#benda_1').val();
				var benda_2 = $('#benda_2').val();
				var benda_3 = $('#benda_3').val();
				var benda_4 = $('#benda_4').val();
				var benda_5 = $('#benda_5').val();

				var jenis_1 = $('#jenis_1').val();
				var jenis_2 = $('#jenis_2').val();
				var jenis_3 = $('#jenis_3').val();
				var jenis_4 = $('#jenis_4').val();
				var jenis_5 = $('#jenis_5').val();

				var kesimpulan_1 = $('#kesimpulan_1').val();
				var kesimpulan_2 = $('#kesimpulan_2').val();
				var kesimpulan_3 = $('#kesimpulan_3').val();
				var kesimpulan_4 = $('#kesimpulan_4').val();
				var kesimpulan_5 = $('#kesimpulan_5').val();

				var nama_tim = $('#nama_tim').val();
				var kode_soal = $('#test_kode_soal').val();

				var konkrit_1 = $('#konkrit_1').val();
				var konkrit_2 = $('#konkrit_2').val();
				var konkrit_3 = $('#konkrit_3').val();
				var konkrit_4 = $('#konkrit_4').val();
				var konkrit_5 = $('#konkrit_5').val();

				var tindakan_tim = $('#tindakan_tim').val();
				var ikrar = $('#ikrar').val();

				var data = {
					isian_1 : isian_1,
					isian_2 : isian_2,
					isian_3 : isian_3,
					isian_4 : isian_4,
					isian_5 : isian_5,
					jenis_1 : jenis_1,
					jenis_2 : jenis_2,
					jenis_3 : jenis_3,
					jenis_4 : jenis_4,
					jenis_5 : jenis_5,
					kesimpulan_1 : kesimpulan_1,
					kesimpulan_2 : kesimpulan_2,
					kesimpulan_3 : kesimpulan_3,
					kesimpulan_4 : kesimpulan_4,
					kesimpulan_5 : kesimpulan_5,
					nama_tim : nama_tim,
					kode_soal : kode_soal,
					konkrit_1 : konkrit_1,
					konkrit_2 : konkrit_2,
					konkrit_3 : konkrit_3,
					konkrit_4 : konkrit_4,
					konkrit_5 : konkrit_5,
					tindakan_tim : tindakan_tim,
					ikrar : ikrar,
					benda_1 : benda_1,
					benda_2 : benda_2,
					benda_3 : benda_3,
					benda_4 : benda_4,
					benda_5 : benda_5
				}
				$.post('{{ url("insert/jawaban") }}', data, function(result, status, xhr) {
					if(result.status){
						$("#loading").hide();
						openSuccessGritter('Success','Berhasil Di Simpan');
						$('#ModalKonfirmasiTim').modal('hide');
						// location.href("{{url('index/ky_hh')}}");
						window.location.replace("{{url('index/ky_hh')}}");

					}
					else{
						openErrorGritter('Error!', result.message);
						$("#loading").hide();
					}
				})
			}

			function ShowModalKesimpulan(id){
				$('#ModalKesimpulan').modal('show');
				// console.log(id);
			}

			function UpdateKonkrit(id){
				// console.log(id)
				if (id == 'btn_konkret_1') {
					console.log('konkret_1');
				}else{
					console.log('konkret_5');
				}
			}

			function dataTim(){
				var nama_tim = $('#nama_tim').val();
				var kode_soal = $('#test_kode_soal').val();
				
				var data = {
					nama_tim : nama_tim,
					kode_soal : kode_soal
				}

				$.get('{{ url("fetch/tim") }}', data, function(result, status, xhr) {
					$('#TableKehadiranA').DataTable().clear();
					$('#TableKehadiranA').DataTable().destroy();
					$("#BodyTableKehadiranA").empty();
					var body = '';

					$.each(result.tim_hadir, function(index, value){
						body += '<tr>';
						body += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
						body += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
						if (value.remark ==  null) {
							body += '<td style="text-align: center; background-color: RGB(255,204,255)">Tidak Hadir</td>';
						}else{
							body += '<td style="text-align: center; background-color: RGB(204,255,255)">'+ value.remark +'</td>';
						}
						body += '</tr>';
					})

					$("#BodyTableKehadiranA").append(body);
				})
			}

			function kataKunci(){
				$.get('{{ url("fetch/keywords") }}', function(result, status, xhr) {
					for (var i = 0; i < result.keywords.length; i++) {
						global_key.push(result.keywords[i].kata_kunci);
					}
				})	
			}

			function CheckKeyword(value){
				var a = value.split(' ');
				var j = 0;
				for (var z = 0; z < a.length; z++) {
					for (var i = 0; i < global_key.length; i++) {
						if (a[z] == global_key[i]) {
							j++;
						}
					}
				}

				if (j >= 1) {
					openSuccessGritter('Success!', 'NIK Ditemukan');
				}
			}


			function KonfirmasiTim(){
				$('#ModalKonfirmasiTim').modal('show');
				var nama_tim = $('#nama_tim').val();
				var kode_soal = $('#test_kode_soal').val();
				
				var data = {
					nama_tim : nama_tim,
					kode_soal : kode_soal
				}
				$.get('{{ url("fetch/tim") }}', data, function(result, status, xhr) {
					$('#nik_user').focus();
					// var nama_tim = result.kode;
					$('#nik_user').keydown(function(event) {
						if (event.keyCode == 13 || event.keyCode == 9) {
							var nik = $("#nik_user").val();
							var data = {
								nama_tim:nama_tim,
								nik:nik
							}
							$.get('<?php echo e(url("confirm/kehadiran")); ?>', data, function(result, status, xhr){
								if(result.status){
									openSuccessGritter('Success!', result.message);
									$("#nik_user").val('');
									// KonfirmasiTim()
									RefreshTim(nama_tim, kode_soal);
								}
								else{
									openErrorGritter('Error!', result.message);
									$("#nik_user").val('');
									return false;
								}
							});
						}
					});

					$('#TableKehadiran').DataTable().clear();
					$('#TableKehadiran').DataTable().destroy();
					$("#BodyTableKehadiran").empty();
					var body = '';

					var tim = [];
					tim.push(nama_tim);

					// console.log(tim);
					$.each(result.tim, function(index, value){
						body += '<tr>';
						body += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
						body += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
						if (value.remark ==  null) {
							// body += '<td style="text-align: center; background-color: RGB(255,204,255)">Tidak Hadir</td>';
							body += '<td><select class="form-control select2" id="kehadiran" name="kehadiran" onChange="KonfirmasiAlasan(\''+tim+'\', this.value, \''+value.nik+'\')" data-placeholder="Alasan Kehadiran" style="width: 100%"><option value="">&nbsp;</option><option value="Sakit">Sakit</option><option value="Izin">Izin</option><option value="Cuti">Cuti</option></select></td>';
						}else{
							body += '<td style="text-align: center; background-color: RGB(204,255,255)">'+ value.remark +'</td>';
						}
						body += '</tr>';
					})

					$("#BodyTableKehadiran").append(body);
					$('.select2').select2({
						dropdownParent: $('#BodyTableKehadiran'),
						allowClear : true,
					});
				})
			}

			function RefreshTim(nama_tim, kode_soal){
				var data = {
					nama_tim : nama_tim,
					kode_soal : kode_soal
				}
				$.get('{{ url("fetch/tim") }}', data, function(result, status, xhr) {
					$('#TableKehadiran').DataTable().clear();
					$('#TableKehadiran').DataTable().destroy();
					$("#BodyTableKehadiran").empty();
					var body = '';

					var nama_tim = $('#nama_tim').val();
					var tim = [];
					tim.push(nama_tim);

					$.each(result.tim, function(index, value){
						body += '<tr>';
						body += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
						body += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
						if (value.remark ==  null) {
							// body += '<td style="text-align: center; background-color: RGB(255,204,255)">Tidak Hadir</td>';
							body += '<td><select class="form-control select2" id="kehadiran" name="kehadiran" onChange="KonfirmasiAlasan(\''+tim+'\', this.value, \''+value.nik+'\')" data-placeholder="Alasan Kehadiran" style="width: 100%"><option value="">&nbsp;</option><option value="Sakit">Sakit</option><option value="Izin">Izin</option><option value="Cuti">Cuti</option></select></td>';
						}else{
							body += '<td style="text-align: center; background-color: RGB(204,255,255)">'+ value.remark +'</td>';
						}
						body += '</tr>';
					})

					$("#BodyTableKehadiran").append(body);
					$('.select2').select2({
						dropdownParent: $('#BodyTableKehadiran'),
						allowClear : true,
					});
				})
			}

			function KonfirmasiAlasan(tim, value, nik){
				var data = {
					nama_tim:tim,
					nik:nik,
					value:value
				}

				$.get('<?php echo e(url("confirm/kehadiran")); ?>', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'NIK Ditemukan');
						$("#nik_user").val('');
						KonfirmasiTim()
					}
					else{
						openErrorGritter('Error!', 'NIK Tidak Ditemukan');
						$("#nik_user").val('');
					}
				});
			}

			function ViewSoal(){
				var kode_soal = $("#test_kode_soal").val();
				var data = {
					kode_soal:kode_soal
				}
				$.get('{{ url("fetch/soal/ky") }}', data, function(result, status, xhr){

					if(result.status){
						$('#TableDetailSoal').DataTable().clear();
						$('#TableDetailSoal').DataTable().destroy();
						$('#bodyTableDetailSoal').html("");
						var tableData = "";
						$.each(result.resumes, function(key, value) {

							var judul = value.kode_soal+' - '+value.remark;
							$('#id_kode_soal').html(judul);
							$('#kode_soal').html(value.kode_soal);

							$('#attach_pdf').html('');
							var pdf = "{{ url('data_file/std/ky') }}"+'/'+value.nama_gambar;
							$('#attach_pdf').append('<img src="'+pdf+'" style="width: 40vw; height: auto">');

							tableData += '<tr>';
							tableData += '<td style="text-align: left;">'+ value.soal +'</td>';
							tableData += '</tr>';
						});
						$('#bodyTableDetailSoal').append(tableData);
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				});
			}

			function SelectPeriode(){
				var periode = $('#select-periode').val();
				var p = periode.split("/");
				var id_team = $('#nama_tim').val();
				var data = {
					periode : p[0],
					id_team : id_team
				}

				$.post('{{ url("update/kode_soal/input") }}', data, function(result, status, xhr) {
					if(result.status){
						openSuccessGritter('Success!', result.message);
						window.location.href = '{{ url('index/soal/ky') }}/'+id_team+'/'+p[1]+'';
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});
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