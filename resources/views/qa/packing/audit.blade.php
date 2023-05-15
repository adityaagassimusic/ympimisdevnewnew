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
		<input type="hidden" name="audit_id" id="audit_id">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Auditor</td>
					<td colspan="3" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Serial Number Sedang Audit (8 Digit)</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<select class="form-control" id="auditor" style="width: 100%;" data-placeholder="Pilih Auditor">
							<option value=""></option>
							@foreach($auditor as $auditor)
							<option value="{{$auditor->employee_id}}_{{$auditor->name}}">{{$auditor->employee_id}} - {{$auditor->name}}</option>
							@endforeach
						</select>
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="divSerialNumber">
						<input type="text" name="serial_number" id="serial_number" class="form-control" style="width: 100%" placeholder="Input Serial Number" onkeyup="checkSerial(this.value)">
					</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Auditee</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<select class="form-control" id="auditee" style="width: 100%;" data-placeholder="Pilih Auditee">
							<option value=""></option>
							@foreach($emp_all as $emp_all)
							<option value="{{$emp_all->employee_id}}_{{$emp_all->name}}">{{$emp_all->employee_id}} - {{$emp_all->name}}</option>
							@endforeach
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Product</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Material</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="product"></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="material"></td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Material Sedang Audit</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;" >
						<select class="form-control" id="material_audited" style="width: 100%;" data-placeholder="Pilih Material Sedang Audit" onchange="checkStamp(this.value)">
							<option value=""></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableCheck">
				<thead id="headCheck" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 2%">Point Check</th>
						<th style="width: 5%">Standard</th>
						<th style="width: 5%">Condition</th>
						<th style="width: 10%">Result</th>
						<th style="width: 10%">Evidence & Note</th>
					</tr>
				</thead>
				<tbody id="bodyCheck" style="background-color: #f0f0ff;color: black;">
					
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
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];

		$('.select2').select2({
			allowClear:true,
			ropdownParent: $('#modalProduct')
		});

		$('#auditee').select2({
			allowClear:true,
		});

		$('#auditor').select2({
			allowClear:true,
		});

		$('#material_audited').select2({
			allowClear:true,
		});

		$("#select_point_check").val('').trigger('change');
		$("#material_audited").val('').trigger('change');
		$("#auditee").val('').trigger('change');
		$("#auditor").val('').trigger('change');

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

      	$('body').toggleClass("sidebar-collapse");
	    count_point = 0;
	    point_check = null;
	    gmc = null;
	    janean = null;
	    upc = null;
	    stamp_hierarchy = null;
	    $('#bodyCheck').html('');
	});

	$('#modalProduct').on('shown.bs.modal', function () {
	});

	function cancelAll() {
		location.reload();
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

		if ($('#select_point_check').val().split('_')[1] == 'ZE92410') {
			$('#divSerialNumber').html('');
			$('#divSerialNumber').append('<input type="text" name="serial_number" id="serial_number" class="form-control" style="width: 100%" placeholder="Input Serial Number" value="Non-Serial-Number" readonly>');
		}

		$.get('{{ url("fetch/qa/packing/point_check") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyCheck').html('');
				var bodyCheck = '';

				for(var i = 0; i < result.point_check.length;i++){
					bodyCheck += '<tr>';
					bodyCheck += '<td style="border: 1px solid black;text-align: right;">'+(i+1);
						bodyCheck += '<input type="hidden" name="point_id_'+i+'" id="point_id_'+i+'" value="'+result.point_check[i].id+'">';
						bodyCheck += '<input type="hidden" name="ordering_'+i+'" id="ordering_'+i+'" value="'+result.point_check[i].ordering+'">';
						bodyCheck += '<input type="hidden" name="point_check_type_'+i+'" id="point_check_type_'+i+'" value="'+result.point_check[i].point_check_type+'">';
					bodyCheck += '</td>';
					bodyCheck += '<td id="point_check_'+i+'" style="border: 1px solid black;">'+result.point_check[i].point_check+'</td>';
					bodyCheck += '<td id="standard_'+i+'" style="border: 1px solid black;">'+result.point_check[i].standard+'</td>';
					bodyCheck += '<td style="border: 1px solid black;text-align: center;padding-top: 12px;">';
					bodyCheck += '<div class="col-xs-6">';
						bodyCheck += '<label class="containers">&#9711;';
						  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
						  bodyCheck += '<span class="checkmark"></span>';
						bodyCheck += '</label>';
					bodyCheck += '</div>';
					bodyCheck += '<div class="col-xs-6">';
						bodyCheck += '<label class="containers">&#9747;';
						  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
						  bodyCheck += '<span class="checkmark"></span>';
						bodyCheck += '</label>';
					bodyCheck += '</div>';
					if (result.point_check[i].point_check.match(/Japan/gi)) {
						var inputs = result.point_check[i].point_check_details.split(',');
						bodyCheck += '<div class="col-xs-6" style="margin-top:10px;">';
							bodyCheck += '<label class="containers" style="font-size:15px !important">Tidak Ada';
							  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="Tidak Ada" onclick="checkAllOk(\''+i+'\',\''+inputs.length+'\')">';
							  bodyCheck += '<span class="checkmark"></span>';
							bodyCheck += '</label>';
						bodyCheck += '</div>';
					}
					bodyCheck += '</td>';
					if (result.point_check[i].point_check_type == 'input') {
						bodyCheck += '<td style="border: 1px solid black;text-align: center;padding-top: 12px;" >';
						var inputs = result.point_check[i].point_check_details.split(',');
						for(var j = 0; j < inputs.length;j++){
							if (inputs[j].match(/GMC/gi)) {
								bodyCheck += inputs[j]+' <input class="form-control gmc" type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'" readonly><hr style="border:1px solid red">';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9711;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9747;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
							}else if (inputs[j].match(/JAN/gi)) {
								bodyCheck += inputs[j]+' <input class="form-control janean" type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'" readonly><hr style="border:1px solid red">';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9711;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9747;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
							}else if (inputs[j].match(/UPC/gi)) {
								bodyCheck += inputs[j]+' <input class="form-control upc" type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'" readonly><hr style="border:1px solid red">';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9711;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9747;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
							}else if(inputs[j].match(/Tipe Produk/gi)){
								bodyCheck += inputs[j]+' <input class="form-control tipe_produk" readonly type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'"><hr style="border:1px solid red">';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9711;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9747;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
							}else if(inputs[j].match(/Seri/gi) || inputs[j].match(/seri/gi)){
								bodyCheck += inputs[j]+' <input class="form-control no_seri" readonly type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'"><hr style="border:1px solid red">';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9711;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
								bodyCheck += '<div class="col-xs-6">';
									bodyCheck += '<label class="containers">&#9747;';
									  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
									  bodyCheck += '<span class="checkmark"></span>';
									bodyCheck += '</label>';
								bodyCheck += '</div>';
							}else{
								if (result.point_check[i].point_check.match(/Japan/gi)) {
									bodyCheck += inputs[j]+' <input class="form-control" value="Japan" readonly type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'"><hr style="border:1px solid red">';
									bodyCheck += '<div class="col-xs-6">';
										bodyCheck += '<label class="containers">&#9711;';
										  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="OK">';
										  bodyCheck += '<span class="checkmark"></span>';
										bodyCheck += '</label>';
									bodyCheck += '</div>';
									bodyCheck += '<div class="col-xs-6">';
										bodyCheck += '<label class="containers">&#9747;';
										  bodyCheck += '<input type="radio" name="condition_input_'+i+'_'+j+'" id="condition_input_'+i+'_'+j+'" value="NG">';
										  bodyCheck += '<span class="checkmark"></span>';
										bodyCheck += '</label>';
									bodyCheck += '</div>';
								}else{
									bodyCheck += inputs[j]+' <input class="form-control" type="text" id="input_'+i+'_'+j+'" placeholder="Input '+inputs[j]+'"><hr style="border:1px solid red">';
								}
							}
						}
						bodyCheck += '<input type="hidden" id="count_input_'+i+'" value="'+inputs.length+'"></td>';
					}else{
						bodyCheck += '<td style="border: 1px solid black;"></td>';
					}
					bodyCheck += '<td style="border: 1px solid black;"><textarea id="note_'+i+'"></textarea></td>';
					bodyCheck += '</tr>';
				}

				$('#bodyCheck').append(bodyCheck);

				stamp_hierarchy = result.stamp_hierarchy;

				for(var i = 0; i < result.point_check.length;i++){
					CKEDITOR.replace('note_'+i ,{
				        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
				        height: '100px',
				        toolbar:'MA'
				    });
				}
				$('#product').html($('#select_point_check').val().split('_')[0]);
				var sel = document.getElementById("select_point_check");
				var text= sel.options[sel.selectedIndex].text;
				$('#material').html(text);
				var materials = text.split(',');
				$('#material_audited').html('');
				var material_audited = '';
				material_audited += '<option value=""></option>';
				for(var i = 0; i < materials.length;i++){
					material_audited += '<option value="'+materials[i]+'">'+materials[i]+'</option>';
				}
				$('#material_audited').append(material_audited);

				if (materials.length == 1) {
					$('#material_audited').val(text).trigger('change');
					checkStamp(text);
				}
				point_check = result.point_check;

				$('#audit_id').val(result.audit_id);

				$("#modalProduct").modal('hide');
				$("#loading").hide();
			}else{
				$("#loading").hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function checkAllOk(index,lengths) {
		for(var i = 0; i < parseInt(lengths);i++){
			$("input[name='condition_input_"+index+"_"+i+"'][value='OK']").attr("checked", true);
		}
	}

	function checkStamp(material) {
		if (material != '') {
			var material_number = material.split(' - ')[0];
			if (stamp_hierarchy != null) {
				for(var i = 0; i < stamp_hierarchy.length;i++){
					if (material_number == stamp_hierarchy[i].finished) {
						gmc = material_number;
						janean = stamp_hierarchy[i].janean;
						upc = stamp_hierarchy[i].upc;
					}
				}
			}
			
			if (document.getElementsByClassName('gmc').length > 0) {
				document.querySelectorAll('.gmc').forEach( (x) => { x.value = gmc } );
			}
			if (material.split(' - ')[1].match(/YFL-3/gi)) {
				if (document.getElementsByClassName('janean').length > 0) {
					document.querySelectorAll('.janean').forEach( (x) => { x.value = 'No Data' } );
				}
			}else{
				if (janean != '-' && document.getElementsByClassName('janean').length > 0) {
					document.querySelectorAll('.janean').forEach( (x) => { x.value = janean } );
				}
			}
			if (upc != '-' && document.getElementsByClassName('upc').length > 0) {
				document.querySelectorAll('.upc').forEach( (x) => { x.value = upc } );
			}

			if (document.getElementsByClassName('tipe_produk').length > 0) {
				document.querySelectorAll('.tipe_produk').forEach( (x) => { x.value = material.split(' - ')[1] } );
			}
		}
	}

	function confirmAll() {
		var statuses = 0;
		var ngs = 0;
		var notes = 0;
		for(var i = 0; i < point_check.length;i++){
			var note = CKEDITOR.instances['note_'+i].getData();
			if (point_check[i].point_check_type == 'input') {
				var inputs = point_check[i].point_check_details.split(',');
				for(var j = 0; j < parseInt($('#count_input_'+i).val());j++){
					if ($('#input_'+i+'_'+j).val() == '') {
						statuses++;
					}
					if (inputs[j].match(/GMC/gi) || inputs[j].match(/JAN/gi) || inputs[j].match(/UPC/gi) || inputs[j].match(/Tipe Produk/gi)) {
						var decision_input = '';
						$("input[name='condition_input_"+i+"_"+j+"']:checked").each(function (i) {
							decision_input = $(this).val();
				        });
				        if (decision_input == '') {
				        	statuses++;
				        }
					}
				}
			}
			var decision = '';
			$("input[name='condition_"+i+"']:checked").each(function (i) {
				decision = $(this).val();
	        });
	        if (decision == '') {
	        	statuses++;
	        }
	        if (decision == 'NG') {
	        	ngs++;
	        	if (note == '') {
	        		notes++;
	        	}
	        }
		}

		if (document.getElementsByClassName('no_seri').length > 0) {
			var no_seri = [];
			$(".no_seri").each(function(){
			 	no_seri.push($(this).val());
			});
			for(var i = 0; i < no_seri.length;i++){
				if (no_seri[i].length < 6) {
					statuses++;
				}
			}
		}

		if ($("#auditor").val()  == '') {
			openErrorGritter('Error!','Pilih Auditee');
			return false;
		}
		if ($("#auditee").val()  == '') {
			openErrorGritter('Error!','Pilih Auditee');
			return false;
		}

		if ($("#material_audited").val()  == '') {
			openErrorGritter('Error!','Pilih Material Sedang Audit');
			return false;
		}

		if ($('#select_point_check').val().split('_')[1] == 'ZE92410') {
			if ($("#serial_number").val()  == '' || $("#serial_number").val().length < 8) {
				openErrorGritter('Error!','Input Serial Number & Serial Number Harus Sesuai Format');
				return false;
			}
		}

		if (statuses > 0) {
			openErrorGritter('Error!','Semua Hasil Harus Diisi / Pastikan Penulisan Serial Number sudah betul.');
			return false;
		}
		if (ngs > 0 && notes > 0) {
			openErrorGritter('Error!','Ada item yang NG. Pastikan itu betul NG dan Note terisi.');
			return false;
		}
		var kata_confirm = 'Apakah Anda ingin menyelesaikan Audit?';
		if (confirm(kata_confirm)) {
			$('#loading').show();
			var stat = 0;
			var hasils = [];
			for(var i = 0; i < point_check.length;i++){
				var audit_id = $('#audit_id').val();
				var auditor_id = $('#auditor').val().split('_')[0];
				var auditor_name = $('#auditor').val().split('_')[1];
				var auditee_id = $('#auditee').val().split('_')[0];
				var auditee_name = $('#auditee').val().split('_')[1];
				var product = $('#product').text();
				var material_number = $('#select_point_check').val().split('_')[1];
				var material_description = $('#select_point_check').val().split('_')[2];

				var material_audited = $('#material_audited').val();
				var serial_number = $('#serial_number').val();

				var point_id = $('#point_id_'+i).val();
				var ordering = $('#ordering_'+i).val();
				var point_checks = $('#point_check_'+i).text();
				var standard = $('#standard_'+i).text();
				var point_check_type = $('#point_check_type_'+i).val();
				var point_check_details = point_check[i].point_check_details;
				
				var results = [];
				var condition = '';
				var statuses_input = 0;
				if (point_check[i].point_check_type == 'input') {
					var inputs = point_check[i].point_check_details.split(',');
					for(var j = 0; j < parseInt($('#count_input_'+i).val());j++){
						results.push($('#input_'+i+'_'+j).val());
						if (inputs[j].match(/GMC/gi) || inputs[j].match(/JAN/gi) || inputs[j].match(/UPC/gi) || inputs[j].match(/Tipe Produk/gi)) {
							var decision_input = '';
							$("input[name='condition_input_"+i+"_"+j+"']:checked").each(function (i) {
								decision_input = $(this).val();
					        });
					        if (decision_input == 'NG') {
					        	statuses_input++;
					        }
						}
					}
				}
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            condition = $(this).val();
		        });

		        if (statuses_input > 0) {
		        	condition = 'NG';
		        }

		        hasils.push(condition);

				var note = CKEDITOR.instances['note_'+i].getData();

				var formData = new FormData();
				formData.append('auditor_id',auditor_id);
				formData.append('auditor_name',auditor_name);
				formData.append('auditee_id',auditee_id);
				formData.append('auditee_name',auditee_name);
				formData.append('material_number',material_number);
				formData.append('material_description',material_description);
				formData.append('audit_id',audit_id);
				formData.append('product',product);
				formData.append('point_id',point_id);
				formData.append('ordering',ordering);
				formData.append('point_check',point_checks);
				formData.append('standard',standard);
				formData.append('point_check_details',point_check_details);
				formData.append('point_check_type',point_check_type);
				formData.append('results',results.join(','));
				formData.append('condition',condition);
				formData.append('note',note);
				formData.append('material_audited',material_audited);
				formData.append('serial_number',serial_number);

				$.ajax({
					url:"{{ url('input/qa/packing/audit') }}",
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
								window.location.replace("{{url('index/qa/packing')}}");
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

	function checkSerial(value) {
		$("#auditee").val('').trigger('change');
		if (value.length >= 6) {
			var data = {
				serial_number:value,
				product:$('#product').text(),
			}
			$.get('{{ url("fetch/qa/packing/serial_number") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.auditee != null) {
						$('#auditee').val(result.auditee.employee_id+"_"+result.auditee.employee_name).trigger('change');
						openSuccessGritter('Success','Auditee Ditemukan');
					}else{
						openErrorGritter('Error!','Pilih Manual Auditee');
					}
					var serial_number = value;
					if (serial_number != '-' && document.getElementsByClassName('no_seri').length > 0) {
						document.querySelectorAll('.no_seri').forEach( (x) => { x.value = serial_number } );
					}
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
					$("#serial_number").val('');
					$("#auditee").val('').trigger('change');
				}
			})
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