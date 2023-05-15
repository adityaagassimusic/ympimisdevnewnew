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
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;">
			<div class="col-xs-12" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">Pencapaian Meeting CPAR</span></center>
			</div>
		</div>


		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-4" style="padding-right:20px;">
					<div class="small-box" style="background: #ff9800; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('new_cpar')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>New CPAR</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_new">0</h5>
						</div>
						<div class="icon" style="padding-top: 10px;">
							<i class="fa fa-file"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-4" style="padding-right:20px;">
					<div class="small-box" style="background: #b02828; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Open')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>Meeting Open</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_open">0</h5>
						</div>
						<div class="icon" style="padding-top: 20px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
				</div>
				<!-- <div class="col-xs-3" style="padding-right:20px;">
					<div class="small-box" style="background: #ff9800; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('CloseRevised')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>Meeting Close Dengan Revisi</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_revisi">0</h5>
						</div>
						<div class="icon" style="padding-top: 20px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
				</div> -->
				<div class="col-xs-4" style="padding-right:20px;">
					<div class="small-box" style="background: #00af50; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Close')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>Meeting Close</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_close">0</h5>
						</div>
						<div class="icon" style="padding-top: 20px;">
							<!-- <i class="fa fa-check"></i> -->
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>Close Revisi</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;text-align: right;" id="total_revisi">0</h5>
						</div>
					</div>
				</div>
				
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 10px">
			<div id="container" style="height: 50vh;"></div>
		</div>

		<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
	      <div class="modal-dialog modal-lg" style="width: 1200px">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
	          </div>
	          <div class="modal-body">
	            <div class="row">
	              <div class="col-md-12" id="data-activity">
	             	<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
				        <thead>
					        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
						        <th style="padding: 5px;text-align: center;width: 1%">No CPAR</th>
						        <th style="padding: 5px;text-align: center;width: 4%">Permasalahan</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Departemen Penerima</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Kategori</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Approval</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Status</th>
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

		var detail = [];

		function fetchChart(){
			$.get('{{ url("fetch/qc_report/cpar_meeting") }}',function(result, status, xhr){
				if(result.status){

					var total_open = 0;
					var total_revisi = 0;
					var total_close = 0;
					var total_new = 0;

					detail = [];

					var month = [], 
					tahun = [],
					jml = [], 
					meeting_open = [], 
					meeting_revisi = [], 
					meeting_close = [], 
					new_cpar = [];

					$.each(result.resume, function(key, value){
						if(parseInt(value.meeting_open) != 0 || parseInt(value.meeting_revisi) != 0 || parseInt(value.new_cpar) != 0){
							month.push(value.bulan);
	            tahun.push(value.tahun);
	            // console.log(value.meeting_open);
	            // console.log(value.meeting_open[key-1]);
	            meeting_open.push({y: parseInt(value.meeting_open),key:value.tahun});
	            meeting_revisi.push({y: parseInt(value.meeting_revisi),key:value.tahun});
	            meeting_close.push({y: parseInt(value.meeting_close),key:value.tahun});
	            new_cpar.push({y: parseInt(value.new_cpar),key:value.tahun});

							total_open = total_open + parseInt(value.meeting_open);
							total_new = total_new + parseInt(value.new_cpar);
						}
						total_revisi = total_revisi + parseInt(value.meeting_revisi);
						total_close = total_close + parseInt(value.meeting_close);
					});

					total_close_all = total_close+total_revisi;

					$('#total_open').html(total_open+' <span style="font-size:2.4vw"></span>');
					$('#total_revisi').html(total_revisi+' <span style="font-size:2.4vw"></span>');
					$('#total_close').html(total_close_all+' <span style="font-size:2.4vw"></span>');
					$('#total_new').html(total_new+' <span style="font-size:2.4vw"></span>');

					$.each(result.detail, function(key2, value2){
						detail.push({
							cpar_no:value2.cpar_no,
							tgl_permintaan:value2.tgl_permintaan,
							judul_komplain:value2.judul_komplain,
							department_name:value2.department_name,
							kategori:value2.kategori,
							kategori_komplain:value2.kategori_komplain,
							kategori_approval:value2.kategori_approval,
							kategori_meeting:value2.kategori_meeting
						});
					});

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
					        }
					    },
					    xAxis: {
							categories: month,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
                formatter: function (e) {
                  return ''+ this.value +' '+tahun[(this.pos)];
                }
              },
						},
						yAxis: [{
							title: {
								text: 'Total Data',
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
						}
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
							}
						},	
					    title: {
					        text: ''
					    },
					    subtitle: {
					        text: ''
					    },
					    plotOptions: {
					        column: {
					            stacking: 'normal'
					        },
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
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [
					    {
	                name: 'New CPAR',
	                data: new_cpar,
	                stack : 'new',
	                color : '#f0ad4e' //f5f500
	            },
					    {
	                name: 'Meeting Open',
	                color: '#ff6666', //ff6666
	                stack: 'open',
	                data: meeting_open
	            }, 
	            {
	                name: 'Meeting Close',
	                data: meeting_close,
	                stack: 'close',
	                color : '#5cb85c' //00f57f
	            },
	            {
	                name: 'Meeting Close Dengan Revisi',
	                data: meeting_revisi,
	                stack: 'close',
	                color : '#448aff' //f5f500
	            }]

					 //    series: [{
						// 	type: 'column',
						// 	data: need_order,
						// 	name: 'Belum Order',
						// 	colorByPoint: false,
						// 	color:'#f44336'
						// },{
						// 	type: 'column',
						// 	data: order,
						// 	name: 'Sedang Order',
						// 	colorByPoint: false,
						// 	color:'#32a852'
						// }
						// ]
					});

				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

		function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return monthNames[month];
    }

		function ShowModal(bulan,status,tahun) {
			$('#tableDetail').DataTable().clear();
      $('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			// console.log(bulan);

			for(var i = 0; i < detail.length;i++){
			// console.log(getFormattedDate(new Date(detail[i].tgl_permintaan)));
			// console.log(new Date(detail[i].tgl_permintaan).getFullYear());


				if (getFormattedDate(new Date(detail[i].tgl_permintaan)) === bulan && new Date(detail[i].tgl_permintaan).getFullYear() == tahun) {

					var stat = "";

					if (status == "Meeting Open") {
						stat = 'Open';
					}
					else if(status == "Meeting Close Dengan Revisi") {
						stat = 'CloseRevised';
					}
					else if(status == "Meeting Close") {
						stat = 'Close';
					}
					else if(status == "New CPAR") {
						stat = null;
					}

					if (detail[i].kategori_meeting === stat) {

						tableDetail += '<tr>';
						tableDetail += '<td>'+detail[i].cpar_no+'</td>';
						tableDetail += '<td>'+detail[i].judul_komplain+'</td>';
						tableDetail += '<td>'+detail[i].department_name+'</td>';
						tableDetail += '<td>'+detail[i].kategori+' - '+detail[i].kategori_komplain+'</td>';
						tableDetail += '<td>'+(detail[i].kategori_approval || '')+'</td>';
						if(status == "Meeting Open"){
							tableDetail += '<td style="background-color:red;color:white">Open</td>';
						}else if(status == "Meeting Close Dengan Revisi"){
							tableDetail += '<td style="background-color:blue;color:white">Close Dengan Revisi</td>';
						}else if(status == "Meeting Close"){
							tableDetail += '<td style="background-color:green;color:white">Close</td>';
						}else if(status == "New CPAR"){
								tableDetail += '<td style="background-color:orange">Belum Meeting</td>';
						}
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

            $('#judul_detail').html('Detail '+status+' Pada Bulan '+bulan+' '+tahun);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status) {
			$('#tableDetail').DataTable().clear();
       $('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < detail.length;i++){

				var str = detail[i].kategori_meeting;
			
				if (str) {
					if (str.indexOf(status) >= 0) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+detail[i].cpar_no+'</td>';
						tableDetail += '<td>'+detail[i].judul_komplain+'</td>';
						tableDetail += '<td>'+detail[i].department_name+'</td>';
						tableDetail += '<td>'+detail[i].kategori+' - '+detail[i].kategori_komplain+'</td>';
						tableDetail += '<td>'+(detail[i].kategori_approval || '')+'</td>';
						if(str == "CloseRevised"){
							tableDetail += '<td style="background-color:blue;color:white">Close Dengan Revisi</td>';
						}else if(str == "Close"){
							tableDetail += '<td style="background-color:green;color:white">Close</td>';
						}else if(str == "Open"){
							tableDetail += '<td style="background-color:red;color:white">Open</td>';
						}
						tableDetail += '</tr>';
					}
				}else{
						if(status == "new_cpar") {
							
							tableDetail += '<tr>';
							tableDetail += '<td>'+detail[i].cpar_no+'</td>';
							tableDetail += '<td>'+detail[i].judul_komplain+'</td>';
							tableDetail += '<td>'+detail[i].department_name+'</td>';
							tableDetail += '<td>'+detail[i].kategori+' - '+detail[i].kategori_komplain+'</td>';
							tableDetail += '<td>'+(detail[i].kategori_approval || '')+'</td>';
							if (status == "new_cpar") {
								tableDetail += '<td style="background-color:orange">Belum Meeting</td>';
							}
							
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

            $('#judul_detail').html('Detail Komplain '+status);
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