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
	<h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h1>
	<a href="{{ url('index/ky_hh') }}" class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #ffb600; color: white;"><i class="fa fa-desktop" aria-hidden="true"></i> Monitoring</a>
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<table id="TableLogSoal" class="table table-bordered table-hover" style="margin-bottom: 0;">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="text-align: center; width: 10%">No</th>
								<th style="text-align: left; width: 20%">Kode Soal</th>
								<th style="text-align: left; width: 30%">Judul</th>
								<th style="text-align: left; width: 20%">Periode</th>
								<th style="text-align: center; width: 20%">Aksi</th>
							</tr>
						</thead>
						<tbody id="bodyTableLogSoal" style="background-color: #fcf8e3;">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div id="show_soal" class="col-xs-6">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
						<span style="font-weight: bold; font-size: 1.6vw;"><span id="id_kode_soal"></span></span>
					</div>
					<div id="attach_pdf" align="center" style="padding-bottom: 10px"></div>
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
<!-- <script src="{{ url('js/highcharts.js')}}"></script>
<script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		ResultData();
		$('#show_soal').hide();
	});

	function ViewSoal(kode_soal, nama_gambar){
		$('#show_soal').show();

		$('#attach_pdf').html('');
		var pdf = "{{ url('data_file/std/ky') }}"+'/'+nama_gambar;
		$('#attach_pdf').append('<img src="'+pdf+'" style="width: 40vw; height: auto">');

		var data = {
			kode_soal : kode_soal
		}

		$.get('{{ url("fetch/detail/soal/ky") }}', data,function(result, status, xhr){
			

			if(result.status){
				$('#TableDetailSoal').DataTable().clear();
				$('#TableDetailSoal').DataTable().destroy();
				$('#bodyTableDetailSoal').html("");
				var tableData = "";
				$.each(result.data, function(key, value) {
					var judul = kode_soal+' - '+value.remark;
					$('#id_kode_soal').html(judul);

					tableData += '<tr>';
					tableData += '<td style="text-align: center;">'+ value.soal +'</td>';
					tableData += '</tr>';
				});
				$('#bodyTableDetailSoal').append(tableData);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ResultData(){
		$.get('{{ url("fetch/log/soal/ky") }}', function(result, status, xhr){
			if(result.status){
				$('#TableLogSoal').DataTable().clear();
				$('#TableLogSoal').DataTable().destroy();
				$('#bodyTableLogSoal').html("");
				var tableData = "";
				var index = 1;

				var count_tampil = 0;
				$.each(result.resumes, function(key, value) {
					if (value.view == 'Tampil') {
						count_tampil += 1;
					}
				})

				$.each(result.resumes, function(key, value) {

					var date = new Date(value.periode);
					var month = date.toLocaleString('en-us', { month: 'long' });
					var year = date.getFullYear();

					tableData += '<tr>';
					tableData += '<td style="text-align: center;">'+ index++ +'</td>';
					tableData += '<td style="text-align: left;">'+ value.kode_soal +'</td>';
					tableData += '<td style="text-align: left;">'+ value.remark +'</td>';
					tableData += '<td style="text-align: left;">'+ month+' '+year +'</td>';
					if (value.view == 'Tampil') {
						tableData += ' <td style="font-weight: bold; text-align: center;"><a href="javascript:void(0)" class="btn btn-success" onclick="ViewSoal(\''+value.kode_soal+'\', \''+value.nama_gambar+'\')"><i class="fa fa-file-pdf-o"></i></a> <button type="button" class="btn btn-warning" onclick="TutupSoal(\''+value.kode_soal+'\')"><i class="fa fa-eye"></i></button> <button type="button" class="btn btn-danger" onclick="HapusSoal(\''+value.kode_soal+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>';
					}else{
						tableData += '<td style="font-weight: bold; text-align: center;">';
						tableData +='<a href="javascript:void(0)" class="btn btn-success" onclick="ViewSoal(\''+value.kode_soal+'\', \''+value.nama_gambar+'\')"><i class="fa fa-file-pdf-o"></i></a>';
						if (count_tampil == 0) {
							tableData += ' <button type="button" class="btn btn-info" onclick="BukaSoal(\''+value.kode_soal+'\')"><i class="fa fa-eye"></i></button>';
							tableData += ' <button type="button" class="btn btn-danger" onclick="HapusSoal(\''+value.kode_soal+'\')"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
						}
						tableData += '</td>';
						
					}
					tableData += '</tr>';
				});
				$('#bodyTableLogSoal').append(tableData);

				var table = $('#TableLogSoal').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default'
						},
						{
							extend: 'copy',
							className: 'btn btn-success',
							text: '<i class="fa fa-copy"></i> Copy',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
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

	function HapusSoal(kode_soal){
		console.log(kode_soal);
		var data = {
			kode_soal:kode_soal
		}
		$.post('{{ url("delete/soal/kyt") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','Soal Berhasil Ditutup');
				ResultData();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function TutupSoal(kode_soal){
		var data = {
			kode_soal:kode_soal
		}
		$.post('{{ url("update/tutup/soal") }}', data, function(result, status, xhr) {
			
			if(result.status){
				openSuccessGritter('Success','Soal Berhasil Ditutup');
				ResultData();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function BukaSoal(kode_soal){
		var data = {
			kode_soal:kode_soal
		}
		$.post('{{ url("update/buka/soal") }}', data, function(result, status, xhr) {
			openSuccessGritter('Success','Soal Berhasil Dibuka');
			ResultData();
		})
	}

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