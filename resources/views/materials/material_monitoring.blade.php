@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading {
		display: none;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<input type="hidden" id="controlling_group" value="{{ $controlling_group }}">

		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div class="col-xs-1" style="padding-left: 0px">
				<a data-toggle="modal" data-target="#detailModelChart" id="btnModelChart" class="btn btn-info" style="width: 100%;"><i class="fa fa-gear" style="font-size: 2vw;"></i></a>
			</div>

			<div id="period_title" class="col-xs-8" style="background-color: #64b5f6;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
			</div>
			{{-- <div class="col-xs-1" style="padding-right: 0px">
				<a data-toggle="modal" data-target="#uploadModal" id="btnUpload" class="btn btn-info" style="width: 100%;"><i class="fa fa-upload" style="font-size: 2vw;"></i></a>
			</div> --}}
			<div class="col-xs-1" style="padding-right: 0px">
				<a data-toggle="modal" data-target="#detailModal" id="btnDetail" class="btn btn-info" style="width: 100%;"><i class="fa fa-search" style="font-size: 2vw;"></i></a>
			</div>
			<div class="col-xs-2 pull-right" style="padding-right: 0;">
				<div class="input-group date">
					<div class="input-group-addon" style="background-color: #64b5f6;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="period" name="datepicker" onchange="fetchChart()">
				</div>
			</div>
		</div>
		<div class="col-xs-12" id="material_monitoring">
		</div>
	</div>
</section>

<div class="modal fade" id="detailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #64b5f6;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Stock Condition By GMC</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-2" style="margin-bottom: 3%;">
						<select class="form-control select2" onchange="drawChart()" name="searchMaterial" id="searchMaterial" data-placeholder="Select Material" style="width: 100%;">
							<option></option>
							@foreach($materials as $material)
							<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
							@endforeach
						</select>
					</div>

					<div class="col-xs-12" id="material_monitoring_single">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="uploadModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('material')">MONITORED MATERIALS</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('policy')">STOCK POLICY</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('usage')">PLAN USAGE</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('inout')">MATERIAL IN/OUT</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('delivery')">DELIVERY PLAN</button>
					<button class="btn btn-warning" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('update_delivery')">UPDATE DELIVERY PLAN</button>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="detailModelChart">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header no-padding">
				<h3 style="background-color: #00c0ef; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
					CHOOSE CHART TYPE
				</h3>
			</div>
			<div class="modal-body table-responsive">

				<div class="col-xs-6">
					<button class="btn btn-default" id="line" style="width: 100%; font-weight: bold;" onclick="changeModelChart(id)"><i class="fa fa-line-chart" style="font-size: 5vw;"></i></button>
				</div>
				<div class="col-xs-6">
					<button class="btn btn-default" id="bar" style="width: 100%; font-weight: bold;" onclick="changeModelChart(id)"><i class="fa fa-bar-chart" style="font-size: 5vw;" disabled></i></button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="materialModal" style="overflow-y:auto; z-index: 10000;">
	<div class="modal-dialog" style="width:90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Monitored Material</h4>
				<span>Format Upload: [GMC][DESKRIPSI][PURCHASING GROUP][KODE VENDOR][NAMA VENDOR][KATEGORI][NIK BUYER][NIK CONTROL][REMARK]</span>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<textarea id="materialData" style="height: 100px; width: 100%;"></textarea>
				</div>
				<div>
					<button class="btn btn-success pull-right" onclick="uploadData('material');" style="width: 100%; margin-bottom: 10px; margin-top: 10px;">Upload</button>
				</div>
				<div>
					<table id="tableMaterial" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Material</th>
								<th style="width: 20%">Description</th>
								<th style="width: 5%">PGR</th>
								<th style="width: 30%">Vendor</th>
								<th style="width: 5%">Cetegory</th>
								<th style="width: 15%">BUYER</th>
								<th style="width: 15%">CONTROL</th>
								<th style="width: 5%">Remark</th>
							</tr>
						</thead>
						<tbody id="tableMaterialBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="policyModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Stock Policy</h4>
				<span>Format Upload: [GMC][DESKRIPSI][POLICY(DAY)][POLICY(QTY)]</span>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon bg-purple" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="policyPeriod" placeholder="Select Month" >
						</div>
						<textarea id="policyData" style="height: 100px; width: 100%; margin-top: 10px;"></textarea>
					</div>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('policy');">Upload</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="usageModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload MRP Usage</h4>
				<span>Format Upload: [GMC][TANGGAL][USAGE][REMARK]</span>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon bg-purple" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="usagePeriod" placeholder="Select Month" >
						</div>
						<textarea id="usageData" style="height: 100px; width: 100%; margin-top: 10px;"></textarea>
					</div>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('usage');">Upload</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="deliveryModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Plan Delivery</h4>
				<span>Format Upload: [ISSUE DATE][PO NUMBER][ITEM LINE][GMC][ETA YMPI][QUANTITY][REMARK]</span>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<textarea id="deliveryData" style="height: 100px; width: 100%;"></textarea>
					</div>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('delivery');">Upload</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="updateDeliveryModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update Plan Delivery</h4>
				<span>Format Upload: [PO NUMBER][ITEM LINE][GMC][TANGGAL]</span>[<span style="color: orange; font-weight: bold;">REVISI TANGGAL</span>][<span style="color: orange; font-weight: bold;">REVISI QUANTITY</span>]
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<textarea id="updateDeliveryData" style="height: 100px; width: 100%;"></textarea>
					</div>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('update_delivery');">Upload</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="inoutModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload In/Out Material</h4>
				@if($controlling_group == 'DIRECT' || $controlling_group == 'SUBCONT')
				<span>Format Upload: [GMC][MVT][ISSUE LOC][RECEIVE LOC][QUANTITY][ENTRY DATE][POSTING DATE]</span>
				@elseif($controlling_group == 'INDIRECT')
				<span>Format Upload: [GMC][MVT][ISSUE LOC][COST CENTER][QUANTITY][ENTRY DATE][POSTING DATE]</span>
				@endif
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<div class="row" style="padding-bottom: 5px;">
							<div class="col-xs-5">
								<div class="input-group date">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="inoutFrom" placeholder="Select Entry Date From">
								</div>
							</div>
							<div class="col-xs-5">
								<div class="input-group date">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="inoutTo" placeholder="Select Entry Date To">
								</div>
							</div>
						</div>
						<textarea id="inoutData" style="height: 100px; width: 100%;"></textarea>
					</div>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('inout');">Upload</button>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearData();
		fetchChart();
		setInterval(fetchChart, 1000 * 60 * 60);


		$('#period').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});

		$('#policyPeriod').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#usagePeriod').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#deliveryPeriod').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#inoutFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});

		$('#inoutTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});

	});

	var chart_type = 'line';

	function changeModelChart(id) {
		chart_type = id;
		$('#detailModelChart').modal('hide');

		if(chart_type == 'line'){
			$('.bar').hide();
			$('.line').show();

			$('#bar').prop('disabled', false);
			$('#line').prop('disabled', true);

			$('#bar').css('color', 'black');
			$('#line').css('color', 'grey');

		}else if(chart_type == 'bar'){
			$('.bar').show();
			$('.line').hide();

			$('#bar').prop('disabled', true);
			$('#line').prop('disabled', false);

			$('#bar').css('color', 'grey');
			$('#line').css('color', 'black');

		}
	}

	$('#detailModelChart').on('shown.bs.modal', function () {

		if(chart_type == 'line'){
			$('#bar').prop('disabled', false);
			$('#line').prop('disabled', true);

			$('#bar').css('color', 'black');
			$('#line').css('color', 'grey');


		}else if(chart_type == 'bar'){
			$('#bar').prop('disabled', true);
			$('#line').prop('disabled', false);

			$('#bar').css('color', 'grey');
			$('#line').css('color', 'black');

		}
	});

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#detailModal'),
			allowClear: true,
		});
	})

	$('#detailModal').on('hidden.bs.modal', function () {
		$('#material_monitoring_single').html("");
		$("#searchMaterial").prop('selectedIndex', 0).change();			
	});

	function clearData(){
		$('#materialData').val("");
		$('#policyData').val("");
		$('#usageData').val("");
		$('#deliveryData').val("");
		$('#updateDeliveryData').val("");
		$('#inoutData').val("");

		$('#policyPeriod').val("");
		$('#usagePeriod').val("");
		$('#deliveryPeriod').val("");
		$('#inoutFrom').val("");
		$('#inoutTo').val("");

		$('#materialModal').modal('hide');	
		$('#policyModal').modal('hide');	
		$('#usageModal').modal('hide');	
		$('#deliveryModal').modal('hide');	
		$('#updateDeliveryModal').modal('hide');	
		$('#inoutModal').modal('hide');	
	}

	function uploadData(id){
		$('#loading').show();
		var controlling_group = $('#controlling_group').val();

		if(id == 'material'){
			var upload = $('#materialData').val();
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload
			}	
		}
		else if(id == 'policy'){
			var upload = $('#policyData').val();
			var period = $('#policyPeriod').val();
			if(period == ""){
				alert('Data periode tidak boleh kosong');
				return false;
			}
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload,
				period:period
			}				
		}
		else if(id == 'usage'){
			var upload = $('#usageData').val();
			var period = $('#usagePeriod').val();
			if(period == ""){
				alert('Data periode tidak boleh kosong');
				return false;
			}
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload,
				period:period
			}			
		}
		else if(id == 'delivery'){
			var upload = $('#deliveryData').val();
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload,
			}			
		}
		else if(id == 'update_delivery'){
			var upload = $('#updateDeliveryData').val();
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload,
			}			
		}
		else if(id == 'inout'){
			var upload = $('#inoutData').val();
			var inoutFrom = $('#inoutFrom').val();
			var inoutTo = $('#inoutTo').val();
			var data = {
				id:id,
				controlling_group:controlling_group,
				upload:upload,
				inoutFrom:inoutFrom,
				inoutTo:inoutTo
			}			
		}
		else{
			alert('Unidentified Error');
		}

		if(upload == ""){
			alert('Data upload tidak boleh kosong');
			return false;
		}

		$.post('{{ url("upload/material/material_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();
				clearData();
				openSuccessGritter('Success!', result.message);
			}
			else{
				$('#loading').hide();
				alert(result.message);
				clearData();
			}
		});
	}

	function modalOpen(id){
		$('#uploadModal').modal('hide');
		if(id == 'material'){
			$('#materialModal').modal('show');
			$('#loading').show();

			var controlling_group = $('#controlling_group').val();
			var data = {
				controlling_group : controlling_group 
			}

			$.get('{{ url("fetch/material/material_control") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#loading').hide();

					var tableBody = "";
					$('#tableMaterialBody').html("");
					$('#tableMaterial').DataTable().clear();
					$('#tableMaterial').DataTable().destroy();

					$.each(result.material_control, function(key, value){
						tableBody += '<tr>';
						tableBody += '<td>'+value.material_number+'</td>';
						tableBody += '<td>'+value.material_description+'</td>';
						tableBody += '<td>'+value.controlling_group+'</td>';
						tableBody += '<td>'+value.vendor_code+'-'+value.vendor_name+'</td>';
						tableBody += '<td>'+value.controlling_group+'</td>';
						tableBody += '<td>'+value.buyer+'</td>';
						tableBody += '<td>'+value.control+'</td>';
						tableBody += '<td>'+value.remark+'</td>';
						tableBody += '</tr>';
					});
					$('#tableMaterialBody').append(tableBody);

					$('#tableMaterial').DataTable({
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
							},
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
						"processing": true
					});
				}
				else{
					$('#loading').hide();
					alert(result.message);
				}
			});
		}
		else if(id == 'policy'){
			$('#policyModal').modal('show');			
		}
		else if(id == 'usage'){
			$('#usageModal').modal('show');			
		}
		else if(id == 'delivery'){
			$('#deliveryModal').modal('show');			
		}
		else if(id == 'update_delivery'){
			$('#updateDeliveryModal').modal('show');			
		}
		else if(id == 'inout'){
			$('#inoutModal').modal('show');			
		}
		else{
			alert('Unidentified Error');
		}
	}

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

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

	function drawChart() {
		var material_number = $('#searchMaterial').val();

		if(material_number.length != ''){
			var data = {
				material_number:material_number
			}	

			$('#loading').show();
			$.get('{{ url("fetch/material/material_monitoring_single") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#loading').hide();

					var count_material = 0;
					var div_chart = "";
					$('#material_monitoring_single').html("");

					var now = new Date();

					$.each(result.material_percentages, function(key, value){

						div_chart += '<div class="col-xs-12" style="padding: 0 5px 0 5px;">';
						div_chart += '<div class="box box-solid" style="margin-bottom: 10px;">';

						div_chart += '<div class="box-header">';
						div_chart += '<div class="col-xs-8" style="padding: 0px; width: 70%;">';
						div_chart += '<span style="font-weight: bold; font-size: 1.1vw;">'+value.material_number+' '+value.material_description+'</span><br>';
						div_chart += '<span style="font-weight: bold; font-size: 1vw;">'+value.vendor_code+' - '+value.vendor_name+'</span><br>';
						div_chart += '<span style="font-weight: bold; font-size: 1vw;">Stock Policy : '+value.day +' Days ('+value.policy+' '+value.bun+')</span>';
						div_chart += '</div>';
						div_chart += '<div class="col-xs-3" style="padding: 0px; width: 20%;">';
						div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">YMPI Stock Condition : </span><br>';
						div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">WH Stock Condition : </span>';
						div_chart += '</div>';
						div_chart += '<div class="col-xs-1" style="padding: 0px; width: 10%;">';
						div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.ympipercentage+'%</p>';
						div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.percentage+'%</p>';
						div_chart += '</div>';
						div_chart += '</div>';

						div_chart += '<div class="box-body" style="padding: 10px 0 10px 0;">';
						div_chart += '<div style="height: 350px;" id="chart_single_'+value.material_number+'"></div>';
						div_chart += '</div>';

						div_chart += '</div>';
						div_chart += '</div>';
						$('#material_monitoring_single').append(div_chart);
						div_chart = "";

						var material_number = value.material_number;
						var stock_total = [];
						var stock_wip = [];
						var stock_mstk = [];
						var plan_usage = [];
						var plan_delivery = [];
						var plan_stock = [];
						var actual_usage = [];
						var actual_delivery = [];
						var stock_policy = [];
						var policy = value.policy;
						var next_policy = value.next_policy;
						var percentage = 0;
						var stock_percentage = [];
						var yAxis = value.bun;

						var col = 0;

						for(var i = 0; i < result.results.length; i++){
							if(result.results[i].material_number == material_number){
								col++;

								stock_total.push(parseFloat(result.results[i].stock_total));
								stock_mstk.push(parseFloat(result.results[i].stock_mstk));
								stock_wip.push(parseFloat(result.results[i].stock_wip));

								if(col >= result.count_next){
									stock_policy.push(parseFloat(next_policy));	
								}else{
									stock_policy.push(parseFloat(policy));	
								}
								
								plan_usage.push(parseFloat(result.results[i].plan_usage));
								plan_delivery.push(parseFloat(result.results[i].plan_delivery));
								actual_usage.push(parseFloat(result.results[i].actual_usage));
								actual_delivery.push(parseFloat(result.results[i].actual_delivery));
								plan_stock.push(parseFloat(result.results[i].plan_stock));

								if(result.results[i].stock_total > 0){
									percentage = (parseFloat(result.results[i].stock_total)/parseFloat(policy))*100;
								}else{
									percentage = (parseFloat(result.results[i].plan_stock)/parseFloat(policy))*100;
								}
								stock_percentage.push((parseFloat(percentage)).toFixed(2));
							}
						}

						var chart_name = 'chart_single_'+value.material_number;

						Highcharts.chart(chart_name, {
							chart: {
								backgroundColor	: null
							},
							title: {
								text: null
							},
							credits: {
								enabled: false
							},
							xAxis: {
								tickInterval: 1,
								gridLineWidth: 1,
								categories: result.categories,
								crosshair: true,
								plotBands:[{
									from: result.count_now-1.5,
									to: result.count_now-0.5,
									color: 'rgba(68, 170, 213, .2)',
									label: {
										text: 'Today',
										style: {
											color: '#999999'
										},
										y: 20
									}
								}]
							},
							yAxis: [{
								title: {
									text: yAxis
								}
							}],
							legend: {
								align: 'right',
								verticalAlign: 'top',
								layout: 'vertical',
								x: 0,
								y: 100,
								symbolRadius: 1,
								borderWidth: 1
							},
							tooltip: {
								headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
								pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
								'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
								footerFormat: '</table>',
								shared: true,
								useHTML: true
							},
							plotOptions: {
								column: {
									stacking: 'normal',
									pointPadding: 0.93,
									groupPadding: 0.93,
									borderWidth: 0.8,
									borderColor: '#212121'
								}
							},
							series: [
							{
								name: 'Stock Policy',
								type: 'area',
								marker:{
									enabled:false
								},
								lineColor: 'red',
								color: 'RGBA(255,0,0,0.05)',
								data: stock_policy,
								dashStyle: 'shortdash'
							}, {
								name: 'Plan Delivery',
								type: 'column',
								data: plan_delivery,
								color: '#64b5f6'
							}, {
								name: 'Plan Stock',
								type: 'column',
								data: plan_stock,
								color: '#757575'
							}, {
								name: 'Actual Delivery',
								type: 'column',
								stack: 'Stock',
								data: actual_delivery,
								color: '#fff176'
							}, {
								name: 'Actual WIP',
								type: 'column',
								stack: 'Stock',
								data: stock_wip,
								color: '#dcedc8'
							}, {
								name: 'Actual WH',
								type: 'column',
								stack: 'Stock',
								data: stock_mstk,
								color: '#4caf50'
							}, {
								name: 'Plan Usage',
								type: 'spline',
								data: plan_usage,
								dashStyle: 'shortdash',
								color: '#212121'
							}, {
								name: 'Actual Usage',
								type: 'spline',
								data: actual_usage,
								color: '#f57f17'
							}]
						});

					});

}else{
	$('#loading').hide();
	alert(result.message);
}
});
}
}

