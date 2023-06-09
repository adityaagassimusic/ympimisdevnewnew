@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
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

	.buttonclass {
		top: 0;
		left: 0;
		transition: all 0.15s linear 0s;
		position: relative;
		display: inline-block;
		padding: 15px 25px;
		background-color: #ffe800;
		text-transform: uppercase;
		color: #404040;
		font-family: arial;
		letter-spacing: 1px;
		box-shadow: -6px 6px 0 #404040;
		text-decoration: none;
		cursor: pointer;
	}
	.buttonclass:hover {
		top: 3px;
		left: -3px;
		box-shadow: -3px 3px 0 #404040;
		color: white
	}
	.buttonclass:hover::after {
		top: 1px;
		left: -2px;
		width: 4px;
		height: 4px;
	}
	.buttonclass:hover::before {
		bottom: -2px;
		right: 1px;
		width: 4px;
		height: 4px;
	}
	.buttonclass::after {
		transition: all 0.15s linear 0s;
		content: "";
		position: absolute;
		top: 2px;
		left: -4px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg);
		z-index: -1;
	}
	.buttonclass::before {
		transition: all 0.15s linear 0s !important;
		content: "";
		position: absolute;
		bottom: -4px;
		right: 2px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg) !important;
		z-index: -1 !important;
	}

	a.buttonclass {
		position: relative;
	}

	a:active.buttonclass {
		top: 6px;
		left: -6px;
		box-shadow: none;
	}
	a:active.buttonclass:before {
		bottom: 1px;
		right: 1px;
	}
	a:active.buttonclass:after {
		top: 1px;
		left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		History Request Material<span class="text-purple">??</span>
		<!-- <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#importExcel">
			<i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;
			Upload Excel
		</button> -->
		<!-- <a class="buttonclass">
			button
		</a> -->
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
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
	<div class="alert alert-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4> Warning!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<h4>Filter</h4>
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

						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/report/mod_file') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="row" id="divTable">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body">
				Are you sure delete?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Upload Temperature</h4>
				</div>
				<div class="modal-body">
					Upload Excel file here:<span class="text-red">*</span>
					<input type="file" name="file" id="file" required>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success">Upload</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modalViewHis">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL KANBAN REQUEST</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_history" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Kode Request</th>
									<th style="width: 1%;">No Kanban</th>
									<th style="width: 1%;">SlocName</th>
									<th style="width: 1%;">GMC</th>
									<th style="width: 4%;">Description</th>
									<th style="width: 1%;">lot</th>
									<th style="width: 1%;">uom</th>
									<th style="width: 1%;">Quantity Request</th>
									<th style="width: 2%;">Status</th>
								</tr>
							</thead>
							<tbody id="detail_material_body_history">
							</tbody>
						</table>
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
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var detail_history = [];

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();
		// fetchHistory();


		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	$("form#importForm").submit(function(e) {
		if ($('#file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("import/temperature/minmoe") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.message){
					$("#loading").hide();
					$("#file").val('');
					fillList();
					$('#importExcel').modal('hide');
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	


	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th>Tanggal</th>';
		tableData += '<th>Waktu</th>';
		tableData += '<th>PIC Request</th>';
		tableData += '<th>Departement</th>';
		tableData += '<th>No MOD</th>';
		tableData += '<th>Kode Request</th>';
		tableData += '<th>Count Kanban</th>';
		tableData += '<th>Action</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		tableData += "<tfoot>";
		tableData += "<tr>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "</tr>";
		tableData += "</tfoot>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}


	function check_material(kode_request){
		if(confirm("Apakah anda sudah yakin?")){

			data = {
				kode_request : kode_request,
			} 

			$("#loading").show();
			$.post('{{ url("post/update/material") }}', data, function(result, status, xhr){
				if(result.status){ 
					$("#loading").hide(); 
					location.reload();
					openSuccessGritter('Success', result.message);

				}
				else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			return false;
		}
	}


	function fillList(){
		
		$('#loading').show();
		var tanggal_from = $('#tanggal_from').val();
		var tanggal_to = $('#tanggal_to').val();

		var data = {
			tanggal_from:tanggal_from,
			tanggal_to:tanggal_to
		}


		$.get('{{ url("fetch/history/request1") }}',data, function(result, status, xhr){
			if(result.status){
				initiateTable();
				fetchHistory();
				var tableData = "";
				var css = "";
				var btn_check = "";
				var btn_excel = "";
				
				$.each(result.datas, function(key, value) {
					// console.log('{{ Auth::user()->role_code }}');
					if ('{{ Auth::user()->role_code }}' == "MIS" || '{{ Auth::user()->role_code }}' == "F-SPL" || '{{ Auth::user()->role_code }}' == "OP-WH-Exim" || '{{ Auth::user()->role_code }}' == "F" || '{{ Auth::user()->username }}' == "PI1710002") {
	
					if (value.status == "sudah dicheck") {
						css = 'style="background-color: #58e885;"';
						btn_check = '';
						btn_excel = '<a class="btn btn-success btn-xs" id="kodes" href="{{ url("export/request/mod") }}/'+value.kode_request+'"><i class="fa fa-download">Export MOD</i> </a>';

					}else{
						css = "";
						btn_check = '<a class="btn btn-warning btn-xs" id="kode" onclick="check_material(\''+value.kode_request+'\')"><i class="fa fa-eye"></i> Check</a>';
						btn_excel = '<a class="btn btn-success btn-xs" id="kodes" href="{{ url("export/request/mod") }}/'+value.kode_request+'"><i class="fa fa-download">Export MOD</i> </a>';

					}
				}
					
					tableData += '<tr '+css+'>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td>'+ value.times +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.dep +'</td>';
					tableData += '<td>'+ value.loc +''+ value.kode_request +'</td>';
					tableData += '<td>'+ value.kode_request +'</td>';
					tableData += '<td>'+ value.total +'</td>';
					tableData += '<td><a id ="report" href="{{ url("fetch/request/produksi") }}/'+value.kode_request+'" class="btn btn-danger btn-xs" target="_blank"><i class="fa fa-file-pdf-o"></i> Report MOD</a> <a class="btn btn-info btn-xs" id="kode" onclick="detail_historys(\''+value.kode_request+'\')" style="border-color: green;"><i class="fa fa-eye"></i> Detail</a> '+btn_excel+' '+btn_check+'</td>';
					tableData += '</tr>';
				});
				$('#example1Body').append(tableData);

				var table = $('#example1').DataTable({
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
				
				

				$('#example1 tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				table.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#example1 tfoot tr').appendTo('#example1 thead');
				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function fetchHistory() {
		$.get('{{ url("fetch/history/detail") }}', function(result, status, xhr){
			if (result.status) {
				detail_history = [];
				for (var i = 0; i < result.detail_request_history.length; i++) {
					detail_history.push({id: result.detail_request_history[i].id,kode_request:result.detail_request_history[i].kode_request, gmc: result.detail_request_history[i].gmc,description: result.detail_request_history[i].description,lot: result.detail_request_history[i].lot,sloc_name: result.detail_request_history[i].sloc_name,qty_req: result.detail_request_history[i].qty_req, qty_kirim: result.detail_request_history[i].qty_kirim,remark: result.detail_request_history[i].remark,no_hako: result.detail_request_history[i].no_hako,uom: result.detail_request_history[i].uom});
				}
			}
		});
	}

	function detail_historys(kode_request){
		$('#modalViewHis').modal('show');

		var tableData = "";
		var num=1;

		$('#detail_material_history').DataTable().clear();
		$('#detail_material_history').DataTable().destroy();
		$('#detail_material_body_history').html("");

		$.each(detail_history, function(key, value){
			if (value.kode_request == kode_request) {
				tableData += '<tr>';
				tableData += '<td>'+ value.kode_request +'</td>';
				tableData += '<td>'+ value.no_hako +'</td>';
				tableData += '<td>'+ value.sloc_name +'</td>';
				tableData += '<td>'+ value.gmc +'</td>';
				tableData += '<td>'+ value.description +'</td>';
				tableData += '<td>'+ value.lot +'</td>';
				tableData += '<td>'+ value.uom +'</td>';
				tableData += '<td>'+ value.qty_kirim +'</td>';
				tableData += '<td>'+ value.remark +'</td>';
				tableData += '</tr>';
			// no++;
		}
	});

		$('#detail_material_body_history').append(tableData);

		var table = $('#detail_material_history').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
			[ 7, 25, 50, -1 ],
			[ '7 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
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
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'pageLength': 25,
			'searching': true ,
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