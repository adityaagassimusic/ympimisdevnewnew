@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
td{
	overflow:hidden;
	text-overflow: ellipsis;
}
table {
	table-layout:fixed;
}
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
	/*margin-top:20px;*/
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }

#planTable > tbody > tr > td, #planTable > tfoot > tr > th{
	border: 1px solid black;
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Stamp Clarinet<span class="text-purple"> フルートの刻印</span>
		<small>Serial Number <span class="text-purple"> 通し番号</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			{{-- <a href="{{ url("/index/displayWipFl") }}" class="btn btn-warning btn-sm" style="color:white"><i class="fa fa-television "></i>&nbsp;Display</a>
			<button href="javascript:void(0)" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint
			</button> --}}
			{{-- <button onclick="stamp();" class="btn btn-info btn-sm" >
				<i class="fa fa-print"></i>&nbsp;&nbsp;STAMP
			</button> --}}
			<a href="javascript:void(0)" onclick="adjust()" class="btn btn-danger btn-sm" style="color:white"><i class="fa fa-edit "></i>&nbsp;Adjust Serial</a>
			<a href="{{ url("/stamp/resumes_cl") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-calendar-check-o "></i>&nbsp;Stamp Record</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body">
					<input type="hidden" value="{{ Auth::user()->role_code }}" id="role_code" />
					<div class="row">
						<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
							<p style="position: absolute; color: White; top: 45%; left: 35%;">
								<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
							</p>
						</div>
						<div id="error" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(255,102,102); z-index: 30001; opacity: 0.8;">
							<p id="pError" style="position: absolute; color: White; top: 45%; left: 35%;">
								
							</p>
						</div>
						<div class="col-xs-4">
							<center>
								<span style="font-size: 24px;">Resume Total Production:</span>
							</center>
							<table id="planTable" name="planTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th>Model</th>
									<th>Production</th>
								</thead>
								<tbody id="planTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th>Total</th>
									<th id="planTableTotal">0</th>
								</tfoot>
							</table>
						</div>
						<div class="col-xs-3">
							<center>
								<span style="font-size: 24px">Last Counter:</span><br>
								<input id="lastCounter" type="text" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 3vw" disabled>
								<input type="button" style="width: 49%; margin-top: 5px;" class="btn btn-danger" value="MINUS" id="minus" onclick="adjustSerial(id)">
								<input type="button" style="width: 49%; margin-top: 5px;" class="btn btn-danger" value="PLUS" id="plus" onclick="adjustSerial(id)">
								<span style="font-size: 24px">Model:</span><br>
								<input id="model" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 3vw" value="YCL" disabled>
								<span style="font-size: 24px">Category:</span><br>
								<input id="category" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 3vw" value="YCL" disabled>

								<button type="button" style="width: 49%; margin-top: 5px;" class="btn btn-info" id="FG" onclick="model('INDONESIA');category('FG');"> INDONESIA</button>
								<button type="button" style="width: 49%; margin-top: 5px;" class="btn btn-info" id="KD" onclick="model('CHINA');category('KD');"> CHINA</button>
								<hr style="border: 2px solid orange">
								<button type="button" style="width: 49%; margin-top: 5px;font-size: 20px;font-weight: bold;" id="btn_start" class="btn btn-success" onclick="startStamp()">MULAI STAMP</button>
								<button type="button" style="width: 49%; margin-top: 5px;font-size: 20px;font-weight: bold;" id="btn_stop" class="btn btn-danger" onclick="stopStamp()">STOP STAMP</button>
								<div style="font-size: 40px;width: 100%" id="status_stamp"></div><br>
							</center>
						</div>
						<div class="col-xs-5">
							<center>
								<span style="font-size: 24px;">Result:</span>
							</center>
							<table id="resultTable" name="resultTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th style="width: 20%">Serial Number</th>
									<th style="width: 25%">Made In</th>
									<th style="width: 40%">Stamped At</th>
									<th style="width: 15%">#</th>
								</thead>
								<tbody id="resultTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="editModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Stamp</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<input type="text" style="text-align: center;" class="form-control" name="serialNumberText" id="serialNumberText" disabled>
					<select style="width: 100%;text-align: center;" class="form-control" id="modelText">
						<option value="'"></option>
						<option value="INDONESIA">INDONESIA</option>
						<option value="CHINA">CHINA</option>
					</select>
					
					<input type="hidden" class="form-control" name="idStamp" id="idStamp">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="destroyStamp()" class="btn btn-danger pull-left">Delete</button>
				<button type="button" onclick="updateStamp()" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="adjustModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Stamp</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<div class="row">
						<div class="col-md-12">
							<label>Prefix</label>
							<input type="text" style="text-align: center;" class="form-control" name="prefix" id="prefix">
						</div>
						<div class="col-md-12">
							<label>Last Index</label>
							<input type="text" style="text-align: center;" class="form-control" name="lastIndex" id="lastIndex">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="updateSerial()" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="titleModal">Reprint Stamp</h4>
			</div>
			<form class="form-horizontal" role="form" method="post" action="{{url('reprint/stamp')}}">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-body" id="messageModal">
					<label>Serial Number</label>
					<select class="form-control select2" name="stamp_number_reprint" style="width: 100%;" data-placeholder="Choose a Serial Number ..." id="stamp_number_reprint" required>
						<option value=""></option>
						@foreach($model2 as $model)
						<option value="{{ $model->serial_number }}">{{ $model->serial_number }}</option>
						@endforeach
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var intervalStamp;

	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});

		$('body').toggleClass("sidebar-collapse");
		stamp('ready');
		$('#model').val('YCL');
		$('#category').val('YCL');
		$('#status_stamp').html('<button style="width:100%" class="label label-danger">Stamp Berhenti</button>');
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function insert(num){
		document.form.textview.value = document.form.textview.value+num;
	}

	function model(id){
		$('#model').val(id);
	}
	function category(id){
		$('#category').val(id);
	}

	function startStamp() {
		if ($('#category').val() == 'YCL' || $('#model').val() == 'YCL') {
			openErrorGritter('Error!','Pilih Model');
			return false;
		}
		$('#btn_start').prop('disabled',true);
		$('#btn_stop').removeAttr('disabled');
		$('#status_stamp').html('<button style="width:100%" class="label label-success">Stamp Berjalan</button>');
		intervalStamp = setInterval(stamp,1000,'start');
	}

	function stopStamp() {
		clearTimeout(intervalStamp);
		$('#status_stamp').html('<button style="width:100%" class="label label-danger">Stamp Berhenti</button>');
		$('#btn_start').removeAttr('disabled');
	}

	function adjust(){
		var data = {
			originGroupCode:'042'
		}
		$.get('{{ url("fetch/assembly/clarinet/adjust") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val(result.prefix);
					$('#lastIndex').val(result.lastIndex);
					$('#adjustModal').modal('show');
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function updateSerial(){
		var prefix = $('#prefix').val();
		var lastIndex = $('#lastIndex').val();
		var data = {
			adjust:'adjust',
			prefix:prefix,
			lastIndex:lastIndex,
			originGroupCode:'042'
		}
		$.post('{{ url("stamp/assembly/clarinet/adjust") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val("");
					$('#lastIndex').val("");
					$('#adjustModal').modal('hide');
					$('#lastCounter').val(result.lastCounter);
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function adjustSerial(id){
		var data = {
			adjust:id,
			originGroupCode:'042'
		}
		$.post('{{ url("stamp/assembly/clarinet/adjust") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$("#lastCounter").val(result.lastCounter)
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
			}
		});
	}

	var prod_result;
	function stamp(statuses){
		var model = $('#model').val();
		var category = $('#category').val();
		var data = {
			model:model,
			category:category,
			statuses:statuses,
		}
		$.post('{{ url("stamp/assembly/clarinet") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					// openSuccessGritter('Success!', result.message);
					$('#lastCounter').val(result.lastCounter);
					prod_result = null;
					if (result.prod_result != '') {
						prod_result = result.prod_result;
						fetchResult();
					}
				}
				else{
					audio_error.play();
					stopStamp();
					$("#pError").html('<span style="font-size: 40px"><i class="fa fa-unlink"></i> '+ result.message +'</span>');
					$("#error").show();
				}
			}
			else{
				stopStamp();
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	}

	function fetchResult() {
		$('#resultTable').DataTable().destroy();
		$('#resultTableBody').html('');
		var resultTable = '';
		var indonesia = 0;
		var china = 0;
		for(var i = 0; i < prod_result.length;i++){
			resultTable += '<tr>';
			resultTable += '<td>'+prod_result[i].serial_number+'</td>';
			resultTable += '<td>'+prod_result[i].model+'</td>';
			resultTable += '<td>'+prod_result[i].created_at+'</td>';
			resultTable += '<td><button class="btn btn-xs btn-danger" id="'+prod_result[i].id+'" onclick="editStamp(id)"><span class="fa fa-edit"></span></button></td>';
			resultTable += '</tr>';
			if (prod_result[i].model == 'INDONESIA') {
				indonesia++;
			}
			if (prod_result[i].model == 'CHINA') {
				china++;
			}
		}
		$('#resultTableBody').append(resultTable);
		$('#resultTable').DataTable({
			"sDom": '<"top"i>rt<"bottom"flp><"clear">',
			'paging'      	: true,
			'lengthChange'	: false,
			'searching'   	: true,
			'ordering'		: false,
			'info'       	: true,
			'autoWidth'		: false,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"infoCallback": function( settings, start, end, max, total, pre ) {
				return "<b>Total "+ total +" pc(s)</b>";
			}
		});

		$('#planTable').DataTable().destroy();
		$('#planTableBody').html('');
		var resultTableResume = '';

		resultTableResume += '<tr>';
		resultTableResume += '<td>INDONESIA</td>';
		resultTableResume += '<td>'+indonesia+'</td>';
		resultTableResume += '</tr>';
		resultTableResume += '<tr>';
		resultTableResume += '<td>CHINA</td>';
		resultTableResume += '<td>'+china+'</td>';
		resultTableResume += '</tr>';

		$('#planTableBody').append(resultTableResume);
		$('#planTableTotal').html(parseInt(indonesia)+parseInt(china));
	}

	function editStamp(id){
		var data = {
			id:id
		}
		$.get('{{ url("edit/assembly/clarinet") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#modelText').val(result.logProcess.model);
					$('#serialNumberText').val(result.logProcess.serial_number);
					$('#idStamp').val(result.logProcess.id);
					$('#editModal').modal('show');
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	}

	function destroyStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelText').val();
		var data = {
			id:id,
			model:model,
			originGroupCode:'042',
			processCode:'1',
		}
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/stamp") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#idStamp').val('');
						$('#modelText').val('');
						$('#serialNumberText').val('');
						$('#editModal').modal('hide');
						openSuccessGritter('Success!', result.message);					
					}
					else{
						audio_error.play();
						alert('Attempt to retrieve data failed');	
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from sever');
				}
			});
		}
	}

	function updateStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelText').val();
		var data = {
			id:id,
			model:model,
			originGroupCode:'042',
			processCode:'1',
		}
		$.post('{{ url("edit/stamp") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#idStamp').val('');
					$('#modelText').val('');
					$('#serialNumberText').val('');
					$('#editModal').modal('hide');
					openSuccessGritter('Success!', result.message);					
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');	
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
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
			time: '3000'
		});
	}
	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
@endsection