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
  left: -10px;
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
	<input type="hidden" id="schedule_id" value="{{$schedule[0]->id}}">
	<input type="hidden" id="schedule_date" value="{{$schedule[0]->schedule_date}}">
	<input type="hidden" id="id" value="0">
	<div class="row">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="3">Auditor</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_id">{{$schedule[0]->employee_id}}</td>
					<td colspan="2"  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_name">{{$schedule[0]->name}}</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 3%;font-size: 15px">Auditee</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Upload File</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 1%;font-size: 15px">Add Point Check</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<select class="form-control" id="auditee" style="width: 100%;" data-placeholder="Pilih Auditee">
							<option value=""></option>
							@foreach($auditee as $auditee)
							<option value="{{$auditee->employee_id}}">{{$auditee->employee_id}} - {{$auditee->name}}</option>
							@endforeach
						</select>
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<input type="file" id="file_name_finding" class="form-control">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<button class="btn btn-success" style="width: 100%" onclick="addFinding()"><i class="fa fa-plus"></i></button>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Dokumen</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;width: 2%" id="document_number">{{$schedule[0]->document_number}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;width: 5%" id="document_name">{{$schedule[0]->title}}</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">File PDF</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Area</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px"><a href='{{url("files/standardization/documents/".$schedule[0]->file_name_pdf)}}' target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align: left;">
						<select class="form-control" id="area" style="width: 100%;" data-placeholder="Pilih Area">
							<option value=""></option>
							@foreach($area as $area)
							<option value="{{$area->area}}">{{$area->area}}</option>
							@endforeach
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" id="department_shortname">
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<table class="table table-bordered table-hover" style="width: 100%;border:1px solid black" id="tableCheck">
				<thead id="headCheck" style="background-color: #0073b7;color: white">
					<tr>
						<th style="width: 1%">Action</th>
						<th style="width: 2%">Proses</th>
						<th style="width: 2%">Condition</th>
						<th style="width: 2%">Object Audit</th>
						<th style="width: 5%">Temuan</th>
						<th style="width: 5%">Evidence</th>
						<th style="width: 5%">PIC</th>
					</tr>
				</thead>
				<tbody id="bodyCheck" style="background-color: #f0f0ff;color: black;">
					<tr id="tr_0">
						<td style="text-align: center;"></td>
						<td style="padding: 0px;"><input type="text" name="process_0" id="process_0" placeholder="Input Proses" style="width: 100%" class="form-control"></td>
						<td style="padding: 0px;">
							<div class="col-xs-6">
								<label class="containers" style="text-align: right;">&#9711;
								  <input type="radio" name="condition_0" id="condition_0" value="OK">
								  <span class="checkmark"></span>
								</label>
							</div>
							<div class="col-xs-6">
								<label class="containers" style="text-align: right;">&#9747;
								  <input type="radio" name="condition_0" id="condition_0" value="NG">
								  <span class="checkmark"></span>
								</label>
							</div>
						</td>
						<td style="padding: 0px;">
							<select class="form-control select_doc" id="document_0" style="width: 100%;" data-placeholder="Pilih Dokumen">
								<option value=""></option>
								@foreach($all_doc as $all_doc)
								@if($all_doc->document_number == $schedule[0]->document_number)
								<option value="{{$all_doc->document_number}}" selected="selected">{{$all_doc->document_number}} - {{$all_doc->title}}</option>
								@else
								<option value="{{$all_doc->document_number}}">{{$all_doc->document_number}} - {{$all_doc->title}}</option>
								@endif
								@endforeach
							</select>
						</td>
						<td style="padding: 0px;"><textarea id="finding_0"></textarea></td>
						<td style="padding: 0px;"><textarea id="evidence_0"></textarea></td>
						<td style="padding: 0px">
							<select class="form-control select_emp" id="emp_0" style="width: 100%;" data-placeholder="Pilih Karyawan">
								<option value=""></option>
								@foreach($emp as $emps)
								<option value="{{$emps->employee_id}}">{{$emps->employee_id}} - {{$emps->name}}</option>
								@endforeach
							</select>
						</td>

						<script type="text/javascript">
						CKEDITOR.replace('finding_0' ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '200px'
					    });
					    CKEDITOR.replace('evidence_0' ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '200px'
					    });
						</script>
					</tr>
				</tbody>
			</table>
			<div id="clone_process" style="display: none">
				<input type="text" name="process_new" id="process_new" placeholder="Input Proses" style="width: 100%" class="form-control">
			</div>
			<div id="clone_condition" style="display: none">
				<div class="col-xs-6">
					<label class="containers" style="text-align: right;">&#9711;
					  <input type="radio" name="condition_new" id="condition_new" value="OK">
					  <span class="checkmark"></span>
					</label>
				</div>
				<div class="col-xs-6">
					<label class="containers" style="text-align: right;">&#9747;
					  <input type="radio" name="condition_new1" id="condition_new1" value="NG">
					  <span class="checkmark"></span>
					</label>
				</div>
			</div>
			<div id="clone_document" style="display: none">
				<select class="form-control" id="document_new" style="width: 100%;" data-placeholder="Pilih Dokumen">
					<option value=""></option>
					@foreach($all_doc2 as $all_doc2)
					@if($all_doc2->document_number == $schedule[0]->document_number)
					<option value="{{$all_doc2->document_number}}" selected="selected">{{$all_doc2->document_number}} - {{$all_doc2->title}}</option>
					@else
					<option value="{{$all_doc2->document_number}}">{{$all_doc2->document_number}} - {{$all_doc2->title}}</option>
					@endif
					@endforeach
				</select>
			</div>
			<div id="clone_finding" style="display: none">
				<textarea id="finding_new"></textarea>
			</div>
			<div id="clone_evidence" style="display: none">
				<textarea id="evidence_new"></textarea>
			</div>
			<div id="clone_emp" style="display: none">
				<select class="form-control" id="emp_new" style="width: 100%;" data-placeholder="Pilih Karyawan">
					<option value=""></option>
					@foreach($emp2 as $emps2)
					<option value="{{$emps2->employee_id}}">{{$emps2->employee_id}} - {{$emps2->name}}</option>
					@endforeach
				</select>
			</div>
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

    var count_point = 1;

    var documents = null;
    var emps = null;

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$("#id").val(1);
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('#auditee').select2({
			allowClear:true
		});
		$('.select_doc').select2({
			allowClear:true
		});
		$('.select_emp').select2({
			allowClear:true
		});
		$('#area').select2({
			allowClear:true
		});
		$("#select_area").val('').trigger('change');
		documents = null;
		emps = null;

      $('body').toggleClass("sidebar-collapse");
	    count_point = 1;
	});

	function addFinding() {
		var processs = document.getElementById('clone_process').innerHTML;
		var documents = document.getElementById('clone_document').innerHTML;
		var evidences = document.getElementById('clone_evidence').innerHTML;
		var emps = document.getElementById('clone_emp').innerHTML;
		var finding = document.getElementById('clone_finding').innerHTML;
		var condition = document.getElementById('clone_condition').innerHTML;
		var id = $('#id').val();
		var new_id = parseInt(id);
		var clones = '';
		clones += '<tr id="tr_'+new_id+'">';
		clones += '<td style="text-align:center"><button class="btn btn-danger btn-sm" onclick="remove(\''+new_id+'\')"><i class="fa fa-trash"></i></button>';
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += processs;
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += condition;
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += documents;
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += finding;
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += evidences;
		clones += '</td>';
		clones += '<td style="padding-right:0px;padding-left:0px;">';
		clones += emps;
		clones += '</td>';
		clones += '</tr>';

		$('#bodyCheck').append(clones);
		$('#id').val((new_id+1));

		$('#document_new').prop('id','document_'+id);
		$('#process_new').prop('id','process_'+id);
		$('#condition_new').prop('name','condition_'+id);
		$('#condition_new').prop('id','condition_'+id);

		$('#condition_new1').prop('name','condition_'+id);
		$('#condition_new1').prop('id','condition_'+id);

		$('#finding_new').prop('id','finding_'+id);
		$('#evidence_new').prop('id','evidence_'+id);
		$('#emp_new').prop('id','emp_'+id);

		$('#document_'+id).select2({
			allowClear:true
		});

		$('#emp_'+id).select2({
			allowClear:true
		});

		CKEDITOR.replace('finding_'+id ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
	        height: '200px'
	    });
	    CKEDITOR.replace('evidence_'+id ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
	        height: '200px'
	    });
		// $('#bodyCheck').append(html);
		count_point++;
	}

	function confirmAll() {
		$('#loading').show();
		var auditee = $('#auditee').val();
		var area = $('#area').val();
		var schedule_date = $('#schedule_date').val();
		var schedule_id = $('#schedule_id').val();
		var document_number = $('#document_number').text();
		var document_name = $('#document_name').text();
		var auditor_id = $('#auditor_id').text();
		var auditor_name = $('#auditor_name').text();

		var file = $('#file_name_finding').prop('files')[0];
		var filename = $('#file_name_finding').val().replace(/C:\\fakepath\\/i, '').split(".")[0];
		var extension = $('#file_name_finding').val().replace(/C:\\fakepath\\/i, '').split(".")[1];

		if (filename == '') {
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Upload File');
			$('#file_name_finding').focus();
			return false;
		}

		if (auditee == '' || area == '') {
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}

		var stat = 0;

		for(var i = 0; i < parseInt($('#id').val());i++){
			if ($('#tr_'+i).text() != '') {
				var processes = $('#process_'+i).val();
				var emp = $('#emp_'+i).val();
				var decision = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
					decision = $(this).val();
				});
				var document_numbers = $('#document_'+i).val();
				var findings = CKEDITOR.instances['finding_'+i].getData();
				var evidences = CKEDITOR.instances['evidence_'+i].getData();

				var formData = new FormData();
				formData.append('file',file);
				formData.append('filename',filename);
				formData.append('extension',extension);
				formData.append('auditee',auditee);
				formData.append('area',area);
				formData.append('schedule_date',schedule_date);
				formData.append('schedule_id',schedule_id);
				formData.append('document_number',document_number);
				formData.append('document_name',document_name);
				formData.append('auditor_id',auditor_id);
				formData.append('auditor_name',auditor_name);
				formData.append('processes',processes);
				formData.append('conditions',decision);
				formData.append('document_numbers',document_numbers);
				formData.append('findings',findings);
				formData.append('evidences',evidences);
				formData.append('emp',emp);

				$.ajax({
					url:"{{ url('input/qa/qc_koteihyo/audit') }}",
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
							if (stat == count_point) {
								$('#loading').hide();
								openSuccessGritter('Success',data.message);
								alert('Proses Audit Selesai');
								window.location.replace("{{url('index/qa/qc_koteihyo')}}");
							}
						}else{
							openErrorGritter('Error!',data.message);
							audio_error.play();
							$('#loading').hide();
							return false;
						}

					}
				});
			}
		}
	}

	function remove(id) {
		count_point--;
		$('#tr_'+id).remove();
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