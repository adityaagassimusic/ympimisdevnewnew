@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
<style type="text/css">
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
		padding: 0;
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
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
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
					<!-- <h4>Filter</h4> -->
					<div class="row">
						<!-- <div class="col-md-4 col-md-offset-2">
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
									<a href="{{ url('index/mirai_mobile/index') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/survey_covid/report') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div> -->
						<div class="col-xs-12">
							<div class="row" id="divTable"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;"></h4></center>
			</div>
			<div class="modal-body">
				<table class="table table-bordered" id="tableDetail">
					<thead>
						<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
							<th style="width: 1%">No.</th>
							<th style="width: 5%">Question</th>
							<th style="width: 1%">Answer</th>
							<th style="width: 1%">Point</th>
						</tr>
					</thead>
					<tbody id="bodyTableDetail">
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
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

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th>Employee ID</th>';
		tableData += '<th>Name</th>';
		tableData += '<th>Department</th>';
		tableData += '<th>Section</th>';
		tableData += '<th>Group</th>';
		tableData += '<th>Date</th>';
		tableData += '<th>Vaksin 1</th>';
		tableData += '<th>Vaksin 2</th>';
		tableData += '<th>Jenis Vaksin</th>';
		tableData += '<th>Vaksin 3</th>';
		tableData += '<th>Jenis Vaksin 3</th>';
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
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "</tr>";
		tableData += "</tfoot>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}

	function fillList(){
			$('#loading').show();
			// var tanggal_from = $('#tanggal_from').val();
			// var tanggal_to = $('#tanggal_to').val();

			// var data = {
			// 	tanggal_from:tanggal_from,
			// 	tanggal_to:tanggal_to,
			// }
			$.get('{{ url("fetch/vaksin/report") }}', function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				
				$.each(result.survey, function(key, value) {
					
					tableData += '<tr>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ (value.department || "") +'</td>';
					tableData += '<td>'+ (value.section || "") +'</td>';
					tableData += '<td>'+ (value.group || "") +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td>'+ value.vaksin_1 +'</td>';
					tableData += '<td>'+ (value.vaksin_2 || "") +'</td>';
					tableData += '<td>'+ (value.jenis_vaksin || "") +'</td>';
					tableData += '<td>'+ (value.vaksin_3 || "") +'</td>';
					tableData += '<td>'+ (value.jenis_vaksin_3 || "") +'</td>';
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