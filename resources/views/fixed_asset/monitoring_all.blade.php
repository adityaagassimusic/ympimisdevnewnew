@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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

	.inner > center > h3 {
		margin-bottom: 0px;
		font-weight: bold;
	}

	.inner > center > p {
		font-weight: bold;
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
		<div class="col-xs-12" style="margin-bottom: 2vh">
			<div class="form-group">
				<div class="col-sm-2">
					<div class="input-group date">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white">
							<i class="fa fa-calendar"></i>
						</div>
						<select class="form-control select2" id="periode" data-placeholder="Pilih Periode" style="width: 100%;" style="text-align: center">
							<option value=""></option>
							@foreach($period as $period)
							<option value="{{$period->month}}">{{$period->month_name}}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="input-group">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white">
							<i class="fa fa-list"></i>
						</div>
						<select class="select2 form-control" id="category" data-placeholder="Select Category" style="text-align: center;">
							<option value=""></option>
							<option value="YMPI">YMPI</option>
							<option value="Vendor" selected>Vendor</option>
						</select>
					</div>
					
				</div>
				<div class="col-sm-2">
					<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-magic"></i> Generate</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-lg-12 col-xs-6">
				<center><h3 style="margin-top: 10px; color: white; font-weight: bold">Fixed Asset Audit Progress on <span id="period_title">...</span></h3></center>
				<!-- small box -->
				<div class="small-box bg-aqua" style="margin-bottom: 5px">
					<div class="inner">
						<center><h3 id="total_asset">TOTAL ASSET : 0</h3></center>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-green">
					<div class="inner">
						<center>
							<p>Asset Audited</p>

							<h3 id="ava">0</h3>
						</center>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-red">
					<div class="inner">
						<center>
							<p>Asset Not Yet Audited</p>

							<h3 id="not_ava">0</h3>
						</center>
					</div>
				</div>
			</div>

		</div>
		<div class="col-xs-12">
			<div class="col-xs-12">
				<center>
					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px" onclick="detailInformation('Asset Condition')">
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

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px" onclick="detailInformation('Usable Condition')">
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

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px" onclick="detailInformation('Label Condition')">
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

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px" onclick="detailInformation('Map Condition')">
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

					<div style="width: 19%; display: inline-block; margin-left: 5px; margin-right: 5px" onclick="detailInformation('Asset Image Condition')">
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
			<div class="col-xs-12" style="border: 2px solid white; border-radius: 3px">
				<center><h3 style="color: white; font-weight: bold; margin-top: 3px">Audit Progress by Status</h3></center>
				<div id="pie_chart" style="height: 90vh;"></div>
			</div>
		</div>
	</section>

	<div class="modal modal-default fade" id="detailStatusModal">
		<div class="modal-dialog modal-lg" style="width: 95%">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail Audit Progress "<span id="status"></span>"</h1>
					</div>
				</div>
				<div class="modal-body" style="padding-top: 0px">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body" style="padding: 0px;margin-top: 0px">
								
								<table class="table table-bordered" id="tableDetail" style="text-align: center;">
									<thead style="background-color: #7e5686; color: white">
										<tr>
											<th>Location</th>
											<th>SAP Number</th>
											<th>Nama Asset</th>
											<th>Foto Referensi</th>
											<th>Keberadaan</th>
											<th>Kondisi Pengecualian</th>
											<th>Note</th>
											<th>Foto Audit</th>
											<th>Status</th>
											<th>Auditor</th>
										</tr>
									</thead>
									<tbody id="bodyDetail"></tbody>
								</table>
								
							</div>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div>

	@endsection
	@section('scripts')
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/highcharts-3d.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script src="{{ url("js/accessibility.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		jQuery(document).ready(function() {
			$('.datepicker').datepicker({
				format: "yyyy-mm",
				startView: "months", 
				minViewMode: "months",
				autoclose: true,
			});

			$('.select2').select2({
				language : {
					noResults : function(params) {

					}
				}
			});
			fetchChart();
		});

		function fetchChart(){
			$("#loading").show();

			var open = 0;
			var close = 0;

			var data = {
				period : $("#periode").val(),
				category : $("#category").val()
			}

			$.get('{{ url("fetch/fixed_asset/monitoring_all") }}', data, function(result, status, xhr){
				$("#loading").hide();
				if(result.status){
					$("#total_asset").text("TOTAL ASSET : "+result.datas[0].total_asset);
					$("#ava").text(result.datas[0].close_asset);
					$("#not_ava").text(result.datas[0].open_asset);
					$("#broken").text(result.datas[0].rusak_asset);
					$("#not_use").text(result.datas[0].tidak_digunakan_asset);
					$("#label").text(result.datas[0].label_asset);
					$("#map").text(result.datas[0].tidak_map_asset);
					$("#foto").text(result.datas[0].tidak_foto_asset);
					$("#period_title").text(result.period);

					open = parseInt(result.datas[0].open_asset);
					close = parseInt(result.datas[0].close_asset);

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
											detailInformation(this.name);
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
				} else{
					alert('Attempt to retrieve data failed.');
				}
			});		
		}

		function detailInformation(status_asset) {
			$('#loading').show();

			var data = {
				period: $("#periode").val(),
				category_name: $("#category").val(),
				category: status_asset
			}

			$("#status").text(status_asset);

			$.get('{{ url("fetch/fixed_asset/monitoring_all/detail") }}', data ,function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#detailStatusModal').modal('show');

					$('#tableDetail').DataTable().clear();
					$('#tableDetail').DataTable().destroy();

					$("#bodyDetail").empty();
					var body = "";
					$.each(result.details, function(key, value) {
						body += "<tr>";
						body += "<td>"+value.location+"</td>";
						body += "<td>"+value.sap_number+"</td>";
						body += "<td>"+value.asset_name+"</td>";

						var url = '{{url("files/fixed_asset/asset_picture")}}'+'/'+value.asset_images;
						body += '<td style="text-align:center"><img style="width:150px;" src="'+url+'" class="user-image" alt="Asset Image"></td>';

						// body += "<td>"+value.asset_images+"</td>";
						body += "<td>"+value.availability+"</td>";
						body += "<td style='text-align: left'>";
						body += "Asset Digunakan : <br><span class='label label-primary'>"+(value.usable_condition || '')+"</span><br>";
						body += "Kondisi Asset : <br><span class='label label-primary'>"+(value.asset_condition || '')+"</span><br>";
						body += "Kondisi Label : <br><span class='label label-primary'>"+(value.label_condition || '')+"</span><br>";
						body += "Map : <br><span class='label label-primary'>"+(value.asset_image_condition || '')+"</span><br>";
						body += "</td>";
						body += "<td>"+(value.note || '')+"</td>";

						var url = '{{url("files/fixed_asset/asset_audit")}}'+'/'+value.result_images;

						if (value.status == "Close") {
							body += '<td style="text-align:center"><img style="width:150px;" src="'+url+'" class="user-image" alt="Asset image"></td>';
						} else {
							body += "<td></td>";
						}

						// body += "<td>"+value.result_images+"</td>";

						if (value.status == "Close") {
							body += '<td style="font-size: 17px;background-color:#00a65a;text-align:center;color:white">'+value.status+'</td>';
						}else{
							body += '<td style="font-size: 17px;background-color:red;text-align:center;color:white">'+value.status+'</td>';
						}

						// body += "<td>"+value.status+"</td>";
						if (value.status == "Close") {
							body += "<td>"+value.name+"</td>";
						} else {
							body += "<td></td>";
						}
						body += "</tr>";
					})

					$("#bodyDetail").append(body);					

					var table = $('#tableDetail').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 5, 10, 25, -1 ],
						[ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
						'pageLength': 15,
						'searching': true,
						'ordering': true,
						'order': [],
						'info': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
					});
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error',result.message);
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
				time: '2000'
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