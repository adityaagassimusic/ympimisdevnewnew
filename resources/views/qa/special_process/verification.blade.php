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
		<?php if (count($audit) > 0){ ?>
			<div class="col-xs-6" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px" colspan="2">Cek Efektifitas Oleh</td>
					</tr>
					<tr>
						<td  style="background-color: #ffadad;border:1px solid black;font-size: 18px" id="operator_id">{{$emp->employee_id}}</td>
						<td  style="background-color: #ffadad;border:1px solid black;font-size: 18px" id="operator_name">{{$emp->name}}</td>
					</tr>
				</table>
			</div>
			<div class="col-xs-6" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td colspan="2" style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px">Document</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #abcbff;border:1px solid black;font-size: 18px" id="document">{{$audit[0]->document_number}} - {{$audit[0]->document_name}}</td>
					</tr>
				</table>
			</div>
			<div class="col-xs-12" style="text-align: center;margin-top: 10px">
				<table class="table table-responsive" style="width: 100%;border:1px solid black" id="tableCheck">
					<thead>
						<tr>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:1%;">#</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:3%;">Proses Pekerjaan</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:3%;">Point Pekerjaan</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:3%;">Point Safety</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:2%;">Note</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:2%;">Penanganan</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:7%;">Efektifitas</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:4%;">Note</th>
			 			</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						<?php if (count($audit) > 0): ?>
							@foreach($audit as $audit)
								<tr>
									<input type="hidden" id="id_{{$no}}" value="{{$audit->id}}">
									<td style="background-color:white;border:1px solid black;width:1%;">{{$no}}</td>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->work_process ?></td>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->work_point ?></td>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->work_safety ?></td>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->note ?></td>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->handling ?>
										<br><span style="font-weight: bold;"><?php echo $audit->handling_revision ?></span>
										@if($audit->handling_revision == 'Revisi IK')
										<br>
										<a class="btn btn-success" target="_blank" href="{{url('data_file/qa/special_process/handling')}}/{{$audit->handling_revision_document}}"><i class="fa fa-file"></i></a>
										@endif
									</td>
									<td style="background-color:white;border:1px solid black;width:1%;">
									<div class="col-xs-6">
										<label class="containers">&#9711;
										  <input type="radio" name="condition_{{$no}}" id="condition_{{$no}}" value="OK">
										  <span class="checkmark"></span>
										</label>
									</div>
									<div class="col-xs-6">
										<label class="containers">&#9747;
										  <input type="radio" name="condition_{{$no}}" id="condition_{{$no}}" value="NG">
										  <span class="checkmark"></span>
										</label>
									</div>
									</td>
									<td style="background-color:white;border:1px solid black;"><textarea id="verification_{{$no}}"></textarea></td>
									<script type="text/javascript">
									CKEDITOR.replace('verification_'+'{{$no}}' ,{
								        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
								        height: '200px',
								        toolbar:'MA'
								    });
									</script>
								</tr>
								<?php $no++ ?>
							@endforeach
						<?php endif ?>
					</tbody>
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
		CKEDITOR.config.toolbar_MA=[ { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
		{ name: 'insert', items: [ 'Image', 'Table' ] } ];

      $('body').toggleClass("sidebar-collapse");
	});

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			window.location.replace("{{url('index/qa/special_process')}}");
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
		if (confirm('Apakah Anda yakin menyelesaikan verifikasi?')) {
			$('#loading').show();
			for(var i = 1; i <= parseInt('{{$count_audit}}');i++){
				var id = 'verification_'+i;
				if (CKEDITOR.instances[id].getData() == '') {
					audio_error.play();
					openErrorGritter('Error!','Isi Semua Penanganan');
					$('#loading').hide();
					return false;
				}
			}

			var ids = [];
			var verification = [];
			var verification_id = [];
			var verification_name = [];
			var verification_note = [];
			for(var i = 1; i <= parseInt('{{$count_audit}}');i++){
				ids.push($('#id_'+i).val());
				var decision = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
					decision = $(this).val();
		        });
		        verification.push(decision);
				verification_note.push(CKEDITOR.instances['verification_'+i].getData());
				verification_id.push($('#operator_id').text());
				verification_name.push($('#operator_name').text());
			}

			var data = {
				id_verification:ids,
				verification:verification,
				verification_note:verification_note,
				verification_id:verification_id,
				verification_name:verification_name,
			}

			$.post('{{ url("input/qa/special_process/verification") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!',result.message);
					window.location.replace("{{url('index/qa/special_process')}}");
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