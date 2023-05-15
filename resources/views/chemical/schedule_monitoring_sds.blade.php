@extends('layouts.display')
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
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
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


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
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
		<div class="col-xs-12" style="padding-bottom: 10px">

			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select class="form-control select2" name="fiscal_year" id="fiscal_year" data-placeholder="Pilih Fiscal Year" style="width: 100%;">
					<option></option>
					@foreach($fy_all as $fy_all)
					<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search
				</button>
			</div>
			<a class="btn btn-primary pull-right" style="margin-left: 5px" href="{{url('index/chemical/sosialisasi')}}">
				Upload Data
			</a>
			<a class="btn btn-info pull-right" style="margin-left: 5px" href="{{url('index/chemical/safety_data_sheet/')}}"> 
				Manage SDS
			</a>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">

			<div id="container" style="height: 50vh;">
				
			</div>

		</div>
		<div class="col-xs-12" style="margin-top: 0px;padding-top: 0px">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableCode" class="table table-bordered table-hover" style="margin-bottom: 0;">
								<thead style="background-color: #605ca8;color: white" id="headTableCode">
									
								</thead>
								<tbody id="bodyTableCode">
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

	<div class="modal fade" id="modalDetail" style="z-index: 10000;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<ul class="nav nav-tabs">
							<h2 id="detailJudul"></h2>
						</ul>
					</div>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtable">
						<thead style="background-color: rgb(126,86,134)">
							<tr>
								<th>No</th>
								<th>Employee ID</th>
								<th>Name</th>
								<th>Bagian</th>
							</tr>
						</thead>
						<tbody id="detailbodytable">
						</tbody>
					</table>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];
	var data_operator = [];


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
		$('.select2').select2({
			allowClear:true
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
			// $('#loading').show();
			var data_bagian = [];

			data_bagian.push({
				'kategori' :'Ekstrim','bagian' :'KPP (Area cuci)'
			},{
				'kategori' :'Ekstrim','bagian' :'Body process (Sax Body)'
			},{
				'kategori' :'Ekstrim','bagian' :'Body process (Sax Bell)'
			},{
				'kategori' :'Ekstrim','bagian' :'Body process (Flute)'
			},{
				'kategori' :'Ekstrim','bagian' :'Reedplate'
			},{
				'kategori' :'Ekstrim','bagian' :'SLD (Cuci asam)'
			},{
				'kategori' :'Ekstrim','bagian' :'Handatsuke'
			},{
				'kategori' :'Ekstrim','bagian' :'Buffing Flute (cuci enthol)'
			},{
				'kategori' :'Ekstrim','bagian' :'Painting'
			},{
				'kategori' :'Ekstrim','bagian' :'Plating'
			},{
				'kategori' :'Ekstrim','bagian' :'WWT'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Warehouse'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Recorder Assy'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Recorder injection'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Venova'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Mouth piece'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Quality Assurance'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Pianika'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Case'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Molding'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'Workshop'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'KPP (Senban/ Sanding/ Machining/ Press)'
			},{
				'kategori' :'Non ekstrim 1','bagian' :'CL body'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'SLD (non cuci asam)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Buffing (FL key)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Buffing (CL key)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Buffing (SAX key)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Buffing (SAX body-bell)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Buffing (FL body)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Assy (FL)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Assy (CL)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Assy (SAX)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Sub Assy (FL)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Sub Assy (CL)'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Tanpo'
			},{
				'kategori' :'Non ekstrim 2','bagian' :'Sub Assy (SAX)'
			}
			);

			var data = {
			// tanggal_from:$('#tanggal_from').val(),
			// tanggal_to:$('#tanggal_to').val(),
			// status:$('#status').val(),
			fiscal_year:$('#fiscal_year').val(),
		}
		$.get('{{ url("fetch/sosialisasi/shedule/sds") }}',data, function(result, status, xhr){
			if(result.status){
				// $('#tableCode').DataTable().clear();
				// $('#tableCode').DataTable().destroy();

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th colspan="2">Bagian</th>';
				for(var i = 0; i < result.fy.length;i++){
					headTableData += '<th>'+result.fy[i].month_name+'</th>';
				}

				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#bodyTableCode').html("");
				var tableData = "";

				data_operator.push(result.get_data);

				for(var i = 0; i < data_bagian.length;i++){
					tableData += '<tr>';
					tableData += '<td rowspan="2" style="padding:5px !important;text-align:left">'+data_bagian[i].bagian+'</td>';
					tableData += '<td style="padding:5px !important;text-align:left">Plan</td>';
					for (var j = 0; j < result.fy.length;j++) {
						var plans = 0;

						for(var k = 0; k < result.renewals.length;k++){
							if (result.renewals[k].length > 0) {
								for(var l = 0; l < result.renewals[k].length;l++){
									if (result.renewals[k][l].months == result.fy[j].months && data_bagian[i].bagian == result.renewals[k][l].area) {
										plans++;
									}
								}
							}
						}

						if (plans == 0) {
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
						}else{
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(255, 204, 255);cursor: pointer;" onclick="ShowDetailMd(\''+data_bagian[i].bagian+'\',\''+result.fy[j].months+'\',\'plan\')">'+plans+'</td>';
						}
					}
					tableData += '</tr>';
					tableData += '<tr>';
					tableData += '<td style="padding:5px !important;text-align:left">Actual</td>';
					for (var j = 0; j < result.fy.length;j++) {
						var actuals = 0;
						for(var k = 0; k < result.news.length;k++){
							for(var l = 0; l < result.news[k].length;l++){
								if (result.news[k][l].months == result.fy[j].months && data_bagian[i].bagian == result.renewals[k][l].area) {

									actuals++;
								}
							}
						}
						if (actuals == 0) {
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
						}else{
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(204, 255, 215)" onclick="ShowDetailMd(\''+data_bagian[i].bagian+'\',\''+result.fy[j].months+'\',\'actual\')">'+actuals+'</td>';
						}
					}
					tableData += '</tr>';
				}

				$('#bodyTableCode').append(tableData);
				$('#loading').hide();

				var category = [];
				var renewals = [];
				var news = [];
				for (var i = 0; i < result.fy.length;i++) {
					category.push(result.fy[i].month_name);
					renewals.push(parseInt(result.renewals[i].length));
					news.push(parseInt(result.news[i].length));
				}

				const chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						type: 'column',
						backgroundColor:'none',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							depth: 50,
							viewDistance: 25
						}
					},
					xAxis: {
						categories: category,
						type: 'category',
						gridLineWidth: 0,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:1,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Total Data',
							style: {
								color: '#eee',
								fontSize: '12px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						allowDecimals: false,
						labels:{
							style:{
								fontSize:"12px"
							}
						},
						type: 'linear',
						// opposite: true
					}
					],
					tooltip: {
						headerFormat: '<span>{series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
						itemStyle: {
							fontSize:'12px',
						},
						reversed : true
					},	
					title: {
						text: '<b>SDS SOSIALISASI SCHEDULE</b>',
						// style:{
						// 	fontSize:"12px"
						// }
					},
					subtitle: {
						text: ''
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,this.series.name);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '0.9vw'
								}
							},
							animation: false,
							cursor: 'pointer',
							depth:25
						},
					},
					credits:{
						enabled:false
					},
					series: [{
						type: 'column',
						data: renewals,
						name: 'Schedule',
						colorByPoint: false,
						color:'#f44336'
					}
					,{
						type: 'column',
						data: news,
						name: 'Done',
						colorByPoint: false,
						color:'#32a852'
					}
					]
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!','Attempt to retrieve data failed');
			}
		});
}

