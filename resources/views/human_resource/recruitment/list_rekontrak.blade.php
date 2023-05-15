@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
#loading, #error { display: none; }
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
	padding:0;
	font-size: 13px;
	text-align: center;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid black;
	padding:0;
}
td{
	overflow:hidden;
	text-overflow: ellipsis;
}

input[type=number] {
	-moz-appearance:textfield; /* Firefox */
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
	background-color: #ffd8b7;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
	background-color: #FFD700;
}
#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">INPUT PENILAIAN</span>
							<span style="font-size: 25px;color: black;width: 25%;">人事部</span>
						</div>
					</div>
				</div>
			</div>
		</div> 
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table class="table table-hover table-striped table-bordered" id="tablePeserta">
							<thead style="background-color: rgb(126,86,134); color: white;">
								<tr>
									<th style="width: 2%">No</th>
									<th style="width: 10%">NIK</th>
									<th style="width: 10%">Nama</th>
									<th style="width: 10%">No Tlp</th>
									<th style="width: 15%">Penempatan</th>
									<th style="width: 10%">Durasi</th>
									<th style="width: 10%">Putus Kontrak</th>
									<th style="width: 10%">Jeda</th>
									<th style="width: 10%">Jenis Test Kesehatan</th>
									<th style="width: 5%">TPA</th>
									<th style="width: 5%">Kesehatan</th>
									<th style="width: 3%">Action</th>
								</tr>
							</thead>
							<tbody id="tableBodyPeserta">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> 
	</div>


	<div class="modal fade" id="modalTpa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #bb8fce;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: black">PENILAIAN</h1>
					</div>
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
						<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
						<div align="center" style="padding-top: 3%">
							<table class="table table-hover table-striped table-bordered" id="tablePesertaDetail" style="width: 70%">
								<thead style="background-color: rgb(126,86,134); color: white;">
									<tr>
										<th width="3%">Nama</th>
										<th width="4%">TPA</th>
										<th width="4%">Kesehatan</th>
									</tr>
								</thead>
								<tbody id="tableBodyPesertaDetail">
								</tbody>
							</table>
						</div>
						<div align="center" style="padding-top: 0px">
							<button class="btn btn-success" onclick="saveNilai()">Konfirmasi</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		$('#createStartDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		DetailReq('{{$req_id}}');
	});	


	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
	

	function DetailReq(req_id){
		var data = {
			req_id:req_id
		};
		$.get('<?php echo e(url("calon/rekontrak")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#tablePeserta').DataTable().clear();
				$('#tablePeserta').DataTable().destroy();
				var tableData = '';
				$('#tableBodyPeserta').html("");
				$('#tableBodyPeserta').empty();

				var count = 1;

				$.each(result.resumes, function(key, value) {

					var jumlah = parseInt(value.tpa)+parseInt(value.kesehatan);
					
					var difftime = DateDiff(
						new Date(value.end_date),
						new Date());
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.nik +'</td>';
					tableData += '<td>'+ value.nama +'</td>';
					tableData += '<td>'+ value.no_whatsapp +'</td>';
					tableData += '<td>'+ value.penempatan +'</td>';
					tableData += '<td>'+ value.durasi +'</td>';
					tableData += '<td>'+ value.end_date +'</td>';
					tableData += '<td>'+ difftime[0]+' Tahun '+difftime[1]+' Bulan'+'</td>';
					if (difftime[0]< 1) {
						tableData += '<td>Thorax</td>';
					}
					else{
						tableData += '<td>Full</td>';
					}
					if (value.tpa != null) {
						tableData += '<td>'+ value.tpa +'</td>';
					}else{
						tableData += '<td>-</td>';	
					}
					if (value.kesehatan != null) {
						tableData += '<td>'+ value.kesehatan +'</td>';
					}else{
						tableData += '<td>-</td>';	
					}
					if ((value.tpa != null)&&(value.kesehatan != null)) {
						tableData += '<td style="background-color: green">Done</td>';
					}
					else{
						tableData += '<td>'+ '<button class="btn btn-danger btn-xs" onclick="Modal(\''+value.nik+'\',\''+value.nama+'\',\''+value.id+'\')">Input Nilai</button>' +'</td>';	
					}
					
					tableData += '</tr>';
					count += 1;
				});
				$('#tableBodyPeserta').append(tableData);
				var tablePeserta = $('#tablePeserta').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[10, 25, 50, -1], [10, 25, 50, "All"]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': false,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					// 'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				$('#tablePeserta tfoot tr').appendTo('#tablePeserta thead');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DateDiff(b, e)
	{
		let
		endYear = e.getFullYear(),
		endMonth = e.getMonth(),
		years = endYear - b.getFullYear(),
		months = endMonth - b.getMonth(),
		days = e.getDate() - b.getDate();
		if (months < 0)
		{
			years--;
			months += 12;
		}
		if (days < 0)
		{
			months--;
			days += new Date(endYear, endMonth, 0).getDate();
		}
		return [years, months, days];
	}


	function Modal(nik,nama,id){
		jQuery(document).ready(function() {
			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});
		});	
		$('#modalTpa').modal('show');
		$('#tableBodyPesertaDetail').html('');
		var detail  = '';
		detail += '<tr>';
		detail += '<td>'+nama+'</td>';
		detail += '<input type="hidden" value="'+id+'" id="id_nilai">';
		detail += '<td><input type="number" class="form-control numpad" style="width:100%" id="nilai" placeholder="Input Nilai"></td>';
		detail += '<td><input type="text" class="form-control" style="width:100%" id="kesehatan" placeholder="Input Nilai"></td>';
		detail += '</tr>';
		$('#tableBodyPesertaDetail').append(detail);
	}

	function saveNilai() {
		var nilai = $('#nilai').val();
		var kesehatan = $('#kesehatan').val();

		// $('#id_nilai').val(); //input
		// $('#id_nilai').html(''); //ngisi data ke  td, div, span, dkk
		// $('#id_nilai').text(); //ngamibl data ndek td, div, span, dkk

		var data = {
			id:$('#id_nilai').val(),
			nilai:nilai,
			kesehatan:kesehatan
		}
		$.post('<?php echo e(url("save/nilai")); ?>', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#modalTpa').modal('hide');
				DetailReq('{{$req_id}}');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
</script>
@endsection