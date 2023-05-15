@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	/*text-align:center;*/
}
tbody>tr>td{
	/*text-align:center;*/
}
tfoot>tr>th{
	/*text-align:center;*/
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
  left: -20px;
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

#tableCheck > tbody > tr > td > p > img {
	width: 200px !important;
}

.content-wrapper{
	padding-top: 0px !important;
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
		<input type="hidden" name="feeling_id" id="feeling_id" value="{{$feeling_id}}">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td colspan="4" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Auditor</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;" id="auditor_id">
						{{$emp->employee_id}}
					</td>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;" id="auditor_name">
						{{$emp->name}}
					</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 1%;font-size: 15px">Date</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 1%;font-size: 15px">Gendomihon</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 5%;font-size: 15px">Cara Kensa</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 5%;font-size: 15px">Image Evidence</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;"><input type="text" readonly="" id="date" class="datepicker form-control" style="width: 100%" value="{{date('Y-m-d')}}">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;" id="gendo">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;" id="standard">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: center;" id="evidence">
						<input id="files" type="file" class="form-control" style="width: 100%" accept="image/*" capture="environment">
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Jenis Materi</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Tema</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Materi</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="category"></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="content"></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="materi"></td>
				</tr>
				<tr>
					<td colspan="1" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Metode Cek</td>
					<td colspan="1" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Nama NG</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Area NG</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="metode"></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="ng_name"></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="area"></td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableCheck">
				<thead id="headCheck" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 2%">Emp ID</th>
						<th style="width: 5%">Nama</th>
						<th style="width: 5%">Kehadiran</th>
						<th style="width: 10%">Result</th>
						<th style="width: 10%">Note</th>
					</tr>
				</thead>
				<tbody id="bodyCheck" style="background-color: #f0f0ff;color: black;">
					
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
				<button class="btn btn-danger" onclick="location.reload()" style="width: 100%;font-size: 25px;font-weight: bold;">
					CANCEL
				</button>
			</div>
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 0px;padding-left: 5px">
				<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
					SAVE
				</button>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalMateri">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PILIH TEMA PENYAMAAN FEELING
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label class="col-sm-12" style="text-align: left;">Sumber Tema Penyamaan Feeling</label><br>
							<div class="col-sm-12">
								<div class="col-xs-6" style="text-align: center;">
									<label class="containers">EKSTERNAL KOMPLAIN
									  <input type="radio" name="materi_condition" id="materi_condition" value="EKSTERNAL KOMPLAIN" onclick="checkCondition(this)">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="text-align: center;">
									<label class="containers">NG TERBESAR H-1
									  <input type="radio" name="materi_condition" id="materi_condition" value="NG TERBESAR H-1" onclick="checkCondition(this)">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="text-align: center;">
									<label class="containers">TEMUAN AUDIT LEADER
									  <input type="radio" name="materi_condition" id="materi_condition" value="TEMUAN AUDIT LEADER" onclick="checkCondition(this)">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="text-align: center;">
									<label class="containers">LAIN-LAIN
									  <input type="radio" name="materi_condition" id="materi_condition" value="LAIN-LAIN" onclick="checkCondition(this)">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_complain" align="left">
								<label style="text-align: left;">Pilih Jenis Komplain</label><br>
								<select class="form-control" id="select_complain" style="width: 100%" data-placeholder="Pilih Jenis Komplain" onchange="changeComplain(this.value)">
									<option value=""></option>
									<option value="Market Claim">Market Claim</option>
									<option value="NG Jelas">NG Jelas</option>
									<option value="Temuan Gudang YCJ">Temuan Gudang YCJ</option>
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_market_claim" align="left">
								<label style="text-align: left;">Pilih Komplain</label><br>
								<select class="form-control" id="select_market_claim" style="width: 100%" data-placeholder="Pilih Komplain">
									<option value=""></option>
									<!-- @foreach($claim as $claim)
										<option value="{{$claim->cpar_no}}_{{$claim->tgl_permintaan}}_{{$claim->judul_komplain}}_{{$claim->lokasi}}">{{$claim->cpar_no}} - {{$claim->tgl_permintaan}} - {{$claim->judul_komplain}} - {{$claim->lokasi}}</option>
									@endforeach -->
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_ng_before" align="left">
								<label style="text-align: left;">Pilih NG Trebesar H-1</label><br>
								<select class="form-control" id="select_ng_before" style="width: 100%" data-placeholder="Pilih NG Trebesar H-1">
									<option value=""></option>
									@foreach($ng_before as $ng_before)
										<option value="{{$ng_before->model}}_{{$ng_before->ng_name}}_{{$ng_before->ongko}}_{{$ng_before->qty}}">{{$ng_before->model}} - {{$ng_before->ng_name}} - {{$ng_before->ongko}} - {{$ng_before->qty}}</option>
									@endforeach
									@foreach($ng_before_pianica as $ng_before)
										<option value="{{$ng_before->model}}_{{$ng_before->ng_name}}_{{$ng_before->ongko}}_{{$ng_before->qty}}">{{$ng_before->model}} - {{$ng_before->ng_name}} - {{$ng_before->ongko}} - {{$ng_before->qty}}</option>
									@endforeach
									<?php $index = 1; ?>
									<?php for ($i=0; $i < count($ng_name_rcd); $i++) { ?>
										<?php if ($index < 4) { ?>
											<option value="{{$ng_name_rcd[$i]['model']}}_{{$ng_name_rcd[$i]['ng_name']}}_{{$ng_name_rcd[$i]['ongko']}}_{{$ng_name_rcd[$i]['qty']}}">{{$ng_name_rcd[$i]['model']}} - {{$ng_name_rcd[$i]['ng_name']}} - {{$ng_name_rcd[$i]['ongko']}} - {{$ng_name_rcd[$i]['qty']}}</option>
										<?php } ?>
									<?php $index++;
									 } ?>
									}
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_qa_audit" align="left">
								<label style="text-align: left;">Pilih Temuan Audit QA</label><br>
								<select class="form-control" id="select_qa_audit" style="width: 100%" data-placeholder="Pilih Temuan Audit QA">
									<option value=""></option>
									@foreach($qa_audit as $qa_audit)
										<option value="{{$qa_audit->model}}_{{$qa_audit->ng_name}}_{{$qa_audit->ongko}}_{{$qa_audit->qty}}">{{$qa_audit->model}} - {{$qa_audit->ng_name}} - {{$qa_audit->ongko}} - {{$qa_audit->qty}}</option>
									@endforeach
									@foreach($qa_audit_ei as $qa_audit_ei)
										<option value="{{$qa_audit_ei->model}}_{{$qa_audit_ei->ng_name}}_{{$qa_audit_ei->ongko}}_{{$qa_audit_ei->qty}}">{{$qa_audit_ei->model}} - {{$qa_audit_ei->ng_name}} - {{$qa_audit_ei->ongko}} - {{$qa_audit_ei->qty}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_lain" align="left">
								<label style="text-align: left;">Pilih Sumber NG</label><br>
								<select class="form-control" id="select_criteria" style="width: 100%" data-placeholder="Pilih Sumber NG">
									<option value=""></option>
									<option value="Cek Day FG">Cek Day FG</option>
									<option value="Cek Day KD">Cek Day KD</option>
									<option value="Internal Cek Day QA">Internal Cek Day QA</option>
									<option value="NG Produksi Mouthpiece">NG Produksi Mouthpiece</option>
									<option value="NG Produksi Case">NG Produksi Case</option>
									<option value="Lain-lain">Lain-lain</option>
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_ng_lists" align="left">
								<label style="text-align: left;">Pilih List NG</label><br>
								<select class="form-control" id="select_ng_lists" style="width: 100%" data-placeholder="Pilih List NG">
									<option value=""></option>
									@foreach($ng_lists as $ng_lists)
										<option value="{{$ng_lists->ng_name}}">{{$ng_lists->ng_name}}</option>
									@endforeach
									@foreach($all_ng as $all_ng)
										<option value="{{$all_ng}}">{{$all_ng}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_onko" align="left">
								<label style="text-align: left;">Pilih Area NG</label><br>
								<select class="form-control" id="select_onko" style="width: 100%" data-placeholder="Pilih Area NG">
									<option value=""></option>
									@foreach($onko as $onko)
										<option value="{{$onko->keynomor}}">{{$onko->keynomor}}</option>
									@endforeach
									@foreach($onko_ei as $onko_ei)
										<option value="{{$onko_ei}}">{{$onko_ei}}</option>
									@endforeach
									<option value="A">A</option>
									<option value="B">B</option>
									<option value="C">C</option>
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_materi" align="left">
								<label style="text-align: left;">Pilih Materi</label><br>
								<select class="form-control" id="select_materi" style="width: 100%" data-placeholder="Pilih Materi">
									<option value=""></option>
									<option value="Sample OK">Sample OK</option>
									<option value="Sample NG">Sample NG</option>
									<option value="Limit Sample">Limit Sample</option>
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" id="div_metode" align="left">
								<label style="text-align: left;">Pilih Metode Cek</label><br>
								<select class="form-control" id="select_metode" style="width: 100%" data-placeholder="Pilih Metode Cek">
									<option value=""></option>
									<option value="Fungsi">Fungsi</option>
									<option value="Visual">Visual</option>
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px" align="left">
								<label style="text-align: left;">Metode Penyamaan Feeling</label><br>
								<input type="text" name="add_standard" id="add_standard" placeholder="Metode Penyamaan Feeling" class="form-control" style="width: 100%">
							</div>
							<div class="col-xs-12" style="padding-top: 10px" align="left">
								<label style="text-align: left;">Gendomihon</label><br>
								<input type="text" name="add_gendo" id="add_gendo" placeholder="Gendomihon" class="form-control" style="width: 100%">
								<hr style="border: 2px solid black;margin-bottom: 0px;">
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12" style="padding-top: 10px" id="div_code">
								<label style="text-align: left;">Pilih Kategori Sertifikat Kensa</label><br>
								<select class="form-control" id="select_code" style="width: 100%" data-placeholder="Pilih Karyawan" onchange="changeEmp()" multiple="multiple">
									@foreach($code_certificate as $code)
										@if($code->subject == null)
										<option value="{{$code->certificate_codes}}_{{$code->description}}_{{$code->certificate_name}}">{{$code->certificate_codes}} - {{$code->description}} - {{$code->certificate_name}}</option>
										@else
										<option value="{{$code->certificate_codes}}_{{$code->subject}}_">{{$code->certificate_codes}} - {{$code->subject}}</option>
										@endif
									@endforeach
								</select>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<label style="text-align: left;">List Karyawan</label><br>
								<input type="text" name="tag" id="tag" class="form-control" style="width: 100%;text-align: center;margin-bottom: 10px;" placeholder="Scan ID Card Karyawan Jika Tidak Ada di List">
								<table id="tableEmp" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<th width="1%">#</th>
											<th width="1%">Emp ID</th>
											<th width="10%">Name</th>
										</tr>
									</thead>
									<tbody id="bodyEmp">
										
									</tbody>
								</table>
							</div>

							<div class="col-xs-12" style="padding-top: 10px">
								<label style="text-align: left;">List Karyawan Belum Masuk di List Atas</label><br>
								<table id="tableEmp" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
									<thead style="background-color: rgb(255, 199, 199); color: #000;">
										<tr>
											<th width="1%">#</th>
											<th width="1%">Emp ID</th>
											<th width="10%">Name</th>
										</tr>
									</thead>
									<tbody id="bodyEmpNotIn">
										
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<div class="row">
								<button onclick="saveMateri()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
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
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
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

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

    var count_point = 0;
    var point_check = null;
    var stamp_hierarchy = null;
    var gmc = null;
    var janean = null;
    var upc = null;

	jQuery(document).ready(function() {
		op_alls = [];
		$('#tag').val('');
		$('#tag').focus();
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];

		$('#div_complain').hide();
		$('#div_market_claim').hide();
		$('#div_qa_audit').hide();
		$('#div_ng_before').hide();
		$('#div_ng_lists').hide();
		$('#div_lain').hide();
		$('#div_onko').hide();

		$("#select_code").val([]).trigger('change');
		$("#select_qa_audit").val('').trigger('change');
		$("#select_complain").val('').trigger('change');
		$("#select_market_claim").val('').trigger('change');
		$("#select_ng_before").val('').trigger('change');
		$("#select_ng_lists").val('').trigger('change');
		$("#select_criteria").val('').trigger('change');
		$("#select_onko").val('').trigger('change');
		$("#select_materi").val('').trigger('change');
		$("#select_metode").val('').trigger('change');

		$('#add_standard').val('');
		$('#add_gendo').val('');

		$('#select_complain').select2({
			allowClear:true,
			dropdownParent: $('#div_complain')
		});
		$('#select_market_claim').select2({
			allowClear:true,
			dropdownParent: $('#div_market_claim')
		});
		$('#select_ng_before').select2({
			allowClear:true,
			dropdownParent: $('#div_ng_before')
		});
		$('#select_qa_audit').select2({
			allowClear:true,
			dropdownParent: $('#div_qa_audit')
		});
		$('#select_code').select2({
			allowClear:true,
			dropdownParent: $('#div_code')
		});
		$('#select_ng_lists').select2({
			allowClear:true,
			dropdownParent: $('#div_ng_lists')
		});
		$('#select_criteria').select2({
			allowClear:true,
			dropdownParent: $('#div_lain')
		});
		$('#select_onko').select2({
			allowClear:true,
			dropdownParent: $('#div_onko')
		});

		$('#select_materi').select2({
			allowClear:true,
			dropdownParent: $('#div_materi')
		});

		$('#select_metode').select2({
			allowClear:true,
			dropdownParent: $('#div_metode')
		});

		$('#modalMateri').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

      	$('body').toggleClass("sidebar-collapse");
	    $('#bodyEmp').html('');
	    $('#bodyEmpNotIn').html('');
	    $('#bodyCheck').html('');
	    fetchOpAll();
	});

	$('#modalMateri').on('shown.bs.modal', function () {
	});

	function changeComplain(param) {
		$('#loading').show();
		$('#select_market_claim').html('');
		var claims = '';
		var data = {
			category:param
		}
		$.get('{{ url("fetch/qa/feeling/claim") }}', data, function(result, status, xhr){
			if(result.status){
				if (result.claim != null) {
					for(var i = 0; i < result.claim.length;i++){
						if (param == 'NG Jelas') {
							if (result.claim[i].kategori_komplain == 'NG Jelas') {
								claims += '<option value="'+result.claim[i].cpar_no+'_'+result.claim[i].tgl_permintaan+'_'+result.claim[i].judul_komplain+'_'+result.claim[i].lokasi+'">'+result.claim[i].cpar_no+' - '+result.claim[i].tgl_permintaan+' - '+result.claim[i].judul_komplain+' - '+result.claim[i].lokasi+'</option>';
							}
						}else if (param == 'Temuan Gudang YCJ') {
							if (result.claim[i].kategori_komplain == 'Temuan Gudang YCJ') {
								claims += '<option value="'+result.claim[i].cpar_no+'_'+result.claim[i].tgl_permintaan+'_'+result.claim[i].judul_komplain+'_'+result.claim[i].lokasi+'">'+result.claim[i].cpar_no+' - '+result.claim[i].tgl_permintaan+' - '+result.claim[i].judul_komplain+' - '+result.claim[i].lokasi+'</option>';
							}
						}else{
							claims += '<option value="'+result.claim[i].cpar_no+'_'+result.claim[i].tgl_permintaan+'_'+result.claim[i].judul_komplain+'_'+result.claim[i].lokasi+'">'+result.claim[i].cpar_no+' - '+result.claim[i].tgl_permintaan+' - '+result.claim[i].judul_komplain+' - '+result.claim[i].lokasi+'</option>';
						}
					}
					$('#select_market_claim').append(claims);
				}
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function checkCondition(param) {
		$('#div_market_claim').hide();
		$('#div_complain').hide();
		$('#div_qa_audit').hide();
		$('#div_ng_before').hide();
		$('#div_ng_lists').hide();
		$('#div_lain').hide();
		$('#div_onko').hide();
		if (param.value == 'EKSTERNAL KOMPLAIN') {
			$('#div_complain').show();
			$('#div_market_claim').show();
			$('#div_qa_audit').hide();
			$('#div_ng_before').hide();
			$('#div_ng_lists').hide();
			$('#div_lain').hide();
			$('#div_onko').hide();
		}
		if (param.value == 'NG TERBESAR H-1') {
			$('#div_market_claim').hide();
			$('#div_complain').hide();
			$('#div_qa_audit').hide();
			$('#div_ng_before').show();
			$('#div_ng_lists').hide();
			$('#div_lain').hide();
			$('#div_onko').hide();
		}

		if (param.value == 'TEMUAN AUDIT LEADER') {
			$('#div_market_claim').hide();
			$('#div_complain').hide();
			$('#div_qa_audit').show();
			$('#div_ng_before').hide();
			$('#div_ng_lists').hide();
			$('#div_lain').hide();
			$('#div_onko').hide();
		}

		if (param.value == 'LAIN-LAIN') {
			$('#div_market_claim').hide();
			$('#div_complain').hide();
			$('#div_qa_audit').hide();
			$('#div_ng_before').hide();
			$('#div_ng_lists').show();
			$('#div_lain').show();
			$('#div_onko').show();
		}
	}

	var emp = <?php echo json_encode($certificate); ?>;
	var claim = JSON.parse('<?php echo JSON_encode($claim);?>');
	var op_all = JSON.parse('<?php echo JSON_encode($operator_all);?>');
	var emps = [];
	var empss = [];
	var index = 1;

	var op_alls = [];
	var op_alls2 = [];

	function fetchOpAll() {
		$('#bodyEmpNotIn').html('');
		var body = '';

		indexes = 1;

		if (op_all != null) {
			for(var j = 0; j < op_all.length;j++){
				body += '<tr id="'+op_all[j].employee_id+'">';
				body += '<td style="border:1px solid black;text-align:center;">'+indexes+'</td>';
				body += '<td style="border:1px solid black;">'+op_all[j].employee_id+'</td>';
				body += '<td style="border:1px solid black;">'+op_all[j].name+'</td>';
				body += '</tr>';
				indexes++;
				op_alls.push(op_all[j].employee_id);
				op_alls2.push({
					employee_id:op_all[j].employee_id,
					name:op_all[j].name,
				});
			}
		}

		$('#bodyEmpNotIn').append(body);
	}

	function changeEmp() {
		emps = [];
		empss = [];
		var codes = $('#select_code').val();
		for(var i = 0; i < emp.length;i++){
			for(var j = 0; j < codes.length;j++){
				if (codes[j].split('_')[2] == '') {
					var re = new RegExp(codes[j].split('_')[1], 'g');
					if (emp[i].certificate_code == codes[j].split('_')[0] && emp[i].subject.match(re)) {
						if (!empss.includes(emp[i].employee_id)) {
							empss.push(emp[i].employee_id);
							emps.push({
								employee_id:emp[i].employee_id,
								name:emp[i].name,
							});
						}
					}
				}else{
					var re = new RegExp(emp[i].certificate_desc, 'g');
					var re2 = new RegExp(emp[i].certificate_name, 'g');
					if (emp[i].certificate_code == codes[j].split('_')[0] && codes[j].split('_')[1].match(re) && codes[j].split('_')[2].match(re2)) {
						if (!empss.includes(emp[i].employee_id)) {
							empss.push(emp[i].employee_id);
							emps.push({
								employee_id:emp[i].employee_id,
								name:emp[i].name,
							});
						}
					}
				}
			}
		}
		$('#bodyEmp').html('');
		var body = '';

		index = 1;

		op_all = JSON.parse('<?php echo JSON_encode($operator_all);?>');

		for(var j = 0; j < emps.length;j++){
			body += '<tr>';
			body += '<td style="border:1px solid black;text-align:center;">'+index+'</td>';
			body += '<td style="border:1px solid black;">'+emps[j].employee_id+'</td>';
			body += '<td style="border:1px solid black;">'+emps[j].name+'</td>';
			body += '</tr>';
			index++;
		}

		for(var j = 0; j < empss.length;j++){
			op_all = op_all.filter(emp => emp.employee_id != empss[j]);
		}

		fetchOpAll();

		$('#bodyEmp').append(body);
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#tag').val().length > 7) {
				$('#loading').show();
				var data = {
					tag:$('#tag').val()
				}

				$.get('{{ url("fetch/qa/feeling/employee") }}', data, function(result, status, xhr){
					if(result.status){
						if (!empss.includes(result.emp.employee_id)) {
							empss.push(result.emp.employee_id);
							emps.push({
								employee_id:result.emp.employee_id,
								name:result.emp.name,
							});

							var body = '';
							body += '<tr>';
							body += '<td style="border:1px solid black;text-align:center;">'+index+'</td>';
							body += '<td style="border:1px solid black;">'+result.emp.employee_id+'</td>';
							body += '<td style="border:1px solid black;">'+result.emp.name+'</td>';
							body += '</tr>';
							index++;

							op_all = op_all.filter(emp => emp.employee_id != result.emp.employee_id);

							fetchOpAll();

							$('#bodyEmp').append(body);

							$('#tag').val('');
							$('#tag').focus();

							openSuccessGritter('Success','Scan Success');
							$('#loading').hide();
						}else{
							$('#tag').val('');
							$('#tag').focus();

							audio_error.play();
							openErrorGritter('Error!','Karyawan Sudah Ada di List');
							$('#loading').hide();
							return false;
						}
					}else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error!',result.message);
						return false;
					}
				});
			}
		}
	});

	function cancelAll() {
		location.reload();
	}

    const monthNames = ["January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];

	function saveMateri() {
		$('#loading').show();
		var jenis_materi = '';
		$("input[name='materi_condition']:checked").each(function (i) {
            jenis_materi = $(this).val();
        });
		if (emps.length == 0 || jenis_materi == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Karyawan dan Jenis Materi');
			return false;
		}

		if ($('#add_standard').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Isi Cara Kensa');
			return false;
		}

		var materi = '';

		if (jenis_materi == 'EKSTERNAL KOMPLAIN') {
			var materi = $('#select_market_claim').val();
			$('#content').html(materi.split('_')[0]+' - '+materi.split('_')[1]);
			$('#ng_name').html(materi.split('_')[2]);
			$('#area').html(materi.split('_')[3]);
		}
		if (jenis_materi == 'NG TERBESAR H-1') {
			var materi = $('#select_ng_before').val();
			$('#content').html(materi.split('_')[0]);
			$('#ng_name').html(materi.split('_')[1]);
			$('#area').html(materi.split('_')[2]);
		}
		if (jenis_materi == 'TEMUAN AUDIT LEADER') {
			var materi = $('#select_qa_audit').val();
			$('#content').html(materi.split('_')[0]);
			$('#ng_name').html(materi.split('_')[1]);
			$('#area').html(materi.split('_')[2]);
		}
		if (jenis_materi == 'LAIN-LAIN') {
			var materi = $('#select_ng_lists').val();
			var onko = $('#select_onko').val();
			$('#content').html($('#select_criteria').val());
			$('#ng_name').html(materi);
			$('#area').html(onko);
		}

		if (materi == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Tema');
			return false;
		}

		if ($('#select_materi').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Materi');
			return false;
		}

		if ($('#select_metode').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Metode');
			return false;
		}

		$('#materi').html($('#select_materi').val());
		$('#metode').html($('#select_metode').val());
		$('#category').html(jenis_materi);
		$('#gendo').html($('#add_gendo').val());
		$('#standard').html($('#add_standard').val());
		$('#modalMateri').modal('hide');

		$('#bodyCheck').html('');
		var bodyCheck = '';

		for(var i = 0; i < emps.length;i++){
			bodyCheck += '<tr>';
			bodyCheck += '<td style="border: 1px solid black;text-align: right;">'+(i+1)+'</td>';
			bodyCheck += '<td style="border: 1px solid black;text-align: left;">'+emps[i].employee_id+'</td>';
			bodyCheck += '<td style="border: 1px solid black;text-align: left;">'+emps[i].name+'</td>';
			bodyCheck += '<td style="border: 1px solid black;text-align: left;">';
			bodyCheck += '<select style="width:100%" class="form-control" id="select_attendance_'+i+'" onchange="changeAttendance(\''+i+'\',this.value)">';
			bodyCheck += '<option value="Hadir">Hadir</option>';
			bodyCheck += '<option value="Tidak Perlu">Tidak Perlu</option>';
			bodyCheck += '<option value="Tidak Hadir">Tidak Hadir</option>';
			bodyCheck += "<option value='CUTI'>CUTI</option>";
			// bodyCheck += '<option value="CK">CK</option>';
			bodyCheck += "<option value='SAKIT'>SAKIT</option>";
			bodyCheck += "<option value='Izin'>Izin</option>";
			// bodyCheck += "<option value='Mangkir'>Mangkir</option>";
			// bodyCheck += "<option value='ABS'>ABS</option>";
			// bodyCheck += "<option value='IMP'>IMP</option>";
			// bodyCheck += "<option value='TELAT'>TELAT</option>";
			bodyCheck += '</select>';
			bodyCheck += '</td>';
			bodyCheck += '<td style="border: 1px solid black;text-align: center;padding-top: 12px;">';
			bodyCheck += '<div class="col-xs-6" id="div_condition_1_'+i+'">';
				bodyCheck += '<label class="containers"><span class="text-green">&#9711;</span>';
				  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
				  bodyCheck += '<span class="checkmark"></span>';
				bodyCheck += '</label>';
			bodyCheck += '</div>';
			bodyCheck += '<div class="col-xs-6" id="div_condition_2_'+i+'">';
				bodyCheck += '<label class="containers"><span class="text-red">&#9747;</span>';
				  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
				  bodyCheck += '<span class="checkmark"></span>';
				bodyCheck += '</label>';
			bodyCheck += '</div>';
			bodyCheck += '</td>';
			bodyCheck += '<td style="border: 1px solid black;"><div id="div_note_'+i+'"><textarea id="note_'+i+'"></textarea></div></td>';
			bodyCheck += '</tr>';
		}

		$('#bodyCheck').append(bodyCheck);

		for(var i = 0; i < emps.length;i++){
			$('#select_attendance_'+i).select2({
				allowClear:true,
			});

			CKEDITOR.replace('note_'+i ,{
		        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
		        height: '100px',
		        toolbar:'MA'
		    });
		}

		$('#loading').hide();
	}

	function changeAttendance(index,param) {
		if (param != 'Hadir') {
			$('#div_condition_1_'+index).hide();
			$('#div_condition_2_'+index).hide();
			$('#div_note_'+index).hide();
			$("input[name=condition_"+index+"][value=OK]").prop('checked', true);
		}else{
			$('#div_condition_1_'+index).show();
			$('#div_condition_2_'+index).show();
			$('#div_note_'+index).show();
			$("input[name=condition_"+index+"]").prop('checked', false);
		}
	}

	function confirmAll() {
		var statuses = 0;
		var ngs = 0;
		var notes = 0;
		for(var i = 0; i < emps.length;i++){
			var note = CKEDITOR.instances['note_'+i].getData();
			if ($('#select_attendance_'+i).val() == 'Hadir') {
				var decision_input = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
					decision_input = $(this).val();
		        });
		        if (decision_input == '') {
		        	statuses++;
		        }

		        if (decision_input == 'NG') {
		        	ngs++;
		        	if (note == '') {
		        		notes++;
		        	}
		        }
			}
		}
		// if ($('#files').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
		// 	$('#loading').hide();
		// 	openErrorGritter('Error!','Upload Foto Evidence');
		// 	return false;
		// }

		if (statuses > 0) {
			openErrorGritter('Error!','Semua Hasil Harus Diisi.');
			return false;
		}
		if (ngs > 0 && notes > 0) {
			openErrorGritter('Error!','Ada item yang NG. Pastikan itu benar NG dan Note terisi.');
			return false;
		}
		var kata_confirm = 'Apakah Anda ingin menyelesaikan Penyamaan Feeling?';
		if (confirm(kata_confirm)) {
			$('#loading').show();
			var stat = 0;
			var hasils = [];
			for(var i = 0; i < emps.length;i++){
				if ($('#select_attendance_'+i).val() != 'Tidak Perlu') {
					var category = $('#category').text();
					var content = $('#content').text();
					var ng_name = $('#ng_name').text();
					var area = $('#area').text();
					var gendo = $('#gendo').text();
					var date = $('#date').val();
					var materi = $('#materi').text();
					var metode = $('#metode').text();
					var standard = $('#standard').text();
					var auditor_id = $('#auditor_id').text();
					var auditor_name = $('#auditor_name').text();
					var feeling_id = $('#feeling_id').val();

					var answer = '';
					$("input[name='condition_"+i+"']:checked").each(function (i) {
						answer = $(this).val();
			        });

			        auditee_id = emps[i].employee_id;
			        auditee_name = emps[i].name;

			        auditee_status = $('#select_attendance_'+i).val();

					var note = CKEDITOR.instances['note_'+i].getData();

					var file = '';

					var fileData = null;

					fileData = $('#files').prop('files')[0];

					file=$('#files').val().replace(/C:\\fakepath\\/i, '').split(".");				

					var formData = new FormData();
					formData.append('category',category);
					formData.append('content',content);
					formData.append('index',i);
					formData.append('ng_name',ng_name);
					formData.append('area',area);
					formData.append('gendo',gendo);
					formData.append('materi',materi);
					formData.append('date',date);
					formData.append('metode',metode);
					formData.append('feeling_id',feeling_id);
					formData.append('standard',standard);
					formData.append('auditor_id',auditor_id);
					formData.append('auditor_name',auditor_name);
					formData.append('auditee_id',auditee_id);
					formData.append('auditee_name',auditee_name);
					formData.append('auditee_status',auditee_status);
					formData.append('fileData', fileData);
					formData.append('answer',answer);
					formData.append('note',note);
					formData.append('extension', file[1]);
					formData.append('foto_name', file[0]);

					$.ajax({
						url:"{{ url('input/qa/feeling/audit') }}",
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
								if (stat == emps.length) {
									openSuccessGritter('Success!',"Penyamaan Feeling Berhasil Disimpan");
									$('#loading').hide();
									alert('Penyamaan Feeling Telah Dilaksanakan');
									window.location.replace("{{url('index/qa/feeling')}}");
								}
							}else{
								openErrorGritter('Error!',data.message);
								audio_error.play();
								$('#loading').hide();
							}

						}
					});
				}
			}
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

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}
</script>
@endsection