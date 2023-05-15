@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
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
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Knock Downs Tracer <span class="text-purple">KD完成品追跡</span>
		<small>Filters <span class="text-purple">フィルター</span></small>
	</h1>
	<ol class="breadcrumb" id="last_update"></ol>
</section>
@stop
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	<p style="position: absolute; color: White; top: 45%; left: 45%;">
		<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
	</p>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Detail Filters <span class="text-purple">フィルター詳細</span></span></h3>
				</div>
				<div class="box-body">
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="col-md-6">
									<div class="form-group">
										<label>Prod. Date From</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="prodFrom" name="prodFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Prod. Date To</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="prodTo" name="prodTo">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Deliv. FSTK From</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="fstkFrom" name="fstkFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Deliv. FSTK To</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="fstkTo" name="fstkTo">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Ship. Date From</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="shipFrom" name="shipFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Ship. Date To</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="shipTo" name="shipTo">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="form-group">
									<label>Origin Group</label>
									<select class="form-control select2" multiple="multiple" name="originGroup" id="originGroup" data-placeholder="Select Origin Group" style="width: 100%;">
										<option></option>
										@foreach($origin_groups as $origin_group)
										<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label>Material Number</label>
									<select class="form-control select2" multiple="multiple" name="materialNumber" id="materialNumber" data-placeholder="Select Material Number" style="width: 100%;">
										<option></option>
										@foreach($materials as $material)
										<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label>Packing Location</label>
									<select class="form-control select2" multiple="multiple" name="hpl" id="hpl" data-placeholder="Select Material Number" style="width: 100%;">
										<option></option>
										@php
										$location = array();
										@endphp
										@foreach($materials as $material)
										@if(!in_array($material->hpl, $location))
										<option value="{{ $material->hpl }}">{{ $material->hpl }}</option>
										@php
										array_push($location, $material->hpl);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="form-group">
									<label>KDO Number</label>
									<input type="text" class="form-control" name="kdoNumber" id="kdoNumber" placeholder="Enter KDO Number">
								</div>
								<div class="form-group">
									<label>Invoice Number</label>
									<input type="text" class="form-control" name="invoiceNumber" id="invoiceNumber" placeholder="Enter Invoice Number">
								</div>
								<div class="form-group">
									<label>Destination</label>
									<select class="form-control select2" multiple="multiple" name="destination" id="destination" data-placeholder="Select Destination" style="width: 100%;">
										<option></option>
										@foreach($destinations as $destination)
										<option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} ({{ $destination->destination_shortname }})</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button  type="submit" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
						</div>
					</div>
					<div class="col-md-12">
						<table id="traceabilityTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">KDO</th>
									<th style="width: 1%;">Material</th>
									<th style="width: 1%;">Description</th>
									<th style="width: 1%;">Qty</th>
									<th style="width: 1%;">Prod Date</th>
									<th style="width: 1%;">Deliv Date</th>
									<th style="width: 1%;">ST Plan</th>
									<th style="width: 1%;">BL Plan</th>
									<th style="width: 1%;">I/V</th>
									<th style="width: 1%;">Dest.</th>
									<th style="width: 1%;">SO</th>
									<th style="width: 1%;">Stats</th>
								</tr>
							</thead>
							<tbody id="tableBody">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#prodFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#prodTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#shipFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#shipTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#delivFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#delivTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
	});

	function clearConfirmation(){
		location.reload(true);
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillTable(){
		$('#loading').modal('show');
		var prodFrom = $('#prodFrom').val();
		var prodTo = $('#prodTo').val();
		var shipFrom = $('#shipFrom').val();
		var shipTo = $('#shipTo').val();
		var delivFrom = $('#blFrom').val();
		var delivTo = $('#blTo').val();
		var originGroup = $('#originGroup').val();
		var materialNumber = $('#materialNumber').val();
		var hpl = $('#hpl').val();
		var kdoNumber = $('#kdoNumber').val();
		var invoiceNumber = $('#invoiceNumber').val();
		var destination = $('#destination').val();
		var data = {
			prodFrom:prodFrom,
			prodTo:prodTo,
			shipFrom:shipFrom,
			shipTo:shipTo,
			delivFrom:delivFrom,
			delivTo:delivTo,
			originGroup:originGroup,
			materialNumber:materialNumber,
			hpl:hpl,
			kdoNumber:kdoNumber,
			invoiceNumber:invoiceNumber,
			destination:destination,
		}
		$.get('{{ url("fetch/kd_traceability") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#traceabilityTable').DataTable().clear();
					$('#traceabilityTable').DataTable().destroy();
					$('#tableBody').html("");
					var tableData = '';
					$.each(result.knock_down_details, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.kd_number +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '<td>'+ value.deliv +'</td>';
						tableData += '<td>'+ value.st_date +'</td>';
						tableData += '<td>'+ value.bl_date +'</td>';
						tableData += '<td>'+ value.invoice_number +'</td>';
						tableData += '<td>'+ value.destination_shortname +'</td>';
						tableData += '<td>'+ value.sales_order +'</td>';

						if(value.status == 0){
							tableData += '<td>Production</td>';
						}
						else if(value.status == 1){
							tableData += '<td>Production</td>';
						}
						else if(value.status == 2){
							tableData += '<td>WH FSTK</td>';
						}
						else if(value.status == 3){
							tableData += '<td>Stuffing</td>';
						}
						else if(value.status == 4){
							tableData += '<td>Exported</td>';
						}
						else{
							tableData += '<td>Undefined</td>';
						}
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);
					$('#traceabilityTable').DataTable({
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

					$('#loading').modal('hide');
				}
				else{
					$('#loading').modal('hide');
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				$('#loading').modal('hide');
				alert('Disconnected from server');
			}
		});
	}

	function downloadAtt(id){
		var data = {
			container_id:id
		}
		$.get('{{ url("download/att_container_departure") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
			// $('#modalFooter').html("<a href='" + result.file_path + "'>Download</a>");
			document.location.href = (result.file_path);
		}
		else{
			alert('Disconnected from server');
		}
	});
	}
</script>
@endsection