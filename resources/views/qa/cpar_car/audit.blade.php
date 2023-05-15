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
  padding-left: 50px;
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

#tableCheck > tbody > tr > td > p > img {
	width: 200px !important;
}

#tableCheck > tbody > tr > td{
	background-color: white;
	border: 1px solid black;
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
		<input type="hidden" name="audit_id" id="audit_id" value="{{$schedule->audit_id}}">
		<input type="hidden" name="schedule_id" id="schedule_id" value="{{$schedule->id}}">
		<div class="col-xs-6" style="text-align: center;padding-right: 5px">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditor</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="auditor">{{$emp->employee_id}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{$emp->name}}</td>
				</tr>
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" colspan="2">Auditee</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{explode('_',$foreman)[0]}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{explode('_',$foreman)[1]}}</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="text-align: center">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Claim Title</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Area</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Product</td>
				</tr>
				<tr>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{$point_check[0]->audit_title}}</td>
					<td style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{$point_check[0]->area}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{$point_check[0]->product}}</td>
				</tr>
				<tr>
					<td colspan="3" style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px" >Dept</td>
				</tr>
				<tr>
					<td colspan="3" style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px">{{$point_check[0]->department}}</td>
				</tr>
			</table>
			<input type="hidden" id="chief_foreman" value="{{$foreman}}">
			<input type="hidden" id="manager" value="{{$manager}}">
		</div>
		<div class="col-xs-12" style="text-align: center;margin-top: 10px">
			<table class="table table-responsive table-hover" style="width: 100%;border:1px solid black" id="tableCheck">
				<thead>
					<tr>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:1%;font-weight:bold;">#</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Point Check</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Image Reference</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:2%;font-weight:bold;">Condition</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:1%;font-weight:bold;">Upload Evidence</th>
						<th style="background-color:rgb(126,86,134);color:#FFD700;border:2px solid rgb(60, 60, 60);width:3%;font-weight:bold;">Description</th>
					</tr>
				</thead>
				<tbody>
					<input type="hidden" name="count_point" id="count_point" value="{{count($point_check)}}">
					<?php $index = 1; ?>
					<?php $index2 = 0; ?>
					@foreach($point_check as $point_check)
					<tr>
						<td>{{$index}}</td>
						<td><?php echo $point_check->audit_point ?></td>
						@if($point_check->audit_images != null)
						<td><img style="width:100%;cursor:pointer" src="{{url('data_file/qa/ng_jelas_point/'.$point_check->audit_images)}}" class="user-image" alt="User image" onclick="modalImage(this.src)"></td>
						@else
						<td></td>
						@endif
						<td style="font-size:20px;border:2px solid rgb(60, 60, 60);">
							<label class="containers">&#9711;
							  <input type="radio" name="condition_{{$index2}}" id="condition_{{$index2}}" value="OK">
							  <span class="checkmark"></span>
							</label>
							<label class="containers">&#9747;
							  <input type="radio" name="condition_{{$index2}}" id="condition_{{$index2}}" value="NG">
							  <span class="checkmark"></span>
							</label>
						</td>
						<td style="background-color:'+color+';border:2px solid rgb(60, 60, 60);">
							<input type="file" id="file_{{$index2}}" onchange="readURL(this,'{{$index2}}');">
							<br>
							<img width="100px" id="blah_{{$index2}}" src="" style="display: none" alt="your image" />
						</td>
						<td style="font-size:15px;border:2px solid rgb(60, 60, 60);">
						<textarea id="note_{{$index2}}" style="width:100%"></textarea>
						</td>
					</tr>
					<input type="hidden" id="audit_index_{{$index2}}" value="{{$point_check->audit_index}}">
					<script type="text/javascript">
						CKEDITOR.replace('note_'+'{{$index2}}' ,{
					        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
					        height: '100px',
					        toolbar:'MA'
					    });
					</script>
					<?php $index++; ?>
					<?php $index2++; ?>
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
				<a class="btn btn-danger" href="{{url('index/qa/cpar_car')}}" style="width: 100%;font-size: 25px;font-weight: bold;">
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

	jQuery(document).ready(function() {
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];
	});

	function modalImage(url) {
		$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
		$('#modalImage').modal('show');
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

    function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan proses?')) {
			$('#loading').show();
			var stat = 0;
			var count_point = parseInt($('#count_point').val());
			for(var i = 0; i < count_point;i++){
				var result_check = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_check = $(this).val();
		        });
				if ($('#file_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '' || result_check == '') {
					$('#loading').hide();
					openErrorGritter('Error!','Isi Semua Data');
					return false;
				}
			}

			for(var i = 0; i < count_point;i++){
				var file = '';
				var schedule_id = $('#schedule_id').val();
				var audit_id = $('#audit_id').val();
				var chief_foreman = $('#chief_foreman').val();
				var manager = $('#manager').val();
				var audit_index = $('#audit_index_'+i).val();

				var auditor = $('#auditor').text();

				var result_check = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_check = $(this).val();
		        });

		        file=$('#file_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");

		        var note = CKEDITOR.instances['note_'+i].getData();

		        var fileData  = $('#file_'+i).prop('files')[0];

				var formData = new FormData();
				formData.append('fileData', fileData);
				formData.append('audit_id', audit_id);
				formData.append('auditor', auditor);
				formData.append('schedule_id', schedule_id);
				formData.append('result_check', result_check);
				formData.append('note', note);
				formData.append('chief_foreman', chief_foreman);
				formData.append('manager', manager);
				formData.append('audit_index', audit_index);
				formData.append('extension', file[1]);
				formData.append('filename', file[0]);

				$.ajax({
					url:"{{ url('input/qa/cpar_car/audit') }}",
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
								openSuccessGritter('Success!',"Audit Berhasil Disimpan");
								$('#loading').hide();
								alert('Audit Telah Dilaksanakan');
								window.location.replace("{{url('index/qa/cpar_car')}}");
							}
						}else{
							openErrorGritter('Error!',data.message);
							audio_error.play();
							$('#loading').hide();
						}
					},
					error: function(data) {
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
					}
				});
			}
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