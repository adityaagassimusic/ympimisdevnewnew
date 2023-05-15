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
		text-align:center;
		overflow:hidden;
		padding: 3px;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading { display: none; }
	#itemtable_body > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Design Kanban Material <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	@if($role_user->role_code == 'MIS' || $role_user->role_code == 'PC')
	<ol class="breadcrumb">
		<li> 
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-upload"></i> Upload Kanban Material</a>
			<a href="javascript:void(0)" onclick="newData('new')" class="btn btn-success" style="color:white"><i class="fa fa-plus"></i>Create Kanban Material</a> </li>

			<!-- <a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Kanban Material</a></li> -->
		</ol>
		@endif

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
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="row">
			<div class="col-xs-12" style="padding-top: 10px;">
				<div class="box box-primary">
					<div class="box-header">
						<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">

								<div class="col-xs-3 col-xs-offset-3">
									<div class="form-group">
										<label>Rcvg Sloc</label>
										<select class="form-control select2" multiple="multiple" name="loc" id='loc' data-placeholder="Loc" style="width: 100%;">
											<option value=""></option>
											@foreach($loc as $rcvg_sloc) 
											<option value="{{ $rcvg_sloc->rcvg_sloc }}">{{ $rcvg_sloc->rcvg_sloc }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group">
										<label>Sloc Name</label>
										<select class="form-control select2" multiple="multiple" name="sloc_name" id='sloc_name' data-placeholder="Sloc Name" style="width: 100%;">
											<option value=""></option>
											@foreach($sloc_name as $sloc_name) 
											<option value="{{ $sloc_name->sloc_name }}">{{ $sloc_name->sloc_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-4 col-xs-offset-5">
							<div class="form-group pull-right">
								<div class="col-md-" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()"><i class="fa fa-search"></i> Search</button>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>		
			<div class="col-xs-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">

						<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Kanban Detail</a></li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<table id="itemtable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:4%;">Check All</th>
										<th style="width:4%;">Barcode</th>
										<th style="width:4%;">Gmc</th>
										<th style="width:10%;">Description</th>
										<th style="width:4%;">Uom</th>
										<th style="width:6%;">Receiving Sloc</th>
										<th style="width:6%;">Sloc Name</th>
										<th style="width:4%;">Lot</th>
										<th style="width:4%;">No Hako</th>
										<th style="width:4%;">Keterangan</th>
										<th id="but" style="width:9%;">Action</th>

									</tr>
								</thead>
								<tbody id="itemtable_body">
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
										<th id="but1"></th>

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
						<span style="font-weight: bold; font-size: 20px;">Material Picked: </span>
						<span id="picked" style="font-weight: bold; font-size: 24px; color: red;">0</span>
						<span style="font-weight: bold; font-size: 16px; color: red;">of</span>
						<span id="total" style="font-weight: bold; font-size: 16px; color: red;">0</span>
					</center>
					<button class="btn btn-primary" style="margin-left:1%; width: 98%; font-size: 22px; margin-bottom: 30px;" onclick="printJob(this)"><i class="fa fa-print"></i> PRINT</button>
				</div>
			</div>

		</div>


		<div class="modal modal-danger fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<input type="hidden" id="ids">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Confirm Clear Data</h4>
					</div>
					<div class="modal-body">
						Are you sure you want to Clear Data ?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
						<a name="modalbuttoncancel" type="button"  onclick="DeleteForm()" class="btn btn-danger">Yes</a>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade in" id="modal_uploud">
			<form id ="importKanban" method="post" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							<div class="col-xs-12" style="background-color: #605ca8">
							<h2 style="text-align: center; margin:5px; font-weight: bold; color: white">Upload File Kanban Material</h2>
						</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-sm-3">Tanggal<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control " id="tanggal" placeholder="Tanggal" value="{{date('Y-m-d')}}" readonly>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-sm-3">Upload File</label>
							<div class="col-xs-6" align="left">
								<input type="file" name="file" id="file">
							</div>
						</div>

						<div class="modal-footer">
							<div class="col-xs-12" style="padding-top: 20px">
								<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL </button>
								<button class="btn btn-success pull-left" style="font-weight: bold;font-size: 1.3vw; width: 68%;" onclick="$('[name=importKanban]').submit();" >Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

		<div class="modal fade" id="edit_modal">
			<div class="modal-dialog modal-lg" style="width: 900px">
				<div class="modal-content">
					<div class="modal-header" style="padding-top: 0;">
						<center><h2 style="font-weight: bold; padding: 3px;background-color: #00a65a;color: white;" id="modalNewTitle"></h2></center>
						<div class="row">
							<input type="hidden" id="id_edit">

							<div class="col-md-1">
							</div>
							<div class="col-md-10">

								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">Barcode<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="barcode">
									</div>
								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">GMC<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="gmcs">
									</div>
								</div>

								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">Description<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="description">
									</div>
								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">UOM<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<select class="form-control select2" data-placeholder="Select UOM" name="uoms" id="uoms" style="width: 100%">
											<option value=""></option>
											<option value="PC">PC</option>
											<option value="KG">KG</option>
											<option value="SET">SET</option>
											<option value="M">M</option>
											<option value="SHT">SHT</option>
											<option value="DS">DS</option>
										</select>
									</div>

								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">Receiving Sloc<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<select class="form-control select2"  name="sloc" id="sloc" data-placeholder="Receiving Sloc" style="width: 100%;">
											<option value=""></option>
											@foreach($loc1 as $loc1) 
											<option value="{{ $loc1->rcvg_sloc }}">{{ $loc1->rcvg_sloc }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">lot<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="lot">
									</div>
								</div>

								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">No Hako<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="no_hako">
									</div>
								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">Keterangan<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="keterangan">
										<input type="hidden" class="form-control" id="ids1">
									</div>
								</div>
								<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
									<label for="area" class="col-sm-12 control-label">Sloc Name<span class="text-red">*</span></label>
									<div class="col-sm-12">
										<select class="form-control select2"  name="sloc_name1" id="sloc_name1" data-placeholder="Sloc Name" style="width: 100%;">
											<option value=""></option>
											@foreach($sloc_name1 as $sloc_name1) 
											<option value="{{ $sloc_name1->sloc_name }}">{{ $sloc_name1->sloc_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-1">
						</div>

						<div class="col-md-12" style="padding-top:20px;">
							<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.5vw; width: 30%;">CANCEL </button>
							<button class="btn btn-success pull-right" id="newButton" style="font-weight: bold; font-size: 1.5vw; width: 68%;" onclick="Save()">CREATE KANBAN</i></button>
							<button class="btn btn-info pull-right" id="updateButton" style="font-weight: bold; font-size: 1.5vw; width: 68%;" onclick="edit()">UPDATE</i></button>
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
	<script src="{{ url("js/icheck.min.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});


		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
		// $("#sloc_name").val(" ").trigger('change.select2');
		// $("#loc").val(" ").trigger('change.select2');
		$('.select2').select2();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: '<?php echo $tgl_max ?>'
		});

	});

		$("form#importKanban").submit(function(e) {
			if ($('#file').val() == '' || $('#tanggal').val() == '') {
				openErrorGritter('Error!', 'Data Tidak Boleh Kosong');
				return false;
			}

			$("#loading").show();

			e.preventDefault();    
			var formData = new FormData()
			formData.append('file', $('#file').prop('files')[0]);;

			$.ajax({
				url: '{{ url("import/kanban/mt") }}',
				type: 'POST',
				data: formData,
				success: function (result, status, xhr) {
					if(result.status){
						$("#loading").hide();
						location.reload();
						// fetchTable();
						$('#file').val("");
						$('#modal_uploud').modal('hide');
						openSuccessGritter('Success', result.message);

					}else{
						$("#loading").hide();
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

		function openModalCreate(){
			$('#modal_uploud').modal('show');
		}


		function fetchTable(){
		// $('#loading').show();
		var gmc = $('#gmc').val();
		var loc = $('#loc').val();
		var sloc_name = $('#sloc_name').val();
		console.log(sloc_name);

		var data = {
			gmc:gmc,
			loc:loc,
			sloc_name:sloc_name
		}

		$.get('{{ url("fetch/list/material") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', data.message);

				$('#itemtable').DataTable().clear();
				$('#itemtable').DataTable().destroy();				
				$('#itemtable_body').html("");
				var body = "";

				$('input:checkbox').prop('checked', false);
				$('#total').html(result.print_mt.length);
				$('#picked').html(0);
				total = 0;
				var role_us = result.role_users.role_code;
				
				for (var i = 0; i < result.print_mt.length; i++) {

					body += '<tr id="tr+'+result.print_mt[i].id+'">';
					body += '<td><input type="checkbox" name="P" id="'+result.print_mt[i].id+'"></td>';
					body += '<td onClick="countPicked(this)" id="td1+'+result.print_mt[i].id+'">'+result.print_mt[i].barcode+'</td>';
					body += '<td onClick="countPicked(this)" id="td2+'+result.print_mt[i].id+'">'+result.print_mt[i].gmc_material+'</td>';
					body += '<td onClick="countPicked(this)" id="td3+'+result.print_mt[i].id+'">'+result.print_mt[i].description+'</td>';
					body += '<td onClick="countPicked(this)" id="td3+'+result.print_mt[i].id+'">'+result.print_mt[i].uom+'</td>';
					body += '<td onClick="countPicked(this)" id="td4+'+result.print_mt[i].id+'">'+result.print_mt[i].rcvg_sloc+'</td>';
					body += '<td onClick="countPicked(this)" id="td4+'+result.print_mt[i].id+'">'+result.print_mt[i].sloc_name+'</td>';
					body += '<td onClick="countPicked(this)" id="td5+'+result.print_mt[i].id+'">'+result.print_mt[i].lot+'</td>';
					body += '<td onClick="countPicked(this)" id="td6+'+result.print_mt[i].id+'">'+result.print_mt[i].no_hako+'</td>';
					body += '<td onClick="countPicked(this)" id="td7+'+result.print_mt[i].id+'">'+result.print_mt[i].keterangan+'</td>';

					if (role_us == "PC" || role_us == "MIS") {
						$('#but').show();
						$('#but1').show();

						body += "<td><a id='"+result.print_mt[i].barcode+'/-'+ result.print_mt[i].gmc_material +'/-'+ result.print_mt[i].description +'/-'+ result.print_mt[i].uom+'/-'+ result.print_mt[i].rcvg_sloc+'/-'+ result.print_mt[i].lot+'/-'+ result.print_mt[i].no_hako+'/-'+ result.print_mt[i].keterangan+'/-'+ result.print_mt[i].id+'/-'+ result.print_mt[i].sloc_name+"' onclick='showEdit(this);' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-edit'></i>Edit</a> <a onclick='delShow(\""+result.print_mt[i].id+"\");' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-trash'></i>Delete</a></td>";
					}else{
						$('#but').hide();
						$('#but1').hide();

						body += "<td hidden><a id='"+result.print_mt[i].barcode+'/-'+ result.print_mt[i].gmc_material +'/-'+ result.print_mt[i].description +'/-'+ result.print_mt[i].uom+'/-'+ result.print_mt[i].rcvg_sloc+'/-'+ result.print_mt[i].lot+'/-'+ result.print_mt[i].no_hako+'/-'+ result.print_mt[i].keterangan+'/-'+ result.print_mt[i].id+'/-'+ result.print_mt[i].sloc_name+"' onclick='showEdit(this);' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-edit'></i>Edit</a> <a onclick='delShow(\""+result.print_mt[i].id+"\");' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-trash'></i>Delete</a></td>";
					}

					body += '</tr>';

				}
				$("#itemtable_body").append(body);

				$('#itemtable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#itemtable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, 100, -1 ],
					[ '10 rows', '25 rows', '50 rows','100 rows', 'Show all' ]
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

				$('#itemtable tfoot tr').appendTo('#itemtable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', "Data Tidak Ada");
				$('#loading').hide();
			}
		});
}

function newData(id){

	if(id == 'new'){
		$('#modalNewTitle').text('CREATE KANBAN MATERIAL');
		$('#newButton').show();
		$('#updateButton').hide();
		clearNew();
		$('#edit_modal').modal('show');
	}
	else{
		$('#modalNewTitle').text('EDIT KANBAN MATERIAL');
		$('#newButton').hide();
		$('#updateButton').show();


	}
}

function clearNew(){
	$('#barcode').val('');
	$('#gmcs').val('');
	$("#description").val('');
	$("#uoms").val('').trigger('change.select2');
	$("#sloc").val('').trigger('change.select2');
	$("#sloc_name1").val('').trigger('change.select2');
	$("#lot").val('');
	$("#no_hako").val('');
	$("#keterangan").val('');
}

function Save(){
	if(confirm("Apakah anda yakin akan membuat kanban material ini?")){
		$('#loading').show();
		if($("#barcode").val() == "" || $('#gmcs').val() == "" || $('#description').val() == "" || $('#uoms').val() == "" || $('#sloc').val() == "" || $('#lot').val() == "" || $('#no_hako').val() == "" || $('#keterangan').val() == ""|| $('#sloc_name1').val() == ""){
			$('#loading').hide();
			openErrorGritter('Error', "there is an empty data");
			return false;
		}

		var formData = new FormData();

		formData.append('barcode', $("#barcode").val());
		formData.append('gmcs', $("#gmcs").val());
		formData.append('description', $("#description").val());
		formData.append('uoms', $("#uoms").val());
		formData.append('sloc', $("#sloc").val());
		formData.append('lot', $("#lot").val());
		formData.append('no_hako', $("#no_hako").val());
		formData.append('keterangan', $("#keterangan").val());
		formData.append('sloc_name1', $("#sloc_name1").val());


		$.ajax({
			url:"{{ url('create/kanban/produksi') }}",
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
					$('#edit_modal').modal('hide');
					clearNew();
					fetchTable();
				}else{
					openErrorGritter('Error!',data.message);
					$('#loading').hide();
					audio_error.play();
				}

			}
		});
	}else{
		return false;
	}

}


function showEdit(elem){
	var target = $(elem).attr("id");
	var data = target.split("/-");

	var barcode = data[0];
	console.log(barcode);
	var gmcs = data[1];
	var description = data[2];
	var uoms = data[3];
	var sloc = data[4];
	var lot = data[5];
	var no_hako = data[6];
	var keterangan = data[7];
	var ids1 = data[8];
	var sloc_name = data[9];

	document.getElementById("barcode").value = barcode;
	document.getElementById("gmcs").value = gmcs;
	document.getElementById("description").value = description;
	$("#uoms").val(uoms).trigger('change.select2');
	$("#sloc").val(sloc).trigger('change.select2');	
	document.getElementById("lot").value = lot;
	document.getElementById("no_hako").value = no_hako;
	document.getElementById("keterangan").value = keterangan;
	document.getElementById("ids1").value = ids1;
	$("#sloc_name1").val(sloc_name).trigger('change.select2');	


	$("#edit_modal").modal('show');
	$('#newButton').hide();
	$('#updateButton').show();
	$('#modalNewTitle').text('EDIT KANBAN MATERIAL');

}

function edit(){
	if(confirm("Apakah anda yakin akan merubah data kanban material ini?")){
		if($("#barcode").val() == "" || $('#gmcs').val() == "" || $('#description').val() == "" || $('#uoms').val() == "" || $('#sloc').val() == "" || $('#lot').val() == "" || $('#no_hako').val() == "" || $('#keterangan').val() == ""|| $('#sloc_name1').val() == ""){
			$('#loading').hide();
			openErrorGritter('Error', "there is an empty data");
			return false;
		}
		var ids1 = $("#ids1").val();
		var barcode = $("#barcode").val();
		var gmcs = $("#gmcs").val();
		var description = $("#description").val();
		var uom = $("#uoms").val();
		var sloc = $("#sloc").val();
		var lot = $("#lot").val();
		var no_hako = $("#no_hako").val();
		var keterangan = $("#keterangan").val();
		var sloc_name1 = $("#sloc_name1").val();


		var data = {
			id : ids1,
			barcode : barcode,
			gmcs : gmcs,
			description : description,
			uom : uom,
			sloc : sloc,
			lot : lot,
			no_hako : no_hako,
			keterangan : keterangan,
			sloc_name1 : sloc_name1

		}

		$.post('{{ url("update/kanban/produksi") }}', data,  function(result, status, xhr){
			if(result.status){
				// $('#loading').show();
				openSuccessGritter('Success', result.message);
				location.reload();
				$("#edit_modal").modal('hide');
				// fetchTable();
				// $('#loading').hide();
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}else{
		return false;
	}
}



function DeleteForm() {
	var ids = $('#ids').val();
	var data = {
		ids:ids
	}
	$.post('{{ url("delete/kanban/prd") }}', data,  function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success', result.message);
			$("#modaldelete").modal('hide');
				location.reload();
				// fetchTable();

				
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
}

var total;


// $("#checkAll").click(function(){
//     $('input:checkbox').not(this).prop('checked', this.checked);
// });
function checkAll(element){
	var id = $(element).attr("id");
	var checkVal = $('#'+id).is(":checked");

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

	function delShow(id){
		$("#ids").val(id);
		$('#modaldelete').modal('show');
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
			alert("Material Picked 0");
			return false;
		}

		var data = {
			id : tag
		}
		window.open('{{ url("reprint/kanban/material/") }}/'+tag.join(","), '_blank');
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


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

