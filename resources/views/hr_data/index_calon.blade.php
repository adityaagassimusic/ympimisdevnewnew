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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
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
	<!-- <div class="form-group">
		<form method="GET" action="{{ url("download/data/calon/karyawan") }}">
			<div class="col-md-4 pull-left" style="padding-left: 0px; padding-bottom: 10px;">
				<button style="width: 50%" type="submit" class="btn btn-success form-control"><i class="fa fa-download"></i> Export Excel</button>
			</div>
		</form>
	</div> -->
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Data for Candidate Employees Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<form method="GET" action="{{ url("download/data/calon/karyawan") }}">
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Dari Tanggal Input</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" data-placeholder="Select Date">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Sampai Tanggal Input</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="dateto" name="dateto" data-placeholder="Select Date"  onchange="Search()">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-6">
							<div class="form-group pull-right">
								<!-- <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a> -->
								<!-- <button id="search" onClick="Search()" class="btn btn-primary">Search</button> -->
								<button type="submit" class="btn btn-success form-control"><i class="fa fa-download"></i> Export Excel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>			
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableHr" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="3%">Nama</th>
										<th width="1%">NIK</th>
										<th width="1%">NPWP</th>
										<th width="2%">TTL</th>
										<th width="2%">Agama</th>
										<th width="2%">Status</th>
										<th width="3%">Alamat</th>
										<th width="2%">No HP</th>
										<th width="2%">Tgl Input</th>
										<!-- <th width="3%">Update</th> -->
										<th width="3%">Aksi</th>
									</tr>
								</thead>
								<tbody id="bodyTableHr">
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

	<div class="modal fade" id="ModalUpdate" role="dialog">
		<div class="modal-xs modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<ul class="nav nav-tabs">
							<div class="col-xs-12" style="background-color: #78a1d0; color: white; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">Update NIK Karyawan</span>
							</div>
						</ul>
					</div>
					<div class="form-group row" align="center">
						<div class="col-md-12">
							<table class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 0px; text-align: center">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th style="text-align: center; width: 30%">Nama</th>
										<th style="text-align: center; width: 30%">No KTP</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="text-align: center; width: 30%" id="nama_karyawan"></td>
										<td style="text-align: center; width: 30%" id="ktp_karyawan"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="form-group row" align="center">
						<label class="col-xs-12 label-pad">NIK Baru<span class="text-red">*</span></label>
					</div>
					<div class="form-group row" align="center">
						<div class="col-xs-12">
							<input type="hidden" id="id_karyawan">
							<input style="width: 50%; text-align: center;" type="text" class="form-control" id="nik_baru" name="nik_baru" placeholder="NIK" required>
						</div>
					</div>
					<div class="modal-footer">
						<center>
							<button id="button_simpan" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 60%;" type="button"  onclick="SimpanUpdateNIK()">Update</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#datefrom').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});

		Search();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function Download(){
		var data = {
			dateto:$('#dateto').val(),
			datefrom:$('#datefrom').val()
		}
		$.get('{{ url("download/data/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});

	}

	function CreatePdf(nik){
		var data = {
			nik:nik
		}
		$.get('{{ url("create/pdf/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				Search();
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function Search(){
		var data = {
			dateto:$('#dateto').val(),
			datefrom:$('#datefrom').val()
		}
		$.get('{{ url("fetch/data/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHr').DataTable().clear();
				$('#tableHr').DataTable().destroy();
				$('#bodyTableHr').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {
					var b = value.address;
					var a = b.split("/");
					var report = '{{ url("calon_karyawan")}}';

					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.nik +'</td>';
					tableData += '<td>'+ (value.npwp || '-') +'</td>';
					tableData += '<td>'+ value.birth_place+'/'+value.birth_date +'</td>';
					tableData += '<td>'+ value.religion +'</td>';
					tableData += '<td>'+ value.mariage_status +'</td>';
					// tableData += '<td>'+ a[0]+' RT 0'+a[1]+' RW 0'+a[2]+' KELURAHAN '+a[3]+' KECAMATAN '+a[4]+' KOTA/KAB '+a[5] +'</td>';
					tableData += '<td>'+ a[0]+' RT 0'+a[1]+' RW 0'+a[2] +'</td>';
					tableData += '<td>'+ value.handphone +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					// tableData += '<td><button id="Add"class="btn btn-success" style="font-weight: bold; color: white" onclick="UpdateNIK(\''+value.id+'\', \''+value.name+'\', \''+value.nik+'\')"><i class="fa fa-check" aria-hidden="true"></i></button></td>';
					// tableData += '<td>'+ value.created_at+'</td>';
					
					if (result.username == 'PI2101044') {
						tableData += '<td>';
						tableData += '<button id="Add"class="btn btn-success btn-xs" style="font-weight: bold; color: white" onclick="UpdateNIK(\''+value.id+'\', \''+value.name+'\', \''+value.nik+'\', \''+result.a_nik+'\')"><i class="fa fa-check" aria-hidden="true"></i> Update</button><a href="'+report+'/HR'+value.nik+'.pdf" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a> <a onclick="CreatePdf(\''+value.nik+'\')" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Create"><i class="fa fa-file-pdf-o"></i> Create</a>';
						tableData += '</td>';
					}else{
						tableData += '<td>';
						tableData += '<button id="Add"class="btn btn-success btn-xs" style="font-weight: bold; color: white" onclick="UpdateNIK(\''+value.id+'\', \''+value.name+'\', \''+value.nik+'\', \''+result.a_nik+'\')"><i class="fa fa-check" aria-hidden="true"></i> Update</button><br><a href="'+report+'/HR'+value.nik+'.pdf" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a>';
						tableData += '</td>';
					}
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableHr').append(tableData);

				var table = $('#tableHr').DataTable({
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

	function UpdateNIK(id, name, nik, a_nik){
		$('#ModalUpdate').modal('show');
		$('#id_karyawan').html(id);
		$('#nama_karyawan').html(name);
		$('#ktp_karyawan').html(nik);
		$('#nik_baru').val(a_nik);
	}

	function SimpanUpdateNIK(){
		var id = $('#id_karyawan').html();
		var nik = $('#nik_baru').val();
		var data = {
			id:id,
			nik:nik
		}
		$.post('{{ url("update/nik/karyawan/baru") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','Masuk List Disposal');
				$('#ModalUpdate').modal('hide');
				$('#nik_baru').val('');
				Search();
			}else{
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function clearConfirmation(){
		location.reload(true);		
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