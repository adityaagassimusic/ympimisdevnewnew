@extends('layouts.display')
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
	border:1px solid rgb(211,211,211);
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
.container_checkmark {
  display: block;
  position: relative;
  padding-left: 10px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 20px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }} - {{ $product }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 12px;padding-right: 20px !important;width: 49%;background-color: white;margin-left: 15px;">
					<!-- <input type="text" id="operators" placeholder="Operator" > -->
					<span id="operators" style="width: 100%;font-size: 15px;text-align:center;background-color: white;"></span>
			</div>
			<div class="col-xs-6" style="padding-bottom: 12px;width: 50%;text-align:center;">
				<input type="text" name="tag" id="tag" placeholder="Scan Daisha di Sini . . ." style="width: 100%;font-weight: bold;font-size: 20px;height: 45px;text-align: center;">
			</div>
		<!-- <div class="col-xs-6" style="text-align: center;padding-left: 5px;"> -->
			<!-- <div class="col-xs-12" style="padding-right: 0px;">
				<input type="text" id="tag_product" placeholder="Scan Card Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
			</div> -->
			<!-- <div class="col-xs-2" style="padding-right: 0px;padding-left: 0px">
				<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
					CANCEL
				</button>
			</div> -->
		<!-- </div> -->
		<div class="col-xs-12" style="text-align: center;" id="divTray">
			<div style="width: 100%;background-color: lightgreen;padding: 5px;font-weight: bold;">
				DAISHA
			</div>
			<div style="width: 100%;background-color: white;padding: 5px;font-weight: bold;font-size: 50px">
				<span id="daisha_info"></span>
			</div>
		</div>
		<div class="col-xs-12" style="text-align: center;padding-top: 10px">
			<div class="box box-solid">
				<div class="box-body">
					<input type="hidden" name="tray" id="tray">
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<input type="hidden" id="operator_id">
						<input type="hidden" id="operator_name">
			            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
			                <tr>
			                  <th style="width: 1%;">#</th>
			                  <th style="width: 1%;text-align: left;">Daisha</th>
			                  <th style="width: 2%;text-align: left">Card</th>
							  <th style="width: 2%;text-align: left">Model</th>
							  <th style="width: 1%;text-align: left">Timestamp</th>
							  <th style="width: 2%;text-align: left">Serial Number</th>
							  <th style="width: 2%;text-align: left">Action</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
		            <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
		            	<button class="btn btn-danger" id="btn_cancel" onclick="cancelSeasoning()" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">CANCEL</button>
		            </div>
		            <div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;">
		            	<button class="btn btn-success" id="btn_finish" onclick="startSeasoning('start')" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">MASUK SEASONING</button>
		            </div>
		            <!-- <div class="col-xs-12" style="padding-right: 0px;padding-left: 5px;">
		            	<button class="btn btn-warning" id="" onclick="startSeasoning('empty')" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">KOSONGKAN DAISHA</button>
		            </div> -->
		        </div>
		    </div>
		</div>
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card / Ketik NIK" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalLocation">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="background-color: orange">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">PILIH LOKASI</h4>
				</div>
				<div class="modal-body">
					<div class="col-xs-12">
						<table class="table table-bordered table-striped table-hover" style="width: 100%;">
				            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
				                <tr>
				                  <th style="width: 1%;">#</th>
				                  <th style="width: 3%;text-align: center;">Process</th>
				                  <th style="width: 2%;text-align: center">Action</th>
				                </tr>
				            </thead>
				            <tbody>
				            	<!-- <tr >
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">1</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Masuk Seasoning (Material Awal)</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button id="starting" class="btn btn-success" onclick="confirmLocation('Masuk Seasoning (Material Awal)')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr> -->
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">1</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Keluar Seasoning</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" id="btn_keluar_seasoning" onclick="$('#modalLocation').hide();$('#modalLocation').modal('show');$('#modalModel').modal('show');" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">2</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Masuk Assembly</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" onclick="confirmLocation('Masuk Assembly','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">3</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Selesai Assembly</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" onclick="confirmLocation('Selesai Assembly','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">4</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Masuk Kensa</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" onclick="confirmLocation('Masuk Kensa','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">5</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Selesai Kensa</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" onclick="confirmLocation('Selesai Kensa','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">6</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Kembali ke Seasoning</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button class="btn btn-success" onclick="confirmLocation('Kembali ke Seasoning','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr>
				            	<!-- <tr>
				            		<td style="padding:7px;text-align:right;padding-right:5px;vertical-align: middle;font-size:16.5px;">7</td>
				            		<td style="padding:7px;text-align:left;padding-left:5px;vertical-align: middle;font-size:16.5px;">Material Selesai</td>
				            		<td style="padding:7px;text-align:center;">
				            			<button id="cleaning" class="btn btn-success" onclick="confirmLocation('Material Selesai','')" style="font-weight: bold;">
											Pilih
										</button>
				            		</td>
				            	</tr> -->
							</tbody>
			            </table>
						<!-- <button class="btn btn-success pull-left" id="btn_next_loc" onclick="confirmLocation()" style="width:85%;font-size: 25px;font-weight: bold;">
							NEXT LOC
						</button> -->
					</div>
					<div class="col-xs-12">
						<button class="btn btn-danger pull-right" style="width:100%;font-size: 20px;font-weight: bold;" onclick="cancelTag()">
							Batal
						</button>
					</div>
				</div>
				<div class="modal-footer">
			    </div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalModel">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="background-color: orange">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">PILIH MODEL</h4>
				</div>
				<div class="modal-body">
					<div class="col-xs-6">
						<button class="btn btn-success" onclick="confirmLocation('Keluar Seasoning','YCL450')" style="font-weight: bold;font-size: 25px;width: 100%">
							YCL450
						</button>
					</div>
					<div class="col-xs-6">
						<button class="btn btn-success" onclick="confirmLocation('Keluar Seasoning','YCL400AD')" style="font-weight: bold;font-size: 25px;width: 100%">
							YCL400AD
						</button>
					</div>
					<div class="col-xs-12" style="margin-top: 20px;">
						<button class="btn btn-danger pull-right" style="width:100%;font-size: 20px;font-weight: bold;" onclick="cancelTag()">
							Batal
						</button>
					</div>
				</div>
				<div class="modal-footer">
			    </div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var counter = 0;
	var arrPart = [];
	var intervalCheck;

	jQuery(document).ready(function() {
		$('#resultScanBody').html("");
		$('#tray').val("");
		$('#tag').removeAttr('disabled');
		$('#tag').val("");
		$('#daisha_info').html('');
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		fillResult();
		setInterval(fillResult,200000);

      $('body').toggleClass("sidebar-collapse");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_id").val("");
		$("#operator_name").val("");
		$("#operators").html("-");
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operators').html(result.employee.employee_id+' - '+result.employee.name);
						$('#operator_id').val(result.employee.employee_id);
						$('#operator_name').val(result.employee.name);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}		
		}
	});

	var packing = null;

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				$('#loading').show();
				var data = {
					tag : $("#tag").val(),
					origin_group_code:'{{$origin_group_code}}',
					statuses:'IN'
				}
				
				$.get('{{ url("scan/seasoning") }}', data, function(result, status, xhr){
					if(result.status){
						$('#daisha_info').html('Daisha '+result.tags.remark);
						var count_tray = 0;
						for(var i = 0; i < trays.length;i++){
							if (trays[i].location == result.tags.remark) {
								packing = result.packing;
								selectTray(result.tags.remark,trays[i].seasoning_id);
								count_tray++;
							}
						}
						if (count_tray == 0) {
							$('#loading').hide();
							audio_error.play();
							openErrorGritter('Error!','Daisha Sudah Masuk');
							$('#tag').removeAttr('disabled');
							$('#tag').val('');
							$('#tag').focus();
							$('#daisha_info').html('');
							return false;
						}
						openSuccessGritter('Success!', result.message);
						$('#tag').prop('disabled',true);
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
					}
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Tag Invalid.');
				audio_error.play();
				$('#tag').removeAttr('disabled');
				$("#tag").val("");
			}		
		}
	});

	function cancel(){
		$('#tag_product').val("");
		$('#tag_product').focus();
	}

	function cancelTag(){
		$('#modalModel').modal('hide');
		$('#modalLocation').modal('hide');
		$('#tag_product').val("");
		$('#tag_product').focus();
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	var all_tray = null;
	var trays = null;

	function fillResult() {
		var data = {
			origin_group_code:'{{$origin_group_code}}'
		}
		$.get('{{ url("fetch/seasoning_in") }}',data, function(result, status, xhr){
			if(result.status){
				all_tray = result.all_tray;
				// $('#divTray').html('');

				// var tray = '';

				// for(var i = 0; i < result.tray.length;i++){
				// 	tray += '<div class="col-xs-4" style="padding:5px">';
				// 	tray += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:30px;"';
				// 	// tray += 'onClick="selectTray(\''+result.tray[i].location+'\',\''+result.tray[i].seasoning_id+'\')"';
				// 	tray += '>DAISHA '+result.tray[i].location+'<br>';
				// 	tray += '</button>';
				// 	tray += '</div>';
				// }

				trays = result.tray;

				// $('#divTray').append(tray);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function selectTray(tray,seasoning_id) {
		$('#loading').show();
		$('#tray').val("");
		$('#resultScanBody').html('');
		var trays = '';
		var index = 1;
		for(var i = 0; i < all_tray.length;i++){
			var re = new RegExp(all_tray[i].seasoning_id, 'g');
			if (tray == all_tray[i].location && seasoning_id.match(re)) {
				trays += '<tr>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+index+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].location+'</td>';
				trays += '<td style="text-align:right;padding-left:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[0]+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[1]+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].timestamps+'</td>';
				var packing_status = '';
				for(var j = 0; j < packing.length;j++){
					if (packing[j].serial_number == all_tray[i].material.split(' - ')[2]) {
						packing_status = 'Sudah Packing';
					}
				}
				if (packing_status == '') {
					trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;font-weight:bold;color:red;">'+all_tray[i].material.split(' - ')[2]+'</td>';
					trays += '<td style="text-align:left;padding-left:5px;border:1px solid black;"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Check<input type="checkbox" name="check" id="check" class="check" value="'+all_tray[i].id+'"><span class="checkmark_checkmark"></span></label></td>';
				}else{
					trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[2]+'</td>';
					trays += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+packing_status+'</td>';
				}
				trays += '</tr>';
				index++;
			}
		}
		$('#resultScanBody').append(trays);
		$('#tray').val(tray);
	}

	function cancelSeasoning() {
		$("#tray").val('');
		$("#daisha_info").html('');
		$('#tag').removeAttr('disabled');
		$("#tag").val('');
		$('#tag').focus();
		$('#resultScanBody').html('');
	}

	function startSeasoning(empty) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#tray').val() == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Pilih Daisha.');
				return false;
			}
			var check = [];
			$("input[name='check']:checked").each(function (i) {
		        check[i] = $(this).val();
	        });

	        if (check.length == 0) {
	        	$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Pilih Material.');
				return false;
	        }

			var data = {
				tray:$('#tray').val(),
				tag:$('#tag').val(),
				id:check,
				empty:empty
			}

			$.post('{{ url("input/seasoning_in") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success','Success Masuk Seasoning');
					fillResult();
					$('#daisha_info').html('');
					$('#tag').removeAttr('disabled');
					$('#tag').val('');
					$('#tag').focus();
					$('#resultScanBody').html('');
					$('#tray').val('');
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;
				}
			});
		}
	}

	function timeDifference(date1,date2) {
	    var difference = date1.getTime() - date2.getTime();

	    var daysDifference = Math.floor(difference/1000/60/60/24);
	    difference -= daysDifference*1000*60*60*24

	    var hoursDifference = Math.floor(difference/1000/60/60);
	    difference -= hoursDifference*1000*60*60

	    var minutesDifference = Math.floor(difference/1000/60);
	    difference -= minutesDifference*1000*60

	    var secondsDifference = Math.floor(difference/1000);

	    var day = '';
	    var hour = '';
	    var min = '';
	    var sec = '';
	    if (daysDifference != 0) {
	    	day = addZero(daysDifference);
	    }

	    // if (hoursDifference != 0) {
	    	hour = addZero(hoursDifference);
	    // }

	    // if (minutesDifference != 0) {
	    	min = addZero(minutesDifference);
	    // }

	    // if (secondsDifference != 0) {
	    	sec = addZero(secondsDifference);
	    // }
	    return hour+':' + min+':' + sec;
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection