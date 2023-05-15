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
							<div class="col-md-5">
								<div class="form-group">
									<div>
										<img src="{{ url('data_file/std/ky/'.$data[0]->nama_gambar.'') }}" style="width: 37vw"> 
										<input type="hidden" value="{{ $nama_tim }}" id="nama_tim">
										<input type="hidden" value="{{ $data[0]->kode_soal }}" id="kode_soal">
									</div>
								</div>
							</div>
							<div class="col-md-7" align="center">
								<div class="form-group">
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<center>
											<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
												<thead>
													<tr>
														<td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
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

											<table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
												<tbody>
													<tr>
														<td style="border:1px solid black; font-size: 12px; width: 5%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">No</td>
														<td style="border:1px solid black; font-size: 12px; width: 35%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Faktor Bahaya (Tindakan yang tidak aman+kondisi yang tidak aman)</td>
													</tr>
													<?php
													$search = array('[', ']');
													$replace = array('', '');
													$data = explode('","', str_replace($search, $replace, $data[0]->jawaban));

													for ($i=0; $i < count($data); $i++) { 
														$no = $i+1;
														print_r('<tr><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$no++.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: left;">'.str_replace('"', '', $data[$i]).'</td></tr>');
													}
													?> 
												</tbody>            
											</table><br>
											<button id="simpan_ky" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" type="button"  onclick="KonfirmasiSosialisasi()">Konfirmasi Sosialisasi<br>提出物を提出する</button>
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

	<div class="modal fade" id="ModalKonfirmasiKehadiran" role="dialog">
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
					

					<!-- <div class="modal-footer">
						<center>
							<button id="button_simpan" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" type="button"  onclick="SimpanSosialisasi()">Simpan</button>
						</center>
					</div> -->

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

	function KonfirmasiSosialisasi(){
		$('#ModalKonfirmasiKehadiran').modal('show');
		var nama_tim = $('#nama_tim').val();
		var kode_soal = $('#kode_soal').val();

		var data = {
			nama_tim:nama_tim,
			kode_soal:kode_soal
		}

		$.get('{{ url("fetch/sosialisasi_ulang/ky") }}', data, function(result, status, xhr) {
			$('#nik_user').focus();
			$('#nik_user').keydown(function(event) {
				if (event.keyCode == 13 || event.keyCode == 9) {
					var nik = $("#nik_user").val();
					var data = {
						nama_tim:nama_tim,
						nik:nik,
						kode_soal:kode_soal
					}
					$.get('<?php echo e(url("confirm/sosialisasi_ulang")); ?>', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$("#nik_user").val('');
							// KonfirmasiSosialisasi();
						}
						else{
							openErrorGritter('Error!', result.message);
							$("#nik_user").val('');
							return false;
						}
					});
				}
			});

			// $('#TableKehadiran').DataTable().clear();
			// $('#TableKehadiran').DataTable().destroy();
			// $("#BodyTableKehadiran").empty();
			// var body = '';

			// $.each(result.tim, function(index, value){
			// 	body += '<tr>';
			// 	body += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
			// 	body += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
			// 	if (value.remark ==  null) {
			// 		body += '<td style="text-align: center; background-color: RGB(255,204,255)">Tidak Hadir</td>';
			// 	}else{
			// 		body += '<td style="text-align: center; background-color: RGB(204,255,255)">'+ value.remark +'</td>';
			// 	}
			// 	body += '</tr>';
			// })

			// $("#BodyTableKehadiran").append(body);
		})
	}

	function TableList(){
		$.get('{{ url("fetch/sosialisasi_ulang/ky") }}', data, function(result, status, xhr) {
			$('#TableKehadiran').DataTable().clear();
			$('#TableKehadiran').DataTable().destroy();
			$("#BodyTableKehadiran").empty();
			var body = '';

			$.each(result.tim, function(index, value){
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

			$("#BodyTableKehadiran").append(body);
		})
	}

	function KonfirmasiTim(){
		$('#ModalKonfirmasiTim').modal('show');
		var nama_tim = $('#nama_tim').val();
		var data = {
			nama_tim : nama_tim
		}
		$.get('{{ url("fetch/tim") }}', data, function(result, status, xhr) {
			$('#nik_user').focus();
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
							RefreshTim(nama_tim);
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

			$.each(result.tim, function(index, value){
				body += '<tr>';
				body += '<td style="text-align: center;">'+ value.nama_tim +'</td>';
				body += '<td style="text-align: center;">'+ value.nama +'<br>('+value.posisi+')</td>';
				if (value.remark ==  null) {
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
</script>
@endsection