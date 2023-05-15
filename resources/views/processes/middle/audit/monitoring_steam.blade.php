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
			<div class="col-xs-9" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="month_name"></span></center>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;padding-right: 10px">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control" id="month" name="month" placeholder="Select Month">
				</div>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 0px">
				<button class="btn btn-default pull-left" onclick="fetchChart()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search
				</button>
			</div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
		</div>
		<!-- <div class="col-xs-2">
			<div class="row">
				<div class="col-xs-12" style="padding-right:0;">
					<div class="small-box" style="background: #00af50; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>SUDAH MENGISI</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>記入済</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_sudah_cek">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_sudah_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #b02828; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>BELUM MENGISI</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>未記入</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_belum_cek">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_belum_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="col-xs-12" style="padding-left: 5px">
			<div id="container" style="height: 44vh;"></div>
		</div>
		<div class="col-xs-12" style="padding-left: 5px">
			<div id="container2" style="height: 44vh;"></div>
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
						        <th style="padding: 5px;text-align: center;width: 1%">Leader</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Location</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Condition</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Status</th>
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

	  <div class="modal fade" id="modalDetailTemuan" style="color: black;z-index: 10000;">
	      <div class="modal-dialog modal-lg" style="width: 1200px">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_weekly_temuan"></h4>
	          </div>
	          <div class="modal-body">
	            <div class="row">
	              <div class="col-md-12" id="data-activity">
	             	<table id="tableDetailTemuan" class="table table-striped table-bordered" style="width: 100%;">
				        <thead>
					        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
						        <th style="padding: 5px;text-align: center;width: 1%">Leader</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Location</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Point</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Condition</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Evidence</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Note</th>
					        </tr>
				        </thead>
				        <tbody id="bodyTableDetailTemuan">
				        	
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
			$('#month').datepicker({
				autoclose: true,
			    format: "yyyy-mm",
			    todayHighlight: true,
			    startView: "months", 
			    minViewMode: "months",
			    autoclose: true,
			});
			fetchChart();
			setInterval(fetchChart, 1000 * 60 * 5);
			$('.select2').select2({
				allowClear:true
			});
		});


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var audit_detail = null;
		var audit_belum = null;
		var audit_temuan = null;
		var monthTitle = '';

		function fetchChart(){
			var data = {
				month:$('#month').val()
			}
			$.get('{{ url("fetch/maintenance/steam/monitoring") }}', data,function(result, status, xhr){
				if(result.status){


					xCategories = [];
					belum = [];
					sudah = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					audit_detail = null;
					audit_detail = result.audit_all;
					audit_belum = null;
					audit_belum = result.activity;
					audit_temuan = null;
					audit_temuan = result.temuan_all;
					monthTitle = '';
					for(var i = 0; i < result.date_all.length;i++){
						xCategories.push(result.date_all[i].date_name);
						var sudahs = 0;
						if (result.audit_all[i].length > 0) {
							sudahs = sudahs + result.audit_all[i].length;
						}
						sudah.push({y:parseInt(sudahs),key:result.date_all[i].week_date});
						belum.push({y:parseInt(result.activity.length)-parseInt(sudahs),key:result.date_all[i].week_date});
					}

					// $.each(, function(key, value){
						
					// 	belum.push({y:parseInt(value.belum),key:value.department});
					// 	total_belum = total_belum + parseInt(value.belum);
						
					// 	total_sudah = total_sudah + parseInt(value.sudah);
					// });

					// total = total_belum+total_sudah;

					// $('#total_sudah_cek').html(total_sudah+' <span style="font-size:2.4vw"> 人</span>');
					// $('#total_belum_cek').html(total_belum+' <span style="font-size:2.4vw"> 人</span>');
					// $('#persen_sudah_cek').html(((total_sudah/total)*100).toFixed(1)+' %');
					// $('#persen_belum_cek').html(((total_belum/total)*100).toFixed(1)+' %');

					// $.each(result.pkb_detail, function(key2, value2){
					// 	pkb_detail.push({
					// 		employee_id:value2.employee_id,
					// 		name:value2.name,
					// 		department_shortname:value2.department_shortname,
					// 		department:value2.department,
					// 		section:value2.section,
					// 		group:value2.group,
					// 		sub_group:value2.sub_group,
					// 		status_cek:value2.status_cek,
					// 	});
					// });

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
					    title:{
					    	text:'RESUME AUDIT',
					    	style:{
					    		fontSize:'18px',
					    		fontWeight:'bold'
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
							// opposite: true
						}
						],
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
								fontSize:'12px',
							},
							reversed : true
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
											ShowModal(this.category,this.series.name,this.options.key);
										}
									}
								},
								stacking:'normal',
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
					    series: [{
							type: 'column',
							data: belum,
							name: 'Belum',
							colorByPoint: false,
							color:'#f44336',
							
						},{
							type: 'column',
							data: sudah,
							name: 'Sudah',
							colorByPoint: false,
							color:'#32a852'
							// stacking:true,
						}
						]
					});

					xCategories = [];
					temuan = [];

					// audit_detail = null;
					// audit_detail = result.audit_all;
					// audit_belum = null;
					// audit_belum = result.activity;
					// monthTitle = '';
					for(var i = 0; i < result.date_all.length;i++){
						xCategories.push(result.date_all[i].date_name);
						var temuans = 0;
						if (result.temuan_all[i].length > 0) {
							temuans = temuans + result.temuan_all[i].length;
						}
						temuan.push({y:parseInt(temuans),key:result.date_all[i].week_date});
					}

					const chart2 = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container2',
					        type: 'column',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        }
					    },
					    title:{
					    	text:'TREND TEMUAN',
					    	style:{
					    		fontSize:'18px',
					    		fontWeight:'bold'
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
							// opposite: true
						}
						],
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
								fontSize:'12px',
							},
							reversed : true
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
											ShowModalTemuan(this.category,this.series.name,this.options.key);
										}
									}
								},
								stacking:'normal',
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
					    series: [{
							type: 'column',
							data: temuan,
							name: 'Temuan',
							colorByPoint: false,
							color:'#f4c136',
							
						}
						]
					});

					// Highcharts.chart('container', {
					// 	chart: {
					// 		type: 'column'
					// 	},
					// 	title: {
					// 		text: '',
					// 		// style: {
					// 		// 	fontSize: '20px',
					// 		// 	fontWeight: 'bold'
					// 		// }
					// 	},
					// 	xAxis: {
					// 		categories: xCategories,
					// 		type: 'category',
					// 		gridLineWidth: 1,
					// 		gridLineColor: 'RGB(204,255,255)',
					// 		lineWidth:1,
					// 		lineColor:'#9e9e9e',
					// 		labels: {
					// 			style: {
					// 				fontSize: '13px'
					// 			}
					// 		},
					// 	},
					// 	yAxis: [{
					// 		title: {
					// 			text: 'Total Data',
					// 			style: {
					// 				color: '#eee',
					// 				fontSize: '15px',
					// 				fontWeight: 'bold',
					// 				fill: '#6d869f'
					// 			}
					// 		},
					// 		labels:{
					// 			style:{
					// 				fontSize:"15px"
					// 			}
					// 		},
					// 		type: 'linear',
							// opposite: true
					// 	}
					// 	],
					// 	tooltip: {
					// 		headerFormat: '<span>{series.name}</span><br/>',
					// 		pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					// 	},
					// 	legend: {
					// 		layout: 'horizontal',
					// 		backgroundColor:
					// 		Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
					// 		itemStyle: {
					// 			fontSize:'12px',
					// 		},
					// 		reversed : true
					// 	},	
					// 	plotOptions: {
					// 		series:{
					// 			cursor: 'pointer',
					// 			point: {
					// 				events: {
					// 					click: function () {
					// 						ShowModal(this.category,this.series.name,this.options.key);
					// 					}
					// 				}
					// 			},
					// 			animation: false,
					// 			dataLabels: {
					// 				enabled: true,
					// 				format: '{point.y}',
					// 				style:{
					// 					fontSize: '1vw'
					// 				}
					// 			},
					// 			animation: false,
					// 			pointPadding: 0.93,
					// 			groupPadding: 0.93,
					// 			borderWidth: 0.93,
					// 			cursor: 'pointer',
					// 		},
					// 	},credits: {
					// 		enabled: false
					// 	},
					// 	series: [{
					// 		type: 'column',
					// 		data: belum,
					// 		name: 'Belum',
					// 		colorByPoint: false,
					// 		color:'#f44336'
					// 	},{
					// 		type: 'column',
					// 		data: sudah,
					// 		name: 'Sudah',
					// 		colorByPoint: false,
					// 		color:'#32a852'
					// 	}
					// 	]
					// });
					monthTitle = result.monthTitle;
					$("#month_name").html('BULAN '+result.monthTitle.toUpperCase());
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

		function ShowModal(date_name,status,date) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			var audit_id = [];
			var audit_loc = [];
			for(var i = 0; i < audit_detail.length;i++){
				if (audit_detail[i].length > 0) {
					if (audit_detail[i][0].date == date) {
						for(var j = 0; j < audit_detail[i].length;j++){
							audit_id.push(audit_detail[i][j].activity_list_id);
						}
					}
				}
			}
			if (status == 'Sudah') {
				for(var i = 0; i < audit_detail.length;i++){
					if (audit_detail[i].length > 0) {
						if (audit_detail[i][0].date == date) {
							for(var j = 0; j < audit_detail[i].length;j++){
								tableDetail += '<tr>';
								tableDetail += '<td>'+audit_detail[i][j].auditor_name+'</td>';
								tableDetail += '<td>'+audit_detail[i][j].location+'</td>';
								if (audit_detail[i][j].condition.match(/NG/gi)) {
									tableDetail += '<td style="background-color:#ffc4c4">NG</td>';
								}else{
									tableDetail += '<td style="background-color:#e7ffa6">OK</td>';
								}
								tableDetail += '<td>Sudah Dikerjakan</td>';
								tableDetail += '</tr>';
							}
						}
					}
				}
			}else{
				for(var i = 0; i < audit_belum.length;i++){
					if (!audit_id.includes(audit_belum[i].id)) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+audit_belum[i].leader_dept+'</td>';
						tableDetail += '<td></td>';
						tableDetail += '<td></td>';
						tableDetail += '<td>Belum Dikerjakan</td>';
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

            $('#judul_weekly').html('Detail '+status+' Mengerjakan Tanggal '+date_name);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalTemuan(date_name,status,date) {
			$('#tableDetailTemuan').DataTable().clear();
        	$('#tableDetailTemuan').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetailTemuan').html('');
			var tableDetail = '';
			for(var i = 0; i < audit_temuan.length;i++){
				if (audit_temuan[i].length > 0) {
					if (audit_temuan[i][0].date == date) {
						for(var j = 0; j < audit_temuan[i].length;j++){
							tableDetail += '<tr>';
							tableDetail += '<td>'+audit_temuan[i][j].auditor_name+'</td>';
							tableDetail += '<td>'+audit_temuan[i][j].location+'</td>';
							tableDetail += '<td>'+audit_temuan[i][j].point_check+'</td>';
							if (audit_temuan[i][j].condition.match(/NG/gi)) {
								tableDetail += '<td style="background-color:#ffc4c4">NG</td>';
							}else{
								tableDetail += '<td style="background-color:#e7ffa6">OK</td>';
							}
							var url = '{{url("data_file/daily_audit/steam/")}}/'+audit_temuan[i][j].evidence;
							tableDetail += '<td><img src="'+url+'" style="width:100px"></td>';
							tableDetail += '<td>'+(audit_temuan[i][j].note || '')+'</td>';
							tableDetail += '</tr>';
						}
					}
				}
			}
			$('#bodyTableDetailTemuan').append(tableDetail);
			$('#tableDetailTemuan').DataTable({
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

            $('#judul_weekly_temuan').html('Detail Temuan Tanggal '+date_name);
			$('#modalDetailTemuan').modal('show');
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