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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-2">
			<div class="row">
				<div class="col-xs-12" style="padding-right:0;">
					<div class="small-box" style="background: #00af50; height: 20vw; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>SUDAH TRAINING</b></h3>
							<!-- <h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>記入済</b></h3> -->
							<h4 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_sudah_cek">0</h4>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_sudah_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #b02828; height: 20vw; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>BELUM TRAINING</b></h3>
							<!-- <h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>未記入</b></h3> -->
							<h4 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_belum_cek">0</h4>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_belum_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-10" style="padding-left: 5px">
			<div id="container" style="height: 40vw;"></div>
		</div>

		<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
	      <div class="modal-dialog modal-lg" style="width: 1200px">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_weekly"></h4>
	          </div>
	          <div class="modal-body">
	            <div class="row">
	              <div class="col-md-12" id="data-activity">
	             	<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
				        <thead>
					        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
						        <th style="padding: 5px;text-align: center;width: 1%">NIK</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Nama</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Dept</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Sect</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Group</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Sub Group</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Status</th>
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

		jQuery(document).ready(function() {
			fetchChart();
			setInterval(fetchChart, 1000 * 60 * 5);
			$('.select2').select2({
				allowClear:true
			});
		});


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var training_filosofi_detail = [];
		var periode = '';

		function fetchChart(){
		
			$.get('{{ url("fetch/filosofi") }}',function(result, status, xhr){
				if(result.status){


					xCategories = [];
					belum = [];
					sudah = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					training_filosofi_detail = [];
					periode = '';

					$.each(result.training_filosofi, function(key, value){
						xCategories.push(value.department_shortname);
						belum.push({y:parseInt(value.belum),key:value.department});
						total_belum = total_belum + parseInt(value.belum);
						sudah.push({y:parseInt(value.sudah),key:value.department});
						total_sudah = total_sudah + parseInt(value.sudah);
					});

					total = total_belum+total_sudah;

					$('#total_sudah_cek').html(total_sudah+' <span style="font-size:2.4vw"> 人</span>');
					$('#total_belum_cek').html(total_belum+' <span style="font-size:2.4vw"> 人</span>');
					$('#persen_sudah_cek').html(((total_sudah/total)*100).toFixed(1)+' %');
					$('#persen_belum_cek').html(((total_belum/total)*100).toFixed(1)+' %');

					$.each(result.training_filosofi_detail, function(key2, value2){
						training_filosofi_detail.push({
							employee_id:value2.employee_id,
							name:value2.name,
							department_shortname:value2.department_shortname,
							department:value2.department,
							section:value2.section,
							group:value2.group,
							sub_group:value2.sub_group,
							status_cek:value2.status_cek,
						});
					});

					Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Resume Peserta Training Filosofi Yamaha',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xCategories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Actual Employee',
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
							opposite: true
						},
					    ],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
				                fontSize:'12px',
				            },
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
											ShowModal(this.category,this.series.name,this.options.key);

				                    }
				                  }
				                },
				                animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							type: 'column',
							data: belum,
							name: 'Belum',
							colorByPoint: false,
							color:'#f44336'
						},{
							type: 'column',
							data: sudah,
							name: 'Sudah',
							colorByPoint: false,
							color:'#32a852'
						}
						]
					});



					periode = result.periode;
					$("#periode").html('PERIODE '+result.periode);
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

		function ShowModal(department_shortname,status_cek,department) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();
			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < training_filosofi_detail.length;i++){
				if (training_filosofi_detail[i].department === department) {
					if (training_filosofi_detail[i].status_cek === status_cek) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+training_filosofi_detail[i].employee_id+'</td>';
						tableDetail += '<td>'+training_filosofi_detail[i].name+'</td>';
						tableDetail += '<td>'+(training_filosofi_detail[i].department_shortname || '')+'</td>';
						tableDetail += '<td>'+(training_filosofi_detail[i].section || '')+'</td>';
						tableDetail += '<td>'+(training_filosofi_detail[i].group || '')+'</td>';
						tableDetail += '<td>'+(training_filosofi_detail[i].sub_group || '')+'</td>';
						tableDetail += '<td>'+(training_filosofi_detail[i].status_cek || '')+'</td>';
						tableDetail += '</tr>';
					}
				}
			}
			$('#bodyTableDetail').append(tableDetail);
			$('#tableDetail').DataTable({
              'dom': 'Bfrtip',
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
              'searching': true,
              'ordering': true,
              'order': [],
              'info': true,
              'autoWidth': true,
              "sPaginationType": "full_numbers",
              "bJQueryUI": true,
              "bAutoWidth": false,
              "processing": true
            });

            $('#judul_weekly').html('Detail '+status_cek+' Training Filosofi Yamaha <br>'+(department || 'Management'));
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status_cek) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < training_filosofi_detail.length;i++){
				if (training_filosofi_detail[i].status_cek === status_cek) {
					tableDetail += '<tr>';
					tableDetail += '<td>'+training_filosofi_detail[i].employee_id+'</td>';
					tableDetail += '<td>'+training_filosofi_detail[i].name+'</td>';
					tableDetail += '<td>'+(training_filosofi_detail[i].department_shortname || '')+'</td>';
					tableDetail += '<td>'+(training_filosofi_detail[i].section || '')+'</td>';
					tableDetail += '<td>'+(training_filosofi_detail[i].group || '')+'</td>';
					tableDetail += '<td>'+(training_filosofi_detail[i].sub_group || '')+'</td>';
					tableDetail += '<td>'+training_filosofi_detail[i].status_cek+'</td>';
					tableDetail += '</tr>';
				}
			}
			$('#bodyTableDetail').append(tableDetail);
			$('#tableDetail').DataTable({
              'dom': 'Bfrtip',
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
              'searching': true,
              'ordering': true,
              'order': [],
              'info': true,
              'autoWidth': true,
              "sPaginationType": "full_numbers",
              "bJQueryUI": true,
              "bAutoWidth": false,
              "processing": true
            });

            $('#judul_weekly').html('Detail '+status_cek+' Training Filosofi Yamaha <br>');
			$('#modalDetail').modal('show');
			$('#loading').hide();
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