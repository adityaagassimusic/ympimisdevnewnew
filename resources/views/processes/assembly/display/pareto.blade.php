@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<!-- <div class="row"> -->
				<!-- <div id="period_title" class="col-xs-5" style="background-color: #7e5686;color: white;">
		            <center><span style="font-size: 1.6vw; font-weight: bold;" id="title_text"></span></center>
		        </div> -->
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: #7e5686;color: white;">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="date_from" name="date_from" placeholder="Select Date From">
		            </div>
		        </div>
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: #7e5686;color: white;border-color: ">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="date_to" name="date_to" placeholder="Select Date To">
		            </div>
		        </div>

				<div class="col-xs-1" style="padding-left: 5px;padding-right: 0px;">
					<button class="btn btn-success" onclick="fetchChart()" style="background-color: #7e5686;border-color: #7e5686;color: white;"><i class="fa fa-search"></i> Search</button>
				</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div id="container" style="height: 85vh"></div>
			</div>
		</div>
		<?php if ($origin_group_code == '042'): ?>
		<div class="col-xs-12">
			<div class="row">
				<div id="container2" style="height: 85vh"></div>
			</div>
		</div>
		<?php endif ?>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px;margin-top: 10px;">
				<!-- <center>
					<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
				</center> -->
				<div class="col-xs-12">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);font-weight: ">
							<tr>
								<th style="width: 1%;color: white">#</th>
								<th style="width: 2%;color: white">Serial Number</th>
								<th style="width: 2%;color: white">Model</th>
								<th style="width: 3%;color: white">Loc</th>
								<th style="width: 3%;color: white">NG Name</th>
								<th style="width: 3%;color: white">Onko</th>
								<th style="width: 3%;color: white">Qty</th>
								<th style="width: 3%;color: white">Value Atas</th>
								<th style="width: 3%;color: white">Value Bawan</th>
								<th style="width: 3%;color: white">NG Loc</th>
								<th style="width: 3%;color: white">Emp Kensa</th>
								<th style="width: 3%;color: white">At</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<div class="col-xs-12" style="padding-left:0px;padding-right:0px;">
					<button class="btn btn-danger pull-right" onclick="$('#modalDetail').modal('hide')"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/pareto.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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

	var date_from_title = null;
	var date_to_title = null;
	var details = null;
	var date_from_title2 = null;
	var date_to_title2 = null;
	var details2 = null;
	var emp = null;

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#date_to').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart,300000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: null,
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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function sortArray(array, property, direction) {
	    direction = direction || 1;
	    array.sort(function compare(a, b) {
	        let comparison = 0;
	        if (a[property] > b[property]) {
	            comparison = 1 * direction;
	        } else if (a[property] < b[property]) {
	            comparison = -1 * direction;
	        }
	        return comparison;
	    });
	    return array; // Chainable
	}

	function fetchChart(){
		$('#loading').show();
		var data = {
			origin_group_code:'{{$origin_group_code}}',
			date_from:$("#date_from").val(),
			date_to:$("#date_to").val(),
		}

		var ng_names = [
			'Kizu Baru',
			'Handa Suki',
			'Handa Oi',
			'Handa Tare',
			'Oil Mekki',
			'Handa Tobi',
			'Mekki Hilang',
			'Mekki Nai',
			'Kake',
			'Bari',
			'Nami',
			'Mekki Dobel',
			'Heko',
		];

		$.get('{{ url("fetch/assembly/pareto") }}', data, function(result, status, xhr) {
			if(result.status){
				var datestitle = '';
				if (result.dateTitleFirst == result.dateTitleLast) {
                	// $('#title_text').text('Assembly Pareto '+ result.dateTitleFirst);
                	datestitle = result.dateTitleFirst;
                }else{
                	// $('#title_text').text('Assembly Pareto '+ result.dateTitleFirst+' - '+result.dateTitleLast);
                	datestitle = result.dateTitleFirst +" - "+result.dateTitleLast;
                }

                var category = [];
                var series = [];
                var ng_name = [];
                for(var i = 0; i < result.pareto.length;i++){
                	if (ng_names.includes(result.pareto[i].ng_name.split(' - ')[0])) {
                		ng_name.push(result.pareto[i].ng_name.split(' - ')[0]);
                	}else{
                		ng_name.push(result.pareto[i].ng_name);
                	}
                	// ng_name.push(result.pareto[i].ng_name);
                }

                var ng_name_unik = ng_name.filter(onlyUnique);

                for(var i = 0; i < ng_name_unik.length;i++){
                	var serial_number = [];
                	var qty = 0;
                	for(var j = 0; j < result.pareto.length;j++){
                		var re = new RegExp(ng_name_unik[i], 'g');
                		if (result.pareto[j].ng_name.match(re)) {
                			serial_number.push(result.pareto[j].serial_number);
                		}
                	}
                	var serial_number_unik = serial_number.filter(onlyUnique);
                	series.push({y:serial_number_unik.length,ng_name:ng_name_unik[i]});
                }

                var series_sort = sortArray(series, "y", -1);

                var others = [];

                var other = 0;

                var seriesnew = [];

                var other = 0;
                var cat_other = [];

                var series_new = [];

                for(var i = 0; i < series_sort.length;i++){
                	if (i < 20) {
                		series_new.push({y:parseInt(series_sort[i].y),cat:series_sort[i].ng_name});
                	}else{
                		other = other + parseInt(series_sort[i].y);
                		cat_other.push(series_sort[i].ng_name);
                	}
                }

                // series_new.push({y:parseInt(other),cat:'Other'});

                var series_sort_new = sortArray(series_new, "y", -1);

                var category = [];
                var series = [];

                for(var i = 0; i < series_sort_new.length;i++){
                	var ser = series_sort_new[i].cat.split(' - ');
                	if (ser.length == 1) {
                		series.push({y:parseInt(series_sort_new[i].y),key:series_sort_new[i].cat});
                		category.push(series_sort_new[i].cat);
                	}else{
                		if (ser[0] == ser[1]) {
                			series.push({y:parseInt(series_sort_new[i].y),key:ser[0]});
                			category.push(ser[0]);
                		}else{
                			series.push({y:parseInt(series_sort_new[i].y),key:series_sort_new[i].cat});
                			category.push(series_sort_new[i].cat);
                		}
                	}
                }

		      	Highcharts.chart('container', {
				    chart: {
				        renderTo: 'container',
				        type: 'column'
				    },
				    title: {
				        text: 'ASSEMBLY PARETO DEFECT {{strtoupper($product)}}<br>'+datestitle,
				        style:{
				        	fontWeight:'bold'
				        }
				    },
				    tooltip: {
				        shared: true
				    },
				    plotOptions: {
						series:{
							cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	showModalDetailPareto(this.options.key);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y:,.0f}',
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							cursor: 'pointer',
							borderColor: 'black',
							
						},
						pareto:{
							cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	// showModalDetailPareto(this.options.key);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y:,.0f}%',
								style:{
									fontSize: '14px'
								}
							},
							lineWidth: 3,
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							cursor: 'pointer',
							borderColor: 'black',
							
						},
					},credits: {
						enabled: false
					},
				    xAxis: {
				        categories: category,
				        crosshair: true,
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'15px',
				            }
				        }
				    },
				    yAxis: [{
				        title: {
				            text: 'Total Defect',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'13px',
				            }
				        }
				    }, {
				        title: {
				            text: 'Pareto',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        minPadding: 0,
				        maxPadding: 0,
				        max: 100,
				        min: 0,
				        opposite: true,
				        labels: {
				            format: "{value}%",
				            style:{
				            	fontSize:'13px',
				            }
				        }
				    },],
				    series: [{
				        type: 'pareto',
				        name: 'Pareto',
				        yAxis: 1,
				        zIndex: 10,
				        baseSeries: 1,
				        tooltip: {
				            valueDecimals: 1,
				            valueSuffix: '%'
				        },
				        colorByPoint:false,
				        color:'#fff',
				    }, {
				        name: 'Total Defect',
				        type: 'column',
				        zIndex: 2,
				        data: series,
				        colorByPoint:false,
				        color:'#4287f5',
				    },
				    ]
				});

				date_from_title = result.dateTitleFirst;
				date_to_title = result.dateTitleLast;
				details = result.pareto;
				emp = result.emp;

				//PARETO 2
				if (result.pareto2 != null) {
					date_from_title2 = result.dateTitleFirst;
					date_to_title2 = result.dateTitleLast;
					details2 = result.pareto2;

					var datestitle = '';
					if (result.dateTitleFirst == result.dateTitleLast) {
	                	datestitle = result.dateTitleFirst;
	                }else{
	                	datestitle = result.dateTitleFirst +" - "+result.dateTitleLast;
	                }

	                var category = [];
	                var series = [];
	                var ng_name = [];
	                for(var i = 0; i < result.pareto2.length;i++){
	                	if (ng_names.includes(result.pareto2[i].ng_name.split(' - ')[0])) {
	                		ng_name.push(result.pareto2[i].ng_name.split(' - ')[0]);
	                	}else{
	                		ng_name.push(result.pareto2[i].ng_name);
	                	}
	                }

	                var ng_name_unik = ng_name.filter(onlyUnique);

	                for(var i = 0; i < ng_name_unik.length;i++){
	                	var serial_number = [];
	                	var qty = 0;
	                	for(var j = 0; j < result.pareto2.length;j++){
	                		var re = new RegExp(ng_name_unik[i], 'g');
	                		if (result.pareto2[j].ng_name.match(re)) {
	                			serial_number.push(result.pareto2[j].serial_number);
	                		}
	                	}
	                	var serial_number_unik = serial_number.filter(onlyUnique);
	                	series.push({y:serial_number_unik.length,ng_name:ng_name_unik[i]});
	                }

	                var series_sort = sortArray(series, "y", -1);

	                var others = [];

	                var other = 0;

	                var seriesnew = [];

	                var other = 0;
	                var cat_other = [];

	                var series_new = [];

	                for(var i = 0; i < series_sort.length;i++){
	                	if (i < 20) {
	                		series_new.push({y:parseInt(series_sort[i].y),cat:series_sort[i].ng_name});
	                	}else{
	                		other = other + parseInt(series_sort[i].y);
	                		cat_other.push(series_sort[i].ng_name);
	                	}
	                }

	                var series_sort_new = sortArray(series_new, "y", -1);

	                var category = [];
	                var series = [];

	                for(var i = 0; i < series_sort_new.length;i++){
	                	var ser = series_sort_new[i].cat.split(' - ');
	                	if (ser.length == 1) {
	                		series.push({y:parseInt(series_sort_new[i].y),key:series_sort_new[i].cat});
	                		category.push(series_sort_new[i].cat);
	                	}else{
	                		if (ser[0] == ser[1]) {
	                			series.push({y:parseInt(series_sort_new[i].y),key:ser[0]});
	                			category.push(ser[0]);
	                		}else{
	                			series.push({y:parseInt(series_sort_new[i].y),key:series_sort_new[i].cat});
	                			category.push(series_sort_new[i].cat);
	                		}
	                	}
	                }

	                Highcharts.chart('container2', {
					    chart: {
					        renderTo: 'container2',
					        type: 'column'
					    },
					    title: {
					        text: 'ASSEMBLY PARETO DEFECT {{strtoupper($product)}} YCL4XX<br>'+datestitle,
					        style:{
					        	fontWeight:'bold'
					        }
					    },
					    tooltip: {
					        shared: true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailPareto2(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize: '15px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
							pareto:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	// showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}%',
									style:{
										fontSize: '14px'
									}
								},
								lineWidth: 3,
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
						},credits: {
							enabled: false
						},
					    xAxis: {
					        categories: category,
					        crosshair: true,
					        labels: {
					            format: "{value}",
					            style:{
					            	fontSize:'15px',
					            }
					        }
					    },
					    yAxis: [{
					        title: {
					            text: 'Total Defect',
					            style:{
					            	fontSize:'15px',
					            	fontWeight:'bold',
					            	color:'#fff'
					            }
					        },
					        labels: {
					            format: "{value}",
					            style:{
					            	fontSize:'13px',
					            }
					        }
					    }, {
					        title: {
					            text: 'Pareto',
					            style:{
					            	fontSize:'15px',
					            	fontWeight:'bold',
					            	color:'#fff'
					            }
					        },
					        minPadding: 0,
					        maxPadding: 0,
					        max: 100,
					        min: 0,
					        opposite: true,
					        labels: {
					            format: "{value}%",
					            style:{
					            	fontSize:'13px',
					            }
					        }
					    },],
					    series: [{
					        type: 'pareto',
					        name: 'Pareto',
					        yAxis: 1,
					        zIndex: 10,
					        baseSeries: 1,
					        tooltip: {
					            valueDecimals: 1,
					            valueSuffix: '%'
					        },
					        colorByPoint:false,
					        color:'#fff',
					    }, {
					        name: 'Total Defect',
					        type: 'column',
					        zIndex: 2,
					        data: series,
					        colorByPoint:false,
					        color:'#4287f5',
					    },
					    ]
					});
				}

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
}

