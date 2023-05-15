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

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 35%; left: 30%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
				<span style="font-weight: bold; font-size: 1.6vw;"><span>Penanganan Hiyari Hatto <br> ({{ $data[0]->request_id}})</span></span>
			</div>
		</div>
		<div class="col-xs-12">
			<form id="importForm" name="importForm" method="post" action="{{ url('update/penanganan/hiyarihatto') }}"
			enctype="multipart/form-data">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<input type="hidden" name="request_id" value="{{ $data[0]->request_id}}">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
						<thead>
							<tr>
								<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
								<td style="font-weight: bold;font-size: 13px; text-align: right">No Dokumen : YMPI/STD/FK3/044</td>
							</tr>
							<tr>
								<td colspan="9" style="text-align: left;font-size: 13px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
							</tr>
							<tr>
								<td colspan="9" style="text-align: left;font-size: 13px">Phone : (0343) 740290 Fax : (0343) 740291</td>
							</tr>
							<tr>
								<td colspan="10" style="text-align: left;font-size: 13px">Jawa Timur Indonesia</td>
							</tr>
						</thead>
					</table>
					<br>
					<?php
					$karyawan = explode('/', $data[0]->karyawan);
					// $saksi = explode('/', $data[0]->saksi);
					$kejadian = explode('/', $data[0]->ringkasan);
					$detail = explode('/', $data[0]->detail);
					$level = explode('/', $data[0]->level);
					?>
					<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
						<thead>
							<tr>
								<td colspan="2" style="text-align: center; width: 50%"> Category Resiko Hiyari Hatto :</td>
							</tr>
							@if($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Tinggi')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white;">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Sedang')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Rendah')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Tinggi')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: yellow; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Sedang')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: yellow; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Rendah')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Tinggi')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Sedang')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Rendah')
							<tr>
								<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
							</tr>
							@endif
						</thead>
					</table>
					<br>
					<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center">
						<tbody style="background-color: #fcf8e3;">
							<tr>
								<td style="text-align: center; width: 100%; font-size: 13px" colspan="2">Nama Tim : {{ $data[0]->nama_tim}}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Pelapor</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $karyawan[0] }} - {{ $karyawan[1] }}<br>{{ $karyawan[3] }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Saksi</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{  $data[0]->saksi }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Tanggal Kejadian</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->tanggal }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Lokasi Kejadian</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->lokasi }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Ringkasan Kejadian</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $kejadian[0] }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Alat Pelindung Diri yang digunakan (jika ada)</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $kejadian[1] }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Keparahan</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $detail[0] }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px">Kemungkinan</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $detail[1] }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->perbaikan }}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px"> Informasi Lain</td>
								<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->lain_lain}}</td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px;"> Penanganan</td>
								<td style="padding-top: 10px"><textarea class="form-control" id="penanganan" name="penanganan" style="padding-top: 30px"></textarea></td>
							</tr>
							<tr>
								<td style="text-align: left; width: 50%; font-size: 13px;"> Bukti Penanganan</td>
								<td style="padding-top: 10px"><input type="file" name="bukti_penanganan" id="bukti_penanganan" onchange="previewImage()" accept="image/*,application/pdf"><br><img id="image-preview" style="width: 500px"></td>
							</tr>
						</tbody>
					</table>
					<br>
					<center>
						<!-- <button type="submit" id="button_upload" class="btn btn-succes"
						style="font-weight: bold; font-size: 1.3vw; width: 100%; color: white; background-color: #78a1d0;" onclick="SimpanPenanganan()">Simpan</button> -->
						<button type="submit" id="button_upload" class="btn btn-succes"
						style="font-weight: bold; font-size: 1.3vw; width: 100%; color: white; background-color: #2ecc71;">Simpan</button>
					</center>
				</div>
			</div>
		</form>
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
<script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#bukti_penanganan').on('change',function(){
			var fileName = $('input[type=file]').val().split('\\').pop();
			$(this).next('.custom-file-label').html(fileName);
		})
	});

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

	function SimpanPenanganan(){
		$("#loading").show();
		var request_id = '{{ $data[0]->request_id}}';
		var penanganan = $("#penanganan").val();

		if (penanganan.length != 0) {
			var data = {
				request_id : request_id,
				penanganan : penanganan
			}
			$.post('{{ url("update/penanganan/hiyarihatto") }}', data, function(result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Berhasil Di Simpan');
					window.location.replace("{{url('index/ky_hh')}}");
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			})
		}else{
			$("#loading").hide();
			openErrorGritter('Error!', 'Isikan Penanganan Terlebih Dahulu');
			return false;
		}
	}

	function previewImage() {
		document.getElementById("image-preview").style.display = "block";
		var oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById("bukti_penanganan").files[0]);
		oFReader.onload = function(oFREvent) {
			document.getElementById("image-preview").src = oFREvent.target.result;
		};
	};
</script>
@endsection