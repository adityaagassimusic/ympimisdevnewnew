@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.table-bordered > tbody > tr > th, .table-bordered > tbody > tr > td{
		border: 1px solid black !important;
	}
	<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#moldingLog > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#moldingLogPasang > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:150px;
		overflow-y: scroll;
	}

	#molding_pasang {
		height:150px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	#tableParameter > thead > tr > th{
		color: white;
	}

	#tableParameter2 > thead > tr > th{
		color: white;
	}

	#tableParameter3 > thead > tr > th{
		color: white;
	}

	.tabledesign{
		font-size: 12px;
	}

	.blink_me {
	  animation: blinker 1s linear infinite;
	}

	@keyframes blinker {
	  50% {
	    opacity: 0.60;
	  },
	}
	#loading, #error { display: none; }
	.table-condensed > thead {
		background-color: white !important;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px;padding-left: 10px;padding-right: 10px">
			<div class="col-xs-3" style="background-color: rgb(126,86,134);padding-left: 0px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span></center>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;padding-right: 5px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select id="machine" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Machine">
					<option value=""></option>
					@foreach($mesin as $mesin)
					<option value="{{$mesin}}">{{$mesin}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select id="part" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Part">
					<option value=""></option>
					@foreach($parts as $parts)
					<option value="{{$parts->part}}">{{$parts->part}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 0px">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search
				</button>
			</div>
		</div>
		<div class="col-xs-6" style="padding-right: 5px;padding-left: 5px" id="loading_left">
			<div id="container" style="height: 85vh;">
				<div class="col-xs-12 blink_me" style="padding-right: 5px;padding-left: 5px;padding-bottom:0px;margin-bottom:0px;height: 85vh">
					<div class="box-group" id="accordion">
		                <div class="panel box box-solid" style="padding-bottom:5px;margin-bottom:5px;height: 85vh">
		                  <div class="box-header with-border" style="">
			                    <h5 class="box-title" style="font-weight:bold">&nbsp;<br>
			                    <span style="font-weight:normal;font-size:13px;">&nbsp;</span><br>
			                    <span style="font-weight:normal;font-size:13px;">&nbsp;</span>
			                    </h5>
			                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" class="collapsed pull-right align-self-end" style="text-align:bottom">&nbsp;</a>
				          </div>
		                </div>
		            </div>
				</div>
			</div>
		</div>
		<div class="col-xs-6" style="padding-right: 5px;padding-left: 5px" id="data_ng">
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">BEST PARAMETER</span></center>
			</div>
			<?php for ($i=0; $i < 18; $i++) { ?>
				<div class="col-xs-4 blink_me" style="padding-right: 5px;padding-left: 5px;padding-bottom:0px;margin-bottom:0px">
					<div class="box-group" id="accordion">
		                <div class="panel box box-solid" style="padding-bottom:5px;margin-bottom:5px">
		                  <div class="box-header with-border" style="">
			                    <h5 class="box-title" style="font-weight:bold">&nbsp;<br>
			                    <span style="font-weight:normal;font-size:13px;">&nbsp;</span><br>
			                    <span style="font-weight:normal;font-size:13px;">&nbsp;</span>
			                    </h5>
			                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" class="collapsed pull-right align-self-end" style="text-align:bottom">&nbsp;</a>
				          </div>
		                </div>
		            </div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
@stop
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/highcharts-3d.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script src="{{ url("bower_components/moment/moment.js")}}"></script>
	<script src="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	var chart;

	jQuery(document).ready(function() {
		$('.select2').select2({
				allowClear:true
			});
		fillList();
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function fillList() {
		$('#loading').show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			machine:$('#machine').val(),
			part:$('#part').val(),
		}
		$.get('{{ url("fetch/recorder/display/parameter") }}', data, function(result, status, xhr){
			if(result.status){
				$('#data_ng').html('');
				var data_ngs = '';

				data_ngs += '<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;height:36px;vertical-align: middle;">';
				data_ngs += '<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">BEST PARAMETER</span></center>';
				data_ngs += '</div>';
				for(var i = 0; i < result.data_ng.length;i++){
					data_ngs += '<div class="col-xs-4" style="padding-right: 3px;padding-left: 3px;padding-bottom:0px;margin-bottom:0px">';
						data_ngs += '<div class="box-group" id="accordion">';
			                data_ngs += '<div class="panel box box-solid" style="padding-bottom:5px;margin-bottom:5px">';
			                  data_ngs += '<div class="box-header with-border" style="">';
			                    data_ngs += '<h5 class="box-title" style="font-weight:bold">';
			                    data_ngs += result.data_ng[i].mesin+'  - '+result.data_ng[i].part+'<br>';
			                    data_ngs += '<span style="font-weight:normal;font-size:13px;">Periode : '+result.dateTitleFirst+' ~ '+result.dateTitleLast+'</span><br>';
			                    var ratio = 0;
			                    var qty_check = 0;
			                    for(var j = 0; j < result.prod_result.length;j++){
			                    	if (result.prod_result[j][0].check_date == result.data_ng[i].check_date && result.prod_result[j][0].part == result.data_ng[i].part) {
			                    		if (result.prod_result[j][0].qty != 0) {
			                    			ratio = parseInt(result.data_ng[i].data_ng.split('_')[0]) / parseInt(result.prod_result[j][0].qty);
			                    		}
			                    		qty_check = result.prod_result[j][0].qty;
			                    	}
			                    }
			                    data_ngs += '<span style="font-weight:normal;font-size:13px;">NG Ratio : '+ratio.toFixed(3)+' %</span>';
			                    data_ngs += '</h5>';
			                    data_ngs += '<a data-toggle="collapse" data-parent="#accordion" href="#collapse-'+(i+1)+'" aria-expanded="false" class="collapsed btn btn-default btn-sm pull-right align-self-end" style="text-align:bottom;border:2px solid black;" id="link_down_'+i+'" onclick="changeArrow(this.id)">';
				                      	data_ngs += '<i class="fa fa-caret-down"></i>';
				                data_ngs += '</a>';
				                data_ngs += '<a data-toggle="collapse" data-parent="#accordion" href="#collapse-'+(i+1)+'" aria-expanded="true" class="collapsed btn btn-default btn-sm pull-right align-self-end" style="text-align:bottom;border:2px solid black;display:none" id="link_up_'+i+'" onclick="changeArrow(this.id)">';
				                      	data_ngs += '<i class="fa fa-caret-up"></i>';
				                data_ngs += '</a>';
			                  data_ngs += '</div>';
			                  data_ngs += '<div id="collapse-'+(i+1)+'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">';
			                    data_ngs += '<div class="box-body">';
			                      data_ngs += '<table id="" class="table table-bordered table-striped data-log-detail" style="width: 100%;">';
			                      data_ngs += '<tbody style="border:1px solid black">';
			                      	data_ngs += '<tr>';
			                      		data_ngs += '<th colspan="2">Periode</th>';
			                      		data_ngs += '<td colspan="2" style="text-align:right;padding:5px;">'+result.dateTitleFirst+' ~ '+result.dateTitleLast+'</td>';
			                      	data_ngs += '</tr>';
			                      	data_ngs += '<tr>';
			                      		data_ngs += '<th colspan="2">NG Ratio</th>';
			                      		data_ngs += '<td colspan="2" style="text-align:right;padding:5px;">'+ratio.toFixed(3)+' %</td>';
			                      	data_ngs += '</tr>';
									// for(var k = 0; k < result.parameters.length;k++){
										if (result.parameters[i].length > 0) {
											data_ngs += '<tr>';
					                      		data_ngs += '<th colspan="4" style="text-align:center">Parameter</th>';
					                      	data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">NH (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].nh+'</td>';
												data_ngs += '<th class="tabledesign">VI3 (% / mm / sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].h1+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">H1 (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].h2+'</td>';
												data_ngs += '<th class="tabledesign">VI2 (% / mm / sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].h3+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">H2 (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].dryer+'</td>';
												data_ngs += '<th class="tabledesign">VI1 (% / mm / sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].mtc_temp+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">H3 (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].mtc_press+'</td>';
												data_ngs += '<th class="tabledesign">LS4 (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].chiller_temp+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">Dryer (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].chiller_press+'</td>';
												data_ngs += '<th class="tabledesign">LS4D (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].clamp+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">MTC (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ph4+'</td>';
												data_ngs += '<th class="tabledesign">LS4C (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ph3+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">MTC (MPa / Bar)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ph2+'</td>';
												data_ngs += '<th class="tabledesign">LS4B (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ph1+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">Chiller (°C)</th>';
												data_ngs += '<td>'+result.parameters[i][0].trh3+'</td>';
												data_ngs += '<th class="tabledesign">LS4A (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].trh2+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">Chiller (MPa / Bar)</th>';
												data_ngs += '<td>'+result.parameters[i][0].trh1+'</td>';
												data_ngs += '<th class="tabledesign">LS5 (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vh+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">Clamp (kN)</th>';
												data_ngs += '<td>'+result.parameters[i][0].pi+'</td>';
												data_ngs += '<th class="tabledesign">VE1 (mm/sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls10+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">PH4 (% / MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vi5+'</td>';
												data_ngs += '<th class="tabledesign">VE2 (mm/sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vi4+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">PH3 (% / MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vi3+'</td>';
												data_ngs += '<th class="tabledesign">VR (mm/sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vi2+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">PH2 (% / MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vi1+'</td>';
												data_ngs += '<th class="tabledesign">LS31A (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls4+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">PH1 (% / MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls4d+'</td>';
												data_ngs += '<th class="tabledesign">LS31 (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls4c+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">TRH3 (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls4b+'</td>';
												data_ngs += '<th class="tabledesign">SRN (% / min-1)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls4a+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">TRH2 (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls5+'</td>';
												data_ngs += '<th class="tabledesign">RPM (% / min-1)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ve1+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">TRH1 (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ve2+'</td>';
												data_ngs += '<th class="tabledesign">BP (MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].vr+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">VH (mm/sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls31a+'</td>';
												data_ngs += '<th class="tabledesign">TR1 INJ (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].ls31+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">PI (MPa)</th>';
												data_ngs += '<td>'+result.parameters[i][0].srn+'</td>';
												data_ngs += '<th class="tabledesign">TR3 COOL (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].rpm+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">LS10 BB (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].bp+'</td>';
												data_ngs += '<th class="tabledesign">TR4 INT (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].tr1inj+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">VI5 (% / mm / sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].tr3cool+'</td>';
												data_ngs += '<th class="tabledesign">Min. Cush (mm)</th>';
												data_ngs += '<td>'+result.parameters[i][0].tr4int+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th class="tabledesign">VI4 (% / mm / sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].mincush+'</td>';
												data_ngs += '<th class="tabledesign">FILL (Sec)</th>';
												data_ngs += '<td>'+result.parameters[i][0].fill+'</td>';
											data_ngs += '</tr>';
											data_ngs += '<tr>';
												data_ngs += '<th colspan="2" class="tabledesign">Circle Time (Sec)</th>';
												data_ngs += '<td colspan="2" class="tabledesign">'+result.parameters[i][0].circletime+'</td>';
											data_ngs += '</tr>';
										}else{
											data_ngs += '<tr>';
					                      		data_ngs += '<th colspan="4" style="text-align:center">Parameter Not Found</th>';
					                      	data_ngs += '</tr>';
										}
									// }
			                      data_ngs += '</tbody>';
			                      data_ngs += '</table>';
			                    data_ngs += '</div>';
			                  data_ngs += '</div>';
			                data_ngs += '</div>';
			            data_ngs += '</div>';
					data_ngs += '</div>';
				}
				$('#data_ng').append(data_ngs);

				var mesin = [];
				var part = [];

				for(var j = 0; j < result.data_ng.length;j++){
					mesin.push(parseInt(result.data_ng[j].mesin.split(' ')[1]));
					part.push(result.data_ng[j].part);
				}

				var mesin_unik = mesin.filter(onlyUnique);
				var part_unik = part.filter(onlyUnique);

				mesin_unik.sort(function(a, b){return a - b});

				var category = [];

				var series = [];

				for(var i = 0; i < part_unik.length;i++){
					this["part"+i] = [];
					for(var j = 0; j < mesin_unik.length;j++){
						for(var k = 0; k < result.data_ng.length;k++){
							if (result.data_ng[k].mesin.split(' ')[1] == mesin_unik[j] && result.data_ng[k].part == part_unik[i]) {
								var ratio = 0;
			                    var qty_check = 0;
			                    for(var l = 0; l < result.prod_result.length;l++){
			                    	if (result.prod_result[l][0].check_date == result.data_ng[k].check_date && result.prod_result[l][0].part == result.data_ng[i].part) {
			                    		if (result.prod_result[l][0].qty != 0) {
			                    			ratio = parseInt(result.data_ng[k].data_ng.split('_')[0]) / parseInt(result.prod_result[l][0].qty);
			                    		}
			                    		qty_check = result.prod_result[l][0].qty;
			                    	}
			                    }
								this["part"+i].push([j,parseFloat(ratio.toFixed(3))]);
							}
						}
						category.push(parseInt(mesin_unik[j]));
					}
					series.push({
						name: part_unik[i],
						data: this["part"+i],
						marker: {
							symbol: 'circle'
						}
					});
				}

				chart = Highcharts.chart('container', {
					chart: {
						type: 'scatter',
						zoomType: 'xy',
						backgroundColor:'none',
					},
					title: {
						text: 'NG Ratio Injection',
						style:{
							fontWeight:'bold'
						}
					},
					subtitle: {
						text: '成形不良率',
						style:{
							fontSize:'16px',
						}
					},
					credits: {
						enabled: false
					},
					xAxis: {
						categories:category,
						title: {
							enabled: true,
							text: 'Machine'
						},
						startOnTick: true,
						endOnTick: true,
						showLastLabel: true,
						type: 'category',
					},
					yAxis: {
						title: {
							text: 'NG Ratio (%)'
						},
						max: 0.015,
						plotLines: [{
							color: 'yellow', 
							width: 2,
							value: 0.01,
							zIndex: 10,
							dashStyle: 'dash',
							label: {
								text: '<em>NG Ratio Target 目標不良率 (0.01%)</em>',
								align: 'right',
								style: {
									fontSize: '1.1vw',
									fontWeight: 'bold',
									color: 'yellow'
								}
							}
						}],
						// opposite: true
					},
					plotOptions: {
						scatter: {
							marker: {
								radius: 10
							},
							tooltip: {
								headerFormat: '<b>{series.name}</b><br>',
								// pointFormat: 'Mesin: {point.x}<br>NG Ratio: {point.y}%'
								pointFormat: 'NG Ratio: {point.y}%'
							},
							events: {
								legendItemClick: function() {
									chart.yAxis[0].addPlotLine({
										value: 7.5,
										color: 'green',
										dashStyle: 'shortdash',
										width: 2,
										label: {
											text: 'My PlotLine'
										}
									});
									var seriesIndex = this.index;
									var series = this.chart.series;
									if (this.visible && this.chart.restIsHidden) {
										for (var i = 0; i < series.length; i++) {
											if (series[i].index != seriesIndex) {
												series[i].show();
											}
										}
										this.chart.restIsHidden = false;
									} else {
										for (var i = 0; i < series.length; i++) {
											if (series[i].index != seriesIndex) {
												series[i].hide();
											}
										}
										this.show()
										this.chart.restIsHidden = true;
									}
									return false;
								}
							},
						}
					},
					series: series
				});
				$('#periode').html(result.dateTitleFirst+ ' ~ ' +result.dateTitleLast);
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function changeArrow(id) {
		if (id.match(/down/gi)) {
			$('#'+id).hide();
			$('#link_up_'+id.split('_')[2]).show();
		}else{
			$('#'+id).hide();
			$('#link_down_'+id.split('_')[2]).show();
		}
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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

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