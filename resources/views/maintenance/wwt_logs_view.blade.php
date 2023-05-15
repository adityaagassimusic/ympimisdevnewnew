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
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #212121;  
		color:white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(54, 59, 56);
		background-color: #212121;
		color: white;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}
	table.table-condensed > thead > tr > th{   
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}

	table.table-striped > thead > tr > th{
		border:1px solid black !important;
		text-align: center;
		background-color: rgba(126,86,134,.7) !important;  
	}

	table.table-striped > tbody > tr > td{
		border-collapse: collapse;
		color: black;
		padding: 3px;
		vertical-align: middle;
		text-align: center;
		background-color: white;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	#container1 {
		height: 400px;
	}

	.highcharts-figure,
	.highcharts-data-table table {
		min-width: 310px;
		max-width: 800px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #ebebeb;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}

	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}

	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
		padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
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
										<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
											<span style="font-weight: bold; font-size: 1.6vw;">DISPOSAL LIMBAH B3</span>
										</div>
										<table id="TableLogsWWT" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
											<thead style="background-color: rgb(126,86,134); color: #FFD700;">
												<tr>
													<th width="1%">No</th>
													<th width="2%">Slip Disposal</th>
													<th width="2%">Tanggal Disposal</th>
													<th width="2%">Limbah</th>
													<th width="2%">Teknis</th>
													<th width="2%">Manifest</th>
													<th width="2%">Kirim</th>
												</tr>
											</thead>
											<tbody id="bodyTableLogsWWT">
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

		<div class="modal fade" id="ModalPdf" role="dialog">
			<div class="modal-lg modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<div class="col-xs-12" style="background-color:  #FFC300; text-align: center; margin-bottom: 5px;">
									<span style="font-weight: bold; font-size: 1.6vw;" id="judul"></span>
								</div>
								<div class="col-xs-12" style="text-align: center; margin-bottom: 5px;">
									<span style="font-weight: bold; font-size: 1.6vw;" id="slip_disposal"></span>
								</div>
							</ul>
						</div>
						<div id="attach_pdf">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="ModalUpload" role="dialog">
			<div class="modal-xs modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<form id ="importForm" name="importForm" method="post" action="{{ url('upload/dokumen/teknis') }}" enctype="multipart/form-data">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="hidden" id="slip_id" name="slip_id">
							<input type="hidden" id="category_upload" name="category_upload">
							<div class="nav-tabs-custom tab-danger" align="center">
								<ul class="nav nav-tabs">
									<div class="col-xs-12" style="background-color: #e74c3c; color: white; text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;" id="judul_detail"></span>
									</div>
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;" id="slip_teknis" name="slip_teknis"></span>
									</div>
								</ul>
							</div>
							<div class="form-group row" align="right">
								<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
								<div class="col-sm-6">
									<input type="file" name="dokumen_teknis" id="dokumen_teknis" accept="application/pdf">
								</div>
							</div>
							<div class="modal-footer">
								<center>
									<button type="submit" id="button_upload" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #e74c3c;">Simpan</button>
								</center>
							</div>
						</form>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
		var arr = [];
		var arr2 = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('#datefrom').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});

			$('#dateto').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});

			$('#month_next').datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true
		});

			$('.select2').select2({
				allowClear:true
			});

			Search();

		$('#dokumen_teknis').on('change',function(){
			var fileName = $('input[type=file]').val().split('\\').pop();
			$(this).next('.custom-file-label').html(fileName);
		})
		});

	function Search(){
		$.get('{{ url("fetch/logs/wwt") }}', function(result, status, xhr){
			if(result.status){
				$('#TableLogsWWT').DataTable().clear();
				$('#TableLogsWWT').DataTable().destroy();
				$('#bodyTableLogsWWT').html("");
				var tableData = "";
				var index = 1;
				$.each(result.resumes, function(key, value) {

					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.slip_disposal +'</td>';
					tableData += '<td>'+ value.date_disposal +'</td>';

					tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-success btn-md" id="Dokumen Limbah" onclick="ModalDokumen(this.id, \''+value.slip_disposal+'\')"><i class="fa fa-file-pdf-o"></i> Lihat Dokumen</a></td>';

					if (value.dokumen_teknis == null) {
						tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-danger btn-md" id="Dokumen Teknis" onclick="ModalTeknis(this.id, \''+value.slip_disposal+'\', \''+value.dokumen_teknis+'\', \''+value.dokumen_manifest+'\')"><i class="fa fa-file-pdf-o"></i> Upload Dokumen</a></td>';
					}else{
						tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-success btn-md" id="Dokumen Teknis" onclick="ModalTeknis(this.id, \''+value.slip_disposal+'\', \''+value.dokumen_teknis+'\', \''+value.dokumen_manifest+'\')"><i class="fa fa-file-pdf-o"></i> Lihat Dokumen</a></td>';
					}

					if (value.dokumen_manifest == null) {
						tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-danger btn-md" id="Dokumen Manifest" onclick="ModalTeknis(this.id, \''+value.slip_disposal+'\', \''+value.dokumen_teknis+'\', \''+value.dokumen_manifest+'\')"><i class="fa fa-file-pdf-o"></i> Upload Dokumen</a></td>';
					}else{
						tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-success btn-md" id="Dokumen Manifest" onclick="ModalTeknis(this.id, \''+value.slip_disposal+'\', \''+value.dokumen_teknis+'\', \''+value.dokumen_manifest+'\')"><i class="fa fa-file-pdf-o"></i> Lihat Dokumen</a></td>';
					}
					
					index++;
					if (value.dokumen_teknis != null && value.dokumen_manifest != null) {
						tableData += '<td style="font-weight: bold;"><a href="javascript:void(0)" class="btn btn-info btn-md" id="Dokumen Manifest" onclick="ModalDokumen(this.id)"><i class="fa fa-file-pdf-o"></i> Kirim</a></td>';
					}else{
						tableData += '<td>-</td>';
					}
				});
				$('#bodyTableLogsWWT').append(tableData);

				var table = $('#TableLogsWWT').DataTable({
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
							className: 'btn btn-default',
						}
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

	function ModalDokumen(id, slip_disposal){
		$('#ModalPdf').modal('show');
		$('#judul').html(id);
		$('#slip_disposal').html(slip_disposal);
		$('#attach_pdf').html('');
		var pdf = "{{ url('data_file/wwt/disposal/') }}"+'/'+slip_disposal+'.pdf';
      	$('#attach_pdf').append('<iframe src="'+pdf+'" width="100%" height="600px"></iframe>');
	}

	function ModalTeknis(id, slip_disposal, dokumen_teknis, dokumen_manifest){
		$('#judul_detail').html(id);
		if (id == 'Dokumen Teknis') {
			if (id == 'Dokumen Teknis' && dokumen_teknis == 'null') {
				$('#ModalUpload').modal('show');
				$('#slip_teknis').html(slip_disposal);
				$('#slip_id').val(slip_disposal);
				$('#category_upload').val(id);
			}else{
				$('#ModalPdf').modal('show');
				$('#judul').html(id);
				$('#slip_disposal').html(slip_disposal);
				$('#attach_pdf').html('');
				var pdf = "{{ url('data_file/wwt/teknis/') }}"+'/'+slip_disposal+'.pdf';
				$('#attach_pdf').append('<iframe src="'+pdf+'" width="100%" height="600px"></iframe>');	
			}
		}else if(id == 'Dokumen Manifest'){
			if (id == 'Dokumen Manifest' && dokumen_manifest == 'null') {
				$('#ModalUpload').modal('show');
				$('#slip_teknis').html(slip_disposal);
				$('#slip_id').val(slip_disposal);
				$('#category_upload').val(id);
			}else{
				$('#ModalPdf').modal('show');
				$('#judul').html(id);
				$('#slip_disposal').html(slip_disposal);
				$('#attach_pdf').html('');
				var pdf = "{{ url('data_file/wwt/manifest/') }}"+'/'+slip_disposal+'.pdf';
				$('#attach_pdf').append('<iframe src="'+pdf+'" width="100%" height="600px"></iframe>');	
			}
		}
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