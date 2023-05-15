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
			<div class="col-xs-1" style="padding-left: 0px;padding-right: 5px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Audit Date From">
				</div>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Audit Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Auditor" id="auditor">
					<option value=""></option>
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search <small>検索</small>
				</button>
			</div>
			<div class="col-xs-6 pull-right" style="padding-left: 0px;padding-right: 0px;">
				@if($emp != null)
				@if($emp->department == 'Standardization Department' || $emp->department == 'Management Information System Department')
				<a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/feeling/audit')}}">
					<i class="fa fa-pencil"></i>&nbsp;&nbsp;Input Penyamaan Feeling
				</a>
				<a class="btn btn-danger pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/feeling/report')}}">
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
		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
			<table id="tableCode" class="table table-bordered table-hover" style="margin-bottom: 0;background-color: #f0f0ff">
				<thead style="background-color: #0073b7;color: white" id="headTableCode">
				</thead>
				<tbody id="bodyTableCode">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1400px">
        <div class="modal-content">
          <div class="modal-header" style="background-color: skyblue">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul"></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12" style="overflow-x: scroll;">
              	<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
	              <thead>
	              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
	                <th style="width:1%;">#</th>
					<th style="width:1%">Cat</th>
					<th style="width:1%">Tema</th>
					<th style="width:1%">Materi</th>
					<th style="width:1%">Metode</th>
					<th style="width:1%">NG</th>
					<th style="width:1%">Area</th>
					<th style="width:1%">Cara Kensa</th>
					<th style="width:1%">Gendo</th>
					<th style="width:3%">Answer</th>
					<th style="width:1%">Auditor</th>
					<th style="width:3%">Auditee</th>
					<th style="width:3%">Note</th>
					<th style="width:3%">Evidence</th>
	              </tr>
	              </thead>
	              <tbody id="body-detail">
	                
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
		fillList();
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	var all_audit = null;

	function fillList(){
		$('#loading').show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			auditor:$('#auditor').val(),
		}
		$.get('{{ url("fetch/qa/feeling") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var totals = [];
				var datas = [];

				var date = [];
				var date_name = [];

				all_audit = result.audit;

				for(var i = 0; i < result.audit.length;i++){
					date.push(result.audit[i].date);
					date_name.push(result.audit[i].date_audit_name);
				}
				var date_unik = date.filter(onlyUnique);
				var date_name_unik = date_name.filter(onlyUnique);

				for(var i = 0; i < date_unik.length;i++){
					categories.push(date_name_unik[i]);
					var date = '';
					var auditee = [];
					for(var j = 0; j < result.audit.length;j++){
						if (result.audit[j].date == date_unik[i]) {
							auditee.push(result.audit[j].auditee_id);
							date = result.audit[j].date;
						}
					}
					var auditee_unik = auditee.filter(onlyUnique);
					datas.push({
						y:parseInt(auditee_unik.length),
						key:date
					});
				}

				var auditor_status = '';
				if (result.auditorsss != null) {
					auditor_status = 'AUDITOR '+result.auditorsss[0].auditor_name.toUpperCase();
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
				        text: 'PENYAMAAN FEELING QA',
						style:{
							fontWeight:'bold'
						}
				    },
				    subtitle: {
				        text: auditor_status,
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
						data: datas,
						name: 'Penyamaan Feeling',
						colorByPoint: false,
						color:'#32a852'
					}
					// ,{
					// 	type: 'column',
					// 	data: ngs,
					// 	name: 'Audit Sudah Dilakukan (Temuan NG)',
					// 	colorByPoint: false,
					// 	color:'#a60000'
					// }
					]
				});

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				// headTableData += '<th style="width:1%">Cat</th>';
				// headTableData += '<th style="width:1%">Tema</th>';
				// headTableData += '<th style="width:2%">Materi</th>';
				headTableData += '<th style="width:3%">Emp</th>';
				for(var i = 0; i < date_name_unik.length;i++){
					headTableData += '<th style="width:1%;text-align:center;">'+date_name_unik[i].split('-')[0]+'-'+date_name_unik[i].split('-')[1]+'</th>';
				}
				headTableData += '<th style="width:1%;">Total Answer</th>';
				headTableData += '<th style="width:1%;">Correct Answer</th>';
				headTableData += '<th style="width:1%;">Percentage</th>';
				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#tableCode').DataTable().clear();
  			    $('#tableCode').DataTable().destroy();

				$('#bodyTableCode').html("");
				var tableData = "";

				var operator = [];
				var auditors = [];
				for(var i = 0; i < result.audit.length;i++){
					operator.push(result.audit[i].auditee_id);
					auditors.push(result.audit[i].auditor_id);
				}

				var operator_unik = operator.filter(onlyUnique);
				var auditors_unik = auditors.filter(onlyUnique);

				for(var i = 0; i < operator_unik.length;i++){
					var note = [];
					var name = '';
					var auditor = '';
					for(var j = 0; j < result.audit.length;j++){
						if (operator_unik[i] == result.audit[j].auditee_id) {
							name = result.audit[j].auditee_name;
							auditor = result.audit[j].auditor_name;
						}
					}
					tableData += '<tr>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+operator_unik[i]+' - '+name+'</td>';
					var total_ok = 0;
					var total_ng = 0;
					var total_answer = 0;
					for(var k = 0; k < date_name_unik.length;k++){
						// var kehadiran = 'Hadir';
						var bgcolor = '#f0f0ff';
						// var answer = 'ABS';
						var answer = [];
						var kehadiran = [];
						for(var j = 0; j < result.audit.length;j++){
							if (operator_unik[i] == result.audit[j].auditee_id && date_name_unik[k] == result.audit[j].date_audit_name) {
								kehadiran.push(result.audit[j].auditee_status);
								// if (result.audit[j].auditee_status == 'Hadir') {
									answer.push(result.audit[j].answer);
								// }else{
								// 	kehadiran.push(result.audit[j].auditee_status);
								// }
							}
						}
						if (kehadiran.length > 0) {
							var kehadiran_unik = kehadiran.filter(onlyUnique);
							if (kehadiran_unik.join(',').match(/Hadir/gi)) {
								total_answer++;
								if (answer.join(',').match(/NG/gi)) {
									total_ng++;
									tableData += '<td style="padding-left:10px !important;background-color: #ffbdbd;text-align:center;">&#9747;</td>';
								}else if (answer.join(',').match(/OK/gi)) {
									total_ok++;
									tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
								}else{
									tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">Tidak Hadir</td>';
								}
							}else{
								tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+kehadiran_unik.join(',')+'</td>';
							}
						}else{
							tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">Tidak Hadir</td>';
						}
					}
					tableData += '<td style="padding-right:10px !important;background-color: #f0f0ff;text-align:right;font-weight:bold;font-size:18px;">'+total_answer+'</td>';
					tableData += '<td style="padding-right:10px !important;background-color: #f0f0ff;text-align:right;font-weight:bold;font-size:18px;">'+total_ok+'</td>';
					if (total_ok == 0) {
						tableData += '<td style="padding-right:10px !important;background-color: #f0f0ff;text-align:right;font-weight:bold;font-size:18px;">0 %</td>';
					}else{
						tableData += '<td style="padding-right:10px !important;background-color: #f0f0ff;text-align:right;font-weight:bold;font-size:18px;">'+((total_ok/total_answer)*100).toFixed(0)+' %</td>';
					}
					tableData += '</tr>';
				}

				$('#bodyTableCode').append(tableData);

				$('#auditor').html('');
				var auditorss = '';
				auditorss += '<option value=""></option>';
				for(var i = 0; i < result.auditor.length;i++){
					auditorss += '<option value="'+result.auditor[i].auditor_id+'">'+result.auditor[i].auditor_name+'</option>';
				}
				$('#auditor').append(auditorss);

				var table = $('#tableCode').DataTable({
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
		            'pageLength': 10,
		            'searching': true ,
		            'ordering': true,
		            'order': [],
		            'info': true,
		            'autoWidth': true,
		            "sPaginationType": "full_numbers",
		            "bJQueryUI": true,
		            "bAutoWidth": false,
		            "processing": true
		          });
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function showModal(date_name,name,date) {
		$('#data-log').DataTable().clear();
		$('#data-log').DataTable().destroy();

		$('#body-detail').html("");
		var tableData = "";

		var index = 1;

		for(var i = 0; i < all_audit.length;i++){
				if (all_audit[i].date == date) {
					tableData += '<tr id="'+all_audit[i].material_number+'">';
					tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+all_audit[i].category+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].content || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].materi || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].metode || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].ng_name || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].area || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].standard || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(all_audit[i].gendo || '')+'</td>';
					if (all_audit[i].auditee_status == 'Hadir') {
						if (all_audit[i].answer.match(/NG/gi)) {
							tableData += '<td style="padding-left:10px !important;background-color: #ffbdbd;text-align:center;">&#9747;</td>';
						}else if (all_audit[i].answer.match(/OK/gi)) {
							tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
						}else{
							tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">Tidak Ada</td>';
						}
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+all_audit[i].auditee_status+'</td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+all_audit[i].auditor_id+'<br>'+all_audit[i].auditor_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+all_audit[i].auditee_id+'<br>'+all_audit[i].auditee_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+(all_audit[i].note || '')+'</td>';
					var url = '{{url("data_file/qa/feeling")}}/'+all_audit[i].images;
					tableData += '<img src="'+url+'" style="width:100px">';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
		}

		$('#body-detail').append(tableData);

		var table = $('#data-log').DataTable({
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
            'pageLength': 10,
            'searching': true ,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });
		$('#judul').html(name+'<br>Tanggal '+date_name);
		$('#modalDetail').modal('show');
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
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