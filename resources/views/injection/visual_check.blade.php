@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
  left: 0;
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
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-3" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:1px solid black">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%" colspan="2">PIC</td>
				</tr>
				<tr>
					<td  style="background-color: #ffadad;border:1px solid black;font-size: 15px" id="operator_id">-</td>
					<td  style="background-color: #ffadad;border:1px solid black;font-size: 15px" id="operator_name">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Mesin</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #caffbf;border:1px solid black;font-size: 20px" id="machine">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Jam</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #ffd6a5;border:1px solid black;font-size: 20px" id="hour_check">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Material</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #bde0fe;border:1px solid black;font-size: 20px" id="material">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Part</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #a2d2ff;border:1px solid black;font-size: 20px" id="part">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Cavity</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #bde0fe;border:1px solid black;font-size: 20px" id="cavity">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Color</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #a2d2ff;border:1px solid black;font-size: 20px" id="color">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Molding</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #bde0fe;border:1px solid black;font-size: 20px" id="molding">-</td>
					<input type="hidden" name="tag_molding" id="tag_molding">
					<input type="hidden" name="dryer" id="dryer">
					<input type="hidden" name="lot_number" id="lot_number">
				</tr>
			</table>
			<table style="width: 100%;margin-top: 10px;border: 1px solid black">
				<tr>
					<th colspan="2" style="background-color: #3c8dbc;color: white;font-size: 20px;text-align: center;">Keterangan</th>
				</tr>
				<tr style="background-color:#a2ff8f;font-size: 16px;">
					<th style="width: 1%;text-align: center;">
						&#9711;
					</th>
					<th style="width: 4%;text-align: center;">
						Kondisi baik sampai pengecekan berikutnya
					</th>
				</tr>
				<tr style="background-color:#fff68f;font-size: 16px;">
					<th style="text-align: center;">
						&#8420;
					</th>
					<th style="text-align: center;">
						Kondisi perlu pengawasan intensif
					</th>
				</tr>
				<tr style="background-color:#ff8f8f;font-size: 16px;">
					<th style="text-align: center;">
						&#9747;
					</th>
					<th style="text-align: center;">
						Kondisi tidak baik saat pengecekan 
					</th>
				</tr>
			</table>
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
				<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
					CANCEL
				</button>
			</div>
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 0px;padding-left: 5px">
				<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
					SAVE
				</button>
			</div>
		</div>
		<div class="col-xs-9" style="text-align: center;padding-left: 5px">
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck">
				
			</table>
		</div>
		<div class="col-xs-12">
			<div style="background-color:rgb(126,86,134);color:#FFD700;font-weight: bold;font-size: 20px;text-align: center;">
				POINT CHECK
			</div>
			<div id="images">
				
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
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalMesin">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" id="mesin_choice" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin</span></center>
								</div>
								<div class="col-xs-12" id="mesin_btn">
									<div class="row">
										@foreach($mesin as $mesin)
										<div class="col-xs-3" style="padding-top: 5px">
											<center>
												<button class="btn btn-primary" id="{{$mesin}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMesin(this.id)">{{$mesin}}</button>
											</center>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="mesin_fix" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<button class="btn btn-primary" id="mesin_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesin()">
										MESIN
									</button>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="hour_choice" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Jam</span></center>
								</div>
								<div class="col-xs-12" id="hour_btn">
									<div class="row">
										@foreach($hour as $hour)
										<div class="col-xs-3" style="padding-top: 5px">
											<center>
												<button class="btn btn-warning" id="{{$hour}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getHour(this.id)">{{$hour}}</button>
											</center>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="hour_fix" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Jam</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<button class="btn btn-warning" id="hour_fix2" style="width: 100%;font-size: 20px;font-weight: bold;">
										JAM
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button onclick="saveMesin()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCar">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center style="background-color: orange"> <b style="font-size: 2vw">CREATE CAR</b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Cavity</label>
									<input type="text" id="cavity_detail_car" class="form-control" style="width: 100%" readonly>
									<input type="hidden" id="id_car">
								</div>
								<div class="form-group">
									<label>Deskripsi</label><br>
									<textarea id="car_desctiprion" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action</label><br>
									<textarea id="car_action_now" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause</label><br>
									<textarea id="car_cause" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action</label><br>
									<textarea id="car_action" style="width: 100%"></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button onclick="saveCar()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
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

	$.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

	jQuery(document).ready(function() {
		getHour('{{$hour_check}}');
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

      $('body').toggleClass("sidebar-collapse");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_name").html("-");

		CKEDITOR.replace('car_desctiprion' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
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
						$('#operator_id').html(result.employee.employee_id);
						$('#operator_name').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#mesin_fix').hide();
						// $('#hour_fix').hide();
						$('#mesin_choice').show();
						// $('#hour_choice').show();
						$('#modalMesin').modal('show');
						getHour('{{$hour_check}}');
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

	function getMesin(value) {
		$('#mesin_fix').show();
		$('#mesin_choice').hide();
		$('#mesin_fix2').html(value);
	}

	function changeMesin() {
		$('#mesin_fix').hide();
		$('#mesin_choice').show();
		$('#mesin_fix2').html("MESIN");
	}

	function getHour(value) {
		$('#hour_fix').show();
		$('#hour_choice').hide();
		$('#hour_fix2').html(value);
	}

	function changeHour() {
		$('#hour_fix').hide();
		$('#hour_choice').show();
		$('#hour_fix2').html("JAM");
	}

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			$('#modalMesin').modal('show');
			$('#machine').html('-');
			$('#hour_check').html('-');
			$('#material').html('-');
			$('#part').html('-');
			$('#cavity').html('-');
			$('#color').html('-');
			$('#molding').html('-');
			$('#tableCheck').html('');
			$('#images').html('');
			changeHour();
			changeMesin();
			getHour('{{$hour_check}}');
		}
	}

	var cav_detail = [];
	var point_check = [];
	var cavity_total = 0;
	var point_check_total = 0;

	function saveMesin() {
		$('#loading').show();
		if ($('#hour_fix2').text() == 'JAM' || $('#mesin_fix2').text() == 'MESIN') {
			audio_error.play();
			openErrorGritter('Error!','Pilih Mesin dan Jam Cek');
		}else{
			var machine = $('#mesin_fix2').text();
			var hour = $('#hour_fix2').text();

			var data = {
				mesin:machine
			}

			cav_detail = [];

			$.get('{{ url("fetch/injection/machine_work") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.machine.tag_molding == null) {
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', 'Mesin Terdeteksi Tidak Berjalan.');
					}else{
						$('#machine').html(machine);
						$('#hour_check').html(hour);
						$('#loading').hide();
						$('#modalMesin').modal('hide');
						$('#material').html(result.machine.material_number);
						$('#part').html(result.machine.part_name+' '+result.machine.part_type);
						$('#cavity').html(result.machine.cavity);
						$('#color').html(result.machine.color);
						$('#molding').html(result.machine.molding);
						$('#tag_molding').val(result.machine.tag_molding);
						$('#dryer').val(result.machine.dryer);
						$('#lot_number').val(result.machine.dryer_lot_number);

						$('#tableCheck').html('');
						var tableCheck = '';

						tableCheck += '<tr>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:2%;">Point Check / Cavity</td>';

						cavity_total = 0;
						point_check_total = 0;

						if (result.machine.part_name.match(/YRS/gi)) {
							if (result.machine.part_type == 'HJ') {
								var url = '{{url("data_file/injection/point_check_visual/YRS_HJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td id="cavity_1" style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td id="cavity_2" style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								tableCheck += '<td id="cavity_3" style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_3+'</td>';
								tableCheck += '<td id="cavity_4" style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_4+'</td>';
								cavity_total = 4;

								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
								cav_detail.push(result.cavity[0].cavity_3);
								cav_detail.push(result.cavity[0].cavity_4);
							}else if(result.machine.part_type == 'FJ'){
								var url = '{{url("data_file/injection/point_check_visual/YRS_FJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_3+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_4+'</td>';
								cavity_total = 4;
								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
								cav_detail.push(result.cavity[0].cavity_3);
								cav_detail.push(result.cavity[0].cavity_4);
								if (result.cavity[0].cavity_5 != null) {
									tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_5+'</td>';
									tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_6+'</td>';
									cavity_total = 6;
									cav_detail.push(result.cavity[0].cavity_5);
									cav_detail.push(result.cavity[0].cavity_6);
								}
							}else if(result.machine.part_type == 'BJ'){
								var url = '{{url("data_file/injection/point_check_visual/YRS_BJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_3+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_4+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_5+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_6+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_7+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_8+'</td>';
								cavity_total = 8;

								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
								cav_detail.push(result.cavity[0].cavity_3);
								cav_detail.push(result.cavity[0].cavity_4);
								cav_detail.push(result.cavity[0].cavity_5);
								cav_detail.push(result.cavity[0].cavity_6);
								cav_detail.push(result.cavity[0].cavity_7);
								cav_detail.push(result.cavity[0].cavity_8);
							}else if(result.machine.part_type.match(/MJ/gi)){
								var url = '{{url("data_file/injection/point_check_visual/YRS_MJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_3+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_4+'</td>';
								cavity_total = 4;
								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
								cav_detail.push(result.cavity[0].cavity_3);
								cav_detail.push(result.cavity[0].cavity_4);
							}
						}
						if (result.machine.part_name.match(/YRF/gi)) {
							if (result.machine.part_type == 'A YRF H') {
								var url = '{{url("data_file/injection/point_check_visual/YRS_HJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								cavity_total = 2;
								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
							}else if(result.machine.part_type == 'A YRF B'){
								var url = '{{url("data_file/injection/point_check_visual/YRS_MJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								cavity_total = 2;
								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
							}else if(result.machine.part_type == 'A YRF S'){
								var url = '{{url("data_file/injection/point_check_visual/YRS_BJ.png")}}';
								$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_1+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_2+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_3+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_4+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_5+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_6+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_7+'</td>';
								tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">'+result.cavity[0].cavity_8+'</td>';
								cavity_total = 8;
								cav_detail.push(result.cavity[0].cavity_1);
								cav_detail.push(result.cavity[0].cavity_2);
								cav_detail.push(result.cavity[0].cavity_3);
								cav_detail.push(result.cavity[0].cavity_4);
								cav_detail.push(result.cavity[0].cavity_5);
								cav_detail.push(result.cavity[0].cavity_6);
								cav_detail.push(result.cavity[0].cavity_7);
								cav_detail.push(result.cavity[0].cavity_8);
							}
						}
						tableCheck += '</tr>';
						for(var i = 0; i < result.point_check.length;i++){
							tableCheck += '<tr>';
							tableCheck += '<td id="point_check_'+i+'" style="background-color:orange;font-size:15px;border:1px solid black;padding:5px;">'+result.point_check[i].point_check_index+'. '+result.point_check[i].point_check_name+'</td>';
							point_check_total++;
							for (var j = 0; j < cavity_total; j++) {
								tableCheck += '<td style="background-color:white;font-size:20px;border:1px solid black;width:1%;" onclick="checkCondition(\''+j+'\',\''+i+'\')">';
								tableCheck += '<label class="containers">&#9711;';
								  tableCheck += '<input type="radio" name="condition_'+j+'_'+i+'" id="condition_'+j+'_'+i+'" value="OK">';
								  tableCheck += '<span class="checkmark"></span>';
								tableCheck += '</label>';
								tableCheck += '<label class="containers">&#8420;';
								  tableCheck += '<input type="radio" name="condition_'+j+'_'+i+'" id="condition_'+j+'_'+i+'" value="NS">';
								  tableCheck += '<span class="checkmark"></span>';
								tableCheck += '</label>';
								tableCheck += '<label class="containers">&#9747;';
								  tableCheck += '<input type="radio" name="condition_'+j+'_'+i+'" id="condition_'+j+'_'+i+'" value="NG">';
								  tableCheck += '<span class="checkmark"></span>';
								tableCheck += '</label>';
								tableCheck += '</td>';
							}
							tableCheck += '</tr>';
						}
					}

					tableCheck += '<tr>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">NOTE</td>';
					for (var j = 0; j < cavity_total; j++) {
						tableCheck += '<td style="background-color:white;font-size:20px;border:1px solid black;width:1%;">';
						tableCheck += '<input type="text" id="note_'+j+'" style="width:100%;font-size:20px;text-align:center" placeholder="Note">';
						tableCheck += '</td>';
						
					}
					tableCheck += '</tr>';

					tableCheck += '<tr>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">CAR</td>';
					for (var j = 0; j < cavity_total; j++) {
						tableCheck += '<td style="background-color:white;font-size:20px;border:1px solid black;width:1%;">';
						tableCheck += '<button style="display:none" class="btn btn-primary" id="btn_car_'+j+'" onclick="createCAR(\''+cav_detail[j]+'\',\''+j+'\')">Create CAR</button>';
						tableCheck += '<input type="hidden" id="car_desctiprion_'+j+'">';
						tableCheck += '<input type="hidden" id="car_action_now_'+j+'">';
						tableCheck += '<input type="hidden" id="car_cause_'+j+'">';
						tableCheck += '<input type="hidden" id="car_action_'+j+'">';
						tableCheck += '<input type="hidden" id="car_images_'+j+'">';
						tableCheck += '</td>';
					}
					tableCheck += '</tr>';

					$('#tableCheck').append(tableCheck);

					$('input[type="radio"]').prop('checked', false);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#operator').val('');
				}
			});
		}
	}

	function saveCar() {
		var id = $('#id_car').val();
		var description = CKEDITOR.instances.car_desctiprion.getData();
		var action_now = CKEDITOR.instances.car_action_now.getData();
		var cause = CKEDITOR.instances.car_cause.getData();
		var action = CKEDITOR.instances.car_action.getData();

		$('#car_desctiprion_'+id).val(description);
		$('#car_action_now_'+id).val(action_now);
		$('#car_cause_'+id).val(cause);
		$('#car_action_'+id).val(action);
		$('#btn_car_'+id).hide();
		$('#modalCar').modal('hide');
	}

	function createCAR(cavity,index) {
		$('#cavity_detail_car').val(cavity);
		$('#id_car').val(index);
		$('#modalCar').modal('show');
		CKEDITOR.instances.car_desctiprion.setData('');
		CKEDITOR.instances.car_action_now.setData('');
		CKEDITOR.instances.car_cause.setData('');
		CKEDITOR.instances.car_action.setData('');
	}

	function checkCondition() {
		var result_check = [];
		for(var i = 0;i < point_check_total;i++){
			for(var j = 0; j < cavity_total;j++){
				$("input[name='condition_"+j+"_"+i+"']:checked").each(function (i) {
		            result_check.push({cav:cav_detail[j],result:$(this).val()});
		        });
			}
		}
		for(var i = 0; i < cav_detail.length;i++){
			var salah = 0;
			for(var j = 0; j < result_check.length;j++){
				if (result_check[j].cav == cav_detail[i]) {
					if (result_check[j].result == 'NG') {
						salah++;
					}
				}
			}
			if (salah > 0) {
				$('#btn_car_'+i).removeAttr('style');
			}else{
				$('#btn_car_'+i).prop('style','display:none');
			}
		}
	}

	function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan proses?')) {
			$('#loading').show();
			var machine = $('#machine').text();
			var hour_check = $('#hour_check').text();
			var material_number = $('#material').text();
			var part_name = $('#part').text().split(' ')[0];
			var part_type = $('#part').text().split(' ')[1];
			var cavity = $('#cavity').text();
			var color = $('#color').text();
			var molding = $('#molding').text();
			var tag_molding = $('#tag_molding').val();
			var dryer = $('#dryer').val();
			var lot_number = $('#lot_number').val();
			var pic_check = $('#operator_id').text();
			var point_check = [];
			var result_check = [];
			var note = [];
			for(var i = 0;i < point_check_total;i++){
				point_check.push($('#point_check_'+i).text());
				for(var j = 0; j < cavity_total;j++){
					$("input[name='condition_"+j+"_"+i+"']:checked").each(function (i) {
			            result_check.push($(this).val());
			        });
				}
			}

			var description = [];
			var action_now = [];
			var cause = [];
			var action = [];

			for(var j = 0; j < cavity_total;j++){
				note.push($('#note_'+j).val());
				description.push($('#car_desctiprion_'+j).val());
				action_now.push($('#car_action_now_'+j).val());
				cause.push($('#car_cause_'+j).val());
				action.push($('#car_action_'+j).val());
			}

			var total_check = point_check_total * cavity_total;

			if (result_check.length < total_check) {
				$("#loading").hide();
				openErrorGritter('Error!','Semua Data Harus Diisi');
				return false;
			}

			var data = {
				machine:machine,
				hour_check:hour_check,
				material_number:material_number,
				part_name:part_name,
				part_type:part_type,
				cavity:cavity,
				color:color,
				molding:molding,
				tag_molding:tag_molding,
				dryer:dryer,
				lot_number:lot_number,
				cav_detail:cav_detail,
				cavity_total:cavity_total,
				point_check_total:point_check_total,
				point_check:point_check,
				result_check:result_check,
				pic_check:pic_check,
				note:note,
				description:description,
				action_now:action_now,
				cause:cause,
				action:action,
			}

			// console.log(data);

			$.post('{{ url("input/injection/visual") }}', data, function(result, status, xhr){
				if(result.status){
					location.reload();
					openSuccessGritter('Success',result.message);
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			});
		}
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