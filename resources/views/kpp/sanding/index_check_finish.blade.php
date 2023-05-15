
@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
		overflow:hidden;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:2px;
	}

	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	input[type=radio]{
		width: 18px;
		height: 18px;
	}

	table {
		margin-bottom: 10px !important;
	}

	.radio_text {
		font-weight: bold;
		font-size: 20px;
		margin-right: 5px;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<!-- <h1>
		<div class="col-xs-12 col-md-9 col-lg-9">
			<h3 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
		</div>
	</h1> -->
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 3px">
			<center>
				<table style="margin-bottom: 2px; border: 1px solid black">
					<tr>
						<td style="padding: 2px 10px 2px 5px; background-color: #2e51ff; color: white; font-weight: bold">OPERATOR : </td>
						<td id="op_nik" style="padding: 2px 2px 2px 10px">{{ explode('/', $material->checker)[0] }}</td>
						<td id="op_name" style="padding: 2px 10px 2px 2px">{{ explode('/', $material->checker)[1] }}</td>
						<td style="padding: 2px 10px 2px 5px; background-color: #2e51ff; color: white; font-weight: bold">MATERIAL NUMBER : </td>
						<td id="material_number" style="padding: 2px 10px 2px 10px">{{ $material->material_number }}</td>
					</tr>
				</table>
				<label id="judul" style="font-size: 18px">CEK VISUAL MATERIAL <span id="gmc">{{ $check_point[0]->material_description }}</span></label>
			</center>
			<span style="color: red; background-color: yellow; font-weight: bold; font-size: 14px">&nbsp; 2. Pahami point kualitas material finish sebelum melakukan proses &nbsp;</span>
		</div>
		<div class="col-xs-3">
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white">FOTO MATERIAL KESELURUHAN</th>
				</tr>
				<tr>
					<td id="material_photo"><img src="{{ url('files/sanding/visual_check/MF_'.$check_point[0]->material_number.'.jpg') }}" style='margin: 3px 0px 3px 0px; max-width: 250px'></br></td>
				</tr>
			</table>
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white">PENJELASAN KUALITAS (Level)</th>
				</tr>
				<tr>
					<td style="text-align: left">
						<b>Paham</b> = Memahami kualitas produk dan cara proses yang benar<br><br>
						<b>Tidak</b> = Tidak = Tidak memahami kualitas produk dan cara proses yang benar<br><br>
					</td>
				</tr>
			</table>

			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff;"><a href="{{ url('files/sanding/visual_check/ik/'.$check_point[0]->ik) }}" style="color: white" target="_blank">DOKUMEN IK (FILE <i class="fa fa-file-text"></i>)</a></th>
				</tr>
			</table>

			<?php 
			$video = explode(',', $check_point[0]->remark);

			foreach ($video as $vid) {
				$name = explode('_', $vid);
				// dd($name[2]);
				$name2 = explode('.', $name[3]);
				print_r('<table class="table table-bordered" style="width: 100%">');
				print_r('<tr><th style="background-color: #2e51ff;"><a href="'.url('files/sanding/visual_check/'.trim($vid)).'" style="color: white" target="_blank">VIDEO PROCESS '.$name2[0].' <i class="fa fa-video-camera"></i></a></th></tr>');
				print_r('</table>');
			}
			?>
		</div>

		<div class="col-xs-9">
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white" id="head_item" colspan="{{ count($check_point) }}">DETAIL POIN CEK MATERIAL FINISH</th>
				</tr>
				<tr id="text_item">
					<?php 
					foreach ($check_point as $cp) {
						print_r("<td style='width:1%; vertical-align: top'>".$cp->point.". ".$cp->description."<br>");
						print_r("<img src='".url('files/sanding/visual_check/MF_'.$cp->material_number.'_P'.$cp->point.'.jpg')."' style='margin: 3px 0px 3px 0px; max-width: 250px'></td>");
					}
					?>
				</tr>
				<tr id="photo_item">
				</tr>
				<tr id="pointer_item">
					<?php 
					foreach ($check_point as $cp) {
						print_r('<td><label class="btn btn-default radio_text"><input type="radio" name="check_'.$cp->point.'" value="Paham">&nbsp;Paham</label>&nbsp;');
						print_r('<label class="btn btn-default radio_text"><input type="radio" name="check_'.$cp->point.'" value="Tidak">&nbsp;Tidak</label></td>');
					}
					?>
				</tr>
			</table>
			<button class="btn btn-success" style="width: 100%; font-weight: bold; margin-bottom: 5px;" onclick="save_data()"><i class="fa fa-check"></i> LANJUTKAN PROSES</button>
			<div id="chart_container">
				<div class="col-xs-6">
					<div id="chart" style="width: 99%;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalError">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" style="background-color: rgba(247, 88, 79, 0.8);">
				<div class="modal-body no-padding">
					<div class="form-group">
						<center>
							<br>
							<label style="font-size: 25px; font-weight: bold; color: white">ANDA TIDAK MEMAHAMI KUALITAS, JANGAN LANJUTKAN PROSES</label> <br>
							<label style="font-size: 25px; font-weight: bold; color: white">SILAHKAN MELIHAT VIDEO PROCESS & IK (*WAJIB)</label><br>
							<?php 
							$video = explode(',', $check_point[0]->remark);

							foreach ($video as $vid) {
								$name = explode('_', $vid);
								$name2 = explode('.', $name[3]);

								print_r('<input type="checkbox" class="video" disabled value="OK"> <a href="'.url('files/sanding/visual_check/'.trim($vid)).'" style="color: white; margin-right: 3px" target="_blank" class="btn btn-primary" onclick="cek_item(this)"><b>VIDEO PROCESS</b> <br> '.$name2[0].' <i class="fa fa-video-camera"></i></a>');
							}
							?>
							<br>
							<input type="checkbox" class="video" disabled>
							<a href="{{ url('files/sanding/visual_check/IK-Sanding-Pintop.pdf') }}" class="btn btn-primary" onclick="cek_item(this)" style="margin-top: 3px" target="_blank">DOKUMEN IK (FILE <i class="fa fa-file-text"></i>)</a>
							<br><br>

							<button class="btn btn-success" style="width: 80%; font-weight: bold; margin-bottom: 5px;" onclick="next_step()">LANJUTKAN PROSES</button>
							<br>
						</center>
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

<!-- <script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var len = <?php echo json_encode($check_point); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});	

	function save_data() {
		var stat = 1;
		$.each(len, function(key, value) {
			if ( ! $("input[name='check_"+value.point+"']").is(':checked') ) {

				stat = 0;
				return false;
			}		
		})


		if (stat == 0) {
			openErrorGritter('Error', 'Semua Poin Cek Harus Diisi');
			return false;
		}

		if (confirm('Anda Yakin untuk Menyimpan Data?')) {
			var hasil_cek = [];
			var status_cek = 1;

			$.each(len, function(key, value) {
				hasil_cek.push({'point' : value.point, 'description' : value.description, 'value' : $("input[name='check_"+value.point+"']:checked").val()})

				if ($("input[name='check_"+value.point+"']:checked").val() == 'Tidak') {
					status_cek = 0;
				}
			})

			if (status_cek == 0) {
				$('#modalError').modal({
					backdrop: 'static',
					keyboard: false
				});

				return false;
			}

			$("#loading").show();

			var data = {
				form_number : '{{ Request::segment(5) }}',
				material_number : '{{ $check_point[0]->material_number }}',
				material_desc : '{{ $check_point[0]->material_description }}',
				check : hasil_cek,
				status : 'OK'
			}

			$.post('{{ url("input/material_check/sanding/finish") }}', data, function(result, status, xhr){
				if (result.status) {
					$("#loading").hide();
					openSuccessGritter('sukses', 'Data Berhasil tersimpan');
					window.setTimeout(function(){window.location.href = "{{ url('index/material_check/sanding') }}";}, 2000);
				}
			})
		}
	}

	function cek_item(elem) {
		$(elem).prev().prop('checked', true)
	}

	function next_step() {
		var stat = 1;

		$('.video').each(function(i, obj) {
			if ( ! $(obj).is(':checked') ) {
				stat = 0;
				return false;
			}
		});

		if (stat == 0) {
			openErrorGritter('Error', 'Semua Video harus dilihat dan IK harus dibaca');
			return false;
		}

		 // ---------------------  INPUT ---------------------------

		 var hasil_cek = [];
		 var status_cek = 1;

		 $.each(len, function(key, value) {
		 	hasil_cek.push({'point' : value.point, 'description' : value.description, 'value' : $("input[name='check_"+value.point+"']:checked").val()})
		 })

		 $("#loading").show();

		 var data = {
		 	form_number : '{{ Request::segment(5) }}',
		 	material_number : '{{ $check_point[0]->material_number }}',
		 	material_desc : '{{ $check_point[0]->material_description }}',
		 	op : $("#op_nik").text()+'/'+$("#op_name").text(),
		 	check : hasil_cek,
		 	status : 'SUDAH TRAINING'
		 }

		 $.post('{{ url("input/material_check/sanding/finish") }}', data, function(result, status, xhr){
		 	if (result.status) {
		 		$("#loading").hide();
		 		openSuccessGritter('sukses', 'Data Berhasil tersimpan');
		 		window.setTimeout(function(){window.location.href = "{{ url('index/material_check/sanding') }}";}, 2000);
		 	}
		 })
		}

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '3000'
			});

			audio_ok.play();
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '3000'
			});

			audio_error.play();
		}

	</script>
	@endsection