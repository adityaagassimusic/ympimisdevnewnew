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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" id="keterangan" data-placeholder="Pilih Survey" style="width: 100%;">
						<option value=""></option>
						<option value="Emergency 1">Emergency 1</option>
						<option value="Emergency 2">Emergency 2</option>
						<option value="Emergency 3">Emergency 3</option>
						<option value="Emergency 4">Emergency 4</option>
						<option value="Emergency 5">Emergency 5</option>
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
			</div>
			<div class="col-xs-8" style="margin-top: 5px;padding-right: 5px">
				<div id="container1" style="width: 100%;height: 500px;"></div>
				<!-- <div id="container2" style="width: 100%;"></div> -->
			</div>
			<div class="col-xs-4" style="margin-top: 5px;padding-left: 0px">
				<div id="container2" style="width: 100%;height: 500px;"></div>
				<!-- <div id="container2" style="width: 100%;"></div> -->
			</div>
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
								<th style="width: 3%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
								<th style="width: 3%;">Answer</th>
								<th style="width: 3%;">Relationship</th>
								<th style="width: 3%;">Family Name</th>
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
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
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
		$('.select2').select2({
			allowClear:true
		});
		fillChart();
		intervalChart = setInterval(fillChart, 300000);
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

	var survey_detail = [];

	function fillChart() {
		$('#loading').show();
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			keterangan:$('#keterangan').val()
		}

		$.get('{{ url("fetch/survey") }}', data,function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Chart Machine Report
					var dept = [];
					var jml_iya = [];
					var jml_tidak = [];
					var jml_belum = [];
					var iya = 0;
					var tidak = 0;
					var belum = 0;
					var series = []
					var series2 = [];
					var series3 = [];

					var data_emergency = [];
					var keterangan_all = [];
					var dept_all = [];

					if (result.survey.emergency.length > 0 ) {
						for (var i=0; i < result.emp.length; i++) { 
							var answer = null;
							var relationship = null;
							var family = null;
							for (var j=0; j < result.survey.emergency.length; j++) {
								if (result.emp[i].employee_id.toUpperCase() == result.survey.emergency[j].employee_id.toUpperCase() && result.survey.emergency[j].keterangan == result.keterangan) {
									answer = result.survey.emergency[j].jawaban;
									relationship = result.survey.emergency[j].hubungan;
									family = result.survey.emergency[j].nama;
								}
							}
							dept_all.push(result.emp[i].department);
							var department_shortname = '';
							for(var u = 0; u < result.dept.length;u++){
								if (result.emp[i].department == null) {
									department_shortname = 'MNGT';
								}else{
									if (result.dept[u].department_name == result.emp[i].department) {
										department_shortname = result.dept[u].department_shortname;
									}
								}
							}
							data_emergency.push({
								employee_id: result.emp[i].employee_id.toUpperCase(),
								name: result.emp[i].name,
								department: result.emp[i].department,
								department_shortname: department_shortname,
								answer: answer,
								relationship: relationship,
								family: family,
							});
						}
					}

					survey_detail = data_emergency;

					var keterangan_unik = result.keterangan_all.filter(onlyUnique);
					$('#keterangan').html('');
					var ket = '';
					for(var i = 0; i < keterangan_unik.length;i++){
						ket += '<option value="'+keterangan_unik[i]+'">'+keterangan_unik[i]+'</option>';
					}
					$('#keterangan').append(ket);
					$('#keterangan').val(result.keterangan).trigger('change');

					var dept_unik = dept_all.filter(onlyUnique);

					var emp = [];

					var total_iya = 0;
					var total_tidak = 0;
					var total_belum = 0;

					for(var k = 0; k < dept_unik.length;k++){
						var belum = 0;
						var iya = 0;
						var tidak = 0;
						for (var i = 0; i < data_emergency.length; i++) {
							if (data_emergency[i].department == dept_unik[k]) {
								if (data_emergency[i].answer == null) {
									belum++;
								}else{
									if (data_emergency[i].answer == 'Iya') {
										iya++;
									}else{
										tidak++;
									}
								}
							}
						}
						var department_shortname = '';
						for(var u = 0; u < result.dept.length;u++){
							if (dept_unik[k] == null) {
								department_shortname = 'MNGT';
							}else{
								if (result.dept[u].department_name == dept_unik[k]) {
									department_shortname = result.dept[u].department_shortname;
								}
							}
						}
						dept.push(department_shortname);
						series.push(parseInt(iya));
						series2.push(parseInt(tidak));
						series3.push(parseInt(belum));
						total_iya = total_iya + parseInt(iya);
						total_tidak = total_tidak + parseInt(tidak);
						total_belum = total_belum + parseInt(belum);
					}

					var bencana;

					if (result.keterangan == "Emergency 1") {
						bencana = "Gempa Di Jawa Timur";
					} else if (result.keterangan == "Emergency 2"){
						bencana = "Banjir Bandang Di Malang dan Batu"; 
					} else if (result.keterangan == "Emergency 3"){
						bencana = "Erupsi Gunung Semeru"; 
					} else if (result.keterangan == "Emergency 4"){
						bencana = "Kebakaran di Tunjungan Plaza Surabaya"; 
					} else if (result.keterangan == "Emergency 5"){
						bencana = ""; 
					}else if (result.keterangan == "Emergency Kanjuruhan"){
						bencana = "Kanjuruhan"; 
					}else if (result.keterangan == "Emergency Erupsi Gunung Semeru"){
						bencana = "Erupsi Gunung Semeru"; 
					}else if (result.keterangan == "Kuisioner Compliance"){
						bencana = "Kuisioner Awareness Compliance"; 
					}


					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Survey '+bencana,
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
				                      ShowModal(this.category,this.series.name,bencana);
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
							name: 'Iya',
							colorByPoint: false,
							color: "#c7c118"
						},{
							type: 'column',
							data: series2,
							name: 'Tidak',
							colorByPoint: false,
							color:'#32a852'
						}
						,{
							type: 'column',
							data: series3,
							name: 'Belum Menjawab',
							colorByPoint: false,
							color:'#a83232'
						},
						]
					});

					Highcharts.chart('container2', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie'
					    },
					    title: {
					        text: 'Total Answer'
					    },
					    // tooltip: {
					    //     pointFormat: '{series.name}: <b>{point.y} Orang</b>'
					    // },
					    accessibility: {
					        point: {
					            valueSuffix: '%'
					        }
					    },
					    plotOptions: {
					        pie: {
					            allowPointSelect: true,
					            cursor: 'pointer',
					            dataLabels: {
					                enabled: true,
					                format: '<b>{point.name}</b>: {point.y} Orang'
					            },
					            animation: false,
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Total Karyawan',
					        colorByPoint: true,
					        data: [{
					            name: 'Iya',
					            y: total_iya,
					            sliced: true,
					            selected: true,
					            colorByPoint: false,
								color: "#c7c118"
					        }, {
					            name: 'Tidak',
					            y: total_tidak,
					            colorByPoint: false,
								color:'#32a852'
					        }, {
					            name: 'Belum Menjawab',
					            y: total_belum,
					            colorByPoint: false,
								color:'#a83232'
					        }, ]
					    }]
					});

					$('#loading').hide();
				}
			}
		});

	}

	function ShowModal(dept,answer,keterangan) {
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetailBody').html('');

		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();

		var index = 1;
		var resultData = "";
		var total = 0;
		console.log(dept);

		$.each(survey_detail, function(key, value) {
			if (answer == 'Belum Menjawab') {
				if (value.answer == null && value.department_shortname == dept) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					if (value.department_shortname == 'MNGT') {
						resultData += '<td>Management</td>';
					}else{
						resultData += '<td>'+ value.department_shortname +'</td>';
					}
					resultData += '<td>'+ (value.answer || '') +'</td>';
					resultData += '<td>'+ (value.relationship || '') +'</td>';
					resultData += '<td>'+ (value.family || '') +'</td>';
					resultData += '</tr>';
					index += 1;
				}
			}else{
				if (value.answer == answer && value.department_shortname == dept) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					if (value.department_shortname == 'MNGT') {
						resultData += '<td>Management</td>';
					}else{
						resultData += '<td>'+ value.department_shortname +'</td>';
					}
					resultData += '<td>'+ (value.answer || '') +'</td>';
					resultData += '<td>'+ (value.relationship || '') +'</td>';
					resultData += '<td>'+ (value.family || '') +'</td>';
					resultData += '</tr>';
					index += 1;
				}
			}
		});
		$('#tableDetailBody').append(resultData);
		$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Karyawan dengan Jawaban '"+answer+"'<br>Survey "+keterangan+"</span></center>");

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
		$('#modalDetail').modal('show');
		$('#loading').hide();
  	}

  	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}


</script>
@endsection