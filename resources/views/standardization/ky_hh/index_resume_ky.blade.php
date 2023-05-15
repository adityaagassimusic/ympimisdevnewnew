@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 70%;
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
	/*.table > tbody > tr:hover {
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
	}*/
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
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<center>
											<table style="width: 70%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
												<thead>
													<tr>
														<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
														<td>
															<!-- <a type="button" target="_blank" class="btn btn-block btn-primary" style="width: 50%" href="{{ url('report/pdf/kiken_yochi')}}/{{$resume['id']}}"><i class="fa fa-print" aria-hidden="true"></i> Print</a> -->
														</td>
													</tr>
													<tr>
														<td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
													</tr>
													<tr>
														<td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
													</tr>
													<tr>
														<td colspan="10" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
													</tr>
												</thead>
											</table>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody align="center">
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; width: 25%; height: 30; background-color:  #e8daef ">Tanggal Pelaksanaan</td>
														<td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; width: 25%">{{ $resume['data'][0]->tanggal }}</td>
														<td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color: #e8daef; width: 25%">Nama Bagian</td>
														<td colspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color: #e8daef; width: 25%">TTD</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30;">{{ $resume['data'][0]->nama_tim }} - {{ $resume['data'][0]->department_short }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 60;">Appproved</td>
														<td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 60;">Appproved</td>
													</tr>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">Nama Pekerjaan/Kejadian</td>
														<td rowspan="2" colspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30;">{{ $resume['data'][0]->kode_soal }} - {{ $resume['data'][0]->remark }}</td>
														<input type="hidden" id="test_kode_soal" value="{{ $resume['data'][0]->kode_soal }}">
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">{{ $resume['data'][0]->ketua }}</td>
														<td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">{{ $resume['data'][0]->wakil }}</td>
													</tr>
												</tbody>            
											</table>
											<br>
											<div id="attach_pdf" align="center"></div>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 1</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%">Menentukan jenis potensi bahaya yang akan terjadi sesuai dengan kasus yang ada</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 2</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%">Memprediksikan faktor bahaya (tindakan tidak aman+kondisi tidak aman) dan gejala yang bisa timbul. Serta memilih item yang paling dianggap berbahaya dari bahaya yang ditemukan dan lingkarilah.</td>
													</tr>
												</tbody>            
											</table>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">No</td>
														<td style="border:1px solid black; font-size: 12px; width: 35%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Faktor Bahaya (Tindakan yang tidak aman+kondisi yang tidak aman)</td>
														<td style="border:1px solid black; font-size: 12px; width: 20%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Jenis Kecelakaan</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Kesimpulan</td>
													</tr>
													<?php
													$faktor_bahaya = explode('/', $resume['data'][0]->faktor_bahaya);
													$faktor_benda = explode('/', $resume['data'][0]->faktor_benda);
													$jenis_kecelakaan = explode('/', $resume['data'][0]->jenis_kecelakaan);
													$kesimpulan = explode('/', $resume['data'][0]->kesimpulan);
													$konkrit = explode('/', $resume['data'][0]->konkrit);
													?>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">1.</td>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[0] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $jenis_kecelakaan[0] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $kesimpulan[0] }}</td>
													</tr>
													<tr>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[0] }}</td>
													</tr>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">2.</td>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[1] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $jenis_kecelakaan[1] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $kesimpulan[1] }}</td>
													</tr>
													<tr>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[1] }}</td>
													</tr>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">3.</td>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[2] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $jenis_kecelakaan[2] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $kesimpulan[2] }}</td>
													</tr>
													<tr>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[2] }}</td>
													</tr>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">4.</td>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[3] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $jenis_kecelakaan[3] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $kesimpulan[3] }}</td>
													</tr>
													<tr>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[3] }}</td>
													</tr>
													<tr>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">5.</td>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[4] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $jenis_kecelakaan[4] }}</td>
														<td rowspan="2" style="border:1px solid black; font-size: 12px; width: 20%; height: 25; text-align: center;">{{ $kesimpulan[4] }}</td>
													</tr>
													<tr>
														<td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[4] }}</td>
													</tr>
												</tbody>            
											</table>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 3</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%">(Menyusun Tindakan penanggulangan/apa yang akan anda lakukan). Memikirkan penanggulangan yang bisa dilakukan secara konkret untuk menyelesaikan item-item. [Point bahaya yang telah dilingkari].</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 4</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%">Menetapkan [Item pelaksanaan penting]. Dan diberi tanda # kemudian menjadikannya target tindakan Tim untuk merealisasikannya.</td>
													</tr>
												</tbody>            
											</table>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">No</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Penanggulangan Konkret</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">#</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">1.</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[0] }}</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">2.</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[1] }}</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">3.</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[2] }}</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">4.</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[3] }}</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">5.</td>
														<td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[4] }}</td>
														<td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
													</tr>
												</tbody>            
											</table>
											<br>

											<table class="table table-bordered" style="border:1px solid black; width: 70%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Target / Tindakan Tim</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%; font-weight: bold; height: 25; text-align: center;">{{ $resume['data'][0]->target_tindakan }}</td>
													</tr>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Item Ikrar (Yubishasi Koshou)</td>
														<td style="border:1px solid black; font-size: 12px; width: 75%; font-weight: bold; height: 25; text-align: center;">{{ $resume['data'][0]->ikrar }}</td>
													</tr>
												</tbody>            
											</table>
											<br>

											<span>Informasi Kehadiran Anggota</span>

											<table class="table table-bordered table-hover" style="width: 70%; margin-bottom: 0px; text-align: center; border:1px solid black">
												<thead style="background-color: #605ca8; color: white;">
													<tr>
														<th style="text-align: center; width: 30%; border:1px solid black">Nama Tim</th>
														<th style="text-align: center; width: 30%; border:1px solid black">Nama</th>
														<th style="text-align: center; width: 30%; border:1px solid black">Kehadiran</th>
													</tr>
												</thead>
												<tbody style="background-color: #fcf8e3;">
													<?php
													for ($i=0; $i < count($resume['data_kehadiran']); $i++) { 
														$no = $i+1;
														print_r('<tr><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$no++.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$resume['data_kehadiran'][$i]->nama.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$resume['data_kehadiran'][$i]->remark.'</td></tr>');
													}
													?> 
												</tbody>
											</table>
										</center>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
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
		ViewSoal();
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

	function ViewSoal(){
		var kode_soal = $("#test_kode_soal").val();
		var data = {
			kode_soal:kode_soal
		}
		$.get('{{ url("fetch/soal/ky") }}', data, function(result, status, xhr){

			if(result.status){
				$.each(result.resumes, function(key, value) {
					$('#attach_pdf').html('');
					var pdf = "{{ url('data_file/std/ky') }}"+'/'+value.nama_gambar;
					$('#attach_pdf').append('<img src="'+pdf+'" style="width: 40vw; height: auto">');
				});
			}
			else{
				alert('Gambar Tidak Ditemukan.');
			}
		});
	}

	function PrintReport(){
		var id = $("#id_ky").val();
		var data = {
			id:id
		}

		$.get('{{ url("report/pdf/kiken_yochi") }}', data, function(result, status, xhr){

			if(result.status){
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
</script>
@endsection