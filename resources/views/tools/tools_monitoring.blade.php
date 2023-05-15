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

div.dataTables_wrapper div.dataTables_filter label{
  color: white;
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
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">Monitoring Order Tools ツール発注のモニタリング</span></center>
			</div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
		</div>

		<div class="col-xs-10" style="padding-left: 10px">
			<div class="col-xs-12" style="padding:0">
				<div id="container" style="height: 50vh;"></div>
			</div>
			<div class="col-xs-12" style="padding:0">
				<div class="row" style="padding-left: 15px;padding-right: 15px;">
					<table class="table table-bordered" id="tableOutstanding" style="width: 100%;color: white">
						<thead style="font-size: 12px;font-weight: bold">
							<tr style="background-color: #2d2d2e;border-bottom: 3px solid white;border-top: 1px solid #2d2d2e">
								<th style="vertical-align: middle;">Item Code</th>
								<th style="vertical-align: middle;">Description</th>
								<th style="vertical-align: middle;">Rack Code</th>
								<th style="vertical-align: middle;">Location</th>
								<th style="vertical-align: middle;">Group</th>
								<th style="vertical-align: middle;">Qty</th>
								<th style="vertical-align: middle;">Status</th>
							</tr>
						</thead>
						<tbody id="bodyTableOutstanding">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<div class="row">
				<div class="col-xs-12" style="padding-right:20px;">

					<div class="small-box" style="background: #b02828; height: 17vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('waiting_order')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>Waiting Order</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.6vw;color: white;"><b>発注待ち</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_waiting_order_cek">0</h5>
						</div>
						<div class="icon" style="padding-top: 35px;padding-right: 10px;font-size: 10vh;">
							<i class="fa fa-clock-o"></i>
						</div>
					</div>


					<div class="small-box" style="background: #f57f17; height: 17vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('order')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>Approval PR</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.6vw;color: white;"><b>PR承認</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_pr_approval">0</h5>
						</div>
						<div class="icon" style="padding-top: 35px;padding-right: 10px;font-size: 10vh;">
							<i class="fa fa-check-circle-o"></i>
						</div>
					</div>

					<div class="small-box" style="background: #01579b; height: 17vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('order')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>PO Confirmed</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.6vw;color: white;"><b>PO確認済み</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_po_confirmed">0</h5>
						</div>
						<div class="icon" style="padding-top: 35px;padding-right: 10px;font-size: 10vh;">
							<i class="fa fa-shopping-cart"></i>
						</div>
					</div>

					<div class="small-box" style="background: #00af50; height: 17vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('order')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>Received</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.6vw;color: white;"><b>倉庫受入済み</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_received">0</h5>
						</div>
						<div class="icon" style="padding-top: 35px;padding-right: 10px;font-size: 10vh;">
							<i class="fa fa-truck"></i>
						</div>
					</div>

				</div>
			</div>
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
						        <th style="padding: 5px;text-align: center;width: 1%">Rack Code</th>
						        <th style="padding: 5px;text-align: left;width: 4%">Tools</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Group</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Category</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Status Order</th>
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

		var tools_order = [];

		function fetchChart(){
			$.get('{{ url("fetch/tools/monitoring") }}',function(result, status, xhr){
				if(result.status){

					loc = [];
					waiting_order = [];
					pr_approval = [];
					po_confirmed = [];
					received = [];

					tools_order = [];

					var total_waiting_order = 0;
					var total_pr_approval = 0;
					var total_po_confirmed = 0;
					var total_received = 0;

					$.each(result.tools_resume, function(key, value){
						loc.push(value.location);
						waiting_order.push(parseInt(value.waiting_order));
						pr_approval.push(parseInt(value.pr_approval));
						po_confirmed.push(parseInt(value.po_confirmed));
						received.push(parseInt(value.received));

						total_waiting_order = total_waiting_order + parseInt(value.waiting_order);
						total_pr_approval = total_pr_approval + parseInt(value.pr_approval);
						total_po_confirmed = total_po_confirmed + parseInt(value.po_confirmed);
						total_received = total_received + parseInt(value.received);

					});

					$('#total_waiting_order_cek').html(total_waiting_order+' <span style="font-size:2.4vw"></span>');
					$('#total_pr_approval').html(total_pr_approval+' <span style="font-size:2.4vw"></span>');
					$('#total_po_confirmed').html(total_po_confirmed+' <span style="font-size:2.4vw"></span>');
					$('#total_received').html(total_received+' <span style="font-size:2.4vw"></span>');

					$.each(result.tools_order, function(key2, value2){
						tools_order.push({
							rack_code:value2.rack_code,
							item_code:value2.item_code,
							description:value2.description,
							location:value2.location,
							group:value2.group,
							category:value2.kategori,
							quantity_order:value2.qty,
							status:value2.status
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
							categories: loc,
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
					        series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,this.series.name);
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
								// pointPadding: 0.93,
								// groupPadding: 0.93,
								// borderWidth: 0.93,
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [
					    {
								type: 'column',
								data: waiting_order,
								name: 'Waiting Order',
								colorByPoint: false,
								color:'#f44336'
							},
							{
								type: 'column',
								data: pr_approval,
								name: 'Approval PR',
								colorByPoint: false,
								color:'#f57f17'
							},{
								type: 'column',
								data: po_confirmed,
								name: 'PO Confirmed',
								colorByPoint: false,
								color:'#01579b'
							},{
								type: 'column',
								data: received,
								name: 'Received',
								colorByPoint: false,
								color:'#00af50'
							}
						]
					});

				}
				else{
					alert('Attempt to retrieve data failed.');
				}


        $('#tableOutstanding').DataTable().clear();
        $('#tableOutstanding').DataTable().destroy();
				$('#bodyTableOutstanding').empty();
				var outstanding = "";
				var index = 1;
				$.each(result.tools_order, function(key, value) {
					outstanding += '<tr>';
					outstanding += '<td width="5%">'+value.item_code+'</td>';
					outstanding += '<td width="25%">'+value.description+'</td>';
					outstanding += '<td width="5%">'+value.rack_code+'</td>';
					outstanding += '<td width="10%">'+value.location+'</td>';
					outstanding += '<td width="10%">'+value.group+'</td>';
					outstanding += '<td width="5%">'+value.qty+'</td>';
					if (value.status == "waiting_order") {
						outstanding += '<td width="5%" style="background-color:#b02828">Waiting Order</td>';
					} else if (value.status == "pr_approval") {
						outstanding += '<td width="5%" style="background-color:#f57f17">Approval PR</td>';
					} else if (value.status == "po_confirmed") {
						outstanding += '<td width="5%" style="background-color:#01579b">Konfirmasi PO</td>';
					} else if (value.status == "received") {
						outstanding += '<td width="5%" style="background-color:#00af50">Diterima Gudang</td>';
					}
					outstanding += '</tr>';

					index++;
				});

				$('#bodyTableOutstanding').append(outstanding);

				var table = $('#tableOutstanding').DataTable({
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'paging': false,
          'lengthChange': true,
          'pageLength': 10,
          'searching': true ,
          'ordering': false,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

			});
		}

		function ShowModal(location,status) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < tools_order.length;i++){
				if (tools_order[i].location === location) {
					var stat = "";
					if (status == "Waiting Order") {
						stat = 'waiting_order';
					}else if(status == "Approval PR") {
						stat = 'pr_approval';
					}else if(status == "PO Confirmed") {
						stat = 'po_confirmed';
					}else if(status == "Received") {
						stat = 'received';
					}

					if (tools_order[i].status === stat) {
						tableDetail += '<tr>';
						tableDetail += '<td width="5%">'+tools_order[i].rack_code+'</td>';
						tableDetail += '<td width="25%">'+tools_order[i].item_code+' '+tools_order[i].description+'</td>';
						tableDetail += '<td width="10%">'+(tools_order[i].group)+'</td>';
						tableDetail += '<td width="5%">'+(tools_order[i].category || '')+'</td>';
						tableDetail += '<td width="5%">'+tools_order[i].quantity_order+'</td>';
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

            $('#judul_detail').html('Detail '+status+' Pada Lokasi '+(location));
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < tools_order.length;i++){
				if (tools_order[i].status === status) {
					tableDetail += '<tr>';
					tableDetail += '<td>'+tools_order[i].rack_code+'</td>';
					tableDetail += '<td>'+tools_order[i].item_code+' '+tools_order[i].description+'</td>';
					tableDetail += '<td>'+(tools_order[i].group)+'</td>';
					tableDetail += '<td>'+(tools_order[i].category || '')+'</td>';
					if (tools_order[i].status == "waiting_order") {
						tableDetail += '<td style="background-color:red">'+tools_order[i].quantity_order+'</td>';
					}else if(tools_order[i].status == "pr_approval"){
						tableDetail += '<td style="background-color:green">'+tools_order[i].quantity_order+'</td>';
					}
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

            $('#judul_detail').html('Detail Tools '+status);
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