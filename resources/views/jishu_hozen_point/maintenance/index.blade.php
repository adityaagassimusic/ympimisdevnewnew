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
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create-modal" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point
		</button>
		<a class="btn btn-info btn-sm pull-right" style="margin-right: 5px" href="{{url('index/maintenance/jishu_hozen_point?jishu_id=')}}">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Pilih Area
		</a>
		<a class="btn btn-primary btn-sm pull-right" style="margin-right: 5px" href="{{url('index/maintenance/jishu_hozen')}}"><i class="fa fa-arrow-circle-left"></i>&nbsp;&nbsp;Create Jishu Hozen
		</a>
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
		<!-- <div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="box-header">
						<h3 class="box-title">Filter</h3>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-md-offset-4">
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
				</div>
			</div>
		</div> -->
		<div class="col-xs-12 pull-left">
			<table id="tableJishuHozen" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">No.</th>
						<th width="1%">ID</th>
						<th width="3%">Title</th>
						<th width="1%">Loc</th>
						<th width="1%">Machine</th>
						<th width="1%">Doc Number</th>
						<th width="1%">Timing</th>
						<th width="1%">Classification</th>
						<th width="2%">Index</th>
						<th width="3%">Point</th>
						<th width="2%">Standard</th>
						<th width="2%">Type</th>
						<th width="2%">Drawing</th>
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
							<select class="form-control selectArea" data-placeholder="Pilih Area" style="width: 100%" id="area_code">
								<option value=""></option>
								@foreach($area as $area)
								<option value="{{$area->area_code}}_{{$area->area}}">{{$area->area}}</option>
								@endforeach
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
	        <h4 class="modal-title" align="center"><b>ADD CHECK LIST</b></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="box-body">
	        <div>
	          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
	            <div class="col-xs-4 col-md-offset-2">
		            <div class="form-group" id="selectCreateArea">
		              <label for="">Area</label>
		              <select class="form-control selectCreateArea" data-placeholder="Pilih Area" style="width: 100%" id="inputarea_code">
							<option value=""></option>
							@foreach($area2 as $area)
							<option value="{{$area->area_code}}">{{$area->area}}</option>
							@endforeach
						</select>
		            </div>
	            </div>
	            <div class="col-xs-4">
		            <div class="form-group">
		              <label for="">ID</label>
		              <input type="text" name="jishu_id" id="inputjishu_id" class="form-control" required="required" value="" title="" placeholder="Input Jishu ID" onkeyup="checkId(this.value)">
		            </div>
	            </div>
	            <div class="col-xs-8 col-md-offset-2">
		            <div class="form-group">
		              <label for="">Title</label>
		              <input type="text" name="title" id="inputtitle" class="form-control" required="required" value="" title="" placeholder="Input Title">
		            </div>
	            </div>
	            <div class="col-xs-4 col-md-offset-2">
		            <div class="form-group">
		              <label for="">Machine</label>
		              <input type="text" name="machine" id="inputmachine" class="form-control" required="required" value="" title="" placeholder="Input Machine">
		            </div>
	            </div>
	            <div class="col-xs-4">
		            <div class="form-group">
		              <label for="">Doc Number</label>
		              <input type="text" name="doc_number" id="inputdoc_number" class="form-control" required="required" value="" title="" placeholder="Input Document Number">
		            </div>
	            </div>
	            <div class="col-xs-4 col-md-offset-2">
		            <div class="form-group">
		              <label for="">Doc Rev</label>
		              <input type="text" name="rev" id="inputrev" class="form-control" required="required" value="" title="" placeholder="Input Document Rev (Contoh : 01)">
		            </div>
	            </div>
	            <div class="col-xs-4">
		            <div class="form-group">
		              <label for="">Rev Date</label>
		              <input type="text" name="rev_date" id="inputrev_date" class="form-control datepicker" required="required" readonly value="" title="" placeholder="Input Document Rev Date">
		            </div>
	            </div>
	            <div class="col-xs-4 col-md-offset-2">
		            <div class="form-group" id="selectCreateLeader">
		              <label for="">Leader</label>
		              <select class="form-control selectCreateLeader" data-placeholder="Pilih Leader" style="width: 100%" id="inputleader">
							<option value=""></option>
							@foreach($leader as $leader)
							<option value="{{$leader->employee_id}}">{{$leader->employee_id}} - {{$leader->name}}</option>
							@endforeach
						</select>
		            </div>
	            </div>
	            <div class="col-xs-4">
		            <div class="form-group" id="selectCreateForeman">
		              <label for="">Foreman</label>
		              <select class="form-control selectCreateForeman" data-placeholder="Pilih Foreman" style="width: 100%" id="inputforeman">
							<option value=""></option>
							@foreach($foreman as $foreman)
							<option value="{{$foreman->employee_id}}">{{$foreman->employee_id}} - {{$foreman->name}}</option>
							@endforeach
						</select>
		            </div>
	            </div>
	            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            	<hr style="border: 2px solid red">
	            	<div style="background-color: #d6adff;text-align: center;">
		            	<span style="width: 100%;padding: 2px;font-weight: bold;font-size: 20px">Point Check Detail</span>
		            </div>
		            <div class="col-xs-4 col-md-offset-2">
			            <div class="form-group" id="selectCreateCheckTime">
			              <label for="">Check Time</label>
			              <select class="form-control selectCreateCheckTime" data-placeholder="Pilih Check Time" style="width: 100%" id="inputcheck_time">
			              		<option value=""></option>
								<option value="Persiapan">Persiapan</option>
								<option value="Selesai Bekerja">Selesai Bekerja</option>
							</select>
			            </div>
		            </div>
		            <div class="col-xs-4">
			            <div class="form-group" id="selectCreateClassification">
			              <label for="">Classification</label>
			              <select class="form-control selectCreateClassification" data-placeholder="Pilih Classification" style="width: 100%" id="inputclassification">
			              		<option value=""></option>
								<option value="Pengecekan">Pengecekan</option>
								<option value="Pembersihan">Pembersihan</option>
							</select>
			            </div>
		            </div>
		            <div class="col-xs-8 col-md-offset-2">
			            <div class="form-group">
			              <label for="">Point Check Name</label>
			              <input type="text" name="point_check_name" id="inputpoint_check_name" class="form-control" required="required" value="" title="" placeholder="Input Point Check Name">
			            </div>
		            </div>
		            <div class="col-xs-4 col-md-offset-2">
			            <div class="form-group">
			              <label for="">Standard</label>
			              <input type="text" name="standard" id="inputstandard" class="form-control" required="required" value="" title="" placeholder="Input Standard">
			            </div>
		            </div>
		            <div class="col-xs-4" id="selectCreateType">
			            <div class="form-group">
			              <label for="">Type</label>
			              <select class="form-control selectCreateType" data-placeholder="Pilih Type" style="width: 100%" id="inputtype">
			              		<option value=""></option>
								<option value="condition">Kondisi (OK, NS, NG)</option>
								<option value="value">Value (Angka)</option>
								<option value="typing">Tulisan</option>
							</select>
			            </div>
		            </div>
		            <div class="col-xs-8 col-md-offset-2" style="margin-bottom: 10px">
		            	<button class="btn btn-success pull-right" onclick="addJishu()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Jishu Point</button>
		            </div>
	            	<table class="table table-bordered table-striped table-hover">
	            		<thead style="background-color: rgba(126,86,134,.7);color: white">
	            			<tr>
	            				<th style="width: 2%">Check Time</th>
	            				<th style="width: 2%">Classification</th>
	            				<th style="width: 6%">Point Check Name</th>
	            				<th style="width: 2%">Standard</th>
	            				<th style="width: 2%">Type</th>
	            				<th style="width: 2%">Action</th>
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
	var jishu_hozen_point = [];
	var jishu_hozen_point_id = [];
	var count = 0;

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		if ("{{$_GET['jishu_id']}}" == '') {
			$('#modalArea').modal({
				backdrop: 'static',
				keyboard: false
			});
		}else{
			fetchJishuHozen();
		}


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
		checkId($('#inputjishu_id').val());
		$('#inputtitle').val('');
		$('#inputmachine').val('');
		$('#inputdoc_number').val('');
		$('#inputrev_date').val('');
		$('#inputrev').val('');
		$('#bodyTableInput').html('');
		jishu_hozen_point = [];
		count = 0;
		$('#inputcheck_time').val('').trigger('change');
		$('#inputclassification').val('').trigger('change');
		$('#inputtype').val('').trigger('change');
		$('#inputpoint_check_name').val('');
		$('#inputstandard').val('');
		$('#inputleader').val('').trigger('change');
		$('#inputforeman').val('').trigger('change');
	}


	$(function () {
		$('.selectArea').select2({
			dropdownParent: $('#modalArea'),
			allowClear:true
		});
		$('.selectCreateArea').select2({
			dropdownParent: $('#selectCreateArea'),
			allowClear:true
		});
		$('.selectCreateCheckTime').select2({
			dropdownParent: $('#selectCreateCheckTime'),
			allowClear:true
		});
		$('.selectCreateClassification').select2({
			dropdownParent: $('#selectCreateClassification'),
			allowClear:true
		});
		$('.selectCreateType').select2({
			dropdownParent: $('#selectCreateType'),
			allowClear:true
		});
		$('.selectCreateLeader').select2({
			dropdownParent: $('#selectCreateLeader'),
			allowClear:true
		});
		$('.selectCreateForeman').select2({
			dropdownParent: $('#selectCreateForeman'),
			allowClear:true
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


	function checkId(jishu_id) {
		var data = {
			jishu_id:jishu_id,
		}

		$.get('{{ url("fetch/maintenance/jishu_hozen_point") }}', data, function(result, status, xhr){
			if(result.status){
				if (result.jishu_hozen_point.length > 0) {
					$('#inputmachine').val(result.jishu_hozen_point[0].machine);
					$('#inputtitle').val(result.jishu_hozen_point[0].title);
					$('#inputdoc_number').val(result.jishu_hozen_point[0].doc_number);
					$('#inputrev').val(result.jishu_hozen_point[0].rev);
					$('#inputrev_date').val(result.jishu_hozen_point[0].rev_date);
					$('#inputleader').val(result.jishu_hozen_point[0].leader).trigger('change');
					$('#inputforeman').val(result.jishu_hozen_point[0].foreman).trigger('change');

					jishu_hozen_point = [];
					count = 0;
					$('#bodyTableInput').html('');
					var jishubody = '';

					for(var i = 0; i < result.jishu_hozen_point.length;i++){
						jishu_hozen_point.push({point_id:result.jishu_hozen_point[i].jishu_id+'_'+count,id:result.jishu_hozen_point[i].id,check_time:result.jishu_hozen_point[i].check_time,classification:result.jishu_hozen_point[i].classification,point_check_name:result.jishu_hozen_point[i].point_check_name,standard:result.jishu_hozen_point[i].standard,point_check_type:result.jishu_hozen_point[i].point_check_type});
						jishubody += '<tr id="'+result.jishu_hozen_point[i].jishu_id+'_'+count+'">';
						jishubody += '<td>'+result.jishu_hozen_point[i].check_time+'</td>';
						jishubody += '<td>'+result.jishu_hozen_point[i].classification+'</td>';
						jishubody += '<td>'+result.jishu_hozen_point[i].point_check_name+'</td>';
						jishubody += '<td>'+result.jishu_hozen_point[i].standard+'</td>';
						jishubody += '<td>'+result.jishu_hozen_point[i].point_check_type+'</td>';
						jishubody += "<td><a href='javascript:void(0)' onclick='remJishu(id)' id='"+result.jishu_hozen_point[i].jishu_id+"_"+count+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
						jishubody += '</tr>';
						jishu_hozen_point_id.push(result.jishu_hozen_point[i].id);
						count++;
					}

					$('#bodyTableInput').append(jishubody);
				}
			}else{
				clearAll();
			}
		})
	}

	function fetchJishuHozen() {
		$('#loading').show();
		var jishu_id = "{{$_GET['jishu_id']}}";
		var area_code = $("#area_code").val().split('_')[0];

		var data = {
			jishu_id:jishu_id,
			area_code:area_code,
		}

		$.get('{{ url("fetch/maintenance/jishu_hozen_point") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableJishuHozen').DataTable().clear();
				$('#tableJishuHozen').DataTable().destroy();
				$('#bodyTableJishuHozen').html('');
				var jishu_hozen_point = '';

				if (result.jishu_hozen_point.length > 0) {
					var index = 1;
					for(var i = 0; i < result.jishu_hozen_point.length;i++){
						jishu_hozen_point += '<tr>';
						jishu_hozen_point += '<td>'+index+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].jishu_id+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].title+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].location+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].machine+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].doc_number+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].check_time+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].classification+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].point_check_index+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].point_check_name+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].standard+'</td>';
						jishu_hozen_point += '<td>'+result.jishu_hozen_point[i].point_check_type+'</td>';
						jishu_hozen_point += '<td>'+(result.jishu_hozen_point[i].drawing || "")+'</td>';
						jishu_hozen_point += '</tr>';
						index++;
					}
					$('#bodyTableJishuHozen').append(jishu_hozen_point);

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
				}

				if (jishu_id == '') {
					$('#jishu_hozen_title').html($('#area_code').val().split('_')[1]);
					$('#inputtitle').val('');
					$('#inputarea_code').val($('#area_code').val().split('_')[0]).trigger('change.select2');
					$('#inputjishu_id').val(result.jishu_id);
					$('#inputmachine').val('');
					$('#inputdoc_number').val('');
					$('#inputrev').val('');
					$('#inputrev_date').val('');
					$('#inputleader').val('');
					$('#inputforeman').val('');
					$('#bodyTableInput').html('');
					jishu_hozen_point = [];
					count = 0;
				}else{
					// $('#jishu_hozen_title').html(result.jishu_hozen_point[0].title);
					// $('#inputtitle').val(result.jishu_hozen_point[0].title);
					$('#inputarea_code').val(result.jishu_hozen_point[0].area_code).trigger('change.select2');
					$('#inputjishu_id').val(result.jishu_hozen_point[0].jishu_id);
					// $('#inputmachine').val(result.jishu_hozen_point[0].machine);
					// $('#inputdoc_number').val(result.jishu_hozen_point[0].doc_number);
					// $('#inputrev').val(result.jishu_hozen_point[0].rev);
					// $('#inputrev_date').val(result.jishu_hozen_point[0].rev_date);
					checkId(result.jishu_hozen_point[0].jishu_id);
				}

				$('#loading').hide();
				$('#modalArea').modal('hide');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function remJishu(id){
		for(var i = 0;i < jishu_hozen_point.length;i++ ){
			if (jishu_hozen_point[i]['point_id'] === id) {
				jishu_hozen_point.splice( i, 1 );
			}
		}
		$('#'+id).remove();
	}

	function addJishu() {
		var check_time = $('#inputcheck_time').val();
		var classification = $('#inputclassification').val();
		var point_check_name = $('#inputpoint_check_name').val();
		var standard = $('#inputstandard').val();
		var point_check_type = $('#inputtype').val();

		if (check_time == "" || classification == "" || point_check_name == "" || standard == "" || point_check_type == "") {
			audio_error.play();
			openErrorGritter('Error!','Input Semua Data.');
			return false;
		}


		var tableJishu = "";
		var jishu_id = $('#inputjishu_id').val();

		count += 1;
		tableJishu += '<tr id="'+jishu_id+'_'+count+'">';
		tableJishu += '<td>'+check_time+'</td>';
		tableJishu += '<td>'+classification+'</td>';
		tableJishu += '<td>'+point_check_name+'</td>';
		tableJishu += '<td>'+standard+'</td>';
		tableJishu += '<td>'+point_check_type+'</td>';
		tableJishu += "<td><a href='javascript:void(0)' onclick='remJishu(id)' id='"+jishu_id+"_"+count+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";

		jishu_hozen_point.push({point_id:jishu_id+'_'+count,id:'',check_time:check_time,classification:classification,point_check_name:point_check_name,standard:standard,point_check_type:point_check_type});

		$('#bodyTableInput').append(tableJishu);

		$('#inputcheck_time').val('').trigger('change');
		$('#inputclassification').val('').trigger('change');
		$('#inputpoint_check_name').val('');
		$('#inputstandard').val('');
		$('#inputtype').val('').trigger('change');
		openSuccessGritter('Success!','Add Point Success');
	}

	function save() {
		var area_code = $('#inputarea_code').val();
		var jishu_id = $('#inputjishu_id').val();
		var machine = $('#inputmachine').val();
		var title = $('#inputtitle').val();
		var doc_number = $('#inputdoc_number').val();
		var rev = $('#inputrev').val();
		var rev_date = $('#inputrev_date').val();
		var leader = $('#inputleader').val();
		var foreman = $('#inputforeman').val();

		var data = {
			area_code:area_code,
			jishu_id:jishu_id,
			machine:machine,
			title:title,
			doc_number:doc_number,
			rev:rev,
			rev_date:rev_date,
			jishu_hozen_point:jishu_hozen_point,
			jishu_hozen_point_id:jishu_hozen_point_id,
			leader:leader,
			foreman:foreman,
		}

		$.post('{{ url("input/maintenance/jishu_hozen_point") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Save Point Check Success');
				$('#create-modal').modal('hide');
			}else{
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		})
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