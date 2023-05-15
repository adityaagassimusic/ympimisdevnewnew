@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td:hover {
		overflow: visible;
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
		padding-top: 0;
		padding-bottom: 0;
		cursor: pointer;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.selected {
		background: gold !important;
	}
</style>
@stop

@section('header')
<section class="content-header">

<!-- 	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_Item" style="margin-right: 5px">
		<i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Item
	</button>
 -->
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-xs-2 col-xs-offset-3">
							<div class="form-group">
								<label>Location</label>
								<select class="form-control select2" multiple="multiple" name="location" id='location' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($locations as $location) 
									<option value="{{ $location->location }}">{{ $location->location }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Group</label>
								<select class="form-control select2" multiple="multiple" name="group" id='group' data-placeholder="Select Group" style="width: 100%;">
									<option value=""></option>
									@foreach($groups as $group) 
									<option value="{{ $group->group }}">{{ $group->group }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Category</label>
								<select class="form-control select2" multiple="multiple" name="category" id='category' data-placeholder="Select Category" style="width: 100%;">
									<option value=""></option>
									@foreach($categories as $category) 
									<option value="{{ $category->category }}">{{ $category->category }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-4 col-xs-offset-5">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Detail Kanban</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="store_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">ID</th>
									<th style="width: 3%">Rack Code</th>
									<th style="width: 5%">Item Code</th>
									<th style="width: 10%">Description</th>
									<th style="width: 7%">Location</th>
									<th style="width: 7%">Group</th>
									<th style="width: 5%">Category</th>
									<th style="width: 3%">No Kanban</th>
									<th style="width: 3%">Remark</th>
									<th style="width: 3%">Status</th>
									<th style="width: 3%">Checklist</th>
								</tr>
							</thead>
							<tbody id="store_detail_body">
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

				<center>
					<span style="font-weight: bold; font-size: 20px;">
						<input onClick="checkAll(this)" type="checkbox" id="checkAllBox" /> <b>Check All</b>
					</span>
					<br>
					<span style="font-weight: bold; font-size: 20px;">Item Picked: </span>
					<span id="picked" style="font-weight: bold; font-size: 24px; color: red;">0</span>
					<span style="font-weight: bold; font-size: 16px; color: red;">of</span>
					<span id="total" style="font-weight: bold; font-size: 16px; color: red;">0</span>
				</center>
				<button class="btn btn-primary" style="margin-left:1%; width: 98%; font-size: 22px; margin-bottom: 30px;" onclick="printJob(this)"><i class="fa fa-print"></i> PRINT</button>
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
<script src="{{ url("js/icheck.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();

		jQuery('.tags').tagsInput({ width: 'auto' });
		fetchTable();
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	var total;
	var store_detail;

	function checkAll(element){
		var id = $(element).attr("id");
		var checkVal = $('#'+id).is(":checked");

		// console.log(checkVal);

		if(checkVal) {
			total = $('#total').text();
			$('input:checkbox').prop('checked', true);
		}else{
			total = 0;
			$('input:checkbox').prop('checked', false);
		}
		$("#picked").html(total);
	}

	function countPicked(element){

		var id = $(element).attr("id");
		var checkId = id.slice(4);
		var checkVal = $('#'+checkId).is(":checked");

		if(checkVal) {
			total--;
			$('#'+ String(checkId)).prop('checked', false);
			// $('#tr+'+ String(checkId)).css('background-color', '#000000');

		}else{
			total++;
			$('#'+ String(checkId)).prop('checked', true);
			// $('#tr+'+ String(checkId)).toggleClass('active');
		}
		
		$("#picked").html(total);
	}	

	function printJob(element){
		var tag = [];
		$("input[type=checkbox]:checked").each(function() {
			if (this.id.indexOf("All") >= 0) {

			} else {
				tag.push(this.id);
			}
		});

		if(tag.length < 1){
			alert("Item Picked 0");
			return false;
		}

		var data = {
			id : tag
		}

		window.open('{{ url("print/tools/kanban/") }}/'+tag.join(","), '_blank');

	}

	function fetchTable() {
		var location = $("#location").val(); 
		var group = $("#group").val(); 
		var category = $("#category").val(); 

		var data = {
			location : location,
			group : group,
			category : category
		}
		//Detail
		$.get('{{ url("fetch/tools/kanban") }}', data, function(result, status, xhr){
			if (result.status) {

				$('#store_detail').DataTable().clear();
				$('#store_detail').DataTable().destroy();
				$('#store_detail_body').html("");

				$('input:checkbox').prop('checked', false);
				$('#total').html(result.data.length);
				$('#picked').html(0);
				total = 0;

				var body = '';
				var css = 'style="background-color: #000000;"';

				for (var i = 0; i < result.data.length; i++) {

					body += '<tr id="tr+'+result.data[i].id+'">';
					body += '<td onClick="countPicked(this)" id="td0+'+result.data[i].id+'">'+parseInt(i+1)+'</td>';
					body += '<td onClick="countPicked(this)" id="td1+'+result.data[i].id+'">'+result.data[i].rack_code+'</td>';
					body += '<td onClick="countPicked(this)" id="td2+'+result.data[i].id+'">'+result.data[i].item_code+'</td>';
					body += '<td onClick="countPicked(this)" id="td3+'+result.data[i].id+'">'+result.data[i].description+'</td>';
					body += '<td onClick="countPicked(this)" id="td4+'+result.data[i].id+'">'+result.data[i].location+'</td>';
					body += '<td onClick="countPicked(this)" id="td5+'+result.data[i].id+'">'+result.data[i].group+'</td>';
					body += '<td onClick="countPicked(this)" id="td6+'+result.data[i].id+'">'+result.data[i].category+'</td>';
					body += '<td onClick="countPicked(this)" id="td7+'+result.data[i].id+'">'+result.data[i].no_kanban+'</td>';
					body += '<td onClick="countPicked(this)" id="td8+'+result.data[i].id+'">'+result.data[i].remark+'</td>';

					if(result.data[i].print_status == null){
						body += '<td style="background-color: #ff1744;color:white" id="td8+'+result.data[i].id+'">Belum Cetak</td>';
					}else{
						body += '<td style="background-color: #ffea00;" id="td8+'+result.data[i].id+'">Sudah Cetak</td>';
					}

					if(result.data[i].print_status == null){
						body += '<td><input type="checkbox" name="P" id="'+result.data[i].id+'"></td>';
					}else{
						body += '<td><input type="checkbox" name="RP" id="'+result.data[i].id+'"></td>';
					}

					body += '</tr>';

				}
				$("#store_detail_body").append(body);

				$('#store_detail tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" class="cari" type="text" placeholder="Search '+title+'" />' );
				});
				store_detail = $('#store_detail').DataTable({
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
							className: 'btn btn-default'
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
				});

				store_detail.columns().every( function () {
					var that = this;

					$( '.cari', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					});
				});
				$('#store_detail tfoot tr').appendTo('#store_detail thead');

			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection

