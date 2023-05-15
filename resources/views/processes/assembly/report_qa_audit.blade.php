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
		
		overflow:hidden;
	}
	tbody>tr>td{
		
	}
	tfoot>tr>th{
		
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
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
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Date To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="date_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/assembly/report_qa_audit/') }}/{{$origin_group_code}}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableReport" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">ID</th>
										<th width="1%">Serial Number</th>
										<th width="1%">Model</th>
										<th width="2%">Start</th>
										<th width="2%">Finish</th>
										<th width="3%">Auditee 1</th>
										<th width="3%">Auditee 2</th>
										<th width="3%">Auditor</th>
										<th width="1%">NG Name</th>
										<th width="1%">Kunci</th>
										<th width="1%">Value</th>
										<th width="1%">Lokasi</th>
										<th width="1%">Ganti Kunci</th>
									</tr>
								</thead>
								<tbody id="bodyTableReport">
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
		$('#loading').show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			origin_group_code:'{{$origin_group_code}}',
		}
		$.get('{{ url("fetch/assembly/report_qa_audit") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableReport').DataTable().clear();
				$('#tableReport').DataTable().destroy();
				$('#bodyTableReport').html("");
				var tableData = "";
				var index = 1;
				$.each(result.report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:center;">'+ value.id +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.serial_number +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.model +'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.sedang_start_date+'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.sedang_finish_date +'</td>';
					if (value.operator_audited != null) {
						var auditee = '';
						if (value.operator_audited.match(/_/gi)) {
							var auditees = value.operator_audited.split('_');
							for(var j = 0; j < auditees.length;j++){
								var name_auditee = '';
								for(var i = 0; i < result.emp.length;i++){
									if (result.emp[i].employee_id == auditees[j]) {
										name_auditee = result.emp[i].name;
									}
								}
								auditee = auditees[j]+' - '+name_auditee;
								tableData += '<td style="text-align:left;padding-left:4px;">'+ auditee +'</td>';
							}
						}else{
							var name_auditee = '';
							for(var i = 0; i < result.emp.length;i++){
								if (result.emp[i].employee_id == value.operator_audited) {
									name_auditee = result.emp[i].name;
								}
							}
							auditee = value.operator_audited+' - '+name_auditee;
							tableData += '<td style="text-align:left;padding-left:4px;">'+ auditee +'</td>';
							tableData += '<td style="text-align:left;padding-left:4px;"></td>';
						}
					}else{
						tableData += '<td style="text-align:left;padding-left:4px;">-</td>';
						tableData += '<td style="text-align:left;padding-left:4px;">-</td>';
					}
					var name_auditor = '';
					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.operator_id) {
							name_auditor = result.emp[i].name;
						}
					}
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.operator_id +' - '+name_auditor+'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.ng_name || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.ongko || '') +'</td>';
					if (value.value_bawah == null) {
						tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.value_atas || '') +'</td>';
					}else{
						tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.value_atas || '') +'-'+ (value.value_bawah || '') +'</td>';
					}
					tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.value_lokasi || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.decision || '') +'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableReport').append(tableData);

				var table = $('#tableReport').DataTable({
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
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}



</script>
@endsection