@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
  left: 20px;
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
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditor</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="operator_id">-</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="operator_name">-</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditee</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditee_id">-</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditee_name">-</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Claim Title</td>
					<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Area</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="audit_title">-</td>
					<td colspan="2" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="area">-</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Dept</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Product</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Claim Date</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Claim Origin</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="department_name">-</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="product">-</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="incident_date_title">-</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="origin">-</td>
				</tr>
			</table>
			<input type="hidden" id="email_date">
			<input type="hidden" id="periode">
			<input type="hidden" id="department">
			<input type="hidden" id="incident_date">
			<input type="hidden" id="chief_foreman">
			<input type="hidden" id="manager">
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px">
			<table class="table table-responsive table-hover" style="width: 100%;border:1px solid black" id="tableCheck">
				
			</table>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
				<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
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

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalSchedule">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" id="schedule_choice" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Schedule</span></center>
								</div>
								<div class="col-xs-12" id="schedule_btn">
									<div class="row">
										@foreach($schedule as $schedule)
										<div class="col-xs-6" style="padding-top: 5px">
											<center>
												<button class="btn btn-primary" id="{{$schedule->audit_id}}" style="width: 100%;font-size: 15px;font-weight: bold;" onclick="getSchedule('{{$schedule->audit_id}}','{{$schedule->audit_title}}','{{$schedule->id}}')">{{$schedule->audit_title}}<br>{{$schedule->schedule_date}}</button>
											</center>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="schedule_fix" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Schedule</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<button class="btn btn-primary" id="schedule_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeSchedule()">
										SCHEDULE
									</button>
								</div>
								<input type="hidden" id="audit_id" name="audit_id">
								<input type="hidden" id="schedule_id" name="schedule_id">
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button onclick="saveSchedule()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
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

	jQuery(document).ready(function() {
		setSchedule();

      $('body').toggleClass("sidebar-collapse");
	    count_point = 0;
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	function setSchedule() {
		$('#operator_id').html('{{$emp->employee_id}}');
		$('#operator_name').html('{{$emp->name}}');
		$('#schedule_fix').hide();
		$('#schedule_choice').show();
		$('#modalSchedule').modal('show');
	}

	function getSchedule(id,title,schedule_id) {
		$('#schedule_fix').show();
		$('#schedule_choice').hide();
		$('#schedule_fix2').html(title);
		$('#audit_id').val(id);
		$('#schedule_id').val(schedule_id);
	}

	function changeSchedule() {
		$('#schedule_fix').hide();
		$('#schedule_choice').show();
		$('#schedule_fix2').html("SCHEDULE");
		$('#audit_id').val('0');
		$('#schedule_id').val('0');
	}

	function getHour(value) {
		$('#hour_fix').show();
		$('#hour_choice').hide();
		$('#hour_fix2').html(value);
	}

	function changeHour() {
		$('#hour_fix').hide();
		$('#hour_choice').show();
		$('#hour_fix2').html("JAM");
	}

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			count_point = 0;
			location.reload();
		}
	}

	function readURL(input,idfile,idfile2) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#blah_'+idfile+'_'+idfile2).show();
              $('#blah_'+idfile+'_'+idfile2)
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

    const monthNames = ["January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];

	function saveSchedule() {
		$('#loading').show();
		if ($('#schedule_fix2').text() == 'SCHEDULE' || $('#audit_id').val() == '0') {
			audio_error.play();
			openErrorGritter('Error!','Pilih Schedule');
		}else{
			var audit_id = $('#audit_id').val();

			var data = {
				audit_id:audit_id
			}

			$.get('{{ url("fetch/audit_ng_jelas/point") }}', data, function(result, status, xhr){
				if(result.status){

					var chief_foreman = [];
					var manager = [];

					for(var i = 0; i < result.auditee.length;i++){
						if (result.auditee[i].remark == 'Foreman') {
							if (result.auditee[i].approver_id != '') {
								if (result.audit[0].area.match(/Saxophone/gi)) {
									if (result.auditee[i].section == 'Assembly Sax Process Section') {
										$('#auditee_id').html(result.auditee[i].approver_id);
										$('#auditee_name').html(result.auditee[i].approver_name);
										chief_foreman.push(result.auditee[i].approver_email);
										break;
									}
								}else if (result.audit[0].area.match(/Flute/gi)){
									if (result.auditee[i].section == 'Assembly FL Process Section') {
										$('#auditee_id').html(result.auditee[i].approver_id);
										$('#auditee_name').html(result.auditee[i].approver_name);
										chief_foreman.push(result.auditee[i].approver_email);
										break;
									}
								}else{
									$('#auditee_id').html(result.auditee[i].approver_id);
									$('#auditee_name').html(result.auditee[i].approver_name);
									chief_foreman.push(result.auditee[i].approver_email);
									break;
								}
							}
						}else if (result.auditee[i].remark == 'Chief') {
							if (result.auditee[i].approver_id != '') {
								if (result.audit[0].area.match(/Saxophone/gi)) {
									if (result.auditee[i].section == 'Assembly Sax Process Section') {
										$('#auditee_id').html(result.auditee[i].approver_id);
										$('#auditee_name').html(result.auditee[i].approver_name);
										chief_foreman.push(result.auditee[i].approver_email);
										break;
									}
								}else if (result.audit[0].area.match(/Flute/gi)){
									if (result.auditee[i].section == 'Assembly FL Process Section') {
										$('#auditee_id').html(result.auditee[i].approver_id);
										$('#auditee_name').html(result.auditee[i].approver_name);
										chief_foreman.push(result.auditee[i].approver_email);
										break;
									}
								}else{
									$('#auditee_id').html(result.auditee[i].approver_id);
									$('#auditee_name').html(result.auditee[i].approver_name);
									chief_foreman.push(result.auditee[i].approver_email);
									break;
								}
							}
						}
					}

					for(var i = 0; i < result.auditee.length;i++){
						if (result.auditee[i].remark == 'Manager') {
							manager.push(result.auditee[i].approver_email);
						}

						if (result.auditee[i].remark == 'Chief') {
							if (result.auditee[i].approver_id != '') {
								chief_foreman.push(result.auditee[i].approver_email);
							}
						}
					}

					$('#chief_foreman').val(chief_foreman.join(','));
					$('#manager').val(manager.join(','));

					$('#audit_title').html(result.audit[0].audit_title);
					$('#product').html(result.audit[0].product);
					$('#origin').html(result.audit[0].origin);
					$('#department_name').html(result.audit[0].department_shortname);
					$('#department').val(result.audit[0].department);
					$('#periode').val(result.audit[0].periode);
					var incident_date = new Date(result.audit[0].incident_date);
					var date = incident_date.toLocaleDateString();

					var day = addZero(incident_date.getDate());
					var month = monthNames[incident_date.getMonth()];
					var year = addZero(incident_date.getFullYear());
					$('#incident_date_title').html(day+' '+month+' '+year);
					$('#area').html(result.audit[0].area);
					$('#email_date').val(result.audit[0].email_date);
					$('#incident_date').val(result.audit[0].incident_date);

					$('#tableCheck').html('');
					var tableCheck = '';

					tableCheck += '<tr>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:1%;font-weight:bold;">#</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Point Check</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Image Reference</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:2%;font-weight:bold;">Condition</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:1%;font-weight:bold;">Upload Evidence</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Images Evidence</td>';
					tableCheck += '<td style="background-color:rgb(126,86,134);color:#FFD700;font-size:20px;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Description</td>';
					tableCheck += '</tr>';

					count_point = result.audit.length;

					var satu = '1';
					var index = 1;
					for(var i = 0; i < result.audit.length;i++){
						if (index % 2 === 0 ) {
							var color = '#e6e6e6';
						} else {
							var color = '#ffffff';
						}
						tableCheck += '<tr>';
						tableCheck += '<td id="audit_index_'+i+'" style="background-color:'+color+';font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;">'+result.audit[i].audit_index+'</td>';
						tableCheck += '<td id="audit_point_'+i+'" style="background-color:'+color+';font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;">'+result.audit[i].audit_point+'</td>';
						if (result.audit[i].audit_images == null) {
							tableCheck += '<td id="point_check_images_'+i+'" style="background-color:'+color+';font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;"><input type="hidden" id="audit_images_'+i+'" value="'+result.audit[i].audit_images+'"></td>';
						}else{
							var url = '{{url("data_file/qa/ng_jelas_point/")}}'+'/'+result.audit[i].audit_images;
							tableCheck += '<td id="point_check_images_'+i+'" style="background-color:'+color+';font-size:15px;border:2px solid rgb(60, 60, 60);padding:5px;"><img style="width:100%;cursor:pointer" src="'+url+'" class="user-image" alt="User image" onclick="modalImage(\''+url+'\')"><input type="hidden" id="audit_images_'+i+'" value="'+result.audit[i].audit_images+'"></td>';
						}
						tableCheck += '<td style="background-color:'+color+';font-size:20px;border:2px solid rgb(60, 60, 60);width:1%;">';
						tableCheck += '<label class="containers">&#9711;';
						  tableCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
						  tableCheck += '<span class="checkmark"></span>';
						tableCheck += '</label>';
						tableCheck += '<label class="containers">&#9747;';
						  tableCheck += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
						  tableCheck += '<span class="checkmark"></span>';
						tableCheck += '</label>';
						tableCheck += '</td>';

						tableCheck += '<td style="background-color:'+color+';border:2px solid rgb(60, 60, 60);">';
						tableCheck += '<div id="increment_'+i+'" class="col-xs-12">';
						tableCheck += '<input type="hidden" id="nomor_'+i+'" value="1">';
						tableCheck += '<input type="file" id="file_'+i+'_1" onchange="readURL(this,\''+i+'\',\''+satu+'\');">';
						tableCheck += '<button class="btn btn-success pull-left" onclick="tambah_foto(\''+i+'\')"><i class="fa fa-plus"></i></button>';
						tableCheck += '</div>';

						tableCheck += '<div id="clone_'+i+'" class="hide">';
						tableCheck += '<div class="col-xs-12" style="padding-top:5px	">';
						tableCheck += '<input type="file" id="file_'+i+'_0" onchange="readURL(this,\''+i+'\');">';
						tableCheck += '<button class="btn btn-danger pull-left" id="btn_kurang_'+i+'_0" onclick="kurang_foto(\''+i+'\',\''+satu+'\')"><i class="fa fa-minus"></i></button>';
						tableCheck += '</div>';
						tableCheck += '</div>';
						tableCheck += '</td>';

						tableCheck += '<td style="background-color:'+color+';font-size:20px;border:2px solid rgb(60, 60, 60);width:1%;" id="reference_'+i+'">';
						tableCheck += '<div id="increment_image_'+i+'" class="col-xs-12">';
						tableCheck += '<input type="hidden" id="nomor_image_'+i+'" value="1">';
						tableCheck += '<img width="100px" id="blah_'+i+'_1" src="" style="display: none" alt="your image" />';
						tableCheck += '</div>';
						tableCheck += '<div id="clone_image_'+i+'" class="hide">';
						tableCheck += '<div class="col-xs-12">';
						tableCheck += '<img width="100px" id="blah_'+i+'_0" src="" style="display: none" alt="your image" />';
						tableCheck += '</div>';
						tableCheck += '</div>';
						tableCheck += '</td>';
						tableCheck += '<td style="background-color:'+color+';font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;">';
						tableCheck += '<textarea id="note_'+i+'" style="width:100%"></textarea>';
						tableCheck += '</td>';
						tableCheck += '</tr>';
						tableCheck += '<input type="hidden" id="audit_id_'+i+'" value="'+result.audit[i].id+'">';
						index++;
					}

					$('#tableCheck').append(tableCheck);

					$('#loading').hide();
					$('#modalSchedule').modal('hide');
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function tambah_foto(ii) {
		var html = $("#clone_"+ii).html();
		$("#increment_"+ii).after(html);
		$('#file_'+ii+'_0').prop('id','file_'+ii+'_'+(parseInt($('#nomor_'+ii).val())+1));
		$('#btn_kurang_'+ii+'_0').prop('id','btn_kurang_'+ii+'_'+(parseInt($('#nomor_'+ii).val())+1));
		$('#file_'+ii+'_0').prop('onchange','testss()');
		$('#file_'+ii+'_'+(parseInt($('#nomor_'+ii).val())+1)).removeAttr('onchange');
		var new_nomor = (parseInt($('#nomor_'+ii).val())+1);
		document.getElementById('file_'+ii+'_'+(parseInt($('#nomor_'+ii).val())+1)).setAttribute("onchange", 'readURL(this,\''+ii+'\',\''+(parseInt($('#nomor_'+ii).val())+1)+'\')');
		document.getElementById('btn_kurang_'+ii+'_'+(parseInt($('#nomor_'+ii).val())+1)).setAttribute("onclick", 'kurang_foto(\''+ii+'\',\''+(parseInt($('#nomor_'+ii).val())+1)+'\')');
		$('#nomor_'+ii).val(parseInt($('#nomor_'+ii).val())+1);
		var html_image = $("#clone_image_"+ii).html();
		$("#increment_image_"+ii).after(html_image);
		$('#blah_'+ii+'_0').prop('id','blah_'+ii+'_'+(parseInt($('#nomor_image_'+ii).val())+1));
		$('#nomor_image_'+ii).val(parseInt($('#nomor_image_'+ii).val())+1);
	}

	function kurang_foto(ii,iii) {
		$('#file_'+ii+'_'+iii).remove();
		$('#blah_'+ii+'_'+iii).remove();
		$('#btn_kurang_'+ii+'_'+iii).remove();

		// var id_akhir = $('#nomor_'+ii).val();
		// var id_sebelum = parseInt(iii)-1;

		// for(var i = parseInt(iii)+1; i < id_akhir;i++){
		// 	var k = i+1;
		// 	$('#file_'+ii+'_'+k).prop('id','file_'+ii+'_'+i);
		// 	$('#btn_kurang_'+ii+'_'+k).prop('id','btn_kurang_'+ii+'_'+i);
		// 	document.getElementById('file_'+ii+'_'+k).setAttribute("onchange", 'readURL(this,\''+ii+'\',\''+i+'\')');
		// 	document.getElementById('btn_kurang_'+ii+'_'+k).setAttribute("onclick", 'kurang_foto(\''+ii+'\',\''+i+'\')');
		// 	$('#blah_'+ii+'_'+k).prop('id','blah_'+ii+'_'+i);
		// }

		// $('#nomor_'+ii).val(parseInt(id_akhir)-1);
		// $('#nomor_image_'+ii).val(parseInt(id_akhir)-1);
	}

	function modalImage(url) {
		$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
		$('#modalImage').modal('show');
	}

	
	function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan proses?')) {
			$('#loading').show();
			var stat = 0;
			for(var i = 0; i < count_point;i++){
				var result_check = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_check = $(this).val();
		        });
		        var nomor = $('#nomor_'+i).val();
		        var k = parseInt(nomor)+1;
		        if ($('#file_'+i+'_'+k).length > 0) {
					if ($('#file_'+i+'_'+k).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '' || result_check == '') {
						$('#loading').hide();
						openErrorGritter('Error!','Isi Semua Data');
						return false;
					}
				}
			}
			for(var i = 0; i < count_point;i++){
				var file = '';

				var schedule_id = $('#schedule_id').val();
				var audit_id = $('#audit_id').val();
				var audit_index =  $('#audit_index_'+i).text();
				var audit_point =  $('#audit_point_'+i).text();
				var auditor =  $('#operator_id').text();
				var audit_images =  $('#audit_images_'+i).val();
				var audit_title = $('#audit_title').text();
				var periode = $('#periode').val();
				var email_date = $('#email_date').val();
				var incident_date = $('#incident_date').val();
				var origin = $('#origin').text();
				var department = $('#department').val();
				var area = $('#area').text();
				var product = $('#product').text();
				var note = $('#note_'+i).val();
				var chief_foreman = $('#chief_foreman').val();
				var manager = $('#manager').val();
				var nomor = $('#nomor_'+i).val();
				var result_check = null;
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_check = $(this).val();
		        });
		        var filenames = [];
				if (nomor == 1) {
					var stat_image_ready = 1;
					var stat_image = 1;
					var fileData  = $('#file_'+i+'_1').prop('files')[0];

					file=$('#file_'+i+'_1').val().replace(/C:\\fakepath\\/i, '').split(".");

					var formData = new FormData();
					formData.append('fileData', fileData);
					formData.append('audit_id', audit_id);
					formData.append('audit_index', audit_index);
					formData.append('extension', file[1]);
					formData.append('foto_name', file[0]);
					formData.append('filename','1_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
					if (file[0] != '') {
						filenames.push('1_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
					}
					
					$.ajax({
						url:"{{ url('upload/file/audit_ng_jelas') }}",
						method:"POST",
						data:formData,
						dataType:'JSON',
						contentType: false,
						cache: false,
						processData: false,
						success:function(data)
						{
							filenames.push(data.filename);
							stat_image++;
						},
						error: function(data) {
							$('#loading').hide();
							openErrorGritter('Error!',data.message);
						}
					});
					if (stat_image_ready == stat_image) {
						var formData = new FormData();
						formData.append('schedule_id', schedule_id);
						formData.append('audit_id', audit_id);
						formData.append('audit_index', audit_index);
						formData.append('audit_point', audit_point);
						formData.append('auditor', auditor);
						formData.append('audit_images', audit_images);
						formData.append('audit_title', audit_title);
						formData.append('periode', periode);
						formData.append('email_date', email_date);
						formData.append('result_check', result_check);
						formData.append('incident_date', incident_date);
						formData.append('origin', origin);
						formData.append('department', department);
						formData.append('area', area);
						formData.append('product', product);
						formData.append('note', note);
						formData.append('chief_foreman', chief_foreman);
						formData.append('manager', manager);
						if (filenames.length == 1) {
							formData.append('filenames', filenames[0]);
						}else{
							formData.append('filenames', filenames.join(','));
						}

						$.ajax({
							url:"{{ url('input/audit_ng_jelas') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								if (data.status == false) {
									$('#loading').hide();
									openErrorGritter('Error!',data.message);
								}else if(data.status == true){
									stat++;
								}
								if (stat == count_point) {
									$('#loading').hide();
									openSuccessGritter('Success!','Save Data Success');
									location.reload();
								}
							},
							error: function(data) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
								return false;
							}
						});
					}
				}else{
					var stat_image_ready = 0;
					for(var k = 1; k <= nomor;k++){
						if ($('#file_'+i+'_'+k).length > 0) {
							stat_image_ready++;
						}
					}
					var stat_image = 0;
					for(var k = 1; k <= nomor;k++){
						if ($('#file_'+i+'_'+k).length > 0) {
							stat_image++;
							var fileData  = $('#file_'+i+'_'+k).prop('files')[0];

							file=$('#file_'+i+'_'+k).val().replace(/C:\\fakepath\\/i, '').split(".");

							var formData = new FormData();
							formData.append('fileData', fileData);
							formData.append('audit_id', audit_id);
							formData.append('audit_index', audit_index);
							formData.append('extension', file[1]);
							formData.append('foto_name', file[0]);
							formData.append('filename',k+'_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
							if (file[0] != '') {
								filenames.push(k+'_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
							}
							
							$.ajax({
								url:"{{ url('upload/file/audit_ng_jelas') }}",
								method:"POST",
								data:formData,
								dataType:'JSON',
								contentType: false,
								cache: false,
								processData: false,
								success:function(data)
								{
									filenames.push(data.filename);
									stat_image++;
								},
								error: function(data) {
									$('#loading').hide();
									openErrorGritter('Error!',data.message);
								}
							});
						}
					}
					if (stat_image == stat_image_ready) {
						var formData = new FormData();
						formData.append('schedule_id', schedule_id);
						formData.append('audit_id', audit_id);
						formData.append('audit_index', audit_index);
						formData.append('audit_point', audit_point);
						formData.append('auditor', auditor);
						formData.append('audit_images', audit_images);
						formData.append('audit_title', audit_title);
						formData.append('periode', periode);
						formData.append('email_date', email_date);
						formData.append('result_check', result_check);
						formData.append('incident_date', incident_date);
						formData.append('origin', origin);
						formData.append('department', department);
						formData.append('area', area);
						formData.append('product', product);
						formData.append('note', note);
						formData.append('chief_foreman', chief_foreman);
						formData.append('manager', manager);
						if (filenames.length == 1) {
							formData.append('filenames', filenames[0]);
						}else{
							formData.append('filenames', filenames.join(','));
						}

						$.ajax({
							url:"{{ url('input/audit_ng_jelas') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								if (data.status == false) {
									$('#loading').hide();
									openErrorGritter('Error!',data.message);
								}else if(data.status == true){
									stat++;
								}
								if (stat == count_point) {
									$('#loading').hide();
									openSuccessGritter('Success!','Save Data Success');
								}
							},
							error: function(data) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
								return false;
							}
						})
					}
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