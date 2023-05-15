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
	<div class="row">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditor</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_id">{{$emp->employee_id}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor_name">{{$emp->name}}</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditee</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<select id="select_auditee" class="form-control select2" data-placeholder="Pilih Auditee" onchange="changeAuditee()" multiple="">
							<option value=""></option>
							@foreach($certificate as $certificate)
							<option value="{{$certificate->certificate_name}}">{{$certificate->certificate_name}}</option>
							@endforeach
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td colspan="2" style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Document</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">
						<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Document IK" id="document_number" onchange="changeDocument()">
							<option value=""></option>
							@foreach($point as $point)
							<option value="{{$point->document_number}}_{{$point->document_name}}">{{$point->document_number}} - {{$point->document_name}}</option>
							@endforeach
						</select>
					</td>
				</tr>
				<tr>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Alat Pelindung Diri</td>
					<td style="background-color: yellowgreen;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Alat / Mesin yang Digunakan</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="alat">
					</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="mesin">
					</td>
				</tr>
			</table>
			<input type="hidden" id="department_shortname">
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<div class="col-xs-8" style="padding-left: 0px;padding-right: 5px;background-color: chocolate;padding-bottom: 0px;height: 40px;display:table;">
				<span style="font-weight: bold;font-size: 20px;color: white; display:table-cell;vertical-align:middle;padding-left: 10px !important;">Peserta Audit IK</span>
			</div>
			<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
				<input type="text" name="tag" id="tag" class="form-control" placeholder="Scan ID Card Here . . ." style="height: 40px;">
			</div>
			<table class="table table-bordered table-hover" style="width: 125%;border:1px solid black" id="tableParticipant">
				<thead id="headParticipant" style="background-color: lightgrey;color: black">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 3%">Emp</th>
						<th style="width: 5%">Name</th>
						<th style="width: 2%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyParticipant" style="background-color: #f0f0ff;color: black;">
					
				</tbody>
			</table>
		</div>
		<div class="col-xs-12" style="margin-top: 10px;overflow-x: scroll;">
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;background-color: goldenrod;padding-bottom: 0px;height: 40px;display:table;">
				<span style="font-weight: bold;font-size: 20px;color: white; display:table-cell;vertical-align:middle;padding-left: 10px !important;">Point Check Audit IK</span>
			</div>
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

    function reloads() {
    	location.reload();
    }

    var index_participant = 0;
    var point_check = null;
    var auditees = [];

	jQuery(document).ready(function() {
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];
		$('#document_number').val('').trigger('change');
		$('#select_auditee').val([]).trigger('change');
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		// $("#id").val(0);
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
		});
		$('#select_emp').select2({
			allowClear:true
		});
		$('#select_auditee').select2({
			allowClear:true
		});
		$('#document_number').select2({
			allowClear:true
		});
		$("#select_area").val('').trigger('change');
		documents = null;

      $('body').toggleClass("sidebar-collapse");
	    count_point = 0;
	    // setInterval(reloads,10000);
	    point_check = null;
	});

	var certificate_all = <?php echo json_encode($certificate_all); ?>;

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

	function changeAuditee() {
		index_participant = 0;
		var auditee = $('#select_auditee').val();
		auditees = [];
		var emp = '';
		$('#bodyParticipant').html('');
		var index = 1;
		for(var j = 0; j < auditee.length;j++){
			for(var i = 0; i < certificate_all.length;i++){
				if (certificate_all[i].certificate_name == auditee[j]) {
					emp += '<tr>';
					emp += '<td style="text-align:center;border:1px solid black;vertical-align:middle;">'+index+'</td>';
					emp += '<td style="border:1px solid black;vertical-align:middle;">'+certificate_all[i].employee_id+'</td>';
					emp += '<td style="border:1px solid black;vertical-align:middle;">'+certificate_all[i].name+'</td>';
					emp += '<td style="border:1px solid black;vertical-align:middle;">';
					emp += '<select style="width:100%" class="form-control" id="select_attendance_'+index_participant+'">';
					emp += '<option value="Hadir">Hadir</option>';
					emp += "<option value='CUTI'>CUTI</option>";
					emp += '<option value="CK">CK</option>';
					emp += "<option value='SAKIT'>SAKIT</option>";
					emp += "<option value='Izin'>Izin</option>";
					emp += "<option value='Mangkir'>Mangkir</option>";
					emp += "<option value='ABS'>ABS</option>";
					emp += "<option value='IMP'>IMP</option>";
					emp += "<option value='TELAT'>TELAT</option>";
					emp += '</select>';
					emp += '</td>';
					emp += '</tr>';
					auditees.push({
						employee_id:certificate_all[i].employee_id,
						name:certificate_all[i].name,
					});
					index_participant++;
					index++;
				}
			}
		}
		$('#bodyParticipant').append(emp);

		for(var i = 0; i < index_participant;i++){
			$('#select_attendance_'+i).select2({
				allowClear:true,
			});
		}
	}

	function changeDocument() {
		if ($('#document_number').val() != '') {
			$('#loading').show();
			var document_number = $('#document_number').val().split('_')[0];
			var document_name = $('#document_number').val().split('_')[1];

			var data = {
				document_number:document_number,
				document_name:document_name,
			}

			$.get('{{ url("fetch/qa/ik/audit") }}',data, function(result, status, xhr){
				if(result.status){
					if (result.apd_alat != null) {
						$('#alat').html(result.apd_alat.point_safety);
					}
					if (result.mesin != null) {
						$('#mesin').html(result.mesin.point_safety);
					}

					point_check = result.point_check;
					$('#bodyCheck').html('');
					var index = 1;
					for(var i = 0; i < result.point_check.length;i++){
						bodyCheck += '<tr style="border: 1px solid black">';
						bodyCheck += '<td style="border: 1px solid black;text-align: right;">'+index;
						bodyCheck += '<input type="hidden" name="point_id_'+i+'" id="point_id_'+i+'" value="'+result.point_check[i].id+'">';
						bodyCheck += '</td>';
						bodyCheck += '<td id="work_process_'+i+'" style="border: 1px solid black;">'+result.point_check[i].work_process+'</td>';
						bodyCheck += '<td id="work_point_'+i+'" style="border: 1px solid black;">'+result.point_check[i].work_point+'</td>';
						bodyCheck += '<td id="work_safety_'+i+'" style="border: 1px solid black;">'+(result.point_check[i].work_safety || '')+'</td>';
						bodyCheck += '<td style="border: 1px solid black;text-align: center;padding-top: 12px;">';
						bodyCheck += '<div class="col-xs-6">';
							bodyCheck += '<label class="containers"><span style="color:green">&#9711;</span>';
							  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
							  bodyCheck += '<span class="checkmark"></span>';
							bodyCheck += '</label>';
						bodyCheck += '</div>';
						bodyCheck += '<div class="col-xs-6">';
							bodyCheck += '<label class="containers"><span style="color:red">&#9747;</span>';
							  bodyCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
							  bodyCheck += '<span class="checkmark"></span>';
							bodyCheck += '</label>';
						bodyCheck += '</div>';
						bodyCheck += '<input type="hidden" id="audit_type_'+i+'" class="form-control" value="'+result.point_check[i].audit_type+'" readonly>';
						if (result.point_check[i].audit_type == 'Isian') {
							bodyCheck += '<hr style="border: 2px solid red">';
							bodyCheck += '<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'Batas Bawah<br><input type="text" id="lower_'+i+'" class="form-control" value="'+result.point_check[i].lower+'" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 0px;">';
								bodyCheck += 'Batas Atas<br><input type="text" id="upper_'+i+'" class="form-control" value="'+result.point_check[i].upper+'" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'UOM<br><input type="text" id="uom_'+i+'" class="form-control" value="'+result.point_check[i].uom+'" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'Hasil<br><input type="text" id="hasil_'+i+'" class="form-control numpad" style="background-color: gold" placeholder="Hasil" value="" readonly onchange="checkValue(this.value,\''+result.point_check[i].lower+'\',\''+result.point_check[i].upper+'\',\''+i+'\')"><br>';
							bodyCheck += '</div>';
						}else{
							bodyCheck += '<div style="display:none">';
							bodyCheck += '<hr style="border: 2px solid red">';
							bodyCheck += '<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'Batas Bawah<br><input type="text" id="lower_'+i+'" class="form-control" value="" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6" style="text-align: left;padding-left: 0px;padding-right: 0px;">';
								bodyCheck += 'Batas Atas<br><input type="text" id="upper_'+i+'" class="form-control" value="" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'UOM<br><input type="text" id="uom_'+i+'" class="form-control" value="" readonly>';
							bodyCheck += '</div>';
							bodyCheck += '<div class="col-xs-6"  style="text-align: left;padding-left: 0px;padding-right: 5px;">';
								bodyCheck += 'Hasil<br><input type="text" id="hasil_'+i+'" class="form-control" style="background-color: gold" placeholder="Hasil" value="" readonly><br>';
							bodyCheck += '</div>';
							bodyCheck += '</div>';
						}
						bodyCheck += '</td>';
						bodyCheck += '<td style="border: 1px solid black;padding:0px;"><textarea id="note_'+i+'"></textarea></td>';
						bodyCheck += '</tr>';
						index++;
					}
					$('#bodyCheck').append(bodyCheck);

					$('.numpad').numpad({
						hidePlusMinusButton : true,
						decimalSeparator : '.'
					});

					for(var i = 0; i < result.point_check.length;i++){
						CKEDITOR.replace('note_'+i ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '100px',
				        	toolbar:'MA'
					    });
					}
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
					return false;
				}
			});
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
		var salah = 0;
		for(var i = 0; i < point_check.length;i++){
			var decision = '';
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            decision = $(this).val();
	        });
	        if (decision == '') {
	        	salah++;
	        }
	        if (point_check[i].audit_type == 'Isian') {
	        	// if ($('#hasil_'+i).val() == '') {
	        	// 	salah++;
	        	// }
	        }
		}

		if (auditees.length == 0) {
			openErrorGritter('Error!','Pilih Karyawan');
			return false;
		}

		if (salah > 0) {
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}
		// if (statuses < parseInt($('#point_length').val())) {
		// 	var kata_confirm = 'Anda belum menyelesaikan Audit. Apakah Anda ingin menyimpan data sementara?';
		// 	var status_audit = 'Belum';
		// }else{
			var kata_confirm = 'Apakah Anda ingin menyelesaikan Audit?';
		// 	var status_audit = 'Sudah';
		// }
		if (confirm(kata_confirm)) {
			$('#loading').show();
			var stat = 0;
			var hasils = [];
			for(var i = 0; i < point_check.length;i++){

				var auditor_id = $('#auditor_id').text();
				var auditor_name = $('#auditor_name').text();
				var auditee_status = [];
				var auditee_id = [];
				var auditee_name = [];
				for(var j = 0; j < auditees.length;j++){
					auditee_status.push($('#select_attendance_'+j).val());
					auditee_id.push(auditees[j].employee_id);
					auditee_name.push(auditees[j].name);
				}
				var document_number = $('#document_number').val().split('_')[0];
				var document_name = $('#document_number').val().split('_')[1];
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
				var note = CKEDITOR.instances['note_'+i].getData();

				var formData = new FormData();
				formData.append('auditor_id',auditor_id);
				formData.append('auditor_name',auditor_name);
				formData.append('auditee_id',auditee_id.join(','));
				formData.append('auditee_name',auditee_name.join(','));
				formData.append('auditee_status',auditee_status);
				formData.append('document_number',document_number);
				formData.append('document_name',document_name);
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
				// formData.append('status_audit',status_audit);

				$.ajax({
					url:"{{ url('input/qa/ik/audit') }}",
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
							if (stat == parseInt(point_check.length)) {
								openSuccessGritter('Success!',"Audit Berhasil Disimpan");
								$('#loading').hide();
								alert('Audit Telah Dilaksanakan');
								window.location.replace("{{url('index/qa/ik')}}");
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