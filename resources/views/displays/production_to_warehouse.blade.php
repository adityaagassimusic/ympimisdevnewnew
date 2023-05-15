@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table{
		border:1px solid black !important;
	}
	thead>tr>th{
		vertical-align: middle !important;
		text-align:center !important;
		border:1px solid black !important;
	}
	tbody>tr>td{
		vertical-align: middle !important;
		padding: 3px 3px 3px 3px !important;
		border:1px solid black !important;
	}
	tfoot>tr>th{
		border:1px solid black !important;
		padding: 3px 3px 3px 3px !important;
	}
	.total:hover {
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	#loading{
		display: none;
	}
</style>
@stop
@section('header')

@stop
@section('content')
<section class="content" style="padding-top: 0">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-2" style="margin-top: 0; margin-bottom: 0;">
			<div class="form-group">
				<label style="padding-top: 0; padding-left: 0; color: white;" for="" class="col-xs-12 control-label">Cari Periode<span class="text-red"></span> :</label>
				<select class="form-control select2" id="fiscal_year" style="width: 100%; height: 100%;" data-placeholder="Select Fiscal Year" onchange="fetchData()" required>
					@foreach($fiscal_years as $fiscal_year)
					<option value="{{ $fiscal_year->fiscal_year }}">{{ $fiscal_year->fiscal_year }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div id="chart_title" class="col-xs-10" style="background-color: rgb(96, 92, 168); height: 60px;">
			<center>
				<span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
			</center>
		</div>
		<div class="col-xs-12" style="margin-top: 0;">
			<div class="box box-solid">
				<div class="box-body">
					<div id="container" style="height: 30vh;"></div>
					<div class="col-xs-12" style="padding-left: 0;" id="div_table">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #605ca8; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Production Resume Detail<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableDetail" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 0.1%; text-align: center;">#</th>
								<th style="width: 1%; text-align: left;">HPL</th>
								<th style="width: 1%; text-align: left;">Material</th>
								<th style="width: 2%; text-align: left;">Deskripsi</th>
								<th style="width: 1%; text-align: left;">Destinasi</th>
								<th style="width: 1%; text-align: right;">Shipment</th>
								<th style="width: 1%; text-align: right;">Plan</th>
								<th style="width: 1%; text-align: right;">Tepat Waktu</th>
								<th style="width: 1%; text-align: right;">Terlambat</th>
								<th style="width: 1%; text-align: right;">%</th>
								<th style="width: 1%; text-align: right;">Tanggal Terakhir ke FSTK</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tbody id="tableDetailFoot">
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
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			minimumResultsForSearch: -1
		});
		fetchData();
	});

	var details = [];
	var weekly_calendars = [];

	function modalDetail(st_month){
		$('#tableDetailBody').html("");
		var tableDetailBody = "";
		var cnt = 1;
		$.each(details, function(key, value){
			if(value.st_month == st_month){
				if(value.ok_total < value.plan_total){
					color = "background-color: #ffccff;"
				}
				else{
					color = "";
				}
				tableDetailBody += '<tr style="'+color+'">';
				tableDetailBody += '<td style="width: 0.1%; text-align: center;">'+cnt+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: left;">'+value.hpl+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: left;">'+value.material_number+'</td>';
				tableDetailBody += '<td style="width: 2%; text-align: left;">'+value.material_description+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: left;">'+value.destination_shortname+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+value.st_date+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+value.plan_total+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+value.ok_total+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+(value.plan_total-value.ok_total)+'</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+((value.ok_total/value.plan_total)*100).toFixed(2)+'%</td>';
				tableDetailBody += '<td style="width: 1%; text-align: right;">'+value.ng_date+'</td>';
				tableDetailBody += '</tr>';
				cnt += 1;				
			}
		});
		$('#tableDetailBody').append(tableDetailBody);
		$('#modalDetail').modal('show');		
	}

	function fetchData(){
		$('#loading').show();

		var fiscal_year = $('#fiscal_year').val();
		var data = {
			fiscal_year:fiscal_year
		}

		$.get('{{ url("fetch/production_warehouse") }}', data, function(result, status, xhr){
			if(result.status){
				$('#title_text').text('Pengiriman Produksi ke FSTK '+$('#fiscal_year').val());
				details = result.details;
				weekly_calendars = result.weekly_calendars;

				var array = result.details;
				var resume_charts = [];
				var charts = [];

				array.reduce(function(res, value){
					if(!res[value.st_month]){
						res[value.st_month] = { st_month: value.st_month, plan_total: 0, ng_total: 0, ok_total: 0 };
						resume_charts.push(res[value.st_month]);
					}
					res[value.st_month].plan_total += value.plan_total;
					res[value.st_month].ng_total += value.ng_total;
					res[value.st_month].ok_total += value.ok_total;
					return res;
				}, {});

				for (var i = 0; i < weekly_calendars.length; i++) {
					var st_month = weekly_calendars[i].st_month;
					var plan_total = 0;
					var ng_total = 0;
					var ok_total = 0;

					for (var j = 0; j < resume_charts.length; j++) {
						if(resume_charts[j].st_month == st_month){
							plan_total = resume_charts[j].plan_total;
							ng_total = resume_charts[j].ng_total;
							ok_total = resume_charts[j].ok_total;
							break;
						}
					}

					charts.push({
						'st_month': st_month,
						'plan_total': plan_total,
						'ng_total': ng_total,
						'ok_total': ok_total,
					});
				}

				var categories = [];
				var series_plan = [];
				var series_ng = [];
				var series_ok = [];
				var series_persentase = [];

				$.each(charts, function(key, value){
					categories.push(value.st_month);
					series_plan.push(value.plan_total);
					series_ng.push(value.plan_total-value.ok_total);
					series_ok.push(value.ok_total);
					if(value.ok_total != 0){
						series_persentase.push((value.ok_total/value.plan_total)*100);						
					}
					else{
						series_persentase.push(0);
					}
				});
				
				Highcharts.chart('container', {

					chart: {
						type: 'column'
					},

					title: {
						text: '('+result.min_date+' - '+result.max_date+')'
					},

					xAxis: {
						categories: categories
					},

					yAxis: [{
						title: {
							text: 'Set(s)'
						}
					},{
						title: {
							text: 'Persentase %'
						},
						opposite: true
					}],

					tooltip: {
						shared: true
					},

					legend: {
						layout: 'vertical',
						align: 'left',
						verticalAlign: 'middle'
					},

					credits: {
						enabled: false
					},

					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121'
						},
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										modalDetail(this.category);
									}
								}
							}
						},
						line: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.1f}%'
							},
							enableMouseTracking: false
						}
					},

					series: [{
						name: 'Terlambat',
						data: series_ng,
						stack: 'Jumlah',
						color: '#dd4b39'
					}, {
						name: 'Tepat Waktu',
						data: series_ok,
						stack: 'Jumlah',
						color: '#00a65a'
					}, {
						name: 'Persentase%',
						type: 'line',
						data: series_persentase,
						color: 'black',
						yAxis: 1,
					}]
				});

				var array = result.details;
				var resume_tables = [];

				array.reduce(function(res, value){
					if(!res[value.st_month+'-'+value.hpl]){
						res[value.st_month+'-'+value.hpl] = { st_month: value.st_month, hpl: value.hpl, plan_total: 0, ng_total: 0, ok_total: 0 };
						resume_tables.push(res[value.st_month+'-'+value.hpl]);
					}
					res[value.st_month+'-'+value.hpl].plan_total += value.plan_total;
					res[value.st_month+'-'+value.hpl].ng_total += value.ng_total;
					res[value.st_month+'-'+value.hpl].ok_total += value.ok_total;
					return res;
				}, {});

				var categories_hpl = [];

				$.each(result.details, function(key, value){
					if(jQuery.inArray(value.hpl, categories_hpl) == -1){
						categories_hpl.push(value.hpl);						
					}
				});

				var div_table = "";
				$('#div_table').html("");

				div_table += '<table id="tableResumeFG" class="table table-bordered table-striped table-hover">';
				div_table += '<thead style="background-color: rgb(96, 92, 168); color: white;">';
				div_table += '<tr>';
				div_table += '<th colspan="2" style="width: 1%; text-align: center; font-size: 18px;">HPL</th>';
				$.each(categories, function(key, value){
					div_table += '<th style="width: 1%; text-align: center; font-size: 18px;">'+value+'</th>';
				});
				div_table += '</tr>';
				div_table += '</thead>';
				div_table += '<tbody>';
				for (var i = 0; i < categories_hpl.length; i++) {
					div_table += '<tr>';
					div_table += '<td rowspan="4" style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center; font-weight: bold; font-size: 20px;">'+categories_hpl[i]+'</td>';
					div_table += '<td style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center;">Rencana</td>';
					for (var j = 0; j < categories.length; j++) {
						var inserted = false;
						for (var k = 0; k < resume_tables.length; k++) {
							if(categories[j] == resume_tables[k].st_month && categories_hpl[i] == resume_tables[k].hpl){
								div_table += '<td style="text-align: right;">'+resume_tables[k].plan_total+'</td>';
								inserted = true;
								break;
							}
						}
						if(!inserted){
							div_table += '<td style="text-align: right;">-</td>';							
						}
					}
					div_table += '</tr>';
					div_table += '<td style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center;">Tepat Waktu</td>';
					for (var j = 0; j < categories.length; j++) {
						var inserted = false;
						for (var k = 0; k < resume_tables.length; k++) {
							if(categories[j] == resume_tables[k].st_month && categories_hpl[i] == resume_tables[k].hpl){
								div_table += '<td style="text-align: right;">'+resume_tables[k].ok_total+'</td>';
								inserted = true;
								break;
							}
						}
						if(!inserted){
							div_table += '<td style="text-align: right;">-</td>';							
						}
					}
					div_table += '</tr>';
					div_table += '<td style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center;">Terlambat</td>';
					for (var j = 0; j < categories.length; j++) {
						var inserted = false;
						for (var k = 0; k < resume_tables.length; k++) {
							if(categories[j] == resume_tables[k].st_month && categories_hpl[i] == resume_tables[k].hpl){
								div_table += '<td style="text-align: right;">'+(resume_tables[k].plan_total-resume_tables[k].ok_total)+'</td>';
								inserted = true;
								break;
							}
						}
						if(!inserted){
							div_table += '<td style="text-align: right;">-</td>';							
						}
					}
					div_table += '</tr>';
					div_table += '<td style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center;">(Tepat Waktu/Rencana)</td>';
					for (var j = 0; j < categories.length; j++) {
						var inserted = false;
						for (var k = 0; k < resume_tables.length; k++) {
							if(categories[j] == resume_tables[k].st_month && categories_hpl[i] == resume_tables[k].hpl){
								div_table += '<td style="text-align: right; font-weight: bold;">'+((resume_tables[k].ok_total/resume_tables[k].plan_total)*100).toFixed(1)+'%</td>';
								inserted = true;
								break;
							}
						}
						if(!inserted){
							div_table += '<td style="text-align: right;">-</td>';							
						}
					}
					div_table += '</tr>';
				};

				div_table += '<tr>';
				div_table += '<td rowspan="4" style="background-color: #ccffff; width: 1%; text-align: center; font-weight: bold; font-size: 20px;">ALL<br>PRODUCTS</td>';
				div_table += '<td style="background-color: #ccffff; width: 1%; text-align: center;">Rencana</td>';
				for (var j = 0; j < categories.length; j++) {
					var inserted = false;
					for (var k = 0; k < charts.length; k++) {
						if(categories[j] == charts[k].st_month){
							div_table += '<td style="text-align: right; background-color: #fffcb7;">'+charts[k].plan_total+'</td>';
							inserted = true;
							break;
						}
					}
					if(!inserted){
						div_table += '<td style="text-align: right;">-</td>';							
					}
				}
				div_table += '</tr>';
				div_table += '<tr>';
				div_table += '<td style="background-color: #ccffff; width: 1%; text-align: center;">Tepat Waktu</td>';
				for (var j = 0; j < categories.length; j++) {
					var inserted = false;
					for (var k = 0; k < charts.length; k++) {
						if(categories[j] == charts[k].st_month){
							div_table += '<td style="text-align: right; background-color: #fffcb7;">'+charts[k].ok_total+'</td>';
							inserted = true;
							break;
						}
					}
					if(!inserted){
						div_table += '<td style="text-align: right;">-</td>';							
					}
				}
				div_table += '</tr>';
				div_table += '<tr>';
				div_table += '<td style="background-color: #ccffff; width: 1%; text-align: center;">Terlambat</td>';
				for (var j = 0; j < categories.length; j++) {
					var inserted = false;
					for (var k = 0; k < charts.length; k++) {
						if(categories[j] == charts[k].st_month){
							div_table += '<td style="text-align: right; background-color: #fffcb7;">'+(charts[k].plan_total-charts[k].ok_total)+'</td>';
							inserted = true;
							break;
						}
					}
					if(!inserted){
						div_table += '<td style="text-align: right;">-</td>';							
					}
				}
				div_table += '</tr>';
				div_table += '<tr>';
				div_table += '<td style="background-color: #ccffff; width: 1%; text-align: center;">(Tepat Waktu/Rencana)</td>';
				for (var j = 0; j < categories.length; j++) {
					var inserted = false;
					for (var k = 0; k < charts.length; k++) {
						if(categories[j] == charts[k].st_month){
							if(charts[k].plan_total == 0){
								div_table += '<td style="text-align: right; background-color: #fffcb7; font-weight: bold; font-size: 18px;">-</td>';
							}
							else{
								div_table += '<td class="total" style="text-align: right; background-color: #fffcb7; font-weight: bold; font-size: 18px;" onclick="modalDetail(\''+categories[j]+'\')">'+((charts[k].ok_total/charts[k].plan_total)*100).toFixed(1)+'%</td>';
							}
							inserted = true;
							break;
						}
					}
					if(!inserted){
						div_table += '<td style="text-align: right;">-</td>';							
					}
				}
				div_table += '</tr>';

				// for (var i = 0; i < categories_hpl.length; i++) {
				// 	div_table += '<tr>';
				// 	div_table += '<td rowspan="2" style="background-color: rgb(96, 92, 168); color: white; width: 1%; text-align: center;">'+categories_hpl[i]+'</td>';
				// 	for (var j = 0; j < categories.length; j++) {
				// 		var inserted = false;
				// 		for (var k = 0; k < resume_tables.length; k++) {
				// 			if(categories[j] == resume_tables[k].st_month && categories_hpl[i] == resume_tables[k].hpl){
				// 				div_table += '<td>Plan</td>';
				// 				div_table += '<td>'+resume_tables[k].plan_total+'</td>';
				// 				var inserted = true;
				// 				break;
				// 			}
				// 		}
				// 		if(inserted == false){
				// 			div_table += '<td>Plan</td>';
				// 			div_table += '<td>-</td>';							
				// 		}
				// 	}

				// 	div_table += '</tr>';
				// };

				div_table += '</tbody>';
				div_table += '</table>';

				$('#div_table').append(div_table);

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed.')
			}
		});
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