function showModalDetailPareto(ng_name) {
	$("#loading").show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var tableBody = '';
	var index = 1;
	$.each(details, function(key, value) {
		var re = new RegExp(ng_name, 'g');
		if (value.ng_name.match(re)) {
			tableBody += '<tr>';
			tableBody += '<td>'+index+'</td>';
			tableBody += '<td>'+value.serial_number+'</td>';
			tableBody += '<td>'+value.model+'</td>';
			tableBody += '<td>'+value.location+'</td>';
			tableBody += '<td>'+value.ng_name+'</td>';
			tableBody += '<td>'+value.ongko+'</td>';
			var qty = 1;
			if (value.value_bawah == null) {
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td></td>';
				tableBody += '<td></td>';
			}else{
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td>'+value.value_bawah+'</td>';
				tableBody += '<td>'+value.value_atas+'</td>';
			}
			tableBody += '<td>'+(value.value_lokasi || "")+'</td>';
			var name = '';
			for(var i = 0; i < emp.length;i++){
				if (value.employee_id == emp[i].employee_id) {
					name = emp[i].name;
				}
			}
			tableBody += '<td>'+value.employee_id+'<br>'+name+'</td>';
			tableBody += '<td>'+value.created_at+'</td>';
			tableBody += '</tr>';
			index++;
		}
	});
	$('#tableDetailBody').append(tableBody);

	$('#tableDetail').DataTable({
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
			},
			]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 10,
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
	if (date_to_title == date_from_title) {
		$('#modalDetailTitle').html('Detail NG '+ng_name+'<br>Tanggal '+date_from_title);
	}else{
		$('#modalDetailTitle').html('Detail NG '+ng_name+'<br>Tanggal '+date_from_title+' - '+date_to_title);
	}
	$("#loading").hide();
	$('#modalDetail').modal('show');
}

