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
	.dataTables_filter,.dataTables_info{
		color: white !important;
	}
	#data-log-detail_info,#data-log-detail_filter,#data-log_info,#data-log_filter{
		color: black !important;
	}
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
		<div class="col-xs-12" style="padding-bottom: 10px;padding-left: 0px;padding-right: 0px;">
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Select Fiscal Year" id="fy">
					<option value=""></option>
					@foreach($fy_all as $fy_all)
					<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Select Point Check" id="audit_id">
					<option value=""></option>
					@foreach($point as $point)
					<option value="{{$point->audit_id}}">{{$point->audit_title}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search <small>検索</small>
				</button>
			</div>
			<div class="col-xs-6 pull-right" style="padding-left: 0px;padding-right: 0px;">
				@if($emp != null)
				@if($emp->department == 'Quality Assurance Department' || $emp->department == 'Management Information System Department')
				<a class="btn btn-info pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('index/qa/cpar_car/point_check')}}">
					<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Point Check
				</a>
				<a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/cpar_car/schedule')}}">
					<i class="fa fa-calendar"></i>&nbsp;&nbsp;Schedule
				</a>
				<a class="btn btn-danger pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/cpar_car/report')}}">
					<i class="fa fa-book"></i>&nbsp;&nbsp;Report
				</a>
				@endif
				@endif
				<button class="btn btn-primary pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" onclick="fillList()">
					<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
				</button>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<div id="container" style="height: 40vh;">
				
			</div>
		</div>
		<div class="col-xs-10" style="background-color: #ffeb3b;color:black;text-align: center;height: 35px;margin-top:20px;" id="div_detail">
        	<span style="font-size: 25px;font-weight: bold;">RESUME</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;margin-top:20px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<select class="form-control select2" style="width: 100%" id="select_month" data-placeholder="Pilih Bulan" onchange="selectMonth()">
        		<option value=""></option>
        	</select>
        </div>
		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
			<table id="tableCode" class="table table-bordered table-hover" style="margin-bottom: 0;background-color: #f0f0ff">
				<thead style="background-color: #0073b7;color: white" id="headTableCode">
				</thead>
				<tbody id="bodyTableCode">
				</tbody>
			</table>
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
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
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
	var packing_detail = null;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		packing_detail = null;
		fillList();
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fillList(){
		$('#loading').show();
		var data = {
			fy:$('#fy').val(),
			audit_id:$('#audit_id').val(),
		}
		$.get('{{ url("fetch/qa/cpar_car") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var totals = [];
				var sudah = [];
				var sudah_ng = [];
				var belum = [];

				var date = [];
				var date_name = [];

				for(var i = 0; i < result.fy_all.length;i++){
					categories.push(result.fy_all[i].month_name);
					var qty_sudah = 0;
					var qty_belum = 0;
					var qty_sudah_ng = 0;
					var total = 0;
					for(var j = 0; j < result.audit.length;j++){
						if (result.audit[j].month == result.fy_all[i].month && result.audit[j].schedule_status == 'Sudah Dikerjakan' && result.audit[j].handled_by != null) {
							qty_sudah++;
							total++;
						}
						if (result.audit[j].month == result.fy_all[i].month && result.audit[j].schedule_status == 'Sudah Dikerjakan' && result.audit[j].result_check == 'OK' && result.audit[j].handled_by == null) {
							qty_sudah++;
							total++;
						}
						if (result.audit[j].month == result.fy_all[i].month && result.audit[j].schedule_status == 'Sudah Dikerjakan' && result.audit[j].handled_by == null && result.audit[j].result_check.match(/NG/gi)) {
							qty_sudah_ng++;
							total++;
						}
						if (result.audit[j].month == result.fy_all[i].month && result.audit[j].schedule_status == 'Belum Dikerjakan') {
							qty_belum++;
							total++;
						}
					}
					sudah.push({key:result.fy_all[i].month,y:parseInt(qty_sudah)});
					sudah_ng.push({key:result.fy_all[i].month,y:parseInt(qty_sudah_ng)});
					belum.push({key:result.fy_all[i].month,y:parseInt(qty_belum)});
				}

				const chart = new Highcharts.Chart({
				    chart: {
				        renderTo: 'container',
				        type: 'column',
				        backgroundColor:'none',
				        options3d: {
				            enabled: true,
				            alpha: 0,
				            beta: 0,
				            depth: 50,
				            viewDistance: 25
				        }
				    },
				    xAxis: {
						categories: categories,
						type: 'category',
						gridLineWidth: 0,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:1,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight:'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Total Data <small>トータルデータ</small>',
							style: {
								color: '#eee',
								fontSize: '12px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						allowDecimals: false,
						labels:{
							style:{
								fontSize:"12px",
								fontWeight:'bold'
							}
						},
						type: 'linear',
						// opposite: true
					}
					],
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'12px',
						},
					},	
				    title: {
				        text: '<b>AUDIT CPAR & CAR QA',
						// style:{
						// 	fontSize:"12px"
						// }
				    },
				    plotOptions: {
				        series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showModal(this.category,this.series.name,this.options.key);
									}
								}
							},
							stacking: 'normal',
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '0.9vw'
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
						type: 'column',
						data: belum,
						name: 'Audit Belum Dilakukan',
						colorByPoint: false,
						color:'#3d53ff',
						stack:'st2'
					},{
						type: 'column',
						data: sudah,
						name: 'Audit Sudah Dilakukan (OK & Sudah Ditangani)',
						colorByPoint: false,
						color:'#32a852',
						stack:'st'
					},{
						type: 'column',
						data: sudah_ng,
						name: 'Audit Sudah Dilakukan (Belum Ditangani)',
						colorByPoint: false,
						color:'#a60000',
						stack:'st'
					}
					]
				});

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th style="width:1%">Periode</th>';
				headTableData += '<th style="width:3%">Audit Title</th>';
				headTableData += '<th style="width:1%">Product</th>';
				headTableData += '<th style="width:1%">Area</th>';
				headTableData += '<th style="width:3%">Auditor</th>';
				headTableData += '<th style="width:1%">Hasil</th>';
				headTableData += '<th style="width:1%">Status</th>';
				headTableData += '<th style="width:3%">Action</th>';
				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#tableCode').DataTable().clear();
  			    $('#tableCode').DataTable().destroy();

				$('#bodyTableCode').html("");
				var tableData = "";

				for(var i = 0; i < result.audit.length;i++){
					tableData += '<tr id="'+result.audit[i].month+'">';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].month_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].audit_title+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].product || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].area || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].employee_id+' - '+result.audit[i].name+'</td>';
					if (result.audit[i].result_check != null) {
						if (result.audit[i].result_check.match(/NG/gi)) {
							tableData += '<td style="text-align:center;background-color: #ffc2c2">NG</td>';
						}else{
							tableData += '<td style="text-align:center;background-color: #d1ffb3">OK</td>';
						}
					}else{
						tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
					}
					if (result.audit[i].result_check != null) {
						if (result.audit[i].result_check.match(/NG/gi)) {
							if (result.audit[i].handled_by == null) {
								tableData += '<td style="text-align:center;background-color: #ffc2c2">Belum Ditangani</td>';
							}else{
								tableData += '<td style="text-align:center;background-color: #d1ffb3">Sudah Ditangani</td>';
							}
						}else{
							tableData += '<td style="text-align:center;background-color: #d1ffb3">OK</td>';
						}
					}else{
						tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
					if (result.audit[i].schedule_status == 'Belum Dikerjakan') {
						// if ('{{$emp->employee_id}}' == result.audit[i].employee_id) {
							var url = '{{url("index/qa/cpar_car/audit")}}/'+result.audit[i].id_schedule;
							tableData += '<a class="btn btn-primary btn-xs" href="'+url+'"><i class="fa fa-list"></i>&nbsp;&nbsp;Audit</a>';
						// }
					}else{
						if (result.audit[i].result_check != null && result.audit[i].result_check.match(/NG/gi) && result.audit[i].handled_by == null) {
							var url = '{{url("index/qa/cpar_car/handling")}}/'+result.audit[i].id_schedule;
							tableData += '<a class="btn btn-warning btn-xs" style="margin-right:5px;" href="'+url+'"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Penanganan</a>';
							if (result.audit[i].send_status == null) {
								tableData += '<button class="btn btn-success btn-xs" style="margin-right:5px;" onclick="sendEmail(\''+result.audit[i].id_schedule+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Send Email</button>';
							}else{
								tableData += '<button class="btn btn-success btn-xs" style="margin-right:5px;" onclick="sendEmail(\''+result.audit[i].id_schedule+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Re-Send Email</button>';
							}
						}
						if (result.audit[i].schedule_date > '2022-11-31') {
							var url = '{{url("index/qa/cpar_car/pdf")}}/'+result.audit[i].id_schedule;
							tableData += '<a class="btn btn-danger btn-xs" target="_blank" style="margin-right:5px;" href="'+url+'"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
						}
					}
					tableData += '</td>';
					tableData += '</tr>';
				}

				$('#bodyTableCode').append(tableData);

		  		$('#select_month').html('');
				var months = '';
				months += '<option value=""></option>';
				for(var i = 0; i < result.fy_all.length;i++){
					months += '<option value="'+result.fy_all[i].month+'">'+result.fy_all[i].month_name+'</option>';
				}
				$('#select_month').append(months);

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function sendEmail(schedule_id) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			$.get('{{ url("index/qa/cpar_car/send_email") }}/'+schedule_id, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Success Send Email');
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function selectMonth() {
	    var input, filter, table,tbody, tr, td, i, txtValue;
	      input = document.getElementById("select_month");
	      filter = input.value;
	      if (filter == null) {
	        table = document.getElementById("bodyTableCode");
	        tr = table.getElementsByTagName("tr");
	        for (i = 0; i < tr.length; i++) {
	          td = tr[i].getElementsByTagName("td")[0];
	          if (td) {
	              tr[i].style.display = "";
	          }
	        }
	      }else{
	        table = document.getElementById("bodyTableCode");
	        tr = table.getElementsByTagName("tr");
	        for (i = 0; i < tr.length; i++) {
	          // td = tr[i].getElementsByTagName("td")[0];
	          // console.log(td);
	          if (tr[i].getAttribute("id").indexOf(filter) > -1) {
	            // txtValue = td.textContent || td.innerText;
	            // if (txtValue.indexOf(filter) > -1) {
	              tr[i].style.display = "";
	            // } else {
	              
	            // }
	          }else{
	            tr[i].style.display = "none";
	          }
	        }
	      }
	  }

	function showModal(month_name,status,month) {
		location.href='#div_detail'
		$('#select_month').val(month).trigger('change');
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