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
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait <i class="fa fa-spin fa-refresh"></i></span>
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
									<input type="text" class="form-control datepicker" id="date_from" placeholder="Select Date From" autocomplete="off">
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
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Serial Number</span>
							<div class="form-group">
								<input type="text" class="form-control" id="serial_number" placeholder="Input Serial Number" autocomplete="off">
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="form-group pull-right">
								<a href="{{ url('index/process_stamp_cl') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/assembly/report_spec_product/') }}" class="btn btn-danger">Clear</a>
								<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
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
							<table id="tableMaterial" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: lightgrey; color: #000;">
									<tr>
										<th width="1%">#</th>
										<th width="3%">SN</th>
										<th width="3%">Model</th>
										<th width="1%">Made In</th>
										<th width="1%">Body</th>
										<th width="1%">Bell</th>
										<th width="1%">Side Cover</th>
										<th width="1%">F-4</th>
										<th width="1%">J-3</th>
										<th width="5%">PIC</th>
										<th width="3%">At</th>
									</tr>
								</thead>
								<tbody id="bodyTableMaterial">
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
	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}
	function fillList(){
		$('#loading').show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			serial_number:$('#serial_number').val(),
		}
		$.get('{{ url("fetch/assembly/report_spec_product_process") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMaterial').DataTable().clear();
				$('#tableMaterial').DataTable().destroy();
				$('#bodyTableMaterial').html("");

				var tableData = "";
				var index = 1;
				$.each(result.report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.serial_number +'</td>';
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.model +'</td>';
					var made_in = '';
					var body = '';
					var bell = '';
					var side_cover = '';
					var f_4 = '';
					var j_3 = '';
					for(var i = 0; i < result.report_all.length;i++){
						if (result.report_all[i].serial_number == value.serial_number && result.report_all[i].model == value.model) {
							if (result.report_all[i].category == 'Made In') {
								made_in = result.report_all[i].results;
							}
							if (result.report_all[i].category == 'Body') {
								body = result.report_all[i].results;
							}
							if (result.report_all[i].category == 'Bell') {
								bell = result.report_all[i].results;
							}
							if (result.report_all[i].category == 'Side Cover') {
								side_cover = result.report_all[i].results;
							}
							if (result.report_all[i].category == 'F-4') {
								f_4 = result.report_all[i].results;
							}
							if (result.report_all[i].category == 'J-3') {
								j_3 = result.report_all[i].results;
							}
						}
					}
					tableData += '<td style="text-align:center;">'+made_in+'</td>';
					tableData += '<td style="text-align:center;">'+body+'</td>';
					tableData += '<td style="text-align:center;">'+bell+'</td>';
					tableData += '<td style="text-align:center;">'+side_cover+'</td>';
					tableData += '<td style="text-align:center;">'+f_4+'</td>';
					tableData += '<td style="text-align:center;">'+j_3+'</td>';
					var name = '';
					for(var i = 0; i < result.emp.length;i++){
						if (value.employee_id == result.emp[i].employee_id) {
							name = result.emp[i].name;
						}
					}
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.employee_id+' - '+ name+'</td>';
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.created +'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableMaterial').append(tableData);

				var table = $('#tableMaterial').DataTable({
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
			}
		});
	}


</script>
@endsection