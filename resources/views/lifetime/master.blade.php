@extends('layouts.master')
@section('stylesheets')
<?php use \App\Http\Controllers\AssemblyProcessController; ?>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.autocomplete {
  position: relative;
  display: inline-block;
}
html {
	  scroll-behavior: smooth;
	}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
		<a class="btn btn-primary btn-sm pull-right" href="{{url('index/lifetime/'.$category.'/'.$location)}}" style="margin-right: 5px">
			<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Back
		</a>
		<a class="btn btn-info btn-sm pull-right" href="#report" style="margin-right: 5px" onclick="fillDataReport()">
			<i class="fa fa-file-o"></i>&nbsp;&nbsp;Report
		</a>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add {{ucwords($category)}}
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header" style="background-color: orange">
					<h3 class="box-title"><b>Master Data</b></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableItem" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">Product</th>
										<th style="width: 3%">Tag</th>
										<th style="width: 3%">Item Code</th>
										<th style="width: 3%">Name</th>
										<th style="width: 3%">Type</th>
										<th style="width: 3%">Number</th>
										<th style="width: 3%">Alias</th>
										<th style="width: 3%">Made In</th>
										<th style="width: 3%">Availability</th>
										<th style="width: 2%">Lifetime Limit</th>
										<th style="width: 2%">Lifetime</th>
										<th style="width: 2%">Repair</th>
										<th style="width: 3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableItem">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header" style="background-color: lightgreen">
					<h3 class="box-title" style="font-weight: bold;">Lifetime History</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body" id="report">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableLifetime" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">Product</th>
										<th style="width: 3%">Item Code</th>
										<th style="width: 3%">Item Name</th>
										<th style="width: 3%">Made In</th>
										<th style="width: 2%">Lifetime</th>
										<th style="width: 2%">Repair</th>
										<th style="width: 2%">Created At</th>
									</tr>
								</thead>
								<tbody id="bodyTableLifetime">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header" style="background-color: lightskyblue">
					<h3 class="box-title" style="font-weight: bold;">Repair History</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body" id="report">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableRepair" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">Product</th>
										<th style="width: 3%">Item Code</th>
										<th style="width: 3%">Item Name</th>
										<th style="width: 3%">Made In</th>
										<th style="width: 2%">Lifetime</th>
										<th style="width: 2%">Repair</th>
										<th style="width: 2%">Created At</th>
									</tr>
								</thead>
								<tbody id="bodyTableRepair">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit {{ucwords($category)}}</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Product<span class="text-red">*</span></label>
									<input type="hidden" name="id" id="id">
									<div class="col-sm-5" align="left" id="divEditProduct">
										<select class="form-control" data-placeholder="Select Product" name="edit_product" id="edit_product" style="width: 100%">
											<option value=""></option>
											@foreach($product2 as $product2)
											<option value="{{$product2}}">{{$product2}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_tag" placeholder="Scan RFID di Sini" required>
									</div>
									<div class="col-sm-1" style="padding-left: 0px;">
										<button onclick="$('#edit_tag').val('');$('#edit_tag').focus()" class="btn btn-danger btn-sm pull-left"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Name<span class="text-red">*</span></label>
									<div class="col-sm-5 autocomplete">
										<input type="text" class="form-control" id="edit_item_name" placeholder="Item Name" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Type<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_item_type" placeholder="Item Type" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Alias<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_item_alias" placeholder="Item Alias (Nama Pendek)" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Lifetime Limit<span class="text-red">*</span></label>
									<div class="col-sm-2" style="padding-right: 5px;">
										<input type="text" class="form-control" id="edit_lifetime_limit" placeholder="Lifetime Limit" required>
									</div>
									<div class="col-sm-3" align="left" id="divEditLimitUnit" style="padding-left: 5px;">
										<select class="form-control" data-placeholder="Select Limit Unit" name="edit_limit_unit" id="edit_limit_unit" style="width: 100%">
											<option value=""></option>
											<option value="Pemakaian">Pemakaian</option>
											<option value="Hari">Hari</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Made In<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditMadeIn">
										<select class="form-control" data-placeholder="Select Made In" name="edit_item_made_in" id="edit_item_made_in" style="width: 100%">
											<option value=""></option>
											<option value="Local">Local</option>
											<option value="Japan">Japan</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Availability<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditAvailability">
										<select class="form-control" data-placeholder="Select Avail" name="edit_availability" id="edit_availability" style="width: 100%">
											<option value=""></option>
											<!-- <option value="0">Empty</option> -->
											<option value="1">Used</option>
											<option value="2">Repair</option>
											<option value="3">Not Use</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-pencil"></i> Edit</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="add-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add {{ucwords($category)}}</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Product<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddProduct">
										<select class="form-control" data-placeholder="Select Product" name="add_product" id="add_product" style="width: 100%">
											<option value=""></option>
											@foreach($product as $product)
											<option value="{{$product}}">{{$product}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_tag" placeholder="Scan RFID di Sini" required>
									</div>
									<div class="col-sm-1" style="padding-left: 0px;">
										<button onclick="$('#add_tag').val('');$('#add_tag').focus()" class="btn btn-danger btn-sm pull-left"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Name<span class="text-red">*</span></label>
									<div class="col-sm-5 autocomplete">
										<input type="text" class="form-control" id="add_item_name" placeholder="Item Name" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Lifetime Limit<span class="text-red">*</span></label>
									<div class="col-sm-2" style="padding-right: 5px;">
										<input type="text" class="form-control" id="add_lifetime_limit" placeholder="Lifetime Limit" required>
									</div>
									<div class="col-sm-3" align="left" id="divAddLimitUnit" style="padding-left: 5px;">
										<select class="form-control" data-placeholder="Select Limit Unit" name="add_limit_unit" id="add_limit_unit" style="width: 100%">
											<option value=""></option>
											<option value="Pemakaian">Pemakaian</option>
											<option value="Hari">Hari</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Type<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_item_type" placeholder="Item Type" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Item Alias<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_item_alias" placeholder="Item Alias" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Made In<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddMadeIn">
										<select class="form-control" data-placeholder="Select Made In" name="add_item_made_in" id="add_item_made_in" style="width: 100%">
											<option value=""></option>
											<option value="Local">Local</option>
											<option value="Japan">Japan</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#add-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
{{-- <script src="{{ url('js/pdfmake.min.js')}}"></script> --}}
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillData();

		$('#add_product').select2({
			allowClear:true,
			dropdownParent: $('#divAddProduct'),
		});

		$('#add_limit_unit').select2({
			allowClear:true,
			dropdownParent: $('#divAddLimitUnit'),
		});

		$('#edit_limit_unit').select2({
			allowClear:true,
			dropdownParent: $('#divEditLimitUnit'),
		});

		$('#edit_product').select2({
			allowClear:true,
			dropdownParent: $('#divEditProduct'),
		});

		$('#edit_availability').select2({
			allowClear:true,
			dropdownParent: $('#divEditAvailability'),
		});

		$('#edit_item_made_in').select2({
			allowClear:true,
			dropdownParent: $('#divEditMadeIn'),
		});

		$('#add_item_made_in').select2({
			allowClear:true,
			dropdownParent: $('#divAddMadeIn'),
		});
	});

	var jig_name = [];
	var jig_type = [];
	var availability = ['Empty','Used','Repair','Not Use'];

	function fillData(){
		$('#loading').show();
		
		$.get('{{ url("fetch/master/lifetime/".$category."/".$location) }}', function(result, status, xhr){
			if(result.status){
				if (result.lifetime != null) {
					$('#tableItem').DataTable().clear();
					$('#tableItem').DataTable().destroy();
					$('#bodyTableItem').html("");
					var tableItem = "";
					
					var index = 1;
					jig_name = [];
					jig_type = [];

					$.each(result.lifetime, function(key, value) {
						tableItem += '<tr>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableItem += '<td style="text-align:left;padding-left:7px;">'+value.product+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+(value.tag || '')+'</td>';
						tableItem += '<td style="text-align:left;padding-left:7px;">'+value.item_code+'</td>';
						tableItem += '<td style="text-align:left;padding-left:7px;">'+value.item_name+'</td>';
						tableItem += '<td style="text-align:left;padding-left:7px;">'+value.item_type+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.item_index+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.item_alias+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.item_made_in+'</td>';
						if (value.availability == 2) {
							var color = '#ffd86b';
						}else if (value.availability == 1) {
							var color = '#a5ff87';
						}else if (value.availability == 3) {
							var color = '#cfcfcf';
						}
						tableItem += '<td style="text-align:left;padding-left:7px;background-color:'+color+'">'+availability[value.availability]+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.lifetime_limit+' '+value.limit_unit+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.lifetime+'</td>';
						tableItem += '<td style="text-align:right;padding-right:7px;">'+value.repair+'</td>';
						var url = '{{url("index/repair/lifetime/".$category."/".$location)}}/'+value.id;
						tableItem += '<td style="text-align:center">';
						if (!'{{$role}}'.match('L-')) {
							tableItem += '<button class="btn btn-warning btn-sm" onclick="editItem(\''+value.id+'\',\''+value.product+'\',\''+value.item_code+'\',\''+value.item_name+'\',\''+value.item_type+'\',\''+value.item_index+'\',\''+value.availability+'\',\''+value.lifetime+'\',\''+value.repair+'\',\''+value.tag+'\',\''+value.lifetime_limit+'\',\''+value.item_made_in+'\',\''+value.limit_unit+'\',\''+value.item_alias+'\')"><i class="fa fa-edit"></i></button>';
							tableItem += '<button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteItem(\''+value.id+'\')"><i class="fa fa-trash"></i></button>';
						}
						tableItem += '<a style="margin-left:7px;" href="'+url+'" class="btn btn-sm btn-success"><i class="fa fa-gears"></i></a>';
						tableItem += '</td>';
						tableItem += '</tr>';
						index++;
						jig_name.push(value.item_name);
						jig_type.push(value.item_type);
					});
					$('#bodyTableItem').append(tableItem);

					var jig_name_unik = jig_name.filter(onlyUnique);
					var jig_type_unik = jig_type.filter(onlyUnique);

					var table = $('#tableItem').DataTable({
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
						'searching': true,
						"processing": true,
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

				$('#loading').hide();

				autocomplete(document.getElementById("add_item_name"), jig_name_unik);
				autocomplete(document.getElementById("add_item_type"), jig_type_unik);

				autocomplete(document.getElementById("edit_item_name"), jig_name_unik);
				autocomplete(document.getElementById("edit_item_type"), jig_type_unik);
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function fillDataReport(){
		$('#loading').show();
		
		$.get('{{ url("fetch/report/lifetime/".$category."/".$location) }}', function(result, status, xhr){
			if(result.status){
				if (result.lifetime != null) {
					$('#tableLifetime').DataTable().clear();
					$('#tableLifetime').DataTable().destroy();
					$('#bodyTableLifetime').html("");
					var tableLifetime = "";
					
					var index = 1;

					$.each(result.lifetime, function(key, value) {
						tableLifetime += '<tr>';
						tableLifetime += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableLifetime += '<td style="text-align:left;padding-left:7px;">'+value.product+'</td>';
						tableLifetime += '<td style="text-align:left;padding-left:7px;">'+value.item_code+'</td>';
						tableLifetime += '<td style="text-align:left;padding-left:7px;">'+value.item_name+' '+value.item_type+' ('+value.item_index+')</td>';
						tableLifetime += '<td style="text-align:left;padding-left:7px;">'+value.item_made_in+'</td>';
						tableLifetime += '<td style="text-align:right;padding-right:7px;">'+value.lifetime+'</td>';
						tableLifetime += '<td style="text-align:right;padding-right:7px;">'+value.repair+'</td>';
						tableLifetime += '<td style="text-align:right;padding-right:7px;">'+value.lifetime_at+'</td>';
						index++;
					});
					$('#bodyTableLifetime').append(tableLifetime);

					var table = $('#tableLifetime').DataTable({
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
						'searching': true,
						"processing": true,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					$('#tableRepair').DataTable().clear();
					$('#tableRepair').DataTable().destroy();
					$('#bodyTableRepair').html("");
					var tableRepair = "";
					
					var index = 1;

					$.each(result.repair, function(key, value) {
						tableRepair += '<tr>';
						tableRepair += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableRepair += '<td style="text-align:left;padding-left:7px;">'+value.product+'</td>';
						tableRepair += '<td style="text-align:left;padding-left:7px;">'+value.item_code+'</td>';
						tableRepair += '<td style="text-align:left;padding-left:7px;">'+value.item_name+' '+value.item_type+' ('+value.item_index+')</td>';
						tableRepair += '<td style="text-align:left;padding-left:7px;">'+value.item_made_in+'</td>';
						tableRepair += '<td style="text-align:right;padding-right:7px;">'+value.lifetime+'</td>';
						tableRepair += '<td style="text-align:right;padding-right:7px;">'+value.repair+'</td>';
						tableRepair += '<td style="text-align:right;padding-right:7px;">'+value.created_at+'</td>';
						index++;
					});
					$('#bodyTableRepair').append(tableRepair);

					var table = $('#tableRepair').DataTable({
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
						'searching': true,
						"processing": true,
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

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function editItem(id,product,item_code,item_name,item_type,item_index,availability,lifetime,repair,tag,lifetime_limit,item_made_in,limit_unit,item_alias) {
		$('#id').val(id);
		$('#edit_product').val(product).trigger('change');
		$('#edit_item_name').val(item_name);
		$('#edit_item_type').val(item_type);
		$('#edit_lifetime_limit').val(lifetime_limit);
		$('#edit_item_alias').val(item_alias);
		$('#edit_item_made_in').val(item_made_in).trigger('change');
		$('#edit_limit_unit').val(limit_unit).trigger('change');
		if (tag == null || tag == 'null') {
			$('#edit_tag').val('');
		}else{
			$('#edit_tag').val(tag);
		}
		$('#edit_availability').val(availability).trigger('change');
		$('#edit-modal').modal('show');
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				product:$("#add_product").val(),
				tag:$("#add_tag").val(),
				item_name:$("#add_item_name").val(),
				item_type:$("#add_item_type").val(),
				item_alias:$("#add_item_alias").val(),
				lifetime_limit:$("#add_lifetime_limit").val(),
				limit_unit:$("#add_limit_unit").val(),
				item_made_in:$("#add_item_made_in").val(),
			}
			$.post('{{ url("input/master/lifetime/".$category."/".$location) }}', data,function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Add {{ucwords($category)}}');
					$('#add-modal').modal('hide');
					$('#loading').hide();
					fillData();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				id:$("#id").val(),
				product:$("#edit_product").val(),
				tag:$("#edit_tag").val(),
				item_name:$("#edit_item_name").val(),
				item_type:$("#edit_item_type").val(),
				item_alias:$("#edit_item_alias").val(),
				lifetime_limit:$("#edit_lifetime_limit").val(),
				limit_unit:$("#edit_limit_unit").val(),
				availability:$("#edit_availability").val(),
				item_made_in:$("#edit_item_made_in").val(),
			}
			$.post('{{ url("update/master/lifetime/".$category."/".$location) }}', data,function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Update {{ucwords($category)}}');
					$('#edit-modal').modal('hide');
					$('#loading').hide();
					fillData();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function deleteItem(id) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				id:id,
			}
			$.post('{{ url("delete/master/lifetime/".$category."/".$location) }}', data,function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Delete {{ucwords($category)}}');
					$('#loading').hide();
					fillData();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function autocomplete(inp, arr) {
	  /*the autocomplete function takes two arguments,
	  the text field element and an array of possible autocompleted values:*/
	  var currentFocus;
	  /*execute a function when someone writes in the text field:*/
	  inp.addEventListener("input", function(e) {
	      var a, b, i, val = this.value;
	      /*close any already open lists of autocompleted values*/
	      closeAllLists();
	      if (!val) { return false;}
	      currentFocus = -1;
	      /*create a DIV element that will contain the items (values):*/
	      a = document.createElement("DIV");
	      a.setAttribute("id", this.id + "autocomplete-list");
	      a.setAttribute("class", "autocomplete-items");
	      /*append the DIV element as a child of the autocomplete container:*/
	      this.parentNode.appendChild(a);
	      /*for each item in the array...*/
	      for (i = 0; i < arr.length; i++) {
	        /*check if the item starts with the same letters as the text field value:*/
	        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
	          /*create a DIV element for each matching element:*/
	          b = document.createElement("DIV");
	          /*make the matching letters bold:*/
	          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
	          b.innerHTML += arr[i].substr(val.length);
	          /*insert a input field that will hold the current array item's value:*/
	          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
	          /*execute a function when someone clicks on the item value (DIV element):*/
	          b.addEventListener("click", function(e) {
	              /*insert the value for the autocomplete text field:*/
	              inp.value = this.getElementsByTagName("input")[0].value;
	              /*close the list of autocompleted values,
	              (or any other open lists of autocompleted values:*/
	              closeAllLists();
	          });
	          a.appendChild(b);
	        }
	      }
	  });
	  /*execute a function presses a key on the keyboard:*/
	  inp.addEventListener("keydown", function(e) {
	      var x = document.getElementById(this.id + "autocomplete-list");
	      if (x) x = x.getElementsByTagName("div");
	      if (e.keyCode == 40) {
	        /*If the arrow DOWN key is pressed,
	        increase the currentFocus variable:*/
	        currentFocus++;
	        /*and and make the current item more visible:*/
	        addActive(x);
	      } else if (e.keyCode == 38) { //up
	        /*If the arrow UP key is pressed,
	        decrease the currentFocus variable:*/
	        currentFocus--;
	        /*and and make the current item more visible:*/
	        addActive(x);
	      } else if (e.keyCode == 13) {
	        /*If the ENTER key is pressed, prevent the form from being submitted,*/
	        e.preventDefault();
	        if (currentFocus > -1) {
	          /*and simulate a click on the "active" item:*/
	          if (x) x[currentFocus].click();
	        }
	      }
	  });
	  function addActive(x) {
	    /*a function to classify an item as "active":*/
	    if (!x) return false;
	    /*start by removing the "active" class on all items:*/
	    removeActive(x);
	    if (currentFocus >= x.length) currentFocus = 0;
	    if (currentFocus < 0) currentFocus = (x.length - 1);
	    /*add class "autocomplete-active":*/
	    x[currentFocus].classList.add("autocomplete-active");
	  }
	  function removeActive(x) {
	    /*a function to remove the "active" class from all autocomplete items:*/
	    for (var i = 0; i < x.length; i++) {
	      x[i].classList.remove("autocomplete-active");
	    }
	  }
	  function closeAllLists(elmnt) {
	    /*close all autocomplete lists in the document,
	    except the one passed as an argument:*/
	    var x = document.getElementsByClassName("autocomplete-items");
	    for (var i = 0; i < x.length; i++) {
	      if (elmnt != x[i] && elmnt != inp) {
	        x[i].parentNode.removeChild(x[i]);
	      }
	    }
	  }
	  /*execute a function when someone clicks in the document:*/
	  document.addEventListener("click", function (e) {
	      closeAllLists(e.target);
	  });
	}
</script>
@endsection