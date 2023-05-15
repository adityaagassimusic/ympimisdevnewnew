@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.radio {
		display: inline-block;
		position: relative;
		padding-left: 32px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 14px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	.radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span> ~ <span style="font-weight: bold;color: black" id="jishu_hozen_title"></span></small>
		<a class="btn btn-info btn-sm pull-right" style="margin-right: 5px" href="" id="linkPointCheck">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Manage Point Check
		</a>
		<button class="btn btn-primary btn-sm pull-right" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Ganti Jishu Hozen
		</button>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create-modal" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Create Jishu Hozen
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date From" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date To" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/maintenance/jishu_hozen/') }}" class="btn btn-danger">Clear</a>
									<button onclick="fetchJishuHozen()" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker2" id="tgl_print" name="month" placeholder="Pilih Bulan" required autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<button onclick="printPdf($('#tgl_print').val())" class="btn btn-primary col-sm-14">Cetak</button>
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker2" id="date_email" name="date_email" placeholder="Pilih Tanggal" required autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<button onclick="sendEmail()" class="btn btn-primary col-sm-14">Kirim Email</button>
								</div>
							</div>
						</div>
					</div> -->
				</div>
			</div>
		</div>
		<div class="col-xs-12 pull-left">
			<table id="tableJishuHozen" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">No.</th>
						<th width="1%">Check Date</th>
						<th width="2%">Machine</th>
						<th width="1%">Timing</th>
						<th width="1%">Classification</th>
						<th width="3%">Point Check</th>
						<th width="2%">Standard</th>
						<th width="1%">Result</th>
						<th width="2%">PIC</th>
						<th width="2%">At</th>
						<th width="2%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableJishuHozen">
				</tbody>
				<!-- <tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot> -->
			</table>
		</div>
	</div>

	<div class="modal fade" id="modalArea">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div style="background-color: #ffba42;text-align: center;">
							<label style="width: 100%;padding: 3px;font-size: 20px" for="exampleInputEmail1">Pilih Area</label>
						</div>
						<div class="form-group" style="margin-top: 10px">
							<select class="form-control selectArea" data-placeholder="Pilih Area" style="width: 100%" id="area_code" onchange="fetchJishuHozenTitle(this.value)">
								<option value=""></option>
								@foreach($area as $area)
								<option value="{{$area->area_code}}_{{$area->area}}">{{$area->area}}</option>
								@endforeach
							</select>
						</div>

						<div style="background-color: #4261ff;text-align: center;">
							<label style="width: 100%;padding: 3px;font-size: 20px;color: white" for="exampleInputEmail1">Pilih Jishu Hozen</label>
						</div>
						<div class="form-group" style="margin-top: 10px">
							<select class="form-control selectTitle" data-placeholder="Pilih Jishu Hozen" style="width: 100%" id="jishu_hozen_point_id">
								<option value=""></option>
							</select>
						</div>

						<button style="width: 100%;padding: 2px;font-weight: bold;" class="btn btn-success btn-lg" onclick="fetchJishuHozen()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Are you sure delete?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="create-modal">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header" style="background-color: #ffb03b">
	        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button> -->
	        <h4 class="modal-title" align="center"><b>CHECK LIST</b><br><span style="font-weight: bold;" id="jishu_hozen_title2"></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="box-body">
	        <div>
	          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
	            <div class="col-xs-4 col-md-offset-4">
		            <div class="form-group">
		              <label for="">Check Date</label>
		              <input type="text" name="check_date" id="inputcheck_date" class="form-control" readonly required="required" value="{{date('Y-m-d')}}" title="">
					  <input type="hidden" name="jishu_id" id="inputjishu_id" class="form-control" readonly required="required" title="">
					  <input type="hidden" name="leader" id="inputleader" class="form-control" readonly required="required" title="">
					  <input type="hidden" name="foreman" id="inputforeman" class="form-control" readonly required="required" title="">
		            </div>
	            </div>
	            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            	<table class="table table-bordered table-striped table-hover">
	            		<thead style="background-color: rgba(126,86,134,.7);color: white">
	            			<tr>
	            				<th style="width: 1%">No.<br>番</th>
	            				<th style="width: 2%">Time<br>時間</th>
	            				<th style="width: 2%">Classification<br>分類</th>
	            				<th style="width: 3%">Point Check<br>監査箇所</th>
	            				<th style="width: 2%">Standard<br>標準</th>
	            				<th style="width: 6%">Condition<br>調子</th>
	            			</tr>
	            		</thead>
	            		<tbody id="bodyTableInput">
	            		</tbody>
	            	</table>
	            </div>
	          </div>
	          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	          	<!-- <div class="modal-footer"> -->
		            <button type="button" class="btn btn-danger pull-left" style="font-weight: bold;" data-dismiss="modal">Cancel</button>
	          	<!-- </div> -->
	          </div>
	          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	          	<!-- <div class="modal-footer"> -->
		            <button type="button" class="btn btn-success pull-right" onclick="save()" style="font-weight: bold;">Submit</button>
	          	<!-- </div> -->
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="edit-modal">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header" style="background-color: #ffb03b">
	        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button> -->
	        <h4 class="modal-title" align="center"><b>CHECK LIST</b><br><span style="font-weight: bold;" id="jishu_hozen_title3"></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="box-body">
	        <div>
	          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
	            <div class="col-xs-4 col-md-offset-4">
		            <div class="form-group">
		              <label for="">Check Date</label>
		              <input type="text" name="check_date" id="editcheck_date" class="form-control" readonly required="required" value="{{date('Y-m-d')}}" title="">
					  <input type="hidden" name="jishu_id" id="editjishu_id" class="form-control" readonly required="required" title="">
					  <input type="hidden" name="leader" id="editleader" class="form-control" readonly required="required" title="">
					  <input type="hidden" name="foreman" id="editforeman" class="form-control" readonly required="required" title="">
		            </div>
	            </div>
	            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            	<table class="table table-bordered table-striped table-hover">
	            		<thead style="background-color: rgba(126,86,134,.7);color: white">
	            			<tr>
	            				<th style="width: 1%">No.<br>番</th>
	            				<th style="width: 2%">Time<br>時間</th>
	            				<th style="width: 2%">Classification<br>分類</th>
	            				<th style="width: 3%">Point Check<br>監査箇所</th>
	            				<th style="width: 2%">Standard<br>標準</th>
	            				<th style="width: 6%">Condition<br>調子</th>
	            			</tr>
	            		</thead>
	            		<tbody id="bodyTableUpdate">
	            		</tbody>
	            	</table>
	            </div>
	          </div>
	          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	          	<!-- <div class="modal-footer"> -->
		            <button type="button" class="btn btn-danger pull-left" style="font-weight: bold;" data-dismiss="modal">Cancel</button>
	          	<!-- </div> -->
	          </div>
	          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	          	<!-- <div class="modal-footer"> -->
		            <button type="button" class="btn btn-success pull-right" onclick="update()" style="font-weight: bold;">Update</button>
	          	<!-- </div> -->
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
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var employees = [];
	var count = 0;
	var destinations = [];
	var countDestination = 0;

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#modalArea').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.datepicker2').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
	});

	$('#modalArea').on('shown.bs.modal', function () {
		$('#area_code').val('').trigger('change');
	});

	function clearAll() {
		$('input[type="radio"]').prop('checked', false);
	}


	$(function () {
		$('.selectArea').select2({
		});
		// $('.selectDet').select2({
		// 	dropdownParent: $('#selectDet'),
		// 	allowClear:true
		// });
		// $('.selectEmp').select2({
		// 	dropdownParent: $('#selectEmp'),
		// 	allowClear:true
		// });

		// $('.selectPurEdit').select2({
		// 	dropdownParent: $('#selectPurEdit'),
		// 	allowClear:true
		// });
		// $('.selectDetEdit').select2({
		// 	dropdownParent: $('#selectDetEdit'),
		// 	allowClear:true
		// });
		// $('.selectEmpEdit').select2({
		// 	dropdownParent: $('#selectEmpEdit'),
		// 	allowClear:true
		// });
	});

	function fetchJishuHozenTitle(area_code) {
		var data = {
			area_code:area_code.split('_')[0]
		}
		$('#jishu_hozen_point_id').html('');
		var title = '';
		title += '<option value=""></option>';
		$.get('{{ url("fetch/maintenance/jishu_hozen/title") }}', data, function(result, status, xhr){
			if(result.status){
				for(var i = 0; i < result.jishu_hozen_title.length;i++){
					title += '<option value="'+result.jishu_hozen_title[i].jishu_id+'_'+result.jishu_hozen_title[i].title+'">'+result.jishu_hozen_title[i].title+' (Mesin '+result.jishu_hozen_title[i].machine+')</option>';
				}
				$('#jishu_hozen_point_id').append(title);
				$('#jishu_hozen_point_id').select2();
			}else{
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	var pointCheckId = [];
	var conditionId = [];
	var valueId = [];
	var typingId = [];
	var indexPointCheck = 0;
	var indexCondition = 0;
	var indexValue = 0;
	var indexTyping = 0;

	function fetchJishuHozen() {
		$('#loading').show();
		var jishu_id = $('#jishu_hozen_point_id').val().split('_')[0];
		var area_code = $('#area_code').val().split('_')[0];
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		if (area_code == '' || jishu_id == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Area dan Judul Jishu Hozen');
			return false;
		}

		var urls = "{{url('index/maintenance/jishu_hozen_point?jishu_id=')}}";
		var url = document.getElementById('linkPointCheck');
		url.setAttribute("href", urls + jishu_id);

		var data = {
			jishu_id:jishu_id,
			area_code:area_code,
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/maintenance/jishu_hozen") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableJishuHozen').DataTable().clear();
				$('#tableJishuHozen').DataTable().destroy();
				$('#bodyTableJishuHozen').html('');
				var jishu_hozen = '';

				if (result.jishu_hozen != null) {
					var index = 1;
					for(var i = 0; i < result.jishu_hozen.length;i++){
						jishu_hozen += '<tr>';
						jishu_hozen += '<td>'+index+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].date+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].machine+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].check_time+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].classification+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].point_check_name+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].standard+'</td>';
						if (result.jishu_hozen[i].point_check_type == 'condition') {
							jishu_hozen += '<td>';
							if (result.jishu_hozen[i].point_result == 'OK') {
								jishu_hozen += '&#9711;';
							}else if (result.jishu_hozen[i].point_result == 'NS') {
								jishu_hozen += '&#9651;';
							}else if (result.jishu_hozen[i].point_result == 'NG') {
								jishu_hozen += '&#9747;';
							}else{
								jishu_hozen += 'Belum Diisi';
							}
							jishu_hozen += '</td>';
						}else{
							jishu_hozen += '<td>'+(result.jishu_hozen[i].point_result || "Belum Diisi")+'</td>';
						}
						jishu_hozen += '<td>'+result.jishu_hozen[i].pic_check.split(' ').slice(0,2).join(' ')+'</td>';
						jishu_hozen += '<td>'+result.jishu_hozen[i].created+'</td>';
						jishu_hozen += '<td><button class="btn btn-warning" onclick="editJishuHozen(\''+result.jishu_hozen[i].date+'\',\''+result.jishu_hozen[i].jishu_id+'\')">Edit</button></td>';
						jishu_hozen += '</tr>';
						index++;
					}
					$('#bodyTableJishuHozen').append(jishu_hozen);
				}

				var table = $('#tableJishuHozen').DataTable({
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
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#jishu_hozen_title').html($('#jishu_hozen_point_id').val().split('_')[1]);
				$('#jishu_hozen_title2').html($('#jishu_hozen_point_id').val().split('_')[1]);
				$('#jishu_hozen_title3').html($('#jishu_hozen_point_id').val().split('_')[1]);
				$('#inputjishu_id').val(result.jishu_hozen_point[0].jishu_id);
				$('#inputleader').val(result.jishu_hozen_point[0].leader);
				$('#inputforeman').val(result.jishu_hozen_point[0].foreman);

				$('#bodyTableInput').html('');
				var jishu_hozen_point = '';

				if (result.jishu_hozen_point != null) {
					var index = 1;
					pointCheckId = [];
					conditionId = [];
					valueId = [];
					typingId = [];
					indexPointCheck = 0;
					indexCondition = 0;
					indexValue = 0;
					indexTyping = 0;
					for(var i = 0; i < result.jishu_hozen_point.length;i++){
						jishu_hozen_point += '<tr>';
						jishu_hozen_point += '<td>'+index+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].check_time+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].classification+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].point_check_name+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].standard+'</td>';
						jishu_hozen_point += '<td>';
						if (result.jishu_hozen_point[i].point_check_type == 'condition') {
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px;margin-left: 5px">&#9711;';
								jishu_hozen_point += '<input type="radio" checked="checked" id="condition_'+indexCondition+'" name="condition_'+indexCondition+'" value="OK">';
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							jishu_hozen_point += '&nbsp;&nbsp;';
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px">&#9651;';
								jishu_hozen_point += '<input type="radio" id="condition_'+indexCondition+'" name="condition_'+indexCondition+'" value="NS">';
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px">&#9747;';
								jishu_hozen_point += '<input type="radio" id="condition_'+indexCondition+'" name="condition_'+indexCondition+'" value="NG">';
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							conditionId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexCondition++;
						}

						if (result.jishu_hozen_point[i].point_check_type == 'value') {
							jishu_hozen_point += '<input type="text" class="form-control numpad" name="value_'+indexValue+'" id="value_'+indexValue+'" readonly style="width:100%;font-size:20px;text-align:center" value="">';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							valueId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexValue++;
						}

						if (result.jishu_hozen_point[i].point_check_type == 'typing') {
							jishu_hozen_point += '<input type="text" class="form-control" name="typing_'+indexTyping+'" id="typing_'+indexTyping+'" style="width:100%;font-size:20px;text-align:center" value="">';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							typingId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexTyping++;
						}
						jishu_hozen_point += '</td>';
						jishu_hozen_point += '</tr>';
						index++;
						
						
					}
					$('#bodyTableInput').append(jishu_hozen_point);
				}

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				$('#loading').hide();
				$('#modalArea').modal('hide');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function save() {
		$('#loading').show();
		var result_check = [];
		var result_condition = 0;
		var result_value = 0;
		var result_typing = 0;

		var jishu_id = $('#inputjishu_id').val();
		var leader = $('#inputleader').val();
		var foreman = $('#inputforeman').val();
		var check_date = $('#inputcheck_date').val();
		var jishu_hozen_title = $('#jishu_hozen_title').text();

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexCondition; j++) {
				if (conditionId[j] == pointCheckId[i]) {
					var radios = document.getElementsByName('condition_'+j);
					var rescon = '';
					for (var k = 0, length = radios.length; k < length; k++) {
					  if (radios[k].checked) {
				    	rescon = radios[k].value;
					  }
					}
					result_check.push({point_check_id:conditionId[j],result:rescon});
					result_condition++;
				}
			}
		}

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexValue; j++) {
				if (valueId[j] == pointCheckId[i]) {
					result_check.push({point_check_id:pointCheckId[i],result:$('#value_'+j).val()});
				    result_value++;
				}
			}
		}

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexTyping; j++) {
				if (typingId[j] == pointCheckId[i]) {
					result_check.push({point_check_id:pointCheckId[i],result:$('#typing_'+j).val()});
				    result_typing++;
				}
			}
		}

		var data = {
			result_check:result_check,
			jishu_id:jishu_id,
			leader:leader,
			foreman:foreman,
			check_date:check_date,
			jishu_hozen_title:jishu_hozen_title
		}


		$.post('{{ url("input/maintenance/jishu_hozen") }}', data, function(result, status, xhr){
			if(result.status){
				$('#create-modal').modal('hide');
				openSuccessGritter('Success!','Input Data Success');
				fetchJishuHozen();
				$('#loading').hide();
			}else{
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function editJishuHozen(date,jishu_id) {
		$('#loading').show();
		var jishu_id = $('#jishu_hozen_point_id').val().split('_')[0];
		var area_code = $('#area_code').val().split('_')[0];

		var data = {
			jishu_id:jishu_id,
			area_code:area_code,
			date:date
		}

		$.get('{{ url("edit/maintenance/jishu_hozen") }}', data, function(result, status, xhr){
			if(result.status){
				$('#jishu_hozen_title3').html($('#jishu_hozen_point_id').val().split('_')[1]);
				$('#editjishu_id').val(result.jishu_hozen_point[0].jishu_id);
				$('#editleader').val(result.jishu_hozen_point[0].leader);
				$('#editforeman').val(result.jishu_hozen_point[0].foreman);

				$('#bodyTableUpdate').html('');
				var jishu_hozen_point = '';

				clearAll();

				if (result.jishu_hozen_point != null) {
					var index = 1;
					pointCheckId = [];
					conditionId = [];
					valueId = [];
					typingId = [];
					indexPointCheck = 0;
					indexCondition = 0;
					indexValue = 0;
					indexTyping = 0;
					for(var i = 0; i < result.jishu_hozen_point.length;i++){
						jishu_hozen_point += '<tr>';
						jishu_hozen_point += '<td>'+index+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].check_time+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].classification+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].point_check_name+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].standard+'</td>';
						jishu_hozen_point += '<td>';
						if (result.jishu_hozen_point[i].point_check_type == 'condition') {
							var values = '';
							for(var j = 0; j < result.jishu_hozen.length;j++){
								if (result.jishu_hozen[j].point_id == result.jishu_hozen_point[i].id) {
									values = result.jishu_hozen[j].point_result;
								}
							}
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px;margin-left: 5px">&#9711;';
								if (values == 'OK') {
									jishu_hozen_point += '<input type="radio" checked="checked" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="OK">';
								}else{
									jishu_hozen_point += '<input type="radio" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="OK">';
								}
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							jishu_hozen_point += '&nbsp;&nbsp;';
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px">&#9651;';
								if (values == 'NS') {
									jishu_hozen_point += '<input type="radio" checked="checked" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="NS">';
								}else{
									jishu_hozen_point += '<input type="radio" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="NS">';
								}
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							jishu_hozen_point += '<label class="radio" style="margin-top: 5px">&#9747;';
								if (values == 'NG') {
									jishu_hozen_point += '<input type="radio" checked="checked" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="NG">';
								}else{
									jishu_hozen_point += '<input type="radio" id="editcondition_'+indexCondition+'" name="editcondition_'+indexCondition+'" value="NG">';
								}
								jishu_hozen_point += '<span class="checkmark"></span>';
							jishu_hozen_point += '</label>';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							conditionId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexCondition++;
						}

						if (result.jishu_hozen_point[i].point_check_type == 'value') {
							var values = '';
							for(var j = 0; j < result.jishu_hozen.length;j++){
								if (result.jishu_hozen[j].point_id == result.jishu_hozen_point[i].id) {
									values = result.jishu_hozen[j].point_result;
								}
							}
							jishu_hozen_point += '<input type="text" class="form-control numpad" name="editvalue_'+indexValue+'" id="editvalue_'+indexValue+'" readonly style="width:100%;font-size:20px;text-align:center" value="'+values+'">';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							valueId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexValue++;
						}

						if (result.jishu_hozen_point[i].point_check_type == 'typing') {
							for(var j = 0; j < result.jishu_hozen.length;j++){
								if (result.jishu_hozen[j].point_id == result.jishu_hozen_point[i].id) {
									values = result.jishu_hozen[j].point_result;
								}
							}
							jishu_hozen_point += '<input type="text" class="form-control" name="edittyping_'+indexTyping+'" id="edittyping_'+indexTyping+'" style="width:100%;font-size:20px;text-align:center" value="'+values+'">';
							pointCheckId.push(result.jishu_hozen_point[i].id);
							typingId.push(result.jishu_hozen_point[i].id);
							indexPointCheck++;
							indexTyping++;
						}
						jishu_hozen_point += '</td>';
						jishu_hozen_point += '</tr>';
						index++;
					}
					$('#bodyTableUpdate').append(jishu_hozen_point);
				}


				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				$('#loading').hide();
				$("#edit-modal").modal('show');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function update() {
		$('#loading').show();
		var result_check = [];
		var result_condition = 0;
		var result_value = 0;
		var result_typing = 0;

		var jishu_id = $('#editjishu_id').val();
		var leader = $('#editleader').val();
		var foreman = $('#editforeman').val();
		var check_date = $('#editcheck_date').val();
		var jishu_hozen_title = $('#jishu_hozen_title').text();

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexCondition; j++) {
				if (conditionId[j] == pointCheckId[i]) {
					var radios = document.getElementsByName('editcondition_'+j);
					var rescon = '';
					for (var k = 0, length = radios.length; k < length; k++) {
					  if (radios[k].checked) {
				    	rescon = radios[k].value;
					  }
					}
					result_check.push({point_check_id:conditionId[j],result:rescon});
					result_condition++;
				}
			}
		}

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexValue; j++) {
				if (valueId[j] == pointCheckId[i]) {
					result_check.push({point_check_id:pointCheckId[i],result:$('#editvalue_'+j).val()});
				    result_value++;
				}
			}
		}

		for(var i = 0; i < indexPointCheck;i++){
			for (var j = 0; j < indexTyping; j++) {
				if (typingId[j] == pointCheckId[i]) {
					result_check.push({point_check_id:pointCheckId[i],result:$('#edittyping_'+j).val()});
				    result_typing++;
				}
			}
		}

		var data = {
			result_check:result_check,
			jishu_id:jishu_id,
			leader:leader,
			foreman:foreman,
			check_date:check_date,
			jishu_hozen_title:jishu_hozen_title
		}


		$.post('{{ url("update/maintenance/jishu_hozen") }}', data, function(result, status, xhr){
			if(result.status){
				$('#edit-modal').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!','Update Data Success');
				fetchJishuHozen();
			}else{
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
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


	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection