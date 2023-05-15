@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	#main tbody>tr>td {
		text-align:center;
	}

	thead>tr>th {
		background-color: white;
		text-align: center;
		/*font-size: 1vw;*/
	}

	table{
		table-layout: fixed;
	}

	td{
		word-wrap:break-word
	}

	tbody>tr>td {
		/*color: white;*/
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> {{ $jpn }}</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<!-- <div class="col-xs-12">
			<div class="row" style="margin:0px;">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
						</div>
					</div>

					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="button" onclick="">Update Schedule</button>
						</div>
					</div>
			</div>
		</div> -->


		<div class="col-xs-12">
			<div class="col-xs-2">
				<div class="input-group date" style="margin-top: 10px;">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
				</div>
			</div>

			<div class="col-xs-2">
				<div class="input-group date" style="margin-top: 10px;">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tanggal22" name="tanggal22" placeholder="Select Date To">
				</div>
			</div>

			<div class="col-xs-2">
				<div class="input-group date" style="margin-top: 10px;">
				<button type="button" class="btn btn-warning" style="margin-right: 20px" onclick="makeSchedule()">Calculate</button>
				<button type="button" class="btn btn-success" style="margin-right: 20px" onclick="saveSchedule()">Save</button>
			</div>
			</div>
			

			<!-- <div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 1</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 2</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> -->  

			<!-- <div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 3</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 4</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div>  -->

			<!-- <div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 5</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 6</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div>  -->

			<!-- <div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 7</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 8</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> --> 

			<!-- <div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 9</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>   -->
				<!-- <div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 11</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  --> 
			</div> 


		<!-- <div class="col-xs-12">
			<div class="col-xs-2">
				<button type="button" class="btn btn-warning" style="margin-right: 20px" onclick="makeSchedule()">Calculate</button>
				<button type="button" class="btn btn-success" style="margin-right: 20px" onclick="saveSchedule()">Save</button>
			</div>
		</div><br><br> -->

		<div class="col-xs-12">

			<div id="chartplan">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div id="chartplanMesin2">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div id="chartplanMesin">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div class="col-xs-6" id="injeksiVsAssyBlue">
				
			</div>

			<div class="col-xs-6" id="injeksiVsAssyGreen">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div class="col-xs-6" id="injeksiVsAssyPink">
				
			</div>

			<div class="col-xs-6" id="injeksiVsAssyRed">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div class="col-xs-6" id="injeksiVsAssyBrown">
				
			</div>

			<div class="col-xs-6" id="injeksiVsAssyIvory">
				
			</div>

		</div>

		<div class="col-xs-12">

			<div  id="injeksiVsAssyYrf">
				
			</div>

			

		</div>

		<div class="col-xs-12">

			<div class="col-xs-3" id="headblue">
				
			</div>

			<div class="col-xs-3" id="blokblue">
				
			</div>

			<div class="col-xs-3" id="mjblue">
				
			</div>

			<div class="col-xs-3" id="footblue">
				
			</div>

		</div>

		<div class="modal fade" id="myModal">
          <div class="modal-dialog modal-lg" >
            <div class="modal-content">
              <div class="modal-header">
                <center><h4 class="modal-title">Make Schedule</h4></center>
              </div>
              <div class="modal-body">
                <div id="progressbar2">
              <center>
               <i class="fa fa-refresh fa-spin" style="font-size: 6em;"></i> 
               <br><h4>Loading ...</h4>
             </center>
           </div>
              </div>
              
            </div>
            <!-- /.modal-content -->
          </div>
      </div>

		
		<div class="col-xs-12">
			<br><br>
			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle;width:150px;">Mesin 1</td	>

				</tr>
				<tr id="HeadMESIN1">

				</tr>
				<tr id="BodyMESIN1">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle;width:150px;">Mesin 2</td>

				</tr>
				<tr id="HeadMESIN2">

				</tr>
				<tr id="BodyMESIN2">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 3</td>

				</tr>
				<tr id="HeadMESIN3">

				</tr>
				<tr id="BodyMESIN3">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 4</td>

				</tr>
				<tr id="HeadMESIN4">

				</tr>
				<tr id="BodyMESIN4">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 5</td>

				</tr>
				<tr id="HeadMESIN5">

				</tr>
				<tr id="BodyMESIN5">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 6</td>

				</tr>
				<tr id="HeadMESIN6">

				</tr>
				<tr id="BodyMESIN6">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 7</td>

				</tr>
				<tr id="HeadMESIN7">

				</tr>
				<tr id="BodyMESIN7">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 8</td>

				</tr>
				<tr id="HeadMESIN8">

				</tr>
				<tr id="BodyMESIN8">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 9</td>

				</tr>
				<tr id="HeadMESIN9">

				</tr>
				<tr id="BodyMESIN9">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main" hidden="">
				<tr>
					<td  rowspan="3" style="text-align: center;
					vertical-align: middle; width:150px;">Mesin 11</td>

				</tr>
				<tr id="HeadMESIN11">

				</tr>
				<tr id="BodyMESIN11">


				</tr>
			</table>
		</div>
		
		
	</div>


      
          <!-- /.modal-dialog -->
        
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$('.datepicker').datepicker({
		
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
	});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");
		getMesin();
		// makeSchedule();
	});

	function unique(list) {
		var result = [];
		$.each(list, function(i, e) {
			if ($.inArray(e, result) == -1) result.push(e);
		});
		return result;
	}

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

	var mesin1_r2 = [];
	var MESIN2_r2 = [];
	var MESIN3_r2 = [];
	var MESIN4_r2 = [];
	var MESIN5_r2 = [];
	var MESIN6_r2 = [];
	var MESIN7_r2 = [];
	var MESIN8_r2 = [];
	var MESIN9_r2 = [];
	var MESIN11_r2 = [];

	var HeadMesin1 = '';
	var BodyMesin1 = '';

	var HeadMesin2 = '';
	var BodyMesin2 = '';

	var HeadMesin3 = '';
	var BodyMesin3 = '';

	var HeadMesin4 = '';
	var BodyMesin4 = '';

	var HeadMesin5 = '';
	var BodyMesin5 = '';

	var HeadMesin6 = '';
	var BodyMesin6 = '';

	var HeadMesin7 = '';
	var BodyMesin7 = '';

	var HeadMesin8 = '';
	var BodyMesin8 = '';

	var HeadMesin9 = '';
	var BodyMesin9 = '';

	var HeadMesin11 = '';
	var BodyMesin11 = '';

	var max = 0;

	var dateMax = [];

	var PostMESIN1 = [];
	var PostMESIN2 = [];
	var PostMESIN3 = [];
	var PostMESIN4 = [];
	var PostMESIN5 = [];
	var PostMESIN6 = [];
	var PostMESIN7 = [];
	var PostMESIN8 = [];
	var PostMESIN9 = [];
	var PostMESIN11 = [];

	var ganti = [];

	function makeSchedule() {

        $('#myModal').modal({backdrop: 'static', keyboard: false});
		$("#myModal").modal('show');

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}



		$.get('{{ url("fetch/Schedulepart") }}',data , function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var TargetAllMesin = [];
					var Mesin1detail = [];

					var MESIN1 = [];
					var MESIN2 = [];
					var MESIN3 = [];
					var MESIN4 = [];
					var MESIN5 = [];
					var MESIN6 = [];
					var MESIN7 = [];
					var MESIN8 = [];
					var MESIN9 = [];
					var MESIN11 = [];

					var MesinQty =[];
					var a = [];
					var unik = [];
					var judul = "";

					for (var i = 0; i < 35; i++) {
						mesin1_r2.push([]);
						MESIN2_r2.push([]);
						MESIN3_r2.push([]);
						MESIN4_r2.push([]);
						MESIN5_r2.push([]);
						MESIN6_r2.push([]);
						MESIN7_r2.push([]);
						MESIN8_r2.push([]);
						MESIN9_r2.push([]);
						MESIN11_r2.push([]);
					}

					

					// for (var i = 0; i < result.part.length; i++) {
					// 	if (String(result.part[i].working).match(/MESIN1.*/)) {				
					// 		Mesin1.push([result.part[i].part,result.part[i].part_code,result.part[i].color,result.part[i].target_hako_qty]);		
					// 	}						
					// }

					// for (var i = 0; i < Mesin1.length; i++) {
					// 	alert(Mesin1[i][0])
					// }

					// for (var i = 0; i < result.part.length; i++) {
						
					// 	if (String(result.part[i].working).match(/MESIN1.*/)) {

					// 	for (var qty = result.part[0].target_hako_qty; qty > result.part[0].max_day; qty--) {
					// 	qty -= result.part[0].max_day;
					// 	Mesin1.push([result.part[0].part,result.part[0].part_code,result.part[0].color,result.part[0].target_hako_qty, qty]);
					// 	}		

					// 	}						
					// }
					// var qty = result.part[0].target_hako_qty;
					// var qty = 0;
					// var max = result.part[0].max_day;

					// for (var i = 0; i < 10; i++) {						
					// 	qty += parseInt(result.part[0].max_day);
					// 	if (qty >= result.part[0].target_hako_qty) {
					// 		qty = qty - result.part[0].target_hako_qty
					// 	}
					// 	Mesin1.push(qty, max);
					// 	// if (qty > 10 ) {
					// 	// 	break;
					// 	// }	
					// }

					

						// var d = result.part[0].target_hako_qty;

						// while (d - result.part[0].max_day >= result.part[0].max_day) {
						// 	alert(d + "-"+result.part[0].max_day)

					 // 	 Mesin1.push([result.part[0].part,result.part[0].part_code,result.part[0].color,result.part[0].target_hako_qty, d]);
					 // 	 d-=result.part[0].max_day;
						// }

						$.each(result.part, function(key, value) {
							var qty = value.target_hako_qty;

							if (value.part==value.part) {

								while(qty >= value.max_day){						
									TargetAllMesin.push([value.part_code,value.color,value.part,parseInt(value.max_day),value.working,value.max_day,value.cycle,value.shoot]);
									if (qty > value.max_day) {
										qty-=value.max_day;
									}else{
										qty=qty;
									}

								}
								TargetAllMesin.push([value.part_code,value.color,value.part,qty,value.working,value.max_day,value.cycle,value.shoot]);
							}
						});


						console.table(result.part);


						for (var i = 0; i < TargetAllMesin.length; i++) {
							a = TargetAllMesin[i][4];
							var m = "";

							if (a.match(/,.*/) ) {
								m = a.split(',');

								for (var y = 0; y < m.length; y++) {
									eval(m[y]).push([TargetAllMesin[i][0],TargetAllMesin[i][1],TargetAllMesin[i][2],TargetAllMesin[i][3],TargetAllMesin[i][5],TargetAllMesin[i][6],TargetAllMesin[i][7],(((TargetAllMesin[i][3] / TargetAllMesin[i][7]) * TargetAllMesin[i][6]) / 60)]);
								}

						// alert(m[0])
					}else{

						eval(a).push([TargetAllMesin[i][0],TargetAllMesin[i][1],TargetAllMesin[i][2],TargetAllMesin[i][3],TargetAllMesin[i][5],TargetAllMesin[i][6],TargetAllMesin[i][7],(((TargetAllMesin[i][3] / TargetAllMesin[i][7]) * TargetAllMesin[i][6]) / 60)]);
					}
				}

				console.table(TargetAllMesin);

				
				// BodyMesin1 += '<td style="padding: 0px">';
				// BodyMesin1 +='<table border="1" width="100%" >';
				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (MESIN1[i][3] != MESIN1[i][4]) {	

				// 		BodyMesin1 +='<tr>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][0] +' - '+MESIN1[i][1]+'</td>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
				// 		BodyMesin1 +='<td>'+((MESIN1[i][3] / MESIN1[i][6]) * MESIN1[i][5]) / 60+'</td>';
				// 		BodyMesin1 +='</tr>';

				// 	}					
				// }
				// BodyMesin1 +='</table>';
				// BodyMesin1 +='</td>';

				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (MESIN1[i][3] == MESIN1[i][4]){
				// 		BodyMesin1 += '<td style="padding: 0px">';
				// 		BodyMesin1 +='<table border="1" width="100%" >';	
				// 		BodyMesin1 +='<tr>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][0] +' - '+MESIN1[i][1]+'</td>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
				// 		BodyMesin1 +='<td>'+((MESIN1[i][3] / MESIN1[i][6]) * MESIN1[i][5]) / 60	+'</td>';
				// 		BodyMesin1 +='</tr>';
				// 		BodyMesin1 +='</table>';
				// 		BodyMesin1 +='</td>';
				// 	}
				// }

				var x = 0;
				// var mesin1_r = [[],[],[],[],[],[],[]];

				console.table(TargetAllMesin);
				
				var z = 0;
				var j = 0;
				var c = 0;
				var d = 0;
				var lp = 0;

				// ---------------- KEEP ------------------

				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (typeof MESIN1[i+1] === 'undefined') {
				// 		mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);
				// 	} else {
				// 		if (((MESIN1[i][7] + MESIN1[i+1][7]) / 60) > 23.5) {
				// 			mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);

				// 			x+=1;
				// 		} else {

				// 			z += MESIN1[i][7];


				// 			if (((z + MESIN1[i+2][7]) / 60) > 23.5) {
				// 				var d = Math.floor((((23.5 - ((MESIN1[i][7] + MESIN1[i+1][7]) / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
				// 				console.log(d);
				// 				MESIN1[i+2][3] -= d;
				// 				mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);

				// 			}else{
				// 				mesin1_r[x].push([MESIN1[i+2][2], d]);
				// 			}
				// 		}
				// 	}
				// }

				// console.log(mesin1_r);

				// ---------------- END KEEP ------------------

				// ---------------- Mesin1 KEEP ------------------
				// x=0;
				// c=0;
				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (typeof MESIN1[i+1] === 'undefined') {
				// 		mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN1[i][7];
				// 		}
				// 		if (((j + MESIN1[i+1][7]) / 60) > 23.5) {
				// 			mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
				// 				mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], d]);
				// 				MESIN1[i+1][3] -= d;
				// 				MESIN1[i+1][7] = Math.floor((Math.floor(MESIN1[i+1][3] / MESIN1[i+1][6]) * MESIN1[i+1][5]) / 60);
				// 			} else {
				// 				if (MESIN1[i][3] != MESIN1[i][4]) {

				// 					// ------minus


				// 					if ((Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]) < 0) {

				// 						mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i+1][2], 
				// 						(Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
				// 						]);

				// 						MESIN1[i+1][3] -= 0;
				// 					}else{

				// 						mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i+1][2], 
				// 						(Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
				// 						]);


				// 					MESIN1[i+1][3] -= (Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]);
				// 				}

				// 				// ------ end minus

				// 				}

				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN1[i][7] + MESIN1[i+1][7];
				// 			// d = MESIN1[i][3] + MESIN1[i+1][3];
				// 			mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }
				// console.log(mesin1_r2);

				// ---------------- End Mesin1 KEEP ------------------

				
				// ---------------- Mesin11 KEEP ------------------
				// x=0;
				// c=0;
				// for (var i = 0; i < MESIN11.length; i++) {
				// 	if (typeof MESIN11[i+1] === 'undefined') {
				// 		MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN11[i][7];
				// 		}
				// 		if (((j + MESIN11[i+1][7]) / 60) > 23.5) {
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
				// 				MESIN11_r2[x].push([MESIN11[i+1][2], d]);
				// 				MESIN11[i+1][3] -= d;
				// 				MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
				// 			} else {

				// 				// ---------------- INI PENYEBABNYA -------------------
				// 				if (MESIN11[i][3] != MESIN11[i][4]) {

				// 					if ((Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]) < 0) {

				// 						MESIN11_r2[x].push([MESIN11[i+1][2], 
				// 						(Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
				// 						]);

				// 						MESIN11[i+1][3] -= 0;
				// 					}else{

				// 						MESIN11_r2[x].push([MESIN11[i+1][2], 
				// 						(Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
				// 						]);


				// 					MESIN11[i+1][3] -= (Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]);
				// 				}

				// 				}

				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN11[i][7] + MESIN11[i+1][7];
				// 			console.log(j);
				// 			// d = MESIN11[i][3] + MESIN11[i+1][3];
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }

				// ---------------- End Mesin11 KEEP ------------------

				// console.log(MESIN11_r2);
				
				// x=0;
				// for (var i = 0; i < MESIN11.length; i++) {
				// 	if (typeof MESIN11[i+1] === 'undefined') {
				// 		MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN11[i][7];
				// 		}

				// 		if ((j  / 60) > 23.5) {
				// 			lp++;
				// 			for (var lpp = 0; lpp < lp; lpp++) {
				// 			 alert(	MESIN11[i+lpp][2]);
				// 			}

				// 		}
				// 		if (((j + MESIN11[i+1][7]) / 60) > 23.5) {
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
				// 				MESIN11_r2[x].push([MESIN11[i+1][2], d]);
				// 				MESIN11[i+1][3] -= d;
				// 				MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
				// 			} else {

				// 				// ---------------- INI PENYEBABNYA -------------------


				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN11[i][7] + MESIN11[i+1][7];
				// 			console.log(j);
				// 			// d = MESIN11[i][3] + MESIN11[i+1][3];
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }


				// ---------------- Mesin1  ------------------
				x=0;
				c=0;

				for (var i = 0; i < MESIN1.length; i++) {
					if (typeof MESIN1[i+1] === 'undefined') {
						mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN1[i][7];
						}

						if (((j + MESIN1[i+1][7]) / 60) > 23.5) {
							mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
								mesin1_r2[x].push([(MESIN1[i+1][0]+' - '+MESIN1[i+1][1]),MESIN1[i+1][2], d]);
								MESIN1[i+1][3] -= d;
								MESIN1[i+1][7] = Math.floor((Math.floor(MESIN1[i+1][3] / MESIN1[i+1][6]) * MESIN1[i+1][5]) / 60);
							} else {
								if (MESIN1[i][3] != MESIN1[i][4]) {
									
									// ------minus

									if ((Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]) < 0) {

										mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i+1][2], 
											(Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
											]);

										MESIN1[i+1][3] -= 0;
									}else{

										mesin1_r2[x].push([(MESIN1[i+1][0]+' - '+MESIN1[i+1][1]),MESIN1[i+1][2], 
											(Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
											]);

										
										MESIN1[i+1][3] -= (Math.floor(((((23.5 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]);
										MESIN1[i+1][7] = Math.floor(MESIN1[i+1][3] / MESIN1[i+1][6] * MESIN1[i+1][5] / 60);
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN1[i][7] + MESIN1[i+1][7];
							// d = MESIN1[i][3] + MESIN1[i+1][3];
							mesin1_r2[x].push([(MESIN1[i][0]+' - '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
							c = 1;
						}
					}

				}

				console.log(mesin1_r2);

				// console.table(MESIN1);

				// ---------------- End Mesin1  ------------------

				// ---------------- MESIN2  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN2.length; i++) {
					if (typeof MESIN2[i+1] === 'undefined') {
						MESIN2_r2[x].push([(MESIN2[i][0]+' - '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN2[i][7];
						}
						if (((j + MESIN2[i+1][7]) / 60) > 23.5) {
							MESIN2_r2[x].push([(MESIN2[i][0]+' - '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN2[i+1][5]) * MESIN2[i+1][6];
								MESIN2_r2[x].push([(MESIN2[i+1][0]+' - '+MESIN2[i+1][1]),MESIN2[i+1][2], d]);
								MESIN2[i+1][3] -= d;
								MESIN2[i+1][7] = Math.floor((Math.floor(MESIN2[i+1][3] / MESIN2[i+1][6]) * MESIN2[i+1][5]) / 60);
							} else {
								if (MESIN2[i][3] != MESIN2[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6]) < 0) {

										MESIN2_r2[x].push([(MESIN2[i][0]+' - '+MESIN2[i][1]),MESIN2[i+1][2], 
											(Math.floor(((((23.5 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6])
											]);

										MESIN2[i+1][3] -= 0;
									}else{

										MESIN2_r2[x].push([(MESIN2[i+1][0]+' - '+MESIN2[i+1][1]),MESIN2[i+1][2], 
											(Math.floor(((((23.5 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6])
											]);

										
										MESIN2[i+1][3] -= (Math.floor(((((23.5 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6]);
										MESIN2[i+1][7] = MESIN2[i+1][3] / MESIN2[i+1][6] * MESIN2[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN2[i][7] + MESIN2[i+1][7];
							// d = MESIN2[i][3] + MESIN2[i+1][3];
							MESIN2_r2[x].push([(MESIN2[i][0]+' - '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN2_r2);
				// console.table(MESIN2);

				// ---------------- End MESIN2  ------------------

				// ---------------- MESIN3  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN3.length; i++) {
					if (typeof MESIN3[i+1] === 'undefined') {
						MESIN3_r2[x].push([(MESIN3[i][0]+' - '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN3[i][7];
						}
						if (((j + MESIN3[i+1][7]) / 60) > 23.5) {
							MESIN3_r2[x].push([(MESIN3[i][0]+' - '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN3[i+1][5]) * MESIN3[i+1][6];
								MESIN3_r2[x].push([(MESIN3[i+1][0]+' - '+MESIN3[i+1][1]),MESIN3[i+1][2], d]);
								MESIN3[i+1][3] -= d;
								MESIN3[i+1][7] = Math.floor((Math.floor(MESIN3[i+1][3] / MESIN3[i+1][6]) * MESIN3[i+1][5]) / 60);
							} else {
								if (MESIN3[i][3] != MESIN3[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6]) < 0) {

										MESIN3_r2[x].push([(MESIN3[i][0]+' - '+MESIN3[i][1]),MESIN3[i+1][2], 
											(Math.floor(((((23.5 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6])
											]);

										MESIN3[i+1][3] -= 0;
									}else{

										MESIN3_r2[x].push([(MESIN3[i+1][0]+' - '+MESIN3[i+1][1]),MESIN3[i+1][2], 
											(Math.floor(((((23.5 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6])
											]);

										
										MESIN3[i+1][3] -= (Math.floor(((((23.5 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6]);
										MESIN3[i+1][7] = MESIN3[i+1][3] / MESIN3[i+1][6] * MESIN3[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN3[i][7] + MESIN3[i+1][7];
							// d = MESIN3[i][3] + MESIN3[i+1][3];
							MESIN3_r2[x].push([(MESIN3[i][0]+' - '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN3_r2);

				// ---------------- End MESIN3  ------------------

				// ---------------- MESIN4  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN4.length; i++) {
					if (typeof MESIN4[i+1] === 'undefined') {
						MESIN4_r2[x].push([(MESIN4[i][0]+' - '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN4[i][7];
						}
						if (((j + MESIN4[i+1][7]) / 60) > 23.5) {
							MESIN4_r2[x].push([(MESIN4[i][0]+' - '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN4[i+1][5]) * MESIN4[i+1][6];
								MESIN4_r2[x].push([(MESIN4[i+1][0]+' - '+MESIN4[i+1][1]),MESIN4[i+1][2], d]);
								MESIN4[i+1][3] -= d;
								MESIN4[i+1][7] = Math.floor((Math.floor(MESIN4[i+1][3] / MESIN4[i+1][6]) * MESIN4[i+1][5]) / 60);
							} else {
								if (MESIN4[i][3] != MESIN4[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6]) < 0) {

										MESIN4_r2[x].push([(MESIN4[i][0]+' - '+MESIN4[i][1]),MESIN4[i+1][2], 
											(Math.floor(((((23.5 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6])
											]);

										MESIN4[i+1][3] -= 0;
									}else{

										MESIN4_r2[x].push([(MESIN4[i+1][0]+' - '+MESIN4[i+1][1]),MESIN4[i+1][2], 
											(Math.floor(((((23.5 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6])
											]);

										
										MESIN4[i+1][3] -= (Math.floor(((((23.5 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6]);
										MESIN4[i+1][7] = MESIN4[i+1][3] / MESIN4[i+1][6] * MESIN4[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN4[i][7] + MESIN4[i+1][7];
							// d = MESIN4[i][3] + MESIN4[i+1][3];
							MESIN4_r2[x].push([(MESIN4[i][0]+' - '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN4_r2);

				// ---------------- End MESIN4  ------------------

				// ---------------- MESIN5  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN5.length; i++) {
					if (typeof MESIN5[i+1] === 'undefined') {
						MESIN5_r2[x].push([(MESIN5[i][0]+' - '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN5[i][7];
						}
						if (((j + MESIN5[i+1][7]) / 60) > 23.5) {
							MESIN5_r2[x].push([(MESIN5[i][0]+' - '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN5[i+1][5]) * MESIN5[i+1][6];
								MESIN5_r2[x].push([(MESIN5[i+1][0]+' - '+MESIN5[i+1][1]),MESIN5[i+1][2], d]);
								MESIN5[i+1][3] -= d;
								MESIN5[i+1][7] = Math.floor((Math.floor(MESIN5[i+1][3] / MESIN5[i+1][6]) * MESIN5[i+1][5]) / 60);
							} else {
								if (MESIN5[i][3] != MESIN5[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6]) < 0) {

										MESIN5_r2[x].push([(MESIN5[i][0]+' - '+MESIN5[i][1]),MESIN5[i+1][2], 
											(Math.floor(((((23.5 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6])
											]);

										MESIN5[i+1][3] -= 0;
									}else{

										MESIN5_r2[x].push([(MESIN5[i+1][0]+' - '+MESIN5[i+1][1]),MESIN5[i+1][2], 
											(Math.floor(((((23.5 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6])
											]);

										
										MESIN5[i+1][3] -= (Math.floor(((((23.5 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6]);
										MESIN5[i+1][7] = MESIN5[i+1][3] / MESIN5[i+1][6] * MESIN5[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN5[i][7] + MESIN5[i+1][7];
							// d = MESIN5[i][3] + MESIN5[i+1][3];
							MESIN5_r2[x].push([(MESIN5[i][0]+' - '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN5_r2);

				// ---------------- End MESIN5  ------------------

				// ---------------- MESIN6  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN6.length; i++) {
					if (typeof MESIN6[i+1] === 'undefined') {
						MESIN6_r2[x].push([(MESIN6[i][0]+' - '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN6[i][7];
						}
						if (((j + MESIN6[i+1][7]) / 60) > 23.5) {
							MESIN6_r2[x].push([(MESIN6[i][0]+' - '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN6[i+1][5]) * MESIN6[i+1][6];
								MESIN6_r2[x].push([(MESIN6[i+1][0]+' - '+MESIN6[i+1][1]),MESIN6[i+1][2], d]);
								MESIN6[i+1][3] -= d;
								MESIN6[i+1][7] = Math.floor((Math.floor(MESIN6[i+1][3] / MESIN6[i+1][6]) * MESIN6[i+1][5]) / 60);
							} else {
								if (MESIN6[i][3] != MESIN6[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6]) < 0) {

										MESIN6_r2[x].push([(MESIN6[i][0]+' - '+MESIN6[i][1]),MESIN6[i+1][2], 
											(Math.floor(((((23.5 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6])
											]);

										MESIN6[i+1][3] -= 0;
									}else{

										MESIN6_r2[x].push([(MESIN6[i+1][0]+' - '+MESIN6[i+1][1]),MESIN6[i+1][2], 
											(Math.floor(((((23.5 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6])
											]);

										
										MESIN6[i+1][3] -= (Math.floor(((((23.5 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6]);
										MESIN6[i+1][7] = MESIN6[i+1][3] / MESIN6[i+1][6] * MESIN6[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN6[i][7] + MESIN6[i+1][7];
							// d = MESIN6[i][3] + MESIN6[i+1][3];
							MESIN6_r2[x].push([(MESIN6[i][0]+' - '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN6_r2);

				// ---------------- End MESIN6  ------------------

				// ---------------- MESIN7  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN7.length; i++) {
					if (typeof MESIN7[i+1] === 'undefined') {
						MESIN7_r2[x].push([(MESIN7[i][0]+' - '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN7[i][7];
						}
						if (((j + MESIN7[i+1][7]) / 60) > 23.5) {
							MESIN7_r2[x].push([(MESIN7[i][0]+' - '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN7[i+1][5]) * MESIN7[i+1][6];
								MESIN7_r2[x].push([(MESIN7[i+1][0]+' - '+MESIN7[i+1][1]),MESIN7[i+1][2], d]);
								MESIN7[i+1][3] -= d;
								MESIN7[i+1][7] = Math.floor((Math.floor(MESIN7[i+1][3] / MESIN7[i+1][6]) * MESIN7[i+1][5]) / 60);
							} else {
								if (MESIN7[i][3] != MESIN7[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6]) < 0) {

										MESIN7_r2[x].push([(MESIN7[i][0]+' - '+MESIN7[i][1]),MESIN7[i+1][2], 
											(Math.floor(((((23.5 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6])
											]);

										MESIN7[i+1][3] -= 0;
									}else{

										MESIN7_r2[x].push([(MESIN7[i+1][0]+' - '+MESIN7[i+1][1]),MESIN7[i+1][2], 
											(Math.floor(((((23.5 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6])
											]);

										
										MESIN7[i+1][3] -= (Math.floor(((((23.5 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6]);
										MESIN7[i+1][7] = MESIN7[i+1][3] / MESIN7[i+1][6] * MESIN7[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN7[i][7] + MESIN7[i+1][7];
							// d = MESIN7[i][3] + MESIN7[i+1][3];
							MESIN7_r2[x].push([(MESIN7[i][0]+' - '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN7_r2);

				// ---------------- End MESIN7  ------------------

				// ---------------- MESIN8  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN8.length; i++) {
					if (typeof MESIN8[i+1] === 'undefined') {
						MESIN8_r2[x].push([(MESIN8[i][0]+' - '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN8[i][7];
						}
						if (((j + MESIN8[i+1][7]) / 60) > 23.5) {
							MESIN8_r2[x].push([(MESIN8[i][0]+' - '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN8[i+1][5]) * MESIN8[i+1][6];
								MESIN8_r2[x].push([(MESIN8[i+1][0]+' - '+MESIN8[i+1][1]),MESIN8[i+1][2], d]);
								MESIN8[i+1][3] -= d;
								MESIN8[i+1][7] = Math.floor((Math.floor(MESIN8[i+1][3] / MESIN8[i+1][6]) * MESIN8[i+1][5]) / 60);
							} else {
								if (MESIN8[i][3] != MESIN8[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6]) < 0) {

										MESIN8_r2[x].push([(MESIN8[i][0]+' - '+MESIN8[i][1]),MESIN8[i+1][2], 
											(Math.floor(((((23.5 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6])
											]);

										MESIN8[i+1][3] -= 0;
									}else{

										MESIN8_r2[x].push([(MESIN8[i+1][0]+' - '+MESIN8[i+1][1]),MESIN8[i+1][2], 
											(Math.floor(((((23.5 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6])
											]);

										
										MESIN8[i+1][3] -= (Math.floor(((((23.5 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6]);
										MESIN8[i+1][7] = MESIN8[i+1][3] / MESIN8[i+1][6] * MESIN8[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN8[i][7] + MESIN8[i+1][7];
							// d = MESIN8[i][3] + MESIN8[i+1][3];
							MESIN8_r2[x].push([(MESIN8[i][0]+' - '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN8_r2);

				// ---------------- End MESIN8  ------------------

				// ---------------- MESIN9  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN9.length; i++) {
					if (typeof MESIN9[i+1] === 'undefined') {
						MESIN9_r2[x].push([(MESIN9[i][0]+' - '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN9[i][7];
						}
						if (((j + MESIN9[i+1][7]) / 60) > 23.5) {
							MESIN9_r2[x].push([(MESIN9[i][0]+' - '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN9[i+1][5]) * MESIN9[i+1][6];
								MESIN9_r2[x].push([(MESIN9[i+1][0]+' - '+MESIN9[i+1][1]),MESIN9[i+1][2], d]);
								MESIN9[i+1][3] -= d;
								MESIN9[i+1][7] = Math.floor((Math.floor(MESIN9[i+1][3] / MESIN9[i+1][6]) * MESIN9[i+1][5]) / 60);
							} else {
								if (MESIN9[i][3] != MESIN9[i][4]) {
									
									// ------minus


									if ((Math.floor(((((23.5 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6]) < 0) {

										MESIN9_r2[x].push([(MESIN9[i][0]+' - '+MESIN9[i][1]),MESIN9[i+1][2], 
											(Math.floor(((((23.5 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6])
											]);

										MESIN9[i+1][3] -= 0;
									}else{

										MESIN9_r2[x].push([(MESIN9[i+1][0]+' - '+MESIN9[i+1][1]),MESIN9[i+1][2], 
											(Math.floor(((((23.5 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6])
											]);

										
										MESIN9[i+1][3] -= (Math.floor(((((23.5 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6]);
										MESIN9[i+1][7] = MESIN9[i+1][3] / MESIN9[i+1][6] * MESIN9[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN9[i][7] + MESIN9[i+1][7];
							// d = MESIN9[i][3] + MESIN9[i+1][3];
							MESIN9_r2[x].push([(MESIN9[i][0]+' - '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN9_r2);

				// ---------------- End MESIN9  ------------------

				// ---------------- MESIN11  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN11.length; i++) {
					if (typeof MESIN11[i+1] === 'undefined') {
						MESIN11_r2[x].push([(MESIN11[i][0]+' - '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN11[i][7];
						}
						if (((j + MESIN11[i+1][7]) / 60) > 23.5) {
							MESIN11_r2[x].push([(MESIN11[i][0]+' - '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);
							if (c == 1) {
								d = Math.floor((((23.5 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
								MESIN11_r2[x].push([(MESIN11[i+1][0]+' - '+MESIN11[i+1][1]),MESIN11[i+1][2], d]);

								MESIN11[i+1][3] -= d;
								MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
							} else {
								if (MESIN11[i][3] != MESIN11[i][4]) {
									
									// ------ minus

									if ((Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]) < 0) {

										MESIN11_r2[x].push([(MESIN11[i][0]+' - '+MESIN11[i][1]),MESIN11[i+1][2], 
											(Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
											]);

										if (Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6] < 0) console.log(Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]); 

										MESIN11[i+1][3] -= 0;
									}else{

										MESIN11_r2[x].push([(MESIN11[i+1][0]+' - '+MESIN11[i+1][1]),MESIN11[i+1][2], 
											(Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
											]);

										
										MESIN11[i+1][3] -= (Math.floor(((((23.5 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]);
										MESIN11[i+1][7] = MESIN11[i+1][3] / MESIN11[i+1][6] * MESIN11[i+1][5] / 60;
									}

								// ------ end minus

							}

							j = 0;
						}

						c = 0;
						x+=1;
					} else {
						j = MESIN11[i][7] + MESIN11[i+1][7];
							// d = MESIN11[i][3] + MESIN11[i+1][3];
							MESIN11_r2[x].push([(MESIN11[i][0]+' - '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN11_r2);

				// ---------------- End MESIN11  ------------------

// console.table(MESIN11);


				// ---------------- Mesin1 Table  ------------------

				for (var i = 0; i < mesin1_r2.length; i++) {	
					unik = [];
					lp = "";


					if (mesin1_r2[i].length === 1) {
						HeadMesin1 +='<td style="font-size:20px; width:10px;">'+mesin1_r2[i][0][0]+'</td>';

						BodyMesin1 += '<td style="padding: 0px">';
						BodyMesin1 +='<table border="1"  style="width:100%">';	
						BodyMesin1 +='<tr>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][0] +'</td>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][1]+'</td>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][2]+'</td>';
						BodyMesin1 +='</tr>';
						BodyMesin1 +='</table>';
						BodyMesin1 +='</td>';
					}

					if (mesin1_r2[i].length > 1) {
						

						BodyMesin1 += '<td style="padding: 0px">';
						BodyMesin1 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < mesin1_r2[i].length; a++) {
							
							if (mesin1_r2[i][a][2] > 0) {

								BodyMesin1 +='<tr>';

								if (typeof mesin1_r2[i][a+1] === 'undefined' ) {
									BodyMesin1 +='<td style="font-size:20px; ">'+mesin1_r2[i][a][0] +'</td>';								

								}else{

									if (mesin1_r2[i][a][0] != mesin1_r2[i][a+1][0]) {
										BodyMesin1 +='<td style="font-size:20px; background-color: #ffd03a;">'+mesin1_r2[i][a][0] +'</td>';									
									}else{
										BodyMesin1 +='<td style="font-size:20px; ">'+mesin1_r2[i][a][0] +'</td>';
									}
								}

								BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][a][1]+'</td>';
								BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][a][2]+'</td>';
								BodyMesin1 +='</tr>';							

							}
							unik.push(mesin1_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMesin1 +='<td style="font-size:20px; background-color: #ffd03a; width:10px;">'+unique(unik)+'</td>';
							ganti.push(i);

						}else{
							HeadMesin1 +='<td style="font-size:20px;  width:10px;">'+unique(unik)+'</td>';
						}		
						
						BodyMesin1 +='</table>';
						BodyMesin1 +='</td>';						
					}

				}			

				$('#HeadMESIN1').append(HeadMesin1);
				$('#BodyMESIN1').append(BodyMesin1);

				// ---------------- End Mesin1 Table  ------------------

				// ---------------- MESIN2 Table  ------------------

				for (var i = 0; i < MESIN2_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN2_r2[i].length === 1) {
						HeadMESIN2 +='<td style="font-size:20px; width:10px">'+MESIN2_r2[i][0][0]+'</td>';

						BodyMESIN2 += '<td style="padding: 0px">';
						BodyMESIN2 +='<table border="1" style="width:100%">';	
						BodyMESIN2 +='<tr>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][0] +'</td>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][1]+'</td>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][2]+'</td>';
						BodyMESIN2 +='</tr>';
						BodyMESIN2 +='</table>';
						BodyMESIN2 +='</td>';
					}

					if (MESIN2_r2[i].length > 1) {
						

						BodyMESIN2 += '<td style="padding: 0px">';
						BodyMESIN2 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN2_r2[i].length; a++) {
							
							if (MESIN2_r2[i][a][2] > 0) {

								BodyMESIN2 +='<tr>';

								if (typeof MESIN2_r2[i][a+1] === 'undefined' ) {
									BodyMESIN2 +='<td style="font-size:20px; ">'+MESIN2_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN2_r2[i][a][0] != MESIN2_r2[i][a+1][0]) {
										BodyMESIN2 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN2_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN2 +='<td style="font-size:20px; ">'+MESIN2_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][a][1]+'</td>';
								BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][a][2]+'</td>';
								BodyMESIN2 +='</tr>';							

							}
							unik.push(MESIN2_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN2 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';
							ganti.push(i);

						}else{
							HeadMESIN2 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN2 +='</table>';
						BodyMESIN2 +='</td>';						
					}

				}			

				// $('#HeadMESIN2').append(HeadMESIN2);
				// $('#BodyMESIN2').append(BodyMESIN2);

				// ---------------- End MESIN2 Table  ------------------



				// ---------------- MESIN3 Table  ------------------

				for (var i = 0; i < MESIN3_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN3_r2[i].length === 1) {
						HeadMESIN3 +='<td style="font-size:20px; width:10px">'+MESIN3_r2[i][0][0]+'</td>';

						BodyMESIN3 += '<td style="padding: 0px">';
						BodyMESIN3 +='<table border="1"  style="width:100%">';	
						BodyMESIN3 +='<tr>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][0] +'</td>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][1]+'</td>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][2]+'</td>';
						BodyMESIN3 +='</tr>';
						BodyMESIN3 +='</table>';
						BodyMESIN3 +='</td>';
					}

					if (MESIN3_r2[i].length > 1) {
						

						BodyMESIN3 += '<td style="padding: 0px">';
						BodyMESIN3 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN3_r2[i].length; a++) {
							
							if (MESIN3_r2[i][a][2] > 0) {

								BodyMESIN3 +='<tr>';

								if (typeof MESIN3_r2[i][a+1] === 'undefined' ) {
									BodyMESIN3 +='<td style="font-size:20px; ">'+MESIN3_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN3_r2[i][a][0] != MESIN3_r2[i][a+1][0]) {
										BodyMESIN3 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN3_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN3 +='<td style="font-size:20px; ">'+MESIN3_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][a][1]+'</td>';
								BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][a][2]+'</td>';
								BodyMESIN3 +='</tr>';							

							}
							unik.push(MESIN3_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN3 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';
							ganti.push(i);	
						}else{
							HeadMESIN3 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN3 +='</table>';
						BodyMESIN3 +='</td>';						
					}

				}			

				// $('#HeadMESIN3').append(HeadMESIN3);
				// $('#BodyMESIN3').append(BodyMESIN3);

				// ---------------- End MESIN3 Table  ------------------


				// ---------------- MESIN4 Table  ------------------

				for (var i = 0; i < MESIN4_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN4_r2[i].length === 1) {
						HeadMESIN4 +='<td style="font-size:20px; width:10px">'+MESIN4_r2[i][0][0]+'</td>';

						BodyMESIN4 += '<td style="padding: 0px">';
						BodyMESIN4 +='<table border="1"  style="width:100%">';	
						BodyMESIN4 +='<tr>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][0] +'</td>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][1]+'</td>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][2]+'</td>';
						BodyMESIN4 +='</tr>';
						BodyMESIN4 +='</table>';
						BodyMESIN4 +='</td>';
					}

					if (MESIN4_r2[i].length > 1) {
						

						BodyMESIN4 += '<td style="padding: 0px">';
						BodyMESIN4 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN4_r2[i].length; a++) {
							
							if (MESIN4_r2[i][a][2] > 0) {

								BodyMESIN4 +='<tr>';

								if (typeof MESIN4_r2[i][a+1] === 'undefined' ) {
									BodyMESIN4 +='<td style="font-size:20px; ">'+MESIN4_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN4_r2[i][a][0] != MESIN4_r2[i][a+1][0]) {
										BodyMESIN4 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN4_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN4 +='<td style="font-size:20px; ">'+MESIN4_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][a][1]+'</td>';
								BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][a][2]+'</td>';
								BodyMESIN4 +='</tr>';							

							}
							unik.push(MESIN4_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN4 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';
							ganti.push(i);	
						}else{
							HeadMESIN4 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN4 +='</table>';
						BodyMESIN4 +='</td>';						
					}

				}			

				// $('#HeadMESIN4').append(HeadMESIN4);
				// $('#BodyMESIN4').append(BodyMESIN4);

				// ---------------- End MESIN4 Table  ------------------


				// ---------------- MESIN5 Table  ------------------

				for (var i = 0; i < MESIN5_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN5_r2[i].length === 1) {
						HeadMESIN5 +='<td style="font-size:20px; width:10px">'+MESIN5_r2[i][0][0]+'</td>';

						BodyMESIN5 += '<td style="padding: 0px">';
						BodyMESIN5 +='<table border="1"  style="width:100%">';	
						BodyMESIN5 +='<tr>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][0] +'</td>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][1]+'</td>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][2]+'</td>';
						BodyMESIN5 +='</tr>';
						BodyMESIN5 +='</table>';
						BodyMESIN5 +='</td>';
					}

					if (MESIN5_r2[i].length > 1) {
						

						BodyMESIN5 += '<td style="padding: 0px">';
						BodyMESIN5 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN5_r2[i].length; a++) {
							
							if (MESIN5_r2[i][a][2] > 0) {

								BodyMESIN5 +='<tr>';

								if (typeof MESIN5_r2[i][a+1] === 'undefined' ) {
									BodyMESIN5 +='<td style="font-size:20px; ">'+MESIN5_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN5_r2[i][a][0] != MESIN5_r2[i][a+1][0]) {
										BodyMESIN5 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN5_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN5 +='<td style="font-size:20px; ">'+MESIN5_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][a][1]+'</td>';
								BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][a][2]+'</td>';
								BodyMESIN5 +='</tr>';							

							}
							unik.push(MESIN5_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN5 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';
							ganti.push(i);	
						}else{
							HeadMESIN5 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN5 +='</table>';
						BodyMESIN5 +='</td>';						
					}

				}			

				// $('#HeadMESIN5').append(HeadMESIN5);
				// $('#BodyMESIN5').append(BodyMESIN5);

				// ---------------- End MESIN5 Table  ------------------



				// ---------------- MESIN6 Table  ------------------

				for (var i = 0; i < MESIN6_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN6_r2[i].length === 1) {
						HeadMESIN6 +='<td style="font-size:20px; width:10px">'+MESIN6_r2[i][0][0]+'</td>';

						BodyMESIN6 += '<td style="padding: 0px">';
						BodyMESIN6 +='<table border="1"  style="width:100%">';	
						BodyMESIN6 +='<tr>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][0] +'</td>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][1]+'</td>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][2]+'</td>';
						BodyMESIN6 +='</tr>';
						BodyMESIN6 +='</table>';
						BodyMESIN6 +='</td>';
					}

					if (MESIN6_r2[i].length > 1) {
						

						BodyMESIN6 += '<td style="padding: 0px">';
						BodyMESIN6 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN6_r2[i].length; a++) {
							
							if (MESIN6_r2[i][a][2] > 0) {

								BodyMESIN6 +='<tr>';

								if (typeof MESIN6_r2[i][a+1] === 'undefined' ) {
									BodyMESIN6 +='<td style="font-size:20px; ">'+MESIN6_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN6_r2[i][a][0] != MESIN6_r2[i][a+1][0]) {
										BodyMESIN6 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN6_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN6 +='<td style="font-size:20px; ">'+MESIN6_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][a][1]+'</td>';
								BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][a][2]+'</td>';
								BodyMESIN6 +='</tr>';							

							}
							unik.push(MESIN6_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN6 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';	
							ganti.push(i);
						}else{
							HeadMESIN6 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN6 +='</table>';
						BodyMESIN6 +='</td>';						
					}

				}			

				// $('#HeadMESIN6').append(HeadMESIN6);
				// $('#BodyMESIN6').append(BodyMESIN6);

				// ---------------- End MESIN6 Table  ------------------



				// ---------------- MESIN7 Table  ------------------

				for (var i = 0; i < MESIN7_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN7_r2[i].length === 1) {
						HeadMESIN7 +='<td style="font-size:20px; width:10px">'+MESIN7_r2[i][0][0]+'</td>';

						BodyMESIN7 += '<td style="padding: 0px">';
						BodyMESIN7 +='<table border="1"  style="width:100%">';	
						BodyMESIN7 +='<tr>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][0] +'</td>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][1]+'</td>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][2]+'</td>';
						BodyMESIN7 +='</tr>';
						BodyMESIN7 +='</table>';
						BodyMESIN7 +='</td>';
					}

					if (MESIN7_r2[i].length > 1) {
						

						BodyMESIN7 += '<td style="padding: 0px">';
						BodyMESIN7 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN7_r2[i].length; a++) {
							
							if (MESIN7_r2[i][a][2] > 0) {

								BodyMESIN7 +='<tr>';

								if (typeof MESIN7_r2[i][a+1] === 'undefined' ) {
									BodyMESIN7 +='<td style="font-size:20px; ">'+MESIN7_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN7_r2[i][a][0] != MESIN7_r2[i][a+1][0]) {
										BodyMESIN7 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN7_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN7 +='<td style="font-size:20px; ">'+MESIN7_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][a][1]+'</td>';
								BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][a][2]+'</td>';
								BodyMESIN7 +='</tr>';							

							}
							unik.push(MESIN7_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN7 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';
							ganti.push(i);	
						}else{
							HeadMESIN7 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN7 +='</table>';
						BodyMESIN7 +='</td>';						
					}

				}			

				// $('#HeadMESIN7').append(HeadMESIN7);
				// $('#BodyMESIN7').append(BodyMESIN7);

				// ---------------- End MESIN7 Table  ------------------



				// ---------------- MESIN8 Table  ------------------

				for (var i = 0; i < MESIN8_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN8_r2[i].length === 1) {
						HeadMESIN8 +='<td style="font-size:20px; width:10px">'+MESIN8_r2[i][0][0]+'</td>';

						BodyMESIN8 += '<td style="padding: 0px">';
						BodyMESIN8 +='<table border="1"  style="width:100%">';	
						BodyMESIN8 +='<tr>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][0] +'</td>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][1]+'</td>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][2]+'</td>';
						BodyMESIN8 +='</tr>';
						BodyMESIN8 +='</table>';
						BodyMESIN8 +='</td>';
					}

					if (MESIN8_r2[i].length > 1) {
						

						BodyMESIN8 += '<td style="padding: 0px">';
						BodyMESIN8 +='<table border="1" style="width:100%" >';

						for (var a = 0; a < MESIN8_r2[i].length; a++) {
							
							if (MESIN8_r2[i][a][2] > 0) {

								BodyMESIN8 +='<tr>';

								if (typeof MESIN8_r2[i][a+1] === 'undefined' ) {
									BodyMESIN8 +='<td style="font-size:20px; ">'+MESIN8_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN8_r2[i][a][0] != MESIN8_r2[i][a+1][0]) {
										BodyMESIN8 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN8_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN8 +='<td style="font-size:20px; ">'+MESIN8_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][a][1]+'</td>';
								BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][a][2]+'</td>';
								BodyMESIN8 +='</tr>';							

							}
							unik.push(MESIN8_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN8 +='<td style="font-size:20px; background-color: #ffd03a; width:10px">'+unique(unik)+'</td>';	
							ganti.push(i);
						}else{
							HeadMESIN8 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN8 +='</table>';
						BodyMESIN8 +='</td>';						
					}

				}			

				// $('#HeadMESIN8').append(HeadMESIN8);
				// $('#BodyMESIN8').append(BodyMESIN8);

				// ---------------- End MESIN8 Table  ------------------



				// ---------------- MESIN9 Table  ------------------

				for (var i = 0; i < MESIN9_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN9_r2[i].length === 1) {
						HeadMESIN9 +='<td style="font-size:20px; width:10px">'+MESIN9_r2[i][0][0]+'</td>';

						BodyMESIN9 += '<td style="padding: 0px">';
						BodyMESIN9 +='<table border="1" style="width:100%" >';	
						BodyMESIN9 +='<tr>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][0] +'</td>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][1]+'</td>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][2]+'</td>';
						BodyMESIN9 +='</tr>';
						BodyMESIN9 +='</table>';
						BodyMESIN9 +='</td>';
					}

					if (MESIN9_r2[i].length > 1) {
						

						BodyMESIN9 += '<td style="padding: 0px">';
						BodyMESIN9 +='<table border="1"  style="width:100%">';

						for (var a = 0; a < MESIN9_r2[i].length; a++) {
							
							if (MESIN9_r2[i][a][2] > 0) {

								BodyMESIN9 +='<tr>';

								if (typeof MESIN9_r2[i][a+1] === 'undefined' ) {
									BodyMESIN9 +='<td style="font-size:20px; ">'+MESIN9_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN9_r2[i][a][0] != MESIN9_r2[i][a+1][0]) {
										BodyMESIN9 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN9_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN9 +='<td style="font-size:20px; ">'+MESIN9_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][a][1]+'</td>';
								BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][a][2]+'</td>';
								BodyMESIN9 +='</tr>';							

							}
							unik.push(MESIN9_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN9 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';	
							ganti.push(i);
						}else{
							HeadMESIN9 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN9 +='</table>';
						BodyMESIN9 +='</td>';						
					}

				}			

				// $('#HeadMESIN9').append(HeadMESIN9);
				// $('#BodyMESIN9').append(BodyMESIN9);

				// ---------------- End MESIN9 Table  ------------------



				// ---------------- MESIN11 Table  ------------------

				for (var i = 0; i < MESIN11_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN11_r2[i].length === 1) {
						HeadMESIN11 +='<td style="font-size:20px; width:10px">'+MESIN11_r2[i][0][0]+'</td>';

						BodyMESIN11 += '<td style="padding: 0px">';
						BodyMESIN11 +='<table border="1" style="width:100%" >';	
						BodyMESIN11 +='<tr>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][0] +'</td>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][1]+'</td>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][2]+'</td>';
						BodyMESIN11 +='</tr>';
						BodyMESIN11 +='</table>';
						BodyMESIN11 +='</td>';
					}

					if (MESIN11_r2[i].length > 1) {
						

						BodyMESIN11 += '<td style="padding: 0px">';
						BodyMESIN11 +='<table border="1" style="width:100%" >';

						for (var a = 0; a < MESIN11_r2[i].length; a++) {
							
							if (MESIN11_r2[i][a][2] > 0) {

								BodyMESIN11 +='<tr>';

								if (typeof MESIN11_r2[i][a+1] === 'undefined' ) {
									BodyMESIN11 +='<td style="font-size:20px; ">'+MESIN11_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN11_r2[i][a][0] != MESIN11_r2[i][a+1][0]) {
										BodyMESIN11 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN11_r2[i][a][0] +'</td>';									
									}else{
										BodyMESIN11 +='<td style="font-size:20px; ">'+MESIN11_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][a][1]+'</td>';
								BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][a][2]+'</td>';
								BodyMESIN11 +='</tr>';							

							}
							unik.push(MESIN11_r2[i][a][0]);

						}

						if (unique(unik).length > 1	) {
							HeadMESIN11 +='<td style="font-size:20px; background-color: #ffd03a;width:10px">'+unique(unik)+'</td>';	
							ganti.push(i);
						}else{
							HeadMESIN11 +='<td style="font-size:20px; width:10px">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN11 +='</table>';
						BodyMESIN11 +='</td>';						
					}

				}			

				// $('#HeadMESIN11').append(HeadMESIN11);
				// $('#BodyMESIN11').append(BodyMESIN11);

				// ---------------- End MESIN11 Table  ------------------
				


				// console.table(TargetAllMesin);
				// console.table(MESIN1);
				// console.table(MESIN2);
				// console.table(MESIN3);
				// console.table(MESIN4);
				// console.table(MESIN5);
				// console.table(MESIN6);
				// console.table(MESIN7);
				// console.table(MESIN8);
				// console.table(MESIN9);
				// console.table(MESIN11);

				openSuccessGritter('Success!', result.message);
				fjudul();


			}
			else{
				audio_error.play();

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
		}
	});

}

function fjudul() {
	var akhir = [];
	var total = [];
	// var max = 0;

	for (var i = 0; i < mesin1_r2.length; i++) {		
		if (mesin1_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	max = parseInt(akhir.slice(0,1))+1
	akhir = [];

	for (var i = 0; i < MESIN2_r2.length; i++) {		
		if (MESIN2_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN3_r2.length; i++) {		
		if (MESIN3_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN4_r2.length; i++) {		
		if (MESIN4_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN5_r2.length; i++) {		
		if (MESIN5_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN6_r2.length; i++) {		
		if (MESIN6_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN7_r2.length; i++) {		
		if (MESIN7_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN8_r2.length; i++) {		
		if (MESIN8_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN9_r2.length; i++) {		
		if (MESIN9_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	for (var i = 0; i < MESIN11_r2.length; i++) {		
		if (MESIN11_r2[i].length >= 1) {
			akhir.push(i);
		}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	if (max < parseInt(akhir.slice(0,1))+1) {
		max = parseInt(akhir.slice(0,1))+1;
	}
	akhir = [];

	

	for (var a = 0; a < (max - total[0]); a++) {
		if (total[0] < max ) {
			HeadMESIN1 +='<td style="font-size:20px;">OFF</td>';
			BodyMESIN1 +='<td style="font-size:20px;">OFF</td>';
		}else{
			HeadMESIN1 +='';
			BodyMESIN1 +='';
		}						
	}		
	$('#HeadMESIN1').append(HeadMESIN1);
	$('#BodyMESIN1').append(BodyMESIN1);

	for (var a = 0; a < (max - total[1]); a++) {
		if (total[1] < max) {
			HeadMESIN2 +='<td style="font-size:20px;width:10px">OFF</td>';
			BodyMESIN2 +='<td style="font-size:20px;width:10px">OFF</td>';
		}else{
			HeadMESIN2 +='';
			BodyMESIN2 +='';	
		}						
	}		
	$('#HeadMESIN2').append(HeadMESIN2);
	$('#BodyMESIN2').append(BodyMESIN2);

	for (var a = 0; a < (max - total[2]); a++) {
		if (total[2] < max) {
			HeadMESIN3 +='<td style="font-size:20px;width:10px">OFF</td>';
			BodyMESIN3 +='<td style="font-size:20px;width:10px">OFF</td>';
		}else{
			HeadMESIN3 +='';
			BodyMESIN3 +='';	
		}						
	}		
	$('#HeadMESIN3').append(HeadMESIN3);
	$('#BodyMESIN3').append(BodyMESIN3);

	for (var a = 0; a < (max - total[3]); a++) {
		HeadMESIN4 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN4 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN4').append(HeadMESIN4);
	$('#BodyMESIN4').append(BodyMESIN4);

	for (var a = 0; a < (max - total[4]); a++) {
		HeadMESIN5 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN5 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN5').append(HeadMESIN5);
	$('#BodyMESIN5').append(BodyMESIN5);

	for (var a = 0; a < (max - total[5]); a++) {
		HeadMESIN6 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN6 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN6').append(HeadMESIN6);
	$('#BodyMESIN6').append(BodyMESIN6);

	for (var a = 0; a < (max - total[6]); a++) {
		HeadMESIN7 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN7 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN7').append(HeadMESIN7);
	$('#BodyMESIN7').append(BodyMESIN7);

	for (var a = 0; a < (max - total[7]); a++) {
		HeadMESIN8 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN8 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN8').append(HeadMESIN8);
	$('#BodyMESIN8').append(BodyMESIN8);

	for (var a = 0; a < (max - total[8]); a++) {
		HeadMESIN9 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN9 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN9').append(HeadMESIN9);
	$('#BodyMESIN9').append(BodyMESIN9);

	for (var a = 0; a < (max - total[9]); a++) {
		HeadMESIN11 +='<td style="font-size:20px;">OFF</td>';
		BodyMESIN11 +='<td style="font-size:20px;">OFF</td>';						
	}		
	$('#HeadMESIN11').append(HeadMESIN11);
	$('#BodyMESIN11').append(BodyMESIN11);

	getDateWorking();

	




}

function getMesin() {

	$.get('{{ url("fetch/getStatusMesin") }}',  function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){



				openSuccessGritter('Success!', result.message);


			}
			else{
				audio_error.play();

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
		}
	});
	
}

function getDateWorking() {

	var data = {
		max:max,

	}

	$.get('{{ url("fetch/getDateWorking") }}', data,  function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){

				for (var i = 0; i < result.tgl.length; i++) {
					dateMax.push(result.tgl[i].week_date);
				}

				saveScheduletmp();



				openSuccessGritter('Success!', result.message);


			}
			else{
				audio_error.play();

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
		}
	});
	
}

function saveSchedule() {
	$('#myModal').modal({backdrop: 'static', keyboard: false});
	$("#myModal").modal('show');

	
	// // mesin1

	// for (var i = 0; i < max; i++) {
	// 	if (mesin1_r2[i].length < 1) {
	// 		if (mesin1_r2[i] > 0) {
	// 			PostMESIN1.push(dateMax[i]+'#'+mesin1_r2[i][0]+'#'+mesin1_r2[i][1]+'#'+mesin1_r2[i][2])
	// 		}else{
	// 			PostMESIN1.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < mesin1_r2[i].length; a++) {
	// 			if (mesin1_r2[i][a][2] > 0) {
	// 				PostMESIN1.push(dateMax[i]+'#'+mesin1_r2[i][a][0]+'#'+mesin1_r2[i][a][1]+'#'+mesin1_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end mesin1

	// // MESIN2

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN2_r2[i].length < 1) {
	// 		if (MESIN2_r2[i] > 0) {
	// 			PostMESIN2.push(dateMax[i]+'#'+MESIN2_r2[i][0]+'#'+MESIN2_r2[i][1]+'#'+MESIN2_r2[i][2])
	// 		}else{
	// 			PostMESIN2.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN2_r2[i].length; a++) {
	// 			if (MESIN2_r2[i][a][2] > 0) {
	// 				PostMESIN2.push(dateMax[i]+'#'+MESIN2_r2[i][a][0]+'#'+MESIN2_r2[i][a][1]+'#'+MESIN2_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN2
	// // MESIN3

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN3_r2[i].length < 1) {
	// 		if (MESIN3_r2[i] > 0) {
	// 			PostMESIN3.push(dateMax[i]+'#'+MESIN3_r2[i][0]+'#'+MESIN3_r2[i][1]+'#'+MESIN3_r2[i][2])
	// 		}else{
	// 			PostMESIN3.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN3_r2[i].length; a++) {
	// 			if (MESIN3_r2[i][a][2] > 0) {
	// 				PostMESIN3.push(dateMax[i]+'#'+MESIN3_r2[i][a][0]+'#'+MESIN3_r2[i][a][1]+'#'+MESIN3_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN3
	// // MESIN4

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN4_r2[i].length < 1) {
	// 		if (MESIN4_r2[i] > 0) {
	// 			PostMESIN4.push(dateMax[i]+'#'+MESIN4_r2[i][0]+'#'+MESIN4_r2[i][1]+'#'+MESIN4_r2[i][2])
	// 		}else{
	// 			PostMESIN4.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN4_r2[i].length; a++) {
	// 			if (MESIN4_r2[i][a][2] > 0) {
	// 				PostMESIN4.push(dateMax[i]+'#'+MESIN4_r2[i][a][0]+'#'+MESIN4_r2[i][a][1]+'#'+MESIN4_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN4
	// // MESIN5

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN5_r2[i].length < 1) {
	// 		if (MESIN5_r2[i] > 0) {
	// 			PostMESIN5.push(dateMax[i]+'#'+MESIN5_r2[i][0]+'#'+MESIN5_r2[i][1]+'#'+MESIN5_r2[i][2])
	// 		}else{
	// 			PostMESIN5.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN5_r2[i].length; a++) {
	// 			if (MESIN5_r2[i][a][2] > 0) {
	// 				PostMESIN5.push(dateMax[i]+'#'+MESIN5_r2[i][a][0]+'#'+MESIN5_r2[i][a][1]+'#'+MESIN5_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN5
	// // MESIN6

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN6_r2[i].length < 1) {
	// 		if (MESIN6_r2[i] > 0) {
	// 			PostMESIN6.push(dateMax[i]+'#'+MESIN6_r2[i][0]+'#'+MESIN6_r2[i][1]+'#'+MESIN6_r2[i][2])
	// 		}else{
	// 			PostMESIN6.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN6_r2[i].length; a++) {
	// 			if (MESIN6_r2[i][a][2] > 0) {
	// 				PostMESIN6.push(dateMax[i]+'#'+MESIN6_r2[i][a][0]+'#'+MESIN6_r2[i][a][1]+'#'+MESIN6_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN6
	// // MESIN7

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN7_r2[i].length < 1) {
	// 		if (MESIN7_r2[i] > 0) {
	// 			PostMESIN7.push(dateMax[i]+'#'+MESIN7_r2[i][0]+'#'+MESIN7_r2[i][1]+'#'+MESIN7_r2[i][2])
	// 		}else{
	// 			PostMESIN7.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN7_r2[i].length; a++) {
	// 			if (MESIN7_r2[i][a][2] > 0) {
	// 				PostMESIN7.push(dateMax[i]+'#'+MESIN7_r2[i][a][0]+'#'+MESIN7_r2[i][a][1]+'#'+MESIN7_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN7
	// // MESIN8

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN8_r2[i].length < 1) {
	// 		if (MESIN8_r2[i] > 0) {
	// 			PostMESIN8.push(dateMax[i]+'#'+MESIN8_r2[i][0]+'#'+MESIN8_r2[i][1]+'#'+MESIN8_r2[i][2])
	// 		}else{
	// 			PostMESIN8.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN8_r2[i].length; a++) {
	// 			if (MESIN8_r2[i][a][2] > 0) {
	// 				PostMESIN8.push(dateMax[i]+'#'+MESIN8_r2[i][a][0]+'#'+MESIN8_r2[i][a][1]+'#'+MESIN8_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN8
	// // MESIN9

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN9_r2[i].length < 1) {
	// 		if (MESIN9_r2[i] > 0) {
	// 			PostMESIN9.push(dateMax[i]+'#'+MESIN9_r2[i][0]+'#'+MESIN9_r2[i][1]+'#'+MESIN9_r2[i][2])
	// 		}else{
	// 			PostMESIN9.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN9_r2[i].length; a++) {
	// 			if (MESIN9_r2[i][a][2] > 0) {
	// 				PostMESIN9.push(dateMax[i]+'#'+MESIN9_r2[i][a][0]+'#'+MESIN9_r2[i][a][1]+'#'+MESIN9_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end MESIN9


	// // mesin11

	// for (var i = 0; i < max; i++) {
	// 	if (MESIN11_r2[i].length < 1) {
	// 		if (MESIN11_r2[i] > 0) {
	// 			PostMESIN11.push(dateMax[i]+'#'+MESIN11_r2[i][0]+'#'+MESIN11_r2[i][1]+'#'+MESIN11_r2[i][2])
	// 		}else{
	// 			PostMESIN11.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
	// 		}
			
	// 	}else{
	// 		for (var a = 0; a < MESIN11_r2[i].length; a++) {
	// 			if (MESIN11_r2[i][a][2] > 0) {
	// 				PostMESIN11.push(dateMax[i]+'#'+MESIN11_r2[i][a][0]+'#'+MESIN11_r2[i][a][1]+'#'+MESIN11_r2[i][a][2])
	// 			}				
	// 		}			
	// 	}		
	// }

	// // end mesin11

	// alert(PostMESIN11);

	var data = {
		PostMESIN1:PostMESIN1,
		PostMESIN2:PostMESIN2,
		PostMESIN3:PostMESIN3,
		PostMESIN4:PostMESIN4,
		PostMESIN5:PostMESIN5,
		PostMESIN6:PostMESIN6,
		PostMESIN7:PostMESIN7,
		PostMESIN8:PostMESIN8,
		PostMESIN9:PostMESIN9,
		PostMESIN11:PostMESIN11,

	}

	$.post('{{ url("save/Schedule") }}', data,  function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				

				openSuccessGritter('Success!', result.message);
				$("#myModal").modal('hide');


			}
			else{
				audio_error.play();
				$("#myModal").modal('hide');

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
			$("#myModal").modal('hide');
		}
	});



	
}

function chartplan() {
		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		$.get('{{ url("fetch/getChartPlan") }}', data, function(result, status, xhr) {
			console.log(status);
			console.log(result);
			console.log(xhr);
			var tgl = [];
			var target = [];
			var mesin =[];
			var molding =[];

			var blue =[];
			var green =[];
			var pink =[];
			var red =[];
			var brown =[];
			var ivory =[];
			var yrf =[];

			var Mesinblue =[];
			var Mesingreen =[];
			var Mesinpink =[];
			var Mesinred =[];
			var Mesinbrown =[];
			var Mesinivory =[];
			var Mesinyrf =[];

			var gantimolding = [];
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.tgl.length; i++) {
							tgl.push(result.tgl[i].week_date);
					}

					for (var i = 0; i < result.part.length; i++) {
						blue.push(result.part[i].blue);
						green.push(result.part[i].green);
						pink.push(result.part[i].pink);
						red.push(result.part[i].red);
						brown.push(result.part[i].brown);
						ivory.push(result.part[i].ivory);
						yrf.push(result.part[i].yrf);

					}

					for (var i = 0; i < result.plan.length; i++) {
						Mesinblue.push( parseInt(result.plan[i].blue));
						Mesingreen.push( parseInt(result.plan[i].green));
						Mesinpink.push( parseInt(result.plan[i].pink));
						Mesinred.push( parseInt(result.plan[i].red));
						Mesinbrown.push( parseInt(result.plan[i].brown));
						Mesinivory.push( parseInt(result.plan[i].ivory));
						Mesinyrf.push( parseInt(result.plan[i].yrf));
					}

					


					// for (var i = 0; i < result.molding.length; i++) {

					// 	if (result.molding[i].mesin == "Mesin 8") {			

							
					// 		if (result.molding[i].color_p != result.molding[i+1].color_p && result.molding[i].part_p != result.molding[i+1].part_p && result.molding[i].color_p != "OFF") {
					// 			molding45.push(result.molding[i].due_date+'#45')

					// 		}else if (result.molding[i].color_p == result.molding[i+1].color_p && result.molding[i].part_p != result.molding[i+1].part_p && result.molding[i].color_p != "OFF") {
					// 			molding45.push(result.molding[i].due_date+'#15')

					// 		}else if (result.molding[i].color_p != result.molding[i+1].color_p && result.molding[i].color_p != "OFF") {
					// 			molding15.push(result.molding[i].due_date+'#45')
					// 		}							
					// 	}			
					// }

					// 	if (molding45.length > 0) {
					// 		for (var i = 0; i < (molding45.length - 1); i++) {
					// 			molding.push(molding45[i]);
					// 		}
					// 	}

					// 	if (molding15.length > 0) {
					// 		for (var i = 0; i < (molding45.length - 1); i++) {
					// 			molding.push(molding15[i]);
					// 		}
					// 	}

					for (var i = 0; i < result.molding.length; i++) {
						if (((result.molding[i].total + 1) - 1 > 0)) {
						molding.push(parseInt(result.molding[i].total  - 1))
						}
					}

					for (var i = 0; i < max; i++) {
						gantimolding.push(0);
					}

					for (var i = 0; i <= (gantimolding.length - ganti.length); i++) {
						ganti.push(-1);
					}

					var a = 0;
					for (var i = 0; i < ganti.length; i++) {

						if (gantimolding[ganti[i]] > 0) {
							a++;
							gantimolding[ganti[i]] = a + 1;
						}

						else {						
							a++;
							gantimolding[ganti[i]] = a;
						}

						a=0;				

							
					}

					
					console.table(ganti);
					console.table(gantimolding);	
					
					Highcharts.chart('chartplan', {
				    
				    title: {
				        text: 'Plan Assy vs Plan Injection'
				    },
				    subtitle: {
				        text: ''
				    },
				    xAxis: {
				        categories: tgl
				    },
				    yAxis: [{
				        title: {
				            text: 'Pc'
				        }
				    },{
			        title: {
			            text: 'Last Update: '+getActualFullDate(),
			        },
			        opposite: true
			    }],
				    plotOptions: {
				        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        column: {
				            dataLabels: {
				                enabled: true
				            }
				        },
				        

				    },
				    series: [{
				    	type: 'column',
				        name: 'Frequency Change Molding',
				        data: gantimolding,
				        yAxis: 1
				    }, {
				    	type: 'spline',
				    	color: 'blue',
				        name: 'Plan Injeksi YRS BLUE',
				        data: Mesinblue
				    }, {
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'blue',
				        name: 'Target Assy YRS BLUE',
				        data: blue
				    }, {
				    	type: 'spline',
				    	color: 'green',
				        name: 'Plan Injeksi YRS GREEN',
				        data: Mesingreen
				    },{
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'GREEN',
				        name: 'Target Assy YRS GREEN',
				        data: green
				    }, {
				    	type: 'spline',
				    	color: 'pink',
				        name: 'Plan Injeksi YRS PINK',
				        data: Mesinpink
				    },{
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'pink',
				        name: 'Target Assy YRS PINK',
				        data: pink
				    }, {
				    	type: 'spline',
				    	color: 'brown',
				        name: 'Plan Injeksi YRS BROWN',
				        data: Mesinbrown
				    },{
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'brown',
				        name: 'Target Assy YRS BROWN',
				        data: brown
				    }, {
				    	type: 'spline',
				    	color: 'Red',
				        name: 'Plan Injeksi YRS IVORY',
				        data: Mesinivory
				    },{
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'Red',
				        name: 'Target Assy YRS IVORY',
				        data: ivory
				    }, {
				    	type: 'spline',
				    	color: 'gray',
				        name: 'Plan Injeksi YRF IVORY',
				        data: Mesinyrf
				    },{
				    	type: 'spline',
				    	dashStyle: 'Dash',
				    	color: 'gray',
				        name: 'Target Assy YRF IVORY',
				        data: yrf
				    }]
				});

		$("#myModal").modal('hide');
				}
			}
		});
}

function saveScheduletmp() {

	
	// mesin1

	for (var i = 0; i < max; i++) {
		if (mesin1_r2[i].length < 1) {
			if (mesin1_r2[i] > 0) {
				PostMESIN1.push(dateMax[i]+'#'+mesin1_r2[i][0]+'#'+mesin1_r2[i][1]+'#'+mesin1_r2[i][2])
			}else{
				PostMESIN1.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < mesin1_r2[i].length; a++) {
				if (mesin1_r2[i][a][2] > 0) {
					PostMESIN1.push(dateMax[i]+'#'+mesin1_r2[i][a][0]+'#'+mesin1_r2[i][a][1]+'#'+mesin1_r2[i][a][2])
				}				
			}			
		}		
	}

	// end mesin1

	// MESIN2

	for (var i = 0; i < max; i++) {
		if (MESIN2_r2[i].length < 1) {
			if (MESIN2_r2[i] > 0) {
				PostMESIN2.push(dateMax[i]+'#'+MESIN2_r2[i][0]+'#'+MESIN2_r2[i][1]+'#'+MESIN2_r2[i][2])
			}else{
				PostMESIN2.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN2_r2[i].length; a++) {
				if (MESIN2_r2[i][a][2] > 0) {
					PostMESIN2.push(dateMax[i]+'#'+MESIN2_r2[i][a][0]+'#'+MESIN2_r2[i][a][1]+'#'+MESIN2_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN2
	// MESIN3

	for (var i = 0; i < max; i++) {
		if (MESIN3_r2[i].length < 1) {
			if (MESIN3_r2[i] > 0) {
				PostMESIN3.push(dateMax[i]+'#'+MESIN3_r2[i][0]+'#'+MESIN3_r2[i][1]+'#'+MESIN3_r2[i][2])
			}else{
				PostMESIN3.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN3_r2[i].length; a++) {
				if (MESIN3_r2[i][a][2] > 0) {
					PostMESIN3.push(dateMax[i]+'#'+MESIN3_r2[i][a][0]+'#'+MESIN3_r2[i][a][1]+'#'+MESIN3_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN3
	// MESIN4

	for (var i = 0; i < max; i++) {
		if (MESIN4_r2[i].length < 1) {
			if (MESIN4_r2[i] > 0) {
				PostMESIN4.push(dateMax[i]+'#'+MESIN4_r2[i][0]+'#'+MESIN4_r2[i][1]+'#'+MESIN4_r2[i][2])
			}else{
				PostMESIN4.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN4_r2[i].length; a++) {
				if (MESIN4_r2[i][a][2] > 0) {
					PostMESIN4.push(dateMax[i]+'#'+MESIN4_r2[i][a][0]+'#'+MESIN4_r2[i][a][1]+'#'+MESIN4_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN4
	// MESIN5

	for (var i = 0; i < max; i++) {
		if (MESIN5_r2[i].length < 1) {
			if (MESIN5_r2[i] > 0) {
				PostMESIN5.push(dateMax[i]+'#'+MESIN5_r2[i][0]+'#'+MESIN5_r2[i][1]+'#'+MESIN5_r2[i][2])
			}else{
				PostMESIN5.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN5_r2[i].length; a++) {
				if (MESIN5_r2[i][a][2] > 0) {
					PostMESIN5.push(dateMax[i]+'#'+MESIN5_r2[i][a][0]+'#'+MESIN5_r2[i][a][1]+'#'+MESIN5_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN5
	// MESIN6

	for (var i = 0; i < max; i++) {
		if (MESIN6_r2[i].length < 1) {
			if (MESIN6_r2[i] > 0) {
				PostMESIN6.push(dateMax[i]+'#'+MESIN6_r2[i][0]+'#'+MESIN6_r2[i][1]+'#'+MESIN6_r2[i][2])
			}else{
				PostMESIN6.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN6_r2[i].length; a++) {
				if (MESIN6_r2[i][a][2] > 0) {
					PostMESIN6.push(dateMax[i]+'#'+MESIN6_r2[i][a][0]+'#'+MESIN6_r2[i][a][1]+'#'+MESIN6_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN6
	// MESIN7

	for (var i = 0; i < max; i++) {
		if (MESIN7_r2[i].length < 1) {
			if (MESIN7_r2[i] > 0) {
				PostMESIN7.push(dateMax[i]+'#'+MESIN7_r2[i][0]+'#'+MESIN7_r2[i][1]+'#'+MESIN7_r2[i][2])
			}else{
				PostMESIN7.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN7_r2[i].length; a++) {
				if (MESIN7_r2[i][a][2] > 0) {
					PostMESIN7.push(dateMax[i]+'#'+MESIN7_r2[i][a][0]+'#'+MESIN7_r2[i][a][1]+'#'+MESIN7_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN7
	// MESIN8

	for (var i = 0; i < max; i++) {
		if (MESIN8_r2[i].length < 1) {
			if (MESIN8_r2[i] > 0) {
				PostMESIN8.push(dateMax[i]+'#'+MESIN8_r2[i][0]+'#'+MESIN8_r2[i][1]+'#'+MESIN8_r2[i][2])
			}else{
				PostMESIN8.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN8_r2[i].length; a++) {
				if (MESIN8_r2[i][a][2] > 0) {
					PostMESIN8.push(dateMax[i]+'#'+MESIN8_r2[i][a][0]+'#'+MESIN8_r2[i][a][1]+'#'+MESIN8_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN8
	// MESIN9

	for (var i = 0; i < max; i++) {
		if (MESIN9_r2[i].length < 1) {
			if (MESIN9_r2[i] > 0) {
				PostMESIN9.push(dateMax[i]+'#'+MESIN9_r2[i][0]+'#'+MESIN9_r2[i][1]+'#'+MESIN9_r2[i][2])
			}else{
				PostMESIN9.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN9_r2[i].length; a++) {
				if (MESIN9_r2[i][a][2] > 0) {
					PostMESIN9.push(dateMax[i]+'#'+MESIN9_r2[i][a][0]+'#'+MESIN9_r2[i][a][1]+'#'+MESIN9_r2[i][a][2])
				}				
			}			
		}		
	}

	// end MESIN9


	// mesin11

	for (var i = 0; i < max; i++) {
		if (MESIN11_r2[i].length < 1) {
			if (MESIN11_r2[i] > 0) {
				PostMESIN11.push(dateMax[i]+'#'+MESIN11_r2[i][0]+'#'+MESIN11_r2[i][1]+'#'+MESIN11_r2[i][2])
			}else{
				PostMESIN11.push(dateMax[i]+'#'+'OFF'+'#'+'OFF'+'#'+'0')
			}
			
		}else{
			for (var a = 0; a < MESIN11_r2[i].length; a++) {
				if (MESIN11_r2[i][a][2] > 0) {
					PostMESIN11.push(dateMax[i]+'#'+MESIN11_r2[i][a][0]+'#'+MESIN11_r2[i][a][1]+'#'+MESIN11_r2[i][a][2])
				}				
			}			
		}		
	}

	// end mesin11

	// alert(PostMESIN11);

	var data = {
		PostMESIN1:PostMESIN1,
		PostMESIN2:PostMESIN2,
		PostMESIN3:PostMESIN3,
		PostMESIN4:PostMESIN4,
		PostMESIN5:PostMESIN5,
		PostMESIN6:PostMESIN6,
		PostMESIN7:PostMESIN7,
		PostMESIN8:PostMESIN8,
		PostMESIN9:PostMESIN9,
		PostMESIN11:PostMESIN11,

	}

	$.post('{{ url("save/Scheduletmp") }}', data,  function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				

				openSuccessGritter('Success!', result.message);
				chartplan();
				percenMesin();
				// headblue();
				// blokblue();
				// mjblue();
				// footblue();
				injeksiVsAssyBlue();
				injeksiVsAssyGreen();
				injeksiVsAssyPink();
				injeksiVsAssyRed();
				injeksiVsAssyBrown();
				injeksiVsAssyIvory();

				chartWorkingMachine();


			}
			else{
				// audio_error.play();

			}
		}
		else{
			// audio_error.play();
			alert('Disconnected from sever');
		}
	});



	
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

function percenMesin() {

	$.get('{{ url("fetch/percenMesin") }}',  function(result, status, xhr) {
			console.log(status);
			console.log(result);
			console.log(xhr);

			var mesin = [];
			var on = [];
			var off = [];
			
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
							off.push(result.part[i].OFF);
							on.push(result.part[i].ON);
							mesin.push(result.part[i].mesin);
					}

						
					
					Highcharts.chart('chartplanMesin', {
				    chart: {
				        type: 'column'
				    },
				    title: {
				        text: 'Machine Monitoring'
				    },
				    xAxis: {
				        categories: mesin
				    },
				    yAxis: {
				        min: 0,
				        title: {
				            text: 'Last Update: '+getActualFullDate(),
				        }
				    },
				    tooltip: {
				        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
				        shared: true
				    },
				    plotOptions: {
				        column: {
				            stacking: 'percent'
				        }
				    },
				    series: [{
				    	dataLabels: {
				            enabled: true,
				            format: '{point.percentage:.0f}%',
				            },
				        name: 'ON',
				        data: on
				    }, {
				    	dataLabels: {
				            enabled: true,
				            format: '{point.percentage:.0f}%',
				            },
				        color: 'red',
				        name: 'OFF',
				        data: off
				    }]
				});

					
				}
			}
		})
}

function chartWorkingMachine() {

	$.get('{{ url("fetch/chartWorkingMachine") }}',  function(result, status, xhr) {
			console.log(status);
			console.log(result);
			console.log(xhr);

			var tgl = [];
			var total_1 = [];
			var total_2 = [];
			var total_3 = [];
			var total_4 = [];
			var total_5 = [];
			var total_6 = [];
			var total_7 = [];
			var total_8 = [];
			var total_9 = [];
			var total_11 = [];
			
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
							tgl.push(result.part[i].week_date);
							total_1.push(parseInt( result.part[i].total_1));
							total_2.push(parseInt( result.part[i].total_2));							
							total_3.push(parseInt( result.part[i].total_3));
							total_4.push(parseInt( result.part[i].total_4));
							total_5.push(parseInt( result.part[i].total_5));
							total_6.push(parseInt( result.part[i].total_6));
							total_7.push(parseInt( result.part[i].total_7));
							total_8.push(parseInt( result.part[i].total_8));
							total_9.push(parseInt( result.part[i].total_9));
							total_11.push(parseInt( result.part[i].total_11));
					}

						
					
					Highcharts.chart('chartplanMesin2', {
				    chart: {
				        type: 'spline'
				    },
				    title: {
				        text: 'Working Machine Monitoring'
				    },
				    xAxis: {
				        categories: tgl
				    },
				    yAxis: {
				        min: 0,
				        title: {
				            text: 'Last Update: '+getActualFullDate(),
				        }
				    },
				    tooltip: {
				        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> <br/>',
				        shared: true
				    },
				    plotOptions: {
				        column: {
				            stacking: 'percent'
				        }
				    },
				    series: [{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 1',
				        data: total_1
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 2',
				        data: total_2
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 3',
				        data: total_3
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 4',
				        data: total_4
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 5',
				        data: total_5
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 6',
				        data: total_6
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 7',
				        data: total_7
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 8',
				        data: total_8
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 9',
				        data: total_9
				    },{
				    	dataLabels: {
				            enabled: true,
				            },
				        name: 'MESIN 11',
				        data: total_11
				    },]
				});

					
				}
			}
		})
}

function mjblue() {	

	var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var green = [];
		var act = [];
		var tgl = [];
		var actgreen = 0;
		var actgreen2 = 0;

		$.get('{{ url("fetch/mjblue") }}', data,  function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						green.push(parseInt( result.part[i].target));

						
						actgreen = (parseInt( result.part[i].target - result.part[i].assy    ) + actgreen2);
						actgreen2 = actgreen;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act.push(parseInt( actgreen2));

					}

					
					Highcharts.chart('mjblue', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'Middle Joint Blue',
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	color: 'blue',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi',
				    	color: 'blue',
				        data: green
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock',
				    	color: 'blue',
				        data: act
				    },]
					});
				}
			}
		});
	}

	function footblue() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var green = [];
		var act = [];
		var tgl = [];
		var actgreen = 0;
		var actgreen2 = 0;




		$.get('{{ url("fetch/footblue") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						green.push(parseInt( result.part[i].target));

						
						actgreen = (parseInt( result.part[i].target - result.part[i].assy    ) + actgreen2);
						actgreen2 = actgreen;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act.push(parseInt( actgreen2));

					}

					
					Highcharts.chart('footblue', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'Foot Joint Blue',
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	color: 'blue',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi',
				    	color: 'blue',
				        data: green
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock',
				    	color: 'blue',
				        data: act
				    },]
					});
				}
			}
		});
	}

	function blokblue() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var green = [];
		var act = [];
		var tgl = [];
		var actgreen = 0;
		var actgreen2 = 0;




		$.get('{{ url("fetch/blockblue") }}', data,  function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						green.push(parseInt( result.part[i].target));

						
						actgreen = (parseInt( result.part[i].target - result.part[i].assy    ) + actgreen2);
						actgreen2 = actgreen;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act.push(parseInt( actgreen2));

					}

					
					Highcharts.chart('blokblue', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'Block Joint Blue',
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	color: 'blue',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi',
				    	color: 'blue',
				        data: green
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock',
				    	color: 'blue',
				        data: act
				    },]
					});
				}
			}
		});
	}

	function headblue() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var green = [];
		var act = [];
		var tgl = [];
		var actgreen = 0;
		var actgreen2 = 0;




		$.get('{{ url("fetch/headblue") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						green.push(parseInt( result.part[i].target));

						
						actgreen = (parseInt( result.part[i].target - result.part[i].assy    ) + actgreen2);
						actgreen2 = actgreen;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act.push(parseInt( actgreen2));

					}

					
					Highcharts.chart('headblue', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'Head Joint Blue',
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	color: 'blue',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi',
				    	color: 'blue',
				        data: green
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock',
				    	color: 'blue',
				        data: act
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyBlue() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyBlue") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyBlue', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS BLUE All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyGreen() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyGreen") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyGreen', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS GREEN All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyPink() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyPink") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyPink', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS PINK All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyRed() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyRed") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyRed', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS RED All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyBrown() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyBrown") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyBrown', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS BROWN All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
		});
	}

	function injeksiVsAssyIvory() {	

		var tgl1 = $('#tanggal').val();
		var tgl22 = $('#tanggal22').val();
		var data = {
			from:tgl1,
			toa:tgl22,
		}

		var assy = [];
		var p_mj = [];
		var p_hj = [];
		var p_bj = [];
		var p_fj = [];

		var act_mj = [];
		var act_hj = [];
		var act_bj = [];
		var act_fj = [];

		var tgl = [];

		var mj = 0;
		var mj2 = 0;

		var bj = 0;
		var bj2 = 0;

		var fj = 0;
		var fj2 = 0;

		var hj = 0;
		var hj2 = 0;

		$.get('{{ url("fetch/injeksiVsAssyIvory") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					for (var i = 0; i < result.part.length; i++) {
						tgl.push(result.part[i].week_date);
						assy.push(parseInt( result.part[i].assy));
						p_mj.push(parseInt( result.part[i].mj));
						p_bj.push(parseInt( result.part[i].block));
						p_hj.push(parseInt( result.part[i].head));
						p_fj.push(parseInt( result.part[i].foot));

						
						mj = (parseInt( result.part[i].mj - result.part[i].assy    ) + mj2);
						mj2 = mj;
						// alert(result.part[i].target +' - '+ result.part[i].assy +' = '+actgreen2)
						act_mj.push(parseInt( mj2));

						bj = (parseInt( result.part[i].block - result.part[i].assy    ) + bj2);
						bj2 = bj;
						act_bj.push(parseInt( bj2));

						hj = (parseInt( result.part[i].head - result.part[i].assy    ) + hj2);
						hj2 = hj;
						act_hj.push(parseInt( hj2));

						fj = (parseInt( result.part[i].foot - result.part[i].assy    ) + fj2);
						fj2 = fj;
						act_fj.push(parseInt( fj2));

					}


					Highcharts.chart('injeksiVsAssyIvory', {
					    chart: {
					        type: 'spline'
					    },
					    title: {
							text: 'YRS IVORY All PART ',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'Last Update: '+getActualFullDate(),
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
					    xAxis: {
					        categories: tgl,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					        	dataLabels: {
				                enabled: true
				            },
					            pointPadding: 0.2,
					            borderWidth: 0
					        },
					        spline: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: true
				        },
				        
					    },
					    series: [{
      					animation: false,
					    name: 'Plan Assy',
					    dashStyle: 'Dash',
				    	// color: 'Red',
					    data: assy

					    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Middle Joint',
				    	// color: 'Red',
				        data: p_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Head Joint',
				    	// color: 'Red',
				        data: p_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Block Joint',
				    	// color: 'Red',
				        data: p_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
				        name: 'Plan Injeksi Foot Joint',
				    	// color: 'Red',
				        data: p_fj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Middle Joint',
				    	// color: 'Red',
				        data: act_mj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Head Joint',
				    	// color: 'Red',
				        data: act_hj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Block Joint',
				    	// color: 'Red',
				        data: act_bj
				    },{
				    	type: 'spline',				    	
      					animation: false,
      					dashStyle: 'Dot',
				        name: 'Stock Foot Joint',
				    	// color: 'Red',
				        data: act_fj
				    },]
					});
				}
			}
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
</script>
@endsection