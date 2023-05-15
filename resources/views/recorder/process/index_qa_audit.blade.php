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
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create-modal" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Create Temuan Audit QA
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
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
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
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
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
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group pull-right">
									<a href="{{ url('index/recorder_process') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/recorder/qa_audit') }}" class="btn btn-danger">Clear</a>
									<button onclick="fetchAudit()" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 pull-left">
			<table id="tableAudit" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">No.</th>
						<th width="1%">Product</th>
						<th width="1%">Defect</th>
						<th width="1%">Area</th>
						<th width="1%">Category</th>
						<th width="3%">Image</th>
						<th width="2%">Auditor QA</th>
						<th width="2%">Kakuning RC</th>
						<th width="1%">Kensa Code</th>
						<th width="2%">PIC Injection</th>
						<th width="2%">Counceled Emp</th>
						<th width="2%">Counceled By</th>
						<th width="2%">Counceled At</th>
						<th width="2%">At</th>
						<!-- <th width="2%">Action</th> -->
					</tr>
				</thead>
				<tbody id="bodyTableAudit">
				</tbody>
			</table>
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
	        <h4 class="modal-title" align="center"><b>INPUT NG AUDIT QA</b><br><span style="font-weight: bold;" id="jishu_hozen_title2"></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="box-body">
	        <div>
	          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
	            <div class="col-xs-4">
		            <div class="form-group">
		              <label for="">Check Date</label>
		              <input type="text" name="check_date" id="inputcheck_date" class="form-control datepicker" required="required" value="" placeholder="Input Date" readonly title="">
		            </div>
	            </div>
	            <div class="col-xs-4" id="selectProduct">
		            <div class="form-group">
		              <label for="">Product</label>
		              <select class="form-control selectProduct" data-placeholder="Pilih Produk" style="width: 100%" id="inputproduct">
		              		<option value=""></option>
							@foreach($product as $product)
							<option value="{{$product->gmc}}">{{$product->gmc}} - {{$product->part_name}}</option>
							@endforeach
						</select>
		            </div>
	            </div>
	            <div class="col-xs-4">
		            <div class="form-group">
		              <label for="">Kensa Code</label>
		              <input type="text" name="kensa_code" id="inputkensa_code" class="form-control" required="required" value="" placeholder="Input Kode Kensa" title="">
		            </div>
	            </div>
	            <div class="col-xs-4" id="selectNg">
		            <div class="form-group">
		              <label for="">Defect</label>
		              <select class="form-control selectNg" data-placeholder="Pilih Defect" style="width: 100%" id="inputdefect">
		              		<option value=""></option>
							@foreach($ng_list as $ng_list)
							<option value="{{$ng_list->ng_name}}">{{$ng_list->ng_name}}</option>
							@endforeach
						</select>
		            </div>
	            </div>
	            <div class="col-xs-4" id="selectArea">
		            <div class="form-group">
		              <label for="">Area</label>
		              <select class="form-control selectArea" data-placeholder="Pilih Area" style="width: 100%" id="inputarea">
		              		<option value=""></option>
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="C">C</option>
							<option value="D">D</option>
							<option value="E">E</option>
						</select>
		            </div>
	            </div>
	            <div class="col-xs-4" id="selectCategory">
		            <div class="form-group">
		              <label for="">Category</label>
		              <select class="form-control selectCategory" data-placeholder="Pilih Category" style="width: 100%" id="inputcategory">
		              		<option value=""></option>
							<option value="Major">Major</option>
							<option value="Minor">Minor</option>
						</select>
		            </div>
	            </div>
	            <div class="col-xs-6">
		            <div class="form-group">
		              <label for="">Auditor</label>
		              <input type="text" class="form-control" name="inputauditor" placeholder="Scan ID Card Auditor" id="inputauditor" style="width: 100%">
		            </div>
	            </div>
	            <!-- <div class="col-xs-6">
		            <div class="form-group">
		              <label for="">Auditee</label>
		              <input type="text" class="form-control" name="inputauditee" placeholder="Scan ID Card Auditee" id="inputauditee" style="width: 100%">
		            </div>
	            </div> -->
	            <div class="col-xs-6">
		            <div class="form-group">
		              <label for="">Image</label>
		              <input type="file" class="form-control" name="inputfile" placeholder="Image" id="inputfile" style="width: 100%">
		            </div>
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

		fetchAudit();

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

		clearAll();
	});


	function clearAll() {
		$('#inputauditor').removeAttr('disabled');
		$('#inputauditor').val('');
		// $('#inputauditee').removeAttr('disabled');
		// $('#inputauditee').val('');
		$('#inputproduct').val('').trigger('change');
		$('#inputdefect').val('').trigger('change');
		$('#inputarea').val('').trigger('change');
		$('#inputcategory').val('').trigger('change');
		$('#inputkensa_code').val('');
	}


	$(function () {
		$('.selectProduct').select2({
			dropdownParent:$('#selectProduct'),
			allowClear:true
		});
		$('.selectNg').select2({
			dropdownParent:$('#selectNg'),
			allowClear:true
		});
		$('.selectArea').select2({
			dropdownParent:$('#selectArea'),
			allowClear:true
		});
		$('.selectCategory').select2({
			dropdownParent:$('#selectCategory'),
			allowClear:true
		});
	});

	$('#inputauditor').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#inputauditor").val().length >= 8){
				var data = {
					employee_id : $("#inputauditor").val()
				}
				
				$.get('{{ url("scan/recorder/qa_audit") }}', data, function(result, status, xhr){
					if(result.status){
						$("#inputauditor").val(result.emp.employee_id+' - '+result.emp.name);
						$('#inputauditor').prop('disabled',true);
						openSuccessGritter('Success!', result.message);
					}else{
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				});
			}else{
				audio_error.play();
				openErrorGritter('Error!','Tag Invalid');
			}
		}
	})

	// $('#inputauditee').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#inputauditee").val().length >= 8){
	// 			var data = {
	// 				employee_id : $("#inputauditee").val()
	// 			}
				
	// 			$.get('{{ url("scan/recorder/qa_audit") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					$("#inputauditee").val(result.emp.employee_id+' - '+result.emp.name);
	// 					$('#inputauditee').prop('disabled',true);
	// 					openSuccessGritter('Success!', result.message);
	// 				}else{
	// 					audio_error.play();
	// 					openErrorGritter('Error!',result.message);
	// 				}
	// 			});
	// 		}else{
	// 			audio_error.play();
	// 			openErrorGritter('Error!','Tag Invalid');
	// 		}
	// 	}
	// })


	function fetchAudit() {
		$('#loading').show();
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/recorder/qa_audit") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableAudit').DataTable().clear();
				$('#tableAudit').DataTable().destroy();
				$('#bodyTableAudit').html('');
				var audit = '';

				if (result.audit != null) {
					var index = 1;
					for(var i = 0; i < result.audit.length;i++){
						audit += '<tr>';
						audit += '<td>'+index+'</td>';
						audit += '<td>'+result.audit[i].product+'</td>';
						audit += '<td>'+result.audit[i].defect+'</td>';
						audit += '<td>'+result.audit[i].area+'</td>';
						audit += '<td>'+result.audit[i].category+'</td>';
						var url = "{{ url('/data_file/recorder/qa_audit/') }}"+'/'+result.audit[i].image;
						audit += '<td><img width="200px" src="'+url+'"></td>';
						audit += '<td>'+result.audit[i].auditor+'</td>';
						audit += '<td>'+result.audit[i].auditee+'</td>';
						audit += '<td>'+(result.audit[i].kensa_code || "")+'</td>';
						audit += '<td>'+(result.audit[i].pic_injection || "")+'</td>';
						audit += '<td>'+result.audit[i].created+'</td>';
						audit += '<td>'+(result.audit[i].counceled_employee || "")+'</td>';
						audit += '<td>'+(result.audit[i].counceled_by || "")+'</td>';
						audit += '<td>'+(result.audit[i].counceled_at || "")+'</td>';
						// audit += '<td><button class="btn btn-warning" onclick="editJishuHozen(\''+result.audit[i].date+'\',\''+result.audit[i].jishu_id+'\')">Edit</button></td>';
						audit += '</tr>';
						index++;
					}
					$('#bodyTableAudit').append(audit);
				}

				var table = $('#tableAudit').DataTable({
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
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function save() {
		$('#loading').show();
		var check_date = $("#inputcheck_date").val();
		var product = $("#inputproduct").val();
		var defect = $("#inputdefect").val();
		var area = $("#inputarea").val();
		var category = $("#inputcategory").val();
		var auditor = $("#inputauditor").val();
		// var auditee = $("#inputauditee").val();
		var kensa_code = $("#inputkensa_code").val();
		var fileData  = $('#inputfile').prop('files')[0];

		var file=$('#inputfile').val().replace(/C:\\fakepath\\/i, '').split(".");
		var formData = new FormData();
		formData.append('check_date',check_date);
		formData.append('product',product);
		formData.append('defect',defect);
		formData.append('area',area);
		formData.append('category',category);
		formData.append('auditor',auditor);
		// formData.append('auditee',auditee);
		formData.append('kensa_code',kensa_code);
		formData.append('fileData', fileData);
		formData.append('extension', file[1]);
		formData.append('foto_name', file[0]);

		$.ajax({		
			url:"{{ url('input/recorder/qa_audit') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				$('#loading').hide();
				fetchAudit();
				$('#create-modal').modal('hide');
				openSuccessGritter('Success','Input NG QA Audit Berhasil');
			},
			error: function (err) {
				$('#loading').hide();
		        openErrorGritter('Error!',err);
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