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
		List of Label Asset Request
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" style="font-size: 20px; color: white; line-height: 20px !important; font-weight: bold" class="btn btn-success btn-md" onclick="openModalChart()"><i class="fa fa-shopping-cart"></i> <span class="badge badge-light" id="jml_chart">0</span></a>

		</li>
		<li>
			<a data-toggle="modal" data-target="#receiveModal" class="btn btn-warning btn-md" style="color:white"><i class="fa fa-pencil"></i>Receive Label</a>
		</li>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-header"></div>
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 2%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 10%">PIC</th>
								<th style="width: 10%">Location</th>
								<th style="width: 10%">Reason</th>
								<th style="width: 1%">Remark</th>
								<th style="width: 3%">Status Approval</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
						<tbody id="masterBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="receiveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #f39c12; margin-bottom: 5px">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Receive Label</h1>
					</div>
					<div class="col-xs-3">
						<select class="form-control select2" id="select_form" data-placeholder="Select Request Form Number" style="width: 100%">
							<option value=""></option>
						</select>
					</div>
					<div class="col-xs-3">
						<button class="btn btn-warning" type="button" onclick="add_asset()"><i class="fa fa-check"></i> Select</button>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<table style="width: 100%" class="table">
							<thead>
								<tr>
									<th>Asset Name<span class="text-red">*</span></th>
									<th>Asset No<span class="text-red">*</span></th>
									<th>Section Control<span class="text-red">*</span></th>
									<th>Location<span class="text-red">*</span></th>
									<th>PIC<span class="text-red">*</span></th>
								</tr>
							</thead>
							<tbody id="body_asset">
							</tbody>
						</table>

						<div class="form-group">
							<center>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
									<input type="text" style="text-align: center" class="form-control input-lg" id="receive_nik" placeholder="TAP ID CARD HERE">
									<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
								</div>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; margin-bottom: 5px">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Print Label</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<table style="width: 100%" class="table">
							<thead>
								<tr>
									<th>Form Number</th>
									<th>Asset No</th>
									<th>Asset Name</th>
									<th>Section</th>
									<th>Location</th>
									<th>PIC</th>
								</tr>
							</thead>
							<tbody id="body_print">
							</tbody>
						</table>

						<div class="form-group">
							<center>
								<button class="btn btn-success bnt-lg" onclick="print_all()"><i class="fa fa-print"></i> PRINT LABEL</button>
							</center>
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

	var no = 0;
	var asset_list = [];
	var pic_list = [];
	var arr_keranjang = [];


	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.select2').select2({
			dropdownParent: $('#receiveModal'),
		})

		drawData();

	});

	function drawData() {
		var data = {
			status : ['printed', 'received']
		}

		$.get('{{ url("fetch/fixed_asset/label_asset") }}', data, function(result, status, xhr){
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			$("#masterBody").empty();
			var body = "";
			var option = "<option value=''></option>";
			var option_arr = [];
			asset_list = result.datas;

			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.create_at+"</td>";
				body += "<td>"+value.fixed_asset_no+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.location+"</td>";
				body += "<td>"+value.reason+"</td>";
				body += "<td>"+(value.remark || '')+"</td>";
				body += "<td>"+value.status+"</td>";
				body += "<td>";
				if (value.status == 'printed') {
					body += "&nbsp;<button class='btn btn-warning btn-xs' onclick='print("+value.id+")'><i class='fa fa-print'></i> Print</button>";
					body += "&nbsp;<div class='checkbox'><button class='btn btn-success btn-xs'><label><input type='checkbox' class='cek_"+value.form_number+"' onchange='selectAsset(this,\""+value.form_number+"\","+value.id+",\""+value.fixed_asset_no+"\", \""+value.fixed_asset_name+"\", \""+value.location+"\", \""+value.name+"\", \""+value.section+"\")'>&nbsp;<i class='fa fa-shopping-cart'></i></label></button></div>";
				}
				body += "</tr>";

				if (value.status == "printed" || value.status == "acc_label") {
					option_arr.push(value.form_number);
				}
			})

			$("#masterBody").append(body);

			$.each(unique(option_arr), function(index, value){
				option += "<option value='"+value+"'>"+value+"</option>";
			})

			$("#select_form").empty();
			$("#select_form").append(option);

			var table = $('#masterTable').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 25, 50, -1 ],
				[ '25 rows', '50 rows', 'Show all' ]
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

		})
	}


	$("form#form_master").submit(function(e) {

		if ($("#request_reason").val() == "") {
			openErrorGritter('Failed','Please Fill Reason Field');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/fixed_asset/label_asset") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();


					// $('#createModal').modal('hide');

					openSuccessGritter('Success', result.message);

					// location.reload(true);

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

	function save_form() {
		var arr_asset_id = [];
		var arr_asset_name = [];
		var arr_asset_section = [];
		var arr_asset_location = [];
		var arr_asset_pic = [];


		$('.asset_no').each(function(index, value) {
			arr_asset_id.push($(this).val());
		});

		$('.asset_name').each(function(index, value) {
			arr_asset_name.push($(this).val());
		});

		$('.section').each(function(index, value) {
			arr_asset_section.push($(this).val());
		});

		$('.location').each(function(index, value) {
			arr_asset_location.push($(this).val());
		});

		$('.pic').each(function(index, value) {
			arr_asset_pic.push($(this).val());
		});

		if(jQuery.inArray("", arr_asset_id) !== -1) {
			openErrorGritter('Error', 'there is empty field');
			return false;
		}

		if ($("#reason").val() == "") {
			openErrorGritter('Error', 'Reason field is empty');
			return false;
		}

		var data = {
			asset_id : arr_asset_id,
			asset_name : arr_asset_name,
			asset_section : arr_asset_section,
			asset_location : arr_asset_location,
			asset_pic : arr_asset_pic,
			reason : $("#reason").val()
		}

		$.post('{{ url("post/fixed_asset/label_asset") }}', data, function(result, status, xhr){
			openSuccessGritter('Success', 'Success Request Label');
			$("#createModal").modal('hide');
			$("#body_asset").empty();
			$("#reason").val("");

			drawData();
		})
	}

	function print(id) {

		newwindow = window.open('{{ url("print/fixed_asset/label") }}'+'/'+id, 'height=250,width=450');

		if (window.focus) {
			newwindow.focus();
		}
		
	}

	function print_all() {
		if (arr_keranjang.length > 0) {
			var ids = [];
			$.each(arr_keranjang, function(index, value){
				ids.push(value.id);
			})

			newwindow = window.open('{{ url("print/fixed_asset/label_all") }}'+'/'+ids.toString(), 'height=250,width=450');

			if (window.focus) {
				newwindow.focus();
			}
		}
	}

	function add_asset() {
		var id_form = $("#select_form").val();

		$("#body_asset").empty();

		var body = '';
		$.each(asset_list, function(index, value){
			if (value.form_number == id_form) {
				body += '<tr style="margin-bottom: 3px">';

				body += '<td>'+value.fixed_asset_name+'</td>';
				body += '<td>'+value.fixed_asset_no+'</td>';
				body += '<td>'+value.section+'</td>';
				body += '<td>'+value.location+'</td>';
				body += '<td>'+value.name+'</td>';
				body += '</tr>';
			}

		})

		$("#body_asset").append(body);

		$(".select2").select2();

		$("#receive_nik").focus();
	}

	$('#receive_nik').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var nik = $("#receive_nik").val();
			var id_form = $("#select_form").val();

			var data = {
				employee_id : nik,
				form_number : id_form
			}

			$.post('{{ url("post/fixed_asset/label_asset/receive_asset") }}', data, function(result, status, xhr){
				openSuccessGritter('Success', 'Success Receive Label');
				$("#receive_nik").val('');
				$("#receiveModal").modal('hide');
				$("#body_asset").empty();

				drawData();
			})
		}
	});

	function selectAsset(elem, form_number, id, fixed_asset_no, fixed_asset_name, location, name, section) {
		if ($(elem).prop('checked') === true) {
			arr_keranjang.push({
				'form_number' : form_number,
				'id' : id,
				'fixed_asset_no' : fixed_asset_no,
				'fixed_asset_name' : fixed_asset_name,
				'location' : location,
				'name' : name,
				'section' : section
			});
		} else {
			arr_keranjang = arr_keranjang.filter(function( obj ) {
				return obj.id !== id;
			});
		}

		$("#jml_chart").text(arr_keranjang.length);
	}

	function openModalChart() {
		$("#printModal").modal('show');
		$("#body_print").empty();
		var body = '';

		$.each(arr_keranjang, function(index, value){
			body += '<tr>';
			body += '<td>'+value.form_number+'</td>';
			body += '<td>'+value.fixed_asset_no+'</td>';
			body += '<td>'+value.fixed_asset_name+'</td>';
			body += '<td>'+value.section+'</td>';
			body += '<td>'+value.location+'</td>';
			body += '<td>'+value.name+'</td>';
			body += '</tr>';
		})

		$("#body_print").append(body);
	}

	function unique(array){
		return array.filter(function(el, index, arr) {
			return index === arr.indexOf(el);
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