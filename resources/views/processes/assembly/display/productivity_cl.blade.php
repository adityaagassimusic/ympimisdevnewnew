@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-10" style="padding-left: 0px;padding-right: 5px;">
					<div id="container2" class="container2" style="width: 100%;height: 45vh;"></div>
				</div> -->

				<div id="period_title" class="col-xs-6" style="background-color: rgba(248,161,63,0.9);">
	                <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
	            </div>
	            <div class="col-xs-3">
	                <div class="input-group date">
	                    <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
	                        <i class="fa fa-calendar"></i>
	                    </div>
	                    <input type="text" class="form-control pull-right" id="date_from" name="date_from"
	                        onchange="fetchChart()">
	                </div>
	            </div>
	            <div class="col-xs-3">
	                <div class="input-group date">
	                    <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
	                        <i class="fa fa-calendar"></i>
	                    </div>
	                    <input type="text" class="form-control pull-right" id="date_to" name="date_to"
	                        onchange="fetchChart()">
	                </div>
	            </div>

            	<div class="col-xs-12" id="detail_container"></div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		training = null;
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
			todayHighlight: true
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		// setInterval(fetchChart, 10000);
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

	function fetchChart(){
		$('#loading').show();

       var date_from = $('#date_from').val();
       var date_to = $('#date_to').val();

		var data = {
			origin_group_code:'{{$origin_group_code}}',
			date_from:date_from,
			date_to:date_to
		}

		$.get('{{ url("fetch/assembly/productivity") }}', data, function(result, status, xhr) {
			if(result.status){

				$("#loading").hide();
                $('#title_text').text('Operator Productivity Clarinet');
                
				var categories = [];
				var tact_times = [];
				var std = 24;

				var target = [];
				// $('#tact_time_average').html(std);

				var reason_wscr = [];
                var div_detail = "";
                $('#detail_container').html("");

                var operator = [];
                var names = [];

                $.each(result.average, function(key, value) {
                	operator.push(value.operator_id);
                	names.push({operator_id:value.operator_id,name:value.name});
                });

                var operator_unik = operator.filter(onlyUnique);
                for(var i = 0; i < operator_unik.length;i++){
                	div_detail = '<div style="height:45vh;" class="col-xs-6" id="' + operator_unik[i] + '"></div>';
                   	$('#detail_container').append(div_detail);
                }

                var data_all = [];

                for(var i = 0; i < operator_unik.length;i++){
                	var array_data = [];
                	for(var j = 0; j < result.average.length;j++){
                		if (result.average[j].operator_id == operator_unik[i]) {
                			array_data.push({
                                "operator_id": result.average[j].operator_id,
                                "average" : result.average[j].tact_time,
                                "tanggal": result.average[j].tanggal
                            });
                		}
                	}
                	data_all.push(array_data);
                }


                for(var i = 0; i < operator_unik.length;i++){
                	var series = [];
                	var categories = [];
                	for(var j = 0; j < data_all[i].length;j++){
                		if(data_all[i][j].operator_id == operator_unik[i]){
                			if (parseFloat(data_all[i][j].average) > 0 && parseFloat(data_all[i][j].average) < 35) {
		                		categories.push(data_all[i][j].tanggal);
		                		series.push(parseFloat(data_all[i][j].average));
                			}
                		}
                	}

                	var name = '';
                	for(var j = 0; j < names.length;j++){
                		if (names[j].operator_id == operator_unik[i]) {
                			name = names[j].name;
                		}
                	}

            		Highcharts.chart(operator_unik[i], {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: ''+name,
                            style: {
                                fontSize: '14px'
                            }
                        },
                        subtitle: {
                            text: operator_unik[i],
                            style: {
                                fontSize: '10px'
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories:categories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '12px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											// showDetail(result.date, event.point.category);
										}
									}
								},
							}
						},
                        series: [
                        {
                            name: 'Average',
                            color: '#90ee7e',
                            data: series,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return (this.y).toFixed(1);
                                }
                            }
                        },
                        {
                            name: 'Average Line',
                            type: 'spline',
                            color: 'blue',
                            data: series
                        }]
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

function showModal(name,cat,employee_id) {
	$("#detail_penyebab").text("");
	$("#detail_aksi").text("");
	$("#detail_evidence").text("");

	cancelAll();

	$('#employee_id').html(employee_id);
	$('#name').html(name);

	var op_training = [];

	if (training.length > 0) {
		$.each(training, function(key, value){
			op_training.push(value.operator.split('-')[0]);
		});
	}

	if (op_training.includes(employee_id)) {
		$('#modal_hasil').show();
		$('#modal_training').hide();
		$.each(training, function(key, value){
			if (employee_id == value.operator.split('-')[0]) {
				photos = '<img style="width:400px" src="http://10.109.52.1:887/miraidev/public/images/training/'+value.evidence+'" class="user-image" alt="User image">';
				$("#detail_penyebab").append(value.detail);
				$("#detail_aksi").append(value.action);
				$("#detail_evidence").html(photos);
			}
		});
	}else{
		$('#modal_hasil').hide();
		$('#modal_training').show();
	}

	$('#myModal').modal('show');
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

function saveTraining(){
	$("#loading").show();

	var formData = new FormData();
	formData.append('operator', $('#employee_id').text()+'-'+$('#name').text());
	formData.append('deskripsi',  CKEDITOR.instances.penyebab.getData());
	formData.append('action',  CKEDITOR.instances.aksi.getData());
	formData.append('evidence', $('#evidence').prop('files')[0]);

	$.ajax({
		url:"{{ url('post/assembly/eff/training') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success: function (response) {
			$("#loading").hide();
			openSuccessGritter('Success', response.message);
			fetchChart();
			cancelAll();
			$('#myModal').modal('hide');
		},
		error: function (response) {
			openErrorGritter('Error!', response.message);
		},
	})	
}

function cancelAll() {
	$("#aksi").html(CKEDITOR.instances.aksi.setData(''));
	$("#penyebab").html(CKEDITOR.instances.penyebab.setData(''));
	$('#evidence').val('');
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