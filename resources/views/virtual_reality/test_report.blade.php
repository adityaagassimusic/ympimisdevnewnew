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
		<!-- <button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#modalUploadSchedule').modal('show')"><i class="fa fa-upload"></i> Upload Schedule</button> -->
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>							
	<div class="row">
		<!-- <div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="mcu_periode" id="mcu_periode" data-placeholder="Pilih Periode MCU" style="width: 100%;">
									<option></option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">MCU Group Code</span>
							<div class="form-group">
								<select class="form-control select2" name="mcu_group" id="mcu_group" data-placeholder="Pilih Kode MCU" style="width: 100%;">
									<option value=""></option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_cleaning') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableOculus" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">NIK</th>
										<th width="4%">Nama</th>
										<th width="2%">Dept</th>
										<th width="4%">Sect</th>
										<th width="4%">Group</th>
										<th width="4%">Sub Group</th>
										<th width="3%">Test</th>
										<th width="4%">Sub Test</th>
										<th width="2%">Score</th>
										<th width="4%">At</th>
									</tr>
								</thead>
								<tbody id="bodyTableOculus">
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

<div class="modal fade" id="modalUploadSchedule">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Schedule</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-8">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<input type="file" name="scheduleFile" id="scheduleFile">
						</div>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="form-group row" align="right">
						<div class="col-sm-12" align="left">
							<a class="btn btn-info pull-right" href="{{url('download/ga_control/mcu/physical/schedule')}}">Example</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="uploadSchedule()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
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

		fillList();

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
	function fillList(){

		// var data = {
		// 	mcu_periode:$('#mcu_periode').val(),
		// 	mcu_group:$('#mcu_group').val(),
		// }
		$.get('{{ url("fetch/oculus/test/report") }}', function(result, status, xhr){
			if(result.status){
				$('#tableOculus').DataTable().clear();
				$('#tableOculus').DataTable().destroy();
				$('#bodyTableOculus').html("");
				var tableData = "";
				var index = 1;
				$.each(result.tests, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name+'</td>';
					tableData += '<td>'+ (value.department_shortname || '') +'</td>';
					tableData += '<td>'+ (value.section || '') +'</td>';
					tableData += '<td>'+ (value.group || '') +'</td>';
					tableData += '<td>'+ (value.sub_group || '') +'</td>';
					tableData += '<td>'+ (value.oculus_answer || '') +'</td>';
					tableData += '<td>'+ (value.oculus_sub_answer || '') +'</td>';
					tableData += '<td>'+ (value.oculus_result || '') +'</td>';
					tableData += '<td>'+ (value.created || '') +'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableOculus').append(tableData);

				var table = $('#tableOculus').DataTable({
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



</script>
@endsection