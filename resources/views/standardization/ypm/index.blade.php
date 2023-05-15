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

	/**{
	    margin: 0;
	    padding: 0;
	}*/
	.rating {
		display: flex;
		flex-direction: row-reverse;
		justify-content: center;
		}


		.rating > input{ display:none;}

		.rating > label {
		position: relative;
		width: 1.1em;
		font-size: 2vw;
		color: #4881db;
		cursor: pointer;
		}

		.rating > label::before{
		content: "\2605";
		position: absolute;
		opacity: 0;
		}

		.rating > label:hover:before,
		.rating > label:hover ~ label:before {
		opacity: 1 !important;
		}

		.rating > input:checked ~ label:before{
		opacity:1;
		}

		.rating:hover > input:checked ~ label:before{ opacity: 0.2; }

	#tableDetail > tbody > tr > td:hover {
		background-color: #cff1ff !important;
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	#tableCheck > tbody > tr > td:hover{
		background-color: #a7ff8f !important;
	}

	.hover_td:hover{
		background-color: #a7ff8f !important;
	}

	.nav-tabs-custom > .nav-tabs > li.active{
	border-top-color: lightgray !important;
}

.nav-tabs-custom > .nav-tabs > li.active > a, .nav-tabs-custom > .nav-tabs > li.active:hover > a{
	background-color: lightgray !important;
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
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode_fix"></span></center>
				<input type="hidden" name="periode" id="periode">
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select id="periode_select" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Periode (年度選択)">
					<option value=""></option>
					@foreach($periode as $per)
					<option value="{{$per->periode}}">{{$per->periode}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 5px">
				<button class="btn btn-default pull-left" onclick="fetchChart()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search (検索)
				</button>
			</div>
			@if(str_contains($role,'STD') || str_contains($role,'MIS'))
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px">
				<a class="btn btn-success pull-right" href="{{ url('index/standardization/ypm/report') }}" style="margin-left: 5px;">
					<i class="fa fa-book"></i> Report
				</a>
				<a class="btn btn-primary pull-right" href="{{ url('index/standardization/ypm/master') }}" style="margin-left: 5px;">
					<i class="fa fa-list"></i> Master
				</a>
				<a class="btn btn-default pull-right" href="{{ url('index/standardization/ypm/point_check') }}" style="margin-left: 5px;">
					<i class="fa fa-check-square-o"></i> Point Check
				</a>
			</div>
			@endif
		</div>
		<div class="col-xs-6" style="padding-right: 0px;">
			<div id="container" style="height: 45vh;"></div>
		</div>
		<div class="col-xs-6" style="padding-left: 5px;background-color: #333333;height: 45vh;border-left: 2px solid white;" id="divTop">
			<span style="font-weight: bold;font-size: 18px;color: white"><i class="fa fa-trophy fa-2x" style="font-size: 18px"></i>&nbsp;&nbsp;YPM Team <small style="color: #b4b0ff">YPMチーム</small></span>
			<!-- <div class="box box-solid" >
				<div class="box-body"> -->
					<div class="nav-tabs-custom" style="height: 40vh">
						<ul class="nav nav-tabs" style="font-weight: bold; font-size: 18px">
							<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Day 1 <small class="text-purple">一日目</small></a></li>
							<li class="vendor-tab" id="tab_ke_2"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Day 2 <small class="text-purple">二日目</small></a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1" >
								<div id="team_all_1">
									
								</div>
							</div>
							<div class="tab-pane" id="tab_2" >
								<div id="team_all_2">
									
								</div>
							</div>
						</div>
					</div>
				<!-- </div>
			</div> -->
			
			<!-- <table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
		        <thead>
			        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;font-size:15px">
			        	<th style="padding: 2px;text-align: center;width: 1%">#</th>
				        <th style="padding: 2px;text-align: center;width: 4%">Team Name </th>
				        <th style="padding: 2px;text-align: center;width: 5%">Title</th>
				        <th style="padding: 2px;text-align: center;width: 2%">Q1</th>
				        <th style="padding: 2px;text-align: center;width: 2%">Q2</th>
				        <th style="padding: 2px;text-align: center;width: 2%">Q3</th>
				        <th style="padding: 2px;text-align: center;width: 2%">Contest</th>
				        <th style="padding: 2px;text-align: center;width: 2%">Total Nilai</th>
			        </tr>
		        </thead>
		        <tbody id="bodyTableDetail">
		        	
		        </tbody>
		    </table> -->
					
					<!-- <button class="btn btn-danger" onclick="$('#modalPenilaian').modal('hide');$('#team_id').html('')" style="font-size: 20px;font-weight: bold;text-align: center;width: 100%">
						SELESAI
					</button> -->
		</div>
		<div id="divPenilaian" class="col-xs-12" style="margin-top: 20px;">
			<center style="background-color: lightsalmon">
				<span style="font-weight: bold;font-weight: bold;font-size: 20px;">INPUT PENILAIAN <small class="text-purple" style="font-size: 15px;">評価入力</small></span>
			</center>
			<table style="width: 100%;margin-bottom: 5px;"  class="table table-bordered table-striped table-hover">
				<tr>
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 1%">Assessor ID <br><small class="text-purple" style="font-size: 15px;">評価者ID</small></th>
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 3%">Assessor Name <br><small class="text-purple" style="font-size: 15px;">評価者名</small></th>
					<!-- <th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 1%">Team ID <br><small class="text-purple" style="font-size: 15px;">チームID</small></th> -->
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 3%">Team Dept. <br><small class="text-purple" style="font-size: 15px;">チーム部</small></th>
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">Team Name <br><small class="text-purple" style="font-size: 15px;">チーム名</small></th>
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 6%">Title <br><small class="text-purple" style="font-size: 15px;">タイトル</small></th>
					<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">File PDF <br><small class="text-purple" style="font-size: 15px;">PDFファイル</small></th>
				</tr>
				<tr>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="asesor_id">{{$emp->employee_id}}</td>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="asesor_name">{{$emp->name}}</td>
					<td style="background-color: white;padding: 3px;font-size: 18px;display: none;" id="team_id"></td>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="team_dept"></td>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="team_name"></td>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="title"></td>
					<td style="background-color: white;padding: 3px;font-size: 18px;" id="pdf"></td>
					<input type="hidden" name="pdf_name" id="pdf_name">
				</tr>
				<!-- <tr>
					
					
				</tr>
				<tr>
				</tr> -->
			</table>
			<?php $indexes = 0; ?>
			<?php $indexes2 = 0; ?>
			<?php $nilai = []; ?>
			<table style="width: 100%"  class="table table-bordered table-striped table-hover" id="tableCheck">
					<tr>
					<?php for ($i=0; $i < count($point); $i++) { ?>
						<th style="width:15%;background-color: #cddc39;border: 1px solid black;" id="criteria_{{$indexes2}}"><?php echo $point[$i]->criteria ?></th>
						<?php $indexes2++ ?>
					<?php } ?>
					</tr>
					<tr>
						<?php for ($i=0; $i < count($point); $i++) { ?>
							<td style="padding: 0px;"><input type="number" readonly="" id="point_{{$indexes}}" class="hover_td" onclick="fetchPoint('{{$point[$i]->criteria}}')" style="cursor: pointer;height: 100px;width:100%;font-weight: bold;font-size: 35px;text-align: center;background-color: white;" placeholder="Input Nilai 評価入力"></td>
							<?php $indexes++ ?>
						<?php } ?>
					</tr>
					<tr id="div_attach_pdf" style="display: none;">
						<td colspan="{{count($point)}}" style="padding: 3px;background-color: white;">
							<button style="width: 100%;font-weight: bold;font-size: 25px" class="btn btn-danger btn-xs" onclick="$('#div_attach_pdf').hide();$('#attach_pdf').html('');"><i class="fa fa-close"></i>&nbsp;&nbsp;Close <small>クロス</small></button>
							<div id="attach_pdf"></div>
						</td>
					</tr>
			</table>
		</div>

		<div class="col-xs-12" id="divApproval" style="margin-top: 10px;">
			<span style="font-weight: bold;font-size: 18px;color: white"><i class="fa fa-pencil-square" style="font-size: 18px"></i>&nbsp;&nbsp;Approval Progress <small>承認の進捗</small></span>
			<table id="tableSga" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="" id="headTableYPM">
					
				</thead>
				<tbody id="bodyTableYPM">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>

		<!-- <div class="modal modal-default fade" id="modalPenilaian" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header no-padding">
						<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 10px; padding-bottom: 10px;" class="modal-title">
							PENILAIAN YPM
						</h4>
					</div>
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<table style="width: 100%"  class="table table-bordered table-striped table-hover">
									<tr>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">Assessor ID</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 5%">Assessor Name</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">Team No.</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 2%">Team ID</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;width: 5%">Team Name</th>
									</tr>
									<tr>
										<td style="padding: 3px;" id="asesor_id">{{$emp->employee_id}}</td>
										<td style="padding: 3px;" id="asesor_name">{{$emp->name}}</td>
										<td style="padding: 3px;text-align: center;" id="team_no"></td>
										<td style="padding: 3px;" id="team_id"></td>
										<td style="padding: 3px;" id="team_name"></td>
									</tr>
									<tr>
										<th colspan="4" style="padding: 3px;background-color: lightskyblue;border:1px solid black;">Title</th>
										<th style="padding: 3px;background-color: lightskyblue;border:1px solid black;">File PDF</th>
									</tr>
									<tr>
										<td colspan="4" style="padding: 3px;" id="title"></td>
										<td style="padding: 3px;" id="pdf"></td>
									</tr>
									<tr id="div_attach_pdf" style="display: none;">
										<td colspan="5" style="padding: 3px;">
											<button style="width: 100%;font-weight: bold;font-size: 25px" class="btn btn-danger btn-xs" onclick="$('#div_attach_pdf').hide();$('#attach_pdf').html('');"><i class="fa fa-close"></i>&nbsp;&nbsp;Close <small>クロス</small></button>
											<div id="attach_pdf"></div>
										</td>
									</tr>
								</table>
								<?php $indexes = 0; ?>
								<?php $nilai = []; ?>
								<table style="width: 100%"  class="table table-bordered table-striped table-hover" id="tableCheck">
										<tr>
										<?php for ($i=0; $i < count($point); $i++) { ?>
											<th style="width:15%;background-color: #cddc39;border: 1px solid black;">{{$point[$i]->criteria}}</th>
										<?php } ?>
										</tr>
										<tr>
											<?php for ($i=0; $i < count($point); $i++) { ?>
												<td id="point_{{$indexes}}" class="hover_td" onclick="fetchPoint('{{$point[$i]->criteria}}')" style="cursor: pointer;height: 100px;font-weight: bold;font-size: 40px;text-align: center;"></td>
												<?php $indexes++ ?>
											<?php } ?>
										</tr>
								</table>
								<button class="btn btn-danger" onclick="$('#modalPenilaian').modal('hide');$('#team_id').html('')" style="font-size: 20px;font-weight: bold;text-align: center;width: 100%">
									SELESAI
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		<div class="modal modal-default fade" id="modalNilai">
			<div class="modal-dialog modal-md" style="width: 150vh;margin-top: 130px;">
				<div class="modal-content">
					<div class="modal-header no-padding">
						<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;font-size: 30px;" class="modal-title">
							PILIH NILAI <small style="color: black;">点数</small>
						</h4>
					</div>
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<input type="hidden" name="criteria" id="criteria">
								<table style="width: 100%">
									<tr>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-default" id="1" style="border:1px solid black;width: 100%;font-size: 20px;font-weight: bold;" onclick="saveResult(25)"><input type="hidden" name="nilai_pilihan_1" id="nilai_pilihan_1">Kurang <small style="color: #756eff">良くない</small> (25)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-default" id="2" style="border:1px solid black;width: 100%;font-size: 20px;font-weight: bold;" onclick="saveResult(50)"><input type="hidden" name="nilai_pilihan_2" id="nilai_pilihan_2">Cukup <small style="color: #756eff">普通</small> (50)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-default" id="3" style="border:1px solid black;width: 100%;font-size: 20px;font-weight: bold;" onclick="saveResult(75)"><input type="hidden" name="nilai_pilihan_3" id="nilai_pilihan_3">Baik <small style="color: #756eff">良い</small> (75)</button>
										</td>
										<td style="padding: 3px;width: 15%">
											<button class="btn btn-default" id="4" style="border:1px solid black;width: 100%;font-size: 20px;font-weight: bold;" onclick="saveResult(100)"><input type="hidden" name="nilai_pilihan_4" id="nilai_pilihan_4">Sangat Baik <small style="color: #756eff">とても良い</small> (100)</button>
										</td>
										<td style="padding: 3px;width: 1%">
											<button class="btn btn-danger" id="5" style="border:1px solid black;width: 100%;font-size: 20px;font-weight: bold;" onclick="saveResult(0)"><input type="hidden" name="nilai_pilihan_5" id="nilai_pilihan_5" value="Clear">Clear <small style="color: white">点数削除</small></button>
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


		var kata_confirm = 'Are You Sure?';

		jQuery(document).ready(function() {
			$(document).keydown(function(e) {
				switch(e.which) {
					case 48:
					location.reload(true);
					break;
					case 49:
					$("#tab_header_1").click()
					break;
					case 50:
					$("#tab_header_2").click()
					break;
				}
			});
			$("#divPenilaian").hide();
			$("#divApproval").hide();
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

		var ypm_all = [];
		var points = <?php echo json_encode($point) ?>;


		var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		var q1 = [];
		var q2 = [];
		var q3 = [];

		var approval_status = null;

		var teams = null;

		var team_quarter = [];

		function fetchChart(){
			var data = {
				periode:$('#periode_select').val(),
			}
			$.get('{{ url("fetch/standardization/ypm") }}',data,function(result, status, xhr){
				if(result.status){

					xCategories = [];
					nilai_final = [];

					$.each(result.ypm, function(key, value){
						xCategories.push(value.team_name);
						nilai_final.push({y:parseInt(value.contest),key:value.team_name});
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
								text: 'Total Nilai',
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
							// layout: 'horizontal',
							// backgroundColor:
							// Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							// itemStyle: {
							// 	fontSize:'12px',
							// },
							enabled:false
						},	
					    title: {
					        text: 'YMPI Productivity Management Evaluation',
					        style:{
					        	fontWeight:'bold',
					        	fontSize:'17px'
					        }
					    },
					    subtitle: {
					        text: 'YMPI生産性管理評価',
					        style:{
					        	fontWeight:'bold',
					        	color:'#b4b0ff',
					        	backgroundColor:'#fff'
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
							data: nilai_final,
							name: 'Total Nilai',
							colorByPoint: false,
							color:'#599bd9',
						}
						]
					});

					$('#bodyTableDetail').html('');
					var tableDetail = '';
					var index = 1;
					var index2 = 1;
					q1 = [];
					q2 = [];
					q3 = [];
					team_quarter = [];
					// $.each(result.ypm, function(key, value){
					// 	tableDetail += '<tr style="background-color:white;color:black;font-size:16px;">';
					// 	tableDetail += '<td style="text-align:center">'+index+'</td>';
					// 	tableDetail += '<td>'+value.team_name+'</td>';
					// 	tableDetail += '<td>'+value.team_title+'</td>';
						// if (value.q1 != 0) {
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q1 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q1;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<input id="id_q1" type="hidden" value="'+value.idq1+'">';
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		if (i >= parseInt(value.q1)) {
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" checked onclick="changeStar(\''+value.idq1+'\',this.value)">';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}else{
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="changeStar(\''+value.idq1+'\',this.value)">';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}
						// 	}
						// 	tableDetail += '<a style="color:#ff6666;text-decoration:none;font-size:30px;vertical-align:middle" onclick="deleteStar(\''+value.idq1+'\')"><i class="fa fa-trash"></i></a>';
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// 	q1.push(value.q1);
						// 	team_quarter.push(value.team_id);
						// }else{
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q1 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q1;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		var periodes = 'Q1_'+result.periode;
						// 		var criteria = 'All';
						// 		tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="inputStar(this.value,\''+periodes+'\',\''+value.team_id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+criteria+'\')">';
						//     	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 	}
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// }

						// if (value.q2 != 0) {
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q2 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q2;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<input id="id_q1" type="hidden" value="'+value.idq2+'">';
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		if (i >= parseInt(value.q2)) {
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="changeStar(\''+value.idq2+'\',this.value)" checked>';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}else{
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="changeStar(\''+value.idq2+'\',this.value)">';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}
						// 	}
						// 	tableDetail += '<a style="color:#ff6666;text-decoration:none;font-size:30px;vertical-align:middle" onclick="deleteStar(\''+value.idq2+'\')"><i class="fa fa-trash"></i></a>';
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// 	q2.push(value.q2);
						// 	team_quarter.push(value.team_id);
						// }else{
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q2 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q2;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		var periodes = 'Q2_'+result.periode;
						// 		var criteria = 'All';
						// 		tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="inputStar(this.value,\''+periodes+'\',\''+value.team_id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+criteria+'\')">';
						//     	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 	}
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// }

						// if (value.q3 != 0) {
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q3 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q3;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<input id="id_q1" type="hidden" value="'+value.idq3+'">';							
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		if (i >= parseInt(value.q3)) {
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="changeStar(\''+value.idq3+'\',this.value)" checked>';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}else{
						// 			tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="changeStar(\''+value.idq3+'\',this.value)">';
						// 	    	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 		}
						// 	}
						// 	tableDetail += '<a style="color:#ff6666;text-decoration:none;font-size:30px;vertical-align:middle" onclick="deleteStar(\''+value.idq3+'\')"><i class="fa fa-trash"></i></a>';
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// 	team_quarter.push(value.team_id);
						// }else{
						// 	tableDetail += '<td style="cursor:pointer;text-align:center">';
						// 	if (value.file_pdf_q3 != null) {
						// 		var url_pdf = "{{ url('data_file/ypm/pdf/') }}"+'/'+value.file_pdf_q3;
						// 		tableDetail += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a>';
						// 	}
						// 	tableDetail += '<div class="rating">';
						// 	for(var i = 5; i >= 1;i--){
						// 		var periodes = 'Q3_'+result.periode;
						// 		var criteria = 'All';
						// 		tableDetail += '<input type="radio" name="rating'+index2+'" value="'+i+'" id="'+i+'_'+index2+'" onclick="inputStar(this.value,\''+periodes+'\',\''+value.team_id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+criteria+'\')">';
						//     	tableDetail += '<label for="'+i+'_'+index2+'">☆</label>';
						// 	}
						// 	index2++;
						// 	tableDetail += '</div>';
						// 	tableDetail += '</td>';
						// }

					// 	if (value.contest_asesor == 0) {
					// 		tableDetail += '<td style="cursor:pointer;text-align:center;font-size:20px;font-weight:bold;" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\');"></td>';
					// 	}else{
					// 		tableDetail += '<td style="cursor:pointer;text-align:center;font-size:20px;font-weight:bold;" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\');">'+(value.contest_asesor || '0')+'</td>';
					// 	}
					// 	tableDetail += '<td style="cursor:pointer;text-align:center;font-size:20px;font-weight:bold;">'+(value.contest || '0')+'</td>';
					// 	tableDetail += '</tr>';
					// 	index++;
					// });
					// $('#bodyTableDetail').append(tableDetail);

					var approvals = null;
					approval_status = null;
					if (result.teams[0].std_approval != null) {
						approvals = 'Done';
						approval_status = 'Done';
					}

					$("#periode_fix").html('PERIODE '+result.periode+' ('+result.periode.split('FY')[1]+'期)');
					$("#periode").val(result.periode);

					ypm_all = result.ypm_all;

					if ($('#team_name').text() != '') {
						inputPenilaian($('#team_id').text(),$('#team_dept').text(),$('#team_name').text(),$('#title').text(),$('#pdf_name').val());
					}

					$('#team_all_1').html('');
					$('#team_all_2').html('');
					var teams_all_1 = '';
					var teams_all_2 = '';
					var uu = 0;
					$.each(result.teams, function(key, value){
						if (value.day == '1') {
							if (value.contest_asesor != 0) {
								teams_all_1 += '<div class="col-xs-4" style="margin-bottom:10px;padding-left:5px;padding-right5px;"><button class="btn btn-success" style="width:100%;font-weight:bold;border:1px solid black;" id="btn_team_'+uu+'" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_dept+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\',\''+uu+'\',\''+value.contest_asesor+'\');">'+value.team_name+'</button></div>';
							}else{
								teams_all_1 += '<div class="col-xs-4" style="margin-bottom:10px;padding-left:5px;padding-right5px;"><button class="btn btn-default" style="width:100%;font-weight:bold;border:1px solid black;" id="btn_team_'+uu+'" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_dept+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\',\''+uu+'\',\''+value.contest_asesor+'\');">'+value.team_name+'</button></div>';
							}
						}else{
							if (value.contest_asesor != 0) {
								teams_all_2 += '<div class="col-xs-4" style="margin-bottom:10px;padding-left:5px;padding-right5px;"><button class="btn btn-success" style="width:100%;font-weight:bold;border:1px solid black;" id="btn_team_'+uu+'" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_dept+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\',\''+uu+'\',\''+value.contest_asesor+'\');">'+value.team_name+'</button></div>';
							}else{
								teams_all_2 += '<div class="col-xs-4" style="margin-bottom:10px;padding-left:5px;padding-right5px;"><button class="btn btn-default" style="width:100%;font-weight:bold;border:1px solid black;" id="btn_team_'+uu+'" onclick="inputPenilaian(\''+value.team_id+'\',\''+value.team_dept+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.file_pdf_contest+'\',\''+uu+'\',\''+value.contest_asesor+'\');">'+value.team_name+'</button></div>';
							}
						}
						uu++;
					});
					$('#team_all_1').append(teams_all_1);
					$('#team_all_2').append(teams_all_2);

					teams = result.teams;

					$('#divApproval').hide();
					if (approvals != null) {
						if (result.judges.length > 0) {
							$('#divApproval').show();
							$('#headTableYPM').html('');
							var headYPM = '';
							headYPM += '<th width="2%" style="background-color: lightgrey; color: #000;border:1px solid black;padding:4px;">Secretariat</th>';
							for(var i = 0; i < result.judges.length;i++){
								headYPM += '<th width="2%" style="background-color: lightgrey; color: #000;border:1px solid black;padding:4px;">Asesor '+(i+1)+'</th>';
							}
							$('#headTableYPM').append(headYPM);
							$('#bodyTableYPM').html('');
							var bodyYPM = '';
							bodyYPM += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams[0].std_name +'<br>'+result.teams[0].std_approval+'<br>'+result.teams[0].std_approved_ats+'</td>';
							for(var i = 0; i < result.judges.length;i++){
								if (result.judges[i].judges_approval != null) {
									if (result.judges[i].judges_approval == 'Approved') {
										bodyYPM += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px;border:1px solid black;">'+ result.judges[i].judges_name +'<br>'+result.judges[i].judges_approval+'<br>'+result.judges[i].judges_approved_ats+'</td>';
									}else{
										bodyYPM += '<td style="background-color:#d1a513;color:white;font-weight:bold;font-size:11px;padding:10px;border:1px solid black;">'+ result.judges[i].judges_name +'<br>'+result.judges[i].judges_approval+'<br>'+result.judges[i].judges_approved_ats+'</td>';
									}
								}else{
									bodyYPM += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:13px;padding:10px;border:1px solid black;cursor:pointer" onclick="approval(\''+result.judges[i].judges_id+'\',\''+result.periode+'\')">'+ result.judges[i].judges_name +'<br>Waiting</td>';
								}
							}
							$('#bodyTableYPM').append(bodyYPM);
						}
					}

					// $('#divPenilaian').hide();
				}
				else{
					openErrorGritter('Error',result.message);
				}
			});
		}

		function approval(judges_id,periode) {
			var url = "{{ url('approval/standardization/ypm/') }}/"+periode+'/judges/'+judges_id;
			window.location.replace(url);
		}

		function inputPenilaian(team_id,team_dept,team_name,team_title,pdf,index) {
			$("#div_attach_pdf").hide();
			$('#team_id').html(team_id);
			$('#team_dept').html(team_dept);
			$('#team_name').html(team_name);
			$('#title').html(team_title);
			$('#pdf').html('');
			$('#pdf_name').val('');
			if (pdf != null && pdf != 'null' && pdf != '') {
				$('#pdf').html('<button class="btn btn-danger btn-xs" onclick="attach_pdf(\''+pdf+'\')"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Preview <small>PDFプレビュー</small></button>');
				$('#pdf_name').val(pdf);
			}
			// $('#modalPenilaian').modal('show');
			var indexes = 0;
			for(var j = 0; j < points.length;j++){
				if (approval_status == 'Done') {
					$('#point_'+indexes).removeAttr('onclick');
				}else{
					var elem = document.getElementById('point_'+indexes); 
					elem.setAttribute("onclick","fetchPoint('"+points[j].criteria+"');");
				}
				$('#point_'+indexes).val('');
				indexes++;
			}
			var indexes = 0;
			for(var j = 0; j < points.length;j++){
				for(var i = 0; i < ypm_all.length;i++){
					if (ypm_all[i].criteria == points[j].criteria && ypm_all[i].asesor_id == '{{$emp->employee_id}}' && ypm_all[i].team_id == team_id && ypm_all[i].team_dept == team_dept) {
						$('#point_'+indexes).val(ypm_all[i].result);
					}
				}
				indexes++;
			}
			
			// $('#modalPenilaian').modal('show');
			$('#divPenilaian').show();

			for(var i = 0; i < teams.length;i++){
				if (teams[i].contest_asesor != 0) {
					$('#btn_team_'+i).removeAttr('class');
					$('#btn_team_'+i).attr('class','btn btn-success');
				}else{
					$('#btn_team_'+i).removeAttr('class');
					$('#btn_team_'+i).attr('class','btn btn-default');
				}
			}
			$('#btn_team_'+index).removeAttr('class');
			$('#btn_team_'+index).attr('class','btn btn-warning');
		}

		function attach_pdf(file_name) {
			$("#div_attach_pdf").show();
			$('#attach_pdf').html('');
			var path = "{{asset('/data_file/ypm/pdf/')}}"+'/'+file_name;
	      	$('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
		}

		function fetchPoint(criteria) {
			$('#modalNilai').modal('show');
			$('#criteria').val(criteria);
		}

		function changeStar(id,values) {
			if ('{{$emp->employee_id}}' == 'PI0109004' || '{{$emp->employee_id}}' == 'PI1910002') {
				$('#loading').show();
				var data = {
					id:id,
					values:values
				}

				$.post('{{ url("update/standardization/ypm/evaluation") }}',data,function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!','Change Star Succeeded');
						fetchChart();
						$('#loading').hide();
					}else{
						$('#loading').hide();
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}

		function deleteStar(id) {
			if ('{{$emp->employee_id}}' == 'PI0109004' || '{{$emp->employee_id}}' == 'PI1910002') {
				$('#loading').show();
				var data = {
					id:id,
				}

				$.post('{{ url("delete/standardization/ypm/evaluation") }}',data,function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!','Delete Star Succeeded');
						fetchChart();
						$('#loading').hide();
					}else{
						$('#loading').hide();
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}

		function inputStar(values,periodes,team_id,team_dept,team_name,criteria) {
			if ('{{$emp->employee_id}}' == 'PI0109004' || '{{$emp->employee_id}}' == 'PI1910002') {
				if ((periodes.toLowerCase().split('_')[0] == 'q1' && q1.length < 5) || (periodes.toLowerCase().split('_')[0] == 'q2' && q2.length < 5) || (periodes.toLowerCase().split('_')[0] == 'q3' && q3.length < 5)) {
					if (!team_quarter.includes(team_id)) {
						$('#loading').show();
						var data = {
							result:values,
							periode:periodes,
							team_dept:team_dept,
							team_id:team_id,
							team_name:team_name,
							criteria:criteria,
							asesor_id:'{{$emp->employee_id}}',
							asesor_name:'{{$emp->name}}',
						}

						$.post('{{ url("input/standardization/ypm/evaluation") }}',data,function(result, status, xhr){
							if(result.status){
								openSuccessGritter('Success!','Input Star Succeeded');
								fetchChart();
								$('#loading').hide();
							}else{
								$('#loading').hide();
								openErrorGritter('Error!',result.message);
							}
						});
					}else{
						openErrorGritter('Error!','Team sudah pernah dipilih.');
						fetchChart();
					}
				}else{
					openErrorGritter('Error!','Anda sudah memilih 5 Team');
					fetchChart();
				}
			}else{
				openErrorGritter('Error!','Anda tidak memiliki hak akses.');
				fetchChart();
			}
		}

		function saveResult(id) {
			$('#loading').show();
			var data = {
				asesor_id:'{{$emp->employee_id}}',
				asesor_name:'{{$emp->name}}',
				team_dept:$('#team_dept').text(),
				team_id:$('#team_id').text(),
				team_name:$('#team_name').text(),
				title:$('#title').text(),
				criteria:$('#criteria').val(),
				periode:$('#periode').val(),
				result:id
			}

			$.post('{{ url("input/standardization/ypm/contest") }}',data,function(result, status, xhr){
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
	</script>
	@endsection