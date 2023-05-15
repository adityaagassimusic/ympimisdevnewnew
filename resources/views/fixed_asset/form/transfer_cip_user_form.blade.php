@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:left;
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
	<h1>
		Form Confirmation CIP
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<b style='font-size: 22px'>ASSET IDENTIFICATION</b>
					<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-2" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Select Asset to Transfer : </span>
						</div>
						<div class="col-xs-5">
							<select class="select2" data-placeholder="Select Asset" style="width:100%" id="asset_list">';
								<option value=""></option>

								<?php 
								foreach ($assets as $ast) {
									echo '<option value="'.$ast->sap_number.'">'.$ast->fixed_asset_name.' ('.$ast->sap_number.')</option>';
								}
								?>

							</select>
						</div>
						<div class="col-xs-2">
							<button class="btn btn-sm btn-success" onclick="add_asset()"><i class="fa fa-plus"></i> Add Asset</button>
						</div>
					</div> -->
					<div class="col-xs-12">
						<table class="table table-bordered" id="table_cip">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%">Fixed Asset Number</th>
									<th style="width: 25%">Fixed Asset Name</th>
									<th style="width: 10%">Date Acquisition</th>
									<th style="width: 8%">Amount</th>
									<th style="width: 10%">Old Plan Use</th>
									<th style="width: 20%">New Usage Term <span class="text-red">*</span></th>
									<th style="width: 20%">New Plan Use</th>
									<th style="width: 50px">Clasification</th>
								</tr>
							</thead>
							<tbody id="body_cip"></tbody>
						</table>
					</div>
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-sm-2 control-label">PIC</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="pic_name" value="{{ $assets[0]->name_pic }}" readonly>
								<input type="hidden" class="form-control" id="pic" value="{{ $assets[0]->pic }}">
							</div>
						</div>
						<br>

						<div class="form-group">
							<label class="col-sm-2 control-label">Department</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="department" placeholder="Department" value="{{ $assets[0]->department }}" readonly>
							</div>
						</div>
						<br>
