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
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #212121;  
		color:white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(54, 59, 56);
		background-color: #212121;
		color: white;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}
	table.table-condensed > thead > tr > th{   
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}

	table.table-striped > thead > tr > th{
		border:1px solid black !important;
		text-align: center;
		background-color: rgba(126,86,134,.7) !important;  
	}

	table.table-striped > tbody > tr > td{
		border-collapse: collapse;
		color: black;
		padding: 3px;
		vertical-align: middle;
		text-align: center;
		background-color: white;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	#container1 {
		height: 400px;
	}

	.highcharts-figure,
	.highcharts-data-table table {
		min-width: 310px;
		max-width: 800px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #ebebeb;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}

	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}

	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
		padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}
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
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="form-group">
									<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
										<span style="font-weight: bold; font-size: 1.6vw;">LOGBOOK LIMBAH {{ $slip }}</span>
										<input type="hidden" id="no_slip" value="{{ $slip }}">
									</div>
									<table id="TableLogBook" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
										<thead style="background-color: rgb(126,86,134); color: #FFD700;">
											<tr>
												<th width="1%">No</th>
												<th width="2%">Slip</th>
												<th width="2%">Asal Limbah</th>
												<th width="2%">Nama Limbah</th>
												<th width="2%">Tanggal Diterima</th>
												<th width="2%">PIC</th>
											</tr>
										</thead>
										<tbody id="bodyTableLogBook">
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			Resume();
		});

	function Resume(){
		var slip = $('#no_slip').val();
		var data = {
			slip:slip
		}
		$.get('{{ url("fetch/logbook") }}', data, function(result, status, xhr){
			if(result.status){
				$('#TableLogBook').DataTable().clear();
				$('#TableLogBook').DataTable().destroy();
				$('#bodyTableLogBook').html("");
				var tableData = "";
				var index = 1;
				$.each(result.resumes, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.slip +'</td>';
					tableData += '<td>'+ value.dari_lokasi +'</td>';
					tableData += '<td>'+ value.waste_category +'</td>';
					tableData += '<td>'+ value.tanggal_logbook +'</td>';
					tableData += '<td>'+ value.pic.split('/')[1] +'</td>';
					index++;
				});
				$('#bodyTableLogBook').append(tableData);

				var table = $('#TableLogBook').DataTable({
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
</script>
@endsection