@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
	.td_hasil:hover{
		background-color: #7dfa8c !important;
	}
	#qr_code {
		text-align: center;
		font-weight: bold;
	}
	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: -10px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<br>
		<span style="font-size: 17px;">{{$activity->leader_dept}} ~</span> <span style="font-size: 17px;" id="location"></span>
		<a href="{{url('index/production_report/index/'.$activity->department_id)}}" class="btn btn-primary pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-arrow-left"></i> Kembali</a>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#modalCreate').modal('show');"><i class="fa fa-plus"></i> Buat Audit</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="col-xs-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 0px;padding-right: 0px;">
		<span id="title_periode" style="width: 100%;font-weight: bold;font-size: 20px;">
			
		</span>
	</div>
	<div class="col-xs-6" style="padding-top: 10px; padding-bottom: 10px;">
		<div class="col-sm-6" style="text-align: right;vertical-align: middle;">
			<label for="">Select Periode<span class="text-red"> :</span></label>
		</div>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="month" placeholder="Select Month" onchange="fetchAudit()">
		</div>
	</div>
	<div class="col-xs-12" style="overflow-x: scroll;padding-left: 0px;padding-right: 0px;">
		<table class="table table-hover table-bordered table-striped" id="tableList">
			<thead style="background-color: white;border: 1px solid black" id="tableListHead">
			</thead>
			<tbody id="tableListBody" style="background-color: white;border: 1px solid black">
			</tbody>
		</table>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-lg" style="width: 1100px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: green; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Buat Audit</h3>
				</center>
				<div class="modal-body no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table class="table table-hover table-bordered table-striped" id="tableCreate">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Point Check</th>
								<th style="width: 5%;">Condition</th>
								@if($category == 'compressor' || $category == 'steam')
								<th style="width: 2%;">Evidence Cek</th>
								@endif
								<th style="width: 3%;">Evidence NG</th>
								<th style="width: 6%;">Note</th>
							</tr>
						</thead>
						<tbody id="tableCreateBody">
						</tbody>
					</table>
					<div id="image_reference">
						
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
					<button onclick="inputSafety()" class="btn btn-success">Confirm</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalNote">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
										CLOSE
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="notes" style="padding-top: 20px">
							<table class="table table-hover table-bordered table-striped" style="text-align: center;">
								<tr>
									<th style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Date</th>
									<th colspan="2" style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Decision</th>
								</tr>
								<tr>
									<td style="width: 1%;background-color: white" id="date_detail"></td>
									<td colspan="2" style="width: 1%;background-color: white" id="decision_detail"></td>
								</tr>
								<tr>
									<th style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Point</th>
									<th colspan="2" style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Note</th>
								</tr>
								<tr>
									<td style="width: 1%;background-color: white" id="point_detail"></td>
									<td colspan="2" style="width: 1%;background-color: white" id="note_detail"></td>
								</tr>
								<tr>
									<th colspan="3" style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Evidence NG</th>
								</tr>
								<tr>
									<td colspan="3" style="width: 1%;background-color: white" id="evidence_detail"></td>
								</tr>
								<?php if ($category == 'compressor' || $category == 'steam'): ?>
									<tr>
										<th style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Handling</th>
										<th style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Handled By</th>
										<th style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Handled At</th>
									</tr>
									<tr>
										<td style="width: 1%;background-color: white" id="handling"></td>
										<td style="width: 1%;background-color: white" id="handled_by"></td>
										<td style="width: 1%;background-color: white" id="handled_at"></td>
									</tr>
									<tr>
										<th colspan="3" style="width: 1%;background-color: rgba(126,86,134,.7);color: white;">Evidence Penanganan</th>
									</tr>
									<tr>
										<td colspan="3" style="width: 1%;background-color: white" id="evidence"></td>
									</tr>
								<?php endif ?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="scanModal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-center"><b>SCAN QR CODE HERE</b></h4>
					</div>
					<div class="modal-body">
						<div id='scanner' class="col-xs-12">
							<input type="hidden" name="qr_code_scan" id="qr_code_scan" value="0">
							<input type="hidden" name="qr_code_scan_index" id="qr_code_scan_index" value="0">
							<center>
								<div id="loadingMessage">
									🎥 Unable to access video stream
									(please make sure you have a webcam enabled)
								</div>
								<video autoplay muted playsinline id="video"></video>
								<div id="output" hidden>
									<div id="outputMessage">No QR code detected.</div>
								</div>
							</center>								
						</div>

						<p style="visibility: hidden;">camera</p>
						<input type="hidden" id="code">
					</div>
				</div>
			</div>
		</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");
		$('#month').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		fetchAudit();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var count = 0;
	var video;

	function fetchAudit(){
		$('#loading').show();
		var data = {
			category:'{{$category}}',
			id:'{{$id}}',
			month:$('#month').val()
		}
		$.get('{{ url("fetch/daily/audit") }}',data, function(result, status, xhr){
			if(result.status){

				// $('#tableList').DataTable().clear();
				// $('#tableList').DataTable().destroy();
				$('#tableListHead').html("");
				var tableListHead = "";

				tableListHead += '<tr>';
				tableListHead += '<th style="text-align: right; width: 0.1%;border: 1px solid black;width:1%;">No.</th>';
				tableListHead += '<th style="text-align: left; width: 0.1%;border: 1px solid black;width:10%;">Point Check</th>';
				for(var i = 0; i < result.months.length;i++){
					if ('{{date("d")}}' == result.months[i].date_name) {
						var color = 'background-color:gold';
					}else{
						var color = '';
					}
					tableListHead += '<th style="text-align: center; width: 0.1%;border: 1px solid black;width:1%;'+color+'">'+result.months[i].date_name+'</th>';
				}
				tableListHead += '</tr>';

				$('#tableListHead').append(tableListHead);

				$('#tableListBody').html("");
				var tableListBody = "";

				var auditors = [];

				$('#location').html('');

				if (result.point_check.length > 0) {
					$('#location').html(result.point_check[0].location);
					for(var i = 0; i < result.point_check.length;i++){
						tableListBody += '<tr>';
						tableListBody += '<td style="text-align: right; padding-right:10px; width: 0.1%;border: 1px solid black;">'+(i+1)+'</td>';
						tableListBody += '<td style="text-align: left; padding-left:10px; width: 0.1%;border: 1px solid black;">'+result.point_check[i].point_check+'</td>';
						for(var k = 0; k < result.months.length;k++){
							var hasil = '';
							var auditor = '';
							var note = '';
							var point = '';
							var evidence = '';
							var handling = '';
							var handled_by = '';
							var handled_at = '';
							var handling_evidence = '';
							for(var j = 0; j < result.audit.length;j++){
								if (result.audit[j].date == result.months[k].date && result.audit[j].point_check == result.point_check[i].point_check) {
									hasil = result.audit[j].condition;
									if (result.audit[j].auditor_name.split(' ').length > 1) {
										auditor = result.audit[j].auditor_id+' - '+result.audit[j].auditor_name.split(' ')[0]+' '+result.audit[j].auditor_name.split(' ')[1];
									}else{
										auditor = result.audit[j].auditor_id+' - '+result.audit[j].auditor_name;
									}
									note = result.audit[j].note;
									point = result.audit[j].point_check;
									evidence = result.audit[j].evidence;
									handling = result.audit[j].handling;
									handled_by = result.audit[j].handled_id+' - '+result.audit[j].handled_name;
									handling_at = result.audit[j].finished_at;
									handling_evidence = result.audit[j].handling_evidence;
								}
							}
							var cursor = '';
							var onclick = '';
							if (hasil == 'OK') {
		                        var icon = '&#9711;';
		                        var color = '#e0ffe4';
		                      }else if (hasil == 'NG') {
		                        var color = '#ffd4d4';
		                        var icon = '&#9747;';
		                        cursor = 'cursor:pointer;';
		                        onclick = 'onclick="fetchNote(\''+note+'\',\''+result.months[k].date+'\',\''+icon+'\',\''+point+'\',\''+evidence+'\',\''+handling+'\',\''+handled_by+'\',\''+handling_at+'\',\''+handling_evidence+'\')"';
		                      }else{
		                      	var color = '#fff';
		                        var icon = '';
		                      }
							tableListBody += '<td class="td_hasil" style="text-align: center; width: 0.1%;border: 1px solid black;background-color:'+color+';'+cursor+'" '+onclick+'>'+icon+'</td>';
							auditors.push(auditor);
						}
						tableListBody += '</tr>';
					}
				}

				if (result.point_check.length > 0) {
					tableListBody += '<tr>';
					tableListBody += '<td style="text-align: right; width: 0.1%;border: 1px solid black;background-color:white;padding-right:10px;">#</td>';
					tableListBody += '<td style="text-align: left; width: 0.1%;border: 1px solid black;background-color:white;padding-left:10px;">Auditor</td>';
						for(var i = 0; i < result.months.length;i++){
							tableListBody += '<td style="text-align: center; width: 0.1%;height:120px;border: 1px solid black;background-color:white;"><div style="transform: rotate(90deg);-moz-transform: rotate(90deg) !important;">'+auditors[i]+'</div></td>';
						}
					tableListBody += '</tr>';
				}

				$('#tableListBody').append(tableListBody);

				// $('#tableList').DataTable({
				// 	'dom': 'Bfrtip',
				// 	'responsive':true,
				// 	'lengthMenu': [
				// 	[ 25, 50, -1 ],
				// 	[ '25 rows', '50 rows', 'Show all' ]
				// 	],
				// 	'buttons': {
				// 		buttons:[
				// 		{
				// 			extend: 'pageLength',
				// 			className: 'btn btn-default',
				// 		},
				// 		{
				// 			extend: 'copy',
				// 			className: 'btn btn-success',
				// 			text: '<i class="fa fa-copy"></i> Copy',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		{
				// 			extend: 'excel',
				// 			className: 'btn btn-info',
				// 			text: '<i class="fa fa-file-excel-o"></i> Excel',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		{
				// 			extend: 'print',
				// 			className: 'btn btn-warning',
				// 			text: '<i class="fa fa-print"></i> Print',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		]
				// 	},
				// 	'paging': true,
				// 	'lengthChange': true,
				// 	'searching': true,
				// 	'ordering': true,
				// 	'order': [],
				// 	'info': true,
				// 	'autoWidth': true,
				// 	"sPaginationType": "full_numbers",
				// 	"bJQueryUI": true,
				// 	"bAutoWidth": false,
				// 	"processing": true
				// });

				$('#tableCreateBody').html('');
				var tableCreateBody = '';

				count = 0;

				count = result.point_check.length;

				$('#image_reference').html('');

				if (count > 0) {
					for(var i = 0; i < result.point_check.length;i++){
						tableCreateBody += '<tr>';
						tableCreateBody += '<td style="background-color:white;border:2px solid black;width:1%;text-align:right;padding-right:10px;">'+(i+1)+'</td>';
						tableCreateBody += '<td style="background-color:white;border:2px solid black;width:1%;padding-left:10px;" id="point_check_'+i+'">'+result.point_check[i].point_check+'<input type="hidden" id="category_'+i+'" value="'+result.point_check[i].category+'"><input type="hidden" id="location_'+i+'" value="'+result.point_check[i].location+'"><input type="hidden" id="image_reference_'+i+'" value="'+result.point_check[i].image_reference+'"><input type="hidden" id="id_point_'+i+'" value="'+result.point_check[i].scan_index+'"></td>';
						tableCreateBody += '<td style="background-color:white;border:2px solid black;width:1%;text-align:center">';
						tableCreateBody += '<div class="col-xs-4">';
						tableCreateBody += '<label class="containers">&#9711;';
						  tableCreateBody += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK" onclick="checkCondition(this.value,this.id,\''+i+'\')">';
						  tableCreateBody += '<span class="checkmark"></span>';
						tableCreateBody += '</label>';
						tableCreateBody += '</div>';
						tableCreateBody += '<div class="col-xs-4">';
						tableCreateBody += '<label class="containers">&#9747;';
						  tableCreateBody += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG" onclick="checkCondition(this.value,this.id,\''+i+'\')">';
						  tableCreateBody += '<span class="checkmark"></span>';
						tableCreateBody += '</label>';
						tableCreateBody += '</div>';
						tableCreateBody += '</td>';
						if (result.point_check[i].category == 'compressor' || result.point_check[i].category == 'steam') {
							tableCreateBody += '<td style="background-color:white;border:2px solid black;">';
							tableCreateBody += '<div class="input-group input-group-lg">';
								tableCreateBody += '<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 15px;">';
									tableCreateBody += '<i class="fa fa-qrcode"></i>';
								tableCreateBody += '</div>';
								tableCreateBody += '<input type="text" class="form-control" placeholder="SCAN QR CODE" id="qr_code_'+i+'">';
								tableCreateBody += '<span class="input-group-btn">';
									tableCreateBody += '<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal" onclick="scanQR(\''+i+'\')"><i class="fa fa-camera"></i>&nbsp;Scan QR</button>';
								tableCreateBody += '</span>';
							tableCreateBody += '</div>';
							tableCreateBody += '</td>';
						}
						tableCreateBody += '<td style="background-color:white;border:2px solid black;width:1%;"><div id="div_evidence_'+i+'" style="display:none"><input type="file" style="width:100%" id="evidence_'+i+'"></div></td>';
						tableCreateBody += '<td style="background-color:white;border:2px solid black;width:1%;"><textarea style="width:100%" id="note_'+i+'"></textarea></td>';
						tableCreateBody += '</tr>';
					}
				}

				if (count > 0) {
					if (result.point_check[0].image_reference != null) {
						var url = '{{url("data_file/st/")}}/'+result.point_check[0].image_reference;
						var img = '<img src="'+url+'" style="width:100%">';
						$('#image_reference').append(img);
					}
				}

				$('#tableCreateBody').append(tableCreateBody);


				$('#title_periode').html('Periode : '+result.monthTitle);

				$('#loading').hide();

			}
			else{
				$('#loading').hide();
				alert('Unidentified Error');
				audio_error.play();
				return false;	
			}
		});
	}

	function scanQR(index) {
		$("#qr_code_scan").val('qr_code_'+index+'');
		$("#qr_code_scan_index").val(index);
	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	$( "#scanModal" ).on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#scanModal').on('hidden.bs.modal', function () {
		videoOff();
	});

	function showCheck(kode) {
		$(".modal-backdrop").add();
		$('#scanner').show();

		var vdo = document.getElementById("video");
		video = vdo;
		var tickDuration = 200;
		video.style.boxSizing = "border-box";
		video.style.position = "absolute";
		video.style.left = "0px";
		video.style.top = "0px";
		video.style.width = "400px";
		video.style.zIndex = 1000;

		var loadingMessage = document.getElementById("loadingMessage");
		var outputContainer = document.getElementById("output");
		var outputMessage = document.getElementById("outputMessage");

		navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
			video.srcObject = stream;
			video.play();
			setTimeout(function() {tick();},tickDuration);
		});

		function tick(){
			loadingMessage.innerText = "⌛ Loading video..."

			try{

				loadingMessage.hidden = true;
				video.style.position = "static";

				var canvasElement = document.createElement("canvas");            
				var canvas = canvasElement.getContext("2d");
				canvasElement.height = video.videoHeight;
				canvasElement.width = video.videoWidth;
				canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
				var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
				var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
				if (code) {
					outputMessage.hidden = true;
					videoOff();
					document.getElementById($('#qr_code_scan').val()).value = code.data;
					checkCode(code.data,$('#qr_code_scan_index').val());

				}else{
					outputMessage.hidden = false;
				}
			} catch (t) {
				console.log("PROBLEM: " + t);
			}

			setTimeout(function() {tick();},tickDuration);
		}

	}

	function checkCode(code,index) {
		if (code == $('#id_point_'+index).val()) {
			$('#'+$('#qr_code_scan').val()).prop('disabled',true);
			// $('#scanModal').modal('hide');
			$('#scanModal').hide();
			openSuccessGritter('Success!','Lokasi Sesuai');
		}else{
			openErrorGritter('Error!','Lokasi Tidak Sesuai');
			$('#'+$('#qr_code_scan').val()).val('');
			// $('#scanModal').modal('hide');
			$('#scanModal').hide();
		}
	}

	function checkCondition(value,id,index) {
		if (value == 'NG') {
			$("#div_evidence_"+index).show();
		}else{
			$("#div_evidence_"+index).hide();
		}
	}

	function fetchNote(note,date,hasil,point,evidence,handling,handled_by,handled_at,handling_evidence) {
		$('#modalNote').modal('show');
		if (note == null || note == 'null') {
			$('#note_detail').html('');
		}else{
			$('#note_detail').html(note);
		}
		$('#date_detail').html(date);
		$('#decision_detail').html(hasil);
		$('#point_detail').html(point);
		var url = '{{url("data_file/daily_audit/".$category)}}/'+evidence;
		$('#evidence_detail').html('<img src="'+url+'" style="width:300px">');
		if ('{{$category}}' == 'compressor' || '{{$category}}' == 'steam') {
			if (handling != 'null') {
				$('#handling').html(handling);
			}
			if (handled_by != 'null - null') {
				$('#handled_by').html(handled_by);
			}
			if (handled_at != 'null') {
				$('#handled_at').html(handled_at);
			}
			if (handling_evidence != 'null') {
				var url2 = '{{url("data_file/daily_audit/".$category)}}/penanganan/'+handling_evidence;
				$('#evidence').html('<img src="'+url2+'" style="width:300px">');
			}
		}
	}

	function inputSafety(){
		$('#loading').show();
		for (var i = 0; i < count; i++) {
			var decision = '';
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            decision = $(this).val();
	        });
			if (decision == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Input Semua Kondisi');
				return false;
			}

			if ($('#note_'+i+'').val() == '' && decision == 'NG') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Input Note');
				return false;
			}
		}

		if ('{{$category}}' == 'compressor' || '{{$category}}' == 'steam') {
			for (var i = 0; i < count; i++) {
				if ($('#qr_code_'+i).val() == '') {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Scan Semua QR Code');
					return false;
				}
			}
		}

		// var point_check = [];
		// var category = [];
		// var location = [];
		// var image_reference = [];
		// var condition = [];
		// var note = [];
		// var activity_list_id = [];
		// var filenames = [];
		// var files = [];
		// var extensions = [];

		var stat = 0;


		for (var i = 0; i < count; i++) {
			var file = $('#evidence_'+i).prop('files')[0];
			var filename = $('#evidence_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[0];
			var extension = $('#evidence_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[1];

			var decision = '';
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            decision = $(this).val();
	        });
			// point_check.push($('#point_check_'+i+'').text());
			// category.push($('#category_'+i+'').val());
			// location.push($('#location_'+i+'').val());
			// image_reference.push($('#image_reference_'+i+'').val());
			// condition.push(decision);
			// note.push($('#note_'+i+'').val());
			// activity_list_id.push('{{$id}}');

			var formData = new FormData();
			formData.append('point_check',$('#point_check_'+i+'').text());
			formData.append('category',$('#category_'+i+'').val());
			formData.append('location',$('#location_'+i+'').val());
			formData.append('image_reference',$('#image_reference_'+i+'').val());
			formData.append('condition',decision);
			formData.append('note',$('#note_'+i+'').val());
			formData.append('activity_list_id','{{$id}}');
			formData.append('file',file);
			formData.append('filename',filename);
			formData.append('extension',extension);

			$.ajax({
				url:"{{ url('input/daily/audit') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						stat += 1;
						if (stat == count) {
							openSuccessGritter('Success', data.message);
							$('#modalCreate').modal('hide');
							fetchAudit();
							audio_ok.play();
							$('#loading').hide();
						}
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
		}

		// var data = {
		// 	point_check:point_check,
		// 	category:category,
		// 	location:location,
		// 	image_reference:image_reference,
		// 	condition:condition,
		// 	note:note,
		// 	activity_list_id:activity_list_id,
		// }

		// $.post('{{ url("input/daily/audit") }}', data, function(result, status, xhr){
		// 	if(result.status){
		// 		openSuccessGritter('Success', result.message);
		// 		// $('#tableCreateBody').html("");
		// 		$('#modalCreate').modal('hide');
		// 		fetchAudit();
		// 		audio_ok.play();
		// 		$('#loading').hide();
		// 	}
		// 	else{
		// 		$('#loading').hide();
		// 		openErrorGritter('Error!', result.message);
		// 		audio_error.play();
		// 		return false;	
		// 	}
		// });
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection