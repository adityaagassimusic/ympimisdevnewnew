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
	thead>tr>th{
	  text-align:left;
	  overflow:hidden;
	  padding: 3px;
	}
	tbody>tr>td{
	  text-align:left;
	}
	tfoot>tr>th{
	  text-align:left;
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
	td{
	    overflow:hidden;
	    text-overflow: ellipsis;
	  }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Tools BOM Data <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<?php if(Auth::user()->role_code == "MIS") { ?>
		<li>
			<button class="btn btn-success " data-toggle="modal"  data-target="#upload_bom" style="margin-right: 5px">
				<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload BOM
			</button>
		</li>
		<?php } ?>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Tools</label>
								<select class="form-control select2" multiple="multiple" id='tools' data-placeholder="Select Tools" style="width: 100%;">
									<option></option>
									@foreach($tools as $tl)
									<option value="{{ $tl->tools_description }}">{{ $tl->tools_description }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body" style="padding-top: 0;">
							<table id="toolTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:2%;">GMC Parent</th>
										<th style="width:5%;">GMC Parent Description</th>
										<th style="width:2%;">GMC Component</th>
										<th style="width:6%;">GMC Component Description</th>
										<th style="width:1%;">Base Unit</th>
										<th style="width:1%;">Unit</th>
										<!-- <th style="width:2%;">Location</th> -->
										<th style="width:2%;">Item Code</th>
										<th style="width:5%;">Tools</th>
										<th style="width:1%;">Usage</th>
										<th style="width:2%;">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
					              <tr>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <!-- <th></th> -->
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					              </tr>
					            </tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="upload_bom">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
						Sample: <a href="{{ url('uploads/receive/sample/receive_sample.xlsx') }}">upload_sample.xlsx</a>
					</div>
					<div class="modal-body">
						Upload Excel file here:<span class="text-red">*</span>
						<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="modalImportButton" type="submit" class="btn btn-success">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div class="modal fade in" id="modalEdit">
		<form id="importFormEdit" name="importFormEdit" method="post" action="{{ url('update/purchase_requisition') }}">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Edit BOM Tools</h4>
						
						<br>
						<h4 class="modal-title" id="modalDetailTitle"></h4>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-12">
									<div class="form-group">
										<label>Tools</label>
										<input type="text" class="form-control" id="tools_edit" name="tools_edit" placeholder="Identitas">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label>Usage</label>
										<input type="text" class="form-control" id="usage_edit" name="usage_edit" placeholder="Departemen">
									</div>
								</div>
								<div class="col-md-12">
									<button type="submit" class="btn btn-warning pull-right">Update</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		fetchTable();
		$('body').toggleClass("sidebar-collapse");
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#toolTable').DataTable().destroy();
		
		var tools = $('#tools').val();
		var data = {
			tools:tools
		}
		
		$('#toolTable tfoot th').each( function () {
	      var title = $(this).text();
	      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	    } );

		var table = $('#toolTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				// dom: {
				// 	button: {
				// 		tag:'button',
				// 		className:''
				// 	}
				// },
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url('fetch/tools_bom') }}",
				"data" : data
			},
			"columns": [
				{ "data": "gmc_parent"},
				{ "data": "gmc_desc_parent"},
				{ "data": "gmc_component"},
				{ "data": "gmc_desc_component"},
				{ "data": "base_unit"},
				{ "data": "unit"},
				// { "data": "location"},
				{ "data": "tools_item"},
				{ "data": "tools_description"},
				{ "data": "usage"},
				{ "data": "action"}
			]
		});

		table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	          if ( that.search() !== this.value ) {
	            that
	            .search( this.value )
	            .draw();
	          }
	        } );
	      } );
		
      	$('#toolTable tfoot tr').appendTo('#toolTable thead');
	}


  function edit_bom(id){
  	var isi = "";

  	$('#modalEdit').modal("show");
  	
  	var data = {
  		id:id
  	};
  	
  	$.get('{{ url("edit/tools_bom") }}', data, function(result, status, xhr){	
  		$("#tools_edit").val(result.bom.tools_item+' - '+result.bom.tools_description).attr('readonly', true);
  		$("#usage_edit").val(result.bom.usage);
	});
}
</script>
@endsection

