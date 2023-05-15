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
	<input type="hidden" id="schedule_id" value="{{$schedule->id}}">
	<input type="hidden" id="schedule_date" value="{{$schedule->schedule_date}}">
	<input type="hidden" id="point_length" value="{{count($point_check)}}">
	<div class="row">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditor</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_id">{{$schedule->auditor_id}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_name">{{$schedule->auditor_name}}</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditee</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditee_id">{{$schedule->auditee_id}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditee_name">{{$schedule->auditee_name}}</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Employee</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<select class="form-control" id="select_emp" style="width: 100%;" data-placeholder="Pilih Karyawan">
							<option value=""></option>
							@foreach($emp as $emp)
							<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
							@endforeach
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td colspan="4" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Dokumen</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="document_number">{{$schedule->document_number}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="document_name">{{$schedule->document_name}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">Rev : <span id="document_version">{{$schedule->document_version}}</span></td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Alat Pelindung Diri</td>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Alat / Mesin yang Digunakan</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="alat">
						<?php if ($apd_alat->point_safety == null){ ?>
							None
						<?php }else{ ?>
							<?php echo $apd_alat->point_safety ?>
						<?php } ?>
					</td>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="mesin">
						<?php if ($mesin->point_safety == null){ ?>
							None
						<?php }else{ ?>
							<?php echo $mesin->point_safety ?>
						<?php } ?>
					</td>
				</tr>
			</table>
			<input type="hidden" id="department_shortname">
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableCheck">
				<thead id="headCheck" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 2%">Proses Pekerjaan</th>
						<th style="width: 2%">Point Pekerjaan</th>
						<th style="width: 2%">Safety Point</th>
						<th style="width: 10%">Hasil</th>
						<th style="width: 10%">Evidence & Note</th>
					</tr>
				</thead>
				<tbody id="bodyCheck" style="background-color: #f0f0ff;color: black;">
					<?php $index = 0; ?>
					@foreach($point_check as $point)
					<tr style="border: 1px solid black">
						<td style="border: 1px solid black;text-align: right;"><?php echo $index+1 ?>
							<input type="hidden" name="point_id_{{$index}}" id="point_id_{{$index}}" value="{{$point->id}}">
						</td>
						<td id="work_process_{{$index}}" style="border: 1px solid black;"><?php echo $point->work_process ?></td>
						<td id="work_point_{{$index}}" style="border: 1px solid black;"><?php echo $point->work_point ?></td>
						<td id="work_safety_{{$index}}" style="border: 1px solid black;"><?php echo $point->work_safety ?></td>
						<td style="border: 1px solid black;text-align: center;padding-top: 12px;">
							<div class="col-xs-6">
								<label class="containers">&#9711;
								  <input type="radio" name="condition_{{$index}}" id="condition_{{$index}}" value="OK">
								  <span class="checkmark"></span>
								</label>
							</div>
							<div class="col-xs-6">
								<label class="containers">&#9747;
								  <input type="radio" name="condition_{{$index}}" id="condition_{{$index}}" value="NG">
								  <span class="checkmark"></span>
								</label>
							</div>
							<input type="hidden" id="audit_type_{{$index}}" class="form-control" value="{{$point->audit_type}}" readonly>
							@if($point->audit_type == 'Isian')
							<hr style="border: 2px solid red">
							<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 5px;">
								Batas Bawah<br><input type="text" id="lower_{{$index}}" class="form-control" value="{{$point->lower}}" readonly>
							</div>
							<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 0px;">
								Batas Atas<br><input type="text" id="upper_{{$index}}" class="form-control" value="{{$point->upper}}" readonly>
							</div>
							<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">
								UOM<br><input type="text" id="uom_{{$index}}" class="form-control" value="{{$point->uom}}" readonly>
							</div>
							<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">
								Hasil<br><input type="text" id="hasil_{{$index}}" class="form-control numpad" style="background-color: gold" placeholder="Hasil" value="" readonly onchange="checkValue(this.value,'{{$point->lower}}','{{$point->upper}}','{{$index}}')"><br>
							</div>
							@else
								<input type="hidden" id="lower_{{$index}}" class="form-control" value="{{$point->lower}}" readonly>
								<input type="hidden" id="upper_{{$index}}" class="form-control" value="{{$point->upper}}" readonly>
								<input type="hidden" id="uom_{{$index}}" class="form-control" value="{{$point->uom}}" readonly>
								<input type="hidden" id="hasil_{{$index}}" class="form-control numpad" style="background-color: gold" placeholder="Hasil" value="" readonly>
							</div>
							@endif
						</td>
						<td style="border: 1px solid black;"><textarea id="note_{{$index}}"></textarea></td>
						<script type="text/javascript">
						CKEDITOR.replace('note_'+'{{$index}}' ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '100px',
				        	toolbar:'MA'
					    });
						</script>
					</tr>
					<?php $index++ ?>
					@endforeach
				</tbody>
			</table>
			<!-- <div id="clones" style="display: none">
				
			</div> -->
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

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

    var count_point = 0;

    var documents = null;

	jQuery(document).ready(function() {
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		// $("#id").val(0);
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('#select_emp').select2({
			allowClear:true
		});
		$("#select_area").val('').trigger('change');
		documents = null;

      $('body').toggleClass("sidebar-collapse");
	    count_point = 0;
	});

	function checkValue(id,lower,upper,index) {
		if(parseFloat(id) < parseFloat(lower) || parseFloat(id) > parseFloat(upper)){
			$('#hasil_'+index).css('background-color','none');
			$('#hasil_'+index).css('background-color','red');
			$('#hasil_'+index).css('color','white');
			$("input[name=condition_"+parseInt(index)+"][value=NG]").prop('checked', true);
		}else{
			$('#hasil_'+index).css('background-color','none');
			$('#hasil_'+index).css('background-color','lightgreen');
			$('#hasil_'+index).css('color','black');
			$("input[name=condition_"+parseInt(index)+"][value=OK]").prop('checked', true);
		}
	}

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			count_point = 0;
			location.reload();
		}
		documents = null;
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

	function confirmAll() {
		var statuses = 0;
		for(var i = 0; i < parseInt($('#point_length').val());i++){
			var decision = '';
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            statuses++;
	        });
		}

		if ($('#select_emp').val() == '') {
			openErrorGritter('Error!','Pilih Karyawan');
			$('#select_emp').focus();
			return false;
		}
		if (statuses < parseInt($('#point_length').val())) {
			var kata_confirm = 'Anda belum menyelesaikan Audit. Apakah Anda ingin menyimpan data sementara?';
			var status_audit = 'Belum';
		}else{
			var kata_confirm = 'Apakah Anda ingin menyelesaikan Audit?';
			var status_audit = 'Sudah';
		}
		if (confirm(kata_confirm)) {
			$('#loading').show();
			var stat = 0;
			var hasils = [];
			for(var i = 0; i < parseInt($('#point_length').val());i++){

				var schedule_id = $('#schedule_id').val();
				var schedule_date = $('#schedule_date').val();
				var auditor_id = $('#auditor_id').text();
				var auditor_name = $('#auditor_name').text();
				var auditee_id = $('#auditee_id').text();
				var auditee_name = $('#auditee_name').text();
				var document_number = $('#document_number').text();
				var document_name = $('#document_name').text();
				var document_version = $('#document_version').text();
				var alat = $('#alat').text();
				var mesin = $('#mesin').text();
				var employee_id = $('#select_emp').val();

				var point_id = $('#point_id_'+i).val();
				var audit_type = $('#audit_type_'+i).val();
				var lower = $('#lower_'+i).val();
				var upper = $('#upper_'+i).val();
				var uom = $('#uom_'+i).val();
				var hasil = $('#hasil_'+i).val();
				
				var decision = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            decision = $(this).val();
		        });

		        hasils.push(decision);

				var note = CKEDITOR.instances['note_'+i].getData();

				// var file = $('#image_evidence_'+i).prop('files')[0];
				// var filename = $('#image_evidence_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[0];
				// var extension = $('#image_evidence_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[1];

				var formData = new FormData();
				formData.append('schedule_id',schedule_id);
				formData.append('schedule_date',schedule_date);
				formData.append('auditor_id',auditor_id);
				formData.append('auditor_name',auditor_name);
				formData.append('auditee_id',auditee_id);
				formData.append('auditee_name',auditee_name);
				formData.append('document_number',document_number);
				formData.append('document_name',document_name);
				formData.append('document_version',document_version);
				formData.append('alat',alat);
				formData.append('mesin',mesin);
				formData.append('point_id',point_id);
				formData.append('decision',decision);
				formData.append('note',note);
				formData.append('audit_type',audit_type);
				formData.append('upper',upper);
				formData.append('lower',lower);
				formData.append('uom',uom);
				formData.append('hasil',hasil);
				formData.append('employee_id',employee_id);
				formData.append('status_audit',status_audit);
				// formData.append('file',file);
				// formData.append('filename',filename);
				// formData.append('extension',extension);

				$.ajax({
					url:"{{ url('input/audit/qa/special_process') }}",
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
							if (stat == parseInt($('#point_length').val())) {
								openSuccessGritter('Success!',"Audit Berhasil Disimpan");
								$('#loading').hide();
								alert('Audit Telah Dilaksanakan');
								if (status_audit == 'Sudah') {
									if (hasils.join().match(/NG/gi)) {
										if (confirm('Apakah Anda ingin mengirimkan Email?')) {
											var url = '{{url("sendemail/qa/special_process")}}/'+schedule_id;
											$("#loading").show();
											$.get(url, function(result, status, xhr){
												if(result.status){
													$('#loading').hide();
													openSuccessGritter('Success!','Send Email Succeeded');
													window.location.replace("{{url('index/qa/special_process')}}");
												}else{
													$('#loading').hide();
													openErrorGritter('Error!',result.message);
												}
											})
										}else{
											window.location.replace("{{url('index/qa/special_process')}}");
										}
									}else{
										window.location.replace("{{url('index/qa/special_process')}}");
									}
								}else{
									window.location.replace("{{url('index/qa/special_process')}}");
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