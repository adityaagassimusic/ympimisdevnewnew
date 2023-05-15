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
table.table-responsive  > tr > td:hover{
	background-color: #7dfa8c !important;
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

input:focus, textarea:focus, select:focus{
        outline: none;
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

#tableCheck > tr > td:hover,{
	background-color: #7dfa8c !important;
}

#tableCheck2 > tr > th, #tableCheck2 > tr > td,{
	padding: 2px;
}

#tableCheck2 > tr > td:hover,{
	background-color: #7dfa8c !important;
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
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 20px" colspan="2">Assessor</td>
				</tr>
				<tr>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 20px;color: white" id="auditor_id">{{$auditor_id}}</td>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 20px;color: white" id="auditor_name">{{$auditor_name}}</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center;padding-left: 5px">
			<table style="width: 100%;border:1px solid black">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%;font-size: 20px">Periode</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 20px;color:white" id="periode_fix">-</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 20px">
					@if(!preg_match($pattern_periode,$periode))
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Assessment Hari 1</a></li>
					<li class="vendor-tab" id="tab_ke_2"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Assessment Hari 2</a></li>
					@endif
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1" >
						<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck">
						
						</table>
					</div>
					<div class="tab-pane" id="tab_2" >
						<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck2">
						
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;padding-right: 5px">
			<?php if (preg_match($pattern, $username)){ ?>
				<?php $url = url(''); ?>
				<a class="btn btn-success" href="{{url('')}}" style="width: 100%;font-size: 25px;font-weight: bold;">
					SELESAI
				</a>
			<?php }else{ ?>
				<?php $url = url('index/sga/monitoring') ?>
				<a class="btn btn-success" href="{{url('index/sga')}}" style="width: 100%;font-size: 25px;font-weight: bold;">
					SELESAI
				</a>
			<?php } ?>
		</div>
		<!-- <div class="col-xs-4" style="margin-top: 10px;padding-right: 5px;padding-left: 5px;">
			<button class="btn btn-warning" onclick="confirmTemp()" style="width: 100%;font-size: 25px;font-weight: bold;">
				SAVE TEMPORARY
			</button>
		</div> -->
		<!-- <div class="col-xs-6" style="margin-top: 10px;padding-left: 5px">
			<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
				SAVE
			</button>
		</div> -->
	</div>


	<div class="modal fade" id="modalCode">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						SGA ASSESSMENT
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Periode</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px;text-align: center;font-size: 20px">
								<select class="form-control select2" id="periode" style="width: 100%;text-align: center;font-size: 20px" data-placeholder="Pilih Periode">
									@foreach($periode as $per)
										<option value="{{$per->periode}}" style="font-size: 20px">{{$per->periode}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<div class="row">
								<button onclick="savePeriode()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
									CONFIRM
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" id="modalNilai">
		<div class="modal-dialog modal-md" style="width: 100vh">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PILIH NILAI
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="row">
							<input type="hidden" name="id_result" id="id_result" value="">
							<input type="hidden" name="id_nilai" id="id_nilai" value="">
							<table style="width: 100%">
								<tr>
									<td style="padding: 3px;width: 15%">
										<button class="btn btn-primary" id="1" style="width: 100%;font-size: 20px" onclick="saveResult(this.id)"><input type="hidden" name="nilai_pilihan_1" id="nilai_pilihan_1">Kurang</button>
									</td>
									<td style="padding: 3px;width: 15%">
										<button class="btn btn-primary" id="2" style="width: 100%;font-size: 20px" onclick="saveResult(this.id)"><input type="hidden" name="nilai_pilihan_2" id="nilai_pilihan_2">Cukup</button>
									</td>
									<td style="padding: 3px;width: 15%">
										<button class="btn btn-primary" id="3" style="width: 100%;font-size: 20px" onclick="saveResult(this.id)"><input type="hidden" name="nilai_pilihan_3" id="nilai_pilihan_3">Baik</button>
									</td>
									<td style="padding: 3px;width: 15%">
										<button class="btn btn-primary" id="4" style="width: 100%;font-size: 20px" onclick="saveResult(this.id)"><input type="hidden" name="nilai_pilihan_4" id="nilai_pilihan_4">Sangat Baik</button>
									</td>
									<td style="padding: 3px;width: 1%">
										<button class="btn btn-danger" id="5" style="width: 100%;font-size: 20px" onclick="saveResult(this.id)"><input type="hidden" name="nilai_pilihan_5" id="nilai_pilihan_5" value="Clear">Clear</button>
									</td>
								</tr>
							</table>
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
			allowClear:true,
			ropdownParent: $('#modalCode')
		});

		$('.select3').select2({
			allowClear:true,
			ropdownParent: $('#modalCode')
		});

		// $('#modalCode').modal({
		// 	backdrop: 'static',
		// 	keyboard: false
		// });

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

      	$('body').toggleClass("sidebar-collapse");
		cancelAll();
		$(document).keydown(function(e) {
			switch(e.which) {
				case 48:
				location.reload(true);
				break;
				case 49:
				$("#tab_header_1").click()
				break;
				case 50:
				$("#tab_header_2").click()
				break;
			}
		});

	});

	var points = [];
	var teams1 = [];
	var teams2 = [];

	// $('#modalCode').on('shown.bs.modal', function () {
	// 	// $('#operator').focus();
	// });

	function cancelAll() {
		savePeriode();
		// $('#modalCode').modal('show');
		points = [];
		teams1 = [];
		teams2 = [];
	}

	function savePeriode() {
		$("#loading").show();
		if ($('#periode').val() == '' || $('#periode').val() == null) {
			audio_error.play();
			$("#loading").hide();
			openErrorGritter('Error!','Assessment is not ready to use.');
			window.location.replace("{{$url}}");
			return false;
		}

		var data = {
			periode:$('#periode').val().replace(' ','_'),
			asesor_id:$('#auditor_id').text(),
		}


		$.get('{{ url("fetch/sga/point") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableCheck').html('');
				var tableCheck = '';

				const nilais = ["Kurang", "Cukup", "Baik","Sangat Baik"];

				if (result.cek_temp == null) {
					tableCheck += '<tr id="header">';
					tableCheck += '<th colspan="2" rowspan="3" style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:10%;padding:5px;text-align:center">Assessment Criteria</th>';
					for(var i = 0; i < result.teams1.length;i++){
						var url_pdf = "{{ url('data_file/sga/pdf/') }}"+'/'+result.teams1[i].file_pdf;
						if ($('#periode').val().match(/Final/gi)) {
							tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_no_'+i+'">#'+(i+1)+' '+result.teams1[i].team_no+'<br><a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a><input type="hidden" id="day_'+i+'" value="'+result.teams1[i].day+'"></th>';
						}else{
							tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_no_'+i+'">#'+(i+1)+' '+result.teams1[i].team_no+'<input type="hidden" id="day_'+i+'" value="'+result.teams1[i].day+'"></th>';
						}
					}
					tableCheck += '</tr>';
					tableCheck += '<tr>';
					for(var i = 0; i < result.teams1.length;i++){
						tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_name_'+i+'">'+result.teams1[i].team_name+'</th>';
					}
					tableCheck += '</tr>';
					tableCheck += '<tr>';
					for(var i = 0; i < result.teams1.length;i++){
						tableCheck += '<th style="background-color:#15850d;color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center">Total Nilai<br><span id="total_'+i+'">0</span></th>';
					}
					tableCheck += '</tr>';

					for(var j = 0; j < result.points.length;j++){
						var point_detail = result.points[j].criteria.split('_');
						for(var k = 0; k < 1;k++){
							tableCheck += '<tr>';
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck += '<td rowspan="'+point_detail.length+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left"><input type="hidden" id="nilai_1_'+j+'" value="'+result.points[j].result_1+'"><input type="hidden" id="nilai_2_'+j+'" value="'+result.points[j].result_2+'"><input type="hidden" id="nilai_3_'+j+'" value="'+result.points[j].result_3+'"><input type="hidden" id="nilai_4_'+j+'" value="'+result.points[j].result_4+'">'+result.points[j].criteria_category+'</td>';
							for(var i = 0; i < result.teams1.length;i++){
								tableCheck += '<td rowspan="'+point_detail.length+'" id="text_result_'+j+'_'+i+'" onclick="setResult(this.id)" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left;cursor:pointer;text-align:center"><input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="result_'+j+'_'+i+'" readonly><input type="hidden" id="id_nilai_'+j+'_'+i+'" readonly></td>';
							}
							tableCheck += '</tr>';
						}
						for(var k = 1; k < point_detail.length;k++){
							tableCheck += '<tr>';
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck += '</tr>';
						}
					}

					$("#tableCheck").append(tableCheck);


					//CHECK 2
					$('#tableCheck2').html('');
					var tableCheck2 = '';

					tableCheck2 += '<tr id="header">';
					tableCheck2 += '<th colspan="2" rowspan="3" style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:10%;padding:5px;text-align:center;">Assessment Criteria</th>';
					var ss = parseInt(result.teams1.length);
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center;" id="team_no_'+ss+'">#'+(ss+1)+' '+result.teams2[i].team_no+'<input type="hidden" id="day_'+ss+'" value="'+result.teams2[i].day+'"></th>';
						ss++;
					}
					tableCheck2 += '</tr>';
					tableCheck2 += '<tr>';
					var ss = parseInt(result.teams1.length);
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center;" id="team_name_'+ss+'">'+result.teams2[i].team_name+'</th>';
						ss++;
					}
					tableCheck2 += '</tr>';
					tableCheck2 += '<tr>';
					var ss = parseInt(result.teams1.length);
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:#15850d;color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center">Total Nilai<br><span id="total_'+ss+'">0</span></th>';
						ss++;
					}
					tableCheck2 += '</tr>';

					for(var j = 0; j < result.points.length;j++){
						var point_detail = result.points[j].criteria.split('_');
						for(var k = 0; k < 1;k++){
							tableCheck2 += '<tr>';
							tableCheck2 += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck2 += '<td rowspan="'+point_detail.length+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left"><input type="hidden" id="nilai_1_'+j+'" value="'+result.points[j].result_1+'"><input type="hidden" id="nilai_2_'+j+'" value="'+result.points[j].result_2+'"><input type="hidden" id="nilai_3_'+j+'" value="'+result.points[j].result_3+'"><input type="hidden" id="nilai_4_'+j+'" value="'+result.points[j].result_4+'">'+result.points[j].criteria_category+'</td>';
							var ss = parseInt(result.teams1.length);
							for(var i = 0; i < result.teams2.length;i++){
								tableCheck2 += '<td rowspan="'+point_detail.length+'" id="text_result_'+j+'_'+ss+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:center;cursor:pointer" onclick="setResult(this.id)"><input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="result_'+j+'_'+ss+'" readonly><input type="hidden" id="id_nilai_'+j+'_'+ss+'" readonly></td>';
								ss++;
							}
							tableCheck2 += '</tr>';
						}
						for(var k = 1; k < point_detail.length;k++){
							tableCheck2 += '<tr>';
							tableCheck2 += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck2 += '</tr>';
						}
					}

					$('#tableCheck2').append(tableCheck2);
				}else{
					tableCheck += '<tr id="header">';
					tableCheck += '<th colspan="2" rowspan="3" style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:10%;padding:5px;text-align:center">Assessment Criteria</th>';
					for(var i = 0; i < result.teams1.length;i++){
						var url_pdf = "{{ url('data_file/sga/pdf/') }}"+'/'+result.teams1[i].file_pdf;
						if ($('#periode').val().match(/Final/gi)) {
							tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_no_'+i+'">#'+(i+1)+' '+result.teams1[i].team_no+'<br><a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a><input type="hidden" id="day_'+i+'" value="'+result.teams1[i].day+'"></th>';
						}else{
							tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_no_'+i+'">#'+(i+1)+' '+result.teams1[i].team_no+'<input type="hidden" id="day_'+i+'" value="'+result.teams1[i].day+'"></th>';
						}
					}
					tableCheck += '</tr>';
					tableCheck += '<tr>';
					for(var i = 0; i < result.teams1.length;i++){
						tableCheck += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_name_'+i+'">'+result.teams1[i].team_name+'</th>';
					}
					tableCheck += '</tr>';
					tableCheck += '<tr>';
					for(var i = 0; i < result.teams1.length;i++){
						tableCheck += '<th style="background-color:#15850d;color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center">Total Nilai<br><span id="total_'+i+'">0</span></th>';
					}
					tableCheck += '</tr>';

					for(var j = 0; j < result.points.length;j++){
						var point_detail = result.points[j].criteria.split('_');
						var u = 0;
						for(var k = 0; k < 1;k++){
							tableCheck += '<tr>';
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck += '<td rowspan="'+point_detail.length+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left"><input type="hidden" id="nilai_1_'+j+'" value="'+result.points[j].result_1+'"><input type="hidden" id="nilai_2_'+j+'" value="'+result.points[j].result_2+'"><input type="hidden" id="nilai_3_'+j+'" value="'+result.points[j].result_3+'"><input type="hidden" id="nilai_4_'+j+'" value="'+result.points[j].result_4+'">'+result.points[j].criteria_category+'</td>';
							for(var i = 0; i < result.cek_temp.length;i++){
								if (u < result.teams1.length) {
										if (result.cek_temp[i].criteria_category == result.points[j].criteria_category && result.cek_temp[i].team_no == result.teams1[u].team_no) {
										var katanilai = '';
										if (result.cek_temp[i].result == result.points[j].result_1 && result.cek_temp[i].result != null) {
											var katanilai = nilais[0]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_2  && result.cek_temp[i].result != null) {
											var katanilai = nilais[1]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_3  && result.cek_temp[i].result != null) {
											var katanilai = nilais[2]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_4  && result.cek_temp[i].result != null) {
											var katanilai = nilais[3]+' ('+result.cek_temp[i].result+')';
										}
										tableCheck += '<td rowspan="'+point_detail.length+'" onclick="setResult(this.id)" id="text_result_'+j+'_'+u+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:center;cursor:pointer"><input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="result_'+j+'_'+u+'" value="'+result.cek_temp[i].result+'" readonly><input type="hidden" id="id_nilai_'+j+'_'+u+'" readonly>'+katanilai+'</td>';
										u++;
									}
								}
							}
							tableCheck += '</tr>';
						}
						for(var k = 1; k < point_detail.length;k++){
							tableCheck += '<tr>';
							tableCheck += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck += '</tr>';
						}
					}

					$("#tableCheck").append(tableCheck);

					for(var j = 0; j < result.teams1.length;j++){
						var total_team = 0;
						for(var i = 0; i < result.cek_temp.length;i++){
							if (result.cek_temp[i].team_no == result.teams1[j].team_no) {
								if (result.cek_temp[i].result != null && result.cek_temp[i].result != 'null' && result.cek_temp[i].result != '') {
									total_team = total_team + parseInt(result.cek_temp[i].result);
								}
							}
						}
						$('#total_'+j).html(total_team);
					}

					$('#tableCheck2').html('');
					var tableCheck2 = '';

					tableCheck2 += '<tr id="header">';
					tableCheck2 += '<th colspan="2" rowspan="3" style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:10%;padding:5px;text-align:center">Assessment Criteria</th>';
					var ss = parseInt(result.teams1.length);
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_no_'+i+'">#'+(ss+1)+' '+result.teams2[i].team_no+'<input type="hidden" id="day_'+ss+'" value="'+result.teams2[i].day+'"></th>';
						ss++;
					}
					tableCheck2 += '</tr>';
					tableCheck2 += '<tr>';
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:rgb(126,86,134);color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center" id="team_name_'+i+'">'+result.teams2[i].team_name+'</th>';
					}
					tableCheck2 += '</tr>';
					tableCheck2 += '<tr>';
					var ss = parseInt(result.teams1.length);
					for(var i = 0; i < result.teams2.length;i++){
						tableCheck2 += '<th style="background-color:#15850d;color:#fff;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:5px;text-align:center">Total Nilai<br><span id="total_'+ss+'">0</span></th>';
						ss++;
					}
					tableCheck2 += '</tr>';

					for(var j = 0; j < result.points.length;j++){
						var point_detail = result.points[j].criteria.split('_');
						var u = result.teams1.length;
						var ss = 0;
						for(var k = 0; k < 1;k++){
							tableCheck2 += '<tr>';
							tableCheck2 += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck2 += '<td rowspan="'+point_detail.length+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left"><input type="hidden" id="nilai_1_'+j+'" value="'+result.points[j].result_1+'"><input type="hidden" id="nilai_2_'+j+'" value="'+result.points[j].result_2+'"><input type="hidden" id="nilai_3_'+j+'" value="'+result.points[j].result_3+'"><input type="hidden" id="nilai_4_'+j+'" value="'+result.points[j].result_4+'">'+result.points[j].criteria_category+'</td>';
							for(var i = 0; i < result.cek_temp.length;i++){
								if (u < (parseInt(result.teams1.length)+parseInt(result.teams2.length))) {
										if (result.cek_temp[i].criteria_category == result.points[j].criteria_category && result.cek_temp[i].team_no == result.teams2[ss].team_no) {
										var katanilai = '';
										if (result.cek_temp[i].result == result.points[j].result_1 && result.cek_temp[i].result != null) {
											var katanilai = nilais[0]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_2  && result.cek_temp[i].result != null) {
											var katanilai = nilais[1]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_3  && result.cek_temp[i].result != null) {
											var katanilai = nilais[2]+' ('+result.cek_temp[i].result+')';
										}else if (result.cek_temp[i].result == result.points[j].result_4  && result.cek_temp[i].result != null) {
											var katanilai = nilais[3]+' ('+result.cek_temp[i].result+')';
										}
										tableCheck2 += '<td rowspan="'+point_detail.length+'"  onclick="setResult(this.id)" id="text_result_'+j+'_'+u+'" style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:center;cursor:pointer"><input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="result_'+j+'_'+u+'" value="'+result.cek_temp[i].result+'" readonly><input type="hidden" id="id_nilai_'+j+'_'+u+'" readonly>'+katanilai+'</td>';
										u++;
										ss++;
									}
								}
							}
							tableCheck2 += '</tr>';
						}
						for(var k = 1; k < point_detail.length;k++){
							tableCheck2 += '<tr>';
							tableCheck2 += '<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;text-align:left">'+point_detail[k]+'</td>';
							tableCheck2 += '</tr>';
						}
					}

					$('#tableCheck2').append(tableCheck2);
					var ss = result.teams1.length;
					for(var j = 0; j < result.teams2.length;j++){
						var total_team = 0;
						for(var i = 0; i < result.cek_temp.length;i++){
							if (result.cek_temp[i].team_no == result.teams2[j].team_no) {
								if (result.cek_temp[i].result != null && result.cek_temp[i].result != 'null' && result.cek_temp[i].result != '') {
									total_team = total_team + parseInt(result.cek_temp[i].result);
								}
							}
						}
						$('#total_'+ss).html(total_team);
						ss++;
					}


				}

				$('#modalCode').modal('hide');
				$('#loading').hide();

				$('#periode_fix').html($('#periode').val().replace("_", " "));

				points = result.points;
				teams1 = result.teams1;
				teams2 = result.teams2;

				confirmAll();

			}else{
				$("#loading").hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function setResult(id) {
		$('#modalNilai').modal('show');
		$('#nilai_pilihan_1').val($('#nilai_1_'+id.replace('text_','').split('_')[1]).val());
		$('#nilai_pilihan_2').val($('#nilai_2_'+id.replace('text_','').split('_')[1]).val());
		$('#nilai_pilihan_3').val($('#nilai_3_'+id.replace('text_','').split('_')[1]).val());
		$('#nilai_pilihan_4').val($('#nilai_4_'+id.replace('text_','').split('_')[1]).val());

		$('#1').html('<input type="hidden" name="nilai_pilihan_1" id="nilai_pilihan_1" value="'+$('#nilai_1_'+id.replace('text_','').split('_')[1]).val()+'">Kurang ('+$('#nilai_1_'+id.replace('text_','').split('_')[1]).val()+')');
		$('#2').html('<input type="hidden" name="nilai_pilihan_2" id="nilai_pilihan_2" value="'+$('#nilai_2_'+id.replace('text_','').split('_')[1]).val()+'">Cukup ('+$('#nilai_2_'+id.replace('text_','').split('_')[1]).val()+')');
		$('#3').html('<input type="hidden" name="nilai_pilihan_3" id="nilai_pilihan_3" value="'+$('#nilai_3_'+id.replace('text_','').split('_')[1]).val()+'">Baik ('+$('#nilai_3_'+id.replace('text_','').split('_')[1]).val()+')');
		$('#4').html('<input type="hidden" name="nilai_pilihan_4" id="nilai_pilihan_4" value="'+$('#nilai_4_'+id.replace('text_','').split('_')[1]).val()+'">Sangat Baik ('+$('#nilai_4_'+id.replace('text_','').split('_')[1]).val()+')');
		$('#id_result').val(id);
		$('#id_nilai').val($('#id_nilai_'+id.replace('text_','').split('_')[1]+'_'+id.replace('text_','').split('_')[2]).val());
	}

	function saveResult(id) {
		$('#modalNilai').modal('hide');
		var ids = $('#id_result').val().replace('text_','');
		var ids_nilai = $('#id_result').val().replace('text_result_','id_nilai_');
		var nilai = null;
		if ($('#'+id).text() == 'Clear') {
			$('#'+$('#id_result').val()).html('<input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="'+ids+'" readonly><input type="hidden" value="'+$('#id_nilai').val()+'" id="'+ids_nilai+'" readonly>');
			$('#'+ids).val('');
		}else{
			$('#'+$('#id_result').val()).html($('#'+id).text()+'<input type="hidden" style="width:100%;background-color:white;text-align:right;min-height:23vh;cursor:pointer;font-size:30px;padding-right:5px" id="'+ids+'" readonly><input type="hidden" value="'+$('#id_nilai').val()+'" id="'+ids_nilai+'" readonly>');
			$('#'+ids).val($('#nilai_pilihan_'+id).val());
			nilai = $('#nilai_pilihan_'+id).val();
		}
		var total_team = 0;
		for(var i = 0; i < points.length;i++){
			if ($('#result_'+i+'_'+ids.split('_')[2]).val() != '' && $('#result_'+i+'_'+ids.split('_')[2]).val() != 'null' && $('#result_'+i+'_'+ids.split('_')[2]).val() != null) {
				total_team = total_team + parseInt($('#result_'+i+'_'+ids.split('_')[2]).val());
			}
		}
		$('#total_'+ids.split('_')[2]).html(total_team);

		var id_nilai = $('#id_nilai').val();
		$('#'+ids_nilai).val($('#id_nilai').val());
		var data = {
			id:id_nilai,
			nilai:nilai
		}
		$.post('{{ url("input/sga/assessment/result") }}', data, function(result, status, xhr){
			if(result.status){

			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				$('#loading').hide();
			}
		})
	}

	function confirmTemp() {
		$('#loading').show();
		var results = [];

		for (var i = 0; i < teams1.length; i++) {
			for (var j = 0; j < points.length; j++) {
				results.push({
					criteria:points[j].criteria,
					criteria_category:points[j].criteria_category,
					team_no:teams1[i].team_no,
					team_name:teams1[i].team_name,
					result:$('#result_'+j+'_'+i).val(),
					day:$('#day_'+i).val(),
					asesor_id:$("#auditor_id").text(),
					asesor_name:$("#auditor_name").text(),
					assessment_date:'{{date("Y-m-d")}}',
					periode:$("#periode_fix").text().replace(" ", "_"),
				});
			}
		}

		var ss = teams1.length;
		for (var i = 0; i < teams2.length; i++) {
			for (var j = 0; j < points.length; j++) {
				results.push({
					criteria:points[j].criteria,
					criteria_category:points[j].criteria_category,
					team_no:teams2[i].team_no,
					team_name:teams2[i].team_name,
					result:$('#result_'+j+'_'+ss).val(),
					day:$('#day_'+ss).val(),
					asesor_id:$("#auditor_id").text(),
					asesor_name:$("#auditor_name").text(),
					assessment_date:'{{date("Y-m-d")}}',
					periode:$("#periode_fix").text().replace(" ", "_"),
				});
			}
			ss++;
		}

		var data = {
			results:results
		}

		$.post('{{ url("input/sga/assessment/temp") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!',result.message);
				// location.reload();
			}else{
				audio_error.play();
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
				return false;
			}
		});
	}

	function confirmAll() {
		// if (confirm('Apakah Anda yakin ingin menyelesaikan penilaian? Anda tidak dapat mengubah Nilai setelah menekan tombol Save.')) {
			$('#loading').show();
			var results = [];

			for (var i = 0; i < teams1.length; i++) {
				for (var j = 0; j < points.length; j++) {
					results.push({
						criteria:points[j].criteria,
						criteria_category:points[j].criteria_category,
						team_no:teams1[i].team_no,
						team_name:teams1[i].team_name,
						day:$('#day_'+i).val(),
						result:$('#result_'+j+'_'+i).val(),
						asesor_id:$("#auditor_id").text(),
						asesor_name:$("#auditor_name").text(),
						assessment_date:'{{date("Y-m-d")}}',
						periode:$("#periode_fix").text().replace(" ", "_"),
					});
				}
			}

			var ss = teams1.length;
			for (var i = 0; i < teams2.length; i++) {
				for (var j = 0; j < points.length; j++) {
					results.push({
						criteria:points[j].criteria,
						criteria_category:points[j].criteria_category,
						team_no:teams2[i].team_no,
						team_name:teams2[i].team_name,
						day:$('#day_'+ss).val(),
						result:$('#result_'+j+'_'+ss).val(),
						asesor_id:$("#auditor_id").text(),
						asesor_name:$("#auditor_name").text(),
						assessment_date:'{{date("Y-m-d")}}',
						periode:$("#periode_fix").text().replace(" ", "_"),
					});
				}
				ss++;
			}

			var data = {
				results:results
			}

			$.post('{{ url("input/sga/assessment") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					// openSuccessGritter('Success!',result.message);
					// location.reload();
					var uu = 0;
					for (var i = 0; i < teams1.length; i++) {
						for (var j = 0; j < points.length; j++) {
							$('#id_nilai_'+j+'_'+i).val(result.id[uu]);
							uu++;
						}
					}

					var ss = teams1.length;
					console.log(uu);
					for (var i = 0; i < teams2.length; i++) {
						for (var j = 0; j < points.length; j++) {
							$('#id_nilai_'+j+'_'+ss).val(result.id[uu]);
							uu++;
						}
						ss++;
					}
				}else{
					audio_error.play();
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
					return false;
				}
			});
		// }
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
