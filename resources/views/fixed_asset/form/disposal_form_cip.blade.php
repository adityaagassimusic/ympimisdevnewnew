@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of {{$page}}
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Fixed Asset Scrap.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Disposal Scrap</a> &nbsp;
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Disposal Fixed Asset Sale.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Disposal Sale</a> &nbsp;
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					@foreach($asset_list as $al)
					<div class="col-xs-6 master_asset" style="padding-top: 10px; padding-bottom: 10px; border: 1px solid #605ca8">
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="hidden" class="asset_name" value="{{ $al->fixed_asset_name }}">
										<select class="form-control select2 asset_id" data-placeholder="Select Asset" style="width: 100%" disabled="">
											<option value="{{ $al->sap_number }}">{{ $al->sap_number }} - {{ $al->fixed_asset_name }}</option>
										</select>
									</div>
									<div class="col-xs-2" style="padding-right: 0px">
										<button class="btn btn-xs btn-danger pull-right" onclick="delete_asset(this)"><i class="fa fa-trash"></i></button>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="file" accept="image/*" class="asset_picture">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control asset_no" placeholder="fixed asset number" readonly value="{{ $al->sap_number }}">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control asset_cls" placeholder="fixed asset Clasification" readonly value="{{ $al->classification_category }}">
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control section" placeholder="Section Control" value="{{ $al->section }}" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control disposal_reason" placeholder="input reason"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason (Japanese): <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control disposal_reason_jp" placeholder="input reason (Japanese)"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Mode : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<select class="form-control select2 mode" data-placeholder="select mode" onchange="pilihMode(this)" style="width: 100%">
											<option value=""></option>
											<option value="SALE">SALE</option>
											<option value="SCRAP">SCRAP</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12 div_quot" style="padding-bottom: 1%; visibility: hidden;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Quotation : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" class="quotation_file" accept="application/pdf">
									</div>
								</div>
							</div>
						</form>
					</div>
					@endforeach

					<div class="col-xs-12" style="padding-top: 10px">
						<center><button class="btn btn-success btn-lg" id="create_btn" style="width: 100%" onclick="submit()"><i class="fa fa-check"></i> Request Disposal </button></center>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var no = 0;
	var investment_list = [];
	var pic_list = [];
	var asset_list = <?php echo json_encode($asset_list); ?>;


	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.select2').select2();

		$('.select3').select2({
			dropdownParent: $('#modalFill'),
		})

		$('.select4').select2({
			dropdownParent: $('#modalEdit'),
		})

	});

	$("form#form_master").submit(function(e) {
		if( document.getElementById("asset_picture").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Asset Photo');
			return false;
		}

		if ($("#mode").val() == "SALE") {
			if(document.getElementById("quotation_file").files.length == 0 ){
				openErrorGritter('Error!', 'Please Add Quotation File');
				return false;
			}
		}

		if( $("#disposal_reason").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason');
			return false;
		}

		if( $("#disposal_reason_jp").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason Japanese');
			return false;
		}

		if( $("#mode").val() == ""){
			openErrorGritter('Error!', 'Please Select Mode');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/fixed_asset/disposal") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();


				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function pilihMode(elem) {
		if ($(elem).val() == "SCRAP") {
			$(elem).parent().parent().parent().children('.div_quot').css('visibility', 'hidden');
		} else if($(elem).val() == "SALE") {
			$(elem).parent().parent().parent().children('.div_quot').css('visibility', 'visible');
		}
	}

	function delete_asset(elem) {
		$(elem).closest(".master_asset").remove();
	}

	function submit() {

		var stat = true;
		var formData = new FormData();

		var asset_name = [];
		var asset_id = [];
		var asset_no = [];
		var classification = [];
		var section = [];
		var reason = [];
		var reason_jp = [];
		var mode = [];

		$('.asset_name').each(function(i, obj) {
			asset_name.push($(obj).val());
		});

		$('.asset_id').each(function(i, obj) {
			asset_id.push($(obj).val());
		});

		$('.asset_picture').each(function(i, obj) {
			if ($(obj).prop('files')[0]) {
				formData.append('asset_picture_'+i, $(obj).prop('files')[0]);
			} else {
				stat = false;
			}
		});

		$('.asset_no').each(function(i, obj) {
			asset_no.push($(obj).val());
		});

		$('.asset_cls').each(function(i, obj) {
			classification.push($(obj).val());
		});

		$('.section').each(function(i, obj) {
			section.push($(obj).val());
		});

		$('.disposal_reason').each(function(i, obj) {
			if ($(obj).val() == '') {
				stat = false;
			}

			reason.push($(obj).val());
		});

		$('.disposal_reason_jp').each(function(i, obj) {
			if ($(obj).val() == '') {
				stat = false;
			}

			reason_jp.push($(obj).val());
		});
		
		$('.mode').each(function(i, obj) {
			if ($(obj).val() == '') {
				stat = false;
			}


			if ($(obj).val() == 'SALE' && $(obj).parent().parent().parent().children('.div_quot').find('.quotation_file').prop('files').length == 0) {
				stat = false;
			}

			mode.push($(obj).val());
		});

		$('.quotation_file').each(function(i, obj) {
			formData.append('quot_file_'+i, $(obj).prop('files')[0]);
		});

		if (!stat) {
			openErrorGritter('Error', 'Please Fill All Field!');
			return false;
		}		

		formData.append('asset_name', asset_name);
		formData.append('asset_id', asset_id);
		formData.append('asset_no', asset_no);
		formData.append('classification', classification);
		formData.append('section', section);
		formData.append('reason', reason);
		formData.append('reason_jp', reason_jp);
		formData.append('mode', mode);

		$("#loading").show();

		$.ajax({
			url:"{{ url('post/fixed_asset/cip_disposal') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				openSuccessGritter("Success", "Disposal Form Successfully Created");
				setTimeout(function (){
					window.location.href = "{{ url('index/fixed_asset/disposal') }}";
				}, 2000);
			},
			error: function (response) {
				console.log(response.message);
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