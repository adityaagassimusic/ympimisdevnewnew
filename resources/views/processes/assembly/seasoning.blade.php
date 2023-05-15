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
		<!-- <div class="col-xs-12" style="text-align: center;padding-right: 5px;">
			
		</div> -->
			<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 12px;padding-right: 20px !important;width: 49%;background-color: white;margin-left: 15px;">
					<!-- <input type="text" id="operators" placeholder="Operator" > -->
					<span id="operators" style="width: 100%;font-size: 15px;text-align:center;background-color: white;"></span>
			</div>
			<div class="col-xs-6" style="width: 50%;text-align:center;">
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
		<div class="col-xs-12" style="font-weight: bold;text-align: center;font-size: 20px;">
			<div style="padding: 5px;background-color: lightgreen;">AFTER INCOMING CHECK QA</div>
		</div>
		<div class="col-xs-12" style="text-align: center;" id="divTray">
			
		</div>
		<div class="col-xs-12" style="font-weight: bold;text-align: center;font-size: 20px;">
			<div style="padding: 5px;background-color: lightblue;">IN PROGRESS <span id="qty_set"></span></div>
		</div>
		<div class="col-xs-12" style="text-align: center;" id="divTraySet">
			
		</div>
		<!-- <div class="col-xs-12" style="font-weight: bold;text-align: center;font-size: 20px;">
			<div style="padding: 5px;background-color: lightcoral;">IN PROGRESS (KANGO) <span id="qty_kango"></span></div>
		</div> -->
		<div class="col-xs-12" style="text-align: center;" id="divTrayKango">
			
		</div>
		<!-- <div class="col-xs-12" style="text-align: center;">
			
		</div>
		<div class="col-xs-12" style="text-align: center;padding-top: 10px">
			<div class="box box-solid">
				<div class="box-body">
					
		        </div>
		    </div>
		</div> -->
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

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="background-color: orange">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">Detail Material</h4>
				</div>
				<div class="modal-body">
					<div class="col-xs-12">
						<!-- <div style="width: 100%;background-color: lightgreen;padding: 5px;font-weight: bold;text-align: center;">
							DAISHA
						</div> -->
						<div style="width: 100%;background-color: white;padding: 5px;font-weight: bold;font-size: 50px;text-align: center;">
							<span id="daisha_info"></span>
						</div>
					</div>
					<div class="col-xs-12">
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
								  <th style="width: 2%;text-align: left">Serial Number</th>
								  <th style="width: 1%;text-align: left">Time IN</th>
								  <th style="width: 1%;text-align: left">Plan Time OUT</th>
								  <th style="width: 2%;text-align: left">PIC QA</th>
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
			            	<button class="btn btn-success" id="btn_finish" onclick="finishSeasoning()" disabled="true" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">KELUAR SEASONING</button>
			            </div>
					</div>
					<!-- <div class="col-xs-12">
						<button class="btn btn-danger pull-right" style="width:100%;font-size: 20px;font-weight: bold;" onclick="cancelTag()">
							Batal
						</button>
					</div> -->
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
		$('#btn_finish').prop('disabled',true);
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
		$("#tag_product").val("");
		
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

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				$('#loading').show();
				var data = {
					tag : $("#tag").val(),
					origin_group_code:'{{$origin_group_code}}',
					statuses:'OUT'
				}
				
				$.get('{{ url("scan/seasoning") }}', data, function(result, status, xhr){
					if(result.status){
						$('#daisha_info').html('Daisha '+result.tags.remark);
						var count_tray = 0;
						for(var i = 0; i < trays.length;i++){
							if (trays[i].location == result.tags.remark) {
								if (parseFloat(trays[i].days) < 7) {
									var click = 'Belum';
								}else{
									var click = 'Sudah';
								}
								if (click == 'Sudah') {
									selectTray(result.tags.remark,trays[i].seasoning_id,'OUT');
									count_tray++;
								}
							}
						}
						if (count_tray == 0) {
							$('#loading').hide();
							audio_error.play();
							openErrorGritter('Error!','Daisha Belum Siap Diambil / Daisha sedang di luar.');
							$('#tag').removeAttr('disabled');
							$('#tag').val('');
							$('#tag').focus();
							$('#daisha_info').html('');
							$('#btn_finish').prop('disabled',true);
							return false;
						}
						$('#btn_finish').removeAttr('disabled');
						openSuccessGritter('Success!', result.message);
						$('#tag').prop('disabled',true);
						$('#loading').hide();
					}
					else{
						$('#btn_finish').prop('disabled',true);
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
					}
				});
			}
			else{
				$('#btn_finish').prop('disabled',true);
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
		$.get('{{ url("fetch/seasoning") }}',data, function(result, status, xhr){
			if(result.status){
				all_tray = result.all_tray;

				$('#divTray').html('');
				var tray = '';
				for(var i = 0; i < result.tray.length;i++){
					if (result.tray[i].location != 'SET' && result.tray[i].location != 'KANGO') {
						tray += '<div class="col-xs-4" style="padding:5px">';
						if (parseFloat(result.tray[i].days) < 7) {
							var btns = 'btn btn-danger';
							var click = 'Belum';
						}else{
							var btns = 'btn btn-success';
							var click = 'Sudah';
						}
						var ycl450 = 0;
						var ycl400 = 0;
						var outs = 'NO';
						tray += '<button class="'+btns+'" style="width:100%;font-weight:bold;font-size:20px;text-align:left;"';
						tray += 'onClick="selectTray(\''+result.tray[i].location+'\',\''+click+'\',\''+outs+'\')"';
						tray += '>DAISHA '+result.tray[i].location+'<span style="font-size:20px;" class="pull-right">YCL450 = '+result.tray[i].ycl450+' Pc(s)</span><br>';
						tray += '<span style="font-size:30px;">'+result.tray[i].days+' Day(s)</span>';
						tray += '<span style="font-size:20px;" class="pull-right">YCL400 = '+result.tray[i].ycl400+' Pc(s)</span>';
						tray += '</button>';
						tray += '</div>';
					}
				}
				$('#divTray').append(tray);

				$('#divTraySet').html('');
				var traySet = '';
				var qty_set = 0;
				var sets = [];
				var ycl450 = 0;
				var ycl400 = 0;
				for(var i = 0; i < result.all_tray.length;i++){
					if (result.all_tray[i].location == 'SET') {
						if (result.all_tray[i].material.split(' - ')[1].match(/450/gi)) {
							ycl450++;
						}
						if (result.all_tray[i].material.split(' - ')[1] == 'YCL400AD') {
							ycl400++;
						}
					// 	traySet += '<div class="col-xs-3" style="padding:5px">';
					// 	traySet += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:20px;"';
					// 	traySet += '>'+result.all_tray[i].material.split(' - ')[1]+' - '+result.all_tray[i].material.split(' - ')[2]+'<br>';
					// 	traySet += '</button>';
					// 	traySet += '</div>';
						qty_set++;
					}
				}
				var locs = 'SET';
				var click = 'Sudah';
				var outs = 'NO';
				if (ycl400 > 0) {
					var model = 'YCL400AD';
					traySet += '<div class="col-xs-3" style="padding:5px">';
					traySet += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:20px;"';
					traySet += 'onClick="selectTraySet(\''+locs+'\',\''+click+'\',\''+outs+'\',\''+model+'\')"';
					traySet += '>YCL400AD - '+ycl400+' Pc(s)<br>';
					traySet += '</button>';
					traySet += '</div>';
				}
				if (ycl450 > 0) {
					var model = '450';
					traySet += '<div class="col-xs-3" style="padding:5px">';
					traySet += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:20px;"';
					traySet += 'onClick="selectTraySet(\''+locs+'\',\''+click+'\',\''+outs+'\',\''+model+'\')"';
					traySet += '>YCL450 - '+ycl450+' Pc(s)<br>';
					traySet += '</button>';
					traySet += '</div>';
				}

				$('#divTraySet').append(traySet);
				if (qty_set > 0) {
					$('#qty_set').html(' - '+qty_set+' Pc(s)');
				}

				$('#divTrayKango').html('');
				var trayKango = '';
				var qty_kango = 0;
				for(var i = 0; i < result.all_tray.length;i++){
					if (result.all_tray[i].location == 'KANGO') {
						trayKango += '<div class="col-xs-3" style="padding:5px">';
						trayKango += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:20px;"';
						trayKango += '>'+result.all_tray[i].material.split(' - ')[1]+' - '+result.all_tray[i].material.split(' - ')[2]+'<br>';
						trayKango += '</button>';
						trayKango += '</div>';
						qty_kango++;
					}
				}
				$('#divTrayKango').append(trayKango);
				if (qty_kango > 0) {
					$('#qty_kango').html(' - '+qty_kango+' Pc(s)');
				}

				trays = result.tray;
			}
			else{
				$('#btn_finish').prop('disabled',true);
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function selectTray(tray,status,outs) {
		$('#tray').val("");
		$('#resultScanBody').html('');
		var trayss = '';
		var index = 1;
		// if (status == 'Sudah') {
			for(var i = 0; i < all_tray.length;i++){
				if (tray == all_tray[i].location) {
					trayss += '<tr>';
					trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+index+'</td>';
					trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].location+'</td>';
					trayss += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[0]+'</td>';
					trayss += '<td style="text-align:left;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[1]+'</td>';
					trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;font-weight:bold;color:red;">'+all_tray[i].material.split(' - ')[2]+'</td>';
					trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].timestamps+'</td>';
					trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].plan_out+'</td>';
					trayss += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+all_tray[i].name+'</td>';
					if (outs == 'OUT') {
						trayss += '<td style="text-align:center;border:1px solid black;"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Inprogress<input type="checkbox" name="check" id="check" class="check" value="'+all_tray[i].id+'"><span class="checkmark_checkmark"></span></label></td>';
					}else{
						trayss += '<td style="text-align:center;border:1px solid black;"></td>';
					}
					trayss += '</tr>';
					index++;
				}
			}
			$('#resultScanBody').append(trayss);
			$('#tray').val(tray);
			$("#daisha_info").html('Daisha '+tray);
			$('#modalDetail').modal('show');
		// }else{
		// 	$('#loading').hide();
		// 	audio_error.play();
		// 	openErrorGritter('Error!','Daisha Belum Siap Diambil');
		// 	$('#tag').removeAttr('disabled');
		// 	$('#tag').val('');
		// 	$('#daisha_info').html('');
		// }
	}

	function selectTraySet(tray,status,outs,model) {
		$('#tray').val("");
		$('#resultScanBody').html('');
		var trayss = '';
		var index = 1;
		// if (status == 'Sudah') {
			for(var i = 0; i < all_tray.length;i++){
				if (tray == all_tray[i].location) {
					var re = new RegExp(model, 'g');
					if (all_tray[i].material.split(' - ')[1].match(re)) {
						trayss += '<tr>';
						trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+index+'</td>';
						trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].location+'</td>';
						trayss += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[0]+'</td>';
						trayss += '<td style="text-align:left;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[1]+'</td>';
						trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;font-weight:bold;color:red;">'+all_tray[i].material.split(' - ')[2]+'</td>';
						trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].timestamps+'</td>';
						trayss += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].plan_out+'</td>';
						trayss += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+all_tray[i].name+'</td>';
						if (outs == 'OUT') {
							trayss += '<td style="text-align:center;border:1px solid black;"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Inprogress<input type="checkbox" name="check" id="check" class="check" value="'+all_tray[i].id+'"><span class="checkmark_checkmark"></span></label></td>';
						}else{
							trayss += '<td style="text-align:center;border:1px solid black;"></td>';
						}
						trayss += '</tr>';
						index++;
					}
				}
			}
			$('#resultScanBody').append(trayss);
			$('#tray').val(tray);
			$("#daisha_info").html('Daisha '+tray);
			$('#modalDetail').modal('show');
		// }else{
		// 	$('#loading').hide();
		// 	audio_error.play();
		// 	openErrorGritter('Error!','Daisha Belum Siap Diambil');
		// 	$('#tag').removeAttr('disabled');
		// 	$('#tag').val('');
		// 	$('#daisha_info').html('');
		// }
	}

	function cancelSeasoning() {
		$('#modalDetail').modal('hide');
		$('#btn_finish').prop('disabled',true);
		$('#tag').removeAttr('disabled');
		$('#tag').val('');
		$('#tag').focus();
		$('#daisha_info').html('');
		$("#tray").val('');
		$('#resultScanBody').html('');
	}

	function finishSeasoning() {
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
			var data = {
				tray:$('#tray').val(),
				tag:$('#tag').val(),
				inprogress:check
			}

			$.post('{{ url("input/seasoning") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#btn_finish').prop('disabled',true);
					openSuccessGritter('Success','Success Selesai Seasoning');
					fillResult();
					$('#resultScanBody').html('');
					$('#tray').val('');
					$('#tag').removeAttr('disabled');
					$('#tag').val('');
					$('#tag').focus();
					$('#daisha_info').html('');
					$('#modalDetail').modal('hide');
				}else{
					$('#btn_finish').removeAttr('disabled');
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