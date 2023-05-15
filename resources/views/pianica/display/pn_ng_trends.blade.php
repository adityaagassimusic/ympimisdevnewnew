@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="row" style="margin:0px;">
					<!-- <form method="GET" action="{{ action('Pianica@indexNgRate') }}"> -->
						<div class="col-xs-2" style="padding-right: 5px;">
							<div class="input-group date">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" name="date_from" id="date_from" placeholder="Select Date From">
							</div>
						</div>
						<div class="col-xs-2" style="padding-right: 5px;padding-left: 0px;">
							<div class="input-group date">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" name="date_to" id="date_to" placeholder="Select Date To">
							</div>
						</div>
						<div class="col-xs-2" style="padding-right: 5px;padding-left: 0px;">
							<select class="form-control select2" style="width: 100%;" data-placeholder="Pilih Lokasi" id="location">
								<option value=""></option>
								<option value="Bentsuki - Benage">Bentsuki - Benage</option>
								<option value="Tuning">Tuning</option>
							</select>
						</div>
						<div class="col-xs-1" style="padding-right: 5px;padding-left: 0px;">
							<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
						</div>
					<!-- </form> -->
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 10px;margin-top: 20px;">
			<div id="container"></div>
		</div>
		<!-- <div class="col-xs-4" style="margin-bottom: 10px;padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<center style="background-color: lightskyblue;">
						<span style="font-weight: bold;font-size: 20px;">Biri</span>
					</center>
					<table class="table table-bordered table-responsive" id="biri" style="margin-top: 10px;">
						<thead >
							<tr style="background-color: #cddc39;">
								<th style="width: 1%">#</th>
								<th style="width: 3%">Operator</th>
								<th style="width: 1%">Qty NG</th>
								<th style="width: 3%">Reed</th>
							</tr>
						</thead>
						<tbody id="tableBodyBiri">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-4" style="margin-bottom: 10px;padding-right: 5px;padding-left: 0px;">
			<div class="box box-solid">
				<div class="box-body">
					<center style="background-color: lightskyblue;">
						<span style="font-weight: bold;font-size: 20px;">Terlalu Tinggi</span>
					</center>
					<table class="table table-bordered table-responsive" id="t_tinggi" style="margin-top: 10px;">
						<thead >
							<tr style="background-color: #cddc39;">
								<th style="width: 1%">#</th>
								<th style="width: 3%">Operator</th>
								<th style="width: 1%">Qty NG</th>
								<th style="width: 3%">Reed</th>
							</tr>
						</thead>
						<tbody id="tableBodyTTinggi">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-4" style="margin-bottom: 10px;padding-left: 0px;">
			<div class="box box-solid">
				<div class="box-body">
					<center style="background-color: lightskyblue;">
						<span style="font-weight: bold;font-size: 20px;">Terlalu Rendah</span>
					</center>
					<table class="table table-bordered table-responsive" id="t_rendah" style="margin-top: 10px;">
						<thead >
							<tr style="background-color: #cddc39;">
								<th style="width: 1%">#</th>
								<th style="width: 3%">Operator</th>
								<th style="width: 1%">Qty NG</th>
								<th style="width: 3%">Reed</th>
							</tr>
						</thead>
						<tbody id="tableBodyTRendah">
							
						</tbody>
					</table>
				</div>
			</div>
		</div> -->
	</div>
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-bordered table-responsive" style="margin-bottom:0px;">
				<thead >
					<tr style="background-color: #cddc39;">
						<th style="width: 1%;font-size: 18px;cursor: pointer;" onclick="hideLine('biri')">Trend NG <span id="loc_biri"></span> (Biri) <span id="line_biri"></span></th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_biri_line">
			<table>
				<tr id="containers_biri_line">
					
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_biri">
			<table>
				<tr id="containers_biri">
					
				</tr>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<table class="table table-bordered table-responsive" style="margin-bottom:0px;">
				<thead >
					<tr style="background-color: #cddc39;">
						<th style="width: 1%;font-size: 18px;cursor: pointer;" onclick="hideLine('tinggi')">Trend NG <span id="loc_tinggi"></span> (Terlalu Tinggi) <span id="line_tinggi"></span></th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_tinggi_line">
			<table>
				<tr id="containers_tinggi_line">
					
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_tinggi">
			<table>
				<tr id="containers_tinggi">
					
				</tr>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<table class="table table-bordered table-responsive" style="margin-bottom:0px;">
				<thead >
					<tr style="background-color: #cddc39;">
						<th style="width: 1%;font-size: 18px;cursor: pointer;" onclick="hideLine('rendah')">Trend NG <span id="loc_rendah"></span> (Terlalu Rendah) <span id="line_rendah"></span></th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_rendah_line">
			<table>
				<tr id="containers_rendah_line">
					
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-bottom: 10px;overflow-x: scroll;" id="div_rendah">
			<table>
				<tr id="containers_rendah">
					
				</tr>
			</table>
		</div>
	</div>

</section>
@endsection
@section('scripts')

