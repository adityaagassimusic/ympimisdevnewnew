@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-hover > tbody > tr > td:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input {
		line-height: 24px;
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
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;

	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error {
		display: none;
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-3 col-xs-offset-3">
							<div class="form-group">
								<label>Approval From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="datefrom" placeholder="Select Date">
								</div>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Approval To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="dateto" placeholder="Select Date">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-3">
							<div class="form-group">
								<label>Material:</label>
								<select class="form-control select3" multiple="multiple" id='material' data-placeholder="Select Material" style="width: 100%;">
									<option></option>
									@foreach($material as $mt)
									<option value="{{ $mt->material_number }}">{{ $mt->material_number }} - {{ $mt->material_description }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Status:</label>
								<select class="form-control select2" id='status' data-placeholder="Select Status" style="width: 100%;">
									<option></option>
									<option value="1">OK</option>
									<option value="0">NG</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<div class="pull-right">
								<div class="form-group">
									<button onClick="fillTable()" class="btn btn-success form-control">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="box no-border">
				<div class="box-body">
					<table id="docTable" class="table table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="">Created At</th>
								<th style="">Order ID</th>
								<th style="">Material</th>
								<th style="">Description</th>
								<th style="">Status</th>
								<th style="width: 15%;">OP Molding</th>
								<th style="">Mesin</th>
								<th style="">Pre Approval</th>
								<th style="">Approval</th>
								<th style="">Details</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		
	</div>
</div>
</section>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="title_modal" style="font-weight: bold; font-size: 1.5vw;"></span>
				</center>				
			</div>
			<div class="modal-body" style="min-height: 100px; padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-6">
						<table class="table table-bordered">
							<thead style="background-color: orange;">
								<tr>
									<th colspan="2" style="font-size: 2vw;" id="tittle_table"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th style="vertical-align: middle;">ORDER ID</th>
									<th>
										<input type="text" name="order_id" id="order_id" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">TANGGAL</th>
									<th>
										<input type="text" name="date" id="date" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">OP MTC Molding</th>
									<th>
										<input type="text" name="operator" id="operator" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">MESIN</th>
									<th>
										<input type="text" name="machine" id="machine" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">RESIN</th>
									<th>
										<input type="text" name="resin" id="resin" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">NO. LOT RESIN</th>
									<th>
										<input type="text" name="lot" id="lot" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">PARAMETER LS 4</th>
									<th>
										<input type="text" name="parameter" id="parameter" class="form-control" readonly>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: middle;">PARAMETER INJEKSI</th>
									<th>
										<img width="150px" id="photo" src="" style="width: 500px; height: 350px;" alt=""/>
									</th>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-xs-6">
						<div id="message_ok">
							<table class="table table-hover table-bordered table-striped" id="tableDetail">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 20%; vertical-align: middle; padding: 0px;" rowspan="3">No Shot</th>
										<th colspan="4" style="padding: 0px;">Poin Ukur</th>
									</tr>
									<tr>
										<th style="width: 20%; padding: 0px;">1</th>
										<th style="width: 20%; padding: 0px;">2</th>
										<th style="width: 20%; padding: 0px;">3</th>
										<th style="width: 20%; padding: 0px;">4</th>
									</tr>
									<tr>
										<th style="vertical-align: middle; padding: 0px;">Panjang (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Diameter Cold Slug Well (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Tebal (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Berat (gr)</th>
									</tr>
								</thead>
								<tbody id="tableDetailBody">
								</tbody>
							</table>
							<div class="col-xs-12">
								<h3 style="margin-top: 0px;" id="tittle_poin_ukur"></h3>
								<table class="table table-bordered table-stripped">
									<thead style="background-color: #cdcdcd">
										<tr>
											<th style="width: 20%; padding: 0px; vertical-align: middle;">Produk</th>
											<th style="width: 20%; padding: 0px; vertical-align: middle;">Panjang</th>
											<th style="width: 20%; padding: 0px; vertical-align: middle;">Diamater Cold Slug Well</th>
											<th style="width: 20%; padding: 0px; vertical-align: middle;">Tebal</th>
											<th style="width: 20%; padding: 0px; vertical-align: middle;">Berat</th>
										</tr>
									</thead>
									<tbody id="tableMeasurementBody">
									</tbody>
								</table>
							</div>
							<div class="col-xs-12">
								<h5 style="font-weight: bold;">Standart Panjang : Rata-rata keseluruhan - (4 x σ) ≥ Dimensi Trimming Maksimal + Trim Allowance</h5>
							</div>
							<div class="col-xs-8 col-xs-offset-2" style="padding-left: 0px;">
								<table class="table table-bordered table-stripped">
									<thead style="background-color: #cdcdcd">
										<tr>
											<th style="padding: 0px;">Poin Ukur</th>
											<th style="padding: 0px;">Status</th>
											<th style="padding: 0px;">Ket.</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="padding: 0px;">Panjang</td>
											<td style="padding: 0px;" id="panjang_ket"></td>
											<td style="padding: 0px;" id="panjang_value"></td>
										</tr>
										<tr>
											<td style="padding: 0px; font-size: 14px;">Diameter Cold Slug Well</td>
											<td style="padding: 0px;" id="diameter_ket"></td>
											<td style="padding: 0px;" id="diameter_value"></td>
										</tr>
										<tr>
											<td style="padding: 0px;">Tebal</td>
											<td style="padding: 0px;" id="tebal_ket"></td>
											<td style="padding: 0px;" id="tebal_value"></td>
										</tr>
										<tr>
											<td style="padding: 0px;">Berat</td>
											<td style="padding: 0px;" id="berat_ket"></td>
											<td style="padding: 0px;" id="berat_value"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div id="message_ng">
							<div class="col-xs-8 col-xs-offset-2">
								<div class="small-box bg-red">
									<div class="inner">
										<h3>NG</h3>
										<p>Sample NG sebelum dilakukan<br>cek dimensi material</p>
									</div>
									<div class="icon" style="margin-top: 2%;">
										<i class="glyphicon glyphicon-remove"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear: true
		});

		$('.select3').select2();

		fillTable();
	});

	function showPreApproval(id) {
		window.open('{{ url("index/reed/pre_approval_pdf") }}'+'/'+id, '_blank');
	}

	function showApproval(id) {
		window.open('{{ url("index/reed/approval_pdf") }}'+'/'+id, '_blank');
	}


	function fillTable(){
		$('#docTable').DataTable().clear();
		$('#docTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var material = $('#material').val();
		var status = $('#status').val();
		var id = "{{ $id }}";

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			material:material,
			status:status,
			id:id,
		}

		var table = $('#docTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
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
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/reed/injection_report") }}",
				"data" : data
			},
			"columnDefs": [ {
				"targets": [4],
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData == 'OK' ) {
						$(td).css('background-color', 'rgba(107, 255, 104, 0.6)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}else {
						$(td).css('background-color', 'rgba(242, 75, 75, 0.8)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}
				}
			}],
			"columns": [
			{ "data": "created_at" },
			{ "data": "order_id" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "status_name" },
			{ "data": "operator" },
			{ "data": "mesin" },
			{ "data": "pre_approval" },
			{ "data": "approval" },
			{ "data": "detail" }
			]
		});			
	}

	function showDetail(id) {

		var data = {
			id : id
		}

		$.get('{{ url("fetch/reed/injection_report_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableDetailBody').html("");
				$('#tableMeasurementBody').html("");

				$('#title_modal').text('CEK DIMENSI MATERIAL REED SYNTHETIC');
				$('#tittle_poin_ukur').text('STANDAR POIN UKUR ' + result.approval.material_description.toUpperCase());

				$('#tittle_table').text(result.approval.material_description);
				$('#order_id').val(result.approval.order_id);
				$('#date').val(result.approval.date);
				$('#operator').val(result.approval.operator_id + ' - ' + result.approval.name);
				$('#machine').val(result.approval.mesin);
				$('#resin').val(result.approval.resin);
				$('#lot').val(result.approval.lot_resin);
				$('#parameter').val(result.approval.parameter);
				$("#photo").attr('src', result.photo);

				if(result.detail.length > 0){
					$('#message_ok').show();
					$('#message_ng').hide();

					var detail = '';
					var sum_length = 0;
					var sum_diameter = 0;
					var sum_thickness = 0;
					var sum_weight = 0;
					var length = [];
					var diameter = [];
					var thickness = [];
					var weight = [];
					var status_length = 'OK';
					var status_diameter = 'OK';
					var status_thickness = 'OK';
					var status_weight = 'OK';
					$.each(result.detail, function(key, value){

						var css_diameter = '';
						if(result.measurement.max_diameter == null){
							if(value.diameter < result.measurement.min_diameter){
								css_diameter += 'style="background-color: rgb(255, 204, 255);"';
								status_diameter = 'NG';
							}
						}else{
							if(value.diameter < result.measurement.min_diameter || value.diameter > result.measurement.max_diameter){
								css_diameter += 'style="background-color: rgb(255, 204, 255);"';
								status_diameter = 'NG';
							}
						}

						var css_thickness = '';
						if(result.measurement.max_thickness == null){
							if(value.thickness < result.measurement.min_thickness){
								css_thickness += 'style="background-color: rgb(255, 204, 255);"';
								status_thickness = 'NG';
							}
						}else{
							if(value.thickness < result.measurement.min_thickness || value.thickness > result.measurement.max_thickness){
								css_thickness += 'style="background-color: rgb(255, 204, 255);"';
								status_thickness = 'NG';
							}
						}

						var css_weight = '';
						if(result.measurement.max_weight == null){
							if(value.weight < result.measurement.min_weight){
								css_weight += 'style="background-color: rgb(255, 204, 255);"';
								status_weight = 'NG';
							}
						}else{
							if(value.weight < result.measurement.min_weight || value.weight > result.measurement.max_weight){
								css_weight += 'style="background-color: rgb(255, 204, 255);"';
								status_weight = 'NG';
							}
						}	

						detail += '<tr>';
						detail += '<td>'+value.shot+'</td>';
						detail += '<td>'+value.length+'</td>';
						detail += '<td '+css_diameter+'>'+value.diameter+'</td>';
						detail += '<td '+css_thickness+'>'+value.thickness+'</td>';
						detail += '<td '+css_weight+'>'+value.weight+'</td>';
						detail += '</tr>';

						sum_length += parseFloat(value.length);
						sum_diameter += parseFloat(value.diameter);
						sum_thickness += parseFloat(value.thickness);
						sum_weight += parseFloat(value.weight);

						length.push(parseFloat(value.length));
						diameter.push(parseFloat(value.diameter));
						thickness.push(parseFloat(value.thickness));
						weight.push(parseFloat(value.weight));
					});
					var css = 'style="border: 1px solid rgb(211,211,211); text-align: center; vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold; background-color: rgb(252, 248, 227);"';

					var sigma_length = 0;
					var sigma_diameter = 0;
					var sigma_thickness = 0;
					var sigma_weight = 0;
					for (var x = 0; x < result.detail.length; x++) {
						var v1 =  Math.pow(parseFloat(length[x] - (sum_length/result.detail.length)), 2);
						var v2 =  Math.pow(parseFloat(diameter[x] - (sum_diameter/result.detail.length)), 2);
						var v3 =  Math.pow(parseFloat(thickness[x] - (sum_thickness/result.detail.length)), 2);
						var v4 =  Math.pow(parseFloat(weight[x] - (sum_weight/result.detail.length)), 2);
						sigma_length += v1;
						sigma_diameter += v2;
						sigma_thickness += v3;
						sigma_weight += v4;
					}

					var std_length = Math.sqrt(sigma_length/result.detail.length);
					var std_diameter = Math.sqrt(sigma_diameter/result.detail.length);
					var std_thickness = Math.sqrt(sigma_thickness/result.detail.length);
					var std_weight = Math.sqrt(sigma_weight/result.detail.length);

					detail += '<tr>';
					detail += '<th '+css+'>STD Deviasi (σ)</th>';
					detail += '<th '+css+'>'+parseFloat(std_length).toFixed(4)+'</th>';
					detail += '<th '+css+'>'+parseFloat(std_diameter).toFixed(4)+'</th>';
					detail += '<th '+css+'>'+parseFloat(std_thickness).toFixed(4)+'</th>';
					detail += '<th '+css+'>'+parseFloat(std_weight).toFixed(4)+'</th>';
					detail += '</tr>';

					detail += '<tr>';
					detail += '<th '+css+'>Rata-rata</th>';
					detail += '<th '+css+'>'+parseFloat(sum_length/result.detail.length).toFixed(2)+'</th>';
					detail += '<th '+css+'>'+parseFloat(sum_diameter/result.detail.length).toFixed(2)+'</th>';
					detail += '<th '+css+'>'+parseFloat(sum_thickness/result.detail.length).toFixed(2)+'</th>';
					detail += '<th '+css+'>'+parseFloat(sum_weight/result.detail.length).toFixed(2)+'</th>';
					detail += '</tr>';

					detail += '<tr>';
					detail += '<th '+css+'>Nilai Maksimum</th>';
					detail += '<th '+css+'>'+Math.max.apply(null, length)+'</th>';
					detail += '<th '+css+'>'+Math.max.apply(null, diameter)+'</th>';
					detail += '<th '+css+'>'+Math.max.apply(null, thickness)+'</th>';
					detail += '<th '+css+'>'+Math.max.apply(null, weight)+'</th>';
					detail += '</tr>';

					detail += '<tr>';
					detail += '<th '+css+'>Nilai Minimum</th>';
					detail += '<th '+css+'>'+Math.min.apply(null, length)+'</th>';
					detail += '<th '+css+'>'+Math.min.apply(null, diameter)+'</th>';
					detail += '<th '+css+'>'+Math.min.apply(null, thickness)+'</th>';
					detail += '<th '+css+'>'+Math.min.apply(null, weight)+'</th>';
					detail += '</tr>';
					$('#tableDetailBody').append(detail);

					var measurement = '';
					measurement += '<tr>';
					measurement += '<td>'+ result.measurement.material_description +'</td>';

					if(result.measurement.max_length == null) {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_length + '</td>';
					} else {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_length + ' & ≤ ' + result.measurement.max_length +'</td>';
					}

					if(result.measurement.max_diameter == null) {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_diameter + '</td>';
					}else {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_diameter + ' & ≤ ' + result.measurement.max_diameter +'</td>';
					}

					if(result.measurement.max_thickness == null) {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_thickness + '</td>';
					}else {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_thickness + ' & ≤ ' + result.measurement.max_thickness +'</td>';
					}

					if(result.measurement.max_length == null) {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_weight + '</td>';
					}else {
						measurement += '<td style="vertical-align: middle;">≥ ' + result.measurement.min_weight + ' & ≤ ' + result.measurement.max_weight +'</td>';
					}	
					$('#tableMeasurementBody').append(measurement);


					var value_length = (sum_length/result.detail.length) - (4 * std_length);
					var color_length = 'rgb(204, 255, 255)';
					var color_diameter = 'rgb(204, 255, 255)';
					var color_thickness = 'rgb(204, 255, 255)';
					var color_weight = 'rgb(204, 255, 255)';
					if(result.measurement.max_length == null){
						if(value_length < result.measurement.min_length){
							color_length = 'rgb(255, 204, 255)';
							status_length = 'NG';
						}
					}else{
						if(value_length < result.measurement.min_length || value_length > result.measurement.max_length){
							color_length = 'rgb(255, 204, 255)';
							status_length = 'NG'
						}
					}

					if(status_diameter == 'NG'){
						color_diameter = 'rgb(255, 204, 255)';
					}

					if(status_thickness == 'NG'){
						color_thickness = 'rgb(255, 204, 255)';
					}

					if(status_weight == 'NG'){
						color_weight = 'rgb(255, 204, 255)';
					}

					$('#panjang_ket').text(status_length);
					$('#diameter_ket').text(status_diameter);
					$('#tebal_ket').text(status_thickness);
					$('#berat_ket').text(status_weight);

					$('#panjang_ket').css({"background-color" : color_length});
					$('#diameter_ket').css({"background-color" : color_diameter});
					$('#tebal_ket').css({"background-color" : color_thickness});
					$('#berat_ket').css({"background-color" : color_weight});

					$('#panjang_value').text(value_length.toFixed(2));
				}else{
					$('#message_ok').hide();
					$('#message_ng').show();
				}

				$('#modalDetail').modal('show');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
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
}

$('.datepicker').datepicker({
	autoclose: true,
	format: "yyyy-mm-dd",
	todayHighlight: true,	
});



</script>
@endsection

