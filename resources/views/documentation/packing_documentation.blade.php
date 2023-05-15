@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}

	#tableMenuList td:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tablemenuu> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #e0e0e0;
	}

	#tableResult > thead > tr > th {
		border:rgba(126,86,134,.7);
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #ddd;
	}

	#tablehistory > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	input[type="radio"] {
	}

	#loading { display: none; }

	.radio {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 16px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default radio button */
	.radio input {
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
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}
</style>
@stop
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	<p style="position: absolute; color: White; top: 45%; left: 35%;">
		<span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<span class="text-purple"> ({{ $title_jp }})</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" id="data" value="data">
	<div class="row">
		<div class="col-xs-5">
			<div class="row">
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Detail PIC</span>
					<input type="text" id="employee_detail" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly>
					<input type="hidden" id="employee_id">
					<input type="hidden" id="employee_name">
					<input type="hidden" id="location" value="{{ $loc }}">
				</div>

				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Serial Number</span>
					@if($loc == "Clarinet")
					<input maxlength="8" type="text" id="serial" style="width: 100%; height: 40px; font-size: 25px; text-align: center;">

					<input type="hidden" id="gmc_latch" class="form-control">
					<input type="hidden" id="desc_latch" class="form-control">

					<div id="latch">
						<span style="font-weight: bold; font-size: 16px;">Masuk Kategori Reed Baru<span style="color:red">*</span>	</span>
						<div style="height: 40px;vertical-align: middle;border: 1px solid #d2d6de;">
							<label class="radio" style="margin-top: 5px;margin-left: 5px">Ya
								<input type="radio" id="latch_information" name="latch_information" value="Ya">
								<span class="checkmark"></span>
							</label>
							&nbsp;&nbsp;
							<label class="radio" style="margin-top: 5px">Tidak
								<input type="radio" id="latch_information" name="latch_information" value="Tidak">
								<span class="checkmark"></span>
							</label>
						</div>
					</div>
					@else
					<input maxlength="8" type="text" id="serial" style="width: 100%; height: 40px; font-size: 25px; text-align: center;">
					@endif
					<!-- <input maxlength="8" type="text" id="serial" style="width: 100%; height: 40px; font-size: 25px; text-align: center;"> -->
				</div>

				<div class="col-xs-12" id="three_man_label" style="display: none">
					<label style="font-weight: bold; color: red; text-align: center; width: 100%">*NEW LABEL 3 MAN INFORMATION*</label>
					<label>Label Position : </label>
					<img src="#" id="img_label" style="max-width: 50%">

					<div>
						<label>Evidence : </label>
						<input type="file" onchange="readURL(this,'');" id="file_label" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="environment" class="file">
						<button class="btn btn-primary btn-lg" id="btnImage_label" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>

						<center><img id="blah_label" src="" style="display: none;width: 40%" alt="your image" /></center>
					</div>

				</div>

			</div>
		</div>

		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Foto</span>
					<!-- <input type="file" id="photo" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled> -->

					<input type="file" onchange="readURL(this,'');" id="file1" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="environment" class="file">
					<button class="btn btn-primary btn-lg" id="btnImage1" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>

					<img id="blah1" src="" style="display: none;width: 100%" alt="your image" />
				</div>

				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Foto</span>
					<!-- <input type="file" id="photo" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled> -->

					<input type="file" onchange="readURL(this,'');" id="file2" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="environment" class="file">
					<button class="btn btn-primary btn-lg" id="btnImage2" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>

					<img id="blah2" src="" style="display: none;width: 100%" alt="your image" />
				</div>

				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Foto</span>
					<!-- <input type="file" id="photo" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled> -->

					<input type="file" onchange="readURL(this,'');" id="file3" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="environment" class="file">
					<button class="btn btn-primary btn-lg" id="btnImage3" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>

					<img id="blah3" src="" style="display: none;width: 100%" alt="your image" />
				</div>

				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Foto</span>
					<!-- <input type="file" id="photo" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled> -->

					<input type="file" onchange="readURL(this,'');" id="file4" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="environment" class="file">
					<button class="btn btn-primary btn-lg" id="btnImage4" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>

					<img id="blah4" src="" style="display: none;width: 100%" alt="your image" />
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<button onclick="confirm()" class="btn btn-success" class="btn btn-danger btn-sm" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px">
				CONFIRM
			</button>
		</div>
	</div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Konfirmasi Data</h4>
			</div>
			<div class="modal-body">
				Apakah anda yakin ingin Konfirmasi Dokumentasi Berikut?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button onclick="finalConfirm()" href="#" type="button" class="btn btn-success">Konfirmasi</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalInputor">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Inputor</label>
							<select class="form-control select2" name="inputor" onchange="inputorInput()" id='inputor' data-placeholder="Select Inputor" style="width: 100%;">
								<option value="">Select Inputor</option>
								@foreach($employees as $employee)
								<option value="{{ $employee->employee_id }}_{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection
	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		var three_man_stat = false;

		jQuery(document).ready(function() {

			$('body').toggleClass("sidebar-collapse");
			$('.select2').select2({
				dropdownAutoWidth : true,
				allowClear:true,
				minimumInputLength: 3
			});

			$('#latch').hide();

			$('#modalInputor').modal({
				backdrop: 'static',
				keyboard: false
			});

			cancelAll();
		});

		var delay = (function() {
			var timer = 0;
			return function(callback, ms) {
				clearTimeout(timer);
				timer = setTimeout(callback, ms);
			};
		})();

		$("#serial").on("input", function() {
			delay(function() {
				if ($("#serial").val().length < 6) {
					$("#serial").val("");
				}
			}, 500);
		});


		$('#serial').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if ($("#serial").val().length == 8 && $("#serial").val().substr(0, 2) == '21') {
					cek_gmc_cl($("#serial").val());
				} else if ($("#serial").val().length == 6) {
					if ($('#location').val() == "Clarinet") {
						cek_gmc_cl($("#serial").val());
					}
				}
				else {
					openErrorGritter('Error!', 'Serial Number invalid.');
					audio_error.play();
					$("#serial").val("");
					$('#latch').hide();
				}
			}
		});



		function cancelAll() {
			$('#serial').val('');
		}


		$('#modalInputor').on('hidden.bs.modal', function () {
			$('#serial').focus();
		});

		function inputorInput(){
			$('#modalInputor').modal('hide');

			var inputor = $('#inputor').val().split("_");

			$('#employee_detail').text('');
			$('#employee_detail').val(inputor[0]+' - '+inputor[1]);
			$('#employee_id').text('');
			$('#employee_id').val(inputor[0]);
			$('#employee_name').text('');
			$('#employee_name').val(inputor[1]);

			$("#serial").focus();
		};

		function buttonImage(elem) {
			$(elem).closest("div").find("input").click();
		}

		function readURL(input,idfile) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					var img = $(input).closest("div").find("img");
					$(img).show();
					$(img).attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);
			}

			$(input).closest("div").find("button").hide();
			// $('#btnImage'+idfile).hide();
		}

		function confirm(){

			if($('#serial').val() == null || $('#serial').val() == ""){
				openErrorGritter('Error', "Please Fill the Serial Number");
				return false;
			}

			var serial_number = $("#serial").val();

			if (letterCounter(serial_number) >= 2) {
				openErrorGritter('Error!', 'Serial number tidak sesuai.');
				$("#serial").val("");
				audio_error.play();
				return false;
			}

			// if ($("#three_man_label").is(":hidden") == false && typeof $('#file_label').prop('files')[0] === 'undefined') {
			// 	openErrorGritter('Error!', 'Please Upload Evidence Label');
			// 	audio_error.play();
			// 	return false;
			// }

			$('#myModal').modal('show');
		}

		function finalConfirm(){
			$("#loading").show();

			var formData = new FormData();

			if ($('#location').val() == "Clarinet") {
				if ($('#gmc_latch').val() != "") {
					if ($('input[id="latch_information"]:checked').val()) {
						formData.append('latch_information', $('input[id="latch_information"]:checked').val());
					}
					else{
						$("#loading").hide();
						openErrorGritter('Error!', 'Mohon Isi Kondisi Reed nya');
						$('#myModal').modal('hide');
						return false;
					}
					formData.append('gmc_latch', $('#gmc_latch').val());
					formData.append('desc_latch', $('#desc_latch').val());
				}
			}

			if (three_man_stat) {
				if (typeof $('#file_label').prop('files')[0] === 'undefined') {
					openErrorGritter('Error!', 'Please Upload Evidence Label');
					audio_error.play();
					$("#loading").hide();
					$('#myModal').modal('hide');
					return false;
				}
			}

			formData.append('employee_id', $('#employee_id').val());
			formData.append('employee_name',  $('#employee_name').val());
			formData.append('serial_number',  $('#serial').val());
			formData.append('location',  $('#location').val());
			formData.append('file_datas[]', $('#file1').prop('files')[0]);
			formData.append('file_datas[]', $('#file2').prop('files')[0]);
			formData.append('file_datas[]', $('#file3').prop('files')[0]);
			formData.append('file_datas[]', $('#file4').prop('files')[0]);
			formData.append('file_evidence', $('#file_label').prop('files')[0]);
			formData.append('three_man_stat', three_man_stat);

			$.ajax({
				url:"{{ url('post/packing_documentation') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {
					$("#loading").hide();


					$('#latch').hide();
					$("#gmc_latch").val("");
					$("#desc_latch").val("");
					
					openSuccessGritter('Success', response.message);
					cancelAll();
					$('#myModal').modal('hide');
					$('#btnImage1').show();
					$('#btnImage2').show();
					$('#btnImage3').show();
					$('#btnImage4').show();
					$('#file1').hide();
					$('#file2').hide();
					$('#file3').hide();
					$('#file4').hide();
					$('#blah1').hide();
					$('#blah2').hide();
					$('#blah3').hide();
					$('#blah4').hide();
					$('#file1').val("");
					$('#file2').val("");
					$('#file3').val("");
					$('#file4').val("");
					$('#serial').focus();
					$('#btnImage_label').show();
					document.getElementById("file_label").value = "";
					$('#three_man_label').hide();
					three_man_stat = false;
					$('#blah_label').hide();
				},
				error: function (response) {
					openErrorGritter('Error!', response.message);
				},
			})	
		}


		function cek_gmc_cl(elem){
			if (elem.length >= 6) {
				var serial_number = elem;

				var data = {
					serial_number : serial_number,
					location : $('#location').val()
				}

				$.get('{{ url("fetch/gmc/packing/cl") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							// $("#serial").val(result.sn.serial_number);
							$("#gmc_latch").val(result.sn.material_number);
							$("#desc_latch").val(result.sn.material_description);
							$('#latch').show();

							if (result.label_image != '') {
								three_man_stat = true;
								$("#three_man_label").show();
								$('#img_label').attr('src','http://10.109.52.4/mirai/public/files/label/three_man/'+result.label_image);
							} else {
								three_man_stat = false;
								$("#three_man_label").hide();
								$('#img_label').attr('src','');
							}
						}
						else{
							// openErrorGritter('Error!', result.message);
							// audio_error.play();
							$("#gmc_latch").val("");
							$("#desc_latch").val("");
							$('#latch').hide();
							if (result.label_image != '') {
								three_man_stat = true;
								$("#three_man_label").show();
								$('#img_label').attr('src','http://10.109.52.4/mirai/public/files/label/three_man/'+result.label_image);
							} else {
								three_man_stat = false;
								$("#three_man_label").hide();
								$('#img_label').attr('src','');
							}
						}
					}
					else{
						alert('Disconnected from server');
					}
				});
			}
		}

		function letterCounter(x) {
			return x.replace(/[^a-zA-Z]/g, '').length;
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

	</script>
	@endsection