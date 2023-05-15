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
	<input type="hidden" id="check_time">
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
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Tools</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #caffbf;border:1px solid black;font-size: 20px" id="tools">-</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Point</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #bfdbff;border:1px solid black;font-size: 20px" id="point">-</td>
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
			<div class="col-xs-12" style="margin-top: 10px;padding-right: 0px;padding-left: 0px;background-color: grey">
				<div class="timercleaning" style="color:#000;font-size: 35px;background-color: #85ffa7">
		            <span class="hourcleaning" id="hourcleaning">00</span>:<span class="minutecleaning" id="minutecleaning">00</span>:<span class="secondcleaning" id="secondcleaning">00</span>
		        </div>
			</div>
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

	<div class="modal fade" id="modalOperator" data-backdrop="static" data-keyboard="false">
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

	<div class="modal fade" id="modalTools" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" id="tools_choice" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Tools</span></center>
								</div>
								<div class="col-xs-12" id="tools_btn">
									<div class="row">
										<div class="col-xs-3" style="padding-top: 5px">
											<center>
												<button class="btn btn-primary" id="Hopper" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getTools(this.id)">Hopper</button>
											</center>
										</div>
										<?php if ($dayname == 'Mon' || $dayname == 'Wed' || $dayname == 'Fri'): ?>
											<div class="col-xs-3" style="padding-top: 5px">
												<center>
													<button class="btn btn-primary" id="Dryer" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getTools(this.id)">Dryer</button>
												</center>
											</div>
										<?php endif ?>
										<div class="col-xs-3" style="padding-top: 5px">
											<center>
												<button class="btn btn-primary" id="Crusher" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getTools(this.id)">Crusher</button>
											</center>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="tools_fix" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Tools</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<button class="btn btn-primary" id="tools_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeTools()">
										TOOLS
									</button>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="point_select" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Point</span></center>
								</div>
								<div class="col-xs-12">
									<select class="form-control select2" id="select_point" data-placeholder="Pilih Point" style="width: 100%;text-align: center;">
										<option value=""></option>
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-top: 20px" id="ada_select">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Ada Pengecekan / Tidak</span></center>
								</div>
								<div class="col-xs-12">
									<select class="form-control" data-placeholder="Pilih Ada / Tidak" style="width: 100%;text-align: center;" id="select_ada">
										<option value="Ada Pengecekan">Ada Pengecekan</option>
										<option value="Tidak Ada Pengecekan">Tidak Ada Pengecekan</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<!-- <div class="modal-footer"> -->
								<div class="row" style="padding-left: 15px;">
									<button onclick="saveTools()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
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

    var count_point = 0;
    var hour;
    var minute;
    var second;
    var intervalTime;

	jQuery(document).ready(function() {
		clearTimeout(intervalTime);
		$('#secondcleaning').html("00");
		$('#minutecleaning').html("00");
		$('#hourcleaning').html("00");
		$('.select2').select2({
			allowClear:true
		});
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

      $('body').toggleClass("sidebar-collapse");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_name").html("-");
		$('#check_time').val('');
		$('#images').html('');
		count_point = 0;

	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	function readURL(input,idfile) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#blah_'+idfile).show();
              $('#blah_'+idfile)
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

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
						$('#tools_fix').hide();
						// $('#hour_fix').hide();
						$('#tools_choice').show();
						$('#point_select').hide();
						$('#ada_select').hide();
						// $('#hour_choice').show();
						$('#images').html('');
						$('#modalTools').modal('show');
						$('#check_time').val('');
						count_point = 0;
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
				$('#check_time').val('');
			}			
		}
	});

	function getTools(value) {
		$("#loading").show();
		$('#tools_fix').show();
		$('#tools_choice').hide();
		$('#tools_fix2').html(value);
		var data ={
			point:value
		}
		$.get('{{ url("fetch/injection/cleaning/point") }}', data, function(result, status, xhr){
			if(result.status){
				$('#select_point').html('');
				var points = '';
				for(var i = 0; i < result.point.length; i++){
					points += '<option value="'+result.point[i].point_id+'">'+result.point[i].point+'</option>';
				}
				$('#point_select').show();
				$('#ada_select').show();
				$('#select_ada').val('Ada Pengecekan').trigger('change');
				$('#select_point').append(points);
				$("#loading").hide();
			}
			else{
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#point_select').hide();
				$('#ada_select').hide();
				$('#select_point').html('');
			}
		});
	}

	function changeTools() {
		$('#point_select').hide();
		$('#ada_select').hide();
		$('#select_point').html('');
		$('#tools_fix').hide();
		$('#tools_choice').show();
		$('#tools_fix2').html("TOOLS");
	}

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian dan menghentikan waktu pengisian?')) {
			clearTimeout(intervalTime);
			$('#modalTools').modal('show');
			$('#tools_choice').show();
			$('#tools_fix').hide();
			$('#point_select').hide();
			$('#ada_select').hide();
			$('#tools').html('-');
			$('#point').html('-');
			$('#tableCheck').html('');
			$('#check_time').val('');
			$('#images').html('');
			count_point = 0;
			$('#secondcleaning').html("00");
			$('#minutecleaning').html("00");
			$('#hourcleaning').html("00");

			var point = $('#select_point').val();

			var data = {
				point_check_type:point.split('_')[0],
				point_check_machine:point.split('_')[1],
			}

			$.get('{{ url("delete/injection/cleaning_timeline") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success','Cancelation Success');
					location.reload();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function saveTools() {
		$('#loading').show();
		if ($('#select_point').val() == '' || $('#tools_fix2').text() == 'TOOLS') {
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!','Pilih Tools dan Point');
		}else{
			if ($('#select_ada').val() == 'Tidak Ada Pengecekan') {
				var tools = $('#tools_fix2').text();
				var point = $('#select_point').val();

				var data = {
					tools:point.split('_')[0],
					point:point.split('_')[1],
					ada:$('#select_ada').val(),
					pic_check:$('#operator_id').text()
				}

				$.get('{{ url("fetch/injection/cleaning/point_detail") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success','Report Tidak Ada Pengecekan Berhasil Dibuat.');
						location.reload();
					}else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
					}
				});
			}else{
				var tools = $('#tools_fix2').text();
				var point = $('#select_point').val();

				var data = {
					tools:point.split('_')[0],
					point:point.split('_')[1],
					ada:$('#select_ada').val(),
					pic_check:$('#operator_id').text()
				}

				$.get('{{ url("fetch/injection/cleaning/point_detail") }}', data, function(result, status, xhr){
					if(result.status){
						$('#tools').html(point.split('_')[0]);
						$('#point').html(point.split('_')[1]);
						$('#loading').hide();
						$('#modalTools').modal('hide');

						$('#tableCheck').html('');
						var tableCheck = '';

						tableCheck += '<tr>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:1%;">#</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:2%;">Point Check</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:2%;">Standard</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:2%;">Condition</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:2%;">Input Images</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:3%;">Images</td>';
						tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:1px solid black;width:3%;">Note</td>';
						tableCheck += '</tr>';

						count_point = result.point.length;

						for(var i = 0; i < result.point.length;i++){
							tableCheck += '<tr>';
							tableCheck += '<td id="point_check_index_'+i+'" style="background-color:orange;font-size:15px;border:1px solid black;padding:5px;">'+result.point[i].point_check_index+'</td>';
							tableCheck += '<td id="point_check_name_'+i+'" style="background-color:orange;font-size:15px;border:1px solid black;padding:5px;">'+result.point[i].point_check_name+'</td>';
							tableCheck += '<td id="point_check_standard_'+i+'" style="background-color:orange;font-size:15px;border:1px solid black;padding:5px;">'+result.point[i].point_check_standard+'</td>';
							tableCheck += '<td style="background-color:white;font-size:20px;border:1px solid black;width:1%;">';
							tableCheck += '<label class="containers">&#9711;';
							  tableCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							  tableCheck += '<span class="checkmark"></span>';
							tableCheck += '</label>';
							tableCheck += '<label class="containers">&#8420;';
							  tableCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NS">';
							  tableCheck += '<span class="checkmark"></span>';
							tableCheck += '</label>';
							tableCheck += '<label class="containers">&#9747;';
							  tableCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							  tableCheck += '<span class="checkmark"></span>';
							tableCheck += '</label>';
							tableCheck += '</td>';
							tableCheck += '<td style="background-color:white;font-size:15px;border:1px solid black;width:1%;">';
							tableCheck += '<input accept="image/*" capture="environment" type="file" id="file_'+i+'" onchange="readURL(this,\''+i+'\');">';
							tableCheck += '</td>';
							tableCheck += '<td style="background-color:white;font-size:20px;border:1px solid black;width:1%;">';
							tableCheck += '<img width="100px" id="blah_'+i+'" src="" style="display: none" alt="your image" />';
							tableCheck += '</td>';
							tableCheck += '<td style="background-color:white;font-size:15px;border:1px solid black;width:1%;">';
							tableCheck += '<textarea id="note_'+i+'" style="width:100%"></textarea>';
							tableCheck += '</td>';
							tableCheck += '</tr>';
							tableCheck += '<input type="hidden" id="id_point_'+i+'" value="'+result.point[i].id+'">';

							var url = '{{url("data_file/injection/point_check_cleaning/")}}'+'/'+result.point[i].point_check_drawing;
							$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
						}

						$('#tableCheck').append(tableCheck);

						$('#check_time').val(result.check_time);

						var tanggal_fix = result.started_at.replace(/-/g,'/');
						started_at = new Date(tanggal_fix);
						countUpFromTime(started_at);
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
					}
				});
			}
		}
	}

	function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan proses?')) {
			$('#loading').show();
			for(var i = 0; i < count_point;i++){
				var result_check = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_check = $(this).val();
		        });
				if (result_check == '') {
					$('#loading').hide();
					openErrorGritter('Error!','Isi Semua Data');
					return false;
				}
			}

			var cleaning_id = '';

			var datas = {
				point_check_type:$('#tools').text(),
				point_check_machine:$('#point').text(),
			}

			$.post('{{ url("update/injection/cleaning_timeline") }}', datas, function(result, status, xhr){
				if(result.status){
					// $('#loading').hide();
					openSuccessGritter('Success','Update Timeline Success');
					savePoint(result.cleaning_id);
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function savePoint(cleaning_id) {
		var stat = 0;
		for(var i = 0; i < count_point;i++){

			var check_time = $('#check_time').val();
			var point_check_type =  $('#tools').text();
			var point_check_machine =  $('#point').text();
			var pic_check =  $('#operator_id').text();
			var point_check_name =  $('#point_check_name_'+i).text();
			var point_check_standard =  $('#point_check_standard_'+i).text();
			var id_point = $('#id_point_'+i).val();
			var note = $('#note_'+i).val();
			var result_check = null;
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            result_check = $(this).val();
	        });
			var point_check_index = $('#point_check_index_'+i).text();
			var fileData  = $('#file_'+i).prop('files')[0];

			var file=$('#file_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");

			var formData = new FormData();
			formData.append('fileData', fileData);
			formData.append('cleaning_id', cleaning_id);
			formData.append('check_time', check_time);
			formData.append('point_check_type', point_check_type);
			formData.append('point_check_machine', point_check_machine);
			formData.append('pic_check', pic_check);
			formData.append('point_check_name', point_check_name);
			formData.append('point_check_standard', point_check_standard);
			formData.append('result_check', result_check);
			formData.append('id_point', id_point);
			formData.append('note', note);
			formData.append('point_check_index', point_check_index);
			formData.append('extension', file[1]);
			formData.append('foto_name', file[0]);
			

			$.ajax({
				url:"{{ url('input/injection/cleaning') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status == false) {
						openErrorGritter('Error!',data.message);
					}else if(data.status == true){
						stat++;
					}
					if (stat == count_point) {
						$('#loading').hide();
						openSuccessGritter('Success!','Save Data Success');
						location.reload();
					}
				},
				error: function(data) {
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
				}
			})
		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function countUpFromTime(countFrom) {
	  countFrom = new Date(countFrom).getTime();
	  var now = new Date(),
	      countFrom = new Date(countFrom),
	      timeDifference = (now - countFrom);
	    
	  var secondsInADay = 60 * 60 * 1000 * 24,
	      secondsInAHour = 60 * 60 * 1000;
	    
	  days = Math.floor(timeDifference / (secondsInADay) * 1);
	  years = Math.floor(days / 365);
	  if (years > 1){
	  	days = days - (years * 365) 
	  }
	  hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
	  mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
	  secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

	  $('div.timercleaning span.secondcleaning').html(addZero(secs));
	  $('div.timercleaning span.minutecleaning').html(addZero(mins));
	  $('div.timercleaning span.hourcleaning').html(addZero(hours));

	  clearTimeout(intervalTime);
	  intervalTime = setTimeout(function(){ countUpFromTime(countFrom); }, 1000);
	}
	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
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