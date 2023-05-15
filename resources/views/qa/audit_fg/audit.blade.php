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

input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
	<input type="hidden" name="audit_id" id="audit_id">
	<div class="row">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Auditor</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Qty Per Auditor</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Auditee</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 15px;text-align: left;">
						<select class="form-control" id="select_auditor" style="width: 100%;" data-placeholder="Pilih Auditor" multiple="multiple" onchange="changeAuditor()">
							<option value=""></option>
							@foreach($auditor as $auditor)
							<option value="{{$auditor->employee_id}}">{{$auditor->employee_id}} - {{$auditor->name}}</option>
							@endforeach
						</select>
						<input type="hidden" name="auditor" id="auditor">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 15px;text-align: left;">
						<select class="form-control" id="select_qty_auditor" style="width: 100%;" data-placeholder="Ketik Quantity Per Auditor" multiple="multiple" onchange="changeQtyPerAuditor()">
							<option value=""></option>
						</select>
						<input type="hidden" name="qty_auditor" id="qty_auditor">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 15px;text-align: left;">
						<select class="form-control" id="auditee" style="width: 100%;" data-placeholder="Pilih Auditee">
							<option value=""></option>
							@foreach($emp_all as $emp_all)
							<option value="{{$emp_all->employee_id}}_{{$emp_all->name}}">{{$emp_all->employee_id}} - {{$emp_all->name}}</option>
							@endforeach
						</select>
					</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Sesi</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Qty Lot</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Qty Cek (Sampling)</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<input type="number" name="session" id="session" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;" placeholder="Sesi">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" >
						<input type="number" name="qty_lot" id="qty_lot" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;" placeholder="Qty Lot">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" >
						<input type="number" name="qty_check" id="qty_check" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;" placeholder="Qty Check" onchange="checkQty()">
					</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Critical (AQL : 0%)</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Major (AQL : 2.5%)</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Minor (AQL : 4.0%)</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="critical_acc" id="critical_acc" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Acc">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="critical_re" id="critical_re" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Re">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="critical_qty" id="critical_qty" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Qty" value="0">
						</div>
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="major_acc" id="major_acc" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Acc">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="major_re" id="major_re" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Re">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="major_qty" id="major_qty" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Qty" value="0">
						</div>
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="minor_acc" id="minor_acc" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Acc">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="minor_re" id="minor_re" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Re">
						</div>
						<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
							<input type="number" name="minor_qty" id="minor_qty" class="form-control numpad" style="width: 100%;font-size: 20px;text-align: center;"  readonly placeholder="Qty" value="0">
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Tanggal</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Product</td>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Material</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="date">
						{{date('Y-m-d')}}
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 20px" id="product"></td>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 15px" id="material2"></td>
					<span id="material" style="display: none"></span>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px;">Qty NG</td>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Material Sedang Audit</td>
				</tr>
				<tr>
					<td style="background-color: #ffaca6;color:#000;border:2px solid rgb(60, 60, 60);font-size: 20px" id="total_ng">0</td>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;" >
						<select class="form-control" id="material_audited" style="width: 100%;font-size: 18px" data-placeholder="Pilih Material Sedang Audit">
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Cav Head</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Cav Middle</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Cav Foot</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;" >
						<select class="form-control" id="select_cav_head" style="width: 100%;font-size: 18px" data-placeholder="Pilih Cavity Head" multiple="multiple" onchange="changeCav('head')">
							<option value=""></option>
						</select>
						<input type="hidden" name="cav_head" id="cav_head">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;" >
						<select class="form-control" id="select_cav_middle" style="width: 100%;font-size: 18px" data-placeholder="Pilih Cavity Middle" multiple="multiple" onchange="changeCav('middle')">
							<option value=""></option>
						</select>
						<input type="hidden" name="cav_middle" id="cav_middle">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;" >
						<select class="form-control" id="select_cav_foot" style="width: 100%;font-size: 18px" data-placeholder="Pilih Cavity Foot" multiple="multiple" onchange="changeCav('foot')">
							<option value=""></option>
						</select>
						<input type="hidden" name="cav_foot" id="cav_foot">
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<div class="col-xs-12" style="background-color: crimson;text-align: center;" id="divLotOut">
				<span style="font-weight: bold;font-size: 50px;padding:15px;color: yellow;">LOT OUT</span>
			</div>
			<input type="hidden" name="status_lot" id="status_lot">
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableCheck">
				<thead id="headCheck" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 2%">Urutan</th>
						<th style="width: 5%">Point Check</th>
						<th style="width: 5%">Standard</th>
						<th style="width: 5%">Def. Cay.</th>
						<th style="width: 5%">Qty NG</th>
						<th style="width: 5%">NG Detail</th>
						<th style="width: 10%">Evidence & Note</th>
					</tr>
				</thead>
				<tbody id="bodyCheck" style="background-color: #f0f0ff;color: black;">
					
				</tbody>
			</table>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableBox">
				<thead id="headBox" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 2%">Qty Box</th>
						<th style="width: 5%">PIC Kensa</th>
						<th style="width: 1%">Action</th>
					</tr>
				</thead>
				<input type="hidden" name="count_box" id="count_box" value="1">
				<tbody id="bodyBox" style="background-color: #f0f0ff;color: black;">
					<tr id="box_tr_1">
						<td style="border: 1px solid black;text-align: left;">
							<input type="number" name="box_qty_1" id="box_qty_1" class="form-control numpad" placeholder="Input Qty Box" style="width: 100%">
						</td>
						<td style="border: 1px solid black;">
							<select class="form-control select2" id="box_pic_1" name="box_pic_1" data-placeholder="Pilih PIC Kensa" style="width: 100%">
								<option value=""></option>
								@foreach($emp_groups as $emp_groups)
								<option value="{{$emp_groups->group}}_{{$emp_groups->employee_id}}_{{$emp_groups->name}}">{{$emp_groups->group}} - {{$emp_groups->employee_id}} - {{$emp_groups->name}}</option>
								@endforeach
							</select>
						</td>
						<td style="border: 1px solid black;text-align: center;">
							<button class="btn btn-success btn-sm" onclick="addBox()"><i class="fa fa-plus"></i></button>
							<button class="btn btn-danger btn-sm" onclick="deleteBox(1)"><i class="fa fa-minus"></i></button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
				<a class="btn btn-danger" href="{{url('index/qa/special_process')}}" style="width: 100%;font-size: 25px;font-weight: bold;">
					CANCEL
				</a>
			</div>
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 0px;padding-left: 5px">
				<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
					SAVE
				</button>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalProduct">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PILIH POINT CHECK
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div class="col-xs-12">
						<div class="row">
							<!-- <div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Point Check</span></center>
							</div> -->
							<div class="col-xs-12" style="padding-top: 10px">
								<select class="form-control select2" id="select_point_check" style="width: 100%" data-placeholder="Pilih Material" onchange="">
									<option value=""></option>
									@foreach($point as $point)
									<option value="{{$point->product}}_{{$point->material_number}}_{{$point->material_description}}">
										@if(str_contains($point->material_number,','))
										<?php $material_number = explode(',', $point->material_number);
										$material_description = explode(',', $point->material_description);
										$material = [];
										for ($i=0; $i < count($material_number); $i++) { 
											array_push($material, $material_number[$i].' - '.$material_description[$i]);
										}
										echo join(',',$material);
										?>
										@else
										{{$point->material_number}} - {{$point->material_description}}
										@endif
									</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<div class="row">
								<button onclick="saveProduct()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
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
		cancelAll();
		$('#divLotOut').hide();
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];

		$('.select2').select2({
			allowClear:true,
			ropdownParent: $('#modalProduct')
		});

		$('#auditee').select2({
			allowClear:true,
		});

		$('#select_auditor').select2({
		});

		$('#select_box_pic').select2({
		});

		$('#select_qty_auditor').select2({
			tags: true,
		});

		$("#select_qty_auditor").on("select2:select",function(e){	
			e.preventDefault();
			let limite_periodos        = $("#select_auditor").val().length;
			var element                = e.params.data.element;
			var $element               = $(element);
			$element.detach();
			$(this).append($element);
			$(this).trigger("change");				
		   	$("#select_qty_auditor").append('<option value="'+e.params.data.text+'">' +e.params.data.text + '</option>');
			$('#select_qty_auditor').trigger('select2:close');
			return true;
		});	
		$('#select_qty_auditor').on('select2:unselect',function(event){
		    var detect                 = false;
			var element                = event.params.data.text;			
			var selections             = $('#select_qty_auditor').select2('data');
			var el                     = event.params.data.element;
			var $el                    = $(el);
			$el.detach();
		});	
		$('#select_qty_auditor').on('select2:close',function(event){	
			var select = document.getElementById("select_qty_auditor");
			var options = [];			
			document.querySelectorAll('#select_qty_auditor > option').forEach(
			  option => options.push(option)
			);			
			while (select.firstChild) {
				select.removeChild(select.firstChild);
			}	
			// options.sort((a, b) => parseInt(a.innerText)-parseInt(b.innerText));		
			for (var i in options) {
				select.appendChild(options[i]);
			}			
		});

		$('#material_audited').select2({
			allowClear:true,
		});

		$("#select_point_check").val('').trigger('change');
		$("#material_audited").val('').trigger('change');
		$("#auditee").val('').trigger('change');

		$('#material_audited').html('');

		$('#serial_number').val('');

		$('#modalProduct').modal({
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
		$('#select_emp').select2({
			allowClear:true
		});
		$("#select_area").val('').trigger('change');

		$('#select_cav_head').select2({
			allowClear:true
		});
		$('#select_cav_middle').select2({
			allowClear:true
		});
		$('#select_cav_foot').select2({
			allowClear:true
		});

      	$('body').toggleClass("sidebar-collapse");
	    count_point = 0;
	    point_check = null;
	    gmc = null;
	    janean = null;
	    upc = null;
	    stamp_hierarchy = null;
	    $('#bodyCheck').html('');
	});
	var emp_groups = <?php echo json_encode($emp_groups2); ?>;

	function addBox() {
		var id = parseInt($('#count_box').val())+1;
		var box = '';
		box += '<tr id="box_tr_'+id+'">';
		box += '<td style="border: 1px solid black;text-align: left;">';
		box += '<input type="number" name="box_qty_'+id+'" id="box_qty_'+id+'" class="form-control numpad2" placeholder="Input Qty Box" style="width: 100%">';
		box += '</td>';
		box += '<td style="border: 1px solid black;">';
		box += '<select class="form-control" id="box_pic_'+id+'" name="box_pic_'+id+'" data-placeholder="Pilih PIC Kensa" style="width: 100%">';
		box += '<option value=""></option>';
		for(var i = 0; i < emp_groups.length;i++){
			box += '<option value="'+emp_groups[i].group+'_'+emp_groups[i].employee_id+'_'+emp_groups[i].name+'">'+emp_groups[i].group+' - '+emp_groups[i].employee_id+' - '+emp_groups[i].name+'</option>';
		}
		box += '</select>';
		box += '</td>';
		box += '<td style="border: 1px solid black;text-align: center;">';
		box += '<button class="btn btn-success btn-sm" onclick="addBox()"><i class="fa fa-plus"></i></button>';
		box += '<button class="btn btn-danger btn-sm" onclick="deleteBox(\''+id+'\')"><i class="fa fa-minus"></i></button>';
		box += '</td>';
		box += '</tr>';
		$('#bodyBox').append(box);

		$('#box_pic_'+id).select2({
			allowClear:true,
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('#count_box').val(id);
	}

	function deleteBox(id) {
		$("#box_tr_"+id).remove();
	}

	function changeCav(param) {
		$('#cav_'+param).val($('#select_cav_'+param).val());
	}

	function changeAuditor() {
		$('#auditor').val($('#select_auditor').val());
	}

	function changeQtyPerAuditor() {
		$('#qty_auditor').val($('#select_qty_auditor').val());
	}

	var inspection_level = JSON.parse('<?php echo JSON_encode($inspection_level);?>');
	var cavity = JSON.parse('<?php echo JSON_encode($cavity);?>');

	var qty_critical = 0;
	var qty_major = 0;
	var qty_minor = 0;

	function checkQty() {
		var qty_check = $('#qty_check').val();
		var aql0_acc,alq0_re;
		var aql2_acc,alq2_re;
		var aql4acc,alq4_re;
		for(var i = 0; i < inspection_level.length;i++){
			if (parseInt(qty_check) >= parseInt(inspection_level[i].lower) && parseInt(qty_check) <= parseInt(inspection_level[i].upper)) {
				if (inspection_level[i].aql == '0') {
					aql0_acc = inspection_level[i].acc;
					aql0_re = inspection_level[i].re;
				}
				if (inspection_level[i].aql == '2.5') {
					aql2_acc = inspection_level[i].acc;
					aql2_re = inspection_level[i].re;
				}
				if (inspection_level[i].aql == '4') {
					aql4_acc = inspection_level[i].acc;
					aql4_re = inspection_level[i].re;
				}
			}
		}
		$("#critical_acc").val(aql0_acc);
		$("#critical_re").val(aql0_re);

		$("#major_acc").val(aql2_acc);
		$("#major_re").val(aql2_re);

		$("#minor_acc").val(aql4_acc);
		$("#minor_re").val(aql4_re);
	}

	$('#modalProduct').on('shown.bs.modal', function () {
	});

	function cancelAll() {
		$('#box_qty_1').val('');
		$('#box_pic_1').val('').trigger('change');
		$('#audit_id').val('');
		$('#divLotOut').hide();
		$('#status_lot').val('LOT OK');
		$('#auditor').val('');
		$("#select_auditor").val([]).trigger('change');
		$('#qty_auditor').val('');
		$("#select_qty_auditor").val([]).trigger('change');
		$('#session').val('');
		$('#qty_check').val('');
		$('#qty_lot').val('');
		$('#critical_re').val('');
		$('#critical_acc').val('');
		$('#critical_qty').val('0');
		$('#major_re').val('');
		$('#major_acc').val('');
		$('#major_qty').val('0');
		$('#minor_re').val('');
		$('#minor_acc').val('');
		$('#minor_qty').val('0');
		$('#total_ng').val('0');
		$('#material_audited').val('').trigger('change');
		$('#select_cav_head').val([]).trigger('change');
		$('#cav_head').val('');
		$('#select_cav_middle').val([]).trigger('change');
		$('#cav_middle').val('');
		$('#select_cav_foot').val([]).trigger('change');
		$('#cav_foot').val('');
	}

    const monthNames = ["January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];

	function saveProduct() {
		if ($('#select_point_check').val() == '') {
			openErrorGritter('Error!','Pilih Point Check');
			return false;
		}
		$('#loading').show();
		var data = {
			material_number:$('#select_point_check').val().split('_')[1]
		}

		$.get('{{ url("fetch/qa/audit_fg/point_check") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyCheck').html('');
				var bodyCheck = '';

				for(var i = 0; i < result.point_check.length;i++){
					bodyCheck += '<tr>';
					bodyCheck += '<td style="border: 1px solid black;text-align: right;">'+(i+1);
						bodyCheck += '<input type="hidden" name="point_id_'+i+'" id="point_id_'+i+'" value="'+result.point_check[i].id+'">';
						bodyCheck += '<input type="hidden" name="ordering_'+i+'" id="ordering_'+i+'" value="'+result.point_check[i].ordering+'">';
					bodyCheck += '</td>';
					bodyCheck += '<td id="point_check_'+i+'" style="border: 1px solid black;">'+result.point_check[i].point_check+'</td>';
					bodyCheck += '<td id="point_check_details_'+i+'" style="border: 1px solid black;">'+result.point_check[i].point_check_details+'</td>';
					bodyCheck += '<td id="standard_'+i+'" style="border: 1px solid black;">'+result.point_check[i].standard+'</td>';
					bodyCheck += '<td id="defect_category_'+i+'" style="border: 1px solid black;">'+result.point_check[i].defect_category+'</td>';
					bodyCheck += '<td style="border: 1px solid black;padding:0px;width:100px;">';
					bodyCheck += '<div class="col-xs-4" id="minus" onclick="minus('+i+')" class="unselectable" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;text-align:center">-</div>';
					bodyCheck += '<div class="col-xs-4" style="padding:0px;text-align:center">';
					bodyCheck += '<input type="text" class="form-control" id="result_check_'+i+'" style="font-size: 35px;height:64px;width:100%;text-align:center;font-weight:bold;background-color:rgb(100,100,100);color:yellow;" value="0" placeholder="Input Result">';
					bodyCheck += '</div>';
					bodyCheck += '<div class="col-xs-4" id="plus" onclick="plus('+i+')" class="unselectable" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;text-align:center">+</div>';
					bodyCheck += '</td>';
					bodyCheck += '<td style="border: 1px solid black;">';
					bodyCheck += '<div id="div_ng_detail_'+i+'" style="display:none">';
					bodyCheck += '<label>Nama NG</label>';
					bodyCheck += '<select class="form-control" style="width:100%;" id="ng_name_'+i+'" name="ng_name_'+i+'" data-placeholder="Pilih Nama NG">';
					var ng_list = null;
					if (result.point_check[i].product == 'Pianica') {
						ng_list = result.ng_list_pianica;
					}else{
						ng_list = result.ng_list_recorder;
					}
					bodyCheck += '<option value=""></option>';
					for(var j = 0;j< ng_list.length;j++){
						bodyCheck += '<option value="'+ng_list[j].ng_name+'">'+ng_list[j].ng_name+'</option>';
					}
					bodyCheck += '</select>';
					bodyCheck += '<label>Area</label>';
					bodyCheck += '<select class="form-control" style="width:100%;" id="area_'+i+'" name="area_'+i+'" data-placeholder="Pilih Area">';
					bodyCheck += '<option value=""></option>';
					bodyCheck += '<option value="A">A</option>';
					bodyCheck += '<option value="B">B</option>';
					bodyCheck += '<option value="C">C</option>';
					bodyCheck += '<option value="D">D</option>';
					bodyCheck += '<option value="E">E</option>';
					bodyCheck += '</select>';
					if (result.point_check[i].product == 'Recorder') {
						bodyCheck += '<label>Cavity</label>';
						bodyCheck += '<select class="form-control" style="width:100%;" id="cav_'+i+'" name="cav_'+i+'" data-placeholder="Pilih Cavity NG Produksi">';
						bodyCheck += '<option value=""></option>';
						bodyCheck += '<option value="1">1</option>';
						bodyCheck += '<option value="2">2</option>';
						bodyCheck += '<option value="3">3</option>';
						bodyCheck += '<option value="4">4</option>';
						bodyCheck += '<option value="5">5</option>';
						bodyCheck += '<option value="6">6</option>';
						bodyCheck += '<option value="7">7</option>';
						bodyCheck += '<option value="8">8</option>';
						bodyCheck += '<option value="9">9</option>';
						bodyCheck += '<option value="10">10</option>';
						bodyCheck += '<option value="11">11</option>';
						bodyCheck += '<option value="12">12</option>';
						bodyCheck += '<option value="13">13</option>';
						bodyCheck += '<option value="14">14</option>';
						bodyCheck += '<option value="15">15</option>';
						bodyCheck += '<option value="16">16</option>';
						bodyCheck += '<option value="17">17</option>';
						bodyCheck += '<option value="18">18</option>';
						bodyCheck += '<option value="19">19</option>';
						bodyCheck += '<option value="20">20</option>';
						bodyCheck += '<option value="21">21</option>';
						bodyCheck += '<option value="22">22</option>';
						bodyCheck += '<option value="23">23</option>';
						bodyCheck += '<option value="24">24</option>';
						bodyCheck += '</select>';
					}
					bodyCheck += '<label>Pallet</label>';
					bodyCheck += '<input class="form-control numpad2" style="width:100%;" placeholder="Input Pallet" id="pallet_'+i+'">';
					bodyCheck += '<label>Baris</label>';
					bodyCheck += '<input class="form-control numpad2" style="width:100%;" placeholder="Input Baris" id="row_'+i+'">';
					bodyCheck += '<label>Box Ke-</label>';
					bodyCheck += '<input class="form-control numpad2" style="width:100%;" placeholder="Input Box Ke-" id="box_'+i+'">';
					bodyCheck += '<label>Line</label>';
					bodyCheck += '<input class="form-control numpad2" style="width:100%;" placeholder="Input Line" id="line_'+i+'">';
					bodyCheck += '<label>Emp Produksi</label>';
					bodyCheck += '<select class="form-control" style="width:100%;" id="emp_'+i+'" name="emp_'+i+'" data-placeholder="Pilih Emp Produksi">';
					bodyCheck += '<option value=""></option>';
					for(var j = 0;j< result.emp.length;j++){
						bodyCheck += '<option value="'+result.emp[j].employee_id+'_'+result.emp[j].name+'">'+result.emp[j].employee_id+' - '+result.emp[j].name+'</option>';
					}
					bodyCheck += '</select>';
					bodyCheck += '</div>';
					bodyCheck += '</td>';
					bodyCheck += '<td style="border: 1px solid black;"><textarea id="note_'+i+'"></textarea></td>';
					bodyCheck += '</tr>';
				}

				$('#bodyCheck').append(bodyCheck);

				// stamp_hierarchy = result.stamp_hierarchy;

				$('.numpad2').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				for(var i = 0; i < result.point_check.length;i++){
					CKEDITOR.replace('note_'+i ,{
				        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
				        height: '100px',
				        toolbar:'MA'
				    });

				    $('#ng_name_'+i).select2({
						allowClear:true
					});

					$('#area_'+i).select2({
						allowClear:true
					});
					$('#emp_'+i).select2({
						allowClear:true
					});
					if (result.point_check[i].product == 'Recorder') {
						$('#cav_'+i).select2({
							allowClear:true
						});
					}
				}
				$('#product').html($('#select_point_check').val().split('_')[0]);
				var sel = document.getElementById("select_point_check");
				var text= sel.options[sel.selectedIndex].text;
				if (text.match(/,/gi)) {
					$('#material2').html(text.split(',')[0]+','+text.split(',')[0]+'...');
				}else{
					$('#material2').html(text);
				}
				$('#material').html(text);
				var materials = text.split(',');
				$('#material_audited').html('');
				var material_audited = '';
				var material_description = '';
				material_audited += '<option value=""></option>';
				for(var i = 0; i < materials.length;i++){
					material_audited += '<option value="'+materials[i]+'">'+materials[i]+'</option>';
					material_description += materials[i].split(' - ')[1];
				}
				$('#material_audited').append(material_audited);
				point_check = result.point_check;

				$('#audit_id').val(result.audit_id);

				$('#select_cav_head').html('');
				$('#select_cav_middle').html('');
				$('#select_cav_foot').html('');

				var cav_head = '';
				var cav_middle = '';
				var cav_foot = '';

				if (material_description.match(/YRS/gi) || material_description.match(/YRF/gi)) {
					cav_head += '<option value=""></option>';
					cav_middle += '<option value=""></option>';
					cav_foot += '<option value=""></option>';

					for(var i = 0; i < cavity.length;i++){
						var cavs = cavity[i].cavity.split(',');
						for(var j = 0; j < cavs.length;j++){
							if (cavity[i].type == 'head') {
								cav_head += '<option value="'+cavs[j]+'">'+cavs[j]+'</option>';
							}
							if (cavity[i].type == 'middle') {
								cav_middle += '<option value="'+cavs[j]+'">'+cavs[j]+'</option>';
							}
							if (cavity[i].type == 'foot' || cavity[i].type == 'body') {
								cav_foot += '<option value="'+cavs[j]+'">'+cavs[j]+'</option>';
							}
						}
					}

					$('#select_cav_head').append(cav_head);
					$('#select_cav_middle').append(cav_middle);
					$('#select_cav_foot').append(cav_foot);
				}else{
					$("#bodyBox").html('');
					$('#count_box').val(0);
				}

				$('#divLotOut').hide();
				$('#status_lot').val('LOT OK');

				$("#modalProduct").modal('hide');
				$("#loading").hide();
			}else{
				$("#loading").hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function plus(id) {
	    if ($('#qty_check').val() == '' || $('#qty_lot').val() == '') {
	    	audio_error.play();
	    	openErrorGritter('Error!','Masukkan Quantity Check & Quantity lot.');
	    }else{
	    	var count = $('#result_check_' + id).val();
	    	if ((parseInt(count) + 1) > 0) {
	    		$('#div_ng_detail_'+id).show();
	    	}else{
	    		$('#div_ng_detail_'+id).hide();
	    	}
	        $('#result_check_' + id).val(parseInt(count) + 1);
	        // count_ng++;
	        var total_ng = $('#total_ng').text();
	        $('#total_ng').html(parseInt(total_ng) + 1);
	        var cat = $('#defect_category_'+id).text();
	        var lot_out = 0;
	        $('#divLotOut').hide();
	        $('#status_lot').val('LOT OK');
	        if (cat == 'Critical') {
	        	$("#critical_qty").val(parseInt($("#critical_qty").val())+1);
	        	if (parseInt($("#critical_qty").val()) >= parseInt($("#critical_re").val())) {
	        		lot_out++;
	        	}
	        }

	        if (cat == 'Major') {
	        	$("#major_qty").val(parseInt($("#major_qty").val())+1);
	        	if (parseInt($("#major_qty").val()) >= parseInt($("#major_re").val())) {
	        		lot_out++;
	        	}
	        }

	        if (cat == 'Minor') {
	        	$("#minor_qty").val(parseInt($("#minor_qty").val())+1);
	        	if (parseInt($("#minor_qty").val()) >= parseInt($("#minor_re").val())) {
	        		lot_out++;
	        	}
	        }

	        if (lot_out > 0) {
	        	$('#divLotOut').show();
	        	$('#status_lot').val('LOT OUT');
	        }
	    }
        // $('#ng_count').html(count_ng);
	}

	function minus(id) {
		if ($('#qty_check').val() == '') {
			audio_error.play();
	    	openErrorGritter('Error!','Masukkan Quantity Check.');
	    }else{
	    	var count = $('#result_check_' + id).val();
	    	if ((parseInt(count) - 1) > 0) {
	    		$('#div_ng_detail_'+id).show();
	    	}else{
	    		$('#div_ng_detail_'+id).hide();
	    	}
		    var total_ng = $('#total_ng').text();
	        if (parseInt(count) != 0) {
	        	$('#result_check_' + id).val(parseInt(count) - 1);
	        }
	        if (total_ng != 0) {
	        	$('#total_ng').html(parseInt(total_ng) - 1);
	        }

	        var cat = $('#defect_category_'+id).text();
	        var lot_out = 0;
	        $('#divLotOut').hide();
	        $('#status_lot').val('LOT OK');
	        if (cat == 'Critical') {
	        	if ($("#critical_qty").val() != 0) {
	        		$("#critical_qty").val(parseInt($("#critical_qty").val())-1);
	        		if (parseInt($("#critical_qty").val()) >= parseInt($("#critical_re").val())) {
		        		lot_out++;
		        	}
	        	}
	        }

	        if (cat == 'Major') {
	        	if ($("#major_qty").val() != 0) {
	        		$("#major_qty").val(parseInt($("#major_qty").val())-1);
	        		if (parseInt($("#major_qty").val()) >= parseInt($("#major_re").val())) {
		        		lot_out++;
		        	}
	        	}
	        }

	        if (cat == 'Minor') {
	        	if ($("#minor_qty").val() != 0) {
	        		$("#minor_qty").val(parseInt($("#minor_qty").val())-1);
	        		if (parseInt($("#minor_qty").val()) >= parseInt($("#minor_re").val())) {
		        		lot_out++;
		        	}
	        	}
	        }

	        if (lot_out > 0) {
	        	$('#divLotOut').show();
	        	$('#status_lot').val('LOT OUT');
	        }
	    }
	}

	// function checkStamp(material) {
	// 	var material_number = material.split(' - ')[0];
	// 	if (stamp_hierarchy != null) {
	// 		for(var i = 0; i < stamp_hierarchy.length;i++){
	// 			if (material_number == stamp_hierarchy[i].finished) {
	// 				gmc = material_number;
	// 				janean = stamp_hierarchy[i].janean;
	// 				upc = stamp_hierarchy[i].upc;
	// 			}
	// 		}
	// 	}
		
	// 	if (document.getElementsByClassName('gmc').length > 0) {
	// 		document.querySelectorAll('.gmc').forEach( (x) => { x.value = gmc } );
	// 	}
	// 	if (janean != '-' && document.getElementsByClassName('janean').length > 0) {
	// 		document.querySelectorAll('.janean').forEach( (x) => { x.value = janean } );
	// 	}
	// 	if (upc != '-' && document.getElementsByClassName('upc').length > 0) {
	// 		document.querySelectorAll('.upc').forEach( (x) => { x.value = upc } );
	// 	}

	// 	if (document.getElementsByClassName('tipe_produk').length > 0) {
	// 		document.querySelectorAll('.tipe_produk').forEach( (x) => { x.value = material.split(' - ')[1] } );
	// 	}
	// }

	function confirmAll() {
		var ngs = 0;
		var notes = 0;
		for(var i = 0; i < point_check.length;i++){
			if (parseInt($('#result_check_'+i).val()) > 0) {
				var note = CKEDITOR.instances['note_'+i].getData();
	        	ngs++;
	        	if (note == '') {
	        		notes++;
	        	}
			}
		}
		if (ngs > 0 && notes > 0) {
			openErrorGritter('Error!','Ada item yang NG. Pastikan itu betul NG dan Note terisi.');
			return false;
		}

		var count_box = $('#count_box').val();
		var salah_box = 0;
		if ($('#product').text() == 'Recorder') {
			for(var i = 1; i <= parseInt(count_box);i++){
				if ($('#box_tr_'+i).text() != '') {
					if ($('#box_qty_'+i).val() == '') {
						salah_box++;
					}
					if ($('#box_pic_'+i).val() == '') {
						salah_box++;
					}
				}
			}
		}
		if (salah_box > 0) {
			openErrorGritter('Error!','Input Box dan PIC');
			return false;
		}

		if ($("#qty_auditor").val()  == '') {
			openErrorGritter('Error!','Pilih Auditor');
			return false;
		}

		if ($("#auditor").val()  == '') {
			openErrorGritter('Error!','Pilih Auditor');
			return false;
		}

		if ($("#auditee").val()  == '') {
			openErrorGritter('Error!','Pilih Auditee');
			return false;
		}

		if ($("#session").val()  == '') {
			openErrorGritter('Error!','Masukkan Sesi');
			return false;
		}

		if ($("#material_audited").val()  == '') {
			openErrorGritter('Error!','Pilih Material Sedang Audit');
			return false;
		}

		if ($("#qty_check").val()  == '') {
			openErrorGritter('Error!','Masukkan Qty Check');
			return false;
		}

		if ($("#qty_lot").val()  == '') {
			openErrorGritter('Error!','Masukkan Qty Check');
			return false;
		}

		if ($('#select_point_check').val().split('_')[1].match(/YRS/gi) || $('#select_point_check').val().split('_')[1].match(/YRF/gi)) {
			if ($('#cav_head').val() == '') {
				openErrorGritter('Error!','Masukkan Cavity Head');
				return false;
			}
			if ($('#cav_middle').val() == '') {
				openErrorGritter('Error!','Masukkan Cavity Middle');
				return false;
			}
			if ($('#cav_foot').val() == '') {
				openErrorGritter('Error!','Masukkan Cavity Foot');
				return false;
			}
		}
		var kata_confirm = 'Apakah Anda ingin menyelesaikan Audit?';
		if (confirm(kata_confirm)) {
			$('#loading').show();
			var stat = 0;
			var hasils = [];
			for(var i = 0; i < point_check.length;i++){
				var auditor = $('#auditor').val();
				var qty_auditor = $('#qty_auditor').val();
				var auditee_id = $('#auditee').val().split('_')[0];
				var auditee_name = $('#auditee').val().split('_')[1];
				var product = $('#product').text();
				var material_number = $('#select_point_check').val().split('_')[1];
				var material_description = $('#select_point_check').val().split('_')[2];

				var material_audited = $('#material_audited').val();
				var session = $('#session').val();
				var audit_id = $('#audit_id').val();
				var total_ng = $('#total_ng').text();
				var qty_check = $('#qty_check').val();
				var qty_lot = $('#qty_lot').val();
				var status_lot = $('#status_lot').val();

				var point_id = $('#point_id_'+i).val();
				var ordering = $('#ordering_'+i).val();
				var point_checks = $('#point_check_'+i).text();
				var standard = $('#standard_'+i).text();
				var point_check_details = $('#point_check_details_'+i).text();
				var defect_category = $('#defect_category_'+i).text();
				
				var result_check = $('#result_check_'+i).val();

				var note = CKEDITOR.instances['note_'+i].getData();

				var critical_acc_re = $("#critical_acc").val()+'_'+$("#critical_re").val();
				var major_acc_re = $("#major_acc").val()+'_'+$("#major_re").val();
				var minor_acc_re = $("#minor_acc").val()+'_'+$("#minor_re").val();

				var critical_qty = $("#critical_qty").val();
				var major_qty = $("#major_qty").val();
				var minor_qty = $("#minor_qty").val();

				var cavity_head = $("#cav_head").val();
				var cavity_middle = $("#cav_middle").val();
				var cavity_foot = $("#cav_foot").val();

				var box_qty = [];
				var box_pic = [];

				if ($('#product').text() == 'Recorder') {
					for(var j = 1; j <= parseInt(count_box);j++){
						if ($('#box_tr_'+j).text() != '') {
							box_qty.push($('#box_qty_'+j).val());
							box_pic.push($('#box_pic_'+j).val());
						}
					}
				}

				var ng_detail = [];
				ng_detail.push($('#ng_name_'+i).val());
				ng_detail.push($('#area_'+i).val());
				ng_detail.push($('#pallet_'+i).val());
				ng_detail.push($('#row_'+i).val());
				ng_detail.push($('#box_'+i).val());
				ng_detail.push($('#line_'+i).val());
				ng_detail.push($('#emp_'+i).val());

				var cavity_ng = null;
				if (product == 'Recorder') {
					cavity_ng = $('#cav_'+i).val();
				}

				var formData = new FormData();
				formData.append('auditor',auditor);
				formData.append('qty_auditor',qty_auditor);
				formData.append('auditee_id',auditee_id);
				formData.append('auditee_name',auditee_name);
				formData.append('material_number',material_number);
				formData.append('material_description',material_description);
				formData.append('session',session);
				formData.append('audit_id',audit_id);
				formData.append('total_ng',total_ng);
				formData.append('product',product);
				formData.append('point_id',point_id);
				formData.append('ordering',ordering);
				formData.append('qty_check',qty_check);
				formData.append('box_pic',box_pic.join(','));
				formData.append('box_qty',box_qty.join(','));
				formData.append('cavity_head',cavity_head);
				formData.append('cavity_middle',cavity_middle);
				formData.append('cavity_foot',cavity_foot);
				formData.append('cavity_ng',cavity_ng);
				formData.append('qty_lot',qty_lot);
				formData.append('status_lot',status_lot);
				formData.append('critical_acc_re',critical_acc_re);
				formData.append('major_acc_re',major_acc_re);
				formData.append('minor_acc_re',minor_acc_re);
				formData.append('critical_qty',critical_qty);
				formData.append('major_qty',major_qty);
				formData.append('minor_qty',minor_qty);
				formData.append('point_check',point_checks);
				formData.append('standard',standard);
				formData.append('point_check_details',point_check_details);
				formData.append('defect_category',defect_category);
				formData.append('result_check',result_check);
				formData.append('note',note);
				formData.append('ng_detail',ng_detail.join('_'));
				formData.append('material_audited',material_audited);

				$.ajax({
					url:"{{ url('input/qa/audit_fg/audit') }}",
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
							if (stat == point_check.length) {
								openSuccessGritter('Success!',"Audit Berhasil Disimpan");
								$('#loading').hide();
								alert('Audit Telah Dilaksanakan');
								if (parseInt(total_ng) > 0) {
									if (confirm('Apakah Anda ingin mengirimkan Email?')) {
										var url = '{{url("sendemail/qa/audit_fg")}}/'+audit_id;
										$("#loading").show();
										$.get(url, function(result, status, xhr){
											if(result.status){
												$('#loading').hide();
												openSuccessGritter('Success!','Send Email Succeeded');
												window.location.replace("{{url('index/qa/audit_fg/audit')}}");
											}else{
												$('#loading').hide();
												openErrorGritter('Error!',result.message);
											}
										})
									}else{
										window.location.replace("{{url('index/qa/audit_fg/audit')}}");
										cancelAll();
									}
								}else{
									window.location.replace("{{url('index/qa/audit_fg/audit')}}");
									cancelAll();
								}
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