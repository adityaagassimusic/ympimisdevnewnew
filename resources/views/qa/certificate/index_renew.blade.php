@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 13px;
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
  background-color: #999999;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

#tableCheck > tr > td > input:hover{
	background-color: #7dfa8c !important;
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

#tableCheck > tr > th, #tableCheck > tr > td,{
	padding: 2px;
}

input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" id="check_time">
	<div class="row">
		<input type="hidden" name="staff_id" id="staff_id">
		<input type="hidden" name="staff_name" id="staff_name">
		<input type="hidden" name="staff_email" id="staff_email">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:1px solid black">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%" colspan="4">Asesor (Leader QA)</td>
				</tr>
				<tr>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 15px;color: white" id="auditor_id" colspan="2">{{$auditor_id}}</td>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 15px;color: white" id="auditor_name" colspan="2">{{$auditor_name}}</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Certificate ID</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Certificate Code</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Desc</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Cert Name</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="certificate_id">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="certificate_code">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="code_desc">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="certificate_name">-</td>
				</tr>
				<tr>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center;padding-left: 5px">
			<table style="width: 100%;border:1px solid black">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Emp ID</td>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Emp Name</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="employee_id">-</td>
					<td colspan="2" style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="employee_name">-</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Issued Date</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Expired Date</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Status</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="periode_from">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="periode_to">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="status">-</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px">
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableSubject">
				
			</table>
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px">
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck">
				
			</table>
		</div>
		<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px">
			<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
				CANCEL
			</button>
		</div>
		<div class="col-xs-6" style="margin-top: 10px;padding-left: 5px">
			<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
				SAVE
			</button>
		</div>
	</div>


	<div class="modal fade" id="modalCode">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						RENEW CERTIFICATE IDENTIFICATION
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="row" id="divCode">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Kode Sertifikasi</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<select class="form-control select2" id="code_number_select" style="width: 100%" data-placeholder="Pilih Kode Sertifikat" onchange="changeCode(this.value)">
									<option></option>
									@foreach($code_number as $cn)
									<option value="{{$cn->code}}-{{$cn->code_number}}-{{$cn->description}}">{{$cn->code}} - {{$cn->code_number}} - {{$cn->description}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row" id="divCertificateId" style="padding-top: 10px">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Karyawan</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<select class="form-control select2" id="certificate_code_select" style="width: 100%" data-placeholder="Pilih Sertifikat">
									<option></option>
									@foreach($certificate as $cr)
									<option value="{{$cr->certificate_id}}_{{$cr->certificate_code}}_{{$cr->employee_id}}_{{$cr->name}}_{{$cr->periode_from}}_{{$cr->periode_to}}_{{$cr->status}}">{{$cr->certificate_code}} - {{$cr->certificate_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<div class="row">
								<button onclick="saveCode()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
									CONFIRM
								</button>
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
    var data_subject = [];

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});

		$('#modalCode').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

      $('body').toggleClass("sidebar-collapse");
		
		$('#check_time').val('');
		$('#images').html('');
		count_point = 0;
		data_subject = [];
		cancelAll();

	});

	$('#modalCode').on('shown.bs.modal', function () {
		// $('#operator').focus();
	});

	var periode = JSON.parse( '<?php echo json_encode($periode) ?>' );

	function cancelAll() {
		$('#modalCode').modal('show');
		$('#certificate_code_select').val('').trigger('change');
		$('#code_number_select').val('').trigger('change');
		$('#divCertificateId').hide();
		count_point = 0;
		data_subject = [];
	}

	function changeCode(value) {
		
		var certificate_code = $.parseJSON('<?php echo $certificate; ?>');
		$("#certificate_code_select").html('');
		var certificate = '';
		certificate += '<option value=""></option>';
		for(var i = 0; i < certificate_code.length;i++){
			if (value === '') {
				certificate += '<option value=""></option>';
			}else{
				if (certificate_code[i].certificate_code.includes(value.split('-')[0]+'-'+value.split('-')[1])) {
					certificate += '<option value="'+certificate_code[i].certificate_id+'_'+certificate_code[i].certificate_code+'_'+certificate_code[i].employee_id+'_'+certificate_code[i].name+'_'+certificate_code[i].periode_from+'_'+certificate_code[i].periode_to+'_'+certificate_code[i].status+'_'+certificate_code[i].certificate_name+'">'+certificate_code[i].certificate_code+' - '+certificate_code[i].certificate_name+' - '+certificate_code[i].name.split(' ').slice(0,2).join(' ')+'</option>';
				}
			}
		}
		$("#certificate_code_select").append(certificate);
		$('#divCertificateId').show();
	}

	function saveCode() {
		$("#loading").show();
		if ($('#code_number_select').val() == '' || $('#certificate_code_select').val() == '') {
			audio_error.play();
			$("#loading").hide();
			openErrorGritter('Error!','Pilih Kode Sertifikasi');
			return false;
		}

		$('#code').html($('#code_number_select').val().split('-')[0]);
		$('#code_number').html($('#code_number_select').val().split('-')[1]);
		$('#code_desc').html($('#code_number_select').val().split('-')[2]);

		$('#certificate_id').html($('#certificate_code_select').val().split('_')[0]);
		$('#certificate_code').html($('#certificate_code_select').val().split('_')[1]);
		$('#employee_id').html($('#certificate_code_select').val().split('_')[2]);
		$('#employee_name').html($('#certificate_code_select').val().split('_')[3]);

		var code = $('#certificate_code_select').val().split('_')[1].split('-')[2];
		var code_numbers = $('#certificate_code_select').val().split('_')[1].split('-')[3];
		var code_number = code_numbers.substring(0, code_numbers.length-3);

		var month_periode = '';
		for(var i = 0; i < periode.length;i++){
			if (periode[i].code == code && periode[i].code_number == code_number) {
				month_periode = periode[i].periode;
			}
		}
		var th_next = $('#certificate_code_select').val().split('_')[5].split('-')[0];
		var periode_from = th_next+'-'+month_periode+'-01';
		var periode_to = (parseInt(th_next)+1)+'-'+month_periode+'-01';
		// $('#periode_from').html($('#certificate_code_select').val().split('_')[4]);
		// $('#periode_to').html($('#certificate_code_select').val().split('_')[5]);
		$('#periode_from').html(periode_from);
		$('#periode_to').html(periode_to);
		if ($('#certificate_code_select').val().split('_')[6] == 2) {
			var statuss = 'Need Renewal';
		}else if($('#certificate_code_select').val().split('_')[6] == 3){
			var statuss = 'Expired';
		}else if($('#certificate_code_select').val().split('_')[6] == 1){
			var statuss = 'Active';
		}
		$('#status').html(statuss);
		$('#certificate_name').html($('#certificate_code_select').val().split('_')[7]);

		var data = {
			code:$('#code_number_select').val().split('-')[0],
			code_number:$('#code_number_select').val().split('-')[1]
		}


		$.get('{{ url("fetch/renew/qa/certificate") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableCheck').html('');
				var tableCheck = '';
				var index = 1;
				var index_subject = 0;

				tableCheck += '<tr id="header">';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">#</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:4%;padding:3px">Subject</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:5%;padding:3px">Jenis Tes</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Kategori</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Standard</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Jumlah Soal</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Bobot Nilai</th>';
				tableCheck += '<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Jumlah Soal Benar</th>';
				tableCheck += '</tr>';
				count_point = 0;
				count_point = result.point.length;
				for(var i = 0; i < result.point.length;i++){
					for(var j = 0; j < result.data_subject.length;j++){
						if (result.data_subject[j].subject == result.point[i].subject) {
							index_subject = j;
						}
					}
					tableCheck += '<tr id="'+result.point[i].subject+'">';
					tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right">'+index+'</td>';
					tableCheck += '<td id="subject_'+i+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:left">'+result.point[i].subject+'</td>';
					tableCheck += '<td id="test_type_'+i+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:left">'+(result.point[i].test_type || '')+'</td>';
					tableCheck += '<td id="category_'+i+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:left">'+result.point[i].category+'</td>';
					tableCheck += '<td id="standard_'+i+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:left"> >= / = '+result.point[i].standard+'%</td>';

					if (result.point[i].question == null) {
						if (result.point[i].category == 'Nilai Total' || result.point[i].category == 'Total') {
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_sub_total_question" name="'+index_subject+'_sub_total_question"></td>';
						}else{
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_question_'+i+'" name="'+index_subject+'_question_'+i+'" onchange="checkSubTotal(this.id)"></td>';
						}
					}else{
						tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input type="text" class="form-control" readonly style="width:100%;text-align:right" id="'+index_subject+'_question_'+i+'" name="'+index_subject+'_question_'+i+'" value="'+result.point[i].question+'"></td>';
					}

					if (result.point[i].weight == null) {
						if (result.point[i].category == 'Nilai Total' || result.point[i].category == 'Total') {
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_weight_'+i+'" name="'+index_subject+'_weight_'+i+'"></td>';
						}else{
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_weight_'+i+'" name="'+index_subject+'_weight_'+i+'"></td>';
						}
					}else{
						tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input type="text" class="form-control" readonly style="width:100%;text-align:right" id="'+index_subject+'_weight_'+i+'" name="'+index_subject+'_weight_'+i+'" value="'+result.point[i].weight+'"></td>';
					}

					if (result.point[i].question_result == null) {
						if (result.point[i].category == 'Nilai Total' || result.point[i].category == 'Total') {
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_sub_total_question_result" name="'+index_subject+'_sub_total_question_result"></td>';
						}else{
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input  type="number" class="form-control numpad" readonly style="width:100%;text-align:right" id="'+index_subject+'_question_result_'+i+'" name="'+index_subject+'_question_result_'+i+'" onchange="checkSubTotalResult(this.id)"></td>';
						}
					}else{
						tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:right"><input type="text" class="form-control" readonly style="width:100%;text-align:right" id="'+index_subject+'_question_result_'+i+'" name="'+index_subject+'_question_result_'+i+'" value="'+result.point[i].question_result+'"></td>';
					}
					tableCheck += '</tr>';
					index++;
					$('#staff_id').val(result.point[i].staff_id);
					$('#staff_name').val(result.point[i].staff_name);
					$('#staff_email').val(result.point[i].staff_email);
				}
				$("#tableCheck").append(tableCheck);

				$('#tableSubject').html('');
				var tableSubject = '';
				tableSubject += '<tr>';
				tableSubject += '<th colspan="'+result.data_subject.length+'" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align:center">Subject</th>';
				tableSubject += '</tr>';
				tableSubject += '<tr>';
				for(var j = 0; j < result.data_subject.length;j++){
					tableSubject += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:3px;text-align:center">';
					tableSubject += '<center>';
					tableSubject += '<label class="container_checkmark" style="color: green">'+result.data_subject[j].subject;
					tableSubject += '<input type="checkbox" name="subject_select" id="subject_select_'+j+'" class="complaint" value="'+result.data_subject[j].subject+'" onclick="checkSubject()">';
					tableSubject += '<span class="checkmark_checkmark"></span>';
					tableSubject += '</label>';
					tableSubject += '</center>';
					tableSubject += '</td>';
				}
				tableSubject += '</tr>';
				$("#tableSubject").append(tableSubject);
				data_subject = result.data_subject;

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
				$("#loading").hide();
				$('#modalCode').modal('hide');

				$('#tableCheck').hide();
			}else{
				$("#loading").hide();
				alert('Point Check Tidak Tersedia');
				location.reload();
			}
		});
	}

	function checkSubject() {
		var subject_select = [];
		$("input[name='subject_select']:checked").each(function (i) {
	            subject_select[i] = $(this).val();
        });
        if (subject_select.length == 0) {
		  	table = document.getElementById("tableCheck");
			  tr = table.getElementsByTagName("tr");
			  for (i = 0; i < tr.length; i++) {
			      $('#tableCheck').hide();
			        tr[i].style.display = "none";
			  }
		}else{
			table = document.getElementById("tableCheck");
			  tr = table.getElementsByTagName("tr");
			  for (i = 0; i < tr.length; i++) {
		        	tr[i].style.display = "none";
			  }
        	for (var j =0; j < subject_select.length; j++) {
			  	table = document.getElementById("tableCheck");
				  tr = table.getElementsByTagName("tr");
				  for (i = 0; i < tr.length; i++) {
				  	if (tr[i].id == 'header') {
				  		tr[i].style.display = "";
				  	}
			      	if (tr[i].id == subject_select[j]) {
			      		$('#tableCheck').show();
			        	tr[i].style.display = "";
			      	}
				  }
			}
        }
	}

	function checkSubTotal(id) {
		var subject_select = [];
		$("input[name='subject_select']:checked").each(function (i) {
	            subject_select[i] = $(this).val();
        });
        var total = 0;
		for(var i = 0; i < count_point;i++){
			if (data_subject[id.split('_')[0]].subject == $('#subject_'+i).text()) {
				if ($('#category_'+i).text() != 'Nilai Total' && $('#category_'+i).text() != 'Total' && $('#category_'+i).text() != 'Kesesuaian proses kerja berdasarkan IK') {
					if ($('#'+id.split('_')[0]+'_question_'+i).val() == '' || $('#'+id.split('_')[0]+'_question_'+i).val() == 'Point & urutan pekerjaan' || $('#'+id.split('_')[0]+'_question_'+i).val() == 'undefined') {
						totals = 0;
					}else{
						totals = parseInt($('#'+id.split('_')[0]+'_question_'+i).val());
					}
					total = total + parseInt(totals);
				}
			}
			if (data_subject[id.split('_')[0]].subject == $('#subject_'+i).text() && $('#category_'+i).text() == 'Kesesuaian proses kerja berdasarkan IK') {
				// console.log($('#category_'+i).text());
				// console.log($('#'+id.split('_')[0]+'_question_'+i).val());
				$('#'+id.split('_')[0]+'_weight_'+i).val($('#'+id.split('_')[0]+'_question_'+i).val());
			}
		}
		$('#'+id.split('_')[0]+'_sub_total_question').val(total);
	}

	function checkSubTotalResult(id) {
		var subject_select = [];
		$("input[name='subject_select']:checked").each(function (i) {
	            subject_select[i] = $(this).val();
        });
        var total = 0;
		for(var i = 0; i < count_point;i++){
			if (data_subject[id.split('_')[0]].subject == $('#subject_'+i).text()) {
				if ($('#category_'+i).text() != 'Nilai Total' && $('#category_'+i).text() != 'Total' && $('#category_'+i).text() != 'Kesesuaian proses kerja berdasarkan IK') {
					if ($('#'+id.split('_')[0]+'_question_result_'+i).val() == '' || $('#'+id.split('_')[0]+'_question_result_'+i).val() == 'Point & urutan pekerjaan' || $('#'+id.split('_')[0]+'_question_result_'+i).val() == 'undefined') {
						totals = 0;
					}else{
						totals = parseInt($('#'+id.split('_')[0]+'_question_result_'+i).val());
					}
					total = total + parseInt(totals);
				}
			}
		}
		$('#'+id.split('_')[0]+'_sub_total_question_result').val(total);
	}

	function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan proses?')) {
			$("#loading").show();
			var subject_select = [];
			$("input[name='subject_select']:checked").each(function (i) {
		            subject_select[i] = $(this).val();
	        });
	        var subjects = [];
	        for(var k = 0; k < data_subject.length;k++){
	        	subjects.push(data_subject[k].subject);
	        }
			for(var i = 0; i < count_point;i++){
				for(var j = 0; j < subject_select.length;j++){
					// console.log($('#subject_'+i).text());
					if (subject_select[j] == $('#subject_'+i).text()) {
						if ($('#'+subjects.indexOf(subject_select[j])+'_question_'+i).val() == '' || $('#'+subjects.indexOf(subject_select[j])+'_weight_'+i).val() == '' || $('#'+subjects.indexOf(subject_select[j])+'_question_result_'+i).val() == '' || $('#'+subjects.indexOf(subject_select[j])+'_sub_total_question_result').val() == '' || $('#'+subjects.indexOf(subject_select[j])+'_sub_total_question').val() == '') {
							openErrorGritter('Error!','Isi Semua Data.');
							$("#loading").hide();
							return false;
						}
					}
				}
			}

			var subject = [];
			var test_type = [];
			var category = [];
			var question = [];
			var weight = [];
			var question_result = [];
			var note = [];
			var presentase_result = [];
			var presentase_a = [];
			var subject_select = [];
			var standard = [];

			$("input[name='subject_select']:checked").each(function (i) {
		            subject_select[i] = $(this).val();
	        });
	        for(var i = 0; i < count_point;i++){
				subject.push($('#subject_'+i).text());
				test_type.push($('#test_type_'+i).text());
				category.push($('#category_'+i).text());
				standard.push($('#standard_'+i).text());
				for(var j = 0; j < subjects.length;j++){
					if (subjects[j] == $('#subject_'+i).text()) {
						weight.push($('#'+j+'_weight_'+i).val());
						if ($('#category_'+i).text() == 'Nilai Total' || $('#category_'+i).text() == 'Total') {
							question.push($('#'+j+'_sub_total_question').val());
							question_result.push($('#'+j+'_sub_total_question_result').val());
						}else if($('#category_'+i).text() == 'Kesesuaian proses kerja berdasarkan IK'){
							question.push($('#'+j+'_question_'+i).val());
							question_result.push($('#'+j+'_question_result_'+i).val());
						}else{
							question.push($('#'+j+'_question_'+i).val());
							question_result.push($('#'+j+'_question_result_'+i).val());
						}
					}
				}

				// presentase_a.push(presentase_as);
				// 		presentase_result.push(presentase_results);
				// 		note.push('-');

				// var re = new RegExp($('#subject_'+i).text(), 'gi');
				// console.log(subject_select.join());
				// console.log(subject_select.join().match(re));

				if (subject_select.indexOf($('#subject_'+i).text()) > -1) {
					for(var k = 0; k < subject_select.length;k++){
						if (subject_select[k] == $('#subject_'+i).text()) {
							var presentase_as = 0;
							var presentase_results = 0;
							if ($('#category_'+i).text() == 'Grade A') {
								presentase_as = ((parseInt($('#'+subjects.indexOf(subject_select[k])+'_question_result_'+i).val()) / parseInt($('#'+subjects.indexOf(subject_select[k])+'_question_'+i).val()))*100).toFixed(0);
							}else if ($('#category_'+i).text() == 'Nilai Total') {
								presentase_results = ((parseInt($('#'+subjects.indexOf(subject_select[k])+'_sub_total_question_result').val()) / parseInt($('#'+subjects.indexOf(subject_select[k])+'_sub_total_question').val()))*100).toFixed(0);
							}else if ($('#category_'+i).text() == 'Kesesuaian proses kerja berdasarkan IK') {
								presentase_results = ((parseInt($('#'+subjects.indexOf(subject_select[k])+'_question_result_'+i).val()) / parseInt($('#'+subjects.indexOf(subject_select[k])+'_weight_'+i).val()))*100).toFixed(0);
							}else if ($('#category_'+i).text() == 'Total') {
								presentase_results = ((parseInt($('#'+subjects.indexOf(subject_select[k])+'_sub_total_question_result').val()) / parseInt($('#'+subjects.indexOf(subject_select[k])+'_sub_total_question').val()))*100).toFixed(0);
							}
							presentase_a.push(presentase_as);
							presentase_result.push(presentase_results);
							note.push('-');
						}
					}
				}else{
					presentase_a.push(null);
					presentase_result.push(null);
					note.push('Tidak Sertifikasi');
				}
			}

			var auditor_id = $('#auditor_id').text();
			var auditor_name = $('#auditor_name').text();
			var certificate_id = $('#certificate_id').text();
			var certificate_code = $('#certificate_code').text();
			var code_desc = $('#code_desc').text();
			var employee_id = $('#employee_id').text();
			var employee_name = $('#employee_name').text();
			var periode_from = $('#periode_from').text();
			var periode_to = $('#periode_to').text();
			var certificate_name = $('#certificate_name').text();
			var staff_id = $('#staff_id').val();
			var staff_name = $('#staff_name').val();
			var staff_email = $('#staff_email').val();

			var data = {
				subject:subject,
				test_type:test_type,
				category:category,
				question:question,
				weight:weight,
				question_result:question_result,
				count_point:count_point,
				subjects:subjects,
				subject_select:subject_select,
				presentase_result:presentase_result,
				presentase_a:presentase_a,
				note:note,
				auditor_id:auditor_id,
				auditor_name:auditor_name,
				certificate_id:certificate_id,
				certificate_code:certificate_code,
				code_desc:code_desc,
				employee_id:employee_id,
				employee_name:employee_name,
				periode_from:periode_from,
				periode_to:periode_to,
				certificate_name:certificate_name,
				standard:standard,
				staff_id:staff_id,
				staff_name:staff_name,
				staff_email:staff_email,
			}

			$.post('{{ url("input/renew/qa/certificate") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					// location.reload();
					$("#loading").hide();
					location.replace("{{url('review/qa/certificate')}}/"+result.certificate_id+'/Leader QA');
				}else{
					$("#loading").hide();
					openErrorGritter('Error!',result.message);
					return false;
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
