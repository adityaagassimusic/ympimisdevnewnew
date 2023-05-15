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
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 5px;padding-bottom: 10px;">
			<div class="col-xs-3" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span></center>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select id="periode_select" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Periode">
					<option value=""></option>
					@foreach($periode as $per)
					<option value="{{$per->periode}}">{{$per->periode}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 5px">
				<button class="btn btn-default pull-left" onclick="fetchChart()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search
				</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px">
				<a class="btn btn-info pull-right" href="{{ url('index/sga/assessment') }}" style="margin-left: 5px;">
					<i class="fa fa-pencil"></i> Assessment
				</a>
				@if($username == 'PI0904007' || $username == 'PI1910002')
				<a class="btn btn-success pull-right" href="{{ url('index/sga/report') }}" style="margin-left: 5px;">
					<i class="fa fa-book"></i> Report
				</a>
				<a class="btn btn-primary pull-right" href="{{ url('index/sga/master') }}" style="margin-left: 5px;">
					<i class="fa fa-list"></i> Master
				</a>
				<a class="btn btn-default pull-right" href="{{ url('index/sga/point_check') }}" style="margin-left: 5px;">
					<i class="fa fa-check-square-o"></i> Point Check
				</a>
				@endif
			</div>
		</div>
		<!-- <div class="col-xs-2">
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
		</div> -->
		<div class="col-xs-12" style="">
			<div id="container" style="height: 52vh;"></div>
		</div>
		<div class="col-xs-12" style="" id="divTop">
			<span style="font-weight: bold;font-size: 18px;color: white"><i class="fa fa-trophy fa-2x" style="font-size: 18px"></i>&nbsp;&nbsp;Top 5 SGA Score <small>上位５のSGAの得点</small></span>
			<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
		        <thead>
			        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;font-size:15px">
			        	<th style="padding: 2px;text-align: center;width: 1%">#</th>
				        <th style="padding: 2px;text-align: center;width: 3%">Team No. <small>チーム番号</small></th>
				        <th style="padding: 2px;text-align: center;width: 3%">Team Name  <small>チーム名</small></th>
				        <th style="padding: 2px;text-align: center;width: 5%">Title <small>件名</small></th>
				        <th style="padding: 2px;text-align: center;width: 2%">Seleksi <small>予選得点</small></th>
				        <th style="padding: 2px;text-align: center;width: 2%">Final <small>決勝得点</small></th>
				        <th style="padding: 2px;text-align: center;width: 2%">40% Seleksi <small>40％予選</small></th>
				        <th style="padding: 2px;text-align: center;width: 2%">60% Final <small>60%決勝</small></th>
				        <th style="padding: 2px;text-align: center;width: 2%">Total Penilaian <small>審査合計</small></th>
			        </tr>
		        </thead>
		        <tbody id="bodyTableDetail">
		        	
		        </tbody>
		    </table>
		</div>
		<div class="col-xs-12" id="divSga">
			<span style="font-weight: bold;font-size: 18px;color: white"><i class="fa fa-pencil-square" style="font-size: 18px"></i>&nbsp;&nbsp;Approval Progress <small>承認の進捗</small></span>
			<table id="tableSga" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="" id="headTableSga">
					
				</thead>
				<tbody id="bodyTableSga">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
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


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var true_detail_belum = [];
		var true_detail_sudah = [];

		function fetchChart(){
			var data = {
				periode:$('#periode_select').val(),
			}
			$.get('{{ url("fetch/sga/monitoring") }}',data,function(result, status, xhr){
				if(result.status){

					xCategories = [];
					nilai_seleksi = [];
					nilai_final = [];

					$.each(result.teams_all, function(key, value){
						xCategories.push(value.team_no+' - '+value.team_name);
						var seleksi = 0;
						var final = 0;
						for(var i = 0; i < result.teams.length;i++){
							if (result.teams[i].team_no == value.team_no) {
								seleksi = result.teams[i].total_nilai_seleksi;
								final = result.teams[i].total_nilai_final;
							}
						}
						nilai_seleksi.push({y:parseInt(seleksi),key:value.team_no});
						nilai_final.push({y:parseInt(final),key:value.team_no});
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
					        text: 'SMALL GROUP ACTIVITY (SGA)',
					        style:{
					        	fontWeight:'bold',
					        	fontSize:'17px'
					        }
					    },
					    subtitle: {
					        text: 'スモールグループ活動',
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
							name: 'Hasil Seleksi',
							colorByPoint: false,
							color:'#599bd9',
						},{
							type: 'column',
							data: nilai_final,
							name: 'Hasil Final',
							stacking:true,
							colorByPoint: false,
							color:'#15850d',
						}
						]
					});

					$('#bodyTableDetail').html('');
					var tableDetail = '';
					var index = 1;
					$.each(result.teams_all, function(key, value){
						if (index < 6) {
							tableDetail += '<tr style="background-color:white;color:black;font-size:14px">';
							tableDetail += '<td style="text-align:right">'+index+'</td>';
							tableDetail += '<td>'+value.team_no+'</td>';
							tableDetail += '<td>'+value.team_name+'</td>';
							tableDetail += '<td>'+value.team_title+'</td>';
							var seleksi = 0;
							var final = 0;
							for(var i = 0; i < result.teams.length;i++){
								if (result.teams[i].team_no == value.team_no) {
									seleksi = result.teams[i].total_nilai_seleksi;
									final = result.teams[i].total_nilai_final;
								}
							}
							tableDetail += '<td style="text-align:right">'+(seleksi || '0')+'</td>';
							tableDetail += '<td style="text-align:right">'+(final || '0')+'</td>';
							tableDetail += '<td style="text-align:right">'+(value.persen_seleksi || '0')+'</td>';
							tableDetail += '<td style="text-align:right">'+(value.persen_final || '0')+'</td>';
							tableDetail += '<td style="text-align:right">'+(value.totals || '0')+'</td>';
							tableDetail += '</tr>';
						}
						index++;
					});
					$('#bodyTableDetail').append(tableDetail);
					$("#periode").html('PERIODE '+result.periode);

					$('#headTableSga').html('');
					var tableApprovalHead = '';

					if (result.approval.length > 0) {
						if (result.approval[0].periode.match(/Final/gi)) {
							tableApprovalHead += '<tr>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Secretariat <small>事務局</small></th>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Manager QA <small>品質保証課長</small></th>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Deputy General Manager <small>副部長</small></th>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">General Manager <small>部長</small></th>';
							// tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Vice President Director <small>副社長</small></th>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">President Director <small>社長</small></th>';
							tableApprovalHead += '</tr>';
						}else{
							tableApprovalHead += '<tr>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Secretariat <small>事務局</small></th>';
							tableApprovalHead += '<th width="2%" style="background-color: #537cf5; color: #fff;">Deputy General Manager <small>副部長</small></th>';
							tableApprovalHead += '</tr>';
						}

						$('#bodyTableSga').html('');
						var tableApproval = '';
						tableApproval += '<tr>';
							$.each(result.approval, function(key, value){
								if (value.periode.match(/Final/gi)) {
									if (value.secretariat_approver_status == null || ('{{$username}}' != value.manager_qa_approver_id && '{{$username}}' != value.dgm_approver_id && '{{$username}}' != value.gm_approver_id && '{{$username}}' != value.presdir_approver_id && '{{$role}}' != 'S-MIS' && '{{$username}}' != value.secretariat_approver_id)) {
										$('#divSga').hide();
										$('#divTop').removeClass('col-xs-7');
										$('#divTop').addClass('col-xs-12');
										// tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
									}else{
										if (value.secretariat_approver_status != null) {
											tableApproval += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.secretariat_approver_status.split('_')[1]))+'</td>';
										}else{
											if (value.secretariat_approver_name == null) {
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}

										if (value.manager_qa_approver_status != null) {
											if (value.manager_qa_approver_status.split('_')[0] == 'Approved') {
												var bgcolorapprove = '#00a65a';
											}else{
												var bgcolorapprove = '#d1a513';
											}
											tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.manager_qa_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.manager_qa_approver_status.split('_')[1]))+'</td>';
										}else{
											if ('{{$username}}' == value.manager_qa_approver_id || '{{$role}}' == 'S-MIS') {
												var url = "{{ url('approval/sga/report/') }}";
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/manager_qa">'+ value.manager_qa_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.manager_qa_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}

										if (value.dgm_approver_status != null) {
											if (value.dgm_approver_status.split('_')[0] == 'Approved') {
												var bgcolorapprove = '#00a65a';
											}else{
												var bgcolorapprove = '#d1a513';
											}
											tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.dgm_approver_status.split('_')[1]))+'</td>';
										}else{
											if ('{{$username}}' == value.dgm_approver_id || '{{$role}}' == 'S-MIS') {
												var url = "{{ url('approval/sga/report/') }}";
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/dgm">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}

										if (value.gm_approver_status != null) {
											if (value.gm_approver_status.split('_')[0] == 'Approved') {
												var bgcolorapprove = '#00a65a';
											}else{
												var bgcolorapprove = '#d1a513';
											}
											tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.gm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.gm_approver_status.split('_')[1]))+'</td>';
										}else{
											if ('{{$username}}' == value.gm_approver_id || '{{$role}}' == 'S-MIS') {
												var url = "{{ url('approval/sga/report/') }}";
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/gm">'+ value.gm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.gm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}

										// if (value.vice_approver_status != null) {
										// 	if (value.vice_approver_status.split('_')[0] == 'Approved') {
										// 		var bgcolorapprove = '#00a65a';
										// 	}else{
										// 		var bgcolorapprove = '#d1a513';
										// 	}
										// 	tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.vice_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.vice_approver_status.split('_')[1]))+'</td>';
										// }else{
										// 	if ('{{$username}}' == value.vice_approver_id || '{{$role}}' == 'MIS') {
										// 		var url = "{{ url('approval/sga/report/') }}";
										// 		tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/vice">'+ value.vice_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
										// 	}else{
										// 		tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.vice_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
										// 	}
										// }

										if (value.presdir_approver_status != null) {
											if (value.presdir_approver_status.split('_')[0] == 'Approved') {
												var bgcolorapprove = '#00a65a';
											}else{
												var bgcolorapprove = '#d1a513';
											}
											tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.presdir_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.presdir_approver_status.split('_')[1]))+'</td>';
										}else{
											if ('{{$username}}' == value.presdir_approver_id || '{{$role}}' == 'S-MIS') {
												var url = "{{ url('approval/sga/report/') }}";
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/presdir">'+ value.presdir_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.presdir_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}
									}
								}else{
									if (value.secretariat_approver_status == null || ('{{$username}}' != value.dgm_approver_id && '{{$role}}' != 'S-MIS' && '{{$username}}' != value.secretariat_approver_id)) {
										$('#divSga').hide();
										$('#divTop').removeClass('col-xs-7');
										$('#divTop').addClass('col-xs-12');
										// tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
									}else{
										$('#divSga').removeClass('col-xs-12');
										$('#divSga').addClass('col-xs-4');
										if (value.secretariat_approver_status != null) {
											tableApproval += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.secretariat_approver_status.split('_')[1]))+'</td>';
										}else{
											if (value.secretariat_approver_name == null) {
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}

										if (value.dgm_approver_status != null) {
											if (value.dgm_approver_status.split('_')[0] == 'Approved') {
												var bgcolorapprove = '#00a65a';
											}else{
												var bgcolorapprove = '#d1a513';
											}
											tableApproval += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(value.dgm_approver_status.split('_')[1]))+'</td>';
										}else{
											if ('{{$username}}' == value.dgm_approver_id || '{{$role}}' == 'S-MIS') {
												var url = "{{ url('approval/sga/report/') }}";
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.periode+'/dgm">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</a></td>';
											}else{
												tableApproval += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ value.dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
											}
										}
									}
								}
							});
						tableApproval += '</tr>';
						$('#headTableSga').append(tableApprovalHead);
						$('#bodyTableSga').append(tableApproval);
					}else{
						$('#divSga').hide();
					}
				}
				else{
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
	</script>
	@endsection