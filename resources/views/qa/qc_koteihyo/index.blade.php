
			<!-- <a href="{{ url('index/qa/qc_koteihyo/point_check') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> Point Check </a> -->

			<!-- <a href="{{ url('index/qa/qc_koteihyo/audit') }}" class="btn btn-default btn-block" style="font-size: 1.3vw; border-color: green;"> Audit QC Koteihyo </a> -->

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
		<div class="col-xs-12" style="padding-bottom: 10px;padding-right: 0px;">
			<!-- <div class="col-xs-2" style="padding-left: 0px;padding-right: 5px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Expired Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Expired Date To">
				</div>
			</div> -->
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select class="form-control select2" name="fiscal_year" id="fiscal_year" data-placeholder="Pilih Fiscal Year" style="width: 100%;">
					<option></option>
					@foreach($fy_all as $fy_all)
					<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-3 pull-right" style="padding-left: 5px;padding-right: 0px;">
				<!-- <a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('index/qa/qc_koteihyo/point_check')}}">
					<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Point Check
				</a> -->
				<button class="btn btn-primary pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);" onclick="fillList()">
					<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
				</button>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search <small>検索</small>
				</button>
			</div>
		</div>
		<div class="col-xs-10" style="padding-right: 5px;">
			<div id="container" style="height: 83vh;">
				
			</div>
		</div>
		<div class="col-xs-2" style="padding-right: 0;padding-left:5px;padding-top: 10px;">
	      <div class="small-box" style="background: #f1f2ee; height: 20vh; margin-bottom: 5px;">
	        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
	          <h3 style="margin-bottom: 0px;font-size: 1.2vw;"><b>TOTAL QC KOTEIHYO</b></h3>
	          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>クレーム件数の合計</b></h3>
	          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_ik">0</h5>
	          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="pres">0 %</h4> -->
	        </div>
	        <div class="icon" style="padding-top: 12vh;font-size:8vh">
	          <i class="fa fa-history" ></i>
	        </div>
	      </div>

	      <div class="small-box" style="background: #42a5f5; height: 20vh; margin-bottom: 5px;">
	        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
	          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>FREKUENSI AUDIT</b></h3>
	          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査の頻度</b></h3>
	          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_frek_audit">0</h5>
	          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
	        </div>
	        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
	          <i class="fa fa-list"></i>
	        </div>
	      </div>
	      <div class="small-box" style="background: #cc0000; height: 20vh; margin-bottom: 5px;">
	        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
	          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: white"><b>BELUM AUDIT</b></h3>
	          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;text-shadow: 1px 1px 9px #fff;"><b>監査未実施み</b></h3>
	          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="total_belum">0</h5>
	          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
	        </div>
	        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
	          <i class="fa fa-remove"></i>
	        </div>
	      </div>
	      <div class="small-box" style="background: #00ff73; height: 20vh; margin-bottom: 5px;">
	        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
	          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>SUDAH AUDIT</b></h3>
	          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査実施済</b></h3>
	          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_sudah">0</h5>
	          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: red;" id="persen_belum_vaksin">0 %</h4> -->
	        </div>
	        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
	          <i class="fa fa-check"></i>
	        </div>
	      </div>
	    </div>
		<!-- <div class="col-xs-12" style="margin-top: 0px;padding-top: 0px"> -->
			<!-- <div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;"> -->
					<!-- <div class="col-xs-12"> -->
						<!-- <div class="row"> -->
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
										<!-- <tr> -->
											<!-- <th width="1%">#</th>
											<th width="5%">Certificate No.<br><small>認定番号</small></th>
											<th width="5%">Desc<br><small>内容</small></th>
											<th width="5%">From<br><small>有効日付</small></th>
											<th width="5%">To<br><small>無効日付</small></th>
											<th width="5%">Emp<br><small>従業員</small></th>
											<th width="1%">Status<br><small>ステイタス</small></th>
											<th width="1%">Leader QA<br><small>品質保証リーダー</small></th>
											<th width="1%">Staff QA<br><small>品質保証スタッフ</small></th>
											<th width="1%">Foreman QA<br><small>品質保証工長</small></th>
											<th width="1%">Chief QA<br><small>組立工長</small></th>
											<th width="1%">Foreman Assy<br><small>組立工長</small></th>
											<th width="1%">Manager Assy<br><small>組立課長</small></th>
											<th width="1%">Manager QA<br><small>品質保証課長</small></th>
											<th width="1%">DGM<br><small>副部長</small></th>
											<th width="1%">GM<br><small>部長</small></th>
											<th width="1%">President Director<br><small>社長</small></th>
											<th width="5%">Action<br><small>アクション</small></th> -->
										<!-- </tr> -->
									</thead>
									<tbody id="bodyTableCode">
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
						<!-- </div> -->
					<!-- </div>
				</div>
			</div> -->
		<!-- </div> -->
	</div>

	<div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
          </div>
          <div class="modal-body">
            <div class="row">
            <div class="col-md-12" id="data-activity">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
              <thead>
              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                <th style="width: 1%;">#</th>
                <th style="width: 4%;">Document</th>
                <th style="width: 3%;">Auditee</th>
                <th style="width: 3%;">Auditor</th>
                <th style="width: 1%;">Schedule Date</th>
                <th style="width: 1%;">Status</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		arr_sudah = null;
		arr_belum = null;
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

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
	function fillList(){
		$('#loading').show();
		var data = {
			// tanggal_from:$('#tanggal_from').val(),
			// tanggal_to:$('#tanggal_to').val(),
			// status:$('#status').val(),
			fiscal_year:$('#fiscal_year').val(),
		}
		$.get('{{ url("fetch/qa/qc_koteihyo") }}',data, function(result, status, xhr){
			if(result.status){
				// $('#tableCode').DataTable().clear();
				// $('#tableCode').DataTable().destroy();

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th style="width:1%">Periode</th>';
				headTableData += '<th style="width:4%">Document</th>';
				headTableData += '<th style="width:1%">Area</th>';
				headTableData += '<th style="width:3%">Auditor</th>';
				headTableData += '<th style="width:1%">Plan</th>';
				headTableData += '<th style="width:1%">Actual</th>';
				headTableData += '<th style="width:1%">Hasil</th>';
				headTableData += '<th style="width:1%">Penanganan</th>';
				headTableData += '<th style="width:1%">Action</th>';
				// for(var i = 0; i < result.fy.length;i++){
				// 	// headTableData += '<th>'+result.fy[i].month_name+'</th>';
				// }

				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#bodyTableCode').html("");
				var tableData = "";

				for(var i = 0; i < result.fy.length;i++){
					tableData += '<tr id="'+result.fy[i].month+'">';
					if (result.resumes[i].length > 0) {
						tableData += '<td rowspan="'+result.resumes[i].length+'" style="padding-left:10px !important;background-color: #f0f0ff">'+result.fy[i].month_name+'</td>';
						var url_pdf = '';
						var pdfs = '';
						if (result.resumes[i][0].file_name_pdf != null) {
							var url_pdf = '{{url("files/standardization/documents/")}}/'+result.resumes[i][0].file_name_pdf;
							var pdfs = '<div class="col-xs-1" style="padding-left:0px;padding-right:0px;"><a target="_blank" href="'+url_pdf+'"><i class="fa fa-file-pdf-o"></i></a></div>';
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"><div class="col-xs-11" style="padding-left:0px;padding-right:0px;">'+result.resumes[i][0].document_number+'<br>'+result.resumes[i][0].title+'</div>'+pdfs+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.resumes[i][0].department_shortname+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.resumes[i][0].employee_id+'<br>'+result.resumes[i][0].name+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">1</td>';
						if (result.resumes[i][0].status == 'Belum Dikerjakan') {
							tableData += '<td style="padding-left:10px !important;background-color: #ffd7d4">0</td>';
						}else{
							tableData += '<td style="padding-left:10px !important;background-color: #dfffd4">1</td>';
						}
						var ditangani = null;

						if (result.resumes[i][0].condition == null) {
							tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
							tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
						}else{
							var url_pdf = '{{url("data_file/qc_koteihyo/finding/")}}/'+result.resumes[i][0].file_name_finding;
							var pdfs = '<div class="col-xs-6" style="padding-left:0px;padding-right:0px;"><a class="btn btn-danger btn-xs" target="_blank" href="'+url_pdf+'"><i class="fa fa-file-pdf-o"></i></a></div>';
							if (result.resumes[i][0].condition.match(/NG/gi)){
								tableData += '<td style="text-align:center;background-color: #ff8f8f"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#9747;</div>'+pdfs+'</td>';
								if (result.resumes[i][0].handling == null) {
									tableData += '<td style="text-align:center;background-color: #ff8f8f;">Belum Ditangani<br>Due Date: '+result.resumes[i][0].due_date+'</td>';
									ditangani = false;
								}else{
									var file_auditee = '';
									var file_auditor = '';
									if (result.resumes[i][0].handling_file_auditor != null) {
										var url_pdf_auditor = '{{url("data_file/qc_koteihyo/handling/")}}/'+result.resumes[i][0].handling_file_auditor;
										file_auditor = '<br>File Auditor : <a target="_blank" href="'+url_pdf_auditor+'"><i class="fa fa-file-pdf-o"></i></a>';
									}
									if (result.resumes[i][0].handling_file_auditee != null) {
										var url_pdf_auditee = '{{url("data_file/qc_koteihyo/handling/")}}/'+result.resumes[i][0].handling_file_auditee;
										file_auditee = '<br>File Auditee : <a target="_blank" href="'+url_pdf_auditee+'"><i class="fa fa-file-pdf-o"></i></a>';
									}
									tableData += '<td style="text-align:center;background-color: #dfffd4">Sudah Ditangani<br>'+result.resumes[i][0].handled_at+''+file_auditor+''+file_auditee+'</td>';
									ditangani = true;
								}
							}else if (result.resumes[i][0].condition.match(/NS/gi)){
								tableData += '<td style="text-align:center;background-color: #ff8f8f"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#8420;</div>'+pdfs+'</td>';
								if (result.resumes[i][0].handling == null) {
									tableData += '<td style="text-align:center;background-color: #ff8f8f;">Belum Ditangani<br>Due Date: '+result.resumes[i][0].due_date+'</td>';
									ditangani = false;
								}else{
									tableData += '<td style="text-align:center;background-color: #dfffd4">Sudah Ditangani<br>'+result.resumes[i][0].handled_at+'</td>';
									ditangani = true;
								}
							}else{
								tableData += '<td style="text-align:center;background-color: #dfffd4"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#9711;</div>'+pdfs+'</td>';
								tableData += '<td style="text-align:center;background-color: #dfffd4">TIdak Perlu</td>';
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
						var emp = new RegExp('{{$employee_id}}', 'g');
						if (result.resumes[i][0].status == 'Belum Dikerjakan') {
							if (result.resumes[i][0].auditor_id.match(emp) || '{{$role}}'.match(/MIS/gi)) {
								if (result.resumes[i][0].status_audit == 'Belum') {
									// var url = '{{url("audit/continue/qa/special_process")}}/'+result.resumes[i][0].id_audit;
									// tableData += '<a class="btn btn-warning btn-sm" href="'+url+'"><i class="fa fa-list"></i>&nbsp;&nbsp;Lanjutkan Audit</a>';
								}else{
									var url = '{{url("index/qa/qc_koteihyo/audit")}}/'+result.resumes[i][0].id_audit;
									tableData += '<a class="btn btn-primary btn-sm" href="'+url+'"><i class="fa fa-list"></i>&nbsp;&nbsp;Audit</a>';
								}
							}
						}else{
							var url = '{{url("pdf/qa/qc_koteihyo")}}/'+result.resumes[i][0].schedule_id;
							tableData += '<a class="btn btn-danger btn-sm" target="_blank" style="margin-bottom:5px;" href="'+url+'"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
							if (ditangani == false) {
								var url = '{{url("handling/qa/qc_koteihyo")}}/'+result.resumes[i][0].schedule_id+'/'+'{{$employee_id}}';
								tableData += '<a class="btn btn-warning btn-sm" style="margin-bottom:5px;" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Penanganan</a>';
								if (result.resumes[i][0].send_status == null) {
									var url = '{{url("sendemail/qa/qc_koteihyo")}}/'+result.resumes[i][0].schedule_id;
									tableData += '<button class="btn btn-success btn-sm" onclick="sendEmail(\''+url+'\',\''+result.resumes[i][0].schedule_id+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Kirim Email</button>';
								}else{
									var url = '{{url("sendemail/qa/qc_koteihyo")}}/'+result.resumes[i][0].schedule_id;
									tableData += '<button class="btn btn-success btn-sm" onclick="sendEmail(\''+url+'\',\''+result.resumes[i][0].schedule_id+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Resend Email</button>';
								}
							}
						}
						tableData += '</td>';
					}
					tableData += '</tr>';

					for(var k = 1; k < result.resumes[i].length;k++){
						tableData += '<tr id="'+result.fy[i].month+'">';
						if (result.resumes[i][k].file_name_pdf != null) {
							var url_pdf = '{{url("files/standardization/documents/")}}/'+result.resumes[i][k].file_name_pdf;
							var pdfs = '<div class="col-xs-1" style="padding-left:0px;padding-right:0px;"><a target="_blank" href="'+url_pdf+'"><i class="fa fa-file-pdf-o"></i></a></div>';
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"><div class="col-xs-11" style="padding-left:0px;padding-right:0px;">'+result.resumes[i][k].document_number+'<br>'+result.resumes[i][k].title+'</div>'+pdfs+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.resumes[i][k].department_shortname+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+result.resumes[i][k].employee_id+'<br>'+result.resumes[i][k].name+'</td>';
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">1</td>';
						if (result.resumes[i][k].status == 'Belum Dikerjakan') {
							tableData += '<td style="padding-left:10px !important;background-color: #ffd7d4">0</td>';
						}else{
							tableData += '<td style="padding-left:10px !important;background-color: #dfffd4">1</td>';
						}
						var ditangani = null;

						if (result.resumes[i][k].condition == null) {
							tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
							tableData += '<td style="text-align:center;background-color: #f0f0ff"></td>';
						}else{
							var url_pdf = '{{url("data_file/qc_koteihyo/finding/")}}/'+result.resumes[i][k].file_name_finding;
							var pdfs = '<div class="col-xs-6" style="padding-left:0px;padding-right:0px;"><a class="btn btn-danger btn-xs" target="_blank" href="'+url_pdf+'"><i class="fa fa-file-pdf-o"></i></a></div>';
							if (result.resumes[i][k].condition.match(/NG/gi)){
								tableData += '<td style="text-align:center;background-color: #ff8f8f"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#9747;</div>'+pdfs+'</td>';
								if (result.resumes[i][k].handling == null) {
									tableData += '<td style="text-align:center;background-color: #ff8f8f;">Belum Ditangani<br>Due Date: '+result.resumes[i][k].due_date+'</td>';
									ditangani = false;
								}else{
									var file_auditee = '';
									var file_auditor = '';
									if (result.resumes[i][k].handling_file_auditor != null) {
										var url_pdf_auditor = '{{url("data_file/qc_koteihyo/handling/")}}/'+result.resumes[i][k].handling_file_auditor;
										file_auditor = '<br>File Auditor : <a target="_blank" href="'+url_pdf_auditor+'"><i class="fa fa-file-pdf-o"></i></a>';
									}
									if (result.resumes[i][k].handling_file_auditee != null) {
										var url_pdf_auditee = '{{url("data_file/qc_koteihyo/handling/")}}/'+result.resumes[i][k].handling_file_auditee;
										file_auditee = '<br>File Auditee : <a target="_blank" href="'+url_pdf_auditee+'"><i class="fa fa-file-pdf-o"></i></a>';
									}
									tableData += '<td style="text-align:center;background-color: #dfffd4">Sudah Ditangani<br>'+result.resumes[i][k].handled_at+''+file_auditor+''+file_auditee+'</td>';
									ditangani = true;
								}
							}else if (result.resumes[i][k].condition.match(/NS/gi)){
								tableData += '<td style="text-align:center;background-color: #ff8f8f"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#8420;</div>'+pdfs+'</td>';
								if (result.resumes[i][k].handling == null) {
									tableData += '<td style="text-align:center;background-color: #ff8f8f;">Belum Ditangani<br>Due Date: '+result.resumes[i][k].due_date+'</td>';
									ditangani = false;
								}else{
									tableData += '<td style="text-align:center;background-color: #dfffd4">Sudah Ditangani<br>'+result.resumes[i][k].handled_at+'</td>';
									ditangani = true;
								}
							}else{
								tableData += '<td style="text-align:center;background-color: #dfffd4"><div style="padding-left:0px;padding-right:0px;" class="col-xs-6">&#9711;</div>'+pdfs+'</td>';
								tableData += '<td style="text-align:center;background-color: #dfffd4">TIdak Perlu</td>';
							}
						}
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">';
						var emp = new RegExp('{{$employee_id}}', 'g');
						if (result.resumes[i][k].status == 'Belum Dikerjakan') {
							if (result.resumes[i][k].auditor_id.match(emp) || '{{$role}}'.match(/MIS/gi)) {
								if (result.resumes[i][k].status_audit == 'Belum') {
									// var url = '{{url("audit/continue/qa/special_process")}}/'+result.resumes[i][k].id_audit;
									// tableData += '<a class="btn btn-warning btn-sm" href="'+url+'"><i class="fa fa-list"></i>&nbsp;&nbsp;Lanjutkan Audit</a>';
								}else{
									var url = '{{url("index/qa/qc_koteihyo/audit")}}/'+result.resumes[i][k].id_audit;
									tableData += '<a class="btn btn-primary btn-sm" href="'+url+'"><i class="fa fa-list"></i>&nbsp;&nbsp;Audit</a>';
								}
							}
						}else{
							var url = '{{url("pdf/qa/qc_koteihyo")}}/'+result.resumes[i][k].schedule_id;
							tableData += '<a class="btn btn-danger btn-sm" target="_blank" style="margin-bottom:5px;" href="'+url+'"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Report PDF</a>';
							if (ditangani == false) {
								var url = '{{url("handling/qa/qc_koteihyo")}}/'+result.resumes[i][k].schedule_id+'/'+'{{$employee_id}}';
								tableData += '<a class="btn btn-warning btn-sm" style="margin-bottom:5px;" href="'+url+'"><i class="fa fa-edit"></i>&nbsp;&nbsp;Penanganan</a>';
								if (result.resumes[i][k].send_status == null) {
									var url = '{{url("sendemail/qa/qc_koteihyo")}}/'+result.resumes[i][k].schedule_id;
									tableData += '<button class="btn btn-success btn-sm" onclick="sendEmail(\''+url+'\',\''+result.resumes[i][k].schedule_id+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Kirim Email</button>';
								}else{
									var url = '{{url("sendemail/qa/qc_koteihyo")}}/'+result.resumes[i][k].schedule_id;
									tableData += '<button class="btn btn-success btn-sm" onclick="sendEmail(\''+url+'\',\''+result.resumes[i][k].schedule_id+'\')"><i class="fa fa-envelope"></i>&nbsp;&nbsp;Resend Email</button>';
								}
							}
						}
						tableData += '</td>';
						tableData += '</tr>';
					}
				}

				$('#bodyTableCode').append(tableData);
				$('#loading').hide();

				var category = [];
				var not_yet = [];
				var done = [];
				var done_ns = [];
				var done_ng = [];
				var total_belum = 0;
				var total_sudah = 0;
				var total_audit = 0;
				var total_ik = 0;
				for (var i = 0; i < result.fy.length;i++) {
					category.push(result.fy[i].month_name);
					var dones = 0;
					var not_yets = 0;
					var months = new RegExp(result.fy[i].month, 'g');
					for(var j = 0; j < result.schedule_sudah.length;j++){
						if (result.schedule_sudah[j].schedule_date.match(months)) {
							dones++;
							total_audit++;
							total_sudah++;
						}
					}
					for(var k = 0; k < result.schedule_belum.length;k++){
						if (result.schedule_belum[k].schedule_date.match(months)) {
							not_yets++;
							total_audit++;
							total_belum++;
						}
					}
					done.push({y:dones,key:result.fy[i].month});
					not_yet.push({y:not_yets,key:result.fy[i].month});
				}

				total_ik = result.all_doc.length;

				$("#total_ik").html(total_ik);
				$("#total_frek_audit").html(total_audit);
				$("#total_belum").html(total_belum);
				$("#total_sudah").html(total_sudah);

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
						categories: category,
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
								fontSize:"12px"
							}
						},
						type: 'linear',
						// opposite: true
					}
					],
					tooltip: {
						headerFormat: '<span>{series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						// backgroundColor:
						// Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
						itemStyle: {
							fontSize:'12px',
						},
					},	
				    title: {
				        text: '<b>AUDIT QC KOTEIHYO</b>',
						// style:{
						// 	fontSize:"12px"
						// }
				    },
				    subtitle: {
				        text: '品証のQC工程表'
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
				    series: [{
						type: 'column',
						data: not_yet,
						name: 'Schedule',
						colorByPoint: false,
						color:'#3d53ff'
					}
					,{
						type: 'column',
						data: done,
						name: 'Audit Sudah Dilakukan',
						colorByPoint: false,
						color:'#32a852'
					}
					// ,{
					// 	type: 'column',
					// 	data: done_ns,
					// 	name: 'Audit Sudah Dilakukan (Temuan Belum Sempurna)',
					// 	colorByPoint: false,
					// 	color:'#ffed29'
					// },{
					// 	type: 'column',
					// 	data: done_ng,
					// 	name: 'Audit Sudah Dilakukan (Temuan NG)',
					// 	colorByPoint: false,
					// 	color:'#a60000'
					// }
					]
				});

				arr_sudah = result.schedules_sudah;
				arr_belum = result.schedules_belum;

				$('#select_month').html('');
				var months = '';
				months += '<option value=""></option>';
				for(var i = 0; i < result.fy.length;i++){
					months += '<option value="'+result.fy[i].month+'">'+result.fy[i].month_name+'</option>';
				}
				$('#select_month').append(months);

				// $('#select_document').html('');
				// var docs = '';
				// docs += '<option value=""></option>';
				// for(var i = 0; i < result.document.length;i++){
				// 	docs += '<option value="'+result.document[i].document_number+'">'+result.document[i].document_number+' - '+result.document[i].document_name+'</option>';
				// }
				// $('#select_document').append(docs);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!','Attempt to retrieve data failed');
			}
		});
	}

	function sendEmail(url,id) {
		if (confirm(kataconfirm)) {
			$('#loading').show();
			$.get(url, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Send Email Succeeded');
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			})
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

	function ShowModal(month_name,status,month) {
		location.href='#div_detail'
		$('#select_month').val(month).trigger('change');
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