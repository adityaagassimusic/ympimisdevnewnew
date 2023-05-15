@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 22%; font-weight: bold">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i> Loading, Please Wait...</span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 5px">
					<div class="input-group date">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<select class="form-control select2" name="period" id="period" data-placeholder="Pilih Periode" style="width: 100%;">
							<option value=""></option>
							@foreach($period as $period)
							<option value="{{$period->month}}">{{$period->month_name}}</option>
							@endforeach
						</select>
					</div>
					<input type="hidden" id="category" value="">
				</div>
				<!-- <div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
					<div class="input-group">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
							<i class="fa fa-list"></i>
						</div>

						<select class="form-control select2" name="category" id="category" data-placeholder="Pilih Kategori" style="width: 100%;" onchange="selectCategory(this.value)">
							<option value=""></option>
							<option value="YMPI">YMPI</option>
							<option value="vendor">Vendor</option>
							<option value="All">All</option>
						</select>
					</div>
				</div> -->
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px; " id="div_ympi">
					<div class="input-group">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
							<i class="fa fa-list"></i>
						</div>
						<select class="form-control select2" name="location" id="location" data-placeholder="Pilih Lokasi" style="width: 100%;">
							<option value=""></option>
							@foreach($loc as $loc)
							<option value="{{$loc->location}}">{{$loc->location}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
					<button class="btn btn-success pull-left" onclick="fetchChart()" style="font-weight: bold;">
						<i class="fa fa-search"></i> Search
					</button>
				</div>
				<!-- <div class="col-xs-2 pull-right">
					<button class="btn btn-danger pull-right" style="font-weight: bold;">
						<i class="fa fa-file-excel-o"></i> Summary Report Audit
					</button>
				</div> -->
			</div>
		</div>

		<div class="col-xs-12">
			<div class="col-lg-12 col-xs-6">
				<center><h3 style="margin-top: 10px; color: white; font-weight: bold; font-size: 30px">Yearly Fixed Asset Audit (Vendor) <span id="period_title">...</span></h3></center>
				<!-- small box -->
				<div class="small-box bg-aqua" style="margin-bottom: 5px" id="total_asset_div">
					<div class="inner">
						<center><h3 id="total_asset" style="font-size: 24px; cursor: pointer">TOTAL ASSET : 0</h3></center>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-3">
				<!-- small box -->
				<div class="small-box bg-orange" id="ck1_div" style="cursor: pointer;">
					<div class="inner">
						<center>
							<p>Asset Check 1</p>

							<h3 id="ck1">0</h3>
						</center>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-3">
				<!-- small box -->
				<div class="small-box bg-orange" id="ck2_div" style="cursor: pointer;">
					<div class="inner">
						<center>
							<p>Asset Check 2</p>

							<h3 id="ck2">0</h3>
						</center>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-3">
				<!-- small box -->
				<div class="small-box bg-red" id="not_ava_div" style="cursor: pointer;">
					<div class="inner">
						<center>
							<p>Asset Not Yet Audited</p>

							<h3 id="not_ava">0</h3>
						</center>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-3">
				<!-- small box -->
				<div class="small-box bg-green" id="ava_div" style="cursor: pointer;">
					<div class="inner">
						<center>
							<p>Asset Audited</p>

							<h3 id="ava">0</h3>
						</center>
					</div>
				</div>
			</div>

		</div>
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-left: 7px; padding-right: 7px">
				<center>
					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px; cursor: pointer" onclick="showModal('','Asset Condition')">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<center>
									<p>Asset Broken</p>

									<h3 id="broken">0</h3>
								</center>
							</div>
						</div>
					</div>

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px; cursor: pointer" onclick="showModal('','Usable Condition')">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<center>
									<p>Asset Not Use</p>

									<h3 id="not_use">0</h3>
								</center>
							</div>
						</div>
					</div>

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px; cursor: pointer" onclick="showModal('','Label Condition')">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<center>
									<p>Label Not Update</p>

									<h3 id="label">0</h3>
								</center>
							</div>
						</div>
					</div>

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px; cursor: pointer" onclick="showModal('','Map Condition')">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<center>
									<p>Map Not Update</p>

									<h3 id="map">0</h3>
								</center>
							</div>
						</div>
					</div>

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px; cursor: pointer" onclick="showModal('','Asset Image Condition')">
						<!-- small box -->
						<div class="small-box bg-orange">
							<div class="inner">
								<center>
									<p>Image Not Update</p>

									<h3 id="foto">0</h3>
								</center>
							</div>
						</div>
					</div>
				</center>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="col-xs-3">
				<center><h3 style="color: white; font-weight: bold; margin-top: 3px">Audit Progress by Status</h3></center>
				<div id="pie_chart" style="height: 85vh;"></div>
			</div>
			<div class="col-xs-9">
				<center><h3 style="color: white; font-weight: bold; margin-top: 3px; margin-bottom: 0px">Fixed Asset Monitoring Per vendor</h3><span id="sub_title_bar" style="color: white; font-weight: bold"></span></center>
				<div id="container"></div>
			</div>
		</div>


		<div class="col-xs-12">
		</div>
	</div>

	<div class="modal fade" id="modalDetailAll">
		<div class="modal-dialog modal-lg" style="width: 95%">
			<div class="modal-content">
				<div class="modal-header">
					<center><h4 style="padding-bottom: 15px;color: black;font-weight: bold;" class="modal-title" id="modalDetailTitleAll"></h4></center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<table class="table table-hover table-bordered table-striped" id="tableDetailAll">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="width: 1%;text-align: center;">#</th>
									<th style="width: 3%;text-align: center;">SAP Number</th>
									<th style="width: 3%;text-align: center;">Asset Name</th>
									<th style="width: 4%;text-align: center;">Reference Photo</th>
									<th style="width: 4%;text-align: center;">Existence</th>
									<th style="width: 3%;text-align: center;">Exception Condition</th>
									<th style="width: 3%;text-align: center;">Note</th>
									<th style="width: 4%;text-align: center;">Audit Photo/Video</th>
									<th style="width: 3%;text-align: center;">Status</th>
									<th style="width: 3%;text-align: center;">Auditor</th>
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
</section>


@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
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

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		// setInterval(fetchChart, 1000 * 60 * 5);
		$('#div_ympi').hide();
	});

	function selectCategory(value) {
		if (value === 'YMPI') {
			$('#div_ympi').show();
		}else{
			$('#div_ympi').hide();
		}
	}

	$("#ava_div").click(function() {
		$('html,body').animate({
			scrollTop: $("#container").offset().top},
			'slow');
	});

	$("#not_ava_div").click(function() {
		$('html,body').animate({
			scrollTop: $("#container").offset().top},
			'slow');
	});

	$("#total_asset_div").click(function() {
		$('html,body').animate({
			scrollTop: $("#container").offset().top},
			'slow');
	});

	function fetchChart(){
		$("#loading").show();
		var data = {
			period:$('#period').val(),
			category:$('#category').val(),
			location:$('#location').val(),
		}
		if ($('#category').val() == 'YMPI') {
			document.getElementById('container').style.height = '300vh';
		}else{
			document.getElementById('container').style.height = '85vh';
		}
		$.get('{{ url("fetch/fixed_asset/monitoring") }}',data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$("#total_asset").text("TOTAL ASSET : "+result.resume[0].total_asset);
				$("#ck1").text(result.resume[0].cek1);
				$("#ck2").text(result.resume[0].cek2);
				$("#ava").text(result.resume[0].close_asset);
				$("#not_ava").text(result.resume[0].open_asset);
				$("#broken").text(result.resume[0].rusak_asset);
				$("#not_use").text(result.resume[0].tidak_digunakan_asset);
				$("#label").text(result.resume[0].label_asset);
				$("#map").text(result.resume[0].tidak_map_asset);
				$("#foto").text(result.resume[0].tidak_foto_asset);
				$("#period_title").html(result.year+" Period<br> on "+result.period);

				open = parseInt(result.resume[0].open_asset);
				close = parseInt(result.resume[0].close_asset);

				// ----------------------------------  Chart Pie ---------------------------

				Highcharts.chart('pie_chart', {
					chart: {
						backgroundColor: '#3c3c3c',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},
					title: {
						text: ''
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
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
								format: '<b>{point.name}</b>: {point.percentage:.1f} %'
							},
							showInLegend: true,
							point: {
								events: {
									click: function () {
										showModal("",this.name);
									}
								}
							}
						}
					},
					credits: {
						enabled: false
					},
					series: [{
						name: 'Status',
						data: [{
							name: 'Open',
							y: open,
							color: '#f45b5b'
						}, {
							name: 'Close',
							y: close,
							color: '#90ee7e'
						}]
					}]
				});

				// ------------------------------- Chart Batang --------------------------

				var categories = [];
				var plan = [];
				var done = [];
				for(var i = 0; i < result.datas.length;i++){
					categories.push(result.datas[i].location);
					plan.push(parseInt(result.datas[i].plan) - parseInt(result.datas[i].done));
					done.push(parseInt(result.datas[i].done));
				}

				$("#sub_title_bar").text('Periode '+result.monthTitle+' On '+result.category.toUpperCase());

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						backgroundColor: '#3c3c3c',
					},
					title: {
						text: '',
						style:{
							fontWeight:'bold'
						}
					},
					subtitle: {
						text: '',
						style:{
							fontWeight:'bold'
						}
					},
					xAxis: {
						categories: categories,
						title: {
							text: null
						},
						labels: {
							useHTML: true,
							formatter: function() {
								var url = '{{url("files/fixed_asset/asset_addendum")}}'+'/'+this.value+'.pdf';

								return '<a target="_blank" href="'+url+'"  style="color: white">' + this.value + '</a>';
							},
						},
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Total Assets',
							align: 'high'
						},
						labels: {
							overflow: 'justify'
						},
					},
					tooltip: {
						valueSuffix: ''
					},
					plotOptions: {
						column: {
							stacking: 'percent',
							dataLabels: {
								enabled: true
							},
							cursor:'pointer',
							point: {
								events: {
									click: function () {
										showModal(this.category,this.series.name);
									}
								}
							},
							animation: false,

						}
					},
					legend: {
						enabled:true,
					},
					credits: {
						enabled: false
					},
					series: [ {
						name: 'Plan',
						data: plan
					}, {
						name: 'Audited',
						data: done
					}, ]
				});
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
	}

	function showModal(location,condition) {
		$('#loading').show();

		var data = {
			location:location,
			condition:condition,
			period:$("#period").val(),
			category:$("#category").val(),
		}
		$.get('{{ url("fetch/fixed_asset/monitoring/detail") }}',data, function(result, status, xhr){
			if(result.status){
				var tableDetail = '';
				$('#tableDetailAll').DataTable().clear();
				$('#tableDetailAll').DataTable().destroy();
				$("#tableDetailBodyAll").html('');
				var index = 1;
				$.each(result.details, function(key, value) {
					tableDetail += '<tr>';
					tableDetail += '<td style="text-align:center">'+index+'</td>';
					tableDetail += '<td style="text-align:center">'+value.sap_number+'</td>';
					tableDetail += '<td style="text-align:center">'+value.asset_name+'</td>';
					var url = '{{url("files/fixed_asset/asset_picture")}}'+'/'+value.asset_images;
					var url2 = '{{url("files/fixed_asset/property_receipt")}}'+'/'+value.sap_number+'.pdf';
					tableDetail += '<td style="text-align:center"><img style="width:150px;" src="'+url+'" class="user-image" alt="Image Not Available"> </td>';
					tableDetail += '<td style="text-align:center">'+(value.availability || '')+'</td>';
					tableDetail += '<td style="text-align:center">';
					if (value.usable_condition != null) {
						tableDetail += 'Asset Digunakan<br><span class="label label-primary">'+value.usable_condition+'</span><br>';
					}
					if (value.asset_condition != null) {
						tableDetail += 'Kondisi Asset<br><span class="label label-primary">'+value.asset_condition+'</span><br>';
					}
					if (value.label_condition != null) {
						tableDetail += 'Kondisi Label<br><span class="label label-primary">'+value.label_condition+'</span><br>';
					}
					if (value.map_condition != null) {
						tableDetail += 'Kondisi Map<br><span class="label label-primary">'+value.map_condition+'</span><br>';
					}
					if (value.asset_image_condition != null) {
						tableDetail += 'Kesesuaian Foto<br><span class="label label-primary">'+value.asset_image_condition+'</span><br>';
					}
					tableDetail += '</td>';

					var repair = '';

					if (value.sap_number == '70000121') {
						var url_1 = '{{url("files/fixed_asset/other")}}'+'/'+value.sap_number+'_YMPI.pdf';
						var url_2 = '{{url("files/fixed_asset/other")}}'+'/'+value.sap_number+'_YCJ.pdf';

						repair = '<br><a class="label label-primary" href="'+url_1+'" target="_blank"><i class="fa fa-book"></i> Delivery_YMPI.pdf</a><br><a class="label label-primary" href="'+url_2+'" target="_blank"><i class="fa fa-book"></i> Delivery_YCJ.pdf</a>';
					}

					var url = '{{url("files/fixed_asset/remote_report")}}'+'/'+value.sap_number+'.pdf';
					// tableDetail += '<td style="text-align:center; padding: 0px;">';
					// tableDetail += '<div><a class="label label-danger" href="'+url+'" target="_blank"><i class="fa fa-book"></i> Remote : BeritaAcara.pdf</a>'+repair+'</div><br>';
					// tableDetail += '<br><div style="vertical-align: middle; height: 50%">'+(value.note || '')+'</div>';
					// tableDetail += '</td>';
					tableDetail += '<td></td>';

					var url = '{{url("files/fixed_asset/asset_audit")}}'+'/'+value.result_images;

					if (value.result_images == null) {
						tableDetail += '<td style="text-align:center"></td>';
					}else{
						tableDetail += '<td style="text-align:center"><img style="width:150px;" src="'+url+'" class="user-image" alt="Image Not Available"></td>';
					}
					if (value.status == 'Close') {
						var color = '#bfffa6';
					}else{
						var color = '#ffa6a6';
					}
					tableDetail += '<td style="text-align:center;background-color:'+color+'">'+value.status+'</td>';
					tableDetail += '<td style="text-align:center">'+value.checked_by.split("/")[0]+'<br>'+value.checked_by.split("/")[1]+'</td>';

					tableDetail += '</tr>';
					index++;
				});
				$("#tableDetailBodyAll").append(tableDetail);
				$('#modalDetailTitleAll').html('Detail Audit Fixed Asset<br>'+result.category.toUpperCase()+' - '+(result.location || '')+'<br>'+result.monthTitle+' Period');

				var table = $('#tableDetailAll').DataTable({
					'dom': '<"pull-left"B><"pull-right"f>rt<"row"<"col-sm-3"l><"col-sm-3"i><"col-sm-6"p>>',
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

				$('#loading').hide();
				$('#modalDetailAll').modal('show');
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed.');
			}
		});
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

</script>
@endsection