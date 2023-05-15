@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
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

	.gambar {
	    width: 350px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }

	  .select2-search__field {
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
			<span style="font-size: 40px">Loading. Please Wait. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="form-group">
					<select class="form-control select3" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<button class="btn btn-success pull-left" onclick="fetchLotStatus()" style="font-weight: bold;">
					Search
				</button>
			</div>
			<div class="col-xs-2" style="padding-left: 0px">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 12.5px"></div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-left: 0px;padding-top: 5px">
			<div id="container" style="width: 100%;height: 85vh"></div>
		</div>
	</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center><h3 style="font-weight: bold;color:black ;font-size: 20px" id="judul_detail"></h3></center>
					<div class="col-md-12" id="bodyDetail">
			          <table class="table table-bordered table-striped" style="font-size:15px" id="tableDetail">
			          	<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:15px">
			          		<tr>
			          			<th>Date</th>
			          			<th>Location</th>
			          			<th>Material</th>
			          			<th>Vendor</th>
			          			<th>Invoice</th>
			          			<th>Inspection Level</th>
			          			<th>Qty Rec</th>
			          			<th>Qty Check</th>
			          			<th>Qty Repair</th>
			          			<th>Qty Return</th>
			          			<th>Total NG</th>
			          			<th>Status</th>
			          		</tr>
			          	</thead>
			          	<tbody id="bodyTableDetail">
			          		
			          	</tbody>
			          </table>
			        </div>
				</div>
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
		</div>
	</div>
</div>
</section>
@endsection
@section('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<!-- <script src="{{ url("js/pareto.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		$('.select3').select2();
		fetchLotStatus();
		setInterval(fetchLotStatus, 300000);


	});

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
		fetchSelectMaterial();
		// fetchLotStatus();
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
		// fetchLotStatus();
	}

	function fetchSelectMaterial() {
		var data = {
			vendor:$('#vendor').val()
		}

		$.get('{{ url("fetch/qa/display/incoming/material_select") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#materialSelect').html('');
					var materialSelect = '';
					$.each(result.material_select, function(key,value){
						materialSelect += '<option value="'+value.material_number+'">'+value.material_number+' - '+value.material_description+'</option>';
					});
					$('#materialSelect').append(materialSelect);
				}
			}
		});
	}

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
	});

	function fetchLotStatus() {
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:'{{$vendor_shortname}}',
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/ng_rate_vendor") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<span><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</span>');

					var categories = [];
					var checkes = [];
					var qty_ng = [];
					var persen = [];
					var qty_ng_ympi = [];
					var qty_check_ympi = [];
					var persen_ympi = [];

					$.each(result.ng_rate, function(key,value){
						categories.push(value.check_date);
						checkes.push(parseInt(value.qty_check));
						qty_ng.push(parseInt(value.qty_ng));
						persen.push(parseFloat(value.ng_ratio));

						var qty_ng_ympis = 0;
						var qty_check_ympis = 0;
						var persen_ympis = 0;

						for(var i=0; i < result.ng_rate_ympi_all.length;i++){
							var re = new RegExp( result.ng_rate_ympi_all[i][0].serial_number,'g');
							if (value.serial_number.match(re)) {
								qty_ng_ympis = qty_ng_ympis + parseInt(result.ng_rate_ympi_all[i][0].qty_ng);
								qty_check_ympis = qty_check_ympis + parseInt(result.ng_rate_ympi_all[i][0].qty_check);
								persen_ympis = persen_ympis + parseInt(result.ng_rate_ympi_all[i][0].ng_ratio);
							}
						}

						qty_ng_ympi.push(qty_ng_ympis);
						qty_check_ympi.push(qty_check_ympis);
						persen_ympi.push(parseFloat(persen_ympis.toFixed(2)));
					});

					Highcharts.chart('container', {
					    chart: {
					        zoomType: 'xy'
					    },
					    title: {
					        text: 'NG RATE '+'{{$vendor_name}}'
					    },
					    subtitle: {
					        text: result.firstDateTitle+' - '+result.lastDateTitle
					    },
					    xAxis: [{
					        categories: categories,
					        crosshair: true
					    }],
					    yAxis: [{ 
					        labels: {
					            format: '{value}',
					            style: {
					                color: '#fff'
					            }
					        },
					        title: {
					            text: 'Qty',
					            style: {
					                color: '#fff'
					            }
					        }
					    }, { 
					        title: {
					            text: 'NG Rate',
					            style: {
					                color: '#fff'
					            }
					        },
					        labels: {
					            format: '{value}%',
					            style: {
					                color: '#fff'
					            }
					        },
					        opposite: true
					    }],
					    tooltip: {
					        shared: true
					    },
					    legend: {
					        enabled:true,
					         reversed: true
					    },
					    credits:{
					    	enabled:false
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
							},
							spline:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}%',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
							},
						},
					    series: [
					    {
					        name: 'NG Incoming YMPI',
					        type: 'column',
					        data: qty_ng_ympi,
					        color: '#e75959'

					    },{
					        name: 'Total Check Incoming YMPI',
					        type: 'column',
					        data: qty_check_ympi,
					        color: '#e1e5ea'

					    },{
					        name: 'NG By Vendor',
					        type: 'column',
					        data: qty_ng,
					        color: '#ffdd66'

					    },{
					        name: 'Total Check By Vendor',
					        type: 'column',
					        data: checkes,
					        color: '#4bc16b'

					    }, {
					        name: 'NG Rate By Vendor',
					        type: 'spline',
					        data: persen,
					        color: '#ed151d',
					        yAxis: 1,
					        tooltip: {
					            valueSuffix: '%'
					        }
					    },{
					        name: 'NG Rate Incoming YMPI',
					        type: 'spline',
					        data: persen_ympi,
					        color: '#87171b',
					        dashStyle: 'shortdot',
					        yAxis: 1,
					        tooltip: {
					            valueSuffix: '%'
					        }
					    }]
					});
				}
			}
		});
	}

	function showModalDetail(categories) {
		$('#loading').show();
		$('#judul_detail').html("");
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
			categories:categories
		}

		$.get('{{ url("fetch/qa/display/incoming/ng_rate/detail") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#judul_detail').html("Detail NG Rate Incoming Check QA on "+categories);
					$('#bodyTableDetail').html("");
					var bodyDetail = "";
					var total_ng = 0;
					var total_check= 0;
					$.each(result.detail, function(key,value){
						if (value.location == 'wi1') {
				  			var loc = 'Woodwind Instrument (WI) 1';
				  		}else if (value.location == 'wi2') {
				  			var loc = 'Woodwind Instrument (WI) 2';
				  		}else if(value.location == 'ei'){
				  			var loc = 'Educational Instrument (EI)';
				  		}else if(value.location == 'sx'){
				  			var loc = 'Saxophone Body';
				  		}else if (value.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						bodyDetail += '<tr>';
						bodyDetail += '<td>'+value.created+'</td>';
						bodyDetail += '<td>'+loc+'</td>';
						bodyDetail += '<td>'+value.material_number+' - '+value.material_description+'</td>';
						bodyDetail += '<td>'+value.vendor+'</td>';
						bodyDetail += '<td>'+value.invoice+'</td>';
						bodyDetail += '<td>'+value.inspection_level+'</td>';
						bodyDetail += '<td>'+value.qty_rec+'</td>';
						bodyDetail += '<td>'+value.qty_check+'</td>';
						bodyDetail += '<td>'+value.repair+'</td>';
						bodyDetail += '<td>'+value.return+'</td>';
						bodyDetail += '<td>'+value.total_ng+'</td>';
						bodyDetail += '<td>'+value.status_lot+'</td>';
						bodyDetail += '</tr>';

						total_ng = total_ng + parseInt(value.total_ng);
						total_check = total_check + parseInt(value.qty_check);
					});

					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL NG</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_ng+'</td>';
					bodyDetail += '</tr>';
					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL CHECK</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_check+'</td>';
					bodyDetail += '</tr>';
					bodyDetail += '</tr>';
					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">NG RATE</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+Math.round((total_ng / total_check) * 100)+' %</td>';
					bodyDetail += '</tr>';

					$('#bodyTableDetail').append(bodyDetail);

					$('#modalDetail').modal('show');
					$('#loading').hide();
				}
			}
		});
	}

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
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection