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
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Select Product" id="product">
					<option value=""></option>
					<option value="Recorder">Recorder</option>
					<option value="Pianica">Pianica</option>
					<option value="043">Saxophone</option>
					<option value="041">Flute</option>
					<option value="042">Clarinet</option>
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<select class="form-control select2" style="width: 100%" data-placeholder="Select Point Check EI" id="material_number">
					<option value=""></option>
					@foreach($point as $point)
					<option value="{{$point->material_number}}">
						@if(str_contains($point->material_number,','))
						<?php $material_number = explode(',', $point->material_number);
						$material_description = explode(',', $point->material_description);
						$material = [];
						for ($i=0; $i < count($material_number); $i++) { 
							array_push($material, $material_number[$i].' - '.$material_description[$i]);
						}
						echo join(',',$material);
						?>
						@else
						{{$point->material_number}} - {{$point->material_description}}
						@endif
					</option>
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
				@if($emp->department == 'Standardization Department' || $emp->department == 'Management Information System Department')
				<a class="btn btn-info pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('index/qa/audit_fg/point_check')}}">
					<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Point Check EI
				</a>
				<!-- <a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/audit_fg/audit')}}">
					<i class="fa fa-pencil"></i>&nbsp;&nbsp;Audit
				</a> -->
				<button class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" onclick="$('#modalAudit').modal('show')">
					<i class="fa fa-pencil"></i>&nbsp;&nbsp;Audit
				</button>
				<!-- <a class="btn btn-danger pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" href="{{url('index/qa/audit_fg/report')}}">
					<i class="fa fa-book"></i>&nbsp;&nbsp;Report
				</a> -->
				<button class="btn btn-danger pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);margin-right: 5px;" onclick="$('#modalReport').modal('show')">
					<i class="fa fa-book"></i>&nbsp;&nbsp;Report
				</button>
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
              <div class="col-xs-12" id="div_resume" style="overflow-x: scroll;">
              	<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
	              <thead>
	              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
	                <th style="width:1%;">#</th>
					<th style="width:1%">Product</th>
					<th style="width:1%">Material Audited</th>
					<th style="width:1%">Sesi</th>
					<th style="width:1%">SN</th>
					<th style="width:1%">Qty Lot</th>
					<th style="width:1%">Qty Check</th>
					<th style="width:3%">Auditor</th>
					<th style="width:1%">Qty Auditor</th>
					<th style="width:3%">Auditee</th>
					<th style="width:1%">Hasil</th>
					<th style="width:3%">Action</th>
	              </tr>
	              </thead>
	              <tbody id="body-detail">
	                
	              </tbody>
	              </table>
              </div>
              <div class="col-xs-12" id="div_detail" style="overflow-x: scroll;">
              	<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;margin-bottom: 10px;">
              		<button class="btn btn-danger" onclick="backToDetail()"><i class="fa fa-arrow-circle-o-left"></i> Back</button>
              	</div>
              	<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
              		<table id="data-log-detail" class="table table-striped table-bordered" style="width: 100%;">
		              	<thead>
			              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
			                <th style="width:1%;">#</th>
							<th style="width:1%">Product</th>
							<th style="width:1%">Material Audited</th>
							<th style="width:1%">SN</th>
							<th style="width:3%">Point Check</th>
							<th style="width:3%">Standard</th>
							<th style="width:3%">Result</th>
							<th style="width:3%">Result Detail</th>
							<th style="width:3%">NG Detail</th>
							<th style="width:3%">Note</th>
							<th style="width:3%">Auditor</th>
							<th style="width:3%">Auditee</th>
							<th style="width:3%">Penanganan</th>
							<th style="width:3%">Detail Penanganan</th>
			              </tr>
			            </thead>
			            <tbody id="body-detail-detail">
			                
			            </tbody>
		            </table>

		            <table id="data-log-detail-ei" class="table table-striped table-bordered" style="width: 100%;">
		              	<thead>
			              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
			                <th style="width:1%;">#</th>
							<th style="width:1%">Product</th>
							<th style="width:1%">Material Audited</th>
							<th style="width:1%">Sesi</th>
							<th style="width:3%">Urutan</th>
							<th style="width:3%">Point Check</th>
							<th style="width:3%">Standard</th>
							<th style="width:3%">Def. Cat.</th>
							<th style="width:3%">Qty Check</th>
							<th style="width:3%">Qty NG</th>
							<th style="width:3%">NG Detail</th>
							<th style="width:3%">Cav Head</th>
							<th style="width:3%">Cav Middle</th>
							<th style="width:3%">Cav Foot</th>
							<th style="width:3%">Note</th>
							<th style="width:3%">Status Lot</th>
							<th style="width:3%">Auditor</th>
							<th style="width:3%">Qty Auditor</th>
							<th style="width:3%">Auditee</th>
							<th style="width:3%">Box</th>
							<th style="width:3%">Penanganan</th>
							<th style="width:3%">Detail Penanganan</th>
			              </tr>
			            </thead>
			            <tbody id="body-detail-detail-ei">
			                
			            </tbody>
		            </table>
              	</div>
              </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="modalAudit" style="color: black;">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style="background-color: lightgreen">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">Pilih Lokasi Audit</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
              	<a class="btn btn-success" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/qa/audit_fg/audit')}}">
	              	Pianica
	            </a>
	            <a class="btn btn-success" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/qa/audit_fg/audit')}}">
	              	Recorder
	            </a>
	            <a class="btn btn-success" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/cl/kensa/qa-audit')}}">
	              	Clarinet
	            </a>
	            <a class="btn btn-success" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/sax/kensa/qa-audit')}}">
	              	Saxophone
	            </a>
	            <a class="btn btn-success" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/kensa/qa-audit')}}">
	              	Flute
	            </a>
              </div>
	      </div>
	      <div class="modal-footer">
	        <div class="row">
	        	<button type="button" class="btn btn-danger pull-right" style="width: 100%" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
</div>

	<div class="modal fade" id="modalReport" style="color: black;">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style="background-color: skyblue">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">Pilih Lokasi Report</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
              	<a class="btn btn-primary" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/qa/audit_fg/report/pianica')}}">
	              	Pianica
	            </a>
	            <a class="btn btn-primary" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/qa/audit_fg/report/recorder')}}">
	              	Recorder
	            </a>
	            <a class="btn btn-primary" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/assembly/report_qa_audit/042')}}">
	              	Clarinet
	            </a>
	            <a class="btn btn-primary" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/assembly/report_qa_audit/043')}}">
	              	Saxophone
	            </a>
	            <a class="btn btn-primary" style="width: 100%;font-size: 18px;font-weight: bold;margin-bottom: 10px;" href="{{url('index/assembly/report_qa_audit/041')}}">
	              	Flute
	            </a>
              </div>
	      </div>
	      <div class="modal-footer">
	        <div class="row">
	        	<button type="button" class="btn btn-danger pull-right" style="width: 100%" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
	        </div>
	      </div>
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
	var arr_sudah = null;
	var arr_belum = null;
	var kataconfirm = 'Apakah Anda yakin?';
	var packing_detail_ei = null;
	var packing_detail_wi = null;
	var emp = null;

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
		packing_detail_ei = null;
		packing_detail_wi = null;
		emp = null;
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function backToDetail() {
		$('#div_resume').show();
		$('#div_detail').hide();
		$('#data-log-detail').DataTable().clear();
		$('#data-log-detail').DataTable().destroy();
		$('#body-detail-detail').html('');
	}

	function fillList(){
		$('#loading').show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			material_number:$('#material_number').val(),
			product:$('#product').val(),
		}
		$.get('{{ url("fetch/qa/audit_fg") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var oks = [];
				var ngs = [];

				var date = [];
				var date_name = [];

				for(var i = 0; i < result.ei.length;i++){
					date.push(result.ei[i].date_audit);
					date_name.push(result.ei[i].date_audit_name);
				}
				for(var i = 0; i < result.wi.length;i++){
					date.push(result.wi[i].date_audit);
					date_name.push(result.wi[i].date_audit_name);
				}
				var date_unik = date.filter(onlyUnique);
				var date_name_unik = date_name.filter(onlyUnique);

				for(var i = 0; i < date_unik.length;i++){
					categories.push(date_name_unik[i]);
					var ok = 0;
					var ng = 0;
					for(var j = 0; j < result.ei.length;j++){
						if (result.ei[j].date_audit == date_unik[i]) {
							if (result.ei[j].result_check.match(/NG/gi) && result.ei[j].handled_id == null) {
								ng++;
							}else{
								ok++;
							}
						}
					}
					for(var j = 0; j < result.wi.length;j++){
						if (result.wi[j].date_audit == date_unik[i]) {
							if (result.wi[j].result_check.match(/NG/gi) && result.wi[j].handled_id == null) {
								ng++;
							}else{
								ok++;
							}
						}
					}
					oks.push({y:parseInt(ok),key:date_unik[i]});
					ngs.push({y:parseInt(ng),key:date_unik[i]});
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
				        text: '<b>AUDIT FG / KD QA',
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
						data: oks,
						name: 'Audit Sudah Dilakukan (OK & Sudah Ditangani)',
						colorByPoint: false,
						color:'#32a852'
					},{
						type: 'column',
						data: ngs,
						name: 'Audit Sudah Dilakukan (Temuan NG)',
						colorByPoint: false,
						color:'#a60000'
					}
					]
				});

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th style="width:1%">Date</th>';
				headTableData += '<th style="width:1%">Product</th>';
				headTableData += '<th style="width:1%">Material Audited</th>';
				headTableData += '<th style="width:1%">SN</th>';
				headTableData += '<th style="width:3%">Auditor</th>';
				headTableData += '<th style="width:3%">Auditee</th>';
				headTableData += '<th style="width:1%">Hasil</th>';
				headTableData += '<th style="width:1%">Status</th>';
				headTableData += '<th style="width:3%">Action</th>';

				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#tableCode').DataTable().clear();
  			    $('#tableCode').DataTable().destroy();

				$('#bodyTableCode').html("");
				var tableData = "";

				for(var i = 0; i < result.ei.length;i++){
					if (result.ei[i].result_check.match(/NG/gi) && result.ei[i].handled_id == null) {
						tableData += '<tr id="'+result.ei[i].material_number+'">';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.ei[i].date_audit_name+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.ei[i].product+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.ei[i].material_audited || '')+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.ei[i].serial_number || '')+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.ei[i].auditor_id+'<br>'+result.ei[i].auditor_name+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.ei[i].auditee_id+'<br>'+result.ei[i].auditee_name+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;font-weight:bold;text-align:center"><span class="label label-danger">Open</span><br><span style="font-weight:normal">Due Date : '+result.ei[i].due_date+'</span></td>';
						tableData += '<td style="background-color: #f0f0ff;font-weight:bold;text-align:center">';
						if (result.ei[i].send_status == null && result.ei[i].handled_id == null) {
							if (result.ei[i].product == 'Pianica' || result.ei[i].product == 'Recorder') {
								tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmailEi(\''+result.ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Send Email</button>';
							}else{
								tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmail(\''+result.ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Send Email</button>';
							}
						}else{
							var url = '{{url("index/qa/packing/send_email")}}/'+result.ei[i].audit_id;
							if (result.ei[i].product == 'Pianica' || result.ei[i].product == 'Recorder') {
								tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmailEi(\''+result.ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Re-Send Email</button>';
							}else{
								tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmail(\''+result.ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Re-Send Email</button>';
							}
						}
						if (result.ei[i].product == 'Pianica' || result.ei[i].product == 'Recorder') {
							var url = '{{url("index/qa/audit_fg/pdf")}}/'+result.ei[i].audit_id;
							tableData += '<a class="btn btn-danger btn-xs" target="_blank" href="'+url+'" style="margin-right:5px;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
							var url = '{{url("index/qa/packing/handling")}}/'+result.ei[i].audit_id;
							tableData += '<a class="btn btn-success btn-xs" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Penanganan</a>';
						}else{
							var url = '{{url("index/qa/packing/pdf")}}/'+result.ei[i].audit_id;
							tableData += '<a class="btn btn-danger btn-xs" target="_blank" href="'+url+'" style="margin-right:5px;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
							var url = '{{url("index/qa/packing/handling")}}/'+result.ei[i].audit_id;
							tableData += '<a class="btn btn-success btn-xs" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Penanganan</a>';
						}
						tableData += '</td>';
						tableData += '</tr>';
					}
				}

				$('#bodyTableCode').append(tableData);

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
				packing_detail_ei = result.ei;
				packing_detail_wi = result.wi;
				emp = result.emp;
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function sendEmailEi(audit_id) {
		$('#loading').show();
		$.get('{{ url("sendemail/qa/audit_fg") }}/'+audit_id, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!','Success Send Email');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function showModal(date_name,condition,date) {
		$('#data-log').DataTable().clear();
		$('#data-log').DataTable().destroy();

		$('#body-detail').html("");
		var tableData = "";

		var index = 1;

		for(var i = 0; i < packing_detail_ei.length;i++){
			if (condition == 'Audit Sudah Dilakukan (OK & Sudah Ditangani)' && packing_detail_ei[i].date_audit == date) {
				if ((packing_detail_ei[i].result_check.match(/NG/gi) && packing_detail_ei[i].handled_id != null) || packing_detail_ei[i].result_check == 'OK' || packing_detail_ei[i].result_check == 'OK,Tidak Ada') {
					tableData += '<tr id="'+packing_detail_ei[i].material_number+'">';
					tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].product+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].material_audited || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].session || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].serial_number || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_lot || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_check || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].auditor_id+'<br>'+packing_detail_ei[i].auditor_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_auditor || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].auditee_id+'<br>'+packing_detail_ei[i].auditee_name+'</td>';
					if (packing_detail_ei[i].result_check.match(/NG/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}else if (packing_detail_ei[i].result_check.match(/OK/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">Tidak Ada</td>';
					}
					tableData += '<td style="background-color: #f0f0ff;font-weight:bold;text-align:center">';
					tableData += '<button class="btn btn-info btn-xs" style="margin-right:5px;" onclick="detailFindingEi(\''+packing_detail_ei[i].audit_id+'\')"><i class="fa fa-eye"></i> Detail</button>';
					if ('{{$employee_id}}' == 'PI0904005' || '{{$employee_id}}' == 'PI1910002') {
						var url = '{{url("index/qa/audit_fg/edit")}}/'+packing_detail_ei[i].audit_id;
						tableData += '<a class="btn btn-warning btn-xs" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</a>';
						var url = '{{url("index/qa/audit_fg/delete")}}/'+packing_detail_ei[i].audit_id;
						tableData += '<button style="margin-bottom:2px;" onclick="deleteAudit(\''+url+'\')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</button>';
					}
					var url = '{{url("index/qa/audit_fg/pdf")}}/'+packing_detail_ei[i].audit_id;
					tableData += '<a class="btn btn-danger btn-xs" target="_blank" href="'+url+'" style="margin-right:5px;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
			}
			if (condition == 'Audit Sudah Dilakukan (Temuan NG)') {
				if (packing_detail_ei[i].result_check.match(/NG/gi) && packing_detail_ei[i].handled_id == null && packing_detail_ei[i].date_audit == date) {
					tableData += '<tr id="'+packing_detail_ei[i].material_number+'">';
					tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].product+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].material_audited || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].session || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].serial_number || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_lot || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_check || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].auditor_id+'<br>'+packing_detail_ei[i].auditor_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_ei[i].qty_auditor || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_ei[i].auditee_id+'<br>'+packing_detail_ei[i].auditee_name+'</td>';
					if (packing_detail_ei[i].result_check.match(/NG/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}else if (packing_detail_ei[i].result_check.match(/OK/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">Tidak Ada</td>';
					}
					tableData += '<td style="background-color: #f0f0ff;font-weight:bold;text-align:center">';
					tableData += '<button class="btn btn-info btn-xs" style="margin-right:5px;" onclick="detailFindingEi(\''+packing_detail_ei[i].audit_id+'\')"><i class="fa fa-eye"></i> Detail</button>';
					if ('{{$employee_id}}' == 'PI0904005' || '{{$employee_id}}' == 'PI1910002') {
						var url = '{{url("index/qa/audit_fg/edit")}}/'+packing_detail_ei[i].audit_id;
						tableData += '<a class="btn btn-warning btn-xs" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</a>';
						var url = '{{url("index/qa/audit_fg/delete")}}/'+packing_detail_ei[i].audit_id;
						tableData += '<button style="margin-bottom:2px;" onclick="deleteAudit(\''+url+'\')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</button>';
					}
					if (packing_detail_ei[i].send_status == null && packing_detail_ei[i].handled_id == null) {
						tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmailEi(\''+packing_detail_ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Send Email</button>';
					}else{
						tableData += '<button class="btn btn-primary btn-xs" onclick="sendEmailEi(\''+packing_detail_ei[i].audit_id+'\')" style="margin-right:5px;"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Re-Send Email</button>';
					}
					var url = '{{url("index/qa/audit_fg/pdf")}}/'+packing_detail_ei[i].audit_id;
					tableData += '<a class="btn btn-danger btn-xs" target="_blank" href="'+url+'" style="margin-right:5px;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
					var url = '{{url("index/qa/packing/handling")}}/'+packing_detail_ei[i].audit_id;
					tableData += '<a class="btn btn-success btn-xs" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Penanganan</a>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
			}
		}

		for(var i = 0; i < packing_detail_wi.length;i++){
			if (condition == 'Audit Sudah Dilakukan (OK & Sudah Ditangani)' && packing_detail_wi[i].date_audit == date) {
				if ((packing_detail_wi[i].result_check.match(/NG/gi) && packing_detail_wi[i].handled_id != null) || packing_detail_wi[i].result_check == 'OK' || packing_detail_wi[i].result_check == 'OK,Tidak Ada') {
					tableData += '<tr id="'+packing_detail_wi[i].material_number+'">';
					tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					if (packing_detail_wi[i].product == '041') {
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Flute</td>';
					}else if (packing_detail_wi[i].product == '042') {
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Clarinet</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Saxophone</td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].material_audited || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].session || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].serial_number || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_lot || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_check || '')+'</td>';
					var auditor_name = '';
					for(var k = 0; k < emp.length;k++){
						if (emp[k].employee_id == packing_detail_wi[i].auditor_id) {
							auditor_name = emp[k].name;
						}
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_wi[i].auditor_id+'<br>'+auditor_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_auditor || '')+'</td>';
					var auditee_id = packing_detail_wi[i].auditee_id.split('_');
					if (auditee_id.length > 1) {
						var auditee_name = [];
						for(var u = 0; u < auditee_id.length;u++){
							for(var k = 0; k < emp.length;k++){
								if (emp[k].employee_id == auditee_id[u]) {
									auditee_name.push(auditee_id[u]+' - '+emp[k].name);
								}
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+auditee_name.join('<br>')+'</td>';
					}else{
						var auditee_name = '';
						for(var k = 0; k < emp.length;k++){
							if (emp[k].employee_id == packing_detail_wi[i].auditee_id) {
								auditee_name = emp[k].name;
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_wi[i].auditee_id.replace("_", ",")+'<br>'+auditee_name+'</td>';
					}
					if (packing_detail_wi[i].result_check.match(/NG/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}else if (packing_detail_wi[i].result_check.match(/OK/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">Tidak Ada</td>';
					}
					tableData += '<td style="background-color: #f0f0ff;font-weight:bold;text-align:center">';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
			}
			if (condition == 'Audit Sudah Dilakukan (Temuan NG)') {
				if (packing_detail_wi[i].result_check.match(/NG/gi) && packing_detail_wi[i].handled_id == null && packing_detail_wi[i].date_audit == date) {
					tableData += '<tr id="'+packing_detail_wi[i].material_number+'">';
					tableData += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					if (packing_detail_wi[i].product == '041') {
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Flute</td>';
					}else if (packing_detail_wi[i].product == '042') {
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Clarinet</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">Saxophone</td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].material_audited || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].session || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].serial_number || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_lot || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_check || '')+'</td>';
					var auditor_name = '';
					for(var k = 0; k < emp.length;k++){
						if (emp[k].employee_id == packing_detail_wi[i].auditor_id) {
							auditor_name = emp[k].name;
						}
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_wi[i].auditor_id+'<br>'+auditor_name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(packing_detail_wi[i].qty_auditor || '')+'</td>';
					var auditee_id = packing_detail_wi[i].auditee_id.split('_');
					if (auditee_id.length > 1) {
						var auditee_name = [];
						for(var u = 0; u < auditee_id.length;u++){
							for(var k = 0; k < emp.length;k++){
								if (emp[k].employee_id == auditee_id[u]) {
									auditee_name.push(auditee_id[u]+' - '+emp[k].name);
								}
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+auditee_name.join('<br>')+'</td>';
					}else{
						var auditee_name = '';
						for(var k = 0; k < emp.length;k++){
							if (emp[k].employee_id == packing_detail_wi[i].auditee_id) {
								auditee_name = emp[k].name;
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+packing_detail_wi[i].auditee_id.replace("_", ",")+'<br>'+auditee_name+'</td>';
					}
					if (packing_detail_wi[i].result_check.match(/NG/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}else if (packing_detail_wi[i].result_check.match(/OK/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">Tidak Ada</td>';
					}
					tableData += '<td style="background-color: #f0f0ff;font-weight:bold;text-align:center">';
					tableData += packing_detail_wi[i].ng;
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
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
		$('#judul').html(condition+'<br>Tanggal '+date_name);
		$('#div_detail').hide();
		$('#div_resume').show();
		$('#modalDetail').modal('show');
	}

	function deleteAudit(url) {
		if (confirm(kataconfirm)) {
			window.location.replace(url);
		}
	}

	function detailFindingEi(audit_id) {
		$('#loading').show();
		data = {
			audit_id:audit_id
		}

		$.get('{{ url("fetch/qa/audit_fg/detail") }}',data, function(result, status, xhr){
			if(result.status){
				$('#data-log-detail').DataTable().clear();
				$('#data-log-detail').DataTable().destroy();
				$('#data-log-detail-ei').DataTable().clear();
				$('#data-log-detail-ei').DataTable().destroy();

				$('#body-detail-detail-ei').html('');

				var details = '';
				var index = 1;
				for(var i = 0; i < result.audit.length;i++){
					details += '<tr id="'+result.audit[i].material_number+'">';
					details += '<td style="background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].product+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].material_audited || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+result.audit[i].session+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].point_check+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].point_check_details+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].standard || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+result.audit[i].defect_category+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+result.audit[i].qty_check+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+result.audit[i].qty_ng+'</td>';
					if (result.audit[i].ng_detail == '______') {
						details += '<td style="padding-left:10px !important;background-color: #f0f0ff"></td>';
					}else{
						details += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
						details += 'Nama NG : '+result.audit[i].ng_detail.split('_')[0]+'<br>';
						details += 'Area : '+result.audit[i].ng_detail.split('_')[1]+'<br>';
						details += 'Pallet : '+result.audit[i].ng_detail.split('_')[2]+'<br>';
						details += 'Baris : '+result.audit[i].ng_detail.split('_')[3]+'<br>';
						details += 'Box : '+result.audit[i].ng_detail.split('_')[4]+'<br>';
						details += 'Line : '+result.audit[i].ng_detail.split('_')[5]+'<br>';
						if (result.audit[i].ng_detail.split('_')[6] != '') {
							details += 'Emp : '+result.audit[i].ng_detail.split('_')[6]+' - '+result.audit[i].ng_detail.split('_')[7]+'<br>';
						}
						details += '</td>';
					}
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].cavity_head || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].cavity_middle || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].cavity_foot || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].note || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].status_lot || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].auditor_id+'<br>'+result.audit[i].auditor_name+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].qty_auditor || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.audit[i].auditee_id+'<br>'+result.audit[i].auditee_name+'</td>';
					if (result.audit[i].box_qty != null) {
						details += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
						var box_qty = result.audit[i].box_qty.split(',');
						var box_pic = result.audit[i].box_pic.split(',');
						for(var j = 0; j < box_qty.length;j++){
							details += box_qty[j]+' - '+box_pic[j].split('_')[0]+' - '+box_pic[j].split('_')[2].split(' ').slice(0,2).join(' ')+'<br>';
						}
						details += '</td>';
					}else{
						details += '<td style="padding-left:10px !important;background-color: #f0f0ff"></td>';
					}
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].handling_name || '')+'<br>'+(result.audit[i].handling_at || '')+'</td>';
					details += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(result.audit[i].handling || '')+'</td>';
					details += '</tr>';
					index++;
				}

				$('#body-detail-detail-ei').append(details);

				var table = $('#data-log-detail-ei').DataTable({
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

				$('#div_detail').show();
				$('#data-log-detail-ei').show();
				$('#data-log-detail').hide();
				$('#div_resume').hide();
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
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