<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<!-- <script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
<!-- <script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#div_biri').hide();
		$('#div_biri_line').show();
		$('#div_tinggi').hide();
		$('#div_tinggi_line').show();
		$('#div_rendah').hide();
		$('#div_rendah_line').show();
		$('#date').datepicker({
			autoclose: true
		});
		$('.select2').select2({
			allowClear:true
		});

		fillChart();
		setInterval(fillChart, 600000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function dynamicSort(property) {
	    var sortOrder = 1;
	    if(property[0] === "-") {
	        sortOrder = -1;
	        property = property.substr(1);
	    }
	    return function (a,b) {
	        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
	        return result * sortOrder;
	    }
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

	var data_reed = null;
	var data_op = null;
	var data_op_all = null;

	function fillChart(){
		$('#loading').show();
		var data = {
			date_from:$("#date_from").val(),
			date_to:$("#date_to").val(),
			location:$("#location").val(),
		}

		$.get('{{ url("fetch/pn/ng_trend") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Bentsuki

					var all_reeds = [];

					var ng_name = [];
					var op = [];

					for (var i = 0; i < result.ng.length; i++) {
						ng_name.push(result.ng[i].ng_name);
						op.push(result.ng[i].operator);
					}

					var ng_name_unik = ng_name.filter(onlyUnique);
					var op_unik = op.filter(onlyUnique);

					var ng_all = [];
					for(var i = 0; i < ng_name_unik.length;i++){
						var ngs = [];
						for(var j = 0; j < result.ng.length;j++){
							if (result.ng[j].ng_name == ng_name_unik[i]) {
								ngs.push({op:result.ng[j].operator,qty:parseInt(result.ng[j].jml),ng_name:ng_name_unik[i]});
							}
						}
						ng_all.push(ngs);
					}

					var ng_all_sort = [];

					var index = 0;
					for(var i = 0; i < ng_all.length;i++){
						var ngs = sortArray(ng_all[i], "qty", -1);
						ng_all_sort.push(ngs);
					}

					var ng_all_high = [];
					for(var i = 0; i < ng_all_sort.length;i++){
						var ngs = [];
						for(var j = 0; j < 3;j++){
							ngs.push(ng_all_sort[i][j]);
						}
						ng_all_high.push(ngs);
					}

					var ng_all_reed = [];

					for(var i = 0; i < ng_all_high.length;i++){
						var ngs = [];
						for(var j = 0; j < ng_all_high[i].length;j++){
							var reeds = [];
							for(var k = 0; k < result.reed.length;k++){
								if (result.reed[k].tuning == ng_all_high[i][j].op && result.reed[k].ng_name == ng_all_high[i][j].ng_name) {
									reeds.push(result.reed[k].reed);
								}
							}
							ngs.push({op:ng_all_high[i][j].op,qty:parseInt(ng_all_high[i][j].qty),ng_name:ng_all_high[i][j].ng_name,reed:reeds.join(',')});
						}
						ng_all_reed.push(ngs);
					}
					var ng_all_fix = [];

					for(var i = 0; i < ng_all_reed.length;i++){
						var ngs = [];
						for(var j = 0; j < ng_all_reed[i].length;j++){
							var reeds = ng_all_reed[i][j].reed.split(',');
							var  count = {};
							reeds.forEach(function(u) { count[u] = (count[u]||0) + 1;});
							var resultReed = Object.keys(count).map(function(key) {
								return [Number(key), count[key]];
							});
							resultReed.sort(function(a, b){return b[1] - a[1]});
							ngs.push({op:ng_all_reed[i][j].op,qty:parseInt(ng_all_reed[i][j].qty),ng_name:ng_all_reed[i][j].ng_name,reed:resultReed});
						}
						ng_all_fix.push(ngs);
					}

					$('#tableBodyBiri').html('');
					$('#tableBodyTRendah').html('');
					$('#tableBodyTTinggi').html('');

					var bodyBiri = '';
					var bodyTinggi = '';
					var bodyRendah = '';

					var all_reeds = [];

					for(var i = 0; i < ng_all_fix.length;i++){
						if (ng_all_fix[i][0].ng_name == 'Biri') {
							var index = 1;
							for(var j = 0; j < ng_all_fix[i].length;j++){
								bodyBiri += '<tr>';
								bodyBiri += '<td style="vertical-align:middle;background-color: #f0f0ff;text-align:center; !important;border:1px solid black;">'+index+'</td>';
								var op_name = '';
								for(var k = 0; k < result.op.length;k++){
									if (result.op[k].operator == ng_all_fix[i][j].op) {
										op_name = result.op[k].nama;
									}
								}
								bodyBiri += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">'+ng_all_fix[i][j].op+' - '+op_name.split(' ').slice(0,2).join(' ')+'</td>';
								bodyBiri += '<td style="vertical-align:middle;padding-right:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:right;">'+ng_all_fix[i][j].qty+'</td>';
								bodyBiri += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">';
								var reeds = ng_all_fix[i][j].reed;
								for(var u = 0; u < 5;u++){
									bodyBiri += reeds[u][0]+' = '+reeds[u][1]+'<br>';
									all_reeds.push(reeds[u][0]);
								}
								bodyBiri += '</td>';
								bodyBiri += '</tr>';
								index++;
							}
						}

						if (ng_all_fix[i][0].ng_name == 'Rendah') {
							var index = 1;
							for(var j = 0; j < ng_all_fix[i].length;j++){
								bodyRendah += '<tr>';
								bodyRendah += '<td style="vertical-align:middle;background-color: #f0f0ff;text-align:center; !important;border:1px solid black;">'+index+'</td>';
								var op_name = '';
								for(var k = 0; k < result.op.length;k++){
									if (result.op[k].operator == ng_all_fix[i][j].op) {
										op_name = result.op[k].nama;
									}
								}
								bodyRendah += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">'+ng_all_fix[i][j].op+' - '+op_name.split(' ').slice(0,2).join(' ')+'</td>';
								bodyRendah += '<td style="vertical-align:middle;padding-right:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:right;">'+ng_all_fix[i][j].qty+'</td>';
								bodyRendah += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">';
								var reeds = ng_all_fix[i][j].reed;
								for(var u = 0; u < 5;u++){
									bodyRendah += reeds[u][0]+' = '+reeds[u][1]+'<br>';
									all_reeds.push(reeds[u][0]);
								}
								bodyRendah += '</td>';
								bodyRendah += '</tr>';
								index++;
							}
						}

						if (ng_all_fix[i][0].ng_name == 'Tinggi') {
							var index = 1;
							for(var j = 0; j < ng_all_fix[i].length;j++){
								bodyTinggi += '<tr>';
								bodyTinggi += '<td style="vertical-align:middle;background-color: #f0f0ff;text-align:center; !important;border:1px solid black;">'+index+'</td>';
								var op_name = '';
								for(var k = 0; k < result.op.length;k++){
									if (result.op[k].operator == ng_all_fix[i][j].op) {
										op_name = result.op[k].nama;
									}
								}
								bodyTinggi += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">'+ng_all_fix[i][j].op+' - '+op_name.split(' ').slice(0,2).join(' ')+'</td>';
								bodyTinggi += '<td style="vertical-align:middle;padding-right:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:right;">'+ng_all_fix[i][j].qty+'</td>';
								bodyTinggi += '<td style="vertical-align:middle;padding-left:5px !important;background-color: #f0f0ff;border:1px solid black;text-align:left;">';
								var reeds = ng_all_fix[i][j].reed;
								for(var u = 0; u < 5;u++){
									bodyTinggi += reeds[u][0]+' = '+reeds[u][1]+'<br>';
									all_reeds.push(reeds[u][0]);
								}
								bodyTinggi += '</td>';
								bodyTinggi += '</tr>';
								index++;
							}
						}
					}

					// $('#tableBodyBiri').append(bodyBiri);
					// $('#tableBodyTRendah').append(bodyRendah);
					// $('#tableBodyTTinggi').append(bodyTinggi);

					var op = [];
					var sum = [];
					var data = [];

					for(var i = 0; i < op_unik.length;i++){
						var op_name = '';
						for(var k = 0; k < result.op.length;k++){
							if (result.op[k].operator == op_unik[i]) {
								op_name = result.op[k].nama;
							}
						}
						op.push(op_name.split(' ').slice(0,2).join(' '));
						var biris = 0;
						var t_tinggis = 0;
						var t_rendahs = 0;
						var oktafs = 0;
						for(var j = 0; j < result.ng.length;j++){
							if (result.ng[j].operator == op_unik[i] && result.ng[j].ng_name == 'Biri') {
								biris = parseInt(result.ng[j].jml);
							}
							if (result.ng[j].operator == op_unik[i] && result.ng[j].ng_name == 'Tinggi') {
								t_tinggis = parseInt(result.ng[j].jml);
							}
							if (result.ng[j].operator == op_unik[i] && result.ng[j].ng_name == 'Rendah') {
								t_rendahs = parseInt(result.ng[j].jml);
							}
							if (result.ng[j].operator == op_unik[i] && result.ng[j].ng_name == 'Oktaf') {
								oktafs = parseInt(result.ng[j].jml);
							}
						}

						sum.push(biris  + t_rendahs + t_tinggis + oktafs);

						data.push({nama: op_name, biri: biris, oktaf: oktafs, t_rendah: t_rendahs, t_tinggi: t_tinggis, sum: sum[i]});
					}

					data.sort((a, b) => b.sum - a.sum);

					var op = [];
					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];
					for (var i = 0; i < data.length; i++) {
						op.push(data[i].nama);
						biri.push(data[i].biri);
						oktaf.push(data[i].oktaf);
						t_rendah.push(data[i].t_rendah);
						t_tinggi.push(data[i].t_tinggi);
					}

					Highcharts.chart('container', {
						chart: {
							height:'425',
							type: 'column'
						},
						title: {
							text: 'Trend NG '+result.location,
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
							style: {
								fontSize: '16px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: op,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total NG'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Biri',
							data: biri,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 101 , result.date_from,result.date_to,"Bentsuki");
									}
								}
							}
						},
						// {
						// 	name: 'Oktaf',
						// 	data: oktaf,
						// 	color: '#90ee7e',
						// 	point: {
						// 		events: {
						// 			click: function () {
						// 				fillModalTuning(this.category , 101 , result.date_from,result.date_to,"Bentsuki");
						// 			}
						// 		}
						// 	}
						// },
						{
							name: 'Tinggi',
							data: t_tinggi,
							color: '#f45b5b',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 103 , result.date_from,result.date_to,"Bentsuki");
									}
								}
							}
						},
						{
							name: 'Rendah',
							data: t_rendah,
							color: '#7798BF',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 104 , result.date_from,result.date_to,"Bentsuki");
									}
								}
							}
						}
						]
					});

					// var category_biri = [];
					// var series_biri = [];
					// var name_biri = [];

					// all_reeds.sort(function(a, b){return b - a});

					// var all_reeds_unik = all_reeds.filter(onlyUnique);

					// var op_biri = [];
					// var op_tinggi = [];
					// var op_rendah = [];

					// for(var i = 0; i < ng_all_fix.length;i++){
					// 	if (ng_all_fix[i][0].ng_name == 'Biri') {
					// 		var index = 1;
					// 		for(var j = 0; j < ng_all_fix[i].length;j++){
					// 			var op_name = '';
					// 			for(var k = 0; k < result.op.length;k++){
					// 				if (result.op[k].operator == ng_all_fix[i][j].op) {
					// 					op_name = result.op[k].nama;
					// 				}
					// 			}
					// 			op_biri.push({op:ng_all_fix[i][j].op,name:op_name,reed:ng_all_fix[i][j].reed});
					// 		}
					// 	}

					// 	if (ng_all_fix[i][0].ng_name == 'Tinggi') {
					// 		var index = 1;
					// 		for(var j = 0; j < ng_all_fix[i].length;j++){
					// 			var op_name = '';
					// 			for(var k = 0; k < result.op.length;k++){
					// 				if (result.op[k].operator == ng_all_fix[i][j].op) {
					// 					op_name = result.op[k].nama;
					// 				}
					// 			}
					// 			op_tinggi.push({op:ng_all_fix[i][j].op,name:op_name,reed:ng_all_fix[i][j].reed});
					// 		}
					// 	}

					// 	if (ng_all_fix[i][0].ng_name == 'Rendah') {
					// 		var index = 1;
					// 		for(var j = 0; j < ng_all_fix[i].length;j++){
					// 			var op_name = '';
					// 			for(var k = 0; k < result.op.length;k++){
					// 				if (result.op[k].operator == ng_all_fix[i][j].op) {
					// 					op_name = result.op[k].nama;
					// 				}
					// 			}
					// 			op_rendah.push({op:ng_all_fix[i][j].op,name:op_name,reed:ng_all_fix[i][j].reed});
					// 		}
					// 	}						
					// }

					// $("#containers_biri").html('');
					// var cons = '';

					// var index = 1;
					// var indexes = [];
					// for(var i = 0; i < op_biri.length;i++){
					// 	cons += '<div class="col-xs-4" style="padding:0" id="container_'+index+'"></div>';
					// 	indexes.push(index);
					// 	index++;
					// }

					// $("#containers_biri").append(cons);

					// for(var i = 0; i < op_biri.length;i++){
					// 	var category = [];
					// 	var series = [];
					// 	var reeds = op_biri[i].reed;
					// 	for(var j = 0; j < all_reeds_unik.length;j++){
					// 		var qty = 0;
					// 		for(var u = 0; u < 5;u++){
					// 			if (reeds[u][0] == all_reeds_unik[j]) {
					// 				qty = reeds[u][1];
					// 			}
					// 		}
					// 		series.push({low:-(qty/2),high:(qty/2),key:qty});
					// 		category.push(all_reeds_unik[j]);
					// 	}

					// 	Highcharts.chart('container_'+indexes[i], {

					// 	    chart: {
					// 	        type: 'columnrange',
					// 	        inverted: true
					// 	    },

					// 	    title: {
					// 	        text: op_biri[i].op+' - '+op_biri[i].name,
					// 	        style:{
					// 	        	fontWeight:'bold',
					// 	        	fontSize:'15px'
					// 	        }
					// 	    },

					// 	    xAxis: {
					// 	        categories: category,
					// 	        gridLineWidth: 0.7,
					// 			gridLineColor: 'RGB(204,255,255)',
					// 			title:{
					// 				text:"Reed",
					// 				style:{
					// 					fontWeight:'bold'
					// 				}
					// 			},
					// 	    },

					// 	    yAxis: {
					// 	        title: {
					// 	            text: 'Qty NG',
					// 	            style:{
					// 	            	color:'white'
					// 	            }
					// 	        },
					// 	        labels:{
					// 	        	enabled:false
					// 	        },
					// 	        gridLineWidth: 0.2,
					// 	        gridLineColor: '#b8b8b8'
					// 	    },
					// 	    credits:{
					// 	    	enabled:false
					// 	    },
					// 	    plotOptions: {
					// 	        columnrange: {
					// 	            dataLabels: {
					// 	                enabled: true,
					// 				      formatter: function(e) {
					// 				        if (this.point.key != 0) {
					// 				        	if (e.align == "right") {
					// 					            return this.point.key
					// 					          }
					// 				        }
					// 				      },
					// 				      style:{
					// 				      	fontSize:'14px'
					// 				      }
					// 	            },
					// 	            pointWidth: 15
					// 	        }
					// 	    },
					// 	    tooltip:{
					// 	    	enabled:true,
					// 	    	headerFormat: '<b>Reed : {point.x}</b>',
     //      						pointFormat: '<br/>Qty NG : {point.key}'
					// 	    },

					// 	    legend: {
					// 	        enabled: false
					// 	    },

					// 	    series: [{
					// 	        name: 'Qty NG',
					// 	        data: series,
					// 	        colorByPoint:false,
					// 	        color:'#90EE90'
					// 	    }]

					// 	});
					// }

					// $("#containers_tinggi").html('');
					// var cons = '';

					// indexes = [];
					// for(var i = 0; i < op_tinggi.length;i++){
					// 	cons += '<div class="col-xs-4" style="padding:0" id="container_'+index+'"></div>';
					// 	indexes.push(index);
					// 	index++;
					// }

					// $("#containers_tinggi").append(cons);

					// for(var i = 0; i < op_tinggi.length;i++){
					// 	var category = [];
					// 	var series = [];
					// 	var reeds = op_tinggi[i].reed;
					// 	for(var j = 0; j < all_reeds_unik.length;j++){
					// 		var qty = 0;
					// 		for(var u = 0; u < 5;u++){
					// 			if (reeds[u][0] == all_reeds_unik[j]) {
					// 				qty = reeds[u][1];
					// 			}
					// 		}
					// 		series.push({low:-(qty/2),high:(qty/2),key:qty});
					// 		category.push(all_reeds_unik[j]);
					// 	}

					// 	Highcharts.chart('container_'+indexes[i], {

					// 	    chart: {
					// 	        type: 'columnrange',
					// 	        inverted: true
					// 	    },

					// 	    title: {
					// 	        text: op_tinggi[i].op+' - '+op_tinggi[i].name,
					// 	        style:{
					// 	        	fontWeight:'bold',
					// 	        	fontSize:'15px'
					// 	        }
					// 	    },

					// 	    xAxis: {
					// 	        categories: category,
					// 	        gridLineWidth: 0.7,
					// 			gridLineColor: 'RGB(204,255,255)',
					// 			title:{
					// 				text:"Reed",
					// 				style:{
					// 					fontWeight:'bold'
					// 				}
					// 			},
					// 	    },

					// 	    yAxis: {
					// 	        title: {
					// 	            text: 'Qty NG',
					// 	            style:{
					// 	            	color:'white'
					// 	            }
					// 	        },
					// 	        labels:{
					// 	        	enabled:false
					// 	        },
					// 	        gridLineWidth: 0.2,
					// 	        gridLineColor: '#b8b8b8'
					// 	    },
					// 	    credits:{
					// 	    	enabled:false
					// 	    },
					// 	    plotOptions: {
					// 	        columnrange: {
					// 	            dataLabels: {
					// 	                enabled: true,
					// 				      formatter: function(e) {
					// 				        if (this.point.key != 0) {
					// 				        	if (e.align == "right") {
					// 					            return this.point.key
					// 					          }
					// 				        }
					// 				      },
					// 				      style:{
					// 				      	fontSize:'14px'
					// 				      }
					// 	            },
					// 	            pointWidth: 15
					// 	        }
					// 	    },
					// 	    tooltip:{
					// 	    	enabled:true,
					// 	    	headerFormat: '<b>Reed : {point.x}</b>',
     //      						pointFormat: '<br/>Qty NG : {point.key}'
					// 	    },

					// 	    legend: {
					// 	        enabled: false
					// 	    },

					// 	    series: [{
					// 	        name: 'Qty NG',
					// 	        data: series,
					// 	        colorByPoint:false,
					// 	        color:'#90EE90'
					// 	    }]

					// 	});
					// }

					// $("#containers_rendah").html('');
					// var cons = '';

					// indexes = [];
					// for(var i = 0; i < op_rendah.length;i++){
					// 	cons += '<div class="col-xs-4" style="padding:0" id="container_'+index+'"></div>';
					// 	indexes.push(index);
					// 	index++;
					// }

					// $("#containers_rendah").append(cons);

					// for(var i = 0; i < op_rendah.length;i++){
					// 	var category = [];
					// 	var series = [];
					// 	var reeds = op_rendah[i].reed;
					// 	for(var j = 0; j < all_reeds_unik.length;j++){
					// 		var qty = 0;
					// 		for(var u = 0; u < 5;u++){
					// 			if (reeds[u][0] == all_reeds_unik[j]) {
					// 				qty = reeds[u][1];
					// 			}
					// 		}
					// 		series.push({low:-(qty/2),high:(qty/2),key:qty});
					// 		category.push(all_reeds_unik[j]);
					// 	}

					// 	Highcharts.chart('container_'+indexes[i], {

					// 	    chart: {
					// 	        type: 'columnrange',
					// 	        inverted: true
					// 	    },

					// 	    title: {
					// 	        text: op_rendah[i].op+' - '+op_rendah[i].name,
					// 	        style:{
					// 	        	fontWeight:'bold',
					// 	        	fontSize:'15px'
					// 	        }
					// 	    },

					// 	    xAxis: {
					// 	        categories: category,
					// 	        gridLineWidth: 0.7,
					// 			gridLineColor: 'RGB(204,255,255)',
					// 			title:{
					// 				text:"Reed",
					// 				style:{
					// 					fontWeight:'bold'
					// 				}
					// 			},
					// 	    },

					// 	    yAxis: {
					// 	        title: {
					// 	            text: 'Qty NG',
					// 	            style:{
					// 	            	color:'white'
					// 	            }
					// 	        },
					// 	        labels:{
					// 	        	enabled:false
					// 	        },
					// 	        gridLineWidth: 0.2,
					// 	        gridLineColor: '#b8b8b8'
					// 	    },
					// 	    credits:{
					// 	    	enabled:false
					// 	    },
					// 	    plotOptions: {
					// 	        columnrange: {
					// 	            dataLabels: {
					// 	                enabled: true,
					// 				      formatter: function(e) {
					// 				        if (this.point.key != 0) {
					// 				        	if (e.align == "right") {
					// 					            return this.point.key
					// 					          }
					// 				        }
					// 				      },
					// 				      style:{
					// 				      	fontSize:'14px'
					// 				      }
					// 	            },
					// 	            pointWidth: 15
					// 	        }
					// 	    },
					// 	    tooltip:{
					// 	    	enabled:true,
					// 	    	headerFormat: '<b>Reed : {point.x}</b>',
     //      						pointFormat: '<br/>Qty NG : {point.key}'
					// 	    },

					// 	    legend: {
					// 	        enabled: false
					// 	    },

					// 	    series: [{
					// 	        name: 'Qty NG',
					// 	        data: series,
					// 	        colorByPoint:false,
					// 	        color:'#90EE90'
					// 	    }]

					// 	});
					// }

					$('#loc_biri').html(result.location);
					$('#loc_tinggi').html(result.location);
					$('#loc_rendah').html(result.location);

					//LINE BIRI

					var line_today = [];

					for(var i = 0; i < result.op_all.length;i++){
						line_today.push(parseInt(result.op_all[i].line.slice(-1)));
					}

					var line_today_unik1 = line_today.filter(onlyUnique);

					var line_today_unik = line_today_unik1.sort(function(a, b){return a - b});

					var line_biri = [];

					var reeds_biri = [];

					for(var i = 0; i < result.reed.length;i++){
						if (result.reed[i].ng_name == 'Biri') {
							if (line_today_unik.includes(result.reed[i].line)) {
								line_biri.push(result.reed[i].line);
							}
							if (result.reed[i].reed != null) {
								reeds_biri.push(result.reed[i].reed);
							}
						}
					}

					var all_reeds_biri = [];

					for(var i = 0; i < reeds_biri.length;i++){
						if (reeds_biri[i].match(/,/gi)) {
							var reed = reeds_biri[i].split(',');
							for(var j = 0; j < reed.length;j++){
								all_reeds_biri.push(parseInt(reed[j]));
							}
						}else{
							all_reeds_biri.push(parseInt(reeds_biri[i]));
						}
					}

					var line_unik_biri1 = line_biri.filter(onlyUnique);

					var line_unik_biri = line_unik_biri1.sort(function(a, b){return a - b});

					var all_reeds_biri_unik1 = all_reeds_biri.filter(onlyUnique);

					var all_reeds_biri_unik = all_reeds_biri_unik1.sort(function(a, b){return b - a});

					var all_data = [];

					for(var i = 0; i < line_unik_biri.length;i++){
						for(var j = 0; j < all_reeds_biri_unik.length;j++){
							var qty = 0;
							for(var k = 0; k < result.reed.length;k++){
								if (result.reed[k].reed != null && result.reed[k].line == line_unik_biri[i] && result.reed[k].ng_name == 'Biri') {
									if (result.reed[k].reed.match(/,/gi)) {
										var reed_pisah = result.reed[k].reed.split(',');
										for(var u = 0; u < reed_pisah.length;u++){
											if (reed_pisah[u] == all_reeds_biri_unik[j]) {
												qty++;
											}
										}
									}else{
										if (all_reeds_biri_unik[j] == result.reed[k].reed) {
											qty++;
										}
									}
								}
							}
							all_data.push({
								line:line_unik_biri[i],ng_name:'Biri',reed:all_reeds_biri_unik[j],qty:qty
							});
						}
					}

					$("#containers_biri_line").html('');
					var cons = '';

					var index = 1;
					var indexes = [];
					for(var i = 0; i < line_unik_biri.length;i++){
						cons += '<td style="padding:0;" id="container_line_biri_'+index+'"></td>';
						indexes.push(index);
						index++;
					}

					$("#containers_biri_line").append(cons);

					for(var i = 0; i < line_unik_biri.length;i++){
						var category = [];
						var series = [];
						for(var j = 0;j < all_data.length;j++){
							if (all_data[j].line == line_unik_biri[i]) {
								category.push(all_data[j].reed);
								series.push({low:-(all_data[j].qty/2),high:(all_data[j].qty/2),key:all_data[j].qty});
							}
						}
						
						if (i == 0) {
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"Reed",
									style:{
										fontWeight:'bold'
									}
								},
						    };
						}else{
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"",
								},
								labels:{
									enabled:false
								}
						    };
						}

						Highcharts.chart('container_line_biri_'+indexes[i], {

						    chart: {
						        type: 'columnrange',
						        inverted: true,
						        height:'500',
						        width:'300'
						    },

						    title: {
						    	useHTML: true,
         						text: '<div style="cursor:pointer" id="biri_line_'+line_unik_biri[i]+'">Line '+line_unik_biri[i]+'</div>',
						        style:{
						        	fontWeight:'bold',
						        	fontSize:'15px'
						        }
						    },

						    xAxis: xaxis,

						    yAxis: {
						        title: {
						            text: 'Qty NG',
						            style:{
						            	color:'white'
						            }
						        },
						        labels:{
						        	enabled:false
						        },
						        gridLineWidth: 0.2,
						        gridLineColor: '#b8b8b8'
						    },
						    credits:{
						    	enabled:false
						    },
						    plotOptions: {
						        columnrange: {
						            dataLabels: {
						                enabled: true,
									      formatter: function(e) {
									        if (this.point.key != 0) {
									        	if (e.align == "right") {
										            return 'Reed '+this.point.category+'='+this.point.key
										          }
									        }
									      },
									      style:{
									      	fontSize:'10px'
									      }
						            },
						            pointWidth: 8
						        }
						    },
						    tooltip:{
						    	enabled:true,
						    	headerFormat: '<b>Reed : {point.x}</b>',
          						pointFormat: '<br/>Qty NG : {point.key}'
						    },

						    legend: {
						        enabled: false
						    },

						    series: [{
						        name: 'Qty NG',
						        data: series,
						        colorByPoint:false,
						        color:'#90EE90'
						    }]

						});

						var line = line_unik_biri[i];

						document.getElementById("biri_line_"+line_unik_biri[i]).onclick = function(line){
							showLine(line,'Biri');
							showLine(line,'Tinggi');
							showLine(line,'Rendah');
						}
					}

					//LINE TINGGI

					var line_today = [];

					for(var i = 0; i < result.op_all.length;i++){
						line_today.push(parseInt(result.op_all[i].line.slice(-1)));
					}

					var line_today_unik1 = line_today.filter(onlyUnique);

					var line_today_unik = line_today_unik1.sort(function(a, b){return a - b});

					var line_tinggi = [];

					var reeds_tinggi = [];

					for(var i = 0; i < result.reed.length;i++){
						if (result.reed[i].ng_name == 'Tinggi') {
							if (line_today_unik.includes(result.reed[i].line)) {
								line_tinggi.push(result.reed[i].line);
							}
							if (result.reed[i].reed != null) {
								reeds_tinggi.push(result.reed[i].reed);
							}
						}
					}

					var all_reeds_tinggi = [];

					for(var i = 0; i < reeds_tinggi.length;i++){
						if (reeds_tinggi[i].match(/,/gi)) {
							var reed = reeds_tinggi[i].split(',');
							for(var j = 0; j < reed.length;j++){
								all_reeds_tinggi.push(parseInt(reed[j]));
							}
						}else{
							all_reeds_tinggi.push(parseInt(reeds_tinggi[i]));
						}
					}

					var line_unik_tinggi1 = line_tinggi.filter(onlyUnique);

					var line_unik_tinggi = line_unik_tinggi1.sort(function(a, b){return a - b});

					var all_reeds_tinggi_unik1 = all_reeds_tinggi.filter(onlyUnique);

					var all_reeds_tinggi_unik = all_reeds_tinggi_unik1.sort(function(a, b){return b - a});

					var all_data = [];

					for(var i = 0; i < line_unik_tinggi.length;i++){
						for(var j = 0; j < all_reeds_tinggi_unik.length;j++){
							var qty = 0;
							for(var k = 0; k < result.reed.length;k++){
								if (result.reed[k].reed != null && result.reed[k].line == line_unik_tinggi[i] && result.reed[k].ng_name == 'Tinggi') {
									if (result.reed[k].reed.match(/,/gi)) {
										var reed_pisah = result.reed[k].reed.split(',');
										for(var u = 0; u < reed_pisah.length;u++){
											if (reed_pisah[u] == all_reeds_tinggi_unik[j]) {
												qty++;
											}
										}
									}else{
										if (all_reeds_tinggi_unik[j] == result.reed[k].reed) {
											qty++;
										}
									}
								}
							}
							all_data.push({
								line:line_unik_tinggi[i],ng_name:'Tinggi',reed:all_reeds_tinggi_unik[j],qty:qty
							});
						}
					}

					$("#containers_tinggi_line").html('');
					var cons = '';

					var index = 1;
					var indexes = [];
					for(var i = 0; i < line_unik_tinggi.length;i++){
						cons += '<td style="padding:0;" id="container_line_tinggi_'+index+'"></td>';
						indexes.push(index);
						index++;
					}

					$("#containers_tinggi_line").append(cons);

					for(var i = 0; i < line_unik_tinggi.length;i++){
						var category = [];
						var series = [];
						for(var j = 0;j < all_data.length;j++){
							if (all_data[j].line == line_unik_tinggi[i]) {
								category.push(all_data[j].reed);
								series.push({low:-(all_data[j].qty/2),high:(all_data[j].qty/2),key:all_data[j].qty});
							}
						}
						
						if (i == 0) {
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"Reed",
									style:{
										fontWeight:'bold'
									}
								},
						    };
						}else{
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"",
								},
								labels:{
									enabled:false
								}
						    };
						}

						Highcharts.chart('container_line_tinggi_'+indexes[i], {

						    chart: {
						        type: 'columnrange',
						        inverted: true,
						        height:'500',
						        width:'300'
						    },

						    title: {
						    	useHTML: true,
         						text: '<div style="cursor:pointer" id="tinggi_line_'+line_unik_tinggi[i]+'">Line '+line_unik_tinggi[i]+'</div>',
						        style:{
						        	fontWeight:'bold',
						        	fontSize:'15px'
						        }
						    },

						    xAxis: xaxis,

						    yAxis: {
						        title: {
						            text: 'Qty NG',
						            style:{
						            	color:'white'
						            }
						        },
						        labels:{
						        	enabled:false
						        },
						        gridLineWidth: 0.2,
						        gridLineColor: '#b8b8b8'
						    },
						    credits:{
						    	enabled:false
						    },
						    plotOptions: {
						        columnrange: {
						            dataLabels: {
						                enabled: true,
									      formatter: function(e) {
									        if (this.point.key != 0) {
									        	if (e.align == "right") {
										            return 'Reed '+this.point.category+'='+this.point.key
										          }
									        }
									      },
									      style:{
									      	fontSize:'10px'
									      }
						            },
						            pointWidth: 8
						        }
						    },
						    tooltip:{
						    	enabled:true,
						    	headerFormat: '<b>Reed : {point.x}</b>',
          						pointFormat: '<br/>Qty NG : {point.key}'
						    },

						    legend: {
						        enabled: false
						    },

						    series: [{
						        name: 'Qty NG',
						        data: series,
						        colorByPoint:false,
						        color:'#90EE90'
						    }]

						});

						var line = line_unik_tinggi[i];

						document.getElementById("tinggi_line_"+line_unik_tinggi[i]).onclick = function(line){
							showLine(line,'Biri');
							showLine(line,'Tinggi');
							showLine(line,'Rendah');
						}
					}

					//LINE RENDAH

					var line_today = [];

					for(var i = 0; i < result.op_all.length;i++){
						line_today.push(parseInt(result.op_all[i].line.slice(-1)));
					}

					var line_today_unik1 = line_today.filter(onlyUnique);

					var line_today_unik = line_today_unik1.sort(function(a, b){return a - b});

					var line_rendah = [];

					var reeds_rendah = [];

					for(var i = 0; i < result.reed.length;i++){
						if (result.reed[i].ng_name == 'Rendah') {
							if (line_today_unik.includes(result.reed[i].line)) {
								line_rendah.push(result.reed[i].line);
							}
							if (result.reed[i].reed != null) {
								reeds_rendah.push(result.reed[i].reed);
							}
						}
					}

					var all_reeds_rendah = [];

					for(var i = 0; i < reeds_rendah.length;i++){
						if (reeds_rendah[i].match(/,/gi)) {
							var reed = reeds_rendah[i].split(',');
							for(var j = 0; j < reed.length;j++){
								all_reeds_rendah.push(parseInt(reed[j]));
							}
						}else{
							all_reeds_rendah.push(parseInt(reeds_rendah[i]));
						}
					}

					var line_unik_rendah1 = line_rendah.filter(onlyUnique);

					var line_unik_rendah = line_unik_rendah1.sort(function(a, b){return a - b});

					var all_reeds_rendah_unik1 = all_reeds_rendah.filter(onlyUnique);

					var all_reeds_rendah_unik = all_reeds_rendah_unik1.sort(function(a, b){return b - a});

					var all_data = [];

					for(var i = 0; i < line_unik_rendah.length;i++){
						for(var j = 0; j < all_reeds_rendah_unik.length;j++){
							var qty = 0;
							for(var k = 0; k < result.reed.length;k++){
								if (result.reed[k].reed != null && result.reed[k].line == line_unik_rendah[i] && result.reed[k].ng_name == 'Rendah') {
									if (result.reed[k].reed.match(/,/gi)) {
										var reed_pisah = result.reed[k].reed.split(',');
										for(var u = 0; u < reed_pisah.length;u++){
											if (reed_pisah[u] == all_reeds_rendah_unik[j]) {
												qty++;
											}
										}
									}else{
										if (all_reeds_rendah_unik[j] == result.reed[k].reed) {
											qty++;
										}
									}
								}
							}
							all_data.push({
								line:line_unik_rendah[i],ng_name:'Rendah',reed:all_reeds_rendah_unik[j],qty:qty
							});
						}
					}

					$("#containers_rendah_line").html('');
					var cons = '';

					var index = 1;
					var indexes = [];
					for(var i = 0; i < line_unik_rendah.length;i++){
						cons += '<td style="padding:0;" id="container_line_rendah_'+index+'"></td>';
						indexes.push(index);
						index++;
					}

					$("#containers_rendah_line").append(cons);

					for(var i = 0; i < line_unik_rendah.length;i++){
						var category = [];
						var series = [];
						for(var j = 0;j < all_data.length;j++){
							if (all_data[j].line == line_unik_rendah[i]) {
								category.push(all_data[j].reed);
								series.push({low:-(all_data[j].qty/2),high:(all_data[j].qty/2),key:all_data[j].qty});
							}
						}
						
						if (i == 0) {
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"Reed",
									style:{
										fontWeight:'bold'
									}
								},
						    };
						}else{
							var xaxis = {
						        categories: category,
						        gridLineWidth: 0.7,
								gridLineColor: 'RGB(204,255,255)',
								title:{
									text:"",
								},
								labels:{
									enabled:false
								}
						    };
						}

						Highcharts.chart('container_line_rendah_'+indexes[i], {

						    chart: {
						        type: 'columnrange',
						        inverted: true,
						        height:'500',
						        width:'300'
						    },

						    title: {
						    	useHTML: true,
         						text: '<div style="cursor:pointer" id="rendah_line_'+line_unik_rendah[i]+'">Line '+line_unik_rendah[i]+'</div>',
						        style:{
						        	fontWeight:'bold',
						        	fontSize:'15px'
						        }
						    },

						    xAxis: xaxis,

						    yAxis: {
						        title: {
						            text: 'Qty NG',
						            style:{
						            	color:'white'
						            }
						        },
						        labels:{
						        	enabled:false
						        },
						        gridLineWidth: 0.2,
						        gridLineColor: '#b8b8b8'
						    },
						    credits:{
						    	enabled:false
						    },
						    plotOptions: {
						        columnrange: {
						            dataLabels: {
						                enabled: true,
									      formatter: function(e) {
									        if (this.point.key != 0) {
									        	if (e.align == "right") {
										            return 'Reed '+this.point.category+'='+this.point.key
										          }
									        }
									      },
									      style:{
									      	fontSize:'10px'
									      }
						            },
						            pointWidth: 8
						        }
						    },
						    tooltip:{
						    	enabled:true,
						    	headerFormat: '<b>Reed : {point.x}</b>',
          						pointFormat: '<br/>Qty NG : {point.key}'
						    },

						    legend: {
						        enabled: false
						    },

						    series: [{
						        name: 'Qty NG',
						        data: series,
						        colorByPoint:false,
						        color:'#90EE90'
						    }]

						});

						var line = line_unik_rendah[i];

						document.getElementById("rendah_line_"+line_unik_rendah[i]).onclick = function(line){
							showLine(line,'Biri');
							showLine(line,'Tinggi');
							showLine(line,'Rendah');
						}
					}

					data_reed = result.reed;
					data_op = result.op;
					data_op_all = result.op_all;

					$('#loading').hide();
				}
			}
		});
	}

	function showLine(line,ng_name) {
		var lines = line.target.id.slice(-1);
		$('#loading').show();
		var emp_biri = [];

		var reeds_biri = [];

		var op_line_now = [];
		if (data_op_all != null && data_op_all.length > 0) {
			for(var i = 0; i < data_op_all.length;i++){
				if (data_op_all[i].line == 'Line '+lines) {
					op_line_now.push(data_op_all[i].operator_id);
				}
			}
		}

		for(var i = 0; i < data_reed.length;i++){
			if (data_reed[i].ng_name == ng_name && data_reed[i].line == lines) {
				emp_biri.push(data_reed[i].tuning);
				if (data_reed[i].reed != null) {
					reeds_biri.push(data_reed[i].reed);
				}
			}
		}

		var all_reeds_biri = [];

		for(var i = 0; i < reeds_biri.length;i++){
			if (reeds_biri[i].match(/,/gi)) {
				var reed = reeds_biri[i].split(',');
				for(var j = 0; j < reed.length;j++){
					all_reeds_biri.push(parseInt(reed[j]));
				}
			}else{
				all_reeds_biri.push(parseInt(reeds_biri[i]));
			}
		}

		var emp_unik_biri = emp_biri.filter(onlyUnique);

		var all_reeds_biri_unik1 = all_reeds_biri.filter(onlyUnique);

		var all_reeds_biri_unik = all_reeds_biri_unik1.sort(function(a, b){return b - a});

		var all_data = [];

		for(var i = 0; i < emp_unik_biri.length;i++){
			for(var j = 0; j < all_reeds_biri_unik.length;j++){
				var qty = 0;
				for(var k = 0; k < data_reed.length;k++){
					if (data_reed[k].reed != null && data_reed[k].tuning == emp_unik_biri[i] && data_reed[k].ng_name == ng_name && data_reed[k].line == lines) {
						if (data_reed[k].reed.match(/,/gi)) {
							var reed_pisah = data_reed[k].reed.split(',');
							for(var u = 0; u < reed_pisah.length;u++){
								if (reed_pisah[u] == all_reeds_biri_unik[j]) {
									qty++;
								}
							}
						}else{
							if (all_reeds_biri_unik[j] == data_reed[k].reed) {
								qty++;
							}
						}
					}
				}
				all_data.push({
					emp:emp_unik_biri[i],ng_name:ng_name,reed:all_reeds_biri_unik[j],qty:qty
				});
			}
		}

		$("#containers_"+ng_name.toLowerCase()).html('');
		var cons = '';

		var index = 1;
		var indexes = [];
		for(var i = 0; i < emp_unik_biri.length;i++){
			cons += '<td style="padding:0;" id="container_'+ng_name.toLowerCase()+'_'+index+'"></td>';
			indexes.push(index);
			index++;
		}

		$("#containers_"+ng_name.toLowerCase()).append(cons);

		for(var i = 0; i < emp_unik_biri.length;i++){
			if (op_line_now.length > 0) {
				if (op_line_now.includes(emp_unik_biri[i])) {
					var category = [];
					var series = [];
					for(var j = 0;j < all_data.length;j++){
						if (all_data[j].emp == emp_unik_biri[i]) {
							category.push(all_data[j].reed);
							series.push({low:-(all_data[j].qty/2),high:(all_data[j].qty/2),key:all_data[j].qty});
						}
					}
					var op_name = '';
					for(var k = 0; k < data_op.length;k++){
						if (data_op[k].operator == emp_unik_biri[i]) {
							op_name = data_op[k].nama.split(' ').slice(0,2).join(' ');
						}
					}
					
					if (i == 0) {
						var xaxis = {
					        categories: category,
					        gridLineWidth: 0.7,
							gridLineColor: 'RGB(204,255,255)',
							title:{
								text:"Reed",
								style:{
									fontWeight:'bold'
								}
							},
					    };
					}else{
						var xaxis = {
					        categories: category,
					        gridLineWidth: 0.7,
							gridLineColor: 'RGB(204,255,255)',
							title:{
								text:"",
							},
							labels:{
								enabled:false
							}
					    };
					}

					Highcharts.chart('container_'+ng_name.toLowerCase()+'_'+indexes[i], {

					    chart: {
					        type: 'columnrange',
					        inverted: true,
					        height:'500',
					        width:'300'
					    },

					    title: {
					        text: emp_unik_biri[i]+' - '+op_name,
					        style:{
					        	fontWeight:'bold',
					        	fontSize:'15px'
					        }
					    },

					    xAxis: xaxis,

					    yAxis: {
					        title: {
					            text: 'Qty NG',
					            style:{
					            	color:'white'
					            }
					        },
					        labels:{
					        	enabled:false
					        },
					        gridLineWidth: 0.2,
					        gridLineColor: '#b8b8b8'
					    },
					    credits:{
					    	enabled:false
					    },
					    plotOptions: {
					        columnrange: {
					            dataLabels: {
					                enabled: true,
								      formatter: function(e) {
								        if (this.point.key != 0) {
								        	if (e.align == "right") {
									            return 'Reed '+this.point.category+'='+this.point.key
									          }
								        }
								      },
								      style:{
								      	fontSize:'10px'
								      }
					            },
					            pointWidth: 8
					        }
					    },
					    tooltip:{
					    	enabled:true,
					    	headerFormat: '<b>Reed : {point.x}</b>',
								pointFormat: '<br/>Qty NG : {point.key}'
					    },

					    legend: {
					        enabled: false
					    },

					    series: [{
					        name: 'Qty NG',
					        data: series,
					        colorByPoint:false,
					        color:'#90EE90'
					    }]

					});
				}
			}else{
				var category = [];
				var series = [];
				for(var j = 0;j < all_data.length;j++){
					if (all_data[j].emp == emp_unik_biri[i]) {
						category.push(all_data[j].reed);
						series.push({low:-(all_data[j].qty/2),high:(all_data[j].qty/2),key:all_data[j].qty});
					}
				}
				var op_name = '';
				for(var k = 0; k < data_op.length;k++){
					if (data_op[k].operator == emp_unik_biri[i]) {
						op_name = data_op[k].nama.split(' ').slice(0,2).join(' ');
					}
				}
				
				if (i == 0) {
					var xaxis = {
				        categories: category,
				        gridLineWidth: 0.7,
						gridLineColor: 'RGB(204,255,255)',
						title:{
							text:"Reed",
							style:{
								fontWeight:'bold'
							}
						},
				    };
				}else{
					var xaxis = {
				        categories: category,
				        gridLineWidth: 0.7,
						gridLineColor: 'RGB(204,255,255)',
						title:{
							text:"",
						},
						labels:{
							enabled:false
						}
				    };
				}

				Highcharts.chart('container_'+ng_name.toLowerCase()+'_'+indexes[i], {

				    chart: {
				        type: 'columnrange',
				        inverted: true,
				        height:'500',
				        width:'300'
				    },

				    title: {
				        text: emp_unik_biri[i]+' - '+op_name,
				        style:{
				        	fontWeight:'bold',
				        	fontSize:'15px'
				        }
				    },

				    xAxis: xaxis,

				    yAxis: {
				        title: {
				            text: 'Qty NG',
				            style:{
				            	color:'white'
				            }
				        },
				        labels:{
				        	enabled:false
				        },
				        gridLineWidth: 0.2,
				        gridLineColor: '#b8b8b8'
				    },
				    credits:{
				    	enabled:false
				    },
				    plotOptions: {
				        columnrange: {
				            dataLabels: {
				                enabled: true,
							      formatter: function(e) {
							        if (this.point.key != 0) {
							        	if (e.align == "right") {
								            return 'Reed '+this.point.category+'='+this.point.key
								          }
							        }
							      },
							      style:{
							      	fontSize:'10px'
							      }
				            },
				            pointWidth: 8
				        }
				    },
				    tooltip:{
				    	enabled:true,
				    	headerFormat: '<b>Reed : {point.x}</b>',
							pointFormat: '<br/>Qty NG : {point.key}'
				    },

				    legend: {
				        enabled: false
				    },

				    series: [{
				        name: 'Qty NG',
				        data: series,
				        colorByPoint:false,
				        color:'#90EE90'
				    }]

				});
			}
		}
		$('#div_'+ng_name.toLowerCase()).show();
		$('#div_'+ng_name.toLowerCase()+'_line').hide();
		$('#line_'+ng_name.toLowerCase()).html('Line '+lines);
		$('#div_'+ng_name.toLowerCase()).css('overflow-x', 'scroll');
		$('#loading').hide();
	}

	function hideLine(ng) {
		$('#line_biri').html('');
		$('#div_biri').hide();
		$('#div_biri_line').show();

		$('#line_tinggi').html('');
		$('#div_tinggi').hide();
		$('#div_tinggi_line').show();

		$('#line_rendah').html('');
		$('#div_rendah').hide();
		$('#div_rendah_line').show();
	}

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate(){
	var d = new Date();
	var day = addZero(d.getDate());
	var month = addZero(d.getMonth()+1);
	var year = addZero(d.getFullYear());
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
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
			[0, '#2a2a2b']
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
		// gridLineColor: '#707073',
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
		// gridLineColor: '#ebebeb',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		// lineColor: '#707073',
		// minorGridLineColor: '#505053',
		// tickColor: '#707073',
		// tickWidth: 1,
		// title: {
		// 	style: {
		// 		color: '#A0A0A3'
		// 	}
		// }
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
			// gridLineColor: '#505053'
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