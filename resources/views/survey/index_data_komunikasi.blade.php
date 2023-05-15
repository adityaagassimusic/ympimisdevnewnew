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
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #32a852; height: 200px; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>SUDAH</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px" id="total_sudah">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px" id="persen_sudah">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #f44336; height: 200px; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>BELUM</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;" id="total_belum">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px" id="persen_belum">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
				</div>
			<div class="col-xs-10" style="margin-top: 5px;padding-right: 5px">
				<div id="container1" style="width: 100%;height: 40vw;"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width:1200px">
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
								<th style="width: 3%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Dept</th>
								<th style="width: 10%;">No HP</th>
								<th style="width: 10%;">No Alternatif</th>
								<th style="width: 15%;">Keterangan Mudik</th>
								<th style="width: 5%;">Status</th>
								<th style="width: 10%;">Vaksin Berangkat</th>
								<th style="width: 10%;">Vaksin Kembali</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<!-- <tfoot>
							<tr>
								<th colspan="5">Total Duration</th>
								<th id="totalDetail">9</th>
							</tr>
						</tfoot> -->
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailAll" style="z-index:10000">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitleAll"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetailAll" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetailAll">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Dept</th>
								<th style="width: 10%;">No HP</th>
								<th style="width: 10%;">No Alternatif</th>
								<th style="width: 15%;">Keterangan Mudik</th>
								<th style="width: 5%;">Status</th>
								<th style="width: 10%;">Vaksin Berangkat</th>
								<th style="width: 10%;">Vaksin Kembali</th>
							</tr>
						</thead>
						<tbody id="tableDetailBodyAll">
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
		intervalChart = setInterval(fillChart, 15000);
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
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');


		$.get('{{ url("fetch/data_komunikasi") }}',function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Chart Machine Report
					var dept = [];
					var jml_iya = [];
					// var jml_tidak = [];
					var jml_belum = [];
					var iya = 0;
					// var tidak = 0;
					var belum = 0;
					var series = []
					// var series2 = [];
					var series3 = [];


					var total_sudah = 0;
					// var total_tidak = 0;
					var total_belum = 0;
					var total_all = 0;

					for (var i = 0; i < result.survey.length; i++) {
						dept.push(result.survey[i].department_shortname);

						jml_iya.push(parseInt(result.survey[i].iya));
						iya = iya+parseInt(result.survey[i].iya);

						// tidak = tidak+parseInt(result.survey[i].tidak);
						// jml_tidak.push(parseInt(result.survey[i].tidak));

						belum = belum+parseInt(result.survey[i].belum);
						jml_belum.push(parseInt(result.survey[i].belum));

						total_sudah += parseInt(result.survey[i].iya);
						// total_tidak += parseInt(result.survey[i].tidak);
						total_belum += parseInt(result.survey[i].belum);

						series.push([dept[i], jml_iya[i]]);
						// series2.push([dept[i], jml_tidak[i]]);
						series3.push([dept[i], jml_belum[i]]);
					}

					total_all = total_sudah + total_belum;
					$('#total_sudah').text(total_sudah);
					// $('#total_tidak').text(total_tidak);
					$('#total_belum').text(total_belum);
					$('#persen_sudah').text(((total_sudah/total_all)*100).toFixed(2)+'%');
					// $('#persen_tidak').text(((total_tidak/total_all)*100).toFixed(2)+'%');
					$('#persen_belum').text(((total_belum/total_all)*100).toFixed(2)+'%');


					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Data Komunikasi Lebaran',
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
								text: 'Total Actual Employee',
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
							name: 'Yes',
							colorByPoint: false,
							color: "#32a852"
						}
						,{
							type: 'column',
							data: series3,
							name: 'No Answer',
							colorByPoint: false,
							color:'#f44336'
						},
						]
					});

					// Highcharts.chart('container2', {
					//     chart: {
					//         plotBackgroundColor: null,
					//         plotBorderWidth: null,
					//         plotShadow: false,
					//         type: 'pie'
					//     },
					//     title: {
					//         text: 'Total Answer'
					//     },
					//     tooltip: {
					//         pointFormat: '{series.name}: <b>{point.y:.0f} Orang</b>'
					//     },
					//     accessibility: {
					//         point: {
					//             valueSuffix: '%'
					//         }
					//     },
					//     plotOptions: {
					//         pie: {
					//             allowPointSelect: true,
					//             cursor: 'pointer',
					//             dataLabels: {
					//                 enabled: true,
					//                 format: '<b>{point.name}</b>: {point.y:.0f} Orang'
					//             },
					//             animation: false,
					//         }
					//     },credits: {
					// 		enabled: false
					// 	},
					//     series: [{
					//         name: 'Survey',
					//         colorByPoint: true,
					//         data: [{
					//             name: 'Yes',
					//             y: iya,
					//             sliced: true,
					//             selected: true,
					//             colorByPoint: false,
					// 			color: "#c7c118"
					//         }, {
					//             name: 'No Answer',
					//             y: belum,
					//             colorByPoint: false,
					// 			color:'#a83232'
					//         }, ]
					//     }]
					// });


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
		var data = {
			dept:dept,
			answer:answer
		}

		$.get('{{ url("fetch/data_komunikasi/detail") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableDetailBody').html('');

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.survey, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department_shortname +'</td>';
					resultData += '<td>'+ (value.no_hp || '') +'</td>';
					resultData += '<td>'+ (value.no_alternatif || '') +'</td>';
					resultData += '<td>'+ (value.rencana_mudik || '')	 +' ('+ (value.tanggal_berangkat || '')+' - '+ (value.tanggal_kembali || '')+')</td>';
					resultData += '<td>'+ (value.test_type || '') +'</td>';
					resultData += '<td>'+ (value.tanggal_departure || '');
					if (value.departure != null) {
						resultData += '<br><a href="http://10.109.33.10/ympicoid/public/files/mudik/'+value.departure+'" target="_blank" class="fa fa-paperclip"></a>';
					}
					resultData += '</td>';

					resultData += '<td>'+ (value.tanggal_arrived || '');
					if (value.arrived != null) {
						resultData += '<br><a href="http://10.109.33.10/ympicoid/public/files/mudik/'+value.arrived+'" target="_blank" class="fa fa-paperclip"></a>';
					}
					resultData += '</td>';
					resultData += '</tr>';
					index += 1;

				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Answer '"+answer+"'</span></center>");

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
				intervalChart = setInterval(fillChart,15000);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
  	}


 function ShowModalAll(answer) {
		clearInterval(intervalChart);
		$('#modalDetailAll').modal('show');
		$('#loadingDetailAll').show();
		$('#modalDetailTitleAll').html("");
		$('#tableDetailAll').hide();

		$("#loading").show();

		var data = {
			answer:answer,
		}

		$.get('{{ url("fetch/data_komunikasi/detailAll") }}', data, function(result, status, xhr) {
			if(result.status){

				$("#loading").hide();
				$('#tableDetailBodyAll').html('');

				$('#tableDetailAll').DataTable().clear();
				$('#tableDetailAll').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.komunikasi, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department_shortname +'</td>';
					resultData += '<td>'+ (value.no_hp || '') +'</td>';
					resultData += '<td>'+ (value.no_alternatif || '') +'</td>';
					resultData += '<td>'+ (value.rencana_mudik || '')	 +' ('+ (value.tanggal_berangkat || '')+' - '+ (value.tanggal_kembali || '')+')</td>';
					resultData += '<td>'+ (value.test_type || '') +'</td>';
					
					resultData += '<td>'+ (value.tanggal_departure || '');
					if (value.departure != null) {
						resultData += '<br><a href="http://10.109.33.10/ympicoid/public/files/mudik/'+value.departure+'" target="_blank" class="fa fa-paperclip"></a>';
					}
					resultData += '</td>';

					resultData += '<td>'+ (value.tanggal_arrived || '');
					if (value.arrived != null) {
						resultData += '<br><a href="http://10.109.33.10/ympicoid/public/files/mudik/'+value.arrived+'" target="_blank" class="fa fa-paperclip"></a>';
					}
					resultData += '</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBodyAll').append(resultData);
				$('#modalDetailTitleAll').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees '"+answer+"'</span></center>");

				$('#loadingDetailAll').hide();
				$('#tableDetailAll').show();
				var table = $('#tableDetailAll').DataTable({
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


</script>
@endsection