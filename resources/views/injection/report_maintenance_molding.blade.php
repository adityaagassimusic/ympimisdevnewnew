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

	.btn_less_more {
	  background: none!important;
	  border: none;
	  padding: 0!important;
	  font-size: 12px;
	  /*optional*/
	  font-family: arial, sans-serif;
	  /*input has OS specific font-family*/
	  color: #069;
	  cursor: pointer;
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
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
									<input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_maintenance_molding') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="row" style="overflow-x: scroll;">
							<table id="tableMaintenanceMolding" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">Date</th>
										<th width="3%">PIC</th>
										<th width="1%">Product</th>
										<th width="2%">Part</th>
										<th width="2%">Last Shot</th>
										<th width="2%">Status</th>
										<th width="2%">Start</th>
										<th width="2%">End</th>
										<th width="2%">Mtc Duration</th>
										<th width="2%">Pause</th>
										<th width="2%">Reason Pause</th>
										<th width="2%">Note</th>
									</tr>
								</thead>
								<tbody id="bodyTableMaintenanceMolding">
								</tbody>
								<!-- <tfoot> -->
									<!-- <tr style="color: black">
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr> -->
								<!-- </tfoot> -->
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
		}
		$.get('{{ url("fetch/injection/report_maintenance_molding") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMaintenanceMolding').DataTable().clear();
				$('#tableMaintenanceMolding').DataTable().destroy();
				$('#bodyTableMaintenanceMolding').html("");
				var tableData = "";
				$.each(result.maintenance, function(key, value) {
					var duration_total = parseFloat(value.duration);
					var duration_pause = 0;
					var reason_pause = [];
					for(var i = 0; i < result.dataworkall.length;i++){
						var dataone = result.dataworkall[i].split("+");
						if (dataone[0] == value.maintenance_code) {
							if (dataone[1] == 'PAUSE') {
								duration_total = duration_total - parseFloat(dataone[4]);
								duration_pause = duration_pause + parseFloat(dataone[4]);
								reason_pause.push(dataone[5]+" "+dataone[4]+" Menit");
							}
						}
					}
					tableData += '<tr>';
					tableData += '<td>'+ value.dates +'</td>';
					tableData += '<td>'+ value.pic +'</td>';
					tableData += '<td>'+ value.product +'</td>';
					tableData += '<td>'+ value.part +'</td>';
					tableData += '<td>'+ value.last_counter +'</td>';
					tableData += '<td>'+ value.status +'</td>';
					tableData += '<td>'+ value.start_time +'</td>';
					tableData += '<td>'+ value.end_time +'</td>';
					tableData += '<td>'+ duration_total.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_pause.toFixed(2) +'</td>';
					tableData += '<td>'+ reason_pause.join(', ') +'</td>';
					tableData += '<td>'+ (value.note || '') +'</td>';
					tableData += '</tr>';
				});
				$('#bodyTableMaintenanceMolding').append(tableData);

				// $('#tableMaintenanceMolding tfoot th').each(function(){
				// 	var title = $(this).text();
				// 	$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				// });
				
				var table = $('#tableMaintenanceMolding').DataTable({
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

				// table.columns().every( function () {
				// 	var that = this;

				// 	$( 'input', this.footer() ).on( 'keyup change', function () {
				// 		if ( that.search() !== this.value ) {
				// 			that
				// 			.search( this.value )
				// 			.draw();
				// 		}
				// 	} );
				// } );

				// $('#tableMaintenanceMolding tfoot tr').appendTo('#tableMaintenanceMolding thead');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function readMore(index) {
		$('#less_'+index).hide();
		$('#more_'+index).show();
	}

	function readLess(index) {
		$('#less_'+index).show();
		$('#more_'+index).hide();
	}


</script>
@endsection