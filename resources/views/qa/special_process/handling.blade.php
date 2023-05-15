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
	<div class="row">
		<?php if (count($audit) > 0){ ?>
			<div class="col-xs-6" style="text-align: center;padding-right: 5px">
				<table style="width: 100%;border:1px solid black">
					<tr>
						<td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%;font-size: 15px" colspan="2">Penanganan Oleh</td>
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
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:2%;">Decision</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:3%;">Note</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:4%;">Hasil Penanganan</th>
			 				<th style="background-color:rgb(126,86,134);color:#FFD700;border:1px solid black;width:3%;">Note Penanganan</th>
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
									<?php if ($audit->decision == 'OK'){ ?>
										<td style="background-color:white;border:1px solid black;width:1%;font-size: 20px">&#9711;</td>
									<?php }else if($audit->decision == 'NS'){ ?>
										<td style="background-color:white;border:1px solid black;width:1%;font-size: 20px">&#8420;</td>
									<?php }else if($audit->decision == 'NG'){ ?>
										<td style="background-color:white;border:1px solid black;width:1%;font-size: 20px">&#9747;</td>
									<?php } ?>
									<td style="background-color:white;border:1px solid black;width:1%;"><?php echo $audit->note ?></td>
									<td style="background-color:white;border:1px solid black;width:30%;">
										<select style="width: 100%" class="form-control select2" id="revision_{{$no}}" onchange="changeRev(this.id)" data-placeholder="Pilih Hasil Penanganan">
											<option value=""></option>
											<option value="Revisi IK">Revisi IK</option>
											<option value="Tidak Revisi IK">Tidak Revisi IK</option>
										</select>
										<div id="divRevisi_{{$no}}" style="display: none;margin-top: 20px;">
											<label class="pull-left" style="color: #822424">Upload Dokumen IK yang Telah Disetujui STD</label>
											<input type="file" onchange="checkDocuments('{{$no}}')" class="form-control" style="width: 100%" name="revision_document_{{$no}}" id="revision_document_{{$no}}">
										</div>
									</td>
									<td style="background-color:white;border:1px solid black;width:30%;"><textarea id="handling_{{$no}}"></textarea></td>
									<script type="text/javascript">
									CKEDITOR.replace('handling_'+'{{$no}}' ,{
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

    function changeRev(id) {
    	$('#divRevisi_'+id.split('_')[1]).hide();
    	if ($('#'+id).val() == 'Revisi IK') {
    		$('#divRevisi_'+id.split('_')[1]).show();
    	}else{
    		$('#divRevisi_'+id.split('_')[1]).hide();
    	}
    }

    var count_point = 0;

	jQuery(document).ready(function() {
		$('.select2').select2({
	    	allowClear:true
	    });
		CKEDITOR.config.toolbar_MA=[ { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
		{ name: 'insert', items: [ 'Image', 'Table' ] } ];

      $('body').toggleClass("sidebar-collapse");
	});

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			window.location.replace("{{url('index/qa/special_process')}}");
		}
	}

	function checkDoc(file) {
		// var file=$('#revision_document_'+indexx).val().replace(/C:\\fakepath\\/i, '').split(".");
		if (file.reverse()[0].match(/pdf/gi)) {
			return true;
		}else{
			return false;
		}
	}

	function checkDocuments(indexx) {
		var file=$('#revision_document_'+indexx).val().replace(/C:\\fakepath\\/i, '').split(".");
		console.log(file.reverse()[0]);
		if (file.reverse()[0].match(/pdf/gi)) {
			$('#loading').hide();
			$('#revision_document_'+indexx).val('');
			openErrorGritter('Error!','Dokumen Revisi IK harus dalam format PDF dan sudah disetujui STD.');
			return false;
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
		if (confirm('Apakah Anda yakin menyelesaikan penanganan?')) {
			$('#loading').show();
			for(var i = 1; i <= parseInt('{{$count_audit}}');i++){
				var id = 'handling_'+i;
				if (CKEDITOR.instances[id].getData() == '') {
					audio_error.play();
					openErrorGritter('Error!','Isi Semua Penanganan');
					$('#loading').hide();
					return false;
				}

				var id = 'revision_'+i;
				if ($('#'+id).val() == 'Revisi IK') {
					var file=$('#revision_document_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");
					if (!checkDoc(file)) {
						audio_error.play();
						openErrorGritter('Error!','Dokumen Revisi IK harus dalam format PDF dan sudah disetujui STD.');
						$('#loading').hide();
						return false;
					}
					if ($('#revision_document_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
						audio_error.play();
						openErrorGritter('Error!','Upload Semua Dokumen Revisi IK');
						$('#loading').hide();
						return false;
					}
				}
			}

			var stat = 0;
			var hasils = [];
			for(var i = 1; i <= parseInt('{{$count_audit}}');i++){
				var ids = $('#id_'+i).val();
				var handling = CKEDITOR.instances['handling_'+i].getData();
				var handled_id = $('#operator_id').text();
				var handled_name = $('#operator_name').text();

				var file = '';

				var fileData = null;

				var revision = $('#revision_'+i).val();

				if (revision == 'Revisi IK') {
					fileData = $('#revision_document_'+i).prop('files')[0];

					file=$('#revision_document_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");
				}

				var formData = new FormData();
				formData.append('id_handling',ids);
				formData.append('handling',handling);
				formData.append('handled_id',handled_id);
				formData.append('handled_name',handled_name);
				formData.append('handling_revision',revision);
				formData.append('fileData', fileData);
				formData.append('extension', file[1]);
				formData.append('foto_name', file[0]);

				$.ajax({
					url:"{{ url('input/handling/qa/special_process') }}",
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
							if (stat == '{{$count_audit}}') {
								openSuccessGritter('Success!',"Penanganan Berhasil Disimpan");
								$('#loading').hide();
								alert('Penanganan Telah Dilaksanakan');
								window.location.replace("{{url('index/qa/special_process')}}");
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