@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	
	.box-header{
		text-transform: uppercase;
	}

	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #e57373;
		}
		50%, 100% {
			background-color: #ffccff;
		}
	}

	.alarm {
		-webkit-animation: alarm_ani 1s infinite;  /* Safari 4+ */
		-moz-animation: alarm_ani 1s infinite;  /* Fx 5+ */
		-o-animation: alarm_ani 1s infinite;  /* Opera 12+ */
		animation: alarm_ani 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes alarm_ani {
		0%, 49% {
			background-color: #57ff5c;
		}
		50%, 100% {
			background-color: #ed2f2f;
		}
	}

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
	<div class="row" style="padding: 0">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-8" id="panel_machine" style="padding-right:20px;padding-left: 20px;">
					<!-- <div class="col-xs-3" style="border: 1px solid white;" >
						<h4 style="color:white">
							<center>
								Mesin
							</center>
						</h4>
						<center>
							<div style="display: inline-block;background-color: #222222; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div> 
							<div style="display: inline-block;background-color: #00c853; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>
						</center>
					</div> -->
				</div>
				<div class="col-xs-2">
					<div class="row" style="margin:0">
						<div class="col-xs-12" style="padding-right:0;">

							@foreach($location as $loc)
							<?php $locfix = str_replace(" ","_",$loc->location); ?>

							<table style="width: 100%;margin-top: 5px">
								<tbody>
									<tr>
										<td colspan="2" style="background-color: #a763ff;text-align: center; font-size: 1.8vw; font-weight: bold;color:white;border:2px solid black;">{{$loc->location}}</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 1.2vw; font-weight: bold;border:2px solid black;">Temperature</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 4vw; font-weight: bold;border:2px solid black;" id="temp_{{$locfix}}">0 °C</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Min</td>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Max</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="min_temp_{{$locfix}}">0</td>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="max_temp_{{$locfix}}">0</td>
									</tr>
								</tbody>
							</table>

							<table style="width: 100%;margin-top: 5px">
								<tbody>
									<tr>
										<td colspan="2" style="background-color: #a763ff;text-align: center; font-size: 1.8vw; font-weight: bold;color:white;border:2px solid black;">{{$loc->location}}</td>
									</tr>
									<tr>
										<td class="humidity_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 1.2vw; font-weight: bold;border:2px solid black;">Humidity</td>
									</tr>
									<tr>
										<td class="humidity_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 4vw; font-weight: bold;border:2px solid black;" id="hum_{{$locfix}}">0 %</td>
									</tr>
									<tr>
										<td class="humidity_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Min</td>
										<td class="humidity_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Max</td>
									</tr>
									<tr>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="min_hum_{{$locfix}}">0</td>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="max_hum_{{$locfix}}">0</td>
									</tr>
								</tbody>
							</table>

							@endforeach

							<table style="width: 100%;margin-top: 5px">
								<tbody>
									<tr>
										<td colspan="2" style="background-color: #a763ff;text-align: center; font-size: 1.1vw; font-weight: bold;color:white;border:2px solid black;">Chiller Storage Temp.</td>
									</tr>
									<tr>
										<td colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 4vw; font-weight: bold;border:2px solid black;" id="tandon">0 &#8451;</td>
									</tr>
								</tr>
								<tr>
									<td style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Min</td>
									<td style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Max</td>
								</tr>
								<tr>
									<td style="background-color: #7dfa8c;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="min_tandon">5.5 &#8451;</td>
									<td style="background-color: #7dfa8c;text-align: center; font-size: 1.5vw; font-weight: bold;border:2px solid black;" id="max_tandon">14.5 &#8451;</td>
								</tr>
							</tbody>
						</table>

							<!-- @foreach($location as $loc)
							<?php $locfix = str_replace(" ","_",$loc->location); ?>

							<table style="width: 100%;margin-top: 5px">
								<tbody>
									<tr>
										<td colspan="4" style="background-color: #a763ff;text-align: center; font-size: 1.8vw; font-weight: bold;color:white;border:2px solid black;">{{$loc->location}}</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 1.2vw; font-weight: bold;border:2px solid black;">Temperature</td>
										<td class="humidity_{{$locfix}}" colspan="2" style="background-color: #eef132;text-align: center; font-size: 1.2vw; font-weight: bold;border:2px solid black;">Humidity</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" colspan="2" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="temp_{{$locfix}}">0 °C</td>
										<td class="humidity_{{$locfix}}" colspan="2" style="background-color: #eef132;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="hum_{{$locfix}}">0 %</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Min</td>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Max</td>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Min</td>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" >Max</td>
									</tr>
									<tr>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="min_temp_{{$locfix}}">0</td>
										<td class="temperature_{{$locfix}}" style="background-color: #7dfa8c;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="max_temp_{{$locfix}}">0</td>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="min_hum_{{$locfix}}">0</td>
										<td class="humidity_{{$locfix}}" style="background-color: #eef132;text-align: center; font-size: 1vw; font-weight: bold;border:2px solid black;" id="max_hum_{{$locfix}}">0</td>
									</tr>
									
									-----------------------------------------------------

									<tr style="background-color: #7dfa8c;">
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold; text-align: center" id="min_temp">--&#8451;</td>
										<td style="text-align: center; font-size: 1.5vw; font-weight: bold; text-align: center" id="temp">--&#8451;</td>
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold; text-align: center" id="max_temp">--&#8451;</td>
									</tr>
									<tr style="background-color: #eef132;">
										<td colspan="3" style="text-align: center; font-size: 1.2vw; font-weight: bold;">Humidity</td>
									</tr>
									<tr style="background-color: #eef132;">
										<td style="text-align: center; font-size: 1vw; font-weight: bold;">Min</td>
										<td style="text-align: center; font-size: 1vw; font-weight: bold;">Current</td>
										<td style="text-align: center; font-size: 1vw; font-weight: bold;">Max</td>
									</tr>
									<tr style="background-color: #eef132;">
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold; text-align: center" id="min_hum">--</td>
										<td style="text-align: center; font-size: 1.5vw; font-weight: bold; text-align: center" id="hum">--</td>
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold; text-align: center" id="max_hum">--</td>
									</tr>
								</tbody>
							</table>

							@endforeach -->

							<!-- <table id="humidity" style="background-color: #eef132; width: 100%;">
								<tbody>
									<tr>
										<td colspan="4" style="text-align: center; font-size: 1.8vw; font-weight: bold;">HUMIDITY</td>
									</tr>
									<tr>
										<td style="min-width: 0.1%; font-size: 1.5vw;">MIN:</td>
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="min_hum">--</td>
										<td style="min-width: 0.1%; font-size: 1.5vw;">MAX:</td>
										<td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="max_hum">--</td>
									</tr>
									<tr>
										<td colspan="4" style="text-align: center; font-size: 5vw; font-weight: bold;" id="hum">--</td>
									</tr>
								</tbody>
							</table> -->
						</div>
					</div>
				</div>


				<div class="col-xs-2">
					<div class="col-xs-12" style="padding-left: 10px;padding-right: 0">
						<div id="container" style="width: 100%;"></div>
					</div>

					<div class="col-xs-12" style="padding-left: 10px;padding-right: 0;margin-top: 10px">
						<div id="container2" style="width: 100%;"></div>
					</div>

					<div class="col-xs-12" style="padding-left: 10px;padding-right: 0;margin-top: 10px">
						<div id="container3" style="width: 100%;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		getData();
		setInterval(getData, 60000 * 1);
	});

	var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	// ,"Boiler_Solar"
	var boiler = ["Boiler_LNG"];
	// ,"Atlas_ZT75","Atlas_ZT37"
	var compressor = ["Kobelco_AG_37","Kobelco_AG_1070","Kobelco_VS_660","Kobelco_VS_1020","Mitsui"];
	var chiller = ["PNT_1","PNT_2","PLT"];
	// "Injection_Old","Injection_New","Kohki_Soudon_B-PRO","Kohki_Soudon_PP",
	var ct = ["Soldering"];
	var pump = ["Domestic_Pump"];

	function getData(){

		$.get('http://10.109.33.33/mirai/public/fetch/maintenance/tpm/pressure', function(result, status, xhr) {
			if(result.status){
				$("#panel_machine").empty();
				var body_panel = "";

				body_panel += '<div class="col-xs-12" style="padding:5;background-color:#a763ff;border: 1px solid white"><h3 style="color:white;margin-top:10px">Boiler</h3></div>';

				$.each(boiler, function(index, item) {
					body_panel += "";
					var color_1 = '#222222';
					var color_2 = '#222222';

					var item_fix = item.replace(/\_/g, ' ');

					body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
					body_panel += '<h4 style="color:white"><center>';
					body_panel += ''+item_fix;
					body_panel += '</center></h4>';
					body_panel += '<center>'

					$.each(result.machine_status, function(key2, value2) {
						if (value2.unit == item) {
							if (result.last_status == 1) {
								if (value2.remark == 'ON') {
									color_1 = '#222222';
									color_2 = '#00c853';
								} else {
									color_1 = '#df5353';
									color_2 = '#222222';
								}
							} else {
								color_1 = '#292828';
								color_2 = '#292828';
							}
						}
					});
					
					body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
					body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
					body_panel += '</center>';
					body_panel += '</div>';
					// body_panel += '</div>';
				})

				body_panel += '<div class="col-xs-12" style="padding:5;background-color:#a763ff;border: 1px solid white"><h3 style="color:white;margin-top:10px">Compressor</h3></div>';

				$.each(compressor, function(index, item) {
					body_panel += "";
					var color_1 = '#222222';
					var color_2 = '#222222';

					var item_fix = item.replace(/\_/g, ' ');

					body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
					body_panel += '<h4 style="color:white"><center>';
					body_panel += ''+item_fix;
					body_panel += '</center></h4>';
					body_panel += '<center>'

					$.each(result.machine_status, function(key2, value2) {
						if (value2.unit == item) {
							if (result.last_status == 1) {
								if (value2.remark == 'ON') {
									color_1 = '#222222';
									color_2 = '#00c853';
								} else {
									color_1 = '#df5353';
									color_2 = '#222222';
								}
							} else {
								color_1 = '#292828';
								color_2 = '#292828';
							}
						}
					});
					
					body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
					body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
					body_panel += '</center>';
					body_panel += '</div>';
					// body_panel += '</div>';
				})


				body_panel += '<div class="col-xs-12" style="padding:5;background-color:#a763ff;border: 1px solid white"><h3 style="color:white;margin-top:10px">Chiller</h3></div>';

				$.each(chiller, function(index, item) {
					body_panel += "";
					var color_1 = '#222222';
					var color_2 = '#222222';

					var item_fix = item.replace(/\_/g, ' ');

					body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
					body_panel += '<h4 style="color:white"><center>';
					body_panel += ''+item_fix;
					body_panel += '</center></h4>';
					body_panel += '<center>'

					$.each(result.machine_status, function(key2, value2) {
						if (value2.unit == item) {
							if (result.last_status == 1) {
								if (value2.remark == 'ON') {
									color_1 = '#222222';
									color_2 = '#00c853';
								} else {
									color_1 = '#df5353';
									color_2 = '#222222';
								}
							} else {
								color_1 = '#292828';
								color_2 = '#292828';
							}
						}
					});
					
					body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
					body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
					body_panel += '</center>';
					body_panel += '</div>';
					// body_panel += '</div>';
				})

				body_panel += '<div class="col-xs-12" style="padding:5;background-color:#a763ff;border: 1px solid white"><h3 style="color:white;margin-top:10px">Cooling Tower</h3></div>';

				$.each(ct, function(index, item) {
					body_panel += "";
					var color_1 = '#222222';
					var color_2 = '#222222';

					var item_fix = item.replace(/\_/g, ' ');

					body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
					body_panel += '<h4 style="color:white"><center>';
					body_panel += ''+item_fix;
					body_panel += '</center></h4>';
					body_panel += '<center>'

					$.each(result.machine_status, function(key2, value2) {
						if (value2.unit == item) {
							if (result.last_status == 1) {
								if (value2.remark == 'ON') {
									color_1 = '#222222';
									color_2 = '#00c853';
								} else {
									color_1 = '#df5353';
									color_2 = '#222222';
								}
							} else {
								color_1 = '#292828';
								color_2 = '#292828';
							}
						}
					});
					
					body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
					body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
					body_panel += '</center>';
					body_panel += '</div>';
					// body_panel += '</div>';
				})

				body_panel += '<div class="col-xs-12" style="padding:5;background-color:#a763ff;border: 1px solid white"><h3 style="color:white;margin-top:10px">Pump Room</h3></div>';

				$.each(pump, function(index, item) {
					body_panel += "";
					var color_1 = '#222222';
					var color_2 = '#222222';

					var item_fix = item.replace(/\_/g, ' ');

					body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
					body_panel += '<h4 style="color:white"><center>';
					body_panel += ''+item_fix;
					body_panel += '</center></h4>';
					body_panel += '<center>'

					$.each(result.machine_status, function(key2, value2) {
						if (value2.unit == item) {
							if (result.last_status == 1) {
								if (value2.remark == 'ON') {
									color_1 = '#222222';
									color_2 = '#00c853';
								} else {
									color_1 = '#df5353';
									color_2 = '#222222';
								}
							} else {
								color_1 = '#292828';
								color_2 = '#292828';
							}
						}
					});
					
					body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
					body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
					body_panel += '</center>';
					body_panel += '</div>';
					// body_panel += '</div>';
				})



				// $.each(result.machine_status, function(key2, value2) {
				// 	body_panel += "";
				// 	var color_1 = '';
				// 	var color_2 = '';

				// 	body_panel += '<div class="col-xs-3" style="border: 1px solid white;" >';
				// 	body_panel += '<h4 style="color:white"><center>';
				// 	body_panel += 'Mesin '+value2.unit;
				// 	body_panel += '</center></h4>';
				// 	body_panel += '<center>'
				// 	if (result.last_status == 1) {
				// 		if (value2.remark == 'ON') {
				// 			color_1 = '#222222';
				// 			color_2 = '#00c853';
				// 		} else {
				// 			color_1 = '#df5353';
				// 			color_2 = '#222222';
				// 		}
				// 	} else {
				// 		color_1 = '#292828';
				// 		color_2 = '#292828';
				// 	}
				// 	body_panel += '<div style="display: inline-block;background-color: '+color_1+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>';
				// 	body_panel += '<div style="display: inline-block;background-color: '+color_2+'; border-radius: 50%; width: 50px;height: 50px;margin-bottom: 20px;border:2px solid black"></div>'
				// 	body_panel += '</center>';
				// 	body_panel += '</div>';
				// })

				$("#panel_machine").append(body_panel);

				var data_compressor = [];
				var data_ahu = [];
				var data_boiler = [];

				$.each(result.list_suhu, function(key, value) {
					var loc = value.location.replace(" ","_");

					if (value.upper_limit == null && value.lower_limit == null) {
						value.upper_limit = '-';
						value.lower_limit = '-';
					}

					if (value.upper_limit == null) {
						value.upper_limit = '-';
					}

					if (value.lower_limit == null) {
						value.lower_limit = '-';
					}

					if(value.remark == "temperature"){
						$('#min_temp_'+loc).text(value.lower_limit+" °C");
						$('#max_temp_'+loc).text(value.upper_limit+" °C");
					} else if(value.remark == "humidity"){
						$('#min_hum_'+loc).text(value.lower_limit+" %");
						$('#max_hum_'+loc).text(value.upper_limit+" %");
					}

					if (value.value > value.upper_limit || value.value < value.lower_limit ) {
						if(value.remark == "temperature"){
							$(".temperature_"+loc).css('background-color', '#e57373');
							$(".temperature_"+loc).addClass('sedang');
							
							$('#temp_'+loc).text(value.value+" °C");
							alarm_error.play();

						}
						else if(value.remark == "humidity"){
							$(".humidity_"+loc).css('background-color', '#e57373');
							$(".humidity_"+loc).addClass('sedang');
							$('#hum_'+loc).text(value.value+" %");
							alarm_error.play();
						}
					}
					else{
						if(value.remark == "temperature"){

							$(".temperature_"+loc).css('background-color', '#7dfa8c');
							// document.getElementsByClassName('temperature').style.backgroundColor = "#7dfa8c";
							$('.temperature_'+loc).removeClass('sedang');
							$('#temp_'+loc).text(value.value+" °C");
						}
						else if(value.remark == "humidity"){
							$(".humidity_"+loc).css('background-color', '#eef132');
							// document.getElementsByClassName('humidity').style.backgroundColor = "#eef132";
							$('.humidity_'+loc).removeClass('sedang');
							$('#hum_'+loc).text(value.value+" %");
						}
					}

				});	

				$.each(result.lists, function(key, value) {
					if (value.location == "Air Compressor") {
						data_compressor.push(parseFloat(value.value/100));
					}
					if (value.location == "Steam AHU") {
						data_ahu.push(parseFloat(value.value/100));
					}
					if (value.location == "Steam Boiler") {
						data_boiler.push(parseFloat(value.value/10));
					}
				});

				// -- suhu tandon
				$("#tandon").html(result.pump_data[0].value_sensor+ ' &#8451;');
				if (parseFloat(result.pump_data[0].value_sensor) < 5.5 || parseFloat(result.pump_data[0].value_sensor) >= 14.5) {
					$("#tandon").addClass('alarm');
				} else {
					$("#tandon").removeClass('alarm');
				}

				Highcharts.chart('container', {
					chart: {
						type: 'gauge',
						plotBackgroundColor: null,
						plotBackgroundImage: null,
						plotBorderWidth: 0,
						plotShadow: false,
						color: '#000',
						height:'300'
					},

					title: {
						text: 'AIR COMPRESSOR',
						style: {
							fontSize: '16px',
							fontWeight: 'bold'
						}
					},

					pane: {
						startAngle: -150,
						endAngle: 150,
						color : '#000',
						background: [{
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#FFF'],
								[1, '#333']
								]
							},
							borderWidth: 0,
							outerRadius: '109%'
						}, {
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#333'],
								[1, '#FFF']
								]
							},
							borderWidth: 1,
							outerRadius: '107%'
						}, {
						}, {
							backgroundColor: '#DDD',
							borderWidth: 0,
							outerRadius: '105%',
							innerRadius: '103%'
						}]
					},

				    // the value axis
				    yAxis: {
				    	tickInterval:1,
				    	min: 0,
				    	max: 8,
				    	minorTickInterval: 'auto',
				    	minorTickWidth: 1,
				    	minorTickLength: 10,
				    	minorTickPosition: 'inside',
				    	minorTickColor: '#666',

				    	tickPixelInterval: 30,
				    	tickWidth: 2,
				    	tickPosition: 'inside',
				    	tickLength: 10,
				    	tickColor: '#666',
				    	labels: {
				    		step: 1,
				    		rotation: 'auto',
				    		style: {
				    			color:'#000',
				    			fontSize:'14px'
				    		},
				    	},
				    	title: {
				    		text: 'kgf/cm2',

				    	},
				    	plotBands: [

				    	{
				    		from: 0,
				    		to: 6,
				            color: '#DF5353' // red
				        }, {
				        	from: 6,
				        	to: 7.5,
				            color: '#55BF3B' // green
				        }, {
				        	from: 7.5,
				        	to: 8,
				            color: '#DF5353' // red
				        }]
				    },

				    credits: {
				    	enabled: false
				    },

				    series: [{
				    	name: 'Pressure',
				    	data: data_compressor,
				    	color:'#000000',
				    	tooltip: {
				    		valueSuffix: ' kgf/cm2'
				    	},
				    	dataLabels: {
				    		style: {
				    			fontSize: '26px',
				    			color:'#000'
				    		}
				    	}
				    }]

				});

				Highcharts.chart('container2', {
					chart: {
						type: 'gauge',
						plotBackgroundColor: null,
						plotBackgroundImage: null,
						plotBorderWidth: 0,
						plotShadow: false,
						height:'300'
					},

					title: {
						text: 'Steam AHU',
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},

					pane: {
						startAngle: -150,
						endAngle: 150,
						background: [{
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#FFF'],
								[1, '#333']
								]
							},
							borderWidth: 0,
							outerRadius: '109%'
						}, {
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#333'],
								[1, '#FFF']
								]
							},
							borderWidth: 1,
							outerRadius: '107%'
						}, {
						}, {
							backgroundColor: '#DDD',
							borderWidth: 0,
							outerRadius: '105%',
							innerRadius: '103%'
						}]
					},

				    // the value axis
				    yAxis: {
				    	tickInterval:0.2,
				    	min: 0,
				    	max: 4.5,
				    	minorTickInterval: 'auto',
				    	minorTickWidth: 1,
				    	minorTickLength: 10,
				    	minorTickPosition: 'inside',
				    	minorTickColor: '#666',

				    	tickPixelInterval: 30,
				    	tickWidth: 2,
				    	tickPosition: 'inside',
				    	tickLength: 10,
				    	tickColor: '#666',
				    	labels: {
				    		step: 1,
				    		rotation: 'auto',
				    		style: {
				    			color:'#000',
				    			fontSize:'14px'
				    		},
				    	},
				    	title: {
				    		text: 'kgf/cm2'
				    	},
				    	plotBands: [

				    	{
				    		from: 0,
				    		to: 1.5,
				            color: '#DF5353' // red
				        }, {
				        	from: 1.5,
				        	to: 3,
				            color: '#55BF3B' // green
				        }, {
				        	from: 3,
				        	to: 4.5,
				            color: '#55BF3B' // green
				        }]
				    },

				    credits: {
				    	enabled: false
				    },

				    series: [{
				    	name: 'Steam AHU',
				    	data: data_ahu,
				    	tooltip: {
				    		valueSuffix: ' kgf/cm2'
				    	},
				    	dataLabels: {
				    		style: {
				    			fontSize: '26px',
				    			color:'#000'
				    		}
				    	}
				    }]

				});

				Highcharts.chart('container3', {
					chart: {
						type: 'gauge',
						plotBackgroundColor: null,
						plotBackgroundImage: null,
						plotBorderWidth: 0,
						plotShadow: false,
						height:'300'
					},

					title: {
						text: 'Steam Boiler',
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},

					pane: {
						startAngle: -150,
						endAngle: 150,
						background: [{
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#FFF'],
								[1, '#333']
								]
							},
							borderWidth: 0,
							outerRadius: '109%'
						}, {
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
								stops: [
								[0, '#333'],
								[1, '#FFF']
								]
							},
							borderWidth: 1,
							outerRadius: '107%'
						}, {
						}, {
							backgroundColor: '#DDD',
							borderWidth: 0,
							outerRadius: '105%',
							innerRadius: '103%'
						}]
					},

				    // the value axis
				    yAxis: {
				    	tickInterval:0.5,
				    	min: 0,
				    	max: 6,
				    	minorTickInterval: 'auto',
				    	minorTickWidth: 1,
				    	minorTickLength: 10,
				    	minorTickPosition: 'inside',
				    	minorTickColor: '#666',

				    	tickPixelInterval: 30,
				    	tickWidth: 2,
				    	tickPosition: 'inside',
				    	tickLength: 10,
				    	tickColor: '#666',
				    	labels: {
				    		step: 1,
				    		rotation: 'auto',
				    		style: {
				    			color:'#000',
				    			fontSize:'14px'
				    		},
				    	},
				    	title: {
				    		text: 'kgf/cm2'
				    	},
				    	plotBands: [
				    	{
				    		from: 0,
				    		to: 2.5,
				            color: '#DF5353' // red
				        }, {
				        	from: 2.5,
				        	to: 5,
				            color: '#55BF3B' // green
				        }, {
				        	from: 5,
				        	to: 6,
				            color: '#DF5353' // red
				        }]
				    },

				    credits: {
				    	enabled: false
				    },

				    series: [{
				    	name: 'Steam Boiler',
				    	data: data_boiler,
				    	tooltip: {
				    		valueSuffix: ' kgf/cm2'
				    	},
				    	dataLabels: {
				    		style: {
				    			fontSize: '26px',
				    			color:'#000'
				    		}
				    	}
				    }]

				});
			}
			else{
				alert('Attempt to retrieve data failed');
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

</script>
@endsection