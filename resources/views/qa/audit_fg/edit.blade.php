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

#tableCheck > tbody > tr > td > p > img {
	width: 250px !important;
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
	<input type="hidden" name="audit_id" id="audit_id" value="{{$audit_id}}">
	<div class="row">
		<?php if (count($audit) > 0){ ?>
			<div class="col-xs-6" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Tanggal Audit</td>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Qty Lot</td>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Qty Check</td>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Sesi</td>
					</tr>
					<tr>
						<td style="background-color: #ffadad;border:1px solid black;font-size: 18px">
							<input type="text" class="form-control datepicker" style="width: 100%" name="date" id="date" value="{{$audit->date}}" readonly="">
						</td>
						<td style="background-color: #ffadad;border:1px solid black;font-size: 18px">
							<input type="text" class="form-control numpad" style="width: 100%" name="qty_lot" id="qty_lot" value="{{$audit->qty_lot}}" readonly="">
						</td>
						<td style="background-color: #ffadad;border:1px solid black;font-size: 18px">
							<input type="text" class="form-control numpad" style="width: 100%" name="qty_check" id="qty_check" value="{{$audit->qty_check}}">
						</td>
						<td style="background-color: #ffadad;border:1px solid black;font-size: 18px">
							<input type="text" class="form-control numpad" style="width: 100%" name="session" id="session" value="{{$audit->session}}">
						</td>
					</tr>
				</table>
			</div>
			<div class="col-xs-6" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Auditor</td>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Qty Auditor</td>
					</tr>
					<tr>
						<td style="background-color: #abcbff;border:1px solid black;font-size: 18px">
							<select class="form-control" id="select_auditor" style="width: 100%;" data-placeholder="Pilih Auditor" multiple="multiple" onchange="changeAuditor()">
								<option value=""></option>
								@foreach($auditor as $auditor)
								<option value="{{$auditor->employee_id}}">{{$auditor->employee_id}} - {{$auditor->name}}</option>
								@endforeach
							</select>
							<input type="hidden" name="auditor" id="auditor">
						</td>
						<td style="background-color: #abcbff;border:1px solid black;font-size: 18px">
							<select class="form-control" id="select_qty_auditor" style="width: 100%;" data-placeholder="Ketik Quantity Per Auditor" multiple="multiple" onchange="changeQtyPerAuditor()">
								<option value=""></option>
							</select>
							<input type="hidden" name="qty_auditor" id="qty_auditor">
						</td>
					</tr>
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
					<input type="hidden" name="count_box" id="count_box" value="{{count(explode(',', $audit->box_qty))}}">
					<tbody id="bodyBox" style="background-color: #f0f0ff;color: black;">
						@if($audit->box_qty != null)
						<?php $box_qty = explode(',', $audit->box_qty); ?>
						<?php $box_pic = explode(',', $audit->box_pic); ?>
						<?php for ($i=0; $i < count($box_qty); $i++) { ?>
							<tr id="box_tr_{{$i+1}}">
								<td style="border: 1px solid black;text-align: left;">
									<input type="number" name="box_qty_{{$i+1}}" id="box_qty_{{$i+1}}" class="form-control numpad" placeholder="Input Qty Box" style="width: 100%" value="{{$box_qty[$i]}}">
								</td>
								<td style="border: 1px solid black;text-align: left;">
									<select class="form-control select2" id="box_pic_{{$i+1}}" name="box_pic_{{$i+1}}" data-placeholder="Pilih PIC Kensa" style="width: 100%">
										<option value=""></option>
										@foreach($emp_groups3 as $emp_groups)
										<?php if ($box_pic[$i] == $emp_groups->group.'_'.$emp_groups->employee_id.'_'.$emp_groups->name){ ?>
											<option value="{{$emp_groups->group}}_{{$emp_groups->employee_id}}_{{$emp_groups->name}}" selected="">{{$emp_groups->group}} - {{$emp_groups->employee_id}} - {{$emp_groups->name}}</option>
										<?php }else{ ?>
											<option value="{{$emp_groups->group}}_{{$emp_groups->employee_id}}_{{$emp_groups->name}}">{{$emp_groups->group}} - {{$emp_groups->employee_id}} - {{$emp_groups->name}}</option>
										<?php } ?>
										@endforeach
									</select>
								</td>
								<td style="border: 1px solid black;text-align: center;">
									<button class="btn btn-success btn-sm" onclick="addBox()"><i class="fa fa-plus"></i></button>
									<button class="btn btn-danger btn-sm" onclick="deleteBox('{{$i+1}}')"><i class="fa fa-minus"></i></button>
								</td>
							</tr>
						<?php } ?>
						@else
						<tr id="box_tr_1">
							<td style="border: 1px solid black;text-align: left;">
								<input type="number" name="box_qty_1" id="box_qty_1" class="form-control numpad" placeholder="Input Qty Box" style="width: 100%">
							</td>
							<td style="border: 1px solid black;text-align: left;">
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
						@endif
					</tbody>
				</table>
			</div>
			<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
				<label style="color: white;">Note</label>
				<?php if (count($audit_ng) > 0): ?>
					<?php $index_note = 0; ?>
					@foreach($audit_ng as $audit_ng)
					<input type="hidden" name="id_ng_{{$index_note}}" id="id_ng_{{$index_note}}" value="{{$audit_ng->id}}">
					<textarea id="note_{{$index_note}}">{{$audit_ng->note}}</textarea>
					<script type="text/javascript">
						CKEDITOR.replace('note_{{$index_note}}' ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '100px',
					        toolbar:'MA'
					    });
					</script>
					<?php $index_note++ ?>
					@endforeach
				<?php endif ?>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
					<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
						CANCEL
					</button>
				</div>
				<div class="col-xs-6" style="margin-top: 10px;padding-right: 0px;padding-left: 5px">
					<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
						UPDATE
					</button>
				</div>
			</div>
		<?php }else{ ?>
			<div class="col-xs-12" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td style="background-color: white;font-weight: bold;border:1px solid black;width: 2%;font-size: 30px" colspan="2">
							<span>Temuan Ini Sudah Pernah Ditangani</span>
						</td>
					</tr>
				</table>
			</div>
		<?php } ?>
	</div>

	<div class="modal fade" id="modalImage">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
										CLOSE
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="images" style="padding-top: 20px">
							
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

    var count_point = 0;

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});

		$('.select2').select2({
			allowClear:true
		});

		$('#select_auditor').select2({
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

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

      $('body').toggleClass("sidebar-collapse");
      fetchAuditor();
	});

	var emp_groups = <?php echo json_encode($emp_groups2); ?>;

	function addBox() {
		var id = parseInt($('#count_box').val())+1;
		var box = '';
		box += '<tr id="box_tr_'+id+'">';
		box += '<td style="border: 1px solid black;">';
		box += '<input type="number" name="box_qty_'+id+'" id="box_qty_'+id+'" class="form-control numpad2" placeholder="Input Qty Box" style="width: 100%">';
		box += '</td>';
		box += '<td style="border: 1px solid black;text-align:left">';
		box += '<select class="form-control" id="box_pic_'+id+'" name="box_pic_'+id+'" data-placeholder="Pilih PIC Kensa" style="width: 100%;">';
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

	function fetchAuditor() {
		var auditor_id = '{{$audit->auditor_id}}';
		var qty_auditor = '{{$audit->qty_auditor}}';
		if (auditor_id.match(/,/gi)) {
			$('#select_auditor').val(auditor_id.split(',')).trigger('change');
		}else{
			$('#select_auditor').val(auditor_id).trigger('change');
		}
		$('#auditor').val(auditor_id);

			var qty_auditors = qty_auditor.split(',');
			var detail = '';
			for(var i = 0; i < qty_auditors.length;i++){
				detail += '<option value="'+qty_auditors[i]+'">'+qty_auditors[i]+'</option>';
			}
			$('#select_qty_auditor').append(detail);
			$('#select_qty_auditor').val(qty_auditors).trigger('change');

		$('#qty_auditor').val(qty_auditor);
	}

	function changeAuditor() {
		$('#auditor').val($('#select_auditor').val());
	}

	function changeQtyPerAuditor() {
		$('#qty_auditor').val($('#select_qty_auditor').val());
	}

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			window.location.replace("{{url('index/qa/packing')}}");
		}
	}

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

    const monthNames = ["January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];


	function modalImage(url) {
		$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
		$('#modalImage').modal('show');
	}

	
	function confirmAll() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			var audit_id = $('#audit_id').val();
			var qty_check = $('#qty_check').val();
			var session = $('#session').val();
			var qty_lot = $('#qty_lot').val();
			var date = $('#date').val();
			var auditor = $('#auditor').val();
			var qty_auditor = $('#qty_auditor').val();

			var box_qty = [];
			var box_pic = [];

			var count_box = $('#count_box').val();
			if ('{{$audit->product}}' == 'Recorder') {
				for(var j = 1; j <= parseInt(count_box);j++){
					if ($('#box_tr_'+j).text() != '') {
						box_qty.push($('#box_qty_'+j).val());
						box_pic.push($('#box_pic_'+j).val());
					}
				}
			}

			var note = [];
			var id_ng = [];
			if (parseInt('{{count($audit_ng)}}') > 0) {
				for(var i = 0; i < parseInt('{{count($audit_ng)}}');i++){
					note.push(CKEDITOR.instances['note_'+i].getData());
					id_ng.push($('#id_ng_'+i).val());
				}
			}

			var data = {
				audit_id:audit_id,
				qty_check:qty_check,
				session:session,
				qty_lot:qty_lot,
				date:date,
				auditor:auditor,
				note:note,
				id_ng:id_ng,
				qty_auditor:qty_auditor,
				box_pic:box_pic.join(','),
				box_qty:box_qty.join(','),
			}

			$.post('{{ url("input/qa/audit_fg/update") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!',result.message);
					window.location.replace("{{url('index/qa/packing')}}");
				}else{
					audio_error.play();
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
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

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}
</script>
@endsection