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

input:focus, textarea:focus, select:focus{
        outline: none;
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
/*#tableCheck > tbody > tr > td {
	background-color: #7dfa8c !important;
}*/
.td_isi:hover {
	background-color: #7dfa8c !important;
}
.td_isi{
	outline: none !important;
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
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%" colspan="2">Asesor (Leader QA)</td>
				</tr>
				<tr>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 15px;color: white" id="auditor_id">{{$auditor_id}}</td>
					<td  style="background-color: #605ca8;border:1px solid black;font-size: 15px;color: white" id="auditor_name">{{$auditor_name}}</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Code No.</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Code</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="code">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="code_number">-</td>
				</tr>
				<tr>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center;padding-left: 5px">
			<table style="width: 100%;border:1px solid black">
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Emp ID</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Emp Name</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="employee_id">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="employee_name">-</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Desc</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Issued Date</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 1%">Expired Date</td>
				</tr>
				<tr>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="code_desc">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="periode">-</td>
					<td style="background-color: #605ca8;border:1px solid black;font-size: 15px;color:white" id="expired_date">-</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px;padding: 5px;background-color: #ff3729;margin-left: 15px;display: none;" id="div_error">
			<span style="font-weight: bold;font-size: 20px;color: white">PESERTA TIDAK LULUS !!! PERIKSA KEMBALI NILAI YANG ANDA MASUKKAN.</span>
		</div>
		<div class="col-xs-6" style="text-align: center;margin-top: 10px;padding-right: 5px">
			<div class="col-xs-6" style="padding-left: 0px">
				<span style="font-weight: bold;color: white" class="pull-left">Standart kelulusan : Penguasaan 100% point IK</span>
			</div>
			<div class="col-xs-6" style="background-color: red;display: none;text-align: center;display: grid;" id="div_error_ik">
				<span style="font-weight: bold;color: white;text-align: center;" class="pull-left">PEMAHAMAN IK TIDAK LULUS</span>
			</div>
			<input type="hidden" id="error_ik">
			<input type="hidden" id="error_total">
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableComposition">
				<thead>
					<tr>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">Total Point IK</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">OK</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">NG</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">% Nilai</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="total_ik" class="td_isi numpad" style="width: 100%;text-align: center;"></td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="ok_ik" onchange="checkIk()" class="td_isi numpad" style="width: 100%;text-align: center;"></td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="ng_ik" readonly="" style="width: 100%;text-align: center;"></td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="text" id="presentase_ik" readonly="" style="width: 100%;text-align: center;background-color: #ffffc2"></td>
					</tr>
				</tbody>
			</table>
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableComposition">
				<thead>
					<tr>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">Grade Soal</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">A</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">B</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">C</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">D</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;" rowspan="2">Total Kesalahan</th>
					</tr>
					<tr>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;">Skor tiap Grade</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">4</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">3</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">2</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">1</th>
					</tr>
				</thead>
				<tbody>
					<?php $index = 1; ?>
					@foreach($composition as $com)
					<tr>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">{{$com->composition}}</td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_a">{{$com->com_a}}</td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_b">{{$com->com_b}}</td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_c">{{$com->com_c}}</td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_d">{{$com->com_d}}</td>
						<td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_fault">{{$com->total_fault}}</td>
					</tr>
					<?php $index++ ?>
					@endforeach
				</tbody>
			</table>
			<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
				CANCEL
			</button>
		</div>
		<div class="col-xs-6" style="text-align: center;margin-top: 10px;padding-left: 5px">
			<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck">
				<thead>
					<tr>
						<th rowspan="2" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;width: 2%">Keterangan</th>
						<th colspan="4" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;width: 5%">Grade Soal</th>
						<th rowspan="2" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;">Total</th>
					</tr>
					<tr>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">A</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">B</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">C</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">D</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">1. Skor</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_1">4</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_2">3</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_3">2</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_4">1</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">-</td>
					</tr>
					<tr>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">2. Jumlah Soal</th>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_1" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_2" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_3" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_4" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="total_question"></td>
					</tr>
					<tr>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">3. Jumlah Soal Benar</td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_1" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_2" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_3" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_4" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
						<td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="total_answer"></td>
					</tr>
					<tr>
						<td style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;font-weight: bold;">4. Presentase Nilai Grade A</td>
						<td colspan="5" style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;font-weight: bold;" id="presentase_a"></td>
					</tr>
					<tr>
						<td style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;font-weight: bold;">5. Presentase Nilai Total</td>
						<td colspan="5" style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;font-weight: bold;" id="presentase_total"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-6" style="margin-left: 0px;padding-left: 5px">
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
						NEW CERTIFICATE IDENTIFICATION
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Kode Sertifikasi</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<select class="form-control select2" id="code_number_select" style="width: 100%" data-placeholder="Pilih Kode Sertifikat" onchange="">
									<option value=""></option>
									@foreach($code_number as $cn)
									<option value="{{$cn->code}}_{{$cn->code_number}}_{{$cn->description}}">{{$cn->code}} - {{$cn->code_number}} - {{$cn->description}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row" style="padding-top: 10px">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Karyawan</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<select class="form-control select3" id="employee_id_select" style="width: 100%" data-placeholder="Pilih Karyawan">
									<option value=""></option>
									@foreach($employees as $employees)
									<option value="{{$employees->employee_id}}_{{$employees->name}}_{{$employees->department}}_{{$employees->sub_group}}">{{$employees->employee_id}} - {{$employees->name}}</option>
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

	var periode = JSON.parse('<?php echo $periode ?>');
	var fy = JSON.parse('<?php echo $fy ?>');

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true,
			ropdownParent: $('#modalCode')
		});

		$('.select3').select2({
			allowClear:true,
			ropdownParent: $('#modalCode')
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
		
		$('#check_time').val('{{date("Y-m-d H:i:s")}}');
		count_point = 0;
		cancelAll();

	});

	function checkIk() {
		var total_ik = $('#total_ik').val();
		var ok_ik = $('#ok_ik').val();

		if (ok_ik != total_ik) {
			audio_error.play();
			openErrorGritter('Error!','Jumlah OK HARUS SAMA DENGAN Total Point IK');
			$('#ok_ik').val('');
			return false;
		}

		$('#div_error_ik').hide();
		$('#error_ik').val('');

		var presentase_ik = ((parseInt(ok_ik)/parseInt(total_ik))*100).toFixed(2);

		$('#ng_ik').val(parseInt(total_ik)-parseInt(ok_ik));
		if (presentase_ik < 100) {
			$('#div_error_ik').show();
			$('#error_ik').val('Error');
		}
		$('#presentase_ik').val(presentase_ik+' %');
	}

	function totalQuestion() {
		var total = 0;
		total = total + (parseInt($('#question_1').val() || 0)*parseInt($('#bobot_1').text())) + (parseInt($('#question_2').val() || 0)*parseInt($('#bobot_2').text())) + (parseInt($('#question_3').val() || 0)*parseInt($('#bobot_3').text())) + (parseInt($('#question_4').val() || 0)*parseInt($('#bobot_4').text()));
		$('#total_question').html(total);
	}

	function totalAnswer() {
		var total = 0;
		total = total + (parseInt($('#answer_1').val() || 0)*parseInt($('#bobot_1').text())) + (parseInt($('#answer_2').val() || 0)*parseInt($('#bobot_2').text())) + (parseInt($('#answer_3').val() || 0)*parseInt($('#bobot_3').text())) + (parseInt($('#answer_4').val() || 0)*parseInt($('#bobot_4').text()));
		$('#total_answer').html(total);

		$('#div_error').hide();
		$('#error_total').val('');

		var status = 0;

		var q_1 = parseInt($('#question_1').val() || 0);
		var a_1 = parseInt($('#answer_1').val() || 0);
		var q_2 = parseInt($('#question_2').val() || 0);
		var a_2 = parseInt($('#answer_2').val() || 0);
		var q_3 = parseInt($('#question_3').val() || 0);
		var a_3 = parseInt($('#answer_3').val() || 0);
		var q_4 = parseInt($('#question_4').val() || 0);
		var a_4 = parseInt($('#answer_4').val() || 0);

		if (checkComposition1() == 0) {
			
		}else if (checkComposition2() == 0) {
			
		}else if (checkComposition3() == 0) {
			
		}else if (checkComposition4() == 0) {
			
		}else{
			$('#div_error').show();
			$('#error_total').val('Error');
		}

		var presentase_a = a_1 / q_1;
		var presentase_total = total / parseInt($('#total_question').text());
		var presentase_as = (presentase_a.toFixed(2))*100;
		var presentase_totals = (presentase_total.toFixed(2))*100;
		if (presentase_as < 100) {
			$('#div_error').show();
			$('#error_total').val('Error');
		}
		if (presentase_totals < 90) {
			$('#div_error').show();
			$('#error_total').val('Error');
		}
		$('#presentase_a').html(presentase_as+' %');
		$('#presentase_total').html(presentase_totals+' %');
	}

	function checkComposition1() {
		var q_1 = parseInt($('#question_1').val() || 0);
		var a_1 = parseInt($('#answer_1').val() || 0);
		var q_2 = parseInt($('#question_2').val() || 0);
		var a_2 = parseInt($('#answer_2').val() || 0);
		var q_3 = parseInt($('#question_3').val() || 0);
		var a_3 = parseInt($('#answer_3').val() || 0);
		var q_4 = parseInt($('#question_4').val() || 0);
		var a_4 = parseInt($('#answer_4').val() || 0);

		var status_1 = 0;

		if ((q_1 - a_1) > parseInt($('#com_1_a').text())) {
			status_1++;
		}
		if ((q_2 - a_2) > parseInt($('#com_1_b').text())) {
			status_1++;
		}
		if ((q_3 - a_3) > parseInt($('#com_1_c').text())) {
			status_1++;
		}
		if ((q_4 - a_4) > parseInt($('#com_1_d').text())) {
			status_1++;
		}
		return status_1;
	}

	function checkComposition2() {
		var q_1 = parseInt($('#question_1').val() || 0);
		var a_1 = parseInt($('#answer_1').val() || 0);
		var q_2 = parseInt($('#question_2').val() || 0);
		var a_2 = parseInt($('#answer_2').val() || 0);
		var q_3 = parseInt($('#question_3').val() || 0);
		var a_3 = parseInt($('#answer_3').val() || 0);
		var q_4 = parseInt($('#question_4').val() || 0);
		var a_4 = parseInt($('#answer_4').val() || 0);

		var status_1 = 0;

		if ((q_1 - a_1) > parseInt($('#com_2_a').text())) {
			status_1++;
		}
		if ((q_2 - a_2) > parseInt($('#com_2_b').text())) {
			status_1++;
		}
		if ((q_3 - a_3) > parseInt($('#com_2_c').text())) {
			status_1++;
		}
		if ((q_4 - a_4) > parseInt($('#com_2_d').text())) {
			status_1++;
		}
		return status_1;
	}

	function checkComposition3() {
		var q_1 = parseInt($('#question_1').val() || 0);
		var a_1 = parseInt($('#answer_1').val() || 0);
		var q_2 = parseInt($('#question_2').val() || 0);
		var a_2 = parseInt($('#answer_2').val() || 0);
		var q_3 = parseInt($('#question_3').val() || 0);
		var a_3 = parseInt($('#answer_3').val() || 0);
		var q_4 = parseInt($('#question_4').val() || 0);
		var a_4 = parseInt($('#answer_4').val() || 0);

		var status_1 = 0;

		if ((q_1 - a_1) > parseInt($('#com_3_a').text())) {
			status_1++;
		}
		if ((q_2 - a_2) > parseInt($('#com_3_b').text())) {
			status_1++;
		}
		if ((q_3 - a_3) > parseInt($('#com_3_c').text())) {
			status_1++;
		}
		if ((q_4 - a_4) > parseInt($('#com_3_d').text())) {
			status_1++;
		}
		return status_1;
	}

	function checkComposition4() {
		var q_1 = parseInt($('#question_1').val() || 0);
		var a_1 = parseInt($('#answer_1').val() || 0);
		var q_2 = parseInt($('#question_2').val() || 0);
		var a_2 = parseInt($('#answer_2').val() || 0);
		var q_3 = parseInt($('#question_3').val() || 0);
		var a_3 = parseInt($('#answer_3').val() || 0);
		var q_4 = parseInt($('#question_4').val() || 0);
		var a_4 = parseInt($('#answer_4').val() || 0);

		var status_1 = 0;

		if ((q_1 - a_1) > parseInt($('#com_4_a').text())) {
			status_1++;
		}
		if ((q_2 - a_2) > parseInt($('#com_4_b').text())) {
			status_1++;
		}
		if ((q_3 - a_3) > parseInt($('#com_4_c').text())) {
			status_1++;
		}
		if ((q_4 - a_4) > parseInt($('#com_4_d').text())) {
			status_1++;
		}
		return status_1;
	}

	$('#modalCode').on('shown.bs.modal', function () {
		// $('#operator').focus();
	});

	function cancelAll() {
		$('#div_error_ik').hide();
		$('#div_error').hide();
		$('#error_total').val('');
		$('#error_ik').val('');
		$('#modalCode').modal('show');
		$('#employee_id_select').val('').trigger('change');
		$('#code_number_select').val('').trigger('change');
		$('#question_1').val('');
		$('#question_2').val('');
		$('#question_3').val('');
		$('#question_4').val('');

		$('#answer_1').val('');
		$('#answer_2').val('');
		$('#answer_3').val('');
		$('#answer_4').val('');

		$('#presentase_a').html('0 %');
		$('#presentase_total').html('0 %');

		$('#total_ik').val('');
		$('#ok_ik').val('');
		$('#ng_ik').val('');
		$('#presentase_ik').val('0 %');
		count_point = 0;
	}

	// function changeCode(value) {
		
	// 	var certificate_code = '';
	// 	$("#certificate_code_select").html('');
	// 	var certificate = '';
	// 	certificate += '<option value=""></option>';
	// 	for(var i = 0; i < certificate_code.length;i++){
	// 		if (value === '') {
	// 			certificate += '<option value=""></option>';
	// 		}else{
	// 			if (certificate_code[i].certificate_code.includes(value.split('-')[0]+'-'+value.split('-')[1])) {
	// 				certificate += '<option value="'+certificate_code[i].certificate_id+'_'+certificate_code[i].certificate_code+'_'+certificate_code[i].employee_id+'_'+certificate_code[i].name+'_'+certificate_code[i].periode_from+'_'+certificate_code[i].periode_to+'_'+certificate_code[i].status+'_'+certificate_code[i].certificate_name+'">'+certificate_code[i].certificate_code+' - '+certificate_code[i].certificate_name+' - '+certificate_code[i].name.split(' ').slice(0,2).join(' ')+'</option>';
	// 			}
	// 		}
	// 	}
	// 	$("#certificate_code_select").append(certificate);
	// 	$('#divCertificateId').show();
	// }

	function saveCode() {
		$("#loading").show();
		if ($('#code_number_select').val() == '' || $('#employee_id_select').val() == '') {
			audio_error.play();
			$("#loading").hide();
			openErrorGritter('Error!','Pilih Kode Sertifikasi');
			return false;
		}

		$('#code').html($('#code_number_select').val().split('_')[0]);
		$('#code_number').html($('#code_number_select').val().split('_')[1]);

		$('#employee_id').html($('#employee_id_select').val().split('_')[0]);
		$('#employee_name').html($('#employee_id_select').val().split('_')[1]);

		if ($('#employee_id_select').val().split('_')[2] == 'Woodwind Instrument - Welding Process (WI-WP) Department' && $('#employee_id_select').val().split('_')[3] == 'FL CL AS KP') {
			$('#code_desc').html($('#code_number_select').val().split('_')[2].split(',')[0]);
		}else if ($('#employee_id_select').val().split('_')[2] == 'Woodwind Instrument - Welding Process (WI-WP) Department' && $('#employee_id_select').val().split('_')[3] == 'FL body HTS') {
			$('#code_desc').html($('#code_number_select').val().split('_')[2].split(',')[1]);
		}else{
			$('#code_desc').html($('#code_number_select').val().split('_')[2]);
		}

		var periodes = '';
		var years = '';
		var year = '';

		for(var i = 0; i < periode.length;i++){
			if (periode[i].code == $('#code_number_select').val().split('_')[0] && periode[i].code_number == $('#code_number_select').val().split('_')[1]) {
				periodes = periode[i].periode;
				// $('#staff_id').val(periode[i].staff_id);
				// $('#staff_name').val(periode[i].staff_name);
				// $('#staff_email').val(periode[i].staff_email);
				var data = {
					code:periode[i].code,
					code_number:periode[i].code_number,
				}
				$.get('{{ url("fetch/new/qa/certificate/inprocess") }}', data, function(result, status, xhr){
					if(result.status){
						$('#staff_id').val(result.periode.staff_id);
						$('#staff_name').val(result.periode.staff_name);
						$('#staff_email').val(result.periode.staff_email);
					}else{
						$('#loading').hide();
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}

		for(var j  =0; j < fy.length;j++){
			if (fy[j].months.split('-')[1] == periodes) {
				years = fy[j].months;
				year = fy[j].months.split('-')[0];
			}
		}

		$('#periode').html('{{date("Y-m-01",strtotime("+1 month"))}}');
		$('#expired_date').html('{{date("Y-m",strtotime("+1 year", strtotime(date("Y-m-01",strtotime("+1 month")))))}}'+'-01');

		var data = {
			code:$('#code_number_select').val().split('_')[0],
			code_number:$('#code_number_select').val().split('_')[1]
		}
		$('#modalCode').modal('hide');
		$("#loading").hide();
	}

	function confirmAll() {
		if (confirm('Apakah Anda yakin untuk menyelesaikan proses?')) {
			$('#loading').show();

			if ($('#error_ik').val() == 'Error' || $('#error_total').val() == 'Error') {
				openErrorGritter('Error!','Peserta TIDAK LULUS. Pastikan nilai sudah benar.');
				$('#loading').hide();
				audio_error.play();
				return false;
			}

			var q_1 = $('#question_1').val();
			var a_1 = $('#answer_1').val();
			var q_2 = $('#question_2').val();
			var a_2 = $('#answer_2').val();
			var q_3 = $('#question_3').val();
			var a_3 = $('#answer_3').val();
			var q_4 = $('#question_4').val();
			var a_4 = $('#answer_4').val();

			var total_question = $('#total_question').text();
			var total_answer = $('#total_answer').text();

			var presentase_a = $('#presentase_a').text();
			var presentase_total = $('#presentase_total').text();

			var total_ik = $('#total_ik').val();
			var ok_ik = $('#ok_ik').val();
			var ng_ik = $('#ng_ik').val();
			var presentase_ik = $('#presentase_ik').val();

			var auditor_id = $('#auditor_id').text();
			var auditor_name = $('#auditor_name').text();
			var employee_id = $('#employee_id').text();
			var employee_name = $('#employee_name').text();
			var staff_id = $('#staff_id').val();
			var staff_name = $('#staff_name').val();
			var staff_email = $('#staff_email').val();
			var code = $('#code').text();
			var code_number = $('#code_number').text();
			var code_desc = $('#code_desc').text();
			var issued_date = $('#periode').text();
			var expired_date = $('#expired_date').text();

			if (total_ik == '' || 
				ok_ik == '' || 
				ng_ik == '' || 
				ok_ik == '' ||
				q_1 == '' ||
				a_1 == '' ||
				q_2 == '' ||
				a_2 == '' ||
				q_3 == '' ||
				a_3 == '' ||
				q_4 == '' ||
				a_4 == '') {
				openErrorGritter('Error!','Semua nilai harus diisi.');
				$('#loading').hide();
				audio_error.play();
				return false;
			}

			var data = {
				q_1:q_1,
				a_1:a_1,
				q_2:q_2,
				a_2:a_2,
				q_3:q_3,
				a_3:a_3,
				q_4:q_4,
				a_4:a_4,

				total_question:total_question,
				total_answer:total_answer,

				presentase_a:presentase_a,
				presentase_total:presentase_total,

				total_ik:total_ik,
				ok_ik:ok_ik,
				ng_ik:ng_ik,
				presentase_ik:presentase_ik,

				auditor_id:auditor_id,
				auditor_name:auditor_name,
				employee_id:employee_id,
				employee_name:employee_name,
				staff_id:staff_id,
				staff_name:staff_name,
				staff_email:staff_email,
				code:code,
				code_number:code_number,
				code_desc:code_desc,
				issued_date:issued_date,
				expired_date:expired_date,
			}
			$.post('{{ url("input/new/qa/certificate/inprocess") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!',result.message);
					location.replace("{{url('review/qa/certificate/inprocess')}}/"+result.certificate_id+'/Leader QA');
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
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
