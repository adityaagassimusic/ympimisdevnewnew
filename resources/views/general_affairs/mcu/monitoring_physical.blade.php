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
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span></center>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;padding-right: 10px">
					<select style="width: 100%;text-align: left;" class="form-control select2" id="mcu_periode" data-placeholder="Pilih Periode MCU">
						<option value=""></option>
						@foreach($mcu_periode as $mcu_periode)
							<option value="{{$mcu_periode->periode}}">{{$mcu_periode->periode}}</option>
						@endforeach
					</select>
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
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>身体診断済み</b></h3>
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
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>未身体診断</b></h3>
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
		<div class="col-xs-12" style="padding-bottom: 5px;padding-top: 10px">
			<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;background-color: rgb(126,86,134);height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;font-weight: bold;">Medical Check Up Code</span></center>
			</div>
		</div>
		<div class="col-xs-12">
			<div id="container2" style="height: 77vh;"></div>
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
						        <th style="padding: 5px;text-align: center;width: 1%">Status</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Schedule</th>
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

	  <div class="modal fade" id="modalDetailCode" style="color: black;z-index: 10000;">
	      <div class="modal-dialog modal-lg" style="width: 1200px">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_weekly_code"></h4>
	          </div>
	          <div class="modal-body">
	            <div class="row">
	              <div class="col-md-12" id="data-activity">
	             	<table id="tableDetailCode" class="table table-striped table-bordered" style="width: 100%;">
				        <thead>
					        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
						        <th style="padding: 5px;text-align: center;width: 1%">NIK</th>
						        <th style="padding: 5px;text-align: center;width: 2%">Nama</th>
						        <th style="padding: 5px;text-align: center;width: 1%">Dept</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Sect</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Group</th>
						        <th style="padding: 5px;text-align: center;width: 3%">Sub Group</th>
						        <th style="padding: 5px;text-align: center;width: 1%">MCU Code</th>
					        </tr>
				        </thead>
				        <tbody id="bodyTableDetailCode">
				        	
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
			setInterval(fetchChart, 300000);
			$('.select2').select2({
				allowClear:true
			});
		});


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var mcu_detail = [];
		var mcu_detail_code = [];
		var periode = '';

		function fetchChart(){
			$('#loading').show();
			var data = {
				mcu_periode:$('#mcu_periode').val()
			}
			$.get('{{ url("fetch/ga_control/mcu/monitoring/physical") }}', data,function(result, status, xhr){
				if(result.status){


					xCategories = [];
					belum = [];
					sudah = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					mcu_detail = [];
					periode = '';

					for(var i = 0; i < result.department.length;i++){
						var count_sudah = 0;
						var count_belum = 0;
						for(var j = 0; j < result.mcus.length;j++){
							for(var k = 0; k < result.employees.length;k++){
								if (result.mcus[j].employee_id == result.employees[k].employee_id && result.employees[k].department == result.department[i].department_name) {
									if (result.mcus[j].doctor_status == 'Done') {
										count_sudah++;
										total_sudah++;
										mcu_detail.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											status_cek:'Sudah',
										});
									}else{
										count_belum++;
										total_belum++;
										mcu_detail.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											status_cek:'Belum',
										});
									}
								}
							}
						}
						sudah.push({y:parseInt(count_sudah),key:result.department[i].department_name});
						belum.push({y:parseInt(count_belum),key:result.department[i].department_name});
						xCategories.push(result.department[i].department_shortname);
					}

					total = total_belum+total_sudah;

					$('#total_sudah_cek').html(total_sudah+' <span style="font-size:2.4vw"> 人</span>');
					$('#total_belum_cek').html(total_belum+' <span style="font-size:2.4vw"> 人</span>');
					$('#persen_sudah_cek').html(((total_sudah/total)*100).toFixed(2)+' %');
					$('#persen_belum_cek').html(((total_belum/total)*100).toFixed(2)+' %');

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
							reversed : true
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

					xCategories2 = [];
					mcu = [];

					mcu_detail_code = [];

					var count_a = 0;
					var count_b = 0;
					var count_c = 0;
					var count_d = 0;
					var count_e = 0;
					var count_f = 0;
					var count_g = 0;
					var count_no = 0;

					for(var i = 0; i < result.department.length;i++){
						for(var j = 0; j < result.mcus.length;j++){
							for(var k = 0; k < result.employees.length;k++){
								if (result.mcus[j].employee_id == result.employees[k].employee_id && result.employees[k].department == result.department[i].department_name) {
									if (result.mcus[j].mcu_group_code == 'A') {
										count_a++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'B') {
										count_b++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'C') {
										count_c++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'D') {
										count_d++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'E') {
										count_e++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'F') {
										count_f++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'G') {
										count_g++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}else if (result.mcus[j].mcu_group_code == 'Tidak Perlu') {
										count_no++;
										mcu_detail_code.push({
											employee_id:result.mcus[j].employee_id,
											name:result.mcus[j].name,
											schedule_date:result.mcus[j].schedule_date,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											mcu_group_code:result.mcus[j].mcu_group_code,
										});
									}
								}
							}
						}
					}
					mcu.push({y:count_a,key:'A'});
					mcu.push({y:count_b,key:'B'});
					mcu.push({y:count_c,key:'C'});
					mcu.push({y:count_d,key:'D'});
					mcu.push({y:count_e,key:'E'});
					mcu.push({y:count_f,key:'F'});
					mcu.push({y:count_g,key:'G'});
					mcu.push({y:count_no,key:'Tidak Perlu'});
					xCategories2.push('A');
					xCategories2.push('B');
					xCategories2.push('C');
					xCategories2.push('D');
					xCategories2.push('E');
					xCategories2.push('F');
					xCategories2.push('G');
					xCategories2.push('Tidak Perlu');

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
					    xAxis: {
							categories: xCategories2,
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
						// tooltip: {
						// 	headerFormat: '<span>{series.name}</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name}{point.key}</span>: <b>{point.y}</b><br/>',
						// },
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
											ShowModalCode(this.category);
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
					 //    {
						// 	type: 'column',
						// 	data: belum,
						// 	name: 'Belum',
						// 	colorByPoint: false,
						// 	color:'#f44336'
						// },
						{
							type: 'column',
							data: mcu,
							name: 'MCU Code',
							colorByPoint: false,
							color:'#32a852'
						}
						]
					});
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
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
			for(var i = 0; i < mcu_detail.length;i++){
				if (mcu_detail[i].department === department) {
					if (mcu_detail[i].status_cek == status_cek) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+mcu_detail[i].employee_id+'</td>';
						tableDetail += '<td>'+mcu_detail[i].name+'</td>';
						tableDetail += '<td>'+(mcu_detail[i].department_shortname || '')+'</td>';
						tableDetail += '<td>'+(mcu_detail[i].section || '')+'</td>';
						tableDetail += '<td>'+(mcu_detail[i].group || '')+'</td>';
						tableDetail += '<td>'+(mcu_detail[i].sub_group || '')+'</td>';
						tableDetail += '<td>'+mcu_detail[i].status_cek+'</td>';
						tableDetail += '<td style="text-align:right">'+mcu_detail[i].schedule_date+'</td>';
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

            $('#judul_weekly').html('Detail '+status_cek+' Melakukan Cek Fisik<br>'+(department || 'Management')+'<br>Periode '+periode);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status_cek) {
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < mcu_detail.length;i++){
				if (mcu_detail[i].status_cek == status_cek) {
					tableDetail += '<tr>';
					tableDetail += '<td>'+mcu_detail[i].employee_id+'</td>';
					tableDetail += '<td>'+mcu_detail[i].name+'</td>';
					tableDetail += '<td>'+(mcu_detail[i].department_shortname || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail[i].section || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail[i].group || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail[i].sub_group || '')+'</td>';
					tableDetail += '<td>'+mcu_detail[i].status_cek+'</td>';
					tableDetail += '<td style="text-align:right">'+mcu_detail[i].schedule_date+'</td>';
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

            $('#judul_weekly').html('Detail '+status_cek+' Melakukan Cek Fisik<br>Periode '+periode);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalCode(mcu_code) {
			$('#tableDetailCode').DataTable().clear();
        	$('#tableDetailCode').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetailCode').html('');
			var tableDetail = '';
			for(var i = 0; i < mcu_detail_code.length;i++){
				if (mcu_detail_code[i].mcu_group_code === mcu_code) {
					tableDetail += '<tr>';
					tableDetail += '<td>'+mcu_detail_code[i].employee_id+'</td>';
					tableDetail += '<td>'+mcu_detail_code[i].name+'</td>';
					tableDetail += '<td>'+(mcu_detail_code[i].department_shortname || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail_code[i].section || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail_code[i].group || '')+'</td>';
					tableDetail += '<td>'+(mcu_detail_code[i].sub_group || '')+'</td>';
					tableDetail += '<td>'+mcu_detail_code[i].mcu_group_code+'</td>';
					tableDetail += '</tr>';
				}
			}
			$('#bodyTableDetailCode').append(tableDetail);
			$('#tableDetailCode').DataTable({
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

            $('#judul_weekly_code').html('Detail Karyawan Kode MCU '+mcu_code);
			$('#modalDetailCode').modal('show');
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