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
						<span style="font-weight: bold; font-size: 16px;">Material Number</span>
						<input type="text" id="material_number" style="width: 100%; height: 40px; font-size: 25px; text-align: center;">
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

		jQuery(document).ready(function() {

			$('body').toggleClass("sidebar-collapse");
			$('.select2').select2({
    			dropdownAutoWidth : true,
    			allowClear:true,
    			minimumInputLength: 3
			});

			$('#modalInputor').modal({
				backdrop: 'static',
				keyboard: false
			});

			cancelAll();

		});

		function cancelAll() {
			$('#material_number').val('');
			$('#model').val('');
		}


		$('#modalInputor').on('hidden.bs.modal', function () {
			$('#material_number').focus();
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

			$("#material_number").focus();
		};

		function buttonImage(elem) {
			$(elem).closest("div").find("input").click();
			// console.log(input);
		}

		function readURL(input,idfile) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					var img = $(input).closest("div").find("img");
					$(img).show();
					$(img)
					.attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);
			}

			$(input).closest("div").find("button").hide();
			// $('#btnImage'+idfile).hide();
		}

		function confirm(){
			if($('#material_number').val() == null || $('#material_number').val() == ""){
				openErrorGritter('Error', "Please Fill the Material Number");
				return false;
			}

			$('#myModal').modal('show');
		}

		function finalConfirm(){
			$("#loading").show();

			var formData = new FormData();

			formData.append('employee_id', $('#employee_id').val());
			formData.append('employee_name',  $('#employee_name').val());
			formData.append('material_number',  $('#material_number').val());
			formData.append('location',  $('#location').val());
			formData.append('file_datas[]', $('#file1').prop('files')[0]);
			formData.append('file_datas[]', $('#file2').prop('files')[0]);
			formData.append('file_datas[]', $('#file3').prop('files')[0]);
			formData.append('file_datas[]', $('#file4').prop('files')[0]);

			$.ajax({
				url:"{{ url('post/packing_outer_documentation') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {
					$("#loading").hide();
					
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
					$('#material_number').focus();
				},
				error: function (response) {
					openErrorGritter('Error!', response.message);
				},
			})
			
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