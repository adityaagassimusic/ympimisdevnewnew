@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
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
		<div class="col-xs-2" style="padding-bottom: 10px">
			<select class="form-control select2" name="department" id='department' style="width: 100%;" onchange="drawChart()" data-placeholder="Select Department">
				<option value=""></option>
				@foreach($dept as $dept)
				<option value="{{ $dept->department }}">{{ $dept->department }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-xs-10" style="padding-bottom: 10px" align="right">
			<p style="font-size: 15px; color: white">Last Update: {{ date('d-M-Y H:i:s') }}</p>
		</div>
		<div class="col-xs-12">
			<div id="chart" style="height: 70vh"></div>
		</div>
<!-- 		<div class="col-xs-12">
			<div id="chart2" style="height: 40vh;margin-top: 10px;"></div>
		</div> -->
	</div>
</section>

<div class="modal fade" id="ModalDetail" style="z-index: 10000;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<span style="font-weight: bold; font-size: 2vw;" class="text-purple" id="title-detail"></span>
					</div>
					<div class="col-md-12" style="padding-top: 10px">
						<table id="detailModal" class="table table-bordered table-striped table-hover">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 10%;">ID</th>
									<th style="width: 10%;">Dept.</th>
									<th style="width: 50%;">Title</th>
									<th style="width: 10%;">Status</th>
									<th style="width: 20%;">PIC</th>
								</tr>
							</thead>
							<tbody id="detailModalBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
		$('.select2').select2({
			allowClear : true,
		});
		drawChart();
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

	function drawChart() {
		$("#loading").show();

    	var department = $('#department').val();

		var data = {
		    department: department,
		}
		$.get('{{ url("fetch/ticket_monitoring/category") }}',data,function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$("#loading").hide();
					var categories = [];
					var series = [];

					$.each(result.data, function(key, value){
						// var date = new Date(value.bulan);
						// var nama_bulan = date.getMonth();
						categories.push(value.group);
						series.push(parseInt(value.jumlah));
					});
					
					Highcharts.chart('chart', {
						chart: {
							type: 'column',
							options3d: {
								enabled: true,
								alpha: 15,
								beta: 0,
								depth: 0,
								viewDistance: 50
							}
						},
						title: {
							text: 'MONITORING TICKET BY CATEGORY'
						},
						xAxis: {
							categories: categories,
							type: 'category',
							gridLineColor: '#707073',
							labels: {
								style: {
									color: '#E0E0E3'
								}
							},
							lineColor: '#707073',
							minorGridLineColor: '#505053',
							tickColor: '#707073',
							title: {
								style: {
									color: '#A0A0A3'

								}
							}
						},yAxis: [{
							title: {
								text: 'Total',
								style: {
									color: '#eee',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"15px"
								}
							},
							type: 'linear',
							opposite: false
						},
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							itemStyle: {
								color: '#E0E0E3'
							},
							itemHoverStyle: {
								color: '#FFF'
							},
							itemHiddenStyle: {
								color: '#606063'
							}
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											DetailModal(this.category);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							name:'Project Category',
							data: series,
							showInLegend: false,
							color : '#00BFFF'
						}]
					});
				}
			}
		});
}


	function DetailModal(category){
		$('#loading').show();

    	var department = $('#department').val();

		var data = {
			category:category,
			department:department
		}

		$.get('{{ url("fetch/detail/tiket_category") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#ModalDetail').modal('show');
				$('#title-detail').html('Detail Ticket '+category);

				$('#detailModal').DataTable().clear();
				$('#detailModal').DataTable().destroy();
				$('#detailModalBody').html("");
				var tableData = "";
				$.each(result.data, function(key, value) {

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ value.ticket_id +'</td>';
					tableData += '<td style="text-align: center">'+ value.department_shortname +'</td>';
					tableData += '<td style="text-align: left">'+ value.case_title +'</td>';
					tableData += '<td style="text-align: center">'+ value.status +'</td>';
					tableData += '<td style="text-align: left">'+ (value.pic_name || '') +'</td>';
					tableData += '</tr>';

				});
				$('#detailModalBody').append(tableData);

				var table = $('#detailModal').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[{
						extend: 'pageLength',
						className: 'btn btn-default',
					}]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
				'DataListing': true	,
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
		});
	}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
				]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	}
};
Highcharts.setOptions(Highcharts.theme);
</script>
@endsection