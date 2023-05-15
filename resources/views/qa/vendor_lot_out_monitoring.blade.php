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
			<div class="col-xs-7" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
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
			<div class="col-xs-1" style="padding-left: 0;padding-right: 0px">
				<button class="btn btn-default pull-left" onclick="fetchChart()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search
				</button>
			</div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
		</div>
		<div class="col-xs-2">
			<div class="row">
				<div class="col-xs-12" style="padding-right:0;">
					<div class="small-box" style="background: #00af50; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>SUDAH CEK</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b></b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_sudah_cek">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_sudah_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #b02828; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>BELUM CEK</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b></b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_belum_cek">0</h5>
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
			<div id="container" style="height: 77vh;"></div>
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
						        <th style="padding: 5px;text-align: center;width: 1%">Serial Number</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Material</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Qty Check</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Total OK</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Total NG</th>
						        <th style="padding: 5px;text-align: center;width: 1%">NG Ratio</th>
						        <th style="padding: 5px;text-align: center;width: 1%">NG Name</th>
						        <th style="padding: 5px;text-align: center;width: 3%">NG Qty</th>
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
			$('.datepicker').datepicker({
				<?php $tgl_max = date('Y-m-d') ?>
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,	
				endDate: '<?php echo $tgl_max ?>'
			});
			fetchChart();
			setInterval(fetchChart, 1000 * 60 * 5);
			$('.select2').select2({
				allowClear:true
			});
		});


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var true_detail_belum = [];
		var true_detail_sudah = [];

		function fetchChart(){
			var data = {
				date_from:$('#date_from').val(),
				date_to:$('#date_to').val(),
			}
			$.get('{{ url("fetch/qa/display/incoming/vendor/lot_out") }}',data,function(result, status, xhr){
				if(result.status){


					xCategories = [];
					belum_true = [];
					sudah_true = [];
					belum_arisa = [];
					sudah_arisa = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					true_detail_belum = [];
					true_detail_sudah = [];

					arisa_detail_belum = [];
					arisa_detail_sudah = [];

					$.each(result.count, function(key, value){
						xCategories.push(value.check_dates);
						belum_true.push({y:parseInt(value.belum_true)-parseInt(value.sudah_true),key:value.check_date});
						sudah_true.push({y:parseInt(value.sudah_true),key:value.check_date});
						belum_arisa.push({y:parseInt(value.belum_arisa)-parseInt(value.sudah_arisa),key:value.check_date});
						sudah_arisa.push({y:parseInt(value.sudah_arisa),key:value.check_date});
						total_belum = total_belum + (parseInt(value.belum_true)+parseInt(value.belum_arisa))-(parseInt(value.sudah_true)+parseInt(value.sudah_arisa));
						total_sudah = total_sudah + (parseInt(value.sudah_true)+parseInt(value.sudah_arisa));
					});

					total = total_belum+total_sudah;

					$('#total_sudah_cek').html(total_sudah);
					$('#total_belum_cek').html(total_belum);

					if (total_sudah != 0) {
						$('#persen_sudah_cek').html(((total_sudah/total)*100).toFixed(1)+' %');
					}else{
						$('#persen_sudah_cek').html('0 %');
					}
					if (total_belum != 0) {
						$('#persen_belum_cek').html(((total_belum/total)*100).toFixed(1)+' %');
					}else{
						$('#persen_belum_cek').html('0 %');
					}

					$.each(result.data_recheck, function(key2, value2){
						if (value2.vendor_shortname == 'TRUE') {
								true_detail_sudah.push({
								serial_number:value2.serial_number,
								check_date:value2.check_date,
								material_number:value2.material_number,
								material_description:value2.material_description,
								vendor:value2.vendor,
								vendor_shortname:value2.vendor_shortname,
								hpl:value2.hpl,
								qty_check:value2.qty_check,
								total_ok:value2.total_ok,
								total_ng:value2.total_ng,
								ng_ratio:value2.ng_ratio,
								ng_name:value2.ng_name,
								ng_qty:value2.ng_qty,
								lot_status:value2.lot_status,
								inspector:value2.inspector,
							});
						}else if (value2.vendor_shortname == 'ARISA') {
								arisa_detail_sudah.push({
								serial_number:value2.serial_number,
								check_date:value2.check_date,
								material_number:value2.material_number,
								material_description:value2.material_description,
								vendor:value2.vendor,
								vendor_shortname:value2.vendor_shortname,
								hpl:value2.hpl,
								qty_check:value2.qty_check,
								total_ok:value2.total_ok,
								total_ng:value2.total_ng,
								ng_ratio:value2.ng_ratio,
								ng_name:value2.ng_name,
								ng_qty:value2.ng_qty,
								lot_status:value2.lot_status,
								inspector:value2.inspector,
							});
						}
					});

					$.each(result.datas, function(key2, value2){
						if (value2.vendor_shortname == 'TRUE') {
								true_detail_belum.push({
								serial_number:value2.serial_number,
								check_date:value2.check_date,
								material_number:value2.material_number,
								material_description:value2.material_description,
								vendor:value2.vendor,
								vendor_shortname:value2.vendor_shortname,
								hpl:value2.hpl,
								qty_check:value2.qty_check,
								total_ok:value2.total_ok,
								total_ng:value2.total_ng,
								ng_ratio:value2.ng_ratio,
								ng_name:value2.ng_name,
								ng_qty:value2.ng_qty,
								lot_status:value2.lot_status,
								inspector:value2.inspector,
							});
						}else if (value2.vendor_shortname == 'ARISA') {
								arisa_detail_belum.push({
								serial_number:value2.serial_number,
								check_date:value2.check_date,
								material_number:value2.material_number,
								material_description:value2.material_description,
								vendor:value2.vendor,
								vendor_shortname:value2.vendor_shortname,
								hpl:value2.hpl,
								qty_check:value2.qty_check,
								total_ok:value2.total_ok,
								total_ng:value2.total_ng,
								ng_ratio:value2.ng_ratio,
								ng_name:value2.ng_name,
								ng_qty:value2.ng_qty,
								lot_status:value2.lot_status,
								inspector:value2.inspector,
							});
						}
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
							},
							reversed : true
						},	
					    title: {
					        text: 'LOT OUT MONITORING',
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
									// format: '{point.y}',
									style:{
										fontSize: '1vw'
									},
									formatter: function() {
							            if (this.y > 0) {
							              return this.y;
							            }
							          }
								},
								animation: false,
								// pointPadding: 0.93,
								// groupPadding: 0.93,
								// borderWidth: 0.93,
								cursor: 'pointer',
								depth:25,
								stacking: 'normal'
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [{
							type: 'column',
							data: belum_true,
							name: 'Belum TRUE',
							colorByPoint: false,
							color:'#f44336',
							stack: 'male'
						},{
							type: 'column',
							data: belum_arisa,
							name: 'Belum ARISA',
							colorByPoint: false,
							color:'#3672f4',
							stack: 'male'
						},{
							type: 'column',
							data: sudah_true,
							name: 'Sudah TRUE',
							colorByPoint: false,
							color:'#32a852',
							stack: 'female'
						},{
							type: 'column',
							data: sudah_arisa,
							name: 'Sudah ARISA',
							stacking:true,
							colorByPoint: false,
							color:'#a86932',
							stack: 'female'
						}
						]
					});
					$("#periode").html('PERIODE '+result.periode);
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

		function ShowModal(date_name,status_cek,date) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			if (status_cek.split(' ')[1] == 'TRUE') {
				if (status_cek.split(' ')[0] == 'Belum') {
					for(var i = 0; i < true_detail_belum.length;i++){
						if (true_detail_belum[i].recheck_status == null) {
							if (true_detail_belum[i].check_date === date) {
								tableDetail += '<tr>';
								tableDetail += '<td style="color:red;font-weight:bold">'+true_detail_belum[i].serial_number+'</td>';
								tableDetail += '<td>'+true_detail_belum[i].material_number+' - '+true_detail_belum[i].material_description+'</td>';
								tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].qty_check || '')+'</td>';
								tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].total_ok || '')+'</td>';
								tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].total_ng || '')+'</td>';
								tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].ng_ratio || '')+'</td>';
								tableDetail += '<td>'+(true_detail_belum[i].ng_name || '')+'</td>';
								tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].ng_qty || '')+'</td>';
								tableDetail += '</tr>';
							}
						}
					}
				}else{
					for(var i = 0; i < true_detail_sudah.length;i++){
						if (true_detail_sudah[i].check_date === date) {
							tableDetail += '<tr>';
							tableDetail += '<td style="color:red;font-weight:bold">'+true_detail_sudah[i].serial_number+'</td>';
							tableDetail += '<td>'+true_detail_sudah[i].material_number+' - '+true_detail_sudah[i].material_description+'</td>';
							tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].qty_check || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].total_ok || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].total_ng || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].ng_ratio || '')+'</td>';
							tableDetail += '<td>'+(true_detail_sudah[i].ng_name || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].ng_qty || '')+'</td>';
							tableDetail += '</tr>';
						}
					}
				}
			}else{
				if (status_cek.split(' ')[0] == 'Belum') {
					for(var i = 0; i < arisa_detail_belum.length;i++){
						if (arisa_detail_belum[i].check_date === date) {
							tableDetail += '<tr>';
							tableDetail += '<td style="color:red;font-weight:bold">'+arisa_detail_belum[i].serial_number+'</td>';
							tableDetail += '<td>'+arisa_detail_belum[i].material_number+' - '+arisa_detail_belum[i].material_description+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].qty_check || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].total_ok || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].total_ng || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].ng_ratio || '')+'</td>';
							tableDetail += '<td>'+(arisa_detail_belum[i].ng_name || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].ng_qty || '')+'</td>';
							tableDetail += '</tr>';
						}
					}
				}else{
					for(var i = 0; i < arisa_detail_sudah.length;i++){
						if (arisa_detail_sudah[i].check_date === date) {
							tableDetail += '<tr>';
							tableDetail += '<td style="color:red;font-weight:bold">'+arisa_detail_sudah[i].serial_number+'</td>';
							tableDetail += '<td>'+arisa_detail_sudah[i].material_number+' - '+arisa_detail_sudah[i].material_description+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].qty_check || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].total_ok || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].total_ng || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].ng_ratio || '')+'</td>';
							tableDetail += '<td>'+(arisa_detail_sudah[i].ng_name || '')+'</td>';
							tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].ng_qty || '')+'</td>';
							tableDetail += '</tr>';
						}
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

            $('#judul_weekly').html('Detail '+status_cek+' Recheck Material Periode '+date_name);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status_cek) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			if (status_cek.split(' ')[0] == 'Belum') {
				for(var i = 0; i < true_detail_belum.length;i++){
					tableDetail += '<tr>';
					tableDetail += '<td style="color:red;font-weight:bold">'+true_detail_belum[i].serial_number+'</td>';
					tableDetail += '<td>'+true_detail_belum[i].material_number+' - '+true_detail_belum[i].material_description+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].qty_check || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].total_ok || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].total_ng || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].ng_ratio || '')+'</td>';
					tableDetail += '<td>'+(true_detail_belum[i].ng_name || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_belum[i].ng_qty || '')+'</td>';
					tableDetail += '</tr>';
				}
				for(var i = 0; i < arisa_detail_belum.length;i++){
					tableDetail += '<tr>';
					tableDetail += '<td style="color:red;font-weight:bold">'+arisa_detail_belum[i].serial_number+'</td>';
					tableDetail += '<td>'+arisa_detail_belum[i].material_number+' - '+arisa_detail_belum[i].material_description+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].qty_check || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].total_ok || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].total_ng || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].ng_ratio || '')+'</td>';
					tableDetail += '<td>'+(arisa_detail_belum[i].ng_name || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_belum[i].ng_qty || '')+'</td>';
					tableDetail += '</tr>';
				}
			}else{
				for(var i = 0; i < true_detail_sudah.length;i++){
					tableDetail += '<tr>';
					tableDetail += '<td style="color:red;font-weight:bold">'+true_detail_sudah[i].serial_number+'</td>';
					tableDetail += '<td>'+true_detail_sudah[i].material_number+' - '+true_detail_sudah[i].material_description+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].qty_check || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].total_ok || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].total_ng || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].ng_ratio || '')+'</td>';
					tableDetail += '<td>'+(true_detail_sudah[i].ng_name || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(true_detail_sudah[i].ng_qty || '')+'</td>';
					tableDetail += '</tr>';
				}
				for(var i = 0; i < arisa_detail_sudah.length;i++){
					tableDetail += '<tr>';
					tableDetail += '<td style="color:red;font-weight:bold">'+arisa_detail_sudah[i].serial_number+'</td>';
					tableDetail += '<td>'+arisa_detail_sudah[i].material_number+' - '+arisa_detail_sudah[i].material_description+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].qty_check || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].total_ok || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].total_ng || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].ng_ratio || '')+'</td>';
					tableDetail += '<td>'+(arisa_detail_sudah[i].ng_name || '')+'</td>';
					tableDetail += '<td style="text-align:right">'+(arisa_detail_sudah[i].ng_qty || '')+'</td>';
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

            $('#judul_weekly').html('Detail '+status_cek+' Menyetujui Surat Pernyataan PKB<br>Periode '+periode);
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