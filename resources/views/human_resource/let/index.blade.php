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

	#tableDetail > tbody > tr:hover {
		background-color: #a7ff8f !important;
	}

	#tableCheck > tbody > tr > td:hover{
		background-color: #a7ff8f !important;
	}

	#tableCheck2 > tbody > tr > td:hover{
		background-color: #a7ff8f !important;
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
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 5px;padding-bottom: 10px;">
			<div class="col-xs-3" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode_text"></span></center>
				<input type="hidden" name="periode" id="periode">
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select id="periode_select" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Periode">
					<option value=""></option>
					@foreach($periode as $per)
					<option value="{{$per->fiscal_year}}">{{$per->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 5px">
				<button class="btn btn-default pull-left" onclick="fetchChart()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search
				</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px">
				<!-- <a class="btn btn-info pull-right" href="{{ url('index/sga/assessment') }}" style="margin-left: 5px;">
					<i class="fa fa-pencil"></i> Assessment
				</a> -->
				<button class="btn btn-success pull-right" onclick="fetchChart()" style="margin-left: 5px;">
					<i class="fa fa-refresh"></i> Refresh
				</button>
				<a class="btn btn-danger pull-right" href="{{ url('index/human_resource/let/report') }}" style="margin-left: 5px;">
					<i class="fa fa-book"></i> Report
				</a>
				<a class="btn btn-primary pull-right" href="{{ url('index/human_resource/let/master') }}" style="margin-left: 5px;">
					<i class="fa fa-list"></i> Master
				</a>
				<a class="btn btn-default pull-right" href="{{ url('index/human_resource/let/point_check') }}" style="margin-left: 5px;">
					<i class="fa fa-check-square-o"></i> Point Check
				</a>
			</div>
		</div>
		<div class="col-xs-12" style="">
			<div id="container" style="height: 52vh;"></div>
		</div>
		<div class="col-xs-12" style="" id="divTop">
			<span style="font-weight: bold;font-size: 18px;color: white"><i class="fa fa-trophy fa-2x" style="font-size: 18px"></i>&nbsp;&nbsp;LET Score</span>
			<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
		        <thead>
			        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#dabdff;font-size:15px">
			        	<th style="padding: 2px;text-align: center;width: 1%">#</th>
				        <th style="padding: 2px;text-align: center;width: 2%">ID (社員ID)</th>
				        <th style="padding: 2px;text-align: center;width: 5%">Name (名前)</th>
				        <th style="padding: 2px;text-align: center;width: 10%">Title (役職)</th>
				        <th style="padding: 2px;text-align: center;width: 1%">Dept (部署)</th>
				        <th style="padding: 2px;text-align: center;width: 1%">Total Nilai (得点)</th>
			        </tr>
		        </thead>
		        <tbody id="bodyTableDetail">
		        	
		        </tbody>
		    </table>
		</div>

		<div class="modal modal-default fade" id="modalPenilaian" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg" style="width: 1500px">
				<div class="modal-content">
					<div class="modal-header no-padding">
						<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 10px; padding-bottom: 10px;" class="modal-title">
							PENILAIAN LEADER TRAINING
						</h4>
					</div>
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<table style="width: 100%"  class="table table-bordered table-striped table-hover">
									<tr>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">Assessor ID</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 5%">Assessor Name</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">NIK</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 5%">Nama</th>
									</tr>
									<tr>
										<td style="padding: 3px;" id="asesor_id">{{$username}}</td>
										<td style="padding: 3px;" id="asesor_name">{{$name}}</td>
										<td style="padding: 3px;" id="employee_id"></td>
										<td style="padding: 3px;" id="name"></td>
									</tr>
									<tr>
										<th colspan="4" style="padding: 3px;background-color: lightskyblue;border:1px solid black;">Title</th>
									</tr>
									<tr>
										<td colspan="4" style="padding: 3px;" id="title"></td>
									</tr>
								</table>
								<?php $indexes = 0; ?>
								<table style="width: 100%"  class="table table-bordered table-striped table-hover" id="tableCheck">
										<tr>
										<?php for ($i=0; $i < count($point); $i++) { ?>
											<?php $criterias = explode('_', $point[$i]->criteria); ?>
											<?php for ($j=0; $j < count($criterias); $j++) { ?>
												@if($point[$i]->category == $criterias[$j])
												<th style="width:15%;background-color: #cddc39;border: 1px solid black;">{{$point[$i]->category}}</th>
												@else
												<th style="width:15%;background-color: #cddc39;border: 1px solid black;">{{$point[$i]->category}}<br><br>{{$criterias[$j]}}</th>
												@endif
											<?php } ?>
										<?php } ?>
										</tr>
										<tr>
											<?php for ($i=0; $i < count($point); $i++) { ?>
												<?php $criterias = explode('_', $point[$i]->criteria); ?>
												<?php for ($j=0; $j < count($criterias); $j++) { ?>
													<td id="point_{{$indexes}}" class="hover_td" onclick="fetchPoint('{{$point[$i]->category}}','{{$criterias[$j]}}')" style="cursor: pointer;height: 100px;font-weight: bold;font-size: 40px;text-align: center;"></td>
												<?php $indexes++ ?>
												<?php } ?>
											<?php } ?>
										</tr>
								</table>
								<button class="btn btn-danger" onclick="$('#modalPenilaian').modal('hide');$('#employee_id').html('')" style="font-size: 20px;font-weight: bold;text-align: center;width: 100%">
									SELESAI
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal modal-default fade" id="modalNilai">
			<div class="modal-dialog modal-md" style="width: 100vh">
				<div class="modal-content">
					<div class="modal-header no-padding">
						<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
							PILIH NILAI
						</h4>
					</div>
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<input type="hidden" name="point" id="point">
								<input type="hidden" name="criteria" id="criteria">
								<table style="width: 100%">
									<tr>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-primary" id="1" style="width: 100%;font-size: 20px" onclick="saveResult(60)"><input type="hidden" name="nilai_pilihan_1" id="nilai_pilihan_1">Kurang (60)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-primary" id="2" style="width: 100%;font-size: 20px" onclick="saveResult(70)"><input type="hidden" name="nilai_pilihan_2" id="nilai_pilihan_2">Cukup (70)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-primary" id="3" style="width: 100%;font-size: 20px" onclick="saveResult(80)"><input type="hidden" name="nilai_pilihan_3" id="nilai_pilihan_3">Baik (80)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-primary" id="4" style="width: 100%;font-size: 20px" onclick="saveResult(90)"><input type="hidden" name="nilai_pilihan_4" id="nilai_pilihan_4">Sangat Baik (90)</button>
										</td>
										<td style="padding: 3px;width: 1%">
											<button class="btn btn-danger" id="5" style="width: 100%;font-size: 20px" onclick="saveResult(0)"><input type="hidden" name="nilai_pilihan_5" id="nilai_pilihan_5" value="Clear">Clear</button>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	@endsection
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
	<!-- <script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script> -->
	<script src="{{ url("bower_components/moment/moment.js")}}"></script>
	<script src="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var points = <?php echo json_encode($point) ?>;

		var kata_confirm = 'Are You Sure?';

		jQuery(document).ready(function() {
			$('.datepicker').datepicker({
				<?php $tgl_max = date('Y-m-d') ?>
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,	
				endDate: '<?php echo $tgl_max ?>'
			});
			fetchChart();
			setInterval(fetchChart, 60000);
			$('.select2').select2({
				allowClear:true
			});
		});

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

		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var let_results = null;

		function fetchChart(){
			// $('#loading').show();
			var data = {
				periode:$('#periode_select').val(),
			}
			$.get('{{ url("fetch/human_resource/let") }}',data,function(result, status, xhr){
				if(result.status){

					xCategories = [];
					nilai_seleksi = [];

					var data_all = [];

					$.each(result.let_participants, function(key, value){
						var count_selesai = 0;
						var seleksi = 0;
						for(var i = 0; i < result.let_results.length;i++){
							if (result.let_results[i].employee_id == value.employee_id) {
								seleksi = seleksi + result.let_results[i].result;
								if (result.let_results[i].asesor_id == '{{$username}}') {
									count_selesai++;
								}
							}
						}
						var dept = '';
						for(var i = 0; i < result.emp.length;i++){
							if (result.emp[i].employee_id == value.employee_id) {
								dept = result.emp[i].department_shortname;
							}
						}
						data_all.push({
							'employee_id':value.employee_id,
							'name':value.name,
							'dept':dept,
							'title':value.title,
							'seleksi':seleksi,
							'count_selesai':count_selesai,
						});
					});

					var data_all_sort = sortArray(data_all, "seleksi", -1);

					$.each(data_all_sort, function(key, value){
						xCategories.push(value.employee_id+' - '+value.name);
						nilai_seleksi.push({y:parseInt(value.seleksi),key:value.employee_id+' - '+value.name});
					});

					let_results = result.let_results;

					const chart = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container',
					        type: 'column',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        },
					        style:{
					        	backgroundColor:'none'
					        }
					    },
					    xAxis: {
							categories: xCategories,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Nilai',
								style: {
									color: '#eee',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"15px"
								}
							},
							type: 'linear',
						}
						],
						// tooltip: {
						// 	headerFormat: '<span>{series.name}</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						// },
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
								fontSize:'12px',
							},
						},	
					    title: {
					        text: 'LEADER TRAINING (LET)',
					        style:{
					        	fontWeight:'bold',
					        	fontSize:'17px'
					        }
					    },
					    subtitle: {
					        text: '職長教育の評価',
					        style:{
					        	fontWeight:'bold'
					        }
					    },
					    plotOptions: {
					        series:{
								// cursor: 'pointer',
								point: {
									events: {
										click: function () {
											// ShowModal(this.category,this.series.name,this.options.key);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									},
									// formatter: function() {
							  //           if (this.y > 0) {
							  //             return this.y;
							  //           }
							  //         }
								},
								animation: false,
								// pointPadding: 0.93,
								// groupPadding: 0.93,
								// borderWidth: 0.93,
								// cursor: 'pointer',
								depth:25,
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [{
							type: 'column',
							data: nilai_seleksi,
							name: 'Nilai',
							colorByPoint: false,
							color:'#599bd9',
						}
						]
					});

					$('#bodyTableDetail').html('');
					var tableDetail = '';
					var index = 1;
					$.each(data_all_sort, function(key, value){
						var count_point = 0;
						for(var i = 0; i < result.point.length;i++){
							var criterias = result.point[i].criteria;
							for(var j = 0; j < result.point.length;j++){
								count_point++;
							}
						}
						var bgcolor = 'white';
						if ('{{$role}}' == 'M' || '{{$role}}' == 'DGM' || '{{$role}}' == 'GM' || '{{$role}}' == 'D' || '{{$role}}' == 'M' || '{{$role}}'.match(/HR/gi) || '{{$role}}'.match(/MIS/gi) || '{{$username}}' == 'PI1110002' || '{{$username}}' == 'PI1910002') {
							if (value.count_selesai < count_point) {
								var bgcolor = 'white';
							}else{
								var bgcolor = '#bcff8f';
							}
							tableDetail += '<tr style="background-color:'+bgcolor+';color:black;font-size:14px;cursor:pointer" onclick="inputNilai(\''+value.employee_id+'\',\''+value.name+'\',\''+value.title+'\')">';
						}else{
							tableDetail += '<tr style="background-color:'+bgcolor+';color:black;font-size:14px">';
						}
						tableDetail += '<td style="text-align:right">'+index+'</td>';
						tableDetail += '<td>'+value.employee_id+'</td>';
						tableDetail += '<td>'+value.name+'</td>';
						tableDetail += '<td>'+value.title+'</td>';
						tableDetail += '<td>'+value.dept+'</td>';
						// var seleksi = 0;
						// for(var i = 0; i < result.let_results.length;i++){
						// 	if (result.let_results[i].employee_id == value.employee_id) {
						// 		seleksi = seleksi + result.let_results[i].result;
						// 	}
						// }
						tableDetail += '<td style="text-align:right">'+(value.seleksi || '0')+'</td>';
						tableDetail += '</tr>';
						index++;
					});
					$('#bodyTableDetail').append(tableDetail);
					$("#periode_text").html('PERIODE '+result.periode);
					$("#periode").val(result.periode);
					$('#loading').hide();

					if ($('#employee_id').text() != '') {
						inputNilai($('#employee_id').text(),$('#name').text(),$('#title').text());
					}
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchPoint(point,criteria) {
			$('#modalNilai').modal('show');
			$('#point').val(point);
			$('#criteria').val(criteria);
		}

		function saveResult(id) {
			$('#loading').show();
			var data = {
				asesor_id:$('#asesor_id').text(),
				asesor_name:$('#asesor_name').text(),
				employee_id:$('#employee_id').text(),
				name:$('#name').text(),
				title:$('#title').text(),
				category:$('#point').val(),
				criteria:$('#criteria').val(),
				periode:$('#periode').val(),
				result:id
			}

			$.post('{{ url("input/human_resource/let/evaluation") }}',data,function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#modalNilai').hide();
					$('#modalNilai').modal('hide');
					fetchChart();
				}else{
					$('#loading').hide();
					openErrorGritter('Errro',result.message);
					return false;
				}
			});
		}

		function inputNilai(employee_id,name,title) {
			$('#employee_id').html(employee_id);
			$('#name').html(name);
			$('#title').html(title);
			$('#modalPenilaian').modal('show');
			var indexes = 0;
			for(var j = 0; j < points.length;j++){
				var criterias = points[j].criteria.split('_');
				for(var k = 0; k < criterias.length;k++){
					$('#point_'+indexes).html('');
					indexes++;
				}
			}
			var indexes = 0;
				// if () {
					for(var j = 0; j < points.length;j++){
						var criterias = points[j].criteria.split('_');
						for(var k = 0; k < criterias.length;k++){
							for(var i = 0; i < let_results.length;i++){
							if (let_results[i].criteria == criterias[k] && let_results[i].category == points[j].category && let_results[i].asesor_id == '{{$username}}' && let_results[i].employee_id == employee_id) {
								$('#point_'+indexes).html(let_results[i].result);
							}
							}
							indexes++;
						}
					}
				// }
			
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

		function getFormattedDateTime(date) {
	        var year = date.getFullYear();

	        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
	          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
	        ];

	        var month = date.getMonth();

	        var day = date.getDate().toString();
	        day = day.length > 1 ? day : '0' + day;

	        var hour = date.getHours();
	        if (hour < 10) {
	            hour = "0" + hour;
	        }

	        var minute = date.getMinutes();
	        if (minute < 10) {
	            minute = "0" + minute;
	        }
	        var second = date.getSeconds();
	        if (second < 10) {
	            second = "0" + second;
	        }
	        
	        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
	    }

	    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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