function ShowDetailMd(bag,month,st){
	$('#detailtable').DataTable().clear();
	$('#detailtable').DataTable().destroy();
	$("#detailbodytable").empty();

	var body = '';
	var count = 1;
	var jud = "";



	for (var i = 0; i < data_operator[0].length; i++) {

		if (st == "actual") {
			if (data_operator[0][i].area == bag)  {
				if (data_operator[0][i].months_new == month && data_operator[0][i].status == 1) {
					body += '<tr>';
					body += '<td>'+count+'</td>';
					body += '<td>'+data_operator[0][i].employee_id+'</td>';
					body += '<td>'+data_operator[0][i].name+'</td>';
					body += '<td>'+data_operator[0][i].area+'</td>';
					body += '</tr>';
					count++;
					jud = "Done";
				}
			}
		}else{
			if (data_operator[0][i].area == bag)  {
				if (data_operator[0][i].months_new == month) {
				body += '<tr>';
				body += '<td>'+count+'</td>';
				body += '<td>'+data_operator[0][i].employee_id+'</td>';
				body += '<td>'+data_operator[0][i].name+'</td>';
				body += '<td>'+data_operator[0][i].area+'</td>';
				body += '</tr>';
				count++;
				jud = "Schedule";
				}
			}

		}

	}

	$("#detailbodytable").append(body);

	var table = $('#detailtable').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": false,
	});


	$('#modalDetail').modal('show');
	$('#detailJudul').html('Detail Operator Sosialisasi SDS '+jud);
}

function ShowModal(categorys,name){
	

	$('#detailtable').DataTable().clear();
	$('#detailtable').DataTable().destroy();
	$("#detailbodytable").empty();

	var body = '';
	var count = 1;
	var jud = "";


	for (var i = 0; i < data_operator[0].length; i++) {
		if (name == "Done") {
			if (data_operator[0][i].months == categorys && data_operator[0][i].status == 1)  {
				body += '<tr>';
				body += '<td>'+count+'</td>';
				body += '<td>'+data_operator[0][i].employee_id+'</td>';
				body += '<td>'+data_operator[0][i].name+'</td>';
				body += '<td>'+data_operator[0][i].area+'</td>';
				body += '</tr>';
				count++;
				jud = "Done";
			}

		}else{
			if (data_operator[0][i].months == categorys)  {
				body += '<tr>';
				body += '<td>'+count+'</td>';
				body += '<td>'+data_operator[0][i].employee_id+'</td>';
				body += '<td>'+data_operator[0][i].name+'</td>';
				body += '<td>'+data_operator[0][i].area+'</td>';
				body += '</tr>';
				count++;
				jud = "Schedule";

			}

		}

	}

	$("#detailbodytable").append(body);

	var table = $('#detailtable').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": false,
	});


	$('#modalDetail').modal('show');
	$('#detailJudul').html('Detail Operator Sosialisasi SDS '+jud);
}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
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
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
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
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
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
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);



</script>
@endsection