@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	html {
	  scroll-behavior: smooth;
	}


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif						
	<div class="row" style="padding-left: 10px;padding-right: 10px;">
		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;padding-bottom: 10px;">
			<div class="col-xs-2" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<center><span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span></center>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;padding-right: 10px">
					<select style="width: 100%;text-align: left;" class="form-control select2" id="select_periode" data-placeholder="Pilih Periode (期間を設定する)">
						<option value=""></option>
						@foreach($fy_all as $fy_all)
							<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
						@endforeach
					</select>
			</div>
			<div class="col-xs-1" style="padding-left: 0;padding-right: 0px">
				<button class="btn btn-default pull-left" onclick="fillData()" style="font-weight: bold;height:36px;background-color: rgb(126,86,134);color: white;border: 1px solid rgb(126,86,134);vertical-align: middle;width: 100%">
					Search (検索)
				</button>
			</div>
			<div class="col-xs-6 pull-right" style="padding-left: 5px;padding-right: 0px;">
				@if($emp != null)
				@if($emp->department == 'Quality Assurance Department' || $emp->department == 'Management Information System Department')
				<a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('index/slogan/report')}}">
					<i class="fa fa-book"></i>&nbsp;&nbsp;Report (報告)
				</a>
				@endif
				@endif
				<a class="btn btn-info pull-right" id="btn_assessment" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/slogan/assessment')}}">
					<i class="fa fa-pencil"></i>&nbsp;&nbsp;Assessment (アセスメント)
				</a>
				<button class="btn btn-primary pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" onclick="fillData()">
					<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh (リフレッシュ)
				</button>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 10px">
			
		</div>
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
					<div class="small-box" style="background: #00af50; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>SUDAH MENGIKUTI</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>参加済</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_sudah_cek">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_sudah_cek">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 90px;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #b02828; height: 38vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 40px;">
							<h3 style="margin-bottom: 0px;font-size: 1.8vw;color: white;"><b>BELUM MENGIKUTI</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>未参加</b></h3>
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
	</div>
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
					        <th style="padding: 5px;text-align: center;width: 1%">NIK (従業員ID)</th>
					        <th style="padding: 5px;text-align: center;width: 2%">Nama (名前)</th>
					        <th style="padding: 5px;text-align: center;width: 1%">Dept (課)</th>
					        <th style="padding: 5px;text-align: center;width: 3%">Sect (班)</th>
					        <th style="padding: 5px;text-align: center;width: 3%">Group (グループ)</th>
					        <th style="padding: 5px;text-align: center;width: 3%">Sub Group (サブグループ)</th>
					        <th style="padding: 5px;text-align: center;width: 3%">Slogan (スローガン)</th>
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

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr_sudah = null;
	var arr_belum = null;
	var kataconfirm = 'Apakah Anda yakin?';

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillData();

		$('.select2').select2({
			allowClear:true
		});
	});

	var slogan_detail = null;
	var periode = '';

	function fillData() {
		$("#loading").show();
		var data = {
			fiscal_year:$('#select_periode').val()
		}
		$.get('{{ url("fetch/slogan") }}',data, function(result, status, xhr){
			if(result.status){
				if (result.asesor == 'Yes') {
					$('#btn_assessment').show();
				}else{
					$('#btn_assessment').hide();
				}

				xCategories = [];
					belum = [];
					sudah = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					slogan_detail = null;
					periode = '';

					$.each(result.department, function(key, value){
						xCategories.push(value.department_shortname);
						var belums = 0;
						var sudahs = 0;
						for(var i = 0; i < result.slogan.length;i++){
							if (result.slogan[i].department_name == value.department) {
								if (result.slogan[i].slogan_1 == null) {
									belums++;
								}else{
									sudahs++;
								}
							}
						}
						belum.push({y:parseInt(belums),key:value.department});
						total_belum = total_belum + parseInt(belums);
						sudah.push({y:parseInt(sudahs),key:value.department});
						total_sudah = total_sudah + parseInt(sudahs);
					});

					total = total_belum+total_sudah;

					$('#total_sudah_cek').html(total_sudah+' <span style="font-size:2.4vw"> 人</span>');
					$('#total_belum_cek').html(total_belum+' <span style="font-size:2.4vw"> 人</span>');
					$('#persen_sudah_cek').html(((total_sudah/total)*100).toFixed(1)+' %');
					$('#persen_belum_cek').html(((total_belum/total)*100).toFixed(1)+' %');

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
								text: 'Total Data (合計データ)',
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
							name: 'Belum (未参加)',
							colorByPoint: false,
							color:'#f44336'
						},{
							type: 'column',
							data: sudah,
							name: 'Sudah (参加済)',
							colorByPoint: false,
							color:'#32a852'
						}
						]
					});

					slogan_detail = result.slogan;

					periode = result.periode;
					$("#periode").html('PERIODE '+result.periode+' ('+result.periode+'期間)');
				$("#loading").hide();
			}else{
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function ShowModal(department_shortname,status_cek,department) {
			$('#loading').show();
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < slogan_detail.length;i++){
				if (slogan_detail[i].department_name === department) {
					if (status_cek == 'Sudah (参加済)') {
						if (slogan_detail[i].slogan_1 != null) {
							tableDetail += '<tr>';
							tableDetail += '<td>'+slogan_detail[i].employee_id+'</td>';
							tableDetail += '<td>'+slogan_detail[i].name+'</td>';
							if (department_shortname == 'MGT') {
								tableDetail += '<td>Management</td>';
							}else{
								tableDetail += '<td>'+(department_shortname || '')+'</td>';
							}
							tableDetail += '<td>'+(slogan_detail[i].section || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].group || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].sub_group || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].slogan_1 || '')+'</td>';
							tableDetail += '</tr>';
						}
					}
					if (status_cek == 'Belum (未参加)') {
						if (slogan_detail[i].slogan_1 == null) {
							tableDetail += '<tr>';
							tableDetail += '<td>'+slogan_detail[i].employee_id+'</td>';
							tableDetail += '<td>'+slogan_detail[i].name+'</td>';
							if (department_shortname == 'MGT') {
								tableDetail += '<td>Management</td>';
							}else{
								tableDetail += '<td>'+(department_shortname || '')+'</td>';
							}
							tableDetail += '<td>'+(slogan_detail[i].section || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].group || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].sub_group || '')+'</td>';
							tableDetail += '<td>'+(slogan_detail[i].slogan_1 || '')+'</td>';
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

            $('#judul_weekly').html('Detail '+status_cek+' Mengikuti Lomba Slogan Mutu YMPI (YMPIスローガン大会に詳細情報​​​)<br>'+(department || 'Management')+'<br>Periode '+periode+' ('+periode+' 期間)');
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

		function ShowModalAll(status_cek) {
			$('#loading').show();
			$('#tableDetail').DataTable().clear();
        	$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';
			for(var i = 0; i < slogan_detail.length;i++){
				if (status_cek == 'Sudah') {
					if (slogan_detail[i].slogan_1 != null) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+slogan_detail[i].employee_id+'</td>';
						tableDetail += '<td>'+slogan_detail[i].name+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].department_name || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].section || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].group || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].sub_group || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].slogan_1 || '')+'</td>';
						tableDetail += '</tr>';
					}
				}
				if (status_cek == 'Belum') {
					if (slogan_detail[i].slogan_1 == null) {
						tableDetail += '<tr>';
						tableDetail += '<td>'+slogan_detail[i].employee_id+'</td>';
						tableDetail += '<td>'+slogan_detail[i].name+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].department_name || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].section || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].group || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].sub_group || '')+'</td>';
						tableDetail += '<td>'+(slogan_detail[i].slogan_1 || '')+'</td>';
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

            $('#judul_weekly').html('Detail '+status_cek+' Mengikuti Lomba Slogan Mutu YMPI (YMPIスローガン大会に詳細情報​​​)<br>Periode '+periode+' ('+periode+' 期間)');
			$('#modalDetail').modal('show');
			$('#loading').hide();
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