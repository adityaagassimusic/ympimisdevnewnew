@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">

	.nmpd-grid {border: none; padding: 20px; top: 100px !important}
	.nmpd-grid>tbody>tr>td {border: none;}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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

	.disabledTab{
		pointer-events: none;
	}

	input.currency {
		text-align: left;
		padding: 2px 2px;
	}

	.input-group-addon {
		padding: 2px 5px;
	}

</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-upload"></i> Upload Packing List</a>

			<a href="javascript:void(0)" onclick="openModalSuratJalan()" class="btn btn-md bg-green" style="color:white"><i class="fa fa-plus"></i> Create Surat Jalan</a>
		</li>
	</ol>
	<br>
</section>
@endsection

@section('content')
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif

	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Data Packing List</span></h3>

					<div class="row">
						<div class="col-xs-12">
							<div class="box no-border">
								<div class="box-header">
								</div>
								<div class="box-body" style="padding-top: 0;">
									<table id="outstandingTable" name="outstandingTable" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 1%">No</th>
												<th style="width: 2%">No Invoice</th>
												<th style="width: 8%">No Surat Jalan</th>
												<th style="width: 4%">Tanggal Kedatangan</th>
												<th style="width: 5%">Vendor</th>
												<th style="width: 2%">Total Material</th>
												<th style="width: 5%">Action</th>
											</tr>
										</thead>
										<tbody id="tableBodyFinish">
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade in" id="modal_uploud">
	<form id ="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Upload File Packing List</h4>
					<br>
				</div>

				<div class="form-group row" align="right">
					<label class="col-sm-3">Tanggal Kedatangan<span class="text-red">*</span></label>
					<div class="col-sm-6">
						<div class="input-group">
							<input type="text" class="form-control datepicker" id="createStart" name="createStart"placeholder="Select Date" >
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>

				<div class="form-group row" align="right">
					<label class="col-sm-3">Vendor</label>
					<div class="col-xs-6" align="left">
						<select class="form-control select2" data-placeholder="Select Vendor" name="vendor" id="vendor" style="width: 100%">
							<option value="" selected></option>
							<option value="YMMJ">YMMJ</option>
							<option value="YCJ">YCJ</option>
						</select>
					</div>
				</div>

				<div class="form-group row" align="right">
					<label class="col-sm-3">Upload File</label>
					<div class="col-xs-6" align="left">
						<input type="file" name="file" id="file">
					</div>
				</div>

				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button> -->
					<div class="col-xs-12" style="padding-top: 20px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="$('[name=importForm]').submit();" >Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade in" id="modal_suratjalan">
	<form id ="importFormSurat" method="post" enctype="multipart/form-data" action="{{ url('create/suratjalan') }}">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<center>
						<div class="col-xs-12" style="background-color: #605ca8">
							<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CREATE SURAT JALAN</h1>
						</div>
					</center>
				</div>

				<div class="row">
					<div class="col-xs-12" style="padding-bottom: 10px; padding-top: 4px;">
						<div class="col-md-12" style="padding-top: 10px;">
							<div class="col-xs-6">
								<div class="form-group">
									<label>No Surat Jalan<span class="text-red">*</span></label>
									<input type="text" style="width: 100%" class="form-control" name="no_surat_jalan" id="no_surat_jalan" placeholder="Masukkan No Surat Jalan">
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Supllier<span class="text-red">*</span></label>
									<select class="form-control select2" id="supplier_code" name="supplier_code" data-placeholder='Supplier' style="width: 100%">
										<option value="">&nbsp;</option>
										@foreach($vendor as $ven)
										<option value="{{$ven->supplier_name}}">{{$ven->supplier_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>


						<div class="col-md-12" style="margin-bottom: 5px">
							<input type="text" name="lop" id="lop" value="1" hidden>

							<div class="col-xs-2" id="from">
								<label for="exampleInputEmail1">Choose GMC</label>
								<select class="form-control select2" id="gmc1" name="gmc1" data-placeholder='Choose GMC' style="width: 100%" onchange="checkEmp(this.value, this.id)">
									<option value="">&nbsp;</option>
									@foreach($material_sr as $row)
									<option value="{{$row->material_number}}">{{$row->material_number}}</option>
									@endforeach
								</select>
							</div>

							<div class="col-xs-6">
								<label for="exampleInputEmail1">Description</label>
								<input class="form-control" style="width: 100%; text-align: center;" type="text" id="description1" name="description1" placeholder="Description" readonly>
							</div>
							<div class="col-xs-2">
								<label for="exampleInputEmail1">Quantity</label>
								<!-- <input class="form-control" style="width: 100%; text-align: center;" type="text" id="quantity" placeholder="Quantity"> -->
								<input type="text" style="font-size:20px; width: 100%; height: 35px;text-align: center; " class="numpad1 input" id="qty1" name="qty1" >
							</div>
							<div class="col-xs-2" style="padding-top: 25px; right: 13px">
								<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
								<!-- <a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a> -->
							</div>    
						</div>
						<div id="tambah"></div>
					</div>
				</div>

				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button> -->
					<div class="col-xs-12" style="padding-top: 20px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="$('[name=importFormSurat]').submit();" >Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal modal-danger fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<input type="hidden" id="ids">
				<input type="hidden" id="no_inv">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
			</div>
			<div class="modal-body">
				Apakah anda yakin ingin Hapus Data Packing List Ini ?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<a name="modalbuttoncancel" type="button"  onclick="DeleteForm()" class="btn btn-danger">Yes</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalViewMaterial">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL REQUEST</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_pel" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th id="no_invc" style="width: 2%;">No Invoice</th>
									<th id="no_sj" style="width: 1%;" >No Surat Jalan</th>	
									<th style="width: 1%;">No Pallet</th>
									<th style="width: 1%;">GMC</th>
									<th style="width: 4%;">Description</th>
									<th style="width: 1%;">Quantity</th>
								</tr>
							</thead>
							<tbody id="detail_material_pel_body">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalAdd">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">EDIT MATERIAL REQUEST</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-12">
							<div class="form-group">
								<label>Request Material Produksi<span class="text-red">*</span></label>
								<input type="hidden" style="width: 100%" class="form-control" id="id_material">

							</div>
						</div>

						<div class="row">
							<div class="col-xs-12" >
								<div class="col-xs-12" >
									<div class="col-xs-3" style="font-weight: bold;">
										GMC
									</div>
									<div class="col-xs-5" style="font-weight: bold;">
										Description
									</div>
									<div class="col-xs-2" style="font-weight: bold;">
										Quantity
									</div>
									<div class="col-xs-2" style="font-weight: bold;">
										<button class="btn btn-primary btn-sm pull-right" onclick="add_employee()"><i class="fa fa-plus"></i>&nbsp; Add</button>
									</div>
								</div>
								<div class="col-xs-"12 id="div_process">
								</div>
							</div>
							<div class="modal-footer">
								<div class="col-xs-12" style="padding-top: 20px">
									<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="save_data()">Save</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_edit_pk1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">EDIT MATERIAL REQUEST</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12" style="padding-bottom: 10px; padding-top: 4px;">
						<div class="col-md-12" style="padding-top: 10px;">
							<div class="col-xs-6">
								<div class="form-group">
									<label>No Invoice<span class="text-red">*</span></label>
									<input type="text" style="width: 100%" class="form-control" name="no_invoice" id="no_invoice" placeholder="no_invoice" readonly>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Date Arrival<span class="text-red">*</span></label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="datearrival" name="datearrival">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_create" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">GMC</th>
									<th style="width: 10%;">Description</th>
									<th style="width: 1%;">Quantity</th>
								</tr>
							</thead>
							<tbody id="detail_material_create_body">
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<div class="col-xs-12" style="padding-top: 20px">
							<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="save_data_material()">Save</button>
						</div>
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
<script src="{{ url("js/jquery.numpad.js")}}"></script>

<!-- <script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script> -->

<script>

	no = 2;
	nums = 1;
	inv_list = "";
	exchange_rate = [];
	item_list = "";
	limitdate = "";
	var ids;
	var detail_request = [];

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%; "></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		// fillTableOutstanding();
		fetchHistoryFinish();
		$('#description1').val('');
		$('#qty1').val('');
		$('#gmc1').val('');
		$('#supplier_code').val('');
		$('#no_surat_jalan').val('');

		$('body').toggleClass("sidebar-collapse");
		$('#st_date').datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,
		});
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true	
		});

		$('.select2').select2({
			dropdownAutoWidth : true,
			// dropdownParent: $("#modal_suratjalan"),
			allowClear:true,
			tags: true
		});

		$('.numpad1').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('#datearrival').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});

	});

	// Submit Form

	$("form#importForm").submit(function(e) {
		if ($('#file').val() == '' || $('#vendor').val() == '' || $('#createStart').val() == '') {
			openErrorGritter('Error!', 'Data Tidak Boleh Kosong');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData()
		formData.append('createStart', $("#createStart").val());
		formData.append('vendor', $("#vendor").val());
		formData.append('file', $('#file').prop('files')[0]);;

		$.ajax({
			url: '{{ url("import/packinglist") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					fetchHistoryFinish();
					$('#file').val("");
					$("#vendor").val(" ").trigger('change.select2');
					$('#createStart').val("")
					$('#modal_uploud').modal('hide');
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					$('#vendor').val("");

					openErrorGritter('Error!', result.message);
				}
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

	$('').click(function(){
		var category = $('#qty1').val();
		if(category == ''){
			alert('All field must be filled');	
		}
		else{
			$('.nav-tabs > .active').next('li').find('a').trigger('click');
		}
	});

	function add_employee() {
		var proc = "";
		proc += '<tr><td><div class="col-xs-3" style="margin-top: 5px">';
		proc += '<select class="select2 gmck" id="gmc'+nums+'" name="gmc'+nums+'" style="width: 100%" onchange="checkEmp(this.value, this.id)" data-placeholder= "Choose GMC" ><option value="">&nbsp;</option> @foreach($material_sr as $row)<option value="{{$row->material_number}}"">{{$row->material_number}}</option> @endforeach</select>';
		proc += '</div>';
		proc += '<div class="col-xs-5" style="margin-top: 5px">';
		proc += '<input type="text" class="form-control description2" id="description'+nums+'" name="description'+nums+'" placeholder="Description">';
		proc += '</div>';
		proc += '<div class="col-xs-2" style="margin-top: 5px;">';
		proc += '<input type="text" style="font-size:20px; width: 100%; height: 35px;text-align: center;" class="quantitycheck2 numpad'+nums+'" input" id="qty'+nums+'" name="qty'+nums+'">'
		proc += '</div>';
		proc += '<div class="col-xs-2" style="margin-top: 5px; text-align: right;">';
		proc += '<button class="btn btn-danger btn-xs" onclick="deleteEmp(this)"><i class="fa fa-close"></i></button>';
		proc += '</div>';
		proc += '</td></tr>';

		$("#div_process").append(proc);

		$('.numpad'+nums).numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		nums+=1;

		$('.select2').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true,
		});
	}

	function openEdit(no_sjo) {
		var data = {
			no_sj : no_sjo
		}

		$("#div_process").empty();
		$.get('{{ url("fetch/internal/edit/material") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#modalAdd").modal('show');
				var no = 1;
				$.each(result.no_sj, function(index, value){
					var proc = "";
					$("#id_material").val(value.kode_requests);
					proc += '<tr><td><div class="col-xs-3" style="margin-top: 5px">';
					proc += '<input type="text" class="form-control gmc" placeholder="GMC" value="'+value.gmc+'" readonly> <input type="hidden" class="id_no" value="'+value.id+'"> <input type="hidden" class="no_sj" value="'+value.no_surat_jalan+'"> <input type="hidden" class="no_sjl" value="'+no_sjo+'"> <input type="hidden" class="no_case" value="'+value.no_case+'"> <input type="hidden" class="vendor" value="'+value.vendor+'">';
					proc += '</div>';
					proc += '<div class="col-xs-5" style="margin-top: 5px">';
					proc += '<input type="text" class="form-control description" placeholder="Description" value="'+value.description+'" readonly>';
					proc += '</div>';
					proc += '<div class="col-xs-2" style="margin-top: 5px;">';
					proc += '<input type="text" style="font-size:20px; width: 100%; height: 35px;text-align: center;" class="quantitycheck numpad'+no+'" input" value="'+value.quantity+'" id="qty'+no+'" name="qty'+no+'">'
					proc += '</div>';
					proc += '<div class="col-xs-2" style="margin-top: 5px; text-align: right;">';
					proc += '<button class="btn btn-danger btn-xs" onclick="deleteEmp(this)"><i class="fa fa-close"></i></button>';
					proc += '</div>';
					proc += '</td></tr>';

					$("#div_process").append(proc);
				})

				$('.numpad'+no).numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
				no+=1;

				$('.select2').select2({
					dropdownParent: $('#modalAdd'),
					allowClear: true,
				});
			}
		})
	}


	function openEditPk(no_invoice) {
		$("#modal_edit_pk1").modal('show');


		var tableData = "";
		var num=1;

		$('#detail_material_create').DataTable().clear();
		$('#detail_material_create').DataTable().destroy();
		$('#detail_material_create_body').html("");

		$.each(detail_request, function(key, value){
			if (value.no_invoice == no_invoice){
				$('#no_invoice').val(value.no_invoice);
				$('#datearrival').val(value.tanggal_kedatangan);
				tableData += '<tr>';
				tableData += '<td>'+ value.gmc +'</td>';
				tableData += '<td>'+ value.description +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '</tr>';
			}
		});

		$('#detail_material_create_body').append(tableData);

		var table = $('#detail_material_create').DataTable({
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
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'pageLength': 10,
			'searching': true	,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
	}

	function deleteEmp(elem) {
		$(elem).closest('tr').remove();
	}


	function save_data() {

		var ids = [];
		var gmcs = [];
		var no_cases = [];
		var vendors = [];
		var no_sjs = [];
		var no_sjl = [];
		var no_sjks = [];
		var gm = [];
		var descriptions2 = [];
		var quantitychecks2 = [];

		$('.id_no').each(function() {
			ids.push($(this).val());
		}); 
		$('.quantitycheck').each(function() {
			gmcs.push($(this).val());
		});

		$('.no_case').each(function() {
			no_cases.push($(this).val());
		});
		$('.vendor').each(function() {
			vendors.push($(this).val());
		});
		$('.no_sj').each(function() {
			no_sjs.push($(this).val());
		});
		$('.no_sjl').each(function() {
			no_sjl.push($(this).val());
		});

		$('.description2').each(function() {
			descriptions2.push($(this).val());
		});

		$('.quantitycheck2').each(function() {
			quantitychecks2.push($(this).val());
		});

		$('.no_sjk').each(function() {
			no_sjks.push($(this).val());
		});

		$('.gmck').each(function() {
			gm.push($(this).val());
		});

		var data = {
			ids : ids,
			gmcs  : gmcs,
			no_cases : no_cases,
			vendors : vendors,
			no_sjs : no_sjs,
			no_sjl : no_sjl,
			no_sjks : no_sjks,
			gm : gm,
			description2 : descriptions2,
			quantitychecks2 : quantitychecks2
		}

		$.post('{{ url("post/warehouse/edit") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', 'Updated Materials');
				$("#modalAdd").modal('hide');
				fetchHistoryFinish();
			}
		})
	}

	function save_data_material() {

		var dates = $('#datearrival').val();
		var no_invoice = $('#no_invoice').val();
		console.log(dates, no_invoice);
		var data = {
			dates : dates,
			no_invoice : no_invoice
		}

		$.post('{{ url("post/warehouse/edit/material") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', 'Updated Materials');
				$("#modal_edit_pk1").modal('hide');
				fetchHistoryFinish();
			}
		})
	}

	function tambah(id,lop) {
		var id = id;
		var lop = "";
		lop = "lop";

		var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-2'><select class='select2' id='gmc"+no+"' name='gmc"+no+"' style='width: 100%' onchange='checkEmp(this.value, this.id)' data-placeholder='Choose GMC' ><option value=''>&nbsp;</option> @foreach($material_sr as $row)<option value='{{$row->material_number}}'>{{$row->material_number}}</option> @endforeach</select></div><div class='col-xs-6'><input class='form-control' id='description"+no+"' name='description"+no+"'  style='width: 100%; text-align: center;' type='text' placeholder='Description' readonly></div><div class='col-xs-2'><input type='text' style='font-size:20px; width: 100%; height: 35px;text-align: center;' class='numpad"+no+" input' id='qty"+no+"' name='qty"+no+"'></div><div class='col-xs-2' style='padding:0;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+","+lop+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");



		$("#"+id).append(divdata);

		document.getElementById(lop).value = no;
		$('.numpad'+no).numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		no+=1;
		$('.select2').select2({
			dropdownAutoWidth : true,
			dropdownParent: $("#modal_suratjalan"),
			allowClear:true,
			tags: true
		});
	}

	function kurang(elem,lop) {
		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);
		jQuery("#gmc"+newid).attr("name","gmc"+oldid);
		jQuery("#qty"+newid).attr("name","qty"+oldid);


		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {  
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;

			jQuery("#"+newid).attr("id",oldid);
			jQuery("#description"+newid).attr("name","description"+oldid);
			jQuery("#gmc"+newid).attr("name","gmc"+oldid);
			jQuery("#qty"+newid).attr("name","qty"+oldid);


		}
		document.getElementById(lop).value = a;
	}


	function openModalCreate(){
		$('#modal_uploud').modal('show');
	}

	function openModalSuratJalan(){
		$('#modal_suratjalan').modal('show');
	}

	function checkEmp(value,id) {
		var index = id;
		var i = index.replace('gmc','');

		var data = {
			gmc:$('#gmc'+i).val()
		}

		$.get('{{ url("check/gmc")}}',data, function(result, status, xhr){
			if(result.status){
				$.each(result.check_detail_gmc, function(key, value) {
					$('#gmc'+i).val(value.material_number);
					$('#description'+i).val(value. material_description);
				});
			}else{

			}
		});
	}

	function fetchHistoryFinish(){
		$.get('{{ url("fetch/packinglist/warehouse") }}', function(result, status, xhr){
			if(result.status){
				detail_request = [];
				for (var i = 0; i < result.det_materials.length; i++) {
					detail_request.push({id: result.det_materials[i].id,gmc: result.det_materials[i].gmc,description: result.det_materials[i].description,quantity: result.det_materials[i].quantity,no_invoice: result.det_materials[i].no_invoice,no_surat_jalan: result.det_materials[i].no_surat_jalan,tanggal_kedatangan: result.det_materials[i].tanggal_kedatangan,no_case: result.det_materials[i].no_case});
				}
				$('#outstandingTable').DataTable().clear();
				$('#outstandingTable').DataTable().destroy();
				$('#tableBodyFinish').html("");
				var tableData = "";
				var no = 1;
				var total = "";
				var edit_material = "";

				for (var i = 0; i < result.datas.length; i++) {

					tableData += '<tr>';
					tableData += "<td>"+no+"</td>";
					if (result.datas[i].no_invoice == null) {
						tableData += '<td>-</td>';	
						tableData += '<td>'+ result.datas[i].no_surat_jalan +'</td>';
						total = result.datas[i].total; 
						edit_material = '<a onclick="openEdit(\''+result.datas[i].no_surat_jalan+'\');" class="btn btn-warning btn-xs" target="_blank"><i class="fa fa-edit"></i>Edit</a>';

					}else{

						tableData += '<td>'+ result.datas[i].no_invoice +'</td>';
						tableData += '<td>-</td>';	
						total = result.datas[i].total1;
						edit_material = '<a onclick="openEditPk(\''+result.datas[i].no_invoice+'\');" class="btn btn-warning btn-xs" target="_blank"><i class="fa fa-edit"></i>Edit</a>';

					}
					tableData += '<td>'+ result.datas[i].tanggal_kedatangan +'</td>';
					tableData += '<td>'+ result.datas[i].vendor +'</td>';
					tableData += '<td>'+ total +'</td>';
					tableData += '<td><a class="btn btn-primary btn-xs" onclick="detMaterial(\''+result.datas[i].no_surat_jalan+'\',\''+result.datas[i].no_invoice+'\')" style="color:white;"><i class="fa fa-eye"></i> Detail </a> '+edit_material+' <a class="btn btn-danger btn-xs" onclick="delShows(\''+result.datas[i].no_surat_jalan+'\',\''+result.datas[i].no_invoice+'\');" style="color:white;"><i class="fa fa-trash"></i> Delete </a> </td></td>';
					tableData += '</tr>';
					no++;

				}

				$('#tableBodyFinish').append(tableData);

				var table = $('#outstandingTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 7, 25, 50, -1 ],
					[ '7 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						{
							extend: 'copy',
							className: 'btn btn-success',
							text: '<i class="fa fa-copy"></i> Copy',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 7,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function detMaterial(no_surat_jalan,no_invoice){
	// var kode_request = kode_request;
	$('#modalViewMaterial').modal('show');

	var tableData = "";
	var num=1;

	$('#detail_material_pel').DataTable().clear();
	$('#detail_material_pel').DataTable().destroy();
	$('#detail_material_pel_body').html("");

	$.each(detail_request, function(key, value){
		if (value.no_surat_jalan == no_surat_jalan) {
			tableData += '<tr>';
			if(value.no_invoice == null){
				$("#no_invc").hide("");
				$("#no_sj").show("");
				tableData += '<td hidden></td>';
				tableData += '<td>'+ value.no_surat_jalan +'</td>';

			}else{
				$("#no_sj").hide("");
				$("#no_invc").show("");
				tableData += '<td hidden></td>';
				tableData += '<td>'+ value.no_invoice +'</td>';
			}
			tableData += '<td>'+ value.no_case +'</td>';
			tableData += '<td>'+ value.gmc +'</td>';
			tableData += '<td>'+ value.description +'</td>';
			tableData += '<td>'+ value.quantity +'</td>';
			tableData += '</tr>';
			// no++;
		}else if (value.no_invoice == no_invoice){
			tableData += '<tr>';
			if(value.no_invoice == null){
				$("#no_invc").hide("");
				$("#no_sj").show("");
				tableData += '<td hidden></td>';
				tableData += '<td>'+ value.no_surat_jalan +'</td>';
			}else{
				$("#no_sj").hide("");
				$("#no_invc").show("");
				tableData += '<td hidden></td>';
				tableData += '<td>'+ value.no_invoice +'</td>';
			}
			tableData += '<td>'+ value.no_case +'</td>';
			tableData += '<td>'+ value.gmc +'</td>';
			tableData += '<td>'+ value.description +'</td>';
			tableData += '<td>'+ value.quantity +'</td>';
			tableData += '</tr>';
		}
	});

	$('#detail_material_pel_body').append(tableData);


	var table = $('#detail_material_pel').DataTable({
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
			{
				extend: 'copy',
				className: 'btn btn-success',
				text: '<i class="fa fa-copy"></i> Copy',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'print',
				className: 'btn btn-warning',
				text: '<i class="fa fa-print"></i> Print',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}
			]
		},
		'paging': true,
		'lengthChange': true,
		'pageLength': 10,
		'searching': true	,
		'ordering': true,
		'order': [],
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true
	});
}

function delShows(id, no_invoices){
	console.log(no_invoices);
	if (no_invoices == null) {
		$("#ids").val(id);
		$("#no_inv").val(no_invoices);
		console.log(no_inv);
	}else{
		$("#no_inv").val(no_invoices);
		$("#ids").val(id);
	}

	$('#modaldelete').modal('show');
}

function DeleteForm() {

	var ids = $('#ids').val();
	var no_inv = $('#no_inv').val();
	console.log(no_inv);
	var data = {
		id:ids,
		no_inv:no_inv
	}

	$.post('{{ url("delete/gmc/packinglist") }}', data,  function(result, status, xhr){
		if(result.status){
				// fillTable();
				openSuccessGritter('Success', result.message);
				$("#modaldelete").modal('hide');
				fetchHistoryFinish();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
}

$('.select2').select2({
	dropdownAutoWidth : true,
	allowClear: true
});




function getSupplierEdit(elem){

	$.ajax({
		url: "{{ route('admin.pogetsupplier') }}?supplier_code="+elem.value,
		method: 'GET',
		success: function(data) {
			var json = data,
			obj = JSON.parse(json);
			$('#supplier_name_edit').val(obj.name);
			$('#supplier_due_payment_edit').val(obj.duration);
			$('#supplier_status_edit').val(obj.status);
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
		time: '2000'
	});
}


</script>

@endsection