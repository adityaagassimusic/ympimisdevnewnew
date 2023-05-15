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

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<!-- <h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
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
									<input type="text" class="form-control datepicker" id="tanggal_to"name="tanggal_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Location</span>
							<div class="form-group">
								<select class="form-control select2" name="location" id="location" data-placeholder="Select Location" style="width: 100%;">
									<option></option>
									<option value="RC11">RC11</option>
									<option value="RC91">RC91</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Status</span>
							<div class="form-group">
								<select class="form-control select2" name="status" id="status" data-placeholder="Select Status" style="width: 100%;">
									<option></option>
									<option value="IN">IN</option>
									<option value="IN">OUT</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_setup_molding') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div> -->
					<div class="col-xs-12">
						<div class="row">
							<span>BY OPERATOR MOLDING</span>
							<table id="tableMolding" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="3%">Date Injeksi</th>
										<th width="1%">Mesin</th>
										<th width="1%">Molding</th>
										<th width="1%">NG Rate</th>
										<th width="1%">PIC Molding</th>
									</tr>
								</thead>
								<tbody id="bodyTableMolding">
								</tbody>
							</table>
						</div>
					</div>

					<div class="col-xs-12">
						<div class="row">
							<span>BY RESIN</span>
							<table id="tableResin" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="3%">Date Injeksi</th>
										<th width="1%">Mesin</th>
										<th width="1%">Molding</th>
										<th width="1%">NG Rate</th>
										<th width="1%">PIC Molding</th>
										<th width="1%">Resin</th>
									</tr>
								</thead>
								<tbody id="bodyTableResin">
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
		$('.select2').select2();
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

		var data = {
			tanggal_from:$('#tanggal_from').val(),
			tanggal_to:$('#tanggal_to').val(),
			location:$('#location').val(),
			status:$('#status').val(),
		}
		$.get('{{ url("fetch/recorder/ng_rate/data") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMolding').DataTable().clear();
				$('#tableMolding').DataTable().destroy();
				$('#bodyTableMolding').html("");
				var tableData = "";
				var data_operator = [];
				var molding = [];
				$.each(result.molding, function(key, value) {
					var dates = value.date_injeksi.split(',');
					for(var i = 0; i < dates.length;i++){
						tableData += '<tr>';
						tableData += '<td>'+dates[i] +'</td>';
						tableData += '<td>'+ value.mesin+'</td>';
						tableData += '<td>'+ value.molding+'</td>';
						tableData += '<td>'+ value.ng_rate+'</td>';
						tableData += '<td>'+ value.person+'</td>';
						tableData += '</tr>';
					}
				});
				$('#bodyTableMolding').append(tableData);

				// var molding_unik = molding.filter(onlyUnique);

				// $('#headPivot1').html('');
				// var headpivot1 = '';
				// headpivot1 += '<tr>';
				// headpivot1 += '<th>Molding</th>';
				// for(var i = 0; i < operator_unik.length;i++){
				// 	headpivot1 += '<th>'+operator_unik[i]+'</th>';
				// }
				// headpivot1 += '</tr>';

				// $('#headPivot1').append(headpivot1);

				// $('#bodyTablePivot1').html('');
				// var bodypiv1 = '';

				// for(var j = 0; j < molding_unik.length;j++){
				// 	bodypiv1 += '<tr>';
				// 	bodypiv1 += '<td>'+ molding_unik[j]+'</td>';
				// 	for(var k = 0; k < operator_unik.length;k++){
				// 		for(var l = 0; l < result.molding.length;l++){
				// 			if (result.molding[l].molding == molding_unik[k] && result.molding[l].person == operator_unik[k]) {
				// 				bodypiv1 += '<td>'+ result.molding[l].mesin+'</td>';
				// 			}
				// 		}
				// 	}
				// 	bodypiv1 += '</tr>';
				// }

				// $('#bodyTablePivot1').append(bodypiv1);

				
				var table = $('#tableMolding').DataTable({
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

				$('#tableResin').DataTable().clear();
				$('#tableResin').DataTable().destroy();
				$('#bodyTableResin').html("");
				var tableData = "";
				$.each(result.resin, function(key, value) {
					var dates = value.date_injeksi.split(',');
					for(var i = 0; i < dates.length;i++){
						tableData += '<tr>';
						tableData += '<td>'+dates[i] +'</td>';
						tableData += '<td>'+ value.mesin+'</td>';
						tableData += '<td>'+ value.molding+'</td>';
						tableData += '<td>'+ value.ng_rate+'</td>';
						tableData += '<td>'+ value.person+'</td>';
						tableData += '<td>'+ value.resin+'</td>';
						tableData += '</tr>';
					}
				});
				$('#bodyTableResin').append(tableData);
				
				var table = $('#tableResin').DataTable({
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

function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

</script>
@endsection