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
										<th width="5%">PIC</th>
										<th width="3%">At</th>
										<th width="3%">Action</th>
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

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #fcba03;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<div id="spec_product_table">
							<table id="specProductTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
							</table>
						</div>
						<!-- <center>
							<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
						</center> -->
						<!-- <table class="table table-hover table-bordered table-striped" id="tableDetail"> -->
							<!-- <thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">#</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody> -->
						<!-- </table> -->

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
		$.get('{{ url("fetch/assembly/report_spec_product") }}',data, function(result, status, xhr){
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
					var name = '';
					for(var i = 0; i < result.emp.length;i++){
						if (value.employee_id == result.emp[i].employee_id) {
							name = result.emp[i].name;
						}
					}
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.employee_id+' - '+ name+'</td>';
					tableData += '<td style="text-align:left;padding-left:5px;">'+ value.created +'</td>';
					tableData += '<td style="text-align:center;"><button class="btn btn-success btn-xs" onclick="detailSpec(\''+value.serial_number+'\',\''+value.model+'\',\''+value.employee_id+'\',\''+value.created+'\')">Detail</button></td>';
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

	function detailSpec(serial_number,model,employee_id,created) {
		$('#loading').show();
		var data = {
			serial_number:serial_number,
			model:model,
			employee_id:employee_id,
			created:created,
		}

		$.get('{{ url("fetch/assembly/report_spec_product/detail") }}',data, function(result, status, xhr){
			if(result.status){
				$('#specProductTable').html('');
					var tableSpec = '';
					// tableSpec += '<thead>';
					// tableSpec += '<tr>';
					// tableSpec += '<th colspan="5" style="width: 3%; background-color: dodgerblue; color: white; padding:0;font-size: 15px;" >Spec Product</th>';
					// tableSpec += '</tr>';
					// tableSpec += '</thead>';
					tableSpec += '<tbody>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;padding-left:7px;font-weight:bold;" >BELL AND BOW</td>';
					var index = 1;
					for(var i = 0; i < result.spec_qa.length;i++){
						if (result.spec_qa[i].location == 'BELL AND BOW') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec_qa[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec_qa[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec_qa[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec_qa[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec_qa[i].point+'</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+model+'_BELL AND BOW_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec_qa[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec_qa[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
								if (result.spec_qa_now.length > 0) {
									if (result.spec_qa_now[i].results == 'OK') {
										tableSpec += '<td style="background-color:#dfffd4;text-align:center;border:1px solid black;">';
										tableSpec += '&#9711;';
										tableSpec += '</td>';
									}else{
										tableSpec += '<td style="background-color:#ffbdbd;text-align:center;border:1px solid black;">';
										tableSpec += '&#9747;';
										tableSpec += '</td>';
									}
								}else{
									tableSpec += '<td style="text-align:center">';
									tableSpec += '</td>';
								}
								// tableSpec += '<div class="col-xs-6">';
								// tableSpec += '<label class="containers">OK';
								// if (result.spec_qa_now.length > 0) {
								// 	if (result.spec_qa_now[i].results == 'OK') {
								// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
								// 	}else{
								// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
								// 	}
								// }else{
								// 	tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
								// }
								// tableSpec += '<span class="checkmark"></span>';
								// tableSpec += '</label>';
								// tableSpec += '</div>';
								// tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
								// tableSpec += '<label class="containers">NG';
								// if (result.spec_qa_now.length > 0) {
								// 	if (result.spec_qa_now[i].results == 'NG') {
								// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
								// 	}else{
								// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
								// 	}
								// }else{
								// 	tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
								// }
								// tableSpec += '<span class="checkmark"></span>';
								// tableSpec += '</label>';
								// tableSpec += '</div>';
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					tableSpec += '</tr>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;padding-left:7px;font-weight:bold;" >BODY</td>';
					var index = 1;
					for(var i = 0; i < result.spec_qa.length;i++){
						if (result.spec_qa[i].location == 'BODY') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec_qa[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec_qa[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec_qa[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec_qa[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec_qa[i].point;
							if (result.spec_qa[i].point == 'No. Seri') {
								tableSpec += ' = <span style="font-weight:bold;color:red;font-size:18px">'+serial_number+'</span>';
							}
							tableSpec += '</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+model+'_BODY_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec_qa[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec_qa[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							// tableSpec += '<td style="">';
							// 	tableSpec += '<div class="col-xs-6">';
							// 	tableSpec += '<label class="containers">OK';
							// 	if (result.spec_qa_now.length > 0) {
							// 		if (result.spec_qa_now[i].results == 'OK') {
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
							// 		}else{
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							// 		}
							// 	}else{
							// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							// 	}
							// 	tableSpec += '<span class="checkmark"></span>';
							// 	tableSpec += '</label>';
							// 	tableSpec += '</div>';
							// 	tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
							// 	tableSpec += '<label class="containers">NG';
							// 	if (result.spec_qa_now.length > 0) {
							// 		if (result.spec_qa_now[i].results == 'NG') {
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
							// 		}else{
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							// 		}
							// 	}else{
							// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							// 	}
							// 	tableSpec += '<span class="checkmark"></span>';
							// 	tableSpec += '</label>';
							// 	tableSpec += '</div>';
							// tableSpec += '</td>';
								if (result.spec_qa_now.length > 0) {
									if (result.spec_qa_now[i].results == 'OK') {
										tableSpec += '<td style="background-color:#dfffd4;text-align:center;border:1px solid black;">';
										tableSpec += '&#9711;';
										tableSpec += '</td>';
									}else{
										tableSpec += '<td style="background-color:#ffbdbd;text-align:center;border:1px solid black;">';
										tableSpec += '&#9747;';
										tableSpec += '</td>';
									}
								}else{
									tableSpec += '<td style="text-align:center">';
									tableSpec += '</td>';
								}
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					tableSpec += '</tr>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;padding-left:7px;font-weight:bold;">NECK</td>';
					var index = 1;
					for(var i = 0; i < result.spec_qa.length;i++){
						if (result.spec_qa[i].location == 'NECK') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec_qa[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec_qa[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec_qa[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec_qa[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec_qa[i].point+'</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+model+'_NECK_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec_qa[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec_qa[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							// tableSpec += '<td style="">';
							// 	tableSpec += '<div class="col-xs-6">';
							// 	tableSpec += '<label class="containers">OK';
							// 	if (result.spec_qa_now.length > 0) {
							// 		if (result.spec_qa_now[i].results == 'OK') {
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
							// 		}else{
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							// 		}
							// 	}else{
							// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							// 	}
							// 	tableSpec += '<span class="checkmark"></span>';
							// 	tableSpec += '</label>';
							// 	tableSpec += '</div>';
							// 	tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
							// 	tableSpec += '<label class="containers">NG';
							// 	if (result.spec_qa_now.length > 0) {
							// 		if (result.spec_qa_now[i].results == 'NG') {
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
							// 		}else{
							// 			tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							// 		}
							// 	}else{
							// 		tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							// 	}
							// 	tableSpec += '<span class="checkmark"></span>';
							// 	tableSpec += '</label>';
							// 	tableSpec += '</div>';
							// tableSpec += '</td>';
								if (result.spec_qa_now.length > 0) {
									if (result.spec_qa_now[i].results == 'OK') {
										tableSpec += '<td style="background-color:#dfffd4;text-align:center;border:1px solid black;">';
										tableSpec += '&#9711;';
										tableSpec += '</td>';
									}else{
										tableSpec += '<td style="background-color:#ffbdbd;text-align:center;border:1px solid black;">';
										tableSpec += '&#9747;';
										tableSpec += '</td>';
									}
								}else{
									tableSpec += '<td style="text-align:center">';
									tableSpec += '</td>';
								}
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					var point = [];
					var detail = [];
					for(var i = 0; i < result.spec_qa.length;i++){
						if (result.spec_qa[i].location == '') {
							point.push(result.spec_qa[i].point);
							detail.push(result.spec_qa[i].detail);
						}
					}
					var point_unik = point.filter(onlyUnique);
					var detail_unik = detail.filter(onlyUnique);
					tableSpec += '<td colspan="2" style="width: 3%; background-color: white; padding:0;font-size: 15px;">';
					tableSpec += '<table style="text-align:left;height:200px;width:100%">';
					tableSpec += '<tr>';
					tableSpec += '<td style="text-align:center;border:1px solid black;"></td>';
					for(var j = 0; j < detail_unik.length;j++){
						tableSpec += '<td style="text-align:left;padding-left:7px;border:1px solid black;font-weight:bold;">'+detail_unik[j]+'</td>';
					}
					tableSpec += '</tr>';
					for(var j = 0; j < point_unik.length;j++){
						tableSpec += '<tr>';
						tableSpec += '<td style="text-align:left;padding-left:7px;border:1px solid black;font-weight:bold;">'+point_unik[j]+'</td>';
						for(var i = 0; i < detail_unik.length;i++){
							for(var k = 0; k < result.spec_qa.length;k++){
								if (result.spec_qa[k].detail == detail_unik[i] && result.spec_qa[k].point == point_unik[j]) {
									tableSpec += '<input type="hidden" id="location_'+k+'" value="'+result.spec_qa[k].location+'">';
									tableSpec += '<input type="hidden" id="point_'+k+'" value="'+result.spec_qa[k].point+'">';
									tableSpec += '<input type="hidden" id="detail_'+k+'" value="'+result.spec_qa[k].detail+'">';
									tableSpec += '<input type="hidden" id="how_to_check_'+k+'" value="'+result.spec_qa[k].how_to_check+'">';
									// tableSpec += '<td style="text-align:right;border:1px solid black;">';
									// tableSpec += '<div class="col-xs-6">';
									// tableSpec += '<label class="containers">IYA';
									// if (result.spec_qa_now.length > 0) {
									// 	if (result.spec_qa_now[k].results == 'IYA') {
									// 		tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" checked value="IYA">';
									// 	}else{
									// 		tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="IYA">';
									// 	}
									// }else{
									// 	tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="IYA">';
									// }
									//   tableSpec += '<span class="checkmark"></span>';
									// tableSpec += '</label>';
									// tableSpec += '</div>';
									// tableSpec += '<div class="col-xs-6" style="border-left:2px solid black;">';
									// tableSpec += '<label class="containers" style="margin-left:10px;">TIDAK';
									// if (result.spec_qa_now.length > 0) {
									// 	if (result.spec_qa_now[k].results == 'TIDAK') {
									// 		tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" checked value="TIDAK">';
									// 	}else{
									// 		tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="TIDAK">';
									// 	}
									// }else{
									// 	tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="TIDAK">';
									// }
									//   tableSpec += '<span class="checkmark"></span>';
									// tableSpec += '</label>';
									// tableSpec += '</div>';
									// tableSpec += '</td>';
									if (result.spec_qa_now.length > 0) {
										if (result.spec_qa_now[k].results == 'IYA') {
											tableSpec += '<td style="background-color:#dfffd4;text-align:center;border:1px solid black;">';
											tableSpec += 'IYA';
											tableSpec += '</td>';
										}else{
											tableSpec += '<td style="background-color:#ffbdbd;text-align:center;border:1px solid black;">';
											tableSpec += 'TIDAK';
											tableSpec += '</td>';
										}
									}else{
										tableSpec += '<td style="text-align:center">';
										tableSpec += '</td>';
									}
								}
							}
						}
						tableSpec += '</tr>';
					}
					tableSpec += '</table>';
					tableSpec += '</td>';
					tableSpec += '</tr>';
					tableSpec += '</tbody>';

					$('#specProductTable').append(tableSpec);

					$('#modalDetailTitle').html('Detail Spec Product '+serial_number+' - '+model);

					$('#modalDetail').modal('show');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});

	}



</script>
@endsection