<!-- 
						<div class="form-group">
							<label class="col-sm-2 control-label">Clasification <span class="text-red">*</span></label>
							<div class="col-sm-3">
								<select class="select2" id="clasification_large" data-placeholder="Select Large Clasification" style="width: 100%">
									<option></option>
								</select>
							</div>
						</div>
						<br>

						<div class="form-group">
							<div class="col-xs-6 col-xs-offset-2">
								<select class="select2" id="clasification_mid" name="clasification_mid" style="width: 100%" data-placeholder="Middle Clasification (lifetime)">
									<option></option>
								</select>
							</div>
						</div>
						<br>

						<div class="form-group">
							<label class="col-sm-2 control-label">Usefull Life (year)</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="usefull" placeholder="Usefull Life" readonly>
							</div>
						</div>
						<br> -->

					<!-- 	<div class="form-group">
							<label class="col-sm-2 control-label">Usage Term</label>
							<div class="col-sm-3">
								<div class="radio">
									<label>
										<input type="radio" name="usage_term" id="not_use" value="not use yet" required>
										Not Use Yet ( if yes fill usage estimation)
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="usage_term" id="soon" value="soon">
										Soon
									</label>
								</div>
								<div class="input-group date" id="usage_tab" style="display: none">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input class="form-control datepicker" type="text" name="usage_est" id="usage_est" placeholder="Select Date Usage Estimation" style="font-size: 15px;">
								</div>
							</div>
						</div> -->
						<div class="form-group">
							<div class="col-sm-8 col-sm-offset-2" style="margin-top: 10px">
								<button class="btn btn-success" onclick="save_form()"><i class="fa fa-check"></i> Submit</button>
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

	var assets = <?php echo json_encode($assets); ?>;
	var clasic = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.plan_use').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchAsset();
	});

	$(function () {
		$('.select2').select2({
			allowClear : true
		});
	})

	function fetchAsset() {
		var body = '';

		$('#table_cip').DataTable().clear();
		$('#table_cip').DataTable().destroy();
		$("#body_cip").empty();

		$.each(assets, function(key, value) {
			body += '<tr>';
			body += '<td style="text-align: right">'+value.sap_number+'</td>';
			body += '<td>'+value.fixed_asset_name+'</td>';
			body += '<td>'+value.request_date+'</td>';
			body += '<td style="text-align: right">$ '+value.amount_usd+'</td>';
			body += '<td>'+value.usage_estimation+'</td>';
			body += '<td>';
			body += '<div class="radio"><label><input type="radio" name="usage_term_'+key+'" id="not_use_'+key+'" value="not use yet" required> Not Use Yet <br> ( if yes fill usage estimation)</label></div><br>';
			body += '<div class="radio"><label><input type="radio" name="usage_term_'+key+'" id="soon_'+key+'" value="soon"> Soon</label></div>';
			body += '</td>';

			body += '<td><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="datepicker form-control plan_use" id="plan_est_'+key+'" placeholder="Select date"></div></td>';
			body += '<td>';
			body += '<select class="select2" id="clasification_large_'+key+'" onchange="changeClasif(this)" data-placeholder="Select Large Clasification" style="width: 100%"><option></option></select>';
			body += '<select class="select2" id="clasification_mid_'+key+'" onchange="changeClasifMid(this)" style="width: 100%;" data-placeholder="Middle Clasification (lifetime)"><option></option></select>';
			body += '<input type="text" class="form-control" id="usefull_'+key+'" placeholder="Usefull Life" readonly>';
			body += '</td>';
			body += '</tr>';
		});

		$("#body_cip").append(body);

		$.each(assets, function(key, value) {
			clasic = <?php echo json_encode($clasification); ?>;
			var	xCategories = [];
			$.each(clasic, function(index2, value2){
				if(xCategories.indexOf(value2.category) === -1){
					xCategories[xCategories.length] = value2.category;
				}

			})

			$("#clasification_large_"+key).empty();
			cat = "<option></option>";

			$.each(xCategories, function(index2, value2){
				cat += "<option value='"+value2+"'>"+value2+"</option>";
			})
			$("#clasification_large_"+key).append(cat);
		})


		$('.plan_use').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		var table = $('#table_cip').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
		});
	}

	function changeClasif(elem) {
		var val = elem.value;

		var ids = $(elem).attr('id');
		ids = ids.split('_')[2];

		$("#clasification_mid_"+ids).empty();
		cat = "<option></option>";

		filteredArray = clasic.filter(function(item)
		{
			return item.category.indexOf(elem.value) > -1;
		});

		$.each(clasic, function(index, value){
			if (value.category == val) {
				cat += "<option value='"+value.category_code+"'>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			}
		})
		$("#clasification_mid_"+ids).append(cat);
	};

	function changeClasifMid(elem) {
		var ids = $(elem).attr('id');
		ids = ids.split('_')[2];

		var txt = $('#clasification_mid_'+ids+' option:selected').text();
		txt = txt.split("( ");
		txt = txt[1].split(" years");

		$("#usefull_"+ids).val(txt[0]);
	};

	

	$('input:radio[name="usage_term"]').change(
		function(){
			if ($(this).is(':checked') && $(this).val() == 'not use yet') {
				$("#usage_tab").show();
			} else {
				$("#usage_tab").hide();
			}
		});

	function add_asset() {
		var data = {
			asset_id : $("#asset_list").val()
		}

		$.get('{{ url("fetch/fixed_asset/asset_list") }}', data, function(result, status, xhr) {
			var body = '';
			body += '<table style="width : 100%">';
			body += '<tr>';
			body += '<td style="width: 30%">Asset Number</td>';
			body += '<td>'+result.assets[0].sap_number+'</td>';
			body += '</tr>';
			body += '<tr>';
			body += '<td>Asset Name</td>';
			body += '<td>'+result.assets[0].fixed_asset_name+'</td>';
			body += '</tr>';
			body += '<tr>';
			body += '<td>Clasification Category</td>';
			body += '<td>'+result.assets[0].classification_category+'</td>';
			body += '</tr>';
			body += '</table><br>';

			$("#div_asset").append(body);
		})

	}

	function delete_asset(elem) {
		$(elem).closest('tr').remove();
	}

	function save_form() {
		if (confirm('Are you sure want to Submit this form?')) {
			if ($('input[type="radio"]:checked').length != ($('input[type="radio"]').length / 2)) {
				openErrorGritter('Error', 'Mohon Lengkapi kolom "Usage Term"');
				return false;
			}

			var stat_radio = true;
			$('input:radio').each(function () {
				var ids = $(this).attr('name');
				ids = ids.split('_')[2];
				if ($(this).prop('checked')) {
					if($(this).val() == 'not use yet' && $("#plan_est_"+ids).val() == '') {
						stat_radio = false;
					}
				}
			});

			if (!stat_radio) {
				openErrorGritter('Error', 'Mohon Lengkapi kolom "New Plan Use"');
				return false;
			}

			// if ($("#clasification_large").val() == '' || $("#clasification_mid").val() == '') {
			// 	openErrorGritter('Error', 'Mohon Lengkapi kolom "Clasification"');
			// 	return false;
			// }



			var ast = [];

			$.each(assets, function(index, value){
				if (($("#clasification_large_"+index).val() == '' || $("#clasification_mid_"+index).val() == '') && $('input[name="usage_term_'+index+'"]:checked').val() == 'soon') {
					openErrorGritter('Error', 'Mohon Lengkapi kolom "Clasification"');
					return false;
				}

				ast.push({'sap_number' : value.sap_number, 'asset_name' : value.fixed_asset_name, 'acq_date' : value.request_date, 'amount_usd' : value.amount_usd, 'plan_use' : value.usage_estimation, 'usage_term' : $('input[name="usage_term_'+index+'"]:checked').val(), 'usage_est' : $("#plan_est_"+index).val(), 'pic' : $('#pic').val(), 'dept' : $("#department").val(), 'cls_cat' : $("#clasification_large_"+index).val(), 'cls' : $("#clasification_mid_"+index).val(), 'usefull' : $("#usefull_"+index).val()});
			});

			var data = {
				details : ast,
				form_number : "{{ Request::segment(5) }}"
			}

			$.post('{{ url("post/fixed_asset/transfer_cip") }}', data, function(result, status, xhr) {
				if (result.status) {
					openSuccessGritter('Success', 'Success');
					window.setTimeout(function(){window.location.href = "{{ url('index/fixed_asset/transfer_cip') }}";}, 2000);
				} else {
					openErrorGritter('Error', result.message);
				}
			})
		}

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