function fetchChart(id){
	$('#loading').show();
	var controlling_group = $('#controlling_group').val();
	var period = $('#period').val();
	var data = {
		controlling_group:controlling_group,
		period:period
	}
	$.get('{{ url("fetch/material/material_monitoring") }}', data, function(result, status, xhr) {
		if(result.status){

			$('#title_text').text('Stock Condition on '+result.period+' ('+result.count_item+' item(s) Under Stock Policy)');
			var h = $('#period_title').height();
			$('#period').css('height', h);
			$('#btnUpload').css('height', h);
			$('#btnDetail').css('height', h);
			$('#btnModelChart').css('height', h);

			var count_material = 0;
			var div_chart = "";
			$('#material_monitoring').html("");

			if(result.material_percentages.length == 0){
				alert('Data pada periode tersebut belum di update atau ditambahkan');
				$('#loading').hide();
				return false;
			}

			var now = new Date();

			$.each(result.material_percentages, function(key, value){
				count_material++;

				//Bar Chart
				div_chart += '<div class="col-xs-6 bar" style="padding: 0 5px 0 5px;">';
				div_chart += '<div class="box box-solid" style="margin-bottom: 10px;">';

				div_chart += '<div class="box-header">';
				div_chart += '<div class="col-xs-1" style="padding: 0px; width: 4%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1.2vw;">'+count_material+')</span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-7" style="padding: 0px; width: 62%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1.1vw;">'+value.material_number+' '+value.material_description+'</span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;">'+value.vendor_code+' - '+value.vendor_name+'</span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;">Stock Policy : '+value.day +' Days ('+value.policy+' '+value.bun+')</span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-3" style="padding: 0px; width: 24%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">YMPI Stock Condition : </span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">WH Stock Condition : </span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-1" style="padding: 0px; width: 10%;">';
				div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.ympipercentage+'%</p>';
				div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.percentage+'%</p>';
				div_chart += '</div>';
				div_chart += '</div>';

				div_chart += '<div class="box-body" style="padding: 10px 0 10px 0;">';
				div_chart += '<div style="height: 350px;" id="chart_'+value.material_number+'"></div>';
				div_chart += '</div>';

				div_chart += '</div>';
				div_chart += '</div>';


				//Line Chart
				div_chart += '<div class="col-xs-6 line" style="padding: 0 5px 0 5px;">';
				div_chart += '<div class="box box-solid" style="margin-bottom: 10px;">';

				div_chart += '<div class="box-header">';
				div_chart += '<div class="col-xs-1" style="padding: 0px; width: 4%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1.2vw;">'+count_material+')</span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-7" style="padding: 0px; width: 62%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1.1vw;">'+value.material_number+' '+value.material_description+'</span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;">'+value.vendor_code+' - '+value.vendor_name+'</span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;">Stock Policy : '+value.day +' Days ('+value.policy+' '+value.bun+')</span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-3" style="padding: 0px; width: 24%;">';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">YMPI Stock Condition : </span><br>';
				div_chart += '<span style="font-weight: bold; font-size: 1vw;" class="pull-right">WH Stock Condition : </span>';
				div_chart += '</div>';
				div_chart += '<div class="col-xs-1" style="padding: 0px; width: 10%;">';
				div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.ympipercentage+'%</p>';
				div_chart += '<p style="font-weight:bold; font-size:1vw; color:red; text-align:right; margin:0px;">'+value.percentage+'%</p>';
				div_chart += '</div>';
				div_chart += '</div>';

				div_chart += '<div class="box-body" style="padding: 10px 0 10px 0;">';
				div_chart += '<div style="height: 350px;" id="chart_line_'+value.material_number+'"></div>';
				div_chart += '</div>';

				div_chart += '</div>';
				div_chart += '</div>';


				$('#material_monitoring').append(div_chart);

				div_chart = "";

				var material_number = value.material_number;
				var stock_total = [];
				var stock_wip = [];
				var stock_wh = [];
				var plan_usage = [];
				var plan_delivery = [];
				var plan_stock = [];
				var actual_usage = [];
				var actual_delivery = [];
				var stock_policy = [];
				var policy = value.policy;
				var next_policy = value.next_policy;
				var percentage = 0;
				var stock_percentage = [];
				var yAxis = value.bun;

				var line_actual_stock = [];

				var col = 0;



				for(var i = 0; i < result.results.length; i++){
					if(result.results[i].material_number == material_number){
						col++;

						stock_total.push(parseFloat(result.results[i].stock_total));
						stock_wh.push(parseFloat(result.results[i].stock_wh));
						stock_wip.push(parseFloat(result.results[i].stock_wip));

						if(col >= result.count_next){
							stock_policy.push(parseFloat(next_policy));	
						}else{
							stock_policy.push(parseFloat(policy));	
						}

						plan_usage.push(parseFloat(result.results[i].plan_usage));
						plan_delivery.push(parseFloat(result.results[i].plan_delivery));
						actual_usage.push(parseFloat(result.results[i].actual_usage));
						actual_delivery.push(parseFloat(result.results[i].actual_delivery));
						plan_stock.push(parseFloat(result.results[i].plan_stock));
						if(result.results[i].stock_total > 0){
							percentage = (parseFloat(result.results[i].stock_total)/parseFloat(policy))*100;
						}
						else{
							percentage = (parseFloat(result.results[i].plan_stock)/parseFloat(policy))*100;
						}
						stock_percentage.push((parseFloat(percentage)).toFixed(2));


						line_actual_stock.push(parseFloat(result.results[i].stock_total));

					}
				}

				var chart_name = 'chart_'+value.material_number;

				Highcharts.chart(chart_name, {
					chart: {
						backgroundColor	: null
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: result.categories,
						crosshair: true,
						plotBands:[{
							from: result.count_now-1.5,
							to: result.count_now-0.5,
							color: 'rgba(68, 170, 213, .2)',
							label: {
								text: 'Today',
								style: {
									color: '#999999'
								},
								y: 20
							}
						}]
					},
					yAxis: [{
						title: {
							text: yAxis
						}
					}],
					legend: {
						align: 'right',
						verticalAlign: 'top',
						layout: 'vertical',
						x: 0,
						y: 100,
						symbolRadius: 1,
						borderWidth: 1
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121'
						}
					},
					series: [
					{
						name: 'Stock Policy',
						type: 'area',
						marker:{
							enabled:false
						},
						lineColor: 'red',
						color: 'RGBA(255,0,0,0.05)',
						data: stock_policy,
						dashStyle: 'shortdash'
					}, {
						name: 'Plan Delivery',
						type: 'column',
						data: plan_delivery,
						color: '#64b5f6'
					}, {
						name: 'Plan Stock',
						type: 'column',
						data: plan_stock,
						color: '#757575'
					}, {
						name: 'Actual Delivery',
						type: 'column',
						stack: 'Stock',
						data: actual_delivery,
						color: '#fff176'
					}, {
						name: 'Actual WIP',
						type: 'column',
						stack: 'Stock',
						data: stock_wip,
						color: '#dcedc8'
					}, {
						name: 'Actual WH',
						type: 'column',
						stack: 'Stock',
						data: stock_wh,
						color: '#4caf50'
					}, {
						name: 'Plan Usage',
						type: 'spline',
						data: plan_usage,
						dashStyle: 'shortdash',
						color: '#212121'
					}, {
						name: 'Actual Usage',
						type: 'spline',
						data: actual_usage,
						color: '#f57f17'
					}]
				});

				var chart_name = 'chart_line_'+value.material_number;

				Highcharts.chart(chart_name, {
					chart: {
						backgroundColor	: null
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: result.categories,
						crosshair: true,
						plotBands:[{
							from: result.count_now-1.5,
							to: result.count_now-0.5,
							color: 'rgba(68, 170, 213, .2)',
							label: {
								text: 'Today',
								style: {
									color: '#999999'
								},
								y: 20
							}
						}]
					},
					yAxis: [{
						title: {
							text: yAxis
						}
					}],
					legend: {
						align: 'right',
						verticalAlign: 'top',
						layout: 'vertical',
						x: 0,
						y: 100,
						symbolRadius: 1,
						borderWidth: 1
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: false,
						useHTML: true
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121'
						}
					},
					series:
					[{
						name: 'Stock Policy',
						type: 'spline',
						marker:{
							enabled:false
						},
						lineColor: 'red',
						color: 'RGBA(255,0,0,0.05)',
						data: stock_policy,
						dashStyle: 'shortdash'
					},{
						name: 'Total Stock',
						type: 'spline',
						stack: 'Stock',
						data: stock_total,
						marker: {
							enabled: false
						},
						color: '#00a307'
					},{
						name: 'Plan Stock',
						type: 'spline',
						dashStyle: 'shortdash',
						data: plan_stock,
						marker: {
							enabled: false
						},
						color: '#00a307'
					},{
						name: 'Actual Usage',
						type: 'spline',
						data: actual_usage,
						marker: {
							enabled: false
						},
						color: '#fab06e'
					},{
						name: 'Plan Usage',
						type: 'spline',
						dashStyle: 'shortdash',
						data: plan_usage,
						marker: {
							enabled: false
						},
						color: '#fab06e'
					},{
						name: 'Actual Delivery',
						type: 'spline',
						stack: 'Stock',
						data: actual_delivery,
						marker: {
							enabled: false
						},
						color: '#64b5f6'
					},{
						name: 'Plan Delivery',
						type: 'spline',
						dashStyle: 'shortdash',
						data: plan_delivery,
						marker: {
							enabled: false
						},
						color: '#64b5f6'
					}]
				});

				$('#loading').hide();
			});

if(chart_type == 'line'){
	console.log(chart_type);
	$('.bar').hide();
	$('.line').show();

	$('#bar').prop('disabled', false);
	$('#line').prop('disabled', true);

	$('#bar').css('color', 'black');
	$('#line').css('color', 'grey');

}else if(chart_type == 'bar'){
	console.log(chart_type);
	$('.bar').show();
	$('.line').hide();

	$('#bar').prop('disabled', true);
	$('#line').prop('disabled', false);

	$('#bar').css('color', 'grey');
	$('#line').css('color', 'black');

}

}
else{
	$('#loading').hide();
	alert(result.message);
}
});
}

</script>
@endsection