function showModalDetailPareto2(ng_name) {
	$("#loading").show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var tableBody = '';
	var index = 1;
	$.each(details2, function(key, value) {
		var re = new RegExp(ng_name, 'g');
		if (value.ng_name.match(re)) {
			tableBody += '<tr>';
			tableBody += '<td>'+index+'</td>';
			tableBody += '<td>'+value.serial_number+'</td>';
			tableBody += '<td>'+value.model+'</td>';
			tableBody += '<td>'+value.location+'</td>';
			tableBody += '<td>'+value.ng_name+'</td>';
			tableBody += '<td>'+value.ongko+'</td>';
			var qty = 1;
			if (value.value_bawah == null) {
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td></td>';
				tableBody += '<td></td>';
			}else{
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td>'+value.value_bawah+'</td>';
				tableBody += '<td>'+value.value_atas+'</td>';
			}
			tableBody += '<td>'+(value.value_lokasi || "")+'</td>';
			var name = '';
			for(var i = 0; i < emp.length;i++){
				if (value.employee_id == emp[i].employee_id) {
					name = emp[i].name;
				}
			}
			tableBody += '<td>'+value.employee_id+'<br>'+name+'</td>';
			tableBody += '<td>'+value.created_at+'</td>';
			tableBody += '</tr>';
			index++;
		}
	});
	$('#tableDetailBody').append(tableBody);

	$('#tableDetail').DataTable({
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
			},
			]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 10,
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
	if (date_to_title2 == date_from_title2) {
		$('#modalDetailTitle').html('Detail NG '+ng_name+'<br>Tanggal '+date_from_title2);
	}else{
		$('#modalDetailTitle').html('Detail NG '+ng_name+'<br>Tanggal '+date_from_title2+' - '+date_to_title2);
	}
	$("#loading").hide();
	$('#modalDetail').modal('show');
}



$.date = function(dateObject) {
	var d = new Date(dateObject);
	var day = d.getDate();
	var month = d.getMonth() + 1;
	var year = d.getFullYear();
	if (day < 10) {
		day = "0" + day;
	}
	if (month < 10) {
		month = "0" + month;
	}
	var date = year + "-" + month + "-" + day;

	return date;
};

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

</script>
@endsection