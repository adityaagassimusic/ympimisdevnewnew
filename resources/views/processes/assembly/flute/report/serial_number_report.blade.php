@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">Serial Number Report Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<label>Date</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" placeholder="Date">
								</div>
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" name="dateto"  placeholder="Date To">
								</div>
							</div>
						</div> -->
					</div>
					<div class="col-md-12 col-md-offset-6">
						<div class="col-md-3">
							<!-- <div class="form-group">
								<label>Model</label>
								<select class="form-control select2" data-placeholder="Select Model" name="model" id="model" style="width: 100%;">
									<option value=""></option>
									@foreach($models as $models) 
									<option value="{{ $models->model }}">{{ $models->model }}</option>
									@endforeach
								</select>
							</div> -->
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillData()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<div style="background-color: orange;text-align: center;color: white;font-size: 20px;font-weight: bold;margin-bottom: 10px" id="titleFungsi">
								<span style="width: 100%">QA FUNGSI</span>
							</div>
							<table id="tableNgReportFungsi" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Serial Number</th>
										<th>Model Stamp</th>
										<th>Model WIP</th>
										<th>Model Packing</th>
										<th>Cek Fungsi Produksi</th>
										<th>Operator QA Fungsi</th>
										<th>Datetime QA Fungsi</th>
										<th>Result QA Fungsi</th>
										<th>NG</th>
										<th>Kunci</th>
										<th>Value</th>
										<th>Lokasi</th>
										<th>Ganti Kunci</th>
										<th>Note</th>
										<th>Inputed At</th>
										<th>Packing Date</th>
										<th>Packing Time</th>
									</tr>
								</thead>
								<tbody id="bodyTableNgReportFungsi">
								</tbody>
							</table>

							<div style="background-color: green;text-align: center;color: white;font-size: 20px;font-weight: bold;margin-bottom: 10px" id="titleVisual1">
								<span style="width: 100%">QA VISUAL 1</span>
							</div>
							<table id="tableNgReportVisual1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Serial Number</th>
										<th>Model Stamp</th>
										<th>Model WIP</th>
										<th>Model Packing</th>
										<th>Cek Visual Produksi</th>
										<th>Operator QA Visual 1</th>
										<th>Datetime QA Visual 1</th>
										<th>Result QA Visual 1</th>
										<th>NG</th>
										<th>Kunci</th>
										<th>Value</th>
										<th>Lokasi</th>
										<th>Ganti Kunci</th>
										<th>Note</th>
										<th>Inputed At</th>
										<th>Packing Date</th>
										<th>Packing Time</th>
									</tr>
								</thead>
								<tbody id="bodyTableNgReportVisual1">
								</tbody>
							</table>

							<div style="background-color: blue;text-align: center;color: white;font-size: 20px;font-weight: bold;margin-bottom: 10px" id="titleVisual2">
								<span style="width: 100%">QA VISUAL 2</span>
							</div>
							<table id="tableNgReportVisual2" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Serial Number</th>
										<th>Model Stamp</th>
										<th>Model WIP</th>
										<th>Model Packing</th>
										<th>Cek Visual Produksi</th>
										<th>Operator QA Visual 2</th>
										<th>Datetime QA Visual 2</th>
										<th>Result QA Visual 2</th>
										<th>NG</th>
										<th>Kunci</th>
										<th>Value</th>
										<th>Lokasi</th>
										<th>Ganti Kunci</th>
										<th>Note</th>
										<th>Inputed At</th>
										<th>Packing Date</th>
										<th>Packing Time</th>
									</tr>
								</thead>
								<tbody id="bodyTableNgReportVisual2">
								</tbody>
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
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
{{-- <script src="{{ url('js/pdfmake.min.js')}}"></script> --}}
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		$('.select2').select2({
			allowClear:true
		});

		if ('{{$process}}' == 'qa-fungsi') {
			$('#tableNgReportFungsi').show();
			$('#tableNgReportVisual1').hide();
			$('#tableNgReportVisual2').hide();

			$('#titleFungsi').show();
			$('#titleVisual1').hide();
			$('#titleVisual2').hide();
		}else if('{{$process}}' == 'qa-visual1'){
			$('#tableNgReportFungsi').hide();
			$('#tableNgReportVisual1').show();
			$('#tableNgReportVisual2').hide();

			$('#titleFungsi').hide();
			$('#titleVisual1').show();
			$('#titleVisual2').hide();
		}else if('{{$process}}' == 'qa-visual2'){
			$('#tableNgReportFungsi').hide();
			$('#tableNgReportVisual1').hide();
			$('#tableNgReportVisual2').show();

			$('#titleFungsi').hide();
			$('#titleVisual1').hide();
			$('#titleVisual2').show();
		}
		// fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fillData(){
		$('#loading').show();
		$('#flo_detail_table').DataTable().destroy();
		var datefrom = $('#datefrom').val();
		// var dateto = $('#dateto').val();
		// var model = $('#model').val();

		var proces = '{{$process}}';

		url	= '{{ url("fetch/assembly/serial_number_report") }}'+'/'+proces;
		
		var data = {
			datefrom:datefrom,
			// dateto:dateto,
			// model:model
		}
		$.get(url,data, function(result, status, xhr){
			if(result.status){
				if (result.report_fungsi != null) {
					$('#tableNgReportFungsi').DataTable().clear();
					$('#tableNgReportFungsi').DataTable().destroy();
					$('#bodyTableNgReportFungsi').html("");
					var tableDataFungsi = "";
					
					$.each(result.report_fungsi, function(key, value) {
						if (value.ng_name ==  null) {
							tableDataFungsi += '<tr>';
							tableDataFungsi += '<td>'+ value.serial_number +'</td>';
							tableDataFungsi += '<td>'+ value.model_stamp +'</td>';
							tableDataFungsi += '<td>'+ value.model_wip +'</td>';
							tableDataFungsi += '<td>'+ value.model_packing +'</td>';
							var op_prod_name = '';
							if (value.op_prod != null) {
								if (value.op_prod.match(/,/gi)) {
									var emps = value.op_prod.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_prod_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_prod) {
											op_prod_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataFungsi += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
							var op_qa_name = '';
							if (value.op_qa != null) {
								if (value.op_qa.match(/,/gi)) {
									var emps = value.op_qa.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_qa_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_qa) {
											op_qa_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataFungsi += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
							if (value.datetime_qa != null) {
								tableDataFungsi += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
							}else{
								tableDataFungsi += '<td></td>';
							}
							tableDataFungsi += '<td><span class="label label-success">OK</span></td>';
							tableDataFungsi += '<td></td>';
							tableDataFungsi += '<td></td>';
							tableDataFungsi += '<td></td>';
							tableDataFungsi += '<td></td>';
							var ganti_kunci = '';
							if (value.ganti_kunci != null) {
								ganti_kunci = value.ganti_kunci;
							}else if(value.ganti_kunci_temuan != null){
								ganti_kunci = value.ganti_kunci_temuan;
							}
							tableDataFungsi += '<td>';
							tableDataFungsi += ganti_kunci;
							tableDataFungsi += '</td>';
							var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataFungsi += '<td>'+notes.join('')+'</td>';
							tableDataFungsi += '<td>'+ (value.inputed_at || '') +'</td>';
							tableDataFungsi += '<td>'+value.packing_date+'</td>';
							tableDataFungsi += '<td>'+value.packing_time+'</td>';
							tableDataFungsi += '</tr>';
						}else{
							if (value.ng_name.match(/,/gi)) {
								var ngs = value.ng_name.split(',');
								for(var i = 0; i < ngs.length;i++){
									tableDataFungsi += '<tr>';
									tableDataFungsi += '<td>'+ value.serial_number +'</td>';
									tableDataFungsi += '<td>'+ value.model_stamp +'</td>';
									tableDataFungsi += '<td>'+ value.model_wip +'</td>';
									tableDataFungsi += '<td>'+ value.model_packing +'</td>';
									var op_prod_name = '';
									if (value.op_prod != null) {
										if (value.op_prod.match(/,/gi)) {
											var emps = value.op_prod.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var j = 0; j < result.emp.length;j++){
												if (result.emp[j].employee_id == value.op_prod) {
													op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}
									tableDataFungsi += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
									var op_qa_name = '';
									if (value.op_qa != null) {
										if (value.op_qa.match(/,/gi)) {
											var emps = value.op_qa.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == value.op_qa) {
													op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
												}
											}
										}
									}
									tableDataFungsi += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
									if (value.datetime_qa != null) {
										tableDataFungsi += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
									}else{
										tableDataFungsi += '<td></td>';
									}
									tableDataFungsi += '<td><span class="label label-danger">NG</span></td>';
									if (ngs[i].split('_')[0] == 'Tanpo Awase') {
										tableDataFungsi += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataFungsi += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataFungsi += '<td>'+ngs[i].split('_')[2]+'-'+ngs[i].split('_')[3]+'</td>';
										tableDataFungsi += '<td>'+ngs[i].split('_')[4]+'</td>';
									}else{
										tableDataFungsi += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataFungsi += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataFungsi += '<td></td>';
										tableDataFungsi += '<td></td>';
									}
									var ganti_kunci = '';
									if (value.ganti_kunci != null) {
										ganti_kunci = value.ganti_kunci;
									}else if(value.ganti_kunci_temuan != null){
										ganti_kunci = value.ganti_kunci_temuan;
									}
									tableDataFungsi += '<td>';
									tableDataFungsi += ganti_kunci;
									tableDataFungsi += '</td>';
									var notes = [];
									if (value.notes != null) {
										notes.push('Note:'+value.notes);
									}
									tableDataFungsi += '<td>'+notes.join('')+'</td>';
									tableDataFungsi += '<td>'+ (value.inputed_at || '') +'</td>';
									tableDataFungsi += '<td>'+value.packing_date+'</td>';
									tableDataFungsi += '<td>'+value.packing_time+'</td>';
									tableDataFungsi += '</tr>';
								}
							}else{
								tableDataFungsi += '<tr>';
								tableDataFungsi += '<td>'+ value.serial_number +'</td>';
								tableDataFungsi += '<td>'+ value.model_stamp +'</td>';
								tableDataFungsi += '<td>'+ value.model_wip +'</td>';
								tableDataFungsi += '<td>'+ value.model_packing +'</td>';
								var op_prod_name = '';
								if (value.op_prod != null) {
									if (value.op_prod.match(/,/gi)) {
										var emps = value.op_prod.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var j = 0; j < result.emp.length;j++){
											if (result.emp[j].employee_id == value.op_prod) {
												op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}
								tableDataFungsi += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
								var op_qa_name = '';
								if (value.op_qa != null) {
									if (value.op_qa.match(/,/gi)) {
										var emps = value.op_qa.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var k = 0; k < result.emp.length;k++){
											if (result.emp[k].employee_id == value.op_qa) {
												op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
											}
										}
									}
								}
								tableDataFungsi += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
								if (value.datetime_qa != null) {
									tableDataFungsi += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
								}else{
									tableDataFungsi += '<td></td>';
								}
								tableDataFungsi += '<td><span class="label label-danger">NG</span></td>';
								if (value.ng_name.split('_')[0] == 'Tanpo Awase') {
									tableDataFungsi += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataFungsi += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataFungsi += '<td>'+value.ng_name.split('_')[2]+'-'+value.ng_name.split('_')[3]+'</td>';
									tableDataFungsi += '<td>'+value.ng_name.split('_')[4]+'</td>';
								}else{
									tableDataFungsi += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataFungsi += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataFungsi += '<td></td>';
									tableDataFungsi += '<td></td>';
								}
								var ganti_kunci = '';
								if (value.ganti_kunci != null) {
									ganti_kunci = value.ganti_kunci;
								}else if(value.ganti_kunci_temuan != null){
									ganti_kunci = value.ganti_kunci_temuan;
								}
								tableDataFungsi += '<td>';
								tableDataFungsi += ganti_kunci;
								tableDataFungsi += '</td>';
								var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataFungsi += '<td>'+notes.join('')+'</td>';
								tableDataFungsi += '<td>'+ (value.inputed_at || '') +'</td>';
								tableDataFungsi += '<td>'+value.packing_date+'</td>';
								tableDataFungsi += '<td>'+value.packing_time+'</td>';
								tableDataFungsi += '</tr>';
							}
						}
					});
					$('#bodyTableNgReportFungsi').append(tableDataFungsi);

					var table = $('#tableNgReportFungsi').DataTable({
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
						'searching': true,
						"processing": true,
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

				if (result.report_visual1 != null) {
					$('#tableNgReportVisual1').DataTable().clear();
					$('#tableNgReportVisual1').DataTable().destroy();
					$('#bodyTableNgReportVisual1').html("");
					var tableDataVisual1 = "";
					
					$.each(result.report_visual1, function(key, value) {
						if (value.ng_name ==  null) {
							tableDataVisual1 += '<tr>';
							tableDataVisual1 += '<td>'+ value.serial_number +'</td>';
							tableDataVisual1 += '<td>'+ value.model_stamp +'</td>';
							tableDataVisual1 += '<td>'+ value.model_wip +'</td>';
							tableDataVisual1 += '<td>'+ value.model_packing +'</td>';
							var op_prod_name = '';
							if (value.op_prod != null) {
								if (value.op_prod.match(/,/gi)) {
									var emps = value.op_prod.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_prod_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_prod) {
											op_prod_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataVisual1 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
							var op_qa_name = '';
							if (value.op_qa != null) {
								if (value.op_qa.match(/,/gi)) {
									var emps = value.op_qa.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_qa_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_qa) {
											op_qa_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataVisual1 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
							if (value.datetime_qa != null) {
								tableDataVisual1 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
							}else{
								tableDataVisual1 += '<td></td>';
							}
							tableDataVisual1 += '<td><span class="label label-success">OK</span></td>';
							tableDataVisual1 += '<td></td>';
							tableDataVisual1 += '<td></td>';
							tableDataVisual1 += '<td></td>';
							tableDataVisual1 += '<td></td>';							
							var ganti_kunci = '';
							if (value.ganti_kunci != null) {
								ganti_kunci = value.ganti_kunci;
							}else if(value.ganti_kunci_temuan != null){
								ganti_kunci = value.ganti_kunci_temuan;
							}
							tableDataVisual1 += '<td>';
							tableDataVisual1 += ganti_kunci;
							tableDataVisual1 += '</td>';
							var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual1 += '<td>'+notes.join('')+'</td>';
							tableDataVisual1 += '<td>'+ (value.inputed_at || '') +'</td>';
							tableDataVisual1 += '<td>'+value.packing_date+'</td>';
							tableDataVisual1 += '<td>'+value.packing_time+'</td>';
							tableDataVisual1 += '</tr>';
						}else{
							if (value.ng_name.match(/,/gi)) {
								var ngs = value.ng_name.split(',');
								for(var i = 0; i < ngs.length;i++){
									tableDataVisual1 += '<tr>';
									tableDataVisual1 += '<td>'+ value.serial_number +'</td>';
									tableDataVisual1 += '<td>'+ value.model_stamp +'</td>';
									tableDataVisual1 += '<td>'+ value.model_wip +'</td>';
									tableDataVisual1 += '<td>'+ value.model_packing +'</td>';
									var op_prod_name = '';
									if (value.op_prod != null) {
										if (value.op_prod.match(/,/gi)) {
											var emps = value.op_prod.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var j = 0; j < result.emp.length;j++){
												if (result.emp[j].employee_id == value.op_prod) {
													op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}
									tableDataVisual1 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
									var op_qa_name = '';
									if (value.op_qa != null) {
										if (value.op_qa.match(/,/gi)) {
											var emps = value.op_qa.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == value.op_qa) {
													op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
												}
											}
										}
									}
									tableDataVisual1 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
									if (value.datetime_qa != null) {
										tableDataVisual1 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
									}else{
										tableDataVisual1 += '<td></td>';
									}
									tableDataVisual1 += '<td><span class="label label-danger">NG</span></td>';
									if (ngs[i].split('_')[0] == 'Tanpo Awase') {
										tableDataVisual1 += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataVisual1 += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataVisual1 += '<td>'+ngs[i].split('_')[2]+'-'+ngs[i].split('_')[3]+'</td>';
										tableDataVisual1 += '<td>'+ngs[i].split('_')[4]+'</td>';
									}else{
										tableDataVisual1 += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataVisual1 += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataVisual1 += '<td></td>';
										tableDataVisual1 += '<td></td>';
									}
									var ganti_kunci = '';
									if (value.ganti_kunci != null) {
										ganti_kunci = value.ganti_kunci;
									}else if(value.ganti_kunci_temuan != null){
										ganti_kunci = value.ganti_kunci_temuan;
									}
									tableDataVisual1 += '<td>';
									tableDataVisual1 += ganti_kunci;
									tableDataVisual1 += '</td>';
									var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual1 += '<td>'+notes.join('')+'</td>';
								tableDataVisual1 += '<td>'+ (value.inputed_at || '') +'</td>';
									tableDataVisual1 += '<td>'+value.packing_date+'</td>';
									tableDataVisual1 += '<td>'+value.packing_time+'</td>';
									tableDataVisual1 += '</tr>';
								}
							}else{
								tableDataVisual1 += '<tr>';
								tableDataVisual1 += '<td>'+ value.serial_number +'</td>';
								tableDataVisual1 += '<td>'+ value.model_stamp +'</td>';
								tableDataVisual1 += '<td>'+ value.model_wip +'</td>';
								tableDataVisual1 += '<td>'+ value.model_packing +'</td>';
								var op_prod_name = '';
								if (value.op_prod != null) {
									if (value.op_prod.match(/,/gi)) {
										var emps = value.op_prod.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var j = 0; j < result.emp.length;j++){
											if (result.emp[j].employee_id == value.op_prod) {
												op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}
								tableDataVisual1 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
								var op_qa_name = '';
								if (value.op_qa != null) {
									if (value.op_qa.match(/,/gi)) {
										var emps = value.op_qa.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var k = 0; k < result.emp.length;k++){
											if (result.emp[k].employee_id == value.op_qa) {
												op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
											}
										}
									}
								}
								tableDataVisual1 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
								if (value.datetime_qa != null) {
									tableDataVisual1 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
								}else{
									tableDataVisual1 += '<td></td>';
								}
								tableDataVisual1 += '<td><span class="label label-danger">NG</span></td>';
								if (value.ng_name.split('_')[0] == 'Tanpo Awase') {
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[2]+'-'+value.ng_name.split('_')[3]+'</td>';
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[4]+'</td>';
								}else{
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataVisual1 += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataVisual1 += '<td></td>';
									tableDataVisual1 += '<td></td>';
								}
								var ganti_kunci = '';
								if (value.ganti_kunci != null) {
									ganti_kunci = value.ganti_kunci;
								}else if(value.ganti_kunci_temuan != null){
									ganti_kunci = value.ganti_kunci_temuan;
								}
								tableDataVisual1 += '<td>';
								tableDataVisual1 += ganti_kunci;
								tableDataVisual1 += '</td>';
								var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual1 += '<td>'+notes.join('')+'</td>';
								tableDataVisual1 += '<td>'+ (value.inputed_at || '') +'</td>';
								tableDataVisual1 += '<td>'+value.packing_date+'</td>';
								tableDataVisual1 += '<td>'+value.packing_time+'</td>';
								tableDataVisual1 += '</tr>';
							}
						}
					});
					$('#bodyTableNgReportVisual1').append(tableDataVisual1);

					var table = $('#tableNgReportVisual1').DataTable({
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
						'searching': true,
						"processing": true,
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

				if (result.report_visual2 != null) {
					$('#tableNgReportVisual2').DataTable().clear();
					$('#tableNgReportVisual2').DataTable().destroy();
					$('#bodyTableNgReportVisual2').html("");
					var tableDataVisual2 = "";
					
					$.each(result.report_visual2, function(key, value) {
						if (value.ng_name ==  null) {
							tableDataVisual2 += '<tr>';
							tableDataVisual2 += '<td>'+ value.serial_number +'</td>';
							tableDataVisual2 += '<td>'+ value.model_stamp +'</td>';
							tableDataVisual2 += '<td>'+ value.model_wip +'</td>';
							tableDataVisual2 += '<td>'+ value.model_packing +'</td>';
							var op_prod_name = '';
							if (value.op_prod != null) {
								if (value.op_prod.match(/,/gi)) {
									var emps = value.op_prod.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_prod_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_prod) {
											op_prod_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataVisual2 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
							var op_qa_name = '';
							if (value.op_qa != null) {
								if (value.op_qa.match(/,/gi)) {
									var emps = value.op_qa.split(',');
									for(var j = 0; j < emps.length; j++){
										for(var i = 0; i < result.emp.length;i++){
											if (result.emp[i].employee_id == emps[j]) {
												op_qa_name += '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}else{
									for(var i = 0; i < result.emp.length;i++){
										if (result.emp[i].employee_id == value.op_qa) {
											op_qa_name = '<br>'+result.emp[i].name.split(' ').slice(0,2).join(' ');
										}
									}
								}
							}
							tableDataVisual2 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
							if (value.datetime_qa != null) {
								tableDataVisual2 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
							}else{
								tableDataVisual2 += '<td></td>';
							}
							tableDataVisual2 += '<td><span class="label label-success">OK</span></td>';
							tableDataVisual2 += '<td></td>';
							tableDataVisual2 += '<td></td>';
							tableDataVisual2 += '<td></td>';
							tableDataVisual2 += '<td></td>';
							var ganti_kunci = '';
							if (value.ganti_kunci != null) {
								ganti_kunci = value.ganti_kunci;
							}else if(value.ganti_kunci_temuan != null){
								ganti_kunci = value.ganti_kunci_temuan;
							}
							tableDataVisual2 += '<td>';
							tableDataVisual2 += ganti_kunci;
							tableDataVisual2 += '</td>';
							var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual2 += '<td>'+notes.join('')+'</td>';
							tableDataVisual2 += '<td>'+ (value.inputed_at || '') +'</td>';
							tableDataVisual2 += '<td>'+value.packing_date+'</td>';
							tableDataVisual2 += '<td>'+value.packing_time+'</td>';
							tableDataVisual2 += '</tr>';
						}else{
							if (value.ng_name.match(/,/gi)) {
								var ngs = value.ng_name.split(',');
								for(var i = 0; i < ngs.length;i++){
									tableDataVisual2 += '<tr>';
									tableDataVisual2 += '<td>'+ value.serial_number +'</td>';
									tableDataVisual2 += '<td>'+ value.model_stamp +'</td>';
									tableDataVisual2 += '<td>'+ value.model_wip +'</td>';
									tableDataVisual2 += '<td>'+ value.model_packing +'</td>';
									var op_prod_name = '';
									if (value.op_prod != null) {
										if (value.op_prod.match(/,/gi)) {
											var emps = value.op_prod.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var j = 0; j < result.emp.length;j++){
												if (result.emp[j].employee_id == value.op_prod) {
													op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}
									tableDataVisual2 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
									var op_qa_name = '';
									if (value.op_qa != null) {
										if (value.op_qa.match(/,/gi)) {
											var emps = value.op_qa.split(',');
											for(var j = 0; j < emps.length; j++){
												for(var k = 0; k < result.emp.length;k++){
													if (result.emp[k].employee_id == emps[j]) {
														op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
													}
												}
											}
										}else{
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == value.op_qa) {
													op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
												}
											}
										}
									}
									tableDataVisual2 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
									if (value.datetime_qa != null) {
										tableDataVisual2 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
									}else{
										tableDataVisual2 += '<td></td>';
									}
									tableDataVisual2 += '<td><span class="label label-danger">NG</span></td>';
									if (ngs[i].split('_')[0] == 'Tanpo Awase') {
										tableDataVisual2 += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataVisual2 += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataVisual2 += '<td>'+ngs[i].split('_')[2]+'-'+ngs[i].split('_')[3]+'</td>';
										tableDataVisual2 += '<td>'+ngs[i].split('_')[4]+'</td>';
									}else{
										tableDataVisual2 += '<td>'+ngs[i].split('_')[0]+'</td>';
										tableDataVisual2 += '<td>'+ngs[i].split('_')[1]+'</td>';
										tableDataVisual2 += '<td></td>';
										tableDataVisual2 += '<td></td>';
									}
									var ganti_kunci = '';
									if (value.ganti_kunci != null) {
										ganti_kunci = value.ganti_kunci;
									}else if(value.ganti_kunci_temuan != null){
										ganti_kunci = value.ganti_kunci_temuan;
									}
									tableDataVisual2 += '<td>';
									tableDataVisual2 += ganti_kunci;
									tableDataVisual2 += '</td>';
									var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual2 += '<td>'+notes.join('')+'</td>';
								tableDataVisual2 += '<td>'+ (value.inputed_at || '') +'</td>';
									tableDataVisual2 += '<td>'+value.packing_date+'</td>';
									tableDataVisual2 += '<td>'+value.packing_time+'</td>';
									tableDataVisual2 += '</tr>';
								}
							}else{
								tableDataVisual2 += '<tr>';
								tableDataVisual2 += '<td>'+ value.serial_number +'</td>';
								tableDataVisual2 += '<td>'+ value.model_stamp +'</td>';
								tableDataVisual2 += '<td>'+ value.model_wip +'</td>';
								tableDataVisual2 += '<td>'+ value.model_packing +'</td>';
								var op_prod_name = '';
								if (value.op_prod != null) {
									if (value.op_prod.match(/,/gi)) {
										var emps = value.op_prod.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_prod_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var j = 0; j < result.emp.length;j++){
											if (result.emp[j].employee_id == value.op_prod) {
												op_prod_name = '<br>'+result.emp[j].name.split(' ').slice(0,2).join(' ');
											}
										}
									}
								}
								tableDataVisual2 += '<td>'+ value.op_prod +''+ op_prod_name +'</td>';
								var op_qa_name = '';
								if (value.op_qa != null) {
									if (value.op_qa.match(/,/gi)) {
										var emps = value.op_qa.split(',');
										for(var j = 0; j < emps.length; j++){
											for(var k = 0; k < result.emp.length;k++){
												if (result.emp[k].employee_id == emps[j]) {
													op_qa_name += '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ');
												}
											}
										}
									}else{
										for(var k = 0; k < result.emp.length;k++){
											if (result.emp[k].employee_id == value.op_qa) {
												op_qa_name = '<br>'+result.emp[k].name.split(' ').slice(0,2).join(' ')
											}
										}
									}
								}
								tableDataVisual2 += '<td>'+ value.op_qa +''+ op_qa_name +'</td>';
								if (value.datetime_qa != null) {
									tableDataVisual2 += '<td>'+ value.datetime_qa.split(',').join('<br>') +'</td>';
								}else{
									tableDataVisual2 += '<td></td>';
								}
								tableDataVisual2 += '<td><span class="label label-danger">NG</span></td>';
								if (value.ng_name.split('_')[0] == 'Tanpo Awase') {
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[2]+'-'+value.ng_name.split('_')[3]+'</td>';
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[4]+'</td>';
								}else{
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[0]+'</td>';
									tableDataVisual2 += '<td>'+value.ng_name.split('_')[1]+'</td>';
									tableDataVisual2 += '<td></td>';
									tableDataVisual2 += '<td></td>';
								}
								var ganti_kunci = '';
								if (value.ganti_kunci != null) {
									ganti_kunci = value.ganti_kunci;
								}else if(value.ganti_kunci_temuan != null){
									ganti_kunci = value.ganti_kunci_temuan;
								}
								tableDataVisual2 += '<td>';
								tableDataVisual2 += ganti_kunci;
								tableDataVisual2 += '</td>';
								var notes = [];
								if (value.notes != null) {
									notes.push('Note:'+value.notes);
								}
								tableDataVisual2 += '<td>'+notes.join('')+'</td>';
								tableDataVisual2 += '<td>'+ (value.inputed_at || '') +'</td>';
								tableDataVisual2 += '<td>'+value.packing_date+'</td>';
								tableDataVisual2 += '<td>'+value.packing_time+'</td>';
								tableDataVisual2 += '</tr>';
							}
						}
					});
					$('#bodyTableNgReportVisual2').append(tableDataVisual2);

					var table = $('#tableNgReportVisual2').DataTable({
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
						'searching': true,
						"processing": true,
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

				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection