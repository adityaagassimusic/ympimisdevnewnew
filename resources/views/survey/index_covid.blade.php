@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

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
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}
	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
	}

	 div#tableDetail_info.dataTables_info,
	 div#tableDetail_filter.dataTables_filter label,
	 div#tableDetail_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetail_info.dataTables_info,
	#tableDetail_info.dataTables_length {
		color: black;
	}

	div#tableDetailPie_info.dataTables_info,
	 div#tableDetailPie_filter.dataTables_filter label,
	 div#tableDetailPie_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailPie_info.dataTables_info,
	#tableDetailPie_info.dataTables_length {
		color: black;
	}

	div#tableDetailCategory_info.dataTables_info,
	 div#tableDetailCategory_filter.dataTables_filter label,
	 div#tableDetailCategory_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailCategory_info.dataTables_info,
	#tableDetailCategory_info.dataTables_length {
		color: black;
	}
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 27%;">
      <span style="font-size: 40px">Loading, please wait a moment . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<!-- <div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" id="keterangan" data-placeholder="Pilih Survey" style="width: 100%;">
						<option value=""></option>
						<option value="covid">Covid</option>
					</select>
				</div> -->
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;padding-right: 5px">
				<div id="container1" style="width: 100%;height: 500px;"></div>
			</div>

			<?php if(str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'GA') || str_contains(Auth::user()->role_code, 'HR')) { ?>

			<div class="col-xs-6" style="margin-top: 5px;padding-left: 0px">
				<div id="container2" style="width: 100%;height: 500px;"></div>
			</div>
			<div class="col-xs-6" style="margin-top: 5px;padding-left: 0px">
				<div id="container3" style="width: 100%;height: 500px;"></div>
			</div>

			<?php } ?>
		</div>
	</div>
</section>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 6%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailPie">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitlePie"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetailPie">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 6%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
							</tr>
						</thead>
						<tbody id="tableDetailBodyPie">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailCategory">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitleCategory"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetailCategory">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 6%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
							</tr>
						</thead>
						<tbody id="tableDetailBodyCategory">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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
	var intervalChart;

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		intervalChart = setInterval(fillChart, 60000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
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

	function fillChart() {
    $("#loading").show();

		var tanggal = $('#tanggal').val();
		
		var data = {
			tanggal:tanggal
		}

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		$.get('{{ url("fetch/survey_covid") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){


		      $("#loading").hide();
					//Chart Machine Report
					var dept = [];

					var jml_sudah = [];
					var jml_belum = [];

					var sudah = 0;
					var belum = 0;

					var series = []
					var series2 = [];

					var jml_rendah = 0;
					var jml_sedang = 0;
					var jml_tinggi = 0;

					var keterangan = 'covid-19';

					for (var i = 0; i < result.survey.length; i++) {
						dept.push(result.survey[i].department_shortname);

						sudah = sudah+parseInt(result.survey[i].sudah);
						belum = belum+parseInt(result.survey[i].belum);

						jml_sudah.push(parseInt(result.survey[i].sudah));
						jml_belum.push(parseInt(result.survey[i].belum));

						series.push([dept[i], jml_sudah[i]]);
						series2.push([dept[i], jml_belum[i]]);
					}

					jml_rendah = (parseInt(result.nilai[0].jumlah_rendah));
					jml_sedang = (parseInt(result.nilai[0].jumlah_sedang));
					jml_tinggi = (parseInt(result.nilai[0].jumlah_tinggi));

					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Resume Pengisian Survey '+keterangan,
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: dept,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Actual',
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
							opposite: true
						},
					    ],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
				                fontSize:'12px',
				            },
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
							type: 'column',
							data: series,
							name: 'Sudah',
							colorByPoint: false,
							color: "#32a852"
						},{
							type: 'column',
							data: series2,
							name: 'Belum',
							colorByPoint: false,
							color:'#a83232'
						}
						]
					});

					Highcharts.chart('container2', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie',
									options3d: {
										enabled: true,
										alpha: 45,
										beta: 0
									}
					    },
					    title: {
					        text: 'Total Answer By Person'
					    },
					    tooltip: {
					        pointFormat: '{series.name}: <b>{point.y}</b>'
					    },
					    accessibility: {
					        point: {
					            valueSuffix: '%'
					        }
					    },
					    plotOptions: {
					        pie: {
					            allowPointSelect: true,
					            cursor: 'pointer',
											edgeWidth: 1,
											edgeColor: 'rgb(126,86,134)',
											depth: 35,
					            dataLabels: {
					                enabled: true,
					                format: '<b>{point.name}</b>: {point.y}'
					            },
					            animation: false,
											showInLegend: true,
											point: {
												events: {
													click: function () {
														fetchDetailAnswer(this.name);
													}
												}
											}
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Survey',
					        colorByPoint: true,
					        data: [{
					            name: 'Sudah',
					            y: sudah,
					            sliced: true,
					            selected: true,
					            colorByPoint: false,
								color: "#32a852"
					        }, {
					            name: 'Belum',
					            y: belum,
					            colorByPoint: false,
								color:'#a83232'
					        }]
					    }]
					});

					Highcharts.chart('container3', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie',
									options3d: {
										enabled: true,
										alpha: 45,
										beta: 0
									}
					    },
					    title: {
					        text: 'Total Answer By Type'
					    },
					    tooltip: {
					        pointFormat: '{series.name}: <b>{point.y}</b>'
					    },
					    accessibility: {
					        point: {
					            valueSuffix: '%'
					        }
					    },
					    plotOptions: {
					        pie: {
					            allowPointSelect: true,
					            cursor: 'pointer',
											edgeWidth: 1,
											edgeColor: 'rgb(126,86,134)',
											depth: 35,
					            dataLabels: {
					                enabled: true,
					                format: '<b>{point.name}</b>: {point.y}'
					            },
					            animation: false,
											showInLegend: true,
											point: {
												events: {
													click: function () {
														fetchDetailCategory(this.name);
													}
												}
											}
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Nilai',
					        colorByPoint: true,
					        data: [{
					            name: 'Rendah',
					            y: jml_rendah,
					            colorByPoint: false,
								color: "#348ceb"
					        }, {
					            name: 'Sedang',
					            y: jml_sedang,
					            colorByPoint: false,
								color:'#ebab34'
					        }, {
					            name: 'Tinggi',
					            y: jml_tinggi,
					            colorByPoint: false,
								color:'#eb4c34'
					        }]
					    }]
					});

				}
			}
		});

	}

	function ShowModal(dept,answer) {
		clearInterval(intervalChart);
		$('#modalDetail').modal('show');
		$('#loadingDetail').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();


    $("#loading").show();

		var tanggal = $('#tanggal').val();

		var data = {
			dept:dept,
			answer:answer,
			tanggal:tanggal
		}

		$.get('{{ url("fetch/survey_covid/detail") }}', data, function(result, status, xhr) {
			if(result.status){

      	$("#loading").hide();
				$('#tableDetailBody').html('');

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "covid";

				$.each(result.survey, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Answer '"+answer+"'<br>On Survey "+keterangan+"</span></center>");

				$('#loadingDetail').hide();
				$('#tableDetail').show();
				var table = $('#tableDetail').DataTable({
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
				intervalChart = setInterval(fillChart,60000);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
  	}

	function fetchDetailAnswer(answer) {

		$('#modalDetailPie').modal('show');
		$('#modalDetailTitlePie').html("");
		$('#tableDetailPie').hide();
    $("#loading").show();

		var tanggal = $('#tanggal').val();

		var data = {
			answer:answer,
			tanggal:tanggal
		}

		$.get('{{ url("fetch/survey_covid/detail") }}', data, function(result, status, xhr) {
			if(result.status){

      	$("#loading").hide();
				$('#tableDetailBodyPie').html('');

				$('#tableDetailPie').DataTable().clear();
				$('#tableDetailPie').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "covid";

				$.each(result.survey_info, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBodyPie').append(resultData);
				$('#modalDetailTitlePie').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Answer <b>'"+answer+"'</b><br>On Survey "+keterangan+"</span></center>");

				$('#tableDetailPie').show();
				var table = $('#tableDetailPie').DataTable({
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

  function fetchDetailCategory(category) {

		$('#modalDetailCategory').modal('show');
		$('#modalDetailTitleCategory').html("");
		$('#tableDetailCategory').hide();
    $("#loading").show();

		var tanggal = $('#tanggal').val();

		var data = {
			category:category,
			tanggal:tanggal
		}

		$.get('{{ url("fetch/survey_covid/detail") }}', data, function(result, status, xhr) {
			if(result.status){

      	$("#loading").hide();
				$('#tableDetailBodyCategory').html('');

				$('#tableDetailCategory').DataTable().clear();
				$('#tableDetailCategory').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "covid";

				$.each(result.survey_category, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBodyCategory').append(resultData);
				$('#modalDetailTitleCategory').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Karyawan Dengan <b> Resiko '"+category+"'</b></center>");

				$('#tableDetailCategory').show();
				var table = $('#tableDetailCategory').DataTable({
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

</script>
@endsection