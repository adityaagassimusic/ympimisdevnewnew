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
	#data-log-peserta-detail_info,#data-log-peserta-detail_filter,#data-log-peserta_info,#data-log-peserta_filter{
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
					<input type="text" class="form-control datepicker" id="month_from" name="month_from" placeholder="Select Audit Date From">
				</div>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="month_to" name="month_to" placeholder="Select Audit Date To">
				</div>
			</div>
			<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Document IK" id="document_number">
					<option value=""></option>
					@foreach($point as $point)
					<option value="{{$point->document_number}}">{{$point->document_number}} - {{$point->document_name}}</option>
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
				<a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/ik/audit')}}">
					<i class="fa fa-pencil"></i>&nbsp;&nbsp;Lakukan Audit IK
				</a>
				<a class="btn btn-danger pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/ik/report')}}">
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

	<div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1400px">
        <div class="modal-content">
          <div class="modal-header" style="background-color: skyblue">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul"></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12" style="overflow-x: scroll;">
              	<table id="data-log-peserta" class="table table-striped table-bordered" style="width: 100%;">
	              <thead>
	              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
	                <th style="width:1%;">#</th>
					<th style="width:3%">ID</th>
					<th style="width:3%">Name</th>
					<th style="width:3%">Attendance</th>
	              </tr>
	              </thead>
	              <tbody id="body-detail-peserta">
	                
	              </tbody>
	              </table>
              </div>
              <div class="col-xs-12" style="overflow-x: scroll;">
              	<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
	              <thead>
	              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
	                <th style="width:1%;">#</th>
					<th style="width:2%">Proses</th>
					<th style="width:2%">Point Pekerjaan</th>
					<th style="width:2%">Safety Point</th>
					<th style="width:1%">Hasil</th>
					<th style="width:1%">Note</th>
					<th style="width:1%">Auditor</th>
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
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		fillList();
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	var audit_all = null;

	function fillList(){
		$('#loading').show();
		var data = {
			month_from:$('#month_from').val(),
			month_to:$('#month_to').val(),
			document_number:$('#document_number').val(),
		}
		$.get('{{ url("fetch/qa/ik") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var datas = [];
				var datas_ng = [];

				for(var i = 0; i < result.audit.length;i++){
					categories.push(result.audit[i].month_name);
					datas.push({
						y:parseInt(result.audit[i].qty_ok),
						key:result.audit[i].month
					});
					datas_ng.push({
						y:parseInt(result.audit[i].qty_ng),
						key:result.audit[i].month
					});
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
					}
					],
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'12px',
						},
					},	
				    title: {
				        text: 'AUDIT IK LEADER',
						style:{
							fontWeight:'bold'

						}
				    },
				    subtitle: {
				        text: result.monthTitle,
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
										filterChart(this.category,this.series.name,this.options.key);
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
						name: 'Audit IK OK',
						colorByPoint: false,
						color:'#32a852'
					},
					{
						type: 'column',
						data: datas_ng,
						name: 'Audit IK NG',
						colorByPoint: false,
						color:'#a60000'
					}
					]
				});

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th style="width:1%">#</th>';
				headTableData += '<th style="width:1%">Periode</th>';
				headTableData += '<th style="width:1%">Date Audit</th>';
				headTableData += '<th style="width:3%;">Document No.</th>';
				headTableData += '<th style="width:7%;">Title</th>';
				headTableData += '<th style="width:2%;">Overall Result</th>';
				headTableData += '<th style="width:5%;">Auditor</th>';
				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#tableCode').DataTable().clear();
  			    $('#tableCode').DataTable().destroy();

				$('#bodyTableCode').html("");
				var tableData = "";
				var index = 1;
				for(var i = 0; i < result.audit_all.length;i++){
					tableData += '<tr style="cursor:pointer" onclick="showModal(\''+result.audit_all[i].audit_id+'\',\''+result.audit_all[i].document_number+'\',\''+result.audit_all[i].document_name+'\',\''+result.audit_all[i].dates+'\')" id="'+result.audit_all[i].month+'">';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:left;">'+result.audit_all[i].month_name+'</td>';
					tableData += '<td style="padding-right:10px !important;background-color: #f0f0ff;text-align:right;">'+result.audit_all[i].dates+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:left;">'+result.audit_all[i].document_number+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:left;">'+result.audit_all[i].document_name+'</td>';
					if (result.audit_all[i].result == 'OK') {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:left;">'+result.audit_all[i].auditor_id+' - '+result.audit_all[i].auditor_name+'</td>';
					tableData += '</tr>';
					index++;
				}

				$('#bodyTableCode').append(tableData);

				$('#select_month').html('');
				var months = '';
				months += '<option value=""></option>';
				for(var i = 0; i < result.month_all.length;i++){
					months += '<option value="'+result.month_all[i].month+'">'+result.month_all[i].month_name+'</option>';
				}
				$('#select_month').append(months);

				// var table = $('#tableCode').DataTable({
		  //           'dom': 'Bfrtip',
		  //           'responsive':true,
		  //           'lengthMenu': [
		  //           [ 10, 25, 50, -1 ],
		  //           [ '10 rows', '25 rows', '50 rows', 'Show all' ]
		  //           ],
		  //           'buttons': {
		  //             buttons:[
		  //             {
		  //               extend: 'pageLength',
		  //               className: 'btn btn-default',
		  //             },
		  //             {
		  //               extend: 'copy',
		  //               className: 'btn btn-success',
		  //               text: '<i class="fa fa-copy"></i> Copy',
		  //                 exportOptions: {
		  //                   columns: ':not(.notexport)'
		  //               }
		  //             },
		  //             {
		  //               extend: 'excel',
		  //               className: 'btn btn-info',
		  //               text: '<i class="fa fa-file-excel-o"></i> Excel',
		  //               exportOptions: {
		  //                 columns: ':not(.notexport)'
		  //               }
		  //             },
		  //             {
		  //               extend: 'print',
		  //               className: 'btn btn-warning',
		  //               text: '<i class="fa fa-print"></i> Print',
		  //               exportOptions: {
		  //                 columns: ':not(.notexport)'
		  //               }
		  //             }
		  //             ]
		  //           },
		  //           'paging': true,
		  //           'lengthChange': true,
		  //           'pageLength': 10,
		  //           'searching': true ,
		  //           'ordering': true,
		  //           'order': [],
		  //           'info': true,
		  //           'autoWidth': true,
		  //           "sPaginationType": "full_numbers",
		  //           "bJQueryUI": true,
		  //           "bAutoWidth": false,
		  //           "processing": true
		  //         });

				audit_all = result.audits;
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
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

	function filterChart(month_name,status,month) {
		$('#select_month').val(month).trigger('change');
	}

	function showModal(audit_id,document_number,document_name,dates) {
		$('#loading').show();
		$('#data-log').DataTable().clear();
		$('#data-log').DataTable().destroy();
		$('#body-detail').html("");
		var tableData = "";

		var index = 1;

		var auditee_id = '';
		var auditee_name = '';
		var auditee_status = '';

		for(var i = 0; i < audit_all.length;i++){
			if (audit_all[i].audit_id == audit_id) {
				tableData += '<tr>';
				tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
				tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(audit_all[i].work_process || '')+'</td>';
				tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(audit_all[i].work_point || '')+'</td>';
				tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(audit_all[i].work_safety || '')+'</td>';
				if (audit_all[i].decision == 'OK') {
					tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
				}else{
					tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
				}
				tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+(audit_all[i].note || '')+'</td>';
				tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+audit_all[i].auditor_id+'<br>'+audit_all[i].auditor_name+'</td>';
				tableData += '</tr>';
				index++;
				auditee_id = audit_all[i].auditee_id;
				auditee_name = audit_all[i].auditee_name;
				auditee_status = audit_all[i].auditee_status;
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

		$('#data-log-peserta').DataTable().clear();
		$('#data-log-peserta').DataTable().destroy();
		$('#body-detail-peserta').html("");

		var tableData = "";

		var index = 1;
		var tableData = "";

		var auditee_ids = auditee_id.split(',');
		var auditee_names = auditee_name.split(',');
		var auditee_statuss = auditee_status.split(',');

		for(var i = 0; i < auditee_ids.length;i++){
			tableData += '<tr>';
			tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
			tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(auditee_ids[i] || '')+'</td>';
			tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(auditee_names[i] || '')+'</td>';
			tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(auditee_statuss[i] || '')+'</td>';
			tableData += '</tr>';
			index++;
		}

		$('#body-detail-peserta').append(tableData);

		var table = $('#data-log-peserta').DataTable({
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
		$('#judul').html('Audit IK '+document_number+' - '+document_name+'<br>Tanggal '+dates);
		$('#loading').hide();
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