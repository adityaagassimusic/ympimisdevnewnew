@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
	}

	.datepicker {
		padding: 6px 12px 6px 12px;
	}

	.btn { margin: 2px; }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center style="position: absolute; top: 45%; left: 35%;">
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-refresh"></i> &nbsp; Please Wait ...</span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<embed src='{{ url("uploads/sakurentsu/trial_req/report/Report_") }}{{ Request::segment(4) }}.pdf' type='application/pdf' width='100%' height='600px'>
							</div>
						</div>
						<div class="col-xs-12" style="display: none" id="div_hasil_trial">
							<div class="row">
								<div class="col-xs-12"><center><h3 style="font-weight: bold;">HASIL TRIAL</h3></center></div>

								<div class="col-sm-6">
									<label for="metode_trial" class="col-sm-12 control-label">Metode Trial</label>
									<div class="col-sm-12">
										<textarea class="form-control" id="metode_trial" name="metode_trial" placeholder="Metode Trial"></textarea>
									</div>
									
								</div>

								<div class="col-sm-6">
									<label for="hasil_trial" class="col-sm-12 control-label">Hasil Trial</label>
									<div class="col-sm-12">
										<textarea class="form-control" id="hasil_trial" name="hasil_trial" placeholder="Hasil Trial"></textarea>
									</div>
								</div>

								<div class="col-sm-12" style="margin-top: 10px">
									<div class="col-sm-12">
										<button class="btn btn-success btn-lg" style="width: 100%" onclick="save_result()"><i class="fa fa-check"></i> SUBMIT</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	@endsection

	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
	<script src="{{ url("ckeditor/ckeditor.js") }}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

		jQuery(document).ready(function() {

			$('body').toggleClass("sidebar-collapse");

			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,	
			});


			$('.select4').select2({
				dropdownAutoWidth : true,
				allowClear: true,
				dropdownParent: $('#modalTrial'),
			});

			CKEDITOR.replace('metode_trial' ,{
				filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
			});

			CKEDITOR.replace('hasil_trial' ,{
				filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
			});

			var appr = <?php echo json_encode($approvals); ?>;

			
			if(appr == '1' || '{{ Auth::user()->role_code }}' == 'MIS') {
				$("#div_hasil_trial").show();
			}			

		});

		function save_result() {
			if (CKEDITOR.instances.metode_trial.getData() == '' || CKEDITOR.instances.hasil_trial.getData() == '') {
				openErrorGritter('Error', 'Terdapat kolom yang kosong');
				return false;
			}

			$('#loading').show();

			var formData = new FormData();
			formData.append('form_number', '{{ Request::segment(4) }}');
			formData.append('section', '{{ Request::segment(5) }}');
			formData.append('metode_trial', CKEDITOR.instances.metode_trial.getData());
			formData.append('hasil_trial', CKEDITOR.instances.hasil_trial.getData());

			$.ajax({
				url:"{{ url('update/trial_request/trial_result') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						
						setTimeout(function(){ window.location = "{{ url('uploads/sakurentsu/trial_req/report/Report_'.Request::segment(4).'.pdf') }}"; }, 3000);
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '3000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '3000'
			});
		}

	</script>
	@endsection

